<?php
/**
 * Plugin Name: Void Elementor Post Grid Addon for Elementor Page builder
 * Description: Elementor Post Grid in 5 different style by voidcoders for elementor page builder
 * Version:     2.1.6
 * Author:      VOID CODERS
 * Plugin URI:  https://voidcoders.com/product/post-grid-add-on-for-elementor-free/
 * Author URI:  http://voidcoders.com
 * Text Domain: void
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define('VOID_GRID_VERSION', '2.1.6');
define('VOID_GRID_PLUGIN_URL', trailingslashit(plugin_dir_url( __FILE__ )));
define('VOID_GRID_PLUGIN_DIR', trailingslashit(plugin_dir_path( __FILE__ )));

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
    require( __DIR__ . '/plugin.php' );  
    //loading the main plugin
    // helper file for this plugin. currently used for gettings all post type. also used for ajax request handle
    require __DIR__ . '/helper/helper.php';
    // taxonom data updater include
    require __DIR__ . '/data-updater/init.php';

}
add_action( 'plugins_loaded', 'voidgrid_load_elements' ); 

// display custom admin notice
function voidgrid_load_elements_notice() { ?>
    <?php if ( ! did_action( 'elementor/loaded' ) ) : ?>
        <div class="notice notice-warning is-dismissible">
            <?php if ( file_exists( WP_PLUGIN_DIR . '/elementor/elementor.php' ) ) : ?>
                    <p><?php echo sprintf( __( '<a href="%s" class="button button-primary">Active Now</a> <b>Elementor Page Builder</b> must be activated for <b>"Void Elementor Post Grid"</b> to work' ),  wp_nonce_url( 'plugins.php?action=activate&plugin=elementor/elementor.php&plugin_status=all&paged=1', 'activate-plugin_elementor/elementor.php') ); ?></p>
            <?php else : ?>
                    <p><?php echo sprintf( __( '<a href="%s" class="button button-primary">Install Now</a> <b>Elementor Page Builder</b> must be installed for <b>"Void Elementor Post Grid"</b> to work' ),  wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' )); ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php }
add_action('admin_notices', 'voidgrid_load_elements_notice');

/**
 * data update call back function
 *
 * @since 2.1
 */
function void_grid_data_update_notice() {

	$version = get_option('void_grid_active_info');
	$update_info = get_option('VOID_GRID_DATA_UPDATE');
	$active_time = get_option('void_grid_elementor_post_grid_activation_time');

	if ( (!isset($update_info['taxonomy_repeater']['status']) || $update_info['taxonomy_repeater']['status'] == '0') && $active_time ) {
		?>
		<div class="notice notice-error">
			<?php echo sprintf( 
				__( '<div class="void-grid-message-inner">
						<div class="void-grid-message-icon">
							<img class="void-grid-notice-icon" src="%s" alt="Void post grid builder logo">
						</div>
						<div class="void-grid-message-content">
							<strong>Void post grid - Requires Database update.</strong>
							<p>Please Press Update now button to update your database for the plugin. We suggest you take a full backup of your site or database before you proceed.</p>
						</div>
						<div class="void-grid-message-action">
							<a class="void-grid-button" href="%s">Update Now</a>
						</div>
					</div>'
				),
					plugins_url('void-elementor-post-grid-addon-for-elementor-page-builde/assets/img/void-grid-logo.png'),
					admin_url('?void_grid_database_updater=taxonomy-repeater')
			); ?>
			
		</div>
	<?php
	}
}

add_action('admin_notices', 'void_grid_data_update_notice');

function void_grid_image_size(){
    add_image_size( 'blog-list-post-size', 350 );
}
add_action('init', 'void_grid_image_size');

/**
 * load js in elementor editor panel call back function
 *
 * @since 1.0.0
 */
function void_grid_elementor_js_load() {
    // load our jquery file that sends the $.post request
    wp_enqueue_script( "void-grid-ajax", plugins_url('assets/js/void-ajax.js', VOID_ELEMENTS_FILE_ ) , array( 'jquery', 'json2' ), VOID_GRID_VERSION, true );

    // make the ajaxurl var available to the above script
    wp_localize_script( 'void-grid-ajax', 'void_grid_ajax', array(
        'ajaxurl'          => admin_url( 'admin-ajax.php' ),
        'postTypeNonce' => wp_create_nonce( 'void_grid-post-type-nonce' ),
        ) 
    );
}
add_action( 'elementor/editor/after_enqueue_scripts', 'void_grid_elementor_js_load');

// shuffle handle js register. enqueue will be occured when the widget will be dragged
function void_elementor_post_grid_js_load(){
    wp_register_script( 'void-elementor-grid-js', VOID_GRID_PLUGIN_URL . 'assets/js/plugin.js', array('jquery'), VOID_GRID_VERSION, true );
}

add_action( 'elementor/frontend/before_enqueue_scripts', 'void_elementor_post_grid_js_load');

// font and css register for front-end design. enqueue will be occured when the widget will be dragged
function void_elementor_post_grid_css_load(){
    wp_register_style( 'google-font-poppins', 'https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap', [], VOID_GRID_VERSION );
    wp_register_style( 'void-grid-main', plugins_url ( '/assets/css/main.css', VOID_ELEMENTS_FILE_ ),false, VOID_GRID_VERSION,'all');
}

add_action( 'elementor/frontend/before_enqueue_styles', 'void_elementor_post_grid_css_load');

// add plugin activation time
function void_grid_activation_time(){
    add_action( 'wp_loaded', 'void_grid_wp_load_action' );
}
register_activation_hook( __FILE__, 'void_grid_activation_time' );


/**
 * action after wp load function
 *
 * @return void
 * @since 2.1
 */
function void_grid_wp_load_action(){
	update_option('void_grid_active_info', [
			'version' => VOID_GRID_VERSION,
			'void_grid_activation_time' => strtotime("now"),
		]);
}

//check if review notice should be shown or not

function void_grid_check_installation_time() {

    $spare_me = get_option('void_grid_spare_me');
    if( !$spare_me ){
        $install_info = get_option( 'veqb_active_info' );
        $install_date = isset($install_info['void_grid_activation_time'])? $install_info['void_grid_activation_time']: '';
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
    $update_info = get_option('VOID_GRID_DATA_UPDATE');
	$active_time = get_option('void_grid_elementor_post_grid_activation_time');
    if( $pagenow == 'index.php' || !$update_info || $active_time ){
        wp_enqueue_style( 'void-grid-admin', plugins_url( 'assets/css/void-grid-admin.css', __FILE__ ) );
    }
}
add_action( 'admin_enqueue_scripts', 'void_grid_admin_css' );
