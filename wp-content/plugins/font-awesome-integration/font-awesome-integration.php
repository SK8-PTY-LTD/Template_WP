<?php
/**
 * Plugin Name: Font Awesome Integration
 * Plugin URI: http://www.jumptoweb.com
 * Description: This plugin integrate the Font Awesome library with your wordpress installation.
 * Version: 3
 * Author: Manuel Costales
 * Author URI: http://www.manuelcostales.com
 */
defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );
/*add javascripts to the header*/
add_action('wp_head', 'add_fawesome_mc');
function add_fawesome_mc() {
	wp_enqueue_script( 'font-awesome', 'https://use.fontawesome.com/4ef6ac5e6d.js' );
}

//enabling the ability to enter shortcodes into widgets
add_filter('widget_text', 'do_shortcode');

add_shortcode( 'fawesome', 'fawesome_shortcode_mc' );
function fawesome_shortcode_mc( $atts ) {
/*
 * Attributes availables
 * aclass -> classes to use in the <a> tag
 * target -> target of the <a> tag (_blank, _self, _parent, _top)
 * href   -> link to use in the <a> tag
 * iclass -> classes to use in the <i> tag
 *
 * All this attributes but the iclass are optionals
 */
	if (is_array($atts) && $atts['iclass']){ 
		$iclass = $atts['iclass'];}
	else {
		$iclass = "fa-flag";}

	if (is_array($atts) && $atts['aclass']){ 
		$aclass = $atts['aclass'];}

	if (is_array($atts) && $atts['target']){ 
		$target = $atts['target'];}

	if (is_array($atts) && $atts['href']){ 
		$href = $atts['href'];}

	return isset($href) ? 
		"<a class=' ".$aclass."' href='".$href."' target=' ".$target."'><i class='fa ".$iclass."'></i></a>"
		: 
		"<i class='fa ".$iclass."'></i>";
}
