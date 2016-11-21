<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 *	Class WPJMP_Settings.
 *
 *	This class handles everything concerning the settings.
 *
 *	@class		WPJMP_Settings
 *	@version	1.0.0
 *	@author		Jeroen Sormani
 */
class WPJMP_Settings {


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Settings tab
		add_action( 'job_manager_settings', array( $this, 'wpjmp_settings' ) );

	}


	/**
	 * Settings page.
	 *
	 * Add an settings tab to the Listings -> settings page.
	 *
	 * @since 1.0.0
	 *
	 * @param 	array 	$settings	Array of default settings.
	 * @return 	array	$settings	Array including the new settings.
	 */
	public function wpjmp_settings( $settings )  {

		$settings['wpjmp_settings'] = array(
			__( 'Products', 'wp-job-manager-products' ),
			array(

				array(
					'name'			=> 'wpjmp_products_limit',
					'type'			=> 'select',
					'label'			=> __( 'Products limitation', 'wp-job-manager-products' ),
					'desc'			=> __( 'What products can listing owners select? <small>Admins will see all products</small>', 'wp-job-manager-products' ),
					'options'		=> array(
						'own' => __( 'Only their own', 'wp-job-manager-products' ),
						'all' => __( 'All', 'wp-job-manager-products' ),
					),
				),

				array(
					'name'			=> 'wpjmp_select_products_text',
					'type'			=> 'text',
					'label'			=> __( 'Select Products Text', 'wp-job-manager-products' ),
					'desc'			=> __( '<em>Default: "Select Your Services & Products"</em>', 'wp-job-manager-products' ),
					'std'			=> 'Select Your Services & Products',
				),

				array(
					'name'			=> 'wpjmp_listing_products_text',
					'type'			=> 'text',
					'label'			=> __( 'Listing Products Text', 'wp-job-manager-products' ),
					'desc'			=> __( '<em>Default: "Listing products"</em>', 'wp-job-manager-products' ),
					'std'			=> __( 'Listing products', 'wp-job-manager-products' ),
				),

				array(
					'name'			=> 'wpjmp_enable_products_on_listings',
					'std'			=> '1',
					'placeholder'	=> '',
					'label'			=> __( 'Enable Products List for Job Listings', 'wp-job-manager-products' ),
					'cb_label'		=> __( 'Enable product selection when submitting a listing using [submit_job_form]', 'wp-job-manager-products' ),
					'desc'			=> __( 'Allow users to attach products to listings when submitting a listing on the frontend.', 'wp-job-manager-products' ),
					'type'			=> 'checkbox'
				),

				array(
					'name'			=> 'wpjmp_enable_products_on_events',
					'std'			=> '1',
					'placeholder'	=> '',
					'label'			=> __( 'Enable Products List for Events', 'wp-job-manager-products' ),
					'cb_label'		=> __( 'Enable product selection when submitting an event using [submit_event_form]', 'wp-job-manager-products' ),
					'desc'			=> __( 'Allow users to attach products to events when submitting an event on the frontend.', 'wp-job-manager-products' ),
					'type'			=> 'checkbox'
				),

				array(
					'name'			=> 'wpjmp_draft_products_on_listing_expiration',
					'std'			=> '1',
					'placeholder'	=> '',
					'label'			=> __( 'Draft Products', 'wp-job-manager-products' ),
					'cb_label'		=> __( 'Draft products connected to listings when the listing expires', 'wp-job-manager-products' ),
					'desc'			=> __( 'Force products to be set to draft when a connected listing expires.', 'wp-job-manager-products' ),
					'type'			=> 'checkbox'
				),

			),
		);

		return $settings;

	}

}
