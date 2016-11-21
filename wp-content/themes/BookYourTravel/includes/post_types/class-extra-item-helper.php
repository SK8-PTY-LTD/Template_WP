<?php

class BookYourTravel_Extra_Item_Helper extends BookYourTravel_BaseSingleton {

	private $enable_extra_items;
	
	private $enable_accommodations;
	private $enable_cruises;
	private $enable_tours;
	private $enable_car_rentals;
	
	private $extra_item_custom_meta_fields;
	
	protected function __construct() {
	
		global $bookyourtravel_theme_globals;
		
		$this->enable_extra_items = $bookyourtravel_theme_globals->enable_extra_items();
		$this->enable_accommodations = $bookyourtravel_theme_globals->enable_accommodations();
		$this->enable_cruises = $bookyourtravel_theme_globals->enable_cruises();
		$this->enable_tours = $bookyourtravel_theme_globals->enable_tours();
		$this->enable_car_rentals = $bookyourtravel_theme_globals->enable_car_rentals();
		
        // our parent class might contain shared code in its constructor
        parent::__construct();		
    }
	
    public function init() {

		add_action( 'bookyourtravel_initialize_post_types', array( $this, 'initialize_extra_items_post_type' ), 0);			
	
		if ($this->enable_extra_items) {

			add_action( 'admin_init', array( $this, 'extra_item_admin_init' ) );
		}
	}
	
	function initialize_extra_items_post_type() {
	
		global $bookyourtravel_theme_globals;	
		$this->enable_extra_items = $bookyourtravel_theme_globals->enable_extra_items();
	
		if ($this->enable_extra_items) {
			$this->register_extra_item_post_type();
		}
	}
	
