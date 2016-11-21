<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Plugin Name: Oganro Reservation Widget
 * Plugin URI: http://www.oganro.com/
 * Description: Customize your own reservation panel
 * Version: 2.0
 * Author: Oganro (Pvt)Ltd
 * Author URI: http://www.oganro.com/
 * License: GPL2
 */ 


/*----------------------------------------------
 Initiating Methods to generate Search box
------------------------------------------------*/

add_shortcode( 'ogn_rw_widget_screen', 'ogn_rw_load_widget_screen' );

function ogn_rw_load_widget_screen(){

	include('includes/ogn_rw_load_options.php');

	ogn_rw_load_css_files();
	ogn_rw_load_js_files($ogn_rw_bootstrap);

	//$ogn_rw_submit_url = "http://localhost/test/test.php";
	include('templates/ogn_rw_searchbox.php');
}


/*--------------------------------------------------
 Initiating action to add search box admin menu 
----------------------------------------------------*/
add_action('admin_menu', 'ogn_rw_load_admin_menu');

# add search box admin menu 
function ogn_rw_load_admin_menu() {
    add_options_page(__('Reservation Widget','menu-ogn-rw'), __('Oganro-Reservation','menu-ogn-rw'), 'manage_options', 'ogn_rw_reservation_admin', 'ogn_rw_load_settings_page');
}

# process admin menu
function ogn_rw_load_settings_page() {

	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	$ogn_rw_admin_erros = array();

	# save admin menu options
	if(isset($_POST['ogn_rw_srch_wdgt_opt']) && $_POST['ogn_rw_srch_wdgt_opt'] == "y"){
	
		
		$ogn_rw_options = array();
		$ogn_rw_h_fields = array();

		include('includes/ogn_rw_load_options.php');
		include('includes/ogn_rw_validate_inputs.php');
		 
		$ogn_rw_options['submit_url'] 			= $ogn_rw_post_sb_submit_url; 
		$ogn_rw_options['autocomplete_url'] 	= $ogn_rw_post_sb_autocomplete_url; 
		$ogn_rw_options['nights'] 				= $ogn_rw_post_sb_nights; 
		$ogn_rw_options['background_color'] 	= $ogn_rw_post_sb_background_color; 
		$ogn_rw_options['background_rgba'] 		= $ogn_rw_post_sb_background_rgba; 
		$ogn_rw_options['icon_color'] 			= $ogn_rw_post_sb_icon_color; 
		$ogn_rw_options['label_color'] 			= $ogn_rw_post_sb_label_color; 
		
		$ogn_rw_options['border_radius'] 		= $ogn_rw_post_sb_border_radius; 
		$ogn_rw_options['opacity'] 				= $ogn_rw_post_sb_opacity; 
		$ogn_rw_options['border_width'] 		= $ogn_rw_post_sb_border_width;
		$ogn_rw_options['border_color'] 		= $ogn_rw_post_sb_border_color; 
		$ogn_rw_options['search_box_width'] 	= $ogn_rw_post_sb_search_box_width; 
		$ogn_rw_options['bootstrap'] 			= $ogn_rw_post_sb_bootstrap; 

		#title options
		$ogn_rw_options['title'] 				= $ogn_rw_post_sb_title;
		$ogn_rw_options['title_color'] 			= $ogn_rw_post_sb_title_color; 
		$ogn_rw_options['title_size'] 			= $ogn_rw_post_sb_title_size; 

		#location field options
		$ogn_rw_options['location_placeholder'] = $ogn_rw_post_sb_location_placeholder;
		$ogn_rw_options['location_title'] 		= $ogn_rw_post_sb_location_title; 

		#date options
		$ogn_rw_options['checkin_title'] 		= $ogn_rw_post_sb_checkin_title;
		$ogn_rw_options['checkout_title'] 		= $ogn_rw_post_sb_checkout_title;

		#nights options
		$ogn_rw_options['nights_title']			= $ogn_rw_post_sb_nights_title;

		#rooms options
		$ogn_rw_options['rooms_title']			= $ogn_rw_post_sb_rooms_title;

		#search button options
		$ogn_rw_options['button_background_color']= $ogn_rw_post_sb_button_background_color;
		$ogn_rw_options['button_text_color'] 	= $ogn_rw_post_sb_button_text_color; 
		$ogn_rw_options['button_text'] 			= $ogn_rw_post_sb_button_text;

		if(isset($_POST['ogn_rw_opt_hfields']) && count($_POST['ogn_rw_opt_hfields'])){
			$count = 0 ;
			foreach ($_POST['ogn_rw_opt_hfields'] as $key => $value) {
				
				$ogn_rw_h_fields[$count]['name']	= sanitize_text_field($value['name']);
				$ogn_rw_h_fields[$count]['value']	= sanitize_text_field($value['value']);
				$count++;
			}
		}

		$ogn_rw_options['hidden_fields'] 		= json_encode($ogn_rw_h_fields); 

		$ogn_rw_values = json_encode($ogn_rw_options);

		update_option( 'ogn_rw_sb_options', $ogn_rw_values );
	}

	if(isset($_POST["ogn_rw_sb_default_opt"]) && $_POST["ogn_rw_sb_default_opt"] == "y"){
		$ogn_rw_empty_arr = json_encode(array());
		update_option( 'ogn_rw_sb_options', $ogn_rw_empty_arr );
	}

	include('includes/ogn_rw_load_options.php');

	ogn_rw_load_admin_css_and_js_files();
	
	include('templates/ogn_rw_admin_form.php');
}


/********************************** Helper functions **********************************/

function ogn_rw_load_css_files(){

	wp_enqueue_style( 'jquery-ui-datepicker' );
	wp_enqueue_style( 'ogn_rw_jquery_ui.css', plugins_url( '/css/ogn_rw_jquery_ui.css', __FILE__ ) );
	wp_enqueue_style( 'ogn_rw_bootstrap_min.css', plugins_url( '/css/ogn_rw_bootstrap_min.css', __FILE__ ) );
}

function ogn_rw_load_js_files($bootstrap = true){

	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_script('jquery-ui-autocomplete');
	if($bootstrap){
		wp_enqueue_script( 'ogn_rw_bootstrap_js', plugin_dir_url( __FILE__ ) . '/js/ogn_rw_bootstrap_min.js' );
	}
	wp_enqueue_script( 'ogn_rw_custom_script', plugin_dir_url( __FILE__ ) . '/js/ogn_rw_sb_script.js' );
}

function ogn_rw_load_admin_css_and_js_files(){

	wp_enqueue_script('jquery-ui-slider');
	wp_enqueue_style( 'ogn_rw_jquery_ui.css', plugins_url( '/css/ogn_rw_jquery_ui.css', __FILE__ ) );
	wp_enqueue_style( 'ogn_rw_sb_admin_css', plugins_url( '/css/ogn_rw_reservation_admin.css', __FILE__ ) );
	wp_enqueue_style( 'ogn_rw_sb_switch_css', plugins_url( '/css/ogn_rw_tinytools_toggleswitch_min.css', __FILE__ ) );
	wp_enqueue_script( 'ogn_rw_sb_color_selector', plugin_dir_url( __FILE__ ) . '/js/ogn_rw_jscolor.js' );
	wp_enqueue_script( 'ogn_rw_sb_toggleswitch', plugin_dir_url( __FILE__ ) . '/js/ogn_rw_tinytools_toggleswitch_min.js' );
	wp_enqueue_script( 'ogn_rw_sb_admin_custom_script', plugin_dir_url( __FILE__ ) . '/js/ogn_rw_admin_sb_script.js' );
}