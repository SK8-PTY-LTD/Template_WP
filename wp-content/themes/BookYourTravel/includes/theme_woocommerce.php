<?php

if ( file_exists( WP_PLUGIN_DIR .'/woocommerce/includes/api/interface-wc-api-handler.php' ) ) {

	require_once( WP_PLUGIN_DIR .'/woocommerce/includes/api/interface-wc-api-handler.php');
	require_once( WP_PLUGIN_DIR .'/woocommerce/includes/api/class-wc-api-server.php');
	require_once( WP_PLUGIN_DIR .'/woocommerce/includes/api/class-wc-api-json-handler.php');
	require_once( WP_PLUGIN_DIR .'/woocommerce/includes/api/interface-wc-api-handler.php');
	require_once( WP_PLUGIN_DIR .'/woocommerce/includes/api/class-wc-api-resource.php');
	require_once( WP_PLUGIN_DIR .'/woocommerce/includes/api/class-wc-api-orders.php');
}

if ( ! defined( 'BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID' ) )
    define( 'BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID', 'bookyourtravel_pa_accommodation_booking_id' );

if ( ! defined( 'BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID' ) )
    define( 'BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID', 'bookyourtravel_pa_tour_booking_id' );

if ( ! defined( 'BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID' ) )
    define( 'BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID', 'bookyourtravel_pa_cruise_booking_id' );
	
if ( ! defined( 'BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID' ) )
    define( 'BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID', 'bookyourtravel_pa_car_rental_booking_id' );
	
if ( ! defined( 'BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY' ) )
    define( 'BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY', 'bookyourtravel_booking_session_key' );
	
if ( ! defined( 'BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_ATT' ) )
    define( 'BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_ATT', 'bookyourtravel_pa_accommodation' );

if ( ! defined( 'BOOKYOURTRAVEL_WOOCOMMERCE_ROOM_TYPE_ATT' ) )
    define( 'BOOKYOURTRAVEL_WOOCOMMERCE_ROOM_TYPE_ATT', 'bookyourtravel_pa_room_type' );

if ( ! defined( 'BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_ATT' ) )
    define( 'BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_ATT', 'bookyourtravel_pa_tour' );

if ( ! defined( 'BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_ATT' ) )
    define( 'BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_ATT', 'bookyourtravel_pa_cruise' );
	
if ( ! defined( 'BOOKYOURTRAVEL_WOOCOMMERCE_CABIN_TYPE_ATT' ) )
    define( 'BOOKYOURTRAVEL_WOOCOMMERCE_CABIN_TYPE_ATT', 'bookyourtravel_pa_cabin_type' );
	
if ( ! defined( 'BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_ATT' ) )
    define( 'BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_ATT', 'bookyourtravel_pa_car_rental' );
	
class BookYourTravel_Theme_WooCommerce extends BookYourTravel_BaseSingleton {

	private $accommodation_product_slug = 'bookyourtravel-accommodation-product';
	private $tour_product_slug = 'bookyourtravel-tour-product';
	private $cruise_product_slug = 'bookyourtravel-cruise-product';
	private $car_rental_product_slug = 'bookyourtravel-car-rental-product';
	private $page_sidebar_positioning = '';
	private $default_product_placeholder_image_src = '';
	private	$date_format = '';
	private $use_woocommerce_for_checkout = false;
	
	protected function __construct() {
	
		global $bookyourtravel_theme_globals;

		$this->page_sidebar_positioning = $bookyourtravel_theme_globals->get_woocommerce_pages_sidebar_position();
		$this->page_sidebar_positioning = empty($this->page_sidebar_positioning) ? '' : $this->page_sidebar_positioning;
		$this->default_product_placeholder_image_src = $bookyourtravel_theme_globals->get_woocommerce_product_placeholder_image();
		$this->date_format = get_option('date_format');
		$this->use_woocommerce_for_checkout = $bookyourtravel_theme_globals->use_woocommerce_for_checkout();
		
        // our parent class might contain shared code in its constructor
        parent::__construct();		
    }
	
    public function init() {
	
		if (BookYourTravel_Theme_Utils::is_woocommerce_active()) {
		
			add_action('init', array( $this, 'setup'));
			add_action('bookyourtravel_before_delete_accommodation_booking', array( $this, 'before_delete_accommodation_booking'));
			add_action('bookyourtravel_before_delete_tour_booking', array( $this, 'before_delete_tour_booking'));
			add_action('bookyourtravel_before_delete_cruise_booking', array( $this, 'before_delete_cruise_booking'));
			add_action('bookyourtravel_before_delete_car_rental_booking', array( $this, 'before_delete_car_rental_booking'));
			
			add_action('wp_ajax_accommodation_booking_add_to_cart_ajax_request', array( $this, 'accommodation_booking_add_to_cart_ajax_request'));
			add_action('wp_ajax_nopriv_accommodation_booking_add_to_cart_ajax_request', array( $this, 'accommodation_booking_add_to_cart_ajax_request'));
			
			add_action('wp_ajax_tour_booking_add_to_cart_ajax_request', array( $this, 'tour_booking_add_to_cart_ajax_request'));
			add_action('wp_ajax_nopriv_tour_booking_add_to_cart_ajax_request', array( $this, 'tour_booking_add_to_cart_ajax_request'));

			add_action('wp_ajax_cruise_booking_add_to_cart_ajax_request', array( $this, 'cruise_booking_add_to_cart_ajax_request'));
			add_action('wp_ajax_nopriv_cruise_booking_add_to_cart_ajax_request', array( $this, 'cruise_booking_add_to_cart_ajax_request'));
			
			add_action('wp_ajax_car_rental_booking_add_to_cart_ajax_request', array( $this, 'car_rental_booking_add_to_cart_ajax_request'));
			add_action('wp_ajax_nopriv_car_rental_booking_add_to_cart_ajax_request', array( $this, 'car_rental_booking_add_to_cart_ajax_request'));
		}	
	}

	function deactivate_wcml_product_unduplicate( $not_active, $cart_content ) {
		return true;
	}			
	
	/**
	 * Hook in woocommerce actions and filters
	 */
	function setup() {
	
		remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
		remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
		remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
		
		add_action('woocommerce_before_main_content', array($this, 'before_main_content'), 30);
		add_action('woocommerce_after_main_content', array($this, 'after_main_content'), 30);		
		add_action('woocommerce_before_main_content', array($this, 'customized_breadcrumbs'), 10, 0);
		add_filter('woocommerce_cart_item_name', array( $this, 'cart_item_name'), 20, 3);
		add_filter('woocommerce_order_item_name', array( $this, 'order_item_name'), 20, 3);
		add_filter('woocommerce_cart_item_thumbnail', array($this, 'cart_item_thumbnail'), 20, 3);
		add_filter('woocommerce_variation_is_purchasable', array($this, 'variation_is_purchasable'), 20, 2);		   
		add_action('woocommerce_before_calculate_totals', array( $this, 'add_custom_total_price'), 1, 1);
		add_action('woocommerce_before_order_itemmeta', array($this, 'before_order_itemmeta'), 20, 3);
		add_action('woocommerce_add_order_item_meta', array( $this, 'add_order_item_meta'), 10, 3);
		add_action('woocommerce_checkout_order_processed', array( $this, 'checkout_order_processed'), 10, 2);
		add_action('woocommerce_order_status_changed', array( $this, 'order_status_changed'), 10, 3 );
		add_action('woocommerce_delete_order_items', array( $this, 'delete_order_items'), 10, 1);
		add_action('woocommerce_cart_updated', array( $this, 'cart_updated') );

		add_filter('loop_shop_columns', array($this, 'loop_shop_columns'));
		add_filter('post_class', array($this, 'post_class'));
		add_filter('template_include', array($this, 'template_include' ));
		add_filter( 'woocommerce_checkout_fields' , array($this, 'override_checkout_fields' ));
		
		add_filter( 'wcml_exception_duplicate_products_in_cart', array( $this, 'deactivate_wcml_product_unduplicate'), 10, 2 );
		add_filter( 'woocommerce_email_recipient_new_order', array( $this, 'modify_email_headers_filter_function'), 10, 2);
 	}	

	function modify_email_headers_filter_function( $recipients, $order ) {

		global $bookyourtravel_theme_of_custom, $bookyourtravel_theme_globals, $bookyourtravel_accommodation_helper, $bookyourtravel_tour_helper, $bookyourtravel_cruise_helper, $bookyourtravel_car_rental_helper;
		
		if ($order != null) {
		
			$items = $order->get_items();
			
			if ($items != null) {
			
				foreach ($items as $item_id => $item) {
				
					$contact_emails = '';
					
					$booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID, true);
					if ($booking_id > 0) {
						$booking_object = $bookyourtravel_accommodation_helper->get_accommodation_booking($booking_id);
						if ($booking_object != null) {
							$contact_emails = trim(get_post_meta($booking_object->accommodation_id, 'accommodation_contact_email', true ));
						}
					} else {
						$booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID, true);
						if ($booking_id > 0) {
							$booking_object = $bookyourtravel_tour_helper->get_tour_booking($booking_id);
							if ($booking_object != null) {
								$contact_emails = trim(get_post_meta($booking_object->tour_id, 'tour_contact_email', true ));
							}
						} else {
							$booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID, true);
							if ($booking_id > 0) {
								$booking_object = $bookyourtravel_cruise_helper->get_cruise_booking($booking_id);
								if ($booking_object != null) {
									$contact_emails = trim(get_post_meta($booking_object->cruise_id, 'cruise_contact_email', true ));
								}
							} else {
								$booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID, true);
								if ($booking_id > 0) {
									$booking_object = $bookyourtravel_car_rental_helper->get_car_rental_booking($booking_id);
									if ($booking_object != null) {
										$contact_emails = trim(get_post_meta($booking_object->car_rental_id, 'car_rental_contact_email', true ));
									}
								}							
							}						
						}
					}
						
