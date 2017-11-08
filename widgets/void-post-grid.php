<?php
namespace voidgrid\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * @since 1.0.0
 */

class Void_Post_Grid extends Widget_Base {   //this name is added to plugin.php of the root folder

	public function get_name() {
		return 'void-post-grid';
	}

	public function get_title() {
		return 'Void Post Grid';   // title to show on elementor
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_categories() {
		return [ 'void-elements' ];    // category of the widget
	}

	public function is_reload_preview_required() {
		return true;
	}

	public function get_script_depends() {		//load the dependent scripts defined in the voidgrid-elements.php
		return [ 'void-grid-equal-height-js', 'void-grid-custom-js' ];
	}

	/**
	 * A list of scripts that the widgets is depended in
	 * @since 1.3.0
	 **/
	protected function _register_controls() {
		#########
		## LAYOUT
		#########
		$this->_register_controls_layout();


		########
		## QUERY
		########
		$this->_register_controls_query();


		#######
		## AJAX
		#######
		$this->_register_controls_ajax();

	}

	private function _register_controls_layout(){
		$this->start_controls_section(
			'section_layout',
			[
				'label' => esc_html__( 'Layout', 'void' ),   //section name for controler view
			]
		);

		$this->add_control(
			'post_type_front_filters_yes',
			[
				'label'   => __( 'Post type filters exposed', 'void' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'yes' => 'Yes',
					'no' => 'No'
				],
				'default' => 'no',
			]
		);

		$this->add_control(
			'terms_front_filters_yes',
			[
				'label'   => __( 'Terms filters exposed', 'void' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'yes' => 'Yes',
					'no'  => 'No'
				],
				'default' => 'no',
			]
		);

		$this->add_control(
			'terms_front_filters_resetall_text',
			[
				'label'   => __( 'Reset filters label', 'void' ),
				Controls_Manager::TEXT,
				'condition' => [
					'terms_front_filters_yes' => ['yes'],
				],
				'default' => ''
			]
		);

		$this->add_control(
			'display_type',
			[
				'label' => esc_html__( 'Choose your desired style', 'void' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'grid' => 'Grid Layout',
					'list' => 'List Layout',
					'first-post-grid' => '1st Full Post then Grid',
					'first-post-list' => '1st Full Post then List',
					'minimal' => 'Minimal Grid'
				],
				'default' => '1'
			]
		);

		$this->add_responsive_control(
			'posts_per_row',
			[
				'label' => esc_html__( 'Posts Per Row', 'void' ),
				'type' => Controls_Manager::SELECT,
				'condition' => [
					'display_type' => ['grid','minimal'],
				],
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'6' => '6',
				],
				'default' => '2',
			]
		);

		$this->add_control(
			'pagination_yes',
			[
				'label' => esc_html__( 'Pagination Enabled', 'void' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'yes' => 'Yes',
					'no' => 'No'
				],
				'default' => 'yes',

			]
		);

		$this->add_control(
			'image_style',
			[
				'label' => esc_html__('Choose your desired featured image style', 'void'),
				'type'  => Controls_Manager::SELECT2,
				'options' => [
					'' => 'Standard',
					'top-left' => 'left top rounded',
					'top-right' => 'left bottom rounded'
				],
				'default'   => '',
			]
		);


		$this->end_controls_section();
	}

