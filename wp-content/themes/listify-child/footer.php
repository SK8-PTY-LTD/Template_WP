<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Listify
 */
?>

	</div><!-- #content -->

</div><!-- #page -->

<div class="footer-wrapper">

	<?php if ( ! listify_is_job_manager_archive() ) : ?>

		<?php get_template_part( 'content', 'aso' ); ?>

		<?php if ( is_active_sidebar( 'widget-area-footer-1' ) || is_active_sidebar( 'widget-area-footer-2' ) || is_active_sidebar( 'widget-area-footer-3' ) ) : ?>

			<footer class="site-footer-widgets">
				<div class="container">
					<div class="row">
						<div class="footer-widget-column col-xs-12 col-sm-6 col-lg-3">
							<?php dynamic_sidebar( 'widget-area-footer-1' ); ?>
						</div>

						<div class="footer-widget-column col-xs-12 col-sm-6 col-lg-3">
							<?php dynamic_sidebar( 'widget-area-footer-2' ); ?>
						</div>

						<div class="footer-widget-column col-xs-12 col-sm-6 col-lg-3">
							<?php dynamic_sidebar( 'widget-area-footer-3' ); ?>
						</div>
						<div class="footer-widget-column col-xs-12 col-sm-6 col-lg-3">
							<a href="#"><img border="0" alt="WeChat" src="http://freshjourney.com.au/social-icons/wc.svg" width="50" height="50"></a>
							<a href="#"><img border="0" alt="Facebook" src="http://freshjourney.com.au/social-icons/fb.svg" width="50" height="50"></a>
							<a href="#"><img border="0" alt="Instagram" src="http://freshjourney.com.au/social-icons/ig.svg" width="50" height="50"></a>
						</div>
					</div>
				</div>
			</footer>

		<?php endif; ?>

	<?php endif; ?>


</div>

<div id="ajax-response"></div>

<?php wp_footer(); ?>

</body>
</html>
