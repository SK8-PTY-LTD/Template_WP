<?php

class BookYourTravel_Theme_Filters extends BookYourTravel_BaseSingleton {
	
	protected function __construct() {
	
        // our parent class might contain shared code in its constructor
        parent::__construct();
		
    }
	
    public function init() {
		add_filter( 'wp_title', array($this, 'custom_wp_title'), 10, 2 );
		add_filter('wp_dropdown_users', array( $this, 'custom_switch_post_author' ) );
		add_filter('megamenu_themes', array( $this, 'bookyourtravel_mega_menu_theme') );		
	}
		
	function bookyourtravel_mega_menu_theme($themes) {
		$themes["bookyourtravel_1458206826"] = array(
			'title' => 'BookYourTravel',
			'container_background_from' => 'rgb(193, 182, 174)',
			'container_background_to' => 'rgb(186, 172, 163)',
			'arrow_up' => 'disabled',
			'arrow_down' => 'disabled',
			'arrow_left' => 'dash-f341',
			'arrow_right' => 'dash-f345',
			'menu_item_background_from' => 'rgba(0,0,0,0)',
			'menu_item_background_to' => 'rgba(0,0,0,0)',
			'menu_item_background_hover_from' => 'rgba(51, 51, 51, 0)',
			'menu_item_background_hover_to' => 'rgba(51, 51, 51, 0)',
			'menu_item_link_font_size' => '12px',
			'menu_item_link_weight' => 'bold',
			'menu_item_link_text_transform' => 'uppercase',
			'menu_item_link_color_hover' => 'rgb(63, 63, 63)',
			'menu_item_link_weight_hover' => 'bold',
			'menu_item_link_padding_left' => '0px',
			'menu_item_link_padding_right' => '20px',
			'menu_item_highlight_current' => 'on',
			'panel_background_from' => 'rgb(193, 182, 174)',
			'panel_background_to' => 'rgb(186, 172, 163)',
			'panel_header_font_size' => '12px',
			'panel_header_border_color' => '#555',
			'panel_font_size' => '12px',
			'panel_font_color' => '#666',
			'panel_font_family' => 'inherit',
			'panel_second_level_font_color' => 'rgb(255, 255, 255)',
			'panel_second_level_font_color_hover' => 'rgb(63, 63, 63)',
			'panel_second_level_text_transform' => 'uppercase',
			'panel_second_level_font' => 'inherit',
			'panel_second_level_font_size' => '12px',
			'panel_second_level_font_weight' => 'bold',
			'panel_second_level_font_weight_hover' => 'bold',
			'panel_second_level_text_decoration' => 'none',
			'panel_second_level_text_decoration_hover' => 'none',
			'panel_second_level_background_hover_from' => 'rgba(0,0,0,0)',
			'panel_second_level_background_hover_to' => 'rgba(0,0,0,0)',
			'panel_second_level_border_color' => '#555',
			'panel_third_level_font_color' => 'rgb(255, 255, 255)',
			'panel_third_level_font_color_hover' => 'rgb(63, 63, 63)',
			'panel_third_level_text_transform' => 'uppercase',
			'panel_third_level_font' => 'inherit',
			'panel_third_level_font_size' => '12px',
			'panel_third_level_font_weight' => 'bold',
			'panel_third_level_font_weight_hover' => 'bold',
			'panel_third_level_background_hover_from' => 'rgba(0,0,0,0)',
			'panel_third_level_background_hover_to' => 'rgba(0,0,0,0)',
			'flyout_width' => '200px',
			'flyout_menu_background_from' => 'rgb(186, 172, 163)',
			'flyout_menu_background_to' => 'rgb(186, 172, 163)',
			'flyout_padding_bottom' => '15px',
			'flyout_link_padding_left' => '12px',
			'flyout_link_padding_right' => '12px',
			'flyout_link_padding_top' => '15px',
			'flyout_link_padding_bottom' => '0',
			'flyout_link_weight' => 'bold',
			'flyout_link_weight_hover' => 'bold',
			'flyout_link_height' => '15px',
			'flyout_background_from' => 'rgba(241, 241, 241, 0)',
			'flyout_background_to' => 'rgba(241, 241, 241, 0)',
			'flyout_background_hover_from' => 'rgba(221, 221, 221, 0)',
			'flyout_background_hover_to' => 'rgba(221, 221, 221, 0.01)',
			'flyout_link_size' => '12px',
			'flyout_link_color' => 'rgb(255, 255, 255)',
			'flyout_link_color_hover' => 'rgb(63, 63, 63)',
			'flyout_link_family' => 'inherit',
			'flyout_link_text_transform' => 'uppercase',
			'responsive_breakpoint' => '1040px',
			'line_height' => '1.5',
			'shadow_blur' => '2px',
			'shadow_color' => 'rgba(0, 0, 0, 0.2)',
			'toggle_background_from' => 'rgb(193, 182, 174)',
			'toggle_background_to' => 'rgb(186, 172, 163)',
			'toggle_font_color' => '#ffffff',
			'custom_css' => '#{$wrap} #{$menu} {
		/** Custom styles should be added below this line **/
	}
	#{$wrap} {
		clear: both;
	}

