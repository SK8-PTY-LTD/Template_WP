<?php

/*
*******************************************************************************
************************** LOAD THE BASE CLASS *******************************
*******************************************************************************
* The WP_List_Table class isn't automatically available to plugins, 
* so we need to check if it's available and load it if necessary.
*/
 
if(!class_exists('WP_List_Table')) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class BookYourTravel_Accommodation_Bookings_Admin extends BookYourTravel_BaseSingleton {
	
	private $enable_accommodations;
	private $price_decimal_places;
	private $default_currency_symbol;
	private $show_currency_symbol_after;
	
	protected function __construct() {
	
		global $bookyourtravel_theme_globals;
		
		$this->enable_accommodations = $bookyourtravel_theme_globals->enable_accommodations();
		$this->default_currency_symbol = $bookyourtravel_theme_globals->get_default_currency_symbol();
		$this->show_currency_symbol_after = $bookyourtravel_theme_globals->show_currency_symbol_after();
		
        // our parent class might
        // contain shared code in its constructor
        parent::__construct();
	}

    public function init() {

		if ($this->enable_accommodations) {	

			add_action( 'admin_menu' , array( $this, 'bookings_admin_page' ) );
			add_filter( 'set-screen-option', array( $this, 'bookings_set_screen_options' ), 10, 3);
			add_action( 'admin_head', array( $this, 'bookings_admin_head' ) );
		}
	}
	
	function bookings_admin_page() {

		$hook = add_submenu_page('edit.php?post_type=accommodation', esc_html__('Accommodation Bookings', 'bookyourtravel'), esc_html__('Bookings', 'bookyourtravel'), 'edit_posts', basename(__FILE__), array( $this, 'bookings_admin_display') );
		add_action( "load-$hook", array( $this, 'bookings_add_screen_options') );
	}
	
	function bookings_set_screen_options($status, $option, $value) {
		if ( 'bookings_per_page' == $option ) 
			return $value;
	}
	
	function bookings_admin_head() {
		$page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
		if( 'theme_accommodation_booking_admin.php' != $page )
			return;

		$this->bookings_admin_styles();
	}
	
	function bookings_admin_styles() {
	
		$booking_id = 0;
		if (isset($_GET['booking_id'])) {
			$booking_id = (int)$_GET['booking_id'];
		}
	
		echo '<style type="text/css">';
		echo '.wp-list-table .column-Id { width: 10%; }';
		echo '.wp-list-table .column-Customer { width: 15%; }';
		echo '.wp-list-table .column-AccommodationName { width: 20%; }';
		echo '.wp-list-table .column-RoomType { width: 15%; }';
		echo '.wp-list-table .column-DateFrom { width: 12%; }';
		echo '.wp-list-table .column-DateTo { width: 12%; }';
		echo '.wp-list-table .column-TotalPrice { width: 10%; }';
		echo '</style>';	

		echo '<script>';
		echo 'window.adminAjaxUrl = ' . json_encode(admin_url('admin-ajax.php')) . ';';
		echo 'window.datepickerDateFormat = ' . json_encode(BookYourTravel_Theme_Utils::dateformat_PHP_to_jQueryUI(get_option('date_format'))) . ';';
		echo 'window.datepickerAltFormat = ' . json_encode(BOOKYOURTRAVEL_ALT_DATE_FORMAT) . ';';
		echo 'window.currentDay = ' . json_encode(date_i18n('j')) . ';';
		echo 'window.currentMonth = ' . json_encode(date_i18n('n')) . ';';
		echo 'window.currentYear = ' . json_encode(date_i18n('Y')) . ';';
		echo 'window.currentBookingId = ' . ($booking_id > 0 ? $booking_id : 0) . ';';
		echo 'window.currencySymbol = ' . json_encode($this->default_currency_symbol) . ';';
		echo 'window.currencySymbolShowAfter = ' . json_encode($this->show_currency_symbol_after) . ';';
		echo '</script>';

	}
		
	function bookings_add_screen_options() {
	
		global $wp_accommodation_bookings_table;
		$option = 'per_page';
		$args = array('label' => esc_html__('Bookings', 'bookyourtravel'),'default' => 50,'option' => 'bookings_per_page');
		add_screen_option( $option, $args );
		$wp_accommodation_bookings_table = new Accommodation_Bookings_Admin_List_Table();
	}

	function bookings_admin_display() {
	
		echo '</pre><div class="wrap">';
		echo '<h2>' . esc_html__('Accommodation bookings', 'bookyourtravel') . '</h2>';
		
		global $wp_accommodation_bookings_table;

		$wp_accommodation_bookings_table->handle_form_submit();
		
		$booking_id = 0;
		if (isset($_GET['booking_id'])) {
			$booking_id = (int)$_GET['booking_id'];
		}
	
		global $bookyourtravel_accommodation_helper, $booking_insert_success;

		$accommodation_id = 0;
		
		if (!empty($booking_id)) {
			$booking_object = $bookyourtravel_accommodation_helper->get_accommodation_booking($booking_id);
			
			if ($booking_object) {
				$accommodation_id = $booking_object->accommodation_id;
				$room_type_id = $booking_object->room_type_id;
			}
		} else {
		
			if (isset($_POST['accommodation_id']) && !$booking_insert_success) {
				$accommodation_id = (int)$_POST['accommodation_id'];
			}
			
			if (isset($_POST['room_type_id']) && !$booking_insert_success) {
				$room_type_id = (int)$_POST['room_type_id'];
			}
		}
		
		echo '<script>';
		
		if ($accommodation_id > 0) {
		
			$accommodation_obj = new BookYourTravel_Accommodation($accommodation_id);
			
			$checkin_week_day = $accommodation_obj->get_checkin_week_day();
			$rent_type = $accommodation_obj->get_rent_type();
			$checkout_week_day = $accommodation_obj->get_checkout_week_day();
			$is_price_per_person = $accommodation_obj->get_is_price_per_person();
			$disabled_room_types = $accommodation_obj->get_disabled_room_types();
			$min_days_stay = $accommodation_obj->get_min_days_stay();
			$max_days_stay = $accommodation_obj->get_max_days_stay();
			$children_stay_free = $accommodation_obj->get_count_children_stay_free();
			
			echo 'window.accommodationId = ' . $accommodation_id . ';';
			echo 'window.accommodationRentType = ' . ($rent_type > 0 ? $rent_type : 0) . ';';
			echo 'window.roomTypeId = ' . ($room_type_id > 0 ? $room_type_id : 0) . ';';
			echo 'window.disabledRoomTypes = ' . json_encode($disabled_room_types) . ';';
			echo 'window.isPricePerPerson = ' . json_encode($is_price_per_person) . ';';
			echo 'window.countChildrenStayFree = ' . $children_stay_free . ';';
			echo 'window.accommodationMinDaysStay = ' . $min_days_stay . ';';
			echo 'window.accommodationMaxDaysStay = ' . $max_days_stay . ';'; 
			echo 'window.accommodationCheckinWeekday = ' . json_encode($checkin_week_day) . ';';
			echo 'window.accommodationCheckoutWeekday = ' . json_encode($checkout_week_day) . ';';		
		}

		echo '</script>';	
		
		if (isset($_GET['view'])) {
			$wp_accommodation_bookings_table->render_view_form(); 
		} else if (isset($_GET['sub']) && $_GET['sub'] == 'manage') {
			$wp_accommodation_bookings_table->render_entry_form($booking_id); 
		} else {	
			$wp_accommodation_bookings_table->prepare_items(); 
			
			if (!empty($_REQUEST['s']))
				$form_uri = esc_url( add_query_arg( 's', sanitize_text_field($_REQUEST['s']), $_SERVER['REQUEST_URI'] ));
			else 
				$form_uri = esc_url($_SERVER['REQUEST_URI']);	
			?>
			<div class="alignright actions ">
				<form method="get" action="<?php echo esc_url($form_uri); ?>">
					<input type="hidden" name="paged" value="1">
					<input type="hidden" name="post_type" value="accommodation">
					<input type="hidden" name="page" value="theme_accommodation_booking_admin.php">
					<?php
					$wp_accommodation_bookings_table->search_box( 'search', 'search_id' );
					?>
				</form>
			</div>
			<?php	
			$wp_accommodation_bookings_table->display(); 
		} 
	}
}

