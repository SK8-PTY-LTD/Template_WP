<?php

/*
Plugin Name: Advanced Woo Search
Description: Advance ajax WooCommerce product search.
Version: 1.09
Author: ILLID
Text Domain: aws
*/


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AWS_VERSION', '1.09' );


define( 'AWS_DIR', dirname( __FILE__ ) );
define( 'AWS_URL', plugins_url( '', __FILE__ ) );


define( 'AWS_INDEX_TABLE_NAME', 'aws_index' );


if ( ! class_exists( 'AWS_Main' ) ) :

/**
 * Main plugin class
 *
 * @class AWS_Main
 */
final class AWS_Main {

	/**
	 * @var AWS_Main The single instance of the class
	 */
	protected static $_instance = null;

    /**
     * @var AWS_Main Array of all plugin data $data
     */
    private $data = array();

	/**
	 * Main AWS_Main Instance
	 *
	 * Ensures only one instance of AWS_Main is loaded or can be loaded.
	 *
	 * @static
	 * @return AWS_Main - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {

        $this->data['settings'] = get_option( 'aws_settings' );

		add_filter( 'widget_text', 'do_shortcode' );

		add_shortcode( 'aws_search_form', array( $this, 'markup' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );

		add_filter( 'plugin_action_links', array( $this, 'add_action_link' ), 10, 2 );

		load_plugin_textdomain( 'aws', false, dirname( plugin_basename( __FILE__ ) ). '/languages/' );

        update_option( 'aws_plugin_ver', AWS_VERSION );

        $this->includes();

	}

    /**
     * Include required core files used in admin and on the frontend.
     */
    public function includes() {
        include_once( 'includes/class-aws-helpers.php' );
        include_once( 'includes/class-aws-admin.php' );
        include_once( 'includes/class-aws-table.php' );
        include_once( 'includes/class-aws-markup.php' );
        include_once( 'includes/class-aws-search.php' );
        include_once( 'includes/widget.php' );
    }

	/*
	 * Generate search box markup
	 */
	 public function markup( $args = array() ) {

         $markup = new AWS_Markup();

         return $markup->markup();

	}

	/*
	 * Load assets for search form
	 */
	public function load_scripts() {
		wp_enqueue_style( 'aws-style', AWS_URL . '/assets/css/common.css', array(), AWS_VERSION );
		wp_enqueue_script( 'aws-script', AWS_URL . '/assets/js/common.js', array('jquery'), AWS_VERSION, true );
	}

	/*
	 * Add settings link to plugins
	 */
	public function add_action_link( $links, $file ) {
		$plugin_base = plugin_basename( __FILE__ );

		if ( $file == $plugin_base ) {
			$setting_link = '<a href="' . admin_url('admin.php?page=aws-options') . '">'.__( 'Settings', 'aws' ).'</a>';
			array_unshift( $links, $setting_link );

            $premium_link = '<a href="https://advanced-woo-search.com/" target="_blank">'.__( 'Get Premium', 'aws' ).'</a>';
            array_unshift( $links, $premium_link );
		}

		return $links;
	}

    /*
     * Get plugin settings
     */
    public function get_settings( $name ) {
        $plugin_options = $this->data['settings'];
        return $plugin_options[ $name ];
    }

}

endif;


/**
 * Returns the main instance of AWS_Main
 *
 * @return AWS_Main
 */
function AWS() {
    return AWS_Main::instance();
}


/*
 * Check if WooCommerce is active
 */
if ( aws_is_plugin_active('woocommerce/woocommerce.php') ) {
	add_action( 'woocommerce_loaded', 'aws_init' );
} else {
	add_action( 'admin_notices', 'aws_install_woocommerce_admin_notice' );
}


/*
 * Check whether the plugin is active by checking the active_plugins list.
 */
function aws_is_plugin_active( $plugin ) {
    return in_array( $plugin, (array) get_option( 'active_plugins', array() ) ) || aws_is_plugin_active_for_network( $plugin );
}


/*
 * Check whether the plugin is active for the entire network
 */
function aws_is_plugin_active_for_network( $plugin ) {
    if ( !is_multisite() )
        return false;

    $plugins = get_site_option( 'active_sitewide_plugins');
    if ( isset($plugins[$plugin]) )
        return true;

    return false;
}


/*
 * Error notice if WooCommerce plugin is not active
 */
function aws_install_woocommerce_admin_notice() {
	?>
	<div class="error">
		<p><?php _e( 'Advanced Woo Search plugin is enabled but not effective. It requires WooCommerce in order to work.', 'aws' ); ?></p>
	</div>
	<?php
}

/*
 * Init AWS plugin
 */
function aws_init() {
    AWS();
}