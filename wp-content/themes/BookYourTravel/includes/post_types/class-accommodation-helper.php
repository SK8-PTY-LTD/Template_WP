<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * BookYourTravel_Accommodation_Helper
 *
 * Accommodation helper class
 *
 * @class    BookYourTravel_Accommodation_Helper
 * @version  7.0.0
 * @package  includes/post_types
 * @category post type helper
 * @author   BookYourTravel
 */
class BookYourTravel_Accommodation_Helper extends BookYourTravel_BaseSingleton {

	/**
	 * Member variables
	 */
	private $enable_accommodations;	
	private $accommodation_custom_meta_fields;
	private $accommodation_list_custom_meta_fields;
	private $accommodation_list_meta_box;
	
	/**
	 * Constructor
	 */
	protected function __construct() {
	
		global $bookyourtravel_theme_globals;		
		$this->enable_accommodations = $bookyourtravel_theme_globals->enable_accommodations();
		
        // our parent class might contain shared code in its constructor
        parent::__construct();		
    }

	/**
	 * Initialize class
	 */
    public function init() {

		add_action('bookyourtravel_initialize_post_types', array( $this, 'initialize_post_type' ), 0);
	
		if ($this->enable_accommodations) {
			add_action('bookyourtravel_after_delete_accommodation', array( $this, 'after_delete_accommodation' ), 10, 1);
			add_action('bookyourtravel_save_accommodation', array( $this, 'save_accommodation' ), 10, 1);
			add_action('admin_init', array($this, 'remove_unnecessary_meta_boxes') );
			add_filter('manage_edit-accommodation_columns', array( $this, 'manage_edit_accommodation_columns'), 10, 1);	
			add_action('admin_init', array( $this, 'accommodation_admin_init' ) );
			add_action('bookyourtravel_initialize_ajax', array( $this, 'initialize_ajax' ), 0);
			add_filter('manage_accommodation_posts_columns', array($this, 'columns_head'));
			add_action('manage_accommodation_posts_custom_column', array($this, 'columns_content'), 10, 2);
		}
	}
	
	function save_accommodation($post_id) {
		
		delete_post_meta_by_key('_location_accommodation_count');
		
	}
	
	function after_delete_accommodation($post_id) {
		
		delete_post_meta_by_key('_location_accommodation_count');
		
	}
	
	function columns_head($defaults) {
		$defaults['rent_type'] = __('Rent type', 'bookyourtravel');
		return $defaults;
	}
	
	function columns_content($column_name, $post_ID) {
		if ($column_name == 'rent_type') {
			$accommodation_obj = new BookYourTravel_Accommodation($post_ID);
			$rent_type = $accommodation_obj->get_rent_type();
			if ($rent_type == 1) {
				echo __('Weekly', 'bookyourtravel');
			} else if ($rent_type == 2) {
				echo __('Monthly', 'bookyourtravel');
			} else {
				echo __('Daily', 'bookyourtravel');
			}
		}
	}
		
	/**
	 * Hook in ajax handlers
	 */
	function initialize_ajax() {
	
		add_action( 'wp_ajax_accommodation_disabled_room_types_ajax_request', array( $this, 'disabled_room_types_ajax_request' ) );
		add_action( 'wp_ajax_accommodation_is_price_per_person_ajax_request', array( $this, 'is_price_per_person_ajax_request' ) );
		add_action( 'wp_ajax_accommodation_get_rent_type_ajax_request', array( $this, 'get_rent_type_ajax_request' ) );
		add_action( 'wp_ajax_accommodation_list_room_types_ajax_request', array( $this, 'list_room_types_ajax_request' ) );
		add_action( 'wp_ajax_accommodation_available_end_dates_ajax_request', array( $this, 'available_end_dates_ajax_request' ) );
		add_action( 'wp_ajax_accommodation_available_start_days_ajax_request', array( $this, 'available_start_days_ajax_request' ) );
		add_action( 'wp_ajax_accommodation_get_prices_ajax_request', array( $this, 'get_prices_ajax_request' ) );
		add_action( 'wp_ajax_accommodation_process_booking_ajax_request', array( $this, 'process_booking_ajax_request' ) );
		add_action( 'wp_ajax_accommodation_is_reservation_only_ajax_request', array($this, 'is_reservation_only_ajax_request'));
		add_action( 'wp_ajax_accommodation_list_extra_items_ajax_request', array( $this, 'list_extra_items_ajax_request' ) );
		add_action( 'wp_ajax_accommodation_available_days_ajax_request', array( $this, 'available_days_ajax_request' ) );
		add_action( 'wp_ajax_accommodation_checkin_weekday_ajax_request', array( $this, 'checkin_weekday_ajax_request') );
		add_action( 'wp_ajax_accommodation_checkout_weekday_ajax_request', array( $this, 'checkout_weekday_ajax_request') );

		add_action( 'wp_ajax_nopriv_accommodation_disabled_room_types_ajax_request', array( $this, 'disabled_room_types_ajax_request' ) );
		add_action( 'wp_ajax_nopriv_accommodation_is_price_per_person_ajax_request', array( $this, 'is_price_per_person_ajax_request' ) );
		add_action( 'wp_ajax_nopriv_accommodation_get_rent_type_ajax_request', array( $this, 'get_rent_type_ajax_request' ) );		
		add_action( 'wp_ajax_nopriv_accommodation_list_room_types_ajax_request', array( $this, 'list_room_types_ajax_request' ) );
		add_action( 'wp_ajax_nopriv_accommodation_available_end_dates_ajax_request', array( $this, 'available_end_dates_ajax_request' ) );
		add_action( 'wp_ajax_nopriv_accommodation_available_start_days_ajax_request', array( $this, 'available_start_days_ajax_request' ) );
		add_action( 'wp_ajax_nopriv_accommodation_get_prices_ajax_request', array( $this, 'get_prices_ajax_request' ) );
		add_action( 'wp_ajax_nopriv_accommodation_process_booking_ajax_request', array( $this, 'process_booking_ajax_request' ) );		
		add_action( 'wp_ajax_nopriv_accommodation_is_reservation_only_ajax_request', array($this, 'is_reservation_only_ajax_request'));
		add_action( 'wp_ajax_nopriv_accommodation_list_extra_items_ajax_request', array( $this, 'list_extra_items_ajax_request' ) );
		add_action( 'wp_ajax_nopriv_accommodation_available_days_ajax_request', array( $this, 'available_days_ajax_request' ) );
		add_action( 'wp_ajax_nopriv_accommodation_checkin_weekday_ajax_request', array( $this, 'checkin_weekday_ajax_request') );
		add_action( 'wp_ajax_nopriv_accommodation_checkout_weekday_ajax_request', array( $this, 'checkout_weekday_ajax_request') );
		
		add_action( 'wp_ajax_accommodation_admin_get_fields_ajax_request', array( $this, 'admin_get_fields_ajax_request') );
		add_action( 'wp_ajax_accommodation_admin_available_days_ajax_request', array( $this, 'admin_available_days_ajax_request') );
		add_action( 'wp_ajax_accommodation_admin_available_start_days_ajax_request', array( $this, 'admin_available_start_days_ajax_request') );
		add_action( 'wp_ajax_accommodation_admin_available_end_dates_ajax_request', array( $this, 'admin_available_end_dates_ajax_request') );
		
		add_action( 'wp_ajax_frontend_delete_accommodation_vacancy_ajax_request', array( $this, 'frontend_delete_accommodation_vacancy_ajax_request') );		
	}
	
	function frontend_delete_accommodation_vacancy_ajax_request() {
	
		global $bookyourtravel_theme_globals, $bookyourtravel_accommodation_helper;
		
		if ( isset($_REQUEST) ) {
		
			$nonce = wp_kses($_REQUEST['nonce'], array());
			
			if (wp_verify_nonce( $nonce, 'bookyourtravel_nonce' )) {

				$vacancy_id = isset($_REQUEST['vacancy_id']) ? intval(wp_kses($_REQUEST['vacancy_id'], array())) : 0;

				if ($vacancy_id > 0) {

					$bookyourtravel_accommodation_helper->delete_accommodation_vacancy($vacancy_id);	

					echo '1';
				}				
			
			}			
		}
		
		die();	
	}
	
	function admin_available_days_ajax_request() {
		
		global $bookyourtravel_theme_globals;
		
		if ( isset($_REQUEST) ) {
		
			$nonce = wp_kses($_REQUEST['nonce'], array());
			
			if (wp_verify_nonce( $nonce, 'bookyourtravel_nonce' )) {
			
				$accommodation_id = isset($_REQUEST['accommodation_id']) ? intval(wp_kses($_REQUEST['accommodation_id'], array())) : 0;	
				$room_type_id = isset($_REQUEST['room_type_id']) ? intval(wp_kses($_REQUEST['room_type_id'], array())) : 0;	
				$month = isset($_REQUEST['month']) ? intval(wp_kses($_REQUEST['month'], array())) : 0;	
				$year = isset($_REQUEST['year']) ? intval(wp_kses($_REQUEST['year'], array())) : 0;
				$current_booking_id = isset($_REQUEST['current_booking_id']) ? intval(wp_kses($_REQUEST['current_booking_id'], array())) : 0;
			
				if ($accommodation_id > 0) {
					
					$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
					
					$raw_dates = $this->list_accommodation_vacancy_start_dates($accommodation_id, $room_type_id, $month, $year, 3, $current_booking_id);
					
					$available_dates = array();
					
					for ($i = 0;$i<count($raw_dates);$i++) {
											
						$prices_row = $this->get_accommodation_prices($raw_dates[$i]->single_date, $accommodation_id, $room_type_id, $current_booking_id);
						
						if ($prices_row != null) {
						
							$available_date = new stdClass();
						
							$available_date->single_date = $raw_dates[$i]->single_date;
							$available_date->price_per_day = number_format ($prices_row->price_per_day, $price_decimal_places, ".", "");
							$available_date->price_per_day_child = number_format ($prices_row->price_per_day_child, $price_decimal_places, ".", "");
							$available_date->weekend_price_per_day = isset($prices_row->weekend_price_per_day) ? number_format ($prices_row->weekend_price_per_day, $price_decimal_places, ".", "") : 0;
							$available_date->weekend_price_per_day_child = isset($prices_row->weekend_price_per_day_child) ? number_format ($prices_row->weekend_price_per_day_child, $price_decimal_places, ".", "") : 0;
							$available_date->is_weekend = BookYourTravel_Theme_Utils::is_weekend($raw_dates[$i]->single_date);
							
							$available_dates[] = $available_date;
						}
					}
					
					echo json_encode($available_dates);
				}
			}
		}
		
		die();
	}
	
	function admin_available_end_dates_ajax_request() {

		if ( isset($_REQUEST) ) {

			$nonce = wp_kses($_REQUEST['nonce'], array());
			
			if (wp_verify_nonce( $nonce, 'bookyourtravel_nonce' )) {
			
				$accommodation_id = isset($_REQUEST['accommodation_id']) ? intval(wp_kses($_REQUEST['accommodation_id'], array())) : 0;	
				$room_type_id = isset($_REQUEST['room_type_id']) ? intval(wp_kses($_REQUEST['room_type_id'], array())) : 0;	
				$month = isset($_REQUEST['month']) ? intval(wp_kses($_REQUEST['month'], array())) : 0;	
				$year = isset($_REQUEST['year']) ? intval(wp_kses($_REQUEST['year'], array())) : 0;	
				$start_date = isset($_REQUEST['start_date']) ? sanitize_text_field($_REQUEST['start_date']) : null;
				$day = isset($_REQUEST['day']) ? intval(wp_kses($_REQUEST['day'], array())) : 0;	
					
				if ($accommodation_id > 0) {

					$accommodation_obj = new BookYourTravel_Accommodation($accommodation_id);
					$accommodation_checkin_week_day = $accommodation_obj->get_checkin_week_day();
					$accommodation_checkout_week_day = $accommodation_obj->get_checkout_week_day();
					$accommodation_min_days_stay = $accommodation_obj->get_min_days_stay();
					$accommodation_max_days_stay = $accommodation_obj->get_max_days_stay();
				
					$min_stay_date = null;
					if ($accommodation_min_days_stay > 0) {
						$min_stay_date = date('Y-m-d', strtotime($start_date . " +" . $accommodation_min_days_stay . " days"));
					}
					
					$max_stay_date = null;
					if ($accommodation_max_days_stay > 0) {
						$max_stay_date = date('Y-m-d', strtotime($start_date . " +" . $accommodation_max_days_stay . " days"));
					}
				
					$raw_dates = $this->list_accommodation_vacancy_end_dates($start_date, $accommodation_id, $room_type_id, $month, $year, $day, 3);
					$available_dates = array();
					
					for ($i = 0; $i < count($raw_dates); $i++) {
						
						$pass = true;
						if ($accommodation_checkout_week_day > -1 && date('w', strtotime($raw_dates[$i]->single_date)) != $accommodation_checkout_week_day) {
							$pass = false;
						}
						
						if ($min_stay_date > date('Y-m-d', strtotime($raw_dates[$i]->single_date))) {
							$pass = false;
						}
						
						if ($max_stay_date != null && $max_stay_date < date('Y-m-d', strtotime($raw_dates[$i]->single_date))) {
							$pass = false;
						}
						
						if ($pass) {
							$available_dates[] = $raw_dates[$i];
						}
					}
					
					echo json_encode($available_dates);
				}
			}
		}
		
		die();
	}
	
