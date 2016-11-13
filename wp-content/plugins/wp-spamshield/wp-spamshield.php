<?php
/*
Plugin Name: WP-SpamShield
Plugin URI: https://www.redsandmarketing.com/plugins/wp-spamshield/
Description: An extremely powerful and user-friendly all-in-one anti-spam plugin that <strong>eliminates comment spam, trackback spam, contact form spam, and registration spam</strong>. No CAPTCHA's, challenge questions, or other inconvenience to website visitors. Enjoy running a WordPress site without spam! Includes a spam-blocking contact form feature.
Author: Scott Allen
Version: 1.9.9.5
Author URI: https://www.redsandmarketing.com/
Text Domain: wp-spamshield
Domain Path: /languages
License: GPLv2
*/

/*  Copyright 2014-2016 Scott Allen ( www [dot] redsandmarketing [dot] com )

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
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


/* PLUGIN - BEGIN */

/* Make sure plugin remains secure if called directly */
if( !defined( 'ABSPATH' ) ) {
	if( !headers_sent() ) { @header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden',TRUE,403); }
	die( 'ERROR: This plugin requires WordPress and will not function if called directly.' );
}

define( 'WPSS_VERSION', '1.9.9.5' );
define( 'WPSS_REQUIRED_WP_VERSION', '4.0' );
define( 'WPSS_REQUIRED_PHP_VERSION', '5.3' );

if( !defined( 'WPSS_DEBUG' ) ) 					{ define( 'WPSS_DEBUG', FALSE ); } /* Do not change value unless developer asks you to - for debugging only. Change in wp-config.php. */
if( TRUE !== WPSS_DEBUG && TRUE !== WP_DEBUG )	{ @ini_set('display_errors',0); @error_reporting(0); } /* Prevents error display, but will display errors if WP_DEBUG turned on. */

rs_wpss_getenv();

/**
 *  Setting important URL and PATH constants so the plugin can find things
 */
if( !defined( 'WPSS_PHP_VERSION' ) ) 			{ define( 'WPSS_PHP_VERSION', PHP_VERSION ); }
if( !defined( 'WPSS_WP_VERSION' ) ) 			{ global $wp_version; define( 'WPSS_WP_VERSION', $wp_version ); }
if( !defined( 'WPSS_EDGE' ) ) 					{ define( 'WPSS_EDGE', FALSE ); }
if( !defined( 'WPSS_DEBUG_SERVER_NAME' ) )		{ define( 'WPSS_DEBUG_SERVER_NAME', '.redsandmarketing.com' ); }
if( !defined( 'WPSS_DEBUG_SERVER_NAME_REV' ) )	{ define( 'WPSS_DEBUG_SERVER_NAME_REV', strrev( WPSS_DEBUG_SERVER_NAME ) ); }
if( !defined( 'WPSS_MDBUG_SERVER_NAME' ) ) 		{ define( 'WPSS_MDBUG_SERVER_NAME', '.redsandmarketing.com' ); }
if( !defined( 'WPSS_MDBUG_SERVER_NAME_REV' ) )	{ define( 'WPSS_MDBUG_SERVER_NAME_REV', strrev( WPSS_MDBUG_SERVER_NAME ) ); }
if( !defined( 'WPSS_EOL' ) ) 					{ $wpss_eol	= defined( 'PHP_EOL' ) ? PHP_EOL : rs_wpss_eol(); define( 'WPSS_EOL', $wpss_eol ); }
if( !defined( 'WPSS_DS' ) ) 					{ $wpss_ds	= defined( 'DIRECTORY_SEPARATOR' ) ? DIRECTORY_SEPARATOR : rs_wpss_ds(); define( 'WPSS_DS', $wpss_ds ); }
if( !defined( 'WPSS_MEMORY_LIMIT' ) ) 			{ define( 'WPSS_MEMORY_LIMIT', '128M' ); }
if( !defined( 'WPSS_SITE_URL' ) ) 				{ define( 'WPSS_SITE_URL', untrailingslashit( strtolower( home_url() ) ) ); }
if( !defined( 'WPSS_SITE_WP_URL' ) ) 			{ define( 'WPSS_SITE_WP_URL', untrailingslashit( strtolower( site_url() ) ) ); }
if( !defined( 'WPSS_SITE_DOMAIN' ) ) 			{ define( 'WPSS_SITE_DOMAIN', rs_wpss_get_domain( WPSS_SITE_URL ) ); }
if( !defined( 'WPSS_CONTENT_DIR_URL' ) ) 		{ define( 'WPSS_CONTENT_DIR_URL', WP_CONTENT_URL ); }
if( !defined( 'WPSS_CONTENT_DIR_PATH' ) ) 		{ define( 'WPSS_CONTENT_DIR_PATH', WP_CONTENT_DIR ); }
if( !defined( 'WPSS_PLUGINS_DIR_URL' ) ) 		{ define( 'WPSS_PLUGINS_DIR_URL', WP_PLUGIN_URL ); }
if( !defined( 'WPSS_PLUGINS_DIR_PATH' ) ) 		{ define( 'WPSS_PLUGINS_DIR_PATH', WP_PLUGIN_DIR ); }
if( !defined( 'WPSS_ADMIN_URL' ) ) 				{ define( 'WPSS_ADMIN_URL', untrailingslashit( admin_url() ) ); }
if( !defined( 'WPSS_COMMENTS_POST_URL' ) ) 		{ define( 'WPSS_COMMENTS_POST_URL', WPSS_SITE_WP_URL . '/wp-comments-post.php' ); }
if( !defined( 'WPSS_PLUGIN_BASENAME' ) ) 		{ define( 'WPSS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) ); }
if( !defined( 'WPSS_PLUGIN_FILE_BASENAME' ) ) 	{ define( 'WPSS_PLUGIN_FILE_BASENAME', trim( basename( __FILE__ ), '/' ) ); }
if( !defined( 'WPSS_PLUGIN_NAME' ) ) 			{ define( 'WPSS_PLUGIN_NAME', trim( dirname( WPSS_PLUGIN_BASENAME ), '/' ) ); }
if( !defined( 'WPSS_PLUGIN_URL' ) ) 			{ define( 'WPSS_PLUGIN_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) ); }
if( !defined( 'WPSS_PLUGIN_FILE_URL' ) ) 		{ define( 'WPSS_PLUGIN_FILE_URL',  WPSS_PLUGIN_URL.'/'.WPSS_PLUGIN_FILE_BASENAME ); }
if( !defined( 'WPSS_PLUGIN_COUNTER_URL' ) ) 	{ define( 'WPSS_PLUGIN_COUNTER_URL', WPSS_PLUGIN_URL . '/counter' ); }
if( !defined( 'WPSS_PLUGIN_CSS_URL' ) ) 		{ define( 'WPSS_PLUGIN_CSS_URL', WPSS_PLUGIN_URL . '/css' ); }
if( !defined( 'WPSS_PLUGIN_DATA_URL' ) ) 		{ define( 'WPSS_PLUGIN_DATA_URL', WPSS_PLUGIN_URL . '/data' ); }
if( !defined( 'WPSS_PLUGIN_IMG_URL' ) ) 		{ define( 'WPSS_PLUGIN_IMG_URL', WPSS_PLUGIN_URL . '/img' ); }
if( !defined( 'WPSS_PLUGIN_JS_URL' ) ) 			{ define( 'WPSS_PLUGIN_JS_URL', WPSS_PLUGIN_URL . '/js' ); }
if( !defined( 'WPSS_PLUGIN_PATH' ) ) 			{ define( 'WPSS_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) ); }
if( !defined( 'WPSS_PLUGIN_FILE_PATH' ) ) 		{ define( 'WPSS_PLUGIN_FILE_PATH', WPSS_PLUGIN_PATH.'/'.WPSS_PLUGIN_FILE_BASENAME ); }
if( !defined( 'WPSS_PLUGIN_COUNTER_PATH' ) ) 	{ define( 'WPSS_PLUGIN_COUNTER_PATH', WPSS_PLUGIN_PATH . '/counter' ); }
if( !defined( 'WPSS_PLUGIN_CSS_PATH' ) ) 		{ define( 'WPSS_PLUGIN_CSS_PATH', WPSS_PLUGIN_PATH . '/css' ); }
if( !defined( 'WPSS_PLUGIN_DATA_PATH' ) ) 		{ define( 'WPSS_PLUGIN_DATA_PATH', WPSS_PLUGIN_PATH . '/data' ); }
if( !defined( 'WPSS_PLUGIN_IMG_PATH' ) ) 		{ define( 'WPSS_PLUGIN_IMG_PATH', WPSS_PLUGIN_PATH . '/img' ); }
if( !defined( 'WPSS_PLUGIN_INCL_PATH' ) ) 		{ define( 'WPSS_PLUGIN_INCL_PATH', WPSS_PLUGIN_PATH . '/includes' ); }
if( !defined( 'WPSS_PLUGIN_JS_PATH' ) ) 		{ define( 'WPSS_PLUGIN_JS_PATH', WPSS_PLUGIN_PATH . '/js' ); }
if( !defined( 'WPSS_PLUGIN_LANG_PATH' ) ) 		{ define( 'WPSS_PLUGIN_LANG_PATH', WPSS_PLUGIN_PATH . '/languages' ); }
if( !defined( 'WPSS_I18N_LANG_PATH' ) ) 		{ define( 'WPSS_I18N_LANG_PATH', basename( dirname( __FILE__ ) ) . '/languages' ); }
if( !defined( 'WPSS_SERVER_NAME' ) ) 			{ define( 'WPSS_SERVER_NAME', rs_wpss_get_server_name() ); }
if( !defined( 'WPSS_SERVER_ADDR' ) ) 			{ define( 'WPSS_SERVER_ADDR', rs_wpss_get_server_addr() ); }
if( !defined( 'WPSS_SERVER_NAME_REV' ) ) 		{ define( 'WPSS_SERVER_NAME_REV', strrev( WPSS_SERVER_NAME ) ); }
if( !defined( 'WPSS_SERVER_NAME_NODOT' ) ) 		{ $wpss_server_name_nodot = str_replace( '.', '', WPSS_SERVER_NAME ); define( 'WPSS_SERVER_NAME_NODOT', $wpss_server_name_nodot ); }
if( !defined( 'WPSS_SERVER_HOSTNAME' ) ) 		{ define( 'WPSS_SERVER_HOSTNAME', rs_wpss_get_server_hostname() ); }
if( !defined( 'WPSS_HASH_ALT' ) ) 				{ $wpss_alt_prefix	= rs_wpss_md5( WPSS_SERVER_NAME_NODOT ); define( 'WPSS_HASH_ALT', $wpss_alt_prefix ); }
if( !defined( 'WPSS_HASH' ) )					{ $wpss_hash_prefix	= defined( 'COOKIEHASH' ) ? COOKIEHASH : rs_wpss_md5( WPSS_SITE_URL ); define( 'WPSS_HASH', $wpss_hash_prefix ); }
if( !defined( 'WPSS_THIS_URL' ) ) 				{ define( 'WPSS_THIS_URL', rs_wpss_get_url() ); }
if( !defined( 'WPSS_REF2XJS' ) ) 				{ define( 'WPSS_REF2XJS', 'r3f5x9JS' ); }
if( !defined( 'WPSS_JSONST' ) ) 				{ define( 'WPSS_JSONST', 'JS04X7' ); }
if( !defined( 'WPSS_SPH' ) ) 					{ define( 'WPSS_SPH', 240 ); }
if( !defined( 'WPSS_RSM_URL' ) ) 				{ define( 'WPSS_RSM_URL', 'https://www.redsandmarketing.com/' ); }
if( !defined( 'WPSS_HOME_URL' ) ) 				{ define( 'WPSS_HOME_URL', WPSS_RSM_URL.'plugins/'.WPSS_PLUGIN_NAME.'/' ); }
if( !defined( 'WPSS_SUPPORT_URL' ) ) 			{ define( 'WPSS_SUPPORT_URL', WPSS_RSM_URL.'plugins/'.WPSS_PLUGIN_NAME.'/support/' ); }
if( !defined( 'WPSS_WP_URL' ) ) 				{ define( 'WPSS_WP_URL', 'https://wordpress.org/extend/plugins/'.WPSS_PLUGIN_NAME.'/' ); }
if( !defined( 'WPSS_WP_RATING_URL' ) ) 			{ define( 'WPSS_WP_RATING_URL', 'https://wordpress.org/support/view/plugin-reviews/'.WPSS_PLUGIN_NAME ); }
if( !defined( 'WPSS_DONATE_URL' ) ) 			{ define( 'WPSS_DONATE_URL', WPSS_RSM_URL.'/go/donate/'.WPSS_PLUGIN_NAME.'/' ); }
if( !defined( 'WPSS_PHP_MEM_LIMIT' ) ) 			{ $wpss_php_memory_limit = rs_wpss_format_bytes( ini_get( 'memory_limit' ) ); define( 'WPSS_PHP_MEM_LIMIT', $wpss_php_memory_limit ); }
/* INCLUDE POPULAR CACHE PLUGINS HERE (16) */
$popular_cache_plugins_default 					= array ( 'cachify', 'db-cache-reloaded', 'db-cache-reloaded-fix', 'gator-cache', 'hyper-cache', 'hyper-cache-extended', 'lite-cache', 'quick-cache', 'w3-total-cache', 'wp-fast-cache', 'wp-fastest-cache', 'wp-fastest-cache-premium', 'wp-rocket', 'wp-super-cache', 'zencache', 'zencache-pro' );
if( !defined( 'WPSS_POPULAR_CACHE_PLUGINS' ) )	{ define( 'WPSS_POPULAR_CACHE_PLUGINS', serialize( $popular_cache_plugins_default ) ); }
/* SET THE DEFAULT CONSTANT VALUES HERE */
$wpss_options_default							= array ( 'block_all_trackbacks' => 0, 'block_all_pingbacks' => 0, 'comment_logging' => 0, 'comment_logging_all' => 0, 'comment_logging_start_date' => 0, 'comment_logging_end_date' => 0, 'enhanced_comment_blacklist' => 0, 'enable_whitelist' => 0, 'comment_min_length' => 15, 'allow_proxy_users' => 1, 'hide_extra_data' => 0, 'registration_shield_disable' => 0, 'registration_shield_level_1' => 0, 'disable_cf7_shield' => 0, 'disable_gf_shield' => 0, 'disable_misc_form_shield' => 0, 'disable_email_encode' => 0, 'allow_comment_author_keywords' => 0, 'form_include_website' => 1, 'form_require_website' => 0, 'form_include_phone' => 1, 'form_require_phone' => 0, 'form_include_company' => 0, 'form_require_company' => 0, 'form_include_drop_down_menu' => 0, 'form_require_drop_down_menu' => 0, 'form_drop_down_menu_title' => '', 'form_drop_down_menu_item_1' => '', 'form_drop_down_menu_item_2' => '', 'form_drop_down_menu_item_3' => '', 'form_drop_down_menu_item_4' => '', 'form_drop_down_menu_item_5' => '', 'form_drop_down_menu_item_6' => '', 'form_drop_down_menu_item_7' => '', 'form_drop_down_menu_item_8' => '', 'form_drop_down_menu_item_9' => '', 'form_drop_down_menu_item_10' => '', 'form_mail_encode' => 0, 'form_message_width' => 40, 'form_message_height' => 10, 'form_message_min_length' => 25, 'form_response_thank_you_message' => __( 'Your message was sent successfully. Thank you.', 'wp-spamshield' ), 'form_include_user_meta' => 1, 'promote_plugin_link' => 0, );
if( !defined( 'WPSS_OPTIONS_DEFAULT' ) ) 		{ define( 'WPSS_OPTIONS_DEFAULT', serialize( $wpss_options_default ) ); }
$wpss_depr_options_default 						= array( 'wpss_version' => '', 'init_user_approve_run' => '', 'install_status' => '', 'warning_status' => '', 'regalert_status' => '', 'last_admin' => '', 'wpss_admins' => array(), 'reg_count' => 0, 'spam_count' => 0, 'wpssmid_cache' => array(), 'wpss_procdat' => array( 'total_tracked' => 0, 'total_wpss_time' => 0, 'avg_wpss_proc_time' => 0, 'total_comment_proc_time' => 0, 'avg_comment_proc_time' => 0, 'total_wpss_avg_tracked' => 0, 'total_avg_wpss_proc_time' => 0, 'avg2_wpss_proc_time' => 0 ), );
if( !defined( 'WPSS_DEPR_OPTIONS_DEFAULT' ) ) 	{ define( 'WPSS_DEPR_OPTIONS_DEFAULT', serialize( $wpss_depr_options_default ) ); }

unset( $wpss_eol, $wpss_ds, $wpss_server_name_nodot, $wpss_alt_prefix, $wpss_hash_prefix, $wpss_php_memory_limit, $popular_cache_plugins_default, $wpss_options_default, $wpss_depr_options_default );
rs_wpss_set_memory();
$wpss_ecom_urls 								= array( '/checkout/', '/store/', '/shop/', '/cart/', '/products/', );

/* Includes - BEGIN */
require_once( WPSS_PLUGIN_INCL_PATH.'/advanced.php' );
require_once( WPSS_PLUGIN_INCL_PATH.'/blacklists.php' );
/* require_once( WPSS_PLUGIN_INCL_PATH.'/class.wpss-admin.php' ); */
require_once( WPSS_PLUGIN_INCL_PATH.'/class.wpss-compatibility.php' );
require_once( WPSS_PLUGIN_INCL_PATH.'/class.wpss-security.php' );
/* require_once( WPSS_PLUGIN_INCL_PATH.'/class.wpss-session.php' ); */
/* require_once( WPSS_PLUGIN_INCL_PATH.'/class.wpss-utils.php' ); */
require_once( WPSS_PLUGIN_INCL_PATH.'/class.wpss-widget.php' );
/* Includes - END */

/* SET ADVANCED OPTIONS - Can be overridden in wp-config.php. Advanced users only. */
if( !defined( 'WPSS_COMPAT_MODE' ) ) 			{ define( 'WPSS_COMPAT_MODE', FALSE ); }
if( !defined( 'WPSS_TEMP_BL_DISABLE' ) ) 		{ define( 'WPSS_TEMP_BL_DISABLE', FALSE ); }
if( !defined( 'WPSS_TEMP_BL_CF_ONLY' ) ) 		{ define( 'WPSS_TEMP_BL_CF_ONLY', FALSE ); }
if( !defined( 'WPSS_IP_BAN_ENABLE' ) ) 			{ define( 'WPSS_IP_BAN_ENABLE', FALSE ); }
if( !defined( 'WPSS_IP_BAN_CLEAR' ) ) 			{ define( 'WPSS_IP_BAN_CLEAR', FALSE ); }
if( !defined( 'WPSS_CUSTOM_ECOM' ) ) 			{ define( 'WPSS_CUSTOM_ECOM', FALSE ); }
if( !defined( 'WPSS_CUSTOM_CART_URL' ) )		{ define( 'WPSS_CUSTOM_CART_URL', FALSE ); }
else {
	if( preg_match( "~^/[a-z0-9\-_]+/$~i", WPSS_CUSTOM_CART_URL ) ) {
		$wpss_ecom_urls[] = WPSS_CUSTOM_CART_URL;
	}
}
if( !defined( 'WPSS_ECOM_URLS' ) ) 				{ define( 'WPSS_ECOM_URLS', serialize( $wpss_ecom_urls ) ); }
if( !defined( 'WPSS_INIT_SPAM_COUNT' ) ) 		{ define( 'WPSS_INIT_SPAM_COUNT', FALSE ); }
else {
	global $wpss_init_spam_count; $wpss_init_spam_count = is_int( WPSS_INIT_SPAM_COUNT ) ? WPSS_INIT_SPAM_COUNT : 0;
}
unset( $wpss_ecom_urls );


/* Early Security Check */

if( !empty( $_SERVER['WPSS_SEC_THREAT'] ) || !empty( $_SERVER['BHCS_SEC_THREAT'] ) ) {
	$_SERVER['WPSS_SEC_THREAT'] = TRUE;
	if( TRUE === WP_DEBUG && TRUE === WPSS_DEBUG && strpos( WPSS_SERVER_NAME_REV, WPSS_MDBUG_SERVER_NAME_REV ) === 0 ) {
		if( !headers_sent() ) { @header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden',TRUE,403); }
		die( '403 Forbidden' );
	}
}


/* Standard Functions - BEGIN */

function rs_wpss_eol() {
	global $is_IIS; return !empty( $is_IIS ) ? "\r\n" : "\n";
}

function rs_wpss_ds() {
	global $is_IIS; return !empty( $is_IIS ) ? '\\' : '/';
}

function rs_wpss_getenv( $e = FALSE, $add_vars = array() ) {
	/**
	 *  Get Environment Variables, or load a specific one.
	 *  Ensures compatibility with servers (ie IIS) that aren't set to populate $_ENV[] automatically.
	 *  @since 1.9.6.2
	 */
	global $_WPSS_ENV; if( empty( $_WPSS_ENV ) || !is_array( $_WPSS_ENV ) ) { $_WPSS_ENV = array(); }
	$_WPSS_ENV = (array) $_WPSS_ENV + (array) $_ENV;
	$vars = array( 'REMOTE_ADDR', 'SERVER_ADDR', 'LOCAL_ADDR', 'HTTP_HOST', 'SERVER_NAME', );
	$vars = !empty( $add_vars ) ? (array) $vars + (array) $add_vars : $vars;
	if( !empty( $e ) ) { $vars[] = $e; }
	foreach( $vars as $i => $v ) {
		if( empty( $_WPSS_ENV[$v] ) ) { $_WPSS_ENV[$v] = $_ENV[$v] = ''; if( function_exists( 'getenv' ) ) { $_WPSS_ENV[$v] = $_ENV[$v] = @getenv($v); } }
		if( !empty( $_WPSS_ENV[$v] ) && FALSE !== strpos( $v, '_ADDR' ) ) { $_WPSS_ENV[$v] = rs_wpss_sanitize_ip( $_WPSS_ENV[$v] ); }
	}
	return FALSE !== $e ? $_WPSS_ENV[$e] : $_WPSS_ENV;
}

function rs_wpss_start_session( $force = FALSE ) {
	global $wpss_session_id,$wpss_cache_check; if( empty( $wpss_session_id ) ) { $wpss_session_id = @session_id(); }
	if( 'POST' !== $_SERVER['REQUEST_METHOD'] && empty( $force ) ) {
		if( empty( $wpss_cache_check ) ) { $wpss_cache_check = rs_wpss_check_cache_status(); }
		$cache_check_status	= $wpss_cache_check['cache_check_status'];
		if( $cache_check_status === 'ACTIVE' || TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) ) { return; }
	}
	if( empty( $wpss_session_id ) && !headers_sent() ) { @session_start(); $wpss_session_id = @session_id(); }
}

function rs_wpss_end_session() {
	global $wpss_session_name; if( empty( $wpss_session_name ) ) { $wpss_session_name = @session_name(); }
	if( !rs_wpss_is_session_active() ) { return; }
	$_SESSION = array(); @session_destroy();
	if( empty( $_COOKIE ) ) { return; }
	if( isset( $_COOKIE[$wpss_session_name] ) ) {
		$params = @session_get_cookie_params(); /* http://php.net/manual/en/function.session-get-cookie-params.php */
		if( empty( $params ) || !is_array( $params ) ) {
			$params = array( 'path' => '/', 'domain' => rs_wpss_get_cookie_domain(), 'secure' => rs_wpss_is_ssl(), 'httponly' => TRUE );
		}
		/* Delete session cookie */
		@setcookie( $wpss_session_name, '', time() - WEEK_IN_SECONDS,  $params['path'], $params['domain'], $params['secure'], $params['httponly'] );
	}
}

function rs_wpss_is_session_active() {
	global $wpss_session_active; if( isset( $wpss_session_active ) && is_bool( $wpss_session_active ) ) { return $wpss_session_active; }
	if( rs_wpss_is_php_ver( '5.4' ) && function_exists( 'session_status' ) && defined( 'PHP_SESSION_ACTIVE' ) ) {
		$wpss_session_active = ( session_status() === PHP_SESSION_ACTIVE ) ? TRUE : FALSE; return $wpss_session_active;
	} else {
		global $wpss_session_id; if( empty( $wpss_session_id ) ) { $wpss_session_id = @session_id(); }
		$wpss_session_active = !empty( $wpss_session_id ) ? TRUE : FALSE; return $wpss_session_active;
	}
	return FALSE;
}

function rs_wpss_login( $user_login, $user = NULL ) {
	if( !is_object( $user ) || empty( $user ) || empty( $user->ID ) ) { $user = wp_get_current_user(); }
	if( !is_object( $user ) || empty( $user ) || empty( $user->ID ) ) { return; }
	$user_id = $user->ID; rs_wpss_update_user_ip( $user_id ); rs_wpss_remove_expired_admins();
}

function rs_wpss_logout() {
	rs_wpss_end_session();
}

function rs_wpss_update_user_ip( $user_id = NULL, $add_admin_ip = FALSE, $admin_ips = NULL ) {
	if( empty( $user_id ) ){ global $current_user; $current_user = wp_get_current_user(); $user_id = $current_user->ID; }
	if( empty( $user_id ) ){ return; }
	/* IP / PROXY INFO - BEGIN */
	global $wpss_ip_proxy_info;
	if( empty( $wpss_ip_proxy_info ) ) { $wpss_ip_proxy_info = rs_wpss_ip_proxy_info(); }
	extract( $wpss_ip_proxy_info );
	/* IP / PROXY INFO - END */
	if( !rs_wpss_is_valid_ip( $ip ) ) { return; }
	$timenow = time();
	$user_ip = get_user_meta( $user_id, 'wpss_user_ip', TRUE );
	if( empty( $user_ip ) ) { $user_ip = array(); }
	$user_ip[$ip] = array( 'server' => $reverse_dns, 'time' => $timenow );
	update_user_meta( $user_id, 'wpss_user_ip', $user_ip );
	if( !empty( $add_admin_ip ) ) {
		if( empty( $admin_ips ) ) { $admin_ips = get_option( 'spamshield_admins' ); }
		if( empty( $admin_ips ) ) { $admin_ips = array(); }
		$admin_ips[$ip] = $timenow;
		update_option( 'spamshield_admins', $admin_ips );
		update_option( 'spamshield_last_admin', $ip );
	}
}

function rs_wpss_set_memory() {
	/**
	 *  Boost memory limits
	 *  WordPress' default is 40M, but it requires at least 64M to run smoothly on many sites. 128M+ is better.
	 */
	if( function_exists( 'memory_get_usage' ) && rs_wpss_is_function_enabled( 'ini_set' ) ) {
		$current_limit		= ini_get( 'memory_limit' );
		$current_limit_int	= intval( $current_limit );
		if( FALSE !== strpos( $current_limit, 'G' ) ) { $current_limit_int *= 1024; }
		$wpss_limit_int		= intval( WPSS_MEMORY_LIMIT );
		if( FALSE !== strpos( WPSS_MEMORY_LIMIT,'G' ) ) { $wpss_limit_int *= 1024; }
		if( -1 != $current_limit && ( -1 == WPSS_MEMORY_LIMIT || $current_limit_int < $wpss_limit_int ) ) {
			@ini_set( 'memory_limit', WPSS_MEMORY_LIMIT );
		}
	}
}

function rs_wpss_sanitize_string( $string ) {
	/**
	 *  Sanitize a string. Much faster than sanitize_text_field()
	 *  @since ...
	 */
	return trim( addslashes( htmlentities( stripslashes( strip_tags( $string ) ) ) ) );
}

function rs_wpss_sanitize_textarea( $string ) {
	/**
	 *  sanitize_text_field() without killing the line breaks
	 *  Use for contact forms
	 *  @since 1.9.7.8
	 */
	return implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $string ) ) );
}

function rs_wpss_sanitize_opt_string( $string ) {
	/* Sanitize an options string. ie. Contact Form Drop Down Menu... */
	$string = trim( addslashes( htmlentities( stripslashes( strip_tags( $string ) ), ENT_QUOTES, get_bloginfo( 'charset' ), FALSE ) ) );
	if( FALSE !== strpos( $string, "\'" ) || FALSE !== strpos( $string, '\"' ) ) {
		$string = preg_replace( "~\\+'~", '&rsquo;', $string );
		$string = preg_replace( "~\\+\"~", '', $string );
	}
	return $string;
}

function rs_wpss_prep_cf_email( $string ) {
	/* TO DO: Possibly integrate htmlspecialchars_decode() & nl2br() */
	$string = preg_replace( "~(\\*['’]|&(apos|#x0+27|#0?39|rsquo);)~i", '’', $string );
	$string = str_replace( array( "\\'", '\’', "'", '’', '&apos;', '&rsquo;', '&#x00027;', '&#039;', '&#39;', ), '’', $string );
	return $string;
}

function rs_wpss_is_function_enabled( $func ) {
	if( !function_exists( $func ) ) { return FALSE; }
	$d = strtolower( trim( ini_get( 'disable_functions' ), ", \t\n\r\0\x0B" ) );
    $s = strtolower( trim( ini_get( 'suhosin.executor.func.blacklist' ), ", \t\n\r\0\x0B" ) );
	if( empty( $d ) && empty( $s ) ) { return TRUE; }
	$ds = str_replace( array( '  ', ' ', ',,' ), array( '', '', ',' ), $d.','.$s);
	$arr = !empty($ds) ? (array) explode( ',', $ds ) : array(); $arr = array_filter( $arr );
	if( !empty( $arr ) && in_array( $func, $arr, TRUE ) ) { return FALSE; }
	return TRUE;
}

function rs_wpss_is_utf8( $string ) {
	/* Check if a string is UTF8 */
	if( preg_match( "~~u", $string ) ) { return TRUE; }
	return FALSE;
}

function rs_wpss_count_words( $string ) {
	$string = trim( $string ); $char_count = rs_wpss_strlen( $string );
	if( empty( $string ) || $char_count == 0 ) { $num_words = 0; } else { $exploded_string = preg_split( "~\s+~", $string ); $num_words = count( $exploded_string ); }
	return $num_words;
}

function rs_wpss_strlen( $string ) {
	/**
	 *  Use this function instead of mb_strlen because some servers (often IIS) have mb_ functions disabled by default
	 *  BUT mb_strlen is superior to strlen, so use it whenever possible
	 */
	return function_exists( 'mb_strlen' ) ? mb_strlen( $string, 'UTF-8' ) : strlen( $string );
}

function rs_wpss_casetrans( $type, $string ) {
	/**
	 *  Convert case using multibyte version if available, if not, use defaults
	 *  @since 1.8.4
	 */
	switch( $type ) {
		case 'upper':
			return function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $string, 'UTF-8' ) : strtoupper( $string );
		case 'lower':
			return function_exists( 'mb_strtolower' ) ? mb_strtolower( $string, 'UTF-8' ) : strtolower( $string );
		case 'ucfirst':
			if( function_exists( 'mb_strtoupper' ) && function_exists( 'mb_substr' ) ) {
				$strtmp = mb_strtoupper( mb_substr( $string, 0, 1, 'UTF-8' ), 'UTF-8' ) . mb_substr( $string, 1, NULL, 'UTF-8' );
				/* 1.9.5.1 - Added workaround for strange PHP bug in mb_substr() on some servers */
				return rs_wpss_strlen( $string ) === rs_wpss_strlen( $strtmp ) ? $strtmp : ucfirst( $string );
			} else { return ucfirst( $string ); }
		case 'ucwords':
			return function_exists( 'mb_convert_case' ) ? mb_convert_case( $string, MB_CASE_TITLE, 'UTF-8' ) : ucwords( $string );
			/**
			 *  Note differences in results between ucwords() and this.
			 *  ucwords() will capitalize first characters without altering other characters, whereas this will lowercase everything, but capitalize the first character of each word.
			 *  This works better for our purposes, but be aware of differences.
			 */
		default:
			return $string;
	}
}

function rs_wpss_substr_count( $haystack, $needle, $offset = 0, $length = NULL ) {
	/* Has error correction built in */
	$haystack_len = rs_wpss_strlen( $haystack );
	$needle_len = rs_wpss_strlen( $needle );
	if( $offset >= $haystack_len || $offset < 0 ) { $offset = 0; }
	if( empty( $length ) || $length <= 0 ) { $length = $haystack_len; }
	$haystack_len_offset_diff = $haystack_len - $offset;
	if( $length > $haystack_len_offset_diff ) { $length = $haystack_len_offset_diff; }
	$needle_instances = 0;
	if( !empty( $needle ) && !empty( $haystack ) && $needle_len <= $haystack_len ) {
		$needle_instances = substr_count( $haystack, $needle, $offset, $length );
	}
	return $needle_instances;
}

function rs_wpss_sort_unique( $arr ) {
	/* Removes duplicates and orders the array. */
	$arr_tmp = array_unique( $arr ); natcasesort( $arr_tmp ); $new_arr = array_values( $arr_tmp );
	return $new_arr;
}

function rs_wpss_preg_quote( $string ) {
	/* Prep for use in Regex, this plugin uses '~' as a delimiter exclusively, can be changed */
	$regex_string = preg_quote( $string, '~' );
	$regex_string = preg_replace( "~\s+~", "\s+", $regex_string );
	return $regex_string;
}

function rs_wpss_md5( $string ) {
	/* Use this function instead of hash for compatibility. BUT hash is faster than md5 for multiple iterations, so use it whenever possible. */
	return function_exists( 'hash' ) ? hash( 'md5', $string ) : md5( $string );
}

function rs_wpss_parse_url( $url ) {
	/**
	 *  Use this function instead of parse_url() for compatibility with PHP < 5.4.7. wp_parse_url() was added in WP ver 4.4
	 *  @since 1.9.8.4
	 */
    return ( function_exists( 'wp_parse_url' ) && rs_wpss_is_wp_ver('4.4') ) ? wp_parse_url( $url ) : @parse_url( $url );
}

function rs_wpss_json_encode( $data, $options = 0, $depth = 512 ) {
	/**
	 *  Use this function instead of json_encode() for compatibility, esp with non-UTF-8 data. wp_json_encode() was added in WP ver 4.1
	 *  @since 1.9.8.4
	 */
    return ( function_exists( 'wp_json_encode' ) && rs_wpss_is_wp_ver('4.1') ) ? wp_json_encode( $data, $options, $depth ) : json_encode( $data, $options );
}

function rs_wpss_get_wpss_eid( $args ) {
	/**
	 *  Creates unique temporary IDs from hash of data for both contact forms and comments.
	 *  @since 1.7.7
	 *  $type: 'comment','contact'
	 *  $args: name, email, url, content
	 *  'eid' - Entity ID; 'ecid' - Entity Content ID
	 */
	$wpsseid = array( 'eid' => '', 'ecid' => '');
	$wpsseid_args_data_str = implode( '', $args );
	$wpsseid['eid'] = rs_wpss_md5( $wpsseid_args_data_str );
	$wpsseid['ecid'] = rs_wpss_md5( $args['content'] );
	return $wpsseid;
}

function rs_wpss_create_nonce( $action, $name = '_wpss_nonce' ) {
	/**
	 *  Creates a different nonce system than WordPress.
	 *  24 hours or 1 time use.
	 *  Difference vs WP nonces: Nonce must exist in database, is not tied to a user ID, and is truly 1 time use.
	 *  WP nonces don't work for every application. If a comment is posted, and a notification email is sent to admin with link to blacklist the IP, this works better.
	 */
	$i = wp_nonce_tick();
	$timenow = time();
	$nonce = substr( rs_wpss_md5( $i . $action . $name . WPSS_HASH . $timenow ), -12, 10 );
	$spamshield_nonces = get_option( 'spamshield_nonces' );
	if( empty( $spamshield_nonces ) ) { $spamshield_nonces = array(); }
	else {
		foreach( $spamshield_nonces as $i => $n ) {
			if( $n['expire'] <= $timenow ) { unset( $spamshield_nonces[$i] ); }
		}
	}
	$expire = $timenow + 86400; /* 24 hours */
	$spamshield_nonces[] = array( 'nonce' => $nonce, 'action' => $action, 'name' => $name, 'expire' => $expire );
	update_option( 'spamshield_nonces', $spamshield_nonces, FALSE );
	return $nonce;
}

function rs_wpss_verify_nonce( $value, $action, $name = '_wpss_nonce' ) {
	/**
	 *  Verify a WP-SpamShield nonce.
	 *  $value	= value of nonce you're testing for
	 *  $action	= descriptive string used internally for what you're trying to do
	 *  $name	= identifier of nonce
	 */
	$nonce_valid = FALSE;
	$timenow = time();
	$spamshield_nonces = get_option( 'spamshield_nonces' );
	if( empty( $spamshield_nonces ) ) { return FALSE; }
	foreach( $spamshield_nonces as $i => $n ) {
		if( $n['nonce'] === $value && $n['action'] === $action && $n['expire'] > $timenow ) {
			unset( $spamshield_nonces[$i] );
			$nonce_valid = TRUE;
		} elseif( $n['expire'] <= $timenow ) { unset( $spamshield_nonces[$i] ); }
	}
	update_option( 'spamshield_nonces', $spamshield_nonces, FALSE );
	return $nonce_valid;
}

function rs_wpss_purge_nonces() {
	/**
	 *  Purge expired nonces. Keep the nonce cache clean.
	 */
	$timenow = time();
	$spamshield_nonces = get_option( 'spamshield_nonces' );
	if( empty( $spamshield_nonces ) ) { return FALSE; }
	foreach( $spamshield_nonces as $i => $n ) {
		if( $n['expire'] <= $timenow ) { unset( $spamshield_nonces[$i] ); }
	}
	update_option( 'spamshield_nonces', $spamshield_nonces, FALSE );
	return TRUE;
}

function rs_wpss_microtime() {
	$t = microtime( TRUE );
	if( empty( $t ) ) { $t = time(); rs_wpss_append_log_data( 'PHP microtime() function is either disabled or not functioning properly on this server.', FALSE ); }
	return $t;
}

function rs_wpss_timer( $start = NULL, $end = NULL, $show_seconds = FALSE, $precision = 8, $no_format = FALSE, $raw = FALSE, $benchmark = FALSE ) {
	/**
	 *  $precision will default to 8 but can be set to anything - 1,2,3,4,5,6,etc.
	 *  Use $no_format when clean numbers are needed for calculations. International formatting throws a wrench into things.
	 */
	if( empty( $start ) ) { return NULL; }
	if( empty( $end ) ) { $end = microtime(TRUE); }
	$total_time = $end - $start;
	if( empty( $no_format ) ) {
		$total_time_for = rs_wpss_number_format( $total_time, $precision );
		if( !empty( $show_seconds ) ) { $total_time_for .= ' seconds'; }
	} elseif( empty( $raw ) ) {
		$total_time_for = number_format( $total_time, $precision );
	} else { $total_time_for = $total_time; }
	if( TRUE === $benchmark ) {
		rs_wpss_append_log_data( '$start_time: "'.$start.'" Line: '.__LINE__.' | Func: '.__FUNCTION__.' | MEM USED: ' . rs_wpss_wp_memory_used() . ' | VER: ' . WPSS_VERSION, TRUE );
		rs_wpss_append_log_data( '$end_time: "'.$end.'" Line: '.__LINE__.' | Func: '.__FUNCTION__.' | MEM USED: ' . rs_wpss_wp_memory_used() . ' | VER: ' . WPSS_VERSION, TRUE );
		rs_wpss_append_log_data( '$total_time: "'.$total_time_for.'" Line: '.__LINE__.' | Func: '.__FUNCTION__.' | MEM USED: ' . rs_wpss_wp_memory_used() . ' | VER: ' . WPSS_VERSION, TRUE );
		return $total_time_for;
	}
	return $total_time_for;
}

function rs_wpss_timer_bm( $start ) {
	$total_time = rs_wpss_timer( $start, NULL, FALSE, 6, TRUE, FALSE, TRUE );
	return $total_time;
}

function rs_wpss_number_format( $number, $precision = NULL ) {
	/* $precision will default to NULL but can be set to anything - 1,2,3,4,5,6,etc. */
	if( function_exists( 'number_format_i18n' ) ) { $number_for = number_format_i18n( $number, $precision ); }
	else { $number_for = number_format( $number, $precision ); }
	return $number_for;
}

function rs_wpss_format_bytes( $size, $precision = 2 ) {
	if( !is_numeric( $size ) || empty( $size ) ) { return $size; }
    $base = log($size) / log(1024);
    $base_floor = floor($base);
    $suffixes = array('', 'k', 'M', 'G', 'T');
    $suffix = isset( $suffixes[$base_floor] ) ? $suffixes[$base_floor] : '';
	if( empty($suffix) ) { return $size; }
	$formatted_num = round(pow(1024, $base - $base_floor), $precision) . $suffix;
    return $formatted_num;
}

function rs_wpss_wp_memory_used() {
    return function_exists( 'memory_get_usage' ) ? rs_wpss_format_bytes( memory_get_usage() ) : 0;
}

function rs_wpss_date_diff( $start, $end ) {
	$start_ts = strtotime($start);
	$end_ts = strtotime($end);
	$diff = ($end_ts-$start_ts);
	$start_array = explode('-', $start);
	$start_year = $start_array[0];
	$end_array = explode('-', $end);
	$end_year = $end_array[0];
	$years = $end_year-$start_year;
	if(($years%4) == 0) { $extra_days = ((($end_year-$start_year)/4)-1); } else { $extra_days = ((($end_year-$start_year)/4)); }
	$extra_days = round($extra_days);
	return round($diff/86400)+$extra_days;
}

function rs_wpss_scandir( $dir ) {
	clearstatcache();
	$dot_files = array( '..', '.' );
	if( !file_exists( $dir ) || !is_dir( $dir ) ) { return array(); }
	$dir_contents_raw = scandir( $dir );
	$dir_contents = array_values( array_diff( $dir_contents_raw, $dot_files ) );
	return $dir_contents;
}

function rs_wpss_get_domain( $url, $email_domain = FALSE ) {
	/**
	 *  Get domain from URL
	 *  Filter URLs with nothing after http
	 *  $email_domain will run through rs_wpss_get_email_domain()
	 */
	if( empty( $url ) || preg_match( "~^https?\:*/*$~i", $url ) ) { return ''; }
	/* Fix poorly formed URLs so as not to throw errors when parsing */
	$url = rs_wpss_fix_url( $url );
	/* NOW start parsing */
	$parsed = rs_wpss_parse_url( $url );
	/* Filter URLs with no domain */
	if( empty( $parsed['host'] ) ) { return ''; }
	$domain = rs_wpss_casetrans( 'lower', $parsed['host'] );
	if( !empty( $email_domain ) ) { $domain = rs_wpss_get_email_domain( $domain ); }
	return $domain;
}

function rs_wpss_get_email_domain( $domain ) {
	/**
	 *  Get email domain for use in email addresses
	 *  Strip 'www.' & 'm.' from beginning of domain
	 */
	if( empty( $domain ) ) { return ''; }
	$domain = preg_replace( "~^(ww[w0-9]|m)\.~i", '', $domain );
	return $domain;
}

function rs_wpss_get_cookie_domain( $domain = NULL ) {
	/**
	 *  Get domain for use in cookies - ex: .domain.com
	 *  Works with all subdomains
	 */
	if( empty( $domain ) ) {
		$url		= WPSS_THIS_URL;
		$domain		= rs_wpss_get_domain( $url );
	}
	$email_domain	= rs_wpss_get_email_domain( $domain );
	$cookie_domain	= '.'.$email_domain;
	return $cookie_domain;
}

function rs_wpss_get_query_string( $url ) {
	/**
	 *  Get query string from URL
	 *  Filter URLs with nothing after http
	 */
	if( empty( $url ) || preg_match( "~^https?\:*/*$~i", $url ) ) { return ''; }
	/* Fix poorly formed URLs so as not to throw errors when parsing */
	$url = rs_wpss_fix_url( $url );
	/* NOW start parsing */
	$parsed = rs_wpss_parse_url($url);
	/* Filter URLs with no query string */
	if( empty( $parsed['query'] ) ) { return ''; }
	$query_str = $parsed['query'];
	return $query_str;
}

function rs_wpss_get_query_args( $url ) {
	/**
	 *  Get query string array from URL
	 */
	if( empty( $url ) ) { return array(); }
	$query_str = rs_wpss_get_query_string( $url );
	parse_str( $query_str, $args );
	return $args;
}

function rs_wpss_parse_links( $haystack, $type = 'url' ) {
	/**
	 *  Parse a body of content for links - extracts URLs and Anchor Text
	 *  $type: 'url' for URLs, 'domain' for just Domains, 'url_at' for URLs from Anchor Text Links only, 'anchor_text' for Anchor Text
	 *  Returns an array
	 */
	$parse_links_regex = "~(<\s*a\s+[a-z0-9\-_\.\?\='\"\:\(\)\{\}\s]*\s*href|\[(url|link))\s*\=\s*['\"]?\s*(https?\://[a-z0-9\-_\/\.\?\&\=\~\@\%\+\#\:]+)\s*['\"]?\s*[a-z0-9\-_\.\?\='\"\:;\(\)\{\}\s]*\s*(>|\])([a-z0-9àáâãäåçèéêëìíîïñńņňòóôõöùúûü\-_\/\.\?\&\=\~\@\%\+\#\:;\!,'\(\)\{\}\s]*)(<|\[)\s*\/\s*a\s*(>|(url|link)\])~iu";
	$search_http_regex ="~(?:^|\s+)(https?\://[a-z0-9\-_\/\.\?\&\=\~\@\%\+\#\:]+)(?:$|\s+)~iu";
	preg_match_all( $parse_links_regex, $haystack, $matches_links, PREG_PATTERN_ORDER );
	$parsed_links_matches 			= $matches_links[3]; /* Array containing URLs parsed from Anchor Text Links in haystack text */
	$parsed_anchortxt_matches		= $matches_links[5]; /* Array containing Anchor Text parsed from Anchor Text Links in haystack text */
	if( $type === 'url' || $type === 'domain' ) {
		$url_haystack = preg_replace( "~\s~", ' - ', $haystack ); /* Workaround Added 1.3.8 */
		preg_match_all( $search_http_regex, $url_haystack, $matches_http, PREG_PATTERN_ORDER );
		$parsed_http_matches 		= $matches_http[1]; /* Array containing URLs parsed from haystack text */
		$parsed_urls_all_raw 		= array_merge( $parsed_links_matches, $parsed_http_matches );
		$parsed_urls_all			= array_unique( $parsed_urls_all_raw );
		if( $type === 'url' ) { $results = $parsed_urls_all; }
		elseif( $type === 'domain' ) {
			$parsed_urls_all_domains = array();
			foreach( $parsed_urls_all as $u => $url_raw ) {
				$url = rs_wpss_casetrans( 'lower', trim( stripslashes( $url_raw ) ) );
				if( empty( $url ) ) { continue; }
				$domain = rs_wpss_get_domain( $url );
				if( !in_array( $domain, $parsed_urls_all_domains, TRUE ) ) { $parsed_urls_all_domains[] = $domain; }
			}
			$results = $parsed_urls_all_domains;
		}
	} elseif( $type === 'url_at' ) { $results = $parsed_links_matches; }
	elseif( $type === 'anchor_text' ) { $results = $parsed_anchortxt_matches; }
	return $results;
}

function rs_wpss_fix_url( $url = NULL, $rem_frag = FALSE, $rem_query = FALSE, $rev = FALSE ) {
	/**
	 *  Fix poorly formed URLs so as not to throw errors or cause problems
	 */
	if( empty( $url ) ) { return ''; }
	$url = trim( $url );
	/* Too many forward slashes or colons after http */
	$url = preg_replace( "~^(https?)\:+/+~i", "$1://", $url);
	/* Too many dots */
	$url = preg_replace( "~\.+~i", ".", $url);
	/* Too many slashes after the domain */
	$url = preg_replace( "~([a-z0-9]+)/+([a-z0-9]+)~i", "$1/$2", $url);
	/* Remove fragments */
	if( !empty( $rem_frag ) && strpos( $url, '#' ) !== FALSE ) { $url_arr = explode( '#', $url ); $url = $url_arr[0]; }
	/* Remove query string completely */
	if( !empty( $rem_query ) && strpos( $url, '?' ) !== FALSE ) { $url_arr = explode( '?', $url ); $url = $url_arr[0]; }
	/* Reverse */
	if( !empty( $rev ) ) { $url = strrev($url); }
	return $url;
}

function rs_wpss_get_url( $safe = FALSE ) {
	/**
	 *  Get the URL of current page/post/etc
	 *  @Used By: constant 'WPSS_THIS_URL'
	 */
	$url  = rs_wpss_is_ssl() ? 'https://' : 'http://';
	$req  = rs_wpss_casetrans('lower',$_SERVER['REQUEST_URI']); /* $req_uri_lc */
	$url .= WPSS_SERVER_NAME.$req;
	if( TRUE === $safe ) { $url = esc_url( $url ); }
	return $url;
}

function rs_wpss_get_schemeless_url( $url ) {
	/**
	 *  Make URL schemeless / protocol-relative
	 *  Use judiciously to prevent SEO + proxy issues
	 *  @since 1.9.9.3
	 */
	return str_replace( array( 'https://', 'http://' ), '//', $url);
}

function rs_wpss_append_url( $url = NULL ) {
	/** 
	 *  Add essential data to link URLs to aid tech support
	 *	@since 1.9.7
	 */
	if( empty( $url ) ) { return ''; }
	global $wpss_active_plugins; $wpss_active_plugins = rs_wpss_get_active_plugins(); $num_plugins = count( $wpss_active_plugins );
	return add_query_arg( array( 'wpssv' => WPSS_VERSION, 'wpv' => WPSS_WP_VERSION, 'phv' => WPSS_PHP_VERSION, 'npl' => $num_plugins, ), $url );
}

function rs_wpss_is_ssl() {
	/**
	 *  Use instead of WP function is_ssl() - this is more accurate
	 *  @Used By: rs_wpss_get_url(), rs_wpss_get_rewrite_base(), rs_wpss_gf_spam_check(), WPSS_Compatibility::misc_form_bypass()
	 */
	if( !empty( $_SERVER['HTTPS'] )						&& 'off'	!==	strtolower( $_SERVER['HTTPS'] )	)					{ return TRUE; }
	if( !empty( $_SERVER['SERVER_PORT'] )				&& '443'	==	$_SERVER['SERVER_PORT'] )							{ return TRUE; }
	if( !empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] )	&& 'https'	===	strtolower( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) )	{ return TRUE; }
	if( !empty( $_SERVER['HTTP_X_FORWARDED_SSL'] )		&& 'off'	!==	strtolower( $_SERVER['HTTP_X_FORWARDED_SSL'] ) )	{ return TRUE; }
	return FALSE;
}

function rs_wpss_get_http_status( $url = NULL ) {
	/**
	 *  Check HTTP Status - Returns 3-digit response code
	 *  @since 1.9.1
	 */
	$str_con_def = stream_context_get_options( stream_context_get_default() );
	if( empty( $str_con_def ) ) { $str_con_def = array( 'http' => array( 'method' => 'GET' ) ); }
	rs_wpss_stream_context_set_default( array( 'http' => array( 'method' => 'HEAD' ) ) );
	$headers = @get_headers( $url );
	if( !is_array( $headers ) ) { $headers = array(); }
	rs_wpss_stream_context_set_default( $str_con_def );
	$status = !empty( $headers[0] ) ? substr( $headers[0], 9, 3 ) : '200';
	$hdr_data = compact( 'headers', 'status' );
    return $hdr_data;
}

function rs_wpss_stream_context_set_default( $arr ) {
	/**
	 *  Wrapper to prevent fatal errors upon activation in PHP 5.2 and below
	 *  Function stream_context_set_default() was added in PHP 5.3
	 *  @since 1.9.5.1
	 */
	if( function_exists( 'stream_context_set_default' ) ) { @stream_context_set_default( $arr ); }
}

function rs_wpss_get_rewrite_base() {
	$root_url  = rs_wpss_is_ssl() ? 'https://' : 'http://';
	$root_url .= WPSS_SERVER_NAME;
	$tmp = str_replace( $root_url, '', WPSS_SITE_URL );
	$rewrite_base = !empty( $tmp ) ? trailingslashit( $tmp ) : '/';
	return $rewrite_base;
}

function rs_wpss_get_server_name() {
	global $_WPSS_ENV;
	if(		!empty( $_SERVER['HTTP_HOST'] ) )		{ $server_name = $_SERVER['HTTP_HOST']; }
	elseif(	!empty( $_WPSS_ENV['HTTP_HOST'] ) )		{ $server_name = $_SERVER['HTTP_HOST'] = $_WPSS_ENV['HTTP_HOST']; }
	elseif(	!empty( $_SERVER['SERVER_NAME'] ) )		{ $server_name = $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME']; }
	elseif(	!empty( $_WPSS_ENV['SERVER_NAME'] ) )	{ $server_name = $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = $_WPSS_ENV['SERVER_NAME']; }
	return !empty( $server_name ) && '.' !== trim( $server_name ) ? rs_wpss_casetrans( 'lower', $server_name ) : WPSS_SITE_DOMAIN;
}

function rs_wpss_get_server_addr() {
	global $_WPSS_ENV;
	if(		!empty( $_SERVER['SERVER_ADDR'] ) )		{ $server_addr = $_SERVER['SERVER_ADDR']; }
	elseif(	!empty( $_WPSS_ENV['SERVER_ADDR'] ) )	{ $server_addr = $_SERVER['SERVER_ADDR'] = $_WPSS_ENV['SERVER_ADDR']; }
	elseif(	!empty( $_SERVER['LOCAL_ADDR'] ) )		{ $server_addr = $_SERVER['SERVER_ADDR'] = $_SERVER['LOCAL_ADDR']; } /* IIS compatibility */
	elseif(	!empty( $_WPSS_ENV['LOCAL_ADDR'] ) )	{ $server_addr = $_SERVER['SERVER_ADDR'] = $_SERVER['LOCAL_ADDR'] = $_WPSS_ENV['LOCAL_ADDR']; }
	else {
		$server_name = defined( 'WPSS_SERVER_NAME' ) ? WPSS_SERVER_NAME : rs_wpss_get_server_name();
		$server_addr = !empty( $server_name ) ? rs_wpss_get_reverse_dns_ip( $server_name ) : '';
		}
	return !empty( $server_addr ) ? $server_addr : '';
}

function rs_wpss_get_server_hostname( $sanitize = FALSE, $server_hostname = NULL ) {
	if( FALSE === $sanitize || empty( $server_hostname ) ) {
		$server_hostname = function_exists( 'php_uname' ) ? @php_uname('n') : @gethostname();
	}
	if( TRUE === $sanitize ) {
		$server_hostname = ( rs_wpss_strlen( $server_hostname ) < 6 || FALSE === strpos( $server_hostname, '.' ) ) ? '' : $server_hostname;
	}
	return $server_hostname;
}

function rs_wpss_get_web_host() {
	/* Try to identify web host - TO DO: Move to Utils Class */
	global $wpss_web_host; if( !empty( $wpss_web_host ) ) { return $wpss_web_host; }
	$server_hostname = WPSS_SERVER_HOSTNAME;
	if( empty( $server_hostname ) ) { $wpss_web_host = FALSE; return FALSE; }
	$server_hostname = rs_wpss_get_server_hostname( TRUE, WPSS_SERVER_HOSTNAME );
	if( empty( $server_hostname ) ) { $wpss_web_host = FALSE; return FALSE; }
	$wpss_web_host = FALSE;
	if( defined( 'WPE_APIKEY' ) || class_exists( 'WPE_API', FALSE ) || class_exists( 'WpeCommon' ) ) {
		$wpss_web_host = 'WP Engine';
	} elseif( defined( 'PAGELYBIN' ) ) {
		$wpss_web_host = 'Pagely';
	} elseif( FALSE !== strpos( $server_hostname, '.bluehost.com' ) ) {
		$wpss_web_host = 'Bluehost';
	} elseif( FALSE !== strpos( $server_hostname, '.inmotionhosting.com' ) ) {
		$wpss_web_host = 'InMotion Hosting';
	} elseif( FALSE !== strpos( $server_hostname, '.asoshared.com' ) ) {
		$wpss_web_host = 'A Small Orange';
	} elseif( FALSE !== strpos( $server_hostname, '.secureserver.net' ) ) {
		$rssd_web_host = 'GoDaddy';
	} elseif( FALSE !== strpos( $server_hostname, '.doteasy.com' ) ) {
		$rssd_web_host = 'Doteasy';
	} elseif( preg_match( "~\.(dreamhost\.com|dream\.io)~i", $server_hostname ) ) {
		$wpss_web_host = 'Dreamhost';
	} elseif( preg_match( "~\.(siteground([0-9]+)?\.|sgvps\.net)~i", $server_hostname ) ) {
		$wpss_web_host = 'SiteGround';
	} elseif( preg_match( "~\.(hostgator|websitewelcome)\.com~i", $server_hostname ) ) {
		$wpss_web_host = 'HostGator';
	} elseif( preg_match( "~\.cloudways(apps)?\.~i", $server_hostname ) ) {
		$wpss_web_host = 'Cloudways';
	} elseif( preg_match( "~\.(hetzner\.|host\-h\.net|your\-server\.de)~i", $server_hostname ) ) {
		$wpss_web_host = 'Hetzner';
	}
	return $wpss_web_host;
}

function rs_wpss_get_ip_addr() {
	global $wpss_ip_addr,$_WPSS_ENV; if( !empty( $wpss_ip_addr ) ) { return $wpss_ip_addr; }
	if( class_exists( 'wfUtils' ) && WPSS_Compatibility::is_plugin_active( 'wordfence/wordfence.php' ) ) {
		$wpss_ip_addr = wfUtils::getIP();
		return rs_wpss_sanitize_ip( $wpss_ip_addr );
	}
	$wpss_xff_addr = !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ? rs_wpss_sanitize_ip( $_SERVER['HTTP_X_FORWARDED_FOR'] ) : '';
	$wpss_rem_addr = !empty( $_SERVER['REMOTE_ADDR'] ) ? rs_wpss_sanitize_ip( $_SERVER['REMOTE_ADDR'] ) : '';
	/* Check for Google Chrome Data Compression Proxy (Chrome Data-Saver) and get Real IP */
	if( !empty( $_SERVER['HTTP_VIA'] ) && !empty( $wpss_rem_addr ) && !empty( $wpss_xff_addr ) && $wpss_rem_addr !== $wpss_xff_addr && '1.1 Chrome-Compression-Proxy' === $_SERVER['HTTP_VIA'] && rs_wpss_is_valid_ip( $wpss_xff_addr ) && rs_wpss_is_google_ip( $wpss_rem_addr ) ) { return $wpss_xff_addr; }
	/* Check for Opera Data Saver Proxy and get Real IP */
	if( !empty( $wpss_rem_addr ) && !empty( $wpss_xff_addr ) && $wpss_rem_addr !== $wpss_xff_addr && rs_wpss_is_valid_ip( $wpss_xff_addr ) && rs_wpss_is_opera_ip( $wpss_rem_addr ) ) { return $wpss_xff_addr; }
	$wpss_ip_addr = !empty( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : $_WPSS_ENV['REMOTE_ADDR'];
	$wpss_ip_addr = rs_wpss_sanitize_ip( $wpss_ip_addr );
	return !empty( $wpss_ip_addr ) ? $wpss_ip_addr : '';
}

function rs_wpss_sanitize_ip( $str ) {
	/**
	 *  Sanitize IP address input from $_SERVER[] vars
	 *  Can extract IP address from a list of IP's, from forwarded data, remove port #, etc.
	 *  It is possible for $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_X_FORWARDED_FOR'], etc., to return a list instead of a single IP
	 *  Run IP data through this function to sanitize before using in code
	 *  @since 1.9.9.3
	 */
	if( empty( $str ) ) { return ''; }
	$forwarded = array( 'for', '=', '"', );
	$str_tmp = str_replace( $forwarded, '', $str );
	$str_tmp = strtok( $str_tmp, ', ;' ); strtok('', '');
	$str_tmp = strtok( $str_tmp, ':' ); strtok('', '');
	$str_tmp = trim( $str_tmp );
	return rs_wpss_is_valid_ip( $str_tmp ) ? $str_tmp : '';
}

function rs_wpss_get_real_ip() { /* NOT COMPLETE OR IN USE YET */
	/**
	 *  Detect user's real IP when using surrogates (reverse proxies, WAFs, load balancers, etc)
	 */
	global $wpss_real_ip,$_WPSS_ENV;
	if( !empty( $wpss_real_ip ) ) { return $wpss_real_ip; }
	$wpss_real_ip = !empty( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : $_WPSS_ENV['REMOTE_ADDR'];
	$wpss_real_ip  = rs_wpss_sanitize_ip( $wpss_real_ip  );
	if( empty( $wpss_real_ip ) ) { $wpss_real_ip = ''; }
	/**
	 *  TO DO: Add detection for reverse proxies: Incapsula, CloudFlare, Sucuri
	 *  Detect Incapsula, and disable rs_wpss_ubl_cache - 1.8.9.6 *
	 *  if( strpos( $reverse_dns_lc, '.ip.incapdns.net' ) !== FALSE ) { update_option( 'spamshield_ubl_cache_disable', TRUE ); }
	 */
	/* return !empty( $wpss_real_ip ) ? $wpss_real_ip : ''; */
	$wpss_real_ip  = rs_wpss_sanitize_ip( $wpss_real_ip  );
	return $wpss_real_ip;
}

function rs_wpss_get_ip_cbl( $ip = NULL ) {
	/**
	 *  Return C-Block of an IP Address
	 *  @since 1.9.6.8
	 */
	if( empty( $ip ) || !rs_wpss_is_valid_ip( $ip ) || strpos( $ip, '.' ) === FALSE ) { return ''; }
	$ip_arr = explode( '.', $ip ); unset( $ip_arr[3] ); $ip_c = implode( '.', $ip_arr ) . '.';
	return $ip_c;
}

function rs_wpss_compare_ip_cbl( $ip_1 = NULL, $ip_2 = NULL ) {
	/**
	 *  Compare two IP address C-Blocks to see if they match
	 */
	if( empty( $ip_1 ) || empty( $ip_2 ) ) { return FALSE; }
	$ip_1_cbl	= rs_wpss_get_ip_cbl($ip_1);
	$ip_2_cbl	= rs_wpss_get_ip_cbl($ip_2);
	if( $ip_1_cbl === $ip_2_cbl ) { return TRUE; }
	return FALSE;
}

function rs_wpss_get_reverse_dns( $ip ) {
	/**
	 *  Get the reverse DNS ( hostname ) of an IP address
	 *  Be sure to run any IPs through rs_wpss_sanitize_ip() first before sending here
	 */
	if( empty( $ip ) || $ip == '.' ) { return ''; }
	global $wpss_reverse_dns_cache;
	$wpss_hash = ( defined( 'WPSS_HASH' ) ) ? WPSS_HASH : COOKIEHASH;
	$sess_var = 'wpss_reverse_dns_cache_'.$wpss_hash;
	if( rs_wpss_is_session_active() ) {
		if( !empty( $wpss_reverse_dns_cache ) && is_array( $wpss_reverse_dns_cache ) && isset( $wpss_reverse_dns_cache[$ip] ) ) {
			if( empty( $_SESSION[$sess_var] ) || !is_array( $_SESSION[$sess_var] ) ) { $_SESSION[$sess_var] = array(); }
			$_SESSION[$sess_var][$ip] = $wpss_reverse_dns_cache[$ip];
			return $wpss_reverse_dns_cache[$ip];
		}
		if( !empty( $_SESSION[$sess_var] )  && is_array( $_SESSION[$sess_var] ) && isset( $_SESSION[$sess_var][$ip] ) ) {
			if( empty( $wpss_reverse_dns_cache ) || !is_array( $wpss_reverse_dns_cache ) ) { $wpss_reverse_dns_cache = array(); }
			$wpss_reverse_dns_cache[$ip] = $_SESSION[$sess_var][$ip];
			return $wpss_reverse_dns_cache[$ip];
		}
		if( empty( $_SESSION[$sess_var] ) || !is_array( $_SESSION[$sess_var] ) ) { $_SESSION[$sess_var] = array(); }
	}
	if( empty( $wpss_reverse_dns_cache ) || !is_array( $wpss_reverse_dns_cache ) ) { $wpss_reverse_dns_cache = array(); }
	$reverse_dns = @gethostbyaddr($ip);
	$wpss_reverse_dns_cache[$ip] = ( $reverse_dns != '.' ) ? $reverse_dns : '';
	if( rs_wpss_is_session_active() ) { $_SESSION[$sess_var][$ip] = $wpss_reverse_dns_cache[$ip]; }
	return $wpss_reverse_dns_cache[$ip];
}

function rs_wpss_get_reverse_dns_ip( $host ) {
	/**
	 *  Get the reverse DNS IP of a hostname (domain)
	 */
	$host = trim( $host ); if( empty( $host ) || $host == '.' ) { return ''; }
	global $wpss_reverse_dns_ip_cache;
	$wpss_hash = ( defined( 'WPSS_HASH' ) ) ? WPSS_HASH : COOKIEHASH;
	$sess_var = 'wpss_reverse_dns_ip_cache_'.$wpss_hash;
	if( rs_wpss_is_session_active() ) {
		if( !empty( $wpss_reverse_dns_ip_cache ) && is_array( $wpss_reverse_dns_ip_cache ) && isset( $wpss_reverse_dns_ip_cache[$host] ) ) {
			if( empty( $_SESSION[$sess_var] ) || !is_array( $_SESSION[$sess_var] ) ) { $_SESSION[$sess_var] = array(); }
			$_SESSION[$sess_var][$host] = $wpss_reverse_dns_ip_cache[$host];
			return $wpss_reverse_dns_ip_cache[$host];
		}
		if( !empty( $_SESSION[$sess_var] )  && is_array( $_SESSION[$sess_var] ) && isset( $_SESSION[$sess_var][$host] ) ) {
			if( empty( $wpss_reverse_dns_ip_cache ) || !is_array( $wpss_reverse_dns_ip_cache ) ) { $wpss_reverse_dns_ip_cache = array(); }
			$wpss_reverse_dns_ip_cache[$host] = $_SESSION[$sess_var][$host];
			return $wpss_reverse_dns_ip_cache[$host];
		}
		if( empty( $_SESSION[$sess_var] ) || !is_array( $_SESSION[$sess_var] ) ) { $_SESSION[$sess_var] = array(); }
	}
	if( empty( $wpss_reverse_dns_ip_cache ) || !is_array( $wpss_reverse_dns_ip_cache ) ) { $wpss_reverse_dns_ip_cache = array(); }
	$reverse_dns_ip = @gethostbyname($host);
	$wpss_reverse_dns_ip_cache[$host] = rs_wpss_sanitize_ip($reverse_dns_ip);
	if( rs_wpss_is_session_active() ) { $_SESSION[$sess_var][$host] = $wpss_reverse_dns_ip_cache[$host]; }
	return $wpss_reverse_dns_ip_cache[$host];
}

function rs_wpss_ip_proxy_info() {
	/* IP / PROXY INFO - BEGIN */
	$ip = rs_wpss_get_ip_addr();
	if(!empty($_SERVER['HTTP_VIA'])) { $ip_proxy_via=rs_wpss_sanitize_ip($_SERVER['HTTP_VIA']); } else { $ip_proxy_via = ''; }
	$ip_proxy_via_lc = rs_wpss_casetrans('lower',$ip_proxy_via);
	$masked_ip = '';
	if( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$masked_ip = rs_wpss_sanitize_ip($_SERVER['HTTP_X_FORWARDED_FOR']);
		if( $masked_ip === $ip || !rs_wpss_is_valid_ip( $masked_ip ) ) { $masked_ip = ''; }
	}
	$http_x_forwarded			= !empty( $_SERVER['HTTP_X_FORWARDED'] )		? rs_wpss_sanitize_ip( $_SERVER['HTTP_X_FORWARDED'] )		: '';
	$http_forwarded_for			= !empty( $_SERVER['HTTP_FORWARDED_FOR'] )		? rs_wpss_sanitize_ip( $_SERVER['HTTP_FORWARDED_FOR'] )		: '';
	$http_forwarded				= !empty( $_SERVER['HTTP_FORWARDED'] )			? rs_wpss_sanitize_ip( $_SERVER['HTTP_FORWARDED'] )			: '';
	$http_x_real_ip				= !empty( $_SERVER['HTTP_X_REAL_IP'] )			? rs_wpss_sanitize_ip( $_SERVER['HTTP_X_REAL_IP'] )			: '';
	$http_x_sucuri_clientip		= !empty( $_SERVER['HTTP_X_SUCURI_CLIENTIP'] )	? rs_wpss_sanitize_ip( $_SERVER['HTTP_X_SUCURI_CLIENTIP'] )	: '';
	$http_cf_connecting_ip		= !empty( $_SERVER['HTTP_CF_CONNECTING_IP'] )	? rs_wpss_sanitize_ip( $_SERVER['HTTP_CF_CONNECTING_IP'] )	: '';
	$http_incap_client_ip		= !empty( $_SERVER['HTTP_INCAP_CLIENT_IP'] )	? rs_wpss_sanitize_ip( $_SERVER['HTTP_INCAP_CLIENT_IP'] )	: '';
	$http_client_ip				= !empty( $_SERVER['HTTP_CLIENT_IP'] )			? rs_wpss_sanitize_ip( $_SERVER['HTTP_CLIENT_IP'] )			: '';
	$reverse_dns 				= $rev_dns = rs_wpss_get_reverse_dns($ip);
	if( empty( $reverse_dns ) || $reverse_dns == '.' ) { $reverse_dns_ip = '[No Data]'; } elseif( $reverse_dns === $ip ) { $reverse_dns_ip = $ip; } else { $reverse_dns_ip = rs_wpss_get_reverse_dns_ip($reverse_dns); }
	$reverse_dns_ip_regex 		= rs_wpss_preg_quote($reverse_dns_ip);
	$reverse_dns_lc 			= $rev_dns_lc = rs_wpss_casetrans('lower',$reverse_dns);
	$reverse_dns_lc_regex 		= rs_wpss_preg_quote($reverse_dns_lc);
	$reverse_dns_lc_rev 		= strrev($reverse_dns_lc);
	$reverse_dns_lc_rev_regex 	= rs_wpss_preg_quote($reverse_dns_lc_rev);
	/* Forward-confirmed reverse DNS (FCrDNS) */
	$reverse_dns_verification	= $fcrdns = ( $reverse_dns_ip === $ip ) ? '[Verified]' : '[Possibly Forged]';
	/* Detect Incapsula, and disable rs_wpss_ubl_cache - 1.8.9.6 */
	if( strpos( $reverse_dns_lc, '.ip.incapdns.net' ) !== FALSE ) { update_option( 'spamshield_ubl_cache_disable', TRUE ); }
	/* Detect Use of Proxy */
	$ip_proxy_chrome_compression='FALSE';
	if( !empty( $ip_proxy_via ) || !empty( $masked_ip ) ) {
		if( empty( $masked_ip ) ) { $masked_ip='[No Data]'; }
		$ip_proxy='PROXY DETECTED'; $ip_proxy_short='PROXY'; $ip_proxy_data=$ip.' | MASKED IP: '.$masked_ip; $proxy_status='TRUE';
		/* Google Chrome Compression Check */
		if( strpos( $ip_proxy_via_lc, 'chrome-compression-proxy' ) !== FALSE && preg_match( "~^google\-proxy\-(.*)\.google\.com$~i", $reverse_dns ) ) { $ip_proxy_chrome_compression='TRUE'; }
	} else { $ip_proxy=$ip_proxy_short='No Proxy'; $ip_proxy_data=$ip; $proxy_status='FALSE'; }
	if( !empty( $reverse_dns_lc ) && !isset( $_SERVER['REMOTE_HOST'] ) ) { $_SERVER['REMOTE_HOST'] = $reverse_dns_lc; }
	$_SERVER['X_FCRDNS'] = $fcrdns; $_SERVER['X_REVDNS_IP'] = $reverse_dns_ip;
	/* IP / PROXY INFO - END */

	$ip_proxy_info = compact( 'ip', 'reverse_dns', 'rev_dns', 'reverse_dns_lc', 'rev_dns_lc', 'reverse_dns_lc_regex', 'reverse_dns_lc_rev', 'reverse_dns_lc_rev_regex', 'reverse_dns_ip', 'reverse_dns_ip_regex', 'reverse_dns_verification', 'fcrdns', 'ip_proxy_via', 'ip_proxy_via_lc', 'masked_ip', 'http_x_forwarded', 'http_forwarded_for', 'http_forwarded', 'http_x_real_ip', 'http_x_sucuri_clientip', 'http_cf_connecting_ip	', 'http_incap_client_ip', 'http_client_ip', 'ip_proxy', 'ip_proxy_short', 'ip_proxy_data', 'proxy_status', 'ip_proxy_chrome_compression' );
	return $ip_proxy_info;
}

function rs_wpss_get_ns( $domain ) {
	$domain = rs_wpss_get_email_domain( $domain );
	while( !empty( $domain ) && !preg_match( "~^([a-z]{2,3})(\.[a-z]{2,3})[a-z]*$~i", $domain ) && empty( $ns ) ) {
		$dns_ns = @dns_get_record( $domain, DNS_NS ); $ns = array();
		if( !empty( $dns_ns ) && is_array( $dns_ns ) ) {
			foreach( $dns_ns as $i => $a ) {
				if( empty( $a['target'] ) ) { continue; } else { $ns[] = $a['target']; }
			}
			if( !empty( $ns ) ) { break; }
		}
		$dom_els = explode( '.', $domain ); unset( $dom_els[0] );
		$domain = implode( '.', $dom_els );
	}
	return !empty( $ns ) ? $ns : FALSE;
}

function rs_wpss_get_server_x_req_w() {
	return !empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ? rs_wpss_casetrans( 'lower', $_SERVER['HTTP_X_REQUESTED_WITH'] ) : '';
}

function rs_wpss_get_query_arr($url) {
	/* Get array of variables from query string */
	$query_str = rs_wpss_get_query_string($url); /* 1.7.3 - Validates better */
	if( !empty( $query_str ) ) { $query_arr = explode( '&', $query_str ); } else { $query_arr = ''; }
	return $query_arr;
}

function rs_wpss_remove_query( $url, $skip_wp_args = FALSE ) {
	/**
	 *  For removing specific query argument(s)
	 *  If you need URL fragments removed, or the entire query string removed, use rs_wpss_fix_url()
	 */
	$query_arr = rs_wpss_get_query_arr($url);
	if( empty( $query_arr ) ) { return $url; }
	$remove_args = array();
	foreach( $query_arr as $i => $query_arg ) {
		$query_arg_arr = explode( '=', $query_arg );
		$key = $query_arg_arr[0];
		if( !empty( $skip_wp_args ) && ( $key === 'p' || $key === 'page_id' ) ) { continue; } /* DO NOT ADD 'cpage', only 'p' and 'page_id'!! */
		$remove_args[] = $key;
	}
	$clean_url = remove_query_arg( $remove_args, $url );
	return $clean_url;
}

function rs_wpss_get_user_agent( $raw = FALSE, $lowercase = FALSE ) {
	/**
	 *  Gives User-Agent with filters
	 *  If blank, gives an initialized var to eliminate need for testing if isset() everywhere
	 *  Default is sanitized - use raw for testing, and sanitized for output
	 *  Added option for raw & lowercase in 1.5
	 */
	if( !empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
		$user_agent = !empty ( $raw ) ? trim( $_SERVER['HTTP_USER_AGENT'] ) : rs_wpss_sanitize_string( $_SERVER['HTTP_USER_AGENT'] );
		if( !empty ( $lowercase ) )	{ $user_agent = rs_wpss_casetrans( 'lower', $user_agent ); }
	} else { $user_agent = ''; }
	return $user_agent;
}

function rs_wpss_get_plugin_user_agent() {
	return 'WP-SpamShield/'.WPSS_VERSION.' (WordPress/'.WPSS_WP_VERSION.') PHP/'.WPSS_PHP_VERSION.' ('.$_SERVER['SERVER_SOFTWARE'].')';
}

function rs_wpss_get_http_accept( $raw = FALSE, $lowercase = FALSE, $lang = FALSE, $encd = FALSE ) {
	/**
	 *  Gives $_SERVER['HTTP_ACCEPT'], $_SERVER['HTTP_ACCEPT_LANGUAGE'], and $_SERVER['HTTP_ACCEPT_ENCODING'] with filters
	 *  Default is sanitized
	 */
	$http_accept = $http_accept_language = $http_accept_encoding = '';
	if( !empty( $_SERVER['HTTP_ACCEPT'] ) )				{ $http_accept			= $_SERVER['HTTP_ACCEPT']; }
	if( !empty( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) )	{ $http_accept_language	= $_SERVER['HTTP_ACCEPT_LANGUAGE']; }
	if( !empty( $_SERVER['HTTP_ACCEPT_ENCODING'] ) )	{ $http_accept_encoding	= $_SERVER['HTTP_ACCEPT_ENCODING']; }
	$raw_serv_var							= $http_accept;
	if( !empty( $lang ) ) { $raw_serv_var	= $http_accept_language; }
	if( !empty( $encd ) ) { $raw_serv_var	= $http_accept_encoding; }
	if( !empty( $raw_serv_var ) ) {
		$http_accept_var = !empty ( $raw ) ? trim( $raw_serv_var ) : rs_wpss_sanitize_string( $raw_serv_var );
		if( !empty ( $lowercase ) ) { $http_accept_var = rs_wpss_casetrans( 'lower', $http_accept_var ); }
	} else { $http_accept_var = ''; }
	return $http_accept_var;
}

function rs_wpss_get_referrer( $raw = FALSE, $lowercase = FALSE, $init = FALSE ) {
	/**
	 *  Gives $_SERVER['HTTP_REFERER'] with filters
	 *  Default is sanitized
	 */
	global $wpss_referrer; if( !empty( $wpss_referrer ) ) { return $wpss_referrer; }
	$http_referrer = $init_referrer = '';
	$site_domain = WPSS_SERVER_NAME;
	if( !empty( $_SERVER['HTTP_REFERER'] ) ){ $http_referrer = $_SERVER['HTTP_REFERER']; }
	if( !empty( $_COOKIE['JCS_INENREF'] ) )	{
		$init_referrer			= $_COOKIE['JCS_INENREF'];
		$init_referrer_no_query	= rs_wpss_fix_url( $init_referrer, TRUE, TRUE ); /* Remove query string and fragments */
		if( strpos( $init_referrer_no_query, $site_domain ) !== FALSE ) { $init_referrer = ''; } /* Tracking referrals from other sites only */
	}
	if( empty( $init_referrer ) && !empty( $_COOKIE['_referrer_og'] ) )	{
		$init_referrer			= $_COOKIE['_referrer_og'];
		$init_referrer_no_query	= rs_wpss_fix_url( $init_referrer, TRUE, TRUE ); /* Remove query string and fragments */
		if( strpos( $init_referrer_no_query, $site_domain ) !== FALSE ) { $init_referrer = ''; } /* Tracking referrals from other sites only */
	}
	$wpss_referrer = $http_referrer;
	if( !empty( $init ) ) { $wpss_referrer = $init_referrer; }
	if( !empty( $wpss_referrer ) ) {
		$wpss_referrer = !empty ( $raw ) ? trim( $wpss_referrer ) : esc_url_raw( $wpss_referrer );
		if( !empty ( $lowercase ) )	{ $wpss_referrer = rs_wpss_casetrans( 'lower', $wpss_referrer ); }
	} else { $wpss_referrer = ''; }
	return $wpss_referrer;
}

function rs_wpss_get_error_type( $error_code ) {
	/**
	 *  Returns type of error - JavaScript/Cookies Layer or Algorithmic Layer - 'jsck' or 'algo'
	 *  @since 1.8.9.6
	 */
	if( empty( $error_code ) ) { return FALSE; }
	if( strpos( $error_code, 'COOKIE-' ) !== FALSE || strpos( $error_code, 'REF-2-1023-' ) !== FALSE || strpos( $error_code, 'JSONST-1000-' ) !== FALSE || strpos( $error_code, 'FVFJS-' ) !== FALSE  || strpos( $error_code, 'JQHFT-' ) !== FALSE ) { return 'jsck'; }
	else { return 'algo'; }
}

function rs_wpss_is_valid_ip( $ip, $incl_priv_res = FALSE, $ipv4_c_block = FALSE ) {
	if( empty( $ip ) ) { return FALSE; }
	if( !empty( $ipv4_c_block ) ) {
		if( preg_match( "~^(([0-9]|[0-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\.){3}$~", $ip ) ) { return TRUE; } /* Valid C-Block check - checking for C-block: '123.456.78.' format */
	}
	if( function_exists( 'filter_var' ) ) {
		if( empty( $incl_priv_res ) ) { if( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) { return TRUE; } }
		elseif( filter_var( $ip, FILTER_VALIDATE_IP ) ) { return TRUE; }
		/* FILTER_FLAG_IPV4,FILTER_FLAG_IPV6,FILTER_FLAG_NO_PRIV_RANGE,FILTER_FLAG_NO_RES_RANGE */
	} elseif( preg_match( "~^(([0-9]|[0-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[0-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])$~", $ip ) && !preg_match( "~^192\.168\.~", $ip ) ) { return TRUE; }
	return FALSE;
}

function rs_wpss_is_valid_url( $url ) {
	/**
	 *  Checks if user-input URL is valid
	 *  Use for built-in contact form
	 *  @since 1.9.7
	 */
	if( empty( $url ) || FALSE === strpos( $url, '.' ) ) { return FALSE; }
	$url = rs_wpss_casetrans( 'lower', $url );
	if( rs_wpss_is_valid_ip( $url ) ) { return FALSE; }
	if( $url !== esc_url_raw( $url ) ) { $url_san = esc_url_raw( $url ); return FALSE; }
	if( filter_var( $url, FILTER_VALIDATE_URL ) ) { return TRUE; }
	return FALSE;
}

function rs_wpss_is_valid_email( $email ) {
	/**
	 *  Checks if valid first, then checks email against blacklists
	 *  Use for one-stop email check
	 *  @since 1.9.5.8
	 */
	if( empty( $email ) ) { return FALSE; }
	$email = rs_wpss_casetrans( 'lower', $email );
	if( !is_email( $email ) ) { return FALSE; }
	if( rs_wpss_email_blacklist_chk( $email ) ) { return FALSE; }
	return $email;
}

function rs_wpss_email_domain_exists( $email ) {
	/**
	 *  Checks if email domain exists (has MX records)
	 *  Use for contact form, registration, anything
	 *  @since 1.9.8.5
	 */
	if( empty( $email ) ) { return FALSE; }
	$email = rs_wpss_casetrans( 'lower', $email );
	if( !is_email( $email ) ) { return FALSE; }
	if( rs_wpss_is_free_email( $email ) ) { return TRUE; }
	$email_domain = rs_wpss_parse_email( $email, 'domain' );
	if ( @checkdnsrr( $email_domain.'.', 'MX' ) ) { return TRUE; }
	return FALSE;
}

function rs_wpss_domain_exists( $domain ) {
	/**
	 *  Checks if domain exists (has ANY records)
	 *  Use for contact form, anything
	 *  @since 1.9.8.5
	 */
	if( empty( $domain ) ) { return FALSE; }
	$domain = rs_wpss_casetrans( 'lower', $domain );
	if ( @checkdnsrr( $domain.'.', 'ANY' ) ) { return TRUE; }
	if ( @checkdnsrr( $domain.'.', 'NS' ) ) { return TRUE; }
	return FALSE;
}

function rs_wpss_adv_validate_email( $is_email, $email, $message = NULL ) {
	/**
	 *  This adds some additional validation to the built-in WordPress is_email() function.
	 *  Adds: Max length checks according to RFC, validation for Yahoo and Gmail
	 *  Not adding DNS checks
	 *  @since 1.9.5.6
	 */
	if( empty( $email ) || FALSE === $is_email ) { return FALSE; }
	if( !empty( $message ) ) { return $is_email; }
	$email = rs_wpss_sanitize_gmail( $email, FALSE, FALSE );
	/* Default PHP Filter */
	if( !filter_var( $email, FILTER_VALIDATE_EMAIL ) ) { return FALSE; }
	/* General RFC Tests */
	if( rs_wpss_strlen( $email ) > 254 ) { return FALSE; }
	$parsed = rs_wpss_parse_email( $email, NULL, FALSE ); extract( $parsed );
	$local_strlen = rs_wpss_strlen( $local );
	if( $local_strlen > 64 ) { return FALSE; }
	/* ESP-Specific Tests */
	if( !empty( $provider ) ) {
		if( $provider === 'gmail' || $provider === 'googlemail' ) {
			if( $local_strlen < 6 || FALSE !== strpos( $local, '-' ) ) { return FALSE; }
		} elseif( $provider === 'yahoo' ) {
			if( preg_match( "~[^0-9a-z_\-\.]+~i", $local ) || rs_wpss_substr_count( $local, '.') > 1 ) { return FALSE; }
		}
	}
	return $is_email;
}

function rs_wpss_is_free_email( $email ) {
	if( preg_match( "~\@((g(oogle)?mail|hotmail|outlook|yahoo|mail|gmx|inbox|myway|rediffmail|yandex|rambler|arcor|163|aol|lycos|zoho(mail)?|protonmail|hushmail)(\.[a-z]{2,3}){1,2}[a-z]*|usa\.net)$~i", $email ) ) { return TRUE; }
	return FALSE;
}

function rs_wpss_sanitize_gmail( $email, $rem_alias = FALSE, $chk_is_email = TRUE ) {
	/**
	 *  Sanitizes Gmail/Googlemail addresses for validation and spam checks
	 *  Remove dots and alias (section of local part after '+')
	 *  $rem_alias - Local part - Remove alias only - leave dots
	 *  @since 1.9.5.6
	 */
	if( empty( $email ) ) { return ''; }
	$parsed = rs_wpss_parse_email( $email, NULL, $chk_is_email ); extract( $parsed );
	if( !empty( $provider ) && ( $provider === 'gmail' || $provider === 'googlemail' ) ) {
		if( FALSE !== strpos( $local, '+' ) ) { $l = explode( '+', $local ); $local = $l[0]; }
		if( FALSE === $rem_alias ) { $local = str_replace( '.', '', $local ); }
	}
	$email = $local.'@'.$domain;
	return $email;
}

function rs_wpss_parse_email( $email, $component = NULL, $chk_is_email = TRUE ) {
	/**
	 *  Parses email address similar to PHP parse_url() - http://php.net/manual/en/function.parse-url.php
	 *  Returns an associative array containing the various components of the email address.
	 *  Use $component to get a specific component: 'local', 'domain', 'provider' (if major free email, returns provider)
	 *  @since 1.9.5.6
	 */
	if( TRUE === $chk_is_email ) { if( !is_email( $email ) ) { return array(); } }
	$e = explode( '@', $email );
	$parsed = array( 'local' => $e[0], 'domain' => $e[1], 'provider' => '', );
	if( rs_wpss_is_free_email( $email ) ) { $p = explode( '.', $e[1] ); $parsed['provider'] = $p[0]; }
	if( !empty( $component ) && ( $component === 'local' || $component === 'domain' || $component === 'provider' ) ) { return $parsed[$component]; }
	return $parsed;
}

function rs_wpss_is_google_domain($domain) {
	/**
	 *  Check if domain is a Google Domain
	 *  @since 1.7.8
	 *  Google Domains - updated at: https://www.google.com/supported_domains
	 */
	$google_domains = array(
		'.com','.ad','.ae','.com.af','.com.ag','.com.ai','.al','.am','.co.ao','.com.ar','.as','.at','.com.au','.az','.ba','.com.bd','.be','.bf','.bg','.com.bh','.bi','.bj','.com.bn','.com.bo','.com.br','.bs','.bt','.co.bw','.by','.com.bz','.ca','.cd','.cf','.cg','.ch','.ci','.co.ck','.cl','.cm','.cn','.com.co','.co.cr','.com.cu','.cv','.com.cy','.cz','.de','.dj','.dk','.dm','.com.do','.dz','.com.ec','.ee','.com.eg','.es','.com.et','.fi','.com.fj','.fm','.fr','.ga','.ge','.gg','.com.gh','.com.gi','.gl','.gm','.gp','.gr','.com.gt','.gy','.com.hk','.hn','.hr','.ht','.hu','.co.id','.ie','.co.il','.im','.co.in','.iq','.is','.it','.je','.com.jm','.jo','.co.jp','.co.ke','.com.kh','.ki','.kg','.co.kr','.com.kw','.kz','.la','.com.lb','.li','.lk','.co.ls','.lt','.lu','.lv','.com.ly','.co.ma','.md','.me','.mg','.mk','.ml','.com.mm','.mn','.ms','.com.mt','.mu','.mv','.mw','.com.mx','.com.my','.co.mz','.com.na','.com.nf','.com.ng','.com.ni','.ne','.nl','.no','.com.np','.nr','.nu','.co.nz','.com.om','.com.pa','.com.pe','.com.pg','.com.ph','.com.pk','.pl','.pn','.com.pr','.ps','.pt','.com.py','.com.qa','.ro','.ru','.rw','.com.sa','.com.sb','.sc','.se','.com.sg','.sh','.si','.sk','.com.sl','.sn','.so','.sm','.sr','.st','.com.sv','.td','.tg','.co.th','.com.tj','.tk','.tl','.tm','.tn','.to','.com.tr','.tt','.com.tw','.co.tz','.com.ua','.co.ug','.co.uk','.com.uy','.co.uz','.com.vc','.co.ve','.vg','.co.vi','.com.vn','.vu','.ws','.rs','.co.za','.co.zm','.co.zw','.cat',
		);
	$google_dom_base = array( 'google', 'blogger', );
	$tmp_slug = 'XXGOOGLEXX';
	foreach( $google_domains as $i => $ext ) {
		$domain_tmp_slug = $tmp_slug.$ext;
		$regex_tmp = rs_wpss_get_regex_phrase( $domain_tmp_slug, '', 'domain' );
		$regex_check_phrase = str_replace( $tmp_slug, '('.implode( '|', $google_dom_base ).')', $regex_tmp );
		if( preg_match( $regex_check_phrase, $domain ) ) { return TRUE; }
	}
	return FALSE;
}

function rs_wpss_is_google_ip($ip) {
	/**
	 *  Check if domain is a Google IP
	 *  @since 1.7.8
	 */
	if( preg_match( "~^(64\.233\.1([6-8][0-9]|9[0-1])|66\.102\.([0-9]|1[0-5])|66\.249\.(6[4-9]|[7-8][0-9]|9[0-5])|72\.14\.(19[2-9]|2[0-4][0-9]|25[0-5])|74\.125\.([0-9]|[0-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])|209\.85\.(1(2[8-9]|[3-9][0-9])|2[0-4][0-9]|25[0-5])|216\.239\.(3[2-9]|[4-5][0-9]|6[0-3]))\.~", $ip ) ) { return TRUE; }
	return FALSE;
}

function rs_wpss_is_opera_ip($ip) {
	/**
	 *  Check if domain is an Opera IP
	 *  @since 1.9.8.3
	 */
	if( preg_match( "~^(37\.228\.1(0[4-9]|1[01])|82\.145\.2(0[89]|1[0-9]|2[0-3])|91\.203\.9[6-9]|107\.167\.(9[6-9]|1([01][0-9]|2[0-6]))|141\.0\.([89]|1[0-5])|185\.26\.18[0-3]|195\.189\.14[23])\.~", $ip ) ) { return TRUE; }
	return FALSE;
}

function rs_wpss_get_regex_phrase( $input, $custom_delim = NULL, $flag = "N" ) {
	/* Get Regex Phrase from an Array or String */
	$flag_regex_arr = array(
		"N" 			=> "(^|[\s\.])(X)($|[\s\.\:,\!\?\@\/])",
		"S" 			=> "^(X)($|[\s\.\:,\!\?\@\/])",
		"E" 			=> "(^|[\s\.])(X)$",
		"W" 			=> "^(X)$",
		"email_addr"	=> "^(X)$",
		"email_prefix" 	=> "^(X)",
		"email_domain" 	=> "\@((ww[w0-9]|m)\.)?(X)$",
		"domain" 		=> "^((ww[w0-9]|m)\.)?(X)$",
		"ns" 			=> "(^|\.)(X)$",
		"tld" 			=> "\.(X)$",
		"authorkw"		=> "(^|\s+)(X)($|\s+)",
		"atxtwrap"		=> "(<\s*a\s+[a-z0-9\-_\.\?\='\"\:\(\)\{\}\s]*\s*href|\[(url|link))\s*\=\s*['\"]?\s*(https?\:/+[a-z0-9\-_\/\.\?\&\=\~\@\%\+\#\:]+)\s*['\"]?\s*[a-z0-9\-_\.\?\='\"\:;\(\)\{\}\s]*\s*(>|\])([a-z0-9àáâãäåçèéêëìíîïñńņňòóôõöùúûü\-_\/\.\?\&\=\~\@\%\+\#\:;\!,'\(\)\{\}\s]*\s+)?(X)([a-z0-9àáâãäåçèéêëìíîïñńņňòóôõöùúûü\-_\/\.\?\&\=\~\@\%\+\#\:;\!,'\(\)\{\}\s]*\s+)?(<|\[)\s*\/\s*a\s*(>|(url|link)\])",
		/**
		 *  REFERENCE: Parse full html links with this:
		 *  $parse_links_regex = "~(<\s*a\s+[a-z0-9\-_\.\?\='\"\:\(\)\{\}\s]*\s*href|\[(url|link))\s*\=\s*['\"]?\s*(https?\:/+[a-z0-9\-_\/\.\?\&\=\~\@\%\+\#\:]+)\s*['\"]?\s*[a-z0-9\-_\.\?\='\"\:;\(\)\{\}\s]*\s*(>|\])([a-z0-9àáâãäåçèéêëìíîïñńņňòóôõöùúûü\-_\/\.\?\&\=\~\@\%\+\#\:;\!,'\(\)\{\}\s]*)(<|\[)\s*\/\s*a\s*(>|(url|link)\])~iu";
		 */
		"linkwrap"		=> "(<\s*a\s+([a-z0-9\-_\.\?\='\"\:\(\)\{\}\s]*)\s*href|\[(url|link))\s*\=\s*(['\"])?\s*https?\:/+((ww[w0-9]|m)\.)?(X)/?([a-z0-9\-_\/\.\?\&\=\~\@\%\+\#\:]*)(['\"])?(>|\])",
		"httplinkwrap"	=> "(^|\b)https?\:/+((ww[w0-9]|m)\.)?(X)/?([a-z0-9\-_\/\.\?\&\=\~\@\%\+\#\:]*)",
		/**
		 *  REFERENCE: Parse stripped http links with this:
		 *  $search_http_regex ="~\s+(https?\://[a-z0-9\-_\/\.\?\&\=\~\@\%\+\#\:]+)\s+~i";
		 */
		"red_str"		=> "(X)", /* Red-flagged string */
		"rgx_str"		=> "(X)", /* Regex-ready string */
		);
	if( is_array( $input) ) {
		$regex_flag = $flag_regex_arr[$flag];
		$regex_phrase_pre_arr = array();
		foreach( $input as $i => $val ) {
			if( $flag === "rgx_str" || $flag === "authorkw" || $flag === "atxtwrap" || $flag === "tld" ) { $val_reg_pre = $val; } /* Variable must come in prepped for regex (preg_quoted) */
			else { $val_reg_pre = rs_wpss_preg_quote($val); }
			$regex_phrase_pre_arr[] = $val_reg_pre;
		}
		$regex_phrase_pre_str 	= implode( "|", $regex_phrase_pre_arr );
		$regex_phrase_str 		= preg_replace( "~X~", $regex_phrase_pre_str, $regex_flag );
		if( !empty( $custom_delim ) ) {  $delim = $custom_delim; } else { $delim = "~"; }
		$regex_phrase = $delim.$regex_phrase_str.$delim."iu"; /* UTF-8 enabled */
		if( $flag === "email_addr" || $flag === "red_str" ) {
			$regex_phrase = str_replace( '@gmail\.', '@g(oogle)?mail\.', $regex_phrase );
		}
	} elseif( is_string( $input ) ) {
		$val = $input;
		$regex_flag = $flag_regex_arr[$flag];
		if( $flag === "rgx_str" || $flag === "authorkw" || $flag === "atxtwrap" || $flag === "tld" ) { $val_reg_pre = $val; } /* Variable must come in prepped for regex (preg_quoted) */
		else { $val_reg_pre = rs_wpss_preg_quote($val); }
		$regex_phrase_str 	= preg_replace( "~X~", $val_reg_pre, $regex_flag );
		if( !empty( $custom_delim ) ) {  $delim = $custom_delim; } else { $delim = "~"; }
		$regex_phrase = $delim.$regex_phrase_str.$delim."iu"; /* UTF-8 enabled */
		if( $flag === "email_addr" || $flag === "red_str" ) {
			$regex_phrase = str_replace( '@gmail\.', '@g(oogle)?mail\.', $regex_phrase );
		}
	} else { return $input; }
	return $regex_phrase;
}

function rs_wpss_rbkmd( $dat, $mod = 'en', $exp = FALSE, $imp = FALSE, $case = NULL, $del = '~' ) {
	if( !empty( $imp ) && is_array( $dat ) ) { $dat = implode(  $del, $dat ); }
	$lft = '.!:;1234567890|abcdefghijklmnopqrstuvwxyz{}()<>~@#$%^&*?,_-+= \/';
	$rgt = 'ghiJVWXyz@#$%^&*?,_-+=1234567890ABCdefGHIjklMNOpqrSTUvwxYZabcDEF';
	if( $mod === 'en' ) { $mod_dat = strtr( $dat, $lft, $rgt ); } else { $mod_dat = strtr( $dat, $rgt, $lft ); }
	if( !empty( $case ) ) { $mod_dat = rs_wpss_casetrans( $case, $mod_dat ); }
	if( !empty( $exp ) ) { $mod_dat = explode(  $del, $mod_dat ); }
	return $mod_dat;
}

function rs_wpss_is_plugin_active( $plug_bn, $check_network = TRUE ) { /* DEPRECATED */
	return WPSS_Compatibility::is_plugin_active( $plug_bn, $check_network );
}

function rs_wpss_get_active_plugins( $sort = TRUE ) {
	global $wpss_active_plugins;
	if( empty( $wpss_active_plugins ) || !is_array( $wpss_active_plugins ) ) { $wpss_active_plugins = get_option( 'active_plugins' ); }
	if( TRUE === $sort ) { $wpss_active_plugins = rs_wpss_sort_unique( $wpss_active_plugins ); }
	return $wpss_active_plugins;
}

function rs_wpss_get_active_network_plugins() {
	global $wpss_active_network_plugins;
	if( empty( $wpss_active_network_plugins ) || !is_array( $wpss_active_network_plugins ) ) {
		$wpss_active_network_plugins = get_site_option( 'active_sitewide_plugins' );
		if( !empty( $wpss_active_network_plugins ) && is_array( $wpss_active_network_plugins ) ) {
			$wpss_active_network_plugins = rs_wpss_sort_unique( array_flip( $wpss_active_network_plugins ) );
		}
	}
	return $wpss_active_network_plugins;
}

function rs_wpss_is_user_admin() {
	global $wpss_user_can_manage_options,$wpss_is_user_logged_in;
	if( empty( $_COOKIE ) || rs_wpss_ck_sess_o() ) { $wpss_user_can_manage_options = $wpss_is_user_logged_in = 'NO'; return FALSE; }
	if( empty( $wpss_user_can_manage_options ) ) { $wpss_user_can_manage_options = current_user_can( 'manage_options' ) ? 'YES' : 'NO'; }
	if( $wpss_user_can_manage_options === 'YES' ) { $wpss_is_user_logged_in = 'YES'; $_SERVER['WPSS_SEC_THREAT'] = FALSE; return TRUE; }
	$wpss_user_can_manage_options === 'NO';
	return FALSE;
}

function rs_wpss_is_user_logged_in() {
	/**
	 *  Wrapper for is_user_logged_in() when only checking current user
	 *  Much faster check if user has no cookies or only session cookie, and save DB queries
	 */
	global $wpss_is_user_logged_in,$wpss_user_can_manage_options;
	if( empty( $_COOKIE ) || rs_wpss_ck_sess_o() ) { $wpss_is_user_logged_in = $wpss_user_can_manage_options = 'NO'; return FALSE; }
	if( empty( $wpss_is_user_logged_in ) ) { $wpss_is_user_logged_in = is_user_logged_in() ? 'YES' : 'NO'; }
	if( $wpss_is_user_logged_in === 'YES' ) { $_SERVER['WPSS_SEC_THREAT'] = FALSE; return TRUE; }
	$wpss_is_user_logged_in = $wpss_user_can_manage_options = 'NO';
	return FALSE;
}

function rs_wpss_valid_sess_ck() {
	global $wpss_session_id,$wpss_session_name,$wpss_valid_sess_ck;
	if( !empty( $wpss_valid_sess_ck ) ) { $wpss_valid_sess_ck = TRUE; return TRUE; }
	if( empty( $_COOKIE ) ) { $wpss_valid_sess_ck = FALSE; return FALSE; }
	$wpss_valid_sess_ck = FALSE;
	if( empty( $wpss_session_id ) ) 	{ $wpss_session_id		= @session_id(); }
	if( empty( $wpss_session_name ) ) 	{ $wpss_session_name	= @session_name(); }
	if( empty( $wpss_session_name ) )	{ $wpss_session_name	= 'PHPSESSID'; }
	if( !empty( $_COOKIE[$wpss_session_name] ) && $_COOKIE[$wpss_session_name] === $wpss_session_id ) { $wpss_valid_sess_ck = TRUE; }
	return $wpss_valid_sess_ck;
}

function rs_wpss_ck_sess_o() {
	/* Checks if user has only a valid session cookie - common with more recent bots */
	if( empty( $_COOKIE ) || count( $_COOKIE ) >= 2 ) { return FALSE; }
	if( rs_wpss_valid_sess_ck() || count( $_COOKIE ) === 1 ) { return TRUE; }
	return FALSE;
}

function rs_wpss_is_wp_ver( $ver ) {
	/**
	 *  Check if current install is a specific WordPress version or later, for compatibility checks
	 *  @since 1.9.5.7
	 */
	return version_compare( WPSS_WP_VERSION, $ver, '>=' );
}

function rs_wpss_is_php_ver( $ver ) {
	/**
	 *  Check if current install is a specific PHP version or later, for compatibility checks
	 *  @since 1.9.9.5
	 */
	return version_compare( WPSS_PHP_VERSION, $ver, '>=' );
}

function rs_wpss_error_txt( $case = 'UC' ) {
	/* $case = 'def' (default - unaltered), 'UC' (uppercase) */
	$txt = 'Error';
	if( !rs_wpss_is_lang_en_us() ) {
		$txt = trim( str_replace( array( '&rsaquo;', 'WordPress &raquo; ', ), array( '&raquo;', ' ', ), __('WordPress &rsaquo; Error') ) );
		if( rs_wpss_strlen( $txt ) > 17 ) { $txt = trim( str_replace( ':', '', __('ERROR:') ) ); }
	}
	if( empty( $txt ) || !is_string( $txt ) ) { $txt = 'Error'; }
	return $case === 'UC' ? rs_wpss_casetrans( 'upper', $txt ) : $txt;
}

function rs_wpss_first_name_txt( $case = 'UCW' ) {
	/* $case = 'def' (default - unaltered), 'UCW' (uppercase words) */
	global $wpss_loaded_languages;
	if( !rs_wpss_is_lang_en_us() && empty( $wpss_loaded_languages['admin'] ) ) {
		$locale = get_locale(); @load_textdomain( 'default', WP_LANG_DIR . "/admin-$locale.mo" );
	}
	$txt = __('First Name');
	return $case === 'UCW' ? rs_wpss_casetrans( 'ucwords', $txt ) : $txt;
}

function rs_wpss_last_name_txt( $case = 'UCW' ) {
	/* $case = 'def' (default - unaltered), 'UCW' (uppercase words) */
	global $wpss_loaded_languages;
	if( !rs_wpss_is_lang_en_us() && empty( $wpss_loaded_languages['admin'] ) ) {
		$locale = get_locale(); @load_textdomain( 'default', WP_LANG_DIR . "/admin-$locale.mo" );
	}
	$txt = __('Last Name');
	return $case === 'UCW' ? rs_wpss_casetrans( 'ucwords', $txt ) : $txt;
}

function rs_wpss_disp_name_txt( $case = 'UCW' ) {
	/* $case = 'def' (default - unaltered), 'UCW' (uppercase words) */
	global $wpss_loaded_languages;
	if( !rs_wpss_is_lang_en_us() && empty( $wpss_loaded_languages['admin'] ) ) {
		$locale = get_locale(); @load_textdomain( 'default', WP_LANG_DIR . "/admin-$locale.mo" );
	}
	$txt = __('Display name publicly as');
	return $case === 'UCW' ? rs_wpss_casetrans( 'ucwords', $txt ) : $txt;
}

function rs_wpss_enter_your_x_txt( $case = 'def' ) {
	/* $case = 'def' (default - unaltered), 'UCW' (uppercase words) */
	$txt = __( 'Please enter your %s', 'wp-spamshield' );
	return $case === 'UCW' ? rs_wpss_casetrans( 'ucwords', $txt ) : $txt;
}

function rs_wpss_plug_hmpage_txt( $case = 'UCW' ) {
	/* $case = 'def' (default - unaltered), 'UCW' (uppercase words) */
	$txt = trim( str_replace( '&#187;', '', __('Plugin Homepage &#187;') ) );
	return $case === 'UCW' ? rs_wpss_casetrans( 'ucwords', $txt ) : $txt;
}

function rs_wpss_plug_wppage_txt( $case = 'UCW' ) {
	/* $case = 'def' (default - unaltered), 'UCW' (uppercase words) */
	$txt = trim( str_replace( '&#187;', '', __('WordPress.org Plugin Page &#187;') ) );
	return $case === 'UCW' ? rs_wpss_casetrans( 'ucwords', $txt ) : $txt;
}

function rs_wpss_donate_txt( $case = 'def' ) {
	/* $case = 'def' (default - unaltered), 'UCW' (uppercase words) */
	$txt = rs_wpss_is_lang_en_us() ? 'Donate to WP-SpamShield' : trim( str_replace( '&#187;', '', __('Donate to this plugin &#187;') ) );
	return $case === 'UCW' ? rs_wpss_casetrans( 'ucwords', $txt ) : $txt;
}

function rs_wpss_tsg_txt( $case = 'UCW' ) {
	/* $case = 'def' (default - unaltered), 'UCW' (uppercase words) */
	$txt = rs_wpss_is_lang_en_us() ? 'Troubleshooting Guide' : __('Troubleshooting');
	return $case === 'UCW' ? rs_wpss_casetrans( 'ucwords', $txt ) : $txt;
}

function rs_wpss_blocked_txt( $case = 'def' ) {
	/* $case = 'def' (default - unaltered), 'UCW' (uppercase words) */
	$txt = __( 'SPAM BLOCKED', 'wp-spamshield' ); return $case === 'UCW' ? rs_wpss_casetrans( 'ucwords', $txt ) : $txt;
}

function rs_wpss_doc_txt() {
	return __('Documentation');
}

function rs_wpss_is_lang_en_us( $strict = TRUE ) {
	/* Test if site is set to use English (US) - the default - or another language/localization */
	global $wpss_locale, $lang_en_us;
	$wpss_locale = get_locale();
	if( $strict !== TRUE ) {
		/* Not strict - English, but localized translations may be in use */
		if( !empty( $wpss_locale ) && !preg_match( "~^(en(_[a-z]{2})?)?$~i", $wpss_locale ) ) { $lang_en_us = FALSE; } else { $lang_en_us = TRUE; }
	} else {
		/* Strict - English (US), no translation being used */
		if( !empty( $wpss_locale ) && !preg_match( "~^(en(_us)?)?$~i", $wpss_locale ) ) { $lang_en_us = FALSE; } else { $lang_en_us = TRUE; }
	}
	return $lang_en_us;
}

function rs_wpss_wf_geoiploc( $ip = NULL, $disp = FALSE ) {
	/**
	 *  If WordFence installed, get GEO IP Location data
	 *  @since 1.9.5.2
	 */
	global $wpss_geoiploc_data, $wpss_geolocation;
	if( ( class_exists( 'wfUtils' ) && WPSS_Compatibility::is_plugin_active( 'wordfence/wordfence.php' ) ) || function_exists( 'rs_wpss_alt_geoiploc' ) ) {
		/* $start = microtime(TRUE); */
		if( empty( $ip ) ) { $ip = rs_wpss_get_ip_addr(); }
		if( ( empty( $_SESSION['wpss_geoiploc_data_'.WPSS_HASH] ) && empty( $wpss_geoiploc_data ) ) || ( empty( $_SESSION['wpss_geolocation_'.WPSS_HASH] ) && empty( $wpss_geolocation ) || empty( $_SESSION['wpss_geoiploc_ip_'.WPSS_HASH] ) || $_SESSION['wpss_geoiploc_ip_'.WPSS_HASH] !== $ip )  ) {
			if( class_exists( 'wfUtils' ) && method_exists( 'wfUtils', 'getIPGeo' ) ) {
				$wpss_geoiploc_data = wfUtils::getIPGeo( $ip );
			} elseif( function_exists( 'rs_wpss_alt_geoiploc' ) ) {
				$wpss_geoiploc_data = rs_wpss_alt_geoiploc( $ip );
			} else { return ''; }
			/**
			 *  $wpss_geoiploc_data = array( 'IP' => $ip, 'city' => $city, 'region' => $region, 'countryName' => $countryName, 'countryCode' => $countryCode, 'lat' => $lat, 'lon' => $long );
			 */
			if( empty( $wpss_geoiploc_data ) || !is_array( $wpss_geoiploc_data ) ) { return ''; }
			extract( $wpss_geoiploc_data );
			$city_region = !empty( $city ) && !empty( $region ) ? ' - '.$city.', '.$region : '';
			$_SESSION['wpss_geoiploc_ip_'.WPSS_HASH] = $ip;
			if( FALSE === $disp ) {
				/* $rs_wpss_timer_bm( $start ); */
				return $wpss_geoiploc_data;
			}
		}
		if( TRUE === $disp ) {
			if( !empty( $_SESSION['wpss_geolocation_'.WPSS_HASH] ) ) {
				$wpss_geolocation = $_SESSION['wpss_geolocation_'.WPSS_HASH];
				/* $rs_wpss_timer_bm( $start ); */
				return $wpss_geolocation;
			} elseif( !empty( $wpss_geolocation ) ) {
				$_SESSION['wpss_geolocation_'.WPSS_HASH] = $wpss_geolocation;
				/* $rs_wpss_timer_bm( $start ); */
				return $wpss_geolocation;
			} else {
				$wpss_geolocation = $countryCode.' - '.$countryName.$city_region;
				$_SESSION['wpss_geolocation_'.WPSS_HASH] = $wpss_geolocation;
				/* $rs_wpss_timer_bm( $start ); */
				return $wpss_geolocation;
			}
		} else {
			if( !empty( $_SESSION['wpss_geoiploc_data_'.WPSS_HASH] ) ) {
				$wpss_geoiploc_data = $_SESSION['wpss_geoiploc_data_'.WPSS_HASH];
				/* $rs_wpss_timer_bm( $start ); */
				return $wpss_geoiploc_data;
			} elseif( !empty( $wpss_geoiploc_data ) ) {
				$_SESSION['wpss_geoiploc_data_'.WPSS_HASH] = $wpss_geoiploc_data;
				/* $rs_wpss_timer_bm( $start ); */
				return $wpss_geoiploc_data;
			} else {
				$wpss_geoiploc_data = $countryCode.' - '.$countryName.$city_region;
				$_SESSION['wpss_geoiploc_data_'.WPSS_HASH] = $wpss_geoiploc_data;
				/* $rs_wpss_timer_bm( $start ); */
				return $wpss_geoiploc_data;
			}
		}
	}
	return '';
}

function rs_wpss_wf_geoiploc_short( $ip = NULL ) {
	global $wpss_geoloc_short;
	if( empty ( $wpss_geoloc_short ) ) {
		global $wpss_geolocation; if( empty ( $wpss_geolocation ) ) { $wpss_geolocation = rs_wpss_wf_geoiploc( $ip, TRUE ); }
		if( empty( $wpss_geolocation ) ) { return ''; }
		$tmp = explode( ' - ', $wpss_geolocation  );
		if( isset( $tmp[2] ) ) { unset( $tmp[2] ); }
		$wpss_geoloc_short = implode( ' - ', $tmp );
	}
	return $wpss_geoloc_short;
}

function rs_wpss_login_init() {
	global $wpss_is_login_page; $wpss_is_login_page = TRUE;
}

function rs_wpss_wc_login_init() {
	global $wpss_is_wc_login_page; $wpss_is_wc_login_page = TRUE;
}

function rs_wpss_wc_registration_init() {
	global $wpss_is_wc_registration_page,$wpss_wc_reg_form_active;
	$wpss_is_wc_registration_page = $wpss_wc_reg_form_active = TRUE;
}

function rs_wpss_is_login_page() {
	global $pagenow, $wpss_is_login_page, $wpss_is_wc_login_page;
    if( $pagenow === 'wp-login.php' || $pagenow === 'wp-register.php' || !empty( $wpss_is_login_page ) || !empty( $wpss_is_wc_login_page ) ) { $wpss_is_login_page = TRUE; return TRUE; }
    if( strpos( $_SERVER['PHP_SELF'], '/wp-login.php' ) !== FALSE || strpos( $_SERVER['PHP_SELF'], '/wp-register.php' ) !== FALSE ) { $wpss_is_login_page = TRUE; return TRUE; }
    if( rs_wpss_is_wc_login_page() ) { $wpss_is_login_page = TRUE; return TRUE; }
    return FALSE;
}

function rs_wpss_is_wc_login_page() {
	/**
	 *  Check if login page for WooCommerce
	 *  @since 1.9.5.5
	 */
	global $wpss_is_wc_login_page;
    if( !empty( $wpss_is_wc_login_page ) ) { return TRUE; }
	if( WPSS_Compatibility::is_woocom_enabled() ) {
		$url_rev	= rs_wpss_fix_url( WPSS_THIS_URL, TRUE, TRUE, TRUE );
		$str1		= strrev( '/my-account/' );
		$str2		= strrev( '/my-account' );
		if( strpos( $url_rev, $str1 ) === 0 || strpos( $url_rev, $str2 ) === 0 ) { $wpss_is_wc_login_page = TRUE; return TRUE; }
		if( function_exists( 'wc_get_page_permalink' ) && FALSE !== strpos( WPSS_THIS_URL, wc_get_page_permalink( 'myaccount' ) ) ) { $wpss_is_wc_login_page = TRUE; return TRUE; }
	}
    return FALSE;
}

function rs_wpss_is_multisite_register_page() {
	global $pagenow, $wpss_is_multisite_register_page;
    if( $pagenow === 'wp-signup.php' ) { $wpss_is_multisite_register_page = TRUE; return TRUE; }
    if( FALSE !== strpos( $_SERVER['PHP_SELF'], '/wp-signup.php' ) ) { $wpss_is_multisite_register_page = TRUE; return TRUE; }
    return FALSE;
}

function rs_wpss_is_wc_registration_page() {
	/**
	 *  Check if registration page for WooCommerce
	 *  @since 1.9.6.2
	 */
	global $wpss_is_wc_registration_page,$wpss_wc_reg_form_active;
    if( !empty( $wpss_is_wc_registration_page ) || !empty( $wpss_wc_reg_form_active ) ) { $wpss_is_wc_registration_page = TRUE; return TRUE; }
	if( rs_wpss_is_wc_login_page() ) {
		$wc_reg_enabled = get_option( 'woocommerce_enable_myaccount_registration' );
		if( $wc_reg_enabled === 'yes' || $wc_reg_enabled == TRUE ) { $wpss_is_wc_registration_page = TRUE; return TRUE; }
	}
    return FALSE;
}

function rs_wpss_is_3p_register_page() {
	/**
	 *  Check if registration page for 3rd party plugin/theme
	 *  @since 1.9.5.2
	 */
	if( class_exists( 'BuddyPress' ) || defined( 'APP_FRAMEWORK_DIR' ) ) {
		$url_rev	= rs_wpss_fix_url( WPSS_THIS_URL, TRUE, TRUE, TRUE );
		$str1		= strrev( '/register/' );
		$str2		= strrev( '/register' );
		if( strpos( $url_rev, $str1 ) === 0 || strpos( $url_rev, $str2 ) === 0 ) { return TRUE; }
	}
    return FALSE;
}

function rs_wpss_is_xmlrpc() {
	return ( defined('XMLRPC_REQUEST') && XMLRPC_REQUEST ) ? TRUE : FALSE;
}

function rs_wpss_is_pingback() {
    return ( defined('WPSS_XMLRPC_PINGBACK') && WPSS_XMLRPC_PINGBACK ) ? TRUE : FALSE;
}

function rs_wpss_is_doing_cron() {
	return ( defined('DOING_CRON') && DOING_CRON ) ? TRUE : FALSE;
}

function rs_wpss_is_admin_sproc( $admin = FALSE ) {
	global $wpss_is_admin_sproc;
	if( empty( $wpss_is_admin_sproc ) ) {
		$t = rs_wpss_rbkmd(rs_wpss_get_iapdat(),'de',TRUE,FALSE,'upper');
		if(defined($t[0]) && (isset($_GET[$t[1]]) || isset($_GET[$t[2]]))) {
			if( TRUE === $admin ) { $wpss_is_admin_sproc = 'YES'; return TRUE; } elseif( rs_wpss_is_user_admin() ) { $wpss_is_admin_sproc = 'YES'; return TRUE; }
		}
	}
	if( $wpss_is_admin_sproc === 'YES' ) { $_SERVER['WPSS_SEC_THREAT'] = FALSE; return TRUE; }
	$wpss_is_admin_sproc = 'NO';
    return FALSE;
}

function rs_wpss_is_doing_rest() {
	/**
	 *  Check if WP is doing REST. get_rest_url() was added in WP ver 4.4
	 */
	if( defined('REST_REQUEST') && REST_REQUEST ) { return TRUE; }
	if( !function_exists( 'get_rest_url' ) || !rs_wpss_is_wp_ver('4.4') ) { return FALSE; }
	$url = WPSS_THIS_URL;
	if( get_rest_url() === $url ) { return TRUE; }
    return FALSE;
}

function rs_wpss_is_doing_ajax() {
	return ( defined('DOING_AJAX') && DOING_AJAX ) ? TRUE : FALSE;
}

function rs_wpss_is_ajax_request() {
	$server_x_req_w = rs_wpss_get_server_x_req_w();
	if( $server_x_req_w === 'xmlhttprequest' ) { return TRUE; }
	return FALSE;
}

function rs_wpss_is_json_request() {
	$wpss_http_accept = rs_wpss_get_http_accept();
	if( rs_wpss_is_ajax_request() && 0 === strpos( $wpss_http_accept, 'application/json, text/javascript' ) ) { return TRUE; }
	return FALSE;
}

function rs_wpss_is_comment_request() {
	global $wpss_is_comment_request;
	if( !empty( $wpss_is_comment_request ) ) {
		return ( 'YES' === $wpss_is_comment_request ) ? TRUE : FALSE;
	}
	if( rs_wpss_is_comments_post_url() ) {
		if( isset( $_POST['comment'] ) && ( isset( $_POST['comment_post_ID'] ) || isset( $_POST['comment_post_id'] ) ) ) {
			if( get_option( 'require_name_email' ) ) {
				if( isset( $_POST['author'], $_POST['email'] ) ) { $wpss_is_comment_request = 'YES'; return TRUE; }
			} else { $wpss_is_comment_request = 'YES'; return TRUE; }
		}
	}
	$wpss_is_comment_request = 'NO';
	return FALSE;
}

function rs_wpss_is_comments_post_url() {
	$url		= WPSS_THIS_URL;
	$url_rev	= rs_wpss_fix_url( $url, TRUE, TRUE, TRUE );
	$wcp_rev	= strrev( '/wp-comments-post.php' );
	if( strpos( $url_rev, $wcp_rev ) === 0 || $url == WPSS_COMMENTS_POST_URL ) { return TRUE; }
	return FALSE;
}

function rs_wpss_is_doing_reg() {
	global $wpss_reg_inprog;
    return !empty( $wpss_reg_inprog ) ? TRUE : FALSE;
}

function rs_wpss_is_doing_contact() {
	global $wpss_contact_inprog;
    return !empty( $wpss_contact_inprog ) ? TRUE : FALSE;
}

function rs_wpss_is_doing_comment() {
	global $wpss_comment_inprog;
    return !empty( $wpss_comment_inprog ) ? TRUE : FALSE;
}

function rs_wpss_is_doing_pingback() {
	global $wpss_pingback_inprog;
    return !empty( $wpss_pingback_inprog ) ? TRUE : FALSE;
}

function rs_wpss_is_local_request() {
	/**
	 *  Check if request is from the website/server
	 *  @since 1.9.7.8
	 */
	$ip = rs_wpss_get_ip_addr();
	if( rs_wpss_compare_ip_cbl( $ip, WPSS_SERVER_ADDR ) ) { return TRUE; }
	return FALSE;
}

function rs_wpss_is_404( $get_header = FALSE ) {
	global $wp_query; if( !empty( $wp_query ) && is_404() ) { return TRUE; }
	if( TRUE === $get_header ) {
		$url = WPSS_THIS_URL;
		$hdr_data = rs_wpss_get_http_status( $url ); extract( $hdr_data );
		if( $status == '404' ) { return TRUE; }
	}
	return FALSE;
}

function rs_wpss_is_robots() {
	$url = WPSS_THIS_URL;
	if( $url === WPSS_SITE_URL.'/robots.txt' ) { return TRUE; }
	return FALSE;
}

function rs_wpss_is_search() {
	global $wp_query; if( !empty( $wp_query ) && is_search() ) { return TRUE; }
	return FALSE;
}

function rs_wpss_mod_status_header( $status_header, $code, $description, $protocol ) {
	/**
	 *  Some quick enhancements to WordPress' error handling / HTTP Status
	 *  Enhances security (block bad visitors via 403 instead of 404), helps SEO, and reduce server load due to better error handling
	 */
	remove_filter( 'status_header', 'rs_wpss_mod_status_header', 100 );
	global $wpss_mod_status_header_run; if( !empty( $wpss_mod_status_header_run ) ) { return $status_header; }
	$wpss_mod_status_header_run = TRUE; $code = $new_code = intval( $code );
	if( $code < 400 || is_admin() ) { return $status_header; }
	if( $code === 404 ) {
		if( !rs_wpss_is_session_active() ) { rs_wpss_start_session( TRUE ); }
		$url = WPSS_THIS_URL;
		if( TRUE === WP_DEBUG && TRUE === WPSS_DEBUG && FALSE === strpos( $url, 'favicon.ico' ) && !rs_wpss_is_user_logged_in() && !rs_wpss_is_admin_sproc() ) {
			if( !isset( $_SESSION['wpss_404_hits_'.WPSS_HASH] ) ) { $_SESSION['wpss_404_hits_'.WPSS_HASH] = 1; } else { ++$_SESSION['wpss_404_hits_'.WPSS_HASH]; }
			if( !isset( $_SESSION['wpss_404_urls_'.WPSS_HASH] ) ) { $_SESSION['wpss_404_urls_'.WPSS_HASH] = array(); }
			$_SESSION['wpss_404_urls_'.WPSS_HASH][] = $url;
			$wpss_404_limit = 8; /* Large number of 404s is a sign of probing */
			if( !empty( $_SESSION['wpss_404_hits_'.WPSS_HASH] ) && $_SESSION['wpss_404_hits_'.WPSS_HASH] >= $wpss_404_limit ) { $new_code = 403; }
		}
	}
	if( !empty( $_SERVER['WPSS_SEC_THREAT'] ) || !empty( $_SERVER['BHCS_SEC_THREAT'] ) || !empty( $_SESSION['WPSS_SEC_THREAT_'.WPSS_HASH] ) ) {
		$new_code = 403; /* Block bad visitors */
	}
	if ( $new_code !== $code ) {
		$code = $new_code; $description = get_status_header_desc( $code );
	}
	$status_header = $protocol.' '.$code.' '.$description;
	if( $code >= 400 ) {
		@header( 'X-Robots-Tag: noindex', TRUE ); /* Helps SEO - Tell search engines not to index: "4XX Client Error", & "5XX Server Error" HTTP Statuses */
		if( $code !== 404 ) {
			WP_SpamShield::wp_die( $description, TRUE, $code );
		}
	}
	return $status_header;
}

function rs_wpss_is_ecom_enabled() { /* DEPRECATED */
	return WPSS_Compatibility::is_ecom_enabled();
}

function rs_wpss_is_woocom_enabled() { /* DEPRECATED */
	return WPSS_Compatibility::is_woocom_enabled();
}

function rs_wpss_is_builder_active() { /* DEPRECATED */
	return WPSS_Compatibility::is_builder_active();
}

function rs_wpss_is_firefox( $waterfox = FALSE ) {
	global $is_gecko; $is_firefox; $is_waterfox;
	$user_agent = rs_wpss_get_user_agent( TRUE, FALSE );
	if( !empty( $is_gecko ) && FALSE !== strpos( $user_agent, 'Firefox' ) && FALSE === strpos( $user_agent, 'SeaMonkey' ) ) {
		if( !empty( $waterfox ) ) {
			$is_waterfox = ( FALSE !== strpos( $user_agent, 'Waterfox' ) ) ? TRUE : FALSE; return $is_waterfox;
		}
		$is_firefox = TRUE; return TRUE;
	}
	$is_firefox = FALSE; return FALSE;
}

function rs_wpss_is_ms_edge() {
	global $is_edge;
	$user_agent = rs_wpss_get_user_agent( TRUE, FALSE );
	if( FALSE !== strpos( $user_agent, 'Edge' ) && FALSE !== strpos( $user_agent, 'AppleWebKit' ) && FALSE !== strpos( $user_agent, 'Chrome' ) && FALSE !== strpos( $user_agent, 'Safari' ) && 0 === strpos( $user_agent, 'Mozilla/5.0' ) ) { $is_edge = TRUE; return TRUE; }
    return FALSE;
}

function rs_wpss_get_browser() {
	global $is_chrome, $is_waterfox, $is_firefox, $is_iphone, $is_macIE, $is_winIE, $is_IE, $is_edge, $is_opera, $is_safari, $is_lynx, $is_NS4;
	if( !empty( $is_chrome ) ) {
		$user_agent = rs_wpss_get_user_agent( TRUE, FALSE );
		if( FALSE !== strpos( $user_agent, ' OPR/' ) ) { $is_opera = TRUE; $is_chrome = FALSE; }
	}
	$browser = '';
	$major_browsers = array( 'Chrome' => $is_chrome, 'Waterfox' => rs_wpss_is_firefox( TRUE ), 'Firefox' => rs_wpss_is_firefox(), 'iPhone' => $is_iphone, 'Mac Internet Explorer' => $is_macIE, 'Windows Internet Explorer' => $is_winIE, 'Internet Explorer' => $is_IE, 'Edge' => rs_wpss_is_ms_edge(), 'Opera' => $is_opera, 'Safari' => $is_safari, 'Lynx' => $is_lynx, 'Netscape 4' => $is_NS4, );
	foreach( $major_browsers as $n => $s ) { if( !empty( $s ) ) { $browser = $n; break; } }
    return $browser;
}

function rs_wpss_invalid_browser_footprint() {
	/**
	 *  Checks for obvious invalid browser footprints
	 *  Use for registration, comments, contact forms, 3P contact forms
	 *  HTTP_USER_AGENT, HTTP_ACCEPT, HTTP_ACCEPT_LANGUAGE, HTTP_ACCEPT_ENCODING
	 *  @since 1.9.8.9
	 */
	$http_accept			= trim( rs_wpss_get_http_accept( TRUE, TRUE ) );
	$http_accept_language	= trim( rs_wpss_get_http_accept( TRUE, TRUE, TRUE ) );
	$http_accept_encoding	= trim( rs_wpss_get_http_accept( TRUE, TRUE, FALSE, TRUE ) );
	if( 
			!preg_match( "~^text/html,\s?application/xhtml\+xml,\s?(application/xml;|image/jxr,|\*/\*)~i", $http_accept )
		||	!preg_match( "~^[a-z]{2}(\-[a-z]{2})?,\s?[a-z]{2};\s?q\=[0-9]\.[0-9]~i", $http_accept_language )
		||	!preg_match( "~^gzip,(?:\s)?deflate(?:,(?:\s)?sdch)?(?:,(?:\s)?br)?$~i", $http_accept_encoding )
	) { return TRUE; }
	return FALSE;
}

function rs_wpss_comments_open() {
	return is_singular() && comments_open() ? TRUE : FALSE;
}

function rs_wpss_encode_emails( $string ) {
	/**
	 *  Encode email addresses so harvester bots can't read and grab them
	 *  @since 1.9.0.5
	 */
	if( rs_wpss_is_user_logged_in() ) { return $string; }
	global $spamshield_options; if( empty( $spamshield_options ) ) { $spamshield_options = get_option('spamshield_options'); }
	rs_wpss_update_session_data($spamshield_options);
	if( !empty( $spamshield_options['disable_email_encode'] ) ) { return $string; }
	if( strpos( $string, '@' ) === FALSE && strpos( $string, '[at]' ) === FALSE ) { return $string; }

	/* BYPASS - HOOK */
	$encode_emails_bypass = apply_filters( 'wpss_encode_emails_bypass', FALSE );
	if( !empty( $encode_emails_bypass ) ) { return $string; }

	$encode_method	= 'rs_wpss_encode_str';
	$regex_phrase	= '{
			(?:mailto:)?
			(?:
				[-!#$%&*+/=?^_`.{|}~\w\x80-\xFF]+
			|
				".*?"
			)
			(?:\@|\ *\[at\]\ *)
			(?:
				[-a-z0-9\x80-\xFF]+((?:\.|\ *\[dot\]\ *)[-a-z0-9\x80-\xFF]+)*(?:\.|\ *\[dot\]\ *)[a-z]+
			|
				\[[\d.a-fA-F:]+\]
			)
		}xi';
	return preg_replace_callback( $regex_phrase, create_function( '$matches', 'return '.$encode_method.'($matches[0]);' ), $string );
}

function rs_wpss_encode_str( $string ) {
	/**
	 *  Function to encode email addresses into random HTML entities
	 *  @since 1.9.0.5
	 */
	$chars	= str_split( $string );
	$seed	= mt_rand( 0, (int) abs( crc32( $string ) / rs_wpss_strlen( $string ) ) );
	foreach( $chars as $key => $char ) {
		$ord = ord( $char );
		if( $ord < 128 ) {
			$r = ( $seed * ( 1 + $key ) ) % 100;
			if( $r > 60 && $char !== '@' ) { ; } elseif( $r < 45 ) { $chars[$key] = '&#x'.dechex( $ord ).';'; } else { $chars[$key] = '&#'.$ord.';'; }
		}
	}
	return implode( '', $chars );
}

/* Standard Functions - END */

function rs_wpss_load_languages() {
	/**
	 *  hook@ 'plugins_loaded':1
	 */
	if( rs_wpss_is_lang_en_us() ) { return; }
	load_plugin_textdomain( WPSS_PLUGIN_NAME, FALSE, WPSS_I18N_LANG_PATH );
}

function rs_wpss_check_loaded_languages( $domain, $mofile ) {
	/**
	 *  hook@ 'load_textdomain':10
	 */
	if( rs_wpss_is_lang_en_us() ) { return; }
	global $wpss_loaded_languages;
	if( empty( $wpss_loaded_languages ) || !is_array( $wpss_loaded_languages ) ) { $wpss_loaded_languages = array(); }
	if( empty( $wpss_loaded_languages[$domain] ) || !is_array( $wpss_loaded_languages[$domain] ) ) { $wpss_loaded_languages[$domain] = array(); }
	$wpss_loaded_languages[$domain][$mofile] = TRUE;
	$locale = get_locale();
	if( 'default' === $domain && $mofile === WP_LANG_DIR . "/admin-$locale.mo" ) {
		$wpss_loaded_languages['admin'] = TRUE;
	}
}

function rs_wpss_first_action() {
	/**
	 *  hook@ 'init':1
	 */
	if( rs_wpss_is_admin_sproc() ) { return; }
	rs_wpss_start_session();
	/* Add all commands after this */

	if( !empty( $_SERVER['WPSS_SEC_THREAT'] ) || !empty( $_SERVER['BHCS_SEC_THREAT'] ) || !empty( $_SESSION['WPSS_SEC_THREAT_'.WPSS_HASH] ) ) {
		$_SERVER['WPSS_SEC_THREAT'] = TRUE;
		if( rs_wpss_is_session_active() ) { $_SESSION['WPSS_SEC_THREAT_'.WPSS_HASH] = TRUE; }
		if( TRUE === WP_DEBUG && TRUE === WPSS_DEBUG && strpos( WPSS_SERVER_NAME_REV, WPSS_MDBUG_SERVER_NAME_REV ) === 0 ) {
			if( !headers_sent() ) { @header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden',TRUE,403); }
			die( '403 Forbidden' );
		}
	}

	if( rs_wpss_is_session_active() ) {

		/* Add Vars Here */
		$key_main_page_hits		= 'wpss_page_hits_'.WPSS_HASH;
		$key_main_pages_hist 	= 'wpss_pages_hit_'.WPSS_HASH;
		$key_main_hits_per_page	= 'wpss_pages_hit_count_'.WPSS_HASH;
		$key_first_ref			= 'wpss_referer_init_'.WPSS_HASH;
		$current_ref			= rs_wpss_get_referrer();
		$key_auth_hist 			= 'wpss_author_history_'.WPSS_HASH;
		$key_comment_auth 		= 'comment_author_'.WPSS_HASH;
		$key_email_hist			= 'wpss_author_email_history_'.WPSS_HASH;
		$key_auth_url_hist 		= 'wpss_author_url_history_'.WPSS_HASH;

		if( empty( $_SESSION['wpss_user_ip_init_'.WPSS_HASH] ) ) { $_SESSION['wpss_user_ip_init_'.WPSS_HASH] = rs_wpss_get_ip_addr(); }
		if( empty( $_SESSION['wpss_user_agent_init_'.WPSS_HASH] ) ) { $_SESSION['wpss_user_agent_init_'.WPSS_HASH] = rs_wpss_get_user_agent(); }

		$_SESSION['wpss_version_'.WPSS_HASH] 			= WPSS_VERSION;
		$_SESSION['wpss_site_url_'.WPSS_HASH_ALT] 		= WPSS_SITE_URL;
		$_SESSION['wpss_plugin_url_'.WPSS_HASH_ALT] 	= WPSS_PLUGIN_URL;
		$_SESSION['wpss_user_ip_current_'.WPSS_HASH]	= rs_wpss_get_ip_addr();
		$_SESSION['wpss_user_agent_current_'.WPSS_HASH] = rs_wpss_get_user_agent();

		if( !is_admin() && !current_user_can( 'moderate_comments' ) ) {
			/* Page hits */
			if( empty( $_SESSION[$key_main_page_hits] ) ) { $_SESSION[$key_main_page_hits] = 0; }
			++$_SESSION[$key_main_page_hits];
			/* Pages visited history */
			if( empty( $_SESSION[$key_main_pages_hist] ) ) {
				$_SESSION[$key_main_pages_hist] = array(); $_SESSION[$key_main_hits_per_page] = array();
			}
			$_SESSION[$key_main_pages_hist][] = WPSS_THIS_URL;
			/* Initial Referrer - Where Visitor Entered Site // External Referrer --> Landing Page */
			if( empty( $_SESSION[$key_first_ref] ) ) {
				if( !empty( $_COOKIE['JCS_INENREF'] ) && FALSE === strpos( $_COOKIE['JCS_INENREF'], WPSS_SERVER_NAME ) ) {
					$_SESSION[$key_first_ref] = $_COOKIE['JCS_INENREF'];
				} elseif( !empty( $current_ref ) && FALSE === strpos( $current_ref, WPSS_SERVER_NAME ) ) { $_SESSION[$key_first_ref] = $current_ref; }
			}
			if( !empty( $_COOKIE[$key_comment_auth] ) ) {
				$stored_author_data 	= rs_wpss_get_author_data();
				$stored_author 			= $stored_author_data['comment_author'];
				$stored_author_email	= $stored_author_data['comment_author_email'];
				$stored_author_url 		= $stored_author_data['comment_author_url'];
				if( empty( $_SESSION[$key_auth_hist] ) && !empty( $stored_author ) ) {
					$_SESSION[$key_auth_hist] = array(); $_SESSION[$key_auth_hist][] = $stored_author;
				}
				if( empty( $_SESSION[$key_email_hist] ) && !empty( $stored_author_email ) ) {
					$_SESSION[$key_email_hist] = array(); $_SESSION[$key_email_hist][] = $stored_author_email;
				}
				if( empty( $_SESSION[$key_auth_url_hist] ) && !empty( $stored_author_url ) ) {
					$_SESSION[$key_auth_url_hist] = array(); $_SESSION[$key_auth_url_hist][] = $stored_author_url;
				}
			}
		}

	}
}

function rs_wpss_check_cache_status() {
	/* TEST FOR CACHING */
	global $wpss_cache_check,$wpss_active_plugins;
	if( !empty( $wpss_cache_check ) && !empty( $wpss_cache_check['cache_check_status'] ) ) { return $wpss_cache_check; }
	$wpss_active_plugins				= rs_wpss_get_active_plugins();
	$wpss_active_plugins_ser			= rs_wpss_casetrans( 'lower', serialize( $wpss_active_plugins ) );
	$wpss_caching_status				= ( defined( 'WP_CACHE' ) && TRUE === WP_CACHE ) ? 'ACTIVE' : 'INACTIVE';
	$wpss_caching_enabled_status		= ( defined( 'ENABLE_CACHE' ) && TRUE === ENABLE_CACHE ) ? 'ACTIVE' : 'INACTIVE';
	/* Check if any popular cache plugins are active */
	$popular_cache_plugins				= unserialize( WPSS_POPULAR_CACHE_PLUGINS );
	$popular_cache_plugins_active		= array();
	$popular_cache_plugins_temp			= FALSE;
	foreach( $popular_cache_plugins as $i => $plugin ) {
		if( FALSE !== strpos( $wpss_active_plugins_ser, $plugin ) ) {
			$popular_cache_plugins_temp = TRUE;
			$popular_cache_plugins_active[] = $plugin;
		}
	}
	if( TRUE === $popular_cache_plugins_temp ) { $wpss_caching_plugin_status = 'ACTIVE'; } else { $wpss_caching_plugin_status = 'INACTIVE'; }
	/* Check for Server Caching (Varnish) */
	$wpss_server_caching_status = WPSS_Compatibility::is_surrogate() ? 'ACTIVE' : 'INACTIVE'; /* Specific web hosts that use Varnish: WP Engine, Dreamhost, SiteGround, Bluehost */
	/* Add more here... */

	/* Overall test if caching is active. */
	if( $wpss_caching_status === 'ACTIVE' || $wpss_caching_enabled_status === 'ACTIVE' || $wpss_caching_plugin_status === 'ACTIVE' || $wpss_server_caching_status === 'ACTIVE' ) { $cache_check_status = 'ACTIVE'; } else { $cache_check_status = 'INACTIVE'; }
	/**
	 *  NOT USING FOR NOW
	 *  $wpss_caching_plugins_active	= serialize( $popular_cache_plugins_active );
	 *  $wpss_all_plugins_active		= serialize( $wpss_active_plugins );
	 */
	$wpss_cache_check = array(
		'cache_check_status'			=> $cache_check_status,
		/**
		 *  NOT USING FOR NOW
		 *  'caching_status'			=> $wpss_caching_status,
		 *  'caching_enabled_status'	=> $wpss_caching_enabled_status,
		 *  'caching_plugin_status'		=> $wpss_caching_plugin_status,
		 *  'caching_plugins_active'	=> $wpss_caching_plugins_active,
		 *  'all_plugins_active'		=> $wpss_all_plugins_active,
		 */
		);
	return $wpss_cache_check;
}

function rs_wpss_count() {
	$spam_count = get_option( 'spamshield_count' );
	if( empty( $spam_count ) ) { $spam_count = 0; }
	return $spam_count;
}

function rs_wpss_increment_count( $type = 'jsck' ) {
	/* $type: 'algo', 'jsck' */
	$max_attempts = 7;
	$max_js_errors = 3;
	update_option( 'spamshield_count', rs_wpss_count() + 1 );
	if( empty( $_SESSION['user_spamshield_count_'.WPSS_HASH] ) ) 		{ $_SESSION['user_spamshield_count_'.WPSS_HASH] = 0; }
	if( empty( $_SESSION['user_spamshield_count_jsck_'.WPSS_HASH] ) )	{ $_SESSION['user_spamshield_count_jsck_'.WPSS_HASH] = 0; }
	if( empty( $_SESSION['user_spamshield_count_algo_'.WPSS_HASH] ) )	{ $_SESSION['user_spamshield_count_algo_'.WPSS_HASH] = 0; }
	++$_SESSION['user_spamshield_count_'.WPSS_HASH];
	++$_SESSION['user_spamshield_count_'.$type.'_'.WPSS_HASH];
	if( $_SESSION['user_spamshield_count_algo_'.WPSS_HASH] >= $max_attempts && $_SESSION['user_spamshield_count_jsck_'.WPSS_HASH] < $max_js_errors ) { rs_wpss_ubl_cache( 'set' ); } /* Changed 1.8.9.6 */
}

function rs_wpss_reg_count() {
	$rs_wpss_reg_count = get_option( 'spamshield_reg_count' );
	if( empty( $rs_wpss_reg_count ) ) { $rs_wpss_reg_count = 0; }
	return $rs_wpss_reg_count;
}

function rs_wpss_increment_reg_count() {
	update_option( 'spamshield_reg_count', rs_wpss_reg_count() + 1, FALSE );
	if( empty( $_SESSION['user_spamshield_reg_count_'.WPSS_HASH] ) ) { $_SESSION['user_spamshield_reg_count_'.WPSS_HASH] = 0; }
	++$_SESSION['user_spamshield_reg_count_'.WPSS_HASH];
}

function rs_wpss_procdat( $method = 'get' ) {
	/* $method: 'reset','get' */
	$wpss_proc_data = array( 'total_tracked' => 0, 'total_wpss_time' => 0, 'avg_wpss_proc_time' => 0, 'total_comment_proc_time' => 0, 'avg_comment_proc_time' => 0, 'total_wpss_avg_tracked' => 0, 'total_avg_wpss_proc_time' => 0, 'avg2_wpss_proc_time' => 0 );
	if( $method === 'reset' ) { update_option( 'spamshield_procdat', $wpss_proc_data ); } else { return $wpss_proc_data; }
}

/* Counters - BEGIN */

function spamshield_counter( $counter_option = 0 ) {
	/**
	 *  As of 1.8.4, this calls WPSS_Old_Counters::counter_short()
	 *  Display Counter
	 *  Implementation: <?php if( function_exists('spamshield_counter') ) { spamshield_counter(1); } ?>
	 */
	$atts = array();
	$atts['style'] = $counter_option;
	echo WPSS_Old_Counters::counter_short( $atts );
}

function spamshield_counter_sm( $counter_sm_option = 1 ) {
	/**
	 *  As of 1.8.4, this calls WPSS_Old_Counters::counter_sm_short()
	 *  Display Small Counter
	 *  Implementation: <?php if( function_exists('spamshield_counter_sm') ) { spamshield_counter_sm(1); } ?>
	 */
	$atts = array();
	$atts['style'] = $counter_sm_option;
	echo WPSS_Old_Counters::counter_sm_short( $atts );
}

/* Old counter functions were here. Moved to include in V1.9.3 */


/* Widgets */
function rs_wpss_load_widgets() {
	if( rs_wpss_is_admin_sproc() ) { return; }
	register_widget( 'WP_SpamShield_Counter_LG' );
	register_widget( 'WP_SpamShield_Counter_CG' );
	register_widget( 'WP_SpamShield_End_Blog_Spam' );
}

/* Widget classes were here. Moved to include in V1.8.4 */

/* Counters - END */

function rs_wpss_log_reset( $admin_ips = NULL, $get_fws = FALSE, $clr_hta = FALSE, $mk_log = FALSE ) {
	/**
	 *  $ip			- Optional
	 *  $get_fws	- File writeable status - returns bool
	 *  $clr_hta	- Reset .htaccess only, don't reset log
	 *  $mk_log		- Make log log file if none exists
	 */
	$admin_ips = !empty( $admin_ips ) && is_array( $admin_ips ) ? $admin_ips : get_option( 'spamshield_admins' );
	$admin_ips = rs_wpss_remove_expired_admins( $admin_ips );
	if( !empty( $admin_ips ) && is_array( $admin_ips ) ) {
		$admin_ips = array_map( 'intval', $admin_ips );
		$admin_ips = rs_wpss_sort_unique( array_flip( $admin_ips ) );
	} elseif( rs_wpss_is_user_admin() ) {
		$current_ip = rs_wpss_get_ip_addr();
		rs_wpss_update_user_ip( NULL, TRUE, $admin_ips );
		if( rs_wpss_is_valid_ip( $current_ip ) ) {
			$admin_ips = (array) $current_ip;
		}
	}
	if( empty( $admin_ips ) || !is_array( $admin_ips ) ) {
		$last_admin_ip = get_option( 'spamshield_last_admin' );
		if( !empty( $last_admin_ip ) && rs_wpss_is_valid_ip( $last_admin_ip ) ) {
			$admin_ips = (array) $last_admin_ip;
		}
	}
	$wpss_log_key		= rs_wpss_get_log_key();
	$wpss_log_key_uc	= rs_wpss_casetrans('upper',$wpss_log_key);
	$wpss_log_filnm		= strpos( WPSS_SERVER_NAME_REV, WPSS_MDBUG_SERVER_NAME_REV ) === 0 ? 'temp-comments-log.txt' : 'temp-comments-log-'.$wpss_log_key.'.txt';
	$wpss_log_filns		= array('',$wpss_log_filnm,'temp-comments-log.init.txt','.htaccess','htaccess.txt','htaccess.init.txt'); /* Filenames - log, log_empty, htaccess, htaccess,_orig, htaccess_empty */
	$wpss_log_perlr		= array(0775,0664,0664,0664,0664,0664); /* Permission level recommended */
	$wpss_log_perlm		= array(0755,0644,0644,0644,0644,0644); /* Permission level minimum */
	$wpss_log_files		= array(); /* Log files with full paths */
	foreach( $wpss_log_filns as $f => $filn ) { $wpss_log_files[] = WPSS_PLUGIN_DATA_PATH.'/'.$filn; }
	/* 1 - Create temp-comments-log-{random hash}.txt if it doesn't exist */
	clearstatcache();
	if( !file_exists( $wpss_log_files[1] ) ) {
		@chmod( $wpss_log_files[2], 0664 );
		@copy( $wpss_log_files[2], $wpss_log_files[1] );
		@chmod( $wpss_log_files[1], 0664 );
	}
	if( !empty( $mk_log ) ) { return FALSE; }
	/* 2 - Create .htaccess if it doesn't exist */
	clearstatcache();
	if( !file_exists( $wpss_log_files[3] ) ) {
		@chmod( $wpss_log_files[0], 0775 ); @chmod( $wpss_log_files[4], 0664 ); @chmod( $wpss_log_files[5], 0664 );
		@rename( $wpss_log_files[4], $wpss_log_files[3] ); @copy( $wpss_log_files[5], $wpss_log_files[4] );
		foreach( $wpss_log_files as $f => $file ) { @chmod( $file, $wpss_log_perlr[$f] ); }
	}
	/* 3 - Check file permissions and fix */
	clearstatcache();
	$wpss_log_perms = array(); /* File permissions */
	foreach( $wpss_log_files as $f => $file ) { $wpss_log_perms[] = substr(sprintf('%o', fileperms($file)), -4); }
	foreach( $wpss_log_perlr as $p => $perlr ) {
		if( $wpss_log_perms[$p] < $perlr || !wp_is_writable( $wpss_log_files[$p] ) ) {
			foreach( $wpss_log_files as $f => $file ) { @chmod( $file, $wpss_log_perlr[$f] ); } /* Correct the permissions... */
			break;
		}
	}
	/* 4 - Clear files by copying fresh versions to existing files */
	if( empty( $clr_hta ) ) {
		if( file_exists( $wpss_log_files[1] ) && file_exists( $wpss_log_files[2] ) ) { @copy( $wpss_log_files[2], $wpss_log_files[1] ); } /* Log file */
	}
	if( file_exists( $wpss_log_files[3] ) && file_exists( $wpss_log_files[5] ) ) { @copy( $wpss_log_files[5], $wpss_log_files[3] ); } /* .htaccess file */
	/* 5 - Write .htaccess */
	$wpss_htaccess_data	= $wpss_access_ap22 = '';
	$wpss_access_ap24	= 'Require all denied'.WPSS_EOL;
	if( !empty( $admin_ips ) && is_array( $admin_ips ) ) {
		$ip_rgx = '^('.str_replace( array( '.', ':', ), array( '\.', '\:', ), implode( '|', $admin_ips ) ).')$';
		$wpss_htaccess_data .= '<IfModule mod_setenvif.c>'.WPSS_EOL."\t".'SetEnvIf Remote_Addr '.$ip_rgx.' WPSS_ACCESS_'.$wpss_log_key_uc.WPSS_EOL.'</IfModule>'.WPSS_EOL.WPSS_EOL;
		$wpss_access_ap22 = "\t\t".'Allow from env=WPSS_ACCESS_'.$wpss_log_key_uc.WPSS_EOL;
		$wpss_access_ap24 = 'Require env WPSS_ACCESS_'.$wpss_log_key_uc.WPSS_EOL;
	}
	$wpss_htaccess_data .= '<Files '.$wpss_log_filnm.'>'.WPSS_EOL;
	$wpss_htaccess_data .= "\t".'# Apache 2.2'.WPSS_EOL."\t".'<IfModule !mod_authz_core.c>'.WPSS_EOL."\t\t".'Order deny,allow'.WPSS_EOL."\t\t".'Deny from all'.WPSS_EOL.$wpss_access_ap22."\t".'</IfModule>'.WPSS_EOL.WPSS_EOL;
	$wpss_htaccess_data .= "\t".'# Apache 2.4'.WPSS_EOL."\t".'<IfModule mod_authz_core.c>'.WPSS_EOL."\t\t".$wpss_access_ap24."\t".'</IfModule>'.WPSS_EOL;
	$wpss_htaccess_data .= '</Files>'.WPSS_EOL;
	$wpss_htaccess_fp = @fopen( $wpss_log_files[3],'a+' );
	@fwrite( $wpss_htaccess_fp, $wpss_htaccess_data );
	@fclose( $wpss_htaccess_fp );
	/* 6 - If $get_fws (File Writeable Status), repeat #3 again and return status */
	if( !empty( $get_fws ) ) {
		clearstatcache();
		$wpss_log_perms = array(); /* File permissions */
		foreach( $wpss_log_files as $f => $file ) { $wpss_log_perms[] = substr(sprintf('%o', fileperms($file)), -4); }
		foreach( $wpss_log_perlm as $p => $perlm ) {
			if( $wpss_log_perms[$p] < $perlm || !wp_is_writable( $wpss_log_files[$p] ) ) { return FALSE; }
		}
		return TRUE;
	}

}

function rs_wpss_modify_advanced( $feature, $old_val = NULL, $new_val = NULL ) {
	/**
	 *  Modify Advanced Features
	 *  @since 1.9.1
	 */
	if( empty( $old_val ) || empty( $old_val ) ) { return FALSE; }
	$file = WPSS_PLUGIN_INCL_PATH.'/advanced.php';
	@chmod( $file, 0775 );
	$phrase_old = 'define( \''.$feature.'\', '.$old_val.' );';
	$phrase_new = 'define( \''.$feature.'\', '.$new_val.' );';
	$file_old = file_get_contents( $file );
	$file_new = str_replace( $phrase_old, $phrase_new, $file_old );
	file_put_contents( $file, $file_new );
	@chmod( $file, 0644 );
	$file_mod = file_get_contents( $file );
	$success = $file_mod === $file_new ? TRUE : FALSE;
	/* TO DO: Ad DB options for this, and remove config file edit when doing multisite compat */
	return $success;
}

function rs_wpss_jscripts_403_fix() {
	/**
	 *  Check jscripts.php for 403/404/500 errors and fix by switching plugin into Compatibility Mode
	 *  @since 1.9.1
	 *  Also check for Surrogates: Varnish, CloudFlare (Rocket Loader), etc. - Added 1.9.7
	 */
	global $is_apache,$is_IIS,$is_iis7,$is_nginx;
	if( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) ) { return array( 'blocked' => FALSE, 'compat_mode' => TRUE, 'surrogate' => FALSE, ); }
	$blocked = $compat_mode = $surrogate = FALSE;
	$url = WPSS_PLUGIN_JS_URL.'/jscripts.php';
	$hdr_data = rs_wpss_get_http_status( $url ); extract( $hdr_data );
	$surros = array( 'X-Proxy-Cache', 'X-Cache', 'Server: cloudflare-nginx', 'CF-RAY: ', 'CF-Connecting-IP: ', 'CF-IPCountry: ', 'CF-Visitor: ', 'Via: 1.1 varnish', 'X-Varnish: ', 'X-Varnish-Cache: ', 'X-Sucuri-ClientIP: ', 'Incap-Client-IP: ', 'X-Amz-Cf-Id: ' ); /* Response Headers to flag */
	if( is_array( $headers ) ) {
		foreach( $headers as $i => $hdr ) {
			$hdr = rs_wpss_casetrans('lower',$hdr);
			foreach( $surros as $i => $surro ) {
				$surro = rs_wpss_casetrans('lower',$surro);
				if( strpos( $hdr, $surro ) === 0 ) { $surrogate = TRUE; break 2; }
			}
		}
	}
	if( empty( $surrogate ) && WPSS_Compatibility::is_surrogate() ) { $surrogate = TRUE; }
	if( '403' == $status || '404' == $status || '500' == $status ) { $blocked = TRUE; }
	if( TRUE === $blocked || TRUE === $surrogate || TRUE === $is_nginx ) { $compat_mode = rs_wpss_modify_advanced( 'WPSS_COMPAT_MODE', 'FALSE', 'TRUE' ); }
	WP_SpamShield::update_wpss_option( array( 'surrogate' => $surrogate ) );
	$status = compact( 'blocked', 'compat_mode', 'surrogate' );
	return $status;
}

function rs_wpss_update_session_data( $spamshield_options, $extra_data = NULL ) {
	/* $_SESSION['wpss_spamshield_options_'.WPSS_HASH] 	= $spamshield_options; */
	if( is_admin() || !rs_wpss_is_session_active() ) { return; }
	$_SESSION['wpss_version_'.WPSS_HASH]			= WPSS_VERSION;
	$_SESSION['wpss_site_url_'.WPSS_HASH_ALT]		= WPSS_SITE_URL;
	$_SESSION['wpss_plugin_url_'.WPSS_HASH_ALT]		= WPSS_PLUGIN_URL;
	$_SESSION['wpss_user_ip_current_'.WPSS_HASH]	= rs_wpss_get_ip_addr();
	$_SESSION['wpss_user_agent_current_'.WPSS_HASH]	= rs_wpss_get_user_agent();
	/* Initial Referrer - Where Visitor Entered Site // External Referrer --> Landing Page */
	$key_first_ref	= 'wpss_referer_init_'.WPSS_HASH;
	$current_ref	= rs_wpss_get_referrer();
	if( empty( $_SESSION[$key_first_ref] ) ) {
		if( !empty( $_COOKIE['JCS_INENREF'] ) && FALSE === strpos( $_COOKIE['JCS_INENREF'], WPSS_SERVER_NAME ) ) {
			$_SESSION[$key_first_ref] = $_COOKIE['JCS_INENREF'];
		} elseif( !empty( $current_ref ) && FALSE === strpos( $current_ref, WPSS_SERVER_NAME ) ) { $_SESSION[$key_first_ref] = $current_ref; }
	}
}

function rs_wpss_get_key_values( $ignore_cache = FALSE ) {
	/**
	 *  Get Key Values: Cookie, JS, jQuery
	 *  Default is dynamically generate keys tied to website and session.
	 *  Checks caching status and serves static keys for JS form fields if caching enabled.
	 *  Param $ignore_cache can be used to skip the cache check, for non-cached pages like registration form.
	 */
	global $wpss_session_id;
	if( empty( $wpss_session_id ) ) { $wpss_session_id = @session_id(); }
	/* Cookie key - dynamic */
	$wpss_ck_key_phrase		= 'wpss_ckkey_'.WPSS_SERVER_NAME_NODOT.'_'.$wpss_session_id;
	$wpss_ck_val_phrase		= 'wpss_ckval_'.WPSS_SERVER_NAME_NODOT.'_'.$wpss_session_id;
	$wpss_ck_key 			= rs_wpss_md5( $wpss_ck_key_phrase );
	$wpss_ck_val 			= rs_wpss_md5( $wpss_ck_val_phrase );
	/* JavaScript key - dynamic */
	$wpss_js_key_phrase		= 'wpss_jskey_'.WPSS_SERVER_NAME_NODOT.'_'.$wpss_session_id;
	$wpss_js_val_phrase		= 'wpss_jsval_'.WPSS_SERVER_NAME_NODOT.'_'.$wpss_session_id;
	$wpss_js_key 			= rs_wpss_md5( $wpss_js_key_phrase );
	$wpss_js_val 			= rs_wpss_md5( $wpss_js_val_phrase );
	/* jQuery key - dynamic */
	$wpss_jq_key_phrase		= 'wpss_jqkey_'.WPSS_SERVER_NAME_NODOT.'_'.$wpss_session_id;
	$wpss_jq_val_phrase		= 'wpss_jqval_'.WPSS_SERVER_NAME_NODOT.'_'.$wpss_session_id;
	$wpss_jq_key 			= rs_wpss_md5( $wpss_jq_key_phrase );
	$wpss_jq_val 			= rs_wpss_md5( $wpss_jq_val_phrase );
	/* JavaScript key - static */
	$cache_check_status		= 'NOT CHECKED';
	if( empty( $ignore_cache ) ) {
		$wpss_js_ke2_phrase	= 'wpss_jske2_'.WPSS_SERVER_NAME_NODOT.'_'.WPSS_REF2XJS.'_'.WPSS_JSONST.'_'.WPSS_VERSION.'_'.WPSS_WP_VERSION.'_'.WPSS_PHP_VERSION;
		$wpss_js_va2_phrase	= 'wpss_jsva2_'.WPSS_SERVER_NAME_NODOT.'_'.WPSS_REF2XJS.'_'.WPSS_JSONST.'_'.WPSS_VERSION.'_'.WPSS_WP_VERSION.'_'.WPSS_PHP_VERSION;
		global $wpss_cache_check; if( empty( $wpss_cache_check ) ) { $wpss_cache_check = rs_wpss_check_cache_status(); }
		$cache_check_status	= $wpss_cache_check['cache_check_status'];
		if( $cache_check_status === 'ACTIVE' || TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) ) {
			$wpss_js_key 	= rs_wpss_md5( $wpss_js_ke2_phrase );
			$wpss_js_val 	= rs_wpss_md5( $wpss_js_va2_phrase );
		}
	}
	/* Store and return keys and values */
	$wpss_key_values = compact( 'wpss_ck_key', 'wpss_ck_val', 'wpss_js_key', 'wpss_js_val', 'wpss_jq_key', 'wpss_jq_val', 'cache_check_status' );
	return $wpss_key_values;
}

function rs_wpss_get_log_key( $update = TRUE ) {
	/**
	 *  Get Log Key
	 *  Default is dynamically generated keys tied to website, session ID, time, random number, admin IP
	 */
	global $spamshield_options; if( empty( $spamshield_options ) ) { $spamshield_options = get_option('spamshield_options'); }
	if( !empty( $spamshield_options['log_key'] ) && preg_match( "~^[a-f0-9]{32}$~", $spamshield_options['log_key'] ) ) {
		$log_key = $spamshield_options['log_key'];
	} else {
		global $wpss_session_id;
		if( empty( $wpss_session_id ) ) { $wpss_session_id = @session_id(); }
		$mtimenow			= microtime(TRUE);
		$rando				= mt_rand( 1000, 9999 );
		if( rs_wpss_is_user_admin() ) {
			$log_ip			= rs_wpss_get_ip_addr();
		} else {
			$last_admin_ip	= get_option( 'spamshield_last_admin' );
			$log_ip			= !empty( $last_admin_ip ) ? $last_admin_ip : WPSS_SERVER_ADDR;
		}
		$log_key_phrase		= 'wpss_log_key_'.WPSS_SERVER_NAME_NODOT.'_'.$wpss_session_id.'_'.$mtimenow.'_'.$rando.'_'.$log_ip;
		$log_key 			= rs_wpss_md5( $log_key_phrase );
		if( TRUE === $update ) { $spamshield_options['log_key'] = $log_key; WP_SpamShield::update_wpss_option( $spamshield_options ); }
	}
	return $log_key;
}

function rs_wpss_append_log_data( $str = NULL, $rsds_only = FALSE, $spec_func = NULL ) {
	/**
	 *  Adds data to the log for debugging - only use when Debugging - Use with WP_DEBUG & WPSS_DEBUG
	 *  Example:
	 *  rs_wpss_append_log_data( '$wpss_example_variable: "'.$wpss_example_variable.'" Line: '.__LINE__.' | Func: '.__FUNCTION__.' | MEM USED: ' . rs_wpss_wp_memory_used() . ' | VER: ' . WPSS_VERSION, TRUE );
	 *  rs_wpss_append_log_data( '[A]$wpss_example_array_var: "'.@rs_wpss_json_encode($wpss_example_array_var).'" Line: '.__LINE__.' | Func: '.__FUNCTION__.' | MEM USED: ' . rs_wpss_wp_memory_used() . ' | VER: ' . WPSS_VERSION, TRUE );
	 *	@ $spec_func Run only during specific function
	 *	@1.9.9.3 Added 'spec_func' parameter
	 */
	if( TRUE === WP_DEBUG && TRUE === WPSS_DEBUG ) {
		if( !empty( $rsds_only ) && strpos( WPSS_SERVER_NAME_REV, WPSS_DEBUG_SERVER_NAME_REV ) !== 0 ) { return; }
		if( !empty( $spec_func ) && FALSE === strpos( ' Func: '.(string)$spec_func ) ) { return; }
		/* Prevent infinite loops while debugging */
		global $wpss_ALD_i, $wpss_ALD_d;
		if( !empty( $wpss_ALD_i ) ) {
			if( $wpss_ALD_i > 15 ) { return; } else { ++$wpss_ALD_i; }
		} else { $wpss_ALD_i = 1; }
		$str = rs_wpss_filter_null( $str );
		$str = str_replace(array(WPSS_EOL,"\t",'   ','  ',), ' ', $str);
		$str_md5 = rs_wpss_md5( strstr( $str, ' | MEM USED', TRUE ) );
		if( !empty( $wpss_ALD_d ) && is_array( $wpss_ALD_d ) ) {
			if( isset( $wpss_ALD_d[$str_md5] ) ) { return; }
		} else { $wpss_ALD_d = array(); }
		$wpss_ALD_d[$str_md5] = $str;
		$wpss_log_str = 'WP-SpamShield DEBUG: [' . rs_wpss_get_ip_addr() . '][ ' . WPSS_THIS_URL . ' ] '.$str;
		error_log( trim( rs_wpss_filter_null( $wpss_log_str ) ), 0 ); /* Logs to debug.log */
	}
}

function rs_wpss_filter_null($str) {
	return str_replace(chr(0),'',$str);
}

function rs_wpss_get_log_session_data() {
	$noda 					= '[No Data]';
	$key_total_page_hits	= 'wpss_page_hits_js_'.WPSS_HASH;
	$key_last_ref			= 'wpss_jscripts_referer_last_'.WPSS_HASH;
	$key_pages_hist 		= 'wpss_jscripts_referers_history_'.WPSS_HASH;
	$key_hits_per_page		= 'wpss_jscripts_referers_history_count_'.WPSS_HASH;
	$key_ip_hist 			= 'wpss_jscripts_ip_history_'.WPSS_HASH;
	$key_init_ip			= 'wpss_user_ip_init_'.WPSS_HASH;
	$key_pt_hist 			= 'wpss_jscripts_pt_history_'.WPSS_HASH;
	$key_init_pt			= 'wpss_user_pt_init_'.WPSS_HASH;
	$key_init_mt			= 'wpss_time_init_'.WPSS_HASH;
	$key_init_dt			= 'wpss_timestamp_init_'.WPSS_HASH;
	$key_first_ref			= 'wpss_referer_init_'.WPSS_HASH;
	$key_auth_hist 			= 'wpss_author_history_'.WPSS_HASH;
	$key_comment_auth 		= 'comment_author_'.WPSS_HASH;
	$key_email_hist 		= 'wpss_author_email_history_'.WPSS_HASH;
	$key_comment_email		= 'comment_author_email_'.WPSS_HASH;
	$key_auth_url_hist 		= 'wpss_author_url_history_'.WPSS_HASH;
	$key_comment_url		= 'comment_author_url_'.WPSS_HASH;
	$key_comment_acc		= 'wpss_comments_accepted_'.WPSS_HASH;
	$key_comment_den		= 'wpss_comments_denied_'.WPSS_HASH;
	$key_comment_stat_curr	= 'wpss_comments_status_current_'.WPSS_HASH;
	$key_append_log_data	= 'wpss_append_log_data_'.WPSS_HASH;
	global $wpss_session_id, $wpss_session_name;
	if( empty( $wpss_session_id ) ) 	{ $wpss_session_id		= @session_id(); }
	if( empty( $wpss_session_name ) ) 	{ $wpss_session_name	= @session_name(); }
	if( empty( $wpss_session_name ) )	{ $wpss_session_name	= 'PHPSESSID'; }
	if( !empty( $_COOKIE[$wpss_session_name] ) ) {
		$wpss_session_ck = $_COOKIE[$wpss_session_name];
		if( $wpss_session_ck === $wpss_session_id  ) { $wpss_session_verified = '[Verified]'; }
		else { $wpss_session_verified = '[Not Verified]'; }
	} else { $wpss_session_ck = '[No Session Cookie]'; $wpss_session_verified = '[Not Verified]'; }
	if( !empty( $_SESSION[$key_total_page_hits] ) ) { $wpss_page_hits = $_SESSION[$key_total_page_hits]; }
	else { $wpss_page_hits = $noda; }
	if( !empty( $_SESSION[$key_last_ref] ) ) { $wpss_last_page_hit = $_SESSION[$key_last_ref]; }
	else { $wpss_last_page_hit = $noda; }
	$wpss_pages_history = '';
	if( !empty( $_SESSION[$key_pages_hist] ) ) { $wpss_pages_history_data = $_SESSION[$key_pages_hist]; }
	else { $wpss_pages_history = $noda; }
	if( $wpss_pages_history !== $noda ) {
		$wpss_pages_history = WPSS_EOL;
		foreach( $wpss_pages_history_data as $page ) { $wpss_pages_history .= "['".$page."']".WPSS_EOL; }
	}
	$wpss_hits_per_page = '';
	if( !empty( $_SESSION[$key_hits_per_page] ) ) { $wpss_hits_per_page_data = $_SESSION[$key_hits_per_page]; }
	else { $wpss_hits_per_page = $noda; }
	if( $wpss_hits_per_page !== $noda ) {
		$wpss_hits_per_page = WPSS_EOL;
		foreach( $wpss_hits_per_page_data as $page => $hits ) { $wpss_hits_per_page .= "['".$hits."'] ['".$page."']".WPSS_EOL; }
	}
	if( !empty( $_SESSION[$key_init_ip] ) ) { $wpss_user_ip_init = $_SESSION[$key_init_ip]; } else { $wpss_user_ip_init = $noda; }
	if( !empty( $_SESSION[$key_ip_hist] ) ) { $wpss_ip_history = implode(', ', $_SESSION[$key_ip_hist]); } else { $wpss_ip_history = $noda; }
	if( !empty( $_SESSION[$key_init_pt] ) ) { $wpss_user_pt_init = $_SESSION[$key_init_pt]; } else { $wpss_user_pt_init = $noda; }
	if( !empty( $_SESSION[$key_pt_hist] ) ) { $wpss_pt_history = implode(', ', $_SESSION[$key_pt_hist]); } else { $wpss_pt_history = $noda; }
	if( !empty( $_SESSION[$key_init_mt] ) ) { $wpss_time_init = $_SESSION[$key_init_mt]; } else { $wpss_time_init = ''; }
	$ck_key_init_dt 	= 'NCS_INENTIM'; /* Initial Entry Time - PHP Cookie is backup to session var */
	$ck_key_init_dt_js 	= 'JCS_INENTIM'; /* Initial Entry Time - JS Cookie is backup to PHP Cookie */
	if( !empty( $_COOKIE[$ck_key_init_dt] ) ) { $wpss_ck_timestamp_init_int = $_COOKIE[$ck_key_init_dt]; }
	elseif( !empty( $_COOKIE[$ck_key_init_dt_js] ) ) { $wpss_ck_timestamp_init_int = round($_COOKIE[$ck_key_init_dt_js]/1000); }
	else { $wpss_ck_timestamp_init_int = ''; }
	if( !empty( $_SESSION[$key_init_dt] ) ) { $wpss_timestamp_init = $_SESSION[$key_init_dt]; }
	elseif( !empty( $wpss_ck_timestamp_init_int ) ) { $wpss_timestamp_init = $wpss_ck_timestamp_init_int; } else { $wpss_timestamp_init = ''; }
	if( !empty( $_SESSION[$key_first_ref] ) ) { $wpss_referer_init = $_SESSION[$key_first_ref]; } elseif( !empty( $_COOKIE['JCS_INENREF'] ) ) { $wpss_referer_init = $_SESSION[$key_first_ref] = $_COOKIE['JCS_INENREF']; } else { $wpss_referer_init = $noda; }
	$wpss_referer_init_js = rs_wpss_get_referrer( FALSE, TRUE, TRUE ); /* Initial referrer, aka "Referring Site" - Changed 1.7.9 */
	if( empty( $wpss_referer_init_js ) ) { $wpss_referer_init_js = $noda; }
	if( !empty( $_SESSION[$key_auth_hist] ) ) { $wpss_author_history = implode(', ', $_SESSION[$key_auth_hist]); }
	elseif( !empty( $_COOKIE[$key_comment_auth] ) ) { $wpss_author_history = $_COOKIE[$key_comment_auth]; }
	else { $wpss_author_history = $noda; }
	if( !empty( $_SESSION[$key_email_hist] ) ) { $wpss_author_email_history = implode(', ', $_SESSION[$key_email_hist]); }
	elseif( !empty( $_COOKIE[$key_comment_email] ) ) { $wpss_author_email_history = $_COOKIE[$key_comment_email]; }
	else { $wpss_author_email_history = $noda; }
	if( !empty( $_SESSION[$key_auth_url_hist] ) ) { $wpss_author_url_history = implode(', ', $_SESSION[$key_auth_url_hist]); }
	elseif( !empty( $_COOKIE[$key_comment_url] ) ) { $wpss_author_url_history = $_COOKIE[$key_comment_url]; }
	else { $wpss_author_url_history = $noda; }
	if( !empty( $_SESSION[$key_comment_acc] ) ) { $wpss_comments_accepted = $_SESSION[$key_comment_acc]; } else { $wpss_comments_accepted = $noda; }
	if( !empty( $_SESSION[$key_comment_den] ) ) { $wpss_comments_denied = $_SESSION[$key_comment_den]; } else { $wpss_comments_denied = $noda; }
	/* Current status */
	if( !empty( $_SESSION[$key_comment_stat_curr] ) ) { $wpss_comments_status_current = $_SESSION[$key_comment_stat_curr]; } else { $wpss_comments_status_current = $noda; }
	if( !empty( $_SESSION[$key_append_log_data] ) ) { $wpss_append_log_data = $_SESSION[$key_append_log_data]; } else { $wpss_append_log_data = $noda; }
	$wpss_log_session_data = compact( 'wpss_session_id', 'wpss_session_name', 'wpss_session_ck', 'wpss_session_verified', 'wpss_page_hits', 'wpss_last_page_hit', 'wpss_pages_history', 'wpss_hits_per_page', 'wpss_user_ip_init', 'wpss_ip_history', 'wpss_user_pt_init', 'wpss_pt_history', 'wpss_time_init', 'wpss_timestamp_init', 'wpss_referer_init', 'wpss_referer_init_js', 'wpss_author_history', 'wpss_author_email_history', 'wpss_author_url_history', 'wpss_comments_accepted', 'wpss_comments_denied', 'wpss_comments_status_current', 'wpss_append_log_data' );
	return $wpss_log_session_data;
}

function rs_wpss_log_data( $wpss_log_data_array, $wpss_log_data_errors, $wpss_log_comment_type = 'comment', $wpss_log_contact_form_data = NULL, $wpss_log_contact_form_id = NULL, $wpss_log_contact_form_mcid = NULL ) {
	/**
	 *  Example:
	 *  Comment:			rs_wpss_log_data( $commentdata, $wpss_error_code )
	 *  Contact Form:		rs_wpss_log_data( $cf_author_data, $wpss_error_code, 'contact form', $wpss_contact_form_msg, $wpss_contact_form_mid, $wpss_contact_form_mcid );
	 *  Registration:		rs_wpss_log_data( $register_author_data, $wpss_error_code, 'register' );
	 *  BuddyPress Reg:		rs_wpss_log_data( $register_author_data, $wpss_error_code, 'bp-register' );
	 *  WooCommerce Reg:	rs_wpss_log_data( $register_author_data, $wpss_error_code, 'wc-register' );
	 *  s2Member Reg:		rs_wpss_log_data( $register_author_data, $wpss_error_code, 's2-register' );
	 *  WP-Members Reg:		rs_wpss_log_data( $register_author_data, $wpss_error_code, 'wpm-register' );
	 *  Affiliates Reg:		rs_wpss_log_data( $register_author_data, $wpss_error_code, 'aff-register' );
	 *  Contact Form 7:		rs_wpss_log_data( $form_auth_dat, $wpss_error_code, 'contact form 7', $cf7_serial_post );
	 *  Gravity Forms:		rs_wpss_log_data( $form_auth_dat, $wpss_error_code, 'gravity forms', $gf_serial_post );
	 *  Miscellaneous Form:	rs_wpss_log_data( $form_auth_dat, $wpss_error_code, 'misc form', $msc_serial_post );
	 *  JetPack Form:		rs_wpss_log_data( $form_auth_dat, $wpss_error_code, 'jetpack form', $msc_serial_post );
	 *  Ninja Forms:		rs_wpss_log_data( $form_auth_dat, $wpss_error_code, 'ninja forms', $msc_serial_post );
	 *  Mailchimp Signup:	rs_wpss_log_data( $form_auth_dat, $wpss_error_code, 'mailchimp form', $msc_serial_post );
	 *  TO DO: 				Add types for EPC and XML-RPC
	 */

	global $spamshield_options,$current_user,$wpss_active_plugins,$wpss_active_network_plugins,$wpss_cl_active,$wpss_cache_check,$wpss_geolocation;
	$wpss_log_session_data = rs_wpss_get_log_session_data();
	extract( $wpss_log_session_data );

	$noda = '[No Data]'; $thick_line = str_repeat('═',100); $thin_line = str_repeat('─',100);

	/* Timer - BEGIN*/
	$wpss_time_end					= microtime(TRUE);
	if( empty( $wpss_time_init ) && !empty( $wpss_timestamp_init ) ) { $wpss_time_init = $wpss_timestamp_init; }
	if( !empty( $wpss_time_init ) ) {
		$wpss_time_on_site			= rs_wpss_timer( $wpss_time_init, $wpss_time_end, TRUE, 2 );
	} else { $wpss_time_on_site 		= $noda; }
	if( !empty( $wpss_timestamp_init ) ) {
		$wpss_site_entry_time		= get_date_from_gmt( date( 'Y-m-d H:i:s', $wpss_timestamp_init ), 'Y-m-d (D) H:i:s e' ); /* Added 1.7.3 */
	} else { $wpss_site_entry_time 	= $noda; }

	/* Timer - END */

	rs_wpss_log_reset( NULL, FALSE, FALSE, TRUE ); /* Create log file if it doesn't exist */

	$wpss_log_key				= rs_wpss_get_log_key();
	$wpss_log_filnm				= strpos( WPSS_SERVER_NAME_REV, WPSS_MDBUG_SERVER_NAME_REV ) === 0 ? 'temp-comments-log.txt' : 'temp-comments-log-'.$wpss_log_key.'.txt';
	$wpss_log_file				= WPSS_PLUGIN_DATA_PATH.'/'.$wpss_log_filnm;
	$wpss_log_max_filesize		= strpos( WPSS_SERVER_NAME_REV, WPSS_MDBUG_SERVER_NAME_REV ) === 0 ? 20*1048576 : 2*1048576; /* 2 MB */

	if( empty( $wpss_log_comment_type ) ) { $wpss_log_comment_type = 'comment'; }
	$wpss_log_comment_type_display 			= rs_wpss_casetrans('upper',$wpss_log_comment_type);
	$wpss_log_comment_type_display_len		= rs_wpss_strlen($wpss_log_comment_type_display);
	$wpss_log_comment_type_display_len_begin= $wpss_log_comment_type_display_len + 6;
	$wpss_log_comment_type_display_len_end	= $wpss_log_comment_type_display_len + 4;
	$wpss_log_rem_spaces_begin				= 92 - $wpss_log_comment_type_display_len_begin; /* 100 - 8 - x */
	$wpss_log_rem_spaces_end				= 92 - $wpss_log_comment_type_display_len_end; /* 100 - 8 - x */
	$wpss_log_comment_type_ucwords			= rs_wpss_casetrans('ucwords',$wpss_log_comment_type);
	$wpss_log_comment_type_ucwords_ref_disp	= preg_replace("~\sform~i", "", $wpss_log_comment_type_ucwords);

	$wpss_display_name = $wpss_user_firstname = $wpss_user_lastname = $wpss_user_email = $wpss_user_url = $wpss_user_login = $wpss_user_id = $wpss_rsds = $bclm_off = $bclm_oc = '';
	$wpss_user_logged_in 		= FALSE;
	if( rs_wpss_is_user_logged_in() ) {
		$current_user			= wp_get_current_user();
		$wpss_display_name 		= $current_user->display_name;
		$wpss_user_firstname 	= $current_user->user_firstname;
		$wpss_user_lastname 	= $current_user->user_lastname;
		$wpss_user_email		= $current_user->user_email;
		$wpss_user_url			= $current_user->user_url;
		$wpss_user_login 		= $current_user->user_login;
		$wpss_user_id	 		= $current_user->ID;
		$wpss_user_logged_in	= TRUE;
	}

	if( empty( $spamshield_options ) ) { $spamshield_options = get_option('spamshield_options'); }
	rs_wpss_update_session_data($spamshield_options);

	$wpss_active_plugins			= rs_wpss_get_active_plugins();
	$wpss_active_plugins_str		= implode( ', ', $wpss_active_plugins );
	if( is_multisite() ) {
		if( empty( $wpss_active_network_plugins ) ) { $wpss_active_network_plugins = rs_wpss_get_active_network_plugins(); }
		if( !empty( $wpss_active_network_plugins ) ) { $wpss_active_network_plugins_str = implode( ', ', $wpss_active_network_plugins ); }
	}
	if( empty( $wpss_cl_active ) ) {
		$wpss_cl_active				= WPSS_Compatibility::is_plugin_active( 'commentluv/commentluv.php' );
	}

	$wpss_plugin_user_agent			= rs_wpss_get_plugin_user_agent();
	$wpss_php_uname 				= function_exists( 'php_uname' ) ? @php_uname() : PHP_OS .' '. @gethostname();
	$timenow						= time();
	$comment_logging 				= $spamshield_options['comment_logging'];
	$comment_logging_all 			= $spamshield_options['comment_logging_all'];
	$comment_logging_start_date		= $spamshield_options['comment_logging_start_date'] = ( !empty( $spamshield_options['comment_logging_start_date'] ) && $spamshield_options['comment_logging_start_date'] > 1451606400 ) ? $spamshield_options['comment_logging_start_date'] : $timenow;
	$wpss_javascript_page_referrer	= !empty( $wpss_log_data_array['javascript_page_referrer'] ) ? $wpss_log_data_array['javascript_page_referrer'] : '';
	$wpss_jsonst					= !empty( $wpss_log_data_array['jsonst'] ) ? $wpss_log_data_array['jsonst'] : '';

	/**
	 *  Display local time in logs. Won't match other time logs, because those need to be UTC.
	 *  MINUTE_IN_SECONDS, HOUR_IN_SECONDS, DAY_IN_SECONDS , WEEK_IN_SECONDS, MONTH_IN_SECONDS, YEAR_IN_SECONDS
	 */
	$timenow_display			= current_time( 'timestamp', 0 );
	$reset_interval_rsds		= 10 * YEAR_IN_SECONDS;
	$reset_interval_default		= strpos( WPSS_SERVER_NAME_REV, WPSS_MDBUG_SERVER_NAME_REV ) === 0 ? $reset_interval_rsds : WEEK_IN_SECONDS; /* Default is one week */
	$reset_interval_override	= $reset_interval_default; /* Change for TESTING only, and it will override defaults */
	$reset_interval				= ( $reset_interval_override !== $reset_interval_default ) ? $reset_interval_override : $reset_interval_default;
	$comment_logging_end_date	= $spamshield_options['comment_logging_end_date'] = ( !empty( $spamshield_options['comment_logging_end_date'] ) && $spamshield_options['comment_logging_end_date'] > 1451606400 ) ? $spamshield_options['comment_logging_end_date'] : $comment_logging_start_date + $reset_interval;
	/**
	 *  Automatically turns off Blocked Spam Logging Mode if 1 of 2 conditions met:
	 *  	1) Over X amount of time since starting
	 *  	2) Filesize exceeds max
	 */
	if( $timenow >= $comment_logging_end_date ) { $bclm_off = TRUE; $bclm_oc = 'T'; }
	elseif( file_exists( $wpss_log_file ) && filesize( $wpss_log_file ) >= $wpss_log_max_filesize ) { $bclm_off = TRUE; $bclm_oc = 'FS'; }
	if( !empty( $bclm_off ) ) { /* Turns Blocked Spam Logging Mode off and clears vaues */
		WP_SpamShield::update_wpss_option( array( 'comment_logging' => 0, 'comment_logging_all' => 0, 'comment_logging_start_date' => 0, 'comment_logging_end_date' => 0, ) );
		if( !empty( $wpss_rsds ) ) { rs_wpss_append_log_data( 'Blocked Spam Logging Mode has been disabled. '.'['.$bclm_oc.']', FALSE ); }
	} else {
		/* LOG DATA */
		WP_SpamShield::update_wpss_option( array( 'comment_logging' => $comment_logging, 'comment_logging_all' => $comment_logging_all, 'comment_logging_start_date' => $comment_logging_start_date, 'comment_logging_end_date' => $comment_logging_end_date, ) );
		if( empty( $wpss_cache_check ) ) { $wpss_cache_check = rs_wpss_check_cache_status(); }
		$wpss_log_datum			= date('Y-m-d (D) H:i:s',$timenow_display);
		$wpss_log_url 			= WPSS_THIS_URL;
		$wpss_is_ajax			= rs_wpss_is_ajax_request() ? 'TRUE' : 'FALSE';
		$wpss_is_comment		= rs_wpss_is_comment_request() ? 'TRUE' : 'FALSE';
		$wpss_compat_on			= TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) ? 'ON' : 'OFF';
		$wpss_cache_on			= $wpss_cache_check['cache_check_status'] === 'ACTIVE' ? 'ON' : 'OFF';
		$wpss_log_data			= '';
		$wpss_log_data			.= $thick_line.WPSS_EOL;
		$wpss_log_data			.= '────┬─'.str_repeat('─',$wpss_log_comment_type_display_len_begin).'─┬'.str_repeat('─',$wpss_log_rem_spaces_begin).WPSS_EOL;
		$wpss_log_data		   	.= '    │ '.$wpss_log_comment_type_display.' BEGIN'.' │'.WPSS_EOL;
		$wpss_log_data			.= '────┴─'.str_repeat('─',$wpss_log_comment_type_display_len_begin).'─┴'.str_repeat('─',$wpss_log_rem_spaces_begin).WPSS_EOL;

		$submitter_ip_address = rs_wpss_get_ip_addr();
		$submitter_ip_address_short_l = trim( substr( $submitter_ip_address, 0, 6) );
		$submitter_ip_address_short_r = trim( substr( $submitter_ip_address, -6, 2) );
		$submitter_ip_address_obfuscated = $submitter_ip_address_short_l.'****'.$submitter_ip_address_short_r.'.***';

		/* IP / PROXY INFO - BEGIN */
		global $wpss_ip_proxy_info;
		if( empty( $wpss_ip_proxy_info ) ) { $wpss_ip_proxy_info = rs_wpss_ip_proxy_info(); }
		extract( $wpss_ip_proxy_info );
		/* IP / PROXY INFO - END */

		if( empty ( $wpss_geolocation ) ) { $wpss_geolocation = rs_wpss_wf_geoiploc( $ip, TRUE ); }
		$port = ( !empty( $_SERVER['REMOTE_PORT'] ) ) ? $_SERVER['REMOTE_PORT'] : '';

		$wpss_spamshield_count = rs_wpss_number_format( rs_wpss_count() );
		if( $wpss_log_comment_type === 'comment' || $wpss_log_comment_type === 'contact form' ) { $body_content_length = rs_wpss_number_format( $wpss_log_data_array['body_content_len'] ); } else { $body_content_length = ''; }
		if( $wpss_log_comment_type === 'comment' ) {
			$wpss_log_data .= $thin_line.WPSS_EOL;
			/* Comment Post Info */
			$comment_author_email = $wpss_log_data_array['comment_author_email']; $comment_types_allowed = '';
			if( !empty( $wpss_log_data_array['comment_post_comments_open'] ) ) {
				$comment_post_comments_open = 'Open'; $comment_types_allowed .= 'comments';
			} else { $comment_post_comments_open = 'Closed'; }
			if( !empty( $wpss_log_data_array['comment_post_pings_open'] ) ) {
				$comment_post_pings_open = 'Open';
				if( !empty( $comment_types_allowed ) ) { $comment_types_allowed .= ','; }
				$comment_types_allowed .= 'pingbacks,trackbacks';
			} else { $comment_post_pings_open = 'Closed'; }
			if( empty( $comment_types_allowed ) ) { $comment_types_allowed = 'none, comments closed'; }
			if( empty( $wpss_log_data_array['comment_post_type'] ) ) { $wpss_log_data_array['comment_post_type'] = 'Post'; }
			$comment_post_type_ucw = rs_wpss_casetrans('ucwords',$wpss_log_data_array['comment_post_type']);

			$wpss_log_data .= "Date/Time:              ['".$wpss_log_datum."']".WPSS_EOL;
			$wpss_log_data .= "Comment Post ID:        ['".$wpss_log_data_array['comment_post_ID']."']".WPSS_EOL;
			$wpss_log_data .= "Comment Post Title:     ['".$wpss_log_data_array['comment_post_title']."']".WPSS_EOL;
			$wpss_log_data .= "Comment Post URL:       ['".$wpss_log_data_array['comment_post_url']."']".WPSS_EOL;
			$wpss_log_data .= "Comment Post Type:      ['".$wpss_log_data_array['comment_post_type']."']".WPSS_EOL;
			$wpss_log_data .= substr($comment_post_type_ucw." Allows Types:      ",0,24)."['".$comment_types_allowed."']".WPSS_EOL;
			$wpss_log_data .= "Comment Type:           ['";
			$wpss_log_data .= ( !empty( $wpss_log_data_array['comment_type'] ) ) ? $wpss_log_data_array['comment_type'] : 'comment';
			$wpss_log_data .= "']";
			$wpss_log_data .= WPSS_EOL;
			$wpss_log_data .= $thin_line.WPSS_EOL;
			$wpss_log_data .= "Comment Author:         ['".$wpss_log_data_array['comment_author']."']".WPSS_EOL;
			$wpss_log_data .= "Comment Author Email:   ['".$comment_author_email."']".WPSS_EOL;
			$wpss_log_data .= "Comment Author URL:     ['".$wpss_log_data_array['comment_author_url']."']".WPSS_EOL;
			$wpss_log_data .= "Comment Content: ".WPSS_EOL."['comment_content_begin']".WPSS_EOL.$wpss_log_data_array['comment_content'].WPSS_EOL."['comment_content_end']".WPSS_EOL;
			$wpss_log_data .= $thin_line.WPSS_EOL;
			$wpss_log_data .= "WPSSCID:                ['".$wpss_log_data_array['comment_wpss_cid']."']".WPSS_EOL; /* Added 1.7.7 - WPSS Comment ID */
			$wpss_log_data .= "WPSSCCID:               ['".$wpss_log_data_array['comment_wpss_ccid']."']".WPSS_EOL; /* Added 1.7.7 - WPSS Comment Content ID */
		} elseif( strpos( $wpss_log_comment_type, 'register' ) !== FALSE ) {
			$wpss_log_data .= $thin_line.WPSS_EOL;
			$wpss_log_data .= "Date/Time:              ['".$wpss_log_datum."']".WPSS_EOL;
			if( empty( $wpss_log_data_array['ID'] ) ) { $wpss_log_data_array['ID'] = '[None]'; }
			$wpss_log_data .= "User ID:                ['".$wpss_log_data_array['ID']."']".WPSS_EOL;
			$wpss_log_data .= "User Login:             ['".$wpss_log_data_array['user_login']."']".WPSS_EOL;
			$wpss_log_data .= "Display Name:           ['".$wpss_log_data_array['display_name']."']".WPSS_EOL;
			$wpss_log_data .= "First Name:             ['".$wpss_log_data_array['user_firstname']."']".WPSS_EOL;
			$wpss_log_data .= "Last Name:              ['".$wpss_log_data_array['user_lastname']."']".WPSS_EOL;
			$wpss_log_data .= "User Email:             ['".$wpss_log_data_array['user_email']."']".WPSS_EOL;
			if( empty( $wpss_log_data_array['user_url'] ) ) { $wpss_log_data_array['user_url'] = '[None]'; }
			$wpss_log_data .= "User URL:               ['".$wpss_log_data_array['user_url']."']".WPSS_EOL;
		} elseif( $wpss_log_comment_type === 'contact form' ) {
			$wpss_log_contact_form_subject = !empty( $_POST['wpss_contact_subject'] ) ? rs_wpss_prep_cf_email(stripslashes(sanitize_text_field($_POST['wpss_contact_subject']))) : '';
			$wpss_log_data .= $thin_line.WPSS_EOL;
			$wpss_log_data .= "Date/Time:              ['".$wpss_log_datum."']".WPSS_EOL;
			$wpss_log_data .= $thin_line.WPSS_EOL;
			$wpss_log_data .= "Subject:                ['".$wpss_log_contact_form_subject."']".WPSS_EOL;
			$wpss_log_data .= $thin_line.WPSS_EOL;
			$wpss_log_data .= $wpss_log_contact_form_data;
			$wpss_log_data .= $thin_line.WPSS_EOL;
			$wpss_log_data .= "WPSSMID:                ['".$wpss_log_contact_form_id."']".WPSS_EOL; /* Added 1.7.7 - WPSS Message ID */
			$wpss_log_data .= "WPSSMCID:               ['".$wpss_log_contact_form_mcid."']".WPSS_EOL; /* Added 1.7.7 - WPSS Message Content ID */
		} elseif( strpos( $wpss_log_comment_type, 'form' ) !== FALSE ) {
			$wpss_log_data .= $thin_line.WPSS_EOL;
			$wpss_log_data .= "Date/Time:              ['".$wpss_log_datum."']".WPSS_EOL;
			$wpss_log_data .= $thin_line.WPSS_EOL;
			$form_post_data_arr = json_decode( $wpss_log_contact_form_data, TRUE );
			$form_post_data_disp = '';
			foreach( $form_post_data_arr as $k => $v ) {
				if( is_array($v) ) { $v = implode( '|', $v ); }
				$form_post_data_disp .= $k.': '.trim(stripslashes($v)).WPSS_EOL;
			}
			$wpss_log_data .= $form_post_data_disp;
		}
		$wpss_sessions_enabled			= isset( $_SESSION ) ? 'Enabled' : 'Disabled';
		$wpss_server_content_type		= !empty( $_SERVER['CONTENT_TYPE'] ) ? $_SERVER['CONTENT_TYPE'] : '';

		/* Sanitized versions for output */
		$wpss_http_accept				= rs_wpss_get_http_accept();
		$wpss_http_accept_language 		= rs_wpss_get_http_accept( FALSE, FALSE, TRUE );
		$wpss_http_accept_encoding 		= rs_wpss_get_http_accept( FALSE, FALSE, FALSE, TRUE );
		$server_x_req_w 				= rs_wpss_get_server_x_req_w();
		$wpss_http_user_agent 			= rs_wpss_get_user_agent();
		$wpss_http_browser 				= rs_wpss_get_browser();
		$wpss_http_referer				= rs_wpss_get_referrer(); /* Not original ref - Comment Processor Referrer */
		$wpss_log_data .= $thin_line.WPSS_EOL;
		if( $wpss_user_logged_in !== FALSE ) {
			$wpss_log_data .= "User ID:                ['".$wpss_user_id."']".WPSS_EOL;
		}
		$wpss_log_data .= "User-Agent:             ['".$wpss_http_user_agent."']".WPSS_EOL;
		if( !empty( $wpss_http_browser ) ) {
			$wpss_log_data .= "Browser:                ['".$wpss_http_browser."']".WPSS_EOL;
		}
		if( !empty( $wpss_geolocation ) ) {
			$wpss_log_data .= "Location:               ['".$wpss_geolocation."']".WPSS_EOL;
		}
		$wpss_log_data .= "IP Address:             ['".$ip."'] ['http://ipaddressdata.com/".$ip."']".WPSS_EOL;
		$wpss_log_data .= "Port:                   ['".$port."']".WPSS_EOL;
		$wpss_log_data .= "Reverse DNS:            ['".$reverse_dns."']".WPSS_EOL;
		$wpss_log_data .= "Reverse DNS IP:         ['".$reverse_dns_ip."']".WPSS_EOL;
		$wpss_log_data .= "FCrDNS Verified:        ['".$reverse_dns_verification."']".WPSS_EOL; /* Forward-confirmed reverse DNS (FCrDNS) */
		$wpss_log_data .= "Proxy Info:             ['".$ip_proxy."']".WPSS_EOL;
		$wpss_log_data .= "Proxy Data:             ['".$ip_proxy_data."']".WPSS_EOL;
		$wpss_log_data .= "Proxy Status:           ['".$proxy_status."']".WPSS_EOL;
		if( empty( $ip_proxy_via ) ) { $ip_proxy_via = '[None]'; }
		$wpss_log_data .= "HTTP_VIA:               ['".$ip_proxy_via."']".WPSS_EOL;
		if( empty( $masked_ip ) ) { $masked_ip = '[None]'; }
		$wpss_log_data .= "HTTP_X_FORWARDED_FOR:   ['".$masked_ip."']".WPSS_EOL;
		if( strpos( WPSS_SERVER_NAME_REV, WPSS_DEBUG_SERVER_NAME_REV ) === 0 || ( TRUE === WP_DEBUG && TRUE === WPSS_DEBUG ) ) {
			if( !empty( $http_x_forwarded ) )		{ $wpss_log_data .= "HTTP_X_FORWARDED:       ['".$http_x_forwarded."']".WPSS_EOL; }
			if( !empty( $http_forwarded_for ) )		{ $wpss_log_data .= "HTTP_FORWARDED_FOR:     ['".$http_forwarded_for."']".WPSS_EOL;}
			if( !empty( $http_forwarded ) )			{ $wpss_log_data .= "HTTP_FORWARDED:         ['".$http_forwarded."']".WPSS_EOL; }
			if( !empty( $http_x_real_ip ) )			{ $wpss_log_data .= "HTTP_X_REAL_IP:         ['".$http_x_real_ip."']".WPSS_EOL; }
			if( !empty( $http_x_sucuri_clientip) )	{ $wpss_log_data .= "HTTP_X_SUCURI_CLIENTIP: ['".$http_x_sucuri_clientip."']".WPSS_EOL; }
			if( !empty( $http_cf_connecting_ip ) )	{ $wpss_log_data .= "HTTP_CF_CONNECTING_IP:  ['".$http_cf_connecting_ip."']".WPSS_EOL; }
			if( !empty( $http_incap_client_ip ) )	{ $wpss_log_data .= "HTTP_INCAP_CLIENT_IP:   ['".$http_incap_client_ip."']".WPSS_EOL; }
			if( !empty( $http_client_ip ) )			{ $wpss_log_data .= "HTTP_CLIENT_IP:         ['".$http_client_ip."']".WPSS_EOL; }
		}
		$wpss_log_data .= "SERVER_PROTOCOL:        ['". $_SERVER['SERVER_PROTOCOL'] ."']".WPSS_EOL;
		$wpss_log_data .= "HTTP_ACCEPT:            ['".$wpss_http_accept."']".WPSS_EOL;
		$wpss_log_data .= "HTTP_ACCEPT_LANGUAGE:   ['".$wpss_http_accept_language."']".WPSS_EOL;
		$wpss_log_data .= "HTTP_ACCEPT_ENCODING:   ['".$wpss_http_accept_encoding."']".WPSS_EOL;
		$wpss_log_data .= "CONTENT_TYPE:           ['".$wpss_server_content_type."']".WPSS_EOL;
		$wpss_log_data .= "HTTP_X_REQUESTED_WITH:  ['".$server_x_req_w."']".WPSS_EOL;
		$wpss_log_data .= "IS_AJAX:                ['".$wpss_is_ajax."']".WPSS_EOL;
		$wpss_log_data .= "IS_COMMENT:             ['".$wpss_is_comment."']".WPSS_EOL;
		if( $wpss_log_comment_type === 'misc form' ) {	/* TO DO: Add types for EPC and XML-RPC */
			$hdr_data			= rs_wpss_get_http_status( $wpss_log_url ); extract( $hdr_data );
			$wpss_http_status	= $status;
			$wpss_log_data .= "HTTP STATUS:            ['".$wpss_http_status."']".WPSS_EOL;
		}
		$wpss_log_data .= "URL:                    ['".$wpss_log_url."']".WPSS_EOL;
		$wpss_log_data .= "Form Processor Ref:     ['";
		if( !empty( $wpss_http_referer ) ) { $wpss_log_data .= $wpss_http_referer; }
		else { $wpss_log_data .= '[None]'; }
		$wpss_log_data .= "']";
		$wpss_log_data .= WPSS_EOL;
		$wpss_log_data .= "JS Page Ref:            ['";
		$wpss_log_data .= !empty( $wpss_javascript_page_referrer ) ? $wpss_javascript_page_referrer : '[None]';
		$wpss_log_data .= "']";
		$wpss_log_data .= WPSS_EOL;

		$wpss_log_data .= "JSONST:                 ['";
		if( !empty( $wpss_jsonst ) ) { $wpss_log_data .= $wpss_jsonst; }
		else { $wpss_log_data .= '[None]'; }
		$wpss_log_data .= "']";
		$wpss_log_data .= WPSS_EOL;

		/* New Data Section - Begin */
		if( strpos( WPSS_SERVER_NAME_REV, WPSS_DEBUG_SERVER_NAME_REV ) === 0 || ( TRUE === WP_DEBUG && TRUE === WPSS_DEBUG ) ) {
			if( !empty( $_SESSION ) )	{ $wpss_log_data_serial_session	= @rs_wpss_json_encode($_SESSION); }	else { $wpss_log_data_serial_session = ''; }
			if( !empty( $_COOKIE ) )	{ $wpss_log_data_serial_cookie 	= @rs_wpss_json_encode($_COOKIE); }		else { $wpss_log_data_serial_cookie = ''; }
			if( !empty( $_GET ) ||
				!empty( $query_str ) )	{ $wpss_log_data_serial_get 	= @rs_wpss_json_encode($_GET); }		else { $wpss_log_data_serial_get = ''; }
			if( !empty( $_POST ) ) {
				$wpss_log_data_post_raw = $_POST;
				switch ( $wpss_log_comment_type ) {
					case 'comment'		: unset( $wpss_log_data_post_raw['comment'] ); break;
					case 'contact form'	: unset( $wpss_log_data_post_raw['wpss_contact_message'] ); break;
				}
				$wpss_log_data_serial_post 	= @rs_wpss_json_encode($wpss_log_data_post_raw);
			} else { $wpss_log_data_serial_post = ''; }
			if( !empty( $_SERVER['REQUEST_METHOD'] ) ) { $wpss_server_request_method = $_SERVER['REQUEST_METHOD']; } else { $wpss_server_request_method = ''; }
			if( !empty( $_SERVER['HTTP_CONNECTION'] ) ) { $wpss_server_http_connection = $_SERVER['HTTP_CONNECTION']; } else { $wpss_server_http_connection = ''; }
			$wpss_mem_used = rs_wpss_wp_memory_used();
			if( !empty( $_SESSION['user_spamshield_count_'.WPSS_HASH] ) )		{ $wpss_user_spamshield_count = $_SESSION['user_spamshield_count_'.WPSS_HASH]; }		else { $wpss_user_spamshield_count = 0; }
			if( !empty( $_SESSION['user_spamshield_count_jsck_'.WPSS_HASH] ) )	{ $wpss_jsck_spamshield_count = $_SESSION['user_spamshield_count_jsck_'.WPSS_HASH]; }	else { $wpss_jsck_spamshield_count = 0; }
			if( !empty( $_SESSION['user_spamshield_count_algo_'.WPSS_HASH] ) )	{ $wpss_algo_spamshield_count = $_SESSION['user_spamshield_count_algo_'.WPSS_HASH]; }	else { $wpss_algo_spamshield_count = 0; }

			$wpss_log_data .= $thin_line.WPSS_EOL;
			$wpss_log_data .= "PHP Session ID:         ['".$wpss_session_id."']".WPSS_EOL;
			$wpss_log_data .= "PHP Session Cookie:     ['".$wpss_session_ck."']".WPSS_EOL;
			$wpss_log_data .= "Sess ID/CK Match:       ['".$wpss_session_verified."']".WPSS_EOL;
			$wpss_log_data .= "Page Hits:              ['".$wpss_page_hits."']".WPSS_EOL;
			$wpss_log_data .= "Last Page Hit:          ['".$wpss_last_page_hit."']".WPSS_EOL;
			$wpss_log_data .= "Hits Per Page: ".WPSS_EOL."['hits_per_page_begin']".$wpss_hits_per_page."['hits_per_page_end']".WPSS_EOL;
			$wpss_log_data .= "Original IP:            ['".$wpss_user_ip_init."']".WPSS_EOL;
			$wpss_log_data .= "IP History:             ['".$wpss_ip_history."']".WPSS_EOL;
			$wpss_log_data .= "Port History:           ['".$wpss_pt_history.', '.$port."']".WPSS_EOL;
			$wpss_log_data .= "Time on Site:           ['".$wpss_time_on_site."']".WPSS_EOL;
			$wpss_log_data .= "Site Entry Time:        ['".$wpss_site_entry_time."']".WPSS_EOL;
			$wpss_log_data .= "Landing Page:           ['".$wpss_referer_init."']".WPSS_EOL; /* PHP */
			$wpss_log_data .= "Original Referrer:      ['".$wpss_referer_init_js."']".WPSS_EOL; /* JS */
			if( !empty( $_COOKIE['_referrer_og'] ) ) {
				$wpss_log_data .= "Clicky Referrer:        ['".$_COOKIE['_referrer_og']."']".WPSS_EOL; /* Added 1.9.7 */
			}
			if( !empty( $_COOKIE['JCS_INENREF'] ) ) {
				$wpss_log_data .= "JCS_INENREF Referrer:   ['".$_COOKIE['JCS_INENREF']."']".WPSS_EOL; /* Added 1.9.9.4 */
			}
			$wpss_log_data .= "Author History:         ['".$wpss_author_history."']".WPSS_EOL;
			$wpss_log_data .= "Email History:          ['".$wpss_author_email_history."']".WPSS_EOL;
			$wpss_log_data .= "URL History:            ['".$wpss_author_url_history."']".WPSS_EOL;
			$wpss_log_data .= "Entries Accepted:       ['".$wpss_comments_accepted."']".WPSS_EOL;
			$wpss_log_data .= "Entries Denied:         ['".$wpss_comments_denied."']".WPSS_EOL;
			$wpss_log_data .= "Spam Count:             ['".$wpss_spamshield_count."']".WPSS_EOL;
			$wpss_log_data .= "User Spam Count:        ['".$wpss_user_spamshield_count."']".WPSS_EOL;
			$wpss_log_data .= "JSCK Spam Count:        ['".$wpss_jsck_spamshield_count."']".WPSS_EOL;
			$wpss_log_data .= "ALGO Spam Count:        ['".$wpss_algo_spamshield_count."']".WPSS_EOL;
			$wpss_log_data .= "Current Status:         ['".$wpss_comments_status_current."']".WPSS_EOL;
			$wpss_log_data .= "REQUEST_METHOD:         ['".$wpss_server_request_method."']".WPSS_EOL;
			$wpss_log_data .= "HTTP_CONNECTION:        ['".$wpss_server_http_connection."']".WPSS_EOL;
			if( $wpss_log_comment_type === 'comment' || $wpss_log_comment_type === 'contact form' ) {
				$wpss_log_data .= "Content Length:         ['".$body_content_length."']".WPSS_EOL;
			}
			/* $wpss_log_data .= '$_SESSION'." Data:      ['".$wpss_log_data_serial_session."']".WPSS_EOL; // TESTING ONLY */
			$wpss_log_data .= '$_COOKIE'." Data:          ['".$wpss_log_data_serial_cookie."']".WPSS_EOL;
			$wpss_log_data .= '$_GET'." Data:             ['".$wpss_log_data_serial_get."']".WPSS_EOL;
			$wpss_log_data .= 'MOD $_POST'." Data:        ['".$wpss_log_data_serial_post."']".WPSS_EOL;
			$wpss_log_data .= "CL Active:              ['".$wpss_cl_active."']".WPSS_EOL;
			$wpss_log_data .= "Mem Used:               ['".$wpss_mem_used."']".WPSS_EOL;
			$wpss_log_data .= "Extra Data:             ['".$wpss_append_log_data."']".WPSS_EOL;
		}
		/* New Data Section - End */
		$wpss_log_data .= $thin_line.WPSS_EOL;
		if( strpos( $wpss_log_data_errors, 'No Error' ) === 0 ) { /* Changed 1.8 */
			$wpss_log_data_errors_count = 0;
		} else {
			$wpss_log_data_errors_count = rs_wpss_count_words($wpss_log_data_errors);
		}
		if( empty( $wpss_log_data_errors ) ) { $wpss_log_data_errors = 'No Error'; }
		if( $wpss_log_comment_type === 'comment' ) {
			if( empty( $wpss_log_data_array['total_time_jsck_filter'] ) ) {
				$wpss_total_time_jsck_filter 		= 0;
			} else {
				$wpss_total_time_jsck_filter 		= $wpss_log_data_array['total_time_jsck_filter'];
			}
			$wpss_total_time_jsck_filter_disp 		= rs_wpss_number_format( $wpss_total_time_jsck_filter, 6 );
			if( empty( $wpss_log_data_array['total_time_content_filter'] ) ) {
				$wpss_total_time_content_filter 	= 0;
			} else {
				$wpss_total_time_content_filter 	= $wpss_log_data_array['total_time_content_filter'];
			}
			$wpss_total_time_content_filter_disp 	= rs_wpss_number_format( $wpss_total_time_content_filter, 6 );
			$wpss_start_time_comment_processing 	= $wpss_log_data_array['start_time_comment_processing'];
			/* Timer End - Comment Processing */
			$wpss_end_time_comment_processing 		= microtime(TRUE);
			$wpss_total_time_wpss_processing 		= $wpss_total_time_jsck_filter + $wpss_total_time_content_filter;
			$wpss_total_time_wpss_processing_disp	= rs_wpss_number_format( $wpss_total_time_wpss_processing, 6 );
			$wpss_total_time_comment_processing 	= rs_wpss_timer( $wpss_start_time_comment_processing, $wpss_end_time_comment_processing, FALSE, 6, TRUE );
			$wpss_total_time_comment_proc_disp		= rs_wpss_number_format( $wpss_total_time_comment_processing, 6);
			$wpss_total_time_wp_processing			= $wpss_total_time_comment_processing - $wpss_total_time_wpss_processing;
			$wpss_total_time_wp_processing_disp		= rs_wpss_number_format( $wpss_total_time_wp_processing, 6 );
			if( !empty( $wpss_total_time_jsck_filter_disp ) || !empty( $wpss_total_time_content_filter_disp ) || !empty( $wpss_total_time_wpss_processing_disp ) ) {
				$wpss_log_data .= "JS/C Processing Time:   ['".$wpss_total_time_jsck_filter_disp." seconds'] Time for JS/Cookies Layer to test for spam".WPSS_EOL;
				$wpss_log_data .= "Algo Processing Time:   ['".$wpss_total_time_content_filter_disp." seconds'] Time for Algorithmic Layer to test for spam".WPSS_EOL;
				$wpss_log_data .= "WPSS Processing Time:   ['".$wpss_total_time_wpss_processing_disp." seconds'] Total time for WP-SpamShield to test for spam".WPSS_EOL;
			}
			if( strpos( WPSS_SERVER_NAME_REV, WPSS_DEBUG_SERVER_NAME_REV ) === 0 ) {
				$wpss_total_time_part_1 			= isset( $wpss_log_data_array['total_time_part_1'] ) ? $wpss_log_data_array['total_time_part_1'] : 0;
				$wpss_total_time_part_1_disp		= rs_wpss_number_format( $wpss_total_time_part_1, 6 );
				$wpss_proc_data = get_option( 'spamshield_procdat' );
				if( empty( $wpss_proc_data ) || !isset( $wpss_proc_data['total_wpss_time'] ) || !isset( $wpss_proc_data['total_comment_proc_time'] ) ) {
					$wpss_proc_data = array(
						'total_tracked' 			=> 0,
						'total_wpss_time' 			=> 0,
						'avg_wpss_proc_time' 		=> 0,
						'total_comment_proc_time' 	=> 0,
						'avg_comment_proc_time' 	=> 0,
						'total_wpss_avg_tracked' 	=> 0,
						'total_avg_wpss_proc_time' 	=> 0,
						'avg2_wpss_proc_time' 		=> 0
						);
				}
				if( !isset( $wpss_proc_data['total_wpss_avg_tracked'] ) ) { $wpss_proc_data['total_wpss_avg_tracked'] = 0; }
				if( !isset( $wpss_proc_data['total_avg_wpss_proc_time'] ) ) { $wpss_proc_data['total_avg_wpss_proc_time'] = 0; }
				if( !isset( $wpss_proc_data['avg2_wpss_proc_time'] ) ) { $wpss_proc_data['avg2_wpss_proc_time'] = 0; }
				$wpss_proc_data_total_tracked 				= $wpss_proc_data['total_tracked'] + 1;
				$wpss_proc_data_total_wpss_time 			= $wpss_proc_data['total_wpss_time'] + $wpss_total_time_wpss_processing;
				$wpss_proc_data_avg_wpss_proc_time 			= $wpss_proc_data_total_wpss_time / $wpss_proc_data_total_tracked;
				$wpss_proc_data_total_comment_proc_time 	= $wpss_proc_data['total_comment_proc_time'] + $wpss_total_time_comment_processing;
				$wpss_proc_data_avg_comment_proc_time 		= $wpss_proc_data_total_comment_proc_time / $wpss_proc_data_total_tracked;
				$wpss_proc_data_total_wpss_avg_tracked 		= $wpss_proc_data['total_wpss_avg_tracked'] + 1;
				$wpss_proc_data_total_avg_wpss_proc_time	= $wpss_proc_data['total_avg_wpss_proc_time'] + $wpss_proc_data_avg_wpss_proc_time;
				$wpss_proc_data_avg2_wpss_proc_time 		= $wpss_proc_data_total_avg_wpss_proc_time / $wpss_proc_data_total_wpss_avg_tracked;
				$wpss_proc_data = array(
					'total_tracked' 			=> $wpss_proc_data_total_tracked,
					'total_wpss_time' 			=> $wpss_proc_data_total_wpss_time,
					'avg_wpss_proc_time' 		=> $wpss_proc_data_avg_wpss_proc_time,
					'total_comment_proc_time' 	=> $wpss_proc_data_total_comment_proc_time,
					'avg_comment_proc_time' 	=> $wpss_proc_data_avg_comment_proc_time,
					'total_wpss_avg_tracked' 	=> $wpss_proc_data_total_wpss_avg_tracked,
					'total_avg_wpss_proc_time' 	=> $wpss_proc_data_total_avg_wpss_proc_time,
					'avg2_wpss_proc_time' 		=> $wpss_proc_data_avg2_wpss_proc_time,
					);
				update_option( 'spamshield_procdat', $wpss_proc_data );
				$wpss_proc_data_avg_wpss_proc_time_disp 	= rs_wpss_number_format( $wpss_proc_data_avg_wpss_proc_time, 6 );
				$wpss_proc_data_avg2_wpss_proc_time_disp 	= rs_wpss_number_format( $wpss_proc_data_avg2_wpss_proc_time, 6 );
				$wpss_proc_data_avg_comment_proc_time_disp 	= rs_wpss_number_format( $wpss_proc_data_avg_comment_proc_time, 6 );
				$wpss_log_data .= "WP Processing Time:     ['".$wpss_total_time_wp_processing_disp." seconds'] Time for other WordPress processes".WPSS_EOL;
				$wpss_log_data .= "Total Processing Time:  ['".$wpss_total_time_comment_proc_disp." seconds'] Total time for WordPress to process comment".WPSS_EOL;
				$wpss_log_data .= "Avg WPSS Proc Time:     ['".$wpss_proc_data_avg_wpss_proc_time_disp." seconds'] Average total time for WP-SpamShield to test for spam".WPSS_EOL;
				$wpss_log_data .= "FAvg WPSS Proc Time:    ['".$wpss_proc_data_avg2_wpss_proc_time_disp." seconds'] Fuzzy Average total WPSS time".WPSS_EOL;
				$wpss_log_data .= "Avg Total Proc Time:    ['".$wpss_proc_data_avg_comment_proc_time_disp." seconds'] Average total time for WordPress to process comments".WPSS_EOL;
			}
			$wpss_log_data .= $thin_line.WPSS_EOL;
		}
		$wpss_log_data .= "Failed Tests:           ['".$wpss_log_data_errors_count."']".WPSS_EOL;
		$wpss_log_data .= "Failed Test Codes:      ['".$wpss_log_data_errors."']".WPSS_EOL;
		$wpss_log_data .= "Spam Count:             ['".$wpss_spamshield_count."']".WPSS_EOL; /* Added 1.8 */
		$wpss_log_data .= $thin_line.WPSS_EOL;
		$wpss_log_data .= "Compatibility Mode:     ['".$wpss_compat_on."']".WPSS_EOL;
		$wpss_log_data .= "Caching:                ['".$wpss_cache_on."']".WPSS_EOL;
		$wpss_log_data .= "Debugging Data:         ['PHP MemLimit: ".WPSS_PHP_MEM_LIMIT."; WP MemLimit: ".WP_MEMORY_LIMIT."; Sessions: ".$wpss_sessions_enabled."']".WPSS_EOL;
		$wpss_log_data .= "Site Server Name:       ['".WPSS_SERVER_NAME."']".WPSS_EOL;
		$wpss_log_data .= "Site Server IP:         ['".WPSS_SERVER_ADDR."']".WPSS_EOL;
		$wpss_log_data .= $thin_line.WPSS_EOL;
		$wpss_log_data .= "Active Plugins:         ['".$wpss_active_plugins_str."']".WPSS_EOL;
		if( is_multisite() && !empty( $wpss_active_network_plugins ) ) {
			$wpss_log_data .= "Active Net Plugins:     ['".$wpss_active_network_plugins_str."']".WPSS_EOL;
		}
		$wpss_log_data .= $thin_line.WPSS_EOL;
		$wpss_log_data .= $wpss_plugin_user_agent.WPSS_EOL;
		$wpss_log_data .= $wpss_php_uname.WPSS_EOL;
		$wpss_log_data		 .= '────┬─'.str_repeat('─',$wpss_log_comment_type_display_len_end).'─┬'.str_repeat('─',$wpss_log_rem_spaces_end).WPSS_EOL;
		$wpss_log_data		 .= '    │ '.$wpss_log_comment_type_display.' END'.' │'.WPSS_EOL;
		$wpss_log_data		 .= '────┴─'.str_repeat('─',$wpss_log_comment_type_display_len_end).'─┴'.str_repeat('─',$wpss_log_rem_spaces_end).WPSS_EOL;
		$wpss_log_data		 .= $thick_line.WPSS_EOL;
		$wpss_log_data .= WPSS_EOL.WPSS_EOL;
		$wpss_log_data	= rs_wpss_filter_null( $wpss_log_data );
		$wpss_log_fp = @fopen( $wpss_log_file,'a+' );
		@fwrite( $wpss_log_fp, $wpss_log_data );
		@fclose( $wpss_log_fp );
	}
}

function rs_wpss_comment_form_append() {
	if( rs_wpss_is_admin_sproc() ) { return; }
	global $spamshield_options; if( empty( $spamshield_options ) ) { $spamshield_options = get_option('spamshield_options'); }
	rs_wpss_update_session_data($spamshield_options);
	$promote_plugin_link 	= !empty( $spamshield_options['promote_plugin_link'] ) ? 1 : 0;
	if( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) || WPSS_Compatibility::comment_form() ) {
		$wpss_key_values 	= rs_wpss_get_key_values();
		$wpss_js_key 		= $wpss_key_values['wpss_js_key'];
		$wpss_js_val 		= $wpss_key_values['wpss_js_val'];
		global $wpss_ao_active; $ao_noop_open = $ao_noop_close = '';
		if( empty( $wpss_ao_active ) ) { $wpss_ao_active = WPSS_Compatibility::is_plugin_active( 'autoptimize/autoptimize.php' ); }
		if( !empty( $wpss_ao_active ) ) { $ao_noop_open = '<!--noptimize-->'; $ao_noop_close = '<!--/noptimize-->'; }
		echo WPSS_EOL.$ao_noop_open.'<script type=\'text/javascript\'>'.WPSS_EOL.'/* <![CDATA[ */'.WPSS_EOL.WPSS_REF2XJS.'=escape(document[\'referrer\']);'.WPSS_EOL.'hf1N=\''.$wpss_js_key.'\';'.WPSS_EOL.'hf1V=\''.$wpss_js_val.'\';'.WPSS_EOL.'document.write("<input type=\'hidden\' name=\''.WPSS_REF2XJS.'\' value=\'"+'.WPSS_REF2XJS.'+"\' /><input type=\'hidden\' name=\'"+hf1N+"\' value=\'"+hf1V+"\' />");'.WPSS_EOL.'/* ]]> */'.WPSS_EOL.'</script>'.$ao_noop_close;
	}
	echo WPSS_EOL.'<noscript><input type="hidden" name="'.WPSS_JSONST.'" value="NS1" /></noscript>'.WPSS_EOL;

	if( !empty( $promote_plugin_link ) ) {
		$sip5c = '0'; $sip5c = substr(WPSS_SERVER_ADDR, 4, 1); /* Server IP 5th Char */
		$ppl_code = array( '0' => 0, '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9, '.' => 10 );
		if( preg_match( "~^[0-9\.]$~", $sip5c ) ) { $int = $ppl_code[$sip5c]; } else { $int = 0; }
		echo WPSS_Promo_Links::comment_promo_link($int).WPSS_EOL;
	}
	$wpss_js_disabled_msg 	= __( 'Currently you have JavaScript disabled. In order to post comments, please make sure JavaScript and Cookies are enabled, and reload the page.', 'wp-spamshield' );
	$wpss_js_enable_msg 	= __( 'Click here for instructions on how to enable JavaScript in your browser.', 'wp-spamshield' );
	echo '<noscript><p><strong>'.$wpss_js_disabled_msg.'</strong> <a href="http://enable-javascript.com/" rel="nofollow external" >'.$wpss_js_enable_msg.'</a></p></noscript>'.WPSS_EOL;

	/* If need to add anything else to comment area, start here */

}

function rs_wpss_get_author_cookie_data() {
	/* Get Comment Author Data Stored in Cookies */
	$key_comment_auth	= 'comment_author_'.WPSS_HASH;
	$key_comment_email	= 'comment_author_email_'.WPSS_HASH;
	$key_comment_url 	= 'comment_author_url_'.WPSS_HASH;
	if( !empty( $_COOKIE[$key_comment_auth] ) ) {
		$comment_author = $_COOKIE[$key_comment_auth];
		if( rs_wpss_is_session_active() ) { $_SESSION[$key_comment_auth] = $comment_author; }
	} else { $comment_author = ''; }
	if( !empty( $_COOKIE[$key_comment_email] ) ) {
		$comment_author_email = $_COOKIE[$key_comment_email];
		if( rs_wpss_is_session_active() ) { $_SESSION[$key_comment_email] = $comment_author_email; }
	} else { $comment_author_email = ''; }
	if( !empty( $_COOKIE[$key_comment_url] ) ) {
		$comment_author_url = $_COOKIE[$key_comment_url];
		if( rs_wpss_is_session_active() ) { $_SESSION[$key_comment_url] = $comment_author_url; }
	} else { $comment_author_url = ''; }
	$author_data = array( 'comment_author' => $comment_author, 'comment_author_email' => $comment_author_email, 'comment_author_url' => $comment_author_url );
	return $author_data;
}

function rs_wpss_get_author_data() {
	/* Get Comment Author Data Stored in Cookies and Session Vars */
	$key_comment_auth	= 'comment_author_'.WPSS_HASH;
	$key_comment_email	= 'comment_author_email_'.WPSS_HASH;
	$key_comment_url 	= 'comment_author_url_'.WPSS_HASH;
	if( !empty( $_COOKIE[$key_comment_auth] ) ) {
		$comment_author = $_COOKIE[$key_comment_auth];
		if( rs_wpss_is_session_active() ) { $_SESSION[$key_comment_auth] = $comment_author; }
	} elseif( rs_wpss_is_session_active() && !empty( $_SESSION[$key_comment_auth] ) ) {
		$comment_author = $_SESSION[$key_comment_auth];
	} else { $comment_author = ''; }
	if( !empty( $_COOKIE[$key_comment_email] ) ) {
		$comment_author_email = $_COOKIE[$key_comment_email];
		if( rs_wpss_is_session_active() ) { $_SESSION[$key_comment_email] = $comment_author_email; }
	} elseif( rs_wpss_is_session_active() && !empty( $_SESSION[$key_comment_email] ) ) {
		$comment_author_email = $_SESSION[$key_comment_email];
	} else { $comment_author_email = ''; }
	if( !empty( $_COOKIE[$key_comment_url] ) ) {
		$comment_author_url = $_COOKIE[$key_comment_url];
		if( rs_wpss_is_session_active() ) { $_SESSION[$key_comment_url] = $comment_author_url; }
	} elseif( rs_wpss_is_session_active() && !empty( $_SESSION[$key_comment_url] ) ) {
		$comment_author_url = $_SESSION[$key_comment_url];
	} else { $comment_author_url = ''; }
	$author_data = array( 'comment_author' => $comment_author, 'comment_author_email' => $comment_author_email, 'comment_author_url' => $comment_author_url );
	return $author_data;
}

function rs_wpss_update_accept_status( $commentdata, $status = 'r', $line = NULL, $error_code = NULL ) {
	$timenow_display		= current_time( 'timestamp', 0 );
	$wpss_datum 			= date('Y-m-d (D) H:i:s',$timenow_display);
	$key_comment_acc 		= 'wpss_comments_accepted_'.WPSS_HASH;
	$key_comment_den 		= 'wpss_comments_denied_'.WPSS_HASH;
	$key_comment_den_jsck	= 'wpss_comments_denied_jsck_'.WPSS_HASH;
	$key_comment_den_algo	= 'wpss_comments_denied_algo_'.WPSS_HASH;
	$key_comment_stat_curr	= 'wpss_comments_status_current_'.WPSS_HASH;
	$key_auth_hist 			= 'wpss_author_history_'.WPSS_HASH;
	$key_email_hist 		= 'wpss_author_email_history_'.WPSS_HASH;
	$key_auth_url_hist 		= 'wpss_author_url_history_'.WPSS_HASH;
	if( empty( $_SESSION[$key_comment_acc] ) ) 		{ $_SESSION[$key_comment_acc] = 0; }
	if( empty( $_SESSION[$key_comment_den] ) ) 		{ $_SESSION[$key_comment_den] = 0; }
	if( empty( $_SESSION[$key_comment_den_jsck] ) )	{ $_SESSION[$key_comment_den_jsck] = 0; }
	if( empty( $_SESSION[$key_comment_den_algo] ) )	{ $_SESSION[$key_comment_den_algo] = 0; }
	if( !empty( $line ) ) { $line .= ' '; }
	if( $status === 'r' ) {
		++$_SESSION[$key_comment_den];
		$_SESSION[$key_comment_stat_curr] = '[REJECTED '.$line.$wpss_datum.']';
		$error_type = rs_wpss_get_error_type( $error_code ); /* 1.8.9.6 */
		rs_wpss_increment_count( $error_type ); /* 1.8 */
	} elseif( $status === 'a' ) {
		++$_SESSION[$key_comment_acc];
		$_SESSION[$key_comment_stat_curr] = '[ACCEPTED '.$line.$wpss_datum.']';
		$_SESSION['user_spamshield_count_'.WPSS_HASH] = 0; /* 1.8 */
		$_SESSION['user_spamshield_count_jsck_'.WPSS_HASH] = 0; /* 1.8.9.6 */
		$_SESSION['user_spamshield_count_algo_'.WPSS_HASH] = 0;
	} else { $_SESSION[$key_comment_stat_curr] = '[ERROR '.$line.' '.$wpss_datum.']'; }
	if( strpos( WPSS_SERVER_NAME_REV, WPSS_DEBUG_SERVER_NAME_REV ) !== 0 ) { $_SESSION[$key_comment_stat_curr] = ''; }
	$wpss_comment_author 		= $commentdata['comment_author'];
	$wpss_comment_author_email	= $commentdata['comment_author_email'];
	$wpss_comment_author_url 	= $commentdata['comment_author_url'];
	if( empty ( $wpss_comment_author ) )		{ $wpss_comment_author 			= ''; }
	if( empty ( $wpss_comment_author_email ) )	{ $wpss_comment_author_email	= ''; }
	if( empty ( $wpss_comment_author_url ) )	{ $wpss_comment_author_url 		= ''; }
	$_SESSION['wpss_comment_author_'.WPSS_HASH] = $wpss_comment_author;
	if( empty( $_SESSION[$key_auth_hist] ) ) { $_SESSION[$key_auth_hist] = array(); }
	$_SESSION[$key_auth_hist][] = $wpss_comment_author;
	$_SESSION['wpss_comment_author_email_'.WPSS_HASH] = $wpss_comment_author_email;
	if( empty( $_SESSION[$key_email_hist] ) ) { $_SESSION[$key_email_hist] = array(); }
	$_SESSION[$key_email_hist][] = $wpss_comment_author_email;
	$_SESSION['wpss_comment_author_url_'.WPSS_HASH] = $wpss_comment_author_url;
	if( empty( $_SESSION[$key_auth_url_hist] ) ) { $_SESSION[$key_auth_url_hist] = array(); }
	$_SESSION[$key_auth_url_hist][] = $wpss_comment_author_url;
	/**
	 *  if( strpos( WPSS_SERVER_NAME_REV, WPSS_DEBUG_SERVER_NAME_REV ) === 0 ) { $_SESSION['wpss_commentdata_'.WPSS_HASH] = $commentdata; }
	 *  To pass the $commentdata values through SESSION vars to denied_post functions because WP hook won't allow us to.
	 */
	$_SESSION['wpss_commentdata_'.WPSS_HASH] = $commentdata;
}

function rs_wpss_contact_shortcode( $atts = array(), $content = NULL ) {
	/* Implementation: [spamshieldcontact] */
	if( rs_wpss_is_admin_sproc() ) { return NULL; }
	if( is_page() && in_the_loop() && is_main_query() && ( !is_home() && !is_feed() && !is_archive() && !rs_wpss_is_search() && !rs_wpss_is_404() ) ) {
		$content = ''; $atts = array(); $shortcode_check = 'shortcode';
		$content_new_shortcode = rs_wpss_contact_form( $content, $atts, $shortcode_check );
		return $content_new_shortcode;
	}
	return NULL;
}

function rs_wpss_contact_form( $content = NULL, $atts = array(), $shortcode_check = NULL ) {
	/**
	 *  The Contact Form
	 *
	 *  Function can be used as either a filter or shortcode. If shortcode, use rs_wpss_contact_shortcode() as wrapper.
	 *  $shortcode_check: determines if function is being used as filter or shortcode - 'shortcode' | NULL (filter)
	 *
	 */
	/* extract( $atts ); */

	/* If not called from shortcode, and $content is empty, or admin process, then do nothing and return $content unfiltered */
	if( ( empty( $shortcode_check ) && empty( $content ) ) || rs_wpss_is_admin_sproc() ) { return $content; }
	$contact_repl_text = array( '<!--spamshield-contact-->', '<!--spamfree-contact-->' ); /* Legacy method */

	/* If not called from shortcode, and not called from legacy method, then do nothing and return $content unfiltered */
	if( empty( $shortcode_check ) && FALSE === strpos( $content, $contact_repl_text[0] ) && FALSE === strpos( $content, $contact_repl_text[1] ) ) { return $content; }

	/* If not called from appropriate section of code, then do nothing and return $content unfiltered */
	if( !is_page() || !in_the_loop() && !is_main_query() || is_home() || is_feed() || is_archive() || rs_wpss_is_search() || rs_wpss_is_404() ) { return $content; }

	$server_name 					= WPSS_SERVER_NAME;
	$email_domain					= rs_wpss_get_email_domain( $server_name );
	$wpss_contact_sender_email		= 'wpspamshield.noreply@'.$email_domain;
	$wpss_contact_sender_name		= __( 'Contact Form', 'wp-spamshield' );

	/* IP / PROXY INFO - BEGIN */
	global $wpss_ip_proxy_info;
	if( empty( $wpss_ip_proxy_info ) ) { $wpss_ip_proxy_info = rs_wpss_ip_proxy_info(); }
	extract( $wpss_ip_proxy_info );
	/* IP / PROXY INFO - END */

	$user_agent 					= rs_wpss_get_user_agent( TRUE, FALSE );
	$user_agent_lc 					= rs_wpss_casetrans('lower',$user_agent);
	$user_agent_lc_word_count 		= rs_wpss_count_words($user_agent_lc);
	$http_accept 					= rs_wpss_get_http_accept( TRUE, FALSE );
	$http_accept_lc					= rs_wpss_casetrans('lower',$http_accept);
	$http_accept_language			= rs_wpss_get_http_accept( TRUE, FALSE, TRUE );
	$http_accept_language_lc		= rs_wpss_casetrans('lower',$http_accept_language);
	$http_accept_encoding			= rs_wpss_get_http_accept( TRUE, FALSE, FALSE, TRUE );
	$http_accept_encoding_lc		= rs_wpss_casetrans('lower',$http_accept_encoding);
	$cf_url							= WPSS_THIS_URL;
	$cf_baseurl						= remove_query_arg( array( 'form', ), $cf_url );
	$cf_action_url					= esc_url( add_query_arg( array( 'form' => 'response', ), $cf_url ) );
	/* Detect Incapsula, and disable rs_wpss_ubl_cache - 1.8.9.6 */
	if( strpos( $reverse_dns_lc, '.ip.incapdns.net' ) !== FALSE ) { update_option( 'spamshield_ubl_cache_disable', TRUE ); }
	$get_form 			= !empty( $_GET['form'] ) ? $_GET['form'] : '';
	$post_jsonst 		= !empty( $_POST[WPSS_JSONST] ) ? trim( $_POST[WPSS_JSONST] ) : '';
	$post_ref2xjs 		= !empty( $_POST[WPSS_REF2XJS] ) ? trim( $_POST[WPSS_REF2XJS] ) : '';
	$post_jsonst_lc 	= rs_wpss_casetrans( 'lower', $post_jsonst );
	$post_ref2xjs_lc	= rs_wpss_casetrans( 'lower', $post_ref2xjs );
	$ref2xjs_lc			= rs_wpss_casetrans( 'lower', WPSS_REF2XJS );
	$wpss_error_code 	= $cf_content = '';
	if( is_page() && in_the_loop() && is_main_query() && ( !is_home() && !is_feed() && !is_archive() && !rs_wpss_is_search() && !rs_wpss_is_404() ) ) { /* Modified 1.7.7, 1.9.5.6, 1.9.9.1 */
		/* MAKE SURE WE ONLY SHOW THE FORM IN THE RIGHT PLACE */
		global $spamshield_options; if( empty( $spamshield_options ) ) { $spamshield_options = get_option('spamshield_options'); }
		extract( $spamshield_options );
		$wpss_ck_key_bypass 				= $wpss_js_key_bypass = FALSE;
		$wpss_key_values 					= rs_wpss_get_key_values();	extract( $wpss_key_values );
		$wpss_jsck_cookie_val				= !empty( $_COOKIE[$wpss_ck_key] )	? $_COOKIE[$wpss_ck_key]	: '';
		$wpss_jsck_field_val				= !empty( $_POST[$wpss_js_key] )	? $_POST[$wpss_js_key]		: '';
		$wpss_jsck_jquery_val				= !empty( $_POST[$wpss_jq_key] )	? $_POST[$wpss_jq_key]		: '';
		$form_response_thank_you_message	= trim( stripslashes( $spamshield_options['form_response_thank_you_message']) );
		$form_require_website_sess_ovr		= 0; /* SESSION Override - Added 1.7.8 */
		if( rs_wpss_is_session_active() ) { 
			if( !empty( $_SESSION['form_require_website_'.WPSS_HASH] ) ) { $form_require_website_sess_ovr = 1; } else { $_SESSION['form_require_website_'.WPSS_HASH] = 0; }
		}
		if( empty( $form_require_website ) && !empty( $form_require_website_sess_ovr ) ) { $form_require_website = 1; }
		$form_include = array(
			'website'	=> array( 'i' => $form_include_website,	'r' => $form_require_website,	),
			'phone'		=> array( 'i' => $form_include_phone,	'r' => $form_require_phone,		),
			'company'	=> array( 'i' => $form_include_company,	'r' => $form_require_company,	),
			);
		$form_drop_down_menu_item			= array( '', $form_drop_down_menu_item_1, $form_drop_down_menu_item_2, $form_drop_down_menu_item_3, $form_drop_down_menu_item_4, $form_drop_down_menu_item_5, $form_drop_down_menu_item_6, $form_drop_down_menu_item_7, $form_drop_down_menu_item_8, $form_drop_down_menu_item_9, $form_drop_down_menu_item_10 );
		$form_message_width					= ( !empty( $form_message_width )		&& is_int( $form_message_width )		&& $form_message_width >= 40 )		? $form_message_width		: 40;
		$form_message_height				= ( !empty( $form_message_height )		&& is_int( $form_message_height )		&& $form_message_height >= 5 )		? $form_message_height		: 10;
		$form_message_min_length			= ( !empty( $form_message_min_length )	&& is_int( $form_message_min_length )	&& $form_message_min_length >= 15 )	? $form_message_min_length	: 25;
		$form_message_max_length			= 25600; /* 25kb */

		if( $get_form === 'response' && ( 'POST' !== $_SERVER['REQUEST_METHOD'] || empty($_POST) ) ) {
			/**
			 *  1 - PRE-CHECK FOR BLANK FORMS
			 *  REQUEST_METHOD not POST, or empty $_POST - Not a legitimate contact form submission - likely a bot scraping/spamming the site, or wrong content-type
			 *  @since v 1.5.5 to conserve server resources
			 */
			$error_txt = rs_wpss_error_txt();
			$wpss_error = $error_txt.':';
			$cf_content = '<p><strong>'.$wpss_error.' ' . __( 'Please return to the contact form and fill out all required fields.', 'wp-spamshield' ) . '</strong></p><p>&nbsp;</p>'.WPSS_EOL;
			$content_new = str_replace($content, $cf_content, $content);
			$content_shortcode = $cf_content;
		} elseif( $get_form === 'response' ) {
			/**
			 *  2 - RESPONSE PAGE - FORM HAS BEEN SUBMITTED
			 *  CONTACT FORM BACK END - BEGIN
			 */
			global $wpss_contact_inprog; $wpss_contact_inprog = TRUE;
			$wpss_whitelist = $wp_blacklist = $message_spam = $blank_field = $invalid_value = $restricted_url = $restricted_email = $bad_email = $bad_phone = $bad_company = $message_short = $message_long = $cf_jsck_error = $cf_badrobot_error = $cf_spam_loc = $cf_domain_spam_loc = $generic_spam_company = $free_email_address = 0;
			$combo_spam_signal_1 = $combo_spam_signal_2  = $combo_spam_signal_3 = $bad_phone_spammer = 0;
			$wpss_user_blacklisted_prior_cf = 0;
			/* TO DO: Add here */

			/* PROCESSING CONTACT FORM - BEGIN */
			$wpss_contact_name = $wpss_contact_email = $wpss_contact_website = $wpss_contact_phone = $wpss_contact_company = $wpss_contact_drop_down_menu = $wpss_contact_subject = $wpss_contact_message = $wpss_raw_contact_message = '';

			$wpss_contact_time	= microtime(TRUE);
			$cf_author_data		= array();

			if( strpos( WPSS_SERVER_NAME_REV, WPSS_DEBUG_SERVER_NAME_REV ) === 0 ) {
				global $wpss_geolocation; if( empty ( $wpss_geolocation ) ) { $wpss_geolocation = rs_wpss_wf_geoiploc( $ip, TRUE ); }
			} else {
				global $wpss_geoloc_short; if( empty ( $wpss_geoloc_short ) ) { $wpss_geoloc_short = rs_wpss_wf_geoiploc_short( $ip ); }
			}

			if( !empty( $_POST['wpss_contact_name'] ) ) {
				$wpss_contact_name 				= rs_wpss_prep_cf_email(stripslashes(sanitize_text_field($_POST['wpss_contact_name'])));
			}
			if( !empty( $_POST['wpss_contact_email'] ) ) {
				$wpss_contact_email 			= sanitize_email($_POST['wpss_contact_email']);
			}
			$wpss_contact_email_lc 				= rs_wpss_casetrans( 'lower', $wpss_contact_email );
			$wpss_contact_email_lc_rev 			= strrev( $wpss_contact_email_lc );
			if( !empty( $_POST['wpss_contact_website'] ) ) {
				$wpss_contact_website 			= esc_url_raw($_POST['wpss_contact_website']);
			}
			$wpss_contact_website_lc 			= rs_wpss_casetrans( 'lower', $wpss_contact_website );
			$wpss_contact_domain 				= rs_wpss_get_domain( $wpss_contact_website_lc );
			$wpss_contact_domain_rev 			= strrev( $wpss_contact_domain );
			if( !empty( $_POST['wpss_contact_phone'] ) ) {
				$wpss_contact_phone 			= sanitize_text_field($_POST['wpss_contact_phone']);
			}
			if( !empty( $_POST['wpss_contact_company'] ) ) {
				$wpss_contact_company 			= rs_wpss_prep_cf_email(stripslashes(sanitize_text_field($_POST['wpss_contact_company'])));
			}
			$wpss_contact_company_lc			= rs_wpss_casetrans( 'lower', $wpss_contact_company );
			$wpss_common_spam_countries			= array('india','china','russia','ukraine','pakistan','turkey'); /* Most common sources of human spam */
			$wpss_common_spam_ccodes			= array('IN','CN','RU','UA','PK','TR');
			$wpss_contact_company_lc_nc			= trim( str_replace( $wpss_common_spam_countries, '', $wpss_contact_company_lc ) ); /* Remove country names for testing */

			if( !empty( $_POST['wpss_contact_drop_down_menu'] ) ) {
				$wpss_contact_drop_down_menu	= sanitize_text_field($_POST['wpss_contact_drop_down_menu']);
			}
			if( !empty( $_POST['wpss_contact_subject'] ) ) {
				$wpss_contact_subject 			= rs_wpss_prep_cf_email(stripslashes(sanitize_text_field($_POST['wpss_contact_subject'])));
			}
			$wpss_contact_subject_lc 			= rs_wpss_casetrans( 'lower', $wpss_contact_subject );
			if( !empty( $_POST['wpss_contact_message'] ) ) {
				$wpss_contact_message 			= stripslashes(rs_wpss_sanitize_textarea($_POST['wpss_contact_message']));/* body_content */
				$wpss_raw_contact_message 		= trim($_POST['wpss_contact_message']);/* body_content_unsan */
			}
			$wpss_contact_message_lc 			= rs_wpss_casetrans('lower',$wpss_contact_message); /* body_content_lc */
			$wpss_raw_contact_message_lc 		= rs_wpss_casetrans('lower',$wpss_raw_contact_message);
			$wpss_raw_contact_message_lc_deslashed	= stripslashes($wpss_raw_contact_message_lc);
			$wpss_contact_extracted_urls 		= rs_wpss_parse_links( $wpss_raw_contact_message_lc_deslashed, 'url' ); /* Parse message content for all URLs */
			$wpss_contact_num_links 			= count( $wpss_contact_extracted_urls ); /* Count extracted URLS from body content - Added 1.8.4 */
			$wpss_contact_num_limit				= 10; /* Max number of links in message body content */

			$message_length						= rs_wpss_strlen($wpss_contact_message);
			$cf_author_data['body_content_len']	= $message_length;

			$cf_author_data['comment_author']		= $wpss_contact_name;
			$cf_author_data['comment_author_email']	= $wpss_contact_email_lc;
			$cf_author_data['comment_author_url']	= $wpss_contact_website_lc;

			$wpss_contact_id_str 			= $wpss_contact_email_lc.'_'.$ip.'_'.$wpss_contact_time; /* Email/IP/Time */
			$wpss_contact_id_hash 			= rs_wpss_md5( $wpss_contact_id_str );
			$key_contact_status				= 'contact_status_'.$wpss_contact_id_hash;

			/* Update Session Vars */
			$key_comment_auth 				= 'comment_author_'.WPSS_HASH;
			$key_comment_email				= 'comment_author_email_'.WPSS_HASH;
			$key_comment_url				= 'comment_author_url_'.WPSS_HASH;
			$_SESSION[$key_comment_auth] 	= $wpss_contact_name;
			$_SESSION[$key_comment_email]	= $wpss_contact_email_lc;
			$_SESSION[$key_comment_url] 	= $wpss_contact_website_lc;
			$_SESSION[$key_contact_status] 	= 'INITIATED';

			/* Add New Tests for Logging - BEGIN */
			if( !empty( $post_ref2xjs ) ) {
				$ref2xJS = rs_wpss_casetrans( 'lower', addslashes( urldecode( $post_ref2xjs ) ) );
				$ref2xJS = str_replace( '%3a', ':', $ref2xJS );
				$ref2xJS = str_replace( ' ', '+', $ref2xJS );
				$wpss_javascript_page_referrer = esc_url_raw( $ref2xJS );
			} else { $wpss_javascript_page_referrer = '[None]'; }

			if( $post_jsonst_lc === 'ns1' || $post_jsonst_lc === 'ns2' || $post_jsonst_lc === 'ns3' || $post_jsonst_lc === 'ns4' || $post_jsonst_lc === 'ns5' ) { $wpss_jsonst = $post_jsonst; } else { $wpss_jsonst = '[None]'; }

			$cf_author_data['javascript_page_referrer']	= $wpss_javascript_page_referrer;
			$cf_author_data['jsonst']					= $wpss_jsonst;

			unset( $wpss_javascript_page_referrer, $wpss_jsonst );

			/* Add New Tests for Logging - END */

			/* PROCESSING CONTACT FORM - END */

			/* FORM INFO - BEGIN */

			if( !empty( $form_message_recipient ) && is_email( $form_message_recipient ) ) {
				$wpss_contact_form_to			= $form_message_recipient;
			} else {
				$wpss_contact_form_to 			= get_option('admin_email');
			}
			$wpss_contact_form_subject 			= '[' . __( 'Website Contact', 'wp-spamshield' ) . '] '.$wpss_contact_subject;
			$wpss_contact_form_msg_headers 		= "From: $wpss_contact_sender_name <$wpss_contact_sender_email>"."\r\n". "Reply-To: $wpss_contact_name <$wpss_contact_email_lc>"."\r\n". "Content-Type: text/plain\r\n";
			$wpss_contact_form_blog				= WPSS_SITE_URL;
			/* Another option: "Content-Type: text/html" */

			/* FORM INFO - END */

			/* TEST TO PREVENT CONTACT FORM SPAM - BEGIN */

			/* Check if user is blacklisted prior to submitting contact form */
			if( rs_wpss_ubl_cache() ) {
				$wpss_user_blacklisted_prior_cf = 1;
			}

			/* TESTING CONTACT FORM SUBMISSION FOR SPAM - BEGIN */

			/* JS/CK Tests - BEGIN */
			if( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) ) { /* 1.9.1 */
				$wpss_ck_key_bypass = TRUE;
			}
			if( FALSE === $wpss_ck_key_bypass ) {
				if( $wpss_jsck_cookie_val !== $wpss_ck_val ) {
					$wpss_error_code .= ' CF-COOKIE-2';
					$cf_jsck_error = TRUE;
				}
			}
			if( FALSE === $wpss_js_key_bypass ) { /* 1.8.9 */
				if( $wpss_jsck_field_val !== $wpss_js_val ) {
					$wpss_error_code .= ' CF-FVFJS-2';
					$cf_jsck_error = TRUE;
				}
			}
			if( $post_jsonst_lc === 'ns1' || $post_jsonst_lc === 'ns2' || $post_jsonst_lc === 'ns3' || $post_jsonst_lc === 'ns4' || $post_jsonst_lc === 'ns5' ) {
				$wpss_error_code .= ' CF-JSONST-1000-2';
				$cf_jsck_error = TRUE;
			}
			/* JS/CK Tests - END */

			/**
			 *  WPSS Whitelist Check - BEGIN
			 *  Test WPSS Whitelist if option set
			 */
			if( !empty( $spamshield_options['enable_whitelist'] ) && empty( $wpss_error_code ) && rs_wpss_whitelist_check( $wpss_contact_email_lc ) ) { $wpss_whitelist = 1; }
			/* WPSS Whitelist Check - END */

			/* TO DO: REWORK SO THAT IF FAILS COOKIE TEST, TESTS ARE COMPLETE */

			/* ERROR CHECKING */
			$cf_blacklist_status = $contact_response_status_message_addendum = '';
			/* TO DO: Switch this *old* code to REGEX & foreach() using Magic Parser */
			$cf_spam_1_count	= rs_wpss_substr_count( $wpss_contact_message_lc, 'link');
			$cf_spam_1_limit	= 7;
			$cf_spam_2_count	= rs_wpss_substr_count( $wpss_contact_message_lc, 'link building');
			$cf_spam_2_limit	= 3;
			$cf_spam_3_count	= rs_wpss_substr_count( $wpss_contact_message_lc, 'link exchange');
			$cf_spam_3_limit	= 2;
			$cf_spam_4_count	= rs_wpss_substr_count( $wpss_contact_message_lc, 'link request');
			$cf_spam_4_limit	= 1;
			$cf_spam_5_count	= rs_wpss_substr_count( $wpss_contact_message_lc, 'link building service');
			$cf_spam_5_limit	= 2;
			$cf_spam_6_count	= rs_wpss_substr_count( $wpss_contact_message_lc, 'link building experts india');
			$cf_spam_6_limit	= 0;
			$cf_spam_7_count	= rs_wpss_substr_count( $wpss_contact_message_lc, 'india');
			$cf_spam_7_limit	= 1;
			$cf_spam_8_count	= rs_wpss_substr_count( $wpss_contact_message_lc, 'can you outsource some seo business to us? we will work according to you and your clients and for a long term relationship we can start our SEO services in only $99 per month per website. looking forward for your positive reply');
			$cf_spam_8_limit	= 0;
			$cf_spam_9_count	= rs_wpss_substr_count( $wpss_contact_message_lc, 'can you outsource some seo business to us');
			$cf_spam_9_limit	= 0;
			$cf_spam_10_count	= rs_wpss_substr_count( $wpss_contact_message_lc, 'outsource some seo business');
			$cf_spam_10_limit	= 0;
			$cf_spam_11_count	= rs_wpss_substr_count( $wpss_contact_message_lc, 'traffic exchange');
			$cf_spam_11_limit	= 1;

			/* Check if Subject seems spammy */
			$subject_blacklisted_count = 0;
			$cf_spam_subj_arr = array( 'link request', 'link exchange', 'seo service $99 per month', 'seo services $99 per month', 'seo services @ $99 per month', 'partnership with offshore development center', );
			$cf_spam_subj_arr_regex = rs_wpss_get_regex_phrase( $cf_spam_subj_arr,'','red_str' );
			if( preg_match( $cf_spam_subj_arr_regex, $wpss_contact_subject_lc ) ) { $subject_blacklisted = TRUE; $subject_blacklisted_count = 1; } else { $subject_blacklisted = FALSE; }

			/* Check if Content seems spammy */
			if( rs_wpss_cf_content_blacklist_chk( $wpss_contact_message_lc ) ) {
				$content_blacklisted = TRUE;
				$wpss_error_code .= ' CF-10400C-BL';
			} else { $content_blacklisted = FALSE; }
			/* Check if Name is Keyword Spam */
			if( empty( $wpss_whitelist ) && rs_wpss_anchortxt_blacklist_chk( $wpss_contact_name ) ) {
				$name_blacklisted = TRUE;
				$wpss_error_code .= ' CF-10500A-BL';
			} else { $name_blacklisted = FALSE; }
			/* Check if email is blacklisted */
			if( empty( $wpss_whitelist ) && rs_wpss_email_blacklist_chk( $wpss_contact_email_lc ) ) {
				$email_blacklisted = TRUE;
				$wpss_error_code .= ' CF-9200E-BL';
			} else { $email_blacklisted = FALSE; }
			/* Website - Check if domain is blacklisted */
			if( empty( $wpss_whitelist ) && rs_wpss_domain_blacklist_chk( $wpss_contact_domain ) ) {
				$domain_blacklisted = TRUE;
				$wpss_error_code .= ' CF-10500AU-BL';
			} else { $domain_blacklisted = FALSE; }
			/* Website - URL Shortener Check - Added in 1.3.8 */
			if( empty( $wpss_whitelist ) && rs_wpss_urlshort_blacklist_chk( $wpss_contact_website_lc ) ) {
				$website_shortened_url = TRUE;
				$wpss_error_code .= ' CF-10501AU-BL';
			} else { $website_shortened_url = FALSE; }
			/* Website - Excessively Long URL Check (Obfuscated & Exploit) - Added in 1.3.8 */
			if( empty( $wpss_whitelist ) && rs_wpss_long_url_chk( $wpss_contact_website_lc ) ) {
				$website_long_url = TRUE;
				$wpss_error_code .= ' CF-10502AU-BL';
			} else { $website_long_url = FALSE; }
			/**
			 *  Spam URL Check -  Check for URL Shorteners, Bogus Long URLs, and Misc Spam Domains
				if( empty( $wpss_whitelist ) && rs_wpss_at_link_spam_url_chk( $wpss_contact_website_lc ) ) {
					$website_spam_url = TRUE;
					$wpss_error_code .= ' CF-10510AU-BL';
				} else { $website_spam_url = FALSE; }
			 */

			/* Add Misc Spam URLs next... */

			/* Check Website URL for Exploits - Ignores Whitelist */
			if( rs_wpss_exploit_url_chk( $wpss_contact_website_lc ) ) {
				$website_exploit_url = TRUE;
				$wpss_error_code .= ' CF-15000AU-XPL'; /* Added in 1.4 */
			} else { $website_exploit_url = FALSE; }

			/* Body Content - Check for excessive number of links in message ( body_content ) - Added 1.8.4 */
			if( empty( $wpss_whitelist ) && $wpss_contact_num_links > $wpss_contact_num_limit ) {
				$content_excess_links = TRUE;
				$wpss_error_code .= ' CF-1-HT';
			} else { $content_excess_links = FALSE; }

			/* Body Content - Parse URLs and check for URL Shortener Links - Added in 1.3.8 */
			if( empty( $wpss_whitelist ) && rs_wpss_cf_link_spam_url_chk( $wpss_raw_contact_message_lc_deslashed, $wpss_contact_email_lc ) ) {
				$content_shortened_url = TRUE;
				$wpss_error_code .= ' CF-10530CU-BL';
			} else { $content_shortened_url = FALSE; }

			/* Check all URL's in Body Content for Exploits - Ignores Whitelist */
			if( rs_wpss_exploit_url_chk( $wpss_contact_extracted_urls ) ) {
				$content_exploit_url = TRUE;
				$wpss_error_code .= ' CF-15000CU-XPL'; /* Added in 1.4 */
			} else { $content_exploit_url = FALSE; }

			/*  Check if Contact Form Content Contains *Only* URLs - Ignores Whitelist */
			if( !empty( $wpss_contact_extracted_urls ) && is_array( $wpss_contact_extracted_urls ) ) {
				$contact_message_temp = str_replace( $wpss_contact_extracted_urls, '', $wpss_raw_contact_message_lc_deslashed );
				$contact_message_temp = trim( str_replace( array( "\s", WPSS_EOL, "\t", ' ', ), '', $contact_message_temp ) );
				if( empty( $contact_message_temp ) ) {
					$message_spam = 1;
					$wpss_error_code .= ' 10410CU'; /* Added in 1.9.8.2 */
				}
			}

			$cf_spam_term_total = $cf_spam_1_count + $cf_spam_2_count + $cf_spam_3_count + $cf_spam_4_count + $cf_spam_7_count + $cf_spam_10_count + $cf_spam_11_count + $subject_blacklisted_count;
			$cf_spam_term_total_limit = 15;

			if( strpos( $reverse_dns_lc_rev, 'ni.' ) === 0 || strpos( $reverse_dns_lc_rev, 'ur.' ) === 0 || strpos( $reverse_dns_lc_rev, 'kp.' ) === 0 || strpos( $reverse_dns_lc_rev, 'nc.' ) === 0 || strpos( $reverse_dns_lc_rev, 'au.' ) === 0 || strpos( $reverse_dns_lc_rev, 'rt.' ) === 0 || preg_match( "~^1\.22\.2(19|20|23)\.~", $ip ) || strpos( $reverse_dns_lc_rev, '.aidni-tenecap.' ) ) {
				$cf_spam_loc = 1;
				/* TO DO: Add more, switch to Regex */
			} elseif( strpos( $wpss_contact_email_lc_rev , 'ni.' ) === 0 || strpos( $wpss_contact_email_lc_rev , 'ur.' ) === 0 || strpos( $wpss_contact_email_lc_rev , 'kp.' ) === 0 || strpos( $wpss_contact_email_lc_rev , 'nc.' ) === 0 || strpos( $wpss_contact_email_lc_rev , 'au.' ) === 0 || strpos( $wpss_contact_email_lc_rev , 'rt.' ) === 0 ) {
				$cf_spam_loc = 2;
				/* TO DO: Add more, switch to Regex */
			} elseif( strpos( $wpss_contact_domain_rev , 'ni.' ) === 0 || strpos( $wpss_contact_domain_rev , 'ur.' ) === 0 || strpos( $wpss_contact_domain_rev , 'kp.' ) === 0 || strpos( $wpss_contact_domain_rev , 'nc.' ) === 0 || strpos( $wpss_contact_domain_rev , 'au.' ) === 0 || strpos( $wpss_contact_domain_rev , 'rt.' ) === 0 ) {
				$cf_spam_loc = 3;
				/* TO DO: Add more, switch to Regex */
			} else {
				global $wpss_geoiploc_data; if( empty ( $wpss_geoiploc_data ) ) { $wpss_geoiploc_data = rs_wpss_wf_geoiploc( $ip ); }
				if( !empty ( $wpss_geoiploc_data ) ) { extract( $wpss_geoiploc_data ); }
				if( !empty( $countryCode ) && in_array( $countryCode, $wpss_common_spam_ccodes, TRUE ) ) {
					$cf_spam_loc = 4;
					/* TO DO: Add more, switch to Regex */
				}
			}
			if( strpos( WPSS_SERVER_NAME_REV, 'ni.' ) === 0 || strpos( WPSS_SERVER_NAME_REV, 'ur.' ) === 0 || strpos( WPSS_SERVER_NAME_REV, 'kp.' ) === 0 || strpos( WPSS_SERVER_NAME_REV, 'nc.' ) === 0 || strpos( WPSS_SERVER_NAME_REV, 'au.' ) === 0 || strpos( WPSS_SERVER_NAME_REV, 'rt.' ) === 0 ) {
				$cf_domain_spam_loc = 1;
				/* TO DO: Add more, switch to Regex */
			}
			if( !empty( $form_include_company ) && !empty( $wpss_contact_company_lc ) && preg_match( "~^(se(o|m)|(search\s*engine|internet|web)\s*(optimi[zs](a[tc]ion|ing|er)|market(ing|er))|it|informa[tc]ions?\s*tech?nolog(y|i[ea]?)|(se(o|m)|((search\s*engine|internet|web)\s*)?(optimi[zs](a[tc]ion|ing|er)|market(ing|er))|web\s*(design(er|ing)?|develop(ment|er|ing))|(content\s*|copy\s*)?(writ|right)(er?|ing)|it|informa[tc]ions?\s*tech?nolog(y|i[ea]?))s?\s*(comp(an|na)y|firm|services?|freelanc(er?|ing))|(comp(an|na)y|firm|services?|freelanc(er?|ing))\s*(se(o|m)|((search\s*engine|internet|web)\s*)?(optimi[zs](a[tc]ion|ing|er)|market(ing|er))|web\s*(design(er|ing)?|develop(ment|er|ing))|(content\s*|copy\s*)?(writ|right)(er?|ing)|it|informa[tc]ions?\s*tech?nolog(y|i[ea]?))s?)$~", $wpss_contact_company_lc_nc ) ) {
				$generic_spam_company = 1;
			}
			if( rs_wpss_is_free_email( $wpss_contact_email_lc ) ) { $free_email_address = 1; }

			/* Combo Tests - Pre */
			if( preg_match( "~((reply|email\s+us)\s+back\s+to\s+get\s+(a\s+)?full\s+proposal\.$|can\s+you\s+outsource\s+some\s+seo\s+business\s+to\s+us|humble\s+request\s+we\s+are\s+not\s+spammers\.|if\s+by\s+sending\s+this\s+email\s+we\s+have\s+made\s+(an\s+)?offense\s+to\s+you|if\s+you\s+are\s+not\s+interested\s+then\s+please\s+(do\s+)?reply\s+back\s+as|in\s+order\s+to\s+stop\s+receiving\s+(such\s+)?emails\s+from\s+us\s+in\s+(the\s+)?future\s+please\s+reply\s+with|if\s+you\s+do\s+not\s+wish\s+to\s+receive\s+further\s+emails\s+(kindly\s+)?reply\s+with)~", $wpss_contact_message_lc ) ) {
				$combo_spam_signal_1 = 1; 
			}
			if( preg_match( "~^(get|want)\s+more\s+(customer|client|visitor)s?\s+(and|\&|or)\s+(customer|client|visitor)s?\?+$~", $wpss_contact_subject_lc ) ) { $combo_spam_signal_2 = 1; }

			if( preg_match( "~(?:^|[,;\.\!\?\s]+)india(?:[,;\.\!\?\s]+|$)~", $wpss_contact_message_lc ) ) {
				preg_match_all( "~(?:^|[,;\.\!\?\s]+)(SEO)(?:[,;\.\!\?\s]+|$)~", $wpss_contact_message, $matches_raw, PREG_PATTERN_ORDER );
				$spam_signal_3_matches 			= $matches_raw[1]; /* Array containing matches parsed from haystack text ($wpss_contact_message) */
				$spam_signal_3_matches_count	= count( $spam_signal_3_matches );
				/* Changed from 7 to 2 occurrences - 1.6.2 */
				if( $spam_signal_3_matches_count > 1 ) { $combo_spam_signal_3 = 1; }
			}
			if( preg_match( "~^(01[2-9]){3}0$~", $wpss_contact_phone ) ) { $bad_phone_spammer = 1; }
			/* Combo Tests */
			if( empty( $wpss_whitelist ) && ( $cf_spam_term_total > $cf_spam_term_total_limit || $cf_spam_1_count > $cf_spam_1_limit || $cf_spam_2_count > $cf_spam_2_limit || $cf_spam_5_count > $cf_spam_5_limit || $cf_spam_6_count > $cf_spam_6_limit || $cf_spam_10_count > $cf_spam_10_limit ) && !empty( $cf_spam_loc ) ) {
				$message_spam = 1;
				$wpss_error_code .= ' CF-MSG-SPAM1';
				$contact_response_status_message_addendum .= '&bull; ' . __( 'Message appears to be spam.', 'wp-spamshield' ) . ' ' . __( 'Please note that link requests, link exchange requests, and SEO outsourcing requests will be automatically deleted, and are not an acceptable use of this contact form.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
			} elseif( empty( $wpss_whitelist ) && ( !empty( $subject_blacklisted ) || !empty( $content_blacklisted ) || $cf_spam_8_count > $cf_spam_8_limit || $cf_spam_9_count > $cf_spam_9_limit || $cf_spam_11_count > $cf_spam_11_limit || !empty( $name_blacklisted ) || !empty( $email_blacklisted ) || !empty( $domain_blacklisted ) || !empty( $website_shortened_url ) || !empty( $website_long_url ) || !empty( $website_exploit_url ) || !empty( $content_excess_links ) || !empty( $content_shortened_url ) || !empty( $content_exploit_url ) ) ) {
				$message_spam = 1;
				$wpss_error_code .= ' CF-MSG-SPAM2';
				$contact_response_status_message_addendum .= '&bull; ' . __( 'Message appears to be spam.', 'wp-spamshield' ) . ' ' . __( 'Please note that link requests, link exchange requests, and SEO outsourcing/offshoring spam will be automatically deleted, and are not an acceptable use of this contact form.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
			} elseif( empty( $wpss_whitelist ) && !empty( $cf_spam_loc ) && empty ( $cf_domain_spam_loc ) && !empty( $free_email_address ) && ( !empty( $generic_spam_company ) || !empty( $combo_spam_signal_1 ) || !empty( $combo_spam_signal_2 ) || !empty( $bad_phone_spammer ) ) ) {
				$message_spam = 1;
				$wpss_error_code .= ' CF-MSG-SPAM3';
				$contact_response_status_message_addendum .= '&bull; ' . __( 'Message appears to be spam.', 'wp-spamshield' ) . ' ' . __( 'Please note that link requests, link exchange requests, and SEO outsourcing/offshoring spam will be automatically deleted, and are not an acceptable use of this contact form.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
				/* Blacklist on failure - future attempts blocked */
				rs_wpss_ubl_cache( 'set' );
			} elseif( empty( $wpss_whitelist ) && !empty( $generic_spam_company ) && !empty( $combo_spam_signal_3 ) ) {
				$message_spam = 1;
				$wpss_error_code .= ' CF-MSG-SPAM4';
				$contact_response_status_message_addendum .= '&bull; ' . __( 'Message appears to be spam.', 'wp-spamshield' ) . ' ' . __( 'Please note that link requests, link exchange requests, and SEO outsourcing/offshoring spam will be automatically deleted, and are not an acceptable use of this contact form.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
				/* Blacklist on failure - future attempts blocked */
				rs_wpss_ubl_cache( 'set' );
			} elseif( empty( $wpss_whitelist ) && !empty( $generic_spam_company ) && !empty( $free_email_address ) ) {
				/* BOTH are odd as legit companies include their name and don't use free email */
				$message_spam = 1;
				$wpss_error_code .= ' CF-MSG-SPAM5';
				$contact_response_status_message_addendum .= '&bull; ' . __( 'Message appears to be spam.', 'wp-spamshield' ) . ' ' . __( 'Please note that link requests, link exchange requests, and SEO outsourcing/offshoring spam will be automatically deleted, and are not an acceptable use of this contact form.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
				/* Blacklist on failure - future attempts blocked */
				rs_wpss_ubl_cache( 'set' );
			}

			if( empty( $wpss_contact_name ) || empty( $wpss_contact_email ) || empty( $wpss_contact_subject ) || empty( $wpss_contact_message ) || ( !empty( $form_include_website ) && !empty( $form_require_website ) && empty( $wpss_contact_website ) ) || ( !empty( $form_include_phone ) && !empty( $form_require_phone ) && empty( $wpss_contact_phone ) ) || ( !empty( $form_include_company ) && !empty( $form_require_company ) && empty( $wpss_contact_company ) ) || ( !empty( $form_include_drop_down_menu ) && !empty( $form_drop_down_menu_title ) && !empty( $form_drop_down_menu_item_1 ) && !empty( $form_drop_down_menu_item_2 ) && empty( $wpss_contact_drop_down_menu ) ) ) {
				$blank_field=1;
				$wpss_error_code .= ' CF-BLANKFIELD';
				$contact_response_status_message_addendum .= '&bull; ' . __( 'At least one required field was left blank.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
			}
			if( strpos( WPSS_SERVER_NAME_REV, WPSS_DEBUG_SERVER_NAME_REV ) === 0 ) {
				if( $wpss_contact_domain === WPSS_SERVER_NAME && ( !rs_wpss_is_admin_ip( $ip ) || !empty( $cf_spam_loc ) ) ) {
					$invalid_value=1;
					$restricted_url=1;
					$wpss_error_code .= ' CF-RESTR-URL';
					/* TO DO: TRANSLATE */
					$contact_response_status_message_addendum .= '&bull; ' .  __( 'Please enter a valid website.', 'wp-spamshield' ) . ' ' . __( 'Please use <em>your</em> company website URL, not ours.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
					/**
					 *  Bump user spam count to 5
					 */
					if( empty( $_SESSION['user_spamshield_count_'.WPSS_HASH] ) || $_SESSION['user_spamshield_count_'.WPSS_HASH] < 5 ) {
						$_SESSION['user_spamshield_count_'.WPSS_HASH] = 5;
					}
				}
				$wpss_debug_server_rgx = rs_wpss_preg_quote( ltrim( WPSS_DEBUG_SERVER_NAME, '.' ) );
				if( preg_match( "~@$wpss_debug_server_rgx$~", $wpss_contact_email ) && ( !rs_wpss_is_admin_ip( $ip ) || !empty( $cf_spam_loc ) ) ) {
					$invalid_value=1;
					$restricted_email=1;
					$wpss_error_code .= ' CF-RESTR-EMAIL';
					/* TO DO: TRANSLATE */
					$contact_response_status_message_addendum .= '&bull; ' . __( 'Please enter a valid email address.' ) . ' ' . __( 'Please use <em>your</em> email address, not one of ours.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
					/**
					 *  Bump user spam count to 5
					 */
					if( empty( $_SESSION['user_spamshield_count_'.WPSS_HASH] ) || $_SESSION['user_spamshield_count_'.WPSS_HASH] < 5 ) {
						$_SESSION['user_spamshield_count_'.WPSS_HASH] = 5;
					}
				}
			}
			if( !is_email( $wpss_contact_email ) ) {
				$invalid_value=1;
				$bad_email=1;
				$wpss_error_code .= ' CF-INVAL-EMAIL';
				$contact_response_status_message_addendum .= '&bull; ' . __( 'Please enter a valid email address.' ) . '<br />&nbsp;<br />';
			}
			if( empty( $bad_email ) && !rs_wpss_email_domain_exists( $wpss_contact_email ) ) {
				$invalid_value=1;
				$bad_email=1;
				$wpss_error_code .= ' CF-INVAL-EMAIL-D';
				$contact_response_status_message_addendum .= '&bull; ' . __( 'Please enter a valid email address.' ) . '<br />&nbsp;<br />';
			}

			/* TO DO: RE-WORK THIS SECTION */
			$wpss_contact_phone_clean = preg_replace( "~[^0-9]+~", "", $wpss_contact_phone );
			$phone_length = rs_wpss_strlen($wpss_contact_phone_clean); /* Min = 5 */
			$phone_1c = isset( $wpss_contact_phone_clean[0] ) ? $wpss_contact_phone_clean[0] : '0';
			$wpss_contact_phone_zero = str_replace( array( '0120120120', '0130130130', '123456', ' ', $phone_1c, '-', '(', ')', '+', 'N/A', 'NA', 'n/a', 'na' ), '', $wpss_contact_phone );
			if( !empty( $form_require_phone ) && !empty( $form_include_phone ) && ( empty( $wpss_contact_phone_zero ) || !empty( $bad_phone_spammer ) || $phone_length < 5 || strpos( $wpss_contact_phone, '123456' ) === 0 || strpos( $wpss_contact_phone, '0123456' ) === 0 || strpos( $wpss_contact_phone, '1234567' ) !== FALSE ) ) {
				$invalid_value=1;
				$bad_phone=1;
				$wpss_error_code .= ' CF-INVAL-PHONE';
				$contact_response_status_message_addendum .= '&bull; ' . __( 'Please enter a valid phone number.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
			}
			$wpss_contact_company_zero = str_replace( array( ' ', '0', '-', '(', ')', '+', 'N/A', 'NA', 'n/a', 'na', ), '', $wpss_contact_company_lc );
			if( !empty( $form_require_company ) && !empty( $form_include_company ) && ( empty( $wpss_contact_company_zero ) || preg_match( "~(^https?\:/+|^(0+|compan(y|ie)|confidential|empty|expert|fuck([\s\.\-_]*(off|you))?|hack(er|ing)?|invalid|it|na|n/a|nada|negative|nein|no|none?|nothing|null?|nyet|private?|personal|restricted|secret|seo|unknown|void|web)$)~", $wpss_contact_company_lc ) ) ) {
				$invalid_value=1;
				$bad_company=1;
				$wpss_error_code .= ' CF-INVAL-COMPANY';
				$contact_response_status_message_addendum .= '&bull; ' . __( 'Please enter a valid company.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
			}
			/* Spammers using one of Google's official domains as their URL */
			if( !empty( $form_include_website ) && ( !empty( $generic_spam_company ) && strpos( $reverse_dns_lc, 'google' ) === FALSE  && strpos( $reverse_dns_lc, 'blogger' ) === FALSE && !rs_wpss_is_google_ip($ip) ) && rs_wpss_is_google_domain($wpss_contact_domain) ) {
				$invalid_value=1;
				$bad_website=1;
				$wpss_error_code .= ' CF-INVAL-URL-G';
				/* TO DO: TRANSLATE */
				$contact_response_status_message_addendum .= '&bull; ' . __( 'Please enter a valid website.', 'wp-spamshield' ) . ' ' . __( 'Please use <em>your</em> company website URL, not Google\'s.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
				/**
				 *  The only reason we're even putting up with these fools is to honeypot them.
				 *  Also, now makes website field required temporarily for this SESSION.
				 */
				$_SESSION['form_require_website_'.WPSS_HASH] = 1;
			}
			if( !empty( $form_include_website ) && !empty( $wpss_contact_website ) && ( !rs_wpss_is_valid_url( $wpss_contact_website_lc ) || $cf_baseurl === $wpss_contact_website_lc ) ) {
				$invalid_value=1;
				$bad_website=1;
				$wpss_error_code .= ' CF-INVAL-URL';
				/* TO DO: TRANSLATE */
				$contact_response_status_message_addendum .= '&bull; ' . __( 'Please enter a valid website.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
				/**
				 *  Now makes website field required temporarily for this SESSION. Honeypot spammers.
				 */
				$_SESSION['form_require_website_'.WPSS_HASH] = 1;
			}
			if( empty( $bad_website ) && !empty( $form_include_website ) && !empty( $wpss_contact_website ) && !rs_wpss_domain_exists( $wpss_contact_domain ) ) {
				$invalid_value=1;
				$bad_website=1;
				$wpss_error_code .= ' CF-INVAL-URL-D';
				/* TO DO: TRANSLATE */
				$contact_response_status_message_addendum .= '&bull; ' . __( 'Please enter a valid website.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
				/**
				 *  Now makes website field required temporarily for this SESSION. Honeypot spammers.
				 */
				$_SESSION['form_require_website_'.WPSS_HASH] = 1;
			}

			if( $message_length < $form_message_min_length ) {
				$message_short=1;
				$wpss_error_code .= ' CF-MSG-SHORT';
				$contact_response_status_message_addendum .= '&bull; ' . __( 'Message too short. Please enter a complete message.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
			}
			if( $message_length > $form_message_max_length ) {
				$message_long=1;
				$wpss_error_code .= ' CF-MSG-LONG';
				$contact_response_status_message_addendum .= '&bull; ' . __( 'Message too long. Please enter a shorter message.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
			}

			/**
			 *  BAD ROBOT TEST - BEGIN
			 *  This replaces previous CF-REF-2-1023 test and previous rs_wpss_revdns_filter() here.
			 */

			$bad_robot_filter_data 	 = rs_wpss_bad_robot_blacklist_chk( 'contact', '', $ip, $reverse_dns, $wpss_contact_name, $wpss_contact_email_lc );
			$cf_filter_status		 = $bad_robot_filter_data['status'];
			$bad_robot_blacklisted 	 = $bad_robot_filter_data['blacklisted'];

			if( !empty( $bad_robot_blacklisted ) ) {
				$message_spam 		 = 1;
				$wpss_error_code 	.= $bad_robot_filter_data['error_code'];
				$cf_badrobot_error 	 = TRUE;
				$cf_blacklist_status = '3'; /* Implement */
				$contact_response_status_message_addendum = '&bull; ' . __( 'Message appears to be spam.', 'wp-spamshield' ) . ' ' . __( 'Please note that link requests, link exchange requests, SEO outsourcing/offshoring spam, and automated contact form submissions will be automatically deleted, and are not an acceptable use of this contact form.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
			}
			/* BAD ROBOT TEST - END */


			/* Enhanced Comment Blacklist - CF-0-ECBL */
			if( empty( $wpss_whitelist ) && !empty( $spamshield_options['enhanced_comment_blacklist'] ) && empty( $wpss_error_code ) ) {
				if( rs_wpss_blacklist_check( '', $wpss_contact_email_lc, '', '', $ip, '', $reverse_dns_lc ) ) {
					$message_spam = 1; $wp_blacklist = 1;
					$wpss_error_code .= ' CF-0-ECBL';
					$contact_response_status_message_addendum = '&bull; ' . __( 'Your message has been blocked based on the website owner\'s blacklist settings.', 'wp-spamshield' ) . ' ' . __( 'If you feel this is in error, please contact the site owner by some other method.', 'wp-spamshield' );
					if( !empty( $cf_spam_loc ) && empty ( $cf_domain_spam_loc ) ) {
						$contact_response_status_message_addendum .= ' ' . __( 'Please note that link requests, link exchange requests, SEO outsourcing/offshoring spam, and automated contact form submissions will be automatically deleted, and are not an acceptable use of this contact form.', 'wp-spamshield' );
					}
					$contact_response_status_message_addendum .= '<br />&nbsp;<br />';
				}
			}

			/**
			 *  Custom Filter - CF-CUSTOM
			 *  Allows user to add a custom filter in another plugin, functions.php, etc
			 *  Only fires if no filters have been tripped yet.
			 *  @since 1.9.7.8
			 */
			if( empty( $wpss_whitelist ) && empty( $wpss_error_code ) ) {
				$contact_form_data = array(
					'status'	=> FALSE,	/* Haven't determined it to be spam yet... If custom filter trips, change to TRUE */
					'name'		=> $wpss_contact_name,
					'email'		=> $wpss_contact_email_lc,
					'url'		=> $wpss_contact_website_lc,
					'domain'	=> $wpss_contact_domain,
					'ip'		=> $ip,
					'revdns'	=> $reverse_dns_lc,
					'company'	=> $wpss_contact_company,
					'subject'	=> $wpss_contact_subject_lc,
					'ddmenu'	=> $wpss_contact_drop_down_menu,
					'message'	=> $wpss_contact_message_lc,
					'response'	=> __( 'That action is currently not allowed.' ), /* Change this to custom response message. */
				);
				$contact_form_data = apply_filters( 'wpss_cf_custom_filter_check', $contact_form_data );
				if( TRUE === $contact_form_data['status'] ) {
					$message_spam = 1;
					$wpss_error_code .= ' CF-CUSTOM';
					$contact_response_status_message_addendum = $contact_form_data['response'] . '<br />&nbsp;<br />';
				}
			}

			/**
			 *  FINAL TEST
			 *  TEST 0-POST - See if user has already been blacklisted this session (before submission of this form), or a previous session, included for cases where caching is active
			 */
			if( !empty( $wpss_user_blacklisted_prior_cf ) ) {
				/* User is blacklisted prior to submitting contact form */
				$message_spam = 1;
				$user_blacklisted = TRUE;
				$wpss_error_code .= ' CF-0-POST-BL';
				$cf_blacklist_status = '3'; /* Implement */
				rs_wpss_ubl_cache( 'set' );
				$contact_response_status_message_addendum = '&bull; ' . __( 'Contact form has been temporarily disabled to prevent spam. Please try again later.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
			} else { $user_blacklisted = FALSE; }

			/**
			 *  Track # of submissions this session
			 *  Must go after spam tests
			 */
			if( !isset( $_SESSION['wpss_cf_submissions_'.WPSS_HASH] ) ) { $_SESSION['wpss_cf_submissions_'.WPSS_HASH] = 1; } else { ++$_SESSION['wpss_cf_submissions_'.WPSS_HASH]; }

			/* TESTING SUBMISSION FOR SPAM - END */

			/* Sanitized versions for output */
			$wpss_contact_form_http_accept_encoding = $wpss_contact_form_http_accept_language = $wpss_contact_form_http_accept = $wpss_contact_form_http_referer = '';
			$wpss_contact_form_http_accept_encoding = rs_wpss_get_http_accept( FALSE, FALSE, FALSE, TRUE );
			$wpss_contact_form_http_accept_language = rs_wpss_get_http_accept( FALSE, FALSE, TRUE );
			$wpss_contact_form_http_accept 			= rs_wpss_get_http_accept();
			$wpss_contact_form_http_user_agent 		= rs_wpss_get_user_agent();
			$wpss_contact_form_http_browser 		= rs_wpss_get_browser();
			$wpss_contact_form_http_referer 		= rs_wpss_get_referrer( FALSE, TRUE, TRUE ); /* Initial referrer, aka "Referring Site" - Changed 1.7.9 */

			/* MESSAGE CONTENT - BEGIN */
			$wpss_contact_form_msg_1 = $wpss_contact_form_msg_2 = $wpss_contact_form_msg_3 = '';
			$wpss_contact_message_eml = str_replace( "\n", "\r\n", $wpss_contact_message );

			$wpss_contact_form_msg_1 .= __( 'Message', 'wp-spamshield' ) . ': '."\r\n";
			$wpss_contact_form_msg_1 .= $wpss_contact_message_eml."\r\n\r\n";
			$wpss_contact_form_msg_1 .= __( 'Name' ) . ': '.$wpss_contact_name."\r\n";
			$wpss_contact_form_msg_1 .= __( 'Email' ) . ': '.$wpss_contact_email_lc."\r\n";
			$form_include['phone']['d'] = $wpss_contact_phone; $form_include['company']['d'] = $wpss_contact_company; $form_include['website']['d'] = $wpss_contact_website_lc;
			foreach( $form_include as $k => $v ) {
				if( $k === 'website' ) { $text = __( 'Website' ); $type = 'url'; } else { $text = __( rs_wpss_casetrans( 'ucfirst', $k ), 'wp-spamshield' ); $type = 'text'; }
				if( !empty( $v['i'] ) ) { $wpss_contact_form_msg_1 .= $text.': '.$v['d']."\r\n"; }
			}
			if( !empty( $form_include_drop_down_menu ) && !empty( $form_drop_down_menu_title ) && !empty( $form_drop_down_menu_item_1 ) && !empty( $form_drop_down_menu_item_2 ) ) {
				$wpss_contact_form_msg_1 .= $form_drop_down_menu_title.": ".$wpss_contact_drop_down_menu."\r\n";
			}

			$wpss_contact_form_msg_2 .= "\r\n";
			if( !empty( $form_include_user_meta ) ) {
				$wpss_contact_form_msg_2 .= "\r\n";
				$wpss_contact_form_msg_2 .= __( 'Website Generating This Email', 'wp-spamshield' ) . ': '.$wpss_contact_form_blog."\r\n";
				$wpss_contact_form_msg_2 .= __( 'Referrer', 'wp-spamshield' ) . ': '.$wpss_contact_form_http_referer."\r\n"; /* Initial referrer, aka "Referring Site" - Changed 1.7.9 */
				$wpss_contact_form_msg_2 .= __( 'User-Agent (Browser/OS)', 'wp-spamshield' ) . ": ".$wpss_contact_form_http_user_agent."\r\n";
				if( !empty( $wpss_contact_form_http_browser ) ) {
					$wpss_contact_form_msg_2 .= __( 'Browser', 'wp-spamshield' ) . ": ".$wpss_contact_form_http_browser."\r\n";
				}
				if( strpos( WPSS_SERVER_NAME_REV, WPSS_DEBUG_SERVER_NAME_REV ) === 0 ) {
					if( !empty ( $wpss_geolocation ) && rs_wpss_is_lang_en_us() ) { /* English only for now; TO DO: TRANSLATE */
						$wpss_contact_form_msg_2 .= __( 'Location', 'wp-spamshield' ) . ': '.$wpss_geolocation."\r\n";
					}
				} else {
					if( !empty ( $wpss_geoloc_short ) && rs_wpss_is_lang_en_us() ) { /* English only for now; TO DO: TRANSLATE */
						$wpss_contact_form_msg_2 .= __( 'Country', 'wp-spamshield' ) . ': '.$wpss_geoloc_short."\r\n";
					}
				}
				$wpss_contact_form_msg_2 .= __( 'IP Address', 'wp-spamshield' ) . ': '.$ip."\r\n";
				$wpss_contact_form_msg_2 .= __( 'Server', 'wp-spamshield' ) . ': '.$reverse_dns."\r\n";
				$wpss_contact_form_msg_2 .= __( 'IP Address Lookup', 'wp-spamshield' ) . ': http://ipaddressdata.com/'.$ip."\r\n";
				if( !current_user_can( 'manage_options' ) ) {
					$blacklist_text = __( 'Blacklist the IP Address:', 'wp-spamshield' );
					$ip_nodot = str_replace( '.', '', $ip );
					$ip_blacklist_nonce_action	= 'blacklist_ip_'.$ip;
					$ip_blacklist_nonce_name	= 'bl'.$ip_nodot.'tkn';
					$nonce = rs_wpss_create_nonce( $ip_blacklist_nonce_action, $ip_blacklist_nonce_name );
					$blacklist_url = WPSS_ADMIN_URL.'/options-general.php?page='.WPSS_PLUGIN_NAME.'&wpss_action=blacklist_ip&bl_ip='.$ip.'&'.$ip_blacklist_nonce_name.'='.$nonce;
					$wpss_contact_form_msg_2 .= $blacklist_text.' '.$blacklist_url."\r\n";
				}
			}

			$wpss_contact_form_msg_3 .= "\r\n\r\n";

			$wpss_contact_form_msg = rs_wpss_prep_cf_email( $wpss_contact_form_msg_1.$wpss_contact_form_msg_2.$wpss_contact_form_msg_3 );
			$wpss_contact_form_msg_cc = rs_wpss_prep_cf_email( $wpss_contact_form_msg_1.$wpss_contact_form_msg_3 );
			/* MESSAGE CONTENT - END */

			/**
			 *  CREATE MESSAGE WPSSID - BEGIN
			 *  @since 1.7.7
			 */
			$wpsseid_args 				= array( 'name' => $wpss_contact_name, 'email' => $wpss_contact_email_lc, 'url' => $wpss_contact_website_lc, 'content' => $wpss_contact_message );
			$wpsseid 					= rs_wpss_get_wpss_eid( $wpsseid_args );
			$wpss_contact_form_mid 		= $wpsseid['eid'];
			$wpss_contact_form_mcid 	= $wpsseid['ecid'];
			/* CREATE MESSAGE WPSSID - END */

			if( empty( $blank_field ) && empty( $invalid_value ) && empty( $message_short ) && empty( $message_long ) && empty( $message_spam ) && empty( $cf_jsck_error ) && empty( $server_blacklisted ) && empty( $cf_badrobot_error ) && empty( $user_blacklisted ) ) {

				/* SEND MESSAGE */

				/* Verify if Already Sent - to Prevent Duplicates - Added in 1.6 */
				$key_contact_forms_submitted = 'contact_forms_submitted_'.WPSS_HASH;
				if( empty( $_SESSION[$key_contact_forms_submitted] ) ) {
					$_SESSION[$key_contact_forms_submitted] = array();
				}
				$spamshield_wpssmid_cache = get_option( 'spamshield_wpssmid_cache' );
				if( empty( $spamshield_wpssmid_cache ) ) {
					$spamshield_wpssmid_cache = array();
				}
				if( !empty( $_SESSION[$key_contact_status] ) && $_SESSION[$key_contact_status] !== 'SENT' && !in_array( $wpss_contact_form_mid, $_SESSION[$key_contact_forms_submitted], TRUE ) && !in_array( $wpss_contact_form_mid, $spamshield_wpssmid_cache, TRUE ) ) {
					WP_SpamShield::mail( $wpss_contact_form_to, $wpss_contact_form_subject, $wpss_contact_form_msg, $wpss_contact_form_msg_headers, NULL, 'contact' );
					$_SESSION[$key_contact_status] = 'SENT';
					$_SESSION[$key_contact_forms_submitted][] = $wpss_contact_form_mid;
					$spamshield_wpssmid_cache[] = $wpss_contact_form_mid;
					update_option( 'spamshield_wpssmid_cache', $spamshield_wpssmid_cache );
				} elseif( in_array( $wpss_contact_form_mid, $_SESSION[$key_contact_forms_submitted], TRUE ) ) {
					if( !in_array( $wpss_contact_form_mid, $spamshield_wpssmid_cache, TRUE ) ) {
						$spamshield_wpssmid_cache[] = $wpss_contact_form_mid;
						update_option( 'spamshield_wpssmid_cache', $spamshield_wpssmid_cache );
					}
					rs_wpss_append_log_data( 'Duplicate contact form submission. Message not sent. WPSSMID: '.$wpss_contact_form_mid.' WPSSMCID: '.$wpss_contact_form_mcid.' [S]', FALSE );
				} elseif( in_array( $wpss_contact_form_mid, $spamshield_wpssmid_cache, TRUE ) ) {
					$_SESSION[$key_contact_forms_submitted][] = $wpss_contact_form_mid;
					rs_wpss_append_log_data( 'Duplicate contact form submission. Message not sent. WPSSMID: '.$wpss_contact_form_mid.' WPSSMCID: '.$wpss_contact_form_mcid.' [D]', FALSE );
				}

				$contact_response_status = 'thank-you';
				$wpss_error_code = 'No Error';
				rs_wpss_update_accept_status( $cf_author_data, 'a', 'Line: '.__LINE__ );
				if( !empty( $spamshield_options['comment_logging'] ) && !empty( $spamshield_options['comment_logging_all'] ) ) {
					rs_wpss_log_data( $cf_author_data, $wpss_error_code, 'contact form', $wpss_contact_form_msg, $wpss_contact_form_mid, $wpss_contact_form_mcid );
				}
			} else {
				$wpss_error_code = trim( $wpss_error_code );
				if( TRUE === $user_blacklisted ) {
					rs_wpss_append_log_data( 'Blacklisted user detected. Contact form has been temporarily disabled to prevent spam. ERROR CODE: '.$wpss_error_code, FALSE );
				}
				rs_wpss_update_accept_status( $cf_author_data, 'r', 'Line: '.__LINE__, $wpss_error_code );
				$contact_response_status = 'error';
				if( !empty( $spamshield_options['comment_logging'] ) ) {
					rs_wpss_log_data( $cf_author_data, $wpss_error_code, 'contact form', $wpss_contact_form_msg, $wpss_contact_form_mid, $wpss_contact_form_mcid );
				}
			}

			/* TEST TO PREVENT CONTACT FORM SPAM - END */

			$form_response_thank_you_message_default = '<p>' . __( 'Your message was sent successfully. Thank you.', 'wp-spamshield' ) . '</p><p>&nbsp;</p>';
			$form_response_thank_you_message = __( $form_response_thank_you_message, 'wp-spamshield' );

			$error_txt = rs_wpss_error_txt();
			$wpss_error = $error_txt.':';
			$wpss_js_disabled_msg_short = __( 'Currently you have JavaScript disabled.', 'wp-spamshield' );

			if( $contact_response_status === 'thank-you' ) {
				if( !empty( $form_response_thank_you_message ) ) { $cf_content .= '<p>'.$form_response_thank_you_message.'</p><p>&nbsp;</p>'.WPSS_EOL; }
				else { $cf_content .= $form_response_thank_you_message_default.WPSS_EOL; }
			} else {
				/* Back URL was here...moved */
				if( !empty( $message_spam ) ) {
					$contact_response_status_message_addendum .= '<noscript><br />&nbsp;<br />&bull; '.$wpss_js_disabled_msg_short.'</noscript>'.WPSS_EOL;
					$cf_content .= '<p><strong>'.$wpss_error.' <br />&nbsp;<br />'.$contact_response_status_message_addendum.'</strong></p><p>&nbsp;</p>'.WPSS_EOL;
				} else {
					$contact_response_status_message_addendum .= '<noscript><br />&nbsp;<br />&bull; '.$wpss_js_disabled_msg_short.'</noscript>'.WPSS_EOL;
					$cf_content .= '<p><strong>'.$wpss_error.' ' . __( 'Please return to the contact form and fill out all required fields.', 'wp-spamshield' );
					$cf_content .= ' ' . __( 'Please make sure JavaScript and Cookies are enabled in your browser.', 'wp-spamshield' );
					$cf_content .= '<br />&nbsp;<br />'.$contact_response_status_message_addendum.'</strong></p><p>&nbsp;</p>'.WPSS_EOL;
				}
				/* Log error messages when debug is on */
				if( rs_wpss_get_error_type( $wpss_error_code ) === 'algo'  ) {
					rs_wpss_append_log_data( '$cf_content: "'.$cf_content.'" Line: '.__LINE__.' | Func: '.__FUNCTION__.' | MEM USED: ' . rs_wpss_wp_memory_used() . ' | VER: ' . WPSS_VERSION, TRUE );
				}
			}
			$content_new = str_replace($content, $cf_content, $content);
			$content_shortcode = $cf_content;
			/* CONTACT FORM BACK END - END */
		} else {
			/**
			 *  3 - ALL OTHER CASES
			 *  CONTACT FORM FRONT END - BEGIN
			 */

			$cf_content .= '<form id="wpss_contact_form" name="wpss_contact_form" action="'.$cf_action_url.'" method="post" style="text-align:left;" >'.WPSS_EOL;
			$cf_req = 'required="required" ';
			$cf_content .= '<p><label><strong>' . __( 'Name' ) . '</strong> *<br />'.WPSS_EOL;
			$cf_content .= '<input type="text" id="wpss_contact_name" name="wpss_contact_name" value="" size="40" '.$cf_req.'/> </label></p>'.WPSS_EOL;
			$cf_content .= '<p><label><strong>' . __( 'Email' ) . '</strong> *<br />'.WPSS_EOL;
			$cf_content .= '<input type="email" id="wpss_contact_email" name="wpss_contact_email" value="" size="40" '.$cf_req.'/> </label></p>'.WPSS_EOL;
			foreach( $form_include as $k => $v ) {
				if( $k === 'website' ) { $text = __( 'Website' ); $type = 'url'; $value = ''; }
				else { $text = __( rs_wpss_casetrans( 'ucfirst', $k ), WPSS_PLUGIN_NAME ); $type = 'text'; $value = ''; }
				if( !empty( $v['i'] ) ) {
					$cf_req = ''; $cf_content .= '<p><label><strong>'.$text.'</strong> ';
					if( !empty( $v['r'] ) ) { $cf_content .= '*'; $cf_req = 'required="required" '; }
					$cf_content .= '<br />'.WPSS_EOL.'<input type="'.$type.'" id="wpss_contact_'.$k.'" name="wpss_contact_'.$k.'" value="'.$value.'" size="40" '.$cf_req.'/> </label></p>'.WPSS_EOL;
				}
			}
			if( !empty( $form_include_drop_down_menu ) && !empty( $form_drop_down_menu_title ) && !empty( $form_drop_down_menu_item_1 ) && !empty( $form_drop_down_menu_item_2 ) ) {
				$cf_req = '';
				$cf_content .= '<p><label><strong>'.$form_drop_down_menu_title.'</strong> ';
				if( !empty( $form_require_drop_down_menu ) ) {
					$cf_content .= '*';
					$cf_req = 'required="required" ';
				}
				$cf_content .= '<br />'.WPSS_EOL;
				$cf_content .= '<select id="wpss_contact_drop_down_menu" name="wpss_contact_drop_down_menu" '.$cf_req.'> '.WPSS_EOL;
				$cf_content .= '<option value="" selected="selected">' . __( 'Select' ) . '</option> '.WPSS_EOL;
				$cf_content .= '<option value="">--------------------------</option> '.WPSS_EOL;
				$i = 1;
				while( $i <= 10 ) {
					if( !empty( $form_drop_down_menu_item[$i] ) ) { $cf_content .= '<option value="'.$form_drop_down_menu_item[$i].'">'.$form_drop_down_menu_item[$i].'</option> '.WPSS_EOL; }
					++$i;
				}
				$cf_content .= '</select> '.WPSS_EOL;
				$cf_content .= '</label></p>'.WPSS_EOL;
			}
			$cf_req = 'required="required" ';
			$cf_content .= '<p><label><strong>' . __( 'Subject', 'wp-spamshield' ) . '</strong> *<br />'.WPSS_EOL;
    		$cf_content .= '<input type="text" id="wpss_contact_subject" name="wpss_contact_subject" value="" size="40" '.$cf_req.'/> </label></p>'.WPSS_EOL;
			$cf_content .= '<p><label><strong>' . __( 'Message', 'wp-spamshield' ) . '</strong> *<br />'.WPSS_EOL;
			$cf_content .= '<textarea id="wpss_contact_message" name="wpss_contact_message" cols="'.$form_message_width.'" rows="'.$form_message_height.'" minlength="'.$form_message_min_length.'" maxlength="25600" '.$cf_req.'></textarea> </label></p>'.WPSS_EOL;
			$cf_content .= '<noscript><input type="hidden" name="'.WPSS_JSONST.'" value="NS2" /></noscript>'.WPSS_EOL;
			$wpss_js_disabled_msg 	= __( 'Currently you have JavaScript disabled. In order to use this contact form, please make sure JavaScript and Cookies are enabled, and reload the page.', 'wp-spamshield' );
			$wpss_js_enable_msg 	= __( 'Click here for instructions on how to enable JavaScript in your browser.', 'wp-spamshield' );
			$cf_content .= '<noscript><p><strong>'.$wpss_js_disabled_msg.'</strong> <a href="http://enable-javascript.com/" rel="nofollow external" >'.$wpss_js_enable_msg.'</a></p></noscript>'.WPSS_EOL;
			$cf_content .= '<p><input type="submit" id="wpss_contact_submit" name="wpss_contact_submit" value="' . __( 'Send Message', 'wp-spamshield' ) . '" /></p>'.WPSS_EOL;
			$cf_content .= '<p>' . sprintf( __( 'Required fields are marked %s' ), '*' ) . '</p>'.WPSS_EOL;
			$cf_content .= '<p>&nbsp;</p>'.WPSS_EOL;
			if( !empty( $promote_plugin_link ) ) {
				$sip5c = '0';
				$sip5c = substr(WPSS_SERVER_ADDR, 4, 1); /* Server IP 5th Char */
				$ppl_code = array( '0' => 2, '1' => 2, '2' => 2, '3' => 2, '4' => 2, '5' => 2, '6' => 1, '7' => 0, '8' => 2, '9' => 2, '.' => 2 );
				if( preg_match( "~^[0-9\.]$~", $sip5c ) ) {
					$int = $ppl_code[$sip5c];
				} else { $int = 0; }
				$cf_content .= WPSS_Promo_Links::contact_promo_link($int).WPSS_EOL;
				$cf_content .= '<p>&nbsp;</p>'.WPSS_EOL;
			}
			$cf_content .= '</form>'.WPSS_EOL;

			/* PRE-TESTS, WILL DISABLE CONTACT FORM */
			$cf_blacklist_status = '';

			/**
			 *  TEST 0-PRE - See if user has already been blacklisted this session.
			 *  As of 1.8.4, this is only test that will shut down contact form BEFORE it's submitted.
			 */

			if( rs_wpss_ubl_cache() ) {
				$cf_blacklist_status = '3'; /* Was '2', changed to '3' in 1.8.4 */
				$wpss_error_code .= ' CF-0-PRE-BL';
			}

			$wpss_error_code = trim( $wpss_error_code );

			/* DISABLE CONTACT FORM IF BLACKLISTED */
			if( !empty( $cf_blacklist_status ) && $cache_check_status !== 'ACTIVE' ) {
				$cf_content = '<strong>' . __( 'Contact form has been temporarily disabled to prevent spam. Please try again later.', 'wp-spamshield' ) . '</strong>';
				rs_wpss_append_log_data( 'Blacklisted user detected. Contact form has been temporarily disabled to prevent spam. ERROR CODE: '.$wpss_error_code, FALSE );
			}
			$content_new = str_replace( $contact_repl_text, $cf_content, $content );
			$content_shortcode = $cf_content;
			/* CONTACT FORM FRONT END - END */
		}
	} else { $wpss_contact_inprog = FALSE; return $content; }
	if( $get_form === 'response' ) { $content_new = str_replace($content, $cf_content, $content); $content_shortcode = $cf_content; }
	else { $content_new = str_replace( $contact_repl_text, $cf_content, $content); $content_shortcode = $cf_content; }
	if( $shortcode_check === 'shortcode' && !empty( $content_shortcode ) ) { $content_new = $content_shortcode; }
	$wpss_contact_inprog = FALSE;
	return $content_new;
}

/* BLACKLISTS - BEGIN */

function rs_wpss_bad_robot_blacklist_chk( $type = 'comment', $status = NULL, $ip = NULL, $rev_dns = NULL, $author = NULL, $email = NULL ) {
	/* Use this to determine if a visitor is a bad robot. */
	$wpss_error_code			= $ns_val = '';
	$blacklisted				= FALSE;
	/* IP / PROXY INFO - BEGIN */
	global $wpss_ip_proxy_info;
	if( empty( $wpss_ip_proxy_info ) ) { $wpss_ip_proxy_info = rs_wpss_ip_proxy_info(); }
	extract( $wpss_ip_proxy_info );
	/* IP / PROXY INFO - END */
	$rev_dns					= rs_wpss_casetrans('lower',trim($rev_dns));
	$user_agent_lc				= rs_wpss_get_user_agent( TRUE, TRUE );
	$user_agent_word_count		= rs_wpss_count_words( $user_agent_lc );
	$http_accept				= rs_wpss_get_http_accept( TRUE, TRUE );
	$http_accept_language		= rs_wpss_get_http_accept( TRUE, TRUE, TRUE );
	$http_accept_encoding		= rs_wpss_get_http_accept( TRUE, TRUE, FALSE, TRUE );
	/* Detect Incapsula, and disable rs_wpss_ubl_cache - 1.8.9.6 */
	if( strpos( $rev_dns, '.ip.incapdns.net' ) !== FALSE ) { update_option( 'spamshield_ubl_cache_disable', TRUE ); }
	switch ( $type ) {
		/* TO DO: Add types for EPC and XML-RPC */
		case 'register':
			$pref = 'R-'; $ns_val = 'NS3'; break;
		case 'contact':
			$pref = 'CF-'; $ns_val = 'NS2'; break;
		case 'misc form':
			$pref = 'MSC-'; break;
		case 'contact form 7':
			$pref = 'CF7-'; break;
		case 'gravity forms':
			$pref = 'GF-'; break;
		case 'jetpack form':
			$pref = 'JP-'; break;
		case 'ninja forms':
			$pref = 'NF-'; break;
		case 'mailchimp form':
			$pref = 'MCF-'; break;
		default:
			$pref = ''; $ns_val = 'NS1';
	}

	if( !empty( $_SERVER['WPSS_SEC_THREAT'] ) || !empty( $_SERVER['BHCS_SEC_THREAT'] ) ) {
		$wpss_error_code .= ' '.$pref.'HTA1000'; $blacklisted = TRUE; $status = '3';
		$bad_robot_filter_data = array( 'status' => $status, 'error_code' => $wpss_error_code, 'blacklisted' => $blacklisted );
		return $bad_robot_filter_data;
	}

	/**
	 *  REF2XJS
	 *  This case only happens if bots scrape/spam the form. Nice try guys.
	 */
	$post_ref2xjs 		= !empty( $_POST[WPSS_REF2XJS] ) ? trim( $_POST[WPSS_REF2XJS] ) : '';
	$post_ref2xjs_lc	= rs_wpss_casetrans( 'lower', $post_ref2xjs );
	$ref2xjs_lc			= rs_wpss_casetrans( 'lower', WPSS_REF2XJS );
	if( !empty( $post_ref2xjs ) && strpos( $post_ref2xjs_lc, $ref2xjs_lc ) !== FALSE ) {
		$wpss_error_code .= ' '.$pref.'REF-2-1023'; $blacklisted = TRUE;
	}

	if( 'trackback' !== $type && 'pingback' !== $type && !rs_wpss_is_xmlrpc() && !rs_wpss_is_doing_rest() && !rs_wpss_is_doing_cron() && !rs_wpss_is_doing_ajax() && !rs_wpss_is_ajax_request() && !rs_wpss_is_local_request() ) {

		/* HA / HAL / HAE - Robots */
		if( empty( $http_accept ) ) {
			$wpss_error_code .= ' '.$pref.'HA1001'; $blacklisted = TRUE;
		}
		/* HA1002 - REMOVED 1.9.0.3 */
		if( $http_accept === '*' ) {
			$wpss_error_code .= ' '.$pref.'HA1003'; $blacklisted = TRUE;
		}
		if( !empty( $_SERVER['WPSS_HA1004'] ) || $http_accept === '*/*' ) { /* && !rs_wpss_is_ajax_request() */
			$wpss_error_code .= ' '.$pref.'HA1004'; $blacklisted = TRUE;
		}
		$http_accept_mod_1 = preg_replace( "~([\s\;]+)~", ",", $http_accept );
		$http_accept_elements = explode( ',', $http_accept_mod_1 );
		$http_accept_elements_count = count($http_accept_elements);
		$i = 0;
		/* The following line to prevent exploitation: */
		$i_max = 20;
		while( $i < $http_accept_elements_count && $i < $i_max ) {
			if( !empty( $http_accept_elements[$i] ) ) {
				if( $http_accept_elements[$i] === '*' ) {
					$wpss_error_code .= ' '.$pref.'HA1010'; $blacklisted = TRUE; break;
				}
			}
			++$i;
		}
		if( empty( $http_accept_language ) ) {
			$wpss_error_code .= ' '.$pref.'HAL1001'; $blacklisted = TRUE;
		}
		if( $http_accept_language === '*' ) {
			$wpss_error_code .= ' '.$pref.'HAL1002'; $blacklisted = TRUE;
		}
		$http_accept_language_mod_1 = preg_replace( "~([\s\;]+)~", ",", $http_accept_language );
		$http_accept_language_elements = explode( ',', $http_accept_language_mod_1 );
		$http_accept_language_elements_count = count($http_accept_language_elements);
		$i = 0;
		/* The following line to prevent exploitation: */
		$i_max = 20;
		while( $i < $http_accept_language_elements_count && $i < $i_max ) {
			if( !empty( $http_accept_language_elements[$i] ) ) {
				if( $http_accept_language_elements[$i] === '*' && strpos( $user_agent_lc, 'links (' ) !== 0 ) {
					$wpss_error_code .= ' '.$pref.'HAL1010'; $blacklisted = TRUE; break;
				}
			}
			++$i;
		}
		/* HAL1005 - NOT IMPLEMENTED */
	}

	if( $http_accept_language === 'en-US,*' && $http_accept_encoding === 'gzip' ) { /* PhantomJS Sig */
		$wpss_error_code .= ' '.$pref.'HALE1001'; $blacklisted = TRUE;
	}

	/**
	 *  USER-AGENT
	 *  Add Blacklisted User-Agent Function - Note 1.4
	 */

	/* UA1001 - REMOVED 1.9.8.7 - rs_wpss_skiddie_ua_check() now includes this */

	if( rs_wpss_skiddie_ua_check( $user_agent_lc, $type ) ) {
		$wpss_error_code .= ' '.$pref.'UA1004'; $blacklisted = TRUE;
	}

	if( !rs_wpss_is_local_request() ) {
		/* REVDNS */
		$rev_dns_filter_data 	 = rs_wpss_revdns_filter( $type, $status, $ip, $rev_dns, $author, $email );
		$revdns_blacklisted 	 = $rev_dns_filter_data['blacklisted'];
		if( !empty( $revdns_blacklisted ) ) {
			$wpss_error_code 	.= $rev_dns_filter_data['error_code']; $blacklisted = TRUE;
		}
	}

	if( !empty( $blacklisted ) ) { $status = '3'; $_SERVER['WPSS_SEC_THREAT'] = TRUE; } /* Was 2, changed to 3 - V1.8.4 */
	$bad_robot_filter_data = array( 'status' => $status, 'error_code' => $wpss_error_code, 'blacklisted' => $blacklisted );
	return $bad_robot_filter_data;
}

function rs_wpss_email_blacklist_chk( $email = NULL, $get_eml_list_arr = FALSE, $get_pref_list_arr = FALSE, $get_str_list_arr = FALSE, $get_str_rgx_list_arr = FALSE ) {
	/* Email Blacklist Check */
	$blacklisted_emails = rs_wpss_rbkmd( rs_wpss_get_email_blacklist(), 'de', TRUE );
	if( !empty( $get_eml_list_arr ) ) { return $blacklisted_emails; }
	$blacklisted_email_prefixes = array(
		/* The beginning part of the email */
		"anonymous@", "fuckyou@", "root@", "spam@", "spambot@", "spammer@",
		);
	if( !empty( $get_pref_list_arr ) ) { return $blacklisted_email_prefixes; }

	$blacklisted_email_strings = array(
		/* Red-flagged strings that occur anywhere in the email address */
		".seo@gmail.com", ".bizapps@gmail.com",
		);
	if( !empty( $get_str_list_arr ) ) { return $blacklisted_email_strings; }

	$blacklisted_email_strings_rgx = array(
		/* Custom regex strings that occur in the email address */
		"spinfilel?namesdat", "^name\-[0-9]{5}\@g(oogle)?mail(\.[a-z]{2,3}){1,2}[a-z]*$", 
		"\.((marketing|business|web)manager|seo(services?)?)[0-9]*\@((g(oogle)?mail|hotmail|outlook|yahoo|mail|gmx|inbox|myway|rediffmail|yandex|rambler|arcor|163|aol|lycos|zoho(mail)?|protonmail|hushmail)(\.[a-z]{2,3}){1,2}[a-z]*|usa\.net)$", 
		"^((marketing|business|web)manager|seo(services?)?)\..*\@((g(oogle)?mail|hotmail|outlook|yahoo|mail|gmx|inbox|myway|rediffmail|yandex|rambler|arcor|163|aol|lycos|zoho(mail)?|protonmail|hushmail)(\.[a-z]{2,3}){1,2}[a-z]*|usa\.net)$",  
		"\.((marketing|business|web)manager|seo(services?)?).*\@((g(oogle)?mail|hotmail|outlook|yahoo|mail|gmx|inbox|myway|rediffmail|yandex|rambler|arcor|163|aol|lycos|zoho(mail)?|protonmail|hushmail)(\.[a-z]{2,3}){1,2}[a-z]*|usa\.net)$", 
		".*((payday|short-?term|instant|(personal-?)?cash|credit)-?loans?|(cash|payday)-?advance|quick-?cash).*\@((g(oogle)?mail|hotmail|outlook|yahoo|mail|gmx|inbox|myway|rediffmail|yandex|rambler|arcor|163|aol|lycos|zoho(mail)?|protonmail|hushmail)(\.[a-z]{2,3}){1,2}[a-z]*|usa\.net)$", 
		);
	if( !empty( $get_str_rgx_list_arr ) ) { return $blacklisted_email_strings_rgx; }

	/* Goes after all arrays */
	$blacklist_status = FALSE;
	if( empty( $email ) ) { return FALSE; }
	$g_san_email = rs_wpss_sanitize_gmail( $email );
	$blacklisted_domains = rs_wpss_domain_blacklist_chk('',TRUE);

	/* Gmail sanitization check added 1.9.5.6 */
	$regex_phrase_g_san_arr = array();
	foreach( $blacklisted_emails as $i => $blacklisted_email ) {
		$regex_phrase_g_san_arr[] = rs_wpss_get_regex_phrase($blacklisted_email,'','email_addr');
	}
	foreach( $regex_phrase_g_san_arr as $i => $regex_phrase ) {
		if( preg_match( $regex_phrase, $g_san_email ) ) { return TRUE; }
	}

	$n = 0; /* Tests 1-3 - Don't add to UBL Cache */
	$t = 0; /* Total */
	$regex_phrase_arr = array();
	foreach( $blacklisted_email_prefixes as $i => $blacklisted_email_prefix ) {
		$regex_phrase_arr[] = rs_wpss_get_regex_phrase($blacklisted_email_prefix,'','email_prefix');
		++$n; ++$t;
	}
	foreach( $blacklisted_email_strings as $i => $blacklisted_email_string ) {
		$regex_phrase_arr[] = rs_wpss_get_regex_phrase($blacklisted_email_string,'','red_str');
		++$n; ++$t;
	}
	foreach( $blacklisted_email_strings_rgx as $i => $blacklisted_email_string_rgx ) {
		$regex_phrase_arr[] = rs_wpss_get_regex_phrase($blacklisted_email_string_rgx,'','rgx_str');
		++$n; ++$t;
	}
	foreach( $blacklisted_domains as $i => $blacklisted_domain ) {
		$regex_phrase_arr[] = rs_wpss_get_regex_phrase($blacklisted_domain,'','email_domain');
		++$t;
	}
	foreach( $regex_phrase_arr as $i => $regex_phrase ) {
		if( preg_match( $regex_phrase, $email ) ) {
			if( $i > $n ) { rs_wpss_ubl_cache( 'set' ); }
			return TRUE;
		}
	}
	return $blacklist_status;
}

function rs_wpss_host_ns_blacklist_chk( $domain = WPSS_SITE_DOMAIN ) {
	/* Web Host NS Blacklist Check */
	$host_ns_bl = WP_SpamShield::get_wpss_option( 'host_ns_bl' );
	if( !empty( $host_ns_bl ) && ( 'YES' === $host_ns_bl || 'NO' === $host_ns_bl ) ) { return ( 'YES' === $host_ns_bl ) ? TRUE : FALSE; }
	$ns = rs_wpss_get_ns( $domain ); if( empty( $ns ) ) { return FALSE; }
	$blacklisted_host_ns = rs_wpss_rbkmd( rs_wpss_get_host_ns_blacklist(), 'de', TRUE );

	/* The Blacklist */
	foreach( $ns as $i => $n ) {
		foreach( $blacklisted_host_ns as $i => $bl_ns ) {
			$regex_check_phrase = rs_wpss_get_regex_phrase( $bl_ns, '', 'ns' );
			if( preg_match( $regex_check_phrase, $n ) ) { WP_SpamShield::update_wpss_option( array( 'host_ns_bl' => 'YES' ) ); return TRUE; }
		}
	}
	WP_SpamShield::update_wpss_option( array( 'host_ns_bl' => 'NO' ) ); return FALSE;
}


function rs_wpss_domain_blacklist_chk( $domain = NULL, $get_list_arr = FALSE ) {
	/* Domain Blacklist Check */
	$blacklisted_domains = rs_wpss_rbkmd( rs_wpss_get_domain_blacklist(), 'de', TRUE );
	if( !empty( $get_list_arr ) ) { return $blacklisted_domains; }
	/* Goes after array */
	$blacklist_status = FALSE;
	if( empty( $domain ) ) { return FALSE; }
	/* Check if Spammy Domain */
	if( rs_wpss_spammy_domain_chk( $domain ) ) { rs_wpss_ubl_cache( 'set' ); return TRUE; }
	/* Other Checks */
	$regex_phrases_other_checks = array(
		/* Payday Loan Spammers - Keywords in Domain */
		"~((payday|short-?term|instant|quick|fast|(personal\-?)?cash|credit)\-?loans?|(cash|payday)\-?advance|(instant|quick|fast)\-?cash)~i",
		/* "plan cul" Spammers */
		"~(plan\-?cul|tonplanq)~i",
		/* Offshore IT Spammers */
		"~info(tech|soft)(\.[a-z]{2,3}){1,2}[a-z]*$~i",
		/* SEO Spammers */
		"~se[om](lead|prospect)s?~i",
		/* Misc */
		"~^((ww[w0-9]|m)\.)?whereto(buy|get)cannabisoil~i",
		"~(cheap|quick)(\.[a-z]{2,3}){1,2}[a-z]*$~i",
		"~^((ww[w0-9]|m)\.)?(buy|get|play|hack|unlock)\-[a-z0-9\.\-]+\-(online|now|quick|fast)(\.[a-z]{2,3}){1,2}[a-z]*$~i",
		"~^(accounts|cloud|code|developers|docs|feedproxy|firebase|get|groups|hangouts|images|mail|news|photos|picasa|sites|talkgadget|translate)\.google(\.[a-z]{2,3}){1,2}[a-z]*$~i",
		);
	foreach( $regex_phrases_other_checks as $i => $regex_check_phrase ) {
		if( preg_match( $regex_check_phrase, $domain ) ) { rs_wpss_ubl_cache( 'set' ); return TRUE; }
	}
	/* Final Check - The Blacklist...takes longest once blacklist is populated, so put last */
	foreach( $blacklisted_domains as $i => $blacklisted_domain ) {
		$regex_check_phrase = rs_wpss_get_regex_phrase( $blacklisted_domain, '', 'domain' );
		if( preg_match( $regex_check_phrase, $domain ) ) { rs_wpss_ubl_cache( 'set' ); return TRUE; }
	}
	return $blacklist_status;
}

function rs_wpss_spammy_domain_chk( $domain ) {
	/**
	 *  Check for domains that exist only for spammy, low-quality SEO
	 */
	if( empty( $domain ) ) { return FALSE; }
	/* Excessive keyword-stuffing w/hyphens */
	$dom_len = rs_wpss_strlen( $domain );
	$hyphens = rs_wpss_substr_count( $domain, '-' );
	if( $hyphens >= 2 && FALSE === strpos( $domain, '--') ) {
		$dom_els = explode( '.', $domain ); /* Domain elements */
		foreach( $dom_els as $i => $e ) {
			$l = rs_wpss_strlen( $e );
			if( $l < 17 || FALSE === strpos( $e, '-') ) { continue; }
			$h = rs_wpss_substr_count( $e, '-' );
			$p = 100 * ( $h / $l );
			if( $p >= 11 ) { return TRUE; }
		}
	}
	/* Spam TLDs Based on Data */
	$blacklisted_tlds = rs_wpss_rbkmd( rs_wpss_get_tld_blacklist(), 'de', TRUE );
	$regex_check_phrase = rs_wpss_get_regex_phrase( implode( '|', $blacklisted_tlds ), '', 'tld' );
	if( preg_match( $regex_check_phrase, $domain ) ) { return TRUE; }
	return FALSE;
}

function rs_wpss_urlshort_blacklist_chk( $url = NULL, $email_domain = NULL, $comment_type = NULL  ) {
	/**
	 *  URL Shortener Blacklist Check
	 *  Dangerous because users have no idea what website they are clicking through to
	 *  @since $comment_type in 1.5 for trackbacks
	 */
	$url_shorteners = rs_wpss_rbkmd( rs_wpss_get_urlshort_blacklist(), 'de', TRUE );
	/* Goes after array */
	$blacklist_status = FALSE;
	if( empty( $url ) ) { return FALSE; }
	$url = rs_wpss_fix_url( $url );
	$domain = rs_wpss_get_domain( $url );
	if( empty( $domain ) ) { return FALSE; }
	$domain_rgx = rs_wpss_preg_quote( $domain );
	/* See if link points to domain root or legit corporate page (ie. bitly.com) */
	if( $comment_type !== 'trackback' && preg_match( "~^https?\://$domain_rgx(/|.*([a-z0-9\-]+/[a-z0-9\-]{4,}.*|[a-z0-9\-]{4,}\.[a-z]{3,4}))?$~iu", $url ) ) { return FALSE; }
	/* Shortened URL check begins */
	$regex_phrase = rs_wpss_get_regex_phrase($url_shorteners,'','domain');
		/* Consider adding regex for 2-letter domains with 2-letter extensions ( "aa.xx" ) */
	if( $email_domain !== $domain && preg_match( $regex_phrase, $domain ) ) {
		return TRUE;
	}
	return $blacklist_status;
}

function rs_wpss_long_url_chk( $url = NULL ) {
	/**
	 *  Excessively Long URL Check - Added in 1.3.8
	 *  To prevent obfuscated & exploit URL's
	 */
	$blacklist_status = FALSE;
	if( empty( $url ) ) { return FALSE; }
	$url_lim = 140; $url_len = rs_wpss_strlen($url);
	if( $url_len > $url_lim ) { return TRUE; }
	return $blacklist_status;
}

function rs_wpss_social_media_url_chk( $url = NULL, $comment_type = NULL ) {
	/**
	 *  Social Media URL Check - Added in 1.3.8
	 *  To prevent author url and anchor text links to spam social media profiles
	 *  @since $comment_type in 1.5 for trackbacks
	 */
	$social_media_domains = rs_wpss_rbkmd( rs_wpss_get_sm_domain_blacklist(), 'de', TRUE );
	$social_media_domains_ext = rs_wpss_rbkmd( rs_wpss_get_sm_ext_domain_blacklist(), 'de', TRUE );
	/* Goes after array */
	$blacklist_status = FALSE;
	if( empty( $url ) ) { return FALSE; }
	if( $comment_type === 'trackback' ) { $social_media_domains = $social_media_domains_ext; }
	$domain = rs_wpss_get_domain( $url );
	$domain_rgx = rs_wpss_preg_quote( $domain );
	$url = rs_wpss_fix_url( $url );
	/* See if link points to domain root (ie. facebook.com) */
	if( $comment_type !== 'trackback' && preg_match( "~^https?\://$domain_rgx/?$~iu", $url ) ) { return FALSE; }
	$regex_phrase = rs_wpss_get_regex_phrase($social_media_domains,'','domain');
	if( preg_match( $regex_phrase, $domain ) ) { return TRUE; }
	/* When $regex_phrase exceeds a certain size, switch this to run smaller groups or run each domain individually */
	return $blacklist_status;
}

function rs_wpss_misc_spam_url_chk( $url = NULL ) {
	/**
	 *  Spam Domain URL Check - Added in 1.3.8
	 *  To prevent author url and anchor text links to spam domains
	 */
	$spam_domains = rs_wpss_rbkmd( rs_wpss_get_spam_domain_blacklist(), 'de', TRUE );
	/* Goes after array */
	$blacklist_status = FALSE;
	if( empty( $url ) ) { return FALSE; }
	$domain = rs_wpss_get_domain( $url );
	$regex_phrases_other_checks = array(
		"~^https?\://(www\.)?google(\.[a-z]{2,3}){1,2}[a-z]*/maps/~i", "~(/members?/|/user/profile/)~i", "~youporn([0-9]+)\.vox\.com~i", "~shopsquareone\.com/stores/~i", "~yellowpages\.ca/bus/~i", "~seo-?services?-?(new-?york|ny)~i", 
		);
	foreach( $regex_phrases_other_checks as $i => $regex_check_phrase ) {
		if( preg_match( $regex_check_phrase, $url ) ) { return TRUE; }
	}
	$regex_phrase = rs_wpss_get_regex_phrase($spam_domains,'','domain');
	if( preg_match( $regex_phrase, $domain ) ) { return TRUE; }
	/* When $regex_phrase exceeds a certain size, switch this to run smaller groups or run each domain individually */
	return $blacklist_status;
}

function rs_wpss_referrer_blacklist_chk( $url = NULL ) {
	/**
	 *  Referrer Blacklist Check - Added in 1.8
	 *  Certain referrers result in spam 100% of the time
	 */
	$referrer_domains = rs_wpss_rbkmd( rs_wpss_get_referrer_blacklist(), 'de', TRUE );
	/* Goes after array */
	$blacklist_status = FALSE;
	if( empty( $url ) ) { $url = rs_wpss_get_referrer( FALSE, TRUE, TRUE ); }
	if( empty( $url ) ) { return FALSE; }
	$domain = rs_wpss_get_domain( $url );
	$regex_phrases_other_checks = array(
		"~nksmart\.com/tools/website\-opener\.php$~i", "~rygist\.com/multiple\-url\-opener\.php$~i",
		);
	foreach( $regex_phrases_other_checks as $i => $regex_check_phrase ) {
		if( preg_match( $regex_check_phrase, $url ) ) { return TRUE; }
	}
	$regex_phrase = rs_wpss_get_regex_phrase($referrer_domains,'','domain');
	if( preg_match( $regex_phrase, $domain ) ) { return TRUE; }
	/* When $regex_phrase exceeds a certain size, switch this to run smaller groups or run each domain individually */
	return $blacklist_status;
}

function rs_wpss_link_blacklist_chk( $haystack = NULL, $type = 'domain' ) {
	/**
	 *  Link Blacklist Check
	 *  $haystack can be any body of content you want to search for links to blacklisted domains
	 *  $type can be 'domain', 'url', or 'urlshort' depending on what kind of check you need to do. 'domain' is faster, hence it's the default
	 */
	$blacklist_status = FALSE;
	if( empty( $haystack ) ) { return FALSE; }
	$extracted_domains = rs_wpss_parse_links( $haystack, 'domain' );
	foreach( $extracted_domains as $d => $domain ) {
		if( rs_wpss_domain_blacklist_chk( $domain ) ) { return TRUE; }
	}
	return $blacklist_status;
}

function rs_wpss_at_link_spam_url_chk( $urls = NULL, $comment_type = NULL ) {
	/**
	 *  Anchor Text Link Spam URL Check
	 *  Check Anchor Text Links in comment content for links to common spam URLs
	 *  $urls - an array of URLs parsed from anchor text links
	 *  If $urls is string, will convert to array
	 *  @since $comment_type in 1.5 for trackbacks
	 */
	$blacklist_status = FALSE;
	if( empty( $urls ) ) { return FALSE; }
	if( is_string( $urls ) ) {
		$urls_arr 	= array();
		$urls_arr[]	= $urls;
		$urls 		= $urls_arr;
	}
	foreach( $urls as $u => $url ) {
		if( rs_wpss_urlshort_blacklist_chk( $url, '', $comment_type ) || rs_wpss_long_url_chk( $url ) || rs_wpss_social_media_url_chk( $url, $comment_type ) || rs_wpss_misc_spam_url_chk( $url ) ) {
			/* Shortened URLs, Long URLs, Social Media, Other common spam URLs */
			return TRUE;
		}
	}
	return $blacklist_status;
}

function rs_wpss_cf_link_spam_url_chk( $haystack = NULL, $email = NULL ) {
	/**
	 *  Contact Form Link Spam URL Check
	 *  Check Anchor Text Links in message content for links to shortened URLs
	 *  $haystack is contact form message content
	 */
	$blacklist_status = FALSE;
	if( empty( $haystack ) || empty( $email ) ) { return FALSE; }
	$email_domain = rs_wpss_parse_email($email,'domain');
	$extracted_urls = rs_wpss_parse_links( $haystack, 'url' );
	foreach( $extracted_urls as $u => $url ) {
		if( rs_wpss_urlshort_blacklist_chk( $url, $email_domain ) ) {
			return TRUE;
		}
	}
	return $blacklist_status;
}

function rs_wpss_exploit_url_chk( $urls = NULL ) {
	/**
	 *  Security - Misc Exploit URL Check - Added in 1.4
	 *  Check ALL links for common exploit URLs
	 *  $urls - an array of URLs parsed from comment or message content
	 *  If $urls is string, will convert to array (so can be used for Comment Author URL or Contact Form Website)
	 */
	$blacklist_status = FALSE;
	if( empty( $urls ) ) { return FALSE; }
	if( is_string( $urls ) ) {
		$urls_arr 	= array();
		$urls_arr[]	= $urls;
		$urls 		= $urls_arr;
	}
	foreach( $urls as $u => $url ) {
		$query_str = rs_wpss_get_query_string($url);
		if( preg_match( "~/phpinfo\.ph(p[3-6]?|tml)\?~i", $url ) ) {
			/* phpinfo.php Redirect - Used in XSS */
			return TRUE;
		} elseif( preg_match( "~^(https?\:/+)?([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})/?~i", $url ) ) {
			/**
			 *  IP Address URLs
			 *  Normal people (and Trackbacks/Pingbacks) don't post IP addresses as their website address in a comment
			 *  Dangerous because users have no idea what website they are clicking through to
			 *  Likely a Phishing site or XSS
			 */
			return TRUE;
		} elseif( !empty( $query_str ) && preg_match( "~(\.\.(\/|%2f)|boot\.ini|(ftp|https?)(\:|%3a)|mosconfig_[a-z_]{1,21}(\=|%3d)|base64_encode.*\(.*\)|[\[\]\(\)\{\}\<\>\|\"\';\?\*\$]|%22|%24|%27|%2a|%3b|%3c|%3e|%3f|%5b|%5d|%7b|%7c|%7d|%0|%a|%b|%c|%d|%e|%f|127\.0|globals|encode|localhost|loopback|request|select|insert|union|declare)~i", $query_str ) ) { /* Check Query String */
			/**
			 *  Dangerous Exploit URLs - XSS, SQL injection, or other
			 *  Test Query String - This covers a number of SQL Injection and other exploits
			 */
			return TRUE;
		} elseif( preg_match( "~([\[\]\(\)\{\}\<\>\|\"\';\*\$]|%22|%24|%27|%2a|%3b|%3c|%3e|%3f|%5b|%5d|%7b|%7c|%7d)~i", $url ) ) { /* Check Query String */
			/**
			 *  Dangerous Exploit URLs - XSS, SQL injection, or other
			 *  Test URL - no reason these would occur in a normal URL - they're not legal in a URL, but we've seen them in a lot of spam URL submissions
			 */
			return TRUE;
		}
	}
	return $blacklist_status;
}

function rs_wpss_anchortxt_blacklist_chk( $haystack = NULL, $get_list_arr = FALSE, $haystack_type = 'author', $url = NULL ) {
	/**
	 *  Author Keyword Blacklist Check
	 *  Use for testing Comment Author, Contact Form, New User Registrations, and anywhere else you need to test an author name.
	 *  This list assembled based on statistical analysis of common anchor text spam keyphrases.
	 *  Script creates all the necessary alphanumeric and linguistic variations to effectively test.
	 *  $haystack_type can be 'author' (default) or 'content'
	 */
	global $wpss_cl_active;
	if( empty( $wpss_cl_active ) ) {
		$wpss_cl_active	= WPSS_Compatibility::is_plugin_active( 'commentluv/commentluv.php' ); /* Check if active for compatibility with CommentLuv */
	}
	global $spamshield_options; if( empty( $spamshield_options ) ) { $spamshield_options = get_option('spamshield_options'); }
	if( !empty( $spamshield_options['allow_comment_author_keywords'] ) ) { $wpss_cak_active = 1; } else { $wpss_cak_active = 0; } /* Check if Comment Author Name Keywords are allowed - equivalent to CommentLuve being active */
	$blacklisted_keyphrases = rs_wpss_rbkmd( rs_wpss_get_anchortxt_blacklist(), 'de', TRUE );
	$blacklisted_keyphrases_lite = rs_wpss_rbkmd( rs_wpss_get_anchortxt_blacklist_lite(), 'de', TRUE );
	if( $haystack_type === 'author' && ( !empty( $wpss_cl_active ) || !empty( $wpss_cak_active ) || empty( $url ) ) ) {
		$blacklisted_keyphrases = $blacklisted_keyphrases_lite;
	}
	if( !empty( $get_list_arr ) ) {
		if( $haystack_type === 'content' ) { return $blacklisted_keyphrases_lite; } else { return $blacklisted_keyphrases; }
	}
	/* Goes after array */
	$blacklist_status = FALSE;
	if( empty( $haystack ) ) { return FALSE; }
	if( $haystack_type === 'author' ) {
		/* Check 1: Testing for URLs and author domain in author name */
		if( preg_match( "~^https?~i", $haystack ) ) { return TRUE; }
		if( !empty( $url ) ) {
			$author_email_domain = rs_wpss_get_domain( $url, TRUE );
			$author_email_domain_rgx = rs_wpss_get_regex_phrase( $author_email_domain, '', 'N' );
			if( preg_match( $author_email_domain_rgx, $haystack ) ) { return TRUE; }
		}
		/* Check 2: Testing for max # words in author name, more than 7 is fail */
		$author_words = rs_wpss_count_words( $haystack );
		$word_max = 7; /* Default */
		if( !empty( $wpss_cl_active ) || !empty( $wpss_cak_active ) ) { $word_max = 10; } /* CL or CAK active */
		if( $author_words > $word_max ) { return TRUE; }
		/* Check 3: Testing for Odd Characters in author name */
		$odd_char_regex = "~[\@\*]+~"; /* Default */
		if( !empty( $wpss_cl_active ) || !empty( $wpss_cak_active ) ) { $odd_char_regex = "~(\@{2,}|\*)+~"; } /* CL or CAK active */
		if( preg_match( $odd_char_regex, $haystack ) ) { return TRUE; }
		/**
		 *  Check 4: Testing for *author name* surrounded by asterisks
		 *  Check 5: Testing for numbers and cash references ('1000','$5000', etc) in author name
		 */
		if( empty( $wpss_cl_active ) && empty( $wpss_cak_active ) && preg_match( "~(^|[\s\.])(\$([0-9]+)([0-9,\.]+)?|([0-9]+)([0-9,\.]{3,})|([0-9]{3,}))($|[\s])~", $haystack ) ) { return TRUE; }
		/* Final Check: The Blacklist - This is where Magic Parsing function comes from */
		foreach( $blacklisted_keyphrases as $i => $blacklisted_keyphrase ) {
			$blacklisted_keyphrase_rgx = rs_wpss_regexify( $blacklisted_keyphrase );
			$regex_check_phrase = rs_wpss_get_regex_phrase( $blacklisted_keyphrase_rgx, '', 'authorkw' );
			if( preg_match( $regex_check_phrase, $haystack ) ) { return TRUE; }
		}
	} elseif( $haystack_type === 'content' ) {
		/**
		 *  Parse content for links with Anchor Text
		 *  Test 1: Coming Soon
		 *  For possible use later - from old filter: ((payday|students?|title|onli?ne|short([\s\.\-_]*)term)([\s\.\-_]*)loan|cash([\s\.\-_]*)advance)
		 *  Final Check: The Blacklist
		 */
		$anchor_text_phrases = rs_wpss_parse_links( $haystack, 'anchor_text' );
		foreach( $anchor_text_phrases as $a => $anchor_text_phrase ) {
			foreach( $blacklisted_keyphrases_lite as $i => $blacklisted_keyphrase ) {
				$blacklisted_keyphrase_rgx = rs_wpss_regexify( $blacklisted_keyphrase );
				$regex_check_phrase = rs_wpss_get_regex_phrase( $blacklisted_keyphrase_rgx, '', 'authorkw' );
				if( preg_match( $regex_check_phrase, $anchor_text_phrase ) ) { return TRUE; }
			}
		}
	}
	return $blacklist_status;
}

function rs_wpss_cf_content_blacklist_chk( $haystack = NULL, $get_list_arr = FALSE ) {
	/**
	 *  Contact Form Content Blacklist Check
	 *  Use for the message content of any contact form
	 */
	$blacklisted_content = rs_wpss_rbkmd( rs_wpss_get_cf_content_blacklist(), 'de', TRUE );
	if( !empty( $get_list_arr ) ) { return $blacklisted_content; }
	/* Goes after array */
	$blacklist_status = FALSE;
	if( empty( $haystack ) ) { return FALSE; }
	$blacklisted_content_rgx = rs_wpss_get_regex_phrase( $blacklisted_content,'','red_str' );
	$blacklisted_content_rgx = str_replace( array( 'email', 'disclaimer', '2007', '\s+the\s+', '\s+an\s+', '\s+a\s+', ',', ), array( 'e?mail', '(disclaimer|p\.?s\.?)', '20[0-9]{2}', '\s+(the\s+)?', '\s+(an?\s+)?', '\s+(an?\s+)?', ',?', ), $blacklisted_content_rgx );
	if( preg_match( $blacklisted_content_rgx, $haystack ) ) { $blacklist_status = TRUE; }
	return $blacklist_status;
}

function rs_wpss_ubl_cache( $method = 'chk' ) {
	/**
	 *  Check if user has been added to blacklist cache
	 *  @since 1.8
	 *  $method: 'set','chk'
	 */
	/* Temporarily disabled in 1.8.9.2 for testing, re-enabled with modifications in 1.8.9.6 */
	if( TRUE === WPSS_TEMP_BL_DISABLE || !empty( $_SESSION['wpss_clear_blacklisted_user_'.WPSS_HASH] ) ) { rs_wpss_clear_ubl_cache(); return FALSE; }
	$wpss_ubl_cache_disable = get_option('spamshield_ubl_cache_disable');
	if( !empty( $wpss_ubl_cache_disable ) ) { rs_wpss_clear_ubl_cache(); return FALSE; }
	$ip = rs_wpss_get_ip_addr();
	if( $ip === WPSS_SERVER_ADDR ) { return FALSE; } /* Skip website IP address */
	if( rs_wpss_compare_ip_cbl( $ip, WPSS_SERVER_ADDR ) ) { return FALSE; } /* Skip anything on same C-Block as website */
	if( strpos( WPSS_SERVER_NAME_REV, WPSS_DEBUG_SERVER_NAME_REV ) !== 0 ) { if( rs_wpss_is_admin_ip( $ip ) ) { return FALSE; } }
	/* TO DO: Add logic for reverse proxies */
	$blacklist_status = FALSE;
	$wpss_lang_ck_key = 'UBR_LANG'; $wpss_lang_ck_val = 'default';
	$wpss_ubl_cache = get_option('spamshield_ubl_cache');
	if( empty( $wpss_ubl_cache ) ) { $wpss_ubl_cache = array(); }
	/* Check */
	if( !empty( $_SESSION['wpss_blacklisted_user_'.WPSS_HASH] ) || ( !empty( $_COOKIE[$wpss_lang_ck_key] ) && $_COOKIE[$wpss_lang_ck_key] === $wpss_lang_ck_val ) || ( !empty( $ip ) && in_array( $ip, $wpss_ubl_cache, TRUE ) ) || rs_wpss_referrer_blacklist_chk() ) { $blacklist_status = TRUE; }
	/* Set */
	if( !empty( $blacklist_status ) || !empty( $_SERVER['WPSS_SEC_THREAT'] ) || $method === 'set' ) {
		if( !empty( $ip ) && !in_array( $ip, $wpss_ubl_cache, TRUE ) ) { $wpss_ubl_cache[] = $ip; }
		$_SESSION['wpss_blacklisted_user_'.WPSS_HASH] = TRUE;
		update_option( 'spamshield_ubl_cache', $wpss_ubl_cache, FALSE );
	}
	return $blacklist_status;
}

function rs_wpss_clear_ubl_cache() {
	/**
	 *  Clear blacklist cache and delete previously set blacklist cookies
	 *  @since 1.9.0.6
	 */
	update_option( 'spamshield_ubl_cache', array(), FALSE );
	unset( $_SESSION['wpss_blacklisted_user_'.WPSS_HASH], $_SESSION['WPSS_SEC_THREAT_'.WPSS_HASH], $_SERVER['WPSS_SEC_THREAT'] );
	$_SESSION['wpss_clear_blacklisted_user_'.WPSS_HASH]	= TRUE;
	return;
}

function rs_wpss_is_admin_ip( $ip ) {
	/**
	 *  Check IP matches an admin IP...not to be used for authentication
	 *  @since 1.9.2
	 */
	$admin_ips = get_option( 'spamshield_admins' );
	if( empty( $admin_ips ) || !is_array( $admin_ips ) ) { $admin_ips = array(); update_option( 'spamshield_admins', $admin_ips ); return FALSE; }
	$admin_ips = rs_wpss_remove_expired_admins( $admin_ips );
	if( empty( $admin_ips ) ) { return FALSE; }
	if( isset( $admin_ips[$ip] ) ) { $_SERVER['WPSS_SEC_THREAT'] = FALSE; return TRUE; }
	return FALSE;
}

function rs_wpss_remove_expired_admins( $admin_ips = NULL ) {
	/**
	 *  Checks for expired admin IPs and validates IPs
	 */
	$admin_ips = empty( $admin_ips ) ? get_option( 'spamshield_admins' ) : $admin_ips;
	if( empty( $admin_ips ) || !is_array( $admin_ips ) ) { $admin_ips = array(); }
	$timenow = time();
	$timelim = 1209600; /* Time limit: 2 weeks (60*60*24*14) */
	foreach( $admin_ips as $ip => $t ) {
		$time_since_active = rs_wpss_timer( $t, $timenow, FALSE, NULL, TRUE, TRUE );
		if( $time_since_active > $timelim || !rs_wpss_is_valid_ip( $ip ) ) { unset( $admin_ips[$ip] ); }
	}
	if( rs_wpss_is_user_admin() ) {
		$current_ip = rs_wpss_get_ip_addr();
		if( rs_wpss_is_valid_ip( $current_ip ) ) {
			$admin_ips[$current_ip] = $timenow;
		}
	}
	update_option( 'spamshield_admins', $admin_ips );
	return $admin_ips;
}

function rs_wpss_regexify( $var ) {
	if( is_array( $var ) ) {
		$output = array();
		foreach( $var as $i => $string ) {
			$output[] = rs_wpss_regex_alpha_replace( $string );
		}
	} elseif( is_string( $var) ) {
		$output = rs_wpss_regex_alpha_replace( $var );
	} else { $output = $var; }
	return $output;
}

function rs_wpss_regex_alpha_replace( $string ) {
	/**
	 *  Preps international text for evaluating with regex
	 *  Translates 1337 (LEET) as well
	 *  TO DO: Possibly integrate remove_accents() WP function
	 */
	$tmp_string = rs_wpss_casetrans( 'lower', trim( $string ) );
	$input = array( /* 26 */
		"~(^|[\s\.])(online|internet|web(\s*(site|page))?)\s*gambling($|[\s])~i", "~(^|[\s\.])gambling\s*(online|internet|web(\s*(site|page))?)($|[\s])~i",
		"~(?!^online|internet|web(\s*(site|page))?$)(^|[\s\.])(online|internet|web(\s*(site|page))?)($|[\s])~i", "~(?!^india|china|russia|ukraine$)(^|[\s\.])(india|china|russia|ukraine)($|[\s])~i",
		"~(?!^offshore|outsource|data\s+entry$)(^|[\s\.])(offshore|outsource|data\s+entry)($|[\s])~i", "~ph~i",	"~(^|[\s\.])porn~i", "~ual($|[\s])~i", "~al($|[\s])~i", "~ay($|[\s])~i", "~ck($|[\s])~i",
		"~(ct|x)ion($|[\s])~i", "~te($|[\s])~i", "~(?!te$)e($|[\s])~i", "~er($|[\s])~i", "~ey($|[\s])~i", "~ic($|[\s])~i", "~ign($|[\s])~i", "~iou?r($|[\s])~i", "~ism($|[\s])~i", "~ous($|[\s])~i",
		"~oy($|[\s])~i", "~ss($|[\s])~i", "~tion($|[\s])~i", "~y($|[\s])~i", "~([abcdghklmnoprtw])($|[\s])~i",
		);
	$output = array( /* 26 */
		" (online|internet|web( (site|page))?)s? (bet(ting|s)?|blackjack|casinos?|gambl(e|ing)|poker) ", " (bet(ting|s)?|blackjack|casinos?|gambl(e|ing)|poker) (online|internet|web( (site|page))?)s? ",
		" (online|internet|web( (site|page))?)s? ", " (india|china|russia|ukraine) ", " (offshor(e(d|r|s|n|ly)?|ing)s?|outsourc(e(d|r|s|n|ly)?|ing)s?|data entry) ", "(ph|f)", "p(or|ro)n", "u(a|e)l(ly|s)? ",
		"al(ly|s)? ", "ays? ", "ck(e(d|r)?|ing)?s? ", "(ct|cc|x)ions? ", "t(e(d|r|s|n|ly)?|ing|ion)?s? ", "(e(d|r|s|n|ly)?|ing|ation)s? ", "(er|ing)s? ", "eys? ", "i(ck?|que)(s|ly)? ", "ign(e(d|r))?s? ",
		"iou?rs? ", "is(m|t) ", "ous(ly)? ", "oys? ", "ss(es)? ", "(t|c)ions? ", "(y|ie(d|r|s)?) ", "$1s? ",
		);
	$tmp_string = preg_replace( $input, $output, $tmp_string );
	$tmp_string = rs_wpss_casetrans( 'lower', trim( $tmp_string ) );
	$the_replacements = array(
		" "	=> "([\s\.,\;\:\?\!\/\|\@\(\)\[\]\{\}\-_]*)", "-" => "([\s\.,\;\:\?\!\/\|\@\(\)\[\]\{\}\-_]*)", "a"	=> "([a4\@àáâãäåæāăą])", "b" => "([b8ßƀƃƅ])", "c" => "([c¢©çćĉċč])", "d" => "([dďđ])",
		"e" => "([e3èéêëēĕėęěǝ])", "g" => "([g9ĝğġģ])", "h" => "([hĥħ])", "i" => "([i1yìíîïĩīĭį])", "k" => "([kķĸ])", "j" => "([jĵ])", "l" => "([l1ĺļľŀł])", "n" => "([nñńņňŉ])", "o" => "([o0ðòóôõöōŏőœ])",
		"r"	=> "([r®ŕŗř])", "s"	=> "([s5\$śŝşš])", "t" => "([t7ťŧţ])", "u" => "([uùúûüũūŭůűų])", "w" => "([wŵ])", "y" => "([y1i¥ýÿŷ])", "z" => "([z2sźżž])",
		);
	if( !preg_match( "~^NOMOD~i", $tmp_string ) ){
		$new_string = strtr( $tmp_string, $the_replacements );
	} else {
		$new_string = preg_replace( "~^NOMOD~i", "", $tmp_string );
	}
	return $new_string;
}

function rs_wpss_magic_parser( $keyphrase_needles = array(), $haystack = NULL ) {
	/**
	 *  The Magic Parser
	 *  Magically parse a large string of text for a number of keyphrases
	 *  The "magic" is that this will check for all kinds of text variations, accents, plurals, 1337 (LEET), etc.
	 *  Extremely accurate...same mechanism used in the rs_wpss_anchortxt_blacklist_chk() function
	 *  @since 1.9.7.8
	 *  @param array $keyphrase_needles	The array containing keyphrases to search haystack for
	 *  @param string $haystack			The string of text to search. This works well for large chunks of text such as contact form submissions.
	 *  @return bool TRUE if haystack contains any of the keyphrase needles, FALSE if it does not
	 */
	if( empty( $keyphrase_needles ) || empty( $haystack ) || !is_array( $keyphrase_needles ) || !is_string( $haystack ) ) { return FALSE; }
	foreach( $keyphrase_needles as $i => $keyphrase_needle ) {
		$keyphrase_needle_rgx = rs_wpss_regexify( $keyphrase_needle );
		$regex_check_phrase = rs_wpss_get_regex_phrase( $keyphrase_needle_rgx, '', 'authorkw' );
		if( preg_match( $regex_check_phrase, $haystack ) ) { return TRUE; }
	}
	return FALSE;
}

function rs_wpss_revdns_filter( $type = 'comment', $status = NULL, $ip = NULL, $rev_dns = NULL, $author = NULL, $email = NULL ) {
	$wpss_error_code = '';
	$blacklisted = FALSE;
	/* IP / PROXY INFO - BEGIN */
	global $wpss_ip_proxy_info;
	if( empty( $wpss_ip_proxy_info ) ) { $wpss_ip_proxy_info = rs_wpss_ip_proxy_info(); }
	extract( $wpss_ip_proxy_info );
	/* IP / PROXY INFO - END */
	$rev_dns = rs_wpss_casetrans('lower',trim($rev_dns));
	/* Detect Incapsula, and disable rs_wpss_ubl_cache - 1.8.9.6 */
	if( strpos( $rev_dns, '.ip.incapdns.net' ) !== FALSE ) { update_option( 'spamshield_ubl_cache_disable', TRUE ); }
	if( !empty( $author ) ) { $author = rs_wpss_casetrans( 'lower', $author ); }
	if( !empty( $email ) ) 	{ $author = rs_wpss_casetrans( 'lower', $email ); }
	switch ( $type ) {
		/* TO DO: Add types for EPC, XML-RPC, JSON/REST */
		case 'register':
			$pref = 'R-'; break;
		case 'contact':
			$pref = 'CF-'; break;
		case 'trackback':
			$pref = 'T-'; break;
		case 'misc form':
			$pref = 'MSC-'; break;
		case 'early post check':
			$pref = 'EPC-'; break;
		case 'contact form 7':
			$pref = 'CF7-'; break;
		case 'gravity forms':
			$pref = 'GF-'; break;
		case 'jetpack form':
			$pref = 'JP-'; break;
		case 'ninja forms':
			$pref = 'NF-'; break;
		case 'mailchimp form':
			$pref = 'MCF-'; break;
		default:
			$pref = '';
	}

	/* Test Reverse DNS Hosts */

	/* Bad Robot! */
	$banned_servers = array(
		/**
		 *  Web servers should not post comments/contact forms/register/use login pages
		 *  Regex must be *specific* and *tight* to prevent false pos
		 *  These are the worst of the worst offenders
		 */
		"REVD1023" => "~(^|\.)keywordspy\.com$~",
		"REVD1024" => "~(^|\.)clients\.your-server\.de$~",
		"REVD1025" => "~^rover-host\.com$~",
		"REVD1026" => "~^host\.lotosus\.com$~",
		"REVD1027" => "~^rdns\.softwiseonline\.com$~",
		"REVD1028" => "~^s([a-z0-9]+)\.websitehome\.co\.uk$~",
		"REVD1029" => "~\.opentransfer\.com$~",
		"REVD1030" => "~(^|\.)arkada\.rovno\.ua$~",
		"REVD1031" => "~^(host?|vm?)[0-9]+\.server[0-9]+\.vpn(999|2buy)\.com$~",
		"REVD1032" => "~^(ip[\.\-])?([0-9]{1,3}[\.\-]){4}(rdns\.(3e\.vc|as15003\.net|cloudradium\.com|continuumdatacenters\.com|dafeature\.com|mach9servers\.com|micfo\.com|mydnsprovider\.com|purewebtech\.net|racklot\.com|scalabledns\.com|servebyte\.com|smtp[0-9]+\.zhaoshengzixun\.com|ubiquity(servers)?\.(com|io))|static\.(hostnoc\.net|dimenoc\.com|reverse\.(softlayer\.com|queryfoundry\.net))|ip\.idealhosting\.net\.tr|chunkhost\.com|(rev\.)?poneytelecom\.eu|ipvnow.com|customer-(rethinkvps|incero)\.com|unknown\.steephost\.(net|com))$~",
		"REVD1033" => "~^(r?d)?ns([0-9]{1,3})\.(webmasters|rootleveltech)\.com$~",
		"REVD1034" => "~^server([0-9]+)\.(shadowbrokers|junctionmethod|([a-z0-9\-]+))(\.[a-z]{2,3}){1,2}[a-z]*$~",
		"REVD1035" => "~^(([0-9]{1,3}[\.\-]){4})?hosted\-[bn]y\.(ecatel\.net|hosthatch\.com|i3d\.net|inceptionhosting\.com|ipxcore\.com|leaseweb\.com|provisionhost\.com|reliablesite\.net|securefastserver\.com|slaskdatacenter\.pl|snel\.com )$~",
		"REVD1036" => "~^(([0-9]{1,3}[\.\-]){4}static|unassigned)\.quadranet\.com$~",
		"REVD1037" => "~^([a-z]{2}[0-9]*[\.\-])?[0-9]{1,3}[\.\-][0-9]{1,3}[\.\-][0-9]{1,3}[\.\-][0-9]{1,3}(\.[a-z]{2}\-([a-z]{3}\-)?((north|south)(east|west)|north|south|east|west)\-([0-9]+))?\.compute(\-[0-9]+)?\.amazonaws\.com$~",
		"REVD1038" => "~^([a-z]+)([0-9]{1,3})\.(guarmarr\.com|startdedicated\.com)$~",
		"REVD1039" => "~^([a-z0-9\-\.]+)\.rev\.sprintdatacenter\.pl$~",
		"REVD1040" => "~^ns([0-9]+)\.ovh\.net$~", /* OVH also provides ISP services, so do not block those */
		"REVD1041" => "~^(static|clients?)[\.\-]([0-9]{1,3}[\.\-]){4}(clients\.your-server\.de|customers\.filemedia\.net|hostwindsdns\.com)$~",
		"REVD1042" => "~^([0-9]{1,3}[\.\-][0-9]{1,3}[\.\-][0-9]{1,3}[\.\-][0-9]{1,3}|ks[0-9]+)[\.\-]kimsufi\.com$~",
		"REVD1043" => "~^([0-9]{1,3}[\.\-]){4}static-reverse\.[a-z]+-cloud\.serverhub\.com$~",
		"REVD1044" => "~^host[0-9]+\.sale24news\.com$~",
		"REVD1045" => "~\.unassigned\.userdns\.com$~",
		"REVD1046" => "~^h?([0-9]{1,3}[\.\-]){4}(rackcentre|host)\.redstation\.(co|net)\.uk$~",
		"REVD1047" => "~^([0-9]{1,3}[\.\-]){4}(hostvenom\.com|contina\.com|techserverdns\.com)$~",
		"REVD1048" => "~^[a-z]+[0-9]*\.zeehostbox\.com$~",
		"REVD1049" => "~^(h[\.\-])?([0-9]{1,3}[\.\-]){4}keyweb\.de$~",
		"REVD1051" => "~^(([0-9]{1,3}[\.\-]){4})?hosted[\.\-]by[\.\-]~",
		"REVD1052" => "~^no[\.\-]rdns[\.\-]yet\.ukservers\.com$~",
		"REVD1054" => "~^seo[0-9]*\.heilink\.com$~",
		"REVD1055" => "~^([0-9]+[a-z]+){2}\.rdns\.100tb\.com$~",
		"REVD1056" => "~^we\.love\.servers\.at\.ioflood\.com$~",
		"REVD1057" => "~^[a-z]+\.(highspeedinternetsecurity\.com|uaservers\.net)$~",
		"REVD1058" => "~^(([0-9]{1,3}[\.\-]){4})?host\.colocrossing\.com$~",
		"REVD1059" => "~^([0-9]{1,3}[\.\-]){4}bc\.googleusercontent\.com$~",
		"REVD1060" => "~^([0-9]{1,3}[\.\-]){4}static\.reverse\.lstn\.net$~",
		"REVD1061" => "~^dont\.try\.to\.f[uv]ck(ed)?\.us$~",
		"REVD1062" => "~^server\.chservers\.com$~",
		"REVD1063" => "~^host[0-9]+\.hostmonster\.com$~",
		"REVD1065" => "~\.rdns\.[a-z0-9\-\.]+(\.[a-z]{2,3}){1,2}[a-z]*$~",
		"REVD1066" => "~^sh3llbox\.hu$~",
		"REVD1067" => "~^unassigned\.psychz\.net$~",
		"REVD1068" => "~\.hosting\.reg\.ru$~",
		"REVD1069" => "~^(server|host|hosting|hosted\-[bn]y|unassigned|smtp|imap|pop|ns|dns|rdns|no\.rdns\.yet)([0-9]{1,3})?\.([a-z0-9\-\.]+)(\.[a-z]{2,3}){1,2}[a-z]*$~", 
		"REVD1070" => "~\.(port0\.org|unsecu\.re|vultr\.com|babylon\.network|members\.linode\.com|static\.host|static\.contabo\.net|privacyrepublic\.org|dataclub\.biz|multisec\.no|coldhak\.com|worldstream\.nl|reverse\.serversub\.com|cloudatcost\.com|voxility\.net|deltahost\.com\.ua)$~", /* A lot of malicious traffic from Tor nodes on these */
		"REVD1071" => "~\.ip\.secureserver\.net$~",
		"REVD1072" => "~^([0-9]{1,3}[\.\-]){4}\.tailormadeservers\.com$~",
		"REVD1073" => "~(research|scan|probe|benign)(.*)\.eecs\.umich\.edu$~",
		"REVD1074" => "~(^|\.)(censys|scans|zmap)\.io$~",
		);

	$banned_trackback_servers = array(
		/* ISP's Should not send trackbacks, ever */
		"REVD2102" => "~^c\-([0-9]{1,3}[\.\-]){4}hsd[0-9]+\.[a-z]{2,}\.comcast(\.[a-z]{2,3}){1,2}[a-z]*$~",
		"REVD2103" => "~([0-9]{1,3}[\.\-]){4}(static[\.\-])?[a-z]+broadband(\.[a-z]{2,3}){1,2}[a-z]*$~",
		"REVD2104" => "~^host([0-9]{1,3}[\.\-]){4}range[0-9]+\-[0-9]+\.btcentralplus(\.[a-z]{2,3}){1,2}[a-z]*$~",
		"REVD2105" => "~^ip([0-9]{1,3}[\.\-]){4}[a-z]{2,}\.[a-z]{2,}\.cox\.net$~",
		"REVD2106" => "~^([0-9]{1,3}[\.\-]){4}(([a-z]+\.)?broadband|gprs)\.kyivstar(\.[a-z]{2,3}){1,2}[a-z]*$~",
		"REVD2107" => "~^([0-9]{1,3}[\.\-]){2}broadband\.[a-z]{4,}(\.[a-z]{2,3}){1,2}[a-z]*$~",
		"REVD2108" => "~^abs\-static\-([0-9]{1,3}[\.\-]){4}aircel(\.[a-z]{2,3}){1,2}[a-z]*$~",
		"REVD2109" => "~^([0-9]{1,3}[\.\-]){4}res\.bhn\.net$~",
		"REVD2110" => "~^([0-9]{1,3}[\.\-]){4}static\-[a-z]+\.vsnl(\.[a-z]{2,3}){1,2}[a-z]*$~",
		"REVD2111" => "~^cpc([0-9]{1,3})\-([a-z]{4})([0-9]{1,3})\-([0-9]{1,3})\-([0-9]{1,3})\-cust([0-9]{1,3})\.([0-9]{1,3})\-([0-9]{1,3})\.cable\.virginm(\.[a-z]{2,3}){1,2}[a-z]*$~",
		"REVD2112" => "~([0-9]{1,3}[\.\-]){4}lightspeed\.[a-z]{5,6}\.sbcglobal(\.[a-z]{2,3}){1,2}[a-z]*$~",
		"REVD2113" => "~^(static\-)?([0-9]{1,3}[\.\-]){1,4}tataidc(\.[a-z]{2,3}){1,2}[a-z]*$~",
		"REVD2114" => "~([0-9]{1,3}[\.\-]){4}asianet(\.[a-z]{2,3}){1,2}[a-z]*$~",
		"REVD2115" => "~([0-9]{1,3}[\.\-]){4}ras\.beamtele(\.[a-z]{2,3}){1,2}[a-z]*$~",
		"REVD2116" => "~([0-9]{1,3}[\.\-]){4}(bol|mtnl)(\.[a-z]{2,3}){1,2}[a-z]*$~",
		"REVD2117" => "~([0-9]{1,3}[\.\-]){4}reverse\.spectranet(\.[a-z]{2,3}){1,2}[a-z]*$~",
		"REVD2118" => "~([0-9]{1,3}[\.\-]){4}vasaicable(\.[a-z]{2,3}){1,2}[a-z]*$~",
		"REVD2119" => "~([0-9]{1,3}[\.\-]){4}dsl\.ovh(\.[a-z]{2,3}){1,2}[a-z]*$~",
		"REVD2120" => "~([0-9]{1,3}[\.\-]){4}[a-z]+\.(res|biz)\.rr(\.[a-z]{2,3}){1,2}[a-z]*$~",
		"REVD2121" => "~^sol\-fttb\.([0-9]{1,3}[\.\-]){4}\.sovam(\.[a-z]{2,3}){1,2}[a-z]*$~",
		"REVD2122" => "~^[a-f0-9]{8}\.virtua\.com\.br$~",
		/**
		 *  "REVDXXXX" => "~^([0-9]{1,3}[\.\-]){4}(board|broad|dail|dial)\.([a-z]{2}\.){2}dynamic\.163data\.com\.cn$~",
		 *  Covered by REVD2000
		 */
		);

	$banned_xmlrpc_rest_servers = array(
		/* Banned XML-RPC & REST Servers */
		"REVD3100" => "~\.opentransfer\.com$~",
		);

	$banned_tor_exit_nodes = array(
		/* Banned Tor Exit Nodes */
		"REVD4100" => "~tor(\-|\.)?(e?x(\-|\.)?it|relay|proxy|node|gateway)~",
		"REVD4101" => "~(e?x(\-|\.)?it|relay|proxy|node|gateway)[0-9]*(\-|\.)?tor(\-|\.)~",
		"REVD4102" => "~(^|\-|\.)tor(eador|ero)?(\-?[0-9]+)?(\-|\.)~",
		"REVD4103" => "~(^|\-|\.)exit\-?[0-9]*\.~",
		"REVD4104" => "~^torsrv[a-z0-9]{1}\.~",
		"REVD4106" => "~\.tor(proxy|servers?|relays?)(\.[a-z]{2,3}){1,2}[a-z]*$~",
		"REVD4107" => "~\.ipredator\.se$~",
		"REVD4108" => "~\.privintl\.org$~",
		"REVD4109" => "~^vektort[0-9]+\.uaservers\.net$~",
		"REVD4110" => "~^\.proxad\.net$~",
		);

	if( $type === 'trackback' ) { $banned_servers = $banned_trackback_servers; }
	if( rs_wpss_is_xmlrpc() || rs_wpss_is_doing_rest() ) { $banned_servers = $banned_xmlrpc_rest_servers; }
	$banned_servers = array_merge( $banned_servers, $banned_tor_exit_nodes );

	if( $type === 'trackback' && FALSE === strpos( $rev_dns, 'www.dynamic.' ) && preg_match( "~(^dynamic([\.\-][0-9]){2,4}|\.dynamic\.)~i", $rev_dns ) ) {
		$wpss_error_code .= ' '.$pref.'REVD2000'; $blacklisted = TRUE;
	} elseif( $type === 'trackback' && FALSE === strpos( $rev_dns, 'www.broadband.' ) && preg_match( "~(^broadband([\.\-][0-9]){2,4}|\.broadband\.)~i", $rev_dns ) ) {
		$wpss_error_code .= ' '.$pref.'REVD2001'; $blacklisted = TRUE;
	} elseif( $type === 'trackback' && ( FALSE !== strpos( $rev_dns, '.dsl.dyn.' ) || FALSE !== strpos( $rev_dns, '.dyn.dsl.' ) ) ) {
		$wpss_error_code .= ' '.$pref.'REVD2010'; $blacklisted = TRUE;
	}
	/*
	elseif( $type === 'trackback' && strpos( $rev_dns, '.dial-up.' ) !== FALSE && strpos( $rev_dns, 'www.dial-up.' ) === FALSE ) {
		$wpss_error_code .= ' '.$pref.'REVD2011'; $blacklisted = TRUE;
	}
	*/
	elseif( $type === 'comment' || $type === 'register' || $type === 'contact' || ( !rs_wpss_is_doing_cron() && !rs_wpss_is_local_request() ) ) {
		foreach( $banned_servers as $error_code => $regex_phrase ) {
			if( preg_match( $regex_phrase, $rev_dns ) ) {
				$wpss_error_code .= ' '.$pref.$error_code; $blacklisted = TRUE;
				if( rs_wpss_is_xmlrpc() || rs_wpss_is_doing_rest() ) { rs_wpss_ubl_cache( 'set' ); }
				break;
			}
		}
	}

	if( empty( $blacklisted ) && !empty( $author ) && !empty( $email ) && ( $type === 'comment' || $type === 'register' ) ) {
		/* The 8's Pattern - from relakks.com - Anonymous surfing, powered by bots */
		if( preg_match( "~^anon-([0-9]+)-([0-9]+)\.relakks\.com$~", $rev_dns ) && preg_match( "~^([a-z]{8})$~", $author ) && preg_match( "~^([a-z]{8})\@([a-z]{8})\.com$~", $email ) ) {
			/* anon-###-##.relakks.com spammer pattern */
			$wpss_error_code .= ' '.$pref.'REVDA-1050';
			$blacklisted = TRUE;
		}
		/* The 8's - also coming from from the likes of rackcentre.redstation.net.uk */
	}

	/**
	 *  Check for spoofed Reverse DNS entries - common with hackers
	 *  When Miscellaneous Form Spam Prevention enabled, protects against XML-RPC brute force amplification attacks
	 */
	if( empty( $blacklisted ) && !rs_wpss_is_pingback() && !rs_wpss_is_local_request() && '[Possibly Forged]' === $fcrdns && ( rs_wpss_is_xmlrpc() || rs_wpss_is_doing_rest() ) && !rs_wpss_is_valid_ip( $rev_dns ) && !rs_wpss_domain_exists( $rev_dns ) ) {
		$wpss_error_code .= ' '.$pref.'REVDA-1060';
		$blacklisted = TRUE;
	}

	if( !empty( $wpss_error_code ) ) { $status = '3'; } /* Was 2, changed to 3 - V1.8.4 */
	$rev_dns_filter_data = array( 'status' => $status, 'error_code' => $wpss_error_code, 'blacklisted' => $blacklisted );
	return $rev_dns_filter_data;
}

function rs_wpss_skiddie_ua_check( $user_agent_lc = NULL, $type = NULL ) {
	/**
	 *  Undisguised User-Agents commonly used by script kiddies to attack/spam WP.
	 *  There is no reason for a human or Trackback/Pingback/XML-RPC to use one of these UA strings.
	 *  @since 1.7.9
	 */
	if( empty( $user_agent_lc ) ) { $user_agent_lc = rs_wpss_get_user_agent( TRUE, TRUE ); }
	if( empty( $user_agent_lc ) ) { return TRUE; }

	if( $type === 'pingback' && 0 !== strpos( $user_agent_lc, 'the incutio xml-rpc php library -- wordpress/' ) ) { $_SERVER['WPSS_SEC_THREAT'] = TRUE; return TRUE; }

	/* The Short List */
	if( preg_match( "~(^php/|anonymous|bandit|clshttp|collector|curl|copier|download|exploit|extractor|fetch|f[uv]ck|grabber|h[a4]ck|htdig|http_get_vars|http\-?client|httplib|httrack|iopus\-|jakarta|java|larbin|libcurl|libweb|libwww|lwp(\-trivial|\:\:simple)|madfox|mechanize|nutch|offline|phantomjs|phpcrawl|pycurl|python|ruby|scanner|script|siphon|slimerjs|stripper|sucker|synapse|wget|winhttprequest|wordpress\ hash\ grabber|zgrab/|^mozilla\ [1-4](\.[0-9]+)?|firefox/([1-2]?[0-9]|30)(\.[0-9]+)+|chrome/([1-2]?[0-9]|30)(\.[0-9]+)+|msie\ [1-6](\.[0-9]+)?;|windows\ (nt\ 5\.0|9[58]|2000)|(\ |/)20(0[0-9])[0-9]{4}|^mozilla/[1-5]\.[0-9]+\ \(compatible;\ msie\ [1-8](\.[0-9]+)?;\ win(dows)?\ (nt\ [0-5]\.[0-9]+|xp|me|9[0-9a-z]+|2000)|windows\ nt.*macintosh|chrome/.*firefox/|mozilla/5\.0.*mozilla/5\.0|^\}|\<\/?script([\ ]+)?\>|\<\?(.*)\?\>)~i", $user_agent_lc ) ) { $_SERVER['WPSS_SEC_THREAT'] = TRUE; return TRUE; }
	/* TO DO: Add Long List */

	if( 'pingback' === $type || 'trackback' === $type ) {
		global $is_chrome, $is_waterfox, $is_firefox, $is_iphone, $is_macIE, $is_winIE, $is_IE, $is_edge, $is_gecko, $is_opera, $is_safari, $is_lynx, $is_NS4;
		rs_wpss_get_browser();
		if( wp_is_mobile() || $is_waterfox || $is_firefox || $is_chrome || $is_IE || $is_gecko || $is_opera || $is_safari || $is_iphone || $is_lynx || $is_NS4 ) { $_SERVER['WPSS_SEC_THREAT'] = TRUE; return TRUE; }
	}

	if( ( $type === 'comment' || $type === 'contact' || $type === 'register' ) && !rs_wpss_is_doing_rest() && !rs_wpss_is_xmlrpc() ) {
		$user_agent_word_count = rs_wpss_count_words( $user_agent_lc );
		if( !empty( $user_agent_word_count ) && $user_agent_word_count < 3 ) { $_SERVER['WPSS_SEC_THREAT'] = TRUE; return TRUE; }
	}

	return FALSE;
}

/* BLACKLISTS - END */

function rs_wpss_precheck_pingback_spam( $pagelinkedfrom, $pagelinkedto ) {
	/**
	 *  This will knock out almost 100% of Pingback Spam, faster than WordPress' standard checks, with lower server load
	 *  @since 1.9.8.5
	 */
	if( !defined( 'WPSS_XMLRPC_PINGBACK' ) ) { define( 'WPSS_XMLRPC_PINGBACK', TRUE ); }
	global $wpss_pingback_inprog; $wpss_pingback_inprog = TRUE;
	if( rs_wpss_is_local_request() ) { return $pagelinkedfrom; }
	$comment_post_ID = url_to_postid( $pagelinkedto );
	$commentdata = array(
		'comment_type'					=> 'pingback',
		'comment_author'				=> '',
		'comment_author_email'			=> '',
		'comment_author_url'			=> $pagelinkedfrom,
		'comment_content'				=> '',
		'comment_post_url'				=> $pagelinkedto,
		'comment_post_ID'				=> $comment_post_ID,
		'comment_post_title'			=> get_the_title( $comment_post_ID ),
		'comment_post_type'				=> get_post_type( $comment_post_ID ),
		'comment_post_comments_open'	=> comments_open( $comment_post_ID ),
		'comment_post_pings_open'		=> @pings_open( $comment_post_ID ),
		'comment_wpss_cid'				=> '',
		'comment_wpss_ccid'				=> '',
		'body_content_len'				=> 0,
		);

	/* Timer Start - Comment Processing */
	$commentdata['start_time_comment_processing'] = $commentdata['start_time_content_filter'] = microtime(TRUE);

	global $spamshield_options; if( empty( $spamshield_options ) ) { $spamshield_options = get_option('spamshield_options'); }
	rs_wpss_update_session_data($spamshield_options);

	/* 1ST - Skiddie UA Check */
	if( rs_wpss_skiddie_ua_check( NULL, 'pingback' ) ) {
		$content_filter_status	= $commentdata['content_filter_status'] = 3;
		$wpss_error_code		= $commentdata['wpss_error_code'] = 'PUA1004';
		$commentdata = rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
		rs_wpss_update_accept_status( $commentdata, 'r', 'Line: '.__LINE__, $wpss_error_code );
		if( !empty( $spamshield_options['comment_logging'] ) ) { rs_wpss_log_data( $commentdata, $wpss_error_code ); }
		return '';
	}

	/* 2ND - TR2 - Pingback False IP Check */
	$commentdata			= rs_wpss_trackback_ip_filter( $commentdata, $spamshield_options );
	$content_filter_status	= $commentdata['content_filter_status'];
	$wpss_error_code		= trim( $commentdata['wpss_error_code'] );
	if( !empty( $content_filter_status ) ) {
		rs_wpss_update_accept_status( $commentdata, 'r', 'Line: '.__LINE__, $wpss_error_code );
		if( !empty( $spamshield_options['comment_logging'] ) ) { rs_wpss_log_data( $commentdata, $wpss_error_code ); }
		return '';
	}

	/* 3RD - Pingback Pre Filter */
	$commentdata 			= rs_wpss_pingback_pre_filter( $commentdata, $spamshield_options );
	$content_filter_status 	= $commentdata['content_filter_status'];
	$wpss_error_code		= trim( $commentdata['wpss_error_code'] );
	if( !empty( $content_filter_status ) ) {
		rs_wpss_update_accept_status( $commentdata, 'r', 'Line: '.__LINE__, $wpss_error_code );
		if( !empty( $spamshield_options['comment_logging'] ) ) { rs_wpss_log_data( $commentdata, $wpss_error_code ); }
		return '';
	}

	return $pagelinkedfrom;
}

function rs_wpss_check_comment_spam( $commentdata ) {
	/* Timer Start - Comment Processing */
	$commentdata['start_time_comment_processing'] = microtime(TRUE);

	global $spamshield_options; if( empty( $spamshield_options ) ) { $spamshield_options = get_option('spamshield_options'); }
	rs_wpss_update_session_data($spamshield_options);

	$wpss_error_code 	= $wpss_js_key_bypass = '';
	$bypass_tests 		= FALSE;

	/* Add New Tests for Logging - BEGIN */
	$post_jsonst 		= !empty( $_POST[WPSS_JSONST] ) ? trim( $_POST[WPSS_JSONST] ) : '';
	$post_ref2xjs 		= !empty( $_POST[WPSS_REF2XJS] ) ? trim( $_POST[WPSS_REF2XJS] ) : '';
	$post_jsonst_lc 	= rs_wpss_casetrans( 'lower', $post_jsonst );
	$post_ref2xjs_lc 	= rs_wpss_casetrans( 'lower', $post_ref2xjs ); /* For both logging and testing */
	$ref2xjs_lc			= rs_wpss_casetrans( 'lower', WPSS_REF2XJS ); /* For testing later on, not logging */
	if( !empty( $post_ref2xjs ) ) {
		$ref2xJS = rs_wpss_casetrans( 'lower', addslashes( urldecode( $post_ref2xjs ) ) );
		$ref2xJS = str_replace( '%3a', ':', $ref2xJS );
		$ref2xJS = str_replace( ' ', '+', $ref2xJS );
		$wpss_javascript_page_referrer = esc_url_raw( $ref2xJS );
	} else { $wpss_javascript_page_referrer = '[None]'; }

	if( $post_jsonst_lc  === 'ns1' || $post_jsonst_lc  === 'ns2' || $post_jsonst_lc  === 'ns3' || $post_jsonst_lc  === 'ns4' ) { $wpss_jsonst = $post_jsonst; } else { $wpss_jsonst = '[None]'; }

	$commentdata['comment_post_title']			= get_the_title($commentdata['comment_post_ID']);
	$commentdata['comment_post_url']			= get_permalink($commentdata['comment_post_ID']);
	$commentdata['comment_post_type']			= get_post_type($commentdata['comment_post_ID']);
	$commentdata['comment_post_comments_open']	= comments_open($commentdata['comment_post_ID']);
	$commentdata['comment_post_pings_open']		= @pings_open($commentdata['comment_post_ID']);
	$commentdata['javascript_page_referrer']	= $wpss_javascript_page_referrer;
	$commentdata['jsonst']						= $wpss_jsonst;

	$wpss_comment_author_email_lc				= rs_wpss_casetrans('lower',$commentdata['comment_author_email']);

	$commentdata_comment_content_lc_deslashed	= stripslashes(rs_wpss_casetrans('lower',$commentdata['comment_content']));
	$commentdata['body_content_len']			= rs_wpss_strlen($commentdata_comment_content_lc_deslashed);
	unset( $commentdata_comment_content_lc_deslashed, $wpss_javascript_page_referrer, $wpss_jsonst );

	/**
	 *  CREATE COMMENT WPSSID - BEGIN
	 *  @since 1.7.7
	 */
	$wpsseid_args 						= array( 'name' => $commentdata['comment_author'], 'email' => $commentdata['comment_author_email'], 'url' => $commentdata['comment_author_url'], 'content' => $commentdata['comment_content'] );
	$wpsseid 							= rs_wpss_get_wpss_eid( $wpsseid_args );
	$commentdata['comment_wpss_cid']	= $wpsseid['eid'];
	$commentdata['comment_wpss_ccid']	= $wpsseid['ecid'];
	/* CREATE COMMENT WPSSID - END */

	/* Add New Tests for Logging - END */


	/* User Authorization - BEGIN */

	/* Don't use elseif() for these tests - we are stacking $wpss_error_code_addendum results */

	$wpss_error_code_addendum = '';
	/* 1) is_admin() - If in Admin, don't test so user can respond directly to comments through admin */
	if( is_admin() ) {
			$bypass_tests = TRUE;
			$wpss_error_code_addendum .= ' 1-ADMIN';
		}

	/* 2) current_user_can( 'moderate_comments' ) - If user has Admin or Editor level access, don't test */
	if( current_user_can( 'moderate_comments' ) ) {
			$bypass_tests = TRUE;
			$wpss_error_code_addendum .= ' 2-MODCOM';
		}

	if( current_user_can( 'publish_posts' ) ) {
		/* Added Author Requirement - current_user_can( 'publish_posts' ) - v 1.4.7 */
		global $current_user;
		$current_user			= wp_get_current_user();
		$wpss_display_name 		= $current_user->display_name;
		$wpss_user_firstname 	= $current_user->user_firstname;
		$wpss_user_lastname 	= $current_user->user_lastname;
		$wpss_user_email		= $current_user->user_email;
		$wpss_user_url			= $current_user->user_url;
		$wpss_user_login 		= $current_user->user_login;
		$wpss_user_id	 		= $current_user->ID;

		if( !empty( $wpss_user_email ) ) {
			$wpss_user_email_parts				= explode( '@', $wpss_user_email );
			if( !empty( $wpss_user_email_parts[1] ) ) {
			$wpss_user_email_domain				= $wpss_user_email_parts[1];
			} else { $wpss_user_email_domain 		= ''; }
			$wpss_user_email_domain_no_w3		= preg_replace( "~^(ww[w0-9]|m)\.~i", "", $wpss_user_email_domain );
			$wpss_user_email_domain_no_w3_regex	= rs_wpss_preg_quote( $wpss_user_email_domain_no_w3 );
		}
		if( !empty( $wpss_user_url ) ) {
			$wpss_user_domain					= rs_wpss_get_domain( $wpss_user_url );
			$wpss_user_domain_no_w3 			= preg_replace( "~^(ww[w0-9]|m)\.~i", "", $wpss_user_domain );
			$wpss_user_domain_no_w3_regex		= rs_wpss_preg_quote( $wpss_user_domain_no_w3 );
		}
		$wpss_server_domain_no_w3 				= preg_replace( "~^(ww[w0-9]|m)\.~i", "", WPSS_SERVER_NAME );

		/* 3) If user is logged in, Author, and email is from same domain as website, don't test */
		if( !empty( $wpss_user_email_domain_no_w3 ) && preg_match( "~(^|\.)$wpss_user_email_domain_no_w3_regex$~i", $wpss_server_domain_no_w3 ) ) {
			$bypass_tests = TRUE;
			$wpss_error_code_addendum .= ' 3-AEMLDOM';
		}
		/* 4) If user is logged in, Author, and url is same domain as website, don't test */
		if( !empty( $wpss_user_domain_no_w3 ) && preg_match( "~(^|\.)$wpss_user_domain_no_w3_regex$~i", $wpss_server_domain_no_w3 ) ) {
			$bypass_tests = TRUE;
			$wpss_error_code_addendum .= ' 4-AURLDOM';
		}

	}

	/* 5) Whitelist */
	if( !empty( $spamshield_options['enable_whitelist'] ) && rs_wpss_whitelist_check( $wpss_comment_author_email_lc ) ) {
		$bypass_tests = TRUE;
		$wpss_error_code_addendum .= ' 5-WHITELIST';
	}

	if( TRUE === $bypass_tests ) {
		$wpss_error_code = 'No Error';
		/* $wpss_error_code .= $wpss_error_code_addendum; */
	}
	/* Timer End - Part 1 */
	$wpss_end_time_part_1 = microtime(TRUE);
	$wpss_total_time_part_1 = rs_wpss_timer( $commentdata['start_time_comment_processing'], $wpss_end_time_part_1, FALSE, 6, TRUE );
	$commentdata['total_time_part_1'] = $wpss_total_time_part_1;
	/* User Authorization - END */

	if( TRUE !== $bypass_tests ) {
		/* ONLY IF NOT ADMINS, EDITORS - BEGIN */

		/* First Do JS/Cookies Test */

		/**
		 *  JS/Cookies TEST - BEGIN
		 *  Rework this
		 */
		if( $commentdata['comment_type'] !== 'trackback' && $commentdata['comment_type'] !== 'pingback' ) {
			/* If Comment is not a trackback or pingback */
			global $wpss_comment_inprog; $wpss_comment_inprog = TRUE;

			/* Timer Start - JS/Cookies Filter */
			$wpss_start_time_jsck_filter = microtime(TRUE);
			$commentdata['start_time_jsck_filter'] = $wpss_start_time_jsck_filter;

			$wpss_ck_key_bypass = $wpss_js_key_bypass = FALSE;
			if( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) ) { $wpss_ck_key_bypass = TRUE; }
			if( FALSE === $wpss_ck_key_bypass ) {
				if( empty( $_COOKIE ) ) {
					/* JS/CK Test 00 - Added 1.9.8.5 - Failed 0 Cookie Test - Part of the JavaScript/Cookies Layer */
					$wpss_error_code .= ' COOKIE-0';
					$commentdata['wpss_error_code'] = trim( $wpss_error_code );
					rs_wpss_exit_jsck_filter( $commentdata, $spamshield_options, $wpss_error_code );
				}
				if( rs_wpss_ck_sess_o() ) {
					/* JS/CK Test 01 - Added 1.9.8.5 - Only has SESSION Cookie */
					$wpss_error_code .= ' COOKIE-01';
					$commentdata['wpss_error_code'] = trim( $wpss_error_code );
					rs_wpss_exit_jsck_filter( $commentdata, $spamshield_options, $wpss_error_code );
				}
			}
			$wpss_key_values 		= rs_wpss_get_key_values(); extract( $wpss_key_values );
			$wpss_jsck_cookie_val	= !empty( $_COOKIE[$wpss_ck_key] )	? $_COOKIE[$wpss_ck_key]	: '';
			$wpss_jsck_field_val	= !empty( $_POST[$wpss_js_key] )	? $_POST[$wpss_js_key]		: '';
			$wpss_jsck_jquery_val	= !empty( $_POST[$wpss_jq_key] )	? $_POST[$wpss_jq_key]		: '';

			if( rs_wpss_admin_jp_fix( TRUE ) ) { /* Check if JP Comments active - made compatible 1.9.2 */
				$wpss_js_key_bypass = TRUE;
			}
			if( FALSE === $wpss_ck_key_bypass ) {
				if( $wpss_jsck_cookie_val !== $wpss_ck_val ) {
					/**
					 *  JS/CK Test 01
					 *  Failed the Cookie Test - Part of the JavaScript/Cookies Layer
					 */
					$wpss_error_code .= ' COOKIE-1';
					$commentdata['wpss_error_code'] = trim( $wpss_error_code );
					rs_wpss_exit_jsck_filter( $commentdata, $spamshield_options, $wpss_error_code );
				}
			}
			/**
			 *  ALSO PART OF BAD ROBOTS TEST - BEGIN - Will have to add modifier for JS/CK TESTS only and add callback & args to rs_wpss_bad_robot_blacklist_chk() before it can be implemented here
			 *  Test JS Referrer for Obvious Scraping Spambots
			 */
			if( !empty( $post_ref2xjs ) && strpos( $post_ref2xjs_lc, $ref2xjs_lc ) !== FALSE ) {
				/* JS/CK Test 02 */
				$wpss_error_code .= ' REF-2-1023-1';
				$commentdata['wpss_error_code'] = trim( $wpss_error_code );
				rs_wpss_exit_jsck_filter( $commentdata, $spamshield_options, $wpss_error_code );
			}
			/* ALSO PART OF BAD ROBOTS TEST - END */
			/* JavaScript Off NoScript Test - JSONST - will only be sent by Scraping Spambots */
			if( $post_jsonst_lc === 'ns1' || $post_jsonst_lc  === 'ns2' || $post_jsonst_lc  === 'ns3' || $post_jsonst_lc  === 'ns4' ) {
				/* JS/CK Test 03 */
				$wpss_error_code .= ' JSONST-1000-1';
				$commentdata['wpss_error_code'] = trim( $wpss_error_code );
				rs_wpss_exit_jsck_filter( $commentdata, $spamshield_options, $wpss_error_code );
			}

			if( FALSE === $wpss_js_key_bypass ) {
				if( $wpss_jsck_field_val !== $wpss_js_val ) {
					/**
					 *  JS/CK Test 04
					 *  Failed the FVFJS Test
					 *  Part of the JavaScript/Cookies Layer
					 */
					$wpss_error_code .= ' FVFJS-1';
					$commentdata['wpss_error_code'] = trim( $wpss_error_code );
					rs_wpss_exit_jsck_filter( $commentdata, $spamshield_options, $wpss_error_code );
				}
			}
			/* Timer End - JS/Cookies Filter */
			$wpss_end_time_jsck_filter = microtime(TRUE);
			$wpss_total_time_jsck_filter = rs_wpss_timer( $wpss_start_time_jsck_filter, $wpss_end_time_jsck_filter, FALSE, 6, TRUE );
			$commentdata['total_time_jsck_filter'] = $wpss_total_time_jsck_filter;
			if( !empty( $wpss_error_code ) ) {
				$commentdata['wpss_error_code'] = trim( $wpss_error_code );
				rs_wpss_exit_jsck_filter( $commentdata, $spamshield_options, $wpss_error_code );
			}
		}
		/* JS/Cookies TEST - END */

		/* 2ND - Trackbacks/Pingbacks */
		if( 'trackback' === $commentdata['comment_type'] || 'pingback' === $commentdata['comment_type'] ) {
			$wpss_comment_inprog = FALSE;
			/* 1ST - TR1 - Trackback/Pingback Content Filter */
			$commentdata 			= rs_wpss_trackback_content_filter( $commentdata, $spamshield_options );
			$content_filter_status 	= $commentdata['content_filter_status'];
			$wpss_error_code		= trim( $commentdata['wpss_error_code'] );
			if( !empty( $content_filter_status ) ) { /* Same actions as TR2 - Needs Trackback exit filter similar to rs_wpss_exit_jsck_filter() */
				rs_wpss_update_accept_status( $commentdata, 'r', 'Line: '.__LINE__, $wpss_error_code );
				if( !empty( $spamshield_options['comment_logging'] ) ) { rs_wpss_log_data( $commentdata, $wpss_error_code ); }
				rs_wpss_denied_post_response( $commentdata, 'vague' );
			}
			/* 2ND - TR2 - Trackback False IP Check */
			if( 'trackback' === $commentdata['comment_type'] ) {
				$commentdata			= rs_wpss_trackback_ip_filter( $commentdata, $spamshield_options );
				$content_filter_status	= $commentdata['content_filter_status'];
				$wpss_error_code		= trim( $commentdata['wpss_error_code'] );
				if( !empty( $content_filter_status ) ) { /* Same actions as TR1 - Needs Trackback exit filter similar to rs_wpss_exit_jsck_filter() */
					rs_wpss_update_accept_status( $commentdata, 'r', 'Line: '.__LINE__, $wpss_error_code );
					if( !empty( $spamshield_options['comment_logging'] ) ) { rs_wpss_log_data( $commentdata, $wpss_error_code ); }
					rs_wpss_denied_post_response( $commentdata, 'vague' );
				}
			}
		} else { /* Comments only - No Pingbacks or Trackbacks */
			$wpss_comment_inprog = TRUE;

			/* 3RD - (Was 1ST), test if comment is too short or too long */
			$commentdata			= rs_wpss_content_length( $commentdata, $spamshield_options );
			$content_short_status	= $commentdata['content_short_status'];
			$content_long_status	= $commentdata['content_long_status']; /* Added 1.7.9 */

			if( empty( $content_short_status ) && empty( $content_long_status ) ) {
				/* If it doesn't fail the comment length tests, run it through the content filter. This is where the magic happens... */

				/* 4TH - Full Comment Content Filter (No Pingbacks or Trackbacks) */
				$commentdata			= rs_wpss_comment_content_filter( $commentdata, $spamshield_options );
				$content_filter_status	= $commentdata['content_filter_status'];
				/* Now we have a lot more power to work with */
			}
			$wpss_comment_inprog = FALSE;
		}

		$wpss_error_code = trim( $commentdata['wpss_error_code'] );

		if( !empty( $content_short_status ) ) {
			rs_wpss_update_accept_status( $commentdata, 'r', 'Line: '.__LINE__, $wpss_error_code );
			if( !empty( $spamshield_options['comment_logging'] ) ) { rs_wpss_log_data( $commentdata, $wpss_error_code ); }
			rs_wpss_denied_post_response( $commentdata, 'short' );
		} elseif( !empty( $content_long_status ) ) {
			rs_wpss_update_accept_status( $commentdata, 'r', 'Line: '.__LINE__, $wpss_error_code );
			if( !empty( $spamshield_options['comment_logging'] ) ) { rs_wpss_log_data( $commentdata, $wpss_error_code ); }
			rs_wpss_denied_post_response( $commentdata, 'long' );
		} elseif( $content_filter_status == '2' ) {
			/**
			 *  Up to 1.8.2: Only comment filter using this is rs_wpss_revdns_filter(). Used in rs_wpss_bad_robot_blacklist_chk() and CF.
			 *  1.8.4 on: No filters are using this.
			 */
			rs_wpss_update_accept_status( $commentdata, 'r', 'Line: '.__LINE__, $wpss_error_code );
			if( !empty( $spamshield_options['comment_logging'] ) ) { rs_wpss_log_data( $commentdata, $wpss_error_code ); }
			rs_wpss_denied_post_response( $commentdata, 'network' );
		} elseif( $content_filter_status == '3' ) { /* Added 1.8 */
			rs_wpss_update_accept_status( $commentdata, 'r', 'Line: '.__LINE__, $wpss_error_code );
			if( !empty( $spamshield_options['comment_logging'] ) ) { rs_wpss_log_data( $commentdata, $wpss_error_code ); }
			rs_wpss_denied_post_response( $commentdata, 'vague' );
		} elseif( $content_filter_status == '10' ) {
			rs_wpss_update_accept_status( $commentdata, 'r', 'Line: '.__LINE__, $wpss_error_code );
			if( !empty( $spamshield_options['comment_logging'] ) ) { rs_wpss_log_data( $commentdata, $wpss_error_code ); }
			rs_wpss_denied_post_response( $commentdata, 'proxy' );
		} elseif( $content_filter_status == '100' ) {
			rs_wpss_update_accept_status( $commentdata, 'r', 'Line: '.__LINE__, $wpss_error_code );
			if( !empty( $spamshield_options['comment_logging'] ) ) { rs_wpss_log_data( $commentdata, $wpss_error_code ); }
			rs_wpss_denied_post_response( $commentdata, 'blacklist' );
		} elseif( !empty( $content_filter_status ) ) {
			rs_wpss_update_accept_status( $commentdata, 'r', 'Line: '.__LINE__, $wpss_error_code );
			if( !empty( $spamshield_options['comment_logging'] ) ) { rs_wpss_log_data( $commentdata, $wpss_error_code ); }
			rs_wpss_denied_post_content_filter( $commentdata );
		}

		/* ONLY IF NOT ADMINS, EDITORS - END */
	}

	/* No Error - Not a Spam Comment - Accepted */
	if( !empty( $spamshield_options['comment_logging_all'] ) && ( empty( $wpss_error_code ) || ( !empty( $wpss_error_code ) && strpos( $wpss_error_code, 'No Error' ) === 0 ) ) ) {
		$wpss_error_code = 'No Error';
		$commentdata['wpss_error_code'] = trim( $wpss_error_code );
		rs_wpss_update_accept_status( $commentdata, 'a', 'Line: '.__LINE__ );
		rs_wpss_log_data( $commentdata, $wpss_error_code );
	}
	$wpss_comment_inprog = FALSE;
	return $commentdata;
}


/* REJECT BLOCKED SPAM COMMENTS - BEGIN */

function rs_wpss_denied_post_js_cookie( $commentdata = array() ) {
	$error_txt = rs_wpss_error_txt();
	$error_msg_alt = '<strong>'.$error_txt.':</strong> ' . __( 'Sorry, there was an error. Please be sure JavaScript and Cookies are enabled in your browser and try again.', 'wp-spamshield' );
	$error_msg = '<span style="font-size:12px;"><strong>'.$error_txt.': ' . __( 'JavaScript and Cookies are required in order to post a comment.', 'wp-spamshield' ) . '</strong><br /><br />'.WPSS_EOL;
	$error_msg .= '<noscript>' . __( 'Status: JavaScript is currently disabled.', 'wp-spamshield' ) . '<br /><br /></noscript>'.WPSS_EOL;
	$error_msg .= '<strong>' . __( 'Please be sure JavaScript and Cookies are enabled in your browser. Then, please hit the back button on your browser, and try posting your comment again. (You may need to reload the page.)', 'wp-spamshield' ) . '</strong><br /><br />'.WPSS_EOL;
	$error_msg .= '<br /><hr noshade />'.WPSS_EOL;
	if( !empty( $_COOKIE['SJECT15'] ) && $_COOKIE['SJECT15'] === 'CKON15' ) {
		$error_msg .= __( 'If you feel you have received this message in error (for example if JavaScript and Cookies are in fact enabled and you have tried to post several times), there is most likely a technical problem (could be a plugin conflict or misconfiguration). Please contact the author of this site, and let them know they need to look into it.', 'wp-spamshield' ) . '<br />'.WPSS_EOL;
		$error_msg .= '<hr noshade /><br />'.WPSS_EOL;
	}
	$error_msg .= '</span>'.WPSS_EOL;
	WP_SpamShield::wp_die( $error_msg, TRUE );
}

function rs_wpss_denied_post_content_filter( $commentdata = array() ) {
	$error_txt = rs_wpss_error_txt();
	$error_msg_alt = '<span style="font-size:12px;"><strong>'.$error_txt.':</strong> ' . __( 'Comments have been temporarily disabled to prevent spam. Please try again later.', 'wp-spamshield' ) . '</span>'; /* Stop spammers without revealing why. */
	$error_msg = '<span style="font-size:12px;"><strong>'.$error_txt.': ' . __( 'Your comment appears to be spam.', 'wp-spamshield' ) . '</strong><br /><br />'.WPSS_EOL;
	if( $commentdata['wpss_error_code'] === '10500A-BL' && strpos( $commentdata['comment_author'], '@' ) !== FALSE ) {
		$error_msg .= sprintf( __( '"%1$s" appears to be spam. Please enter a different value in the <strong> %2$s </strong> field.', 'wp-spamshield' ), sanitize_text_field($commentdata['comment_author']), __( 'Name' ) ) . '<br /><br />'.WPSS_EOL;
	}
	$error_msg .= __( 'Please go back and check all parts of your comment submission (including name, email, website, and comment content).', 'wp-spamshield' ) . '</span>'.WPSS_EOL;
	if( rs_wpss_is_user_logged_in() ) {
		$error_msg .= '<br /><br />'.WPSS_EOL;
		$error_msg .= '<span style="font-size:12px;">' . __( 'If you are a logged in user, and you are seeing this message repeatedly, then you may need to check your registered user information for spam data.', 'wp-spamshield' ) . '</span>'.WPSS_EOL;
	}
	WP_SpamShield::wp_die( $error_msg, TRUE );
}

function rs_wpss_denied_post_response( $commentdata = array(), $error ) {
	$error_txt = rs_wpss_error_txt();
	$error_msgs = array(
		'short'		=> '<span style="font-size:12px;"><strong>'.$error_txt.':</strong> ' . apply_filters( 'wpss_blocked_comment_response_short', __( 'Your comment was too short. Please go back and try your comment again.', 'wp-spamshield' ) ) . '</span>',
		'long'		=> '<span style="font-size:12px;"><strong>'.$error_txt.':</strong> ' . apply_filters( 'wpss_blocked_comment_response_long', __( 'Your comment was too long. Please go back and try your comment again.', 'wp-spamshield' ) ) . '</span>',
		'network'	=> '<span style="font-size:12px;"><strong>'.$error_txt.': ' . apply_filters( 'wpss_blocked_comment_response_network', __( 'Your location has been identified as part of a reported spam network. Comments have been disabled to prevent spam.', 'wp-spamshield' ) ) . '</strong></span>',
		'vague'		=> '<span style="font-size:12px;"><strong>'.$error_txt.': ' . apply_filters( 'wpss_blocked_comment_response_vague', __( 'Comments have been temporarily disabled to prevent spam. Please try again later.', 'wp-spamshield' ) ) . '</strong><br /><br /></span>',
		'proxy'		=> '<span style="font-size:12px;"><strong>'.$error_txt.': ' . apply_filters( 'wpss_blocked_comment_response_proxy_l1', __( 'Your comment has been blocked because the website owner has set their spam filter to not allow comments from users behind proxies.', 'wp-spamshield' ) ) . '</strong><br /><br />' . apply_filters( 'wpss_blocked_comment_response_proxy_l2', __( 'If you are a regular commenter or you feel that your comment should not have been blocked, please contact the site owner and ask them to modify this setting.', 'wp-spamshield' ) ) . '</span>',
		'blacklist'	=> '<span style="font-size:12px;"><strong>'.$error_txt.': ' . apply_filters( 'wpss_blocked_comment_response_blacklist_l1', __( 'Your comment has been blocked based on the website owner\'s blacklist settings.', 'wp-spamshield' ) ) . '</strong><br /><br />' . apply_filters( 'wpss_blocked_comment_response_blacklist_l2', __( 'If you feel this is in error, please contact the site owner by some other method.', 'wp-spamshield' ) ) . '</span>',
	);
	$error_msg = $error_msgs[$error];
	WP_SpamShield::wp_die( $error_msg, TRUE );
}

/* REJECT BLOCKED SPAM COMMENTS - END */


/* COMMENT SPAM FILTERS - BEGIN */

function rs_wpss_content_length( $commentdata, $spamshield_options ) {
	/* Timer Start  - Content Filter */
	if( empty( $commentdata['start_time_content_filter'] ) ) {
		$wpss_start_time_content_filter				= microtime(TRUE);
		$commentdata['start_time_content_filter']	= $wpss_start_time_content_filter;
	}
	$content_short_status 						= $content_long_status = $wpss_error_code = ''; /* Must go before tests */
	$commentdata_comment_content				= $commentdata['comment_content'];
	$commentdata_comment_content_lc				= rs_wpss_casetrans('lower',$commentdata_comment_content);
	$commentdata_comment_content_lc_deslashed	= stripslashes($commentdata_comment_content_lc);
	$comment_length 							= $commentdata['body_content_len'];
	$comment_min_length							= ( !empty( $spamshield_options['comment_min_length'] ) && is_int( $spamshield_options['comment_min_length'] ) ) ? $spamshield_options['comment_min_length'] : '15';
	$comment_max_length 						= 15360; /* 15kb */
	$commentdata_comment_type					= $commentdata['comment_type'];
	if( $commentdata_comment_type !== 'trackback' && $commentdata_comment_type !== 'pingback' ) {
		if( $comment_length < $comment_min_length ) {
			$content_short_status = TRUE; $wpss_error_code .= ' SHORT15-'.$comment_min_length;
		}
		if( $comment_length > $comment_max_length ) {
			$content_long_status = TRUE; $wpss_error_code .= ' LONG15K';
		}
	}
	if( !empty( $wpss_error_code ) ) {
		$wpss_error_code = trim( $wpss_error_code );
		rs_wpss_update_session_data($spamshield_options);
		/* Timer End - Content Filter */
		$wpss_end_time_content_filter 				= microtime(TRUE);
		$wpss_total_time_content_filter 			= rs_wpss_timer( $commentdata['start_time_content_filter'], $wpss_end_time_content_filter, FALSE, 6, TRUE );
		$commentdata['total_time_content_filter']	= $wpss_total_time_content_filter;
	}
	$commentdata['content_short_status'] 	= $content_short_status;
	$commentdata['content_long_status'] 	= $content_long_status;
	$commentdata['wpss_error_code'] 		= $wpss_error_code;
	return $commentdata;
}

function rs_wpss_exit_jsck_filter( $commentdata, $spamshield_options, $wpss_error_code ) {
	/**
	 *  Exit JS/CK Filter
	 *  This fires when a JavaScript/Cookies spam test is failed.
	 */
	$commentdata['wpss_error_code']			= $wpss_error_code = trim( $wpss_error_code );
	/* Timer End - Content Filter */
	$wpss_end_time_jsck_filter 				= microtime(TRUE);
	$wpss_total_time_jsck_filter 			= rs_wpss_timer( $commentdata['start_time_jsck_filter'], $wpss_end_time_jsck_filter, FALSE, 6, TRUE );
	$commentdata['total_time_jsck_filter']	= $wpss_total_time_jsck_filter;
	rs_wpss_update_accept_status( $commentdata, 'r', 'Line: '.__LINE__, $wpss_error_code );
	if( !empty( $spamshield_options['comment_logging'] ) ) { rs_wpss_log_data( $commentdata, $wpss_error_code ); }
	rs_wpss_denied_post_js_cookie( $commentdata );
}

function rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status ) {
	/**
	 *  Exit Content Filter
	 *  This fires when an algo spam test is failed.
	 */
	$commentdata['wpss_error_code']				= $wpss_error_code = trim( $wpss_error_code );
	if( strpos( $wpss_error_code, '0-BL' ) !== FALSE || strpos( $wpss_error_code, '0-ECBL' ) !== FALSE ) {
		rs_wpss_append_log_data( 'Blacklisted user detected. Comments have been temporarily disabled to prevent spam. ERROR CODE: '.$wpss_error_code, FALSE );
	}
	/* Timer End - Content Filter */
	$wpss_end_time_content_filter 				= microtime(TRUE);
	$wpss_total_time_content_filter 			= rs_wpss_timer( $commentdata['start_time_content_filter'], $wpss_end_time_content_filter, FALSE, 6, TRUE );
	$commentdata['total_time_content_filter']	= $wpss_total_time_content_filter;
	$commentdata['content_filter_status']		= $content_filter_status;
	return $commentdata;
}

function rs_wpss_pingback_pre_filter( $commentdata, $spamshield_options ) {
	/**
	 *  Pingback Pre Filter
	 *  This will knock out most of Pingback Spam, faster than WordPress' standard checks, with lower server load
	 */

	$content_filter_status 						= $wpss_error_code = ''; /* Must go before tests */
	$block_all_pingbacks 						= $spamshield_options['block_all_pingbacks'];
	$commentdata_comment_type					= $commentdata['comment_type'];
	$commentdata_comment_author					= $commentdata['comment_author'];
	$commentdata_comment_author_deslashed		= stripslashes($commentdata_comment_author);
	$commentdata_comment_author_lc				= rs_wpss_casetrans('lower',$commentdata_comment_author);
	$commentdata_comment_author_lc_deslashed	= stripslashes($commentdata_comment_author_lc);
	$commentdata_comment_author_url				= $commentdata['comment_author_url'];
	$commentdata_comment_author_url_lc			= rs_wpss_casetrans('lower',$commentdata_comment_author_url);
	$commentdata_comment_author_url_domain_lc	= rs_wpss_get_domain($commentdata_comment_author_url_lc);
	$commentdata_comment_content				= $commentdata['comment_content'];
	$commentdata_comment_content_lc				= rs_wpss_casetrans('lower',$commentdata_comment_content);
	$commentdata_comment_content_lc_deslashed	= stripslashes($commentdata_comment_content_lc);

	$commentdata_remote_addr					= $ip = rs_wpss_get_ip_addr();
	$commentdata_remote_addr_lc					= rs_wpss_casetrans('lower',$commentdata_remote_addr);
	$commentdata_user_agent 					= rs_wpss_get_user_agent( TRUE, FALSE );
	$commentdata_user_agent_lc					= rs_wpss_casetrans('lower',$commentdata_user_agent);
	$commentdata_user_agent_lc_word_count 		= rs_wpss_count_words($commentdata_user_agent_lc);

	if( !empty( $block_all_pingbacks ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '3'; }
		$wpss_error_code .= ' P-BLOCKING-PINGBACKS';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* IP / PROXY INFO - BEGIN */
	global $wpss_ip_proxy_info;
	if( empty( $wpss_ip_proxy_info ) ) { $wpss_ip_proxy_info = rs_wpss_ip_proxy_info(); }
	extract( $wpss_ip_proxy_info );
	/* IP / PROXY INFO - END */

	/* REVDNS FILTER */
	$rev_dns_filter_data = rs_wpss_revdns_filter( 'trackback', $content_filter_status, $ip, $reverse_dns_lc, $commentdata_comment_author_lc_deslashed );
	$revdns_blacklisted = $rev_dns_filter_data['blacklisted'];
	if( !empty( $revdns_blacklisted ) ) {
		$content_filter_status = $rev_dns_filter_data['status'];
		$wpss_error_code .= $rev_dns_filter_data['error_code'];
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Blacklisted Domains Check */
	if( rs_wpss_domain_blacklist_chk( $commentdata_comment_author_url_domain_lc ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '3'; }
		$wpss_error_code .= ' P-10500AU-BL';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Check for URL Shorteners, Bogus Long URLs, Social Media, and Misc Spam Domains */
	if( rs_wpss_at_link_spam_url_chk( $commentdata_comment_author_url_lc, 'trackback' ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '3'; }
		$wpss_error_code .= ' P-10510AU-BL';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	$commentdata['wpss_error_code'] = trim( $wpss_error_code );
	$commentdata['content_filter_status'] = $content_filter_status;
	return $commentdata;

}

function rs_wpss_trackback_content_filter( $commentdata, $spamshield_options ) {
	/**
	 *  Trackback Content Filter
	 *  This will knock out 98% of Trackback Spam
	 *  Keeping this separate and before trackback IP filter because it's fast
	 *  If passes this, then next filter will take out the rest
	 */

	/* Timer Start  - Content Filter */
	if( empty( $commentdata['start_time_content_filter'] ) ) {
		$wpss_start_time_content_filter				= microtime(TRUE);
		$commentdata['start_time_content_filter']	= $wpss_start_time_content_filter;
	}

	$content_filter_status 						= $wpss_error_code = ''; /* Must go before tests */

	$block_all_trackbacks 						= $spamshield_options['block_all_trackbacks'];
	$block_all_pingbacks 						= $spamshield_options['block_all_pingbacks'];

	$commentdata_comment_type					= $commentdata['comment_type'];

	$commentdata_comment_author					= $commentdata['comment_author'];
	$commentdata_comment_author_deslashed		= stripslashes($commentdata_comment_author);
	$commentdata_comment_author_lc				= rs_wpss_casetrans('lower',$commentdata_comment_author);
	$commentdata_comment_author_lc_deslashed	= stripslashes($commentdata_comment_author_lc);
	$commentdata_comment_author_url				= $commentdata['comment_author_url'];
	$commentdata_comment_author_url_lc			= rs_wpss_casetrans('lower',$commentdata_comment_author_url);
	$commentdata_comment_author_url_domain_lc	= rs_wpss_get_domain($commentdata_comment_author_url_lc);
	$commentdata_comment_content				= $commentdata['comment_content'];
	$commentdata_comment_content_lc				= rs_wpss_casetrans('lower',$commentdata_comment_content);
	$commentdata_comment_content_lc_deslashed	= stripslashes($commentdata_comment_content_lc);
	/**
	 *  For 1-HT Test - Other version using rs_wpss_parse_links() is more robust but not needed yet - current implementation is faster.
	 */

	$commentdata_remote_addr					= rs_wpss_get_ip_addr();
	$commentdata_remote_addr_lc					= rs_wpss_casetrans('lower',$commentdata_remote_addr);
	$commentdata_user_agent 					= rs_wpss_get_user_agent( TRUE, FALSE );
	$commentdata_user_agent_lc					= rs_wpss_casetrans('lower',$commentdata_user_agent);
	$commentdata_user_agent_lc_word_count 		= rs_wpss_count_words($commentdata_user_agent_lc);
	$trackback_length 							= $commentdata['body_content_len'];
	$trackback_max_length 						= 3072; /* 3kb */

	/* TO DO: Re-do this section - some old that needs to be regexified. BEGIN*/
	$commentdata_comment_author_lc_spam_strong = '<strong>'.$commentdata_comment_author_lc_deslashed.'</strong>'; /* Trackbacks */
	$commentdata_comment_author_lc_spam_strong_dot1 = '...</strong>'; /* Trackbacks */
	$commentdata_comment_author_lc_spam_strong_dot2 = '...</b>'; /* Trackbacks */
	$commentdata_comment_author_lc_spam_strong_dot3 = '<strong>...'; /* Trackbacks */
	$commentdata_comment_author_lc_spam_strong_dot4 = '<b>...'; /* Trackbacks */
	$commentdata_comment_author_lc_spam_a1 = $commentdata_comment_author_lc_deslashed.'</a>'; /* Trackbacks/Pingbacks */
	$commentdata_comment_author_lc_spam_a2 = $commentdata_comment_author_lc_deslashed.' </a>'; /* Trackbacks/Pingbacks */
	/* TO DO: Re-do this section - some old that needs to be regexified. END*/

	if( rs_wpss_is_local_request() && $commentdata['comment_type'] === 'pingback' ) { $local_pingback = TRUE; } else { $local_pingback = FALSE; }

	if( !empty( $block_all_trackbacks ) && $commentdata['comment_type'] === 'trackback' ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '3'; }
		$wpss_error_code .= ' BLOCKING-TRACKBACKS ';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}
	if( !empty( $block_all_pingbacks ) && $commentdata['comment_type'] === 'pingback' ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '3'; }
		$wpss_error_code .= ' BLOCKING-PINGBACKS';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Check Length */
	if( $trackback_length > $trackback_max_length ) {
		/* There is no reason for an exceptionally long Trackback or Pingback. */
		if( empty( $content_filter_status ) ) { $content_filter_status = '3'; }
		$wpss_error_code .= ' T-LONG3K';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}
	/* Test User-Agents */

	/* TUA1001,TUA1002 - REMOVED 1.9.8.7 - rs_wpss_skiddie_ua_check() now includes this */

	if( rs_wpss_skiddie_ua_check( $commentdata_user_agent_lc, 'trackback' ) ) {
		/* There is no reason for a Trackback/Pingback to use one of these UA strings. Commonly used to attack/spam WP. */
		$content_filter_status = '3';
		$wpss_error_code .= ' TUA1004';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* TRACKBACK/PINGBACK SPECIFIC TESTS -  BEGIN */

	/* TRACKBACK COOKIE TEST - Trackbacks can't have cookies, but some fake ones do. SMH. */
	if( !empty( $_COOKIE ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '3'; }
		$wpss_error_code .= ' T-COOKIE';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Body Content - Check for excessive number of links (any) in trackback ( body_content ) */
	$trackback_count_http 	= rs_wpss_substr_count( $commentdata_comment_content_lc_deslashed, 'http://' );
	$trackback_count_https 	= rs_wpss_substr_count( $commentdata_comment_content_lc_deslashed, 'https://' );
	$trackback_num_links 	= $trackback_count_http + $trackback_count_https;
	$trackback_num_limit 	= 0;
	if( empty( $local_pingback ) && $trackback_num_links > $trackback_num_limit ) { /* Not using rs_wpss_parse_links() since this should be zero anyway, this is faster */
		/* Genuine trackbacks should have text only, not hyperlinks */
		if( empty( $content_filter_status ) ) { $content_filter_status = '3'; }
		$wpss_error_code .= ' T-1-HT';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	if( preg_match( "~\[\.{1,3}\]\s*\[\.{1,3}\]~i", $commentdata_comment_content_lc_deslashed ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '3'; }
		$wpss_error_code .= ' T200-1';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/**
	 *  T3000-1 - WordPress UA for a Trackback
	 *  DEPRECATED - Removed 1.7.5
	 */

	/* IP / PROXY INFO - BEGIN */
	global $wpss_ip_proxy_info;
	if( empty( $wpss_ip_proxy_info ) ) { $wpss_ip_proxy_info = rs_wpss_ip_proxy_info(); }
	extract( $wpss_ip_proxy_info );
	/* IP / PROXY INFO - END */

	$local_pingback_proxy = FALSE;
	if( !empty( $local_pingback ) && $ip_proxy === 'PROXY DETECTED' ) {
		/* For sites using proxies like Cloudflare, etc. - Added 1.9.6.8 */
		$local_pingback_proxy = TRUE;
	}

	if( empty( $local_pingback ) && empty( $local_pingback_proxy ) && $ip_proxy === 'PROXY DETECTED' ) {
		/* Check to see if Trackback/Pingback is using proxy. (With exceptions for sites using proxies such as Cloudflare.) Real ones don't since they come directly from a website/server. (Or they hide their tracks better.) */
		if( empty( $content_filter_status ) ) { $content_filter_status = '3'; }
		$wpss_error_code .= ' T1011-FPD-1';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	if( empty( $local_pingback ) ) {
		/* REVDNS FILTER */
		$rev_dns_filter_data = rs_wpss_revdns_filter( 'trackback', $content_filter_status, $ip, $reverse_dns_lc, $commentdata_comment_author_lc_deslashed );
		$revdns_blacklisted = $rev_dns_filter_data['blacklisted'];
		if( !empty( $revdns_blacklisted ) ) {
			$content_filter_status = $rev_dns_filter_data['status'];
			$wpss_error_code .= $rev_dns_filter_data['error_code'];
			return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
		}
	}

	/**
	 *  MISC - T1020-1, T2003-1, T2003-1, T2004-1, T2005-1, T2006-1, T2007-1-1, T2007-2-1, T2010-1, T3001-1, T3002-1, T3003-1-1, T3003-2-1, T3003-3-1, T9000 Variants
	 *  DEPRECATED - Removed 1.7.5
	 */

	/* Blacklisted Domains Check */
	if( rs_wpss_domain_blacklist_chk( $commentdata_comment_author_url_domain_lc ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '3'; }
		$wpss_error_code .= ' T-10500AU-BL';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Check for URL Shorteners, Bogus Long URLs, Social Media, and Misc Spam Domains */
	if( rs_wpss_at_link_spam_url_chk( $commentdata_comment_author_url_lc, 'trackback' ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '3'; }
		$wpss_error_code .= ' T-10510AU-BL';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Check Author URL for Exploits */
	if( rs_wpss_exploit_url_chk( $commentdata_comment_author_url_lc ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
		$wpss_error_code .= ' T-15000AU-XPL'; /* Added in 1.9.8.6 - From rs_wpss_comment_content_filter() */
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* TRACKBACK/PINGBACK SPECIFIC TESTS -  END */

	/**
	 *  return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	 */

	/* After rs_wpss_exit_content_filter() implemented, can remove following code - BEGIN */
	if( !empty( $wpss_error_code ) ) {
		$wpss_error_code = trim( $wpss_error_code );
		/* Timer End - Content Filter */
		$wpss_end_time_content_filter 				= microtime(TRUE);
		$wpss_total_time_content_filter 			= rs_wpss_timer( $commentdata['start_time_content_filter'], $wpss_end_time_content_filter, FALSE, 6, TRUE );
		$commentdata['total_time_content_filter']	= $wpss_total_time_content_filter;
	}
	/* After rs_wpss_exit_content_filter() implemented, can remove previous code - END */

	$commentdata['wpss_error_code'] = trim( $wpss_error_code );
	$commentdata['content_filter_status'] = $content_filter_status;
	return $commentdata;
}

function rs_wpss_trackback_ip_filter( $commentdata, $spamshield_options ) {
	/**
	 *  Trackback/Pingback IP Filter
	 *  This will knock out 99.99% of Trackback/Pingback Spam
	 *  WordPress does not validate IP addresses of incoming Pingbacks & Trackbacks
	 *  WordPress checks URL of Pingbacks for link to this blog, but not Trackbacks
	 *  Keeping this separate and before content filter because it's fast
	 *  If passes this, then content filter will take out the rest
	 */

	/* Timer Start  - Content Filter */
	if( empty( $commentdata['start_time_content_filter'] ) ) {
		$wpss_start_time_content_filter				= microtime(TRUE);
		$commentdata['start_time_content_filter']	= $wpss_start_time_content_filter;
	}

	$content_filter_status 				= $wpss_error_code = ''; /* Must go before tests */

	$commentdata_remote_addr			= rs_wpss_get_ip_addr();
	$commentdata_remote_addr_lc			= rs_wpss_casetrans('lower',$commentdata_remote_addr);
	$commentdata_comment_type			= $commentdata['comment_type'];
	$commentdata_comment_author_url		= $commentdata['comment_author_url'];
	$commentdata_comment_author_url_lc	= rs_wpss_casetrans('lower',$commentdata_comment_author_url);

	/* Check to see if IP Trackback client IP matches IP of Server where link is supposedly coming from */
	$trackback_domain = rs_wpss_get_domain($commentdata_comment_author_url_lc);
	$trackback_ip = rs_wpss_get_reverse_dns_ip($trackback_domain);
	if( rs_wpss_is_local_request() && $commentdata['comment_type'] === 'pingback' ) { $local_pingback = TRUE; } else { $local_pingback = FALSE; }
	if( empty( $local_pingback ) && $commentdata_remote_addr_lc !== $trackback_ip ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '3'; }
		$wpss_error_code .= ' TP1000-FIP-1';
	}

	if( !empty( $wpss_error_code ) ) {
		$wpss_error_code = trim( $wpss_error_code );
		/* Timer End - Content Filter */
		$wpss_end_time_content_filter 				= microtime(TRUE);
		$wpss_total_time_content_filter 			= rs_wpss_timer( $commentdata['start_time_content_filter'], $wpss_end_time_content_filter, FALSE, 6, TRUE );
		$commentdata['total_time_content_filter']	= $wpss_total_time_content_filter;
	}

	$commentdata['wpss_error_code']			= trim( $wpss_error_code );
	$commentdata['content_filter_status']	= $content_filter_status;
	return $commentdata;
}

function rs_wpss_comment_content_filter( $commentdata, $spamshield_options ) {
	/**
	 *  Comment Content Filter aka "The Algorithmic Layer"
	 *  Blocking the Obvious to Improve Human Spam Defense
	 */
	if( 'trackback' === $commentdata['comment_type'] || 'pingback' === $commentdata['comment_type'] ) { return $commentdata; }

	/* Timer Start  - Content Filter */
	if( empty( $commentdata['start_time_content_filter'] ) ) {
		$wpss_start_time_content_filter				= microtime(TRUE);
		$commentdata['start_time_content_filter']	= $wpss_start_time_content_filter;
	}

	$content_filter_status = $wpss_error_code = ''; /* Must go before tests */

	rs_wpss_update_session_data($spamshield_options);

	/* TEST 0 - See if user has already been blacklisted this session */
	if( !rs_wpss_is_user_logged_in() && ( rs_wpss_ubl_cache() ) ) {
		$wpss_error_code .= ' 0-BL'; $content_filter_status = '3';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* TEST HTA1000 - See if .htaccess already determined this is a robot */
	if( !empty( $_SERVER['WPSS_SEC_THREAT'] ) || !empty( $_SERVER['BHCS_SEC_THREAT'] ) ) {
		$_SERVER['WPSS_SEC_THREAT'];
		$wpss_error_code .= ' HTA1000'; $content_filter_status = '3';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	$post_ref2xjs 		= !empty( $_POST[WPSS_REF2XJS] ) ? trim( $_POST[WPSS_REF2XJS] ) : '';
	$post_ref2xjs_lc	= rs_wpss_casetrans( 'lower', $post_ref2xjs );

	/* CONTENT FILTERING - BEGIN */

	$commentdata_comment_post_id					= $commentdata['comment_post_ID'];
	$commentdata_comment_post_title					= $commentdata['comment_post_title'];
	$commentdata_comment_post_title_lc				= rs_wpss_casetrans('lower',$commentdata_comment_post_title);
	$commentdata_comment_post_title_lc_regex 		= rs_wpss_preg_quote($commentdata_comment_post_title_lc);
	$commentdata_comment_post_url					= $commentdata['comment_post_url'];
	$commentdata_comment_post_url_lc				= rs_wpss_casetrans('lower',$commentdata_comment_post_url);
	$commentdata_comment_post_url_lc_regex 			= rs_wpss_preg_quote($commentdata_comment_post_url_lc);

	$commentdata_comment_post_type					= $commentdata['comment_post_type'];
	/* Possible results: 'post', 'page', 'attachment', 'revision', 'nav_menu_item' */

	/* Next two are boolean */
	$commentdata_comment_post_comments_open			= $commentdata['comment_post_comments_open'];
	$commentdata_comment_post_pings_open			= $commentdata['comment_post_pings_open'];

	$commentdata_comment_author						= $commentdata['comment_author'];
	$commentdata_comment_author_deslashed			= stripslashes($commentdata_comment_author);
	$commentdata_comment_author_lc					= rs_wpss_casetrans('lower',$commentdata_comment_author);
	$commentdata_comment_author_lc_regex 			= rs_wpss_preg_quote($commentdata_comment_author_lc);
	$commentdata_comment_author_lc_words 			= rs_wpss_count_words($commentdata_comment_author_lc);
	$commentdata_comment_author_lc_space 			= ' '.$commentdata_comment_author_lc.' ';
	$commentdata_comment_author_lc_deslashed		= stripslashes($commentdata_comment_author_lc);
	$commentdata_comment_author_lc_deslashed_regex 	= rs_wpss_preg_quote($commentdata_comment_author_lc_deslashed);
	$commentdata_comment_author_lc_deslashed_words 	= rs_wpss_count_words($commentdata_comment_author_lc_deslashed);
	$commentdata_comment_author_lc_deslashed_space 	= ' '.$commentdata_comment_author_lc_deslashed.' ';
	$commentdata_comment_author_email				= $commentdata['comment_author_email'];
	$commentdata_comment_author_email_lc			= rs_wpss_casetrans('lower',$commentdata_comment_author_email);
	$commentdata_comment_author_email_lc_regex 		= rs_wpss_preg_quote($commentdata_comment_author_email_lc);
	$commentdata_comment_author_url					= $commentdata['comment_author_url'];
	$commentdata_comment_author_url_lc				= rs_wpss_casetrans('lower',$commentdata_comment_author_url);
	$commentdata_comment_author_url_lc_regex 		= rs_wpss_preg_quote($commentdata_comment_author_url_lc);
	$commentdata_comment_author_url_domain_lc		= rs_wpss_get_domain($commentdata_comment_author_url_lc);

	$commentdata_comment_content					= $commentdata['comment_content'];
	$commentdata_comment_content_lc					= rs_wpss_casetrans('lower',$commentdata_comment_content);
	$commentdata_comment_content_lc_deslashed		= stripslashes($commentdata_comment_content_lc);
	$commentdata_comment_content_extracted_urls 	= rs_wpss_parse_links( $commentdata_comment_content_lc_deslashed, 'url' ); /* Parse comment content for all URLs */
	$commentdata_comment_content_extracted_urls_at 	= rs_wpss_parse_links( $commentdata_comment_content_lc_deslashed, 'url_at' ); /* Parse comment content for Anchor Text Link URLs */
	$commentdata_comment_content_num_links 			= count( $commentdata_comment_content_extracted_urls ); /* Count extracted URLS from body content - Added 1.8.4 */
	$commentdata_comment_content_num_limit			= 3; /* Max number of links in comment body content */

	$replace_apostrophes							= array('’','`','&acute;','&grave;','&#39;','&#96;','&#101;','&#145;','&#146;','&#158;','&#180;','&#207;','&#208;','&#8216;','&#8217;');
	$commentdata_comment_content_lc_norm_apost 		= str_replace($replace_apostrophes,"'",$commentdata_comment_content_lc_deslashed);

	$commentdata_comment_type						= $commentdata['comment_type'];

	/*
	if( $commentdata_comment_type !== 'pingback' && $commentdata_comment_type !== 'trackback' ) {
		$commentdata_comment_type = 'comment';
	}
	*/

	$commentdata_user_agent 			= rs_wpss_get_user_agent( TRUE, FALSE );
	$commentdata_user_agent_lc			= rs_wpss_casetrans('lower',$commentdata_user_agent);

	$http_accept						= rs_wpss_get_http_accept( TRUE, TRUE );
	$http_accept_language 				= rs_wpss_get_http_accept( TRUE, TRUE, TRUE );
	$http_accept_encoding 				= rs_wpss_get_http_accept( TRUE, TRUE, FALSE, TRUE );

	$commentdata_remote_addr			= rs_wpss_get_ip_addr();
	$commentdata_remote_addr_regex 		= rs_wpss_preg_quote($commentdata_remote_addr);
	$commentdata_remote_addr_lc			= rs_wpss_casetrans('lower',$commentdata_remote_addr);
	$commentdata_remote_addr_lc_regex 	= rs_wpss_preg_quote($commentdata_remote_addr_lc);

	$commentdata_referrer				= rs_wpss_get_referrer();
	$commentdata_referrer_lc			= rs_wpss_casetrans('lower',$commentdata_referrer);
	$commentdata_php_self				= $_SERVER['PHP_SELF'];
	$commentdata_php_self_lc			= rs_wpss_casetrans('lower',$commentdata_php_self);

	$blog_server_ip 					= WPSS_SERVER_ADDR;
	$blog_server_name 					= WPSS_SERVER_NAME;

	/* IP / PROXY INFO - BEGIN */
	global $wpss_ip_proxy_info;
	if( empty( $wpss_ip_proxy_info ) ) { $wpss_ip_proxy_info = rs_wpss_ip_proxy_info(); }
	extract( $wpss_ip_proxy_info );
	/* IP / PROXY INFO - END */

	/**
	 *  Post Type Filter - INVALTY
	 *  Removed V 1.1.7 - Found Exception
	 */

	/* Simple Filters */

	/* BEING DEPRECATED... */
	$blacklist_word_combo_total_limit = 10; /* you may increase to 30+ if blog's topic is adult in nature - DEPRECATED */
	$blacklist_word_combo_total = 0;

	/* Body Content - Check for excessive number of links in message ( body_content ) - 1.8.4 */
	if( $commentdata_comment_content_num_links > $commentdata_comment_content_num_limit ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
		$wpss_error_code .= ' 1-HT';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/**
	 *  Authors Only - Do not use on Trackbacks/Pingbacks
	 *  Removed Filters 300-423 and replaced with Regex
	 */

	/* Author Blacklist Check - Invalid Author Names - Stopping Human Spam - Do not use on Trackbacks/Pingbacks */
	if( rs_wpss_anchortxt_blacklist_chk( $commentdata_comment_author_lc_deslashed, '', 'author', $commentdata_comment_author_url_lc ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
		$wpss_error_code .= ' 10500A-BL';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Regular Expression Tests - 2nd Gen - Comment Author/Author URL - BEGIN */

	/* 10500-13000 - Complex Test for terms in Comment Author/URL - $commentdata_comment_author_lc_deslashed/$commentdata_comment_author_url_domain_lc */

	/* Blacklisted Domains Check */
	if( rs_wpss_domain_blacklist_chk( $commentdata_comment_author_url_domain_lc ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
		$wpss_error_code .= ' 10500AU-BL';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Check for URL Shorteners, Bogus Long URLs, and Misc Spam Domains */
	if( rs_wpss_at_link_spam_url_chk( $commentdata_comment_author_url_lc ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
		$wpss_error_code .= ' 10510AU-BL';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Testing for a unique identifying string from the comment content in the Author URL Domain */
	preg_match( "~\s+([a-z0-9]{6,})$~i", $commentdata_comment_content_lc_deslashed, $wpss_str_matches );
	if( !empty( $wpss_str_matches[1] ) ) { $wpss_spammer_id_string = $wpss_str_matches[1]; } else { $wpss_spammer_id_string = ''; }
	$commentdata_comment_author_url_domain_lc_elements = explode( '.', $commentdata_comment_author_url_domain_lc );
	$commentdata_comment_author_url_domain_lc_elements_count = count( $commentdata_comment_author_url_domain_lc_elements ) - 1;
	if( !empty ( $wpss_spammer_id_string ) ) {
		$i = 0;
		/* The following line to prevent exploitation: */
		$i_max = 20;
		while( $i < $commentdata_comment_author_url_domain_lc_elements_count && $i < $i_max ) {
			if( !empty( $commentdata_comment_author_url_domain_lc_elements[$i] ) ) {
				if( $commentdata_comment_author_url_domain_lc_elements[$i] === $wpss_spammer_id_string ) {
					if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
					$wpss_error_code .= ' 10511AUA';
					return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
				}
			}
			++$i;
		}
	}

	/**
	 *  Potential Exploits
	 */

	/* Check Author URL for Exploits */
	if( rs_wpss_exploit_url_chk( $commentdata_comment_author_url_lc ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
		$wpss_error_code .= ' 15000AU-XPL'; /* Added in 1.4 - Replacing 15001AU-XPL and 15002AU-XPL, and adds additional protection */
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Regular Expression Tests - 2nd Gen - Comment Author/Author URL - END */

	$blacklist_word_combo_limit = 7;
	$blacklist_word_combo = 0;

	$i = 0;

	/* Regular Expression Tests - 2nd Gen - Comment Content - BEGIN */

	/* Miscellaneous Patterns that Keep Repeating */
	if( preg_match( "~^([0-9]{6})\s([0-9]{6})(.*)\s([0-9]{6})$~i", $commentdata_comment_content_lc_deslashed ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
		$wpss_error_code .= ' 10401C';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}
	/* Blacklisted Anchor Text Check - Links in Content - Stopping Human Spam - Not for Trackbacks/Pingbacks */
	if( rs_wpss_anchortxt_blacklist_chk( $commentdata_comment_content_lc_deslashed, '', 'content' ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
		$wpss_error_code .= ' 10500CAT-BL';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}
	/* Blacklisted Domains Check - Links in Content */
	if( rs_wpss_link_blacklist_chk( $commentdata_comment_content_lc_deslashed ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
		$wpss_error_code .= ' 10500CU-BL';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}
	/* Check Anchor Text Links for URL Shorteners, Bogus Long URLs, and Misc Spam Domains */
	if( rs_wpss_at_link_spam_url_chk( $commentdata_comment_content_extracted_urls_at ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
		$wpss_error_code .= ' 10510CU-BL'; /* Replacing 10510CU-MSC */
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Check all URLs in Comment Content for Exploits */
	if( rs_wpss_exploit_url_chk( $commentdata_comment_content_extracted_urls ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
		$wpss_error_code .= ' 15000CU-XPL'; /* Added in 1.4 */
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Check if Comment Content Contains *Only* URLs */
	if( !empty( $commentdata_comment_content_extracted_urls ) && is_array( $commentdata_comment_content_extracted_urls ) ) {
		$commentdata_comment_content_temp = str_replace( $commentdata_comment_content_extracted_urls, '', $commentdata_comment_content_lc_deslashed );
		$commentdata_comment_content_temp = trim( str_replace( array( "\s", WPSS_EOL, "\t", ' ', ), '', $commentdata_comment_content_temp ) );
		if( empty( $commentdata_comment_content_temp ) ) {
			if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
			$wpss_error_code .= ' 10410CU'; /* Added in 1.9.8.2 */
			return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
		}
	}

	/* Regular Expression Tests - 2nd Gen - Comment Content - END */


	/**
	 *  Test Comment Author
	 *  Words in Comment Author Repeated in Content - With Keyword Density
	 */
	$repeated_terms_filters			= array('.','-',':');
	$repeated_terms_temp_phrase		= str_replace($repeated_terms_filters,'',$commentdata_comment_author_lc_deslashed);
	$repeated_terms_test			= explode(' ',$repeated_terms_temp_phrase);
	$repeated_terms_test_count		= count($repeated_terms_test);
	$comment_content_total_words	= rs_wpss_count_words($commentdata_comment_content_lc_deslashed);
	$i = 0;
	while( $i < $repeated_terms_test_count ) {
		if( !empty( $repeated_terms_test[$i] ) ) {
			$repeated_terms_in_content_count = rs_wpss_substr_count( $commentdata_comment_content_lc_deslashed, $repeated_terms_test[$i] );
			$repeated_terms_in_content_str_len = rs_wpss_strlen($repeated_terms_test[$i]);
			if( $repeated_terms_in_content_count > 1 && $comment_content_total_words < $repeated_terms_in_content_count ) {
				$repeated_terms_in_content_count = 1;
			}
			$repeated_terms_in_content_density = ( $repeated_terms_in_content_count / $comment_content_total_words ) * 100;
			if( $repeated_terms_in_content_count >= 5 && $repeated_terms_in_content_str_len >= 4 && $repeated_terms_in_content_density > 40 ) {
				if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
				$wpss_error_code .= ' 9000-'.$i;
				return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
			}
		}
		++$i;
	}

	/* Comment Author and URL Tests */
	if( !empty( $commentdata_comment_author_url_lc ) && !empty( $commentdata_comment_author_lc_deslashed ) ) {
		/* Comment Author and Comment Author URL appearing in Content - REGEX VERSION */
		if( preg_match( "~(<\s*a\s+([a-z0-9\-_\.\?\='\"\:\(\)\{\}\s]*)\s*href|\[(url|link))\s*\=\s*(['\"])?\s*$commentdata_comment_author_url_lc_regex([a-z0-9\-_\/\.\?\&\=\~\@\%\+\#\:]*)(['\"])?(>|\])$commentdata_comment_author_lc_deslashed_regex(<|\[)\s*\/\s*a\s*(>|(url|link)\])~i", $commentdata_comment_content_lc_deslashed ) ) {
			if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
			$wpss_error_code .= ' 9100-1';
			return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
		}
		if( $commentdata_comment_author_url_lc === $commentdata_comment_author_lc_deslashed && !preg_match( "~https?\:/+~i", $commentdata_comment_author_url_lc ) && preg_match( "~(<\s*a\s+([a-z0-9\-_\.\?\='\"\:\(\)\{\}\s]*)\s*href|\[(url|link))\s*\=\s*(['\"])?\s*(https?\:/+[a-z0-9\-_\/\.\?\&\=\~\@\%\+\#\:]+)\s*(['\"])?\s*(>|\])$commentdata_comment_author_lc_deslashed_regex(<|\[)\s*\/\s*a\s*(>|(url|link)\])~i", $commentdata_comment_content_lc_deslashed ) ) {
			if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
			$wpss_error_code .= ' 9101';
			return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
		}
		if( preg_match( "~^((ww[w0-9]|m)\.)?$commentdata_comment_author_lc_deslashed_regex$~i", $commentdata_comment_author_url_domain_lc) && !preg_match( "~https?\:/+~i", $commentdata_comment_author_lc_deslashed ) ) {
			if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
			$wpss_error_code .= ' 9102';
			return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
		}
		if( $commentdata_comment_author_url_lc === $commentdata_comment_author_lc_deslashed && !preg_match( "~https?\:/+~i", $commentdata_comment_author_url_lc ) && preg_match( "~(https?\:/+[a-z0-9\-_\/\.\?\&\=\~\@\%\+\#\:]+)~i", $commentdata_comment_content_lc_deslashed ) ) {
			if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
			$wpss_error_code .= ' 9103';
			return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
		}
	}

	/**
	 *  Email Filters
	 *  New Test with Blacklists
	 */
	if( rs_wpss_email_blacklist_chk($commentdata_comment_author_email_lc) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
		$wpss_error_code .= ' 9200E-BL';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* TEST REFERRERS 1 - TO THE COMMENT PROCESSOR */
	if( strpos( WPSS_COMMENTS_POST_URL, $commentdata_php_self_lc ) !== FALSE && $commentdata_referrer_lc === WPSS_COMMENTS_POST_URL ) {
		/* Often spammers send the referrer as the URL for the wp-comments-post.php page. */
		if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
		$wpss_error_code .= ' REF-1-1011';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* TEST REFERRERS 2 - SPAMMERS SEARCHING FOR PAGES TO COMMENT ON */
	if( !empty( $post_ref2xjs ) ) {
		$ref2xJS = addslashes( urldecode( $post_ref2xjs ) );
		$ref2xJS = str_replace( '%3A', ':', $ref2xJS );
		$ref2xJS = str_replace( ' ', '+', $ref2xJS );
		$ref2xJS = esc_url_raw( $ref2xJS );
		$ref2xJS_lc = rs_wpss_casetrans( 'lower', $ref2xJS );
		if( preg_match( "~\.google\.co(m|\.[a-z]{2})~i", $ref2xJS ) && strpos( $ref2xJS_lc, 'leave a comment' ) !== FALSE ) {
			/* make test more robust for other versions of google & search query */
			if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
			$wpss_error_code .= ' REF-2-1021';
			return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
		}
		/* add Keyword Script Here */
	}

	/**
	 *  TEST REFERRERS 3 - TO THE PAGE BEING COMMENTED ON
	 *  DISABLED IN V1.5.9
	 */

	/* Spam Network - BEGIN */

	/**
	 *  PART OF BAD ROBOTS TEST - BEGIN
	 *  Test User-Agents
	 */

	/* UA1001 - REMOVED 1.9.8.7 - UA1003 - REMOVED 1.9.8.6 - rs_wpss_skiddie_ua_check() now includes this */

	if( rs_wpss_skiddie_ua_check( $commentdata_user_agent_lc, 'comment' ) ) {
		/* There is no reason for a human to use one of these UA strings. Commonly used to attack/spam WP. */
		$content_filter_status = '3'; /* Was 1, changed to 3 - V1.8.4 */
		$wpss_error_code .= ' UA1004';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}


	/**
	 *  Test HTTP_ACCEPT
	 */
	if( empty( $http_accept ) ) {
		$content_filter_status = '3'; /* Was 1, changed to 3 - V1.8.4 */
		$wpss_error_code .= ' HA1001';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}
	/* HA1002 REMOVED - 1.9.0.3 */
	if( $http_accept === '*' ) {
		$content_filter_status = '3'; /* Was 1, changed to 3 - V1.8.4 */
		$wpss_error_code .= ' HA1003';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}
	if( ( !empty( $_SERVER['WPSS_HA1004'] ) || $http_accept === '*/*' ) && !rs_wpss_is_ajax_request() ) {
		$content_filter_status = '3'; /* Added V1.9.8.5 */
		$wpss_error_code .= ' HA1004';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}
	/* More complex test for invalid 'HTTP_ACCEPT' */
	$http_accept_mod_1 = preg_replace( "~([\s\;]+)~", ",", $http_accept );
	$http_accept_elements = explode( ',', $http_accept_mod_1 );
	$http_accept_elements_count = count($http_accept_elements);
	$i = 0;
	/* The following line to prevent exploitation: */
	$i_max = 20;
	while( $i < $http_accept_elements_count && $i < $i_max ) {
		if( !empty( $http_accept_elements[$i] ) ) {
			if( $http_accept_elements[$i] === '*' ) {
				$content_filter_status = '3'; /* Was 1, changed to 3 - V1.8.4 */
				$wpss_error_code .= ' HA1010';
				return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
			}
		}
		++$i;
	}

	/* Test HTTP_ACCEPT_LANGUAGE */
	if( empty( $http_accept_language ) ) {
		$content_filter_status = '3'; /* Was 1, changed to 3 - V1.8.4 */
		$wpss_error_code .= ' HAL1001';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}
	if( $http_accept_language === '*' ) {
		$content_filter_status = '3'; /* Was 1, changed to 3 - V1.8.4 */
		$wpss_error_code .= ' HAL1002';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}
	/* More complex test for invalid 'HTTP_ACCEPT_LANGUAGE' */
	$http_accept_language_mod_1 = preg_replace( "~([\s\;]+)~", ",", $http_accept_language );
	$http_accept_language_elements = explode( ',', $http_accept_language_mod_1 );
	$http_accept_language_elements_count = count($http_accept_language_elements);
	$i = 0;
	/* The following line to prevent exploitation: */
	$i_max = 20;
	while( $i < $http_accept_language_elements_count && $i < $i_max ) {
		if( !empty( $http_accept_language_elements[$i] ) ) {
			if( $http_accept_language_elements[$i] === '*' && strpos( $commentdata_user_agent_lc, 'links (' ) !== 0 ) {
				$content_filter_status = '3'; /* Was 1, changed to 3 - V1.8.4 */
				$wpss_error_code .= ' HAL1010';
				return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
			}
		}
		++$i;
	}

	if( $http_accept_language === 'en-US,*' && $http_accept_encoding === 'gzip' ) { /* PhantomJS Sig */
		$wpss_error_code .= ' '.$pref.'HALE1001'; $blacklisted = TRUE;
	}

	/**
	 *  Test PROXY STATUS if option
	 *  Google Chrome Data Compression Proxy Bypass (Data Saver)
	 */
	if( $ip_proxy === 'PROXY DETECTED' && $ip_proxy_chrome_compression !== 'TRUE' && empty( $spamshield_options['allow_proxy_users'] ) ) {
		$content_filter_status = '10';
		$wpss_error_code .= ' PROXY1001';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Test Reverse DNS Hosts */
	$rev_dns_filter_data = rs_wpss_revdns_filter( 'comment', $content_filter_status, $ip, $reverse_dns_lc, $commentdata_comment_author_lc_deslashed, $commentdata_comment_author_email_lc );
	$revdns_blacklisted = $rev_dns_filter_data['blacklisted'];
	if( !empty( $revdns_blacklisted ) ) {
		$content_filter_status = $rev_dns_filter_data['status'];
		$wpss_error_code .= $rev_dns_filter_data['error_code'];
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Spam Network - END */


	/* Miscellaneous Preg Match Tests - Changed to regex in V1.8.4 */
	$wpss_misc_spam_phrases_to_check =
		array(
			'5000' => "~\[\.+\]\s+\[\.+\]~",
			'5001' => "~^<new\s+comment>$~i",
			'5003' => "~^([a-z0-9\s\.,!]{0,12})?((he.a?|h([ily]{1,2}))(\s+there)?|howdy|hello|bonjour|good\s+day)([\.,!])?\s+(([ily]{1,2})\s+know\s+)?th([ily]{1,2})s\s+([ily]{1,2})s\s+([a-z\s]{3,12}|somewhat|k([ily]{1,2})nd\s*of)?(of{1,2}\s+)?of{1,2}\s+top([ily]{1,2})c\s+(but|however)\s+([ily]{1,2})\s+(was\s+wonder([ily]{1,2})nn?g?|need\s+some\s+adv([ily]{1,2})ce)~i",
			'5004' => "~^th([ily]{1,2})s\s+([ily]{1,2})s\s+k([ily]{1,2})nd\s+of\s+off\s+top([ily]{1,2})c\s+but~i",
			);
			/* 5002 - Removed in V1.8.4 */
	foreach( $wpss_misc_spam_phrases_to_check as $ec => $rgx_phrase ) {
		if( preg_match( $rgx_phrase, $commentdata_comment_content_lc_deslashed ) ) {
			if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
			$wpss_error_code .= ' '.$ec;
			return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
		}
	}


	/* BOILERPLATE: Add common boilerplate/template spam phrases... Add Blacklist functions */

	/* Enhanced Comment Blacklist - 0-ECBL */
	if( !empty( $spamshield_options['enhanced_comment_blacklist'] ) && empty( $content_filter_status ) ) {
		if( rs_wpss_blacklist_check( $commentdata_comment_author_lc_deslashed, $commentdata_comment_author_email_lc, $commentdata_comment_author_url_lc, $commentdata_comment_content_lc_deslashed, $ip, $commentdata_user_agent_lc, '' ) ) {
			if( empty( $content_filter_status ) ) { $content_filter_status = '100'; }
			$wpss_error_code .= ' 0-ECBL';
			return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
		}
	}

	/* Timer End - Content Filter */
	$wpss_end_time_content_filter 				= microtime(TRUE);
	$wpss_total_time_content_filter 			= rs_wpss_timer( $commentdata['start_time_content_filter'], $wpss_end_time_content_filter, FALSE, 6, TRUE );
	$commentdata['total_time_content_filter']	= $wpss_total_time_content_filter;

	if( empty( $wpss_error_code ) ) {
		$wpss_error_code = 'No Error';
	} else {
		$wpss_error_code = trim( $wpss_error_code );
	}

	/**
	 *  $spamshield_error_data = array( $wpss_error_code, $blacklist_word_combo, $blacklist_word_combo_total );
	 */

	$commentdata['wpss_error_code']			= trim( $wpss_error_code );
	$commentdata['content_filter_status']	= $content_filter_status;

	return $commentdata;

	/* CONTENT FILTERING - END */
}

/* COMMENT SPAM FILTERS - END */

/* SPAM CHECK FOR OTHER PLUGINS AND FORMS - BEGIN */

function rs_wpss_misc_form_spam_check() {
	/**
	 *  Checks all miscellaneous form POST submissions for spam
	 *  Misc Form Spam Check - Layer 1
	 *  @since 1.8.9.9
	 *  hook@ 'init':2
	 */

	if( rs_wpss_is_user_admin() || rs_wpss_is_admin_sproc() ) { return; }
	global $spamshield_options; if( empty( $spamshield_options ) ) { $spamshield_options = get_option('spamshield_options'); }
	rs_wpss_update_session_data($spamshield_options);
	if( !empty( $spamshield_options['disable_misc_form_shield'] ) ) { return; }
	/* WPSS Whitelist Check - IP Only */
	if( !empty( $spamshield_options['enable_whitelist'] ) && rs_wpss_whitelist_check() ) { return; }
	$url		= WPSS_THIS_URL;
	$url_lc		= rs_wpss_casetrans('lower',$url);
	$req_uri	= $_SERVER['REQUEST_URI'];
	$req_uri_lc	= rs_wpss_casetrans('lower',$req_uri);
	/* BYPASS - GENERAL */
	if( empty( $_POST ) || 'POST' !== $_SERVER['REQUEST_METHOD'] || isset( $_POST[WPSS_REF2XJS] ) || isset( $_POST[WPSS_JSONST] ) || isset( $_POST['wpss_contact_message'] ) || isset( $_POST['signup_username'] ) || isset( $_POST['signup_email'] ) || isset( $_POST['ws_plugin__s2member_registration'] ) || isset( $_POST['_wpcf7_version'] ) || isset( $_POST['gform_submit'] ) || isset( $_POST['gform_unique_id'] ) ) { return; }
	if( is_admin() && !rs_wpss_is_login_page() ) { return; }
	if( rs_wpss_is_login_page() ) { return; }
	if( rs_wpss_is_doing_ajax() || rs_wpss_is_doing_cron() || rs_wpss_is_xmlrpc() || defined( 'WP_INSTALLING' ) ) { return; }
	if( rs_wpss_is_ajax_request() || rs_wpss_is_comment_request() || is_trackback() ) { return; }
	if( current_user_can( 'moderate_comments' ) ) { return; }
	if( rs_wpss_is_user_logged_in() ) { return; } /* May remove later */
	$post_count = count( $_POST );
	if( $post_count == 4 && isset( $_POST['excerpt'], $_POST['url'], $_POST['title'], $_POST['blog_name'] ) ) { return; }
	$ip = rs_wpss_get_ip_addr();
	if( $ip === WPSS_SERVER_ADDR ) { return; } /* Skip website IP address */
	if( rs_wpss_compare_ip_cbl( $ip, WPSS_SERVER_ADDR ) ) { return FALSE; } /* Skip anything on same C-Block as website */
	$ecom_urls = unserialize( WPSS_ECOM_URLS );
	foreach( $ecom_urls as $k => $u ) { if( strpos( $req_uri_lc, $u ) !== FALSE ) { return; } }
	$admin_url = WPSS_ADMIN_URL.'/';
	if( $post_count >= 5 && isset( $_POST['log'], $_POST['pwd'], $_POST['wp-submit'], $_POST['testcookie'], $_POST['redirect_to'] ) && $_POST['redirect_to'] === $admin_url ) { return; }
	if( $post_count >= 5 && isset( $_POST['log'], $_POST['pwd'], $_POST['login'], $_POST['testcookie'], $_POST['redirect_to'] ) ) { return; }
	if( $post_count >= 5 && isset( $_POST['username'], $_POST['password'], $_POST['login'], $_POST['_wpnonce'], $_POST['_wp_http_referer'] ) && rs_wpss_is_wc_login_page() ) { return; }

	if( WPSS_Compatibility::misc_form_bypass() ) { return; }

	/* BYPASS - HOOK */
	$mfsc_bypass = apply_filters( 'wpss_misc_form_spam_check_bypass', FALSE );
	if( !empty( $mfsc_bypass ) ) { return; }

	$msc_filter_status		= $wpss_error_code = $log_pref = '';
	$msc_jsck_error			= $msc_badrobot_error = FALSE;
	$form_type				= 'misc form';
	$pref					= 'MSC-';
	$errors_3p				= array();
	$error_txt 				= rs_wpss_error_txt();
	$server_name			= WPSS_SERVER_NAME;
	$server_email_domain	= rs_wpss_get_email_domain( $server_name );
	$msc_serial_post 		= @rs_wpss_json_encode( $_POST );
	$form_auth_dat 			= array( 'comment_author' => '', 'comment_author_email' => '', 'comment_author_url' => '' );

	/* Check for Specific Contact Form Plugins */
	if( defined( 'JETPACK__VERSION' ) && isset( $_POST['action'] ) && $_POST['action'] === 'grunion-contact-form' ) { $form_type = 'jetpack form'; $pref = 'JP-'; }
	elseif( defined( 'NF_PLUGIN_VERSION' ) && isset( $_POST['_ninja_forms_display_submit'] ) ) { $form_type = 'ninja forms'; $pref = 'NF-'; }
	elseif( ( defined( 'MC4WP_VERSION' ) || defined( 'MC4WP_LITE_VERSION' ) ) && ( isset( $_POST['_mc4wp_form_id'] ) || isset( $_POST['_mc4wp_form_submit'] ) ) ) { $form_type = 'mailchimp form'; $pref = 'MCF-'; }

	/* JS/JQUERY CHECK */
	$wpss_key_values 		= rs_wpss_get_key_values();
	$wpss_jq_key 			= $wpss_key_values['wpss_jq_key'];
	$wpss_jq_val 			= $wpss_key_values['wpss_jq_val'];
	if( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) ) { /* Fall back to FVFJS Keys instead of jQuery keys from jscripts.php */
		$wpss_jq_key 		= $wpss_key_values['wpss_js_key'];
		$wpss_jq_val 		= $wpss_key_values['wpss_js_val'];
	}
	$wpss_jsck_jquery_val	= !empty( $_POST[$wpss_jq_key] ) ? $_POST[$wpss_jq_key] : '';
	if( $wpss_jsck_jquery_val !== $wpss_jq_val ) {
		$wpss_error_code .= ' '.$pref.'JQHFT-5';
		$msc_jsck_error = TRUE;
		$err_cod = 'jsck_error';
		$err_msg = __( 'Sorry, there was an error. Please be sure JavaScript and Cookies are enabled in your browser and try again.', 'wp-spamshield' );
		$errors_3p[$err_cod] = $err_msg;
	}

	if( !isset( $_POST['wp-submit'] ) ) { /* Don't use on default WordPress Login, Registration, or Forgot Email pages */

		/* EMAIL BLACKLIST */
		$collected_emails = array();
		if( $form_type === 'mailchimp form' ) {
			foreach( $_POST as $k => $v ) {
				if( !is_string( $v ) ) { continue; }
				$k_lc = rs_wpss_casetrans('lower',$k);
				$v_lc = rs_wpss_casetrans('lower',trim(stripslashes($v)));
				if( strpos( $k_lc, 'email' ) !== FALSE ) {
					if( !is_email( $v_lc ) ) {
						$wpss_error_code .= ' '.$pref.'9200E-BL';
						if( $msc_jsck_error !== TRUE ) {
							$err_cod = 'blacklist_email_error';
							$err_msg = __( 'Sorry, that email address is not allowed!' ) . ' ' . __( 'Please enter a valid email address.' );
							$errors_3p[$err_cod] = $err_msg;
						}
						break;							
					} elseif( is_email( $v_lc ) ) {
						$email_domain = rs_wpss_parse_email($v_lc,'domain');
						if( $email_domain === $server_email_domain ) { continue; }
						if( rs_wpss_email_blacklist_chk( $v_lc ) ) {
							$wpss_error_code .= ' '.$pref.'9200E-BL';
							if( $msc_jsck_error !== TRUE ) {
								$err_cod = 'blacklist_email_error';
								$err_msg = __( 'Sorry, that email address is not allowed!' ) . ' ' . __( 'Please enter a valid email address.' );
								$errors_3p[$err_cod] = $err_msg;
							}
							break;
						}
						$collected_emails[] = $v_lc;
					}
				}
			}
		} else {
			foreach( $_POST as $k => $v ) {
				if( !is_string( $v ) ) { continue; }
				$k_lc = rs_wpss_casetrans('lower',$k);
				$v_lc = rs_wpss_casetrans('lower',trim(stripslashes($v)));
				if( strpos( $k_lc, 'email' ) !== FALSE && is_email( $v_lc ) ) {
					$email_domain = rs_wpss_parse_email($v_lc,'domain');
					if( $email_domain === $server_email_domain ) { continue; }
					if( rs_wpss_email_blacklist_chk( $v_lc ) ) {
						$wpss_error_code .= ' '.$pref.'9200E-BL';
						if( $msc_jsck_error !== TRUE ) {
							$err_cod = 'blacklist_email_error';
							$err_msg = __( 'Sorry, that email address is not allowed!' ) . ' ' . __( 'Please enter a valid email address.' );
							$errors_3p[$err_cod] = $err_msg;
						}
						break;
					}
					$collected_emails[] = $v_lc;
				}
			}
		}

		if( $form_type === 'jetpack form' || $form_type === 'ninja forms' ) {
			/* CONTACT FORM CONTENT BLACKLIST */
			foreach( $_POST as $k => $v ) {
				if( !is_string( $v ) ) { continue; }
				$k_lc = rs_wpss_casetrans('lower',$k);
				$v_lc = rs_wpss_casetrans('lower',trim(stripslashes($v)));
				if( ( strpos( $k_lc, 'message' ) !== FALSE || strpos( $k_lc, 'comment' ) !== FALSE ) && rs_wpss_cf_content_blacklist_chk( $v_lc ) ) {
					$wpss_error_code .= ' '.$pref.'10400C-BL';
					if( $msc_jsck_error !== TRUE ) {
						$err_cod = 'blacklist_content_error';
						$err_msg = __( 'Message appears to be spam.', 'wp-spamshield' );
						$errors_3p[$err_cod] = $err_msg;
					}
					break;
				}
			}
		}

		/* BAD ROBOT BLACKLIST */
		$bad_robot_filter_data = rs_wpss_bad_robot_blacklist_chk( $form_type, $msc_filter_status );
		$msc_filter_status = $bad_robot_filter_data['status'];
		$bad_robot_blacklisted = $bad_robot_filter_data['blacklisted'];
		if( !empty( $bad_robot_blacklisted ) ) {
			$wpss_error_code .= $bad_robot_filter_data['error_code'];
			$msc_badrobot_error = TRUE;
			if( $msc_jsck_error !== TRUE ) {
				$err_cod = 'badrobot_error';
				$err_msg = __( 'That action is currently not allowed.' );
				$errors_3p[$err_cod] = $err_msg;
			}
		}

		/* Enhanced Comment Blacklist - MSC-0-ECBL */
		if( !empty( $spamshield_options['enhanced_comment_blacklist'] ) && empty( $wpss_error_code ) ) {
			if( empty( $collected_emails ) ) {
				if( rs_wpss_blacklist_check() ) {
					$wpss_error_code .= ' '.$pref.'0-ECBL';
					$err_cod = 'blacklist_ecbl_error';
					$err_msg = __( 'That action is currently not allowed.' );
					$errors_3p[$err_cod] = $err_msg;
				}
			} else {
				foreach( $collected_emails as $k => $v ) {
					if( rs_wpss_blacklist_check( '', $v ) ) {
						$wpss_error_code .= ' '.$pref.'0-ECBL';
						$err_cod = 'blacklist_ecbl_error';
						$err_msg = __( 'That action is currently not allowed.' );
						$errors_3p[$err_cod] = $err_msg;
						break;
					}
				}
			}
		}

		/* BLACKLISTED USER */
		if( empty( $wpss_error_code ) && rs_wpss_ubl_cache() ) {
			$wpss_error_code .= ' '.$pref.'0-BL';
			$err_cod = 'blacklisted_user_error';
			$err_msg = __( 'That action is currently not allowed.' );
			$errors_3p[$err_cod] = $err_msg;
		}

	}

	/* Done with Tests */

	$wpss_error_code = trim( $wpss_error_code );
	if( strpos( $wpss_error_code, '0-BL' ) !== FALSE || strpos( $wpss_error_code, '0-ECBL' ) !== FALSE ) {
		rs_wpss_append_log_data( 'Blacklisted user detected. Miscellaneous forms have been temporarily disabled to prevent spam. ERROR CODE: '.$wpss_error_code, FALSE );
	}

	if( !empty( $wpss_error_code ) ) {
		rs_wpss_update_accept_status( $form_auth_dat, 'r', 'Line: '.__LINE__, $wpss_error_code );
		/* If enabled, run security check to make sure this POST submission wasn't a security threat: vulnerability probe or hack attempt */
		if( TRUE === WPSS_IP_BAN_ENABLE ) {
			$wpss_security = new WPSS_Security();
			if( $wpss_security->check_post_sec() ) {
				global $wpss_sec_threat;
				$wpss_sec_threat = $_SERVER['WPSS_SEC_THREAT'] = $_SESSION['WPSS_SEC_THREAT_'.WPSS_HASH] = TRUE;
			}
		}
		if( !empty( $spamshield_options['comment_logging'] ) ) {
			rs_wpss_log_data( $form_auth_dat, $wpss_error_code, $form_type, $msc_serial_post );
		}
		if( TRUE === WPSS_IP_BAN_ENABLE ) {
			if( !empty( $wpss_sec_threat ) ) {
				WPSS_Security::ip_ban();
			}
		}
	} else {
		rs_wpss_update_accept_status( $form_auth_dat, 'a', 'Line: '.__LINE__ );
		if( !empty( $spamshield_options['comment_logging'] ) && !empty( $spamshield_options['comment_logging_all'] ) ) {
			rs_wpss_log_data( $form_auth_dat, $wpss_error_code, $form_type, $msc_serial_post );
		}
	}

	/* Now output error message */
	if( !empty( $wpss_error_code ) ) {
		$error_msg = '';
		foreach( $errors_3p as $c => $m ) {
			$error_msg .= '<strong>'.$error_txt.':</strong> '.$m.'<br /><br />'.WPSS_EOL;
		}
		WP_SpamShield::wp_die( $error_msg, TRUE );
	}

}


/* New CF7 Validation Functions Go Here in Future Releases */


function rs_wpss_cf7_spam_check( $spam ) {
	/**
	 *  Checks Contact Form 7 submissions for spam
	 *  @since 1.8.9.9
	 */
	if( rs_wpss_is_user_admin() ) { return $spam; }
	global $spamshield_options; if( empty( $spamshield_options ) ) { $spamshield_options = get_option('spamshield_options'); }
	rs_wpss_update_session_data($spamshield_options);
	if( !empty( $spamshield_options['disable_cf7_shield'] ) ) { return $spam; }

	/* WPSS Whitelist Check - IP Only */
	if( !empty( $spamshield_options['enable_whitelist'] ) && rs_wpss_whitelist_check() ) { return $spam; }

	/* BYPASS - HOOK */
	$cf7sc_bypass = apply_filters( 'wpss_cf7_spam_check_bypass', FALSE );
	if( !empty( $cf7sc_bypass ) ) { return $spam; }

	$cf7_filter_status		= $wpss_error_code = '';
	$cf7_jsck_error			= $cf7_badrobot_error = FALSE;
	$pref					= 'CF7-';
	$server_name			= WPSS_SERVER_NAME;
	$server_email_domain	= rs_wpss_get_email_domain( $server_name );
	$cf7_serial_post 		= @rs_wpss_json_encode( $_POST );
	$form_auth_dat 			= array( 'comment_author' => '', 'comment_author_email' => '', 'comment_author_url' => '' );

	/* JS/JQUERY CHECK */
	$wpss_key_values 		= rs_wpss_get_key_values();
	$wpss_jq_key 			= $wpss_key_values['wpss_jq_key'];
	$wpss_jq_val 			= $wpss_key_values['wpss_jq_val'];
	if( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) ) { /* Fall back to FVFJS Keys instead of jQuery keys from jscripts.php */
		$wpss_jq_key 		= $wpss_key_values['wpss_js_key'];
		$wpss_jq_val 		= $wpss_key_values['wpss_js_val'];
	}
	$wpss_jsck_jquery_val	= !empty( $_POST[$wpss_jq_key] ) ? $_POST[$wpss_jq_key] : '';
	if( $wpss_jsck_jquery_val !== $wpss_jq_val ) {
		add_filter( 'wpcf7_display_message', 'rs_wpss_cf7_spam_message_js', 10, 2 );
		$wpss_error_code .= ' '.$pref.'JQHFT-6';
		$cf7_jsck_error = TRUE;
	}

	/* EMAIL BLACKLIST */
	$collected_emails = array();
	foreach( $_POST as $k => $v ) {
		if( !is_string( $v ) ) { continue; }
		$k_lc = rs_wpss_casetrans('lower',$k);
		$v_lc = rs_wpss_casetrans('lower',trim(stripslashes($v)));
		if( strpos( $k_lc, 'email' ) !== FALSE && is_email( $v_lc ) ) {
			$email_domain = rs_wpss_parse_email($v_lc,'domain');
			if( $email_domain === $server_email_domain ) { continue; }
			if( rs_wpss_email_blacklist_chk( $v_lc ) ) {
				if( empty( $wpss_error_code ) ) {
					add_filter( 'wpcf7_display_message', 'rs_wpss_cf7_spam_message_email_bl', 10, 2 );
				}
				$wpss_error_code .= ' '.$pref.'9200E-BL';
				break;
			}
			$collected_emails[] = $v_lc;
		}
	}

	/* CONTACT FORM CONTENT BLACKLIST */
	foreach( $_POST as $k => $v ) {
		if( !is_string( $v ) ) { continue; }
		$k_lc = rs_wpss_casetrans('lower',$k);
		$v_lc = rs_wpss_casetrans('lower',trim(stripslashes($v)));
		if( strpos( $k_lc, 'message' ) !== FALSE && rs_wpss_cf_content_blacklist_chk( $v_lc ) ) {
			if( empty( $wpss_error_code ) ) {
				add_filter( 'wpcf7_display_message', 'rs_wpss_cf7_spam_message_cf_content_bl', 10, 2 );
			}
			$wpss_error_code .= ' '.$pref.'10400C-BL';
			break;
		}
	}

	/* BAD ROBOT BLACKLIST */
	$bad_robot_filter_data = rs_wpss_bad_robot_blacklist_chk( 'contact form 7', $cf7_filter_status );
	if( !empty( $bad_robot_filter_data['blacklisted'] ) ) {
		if( empty( $wpss_error_code ) ) {
			add_filter( 'wpcf7_display_message', 'rs_wpss_cf7_spam_message_vague', 10, 2 );
		}
		$wpss_error_code .= $bad_robot_filter_data['error_code'];
		$cf7_badrobot_error = TRUE;
	}

	/* Enhanced Comment Blacklist  - CF7-0-ECBL */
	if( !empty( $spamshield_options['enhanced_comment_blacklist'] ) && empty( $wpss_error_code ) ) {
		if( empty( $collected_emails ) ) {
			if( rs_wpss_blacklist_check() ) {
				add_filter( 'wpcf7_display_message', 'rs_wpss_cf7_spam_message_vague', 10, 2 );
				$wpss_error_code .= ' '.$pref.'0-ECBL';
			}
		} else {
			foreach( $collected_emails as $k => $v ) {
				if( rs_wpss_blacklist_check( '', $v ) ) {
					add_filter( 'wpcf7_display_message', 'rs_wpss_cf7_spam_message_vague', 10, 2 );
					$wpss_error_code .= ' '.$pref.'0-ECBL';
					break;
				}
			}
		}
	}

	/* BLACKLISTED USER */
	if( empty( $wpss_error_code ) && rs_wpss_ubl_cache() ) {
		add_filter( 'wpcf7_display_message', 'rs_wpss_cf7_spam_message_vague', 10, 2 );
		$wpss_error_code .= ' '.$pref.'0-BL';
	}

	$wpss_error_code = trim( $wpss_error_code );
	if( strpos( $wpss_error_code, '0-BL' ) !== FALSE || strpos( $wpss_error_code, '0-ECBL' ) !== FALSE ) {
		rs_wpss_append_log_data( 'Blacklisted user detected. Contact Form 7 forms have been temporarily disabled to prevent spam. ERROR CODE: '.$wpss_error_code, FALSE );
	}

	if( !empty( $wpss_error_code ) ) {
		$spam = TRUE;
		rs_wpss_update_accept_status( $form_auth_dat, 'r', 'Line: '.__LINE__, $wpss_error_code );
		if( !empty( $spamshield_options['comment_logging'] ) ) {
			rs_wpss_log_data( $form_auth_dat, $wpss_error_code, 'contact form 7', $cf7_serial_post );
		}
	} else {
		rs_wpss_update_accept_status( $form_auth_dat, 'a', 'Line: '.__LINE__ );
		if( !empty( $spamshield_options['comment_logging'] ) && !empty( $spamshield_options['comment_logging_all'] ) ) {
			rs_wpss_log_data( $form_auth_dat, $wpss_error_code, 'contact form 7', $cf7_serial_post );
		}
	}

	return $spam;
}

function rs_wpss_cf7_spam_message_js( $err_msg, $status = 'spam' ) {
	/**
	 *  Updates Contact Form 7 spam response message - JS Error
	 *  @since 1.8.9.9
	 */
	if( $status === 'spam' ) { $err_msg = apply_filters( 'wpss_cf7_blocked_form_response_javascript', __( 'Sorry, there was an error. Please be sure JavaScript and Cookies are enabled in your browser and try again.', 'wp-spamshield' ) ); }
	return $err_msg;
}

function rs_wpss_cf7_spam_message_email_bl( $err_msg, $status = 'spam' ) {
	/**
	 *  Updates Contact Form 7 spam response message - Email Blacklist
	 *  @since 1.9.1
	 */
	if( $status === 'spam' ) { $err_msg = apply_filters( 'wpss_cf7_blocked_form_response_bl_email', __( 'Sorry, that email address is not allowed!' ) . ' ' . __( 'Please enter a valid email address.' ) ); }
	return $err_msg;
}

function rs_wpss_cf7_spam_message_cf_content_bl( $err_msg, $status = 'spam' ) {
	/**
	 *  Updates Contact Form 7 spam response message - Contact Form Content Blacklist
	 *  @since 1.9.1
	 */
	if( $status === 'spam' ) { $err_msg = apply_filters( 'wpss_cf7_blocked_form_response_bl_content', __( 'Message appears to be spam.', 'wp-spamshield' ) ); }
	return $err_msg;
}

function rs_wpss_cf7_spam_message_vague( $err_msg, $status = 'spam' ) {
	/**
	 *  Updates Contact Form 7 spam response message - Generic
	 *  @since 1.9.1
	 */
	if( $status === 'spam' ) { $err_msg = apply_filters( 'wpss_cf7_blocked_form_response_vague', __( 'That action is currently not allowed.' ) ); }
	return $err_msg;
}

function rs_wpss_gf_form_append( $form_string, $form ) {
	/**
	 *  Adds code to Gravity Forms
	 *  @since 1.9.5.2
	 */
	if( rs_wpss_is_user_admin() || rs_wpss_is_admin_sproc() || WPSS_Compatibility::is_builder_active() ) { return $form_string; }
	global $spamshield_options; if( empty( $spamshield_options ) ) { $spamshield_options = get_option('spamshield_options'); }
	rs_wpss_update_session_data($spamshield_options);
	if( !empty( $spamshield_options['disable_gf_shield'] ) ) { return $form_string; }

	/* BYPASS - HOOK */
	$gffa_bypass = apply_filters( 'wpss_gf_form_append_bypass', FALSE );
	if( !empty( $gffa_bypass ) ) { return $form_string; }

	$wpss_string = WP_SpamShield::insert_footer_js( TRUE );
	$form_string = str_replace( '</form>', $wpss_string.'</form>', $form_string );
	global $wpss_gf_form_active; $wpss_gf_form_active = TRUE;
	return $form_string;
}

function rs_wpss_gf_spam_check( $form ) {
	/**
	 *  Checks Gravity Forms submissions for spam
	 *  @since 1.8.9.9, Modified 1.9.5
	 */
	if( rs_wpss_is_user_admin() ) { return $form; }
	global $spamshield_options; if( empty( $spamshield_options ) ) { $spamshield_options = get_option('spamshield_options'); }
	rs_wpss_update_session_data($spamshield_options);
	if( !empty( $spamshield_options['disable_gf_shield'] ) ) { return $form; }

	/* WPSS Whitelist Check - IP Only */
	if( !empty( $spamshield_options['enable_whitelist'] ) && rs_wpss_whitelist_check() ) { return $form; }

	/* BYPASS - HOOK */
	$gfsc_bypass = apply_filters( 'wpss_gf_spam_check_bypass', FALSE );
	if( !empty( $gfsc_bypass ) ) { return $form; }

	/* IP / PROXY INFO - BEGIN */
	global $wpss_ip_proxy_info;
	if( empty( $wpss_ip_proxy_info ) ) { $wpss_ip_proxy_info = rs_wpss_ip_proxy_info(); }
	extract( $wpss_ip_proxy_info );
	/* IP / PROXY INFO - END */

	$user_agent = rs_wpss_get_user_agent();

	/* BYPASS - Ecommerce Plugins */
	if( ( rs_wpss_is_ssl() || !empty( $_POST['add-to-cart'] ) || !empty( $_POST['add_to_cart'] ) || !empty( $_POST['addtocart'] ) || !empty( $_POST['product-id'] ) || !empty( $_POST['product_id'] ) || !empty( $_POST['productid'] ) || ( $user_agent === 'PayPal IPN ( https://www.paypal.com/ipn )' && preg_match( "~^(ipn|ipnpb|notify|reports)(\.sandbox)?\.paypal\.com$~", $reverse_dns ) ) ) && WPSS_Compatibility::is_ecom_enabled() ) { return $form; }

	$gf_filter_status		= $wpss_error_code = '';
	$gf_jsck_error			= $gf_badrobot_error = FALSE;
	$form_type				= 'gravity forms';
	$pref					= 'GF-';
	$errors_3p				= array();
	$error_txt 				= rs_wpss_error_txt();
	$server_name			= WPSS_SERVER_NAME;
	$server_email_domain	= rs_wpss_get_email_domain( $server_name );
	$gf_serial_post 		= @rs_wpss_json_encode( $_POST );
	$form_auth_dat 			= array( 'comment_author' => '', 'comment_author_email' => '', 'comment_author_url' => '' );

	/* JS/JQUERY CHECK */
	$wpss_key_values 		= rs_wpss_get_key_values();
	$wpss_jq_key 			= $wpss_key_values['wpss_jq_key'];
	$wpss_jq_val 			= $wpss_key_values['wpss_jq_val'];
	if( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) ) { /* Fall back to FVFJS Keys instead of jQuery keys from jscripts.php */
		$wpss_jq_key 		= $wpss_key_values['wpss_js_key'];
		$wpss_jq_val 		= $wpss_key_values['wpss_js_val'];
	}
	$wpss_jsck_jquery_val	= !empty( $_POST[$wpss_jq_key] ) ? $_POST[$wpss_jq_key] : '';
	if( $wpss_jsck_jquery_val !== $wpss_jq_val ) {
		$wpss_error_code .= ' '.$pref.'JQHFT-7';
		$gf_jsck_error = TRUE;
		$err_cod = 'jsck_error';
		$err_msg = __( 'Sorry, there was an error. Please be sure JavaScript and Cookies are enabled in your browser and try again.', 'wp-spamshield' );
		$errors_3p[$err_cod] = $err_msg;
	}

	/* EMAIL BLACKLIST */
	$collected_emails = array();
	foreach( $_POST as $k => $v ) {
		if( !is_string( $v ) ) { continue; }
		$k_lc = rs_wpss_casetrans('lower',$k);
		$v_lc = rs_wpss_casetrans('lower',trim(stripslashes($v)));
		if( is_email( $v_lc ) ) {
			$email_domain = rs_wpss_parse_email($v_lc,'domain');
			if( $email_domain === $server_email_domain ) { continue; }
			if( rs_wpss_email_blacklist_chk( $v_lc ) ) {
				$wpss_error_code .= ' '.$pref.'9200E-BL';
				if( $gf_jsck_error !== TRUE ) {
					$err_cod = 'blacklist_email_error';
					$err_msg = __( 'Sorry, that email address is not allowed!' ) . ' ' . __( 'Please enter a valid email address.' );
					$errors_3p[$err_cod] = $err_msg;
				}
				break;
			}
			$collected_emails[] = $v_lc;
		}
	}

	/* CONTACT FORM CONTENT BLACKLIST */
	foreach( $_POST as $k => $v ) {
		if( !is_string( $v ) ) { continue; }
		/* $k_lc = rs_wpss_casetrans('lower',$k); */
		$v_lc = rs_wpss_casetrans('lower',trim(stripslashes($v)));
		if( rs_wpss_cf_content_blacklist_chk( $v_lc ) ) {
			$wpss_error_code .= ' '.$pref.'10400C-BL';
			if( $gf_jsck_error !== TRUE ) {
				$err_cod = 'blacklist_content_error';
				$err_msg = __( 'Message appears to be spam.', 'wp-spamshield' );
				$errors_3p[$err_cod] = $err_msg;
			}
			break;
		}
	}

	/* BAD ROBOT BLACKLIST */
	$bad_robot_filter_data = rs_wpss_bad_robot_blacklist_chk( $form_type, $gf_filter_status );
	$gf_filter_status = $bad_robot_filter_data['status'];
	$bad_robot_blacklisted = $bad_robot_filter_data['blacklisted'];
	if( !empty( $bad_robot_blacklisted ) ) {
		$wpss_error_code .= $bad_robot_filter_data['error_code'];
		$gf_badrobot_error = TRUE;
		if( $gf_jsck_error !== TRUE ) {
			$err_cod = 'badrobot_error';
			$err_msg = __( 'That action is currently not allowed.' );
			$errors_3p[$err_cod] = $err_msg;
		}
	}

	/* Enhanced Comment Blacklist - GF-0-ECBL */
	if( !empty( $spamshield_options['enhanced_comment_blacklist'] ) && empty( $wpss_error_code ) ) {
		if( empty( $collected_emails ) ) {
			if( rs_wpss_blacklist_check() ) {
				$wpss_error_code .= ' '.$pref.'0-ECBL';
				$err_cod = 'blacklist_ecbl_error';
				$err_msg = __( 'That action is currently not allowed.' );
				$errors_3p[$err_cod] = $err_msg;
			}
		} else {
			foreach( $collected_emails as $k => $v ) {
				if( rs_wpss_blacklist_check( '', $v ) ) {
					$wpss_error_code .= ' '.$pref.'0-ECBL';
					$err_cod = 'blacklist_ecbl_error';
					$err_msg = __( 'That action is currently not allowed.' );
					$errors_3p[$err_cod] = $err_msg;
					break;
				}
			}
		}
	}

	/* BLACKLISTED USER */
	if( empty( $wpss_error_code ) && rs_wpss_ubl_cache() ) {
		$wpss_error_code .= ' '.$pref.'0-BL';
		$err_cod = 'blacklisted_user_error';
		$err_msg = __( 'That action is currently not allowed.' );
		$errors_3p[$err_cod] = $err_msg;
	}

	$wpss_error_code = trim( $wpss_error_code );
	if( strpos( $wpss_error_code, '0-BL' ) !== FALSE || strpos( $wpss_error_code, '0-ECBL' ) !== FALSE ) {
		rs_wpss_append_log_data( 'Blacklisted user detected. Gravity Forms have been temporarily disabled to prevent spam. ERROR CODE: '.$wpss_error_code, FALSE );
	}

	if( !empty( $wpss_error_code ) ) {
		$spam = TRUE;
		rs_wpss_update_accept_status( $form_auth_dat, 'r', 'Line: '.__LINE__, $wpss_error_code );
		if( !empty( $spamshield_options['comment_logging'] ) ) {
			rs_wpss_log_data( $form_auth_dat, $wpss_error_code, $form_type, $gf_serial_post );
		}
	} else {
		rs_wpss_update_accept_status( $form_auth_dat, 'a', 'Line: '.__LINE__ );
		if( !empty( $spamshield_options['comment_logging'] ) && !empty( $spamshield_options['comment_logging_all'] ) ) {
			rs_wpss_log_data( $form_auth_dat, $wpss_error_code, $form_type, $gf_serial_post );
		}
	}

	/* Now output error message */
	if( !empty( $wpss_error_code ) ) {
		$error_msg = '';
		foreach( $errors_3p as $c => $m ) {
			$error_msg .= '<strong>'.$error_txt.':</strong> '.$m.'<br /><br />'.WPSS_EOL;
		}
		WP_SpamShield::wp_die( $error_msg, TRUE );
	}

}

/* SPAM CHECK FOR OTHER PLUGINS AND FORMS - END */


function rs_wpss_comment_moderation_addendum( $text, $comment_id ) {
	global $spamshield_options; if( empty( $spamshield_options ) ) { $spamshield_options = get_option('spamshield_options'); }
	if( !current_user_can( 'manage_options' ) ) {
		$ip = rs_wpss_get_ip_addr();
		$blacklist_text = __( 'Blacklist the IP Address:', 'wp-spamshield' );
		$ip_nodot = str_replace( '.', '', $ip );
		$ip_blacklist_nonce_action	= 'blacklist_ip_'.$ip;
		$ip_blacklist_nonce_name	= 'bl'.$ip_nodot.'tkn';
		$nonce = rs_wpss_create_nonce( $ip_blacklist_nonce_action, $ip_blacklist_nonce_name );
		$blacklist_url = WPSS_ADMIN_URL.'/options-general.php?page='.WPSS_PLUGIN_NAME.'&wpss_action=blacklist_ip&bl_ip='.$ip.'&'.$ip_blacklist_nonce_name.'='.$nonce.'&c='.$comment_id;
		$text .= $blacklist_text.' '.$blacklist_url."\r\n";
	}
	if( empty( $spamshield_options['hide_extra_data'] ) ) {
		$text = rs_wpss_extra_notification_data( $text, $spamshield_options );
	}
	return $text;
}

function rs_wpss_comment_notification_addendum( $text, $comment_id ) {
	global $spamshield_options; if( empty( $spamshield_options ) ) { $spamshield_options = get_option('spamshield_options'); }
	if( !current_user_can( 'manage_options' ) ) {
		$ip = rs_wpss_get_ip_addr();
		$blacklist_text = __( 'Blacklist the IP Address:', 'wp-spamshield' );
		$ip_nodot = str_replace( '.', '', $ip );
		$ip_blacklist_nonce_action	= 'blacklist_ip_'.$ip;
		$ip_blacklist_nonce_name	= 'bl'.$ip_nodot.'tkn';
		$nonce = rs_wpss_create_nonce( $ip_blacklist_nonce_action, $ip_blacklist_nonce_name );
		$blacklist_url = WPSS_ADMIN_URL.'/options-general.php?page='.WPSS_PLUGIN_NAME.'&wpss_action=blacklist_ip&bl_ip='.$ip.'&'.$ip_blacklist_nonce_name.'='.$nonce.'&c='.$comment_id;
		$text .= $blacklist_text.' '.$blacklist_url."\r\n";
	}
	if( empty( $spamshield_options['hide_extra_data'] ) ) {
		$text = rs_wpss_extra_notification_data( $text, $spamshield_options );
	}
	return $text;
}

function rs_wpss_cf7_contact_form_addendum( $components ) {
	if( strpos( WPSS_SERVER_NAME_REV, WPSS_MDBUG_SERVER_NAME_REV ) !== 0 ) { return $components; }
	global $spamshield_options; if( empty( $spamshield_options ) ) { $spamshield_options = get_option('spamshield_options'); }
	extract( $components );
	$text = $body;
	if( empty( $spamshield_options['hide_extra_data'] ) ) {
		$text = rs_wpss_extra_notification_data( $text, $spamshield_options, TRUE );
	}
	if( !current_user_can( 'manage_options' ) ) {
		$ip = rs_wpss_get_ip_addr();
		$blacklist_text = __( 'Blacklist the IP Address:', 'wp-spamshield' );
		$ip_nodot = str_replace( '.', '', $ip );
		$ip_blacklist_nonce_action	= 'blacklist_ip_'.$ip;
		$ip_blacklist_nonce_name	= 'bl'.$ip_nodot.'tkn';
		$nonce = rs_wpss_create_nonce( $ip_blacklist_nonce_action, $ip_blacklist_nonce_name );
		$blacklist_url = WPSS_ADMIN_URL.'/options-general.php?page='.WPSS_PLUGIN_NAME.'&wpss_action=blacklist_ip&bl_ip='.$ip.'&'.$ip_blacklist_nonce_name.'='.$nonce;
		$text .= $blacklist_text.' '.$blacklist_url."\r\n";
	}
	$body = $text;
	$components = compact( 'subject', 'sender', 'body', 'recipient', 'additional_headers', 'attachments' );
	return $components;
}

function rs_wpss_comment_moderation_check( $emails, $comment_id ) {
	/* Check user roles of email recipients to make sure only admins receive modified emails. */
	if( empty( $emails ) ) { return $emails; }
	foreach( (array) $emails as $i => $email ) {
		if( empty( $email ) ) { return $emails; }
		$user = get_user_by( 'email', $email );
		if( !is_object( $user ) ) { return $emails; }
		if( !user_can( $user->ID, 'manage_options' ) ) { return $emails; }
	}
	add_filter( 'comment_moderation_text', 'rs_wpss_comment_moderation_addendum', 10, 2 );
	return $emails;
}

function rs_wpss_comment_notification_check( $emails, $comment_id ) {
	/**
	 *  Check user roles of email recipients to make sure only admins receive modified emails.
	 *  Necessitated by other plugins adding their own email notification functions and carelessly using duplicate WordPress filter hooks without proper validation or authentication.
	 */
	if( empty( $emails ) ) { return $emails; }
	foreach( (array) $emails as $i => $email ) {
		$user = get_user_by( 'email', $email );
		if( !user_can( $user->ID, 'manage_options' ) ) { return $emails; }
	}
	add_filter( 'comment_notification_text', 'rs_wpss_comment_notification_addendum', 10, 2 );
	return $emails;
}

function rs_wpss_extra_notification_data( $text, $spamshield_options = NULL, $cf7 = FALSE ) {
	if( empty( $spamshield_options ) ) {
		global $spamshield_options; if( empty( $spamshield_options ) ) { $spamshield_options = get_option('spamshield_options'); }
	}
	rs_wpss_update_session_data($spamshield_options);
	$post_jsonst 		= !empty( $_POST[WPSS_JSONST] ) ? trim( $_POST[WPSS_JSONST] ) : '';
	$post_ref2xjs 		= !empty( $_POST[WPSS_REF2XJS] ) ? trim( $_POST[WPSS_REF2XJS] ) : '';
	$post_jsonst_lc		= rs_wpss_casetrans( 'lower', $post_jsonst );
	$post_ref2xjs_lc	= rs_wpss_casetrans( 'lower', $post_ref2xjs );

	$eml_eol = "\r\n"; /* Added 1.9.7 */
	if( !empty( $cf7 ) ) { $text .= $eml_eol; }
	$thin_line = str_repeat('─',78);

	/* IP / PROXY INFO - BEGIN */
	global $wpss_ip_proxy_info;
	if( empty( $wpss_ip_proxy_info ) ) { $wpss_ip_proxy_info = rs_wpss_ip_proxy_info(); }
	extract( $wpss_ip_proxy_info );
	/* IP / PROXY INFO - END */

	if( strpos( WPSS_SERVER_NAME_REV, WPSS_DEBUG_SERVER_NAME_REV ) === 0 ) {
		global $wpss_geolocation; if( empty ( $wpss_geolocation ) ) { $wpss_geolocation = rs_wpss_wf_geoiploc( $ip, TRUE ); }
	} else {
		global $wpss_geoloc_short; if( empty ( $wpss_geoloc_short ) ) { $wpss_geoloc_short = rs_wpss_wf_geoiploc_short( $ip ); }
	}

	/* Sanitized versions for output */
	$wpss_http_user_agent 			= rs_wpss_get_user_agent();
	$wpss_http_browser 				= rs_wpss_get_browser();
	$wpss_http_referer				= rs_wpss_get_referrer( FALSE, TRUE, TRUE ); /* Initial referrer, aka "Referring Site" - Changed 1.7.9 */
	if( empty( $spamshield_options['hide_extra_data'] ) ) {
		if( !empty( $cf7 ) ) { $text .= $eml_eol; }
		$text .= $eml_eol;
		$text .= $thin_line.$eml_eol;
		$text .= __( 'Additional Technical Data Added by WP-SpamShield', 'wp-spamshield' ) . $eml_eol;
		$text .= $thin_line.$eml_eol;
		/* DEBUG ONLY - BEGIN */
		if( strpos( WPSS_SERVER_NAME_REV, WPSS_DEBUG_SERVER_NAME_REV ) === 0 ) {
			/* CF7 Only */
			if( !empty( $cf7 ) ) {
				$wpss_log_session_data = rs_wpss_get_log_session_data();
				extract( $wpss_log_session_data );
				$noda = '[No Data]';
				/* Timer - BEGIN*/
				$wpss_time_end					= microtime(TRUE);
				if( empty( $wpss_time_init ) && !empty( $wpss_timestamp_init ) ) {
					$wpss_time_init = $wpss_timestamp_init;
				}
				if( !empty( $wpss_time_init ) ) {
					$wpss_time_on_site			= rs_wpss_timer( $wpss_time_init, $wpss_time_end, TRUE, 2 );
				} else { $wpss_time_on_site 	= $noda; }
				if( !empty( $wpss_timestamp_init ) ) {
					$wpss_site_entry_time		= get_date_from_gmt( date( 'Y-m-d H:i:s', $wpss_timestamp_init ), 'Y-m-d (D) H:i:s e' ); /* Added 1.7.3 */
				} else { $wpss_site_entry_time 	= $noda; }
				/* Timer - END */
				$wpss_hits_per_page = str_replace( WPSS_EOL, $eml_eol, $wpss_hits_per_page );
				$text .= "Pages Visited: ".$wpss_hits_per_page;
				$text .= "Time on Site: ['".$wpss_time_on_site."']".$eml_eol;
			}
			if( !empty( $post_ref2xjs ) ) {
				$ref2xJS = addslashes( urldecode( $post_ref2xjs ) );
				$ref2xJS = str_replace( '%3A', ':', $ref2xJS );
				$ref2xJS = str_replace( ' ', '+', $ref2xJS );
				$ref2xJS = esc_url_raw( $ref2xJS );
				$text .= $eml_eol."JS Page Referrer Check: $ref2xJS".$eml_eol;
			}
			if( !empty( $post_jsonst ) ) {
				$JSONST = sanitize_text_field( $post_jsonst );
				$text .= $eml_eol."JSONST: $JSONST".$eml_eol;
			}
		}
		/* DEBUG ONLY - END */
		else {
			if( !empty( $post_ref2xjs ) ) {
				$ref2xJS = addslashes( urldecode( $post_ref2xjs ) );
				$ref2xJS = str_replace( '%3A', ':', $ref2xJS );
				$ref2xJS = str_replace( ' ', '+', $ref2xJS );
				$ref2xJS = esc_url_raw( $ref2xJS );
				$text .= $eml_eol . __( 'Page Referrer Check.', 'wp-spamshield' ) . ': '.$ref2xJS.$eml_eol;
			}
		}
		$text .= $eml_eol;
		$text .= __( 'Referrer', 'wp-spamshield' ) . ': '.$wpss_http_referer.$eml_eol.$eml_eol; /* Initial referrer, aka "Referring Site" - Changed 1.7.9 */
		if( strpos( WPSS_SERVER_NAME_REV, WPSS_DEBUG_SERVER_NAME_REV ) === 0 && ( !empty( $_COOKIE['_referrer_og'] ) || !empty( $_COOKIE['_referrer_og'] ) ) ) { /* DEBUG ONLY */
			if( !empty( $_COOKIE['_referrer_og'] ) ) {
				$text .= __( 'Clicky Referrer', 'wp-spamshield' ) . ': '.$_COOKIE['_referrer_og'].$eml_eol;
			} elseif( !empty( $_COOKIE['JCS_INENREF'] ) ) {
				$text .= __( 'JCS_INENREF Referrer', 'wp-spamshield' ) . ': '.$_COOKIE['JCS_INENREF'].$eml_eol;
			}
			$text .= $eml_eol;
		}
		$text .= __( 'User-Agent (Browser/OS)', 'wp-spamshield' ) . ': '.$wpss_http_user_agent.$eml_eol;
		if( !empty( $wpss_http_browser ) ) {
			$text .= __( 'Browser', 'wp-spamshield' ) . ': '.$wpss_http_browser.$eml_eol;
		}
		if( strpos( WPSS_SERVER_NAME_REV, WPSS_DEBUG_SERVER_NAME_REV ) === 0 ) {
			if( !empty ( $wpss_geolocation ) && rs_wpss_is_lang_en_us() ) { /* English only for now; TO DO: TRANSLATE */
				$text .= __( 'Location', 'wp-spamshield' ) . ': '.$wpss_geolocation.$eml_eol;
			}
		} else {
			if( !empty ( $wpss_geoloc_short ) && rs_wpss_is_lang_en_us() ) { /* English only for now; TO DO: TRANSLATE */
				$text .= __( 'Country', 'wp-spamshield' ) . ': '.$wpss_geoloc_short.$eml_eol;
			}
		}
		$text .= __( 'IP Address', 'wp-spamshield' ) . ': '.$ip.$eml_eol;
		$text .= __( 'Server', 'wp-spamshield' ) . ': '.$reverse_dns.$eml_eol;
		$text .= __( 'IP Address Lookup', 'wp-spamshield' ) . ': http://ipaddressdata.com/'.$ip."\r\n\r\n";
		$text .= '(' . __( 'This data is helpful if you need to submit a spam sample.', 'wp-spamshield' ) . ')'.$eml_eol;
	}
	return $text;
}

function rs_wpss_add_ip_to_blacklist($ip_to_blacklist) {
	$blacklist_keys 		= trim(stripslashes(get_option('blacklist_keys')));
	$blacklist_keys_update	= $blacklist_keys.WPSS_EOL.$ip_to_blacklist;
	rs_wpss_update_bw_list_keys( 'black', $blacklist_keys_update );
}

/**
 *  Does comment or contact form contain blacklisted characters, words, IP addresses, or email addresses.
 *
 *  @since 1.5.4
 *
 *  @param string $author The author name of the submitter
 *  @param string $email The email of the submitter
 *  @param string $url The url used in the submission
 *  @param string $content The submitted content
 *  @param string $user_ip The submitter IP address
 *  @param string $user_agent The submitter's browser / user agent
 *  @param string $user_server The submitter's server (reverse DNS of IP)
 *  @return bool TRUE if submission contains blacklisted content, FALSE if submission does not
 */
function rs_wpss_blacklist_check( $author = NULL, $email = NULL, $url = NULL, $content = NULL, $user_ip = NULL, $user_agent = NULL, $user_server = NULL ) {
	/**
	 *  Fires at end of ( Comment | Contact Form | Registration | CF7 | GF | Misc Form ) filters.
	 *  Upgrade from WordPress' built-in and flawed wp_blacklist_check() function.
	 *  Removed User-Agent filter from wp_blacklist_check() - Not a good idea to let users play with User-Agent filtering, most people don't realize this will be tested, leading to false-positives.
	 *  Also, it's not in the documentation...nowhere in the WP Dashboard does it mention testing against User-Agents.
	 *
	 *  @since 1.5.4
	 *
	 *  @param string $author     	Comment | Contact Form | Registration | CF7 | GF | Misc Form: author name.
	 *  @param string $email      	Comment | Contact Form | Registration | CF7 | GF | Misc Form: author's email.
	 *  @param string $url        	Comment | Contact Form | Registration | CF7 | GF | Misc Form: author's URL.
	 *  @param string $content    	Comment | Contact Form | Registration | CF7 | GF | Misc Form: content.
	 *  @param string $user_ip    	Comment | Contact Form | Registration | CF7 | GF | Misc Form: author's IP address.
	 *  @param string $user_agent 	Comment | Contact Form | Registration | CF7 | GF | Misc Form: author's browser / user agent. - Ignored
	 *  @param string $user_server	Comment | Contact Form | Registration | CF7 | GF | Misc Form: author's server (reverse DNS of IP).
	 */

	$blacklist_keys = trim(stripslashes(get_option('blacklist_keys')));
	if( empty( $blacklist_keys ) ) { return FALSE; } /* If blacklist keys are empty */

	if( empty( $user_ip ) || empty( $user_server ) ) {
		/* IP / PROXY INFO - BEGIN */
		global $wpss_ip_proxy_info;
		if( empty( $wpss_ip_proxy_info ) ) { $wpss_ip_proxy_info = rs_wpss_ip_proxy_info(); }
		extract( $wpss_ip_proxy_info );
		/* IP / PROXY INFO - END */
		$user_ip = $ip; $user_server = $reverse_dns_lc;
	}

	if( FALSE !== strpos( $blacklist_keys, '[WPSS-ECBL][COUNTRY]' ) ) {
		global $wpss_geoiploc_data; if( empty ( $wpss_geoiploc_data ) ) { $wpss_geoiploc_data = rs_wpss_wf_geoiploc( $user_ip ); }
		if( !empty ( $wpss_geoiploc_data ) ) { extract( $wpss_geoiploc_data ); }
	}
	$blacklist_keys_arr = explode(WPSS_EOL, $blacklist_keys );
	foreach( (array) $blacklist_keys_arr as $key ) {
		$key = trim( $key );
		/* Skip empty lines */
		if( empty( $key ) ) { continue; }
		/* Do some escaping magic so that '~' chars in the spam words don't break things: */
		$key_pq = rs_wpss_preg_quote( $key );
		$pattern_regex = "~$key_pq~i";
		if( 0 === strpos( $key, '[WPSS-ECBL]' ) ) {
			$key = str_replace( '[WPSS-ECBL]', '', $key );
			if( WPSS_THIS_URL !== WPSS_SUPPORT_URL ) { /* Only if not on support URL - @since 1.9.9.5 */
				if( 0 === strpos( $key, '[SERVER]' ) && !empty( $user_server ) ) {
					$key = str_replace( '[SERVER]', '', $key );
					$referrer = rs_wpss_get_referrer( FALSE, TRUE, TRUE );
					$ref_domain = rs_wpss_get_domain( $referrer );
					if( 0 === strpos( $key, '[REF]' ) && !empty( $ref_domain ) ) { /* Added 1.8.1 */
						$key = str_replace( '[REF]', '', $key );
						$key_pq = rs_wpss_preg_quote( $key );
						if( preg_match( "~$key_pq$~i", $ref_domain ) ) { return TRUE; }
					} elseif( 0 === strpos( $key, '.' ) || 0 === strpos( $key, '-' ) ) {
						$key_pq = rs_wpss_preg_quote( $key );
						if( preg_match( "~$key_pq$~i", $user_server ) ) { return TRUE; }
					} elseif( $key === $user_server ) { return TRUE; }
				} elseif( 0 === strpos( $key, '[COUNTRY]' ) && !empty( $countryCode ) ) {
					/**
					 *  Country Blocking - Ex: '[WPSS-ECBL][COUNTRY]AA,BB,CC'
					 *  @since 1.9.5.2
					 *  Full list of ISO Country codes:
					 *  https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2
					 *  http://www.nationsonline.org/oneworld/country_code_list.htm
					 */
					$key = str_replace( array( '[COUNTRY]', ' ' ), array( '', '' ), $key );
					if( preg_match( "~([A-Z]{2},?)+~", $key ) ) {
						$key_arr = explode( ',', $key );
						if( $key === $countryCode || in_array( $countryCode, $key_arr, TRUE ) ) { return TRUE; }
					}
				}
			}
		} elseif( is_email( $key ) ) {
			if( !empty( $email ) && rs_wpss_sanitize_gmail( $key ) === rs_wpss_sanitize_gmail( $email ) ) { return TRUE; }
		} elseif( rs_wpss_is_valid_ip( $key, '', TRUE ) ) { /* IP C-block */
			if( !empty( $user_ip ) && strpos( $user_ip, $key ) === 0 ) { return TRUE; }
		} elseif( rs_wpss_is_valid_ip( $key ) ) { /* Complete IP Address */
			if( !empty( $user_ip ) && $key === $user_ip ) { return TRUE; }
		} elseif(
			   ( !empty( $author ) 	&& preg_match( $pattern_regex, $author ) )
			|| ( !empty( $url ) 	&& preg_match( $pattern_regex, $url ) )
			|| ( !empty( $content )	&& preg_match( $pattern_regex, $content ) )
			) {
			return TRUE;
		}
	}
	return FALSE;
}

function rs_wpss_whitelist_check( $email = NULL, $ip = NULL ) {
	/**
	 *  Fires at beginning of contact form, comment content, and other filters.
	 *  Bypasses filters if TRUE.
	 *  Still subject to cookies test on contact form to prevent potential abuse by bots.
	 *  @since 1.7.4
	 *  @param string $email      	Comment or Contact Form author's email.
	 */

	$ip = rs_wpss_get_ip_addr();
	$whitelist_keys = trim(stripslashes(get_option('spamshield_whitelist_keys')));
	if( empty( $whitelist_keys ) ) { return FALSE; } /* If whitelist keys or $email are empty */
	$whitelist_keys_arr = explode( WPSS_EOL, $whitelist_keys );
	foreach( (array) $whitelist_keys_arr as $key ) {
		$key = trim( $key );
		/* Skip empty lines */
		if( empty( $key ) ) { continue; }
		if( !empty( $email ) && is_email( $key ) && $key === $email ) { return TRUE; }
		if( rs_wpss_is_valid_ip( $key ) && $key === $ip ) { return TRUE; }
	}
	return FALSE;
}

function rs_wpss_get_bw_list_keys( $list ) {
	/**
	 *  Get blacklist or whitelist keys
	 *  $list - 'white' or 'black'
	 */
	$opname = array( 'white' => 'spamshield_whitelist_keys', 'black' => 'blacklist_keys' );
	$keys	= trim(stripslashes(get_option($opname[$list])));
	$arr	= explode(WPSS_EOL,$keys);
	$tmp	= rs_wpss_sort_unique($arr);
	$keys	= implode(WPSS_EOL,$tmp);
	return $keys;
}

function rs_wpss_update_bw_list_keys( $list, $keys ) {
	/**
	 *  Update blacklist or whitelist keys
	 *  $list - 'white' or 'black'
	 */
	$opname = array( 'white' => 'spamshield_whitelist_keys', 'black' => 'blacklist_keys' );
	$arr	= explode(WPSS_EOL,$keys);
	$tmp	= rs_wpss_sort_unique($arr);
	$keys	= implode(WPSS_EOL,$tmp);
	update_option( $opname[$list], $keys, FALSE );
}


/* Spam Registration Protection - BEGIN */

function rs_wpss_wc_before_register( ) {
	$wc_reg_enabled = get_option( 'woocommerce_enable_myaccount_registration' );
	if( $wc_reg_enabled !== 'yes' && $wc_reg_enabled != TRUE ) { return; }
	global $wpss_wc_reg_form_active; $wpss_wc_reg_form_active = TRUE;
}

function rs_wpss_register_form_append() {
	if( rs_wpss_is_user_logged_in() ) { return; }
	global $spamshield_options,$wpss_wc_reg_form_active,$wpss_reg_form_complete;
	if( empty( $spamshield_options ) ) { $spamshield_options = get_option('spamshield_options'); }
	rs_wpss_update_session_data($spamshield_options);

	/* Check if registration spam shield is disabled, or this function has already been run (usually by 3rd party plugin) */
	if( !empty( $spamshield_options['registration_shield_disable'] ) || !empty( $wpss_reg_form_complete ) ) { return; }

	/* BYPASS - HOOK */
	$reg_form_bypass = apply_filters( 'wpss_registration_form_bypass', FALSE );
	if( !empty( $reg_form_bypass ) ) { return; }

	/* BYPASS CHECKS COMPLETE - NOW START */

	$reg_form_append = $wpss_reg_form_complete = $buddypress_status = $wc_status = $s2member_status = $affiliates_status = $wpmembers_status = FALSE;
	$i18n_cap = !rs_wpss_is_lang_en_us() ? 'def' : '';
	$wc_style = $wc_req = ''; $end_line1 = '<br />'; $end_line2 = '</label>'; $reg_input_class = 'input';
	if( !empty( $wpss_wc_reg_form_active ) || rs_wpss_is_wc_login_page() || rs_wpss_is_wc_registration_page() ) { /* Check if we're on a WooCommerce Registration page */
		$wc_status = TRUE; $wpss_wc_reg_inprog = TRUE; $wc_style = ' class="form-row form-row-wide"'; $wc_req = ''; $reg_input_class = 'input-text'; $end_line1 = '</label>'; $end_line2 = '';
	}
	if( defined( 'WS_PLUGIN__S2MEMBER_VERSION' ) ) { $s2member_status = TRUE; }
	if( defined( 'AFFILIATES_CORE_VERSION' ) ) { $affiliates_status = TRUE; }
	if( defined( 'WPMEM_VERSION' ) ) { $wpmembers_status = TRUE; }

	if( FALSE === $s2member_status && FALSE === $affiliates_status ) {
		if( !rs_wpss_is_lang_en_us() ) { $locale = get_locale(); @load_textdomain( 'default', WP_LANG_DIR . "/admin-$locale.mo" ); $i18n_cap = 'def'; } else { $i18n_cap = ''; }
		$new_fields = array(
			'first_name' 	=> rs_wpss_first_name_txt( $i18n_cap ),
			'last_name' 	=> rs_wpss_last_name_txt( $i18n_cap ),
			'disp_name' 	=> rs_wpss_disp_name_txt( $i18n_cap ),
			);
		if( TRUE === $wpmembers_status ) {
			unset( $new_fields['first_name'], $new_fields['last_name'] );
		}

		if( TRUE === $wc_status ) {
			unset( $new_fields['disp_name'] );
		}

		foreach( $new_fields as $k => $v ) {
			$reg_form_append .= '	<p'.$wc_style.'>
		<label for="'.$k.'">'.$v.$wc_req.$end_line1.'
		<input type="text" name="'.$k.'" id="'.$k.'" class="'.$reg_input_class.'" value="" size="25" />'.$end_line2.'
	</p>
';
		}
	}

	if( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) || TRUE === $wc_status || TRUE === $affiliates_status || rs_wpss_is_3p_register_page() ) {
		$wpss_key_values 		= rs_wpss_get_key_values();
		$wpss_js_key 			= $wpss_key_values['wpss_js_key'];
		$wpss_js_val 			= $wpss_key_values['wpss_js_val'];
		global $wpss_ao_active; $ao_noop_open = $ao_noop_close = '';
		if( empty( $wpss_ao_active ) ) { $wpss_ao_active = WPSS_Compatibility::is_plugin_active( 'autoptimize/autoptimize.php' ); }
		if( !empty( $wpss_ao_active ) ) { $ao_noop_open = '<!--noptimize-->'; $ao_noop_close = '<!--/noptimize-->'; }
		$reg_form_append .= WPSS_EOL."\t".$ao_noop_open.'<script type=\'text/javascript\'>'.WPSS_EOL."\t".'/* <![CDATA[ */'.WPSS_EOL."\t".WPSS_REF2XJS.'=escape(document[\'referrer\']);'.WPSS_EOL."\t".'hf3N=\''.$wpss_js_key.'\';'.WPSS_EOL."\t".'hf3V=\''.$wpss_js_val.'\';'.WPSS_EOL."\t".'document.write("<input type=\'hidden\' name=\''.WPSS_REF2XJS.'\' value=\'"+'.WPSS_REF2XJS.'+"\' /><input type=\'hidden\' name=\'"+hf3N+"\' value=\'"+hf3V+"\' />");'.WPSS_EOL."\t".'/* ]]> */'.WPSS_EOL."\t".'</script>'.$ao_noop_close;
	}
	$reg_form_append .= WPSS_EOL."\t".'<noscript><input type="hidden" name="'.WPSS_JSONST.'" value="NS3" /></noscript>'.WPSS_EOL."\t";
	$wpss_js_disabled_msg 	= __( 'Currently you have JavaScript disabled. In order to register, please make sure JavaScript and Cookies are enabled, and reload the page.', 'wp-spamshield' );
	$wpss_js_enable_msg 	= __( 'Click here for instructions on how to enable JavaScript in your browser.', 'wp-spamshield' );
	$reg_form_append .= '<noscript><p><strong>'.$wpss_js_disabled_msg.'</strong> <a href="http://enable-javascript.com/" rel="nofollow external" >'.$wpss_js_enable_msg.'</a><br /><br /></p></noscript>'.WPSS_EOL."\t";

	/* If need to add anything else to registration area, start here */


	/* FORM COMPLETE */
	$wpss_wc_reg_form_active = FALSE;
	$wpss_reg_form_complete = TRUE;

	if( TRUE === $affiliates_status ) { return $reg_form_append; } else { echo $reg_form_append; }
}

if( !function_exists('wp_new_user_notification') ) {
	function wp_new_user_notification( $user_id, $deprecated = NULL, $notify = '' ) {
		/**
		 *  WPSS Redefined: Copied from includes/pluggable.php in WordPress core and added filters.
		 *  @param int		$user_id		User ID.
		 *  @param null		$deprecated		Not used (argument deprecated).
		 *  @param string	$notify			Optional. Type of notification that should happen. Accepts 'admin' or an empty string (admin only), 'user', or 'both' (admin and user). Default empty.
		 *	Modified in 1.9.5.7 to add compatibility for WP 4.3+ and maintain backwards compatibility for earlier WP versions.
		 *  Modified in 1.9.6.2 for WP 4.3.1+
		 *  Modified in 1.9.7.4 to fix backwards compatibility for WP versions < 4.3.
		 *  Modified in 1.9.9.1 for 4.6+: The `$notify` parameter accepts 'user' for sending notification only to the user created.
		 */
		global $wpdb, $wp_hasher;
		if( rs_wpss_is_wp_ver('4.3.1') && $deprecated !== NULL ) {
			_deprecated_argument( __FUNCTION__, '4.3.1' );
		}
		$user = get_userdata( $user_id );
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		if ( 'user' !== $notify ) {
			$admin_message	= sprintf( __( 'New user registration on your site %s:' ), $blogname ) . "\r\n\r\n";
			$admin_message .= sprintf( __( 'Username: %s' ), $user->user_login ) . "\r\n\r\n";
			$admin_message .= sprintf( __( 'Email: %s' ), $user->user_email ) . "\r\n";
			$admin_message	= apply_filters( 'wpss_signup_notification_text_admin', $admin_message, $user_id, $user );
			WP_SpamShield::mail( get_option( 'admin_email' ), sprintf( __( '[%s] New User Registration' ), $blogname ), $admin_message );
		}
		if( rs_wpss_is_wp_ver('4.3') && !rs_wpss_is_wp_ver('4.3.1') ) {
			$notify = $deprecated; /* 4.3 only - '$deprecated' is '$notify' */
		}
		/* '$deprecated' was the pre-4.3 '$plaintext_pass'. An empty '$plaintext_pass' didn't send a user notifcation. */
		if( 'admin' === $notify || ( empty( $deprecated ) && empty( $notify ) ) ) { return; } /* Changed WP 4.4 */
		$key = wp_generate_password( 20, false );
		do_action( 'retrieve_password_key', $user->user_login, $key );
		if( empty( $wp_hasher ) ) {
			require_once ABSPATH . WPINC . '/class-phpass.php';
			$wp_hasher = new PasswordHash( 8, true );
		}
		$hashed = time() . ':' . $wp_hasher->HashPassword( $key );
		$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );
		$user_message  = sprintf(__('Username: %s'), $user->user_login) . "\r\n";
		if( !rs_wpss_is_wp_ver('4.3') ) {
			$user_message .= sprintf(__('Password: %s'), $deprecated) . "\r\n"; /* $deprecated is the pre-4.3 '$plaintext_pass' */
		} else { /* 4.3+ */
			$user_message .= __( 'To set your password, visit the following address:' ) . "\r\n\r\n";
			$user_message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user->user_login ), 'login') . ">\r\n\r\n";
		}
		$user_subject_text = 'Your username and password info';
		$user_message .= wp_login_url() . "\r\n";
		$user_message = apply_filters( 'wpss_signup_notification_text_user', $user_message, $user_id, $user );
		WP_SpamShield::mail( $user->user_email, sprintf( __( '[%s] '.$user_subject_text ), $blogname ), $user_message );
	}

	/* WPSS Added */

	function rs_wpss_modify_signup_notification_admin( $text, $user_id, $user ) {
		/* Check if registration spam shield is disabled - Added in 1.6.9 */
		global $spamshield_options; if( empty( $spamshield_options ) ) { $spamshield_options = get_option('spamshield_options'); }
		if( !empty( $spamshield_options['registration_shield_disable'] ) ) { return $text; }

		$wpss_display_name 		= $user->display_name;
		$wpss_user_firstname 	= $user->user_firstname;
		$wpss_user_lastname 	= $user->user_lastname;
		$wpss_user_email		= $user->user_email;
		$wpss_user_url			= $user->user_url;
		$wpss_user_login 		= $user->user_login;
		$wpss_user_id	 		= $user->ID;

		if( !rs_wpss_is_lang_en_us() ) { $locale = get_locale(); @load_textdomain( 'default', WP_LANG_DIR . "/admin-$locale.mo" ); $i18n_cap = 'def'; } else { $i18n_cap = ''; }
		$disp_name_txt 			= rs_wpss_disp_name_txt( $i18n_cap );
		$first_name_txt 		= rs_wpss_first_name_txt( $i18n_cap );
		$last_name_txt 			= rs_wpss_last_name_txt( $i18n_cap );
		$user_id_txt 			= __( 'User ID', 'wp-spamshield' );

		$text .= "\r\n";
		$text .= sprintf( $disp_name_txt.': %s', $wpss_display_name ) . "\r\n\r\n";
		$text .= sprintf( $first_name_txt.': %s', $wpss_user_firstname ) . "\r\n\r\n";
		$text .= sprintf( $last_name_txt.': %s', $wpss_user_lastname ) . "\r\n\r\n";
		$text .= sprintf( $user_id_txt .': %s', $wpss_user_id ) . "\r\n\r\n\r\n";

		$text = rs_wpss_extra_notification_data( $text, $spamshield_options );

		return $text;
	}

	function rs_wpss_modify_signup_notification_user( $text, $user_id, $user ) {

		/* Check if registration spam shield is disabled - Added in 1.6.9 */
		global $spamshield_options; if( empty( $spamshield_options ) ) { $spamshield_options = get_option('spamshield_options'); }
		if( !empty( $spamshield_options['registration_shield_disable'] ) ) { return $text; }

		/* Add three new fields */
		$wpss_display_name 		= $user->display_name;
		$wpss_user_firstname 	= $user->user_firstname;
		$wpss_user_lastname 	= $user->user_lastname;
		$wpss_user_email		= $user->user_email;
		$wpss_user_url			= $user->user_url;
		$wpss_user_login 		= $user->user_login;
		$wpss_user_id	 		= $user->ID;

		if( !rs_wpss_is_lang_en_us() ) { $locale = get_locale(); @load_textdomain( 'default', WP_LANG_DIR . "/admin-$locale.mo" ); $i18n_cap = 'def'; } else { $i18n_cap = ''; }
		$disp_name_txt 			= rs_wpss_disp_name_txt( $i18n_cap );
		$first_name_txt 		= rs_wpss_first_name_txt( $i18n_cap );
		$last_name_txt 			= rs_wpss_last_name_txt( $i18n_cap );

		$text .= "\r\n\r\n";
		$text .= sprintf( $disp_name_txt.': %s', $wpss_display_name ) . "\r\n";
		$text .= sprintf( $first_name_txt.': %s', $wpss_user_firstname ) . "\r\n";
		$text .= sprintf( $last_name_txt.': %s', $wpss_user_lastname ) . "\r\n\r\n";

		return $text;
	}

}

function rs_wpss_sanitize_new_user_email( $email ) {
	return rs_wpss_sanitize_gmail( $email, TRUE );
}

function rs_wpss_wc_check_new_user( $errors ) {
	/* WooCommerce wrapper for rs_wpss_check_new_user() */
	if( isset( $_GET['action'] ) && $_GET['action'] === 'woocommerce_checkout' ) { return $errors; }
	$ecom_urls = unserialize( WPSS_ECOM_URLS );
	if( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['email_2'] ) ) { unset( $_POST['email_2'] ); }
	$req_uri_lc	= rs_wpss_casetrans('lower',$_SERVER['REQUEST_URI']);
	foreach( $ecom_urls as $k => $u ) {
		if( strpos( $req_uri_lc, $u ) !== FALSE ) { return $errors; }
	}
	global $wpss_wc_reg_inprog; $wpss_wc_reg_inprog = TRUE; /* WooCommerce registration is in progress */
	$errors = rs_wpss_check_new_user( $errors );
	return $errors;
}

function rs_wpss_check_new_user( $errors = NULL, $user_login = NULL, $user_email = NULL ) {
	/* Error checking for new user registration */
	global $spamshield_options,$wpss_reg_err_chk_complete,$wpss_reg_inprog,$wpss_wc_reg_inprog;

	if( rs_wpss_is_user_logged_in() || !empty( $wpss_reg_err_chk_complete ) ) { return $errors; }
	$req_uri_lc	= rs_wpss_casetrans('lower',$_SERVER['REQUEST_URI']);

	if( !empty( $wpss_wc_reg_inprog ) || WPSS_Compatibility::is_woocom_enabled() ) {
		/* Check if we're on a WooCommerce Checkout Page */
		if( ( isset( $_GET['action'] ) && $_GET['action'] === 'woocommerce_checkout' ) ) { return $errors; }
		$ecom_urls = unserialize( WPSS_ECOM_URLS );
		foreach( $ecom_urls as $k => $u ) {
			if( strpos( $req_uri_lc, $u ) !== FALSE ) { return $errors; }
		}
	} elseif( WPSS_Compatibility::is_ecom_enabled() ) {
		/* Check if we're on another e-commerce Checkout or Shopping Cart Page */
		$ecom_urls = unserialize( WPSS_ECOM_URLS );
		foreach( $ecom_urls as $k => $u ) {
			if( strpos( $req_uri_lc, $u ) !== FALSE ) { return $errors; }
		}
	}

	if( empty( $spamshield_options ) ) { $spamshield_options = get_option('spamshield_options'); }
	if( !empty( $spamshield_options['registration_shield_disable'] ) ) { return $errors; }

	/* BYPASS - HOOK */
	$reg_check_bypass = apply_filters( 'wpss_registration_check_bypass', FALSE );
	if( !empty( $reg_check_bypass ) ) { return $errors; }


	/* BYPASS CHECKS COMPLETE - NOW START */

	if( empty( $errors ) || !is_object( $errors ) ) { $errors = new WP_Error; }

	$reg_filter_status		= $wpss_error_code = $log_pref = '';
	$reg_jsck_error			= $reg_badrobot_error = $wpss_reg_err_chk_complete = $buddypress_status = $wc_status = $s2member_status = $wpmembers_status = $affiliates_status = FALSE;
	$wpss_reg_inprog		= TRUE;
	$ns_val					= 'NS3';
	$pref					= 'R-';
	$errors_3p				= array(); /* Error array for 3rd party plugins that don't follow WordPress standards for registration processing: BuddyPress, ... */
	$error_txt 				= rs_wpss_error_txt();

	if( class_exists( 'BuddyPress' ) ) {
		if( empty( $user_login ) && isset( $_POST['signup_username'] ) ) {
			$user_login			= rs_wpss_casetrans( 'lower', sanitize_user( wp_unslash( $_POST['signup_username'] ) ) );
			$buddypress_status	= TRUE;
			$log_pref			= 'bp-';
		}
		if( empty( $user_email ) && isset( $_POST['signup_email'] ) ) {
			$user_email			= rs_wpss_casetrans( 'lower', sanitize_email( wp_unslash( $_POST['signup_email'] ) ) );
			$buddypress_status	= TRUE;
			$log_pref			= 'bp-';
		}
	}

	if( !empty( $wpss_wc_reg_inprog ) ) { $wc_status = TRUE; $log_pref = 'wc-'; }
	if( defined( 'WS_PLUGIN__S2MEMBER_VERSION' ) ) { $s2member_status = TRUE; $log_pref = 's2-'; }
	if( defined( 'AFFILIATES_CORE_VERSION' ) ) { $affiliates_status = TRUE; $log_pref = 'aff-'; }
	if( defined( 'WPMEM_VERSION' ) ) { $wpmembers_status = TRUE; $log_pref = 'wpm-'; }

	if( TRUE === $wc_status ) {
		$user_login = '';
		if( empty( $user_login ) && isset( $_POST['username'] ) ) {
			$user_login		= rs_wpss_casetrans( 'lower', sanitize_user( wp_unslash( $_POST['username'] ) ) );
		}
		if( empty( $user_email ) && isset( $_POST['email'] ) ) {
			$user_email		= rs_wpss_casetrans( 'lower', sanitize_email( wp_unslash( $_POST['email'] ) ) );
		}
		if( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['email_2'] ) ) { unset( $_POST['email_2'] ); }
	}

	if( TRUE === $affiliates_status ) {
		if( empty( $user_login ) && isset( $_POST['user_login'] ) ) {
			$user_login		= rs_wpss_casetrans( 'lower', sanitize_user( wp_unslash( $_POST['user_login'] ) ) );
		}
		if( empty( $user_email ) && isset( $_POST['user_email'] ) ) {
			$user_email		= rs_wpss_casetrans( 'lower', sanitize_email( wp_unslash( $_POST['user_email'] ) ) );
		}
	}

	/* WPSS Whitelist Check */
	if( !empty( $spamshield_options['enable_whitelist'] ) && rs_wpss_whitelist_check( $user_email ) ) { return $errors; }

	if( !rs_wpss_is_lang_en_us() ) { $locale = get_locale(); @load_textdomain( 'default', WP_LANG_DIR . "/admin-$locale.mo" ); $i18n_cap = 'def'; } else { $i18n_cap = ''; }
	$new_fields = array(
		'first_name' 	=> rs_wpss_first_name_txt( $i18n_cap ),
		'last_name' 	=> rs_wpss_last_name_txt( $i18n_cap ),
		'disp_name' 	=> rs_wpss_disp_name_txt( $i18n_cap ),
		);
	$user_data = array();
	foreach( $new_fields as $k => $v ) {
		if( isset( $_POST[$k] ) ) { $user_data[$k] = sanitize_text_field( wp_unslash( $_POST[$k] ) ); } else { $user_data[$k] = ''; }
	}

	if( FALSE === $buddypress_status && FALSE === $wc_status && FALSE === $s2member_status && FALSE === $affiliates_status ) {
		/* Check New Fields for Blanks */
		$enter_your_x_txt = rs_wpss_enter_your_x_txt();
		foreach( $new_fields as $k => $v ) {
			$k_uc = rs_wpss_casetrans('upper',$k);
			if( empty( $_POST[$k]) ) {
				$errors->add( 'empty_'.$k, '<strong>'.$error_txt.':</strong> ' . sprintf( $enter_your_x_txt . '.', $v ) );
				$wpss_error_code .= ' R-BLANK-'.$k_uc;
			}
		}
	}

	/* BAD ROBOT TEST - BEGIN */
	$bad_robot_filter_data 	 = rs_wpss_bad_robot_blacklist_chk( 'register', $reg_filter_status, '', '', $user_data['disp_name'], $user_email );
	$reg_filter_status		 = $bad_robot_filter_data['status'];
	$bad_robot_blacklisted 	 = $bad_robot_filter_data['blacklisted'];
	if( !empty( $bad_robot_blacklisted ) ) {
		$wpss_error_code 	.= $bad_robot_filter_data['error_code'];
		$reg_badrobot_error	 = TRUE;
	}
	/* BAD ROBOT TEST - END */

	/* BAD ROBOTS */
	if( $reg_badrobot_error !== FALSE ) {
		$err_cod = 'badrobot_error';
		$err_msg = __('User registration is currently not allowed.');
		if( TRUE === $buddypress_status ) { $errors_3p[$err_cod] = $err_msg; } else { $errors->add( $err_cod, '<strong>'.$error_txt.':</strong> ' . $err_msg ); }
	}

	/* JS/COOKIES CHECK */
	$wpss_ck_key_bypass 	= $wpss_js_key_bypass = FALSE;
	$wpss_key_values 		= rs_wpss_get_key_values();
	extract( $wpss_key_values );
	$wpss_jsck_cookie_val	= !empty( $_COOKIE[$wpss_ck_key] )	? $_COOKIE[$wpss_ck_key]	: '';
	$wpss_jsck_field_val	= !empty( $_POST[$wpss_js_key] )	? $_POST[$wpss_js_key]		: '';
	$wpss_jsck_jquery_val	= !empty( $_POST[$wpss_jq_key] )	? $_POST[$wpss_jq_key]		: '';
	if( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) ) { /* 1.9.1 */
		$wpss_ck_key_bypass = TRUE;
	}
	if( FALSE === $wpss_ck_key_bypass ) { /* 1.8.9 */
		/* If jscripts.php is disabled, these would be skipped - Compatibility Mode */
		if( $wpss_jsck_cookie_val !== $wpss_ck_val ) {
			$wpss_error_code .= ' '.$pref.'COOKIE-3';
			$reg_jsck_error = TRUE;
		}
		if( $wpss_jsck_jquery_val !== $wpss_jq_val ) {
			$wpss_error_code .= ' '.$pref.'JQHFT-3';
			$reg_jsck_error = TRUE;
		}
	}
	if( FALSE === $wpss_js_key_bypass ) {
		if( $wpss_jsck_field_val !== $wpss_js_val ) {
			$wpss_error_code .= ' '.$pref.'FVFJS-3';
			$reg_jsck_error = TRUE;
		}
	}
	$post_jsonst 	= !empty( $_POST[WPSS_JSONST] ) ? trim( $_POST[WPSS_JSONST] ) : '';
	$post_jsonst_lc	= rs_wpss_casetrans('lower',$post_jsonst);
	if( FALSE === $buddypress_status ) {
		if( $post_jsonst_lc === 'ns1' || $post_jsonst_lc === 'ns2' || $post_jsonst_lc === 'ns3' || $post_jsonst_lc === 'ns4' || $post_jsonst_lc === 'ns5' ) {
			$wpss_error_code .= ' '.$pref.'JSONST-1000-3';
			$reg_jsck_error = TRUE;
		}
	}

	if( $reg_jsck_error !== FALSE && $reg_badrobot_error !== TRUE ) {
		$err_cod = 'jsck_error';
		$err_msg = __( 'JavaScript and Cookies are required in order to register. Please be sure JavaScript and Cookies are enabled in your browser, and reload the page.', 'wp-spamshield' ); /* NEEDS TRANSLATION */
		if( TRUE === $buddypress_status ) { $errors_3p[$err_cod] = $err_msg; } else { $errors->add( $err_cod, '<strong>'.$error_txt.':</strong> ' . $err_msg ); }
	}

	if( FALSE === $wc_status ) {
		/* EMAIL BLACKLIST */
		if( rs_wpss_email_blacklist_chk( $user_email ) ) {
			$wpss_error_code .= ' '.$pref.'9200E-BL';
			if( $reg_badrobot_error !== TRUE && $reg_jsck_error !== TRUE ) {
				$err_cod = 'blacklist_email_error';
				$err_msg = __('Sorry, that email address is not allowed!') . ' ' . __('Please enter a valid email address.');
				if( TRUE === $buddypress_status ) { $errors_3p[$err_cod] = $err_msg; } else { $errors->add( $err_cod, '<strong>'.$error_txt.':</strong> ' . $err_msg ); }
			}
		}
	}

	if( FALSE === $buddypress_status && FALSE === $wc_status && FALSE === $s2member_status && FALSE === $affiliates_status ) {
		/* AUTHOR KEYPHRASE BLACKLIST */
		foreach( $user_data as $k => $v ) {
			$k_uc = rs_wpss_casetrans('upper',$k);
			if( ( $k === 'user_login' || $k === 'first_name' || $k === 'last_name' || $k === 'disp_name' ) && rs_wpss_anchortxt_blacklist_chk($v) ) {
				$wpss_error_code .= ' '.$pref.'10500A-BL-'.$k_uc;
				if( $reg_badrobot_error !== TRUE && $reg_jsck_error !== TRUE ) {
					$nfk = $new_fields[$k];
					$errors->add( 'blacklist_'.$k.'_error', '<strong>'.$error_txt.':</strong> ' . sprintf( __( '"%1$s" appears to be spam. Please enter a different value in the <strong> %2$s </strong> field.', 'wp-spamshield' ), sanitize_text_field($v), $nfk ) );
				}
			}
		}
	}

	/* Enhanced Comment Blacklist - R-0-ECBL*/
	if( !empty( $spamshield_options['enhanced_comment_blacklist'] ) && empty( $wpss_error_code ) ) {
		if( rs_wpss_blacklist_check( '', $user_email ) ) {
			$wpss_error_code .= ' '.$pref.'0-ECBL';
			$err_cod = 'blacklist_ecbl_error';
			$err_msg = __('User registration is currently not allowed.');
			if( TRUE === $buddypress_status ) { $errors_3p[$err_cod] = $err_msg; } else { $errors->add( $err_cod, '<strong>'.$error_txt.':</strong> ' . $err_msg ); }

		}
	}

	if( FALSE === $wc_status ) {
		/* BLACKLISTED USER */
		if( empty( $wpss_error_code ) && rs_wpss_ubl_cache() ) {
			$wpss_error_code .= ' '.$pref.'0-BL';
			$err_cod = 'blacklisted_user_error';
			$err_msg = __('User registration is currently not allowed.');
			if( TRUE === $buddypress_status ) { $errors_3p[$err_cod] = $err_msg; } else { $errors->add( $err_cod, '<strong>'.$error_txt.':</strong> ' . $err_msg ); }
		}
	}

	/* Done with Tests */

	/* Now Log the Errors, if any */

	$post_ref2xjs 		= !empty( $_POST[WPSS_REF2XJS] ) ? trim( $_POST[WPSS_REF2XJS] ) : '';
	$post_ref2xjs_lc	= rs_wpss_casetrans( 'lower',$post_ref2xjs );
	if( !empty( $post_ref2xjs ) ) {
		$ref2xJS = rs_wpss_casetrans( 'lower', addslashes( urldecode( $post_ref2xjs ) ) );
		$ref2xJS = str_replace( '%3a', ':', $ref2xJS );
		$ref2xJS = str_replace( ' ', '+', $ref2xJS );
		$wpss_javascript_page_referrer = esc_url_raw( $ref2xJS );
	} else { $wpss_javascript_page_referrer = '[None]'; }

	if( $post_jsonst_lc === 'ns1' || $post_jsonst_lc === 'ns2' || $post_jsonst_lc === 'ns3' || $post_jsonst_lc === 'ns4' || $post_jsonst_lc === 'ns5' ) { $wpss_jsonst = $post_jsonst; } else { $wpss_jsonst = '[None]'; }

	$user_id = 'None'; /* Possibly change to '' */

	$register_author_data = array(
		'display_name' 				=> $user_data['disp_name'],
		'user_firstname' 			=> $user_data['first_name'],
		'user_lastname' 			=> $user_data['last_name'],
		'user_email' 				=> $user_email,
		'user_login' 				=> $user_login,
		'ID' 						=> $user_id,
		'comment_author'			=> $user_data['disp_name'],
		'comment_author_email'		=> $user_email,
		'comment_author_url'		=> '',
		'javascript_page_referrer'	=> $wpss_javascript_page_referrer,
		'jsonst'					=> $wpss_jsonst,
		);
	if( empty( $register_author_data['comment_author'] ) && !empty( $user_login ) ) { $register_author_data['comment_author'] = $user_login; }

	unset( $wpss_javascript_page_referrer, $wpss_jsonst );

	$wpss_error_code = trim( $wpss_error_code );
	if( strpos( $wpss_error_code, '0-BL' ) !== FALSE || strpos( $wpss_error_code, '0-ECBL' ) !== FALSE ) {
		rs_wpss_append_log_data( 'Blacklisted user detected. Registration has been temporarily disabled to prevent spam. ERROR CODE: '.$wpss_error_code, FALSE );
	}

	if( !empty( $wpss_error_code ) ) {
		if( TRUE === $buddypress_status ) {
			$wpss_error_code = str_replace( 'R-', 'BPR-', $wpss_error_code );
		} elseif( TRUE === $wc_status ) {
			$wpss_error_code = str_replace( 'R-', 'WCR-', $wpss_error_code );
		} elseif( TRUE === $s2member_status ) {
			$wpss_error_code = str_replace( 'R-', 'S2R-', $wpss_error_code );
		} elseif( TRUE === $wpmembers_status ) {
			$wpss_error_code = str_replace( 'R-', 'WPMR-', $wpss_error_code );
		} elseif( TRUE === $affiliates_status ) {
			$wpss_error_code = str_replace( 'R-', 'AFFR-', $wpss_error_code );
		}
		rs_wpss_update_accept_status( $register_author_data, 'r', 'Line: '.__LINE__, $wpss_error_code );
		rs_wpss_increment_reg_count();
		if( !empty( $spamshield_options['comment_logging'] ) ) {
			rs_wpss_log_data( $register_author_data, $wpss_error_code, $log_pref.'register' );
		}
	} elseif( TRUE === $buddypress_status ) {
		rs_wpss_update_accept_status( $register_author_data, 'a', 'Line: '.__LINE__ );
		if( !empty( $spamshield_options['comment_logging'] ) && !empty( $spamshield_options['comment_logging_all'] ) ) {
			rs_wpss_log_data( $register_author_data, $wpss_error_code, $log_pref.'register' );
		}
	}

	/* Now return the error values, or output error message */
	if( TRUE === $wc_status ) { $wpss_wc_reg_inprog = FALSE; }
	if( !empty( $wpss_error_code ) ) {
		if( TRUE === $buddypress_status ) {
			$error_msg = '';
			foreach( $errors_3p as $c => $m ) {
				$error_msg .= '<strong>'.$error_txt.':</strong> '.$m.'<br /><br />'.WPSS_EOL;
			}
			WP_SpamShield::wp_die( $error_msg, TRUE );
		}
	} elseif( TRUE === $wc_status ) {
		rs_wpss_update_accept_status( $register_author_data, 'a', 'Line: '.__LINE__ );
		if( !empty( $spamshield_options['comment_logging'] ) && !empty( $spamshield_options['comment_logging_all'] ) ) {
			rs_wpss_log_data( $register_author_data, $wpss_error_code, $log_pref.'register' );
		}
	}
	$wpss_reg_inprog = FALSE; $wpss_reg_err_chk_complete = TRUE;
	return $errors;
}

function rs_wpss_user_register( $user_id ) {
	if( rs_wpss_is_login_page() || rs_wpss_is_3p_register_page() ) {
		global $spamshield_options; if( empty( $spamshield_options ) ) { $spamshield_options = get_option('spamshield_options'); }
		$buddypress_status 		= $s2member_status = $wpmembers_status = FALSE;
		$log_pref 				= '';

		/* Check if registration spam shield is disabled - Added in 1.6.9 */
		if( !empty( $spamshield_options['registration_shield_disable'] ) ) { return; }

		if( defined( 'WS_PLUGIN__S2MEMBER_VERSION' ) ) { $s2member_status = TRUE; $log_pref = 's2-'; }
		if( defined( 'AFFILIATES_CORE_VERSION' ) ) { $affiliates_status = TRUE; $log_pref = 'aff-'; }
		if( defined( 'WPMEM_VERSION' ) ) { $wpmembers_status = TRUE; $log_pref = 'wpm-'; }

		if( !rs_wpss_is_lang_en_us() ) { $locale = get_locale(); @load_textdomain( 'default', WP_LANG_DIR . "/admin-$locale.mo" ); $i18n_cap = 'def'; } else { $i18n_cap = ''; }
		$new_fields = array(
			'first_name' 	=> rs_wpss_first_name_txt( $i18n_cap ),
			'last_name' 	=> rs_wpss_last_name_txt( $i18n_cap ),
			'disp_name' 	=> rs_wpss_disp_name_txt( $i18n_cap ),
			);
		$user_data = array();
		foreach( $new_fields as $k => $v ) {
			if( isset( $_POST[$k] ) ) { $user_data[$k] = sanitize_text_field( wp_unslash( $_POST[$k] ) ); } else { $user_data[$k] = ''; }
		}
		if( !empty($user_data) ) {
			$user_data['ID']			= $user_id;
			$user_data['display_name']	= $user_data['disp_name'];
			unset( $user_data['disp_name'] );
			wp_update_user( $user_data );
		}

		$wpss_display_name = $wpss_user_firstname = $wpss_user_lastname = $wpss_user_email = $wpss_user_url = $wpss_user_login = '';

		$user_info = get_userdata( $user_id );

		if( isset( $user_info->display_name ) ) 	{ $wpss_display_name	= $user_info->display_name; }
		if( isset( $user_info->user_firstname ) ) 	{ $wpss_user_firstname	= $user_info->user_firstname; }
		if( isset( $user_info->user_lastname ) ) 	{ $wpss_user_lastname	= $user_info->user_lastname; }
		if( isset( $user_info->user_email ) ) 		{ $wpss_user_email		= $user_info->user_email; }
		if( isset( $user_info->user_url ) ) 		{ $wpss_user_url		= $user_info->user_url; }
		if( isset( $user_info->user_login ) ) 		{ $wpss_user_login		= $user_info->user_login; }

		$wpss_comment_author 		= $wpss_display_name;
		$wpss_comment_author_email	= $wpss_user_email;
		$wpss_comment_author_url 	= $wpss_user_url;

		$register_author_data = array(
			'display_name' 			=> $wpss_display_name,
			'user_firstname' 		=> $wpss_user_firstname,
			'user_lastname' 		=> $wpss_user_lastname,
			'user_email' 			=> $wpss_user_email,
			'user_url' 				=> $wpss_user_url,
			'user_login' 			=> $wpss_user_login,
			'ID' 					=> $user_id,
			'comment_author'		=> $wpss_display_name,
			'comment_author_email'	=> $wpss_user_email,
			'comment_author_url'	=> $wpss_user_url,
			);

		$wpss_error_code = 'No Error';

		rs_wpss_update_user_ip( $user_id );

		rs_wpss_update_accept_status( $register_author_data, 'a', 'Line: '.__LINE__ );
		if( !empty( $spamshield_options['comment_logging'] ) && !empty( $spamshield_options['comment_logging_all'] ) ) {
			rs_wpss_log_data( $register_author_data, $wpss_error_code, $log_pref.'register' );
		}
	}
}

/* Spam Registration Protection - END */

/* Admin Functions - BEGIN */

function rs_wpss_admin_jp_fix( $get_jp_com_status = FALSE ) {
	/**
	 *  Fix compatibility with JetPack if active
	 *  The JP Comments module modifies WordPress' comment system core functionality, incapacitating MANY fine plugins.
	 *  Compatibility with JetPack Comments added in 1.9.2, so this only runs when in Compatibility Mode.
	 */
	global $wpss_jp_active,$wpss_jp_active_mods,$wpss_jp_fix;
	if( FALSE === $get_jp_com_status && ( is_multisite() || !empty( $wpss_jp_fix ) ) ) { return; }
	if( empty( $wpss_jp_active ) ) { $wpss_jp_active = WPSS_Compatibility::is_plugin_active( 'jetpack/jetpack.php' ); }
	if( !empty( $wpss_jp_active ) ) {
		if( empty( $wpss_jp_active_mods ) ) { $wpss_jp_active_mods = get_option('jetpack_active_modules'); }
		if( !empty( $wpss_jp_active_mods ) && is_array( $wpss_jp_active_mods ) ) { $jp_com_key = array_search( 'comments', $wpss_jp_active_mods, TRUE ); } else { $wpss_jp_fix = TRUE; return FALSE; }
		if( isset( $jp_com_key ) && is_int( $jp_com_key ) ) {
			if( TRUE === $get_jp_com_status ) { return TRUE; }
			$jp_num_active_mods = count( $wpss_jp_active_mods );
			if( empty( $wpss_jp_active_mods ) ) { $jp_num_active_mods = 0; }
			if( $jp_num_active_mods < 2 ) { $wpss_jp_active_mods = array(); } else { unset( $wpss_jp_active_mods[$jp_com_key] ); }
			update_option( 'jetpack_active_modules', $wpss_jp_active_mods );
			rs_wpss_append_log_data( 'JetPack Comments module deactivated.', FALSE );
		}
	}
	$wpss_jp_fix = TRUE;
}

function rs_wpss_admin_ao_fix() {
	/**
	 *  Fix compatibility with Autoptimize if active
	 *  @since 1.8.9.9, Modified in 1.9.7.3
	 *  The Autoptimize plugin forces the WP-SpamShield head JavaScript into the footer, which will cause problems. This fix automatically adds WP-SpamShield to the list of ignored scripts.
	 */
	global $wpss_ao_active,$wpss_ao_js,$wpss_ao_js_exc,$wpss_ao_js_frc_hd,$wpss_ao_fix;
	if( is_multisite() || !empty( $wpss_ao_fix ) ) { return; }
	if( empty( $wpss_ao_active ) ) { $wpss_ao_active = WPSS_Compatibility::is_plugin_active( 'autoptimize/autoptimize.php' ); }
	if( !empty( $wpss_ao_active ) ) {
		if( empty( $wpss_ao_js ) ) { $wpss_ao_js = get_option('autoptimize_js'); } /* See if "Optimize JavaScript Code" enabled */
		if( empty( $wpss_ao_js ) ) { $wpss_ao_fix = TRUE; return; }
		if( empty( $wpss_ao_js_frc_hd ) ) { $wpss_ao_js_frc_hd = get_option('autoptimize_js_forcehead'); } /* Setting for Force JavaScript in head */
		if( !empty( $wpss_ao_js_frc_hd ) ) { $wpss_ao_fix = TRUE; return; }
		if( empty( $wpss_ao_js_exc ) ) { $wpss_ao_js_exc = get_option('autoptimize_js_exclude'); } /* Setting for JavaScript exclusion */
		$ao_js_exc		= trim( $wpss_ao_js_exc, ", \t\n\r\0\x0B" );
		$exc_phrs		= array( '1899' => 'wp-spamshield', '192' => '.php,jquery', '1973' => 'wp-spamshield,jquery.js,jquery-migrate.min.js,', ); $exc_phr = $exc_phrs['1973'];
		$ao_js_exc_mod	= trim( implode( ',', array_unique( array_values( array_filter( explode( ',', str_replace( array( $exc_phrs['1899'].'1', $exc_phrs['192'], ), array( '', '', ), $ao_js_exc ) ) ) ) ) ), ", \t\n\r\0\x0B" );
		if( FALSE !== strpos( $ao_js_exc, $exc_phr ) && $wpss_ao_js_exc === $ao_js_exc_mod ) { $wpss_ao_fix = TRUE; return; } else { $ao_js_exc = $ao_js_exc_mod; }
		if( FALSE === strpos( $ao_js_exc, $exc_phr ) ) { $s = empty( $ao_js_exc ) ? '' : ','; $ao_js_exc .= $s.$exc_phr; }
		$ao_js_exc = implode( ',', array_unique( explode( ',', $ao_js_exc ) ) );
		if( $ao_js_exc !== $wpss_ao_js_exc ) {
			update_option( 'autoptimize_js_exclude', $ao_js_exc );
			rs_wpss_append_log_data( 'Autoptimize JavaScript exclusion setting appended.', FALSE );
		}
	}
	$wpss_ao_fix = TRUE;
}

function rs_wpss_admin_fscf_fix() {
	/**
	 *  Fix compatibility with Fast Secure Contact Form if active
	 *  The 'enable_submit_oneclick' option causes problems with jQuery, and the CAPTCHA option is just unnecessary. This fix automatically corrects these settings in all saved forms.
	 */
	global $spamshield_options,$wpss_fscf_active,$wpss_fscf_fix;
	if( empty( $spamshield_options ) ) { $spamshield_options = get_option('spamshield_options'); }
	if( is_multisite() || !empty( $spamshield_options['disable_misc_form_shield'] ) || !empty( $wpss_fscf_fix ) ) { return; }
	if( empty( $wpss_fscf_active ) ) { $wpss_fscf_active = WPSS_Compatibility::is_plugin_active( 'si-contact-form/si-contact-form.php' ); }
	if( !empty( $wpss_fscf_active ) ) {
		$wpss_fscf_cg = get_option( 'fs_contact_global' );
		if( !empty( $wpss_fscf_cg['form_list'] ) ) {
			foreach( $wpss_fscf_cg['form_list'] as $k => $form ) {
				$form_options = get_option( 'fs_contact_form'.$k );
				if( 'true' === $form_options['captcha_enable'] || 'true' === $form_options['enable_submit_oneclick'] ) {
					$form_options['captcha_enable'] = 'false'; $form_options['enable_submit_oneclick'] = 'false';
					update_option( 'fs_contact_form'.$k, $form_options ); $updated = TRUE;
				}
			}
		}
		if( !empty( $updated ) ) {
			rs_wpss_append_log_data( 'Fast Secure Contact Form settings updated.', FALSE );
		}
	}
	$wpss_fscf_fix = TRUE;
}

/* Admin Functions - END */


/* WP_SpamShield CLASS - BEGIN */

if(!class_exists('WP_SpamShield')) {
    class WP_SpamShield {

		function __construct() {
			if( TRUE !== WPSS_DEBUG && TRUE !== WP_DEBUG ) { @ini_set('display_errors',0); @error_reporting(0); } /* Prevents error display, but will display errors if WP_DEBUG turned on. */
			global $pagenow;
			/* Activation */
			register_activation_hook( __FILE__, array($this,'activation') );
			/* Actions */
			foreach( array( -100 => array('WPSS_Compatibility','supported'), 1 => 'rs_wpss_load_languages', 100 => array('WPSS_Compatibility','conflict_check') ) as $o => $a ) { add_action( 'plugins_loaded', $a, $o ); }
			add_action( 'load_textdomain', 'rs_wpss_check_loaded_languages', 10, 2 );
			foreach( array( -999 => array('WPSS_Security','check_request_method'), -990 => array('WPSS_Security','early_post_intercept'), 1 => 'rs_wpss_first_action', 2 => 'rs_wpss_misc_form_spam_check' ) as $o => $a ) { add_action( 'init', $a, $o ); }
			add_action( 'login_init', 'rs_wpss_login_init', -100 );
			if( !is_admin() || 'widgets.php' === $pagenow || 'customize.php' === $pagenow ) {
				add_action( 'widgets_init', 'rs_wpss_load_widgets' );
			}
			add_action( 'admin_menu', array($this,'create_admin_page') );
			foreach(
				array(
					-999	=> array( 'c' => 'WPSS_Security', 'f' => 'check_admin_sec', 'is' => NULL, 'not' => NULL ),
					-100	=> array( 'c' => $this, 'f' => 'hide_cpn_notices', 'is' => NULL, 'not' => NULL ),
					-10		=> array( 'c' => $this, 'f' => 'check_requirements', 'is' => NULL, 'not' => NULL ),
					10		=> array( 'c' => $this, 'f' => 'disable_plugin_edit', 'is' => 'plugin-editor.php', 'not' => NULL ),
				) as $o => $a ) {
					if( ( !empty( $a['is'] ) && $a['is'] !== $pagenow ) || ( !empty( $a['not'] ) && $a['not'] === $pagenow ) ){ continue; }
					add_action( 'admin_init', array( $a['c'], $a['f'] ), $o );
				}
			foreach( array( 'wp_enqueue_scripts', 'login_enqueue_scripts' ) as $a ) { add_action( $a, array($this,'enqueue_scripts'), 100 ); }
			foreach( array( 'wp_head', 'login_head' ) as $a ) { add_action( $a, array($this,'insert_head_js'), 9999 ); }
			add_action( 'comment_form', 'rs_wpss_comment_form_append', 10 );
			foreach( array( 'wp_footer', 'login_footer' ) as $a ) { add_action( $a, array($this,'insert_footer_js') ); }
			add_action( 'activity_box_end', array($this,'dashboard_counter') );
			if( 'edit.php' === $pagenow ) {
				add_action( 'admin_print_footer_scripts', array($this,'editor_add_quicktags') );
			}
			add_action( 'register_form', 'rs_wpss_register_form_append', 1 );
			add_action( 'user_register', 'rs_wpss_user_register', 1 );
			add_action( 'pre_user_email', 'rs_wpss_sanitize_new_user_email', 999 );
			add_action( 'wp_logout', 'rs_wpss_logout' );
			add_action( 'wp_login', 'rs_wpss_login', 10, 2 );
			/* Filters */
			add_filter( 'is_email', 'rs_wpss_adv_validate_email', 999, 3 );
			add_filter( 'auto_update_plugin', array('WPSS_Security','auto_update'), 100, 2 ); /* Added 1.9.7.8 */
			if( 'plugins.php' === $pagenow && is_multisite() ) { add_filter( 'all_plugins', array($this,'network_admin_remove_plugin') ); }
			if( 'plugins.php' === $pagenow ) {
				add_filter( 'plugin_action_links_'.WPSS_PLUGIN_BASENAME, array($this,'action_links') );
				add_filter( 'plugin_row_meta', array($this,'meta_links'), 200, 2 );
			}
			if( rs_wpss_is_xmlrpc() ) { add_filter( 'xmlrpc_methods', array('WPSS_Security','disable_xmlrpc_multicall'), 100 ); /* Added 1.9.7.8 */ }
			add_filter( 'status_header', 'rs_wpss_mod_status_header', 100, 4 );
			add_filter( 'the_content', 'rs_wpss_contact_form', 10 );
			foreach( array( 'the_content', 'the_excerpt', 'widget_text', 'comment_text', 'comment_excerpt' ) as $f ) { add_filter( $f, 'rs_wpss_encode_emails', 9999 ); }
			add_filter( 'pingback_ping_source_uri', 'rs_wpss_precheck_pingback_spam', -100, 2 ); /* Added 1.9.8.5 */
			add_filter( 'preprocess_comment', 'rs_wpss_check_comment_spam', -100 );
			add_filter( 'comment_notification_recipients', 'rs_wpss_comment_notification_check', 9999, 2 );
			add_filter( 'comment_moderation_recipients', 'rs_wpss_comment_moderation_check', 9999, 2 );
			add_filter( 'registration_errors', 'rs_wpss_check_new_user', 9999, 3 );
			if( isset( $_POST[WPSS_REF2XJS] ) ) { /* Don't add if registrations not processed by WPSS, or missing token */
				if( function_exists( 'rs_wpss_modify_signup_notification_admin' ) ) {
					add_filter( 'wpss_signup_notification_text_admin', 'rs_wpss_modify_signup_notification_admin', 1, 3 );
				}
				if( function_exists( 'rs_wpss_modify_signup_notification_user' ) ) {
					add_filter( 'wpss_signup_notification_text_user', 'rs_wpss_modify_signup_notification_user', 1, 3 );
				}
			}
			/* Actions & Filters - 3rd Party Plugins */
			if( defined( 'WC_VERSION' ) || defined( 'WOOCOMMERCE_VERSION' ) ) {
				add_action( 'woocommerce_before_customer_login_form', 'rs_wpss_wc_login_init', -100 );
				add_action( 'woocommerce_before_customer_login_form', 'rs_wpss_wc_before_register', 10 );
				add_action( 'woocommerce_register_form_start', 'rs_wpss_wc_registration_init', -100 );
				add_filter( 'woocommerce_registration_errors', 'rs_wpss_wc_check_new_user', -10 );
			}
			if( class_exists( 'BuddyPress' ) ) { add_filter( 'bp_signup_validate', 'rs_wpss_check_new_user', 999 ); }
			if( defined( 'WPCF7_VERSION' ) ) {
				add_filter( 'wpcf7_spam', 'rs_wpss_cf7_spam_check' );
				add_filter( 'wpcf7_mail_components', 'rs_wpss_cf7_contact_form_addendum' );
			}
			if( class_exists( 'GFForms' ) ) {
				add_filter( 'gform_get_form_filter', 'rs_wpss_gf_form_append', 10, 2 );
				add_filter( 'gform_pre_submission', 'rs_wpss_gf_spam_check' ); /* Modded 1.9.5 */
			}
			/* Shortcodes */
			add_shortcode( 'spamshieldcountersm', array('WPSS_Old_Counters','counter_sm_short') );
			add_shortcode( 'spamshieldcounter', array('WPSS_Old_Counters','counter_short') );
			add_shortcode( 'spamshieldcontact', 'rs_wpss_contact_shortcode' );
			/* Deactivation */
			register_deactivation_hook( __FILE__, array($this,'deactivation') );
        }

		/* Class Admin Functions - BEGIN */

		function activation() {
			global $spamshield_options,$wpss_init_spam_count;
			$spamshield_options = get_option('spamshield_options');
			rs_wpss_update_session_data($spamshield_options);
			self::deprecated_options_check( TRUE );
			$installed_ver = $spamshield_options['wpss_version'];
			$admin_email = get_option('admin_email');
			$this->upgrade_check( $installed_ver, TRUE );
			$this->update_admin_status();
			$this->approve_previous_users();

			/* Manually set spam count */
			if( !empty( $wpss_init_spam_count ) ) { update_option( 'spamshield_count', $wpss_init_spam_count ); }

			/* Only run installation if not installed already */
			if( empty( $installed_ver ) || empty( $spamshield_options ) ) {

				/**
				 *  Upgrade from old version
				 *  Import existing WP-SpamFree Options, only on first activation, if old plugin is active
				 */
				$old_version = 'wp-spamfree/wp-spamfree.php';
				$old_version_active = WPSS_Compatibility::is_plugin_active( $old_version, FALSE );
				$wpsf_installed_ver = get_option('wp_spamfree_version');
				if( !empty( $wpsf_installed_ver ) && empty( $installed_ver ) && !empty( $old_version_active ) ) {
					$spamfree_options = get_option('spamfree_options');
				}

				/* Set Initial Options */
				if( !empty( $spamshield_options ) ) {
					$wpss_options_default = unserialize( WPSS_OPTIONS_DEFAULT );
					foreach( $wpss_options_default as $d => $v ) { if( !isset( $spamshield_options[$d] ) ) { $spamshield_options[$d] = $v; } }
					if( !isset( $spamshield_options['install_date'] ) ) { $spamshield_options['install_date'] = date('Y-m-d'); }
					self::update_wpss_option( array( 'log_key' => rs_wpss_get_log_key( FALSE ), 'wpss_version' => WPSS_VERSION ), FALSE );
				} elseif( !empty( $spamfree_options ) ) {
					$spamshield_options = $spamfree_options;
					$wpss_options_default = unserialize( WPSS_OPTIONS_DEFAULT );
					foreach( $wpss_options_default as $d => $v ) { if( !isset( $spamshield_options[$d] ) ) { $spamshield_options[$d] = $v; } }
					self::update_wpss_option( array( 'install_date' => date('Y-m-d'), 'log_key' => rs_wpss_get_log_key( FALSE ), 'wpss_version' => WPSS_VERSION ), FALSE );
					$notice_text = __( 'You have successfully upgraded from WP-SpamFree to WP-SpamShield. You will now experience improved security, page load speed, and spam-blocking power. All your old settings have been imported, and your contact forms will continue to work without you having to do anything else. You can safely remove the outdated WP-SpamFree plugin.', 'wp-spamshield' );
					$new_admin_notice = array( 'style' => 'updated', 'notice' => $notice_text );
					update_option( 'spamshield_admin_notices', $new_admin_notice );
				} else { /* $spamshield_options must be empty for the defaults to get set...otherwise it will update_option() with the current value */
					/* DEFAULTS */
					$spamshield_options = unserialize( WPSS_OPTIONS_DEFAULT );
					self::update_wpss_option( array( 'form_message_recipient' => $admin_email, 'install_date' => date('Y-m-d'), 'log_key' => rs_wpss_get_log_key( FALSE ), 'wpss_version' => WPSS_VERSION ), FALSE );
				}

				if( empty( $spamshield_options['form_message_recipient'] ) || !is_email( $spamshield_options['form_message_recipient'] ) ) {
					$spamshield_options['form_message_recipient'] = $admin_email;
				}
				$spam_count = rs_wpss_count();
				if( empty( $spam_count ) ) { update_option( 'spamshield_count', 0 ); }
				self::update_wpss_option( $spamshield_options );
				/**
				 *  NEXT LINE - DEBUGGING ONLY
				 *  rs_wpss_procdat('reset');
				 *  Reset Log and Initialize .htaccess
				 */
				$admin_ips = get_option( 'spamshield_admins' );
				if( !empty( $admin_ips ) ) { rs_wpss_log_reset( $admin_ips ); } else { rs_wpss_log_reset( NULL, FALSE, FALSE, TRUE ); /* Create log file if it doesn't exist */ }
				/* Require Author Names and Emails on Comments - Added 1.1.7 */
				update_option('require_name_email', '1');
				/**
				 *  Set 'default_role' to 'subscriber' for security - Added 1.3.7 - Disabled 1.9.0.6
				 *  update_option('default_role', 'subscriber');
				 *  Turn on Comment Moderation
				 *  update_option('comment_moderation', 1);
				 *  update_option('moderation_notify', 1);
				 *  Compatibility Checks
				 */
				if( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) ) { rs_wpss_admin_jp_fix(); }
				rs_wpss_admin_ao_fix();
				rs_wpss_admin_fscf_fix();

				/* Ensure Correct Permissions of JS file */
				$inst_file_test_3 = WPSS_PLUGIN_JS_PATH.'/jscripts.php';
				clearstatcache();
				$inst_file_test_3_perm = substr( sprintf( '%o', fileperms( $inst_file_test_3 ) ), -4 );
				if( $inst_file_test_3_perm < '0644' || !is_readable( $inst_file_test_3 ) ) {
					@chmod( $inst_file_test_3, 0664 );
				}
				/* Set Secure Permissions for Includes Folders */
				@chmod( WPSS_PLUGIN_INCL_PATH, 0750 );
				@chmod( WPSS_PLUGIN_PATH.'/lib', 0750 );
			}

			/* Check Installation Status */
			$this->check_install_status( TRUE );
			if( TRUE === WPSS_IP_BAN_CLEAR ) { WPSS_Security::clear_ip_ban(); }
		}

		function deactivation() {
			rs_wpss_log_reset();
			$upd_options = array( 'spamshield_ubl_cache' => array(), 'spamshield_wpssmid_cache' => array() );
			foreach( $upd_options as $option => $val ) { update_option( $option, $val ); }
			$del_options = array( 'spamshield_admin_notices', 'spamshield_install_status', 'spamshield_warning_status', 'spamshield_regalert_status', );
			foreach( $del_options as $i => $option ) { delete_option( $option ); }
			self::delete_wpss_option( array( 'host_ns_bl', ) );
			rs_wpss_purge_nonces();
			if( TRUE === WPSS_IP_BAN_CLEAR ) { WPSS_Security::clear_ip_ban(); }
		}

		function create_admin_page() {
			if( rs_wpss_is_admin_sproc( TRUE ) ) { return; }
			add_options_page( 'WP-SpamShield ' . __( 'Settings' ), 'WP-SpamShield', 'manage_options', WPSS_PLUGIN_NAME, array($this,'plugin_admin_page') );
		}

		function network_admin_remove_plugin( $all ) {
			/**
			 *  Remove plugin from Network Admin Plugins list to prevent Network Activation
			 *  @since 1.9.7.5
			 */
			global $current_screen;
			if( $current_screen->is_network ) { unset( $all[WPSS_PLUGIN_BASENAME] ); }
			return $all;
		}

		function action_links( $actions ) {
			/**
			 *  Add "Settings" Link on Dashboard Plugins page, in plugin listings
			 *  Before other links
			 */
			if( rs_wpss_is_admin_sproc( TRUE ) ) { return $actions; }
			unset( $actions['edit'] ); /* Remove Edit link - Added 1.9.7.5 */
			$new_actions = array(
				'<a href="options-general.php?page='.WPSS_PLUGIN_NAME.'">' . __( 'Settings' ) . '</a>',
			);
			$actions = array_merge( $new_actions, $actions );
			return $actions;
		}

		function meta_links( $links, $file ) {
			/**
			 *  Add Links on Dashboard Plugins page, in plugin meta
			 *  After other links
			 */
			if( rs_wpss_is_admin_sproc( TRUE ) ) { return $links; }
			if( $file === WPSS_PLUGIN_BASENAME ) {
				$links[] = '<a href="'. rs_wpss_append_url( WPSS_HOME_URL ) .'" target="_blank" rel="external" >' . rs_wpss_doc_txt() . '</a>';
				$links[] = '<a href="'. rs_wpss_append_url( WPSS_SUPPORT_URL ) .'" target="_blank" rel="external" >' . __( 'Support', 'wp-spamshield' ) . '</a>';
				if( rs_wpss_count() >= 2000 ) {
					$links[] = '<a href="'.WPSS_WP_RATING_URL.'" title="' . __( 'Let others know by giving it a good rating on WordPress.org!', 'wp-spamshield' ) . '" target="_blank" rel="external" >' . __( 'Rate the Plugin', 'wp-spamshield' ) . '</a>';
				}
				$links[] = '<a href="'.WPSS_DONATE_URL.'" target="_blank" rel="external" >' . __( 'Donate', 'wp-spamshield' ) . '</a>';
			}
			return $links;
		}

		function settings_css() {
			$css_handle = 'wpss-admin';
			wp_register_style( $css_handle, WPSS_PLUGIN_CSS_URL.'/'.$css_handle.'.css', NULL, WPSS_VERSION );
			wp_enqueue_style( $css_handle );
		}

		function approve_previous_users() {
			/**
			 *  Check # of users and DB size before running
			 *  TO DO: When implementing, ADD: error message, and then schedule cron jobs to handle in small chunks over 24 hours
			 */
			$user_ids = get_users( array( 'blog_id' => '', 'fields' => 'ID' ) );
			if( count( $user_ids ) >= 1000 ) { return FALSE; }
			$db_size = self::get_db_size();
			if( $db_size >= ( 100 * 1024 * 1024 ) ) { return FALSE; }
			foreach( $user_ids as $user_id ) {
				update_user_meta( $user_id, 'wpss_new_user_approved', TRUE );
				update_user_meta( $user_id, 'wpss_new_user_email_sent', TRUE );
			}
			self::update_wpss_option( array( 'init_user_approve_run' => TRUE ) );
		}


		function dashboard_counter() {
			if( rs_wpss_is_admin_sproc( TRUE ) ) { return; }
			$spam_count_raw = rs_wpss_count();
			global $spamshield_options; if( empty( $spamshield_options ) ) { $spamshield_options = get_option('spamshield_options'); }
			rs_wpss_update_session_data($spamshield_options);
			$current_date = date('Y-m-d');
			if( empty( $spamshield_options['install_date'] ) ) {
				$install_date = $current_date;
				$spamshield_options['install_date'] = $install_date;
				self::update_wpss_option( $spamshield_options );
			} else { $install_date = $spamshield_options['install_date']; }
			$num_days_inst	= rs_wpss_date_diff($install_date, $current_date); if( $num_days_inst < 1 ) { $num_days_inst = 1; }
			$spam_count		= $spam_count_raw; if( $spam_count < 1 ) { $spam_count = 1; }
			$avg_blk_dly	= round( $spam_count / $num_days_inst );
			$avg_blk_dly_d	= rs_wpss_number_format( $avg_blk_dly );
			if( rs_wpss_is_user_admin() ) {
				$spam_stat_incl_link = ' (<a href="options-general.php?page='.WPSS_PLUGIN_NAME.'"">' . __( 'Settings' ) . '</a>)</p>'.WPSS_EOL;
				$spam_stat_url = 'options-general.php?page='.WPSS_PLUGIN_NAME;
				$spam_stat_href_attr = '';
			} else {
				$spam_stat_incl_link = '';
				$spam_stat_url = WPSS_HOME_URL;
				$spam_stat_href_attr = 'target="_blank" rel="external"';
			}

			if( empty( $spam_count_raw ) ) {
				echo '<p>' . __( 'No comment spam attempts have been detected yet.', 'wp-spamshield' ) . $spam_stat_incl_link;
			} else {
				echo '<p>'."<img src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAABMElEQVR4Ae3WJVhEURAF4Fdwd5eGZ7Q3pKB9GwXpCQpuBXd3bfSGu7u7+3DC4PAEt/t9/8qVOeuzgqIRW9IBTcJ7DxS1hgkg1g1W71FYA3KAYBUCYAqIxYGWkoJeEALxMArESnldHY6A7jmHRoiEIPAVC9gDYqeQAkb31k1uAkTsiQUQFIqsv0tA4kcHJP0HfPl7kMW3De7jOUdYuyn0yA7PH4gFHADBLuzfx/MLYM579e7juXQgsYAtIBFboC1yPk0qoPeNAZ1wIRaQ+cYAggbxH7tXBmA+Ggj8pH5RS5QGYM6Z11vk9oAx2QH4VPHagezegI12sC4WwPs8eP4KbJV2M1MYfSZAg9dVPLcGZm9pm1VAbBr0oQYI2t6r6UcAsSu+Vr33PwsjGIBpMJZ77BqB/vSrID7csgAAAABJRU5ErkJggg==' alt='' width='24' height='24' style='border-style:none;vertical-align:middle;padding-right:7px;' />".' <a href="'.$spam_stat_url.'" '.$spam_stat_href_attr.'>WP-SpamShield</a> '.sprintf( __( 'has blocked <strong> %s </strong> spam.', 'wp-spamshield' ), rs_wpss_number_format( $spam_count_raw ) ).'</p>'.WPSS_EOL;
				if( $avg_blk_dly >= 2 ) {
					echo "<p><img src='data:image/png;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAUUAAAALAAAAAABAAEAAAICRAEAOw==' alt='' width='24' height='24' style='border-style:none;vertical-align:middle;padding-right:7px;' /> " . __( 'Average spam blocked daily', 'wp-spamshield' ) . ": <strong>".$avg_blk_dly_d."</strong></p>".WPSS_EOL;
				}
			}
		}

		function plugin_admin_page() {
			if( !is_admin() || rs_wpss_is_admin_sproc( TRUE ) ) { return; }
			require_once( WPSS_PLUGIN_INCL_PATH.'/admin-settings.php' );
		}

		function settings_ver_ftr() {
			global $compat_mode;
			echo 'Version '.WPSS_VERSION;
			if( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) || !empty( $compat_mode ) ) { echo "<br />".WPSS_EOL. rs_wpss_casetrans( 'upper', __( 'Compatibility Mode', 'wp-spamshield' ) ); /* TRANSLATE */ }
			if( strpos( WPSS_SERVER_NAME_REV, WPSS_DEBUG_SERVER_NAME_REV ) === 0 ) {
				$wpss_proc_data = get_option( 'spamshield_procdat' );
				if( !isset( $wpss_proc_data['avg2_wpss_proc_time'] ) && isset( $wpss_proc_data['avg_wpss_proc_time'] ) ) {
					$wpss_proc_data['avg2_wpss_proc_time'] = $wpss_proc_data['avg_wpss_proc_time'];
				} elseif( !isset( $wpss_proc_data['avg2_wpss_proc_time'] ) ) { $wpss_proc_data['avg2_wpss_proc_time'] = 0; }
				$wpss_proc_data_avg2_wpss_proc_time_disp = rs_wpss_number_format( $wpss_proc_data['avg2_wpss_proc_time'], 6 );
				echo "<br />".WPSS_EOL.'Avg WPSS Proc Time: '.$wpss_proc_data_avg2_wpss_proc_time_disp.' seconds';
			}
		}

		function admin_notices() {
			$admin_notices = get_option('spamshield_admin_notices');
			if( !empty( $admin_notices ) ) {
				$style 	= $admin_notices['style']; /* 'error', 'updated', 'is-dismissible', 'updated notice is-dismissible' */
				$notice	= $admin_notices['notice'];
				echo '<div class="'.$style.'"><p>'.$notice.'</p></div>';
			}
			delete_option( 'spamshield_admin_notices' );
		}

		function admin_cpn_notices() {
			/* Admin Custom Plugin Notices */
			global $current_user; $current_user = wp_get_current_user();
			$notices = get_user_meta( $current_user->ID, 'wpss_nag_notices', TRUE );
			if( !empty( $notices ) ) {
				$nid			= $notices['nid'];
				$style			= $notices['style']; /* 'error', 'updated', 'is-dismissible', 'updated notice is-dismissible' */
				$spam_count		= rs_wpss_count();
				$timenow		= time();
				$avg_blk_dly_d	= rs_wpss_number_format( $this->spam_blocked_daily() );
				$est_hrs_ret_d	= rs_wpss_number_format( $this->est_hrs_returned( $spam_count ) );
				$url			= rs_wpss_get_url( TRUE );
				$query_args		= rs_wpss_get_query_args( $url );
				$query_str		= '?' . http_build_query( array_merge( $query_args, array( 'wpss_hide_cpn' => '1', 'nid' => $nid ) ) );
				$query_str_con	= 'QUERYSTRING';
				$spamcount_con	= 'SPAMCOUNT';
				$spamdaily_con	= 'SPAMDAILY';
				$spamhours_con	= 'SPAMHOURS';
				$notice			= str_replace( array( $query_str_con, $spamcount_con, $spamdaily_con, $spamhours_con ), array( $query_str, rs_wpss_number_format( $spam_count ), $avg_blk_dly_d, $est_hrs_ret_d ), $notices['notice'] );
				echo '<div class="'.$style.'"><p>'.$notice.'</p></div>';
			}
		}

		function check_cpn_notices() {
			/* Check Custom Plugin Notices */
			global $current_user; $current_user = wp_get_current_user();
			$status			= get_user_meta( $current_user->ID, 'wpss_nag_status', TRUE );
			if( !empty( $status['currentnag'] ) ) { add_action( 'admin_notices', array($this,'admin_cpn_notices') ); return; }
			if( !is_array( $status ) ) { $status = array(); update_user_meta( $current_user->ID, 'wpss_nag_status', $status ); }
			$timenow		= time();
			$spam_count		= rs_wpss_count();
			$avg_blk_dly	= $this->spam_blocked_daily();
			$est_hrs_ret	= $this->est_hrs_returned( $spam_count );
			$query_str_con	= 'QUERYSTRING';
			$spamcount_con	= 'SPAMCOUNT';
			$spamdaily_con	= 'SPAMDAILY';
			$spamhours_con	= 'SPAMHOURS';
			/* Reminders */
			if( empty( $status['currentnag'] ) && ( empty( $status['lastnag'] ) || $status['lastnag'] <= $timenow - 1209600 ) ) {
				if( empty( $status['vote'] ) && $spam_count >= 2500 ) { /* TO DO: TRANSLATE */
					$nid = 'n01'; $style = 'updated';
					$avg_d_txt = '';
					if( $avg_blk_dly >= 2 ) {
						$avg_d_txt = ' '. sprintf( __( 'That\'s <strong> %s </strong> spam a day that you don\'t have to worry about.', 'wp-spamshield' ), $spamdaily_con );
					}
					$notice_text = sprintf( __( 'WP-SpamShield has blocked <strong> %s </strong> spam!', 'wp-spamshield' ), $spamcount_con ) .$avg_d_txt.'</p><p>'. __( 'If you like how WP-SpamShield protects your site from spam, would you take a moment to give it a rating on WordPress.org?', 'wp-spamshield' ) .'</p><p>'. sprintf( '<strong><a href=%1$s>%2$s</a></strong>', '"'.WPSS_WP_RATING_URL.'" target="_blank" rel="external" ', __( 'Yes, I\'d like to rate it!', 'wp-spamshield' ) ) .' &mdash; '.  sprintf( '<strong><a href=%1$s>%2$s</a></strong>', '"'.$query_str_con.'" ', __( 'I already did!', 'wp-spamshield' ) );
					$status['currentnag'] = TRUE; $status['vote'] = FALSE;
				} elseif( empty( $status['donate'] ) && $est_hrs_ret >= 50 ) { /* TO DO: TRANSLATE */
					$nid = 'n02'; $style = 'updated';
					$notice_text = __( 'Happy with WP-SpamShield?', 'wp-spamshield' ) .' '. sprintf( __( 'This plugin has saved you <strong> %s </strong> hours of managing spam. You\'re welcome!', 'wp-spamshield' ), $spamhours_con ) .'</p><p>'. __( 'WP-SpamShield is provided for free.', 'wp-spamshield' ) . ' ' . __( 'If you like the plugin, consider a donation to help further its development.', 'wp-spamshield' ) .'</p><p>'. sprintf( '<strong><a href=%1$s>%2$s</a></strong>', '"'.WPSS_DONATE_URL.'" target="_blank" rel="external" ', __( 'Yes, I\'d like to donate!', 'wp-spamshield' ) ) .' &mdash; '. sprintf( '<strong><a href=%1$s>%2$s</a></strong>', '"'.$query_str_con.'" ', __( 'I already did!', 'wp-spamshield' ) );
					$status['currentnag'] = TRUE; $status['donate'] = FALSE;
				}
			}
			/* Warnings */
			/* TO DO: Add Warnings - about plugin conflicts, configuration issues, and missing PHP functions */
			if( !empty( $status['currentnag'] ) ) {
				add_action( 'admin_notices', array($this,'admin_cpn_notices') );
				$new_cpn_notice = array( 'nid' => $nid, 'style' => $style, 'notice' => $notice_text );
				update_user_meta( $current_user->ID, 'wpss_nag_notices', $new_cpn_notice );
				update_user_meta( $current_user->ID, 'wpss_nag_status', $status );
			}
		}

		function hide_cpn_notices() {
			/* Hide Custom Plugin Notices */
			if( rs_wpss_is_admin_sproc( TRUE ) || !rs_wpss_is_user_admin() || rs_wpss_is_doing_ajax() ) { return; }
			$cpns_codes		= array( 'n01' => 'vote', 'n02' => 'donate', ); /* CPN Status Codes */
			if( !isset( $_GET['wpss_hide_cpn'], $_GET['nid'], $cpns_codes[$_GET['nid']] ) || $_GET['wpss_hide_cpn'] != '1' ) { return; }
			global $current_user; $current_user = wp_get_current_user();
			$status			= get_user_meta( $current_user->ID, 'wpss_nag_status', TRUE );
			$timenow		= time();
			$url			= rs_wpss_get_url( TRUE );
			$query_args		= rs_wpss_get_query_args( $url ); unset( $query_args['wpss_hide_cpn'],$query_args['nid'] );
			$query_str		= http_build_query( $query_args ); if( $query_str != '' ) { $query_str = '?'.$query_str; }
			$redirect_url	= rs_wpss_fix_url( $url, TRUE, TRUE ) . $query_str;
			$status['currentnag'] = FALSE; $status['lastnag'] = $timenow; $status[$cpns_codes[$_GET['nid']]] = TRUE;
			update_user_meta( $current_user->ID, 'wpss_nag_status', $status );
			update_user_meta( $current_user->ID, 'wpss_nag_notices', array() );
			wp_redirect( $redirect_url );
			exit;
		}

		function check_requirements() {
			global $wpss_requirements_checked; if( !empty( $wpss_requirements_checked ) ) { return; }
			if( isset( $_SESSION['wpss_requirements_checked_'.WPSS_HASH] ) && 1 * HOUR_IN_SECONDS > ( time() - $_SESSION['wpss_requirements_checked_'.WPSS_HASH] ) ) { $wpss_requirements_checked = TRUE; return; }
			if( rs_wpss_is_admin_sproc( TRUE ) || rs_wpss_is_doing_ajax() ) { return; }
			if( current_user_can( 'manage_network' ) ) {
				/* Check for pending admin notices */
				$admin_notices = get_option('spamshield_admin_notices');
				if( !empty( $admin_notices ) ) { add_action( 'network_admin_notices', array($this,'admin_notices') ); }
				/* Make sure not network activated */
				if( is_plugin_active_for_network( WPSS_PLUGIN_BASENAME ) ) {
					deactivate_plugins( WPSS_PLUGIN_BASENAME, TRUE, TRUE );
					$notice_text = __( 'Plugin deactivated. WP-SpamShield is not available for network activation.', 'wp-spamshield' ); /* TO DO: Fix translation. */
					$new_admin_notice = array( 'style' => 'error notice is-dismissible', 'notice' => $notice_text );
					update_option( 'spamshield_admin_notices', $new_admin_notice );
					add_action( 'network_admin_notices', array($this,'admin_notices') );
					rs_wpss_append_log_data( $notice_text, FALSE );
					return FALSE;
				}
			}
			if( rs_wpss_is_user_admin() ) {
				/* Check for deprecated options */
				self::deprecated_options_check();
				/* Check if plugin has been upgraded */
				$this->upgrade_check();
				/* Check for outdated WordPress versions with known security flaws */
				$this->insecure_wordpress_check();
				/* Check for pending admin notices */
				$admin_notices = get_option('spamshield_admin_notices');
				if( !empty( $admin_notices ) ) { add_action( 'admin_notices', array($this,'admin_notices') ); }
				/* Make sure user is not running site in incompatible web hosting environments */
				if( rs_wpss_host_ns_blacklist_chk() ) {
					deactivate_plugins( WPSS_PLUGIN_BASENAME );
					$notice_text = '<p>' . __('Plugin <strong>deactivated</strong>.') . ' ' . sprintf( __( 'Your web hosting environment does not meet the compatibility requirements. <a href=%1$s>More Information</a></p>', 'wp-spamshield' ), '"'. rs_wpss_append_url( WPSS_RSM_URL.'recommended-web-hosts/?err=not_compatible' ) .'" target="_blank" rel="external" ' ); /* TO DO: TRANSLATE - Added 1.9.9.4 */
					$new_admin_notice = array( 'style' => 'error notice is-dismissible', 'notice' => $notice_text );
					update_option( 'spamshield_admin_notices', $new_admin_notice );
					add_action( 'admin_notices', array($this,'admin_notices') );
					rs_wpss_append_log_data( $notice_text, FALSE );
					return FALSE;
				}
				/* Make sure user has minimum required WordPress version, in order to prevent issues */
				$wpss_wp_version = WPSS_WP_VERSION; /* Convert constant to variable so it can be checked with empty() in PHP < 5.5 */
				if( !empty( $wpss_wp_version ) && version_compare( $wpss_wp_version, WPSS_REQUIRED_WP_VERSION, '<' ) ) { /* Be sure to check with empty() first */
					deactivate_plugins( WPSS_PLUGIN_BASENAME );
					$notice_text = sprintf( __( 'Plugin deactivated. WordPress Version %s required. Please upgrade WordPress to the latest version.', 'wp-spamshield' ), WPSS_REQUIRED_WP_VERSION ); /* TO DO: Fix translation. */
					$new_admin_notice = array( 'style' => 'error notice is-dismissible', 'notice' => $notice_text );
					update_option( 'spamshield_admin_notices', $new_admin_notice );
					add_action( 'admin_notices', array($this,'admin_notices') );
					rs_wpss_append_log_data( $notice_text, FALSE );
					return FALSE;
				}
				/* Make sure user has minimum required PHP version, in order to prevent issues */
				$wpss_php_version = WPSS_PHP_VERSION; /* Convert constant to variable so it can be checked with empty() in PHP < 5.5 */
				if( !empty( $wpss_php_version ) && version_compare( $wpss_php_version, WPSS_REQUIRED_PHP_VERSION, '<' ) ) { /* Be sure to check with empty() first */
					deactivate_plugins( WPSS_PLUGIN_BASENAME );
					$notice_text = '<p>' . __('Plugin <strong>deactivated</strong>.') . ' ' . str_replace( 'WordPress', 'WP-SpamShield', sprintf( __('Your server is running PHP version %1$s but WordPress %2$s requires at least %3$s.'), WPSS_PHP_VERSION, WPSS_VERSION, WPSS_REQUIRED_PHP_VERSION ) ) . ' ' . sprintf( __( 'We are no longer supporting PHP 5.2, as it reached its End of Life (no longer supported by the PHP team) <a href=%2$s>in 2011</a>, and there are known security, performance, and compatibility issues. We are phasing out support for PHP 5.3 as it reached End of Life in 2014.</p><p>The version of PHP running on your server is <em>extremely out of date</em>. You should upgrade your PHP version as soon as possible.</p><p>If you need help with this, please contact your web hosting company and ask them to switch your PHP version to 5.5, 5.6, or higher. Please see the <a href=%4$s>plugin documentation</a> and <a href=%5$s>changelog</a> if you have further questions.', 'wp-spamshield' ), WPSS_REQUIRED_PHP_VERSION, '"http://php.net/archive/2011.php#id2011-08-23-1" target="_blank" rel="external" ', $wpss_php_version, '"'. rs_wpss_append_url( WPSS_HOME_URL.'?wpss=requirements&src='.WPSS_VERSION.'-php-notice#wpss_requirements' ) .'" target="_blank" rel="external" ', '"'. rs_wpss_append_url( WPSS_HOME_URL.'changelog/?ver=182&src='.WPSS_VERSION.'-php-notice#ver_182' ) .'" target="_blank" rel="external" ' ) . '</p>'; /* TO DO: NEEDS TRANSLATION - Added 1.8.2, Updated 1.9.7.4 */
					$new_admin_notice = array( 'style' => 'error notice is-dismissible', 'notice' => $notice_text );
					update_option( 'spamshield_admin_notices', $new_admin_notice );
					add_action( 'admin_notices', array($this,'admin_notices') );
					rs_wpss_append_log_data( $notice_text, FALSE );
					return FALSE;
				}
				$wpss_wp_config_error = $this->check_wp_config_status(); /* TO DO: Make version for Network Admins when we add full Multisite compatibility (Network Activation) */
				if( !empty( $wpss_wp_config_error ) ) {
					deactivate_plugins( WPSS_PLUGIN_BASENAME );
					$correct_error = sprintf( __( 'Please check your <a href="%1$s">settings</a> and correct the error.', 'wp-spamshield' ), WPSS_ADMIN_URL.'/options-general.php' );
					$notice_text = sprintf( '<p>%1$s <strong>%2$s %3$s</strong> %4$s</p>', __('Plugin <strong>deactivated</strong>.'), __( 'There is an error in your WordPress configuration.', 'wp-spamshield' ), $wpss_wp_config_error, $correct_error ); /* TO DO: NEEDS TRANSLATION - Added 1.9.7.1 */
					$new_admin_notice = array( 'style' => 'error', 'notice' => $notice_text );
					update_option( 'spamshield_admin_notices', $new_admin_notice );
					add_action( 'admin_notices', array($this,'admin_notices') );
					rs_wpss_append_log_data( $notice_text, FALSE );
					return FALSE;
				}
				/**
				 *  TO DO:
				 *  - Add check for Nginx
				 *  	- Check for htscanner extension - http://php.net/manual/en/book.htscanner.php
				 *  	- If Nginx & no htscanner, and then disable plugin
				 *  - Add check for .htaccess capabilities: AllowOverride All 
				 */
				$this->check_cpn_notices();
				/* Security Check - See if(extremely) old version of plugin still active */
				$old_version = 'wp-spamfree/wp-spamfree.php';
				$old_version_active = WPSS_Compatibility::is_plugin_active( $old_version, FALSE );
				if( !empty( $old_version_active ) ) {
					/**
					 *  Not safe to keep old version active due to unpatched security hole(s), broken PHP, and lack of maintenance.
					 *  For security reasons, deactivate old version.
					 */
					deactivate_plugins( $old_version );
					/* Clean up database */
					$del_options = array( 'wp_spamfree_version', 'spamfree_count', 'spamfree_options' );
					foreach( $del_options as $i => $option ) { delete_option( $option ); }
					/**
					 *  Good to go!
					 *  Since WP-SpamShield takes over 100% of old version's responsibilities, there is no loss of functionality, only improvements.
					 *  Site speed will improve and server load will now drop dramatically.
					 */
				}
				/* Compatibility Checks */
				if( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) ) { rs_wpss_admin_jp_fix(); }
				rs_wpss_admin_ao_fix();
				rs_wpss_admin_fscf_fix();
			}
			$_SESSION['wpss_requirements_checked_'.WPSS_HASH] = time();
			$wpss_requirements_checked = TRUE;
		}

		function upgrade_check( $installed_ver = NULL, $activation = FALSE ) {
			if( empty( $installed_ver ) ) { $installed_ver = self::get_wpss_option( 'wpss_version' ); }
			if( $installed_ver !== WPSS_VERSION ) {
				$upd_options = array( 'spamshield_ubl_cache' => array(), 'spamshield_wpssmid_cache' => array() );
				foreach( $upd_options as $option => $val ) { update_option( $option, $val ); }
				$upd_wpss_options = array();
				if( FALSE === $activation ) { $upd_wpss_options['wpss_version'] = WPSS_VERSION; }
				if( !empty( $upd_wpss_options ) ) { self::update_wpss_option( $upd_wpss_options ); }
				$del_options = array( 'spamshield_install_status', 'spamshield_warning_status', 'spamshield_regalert_status' );
				foreach( $del_options as $i => $option ) { delete_option( $option ); }
				rs_wpss_purge_nonces();
				$this->update_admin_status();
				WPSS_Compatibility::upgrade_conflict_check();
				/* $this->clear_cache_plugins(); */
			}
		}

		/**
		 *  TO DO: PRE-CHECKS
		 *  TO DO: function integrity_check() {}
		 */

		function check_install_status( $activation = FALSE ) {
			/**
			 *  Check Installation Status and Do Compatibility Pre-Checks
			 *  @since 1.9.1
			 */
			/* Check for incompatible web hosting environments */
			if( rs_wpss_host_ns_blacklist_chk() ) { update_option( 'spamshield_install_status', 'incorrect' ); return FALSE; }
			/* Install Status */
			$wpss_inst_status_option = get_option( 'spamshield_install_status' );
			if( !empty( $wpss_inst_status_option ) && $wpss_inst_status_option === 'correct' ) { return TRUE; }
			elseif( !empty( $wpss_inst_status_option ) && $wpss_inst_status_option === 'incorrect' ) { return FALSE; }
			else {
				global $is_apache, $is_IIS, $is_iis7, $is_nginx;
				$inst_plugins_get_test_1	= WPSS_PLUGIN_NAME;			/* 'wp-spamshield' - Checking for 'options-general.php?page='.WPSS_PLUGIN_NAME */
				$inst_file_test_0 			= WPSS_PLUGIN_FILE_PATH;	/* '/public_html/wp-content/plugins/wp-spamshield/wp-spamshield.php' */
				$inst_file_test_3 			= WPSS_PLUGIN_JS_PATH.'/jscripts.php';
				clearstatcache();
				$inst_file_test_3_perm		= substr( sprintf( '%o', fileperms( $inst_file_test_3 ) ), -4);
				if( $inst_file_test_3_perm < '0644' || !is_readable( $inst_file_test_3 ) ) { @chmod( $inst_file_test_3, 0664 ); }
				clearstatcache();
				$get_page = !empty( $_GET['page'] ) ? $_GET['page'] : '';
				$jscripts_status = rs_wpss_jscripts_403_fix();
				global $compat_mode; extract( $jscripts_status );
				$jscripts_blocked = ( FALSE === $blocked || TRUE === $compat_mode ) ? FALSE : TRUE;
				$js_url = WPSS_PLUGIN_JS_URL.'/jscripts-ftr-min.js';
				$hdr_data = rs_wpss_get_http_status( $js_url ); extract( $hdr_data );
				$js_status = $status;
				if( ( $inst_plugins_get_test_1 === $get_page || TRUE === $activation ) && file_exists( $inst_file_test_0 ) && file_exists( $inst_file_test_3 ) && FALSE === $jscripts_blocked && $js_status != '500' && FALSE === $is_nginx ) {
					update_option( 'spamshield_install_status', 'correct' ); return TRUE;
				} else {
					update_option( 'spamshield_install_status', 'incorrect' ); return FALSE;
				}
			}
		}

		function insecure_wordpress_check() {
			/**
			 *  SECURITY - Check for WordPress versions with known security vulnerabilies.
			 *  @since 1.9.7.8
			 */
			$current = get_site_transient( 'wpss_iswpv_check' );
			/* Invalidate the transient when WPSS_WP_VERSION changes */
			if ( is_object( $current ) && WPSS_WP_VERSION != $current->version_checked ) { $current = FALSE; }
			if ( !is_object($current) ) {
				$current = new stdClass;
				$current->updates = array();
				$current->version_checked = WPSS_WP_VERSION;
			}
			if ( isset( $current->last_checked, $current->version_checked ) && 1 * WEEK_IN_SECONDS > ( time() - $current->last_checked ) && $current->version_checked == WPSS_WP_VERSION ) { return; }
			$current->last_checked = time();
			set_site_transient( 'wpss_iswpv_check', $current );
			$wpv = str_replace( '.', '', WPSS_WP_VERSION );
			$url = 'https://wpvulndb.com/api/v2/wordpresses/'.$wpv;
			$inf = 'https://wpvulndb.com/wordpresses/'.$wpv;
			$http_args = array(
				'timeout'		=> 10,
				'decompress'	=> FALSE,
				'httpversion'	=> '1.1',
				);
			$resp	= wp_remote_get( $url, $http_args );
			$data	= json_decode( wp_remote_retrieve_body( $resp ), TRUE );
			$vulns	= !empty( $data[WPSS_WP_VERSION]['vulnerabilities'] ) ? $data[WPSS_WP_VERSION]['vulnerabilities'] : '';
			$vnum	= is_array( $vulns ) ? count( $vulns ) : 0;
			if( empty( $vnum ) ) { return FALSE; }
			$notice_text = '<p><strong>' . __( 'SECURITY ALERT', 'wp-spamshield' ) . ':</strong> ' . sprintf( __('Insecure WordPress version detected. Your site is running WordPress version %1$s, which has %2$s known security vulnerabilities. You should upgrade WordPress as soon as possible. <a href=%3$s>More Information</a></p>'), WPSS_WP_VERSION, $vnum, '"'.$inf.'" target="_blank" rel="external" ' ); /* TO DO: TRANSLATE */
			$new_admin_notice = array( 'style' => 'error notice is-dismissible', 'notice' => $notice_text );
			update_option( 'spamshield_admin_notices', $new_admin_notice );
			add_action( 'admin_notices', array($this,'admin_notices') );
			return TRUE;
		}

		function disable_plugin_edit() {
			/**
			 *  Disable editing of the plugin through the Admin Plugin Editor
			 *  @since 1.9.7.5
			 */
			global $pagenow;
			if( $pagenow === 'plugin-editor.php' && ( ( !empty( $_GET['file'] ) && 0 === strpos( $_GET['file'], WPSS_PLUGIN_NAME ) ) || ( !empty( $_POST['plugin'] ) && 0 === strpos( $_POST['plugin'], WPSS_PLUGIN_NAME ) ) ) ) {
				self::wp_die();
			}
		}

		function check_wp_config_status() {
			/**
			 *  Check WordPress Config Status
			 *  @since 1.9.7.1
			 *  TO DO: Add checks specific to Multisite when we add full Multisite compatibility (Network Activation)
			 */
			if( is_multisite() ) { return FALSE; }		/* Exempt Multisite to prevent issues with domain mapping */
			$site_url	= get_option( 'siteurl' );		/* WordPress Address (URL)	- site_url() - The location where WordPress core files reside. */
			$home_url	= get_option( 'home' );			/* Site Address (URL)		- home_url() - The URL people should use to get to the site. */
			$site_dom	= rs_wpss_get_domain( $site_url );
			$home_dom	= rs_wpss_get_domain( $home_url );
			/* if( !is_multisite() && !empty( $site_dom ) && !empty( $home_dom ) && $site_dom !== $home_dom ) { return __( 'Website domains do not match.', 'wp-spamshield' ); } // USE LATER */
			if( !empty( $site_dom ) && !empty( $home_dom ) && $site_dom !== $home_dom ) { return __( 'Website domains do not match.', 'wp-spamshield' ); } /* Domain Mismatch */
			return FALSE;
		}

		function editor_add_quicktags() {
			 /* Added 1.8.3 */
			if( rs_wpss_is_admin_sproc( TRUE ) ) { return; }
			$current_screen = get_current_screen();
			if( !wp_script_is( 'quicktags' ) || $current_screen->parent_base !== 'edit' || $current_screen->post_type !== 'page' || !current_user_can( 'edit_pages' ) ) { return; }
			echo WPSS_EOL.'<script type=\'text/javascript\'>'.WPSS_EOL.'/* <![CDATA[ */'.WPSS_EOL.'QTags.addButton( \'wpss_cf\', \'WPSS '.__( 'Contact Form', 'wp-spamshield' ).'\', \'[spamshieldcontact]\', \'\', \'\', \'WP-SpamShield '.__( 'Contact Form', 'wp-spamshield' ).'\', 999 );'.WPSS_EOL.'/* ]]> */'.WPSS_EOL.'</script>';
		}

		function num_days_inst() {
			global $spamshield_options; if( empty( $spamshield_options ) ) { $spamshield_options = get_option('spamshield_options'); }
			$current_date	= date('Y-m-d');
			$install_date	= empty( $spamshield_options['install_date'] ) ? $current_date : $spamshield_options['install_date'];
			$num_days_inst	= rs_wpss_date_diff($install_date, $current_date); if( $num_days_inst < 1 ) { $num_days_inst = 1; }
			return $num_days_inst;
		}

		function spam_blocked_daily() {
			$num_days_inst	= $this->num_days_inst();
			$spam_count		= rs_wpss_count(); if( $spam_count < 1 ) { $spam_count = 1; }
			return round( $spam_count / $num_days_inst );
		}

		function est_hrs_returned( $spam_count ) {
			if( empty( $spam_count ) ) { $spam_count = rs_wpss_count(); }
			return round( $spam_count / WPSS_SPH );
		}

		static public function mail( $to, $subject, $message, $headers = '', $attachments = NULL, $submission_type = '' ) {
			/**
			 *  This is a wrapper for the wp_mail() function and removes *some* data leakage that happens in the PHP mail process.
			 *  PHP-generated emails (especially on shared hosts) can leak data in the headers and potentially reveal sensitive path info, as well as the main username for a website.
			 *  For more secure mail, users should use SMTP so there isn't data leakage as with the PHP-generated emails.
			 *  @since 1.9.5.5
			 */

			/* Obscure */
			$orig_server	= $_SERVER;
			$orig_env		= $_ENV;
			$orig_ip		= $_SERVER['REMOTE_ADDR'];
			$obsc_ip		= WPSS_SERVER_ADDR;
			$orig_script	= $_SERVER['SCRIPT_FILENAME'];
			$orig_docroot	= $_SERVER['DOCUMENT_ROOT'];
			$orig_requri	= $_SERVER['REQUEST_URI'];

			$obscure = array(
				'REMOTE_ADDR' => $obsc_ip, 'HTTP_X_FORWARDED_FOR' => $obsc_ip, 'HTTP_X_FORWARDED' => $obsc_ip, 'HTTP_FORWARDED_FOR' => $obsc_ip, 'HTTP_FORWARDED' => $obsc_ip, 'HTTP_X_REAL_IP' => $obsc_ip, 'DOCUMENT_ROOT' => '/', 'PHP_SELF' => '/', 'REQUEST_URI' => '/', 'SCRIPT_FILENAME' => '/', 'SCRIPT_NAME' => '/', 'REDIRECT_URL' => '/', 'PHPRC' => '/', 'HTTP_REFERER' => '', 
				);
			foreach( $_SERVER as $k => $v )	{
				if( isset( $obscure[$k] ) )	{ $_SERVER[$k] = $obscure[$k]; }
				if( $v === $orig_ip )		{ $_SERVER[$k] = $obsc_ip; }
				if( !is_string( $v ) )		{ continue; }
				if( FALSE !== strpos( $v, $orig_script ) || FALSE !== strpos( $v, $orig_docroot ) || FALSE !== strpos( $v, $orig_requri ) )	{ $_SERVER[$k] = '/'; $_ENV[$k] = '/'; }
			}
			foreach( $_ENV as $k => $v )	{
				if( isset( $obscure[$k] ) )	{ $_ENV[$k] = $obscure[$k]; }
				if( $v === $orig_ip )		{ $_ENV[$k] = $obsc_ip; }
				if( !is_string( $v ) )		{ continue; }
				if( FALSE !== strpos( $v, $orig_script ) || FALSE !== strpos( $v, $orig_docroot ) || FALSE !== strpos( $v, $orig_requri ) )	{ $_ENV[$k] = '/'; $_SERVER[$k] = '/'; }
			}

			if( 'contact' === $submission_type ) {
				/* PHPMailer Options */
				add_action( 'phpmailer_init', array( __CLASS__, 'phpmailer_config' ), 999 );
			}
			/* Mail */
			$sent = @wp_mail( $to, $subject, $message, $headers, $attachments );
			/* Restore */
			$_SERVER = $orig_server; $_ENV = $orig_env;
			return $sent;
		}

		static public function phpmailer_config( &$phpmailer ) {
			/**
			 *  Set some PHPMailer options
			 */
			if( self::get_wpss_option( 'form_mail_encode' ) ) {
				$phpmailer->Encoding = 'base64'; /* Encode if option enabled - NOTE: Using SMTP plugins may disable/override this feature */
			}
			$phpmailer->Priority = 1;
			$phpmailer->XMailer = ' '; /* Remove X-Mailer header */
			$phpmailer->LE = "\r\n";
		}

		static public function deprecated_options_check( $activation = FALSE ) {
			/**
			 *  Gets new option, and deletes deprecated option if it exists
			 *  This was added to help reduce the number of rows in the options table and help clean up the DB
			 *  @since 1.9.5.5
			 */
			global $spamshield_options; if( empty( $spamshield_options ) ) { $spamshield_options = get_option('spamshield_options'); }
			$wpss_dep_options			= array(
				/* 'new_index'			=> 'original_option', */
				'wpss_version'			=> 'wp_spamshield_version',
				/*
				'install_status'		=> 'spamshield_install_status',
				'warning_status'		=> 'spamshield_warning_status',
				'regalert_status'		=> 'spamshield_regalert_status',
				'last_admin'			=> 'spamshield_last_admin',
				'wpss_admins'			=> 'spamshield_admins',
				'reg_count'				=> 'spamshield_reg_count',
				'spam_count'			=> 'spamshield_count',
				'wpssmid_cache'			=> 'spamshield_wpssmid_cache',
				'wpss_procdat'			=> 'spamshield_procdat',
				*/
				);
			$updated = FALSE;
			foreach( $wpss_dep_options as $new => $original ) {
				if( !isset( $spamshield_options[$new] ) ) {
					/* Check for deprecated */
					$depr_option = get_option( $original );
					if( !empty( $depr_option ) ) {
						$spamshield_options[$new] = $depr_option;
						delete_option( $original );
						$updated = TRUE;
					}
				}
			}
			if( FALSE === $activation && TRUE === $updated ) { self::update_wpss_option( $spamshield_options ); }
		}

		static public function get_wpss_option( $option = 'all' ) {
			/**
			 *  @since 1.9.5.5
			 */
			global $spamshield_options; if( empty( $spamshield_options ) ) { $spamshield_options = get_option('spamshield_options'); }
			/* TO DO: Add ... */
			if( 'all' === $option ) { return $spamshield_options; }
			return isset( $spamshield_options[$option] ) ? $spamshield_options[$option] : '';
		}

		static public function update_wpss_option( $arr, $update = TRUE ) {
			/**
			 *  Differs from update_option() in that it only takes an associative array with option name/value pair(s) and can update multiple options at once.
			 *  @since 1.9.5.5
			 */
			global $spamshield_options; if( empty( $spamshield_options ) ) { $spamshield_options = get_option('spamshield_options'); }
			foreach ( $arr as $option => $value ) { $spamshield_options[$option] = $value; }
			/* TO DO: Add ... */
			if( TRUE === $update ) { update_option( 'spamshield_options', $spamshield_options ); }
		}

		static public function delete_wpss_option( $arr, $update = TRUE ) {
			/**
			 *  Differs from delete_option() in that it only takes a numeric array with option name(s) and can delete multiple options at once.
			 *  @since 1.9.9.4
			 */
			global $spamshield_options; if( empty( $spamshield_options ) ) { $spamshield_options = get_option('spamshield_options'); }
			foreach ( $arr as $i => $option ) { unset ( $spamshield_options[$option] ); }
			/* TO DO: Add ... */
			if( TRUE === $update ) { update_option( 'spamshield_options', $spamshield_options ); }
		}

		static public function wp_die( $error_msg = NULL, $cust_error_txt = FALSE, $status_code = '403' ) {
			/**
			 *  Wrapper for wp_die() with nicer formatting.
			 */
			remove_filter( 'status_header', 'rs_wpss_mod_status_header', 100 );
			$error_txt	= '<strong>'.rs_wpss_error_txt().':</strong> ';
			$error_txt	= ( !empty( $cust_error_txt ) && !empty( $error_msg ) ) ? '' : $error_txt;
			$error_msg	= empty( $error_msg ) ? __('You do not have sufficient permissions to access this page.') : $error_msg;
			$error_str	= $error_txt.$error_msg.WPSS_EOL;
			$args		= array( 'response' => $status_code );
			wp_die( $error_str, '', $args );
		}

		/**
		 * Get WordPress Database Size
		 * @since 1.9.9.3
		 * @return string
		 */
		static public function get_db_size() {
			global $wpdb; $db_size = 0;
			$query = $wpdb->get_results("SHOW table STATUS", ARRAY_A);
			foreach( $query as $row ) {
				$db_size += $row['Data_length'] + $row['Index_length'];
			}
			return $db_size;
		}

		/* Class Admin Functions - END */


		/* Other Functions - BEGIN */

		function update_admin_status( $user_id = NULL, $admin_ips = NULL ) {
			if( empty( $user_id ) ){ global $current_user; $current_user = wp_get_current_user(); $user_id = $current_user->ID; }
			if( empty( $user_id ) ){ return; }
			$admin_status = rs_wpss_is_user_admin() ? TRUE : FALSE;
			update_user_meta( $user_id, 'wpss_admin_status', $admin_status );
			$add_admin_ip = !empty( $admin_status ) ? TRUE : FALSE;
			rs_wpss_update_user_ip( $user_id, $add_admin_ip, $admin_ips );
			rs_wpss_remove_expired_admins( $admin_ips );
		}

		private function clear_cache_plugins() { /* Not fully implemented yet - still testing */
			/**
			 *  Automatically clears page caches after plugin install or upgraded
			 *  Called from upgrade_check()
			 *  @since 1.9.6.2, Modified 1.9.7.5
			 */
			global $wpss_cache_check,$wp_fastest_cache,$file_prefix,$supercachedir;
			/* Flush Memcache(d) */
			$flush_memcache = wp_cache_flush();
			return; /* Temp - 1.9.7.5 */
			/* Check if cache plugins or server-side caching active */
			if( empty( $wpss_cache_check ) ) { $wpss_cache_check = rs_wpss_check_cache_status(); }
			if( $wpss_cache_check['cache_check_status'] === 'ACTIVE' ) {
				if( WPSS_Compatibility::is_plugin_active( 'w3-total-cache/w3-total-cache.php' ) && function_exists( 'w3tc_pgcache_flush' ) ) { /* W3 Total Cache */
					w3tc_pgcache_flush();
				}
				if( WPSS_Compatibility::is_plugin_active( 'wp-super-cache/wp-cache.php' ) && function_exists( 'wp_cache_clean_cache' ) ) { /* WP Super Cache */
					if( empty( $supercachedir ) && function_exists( 'get_supercache_dir' ) ) {
						$supercachedir = get_supercache_dir();
					}
					wp_cache_clean_cache( $file_prefix );
				}
				if( WPSS_Compatibility::is_plugin_active( 'wp-fastest-cache/wpFastestCache.php' ) && method_exists( 'WpFastestCache', 'deleteCache' ) && !empty( $wp_fastest_cache ) ) { /* WP Fastest Cache */
					$wp_fastest_cache->deleteCache();
				}
				if( class_exists( 'WPE_API', FALSE ) && class_exists( 'WpeCommon' ) ) { /* WP Engine Hosting */
					if( method_exists( 'WpeCommon', 'purge_memcached' ) ) {
						WpeCommon::purge_memcached();
					}
					if( method_exists( 'WpeCommon', 'clear_maxcdn_cache' ) ) {
						WpeCommon::clear_maxcdn_cache();
					}
					if( method_exists( 'WpeCommon', 'purge_varnish_cache' ) ) {
						WpeCommon::purge_varnish_cache();
					}
				}
				/* Add Autoptimize & other Cache Plugins... */
			}
		}

		/* Other Functions - END */


		/* Output Functions - BEGIN */

		function insert_head_js() {
			/**
			 *  This JavaScript is purposely NOT enqueued. It's not coded improperly. This is done exactly like it is for a very good reason.
			 *  It needs to not be modified by any other plugin.
			 *  The JS file is really a dynamically generated hybrid script that uses both server-side and client-side code so it requires the PHP functionality.
			 *  "But couldn't that be done by..." Stop right there...No, it cannot.
			 */
			if( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) ) { return; }
			if( ( ( !is_admin() && rs_wpss_is_user_logged_in() ) || !rs_wpss_is_user_logged_in() ) && !rs_wpss_is_admin_sproc() ) {
				echo WPSS_EOL;
				global $wpss_ao_active; $ao_noop_open = $ao_noop_close = '';
				$js_url = WPSS_PLUGIN_JS_URL.'/jscripts.php';
				if( empty( $wpss_ao_active ) ) { $wpss_ao_active = WPSS_Compatibility::is_plugin_active( 'autoptimize/autoptimize.php' ); }
				if( !empty( $wpss_ao_active ) ) { $ao_noop_open = '<!--noptimize-->'; $ao_noop_close = '<!--/noptimize-->'; }
				echo $ao_noop_open."<script type='text/javascript' src='". $js_url ."'></script>".$ao_noop_close." ".WPSS_EOL;
				if( rs_wpss_is_session_active() ) {
					if( !empty( $_SESSION['wpss_user_ip_init_'.WPSS_HASH] ) ) { $_SESSION['wpss_user_ip_init_'.WPSS_HASH] = rs_wpss_get_ip_addr(); }
				}
			}
		}

		static public function insert_footer_js( $ret = FALSE ) {
			/**
			 *  Insert WP-SpamShield JS into footer. This adds essential hidden fields to the relevant forms via jQuery. (REF2XJS and FVFJS)
			 *  @since 1.8.9.9
			 */
			if( ( ( !is_admin() && rs_wpss_is_user_logged_in() ) || !rs_wpss_is_user_logged_in() ) && !rs_wpss_is_admin_sproc() ) {
				/* REF2XJS and FVFJS code */
				$wpss_key_values 	= rs_wpss_get_key_values();
				$wpss_js_key 		= $wpss_key_values['wpss_js_key'];
				$wpss_js_val 		= $wpss_key_values['wpss_js_val'];
				global $spamshield_options; if( empty( $spamshield_options ) ) { $spamshield_options = get_option('spamshield_options'); }
				$comment_min_length = ( !empty( $spamshield_options['comment_min_length'] ) && is_int( $spamshield_options['comment_min_length'] ) ) ? $spamshield_options['comment_min_length'] : '15';
				$cm_var = $cm_str = $bp_str = '';
				if( rs_wpss_comments_open() ) {
					$cm_var			= 'cm4S="form[action=\''.WPSS_COMMENTS_POST_URL.'\']";'.WPSS_EOL;
					$cm_str			= ', "+cm4S+"';
				}
				if( class_exists( 'BuddyPress' ) ) {
					$bp_single		= rs_wpss_is_3p_register_page() ? ', #signup_form' : '';
					$bp_str			= ', #buddypress #signup_form, #buddypress #register-page #signup_form, .buddypress #signup_form'.$bp_single;
				}
				$cf7_str			= defined( 'WPCF7_VERSION' ) ? ', .wpcf7-form' : '';
				$gf_str				= class_exists( 'GFForms' ) ? ', .gform_wrapper form' : '';
				$tpr_str			= rs_wpss_is_3p_register_page() ? ', .login-form.register-form' : '';
				$js = WPSS_EOL;
				global $wpss_ao_active; $ao_noop_open = $ao_noop_close = '';
				if( empty( $wpss_ao_active ) ) { $wpss_ao_active = WPSS_Compatibility::is_plugin_active( 'autoptimize/autoptimize.php' ); }
				if( !empty( $wpss_ao_active ) ) { $ao_noop_open = '<!--noptimize-->'; $ao_noop_close = '<!--/noptimize-->'; } /* Add noptimize tags if Autoptimize is active */
				$js .= $ao_noop_open.'<script type=\'text/javascript\'>'.WPSS_EOL.'/* <![CDATA[ */'.WPSS_EOL.WPSS_REF2XJS.'=escape(document[\'referrer\']);'.WPSS_EOL.'hf4N=\''.$wpss_js_key.'\';'.WPSS_EOL.'hf4V=\''.$wpss_js_val.'\';'.WPSS_EOL.$cm_var.'jQuery(document).ready(function($){'.'var e="#commentform, .comment-respond form, .comment-form'.$cm_str.', #lostpasswordform, #registerform, #loginform, #login_form'.$tpr_str.', #wpss_contact_form'.$cf7_str.$gf_str.$bp_str;
				$js .= WPSS_Compatibility::footer_js();
				$js .= '";$(e).submit(function(){$("<input>").attr("type","hidden").attr("name","'.WPSS_REF2XJS.'").attr("value",'.WPSS_REF2XJS.').appendTo(e);';
				if( FALSE === WPSS_COMPAT_MODE && !defined( 'WPSS_SOFT_COMPAT_MODE' ) ) { $js .= '$("<input>").attr("type","hidden").attr("name",hf4N).attr("value",hf4V).appendTo(e);'; }
				$js .= 'return true;});';
				if( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) ) { $js .= 'var h="form[method=\'post\']";$(h).submit(function(){$("<input>").attr("type","hidden").attr("name",hf4N).attr("value",hf4V).appendTo(h);return true;});'; }
				else { $js .= '$("#comment").attr({minlength:"'.$comment_min_length.'",maxlength:"15360"})'; }
				$js .= '});'.WPSS_EOL.'/* ]]> */'.WPSS_EOL.'</script>'.$ao_noop_close." ".WPSS_EOL;
				if( TRUE === $ret ) { return $js; } else { echo $js; }
			}
		}

		function enqueue_scripts() {
			if( ( ( !is_admin() && rs_wpss_is_user_logged_in() ) || !rs_wpss_is_user_logged_in() ) && !rs_wpss_is_admin_sproc() ) {
				$js_handle = 'wpss-jscripts-ftr'; $js_file = 'jscripts-ftr-min.js'; $js_ver = NULL; /* '1.0', WPSS_VERSION */
				if( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) || current_user_can( 'moderate_comments' ) ) { $js_file = 'jscripts-ftr2-min.js'; }
				$js_url = WPSS_PLUGIN_JS_URL.'/'.$js_file;
				wp_register_script( $js_handle, $js_url, array( 'jquery' ), $js_ver, TRUE );
				wp_enqueue_script( $js_handle );
			}
		}

		/* Output Functions - END */

	}
}

/* WP_SpamShield CLASS - END */

class WPSS_Promo_Links {

	static public function promo_text( $int ) {
		/**
		 *  Text to display in counter widget and promo links.
		 *  Also used in link title attribute.
		 */
		$promo_txt = array(
			/* Showing key numbers to avoid having to count manually when implementing */
			0 	=> __( 'SPAM BLOCKED', 'wp-spamshield' ),
			1	=> __( 'BY WP-SPAMSHIELD', 'wp-spamshield' ),
			2	=> 'WP-SpamShield ' . __( 'Spam Plugin for WordPress', 'wp-spamshield' ),
			3	=> 'WP-SpamShield ' . __( 'WordPress Anti-Spam Plugin', 'wp-spamshield' ),
			4	=> 'WP-SpamShield - ' . __( 'WordPress Spam Filter', 'wp-spamshield' ),
			5	=> 'WP-SpamShield - ' . __( 'WordPress Anti-Spam', 'wp-spamshield' ),
			6	=> __( 'Spam Blocked by WP-SpamShield', 'wp-spamshield' ) . ' - ' . __( 'WordPress Anti-Spam Plugin', 'wp-spamshield' ),
			7	=> __( 'Spam Blocked by WP-SpamShield', 'wp-spamshield' ) . ' - ' . __( 'WordPress Spam Plugin', 'wp-spamshield' ),
			8	=> __( 'Spam Blocked by WP-SpamShield', 'wp-spamshield' ) . ' - ' . __( 'WordPress Spam Blocker', 'wp-spamshield' ),
			9	=> __( 'Protected by WP-SpamShield', 'wp-spamshield' ) . ' - ' . __( 'WordPress Spam Filter', 'wp-spamshield' ),
			10	=> __( 'Protected by WP-SpamShield', 'wp-spamshield' ) . ' - ' . __( 'WordPress Anti Spam Plugin', 'wp-spamshield' ),
			11	=> 'WP-SpamShield - ' . __( 'WordPress Anti-Spam Plugin', 'wp-spamshield' ),
			12	=> 'WP-SpamShield - ' . __( 'WordPress Anti Spam Plugin', 'wp-spamshield' ),
			);
		return $promo_txt[$int];
	}

	static public function contact_promo_link( $int ) {
		/* Promo link for contact page if user opts in */
		$cf_title = __( 'WP-SpamShield Contact Form for WordPress', 'wp-spamshield' );
		$cf_txt = __( 'Contact Form', 'wp-spamshield' );
		$url = self::promo_link_url();
		if( empty( $url ) ) { $url = WPSS_HOME_URL; }
		$link_template = '<a href="'.$url.'" title="'.$cf_title.'" >X1X2X</a>';
		$promo_link_data = array(
			/* Showing key numbers to avoid having to count manually when implementing */
			0	=> __( 'Contact Form Powered by WP-SpamShield', 'wp-spamshield' ) . '|' . __( 'Contact Form', 'wp-spamshield' ),
			1	=> __( 'Powered by WP-SpamShield Contact Form', 'wp-spamshield' ) . '|' . __( 'WP-SpamShield Contact Form', 'wp-spamshield' ),
			2	=> __( 'Contact Form Powered by WP-SpamShield', 'wp-spamshield' ) . '|' . 'WP-SpamShield',
			);
		$promo_link_arr = explode( '|', $promo_link_data[$int] );
		if( !rs_wpss_is_lang_en_us() ) {
			$promo_link_ahref = str_replace( 'X1X2X', $promo_link_arr[0], $link_template );
			$promo_link_phrase = $promo_link_ahref;
		} else {
			$promo_link_ahref = str_replace( 'X1X2X', $promo_link_arr[1], $link_template );
			$promo_link_phrase = str_replace( $promo_link_arr[1], $promo_link_ahref, $promo_link_arr[0] );
		}
		$promo_link_html = '<p style="font-size:9px;clear:both;">'.$promo_link_phrase.'</p>';
		return $promo_link_html;
	}

	static public function comment_promo_link( $int ) {
		/* Promo link for comments box if user opts in */
		$comment_title = __( 'WP-SpamShield WordPress Anti-Spam Plugin', 'wp-spamshield' );
		$url = self::promo_link_url();
		if( empty( $url ) ) { $url = WPSS_HOME_URL; }
		$link_template = '<a href="'.$url.'" title="'.$comment_title.'" >X1X2X</a>';
		$promo_link_data = array(
			/* Showing key numbers to avoid having to count manually when implementing */
			0	=> __( 'WordPress Anti-Spam by WP-SpamShield', 'wp-spamshield' ) . '|' . __( 'WordPress Anti-Spam', 'wp-spamshield' ),
			1	=> __( 'WordPress Anti-Spam by WP-SpamShield', 'wp-spamshield' ) . '|' . 'WP-SpamShield',
			2	=> __( 'WordPress Anti Spam by WP-SpamShield', 'wp-spamshield' ) . '|' . __( 'WordPress Anti Spam', 'wp-spamshield' ),
			3	=> __( 'Comments Protected by WP-SpamShield Anti-Spam', 'wp-spamshield' ) . '|' . __( 'WP-SpamShield Anti-Spam', 'wp-spamshield' ),
			4	=> __( 'Comments Protected by WP-SpamShield Spam Plugin', 'wp-spamshield' ) . '|' . __( 'WP-SpamShield Spam Plugin', 'wp-spamshield' ),
			5	=> __( 'Comments Protected by WP-SpamShield Spam Filter', 'wp-spamshield' ) . '|' . __( 'WP-SpamShield Spam Filter', 'wp-spamshield' ),
			6	=> __( 'Comments Protected by WP-SpamShield Spam Blocker', 'wp-spamshield' ) . '|' . __( 'WP-SpamShield Spam Blocker', 'wp-spamshield' ),
			7	=> __( 'Comments Protected by WP-SpamShield for WordPress', 'wp-spamshield' ) . '|' . __( 'WP-SpamShield for WordPress', 'wp-spamshield' ),
			8	=> __( 'Spam Blocking by WP-SpamShield', 'wp-spamshield' ) . '|' . __( 'Spam Blocking', 'wp-spamshield' ),
			9	=> __( 'Anti-Spam by WP-SpamShield', 'wp-spamshield' ) . '|' . __( 'Anti-Spam', 'wp-spamshield' ),
			10	=> __( 'Comment Spam Blocking by WP-SpamShield', 'wp-spamshield' ) . '|' . __( 'Comment Spam Blocking', 'wp-spamshield' ),
			);
		$promo_link_arr = explode( '|', $promo_link_data[$int] );
		if( !rs_wpss_is_lang_en_us() ) {
			$promo_link_ahref = str_replace( 'X1X2X', $promo_link_arr[0], $link_template );
			$promo_link_phrase = $promo_link_ahref;
		} else {
			$promo_link_ahref = str_replace( 'X1X2X', $promo_link_arr[1], $link_template );
			$promo_link_phrase = str_replace( $promo_link_arr[1], $promo_link_ahref, $promo_link_arr[0] );
		}
		$promo_link_html = '<p style="font-size:9px;clear:both;">'.$promo_link_phrase.'</p>';
		return $promo_link_html;
	}

	static public function promo_link_url() {
		/**
		 *  In the plugin promo links, sometimes use plugin homepage link, sometime use WP.org link to make sure both get visited
		 *  4 to 1 plugin homepage to WP.org (which more people link to anyway)
		 */
		$sip5c = '0';
		$sip5c = substr(WPSS_SERVER_ADDR, 4, 1); /* Server IP 5th Char */
		$gplu_code = array( '0' => 0, '1' => 1, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 1, '.' => 0 );
		$urls = array( WPSS_HOME_URL, WPSS_WP_URL );
		if( preg_match( "~^[0-9\.]$~", $sip5c ) ) { $k = $gplu_code[$sip5c]; } else { $k = 0; }
		$url = $urls[$k];
		if( empty( $url ) ) { $url = WPSS_HOME_URL; }
		return $url;
	}
}


if( class_exists('WP_SpamShield') ) {
	$WP_SpamShield = new WP_SpamShield();
}

/* PLUGIN - END */
