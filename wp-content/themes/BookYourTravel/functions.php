<?php
/**
 * Book Your Travel functions and definitions.
 *
 * Sets up the theme and provides some helper functions, which are used
 * in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook.
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since Book Your Travel 1.0
 *
 * Loads the Options Panel
 *
 * If you're loading from a child theme use stylesheet_directory
 * instead of template_directory
 */
if ( ! defined( 'BOOKYOURTRAVEL_VERSION' ) )
    define( 'BOOKYOURTRAVEL_VERSION', '7.16' );

if ( ! defined( 'BOOKYOURTRAVEL_FRONTEND_SUBMIT_ROLE' ) )
    define( 'BOOKYOURTRAVEL_FRONTEND_SUBMIT_ROLE', 'byt_frontend_contributor' );
	
if ( ! defined( 'BOOKYOURTRAVEL_WOOCOMMERCE_SETUP_COMPLETE' ) )
    define( 'BOOKYOURTRAVEL_WOOCOMMERCE_SETUP_COMPLETE', 'byt_woocommerce_setup_complete' );
	   
if ( ! defined( 'BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_TABLE' ) )
    define( 'BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_TABLE', $wpdb->prefix . 'byt_accommodation_vacancies' );
	
if ( ! defined( 'BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_DAYS_TABLE' ) )
    define( 'BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_DAYS_TABLE', $wpdb->prefix . 'byt_accommodation_vacancies_days' );

if ( ! defined( 'BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE' ) )
    define( 'BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE', $wpdb->prefix . 'byt_accommodation_bookings' );	

if ( ! defined( 'BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_DAYS_TABLE' ) )
    define( 'BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_DAYS_TABLE', $wpdb->prefix . 'byt_accommodation_bookings_days' );	
	
if ( ! defined( 'BOOKYOURTRAVEL_CAR_RENTAL_BOOKINGS_TABLE' ) )
    define( 'BOOKYOURTRAVEL_CAR_RENTAL_BOOKINGS_TABLE', $wpdb->prefix . 'byt_car_rental_bookings' );	
	
if ( ! defined( 'BOOKYOURTRAVEL_CAR_RENTAL_BOOKING_DAYS_TABLE' ) )
    define( 'BOOKYOURTRAVEL_CAR_RENTAL_BOOKING_DAYS_TABLE', $wpdb->prefix . 'byt_car_rental_booking_days' );
	
if ( ! defined( 'BOOKYOURTRAVEL_TOUR_SCHEDULE_TABLE' ) )
    define( 'BOOKYOURTRAVEL_TOUR_SCHEDULE_TABLE', $wpdb->prefix . 'byt_tour_schedule' );
	
if ( ! defined( 'BOOKYOURTRAVEL_TOUR_SCHEDULE_DAYS_TABLE' ) )
    define( 'BOOKYOURTRAVEL_TOUR_SCHEDULE_DAYS_TABLE', $wpdb->prefix . 'byt_tour_schedule_days' );
	
if ( ! defined( 'BOOKYOURTRAVEL_TOUR_BOOKING_TABLE' ) )
    define( 'BOOKYOURTRAVEL_TOUR_BOOKING_TABLE', $wpdb->prefix . 'byt_tour_booking' );
	
if ( ! defined( 'BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE' ) )
    define( 'BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE', $wpdb->prefix . 'byt_cruise_schedule' );
	
if ( ! defined( 'BOOKYOURTRAVEL_CRUISE_SCHEDULE_DAYS_TABLE' ) )
    define( 'BOOKYOURTRAVEL_CRUISE_SCHEDULE_DAYS_TABLE', $wpdb->prefix . 'byt_cruise_schedule_days' );

if ( ! defined( 'BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE' ) )
    define( 'BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE', $wpdb->prefix . 'byt_cruise_booking' );
	
if ( ! defined( 'BOOKYOURTRAVEL_ALT_DATE_FORMAT' ) )
    define( 'BOOKYOURTRAVEL_ALT_DATE_FORMAT', 'yy-mm-dd' );
	