	li.mega-menu-item {letter-spacing:-1px;text-shadow:0 0 1px rgba(0,0,0,0.3);}

	#mega-menu-wrap-primary-menu #mega-menu-primary-menu > li.mega-menu-item.mega-toggle-on > a.mega-menu-link, #mega-menu-wrap-primary-menu #mega-menu-primary-menu > li.mega-menu-item > a.mega-menu-link:hover, #mega-menu-wrap-primary-menu #mega-menu-primary-menu > li.mega-menu-item > a.mega-menu-link:focus,
	#mega-menu-wrap-primary-menu #mega-menu-primary-menu > li.mega-menu-item.mega-current-menu-item > a.mega-menu-link, #mega-menu-wrap-primary-menu #mega-menu-primary-menu > li.mega-menu-item.mega-current-menu-ancestor > a.mega-menu-link,
	#mega-menu-wrap-primary-menu #mega-menu-primary-menu > li.mega-menu-flyout ul.mega-sub-menu li.mega-menu-item a.mega-menu-link:hover, #mega-menu-wrap-primary-menu #mega-menu-primary-menu > li.mega-menu-flyout ul.mega-sub-menu li.mega-menu-item a.mega-menu-link:focus {text-shadow:0 1px 0 rgba(255,255,255,0.15);}

	#{$wrap} {-webkit-box-shadow:0 0 2px rgba(0,0,0,0.2);-moz-box-shadow:0 0 2px rgba(0,0,0,0.2);box-shadow:0 0 2px rgba(0,0,0,0.2);}

	.mega-menu-toggle {padding:0 15px;}
	',
		);
		return $themes;
	}
	
	function custom_switch_post_author($output)
	{
		global $post;
		
		if (isset($post)) {
			//global $post is available here, hence you can check for the post type here
			$users = array();
			$roles = array('byt_frontend_contributor', 'administrator');

			foreach ($roles as $role) :
				$users_query = new WP_User_Query( array( 
					'fields' => 'all_with_meta', 
					'role' => $role, 
					'orderby' => 'display_name'
					) );
				$results = $users_query->get_results();
				if ($results) 
				$users = array_merge($users, $results);
			endforeach;

			$output = "<select id=\"post_author_override\" name=\"post_author_override\" class=\"\">";

			//Leave the admin in the list
			foreach($users as $user)
			{
				$sel = ($post->post_author == $user->ID)?"selected='selected'":'';
				$output .= '<option value="'.$user->ID.'"'.$sel.'>'.$user->user_login.'</option>';
			}
			$output .= "</select>";
		}

		return $output;
	}
	
	function custom_wp_title( $title, $sep ) {
		if ( is_feed() ) {
			return $title;
		}

		global $page, $paged;

		// Add the blog name
		$blog_name = get_bloginfo( 'name', 'display' );

		// Add the blog description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) ) {
			$blog_name .= " $sep $site_description";
		}
		
		$title = $blog_name . " " . $title;

		// Add a page number if necessary:
		if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
			$title .= " $sep " . sprintf( esc_html__( 'Page %s', 'bookyourtravel' ), max( $paged, $page ) );
		}

		return $title;
	}


}

// store the instance in a variable to be retrieved later and call init
$bookyourtravel_theme_filters = BookYourTravel_Theme_Filters::get_instance();
$bookyourtravel_theme_filters->init();