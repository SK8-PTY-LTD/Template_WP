<?php

class BookYourTravel_Theme_Ajax extends BookYourTravel_BaseSingleton {
	
	protected function __construct() {
	
        // our parent class might contain shared code in its constructor
        parent::__construct();		
    }
	
    public function init() {
	
		do_action( 'bookyourtravel_initialize_ajax' );

		add_action( 'wp_ajax_inquiry_ajax_request', array( $this, 'inquiry_ajax_request' ) );
		add_action( 'wp_ajax_nopriv_inquiry_ajax_request', array( $this, 'inquiry_ajax_request' ) );
		add_action( 'wp_ajax_settings_ajax_save_password', array( $this, 'settings_ajax_save_password' ) );
		add_action( 'wp_ajax_settings_ajax_save_email', array( $this, 'settings_ajax_save_email' ) );
		add_action( 'wp_ajax_settings_ajax_save_last_name', array( $this, 'settings_ajax_save_last_name' ) );
		add_action( 'wp_ajax_settings_ajax_save_first_name', array( $this, 'settings_ajax_save_first_name' ) );		
		add_action( 'wp_ajax_upgrade_bookyourtravel_db_ajax_request', array( $this, 'upgrade_bookyourtravel_db' ) );		
		add_action( 'wp_ajax_generate_unique_dynamic_element_id', array( $this, 'generate_unique_dynamic_element_id' ) );
	}
	
	function generate_unique_dynamic_element_id() {
		
		if ( isset($_REQUEST) ) {

			$nonce = wp_kses($_REQUEST['nonce'], array());
			
			if ( wp_verify_nonce( $nonce, 'optionsframework-options' ) ) {

				global $bookyourtravel_theme_globals;
				
				$element_type = sanitize_text_field($_REQUEST['element_type']);
				$parent = sanitize_text_field($_REQUEST['parent']);
				$element_id = trim(sanitize_text_field($_REQUEST['element_id']));
				
				if (empty($element_id) && $element_type == 'tab') {
					$element_id = 't';
				} else if (empty($element_id) && $element_type == 'field') {
					$element_id = 'f';
				} else if (empty($element_id) && $element_type == 'review_field') {
					$element_id = 'rf';
				} else if (empty($element_id) && $element_type == 'inquiry_form_field') {
					$element_id = 'iff';
				} else if (empty($element_id) && $element_type == 'booking_form_field') {
					$element_id = 'bff';
				}
				
				if ($element_type == 'review_field' && !BookYourTravel_Theme_Utils::string_starts_with($element_id, 'review_')) {
					$element_id = 'review_' . $element_id;
				}
				
				$elements = null;
				if ($parent == 'location_tabs') {
					$elements = $bookyourtravel_theme_globals->get_location_tabs();
				} else if ($parent == 'accommodation_tabs') {
					$elements = $bookyourtravel_theme_globals->get_accommodation_tabs();
				} else if ($parent == 'tour_tabs') {
					$elements = $bookyourtravel_theme_globals->get_tour_tabs();
				} else if ($parent == 'cruise_tabs') {
					$elements = $bookyourtravel_theme_globals->get_cruise_tabs();
				} else if ($parent == 'car_rental_tabs') {
					$elements = $bookyourtravel_theme_globals->get_car_rental_tabs();
				} else if ($parent == 'location_extra_fields') {
					$elements = $bookyourtravel_theme_globals->get_location_extra_fields();
				} else if ($parent == 'accommodation_extra_fields') {
					$elements = $bookyourtravel_theme_globals->get_accommodation_extra_fields();
				} else if ($parent == 'tour_extra_fields') {
					$elements = $bookyourtravel_theme_globals->get_tour_extra_fields();
				} else if ($parent == 'cruise_extra_fields') {
					$elements = $bookyourtravel_theme_globals->get_cruise_extra_fields();
				} else if ($parent == 'car_rental_extra_fields') {
					$elements = $bookyourtravel_theme_globals->get_car_rental_extra_fields();
				} else if ($parent == 'car_rental_review_fields') {
					$elements = $bookyourtravel_theme_globals->get_car_rental_review_fields();
				} else if ($parent == 'cruise_review_fields') {
					$elements = $bookyourtravel_theme_globals->get_cruise_review_fields();
				} else if ($parent == 'tour_review_fields') {
					$elements = $bookyourtravel_theme_globals->get_tour_review_fields();
				} else if ($parent == 'accommodation_review_fields') {
					$elements = $bookyourtravel_theme_globals->get_accommodation_review_fields();
				} else if ($parent == 'inquiry_form_fields') {
					$elements = $bookyourtravel_theme_globals->inquiry_form_fields();
				} else if ($parent == 'booking_form_fields') {
					$elements = $bookyourtravel_theme_globals->get_booking_form_fields();
				}

				$exists_count = 1;
				$new_element_id = $element_id;
				$exists = BookYourTravel_Theme_Of_Custom::of_element_exists($elements, $element_id);
				if ($exists) {
					while ($exists) {
						$new_element_id = $element_id . '_' . $exists_count;
						$exists = BookYourTravel_Theme_Of_Custom::of_element_exists($elements, $new_element_id);
						$exists_count++;
					}
				}
				
				echo json_encode($new_element_id);
			}
		}
		
		die();		
	}

