<?php
/**
 * Template part for displaying posts.
 *
 * @package Pingraphy
 */

$excerpt_length = 30;

?>
<article id="post-<?php the_ID(); ?>" <?php post_class('item has-post-thumbnail'); ?>>
	<div class="item-text">
		<div class="item-description">
			<div class="entry-content">
				<a class="quote-url" href="<?php the_permalink() ?>"><?php the_excerpt(); ?></a>
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
		<?php pingraphy_footer_post_meta(); ?>
		<?php edit_post_link('<i class="fa fa-pencil-square-o"></i>', '<span class="edit-link">', '</span>' ); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
