<?php
/**
 * The sidebar containing the header widget area (mainly used by WPML)
 *
 * If no active widgets in sidebar, let's hide it completely.
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since Book Your Travel 1.0
 */
if ( is_active_sidebar( 'header' ) ) : ?>
	<?php dynamic_sidebar( 'header' ); ?>
<?php endif;