	function force_upgrade_bookyourtravel_db() {
	
		global $wpdb, $force_recreate_tables, $bookyourtravel_accommodation_helper, $bookyourtravel_cruise_helper, $bookyourtravel_tour_helper, $bookyourtravel_theme_globals, $bookyourtravel_car_rental_helper;
		
		$force_recreate_tables = true;
		// update post meta from accommodation_is_self_catered to accommodation_disabled_room_types;
		$sql = "UPDATE $wpdb->postmeta SET meta_key = 'accommodation_disabled_room_types' WHERE meta_key = 'accommodation_is_self_catered'";
		$wpdb->query($sql);
			
		if ($bookyourtravel_theme_globals->enable_accommodations()) {

			$bookyourtravel_accommodation_helper->create_accommodation_extra_tables();
			$vacancy_results = $bookyourtravel_accommodation_helper->list_accommodation_vacancies();
		
			if ( $vacancy_results != null && count($vacancy_results) > 0 && $vacancy_results['total'] > 0 ) {
				foreach ($vacancy_results['results'] as $vacancy_result) {
				
					$bookyourtravel_accommodation_helper->update_accommodation_vacancy($vacancy_result->Id, $vacancy_result->season_name, $vacancy_result->start_date, $vacancy_result->end_date, $vacancy_result->accommodation_id, $vacancy_result->room_type_id, $vacancy_result->room_count, $vacancy_result->price_per_day, $vacancy_result->price_per_day_child, $vacancy_result->weekend_price_per_day, $vacancy_result->weekend_price_per_day_child);
				
				}
			}
		}

		if ($bookyourtravel_theme_globals->enable_tours()) {

			$bookyourtravel_tour_helper->create_tour_extra_tables();
			$tour_results = $bookyourtravel_tour_helper->list_tours(0, -1, '', '', 0, array(), array(), array(), false, null, true, false);
		
			if ( count($tour_results) > 0 && $tour_results['total'] > 0 ) {
				foreach ($tour_results['results'] as $tour_result) {
					$location_id = (int)get_post_meta($tour_result->ID, 'tour_location_post_id', true);
					$locations = get_post_meta($tour_result->ID, 'locations', true);
					
					if ($location_id > 0 && !is_array($locations)) {
						update_post_meta($tour_result->ID, 'locations', array($location_id));
					}
				}				
			}
			
			$schedule_results = $bookyourtravel_tour_helper->list_tour_schedules();
		
			if ( $schedule_results != null && count($schedule_results) > 0 && $schedule_results['total'] > 0 ) {
				foreach ($schedule_results['results'] as $schedule_result) {
					$bookyourtravel_tour_helper->update_tour_schedule($schedule_result->Id, $schedule_result->season_name, $schedule_result->start_date, $schedule_result->duration_days, $schedule_result->tour_id, $schedule_result->price, $schedule_result->price_child, $schedule_result->max_people, $schedule_result->end_date);
				}
			}
		}
		
		if ($bookyourtravel_theme_globals->enable_cruises()) {

			$bookyourtravel_cruise_helper->create_cruise_extra_tables();
			$schedule_results = $bookyourtravel_cruise_helper->list_cruise_schedules();
		
			if ( $schedule_results != null && count($schedule_results) > 0 && $schedule_results['total'] > 0 ) {
				foreach ($schedule_results['results'] as $schedule_result) {
					$bookyourtravel_cruise_helper->update_cruise_schedule($schedule_result->Id, $schedule_result->season_name, $schedule_result->cruise_id, $schedule_result->cabin_type_id, $schedule_result->cabin_count, $schedule_result->start_date, $schedule_result->duration_days, $schedule_result->price, $schedule_result->price_child, $schedule_result->end_date);
				}
			}
		}
		
		if ($bookyourtravel_theme_globals->enable_car_rentals()) {
			$bookyourtravel_car_rental_helper->create_car_rental_extra_tables();
		}
		
		$force_recreate_tables = false;
	}
	
