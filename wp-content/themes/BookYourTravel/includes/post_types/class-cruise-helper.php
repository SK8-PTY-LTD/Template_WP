<?php

class BookYourTravel_Cruise_Helper extends BookYourTravel_BaseSingleton {

	private $enable_cruises;
	private $cruise_custom_meta_fields;
	private $cruise_list_custom_meta_fields;
	private $cruise_list_meta_box;

	protected function __construct() {
		
		global $bookyourtravel_theme_globals;
		
		$this->enable_cruises = $bookyourtravel_theme_globals->enable_cruises();
		
		// our parent class might
		// contain shared code in its constructor
		parent::__construct();	
	}
	
    public function init() {

		add_action( 'bookyourtravel_initialize_post_types', array( $this, 'initialize_post_type' ), 0);	
	
		if ($this->enable_cruises) {
		
			add_action( 'bookyourtravel_after_delete_cruise', array( $this, 'after_delete_cruise' ), 10, 1);
			add_action( 'bookyourtravel_save_cruise', array( $this, 'save_cruise' ), 10, 1);
		
			add_action( 'admin_init', array($this, 'remove_unnecessary_meta_boxes') );
			add_filter( 'manage_edit-cruise_columns', array( $this, 'manage_edit_cruise_columns'), 10, 1);	
			add_action( 'admin_init', array( $this, 'cruise_admin_init' ) );
			add_action( 'edited_cruise_type', array( $this, 'save_cruise_type_custom_meta' ), 10, 2 );  
			add_action( 'create_cruise_type', array( $this, 'save_cruise_type_custom_meta' ), 10, 2 );
			add_action( 'cruise_type_add_form_fields', array( $this, 'cruise_type_add_new_meta_fields' ), 10, 2 );
			add_action( 'cruise_type_edit_form_fields', array( $this, 'cruise_type_edit_meta_fields' ), 10, 2 );
			add_action( 'bookyourtravel_initialize_ajax', array( $this, 'initialize_ajax' ), 0);
		}
	}
	
	function save_cruise($post_id) {
		
		delete_post_meta_by_key('_location_cruise_count');
		
	}	
	
	function after_delete_cruise($post_id) {
		
		delete_post_meta_by_key('_location_cruise_count');
		
	}
	
	function initialize_ajax() {
	
		add_action( 'wp_ajax_cruise_is_price_per_person_ajax_request', array( $this, 'cruise_is_price_per_person_ajax_request' ) );
		add_action( 'wp_ajax_cruise_list_cabin_types_ajax_request', array( $this, 'cruise_list_cabin_types_ajax_request') );
		add_action( 'wp_ajax_nopriv_cruise_list_cabin_types_ajax_request', array( $this, 'cruise_list_cabin_types_ajax_request') );
		add_action( 'wp_ajax_cruise_type_is_repeated_ajax_request', array( $this, 'cruise_type_is_repeated_ajax_request') );		
		add_action( 'wp_ajax_nopriv_cruise_type_is_repeated_ajax_request', array( $this, 'cruise_type_is_repeated_ajax_request') );		
		add_action( 'wp_ajax_cruise_get_price_request', array($this, 'cruise_get_price_request' ) );
		add_action( 'wp_ajax_nopriv_cruise_get_price_request', array($this, 'cruise_get_price_request') );
		add_action( 'wp_ajax_cruise_schedule_dates_request', array($this, 'cruise_schedule_dates_request' ));
		add_action( 'wp_ajax_nopriv_cruise_schedule_dates_request', array($this, 'cruise_schedule_dates_request' ));
		add_action( 'wp_ajax_cruise_available_schedule_id_request', array($this, 'cruise_available_schedule_id_request'));
		add_action( 'wp_ajax_nopriv_cruise_available_schedule_id_request', array($this, 'cruise_available_schedule_id_request'));
		add_action( 'wp_ajax_book_cruise_ajax_request', array($this, 'book_cruise_ajax_request') );
		add_action( 'wp_ajax_nopriv_book_cruise_ajax_request', array($this, 'book_cruise_ajax_request') );
		add_action( 'wp_ajax_cruise_is_reservation_only_request', array($this, 'cruise_is_reservation_only_request'));
		add_action( 'wp_ajax_nopriv_cruise_is_reservation_only_request', array($this, 'cruise_is_reservation_only_request'));
		
		add_action( 'wp_ajax_nopriv_cruise_get_schedule_duration_days_request', array($this, 'cruise_get_schedule_duration_days_request'));
		add_action( 'wp_ajax_cruise_get_schedule_duration_days_request', array($this, 'cruise_get_schedule_duration_days_request'));
		
		add_action( 'wp_ajax_cruise_process_booking_ajax_request', array( $this, 'process_booking_ajax_request' ) );
		add_action( 'wp_ajax_nopriv_cruise_process_booking_ajax_request', array( $this, 'process_booking_ajax_request' ) );

		add_action( 'wp_ajax_cruise_admin_get_cruise_fields_ajax_request', array( $this, 'admin_get_cruise_fields_ajax_request') );
		add_action( 'wp_ajax_cruise_admin_get_schedule_fields_ajax_request', array( $this, 'admin_get_schedule_fields_ajax_request') );
		add_action( 'wp_ajax_cruise_admin_available_days_ajax_request', array( $this, 'admin_available_days_ajax_request') );
		add_action( 'wp_ajax_cruise_admin_available_schedule_id_request', array( $this, 'admin_available_schedule_id_request') );
		add_action( 'wp_ajax_frontend_delete_cruise_schedule_ajax_request', array( $this, 'frontend_delete_cruise_schedule_ajax_request') );				
	}
	
	function frontend_delete_cruise_schedule_ajax_request() {
	
		global $bookyourtravel_theme_globals, $bookyourtravel_cruise_helper;
		
		if ( isset($_REQUEST) ) {
		
			$nonce = wp_kses($_REQUEST['nonce'], array());
			
			if (wp_verify_nonce( $nonce, 'bookyourtravel_nonce' )) {

				$schedule_id = isset($_REQUEST['schedule_id']) ? intval(wp_kses($_REQUEST['schedule_id'], array())) : 0;

				if ($schedule_id > 0) {

					$bookyourtravel_cruise_helper->delete_cruise_schedule($schedule_id);	

					echo '1';
				}				
			
			}			
		}
		
		die();	
	}	
	
	function admin_available_schedule_id_request() {
		
		global $bookyourtravel_cruise_helper, $bookyourtravel_theme_globals;
		
		if ( isset($_REQUEST) ) {
		
			$nonce = wp_kses($_REQUEST['nonce'], array());
			
			if (wp_verify_nonce( $nonce, 'bookyourtravel_nonce' )) {

				$cruise_id = isset($_REQUEST['cruise_id']) ? intval(wp_kses($_REQUEST['cruise_id'], array())) : 0;
				$cabin_type_id = isset($_REQUEST['cabin_type_id']) ? intval(wp_kses($_REQUEST['cabin_type_id'], array())) : 0;
				$cruise_date = isset($_REQUEST['cruise_date']) ? sanitize_text_field($_REQUEST['cruise_date']) : null;
				
				if ($cruise_date) {
					$cruise_date = date('Y-m-d', strtotime($cruise_date));
					$schedule_id = $bookyourtravel_cruise_helper->get_cruise_available_schedule_id($cruise_id, $cabin_type_id, $cruise_date);
					echo $schedule_id;
				} else {
					echo 0;
				}
			} else {
				echo 'nonce_error';
			}
		} else {
			echo 'empty_request';
		}
		
		die();
	}
	
	function admin_available_days_ajax_request() {
		
		global $bookyourtravel_theme_globals, $bookyourtravel_cruise_helper;
		
		if ( isset($_REQUEST) ) {
		
			$nonce = wp_kses($_REQUEST['nonce'], array());
			
			if (wp_verify_nonce( $nonce, 'bookyourtravel_nonce' )) {
			
				$cruise_id = intval(wp_kses($_REQUEST['cruise_id'], array()));	
				$cabin_type_id = intval(wp_kses($_REQUEST['cabin_type_id'], array()));	
				$month = intval(wp_kses($_REQUEST['month'], array()));	
				$year = intval(wp_kses($_REQUEST['year'], array()));	
				$day = intval(wp_kses($_REQUEST['day'], array()));
				$current_booking_id = intval(wp_kses($_REQUEST['current_booking_id'], array()));
				$hour = 0;
				$minute = 0;
				
				$date_from = date('Y-m-d', strtotime("$year-$month-$day 00:00"));
			
				if ($cruise_id > 0) {
					$cruise_obj = new BookYourTravel_Cruise(intval($cruise_id));
					$schedule_entries = $bookyourtravel_cruise_helper->list_available_cruise_schedule_entries($cruise_id, $cabin_type_id, $date_from, $year, $month, $cruise_obj->get_type_is_repeated(), $cruise_obj->get_type_day_of_week_indexes(), 1, $current_booking_id);
					echo json_encode($schedule_entries);
				}			
			}
		}		
		
		die();
	}
	
	function admin_get_cruise_fields_ajax_request() {
	
		if ( isset($_REQUEST) ) {
		
			$nonce = wp_kses($_REQUEST['nonce'], array());
			$cruise_id = intval(wp_kses($_REQUEST['cruise_id'], array()));
			$cabin_type_id = 0;
			if (isset($_REQUEST['cabin_type_id'])) {
				$cabin_type_id = intval(wp_kses($_REQUEST['cabin_type_id'], array()));
			}
			
			if (wp_verify_nonce( $nonce, 'bookyourtravel_nonce' )) {
				
				$cruise_obj = new BookYourTravel_Cruise((int)$cruise_id);
				$cabin_type_obj = null;
				if ($cabin_type_id > 0) {
					$cabin_type_obj = new BookYourTravel_Cabin_Type((int)$cabin_type_id);
				}
				
				$fields = new stdClass();				
				if ($cabin_type_obj == null) {
					$fields->max_adult_count = 5;
					$fields->max_child_count = 5;					
				} else {
					$fields->max_adult_count = $cabin_type_obj->get_max_adult_count();
					$fields->max_child_count = $cabin_type_obj->get_max_child_count();										
				}				
				
				$fields->is_price_per_person = $cruise_obj->get_is_price_per_person();
				
				$fields->cabin_types = array();
				$cabin_type_ids = $cruise_obj->get_cabin_types();					
				if ($cruise_obj && $cabin_type_ids && count($cabin_type_ids) > 0) { 	
					for ( $i = 0; $i < count($cabin_type_ids); $i++ ) {
					
						$temp_id = $cabin_type_ids[$i];
						$cabin_type_obj = new BookYourTravel_Cabin_Type(intval($temp_id));
						$cabin_type_temp = new stdClass();
						$cabin_type_temp->name = $cabin_type_obj->get_title();
						$cabin_type_temp->id = $cabin_type_obj->get_id();
						$fields->cabin_types[] = $cabin_type_temp;					
					}
				}
				
				echo json_encode($fields);	
			}
		}
		
		// Always die in functions echoing ajax content
		die();	
	
	}
	
	function admin_get_schedule_fields_ajax_request() {
	
		if ( isset($_REQUEST) ) {
		
			$nonce = wp_kses($_REQUEST['nonce'], array());
			$cruise_schedule_id = intval(wp_kses($_REQUEST['cruise_schedule_id'], array()));
			
			if (wp_verify_nonce( $nonce, 'bookyourtravel_nonce' )) {
				
				$cruise_schedule = $this->get_cruise_schedule($cruise_schedule_id);

				$fields = new stdClass();
				
				$fields->price = $cruise_schedule->price;
				$fields->price_child = $cruise_schedule->price_child;
				$fields->duration_days = $cruise_schedule->duration_days;
				
				echo json_encode($fields);	
			}
		}
		
		// Always die in functions echoing ajax content
		die();	
	}	
	
	function cruise_get_schedule_duration_days_request() {
	
		if ( isset($_REQUEST) ) {
			$nonce = wp_kses($_REQUEST['nonce'], array());
			$schedule_id = intval(wp_kses($_REQUEST['schedule_id'], array()));
			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {

				global $bookyourtravel_cruise_helper;
				
				$schedule = $bookyourtravel_cruise_helper->get_cruise_schedule($schedule_id);
				if ($schedule != null) {
					echo $schedule->duration_days;
				} else {
					echo -1;
				}
			}
		}
		
		// Always die in functions echoing ajax content
		die();	
	}
	
