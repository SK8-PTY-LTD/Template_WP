<?php

class BookYourTravel_Theme_Meta_Boxes extends BookYourTravel_BaseSingleton {

	private $enabled_frontend_content_types;
	private $enable_accommodations;
	private $enable_tours;
	private $enable_cruises;
	private $enable_car_rentals;
	
	private $user_register_custom_meta_fields;
	private $user_register_meta_box;
	private $user_login_custom_meta_fields;
	private $user_login_meta_box;	
	private $frontend_submit_custom_meta_fields;
	private $frontend_submit_meta_box;
	private $user_account_custom_meta_fields;
	private $user_account_meta_box;
	private $user_content_list_custom_meta_fields;
	private $user_content_list_meta_box;
	private $page_sidebars_custom_meta_fields;
	private $page_sidebars_meta_box;
	
	protected function __construct() {
	
		global $bookyourtravel_theme_globals;
		
		$this->enable_accommodations = $bookyourtravel_theme_globals->enable_accommodations();	
		$this->enable_tours = $bookyourtravel_theme_globals->enable_tours();	
		$this->enable_cruises = $bookyourtravel_theme_globals->enable_cruises();	
		$this->enable_car_rentals = $bookyourtravel_theme_globals->enable_car_rentals();	
		$this->enabled_frontend_content_types = array();

        // our parent class might
        // contain shared code in its constructor
        parent::__construct();
    }

    public function init() {
	
		add_action( 'admin_init', array($this, 'pages_meta_box_admin_init' ) );	
    }
	
