<?php  
// voidgrid Shortcodes 

$col_no=$count=$col_width=$post_count='';
global $image_size;
function voidgrid_sc_post_grid( $atts ) {
    extract( shortcode_atts( array (
         'post_type'  => 'post',
         'filter_thumbnail' => 0,
         'taxonomy_type'    => '',
         'cat_exclude'      => '', // actually include or exclude both
         'terms'            => '', 
         'display_type'     => '1',   
         'posts'            => -1,
         'posts_per_row'    =>  2,         
         'image_style'      => '1',
         'orderby'          => 'date',
         'order'            => 'DESC', 
         'offset'           =>  0,  
         'sticky_ignore'    =>  0,
         'display_type'     => 'grid',
         'pagination_yes'   =>  1,
		     'image_size'       =>  ''
    ), $atts ));

   
  set_transient('void_grid_image_size', $image_size, '60' );
  global $col_no,$count,$col_width;
  
  $count = 0;         
  if ($posts_per_row==1)  { $col_width = 12; $col_no = 1; }
  else if ($posts_per_row==2){ $col_width = 6; $col_no = 2; }
  else if ($posts_per_row==3){ $col_width = 4; $col_no = 3; }
  else if ($posts_per_row==4){ $col_width = 3; $col_no = 4; }
  else if ($posts_per_row==6){ $col_width = 2; $col_no = 6; }
  else{ $col_width = 12; $col_no = 1; }
   
    if( $display_type == '1' ){
      $display_type = 'grid';
    }
    elseif( $display_type == '2'){
      $display_type = 'list';
    }
    elseif( $display_type == '3'){
      $display_type = 'first-post-grid';
    }
    elseif( $display_type == '4'){
      $display_type = 'first-post-list';
    }
    elseif( $display_type == '5'){
      $display_type = 'minimal';
    }
    
    if( !empty( $image_style ) ){
      if( $image_style == 1){
        $image_style = '';
      }
      elseif( $image_style == 2){
        $image_style = 'top-left';
      }
      else{
        $image_style = 'top-right';
      }
    }
    else{
      $image_style = '';
    }         
  if( !empty( $taxonomy_type ) ){
    $tax_query = array(                        
                    array(
                            'taxonomy' => $taxonomy_type,
                            'field'    => 'term_id',
                            'terms'    => explode( ',', $terms ),
                          ),
                    );
  }
  else{
    $tax_query = '';
  }

  if($filter_thumbnail){
    $void_image_condition = array(
      'meta_query' => array( 
          array(
              'key' => '_thumbnail_id',
              'compare' => $filter_thumbnail,
            ) 
        )
    );
  }else{
    $void_image_condition='';
  }


  $templates = new Void_Template_Loader;
  

  $grid_query= null;

if ( get_query_var('paged') ) {
    $paged = get_query_var('paged');
} elseif ( get_query_var('page') ) { // if is static front page
    $paged = get_query_var('page');
} else {
    $paged = 1;
}


    $args = array(
       'post_type'      => $post_type,
       'meta_query'     => $void_image_condition,
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
  );

$grid_query = new WP_Query( $args );

global $post_count;
$post_count = $posts;
ob_start();  ?>

<div class="content-area void-grid">
  <div class="site-main <?php echo esc_html( $display_type . ' '. $image_style); ?>" >       
    <div class="row">       
      <?php
      if ( $grid_query->have_posts() ) : 

            /* Start the Loop */
        while ( $grid_query->have_posts() ) : $grid_query->the_post();  // Start of posts loop found posts
          
          $count++;
          $templates->get_template_part( 'content', $display_type );

        endwhile; // End of posts loop found posts

        if($pagination_yes==1) :  //Start of pagination condition 
            global $wp_query;
            $big = 999999999; // need an unlikely integer
            $totalpages = $grid_query->max_num_pages;
            $current = max(1,$paged );
            $paginate_args = array(
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
                              );

            $pagination = paginate_links($paginate_args); ?>
            <div class="col-md-12">
              <nav class='pagination wp-caption void-grid-nav'> 
               <?php echo $pagination; ?>
              </nav>
            </div>
        <?php endif; //end of pagination condition ?>


      <?php else :   //if no posts found

          $templates->get_template_part( 'content', 'none' );

      endif; //end of post loop ?>  

    </div><!-- #main -->
  </div><!-- #primary -->
</div>

<?php
wp_reset_postdata();
return ob_get_clean();
}

add_shortcode('voidgrid_sc_post_grid', 'voidgrid_sc_post_grid');