require_once get_template_directory() . '/includes/plugins/urlify/URLify.php';
require_once get_template_directory() . '/includes/theme_utils.php';

global $wpdb, $bookyourtravel_multi_language_count, $bookyourtravel_installed_version;

$bookyourtravel_multi_language_count = 1;
global $sitepress;
if ($sitepress) {
	$active_languages = $sitepress->get_active_languages();
	$sitepress_settings = $sitepress->get_settings();
	$hidden_languages = array();
	if (isset($sitepress_settings['hidden_languages'])) 
		$hidden_languages = $sitepress_settings['hidden_languages'];
	$bookyourtravel_multi_language_count = count($active_languages) + count($hidden_languages);
}

$bookyourtravel_installed_version = get_option('bookyourtravel_version', null);

//version_compare( $bookyourtravel_installed_version, BOOKYOURTRAVEL_VERSION, '<' )
if (  null !== $bookyourtravel_installed_version && $bookyourtravel_installed_version != 0 && $bookyourtravel_installed_version < BOOKYOURTRAVEL_VERSION) {
	update_option( '_byt_needs_update', 1 );
	update_option( '_byt_version_before_update', $bookyourtravel_installed_version );
}

if (null == $bookyourtravel_installed_version || $bookyourtravel_installed_version < BOOKYOURTRAVEL_VERSION) {
	update_option('bookyourtravel_version', BOOKYOURTRAVEL_VERSION);
}

require_once BookYourTravel_Theme_Utils::get_file_path('/includes/theme_of_default_fields.php');

if(!function_exists('optionsframework_option_name')) {
    function optionsframework_option_name() {
        
		// This gets the theme name from the stylesheet (lowercase and without spaces)
		$themename = get_option( 'stylesheet' );
		$themename = preg_replace( "/\W/", "_", strtolower( $themename ) );

        $optionsframework_settings = get_option('optionsframework');
        $optionsframework_settings['id'] = $themename;
        update_option('optionsframework', $optionsframework_settings);
    }
}

if ( !function_exists( 'optionsframework_init' ) ) {
	define( 'OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/includes/framework/' );
	require_once BookYourTravel_Theme_Utils::get_file_path('/includes/framework/options-framework.php');
}

require_once BookYourTravel_Theme_Utils::get_file_path('/includes/theme_globals.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/theme_of_custom.php');

/*-----------------------------------------------------------------------------------*/
/*	Load Widgets, Shortcodes, Metaboxes & Plugins
/*-----------------------------------------------------------------------------------*/
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/metaboxes/meta_box.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/class-tgm-plugin-activation.php');