	function upgrade_bookyourtravel_db() {
	
		global $bookyourtravel_accommodation_helper, $bookyourtravel_cruise_helper, $bookyourtravel_tour_helper, $bookyourtravel_theme_globals;

		if ( isset($_REQUEST) ) {
		
			$nonce = wp_kses($_REQUEST['nonce'], array());
			
			if ( wp_verify_nonce( $nonce, 'optionsframework-options' ) ) {
			
				$this->force_upgrade_bookyourtravel_db();
				
				update_option( '_byt_needs_update', 0 );
				update_option( '_byt_version_before_update', BOOKYOURTRAVEL_VERSION );
				
			} else {
				echo 'oops!';
			}
		}
				
		// Always die in functions echoing ajax content
		die();
	}

	function settings_ajax_save_password() {
	
		if ( isset($_REQUEST) ) {
			$nonce = wp_kses($_REQUEST['nonce'], array());
			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {
				$user_id = intval(wp_kses($_REQUEST['userId'], array()));	
				$oldPassword = sanitize_text_field($_REQUEST['oldPassword']);
				$password = sanitize_text_field($_REQUEST['password']);
				
				$user = get_user_by( 'id', $user_id );
				if ( $user && wp_check_password( $oldPassword, $user->data->user_pass, $user->ID) )
				{
					// ok
					echo wp_update_user( array ( 'ID' => $user_id, 'user_pass' => $password ) ) ;
				}
			}
		}
		
		// Always die in functions echoing ajax content
		die();
	}

	function settings_ajax_save_email() {
		if ( isset($_REQUEST) ) {
			$nonce = wp_kses($_REQUEST['nonce'], array());
			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {
				$email = sanitize_text_field($_REQUEST['email']);
				$user_id = intval(wp_kses($_REQUEST['userId'], array()));	
				echo wp_update_user( array ( 'ID' => $user_id, 'user_email' => $email ) ) ;
			}
		}
		
		// Always die in functions echoing ajax content
		die();
	}

	function settings_ajax_save_last_name() {
		if ( isset($_REQUEST) ) {
			$nonce = wp_kses($_REQUEST['nonce'], array());
			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {
				$lastName = sanitize_text_field($_REQUEST['lastName']);
				$user_id = intval(wp_kses($_REQUEST['userId'], array()));	
				echo wp_update_user( array ( 'ID' => $user_id, 'last_name' => $lastName ) ) ;
			}
		}
		
		// Always die in functions echoing ajax content
		die();
	}

	function settings_ajax_save_first_name() {
		if ( isset($_REQUEST) ) {
			$nonce = wp_kses($_REQUEST['nonce'], array());
			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {
				$firstName = sanitize_text_field($_REQUEST['firstName']);
				$user_id = intval(wp_kses($_REQUEST['userId'], array()));	
				echo wp_update_user( array ( 'ID' => $user_id, 'first_name' => $firstName ) ) ;
			}
		}
		
		// Always die in functions echoing ajax content
		die();
	}
	
