<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Pingraphy
 */

?>
		</div>
	</div><!-- #content -->

	<footer id="colophon" class="site-footer" role="contentinfo">
		<?php get_sidebar('footer'); ?>
		<div class="site-info">
			<div class="inner clearfix">
				
				<?php pingraphy_footer_copyright(); ?>
				
				<?php if( has_nav_menu('footer'))  : ?>
				<div class="menu-footer">
					<?php wp_nav_menu( array( 'theme_location' => 'footer', 'menu_class' => 'menu clearfix' ) ); ?>
				</div>
				<?php endif; ?>
				
			</div>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->
<!-- Back To Top -->
<span class="back-to-top"><i class="fa fa-angle-double-up"></i></span>
<?php wp_footer(); ?>
</body>
</html>