add_action( 'tgmpa_register', 'bookyourtravel_register_required_plugins' );
/**
 * Register the required plugins for this theme.
 *
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function bookyourtravel_register_required_plugins() {

    /**
     * Array of plugin arrays. Required keys are name and slug.
     * If the source is NOT from the .org repo, then source is also required.
     */
    $plugins = array(
	
        array(
            'name'      => 'WooSidebars',
            'slug'      => 'woosidebars',
            'required'  => false,
        ),
        array(
            'name'      => 'WooCommerce',
            'slug'      => 'woocommerce',
            'required'  => true,
        ),
        array(
            'name'      => 'Contact Form 7',
            'slug'      => 'contact-form-7',
            'required'  => false,
        ),
		
        array(
            'name'      => 'Max Mega Menu',
            'slug'      => 'megamenu',
            'required'  => false,
        ),
		
        // This is an example of how to include a plugin pre-packaged with a theme.
        array(
            'name'               => 'Revolution slider', // The plugin name.
            'slug'               => 'revslider', // The plugin slug (typically the folder name).
            'source'             => BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/revslider/revslider.zip'), // The plugin source.
            'required'           => true, // If false, the plugin is only 'recommended' instead of required.
            'version'            => '5.1.6', // E.g. 1.0.0. If set, the active plugin must be this version or higher.
            'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
            'external_url'       => '', // If set, overrides default API URL and points to an external URL.
        ),

    );

    /**
     * Array of configuration settings. Amend each line as needed.
     * If you want the default strings to be available under your own theme domain,
     * leave the strings uncommented.
     * Some of the strings are added into a sprintf, so see the comments at the
     * end of each line for what each argument will be.
     */
    $config = array(
        'id'           => 'tgmpa',                 // Unique ID for hashing notices for multiple instances of TGMPA.
        'default_path' => '',                      // Default absolute path to pre-packaged plugins.
        'menu'         => 'tgmpa-install-plugins', // Menu slug.
        'has_notices'  => true,                    // Show admin notices or not.
        'dismissable'  => false,                    // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => false,                   // Automatically activate plugins after installation or not.
        'message'      => '',                      // Message to output right before the plugins table.
        'strings'      => array(
            'page_title'                      => esc_html__( 'Install Required Plugins', 'bookyourtravel' ),
            'menu_title'                      => esc_html__( 'Install Plugins', 'bookyourtravel' ),
            'installing'                      => esc_html__( 'Installing Plugin: %s', 'bookyourtravel' ), // %s = plugin name.
            'oops'                            => esc_html__( 'Something went wrong with the plugin API.', 'bookyourtravel' ),
            'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'bookyourtravel' ), // %1$s = plugin name(s).
            'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'bookyourtravel' ), // %1$s = plugin name(s).
            'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'bookyourtravel' ), // %1$s = plugin name(s).
            'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'bookyourtravel' ), // %1$s = plugin name(s).
            'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'bookyourtravel' ), // %1$s = plugin name(s).
            'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'bookyourtravel' ), // %1$s = plugin name(s).
            'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'bookyourtravel' ), // %1$s = plugin name(s).
            'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'bookyourtravel' ), // %1$s = plugin name(s).
            'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'bookyourtravel' ),
            'activate_link'                   => _n_noop( 'Begin activating plugin', 'Begin activating plugins', 'bookyourtravel' ),
            'return'                          => esc_html__( 'Return to Required Plugins Installer', 'bookyourtravel' ),
            'plugin_activated'                => esc_html__( 'Plugin activated successfully.', 'bookyourtravel' ),
            'complete'                        => esc_html__( 'All plugins installed and activated successfully. %s', 'bookyourtravel' ), // %s = dashboard link.
            'nag_type'                        => 'updated' // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
        )
    );

    tgmpa( $plugins, $config );

}

/*-----------------------------------------------------------------------------------*/
/*	Load Utilities & Ajax & Custom Post Types & metaboxes
/*-----------------------------------------------------------------------------------*/

require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/abstracts/class-entity.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-location.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-extra-item.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-room-type.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-accommodation.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-tour.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-cabin-type.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-cruise.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-car-rental.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-post.php');

require_once BookYourTravel_Theme_Utils::get_file_path('/includes/theme_filters.php');
// theme post types needs to be included before theme ajax and theme actions.
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/theme_post_types.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/theme_actions.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/theme_ajax.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/theme_meta_boxes.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/theme_woocommerce.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/admin/theme_accommodation_vacancy_admin.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/admin/theme_accommodation_booking_admin.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/admin/theme_tour_schedule_admin.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/admin/theme_tour_schedule_booking_admin.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/admin/theme_cruise_schedule_admin.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/admin/theme_cruise_schedule_booking_admin.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/admin/theme_car_rental_booking_admin.php');

require_once BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/widgets/widget-accommodation-list.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/widgets/widget-post-list.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/widgets/widget-tour-list.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/widgets/widget-cruise-list.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/widgets/widget-car_rental-list.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/widgets/widget-location-list.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/widgets/widget-search.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/widgets/widget-address.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/widgets/widget-social.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/widgets/widget-home-feature.php');

require_once BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/frontend-submit/frontend-submit.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/theme_woocommerce.php');