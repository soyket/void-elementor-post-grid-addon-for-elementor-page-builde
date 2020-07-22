<?php
namespace voidgrid;  //main namespace
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

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	
	public function __construct() {
		$this->add_actions();
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
			wp_enqueue_style( 'void-grid-main', plugins_url ( '/assets/css/main.css', VOID_ELEMENTS_FILE_ ),false, VOID_GRID_VERSION,'all');
			wp_enqueue_style( 'void-grid-bootstrap', plugins_url ( '/assets/css/bootstrap.min.css', VOID_ELEMENTS_FILE_ ),false,'3.3.7','all');
			//load equal height js
			wp_enqueue_script( 'void-grid-equal-height-js', plugins_url ( '/assets/js/jquery.matchHeight-min.js', VOID_ELEMENTS_FILE_ ), array(), '3.3.7', true );
			//load custom js
			wp_enqueue_script( 'shuffle-min', plugins_url ( '/assets/js/shuffle.min.js', VOID_ELEMENTS_FILE_ ), array(), VOID_GRID_VERSION, true );
			wp_enqueue_script( 'void-grid-custom-js', plugins_url ( '/assets/js/custom.js', VOID_ELEMENTS_FILE_ ), array(), VOID_GRID_VERSION, true );
			wp_enqueue_script( 'void-grid-plugin', plugins_url ( '/assets/js/plugin.js', VOID_ELEMENTS_FILE_ ), array(), VOID_GRID_VERSION, true );
		} );

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

	/**
	 * Includes
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function includes() {
		$void_post_grid= array_map('basename', glob(dirname( __FILE__ ) . '/widgets/*.php'));
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