	function pages_meta_box_admin_init() {	

		if ($this->enable_accommodations) {
			$this->enabled_frontend_content_types[] = array('value' => 'accommodation', 'label' => esc_html__('Accommodations', 'bookyourtravel'));
			$this->enabled_frontend_content_types[] = array('value' => 'room_type', 'label' => esc_html__('Room types', 'bookyourtravel'));
			$this->enabled_frontend_content_types[] = array('value' => 'vacancy', 'label' => esc_html__('Accommodation vacancies', 'bookyourtravel'));
			$this->enabled_frontend_content_types[] = array('value' => 'accommodation_booking', 'label' => esc_html__('Accommodation bookings', 'bookyourtravel'));
		}
		
		if ($this->enable_tours) {
			$this->enabled_frontend_content_types[] = array('value' => 'tour', 'label' => esc_html__('Tours', 'bookyourtravel'));
			$this->enabled_frontend_content_types[] = array('value' => 'tour_schedule', 'label' => esc_html__('Tour schedules', 'bookyourtravel'));
			$this->enabled_frontend_content_types[] = array('value' => 'tour_booking', 'label' => esc_html__('Tour bookings', 'bookyourtravel'));
		}
		
		if ($this->enable_cruises) {
			$this->enabled_frontend_content_types[] = array('value' => 'cruise', 'label' => esc_html__('Cruises', 'bookyourtravel'));
			$this->enabled_frontend_content_types[] = array('value' => 'cabin_type', 'label' => esc_html__('Cabin types', 'bookyourtravel'));
			$this->enabled_frontend_content_types[] = array('value' => 'cruise_schedule', 'label' => esc_html__('Cruise schedules', 'bookyourtravel'));
			$this->enabled_frontend_content_types[] = array('value' => 'cruise_booking', 'label' => esc_html__('Cruise bookings', 'bookyourtravel'));
		}
		
		if ($this->enable_car_rentals) {
			$this->enabled_frontend_content_types[] = array('value' => 'car_rental', 'label' => esc_html__('Car rentals', 'bookyourtravel'));		
			$this->enabled_frontend_content_types[] = array('value' => 'car_rental_booking', 'label' => esc_html__('Car rental bookings', 'bookyourtravel'));	
		}
		
		$pages = get_pages(); 
		$pages_array = array();
		$pages_array[] = array('value' => '', 'label' => esc_html__('Select page', 'bookyourtravel'));
		foreach ( $pages as $page ) {
			$pages_array[] = array('value' => $page->ID, 'label' => $page->post_title);
		}		
		
		$page_sidebars = array();	
		$page_sidebars[] = array('value' => '', 'label' => esc_html__('No sidebar', 'bookyourtravel'));
		$page_sidebars[] = array('value' => 'left', 'label' => esc_html__('Left sidebar', 'bookyourtravel'));
		$page_sidebars[] = array('value' => 'right', 'label' => esc_html__('Right sidebar', 'bookyourtravel'));
		$page_sidebars[] = array('value' => 'both', 'label' => esc_html__('Left and right sidebars', 'bookyourtravel'));
		
		$this->page_sidebars_custom_meta_fields = array(
			array( // Taxonomy Select box
				'label'	=> esc_html__('Select sidebar positioning', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'page_sidebar_positioning', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'select', // type of field
				'options' => $page_sidebars
			)
		);		
		
		$this->user_login_custom_meta_fields = array(
			array( // Post ID select box
				'label'	=> esc_html__('Redirect to after login override?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('Select a page from this dropdown if you want to override the "Redirect to after login" setting set in Theme options -> Page settings for this page.', 'bookyourtravel'), // description
				'id'	=> 'user_login_redirect_to_after_login', // field id and name
				'type'	=> 'select', // type of field
				'options' => $pages_array				
			)
		);		
		
		$this->user_register_custom_meta_fields = array(
			array( // Post ID select box
				'label'	=> esc_html__('Users can front-end submit?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('Check this box if users registering through this form can use the frontend submit pages to submit content.', 'bookyourtravel'), // description
				'id'	=> 'user_register_can_frontend_submit', // field id and name
				'type'	=> 'checkbox', // type of field
			)
		);
				
		$this->frontend_submit_custom_meta_fields = array(
			array( // Taxonomy Select box
				'label'	=> esc_html__('Content type', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'frontend_submit_content_type', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'select', // type of field
				'options' => $this->enabled_frontend_content_types
			)
		);
		
		$this->user_content_list_custom_meta_fields = array(
			array( // Select box
				'label'	=> esc_html__('User content type', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'user_content_type', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'select', // type of field
				'options' => $this->enabled_frontend_content_types
			),
		);
		
		$this->user_account_custom_meta_fields = array(
			array( // Post ID select box
				'label'	=> esc_html__('Is partner page?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will display partner (front end submit) pages and menus', 'bookyourtravel'), // description
				'id'	=> 'user_account_is_partner_page', // field id and name
				'type'	=> 'checkbox', // type of field
			)
		);
	
		$this->user_register_meta_box = new custom_add_meta_box( 'user_register_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->user_register_custom_meta_fields, 'page' );		
		remove_action( 'add_meta_boxes', array( $this->user_register_meta_box, 'add_box' ) );
		add_action('add_meta_boxes', array( $this, 'user_register_add_meta_boxes') );
		
		$this->user_login_meta_box = new custom_add_meta_box( 'user_login_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->user_login_custom_meta_fields, 'page' );		
		remove_action( 'add_meta_boxes', array( $this->user_login_meta_box, 'add_box' ) );
		add_action('add_meta_boxes', array( $this, 'user_login_add_meta_boxes') );		
	
		$this->frontend_submit_meta_box = new custom_add_meta_box( 'frontend_submit_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->frontend_submit_custom_meta_fields, 'page' );		
		remove_action( 'add_meta_boxes', array( $this->frontend_submit_meta_box, 'add_box' ) );
		add_action('add_meta_boxes', array( $this, 'frontend_submit_add_meta_boxes' ) );

		$this->page_sidebars_meta_box = new custom_add_meta_box( 'page_sidebars_custom_meta_fields', esc_html__('Sidebar selection', 'bookyourtravel'), $this->page_sidebars_custom_meta_fields, 'page' );		
		remove_action( 'add_meta_boxes', array( $this->page_sidebars_meta_box, 'add_box' ) );
		add_action('add_meta_boxes', array( $this, 'page_sidebar_add_meta_boxes' ) );

		$this->user_account_meta_box = new custom_add_meta_box( 'user_account_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->user_account_custom_meta_fields, 'page' );	
		remove_action( 'add_meta_boxes', array( $this->user_account_meta_box, 'add_box' ) );
		add_action('add_meta_boxes', array( $this, 'user_account_add_meta_boxes' ) );
		
		$this->user_content_list_meta_box = new custom_add_meta_box( 'user_content_list_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->user_content_list_custom_meta_fields, 'page' );	
		remove_action( 'add_meta_boxes', array( $this->user_content_list_meta_box, 'add_box' ) );
		add_action('add_meta_boxes', array( $this, 'user_content_list_add_meta_boxes' ) );

	}
	
	function user_account_add_meta_boxes() {
	
		global $post;
		$template_file = get_post_meta($post->ID,'_wp_page_template',true);
		if ($template_file == 'page-user-account.php') {
			add_meta_box( $this->user_account_meta_box->id, $this->user_account_meta_box->title, array( $this->user_account_meta_box, 'meta_box_callback' ), 'page', 'normal', 'high' );
		}
	}	
	
	function user_content_list_add_meta_boxes() {
	
		global $post;
		$template_file = get_post_meta($post->ID,'_wp_page_template',true);
		if ($template_file == 'page-user-content-list.php') {
			add_meta_box( $this->user_content_list_meta_box->id, $this->user_content_list_meta_box->title, array( $this->user_content_list_meta_box, 'meta_box_callback' ), 'page', 'normal', 'high' );
		}
	}
		
	function page_sidebar_add_meta_boxes() {
		global $post;
		$template_file = get_post_meta($post->ID,'_wp_page_template',true);
		if ((!function_exists('is_woocommerce') || (function_exists('is_woocommerce') && !is_woocommerce())) &&
			$template_file != 'page-contact.php' && 
			$template_file != 'page-user-register.php' && 
			$template_file != 'page-user-login.php' && 
			$template_file != 'page-user-forgot-pass.php' &&
			$template_file != 'page-contact-form-7.php') {
			add_meta_box( $this->page_sidebars_meta_box->id, $this->page_sidebars_meta_box->title, array( $this->page_sidebars_meta_box, 'meta_box_callback' ), 'page', 'normal', 'high' );
		}
	}
		
	function user_register_add_meta_boxes() {
		global $post;
		$template_file = get_post_meta($post->ID,'_wp_page_template',true);
		if ($template_file == 'page-user-register.php') {
			add_meta_box( $this->user_register_meta_box->id, $this->user_register_meta_box->title, array( $this->user_register_meta_box, 'meta_box_callback' ), 'page', 'normal', 'high' );
		}
	}

	function user_login_add_meta_boxes() {
		global $post;
		$template_file = get_post_meta($post->ID,'_wp_page_template',true);
		if ($template_file == 'page-user-login.php') {
			add_meta_box( $this->user_login_meta_box->id, $this->user_login_meta_box->title, array( $this->user_login_meta_box, 'meta_box_callback' ), 'page', 'normal', 'high' );
		}
	}	
		
	function frontend_submit_add_meta_boxes() {
		global $post;
		$template_file = get_post_meta($post->ID,'_wp_page_template',true);
		if ($template_file == 'page-user-submit-content.php') {
			add_meta_box( $this->frontend_submit_meta_box->id, $this->frontend_submit_meta_box->title, array( $this->frontend_submit_meta_box, 'meta_box_callback' ), 'page', 'normal', 'high' );
		}
	}
	
}

global $bookyourtravel_theme_meta_boxes;
// store the instance in a variable to be retrieved later and call init
$bookyourtravel_theme_meta_boxes = BookYourTravel_Theme_Meta_Boxes::get_instance();
$bookyourtravel_theme_meta_boxes->init();