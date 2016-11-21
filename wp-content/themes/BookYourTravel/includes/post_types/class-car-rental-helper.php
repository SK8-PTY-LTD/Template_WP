<?php


class BookYourTravel_Car_Rental_Helper extends BookYourTravel_BaseSingleton {

	private $enable_car_rentals;
	private $car_rental_custom_meta_fields;
	private $car_rental_list_custom_meta_fields;
	private $car_rental_list_meta_box;	

	protected function __construct() {
	
		global $post, $bookyourtravel_theme_globals;
		
		$this->enable_car_rentals = $bookyourtravel_theme_globals->enable_car_rentals();	
		
        // our parent class might
        // contain shared code in its constructor
        parent::__construct();	
	}
	
    public function init() {

		add_action( 'bookyourtravel_initialize_post_types', array( $this, 'initialize_post_type' ), 0);	
	
		if ($this->enable_car_rentals) {	
			add_action( 'bookyourtravel_after_delete_car_rental', array( $this, 'after_delete_car_rental' ), 10, 1);
			add_action( 'bookyourtravel_save_car_rental', array( $this, 'save_car_rental' ), 10, 1);
			add_action( 'admin_init', array($this, 'remove_unnecessary_meta_boxes') );
			add_filter( 'manage_edit-car_rental_columns', array( $this, 'manage_edit_car_rental_columns'), 10, 1);	
			add_action( 'admin_init', array( $this, 'car_rental_admin_init' ) );
			add_action( 'bookyourtravel_initialize_ajax', array( $this, 'initialize_ajax' ), 0);
		}
	}
	
	function save_car_rental($post_id) {
		
		delete_post_meta_by_key('_location_car_rental_count');
		
	}	
	
	function after_delete_car_rental($post_id) {
		
		delete_post_meta_by_key('_location_car_rental_count');
		
	}
	
	function initialize_ajax() {
	
		add_action( 'wp_ajax_book_car_rental_ajax_request', array($this, 'book_car_rental_ajax_request' ) );
		add_action( 'wp_ajax_nopriv_book_car_rental_ajax_request', array($this, 'book_car_rental_ajax_request' ) );
		add_action( 'wp_ajax_car_rental_is_reservation_only_request', array($this, 'car_rental_is_reservation_only_request'));
		add_action( 'wp_ajax_nopriv_car_rental_is_reservation_only_request', array($this, 'car_rental_is_reservation_only_request'));
		add_action( 'wp_ajax_car_rental_booked_dates_request', array($this, 'car_rental_booked_dates_request' ));
		add_action( 'wp_ajax_nopriv_car_rental_booked_dates_request', array($this, 'car_rental_booked_dates_request' ));
		
		add_action( 'wp_ajax_car_rental_list_extra_items_ajax_request', array( $this, 'list_extra_items_ajax_request' ) );
		add_action( 'wp_ajax_nopriv_car_rental_list_extra_items_ajax_request', array( $this, 'list_extra_items_ajax_request' ) );
		
		add_action( 'wp_ajax_car_rental_process_booking_ajax_request', array( $this, 'process_booking_ajax_request' ) );
		add_action( 'wp_ajax_nopriv_car_rental_process_booking_ajax_request', array( $this, 'process_booking_ajax_request' ) );
		add_action( 'wp_ajax_car_rental_admin_get_fields_ajax_request', array( $this, 'admin_get_fields_ajax_request') );
	}
	
	function admin_get_fields_ajax_request() {
	
		if ( isset($_REQUEST) ) {
		
			$nonce = wp_kses($_REQUEST['nonce'], array());
			$car_rental_id = intval(wp_kses($_REQUEST['carRentalId'], array()));
			
			if (wp_verify_nonce( $nonce, 'bookyourtravel_nonce' )) {
				
				$car_rental_obj = new BookYourTravel_Car_Rental((int)$car_rental_id);

				$fields = new stdClass();
				
				$fields->price_per_day = $car_rental_obj->get_price_per_day();
				
				echo json_encode($fields);	
			}
		}
		
		// Always die in functions echoing ajax content
		die();	
	}
	
