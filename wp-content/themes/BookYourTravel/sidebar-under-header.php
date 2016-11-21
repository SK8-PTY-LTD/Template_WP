<?php
/**
 * The sidebar containing the under the header widget area.
 *
 * If no active widgets in sidebar, let's hide it completely.
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since Book Your Travel 1.0
 */
if ( is_active_sidebar( 'under-header' ) ) : ?>
	<div id="under-header-sidebar" class="under-header-sidebar widget-area" role="complementary">
		<ul>
		<?php dynamic_sidebar( 'under-header' ); ?>
		</ul>
	</div><!-- #secondary -->
<?php endif;