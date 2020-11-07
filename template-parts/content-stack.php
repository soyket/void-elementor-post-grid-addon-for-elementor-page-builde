<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package publishify
 */
global $blog_style;

?>
	

	<header class="entry-header publishify-overlay <?php echo esc_attr($blog_style.' stack-bg-'.get_the_id()); ?>">		
		<div class="post-info">
		<?php
				if ( 'post' === get_post_type() ) : ?>
					<div class="entry-meta">
						<?php						
							publishify_entry_categories();
						?>
					</div><!-- .entry-meta -->					
				<?php endif;					
				the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );				
				publishify_post_icons(); ?>
				<div class="publishify-comments">
					<?php publishify_entry_comments(); ?>
				</div>				
		</div><!--.post-info-->			
	</header><!-- .entry-header -->
<div class="clearfix"></div>