	function admin_available_start_days_ajax_request() {
		
		global $bookyourtravel_theme_globals;
		
		if ( isset($_REQUEST) ) {
		
			$nonce = wp_kses($_REQUEST['nonce'], array());
			
			if (wp_verify_nonce( $nonce, 'bookyourtravel_nonce' )) {
			
				$accommodation_id = isset($_REQUEST['accommodation_id']) ? intval(wp_kses($_REQUEST['accommodation_id'], array())) : 0;	
				$room_type_id = isset($_REQUEST['room_type_id']) ? intval(wp_kses($_REQUEST['room_type_id'], array())) : 0;	
				$month = isset($_REQUEST['month']) ? intval(wp_kses($_REQUEST['month'], array())) : 0;	
				$year = isset($_REQUEST['year']) ? intval(wp_kses($_REQUEST['year'], array())) : 0;	
			
				if ($accommodation_id > 0) {
				
					$accommodation_obj = new BookYourTravel_Accommodation($accommodation_id);
					$accommodation_checkin_week_day = $accommodation_obj->get_checkin_week_day();
					
					$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
					
					$raw_dates = $this->list_accommodation_vacancy_start_dates($accommodation_id, $room_type_id, $month, $year, 3);
					
					$available_dates = array();
					
					for ($i = 0;$i<count($raw_dates);$i++) {
					
						if ($accommodation_checkin_week_day < 0 || date('w', strtotime($raw_dates[$i]->single_date)) == $accommodation_checkin_week_day) {
					
							$available_date = new stdClass();
							
							$prices_row = $this->get_accommodation_prices($raw_dates[$i]->single_date, $accommodation_id, $room_type_id);
							
							if ($prices_row != null) {
							
								$available_date->single_date = $raw_dates[$i]->single_date;
								$available_date->price_per_day = number_format ($prices_row->price_per_day, $price_decimal_places, ".", "");
								$available_date->price_per_day_child = number_format ($prices_row->price_per_day_child, $price_decimal_places, ".", "");
								$available_date->weekend_price_per_day = isset($prices_row->weekend_price_per_day) ? number_format ($prices_row->weekend_price_per_day, $price_decimal_places, ".", "") : 0;
								$available_date->weekend_price_per_day_child = isset($prices_row->weekend_price_per_day_child) ? number_format ($prices_row->weekend_price_per_day_child, $price_decimal_places, ".", "") : 0;
								$available_date->is_weekend = BookYourTravel_Theme_Utils::is_weekend($raw_dates[$i]->single_date);
								
								$available_dates[] = $available_date;
							}
						}
					}
					
					echo json_encode($available_dates);
				}
			}
		}
		
		die();
	}
	
	function admin_get_fields_ajax_request() {
	
		if ( isset($_REQUEST) ) {
		
			$nonce = wp_kses($_REQUEST['nonce'], array());
			$accommodation_id = intval(wp_kses($_REQUEST['accommodationId'], array()));
			$room_type_id = 0;
			if (isset($_REQUEST['roomTypeId'])) {
				$room_type_id = intval(wp_kses($_REQUEST['roomTypeId'], array()));
			}
			
			if (wp_verify_nonce( $nonce, 'bookyourtravel_nonce' )) {
				
				$accommodation_obj = new BookYourTravel_Accommodation((int)$accommodation_id);
				$room_type_obj = null;
				if ($room_type_id > 0) {
					$room_type_obj = new BookYourTravel_Room_Type((int)$room_type_id);
				}

				$fields = new stdClass();
				
				$fields->rent_type = $accommodation_obj->get_rent_type();
				$fields->checkin_week_day = $accommodation_obj->get_checkin_week_day();
				$fields->checkout_week_day = $accommodation_obj->get_checkout_week_day();
				$fields->disabled_room_types = $accommodation_obj->get_disabled_room_types();
				$fields->is_price_per_person = $accommodation_obj->get_is_price_per_person();
				$fields->min_days_stay = $accommodation_obj->get_min_days_stay();
				$fields->max_days_stay = $accommodation_obj->get_max_days_stay();
				$fields->children_stay_free = $accommodation_obj->get_count_children_stay_free();

				if ($room_type_obj == null) {
					$fields->min_adult_count = $accommodation_obj->get_min_adult_count();
					$fields->max_adult_count = $accommodation_obj->get_max_adult_count();
					$fields->min_child_count = $accommodation_obj->get_min_child_count();
					$fields->max_child_count = $accommodation_obj->get_max_child_count();					
				} else {
					$fields->min_adult_count = $room_type_obj->get_min_adult_count();
					$fields->max_adult_count = $room_type_obj->get_max_adult_count();
					$fields->min_child_count = $room_type_obj->get_min_child_count();
					$fields->max_child_count = $room_type_obj->get_max_child_count();										
				}
				
				$fields->room_types = array();

				if (!$fields->disabled_room_types) {
				
					$room_type_ids = $accommodation_obj->get_room_types();					
					if ($accommodation_obj && $room_type_ids && count($room_type_ids) > 0) { 				
					
						for ( $i = 0; $i < count($room_type_ids); $i++ ) {
						
							$temp_id = $room_type_ids[$i];
							$room_type_obj = new BookYourTravel_Room_Type(intval($temp_id));
							$room_type_temp = new stdClass();
							$room_type_temp->name = $room_type_obj->get_title();
							$room_type_temp->id = $room_type_obj->get_id();
							$fields->room_types[] = $room_type_temp;					
						}
					}
				}
				
				echo json_encode($fields);	
			}
		}
		
		// Always die in functions echoing ajax content
		die();	
	
	}
	
	function checkin_weekday_ajax_request() {
	
		if ( isset($_REQUEST) ) {
			$nonce = wp_kses($_REQUEST['nonce'], array());
			$accommodation_id = intval(wp_kses($_REQUEST['accommodationId'], array()));
			if (wp_verify_nonce( $nonce, 'bookyourtravel_nonce' )) {
				$checkin_week_day = get_post_meta( $accommodation_id, 'accommodation_checkin_week_day', true );
				echo $checkin_week_day && $checkin_week_day > -1 ? $checkin_week_day : -1;
			}
		}
		
		// Always die in functions echoing ajax content
		die();	
	}
	
	function checkout_weekday_ajax_request() {
	
		if ( isset($_REQUEST) ) {
			$nonce = wp_kses($_REQUEST['nonce'], array());
			$accommodation_id = intval(wp_kses($_REQUEST['accommodationId'], array()));
			if (wp_verify_nonce( $nonce, 'bookyourtravel_nonce' )) {
				$checkout_week_day = get_post_meta( $accommodation_id, 'accommodation_checkout_week_day', true );
				echo $checkout_week_day && $checkout_week_day > -1 ? $checkout_week_day : -1;
			}
		}
		
		// Always die in functions echoing ajax content
		die();	
	}
		
	function disabled_room_types_ajax_request() {
	
		if ( isset($_REQUEST) ) {
		
			$nonce = wp_kses($_REQUEST['nonce'], array());
			$accommodation_id = intval(wp_kses($_REQUEST['accommodationId'], array()));
			if (wp_verify_nonce( $nonce, 'bookyourtravel_nonce' )) {
				$disabled_room_types = get_post_meta( $accommodation_id, 'accommodation_disabled_room_types', true );
				echo $disabled_room_types ? 1 : 0;
			}
		}
		
		// Always die in functions echoing ajax content
		die();
	}

	function list_extra_items_ajax_request() {	

		if ( isset($_REQUEST) ) {
		
			$nonce = wp_kses($_REQUEST['nonce'], array());
			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {
				
				global $bookyourtravel_extra_item_helper;
				
				$extra_items = $bookyourtravel_extra_item_helper->list_extra_items_by_post_type('accommodation');

				echo json_encode($extra_items);			
			}
		}
		
		// Always die in functions echoing ajax content
		die();	
	}
	
	function list_room_types_ajax_request() {

		if ( isset($_REQUEST) ) {
		
			$nonce = wp_kses($_REQUEST['nonce'], array());
			$accommodation_id = intval(wp_kses($_REQUEST['accommodationId'], array()));
			if (wp_verify_nonce( $nonce, 'bookyourtravel_nonce' )) {
			
				$accommodation_obj = new BookYourTravel_Accommodation((int)$accommodation_id);
				$room_types = array();			
				$room_type_ids = $accommodation_obj->get_room_types();
				if ($accommodation_obj && $room_type_ids && count($room_type_ids) > 0) { 				
					for ( $i = 0; $i < count($room_type_ids); $i++ ) {
						$temp_id = $room_type_ids[$i];
						$room_type_obj = new BookYourTravel_Room_Type(intval($temp_id));
						$room_type_temp = new stdClass();
						$room_type_temp->name = $room_type_obj->get_title();
						$room_type_temp->id = $room_type_obj->get_id();
						$room_types[] = $room_type_temp;					
					}
				}
				
				echo json_encode($room_types);
			}
		}
		
		// Always die in functions echoing ajax content
		die();		
	}

	function get_rent_type_ajax_request() {
	
		if ( isset($_REQUEST) ) {
		
			$nonce = wp_kses($_REQUEST['nonce'], array());
			$accommodation_id = intval(wp_kses($_REQUEST['accommodationId'], array()));
			if (wp_verify_nonce( $nonce, 'bookyourtravel_nonce' )) {
				$rent_type = get_post_meta( $accommodation_id, 'accommodation_rent_type', true );
				echo $rent_type ? $rent_type : 0;
			}
		}
		
		// Always die in functions echoing ajax content
		die();
	}	
	
