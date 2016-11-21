<?php

class BookYourTravel_Location_Helper extends BookYourTravel_BaseSingleton {
	
	private $location_list_custom_meta_fields;
	private $location_custom_meta_fields;
	private $location_list_meta_box;
	
	protected function __construct() {
	
        // our parent class might
        // contain shared code in its constructor
        parent::__construct();
		
    }

    public function init() {
	
		add_action( 'bookyourtravel_after_delete_location', array( $this, 'after_delete_location' ), 10, 1);
		add_action( 'bookyourtravel_save_location', array( $this, 'save_location' ), 10, 1);
	
		add_action( 'admin_init', array($this, 'remove_unnecessary_meta_boxes') );	
		add_action( 'admin_init', array( $this, 'location_admin_init' ) );
		add_action( 'bookyourtravel_initialize_post_types', array( $this, 'initialize_post_type' ), 0);
	}
	
	function save_location($post_id) {
		
		delete_post_meta_by_key('_location_tour_count');
		delete_post_meta_by_key('_location_cruise_count');
		delete_post_meta_by_key('_location_accommodation_count');	
		delete_post_meta_by_key('_location_car_rental_count');		
		
	}
	
	function after_delete_location($post_id) {
		
		delete_post_meta_by_key('_location_tour_count');
		delete_post_meta_by_key('_location_cruise_count');
		delete_post_meta_by_key('_location_accommodation_count');		
		delete_post_meta_by_key('_location_car_rental_count');		
		
	}
	
	function remove_unnecessary_meta_boxes() {

		remove_meta_box('tagsdiv-location_tag', 'location', 'side');		
	}
	
