<?php
// voidgrid Shortcodes
function voidgrid_sc_post_grid( $atts ) {
	global $count, $col_desktop, $col_tablet, $col_mobile, $post_count;
	$count=$post_count='';
	$col_desktop=$col_tablet=$col_mobile = array();


	/**
	 * Attributes
	 */
	$atts = _get_void__query_default_atts_merged($atts);
	extract($atts);
	?>

	<?php
	if( $ajax_enabled == 'yes'): ?>
        <script type="text/javascript">
            var VOID_ELEMENTOR__JS_GLOBALS = {
                settings : <?php print json_encode($atts, JSON_FORCE_OBJECT) ?>
            };
        </script>
		<?php
	endif;


	$grid_query= null;

	ob_start();
	?>
    <div class="content-area void-grid">
        <div class="site-main <?php echo esc_html( $display_type . ' '. $image_style); ?>" >
			<?php
			//################
			//## Terms filters
			//################
			if(!empty($terms_front_filters_yes) ):
				if(empty($terms)){ //no specific terms defined in Back-Office widget, so get all of the choosed taxonomy
					$front_filters_terms = get_terms( array(
						'taxonomy'   => $taxonomy_type,
						'hide_empty' => TRUE,
					) );
				}
				else{
					$front_filters_terms = $terms;
				}

				if( !empty($front_filters_terms)) :
					?>
                    <div class="row">
                        <div class="col-md-12" id="terms_filters">
							<?php
							if(!empty($terms_front_filters_resetall_text)):
								?>
                                <span class="filter_terms" data-void_elementor_ajax_filter_terms="reset_all" data-void_elementor_exclusive="<?php print $terms_front_filters_exclusive; ?>"><?php
									print $terms_front_filters_resetall_text; ?>
                                </span>
								<?php
							endif;
							foreach($front_filters_terms as $front_filters_term):?>
                                <span class="filter_terms" data-void_elementor_ajax_filter_terms="<?php print $front_filters_term->slug; ?>" data-void_elementor_exclusive="<?php print $terms_front_filters_exclusive; ?>" data-taxonomy="<?php print $front_filters_term->taxonomy; ?>"><?php
									print $front_filters_term->name; ?>
                                </span>
								<?php
							endforeach; ?>
                        </div>
                    </div>
					<?php
				endif;
			endif;
			?>




            <div>
				<?php
				if( $ajax_enabled == 'yes' ): ?>
                    <div class="ajax-post-loader">
                        <img src="<?php print $ajax_loader__url; ?>" class="loader"/>
                    </div>
					<?php
				endif; ?>

                <div id="void_elementor-filter_posts_results">
					<?php
					//##########
					//## Content : Ths content will be replaced by AJAX, if ajax_enabled
					//##########
					print do_shortcode('[voidgrid_sc_post_grid__posts '._get_void_shortcode_atts($atts).' ]'); ?>
                </div>

            </div>

        </div><!-- .site-main -->
    </div><!-- .void-grid -->

	<?php
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode('voidgrid_sc_post_grid', 'voidgrid_sc_post_grid');



function voidgrid_sc_post_grid__posts($atts){

	global $count, $col_desktop, $col_tablet, $col_mobile, $post_count, $wp;
	$count=$post_count='';
	$col_desktop=$col_tablet=$col_mobile = array();

	$atts = _get_void__query_default_atts_merged($atts);
	extract($atts);

	$post_count = $posts;


	//Filters by terms
	if( !empty($taxonomy_type) && !empty($terms_selected) && $terms_selected != 'reset_all' ){

		//tax_query
		$tax_query = array(
			array(
				'taxonomy' => $taxonomy_type,
				'field'    => 'slug',
				'terms'    => $terms_selected,
			),
		);

	}

	//ARGS query
    if(empty($paged)) {
	    if ( get_query_var( 'paged' ) ) {
		    $paged = get_query_var( 'paged' );
	    } elseif ( get_query_var( 'page' ) ) { // if is static front page
		    $paged = get_query_var( 'page' );
	    } else {
		    $paged = 1;
	    }
    }

	$args = array(
		'post_type'      => $post_type,
		'post_status'    => 'publish',
		'posts_per_page' => $posts,
		'paged'          => $paged,
		'tax_query'      => $tax_query,
		'orderby'        => $orderby,
		'order'          => $order,   //ASC / DESC
		'ignore_sticky_posts' => $sticky_ignore,
		'void_grid_query' => 'yes',
		'void_set_offset' => $offset,
	);
	//!ARGS query


	$templates = new Void_Template_Loader;
	$grid_query = new WP_Query( $args );

	if ( $grid_query->have_posts() ) :
		$count = 0;

		$col_desktop = _get_void__columns_width_no($posts_per_row);
		$col_tablet = _get_void__columns_width_no($posts_per_row_tablet);
		$col_mobile = _get_void__columns_width_no($posts_per_row_mobile);

		/* Start the Loop */
		while ( $grid_query->have_posts() ) : $grid_query->the_post();  // Start of posts loop found posts
			$count++;
			$templates->get_template_part( 'content', $display_type );
		endwhile; // End of posts loop found posts


		//#############
		//## Pagination
		//#############
		if($pagination_yes == 'yes' && $grid_query->max_num_pages > 1) :

			$big = 999999999; // need an unlikely integer

			$paginate_args = array(
				'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big) ) ) ,
				'format'    => 'page/%#%',
				'current'   => max(1,$paged ),
				'total'     => $grid_query->max_num_pages,
				'show_all'  => false,
				'end_size'  => 1,
				'mid_size'  => 3,
				'prev_next' => true,
				'prev_text' => esc_html__('<') ,
				'next_text' => esc_html__('>') ,
				'type'      => 'plain',
				'add_args'  => false,
			);
			if($ajax_enabled == 'yes') {
				$paginate_args['base'] = home_url(add_query_arg(array(),$wp->request) . '/%_%');
			}

			$paginate_args = apply_filters( 'void_sortcode__paginate_args', $paginate_args );
			?>
            <div id="void_pagination" class="col-md-12">
                    <nav class='pagination wp-caption void-grid-nav'>
						<?php print paginate_links($paginate_args); ?>
                    </nav>
            </div>
			<?php
		endif;

	else : //if no posts found

		$templates->get_template_part( 'content', 'none' );

	endif; //end of post loop



}
add_shortcode('voidgrid_sc_post_grid__posts', 'voidgrid_sc_post_grid__posts');

