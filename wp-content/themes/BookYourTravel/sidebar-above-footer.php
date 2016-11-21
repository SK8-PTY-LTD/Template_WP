<?php
/**
 * The sidebar containing the above the footer widget area.
 *
 * If no active widgets in sidebar, let's hide it completely.
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since Book Your Travel 1.0
 */
if ( is_active_sidebar( 'above-footer' ) ) : ?>
	<div id="above-footer-sidebar" class="above-footer-sidebar widget-area" role="complementary">
		<ul>
		<?php dynamic_sidebar( 'above-footer' ); ?>
		</ul>
	</div><!-- #secondary -->
<?php endif;