global $accommodation_bookings_admin;
$accommodation_bookings_admin = BookYourTravel_Accommodation_Bookings_Admin::get_instance();

/************************** CREATE A PACKAGE CLASS *****************************
 *******************************************************************************
 * Create a new list table package that extends the core WP_List_Table class.
 * WP_List_Table contains most of the framework for generating the table, but we
 * need to define and override some methods so that our data can be displayed
 * exactly the way we need it to be.
 * 
 * To display this on a page, you will first need to instantiate the class,
 * then call $yourInstance->prepare_items() to handle any data manipulation, then
 * finally call $yourInstance->display() to render the table to the page.
 */
class Accommodation_Bookings_Admin_List_Table extends WP_List_Table {

	private $options;
	private $date_format;
	private $price_decimal_places;
	private $default_currency_symbol;
	private $show_currency_symbol_after;
	
	/**
	* Constructor, we override the parent to pass our own arguments.
	* We use the parent reference to set some default configs.
	*/
	function __construct() {
	
		global $status, $page;
		global $bookyourtravel_theme_globals;
		
		$this->price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
		$this->default_currency_symbol = $bookyourtravel_theme_globals->get_default_currency_symbol();
		$this->show_currency_symbol_after = $bookyourtravel_theme_globals->show_currency_symbol_after();
		$this->date_format = get_option('date_format');
	
		 parent::__construct( array(
			'singular'=> 'booking', // Singular label
			'plural' => 'bookings', // plural label, also this well be one of the table css class
			'ajax'	=> false // We won't support Ajax for this table
		) );
		
	}	

	function column_default( $item, $column_name ) {
		return $item->$column_name;
	}
	
    protected function display_tablenav( $which ) {
		if ( 'top' === $which ) {
			wp_nonce_field( 'bulk-' . $this->_args['plural'] );
		}
		?>
		<div class="tablenav <?php echo esc_attr( $which ); ?>">
			<?php
			$this->extra_tablenav( $which );
			$this->pagination( $which );
			?>
			<br class="clear" />
		</div>
		<?php
	}	
	
