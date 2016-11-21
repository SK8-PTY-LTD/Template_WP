<?php

/*************************** LOAD THE BASE CLASS *******************************
 *******************************************************************************
 * The WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary.
 */
if(!class_exists('WP_List_Table')) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


class BookYourTravel_Cruise_Schedule_Admin extends BookYourTravel_BaseSingleton {
	
	private $enable_cruises;
	
	protected function __construct() {
	
		global $bookyourtravel_theme_globals;
		
		$this->enable_cruises = $bookyourtravel_theme_globals->enable_cruises();

        // our parent class might
        // contain shared code in its constructor
        parent::__construct();
	}

    public function init() {

		if ($this->enable_cruises) {	

			add_action( 'admin_menu' , array( $this, 'cruise_schedule_admin_page' ) );
			add_filter( 'set-screen-option', array( $this, 'cruise_schedule_set_screen_options' ), 10, 3);
			add_action( 'admin_head', array( $this, 'cruise_schedule_admin_head' ) );
		}
	}

	function cruise_schedule_admin_page() {
		$hook = add_submenu_page('edit.php?post_type=cruise', esc_html__('Cruise schedule management', 'bookyourtravel'), esc_html__('Schedule', 'bookyourtravel'), 'edit_posts', basename(__FILE__), array($this, 'cruise_schedule_admin_display'));
		add_action( "load-$hook", array($this, 'cruise_schedule_add_screen_options'));
	}

	function cruise_schedule_set_screen_options($status, $option, $value) {
		if ( 'cruise_schedule_per_page' == $option ) 
			return $value;
	}

	function cruise_schedule_admin_head() {
		$page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
		if( 'theme_cruise_schedule_admin.php' != $page )
			return;

		$this->cruise_schedule_admin_styles();
	}

	function cruise_schedule_admin_styles() {

		if (isset($_POST['start_date'])) 
			$start_date =  sanitize_text_field($_POST['start_date']);

		echo '<style type="text/css">';
		echo '	.wp-list-table .column-Id { width: 10%; }';
		echo '	.wp-list-table .column-CruiseName { width: 10%; }';
		echo '	.wp-list-table .column-CabinType { width: 10%; }';
		echo '	.wp-list-table .column-CruiseType { width: 10%; }';
		echo '	.wp-list-table .column-StartDate { width: 10%; }';
		echo '	.wp-list-table .column-EndDate { width: 10%; }';
		echo '	.wp-list-table .column-DurationDays { width: 10%; }';
		echo '	.wp-list-table .column-Action { width: 10%; }';
		echo '	.wp-list-table .column-Action { width: 10%; }';
		echo "</style>";
		
		echo '<script>';
		echo 'window.adminAjaxUrl = "' . admin_url('admin-ajax.php') . '";';
		echo 'window.datepickerDateFormat = "' . BookYourTravel_Theme_Utils::dateformat_PHP_to_jQueryUI(get_option('date_format')) . '";';
		echo 'window.datepickerAltFormat = "' . BOOKYOURTRAVEL_ALT_DATE_FORMAT . '";';
		echo '</script>';
	}

	function cruise_schedule_add_screen_options() {
		global $wp_cruise_schedule_table;
		$option = 'per_page';
		$args = array('label' => esc_html__('Schedule', 'bookyourtravel'),'default' => 50,'option' => 'cruise_schedule_per_page');
		add_screen_option( $option, $args );
		$wp_cruise_schedule_table = new Cruise_Schedule_Admin_List_Table();
	}

