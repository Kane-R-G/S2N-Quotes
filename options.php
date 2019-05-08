<?php

if( session_id() == '' ) session_start();
 
function register_s2n_quotes_settings() {
 
    $s2n_quotes_settings = get_option( 's2n_quotes_settings' );

    $s2n_quotes_settings['quotes-ONOFF'] = ( $s2n_quotes_settings['quotes-ONOFF'] == 'on' ? 'checked' : '' ); 

    //set sections
    add_settings_section( 
        's2n_quotes_settings_op',
        __( 'S2N Quotes Settings' , 's2n-quotes'),
        's2n_quotes_settings_callback',
        's2n_quotes_settings_op'
    );

    add_settings_field(  
        'quotes-ONOFF',                      
        'Enabled Quotes',               
        's2n_quotes_checkbox_callback',   
        's2n_quotes_settings_op',                     
        's2n_quotes_settings_op',
        array (
            'label_for'   => 'quotes-ONOFF', 
            'ID'          => 'quotes-ONOFF', 
            'name'        => 'quotes-ONOFF',
            'value'       => $s2n_quotes_settings['quotes-ONOFF'],
            'option_name' => 's2n_quotes_settings',
            'class'       => '',
            'hint'        => __( 'Turn Quotes On/Off' , 's2n-quotes')
        )
    );
    
    $s2n_quotes_args   = array( 'sanitize_callback'   => 's2n_quotes_post_callback' );

    register_setting( 's2n_quotes_settings_op' , 's2n_quotes_settings' , $s2n_quotes_args );

}
function s2n_quotes_post_callback( $input ) {
    if( ! isset( $input['quotes-ONOFF'])){ $input['quotes-ONOFF'] = 'off'; }      
    return $input;  
}
function s2n_quotes_settings_callback( $args ) { 
    printf( '<p>%s</p>', __( 'Quote Settings' , 's2n-quotes' ) );
}
function s2n_quotes_textarea_callback( $args ) { 
    if(!isset($args["rows"]))$args["rows"] = 5;
    echo '<textarea id="' . $args["ID"] . '" name="' . $args["option_name"] . '[' . $args["ID"] . ']" rows="'.$args["rows"].'" class="field-40">' . $args["value"] . '</textarea> ';
    echo ( isset( $args["hint"] ) ? '<p class="hint">' . $args["hint"] . '</p>' : '' );
}
function s2n_quotes_checkbox_callback( $args ) { 
    echo '<input type="checkbox" id="' . $args["ID"] . '" name="' . $args["option_name"] . '[' . $args["ID"] . ']" ' . $args["value"] . ' ></input>';
    echo ( isset( $args["hint"] ) ? '<p class="hint">' . $args["hint"] . '</p>' : '' );
}

