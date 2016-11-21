<?php
/**
|------------------------------------------------------------------------------
| Static Control
|------------------------------------------------------------------------------
*/

class Pingraphy_Theme_Info extends WP_Customize_Control
	{
		public function render_content()
		{
			echo $this->description;
		}
	}
/**
|------------------------------------------------------------------------------
| OPTIONS
|------------------------------------------------------------------------------
*/

		$wp_customize->add_panel( 'panel_general', array(
			'priority' => 30,
			'capability' => 'edit_theme_options',
			'theme_supports' => '',
			'title' => __( 'General Options', 'pingraphy' )
		));
		
		
		/*******************
		* Section: Excerpt *
		********************/
		$wp_customize->add_section( 'pingraphy_general_excerpt_section' , array(
				'title'       		=> __( 'Excerpt', 'pingraphy' ),
				'priority'    		=> 2,
				'panel' 			=> 'panel_general'
		));

		/* Excerpt Length */
		$wp_customize->add_setting('pingraphy_general_excerpt_lengh', array('sanitize_callback' => 'pingraphy_sanitize_text', 'default' => 34));
		$wp_customize->add_control( 'pingraphy_general_excerpt_lengh', array(
		  	'type' 					=> 'number',
		  	'section' 				=> 'pingraphy_general_excerpt_section',
		  	'label' 				=> __( 'Excerpt Length', 'pingraphy' ),
		  	'description' 			=> __( 'Number of word as Expert Length To be Shown in Home/Archive pages when you choose to show entry text as Excerpt.', 'pingraphy'),
		));

		/* Excerpt End Text */
		$wp_customize->add_setting('pingraphy_general_excerpt_end_text', array('sanitize_callback' => 'pingraphy_sanitize_text', 'default' => '...'));
		$wp_customize->add_control( 'pingraphy_general_excerpt_end_text', array(
		  	'type' 					=> 'text',
		  	'section' 				=> 'pingraphy_general_excerpt_section',
		  	'label' 				=> __( 'Excerpt Length', 'pingraphy' ),
		));

		/***************************
		* Section: Post Meta Info *
		***************************/
		$wp_customize->add_section( 'pingraphy_single_post_meta_info_section' , array(
				'title'       		=> __( 'Post Meta Info', 'pingraphy' ),
				'priority'    		=> 3,
				'panel' 			=> 'panel_general'
		));
		
		/**********************
		* Section: Pagination *
		***********************/
		$wp_customize->add_section( 'pingraphy_general_pagination_section' , array(
			'title'       			=> __( 'Pagination Mode', 'pingraphy' ),
			'priority'    			=> 4,
			'panel' 				=> 'panel_general'
		));

		$wp_customize->add_setting('pingraphy_general_pagination_mode', array(
        	'default'        	=> 'default',
        	'capability'     	=> 'edit_theme_options',
        	'sanitize_callback' => 'pingraphy_sanitize_text',
	    ));
	    $wp_customize->add_control( 'pingraphy_general_pagination_mode', array(
	        'label'   			=> 'Choose Pagination Type',
	        'section' 			=> 'pingraphy_general_pagination_section',
	        'type'    			=> 'radio',
	        'priority'			=> 1,
	        'choices'    		=> array(
	            'default' 				=> __('Default (Older Posts/Newer Posts)', 'pingraphy'),
	            'numberal' 				=> __('Numberal (1 2 3 ..)', 'pingraphy'),
	        ),
	    ));
		
		/******************
		* Section: Footer *
		*******************/
		$wp_customize->add_section( 'pingraphy_general_footer_section' , array(
				'title'       => __( 'Footer', 'pingraphy' ),
				'priority'    => 6,
				'panel' => 'panel_general'
		));


		/* COPYRIGHT */
		$wp_customize->add_setting( 'pingraphy_copyright', array('sanitize_callback' => 'pingraphy_sanitize_text'));
		$wp_customize->add_control( 'pingraphy_copyright', array(
				'label'    => __( 'Copyright', 'pingraphy' ),
				'section'  => 'pingraphy_general_footer_section',
				'settings' => 'pingraphy_copyright',
				'priority'    => 3,
		));