	function cruise_schedule_admin_display() {
	
		global $bookyourtravel_cruise_helper, $bookyourtravel_cabin_type_helper;
	
		echo '<div class="wrap">';
		echo '<h2>' . esc_html__('Cruise schedule', 'bookyourtravel') . '</h2>';

		global $wp_cruise_schedule_table;
		$wp_cruise_schedule_table->handle_form_submit();
		
		if (isset($_GET['sub']) && $_GET['sub'] == 'manage') {
		
			$wp_cruise_schedule_table->render_entry_form(); 
			
		} else {
			$year = isset($_GET['year']) ? intval($_GET['year']) : intval(date("Y"));
			$month = isset($_GET['month']) ? intval($_GET['month']) : intval(date("m"));
			$current_day = ($year == intval(date("Y")) && $month  == intval(date("m"))) ? intval(date("j")) : 0;
			$cruise_id = isset($_GET['cruise_id']) ? intval($_GET['cruise_id']) : 0;
			$cabin_type_id = isset($_GET['cabin_type_id']) ? intval($_GET['cabin_type_id']) : 0;
		
			$cruises_filter = '<select id="cruises_filter" name="cruises_filter" onchange="cruiseFilterRedirect(this.value,' . $cabin_type_id . ',' . $year . ',' . $month . ')">';
			$cruises_filter .= '<option value="">' . esc_html__('Filter by cruise', 'bookyourtravel') . '</option>';

			$author_id = null;
			if (!is_super_admin()) {
				$author_id = get_current_user_id();
			}
			
			$cruise_results = $bookyourtravel_cruise_helper->list_cruises(0, -1, 'title', 'ASC', 0, array(), array(), array(), false, $author_id);
			
			if ( count($cruise_results) > 0 && $cruise_results['total'] > 0 ) {
				foreach ($cruise_results['results'] as $cruise_result) {
					global $post;				
					$post = $cruise_result;
					setup_postdata( $post ); 
					$cruises_filter .= '<option value="' . $post->ID . '" ' . ($post->ID == $cruise_id ? 'selected' : '') . '>' . $post->post_title . '</option>';
				}
			}
			$cruises_filter .= '</select>';

			wp_reset_postdata();
			
			echo '<div class="alignleft bookyourtravel-admin-filter">';
			echo '<div class="alignleft actions">' . esc_html__('Filter by cruise: ', 'bookyourtravel') . $cruises_filter . '</div>';
			echo "<div class='alignleft actions'><a class='button-secondary action alignleft' href='edit.php?post_type=cruise&page=theme_cruise_schedule_admin.php'>";
			echo esc_html__('Reset filters', 'bookyourtravel');
			echo "</a></div>";
			echo '</div>';
			
			$wp_cruise_schedule_table->prepare_items(); 
			$wp_cruise_schedule_table->display();
		} 
	}
}

global $cruise_schedule_admin;
$cruise_schedule_admin = BookYourTravel_Cruise_Schedule_Admin::get_instance();

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
class Cruise_Schedule_Admin_List_Table extends WP_List_Table {

	private $options;
	private $lastInsertedID;
	private $date_format;
	
	/**
	* Constructor, we override the parent to pass our own arguments.
	* We use the parent reference to set some default configs.
	*/
	function __construct() {
		global $status, $page;	
		
		$this->date_format = get_option('date_format');
	
		 parent::__construct( array(
			'singular'=> 'schedule', // Singular label
			'plural' => 'schedule', // plural label, also this well be one of the table css class
			'ajax'	=> false // We won't support Ajax for this table
		) );
		
	}	

	function column_default( $item, $column_name ) {
		return $item->$column_name;
	}
	