	private function _register_controls_query(){
		$this->start_controls_section(
			'section_query',
			[
				'label' => esc_html__( 'Query', 'void' ),   //section name for controler view
			]
		);

		$this->add_control(
			'refer_wp_org',
			[
				'raw' => __( 'For more detail about following filters please refer <a href="https://codex.wordpress.org/Template_Tags/get_posts" target="_blank">here</a>', 'void' ),
				'type' => Controls_Manager::RAW_HTML,
				'classes' => 'elementor-descriptor',
			]
		);

		$this->add_control(
			'post_type',
			[
				'label' => esc_html__( 'Select post type', 'void' ),
				'type' => Controls_Manager::SELECT2,
				'options' => void_grid_post_type(),
			]
		);

		$this->add_control(
			'taxonomy_type',
			[
				'label' => __( 'Select Taxonomy', 'void' ),
				'type' => Controls_Manager::SELECT2,
				'options' => '',
			]
		);

		$this->add_control(
			'terms',
			[
				'label' => __( 'Select Terms (usually categories/tags) * Must Select Taxonomy First', 'void' ),
				'type' => Controls_Manager::SELECT2,
				'options' => '',
				'multiple' => true,
			]
		);

		$this->add_control(
			'terms_front_filters_exclusive',
			[
				'label'   => __( 'Terms filters exclusive (only can be choosed)', 'void' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'unique'    => 'Unique',
					'multiple'  => 'Multiple'
				],
				'default' => 'unique',
			]
		);

		$this->add_control(
			'posts',
			[
				'label' => esc_html__( 'Post Per Page', 'void' ),
				'description' => esc_html__( 'Give -1 for all post & No Pagination', 'void' ),
				'type' => Controls_Manager::NUMBER,
				'default' => -1,
			]
		);

		$this->add_control(
			'offset',
			[
				'label' => esc_html__( 'Post Offset', 'void' ),
				'type' => Controls_Manager::NUMBER,
				'default' => '0'
			]
		);

		$this->add_control(
			'orderby',
			[
				'label' => esc_html__( 'Order By', 'void' ),
				'type' => Controls_Manager::SELECT,
				'options' => voidgrid_post_orderby_options(),
				'default' => 'date',

			]
		);

		$this->add_control(
			'order',
			[
				'label' => esc_html__( 'Order', 'void' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'asc' => 'Ascending',
					'desc' => 'Descending'
				],
				'default' => 'desc',

			]
		);

		$this->add_control(
			'sticky_ignore',
			[
				'label' => esc_html__( 'Sticky Condition', 'void' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'1' => 'Remove Sticky',
					'0' => 'Keep Sticky'
				],

				'default' => '1',
			]
		);



		$this->end_controls_section();
	}