function s2n_quotes_settings_page() {

    if ( ! class_exists('S2N_Admin_Functions') ) {
        include( plugin_dir_path( __FILE__ ) . 'classes/class.admin.php');
    }
    $admin = new S2N_Admin_Functions();

    if ( ! class_exists('S2N_Tables') ) {
        include( plugin_dir_path( __FILE__ ) . 'classes/class.tables.php');
    }
    $tables = new S2N_Tables( $admin );
    $tables->process_bulk_action();

    if( isset( $_POST['add-quote'] ) ){

        if ( ! wp_verify_nonce( $_POST['_wpnonce'] , 'add-quote' ) ) {

            add_settings_error(
                'add-quote',
                'security' ,
                'Unauthorised',
                'error'
            );   
       
        }else{

            if( $admin->checkPOST( $_POST , 'add-quote' ) ){
                $args = [
                    'quotee'    => sanitize_text_field( $_POST['quote-quotee'] ) , 
                    'quote'     => sanitize_textarea_field( $_POST['quote-text'] ) ,
                    'status'    => sanitize_text_field( $_POST['quote-status'] ) ,
                    
                ];
        
                $admin->addQuote( $args );
                add_settings_error(
                    'add-quote',
                    'status' ,
                    'Successfully Added Quote!',
                    'updated'
                );        
            }

        }
    }elseif( isset( $_POST['edit-quote'] ) ){
        if ( ! wp_verify_nonce( $_POST['_wpnonce'] , 'edit-quote' ) ) {

            add_settings_error(
                'edit-quote',
                'security' ,
                'Unauthorised',
                'error'
            );   
       
        }else{
            if( isset( $_POST['id'] ) ){

            
                if( $admin->checkPOST( $_POST , 'edit-quote' ) ){
                    $args = [
                        'quotee'    => sanitize_text_field( $_POST['quote-quotee'] ) , 
                        'quote'     => sanitize_textarea_field( $_POST['quote-text'] ) ,
                        'status'    => sanitize_text_field( $_POST['quote-status'] ),
                        'id'        => sanitize_text_field( $_POST['id'] ) ,
                    ];
            
                    $admin->saveQuote( $args );
                    add_settings_error(
                        'edit-quote',
                        'status' ,
                        'Successfully Added Quote!',
                        'updated'
                    );        
                }

            }else{
                add_settings_error(
                    'edit-quote',
                    'failed' ,
                    'Failed To Find Quote',
                    'error'
                );   
            }

        }
    }

    ?>

    <div class="wrap">  

        <div id="icon-themes" class="icon32"></div>  
        <h2><?php _e( 'S2N Quotes', 's2n-quotes' ); ?></h2>  
        <div class="description"><?php _e( 'S2N Quotes WP', 's2n-quotes' ); ?></div>
        <?php 


        settings_errors(); 
        $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'settings'; 

        

        ?>  

        <h2 class="nav-tab-wrapper">  
            <a href="?page=s2n_quotes_options&tab=settings" class="nav-tab <?php     echo $active_tab == 'settings'     ? 'nav-tab-active' : ''; ?>"><?php _e( 'Settings' , 's2n-quotes' ) ?></a>  
            <a href="?page=s2n_quotes_options&tab=add"      class="nav-tab <?php     echo $active_tab == 'add'          ? 'nav-tab-active' : ''; ?>"><?php _e( 'Add' , 's2n-quotes' ) ?></a>  
            <a href="?page=s2n_quotes_options&tab=quotes"   class="nav-tab <?php     echo $active_tab == 'quotes'       ? 'nav-tab-active' : ''; ?>"><?php _e( 'Quotes' , 's2n-quotes' ) ?></a>  
        </h2>  
        <?php 
        
        if( $active_tab == 'settings' ) {
            ?>
            <form method="post" action="options.php">
            <?php
                settings_fields( 's2n_quotes_settings_op' );
                do_settings_sections( 's2n_quotes_settings_op' );
                submit_button();
            ?>
            </form>
        <?php
        }elseif( $active_tab == 'add' ) { 
            ?>
            
            <form method="post" action="<?php echo admin_url( 'admin.php?page=s2n_quotes_options&tab=add' ); ?>">
            <p>Add Quotes</p>

                <div style="width: 10%;">
                    <label> Quotee: </label>
                </div>
                <div style="width: 30%;">
                    <input style="width: 100%;" type="text" name="quote-quotee" id="quote-quotee" placeholder="Who wrote the quote?" />
                </div>
                <div style="width: 10%;">
                    <label> Quote: </label>
                </div>
                <div style="width: 30%;">
                    <textarea rows="5" style="width: 100%;" name="quote-text" id="quote-text" placeholder="What did they say?"></textarea>
                </div>
                <div style="width: 10%;">
                    <label> Status: </label>
                </div>
                <div style="width: 30%;">
                    <select style="width: 100%;" name="quote-status" id="quote-status">
                        <option selected disabled> Select Status </option>
                        <option value="0"> Active </option>
                        <option value="1"> Inactive </option>
                    </select>
                </div>
                <div>
                    <input type="submit" name="add-quote" id="add-quote" value="Add Quote" />
                    <?php wp_nonce_field( 'add-quote' ); ?>
                </div>
            </form>

            <?php
        }elseif( $active_tab == 'quotes' ) { 
            

            $tables->set_table( $admin->returnQuotes() );
            $tables->prepare_items();
            

            ?>

            <form method="post" action="<?php echo admin_url( 'admin.php?page=s2n_quotes_options&tab=quotes' ); ?>">
            <p>All Quotes</p>

            <input name="form_quotes" value="form_quotes" type="hidden" />

            <?php $tables->display(); ?>

            </form>
            <?php
        }elseif( $active_tab == 'edit' ) { 
            if( isset( $_GET['edit'] ) ){
                $quote = $admin->returnQuoteByID( $_GET['edit'] );
                if( is_array( $quote ) && ! empty( $quote ) ){
                    ?>
                    <form method="post" action="<?php echo admin_url( 'admin.php?page=s2n_quotes_options&tab=edit&edit=' . $quote[0]->id ); ?>">
                    <p>Edit Quote</p>

                        <div style="width: 10%;">
                            <label> Quotee: </label>
                        </div>
                        <div style="width: 30%;">
                            <input style="width: 100%;" type="text" name="quote-quotee" id="quote-quotee" placeholder="Who wrote the quote?" value="<?php echo $quote[0]->quotee; ?>" />
                        </div>
                        <div style="width: 10%;">
                            <label> Quote: </label>
                        </div>
                        <div style="width: 30%;">
                            <textarea rows="5" style="width: 100%;" name="quote-text" id="quote-text" placeholder="What did they say?"><?php echo $quote[0]->quote; ?></textarea>
                        </div>
                        <div style="width: 10%;">
                            <label> Status: </label>
                        </div>
                        <div style="width: 30%;">
                            <select style="width: 100%;" name="quote-status" id="quote-status">
                                <option selected disabled> Select Status </option>
                                <option <?php echo ( ( $quote[0]->status == 0 ) ? 'selected' : '' ); ?> value="0"> Active </option>
                                <option <?php echo ( ( $quote[0]->status == 1 ) ? 'selected' : '' ); ?> value="1"> Inactive </option>
                                <option <?php echo ( ( $quote[0]->status == 2 ) ? 'selected' : '' ); ?> value="2"> Soft Delete </option>
                            </select>
                        </div>
                        <div>
                            <input type="submit" name="edit-quote" id="edit-quote" value="Edit Quote" />
                            <input type="hidden" name="id" id="id" value="<?php echo $quote[0]->id; ?>" />
                            <?php wp_nonce_field( 'edit-quote' ); ?>
                        </div>
                    </form>
                    <?php
                }
            }
        }
?>   
    </div> 
<?php 
}
?>