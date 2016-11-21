<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 *	Class WPJMP_Products.
 *
 *	This class handles everything concerning the products.
 *
 *	@class		WPJMP_Products
 *	@version	1.0.0
 *	@author		Jeroen Sormani
 */
class WPJMP_Products {


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Add products field to submit form
		add_filter( 'submit_job_form_fields', array( $this, 'submit_job_form_fields' ) );

		// Add products field to event submit form
		add_filter( 'submit_event_form_fields', array( $this, 'submit_event_form_fields' ) );

		// Save products from submit form
		add_action( 'job_manager_update_job_data', array( $this, 'update_job_data_products' ), 10, 2 );

		// Add products field to backend
		add_filter( 'job_manager_job_listing_data_fields', array( $this, 'add_listing_data_fields_product' ) );

		// Add products field to events backend
		add_filter( 'wpjm_events_event_fields', array( $this, 'add_event_data_fields_product' ) );

		// Display products on listing page
		add_action( 'single_job_listing_end', array( $this, 'listing_display_products' ) );

		// Save an empty value when no products are in $_POST
		add_action( 'job_manager_save_job_listing', array( $this, 'save_job_listing_data' ), 25, 2 );

		// Delete product when connected listing is deleted
		add_action( 'wp_trash_post', array( $this, 'delete_product_with_listing' ) );

