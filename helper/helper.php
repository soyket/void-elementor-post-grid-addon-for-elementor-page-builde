<?php

function voidgrid_get_flat_icons() {
	return [
		'fi flaticon-contact'=>'<span class="fi flaticon-contact"></span>Contact',
		'fi flaticon-double-angle-pointing-to-right'=>'Double Angle Pointing To Right',
		'fi flaticon-e-mail-envelope'=>'E Mail Envelope',
		'fi flaticon-envelope'=>'Envelope',
		'fi flaticon-fast-forward-double-right-arrows'=>'Fast Forward Double Right Arrows',
		'fi flaticon-fence'=>'Fence',
		'fi flaticon-info'=>'Info',
		'fi flaticon-lawn-mower'=>'Lawn Mower',
		'fi flaticon-location'=>'Location',
		'fi flaticon-log'=>'Log',
		'fi flaticon-mail'=>'Mail',
		'fi flaticon-people'=>'People',
		'fi flaticon-people-1'=>'People 1',
		'fi flaticon-portfolio-black-symbol'=>'Portfolio Black Symbol',
		'fi flaticon-question'=>'Question',
		'fi flaticon-right-arrows-couple'=>'Right Arrows Couple',
		'fi flaticon-sprout'=>'Sprout',
		'fi flaticon-watering-can'=>'Watering Can'
	];
}

function voidgrid_post_orderby_options(){
	$orderby = array(
		'ID' => 'Post Id',
		'author' => 'Post Author',
		'title' => 'Title',
		'date' => 'Date',
		'modified' => 'Last Modified Date',
		'parent' => 'Parent Id',
		'rand' => 'Random',
		'comment_count' => 'Comment Count',
		'menu_order' => 'Menu Order',
	);

	return $orderby;
}


function void_grid_post_type(){
	$args= array(
		'public'	=> 'true',
		'_builtin'	=> false
	);
	$post_types = get_post_types( $args, 'names', 'and' );
	$post_types = array( 'post'	=> 'post' ) + $post_types;
	return $post_types;
}

/**
 * @param null $_query
 *
 * @return string
 */
function _get_void_shortcode_atts($array_data, $write_key = '1', $prefix = '' ){
	$return = '';
	$suffix_seperator = '__';
	$excluded_values = array('\"', '"');

	foreach ($array_data as $key=>$value){


		if ( ! is_int( $key ) ) {
			//key is NOT an Integer
			if ( is_int( $value ) || is_string( $value ) ) {
				//value is String
				if( !in_array($value, $excluded_values) && !empty( trim( $value ) ) ) {
					switch ( $write_key ) {
						case '1':
							$return .= $key . '="' . $value . '" ';
							break;
						case '0':
							$return .= $value . ', ';
							break;
						case 'sub_element':
							$return .= $prefix . $key . '="' . $value . '" ';
							break;
					}
				}
			}
			//Array (recursive call)
			if ( is_array( $value ) ) {
				$return .= _get_void_shortcode_atts( $value, 'sub_element', $key . $suffix_seperator ) . '" ';
			}
		} else {
			//key is integer
			if ( is_int( $value ) || is_string( $value ) ) {
				if( !in_array($value, $excluded_values) && !empty( trim( $value ) ) ) {
					switch ( $write_key ) {
						case '1':
						case 'sub_element':
							$return .= rtrim( $prefix, $suffix_seperator ) . '="' . $value . '" ';
							break;
						case '0':
							$return .= $value . ', ';
							break;
					}
				}
			}
			//Array (recursive call)
			if ( is_array( $value ) ) {
				$return .= _get_void_shortcode_atts( $value, 'sub_element', rtrim( $prefix, $suffix_seperator ) . '__' ) . '" ';
			}
		}

	}
	return rtrim($return, ',');
}

function _get_void__columns_width_no($posts_per_row){ //Bootstrap columnage
	if(! (12 % $posts_per_row) ) {
		$col_width = 12 / $posts_per_row;
		return array( 'width' => $col_width, 'no' => 12 / $col_width );
	}
	else{
		return array( 'width' => 12, 'no' => 1  );
	}

}

function _get_void__query_default_atts_merged($atts){

	$default_atts = array (
		'post_type'  => 'post',
		'taxonomy_type'  => '',
		'terms'          => '',
		'terms_front_filters_yes' => 'no',
		'terms_front_filters_exclusive' => 'unique',
		'posts' => -1,
		'posts_per_row' => 2,
		'image_style'  => 'standard',
		'orderby'          => 'date',
		'order'            => 'DESC',
		'offset'           => 0,
		'sticky_ignore'    => 0,
		'display_type'       => 'grid',
		'pagination_yes'     => 'yes',
		'ajax_loader__url'     => '',
		'ajax_enabled'     => 'yes',
	);


	//TODO: tester le cas sans aucun choix de taxonomy, ni terms
	//TODO: tester le cas avec une taxonomy et avec des termes

	$atts['tax_query'] = '';
	if( !empty($atts['taxonomy_type']) && !empty(!empty($atts['terms'])) ){
		/*
		if(empty($terms)){ //no specific terms defined in Back-Office widget, so get all of the choosed taxonomy
			$terms = get_terms( [
				'taxonomy'   => $taxonomy_type,
				'hide_empty' => TRUE,
			] );
		}

		foreach ( $terms as $term ){
			$terms_slug[] = $term -> slug;
		}
		*/
		//tax_query
		$atts['tax_query'] = array(
			array(
				'taxonomy' => $atts['taxonomy_type'],
				'field'    => 'slug',
				'terms'    => implode( ', ', $terms_slug ),
			),
		);

	}

	//Merge
	if(is_array($atts)){
		$atts = array_merge($default_atts, $atts);
	}
	else {
		$atts = shortcode_atts( $default_atts, $atts );
	}

	return $atts;
}

function _get_void__query_content_args($params){

	return '';

	return array(
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
}