	function cruise_type_is_repeated_ajax_request() {
		if ( isset($_REQUEST) ) {
			$nonce = wp_kses($_REQUEST['nonce'], array());
			$cruise_id = intval(wp_kses($_REQUEST['cruiseId'], array()));
			if (wp_verify_nonce( $nonce, 'bookyourtravel_nonce' )) {
				$cruise_obj = new BookYourTravel_Cruise(intval($cruise_id));
				$cruise_type_is_repeated = $cruise_obj->get_type_is_repeated();
				echo (int)$cruise_type_is_repeated;
			}
		}
		
		// Always die in functions echoing ajax content
		die();
	}
		
	function cruise_list_cabin_types_ajax_request() {

		if ( isset($_REQUEST) ) {
			$nonce = wp_kses($_REQUEST['nonce'], array());
			$cruise_id = intval(wp_kses($_REQUEST['cruiseId'], array()));
			if (wp_verify_nonce( $nonce, 'bookyourtravel_nonce' )) {
				$cruise_obj = new BookYourTravel_Cruise($cruise_id);
				$cabin_types = array();		
				
				$cabin_type_ids = $cruise_obj->get_cabin_types();
				if ($cruise_obj && $cabin_type_ids && count($cabin_type_ids) > 0) { 				
					for ( $i = 0; $i < count($cabin_type_ids); $i++ ) {
						$temp_id = $cabin_type_ids[$i];
						$cabin_type_obj = new BookYourTravel_Cabin_Type(intval($temp_id));
						$cabin_type_temp = new stdClass();
						$cabin_type_temp->name = $cabin_type_obj->get_title();
						$cabin_type_temp->id = $temp_id;
						$cabin_types[] = $cabin_type_temp;					
					}
				}
				
				echo json_encode($cabin_types);
			}
		}
		
		// Always die in functions echoing ajax content
		die();
	}
		
