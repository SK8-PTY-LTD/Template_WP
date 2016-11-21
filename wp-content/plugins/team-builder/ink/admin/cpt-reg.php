<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$labels = array(
				'name'                => _x( 'Team Builder', 'Team Builder', wpshopmart_team_b_text_domain ),
				'singular_name'       => _x( 'Team Builder', 'Team Builder', wpshopmart_team_b_text_domain ),
				'menu_name'           => __( 'Team Builder', wpshopmart_team_b_text_domain ),
				'parent_item_colon'   => __( 'Parent Item:', wpshopmart_team_b_text_domain ),
				'all_items'           => __( 'All Teams', wpshopmart_team_b_text_domain ),
				'view_item'           => __( 'View Teams', wpshopmart_team_b_text_domain ),
				'add_new_item'        => __( 'Add New Teams', wpshopmart_team_b_text_domain ),
				'add_new'             => __( 'Add New Teams', wpshopmart_team_b_text_domain ),
				'edit_item'           => __( 'Edit Teams', wpshopmart_team_b_text_domain ),
				'update_item'         => __( 'Update Teams', wpshopmart_team_b_text_domain ),
				'search_items'        => __( 'Search Teams', wpshopmart_team_b_text_domain ),
				'not_found'           => __( 'No Teams Found', wpshopmart_team_b_text_domain ),
				'not_found_in_trash'  => __( 'No Teams found in Trash', wpshopmart_team_b_text_domain ),
			);
			$args = array(
				'label'               => __( 'Team Builder', wpshopmart_team_b_text_domain ),
				'description'         => __( 'Team Builder', wpshopmart_team_b_text_domain ),
				'labels'              => $labels,
				'supports'            => array( 'title', '', '', '', '', '', '', '', '', '', '', ),
				//'taxonomies'          => array( 'category', 'post_tag' ),
				 'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => false,
				'show_in_admin_bar'   => false,
				'menu_position'       => 5,
				'menu_icon'           => 'dashicons-groups',
				'can_export'          => true,
				'has_archive'         => true,
				'exclude_from_search' => false,
				'publicly_queryable'  => false,
				'capability_type'     => 'page',
			);
			register_post_type( 'team_builder', $args );
			
 ?>