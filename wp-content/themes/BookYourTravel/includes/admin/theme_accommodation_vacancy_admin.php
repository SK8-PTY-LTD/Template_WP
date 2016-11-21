<?php
/*
*******************************************************************************
************************** LOAD THE BASE CLASS ********************************
*******************************************************************************
* The WP_List_Table class isn't automatically available to plugins, 
* so we need to check if it's available and load it if necessary.
*******************************************************************************
*/ 
if(!class_exists('WP_List_Table')) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class BookYourTravel_Accommodation_Vacancies_Admin extends BookYourTravel_BaseSingleton {
	
	private $enable_accommodations;
	private $price_decimal_places;
	private $default_currency_symbol;
	private $show_currency_symbol_after;	
	
	protected function __construct() {
	
		global $bookyourtravel_theme_globals;
		
		$this->price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
		$this->default_currency_symbol = $bookyourtravel_theme_globals->get_default_currency_symbol();
		$this->show_currency_symbol_after = $bookyourtravel_theme_globals->show_currency_symbol_after();
		
		$this->enable_accommodations = $bookyourtravel_theme_globals->enable_accommodations();

        // our parent class might
        // contain shared code in its constructor
        parent::__construct();
	}

    public function init() {

		if ($this->enable_accommodations) {	

			add_action( 'admin_menu' , array( $this, 'vacancies_admin_page' ) );
			add_filter( 'set-screen-option', array( $this, 'vacancies_set_screen_options' ), 10, 3);
			add_action( 'admin_head', array( $this, 'vacancies_admin_head' ) );
		}
	}
	
	function vacancies_admin_page() {
	
		$hook = add_submenu_page('edit.php?post_type=accommodation', esc_html__('Accommodation Vacancies', 'bookyourtravel'), esc_html__('Vacancies', 'bookyourtravel'), 'edit_posts', basename(__FILE__), array($this, 'vacancies_admin_display' ));
		add_action( "load-$hook", array($this,  'vacancies_add_screen_options' ));
	}	

	function vacancies_add_screen_options() {
	
		global $wp_accommodation_vacancy_table;
		
		$option = 'per_page';
		$args = array('label' => esc_html__('Vacancies', 'bookyourtravel'),'default' => 50,'option' => 'accommodation_vacancies_per_page');
		add_screen_option( $option, $args );
		
		$wp_accommodation_vacancy_table = new Accommodation_Vacancy_Admin_List_Table();
	}

	function vacancies_admin_display() {
	
		global $bookyourtravel_accommodation_helper, $bookyourtravel_room_type_helper, $wp_accommodation_vacancy_table;
		echo '<div class="wrap">';
		echo '<h2>' . esc_html__('Accommodation vacancies', 'bookyourtravel') . '</h2>';
		
		$wp_accommodation_vacancy_table->handle_form_submit();
		
		if (isset($_GET['sub']) && $_GET['sub'] == 'manage') {
		
			$wp_accommodation_vacancy_table->render_entry_form(); 
			
		} else {
		
			$accommodation_id = isset($_GET['accommodation_id']) ? intval($_GET['accommodation_id']) : 0;
			$accommodation_id = BookYourTravel_Theme_Utils::get_default_language_post_id($accommodation_id, 'accommodation');
			
			$accommodations_filter = '<select id="accommodations_filter" name="accommodations_filter">';
			$accommodations_filter .= '<option value="">' . esc_html__('Filter by accommodation', 'bookyourtravel') . '</option>';
						
			$author_id = null;
			if (!is_super_admin()) {
				$author_id = get_current_user_id();
			}
				
			$accommodation_results = $bookyourtravel_accommodation_helper->list_accommodations(0, -1, 'title', 'ASC', 0, array(), array(), array(), false, null, $author_id);
			if ( count($accommodation_results) > 0 && $accommodation_results['total'] > 0 ) {
				foreach ($accommodation_results['results'] as $accommodation_result) {
					global $post;				
					$post = $accommodation_result;
					setup_postdata( $post ); 				
					$accommodations_filter .= '<option value="' . $post->ID . '" ' . ($post->ID == $accommodation_id ? 'selected' : '') . '>' . $post->post_title . '</option>';
				}
			}
			$accommodations_filter .= '</select>';
			
			wp_reset_postdata();
		
			echo '<div class="alignleft bookyourtravel-admin-filter">';
			echo "<div class='alignleft actions'>" . esc_html__('Filter by accommodation: ', 'bookyourtravel') . $accommodations_filter . "</div>";
			echo "<div class='alignleft actions'><a class='button-secondary action alignleft' href='edit.php?post_type=accommodation&page=theme_accommodation_vacancy_admin.php'>";
			echo esc_html__('Reset filters', 'bookyourtravel');
			echo "</a></div>";
			echo '</div>';
			
			$wp_accommodation_vacancy_table->prepare_items(); 
			$wp_accommodation_vacancy_table->display();		
	?>
		<?php
		} 
	}

	function vacancies_set_screen_options($status, $option, $value) {
		if ( 'accommodation_vacancies_per_page' == $option ) 
			return $value;
	}

	function vacancies_admin_head() {
		$page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
		if( 'theme_accommodation_vacancy_admin.php' != $page )
			return;

		$this->vacancies_admin_styles();
	}
		
	function vacancies_admin_styles() {
	
		global $bookyourtravel_accommodation_helper;

		$vacancy_id = isset($_GET['vacancy_id']) ? absint($_GET['vacancy_id']) : "";
		$accommodation_id = 0;
		$room_type_id = 0;
		
		if (!empty($vacancy_id)) {
			$vacancy_object = $bookyourtravel_accommodation_helper->get_accommodation_vacancy($vacancy_id);

			if ($vacancy_object) {
				$accommodation_id = $vacancy_object->accommodation_id;
				$room_type_id = $vacancy_object->room_type_id;
			}
		} else {
		
			if (isset($_POST['accommodation_id'])) {
				$accommodation_id = (int)$_POST['accommodation_id'];
			}
			
			if (isset($_POST['room_type_id'])) {
				$room_type_id = (int)$_POST['room_type_id'];
			}
		}
		
		echo '<style type="text/css">';
		echo '	.wp-list-table .column-Id { width: 10%; }';
		echo '	.wp-list-table .column-AccommodationName { width: 15%; }';
		echo '	.wp-list-table .column-SeasonName { width: 15%; }';
		echo '	.wp-list-table .column-RoomType { width: 10%; }';
		echo '	.wp-list-table .column-StartDate { width: 12%; }';
		echo '	.wp-list-table .column-EndDate { width: 12%; }';
		echo '	.wp-list-table .column-Rooms { width: 5%; }';
		echo '	.wp-list-table .column-PricePerDay { width: 10%; }';
		echo '	.wp-list-table .column-Action { width: 10%; }';		
		echo '  table.calendar { width:60%; }
				table.calendar th { text-align:center; }
				table.calendar td { border:none;text-align:center;height:30px;line-height:30px;vertical-align:middle; }
				table.calendar td.sel a { color:#fff;padding:10px;background:#b1b1b1; }
				table.calendar td.cur a { color:#fff;padding:10px;background:#ededed; }';
		echo "</style>";
		echo '<script>';
		echo 'window.adminAjaxUrl = ' . json_encode(admin_url('admin-ajax.php')) . ';';
		echo 'window.datepickerDateFormat = ' . json_encode(BookYourTravel_Theme_Utils::dateformat_PHP_to_jQueryUI(get_option('date_format'))) . ';';
		echo 'window.datepickerAltFormat = ' . json_encode(BOOKYOURTRAVEL_ALT_DATE_FORMAT) . ';';
		echo 'window.pricePerDayLabel = ' . json_encode(__('Price per day', 'bookyourtravel')) . ';';
		echo 'window.pricePerWeekLabel = ' . json_encode(__('Price per week', 'bookyourtravel')) . ';';
		echo 'window.pricePerMonthLabel = ' . json_encode(__('Price per month', 'bookyourtravel')) . ';';
		
		if ($accommodation_id > 0) {
			$accommodation_obj = new BookYourTravel_Accommodation($accommodation_id);
			
			$checkin_week_day = $accommodation_obj->get_checkin_week_day();
			$checkout_week_day = $accommodation_obj->get_checkout_week_day();
			$is_price_per_person = $accommodation_obj->get_is_price_per_person();
			$disabled_room_types = $accommodation_obj->get_disabled_room_types();
			$min_days_stay = $accommodation_obj->get_min_days_stay();
			$max_days_stay = $accommodation_obj->get_max_days_stay();
			$rent_type = $accommodation_obj->get_rent_type();
			
			echo 'window.rentType = ' . json_encode($rent_type) . ';';
			echo 'window.accommodationId = ' . $accommodation_id . ';';
			echo 'window.roomTypeId = ' . ($room_type_id > 0 ? $room_type_id : 0) . ';';
			echo 'window.disabledRoomTypes = ' . json_encode($disabled_room_types) . ';';
			echo 'window.isPricePerPerson = ' . json_encode($is_price_per_person) . ';';
			echo 'window.accommodationMinDaysStay = ' . $min_days_stay . ';';
			echo 'window.accommodationMaxDaysStay = ' . $max_days_stay . ';'; 
			echo 'window.accommodationCheckinWeekday = ' . json_encode($checkin_week_day) . ';';
			echo 'window.accommodationCheckoutWeekday = ' . json_encode($checkout_week_day) . ';';	
		}
		
		echo '</script>';	
	}
}

global $accommodation_vacancies_admin;
$accommodation_vacancies_admin = BookYourTravel_Accommodation_Vacancies_Admin::get_instance();

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
class Accommodation_Vacancy_Admin_List_Table extends WP_List_Table {

	private $options;
	private $lastInsertedID;
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
			'singular'=> 'vacancy', // Singular label
			'plural' => 'vacancies', // plural label, also this well be one of the table css class
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
			$accommodation_id = isset($_GET['accommodation_id']) ? intval($_GET['accommodation_id']) : 0;
			$accommodation_id = BookYourTravel_Theme_Utils::get_default_language_post_id($accommodation_id, 'accommodation');
			
			$accommodation_title = '';
			if ($accommodation_id > 0)
				$accommodation_title = get_the_title($accommodation_id);
			?>
			<div class="alignleft actions bookyourtravel-admin-top">
				<a href="edit.php?post_type=accommodation&page=theme_accommodation_vacancy_admin.php&sub=manage" class="button-secondary action" ><?php esc_html_e('Add Vacancy', 'bookyourtravel') ?></a>
			</div>
		<?php
		}
		if ( $which == "bottom" ) { ?>
			<div class="alignleft actions bookyourtravel-admin-bottom">
				<a href="edit.php?post_type=accommodation&page=theme_accommodation_vacancy_admin.php&sub=manage" class="button-secondary action" ><?php esc_html_e('Add Vacancy', 'bookyourtravel') ?></a>
			</div>
		<?php }
	}		
	
	function column_SeasonName($item) {
		return $item->season_name;	
	}
	
	function column_AccommodationName($item) {
		return $item->accommodation_name;	
	}
	
	function column_RoomType($item) {
		if ($item->room_type && !$item->accommodation_disabled_room_types)
			return $item->room_type;	
		else
			return esc_html__('N/A', 'bookyourtravel');
	}
	
	function column_RoomCount($item) {
		if ($item->room_count && !$item->accommodation_disabled_room_types)
			return $item->room_count;	
		else
			return esc_html__('N/A', 'bookyourtravel');
	}
	
	function column_PricePerDay($item) {
	
		if ($item->accommodation_is_per_person) {
			return $this->format_price($item->price_per_day) . ' / ' . $this->format_price($item->price_per_day_child);	
		} else {
			return $this->format_price($item->price_per_day);
		}
	}
	
	function column_WeekendPricePerDay($item) {
		if ($item->accommodation_is_per_person) {
			if ($item->weekend_price_per_day > 0) {
				return $this->format_price($item->weekend_price_per_day) . ' / ' . $this->format_price($item->weekend_price_per_day_child);
			} else {
				return esc_html__('N/A', 'bookyourtravel');
			}
		} else {
			return $this->format_price($item->weekend_price_per_day);
		}
	}

	function column_StartDate($item) {
		return date($this->date_format, strtotime($item->start_date));	
	}
	
	function column_EndDate($item) {
		return date($this->date_format, strtotime($item->end_date));	
	}
	
	function column_Action($item) {
	
		$accommodation_id = isset($_GET['accommodation_id']) ? intval($_GET['accommodation_id']) : 0;
		
		$url_part = '';
		if ($accommodation_id > 0)
			$url_part .= "&accommodation_id=$accommodation_id";
	
		$action = "<form method='post' name='delete_vacancy_" . $item->Id . "' id='delete_vacancy_" . $item->Id . "' style='display:inline;'>
					<input type='hidden' name='delete_vacancy' id='delete_vacancy' value='" . $item->Id . "' />"
					. wp_nonce_field('bookyourtravel_nonce') . 
					"<a href='javascript: void(0);' onclick='confirmDelete(\"#delete_vacancy_" . $item->Id . "\", \"" . esc_html__('Are you sure?', 'bookyourtravel') . "\");'>" . esc_html__('Delete', 'bookyourtravel') . "</a>
				</form>";

		$action .= ' | 	<a href="edit.php?post_type=accommodation&page=theme_accommodation_vacancy_admin.php&sub=manage&vacancy_id=' . $item->Id . $url_part . '">' . esc_html__('Edit', 'bookyourtravel') . '</a>';
		
		return $action;
	}	
	
	function format_price($price) {
		if (!$this->show_currency_symbol_after) {
			return $this->default_currency_symbol . '' . number_format_i18n( $price, $this->price_decimal_places );
		} else {
			return number_format_i18n( $price, $this->price_decimal_places ) . '' . $this->default_currency_symbol;
		}
	}
	
	/**
	 * Define the columns that are going to be used in the table
	 * @return array $columns, the array of columns to use with the table
	 */
	function get_columns() {
		return $columns= array(
			'Id'=>esc_html__('Id', 'bookyourtravel'),
			'SeasonName'=>esc_html__('Season Name', 'bookyourtravel'),
			'AccommodationName'=>esc_html__('Accommodation Name', 'bookyourtravel'),
			'RoomType'=>esc_html__('Room Type', 'bookyourtravel'),
			'RoomCount'=>esc_html__('Rooms', 'bookyourtravel'),
			'StartDate'=>esc_html__('Start Date', 'bookyourtravel'),
			'EndDate'=>esc_html__('End Date', 'bookyourtravel'),
			'PricePerDay'=>esc_html__('Prices', 'bookyourtravel'),
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
			'SeasonName'=> array( 'season_name', true ),
			'AccommodationName'=> array( 'accommodations.post_title', true ),
			'RoomType'=> array( 'room_types.post_title', true ),
			'RoomCount'=> array( 'room_count', true ),
			'StartDate'=> array( 'start_date', true ),
			'EndDate'=> array( 'end_date', true ),
			'PricePerDay'=> array( 'price_per_day', true ),
		);
		return $sortable_columns;
	}	
	
	/**
	 * Prepare the table with different parameters, pagination, columns and table elements
	 */
	function prepare_items() {
	
		global $_wp_column_headers;
		global $bookyourtravel_accommodation_helper, $bookyourtravel_room_type_helper;
		
		$accommodation_id = isset($_GET['accommodation_id']) ? intval($_GET['accommodation_id']) : 0;
		$accommodation_id = BookYourTravel_Theme_Utils::get_default_language_post_id($accommodation_id, 'accommodation');
		
		$screen = get_current_screen();
		$user = get_current_user_id();
		$option = $screen->get_option('per_page', 'option');
		$per_page = get_user_meta($user, $option, true);
		if ( empty ( $per_page) || $per_page < 1 ) {
			$per_page = $screen->get_option( 'per_page', 'default' );
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
		$paged = !empty($_GET["paged"]) ? intval(wp_kses($_GET["paged"], array())) : 1;
		//Page Number
		if(empty($paged) || !is_numeric($paged) || $paged<=0 ) { $paged=1; }

		$author_id = null;
		if (!is_super_admin()) {
			$author_id = get_current_user_id();
		}
		
		$accommodation_vacancy_results = $bookyourtravel_accommodation_helper->list_accommodation_vacancies($accommodation_id, 0, $orderby, $order, $paged, $per_page, $author_id);
		
		//Number of elements in your table?
		$totalitems = $accommodation_vacancy_results['total']; //return the total number of affected rows

		//How many pages do we have in total?
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
		$this->items = $accommodation_vacancy_results['results'];
	}
	
	function handle_form_submit() {
	
		global $bookyourtravel_accommodation_helper, $bookyourtravel_room_type_helper;
	
		if ((isset($_POST['insert']) || isset($_POST['update'])) && check_admin_referer('bookyourtravel_nonce')) {
		
			$accommodation_id = intval(wp_kses($_POST['accommodation_id'], array()));
			$accommodation_obj = new BookYourTravel_Accommodation(intval($accommodation_id));
			$accommodation_id = $accommodation_obj->get_base_id();
			
			$room_type_obj = null;
			$room_type_id = isset($_POST['room_type_id']) ? intval(wp_kses($_POST['room_type_id'], array())) : 0;
			if ($room_type_id > 0) {
				$room_type_obj = new BookYourTravel_Room_Type(intval($room_type_id));
				$room_type_id = $room_type_obj->get_base_id();
			}
			
			$disabled_room_types = $accommodation_obj->get_disabled_room_types();
			$is_price_per_person = $accommodation_obj->get_is_price_per_person();
			
			$season_name =  sanitize_text_field($_POST['season_name']);
			$room_count = isset($_POST['room_count']) ? intval(wp_kses($_POST['room_count'], array())) : 1;
			$price_per_day = floatval(wp_kses($_POST['price_per_day'], array()));
			$price_per_day_child = isset($_POST['price_per_day_child']) ? floatval(wp_kses($_POST['price_per_day_child'], array())) : 0;
			$weekend_price_per_day = isset($_POST['weekend_price_per_day']) ? floatval(wp_kses($_POST['weekend_price_per_day'], array())) : 0;
			$weekend_price_per_day_child = isset($_POST['weekend_price_per_day_child']) ? floatval(wp_kses($_POST['weekend_price_per_day_child'], array())) : 0;

			$date_from =  sanitize_text_field($_POST['date_from']);
			$start_date = $date_from;

			$date_to =  sanitize_text_field($_POST['date_to']);
			$end_date = $date_to;
			
			if (isset($_POST['insert'])) {
				
				$error = '';
				
				if (empty ($season_name)) {
					$error = esc_html__('You must enter a season name', 'bookyourtravel');
				} else if (empty($accommodation_id)) {
					$error = esc_html__('You must select an accommodation', 'bookyourtravel');
				} else if(!$disabled_room_types && $room_type_id <= 0) {
					$error = esc_html__('You must select a room type', 'bookyourtravel');
				} else if(empty($date_from)) {
					$error = esc_html__('You must select a from date', 'bookyourtravel');
				} else if(empty($date_to)) {
					$error = esc_html__('You must select a to date', 'bookyourtravel');
				} else if(empty($price_per_day) || $price_per_day === 0) {
					$error = esc_html__('You must provide a valid price per day', 'bookyourtravel');
				}
				
				if (!empty($error)) {
					  echo '<div class="error" id="message" onclick="this.parentNode.removeChild(this)">';
					  echo '<p>' . $error . '</p>';
					  echo '</div>';
				} else {
					
					$bookyourtravel_accommodation_helper->create_accommodation_vacancy($season_name, $start_date, $end_date, $accommodation_id, $room_type_id, $room_count, $price_per_day, $price_per_day_child, $weekend_price_per_day, $weekend_price_per_day_child);
					
					echo '<div class="updated" id="message" onclick="this.parentNode.removeChild(this)">';
					echo '<p>' . esc_html__('Successfully inserted new vacancy!', 'bookyourtravel') . '</p>';
					echo '</div>';
				}
			} else if (isset($_POST['update'])) {

				$error = '';
				
				if (empty ($season_name)) {
					$error = esc_html__('You must enter a season name', 'bookyourtravel');
				} else if(empty($accommodation_id)) {
					$error = esc_html__('You must select an accommodation', 'bookyourtravel');
				} else if(!$disabled_room_types && empty($room_type_id)) {
					$error = esc_html__('You must select a room type', 'bookyourtravel');
				} else if (!$disabled_room_types && (empty($room_count) || $room_count === 0)) {
					$error = esc_html__('You must provide a valid room count', 'bookyourtravel');
				} else if(empty($date_from)) {
					$error = esc_html__('You must select a from date', 'bookyourtravel');
				} else if(empty($date_to)) {
					$error = esc_html__('You must select a to date', 'bookyourtravel');
				} else if(empty($price_per_day) || $price_per_day === 0) {
					$error = esc_html__('You must provide a valid price per day', 'bookyourtravel');
				}
				
				if (!empty($error)) {				
					  echo '<div class="error" id="message" onclick="this.parentNode.removeChild(this)">';
					  echo '<p>' . $error . '</p>';
					  echo '</div>';
				} else {
					
					$vacancy_id = absint($_POST['vacancy_id']);
					
					$bookyourtravel_accommodation_helper->update_accommodation_vacancy($vacancy_id, $season_name, $start_date, $end_date, $accommodation_id, $room_type_id, $room_count, $price_per_day, $price_per_day_child, $weekend_price_per_day, $weekend_price_per_day_child);
					
					echo '<div class="updated" id="message" onclick="this.parentNode.removeChild(this)">';
					echo '<p>' . sprintf(esc_html__('Successfully updated vacancy (id=%d)!', 'bookyourtravel'), $vacancy_id) . '</p>';
					echo '</div>';
				}
			} 
		} else if (isset($_POST['delete_vacancy']) && check_admin_referer('bookyourtravel_nonce')) {
		
			$vacancy_id = absint($_POST['delete_vacancy']);
			
			$bookyourtravel_accommodation_helper->delete_accommodation_vacancy($vacancy_id);	
			
			echo '<div class="updated" id="message" onclick="this.parentNode.removeChild(this)">';
			echo '<p>' . esc_html__('Successfully deleted vacancy!', 'bookyourtravel') . '</p>';
			echo '</div>';
		}
		
	}
	
	function render_entry_form() {

		global $bookyourtravel_accommodation_helper, $bookyourtravel_room_type_helper;
	
		$accommodation_id = 0;
		$disabled_room_types = 0;
		$vacancy_object = null;
		$accommodation_obj = null;
		$is_price_per_person = 0;
		
		$vacancy_id = isset($_GET['vacancy_id']) ? absint($_GET['vacancy_id']) : "";
		
		if (!empty($vacancy_id)) {
			$vacancy_object = $bookyourtravel_accommodation_helper->get_accommodation_vacancy($vacancy_id);
		}
		
		if (isset($_POST['accommodation_id'])) {
			$accommodation_id = intval(wp_kses($_POST['accommodation_id'], array()));
		} else if ($vacancy_object) {
			$accommodation_id = $vacancy_object->accommodation_id;
		}
		
		if ($accommodation_id) {
			$accommodation_obj = new BookYourTravel_Accommodation(intval($accommodation_id));
			$accommodation_id = $accommodation_obj->get_base_id();
			$disabled_room_types = $accommodation_obj->get_disabled_room_types();
			$is_price_per_person = $accommodation_obj->get_is_price_per_person();
		}
		
		$room_type_id = 0;
		if (isset($_POST['room_type_id'])) {
			$room_type_id = intval(wp_kses($_POST['room_type_id'], array()));
		} else if ($vacancy_object) {
			$room_type_id = $vacancy_object->room_type_id;
		}
		
		if (!empty($room_type_id)) {
			$room_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($room_type_id, 'room_type');
		}		
		
		$accommodations_select = '<select id="accommodation_id" name="accommodation_id" class="vacancy_accommodations_select">';
		$accommodations_select .= '<option value="">' . esc_html__('Select accommodation', 'bookyourtravel') . '</option>';

		$author_id = null;
		if (!is_super_admin()) {
			$author_id = get_current_user_id();
		}
		
		$accommodation_results = $bookyourtravel_accommodation_helper->list_accommodations(0, -1, 'title', 'ASC', 0, array(), array(), array(), false, null, null);
		
		if ( count($accommodation_results) > 0 && $accommodation_results['total'] > 0 ) {
			foreach ($accommodation_results['results'] as $accommodation_result) {
				global $post;				
				$post = $accommodation_result;
				setup_postdata( $post ); 			
				$accommodations_select .= '<option value="' . $post->ID . '" ' . ($post->ID == $accommodation_id ? 'selected' : '') . '>' . $post->post_title . '</option>';
			}
		}
		$accommodations_select .= '</select>';
		
		$room_types_select = '';
		
		$room_types_select = '<select class="normal" id="room_type_id" name="room_type_id" class="vacancy_room_types_select">';
		
		if (!$disabled_room_types) {

			$room_types_select .= '<option value="">' . esc_html__('Select room type', 'bookyourtravel') . '</option>';
			
			if ($accommodation_obj) { 				
				$room_type_ids = $accommodation_obj->get_room_types();				
				if ($room_type_ids && count($room_type_ids) > 0) {
					for ( $i = 0; $i < count($room_type_ids); $i++ ) {
						$temp_id = $room_type_ids[$i];
						$room_type_obj = new BookYourTravel_Room_Type(intval($temp_id));
						$room_types_select .= '<option value="' . $temp_id . '" ' . ($temp_id == $room_type_id ? 'selected' : '') . '>' . $room_type_obj->get_title() . '</option>';
					}
				}
			}
			
		}
		
		$room_types_select .= '</select>';
		
		wp_reset_postdata();
		
		$date_from = null;
		if (isset($_POST['date_from'])) {
			$date_from =  sanitize_text_field($_POST['date_from']);
		} else if ($vacancy_object) {
			$date_from = $vacancy_object->start_date;
		}
		if (isset($date_from))
			$date_from = date( $this->date_format, strtotime( $date_from ) );
			
		$date_to = null;
		if (isset($_POST['date_to'])) {
			$date_to =  sanitize_text_field($_POST['date_to']);
		} else if ($vacancy_object) {
			$date_to = $vacancy_object->end_date;
		}
		if (isset($date_to))
			$date_to = date( $this->date_format, strtotime( $date_to ) );

		$room_count = 1;
		if (isset($_POST['room_count'])) {
			$room_count = intval(wp_kses($_POST['room_count'], array()));
		} else if ($vacancy_object && isset($vacancy_object->room_count)) {
			$room_count = $vacancy_object->room_count;
		}
		if ($room_count == 0) 
			$room_count = 1;

		$price_per_day = 0;
		if (isset($_POST['price_per_day'])) {
			$price_per_day = floatval(wp_kses($_POST['price_per_day'], array()));
		} else if ($vacancy_object) {
			$price_per_day = $vacancy_object->price_per_day;
		}

		$price_per_day_child = 0;
		if (isset($_POST['price_per_day_child'])) {
			$price_per_day_child = floatval(wp_kses($_POST['price_per_day_child'], array()));
		} else if ($vacancy_object) {
			$price_per_day_child = $vacancy_object->price_per_day_child;
		}
		
		$weekend_price_per_day = 0;
		if (isset($_POST['weekend_price_per_day'])) {
			$weekend_price_per_day = floatval(wp_kses($_POST['weekend_price_per_day'], array()));
		} else if ($vacancy_object) {
			$weekend_price_per_day = $vacancy_object->weekend_price_per_day;
		}
		$weekend_price_per_day = isset($weekend_price_per_day) ? $weekend_price_per_day : 0;

		$weekend_price_per_day_child = 0;
		if (isset($_POST['weekend_price_per_day_child'])) {
			$weekend_price_per_day_child = floatval(wp_kses($_POST['weekend_price_per_day_child'], array()));
		} else if ($vacancy_object) {
			$weekend_price_per_day_child = $vacancy_object->weekend_price_per_day_child;
		}
		$weekend_price_per_day_child = isset($weekend_price_per_day_child) ? $weekend_price_per_day_child : 0;
		
		$season_name = '';
		if (isset($_POST['season_name'])) {
			$season_name = sanitize_text_field($_POST['season_name']);
		} else if ($vacancy_object) {
			$season_name = stripslashes($vacancy_object->season_name);
		}
		
		if ($vacancy_object)
			echo '<h3>' . esc_html__('Update Vacancy', 'bookyourtravel') . '</h3>';
		else
			echo '<h3>' . esc_html__('Add Vacancy', 'bookyourtravel') . '</h3>';
		
		echo '<form id="accommodation_vacancy_form" method="post" action="' . esc_url($_SERVER['REQUEST_URI']) . '" style="clear: both;">';
		
		echo wp_nonce_field('bookyourtravel_nonce');	
				
		echo '<table cellpadding="3" class="form-table"><tbody>';
				
		echo '<tr>';
		echo '	<th scope="row" valign="top">' . esc_html__('Season name', 'bookyourtravel') . '</th>';
		echo '	<td><input type="text" name="season_name" id="season_name" value="' . $season_name . '" /></td>';
		echo '</tr>';
				
		echo '<tr>';
		echo '	<th scope="row" valign="top">' . esc_html__('Select accommodation', 'bookyourtravel') . '</th>';
		echo '	<td>' . $accommodations_select . '<div class="loading" style="display:none;"></div></td>';
		echo '</tr>';
		
		echo '<tr id="room_types_row" style="display:none;" class="accommodation_selected step_0">';
		echo '	<th scope="row" valign="top">' . esc_html__('Select room type', 'bookyourtravel') . '</th>';
		echo '	<td>' . $room_types_select . '</td>';
		echo '</tr>';
		echo '<tr id="room_count_row" style="display:none;" class="accommodation_selected step_0">';
		echo '	<th scope="row" valign="top">' . esc_html__('Number of rooms', 'bookyourtravel') . '</th>';
		echo '  <td>';
		echo '  <select id="room_count" name="room_count">';
		for ($i=0;$i<20;$i++) {
			echo '  <option value="' . $i . '" ' . ($room_count == $i ? "selected" : "") . '>' . $i . '</option>';
		}
		echo '  </select>';
		
		echo '  </td>';
		echo '</tr>';
		
		echo '<tr style="display:none;" class="accommodation_selected step_1">';
		echo '	<th scope="row" valign="top">' . esc_html__('Date from', 'bookyourtravel') . '</th>';
		echo '	<td>';
		echo '		<script>';
		echo '			window.datepickerDateFromValue = "' . $date_from . '";';
		echo '  	</script>';				
		echo '  	<input class="datepicker" type="text" name="vacancy_datepicker_from" id="vacancy_datepicker_from" />';
		echo '		<input type="hidden" name="date_from" id="date_from" />';
		echo '	</td>';	
		echo '</tr>';
		
		echo '<tr style="display:none;" class="accommodation_selected step_1">';
		echo '	<th scope="row" valign="top">' . esc_html__('Date to', 'bookyourtravel') . '</th>';
		echo '	<td>';
		echo '		<script>';
		echo '			window.datepickerDateToValue = "' . $date_to . '";';
		echo '  	</script>';				
		echo '  	<input class="datepicker" type="text" name="vacancy_datepicker_to" id="vacancy_datepicker_to" />';
		echo '		<input type="hidden" name="date_to" id="date_to" />';
		echo '	</td>';	
		echo '</tr>';
		
		echo '<tr style="display:none;" class="accommodation_selected step_1">';
		echo '	<th class="th_price" scope="row" valign="top"><span class="first">' . esc_html__('Price per day', 'bookyourtravel') . '</span> <span class="per_person" ' . ($is_price_per_person ? '' : 'style="display:none"') . '>' . esc_html__('(adult)', 'bookyourtravel') . '</span></th>';
		echo '	<td><input type="text" name="price_per_day" id="price_per_day" value="' . $price_per_day . '" /></td>';
		echo '</tr>';
	
		echo '<tr class="per_person accommodation_selected step_1" style="display:none">';
		echo '	<th class="th_price_per_child" scope="row" valign="top"><span class="first">' . esc_html__('Price per day', 'bookyourtravel') . '</span> <span>' . __('(child)', 'bookyourtravel') . '</span></th>';
		echo '	<td><input type="text" name="price_per_day_child" id="price_per_day_child" value="' . $price_per_day_child . '" /></td>';
		echo '</tr>';
		
		echo '<tr style="display:none;" class="accommodation_selected daily_rent step_1">';
		echo '	<th scope="row" valign="top">' . esc_html__('Weekend price per day', 'bookyourtravel') . ' <span class="per_person" ' . ($is_price_per_person ? '' : 'style="display:none"') . '>' . esc_html__('(adult)', 'bookyourtravel') . '</span><em>' . esc_html__('Leave as 0 to not use', 'bookyourtravel') . '</em></th>';
		echo '	<td><input type="text" name="weekend_price_per_day" id="weekend_price_per_day" value="' . $weekend_price_per_day . '" /></td>';
		echo '</tr>';
	
		echo '<tr class="per_person accommodation_selected daily_rent step_1" style="display:none">';
		echo '	<th scope="row" valign="top">' . esc_html__('Weekend price per day (child)', 'bookyourtravel') . '<em>' . esc_html__('Leave as 0 to not use', 'bookyourtravel') . '</em></th>';
		echo '	<td><input type="text" name="weekend_price_per_day_child" id="weekend_price_per_day_child" value="' . $weekend_price_per_day_child . '" /></td>';
		echo '</tr>';

		echo '</table>';
		echo '<p>';
		echo '<a href="edit.php?post_type=accommodation&page=theme_accommodation_vacancy_admin.php" class="button-secondary">' . esc_html__('Cancel', 'bookyourtravel') . '</a>&nbsp;';
		if ($vacancy_object) {
			echo '<span style="display:none;" class="accommodation_selected step_1">';
			echo '<input id="vacancy_id" name="vacancy_id" value="' . $vacancy_id . '" type="hidden" />';
			echo '<input class="button-primary" type="submit" name="update" value="' . esc_html__('Update Vacancy', 'bookyourtravel') . '"/>';
			echo '</span>';
		} else {
			echo '<span style="display:none;" class="accommodation_selected step_1">';
			echo '<input class="button-primary" type="submit" name="insert" value="' . esc_html__('Add Vacancy', 'bookyourtravel') . '"/>';
			echo '</span>';
		}
		echo '</p>';

		echo '</form>';
	}
	
}