<?php
	if ( ! defined( 'ABSPATH' ) ) exit;

	#load search box options
	$ogn_rw_field_options = json_decode(get_option( 'ogn_rw_sb_options', '' ));

	#set option values
	$ogn_rw_nights 				= (isset($ogn_rw_field_options->nights)) ? $ogn_rw_field_options->nights : '7' ;
	$ogn_rw_submit_url 			= (isset($ogn_rw_field_options->submit_url)) ? $ogn_rw_field_options->submit_url : 'http://www.demo.oganro.net/reservation/searchresult!callmdb.html' ;
	$ogn_rw_autocomplete_url 		= (isset($ogn_rw_field_options->autocomplete_url)) ? $ogn_rw_field_options->autocomplete_url : 'http://www.demo.oganro.net/common/autocompleter!getLocationList.html' ;
	$ogn_rw_background_color 		= (isset($ogn_rw_field_options->background_color)) ? $ogn_rw_field_options->background_color : '#d3d3d3' ;
	$ogn_rw_background_rgba 		= (isset($ogn_rw_field_options->background_rgba)) ? $ogn_rw_field_options->background_rgba : '211, 211, 211' ;
	$ogn_rw_icon_color 			= (isset($ogn_rw_field_options->icon_color)) ? $ogn_rw_field_options->icon_color : '#1a1a1a' ;
	
	$ogn_rw_hidden_fields 			= (isset($ogn_rw_field_options->hidden_fields)) ? json_decode($ogn_rw_field_options->hidden_fields) : array() ;
	$ogn_rw_border_radius 			= (isset($ogn_rw_field_options->border_radius)) ? $ogn_rw_field_options->border_radius : '20' ;
	$ogn_rw_opacity 				= (isset($ogn_rw_field_options->opacity)) ? $ogn_rw_field_options->opacity : '1' ;
	$ogn_rw_border_color 			= (isset($ogn_rw_field_options->border_color)) ? $ogn_rw_field_options->border_color : '#1a1a1a' ;
	$ogn_rw_label_color 			= (isset($ogn_rw_field_options->label_color)) ? $ogn_rw_field_options->label_color : '#1a1a1a' ;
	$ogn_rw_border_width 			= (isset($ogn_rw_field_options->border_width)) ? $ogn_rw_field_options->border_width : '05' ;
	$ogn_rw_search_box_width 		= (isset($ogn_rw_field_options->search_box_width)) ? $ogn_rw_field_options->search_box_width : '100' ;
	$ogn_rw_bootstrap 				= (isset($ogn_rw_field_options->bootstrap)) ? $ogn_rw_field_options->bootstrap : '1' ;

	#title options
	$ogn_rw_title 					= (isset($ogn_rw_field_options->nights)) ? $ogn_rw_field_options->title : 'Search' ;
	$ogn_rw_title_size   			= (isset($ogn_rw_field_options->title_size)) ? $ogn_rw_field_options->title_size : '20' ;
	$ogn_rw_title_color 			= (isset($ogn_rw_field_options->title_color)) ? $ogn_rw_field_options->title_color : '#1a1a1a' ;

	#location field options
	$ogn_rw_location_placeholder 			= (isset($ogn_rw_field_options->location_placeholder)) ? $ogn_rw_field_options->location_placeholder : 'Select your destination' ;
	$ogn_rw_location_title 				= (isset($ogn_rw_field_options->location_title)) ? $ogn_rw_field_options->location_title : 'Where are you going ?' ;

	#date options
	$ogn_rw_checkin_title 			= (isset($ogn_rw_field_options->checkin_title)) ? $ogn_rw_field_options->checkin_title : 'Check In' ;
	$ogn_rw_checkout_title 		= (isset($ogn_rw_field_options->checkout_title)) ? $ogn_rw_field_options->checkout_title : 'Checkout' ;
	$ogn_rw_date_format 			= (isset($ogn_rw_field_options->date_format)) ? $ogn_rw_field_options->date_format : 'dd-mm-yy' ;

	#nights options
	$ogn_rw_nights_title 			= (isset($ogn_rw_field_options->nights_title)) ? $ogn_rw_field_options->nights_title : 'Nights' ;

	#rooms options
	$ogn_rw_rooms_title 			= (isset($ogn_rw_field_options->rooms_title)) ? $ogn_rw_field_options->rooms_title : 'Rooms' ;

	#search button options
	$ogn_rw_button_text 			= (isset($ogn_rw_field_options->button_text)) ? $ogn_rw_field_options->button_text : 'search' ;
	$ogn_rw_button_background_color= (isset($ogn_rw_field_options->button_background_color)) ? $ogn_rw_field_options->button_background_color : '#1a1a1a' ;
	$ogn_rw_button_text_color		= (isset($ogn_rw_field_options->button_text_color)) ? $ogn_rw_field_options->button_text_color : '#FFFFFF' ;
