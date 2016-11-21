<?php
/**
 * The sidebar containing the home footer widget area just above the regular footer.
 *
 * If no active widgets in sidebar, let's hide it completely.
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since Book Your Travel 1.0
 */
if ( is_active_sidebar( 'home-footer' ) ) : ?>
	<section id="home-footer-sidebar" class="home-footer-sidebar widget-area">
		<ul>
		<?php dynamic_sidebar( 'home-footer' ); ?>
		</ul>
	</section><!-- #secondary -->
<?php endif;