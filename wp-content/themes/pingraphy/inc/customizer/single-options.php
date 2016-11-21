<?php

	
	/**
	|------------------------------------------------------------------------------
	| OPTIONS
	|------------------------------------------------------------------------------
	*/
	$wp_customize->add_panel( 'tc_panel_single', array(
			'priority' 				=> 33,
			'capability' 			=> 'edit_theme_options',
			'theme_supports'		=> '',
			'title' 				=> __( 'Single Options', 'pingraphy' )
		));

		

	   /**************************
		* Section: Related Posts *
		**************************/
		$wp_customize->add_section( 'pingraphy_single_related_post_section' , array(
				'title'       		=> __( 'Related Posts', 'pingraphy' ),
				'priority'    		=> 5,
				'panel' 			=> 'tc_panel_single'
		));

		/* Related Post Taxonmy */
		$wp_customize->add_setting('pingraphy_single_related_post_taxonomy', array(
	        'default'        		=> 'category',
	        'sanitize_callback' => 'pingraphy_sanitize_text'
  	  	));
 
	    $wp_customize->add_control('pingraphy_single_related_post_taxonomy', array(
	        'label'      			=> __('Related Posts Taxonomy', 'pingraphy'),
	        'section'    			=> 'pingraphy_single_related_post_section',
	        'type'       			=> 'radio',
	        'priority'				=> 2,
	        'choices'    			=> array(
	            'category' 				=> __('Categories', 'pingraphy'),
	            'tag' 					=> __('Tags', 'pingraphy'),
	        ),
	    ));

		