	function register_location_tag_taxonomy() {
	
		$labels = array(
				'name'              => esc_html__( 'Location Tags', 'bookyourtravel' ),
				'singular_name'     => __( 'Location Tag', 'bookyourtravel' ),
				'search_items'      => esc_html__( 'Search Location tags', 'bookyourtravel' ),
				'all_items'         => esc_html__( 'All Location tags', 'bookyourtravel' ),
				'parent_item'                => null,
				'parent_item_colon'          => null,
				'edit_item'         => esc_html__( 'Edit Location tag', 'bookyourtravel' ),
				'update_item'       => esc_html__( 'Update Location tag', 'bookyourtravel' ),
				'add_new_item'      => esc_html__( 'Add New Location tag', 'bookyourtravel' ),
				'new_item_name'     => esc_html__( 'New Location tag Name', 'bookyourtravel' ),
				'separate_items_with_commas' => esc_html__( 'Separate Location tags with commas', 'bookyourtravel' ),
				'add_or_remove_items'        => esc_html__( 'Add or remove Location tags', 'bookyourtravel' ),
				'choose_from_most_used'      => esc_html__( 'Choose from the most used Location tags', 'bookyourtravel' ),
				'not_found'                  => esc_html__( 'No Location tags found.', 'bookyourtravel' ),
				'menu_name'         => esc_html__( 'Location Tags', 'bookyourtravel' ),
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
			
		register_taxonomy( 'location_tag', array( 'location' ), $args );
	}	

	function location_admin_init() {

		global $bookyourtravel_theme_globals;
	
		$sort_by_columns = array();
		$sort_by_columns[] = array('value' => 'title', 'label' => esc_html__('Location title', 'bookyourtravel'));
		$sort_by_columns[] = array('value' => 'ID', 'label' => esc_html__('Location ID', 'bookyourtravel'));
		$sort_by_columns[] = array('value' => 'date', 'label' => esc_html__('Publish date', 'bookyourtravel'));
		$sort_by_columns[] = array('value' => 'rand', 'label' => esc_html__('Random', 'bookyourtravel'));
		$sort_by_columns[] = array('value' => 'comment_count', 'label' => esc_html__('Comment count', 'bookyourtravel'));
			
	
		$this->location_list_custom_meta_fields = array(
			array( // Taxonomy Select box
				'label'	=> esc_html__('Location', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'location_list_location_post_id', // field id and name
				'type'	=> 'post_select', // type of field
				'post_type' => array('location') // post types to display, options are prefixed with their post type
			),
			array( // Select box
				'label'	=> esc_html__('Sort by field', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'location_list_sort_by', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'select', // type of field
				'options' => $sort_by_columns
			),
			array( // Post ID select box
				'label'	=> esc_html__('Sort descending?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will sort locations in descending order', 'bookyourtravel'), // description
				'id'	=> 'location_list_sort_descending', // field id and name
				'type'	=> 'checkbox', // type of field
			),
			array( // Post ID select box
				'label'	=> esc_html__('Show featured only?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will list featured locations only', 'bookyourtravel'), // description
				'id'	=> 'location_list_show_featured_only', // field id and name
				'type'	=> 'checkbox', // type of field
			),
			array( // Taxonomy Select box
				'label'	=> esc_html__('Location tags', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'location_tag', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'tax_checkboxes' // type of field
			),			
		);
		
		$this->location_custom_meta_fields = array(
			array( // Post ID select box
				'label'	=> esc_html__('Is Featured', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('Show in lists where only featured items are shown.', 'bookyourtravel'), // description
				'id'	=> 'location_is_featured', // field id and name
				'type'	=> 'checkbox', // type of field
			),
			array( // Post ID select box
				'label'	=> esc_html__('Display As Directory?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('Check this option if you want to show list of descendant locations when showing this single location instead of showing what single location page usually shows. Useful for Country locations that than lists all of that country\'s cities.', 'bookyourtravel'), // description
				'id'	=> 'location_display_as_directory', // field id and name
				'type'	=> 'checkbox', // type of field
			),
			array(
				'label'	=> esc_html__('Country', 'bookyourtravel'),
				'desc'	=> esc_html__('Country name', 'bookyourtravel'),
				'id'	=> 'location_country',
				'type'	=> 'text'
			),
			array( // Taxonomy Select box
				'label'	=> esc_html__('Location tag', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'location_tag', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'tax_checkboxes' // type of field
			),			
			array( // Repeatable & Sortable Text inputs
				'label'	=> esc_html__('Gallery images', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('A collection of images to be used in slider/gallery on single page', 'bookyourtravel'), // description
				'id'	=> 'location_images', // field id and name
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
		
		$location_extra_fields = $bookyourtravel_theme_globals->get_location_extra_fields();
			
		foreach ($location_extra_fields as $location_extra_field) {
			$field_is_hidden = isset($location_extra_field['hide']) ? intval($location_extra_field['hide']) : 0;
			
			if (!$field_is_hidden) {
				$extra_field = null;
				$field_label = isset($location_extra_field['label']) ? $location_extra_field['label'] : '';
				$field_id = isset($location_extra_field['id']) ? $location_extra_field['id'] : '';
				$field_type = isset($location_extra_field['type']) ? $location_extra_field['type'] :  '';

				if ($field_type == 'textarea')
					$field_type = 'editor';

				if (!empty($field_label) && !empty($field_id) && !empty($field_type)) {
					$extra_field = array(
						'label'	=> $field_label,
						'desc'	=> '',
						'id'	=> 'location_' . $field_id,
						'type'	=> $field_type
					);
				}

				if ($extra_field) 
					$this->location_custom_meta_fields[] = $extra_field;
			}
		}
	
		new custom_add_meta_box( 'location_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->location_custom_meta_fields, 'location', true );
		
		$this->location_list_meta_box = new custom_add_meta_box( 'location_list_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->location_list_custom_meta_fields, 'page' );
		remove_action( 'add_meta_boxes', array( $this->location_list_meta_box, 'add_box' ) );
		add_action('add_meta_boxes', array($this, 'location_list_add_meta_boxes') );
	}
	
	function location_list_add_meta_boxes() {
		global $post;
		$template_file = get_post_meta($post->ID,'_wp_page_template',true);
		if ($template_file == 'page-location-list.php') {
			add_meta_box( $this->location_list_meta_box->id, $this->location_list_meta_box->title, array( $this->location_list_meta_box, 'meta_box_callback' ), 'page', 'normal', 'high' );
		}
	}
	
	function initialize_post_type() {

		$this->register_location_post_type();
		$this->register_location_tag_taxonomy();				
	}

	function register_location_post_type() {
	
		global $bookyourtravel_theme_globals;
		
		$locations_permalink_slug = $bookyourtravel_theme_globals->get_locations_permalink_slug();

		$location_list_page_id = $bookyourtravel_theme_globals->get_location_list_page_id();
		
		if ($location_list_page_id > 0) {

			add_rewrite_rule(
				"{$locations_permalink_slug}$",
				"index.php?post_type=page&page_id={$location_list_page_id}", 'top');
		
			add_rewrite_rule(
				"{$locations_permalink_slug}/page/?([1-9][0-9]*)",
				"index.php?post_type=page&page_id={$location_list_page_id}&paged=\$matches[1]", 'top');
		
		}
		
		add_rewrite_rule(
			"{$locations_permalink_slug}/.*/([^/]+)/page/?([1-9][0-9]*)",
			"index.php?post_type=location&name=\$matches[1]&paged-byt=\$matches[2]", 'top');
			
		add_rewrite_tag('%paged-byt%', '([1-9][0-9]*)');
		
		$labels = array(
			'name'                => esc_html__( 'Locations', 'bookyourtravel' ),
			'singular_name'       => esc_html__( 'Location', 'bookyourtravel' ),
			'menu_name'           => esc_html__( 'Locations', 'bookyourtravel' ),
			'all_items'           => esc_html__( 'All Locations', 'bookyourtravel' ),
			'view_item'           => esc_html__( 'View Location', 'bookyourtravel' ),
			'add_new_item'        => esc_html__( 'Add New Location', 'bookyourtravel' ),
			'add_new'             => esc_html__( 'New Location', 'bookyourtravel' ),
			'edit_item'           => esc_html__( 'Edit Location', 'bookyourtravel' ),
			'update_item'         => esc_html__( 'Update Location', 'bookyourtravel' ),
			'search_items'        => esc_html__( 'Search locations', 'bookyourtravel' ),
			'not_found'           => esc_html__( 'No locations found', 'bookyourtravel' ),
			'not_found_in_trash'  => esc_html__( 'No locations found in Trash', 'bookyourtravel' ),
		);
		$args = array(
			'label'               => esc_html__( 'Location', 'bookyourtravel' ),
			'description'         => esc_html__( 'Location information pages', 'bookyourtravel' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'author', 'page-attributes' ),
			'taxonomies'          => array( ),
			'hierarchical'        => true,
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
			'rewrite' => array('slug' => $locations_permalink_slug)
		);
		
		register_post_type( 'location', $args );

	}
		
	function list_locations($location_id = 0, $paged = 0, $per_page = -1, $orderby = '', $order = '', $featured_only = false, $location_tags_array = array()) {

		$location_ids = array();
		
		if ($location_id > 0) {
			$location_ids[] = $location_id;
			$location_descendants = BookYourTravel_Theme_Utils::get_post_descendants($location_id, 'location');
			foreach ($location_descendants as $location) {
				$location_ids[] = $location->ID;
			}
		}
		
		$args = array(
			'post_type'         => 'location',
			'post_status'       => array('publish'),
			'posts_per_page'    => $per_page,
			'paged' 			=> $paged, 
			'orderby'           => $orderby,
			'suppress_filters' 	=> false,
			'order'				=> $order,
			'meta_query'        => array('relation' => 'AND')
		);
			
		if (count($location_ids) > 0) {
			$args['meta_query'][] = array(
				'key'       => 'location_location_post_id',
				'value'     => $location_ids,
				'compare'   => 'IN'
			);
		}
		
		if (isset($featured_only) && $featured_only) {
			$args['meta_query'][] = array(
				'key'       => 'location_is_featured',
				'value'     => 1,
				'compare'   => '=',
				'type' => 'numeric'
			);
		}
		
		if (!empty($location_tags_array)) {
			$args['tax_query'][] = 	array(
					'taxonomy' => 'location_tag',
					'field' => 'id',
					'terms' => $location_tags_array,
					'operator'=> 'IN'
			);
		}		
		
		add_filter('posts_join', array($this, 'locations_search_join'), 10, 2 );	

		$posts_query = new WP_Query($args);	
		
		$locations = array();
			
		if ($posts_query->have_posts() ) {
			while ( $posts_query->have_posts() ) {
				global $post;
				$posts_query->the_post(); 
				$locations[] = $post;
			}
		}
		
		$results = array(
			'total' => $posts_query->found_posts,
			'results' => $locations
		);
		
		remove_filter('posts_join', array($this, 'locations_search_join') );		
		
		wp_reset_postdata();
		
		return $results;
	}
	
	function locations_search_join($join) {
		global $wp_query, $wpdb, $bookyourtravel_multi_language_count, $bookyourtravel_theme_globals;

		if(defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('location') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$join .= " INNER JOIN " . $wpdb->prefix . "icl_translations translations_default ON translations_default.element_type = 'post_location' AND translations_default.language_code='" . BookYourTravel_Theme_Utils::get_default_language() . "' AND translations_default.trid = t.trid ";
		}
		
		return $join;
	}
}

global $bookyourtravel_location_helper;
// store the instance in a variable to be retrieved later and call init
$bookyourtravel_location_helper = BookYourTravel_Location_Helper::get_instance();
$bookyourtravel_location_helper->init();