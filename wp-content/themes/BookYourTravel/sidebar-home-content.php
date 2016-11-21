<?php
/**
 * The sidebar containing the home content widget area.
 *
 * If no active widgets in sidebar, let's hide it completely.
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since Book Your Travel 1.0
 */
if ( is_active_sidebar( 'home-content' ) ) { ?>
	<section class="home-content-sidebar">
		<ul>
		<?php dynamic_sidebar( 'home-content' ); ?>
		</ul>
	</section><!-- #secondary -->
<?php } else { ?>
	<section class="home-content-sidebar">
		<?php 
		global $bookyourtravel_theme_globals;
		echo '<ul>';

		$widget_args = array( 'before_widget' => '<li class="widget widget-sidebar">', 'after_widget'  => '</li>', 'before_title'  => '<h2>', 'after_title'   => '</h2>' );
		the_widget('bookyourtravel_search_widget', null, $widget_args); 

		$widget_args = array( 'before_widget' => '<li class="widget widget-sidebar">', 'after_widget'  => '</li>', 'before_title'  => '<h2>', 'after_title'   => '</h2>' );
		the_widget('bookyourtravel_post_list_widget', null, $widget_args); 

		if ($bookyourtravel_theme_globals->enable_accommodations()) {
			$widget_args = array( 'before_widget' => '<li class="widget widget-sidebar">', 'after_widget'  => '</li>', 'before_title'  => '<h2>', 'after_title'   => '</h2>' );
			the_widget('bookyourtravel_accommodation_list_widget', null, $widget_args); 
		}
		if ($bookyourtravel_theme_globals->enable_tours()) {
			$widget_args = array( 'before_widget' => '<li class="widget widget-sidebar">', 'after_widget'  => '</li>', 'before_title'  => '<h2>', 'after_title'   => '</h2>' );
			the_widget('bookyourtravel_tour_list_widget', null, $widget_args); 
		}
		if ($bookyourtravel_theme_globals->enable_cruises()) {
			$widget_args = array( 'before_widget' => '<li class="widget widget-sidebar">', 'after_widget'  => '</li>', 'before_title'  => '<h2>', 'after_title'   => '</h2>' );
			the_widget('bookyourtravel_cruise_list_widget', null, $widget_args); 
		}
		if ($bookyourtravel_theme_globals->enable_car_rentals()) {
			$widget_args = array( 'before_widget' => '<li class="widget widget-sidebar">', 'after_widget'  => '</li>', 'before_title'  => '<h2>', 'after_title'   => '</h2>' );
			the_widget('bookyourtravel_car_rental_list_widget', null, $widget_args); 
		}

		$widget_args = array( 'before_widget' => '<li class="widget widget-sidebar">', 'after_widget'  => '</li>', 'before_title'  => '<h2>', 'after_title'   => '</h2>' );
		the_widget('bookyourtravel_location_list_widget', null, $widget_args); 
		echo '</ul>';
		?>
	</section>
<?php } 