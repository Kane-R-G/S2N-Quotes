<?php
/*
    Plugin Name: S2N Quotes WP
    Plugin URI: http://it.plustime.com.au/
    Description: This is a test plugin for CRUD quotes
    Version: 1.0.0
    Author: PlusTime IT
    Author URI: http://it.plustime.com.au/
*/

if ( ! defined( 'ABSPATH' ) ) exit; 

$url = plugin_dir_url(__FILE__);
$dir = plugin_dir_path( __FILE__ );

include( plugin_dir_path( __FILE__ ) . 'options.php');

add_action( 'plugins_loaded' , 's2n_quotes_init_plugin' );

function s2n_quotes_init_plugin(){
    add_action( 'admin_menu' , 's2n_quotes_create_menu' );       
}

function s2n_quotes_create_menu() {
	add_menu_page( __( 'S2N Quotes WP' , 's2n-quotes') , __( 'S2N Quotes WP' , 's2n-quotes') , 'manage_options' , 's2n_quotes_options' , 's2n_quotes_settings_page' , 'dashicons-universal-access-alt' );
	add_action( 'admin_init', 'register_s2n_quotes_settings' );
}

function s2n_quotes_activate() {

    if ( ! class_exists('S2N_Admin_Functions') ) {
        include( plugin_dir_path( __FILE__ ) . 'classes/class.admin.php');
    }
    $admin = new S2N_Admin_Functions();

    $s2n_quotes_settings = get_option( 's2n_quotes_settings' );
    
    $s2n_quotes_defaults = [
            'quotes-ONOFF'     => 'on',
    ]; 

    if( ! is_array( $s2n_quotes_settings ) ){
        $s2n_quotes_settings = [];
    }

    $s2n_quotes_option = wp_parse_args( $s2n_quotes_settings , $s2n_quotes_defaults );     
    
    update_option( 's2n_quotes_settings' , $s2n_quotes_option ); 

}
register_activation_hook( __FILE__ , 's2n_quotes_activate' );

function s2n_quotes_display(){

    $s2n_quotes_settings = get_option( 's2n_quotes_settings' );
    
    if( ! isset( $s2n_quotes_settings['quotes-ONOFF'] ) || $s2n_quotes_settings['quotes-ONOFF'] != 'on' ){
        return '';
    }

    if ( ! class_exists('S2N_Admin_Functions') ) {
        include( plugin_dir_path( __FILE__ ) . 'classes/class.admin.php');
    }
    $admin = new S2N_Admin_Functions();

    return $admin->returnQuoteDisplay();
}
add_action( 'wp_footer', 's2n_quotes_display' );

function s2n_quotes_author( $atts ) {
    global $wp_query;

    if ( ! class_exists('S2N_Admin_Functions') ) {
        include( plugin_dir_path( __FILE__ ) . 'classes/class.admin.php');
    }
    $admin = new S2N_Admin_Functions();

    $html = '';
    if( isset( $wp_query->query_vars['authors'] ) ){
        $author = urldecode( $wp_query->query_vars['authors'] ); 
        $html = $admin->returnAuthorQuotes( $author );
    }else{
        // display Authors
        $html = $admin->returnAuthorsList();
    }


    return $html;
}
add_shortcode( 'S2N-Authors', 's2n_quotes_author' );

function s2n_quotes_scripts( $args ){
    
    $s2n_quotes_settings = get_option( 's2n_quotes_settings' );

    wp_register_style( 's2n-quotes' , plugins_url('/css/front-end.css' , __FILE__ ) , [] ,  '1.0.0', false , 'all' );
    wp_enqueue_style( 's2n-quotes' );    
} 
add_action( 'wp_enqueue_scripts' , 's2n_quotes_scripts');

function S2N_admin_scripts( $args ){
    $current_screen = get_current_screen();
    if( strpos( $current_screen->base , 's2n_quotes_options' ) !== false ) {
        wp_enqueue_script( 's2n-admin' , plugins_url( 'js/admin.js' , __FILE__ ) , [ 'jquery' ] , '1.0.0' , true );
        wp_enqueue_style( 's2n-admin' ); 
    }
} 
add_action( 'admin_enqueue_scripts' , 'S2N_admin_scripts' );

function S2N_quotes_rewrites() {

    add_rewrite_tag('%authors%', '([^&/]+)');
    add_rewrite_rule('authors/([^/]+)/?$', 'index.php?pagename=authors&authors=$matches[1]' , 'top');
 
}
add_action( 'init', 'S2N_quotes_rewrites', 10, 0 );

function S2N_author_query ($v){ 
    return array_merge( ['authors'] , $v ); 
}
add_filter('query_vars', 'S2N_author_query');

function check_author_canonical ($redirect_url){ 
    if( is_page( 'authors' ) ) $redirect_url = false;        
}
add_filter('redirect_canonical', 'check_author_canonical');
