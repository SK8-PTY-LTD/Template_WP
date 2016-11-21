<?php
/**
 * Pingraphy Theme Customizer
 *
 * @package Pingraphy
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */


function pingraphy_customize_register( $wp_customize ) {

	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	/**
	|------------------------------------------------------------------------------
	| GENERAL OPTIONS
	|------------------------------------------------------------------------------
	*/
	if ( class_exists( 'WP_Customize_Panel' ) ):

		/* LOGO	*/
		$wp_customize->add_setting( 'pingraphy_logo', array('sanitize_callback' => 'esc_url_raw'));
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'themeslug_logo', array(
				'label'    => __( 'Site Logo', 'pingraphy' ),
				'section'  => 'title_tagline',
				'settings' => 'pingraphy_logo',
				'priority'    => 1,
		)));


		/**
		|-------------------------------------------------------------------------------
		| Panel: General Options
		|-------------------------------------------------------------------------------
		|
		*/

		require_once get_template_directory() . '/inc/customizer/general-options.php';

		/**
		|-------------------------------------------------------------------------------
		| Panel: Style Options
		|-------------------------------------------------------------------------------
		|
		*/
		require_once get_template_directory() . '/inc/customizer/style-color-options.php';

		/**
		|-------------------------------------------------------------------------------
		| Panel: Single Options
		|-------------------------------------------------------------------------------
		|
		*/
		require_once get_template_directory() . '/inc/customizer/single-options.php';

		


	endif;
}
add_action( 'customize_register', 'pingraphy_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function pingraphy_customize_preview_js() {
	wp_enqueue_script( 'pingraphy_customizer_script', get_template_directory_uri() . '/js/customizer.js', array( 'jquery'), '20151108', true );

	wp_localize_script( 'pingraphy_customizer_script', 'pingraphyCustomizerObject', array(
		
		'documentation' => __( 'View Documentation', 'pingraphy' ),
		'pro' => __('Upgrade to PRO','pingraphy'),

	) );
}

//add_action( 'customize_preview_init', 'pingraphy_customize_preview_js' );
add_action( 'customize_controls_enqueue_scripts', 'pingraphy_customize_preview_js' );

/**
|------------------------------------------------------------------------------
| Callback Functions
|------------------------------------------------------------------------------
*/
function pingraphy_sanitize_text( $input ) {
    return wp_kses_post( force_balance_tags( $input ) );
}

/**
 * Sanitize checkbox values
 * @since 1.0.0
 */
function pingraphy_sanitize_checkbox( $input ) {
	if ( $input ) {
		$output = '1';
	} else {
		$output = false;
	}
	return $output;
}

/* Pro Version Sanitize */
function pingraphy_sanitize_pro_version( $input ) {
    return $input;
}