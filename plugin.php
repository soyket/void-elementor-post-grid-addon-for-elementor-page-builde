<?php
namespace voidgrid;  //main namespace

global $void_post_grid;
$void_post_grid= array_map('basename', glob(dirname( __FILE__ ) . '/widgets/*.php'));

use voidgrid\Widgets\Void_Post_Grid;   //path define same as class name of the widget

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



// Add a custom category for panel widgets
add_action( 'elementor/init', function() {
	\Elementor\Plugin::$instance->elements_manager->add_category(
		'void-elements',                 // the name of the category
		[
			'title' => esc_html__( 'VOID ELEMENTS', 'void' ),
			'icon' => 'fa fa-header', //default icon
		],
		1 // position
	);
} );



/**
 * Main Plugin Class
 *
 * Register new elementor widget.
 *
 * @since 1.0.0
 */
class Plugin {

	private $_template_loader;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */

	public function __construct() {
		$this->add_actions();

		$this->_template_loader = new \Void_Template_Loader;
	}

	/**
	 * Add Actions
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function add_actions() {
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'on_widgets_registered' ] );

		add_action( 'elementor/frontend/after_register_scripts', function() {
			$_plugin_file_uri = plugin_dir_url(__FILE__);

			wp_enqueue_style( 'void-grid-main', $_plugin_file_uri . 'assets/css/main.css', false, '1.0', 'all');
			wp_enqueue_style( 'void-grid-bootstrap', $_plugin_file_uri . 'assets/css/bootstrap.min.css', false, '3.3.7', 'all');
			//load equal height js
			wp_enqueue_script( 'void-grid-equal-height-js', $_plugin_file_uri . 'assets/js/jquery.matchHeight-min.js', array(), '3.3.7', true );
			//load custom js
			wp_enqueue_script( 'void-grid-custom-js', $_plugin_file_uri . 'assets/js/custom.js', array(), '1.0', true );

			//Give parameters to JS script
			wp_register_script( 'void-grid-front-js', $_plugin_file_uri . 'assets/js/front.js', [], filemtime( VOID_ELEMENTS_DIR . '/assets/js/front.js' ), TRUE );
			wp_localize_script( 'void-grid-front-js', 'void_grid__js__params', [
					'ajax_url' => admin_url( 'admin-ajax.php' ),
				]
			);
			wp_enqueue_script( 'void-grid-front-js' );
		} );

		//ajax action 'display_filtered_posts'
		add_action('wp_ajax_display_filtered_posts', array( $this, 'ajax_display_filtered_posts'));
		add_action('wp_ajax_nopriv_display_filtered_posts', array( $this, 'ajax_display_filtered_posts'));
		//ajax action 'display_pagination'
		add_action('wp_ajax_display_pagination', array( $this, 'ajax_display_pagination'));
		add_action('wp_ajax_nopriv_display_pagination', array( $this, 'ajax_display_pagination'));

	}

	/**
	 * On Widgets Registered
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function on_widgets_registered() {
		$this->includes();
		$this->register_widget();
	}

	/*
	 *	Ajax call construct loop and results
	 */
	public function ajax_display_filtered_posts() {

		require VOID_ELEMENTS_DIR . '/helper/helper.php';

		if(!empty($_POST['settings']) && !empty($_POST['settings_dynamic'])) {
			//data setted in AJAX
			$settings = array_merge( $_POST['settings'], $_POST['settings_dynamic'] );
		}

		//Posts
		print do_shortcode( '[voidgrid_sc_post_grid__posts ' . _get_void_shortcode_atts($settings) . ' ]');

		die();
	}

	/**
	 * Includes
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function includes() {
		global $void_post_grid;              //include the widgets here
		require VOID_ELEMENTS_DIR . '/helper/helper.php';
		foreach($void_post_grid as $key => $value){
			require VOID_ELEMENTS_DIR . '/widgets/'.$value;
		}
	}

	/**
	 * Register Widget
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function register_widget() {
		//this is where we create objects for each widget the above  ->use voidgrid\Widgets\Hello_World; is needed

		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Void_Post_Grid() );
	}
}

new Plugin();