	function is_price_per_person_ajax_request() {
	
		if ( isset($_REQUEST) ) {
		
			$nonce = wp_kses($_REQUEST['nonce'], array());
			$accommodation_id = intval(wp_kses($_REQUEST['accommodationId'], array()));
			if (wp_verify_nonce( $nonce, 'bookyourtravel_nonce' )) {
				$is_price_per_person = get_post_meta( $accommodation_id, 'accommodation_is_price_per_person', true );
				echo $is_price_per_person ? 1 : 0;
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
				
					$accommodation_obj = new BookYourTravel_Accommodation($booking_object->accommodation_id);
					if (isset($booking_object->room_type_id))
						$room_type_obj = new BookYourTravel_Room_Type($booking_object->room_type_id);
					
					if ($accommodation_obj != null) {
					
						if ($add_captcha_to_forms && $c_val_s != ($c_val_1 + $c_val_2)) {
							echo 'captcha_error';
							die();
						} else {
						
							$booking_object->Id = $this->create_accommodation_booking($current_user->ID, $booking_object);
							
							echo $booking_object->Id;

							$use_woocommerce_for_checkout = $bookyourtravel_theme_globals->use_woocommerce_for_checkout();
							$is_reservation_only = get_post_meta( $booking_object->accommodation_id, 'accommodation_is_reservation_only', true );

							if (!$use_woocommerce_for_checkout || !BookYourTravel_Theme_Utils::is_woocommerce_active() || $is_reservation_only) {
							
								// only send email if we are not proceeding to WooCommerce checkout or if woocommerce is not active at all.
								$admin_email = get_bloginfo('admin_email');
								$admin_name = get_bloginfo('name');
								
								$subject = esc_html__('New accommodation booking', 'bookyourtravel');
							
								$message = esc_html__('New accommodation booking: ', 'bookyourtravel');
								$message .= "\n\n";
								$message .= sprintf(esc_html__("Accommodation: %s", 'bookyourtravel'), $accommodation_obj->get_title()) . "\n\n";
								
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
								
								if ($booking_object->total_accommodation_price > 0) {
								
									$total_accommodation_price_string = '';
									if (!$show_currency_symbol_after) { 
										$total_accommodation_price_string = $default_currency_symbol . ' ' . number_format_i18n( $booking_object->total_accommodation_price, $price_decimal_places );
									} else {
										$total_accommodation_price_string = number_format_i18n( $booking_object->total_accommodation_price, $price_decimal_places ) . ' ' . $default_currency_symbol;
									}
								
									$total_accommodation_price_string = preg_replace("/&nbsp;/",' ',$total_accommodation_price_string);
								
									$message .= sprintf(esc_html__("Reservation total: %s", 'bookyourtravel'), $total_accommodation_price_string) . "\n\n";
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

								$contact_emails = trim(get_post_meta($booking_object->accommodation_id, 'accommodation_contact_email', true ));
								
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
	
	function get_prices_ajax_request() {
	
		global $bookyourtravel_theme_globals;
		
		if ( isset($_REQUEST) ) {
			$nonce = wp_kses($_REQUEST['nonce'], array());
			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {

				$accommodation_id = isset($_REQUEST['accommodationId']) ? intval(wp_kses($_REQUEST['accommodationId'], array())) : 0;	
				$room_type_id = isset($_REQUEST['roomTypeId']) ? intval(wp_kses($_REQUEST['roomTypeId'], array())) : 0;	
				$dateValue = isset($_REQUEST['dateValue']) ? wp_kses($_REQUEST['dateValue'], array()) : null;	
				$dateTime = strtotime($dateValue);
				$dateValue = date('Y-m-d', $dateTime);
		
				$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();

				if ($accommodation_id > 0) {				
					
					$prices_row = $this->get_accommodation_prices($dateValue, $accommodation_id, $room_type_id);
					
					$price_per_day = number_format ($prices_row->price_per_day, $price_decimal_places, ".", "");
					$price_per_day_child = number_format ($prices_row->price_per_day_child, $price_decimal_places, ".", "");
					$weekend_price_per_day = isset($prices_row->weekend_price_per_day) ? number_format ($prices_row->weekend_price_per_day, $price_decimal_places, ".", "") : 0;
					$weekend_price_per_day_child = isset($prices_row->weekend_price_per_day_child) ? number_format ($prices_row->weekend_price_per_day_child, $price_decimal_places, ".", "") : 0;
					$is_weekend = BookYourTravel_Theme_Utils::is_weekend($dateValue);
					
					$prices = array( 
						'price_per_day' => $price_per_day, 
						'price_per_day_child' => $price_per_day_child,
						'weekend_price_per_day' => $weekend_price_per_day, 
						'weekend_price_per_day_child' => $weekend_price_per_day_child,
						'is_weekend' => $is_weekend
					);
					
					echo json_encode($prices);
				}
			}
		}
		
		die();
	}
		
	function available_days_ajax_request() {
		
		global $bookyourtravel_theme_globals;
		
		if ( isset($_REQUEST) ) {
		
			$nonce = wp_kses($_REQUEST['nonce'], array());
			
			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {
			
				$accommodation_id = isset($_REQUEST['accommodation_id']) ? intval(wp_kses($_REQUEST['accommodation_id'], array())) : 0;	
				$room_type_id = isset($_REQUEST['room_type_id']) ? intval(wp_kses($_REQUEST['room_type_id'], array())) : 0;	
				$month = isset($_REQUEST['month']) ? intval(wp_kses($_REQUEST['month'], array())) : 0;	
				$year = isset($_REQUEST['year']) ? intval(wp_kses($_REQUEST['year'], array())) : 0;	
			
				if ($accommodation_id > 0) {
					
					$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
					
					$raw_dates = $this->list_accommodation_vacancy_start_dates($accommodation_id, $room_type_id, $month, $year, 3);
					
					$available_dates = array();
					
					for ($i = 0;$i<count($raw_dates);$i++) {
											
						$prices_row = $this->get_accommodation_prices($raw_dates[$i]->single_date, $accommodation_id, $room_type_id);
						
						if ($prices_row != null) {
						
							$available_date = new stdClass();
						
							$available_date->single_date = $raw_dates[$i]->single_date;
							$available_date->price_per_day = number_format ($prices_row->price_per_day, $price_decimal_places, ".", "");
							$available_date->price_per_day_child = number_format ($prices_row->price_per_day_child, $price_decimal_places, ".", "");
							$available_date->weekend_price_per_day = isset($prices_row->weekend_price_per_day) ? number_format ($prices_row->weekend_price_per_day, $price_decimal_places, ".", "") : 0;
							$available_date->weekend_price_per_day_child = isset($prices_row->weekend_price_per_day_child) ? number_format ($prices_row->weekend_price_per_day_child, $price_decimal_places, ".", "") : 0;
							$available_date->is_weekend = BookYourTravel_Theme_Utils::is_weekend($raw_dates[$i]->single_date);
							
							$available_dates[] = $available_date;
						}
					}
					
					echo json_encode($available_dates);
				}
			}
		}
		
		die();
	}
	
	function available_start_days_ajax_request() {
		
		global $bookyourtravel_theme_globals;
		
		if ( isset($_REQUEST) ) {
		
			$nonce = wp_kses($_REQUEST['nonce'], array());
			
			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {
			
				$accommodation_id = isset($_REQUEST['accommodation_id']) ? intval(wp_kses($_REQUEST['accommodation_id'], array())) : 0;	
				$room_type_id = isset($_REQUEST['room_type_id']) ? intval(wp_kses($_REQUEST['room_type_id'], array())) : 0;	
				$month = isset($_REQUEST['month']) ? intval(wp_kses($_REQUEST['month'], array())) : 0;	
				$year = isset($_REQUEST['year']) ? intval(wp_kses($_REQUEST['year'], array())) : 0;	
			
				if ($accommodation_id > 0) {
				
					$accommodation_obj = new BookYourTravel_Accommodation($accommodation_id);
					$accommodation_checkin_week_day = $accommodation_obj->get_checkin_week_day();
					
					$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
					
					$raw_dates = $this->list_accommodation_vacancy_start_dates($accommodation_id, $room_type_id, $month, $year, 3);
					
					$available_dates = array();
					
					for ($i = 0;$i<count($raw_dates);$i++) {
					
//						if ($accommodation_checkin_week_day < 0 || date('w', strtotime($raw_dates[$i]->single_date)) == $accommodation_checkin_week_day) {
					
							$prices_row = $this->get_accommodation_prices($raw_dates[$i]->single_date, $accommodation_id, $room_type_id);
							
							if ($prices_row != null) {
								
								$available_date = new stdClass();							
								$available_date->single_date = $raw_dates[$i]->single_date;
								$available_date->price_per_day = number_format ($prices_row->price_per_day, $price_decimal_places, ".", "");
								$available_date->price_per_day_child = number_format ($prices_row->price_per_day_child, $price_decimal_places, ".", "");
								$available_date->weekend_price_per_day = isset($prices_row->weekend_price_per_day) ? number_format ($prices_row->weekend_price_per_day, $price_decimal_places, ".", "") : 0;
								$available_date->weekend_price_per_day_child = isset($prices_row->weekend_price_per_day_child) ? number_format ($prices_row->weekend_price_per_day_child, $price_decimal_places, ".", "") : 0;
								$available_date->is_weekend = BookYourTravel_Theme_Utils::is_weekend($raw_dates[$i]->single_date);
								
								$available_dates[] = $available_date;
							}
//						}
					}
					
					echo json_encode($available_dates);
				}
			}
		}
		
		die();
	}

	function available_end_dates_ajax_request() {

		if ( isset($_REQUEST) ) {
			$nonce = wp_kses($_REQUEST['nonce'], array());
			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {

				$accommodation_id = isset($_REQUEST['accommodation_id']) ? intval(wp_kses($_REQUEST['accommodation_id'], array())) : 0;	
				$room_type_id = isset($_REQUEST['room_type_id']) ? intval(wp_kses($_REQUEST['room_type_id'], array())) : 0;	
				$month = isset($_REQUEST['month']) ? intval(wp_kses($_REQUEST['month'], array())) : 0;	
				$year = isset($_REQUEST['year']) ? intval(wp_kses($_REQUEST['year'], array())) : 0;	
				$start_date = isset($_REQUEST['start_date']) ? sanitize_text_field($_REQUEST['start_date']) : null;
				$day = isset($_REQUEST['day']) ? intval(wp_kses($_REQUEST['day'], array())) : 0;	
					
				if ($accommodation_id > 0) {

					$accommodation_obj = new BookYourTravel_Accommodation($accommodation_id);
					$accommodation_checkin_week_day = $accommodation_obj->get_checkin_week_day();
					$accommodation_checkout_week_day = $accommodation_obj->get_checkout_week_day();
					$accommodation_min_days_stay = $accommodation_obj->get_min_days_stay();
					$accommodation_max_days_stay = $accommodation_obj->get_max_days_stay();
				
					$min_stay_date = null;
					if ($accommodation_min_days_stay > 0) {
						$min_stay_date = date('Y-m-d', strtotime($start_date . " +" . $accommodation_min_days_stay . " days"));
					}
					
					$max_stay_date = null;
					if ($accommodation_max_days_stay > 0) {
						$max_stay_date = date('Y-m-d', strtotime($start_date . " +" . $accommodation_max_days_stay . " days"));
					}
				
					$raw_dates = $this->list_accommodation_vacancy_end_dates($start_date, $accommodation_id, $room_type_id, $month, $year, $day, 3);
					$available_dates = array();
					
					for ($i = 0; $i < count($raw_dates); $i++) {
						
						$pass = true;
						
						if ($max_stay_date != null && $max_stay_date < date('Y-m-d', strtotime($raw_dates[$i]->single_date))) {
							$pass = false;
						}
						
						if ($pass) {
							$available_dates[] = $raw_dates[$i];
						}
					}
					
					echo json_encode($available_dates);
				}
			}
		}
		
		die();
	}
	
	function is_reservation_only_ajax_request() {
		if ( isset($_REQUEST) ) {
			$nonce = wp_kses($_REQUEST['nonce'], array());
			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {

				$accommodation_id = intval(wp_kses($_REQUEST['accommodation_id'], array()));	
				$is_reservation_only = get_post_meta( $accommodation_id, 'accommodation_is_reservation_only', true );
				$is_reservation_only = isset($is_reservation_only) ? (int)$is_reservation_only : 0;
				
				echo $is_reservation_only;
			}
		}
		
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
			$booking_object->total_accommodation_price = 0;
			$booking_object->total_extra_items_price = 0;

			$booking_object->accommodation_id = isset($_REQUEST['accommodation_id']) ? intval(wp_kses($_REQUEST['accommodation_id'], array())) : 0;
			$booking_object->room_type_id = isset($_REQUEST['room_type_id']) ? intval(wp_kses($_REQUEST['room_type_id'], array())) : 0;
			$booking_object->room_count = isset($_REQUEST['room_count']) ? intval(wp_kses($_REQUEST['room_count'], array())) : 1;
			$booking_object->adults = isset($_REQUEST['adults']) ? intval(wp_kses($_REQUEST['adults'], array())) : 1;
			$booking_object->children = isset($_REQUEST['children']) ? intval(wp_kses($_REQUEST['children'], array())) : 0;
			$booking_object->date_from = isset($_REQUEST['date_from']) ? date('Y-m-d', strtotime(sanitize_text_field($_REQUEST['date_from']))) : null;
			$booking_object->date_to = isset($_REQUEST['date_to']) ? date('Y-m-d', strtotime(sanitize_text_field($_REQUEST['date_to']))) : null;
			
			$booking_object->room_count = isset($booking_object->room_count) && $booking_object->room_count > 0 ? $booking_object->room_count : 1;

			$booking_object->accommodation_id = BookYourTravel_Theme_Utils::get_default_language_post_id($booking_object->accommodation_id, 'accommodation');
			if ($booking_object->room_type_id > 0)
				$booking_object->room_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($booking_object->room_type_id, 'room_type'); 
			
			$accommodation_count_children_stay_free = get_post_meta($booking_object->accommodation_id, 'accommodation_count_children_stay_free', true );
			$accommodation_count_children_stay_free = isset($accommodation_count_children_stay_free) ? intval($accommodation_count_children_stay_free) : 0;

			$booking_object->billable_children = $booking_object->children - $accommodation_count_children_stay_free;
			$booking_object->billable_children = $booking_object->billable_children > 0 ? $booking_object->billable_children : 0;
			
			$booking_object->total_accommodation_price = $this->calculate_total_accommodation_price($booking_object->accommodation_id, $booking_object->room_type_id, $booking_object->date_from, $booking_object->date_to, $booking_object->room_count, $booking_object->adults, $booking_object->billable_children, $booking_object->Id);
			$booking_object->total_price += $booking_object->total_accommodation_price;

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
	
	function accommodation_admin_init() {
	
		global $bookyourtravel_room_type_helper, $post;
		
		$room_types = array();
		$room_type_query = $bookyourtravel_room_type_helper->list_room_types(null, array('publish'));
		if ($room_type_query->have_posts()) {
			while ($room_type_query->have_posts()) {
				$room_type_query->the_post();
				global $post;				
				$room_types[] = array('value' => $post->ID, 'label' => $post->post_title);
			}
		}
		
		wp_reset_postdata();	
		
		$days_of_week = BookYourTravel_Theme_Utils::get_php_days_of_week();
		
		$stay_start_days = array();
		$stay_start_days[] = array('value' => -1, 'label' => __('Any day', 'bookyourtravel'));

		foreach ($days_of_week as $key => $label) {
			$stay_start_days[] = array('value' => $key, 'label' => $label);
		}
		
		$rent_types = array();
		$rent_types[] = array('value' => 0, 'label' => __('Daily', 'bookyourtravel'));
		$rent_types[] = array('value' => 1, 'label' => __('Weekly', 'bookyourtravel'));
		$rent_types[] = array('value' => 2, 'label' => __('Monthly', 'bookyourtravel'));
		
		if ($this->enable_accommodations) {

			$this->accommodation_custom_meta_fields = array(
				array( // Post ID select box
					'label'	=> esc_html__('Is featured', 'bookyourtravel'), // <label>
					'desc'	=> esc_html__('Show in lists where only featured items are shown.', 'bookyourtravel'), // description
					'id'	=> 'accommodation_is_featured', // field id and name
					'type'	=> 'checkbox', // type of field
				),
				array(
					'label'	=> esc_html__('Rent type', 'bookyourtravel'),
					'desc'	=> esc_html__('Are you renting this accommodation on a daily (default), weekly or monthly basis?', 'bookyourtravel'),
					'id'	=> 'accommodation_rent_type',
					'type'	=> 'select',
					'options' => $rent_types
				),
				array( // Post ID select box
					'label'	=> esc_html__('Disable room types?', 'bookyourtravel'), // <label>
					'desc'	=> esc_html__('Is the accommodation bookable as one entity (lodges, houses etc) or does it provide individual room booking (hotel/motel style)?', 'bookyourtravel'), // description
					'id'	=> 'accommodation_disabled_room_types', // field id and name
					'type'	=> 'checkbox', // type of field
				),
				array( // Post ID select box
					'label'	=> esc_html__('Associated room types?', 'bookyourtravel'), // <label>
					'desc'	=> '', // description
					'id'	=>  'room_types', // field id and name
					'type'	=> 'checkbox_group', // type of field
					'options' => $room_types // post types to display, options are prefixed with their post type
				),
				array( 
					'label'	=> esc_html__('Priced per person?', 'bookyourtravel'), // <label>
					'desc'	=> esc_html__('Is price calculated per person (adult, child)? If not then calculations are done per room or per entity (if room types are disabled).', 'bookyourtravel'), // description
					'id'	=> 'accommodation_is_price_per_person', // field id and name
					'type'	=> 'checkbox', // type of field
				),
				array( 
					'label'	=> esc_html__('Is for reservation only?', 'bookyourtravel'), // <label>
					'desc'	=> esc_html__('If this option is checked, then this particular accommodation will not be processed via WooCommerce even if WooCommerce is in use.', 'bookyourtravel'), // description
					'id'	=> 'accommodation_is_reservation_only', // field id and name
					'type'	=> 'checkbox', // type of field
				),
				array( // Post ID select box
					'label'	=> esc_html__('Hide inquiry form?', 'bookyourtravel'), // <label>
					'desc'	=> esc_html__('Do you want to not show the inquiry form and inquiry button in right hand sidebar for this accommodation?', 'bookyourtravel'), // description
					'id'	=> 'accommodation_hide_inquiry_form', // field id and name
					'type'	=> 'checkbox', // type of field
				),
				array(
					'label'	=> esc_html__('Star count', 'bookyourtravel'),
					'desc'	=> '',
					'id'	=> 'accommodation_star_count',
					'type'	=> 'slider',
					'min'	=> '0',
					'max'	=> '5',
					'step'	=> '1'
				),
				array( // Taxonomy Select box
					'label'	=> esc_html__('Facilities', 'bookyourtravel'), // <label>
					// the description is created in the callback function with a link to Manage the taxonomy terms
					'id'	=> 'facility', // field id and name, needs to be the exact name of the taxonomy
					'type'	=> 'tax_checkboxes' // type of field
				),
				array( // Taxonomy Select box
					'label'	=> esc_html__('Tags', 'bookyourtravel'), // <label>
					// the description is created in the callback function with a link to Manage the taxonomy terms
					'id'	=> 'acc_tag', // field id and name, needs to be the exact name of the taxonomy
					'type'	=> 'tax_checkboxes' // type of field
				),
				array( // Taxonomy Select box
					'label'	=> esc_html__('Accommodation type', 'bookyourtravel'), // <label>
					// the description is created in the callback function with a link to Manage the taxonomy terms
					'id'	=> 'accommodation_type', // field id and name, needs to be the exact name of the taxonomy
					'type'	=> 'tax_select' // type of field
				),
				array( // Post ID select box
					'label'	=> esc_html__('Location', 'bookyourtravel'), // <label>
					'desc'	=> '', // description
					'id'	=> 'accommodation_location_post_id', // field id and name
					'type'	=> 'post_select', // type of field
					'post_type' => array('location') // post types to display, options are prefixed with their post type
				),
				array(
					'label'	=> esc_html__('Minimum days stay', 'bookyourtravel'),
					'desc'	=> esc_html__('What is the minimum number of days accommodation can be booked for?', 'bookyourtravel'),
					'id'	=> 'accommodation_min_days_stay',
					'type'	=> 'slider',
					'min'	=> '1',
					'max'	=> '30',
					'step'	=> '1'
				),
				array(
					'label'	=> esc_html__('Maximum days stay', 'bookyourtravel'),
					'desc'	=> esc_html__('What is the maximum number of days accommodation can be booked for? Leave as 0 to ignore.', 'bookyourtravel'),
					'id'	=> 'accommodation_max_days_stay',
					'type'	=> 'slider',
					'min'	=> '0',
					'max'	=> '30',
					'step'	=> '1'
				),
				array(
					'label'	=> esc_html__('Allowed check-in day of the week for stay', 'bookyourtravel'),
					'desc'	=> esc_html__('What is the day of the week that visitors can check-in to the accommodation on? Do not select to ignore.', 'bookyourtravel'),
					'id'	=> 'accommodation_checkin_week_day',
					'type'	=> 'select',
					'options' => $stay_start_days
				),
				array(
					'label'	=> esc_html__('Allowed check-out day of the week for stay', 'bookyourtravel'),
					'desc'	=> esc_html__('What is the day of the week that visitors can check-out from the accommodation on? Do not select to ignore.', 'bookyourtravel'),
					'id'	=> 'accommodation_checkout_week_day',
					'type'	=> 'select',
					'options' => $stay_start_days
				),
				array(
					'label'	=> esc_html__('Maximum adult count', 'bookyourtravel'),
					'desc'	=> esc_html__('How many adults are allowed in the accommodation?', 'bookyourtravel'),
					'id'	=> 'accommodation_max_count',
					'type'	=> 'slider',
					'min'	=> '1',
					'max'	=> '30',
					'step'	=> '1',
					'std'	=> '10'
				),
				array(
					'label'	=> esc_html__('Maximum child count', 'bookyourtravel'),
					'desc'	=> esc_html__('How many children are allowed in the accommodation?', 'bookyourtravel'),
					'id'	=> 'accommodation_max_child_count',
					'type'	=> 'slider',
					'min'	=> '1',
					'max'	=> '30',
					'step'	=> '1'
				),
				array(
					'label'	=> esc_html__('Minimum adult count', 'bookyourtravel'),
					'desc'	=> esc_html__('What is the fewest number of adults required in the accommodation?', 'bookyourtravel'),
					'id'	=> 'accommodation_min_count',
					'type'	=> 'slider',
					'min'	=> '1',
					'max'	=> '30',
					'step'	=> '1'
				),
				array(
					'label'	=> esc_html__('Minimum child count', 'bookyourtravel'),
					'desc'	=> esc_html__('What is the fewest number of children required in the accommodation?', 'bookyourtravel'),
					'id'	=> 'accommodation_min_child_count',
					'type'	=> 'slider',
					'min'	=> '0',
					'max'	=> '30',
					'step'	=> '1'
				),
				array(
					'label'	=> esc_html__('Count children stay free', 'bookyourtravel'),
					'desc'	=> esc_html__('How many kids stay free before we charge a fee?', 'bookyourtravel'),
					'id'	=> 'accommodation_count_children_stay_free',
					'type'	=> 'slider',
					'min'	=> '0',
					'max'	=> '5',
					'step'	=> '1'
				),
				array( // Repeatable & Sortable Text inputs
					'label'	=> esc_html__('Gallery images', 'bookyourtravel'), // <label>
					'desc'	=> esc_html__('A collection of images to be used in slider/gallery on single page', 'bookyourtravel'), // description
					'id'	=> 'accommodation_images', // field id and name
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
					'label'	=> esc_html__('Address', 'bookyourtravel'),
					'desc'	=> '',
					'id'	=> 'accommodation_address',
					'type'	=> 'text'
				),
				array(
					'label'	=> esc_html__('Website address', 'bookyourtravel'),
					'desc'	=> '',
					'id'	=> 'accommodation_website_address',
					'type'	=> 'text'
				),
				array(
					'label'	=> esc_html__('Availability extra text', 'bookyourtravel'),
					'desc'	=> esc_html__('Extra text shown on availability tab above the book now area.', 'bookyourtravel'),
					'id'	=> 'accommodation_availability_text',
					'type'	=> 'textarea'
				),
				array(
					'label'	=> esc_html__('Contact email addresses', 'bookyourtravel'),
					'desc'	=> esc_html__('Override admin contact email address by specifying contact email addresses for this accommodation. If specifying multiple email addresses, separate each address with a semi-colon ;', 'bookyourtravel'),
					'id'	=> 'accommodation_contact_email',
					'type'	=> 'text'
				),
				array(
					'label'	=> esc_html__('Latitude coordinates', 'bookyourtravel'),
					'desc'	=> esc_html__('Latitude coordinates for use with google map (leave blank to not use)', 'bookyourtravel'),
					'id'	=> 'accommodation_latitude',
					'type'	=> 'text'
				),
				array(
					'label'	=> esc_html__('Longitude coordinates', 'bookyourtravel'),
					'desc'	=> esc_html__('Longitude coordinates for use with google map (leave blank to not use)', 'bookyourtravel'),
					'id'	=> 'accommodation_longitude',
					'type'	=> 'text'
				),	
			);
			
			global $default_accommodation_extra_fields;

			$accommodation_extra_fields = of_get_option('accommodation_extra_fields');
			if (!is_array($accommodation_extra_fields) || count($accommodation_extra_fields) == 0)
				$accommodation_extra_fields = $default_accommodation_extra_fields;
							
			foreach ($accommodation_extra_fields as $accommodation_extra_field) {
				$field_is_hidden = isset($accommodation_extra_field['hide']) ? intval($accommodation_extra_field['hide']) : 0;
				
				if (!$field_is_hidden) {
					$extra_field = null;
					$field_label = isset($accommodation_extra_field['label']) ? $accommodation_extra_field['label'] : '';
					$field_id = isset($accommodation_extra_field['id']) ? $accommodation_extra_field['id'] : '';
					$field_type = isset($accommodation_extra_field['type']) ? $accommodation_extra_field['type'] :  '';
					
					if ($field_type == 'textarea')
						$field_type = 'editor';
					
					if (!empty($field_label) && !empty($field_id) && !empty($field_type)) {
						$extra_field = array(
							'label'	=> $field_label,
							'desc'	=> '',
							'id'	=> 'accommodation_' . $field_id,
							'type'	=> $field_type
						);
					}

					if ($extra_field) 
						$this->accommodation_custom_meta_fields[] = $extra_field;
				}
			}
			
			$sort_by_columns = array();
			$sort_by_columns[] = array('value' => 'title', 'label' => esc_html__('Accommodation title', 'bookyourtravel'));
			$sort_by_columns[] = array('value' => 'ID', 'label' => esc_html__('Accommodation ID', 'bookyourtravel'));
			$sort_by_columns[] = array('value' => 'date', 'label' => esc_html__('Publish date', 'bookyourtravel'));
			$sort_by_columns[] = array('value' => 'rand', 'label' => esc_html__('Random', 'bookyourtravel'));
			$sort_by_columns[] = array('value' => 'comment_count', 'label' => esc_html__('Comment count', 'bookyourtravel'));
			
			$this->accommodation_list_custom_meta_fields = array(
				array( // Taxonomy Select box
					'label'	=> esc_html__('Accomodation type', 'bookyourtravel'), // <label>
					// the description is created in the callback function with a link to Manage the taxonomy terms
					'id'	=> 'accommodation_type', // field id and name, needs to be the exact name of the taxonomy
					'type'	=> 'tax_select' // type of field
				),
				array( // Taxonomy Select box
					'label'	=> esc_html__('Tags', 'bookyourtravel'), // <label>
					// the description is created in the callback function with a link to Manage the taxonomy terms
					'id'	=> 'acc_tag', // field id and name, needs to be the exact name of the taxonomy
					'type'	=> 'tax_checkboxes' // type of field
				),
				array( // Taxonomy Select box
					'label'	=> esc_html__('Location', 'bookyourtravel'), // <label>
					// the description is created in the callback function with a link to Manage the taxonomy terms
					'id'	=> 'accommodation_list_location_post_id', // field id and name
					'type'	=> 'post_select', // type of field
					'post_type' => array('location') // post types to display, options are prefixed with their post type
				),
				array( // Select box
					'label'	=> esc_html__('Sort by field', 'bookyourtravel'), // <label>
					// the description is created in the callback function with a link to Manage the taxonomy terms
					'id'	=> 'accommodation_list_sort_by', // field id and name, needs to be the exact name of the taxonomy
					'type'	=> 'select', // type of field
					'options' => $sort_by_columns
				),
				array( // Post ID select box
					'label'	=> esc_html__('Sort descending?', 'bookyourtravel'), // <label>
					'desc'	=> esc_html__('If checked, will sort accommodations in descending order', 'bookyourtravel'), // description
					'id'	=> 'accommodation_list_sort_descending', // field id and name
					'type'	=> 'checkbox', // type of field
				),				
				array( // Post ID select box
					'label'	=> esc_html__('Show featured only?', 'bookyourtravel'), // <label>
					'desc'	=> esc_html__('If checked, will list featured accommodations only', 'bookyourtravel'), // description
					'id'	=> 'accommodation_list_show_featured_only', // field id and name
					'type'	=> 'checkbox', // type of field
				),
			);
		}
		
		new custom_add_meta_box( 'accommodation_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->accommodation_custom_meta_fields, 'accommodation' );

		$this->accommodation_list_meta_box = new custom_add_meta_box( 'accommodation_list_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->accommodation_list_custom_meta_fields, 'page' );	
		remove_action( 'add_meta_boxes', array( $this->accommodation_list_meta_box, 'add_box' ) );
		add_action('add_meta_boxes', array( $this, 'accommodation_list_add_meta_boxes' ) );
	}
	
	function accommodation_list_add_meta_boxes() {
	
		global $post;
		$template_file = get_post_meta($post->ID,'_wp_page_template',true);
		if ($template_file == 'page-accommodation-list.php') {
			add_meta_box( $this->accommodation_list_meta_box->id, $this->accommodation_list_meta_box->title, array( $this->accommodation_list_meta_box, 'meta_box_callback' ), 'page', 'normal', 'high' );
		}
	}
	
	function initialize_post_type() {
	
		global $bookyourtravel_theme_globals;		
		$this->enable_accommodations = $bookyourtravel_theme_globals->enable_accommodations();
		
		if ($this->enable_accommodations) {
		
			$this->register_accommodation_post_type();
			$this->register_accommodation_tag_taxonomy();		
			$this->register_accommodation_type_taxonomy();
			$this->create_accommodation_extra_tables();		
		}
	}
	
	function manage_edit_accommodation_columns($columns) {
	
		return $columns;
	}

	function remove_unnecessary_meta_boxes() {

		remove_meta_box('tagsdiv-acc_tag', 'accommodation', 'side');		
		remove_meta_box('tagsdiv-accommodation_type', 'accommodation', 'side');		
	}
	
	function register_accommodation_tag_taxonomy() {
	
		$labels = array(
				'name'              => esc_html__( 'Accommodation Tags', 'taxonomy general name', 'bookyourtravel' ),
				'singular_name'     => esc_html__( 'Accommodation Tag', 'taxonomy singular name', 'bookyourtravel' ),
				'search_items'      => esc_html__( 'Search Accommodation tags', 'bookyourtravel' ),
				'all_items'         => esc_html__( 'All Accommodation tags', 'bookyourtravel' ),
				'parent_item'                => null,
				'parent_item_colon'          => null,
				'edit_item'         => esc_html__( 'Edit Accommodation tag', 'bookyourtravel' ),
				'update_item'       => esc_html__( 'Update Accommodation tag', 'bookyourtravel' ),
				'add_new_item'      => esc_html__( 'Add New Accommodation tag', 'bookyourtravel' ),
				'new_item_name'     => esc_html__( 'New Accommodation tag Name', 'bookyourtravel' ),
				'separate_items_with_commas' => esc_html__( 'Separate Accommodation tags with commas', 'bookyourtravel' ),
				'add_or_remove_items'        => esc_html__( 'Add or remove Accommodation tags', 'bookyourtravel' ),
				'choose_from_most_used'      => esc_html__( 'Choose from the most used Accommodation tags', 'bookyourtravel' ),
				'not_found'                  => esc_html__( 'No Accommodation tags found.', 'bookyourtravel' ),
				'menu_name'         => esc_html__( 'Accommodation Tags', 'bookyourtravel' ),
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
			
		register_taxonomy( 'acc_tag', array( 'accommodation' ), $args );
	}	

	function register_accommodation_type_taxonomy() {
	
		$labels = array(
				'name'              => esc_html__( 'Accommodation Types', 'bookyourtravel' ),
				'singular_name'     => __( 'Accommodation Type', 'bookyourtravel' ),
				'search_items'      => esc_html__( 'Search Accommodation Types', 'bookyourtravel' ),
				'all_items'         => esc_html__( 'All Accommodation Types', 'bookyourtravel' ),
				'parent_item'                => null,
				'parent_item_colon'          => null,
				'edit_item'         => esc_html__( 'Edit Accommodation Type', 'bookyourtravel' ),
				'update_item'       => esc_html__( 'Update Accommodation Type', 'bookyourtravel' ),
				'add_new_item'      => esc_html__( 'Add New Accommodation Type', 'bookyourtravel' ),
				'new_item_name'     => esc_html__( 'New Accommodation Type Name', 'bookyourtravel' ),
				'separate_items_with_commas' => esc_html__( 'Separate accommodation types with commas', 'bookyourtravel' ),
				'add_or_remove_items'        => esc_html__( 'Add or remove accommodation types', 'bookyourtravel' ),
				'choose_from_most_used'      => esc_html__( 'Choose from the most used accommodation types', 'bookyourtravel' ),
				'not_found'                  => esc_html__( 'No accommodation types found.', 'bookyourtravel' ),
				'menu_name'         => esc_html__( 'Accommodation Types', 'bookyourtravel' ),
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
			
		register_taxonomy( 'accommodation_type', array( 'accommodation' ), $args );
	}
	
	function register_accommodation_post_type() {
		
		global $bookyourtravel_theme_globals;
		
		$accommodations_permalink_slug = $bookyourtravel_theme_globals->get_accommodations_permalink_slug();
			
		$labels = array(
			'name'                => esc_html__( 'Accommodations', 'bookyourtravel' ),
			'singular_name'       => esc_html__( 'Accommodation', 'bookyourtravel' ),
			'menu_name'           => esc_html__( 'Accommodations', 'bookyourtravel' ),
			'all_items'           => esc_html__( 'All Accommodations', 'bookyourtravel' ),
			'view_item'           => esc_html__( 'View Accommodation', 'bookyourtravel' ),
			'add_new_item'        => esc_html__( 'Add New Accommodation', 'bookyourtravel' ),
			'add_new'             => esc_html__( 'New Accommodation', 'bookyourtravel' ),
			'edit_item'           => esc_html__( 'Edit Accommodation', 'bookyourtravel' ),
			'update_item'         => esc_html__( 'Update Accommodation', 'bookyourtravel' ),
			'search_items'        => esc_html__( 'Search Accommodations', 'bookyourtravel' ),
			'not_found'           => esc_html__( 'No Accommodations found', 'bookyourtravel' ),
			'not_found_in_trash'  => esc_html__( 'No Accommodations found in Trash', 'bookyourtravel' ),
		);
		
		$args = array(
			'label'               => esc_html__( 'Accommodation', 'bookyourtravel' ),
			'description'         => esc_html__( 'Accommodation information pages', 'bookyourtravel' ),
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
			'rewrite' => array('slug' => $accommodations_permalink_slug),
		);
		
		register_post_type( 'accommodation', $args );
	}

	function create_accommodation_extra_tables() {
	
		global $bookyourtravel_installed_version, $force_recreate_tables;

		if ($bookyourtravel_installed_version != BOOKYOURTRAVEL_VERSION || $force_recreate_tables) {
		
			global $wpdb;
			
			$sql = "CREATE TABLE " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_TABLE . " (
						Id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
						season_name varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
						start_date datetime NOT NULL,
						end_date datetime NOT NULL,
						accommodation_id bigint(20) unsigned NOT NULL,
						room_type_id bigint(20) unsigned NOT NULL DEFAULT '0',
						room_count int(11) NOT NULL,
						price_per_day decimal(16,2) NOT NULL,
						price_per_day_child decimal(16,2) NOT NULL,
						weekend_price_per_day decimal(16,2) NULL,
						weekend_price_per_day_child decimal(16,2) NULL,
						PRIMARY KEY  (Id)
					);";

			// we do not execute sql directly we are calling dbDelta which cant migrate database
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
			
			global $EZSQL_ERROR;
			
			$EZSQL_ERROR = array();
			
			$sql = "CREATE TABLE " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE . " (
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
						special_requirements text CHARACTER SET utf8 COLLATE utf8_bin NULL,
						other_fields text CHARACTER SET utf8 COLLATE utf8_bin NULL,
						extra_items text CHARACTER SET utf8 COLLATE utf8_bin NULL,
						room_count int(11) NOT NULL DEFAULT '0',
						adults int(11) NOT NULL DEFAULT '0',
						children int(11) NOT NULL DEFAULT '0',
						total_accommodation_price decimal(16,2) NOT NULL DEFAULT '0.00',
						total_extra_items_price decimal(16,2) NOT NULL DEFAULT '0.00',
						total_price decimal(16,2) NOT NULL DEFAULT '0.00',
						accommodation_id bigint(20) unsigned NOT NULL,
						room_type_id bigint(20) unsigned NOT NULL,
						date_from datetime NOT NULL,
						date_to datetime NOT NULL,
						user_id bigint(20) unsigned DEFAULT NULL,
						created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
						woo_order_id bigint(20) NULL,
						woo_status varchar(255) NULL,
						cart_key VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '' NOT NULL,
						PRIMARY KEY  (Id)
					);";
					
			dbDelta($sql);
			
			$EZSQL_ERROR = array();
			
			$sql = "CREATE TABLE " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_DAYS_TABLE . " (
						Id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
						vacancy_id int(11) NOT NULL,
						day datetime NOT NULL,
						PRIMARY KEY  (Id)
					);";
			
			dbDelta($sql);	
			
			$EZSQL_ERROR = array();
			
			$sql = "CREATE TABLE " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_DAYS_TABLE . " (
						Id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
						booking_id int(11) NOT NULL,
						day datetime NOT NULL,
						PRIMARY KEY  (Id)
					);";
			
			dbDelta($sql);
			
			$EZSQL_ERROR = array();
		}
	}
	
	/**
	 * Search helper methods
	 */
	function build_accommodations_search_fields( $fields, &$wp_query ) {

		global $wpdb, $bookyourtravel_multi_language_count, $bookyourtravel_theme_globals;

		if (isset($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] == 'accommodation' ) {

			$search_only_available = false;
			if (isset($wp_query->query_vars['search_only_available']))
				$search_only_available = $wp_query->get('search_only_available');
				
			$date_today = date('Y-m-d', time());				
				
			$date_from = null;			
			if ( isset($wp_query->query_vars['byt_date_from']) ) {
				$date_from = date('Y-m-d', strtotime($wp_query->get('byt_date_from')));
			}
			
			$date_to = null;		
			if ( isset($wp_query->query_vars['byt_date_to']) )
				$date_to = date('Y-m-d', strtotime($wp_query->get('byt_date_to') . ' -1 day'));
			
			if (isset($date_from) && $date_from == $date_to)
				$date_to = date('Y-m-d', strtotime($wp_query->get('byt_date_from') . ' +7 day'));
		
			if ($search_only_available) {
				
				if ((isset($date_from) || isset($date_to))) {
				
					$fields .= ", (
									SELECT IFNULL(SUM(room_count), 0) rooms_available FROM " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_TABLE;

							
					if(defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('accommodation') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
						$fields .= " WHERE accommodation_id = translations_default.element_id ";
					} else {
						$fields .= " WHERE accommodation_id = {$wpdb->posts}.ID ";
					}
									
					if ($date_from != null && $date_to != null) {
						$fields .= $wpdb->prepare(" AND (%s BETWEEN start_date AND end_date OR %s BETWEEN start_date AND end_date) ", $date_from, $date_to);
					} else if ($date_from != null) {
						$fields .= $wpdb->prepare(" AND %s BETWEEN start_date AND end_date ", $date_from);
					} else if ($date_to != null) {
						$fields .= $wpdb->prepare(" AND %s BETWEEN start_date AND end_date ", $date_to);
					}

					$fields .= $wpdb->prepare(" AND end_date >= %s ", $date_today);
					
					$fields .= " ) rooms_available ";
					
					$fields .= ", (
									SELECT IFNULL(SUM(room_count), 0) rooms_booked FROM " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE;
									
					if(defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('accommodation') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
						$fields .= " WHERE accommodation_id = translations_default.element_id ";
					} else {
						$fields .= " WHERE accommodation_id = {$wpdb->posts}.ID ";
					}
									
					if ($date_from != null && $date_to != null) {
						$fields .= $wpdb->prepare(" AND (%s BETWEEN date_from AND date_to OR %s BETWEEN date_from AND date_to) ", $date_from, $date_to);
					} else if ($date_from != null) {
						$fields .= $wpdb->prepare(" AND %s BETWEEN date_from AND date_to ", $date_from);
					} else if ($date_to != null) {
						$fields .= $wpdb->prepare(" AND %s BETWEEN date_from AND date_to ", $date_to);
					}						
					
					$fields .= " ) rooms_booked ";
					
				} else {
					$fields .= ", 1 rooms_available, 0 rooms_booked ";
				}
			} else {
				$fields .= ", 1 rooms_available, 0 rooms_booked ";
			}

			if (!isset($wp_query->query_vars['byt_count_only'])) {
				
				$fields .= ", 
							(
								SELECT MIN(vacancies.price_per_day) min_price_per_day
								FROM " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_TABLE . " vacancies ";
								
				if ($bookyourtravel_theme_globals->calculate_real_time_prices_for_lists()) {
				
					$fields .=	"INNER JOIN " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_DAYS_TABLE . " vacancy_days ON vacancy_days.vacancy_id = vacancies.Id
								 LEFT JOIN " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_DAYS_TABLE . " booking_days ON booking_days.day = vacancy_days.day
								 LEFT JOIN " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE . " bookings ON bookings.Id = booking_days.booking_id AND bookings.accommodation_id=vacancies.accommodation_id ";
				}
				
				$fields .= "	WHERE 1=1 ";
				
				if(defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('accommodation') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
					$fields .= " AND vacancies.accommodation_id = translations_default.element_id ";
				} else {
					$fields .= " AND vacancies.accommodation_id = {$wpdb->posts}.ID ";
				}
				
				if ($date_from != null && $date_to != null) {
					$fields .= $wpdb->prepare(" AND vacancies.start_date <= %s AND vacancies.end_date >= %s ", $date_from, $date_to);
				} else if ($date_from != null) {
					$fields .= $wpdb->prepare(" AND vacancies.start_date <= %s AND vacancies.end_date >= %s ", $date_from, $date_from);
				} else if ($date_to != null) {
					$fields .= $wpdb->prepare(" AND vacancies.end_date >= %s ", $date_to);
				} 	

				if ($date_from != null && $date_to != null) {
					$fields .= $wpdb->prepare(" AND (%s BETWEEN vacancies.start_date AND vacancies.end_date OR %s BETWEEN vacancies.start_date AND vacancies.end_date) ", $date_from, $date_to);
				} else if ($date_from != null) {
					$fields .= $wpdb->prepare(" AND %s BETWEEN vacancies.start_date AND vacancies.end_date ", $date_from);
				} else if ($date_to != null) {
					$fields .= $wpdb->prepare(" AND %s BETWEEN vacancies.start_date AND vacancies.end_date ", $date_to);
				}

				$fields .= $wpdb->prepare(" AND vacancies.end_date >= %s ", $date_today);				

				if ($bookyourtravel_theme_globals->calculate_real_time_prices_for_lists()) {
					$fields .= "	GROUP BY vacancy_days.day, vacancies.Id
									HAVING SUM(vacancies.room_count) > IFNULL(SUM(bookings.room_count), 0) ";
				}
				
				$fields .= "   	ORDER BY min_price_per_day ASC
								LIMIT 1			
							) accommodation_price ";
			} else {
				$fields .= ", 0 accommodation_price ";
			}
		}
		
		return $fields;
	}

	function build_accommodations_search_where( $where, &$wp_query ) {
		
		global $wpdb;
		
		if (isset($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] == 'accommodation' ) {
		
			$where = str_replace('DECIMAL', 'DECIMAL(10,2)', $where);		
		
			if ( isset($wp_query->query_vars['byt_disabled_room_types']) ) {
				$needed_where_part = '';
				$where_array = explode('AND', $where);
				foreach ($where_array as $where_part) {
					if (strpos($where_part,'post_id IS NULL') !== false) {
						// found where part where disabled_room_types is checked for NULL
						$needed_where_part = $where_part;
						break;
					}
				}

				if (!empty($needed_where_part)) {
					$prefix = str_replace("post_id IS NULL","",$needed_where_part);
					$prefix = str_replace(")", "", $prefix);
					$prefix = str_replace("(", "", $prefix);
					$prefix = trim($prefix);
					$where = str_replace("{$prefix}post_id IS NULL", "({$prefix}post_id IS NULL OR CAST({$prefix}meta_value AS SIGNED) = '0')", $where);
				}
			}
			
			if (isset($wp_query->query_vars['s']) && !empty($wp_query->query_vars['s']) && isset($wp_query->query_vars['byt_location_ids']) && isset($wp_query->query_vars['s']) ) {
				$needed_where_part = '';
				$where_array = explode('AND', $where);
				foreach ($where_array as $where_part) {
					if (strpos($where_part,"meta_key = 'accommodation_location_post_id'") !== false) {
						// found where part where disabled_room_types is checked for NULL
						$needed_where_part = $where_part;
						break;
					}
				}
				
				if (!empty($needed_where_part)) {
					$prefix = str_replace("meta_key = 'accommodation_location_post_id'","",$needed_where_part);
					$prefix = str_replace(")", "", $prefix);
					$prefix = str_replace("(", "", $prefix);
					$prefix = trim($prefix);

					$location_ids = $wp_query->query_vars['byt_location_ids'];
					$location_ids_str = "'".implode("','", $location_ids)."'";				
					$location_search_param_part = "{$prefix}meta_key = 'accommodation_location_post_id' AND CAST({$prefix}meta_value AS CHAR) IN ($location_ids_str)";							
				
					$where = str_replace($location_search_param_part, "1=1", $where);
					
					$post_content_part = "OR ($wpdb->posts.post_content LIKE '%" . $wp_query->get('s') . "%')";
					$where = str_replace($post_content_part, $post_content_part . " OR ($location_search_param_part) ", $where);
				}
			}
		}
		
		return $where;
	}

	function build_accommodations_search_groupby( $groupby, &$wp_query ) {

		global $wpdb;
		
		if (empty($groupby)) {
			$groupby = " {$wpdb->posts}.ID ";
		}
		
		if (!is_admin()) {
		
			$search_only_available = false;
			if (isset($wp_query->query_vars['search_only_available'])) {
				$search_only_available = $wp_query->get('search_only_available');
			}
			
			if ( isset($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] == 'accommodation' ) {
				
				$date_from = null;
				if ( isset($wp_query->query_vars['byt_date_from']) ) {
					$date_from = date('Y-m-d', strtotime($wp_query->get('byt_date_from')));
				}
				
				$date_to = null;		
				if ( isset($wp_query->query_vars['byt_date_to']) ) {
					$date_to = date('Y-m-d', strtotime($wp_query->get('byt_date_to') . ' -1 day'));
				}
				
				if (isset($date_from) && $date_from == $date_to) {
					$date_to = date('Y-m-d', strtotime($wp_query->get('byt_date_from') . ' +7 day'));
				}
				
				$groupby .= ' HAVING 1=1 ';
				
				if ($search_only_available) {				
					$groupby .= ' AND rooms_available > rooms_booked ';		
					
					if (isset($wp_query->query_vars['byt_rooms'])) {
						$groupby .= $wpdb->prepare(" AND rooms_available >= %d ", $wp_query->query_vars['byt_rooms']);
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
									$groupby .= $wpdb->prepare(" OR (accommodation_price >= %d AND accommodation_price <= %d ) ", $bottom, $top);
								} else {
									$groupby .= $wpdb->prepare(" OR (accommodation_price >= %d ) ", $bottom);
								}
							}
						}
						
						$groupby .= ")";

					}
				}
				
				if ($search_only_available) {
					$groupby .= " AND accommodation_price > 0 ";
				}
			}
		}
		
		return $groupby;
	}
	
	function build_accommodations_search_join($join, &$wp_query) {
	
		global $wpdb, $bookyourtravel_multi_language_count, $bookyourtravel_theme_globals;

		if(defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('accommodation') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$join .= " INNER JOIN " . $wpdb->prefix . "icl_translations translations_default ON translations_default.element_type = 'post_accommodation' AND translations_default.language_code='" . BookYourTravel_Theme_Utils::get_default_language() . "' AND translations_default.trid = t.trid ";
		}
		
		return $join;
	}
	
	function build_accommodations_search_orderby($orderby, &$wp_query) {
		
		global $wpdb, $bookyourtravel_multi_language_count;

		if (isset($wp_query->query_vars['byt_orderby']) && isset($wp_query->query_vars['byt_order'])) {

			$order = 'ASC';
			if ($wp_query->get('byt_order') == 'DESC') {
				$order = 'DESC';
			}
			
			$column = 'accommodation_price';
			if ($wp_query->get('byt_orderby') == $column) {
				$orderby = $column . ' ' . $order;
			}
		}
		
		return $orderby;
	}

	function list_accommodations_count ( $paged = 0, $per_page = -1, $orderby = '', $order = '', $location_id = 0, $accommodation_types_array = array(), $accommodation_tags_array = array(), $search_args = array(), $featured_only = false, $disabled_room_types = null, $author_id = null, $include_private = false, $count_only = false ) { 
		$results = $this->list_accommodations($paged, $per_page, $orderby, $order, $location_id, $accommodation_types_array, $accommodation_tags_array, $search_args, $featured_only, $disabled_room_types, $author_id, $include_private, true);
		return $results['total'];
	}
	
	function list_accommodations( $paged = 0, $per_page = -1, $orderby = '', $order = '', $location_id = 0, $accommodation_types_array = array(), $accommodation_tags_array = array(), $search_args = array(), $featured_only = false, $disabled_room_types = null, $author_id = null, $include_private = false, $count_only = false ) {

		global $bookyourtravel_theme_globals;
		$location_ids = array();
		
		if ($location_id > 0) {
			$location_ids[] = intval($location_id);
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
			'post_type'         => 'accommodation',
			'post_status'       => array('publish'),
			'posts_per_page'    => $per_page,
			'paged'				=> $paged,
			'orderby'           => $orderby,
			'suppress_filters' 	=> false,
			'order'				=> $order
		);
		
		if ($orderby == 'star_count') {
			$args['meta_key'] = 'accommodation_star_count';
			$args['orderby'] = 'meta_value_num';
		} else if ($orderby == 'review_score') {
			$args['meta_key'] = 'review_score';
			$args['orderby'] = 'meta_value_num';
		} else if ($orderby == 'min_price') {
			$args['byt_orderby'] = 'accommodation_price';
			$args['byt_order'] = $order;
		}
		
		if (isset($search_args['keyword']) && strlen($search_args['keyword']) > 0) {
			$args['s'] = $search_args['keyword'];
		}
		
		if ($include_private) {
			$args['post_status'][] = 'draft';
			$args['post_status'][] = 'private';
		}
		
		$meta_query = array('relation' => 'AND');
		
		if ( isset($search_args['stars']) && strlen($search_args['stars']) > 0 ) {
			$stars = intval($search_args['stars']);
			if ($stars > 0 & $stars <=5) {
				$meta_query[] = array(
					'key'       => 'accommodation_star_count',
					'value'     => $stars,
					'compare'   => '>=',
					'type' => 'numeric'
				);
			}
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

		if (isset($disabled_room_types)) {
		
			$args['byt_disabled_room_types'] = $disabled_room_types;
			if ($disabled_room_types) {
				$meta_query[] = array(
					'key'       => 'accommodation_disabled_room_types',
					'value'     => '1',
					'compare'   => '=',
					'type' => 'numeric'
				);
			} else {
				$meta_query[] = array(
					'key'       => 'accommodation_disabled_room_types',
					'compare'   => 'NOT EXISTS'
				);
			}		
		}
		
		if (isset($featured_only) && $featured_only) {
			$meta_query[] = array(
				'key'       => 'accommodation_is_featured',
				'value'     => 1,
				'compare'   => '=',
				'type' => 'numeric'
			);
		}

		if (isset($author_id)) {
			$author_id = (int)($author_id);
			if ($author_id > 0) {
				$args['author'] = $author_id;
			}
		}

		if (count($location_ids) > 0) {
			$meta_query[] = array(
				'key'       => 'accommodation_location_post_id',
				'value'     => $location_ids,
				'compare'   => 'IN'
			);
			$args['byt_location_ids'] = $location_ids;
		}
		
		if (!empty($accommodation_types_array)) {
			$args['tax_query'][] = 	array(
					'taxonomy' => 'accommodation_type',
					'field' => 'id',
					'terms' => $accommodation_types_array,
					'operator'=> 'IN'
			);
		}
		
		if (!empty($accommodation_tags_array)) {
			$args['tax_query'][] = 	array(
					'taxonomy' => 'acc_tag',
					'field' => 'id',
					'terms' => $accommodation_tags_array,
					'operator'=> 'IN'
			);
		}
		
		$search_only_available = false;
		if ( isset($search_args['search_only_available'])) {				
			$search_only_available = $search_args['search_only_available'];
		}

		if ( isset($search_args['date_from']) ) {
			$args['byt_date_from'] = $search_args['date_from'];
		}
		if ( isset($search_args['date_to']) ) {
			$args['byt_date_to'] =  $search_args['date_to'];
		}
		if ( isset($search_args['rooms']) ) {
			$args['byt_rooms'] = $search_args['rooms'];
		}
		
		if ($count_only) {
			$args['byt_count_only'] = 1;
		}
			
		if ( isset($search_args['prices']) ) {
			$args['prices'] = $search_args['prices'];
			$args['price_range_bottom'] = $bookyourtravel_theme_globals->get_price_range_bottom();
			$args['price_range_increment'] = $bookyourtravel_theme_globals->get_price_range_increment();
			$args['price_range_count'] = $bookyourtravel_theme_globals->get_price_range_count();
		}
			
		$args['search_only_available'] = $search_only_available;

		add_filter('posts_where', array($this, 'build_accommodations_search_where'), 10, 2);		
		add_filter('posts_fields', array($this, 'build_accommodations_search_fields'), 10, 2 );
		add_filter('posts_groupby', array($this, 'build_accommodations_search_groupby'), 10, 2 );
		add_filter('posts_join', array($this, 'build_accommodations_search_join'), 10, 2 );
		add_filter('posts_orderby', array($this, 'build_accommodations_search_orderby'), 10, 2 );
		
		$args['meta_query'] = $meta_query;
		
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

		remove_filter('posts_where', array($this, 'build_accommodations_search_where' ) );		
		remove_filter('posts_fields', array($this, 'build_accommodations_search_fields' ) );
		remove_filter('posts_groupby', array($this, 'build_accommodations_search_groupby') );
		remove_filter('posts_join', array($this, 'build_accommodations_search_join') );		
		remove_filter('posts_orderby', array($this, 'build_accommodations_search_orderby') );	
		
		return $results;
	}
	
	/**
	 * Vacancy and booking related methods
	 */	
	function calculate_total_accommodation_price($accommodation_id, $room_type_id, $date_from, $date_to, $room_count, $adults, $children, $current_booking_id = 0) {

		global $wpdb;
		
		$accommodation_id = BookYourTravel_Theme_Utils::get_default_language_post_id($accommodation_id, 'accommodation');
		if ($room_type_id > 0) {
			$room_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($room_type_id, 'room_type');
		}

		$accommodation_is_price_per_person = get_post_meta($accommodation_id, 'accommodation_is_price_per_person', true);
		$accommodation_is_price_per_person = isset($accommodation_is_price_per_person) ? intval($accommodation_is_price_per_person) : 0;

		$accommodation_rent_type = get_post_meta($accommodation_id, 'accommodation_rent_type', true);
		$accommodation_rent_type = $accommodation_rent_type > 0 ? $accommodation_rent_type : 0;
		
		// we are actually (in terms of db data) looking for date 1 day before the to date
		// e.g. when you look to book a room from 19.12. to 20.12 you will be staying 1 night, not 2
		$date_to = date('Y-m-d', strtotime($date_to.' -1 day'));
		
		$dates = BookYourTravel_Theme_Utils::get_dates_from_range($date_from, $date_to, $accommodation_rent_type);
		
		$total_price = 0;
		
		foreach ($dates as $date) {
		
			$date = date('Y-m-d', strtotime($date));
			
			$prices_row = $this->get_accommodation_prices($date, $accommodation_id, $room_type_id, $current_booking_id);
		
			if (isset($prices_row)) {
				
				$price_per_day = $prices_row->price_per_day;
				$price_per_day_child = $prices_row->price_per_day_child;
				$weekend_price_per_day = isset($prices_row->weekend_price_per_day) ? $prices_row->weekend_price_per_day : 0;
				$weekend_price_per_day_child = isset($prices_row->weekend_price_per_day_child) ? $prices_row->weekend_price_per_day_child : 0;
				$is_weekend = BookYourTravel_Theme_Utils::is_weekend($date);
				
				if ($accommodation_is_price_per_person) {
					
					$price_per_day_per_room = 0;
					
					if ($is_weekend) {				
						if ($weekend_price_per_day && $weekend_price_per_day > 0) {
							$price_per_day_per_room += ($adults * $weekend_price_per_day);
						} else {
							$price_per_day_per_room += ($adults * $price_per_day);
						}
						
						if ($weekend_price_per_day_child && $weekend_price_per_day_child > 0) {
							$price_per_day_per_room += ($children * $weekend_price_per_day_child);
						} else {
							$price_per_day_per_room += ($children * $price_per_day_child);
						}					
					} else {
						$price_per_day_per_room += (($adults * $price_per_day) + ($children * $price_per_day_child));
					}
					
					$total_price += $price_per_day_per_room * $room_count;
					
				} else {
					if ($is_weekend && $weekend_price_per_day && $weekend_price_per_day > 0) {
						$total_price += ($weekend_price_per_day * $room_count);
					} else {
						$total_price += ($price_per_day * $room_count);
					}
				}
			}
		}
		
		$total_price = $total_price * $room_count;

		return $total_price;
	}

	function list_accommodation_vacancy_start_dates($accommodation_id, $room_type_id=0, $month, $year, $month_range, $current_booking_id = 0) {

		global $wpdb, $bookyourtravel_theme_globals;
		
		$accommodation_id = BookYourTravel_Theme_Utils::get_default_language_post_id($accommodation_id, 'accommodation');
		if ($room_type_id > 0)
			$room_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($room_type_id, 'room_type'); 
		
		$accommodation_obj = new BookYourTravel_Accommodation($accommodation_id);
		$accommodation_is_reservation_only = $accommodation_obj->get_is_reservation_only();
		
		$current_date = date('Y-m-d', time());
		$yesterday = date('Y-m-d',strtotime("-1 days"));
		
		$end_date = sprintf("%d-%d-%d", $year, $month, 1);
		$end_date = date('Y-m-d', strtotime(sprintf("+%d months", $month_range), strtotime($end_date)));
		$end_date = date("Y-m-t", strtotime($end_date)); // last day of end date month
		$end_date = date('Y-m-d', strtotime(sprintf("+%d days", 1), strtotime($end_date)));		
		
		$sql = "SELECT availables.single_date, availables.available_rooms, IFNULL(SUM(bookings.room_count), 0) booked_rooms
				FROM (
					SELECT DISTINCT date_format(DATE(dates.day), '%Y-%m-%d') single_date, SUM(vacancies.room_count) available_rooms, date_format(DATE(dates.day), '%Y-%m-%d 12:00:01') as bookable_single_date 
					FROM " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_DAYS_TABLE . " dates
					INNER JOIN " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_TABLE . " vacancies ON dates.vacancy_id=vacancies.Id ";
				
		$sql .= $wpdb->prepare("WHERE dates.day > %s AND dates.day < %s AND vacancies.accommodation_id=%d ", $yesterday, $end_date, $accommodation_id);

		if ($room_type_id > 0) {
			$sql .= $wpdb->prepare(" AND vacancies.room_type_id=%d ", $room_type_id);
		}
				
		$sql .= " 	GROUP BY single_date 
				) availables
				LEFT JOIN " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE . " bookings ON availables.bookable_single_date BETWEEN bookings.date_from AND bookings.date_to ";
			
		$sql .= $wpdb->prepare(" AND bookings.accommodation_id=%d ", $accommodation_id);
		
		if ($room_type_id > 0) {
			$sql .= $wpdb->prepare(" AND bookings.room_type_id=%d ", $room_type_id);
		}
		
		if ($current_booking_id > 0) {
			$sql .= $wpdb->prepare(" AND bookings.Id <> %d ", $current_booking_id);
		}
			
		if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout() && !$accommodation_is_reservation_only) {
			
			$completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
			if (!empty($completed_statuses)) {
				$sql .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses . ")";
			}
		}
		
		$sql .= " GROUP BY availables.single_date";
		
		$results = $wpdb->get_results($sql);
		
		$available_dates = array();
		
		foreach ($results as $result) {
		
			$room_count = $result->available_rooms;
			$booked_rooms = $result->booked_rooms;
			
			if ($room_count > $booked_rooms) {
				$result->single_date = date('Y-m-d', strtotime($result->single_date));
				$available_dates[] = $result;
			}
		}
		
		return $available_dates;
	}

	function list_accommodation_vacancy_end_dates($start_date, $accommodation_id, $room_type_id=0, $month, $year, $day, $month_range = 0) {

		global $wpdb, $bookyourtravel_theme_globals;
		
		$accommodation_id = BookYourTravel_Theme_Utils::get_default_language_post_id($accommodation_id, 'accommodation');
		
		if ($room_type_id > 0) {
			$room_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($room_type_id, 'room_type'); 
		}
		
		$accommodation_obj = new BookYourTravel_Accommodation($accommodation_id);
		$accommodation_is_reservation_only = $accommodation_obj->get_is_reservation_only();		
		
		$start_date = date('Y-m-d', strtotime($start_date));
			
		$end_date = sprintf("%d-%d-%d", $year, $month, $day);
		$end_date = date("Y-m-t", strtotime($end_date)); // last day of end date month
		if ($month_range > 0) {
			$end_date = date('Y-m-d', strtotime(sprintf("+%d months", $month_range), strtotime($end_date)));
		}

		$sql = "SELECT 	availables.single_date, availables.available_rooms, IFNULL(SUM(bookings.room_count), 0) booked_rooms
				FROM (
					SELECT DISTINCT date_format(DATE_ADD(dates.day, INTERVAL 1 DAY), '%Y-%m-%d') single_date, SUM(vacancies.room_count) available_rooms, date_format(DATE(DATE_ADD(dates.day, INTERVAL 1 DAY)), '%Y-%m-%d 11:59:59') as bookable_single_date 
					FROM " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_DAYS_TABLE . " dates
					INNER JOIN " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_TABLE . " vacancies ON dates.vacancy_id=vacancies.Id ";
				
		$sql .= $wpdb->prepare("WHERE dates.day > %s AND dates.day <= %s AND vacancies.accommodation_id=%d ", $start_date, $end_date, $accommodation_id);

		if ($room_type_id > 0) {
			$sql .= $wpdb->prepare(" AND vacancies.room_type_id=%d ", $room_type_id);
		}
				
		$sql .= " 	GROUP BY single_date 
					) availables				
					LEFT JOIN " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE . " bookings ON availables.bookable_single_date BETWEEN bookings.date_from AND bookings.date_to ";
			
		$sql .= $wpdb->prepare(" AND bookings.accommodation_id=%d ", $accommodation_id);
		
		if ($room_type_id > 0) {
			$sql .= $wpdb->prepare(" AND bookings.room_type_id=%d ", $room_type_id);
		}
			
		if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout() && !$accommodation_is_reservation_only) {
			
			$completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
			if (!empty($completed_statuses)) {
				$sql .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses . ")";
			}
		}
		
		$sql .= " GROUP BY availables.single_date";
		
		$results = $wpdb->get_results($sql);
		
		$available_dates = array();
		
		$prev_date = null;
		$next_date = null;
		foreach ($results as $result) {
		
			$new_date = date('Y-m-d', strtotime($result->single_date));
		
			if (isset($prev_date)) {
				$next_date = date('Y-m-d', strtotime($prev_date . ' +1 days'));
				
				if ($next_date != $new_date) {
					// there was a break in days so days after this one are not bookable
					break;
				}
			}

			$room_count = $result->available_rooms;
			$booked_rooms = $result->booked_rooms;
			
			if ($room_count > $booked_rooms) {
				$result->single_date = date('Y-m-d', strtotime($result->single_date));
				$available_dates[] = $result;
			} else if ($new_date == $start_date) {
				$result->single_date = date('Y-m-d', strtotime($result->single_date));
				$result->booked_rooms = $booked_rooms - 1;
				$available_dates[] = $result;
			} else {
				break;
			}
				
			$prev_date = $new_date;
		}
		
		return $available_dates;
	}

	function get_accommodation_prices($search_date, $accommodation_id, $room_type_id = 0, $current_booking_id = 0) {
	
		global $wpdb, $bookyourtravel_theme_globals;
		
		$accommodation_id = BookYourTravel_Theme_Utils::get_default_language_post_id($accommodation_id, 'accommodation');
		if ($room_type_id > 0)
			$room_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($room_type_id, 'room_type'); 
		
		$accommodation_obj = new BookYourTravel_Accommodation($accommodation_id);
		$accommodation_is_reservation_only = $accommodation_obj->get_is_reservation_only();		
		
		$search_date = date('Y-m-d', strtotime($search_date));
		
		$sql = "SELECT a.vacancy_id, a.price_per_day, a.price_per_day_child, a.weekend_price_per_day, a.weekend_price_per_day_child, a.room_count, a.booked_rooms, 
				(@runtot := @runtot + a.room_count) AS running_available_total
				FROM
				(
					SELECT availables.*, IFNULL(SUM(bookings.room_count), 0) booked_rooms
					FROM 
					(
					SELECT availables_inner.*, date_format(DATE(availables_inner.single_date), '%Y-%m-%d 12:00:01') as bookable_single_date ";
					
		$sql .= $wpdb->prepare("FROM
						(
							SELECT vacancies.Id vacancy_id, %s single_date, vacancies.price_per_day, vacancies.price_per_day_child, IFNULL(vacancies.weekend_price_per_day, 0) weekend_price_per_day, IFNULL(vacancies.weekend_price_per_day_child, 0) weekend_price_per_day_child, vacancies.room_count
							FROM " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_TABLE . " vacancies
							WHERE %s >= vacancies.start_date AND %s < vacancies.end_date AND vacancies.accommodation_id = %d ", $search_date, $search_date, $search_date, $accommodation_id );
							
			if ($room_type_id > 0)
				$sql .= $wpdb->prepare(" AND vacancies.room_type_id = %d ", $room_type_id);
							
			$sql .= $wpdb->prepare (" 
							GROUP BY vacancy_id
						) availables_inner
					) availables
					LEFT JOIN " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE . " bookings ON availables.bookable_single_date BETWEEN bookings.date_from AND bookings.date_to
					AND bookings.accommodation_id = %d ", $accommodation_id);
		
		if ($room_type_id > 0) {
			$sql .= $wpdb->prepare(" AND bookings.room_type_id = %d ", $room_type_id);
		}
				
		if ($current_booking_id > 0) {
			$sql .= $wpdb->prepare(" AND bookings.Id <> %d ", $current_booking_id);
		}
		
		if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout() && !$accommodation_is_reservation_only) {
			
			$completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
			if (!empty($completed_statuses)) {
				$sql .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses . ")";
			}
		}
				
		$sql .=		" GROUP BY availables.vacancy_id
				) a, (SELECT @runtot:=0) AS n
				GROUP BY a.vacancy_id
				HAVING running_available_total > booked_rooms
				ORDER BY price_per_day ASC 
				LIMIT 1 ";
				
		return $wpdb->get_row($sql);
	}	
		
	function get_accommodation_price($accommodation_id = 0, $room_type_id = 0, $start_date = null, $end_date = null, $location_id = 0) {

		global $wpdb, $bookyourtravel_theme_globals;
		
		if ($accommodation_id > 0) {
			$accommodation_id = BookYourTravel_Theme_Utils::get_default_language_post_id($accommodation_id, 'accommodation');			
		}
		
		if ($room_type_id > 0) {
			$room_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($room_type_id, 'room_type');			
		}
		
		$price = -1;
		
		$accommodation_obj = new BookYourTravel_Accommodation($accommodation_id);		
		$accommodation_is_reservation_only = $accommodation_obj->get_is_reservation_only();
		
		if ($room_type_id == 0 && $location_id == 0) {

			$last_cache_minutes = 0;
			if ($accommodation_obj->is_custom_field_set('_accommodation_price_cache_time', false)) {
				$last_cache_seconds = intval($accommodation_obj->get_custom_field('_accommodation_price_cache_time', false));
				$current_seconds = time();
				if ($last_cache_seconds > 0) {
					$last_cache_minutes = ($current_seconds - $last_cache_seconds) / (60);
				}
			}
			
			if ($last_cache_minutes > 0 && $last_cache_minutes <= 10) {
				$price = floatval($accommodation_obj->get_custom_field('_accommodation_price_cache', false));
			}
		}
		
		if ($price == -1) {
			
			$sql = "";
			
			$accommodation_ids_string = '';
			$accommodation_ids = array();
			if ($location_id > 0) {
				$location_id = BookYourTravel_Theme_Utils::get_default_language_post_id($location_id, 'location');
				$location_ids[] = intval($location_id);
				$location_descendants = BookYourTravel_Theme_Utils::get_post_descendants($location_id, 'location');
				foreach ($location_descendants as $location) {
					$location_ids[] = $location->ID;
				}
				$location_ids_string = implode(',', $location_ids);
				
				if (strlen($location_ids_string) > 0) {
					$sql = " SELECT ID FROM $wpdb->posts accommodations
							 INNER JOIN $wpdb->postmeta meta ON meta.post_id = accommodations.ID AND meta.meta_key = 'accommodation_location_post_id' AND meta.meta_value IN (" . $location_ids_string . ")";
					
					$accommodation_results = $wpdb->get_results($sql);
					foreach ($accommodation_results as $accommodation_result) {
						$accommodation_ids[] = $accommodation_result->ID;
					}
					if (count($accommodation_ids) > 0) {
						$accommodation_ids_string = implode(',', $accommodation_ids);
					}
				}
			}
			
			$today = date('Y-m-d', time());
			if (!isset($start_date) || $start_date < $today) {
				$start_date = $today;	
			}
			
			 if (!isset($end_date)) {
				$end_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime($start_date)) . " + 365 day"));
			}
				
			$sql = "SELECT MIN(price_per_day) price_per_day
					FROM
					(
						SELECT 	vacancy_id, availables.available_rooms, IFNULL(SUM(bookings.room_count), 0) booked_rooms, price_per_day, 
								@total := IF (@current_date = availables.single_date, @total, 0) + availables.available_rooms as available_rooms_total,
								@current_date := availables.single_date as single_date
						FROM (
							SELECT distinct dates.vacancy_id, dates.day single_date, date_format(DATE(dates.day), '%Y-%m-%d 12:00:01') as bookable_single_date, room_count available_rooms, price_per_day
							FROM " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_DAYS_TABLE . " dates
							LEFT JOIN " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_TABLE . " vacancies ON vacancies.Id = dates.vacancy_id ";
			
			$sql .= $wpdb->prepare(" WHERE dates.day > %s AND dates.day <= %s ", $start_date, $end_date);
			
			if ($accommodation_id > 0) {
				$sql .= $wpdb->prepare(" AND vacancies.accommodation_id = %d ", $accommodation_id);
			} else if (strlen($accommodation_ids_string) > 0) {
				$sql .= " AND vacancies.accommodation_id IN (" . $accommodation_ids_string . ") ";
			} else {
				$sql .= " AND 1=0 "; // if no accommodation id was provided and the sub accommodations are blank, we don't want anything.
			}
			
			if ($room_type_id > 0) {
				$sql .= $wpdb->prepare(" AND vacancies.room_type_id = %d ", $room_type_id);
			}
			
			$sql .= "		GROUP BY vacancy_id, single_date
						) as availables ";
						
			$sql .= " 	LEFT JOIN " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_DAYS_TABLE . " booking_days ON booking_days.day = availables.single_date
						LEFT JOIN " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE . " bookings ON bookings.Id = booking_days.booking_id ";
			
			if ($accommodation_id > 0) {
				$sql .= $wpdb->prepare(" AND bookings.accommodation_id=%d ", $accommodation_id);
			} else if (strlen($accommodation_ids_string) > 0) {
				$sql .= " AND bookings.accommodation_id IN (" . $accommodation_ids_string . ") ";
			} else {
				$sql .= " AND 1=0 "; // if no accommodation id was provided and the sub accommodations are blank, we don't want anything.
			}
			
			if ($room_type_id > 0) {
				$sql .= $wpdb->prepare(" AND bookings.room_type_id = %d ", $room_type_id);
			}

			if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout() && !$accommodation_is_reservation_only) {
				
				$completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
				if (!empty($completed_statuses)) {
					$sql .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses . ")";
				}
			}
			
			$sql .= "	CROSS JOIN (select @current_date := '', @total := 0) as variable_alias
						GROUP BY availables.vacancy_id, availables.single_date
						HAVING available_rooms_total > booked_rooms
					) as available_prices ";
					
			$price = floatval($wpdb->get_var($sql));
			
			if ($room_type_id == 0 && $location_id == 0 ) {
				update_post_meta($accommodation_id, '_accommodation_price_cache', $price);
				update_post_meta($accommodation_id, '_accommodation_price_cache_time', time());
			}
		}
		
		return $price;
	}

	function get_accommodation_vacancy($vacancy_id ) {
	
		global $wpdb;

		$sql = "SELECT vacancies.*, accommodations.post_title accommodation_name, room_types.post_title room_type
				FROM " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_TABLE . " vacancies 
				INNER JOIN $wpdb->posts accommodations ON accommodations.ID = vacancies.accommodation_id 
				LEFT JOIN $wpdb->posts room_types ON room_types.ID = vacancies.room_type_id 
				WHERE vacancies.Id=%d ";

		return $wpdb->get_row($wpdb->prepare($sql, $vacancy_id));
	}

	function list_accommodation_vacancies($accommodation_id = 0, $room_type_id = 0, $orderby = 'Id', $order = 'ASC', $paged = null, $per_page = 0, $author_id = null ) {

		global $wpdb;

		$accommodation_id = BookYourTravel_Theme_Utils::get_default_language_post_id($accommodation_id, 'accommodation');
		if ($room_type_id > 0)
			$room_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($room_type_id, 'room_type'); 
		
		$sql = "SELECT DISTINCT vacancies.*, accommodations.post_title accommodation_name, room_types.post_title room_type, IFNULL(accommodation_meta_is_per_person.meta_value, 0) accommodation_is_per_person, IFNULL(accommodation_meta_disabled_room_types.meta_value, 0) accommodation_disabled_room_types
				FROM " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_TABLE . " vacancies 
				INNER JOIN $wpdb->posts accommodations ON accommodations.ID = vacancies.accommodation_id 
				LEFT JOIN $wpdb->postmeta accommodation_meta_is_per_person ON accommodations.ID=accommodation_meta_is_per_person.post_id AND accommodation_meta_is_per_person.meta_key='accommodation_is_price_per_person'
				LEFT JOIN $wpdb->postmeta accommodation_meta_disabled_room_types ON accommodations.ID=accommodation_meta_disabled_room_types.post_id AND accommodation_meta_disabled_room_types.meta_key='accommodation_disabled_room_types'
				LEFT JOIN $wpdb->posts room_types ON room_types.ID = vacancies.room_type_id 
				WHERE 1=1 ";
				
		if ($accommodation_id > 0) {
			$sql .= $wpdb->prepare(" AND vacancies.accommodation_id=%d ", $accommodation_id);
		}
		
		if ($room_type_id > 0) {
			$sql .= $wpdb->prepare(" AND vacancies.room_type_id=%d ", $room_type_id);
		}
		
		if (isset($author_id)) {
			$sql .= $wpdb->prepare(" AND accommodations.post_author=%d ", $author_id);
		}

		if(!empty($orderby) & !empty($order)) { 
			$sql.=' ORDER BY ' . $orderby . ' ' . $order; 
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
	
	function create_accommodation_vacancy($season_name, $start_date, $end_date, $accommodation_id, $room_type_id, $room_count, $price_per_day, $price_per_day_child, $weekend_price_per_day, $weekend_price_per_day_child) {

		global $wpdb;
		
		$accommodation_id = BookYourTravel_Theme_Utils::get_default_language_post_id($accommodation_id, 'accommodation');
		if ($room_type_id > 0)
			$room_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($room_type_id, 'room_type'); 
		
		$this->clear_price_meta_cache($accommodation_id);
		
		$sql = "INSERT INTO " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_TABLE . "
				(season_name, start_date, end_date, accommodation_id, room_type_id, room_count, price_per_day, price_per_day_child, weekend_price_per_day, weekend_price_per_day_child)
				VALUES
				(%s, %s, %s, %d, %d, %d, %f, %f, %f, %f);";
		
		$wpdb->query($wpdb->prepare($sql, $season_name, $start_date, $end_date, $accommodation_id, $room_type_id, $room_count, $price_per_day, $price_per_day_child, $weekend_price_per_day, $weekend_price_per_day_child));	
		
		$vacancy_id = $wpdb->insert_id;
		
		// we are actually (in terms of db data) creating a vacancy for dates up to 1 day before the date_to value.
		// e.g. when you look to book a room from 19.12. to 20.12 you will be staying 1 night, not 2 as far as our data is concerned.
		$effective_end_date = date('Y-m-d', strtotime($end_date . " -1 days"));
		
		$dates = BookYourTravel_Theme_Utils::get_dates_from_range($start_date, $effective_end_date);
		
		foreach ($dates as $date) {
			$effective_date = date('Y-m-d 12:00:00', strtotime($date));
			$this->insert_vacancy_day($vacancy_id, $effective_date);
		}
		
		return $vacancy_id;
	}

	function insert_vacancy_day($vacancy_id, $day) {
	
		global $wpdb;
		
		$sql = "INSERT INTO " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_DAYS_TABLE . "
				(vacancy_id, day)
				VALUES
				(%d, %s);";
				
		$wpdb->query($wpdb->prepare($sql, $vacancy_id, $day));			
	}
	
	function clear_vacancy_days($vacancy_id) {

		global $wpdb;
		
		$sql = "DELETE FROM " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_DAYS_TABLE . " WHERE vacancy_id = %d";
		$sql = $wpdb->prepare($sql, $vacancy_id);
		$wpdb->query($sql);
	}
	
	function update_accommodation_vacancy($vacancy_id, $season_name, $start_date, $end_date, $accommodation_id, $room_type_id, $room_count, $price_per_day, $price_per_day_child, $weekend_price_per_day, $weekend_price_per_day_child) {

		global $wpdb;
		
		$accommodation_id = BookYourTravel_Theme_Utils::get_default_language_post_id($accommodation_id, 'accommodation');
		if ($room_type_id > 0)
			$room_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($room_type_id, 'room_type'); 
		
		$this->clear_price_meta_cache($accommodation_id);
		
		$start_date = date('Y-m-d', strtotime($start_date));
		$end_date = date('Y-m-d', strtotime($end_date));
		
		$sql = "UPDATE " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_TABLE . "
				SET season_name=%s, start_date=%s, end_date=%s, accommodation_id=%d, room_type_id=%d, room_count=%d, price_per_day=%f, price_per_day_child=%f, weekend_price_per_day=%f, weekend_price_per_day_child=%f
				WHERE Id=%d";
		
		$wpdb->query($wpdb->prepare($sql, $season_name, $start_date, $end_date, $accommodation_id, $room_type_id, $room_count, $price_per_day, $price_per_day_child, $weekend_price_per_day, $weekend_price_per_day_child, $vacancy_id));	
		
		$this->clear_vacancy_days($vacancy_id);
		
		// we are actually (in terms of db data) creating a vacancy for dates up to 1 day before the date_to value.
		// e.g. when you look to book a room from 19.12. to 20.12 you will be staying 1 night, not 2 as far as our data is concerned.
		$effective_end_date = date('Y-m-d', strtotime($end_date . " -1 days"));
		
		$dates = BookYourTravel_Theme_Utils::get_dates_from_range($start_date, $effective_end_date);
		
		foreach ($dates as $date) {
			$effective_date = date('Y-m-d 12:00:00', strtotime($date));
			$this->insert_vacancy_day($vacancy_id, $effective_date);
		}
		
		return $vacancy_id;
	}

	function delete_accommodation_vacancy($vacancy_id) {
		
		global $wpdb;
		
		$vacancy = $this->get_accommodation_vacancy($vacancy_id);
		
		$this->clear_price_meta_cache($vacancy->accommodation_id);
		
		$this->clear_vacancy_days($vacancy_id);

		$sql = "DELETE FROM " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_TABLE . "
				WHERE Id = %d";
		
		$wpdb->query($wpdb->prepare($sql, $vacancy_id));
	}

	function get_accommodation_booking($booking_id) {
	
		global $wpdb, $bookyourtravel_multi_language_count, $bookyourtravel_theme_globals;

		$sql = "SELECT DISTINCT bookings.*, accommodations.post_title accommodation_name, room_types.post_title room_type
				FROM " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE . " bookings ";

		if(defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('accommodation') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$sql .= " INNER JOIN " . $wpdb->prefix . "icl_translations translations_default ON translations_default.element_type = 'post_accommodation' AND translations_default.language_code='" . BookYourTravel_Theme_Utils::get_default_language() . "' AND translations_default.element_id = bookings.accommodation_id ";			
			$sql .= " INNER JOIN " . $wpdb->prefix . "icl_translations translations ON translations.element_type = 'post_accommodation' AND translations.language_code='" . ICL_LANGUAGE_CODE . "' AND translations.trid = translations_default.trid ";
		}
		
		$sql .= " INNER JOIN $wpdb->posts accommodations ON ";
		if(defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('accommodation') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$sql .= " accommodations.ID = translations.element_id ";
		} else {
			$sql .= " accommodations.ID = bookings.accommodation_id ";
		}
				
		$sql .= $wpdb->prepare(" LEFT JOIN $wpdb->posts room_types ON room_types.ID = bookings.room_type_id 
				WHERE accommodations.post_status = 'publish' AND (room_types.post_status IS NULL OR room_types.post_status = 'publish') 
				AND bookings.Id = %d ", $booking_id );

		return $wpdb->get_row($sql);
	}

	function list_accommodation_bookings($paged = null, $per_page = 0, $orderby = 'Id', $order = 'ASC', $search_term = null, $user_id = 0, $author_id = null, $accommodation_id = null) {
	
		global $wpdb, $bookyourtravel_multi_language_count, $bookyourtravel_theme_globals;

		$sql = "SELECT DISTINCT bookings.*, accommodations.post_title accommodation_name, room_types.post_title room_type
				FROM " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE . " bookings ";
				
		if(defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('accommodation') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$sql .= " INNER JOIN " . $wpdb->prefix . "icl_translations translations_default ON translations_default.element_type = 'post_accommodation' AND translations_default.language_code='" . BookYourTravel_Theme_Utils::get_default_language() . "' AND translations_default.element_id = bookings.accommodation_id ";			
			$sql .= " INNER JOIN " . $wpdb->prefix . "icl_translations translations ON translations.element_type = 'post_accommodation' AND translations.language_code='" . ICL_LANGUAGE_CODE . "' AND translations.trid = translations_default.trid ";
		}
		
		$sql .= " INNER JOIN $wpdb->posts accommodations ON ";
		if(defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('accommodation') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$sql .= " accommodations.ID = translations.element_id ";
		} else {
			$sql .= " accommodations.ID = bookings.accommodation_id ";
		}		
				
		$sql .= " LEFT JOIN $wpdb->posts room_types ON room_types.ID = bookings.room_type_id ";
		$sql .= " WHERE accommodations.post_status = 'publish' AND (room_types.post_status IS NULL OR room_types.post_status = 'publish') ";
		
		if ($accommodation_id > 0) {
			$sql .= $wpdb->prepare(" AND bookings.accommodation_id = %d ", $accommodation_id) ;
		}
		
		if ($user_id > 0) {
			$sql .= $wpdb->prepare(" AND bookings.user_id = %d ", $user_id) ;
		}
		
		if ($search_term != null && !empty($search_term)) {
			$search_term = "%" . $search_term . "%";
			$sql .= $wpdb->prepare(" AND 1=1 AND (bookings.first_name LIKE '%s' OR bookings.last_name LIKE '%s' OR accommodations.post_title LIKE '%s') ", $search_term, $search_term, $search_term);
		}
		
		if (isset($author_id)) {
			$sql .= $wpdb->prepare(" AND accommodations.post_author = %d ", $author_id);
		}
		
		if(!empty($orderby) & !empty($order)) { 
			$sql.=' ORDER BY '.$orderby.' '.$order; 
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

	function create_accommodation_booking($user_id, $booking_object) {

		global $wpdb;

		$errors = array();

		$sql = "INSERT INTO " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE . "
				(user_id, accommodation_id, room_type_id, room_count, adults, children, date_from, date_to, first_name, last_name, company, email, phone, address, address_2, town, zip, state, country, special_requirements, other_fields, extra_items, total_accommodation_price, total_extra_items_price, total_price)
				VALUES 
				(%d, %d, %d, %d, %d, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %f, %f, %f);";

		$result = $wpdb->query($wpdb->prepare($sql, $user_id, $booking_object->accommodation_id, $booking_object->room_type_id, $booking_object->room_count, $booking_object->adults, $booking_object->children, $booking_object->date_from, $booking_object->date_to, $booking_object->first_name, $booking_object->last_name, $booking_object->company, $booking_object->email, $booking_object->phone, $booking_object->address, $booking_object->address_2, $booking_object->town, $booking_object->zip,  $booking_object->state, $booking_object->country, $booking_object->special_requirements, serialize($booking_object->other_fields), serialize($booking_object->extra_items), $booking_object->total_accommodation_price, $booking_object->total_extra_items_price, $booking_object->total_price));

		if (is_wp_error($result))
			$errors[] = $result;

		$booking_object->Id = $wpdb->insert_id;

		// we are actually (in terms of db data) creating a vacancy for dates up to 1 day before the date_to value.
		// e.g. when you look to book a room from 19.12. to 20.12 you will be staying 1 night, not 2 as far as our data is concerned.
		$effective_date_to = date('Y-m-d', strtotime($booking_object->date_to .' -1 day'));
		
		$dates = BookYourTravel_Theme_Utils::get_dates_from_range($booking_object->date_from, $effective_date_to);
		
		foreach ($dates as $date) {
		
			$this->insert_booking_day($booking_object->Id, $date);
		}
		
		$this->clear_price_meta_cache($booking_object->accommodation_id);	
			
		return $booking_object->Id;
	}
	
	function insert_booking_day($booking_id, $day) {
	
		global $wpdb;
		
		$effective_date_to = date('Y-m-d 12:00:01', strtotime($day));
		
		$sql = "INSERT INTO " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_DAYS_TABLE . "
				(booking_id, day)
				VALUES
				(%d, %s);";
				
		$wpdb->query($wpdb->prepare($sql, $booking_id, $effective_date_to));			
	}
	
	function clear_booking_days($booking_id) {

		global $wpdb;
		
		$sql = "DELETE FROM " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_DAYS_TABLE . " WHERE booking_id = %d";
				
		$wpdb->query($wpdb->prepare($sql, $booking_id));
	}

	function update_accommodation_booking($booking_id, $booking_object) {

		global $wpdb;
		
		$result = 0;
		
		$sql = "UPDATE " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE . " SET ";
				
		$field_sql = '';
		
		foreach ($booking_object as $field_key => $field_value) {
			
			switch ($field_key) {
			
				case 'accommodation_id' 			: $field_sql .= $wpdb->prepare("accommodation_id = %d, ", $field_value); break;
				case 'room_type_id' 				: $field_sql .= $wpdb->prepare("room_type_id = %d, ", $field_value); break;
				case 'date_from' 					: $field_sql .= $wpdb->prepare("date_from = %s, ", $field_value); break;
				case 'date_to' 						: $field_sql .= $wpdb->prepare("date_to = %s, ", $field_value); break;
				case 'adults' 						: $field_sql .= $wpdb->prepare("adults = %d, ", $field_value); break;
				case 'children' 					: $field_sql .= $wpdb->prepare("children = %d, ", $field_value); break;
				case 'room_count' 					: $field_sql .= $wpdb->prepare("room_count = %d, ", $field_value); break;
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
				case 'total_accommodation_price' 	: $field_sql .= $wpdb->prepare("total_accommodation_price = %f, ", $field_value); break;
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
		
			// we are actually (in terms of db data) creating a vacancy for dates up to 1 day before the date_to value.
			// e.g. when you look to book a room from 19.12. to 20.12 you will be staying 1 night, not 2 as far as our data is concerned.
			$effective_date_to = date('Y-m-d', strtotime($booking_object->date_to .' -1 day'));
			
			$dates = BookYourTravel_Theme_Utils::get_dates_from_range($booking_object->date_from, $effective_date_to);
			
			foreach ($dates as $date) {
			
				$this->insert_booking_day($booking_id, $date);
			}
		
		}
		
		if (isset($booking_object->accommodation_id)) {
			$this->clear_price_meta_cache($booking_object->accommodation_id);
		}
		
		return $result;
	}
	
	function delete_accommodation_booking($booking_id) {
		
		do_action('bookyourtravel_before_delete_accommodation_booking', $booking_id);
		
		global $wpdb;
		
		$sql = "DELETE FROM " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE . "
				WHERE Id = %d";
		
		$booking = $this->get_accommodation_booking($booking_id);
		
		$this->clear_price_meta_cache($booking->accommodation_id);
		$this->clear_booking_days($booking_id);
		
		$wpdb->query($wpdb->prepare($sql, $booking_id));		
	}
	
	function clear_price_meta_cache($accommodation_id) {
		
		delete_post_meta($accommodation_id, '_accommodation_price_cache');
		delete_post_meta($accommodation_id, '_accommodation_price_cache_time');		
	}
	
	function update_booking_woocommerce_info($booking_id, $cart_key = null, $woo_order_id = null, $woo_status = null) {
	
		global $wpdb;
	
		if (isset($cart_key) || isset($woo_order_id) || isset($woo_status)) {
			$sql = "UPDATE " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE . "
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
	
}

global $bookyourtravel_accommodation_helper;
// store the instance in a variable to be retrieved later and call init
$bookyourtravel_accommodation_helper = BookYourTravel_Accommodation_Helper::get_instance();
$bookyourtravel_accommodation_helper->init();