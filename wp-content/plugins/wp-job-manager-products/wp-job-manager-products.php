<?php
/*
Plugin Name: WP Job Manager - Products
Plugin URI: http://astoundify.com/downloads/wp-job-manager-products
Description: Allows you to assign products created in WooCommerce to be associated with listings.
Version: 1.4.0
Author: Astoundify
Author URI: http://astoundify.com/
Text Domain: wp-job-manager-products
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 *	Class WP_Job_Manager_Products
 *
 *	Main WPJMP class initializes the plugin
 *
 *	@class		WP_Job_Manager_Products
 *	@version	1.0.0
 *	@author		Astoundify
 */
class WP_Job_Manager_Products {

	/**
	 * Plugin version.
	 *
	 * @since 1.0.0
	 * @var string $version Plugin version number.
	 */
	public $version = '1.4.0';

	public $products;

	/**
	 * Instace of WP_Job_Manager_Products.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var object $instance The instance of WPJMP.
	 */
	private static $instance;


	/**
	 * Construct.
	 *
	 * Initialize the class and plugin.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( ! ( class_exists( 'WooCommerce' ) && class_exists( 'WP_Job_Manager' ) ) ) {
			return;
		}

		// Enqueue scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );

		add_action( 'admin_init', array( $this, 'updater' ), 9 );

		// Init plugin
		$this->init();
	}

	public function updater() {
		include_once( 'includes/updater/class-astoundify-updater.php' );

		new Astoundify_Updater_Products( __FILE__ );
	}

	/**
	 * Enqueue scripts.
	 *
	 * Enqueue style and java scripts.
	 *
	 * @since 1.0.0
	 */
	public function wp_enqueue_scripts() {
    	// Chosen style (via WP Job Manger) only in admin
    	if ( is_admin() ) {
	    	wp_enqueue_style( 'chosen', JOB_MANAGER_PLUGIN_URL . '/assets/css/chosen.css' );
			wp_enqueue_script( 'chosen', JOB_MANAGER_PLUGIN_URL . '/assets/js/jquery-chosen/chosen.jquery.min.js', array( 'jquery' ), '1.1.0', true );
		}

	    // Only load scripts on admin and Job submit page.
	    $submit_job_page_id = get_option( 'job_manager_submit_job_form_page_id', true );
	    $edit_job_page_id	= get_option( 'job_manager_job_dashboard_page_id', true );

		if ( is_admin() || get_the_ID() == $submit_job_page_id || '' == $submit_job_page_id || get_the_ID() == $edit_job_page_id || '' == $edit_job_page_id ) {

			$deps = array( 'jquery', 'chosen' );

	    	wp_register_script( 'wp-job-manager-products-js', plugins_url( 'assets/js/wp-job-manager-products.js', __FILE__ ), $deps, $this->version );

	    	wp_localize_script( 'wp-job-manager-products-js', 'wpjmp', apply_filters( 'wpjmp_javascript_variables', array(
	    		'chosen_max_selected_options'	=> null,
	    		'no_results_text' 				=> __( 'Oops, nothing found!', 'wp-job-manager-products' ),
	    	) ) );

	    	wp_enqueue_script( 'wp-job-manager-products-js' );
		} 
	}

	/**
	 * Instance.
	 *
	 * An global instance of the class. Used to retrieve the instance
	 * to use on other files/plugins/themes.
	 *
	 * @since 1.0.0
	 * @return object Instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) :
			self::$instance = new self();
		endif;

		return self::$instance;
	}

	/**
	 * init.
	 *
	 * Initialize plugin parts.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpjmp-products.php';
		$this->products = new WPJMP_Products();

		require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-wpjmp-settings.php';
		$this->settings = new WPJMP_Settings();

		require_once plugin_dir_path( __FILE__ ) . 'includes/template-tags.php';

		// Load textdomain
		$this->load_textdomain();
	}

	/**
	 * Textdomain.
	 *
	 * Load the textdomain based on WP language.
	 *
	 * @since 1.3.0
	 */
	public function load_textdomain() {

		$locale = apply_filters( 'plugin_locale', get_locale(), 'wp-job-manager-products' );

		// Load textdomain, search first in wp-content/languages/wp-job-manager-products/wp-job-manager-products-*.mo
		load_textdomain( 'wp-job-manager-products', WP_LANG_DIR . '/wp-job-manager-products/wp-job-manager-products-' . $locale . '.mo' );
		load_plugin_textdomain( 'wp-job-manager-products', false, basename( dirname( __FILE__ ) ) . '/languages' );

	}

}

/**
 * The main function responsible for returning the WP_Job_Manager_Products object.
 *
 * Use this function like you would a global variable, except without needing to declare the global.
 *
 * @since 1.0.0
 *
 * @return object WP_Job_Manager_Products class object.
 */
function wpjmp() {
	return WP_Job_Manager_Products::instance();
}
add_action( 'plugins_loaded', 'wpjmp' );
