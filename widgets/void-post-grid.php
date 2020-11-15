<?php
namespace voidgrid\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * @since 1.0.0
 */

class Void_Post_Grid extends Widget_Base {
    //this name is added to plugin.php of the root folder

    public function __construct( $data = [], $args = null ) {
        parent::__construct( $data, $args );
        // load default font awesome from elementor
        if(class_exists('\Elementor\Icons_Manager')){
            \Elementor\Icons_Manager::enqueue_shim();
        }
		$this->add_style_depends('google-font-poppins');
		$this->add_style_depends('void-grid-main');
		$this->add_script_depends('void-elementor-grid-js');
	}

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

	// public function is_reload_preview_required() {
	// 	return true;   
	// }

	public function get_script_depends() {		//load the dependent scripts defined in the voidgrid-elements.php
		return [ 'void-grid-equal-height-js', 'void-grid-custom-js' ];
	}

	/**
	 * A list of scripts that the widgets is depended in
	 * @since 1.3.0
	 **/
    protected function _register_controls() {
            
    //start of a control box
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Post Grid Query', 'void' ),   //section name for controler view
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

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'taxonomy_type',
            [
                'label' => __( 'Select Taxonomy', 'void' ),
                'type' => Controls_Manager::SELECT2,
                'options' => (object) array(),                              
            ]
        );
        
        $repeater->add_control(
            'terms',
            [
                'label' => __( 'Select Terms (usually categories/tags) * Must Select Taxonomy First', 'void' ),
                'type' => Controls_Manager::SELECT2,
                'label_block' => true,
                'options' => '',              
                'multiple' => true,
                'condition' => [
                            'taxonomy_type!' =>'',
                        ], 
            ]
        );