	function extra_item_admin_init() {
		
		$accommodation_types = array();
		$accommodation_tags = array();
		if ($this->enable_accommodations) {
			$accommodation_types_terms = get_terms(array( 'accommodation_type' ), array( 'hide_empty' => false, 'fields' => 'all' ));
			foreach ($accommodation_types_terms as $accommodation_type) {
				if (isset($accommodation_type) && isset($accommodation_type->term_id) && isset($accommodation_type->name)) {
					$accommodation_types[] = array('value' => $accommodation_type->term_id, 'label' => $accommodation_type->name);
				}
			}
			$accommodation_tags_terms = get_terms(array( 'acc_tag' ), array( 'hide_empty' => false, 'fields' => 'all' ));
			foreach ($accommodation_tags_terms as $accommodation_tag) {
				if (isset($accommodation_tag) && isset($accommodation_tag->term_id) && isset($accommodation_tag->name)) {
					$accommodation_tags[] = array('value' => $accommodation_tag->term_id, 'label' => $accommodation_tag->name);
				}
			}
		}
		
		$tour_types = array();
		$tour_tags = array();
		if ($this->enable_tours) {
			$tour_types_terms = get_terms(array( 'tour_type' ), array( 'hide_empty' => false, 'fields' => 'all' ));
			foreach ($tour_types_terms as $tour_type) {
				if (isset($tour_type) && isset($tour_type->term_id) && isset($tour_type->name)) {
					$tour_types[] = array('value' => $tour_type->term_id, 'label' => $tour_type->name);
				}
			}
			$tour_tags_terms = get_terms(array( 'tour_tag' ), array( 'hide_empty' => false, 'fields' => 'all' ));
			foreach ($tour_tags_terms as $tour_tag) {
				if (isset($tour_tag) && isset($tour_tag->term_id) && isset($tour_tag->name)) {
					$tour_tags[] = array('value' => $tour_tag->term_id, 'label' => $tour_tag->name);
				}
			}
		}
		
		$cruise_types = array();
		$cruise_tags = array();
		if ($this->enable_cruises) {
			$cruise_types_terms = get_terms(array( 'cruise_type' ), array( 'hide_empty' => false, 'fields' => 'all' ));
			foreach ($cruise_types_terms as $cruise_type) {
				if (isset($cruise_type) && isset($cruise_type->term_id) && isset($cruise_type->name)) {
					$cruise_types[] = array('value' => $cruise_type->term_id, 'label' => $cruise_type->name);
				}
			}
			$cruise_tags_terms = get_terms(array( 'cruise_tag' ), array( 'hide_empty' => false, 'fields' => 'all' ));
			foreach ($cruise_tags_terms as $cruise_tag) {
				if (isset($cruise_tag) && isset($cruise_tag->term_id) && isset($cruise_tag->name)) {
					$cruise_tags[] = array('value' => $cruise_tag->term_id, 'label' => $cruise_tag->name);
				}
			}
		}
		
		$car_types = array();
		$car_rental_tags = array();
		if ($this->enable_car_rentals) {
			$car_types_terms = get_terms(array( 'car_type' ), array( 'hide_empty' => false, 'fields' => 'all' ));
			foreach ($car_types_terms as $car_type) {
				if (isset($car_type) && isset($car_type->term_id) && isset($car_type->name)) {
					$car_types[] = array('value' => $car_type->term_id, 'label' => $car_type->name);
				}
			}
			$car_rental_tags_terms = get_terms(array( 'car_rental_tag' ), array( 'hide_empty' => false, 'fields' => 'all' ));
			foreach ($car_rental_tags_terms as $car_rental_tag) {
				if (isset($car_rental_tag) && isset($car_rental_tag->term_id) && isset($car_rental_tag->name)) {
					$car_rental_tags[] = array('value' => $car_rental_tag->term_id, 'label' => $car_rental_tag->name);
				}
			}
		}
	
		$this->extra_item_custom_meta_fields = array(
			array( // Post ID select box
				'label'	=> esc_html__('Price per item?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('What is the price of this extra item?', 'bookyourtravel'), // description
				'id'	=> '_extra_item_price', // field id and name
				'type'	=> 'text',
				'step'  => 'any'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Max allowed items?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('How many pieces of the extra item are allowed?', 'bookyourtravel'), // description
				'id'	=> '_extra_item_max_allowed', // field id and name
				'type'	=> 'slider',
				'min'	=> '1',
				'max'	=> '100',
				'step'	=> '1'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Is price per person?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('Is the extra item price per person? If left unchecked the price is charged per entire accommodation, tour, cruis or car rental.', 'bookyourtravel'), // description
				'id'	=> '_extra_item_price_per_person', // field id and name
				'type'	=> 'checkbox', // type of field
			),		
			array( // Post ID select box
				'label'	=> esc_html__('Is price per day', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('Is the extra item price charged per day? If left unchecked the price is charged per entire duration of accommodation stay, tour, cruise or car rental', 'bookyourtravel'), // description
				'id'	=> '_extra_item_price_per_day', // field id and name
				'type'	=> 'checkbox', // type of field
			),
			array( // Post ID select box
				'label'	=> esc_html__('Is this item required', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked users will be forced to pay for this extra item - allowing admin to for example charge for tourist tax', 'bookyourtravel'), // description
				'id'	=> '_extra_item_is_required', // field id and name
				'type'	=> 'checkbox', // type of field
			),			
		);
		
		if ($this->enable_accommodations) {
			$this->extra_item_custom_meta_fields[] = array( // Post ID select box
				'label'	=> esc_html__('Supported accommodation types', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('The accommodation types this extra item is applicable to.', 'bookyourtravel'), // description
				'id'	=>  'accommodation_types', // field id and name
				'type'	=> 'checkbox_group', // type of field
				'options' => $accommodation_types // post types to display, options are prefixed with their post type
			);
			$this->extra_item_custom_meta_fields[] = array( // Post ID select box
				'label'	=> esc_html__('Supported accommodation tags', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('The accommodation tags this extra item is applicable to.', 'bookyourtravel'), // description
				'id'	=>  'accommodation_tags', // field id and name
				'type'	=> 'checkbox_group', // type of field
				'options' => $accommodation_tags // post types to display, options are prefixed with their post type
			);
		}
			
		if ($this->enable_tours) {
			$this->extra_item_custom_meta_fields[] = array( // Post ID select box
				'label'	=> esc_html__('Supported tour types', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('The tour types this extra item is applicable to.', 'bookyourtravel'), // description
				'id'	=>  'tour_types', // field id and name
				'type'	=> 'checkbox_group', // type of field
				'options' => $tour_types // post types to display, options are prefixed with their post type
			);
			$this->extra_item_custom_meta_fields[] = array( // Post ID select box
				'label'	=> esc_html__('Supported tour tags', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('The tour tags this extra item is applicable to.', 'bookyourtravel'), // description
				'id'	=>  'tour_tags', // field id and name
				'type'	=> 'checkbox_group', // type of field
				'options' => $tour_tags // post types to display, options are prefixed with their post type
			);
		}
			
		if ($this->enable_cruises) {
			$this->extra_item_custom_meta_fields[] = array( // Post ID select box
				'label'	=> esc_html__('Supported cruise types', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('The cruise types this extra item is applicable to.', 'bookyourtravel'), // description
				'id'	=>  'cruise_types', // field id and name
				'type'	=> 'checkbox_group', // type of field
				'options' => $cruise_types // post types to display, options are prefixed with their post type
			);
			$this->extra_item_custom_meta_fields[] = array( // Post ID select box
				'label'	=> esc_html__('Supported cruise tags', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('The cruise tags this extra item is applicable to.', 'bookyourtravel'), // description
				'id'	=>  'cruise_tags', // field id and name
				'type'	=> 'checkbox_group', // type of field
				'options' => $cruise_tags // post types to display, options are prefixed with their post type
			);
		}
		
		if ($this->enable_car_rentals) {
			$this->extra_item_custom_meta_fields[] = array( // Post ID select box
				'label'	=> esc_html__('Supported car types', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('The car types this extra item is applicable to.', 'bookyourtravel'), // description
				'id'	=>  'car_types', // field id and name
				'type'	=> 'checkbox_group', // type of field
				'options' => $car_types // post types to display, options are prefixed with their post type
			);
			$this->extra_item_custom_meta_fields[] = array( // Post ID select box
				'label'	=> esc_html__('Supported car_rental tags', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('The car_rental tags this extra item is applicable to.', 'bookyourtravel'), // description
				'id'	=>  'car_rental_tags', // field id and name
				'type'	=> 'checkbox_group', // type of field
				'options' => $car_rental_tags // post types to display, options are prefixed with their post type
			);
		}
		
		new custom_add_meta_box( 'extra_item_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->extra_item_custom_meta_fields, 'extra_item' );
	}
	
	function register_extra_item_post_type() {
		
		$labels = array(
			'name'                => esc_html__( 'Extra items', 'Post Type General Name', 'bookyourtravel' ),
			'singular_name'       => esc_html__( 'Extra item', 'Post Type Singular Name', 'bookyourtravel' ),
			'menu_name'           => esc_html__( 'Extra items', 'bookyourtravel' ),
			'all_items'           => esc_html__( 'All Extra items', 'bookyourtravel' ),
			'view_item'           => esc_html__( 'View Extra item', 'bookyourtravel' ),
			'add_new_item'        => esc_html__( 'Add New Extra item', 'bookyourtravel' ),
			'add_new'             => esc_html__( 'New Extra item', 'bookyourtravel' ),
			'edit_item'           => esc_html__( 'Edit Extra item', 'bookyourtravel' ),
			'update_item'         => esc_html__( 'Update Extra item', 'bookyourtravel' ),
			'search_items'        => esc_html__( 'Search Extra items', 'bookyourtravel' ),
			'not_found'           => esc_html__( 'No Extra items found', 'bookyourtravel' ),
			'not_found_in_trash'  => esc_html__( 'No Extra items found in Trash', 'bookyourtravel' ),
		);
		$args = array(
			'label'               => esc_html__( 'extra item', 'bookyourtravel' ),
			'description'         => esc_html__( 'Extra item information pages', 'bookyourtravel' ),
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
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
			'rewrite' 			  => false,
		);
		
		register_post_type( 'extra_item', $args );	
	}
	
	public function list_extra_items_by_post_type($post_type, $type_term_ids = array(), $tag_term_ids = array()) {
		
		$results = array();
		
		$raw_results = $this->list_extra_items(0, -1, 'post_title', 'ASC');
		
		if ($raw_results && $raw_results['total'] > 0) {
			
			foreach ($raw_results['results'] as $result) {
			
				$extra_id = $result->ID;
				
				$type_terms = null;
				$tag_terms = null;
				if ($post_type == 'accommodation') {
					$type_terms = get_post_meta($extra_id, 'accommodation_types', true);
					$tag_terms = get_post_meta($extra_id, 'accommodation_tags', true);
				} else if ($post_type == 'tour') {
					$type_terms = get_post_meta($extra_id, 'tour_types', true);
					$tag_terms = get_post_meta($extra_id, 'tour_tags', true);
				} else if ($post_type == 'cruise') {
					$type_terms = get_post_meta($extra_id, 'cruise_types', true);
					$tag_terms = get_post_meta($extra_id, 'cruise_tags', true);
				} else if ($post_type == 'car_rental') {
					$type_terms = get_post_meta($extra_id, 'car_types', true);
					$tag_terms = get_post_meta($extra_id, 'car_rental_tags', true);
				}

				if ($type_terms != null && $type_term_ids > 0) {
					foreach ($type_term_ids as $type_term_id) {
						if ($type_term_id > 0) {
							if (in_array($type_term_id, $type_terms) && !in_array($result, $results)) {
								$results[] = $result;
							}
						}
					}
				}

				if ($tag_terms != null && $tag_term_ids > 0) {
					foreach ($tag_term_ids as $tag_term_id) {
						if ($tag_term_id > 0) {
							if (in_array($tag_term_id, $tag_terms) && !in_array($result, $results)) {
								$results[] = $result;
							}
						}
					}
				}
				
				if (($tag_term_ids == null && $type_term_ids == null) || (count($tag_term_ids) == 0 && count($type_term_ids) == 0)) {
					$results[] = $result;
				}
			}
		}
		
		return $results;
	}
	
	function list_extra_items($paged = 0, $per_page = -1, $orderby = '', $order = '', $author_id = null, $include_private = false, $count_only = false, $extra_item_ids = array() ) {
	
		$args = array(
			'post_type'         => 'extra_item',
			'post_status'       => array('publish'),
			'posts_per_page'    => $per_page,
			'paged' 			=> $paged, 
			'orderby'           => $orderby,
			'suppress_filters' 	=> false,
			'order'				=> $order,
			'meta_query'        => array('relation' => 'AND')
		);
		
		if (count($extra_item_ids) > 0) {
			$args['post__in'] = $extra_item_ids;
		}
		
		if ($include_private) {
			$args['post_status'][] = 'draft';
			$args['post_status'][] = 'private';
		}
		
		if (isset($author_id)) {
			$author_id = intval($author_id);
			if ($author_id > 0) {
				$args['author'] = $author_id;
			}
		}
	
		$posts_query = new WP_Query($args);
		
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
		
		return $results;
	}
	
	function calculate_extra_item_total($extra_item_id, $quantity = 1, $adults = 1, $children = 0, $total_days = 0) {
	
		$extra_item_total_price = 0;
		$extra_item = new BookYourTravel_Extra_Item($extra_item_id);
		
		if ($extra_item) {
		
			$max_allowed = $extra_item->get_custom_field('_extra_item_max_allowed', false);
			$extra_item_price = floatval($extra_item->get_custom_field('_extra_item_price', false));
			$extra_item_price_per_person = intval($extra_item->get_custom_field('_extra_item_price_per_person', false));
			$extra_item_price_per_day = intval($extra_item->get_custom_field('_extra_item_price_per_day', false));
			$extra_item_is_required = intval($extra_item->get_custom_field('_extra_item_is_required', false));

			if ($extra_item_price_per_person) {
				$extra_item_price = ($adults * $extra_item_price) + ($children * $extra_item_price);
			}
								
			if ($extra_item_price_per_day) {
				$extra_item_price = $extra_item_price * $total_days;
			}
			
			if ($extra_item_is_required && $quantity == 0) {
				$quantity = 1;
			}
					
			$extra_item_total_price = $quantity * $extra_item_price;
		}
		
		return $extra_item_total_price;
	}
}

global $bookyourtravel_extra_item_helper;
// store the instance in a variable to be retrieved later and call init
$bookyourtravel_extra_item_helper = BookYourTravel_Extra_Item_Helper::get_instance();
$bookyourtravel_extra_item_helper->init();