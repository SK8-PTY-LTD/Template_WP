<?php

class BookYourTravel_Cabin_Type_Helper extends BookYourTravel_BaseSingleton {

	private $enable_cruises;
	private $cabin_type_custom_meta_fields;

	protected function __construct() {
	
		global $bookyourtravel_theme_globals;
		
		$this->enable_cruises = $bookyourtravel_theme_globals->enable_cruises();

        // our parent class might
        // contain shared code in its constructor
        parent::__construct();		
    }
	
	public function get_min_adult_count() {
		$min_adult_count = $this->get_custom_field( 'min_count' );
		return isset($min_adult_count) && $min_adult_count > 1 ? $min_adult_count : 1;
	}
	
	public function get_max_adult_count() {
		$max_adult_count = $this->get_custom_field( 'max_count' );
		return isset($max_adult_count) && $max_adult_count > 0 ? $max_adult_count : 0;
	}
	
	public function get_min_child_count() {
		$min_child_count = $this->get_custom_field( 'min_child_count' );
		return isset($min_child_count) && $min_child_count > 0 ? $min_child_count : 0;
	}
	
	public function get_max_child_count() {
		$max_child_count = $this->get_custom_field( 'max_child_count' );
		return isset($max_child_count) && $max_child_count > 0 ? $max_child_count : 0;
	}
	
    public function init() {

		add_action('bookyourtravel_initialize_post_types', array( $this, 'initialize_post_type' ), 0);
	
		if ($this->enable_cruises) {
		
			add_action( 'admin_init', array( $this, 'cabin_type_admin_init' ) );
		}
	}

	function initialize_post_type() {

		global $bookyourtravel_theme_globals;	
		$this->enable_cruises = $bookyourtravel_theme_globals->enable_cruises();
	
		if ($this->enable_cruises) {
			$this->register_cabin_type_post_type();
		}
	}
	
	function cabin_type_admin_init() {
			
		if ($this->enable_cruises) {
					
			$this->cabin_type_custom_meta_fields = array(
				array(
					'label'	=> esc_html__('Max adult count', 'bookyourtravel'),
					'desc'	=> esc_html__('How many adults are allowed in the cabin?', 'bookyourtravel'),
					'id'	=> 'cabin_type_max_count',
					'type'	=> 'slider',
					'min'	=> '0',
					'max'	=> '10',
					'step'	=> '1'
				),
				array(
					'label'	=> esc_html__('Max child count', 'bookyourtravel'),
					'desc'	=> esc_html__('How many children are allowed in the cabin?', 'bookyourtravel'),
					'id'	=> 'cabin_type_max_child_count',
					'type'	=> 'slider',
					'min'	=> '0',
					'max'	=> '10',
					'step'	=> '1'
				),
				array(
					'label'	=> esc_html__('Bed size', 'bookyourtravel'),
					'desc'	=> esc_html__('How big is/are the beds?', 'bookyourtravel'),
					'id'	=> 'cabin_type_bed_size',
					'type'	=> 'text'
				),
				array(
					'label'	=> esc_html__('Cabin size', 'bookyourtravel'),
					'desc'	=> esc_html__('What is the cabin size (m2)?', 'bookyourtravel'),
					'id'	=> 'cabin_type_room_size',
					'type'	=> 'text'
				),
				array(
					'label'	=> esc_html__('Cabin meta information', 'bookyourtravel'),
					'desc'	=> esc_html__('What other information applies to this specific cabin type?', 'bookyourtravel'),
					'id'	=> 'cabin_type_meta',
					'type'	=> 'text'
				),
				array( // Taxonomy Select box
					'label'	=> esc_html__('Facilities', 'bookyourtravel'), // <label>
					// the description is created in the callback function with a link to Manage the taxonomy terms
					'id'	=> 'facility', // field id and name, needs to be the exact name of the taxonomy
					'type'	=> 'tax_checkboxes' // type of field
				),
			);
		}
	
		new custom_add_meta_box( 'cabin_type_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->cabin_type_custom_meta_fields, 'cabin_type' );
	}
		
	function register_cabin_type_post_type() {
		
		$labels = array(
			'name'                => esc_html__( 'Cabin types', 'bookyourtravel' ),
			'singular_name'       => esc_html__( 'Cabin type', 'bookyourtravel' ),
			'menu_name'           => esc_html__( 'Cabin types', 'bookyourtravel' ),
			'all_items'           => esc_html__( 'Cabin types', 'bookyourtravel' ),
			'view_item'           => esc_html__( 'View Cabin type', 'bookyourtravel' ),
			'add_new_item'        => esc_html__( 'Add New Cabin type', 'bookyourtravel' ),
			'add_new'             => esc_html__( 'New Cabin type', 'bookyourtravel' ),
			'edit_item'           => esc_html__( 'Edit Cabin type', 'bookyourtravel' ),
			'update_item'         => esc_html__( 'Update Cabin type', 'bookyourtravel' ),
			'search_items'        => esc_html__( 'Search Cabin types', 'bookyourtravel' ),
			'not_found'           => esc_html__( 'No Cabin types found', 'bookyourtravel' ),
			'not_found_in_trash'  => esc_html__( 'No Cabin types found in Trash', 'bookyourtravel' ),
		);
		$args = array(
			'label'               => esc_html__( 'Cabin type', 'bookyourtravel' ),
			'description'         => esc_html__( 'Cabin type information pages', 'bookyourtravel' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'author' ),
			'taxonomies'          => array( ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => 'edit.php?post_type=cruise',
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_icon'           => '',
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'page',
			'rewrite' => false,
		);
		register_post_type( 'cabin_type', $args );	
	}
	
	function list_cabin_types( $author_id = null, $statuses = array('publish'), $cruise_id = null ) {

		$args = array(
		   'post_type' => 'cabin_type',
		   'post_status' => $statuses,
		   'posts_per_page' => -1,
		   'suppress_filters' => 0,
		   'orderby' => 'title',
		   'order' => 'ASC'
		);

		if (isset($author_id) && $author_id > 0) {
			$args['author'] = intval($author_id);
		}
		
		$meta_query = array('relation' => 'AND');

		if (isset($cruise_id) && $cruise_id > 0) {
			$meta_query[] = array(
				'key'       => 'cabin_type_cruise_post_ids',
				'value'     => serialize((string)$cruise_id),
				'compare'   => 'LIKE'
			);	
		}
		
		$args['meta_query'] = $meta_query;
		
		$query = new WP_Query($args);

		return $query;
	}
}

global $bookyourtravel_cabin_type_helper;
// store the instance in a variable to be retrieved later and call init
$bookyourtravel_cabin_type_helper = BookYourTravel_Cabin_Type_Helper::get_instance();
$bookyourtravel_cabin_type_helper->init();