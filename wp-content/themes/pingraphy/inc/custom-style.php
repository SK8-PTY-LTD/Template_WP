<?php 

if ( ! function_exists( 'pingraphy_custom_style' ) ) :

/**
|------------------------------------------------------------------------------
| Generate custom style from theme option
|------------------------------------------------------------------------------
 */

function pingraphy_custom_style() {
	?>
	<style type="text/css">
		a,
		.widget ul li a {
			color: <?php echo esc_html (get_theme_mod('pingraphy_anchor_text_color')) ?>;
		}
		a:hover,
		#breadcrumb a:hover,
		.widget ul li a:hover,
		.widget_calendar #calendar_wrap caption {
			color: <?php echo esc_html (get_theme_mod('pingraphy_anchor_text_color_hover')); ?>;
		}
		.widget_calendar #calendar_wrap table td a {
			background: <?php echo esc_html (get_theme_mod('pingraphy_anchor_text_color_hover')); ?>;
		}

		/* Header Color  */
		.sticky-nav,
		.site-header {
			background: <?php echo esc_html (get_theme_mod('pingraphy_header_color')); ?>;
		}
		.site-header .section-one .toggle-mobile-menu,
		.search-style-one a i {
			color: <?php echo esc_html (get_theme_mod('pingraphy_header_icon_color')); ?>;
		}
		.site-header .site-title a,
		.site-header .site-description {
			color: <?php echo esc_html (get_theme_mod('pingraphy_header_logo_text_color')); ?>;
		}

	</style>
	<?php
	}

add_action('wp_head','pingraphy_custom_style');
endif;