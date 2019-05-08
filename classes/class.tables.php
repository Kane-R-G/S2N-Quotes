<?php


if( ! class_exists( 'WP_List_Table' ) ){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class S2N_Tables extends WP_List_Table {

    public $bulk_actions;
    public $set_columns;
    private $admin;

    function __construct( $admin ){
        $this->screen = get_current_screen();
        $this->bulk_actions = [ 
            'delete' => 'Delete' 
        ];
        $this->set_columns =  [
            'cba'	    => '<input class="S2N-select-all" type="checkbox" />', 	 
            'id'	    => __( 'ID', 's2n-quotes' ),
            'quotee'	=> __( 'Quotee', 's2n-quotes' ),
            'quote'	    => __( 'Quote', 's2n-quotes' ),
            'status'	=> __( 'Status', 's2n-quotes' ),
            'created'	=> __( 'Created', 's2n-quotes' ),
            'edited'	=> __( 'Edited', 's2n-quotes' ),
            'edit'	    => __( 'Edit', 's2n-quotes' ),
        ];	
        $this->admin = $admin;
    }

    public function set_table( $items ){
        $this->items = $items;
    }

    public function get_columns() {	
        return $this->set_columns;	
    }

    public function prepare_items() {

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($this->set_columns, $hidden, $sortable);

    }

    public function column_default( $item, $column_name ) {
        switch( $column_name ) { 
            case 'cba':
                return '<input type="checkbox" name="bulk[]" value="' . $item->id . '"/>';
            case 'id':
                return $item->id;
            case 'quotee':
                return $item->quotee;
            case 'quote':
                return $item->quote;
            case 'status':
                if( $item->status == 0 ){
                    return '<div style="background-color: green; color: white; width: 100%; padding:5px; text-align:center;"> Active </div>';
                }elseif( $item->status == 1 ){
                    return '<div style="background-color: grey; color: white; width: 100%; padding:5px; text-align:center;"> Inactive </div>';
                }else{
                    return '<div style="background-color: red; color: white; width: 100%; padding:5px; text-align:center;"> Error </div>';
                }
                return $item->status;
            case 'created':
                return $item->created;
            case 'edited':
                return $item->edited;
            case 'edit':
                return '<button class="edit-quotes" data-quote="' . $item->id . '" > Edit </button>';
            default:
                return print_r( $column_name );
        }
    }

    public function get_bulk_actions() {
        return $this->bulk_actions;
    }

    public function process_bulk_action() {

        if ( isset( $_POST['action'] ) && isset( $_POST['action2'] ) ){

            if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {

                $nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
                $action = 'bulk-' . $this->_args['plural'];

                if ( ! wp_verify_nonce( $nonce , $action ) )
                    wp_die( 'Nope! Security check failed!' );

                $action = $this->current_action();
                $this->processQuotes( $action );

            }
            return '';
        }
    }

    function processQuotes( $action ){
        switch ( $action ) {
            case 'delete':
                if( isset( $_POST['bulk'] ) && is_array( $_POST['bulk'] ) ){
                    $count = 0;
                    foreach( $_POST['bulk'] as $quote ){
                        $deletes = $this->admin->removeQuote( $quote );  
                        $count++;      
                    }
                    add_settings_error( 
                        'delete-quote', 
                        'quotes',
                        'Deleted ' . $count . ' Quotes!', 
                        'updated' );
                }else{
                    add_settings_error( 
                        'delete-quote', 
                        'quotes',
                        'Error Deleting Quotes', 
                        'error' );
                }
                break;
        }
        return;
    }
}

