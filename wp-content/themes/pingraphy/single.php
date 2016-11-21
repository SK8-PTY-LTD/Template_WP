<?php
/**
 * The template for displaying all single posts.
 *
 * @package Pingraphy
 */

get_header(); ?>
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php while ( have_posts() ) : the_post(); ?>
			<?php get_template_part( 'template-parts/content', 'single' ); ?>
		</main><!-- #main -->

		<?php if ( get_the_author_meta( 'description' ) ): ?>
			<div class="author-bio">
				<div class="bio-avatar"><?php echo get_avatar(get_the_author_meta('user_email'),'128'); ?></div>
				<div class="author-bio-desc">
					<p class="bio-name"><?php esc_html_e('Written by', 'pingraphy'); ?> <?php the_author_posts_link(); ?></p>
					<p class="bio-desc"><?php the_author_meta('description'); ?></p>
					<div class="clear"></div>
				</div>
			</div>
		<?php endif; ?>

		<div class="related-posts clearfix">
				<?php pingraphy_related_posts() ?>
		</div>
		

		<?php pingraphy_the_post_navigation(); ?>

		<?php
			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;
			
		?>
		
		<?php endwhile; // End of the loop. ?>
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
