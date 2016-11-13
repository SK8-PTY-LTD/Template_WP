<?php
/**
 * Plugin Name: Change Permalink Helper
 * Plugin URI:  http://inspyde.com/
 * Text Domain: changepermalinkhelper
 * Domain Path: /languages
 * Description: It checks the Permalink and redirects to the new URL, if it doesn't exist. It sends the header message "moved permanently 301"
 * Version:     1.0.0
 * Author:      Frank Bültge
 * Author URI:  http://bueltge.de/
 * License:     GPLv3+
 */

/**
License:
==============================================================================
Copyright 2010 - 2016 Frank Bueltge  (email : f.bueltge@inpsyde.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

Requirements:
==============================================================================
This plugin requires WordPress >= 2.8 and tested with PHP Interpreter >= 5.2.9
*/

//avoid direct calls to this file, because now WP core and framework has been used
if ( !function_exists( 'add_action' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

if ( ! class_exists( 'ChangePermalinkHelper' ) ) {
	class ChangePermalinkHelper {
		
		/**
		 * Constructor
		 */
		function __construct() {
			
			add_action( 'plugins_loaded', array( $this, 'onLoad' ) );
		}
		
		
		function onLoad() {
			
			if ( is_admin() )
				return;
				
			add_action( 'template_redirect', array( $this, 'is404' ) );
		}
		
		/**
		 * return header message
		 */
		function is404() {
			global $wpdb;
			
			if ( ! is_404() )
				return;
			
			$slug = htmlspecialchars( basename( $_SERVER['REQUEST_URI'] ) );
			$id = $wpdb->get_var( 
					$wpdb->prepare( "
						SELECT ID 
						FROM $wpdb->posts
						WHERE post_name = '%s'
						AND post_status = 'publish'
					", $slug )
				);
			
			if ( $id ) {
				$url = get_permalink( $id );
				header( 'HTTP/1.1 301 Moved Permanently' );
				header( 'Location: ' . $url );
			} else {
				return true;
			}
			
		}
		
	} // end class
	
	$ChangePermalinkHelper = new ChangePermalinkHelper();
}
