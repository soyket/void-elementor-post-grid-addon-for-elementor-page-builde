<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package void
 */
global $count, $col_no, $col_width, $post_count, $display_type;
?>
<div class="void-col-md-<?php echo esc_attr( $col_width );?>">
    <div class="void-post-grid void-<?php echo esc_attr($display_type)?>">
		<?php if( has_post_thumbnail()) : ?>
			<div class="post-img">
				<a href="<?php echo esc_url( get_permalink() ); ?>">
				<?php
					if( get_transient('void_grid_image_size') ){
						$grid_image_size = get_transient('void_grid_image_size');
					}else{
						$grid_image_size = 'full';
					}
					the_post_thumbnail( $grid_image_size, array(
							'class' => 'img-responsive',
							'alt'	=> get_the_title( get_post_thumbnail_id() )
							)
					);
				?>
				</a>
			</div><!--.post-img-->
		<?php endif; ?>

        <!--.post-img-->
        <div class="post-info">
			<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' ); ?>
			<?php if ( 'post' === get_post_type() ) : ?>
				<div class="entry-meta">
					<?php 
						void_posted_on();
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