	function process_booking_ajax_request() {

		global $bookyourtravel_theme_globals, $bookyourtravel_theme_woocommerce, $bookyourtravel_extra_item_helper;

		$enc_key = $bookyourtravel_theme_globals->get_enc_key();
		$add_captcha_to_forms = $bookyourtravel_theme_globals->add_captcha_to_forms();
		$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
		$default_currency_symbol = $bookyourtravel_theme_globals->get_default_currency_symbol();
		$show_currency_symbol_after = $bookyourtravel_theme_globals->show_currency_symbol_after();
		$current_user = wp_get_current_user();
							
		if ( isset($_REQUEST) ) {
		
			$nonce = wp_kses($_REQUEST['nonce'], array());
								
			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {
			
				$booking_object = $this->retrieve_booking_values_from_request();
				
				$c_val_s = isset($_REQUEST['c_val_s']) ? intval(wp_kses($_REQUEST['c_val_s'], array())) : 0;
				$c_val_1 = isset($_REQUEST['c_val_1']) ? intval(BookYourTravel_Theme_Utils::decrypt(wp_kses($_REQUEST['c_val_1'], array()), $enc_key)) : 0;
				$c_val_2 = isset($_REQUEST['c_val_2']) ? intval(BookYourTravel_Theme_Utils::decrypt(wp_kses($_REQUEST['c_val_2'], array()), $enc_key)) : 0;
				
				if ($booking_object != null) {
				
					$car_rental_obj = new BookYourTravel_Car_Rental($booking_object->car_rental_id);
					if (isset($booking_object->room_type_id))
						$room_type_obj = new BookYourTravel_Room_Type($booking_object->room_type_id);
					
					if ($car_rental_obj != null) {
					
						if ($add_captcha_to_forms && $c_val_s != ($c_val_1 + $c_val_2)) {
							echo 'captcha_error';
							die();
						} else {
						
							$booking_object->Id = $this->create_car_rental_booking($current_user->ID, $booking_object);
							
							echo $booking_object->Id;

							$use_woocommerce_for_checkout = $bookyourtravel_theme_globals->use_woocommerce_for_checkout();
							$is_reservation_only = get_post_meta( $booking_object->car_rental_id, 'car_rental_is_reservation_only', true );

							if (!$use_woocommerce_for_checkout || !BookYourTravel_Theme_Utils::is_woocommerce_active() || $is_reservation_only) {
							
								// only send email if we are not proceeding to WooCommerce checkout or if woocommerce is not active at all.
								$admin_email = get_bloginfo('admin_email');
								$admin_name = get_bloginfo('name');
								
								$subject = esc_html__('New car rental booking', 'bookyourtravel');
							
								$message = esc_html__('New car rental booking: ', 'bookyourtravel');
								$message .= "\n\n";
								$message .= sprintf(esc_html__("Car Rental: %s", 'bookyourtravel'), $car_rental_obj->get_title()) . "\n\n";
								
								if ($room_type_obj) {
									$message .= sprintf(esc_html__("Room type: %s", 'bookyourtravel'), $room_type_obj->get_title()) . "\n\n";
								}

								$booking_form_fields = $bookyourtravel_theme_globals->get_booking_form_fields();
								$customer_email = '';								
								foreach ($booking_form_fields as $form_field) {
									
									$field_id = $form_field['id'];
									
									if (isset($_REQUEST[$field_id]) && (!isset($form_field['hide']) || $form_field['hide'] !== '1')) { 

										$field_value = sanitize_text_field($_REQUEST[$field_id]);
										if ($field_id == 'email') {
											$customer_email = $field_value;
										}										
										$field_label = $form_field['label'];
										
										$message .= $field_label . ': ' . $field_value . "\n\n";
									}
								}
								
								$message .= sprintf(esc_html__("Date from: %s", 'bookyourtravel'), $booking_object->date_from) . "\n\n";
								$message .= sprintf(esc_html__("Date to: %s", 'bookyourtravel'), $booking_object->date_to) . "\n\n";
								$message .= sprintf(esc_html__("Adults: %s", 'bookyourtravel'), $booking_object->adults) . "\n\n";
								$message .= sprintf(esc_html__("Children: %s", 'bookyourtravel'), $booking_object->children) . "\n\n";
								
								if ($booking_object->total_extra_items_price > 0) {
								
									$total_extra_items_price_string = '';
									if (!$show_currency_symbol_after) { 
										$total_extra_items_price_string = $default_currency_symbol . ' ' . number_format_i18n( $booking_object->total_extra_items_price, $price_decimal_places );
									} else {
										$total_extra_items_price_string = number_format_i18n( $booking_object->total_extra_items_price, $price_decimal_places ) . ' ' . $default_currency_symbol;
									}
									
									$total_extra_items_price_string = preg_replace("/&nbsp;/",' ',$total_extra_items_price_string);
								
									$message .= sprintf(esc_html__("Extra items total: %s", 'bookyourtravel'), $total_extra_items_price_string) . "\n\n";
								}
								
								if ($booking_object->total_car_rental_price > 0) {
								
									$total_car_rental_price_string = '';
									if (!$show_currency_symbol_after) { 
										$total_car_rental_price_string = $default_currency_symbol . ' ' . number_format_i18n( $booking_object->total_car_rental_price, $price_decimal_places );
									} else {
										$total_car_rental_price_string = number_format_i18n( $booking_object->total_car_rental_price, $price_decimal_places ) . ' ' . $default_currency_symbol;
									}
									
									$total_car_rental_price_string = preg_replace("/&nbsp;/",' ',$total_car_rental_price_string);
								
									$message .= sprintf(esc_html__("Reservation total: %s", 'bookyourtravel'), $total_car_rental_price_string) . "\n\n";
								}
								
								$total_price_string = '';
								if (!$show_currency_symbol_after) { 
									$total_price_string = $default_currency_symbol . ' ' . number_format_i18n( $booking_object->total_price, $price_decimal_places );
								} else {
									$total_price_string = number_format_i18n( $booking_object->total_price, $price_decimal_places ) . ' ' . $default_currency_symbol;
								}
							
								$total_price_string = preg_replace("/&nbsp;/",' ',$total_price_string);
								$message .= sprintf(esc_html__("Total price: %s", 'bookyourtravel'), $total_price_string) . "\n\n";
							
								$headers = "Content-Type: text/plain; charset=utf-8\r\n";
								$headers .= "From: " . $admin_name . " <" . $admin_email . ">\r\n";
								$headers .= "Reply-To: " . $admin_name . " <" . $admin_email . ">\r\n";	
							
								if (!empty($customer_email)) {
									$ret = wp_mail($customer_email, $subject, $message, $headers, "");
									if (!$ret) {
										global $phpmailer;
										if (isset($phpmailer) && WP_DEBUG) {
											var_dump($phpmailer->ErrorInfo);
										}
									}									
								}

								$contact_emails = trim(get_post_meta($booking_object->car_rental_id, 'car_rental_contact_email', true ));
								
								$emails_array = array();
								if (empty($contact_emails))
									$emails_array = array($admin_email);
								else 
									$emails_array = explode(';', $contact_emails);

								foreach ($emails_array as $email) {
									if (!empty($email)) {
										$ret = wp_mail($email, $subject, $message, $headers, "");			
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
			} 		
		}
		
		// Always die in functions echoing ajax content
		die();
	}	

	function retrieve_booking_values_from_request() {
	
		global $bookyourtravel_theme_globals, $bookyourtravel_extra_item_helper;
		
		$enable_extra_items = $bookyourtravel_theme_globals->enable_extra_items();
		
		$booking_object = null;
		
		if ( isset($_REQUEST) ) {

			$booking_object = new stdClass();
			
			$booking_object->Id = isset($_REQUEST['booking_id']) ? intval(wp_kses($_REQUEST['booking_id'], array())) : 0;
			
			$booking_object->total_price = 0;
			$booking_object->total_car_rental_price = 0;
			$booking_object->total_extra_items_price = 0;

			$booking_object->car_rental_id = isset($_REQUEST['car_rental_id']) ? intval(wp_kses($_REQUEST['car_rental_id'], array())) : 0;
			$booking_object->car_rental_id = BookYourTravel_Theme_Utils::get_default_language_post_id($booking_object->car_rental_id, 'car_rental');

			$booking_object->date_from = isset($_REQUEST['date_from']) ? date('Y-m-d', strtotime(sanitize_text_field($_REQUEST['date_from']))) : null;
			$booking_object->date_to = isset($_REQUEST['date_to']) ? date('Y-m-d', strtotime(sanitize_text_field($_REQUEST['date_to']))) : null;
			$booking_object->drop_off = isset($_REQUEST['drop_off']) ? sanitize_text_field($_REQUEST['drop_off']) : 0;
			
			$booking_object->total_car_rental_price = $this->calculate_total_car_rental_price($booking_object->car_rental_id, $booking_object->date_from, $booking_object->date_to, $booking_object->Id);
			$booking_object->total_price += $booking_object->total_car_rental_price;

			$booking_object->date_from = date('Y-m-d 12:00:00',strtotime($booking_object->date_from));
			$booking_object->date_to = date('Y-m-d 12:00:00',strtotime($booking_object->date_to));
			
			$booking_object->extra_items = null;
			
			if ($enable_extra_items && isset($_REQUEST['extra_items'])) {
				
				$booking_object->submitted_extra_items_array = (array)$_REQUEST['extra_items'];
								
				$booking_object->extra_items = array();
				
				$from_time = strtotime($booking_object->date_from);
				$to_time = strtotime($booking_object->date_to);
				$time_diff = $to_time - $from_time;
				$total_days = floor($time_diff/(60*60*24));
				$total_days = $total_days > 0 ? $total_days : 1;

				foreach ($booking_object->submitted_extra_items_array as $submitted_extra_item) {
					if (isset($submitted_extra_item['id']) && $submitted_extra_item['quantity']) {
						$extra_item_id = intval(sanitize_text_field($submitted_extra_item['id']));
						$quantity = intval(sanitize_text_field($submitted_extra_item['quantity']));
						$booking_object->extra_items[$extra_item_id] = $quantity;
						$booking_object->total_extra_items_price += $bookyourtravel_extra_item_helper->calculate_extra_item_total($extra_item_id, $quantity, 1, 0, $total_days);
					}
				}
				
				$booking_object->total_price += $booking_object->total_extra_items_price;
			}
			
			$booking_form_fields = $bookyourtravel_theme_globals->get_booking_form_fields();
			
			$booking_object->first_name = '';
			$booking_object->last_name = '';
			$booking_object->company = '';
			$booking_object->email = '';
			$booking_object->phone = '';
			$booking_object->address = '';
			$booking_object->address_2 = '';
			$booking_object->town = '';
			$booking_object->zip = '';
			$booking_object->state = '';
			$booking_object->country = '';
			$booking_object->special_requirements = '';
			$booking_object->other_fields = array();
						
			foreach ($booking_form_fields as $form_field) {
			
				$field_id = $form_field['id'];
				
				if (isset($_REQUEST[$field_id]) && (!isset($form_field['hide']) || $form_field['hide'] !== '1')) { 
					
					$field_value = sanitize_text_field($_REQUEST[$field_id]);

					switch ($field_id) {
						
						case 'first_name' 			: { $booking_object->first_name = $field_value; break; }
						case 'last_name' 			: { $booking_object->last_name = $field_value; break; }
						case 'company' 				: { $booking_object->company = $field_value; break; }						
						case 'email' 				: { $booking_object->email = $field_value; break; }
						case 'phone' 				: { $booking_object->phone = $field_value; break; }
						case 'address' 				: { $booking_object->address = $field_value; break; }
						case 'address_2' 			: { $booking_object->address_2 = $field_value; break; }
						case 'town' 				: { $booking_object->town = $field_value; break; }
						case 'zip' 					: { $booking_object->zip = $field_value; break; }
						case 'state' 				: { $booking_object->state = $field_value; break; }
						case 'country' 				: { $booking_object->country = $field_value; break; }
						case 'special_requirements' : { $booking_object->special_requirements = $field_value; break; }
						default : {
							$booking_object->other_fields[$field_id] = $field_value;
							break;
						}
					}
				}
			}
		}		
	
		return $booking_object;
	}
	
	function calculate_total_car_rental_price($car_rental_id, $date_from, $date_to, $current_booking_id = 0) {

		global $wpdb;
		
		$car_rental_id = BookYourTravel_Theme_Utils::get_default_language_post_id($car_rental_id, 'car_rental');
	
		$car_rental_obj = new BookYourTravel_Car_Rental($car_rental_id);
		$price_per_day = $car_rental_obj->get_price_per_day();
	
		// we are actually (in terms of db data) looking for date 1 day before the to date
		// e.g. when you look to book a room from 19.12. to 20.12 you will be staying 1 night, not 2
		$date_to = date('Y-m-d', strtotime($date_to.' -1 day'));
		
		$dates = BookYourTravel_Theme_Utils::get_dates_from_range($date_from, $date_to);

		$total_price = 0;
		
		foreach ($dates as $date) {
			$total_price += (float)$price_per_day;
		}
		
		return $total_price;
	}
	
	function list_extra_items_ajax_request() {	

		if ( isset($_REQUEST) ) {
		
			$nonce = wp_kses($_REQUEST['nonce'], array());
			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {
				
				global $bookyourtravel_extra_item_helper;
				
				$extra_items = $bookyourtravel_extra_item_helper->list_extra_items_by_post_type('car_rental');

				echo json_encode($extra_items);			
			}
		}
		
		// Always die in functions echoing ajax content
		die();	
	}
		
	function car_rental_booked_dates_request() {
	
		global $bookyourtravel_car_rental_helper, $bookyourtravel_theme_globals, $bookyourtravel_theme_woocommerce;
		
		if ( isset($_REQUEST) ) {
		
			$nonce = wp_kses($_REQUEST['nonce'], array());
			
			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {

				$car_rental_id = intval(wp_kses($_REQUEST['car_rental_id'], array()));	
				$month = intval(wp_kses($_REQUEST['month'], array()));	
				$year = intval(wp_kses($_REQUEST['year'], array()));
				
				$month_range = 3;
			
				if ($car_rental_id > 0) {
					
					$booked_dates = $bookyourtravel_car_rental_helper->car_rental_get_booked_days($car_rental_id, $month, $year, $month_range);
					echo json_encode($booked_dates);
				}
			}
		}
		
		die();
	}

	function car_rental_is_reservation_only_request() {
		if ( isset($_REQUEST) ) {
			$nonce = wp_kses($_REQUEST['nonce'], array());
			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {

				$car_rental_id = intval(wp_kses($_REQUEST['car_rental_id'], array()));	
				$is_reservation_only = get_post_meta( $car_rental_id, 'car_rental_is_reservation_only', true );
				$is_reservation_only = isset($is_reservation_only) ? (int)$is_reservation_only : 0;
				
				echo (int)$is_reservation_only;
			} else {
				echo 'failed nonce';
			}
		}
		
		die();
	}
		
	function remove_unnecessary_meta_boxes() {

		remove_meta_box('tagsdiv-car_rental_tag', 'car_rental', 'side');		
		remove_meta_box('tagsdiv-car_type', 'car_rental', 'side');		
	}
	
	function manage_edit_car_rental_columns($columns) {
	
		//unset($columns['taxonomy-car_type']);
		return $columns;
	}
	
	function car_rental_admin_init() {
	

		if ($this->enable_car_rentals) {
		
			$transmission_types = array();
			$transmission_types[] = array('value' => 'manual', 'label' => esc_html__('Manual transmission', 'bookyourtravel'));
			$transmission_types[] = array('value' => 'auto', 'label' => esc_html__('Auto transmission', 'bookyourtravel'));
					
			$this->car_rental_custom_meta_fields = array(
				array( // Post ID select box
					'label'	=> esc_html__('Is Featured', 'bookyourtravel'), // <label>
					'desc'	=> esc_html__('Show in lists where only featured items are shown.', 'bookyourtravel'), // description
					'id'	=> 'car_rental_is_featured', // field id and name
					'type'	=> 'checkbox', // type of field
				),
				array( // Post ID select box
					'label'	=> esc_html__('Hide inquiry form', 'bookyourtravel'), // <label>
					'desc'	=> esc_html__('Do you want to not show inquiry form for this car rental?', 'bookyourtravel'), // description
					'id'	=> 'car_rental_hide_inquiry_form', // field id and name
					'type'	=> 'checkbox', // type of field
				),
				array( 
					'label'	=> esc_html__('Is for reservation only?', 'bookyourtravel'), // <label>
					'desc'	=> esc_html__('If this option is checked, then this particular car rental will not be processed via WooCommerce even if WooCommerce is in use.', 'bookyourtravel'), // description
					'id'	=> 'car_rental_is_reservation_only', // field id and name
					'type'	=> 'checkbox', // type of field
				),				
				array( 
					'label'	=> esc_html__('Transmission type', 'bookyourtravel'), // <label>
					'desc'	=> esc_html__('What is the car\'s transmission type?', 'bookyourtravel'), // description
					'id'	=> 'car_rental_transmission_type', // field id and name
					'type'	=> 'select', // type of field
					'options' => $transmission_types
				),
				array( // Taxonomy Select box
					'label'	=> esc_html__('Car type', 'bookyourtravel'), // <label>
					// the description is created in the callback function with a link to Manage the taxonomy terms
					'id'	=> 'car_type', // field id and name, needs to be the exact name of the taxonomy
					'type'	=> 'tax_select' // type of field
				),
				array( // Post ID select box
					'label'	=> esc_html__('Location', 'bookyourtravel'), // <label>
					'desc'	=> '', // description
					'id'	=> 'car_rental_location_post_id', // field id and name
					'type'	=> 'post_select', // type of field
					'post_type' => array('location') // post types to display, options are prefixed with their post type
				),
				array(
					'label'	=> esc_html__('Price per day', 'bookyourtravel'),
					'desc'	=> esc_html__('What is the car\'s rental price per day?', 'bookyourtravel'),
					'id'	=> 'car_rental_price_per_day',
					'type'	=> 'text'
				),
				array(
					'label'	=> esc_html__('Contact email addresses', 'bookyourtravel'),
					'desc'	=> esc_html__('Override admin contact email address by specifying contact email addresses for this car rental. If specifying multiple email addresses, separate each address with a semi-colon ;', 'bookyourtravel'),
					'id'	=> 'car_rental_contact_email',
					'type'	=> 'text'
				),
				array(
					'label'	=> esc_html__('Number of available cars', 'bookyourtravel'),
					'desc'	=> esc_html__('What number of cars are available for rent (used for admin purposes to determine availability)?', 'bookyourtravel'),
					'id'	=> 'car_rental_number_of_cars',
					'type'	=> 'slider',
					'min'	=> '1',
					'max'	=> '50',
					'step'	=> '1'
				),
				array(
					'label'	=> esc_html__('Max count', 'bookyourtravel'),
					'desc'	=> esc_html__('How many people are allowed in the car?', 'bookyourtravel'),
					'id'	=> 'car_rental_max_count',
					'type'	=> 'slider',
					'min'	=> '1',
					'max'	=> '10',
					'step'	=> '1'
				),
				array(
					'label'	=> esc_html__('Minimum age', 'bookyourtravel'),
					'desc'	=> esc_html__('What is the minimum age of people in the car?', 'bookyourtravel'),
					'id'	=> 'car_rental_min_age',
					'type'	=> 'slider',
					'min'	=> '5',
					'max'	=> '100',
					'step'	=> '1'
				),
				array(
					'label'	=> esc_html__('Number of doors', 'bookyourtravel'),
					'desc'	=> esc_html__('What is the number of doors the car has?', 'bookyourtravel'),
					'id'	=> 'car_rental_number_of_doors',
					'type'	=> 'slider',
					'min'	=> '1',
					'max'	=> '10',
					'step'	=> '1'
				),
				array( 
					'label'	=> esc_html__('Unlimited mileage', 'bookyourtravel'), // <label>
					'desc'	=> esc_html__('Is there no restriction on mileage covered?', 'bookyourtravel'), // description
					'id'	=> 'car_rental_is_unlimited_mileage', // field id and name
					'type'	=> 'checkbox', // type of field
				),
				array( 
					'label'	=> esc_html__('Air-conditioning', 'bookyourtravel'), // <label>
					'desc'	=> esc_html__('Is there air-conditioning?', 'bookyourtravel'), // description
					'id'	=> 'car_rental_is_air_conditioned', // field id and name
					'type'	=> 'checkbox', // type of field
				),
				array( // Taxonomy Select box
					'label'	=> esc_html__('Car rental tag', 'bookyourtravel'), // <label>
					// the description is created in the callback function with a link to Manage the taxonomy terms
					'id'	=> 'car_rental_tag', // field id and name, needs to be the exact name of the taxonomy
					'type'	=> 'tax_checkboxes' // type of field
				),

				array( // Repeatable & Sortable Text inputs
					'label'	=> esc_html__('Gallery images', 'bookyourtravel'), // <label>
					'desc'	=> esc_html__('A collection of images to be used in slider/gallery on single page', 'bookyourtravel'), // description
					'id'	=> 'car_rental_images', // field id and name
					'type'	=> 'repeatable', // type of field
					'sanitizer' => array( // array of sanitizers with matching kets to next array
						'featured' => 'meta_box_santitize_boolean',
						'title' => 'sanitize_text_field',
						'desc' => 'wp_kses_data'
					),
					'repeatable_fields' => array ( // array of fields to be repeated
						array( // Image ID field
							'label'	=> esc_html__('Image', 'bookyourtravel'), // <label>
							'id'	=> 'image', // field id and name
							'type'	=> 'image' // type of field
						)
					)
				),
			);

			global $default_car_rental_extra_fields;

			$car_rental_extra_fields = of_get_option('car_rental_extra_fields');
			
			if (!is_array($car_rental_extra_fields) || count($car_rental_extra_fields) == 0)
				$car_rental_extra_fields = $default_car_rental_extra_fields;
				
			foreach ($car_rental_extra_fields as $car_rental_extra_field) {
				$field_is_hidden = isset($car_rental_extra_field['hide']) ? intval($car_rental_extra_field['hide']) : 0;
				
				if (!$field_is_hidden) {
					$extra_field = null;
					$field_label = isset($car_rental_extra_field['label']) ? $car_rental_extra_field['label'] : '';
					$field_id = isset($car_rental_extra_field['id']) ? $car_rental_extra_field['id'] : '';
					$field_type = isset($car_rental_extra_field['type']) ? $car_rental_extra_field['type'] :  '';

					if ($field_type == 'textarea')
						$field_type = 'editor';

					if (!empty($field_label) && !empty($field_id) && !empty($field_type)) {
						$extra_field = array(
							'label'	=> $field_label,
							'desc'	=> '',
							'id'	=> 'car_rental_' . $field_id,
							'type'	=> $field_type
						);
					}

					if ($extra_field) 
						$this->car_rental_custom_meta_fields[] = $extra_field;
				}
			}
			
			$sort_by_columns = array();
			$sort_by_columns[] = array('value' => 'title', 'label' => esc_html__('Car rental title', 'bookyourtravel'));
			$sort_by_columns[] = array('value' => 'ID', 'label' => esc_html__('Car rental ID', 'bookyourtravel'));
			$sort_by_columns[] = array('value' => 'date', 'label' => esc_html__('Publish date', 'bookyourtravel'));
			$sort_by_columns[] = array('value' => 'rand', 'label' => esc_html__('Random', 'bookyourtravel'));
			$sort_by_columns[] = array('value' => 'comment_count', 'label' => esc_html__('Comment count', 'bookyourtravel'));
			
			$this->car_rental_list_custom_meta_fields = array(
				array( // Taxonomy Select box
					'label'	=> esc_html__('Car type', 'bookyourtravel'), // <label>
					// the description is created in the callback function with a link to Manage the taxonomy terms
					'id'	=> 'car_type', // field id and name, needs to be the exact name of the taxonomy
					'type'	=> 'tax_select' // type of field
				),
				array( // Taxonomy Select box
					'label'	=> esc_html__('Location', 'bookyourtravel'), // <label>
					// the description is created in the callback function with a link to Manage the taxonomy terms
					'id'	=> 'car_rental_list_location_post_id', // field id and name
					'type'	=> 'post_select', // type of field
					'post_type' => array('location') // post types to display, options are prefixed with their post type
				),
				array( // Taxonomy Select box
					'label'	=> esc_html__('Car rental tags', 'bookyourtravel'), // <label>
					// the description is created in the callback function with a link to Manage the taxonomy terms
					'id'	=> 'car_rental_tag', // field id and name, needs to be the exact name of the taxonomy
					'type'	=> 'tax_checkboxes' // type of field
				),
				array( // Select box
					'label'	=> esc_html__('Sort by field', 'bookyourtravel'), // <label>
					// the description is created in the callback function with a link to Manage the taxonomy terms
					'id'	=> 'car_rental_list_sort_by', // field id and name, needs to be the exact name of the taxonomy
					'type'	=> 'select', // type of field
					'options' => $sort_by_columns
				),
				array( // Post ID select box
					'label'	=> esc_html__('Sort descending?', 'bookyourtravel'), // <label>
					'desc'	=> esc_html__('If checked, will sort car rentals in descending order', 'bookyourtravel'), // description
					'id'	=> 'car_rental_list_sort_descending', // field id and name
					'type'	=> 'checkbox', // type of field
				),
				array( // Post ID select box
					'label'	=> esc_html__('Show featured only?', 'bookyourtravel'), // <label>
					'desc'	=> esc_html__('If checked, will list featured car rentals only', 'bookyourtravel'), // description
					'id'	=> 'car_rental_list_show_featured_only', // field id and name
					'type'	=> 'checkbox', // type of field
				),				
			);
		
		}
	
		new custom_add_meta_box( 'car_rental_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->car_rental_custom_meta_fields, 'car_rental' );
		
		$this->car_rental_list_meta_box = new custom_add_meta_box( 'car_rental_list_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->car_rental_list_custom_meta_fields, 'page' );	
		remove_action( 'add_meta_boxes', array( $this->car_rental_list_meta_box, 'add_box' ) );
		add_action('add_meta_boxes', array($this, 'car_rental_list_add_meta_boxes'));
	}
	
	function car_rental_list_add_meta_boxes() {
		global $post;
		$template_file = get_post_meta($post->ID,'_wp_page_template',true);
		if ($template_file == 'page-car_rental-list.php') {
			add_meta_box( $this->car_rental_list_meta_box->id, $this->car_rental_list_meta_box->title, array( $this->car_rental_list_meta_box, 'meta_box_callback' ), 'page', 'normal', 'high' );
		}
	}
			
	function initialize_post_type() {
	
		global $bookyourtravel_theme_globals;	
		$this->enable_car_rentals = $bookyourtravel_theme_globals->enable_car_rentals();		
	
		if ($this->enable_car_rentals) {
			$this->register_car_rental_post_type();
			$this->register_car_rental_tag_taxonomy();
			$this->register_car_type_taxonomy();
			$this->create_car_rental_extra_tables();		
		}
	}
	
	function register_car_rental_tag_taxonomy() {
	
		$labels = array(
				'name'              => esc_html__( 'Car rental tags', 'bookyourtravel' ),
				'singular_name'     => esc_html__( 'Car rental tag', 'bookyourtravel' ),
				'search_items'      => esc_html__( 'Search Car rental tags', 'bookyourtravel' ),
				'all_items'         => esc_html__( 'All Car rental tags', 'bookyourtravel' ),
				'parent_item'                => null,
				'parent_item_colon'          => null,
				'edit_item'         => esc_html__( 'Edit Car rental tag', 'bookyourtravel' ),
				'update_item'       => esc_html__( 'Update Car rental tag', 'bookyourtravel' ),
				'add_new_item'      => esc_html__( 'Add New Car rental tag', 'bookyourtravel' ),
				'new_item_name'     => esc_html__( 'New Car rental tag Name', 'bookyourtravel' ),
				'separate_items_with_commas' => esc_html__( 'Separate car rental tags with commas', 'bookyourtravel' ),
				'add_or_remove_items'        => esc_html__( 'Add or remove car rental tags', 'bookyourtravel' ),
				'choose_from_most_used'      => esc_html__( 'Choose from the most used car rental tags', 'bookyourtravel' ),
				'not_found'                  => esc_html__( 'No car rental tags found.', 'bookyourtravel' ),
				'menu_name'         => esc_html__( 'Car rental tags', 'bookyourtravel' ),
			);
			
		$args = array(
				'hierarchical'      => false,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'update_count_callback' => '_update_post_term_count',
				'rewrite'           => false,
			);
			
		register_taxonomy( 'car_rental_tag', array( 'car_rental' ), $args );
	}	
		
	function register_car_rental_post_type() {
			
		global $bookyourtravel_theme_globals;
		$car_rentals_permalink_slug = $bookyourtravel_theme_globals->get_car_rentals_permalink_slug();
		
		$car_rental_list_page_id = $bookyourtravel_theme_globals->get_car_rental_list_page_id();
		
		if ($car_rental_list_page_id > 0) {

			add_rewrite_rule(
				"{$car_rentals_permalink_slug}$",
				"index.php?post_type=page&page_id={$car_rental_list_page_id}", 'top');
		
			add_rewrite_rule(
				"{$car_rentals_permalink_slug}/page/?([1-9][0-9]*)",
				"index.php?post_type=page&page_id={$car_rental_list_page_id}&paged=\$matches[1]", 'top');
		
		}
		
		add_rewrite_rule(
			"{$car_rentals_permalink_slug}/([^/]+)/page/?([1-9][0-9]*)",
			"index.php?post_type=car_rental&name=\$matches[1]&paged-byt=\$matches[2]", 'top');
			
		add_rewrite_tag('%paged-byt%', '([1-9][0-9]*)');		
		
		$labels = array(
			'name'                => esc_html__( 'Car rentals', 'bookyourtravel' ),
			'singular_name'       => esc_html__( 'Car rental', 'bookyourtravel' ),
			'menu_name'           => esc_html__( 'Car rentals', 'bookyourtravel' ),
			'all_items'           => esc_html__( 'All Car rentals', 'bookyourtravel' ),
			'view_item'           => esc_html__( 'View Car rental', 'bookyourtravel' ),
			'add_new_item'        => esc_html__( 'Add New Car rental', 'bookyourtravel' ),
			'add_new'             => esc_html__( 'New Car rental', 'bookyourtravel' ),
			'edit_item'           => esc_html__( 'Edit Car rental', 'bookyourtravel' ),
			'update_item'         => esc_html__( 'Update Car rental', 'bookyourtravel' ),
			'search_items'        => esc_html__( 'Search Car rentals', 'bookyourtravel' ),
			'not_found'           => esc_html__( 'No Car rentals found', 'bookyourtravel' ),
			'not_found_in_trash'  => esc_html__( 'No Car rentals found in Trash', 'bookyourtravel' ),
		);
		$args = array(
			'label'               => esc_html__( 'Car rental', 'bookyourtravel' ),
			'description'         => esc_html__( 'Car rental information pages', 'bookyourtravel' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'author' ),
			'taxonomies'          => array( ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
			'rewrite' =>  array('slug' => $car_rentals_permalink_slug),
		);
		
		register_post_type( 'car_rental', $args );	
	}

	function register_car_type_taxonomy() {

		$labels = array(
				'name'              => esc_html__( 'Car types', 'bookyourtravel' ),
				'singular_name'     => esc_html__( 'Car type', 'bookyourtravel' ),
				'search_items'      => esc_html__( 'Search Car types', 'bookyourtravel' ),
				'all_items'         => esc_html__( 'All Car types', 'bookyourtravel' ),
				'parent_item'                => null,
				'parent_item_colon'          => null,
				'edit_item'         => esc_html__( 'Edit Car type', 'bookyourtravel' ),
				'update_item'       => esc_html__( 'Update Car type', 'bookyourtravel' ),
				'add_new_item'      => esc_html__( 'Add New Car type', 'bookyourtravel' ),
				'new_item_name'     => esc_html__( 'New Car type Name', 'bookyourtravel' ),
				'separate_items_with_commas' => esc_html__( 'Separate car types with commas', 'bookyourtravel' ),
				'add_or_remove_items'        => esc_html__( 'Add or remove car types', 'bookyourtravel' ),
				'choose_from_most_used'      => esc_html__( 'Choose from the most used car types', 'bookyourtravel' ),
				'not_found'                  => esc_html__( 'No car types found.', 'bookyourtravel' ),
				'menu_name'         => esc_html__( 'Car types', 'bookyourtravel' ),
			);
			
		$args = array(
				'hierarchical'      => false,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => false,
				'update_count_callback' => '_update_post_term_count',
				'rewrite'           => false,
			);
		
		register_taxonomy( 'car_type', 'car_rental', $args );
	}

	function create_car_rental_extra_tables() {

		global $wpdb, $bookyourtravel_installed_version, $force_recreate_tables;

		if ($bookyourtravel_installed_version != BOOKYOURTRAVEL_VERSION || $force_recreate_tables) {
			
			// we do not execute sql directly
			// we are calling dbDelta which cant migrate database
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');		
			
			$table_name = BOOKYOURTRAVEL_CAR_RENTAL_BOOKINGS_TABLE;
			$sql = "CREATE TABLE " . $table_name . " (
						Id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
						car_rental_id bigint(20) NOT NULL,
						first_name varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
						last_name varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
						company varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL,
						email varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
						phone varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL,
						address varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL,
						address_2 varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL,
						town varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL,
						zip varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL,
						state varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL,
						country varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL,
						special_requirements text CHARACTER SET utf8 COLLATE utf8_bin NULL,
						other_fields text CHARACTER SET utf8 COLLATE utf8_bin NULL,
						extra_items text CHARACTER SET utf8 COLLATE utf8_bin NULL,
						drop_off bigint(10) NOT NULL DEFAULT 0,
						total_car_rental_price decimal(16,2) NOT NULL DEFAULT '0.00',
						total_extra_items_price decimal(16,2) NOT NULL DEFAULT '0.00',
						total_price decimal(16, 2) NOT NULL,
						user_id bigint(10) NOT NULL DEFAULT 0,
						created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
						woo_order_id bigint(20) NULL,
						cart_key VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '' NOT NULL,
						woo_status varchar(255) NULL,
						PRIMARY KEY  (Id)
					);";

			dbDelta($sql);
			
			$table_name = BOOKYOURTRAVEL_CAR_RENTAL_BOOKING_DAYS_TABLE;
			$sql = "CREATE TABLE " . $table_name . " (
						Id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
						car_rental_booking_id bigint(20) NOT NULL,
						booking_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
						created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
						PRIMARY KEY  (Id)
					);";
			
			dbDelta($sql);
			global $EZSQL_ERROR;
			$EZSQL_ERROR = array();
			
		}
	}
		
	function car_rentals_search_fields( $fields, &$wp_query ) {

		global $wpdb, $bookyourtravel_multi_language_count, $bookyourtravel_theme_globals;

		if ( isset($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] == 'car_rental' ) {
			
			$search_only_available = false;
			if (isset($wp_query->query_vars['search_only_available']))
				$search_only_available = $wp_query->get('search_only_available');
			
			if ($search_only_available || isset($wp_query->query_vars['byt_date_from']) || isset($wp_query->query_vars['byt_date_from'])) {
			
				$date_from = null;
				if ( isset($wp_query->query_vars['byt_date_from']) )
					$date_from = date('Y-m-d', strtotime($wp_query->get('byt_date_from')));
				
				$date_to = null;		
				if ( isset($wp_query->query_vars['byt_date_to']) )
					$date_to = date('Y-m-d', strtotime($wp_query->get('byt_date_to') . ' -1 day'));
				
				if (isset($date_from) && $date_from == $date_to)
					$date_to = date('Y-m-d', strtotime($wp_query->get('byt_date_from') . ' +7 day'));
				
				if (isset($date_from) || isset($date_to)) {
				
					$fields .= ", (
									SELECT IFNULL(SUM(car_rentals_meta_number_of_cars.meta_value+0), 0) cars_available FROM $wpdb->postmeta car_rentals_meta_number_of_cars
									WHERE car_rentals_meta_number_of_cars.post_id = {$wpdb->posts}.ID AND car_rentals_meta_number_of_cars.meta_key='car_rental_number_of_cars' ";
					$fields .= " ) cars_available ";
					$fields .= ",
								(
									SELECT COUNT(DISTINCT car_rental_booking_id) cars_booked
									FROM " . BOOKYOURTRAVEL_CAR_RENTAL_BOOKING_DAYS_TABLE . " booking_days_table 
									INNER JOIN " . BOOKYOURTRAVEL_CAR_RENTAL_BOOKINGS_TABLE . " booking_table ON booking_days_table.car_rental_booking_id = booking_table.Id 
									WHERE ";
									
					if(defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('car_rental') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
						$fields .= " booking_table.car_rental_id = translations_default.element_id ";
					} else {
						$fields .= " booking_table.car_rental_id = {$wpdb->posts}.ID ";
					}

					$fields .= $wpdb->prepare(" AND booking_days_table.booking_date BETWEEN %s AND %s ", $date_from, $date_to);
									
					if ($date_from != null && $date_to != null) {
						$fields .= $wpdb->prepare(" AND (booking_days_table.booking_date BETWEEN %s AND %s) ", $date_from, $date_to);
					} else if ($date_from != null) {
						$fields .= $wpdb->prepare(" AND booking_days_table.booking_date > %s ", $date_from);
					} else if ($date_to != null) {
						$fields .= $wpdb->prepare(" AND booking_days_table.booking_date < %s ", $date_to);
					}
					
					$fields .= " ) cars_booked ";
				}
			}
				
			if (!is_admin()) {
				$fields .= ", (SELECT IFNULL(price_meta2.meta_value, 0) FROM {$wpdb->postmeta} price_meta2 WHERE price_meta2.post_id={$wpdb->posts}.ID AND price_meta2.meta_key='car_rental_price_per_day' LIMIT 1) car_rental_price ";
			}
				
		}

		return $fields;
	}

	function car_rentals_search_where( $where, &$wp_query ) {
		
		global $wpdb;
		
		if ( isset($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] == 'car_rental' ) {
			if ( isset($wp_query->query_vars['s']) && !empty($wp_query->query_vars['s'])  && isset($wp_query->query_vars['byt_location_ids']) && isset($wp_query->query_vars['s']) ) {
				$needed_where_part = '';
				$where_array = explode('AND', $where);
				foreach ($where_array as $where_part) {
					if (strpos($where_part,"meta_key = 'car_rental_location_post_id'") !== false) {
						$needed_where_part = $where_part;
						break;
					}
				}
				
				if (!empty($needed_where_part)) {
					$prefix = str_replace("meta_key = 'car_rental_location_post_id'","",$needed_where_part);
					$prefix = str_replace(")", "", $prefix);
					$prefix = str_replace("(", "", $prefix);
					$prefix = trim($prefix);

					$location_ids = $wp_query->query_vars['byt_location_ids'];
					$location_ids_str = "'".implode("','", $location_ids)."'";				
					$location_search_param_part = "{$prefix}meta_key = 'car_rental_location_post_id' AND CAST({$prefix}meta_value AS CHAR) IN ($location_ids_str)";							
				
					$where = str_replace($location_search_param_part, "1=1", $where);
					
					$post_content_part = "OR ($wpdb->posts.post_content LIKE '%" . $wp_query->get('s') . "%')";
					$where = str_replace($post_content_part, $post_content_part . " OR ($location_search_param_part) ", $where);				
				}
			}
		}
		
		return $where;
	}

	function car_rentals_search_groupby( $groupby, &$wp_query ) {

		global $wpdb;
		
		if (empty($groupby))
			$groupby = " {$wpdb->posts}.ID ";
		
		if ( isset($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] == 'car_rental' ) {		
			
			$date_from = null;
			if ( isset($wp_query->query_vars['byt_date_from']) )
				$date_from = date('Y-m-d', strtotime($wp_query->get('byt_date_from')));
			
			$date_to = null;		
			if ( isset($wp_query->query_vars['byt_date_to']) )
				$date_to = date('Y-m-d', strtotime($wp_query->get('byt_date_to') . ' -1 day'));
			
			if (isset($date_from) && $date_from == $date_to)
				$date_to = date('Y-m-d', strtotime($wp_query->get('byt_date_from') . ' +7 day'));
			
			$search_only_available = false;
			if (isset($wp_query->query_vars['search_only_available']))
				$search_only_available = $wp_query->get('search_only_available');
			
			$groupby .= ' HAVING 1=1 ';
			
			if ($search_only_available && (isset($date_from) || isset($date_to))) {
					
				$groupby .= ' AND cars_available > cars_booked ';
			
			}			

			if (isset($wp_query->query_vars['prices'])) {
			
				$prices = (array)$wp_query->query_vars['prices'];				
				if (count($prices) > 0) {
				
					$price_range_bottom = $wp_query->query_vars['price_range_bottom'];
					$price_range_increment = $wp_query->query_vars['price_range_increment'];
					$price_range_count = $wp_query->query_vars['price_range_count'];
					
					$bottom = 0;
					$top = 0;
					
					$groupby .= ' AND ( 1!=1 ';
					for ( $i = 0; $i < $price_range_count; $i++ ) { 
						$bottom = ($i * $price_range_increment) + $price_range_bottom;
						$top = ( ( $i+1 ) * $price_range_increment ) + $price_range_bottom - 1;	

						if ( in_array( $i + 1, $prices ) ) {
							if ( $i < ( ($price_range_count - 1) ) ) {
									$groupby .= " OR (car_rental_price >= $bottom AND car_rental_price <= $top ) ";
							} else {
								$groupby .= " OR (car_rental_price >= $bottom ) ";
							}
						}
					}
					
					$groupby .= ")";
				}
			}
		}
		
		return $groupby;
	}
	
	function car_rentals_search_join($join) {
	
		global $wp_query, $wpdb, $bookyourtravel_multi_language_count, $bookyourtravel_theme_globals;
		
		if(defined('ICL_LANGUAGE_CODE')  && $bookyourtravel_theme_globals->is_translatable('car_rental') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$join .= " INNER JOIN " . $wpdb->prefix . "icl_translations translations_default ON translations_default.element_type = 'post_car_rental' AND translations_default.language_code='" . BookYourTravel_Theme_Utils::get_default_language() . "' AND translations_default.trid = t.trid ";
		}
		
		return $join;
	}
		
	function list_car_rentals_count ( $paged = 0, $per_page = -1, $orderby = '', $order = '', $location_id = 0, $car_types_array = array(), $car_rental_tags_array = array(), $search_args = array(), $featured_only = false, $author_id = null, $include_private = false ) {
		$results = $this->list_car_rentals ($paged, $per_page, $orderby, $order, $location_id, $car_types_array, $car_rental_tags_array, $search_args, $featured_only, $author_id, $include_private, true);
		return $results['total'];
	}
		
	function list_car_rentals( $paged = 0, $per_page = -1, $orderby = '', $order = '', $location_id = 0, $car_types_array = array(), $car_rental_tags_array = array(), $search_args = array(), $featured_only = false, $author_id = null, $include_private = false, $count_only = false ) {

		global $bookyourtravel_theme_globals;
		$location_ids = array();
		
		if ($location_id > 0) {
			$location_ids[] = $location_id;
			$location_descendants = BookYourTravel_Theme_Utils::get_post_descendants($location_id, 'location');
			foreach ($location_descendants as $location) {
				$location_ids[] = $location->ID;
			}
		}
		
		if (isset($search_args['keyword']) && strlen($search_args['keyword']) > 0) {
			$args = array(
						's' => $search_args['keyword'],
						'post_type' => 'location',
						'posts_per_page' => -1, 
						'post_status' => 'publish',
						'suppress_filters' => false
					);
			
			$location_posts = get_posts($args);
			foreach ($location_posts as $location) {
				$location_ids[] = $location->ID;		
			}

			$descendant_location_ids = array();		
			foreach ($location_ids as $temp_location_id) {
				$location_descendants = BookYourTravel_Theme_Utils::get_post_descendants($temp_location_id, 'location');
				foreach ($location_descendants as $location) {
					$descendant_location_ids[] = $location->ID;
				}
			}
			$location_ids = array_merge($descendant_location_ids,$location_ids);
		}
			
		$args = array(
			'post_type'         => 'car_rental',
			'post_status'       => array('publish'),
			'posts_per_page'    => $per_page,
			'paged' 			=> $paged,
			'orderby'           => $orderby,
			'suppress_filters' 	=> false,
			'order'				=> $order,
			'meta_query'        => array('relation' => 'AND')
		);	
		
		if ($orderby == 'price') {
			$args['meta_key'] = 'car_rental_price_per_day';
			$args['orderby'] = 'meta_value_num';
		}
		
		if (isset($search_args['keyword']) && strlen($search_args['keyword']) > 0) {
			$args['s'] = $search_args['keyword'];
		}
		
		if ($include_private) {
			$args['post_status'][] = 'draft';
			$args['post_status'][] = 'private';
		}
		
		if (isset($featured_only) && $featured_only) {
			$args['meta_query'][] = array(
				'key'       => 'car_rental_is_featured',
				'value'     => 1,
				'compare'   => '=',
				'type' => 'numeric'
			);
		}

		if (isset($author_id)) {
			$author_id = intval($author_id);
			if ($author_id > 0) {
				$args['author'] = $author_id;
			}
		}

		if (count($location_ids) > 0) {
			$args['meta_query'][] = array(
				'key'       => 'car_rental_location_post_id',
				'value'     => $location_ids,
				'compare'   => 'IN'
			);
			$args['byt_location_ids'] = $location_ids;
		}
		
		if (!empty($car_types_array)) {
			$args['tax_query'][] = 	array(
					'taxonomy' => 'car_type',
					'field' => 'id',
					'terms' => $car_types_array,
					'operator'=> 'IN'
			);
		}
		
		if (!empty($car_rental_tags_array)) {
			$args['tax_query'][] = 	array(
					'taxonomy' => 'car_rental_tag',
					'field' => 'id',
					'terms' => $car_rental_tags_array,
					'operator'=> 'IN'
			);
		}
		
		$search_only_available = false;
		if ( isset($search_args['search_only_available'])) {				
			$search_only_available = $search_args['search_only_available'];
		}

		if ( isset($search_args['prices']) ) {
			$args['prices'] = $search_args['prices'];
			$args['price_range_bottom'] = $bookyourtravel_theme_globals->get_price_range_bottom();
			$args['price_range_increment'] = $bookyourtravel_theme_globals->get_price_range_increment();
			$args['price_range_count'] = $bookyourtravel_theme_globals->get_price_range_count();
		}	
		
		if ( isset($search_args['date_from']) )
			$args['byt_date_from'] = $search_args['date_from'];
		if ( isset($search_args['date_to']) )
			$args['byt_date_to'] =  $search_args['date_to'];
			
		$args['search_only_available'] = $search_only_available;
				
		add_filter('posts_where', array($this, 'car_rentals_search_where'), 10, 2);				
		add_filter('posts_fields', array($this, 'car_rentals_search_fields'), 10, 2 );
		add_filter('posts_groupby', array($this, 'car_rentals_search_groupby'), 10, 2 );
		add_filter('posts_join', array($this, 'car_rentals_search_join'), 10, 2 );
		
		$posts_query = new WP_Query($args);
		
		// echo $posts_query->request;
		
		if ($count_only) {
			$results = array(
				'total' => $posts_query->found_posts,
				'results' => null
			);	
		} else {
			$results = array();
			
			if ($posts_query->have_posts() ) {
				while ( $posts_query->have_posts() ) {
					global $post;
					$posts_query->the_post(); 
					$results[] = $post;
				}
			}
		
			$results = array(
				'total' => $posts_query->found_posts,
				'results' => $results
			);
		}
		
		wp_reset_postdata();
		
		remove_filter('posts_where', array($this, 'car_rentals_search_where'));			
		remove_filter('posts_fields', array($this, 'car_rentals_search_fields' ));
		remove_filter('posts_groupby', array($this, 'car_rentals_search_groupby' ));
		remove_filter('posts_join', array($this, 'car_rentals_search_join') );
		
		return $results;
	}

	function create_car_rental_booking($user_id, $booking_object) {

		global $wpdb;

		$errors = array();

		$sql = "INSERT INTO " . BOOKYOURTRAVEL_CAR_RENTAL_BOOKINGS_TABLE . "
				(user_id, car_rental_id, drop_off, first_name, last_name, company, email, phone, address, address_2, town, zip, state, country, special_requirements, other_fields, extra_items, total_car_rental_price, total_extra_items_price, total_price)
				VALUES 
				(%d, %d, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %f, %f, %f);";

		$result = $wpdb->query($wpdb->prepare($sql, $user_id, $booking_object->car_rental_id, $booking_object->drop_off, $booking_object->first_name, $booking_object->last_name, $booking_object->company, $booking_object->email, $booking_object->phone, $booking_object->address, $booking_object->address_2, $booking_object->town, $booking_object->zip,  $booking_object->state, $booking_object->country, $booking_object->special_requirements, serialize($booking_object->other_fields), serialize($booking_object->extra_items), $booking_object->total_car_rental_price, $booking_object->total_extra_items_price, $booking_object->total_price));

		if (is_wp_error($result))
			$errors[] = $result;

		$booking_object->Id = $wpdb->insert_id;

		$effective_date_to = date('Y-m-d', strtotime("-1 days", strtotime($booking_object->date_to)));			
		// $effective_date_to = date('Y-m-d', strtotime($booking_object->date_to));
		
		$dates = BookYourTravel_Theme_Utils::get_dates_from_range($booking_object->date_from, $effective_date_to);
		
		foreach ($dates as $date) {
		
			$this->insert_booking_day($booking_object->Id, $date);
		}
			
		return $booking_object->Id;
	}
	
	function insert_booking_day($booking_id, $day) {
	
		global $wpdb;
		
		$sql = "INSERT INTO " . BOOKYOURTRAVEL_CAR_RENTAL_BOOKING_DAYS_TABLE . "
				(car_rental_booking_id, booking_date)
				VALUES
				(%d, %s);";
				
		$wpdb->query($wpdb->prepare($sql, $booking_id, $day));			
	}
	
	function clear_booking_days($booking_id) {

		global $wpdb;
		
		$sql = "DELETE FROM " . BOOKYOURTRAVEL_CAR_RENTAL_BOOKING_DAYS_TABLE . " WHERE car_rental_booking_id = %d";
				
		$wpdb->query($wpdb->prepare($sql, $booking_id));
	}
	

	function update_car_rental_booking($booking_id, $booking_object) {

		global $wpdb;
		
		$result = 0;
		
		$sql = "UPDATE " . BOOKYOURTRAVEL_CAR_RENTAL_BOOKINGS_TABLE . " SET ";
				
		$field_sql = '';
		
		foreach ($booking_object as $field_key => $field_value) {
			
			switch ($field_key) {
			
				case 'car_rental' 			: $field_sql .= $wpdb->prepare("car_rental = %d, ", $field_value); break;
				case 'drop_off' 					: $field_sql .= $wpdb->prepare("drop_off = %d, ", $field_value); break;
				case 'user_id' 						: $field_sql .= $wpdb->prepare("user_id = %d, ", $field_value); break;
				case 'first_name' 					: $field_sql .= $wpdb->prepare("first_name = %s, ", $field_value); break;
				case 'last_name' 					: $field_sql .= $wpdb->prepare("last_name = %s, ", $field_value); break;
				case 'company' 						: $field_sql .= $wpdb->prepare("company = %s, ", $field_value); break;				
				case 'email' 						: $field_sql .= $wpdb->prepare("email = %s, ", $field_value); break;
				case 'phone' 						: $field_sql .= $wpdb->prepare("phone = %s, ", $field_value); break;
				case 'address' 						: $field_sql .= $wpdb->prepare("address = %s, ", $field_value); break;
				case 'address_2' 					: $field_sql .= $wpdb->prepare("address_2 = %s, ", $field_value); break;				
				case 'town' 						: $field_sql .= $wpdb->prepare("town = %s, ", $field_value); break;
				case 'zip' 							: $field_sql .= $wpdb->prepare("zip = %s, ", $field_value); break;
				case 'state' 						: $field_sql .= $wpdb->prepare("state = %s, ", $field_value); break;				
				case 'country' 						: $field_sql .= $wpdb->prepare("country = %s, ", $field_value); break;
				case 'special_requirements' 		: $field_sql .= $wpdb->prepare("special_requirements = %s, ", $field_value); break;
				case 'other_fields' 				: $field_sql .= $wpdb->prepare("other_fields = %s, ", serialize($field_value)); break;
				case 'extra_items' 					: $field_sql .= $wpdb->prepare("extra_items = %s, ", serialize($field_value)); break;
				case 'total_car_rental_price' 		: $field_sql .= $wpdb->prepare("total_car_rental_price = %f, ", $field_value); break;
				case 'total_extra_items_price' 		: $field_sql .= $wpdb->prepare("total_extra_items_price = %f, ", $field_value); break;
				case 'total_price' 					: $field_sql .= $wpdb->prepare("total_price = %f, ", $field_value); break;
				case 'woo_order_id' 				: $field_sql .= $wpdb->prepare("woo_order_id = %d, ", $field_value); break;
				case 'cart_key' 					: $field_sql .= $wpdb->prepare("cart_key = %s, ", $field_value); break;
				case 'woo_status' 					: $field_sql .= $wpdb->prepare("woo_status = %s, ", $field_value); break;
				default : break;
			}
		}
		
		if (!empty($field_sql)) {
		
			$field_sql = rtrim($field_sql, ", ");
		
			$sql .= $field_sql;
		
			$sql .= $wpdb->prepare(" WHERE Id = %d;", $booking_id);
			
			$result = $wpdb->query($sql);
			
		}
		
		if (isset($booking_object->date_from) && isset($booking_object->date_to)) {

			$this->clear_booking_days($booking_id);
		
			$effective_date_to = date('Y-m-d', strtotime($booking_object->date_to));
			
			$dates = BookYourTravel_Theme_Utils::get_dates_from_range($booking_object->date_from, $effective_date_to);
			
			foreach ($dates as $date) {
			
				$this->insert_booking_day($booking_id, $date);
			}
		
		}
		
		return $result;
	}
	
	function car_rental_get_booked_days($car_rental_id, $month, $year, $month_range = 3) {

		global $wpdb, $bookyourtravel_theme_globals;

		$car_rental_id = BookYourTravel_Theme_Utils::get_default_language_post_id($car_rental_id, 'car_rental');
		
		$car_rental_obj = new BookYourTravel_Car_Rental($car_rental_id);
		$car_rental_is_reservation_only = $car_rental_obj->get_is_reservation_only();		
		
		$sql = "	SELECT DISTINCT DATE(booking_date) booking_date, (car_rentals_meta_number_of_cars.meta_value+0) number_of_cars
					FROM " . BOOKYOURTRAVEL_CAR_RENTAL_BOOKING_DAYS_TABLE . " days
					INNER JOIN " . BOOKYOURTRAVEL_CAR_RENTAL_BOOKINGS_TABLE . " bookings ON bookings.Id = days.car_rental_booking_id 
					INNER JOIN $wpdb->postmeta car_rentals_meta_number_of_cars ON bookings.car_rental_id=car_rentals_meta_number_of_cars.post_id AND car_rentals_meta_number_of_cars.meta_key='car_rental_number_of_cars' 
					WHERE bookings.car_rental_id=%d AND booking_date >= %s AND booking_date <= %s ";
				
		if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout() && !$car_rental_is_reservation_only) {
			
			$completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
			if (!empty($completed_statuses)) {
				$sql .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses . ")";
			}
		}			
					
		$sql .= "	GROUP BY booking_date
					HAVING COUNT(DISTINCT car_rental_booking_id) >= number_of_cars";

		$today = date('Y-m-d H:i:s');
		
		$end_date = sprintf("%d-%d-%d", $year, $month, 1);
		$end_date = date('Y-m-d', strtotime(sprintf("+%d months", $month_range), strtotime($end_date)));
		$end_date = date("Y-m-t", strtotime($end_date)); // last day of end date month
		$end_date = date('Y-m-d', strtotime(sprintf("+%d days", 1), strtotime($end_date)));			
		
		$sql = $wpdb->prepare($sql, $car_rental_id, $today, $end_date);
		
		return $wpdb->get_results($sql);
	}

	function list_car_rental_bookings($search_term = null, $orderby = 'Id', $order = 'ASC', $paged = null, $per_page = 0, $user_id = 0, $author_id = null, $car_rental_id = null ) {

		global $wpdb, $bookyourtravel_multi_language_count, $bookyourtravel_theme_globals;

		$table_name = BOOKYOURTRAVEL_CAR_RENTAL_BOOKINGS_TABLE;
		$sql = "SELECT DISTINCT bookings.*, car_rentals.post_title car_rental_name,
				(
					SELECT MIN(booking_date) FROM " . BOOKYOURTRAVEL_CAR_RENTAL_BOOKING_DAYS_TABLE . " v2 
					WHERE v2.car_rental_booking_id = bookings.Id 
				) from_day,
				(
					SELECT DATE_ADD(MAX(booking_date), INTERVAL +1 DAY) FROM " . BOOKYOURTRAVEL_CAR_RENTAL_BOOKING_DAYS_TABLE . " v3 
					WHERE v3.car_rental_booking_id = bookings.Id 
				) to_day, locations.post_title pick_up_title, locations_2.post_title drop_off_title
				FROM " . $table_name . " bookings ";

		if(defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('car_rental') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$sql .= " INNER JOIN " . $wpdb->prefix . "icl_translations translations_default ON translations_default.element_type = 'post_car_rental' AND translations_default.language_code='" . BookYourTravel_Theme_Utils::get_default_language() . "' AND translations_default.element_id = bookings.car_rental_id ";			
			$sql .= " INNER JOIN " . $wpdb->prefix . "icl_translations translations ON translations.element_type = 'post_car_rental' AND translations.language_code='" . ICL_LANGUAGE_CODE . "' AND translations.trid = translations_default.trid ";
		}
		
		$sql .= " INNER JOIN $wpdb->posts car_rentals ON ";
		if(defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('car_rental') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$sql .= " car_rentals.ID = translations.element_id ";
		} else {
			$sql .= " car_rentals.ID = bookings.car_rental_id ";
		}	
							
		$sql .= "LEFT JOIN $wpdb->postmeta car_rental_meta_location ON car_rentals.ID=car_rental_meta_location.post_id AND car_rental_meta_location.meta_key='car_rental_location_post_id'
				LEFT JOIN $wpdb->posts locations ON locations.ID = car_rental_meta_location.meta_value+0 AND locations.post_status = 'publish'
				LEFT JOIN $wpdb->posts locations_2 ON locations_2.ID = bookings.drop_off AND locations_2.post_status = 'publish'
				WHERE car_rentals.post_status = 'publish' ";
		
		if ($search_term != null && !empty($search_term)) {
			$search_term = "%" . $search_term . "%";
			$sql .= $wpdb->prepare(" AND (bookings.first_name LIKE '%s' OR bookings.last_name LIKE '%s') ", $search_term, $search_term);
		}
		
		if (isset($car_rental_id) && $car_rental_id > 0) {
			$sql .= $wpdb->prepare(" AND bookings.car_rental_id = %d ", $car_rental_id) ;
		}
		
		if ($user_id > 0) {
			$sql .= $wpdb->prepare(" AND bookings.user_id = %d ", $user_id) ;
		}
		
		if (isset($author_id)) {
			$sql .= $wpdb->prepare(" AND car_rentals.post_author=%d ", $author_id);
		}
		
		if(!empty($orderby) & !empty($order)) { 
			$sql.= ' ORDER BY '.$orderby.' '.$order; 
		}
		
		$sql_count = $sql;
		
		if(!empty($paged) && !empty($per_page)) {
			$offset=($paged-1)*$per_page;
			$sql .= $wpdb->prepare(" LIMIT %d, %d ", $offset, $per_page); 
		}
		
		$results = array(
			'total' => $wpdb->query($sql_count),
			'results' => $wpdb->get_results($sql)
		);
		
		return $results;
	}

	function get_car_rental_booking($booking_id) {

		global $wpdb, $bookyourtravel_multi_language_count, $bookyourtravel_theme_globals;
		
		$sql = "SELECT 	DISTINCT bookings.*, 
						car_rentals.post_title car_rental_name,
						(
							SELECT MIN(booking_date) FROM " . BOOKYOURTRAVEL_CAR_RENTAL_BOOKING_DAYS_TABLE . " v2 
							WHERE v2.car_rental_booking_id = bookings.Id 
						) from_day,
						(
							SELECT DATE_ADD(MAX(booking_date), INTERVAL +1 DAY) FROM " . BOOKYOURTRAVEL_CAR_RENTAL_BOOKING_DAYS_TABLE . " v3 
							WHERE v3.car_rental_booking_id = bookings.Id 
						) to_day, 
						locations.ID pick_up_location_id, 
						locations_2.ID drop_off_location_id,					
						locations.post_title pick_up_title, 
						locations_2.post_title drop_off_title
				FROM " . BOOKYOURTRAVEL_CAR_RENTAL_BOOKINGS_TABLE . " bookings ";
				
		if(defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('car_rental') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$sql .= " INNER JOIN " . $wpdb->prefix . "icl_translations translations_default ON translations_default.element_type = 'post_car_rental' AND translations_default.language_code='" . BookYourTravel_Theme_Utils::get_default_language() . "' AND translations_default.element_id = bookings.car_rental_id ";			
			$sql .= " INNER JOIN " . $wpdb->prefix . "icl_translations translations ON translations.element_type = 'post_car_rental' AND translations.language_code='" . ICL_LANGUAGE_CODE . "' AND translations.trid = translations_default.trid ";
		}
		
		$sql .= " INNER JOIN $wpdb->posts car_rentals ON ";
		if(defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('car_rental') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$sql .= " car_rentals.ID = translations.element_id ";
		} else {
			$sql .= " car_rentals.ID = bookings.car_rental_id ";
		}	
				
		$sql .= "LEFT JOIN $wpdb->postmeta car_rental_meta_location ON car_rentals.ID=car_rental_meta_location.post_id AND car_rental_meta_location.meta_key='car_rental_location_post_id'
				LEFT JOIN $wpdb->posts locations ON locations.ID = car_rental_meta_location.meta_value+0 AND locations.post_status = 'publish'
				LEFT JOIN $wpdb->posts locations_2 ON locations_2.ID = bookings.drop_off AND locations_2.post_status = 'publish'
				WHERE car_rentals.post_status = 'publish' AND bookings.Id = $booking_id ";
				
		return $wpdb->get_row($sql);
	}

	function update_booking_woocommerce_info($booking_id, $cart_key = null, $woo_order_id = null, $woo_status = null) {
	
		global $wpdb;
	
		if (isset($cart_key) || isset($woo_order_id) || isset($woo_status)) {
			$sql = "UPDATE " . BOOKYOURTRAVEL_CAR_RENTAL_BOOKINGS_TABLE . "
					SET ";
			
			if (isset($cart_key))
				$sql .= $wpdb->prepare("cart_key = %s, ", $cart_key);
			if (isset($woo_order_id))
				$sql .= $wpdb->prepare("woo_order_id = %d, ", $woo_order_id);
			if (isset($woo_status))
				$sql .= $wpdb->prepare("woo_status = %s, ", $woo_status);
		
			$sql = rtrim($sql, ", ");
			$sql .= $wpdb->prepare(" WHERE Id = %d", $booking_id);
			
			return $wpdb->query($sql);			
		}
		
		return null;
	}	
	
	function delete_car_rental_booking($booking_id) {

		global $wpdb;
		
		do_action('bookyourtravel_before_delete_car_rental_booking', $booking_id);
		
		$sql = "DELETE FROM " . BOOKYOURTRAVEL_CAR_RENTAL_BOOKINGS_TABLE . "
				WHERE Id = %d";
				
		$this->clear_booking_days($booking_id);
		
		$wpdb->query($wpdb->prepare($sql, $booking_id));	
	}

}

global $bookyourtravel_car_rental_helper;
// store the instance in a variable to be retrieved later and call init
$bookyourtravel_car_rental_helper = BookYourTravel_Car_Rental_Helper::get_instance();
$bookyourtravel_car_rental_helper->init();