	function inquiry_ajax_request() {
	
		global $bookyourtravel_theme_globals;

		if ( isset($_REQUEST) ) {

			$enc_key = $bookyourtravel_theme_globals->get_enc_key();
			$add_captcha_to_forms = $bookyourtravel_theme_globals->add_captcha_to_forms();
		
			$user_id = intval(wp_kses($_REQUEST['userId'], array()));
			
			$c_val_s = intval(wp_kses($_REQUEST['c_val_s'], array()));
			$c_val_1_str = BookYourTravel_Theme_Utils::decrypt(wp_kses($_REQUEST['c_val_1'], array()), $enc_key);
			$c_val_2_str = BookYourTravel_Theme_Utils::decrypt(wp_kses($_REQUEST['c_val_2'], array()), $enc_key);
			$c_val_1 = intval($c_val_1_str);
			$c_val_2 = intval($c_val_2_str);
			
			$nonce = wp_kses($_REQUEST['nonce'], array());
			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {
			
				if ($add_captcha_to_forms && $c_val_s != ($c_val_1 + $c_val_2)) {
					
					echo 'captcha_error';
					die();
					
				} else {
				
					// nonce passed ok
					$postId = intval(wp_kses($_REQUEST['postId'], array()));			
					$post = get_post($postId);
					
					if ($post) {
					
						$post_type = $post->post_type;
						
						$contact_form_heading = esc_html__('Use the form below to contact us directly.', 'bookyourtravel');
						$inquiry_form_fields = $bookyourtravel_theme_globals->get_inquiry_form_fields();

						$admin_email = get_bloginfo('admin_email');
						$contact_email = get_post_meta($postId, $post->post_type . '_contact_email', true );
						$contact_emails = explode(';', $contact_email);
						if (empty($contact_email)) {
							$contact_emails = array($admin_email);	
						}
					
						$subject = esc_html__('New inquiry', 'bookyourtravel');	
						
						$message = esc_html__('The following inquiry has just arrived for %s', 'bookyourtravel');
						$message .= "\n";
						$message = sprintf($message, $post->post_title);
						
						$customer_email = '';
						
						foreach ($inquiry_form_fields as $form_field) {
							if ($form_field['hide'] !== '1') {
								$field_id = $form_field['id'];
								$field_value = sanitize_text_field($_REQUEST[$field_id]);
								
								if ($field_id == 'your_email') {
									$customer_email = $field_value;
								}
								
								$message .= $form_field['label'] . ' ' . $field_value . "\n";
							}
						}

						// $headers_array   = array();
						// $headers_array[] = "Content-Type: text/html\r\n";
						// $headers_array[] = "MIME-Version: 1.0";
						// $headers_array[] = "Content-type: text/plain; charset=utf-8";
						// $headers_array[] = "From: " . $admin_name . " <" . $admin_email . ">";
						// $headers_array[] = "Reply-To: " . $admin_name . " <" . $admin_email . ">";
						// $headers_array[] = "X-Mailer: PHP/".phpversion();
						
						// $headers = implode( "\r\n", $headers_array );
						
						$headers = "Content-Type: text/plain; charset=utf-8\r\n";
						$headers .= "From: " . $admin_email . " <" . $admin_email . ">\r\n";						
						if (!empty($customer_email)) {
							$headers .= "Reply-To: " . $customer_email . " <" . $customer_email . ">\r\n";
						} else {
							$headers .= "Reply-To: " . $admin_email . " <" . $admin_email . ">\r\n";						
						}
					
						foreach ($contact_emails as $email) {
							if (!empty($email)) {
								$ret = wp_mail(trim($email), $subject, $message, $headers, "");
								
								if (!$ret) {
									global $phpmailer;
									if (isset($phpmailer) && WP_DEBUG) {
										var_dump($phpmailer->ErrorInfo);
									}
								}
							}
						}
					}
				}
				
			} 
		}
		
		// Always die in functions echoing ajax content
		die();
	}
}

// store the instance in a variable to be retrieved later and call init
$bookyourtravel_theme_ajax = BookYourTravel_Theme_Ajax::get_instance();
$bookyourtravel_theme_ajax->init();