        $repeater->add_control(
            'compare',
            [
                'label' => __( 'Compare(Operator)', 'void' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'IN',
                'label_block' => true,
            ]
        );
        $repeater->add_control(
            'reffer_compare',
            [
                'raw' => __( 'To know about operator check <a href="https://elequerybuilder.com/operator/" target="_blank">this</a>', 'void' ),
                'type' => Controls_Manager::RAW_HTML,
                'classes' => 'elementor-descriptor',
            ]
        );

        $this->add_control(
            'tax_fields',
            [
                'label' => __( 'Taxonomy & terms combination', 'void' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'label_block' => true,
                'item_actions' => array('duplicate' => false),
                'default' => [
                    [
                        'taxonomy_type' => '',
                        'terms' => '',
                        
                    ],
                ],
                'title_field' => '{{{ taxonomy_type }}}', 
                          
            ]
        );

        $this->add_control(
            'tax_fields_relation',
            [
                'label' => __( 'Fields Relation', 'void' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'AND', 'void' ),
                'label_off' => __( 'OR', 'void' ),
                'return_value' => 'AND',
                'default' => 'OR',
                
            ]
        );

        $this->add_control(
			'meta_query_section',
			[
				'label' => __( 'Meta query', 'void' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
        );
        
        $this->add_control(
            'meta_key',
            [
                'label' => __( 'Name/Key', 'void' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'options' => '',
                'label_block' => true,

            ]
        );
        $this->add_control(
            'meta_value',
            [
                'label' => __( 'Value', 'void' ),
                'type' => Controls_Manager::TEXT,
                'options' => '',
                'label_block' => true,
                'placeholder' => __( 'i.e. value1, value2', 'void' ),
                'condition' => [
                    'meta_key!' =>'',
                ] 
            ]
        );
        $this->add_control(
            'meta_compare',
            [
                'label' => __( 'Compare(Operator)', 'void' ),
                'type' => Controls_Manager::TEXT,
                'options' => '',
                'label_block' => true,
                'condition' => [
                    'meta_value!' =>'',
                ] 
            ]
        );
        $this->add_control(
            'reffer_custom_compare',
            [
                'raw' => __( 'To know about operator check <a href="https://elequerybuilder.com/operator/" target="_blank">this</a>', 'void' ),
                'type' => Controls_Manager::RAW_HTML,
                'classes' => 'elementor-descriptor',
                'condition' => [
                    'meta_value!' =>'',
                ] 
            ]
        );

        $this->add_control(
          'cat_exclude',
            [
                'label'       => __( 'Include / Exclude With Category ID', 'void' ),
                'label_block' => true,
                'type'        => Controls_Manager::TEXT,
                'description' => __( 'Get post category id and add them here. To Include use the id(s) directly (Example: 1,2,3), To exclude category add a minus sign before the category ID (Example : -1,-44,-3343)', 'void' ),
                'placeholder' => __( '-1,-2,-33,10,11', 'void' ),
                'condition' => [
                    'post_type' => 'post',
                ],
            ]
        );
        $this->add_control(
            'reffer_category_find',
            [
                'raw' => __( 'For finding out your category ID follow <a href="https://voidcoders.com/find-category-id-wordpress/" target="_blank">this</a>', 'void' ),
                'type' => Controls_Manager::RAW_HTML,
                'classes' => 'elementor-descriptor',
                'condition' => [
                    'post_type' => 'post',
                ],
            ]
        );


        $this->end_controls_section();


		$this->start_controls_section(
            'section_content2',
            [
                'label' => esc_html__( 'Pagination & Setting', 'void' ),   //section name for controler view
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
                'pagination_yes',
                [
                    'label' => esc_html__( 'Pagination Enabled', 'void' ),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        1 => 'Yes',
                        2 => 'No'
                    ],
                    'default' => 1,
                    'condition' => [
                        'posts!' => -1,
                    ]
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
                'label' => esc_html__( 'Post Order', 'void' ),
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

        $this->start_controls_section(
            'section_content3',
            [
                'label' => esc_html__( 'Post Style & Image Settings', 'void' ),   //section name for controler view
            ]
        );


        $this->add_control(
            'display_type',
            [
                'label' => esc_html__( 'Choose your desired style', 'void' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'grid-1' => esc_html__( 'Grid style', 'void' ), 
                    'list-1' => esc_html__( 'List style', 'void' ), 
                    'first-full-post-grid-1' => esc_html__( '1st Full Post then Grid', 'void' ),
                    'first-full-post-list-1' => esc_html__( '1st Full Post then List', 'void' ),
                    'minimal' => esc_html__( 'Minimal Grid', 'void' )
                ],
                'default' => 'grid-1',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'posts_per_row',
            [
                'label' => esc_html__( 'Posts Per Row', 'void' ),
                'type' => Controls_Manager::SELECT,
                'condition' => [
                    'display_type' => ['grid-1', 'grid-2', 'minimal'],
                ],
                'options' => [
                    '1' => esc_html__( '1', 'void' ),
                    '2' => esc_html__( '2', 'void' ),
                    '3' => esc_html__( '3', 'void' ),
                    '4' => esc_html__( '4', 'void' ),
                    '6' => esc_html__( '6', 'void' ),
                ],
                'default' => '2',
            ]
        );
		
        
        $this->add_control(
            'filter_thumbnail',
            [
                'label' => esc_html__( 'Image Condition', 'void' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                     0 => esc_html__( 'Show All', 'void' ),
                    'EXISTS' => esc_html__( 'With Image', 'void' ),
                    'NOT EXISTS' => esc_html__( 'Without Image', 'void' ),
                ],
                'default' => 0,

            ]
        );

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image', // Actually its `image_size`.
				'default' => 'large',
				'exclude' => [ 'custom' ],
                'condition' => [
                    'filter_thumbnail!' => 'NOT EXISTS',
                ],
                
			]
		);
        $this->add_control(
            'image_style',
            [
                'label' => esc_html__('Featured Image Style', 'void'),
                'type'  => Controls_Manager::SELECT2,
                'options' => [
                    'standard' => esc_html__( 'Standard', 'void' ),
                    'top-left' => esc_html__( 'left top rounded', 'void' ),
                    'top-right' => esc_html__( 'left bottom rounded', 'void' )
                ],
                'default'   => 'standard',
                'condition' => [
                    'filter_thumbnail!' => 'NOT EXISTS',
                ],
            ]
        );
       
      
        $this->end_controls_section();
        
        $this->start_controls_section(
            'section_filter_void_grid',
            [
                'label' => esc_html__( 'Filter bar', 'void' ),
                'tab' => Controls_Manager::TAB_CONTENT,
                'condition' => [
                    'display_type!' => [ 'first-full-post-grid-1', 'first-full-post-list-1' ],
                ],
            ]
        );

        $this->add_control(
			'void_show_filter_bar',
			[
				'label' => __( 'Show', 'void' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'void' ),
				'label_off' => __( 'Hide', 'void' ),
				'return_value' => 'true',
				'default' => 'false',
			]
        );

        $this->add_control(
			'void_show_all_filter_bar',
			[
				'label' => __( 'All show', 'void' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'void' ),
				'label_off' => __( 'Off', 'void' ),
				'return_value' => 'true',
                'default' => 'true',
                'condition' => ['void_show_filter_bar' => 'true']
			]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_style_grid_title',
            [
                'label' => esc_html__( 'Title', 'void' ),
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

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => __( 'Typography', 'void' ),
                'scheme' => Typography::TYPOGRAPHY_1,
                'fields_options' => [
                    'typography' => [
                        'default' => 'yes'
                    ],
                    'font_weight' => [
                        'default' => '300',
                    ],
                ],
				'selector' => '{{WRAPPER}} .entry-title',
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

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_grid_meta',
            [
                'label' => esc_html__( 'Meta', 'void' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'display_type!' => ['list-1', 'minimal'],
                ],
            ]
        );

        $this->add_responsive_control(
            'meta_font_size',
            [
                'label' => esc_html__( 'Meta Size', 'void' ),
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
                'default' => [
					'unit' => 'px',
					'size' => 15,
				],
                'selectors' => [
                    '{{WRAPPER}} .entry-meta span a' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'meta_typography',
				'label' => __( 'Typography', 'void' ),
				'scheme' => Typography::TYPOGRAPHY_1,
				'{{WRAPPER}} .entry-meta span a',
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
                    '{{WRAPPER}} .entry-meta i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
        
        $this->start_controls_section(
            'section_style_grid_excerpt',
            [
                'label' => esc_html__( 'Excerpt', 'void' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'display_type!' => ['grid-1'],
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

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'excerpt_typography',
				'label' => __( 'Typography', 'void' ),
				'scheme' => Typography::TYPOGRAPHY_1,
				'{{WRAPPER}} .blog-excerpt p',
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

        $this->end_controls_section();
        
        $this->start_controls_section(
            'section_style_grid_pagination',
            [
                'label' => esc_html__( 'Pagination', 'void' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'posts!' => -1,
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
                    '{{WRAPPER}} .void-grid-nav, {{WRAPPER}} .void-grid-nav a' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'pagination_typography',
				'label' => __( 'Typography', 'void' ),
				'scheme' => Typography::TYPOGRAPHY_1,
				'{{WRAPPER}} .void-grid-nav, {{WRAPPER}} .void-grid-nav a',
			]
		);

        $this->add_responsive_control(
            'pagination_align',
            [
                'label' => __( 'Alignment', 'void' ),
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

        $this->end_controls_section();

	}

	protected function render() {
        //to show on the fontend 
        $settings = $this->get_settings();

        global $col_no, $count, $col_width, $display_type, $is_filter;

        $post_type        = isset($settings['post_type'])? $settings['post_type']: '';
        $filter_thumbnail = isset($settings['filter_thumbnail'])? $settings['filter_thumbnail']: '';
        $cat_exclude      = isset($settings['cat_exclude'])? $settings['cat_exclude']: ''; // actually include or exclude both
        $display_type     = isset($settings['display_type'])? (($settings['display_type'] != '')? $settings['display_type']: 'grid-1'): 'grid-1';   
        $posts            = isset($settings['posts'])? $settings['posts']: '';
        $posts_per_row    = isset($settings['posts_per_row'])? $settings['posts_per_row']: '';      
        $image_style      = isset($settings['image_style'])? $settings['image_style']: 'standard';
        $orderby          = isset($settings['orderby'])? $settings['orderby']: '';
        $order            = isset($settings['order'])? $settings['order']: '';
        $offset           = isset($settings['offset'])? $settings['offset']: ''; 
        $sticky_ignore    = isset($settings['sticky_ignore'])? $settings['sticky_ignore']: '';
        $pagination_yes   = isset($settings['pagination_yes'])? $settings['pagination_yes']: '';
        $image_size       = isset($settings['image_size'])? $settings['image_size']: 'standart';
        $is_filter        = isset($settings['void_show_filter_bar'])? $settings['void_show_filter_bar']: 'false';
        $is_all_filter    = isset($settings['void_show_all_filter_bar'])? $settings['void_show_all_filter_bar']: 'false';

        $all_terms = [];

        //build variable needed for tax_query
        if( !empty($settings[ 'tax_fields' ][0]['taxonomy_type']) ){
    
            if( !empty($settings[ 'tax_fields_relation' ]) ){
                $tax_query[ 'relation' ] = $settings[ 'tax_fields_relation' ];
            }else{
                $tax_query[ 'relation' ] = 'OR';
            }
            foreach ($settings[ 'tax_fields' ] as $key => $value) {
                if( !empty($value['taxonomy_type'])){
                    // remove _id key set by ELEMENTOR CODE
                    unset( $value[ '_id' ] );
                    //as WP_QUERY uses taxonomy key not taxonomy_type
                    $value['taxonomy'] = $value['taxonomy_type'];
                    unset( $value['taxonomy_type'] );

                    $value['terms'] = is_array($value['terms']) ? $value['terms'] : [];
                    //if current post is chosen, get current post terms based on taxonomy chosen
                    foreach( $value[ 'terms' ] as $index => $val ){
                        if( $val == 'current' ){
                            unset( $value[ 'terms' ][$index] );
                            $current_post_terms = get_the_terms( get_the_ID(), $value['taxonomy']  );
                            foreach( $current_post_terms as $index => $term ){
                                //only push terms array if that term is not actively selected, concetaning with '' to returned ineger term_id into string to be used on in_arry as select returns as array
                                if( !( in_array( $term->term_id . '', $value['terms'] ) ) ){
                                    array_push( $value['terms'], $term->term_id );
                                }                       
                            }
                        }
                    }

                    // set all terms on empty term input under the taxonomy
                    if(empty($value[ 'terms' ])){
                        $terms = get_terms( array(
                            'taxonomy' => $value['taxonomy'],
                            'hide_empty' => false
                        ) );
                        foreach($terms as $term_key => $term_val){
                            $value['terms'][] = $term_val->term_id;
                        }
                    }
                    $tax_query[] = $value;
                    $all_terms[$key] = $value['terms'];
                }else{
                    $tax_query = '';
                }
            }

        }else{
            $tax_query = '';
        }

        // process unique terms according to and, or relation for filtering
        $unique_terms = [];
        if(count($all_terms) > 1){
            for( $i=0; $i < count($all_terms); $i++){
                if($i < (count($all_terms)-1) ){
                    $tmp_array = array_merge($unique_terms, $all_terms[$i]);
                    if($tax_query[ 'relation' ] == 'AND'){
                        $unique_terms = array_intersect($tmp_array, $all_terms[$i+1]);
                    }else{
                        $unique_terms = array_unique(array_merge($tmp_array, $all_terms[$i+1]));
                    }
                }
            }
        }else{
            $unique_terms = isset($all_terms[0])? $all_terms[0]: [];
        }

        $meta_query = [];

        if(!empty($settings['meta_value']) || !empty($settings['meta_key'])){
            $meta['key'] = isset($settings['meta_key'])? $settings['meta_key']: '';
            $meta['value'] = isset($settings['meta_value'])? $settings['meta_value']: '';
            $meta['compare'] = isset($settings['meta_compare'])? $settings['meta_compare']: '';
            $meta_query[] = $meta;
        }

        if($filter_thumbnail){
            $void_image_condition = [
                [
                    'key' => '_thumbnail_id',
                    'compare' => $filter_thumbnail,
                ]
            ];
        } else {
            $void_image_condition='';
        }

        $meta_query[] = $void_image_condition;

        set_transient('void_grid_image_size', $image_size, '60' );
    
        $count = 0;         

        // calculate column width by post per row 
        $col_width = ( ($posts_per_row != '') ? (12 / $posts_per_row): 12 );
        // assign column number by post per row
        $col_no = ( ($posts_per_row != '') ? $posts_per_row : 1 );
    
        $templates = new \Void_Template_Loader;
    
        $grid_query= null;
    
        if ( get_query_var('paged') ) {
            $paged = get_query_var('paged');
        } elseif ( get_query_var('page') ) { // if is static front page
            $paged = get_query_var('page');
        } else {
            $paged = 1;
        }
    
        $args = [
            'post_type'      => $post_type,
            'meta_query'     => $meta_query,
            'cat'            => $cat_exclude,        // actually include or exclude both  
            'post_status'    => 'publish',
            'posts_per_page' => $posts, 
            'paged'          => $paged,   
            'tax_query'      => $tax_query,
            'orderby'        => $orderby,
            'order'          => $order,   //ASC / DESC
            'ignore_sticky_posts' => $sticky_ignore,
            'void_grid_query' => 'yes',
            'void_set_offset' => $offset,
        ];
    
        $grid_query = new \WP_Query( $args );
        
        global $post_count;
        $post_count = $posts;

        echo '<div class="void-elementor-post-grid-wrapper">';
            ?>
            <div class="void-Container <?php echo esc_attr($image_style); ?>">
            <!-- turn on filter section if it's on in settings. this section needs extra markup -->
            <?php if($is_filter == 'true' && !in_array($display_type, ['first-full-post-grid-1', 'first-full-post-list-1'])): ?>
                <div class="shuffle-wrapper">
                    <div class="void-row">
                        <div class="void-col-md-12">
                            <div class="btn-group btn-group-toggle void-elementor-post-grid-shuffle-btn" data-toggle="buttons">
                            <!-- all filter show according to the settings -->
                                <?php if($is_filter == 'true' && $is_all_filter == 'true') : ?>
                                    <label class="btn active">
                                        <input class="void-shuffle-all-filter" type="radio" name="vepg-shuffle-filter" value="all" checked="checked" /><?php esc_html_e('All', 'void'); ?>
                                    </label>
                                <?php endif; ?>
                                <?php foreach($unique_terms as $k => $v) :
                                    $term = get_term($v);
                                ?>
                                <!-- show all terms with filter button -->
                                    <label class="btn <?php echo esc_attr((!$is_all_filter && $k==0)? 'active': ''); ?>">
                                        <input type="radio" name="vepg-shuffle-filter" value="<?php echo esc_attr($term->term_id); ?>" <?php echo esc_attr((!$is_all_filter && $k==0)? 'checked="checked"': ''); ?> /><?php echo esc_html($term->name); ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <!-- shuffle div start. if filter is on -->
                    <div class="shuffle-box void-elementor-post-grid-<?php echo esc_attr($display_type); ?>">
            <?php else: ?>
            <!-- only this div will show if there was no filter -->
                <div class="void-row">
            <?php endif;
            // archive page conditon for using wp default query.
                if(is_archive()){
                    // wp default query
                    if(have_posts()):
                        while ( have_posts() ) : the_post();
                        $count++;
                        $templates->get_template_part( 'content', $display_type );
                
                        endwhile; // End of posts loop found posts
                            if($is_filter == 'true' && !in_array($display_type, ['first-full-post-grid-1', 'first-full-post-list-1'])): ?>
                            <!-- filter section closing divs -->
                                </div>
                            <div class="void-col-md-<?php echo esc_attr( $col_width );?> filter-sizer"></div>
                        </div>
                        <?php else: ?>
                        <!-- only this div will be closed if there is no filter -->
                        </div>
                        <?php endif;
                        if($pagination_yes==1) : 
                            //Start of pagination condition 
                            global $wp_query;
                            $big = 999999999; // need an unlikely integer
                            $current = max(1,$paged );
                            $paginate_args = [
                                'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))) ,
                                'format' => '?paged=%#%',
                                'current' => $current,
                                'show_all' => False,
                                'prev_next' => True,
                                'prev_text' => esc_html__('« Previous') ,
                                'next_text' => esc_html__('Next »') ,
                                'type' => 'plain',
                                'add_args' => False,
                            ];
                
                            $pagination = paginate_links($paginate_args); ?>
                            <div class="col-md-12">
                                <nav class='pagination wp-caption void-grid-nav'> 
                                <?php echo $pagination; ?>
                                </nav>
                            </div>
                        <?php
                        endif; //end of pagination condition
                    else:
                        $templates->get_template_part( 'content', 'none' );
                    endif;
                }else{
                    // custom query will be work if this is not archive page
                    if ( $grid_query->have_posts() ) : 
                
                            /* Start the Loop */
                        while ( $grid_query->have_posts() ) : $grid_query->the_post();  // Start of posts loop found posts
                            $count++;
                            $templates->get_template_part( 'content', $display_type );
                
                        endwhile; // End of posts loop found posts
                            if($is_filter == 'true' && !in_array($display_type, ['first-full-post-grid-1', 'first-full-post-list-1'])): ?>
                             <!-- filter section closing divs -->
                            </div>
                        <div class="void-col-md-<?php echo esc_attr( $col_width );?> filter-sizer"></div>
                        </div>
                        <?php else: ?>
                        <!-- only this div will be closed if there is no filter -->
                        </div>
                        <?php endif;
                        if($pagination_yes==1) :  //Start of pagination condition 
                            global $wp_query;
                            $big = 999999999; // need an unlikely integer
                            $totalpages = $grid_query->max_num_pages;
                            $current = max(1,$paged );
                            $paginate_args = [
                                'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))) ,
                                'format' => '?paged=%#%',
                                'current' => $current,
                                'total' => $totalpages,
                                'show_all' => False,
                                'end_size' => 1,
                                'mid_size' => 3,
                                'prev_next' => True,
                                'prev_text' => esc_html__('« Previous') ,
                                'next_text' => esc_html__('Next »') ,
                                'type' => 'plain',
                                'add_args' => False,
                            ];
                
                            $pagination = paginate_links($paginate_args); ?>
                            <div class="col-md-12">
                                <nav class='pagination wp-caption void-grid-nav'> 
                                <?php echo $pagination; ?>
                                </nav>
                            </div>
                        <?php endif; //end of pagination condition ?>
            
            
                    <?php else :   //if no posts found
            
                        $templates->get_template_part( 'content', 'none' );
            
                    endif; //end of post loop 
                }
                ?>
                
            </div>
            
            <?php
            wp_reset_postdata();    
		echo '</div>';
	}

}