	function extra_tablenav( $which ) {
		if ( $which == "top" ) {	
			//The code that goes before the table is here
		?>
		<div class="alignleft actions bookyourtravel-admin-top">
			<a href="edit.php?post_type=accommodation&page=theme_accommodation_booking_admin.php&sub=manage" class="button-secondary action" ><?php esc_html_e('Add Booking', 'bookyourtravel') ?></a>
		</div>
		<?php
		}
		if ( $which == "bottom" ) {
			//The code that goes after the table is there 
		?>
		<div class="alignleft actions bookyourtravel-admin-bottom">
			<a href="edit.php?post_type=accommodation&page=theme_accommodation_booking_admin.php&sub=manage" class="button-secondary action" ><?php esc_html_e('Add Booking', 'bookyourtravel') ?></a>
		</div>
		<?php
		}
	}	

	function format_price($price) {
		if (!$this->show_currency_symbol_after) {
			return $this->default_currency_symbol . '' . number_format_i18n( $price, $this->price_decimal_places );
		} else {
			return number_format_i18n( $price, $this->price_decimal_places ) . '' . $this->default_currency_symbol;
		}
	}
	
	function column_Customer($item) {
		return $item->first_name . ' ' . $item->last_name;	
	}
	
	function column_Accommodation($item) {
		return $item->accommodation_name . (isset($item->room_type) ? '<br />' . $item->room_type : '');	
	}
	
	function column_TotalPrice($item) {
		return $this->format_price($item->total_price);
	}
	
	function column_DateFrom($item) {
		return date($this->date_format, strtotime($item->date_from));	
	}
	
	function column_DateTo($item) {
		return date($this->date_format, strtotime($item->date_to));	
	}
	
	function column_Created($item) {
		return date($this->date_format, strtotime($item->created) );	
	}
	
	function column_Action($item) {
		return  "<a href='edit.php?post_type=accommodation&page=theme_accommodation_booking_admin.php&sub=manage&booking_id=" . $item->Id . "'>" . esc_html__('Edit', 'bookyourtravel') . "</a> | 		
				<form method='post' name='delete_booking_" . $item->Id . "' id='delete_booking_" . $item->Id . "' style='display:inline;'>"
				. wp_nonce_field('bookyourtravel_nonce') . "
					<input type='hidden' name='delete_booking' id='delete_booking' value='" . $item->Id . "' />
					<a href='javascript: void(0);' onclick='confirmDelete(\"#delete_booking_" . $item->Id . "\", \"" . esc_html__('Are you sure?', 'bookyourtravel') . "\");'>" . esc_html__('Delete', 'bookyourtravel') . "</a>
				</form>";
	}	
	
	/**
	 * Define the columns that are going to be used in the table
	 * @return array $columns, the array of columns to use with the table
	 */
	function get_columns() {
		return $columns= array(
			'Id'=>esc_html__('Id', 'bookyourtravel'),
			'Customer'=>esc_html__('Customer', 'bookyourtravel'),
			'DateFrom'=>esc_html__('From', 'bookyourtravel'),
			'DateTo'=>esc_html__('To', 'bookyourtravel'),
			'Accommodation'=>esc_html__('Accommodation', 'bookyourtravel'),
			'TotalPrice'=>esc_html__('Total Price', 'bookyourtravel'),
			'Created'=>esc_html__('Created', 'bookyourtravel'),
			'Action'=>esc_html__('Action', 'bookyourtravel'),				
		);
	}	
		
	/**
	 * Decide which columns to activate the sorting functionality on
	 * @return array $sortable, the array of columns that can be sorted by the user
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'Id'=> array( 'Id', true ),
			'Accommodation'=> array( 'accommodations.post_title', true ),
			'TotalPrice'=> array( 'total_price', true ),
			'DateFrom'=> array( 'date_from', true ),
			'DateTo'=> array( 'date_to', true ),
			'Created'=> array( 'created', true ),
		);
		return $sortable_columns;
	}	
	
	/**
	 * Prepare the table with different parameters, pagination, columns and table elements
	 */
	function prepare_items() {
	
		global $bookyourtravel_accommodation_helper, $bookyourtravel_room_type_helper;
		global $_wp_column_headers;
		
		$screen = get_current_screen();
		$user = get_current_user_id();
		$option = $screen->get_option('per_page', 'option');
		$per_page = get_user_meta($user, $option, true);
		if ( empty ( $per_page) || $per_page < 1 ) {
			$per_page = $screen->get_option( 'per_page', 'default' );
		}	

		$search_term = '';
		if (!empty($_REQUEST['s'])) {
			$search_term = strtolower(sanitize_text_field($_REQUEST['s']));
		}

		$columns = $this->get_columns(); 
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);		
		
		/* -- Ordering parameters -- */
		//Parameters that are going to be used to order the result
		$orderby = !empty($_GET["orderby"]) ? sanitize_text_field($_GET["orderby"]) : 'Id';
		$order = !empty($_GET["order"]) ? sanitize_text_field($_GET["order"]) : 'ASC';
		
		/* -- Pagination parameters -- */
		//How many to display per page?
		//Which page is this?
		$paged = !empty($_GET["paged"]) ? sanitize_text_field($_GET["paged"]) : '';
		//Page Number
		if(empty($paged) || !is_numeric($paged) || $paged<=0 ) { $paged=1; }
		//How many pages do we have in total?

