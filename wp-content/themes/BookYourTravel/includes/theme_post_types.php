<?php

require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-review-helper.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-location-helper.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-facility-helper.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-room-type-helper.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-accommodation-helper.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-tour-helper.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-cabin-type-helper.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-cruise-helper.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-car-rental-helper.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-post-helper.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-extra-item-helper.php');

class BookYourTravel_Theme_Post_Types extends BookYourTravel_BaseSingleton {

	protected function __construct() {
	
        // our parent class might
        // contain shared code in its constructor
        parent::__construct();
    }

    public function init() {
		add_action( 'init', array($this, 'initialize_post_types' ) );
    }
	
	function save_post($post_id, $post, $update) {

		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}
		
		if ($post->post_type != 'location' && $post->post_type != 'accommodation' && $post->post_type != 'tour' && $post->post_type != 'cruise' && $post->post_type != 'car_rental') {
			return;
		}

		do_action('bookyourtravel_save_' . $post->post_type, $post_id);		
	}
	
	function after_delete_post($post_id) {
		
		global $post_type;
		if ($post_type != 'location' && $post_type != 'accommodation' && $post_type != 'tour' && $post_type != 'cruise' && $post_type != 'car_rental') {
			return;
		}
			
		do_action('bookyourtravel_after_delete_' . $post_type, $post_id);		
	}
	
	function initialize_post_types() {
	
		do_action('bookyourtravel_initialize_post_types');
		
		add_action( 'after_delete_post', array($this, 'after_delete_post'), 10, 1 );
		add_action( 'save_post', array($this, 'save_post'), 10, 3 );
	}
	
}

// store the instance in a variable to be retrieved later and call init
$bookyourtravel_theme_post_types = BookYourTravel_Theme_Post_Types::get_instance();
$bookyourtravel_theme_post_types->init();