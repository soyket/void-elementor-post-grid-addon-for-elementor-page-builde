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
        $this->add_control(
            'taxonomy_type',
            [
                'label' => __( 'Select Taxonomy', 'void' ),
                'type' => Controls_Manager::SELECT2,
                'options' => '', 
                'condition' => [
                            'post_type!' =>'',
                        ],                              
            ]
        );
        
        $this->add_control(
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

        $this->add_control(
          'cat_exclude',
          [
             'label'       => __( 'Include / Exclude With Category ID', 'void' ),
             'label_block' => true,
             'type'        => Controls_Manager::TEXT,
             'description' => __( 'Get post category id and add them here. To Include use the id(s) directly (Example: 1,2,3), To exclude category add a minus sign before the category ID (Example : -1,-44,-3343)', 'void' ),
             'placeholder' => __( '-1,-2,-33,10,11', 'void' ),
          ]
        );
        $this->add_control(
            'reffer_category_find',
            [
                'raw' => __( 'For finding out your category ID follow <a href="https://voidcoders.com/find-category-id-wordpress/" target="_blank">this</a>', 'void' ),
                'type' => Controls_Manager::RAW_HTML,
                'classes' => 'elementor-descriptor',
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
                    '1' => 'Grid Layout', 
                    '2' => 'List Layout', 
                    '3' => '1st Full Post then Grid',
                    '4' => '1st Full Post then List',
                    '5' => 'Minimal Grid'
                ],
                'default' => '1'
            ]
        );

        $this->add_control(
            'posts_per_row',
            [
                'label' => esc_html__( 'Posts Per Row', 'void' ),
                'type' => Controls_Manager::SELECT,
                'condition' => [
                    'display_type' => ['1','5'],
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
                    '1' => 'Standard',
                    '2' => 'left top rounded',
                    '3' => 'left bottom rounded'
                ],
                'default'   => '1',
                'condition' => [
                    'filter_thumbnail!' => 'NOT EXISTS',
                ],
            ]
        );
       
      
		$this->end_controls_section();




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
		echo'<div class="elementor-shortcode">';
            echo do_shortcode('[voidgrid_sc_post_grid filter_thumbnail="'.$settings['filter_thumbnail'].'" cat_exclude="'.$settings['cat_exclude'].'" post_type="'.$settings['post_type'].'" pagination_yes="'.$settings['pagination_yes'].'" display_type="'.$settings['display_type'].'" posts="'.$settings['posts'].'" posts_per_row="'.$settings['posts_per_row'].'" image_style="'.$settings['image_style'].'" sticky_ignore="'.$settings['sticky_ignore'].'"  orderby="'.$settings['orderby'].'" order="'.$settings['order'].'" offset="'.$settings['offset'].'"  terms="'.$category.'" taxonomy_type="'.$settings['taxonomy_type'].'" image_size="'. $settings['image_size'] .'" ]');    
		echo'</div>';
	}

}

$current_url=esc_url("//".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);

if( strpos( $current_url, 'action=elementor') == true ){
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



