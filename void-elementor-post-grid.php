<?php
/**
 * Plugin Name: Void Elementor Post Grid Addon for Elementor Page builder
 * Description: Elementor Post Grid in 5 different style by voidcoders for elementor page builder
 * Version:     2.0
 * Author:      VOID CODERS
 * Plugin URI:  https://voidcoders.com/product/post-grid-add-on-for-elementor-free/
 * Author URI:  http://voidcoders.com
 * Text Domain: void
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require( __DIR__ . '/void-shortcode.php' );   //loading the main plugin

define( 'VOID_ELEMENTS_FILE_', __FILE__ );
define( 'VOID_ELEMENTS_DIR', plugin_dir_path( __FILE__ ) );

require VOID_ELEMENTS_DIR . 'class-gamajo-template-loader.php';
require VOID_ELEMENTS_DIR . 'void-template-loader.php';
require VOID_ELEMENTS_DIR . 'template-tags.php';

    
    function voidgrid_load_elements() {
    // Load localization file
    load_plugin_textdomain( 'void' );

    // Notice if the Elementor is not active
    if ( ! did_action( 'elementor/loaded' ) ) {
        return;
    }

    // Check version required
    $elementor_version_required = '1.0.0';
    if ( ! version_compare( ELEMENTOR_VERSION, $elementor_version_required, '>=' ) ) {
        return;
    }

    // Require the main plugin file
    require( __DIR__ . '/plugin.php' );   //loading the main plugin

}
add_action( 'plugins_loaded', 'voidgrid_load_elements' ); 

// display custom admin notice
function voidgrid_load_elements_notice() { ?>

    <?php if (!did_action( 'elementor/loaded' )  ) : ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php echo sprintf( __( '<a href="%s"  target="_blank" >Elementor Page Builder</a> must be installed and activated for "Void Elementor Post Grid" to work' ),  'https://wordpress.org/plugins/elementor/'); ?></p>
        </div>
    <?php endif; ?>

<?php }
add_action('admin_notices', 'voidgrid_load_elements_notice');



function void_grid_image_size(){
    add_image_size( 'blog-list-post-size', 350 );
}
add_action('init', 'void_grid_image_size');

function void_grid_ajax_process_tax_request() {
    // first check if data is being sent and that it is the data we want   
   
    if( isset( $_POST['postTypeNonce'] ) ){     
        $nonce = $_POST['postTypeNonce'];
        if ( ! wp_verify_nonce( $nonce, 'voidgrid-post-type-nonce' ) ){
            wp_die( 'You are not allowed!');
        }
        $post_type = $_POST['post_type'];
        $taxonomoies = get_object_taxonomies( $post_type, 'names' );
        $taxonomy_name = array();    
        foreach( $taxonomoies as $taxonomy ){            
            $taxonomy_name[] = array( 'name'    => $taxonomy ) ;            
                    
        }
        echo json_encode($taxonomy_name);
        wp_die(); 
    } 
}
add_action('wp_ajax_void_grid_ajax_tax', 'void_grid_ajax_process_tax_request');

function void_grid_ajax_process_terms_request() {
    // first check if data is being sent and that it is the data we want
   
    if( isset( $_POST['postTypeNonce'] ) ){     
        $nonce = $_POST['postTypeNonce'];
        if ( ! wp_verify_nonce( $nonce, 'voidgrid-post-type-nonce' ) ){
            wp_die( 'You are not allowed!');
        }
        $taxonomy_type = $_POST['taxonomy_type'];           
        $term_slug = array();
        $terms = get_terms( $taxonomy_type );                   
        foreach ( $terms as $term ){
            $term_slug[] = array(
                    'id'    => $term -> term_id,
                    'name'  => $term -> name
                );              
        }           
    
        echo json_encode($term_slug);
        wp_die(); 
    } 
}
add_action('wp_ajax_void_grid_ajax_terms', 'void_grid_ajax_process_terms_request');

// add plugin activation time

function void_grid_activation_time(){
    $get_installation_time = strtotime("now");
    add_option('void_grid_elementor_post_grid_activation_time', $get_installation_time ); 
}
register_activation_hook( __FILE__, 'void_grid_activation_time' );

//check if review notice should be shown or not

function void_grid_check_installation_time() {

    $spare_me = get_option('void_grid_spare_me');
    if( !$spare_me ){
        $install_date = get_option( 'void_grid_elementor_post_grid_activation_time' );
        $past_date = strtotime( '-7 days' );
     
        if ( $past_date >= $install_date ) {
     
            add_action( 'admin_notices', 'void_grid_display_admin_notice' );
     
        }
    }
}
add_action( 'admin_init', 'void_grid_check_installation_time' );
 
/**
* Display Admin Notice, asking for a review
**/
function void_grid_display_admin_notice() {
    // wordpress global variable 
    global $pagenow;
    if( $pagenow == 'index.php' ){
 
        $dont_disturb = esc_url( get_admin_url() . '?spare_me=1' );
        $plugin_info = get_plugin_data( __FILE__ , true, true );       
        $reviewurl = esc_url( 'https://wordpress.org/support/plugin/'. sanitize_title( $plugin_info['Name'] ) . '/reviews/' );
        $void_url = esc_url( 'https://voidthemes.com' );
     
        printf(__('<div class="void-grid-review wrap">You have been using <b> %s </b> for a while. We hope you liked it ! Please give us a quick rating, it works as a boost for us to keep working on the plugin ! Also you can visit our <a href="%s" target="_blank">site</a> to get more themes & Plugins<div class="void-grid-review-btn"><a href="%s" class="button button-primary" target=
            "_blank">Rate Now!</a><a href="%s" class="void-grid-review-done"> Already Done !</a></div></div>', $plugin_info['TextDomain']), $plugin_info['Name'], $void_url, $reviewurl, $dont_disturb );
    }
}
// remove the notice for the user if review already done or if the user does not want to
function void_grid_spare_me(){    
    if( isset( $_GET['spare_me'] ) && !empty( $_GET['spare_me'] ) ){
        $spare_me = $_GET['spare_me'];
        if( $spare_me == 1 ){
            add_option( 'void_grid_spare_me' , TRUE );
        }
    }
}
add_action( 'admin_init', 'void_grid_spare_me', 5 );

//add admin css
function void_grid_admin_css(){
     global $pagenow;
    if( $pagenow == 'index.php' ){
        wp_enqueue_style( 'void-grid-admin', plugins_url( 'assets/css/void-grid-admin.css', __FILE__ ) );
    }
}
add_action( 'admin_enqueue_scripts', 'void_grid_admin_css' );