					if (!empty($contact_emails)) {
					
						$emails_array = explode(';', $contact_emails);

						if (!empty($recipients)) {
							$recipients .= ',';
						}
						foreach ($emails_array as $email) {
							if (!empty($email)) {
								$recipients .= $email . ',';
							}
						}
						$recipients = rtrim($recipients, ',');
					}
				}		
			}
		}
		
		return $recipients;
	}
	
	function override_checkout_fields($fields) {
		
		global $bookyourtravel_theme_globals, $bookyourtravel_theme_of_custom;
		
		$booking_form_fields = $bookyourtravel_theme_globals->get_booking_form_fields();

		foreach ($booking_form_fields as $booking_field) { 
		
			$field_type = $booking_field['type'];
			$field_hidden = isset($booking_field['hide']) && $booking_field['hide'] == 1 ? true : false;
			$field_required = isset($booking_field['required']) && $booking_field['required'] == '1' ? true : false;
			$field_id = $booking_field['id'];
			$field_label = isset($booking_field['label']) ? $booking_field['label'] : '';
			$field_label = $bookyourtravel_theme_of_custom->get_translated_dynamic_string($bookyourtravel_theme_of_custom->get_option_id_context('booking_form_fields') . ' ' . $field_label, $field_label);			
			
			$woo_field_id = '';
			
			if ($field_id == 'first_name' || $field_id == 'last_name' || $field_id == 'phone' || $field_id == 'email' || $field_id == 'country' || $field_id == 'state' || $field_id == 'address_2' || $field_id == 'company') {
				$woo_field_id = 'billing_' . $field_id;
			} elseif ($field_id == 'zip') {
				$woo_field_id = 'billing_postcode';
			} elseif ($field_id == 'town') {
				$woo_field_id = 'billing_city';
			} elseif ($field_id == 'address') {
				$woo_field_id = 'billing_address_1';
			}
			
			if ($field_hidden) {
				if (isset($fields['billing']) && array_key_exists($woo_field_id, $fields['billing'])) {
					unset($fields['billing'][$woo_field_id]);
				}
			} else {
				// field is not hidden
				if (empty($woo_field_id) && !isset($fields['billing'][$field_id])) {
					
					// and isn't a woo field ie is one we created dynamically.					
					$woo_field_type = 'text';
					if ($field_type == 'textarea')
						$woo_field_type = 'textarea';
					else if ($field_type == 'email')
						$woo_field_type = 'email';
						
					$fields['billing'][$field_id] = array(
						'type' => $woo_field_type,
						'label' => $field_label,
						'placeholder' => $field_label,
						'class' => array('form-row-wide'),
						'required' => ($field_required ? true : false)
					);
				
				}
			}
		}
		
		return $fields;
	}
	
	function before_delete_accommodation_booking($booking_id) {
	
		if(file_exists(WP_PLUGIN_DIR .'/woocommerce/includes/api/interface-wc-api-handler.php')) {
		
			global $bookyourtravel_accommodation_helper;
			$booking_entry = $bookyourtravel_accommodation_helper->get_accommodation_booking($booking_id);
			
			if ($booking_entry != null) {	
				
				$woo_order_id = $booking_entry->woo_order_id;
				
				if ($woo_order_id > 0) {
				
					$wc_api_server = new WC_API_Server('/');
					$wc_orders = new WC_API_Orders($wc_api_server);
					
					$wc_orders->delete_order( $woo_order_id, true );	
				}
			}
		}
	}
	
	function before_delete_tour_booking($booking_id) {
	
		if(file_exists(WP_PLUGIN_DIR .'/woocommerce/includes/api/interface-wc-api-handler.php')) {
		
			global $bookyourtravel_tour_helper;
			$booking_entry = $bookyourtravel_tour_helper->get_tour_booking($booking_id);
			
			if ($booking_entry != null) {	
				
				$woo_order_id = $booking_entry->woo_order_id;
				
				if ($woo_order_id > 0) {
				
					$wc_api_server = new WC_API_Server('/');
					$wc_orders = new WC_API_Orders($wc_api_server);
					
					$wc_orders->delete_order( $woo_order_id, true );	
				}
			}
		}
	}
	
	function before_delete_cruise_booking($booking_id) {
	
		if(file_exists(WP_PLUGIN_DIR .'/woocommerce/includes/api/interface-wc-api-handler.php')) {
		
			global $bookyourtravel_cruise_helper;
			$booking_entry = $bookyourtravel_cruise_helper->get_cruise_booking($booking_id);
			
			if ($booking_entry != null) {	
				
				$woo_order_id = $booking_entry->woo_order_id;
				
				if ($woo_order_id > 0) {
				
					$wc_api_server = new WC_API_Server('/');
					$wc_orders = new WC_API_Orders($wc_api_server);
					
					$wc_orders->delete_order( $woo_order_id, true );	
				}
			}
		}
	}
	
	function before_delete_car_rental_booking($booking_id) {
	
		if(file_exists(WP_PLUGIN_DIR .'/woocommerce/includes/api/interface-wc-api-handler.php')) {
		
			global $bookyourtravel_car_rental_helper;
			$booking_entry = $bookyourtravel_car_rental_helper->get_car_rental_booking($booking_id);
			
			if ($booking_entry != null) {	
				
				$woo_order_id = $booking_entry->woo_order_id;
				
				if ($woo_order_id > 0) {
				
					$wc_api_server = new WC_API_Server('/');
					$wc_orders = new WC_API_Orders($wc_api_server);
					
					$wc_orders->delete_order( $woo_order_id, true );	
				}
			}
		}
	}
	
	function cart_updated() {
		
		global $bookyourtravel_accommodation_helper, $bookyourtravel_cruise_helper, $bookyourtravel_tour_helper, $bookyourtravel_car_rental_helper, $woocommerce ;
		
		if ( isset( $_GET[ 'remove_item' ] ) ){
			
			$cart_item_key = $_GET[ 'remove_item' ];
			
			$cart_item_meta = $woocommerce->session->get(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key);
		
			if ($cart_item_meta != null) {
			
				$accommodation_booking_id = 0;
				if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID])) {
					$accommodation_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID]);
					if ($accommodation_booking_id > 0) {
						$bookyourtravel_accommodation_helper->delete_accommodation_booking($accommodation_booking_id);
					}
				}
				
				$tour_booking_id = 0;
				if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID])) {
					$tour_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID]);
					if ($tour_booking_id > 0) {
						$bookyourtravel_tour_helper->delete_tour_booking($tour_booking_id);
					}
				}
				
				$cruise_booking_id = 0;
				if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID])) {
					$cruise_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID]);
					if ($cruise_booking_id > 0) {
						$bookyourtravel_cruise_helper->delete_cruise_booking($cruise_booking_id);
					}
				}
				
				$car_rental_booking_id = 0;
				if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID])) {
					$car_rental_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID]);
					if ($car_rental_booking_id > 0) {
						$bookyourtravel_car_rental_helper->delete_car_rental_booking($car_rental_booking_id);
					}
				}
			}
		} 
	}
	
	function delete_order_items( $order_id ) {

		global $bookyourtravel_accommodation_helper, $bookyourtravel_tour_helper, $bookyourtravel_cruise_helper, $bookyourtravel_car_rental_helper;
		
		$order = new WC_Order( $order_id );
		
		if ($order != null) {

			$items = $order->get_items();
			
			foreach ($items as $item_id => $item) {

				$accommodation_booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID, true);
				if ($accommodation_booking_id) {				
					$bookyourtravel_accommodation_helper->delete_accommodation_booking(intval($accommodation_booking_id));
				}
				
				$tour_booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID, true);
				if ($tour_booking_id) {				
					$bookyourtravel_tour_helper->delete_tour_booking(intval($tour_booking_id));
				}
				
				$cruise_booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID, true);
				if ($cruise_booking_id) {				
					$bookyourtravel_cruise_helper->delete_cruise_booking(intval($cruise_booking_id));
				}
				
				$car_rental_booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID, true);
				if ($car_rental_booking_id > 0) {
					$bookyourtravel_car_rental_helper->delete_car_rental_booking(intval($car_rental_booking_id));
				}
			}
		}
	}	

	function order_status_changed( $order_id, $old_status, $new_status ) {

		global $bookyourtravel_accommodation_helper, $bookyourtravel_tour_helper, $bookyourtravel_cruise_helper, $bookyourtravel_car_rental_helper;
		
		$order = new WC_Order( $order_id );

		$items = $order->get_items();
		
		if ($items != null) {
		
			foreach ($items as $item_id => $item) {

				$accommodation_booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID, true);
				if ($accommodation_booking_id) {				
					$bookyourtravel_accommodation_helper->update_booking_woocommerce_info($accommodation_booking_id, null, null, $new_status);
				}	
				
				$tour_booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID, true);
				if ($tour_booking_id) {				
					$bookyourtravel_tour_helper->update_booking_woocommerce_info($tour_booking_id, null, null, $new_status);
				}
				
				$cruise_booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID, true);
				if ($cruise_booking_id) {				
					$bookyourtravel_cruise_helper->update_booking_woocommerce_info($cruise_booking_id, null, null, $new_status);
				}
				
				$car_rental_booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID, true);
				if ($car_rental_booking_id) {				
					$bookyourtravel_car_rental_helper->update_booking_woocommerce_info($car_rental_booking_id, null, null, $new_status);
				}
			}
		}
	}
	
	function checkout_order_processed( $order_id, $posted ) {
		
		global $bookyourtravel_accommodation_helper, $bookyourtravel_tour_helper, $bookyourtravel_cruise_helper, $bookyourtravel_car_rental_helper, $woocommerce, $bookyourtravel_theme_globals;
		
		$order = new WC_Order( $order_id );
		
		if ($order != null) {
		
			$status = $order->get_status();

			if ($woocommerce->cart != null) {
			
				foreach ( $woocommerce->cart->cart_contents as $key => $value ) {
				
					$cart_item_meta = $woocommerce->session->get(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $key);
					
					if ($cart_item_meta != null) {
					
						$booking_object = new stdClass();
						
						$booking_object->first_name = (isset($posted['billing_first_name']) ? sanitize_text_field($posted['billing_first_name']) : '');
						$booking_object->last_name = (isset($posted['billing_last_name']) ? sanitize_text_field($posted['billing_last_name']) : '');
						$booking_object->company = (isset($posted['billing_company']) ? sanitize_text_field($posted['billing_company']) : '');
						$booking_object->phone = (isset($posted['billing_phone']) ? sanitize_text_field($posted['billing_phone']) : '');
						$booking_object->email = (isset($posted['billing_email']) ? sanitize_text_field($posted['billing_email']) : '');
						$booking_object->address = (isset($posted['billing_address_1']) ? sanitize_text_field($posted['billing_address_1']) : '');
						$booking_object->address_2 = (isset($posted['billing_address_2']) ? sanitize_text_field($posted['billing_address_2']) : '');
						$booking_object->town = (isset($posted['billing_city']) ? sanitize_text_field($posted['billing_city']) : '');
						$booking_object->zip = (isset($posted['billing_postcode']) ? sanitize_text_field($posted['billing_postcode']) : '');
						$booking_object->state = (isset($posted['billing_state']) ? sanitize_text_field($posted['billing_state']) : '');
						$booking_object->country = (isset($posted['billing_country']) ? sanitize_text_field($posted['billing_country']) : '');
						$booking_object->special_requirements = (isset($posted['special_requirements']) ? sanitize_text_field($posted['special_requirements']) : '');
						$booking_object->other_fields = array();
						
						$booking_form_fields = $bookyourtravel_theme_globals->get_booking_form_fields();
						
						foreach ($booking_form_fields as $booking_field) { 

							$field_hidden = isset($booking_field['hide']) && $booking_field['hide'] == 1 ? true : false;
							$field_id = $booking_field['id'];
							
							if (!$field_hidden && isset($posted[$field_id])) {
							
								if ($field_id != 'first_name' &&
									$field_id != 'last_name' &&
									$field_id != 'company' &&
									$field_id != 'email' &&
									$field_id != 'phone' &&
									$field_id != 'address' &&
									$field_id != 'address_2' &&
									$field_id != 'town' &&
									$field_id != 'zip' &&
									$field_id != 'state' &&
									$field_id != 'country' &&
									$field_id != 'special_requirements') {

									$booking_object->other_fields[$field_id] = sanitize_text_field($posted[$field_id]);										
								}
							}
						}

						$accommodation_booking_id = 0;
						if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID])) {
							$accommodation_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID]);
							
							if ($accommodation_booking_id > 0) {

								$bookyourtravel_accommodation_helper->update_booking_woocommerce_info($accommodation_booking_id, $key, $order_id, $status);
								$bookyourtravel_accommodation_helper->update_accommodation_booking($accommodation_booking_id, $booking_object);
							}
						}
						
						$tour_booking_id = 0;
						if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID])) {
							$tour_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID]);
							
							if ($tour_booking_id > 0) {

								$bookyourtravel_tour_helper->update_booking_woocommerce_info($tour_booking_id, $key, $order_id, $status);
								$bookyourtravel_tour_helper->update_tour_booking($tour_booking_id, $booking_object);
							}
						}
						
						$cruise_booking_id = 0;
						if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID])) {
							$cruise_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID]);
							
							if ($cruise_booking_id > 0) {

								$bookyourtravel_cruise_helper->update_booking_woocommerce_info($cruise_booking_id, $key, $order_id, $status);
								$bookyourtravel_cruise_helper->update_cruise_booking($cruise_booking_id, $booking_object);
							}
						}
						
						$car_rental_booking_id = 0;
						if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID])) {
							$car_rental_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID]);
							
							if ($car_rental_booking_id > 0) {

								$bookyourtravel_car_rental_helper->update_booking_woocommerce_info($car_rental_booking_id, $key, $order_id, $status);
								$bookyourtravel_car_rental_helper->update_car_rental_booking($car_rental_booking_id, $booking_object);
							}
						}
					}
				}
			}
		}
	}
	
	function add_order_item_meta($item_id, $values, $cart_item_key ) {

		global $woocommerce;
		$cart_item_meta = $woocommerce->session->get(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key);
		
		if ($cart_item_meta != null) {
			
			$accommodation_booking_id = 0;
			if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID])) {
				$accommodation_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID]);
				if ($accommodation_booking_id) {			
					wc_add_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID, $accommodation_booking_id, true);
				}
			};
			
			$tour_booking_id = 0;
			if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID])) {
				$tour_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID]);
				if ($tour_booking_id) {			
					wc_add_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID, $tour_booking_id, true);
				}
			};

			$cruise_booking_id = 0;
			if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID])) {
				$cruise_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID]);
				if ($cruise_booking_id) {			
					wc_add_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID, $cruise_booking_id, true);
				}
			};
			
			$car_rental_booking_id = 0;
			if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID])) {
				$car_rental_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID]);
				if ($car_rental_booking_id) {			
					wc_add_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID, $car_rental_booking_id, true);
				}
			};
		}
	}	

	function cart_item_thumbnail($image, $cart_item, $cart_item_key) {
	
		if (isset($cart_item['data'])) {
		
			$object_class = get_class($cart_item['data']);
			
			if ($object_class == 'WC_Product_Variation' && isset($cart_item['data']) && $cart_item['data']->post != null) {
			
				global $woocommerce, $bookyourtravel_accommodation_helper, $bookyourtravel_tour_helper, $bookyourtravel_cruise_helper, $bookyourtravel_car_rental_helper;
				
				if ($cart_item['data']->post->post_name == $this->accommodation_product_slug) {
				
					$product_id   	= $cart_item['product_id'];
					$variation_id   = $cart_item['variation_id'];
					
					$variation = new WC_Product_Variation($variation_id);
					$attributes = $variation->get_variation_attributes();	
					
					$cart_item_meta = $woocommerce->session->get(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key);
					
					if ($cart_item_meta != null) {

						$accommodation_booking_id = 0;
						$accommodation_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID]);
						
						if ($accommodation_booking_id > 0) {
							
							$booking_entry = $bookyourtravel_accommodation_helper->get_accommodation_booking($accommodation_booking_id);
							
							if ($booking_entry != null) {
							
								$accommodation_id = $booking_entry->accommodation_id;
								$room_type_id = $booking_entry->room_type_id;
								
								$accommodation_obj = new BookYourTravel_Accommodation($accommodation_id);
								$image_title = $accommodation_obj->get_title();
								$main_image_src = $accommodation_obj->get_main_image();	
								if (empty($main_image_src)) {
									$main_image_src = BookYourTravel_Theme_Utils::get_file_uri('/images/uploads/img.jpg');
								}
								if ($room_type_id > 0) {
									$room_type_obj = new BookYourTravel_Room_Type(intval($room_type_id));
									$main_image_src = $room_type_obj->get_main_image('medium');
									$image_title = $room_type_obj->get_title();
								}
								
								if (empty($main_image_src)) {
									$main_image_src = $this->default_product_placeholder_image_src;
								}
									
								if (!empty($main_image_src)) {
									$image = "<img src='$main_image_src' alt='$image_title' />";
								}							
							}
						}
					}
				}

				if ($cart_item['data']->post->post_name == $this->tour_product_slug) {
				
					$product_id   	= $cart_item['product_id'];
					$variation_id   = $cart_item['variation_id'];
					
					$variation = new WC_Product_Variation($variation_id);
					$attributes = $variation->get_variation_attributes();	
					
					$cart_item_meta = $woocommerce->session->get(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key);
					
					if ($cart_item_meta != null) {

						$tour_booking_id = 0;
						$tour_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID]);
						
						if ($tour_booking_id > 0) {
							
							$booking_entry = $bookyourtravel_tour_helper->get_tour_booking($tour_booking_id);
							
							if ($booking_entry != null) {
							
								$tour_schedule = $bookyourtravel_tour_helper->get_tour_schedule($booking_entry->tour_schedule_id);
								$booking_entry->tour_id = $tour_schedule->tour_id;
								$tour_id = $booking_entry->tour_id;
								$tour_schedule_id = $booking_entry->tour_schedule_id;
								
								$tour_obj = new BookYourTravel_Tour($tour_id);
								$image_title = $tour_obj->get_title();
								$main_image_src = $tour_obj->get_main_image();	
								if (empty($main_image_src)) {
									$main_image_src = BookYourTravel_Theme_Utils::get_file_uri('/images/uploads/img.jpg');
								}
								
								if (empty($main_image_src)) {
									$main_image_src = $this->default_product_placeholder_image_src;
								}
									
								if (!empty($main_image_src)) {
									$image = "<img src='$main_image_src' alt='$image_title' />";
								}							
							}
						}
					}
				}
				
				if ($cart_item['data']->post->post_name == $this->cruise_product_slug) {
				
					$product_id   	= $cart_item['product_id'];
					$variation_id   = $cart_item['variation_id'];
					
					$variation = new WC_Product_Variation($variation_id);
					$attributes = $variation->get_variation_attributes();	
					
					$cart_item_meta = $woocommerce->session->get(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key);
					
					if ($cart_item_meta != null) {

						$cruise_booking_id = 0;
						$cruise_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID]);
						
						if ($cruise_booking_id > 0) {
							
							$booking_entry = $bookyourtravel_cruise_helper->get_cruise_booking($cruise_booking_id);
							
							if ($booking_entry != null) {
							
								$cruise_schedule = $bookyourtravel_cruise_helper->get_cruise_schedule($booking_entry->cruise_schedule_id);
								$booking_entry->tour_id = $cruise_schedule->cruise_id;
								$booking_entry->cabin_type_id = $cruise_schedule->cabin_type_id;
								
								$cruise_id = $booking_entry->cruise_id;
								$cabin_type_id = $booking_entry->cabin_type_id;
								
								$cruise_obj = new BookYourTravel_Cruise($cruise_id);
								$image_title = $cruise_obj->get_title();
								$main_image_src = $cruise_obj->get_main_image();	
								if (empty($main_image_src)) {
									$main_image_src = BookYourTravel_Theme_Utils::get_file_uri('/images/uploads/img.jpg');
								}
								if ($cabin_type_id > 0) {
									$cabin_type_obj = new BookYourTravel_Cabin_Type(intval($cabin_type_id));
									$main_image_src = $cabin_type_obj->get_main_image('medium');
									$image_title = $cabin_type_obj->get_title();
								}
								
								if (empty($main_image_src)) {
									$main_image_src = $this->default_product_placeholder_image_src;
								}
									
								if (!empty($main_image_src)) {
									$image = "<img src='$main_image_src' alt='$image_title' />";
								}							
							}
						}
					}
				}
				
				if ($cart_item['data']->post->post_name == $this->car_rental_product_slug) {
				
					$product_id   	= $cart_item['product_id'];
					$variation_id   = $cart_item['variation_id'];
					
					$variation = new WC_Product_Variation($variation_id);
					$attributes = $variation->get_variation_attributes();	
					
					$cart_item_meta = $woocommerce->session->get(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key);
					
					if ($cart_item_meta != null) {

						$car_rental_booking_id = 0;
						$car_rental_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID]);
						
						if ($car_rental_booking_id > 0) {
							
							$booking_entry = $bookyourtravel_car_rental_helper->get_car_rental_booking($car_rental_booking_id);
							
							if ($booking_entry != null) {
							
								$car_rental_obj = new BookYourTravel_Car_Rental($booking_entry->car_rental_id);
								$image_title = $car_rental_obj->get_title();
								$main_image_src = $car_rental_obj->get_main_image();	
								if (empty($main_image_src)) {
									$main_image_src = BookYourTravel_Theme_Utils::get_file_uri('/images/uploads/img.jpg');
								}
								
								if (empty($main_image_src)) {
									$main_image_src = $this->default_product_placeholder_image_src;
								}
									
								if (!empty($main_image_src)) {
									$image = "<img src='$main_image_src' alt='$image_title' />";
								}							
							}
						}
					}
				}
			}
		}
		
		return $image;
	}
	
	function template_include($template) {

		$find = array( );
		$file = '';
		
		global $post;
		
		if (isset($post) && 
			(
				$post->post_name == $this->accommodation_product_slug ||
				$post->post_name == $this->tour_product_slug ||
				$post->post_name == $this->cruise_product_slug ||
				$post->post_name == $this->car_rental_product_slug
			)
		) {
			$file 	= '404.php';
			$find[] = $file;
		}

		if ( $file ) {
			$template = locate_template( $find );
		}
		
		return $template;
	}
	
	// Show order details (from, to, transport type, dates etc) in order admin when viewing individual orders.
	function before_order_itemmeta($item_id, $item, $_product) {
	
		global $bookyourtravel_accommodation_helper, $bookyourtravel_tour_helper, $bookyourtravel_cruise_helper, $bookyourtravel_car_rental_helper;
		
		$product_id   	= $item['product_id'];
		$variation_id   = $item['variation_id'];
		
		if (!isset($variation_id) || $variation_id == 0) {
			return;
		}
		$variation = new WC_Product_Variation($variation_id);
		
		$accommodation_booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID, true);
		if ($accommodation_booking_id) {
		
			$booking_entry = $bookyourtravel_accommodation_helper->get_accommodation_booking($accommodation_booking_id);
			
			if ($booking_entry != null && $variation != null) {
			
				$accommodation_id = $booking_entry->accommodation_id;
				$accommodation_obj = new BookYourTravel_Accommodation($accommodation_id);
				$room_type_obj = null;
				$room_type_id = $booking_entry->room_type_id;
				if ($room_type_id > 0) {
					$room_type_obj = new BookYourTravel_Room_Type($room_type_id);
				}
				$adults = $booking_entry->adults;
				$children = $booking_entry->children;
				$room_count = $booking_entry->room_count;
				$date_from = $booking_entry->date_from;
				$date_from = date($this->date_format, strtotime($date_from));
				$date_to = $booking_entry->date_to;
				$date_to = date($this->date_format, strtotime($date_to));
				
				$extra_items_string = '';				
				$extra_items_array = array();
				if (!empty($booking_entry->extra_items)) {
					$extra_items_array = unserialize($booking_entry->extra_items);
				}
				
				if ($extra_items_array != null) {
					foreach ($extra_items_array as $extra_item_id => $extra_item_quantity) {
						$extra_item_obj = new BookYourTravel_Extra_Item($extra_item_id);
						$extra_items_string .= $extra_item_quantity . ' x ' . $extra_item_obj->get_title() . ', ';
					}
				}
			
				$extra_items_string = trim(rtrim($extra_items_string, ', '));
				
				$item_text = '<br />';
				$item_text .= sprintf(esc_html__('%s', 'bookyourtravel'), $accommodation_obj->get_title()) . '<br />';
				if ($room_type_obj) {
					$item_text .= sprintf(esc_html__('%s', 'bookyourtravel'), $room_type_obj->get_title()) . '<br />';
				}
				$item_text .= sprintf(esc_html__('Dates: %s to %s', 'bookyourtravel'), $date_from, $date_to) . '<br />';
				$item_text .= sprintf(esc_html__('People: %d adults, %d children', 'bookyourtravel'), $adults, $children) . '<br />';
				
				if (!empty($extra_items_string)) {
					$item_text .= esc_html__('Extras: ', 'bookyourtravel') . $extra_items_string;
				}
				
				echo $item_text;
			}
		}

		$tour_booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID, true);
		if ($tour_booking_id) {
		
			$booking_entry = $bookyourtravel_tour_helper->get_tour_booking($tour_booking_id);
			
			if ($booking_entry != null && $variation != null) {
			
				$tour_schedule = $bookyourtravel_tour_helper->get_tour_schedule($booking_entry->tour_schedule_id);
				$booking_entry->tour_id = $tour_schedule->tour_id;

				$tour_id = $booking_entry->tour_id;
				$tour_obj = new BookYourTravel_Tour($tour_id);
				$adults = $booking_entry->adults;
				$children = $booking_entry->children;
				$tour_date = $booking_entry->tour_date;
				$tour_date = date($this->date_format, strtotime($tour_date));
				
				$extra_items_string = '';				
				$extra_items_array = array();
				if (!empty($booking_entry->extra_items)) {
					$extra_items_array = unserialize($booking_entry->extra_items);
				}
				
				if ($extra_items_array != null) {
					foreach ($extra_items_array as $extra_item_id => $extra_item_quantity) {
						$extra_item_obj = new BookYourTravel_Extra_Item($extra_item_id);
						$extra_items_string .= $extra_item_quantity . ' x ' . $extra_item_obj->get_title() . ', ';
					}
				}
			
				$extra_items_string = trim(rtrim($extra_items_string, ', '));
				
				$item_text = '<br />';
				$item_text .= sprintf(esc_html__('%s', 'bookyourtravel'), $tour_obj->get_title()) . '<br />';
				$item_text .= sprintf(esc_html__('Tour date: %s', 'bookyourtravel'), $tour_date) . '<br />';
				$item_text .= sprintf(esc_html__('People: %d adults, %d children', 'bookyourtravel'), $adults, $children) . '<br />';
				
				if (!empty($extra_items_string)) {
					$item_text .= esc_html__('Extras: ', 'bookyourtravel') . $extra_items_string;
				}
				
				echo $item_text;
			}
		}
		
		$cruise_booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID, true);
		if ($cruise_booking_id) {
		
			$booking_entry = $bookyourtravel_cruise_helper->get_cruise_booking($cruise_booking_id);
			
			if ($booking_entry != null && $variation != null) {
			
				$cruise_schedule = $bookyourtravel_cruise_helper->get_cruise_schedule($booking_entry->cruise_schedule_id);
				$booking_entry->tour_id = $cruise_schedule->cruise_id;
				$booking_entry->cabin_type_id = $cruise_schedule->cabin_type_id;
				
				$cruise_id = $booking_entry->cruise_id;
				$cruise_obj = new BookYourTravel_Cruise($cruise_id);
				$cabin_type_obj = null;
				$cabin_type_id = $booking_entry->cabin_type_id;
				if ($cabin_type_id > 0) {
					$cabin_type_obj = new BookYourTravel_Cabin_Type($cabin_type_id);
				}
				$adults = $booking_entry->adults;
				$children = $booking_entry->children;
				$cruise_date = $booking_entry->cruise_date;
				$cruise_date = date($this->date_format, strtotime($cruise_date));
				
				$extra_items_string = '';				
				$extra_items_array = array();
				if (!empty($booking_entry->extra_items)) {
					$extra_items_array = unserialize($booking_entry->extra_items);
				}
				
				if ($extra_items_array != null) {
					foreach ($extra_items_array as $extra_item_id => $extra_item_quantity) {
						$extra_item_obj = new BookYourTravel_Extra_Item($extra_item_id);
						$extra_items_string .= $extra_item_quantity . ' x ' . $extra_item_obj->get_title() . ', ';
					}
				}
			
				$extra_items_string = trim(rtrim($extra_items_string, ', '));
				
				$item_text = '<br />';
				$item_text .= sprintf(esc_html__('%s', 'bookyourtravel'), $cruise_obj->get_title()) . '<br />';
				if ($cabin_type_obj) {
					$item_text .= sprintf(esc_html__('%s', 'bookyourtravel'), $cabin_type_obj->get_title()) . '<br />';
				}
				$item_text .= sprintf(esc_html__('Cruise date: %s', 'bookyourtravel'), $cruise_date) . '<br />';
				$item_text .= sprintf(esc_html__('People: %d adults, %d children', 'bookyourtravel'), $adults, $children) . '<br />';
				
				if (!empty($extra_items_string)) {
					$item_text .= esc_html__('Extras: ', 'bookyourtravel') . $extra_items_string;
				}
				
				echo $item_text;
			}
		}
		
		$car_rental_booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID, true);
		if ($car_rental_booking_id) {
		
			$booking_entry = $bookyourtravel_car_rental_helper->get_car_rental_booking($car_rental_booking_id);
			
			if ($booking_entry != null && $variation != null) {
			
				$car_rental_id = $booking_entry->car_rental_id;
				$car_rental_obj = new BookYourTravel_Car_Rental($car_rental_id);

				$start_date = $booking_entry->from_day;
				$start_date = date($this->date_format, strtotime($start_date));
				$end_date = $booking_entry->to_day;
				$end_date = date($this->date_format, strtotime($end_date));
				
				$extra_items_string = '';				
				$extra_items_array = array();
				if (!empty($booking_entry->extra_items)) {
					$extra_items_array = unserialize($booking_entry->extra_items);
				}
				
				if ($extra_items_array != null) {
					foreach ($extra_items_array as $extra_item_id => $extra_item_quantity) {
						$extra_item_obj = new BookYourTravel_Extra_Item($extra_item_id);
						$extra_items_string .= $extra_item_quantity . ' x ' . $extra_item_obj->get_title() . ', ';
					}
				}
			
				$extra_items_string = trim(rtrim($extra_items_string, ', '));
				
				$item_text = '<br />';
				$item_text .= sprintf(esc_html__('%s', 'bookyourtravel'), $car_rental_obj->get_title()) . '<br />';
				$item_text .= sprintf(esc_html__('From date: %s', 'bookyourtravel'), $start_date) . '<br />';
				$item_text .= sprintf(esc_html__('To date: %s', 'bookyourtravel'), $end_date) . '<br />';
				$item_text .= sprintf(esc_html__('Pick up: %s', 'bookyourtravel'), $booking_entry->pick_up_title) . '<br />';
				$item_text .= sprintf(esc_html__('Drop off: %s', 'bookyourtravel'), $booking_entry->drop_off_title) . '<br />';
				
				if (!empty($extra_items_string)) {
					$item_text .= esc_html__('Extras: ', 'bookyourtravel') . $extra_items_string;
				}
				
				echo $item_text;
			}
		}
	}
	
	function variation_is_purchasable($purchasable, $product_variation) {
	
		$object_class = get_class($product_variation);
		
		if ($object_class == 'WC_Product_Variation' && 
			($product_variation->post->post_name == $this->accommodation_product_slug || 
			$product_variation->post->post_name == $this->tour_product_slug ||
			$product_variation->post->post_name == $this->cruise_product_slug ||
			$product_variation->post->post_name == $this->car_rental_product_slug)) {
			// mark purchasable as true even though we have not specified product price when creating product and variation, which allows us to set the price at the time product is added to cart.
			$purchasable = true;
		}
		
		return $purchasable;
	}
	
	function add_custom_total_price($cart_object) {
		
		// this is where we access our booking object, get price, and update cart with it to have things synced.
		global $woocommerce, $bookyourtravel_accommodation_helper, $bookyourtravel_tour_helper, $bookyourtravel_cruise_helper, $bookyourtravel_car_rental_helper;
		
		foreach ( $cart_object->cart_contents as $key => $value ) {
		
			$cart_item_meta = $woocommerce->session->get(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $key);
			
			if ($cart_item_meta != null) {

				if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID])) {
				
					$accommodation_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID]);
					if ($accommodation_booking_id > 0) {
						$booking_entry = $bookyourtravel_accommodation_helper->get_accommodation_booking($accommodation_booking_id);
						
						if ($booking_entry != null) {
							$value['data']->price = $booking_entry->total_price;
						}
					}
				} else if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID])) {
				
					$tour_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID]);
					if ($tour_booking_id > 0) {
						$booking_entry = $bookyourtravel_tour_helper->get_tour_booking($tour_booking_id);
						
						if ($booking_entry != null) {
							$value['data']->price = $booking_entry->total_price;
						}
					}
				} else if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID])) {
				
					$cruise_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID]);
					if ($cruise_booking_id > 0) {
						$booking_entry = $bookyourtravel_cruise_helper->get_cruise_booking($cruise_booking_id);
						
						if ($booking_entry != null) {
							$value['data']->price = $booking_entry->total_price;
						}
					}
				} else if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID])) {
				
					$car_rental_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID]);
					if ($car_rental_booking_id > 0) {
						$booking_entry = $bookyourtravel_car_rental_helper->get_car_rental_booking($car_rental_booking_id);
						
						if ($booking_entry != null) {
							$value['data']->price = $booking_entry->total_price;
						}
					}
				}
			}
		}
	}
			
	function order_item_name($product_title, $item) {
	
		global $bookyourtravel_accommodation_helper, $bookyourtravel_tour_helper, $bookyourtravel_cruise_helper, $bookyourtravel_car_rental_helper;
		
		$product_id   	= $item['product_id'];
		$variation_id   = $item['variation_id'];
		
		if (!isset($variation_id) || $variation_id == 0) {
			return $product_title;
		}		
		
		$variation = new WC_Product_Variation($variation_id);
		
		if (isset($item[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID])) {
		
			$booking_id = (int)$item[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID];
			$booking_entry = $bookyourtravel_accommodation_helper->get_accommodation_booking($booking_id);
			
			if ($booking_entry != null && $variation != null) {
			
				$accommodation_id = $booking_entry->accommodation_id;
				$accommodation_id = BookYourTravel_Theme_Utils::get_current_language_post_id($accommodation_id, 'accommodation');				
				$accommodation_obj = new BookYourTravel_Accommodation($accommodation_id);
				$room_type_obj = null;
				$room_type_id = $booking_entry->room_type_id;
				$room_type_id = BookYourTravel_Theme_Utils::get_current_language_post_id($room_type_id, 'room_type');
				if ($room_type_id > 0) {
					$room_type_obj = new BookYourTravel_Room_Type($room_type_id);
				}
				$adults = $booking_entry->adults;
				$children = $booking_entry->children;
				$room_count = $booking_entry->room_count;
				$date_from = $booking_entry->date_from;
				$date_from = date($this->date_format, strtotime($date_from));
				$date_to = $booking_entry->date_to;
				$date_to = date($this->date_format, strtotime($date_to));
				
				$extra_items_string = '';				
				$extra_items_array = array();
				if (!empty($booking_entry->extra_items)) {
					$extra_items_array = unserialize($booking_entry->extra_items);
				}
				
				if (is_array($extra_items_array)) {
					foreach ($extra_items_array as $extra_item_id => $extra_item_quantity) {
						$extra_item_obj = new BookYourTravel_Extra_Item($extra_item_id);
						$extra_items_string .= $extra_item_quantity . ' x ' . $extra_item_obj->get_title() . ', ';
					}
				}
			
				$extra_items_string = trim(rtrim($extra_items_string, ', '));
				
				$product_title = '';
				$product_title .= sprintf(esc_html__('%s', 'bookyourtravel'), $accommodation_obj->get_title()) . '<br />';
				if ($room_type_obj) {
					$product_title .= sprintf(esc_html__('%s', 'bookyourtravel'), $room_type_obj->get_title()) . '<br />';
				}
				$product_title .= sprintf(esc_html__('Dates: %s to %s', 'bookyourtravel'), $date_from, $date_to) . '<br />';
				$product_title .= sprintf(esc_html__('People: %d adults, %d children', 'bookyourtravel'), $adults, $children) . '<br />';
				
				if (!empty($extra_items_string)) {
					$product_title .= esc_html__('Extras: ', 'bookyourtravel') . $extra_items_string;
				}
			}
		}
		
		if (isset($item[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID])) {
		
			$booking_id = (int)$item[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID];
			$booking_entry = $bookyourtravel_tour_helper->get_tour_booking($booking_id);
			
			if ($booking_entry != null && $variation != null) {
			
				$tour_schedule = $bookyourtravel_tour_helper->get_tour_schedule($booking_entry->tour_schedule_id);
				$booking_entry->tour_id = $tour_schedule->tour_id;
				$tour_id = $booking_entry->tour_id;
				$tour_id = BookYourTravel_Theme_Utils::get_current_language_post_id($tour_id, 'tour');
				$tour_obj = new BookYourTravel_Tour($tour_id);
				$adults = $booking_entry->adults;
				$children = $booking_entry->children;
				$tour_date = $booking_entry->tour_date;
				$tour_date = date($this->date_format, strtotime($tour_date));
				
				$extra_items_string = '';				
				$extra_items_array = array();
				if (!empty($booking_entry->extra_items)) {
					$extra_items_array = unserialize($booking_entry->extra_items);
				}
				
				if (is_array($extra_items_array)) {
					foreach ($extra_items_array as $extra_item_id => $extra_item_quantity) {
						$extra_item_obj = new BookYourTravel_Extra_Item($extra_item_id);
						$extra_items_string .= $extra_item_quantity . ' x ' . $extra_item_obj->get_title() . ', ';
					}
				}
			
				$extra_items_string = trim(rtrim($extra_items_string, ', '));
				
				$product_title = '';
				$product_title .= sprintf(esc_html__('%s', 'bookyourtravel'), $tour_obj->get_title()) . '<br />';

				$product_title .= sprintf(esc_html__('Tour date: %s', 'bookyourtravel'), $tour_date) . '<br />';
				$product_title .= sprintf(esc_html__('People: %d adults, %d children', 'bookyourtravel'), $adults, $children) . '<br />';
				
				if (!empty($extra_items_string)) {
					$product_title .= esc_html__('Extras: ', 'bookyourtravel') . $extra_items_string;
				}
			}
		}
		
		if (isset($item[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID])) {
		
			$booking_id = (int)$item[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID];
			$booking_entry = $bookyourtravel_cruise_helper->get_cruise_booking($booking_id);
			
			if ($booking_entry != null && $variation != null) {
			
				$cruise_schedule = $bookyourtravel_cruise_helper->get_cruise_schedule($booking_entry->cruise_schedule_id);
				$booking_entry->cruise_id = $cruise_schedule->cruise_id;
				$booking_entry->cabin_type_id = $cruise_schedule->cabin_type_id;
			
				$cruise_id = $booking_entry->cruise_id;
				$cruise_id = BookYourTravel_Theme_Utils::get_current_language_post_id($cruise_id, 'cruise');
				$cruise_obj = new BookYourTravel_Cruise($cruise_id);
				$cabin_type_obj = null;
				$cabin_type_id = $booking_entry->cabin_type_id;
				$cabin_type_id = BookYourTravel_Theme_Utils::get_current_language_post_id($cabin_type_id, 'cabin_type');
				if ($cabin_type_id > 0) {
					$cabin_type_obj = new BookYourTravel_Cabin_Type($cabin_type_id);
				}
				$adults = $booking_entry->adults;
				$children = $booking_entry->children;
				$cruise_date = $booking_entry->cruise_date;
				$cruise_date = date($this->date_format, strtotime($cruise_date));
				
				$extra_items_string = '';				
				$extra_items_array = array();
				if (!empty($booking_entry->extra_items)) {
					$extra_items_array = unserialize($booking_entry->extra_items);
				}
				
				if (is_array($extra_items_array)) {
					foreach ($extra_items_array as $extra_item_id => $extra_item_quantity) {
						$extra_item_obj = new BookYourTravel_Extra_Item($extra_item_id);
						$extra_items_string .= $extra_item_quantity . ' x ' . $extra_item_obj->get_title() . ', ';
					}
				}
			
				$extra_items_string = trim(rtrim($extra_items_string, ', '));
				
				$product_title = '';
				$product_title .= sprintf(esc_html__('%s', 'bookyourtravel'), $cruise_obj->get_title()) . '<br />';
				if ($cabin_type_obj) {
					$product_title .= sprintf(esc_html__('%s', 'bookyourtravel'), $cabin_type_obj->get_title()) . '<br />';
				}
				$product_title .= sprintf(esc_html__('Cruise date: %s', 'bookyourtravel'), $cruise_date) . '<br />';
				$product_title .= sprintf(esc_html__('People: %d adults, %d children', 'bookyourtravel'), $adults, $children) . '<br />';
				
				if (!empty($extra_items_string)) {
					$product_title .= esc_html__('Extras: ', 'bookyourtravel') . $extra_items_string;
				}
			}
		}
		
		if (isset($item[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID])) {
		
			$booking_id = (int)$item[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID];
			$booking_entry = $bookyourtravel_car_rental_helper->get_car_rental_booking($booking_id);
			
			if ($booking_entry != null && $variation != null) {
			
				$car_rental_id = $booking_entry->car_rental_id;
				$car_rental_id = BookYourTravel_Theme_Utils::get_current_language_post_id($car_rental_id, 'car_rental');
				$car_rental_obj = new BookYourTravel_Car_Rental($car_rental_id);

				$start_date = $booking_entry->from_day;
				$start_date = date($this->date_format, strtotime($start_date));
				$end_date = $booking_entry->to_day;
				$end_date = date($this->date_format, strtotime($end_date));

				$extra_items_string = '';				
				$extra_items_array = array();
				if (!empty($booking_entry->extra_items)) {
					$extra_items_array = unserialize($booking_entry->extra_items);
				}
				
				if (is_array($extra_items_array)) {
					foreach ($extra_items_array as $extra_item_id => $extra_item_quantity) {
						$extra_item_obj = new BookYourTravel_Extra_Item($extra_item_id);
						$extra_items_string .= $extra_item_quantity . ' x ' . $extra_item_obj->get_title() . ', ';
					}
				}
			
				$extra_items_string = trim(rtrim($extra_items_string, ', '));
				
				$product_title = '';
				$product_title .= sprintf(esc_html__('%s', 'bookyourtravel'), $car_rental_obj->get_title()) . '<br />';
				$product_title .= sprintf(esc_html__('From date: %s', 'bookyourtravel'), $start_date) . '<br />';
				$product_title .= sprintf(esc_html__('To date: %s', 'bookyourtravel'), $end_date) . '<br />';
				$product_title .= sprintf(esc_html__('Pick up: %s', 'bookyourtravel'), $booking_entry->pick_up_title) . '<br />';
				$product_title .= sprintf(esc_html__('Drop off: %s', 'bookyourtravel'), $booking_entry->drop_off_title) . '<br />';
				
				if (!empty($extra_items_string)) {
					$product_title .= esc_html__('Extras: ', 'bookyourtravel') . $extra_items_string;
				}
			}
		}
		
		return $product_title;
	}
	
	function cart_item_name($product_title, $cart_item, $cart_item_key){
	   
		global $woocommerce, $bookyourtravel_accommodation_helper, $bookyourtravel_tour_helper, $bookyourtravel_cruise_helper, $bookyourtravel_car_rental_helper;
		
		if (isset($cart_item['data'])) {
		
			$item_data = $cart_item['data'];
			
			$object_class = get_class($item_data);		
			
			if ( !$item_data || !$item_data->post || 
				(
					$item_data->post->post_name != $this->accommodation_product_slug &&
					$item_data->post->post_name != $this->tour_product_slug && 
					$item_data->post->post_name != $this->cruise_product_slug && 
					$item_data->post->post_name != $this->car_rental_product_slug
				) 
				|| $object_class != 'WC_Product_Variation') {
				return $product_title;
			}
			
			$attributes = $item_data->get_variation_attributes();		
			if ( ! $attributes ) {
				return $product_title;
			}

			$cart_item_meta = $woocommerce->session->get(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key);
			
			if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID])) {

				$booking_id = $cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID];			
				$booking_entry = $bookyourtravel_accommodation_helper->get_accommodation_booking($booking_id);
				
				if ($booking_entry != null) {
					

					$accommodation_id = $booking_entry->accommodation_id;
					$accommodation_id = BookYourTravel_Theme_Utils::get_current_language_post_id($accommodation_id, 'accommodation');
					$accommodation_obj = new BookYourTravel_Accommodation($accommodation_id);
					$room_type_obj = null;
					$room_type_id = $booking_entry->room_type_id;
					$room_type_id = BookYourTravel_Theme_Utils::get_current_language_post_id($room_type_id, 'room_type');
					if ($room_type_id > 0) {
						$room_type_obj = new BookYourTravel_Room_Type($room_type_id);
					}
					$adults = $booking_entry->adults;
					$children = $booking_entry->children;
					$room_count = $booking_entry->room_count;
					$date_from = $booking_entry->date_from;
					$date_from = date($this->date_format, strtotime($date_from));
					$date_to = $booking_entry->date_to;
					$date_to = date($this->date_format, strtotime($date_to));
					
					$extra_items_string = '';				
					$extra_items_array = array();
					if (!empty($booking_entry->extra_items)) {
						$extra_items_array = unserialize($booking_entry->extra_items);
					}
					
					if (is_array($extra_items_array)) {
						foreach ($extra_items_array as $extra_item_id => $extra_item_quantity) {
							$extra_item_obj = new BookYourTravel_Extra_Item($extra_item_id);
							$extra_items_string .= $extra_item_quantity . ' x ' . $extra_item_obj->get_title() . ', ';
						}
					}
				
					$extra_items_string = trim(rtrim($extra_items_string, ', '));
					
					$product_title = '';
					$product_title .= sprintf(esc_html__('%s', 'bookyourtravel'), $accommodation_obj->get_title()) . '<br />';
					if ($room_type_obj) {
						$product_title .= sprintf(esc_html__('%s', 'bookyourtravel'), $room_type_obj->get_title()) . '<br />';
					}
					$product_title .= sprintf(esc_html__('Dates: %s to %s', 'bookyourtravel'), $date_from, $date_to) . '<br />';
					$product_title .= sprintf(esc_html__('People: %d adults, %d children', 'bookyourtravel'), $adults, $children) . '<br />';
					
					if (!empty($extra_items_string)) {
						$product_title .= esc_html__('Extras: ', 'bookyourtravel') . $extra_items_string;
					}
				}
			} else if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID])) {

				$booking_id = $cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID];			
				$booking_entry = $bookyourtravel_tour_helper->get_tour_booking($booking_id);
				
				if ($booking_entry != null) {
					
					$tour_schedule = $bookyourtravel_tour_helper->get_tour_schedule($booking_entry->tour_schedule_id);
					$booking_entry->tour_id = $tour_schedule->tour_id;
					$tour_id = $booking_entry->tour_id;
					$tour_id = BookYourTravel_Theme_Utils::get_current_language_post_id($tour_id, 'tour');					
					$tour_obj = new BookYourTravel_Tour($tour_id);
					$adults = $booking_entry->adults;
					$children = $booking_entry->children;
					$tour_date = $booking_entry->tour_date;
					$tour_date = date($this->date_format, strtotime($tour_date));
					
					$extra_items_string = '';				
					$extra_items_array = array();
					if (!empty($booking_entry->extra_items)) {
						$extra_items_array = unserialize($booking_entry->extra_items);
					}
					
					if (is_array($extra_items_array)) {
						foreach ($extra_items_array as $extra_item_id => $extra_item_quantity) {
							$extra_item_obj = new BookYourTravel_Extra_Item($extra_item_id);
							$extra_items_string .= $extra_item_quantity . ' x ' . $extra_item_obj->get_title() . ', ';
						}
					}
				
					$extra_items_string = trim(rtrim($extra_items_string, ', '));
					
					$product_title = '';
					$product_title .= sprintf(esc_html__('%s', 'bookyourtravel'), $tour_obj->get_title()) . '<br />';
					$product_title .= sprintf(esc_html__('Tour date: %s', 'bookyourtravel'), $tour_date) . '<br />';
					$product_title .= sprintf(esc_html__('People: %d adults, %d children', 'bookyourtravel'), $adults, $children) . '<br />';
					
					if (!empty($extra_items_string)) {
						$product_title .= esc_html__('Extras: ', 'bookyourtravel') . $extra_items_string;
					}
				}
			} else if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID])) {

				$booking_id = $cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID];			
				$booking_entry = $bookyourtravel_cruise_helper->get_cruise_booking($booking_id);
				
				if ($booking_entry != null) {
					
					$cruise_schedule = $bookyourtravel_cruise_helper->get_cruise_schedule($booking_entry->cruise_schedule_id);
					$booking_entry->cruise_id = $cruise_schedule->cruise_id;
					$booking_entry->cabin_type_id = $cruise_schedule->cabin_type_id;
					
					$cruise_id = $booking_entry->cruise_id;
					$cruise_id = BookYourTravel_Theme_Utils::get_current_language_post_id($cruise_id, 'cruise');					
					$cruise_obj = new BookYourTravel_Cruise($cruise_id);
					$cabin_type_obj = null;
					$cabin_type_id = $booking_entry->cabin_type_id;
					$cabin_type_id = BookYourTravel_Theme_Utils::get_current_language_post_id($cabin_type_id, 'cabin_type');					
					if ($cabin_type_id > 0) {
						$cabin_type_obj = new BookYourTravel_Cabin_Type($cabin_type_id);
					}
					$adults = $booking_entry->adults;
					$children = $booking_entry->children;
					$cruise_date = $booking_entry->cruise_date;
					$cruise_date = date($this->date_format, strtotime($cruise_date));
					
					$extra_items_string = '';				
					$extra_items_array = array();
					if (!empty($booking_entry->extra_items)) {
						$extra_items_array = unserialize($booking_entry->extra_items);
					}
					
					if (is_array($extra_items_array)) {
						foreach ($extra_items_array as $extra_item_id => $extra_item_quantity) {
							$extra_item_obj = new BookYourTravel_Extra_Item($extra_item_id);
							$extra_items_string .= $extra_item_quantity . ' x ' . $extra_item_obj->get_title() . ', ';
						}
					}
				
					$extra_items_string = trim(rtrim($extra_items_string, ', '));
					
					$product_title = '';
					$product_title .= sprintf(esc_html__('%s', 'bookyourtravel'), $cruise_obj->get_title()) . '<br />';
					if ($cabin_type_obj) {
						$product_title .= sprintf(esc_html__('%s', 'bookyourtravel'), $cabin_type_obj->get_title()) . '<br />';
					}
					$product_title .= sprintf(esc_html__('Cruise date: %s', 'bookyourtravel'), $cruise_date) . '<br />';
					$product_title .= sprintf(esc_html__('People: %d adults, %d children', 'bookyourtravel'), $adults, $children) . '<br />';
					
					if (!empty($extra_items_string)) {
						$product_title .= esc_html__('Extras: ', 'bookyourtravel') . $extra_items_string;
					}
				}
			} else if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID])) {

				$booking_id = $cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID];			
				$booking_entry = $bookyourtravel_car_rental_helper->get_car_rental_booking($booking_id);
				
				if ($booking_entry != null) {
					
					$car_rental_id = $booking_entry->car_rental_id;
					$car_rental_id = BookYourTravel_Theme_Utils::get_current_language_post_id($car_rental_id, 'car_rental');					
					$car_rental_obj = new BookYourTravel_Car_Rental($car_rental_id);

					$start_date = $booking_entry->from_day;
					$start_date = date($this->date_format, strtotime($start_date));
					$end_date = $booking_entry->to_day;
					$end_date = date($this->date_format, strtotime($end_date));
					
					$extra_items_string = '';				
					$extra_items_array = array();
					if (!empty($booking_entry->extra_items)) {
						$extra_items_array = unserialize($booking_entry->extra_items);
					}
					
					if (is_array($extra_items_array)) {
						foreach ($extra_items_array as $extra_item_id => $extra_item_quantity) {
							$extra_item_obj = new BookYourTravel_Extra_Item($extra_item_id);
							$extra_items_string .= $extra_item_quantity . ' x ' . $extra_item_obj->get_title() . ', ';
						}
					}
				
					$extra_items_string = trim(rtrim($extra_items_string, ', '));
					
					$product_title = '';
					$product_title .= sprintf(esc_html__('%s', 'bookyourtravel'), $car_rental_obj->get_title()) . '<br />';
					$product_title .= sprintf(esc_html__('From date: %s', 'bookyourtravel'), $start_date) . '<br />';
					$product_title .= sprintf(esc_html__('To date: %s', 'bookyourtravel'), $end_date) . '<br />';
					$product_title .= sprintf(esc_html__('Pick up: %s', 'bookyourtravel'), $booking_entry->pick_up_title) . '<br />';
					$product_title .= sprintf(esc_html__('Drop off: %s', 'bookyourtravel'), $booking_entry->drop_off_title) . '<br />';
					
					if (!empty($extra_items_string)) {
						$product_title .= esc_html__('Extras: ', 'bookyourtravel') . $extra_items_string;
					}
				}
			}
			
			return $product_title;
		}
	}
	
	public function accommodation_booking_add_to_cart_ajax_request() {
	
		if ( isset($_REQUEST) ) {

			$nonce = $_REQUEST['nonce'];
			
			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {
					
				global $woocommerce, $bookyourtravel_accommodation_helper;
				
				if ($this->use_woocommerce_for_checkout) {
					
					$current_user = wp_get_current_user();
					
					$booking_request_object 	= $bookyourtravel_accommodation_helper->retrieve_booking_values_from_request();
					$accommodation_booking_id 	= $bookyourtravel_accommodation_helper->create_accommodation_booking($current_user->ID, $booking_request_object);
					
					$product_id 				= $this->get_product_id('accommodation');
					$variation_id 				= $this->get_accommodations_product_variation_id($product_id, $booking_request_object->accommodation_id, $booking_request_object->room_type_id);
					
					if ($product_id > 0 && $variation_id > 0) {
						
						// $cart_item_data 		= array('booking_id' => $accommodation_booking_id);
						$cart_item_key 			= $woocommerce->cart->add_to_cart( $product_id, 1, $variation_id, null, null); // $cart_item_data);
						
						if (!is_user_logged_in()) {
							$woocommerce->session->set_customer_session_cookie(true);
						}
						$woocommerce->session->set(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key, array(BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID => $accommodation_booking_id));
						
						$cart_item_meta = $woocommerce->session->get(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key);
						
						echo $accommodation_booking_id;
					}			
					
				} else  {
					echo -2;
				}
			} else  {
				echo -3;
			}
		}
		
		die();
	}	

	public function cruise_booking_add_to_cart_ajax_request() {
	
		if ( isset($_REQUEST) ) {

			$nonce = $_REQUEST['nonce'];
			
			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {
					
				global $woocommerce, $bookyourtravel_cruise_helper;
				
				if ($this->use_woocommerce_for_checkout) {
					
					$current_user = wp_get_current_user();
					
					$booking_request_object 	= $bookyourtravel_cruise_helper->retrieve_booking_values_from_request();
					
					$cruise_booking_id 	= $bookyourtravel_cruise_helper->create_cruise_booking($current_user->ID, $booking_request_object);
					
					$cruise_schedule = $bookyourtravel_cruise_helper->get_cruise_schedule($booking_request_object->cruise_schedule_id);
					$booking_request_object->cruise_id = $cruise_schedule->cruise_id;
					$booking_request_object->cabin_type_id = $cruise_schedule->cabin_type_id;

					$product_id 				= $this->get_product_id('cruise');
					$variation_id 				= $this->get_cruises_product_variation_id($product_id, $booking_request_object->cruise_id, $booking_request_object->cabin_type_id);
					
					if ($product_id > 0 && $variation_id > 0) {
						
						// $cart_item_data 		= array('booking_id' => $cruise_booking_id);
						$cart_item_key 			= $woocommerce->cart->add_to_cart( $product_id, 1, $variation_id, null, null); // $cart_item_data);
						
						if (!is_user_logged_in()) {
							$woocommerce->session->set_customer_session_cookie(true);
						}
						$woocommerce->session->set(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key, array(BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID => $cruise_booking_id));
						
						$cart_item_meta = $woocommerce->session->get(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key);
						
						echo $cruise_booking_id;
					}			
					
				} else  {
					echo -2;
				}
			} else  {
				echo -3;
			}
		}
		
		die();
	}

	public function tour_booking_add_to_cart_ajax_request() {
	
		if ( isset($_REQUEST) ) {

			$nonce = $_REQUEST['nonce'];
			
			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {
					
				global $woocommerce, $bookyourtravel_tour_helper;
				
				if ($this->use_woocommerce_for_checkout) {
					
					$current_user = wp_get_current_user();
					
					$booking_request_object 	= $bookyourtravel_tour_helper->retrieve_booking_values_from_request();
					
					$tour_booking_id 	= $bookyourtravel_tour_helper->create_tour_booking($current_user->ID, $booking_request_object);
					$tour_schedule = $bookyourtravel_tour_helper->get_tour_schedule($booking_request_object->tour_schedule_id);

					$booking_request_object->tour_id = $tour_schedule->tour_id;

					$product_id 				= $this->get_product_id('tour');
					$variation_id 				= $this->get_tours_product_variation_id($product_id, $booking_request_object->tour_id);
					
					if ($product_id > 0 && $variation_id > 0) {
						
						// $cart_item_data 		= array('booking_id' => $tour_booking_id);
						$cart_item_key 			= $woocommerce->cart->add_to_cart( $product_id, 1, $variation_id, null, null); // $cart_item_data);
						
						if (!is_user_logged_in()) {
							$woocommerce->session->set_customer_session_cookie(true);
						}
						$woocommerce->session->set(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key, array(BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID => $tour_booking_id));
						
						$cart_item_meta = $woocommerce->session->get(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key);
						
						echo $tour_booking_id;
					}			
					
				} else  {
					echo -2;
				}
			} else  {
				echo -3;
			}
		}
		
		die();
	}
	
	public function car_rental_booking_add_to_cart_ajax_request() {
	
		if ( isset($_REQUEST) ) {

			$nonce = $_REQUEST['nonce'];
			
			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {
					
				global $woocommerce, $bookyourtravel_car_rental_helper;
				
				if ($this->use_woocommerce_for_checkout) {
					
					$current_user = wp_get_current_user();
					
					$booking_request_object 	= $bookyourtravel_car_rental_helper->retrieve_booking_values_from_request();
					
					$car_rental_booking_id 		= $bookyourtravel_car_rental_helper->create_car_rental_booking($current_user->ID, $booking_request_object);

					$product_id 				= $this->get_product_id('car_rental');
					$variation_id 				= $this->get_car_rentals_product_variation_id($product_id, $booking_request_object->car_rental_id);
					
					if ($product_id > 0 && $variation_id > 0) {
						
						// $cart_item_data 		= array('booking_id' => $car_rental_booking_id);
						$cart_item_key 			= $woocommerce->cart->add_to_cart( $product_id, 1, $variation_id, null, null); // $cart_item_data);
						
						if (!is_user_logged_in()) {
							$woocommerce->session->set_customer_session_cookie(true);
						}
						$woocommerce->session->set(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key, array(BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID => $car_rental_booking_id));
						
						$cart_item_meta = $woocommerce->session->get(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key);
						
						echo $car_rental_booking_id;
					}			
					
				} else  {
					echo -2;
				}
			} else  {
				echo -3;
			}
		}
		
		die();
	}
	
	function get_product_slug($post_type) {
		
		$slug = '';
		
		switch ($post_type) {
			case 'accommodation' : $slug = $this->accommodation_product_slug;break;
			case 'cruise' 		 : $slug = $this->cruise_product_slug;break;
			case 'tour' 		 : $slug = $this->tour_product_slug;break;
			case 'car_rental' 	 : $slug = $this->car_rental_product_slug;break;
			default 			 : $slug = $this->accommodation_product_slug;break;
		}
		
		return $slug;
	}
	
	function get_product_id($post_type) {
		
		global $wpdb;
		
		$sql = "SELECT Id FROM $wpdb->posts WHERE post_type='product' AND post_name = '%s' AND post_status='publish' LIMIT 1";
		
		$id = $wpdb->get_var($wpdb->prepare($sql, $this->get_product_slug($post_type)));
		
		$product_id = intval($id);
		
		if (!isset($product_id) || empty($product_id)) {
			if ($post_type == 'accommodation') {
				$product_id 			= $this->create_accommodations_product();
			} else if ($post_type == 'tour') {
				$product_id 			= $this->create_tours_product();
			} else if ($post_type == 'cruise') {
				$product_id 			= $this->create_cruises_product();
			} else if ($post_type == 'car_rental') {
				$product_id 			= $this->create_car_rentals_product();
			}
		}

		if (BookYourTravel_Theme_Utils::is_wpml_active() ) {
		
			$translated_product_id = BookYourTravel_Theme_Utils::get_current_language_post_id($product_id, 'product', false);
			
			if (!isset($translated_product_id)) {
				// no translation exists yet... so create one
				icl_makes_duplicates_public($product_id);
				$translated_product_id = BookYourTravel_Theme_Utils::get_current_language_post_id($product_id, 'product');
			}
			
			$product_id = $translated_product_id;
			
			global $sitepress;
			if ($sitepress) {
				$active_languages = $sitepress->get_active_languages();
				foreach ($active_languages as $language => $details) {
					$p_id = BookYourTravel_Theme_Utils::get_language_post_id($product_id, 'product', $language, false);
					if (!isset($p_id)) {
						icl_makes_duplicates_public($product_id);						
					}
				}
			}			
		}
		
		return $product_id;
	}

		
	function get_accommodations_product_variation_id($product_id, $accommodation_id, $room_type_id = 0) {
		
		global $wpdb;
		
		$product_variation_name = $this->build_accommodation_product_variation_slug($accommodation_id, $room_type_id);

		$sql = "SELECT ID FROM $wpdb->posts WHERE post_type='product_variation' AND post_parent = %d AND post_name = '%s' AND post_status='publish' LIMIT 1";
		
		$sql = $wpdb->prepare($sql, $product_id, $product_variation_name);

		$variation_id = $wpdb->get_var($sql);
		if (!isset($variation_id) || empty($variation_id)) {
			$variation_id 			= $this->create_accommodation_product_variation($product_id, $accommodation_id, $room_type_id);
		}
		
		if (BookYourTravel_Theme_Utils::is_wpml_active() ) {
			
			$translated_variation_id = BookYourTravel_Theme_Utils::get_current_language_post_id($variation_id, 'product_variation', false);
			
			if (!isset($translated_variation_id)) {
				// no translation exists yet... so create one
				icl_makes_duplicates_public($variation_id);
				$translated_variation_id = BookYourTravel_Theme_Utils::get_current_language_post_id($variation_id, 'product_variation');
			}
			
			$variation_id = $translated_variation_id;
		}
		
		return $variation_id;
	}
	
	function get_tours_product_variation_id($product_id, $tour_id) {
		
		global $wpdb;
		
		$product_name = $this->build_tour_product_variation_slug($tour_title);

		$sql = "SELECT ID FROM $wpdb->posts WHERE post_type='product_variation' AND post_parent = %d AND post_name = '%s' AND post_status='publish' LIMIT 1";
		
		$sql = $wpdb->prepare($sql, $product_id, $product_name);

		$variation_id = $wpdb->get_var($sql);
		if (!isset($variation_id) || empty($variation_id)) {
			$variation_id 			= $this->create_tour_product_variation($product_id, $tour_id);
		}
		
		if (BookYourTravel_Theme_Utils::is_wpml_active() ) {
			
			$translated_variation_id = BookYourTravel_Theme_Utils::get_current_language_post_id($variation_id, 'product_variation', false);
			
			if (!isset($translated_variation_id)) {
				// no translation exists yet... so create one
				icl_makes_duplicates_public($variation_id);
				$translated_variation_id = BookYourTravel_Theme_Utils::get_current_language_post_id($variation_id, 'product_variation');
			}
			
			$variation_id = $translated_variation_id;
		}
		
		return $variation_id;
	}
	
	function get_cruises_product_variation_id($product_id, $cruise_id, $cabin_type_id = 0) {
		
		global $wpdb;
		
		$product_name = $this->build_cruise_product_variation_slug($cruise_id, $cabin_type_id);

		$sql = "SELECT ID FROM $wpdb->posts WHERE post_type='product_variation' AND post_parent = %d AND post_name = '%s' AND post_status='publish' LIMIT 1";
		
		$sql = $wpdb->prepare($sql, $product_id, $product_name);

		$variation_id = $wpdb->get_var($sql);
		if (!isset($variation_id) || empty($variation_id)) {
			$variation_id 			= $this->create_cruise_product_variation($product_id, $cruise_id, $cabin_type_id);
		}
		
		if (BookYourTravel_Theme_Utils::is_wpml_active() ) {
			
			$translated_variation_id = BookYourTravel_Theme_Utils::get_current_language_post_id($variation_id, 'product_variation', false);
			
			if (!isset($translated_variation_id)) {
				// no translation exists yet... so create one
				icl_makes_duplicates_public($variation_id);
				$translated_variation_id = BookYourTravel_Theme_Utils::get_current_language_post_id($variation_id, 'product_variation');
			}
			
			$variation_id = $translated_variation_id;
		}
		
		return $variation_id;
	}
	
	function get_car_rentals_product_variation_id($product_id, $car_rental_id) {
		
		global $wpdb;
		
		$product_name = $this->build_car_rental_product_variation_slug($car_rental_id);

		$sql = "SELECT ID FROM $wpdb->posts WHERE post_type='product_variation' AND post_parent = %d AND post_name = '%s' AND post_status='publish' LIMIT 1";
		
		$sql = $wpdb->prepare($sql, $product_id, $product_name);

		$variation_id = $wpdb->get_var($sql);
		if (!isset($variation_id) || empty($variation_id)) {
			$variation_id 			= $this->create_car_rental_product_variation($product_id, $car_rental_id);
		}
		
		if (BookYourTravel_Theme_Utils::is_wpml_active() ) {
			
			$translated_variation_id = BookYourTravel_Theme_Utils::get_current_language_post_id($variation_id, 'product_variation', false);
			
			if (!isset($translated_variation_id)) {
				// no translation exists yet... so create one
				icl_makes_duplicates_public($variation_id);
				$translated_variation_id = BookYourTravel_Theme_Utils::get_current_language_post_id($variation_id, 'product_variation');
			}
			
			$variation_id = $translated_variation_id;
		}
		
		return $variation_id;		
	}
		
	function create_accommodations_product() {
		
		$new_post = array(
			'post_title' 		=> esc_html__('BookYourTravel Accommodations Product', 'bookyourtravel'),
			'post_content' 		=> esc_html__('This is a variable product used for bookyourtravel theme accommodation bookings processed with WooCommerce', 'bookyourtravel'),
			'post_status' 		=> 'publish',
			'post_name' 		=> $this->accommodation_product_slug,
			'post_type' 		=> 'product',
			'comment_status' 	=> 'closed'
		);

		$product_id 			= wp_insert_post($new_post);
		$skuu 					= $this->random_sku('bookyourtravel_accommodation_booking_', 6);
		
		update_post_meta($product_id, '_sku', 				$skuu );
		
		wp_set_object_terms($product_id, 'variable', 		'product_type');
		
		$product_attributes = array(
			BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_ATT => array(
				'name'			=> BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_ATT,
				'value'			=> '',
				'is_visible' 	=> '1',
				'is_variation' 	=> '1',
				'is_taxonomy' 	=> '0'
			),
			BOOKYOURTRAVEL_WOOCOMMERCE_ROOM_TYPE_ATT => array(
				'name'			=> BOOKYOURTRAVEL_WOOCOMMERCE_ROOM_TYPE_ATT,
				'value'			=> '',
				'is_visible' 	=> '1',
				'is_variation' 	=> '1',
				'is_taxonomy' 	=> '0'
			),
		);
		
		update_post_meta( $product_id, '_product_attributes', $product_attributes);
		
		return $product_id;
	}	
	
	function create_tours_product() {
		
		$new_post = array(
			'post_title' 		=> esc_html__('BookYourTravel Tours Product', 'bookyourtravel'),
			'post_content' 		=> esc_html__('This is a variable product used for bookyourtravel theme tour bookings processed with WooCommerce', 'bookyourtravel'),
			'post_status' 		=> 'publish',
			'post_name' 		=> $this->tour_product_slug,
			'post_type' 		=> 'product',
			'comment_status' 	=> 'closed'
		);

		$product_id 			= wp_insert_post($new_post);
		$skuu 					= $this->random_sku('bookyourtravel_tour_booking_', 6);
		
		update_post_meta($product_id, '_sku', 				$skuu );
		
		wp_set_object_terms($product_id, 'variable', 		'product_type');
		
		$product_attributes = array(
			BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_ATT => array(
				'name'			=> BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_ATT,
				'value'			=> '',
				'is_visible' 	=> '1',
				'is_variation' 	=> '1',
				'is_taxonomy' 	=> '0'
			),
		);
		
		update_post_meta( $product_id, '_product_attributes', $product_attributes);
		
		return $product_id;
	}
	
	function create_cruises_product() {
		
		$new_post = array(
			'post_title' 		=> esc_html__('BookYourTravel Cruises Product', 'bookyourtravel'),
			'post_content' 		=> esc_html__('This is a variable product used for bookyourtravel theme cruise bookings processed with WooCommerce', 'bookyourtravel'),
			'post_status' 		=> 'publish',
			'post_name' 		=> $this->cruise_product_slug,
			'post_type' 		=> 'product',
			'comment_status' 	=> 'closed'
		);

		$product_id 			= wp_insert_post($new_post);
		$skuu 					= $this->random_sku('bookyourtravel_cruise_booking_', 6);
		
		update_post_meta($product_id, '_sku', 				$skuu );
		
		wp_set_object_terms($product_id, 'variable', 		'product_type');
		
		$product_attributes = array(
			BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_ATT => array(
				'name'			=> BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_ATT,
				'value'			=> '',
				'is_visible' 	=> '1',
				'is_variation' 	=> '1',
				'is_taxonomy' 	=> '0'
			),
			BOOKYOURTRAVEL_WOOCOMMERCE_CABIN_TYPE_ATT => array(
				'name'			=> BOOKYOURTRAVEL_WOOCOMMERCE_CABIN_TYPE_ATT,
				'value'			=> '',
				'is_visible' 	=> '1',
				'is_variation' 	=> '1',
				'is_taxonomy' 	=> '0'
			),
		);
		
		update_post_meta( $product_id, '_product_attributes', $product_attributes);
		
		return $product_id;
	}
	
	function create_car_rentals_product() {
		
		$new_post = array(
			'post_title' 		=> esc_html__('BookYourTravel Car Rentals Product', 'bookyourtravel'),
			'post_content' 		=> esc_html__('This is a variable product used for bookyourtravel theme car rental bookings processed with WooCommerce', 'bookyourtravel'),
			'post_status' 		=> 'publish',
			'post_name' 		=> $this->car_rental_product_slug,
			'post_type' 		=> 'product',
			'comment_status' 	=> 'closed'
		);

		$product_id 			= wp_insert_post($new_post);
		$skuu 					= $this->random_sku('bookyourtravel_car_rental_booking_', 6);
		
		update_post_meta($product_id, '_sku', 				$skuu );
		
		wp_set_object_terms($product_id, 'variable', 		'product_type');
		
		$product_attributes = array(
			BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_ATT => array(
				'name'			=> BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_ATT,
				'value'			=> '',
				'is_visible' 	=> '1',
				'is_variation' 	=> '1',
				'is_taxonomy' 	=> '0'
			),
		);
		
		update_post_meta( $product_id, '_product_attributes', $product_attributes);
		
		return $product_id;
	}
	
	function build_accommodation_product_variation_title($accommodation_id, $room_type_id = 0) {
	
		$cl_accommodation_id = BookYourTravel_Theme_Utils::get_current_language_post_id($accommodation_id, 'accommodation');
		$cl_room_type_id = BookYourTravel_Theme_Utils::get_current_language_post_id($room_type_id, 'room_type');
				
		$accommodation_title = get_the_title($cl_accommodation_id);
		$room_type_title = get_the_title($cl_room_type_id);
	
		$variation_title = sprintf(__('Accommodation %s', 'bookyourtravel'), $accommodation_title);
		if (!empty($room_type_title)) {
			$variation_title .= sprintf(__(' (%s) ', 'bookyourtravel'), $room_type_title);
		}
		$variation_title .= __('booking', 'bookyourtravel');	
	
		return $variation_title;
	}
	
	function build_tour_product_variation_title($tour_id) {
	
		$cl_tour_id = BookYourTravel_Theme_Utils::get_current_language_post_id($tour_id, 'tour');
		$tour_title = get_the_title($cl_tour_id);	
	
		$variation_title = sprintf(__('Tour %s ', 'bookyourtravel'), $tour_title);
		$variation_title .= __('booking', 'bookyourtravel');	
	
		return $variation_title;
	}
	
	function build_cruise_product_variation_title($cruise_id, $cabin_type_id = 0) {
	
		$cl_cruise_id = BookYourTravel_Theme_Utils::get_current_language_post_id($cruise_id, 'cruise');
		$cl_cabin_type_id = BookYourTravel_Theme_Utils::get_current_language_post_id($cabin_type_id, 'cabin_type');
				
		$cruise_title = get_the_title($cl_cruise_id);
		$cabin_type_title = get_the_title($cl_cabin_type_id);	
	
		$variation_title = sprintf(__('Cruise %s', 'bookyourtravel'), $cruise_title);
		if (!empty($room_type_title)) {
			$variation_title .= sprintf(__(' (%s) ', 'bookyourtravel'), $cabin_type_title);
		}
		$variation_title .= __('booking', 'bookyourtravel');	
	
		return $variation_title;
	}
	
	function build_car_rental_product_variation_title($car_rental_id) {

		$cl_car_rental_id = BookYourTravel_Theme_Utils::get_current_language_post_id($car_rental_id, 'car_rental');
		$car_rental_title = get_the_title($cl_car_rental_id);	
	
		$variation_title = sprintf(__('Car rental %s ', 'bookyourtravel'), $car_rental_title);
		$variation_title .= __('booking', 'bookyourtravel');	
	
		return $variation_title;
	}
	
	function create_accommodation_product_variation($product_id, $accommodation_id, $room_type_id = 0) {
				
		$variation_title = $this->build_accommodation_product_variation_title($accommodation_id, $room_type_id);
		
		$new_post = array(
			'post_title' 		=> $variation_title,
			'post_content' 		=> __('This is a bookyourtravel accommodation product variation', 'bookyourtravel'),
			'post_status' 		=> 'publish',
			'post_type' 		=> 'product_variation',
			'post_parent'		=> $product_id,
			'post_name' 		=> $this->build_accommodation_product_variation_slug($accommodation_id, $room_type_id),
			'comment_status' 	=> 'closed'
		);

		$variation_id 			= wp_insert_post($new_post);
		
		update_post_meta($variation_id, '_stock_status', 		'instock');
		update_post_meta($variation_id, '_sold_individually', 	'yes');
		update_post_meta($variation_id, '_virtual', 			'yes');
		update_post_meta($variation_id, '_manage_stock', 'no' );
		update_post_meta($variation_id, '_downloadable', 'no' );
		update_post_meta($variation_id, 'attribute_' . BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_ATT, $accommodation_id);
		update_post_meta($variation_id, 'attribute_' . BOOKYOURTRAVEL_WOOCOMMERCE_ROOM_TYPE_ATT, $room_type_id);
		
		return $variation_id;
	}
	
	function create_tour_product_variation($product_id, $tour_id) {
		
		$variation_title = $this->build_tour_product_variation_title($tour_id);
		
		$new_post = array(
			'post_title' 		=> $variation_title,
			'post_content' 		=> __('This is a bookyourtravel tour product variation', 'bookyourtravel'),
			'post_status' 		=> 'publish',
			'post_type' 		=> 'product_variation',
			'post_parent'		=> $product_id,
			'post_name' 		=> $this->build_tour_product_variation_slug($tour_id),
			'comment_status' 	=> 'closed'
		);

		$variation_id 			= wp_insert_post($new_post);
		
		update_post_meta($variation_id, '_stock_status', 		'instock');
		update_post_meta($variation_id, '_sold_individually', 	'yes');
		update_post_meta($variation_id, '_virtual', 			'yes');
		update_post_meta($variation_id, '_manage_stock', 'no' );
		update_post_meta($variation_id, '_downloadable', 'no' );
		update_post_meta($variation_id, 'attribute_' . BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_ATT, $tour_id);
		
		return $variation_id;
	}
	
	function create_cruise_product_variation($product_id, $cruise_id, $cabin_type_id = 0) {
		
		$variation_title = $this->build_cruise_product_variation_title($cruise_id, $cabin_type_id);
		
		$new_post = array(
			'post_title' 		=> $variation_title,
			'post_content' 		=> __('This is a bookyourtravel cruise product variation', 'bookyourtravel'),
			'post_status' 		=> 'publish',
			'post_type' 		=> 'product_variation',
			'post_parent'		=> $product_id,
			'post_name' 		=> $this->build_cruise_product_variation_slug($cruise_id, $cabin_type_id),
			'comment_status' 	=> 'closed'
		);

		$variation_id 			= wp_insert_post($new_post);
		
		update_post_meta($variation_id, '_stock_status', 		'instock');
		update_post_meta($variation_id, '_sold_individually', 	'yes');
		update_post_meta($variation_id, '_virtual', 			'yes');
		update_post_meta($variation_id, '_manage_stock', 'no' );
		update_post_meta($variation_id, '_downloadable', 'no' );
		update_post_meta($variation_id, 'attribute_' . BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_ATT, $cruise_id);
		update_post_meta($variation_id, 'attribute_' . BOOKYOURTRAVEL_WOOCOMMERCE_CABIN_TYPE_ATT, $cabin_type_id);
		
		return $variation_id;
	}
	
	function create_car_rental_product_variation($product_id, $car_rental_id) {
		
		$variation_title = $this->build_car_rental_product_variation_title($car_rental_id);
		
		$new_post = array(
			'post_title' 		=> $variation_title,
			'post_content' 		=> __('This is a bookyourtravel car rental product variation', 'bookyourtravel'),
			'post_status' 		=> 'publish',
			'post_type' 		=> 'product_variation',
			'post_parent'		=> $product_id,
			'post_name' 		=> $this->build_car_rental_product_variation_slug($car_rental_id),
			'comment_status' 	=> 'closed'
		);

		$variation_id 			= wp_insert_post($new_post);
		
		update_post_meta($variation_id, '_stock_status', 		'instock');
		update_post_meta($variation_id, '_sold_individually', 	'yes');
		update_post_meta($variation_id, '_virtual', 			'yes');
		update_post_meta($variation_id, '_manage_stock', 'no' );
		update_post_meta($variation_id, '_downloadable', 'no' );
		update_post_meta($variation_id, 'attribute_' . BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_ATT, $car_rental_id);
		
		return $variation_id;
	}
	
	function post_class($classes) {
	
		if (in_array('product', $classes) && !is_single()) {

			if ($this->page_sidebar_positioning == 'both')
				$classes[] = 'one-half';
			else if ($this->page_sidebar_positioning == 'left' || $this->page_sidebar_positioning == 'right') 
				$classes[] = 'one-third';
			else
				$classes[] = 'one-fourth';			
		}
	
		return $classes;
	}
	
	function loop_shop_columns() {
	
		if ($this->page_sidebar_positioning == 'both')
			return 2;
		else if ($this->page_sidebar_positioning == 'left' || $this->page_sidebar_positioning == 'right') 
			return 3;
		return 4; // 4 products per row
	}
	
	function before_main_content() {

		$section_class = 'full-width';
		
		if ($this->page_sidebar_positioning == 'both')
			$section_class = 'one-half';
		else if ($this->page_sidebar_positioning == 'left' || $this->page_sidebar_positioning == 'right') 
			$section_class = 'three-fourth';
			
		?>
		<!--row-->
		<div class="row">
		<?php			
		if ($this->page_sidebar_positioning == 'both' || $this->page_sidebar_positioning == 'left') {
			get_sidebar('left');
		}
		?>		
			<section class="content <?php echo esc_attr($section_class); ?>">
		<?php
	}
	
	function after_main_content() {
	
		$section_class = 'full-width';
		
		if ($this->page_sidebar_positioning == 'both')
			$section_class = 'one-half';
		else if ($this->page_sidebar_positioning == 'left' || $this->page_sidebar_positioning == 'right') 
			$section_class = 'three-fourth';
			
		?>
			</section>
		<?php			
		if ($this->page_sidebar_positioning == 'both' || $this->page_sidebar_positioning == 'right') {
			get_sidebar('right');	
		} 
		?>
		</div><!--wrap-->
		<?php
	}
	
	function customized_breadcrumbs() {
	
		if (function_exists('woocommerce_breadcrumb')) {
		
			$args = array(
					'delimiter' => '',
					'before' => '<li>',
					'after' => '</li>',
					'wrap_before' => '<nav role="navigation" class="breadcrumbs" itemprop="breadcrumb"><ul>',
					'wrap_after' => '</ul></nav>',
			);
			
			woocommerce_breadcrumb($args);
		}
	}
	
	function random_sku($prefix, $len = 6) {
	
		$str = '';
		
		for ($i = 0; $i < $len; $i++) {
			$str .= substr('0123456789', mt_rand(0, strlen('0123456789') - 1), 1);
		}
		
		return $prefix . $str; 
	}
	
	function build_accommodation_product_variation_slug($accommodation_id, $room_type_id = 0) {
	
		$slug = sprintf($this->accommodation_product_slug . "-v-%d", $accommodation_id);
		
		if ($room_type_id > 0) {
			$slug .= sprintf("-%d", $room_type_id);
		}
		
		return $slug;
	}
	
	function build_tour_product_variation_slug($tour_id) {
	
		$slug = sprintf($this->tour_product_slug . "-v-%d", $tour_id);
		
		return $slug;
	}
	
	function build_cruise_product_variation_slug($cruise_id, $cabin_type_id = 0) {
	
		$slug = sprintf($this->cruise_product_slug . "-v-%d", $cruise_id);
		
		if ($cabin_type_id > 0) {
			$slug .= sprintf("-%d", $cabin_type_id);
		}
		
		return $slug;
	}
	
	function build_car_rental_product_variation_slug($car_rental_id) {
	
		$slug = sprintf($this->car_rental_product_slug . "-v-%d", $car_rental_id);
		
		return $slug;
	}
	
	public function dynamically_create_accommodation_woo_order($booking_id, $total_price, $address_array, $accommodation_id, $room_type_id = 0) {

		$product_id 				= $this->get_product_id('accommodation');
		$variation_id 				= $this->get_accommodations_product_variation_id($product_id, $accommodation_id, $room_type_id);
		
		return $this->create_accommodation_woo_order($variation_id, $booking_id, $total_price, $address_array);
	}
	
	private function create_accommodation_woo_order($variation_id, $booking_id, $total_price, $address_array) {

		global $bookyourtravel_accommodation_helper, $woocommerce;
		
        $order = wc_create_order();
		
		$product_variation = new WC_Product_Variation($variation_id);

		$item_id = $order->add_product($product_variation, 1);
		
		if ($item_id > 0) {
			wc_add_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID, $booking_id, true);
		}
		
        $order->set_address( $address_array, 'billing' );
        $order->calculate_totals();
		$order->set_total($total_price);
		$order->payment_complete();
		$order->update_status( 'completed' );
 
		if ($woocommerce && $woocommerce->cart) {
			$woocommerce->cart->empty_cart();		
		}
		
		$bookyourtravel_accommodation_helper->update_booking_woocommerce_info($booking_id, 'manual add', $order->id, 'completed');
		
		return $order->id;
	}
	
	public function dynamically_create_tour_woo_order($booking_id, $total_price, $address_array, $tour_id) {

		$product_id 				= $this->get_product_id('tour');
		$variation_id 				= $this->get_tours_product_variation_id($product_id, $tour_id);
		
		return $this->create_tour_woo_order($variation_id, $booking_id, $total_price, $address_array);
	}
	
	private function create_tour_woo_order($variation_id, $booking_id, $total_price, $address_array) {

		global $bookyourtravel_tour_helper, $woocommerce;
		
        $order = wc_create_order();
		
		$product_variation = new WC_Product_Variation($variation_id);

		$item_id = $order->add_product($product_variation, 1);
		
		if ($item_id > 0) {
			wc_add_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID, $booking_id, true);
		}
		
        $order->set_address( $address_array, 'billing' );
        $order->calculate_totals();
		$order->set_total($total_price);
		$order->payment_complete();
		$order->update_status( 'completed' );
 
		if ($woocommerce && $woocommerce->cart) {
			$woocommerce->cart->empty_cart();		
		}
		
		$bookyourtravel_tour_helper->update_booking_woocommerce_info($booking_id, 'manual add', $order->id, 'completed');
		
		return $order->id;
	}
	
	public function dynamically_create_cruise_woo_order($booking_id, $total_price, $address_array, $cruise_id, $cabin_type_id = 0) {

		$product_id 				= $this->get_product_id('cruise');
		$variation_id 				= $this->get_cruises_product_variation_id($product_id, $cruise_id, $cabin_type_id);
		
		return $this->create_cruise_woo_order($variation_id, $booking_id, $total_price, $address_array);
	}
	
	private function create_cruise_woo_order($variation_id, $booking_id, $total_price, $address_array) {

		global $bookyourtravel_cruise_helper, $woocommerce;
		
        $order = wc_create_order();
		
		$product_variation = new WC_Product_Variation($variation_id);

		$item_id = $order->add_product($product_variation, 1);
		
		if ($item_id > 0) {
			wc_add_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID, $booking_id, true);
		}
		
        $order->set_address( $address_array, 'billing' );
        $order->calculate_totals();
		$order->set_total($total_price);
		$order->payment_complete();
		$order->update_status( 'completed' );
 
		if ($woocommerce && $woocommerce->cart) {
			$woocommerce->cart->empty_cart();		
		}
		
		$bookyourtravel_cruise_helper->update_booking_woocommerce_info($booking_id, 'manual add', $order->id, 'completed');
		
		return $order->id;
	}

	public function dynamically_create_car_rental_woo_order($booking_id, $total_price, $address_array, $car_rental_id) {

		$product_id 				= $this->get_product_id('car_rental');
		$variation_id 				= $this->get_car_rentals_product_variation_id($product_id, $car_rental_id);

		return $this->create_car_rental_woo_order($variation_id, $booking_id, $total_price, $address_array);
	}
	
	private function create_car_rental_woo_order($variation_id, $booking_id, $total_price, $address_array) {

		global $bookyourtravel_car_rental_helper, $woocommerce;
		
        $order = wc_create_order();
		
		$product_variation = new WC_Product_Variation($variation_id);

		$item_id = $order->add_product($product_variation, 1);
		
		if ($item_id > 0) {
			wc_add_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID, $booking_id, true);
		}
		
        $order->set_address( $address_array, 'billing' );
        $order->calculate_totals();
		$order->set_total($total_price);
		$order->payment_complete();
		$order->update_status( 'completed' );
 
		if ($woocommerce && $woocommerce->cart) {
			$woocommerce->cart->empty_cart();		
		}
		
		$bookyourtravel_car_rental_helper->update_booking_woocommerce_info($booking_id, 'manual add', $order->id, 'completed');
		
		return $order->id;
	}
}

// store the instance in a variable to be retrieved later and call init
$bookyourtravel_theme_woocommerce = BookYourTravel_Theme_WooCommerce::get_instance();
$bookyourtravel_theme_woocommerce->init();;