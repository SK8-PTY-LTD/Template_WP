<?php
/**
 * Plugin Name: Car Rental Booking Engine by Hispacar
 * Text Domain: c-h-hispacar
 * Plugin URI: http://www.hispacar.com/wordpress-plugins/search/en/
 * Description: Add a search box to your website so your visitors can search and compare car hire prices worldwide.
 * Version: 1.1
 * Author: Hispacar
 * Author URI: http://www.hispacar.com
 * License: GPL2
 */


/*  Copyright 2014  Hispacar.com

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



defined('ABSPATH') or die("No script kiddies please!");



// Translations domain
function c_h_h_init(){

	// Localization
	load_plugin_textdomain('c-h-hispacar', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

// Add actions
add_action('plugins_loaded', 'c_h_h_init');







////////////////////////////////////////////////////////////// WIDGET /////////////////////////////////////////////////
class chh_plugin_widget extends WP_Widget {
	 
	// constructor
	function chh_plugin_widget() {
		parent::WP_Widget(false, $name = 'Car Rental Booking Engine by Hispacar' );
	}
 


	// widget form creation
	function form($instance) {
	 
	// Check values
	if( $instance) {
	     $title = esc_attr($instance['title']);
	} else {
	     $title = '';
	}
	?>
	 
	<p>
	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title', 'c-h-hispacar'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
	</p>
	 


	<?php
	}
 

	// update widget
	function update($new_instance, $old_instance) {
	      $instance = $old_instance;
	      // Fields
	      $instance['title'] = strip_tags($new_instance['title']);
	     return $instance;
	}

 
	// display widget
	function widget($args, $instance) {
	   extract( $args );
	   // these are the widget options
	   $title = apply_filters('widget_title', $instance['title']);
	   echo $before_widget;
	   // Display the widget
	   echo '<div class="widget-text wp_widget_plugin_box">';
	 
	   // Check if title is set
	   if ( $title ) {
	      echo $before_title . $title . $after_title;
	   }
		echo c_h_h_render('half');
		echo '</div>';
		echo $after_widget;
	}
}
	

// register widget
add_action('widgets_init', create_function('', 'return register_widget("chh_plugin_widget");'));

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////





////////////////////////////// SETTINGS LINK //////////////////////////////////////////////////////////////////////////////

function c_h_h_plugin_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=car-hire-hispacar">' . __('Settings', 'c-h-hispacar') . '</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
 
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'c_h_h_plugin_settings_link' );

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////





////////////////////////////// SETTINGS PAGE ///////////////////////////////////////////////////////////////////////////////



// Create the page
add_action('admin_menu', 'car_hire_hispacar_admin_add_page');

function car_hire_hispacar_admin_add_page() {
	add_options_page('Car Rental Booking Engine by Hispacar Settings', 'Car Rental Booking Engine by Hispacar', 'manage_options', 'car-hire-hispacar', 'car_hire_hispacar_options_page');
}


function car_hire_hispacar_options_page() {

	echo "\n<div>";
	echo "\n<h2>" . __('Car Rental Booking Engine by Hispacar Settings', 'c-h-hispacar') . "</h2>";
	echo "\n<form action=\"options.php\" method=\"post\">";
	
	settings_fields('car_hire_hispacar_options');
	do_settings_sections('car-hire-hispacar');
	
	//echo "\n<input name=\"Submit\" type=\"submit\" value=\"" . esc_attr_e('Save Changes') . "\" />";
	echo "\n<br/>";
	echo "\n<input name=\"Submit\" type=\"submit\" value=\"" . __('Save Changes', 'c-h-hispacar') . "\" />";
	echo "\n</form></div>";
 
}



// Create the settings
add_action('admin_init', 'car_hire_hispacar_admin_init');

function car_hire_hispacar_admin_init(){
	register_setting( 'car_hire_hispacar_options', 'c_h_h_options', 'c_h_h_options_validate' );
	add_settings_section('c_h_h_main','', 'c_h_h_section_text', 'car-hire-hispacar');


	// Add the fields
	add_settings_field('c_h_h_title', __('Title', 'c-h-hispacar'), 'c_h_h_title_setting', 'car-hire-hispacar', 'c_h_h_main');
	add_settings_field('c_h_h_af_cod', __('Affiliate ID', 'c-h-hispacar'), 'c_h_h_af_cod_setting', 'car-hire-hispacar', 'c_h_h_main');
	add_settings_field('c_h_h_lang', __('Search box language', 'c-h-hispacar'), 'c_h_h_lang_setting', 'car-hire-hispacar', 'c_h_h_main');
	add_settings_field('c_h_h_pickup', __('Default location id (optional)', 'c-h-hispacar'), 'c_h_h_pickup_id_setting', 'car-hire-hispacar', 'c_h_h_main');
	add_settings_field('c_h_h_width', __('Width', 'c-h-hispacar') . ' (%)', 'c_h_h_width_setting', 'car-hire-hispacar', 'c_h_h_main');
	add_settings_field('c_h_h_color', __('Foreground color', 'c-h-hispacar') . ' (#rrggbb)', 'c_h_h_color_setting', 'car-hire-hispacar', 'c_h_h_main');
	add_settings_field('c_h_h_background_color', __('Background color', 'c-h-hispacar') . ' (#rrggbb)', 'c_h_h_background_color_setting', 'car-hire-hispacar', 'c_h_h_main');
	add_settings_field('c_h_h_button_color', __('Button color', 'c-h-hispacar') . ' (#rrggbb)', 'c_h_h_button_color_setting', 'car-hire-hispacar', 'c_h_h_main');
	add_settings_field('c_h_h_button_text_color', __('Button text color', 'c-h-hispacar') . ' (#rrggbb)', 'c_h_h_button_text_color_setting', 'car-hire-hispacar', 'c_h_h_main');
	add_settings_field('c_h_h_border_width', __('Border width', 'c-h-hispacar'), 'c_h_h_border_width_setting', 'car-hire-hispacar', 'c_h_h_main');
	add_settings_field('c_h_h_border_radius', __('Border radius', 'c-h-hispacar'), 'c_h_h_border_radius_setting', 'car-hire-hispacar', 'c_h_h_main');
	
}




// Section text callback
function c_h_h_section_text() {
	echo '';
}


// Fields callbacks
function c_h_h_title_setting() {
	$options = get_option('c_h_h_options');
	echo "<input id='title_text_string' name='c_h_h_options[title]' size='40' type='text' value='{$options['title']}' />";
}



function c_h_h_af_cod_setting() {
	$options = get_option('c_h_h_options');
	$lang = ($options['lang']) ? $options['lang'] : 'en';
	echo "<input id='af_cod_text_string' name='c_h_h_options[af_cod]' size='40' type='text' value='{$options['af_cod']}' /><br/><small><a href='http://www.hispacar.com/wordpress-plugins/search/" . $lang . "/register/' target='_blank'>" . __('How do I get one?', 'c-h-hispacar') . "</a></small>";
}


function c_h_h_lang_setting() {
	$options = get_option('c_h_h_options');
	echo "<select name='c_h_h_options[lang]' id='lang'>";
	$langs = array('en', 'es', 'fr', 'de', 'it', 'nl');

	foreach($langs as $lang){
		$selected = ($options['lang'] == $lang) ? 'selected="selected"' : '';
		echo "<option value='" . $lang . "' $selected>" . $lang . "</option>";
	}

	echo "</select>";
}


function c_h_h_pickup_id_setting() {
	$options = get_option('c_h_h_options');
	echo "<input id='pickup_id' name='c_h_h_options[pickup_id]' size='15' type='text' value='{$options['pickup_id']}' /><br/><small><a href='#'onclick=\"javascript:window.open('http://www.hispacar.com/affiliate/id_selector/id_selector.php?lang=" . $options['lang'] . "', '', 'width=520, height=320')\">" . __('Help me find the id', 'c-h-hispacar') . "</a></small>";
}


function c_h_h_width_setting() {
	$options = get_option('c_h_h_options');
	echo "<input id='width' name='c_h_h_options[width]' size='3' type='text' value='{$options['width']}' />";
}


function c_h_h_color_setting() {
	$options = get_option('c_h_h_options');
	echo "#<input id='color' name='c_h_h_options[color]' size='6' type='text' value='{$options['color']}' /><br/><small><a href='#' onclick=\"javascript:window.open('http://www.hispacar.com/affiliate/colorpicker/colorpicker.php?lang=" . $options['lang'] . "', '', 'width=320, height=300')\">" . __('Help me find the color code', 'c-h-hispacar') . "</a></small>";
}


function c_h_h_background_color_setting() {
	$options = get_option('c_h_h_options');
	echo "#<input id='background_color' name='c_h_h_options[background_color]' size='6' type='text' value='{$options['background_color']}' /><br/><small><a href='#' onclick=\"javascript:window.open('http://www.hispacar.com/affiliate/colorpicker/colorpicker.php?lang=" . $options['lang'] . "', '', 'width=320, height=300')\">" . __('Help me find the color code', 'c-h-hispacar') . "</a></small>";
}


function c_h_h_button_color_setting() {
	$options = get_option('c_h_h_options');
	echo "#<input id='color' name='c_h_h_options[button_color]' size='6' type='text' value='{$options['button_color']}' /><br/><small><a href='#' onclick=\"javascript:window.open('http://www.hispacar.com/affiliate/colorpicker/colorpicker.php?lang=" . $options['lang'] . "', '', 'width=320, height=300')\">" . __('Help me find the color code', 'c-h-hispacar') . "</a></small>";
}

function c_h_h_button_text_color_setting() {
	$options = get_option('c_h_h_options');
	echo "#<input id='color' name='c_h_h_options[button_text_color]' size='6' type='text' value='{$options['button_text_color']}' /><br/><small><a href='#' onclick=\"javascript:window.open('http://www.hispacar.com/affiliate/colorpicker/colorpicker.php?lang=" . $options['lang'] . "', '', 'width=320, height=300')\">" . __('Help me find the color code', 'c-h-hispacar') . "</a></small>";
}


function c_h_h_border_width_setting() {
	$options = get_option('c_h_h_options');
	echo "<input id='border_width' name='c_h_h_options[border_width]' size='3' type='text' value='{$options['border_width']}' /><small>px</small>";
}


function c_h_h_border_radius_setting() {
	$options = get_option('c_h_h_options');
	echo "<input id='border_radius' name='c_h_h_options[border_radius]' size='3' type='text' value='{$options['border_radius']}' /><small>px</small>";
}



// Validation function 
function c_h_h_options_validate($input) {


	// Title
	$newinput['title'] = trim($input['title']);

	if(!preg_match('/^[a-z0-9 ]{1,32}$/i', $newinput['title'])) {
		$newinput['title'] = '';
	}


	// Af ID
	$newinput['af_cod'] = trim($input['af_cod']);
	if(!preg_match('/^[a-z0-9]{1,16}$/i', $newinput['af_cod'])) {
		$newinput['af_cod'] = '';
	}


	// Lang
	$newinput['lang'] = trim($input['lang']);
	if(!preg_match('/^[a-z]{2}$/i', $newinput['lang'])) {
		$newinput['lang'] = '';
	}


	// pickup_id
	$newinput['pickup_id'] = trim($input['pickup_id']);
	if(!preg_match('/^[0-9]{1,16}$/i', $newinput['pickup_id'])) {
		$newinput['pickup_id'] = '';
	}


	// width
	$newinput['width'] = trim($input['width']);
	if(!preg_match('/^[0-9]{1,3}$/i', $newinput['width']) || $newinput['width']>100) {
		$newinput['width'] = '';
	}


	// color
	$newinput['color'] = trim($input['color']);
	if(!preg_match('/^[a-f0-9]{6}$/i', $newinput['color'])) {
		$newinput['color'] = '';
	}

	// background color
	$newinput['background_color'] = trim($input['background_color']);
	if(!preg_match('/^[a-f0-9]{6}$/i', $newinput['background_color'])) {
		$newinput['background_color'] = '';
	}


	// button color
	$newinput['button_color'] = trim($input['button_color']);
	if(!preg_match('/^[a-f0-9]{6}$/i', $newinput['button_color'])) {
		$newinput['button_color'] = '';
	}

	// button text color
	$newinput['button_text_color'] = trim($input['button_text_color']);
	if(!preg_match('/^[a-f0-9]{6}$/i', $newinput['button_text_color'])) {
		$newinput['button_text_color'] = '';
	}


	// border width
	$newinput['border_width'] = trim($input['border_width']);
	if(!preg_match('/^[0-9]{1,3}$/i', $newinput['border_width'])) {
		$newinput['border_width'] = '';
	}


	// border radius
	$newinput['border_radius'] = trim($input['border_radius']);
	if(!preg_match('/^[0-9]{1,3}$/i', $newinput['border_radius'])) {
		$newinput['border_radius'] = '';
	}



	return $newinput;

}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////







////////////////////////////////////////////////////// SHORTCODE ////////////////////////////////////////////////////////////

function c_h_h_short($atts){
	return c_h_h_render('full');
}
add_shortcode( 'car_hire_hispacar', 'c_h_h_short' );




/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////






/////////////////////////////////////////////////////// RENDER //////////////////////////////////////////////////////////////

function c_h_h_render($type='full'){

	$options = get_option('c_h_h_options');

	$af_cod				= $options['af_cod'];
	$lang				= ($options['lang']) ? $options['lang'] : 'en';
	$pickup_id			= $options['pickup_id'];
	$width				= ($options['width']) ? $options['width'] : '100';
	$color				= ($options['color']) ? $options['color'] : '000';
	$background_color	= ($options['background_color']) ? $options['background_color'] : 'fff';
	$button_color		= ($options['button_color']) ? $options['button_color'] : '000';
	$button_text_color	= ($options['button_text_color']) ? $options['button_text_color'] : 'fff';
	$border_width		= ($options['border_width']) ? $options['border_width'] : '0';
	$border_radius		= ($options['border_radius']) ? $options['border_radius'] : '0';
	$title				= $options['title'];

	$defPickId = false;
	$defPickName = false;

	$plugin_url = trailingslashit( get_bloginfo('wpurl') ).PLUGINDIR.'/'. dirname( plugin_basename(__FILE__) );

	
	// Do we have a default location?
	if($pickup_id){

		// autocomplete json load
		$autoJSON = file_get_contents($plugin_url . "/autocomplete_$lang.json");
		$autoJSON = substr($autoJSON, strpos($autoJSON, '(')+1, strrpos($autoJSON, ')')-strpos($autoJSON, '(')-1);

		$autoJSON = json_decode($autoJSON);

		foreach($autoJSON as $jsonEntry){
			if($jsonEntry->value==$pickup_id){
				$defPickId = $pickup_id;
				$defPickName = $jsonEntry->label;
			}
		}
	}


	switch($type){
		case 'full':
			$formStyle = 'c_c_c_full_width';
		break;

		case 'half':
			$formStyle = 'c_c_c_half_width';
		break;
	}



	$template = '';
	$template .= '<div id="c_h_h_cont" style="width:' . $width . '%">';
	$template .= "\n" . '<div id="search_box" style="border-width:' . $border_width . 'px;border-color:#' . $color . ';border-radius:' . $border_radius . 'px;background-color:#' . $background_color . ';color:#' . $color . '">';
	$template .= "\n" . '<form method="post" class="' . $formStyle . '" action="http://www.hispacar.com/affiliate/redir.php" target="_blank" onsubmit="javascript:return c_h_h_validate_form()">';
	$template .= "\n" . '<h6>' . $title . '</h6>';
	$template .= "\n" . '<div id="validation_errors"><span id="validation_errors_txt"></span><a href="#" onclick="jQuery(\'#validation_errors\').hide();return false" class="close">X</a></div>';
	$template .= "\n" . '<input id="pu_location" name="pu_location" class="loc" type="text" placeholder="' . c_h_h_t('write_location', $lang) . '" onclick="javascript:this.value=\'\'" value="' . $defPickName . '"/>';
	$template .= "\n" . '<input type="hidden" id="pu_location_id" name="pu_location_id" value="' . $defPickId . '"/>';
	$template .= "\n" . '<input type="hidden" id="c_h_h_lang" value="' . $lang . '" name="lang"/>';
	$template .= "\n" . '<input type="hidden" id="af_cod" name="af_cod" value="' . $af_cod . '"/>';
	$template .= "\n" . '<div id="dif_ret_block"><input type="checkbox" id="dif_ret_switch" onclick="javascript:jQuery(\'#do_location\').toggle()"/> ' . c_h_h_t('return_other', $lang) . '</div>';
	$template .= "\n" . '<input id="do_location" name="do_location" class="loc" placeholder="' . c_h_h_t('write_location', $lang) . '" onclick="javascript:this.value=\'\'" value=""/>';
	$template .= "\n" . '<input type="hidden" id="do_location_id" name="do_location_id"/>';
	$template .= "\n" . '<div class="sep1"></div>';
	$template .= "\n" . '<input type="text" id="pu_date" name="pu_date" class="date" placeholder="' . c_h_h_t('start_date', $lang) . '"/>';
	$template .= "\n" . '<select id="pu_time" name="pu_time" class="time">';
	$template .= "\n" . '	<option value="00:00">00:00</option>';
	$template .= "\n" . '	<option value="00:30">00:30</option>';
	$template .= "\n" . '	<option value="01:00">01:00</option>';
	$template .= "\n" . '	<option value="01:30">01:30</option>';
	$template .= "\n" . '	<option value="02:00">02:00</option>';
	$template .= "\n" . '	<option value="02:30">02:30</option>';
	$template .= "\n" . '	<option value="03:00">03:00</option>';
	$template .= "\n" . '	<option value="03:30">03:30</option>';
	$template .= "\n" . '	<option value="04:00">04:00</option>';
	$template .= "\n" . '	<option value="04:30">04:30</option>';
	$template .= "\n" . '	<option value="05:00">05:00</option>';
	$template .= "\n" . '	<option value="05:30">05:30</option>';
	$template .= "\n" . '	<option value="06:00">06:00</option>';
	$template .= "\n" . '	<option value="06:30">06:30</option>';
	$template .= "\n" . '	<option value="07:00">07:00</option>';
	$template .= "\n" . '	<option value="07:30">07:30</option>';
	$template .= "\n" . '	<option value="08:00">08:00</option>';
	$template .= "\n" . '	<option value="08:30">08:30</option>';
	$template .= "\n" . '	<option value="09:00">09:00</option>';
	$template .= "\n" . '	<option value="09:30">09:30</option>';
	$template .= "\n" . '	<option value="10:00" selected="selected">10:00</option>';
	$template .= "\n" . '	<option value="10:30">10:30</option>';
	$template .= "\n" . '	<option value="11:00">11:00</option>';
	$template .= "\n" . '	<option value="11:30">11:30</option>';
	$template .= "\n" . '	<option value="12:00">12:00</option>';
	$template .= "\n" . '	<option value="12:30">12:30</option>';
	$template .= "\n" . '	<option value="13:00">13:00</option>';
	$template .= "\n" . '	<option value="13:30">13:30</option>';
	$template .= "\n" . '	<option value="14:00">14:00</option>';
	$template .= "\n" . '	<option value="14:30">14:30</option>';
	$template .= "\n" . '	<option value="15:00">15:00</option>';
	$template .= "\n" . '	<option value="15:30">15:30</option>';
	$template .= "\n" . '	<option value="16:00">16:00</option>';
	$template .= "\n" . '	<option value="16:30">16:30</option>';
	$template .= "\n" . '	<option value="17:00">17:00</option>';
	$template .= "\n" . '	<option value="17:30">17:30</option>';
	$template .= "\n" . '	<option value="18:00">18:00</option>';
	$template .= "\n" . '	<option value="18:30">18:30</option>';
	$template .= "\n" . '	<option value="19:00">19:00</option>';
	$template .= "\n" . '	<option value="19:30">19:30</option>';
	$template .= "\n" . '	<option value="20:00">20:00</option>';
	$template .= "\n" . '	<option value="20:30">20:30</option>';
	$template .= "\n" . '	<option value="21:00">21:00</option>';
	$template .= "\n" . '	<option value="21:30">21:30</option>';
	$template .= "\n" . '	<option value="22:00">22:00</option>';
	$template .= "\n" . '	<option value="22:30">22:30</option>';
	$template .= "\n" . '	<option value="23:00">23:00</option>';
	$template .= "\n" . '	<option value="23:30">23:30</option>';
	$template .= "\n" . '</select>';
	$template .= "\n" . '<div class="sep2"></div>';
	$template .= "\n" . '<input type="text" id="do_date" name="do_date" class="date" placeholder="' . c_h_h_t('end_date', $lang) . '"/>';
	$template .= "\n" . '<select id="do_time" name="do_time" class="time">';
	$template .= "\n" . '	<option value="00:00">00:00</option>';
	$template .= "\n" . '	<option value="00:30">00:30</option>';
	$template .= "\n" . '	<option value="01:00">01:00</option>';
	$template .= "\n" . '	<option value="01:30">01:30</option>';
	$template .= "\n" . '	<option value="02:00">02:00</option>';
	$template .= "\n" . '	<option value="02:30">02:30</option>';
	$template .= "\n" . '	<option value="03:00">03:00</option>';
	$template .= "\n" . '	<option value="03:30">03:30</option>';
	$template .= "\n" . '	<option value="04:00">04:00</option>';
	$template .= "\n" . '	<option value="04:30">04:30</option>';
	$template .= "\n" . '	<option value="05:00">05:00</option>';
	$template .= "\n" . '	<option value="05:30">05:30</option>';
	$template .= "\n" . '	<option value="06:00">06:00</option>';
	$template .= "\n" . '	<option value="06:30">06:30</option>';
	$template .= "\n" . '	<option value="07:00">07:00</option>';
	$template .= "\n" . '	<option value="07:30">07:30</option>';
	$template .= "\n" . '	<option value="08:00">08:00</option>';
	$template .= "\n" . '	<option value="08:30">08:30</option>';
	$template .= "\n" . '	<option value="09:00">09:00</option>';
	$template .= "\n" . '	<option value="09:30">09:30</option>';
	$template .= "\n" . '	<option value="10:00" selected="selected">10:00</option>';
	$template .= "\n" . '	<option value="10:30">10:30</option>';
	$template .= "\n" . '	<option value="11:00">11:00</option>';
	$template .= "\n" . '	<option value="11:30">11:30</option>';
	$template .= "\n" . '	<option value="12:00">12:00</option>';
	$template .= "\n" . '	<option value="12:30">12:30</option>';
	$template .= "\n" . '	<option value="13:00">13:00</option>';
	$template .= "\n" . '	<option value="13:30">13:30</option>';
	$template .= "\n" . '	<option value="14:00">14:00</option>';
	$template .= "\n" . '	<option value="14:30">14:30</option>';
	$template .= "\n" . '	<option value="15:00">15:00</option>';
	$template .= "\n" . '	<option value="15:30">15:30</option>';
	$template .= "\n" . '	<option value="16:00">16:00</option>';
	$template .= "\n" . '	<option value="16:30">16:30</option>';
	$template .= "\n" . '	<option value="17:00">17:00</option>';
	$template .= "\n" . '	<option value="17:30">17:30</option>';
	$template .= "\n" . '	<option value="18:00">18:00</option>';
	$template .= "\n" . '	<option value="18:30">18:30</option>';
	$template .= "\n" . '	<option value="19:00">19:00</option>';
	$template .= "\n" . '	<option value="19:30">19:30</option>';
	$template .= "\n" . '	<option value="20:00">20:00</option>';
	$template .= "\n" . '	<option value="20:30">20:30</option>';
	$template .= "\n" . '	<option value="21:00">21:00</option>';
	$template .= "\n" . '	<option value="21:30">21:30</option>';
	$template .= "\n" . '	<option value="22:00">22:00</option>';
	$template .= "\n" . '	<option value="22:30">22:30</option>';
	$template .= "\n" . '	<option value="23:00">23:00</option>';
	$template .= "\n" . '	<option value="23:30">23:30</option>';
	$template .= "\n" . '</select>';
	$template .= "\n" . '<div class="sep3"></div>';
	$template .= "\n" . '<input type="submit" id="doSubmit" value="' . c_h_h_t('search_cars', $lang) . '" class="butt" style="background-color:#' . $button_color . ';border-color:#' . $button_color . ';color:#' . $button_text_color . '"/>';
	$template .= "\n" . '</form>';
	$template .= "\n" . '</div>';
	$template .= "\n" . '<div class="' . $formStyle . '"><div class="powered_hispacar">powered by: <img src="' . $plugin_url . '/hispacar_logo.png" alt="Hispacar"/></div></div>';
	$template .= "\n" . '</div>';

	return  "$template";
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////




////////////////////////////////////////////////////// SCRIPTS //////////////////////////////////////////////////////////////

add_action('wp_print_scripts', 'WPC_H_H_scripts');


function WPC_H_H_scripts(){

	$options = get_option('c_h_h_options');
	$lang = ($options['lang']) ? $options['lang'] : 'en';

	$plugin_url = trailingslashit( get_bloginfo('wpurl') ).PLUGINDIR.'/'. dirname( plugin_basename(__FILE__) );

	$error_txt_review_highlighted	= c_h_h_t('error_txt_review_highlighted', $lang);
	$error_txt_type_city			= c_h_h_t('error_txt_type_city', $lang);
	$error_txt_select_start_date	= c_h_h_t('error_txt_select_start_date', $lang);
	$error_txt_select_end_date		= c_h_h_t('error_txt_select_end_date', $lang);

	if (!is_admin()){
		wp_enqueue_script('jquery');
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'jquery-ui-autocomplete' );
		wp_enqueue_script('c_h_h_script', $plugin_url.'/common.js', array('jquery', 'jquery-ui-datepicker', 'jquery-ui-autocomplete'));
		wp_localize_script( 'c_h_h_script', 'c_h_h_script', array(
			'lang' => $lang,
			'plugin_url' => $plugin_url,
			'error_txt_review_highlighted' => $error_txt_review_highlighted,
			'error_txt_type_city' => $error_txt_type_city,
			'error_txt_select_start_date' => $error_txt_select_start_date,
			'error_txt_select_end_date' => $error_txt_select_end_date,
		));
		wp_enqueue_style('c_h_h_jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/smoothness/jquery-ui.css');
		wp_enqueue_style( 'c_h_h_style', $plugin_url.'/style.css' );
	}
}


function c_h_h_t($label_key, $lang){

	static $labels;
	
	if(!is_array($labels)){
		$rawText = "
			write_location|Escriba ciudad|Entrer la ville|Stadt|Type in city|Nome della Città|Vul plaats in
			start_date|Fecha de inicio|Date de début|Mietbeginn|Start date|Data di inizio|Begindatum
			end_date|Fecha de fin|Date de fin|Mietende|End date|Data di fine|Einddatum
			return_other|Devolver en un punto diferente|Lieu de retour différent|Rückgabe an einem anderen Ort|Return to a different location|Riconsegna in altro luogo|Op andere locatie inleveren
			search_cars|Buscar coches|Chercher une voiture|Fahrzeug suchen|Search for cars|Cercare un auto|Auto's zoeken
			error_txt_review_highlighted|Revise los campos resaltados|Veuillez modifier les champs en surbrillance|Bitte markierte Felder prüfen|Please review the highlighted fields|Controlla i campi evidenziati|Controleer de geaccentueerde velden
			error_txt_type_city|Escriba ciudad|Veuillez saisir une ville|Bitte geben Sie eine Stadt|Please type in a city|Digita il nome di una città|Typ hier de naam van een plaats
			error_txt_select_start_date|Seleccione una fecha de inicio|Veuillez sélectionner une date de début|Bitte wählen Sie ein Anfangsdatum|Please select a start date|Seleziona una data di inizio noleggio|Kies een begindatum
			error_txt_select_end_date|Seleccione una fecha de fin|Veuillez sélectionner une date de fin|Bitte wählen Sie ein Enddatum|Please select an end date|Seleziona una data di fine noleggio|Kies een einddatum";

		$lines = explode("\n", $rawText);
		$labels = array();

		foreach($lines as $line){
			$line = trim($line);
			if(!$line) continue;

			$parts = explode("|", $line);
			$label = array();
			$key = $parts[0];
			$label['es'] = $parts[1];
			$label['fr'] = $parts[2];
			$label['de'] = $parts[3];
			$label['en'] = $parts[4];
			$label['it'] = $parts[5];
			$label['nl'] = $parts[6];

			$labels[$key] = $label;

		}

	}

	return $labels[$label_key][$lang];
}