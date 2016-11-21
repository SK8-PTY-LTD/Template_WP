<?php
	  $wp_customize->add_panel( 'panel_styles_colors', array(
			'priority' 			=> 31,
			'capability' 		=> 'edit_theme_options',
			'theme_supports' 	=> '',
			'title' 			=> __( 'Style & Color Options', 'pingraphy' )
		));

		/************************
		* Section: Header Color *
		*************************/
		$wp_customize->add_section( 'pingraphy_header_color_section' , array(
				'title'      	 	=> __( 'Header Color', 'pingraphy' ),
				'priority'    		=> 1,
				'panel' 			=> 'panel_styles_colors'
		));

		/* Header Bg Color*/
		$wp_customize->add_setting( 'pingraphy_header_color' , array(
		    'default' 				=> '#ffffff',
		    'sanitize_callback' 	=> 'sanitize_hex_color',
		));
		$wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'pingraphy_header_color', array(
		    'label'    				=> __( 'Header Background Color', 'pingraphy' ),
		    'section'  				=> 'pingraphy_header_color_section',
		    'settings' 				=> 'pingraphy_header_color',
		    'priority'    			=> 1,
		)));

		/* Header Icon Color*/
		$wp_customize->add_setting( 'pingraphy_header_icon_color' , array(
		    'default' 				=> '#737373',
		    'sanitize_callback' 	=> 'sanitize_hex_color',
		));
		$wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'pingraphy_header_icon_color', array(
		    'label'    				=> __( 'Header Icon Color', 'pingraphy' ),
		    'section'  				=> 'pingraphy_header_color_section',
		    'settings' 				=> 'pingraphy_header_icon_color',
		    'priority'    			=> 2,
		)));

		/* Header Logo/Slogan Text color */
		$wp_customize->add_setting( 'pingraphy_header_logo_text_color' , array(
		    'default' 				=> '#ff6565',
		    'sanitize_callback' 	=> 'sanitize_hex_color',
		));
		$wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'pingraphy_header_logo_text_color', array(
		    'label'    				=> __( 'Logo/Slogan Text Color', 'pingraphy' ),
		    'section'  				=> 'pingraphy_header_color_section',
		    'settings' 				=> 'pingraphy_header_logo_text_color',
		    'priority'    			=> 3,
		)));

		
		

		/**********************
		* Section: Link Color *
		***********************/
		$wp_customize->add_section( 'pingraphy_anchor_text_color_section' , array(
				'title'       	=> __( 'Anchor Text Color (Color Links)', 'pingraphy' ),
				'priority'    	=> 2,
				'panel' 		=> 'panel_styles_colors'
		));

		/* Anchor Text Color*/
		$wp_customize->add_setting( 'pingraphy_anchor_text_color' , array(
		    'default' 			=> '#737373',
		    'sanitize_callback' => 'sanitize_hex_color',
		));
		$wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'pingraphy_anchor_text_color', array(
		    'label'    			=> __( 'Anchor Text Color', 'pingraphy' ),
		    'section'  			=> 'pingraphy_anchor_text_color_section',
		    'settings' 			=> 'pingraphy_anchor_text_color',
		    'priority'    		=> 3,
		)));

		/* Anchor Text Color Hover (Color Links Hover) */
		$wp_customize->add_setting( 'pingraphy_anchor_text_color_hover' , array(
		    'default' 			=> '#ff6565',
		    'sanitize_callback' => 'sanitize_hex_color',
		));
		$wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'pingraphy_anchor_text_color_hover', array(
		    'label'    			=> __( 'Anchor Text Color Hover (Color Links Hover)', 'pingraphy' ),
		    'section'  			=> 'pingraphy_anchor_text_color_section',
		    'settings' 			=> 'pingraphy_anchor_text_color_hover',
		    'priority'    		=> 4,
		)));
