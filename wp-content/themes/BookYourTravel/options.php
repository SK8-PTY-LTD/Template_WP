<?php
/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 * By default it uses the theme name, in lowercase and without spaces, but this can be changed if needed.
 * If the identifier changes, it'll appear as if the options have been reset.
 */

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the 'id' fields, make sure to use all lowercase and no spaces.
 *
 * If you are making your theme translatable, you should replace 'bookyourtravel'
 * with the actual text domain for your theme.  Read more:
 * http://codex.wordpress.org/Function_Reference/load_theme_textdomain
 */
 
function optionsframework_options() {

	$page_sidebars = array(
		'' => esc_html__('No sidebar', 'bookyourtravel'),
		'left' => esc_html__('Left sidebar', 'bookyourtravel'),
		'right' => esc_html__('Right sidebar', 'bookyourtravel'),
		'both' => esc_html__('Left and right sidebars', 'bookyourtravel'),
	);

	$color_scheme_array = array(
		'' => esc_html__('Default', 'bookyourtravel'),
		'theme-black' => esc_html__('Black', 'bookyourtravel'),
		'theme-blue' => esc_html__('Blue', 'bookyourtravel'),
		'theme-orange' => esc_html__('Orange', 'bookyourtravel'),
		'theme-pink' => esc_html__('Pink', 'bookyourtravel'),
		'theme-purple' => esc_html__('Purple', 'bookyourtravel'),
		'theme-strawberry' => esc_html__('Strawberry', 'bookyourtravel'),
		'theme-yellow' => esc_html__('Yellow', 'bookyourtravel'),
		'theme-navy' => esc_html__('Navy', 'bookyourtravel'),
	);
		
	$pages = get_pages(); 
	$pages_array = array();
	$pages_array[0] = esc_html__('Select page', 'bookyourtravel');
	foreach ( $pages as $page ) {
		$pages_array[$page->ID] = $page->post_title;
	}
	
	$price_decimals_array = array(
		'0' => esc_html__('Zero (e.g. $200)', 'bookyourtravel'),
		'1' => esc_html__('One  (e.g. $200.0)', 'bookyourtravel'),
		'2' => esc_html__('Two (e.g. $200.00)', 'bookyourtravel'),
	);
	
	$search_results_view_array = array(
		'0' => esc_html__('Grid view', 'bookyourtravel'),
		'1' => esc_html__('List view', 'bookyourtravel'),
	);

	$pause_seconds_array = array(
		'1' => esc_html__('One second', 'bookyourtravel'),
		'2' => esc_html__('Two seconds', 'bookyourtravel'),
		'3' => esc_html__('Three seconds', 'bookyourtravel'),
		'4' => esc_html__('Four seconds', 'bookyourtravel'),
		'5' => esc_html__('Five seconds', 'bookyourtravel'),
		'6' => esc_html__('Six seconds', 'bookyourtravel'),
		'7' => esc_html__('Seven seconds', 'bookyourtravel'),
		'8' => esc_html__('Eight seconds', 'bookyourtravel'),
		'9' => esc_html__('Nine seconds', 'bookyourtravel'),
		'10' => esc_html__('Ten seconds', 'bookyourtravel'),
	);
	
	$options = array();

	$options[] = array(
		'name' => esc_html__('General Settings', 'bookyourtravel'),
		'type' => 'heading');
		
	$options[] = array(
		'name' => esc_html__('Website logo', 'bookyourtravel'),
		'desc' => esc_html__('Upload your website logo to go in place of default theme logo.N.U-L-L-24.N.E.T', 'bookyourtravel'),
		'id' => 'website_logo_upload',
		'type' => 'upload');
		
	if ( ! function_exists( 'get_site_icon_url' ) ) {	
	
		$options[] = array(
			'name' => esc_html__('Favicon', 'bookyourtravel'),
			'desc' => esc_html__('Upload your website favicon to go in place of default theme favicon.N,U,L,L,24.N,E.T', 'bookyourtravel'),
			'id' => 'website_favicon_upload',
			'type' => 'upload');
	}
		
	$options[] = array(
		'name' => esc_html__('Select color scheme', 'bookyourtravel'),
		'desc' => esc_html__('Select website color scheme.', 'bookyourtravel'),
		'id' => 'color_scheme_select',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $color_scheme_array);
		
	$options[] = array(
		'name' => esc_html__('Company name', 'bookyourtravel'),
		'desc' => esc_html__('Company name displayed on the contact us page.', 'bookyourtravel'),
		'id' => 'contact_company_name',
		'std' => 'Book Your Travel LLC',
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'name' => esc_html__('Contact phone number', 'bookyourtravel'),
		'desc' => esc_html__('Contact phone number displayed on the site.', 'bookyourtravel'),
		'id' => 'contact_phone_number',
		'std' => '1- 555 - 555 - 555',
		'class' => 'mini',
		'type' => 'text');

	$options[] = array(
		'name' => esc_html__('Contact address street', 'bookyourtravel'),
		'desc' => esc_html__('Contact address street displayed on the contact us page.', 'bookyourtravel'),
		'id' => 'contact_address_street',
		'std' => '1400 Pennsylvania Ave',
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'name' => esc_html__('Contact address city', 'bookyourtravel'),
		'desc' => esc_html__('Contact address city displayed on the contact us page.N-U,L,L,24.N,E.T', 'bookyourtravel'),
		'id' => 'contact_address_city',
		'std' => 'Washington DC',
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'name' => esc_html__('Contact address country', 'bookyourtravel'),
		'desc' => esc_html__('Contact address country displayed on the contact us page.', 'bookyourtravel'),
		'id' => 'contact_address_country',
		'std' => 'USA',
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'name' => esc_html__('Contact email', 'bookyourtravel'),
		'desc' => esc_html__('Contact email displayed on the contact us page.', 'bookyourtravel'),
		'id' => 'contact_email',
		'std' => 'info at bookyourtravel',
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'name' => esc_html__('Business address latitude', 'bookyourtravel'),
		'desc' => esc_html__('Enter your business address latitude to use for contact form map', 'bookyourtravel'),
		'id' => 'business_address_latitude',
		'std' => '49.47216',
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'name' => esc_html__('Business address longitude', 'bookyourtravel'),
		'desc' => esc_html__('Enter your business address longitude to use for contact form map', 'bookyourtravel'),
		'id' => 'business_address_longitude',
		'std' => '-123.76307',
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'name' => esc_html__('Footer copyright notice', 'bookyourtravel'),
		'desc' => esc_html__('Copyright notice in footer.', 'bookyourtravel'),
		'id' => 'copyright_footer',
		'std' => '&copy; bookyourtravel.com 2015. All rights reserved.',
		'type' => 'text');

	$options[] = array(
		'name' => esc_html__('Configuration Settings', 'bookyourtravel'),
		'type' => 'heading');
		
	$options[] = array(
		'name' => esc_html__('Google maps api key', 'bookyourtravel'),
		'desc' => esc_html__('Google maps now requires you to provide an api key when using their maps api. As a result of this you must go to their <a href="https://developers.google.com/maps/documentation/javascript/get-api-key">site</a> and get a key. After you do, enter it below.', 'bookyourtravel'),
		'id' => 'google_maps_key',
		'std' => '',
		'class' => 'mini', //mini, tiny, small
		'type' => 'text');		
		
	$options[] = array(
		'name' => esc_html__('Price decimal places', 'bookyourtravel'),
		'desc' => esc_html__('Number of decimal places to show for prices', 'bookyourtravel'),
		'id' => 'price_decimal_places',
		'std' => '0',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $price_decimals_array);		

	$options[] = array(
		'name' => esc_html__('Pause between slides of single lightSlider gallery', 'bookyourtravel'),
		'desc' => esc_html__('Number of seconds to pause between showing each slide in the lightSlider gallery used on single accommodation, tour, cruise and car rental pages.', 'bookyourtravel'),
		'id' => 'light_slider_pause_between_slides',
		'std' => '3',
		'type' => 'select',
		'class' => 'mini',
		'options' => $pause_seconds_array);
		
	$options[] = array(
		'name' => esc_html__('Default currency symbol', 'bookyourtravel'),
		'desc' => esc_html__('What is your default currency symbol', 'bookyourtravel'),
		'id' => 'default_currency_symbol',
		'std' => '$',
		'class' => 'mini', //mini, tiny, small
		'type' => 'text');

	$options[] = array(
		'name' => esc_html__('Show currency symbol after price?', 'bookyourtravel'),
		'desc' => esc_html__('If this option is checked, currency symbol will show up after the price, instead of before (e.g. 150 $ instead of $150).', 'bookyourtravel'),
		'id' => 'show_currency_symbol_after',
		'std' => '0',
		'type' => 'checkbox');
		
	$options[] = array(
		'name' => esc_html__('Hide entire header ribbon', 'bookyourtravel'),
		'desc' => esc_html__('Hide the entire header ribbon (with my account, currencies, languages etc)', 'bookyourtravel'),
		'id' => 'hide_header_ribbon',
		'std' => '0',
		'type' => 'checkbox');
		
	$options[] = array(
		'name' => esc_html__('Hide my account part of header ribbon', 'bookyourtravel'),
		'desc' => esc_html__('Hide the my account part of header ribbon', 'bookyourtravel'),
		'id' => 'hide_my_account_header_ribbon',
		'std' => '0',
		'type' => 'checkbox');
		
	$options[] = array(
		'name' => esc_html__('Enable RTL', 'bookyourtravel'),
		'desc' => esc_html__('Enable right-to-left support', 'bookyourtravel'),
		'id' => 'enable_rtl',
		'std' => '0',
		'type' => 'checkbox');
		
	$options[] = array(
		'name' => esc_html__('Add captcha to forms', 'bookyourtravel'),
		'desc' => esc_html__('Add simple captcha implemented inside BookYourTravel theme to forms (login, register, book, inquire, contact etc)', 'bookyourtravel'),
		'id' => 'add_captcha_to_forms',
		'std' => '1',
		'type' => 'checkbox');
		
	$options[] = array(
		'name' => esc_html__('Override wp-login.php', 'bookyourtravel'),
		'desc' => esc_html__('Override wp-login.php and use custom login, register, forgot password pages', 'bookyourtravel'),
		'id' => 'override_wp_login',
		'std' => '0',
		'type' => 'checkbox');
		
	$options[] = array(
		'name' => esc_html__('Publish frontend submitted content immediately?', 'bookyourtravel'),
		'desc' => esc_html__('When users submit content via frontend, do you publish it immediately or do you leave it for admin to review?', 'bookyourtravel'),
		'id' => 'publish_frontend_submissions_immediately',
		'std' => '0',
		'type' => 'checkbox');
		
	$options[] = array(
		'name' => esc_html__('Users specify password', 'bookyourtravel'),
		'desc' => esc_html__('Let users specify their password when registering', 'bookyourtravel'),
		'id' => 'let_users_set_pass',
		'std' => '0',
		'type' => 'checkbox');
		
	$options[] = array(
		'name' => esc_html__('Calculate "Price from" fields for list pages based on realtime availability of items.', 'bookyourtravel'),
		'desc' => esc_html__('If unchecked, price from will be minimum price of item in the future. Warning: if checked, we will check real time availability (ie against booking tables) of the item which may significantly impact performance and slow down your pages.', 'bookyourtravel'),
		'id' => 'calculate_real_time_prices_for_lists',
		'std' => '0',
		'type' => 'checkbox');
		
	$options[] = array(
		'name' => esc_html__('Page Settings', 'bookyourtravel'),
		'type' => 'heading');

	$sliders_array = array();
	if (class_exists ('RevSlider')) {
	
		$options[] = array(
			'name' => esc_html__('Show slider', 'bookyourtravel'),
			'desc' => esc_html__('Show slider on home page', 'bookyourtravel'),
			'id' => 'frontpage_show_slider',
			'std' => '0',
			'type' => 'checkbox');

		try {
			$slider = new RevSlider();
			$sliders_array = $slider->getAllSliderAliases();
		} catch(Exception $e) {}
		
		if (count($sliders_array) > 0) {
			$options[] = array(
				'name' => esc_html__('Homepage slider', 'bookyourtravel'),
				'desc' => esc_html__('Select homepage slider from existing sliders', 'bookyourtravel'),
				'id' => 'homepage_slider',
				'std' => '',
				'type' => 'select',
				'class' => 'mini', //mini, tiny, small
				'options' => $sliders_array);
		}
	}
	
	$options[] = array(
		'name' => esc_html__('My account dashboard page', 'bookyourtravel'),
		'desc' => esc_html__('Page that displays settings, bookings and reviews of logged in user', 'bookyourtravel'),
		'id' => 'my_account_page',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $pages_array);
		
	$options[] = array(
		'name' => esc_html__('Redirect to after login', 'bookyourtravel'),
		'desc' => esc_html__('Page to redirect to after login if "Override wp-login.php" is checked above', 'bookyourtravel'),
		'id' => 'redirect_to_after_login',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $pages_array);
		
	$options[] = array(
		'name' => esc_html__('Redirect to after logout', 'bookyourtravel'),
		'desc' => esc_html__('Page to redirect to after logout if "Override wp-login.php" is checked above', 'bookyourtravel'),
		'id' => 'redirect_to_after_logout',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $pages_array);

	$options[] = array(
		'name' => esc_html__('Login page url', 'bookyourtravel'),
		'desc' => esc_html__('Login page if "Override wp-login.php" is checked above', 'bookyourtravel'),
		'id' => 'login_page_url',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $pages_array);
		
	$options[] = array(
		'name' => esc_html__('Register page url', 'bookyourtravel'),
		'desc' => esc_html__('Register page if "Override wp-login.php" is checked above', 'bookyourtravel'),
		'id' => 'register_page_url',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $pages_array);
		
	$options[] = array(
		'name' => esc_html__('Reset password page url', 'bookyourtravel'),
		'desc' => esc_html__('Reset password page if "Override wp-login.php" is checked above', 'bookyourtravel'),
		'id' => 'reset_password_page_url',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $pages_array);
		
	$options[] = array(
		'name' => esc_html__('Terms &amp; conditions page url', 'bookyourtravel'),
		'desc' => esc_html__('Terms &amp; conditions page url', 'bookyourtravel'),
		'id' => 'terms_page_url',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $pages_array);
		
	$options[] = array(
		'name' => esc_html__('Contact Us page url', 'bookyourtravel'),
		'desc' => esc_html__('Contact Us page url', 'bookyourtravel'),
		'id' => 'contact_page_url',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $pages_array);
		
	$options[] = array(
		'name' => esc_html__('Submit room types page url', 'bookyourtravel'),
		'desc' => esc_html__('Submit room types page url', 'bookyourtravel'),
		'id' => 'submit_room_types_url',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini accommodations_controls', //mini, tiny, small
		'options' => $pages_array);
		
	$options[] = array(
		'name' => esc_html__('Submit accommodations page url', 'bookyourtravel'),
		'desc' => esc_html__('Submit accommodations page url', 'bookyourtravel'),
		'id' => 'submit_accommodations_url',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini accommodations_controls', //mini, tiny, small
		'options' => $pages_array);

	$options[] = array(
		'name' => esc_html__('Submit accommodation vacancies page url', 'bookyourtravel'),
		'desc' => esc_html__('Submit accommodation vacancies page url', 'bookyourtravel'),
		'id' => 'submit_accommodation_vacancies_url',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini accommodations_controls', //mini, tiny, small
		'options' => $pages_array);
		
	$options[] = array(
		'name' => esc_html__('Submit tours page url', 'bookyourtravel'),
		'desc' => esc_html__('Submit tours page url', 'bookyourtravel'),
		'id' => 'submit_tours_url',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini tours_controls', //mini, tiny, small
		'options' => $pages_array);
		
	$options[] = array(
		'name' => esc_html__('Submit tour schedules page url', 'bookyourtravel'),
		'desc' => esc_html__('Submit tour schedules page url', 'bookyourtravel'),
		'id' => 'submit_tour_schedules_url',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini tours_controls', //mini, tiny, small
		'options' => $pages_array);
		
	$options[] = array(
		'name' => esc_html__('Submit cruises page url', 'bookyourtravel'),
		'desc' => esc_html__('Submit cruises page url', 'bookyourtravel'),
		'id' => 'submit_cruises_url',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini cruises_controls', //mini, tiny, small
		'options' => $pages_array);
		
	$options[] = array(
		'name' => esc_html__('Submit cabin types page url', 'bookyourtravel'),
		'desc' => esc_html__('Submit cabin types page url', 'bookyourtravel'),
		'id' => 'submit_cabin_types_url',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini cruises_controls', //mini, tiny, small
		'options' => $pages_array);
		
	$options[] = array(
		'name' => esc_html__('Submit cruise schedules page url', 'bookyourtravel'),
		'desc' => esc_html__('Submit cruise schedules page url', 'bookyourtravel'),
		'id' => 'submit_cruise_schedules_url',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini cruises_controls', //mini, tiny, small
		'options' => $pages_array);
		
	$options[] = array(
		'name' => esc_html__('Submit car rentals page url', 'bookyourtravel'),
		'desc' => esc_html__('Submit car rentals page url', 'bookyourtravel'),
		'id' => 'submit_car_rentals_url',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini carrentals_controls', //mini, tiny, small
		'options' => $pages_array);
		
	$options[] = array(
		'name' => esc_html__('List user room types page url', 'bookyourtravel'),
		'desc' => esc_html__('List user room types page url', 'bookyourtravel'),
		'id' => 'list_user_room_types_url',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini accommodations_controls', //mini, tiny, small
		'options' => $pages_array);
		
	$options[] = array(
		'name' => esc_html__('List user accommodations page url', 'bookyourtravel'),
		'desc' => esc_html__('List user accommodations page url', 'bookyourtravel'),
		'id' => 'list_user_accommodations_url',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini accommodations_controls', //mini, tiny, small
		'options' => $pages_array);

	$options[] = array(
		'name' => esc_html__('List user accommodation vacancies page url', 'bookyourtravel'),
		'desc' => esc_html__('List user accommodation vacancies page url', 'bookyourtravel'),
		'id' => 'list_user_accommodation_vacancies_url',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini accommodations_controls', //mini, tiny, small
		'options' => $pages_array);
		
	$options[] = array(
		'name' => esc_html__('List user accommodation bookings page url', 'bookyourtravel'),
		'desc' => esc_html__('List user accommodation bookings page url', 'bookyourtravel'),
		'id' => 'list_user_accommodation_bookings_url',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini accommodations_controls', //mini, tiny, small
		'options' => $pages_array);		
		
	$options[] = array(
		'name' => esc_html__('List user tours page url', 'bookyourtravel'),
		'desc' => esc_html__('List user tours page url', 'bookyourtravel'),
		'id' => 'list_user_tours_url',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini tours_controls', //mini, tiny, small
		'options' => $pages_array);
		
	$options[] = array(
		'name' => esc_html__('List user tour schedules page url', 'bookyourtravel'),
		'desc' => esc_html__('List user tour schedules page url', 'bookyourtravel'),
		'id' => 'list_user_tour_schedules_url',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini tours_controls', //mini, tiny, small
		'options' => $pages_array);
		
	$options[] = array(
		'name' => esc_html__('List user tour bookings page url', 'bookyourtravel'),
		'desc' => esc_html__('List user tour bookings page url', 'bookyourtravel'),
		'id' => 'list_user_tour_bookings_url',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini tours_controls', //mini, tiny, small
		'options' => $pages_array);		
		
	$options[] = array(
		'name' => esc_html__('List user cruises page url', 'bookyourtravel'),
		'desc' => esc_html__('List user cruises page url', 'bookyourtravel'),
		'id' => 'list_user_cruises_url',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini cruises_controls', //mini, tiny, small
		'options' => $pages_array);
		
	$options[] = array(
		'name' => esc_html__('List user cabin types page url', 'bookyourtravel'),
		'desc' => esc_html__('List user cabin types page url', 'bookyourtravel'),
		'id' => 'list_user_cabin_types_url',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini cruises_controls', //mini, tiny, small
		'options' => $pages_array);
		
	$options[] = array(
		'name' => esc_html__('List user cruise schedules page url', 'bookyourtravel'),
		'desc' => esc_html__('List user cruise schedules page url', 'bookyourtravel'),
		'id' => 'list_user_cruise_schedules_url',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini cruises_controls', //mini, tiny, small
		'options' => $pages_array);
		
	$options[] = array(
		'name' => esc_html__('List user cruise bookings page url', 'bookyourtravel'),
		'desc' => esc_html__('List user cruise bookings page url', 'bookyourtravel'),
		'id' => 'list_user_cruise_bookings_url',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini cruises_controls', //mini, tiny, small
		'options' => $pages_array);		
		
	$options[] = array(
		'name' => esc_html__('List user car rentals page url', 'bookyourtravel'),
		'desc' => esc_html__('List user car rentals page url', 'bookyourtravel'),
		'id' => 'list_user_car_rentals_url',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini carrentals_controls', //mini, tiny, small
		'options' => $pages_array);
		
	$options[] = array(
		'name' => esc_html__('List user car rental bookings page url', 'bookyourtravel'),
		'desc' => esc_html__('List user car rental bookings page url', 'bookyourtravel'),
		'id' => 'list_user_car_rental_bookings_url',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini carrentals_controls', //mini, tiny, small
		'options' => $pages_array);		
		
	$options[] = array(
		'name' => esc_html__('Search Settings', 'bookyourtravel'),
		'type' => 'heading');
		
	$options[] = array(
		'name' => esc_html__('Search only available properties', 'bookyourtravel'),
		'desc' => esc_html__('Search displays only properties with valid vacancies/schedules etc', 'bookyourtravel'),
		'id' => 'search_only_available_properties',
		'std' => '1',
		'type' => 'checkbox');	
		
	$options[] = array(
		'name' => esc_html__('Custom search results page', 'bookyourtravel'),
		'desc' => esc_html__('Page to redirect to for custom search results', 'bookyourtravel'),
		'id' => 'redirect_to_search_results',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $pages_array);

	$options[] = array(
		'name' => esc_html__('Custom search results default view', 'bookyourtravel'),
		'desc' => esc_html__('Custom search results default view (grid or list view)', 'bookyourtravel'),
		'id' => 'search_results_default_view',
		'std' => '0',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $search_results_view_array);
		
	$options[] = array(
		'name' => esc_html__('Search results posts per page', 'bookyourtravel'),
		'desc' => esc_html__('Number of results to display on custom search page', 'bookyourtravel'),
		'id' => 'search_results_posts_per_page',
		'std' => '12',
		'type' => 'text');
		
	$options[] = array(
		'name' => esc_html__('Price range bottom', 'bookyourtravel'),
		'desc' => esc_html__('Bottom value of price range used in search form (usually 0)', 'bookyourtravel'),
		'id' => 'price_range_bottom',
		'std' => '0',
		'type' => 'text',
		'class' => 'mini');

	$options[] = array(
		'name' => esc_html__('Price range increment', 'bookyourtravel'),
		'desc' => esc_html__('Increment value of price range used in search form (default 50)', 'bookyourtravel'),
		'id' => 'price_range_increment',
		'std' => '50',
		'type' => 'text',
		'class' => 'mini');

	$options[] = array(
		'name' => esc_html__('Price range increment count', 'bookyourtravel'),
		'desc' => esc_html__('Increment count of price range used in search form (default 5)', 'bookyourtravel'),
		'id' => 'price_range_count',
		'std' => '5',
		'type' => 'text',
		'class' => 'mini');
		
	
	$options[] = array(
		'name' => esc_html__('Post Types', 'bookyourtravel'),
		'type' => 'heading');
		
	$options[] = array(
		'name' => esc_html__('Enable Accommodations', 'bookyourtravel'),
		'desc' => esc_html__('Enable "Accommodations" data-type', 'bookyourtravel'),
		'id' => 'enable_accommodations',
		'std' => '0',
		'type' => 'checkbox');			
		
	$options[] = array(
		'name' => esc_html__('Enable Tours', 'bookyourtravel'),
		'desc' => esc_html__('Enable "Tours" data-type', 'bookyourtravel'),
		'id' => 'enable_tours',
		'std' => '0',
		'type' => 'checkbox');	

	$options[] = array(
		'name' => esc_html__('Enable Car rentals', 'bookyourtravel'),
		'desc' => esc_html__('Enable "Car rentals" data-type', 'bookyourtravel'),
		'id' => 'enable_car_rentals',
		'std' => '0',
		'type' => 'checkbox');	
		
	$options[] = array(
		'name' => esc_html__('Enable Cruises', 'bookyourtravel'),
		'desc' => esc_html__('Enable "Cruises" data-type', 'bookyourtravel'),
		'id' => 'enable_cruises',
		'std' => '0',
		'type' => 'checkbox');	

	$options[] = array(
		'name' => esc_html__('Enable Reviews', 'bookyourtravel'),
		'desc' => esc_html__('Enable "Reviews" data-type', 'bookyourtravel'),
		'id' => 'enable_reviews',
		'std' => '0',
		'type' => 'checkbox');
		
	$options[] = array(
		'name' => esc_html__('Enable Extra Items', 'bookyourtravel'),
		'desc' => esc_html__('Enable "Extra Items" data-type to charge people for things like full-board, wifi, tour guides, fuel etc', 'bookyourtravel'),
		'id' => 'enable_extra_items',
		'std' => '0',
		'type' => 'checkbox');
		
	$options[] = array(
		'name' => esc_html__('Locations', 'bookyourtravel'),
		'type' => 'heading');
		
	$options[] = array(
		'name' => esc_html__('Show accommodation count in location items', 'bookyourtravel'),
		'desc' => esc_html__('Show accommodation count in location items shown on location list pages', 'bookyourtravel'),
		'id' => 'show_accommodation_count_in_location_items',
		'std' => '1',
		'class' => 'accommodations_controls',
		'type' => 'checkbox');
		
	$options[] = array(
		'name' => esc_html__('Show cruise count in location items', 'bookyourtravel'),
		'desc' => esc_html__('Show cruise count in location items shown location list page', 'bookyourtravel'),
		'id' => 'show_cruise_count_in_location_items',
		'std' => '0',
		'class' => 'cruises_controls',
		'type' => 'checkbox');
		
	$options[] = array(
		'name' => esc_html__('Show tour count in location items', 'bookyourtravel'),
		'desc' => esc_html__('Show tour count in location items shown location list page', 'bookyourtravel'),
		'id' => 'show_tour_count_in_location_items',
		'std' => '0',
		'class' => 'tours_controls',
		'type' => 'checkbox');
		
	$options[] = array(
		'name' => esc_html__('Show car rental count in location items', 'bookyourtravel'),
		'desc' => esc_html__('Show car rental count in location items shown location list page-N,U-L,L,24.N,E.T', 'bookyourtravel'),
		'id' => 'show_car_rental_count_in_location_items',
		'std' => '0',
		'class' => 'carrentals_controls',
		'type' => 'checkbox');
		
	$allowed_tags = array();
	$allowed_tags['strong'] = array('class' => array());
	$allowed_tags['span'] = array('class' => array());
	$allowed_tags['br'] = array();
	$allowed_tags['a'] = array('class' => array(), 'href' => array());
		
	$options[] = array(
		'name' => esc_html__('Single location permalink slug', 'bookyourtravel'),
		'desc' => wp_kses(__('The permalink slug used for single locations (by default it is set to "location". <br /><strong>Note:</strong> Please make sure you flush your rewrite rules after changing this setting. You can do so by navigating to <a href="/wp-admin/options-permalink.php">Settings->Permalinks</a> and clicking "Save Changes".', 'bookyourtravel'), $allowed_tags),
		'id' => 'locations_permalink_slug',
		'std' => 'location',
		'type' => 'text');
		
	$options[] = array(
		'name' => esc_html__('Locations archive posts per page', 'bookyourtravel'),
		'desc' => esc_html__('Number of locations to display on locations archive page', 'bookyourtravel'),
		'id' => 'locations_archive_posts_per_page',
		'std' => '12',
		'type' => 'text');		
		
	$options[] = array(
		'name' => esc_html__('Tabs displayed on single location page.', 'bookyourtravel'),
		'desc' => esc_html__('Use drag&drop to change order of tabs.', 'bookyourtravel'),
		'id' => 'location_tabs',
		'std' => 'Tab name',
		'type' => 'repeat_tab');
		
	$options[] = array(
		'name' => esc_html__('Extra fields displayed on single location page.', 'bookyourtravel'),
		'desc' => esc_html__('Select the tab your field is displayed on from the tab dropdown.', 'bookyourtravel'),
		'id' => 'location_extra_fields',
		'std' => 'Default field label',
		'type' => 'repeat_extra_field');
		
	$options[] = array(
		'name' => esc_html__('Accommodations', 'bookyourtravel'),
		'type' => 'heading');
		
	$options[] = array(
		'name' => esc_html__('Single accommodation permalink slug', 'bookyourtravel'),
		'desc' => wp_kses(__('The permalink slug used for creating single accommodations (by default it is set to "hotel". <br /><strong>Note:</strong> Please make sure you flush your rewrite rules after changing this setting. You can do so by navigating to <a href="/wp-admin/options-permalink.php">Settings->Permalinks</a> and clicking "Save Changes".', 'bookyourtravel'), $allowed_tags),
		'id' => 'accommodations_permalink_slug',
		'std' => 'hotel',
		'type' => 'text');
		
	$options[] = array(
		'name' => esc_html__('Accommodations archive posts per page', 'bookyourtravel'),
		'desc' => esc_html__('Number of accommodations to display on accommodations archive page', 'bookyourtravel'),
		'id' => 'accommodations_archive_posts_per_page',
		'std' => '12',
		'type' => 'text');		
		
	$options[] = array(
		'name' => esc_html__('Tabs displayed on single accommodation page.', 'bookyourtravel'),
		'desc' => esc_html__('Use drag&drop to change order of tabs.', 'bookyourtravel'),
		'id' => 'accommodation_tabs',
		'std' => 'Tab name',
		'type' => 'repeat_tab');
		
	$options[] = array(
		'name' => esc_html__('Extra fields displayed on single accommodation page.', 'bookyourtravel'),
		'desc' => esc_html__('Select the tab your field is displayed on from the tab dropdown.', 'bookyourtravel'),
		'id' => 'accommodation_extra_fields',
		'std' => 'Default field label',
		'type' => 'repeat_extra_field');
		
	$options[] = array(
		'name' => esc_html__('Tours', 'bookyourtravel'),
		'type' => 'heading');
		
	$options[] = array(
		'name' => esc_html__('Tours archive posts per page', 'bookyourtravel'),
		'desc' => esc_html__('Number of tours to display on tours archive page', 'bookyourtravel'),
		'id' => 'tours_archive_posts_per_page',
		'std' => '12',
		'type' => 'text');
		
	$options[] = array(
		'name' => esc_html__('Single tour permalink slug', 'bookyourtravel'),
		'desc' => wp_kses(__('The permalink slug used for single tours (by default it is set to "tour". <br /><strong>Note:</strong> Please make sure you flush your rewrite rules after changing this setting. You can do so by navigating to <a href="/wp-admin/options-permalink.php">Settings->Permalinks</a> and clicking "Save Changes".', 'bookyourtravel'), $allowed_tags),
		'id' => 'tours_permalink_slug',
		'std' => 'tours',
		'type' => 'text');
		
	$options[] = array(
		'name' => esc_html__('Tabs displayed on single tour page.', 'bookyourtravel'),
		'desc' => esc_html__('Use drag&drop to change order of tabs.', 'bookyourtravel'),
		'id' => 'tour_tabs',
		'std' => 'Tab name',
		'type' => 'repeat_tab');
		
	$options[] = array(
		'name' => esc_html__('Extra fields displayed on single tour page.', 'bookyourtravel'),
		'desc' => esc_html__('Select the tab your field is displayed on from the tab dropdown.', 'bookyourtravel'),
		'id' => 'tour_extra_fields',
		'std' => 'Default field label',
		'type' => 'repeat_extra_field');
		
	$options[] = array(
		'name' => esc_html__('Car Rentals', 'bookyourtravel'),
		'type' => 'heading');
		
	$options[] = array(
		'name' => esc_html__('Car rentals archive posts per page', 'bookyourtravel'),
		'desc' => esc_html__('Number of car rentals to display on car rentals archive page', 'bookyourtravel'),
		'id' => 'car_rentals_archive_posts_per_page',
		'std' => '12',
		'type' => 'text');
		
	$options[] = array(
		'name' => esc_html__('Single car rental permalink slug', 'bookyourtravel'),
		'desc' => wp_kses(__('The permalink slug used for single car rentals (by default it is set to "car-rental". <br /><strong>Note:</strong> Please make sure you flush your rewrite rules after changing this setting. You can do so by navigating to <a href="/wp-admin/options-permalink.php">Settings->Permalinks</a> and clicking "Save Changes".', 'bookyourtravel'), $allowed_tags),
		'id' => 'car_rentals_permalink_slug',
		'std' => 'car-rentals',
		'type' => 'text');
		
	$options[] = array(
		'name' => esc_html__('Tabs displayed on single car rental page.', 'bookyourtravel'),
		'desc' => esc_html__('Use drag&drop to change order of tabs.', 'bookyourtravel'),
		'id' => 'car_rental_tabs',
		'std' => 'Tab name',
		'type' => 'repeat_tab');
		
	$options[] = array(
		'name' => esc_html__('Extra fields displayed on single car rental page.', 'bookyourtravel'),
		'desc' => esc_html__('Select the tab your field is displayed on from the tab dropdown.', 'bookyourtravel'),
		'id' => 'car_rental_extra_fields',
		'std' => 'Default field label',
		'type' => 'repeat_extra_field');
		
	$options[] = array(
		'name' => esc_html__('Cruises', 'bookyourtravel'),
		'type' => 'heading');
		
	$options[] = array(
		'name' => esc_html__('Cruises archive posts per page', 'bookyourtravel'),
		'desc' => esc_html__('Number of cruises to display on cruises archive page', 'bookyourtravel'),
		'id' => 'cruises_archive_posts_per_page',
		'std' => '12',
		'type' => 'text');
		
	$options[] = array(
		'name' => esc_html__('Single cruise permalink slug', 'bookyourtravel'),
		'desc' => wp_kses(__('The permalink slug used for single cruises (by default it is set to "cruise". <br /><strong>Note:</strong> Please make sure you flush your rewrite rules after changing this setting. You can do so by navigating to <a href="/wp-admin/options-permalink.php">Settings->Permalinks</a> and clicking "Save Changes".', 'bookyourtravel'), $allowed_tags),
		'id' => 'cruises_permalink_slug',
		'std' => 'cruises',
		'type' => 'text');
		
	$options[] = array(
		'name' => esc_html__('Tabs displayed on single cruise page.', 'bookyourtravel'),
		'desc' => esc_html__('Use drag&drop to change order of tabs.', 'bookyourtravel'),
		'id' => 'cruise_tabs',
		'std' => 'Tab name',
		'type' => 'repeat_tab');
		
	$options[] = array(
		'name' => esc_html__('Extra fields displayed on single cruise page.', 'bookyourtravel'),
		'desc' => esc_html__('Select the tab your field is displayed on from the tab dropdown.', 'bookyourtravel'),
		'id' => 'cruise_extra_fields',
		'std' => 'Default field label',
		'type' => 'repeat_extra_field');

	$options[] = array(
		'name' => esc_html__('Reviews', 'bookyourtravel'),
		'type' => 'heading');
		
	$options[] = array(
		'text' => esc_html__('Synchronise reviews', 'bookyourtravel'),
		'name' => esc_html__('Synchronise review totals', 'bookyourtravel'),
		'desc' => esc_html__('Click this button to synchronise review totals if your review totals are out of sync', 'bookyourtravel'),
		'id' => 'synchronise_reviews',
		'std' => 'Default',
		'type' => 'link_button_field');
		
	$options[] = array(
		'name' => esc_html__('Accommodation review fields', 'bookyourtravel'),
		'desc' => esc_html__('Review fields for single accommodation', 'bookyourtravel'),
		'id' => 'accommodation_review_fields',
		'std' => 'Default review field label',
		'type' => 'repeat_review_field');
		
	$options[] = array(
		'name' => esc_html__('Tour review fields', 'bookyourtravel'),
		'desc' => esc_html__('Review fields for single tour.', 'bookyourtravel'),
		'id' => 'tour_review_fields',
		'std' => 'Default review field label',
		'type' => 'repeat_review_field');
		
	$options[] = array(
		'name' => esc_html__('Cruise review fields', 'bookyourtravel'),
		'desc' => esc_html__('Review fields for single cruise.', 'bookyourtravel'),
		'id' => 'cruise_review_fields',
		'std' => 'Default review field label',
		'type' => 'repeat_review_field');

	$options[] = array(
		'name' => esc_html__('Car rental review fields', 'bookyourtravel'),
		'desc' => esc_html__('Review fields for single car rental.', 'bookyourtravel'),
		'id' => 'car_rental_review_fields',
		'std' => 'Default review field label',
		'type' => 'repeat_review_field');
		
	$options[] = array(
		'name' => esc_html__('Inquiry Forms', 'bookyourtravel'),
		'type' => 'heading');
		
	$options[] = array(
		'name' => esc_html__('Inquiry form fields', 'bookyourtravel'),
		'desc' => esc_html__('Inquiry form fields accommodations, tours, cruises and car rentals.', 'bookyourtravel'),
		'id' => 'inquiry_form_fields',
		'std' => 'Default form field label',
		'type' => 'repeat_form_field');
		
	$options[] = array(
		'name' => esc_html__('Booking Forms', 'bookyourtravel'),
		'type' => 'heading');
		
	$options[] = array(
		'name' => esc_html__('Booking form fields', 'bookyourtravel'),
		'desc' => esc_html__('Booking form fields for accommodations, tours, cruises and car rentals.', 'bookyourtravel'),
		'id' => 'booking_form_fields',
		'std' => 'Default form field label',
		'type' => 'repeat_form_field');
		
	$bookyourtravel_needs_update = get_option( '_byt_needs_update', 0 );
	
	if ($bookyourtravel_needs_update) {

		$options[] = array(
			'name' => esc_html__('Upgrades', 'bookyourtravel'),
			'type' => 'heading');

		$bookyourtravel_version_before_update = get_option('_byt_version_before_update', 0);
		global $bookyourtravel_installed_version;
		
		// version_compare( $bookyourtravel_version_before_update, $bookyourtravel_installed_version, '<' )
		if ( null !== $bookyourtravel_installed_version && $bookyourtravel_version_before_update < $bookyourtravel_installed_version ) {
						
			$options[] = array(
				'text' => esc_html__('Click here to upgrade', 'bookyourtravel'),
				'name' => esc_html__('Your Book Your Travel database needs an upgrade!', 'bookyourtravel'),
				'desc' => sprintf(__('Your current database version is <strong>%s</strong>, while the current theme version is <strong>%s</strong>.', 'bookyourtravel'), $bookyourtravel_version_before_update, $bookyourtravel_installed_version),
				'id' => 'upgrade_bookyourtravel_db',
				'std' => 'Default',
				'type' => 'link_button_field');
		}

	}
	
	if (BookYourTravel_Theme_Utils::is_woocommerce_active()) {
	
		$options[] = array(
			'name' => esc_html__('WooCommerce integration', 'bookyourtravel'),
			'type' => 'heading');

		$options[] = array(
			'name' => esc_html__('Use WooCommerce for checkout', 'bookyourtravel'),
			'desc' => esc_html__('Use WooCommerce to enable payment after booking', 'bookyourtravel'),
			'id' => 'use_woocommerce_for_checkout',
			'std' => '0',
			'type' => 'checkbox');
			
		$status_array = array (
			'pending' => esc_html__('Pending', 'bookyourtravel'),
			'on-hold' => esc_html__('On hold', 'bookyourtravel'),
			'completed' => esc_html__('Completed', 'bookyourtravel'),
			'processing' => esc_html__('Processing', 'bookyourtravel'),
			'cancelled' => esc_html__('Cancelled', 'bookyourtravel'),
			'initiated' => esc_html__('Initiated', 'bookyourtravel'),
		);
		
		$options[] = array(
			'name' => esc_html__('Completed order WooCommerce statuses', 'bookyourtravel'),
			'desc' => esc_html__('Which WooCommerce statuses do you want to consider as completed so that the item is no longer treated as available?', 'bookyourtravel'),
			'id' => 'completed_order_woocommerce_statuses',
			'options' => $status_array,
			'std' => 'completed',
			'class' => '', //mini, tiny, small
			'type' => 'multicheck');
			
		$options[] = array(
			'name' => esc_html__('WooCommerce pages sidebar position', 'bookyourtravel'),
			'desc' => esc_html__('Select the position (if any) of sidebars to appear on all WooCommerce-specific pages of your website.', 'bookyourtravel'),
			'id' => 'woocommerce_pages_sidebar_position',
			'std' => 'three',
			'type' => 'select',
			'class' => 'mini', //mini, tiny, small
			'options' => $page_sidebars);
	}
		
	return $options;
}