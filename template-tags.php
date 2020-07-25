<?php
/**
 * template tags for voidgrid plugins
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package void
 */

if ( ! function_exists( 'void_posted_on' ) ) :

function void_posted_on() {
	$time_string_posted = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
	$time_string_updated = '<time class="entry-date updated-at" datetime="%1$s">%2$s</time>';
	$time_string_posted = sprintf( $time_string_posted,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() )
	);
	$time_string_updated = sprintf( $time_string_updated,
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	$posted_on = sprintf(
		esc_html_x( '%s', 'post date', 'void' ),
		'<a href="' . esc_url( get_permalink() ) . '"><i class="fas fa-edit"></i> ' . $time_string_posted . '</a>'
	);

	$updated_on = sprintf(
		esc_html_x( '%s', 'post date', 'void' ),
		'<a href="' . esc_url( get_permalink() ) . '"><i class="fas fa-paper-plane"></i> ' . $time_string_updated . '</a>'
	);

	$byline = sprintf(
		esc_html_x( '%s', 'post author', 'void'),
		'<span class="author vcard"><a href="'.esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ).'"><i class="fas fa-user"></i>'.esc_html( get_the_author() ).'</a></span>'
	);

	echo '<span class="posted-on">' . $posted_on . '</span>' . '<span class="updated-on">' . $updated_on . '</span><span class="byline"> ' . $byline . '</span>'; // WPCS: XSS OK.
}
endif;

if ( ! function_exists( 'void_entry_header' ) ) :

function void_entry_header() {
	// Hide category and tag text for pages.
	if ( 'post' === get_post_type() ) {
		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( esc_html__( ', ', 'void' ) );
		if ( $categories_list && void_categorized_blog() ) {
			printf( '<span class="cat-links">' . esc_html__( ' %1$s ', 'void' ) . '</span>', $categories_list ); // WPCS: XSS OK.
		}

		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', esc_html__( ', ', 'void' ) );
		if ( $tags_list ) {
			printf( '<span class="tags-links">' . esc_html__( ' %1$s', 'void' ) . '</span>', $tags_list ); // WPCS: XSS OK.
		}
	}

	if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<span class="comments-link">';
		/* translators: %s: post title */
		comments_popup_link( sprintf( wp_kses( __( 'Leave a Comment<span class="void-screen-reader-text"> on %s</span>', 'void' ), array( 'span' => array( 'class' => array() ) ) ), get_the_title() ) );
		echo '</span>';
	}
	
}
endif;

function void_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'void_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,
			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'void_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so void_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so void_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in void_categorized_blog.
 */
function void_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'void_categories' );
}
add_action( 'edit_category', 'void_category_transient_flusher' );
add_action( 'save_post',     'void_category_transient_flusher' );


// Helper Function for pagination void_post_grid

function void_grid_set_offset( $query ) {
  if( $query->get( 'void_grid_query' ) == 'yes' && !$query->is_main_query() ){
       //$query->set( 'offset', $query->get( 'void_set_offset' ) );
      $offset = $query->get( 'void_set_offset' );

      //Next, determine how many posts per page you want (we'll use WordPress's settings)
      $post_per_page = $query->get( 'posts_per_page' );

      //Next, detect and handle pagination...
      if ( $query->is_paged ) {

        //Manually determine page query offset (offset + current page (minus one) x posts per page)
        $page_offset = $offset + ( ($query->query_vars['paged']-1) * $post_per_page );

        //Apply adjust page offset
        $query->set('offset', $page_offset );

      }
      else {

        //This is the first page. Just use the offset...
        $query->set('offset',$offset);

      }
  }
}
add_action( 'pre_get_posts', 'void_grid_set_offset' );

function void_grid_reset_page_number($found_posts, $query) {
  if( $query->get( 'void_grid_query' ) == 'yes' && !$query->is_main_query() ){

	$offset = $query->get( 'void_set_offset' );

	return $found_posts - $offset;
     
  }
  return $found_posts;
}
add_filter('found_posts', 'void_grid_reset_page_number', 1, 2 );