	private function _register_controls_ajax(){
		$this->start_controls_section(
			'section_ajax',
			[
				'label' => 'Ajax',
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'ajax_enabled',
			[
				'label' => esc_html__( 'Ajax Enabled', 'void' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'yes' => 'Yes',
					'no' => 'No'
				],
				'default' => 'yes',
			]
		);

		$this->add_control(
			'ajax_loader',
			[
				'label' => 'Ajax loader',
				'type' => Controls_Manager::MEDIA,
				'condition' => ['ajax_enabled' => 'yes']
			]
		);

		$this->end_controls_section();



		########
		## STYLE
		########
		$this->start_controls_section(
			'section_style_grid',
			[
				'label' => esc_html__( 'Style', 'void' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_control(
			'title_text_transform',
			[
				'label' => esc_html__( 'Title Text Transform', 'void' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html__( 'None', 'void' ),
					'uppercase' => esc_html__( 'UPPERCASE', 'void' ),
					'lowercase' => esc_html__( 'lowercase', 'void' ),
					'capitalize' => esc_html__( 'Capitalize', 'void' ),
				],
				'selectors' => [
					'{{WRAPPER}} .entry-title' => 'text-transform: {{VALUE}};',   //the selector used above in add_control
				],
			]
		);

		$this->add_responsive_control(
			'title_font_size',
			[
				'label' => esc_html__( 'Title Size', 'void' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .entry-title' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_color',
			[
				'label' => esc_html__( 'Title Color', 'void' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .entry-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_color_hover',
			[
				'label' => esc_html__( 'Title Hover Color', 'void' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .entry-title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'meta_color',
			[
				'label' => esc_html__( 'Meta Color', 'void' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .entry-meta a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'meta_hover_color',
			[
				'label' => esc_html__( 'Meta Hover Color', 'void' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .entry-meta a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'meta_color_i',
			[
				'label' => esc_html__( 'Meta Icon Color', 'void' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .entry-meta' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'excerpt_text_transform',
			[
				'label' => esc_html__( 'Excerpt Transform', 'void' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html__( 'None', 'void' ),
					'uppercase' => esc_html__( 'UPPERCASE', 'void' ),
					'lowercase' => esc_html__( 'lowercase', 'void' ),
					'capitalize' => esc_html__( 'Capitalize', 'void' ),
				],
				'selectors' => [
					'{{WRAPPER}} .blog-excerpt p' => 'text-transform: {{VALUE}};',   //the selector used above in add_control
				],
			]
		);

		$this->add_responsive_control(
			'excerpt_font_size',
			[
				'label' => esc_html__( 'Excerpt Size', 'void' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .blog-excerpt p' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'exceprt_color',
			[
				'label' => esc_html__( 'Excerpt Color', 'void' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .blog-excerpt p' => 'color: {{VALUE}};',
				],
			]
		);


		$this->add_responsive_control(
			'te_align',
			[
				'label' => __( 'Text Alignment', 'void' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'void' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'void' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'void' ),
						'icon' => 'fa fa-align-right',
					],
					'justify' => [
						'title' => __( 'Justified', 'void' ),
						'icon' => 'fa fa-align-justify',
					],
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .blog-excerpt p' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_align',
			[
				'label' => __( 'Pagination Alignment', 'void' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'void' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'void' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'void' ),
						'icon' => 'fa fa-align-right',
					],
					'justify' => [
						'title' => __( 'Justified', 'void' ),
						'icon' => 'fa fa-align-justify',
					],
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .void-grid-nav' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'pagi_font_size',
			[
				'label' => esc_html__( 'Pagination Size', 'void' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .void-grid-nav' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {				//to show on the fontend
		$settings = $this->get_settings();
		/*
		if( !empty($settings['taxonomy_type'])){
			$terms = get_terms( array(
				'taxonomy' => $settings['taxonomy_type'],
				'hide_empty' => true,
			));
			foreach ( $terms as $term ){
				$term_id[] = $term -> term_id;
			}
		}
		if(!empty($settings['terms'])){
			$category = implode (", ", $settings['terms']);
		}
		elseif( !empty($settings['taxonomy_type'])) {
			$category=implode(", ", $term_id);
		}
		else{
			$category = '';
		}

		d($terms);
		?>

		<?php
		if(!empty($settings['ajax_enabled'])): ?>
			<script type="text/javascript">
				var VOID_ELEMENTOR__JS_GLOBALS = {
//                post_type_front_filters_yes : <?php //print $settings['post_type_front_filters_yes']; ?>//,
//                terms_front_filters_yes : <?php //print $settings['terms_front_filters_yes']; ?>//,
//                terms_front_filters_exclusive : <?php //print $settings['terms_front_filters_exclusive']; ?>//,
//                post_type : '<?php //print $settings['post_type']; ?>//',
//                posts_per_page : '<?php //print $settings['posts']; ?>//',
//                display_type : '<?php //print $settings['display_type']; ?>//',
//                ajax_enabled : '<?php //print $settings['ajax_enabled']; ?>//',

					settings : <?php print json_encode($settings, JSON_FORCE_OBJECT) ?>
				};
			</script>
			<?php
		endif;
		*/
		?>
        <div class="elementor-shortcode" id="void_elementor-ajax">
			<?php
			//display this but will be replaced by ajax callback, if AJAX enabled
			print do_shortcode( '[voidgrid_sc_post_grid ' . _get_void_shortcode_atts($settings) . ' ]'); ?>
            <!--        terms="'.$category.'"-->
        </div>
		<?php
	}

}

$current_url=esc_url("//".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);

if( strpos( $current_url, 'elementor') == true ){
	add_action( 'wp_footer', function() {

		if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
			return;
		}

		// load our jquery file that sends the $.post request
		wp_enqueue_script( "void-grid-ajax", plugins_url('assets/js/void-ajax.js', dirname(__FILE__)) , array( 'jquery', 'json2' ) );

		// make the ajaxurl var available to the above script
		wp_localize_script( 'void-grid-ajax', 'void_grid_ajax', array(
				'ajaxurl'          => admin_url( 'admin-ajax.php' ),
				'postTypeNonce' => wp_create_nonce( 'voidgrid-post-type-nonce' ),
			)
		);


	} );
}



