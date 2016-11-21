<?php
/**
 * Template part for displaying posts.
 *
 * @package Pingraphy
 */

$excerpt_length = 30;

?>
<article id="post-<?php the_ID(); ?>" <?php post_class('item has-post-thumbnail'); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="thumbnail">
			<a href="<?php the_permalink() ?>" rel="bookmark"><?php the_post_thumbnail('pingraphy-home-thumbnail'); ?></a>
		</div>
	<?php endif; ?>
	<div class="item-text">
		<header class="entry-header">
			<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

			
		</header><!-- .entry-header -->
		<div class="item-description">
			<div class="entry-content">
				<?php 
					the_excerpt();
				?>
				<?php
					wp_link_pages( array(
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'pingraphy' ),
						'after'  => '</div>',
					) );
				?>
			</div><!-- .entry-content -->
		</div>
	</div>
	<footer class="entry-footer clearfix">
		
		<?php edit_post_link('<i class="fa fa-pencil-square-o"></i>', '<span class="edit-link">', '</span>' ); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
