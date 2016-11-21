<?php

class BookYourTravel_Room_Type_Helper extends BookYourTravel_BaseSingleton {

	private $enable_accommodations;
	private $room_type_custom_meta_fields;
	
	protected function __construct() {
	
		global $bookyourtravel_theme_globals;
		
		$this->enable_accommodations = $bookyourtravel_theme_globals->enable_accommodations();
		
        // our parent class might
        // contain shared code in its constructor
        parent::__construct();		
    }

    public function init() {

		add_action( 'bookyourtravel_initialize_post_types', array( $this, 'initialize_post_type' ), 0);
	
		if ($this->enable_accommodations) {		
		
			add_action('admin_init', array($this, 'remove_unnecessary_meta_boxes') );
			add_action( 'admin_init', array( $this, 'room_type_admin_init' ) );
		}
	}

	function remove_unnecessary_meta_boxes() {

		remove_meta_box('tagsdiv-room_type', 'room_type', 'side');		
	}
	
	function initialize_post_type() {

		global $bookyourtravel_theme_globals;
		$this->enable_accommodations = $bookyourtravel_theme_globals->enable_accommodations();	
		
		if ($this->enable_accommodations) {
			$this->register_room_type_post_type();
		}
	}
	
	function room_type_admin_init() {	

		if ($this->enable_accommodations) {
		
			$this->room_type_custom_meta_fields = array(
				array(
					'label'	=> esc_html__('Minumum adult count', 'bookyourtravel'),
					'desc'	=> esc_html__('What is the fewest number of adults required in the room?', 'bookyourtravel'),
					'id'	=> 'room_type_min_count',
					'type'	=> 'slider',
					'min'	=> '1',
					'max'	=> '10',
					'step'	=> '1'
				),
				array(
					'label'	=> esc_html__('Minumum child count', 'bookyourtravel'),
					'desc'	=> esc_html__('What is the fewest number of children required in the room?', 'bookyourtravel'),
					'id'	=> 'room_type_min_child_count',
					'type'	=> 'slider',
					'min'	=> '0',
					'max'	=> '10',
					'step'	=> '1'
				),
				array(
					'label'	=> esc_html__('Maximum adult count', 'bookyourtravel'),
					'desc'	=> esc_html__('How many adults are allowed in the room?', 'bookyourtravel'),
					'id'	=> 'room_type_max_count',
					'type'	=> 'slider',
					'min'	=> '1',
					'max'	=> '10',
					'step'	=> '1'
				),
				array(
					'label'	=> esc_html__('Maximum child count', 'bookyourtravel'),
					'desc'	=> esc_html__('How many children are allowed in the room?', 'bookyourtravel'),
					'id'	=> 'room_type_max_child_count',
					'type'	=> 'slider',
					'min'	=> '0',
					'max'	=> '10',
					'step'	=> '1'
				),
				array(
					'label'	=> esc_html__('Bed size', 'bookyourtravel'),
					'desc'	=> esc_html__('How big is/are the beds?', 'bookyourtravel'),
					'id'	=> 'room_type_bed_size',
					'type'	=> 'text'
				),
				array(
					'label'	=> esc_html__('Room size', 'bookyourtravel'),
					'desc'	=> esc_html__('What is the room size (m2)?', 'bookyourtravel'),
					'id'	=> 'room_type_room_size',
					'type'	=> 'text'
				),
				array(
					'label'	=> esc_html__('Room meta information', 'bookyourtravel'),
					'desc'	=> esc_html__('What other information applies to this specific room type?', 'bookyourtravel'),
					'id'	=> 'room_type_meta',
					'type'	=> 'text'
				),
				array( // Taxonomy Select box
					'label'	=> esc_html__('Facilities', 'bookyourtravel'), // <label>
					// the description is created in the callback function with a link to Manage the taxonomy terms
					'id'	=> 'facility', // field id and name, needs to be the exact name of the taxonomy
					'type'	=> 'tax_checkboxes' // type of field
				),
				array( // Repeatable & Sortable Text inputs
					'label'	=> esc_html__('Gallery images', 'bookyourtravel'), // <label>
					'desc'	=> esc_html__('A collection of images to be used in gallery of the room type', 'bookyourtravel'), // description
					'id'	=> 'room_type_images', // field id and name
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
		
		}
		
		new custom_add_meta_box( 'room_type_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->room_type_custom_meta_fields, 'room_type' );
	}
	
	function register_room_type_post_type() {
		
		$labels = array(
			'name'                => esc_html__( 'Room types', 'bookyourtravel' ),
			'singular_name'       => esc_html__( 'Room type', 'bookyourtravel' ),
			'menu_name'           => esc_html__( 'Room types', 'bookyourtravel' ),
			'all_items'           => esc_html__( 'Room types', 'bookyourtravel' ),
			'view_item'           => esc_html__( 'View Room type', 'bookyourtravel' ),
			'add_new_item'        => esc_html__( 'Add New Room type', 'bookyourtravel' ),
			'add_new'             => esc_html__( 'New Room type', 'bookyourtravel' ),
			'edit_item'           => esc_html__( 'Edit Room type', 'bookyourtravel' ),
			'update_item'         => esc_html__( 'Update Room type', 'bookyourtravel' ),
			'search_items'        => esc_html__( 'Search room_types', 'bookyourtravel' ),
			'not_found'           => esc_html__( 'No room types found', 'bookyourtravel' ),
			'not_found_in_trash'  => esc_html__( 'No room types found in Trash', 'bookyourtravel' ),
		);
		$args = array(
			'label'               => esc_html__( 'Room type', 'bookyourtravel' ),
			'description'         => esc_html__( 'Room type information pages', 'bookyourtravel' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'author' ),
			'taxonomies'          => array( ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => 'edit.php?post_type=accommodation',
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
		register_post_type( 'room_type', $args );	
	}
	
	function list_room_types( $author_id = null, $statuses = array('publish') ) {

		$args = array(
		   'post_type' => 'room_type',
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
		
		$args['meta_query'] = $meta_query;
		
		$query = new WP_Query($args);

		return $query;
	}

}

global $bookyourtravel_room_type_helper;
// store the instance in a variable to be retrieved later and call init
$bookyourtravel_room_type_helper = BookYourTravel_Room_Type_Helper::get_instance();
$bookyourtravel_room_type_helper->init();