<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Pingraphy
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'pingraphy' ); ?></a>

	<header id="masthead" class="site-header" role="banner">
		<div class="clearfix">
			<div class="section-one">
				<div class="inner">
					<?php if( has_nav_menu('primary'))  : ?>
					<a class="toggle-mobile-menu" href="#" title="Menu"><i class="fa fa-bars"></i></a>
					<nav id="primary-navigation" class="main-navigation" role="navigation">
						<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_id' => 'primary-menu', 'menu_class' => 'menu clearfix' ) ); ?>
					</nav><!-- #site-navigation -->
					<?php endif; ?>
					<div class="site-branding">
						<?php pingraphy_header_title() ?>
					</div><!-- .site-branding -->

					<div class="search-style-one">
						<a id="trigger-overlay">
							<i class="fa fa-search"></i>
						</a>
						<div class="overlay overlay-slideleft">
							<div class="search-row">
								<form method="get" id="searchform" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" _lpchecked="1">
									<a ahref="#" class="overlay-close"><i class="fa fa-times"></i></a>
									<input type="text" name="s" id="s" value="<?php echo get_search_query(); ?>" placeholder="<?php esc_html_e('Search Keyword ...', 'pingraphy'); ?>" />
								</form>
							</div>
						</div>
					</div>
					
				</div>
			</div>
			<div class="section-two">
				<?php if( has_nav_menu('secondary'))  : ?>
				<div class="inner clearfix">
					
					<a class="mobile-only toggle-mobile-menu" href="#" title="Menu"><?php _e('Main Navigation', 'pingraphy'); ?> <i class="fa fa-angle-down"></i></a>
					<nav id="secondary-navigation" class="second-navigation" role="navigation">
						<?php wp_nav_menu( array( 'theme_location' => 'secondary', 'menu_id' => 'secondary-menu', 'menu_class' => 'menu clearfix' ) ); ?>
					</nav><!-- #site-navigation -->
                	
				</div>
				<?php endif; ?>
			</div>
		</div>
		<div id="catcher"></div>
	</header><!-- #masthead -->
	<?php 
		
		$class = ' sidebar-right';

		// for page condition
		if (is_page()) {
			if ( is_page_template( 'sidebar-left.php' )) {
				$class = ' sidebar-left';
			} else if ( is_page_template( 'full-width.php' )) {
				$class = ' full-width';
			} else {
				$class = ' sidebar-right';
			}
		}
	?>

	<div id="content" class="site-content<?php echo $class; ?>">
		<div class="inner clearfix">