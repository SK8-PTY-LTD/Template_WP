<?php
/**
 * The sidebar containing the main widget area.
 *
 * If no active widgets in sidebar, let's hide it completely.
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since Book Your Travel 1.0
 */
if ( is_active_sidebar( 'sidebar' ) ) : ?>
	<aside id="secondary" class="widget-area  lower" role="complementary">
		<ul>
		<?php dynamic_sidebar( 'sidebar' ); ?>
		</ul>
	</aside><!-- #secondary -->
<?php endif;