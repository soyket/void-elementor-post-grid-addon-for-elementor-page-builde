<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package void
 */
global $count, $col_no, $col_width, $post_count;
global $count, $col_desktop, $col_tablet, $col_mobile, $post_count;


//ROW : first post
if($count == 1): ?>
    <div class="row">
<?php
endif;?>
	<article class="col-md-<?php echo esc_attr( $col_desktop['width'] );?> col-sm-<?php echo esc_attr( $col_tablet['width'] );?> col-xs-<?php echo esc_attr( $col_mobile['width'] );?>">
		<header class="entry-header">
		<?php
			if( has_post_thumbnail()) : ?>
			<div class="post-img">
				<a href="<?php echo esc_url( get_permalink() ); ?>">
				<?php
					the_post_thumbnail('full',array(
							'class' => 'img-responsive',
							'alt'	=> get_the_title( get_post_thumbnail_id() )
							)
					);
				?>
				</a>
			</div><!--.post-img-->
			<?php endif; ?>

			<div class="post-info">
				<?php
					the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
				?>

				<?php
					if ( 'post' === get_post_type() ) : ?>
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
			</div><!--.post-info-->

		</header><!-- .entry-header -->
	</article><!--.col-md-?-->

	<?php
    if( $count%$col_desktop['no'] == 0 ) :
        if($post_count == $count): //ROW : last post?>
            </div>
        <?php
        else: //ROW : intermediate post ?>
		    </div><div class="row">
	<?php
        endif;
    endif;