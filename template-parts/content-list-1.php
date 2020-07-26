<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package void
 */
global $col_width, $display_type, $is_filter;
global $post;

$taxonomies = get_post_taxonomies($post);
$terms_id = '[';
foreach($taxonomies as $t_k => $t_v){
	$terms = get_the_terms( $post->ID , $t_v );
	foreach($terms as $term){
		$terms_id .= '"'.$term->term_id.'",';
	}
}
$terms_id = rtrim($terms_id, ',');
$terms_id .= ']';

?>
?>
<div class="void-col-md-12">
	<div class="void-post-grid void-list withExcerpt <?php echo esc_attr( $display_type); ?>">
		<div class="post-img">
			<a href="<?php echo esc_url( get_permalink() ); ?>">
				<?php
					if( get_transient('void_grid_image_size') ){
						$grid_image_size = get_transient('void_grid_image_size');
					}else{
						$grid_image_size = 'blog-list-post-size';
					}
					the_post_thumbnail( $grid_image_size, array(
							'class' => 'img-responsive',
							'alt'	=> get_the_title( get_post_thumbnail_id() )
							)
					);
				?>
			</a>
		</div><!--.post-img-->
		<div class="post-info">
			<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' ); ?>
			<?php if ( 'post' === get_post_type() ) : ?>
				<div class="entry-meta">
					<?php 
						void_entry_header();
					?>
				</div><!-- .entry-meta -->
				<div class="blog-excerpt">
					<?php the_excerpt(); ?>
				</div><!--.blog-excerpt-->				
			<?php endif; ?>
		</div>
		<!--.post-info-->
	</div>
</div>