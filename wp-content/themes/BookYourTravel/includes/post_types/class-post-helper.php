<?php

class BookYourTravel_Post_Helper extends BookYourTravel_BaseSingleton {

	protected function __construct() {
	
        // our parent class might
        // contain shared code in its constructor
        parent::__construct();	
	}
	
    public function init() {
	
	}
	
	function list_posts($paged = 0, $per_page = -1, $orderby = '', $order = '', $categories_array = array(), $author_id = null, $include_private = false, $count_only = false ) {
	
		global $bookyourtravel_theme_globals;
		
		$args = array(
			'post_type'         => 'post',
			'post_status'       => array('publish'),
			'posts_per_page'    => $per_page,
			'paged' 			=> $paged, 
			'orderby'           => $orderby,
			'suppress_filters' 	=> false,
			'order'				=> $order,
			'meta_query'        => array('relation' => 'AND')
		);
		
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
		
		if (!empty($categories_array)) {
			$args['tax_query'][] = 	array(
					'taxonomy' => 'category',
					'field' => 'id',
					'terms' => $categories_array,
					'operator'=> 'IN'
			);
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
	
}

global $bookyourtravel_post_helper;
// store the instance in a variable to be retrieved later and call init
$bookyourtravel_post_helper = BookYourTravel_Post_Helper::get_instance();
$bookyourtravel_post_helper->init();