	function column_SeasonName($item) {
		return $item->season_name;	
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
			$year = isset($_GET['year']) ? intval($_GET['year']) : 0;
			$month = isset($_GET['month']) ? intval($_GET['month']) : 0;
			$day = isset($_GET['day']) ? intval($_GET['day']) : 1;
			$cruise_id = isset($_GET['cruise_id']) ? intval($_GET['cruise_id']) : 0;
			
			$cruise_title = '';
			if ($cruise_id > 0)
				$cruise_title = get_the_title($cruise_id);
			?>
			<div class="alignleft actions bookyourtravel-admin-top">
				<a href="edit.php?post_type=cruise&page=theme_cruise_schedule_admin.php&sub=manage" class="button-secondary action" ><?php esc_html_e('Add schedule', 'bookyourtravel') ?></a>
			</div>
			<?php
		}
		if ( $which == "bottom" ) {
			//The code that goes after the table is there
			?>
			<div class="alignleft actions bookyourtravel-admin-top">
				<a href="edit.php?post_type=cruise&page=theme_cruise_schedule_admin.php&sub=manage" class="button-secondary action" ><?php esc_html_e('Add schedule', 'bookyourtravel') ?></a>
			</div>
			<?php
		}
	}		
	
	function column_CruiseName($item) {
		return $item->cruise_name;	
	}
	
	function column_CabinType($item) {
		return $item->cabin_type;	
	}
	
	function column_DurationDays($item) {
		return $item->duration_days;	
	}
	
	function column_CruiseType($item) {
		$cruise_obj = new BookYourTravel_Cruise($item->cruise_id);
		return $cruise_obj->get_type_name();
	}
	
	function column_Price($item) {
		if ($item->cruise_is_price_per_person)
			return $item->price . ' / ' . $item->price_child;	
		else
			return $item->price;
	}
	
	function column_CabinCount($item) {
		return $item->cabin_count;	
	}
	
	function column_StartDate($item) {
		return date($this->date_format, strtotime($item->start_date));	
	}
	
	function column_EndDate($item) {
		if ($item->end_date != null) {
			$year = date('Y', strtotime($item->end_date));
			if ($year > 1970) {
				return date($this->date_format, strtotime($item->end_date));
			} 
		}
		return esc_html__('N/A', 'bookyourtravel');
	}
	
	function column_Action($item) {
		if (!$item->has_bookings) {
		
			$cruise_id = isset($_GET['cruise_id']) ? intval($_GET['cruise_id']) : 0;
			$cabin_type_id = isset($_GET['cabin_type_id']) ? intval($_GET['cabin_type_id']) : 0;
			
			$url_part = '';
			if ($cruise_id > 0)
				$url_part .= "&cruise_id=$cruise_id";
			if ($cabin_type_id > 0)
				$url_part .= "&cabin_type_id=$cabin_type_id";
		
			$action = "<form method='post' name='delete_schedule_" . $item->Id . "' id='delete_schedule_" . $item->Id . "' style='display:inline;'>
						<input type='hidden' name='delete_schedule' id='delete_schedule' value='" . $item->Id . "' />
						<a href='javascript: void(0);' onclick='confirmDelete(\"#delete_schedule_" . $item->Id . "\", \"" . esc_html__('Are you sure?', 'bookyourtravel') . "\");'>" . esc_html__('Delete', 'bookyourtravel') . "</a>
					</form>";
					
			$action .= ' | 	<a href="edit.php?post_type=cruise&page=theme_cruise_schedule_admin.php&sub=manage&edit=' . $item->Id . $url_part . '">' . esc_html__('Edit', 'bookyourtravel') . '</a>';
			return $action;
		}
		return "";
	}	
	
	/**
	 * Define the columns that are going to be used in the table
	 * @return array $columns, the array of columns to use with the table
	 */
	function get_columns() {
		return $columns= array(
			'Id'=>esc_html__('Id', 'bookyourtravel'),
			'SeasonName'=>esc_html__('Season Name', 'bookyourtravel'),
			'StartDate'=>esc_html__('Start Date', 'bookyourtravel'),
			'EndDate'=>esc_html__('End Date', 'bookyourtravel'),
			'CruiseName'=>esc_html__('Cruise Name', 'bookyourtravel'),
			'CabinType'=>esc_html__('Cabin Type', 'bookyourtravel'),
			'CruiseType'=>esc_html__('Cruise Type', 'bookyourtravel'),
			'DurationDays'=>esc_html__('Days', 'bookyourtravel'),
			'Price'=>esc_html__('Price', 'bookyourtravel'),
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
			'CruiseName'=> array( 'cruises.post_title', true ),
			'CabinType'=> array( 'cabin_types.post_title', true ),
			'StartDate'=> array( 'start_date', true ),
			'DurationDays'=> array( 'duration_days', true ),
			'CabinCount'=> array( 'cabin_count', true ),
			'Price'=> array( 'price', true ),
		);
		return $sortable_columns;
	}	
	
	/**
	 * Prepare the table with different parameters, pagination, columns and table elements
	 */
	function prepare_items() {
		
		global $bookyourtravel_cruise_helper, $bookyourtravel_cabin_type_helper;
		global $_wp_column_headers;
		
		$year = isset($_GET['year']) ? intval($_GET['year']) : 0;
		$month = isset($_GET['month']) ? intval($_GET['month']) : 0;
		$day = isset($_GET['day']) ? intval($_GET['day']) : 0;
		
		$cruise_id = isset($_GET['cruise_id']) ? intval($_GET['cruise_id']) : 0;
		$cruise_id = BookYourTravel_Theme_Utils::get_default_language_post_id($cruise_id, 'cruise');
		
		$cabin_type_id = isset($_GET['cabin_type_id']) ? intval($_GET['cabin_type_id']) : 0;
		$cabin_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($cabin_type_id, 'cabin_type');
		
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
		$paged = !empty($_GET["paged"]) ? intval(wp_kses($_GET["paged"], array())) : 1;
		//Page Number
		if(empty($paged) || !is_numeric($paged) || $paged<=0 ) { $paged=1; }
		
		$author_id = null;
		if (!is_super_admin()) {
			$author_id = get_current_user_id();
		}
		
		$cruise_schedule_results = $bookyourtravel_cruise_helper->list_cruise_schedules($paged, $per_page, $orderby, $order, $day, $month, $year, $cruise_id, $cabin_type_id, $search_term, $author_id);

		//Number of elements in your table?
		$totalitems = $cruise_schedule_results['total']; //return the total number of affected rows

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
		$this->items = $cruise_schedule_results['results'];
	}
	
	function handle_form_submit() {
		
		global $bookyourtravel_cruise_helper, $bookyourtravel_cabin_type_helper;
		
		if (isset($_POST['insert']) && check_admin_referer('bookyourtravel_nonce')) {
			
			$cruise_id = intval(wp_kses($_POST['cruises_select'], array()));			
			$cruise_obj = new BookYourTravel_Cruise(intval($cruise_id));
			$cruise_id = $cruise_obj->get_base_id();

			$season_name =  sanitize_text_field($_POST['season_name']);
			
			$cabin_type_id = isset($_POST['cabin_types_select']) ? intval(wp_kses($_POST['cabin_types_select'], array())) : 0;
			$cabin_type_obj = new BookYourTravel_Cabin_Type(intval($cabin_type_id));
			$cabin_type_id = $cabin_type_obj->get_base_id();
			
			$cabin_count = isset($_POST['cabin_count']) ? intval(wp_kses($_POST['cabin_count'], array())) : 1;
			$is_price_per_person = $cruise_obj->get_is_price_per_person();
			$cruise_type_is_repeated = $cruise_obj->get_type_is_repeated();
			
			$start_date =  sanitize_text_field($_POST['start_date']);
			$duration_days = intval(wp_kses($_POST['duration_days'], array()));
			$price = floatval(wp_kses($_POST['price'], array()));
			$price_child = isset($_POST['price_child']) ? floatval(wp_kses($_POST['price_child'], array())) : 0;
			$end_date = isset($_POST['end_date']) ? sanitize_text_field($_POST['end_date']) : null;
			
			$error = '';
			
			if (empty ($season_name)) {
				$error = esc_html__('You must enter a season name', 'bookyourtravel');
			} else if(empty($cruise_id)) {
				$error = esc_html__('You must select an cruise', 'bookyourtravel');
			} else if($cabin_type_id <= 0) {
				$error = esc_html__('You must select a cabin type', 'bookyourtravel');
			} else if (empty($cabin_count) || $cabin_count === 0) {
				$error = esc_html__('You must provide a valid cabin count', 'bookyourtravel');
			} else if(empty($start_date)) {
				$error = esc_html__('You must select a schedule date', 'bookyourtravel');
			} else if(empty($duration_days) || $duration_days === 0) {
				$error = esc_html__('You must provide a duration in days', 'bookyourtravel');
			} else if(empty($price) || $price === 0) {
				$error = esc_html__('You must provide a valid price', 'bookyourtravel');
			}
			
			if (!empty($error)) {
				  echo '<div class="error" id="message" onclick="this.parentNode.removeChild(this)">';
				  echo '<p>' . $error . '</p>';
				  echo '</div>';
			} else {
				
				$bookyourtravel_cruise_helper->create_cruise_schedule($season_name, $cruise_id, $cabin_type_id, $cabin_count, $start_date, $duration_days, $price, $price_child, $end_date);
				
				echo '<div class="updated" id="message" onclick="this.parentNode.removeChild(this)">';
				echo '<p>' . esc_html__('Successfully inserted new cruise schedule entry!', 'bookyourtravel') . '</p>';
				echo '</div>';

			}
		} else if (isset($_POST['update']) && check_admin_referer('bookyourtravel_nonce')) {

			$cruise_id = intval(wp_kses($_POST['cruises_select'], array()));			
			$cruise_obj = new BookYourTravel_Cruise(intval($cruise_id));
			$cruise_id = $cruise_obj->get_base_id();

			$cabin_type_id = isset($_POST['cabin_types_select']) ? intval(wp_kses($_POST['cabin_types_select'], array())) : 0;
			$cabin_type_obj = new BookYourTravel_Cabin_Type(intval($cabin_type_id));
			$cabin_type_id = $cabin_type_obj->get_base_id();
		
			$season_name =  sanitize_text_field($_POST['season_name']);		
		
			$is_price_per_person = $cruise_obj->get_is_price_per_person();
			$cabin_count = isset($_POST['cabin_count']) ? intval(wp_kses($_POST['cabin_count'], array())) : 1;
			$cruise_type_is_repeated = $cruise_obj->get_type_is_repeated();

			$start_date =  sanitize_text_field($_POST['start_date']);
			$duration_days = intval(wp_kses($_POST['duration_days'], array()));
			$price = floatval(wp_kses($_POST['price'], array()));
			$price_child = isset($_POST['price_child']) ? floatval(wp_kses($_POST['price_child'], array())) : 0;
			$end_date = isset($_POST['end_date']) ? wp_kses($_POST['end_date'], array()) : null;
			
			$error = '';
			
			if (empty ($season_name)) {
				$error = esc_html__('You must enter a season name', 'bookyourtravel');
			} else if(empty($cruise_id)) {
				$error = esc_html__('You must select an cruise', 'bookyourtravel');
			} else if($cabin_type_id <= 0) {
				$error = esc_html__('You must select a cabin type', 'bookyourtravel');
			} else if (empty($cabin_count) || $cabin_count === 0) {
				$error = esc_html__('You must provide a valid cabin count', 'bookyourtravel');
			} else if(empty($start_date)) {
				$error = esc_html__('You must select a schedule date', 'bookyourtravel');
			} else if(empty($duration_days) || $duration_days === 0) {
				$error = esc_html__('You must provide a duration in days', 'bookyourtravel');
			} else if(empty($price) || $price === 0) {
				$error = esc_html__('You must provide a valid price', 'bookyourtravel');
			}
			
			if (!empty($error)) {
				  echo '<div class="error" id="message" onclick="this.parentNode.removeChild(this)">';
				  echo '<p>' . $error . '</p>';
				  echo '</div>';
			} else {
				
				$schedule_id = absint($_POST['schedule_id']);
				
				$bookyourtravel_cruise_helper->update_cruise_schedule($schedule_id, $season_name, $cruise_id, $cabin_type_id, $cabin_count, $start_date, $duration_days, $price, $price_child, $end_date);
				
				echo '<div class="updated" id="message" onclick="this.parentNode.removeChild(this)">';
				echo '<p>' . esc_html__('Successfully updated cruise schedule entry!', 'bookyourtravel') . '</p>';
				echo '</div>';

			}
		
		} else if (isset($_POST['delete_schedule'])) {
			$schedule_id = absint($_POST['delete_schedule']);
			
			$bookyourtravel_cruise_helper->delete_cruise_schedule($schedule_id);
			
			echo '<div class="updated" id="message" onclick="this.parentNode.removeChild(this)">';
			echo '<p>' . esc_html__('Successfully deleted cruise schedule entry!', 'bookyourtravel') . '</p>';
			echo '</div>';
		}
		
	}
	
	function render_entry_form() {

		global $bookyourtravel_cruise_helper;
		
		$cruise_id = 0;		
		$cabin_type_id = 0;		
		$schedule_object = null;
		$cruise_obj = null;
		$cabin_type_obj = null;
		$is_price_per_person = 0;
		$cruise_type_is_repeated = 0; // on-off cruise by default
		
		$edit = isset($_GET['edit']) ? absint($_GET['edit']) : "";
		
		if (!empty($edit)) {
			$schedule_object = $bookyourtravel_cruise_helper->get_cruise_schedule($edit);
		}
		
		if (isset($_POST['cruises_select'])) {
			$cruise_id = intval(wp_kses($_POST['cruises_select'], array()));
		} else if ($schedule_object != null) {
			$cruise_id = $schedule_object->cruise_id;
		}
		
		if ($cruise_id) {
			$cruise_obj = new BookYourTravel_Cruise(intval($cruise_id));
			$cruise_id = $cruise_obj->get_base_id();
			$cruise_type_is_repeated = $cruise_obj->get_type_is_repeated();
			$is_price_per_person = $cruise_obj->get_is_price_per_person();
		}

		if (isset($_POST['cabin_types_select'])) {
			$cabin_type_id = intval(wp_kses($_POST['cabin_types_select'], array()));
		} else if ($schedule_object) {
			$cabin_type_id = $schedule_object->cabin_type_id;
		}
		
		if (!empty($cabin_type_id)) {
			$cabin_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($cabin_type_id, 'cabin_type');
		}		

		$cruises_select = '<select id="cruises_select" name="cruises_select">';
		$cruises_select .= '<option value="">' . esc_html__('Select cruise', 'bookyourtravel') . '</option>';

		$author_id = null;
		if (!is_super_admin()) {
			$author_id = get_current_user_id();
		}
		
		$cruise_results = $bookyourtravel_cruise_helper->list_cruises(0, -1, 'title', 'ASC', 0, array(), array(), array(), false, $author_id);
		if ( count($cruise_results) > 0 && $cruise_results['total'] > 0 ) {
			foreach ($cruise_results['results'] as $cruise_result) {
				global $post;				
				$post = $cruise_result;
				setup_postdata( $post ); 
				$cruises_select .= '<option value="' . $post->ID . '" ' . ($post->ID == $cruise_id ? 'selected' : '') . '>' . $post->post_title . '</option>';
			}
		}
		$cruises_select .= '</select>';
		
		$cabin_types_select = '<select class="normal" id="cabin_types_select" name="cabin_types_select">';
		$cabin_types_select .= '<option value="">' . esc_html__('Select cabin type', 'bookyourtravel') . '</option>';
		
		if ($cruise_obj) { 				
			$cabin_type_ids = $cruise_obj->get_cabin_types();				
			if ($cabin_type_ids && count($cabin_type_ids) > 0) {
				for ( $i = 0; $i < count($cabin_type_ids); $i++ ) {
					$temp_id = $cabin_type_ids[$i];
					$cabin_type_obj = new BookYourTravel_Cabin_Type(intval($temp_id));
					$cabin_types_select .= '<option value="' . $temp_id . '" ' . ($temp_id == $cabin_type_id ? 'selected' : '') . '>' . $cabin_type_obj->get_title() . '</option>';
				}
			}
		}
		
		$cabin_types_select .= '</select>';
		
		$start_date = null;
		if (isset($_POST['start_date']))
			$start_date =  sanitize_text_field($_POST['start_date']);
		else if ($schedule_object != null) {
			$start_date = $schedule_object->start_date;
		}
		if (isset($start_date))
			$start_date = date( $this->date_format, strtotime( $start_date ) );
		
		$duration_days = 0;
		if (isset($_POST['duration_days']))
			$duration_days = intval(wp_kses($_POST['duration_days'], array()));
		else if ($schedule_object != null) {
			$duration_days = $schedule_object->duration_days;
		}			
			
		$cabin_count = 1;
		if (isset($_POST['cabin_count'])) {
			$cabin_count = intval(wp_kses($_POST['cabin_count'], array()));
		} else if ($schedule_object && isset($schedule_object->cabin_count)) {
			$cabin_count = $schedule_object->cabin_count;
		}
		if ($cabin_count == 0) 
			$cabin_count = 1;
			
		$price = 0;
		if (isset($_POST['price']))
			$price = floatval(wp_kses($_POST['price'], array()));
		else if ($schedule_object != null) {
			$price = $schedule_object->price;
		}
		
		$price_child = 0;
		if ($is_price_per_person) {
			if (isset($_POST['price_child']))
				$price_child = floatval(wp_kses($_POST['price_child'], array()));
			else if ($schedule_object != null) {
				$price_child = $schedule_object->price_child;
			}
		}
		
		$end_date = null;
		if (isset($_POST['end_date']))
			$end_date =  sanitize_text_field($_POST['end_date']);
		else if ($schedule_object != null) {
			$end_date = $schedule_object->end_date;
		}
		if (isset($end_date))
			$end_date = date( $this->date_format, strtotime( $end_date ) );
		
		$season_name = '';
		if (isset($_POST['season_name'])) {
			$season_name = sanitize_text_field($_POST['season_name']);
		} else if ($schedule_object) {
			$season_name = stripslashes($schedule_object->season_name);
		}
		
		if ($schedule_object)
			echo '<h3>' . esc_html__('Update Cruise Schedule Entry', 'bookyourtravel') . '</h3>';
		else
			echo '<h3>' . esc_html__('Add Cruise Schedule Entry', 'bookyourtravel') . '</h3>';

		echo '<form id="cruise_schedule_entry_form" method="post" action="' . esc_url($_SERVER['REQUEST_URI']) . '" style="clear: both;">';
		echo wp_nonce_field('bookyourtravel_nonce');	
		echo '<table cellpadding="3" class="form-table"><tbody>';
		
		echo '<tr>';
		echo '	<th scope="row" valign="top">' . esc_html__('Season name', 'bookyourtravel') . '</th>';
		echo '	<td><input type="text" name="season_name" id="season_name" value="' . $season_name . '" /></td>';
		echo '</tr>';
		
		echo '<tr>';
		echo '	<th scope="row" valign="top">' . esc_html__('Select cruise', 'bookyourtravel') . '</th>';
		echo '	<td>' . $cruises_select . '</td>';
		echo '</tr>';
		
		echo '<tr>';
		echo '	<th scope="row" valign="top">' . esc_html__('Select cabin type', 'bookyourtravel') . '</th>';
		echo '	<td>' . $cabin_types_select . '</td>';
		echo '</tr>';
		
		echo '<tr id="cabin_count">';
		echo '	<th scope="row" valign="top">' . esc_html__('Number of cabins available', 'bookyourtravel') . '</th>';
		echo '	<td><input type="text" name="cabin_count" id="cabin_count" value="' . $cabin_count . '" /></td>';
		echo '</tr>';
		
		echo '<tr>';
		echo '	<th scope="row" valign="top">' . esc_html__('Duration (days)', 'bookyourtravel') . '</th>';
		echo '	<td><input type="text" name="duration_days" id="duration_days" value="' . $duration_days . '" /></td>';
		echo '</tr>';
		
		echo '<tr>';
		echo '	<th scope="row" valign="top">' . esc_html__('Start date', 'bookyourtravel') . '</th>';
		echo '	<td>';
		echo '		<script>';
		echo '			window.datepickerStartDateValue = ' . json_encode($start_date) . ';';
		echo '  	</script>';				
		echo '  	<input class="datepicker" type="text" name="datepicker_start_date" id="datepicker_start_date" />';
		echo '		<input type="hidden" name="start_date" id="start_date" />';
		echo '	</td>';	
		echo '</tr>';

		echo '<tr class="is_repeated" ' . ($cruise_type_is_repeated ? '' : 'style="display:none"') . '>';
		echo '	<th scope="row" valign="top">' . esc_html__('End date', 'bookyourtravel') . '</th>';
		echo '	<td>';
		echo '		<script>';
		echo '			window.datepickerEndDateValue = ' .  json_encode($end_date) . ';';
		echo '  	</script>';				
		echo '  	<input class="datepicker" type="text" name="datepicker_end_date" id="datepicker_end_date" />';
		echo '		<input type="hidden" name="end_date" id="end_date" />';
		echo '	</td>';
		echo '</tr>';
		
		echo '<tr>';
		echo '	<th scope="row" valign="top">' . esc_html__('Price', 'bookyourtravel') . ' <span class="per_person" ' . ($is_price_per_person ? '' : 'style="display:none"') . '>' . esc_html__('per adult', 'bookyourtravel') . '</span> <span class="per_person" ' . (!$is_price_per_person ? '' : 'style="display:none"') . '>' . esc_html__('per cabin', 'bookyourtravel') . '</span></th>';
		echo '	<td><input type="text" name="price" id="price" value="' . $price . '" /></td>';
		echo '</tr>';

		echo '<tr class="per_person" ' . ($is_price_per_person ? '' : 'style="display:none"') . '>';		
		echo '	<th scope="row" valign="top">' . esc_html__('Price per child', 'bookyourtravel') . '</th>';
		echo '	<td><input type="text" name="price_child" id="price_child" value="' . $price_child . '" /></td>';
		echo '</tr>';
		
		echo '</table>';
		echo '<p>';
		echo '<a href="edit.php?post_type=cruise&page=theme_cruise_schedule_admin.php" class="button-secondary">' . esc_html__('Cancel', 'bookyourtravel') . '</a>&nbsp;';
		if ($schedule_object) {
			echo '<input id="schedule_id" name="schedule_id" value="' . $edit . '" type="hidden" />';
			echo '<input class="button-primary" type="submit" name="update" value="' . esc_html__('Update Cruise Schedule Entry', 'bookyourtravel') . '"/>';
		} else {
			echo '<input class="button-primary" type="submit" name="insert" value="' . esc_html__('Add Cruise Schedule Entry', 'bookyourtravel') . '"/>';
		}
		
		echo '</p>';
		
		echo '</form>';
	}

}