		// check if a post is being set as expired so we can draft a product
		if ( get_option( 'wpjmp_draft_products_on_listing_expiration' ) ) {
			add_action( 'save_post', array( $this, 'draft_products_on_listing_expiration' ), 10, 2 );
		}

	}

	public function draft_products_on_listing_expiration( $post_id, $post ) {
		global $typenow;

		if ( $typenow === 'job_listing' && $post->post_status == 'expired' ) {
			// lets check for any products here
			$products = get_post_meta( $post_id, '_products', true );
			
			if ( ! empty( $products ) ) {

				remove_action( 'save_post', array( $this, 'draft_products_on_listing_expiration' ) );

				// since we are deleting the listing, lets delete the product
				foreach ( $products as $product ) {

					wp_update_post( array(
						'ID' => $product,
						'post_status' => 'draft'
					) );

				}

				add_action( 'save_post', array( $this, 'draft_products_on_listing_expiration' ), 10, 2 );

			}

		}
	}

	public function delete_product_with_listing( $post_id ) {
		global $typenow;

		if ( $typenow === 'job_listing' ) {
			// lets check for any products here
			$products = get_post_meta( $post_id, '_products', true );
			
			if ( ! empty( $products ) ) {

				// since we are deleting the listing, lets delete the product
				foreach ( $products as $product ) {

					wp_trash_post( $product );

				}

			}

		}
		
	}

	public function submit_event_form_fields( $fields ) {

		if ( ! get_option( 'wpjmp_enable_products_on_events' ) ) {
			return $fields;
		}

		global $current_user;

		$options 		= array();
		$product_args 	= array(
			'post_type' 		=> 'product',
			'posts_per_page' 	=> '-1',
			'meta_query' 		=> array(
				array(
					'key'		=> '_visibility',
					'value'		=> 'hidden',
					'compare'	=> '!=',
				),
			),
		);
		if ( 'own' == get_option( 'wpjmp_products_limit', 'own' ) && ! array_key_exists( 'administrator', $current_user->caps ) ) :
			// Don't show this field when user is not logged in
			if ( ! is_user_logged_in() ) :
				return $fields;
			endif;

			$product_args['author'] = get_current_user_id();
		endif;

		$products = get_posts( apply_filters( 'wpjmp_event_form_products_args', $product_args ) );

		foreach ( $products as $product ) :
			$options[ $product->ID ] = $product->post_title;
		endforeach;

		if ( class_exists( 'WC_Product_Vendors_Utils' ) ) {
			$vendor_products = WC_Product_Vendors_Utils::get_vendor_product_ids();

			foreach ( $vendor_products as $vproduct ) {
				$options[ $vproduct ] = get_the_title( $vproduct );
			}
		}

		if ( empty( $options ) ) :
			return $fields;
		endif;

		$fields['event_fields']['event_products'] = array(
			'label'			=> get_option( 'wpjmp_select_products_text' ),
			'type'			=> 'multiselect',
			'options'		=> $options,
			'required'		=> false,
			'priority' 		=> 20,
		);

		return $fields;

	}


	/**
	 * Product field.
	 *
	 * Add a select product field to the submit listing form products.
	 * (front-end).
	 *
	 * @since 1.0.0
	 *
	 * @param 	array $fields 	List of settingsfields.
	 * @return	array			Modified list of settingsfields.
	 */
	public function submit_job_form_fields( $fields ) {

		if ( ! get_option( 'wpjmp_enable_products_on_listings' ) ) {
			return $fields;
		}

		global $current_user;

		$options 		= array();
		$product_args 	= array(
			'post_type' 		=> 'product',
			'posts_per_page' 	=> '-1',
			'meta_query' 		=> array(
				array(
					'key'		=> '_visibility',
					'value'		=> 'hidden',
					'compare'	=> '!=',
				),
			),
		);
		if ( 'own' == get_option( 'wpjmp_products_limit', 'own' ) && ! array_key_exists( 'administrator', $current_user->caps ) ) :
			// Don't show this field when user is not logged in
			if ( ! is_user_logged_in() ) :
				return $fields;
			endif;

			$product_args['author'] = get_current_user_id();
		endif;

		$products = get_posts( apply_filters( 'wpjmp_job_form_products_args', $product_args ) );

		foreach ( $products as $product ) :
			$options[ $product->ID ] = $product->post_title;
		endforeach;

		if ( class_exists( 'WC_Product_Vendors_Utils' ) ) {
			$vendor_products = WC_Product_Vendors_Utils::get_vendor_product_ids();

			foreach ( $vendor_products as $vproduct ) {
				$options[ $vproduct ] = get_the_title( $vproduct );
			}
		}

		if ( empty( $options ) ) :
			return $fields;
		endif;

		$fields['company']['products'] = array(
			'label'			=> get_option( 'wpjmp_select_products_text' ),
			'type'			=> 'multiselect',
			'options'		=> $options,
			'required'		=> false,
			'priority' 		=> 10,
		);

		return $fields;

	}


	/**
	 * Save submit.
	 *
	 * Save the products when a listing is submitted.
	 *
	 * @since 1.0.0
	 *
	 * @param 	int/numberic $job_id List of settingsfields.
	 * @param 	array		 $values List of posted values.
	 */
	public function update_job_data_products( $job_id, $values ) {

		$value = isset( $values['company']['products'] ) ? $values['company']['products'] : false;

		if ( $value ) {
			update_post_meta( $job_id, '_products', $value );
		}

	}

	public function add_event_data_fields_product( $fields ) {

		global $current_user;

		$product_args 	= array(
			'post_type' 		=> 'product',
			'posts_per_page' 	=> '-1',
		);
		if ( 'own' == get_option( 'wpjmp_products_limit', 'own' ) && ! array_key_exists( 'administrator', $current_user->caps ) ) :
			$product_args['author'] = get_current_user_id();
		endif;

		$products = get_posts( apply_filters( 'wpjmp_admin_job_form_products_args', $product_args ) );

		foreach ( $products as $product ) :
			$options[ $product->ID ] = $product->post_title;
		endforeach;

		if ( class_exists( 'WC_Product_Vendors_Utils' ) ) {
			$vendor_products = WC_Product_Vendors_Utils::get_vendor_product_ids();

			foreach ( $vendor_products as $vproduct ) {
				$options[ $vproduct ] = get_the_title( $vproduct );
			}
		}

		if ( empty( $options ) ) :
			return $fields;
		endif;

		$fields['_event_products'] = array(
			'label' 		=> get_option( 'wpjmp_select_products_text' ),
			'placeholder'	=> '',
			'type'			=> 'multiselect',
			'options'		=> $options,
			'required'    => false,
		);

		return $fields;

	}


	/**
	 * Product field.
	 *
	 * Add a product field to the admin area with the chosen products.
	 *
	 * @since 1.0.0
	 *
	 * @param 	array $fields 	List of settingsfields.
	 * @return	array			Modified list of settingsfields.
	 */
	public function add_listing_data_fields_product( $fields ) {
		
		global $current_user;

		$product_args 	= array(
			'post_type' 		=> 'product',
			'posts_per_page' 	=> '-1',
		);
		if ( 'own' == get_option( 'wpjmp_products_limit', 'own' ) && ! array_key_exists( 'administrator', $current_user->caps ) ) :
			$product_args['author'] = get_current_user_id();
		endif;

		$products = get_posts( apply_filters( 'wpjmp_admin_job_form_products_args', $product_args ) );

		foreach ( $products as $product ) :
			$options[ $product->ID ] = $product->post_title;
		endforeach;

		if ( class_exists( 'WC_Product_Vendors_Utils' ) ) {
			$vendor_products = WC_Product_Vendors_Utils::get_vendor_product_ids();

			foreach ( $vendor_products as $vproduct ) {
				$options[ $vproduct ] = get_the_title( $vproduct );
			}
		}

		if ( empty( $options ) ) :
			return $fields;
		endif;

		$fields['_products'] = array(
			'label' 		=> get_option( 'wpjmp_select_products_text' ),
			'placeholder'	=> '',
			'type'			=> 'multiselect',
			'options'		=> $options,
		);

		return $fields;

	}


	/**
	 * Save products.
	 *
	 * Update the meta when its empty (not done by WP JM by default.
	 * (admin)
	 *
	 * @since 1.0.0
	 */
	public function save_job_listing_data( $post_id, $post ) {

		if ( ! isset( $_POST['_products'] ) ) :
			update_post_meta( $post_id, '_products', '' );
		endif;

	}


	/**
	 * Listing products.
	 *
	 * Display the chosen products on the listing page.
	 * Uses the default WC template to display the products.
	 *
	 * @since 1.0.0
	 */
	public function listing_display_products() {

		global $post;

		$products = get_post_meta( $post->ID, '_products', true );

		// Stop if there are no products
		if ( ! $products || ! is_array( $products ) ) :
			return;
		endif;

		$args = apply_filters( 'woocommerce_related_products_args', array(
			'post_type'            => 'product',
			'ignore_sticky_posts'  => 1,
			'no_found_rows'        => 1,
			'posts_per_page'       => -1,
			'post__in'             => $products,
		) );

		$products = new WP_Query( $args );

		if ( $products->have_posts() ) : ?>

			<div class="listing products woocommerce">

				<h2><?php echo get_option( 'wpjmp_listing_products_text' ); ?></h2>

				<?php woocommerce_product_loop_start();

					while ( $products->have_posts() ) : $products->the_post();

						wc_get_template_part( 'content', 'product' );

					endwhile;

				woocommerce_product_loop_end(); ?>

			</div>

		<?php endif;

		wp_reset_postdata();

	}


}