		$author_id = null;
		if (!is_super_admin()) {
			$author_id = get_current_user_id();
		}
		
		$accommodation_booking_results = $bookyourtravel_accommodation_helper->list_accommodation_bookings($paged, $per_page, $orderby, $order, $search_term, 0, $author_id);		
		//Number of elements in your table?
		$totalitems = $accommodation_booking_results['total']; //return the total number of affected rows

		$totalpages = ceil($totalitems/$per_page);

		/* -- Register the pagination -- */
		$this->set_pagination_args( array(
			"total_items" => $totalitems,
			"total_pages" => $totalpages,
			"per_page" => $per_page,
		) );
		//The pagination links are automatically built according to those parameters

		/* -- Register the Columns -- */
		$columns = $this->get_columns();
		$_wp_column_headers[$screen->id]=$columns;

		/* -- Fetch the items -- */
		$this->items = $accommodation_booking_results['results'];
	}
	
	function handle_form_submit() {
		
		global $bookyourtravel_accommodation_helper, $bookyourtravel_room_type_helper, $bookyourtravel_theme_globals, $bookyourtravel_theme_woocommerce, $booking_insert_success;
		
		$use_woocommerce_for_checkout = $bookyourtravel_theme_globals->use_woocommerce_for_checkout();
		$current_user = wp_get_current_user();
		
		if (isset($_POST['delete_booking']) && check_admin_referer('bookyourtravel_nonce')) {
		
			$booking_id = absint($_POST['delete_booking']);
			
			$bookyourtravel_accommodation_helper->delete_accommodation_booking($booking_id);
			
			echo '<div class="updated" id="message" onclick="this.parentNode.removeChild(this)">';
			echo '<p>' . esc_html__('Successfully deleted booking!', 'bookyourtravel') . '</p>';
			echo '</div>';
			
		} else if ((isset($_POST['insert']) || isset($_POST['update'])) && check_admin_referer('bookyourtravel_nonce')) {
		
			$error = '';		
			$accommodation_id = 0;
			$accommodation_obj = null;
			
			if(empty($_POST['accommodation_id'])) {
				$error = esc_html__('You must select an accommodation', 'bookyourtravel');
			}
			
			if (!empty($error)) {
			
				echo '<div class="error" id="message" onclick="this.parentNode.removeChild(this)">';
				echo '<p>' . $error . '</p>';
				echo '</div>';
			
			} else {

				$accommodation_id = (int)$_POST['accommodation_id'];
				$accommodation_obj = new BookYourTravel_Accommodation($accommodation_id);
				$disabled_room_types = $accommodation_obj->get_disabled_room_types();
				
				if(!$disabled_room_types && empty($_POST['room_type_id'])) {
					$error = esc_html__('You must select a room type', 'bookyourtravel');
				} else if (!$disabled_room_types && (empty($_POST['room_count']) || $_POST['room_count'] === 0)) {
					$error = esc_html__('You must provide a valid room count', 'bookyourtravel');
				} else if(empty($_POST['date_from'])) {
					$error = esc_html__('You must select a from date', 'bookyourtravel');
				} else if(empty($_POST['date_to'])) {
					$error = esc_html__('You must select a to date', 'bookyourtravel');
				} else if(empty($_POST['total_price']) || $_POST['total_price'] === 0) {
					$error = esc_html__('A valid total price must be calculated', 'bookyourtravel');
				}

				if (!empty($error)) {
				
					  echo '<div class="error" id="message" onclick="this.parentNode.removeChild(this)">';
					  echo '<p>' . $error . '</p>';
					  echo '</div>';
					  
				} else {
				
					$room_type_id = 0;
					if (isset($_POST['room_type_id'])) {
						$room_type_id = (int)$_POST['room_type_id'];			
					}
					
					$room_type_obj = null;
					if ($room_type_id > 0) {
						$room_type_obj = new BookYourTravel_Room_Type($room_type_id);
					}
				
					$booking_object = $bookyourtravel_accommodation_helper->retrieve_booking_values_from_request();
				
					if (isset($_POST['insert'])) {
						
						$booking_object->Id = $bookyourtravel_accommodation_helper->create_accommodation_booking($current_user->ID, $booking_object);
						
						$address_array = array(
							'first_name' => isset($booking_object->first_name) ? $booking_object->first_name : '',
							'last_name'  => isset($booking_object->last_name) ? $booking_object->last_name : '',
							'company'    => isset($booking_object->company) ? $booking_object->company : '',
							'email'      => isset($booking_object->email) ? $booking_object->email : '',
							'phone'      => isset($booking_object->phone) ? $booking_object->phone : '',
							'address_1'  => isset($booking_object->address) ? $booking_object->address : '',
							'address_2'  => isset($booking_object->address_2) ? $booking_object->address_2 : '',
							'city'       => isset($booking_object->city) ? $booking_object->city : '',
							'state'      => isset($booking_object->state) ? $booking_object->state : '',
							'postcode'   => isset($booking_object->postcode) ? $booking_object->postcode : '',
							'country'    => isset($booking_object->country) ? $booking_object->country : '',
						);						

						$accommodation_is_reservation_only = $accommodation_obj->get_is_reservation_only();
						
						if (BookYourTravel_Theme_Utils::is_woocommerce_active() && $use_woocommerce_for_checkout && !$accommodation_is_reservation_only) {
							$bookyourtravel_theme_woocommerce->dynamically_create_accommodation_woo_order($booking_object->Id, $booking_object->total_price, $address_array, $accommodation_id, $room_type_id);
						}
						
						if ($booking_object->Id > 0) {
							echo '<div class="updated" id="message" onclick="this.parentNode.removeChild(this)">';
							echo '<p>' . esc_html__('Successfully inserted new accommodation booking entry!', 'bookyourtravel') . '</p>';
							echo '</div>';
							$booking_insert_success = true;
						} else {
							echo '<div class="error" id="message" onclick="this.parentNode.removeChild(this)">';
							echo '<p>' . esc_html__('Oops. Something happened! Failed to insert new accommodation booking entry!', 'bookyourtravel') . '</p>';
							echo '</div>';						
						}
					} else if (isset($_POST['update'])) {

						$booking_id = isset($_POST['booking_id']) ? absint($_POST['booking_id']) : 0;
				
						$result = $bookyourtravel_accommodation_helper->update_accommodation_booking ($booking_id, $booking_object);

						if ($result == 1) {
							echo '<div class="updated" id="message" onclick="this.parentNode.removeChild(this)">';
							echo '<p>' . esc_html__('Successfully updated accommodation booking entry!', 'bookyourtravel') . '</p>';
							echo '</div>';
						} else {
							echo '<div class="error" id="message" onclick="this.parentNode.removeChild(this)">';
							echo '<p>' . esc_html__('Oops. Something happened! Failed to update accommodation booking entry!', 'bookyourtravel') . '</p>';
							echo '</div>';	
						}
						
					}
					
					return $booking_object->Id;
				}
			}
		}
	}
			
	function render_entry_form($booking_id) {
		
		global $booking_insert_success, $bookyourtravel_accommodation_helper, $bookyourtravel_room_type_helper, $enable_extra_items, $bookyourtravel_theme_globals;
		
		$booking_object = null;
		
		if ($booking_id > 0) {
			$booking_object = $bookyourtravel_accommodation_helper->get_accommodation_booking($booking_id);
		}
		
		$accommodation_id = 0;
		if (isset($_GET['accommodation_id'])) {
			$accommodation_id = absint($_GET['accommodation_id']);
		} else if (isset($_POST['accommodation_id']) && !$booking_insert_success ) {
			$accommodation_id = intval(sanitize_text_field($_POST['accommodation_id']));
		} else if ($booking_object != null) {
			$accommodation_id = $booking_object->accommodation_id;
		}
		
		$room_type_id = 0;
		if (isset($_GET['room_type_id'])) {
			$room_type_id = absint($_GET['room_type_id']);
		} else if (isset($_POST['room_type_id']) && !$booking_insert_success) {
			$room_type_id = intval(sanitize_text_field($_POST['room_type_id']));
		} else if ($booking_object != null) {
			$room_type_id = $booking_object->room_type_id;
		}
		
		$date_from = null;
		$date_from_formatted = '';
		if (isset($_POST['date_from']) && !$booking_insert_success)
			$date_from = sanitize_text_field($_POST['date_from']);
		else if ($booking_object != null) {
			$date_from = $booking_object->date_from;
		}
		if (isset($date_from))
			$date_from_formatted = date( $this->date_format, strtotime( $date_from ) );
		
		$date_to = null;
		$date_to_formatted = '';
		if (isset($_POST['date_to']) && !$booking_insert_success)
			$date_to = sanitize_text_field($_POST['date_to']);
		else if ($booking_object != null) {
			$date_to = $booking_object->date_to;
		}
		if (isset($date_to))
			$date_to_formatted = date( $this->date_format, strtotime( $date_to ) );
		
		$adults = 1;
		if (isset($_POST['adults']) && !$booking_insert_success)
			$adults = intval(sanitize_text_field($_POST['adults']));
		else if ($booking_object != null) {
			$adults = $booking_object->adults;
		}
		
		$children = 0;
		if (isset($_POST['children']) && !$booking_insert_success)
			$children = intval(sanitize_text_field($_POST['children']));
		else if ($booking_object != null) {
			$children = $booking_object->children;
		}
		
		$room_count = 1;
		if (isset($_POST['room_count']) && !$booking_insert_success)
			$room_count = intval(sanitize_text_field($_POST['room_count']));
		else if ($booking_object != null) {
			$room_count = $booking_object->room_count;
		}
		
		$total_price = 0;
		if (isset($_POST['total_price']) && !$booking_insert_success)
			$total_price = floatval(sanitize_text_field($_POST['total_price']));
		else if ($booking_object != null) {
			$total_price = $booking_object->total_price;
		}
		
		$total_extra_items_price = 0;
		if (isset($_POST['total_extra_items_price']) && !$booking_insert_success)
			$total_extra_items_price = floatval(sanitize_text_field($_POST['total_extra_items_price']));
		else if ($booking_object != null) {
			$total_extra_items_price = $booking_object->total_extra_items_price;
		}
		
		$total_accommodation_price = 0;
		if (isset($_POST['total_accommodation_price']) && !$booking_insert_success)
			$total_accommodation_price = floatval(sanitize_text_field($_POST['total_accommodation_price']));
		else if ($booking_object != null) {
			$total_accommodation_price = $booking_object->total_accommodation_price;
		}
				
		if ($booking_object)
			echo '<h3>' . esc_html__('Update Accommodation Booking', 'bookyourtravel') . '</h3>';
		else
			echo '<h3>' . esc_html__('Add Accommodation Booking', 'bookyourtravel') . '</h3>';
			
		if ($booking_id > 0 && BookYourTravel_Theme_Utils::is_woocommerce_active() && $accommodation_id > 0) {
			echo esc_html__('Please note: you are unable to edit certain fields of existing bookings because you are using WooCommerce for payment processing. In order to keep data in sync between the book your travel bookings table and WooCommerce orders, and to prevent data corruption or loss, fields like accommodation, room type, dates to and from, numbers of rooms, adults and children as well as the total price of a booking cannot be edited.', 'bookyourtravel');
		}		

		echo '<form id="accommodation_booking_form" method="post" action="' . esc_url($_SERVER['REQUEST_URI']) . '" style="clear: both;">';
		
		echo wp_nonce_field('bookyourtravel_nonce');	
		echo "<input type='hidden' name='booking_id' id='booking_id' value='" . (isset($booking_id) ? $booking_id : 0) . "' />";
		echo '<table cellpadding="3" class="form-table"><tbody>';
		
		$accommodation_obj = null;		
		if ($accommodation_id > 0) {
			$accommodation_obj = new BookYourTravel_Accommodation((int)$accommodation_id);
		}
		
		$room_type_obj = null;
		if ($room_type_id > 0) {
			$room_type_obj = new BookYourTravel_Room_Type((int)$room_type_id);
		}		
		
		$author_id = null;
		if (!is_super_admin()) {
			$author_id = get_current_user_id();
		}

		$accommodations_control = '';
		
		if ($booking_id > 0 && BookYourTravel_Theme_Utils::is_woocommerce_active() && $accommodation_id > 0) {
			// we are editing a booking and woocommerce is active, so don't allow change of accommodation because data will be corrupt then (data sync between woocommerce order and byt booking).
			
			$accommodations_control .= "<input type='hidden' value='" . $accommodation_id . "' id='accommodation_id' name='accommodation_id' />";
			$accommodations_control .= $accommodation_obj->get_title();
			
		} else {
		
			$accommodation_results = $bookyourtravel_accommodation_helper->list_accommodations(0, -1, 'title', 'ASC', 0, array(), array(), array(), false, null, $author_id);

			$accommodations_control = '<select id="accommodation_id" name="accommodation_id" class="booking_accommodations_select">';
			$accommodations_control .= '<option value="">' . esc_html__('Select accommodation', 'bookyourtravel') . '</option>';
			if ( count($accommodation_results) > 0 && $accommodation_results['total'] > 0 ) {
				foreach ($accommodation_results['results'] as $accommodation_result) {
					$accommodations_control .= '<option value="' . $accommodation_result->ID . '" ' . ($accommodation_result->ID == $accommodation_id ? 'selected' : '') . '>' . $accommodation_result->post_title . '</option>';
				}
			}
			$accommodations_control .= '</select>';
		}
			
		echo '<tr>';
		echo '	<th scope="row" valign="top">' . esc_html__('Accommodation', 'bookyourtravel') . '</th>';
		echo '	<td>' . $accommodations_control . '</td>';
		echo '</tr>';
		
		$room_types_control = '';

		if ($booking_id > 0 && BookYourTravel_Theme_Utils::is_woocommerce_active() && $room_type_obj != null) {		
		
			// we are editing a booking and woocommerce is active, so don't allow change of accommodation because data will be corrupt then (data sync between woocommerce order and byt booking).
			
			$room_types_control .= "<input type='hidden' value='" . $room_type_id . "' id='room_type_id' name='room_type_id' />";
			$room_types_control .= $room_type_obj->get_title();		
		
		} else {
		
			$room_types_control = '<select id="room_type_id" name="room_type_id">';
			$room_types_control .= '<option value="">' . esc_html__('Select room type', 'bookyourtravel') . '</option>';
			
			if ($accommodation_obj) {
			
				$disabled_room_types = $accommodation_obj->get_disabled_room_types();
			
				if (!$disabled_room_types) {
					
					if ($accommodation_obj) { 				
						$room_type_ids = $accommodation_obj->get_room_types();				
						if ($room_type_ids && count($room_type_ids) > 0) {
							for ( $i = 0; $i < count($room_type_ids); $i++ ) {
								$temp_id = $room_type_ids[$i];
								$room_type_obj = new BookYourTravel_Room_Type(intval($temp_id));
								$room_types_control .= '<option value="' . $temp_id . '" ' . ($temp_id == $room_type_id ? 'selected' : '') . '>' . $room_type_obj->get_title() . '</option>';
							}
						}
					}
					
				}
			}
			
			$room_types_control .= '</select>';
		}
		
		echo '<tr style="display:none;" class="accommodation_selected step_0">';
		echo '	<th scope="row" valign="top">' . esc_html__('Room type', 'bookyourtravel') . '</th>';
		echo '	<td>' . $room_types_control . '</td>';
		echo '</tr>';
		
		echo '<tr style="display:none;" class="accommodation_selected step_1">';
		echo '	<th scope="row" valign="top">' . esc_html__('Date from', 'bookyourtravel') . '</th>';
		echo '	<td>';
		
		if ($booking_id > 0) {
			echo $date_from_formatted;
		} else {		
			echo '		<script>';
			echo '			window.datepickerDateFromValue = "' . (isset($date_from_formatted) ? $date_from_formatted : null) . '";';
			echo '  	</script>';				
			echo '  	<input class="datepicker" type="text" name="booking_datepicker_from" id="booking_datepicker_from" />';
		} 
		echo '		<input type="hidden" name="date_from" id="date_from" value="' . $date_from . '" />';
		echo '	</td>';	
		echo '</tr>';

		echo '<tr style="display:none;" class="accommodation_selected step_2">';
		echo '	<th scope="row" valign="top">' . esc_html__('Date to', 'bookyourtravel') . '</th>';
		echo '	<td>';
		if ($booking_id > 0) {
			echo $date_to_formatted;
		} else {
			echo '		<script>';
			echo '			window.datepickerDateToValue = "' . (isset($date_to_formatted) ? $date_to_formatted : null) . '";';
			echo '  	</script>';				
			echo '  	<input class="datepicker" type="text" name="booking_datepicker_to" id="booking_datepicker_to" />';
		}
		echo '		<input type="hidden" name="date_to" id="date_to" value="' . $date_to . '" />';
		echo '	</td>';	
		echo '</tr>';

		$booking_object_other_fields = isset($booking_object->other_fields) ? unserialize($booking_object->other_fields) : array();
		$booking_form_fields = $bookyourtravel_theme_globals->get_booking_form_fields();
		
		foreach ($booking_form_fields as $booking_field) {
		
			$field_type = $booking_field['type'];
			$field_hidden = isset($booking_field['hide']) && $booking_field['hide'] == 1 ? true : false;
			$field_id = $booking_field['id'];
			$field_required = isset($booking_field['required']) && $booking_field['required'] == '1' ? true : false;
			
			$field_value = '';
			
			if ($field_id == 'first_name' || $field_id == 'last_name' || $field_id == 'email' || $field_id == 'phone' || $field_id == 'address' || $field_id == 'town' || $field_id == 'zip' || $field_id == 'country' || $field_id == 'special_requirements' || $field_id == 'state' || $field_id == 'address_2' || $field_id == 'company') {
				$field_value = isset($booking_object->{$field_id}) ? $booking_object->{$field_id} : '';
			} else {
				if (isset($booking_object_other_fields[$field_id]))
					$field_value = $booking_object_other_fields[$field_id];
			}

			if (!$field_hidden) {			
			
				echo '<tr style="display:none;" class="accommodation_selected step_3">';
				echo '	<th scope="row" valign="top">' . esc_html($booking_field['label']) . '</th>';
				echo '	<td>';

				if ($field_type == 'email') {
					echo '<input value="' . esc_attr($field_value) . '" ' . ($field_required ? 'data-required' : '') . ' type="email" id="' . esc_attr($field_id) . '" name="' . esc_attr($field_id) . '" />';
				} else if ($field_type == 'textarea') {
					echo '<textarea ' . ($field_required ? 'data-required' : '') . ' name="' . esc_attr($field_id) . '" id="' . esc_attr($field_id) . '" rows="5" cols="50" >' . esc_html($field_value) . '</textarea>';
				} else {
					echo '<input value="' . esc_attr($field_value) . '" ' . ($field_required ? 'data-required' : '') . ' type="text" id="' . esc_attr($field_id) . '" name="' . esc_attr($field_id) . '" />';
				}
			}
			
			echo '  </td>';
			echo '</tr>';
		}
		
		echo '<tr style="display:none;" class="accommodation_selected step_3">';
		echo '	<th scope="row" valign="top">' . esc_html__('Number of adults', 'bookyourtravel') . '</th>';
		echo '	<td>';
		if ($booking_id > 0) {
			echo '	<input type="hidden" name="adults" id="adults" value="' . $adults . '" />' . $adults;		
		} else {
			echo '<select id="adults" name="adults" class="booking_select_adults">';
			echo '</select>';
		}
		echo '	</td>';
		echo '</tr>';
		
		echo '<tr style="display:none;" class="accommodation_selected step_3">';
		echo '	<th scope="row" valign="top">' . esc_html__('Number of children', 'bookyourtravel') . '</th>';
		echo '	<td>';
		if ($booking_id > 0) {
			echo '	<input type="hidden" name="children" id="children" value="' . $children . '" />' . $children;		
		} else {
			echo '<select id="children" name="children" class="booking_select_children">';
			echo '</select>';
		}
		echo '	</td>';
		echo '</tr>';
		
		echo '<tr style="display:none;" class="accommodation_selected step_3">';
		echo '	<th scope="row" valign="top">' . esc_html__('Number of rooms', 'bookyourtravel') . '</th>';
		echo '	<td>';
		if ($booking_id > 0) {
			echo '	<input type="hidden" name="room_count" id="room_count" value="' . $room_count . '" />' . $room_count;		
		} else {
			echo '<select id="room_count" name="room_count" class="booking_select_room_count">';
			echo '</select>';
		}
		echo '	</td>';
		echo '</tr>';
		
		$enable_extra_items = $bookyourtravel_theme_globals->enable_extra_items();
		
		if ($enable_extra_items) {
		
			echo '<tr style="display:none;" class="accommodation_selected step_3">';
			echo '	<th scope="row" valign="top">' . esc_html__('Extra items', 'bookyourtravel') . '</th>';
			echo '	<td>';
			
			if (isset($booking_object->extra_items)) {
				$extra_items_array = unserialize($booking_object->extra_items);
				if ($extra_items_array && count($extra_items_array) > 0) {
				
					echo "<table>";
					echo "<thead>";
					echo "<tr>";
					echo "<th>" . esc_html__('Item', 'bookyourtravel') . "</th>";
					echo "<th>" . esc_html__('Quantity', 'bookyourtravel') . "</th>";
					echo "<th>" . esc_html__('Per day?', 'bookyourtravel') . "</th>";
					echo "<th>" . esc_html__('Per person?', 'bookyourtravel') . "</th>";
					echo "<th>" . esc_html__('Price', 'bookyourtravel') . "</th>";
					echo "</tr>";
					echo "</thead>";
					echo "<tbody>";
					
					$item_count = 0;
					foreach ($extra_items_array as $extra_item_id => $quantity) {
						
						$accommodation_extra_item_obj = new BookYourTravel_Extra_Item($extra_item_id);
						$item_price = $accommodation_extra_item_obj->get_custom_field('_extra_item_price', false);
						$item_price_per_day = intval($accommodation_extra_item_obj->get_custom_field('_extra_item_price_per_day', false));
						$item_price_per_person = intval($accommodation_extra_item_obj->get_custom_field('_extra_item_price_per_person', false));
						
						echo "<tr>";
						echo "<td>";
						echo "<input type='hidden' id='extra_item_" . $extra_item_id . "_id' name='extra_items[" . $item_count . "][id]' value='" . $extra_item_id . "' />";
						echo "<input type='hidden' id='extra_item_" . $extra_item_id . "_quantity' name='extra_items[" . $item_count . "][quantity]' value='" . $quantity . "' />";
						echo esc_html($accommodation_extra_item_obj->get_title());
						echo "</td>";
						echo "<td>";
						echo $quantity;
						echo "</td>";
						echo "<td>";
						echo ($item_price_per_day ? esc_html__('Yes', 'bookyourtravel') : esc_html__('No', 'bookyourtravel'));
						echo "</td>";
						echo "<td>";
						echo ($item_price_per_person ? esc_html__('Yes', 'bookyourtravel') : esc_html__('No', 'bookyourtravel'));
						echo "</td>";
						echo "<td>";
						echo number_format_i18n( $item_price, $this->price_decimal_places );
						echo "</td>";
						echo "</tr>";
						
						$item_count++;
					}
					
					echo "</tbody>";
					echo "</table>";
					
				} else {
					echo "<input type='hidden' name='extra_items[]' id='extra_items[]' />";
					echo esc_html__('None selected', 'bookyourtravel');
				}
			} else {
				echo "<input type='hidden' name='extra_items[]' id='extra_items[]' />";
				echo esc_html__('None selected', 'bookyourtravel');
			}
			
			echo '</td>';
			echo '</tr>';
			
			echo '<tr style="display:none;" class="accommodation_selected step_3">';
			echo '	<th scope="row" valign="top">' . esc_html__('Reservation total', 'bookyourtravel') . '</th>';
			echo '	<td><input type="hidden" name="total_accommodation_price" id="total_accommodation_price" value="' . $total_accommodation_price . '" /><span class="total_accommodation_price">' . $this->format_price($total_accommodation_price) . '</span></td>';		
			echo '</tr>';

			echo '<tr style="display:none;" class="accommodation_selected step_3">';
			echo '	<th scope="row" valign="top">' . esc_html__('Extra items total', 'bookyourtravel') . '</th>';
			echo '	<td><input type="hidden" name="total_extra_items_price" id="total_extra_items_price" value="' . $total_extra_items_price . '" />' . $this->format_price($total_extra_items_price) . '</td>';		
			echo '</tr>';
		
		}
		
		echo '<tr style="display:none;" class="accommodation_selected step_3">';
		echo '	<th scope="row" valign="top">' . esc_html__('Total price', 'bookyourtravel') . '</th>';
		echo '	<td><input type="hidden" name="total_price" id="total_price" value="' . $total_price . '" /><span class="total_price">' . $this->format_price($total_price) . '</span></td>';		
		echo '</tr>';
		
		echo '</table>';
		echo '<p>';
		echo '<a href="edit.php?post_type=accommodation&page=theme_accommodation_booking_admin.php" class="button-secondary">' . esc_html__('Cancel', 'bookyourtravel') . '</a>&nbsp;';
		if ($booking_object) {
			echo '<span style="display:none;" class="accommodation_selected step_3">';
			echo '<input id="booking_id" name="booking_id" value="' . $booking_id . '" type="hidden" />';
			echo '<input class="button-primary" type="submit" name="update" value="' . esc_html__('Update Booking', 'bookyourtravel') . '"/>';
			echo '</span>';
		} else {
			echo '<span style="display:none;" class="accommodation_selected step_3">';
			echo '<input class="button-primary" type="submit" name="insert" value="' . esc_html__('Add Booking', 'bookyourtravel') . '"/>';
			echo '</span>';
		}
		echo '</p>';
		echo '</form>';		
	}
}