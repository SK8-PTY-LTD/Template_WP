<?php
/**
 * The sidebar containing the user account widget area.
 *
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since Book Your Travel 1.0
 */

global $bookyourtravel_theme_globals, $frontend_submit, $post;

$enable_reviews = $bookyourtravel_theme_globals->enable_reviews();
$enable_accommodations = $bookyourtravel_theme_globals->enable_accommodations();
$enable_car_rentals = $bookyourtravel_theme_globals->enable_car_rentals();
$enable_tours = $bookyourtravel_theme_globals->enable_tours();
$enable_cruises = $bookyourtravel_theme_globals->enable_cruises();
$current_url = get_permalink( $page_id );

$page_id = $post->ID;
$page_custom_fields = get_post_custom( $page_id);

$is_partner_page = false;
if (isset($page_custom_fields['user_account_is_partner_page'])) {
	$is_partner_page = $page_custom_fields['user_account_is_partner_page'][0] == '1' ? true : false;
} else {
	$template_file = get_post_meta($post->ID,'_wp_page_template',true);
	if ($template_file == 'page-user-content-list.php' || $template_file == 'page-user-submit-content.php') {
		$is_partner_page = true;
	}
}
?>
<?php
if ( $is_partner_page && $frontend_submit->user_has_correct_role() && has_nav_menu( 'partner-account-menu' ) ) {
	wp_nav_menu( array( 
		'theme_location' => 'partner-account-menu', 
		'container' => 'nav', 
		'container_class' => 'inner-nav'		
	) ); 
} else if (has_nav_menu( 'user-account-menu' )) {
	wp_nav_menu( array( 
		'theme_location' => 'user-account-menu', 
		'container' => 'nav',
		'container_class' => 'inner-nav'
	) ); 
} else { 

	$my_account_page_url = $bookyourtravel_theme_globals->get_my_account_page_url();
	$submit_room_types_url = $bookyourtravel_theme_globals->get_submit_room_types_url();
	$submit_accommodations_url = $bookyourtravel_theme_globals->get_submit_accommodations_url();
	$submit_accommodation_vacancies_url = $bookyourtravel_theme_globals->get_submit_accommodation_vacancies_url();
	$submit_accommodation_vacancies_url_with_arg = $submit_accommodation_vacancies_url;
	$submit_accommodation_vacancies_url_with_arg = add_query_arg( 'fesid', '', $submit_accommodation_vacancies_url_with_arg);
	$submit_tours_url = $bookyourtravel_theme_globals->get_submit_tours_url();
	$submit_tour_schedules_url = $bookyourtravel_theme_globals->get_submit_tour_schedules_url();
	$submit_tour_schedules_url_with_arg = $submit_tour_schedules_url;
	$submit_tour_schedules_url_with_arg = add_query_arg( 'fesid', '', $submit_tour_schedules_url_with_arg);
	$submit_cruises_url = $bookyourtravel_theme_globals->get_submit_cruises_url();
	$submit_cruise_schedules_url = $bookyourtravel_theme_globals->get_submit_cruise_schedules_url();
	$submit_cruise_schedules_url_with_arg = $submit_cruise_schedules_url;
	$submit_cruise_schedules_url_with_arg = add_query_arg( 'fesid', '', $submit_cruise_schedules_url_with_arg);
	$submit_cabin_types_url = $bookyourtravel_theme_globals->get_submit_cabin_types_url();
	$submit_car_rentals_url = $bookyourtravel_theme_globals->get_submit_car_rentals_url();
	$list_user_room_types_url = $bookyourtravel_theme_globals->get_list_user_room_types_url();
	$list_user_accommodations_url = $bookyourtravel_theme_globals->get_list_user_accommodations_url();
	$list_user_accommodation_vacancies_url = $bookyourtravel_theme_globals->get_list_user_accommodation_vacancies_url();
	$list_user_accommodation_vacancies_url_with_arg = $list_user_accommodation_vacancies_url;
	$list_user_accommodation_vacancies_url_with_arg = add_query_arg( 'accid', '', $list_user_accommodation_vacancies_url_with_arg);
	$list_user_accommodation_bookings_url = $bookyourtravel_theme_globals->get_list_user_accommodation_bookings_url();
	$list_user_accommodation_bookings_url_with_arg = $list_user_accommodation_bookings_url;
	$list_user_accommodation_bookings_url_with_arg = add_query_arg( 'accid', '', $list_user_accommodation_bookings_url_with_arg);
	$list_user_tours_url = $bookyourtravel_theme_globals->get_list_user_tours_url();
	$list_user_tour_schedules_url = $bookyourtravel_theme_globals->get_list_user_tour_schedules_url();
	$list_user_tour_schedules_url_with_arg = $list_user_tour_schedules_url;
	$list_user_tour_schedules_url_with_arg = add_query_arg( 'tourid', '', $list_user_tour_schedules_url_with_arg);
	$list_user_tour_bookings_url = $bookyourtravel_theme_globals->get_list_user_tour_bookings_url();
	$list_user_tour_bookings_url_with_arg = $list_user_tour_bookings_url;
	$list_user_tour_bookings_url_with_arg = add_query_arg( 'tourid', '', $list_user_tour_bookings_url_with_arg);
	$list_user_cruises_url = $bookyourtravel_theme_globals->get_list_user_cruises_url();
	$list_user_cruise_schedules_url = $bookyourtravel_theme_globals->get_list_user_cruise_schedules_url();
	$list_user_cruise_schedules_url_with_arg = $list_user_cruise_schedules_url;
	$list_user_cruise_schedules_url_with_arg = add_query_arg( 'cruiseid', '', $list_user_cruise_schedules_url_with_arg);
	$list_user_cruise_bookings_url = $bookyourtravel_theme_globals->get_list_user_cruise_bookings_url();
	$list_user_cruise_bookings_url_with_arg = $list_user_cruise_bookings_url;
	$list_user_cruise_bookings_url_with_arg = add_query_arg( 'cruiseid', '', $list_user_cruise_bookings_url_with_arg);
	$list_user_cabin_types_url = $bookyourtravel_theme_globals->get_list_user_cabin_types_url();
	$list_user_car_rentals_url = $bookyourtravel_theme_globals->get_list_user_car_rentals_url();
	$list_user_car_rental_bookings_url = $bookyourtravel_theme_globals->get_list_user_car_rental_bookings_url();

	if (empty($my_account_page_url) &&
		empty($submit_room_types_url) &&
		empty($submit_accommodations_url) &&
		empty($submit_accommodation_vacancies_url) &&
		empty($submit_tours_url) &&
		empty($submit_tour_schedules_url) &&	
		empty($submit_cruises_url) &&
		empty($submit_cruise_schedules_url) &&		
		empty($submit_cabin_types_url) &&
		empty($submit_car_rentals_url) &&
		empty($list_user_room_types_url) &&
		empty($list_user_accommodations_url) &&
		empty($list_user_accommodation_vacancies_url) &&
		empty($list_user_accommodation_bookings_url) &&		
		empty($list_user_tours_url) &&
		empty($list_user_tour_schedules_url) &&
		empty($list_user_tour_bookings_url) &&		
		empty($list_user_cruises_url) &&
		empty($list_user_cruise_schedules_url) &&
		empty($list_user_cruise_bookings_url) &&		
		empty($list_user_cabin_types_url) &&
		empty($list_user_car_rentals_url) &&
		empty($list_user_car_rental_bookings_url)) {
		echo "<p class='error'>" . esc_html__('You have not configured Page Settings in Theme Options to get full functionality of this page. Please go to Appearance -> Theme Options -> Page Settings and configure the page options.', 'bookyourtravel') . "</p>";
	}

if ( $is_partner_page && $frontend_submit->user_has_correct_role()) {
?>
	<!--inner navigation-->
	<nav class="inner-nav">
		<ul>
			<?php if (!empty($my_account_page_url)) { ?>
			<li <?php echo $current_url == $my_account_page_url ? 'class="active"' : ''; ?>><a href="<?php echo esc_url($my_account_page_url); ?>" title="<?php esc_attr_e('Settings', 'bookyourtravel'); ?>"><?php esc_html_e('Settings', 'bookyourtravel'); ?></a></li>
			<?php } ?>
			<?php if ($frontend_submit->user_has_correct_role()) { ?>
			<?php 	if ($enable_accommodations) { ?>
			<?php 		if (!empty($list_user_room_types_url)) { ?>
			<li <?php echo $current_url == $list_user_room_types_url ? 'class="active"' : ''; ?>><a href="<?php echo esc_url($list_user_room_types_url); ?>" title="<?php esc_attr_e('My Room Types', 'bookyourtravel'); ?>"><?php esc_html_e('My Room Types', 'bookyourtravel'); ?></a></li>
			<?php 		} ?>
			<?php 		if (!empty($list_user_accommodations_url)) { ?>
			<li <?php echo $current_url == $list_user_accommodations_url ? 'class="active"' : ''; ?>><a href="<?php echo esc_url($list_user_accommodations_url); ?>" title="<?php esc_attr_e('My Accommodations', 'bookyourtravel'); ?>"><?php esc_html_e('My Accommodations', 'bookyourtravel'); ?></a></li>
			<?php 		} ?>
			<?php 		if (!empty($list_user_accommodation_vacancies_url)) { ?>
			<li <?php echo $current_url == $list_user_accommodation_vacancies_url ? 'class="active"' : ''; ?>><a href="<?php echo esc_url($list_user_accommodation_vacancies_url); ?>" title="<?php esc_attr_e('My Accommodation Vacancies', 'bookyourtravel'); ?>"><?php esc_html_e('My Accommodation Vacancies', 'bookyourtravel'); ?></a></li>
			<?php 		} ?>
			<?php 		if (!empty($list_user_accommodation_bookings_url)) { ?>
			<li <?php echo $current_url == $list_user_accommodation_bookings_url ? 'class="active"' : ''; ?>><a href="<?php echo esc_url($list_user_accommodation_bookings_url); ?>" title="<?php esc_attr_e('My Accommodation Bookings', 'bookyourtravel'); ?>"><?php esc_html_e('My Accommodation Bookings', 'bookyourtravel'); ?></a></li>
			<?php 		} ?>			
			<?php 	} ?>				
			<?php 	if ($enable_tours) { ?>				
			<?php 		if (!empty($list_user_tours_url)) { ?>
			<li <?php echo $current_url == $list_user_tours_url ? 'class="active"' : ''; ?>><a href="<?php echo esc_url($list_user_tours_url); ?>" title="<?php esc_attr_e('My Tours', 'bookyourtravel'); ?>"><?php esc_html_e('My Tours', 'bookyourtravel'); ?></a></li>
			<?php 		} ?>
			<?php 		if (!empty($list_user_tour_schedules_url)) { ?>
			<li <?php echo $current_url == $list_user_tour_schedules_url ? 'class="active"' : ''; ?>><a href="<?php echo esc_url($list_user_tour_schedules_url); ?>" title="<?php esc_attr_e('My Tour Schedules', 'bookyourtravel'); ?>"><?php esc_html_e('My Tour Schedules', 'bookyourtravel'); ?></a></li>
			<?php 		} ?>
			<?php 		if (!empty($list_user_tour_bookings_url)) { ?>
			<li <?php echo $current_url == $list_user_tour_bookings_url ? 'class="active"' : ''; ?>><a href="<?php echo esc_url($list_user_tour_bookings_url); ?>" title="<?php esc_attr_e('My Tour Bookings', 'bookyourtravel'); ?>"><?php esc_html_e('My Tour Bookings', 'bookyourtravel'); ?></a></li>
			<?php 		} ?>			
			<?php 	} ?>				
			<?php 	if ($enable_cruises) { ?>								
			<?php 		if (!empty($list_user_cruises_url)) { ?>
			<li <?php echo $current_url == $list_user_cruises_url ? 'class="active"' : ''; ?>><a href="<?php echo esc_url($list_user_cruises_url); ?>" title="<?php esc_attr_e('My Cruises', 'bookyourtravel'); ?>"><?php esc_html_e('My Cruises', 'bookyourtravel'); ?></a></li>
			<?php 		} ?>
			<?php 		if (!empty($list_user_cabin_types_url)) { ?>
			<li <?php echo $current_url == $list_user_cabin_types_url ? 'class="active"' : ''; ?>><a href="<?php echo esc_url($list_user_cabin_types_url); ?>" title="<?php esc_attr_e('My Cabin Types', 'bookyourtravel'); ?>"><?php esc_html_e('My Cabin Types', 'bookyourtravel'); ?></a></li>
			<?php 		} ?>
			<?php 		if (!empty($list_user_cruise_schedules_url)) { ?>
			<li <?php echo $current_url == $list_user_cruise_schedules_url ? 'class="active"' : ''; ?>><a href="<?php echo esc_url($list_user_cruise_schedules_url); ?>" title="<?php esc_attr_e('My Cruise Schedules', 'bookyourtravel'); ?>"><?php esc_html_e('My Cruise Schedules', 'bookyourtravel'); ?></a></li>
			<?php 		} ?>
			<?php 		if (!empty($list_user_cruise_bookings_url)) { ?>
			<li <?php echo $current_url == $list_user_cruise_bookings_url ? 'class="active"' : ''; ?>><a href="<?php echo esc_url($list_user_cruise_bookings_url); ?>" title="<?php esc_attr_e('My Cruise Bookings', 'bookyourtravel'); ?>"><?php esc_html_e('My Cruise Bookings', 'bookyourtravel'); ?></a></li>
			<?php 		} ?>
			<?php 	} ?>				
			<?php 	if ($enable_car_rentals) { ?>	
			<?php 		if (!empty($list_user_car_rentals_url)) { ?>
			<li <?php echo $current_url == $list_user_car_rentals_url ? 'class="active"' : ''; ?>><a href="<?php echo esc_url($list_user_car_rentals_url); ?>" title="<?php esc_attr_e('My Car Rentals', 'bookyourtravel'); ?>"><?php esc_html_e('My Car Rentals', 'bookyourtravel'); ?></a></li>
			<?php 		} ?>
			<?php 		if (!empty($list_user_car_rental_bookings_url)) { ?>
			<li <?php echo $current_url == $list_user_car_rental_bookings_url ? 'class="active"' : ''; ?>><a href="<?php echo esc_url($list_user_car_rental_bookings_url); ?>" title="<?php esc_attr_e('My Car Rental Bookings', 'bookyourtravel'); ?>"><?php esc_html_e('My Car Rental Bookings', 'bookyourtravel'); ?></a></li>
			<?php 		} ?>			
			<?php 	} ?>				
			<?php 	if ($enable_accommodations) { ?>					
			<?php 		if (!empty($submit_room_types_url)) { ?>
			<li <?php echo $current_url == $submit_room_types_url ? 'class="active"' : ''; ?>><a href="<?php echo esc_url($submit_room_types_url); ?>" title="<?php esc_attr_e('Submit Room Type', 'bookyourtravel'); ?>"><?php esc_html_e('Submit Room Types', 'bookyourtravel'); ?></a></li>
			<?php 		} ?>
			<?php 		if (!empty($submit_accommodations_url)) { ?>
			<li <?php echo $current_url == $submit_accommodations_url ? 'class="active"' : ''; ?>><a href="<?php echo esc_url($submit_accommodations_url); ?>" title="<?php esc_attr_e('Submit Accommodation', 'bookyourtravel'); ?>"><?php esc_html_e('Submit Accommodations', 'bookyourtravel'); ?></a></li>
			<?php 		} ?>
			<?php 		if (!empty($submit_accommodation_vacancies_url)) { ?>
			<li <?php echo $current_url == $submit_accommodation_vacancies_url ? 'class="active"' : ''; ?>><a href="<?php echo esc_url($submit_accommodation_vacancies_url); ?>" title="<?php esc_attr_e('Submit Vacancy', 'bookyourtravel'); ?>"><?php esc_html_e('Submit Vacancies', 'bookyourtravel'); ?></a></li>
			<?php 		} ?>
			<?php 	} ?>				
			<?php 	if ($enable_tours) { ?>					
			<?php 		if (!empty($submit_tours_url)) { ?>
			<li <?php echo $current_url == $submit_tours_url ? 'class="active"' : ''; ?>><a href="<?php echo esc_url($submit_tours_url); ?>" title="<?php esc_attr_e('Submit Tour', 'bookyourtravel'); ?>"><?php esc_html_e('Submit Tour', 'bookyourtravel'); ?></a></li>
			<?php 		} ?>
			<?php 		if (!empty($submit_tour_schedules_url)) { ?>
			<li <?php echo $current_url == $submit_tour_schedules_url ? 'class="active"' : ''; ?>><a href="<?php echo esc_url($submit_tour_schedules_url); ?>" title="<?php esc_attr_e('Submit Tour Schedules', 'bookyourtravel'); ?>"><?php esc_html_e('Submit Tour Schedules', 'bookyourtravel'); ?></a></li>
			<?php 		} ?>
			<?php 	} ?>				
			<?php 	if ($enable_cruises) { ?>					
			<?php 		if (!empty($submit_cruises_url)) { ?>
			<li <?php echo $current_url == $submit_cruises_url ? 'class="active"' : ''; ?>><a href="<?php echo esc_url($submit_cruises_url); ?>" title="<?php esc_attr_e('Submit Cruise', 'bookyourtravel'); ?>"><?php esc_html_e('Submit Cruise', 'bookyourtravel'); ?></a></li>
			<?php 		} ?>
			<?php 		if (!empty($submit_cabin_types_url)) { ?>
			<li <?php echo $current_url == $submit_cabin_types_url ? 'class="active"' : ''; ?>><a href="<?php echo esc_url($submit_cabin_types_url); ?>" title="<?php esc_attr_e('Submit Cabin Type', 'bookyourtravel'); ?>"><?php esc_html_e('Submit Cabin Type', 'bookyourtravel'); ?></a></li>
			<?php 		} ?>
			<?php 		if (!empty($submit_cruise_schedules_url)) { ?>
			<li <?php echo $current_url == $submit_cruise_schedules_url ? 'class="active"' : ''; ?>><a href="<?php echo esc_url($submit_cruise_schedules_url); ?>" title="<?php esc_attr_e('Submit Cruise Schedules', 'bookyourtravel'); ?>"><?php esc_html_e('Submit Cruise Schedules', 'bookyourtravel'); ?></a></li>
			<?php 		} ?>
			<?php 	} ?>				
			<?php 	if ($enable_car_rentals) { ?>					
			<?php 		if (!empty($submit_car_rentals_url)) { ?>
			<li <?php echo $current_url == $submit_car_rentals_url ? 'class="active"' : ''; ?>><a href="<?php echo esc_url($submit_car_rentals_url); ?>" title="<?php esc_attr_e('Submit Car Rental', 'bookyourtravel'); ?>"><?php esc_html_e('Submit Car Rental', 'bookyourtravel'); ?></a></li>
			<?php 		} ?>
			<?php 	} ?>				
			<?php } ?>
		</ul>
	</nav>
	<!--// inner navigation-->
<?php } else { ?>
	<!--inner navigation-->
	<nav class="inner-nav">
		<ul>
			<?php if (!empty($my_account_page_url)) { ?>
			<li <?php echo $current_url == $my_account_page_url ? 'class="active"' : ''; ?>><a href="<?php echo esc_url($my_account_page_url); ?>" title="<?php esc_attr_e('Settings', 'bookyourtravel'); ?>"><?php esc_html_e('Settings', 'bookyourtravel'); ?></a></li>
			<?php } ?>
			<?php if (!$is_partner_page && $enable_accommodations) { ?>
			<li><a href="#accommodation-bookings" title="<?php esc_attr_e('My Accommodation Bookings', 'bookyourtravel'); ?>"><?php esc_html_e('My Accommodation Bookings', 'bookyourtravel'); ?></a></li>
			<?php } ?>
			<?php if (!$is_partner_page && $enable_car_rentals) { ?>
			<li><a href="#car_rental-bookings" title="<?php esc_attr_e('My Car Rental Bookings', 'bookyourtravel'); ?>"><?php esc_html_e('My Car Rental Bookings', 'bookyourtravel'); ?></a></li>
			<?php } ?>
			<?php if (!$is_partner_page && $enable_tours) { ?>
			<li><a href="#tour-bookings" title="<?php esc_attr_e('My Tour Bookings', 'bookyourtravel'); ?>"><?php esc_html_e('My Tour Bookings', 'bookyourtravel'); ?></a></li>
			<?php } ?>
			<?php if (!$is_partner_page && $enable_cruises) { ?>
			<li><a href="#cruise-bookings" title="<?php esc_attr_e('My Cruise Bookings', 'bookyourtravel'); ?>"><?php esc_html_e('My Cruise Bookings', 'bookyourtravel'); ?></a></li>
			<?php } ?>
			<?php if (!$is_partner_page && $enable_reviews) { ?>
			<li><a href="#reviews" title="<?php esc_attr_e('My Reviews', 'bookyourtravel'); ?>"><?php esc_html_e('My Reviews', 'bookyourtravel'); ?></a></li>
			<?php } ?>
		</ul>
	</nav>
	<!--// inner navigation-->
<?php }
}