	function cruise_is_price_per_person_ajax_request() {
		if ( isset($_REQUEST) ) {
			$nonce = wp_kses($_REQUEST['nonce'], array());
			$cruise_id = intval(wp_kses($_REQUEST['cruiseId'], array()));
			if (wp_verify_nonce( $nonce, 'bookyourtravel_nonce' )) {
				$is_price_per_person = get_post_meta( $cruise_id, 'cruise_is_price_per_person', true );
				echo $is_price_per_person ? 1 : 0;
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
			$booking_object->total_cruise_price = 0;
			$booking_object->total_extra_items_price = 0;

			$booking_object->cruise_id = isset($_REQUEST['cruise_id']) ? intval(wp_kses($_REQUEST['cruise_id'], array())) : 0;
			$booking_object->cabin_type_id = isset($_REQUEST['cabin_type_id']) ? intval(wp_kses($_REQUEST['cabin_type_id'], array())) : 0;
			$booking_object->adults = isset($_REQUEST['adults']) ? intval(wp_kses($_REQUEST['adults'], array())) : 1;
			$booking_object->children = isset($_REQUEST['children']) ? intval(wp_kses($_REQUEST['children'], array())) : 0;
			$booking_object->cruise_date = isset($_REQUEST['cruise_date']) ? date('Y-m-d', strtotime(sanitize_text_field($_REQUEST['cruise_date']))) : null;
			$booking_object->cruise_schedule_id = isset($_REQUEST['cruise_schedule_id']) ? intval(wp_kses($_REQUEST['cruise_schedule_id'], array())) : 0;
			$cruise_schedule = $this->get_cruise_schedule($booking_object->cruise_schedule_id);

			$booking_object->cruise_id = $cruise_schedule->cruise_id;
			$booking_object->cruise_id = BookYourTravel_Theme_Utils::get_default_language_post_id($booking_object->cruise_id, 'cruise');
			$booking_object->cabin_type_id = $cruise_schedule->cabin_type_id;
			if ($booking_object->cabin_type_id > 0)
				$booking_object->cabin_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($booking_object->cabin_type_id, 'cabin_type'); 
			
			$cruise_count_children_stay_free = get_post_meta($booking_object->cruise_id, 'cruise_count_children_stay_free', true );
			$cruise_count_children_stay_free = isset($cruise_count_children_stay_free) ? intval($cruise_count_children_stay_free) : 0;

			$booking_object->billable_children = $booking_object->children - $cruise_count_children_stay_free;
			$booking_object->billable_children = $booking_object->billable_children > 0 ? $booking_object->billable_children : 0;
			
			$booking_object->total_cruise_price = $this->calculate_total_cruise_price($booking_object->cruise_id, $booking_object->cabin_type_id, $booking_object->cruise_schedule_id, $booking_object->cruise_date, $booking_object->adults, $booking_object->billable_children, $booking_object->Id);
			$booking_object->total_price += $booking_object->total_cruise_price;

			$booking_object->extra_items = null;
			
			if ($enable_extra_items && isset($_REQUEST['extra_items'])) {
				
				$booking_object->submitted_extra_items_array = (array)$_REQUEST['extra_items'];
								
				$booking_object->extra_items = array();
				
				$total_days = $cruise_schedule->duration_days;
				$total_days = $total_days > 0 ? $total_days : 1;

				foreach ($booking_object->submitted_extra_items_array as $submitted_extra_item) {
					if (isset($submitted_extra_item['id']) && $submitted_extra_item['quantity']) {
						$extra_item_id = intval(sanitize_text_field($submitted_extra_item['id']));
						$quantity = intval(sanitize_text_field($submitted_extra_item['quantity']));
						$booking_object->extra_items[$extra_item_id] = $quantity;
						$booking_object->total_extra_items_price += $bookyourtravel_extra_item_helper->calculate_extra_item_total($extra_item_id, $quantity, $booking_object->adults, $booking_object->billable_children, $total_days);
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
				
					$cruise_obj = new BookYourTravel_Cruise($booking_object->cruise_id);
					if (isset($booking_object->cabin_type_id))
						$cabin_type_obj = new BookYourTravel_Cabin_Type($booking_object->cabin_type_id);
					
					if ($cruise_obj != null) {
					
						if ($add_captcha_to_forms && $c_val_s != ($c_val_1 + $c_val_2)) {
							echo 'captcha_error';
							die();
						} else {
						
							$booking_object->Id = $this->create_cruise_booking($current_user->ID, $booking_object);
							
							echo $booking_object->Id;

							$use_woocommerce_for_checkout = $bookyourtravel_theme_globals->use_woocommerce_for_checkout();
							$is_reservation_only = get_post_meta( $booking_object->cruise_id, 'cruise_is_reservation_only', true );

							if (!$use_woocommerce_for_checkout || !BookYourTravel_Theme_Utils::is_woocommerce_active() || $is_reservation_only) {
							
								// only send email if we are not proceeding to WooCommerce checkout or if woocommerce is not active at all.
								$admin_email = get_bloginfo('admin_email');
								$admin_name = get_bloginfo('name');
								
								$subject = esc_html__('New cruise booking', 'bookyourtravel');
							
								$message = esc_html__('New cruise booking: ', 'bookyourtravel');
								$message .= "\n\n";
								$message .= sprintf(esc_html__("Cruise: %s", 'bookyourtravel'), $cruise_obj->get_title()) . "\n\n";
								
								if ($cabin_type_obj) {
									$message .= sprintf(esc_html__("Cabin type: %s", 'bookyourtravel'), $cabin_type_obj->get_title()) . "\n\n";
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
								
								$message .= sprintf(esc_html__("Cruise date: %s", 'bookyourtravel'), $booking_object->cruise_date) . "\n\n";
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
								
								if ($booking_object->total_cruise_price > 0) {
								
									$total_cruise_price_string = '';
									if (!$show_currency_symbol_after) { 
										$total_cruise_price_string = $default_currency_symbol . ' ' . number_format_i18n( $booking_object->total_cruise_price, $price_decimal_places );
									} else {
										$total_cruise_price_string = number_format_i18n( $booking_object->total_cruise_price, $price_decimal_places ) . ' ' . $default_currency_symbol;
									}
									
									$total_cruise_price_string = preg_replace("/&nbsp;/",' ',$total_cruise_price_string);
								
									$message .= sprintf(esc_html__("Reservation total: %s", 'bookyourtravel'), $total_cruise_price_string) . "\n\n";
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

								$contact_emails = trim(get_post_meta($booking_object->cruise_id, 'cruise_contact_email', true ));
								
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
	
	function calculate_total_cruise_price($cruise_id, $cabin_type_id, $cruise_schedule_id, $cruise_date, $adults, $children, $booking_id) {

		$cruise_obj = new BookYourTravel_Cruise($cruise_id);
		$cruise_schedule = $this->get_cruise_schedule($cruise_schedule_id);

		$cruise_is_price_per_person = $cruise_obj->get_is_price_per_person();

		$total_price_adults = 0;
		
		$total_price_children = 0;
		if ($cruise_is_price_per_person) {
			$total_price_children = $cruise_schedule->price_child * $children;
			$total_price_adults = $cruise_schedule->price * $adults;
		} else {
			$total_price_adults = $cruise_schedule->price;
		}
			
		$total_price = $total_price_adults + $total_price_children;

		return $total_price;
	}
	
	function update_booking_woocommerce_info($booking_id, $cart_key = null, $woo_order_id = null, $woo_status = null) {
	
		global $wpdb;
	
		if (isset($cart_key) || isset($woo_order_id) || isset($woo_status)) {
			$sql = "UPDATE " . BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE . "
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
	
	function create_cruise_booking($user_id, $booking_object) {

		global $wpdb;

		$errors = array();

		$sql = "INSERT INTO " . BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE . "
				(user_id, cruise_schedule_id, adults, children, cruise_date, first_name, last_name, company, email, phone, address, address_2, town, zip, state, country, special_requirements, other_fields, extra_items, total_cruise_price, total_extra_items_price, total_price)
				VALUES 
				(%d, %d, %d, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %f, %f, %f);";

		$result = $wpdb->query($wpdb->prepare($sql, $user_id, $booking_object->cruise_schedule_id, $booking_object->adults, $booking_object->children, $booking_object->cruise_date, $booking_object->first_name, $booking_object->last_name, $booking_object->company, $booking_object->email, $booking_object->phone, $booking_object->address, $booking_object->address_2, $booking_object->town, $booking_object->zip, $booking_object->state, $booking_object->country, $booking_object->special_requirements, serialize($booking_object->other_fields), serialize($booking_object->extra_items), $booking_object->total_cruise_price, $booking_object->total_extra_items_price, $booking_object->total_price));

		if (is_wp_error($result))
			$errors[] = $result;

		$booking_object->Id = $wpdb->insert_id;
		
		$schedule = $this->get_cruise_schedule($booking_object->cruise_schedule_id);
		$this->clear_price_meta_cache($schedule->cruise_id);
			
		return $booking_object->Id;
	}
	
	function update_cruise_booking($booking_id, $booking_object) {

		global $wpdb;
		
		$result = 0;
		
		$sql = "UPDATE " . BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE . " SET ";
				
		$field_sql = '';
		
		foreach ($booking_object as $field_key => $field_value) {
			
			switch ($field_key) {
			
				case 'cruise_schedule_id' 			: $field_sql .= $wpdb->prepare("cruise_schedule_id = %d, ", $field_value); break;
				case 'cruise_date' 					: $field_sql .= $wpdb->prepare("cruise_date = %s, ", $field_value); break;
				case 'adults' 						: $field_sql .= $wpdb->prepare("adults = %d, ", $field_value); break;
				case 'children' 					: $field_sql .= $wpdb->prepare("children = %d, ", $field_value); break;
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
				case 'total_cruise_price' 	: $field_sql .= $wpdb->prepare("total_cruise_price = %f, ", $field_value); break;
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
		
		$schedule = $this->get_cruise_schedule($booking_object->cruise_schedule_id);
		$this->clear_price_meta_cache($schedule->cruise_id);
		
		return $result;
	}
	
	function cruise_available_schedule_id_request() {
		global $bookyourtravel_cruise_helper, $bookyourtravel_theme_globals;
		if ( isset($_REQUEST) ) {
			$nonce = wp_kses($_REQUEST['nonce'], array());
			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {

				$cruise_id = intval(wp_kses($_REQUEST['cruiseId'], array()));	
				$cabin_type_id = intval(wp_kses($_REQUEST['cabinTypeId'], array()));	
				$date_value = sanitize_text_field($_REQUEST['dateValue']);	
				$date_value = date('Y-m-d', strtotime($date_value));
				$schedule_id = $bookyourtravel_cruise_helper->get_cruise_available_schedule_id($cruise_id, $cabin_type_id, $date_value);
				echo $schedule_id;
			}
		}
		
		die();
	}
	
	function cruise_schedule_dates_request() {
	
		global $bookyourtravel_cruise_helper, $bookyourtravel_theme_globals;

		if ( isset($_REQUEST) ) {
			$nonce = wp_kses($_REQUEST['nonce'], array());
			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {

				$cruise_id = intval(wp_kses($_REQUEST['cruiseId'], array()));	
				$cabin_type_id = intval(wp_kses($_REQUEST['cabinTypeId'], array()));
				$month = intval(wp_kses($_REQUEST['month'], array()));	
				$year = intval(wp_kses($_REQUEST['year'], array()));	
				$day = intval(wp_kses($_REQUEST['day'], array()));
				$hour = 0;
				$minute = 0;
				
				$date_from = date('Y-m-d', strtotime("$year-$month-$day $hour:$minute"));
			
				if ($cruise_id > 0) {
					$cruise_obj = new BookYourTravel_Cruise(intval($cruise_id));
					$schedule_entries = $bookyourtravel_cruise_helper->list_available_cruise_schedule_entries($cruise_id, $cabin_type_id, $date_from, $year, $month, $cruise_obj->get_type_is_repeated(), $cruise_obj->get_type_day_of_week_indexes(), 3);				

					echo json_encode($schedule_entries);
				}
			}
		}
		
		die();
	}
	
	function cruise_get_price_request() {

		global $bookyourtravel_cruise_helper, $bookyourtravel_theme_globals;
		$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();

		if ( isset($_REQUEST) ) {
			$nonce = wp_kses($_REQUEST['nonce'], array());
			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {

				$cruise_id = intval(wp_kses($_REQUEST['cruiseId'], array()));	
				$cabin_type_id = intval(wp_kses($_REQUEST['cabinTypeId'], array()));	
				$date_value = sanitize_text_field($_REQUEST['dateValue']);	
				$date_value = date('Y-m-d', strtotime($date_value));
				$schedule_id = $bookyourtravel_cruise_helper->get_cruise_available_schedule_id($cruise_id, $cabin_type_id, $date_value);
		
				if ($schedule_id > 0) {				
					$price = number_format ($bookyourtravel_cruise_helper->get_cruise_schedule_price($schedule_id, false), $price_decimal_places, ".", "");
					$child_price = number_format ($bookyourtravel_cruise_helper->get_cruise_schedule_price($schedule_id, true), $price_decimal_places, ".", "");
		
					$prices = array( 
						'price' => $price, 
						'child_price' => $child_price 
					);
					
					echo json_encode($prices);
				}
			}
		}
		
		die();
	}
	
	function cruise_is_reservation_only_request() {
		if ( isset($_REQUEST) ) {
			$nonce = wp_kses($_REQUEST['nonce'], array());
			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {

				$cruise_id = intval(wp_kses($_REQUEST['cruise_id'], array()));	
				$is_reservation_only = get_post_meta( $cruise_id, 'cruise_is_reservation_only', true );
				$is_reservation_only = isset($is_reservation_only) ? (int)$is_reservation_only : 0;
				
				echo (int)$is_reservation_only;
			}
		}
		
		die();
	}
		
	function cruise_admin_init() {
	
		global $bookyourtravel_cabin_type_helper, $post;

		$post_id = 0;
		if (isset($post))
			$post_id = $post->ID;
		else if (isset($_GET['post']))
			$post_id = (int)$_GET['post'];
		
		$cabin_types = array();
		
		if ($post_id > 0) {
			$cabin_type_query = $bookyourtravel_cabin_type_helper->list_cabin_types(null, array('publish'), $post_id);
			if ($cabin_type_query->have_posts()) {
				while ($cabin_type_query->have_posts()) {
					$cabin_type_query->the_post();
					global $post;				
					$cabin_types[] = array('value' => $post->ID, 'label' => $post->post_title);
				}
			}
			wp_reset_postdata();
		}
		
		if (count($cabin_types) == 0) {
			// if cruise has no associated cabin types, list all possible cabin types (for backwards compatibility)
			$cabin_type_query = $bookyourtravel_cabin_type_helper->list_cabin_types(null, array('publish'), null);
			if ($cabin_type_query->have_posts()) {
				while ($cabin_type_query->have_posts()) {
					$cabin_type_query->the_post();
					global $post;				
					$cabin_types[] = array('value' => $post->ID, 'label' => $post->post_title);
				}
			}
			wp_reset_postdata();	
		}
		
		if ($this->enable_cruises) {
					
			$this->cruise_custom_meta_fields = array(
				array( // Post ID select box
					'label'	=> esc_html__('Is Featured', 'bookyourtravel'), // <label>
					'desc'	=> esc_html__('Show in lists where only featured items are shown.', 'bookyourtravel'), // description
					'id'	=> 'cruise_is_featured', // field id and name
					'type'	=> 'checkbox', // type of field
				),
				array( 
					'label'	=> esc_html__('Is for reservation only?', 'bookyourtravel'), // <label>
					'desc'	=> esc_html__('If this option is checked, then this particular cruise will not be processed via WooCommerce even if WooCommerce is in use.', 'bookyourtravel'), // description
					'id'	=> 'cruise_is_reservation_only', // field id and name
					'type'	=> 'checkbox', // type of field
				),
				array( // Post ID select box
					'label'	=> esc_html__('Hide inquiry form', 'bookyourtravel'), // <label>
					'desc'	=> esc_html__('Do you want to not show inquiry form for this cruise?', 'bookyourtravel'), // description
					'id'	=> 'cruise_hide_inquiry_form', // field id and name
					'type'	=> 'checkbox', // type of field
				),				
				array( 
					'label'	=> esc_html__('Price per person?', 'bookyourtravel'), // <label>
					'desc'	=> esc_html__('Is price calculated per person (adult, child)? If not then calculations are done per cabin.', 'bookyourtravel'), // description
					'id'	=> 'cruise_is_price_per_person', // field id and name
					'type'	=> 'checkbox', // type of field
				),
				array( // Post ID select box
					'label'	=> esc_html__('Cabin types', 'bookyourtravel'), // <label>
					'desc'	=> '', // description
					'id'	=> 'cabin_types', // field id and name
					'type'	=> 'post_checkboxes', // type of field
					'post_type' => array('cabin_type') // post types to display, options are prefixed with their post type
				),
				array( // Taxonomy Select box
					'label'	=> esc_html__('Cruise type', 'bookyourtravel'), // <label>
					// the description is created in the callback function with a link to Manage the taxonomy terms
					'id'	=> 'cruise_type', // field id and name, needs to be the exact name of the taxonomy
					'type'	=> 'tax_select' // type of field
				),
				array( // Post ID select box
					'label'	=> esc_html__('Locations', 'bookyourtravel'), // <label>
					'desc'	=> '', // description
					'id'	=> 'locations', // field id and name
					'type'	=> 'post_checkboxes', // type of field
					'post_type' => array('location') // post types to display, options are prefixed with their post type
				),

				array(
					'label'	=> esc_html__('Count children stay free', 'bookyourtravel'),
					'desc'	=> esc_html__('How many kids stay free before we charge a fee?', 'bookyourtravel'),
					'id'	=> 'cruise_count_children_stay_free',
					'type'	=> 'slider',
					'min'	=> '1',
					'max'	=> '5',
					'step'	=> '1'
				),
				array( // Taxonomy Select box
					'label'	=> esc_html__('Facilities', 'bookyourtravel'), // <label>
					// the description is created in the callback function with a link to Manage the taxonomy terms
					'id'	=> 'facility', // field id and name, needs to be the exact name of the taxonomy
					'type'	=> 'tax_checkboxes' // type of field
				),
				array( // Taxonomy Checkboxes
					'label'	=> esc_html__('Tags', 'bookyourtravel'), // <label>
					// the description is created in the callback function with a link to Manage the taxonomy terms
					'id'	=> 'cruise_tag', // field id and name, needs to be the exact name of the taxonomy
					'type'	=> 'tax_checkboxes' // type of field
				),
				array( // Repeatable & Sortable Text inputs
					'label'	=> esc_html__('Gallery images', 'bookyourtravel'), // <label>
					'desc'	=> esc_html__('A collection of images to be used in slider/gallery on single page', 'bookyourtravel'), // description
					'id'	=> 'cruise_images', // field id and name
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
				array(
					'label'	=> esc_html__('Availability extra text', 'bookyourtravel'),
					'desc'	=> esc_html__('Extra text shown on availability tab above the book now area.', 'bookyourtravel'),
					'id'	=> 'cruise_availability_text',
					'type'	=> 'textarea'
				),
				array(
					'label'	=> esc_html__('Contact email addresses', 'bookyourtravel'),
					'desc'	=> esc_html__('Override admin contact email address by specifying contact email addresses for this cruise. If specifying multiple email addresses, separate each address with a semi-colon ;', 'bookyourtravel'),
					'id'	=> 'cruise_contact_email',
					'type'	=> 'text'
				),
			);

			global $default_cruise_extra_fields;

			$cruise_extra_fields = of_get_option('cruise_extra_fields');
			if (!is_array($cruise_extra_fields) || count($cruise_extra_fields) == 0)
				$cruise_extra_fields = $default_cruise_extra_fields;
				
			foreach ($cruise_extra_fields as $cruise_extra_field) {
				$field_is_hidden = isset($cruise_extra_field['hide']) ? intval($cruise_extra_field['hide']) : 0;
				
				if (!$field_is_hidden) {
					$extra_field = null;
					$field_label = isset($cruise_extra_field['label']) ? $cruise_extra_field['label'] : '';
					$field_id = isset($cruise_extra_field['id']) ? $cruise_extra_field['id'] : '';
					$field_type = isset($cruise_extra_field['type']) ? $cruise_extra_field['type'] :  '';
					
					if ($field_type == 'textarea')
						$field_type = 'editor';
					
					if (!empty($field_label) && !empty($field_id) && !empty($field_type)) {
						$extra_field = array(
							'label'	=> $field_label,
							'desc'	=> '',
							'id'	=> 'cruise_' . $field_id,
							'type'	=> $field_type
						);
					}

					if ($extra_field) 
						$this->cruise_custom_meta_fields[] = $extra_field;
				}
			}
			
			$sort_by_columns = array();
			$sort_by_columns[] = array('value' => 'title', 'label' => esc_html__('Cruise title', 'bookyourtravel'));
			$sort_by_columns[] = array('value' => 'ID', 'label' => esc_html__('Cruise ID', 'bookyourtravel'));
			$sort_by_columns[] = array('value' => 'date', 'label' => esc_html__('Publish date', 'bookyourtravel'));
			$sort_by_columns[] = array('value' => 'rand', 'label' => esc_html__('Random', 'bookyourtravel'));
			$sort_by_columns[] = array('value' => 'comment_count', 'label' => esc_html__('Comment count', 'bookyourtravel'));
			
			$this->cruise_list_custom_meta_fields = array(
				array( // Taxonomy Select box
					'label'	=> esc_html__('Cruise type', 'bookyourtravel'), // <label>
					// the description is created in the callback function with a link to Manage the taxonomy terms
					'id'	=> 'cruise_type', // field id and name, needs to be the exact name of the taxonomy
					'type'	=> 'tax_select' // type of field
				),
				array( // Taxonomy Select box
					'label'	=> esc_html__('Cruise tags', 'bookyourtravel'), // <label>
					// the description is created in the callback function with a link to Manage the taxonomy terms
					'id'	=> 'cruise_tag', // field id and name, needs to be the exact name of the taxonomy
					'type'	=> 'tax_checkboxes' // type of field
				),
				array( // Taxonomy Select box
					'label'	=> esc_html__('Location', 'bookyourtravel'), // <label>
					// the description is created in the callback function with a link to Manage the taxonomy terms
					'id'	=> 'cruise_list_location_post_id', // field id and name
					'type'	=> 'post_select', // type of field
					'post_type' => array('location') // post types to display, options are prefixed with their post type
				),
				array( // Select box
					'label'	=> esc_html__('Sort by field', 'bookyourtravel'), // <label>
					// the description is created in the callback function with a link to Manage the taxonomy terms
					'id'	=> 'cruise_list_sort_by', // field id and name, needs to be the exact name of the taxonomy
					'type'	=> 'select', // type of field
					'options' => $sort_by_columns
				),
				array( // Post ID select box
					'label'	=> esc_html__('Sort descending?', 'bookyourtravel'), // <label>
					'desc'	=> esc_html__('If checked, will sort cruises in descending order', 'bookyourtravel'), // description
					'id'	=> 'cruise_list_sort_descending', // field id and name
					'type'	=> 'checkbox', // type of field
				),
				array( // Post ID select box
					'label'	=> esc_html__('Show featured only?', 'bookyourtravel'), // <label>
					'desc'	=> esc_html__('If checked, will list featured cruises only', 'bookyourtravel'), // description
					'id'	=> 'cruise_list_show_featured_only', // field id and name
					'type'	=> 'checkbox', // type of field
				),					
			);

		}

		new custom_add_meta_box( 'cruise_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->cruise_custom_meta_fields, 'cruise' );

		$this->cruise_list_meta_box = new custom_add_meta_box( 'cruise_list_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->cruise_list_custom_meta_fields, 'page' );		
		remove_action( 'add_meta_boxes', array( $this->cruise_list_meta_box, 'add_box' ) );
		add_action('add_meta_boxes', array( $this, 'cruise_list_add_meta_boxes') );
	}
	
	function cruise_list_add_meta_boxes() {
		global $post;
		$template_file = get_post_meta($post->ID,'_wp_page_template',true);
		if ($template_file == 'page-cruise-list.php') {
			add_meta_box( $this->cruise_list_meta_box->id, $this->cruise_list_meta_box->title, array( $this->cruise_list_meta_box, 'meta_box_callback' ), 'page', 'normal', 'high' );
		}
	}
	
	function initialize_post_type() {
	
		global $bookyourtravel_theme_globals;	
		$this->enable_cruises = $bookyourtravel_theme_globals->enable_cruises();	
		
		if ($this->enable_cruises) {
			$this->register_cruise_post_type();
			$this->register_cruise_tag_taxonomy();		
			$this->register_cruise_type_taxonomy();
			$this->create_cruise_extra_tables();		
		}
	}
	
	function manage_edit_cruise_columns($columns) {
	
		//unset($columns['taxonomy-cruise_type']);
		return $columns;
	}

	function remove_unnecessary_meta_boxes() {

		remove_meta_box('tagsdiv-cruise_tag', 'cruise', 'side');		
		remove_meta_box('tagsdiv-cruise_type', 'cruise', 'side');		
	}	

	function register_cruise_tag_taxonomy() {
	
		$labels = array(
				'name'              => esc_html__( 'Cruise tags', 'bookyourtravel' ),
				'singular_name'     => esc_html__( 'Cruise tag', 'bookyourtravel' ),
				'search_items'      => esc_html__( 'Search Cruise tags', 'bookyourtravel' ),
				'all_items'         => esc_html__( 'All Cruise tags', 'bookyourtravel' ),
				'parent_item'                => null,
				'parent_item_colon'          => null,
				'edit_item'         => esc_html__( 'Edit Cruise tag', 'bookyourtravel' ),
				'update_item'       => esc_html__( 'Update Cruise tag', 'bookyourtravel' ),
				'add_new_item'      => esc_html__( 'Add New Cruise tag', 'bookyourtravel' ),
				'new_item_name'     => esc_html__( 'New Cruise tag Name', 'bookyourtravel' ),
				'separate_items_with_commas' => esc_html__( 'Separate cruise tags with commas', 'bookyourtravel' ),
				'add_or_remove_items'        => esc_html__( 'Add or remove cruise tags', 'bookyourtravel' ),
				'choose_from_most_used'      => esc_html__( 'Choose from the most used cruise tags', 'bookyourtravel' ),
				'not_found'                  => esc_html__( 'No cruise tags found.', 'bookyourtravel' ),
				'menu_name'         => esc_html__( 'Cruise tags', 'bookyourtravel' ),
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
			
		register_taxonomy( 'cruise_tag', array( 'cruise' ), $args );
	}

	function register_cruise_type_taxonomy() {
		$labels = array(
				'name'              => esc_html__( 'Cruise types', 'bookyourtravel' ),
				'singular_name'     => esc_html__( 'Cruise type', 'bookyourtravel' ),
				'search_items'      => esc_html__( 'Search Cruise types', 'bookyourtravel' ),
				'all_items'         => esc_html__( 'All Cruise types', 'bookyourtravel' ),
				'parent_item'                => null,
				'parent_item_colon'          => null,
				'edit_item'         => esc_html__( 'Edit Cruise type', 'bookyourtravel' ),
				'update_item'       => esc_html__( 'Update Cruise type', 'bookyourtravel' ),
				'add_new_item'      => esc_html__( 'Add New Cruise type', 'bookyourtravel' ),
				'new_item_name'     => esc_html__( 'New Cruise Type Name', 'bookyourtravel' ),
				'separate_items_with_commas' => esc_html__( 'Separate Cruise types with commas', 'bookyourtravel' ),
				'add_or_remove_items'        => esc_html__( 'Add or remove Cruise types', 'bookyourtravel' ),
				'choose_from_most_used'      => esc_html__( 'Choose from the most used Cruise types', 'bookyourtravel' ),
				'not_found'                  => esc_html__( 'No Cruise types found.', 'bookyourtravel' ),
				'menu_name'         => esc_html__( 'Cruise types', 'bookyourtravel' ),
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
		
		register_taxonomy( 'cruise_type', 'cruise', $args );
	}

	function register_cruise_post_type() {
		
		global $bookyourtravel_theme_globals;
		
		$cruises_permalink_slug = $bookyourtravel_theme_globals->get_cruises_permalink_slug();
		
		$cruise_list_page_id = $bookyourtravel_theme_globals->get_cruise_list_page_id();
		
		if ($cruise_list_page_id > 0) {

			add_rewrite_rule(
				"{$cruises_permalink_slug}$",
				"index.php?post_type=page&page_id={$cruise_list_page_id}", 'top');
		
			add_rewrite_rule(
				"{$cruises_permalink_slug}/page/?([1-9][0-9]*)",
				"index.php?post_type=page&page_id={$cruise_list_page_id}&paged=\$matches[1]", 'top');
		
		}
		
		add_rewrite_rule(
			"{$cruises_permalink_slug}/([^/]+)/page/?([1-9][0-9]*)",
			"index.php?post_type=cruise&name=\$matches[1]&paged-byt=\$matches[2]", 'top');
			
		add_rewrite_tag('%paged-byt%', '([1-9][0-9]*)');		
		
		$labels = array(
			'name'                => esc_html__( 'Cruises', 'bookyourtravel' ),
			'singular_name'       => esc_html__( 'Cruise', 'bookyourtravel' ),
			'menu_name'           => esc_html__( 'Cruises', 'bookyourtravel' ),
			'all_items'           => esc_html__( 'All Cruises', 'bookyourtravel' ),
			'view_item'           => esc_html__( 'View Cruise', 'bookyourtravel' ),
			'add_new_item'        => esc_html__( 'Add New Cruise', 'bookyourtravel' ),
			'add_new'             => esc_html__( 'New Cruise', 'bookyourtravel' ),
			'edit_item'           => esc_html__( 'Edit Cruise', 'bookyourtravel' ),
			'update_item'         => esc_html__( 'Update Cruise', 'bookyourtravel' ),
			'search_items'        => esc_html__( 'Search Cruises', 'bookyourtravel' ),
			'not_found'           => esc_html__( 'No Cruises found', 'bookyourtravel' ),
			'not_found_in_trash'  => esc_html__( 'No Cruises found in Trash', 'bookyourtravel' ),
		);
		$args = array(
			'label'               => esc_html__( 'Cruise', 'bookyourtravel' ),
			'description'         => esc_html__( 'Cruise information pages', 'bookyourtravel' ),
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
			'rewrite' => array('slug' => $cruises_permalink_slug),
		);
		
		register_post_type( 'cruise', $args );	
	}

	function create_cruise_extra_tables() {

		global $bookyourtravel_installed_version, $force_recreate_tables;

		if ($bookyourtravel_installed_version != BOOKYOURTRAVEL_VERSION || $force_recreate_tables) {
			
			global $wpdb;
			
			$table_name = BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE;
			$sql = "CREATE TABLE " . $table_name . " (
						Id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
						season_name varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
						start_date datetime NOT NULL,
						duration_days int NOT NULL DEFAULT 0,
						end_date datetime NULL,
						cruise_id bigint(20) unsigned NOT NULL,
						cabin_type_id bigint(20) unsigned NOT NULL DEFAULT '0',
						cabin_count int(11) NOT NULL,
						price decimal(16,2) NOT NULL,
						price_child decimal(16,2) NOT NULL,
						created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
						PRIMARY KEY  (Id)
					);";
					
			// we do not execute sql directly
			// we are calling dbDelta which cant migrate database
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
			
			global $EZSQL_ERROR;
			$EZSQL_ERROR = array();
			
			$table_name = BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE;
			$sql = "CREATE TABLE " . $table_name . " (
						Id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
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
						special_requirements text CHARACTER SET utf8 COLLATE utf8_bin,
						other_fields text CHARACTER SET utf8 COLLATE utf8_bin NULL,
						extra_items text CHARACTER SET utf8 COLLATE utf8_bin NULL,
						adults int(11) NOT NULL DEFAULT '0',
						children int(11) NOT NULL DEFAULT '0',
						cruise_schedule_id bigint(20) NOT NULL,
						cruise_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
						user_id bigint(20) unsigned DEFAULT NULL,
						total_price_adults decimal(16, 2) NOT NULL,
						total_price_children decimal(16, 2) NOT NULL,
						total_cruise_price decimal(16,2) NOT NULL DEFAULT '0.00',
						total_extra_items_price decimal(16,2) NOT NULL DEFAULT '0.00',
						total_price decimal(16, 2) NOT NULL,
						created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
						woo_order_id bigint(20) NULL,
						woo_status varchar(255) NULL,
						cart_key VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '' NOT NULL,
						PRIMARY KEY  (Id)
					);";
			dbDelta($sql);
			
			$EZSQL_ERROR = array();
			
			$sql = "CREATE TABLE " . BOOKYOURTRAVEL_CRUISE_SCHEDULE_DAYS_TABLE . " (
						Id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
						schedule_id int(11) NOT NULL,
						day datetime NOT NULL,
						PRIMARY KEY  (Id)
					);";
			
			dbDelta($sql);	
			
			$EZSQL_ERROR = array();
		}
	}
	
	function cruise_type_add_new_meta_fields() {
		// this will add the custom meta fields to the add new term page	
		$days_of_week = BookYourTravel_Theme_Utils::get_days_of_week();
	?>
		<div class="form-field">
			<label for="term_meta[cruise_type_is_repeated]"><?php esc_html_e( 'Is cruise repeated?', 'bookyourtravel' ); ?></label>
			<select class="cruise_type_repeat_type display_block" id="term_meta[cruise_type_is_repeated]" name="term_meta[cruise_type_is_repeated]">
				<option value="0"><?php esc_html_e('No', 'bookyourtravel') ?></option>
				<option value="1"><?php esc_html_e('Daily', 'bookyourtravel') ?></option>
				<option value="2"><?php esc_html_e('Weekdays', 'bookyourtravel') ?></option>
				<option value="3"><?php esc_html_e('Weekly', 'bookyourtravel') ?></option>
				<option value="4"><?php esc_html_e('Weekly (multi-days)', 'bookyourtravel') ?></option>
			</select>
			<p class="description"><?php esc_html_e( 'Do cruises belonging to this cruise type repeat on a daily, weekly, weekday or monthly basis?','bookyourtravel' ); ?></p>
		</div>
		<div id="tr_cruise_type_day_of_week" class="form-field" style="display:none">
			<label for="term_meta[cruise_type_day_of_week]"><?php esc_html_e( 'Start day (if weekly)', 'bookyourtravel' ); ?></label>
			<select id="term_meta[cruise_type_day_of_week]" name="term_meta[cruise_type_day_of_week]">
			  <?php 
				for ($i=0; $i<count($days_of_week); $i++) { 
					$day_of_week = $days_of_week[$i]; ?>
			  <option value="<?php echo esc_attr($i); ?>"><?php echo $day_of_week; ?></option>
			  <?php } ?>
			</select>		
			<p class="description"><?php esc_html_e( 'Select a start day of the week for weekly cruise','bookyourtravel' ); ?></p>
		</div>
		<div id="tr_cruise_type_days_of_week" class="form-field" style="display:none">
			<label><?php esc_html_e( 'Start day (if weekly multi-days)', 'bookyourtravel' ); ?></label>
			  <?php 
				for ($i=0; $i<count($days_of_week); $i++) { 
					$day_of_week = $days_of_week[$i]; ?>
			<input type="checkbox" id="term_meta[cruise_type_days_of_week_<?php echo esc_attr($i); ?>]" name="term_meta[cruise_type_days_of_week][]" value="<?php echo esc_attr($i); ?>"><?php echo $day_of_week; ?>
			<?php } ?>
			<p class="description"><?php esc_html_e( 'Select multiple start days of the week for weekly cruise','bookyourtravel' ); ?></p>
		</div>
	<?php
	}

	function cruise_type_edit_meta_fields($term) {
	 
		$days_of_week = BookYourTravel_Theme_Utils::get_days_of_week();
	 
		// put the term ID into a variable
		$t_id = $term->term_id;
	 
		// retrieve the existing value(s) for this meta field. This returns an array
		$term_meta = get_option( "taxonomy_$t_id" ); ?>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="term_meta[cruise_type_is_repeated]"><?php esc_html_e( 'Is cruise repeated?', 'bookyourtravel' ); ?></label></th>
			<td>
				<select class="cruise_type_repeat_type display_table_row" id="term_meta[cruise_type_is_repeated]" name="term_meta[cruise_type_is_repeated]">
					<option <?php echo (int) $term_meta['cruise_type_is_repeated'] == 0 ? 'selected' : '' ?> value="0"><?php esc_html_e('No', 'bookyourtravel') ?></option>
					<option <?php echo (int) $term_meta['cruise_type_is_repeated'] == 1 ? 'selected' : '' ?> value="1"><?php esc_html_e('Daily', 'bookyourtravel') ?></option>
					<option <?php echo (int) $term_meta['cruise_type_is_repeated'] == 2 ? 'selected' : '' ?> value="2"><?php esc_html_e('Weekdays', 'bookyourtravel') ?></option>
					<option <?php echo (int) $term_meta['cruise_type_is_repeated'] == 3 ? 'selected' : '' ?> value="3"><?php esc_html_e('Weekly', 'bookyourtravel') ?></option>
					<option <?php echo (int) $term_meta['cruise_type_is_repeated'] == 4 ? 'selected' : '' ?> value="4"><?php esc_html_e('Weekly (multi-days)', 'bookyourtravel') ?></option>
				</select>
				<p class="description"><?php esc_html_e( 'Do cruises belonging to this cruise type repeat on a set basis?','bookyourtravel' ); ?></p>
			</td>
		</tr>
		<tr id="tr_cruise_type_day_of_week" class="form-field" style="<?php echo (int)$term_meta['cruise_type_is_repeated'] != 3 ? 'display:none' : ''; ?>">
			<th scope="row" valign="top"><label for="term_meta[cruise_type_day_of_week]"><?php esc_html_e( 'Start day (if weekly)', 'bookyourtravel' ); ?></label></th>
			<td>
				<select id="term_meta[cruise_type_day_of_week]" name="term_meta[cruise_type_day_of_week]">
				  <?php 
					for ($i=0; $i<count($days_of_week); $i++) { 
						$day_of_week = $days_of_week[$i]; ?>
				  <option <?php echo (int)$term_meta['cruise_type_day_of_week'] == $i ? 'selected' : '' ?> value="<?php echo esc_attr($i); ?>"><?php echo $day_of_week; ?></option>
				  <?php } ?>
				</select>	
				<p class="description"><?php esc_html_e( 'Select a start day of the week for weekly cruise','bookyourtravel' ); ?></p>
			</td>
		</tr>
		<tr id="tr_cruise_type_days_of_week" class="form-field" style="<?php echo (int)$term_meta['cruise_type_is_repeated'] != 4 ? 'display:none' : ''; ?>">
			<th scope="row" valign="top"><label><?php esc_html_e( 'Start day (if weekly multi-days)', 'bookyourtravel' ); ?></label></th>
			<td>
			  <?php 
				for ($i=0; $i<count($days_of_week); $i++) { 
					$day_of_week = $days_of_week[$i]; ?>
				<input <?php echo in_array($i, (array)$term_meta['cruise_type_days_of_week']) ? 'checked' : '' ?> type="checkbox" id="term_meta[cruise_type_days_of_week_<?php echo esc_attr($i); ?>]" name="term_meta[cruise_type_days_of_week][]" value="<?php echo esc_attr($i); ?>"><?php echo $day_of_week; ?>
				<?php } ?>
				<p class="description"><?php esc_html_e( 'Select multiple start days of the week for weekly cruise','bookyourtravel' ); ?></p>
			</td>
		</tr>
	<?php
	}

	function save_cruise_type_custom_meta( $term_id ) {
		if ( isset( $_POST['term_meta'] ) ) {
			$t_id = $term_id;
			$term_meta = get_option( "taxonomy_$t_id" );
			$cat_keys = array_keys( $_POST['term_meta'] );
			foreach ( $cat_keys as $key ) {
				if ( isset ( $_POST['term_meta'][$key] ) ) {
					$term_meta[$key] = $_POST['term_meta'][$key];
				}
			}
			// Save the option array.
			update_option( "taxonomy_$t_id", $term_meta );
		}
	}  

	function cruises_search_fields( $fields, &$wp_query ) {

		global $wpdb, $bookyourtravel_multi_language_count, $bookyourtravel_theme_globals;

		if ( isset($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] == 'cruise' ) {
			
			$search_only_available = false;
			if (isset($wp_query->query_vars['search_only_available']))
				$search_only_available = $wp_query->get('search_only_available');
			
			if ($search_only_available || isset($wp_query->query_vars['byt_date_from']) || isset($wp_query->query_vars['byt_date_from'])) {
				
				$date_from = null;
				if ( isset($wp_query->query_vars['byt_date_from']) )
					$date_from = date('Y-m-d', strtotime($wp_query->get('byt_date_from')));
				
				if (isset($date_from)) {
				
					$fields .= ", (
									SELECT IFNULL(SUM(cabin_count), 0) cabins_available FROM " . BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE . " schedule ";
									
					if(defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('cruise') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
						$fields .= " WHERE cruise_id = t.element_id ";
					} else {
						$fields .= " WHERE cruise_id = {$wpdb->posts}.ID ";
					}							
									
					if ($date_from != null) {
						$fields .= $wpdb->prepare( " AND ( ( %s >= start_date AND DATE_ADD(start_date, INTERVAL schedule.duration_days DAY) >= %s AND (end_date IS NULL OR end_date = '0000-00-00 00:00:00') ) OR ( %s >= start_date AND %s <= end_date	) )	", $date_from, $date_from, $date_from, $date_from);
					}
					
					$fields .= " ) cabins_available ";
					
					$fields .= ", (
									SELECT (IFNULL(SUM(adults), 0) + IFNULL(SUM(children), 0)) places_booked 
									FROM " . BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE . " bookings
									INNER JOIN " . BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE . " schedule ON bookings.cruise_schedule_id = schedule.Id ";
									
					if(defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('cruise') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
						$fields .= " WHERE cruise_id = t.element_id ";
					} else {
						$fields .= " WHERE cruise_id = {$wpdb->posts}.ID ";
					}
					
					if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout()) {
						
						$completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
						if (!empty($completed_statuses)) {
							$fields .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses . ")";
						}
					}	
									
					if ($date_from != null) {
						$fields .= $wpdb->prepare( " AND ( ( %s >= start_date AND DATE_ADD(start_date, INTERVAL schedule.duration_days DAY) >= %s AND (end_date IS NULL OR end_date = '0000-00-00 00:00:00') ) OR ( %s >= start_date AND %s <= end_date	) )	", $date_from, $date_from, $date_from, $date_from);
					}
					
					$fields .= " ) cabins_booked ";
				}
			}
			
			$date_from = null;
			if ( isset($wp_query->query_vars['byt_date_from']) )
				$date_from = date('Y-m-d', strtotime($wp_query->get('byt_date_from')));
			else 
				$date_from = date('Y-m-d', time());
			
			$date_to = null;		
			if ( isset($wp_query->query_vars['byt_date_to']) )
				$date_to = date('Y-m-d', strtotime($wp_query->get('byt_date_to') . ' -1 day'));
			
			if (isset($date_from) && $date_from == $date_to)
				$date_to = date('Y-m-d', strtotime($date_from . ' +7 day'));
			
			$fields .= ", (
							SELECT MIN(schedule.price) min_price
							FROM " . BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE . " schedule 
							INNER JOIN " . BOOKYOURTRAVEL_CRUISE_SCHEDULE_DAYS_TABLE . " schedule_days ON schedule_days.schedule_id = schedule.Id
							LEFT JOIN " . BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE . " bookings_days ON DATE(bookings_days.cruise_date) = DATE(schedule_days.day) AND bookings_days.cruise_schedule_id = schedule.Id
							WHERE 1=1 ";
							
			if(defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('cruise') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
				$fields .= " AND schedule.cruise_id = t.element_id ";
			} else {
				$fields .= " AND schedule.cruise_id = {$wpdb->posts}.ID ";
			}
							
			if (isset($date_from)) {
				$fields .= $wpdb->prepare("  AND DATE(schedule_days.day) >= %s ", $date_from);
			}
			
			if (isset($date_to)) {
				$fields .= $wpdb->prepare("  AND DATE(schedule_days.day) <= %s ", $date_to);
			}
			
			$fields .= " GROUP BY schedule_days.day, schedule.Id
						HAVING SUM(schedule.cabin_count) > (COUNT(bookings_days.Id))
						ORDER BY min_price ASC
						LIMIT 1	) cruise_price ";
							
		}

		return $fields;
	}
	
	function cruises_search_join($join) {
	
		global $wp_query, $wpdb, $bookyourtravel_multi_language_count, $bookyourtravel_theme_globals;
	
		if(defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('cruise') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$join .= " INNER JOIN " . $wpdb->prefix . "icl_translations translations_default ON translations_default.element_type = 'post_cruise' AND translations_default.language_code='" . BookYourTravel_Theme_Utils::get_default_language() . "' AND translations_default.trid = t.trid ";
		}
		
		return $join;
	}
	
	function cruises_search_where( $where, &$wp_query ) {
		
		global $wpdb;
		
		if ( isset($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] == 'cruise' ) {
			$where = str_replace('DECIMAL', 'DECIMAL(10,2)', $where);	
		}
		
		return $where;
	}

	function cruises_search_groupby( $groupby, &$wp_query ) {

		global $wpdb;
		
		if (empty($groupby))
			$groupby = " {$wpdb->posts}.ID ";
		
		if (!is_admin()) {
			if ( isset($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] == 'cruise' ) {
				
				$date_from = null;
				if ( isset($wp_query->query_vars['byt_date_from']) )
					$date_from = date('Y-m-d', strtotime($wp_query->get('byt_date_from')));
				
				$search_only_available = false;
				if (isset($wp_query->query_vars['search_only_available']))
					$search_only_available = $wp_query->get('search_only_available');
				
				$groupby .= " HAVING 1=1 ";
				
				if ($search_only_available && isset($date_from)) {				
					$groupby .= ' AND cabins_available > cabins_booked ';
					if (isset($wp_query->query_vars['byt_cabins'])) {
						$groupby .= $wpdb->prepare(" AND cabins_available >= %d ", $wp_query->query_vars['byt_cabins']);
					}
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
									$groupby .= $wpdb->prepare(" OR (cruise_price >= %d AND cruise_price <= %d ) ", $bottom, $top);
								} else {
									$groupby .= $wpdb->prepare(" OR (cruise_price >= %d ) ", $bottom);
								}
							}
						}
						
						$groupby .= ")";
						

					}
				}
				
				if ($search_only_available)
					$groupby .= " AND cruise_price > 0 ";
			}
		}
		
		return $groupby;
	}
	
	function build_cruises_search_orderby($orderby, &$wp_query) {
		
		global $wpdb, $bookyourtravel_multi_language_count;

		if (isset($wp_query->query_vars['byt_orderby']) && isset($wp_query->query_vars['byt_order'])) {

			$order = 'ASC';
			if ($wp_query->get('byt_order') == 'DESC') {
				$order = 'DESC';
			}
			
			$column = 'cruise_price';
			if ($wp_query->get('byt_orderby') == $column) {
				$orderby = $column . ' ' . $order;
			}
		}
		
		return $orderby;
	}

	function list_cruises_count($paged = 0, $per_page = 0, $orderby = '', $order = '', $location_id = 0, $cruise_types_array = array(), $cruise_tags_array = array(), $search_args = array(), $featured_only = false, $author_id = null, $include_private = false) {
		$results = $this->list_cruises($paged, $per_page, $orderby, $order, $location_id, $cruise_types_array, $cruise_tags_array, $search_args, $featured_only, $author_id, $include_private, true);
		return $results['total'];
	}

	function list_cruises($paged = 0, $per_page = -1, $orderby = '', $order = '', $location_id = 0, $cruise_types_array = array(), $cruise_tags_array = array(), $search_args = array(), $featured_only = false, $author_id = null, $include_private = false, $count_only = false ) {
		
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
			$location_ids = array_merge($descendant_location_ids, $location_ids);
		}
		
		$args = array(
			'post_type'         => 'cruise',
			'post_status'       => array('publish'),
			'posts_per_page'    => $per_page,
			'paged' 			=> $paged, 
			'orderby'           => $orderby,
			'suppress_filters' 	=> false,
			'order'				=> $order,
			'meta_query'        => array('relation' => 'AND')
		);

		if ($orderby == 'review_score') {
			$args['meta_key'] = 'review_score';
			$args['orderby'] = 'meta_value_num';
		} else if ($orderby == 'min_price') {
			$args['byt_orderby'] = 'cruise_price';
			$args['byt_order'] = $order;
		}	
		
		if (isset($search_args['keyword']) && strlen($search_args['keyword']) > 0) {
			$args['s'] = $search_args['keyword'];
		}
		
		if ($include_private) {
			$args['post_status'][] = 'draft';
			$args['post_status'][] = 'private';
		}
			
		if ( isset($search_args['rating']) && strlen($search_args['rating']) > 0 ) {
			$rating = floatval(intval($search_args['rating']) / 10);		
			if ($rating > 0 & $rating <=10) {
				$args['meta_query'][] = array(
					'relation' => 'AND',
						array(
							'key' => 'review_score',
							'value' => $rating,
							'type' => 'DECIMAL',
							'compare'   => '>=',
						),
						array(
							'key' => 'review_score',
							'compare' => 'EXISTS'
						)
				);	
			}
		}
		
		if (isset($featured_only) && $featured_only) {
			$args['meta_query'][] = array(
				'key'       => 'cruise_is_featured',
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

		if (!empty($cruise_types_array)) {
			$args['tax_query'][] = 	array(
					'taxonomy' => 'cruise_type',
					'field' => 'id',
					'terms' => $cruise_types_array,
					'operator'=> 'IN'
			);
		}	
		
		if (!empty($cruise_tags_array)) {
			$args['tax_query'][] = 	array(
					'taxonomy' => 'cruise_tag',
					'field' => 'id',
					'terms' => $cruise_tags_array,
					'operator'=> 'IN'
			);
		}
		
		$search_only_available = false;
		if ( isset($search_args['search_only_available'])) {				
			$search_only_available = $search_args['search_only_available'];
		}
		
		if ( isset($search_args['date_from']) )
			$args['byt_date_from'] = $search_args['date_from'];

		if ( isset($search_args['cabins']) )
			$args['byt_cabins'] = $search_args['cabins'];
			
		$args['search_only_available'] = $search_only_available;

		if ( isset($search_args['prices']) ) {
			$args['prices'] = $search_args['prices'];
			$args['price_range_bottom'] = $bookyourtravel_theme_globals->get_price_range_bottom();
			$args['price_range_increment'] = $bookyourtravel_theme_globals->get_price_range_increment();
			$args['price_range_count'] = $bookyourtravel_theme_globals->get_price_range_count();
		}
				
		if (count($location_ids) > 0) {
			$location_ids_str = implode(',', $location_ids);
			
			$meta_query_array = array(
				'relation' => 'OR'
			);
			
			foreach ($location_ids as $location_id) {
				$meta_query_array[] = array(
					'key' => 'locations',
					'value' => serialize(strval($location_id)),
					'compare' => 'LIKE'
				);
				$meta_query_array[] = array(
					'key' => 'locations',
					'value' => serialize($location_id),
					'compare' => 'LIKE'
				);				
			}
			
			$args['meta_query'][] = $meta_query_array;
		}
		
		add_filter('posts_where', array($this, 'cruises_search_where'), 10, 2);
		add_filter('posts_fields', array($this, 'cruises_search_fields'), 10, 2 );
		add_filter('posts_groupby', array($this, 'cruises_search_groupby'), 10, 2 );
		add_filter('posts_join', array($this, 'cruises_search_join'), 10, 2 );
		add_filter('posts_orderby', array($this, 'build_cruises_search_orderby'), 10, 2 );
		
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
		
		remove_filter('posts_where', array($this, 'cruises_search_where') );
		remove_filter('posts_fields', array($this, 'cruises_search_fields') );
		remove_filter('posts_groupby', array($this, 'cruises_search_groupby' ));
		remove_filter('posts_join', array($this, 'cruises_search_join') );
		remove_filter('posts_orderby', array($this, 'build_cruises_search_orderby') );
		
		return $results;
	}

	function list_available_cruise_schedule_entries($cruise_id, $cabin_type_id, $from_date, $from_year, $from_month, $cruise_type_is_repeated, $cruise_type_days_of_week = array(), $month_range = 3, $current_booking_id = 0) {

		global $wpdb, $bookyourtravel_theme_globals;
		
		$cruise_id = BookYourTravel_Theme_Utils::get_default_language_post_id($cruise_id, 'cruise');
		if ($cabin_type_id > 0)
			$cabin_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($cabin_type_id, 'cabin_type');

		$cruise_obj = new BookYourTravel_Cruise($cruise_id);
		$cruise_is_reservation_only = $cruise_obj->get_is_reservation_only();
		
		$completed_statuses_str = '';
		if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout() && !$cruise_is_reservation_only) {
			$completed_statuses_str = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
		}
		
		$table_name_schedule = BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE;
		$table_name_bookings = BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE;
		
		$yesterday = date('Y-m-d',strtotime("-1 days"));
		
		if ($from_date == null)
			$from_date = date('Y-m-d',time());
		
		$to_date = sprintf("%d-%d-%d", $from_year, $from_month, 1);
		$to_date = date('Y-m-d', strtotime(sprintf("+%d months", $month_range), strtotime($to_date)));
		$to_date = date("Y-m-t", strtotime($to_date)); // last day of end date month
		$to_date = date('Y-m-d', strtotime(sprintf("+%d days", 1), strtotime($to_date)));				
		
		$sql = "";
		
		if ($cruise_type_is_repeated == 0) {
			// oneoff cruises, must have start date in future in order for people to attend
			$sql = "
				SELECT schedule.*, schedule.start_date cruise_date, 
 				(SELECT COUNT(*) ct FROM $table_name_bookings bookings 
				WHERE bookings.cruise_schedule_id = schedule.Id AND DATE(bookings.cruise_date) = DATE(schedule.start_date) ";
				
			if (!empty($completed_statuses_str)) {
				$sql .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses_str . ") ";
			}
				
			if ($current_booking_id > 0) {
				$sql .= $wpdb->prepare(" AND bookings.Id != %d ", $current_booking_id);
			}
			
			$sql .= "
			) booked_cabins,
				0 num
				FROM $table_name_schedule schedule 
				WHERE cruise_id=%d AND cabin_type_id=%d AND start_date >= %s 
				HAVING cabin_count > booked_cabins ";
				
			$sql = $wpdb->prepare($sql, $cruise_id, $cabin_type_id, $from_date);
			
		} else if ($cruise_type_is_repeated == 1) {		
			// daily cruises
			
			$sql = "
				SELECT schedule.*, date_range.single_date cruise_date, 
				(SELECT COUNT(*) ct FROM $table_name_bookings bookings 
				WHERE bookings.cruise_schedule_id = schedule.Id AND DATE(bookings.cruise_date) = DATE(date_range.single_date) ";
				
			if (!empty($completed_statuses_str)) {
				$sql .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses_str . ") ";
			}
				
			if ($current_booking_id > 0) {
				$sql .= $wpdb->prepare(" AND bookings.Id != %d ", $current_booking_id);
			}
			
			$sql .= $wpdb->prepare(" ) booked_cabins,
				num
				FROM $table_name_schedule schedule
				LEFT JOIN 
				(
					SELECT ADDDATE(%s,t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) single_date, (t1.i*10 + t0.i) num ", $yesterday);
					
			$sql .= "
					FROM
					(SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t0,
					(SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
					(SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t2,
					(SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t3,
					(SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t4
					HAVING 1=1 ";

			$sql .= $wpdb->prepare(" AND single_date >= %s ", $from_date);
			$sql .= $wpdb->prepare(" AND single_date <= %s ", $to_date);
					
			$sql .= "		
				) date_range ON date_range.single_date >= %s
				WHERE cruise_id=%d AND cabin_type_id=%d AND ( ( schedule.end_date IS NULL OR schedule.end_date = '0000-00-00 00:00:00' ) OR date_range.single_date < schedule.end_date )
				HAVING schedule.cabin_count > booked_cabins ";
			
			$sql = $wpdb->prepare($sql, $from_date, $cruise_id, $cabin_type_id);

		} else if ($cruise_type_is_repeated == 2) {
		
			// weekday cruises
			$sql = "
				SELECT schedule.*, date_range.single_date cruise_date, 
				(SELECT COUNT(*) ct FROM $table_name_bookings bookings 
				WHERE bookings.cruise_schedule_id = schedule.Id AND DATE(bookings.cruise_date) = DATE(date_range.single_date) ";
				
			if (!empty($completed_statuses_str)) {
				$sql .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses_str . ") ";
			}
				
			if ($current_booking_id > 0) {
				$sql .= $wpdb->prepare(" AND bookings.Id != %d ", $current_booking_id);
			}
			
			$sql .= $wpdb->prepare(" ) booked_cabins,
				num
				FROM $table_name_schedule schedule
				LEFT JOIN 
				(
					SELECT ADDDATE(%s,t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) single_date, (t1.i*10 + t0.i) num ", $yesterday);

			$sql .= "
					FROM
					(SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t0,
					(SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
					(SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t2,
					(SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t3,
					(SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t4
					HAVING WEEKDAY(single_date) BETWEEN 0 AND 4 ";
					
			$sql .= $wpdb->prepare(" AND single_date >= %s ", $from_date);
			$sql .= $wpdb->prepare(" AND single_date <= %s ", $to_date);

			$sql .= "
				) date_range ON date_range.single_date >= %s
				WHERE cruise_id=%d AND cabin_type_id=%d AND ( ( schedule.end_date IS NULL OR schedule.end_date = '0000-00-00 00:00:00' ) OR date_range.single_date < schedule.end_date )	
				HAVING schedule.cabin_count > booked_cabins ";
			
			$sql = $wpdb->prepare($sql, $from_date, $cruise_id, $cabin_type_id);
		} else if ($cruise_type_is_repeated == 3) {
			
			$cruise_type_day_of_week = 0;
			if (count($cruise_type_days_of_week) > 0) {
				$cruise_type_day_of_week = $cruise_type_days_of_week[0];
			}
			
			// weekly cruises
			$sql = "
				SELECT schedule.*, date_range.single_date cruise_date, 
				(SELECT COUNT(*) ct FROM $table_name_bookings bookings 
				WHERE bookings.cruise_schedule_id = schedule.Id AND DATE(bookings.cruise_date) = DATE(date_range.single_date) ";
				
			if (!empty($completed_statuses_str)) {
				$sql .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses_str . ") ";
			}
				
			if ($current_booking_id > 0) {
				$sql .= $wpdb->prepare(" AND bookings.Id != %d ", $current_booking_id);
			}
			
			$sql .= $wpdb->prepare(" ) booked_cabins,
				num
				FROM $table_name_schedule schedule
				LEFT JOIN 
				(
					SELECT ADDDATE(%s,t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) single_date, (t1.i*10 + t0.i) num ", $yesterday);
					
			$sql .= $wpdb->prepare("
					FROM
					(SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t0,
					(SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
					(SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t2,
					(SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t3,
					(SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t4
					HAVING WEEKDAY(single_date) = %d ", $cruise_type_day_of_week);
					
			$sql .= $wpdb->prepare(" AND single_date >= %s ", $from_date);
			$sql .= $wpdb->prepare(" AND single_date <= %s ", $to_date);
			
			$sql .= "
				) date_range ON date_range.single_date >= %s 
				WHERE cruise_id=%d AND cabin_type_id=%d AND ( ( schedule.end_date IS NULL OR schedule.end_date = '0000-00-00 00:00:00' ) OR date_range.single_date < schedule.end_date ) 			
				HAVING schedule.cabin_count > booked_cabins ";
			
			$sql = $wpdb->prepare($sql, $from_date, $cruise_id, $cabin_type_id);	
			
		} else if ($cruise_type_is_repeated == 4) {
			
			// weekly cruises
			$sql = "
				SELECT schedule.*, date_range.single_date cruise_date, 
				(SELECT COUNT(*) ct FROM $table_name_bookings bookings 
				WHERE bookings.cruise_schedule_id = schedule.Id AND DATE(bookings.cruise_date) = DATE(date_range.single_date) ";
				
			if (!empty($completed_statuses_str)) {
				$sql .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses_str . ") ";
			}
				
			if ($current_booking_id > 0) {
				$sql .= $wpdb->prepare(" AND bookings.Id != %d ", $current_booking_id);
			}
			
			$sql .= $wpdb->prepare(" ) booked_cabins,
				num
				FROM $table_name_schedule schedule
				LEFT JOIN 
				(
					SELECT ADDDATE(%s,t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) single_date, (t1.i*10 + t0.i) num ", $yesterday);
					
			$sql .= "
					FROM
					(SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t0,
					(SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
					(SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t2,
					(SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t3,
					(SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t4 
					HAVING 1=1 ";
			
			if (count($cruise_type_days_of_week) > 0) {
				$sql .= " AND (1=0 ";

				foreach ($cruise_type_days_of_week as $day) {
					$sql .= $wpdb->prepare(" OR WEEKDAY(single_date) = %d ", $day);
				}
				$sql .= ") ";
			}
			
			$sql .= $wpdb->prepare(" AND single_date >= %s ", $from_date);
			$sql .= $wpdb->prepare(" AND single_date <= %s ", $to_date);			
					
			$sql .= "
				) date_range ON date_range.single_date >= %s 
				WHERE cruise_id=%d AND cabin_type_id=%d AND ( ( schedule.end_date IS NULL OR schedule.end_date = '0000-00-00 00:00:00' ) OR date_range.single_date < schedule.end_date ) 			
				HAVING schedule.cabin_count > booked_cabins ";
			
			$sql = $wpdb->prepare($sql, $from_date, $cruise_id, $cabin_type_id);		
		}

		return $wpdb->get_results($sql);
	}

	function get_cruise_booking($booking_id) {

		global $wpdb, $bookyourtravel_multi_language_count, $bookyourtravel_theme_globals;

		$table_name_schedule = BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE;
		$table_name_bookings = BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE;
		
		$sql = "SELECT 	DISTINCT bookings.*, 
						cruises.post_title cruise_name, 
						cabin_types.post_title cabin_type, 
						schedule.duration_days,
						bookings.total_price,
						schedule.cruise_id,
						schedule.cabin_type_id
				FROM $table_name_bookings bookings 
				INNER JOIN $table_name_schedule schedule ON schedule.Id = bookings.cruise_schedule_id ";

		if(defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('cruise') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$sql .= " INNER JOIN " . $wpdb->prefix . "icl_translations cruise_translations_default ON cruise_translations_default.element_type = 'post_cruise' AND cruise_translations_default.language_code='" . BookYourTravel_Theme_Utils::get_default_language() . "' AND cruise_translations_default.element_id = schedule.cruise_id ";			
			$sql .= " INNER JOIN " . $wpdb->prefix . "icl_translations cruise_translations ON cruise_translations.element_type = 'post_cruise' AND cruise_translations.language_code='" . ICL_LANGUAGE_CODE . "' AND cruise_translations.trid = cruise_translations_default.trid ";
		}
		
		$sql .= " INNER JOIN $wpdb->posts cruises ON ";
		if(defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('cruise') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$sql .= " cruises.ID = cruise_translations.element_id ";
		} else {
			$sql .= " cruises.ID = schedule.cruise_id ";
		}	
				
		if(defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('cruise') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$sql .= " INNER JOIN " . $wpdb->prefix . "icl_translations cabin_translations_default ON cabin_translations_default.element_type = 'post_cabin_type' AND cabin_translations_default.language_code='" . BookYourTravel_Theme_Utils::get_default_language() . "' AND cabin_translations_default.element_id = schedule.cabin_type_id ";			
			$sql .= " INNER JOIN " . $wpdb->prefix . "icl_translations cabin_translations ON cabin_translations.element_type = 'post_cabin_type' AND cabin_translations.language_code='" . ICL_LANGUAGE_CODE . "' AND cabin_translations.trid = cabin_translations_default.trid ";
		}
		
		$sql .= " INNER JOIN $wpdb->posts cabin_types ON ";
		if(defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('cruise') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$sql .= " cabin_types.ID = cabin_translations.element_id ";
		} else {
			$sql .= " cabin_types.ID = schedule.cabin_type_id ";
		}					
		
		$sql .= " WHERE cruises.post_status = 'publish' AND cabin_types.post_status = 'publish' AND bookings.Id = %d ";

		return $wpdb->get_row($wpdb->prepare($sql, $booking_id));
	}

	function delete_cruise_booking($booking_id) {

		global $wpdb;
		
		$booking = $this->get_cruise_booking($booking_id);
		$schedule = $this->get_cruise_schedule($booking->cruise_schedule_id);
		$this->clear_price_meta_cache($schedule->cruise_id);
		
		do_action('bookyourtravel_before_delete_cruise_booking', $booking_id);
		
		$table_name_bookings = BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE;
		
		$sql = "DELETE FROM $table_name_bookings
				WHERE Id = %d";
		
		$wpdb->query($wpdb->prepare($sql, $booking_id));
	}

	function list_cruise_bookings($paged = null, $per_page = 0, $orderby = 'Id', $order = 'ASC', $search_term = null, $user_id = 0, $author_id = null, $cruise_id = null ) {

		global $wpdb, $bookyourtravel_multi_language_count, $bookyourtravel_theme_globals;
		
		$table_name_schedule = BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE;
		$table_name_bookings = BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE;

		$sql = "SELECT 	DISTINCT bookings.*, 
						cruises.post_title cruise_name, 
						cabin_types.post_title cabin_type, 
						schedule.duration_days,
						bookings.total_price,
						schedule.cruise_id,
						schedule.cabin_type_id
				FROM $table_name_bookings bookings 
				INNER JOIN $table_name_schedule schedule ON schedule.Id = bookings.cruise_schedule_id ";

		if(defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('cruise') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$sql .= " INNER JOIN " . $wpdb->prefix . "icl_translations cruise_translations_default ON cruise_translations_default.element_type = 'post_cruise' AND cruise_translations_default.language_code='" . BookYourTravel_Theme_Utils::get_default_language() . "' AND cruise_translations_default.element_id = schedule.cruise_id ";			
			$sql .= " INNER JOIN " . $wpdb->prefix . "icl_translations cruise_translations ON cruise_translations.element_type = 'post_cruise' AND cruise_translations.language_code='" . ICL_LANGUAGE_CODE . "' AND cruise_translations.trid = cruise_translations_default.trid ";
		}
		
		$sql .= " INNER JOIN $wpdb->posts cruises ON ";
		if(defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('cruise') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$sql .= " cruises.ID = cruise_translations.element_id ";
		} else {
			$sql .= " cruises.ID = schedule.cruise_id ";
		}	
				
		if(defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('cruise') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$sql .= " INNER JOIN " . $wpdb->prefix . "icl_translations cabin_translations_default ON cabin_translations_default.element_type = 'post_cabin_type' AND cabin_translations_default.language_code='" . BookYourTravel_Theme_Utils::get_default_language() . "' AND cabin_translations_default.element_id = schedule.cabin_type_id ";			
			$sql .= " INNER JOIN " . $wpdb->prefix . "icl_translations cabin_translations ON cabin_translations.element_type = 'post_cabin_type' AND cabin_translations.language_code='" . ICL_LANGUAGE_CODE . "' AND cabin_translations.trid = cabin_translations_default.trid ";
		}
		
		$sql .= " INNER JOIN $wpdb->posts cabin_types ON ";
		if(defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('cruise') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$sql .= " cabin_types.ID = cabin_translations.element_id ";
		} else {
			$sql .= " cabin_types.ID = schedule.cabin_type_id ";
		}					
		
		$sql .= " WHERE cruises.post_status = 'publish' AND cabin_types.post_status = 'publish' ";
		
		if ($search_term != null && !empty($search_term)) {
			$search_term = "%" . $search_term . "%";
			$sql .= $wpdb->prepare(" AND (bookings.first_name LIKE '%s' OR bookings.last_name LIKE '%s') ", $search_term, $search_term);
		}
		
		if (isset($cruise_id) && $cruise_id > 0) {
			$sql .= $wpdb->prepare(" AND schedule.cruise_id = %d ", $cruise_id);
		}
		
		if (isset($user_id) && $user_id > 0) {
			$sql .= $wpdb->prepare(" AND bookings.user_id=%d ", $user_id);
		}
		
		if (isset($author_id) && $author_id > 0) {
			$sql .= $wpdb->prepare(" AND cruises.post_author=%d ", $author_id);
		}
		
		if(!empty($orderby) && !empty($order)) { 
			$sql.= "ORDER BY $orderby $order"; 
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

	function get_cruise_schedule_max_people($cruise_schedule_id, $cruise_id, $cabin_type_id, $date) {

		global $wpdb, $bookyourtravel_theme_globals;
			
		$table_name_schedule = BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE;
		$table_name_bookings = BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE;

		$cruise_obj = new BookYourTravel_Cruise(intval($cruise_id));
		$cruise_is_reservation_only = $cruise_obj->get_is_reservation_only();		
		
		$sql = "SELECT 	schedule.max_people, 
						(
							SELECT SUM(adults) + SUM(children) ct 
							FROM $table_name_bookings bookings 
							WHERE bookings.cruise_schedule_id = schedule.Id AND bookings.cruise_date = %s ";
							
		if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout() && !$cruise_is_reservation_only) {
			
			$completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
			if (!empty($completed_statuses)) {
				$sql .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses . ")";
			}
		}	
							
		$sql .= "		) booking_count
				FROM $table_name_schedule schedule 
				WHERE schedule.Id=%d ";
				
		if ($cruise_obj->get_type_is_repeated() == 0) {
			$sql .= " AND schedule.start_date = %s ";
		} else {
			$sql .= " AND %s >= start_date AND (%s < end_date OR end_date IS NULL OR end_date = '0000-00-00 00:00:00') ";
		}
		
		$sql = $wpdb->prepare($sql, $date, $cruise_schedule_id, $date, $date);
		
		return $wpdb->get_row($sql);
	}

	function create_cruise_schedule($season_name, $cruise_id, $cabin_type_id, $cabin_count, $start_date, $duration_days, $price, $price_child, $end_date) {

		global $wpdb;
		
		$table_name_schedule = BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE;
		$table_name_bookings = BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE;
		
		$cruise_id = BookYourTravel_Theme_Utils::get_default_language_post_id($cruise_id, 'cruise');
		$this->clear_price_meta_cache($cruise_id);
		
		$cabin_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($cabin_type_id, 'cabin_type');
		
		if ($end_date == null) {
			$sql = "INSERT INTO $table_name_schedule
					(season_name, cruise_id, cabin_type_id, cabin_count, start_date, duration_days, price, price_child, end_date)
					VALUES
					(%s, %d, %d, %d, %s, %d, %f, %f, null);";
			$sql = $wpdb->prepare($sql, $season_name, $cruise_id, $cabin_type_id, $cabin_count, $start_date, $duration_days, $price, $price_child);
		} else {
			$sql = "INSERT INTO $table_name_schedule
					(season_name, cruise_id, cabin_type_id, cabin_count, start_date, duration_days, price, price_child, end_date)
					VALUES
					(%s, %d, %d, %d, %s, %d, %f, %f, %s);";
			$sql = $wpdb->prepare($sql, $season_name, $cruise_id, $cabin_type_id, $cabin_count, $start_date, $duration_days, $price, $price_child, $end_date);
		}
		
		$wpdb->query($sql);
		
		$schedule_id = $wpdb->insert_id;
		
		$effective_end_date = date('Y-m-d', strtotime($end_date));
		
		$dates = BookYourTravel_Theme_Utils::get_dates_from_range($start_date, $effective_end_date);
		
		foreach ($dates as $date) {
			$effective_date = date('Y-m-d 12:00:00', strtotime($date));
			$this->insert_schedule_day($schedule_id, $effective_date);
		}
		
		return $schedule_id;
	}
	
	function update_cruise_schedule($schedule_id, $season_name, $cruise_id, $cabin_type_id, $cabin_count, $start_date, $duration_days, $price, $price_child, $end_date) {

		global $wpdb;
		
		$table_name_schedule = BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE;
		$table_name_bookings = BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE;
		
		$cruise_id = BookYourTravel_Theme_Utils::get_default_language_post_id($cruise_id, 'cruise');
		$this->clear_price_meta_cache($cruise_id);
		
		$cabin_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($cabin_type_id, 'cabin_type');

		if ($end_date == null) {
			$sql = "UPDATE " . BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE . "
					SET season_name=%s, cruise_id=%d, cabin_type_id=%d, cabin_count=%d, start_date=%s, duration_days=%d, price=%f, price_child=%f, end_date=null
					WHERE Id=%d";
			$sql = $wpdb->prepare($sql, $season_name, $cruise_id, $cabin_type_id, $cabin_count, $start_date, $duration_days, $price, $price_child, $schedule_id);
		} else {
			$sql = "UPDATE " . BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE . "
					SET season_name=%s, cruise_id=%d, cabin_type_id=%d, cabin_count=%d, start_date=%s, duration_days=%d, price=%f, price_child=%f, end_date=%s
					WHERE Id=%d";
			$sql = $wpdb->prepare($sql, $season_name, $cruise_id, $cabin_type_id, $cabin_count, $start_date, $duration_days, $price, $price_child, $end_date, $schedule_id);
		}

		$wpdb->query($sql);
		
		$this->clear_schedule_days($schedule_id);
		$effective_end_date = date('Y-m-d', strtotime($end_date));
		
		$dates = BookYourTravel_Theme_Utils::get_dates_from_range($start_date, $effective_end_date);
		
		foreach ($dates as $date) {
			$effective_date = date('Y-m-d 12:00:00', strtotime($date));
			$this->insert_schedule_day($schedule_id, $effective_date);
		}
	}
	
	function insert_schedule_day($schedule_id, $day) {
	
		global $wpdb;
		
		$sql = "INSERT INTO " . BOOKYOURTRAVEL_CRUISE_SCHEDULE_DAYS_TABLE . "
				(schedule_id, day)
				VALUES
				(%d, %s);";
				
		$sql = $wpdb->prepare($sql, $schedule_id, $day);
		
		$wpdb->query($sql);
	}
	
	function clear_schedule_days($schedule_id) {

		global $wpdb;
		
		$sql = "DELETE FROM " . BOOKYOURTRAVEL_CRUISE_SCHEDULE_DAYS_TABLE . " WHERE schedule_id = %d";
		$sql = $wpdb->prepare($sql, $schedule_id);
		$wpdb->query($sql);
	}

	function delete_cruise_schedule($schedule_id) {

		global $wpdb;
		
		$table_name_schedule = BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE;
		
		$schedule = $this->get_cruise_schedule($schedule_id);
		
		$this->clear_price_meta_cache($schedule->cruise_id);
		
		$sql = "DELETE FROM $table_name_schedule
				WHERE Id = %d";
		
		$this->clear_schedule_days($schedule_id);
		
		$wpdb->query($wpdb->prepare($sql, $schedule_id));	
	}

	function get_cruise_schedule($cruise_schedule_id) {

		global $wpdb, $bookyourtravel_theme_globals;
			
		$table_name_schedule = BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE;
		$table_name_bookings = BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE;
			
		$sql = "SELECT cruise_id FROM $table_name_schedule WHERE Id=%d";
		$cruise_id = $wpdb->get_var($wpdb->prepare($sql, $cruise_schedule_id));
		
		$cruise_obj = new BookYourTravel_Cruise(intval($cruise_id));
		$cruise_is_reservation_only = $cruise_obj->get_is_reservation_only();			
			
		$sql = "SELECT 	schedule.*, 
						cruises.post_title cruise_name, 
						cabin_types.post_title cabin_type,
						(
							SELECT COUNT(*) ct 
							FROM $table_name_bookings bookings 
							WHERE bookings.cruise_schedule_id = schedule.Id ";
							
		if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout() && !$cruise_is_reservation_only) {
			
			$completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
			if (!empty($completed_statuses)) {
				$sql .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses . ")";
			}
		}							
							
		$sql .= "		) has_bookings,
						IFNULL(cruise_price_meta.meta_value, 0) cruise_is_price_per_person
				FROM $table_name_schedule schedule 
				INNER JOIN $wpdb->posts cruises ON cruises.ID = schedule.cruise_id 
				INNER JOIN $wpdb->posts cabin_types ON cabin_types.ID = schedule.cabin_type_id 
				LEFT JOIN $wpdb->postmeta cruise_price_meta ON cruises.ID = cruise_price_meta.post_id AND cruise_price_meta.meta_key = 'cruise_is_price_per_person'
				WHERE schedule.Id=%d AND cruises.post_status = 'publish' AND cabin_types.post_status = 'publish'  ";
		
		$sql = $wpdb->prepare($sql, $cruise_schedule_id);
		return $wpdb->get_row($sql);
	}

	function list_cruise_schedules ($paged = null, $per_page = 0, $orderby = 'Id', $order = 'ASC', $day = 0, $month = 0, $year = 0, $cruise_id = 0, $cabin_type_id=0, $search_term = '', $author_id = null) {

		global $wpdb, $bookyourtravel_theme_globals;
		
		$cruise_id = BookYourTravel_Theme_Utils::get_default_language_post_id($cruise_id, 'cruise');
		$cabin_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($cabin_type_id, 'cabin_type');
		
		$cruise_obj = new BookYourTravel_Cruise(intval($cruise_id));
		$cruise_is_reservation_only = $cruise_obj->get_is_reservation_only();			
		
		$filter_date = '';
		if ($day > 0 || $month > 0 || $year) { 
			$filter_date .= ' AND ( 1=1 ';
			if ($day > 0)
				$filter_date .= $wpdb->prepare(" AND DAY(start_date) = %d ", $day);			
			if ($month > 0)
				$filter_date .= $wpdb->prepare(" AND MONTH(start_date) = %d ", $month);			
			if ($year > 0)
				$filter_date .= $wpdb->prepare(" AND YEAR(start_date) = %d ", $year);			
			$filter_date .= ')';		
		}

		$table_name_schedule = BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE;
		$table_name_bookings = BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE;
		
		$sql = "SELECT 	schedule.*, 
						cruises.post_title cruise_name, 
						cabin_types.post_title cabin_type,
						(
							SELECT COUNT(*) ct 
							FROM $table_name_bookings bookings 
							WHERE bookings.cruise_schedule_id = schedule.Id ";
							
		if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout() && !$cruise_is_reservation_only) {
			
			$completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
			if (!empty($completed_statuses)) {
				$sql .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses . ")";
			}
		}								
							
		$sql .= "		) has_bookings,
						IFNULL(cruise_price_meta.meta_value, 0) cruise_is_price_per_person
				FROM $table_name_schedule schedule 
				INNER JOIN $wpdb->posts cruises ON cruises.ID = schedule.cruise_id 
				INNER JOIN $wpdb->posts cabin_types ON cabin_types.ID = schedule.cabin_type_id 
				LEFT JOIN $wpdb->postmeta cruise_price_meta ON cruises.ID = cruise_price_meta.post_id AND cruise_price_meta.meta_key = 'cruise_is_price_per_person'
				WHERE cruises.post_status = 'publish' AND cabin_types.post_status = 'publish' ";
				
		if ($cruise_id > 0) {
			$sql .= $wpdb->prepare(" AND schedule.cruise_id=%d ", $cruise_id);
		}
		
		if ($cabin_type_id > 0) {
			$sql .= $wpdb->prepare(" AND schedule.cabin_type_id=%d ", $cabin_type_id);
		}
		
		if (isset($author_id)) {
			$sql .= $wpdb->prepare(" AND cruises.post_author=%d ", $author_id);
		}

		if ($filter_date != null && !empty($filter_date)) {
			$sql .= $filter_date;
		}
		
		if(!empty($orderby) & !empty($order)) { 
			$sql .= $wpdb->prepare(" ORDER BY %s %s ", $orderby, $order); 
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

	function get_cruise_schedule_price($schedule_id, $is_child_price) {

		global $wpdb;
		
		$table_name_schedule = BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE;

		$sql = "SELECT " . ($is_child_price ? "schedule.price_child" : "schedule.price") . "
				FROM $table_name_schedule schedule 
				WHERE id=%d ";	
				
		$price = $wpdb->get_var($wpdb->prepare($sql, $schedule_id));
		
		return $price;
	}

	function get_cruise_available_schedule_id($cruise_id, $cabin_type_id, $date) {

		global $wpdb;
		
		$cruise_obj = new BookYourTravel_Cruise(intval($cruise_id));
		$cruise_id = $cruise_obj->get_base_id();

		$cabin_type_obj = new BookYourTravel_Cabin_Type(intval($cabin_type_id));
		$cabin_type_id = $cabin_type_obj->get_base_id();
		
		$table_name_schedule = BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE;
		$table_name_bookings = BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE;

		$sql = "SELECT MIN(id) schedule_id
				FROM $table_name_schedule schedule 
				WHERE cruise_id=%d AND cabin_type_id=%d
				";	
				
		if ($cruise_obj->get_type_is_repeated() == 0) {
			$sql .= " AND schedule.start_date = %s ";
		}	

		$schedule_id = $wpdb->get_var($wpdb->prepare($sql, $cruise_id, $cabin_type_id, $date, $date));
		
		return $schedule_id;
	}

	function get_cruise_min_price($cruise_id, $cabin_type_id=0, $date=null) {

		global $wpdb;

		$cruise_obj = new BookYourTravel_Cruise(intval($cruise_id));
		$cruise_id = $cruise_obj->get_base_id();

		if ($cabin_type_id > 0) {
			$cabin_type_obj = new BookYourTravel_Cabin_Type(intval($cabin_type_id));
			$cabin_type_id = $cabin_type_obj->get_base_id();
		}
		
		$min_price = -1;
		
		if ($cabin_type_id == 0) {

			$last_cache_minutes = 0;
			if ($cruise_obj->is_custom_field_set('_cruise_price_cache_time', false)) {
				$last_cache_seconds = intval($cruise_obj->get_custom_field('_cruise_price_cache_time', false));
				$current_seconds = time();
				if ($last_cache_seconds > 0) {
					$last_cache_minutes = ($current_seconds - $last_cache_seconds) / (60);
				}
			}
			
			if ($last_cache_minutes > 0 && $last_cache_minutes <= 10) {
				$min_price = floatval($cruise_obj->get_custom_field('_cruise_price_cache', false));
			}
		}
		
		if ($min_price == -1) {
			
			if (!isset($date))
				$date = date('Y-m-d', time());
			
			$sql = "SELECT MIN(schedule.price) min_price
					FROM " . BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE . " schedule 
					INNER JOIN " . BOOKYOURTRAVEL_CRUISE_SCHEDULE_DAYS_TABLE . " schedule_days ON schedule_days.schedule_id = schedule.Id
					LEFT JOIN " . BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE . " bookings_days ON DATE(bookings_days.cruise_date) = DATE(schedule_days.day) AND bookings_days.cruise_schedule_id = schedule.Id
					WHERE 1=1 ";							
			
			$sql .= $wpdb->prepare(" AND schedule.cruise_id = %d ", $cruise_id);
			
			if ($cabin_type_id > 0) {
				$sql .= $wpdb->prepare(" AND schedule.cabin_type_id=%d ", $cabin_type_id);
			}
			
			if (isset($date)) {
				$sql .= $wpdb->prepare("  AND DATE(schedule_days.day) >= %s ", $date);
			}
				
			$sql .= " GROUP BY schedule_days.day, schedule.Id
					  HAVING SUM(schedule.cabin_count) > (COUNT(bookings_days.Id))
						ORDER BY min_price ASC
						LIMIT 1	";	

			$min_price = $wpdb->get_var($sql);
			
			if ($cabin_type_id == 0) {
				update_post_meta($cruise_id, '_cruise_price_cache', $min_price);
				update_post_meta($cruise_id, '_cruise_price_cache_time', time());
			}
		}
		
		return $min_price;
	}	

	function clear_price_meta_cache($cruise_id) {
		
		delete_post_meta($cruise_id, '_cruise_price_cache');
		delete_post_meta($cruise_id, '_cruise_price_cache_time');		
	}
}

global $bookyourtravel_cruise_helper;
// store the instance in a variable to be retrieved later and call init
$bookyourtravel_cruise_helper = BookYourTravel_Cruise_Helper::get_instance();
$bookyourtravel_cruise_helper->init();