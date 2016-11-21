<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package Pingraphy
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="content-wrap">
		<header class="entry-header">
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
		</header><!-- .entry-header -->

		<div class="entry-content">
			<?php the_content(); ?>
			<?php
				wp_link_pages( array(
					'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'pingraphy' ),
					'after'  => '</div>',
				) );
			?>
		</div><!-- .entry-content -->
	</div>

	<?php if ( is_user_logged_in() ) : ?>
	<footer class="entry-footer clearfix">
		<?php edit_post_link('<i class="fa fa-pencil-square-o"></i>', '<span class="edit-link">', '</span>' ); ?>
	</footer><!-- .entry-footer -->
	<?php endif; ?>
</article><!-- #post-## -->

