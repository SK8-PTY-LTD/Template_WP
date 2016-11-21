<?php

class BookYourTravel_Facility_Helper extends BookYourTravel_BaseSingleton {
	
	private $enable_accommodations;
	private $enable_cruises;
	
	protected function __construct() {
	
		global $bookyourtravel_theme_globals;
		$this->enable_accommodations = $bookyourtravel_theme_globals->enable_accommodations();
		$this->enable_cruises = $bookyourtravel_theme_globals->enable_cruises();

        // our parent class might
        // contain shared code in its constructor
        parent::__construct();
    }

    public function init() {
			
		if ($this->enable_accommodations || $this->enable_cruises) {
		
			add_action('admin_init', array($this, 'remove_unnecessary_meta_boxes') );
			add_filter('manage_edit-accommodation_columns', array( $this, 'manage_edit_accommodation_columns'), 10, 1);	
			add_action('bookyourtravel_initialize_post_types', array( $this, 'initialize_taxonomy' ), 0);
		}
	}

	function initialize_taxonomy() {
		$this->register_facility_taxonomy();
	}	

	function register_facility_taxonomy() {
	
		$labels = array(
				'name'              		 => esc_html__( 'Facilities', 'bookyourtravel' ),
				'singular_name'     		 => esc_html__( 'Facility', 'bookyourtravel' ),
				'search_items'      		 => esc_html__( 'Search Facilities', 'bookyourtravel' ),
				'all_items'         		 => esc_html__( 'All Facilities', 'bookyourtravel' ),
				'parent_item'                => null,
				'parent_item_colon'          => null,
				'edit_item'         		 => esc_html__( 'Edit Facility', 'bookyourtravel' ),
				'update_item'       		 => esc_html__( 'Update Facility', 'bookyourtravel' ),
				'add_new_item'      		 => esc_html__( 'Add New Facility', 'bookyourtravel' ),
				'new_item_name'     		 => esc_html__( 'New Facility Name', 'bookyourtravel' ),
				'separate_items_with_commas' => esc_html__( 'Separate facilities with commas', 'bookyourtravel' ),
				'add_or_remove_items'        => esc_html__( 'Add or remove facilities', 'bookyourtravel' ),
				'choose_from_most_used'      => esc_html__( 'Choose from the most used facilities', 'bookyourtravel' ),
				'not_found'                  => esc_html__( 'No facilities found.', 'bookyourtravel' ),
				'menu_name'         		 => esc_html__( 'Facilities', 'bookyourtravel' ),
			);
			
		$args = array(
				'hierarchical'      		 => false,
				'labels'            		 => $labels,
				'show_ui'           		 => true,
				'show_admin_column' 		 => true,
				'query_var'         		 => true,
				'update_count_callback' 	 => '_update_post_term_count',
				'rewrite'           		 => null
			);
		
		$types_for_facility = array();

		if ($this->enable_accommodations) {
			$types_for_facility[] = 'accommodation';
			$types_for_facility[] = 'room_type';
		}
		if ($this->enable_cruises) {
			$types_for_facility[] = 'cruise';
			$types_for_facility[] = 'cabin_type';
		}		
		
		if (count($types_for_facility) > 0)
			register_taxonomy( 'facility', $types_for_facility, $args );
	}
	
	function manage_edit_accommodation_columns($columns) {
	
		unset($columns['taxonomy-facility']);

		return $columns;
	}

	function remove_unnecessary_meta_boxes() {
		
		if ($this->enable_accommodations) {
			remove_meta_box('tagsdiv-facility', 'accommodation', 'side');
			remove_meta_box('tagsdiv-facility', 'room_type', 'side');
		}
		if ($this->enable_cruises) {
			remove_meta_box('tagsdiv-facility', 'cruise', 'side');
			remove_meta_box('tagsdiv-facility', 'cabin_type', 'side');
		}
		
	}
}

global $bookyourtravel_facility_helper;
// store the instance in a variable to be retrieved later and call init
$bookyourtravel_facility_helper = BookYourTravel_Facility_Helper::get_instance();
$bookyourtravel_facility_helper->init();