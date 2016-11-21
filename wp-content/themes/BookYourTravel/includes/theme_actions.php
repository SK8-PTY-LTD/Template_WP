<?php

// Loads the required Options Framework classes.
require_once plugin_dir_path( __FILE__ ) . '/framework/includes/class-options-framework.php';
require_once plugin_dir_path( __FILE__ ) . '/framework/includes/class-options-framework-admin.php';
require_once plugin_dir_path( __FILE__ ) . '/framework/includes/class-options-interface.php';
require_once plugin_dir_path( __FILE__ ) . '/framework/includes/class-options-media-uploader.php';
require_once plugin_dir_path( __FILE__ ) . '/framework/includes/class-options-sanitization.php';
require_once plugin_dir_path( __FILE__ ) . '/framework/includes/class-options-importer.php';

class BookYourTravel_Theme_Actions extends BookYourTravel_BaseSingleton {
	
	protected function __construct() {
	
        // our parent class might contain shared code in its constructor
        parent::__construct();
		
    }

    public function init() {
	
		add_action( 'after_setup_theme', array( $this, 'setup' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts_styles' ) );
		
		$bookyourtravel_needs_update = get_option('_byt_needs_update', 0);
		if ($bookyourtravel_needs_update) {	
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		}	

		add_action( 'register_form', array( $this, 'password_register_fields'), 10, 1 );
		add_action( 'login_form_login', array( $this, 'disable_wp_login') );
		
		add_action( 'after_setup_theme', array( $this, 'check_pre_requisites') );
		
		add_action('updated_option', array( $this, 'updated_options_check'), 10, 3);
		add_action('added_option', array( $this, 'added_options_check'), 10, 2); 
	}
	
	// When options are saved or added, make sure you force recreate tables. This makes sure our custom tables are created and up to date.
	function updated_options_check( $option_name, $old_value, $value ) {
	
		$options_framework = new Options_Framework;
		$theme_option_name = $options_framework->get_option_name();

		if ($option_name == $theme_option_name) {
			global $force_recreate_tables;
			$force_recreate_tables = true;
			do_action('bookyourtravel_initialize_post_types');
			$force_recreate_tables = false;
		}
	}
	
	function added_options_check( $option_name, $option_value ) {
	
		$options_framework = new Options_Framework;
		$theme_option_name = $options_framework->get_option_name();
		
		if ($option_name == $theme_option_name) {
			global $force_recreate_tables;
			$force_recreate_tables = true;
			do_action('bookyourtravel_initialize_post_types');
			$force_recreate_tables = false;
		}	
	}
	
	function get_google_fonts_icon_uri() {
	
		$fonts_url = '';
	 
		/* Translators: If there are characters in your language that are not
		* supported by Material Icons, translate this to 'off'. Do not translate
		* into your own language.
		*/
		$material_icons = esc_html__( 'on', 'bookyourtravel' );

		if ( 'off' !== $material_icons ) {
		
			$font_families = array();
	 
			if ( 'off' !== $material_icons ) {
				$font_families[] = 'Material+Icons';
			}
	 
			$query_args = array(
				'family' => implode( '|', $font_families ),
			);
	 
			$fonts_url = add_query_arg( $query_args, '//fonts.googleapis.com/icon' );
		}
	 
		return esc_url_raw( $fonts_url );	
	}
	
	function get_google_fonts_css_uri() {
	
		$fonts_url = '';
	 
		/* Translators: If there are characters in your language that are not
		* supported by Roboto Slab, translate this to 'off'. Do not translate
		* into your own language.
		*/
		$roboto_slab = esc_html__( 'on', 'bookyourtravel' );

		/* Translators: If there are characters in your language that are not
		* supported by Open Sans, translate this to 'off'. Do not translate
		* into your own language.
		*/		
		$open_sans = esc_html__( 'on', 'bookyourtravel' );

		if ( 'off' !== $roboto_slab || 'off' !== $open_sans ) {
		
			$font_families = array();
	 
			if ( 'off' !== $roboto_slab ) {
				$font_families[] = 'Roboto+Slab:400,700';
			}
			
			if ( 'off' !== $open_sans ) {
				$font_families[] = 'Open+Sans:400,500,600,700';
			}
	 
			$query_args = array(
				'family' => implode( '|', $font_families ),
				'subset' => 'latin,cyrillic,latin-ext,vietnamese,greek,greek-ext,cyrillic-ext',
			);
	 
			$fonts_url = add_query_arg( $query_args, '//fonts.googleapis.com/css' );
		}
	 
		return esc_url_raw( $fonts_url );	
	}
	
	function check_pre_requisites() {

		if ( !BookYourTravel_Theme_Utils::php_version_recent_enough() ) {
		
			echo sprintf(esc_html__('Theme BookYourTravel requires a minimum PHP version 5.3 to function properly. Please ask your web host to upgrade your php configuration to at least this version. Your server is currently running PHP version %s', 'bookyourtravel'), phpversion());
			die();
		}
	}

	/**
	 * Add password fields to wordpress registration form if option for users to set their own password is enabled in Theme settings.
	 */
	function password_register_fields() {

		global $bookyourtravel_theme_globals;
		
		$let_users_set_pass = $bookyourtravel_theme_globals->let_users_set_pass();
			
		if ($let_users_set_pass) {
	?>
		<div class="f-item one-half">
			<label for="password"><?php esc_html_e('Password', 'bookyourtravel'); ?></label>
			<input id="password" class="input" type="password" tabindex="30" size="25" value="" name="password" />
		</div>
		<div class="f-item one-half">
			<label for="repeat_password"><?php esc_html_e('Repeat password', 'bookyourtravel'); ?></label>
			<input id="repeat_password" class="input" type="password" tabindex="40" size="25" value="" name="repeat_password" />
		</div>
	<?php
		}
	}

	/**
	 * Disable WP login if option enabled in Theme settings
	 */
	function disable_wp_login() {
	
		global $bookyourtravel_theme_globals;
		
		$permalinks_enabled = $bookyourtravel_theme_globals->permalinks_enabled();
		
		if ($permalinks_enabled) {

			$login_page_url = $bookyourtravel_theme_globals->get_login_page_url();
			$override_wp_login = $bookyourtravel_theme_globals->override_wp_login();
			$redirect_to_after_logout_url = $bookyourtravel_theme_globals->get_redirect_to_after_logout_url();
			
			if ($override_wp_login) {				
				if (!empty($login_page_url) && !empty($redirect_to_after_logout_url)) {
					if( isset( $_GET['loggedout'] ) ) {
						wp_redirect( $redirect_to_after_logout_url );
					} else{
						wp_redirect( $login_page_url );
					}
				}
			}
		}
	}
	
	function admin_notices() {
	
		if (is_super_admin()) {
			$screen = get_current_screen();
		
			if ($screen->id != 'appearance_page_options-framework') {
				$bookyourtravel_version_before_update = get_option('_byt_version_before_update', 0);
				global $bookyourtravel_installed_version;
			?>
			<div id="message" class="updated">
				<p><strong><?php esc_html_e( 'Your Book Your Travel database needs an upgrade!', 'bookyourtravel'); ?></strong></p>
				<p><?php echo sprintf(__('Your current database version is <strong>%s</strong>, while the current theme version is <strong>%s</strong>.', 'bookyourtravel'), $bookyourtravel_version_before_update, $bookyourtravel_installed_version); ?></p>
				<p><?php esc_html_e( 'Please click the button below to go to the upgrade screen.', 'bookyourtravel' ); ?></p>
				<p class="submit"><a href="<?php echo esc_url( admin_url( 'themes.php?page=options-framework#options-group-14' ) ); ?>" class="button-primary"><?php esc_html_e( 'Go To Upgrade Screen', 'woocommerce' ); ?></a></p>
			</div>
			<?php
			}
		}
	}
		
	 /**
	 * Sets up theme defaults and registers the various WordPress features that
	 * Book Your Travel supports.
	 *
	 * @uses load_theme_textdomain() For translation/localization support.
	 * @uses add_theme_support() To add support for post thumbnails, automatic feed links,
	 * 	custom background, and post formats.
	 * @uses register_nav_menu() To add support for navigation menus.
	 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
	 *
	 * @since Book Your Travel 1.0
	 */
	function setup() {
		/*
		 * Book Your Travel available for translation.
		 *
		 * Translations can be added to the /languages/ directory.
		 * If you're building a theme based on Book Your Travel, use a find and replace
		 * to change 'bookyourtravel' to the name of your theme in all the template files.
		 */

		load_theme_textdomain( 'bookyourtravel', get_template_directory() . '/languages' );	
		
		// This theme uses wp_nav_menu() in three locations.
		register_nav_menus( array(
			'primary-menu' => esc_html__( 'Primary Menu', 'bookyourtravel' ),
			'footer-menu' => esc_html__( 'Footer Menu', 'bookyourtravel' ),
			'user-account-menu' => esc_html__( 'User Account Menu', 'bookyourtravel' ),
			'partner-account-menu' => esc_html__( 'Partner Account Menu', 'bookyourtravel' )			
		) );	
		
		// This theme uses a custom image size for featured images, displayed on "standard" posts.
		add_theme_support( 'post-thumbnails' );
		
		// This theme is woocommerce compatible
		add_theme_support( 'woocommerce' );
		
		add_theme_support( 'automatic-feed-links' );
		
		if ( ! isset( $content_width ) ) {
			$content_width = 850;
		}
		
		set_post_thumbnail_size( 200, 200, true );
		add_image_size( 'related', 180, 120, true ); //related
		add_image_size( 'featured', 850, 459, true ); //Featured
				
		//Left Sidebar Widget area
		register_sidebar(array(
			'name'=> esc_html__('Left Sidebar', 'bookyourtravel'),
			'id'=>'left',
			'description' => esc_html__('This Widget area is used for the left sidebar', 'bookyourtravel'),
			'before_widget' => '<li class="widget widget-sidebar">',
			'after_widget' => '</li>',
			'before_title' => '<h4>',
			'after_title' => '</h4>',
		));
		
		// Right Sidebar Widget area
		register_sidebar(array(
			'name'=> esc_html__('Right Sidebar', 'bookyourtravel'),
			'id'=>'right',
			'description' => esc_html__('This Widget area is used for the right sidebar', 'bookyourtravel'),
			'before_widget' => '<li class="widget widget-sidebar">',
			'after_widget' => '</li>',
			'before_title' => '<h4>',
			'after_title' => '</h4>',
		));
		
		// Right Sidebar Widget area
		register_sidebar(array(
			'name'=> esc_html__('Right Accommodation Sidebar', 'bookyourtravel'),
			'id'=>'right-accommodation',
			'description' => esc_html__('This Widget area is used for the right sidebar for single accommodations', 'bookyourtravel'),
			'before_widget' => '<li class="widget widget-sidebar">',
			'after_widget' => '</li>',
			'before_title' => '<h4>',
			'after_title' => '</h4>',
		));
		
		// Right Sidebar Widget area
		register_sidebar(array(
			'name'=> esc_html__('Right Tour Sidebar', 'bookyourtravel'),
			'id'=>'right-tour',
			'description' => esc_html__('This Widget area is used for the right sidebar for single tours', 'bookyourtravel'),
			'before_widget' => '<li class="widget widget-sidebar">',
			'after_widget' => '</li>',
			'before_title' => '<h4>',
			'after_title' => '</h4>',
		));
		
		// Right Sidebar Widget area
		register_sidebar(array(
			'name'=> esc_html__('Right Cruise Sidebar', 'bookyourtravel'),
			'id'=>'right-cruise',
			'description' => esc_html__('This Widget area is used for the right sidebar for single cruises', 'bookyourtravel'),
			'before_widget' => '<li class="widget widget-sidebar">',
			'after_widget' => '</li>',
			'before_title' => '<h4>',
			'after_title' => '</h4>',
		));
		
		// Right Sidebar Widget area
		register_sidebar(array(
			'name'=> esc_html__('Right Car Rental Sidebar', 'bookyourtravel'),
			'id'=>'right-car_rental',
			'description' => esc_html__('This Widget area is used for the right sidebar for single car rentals', 'bookyourtravel'),
			'before_widget' => '<li class="widget widget-sidebar">',
			'after_widget' => '</li>',
			'before_title' => '<h4>',
			'after_title' => '</h4>',
		));
		
		// Header Sidebar Widget area
		register_sidebar(array(
			'name'=> esc_html__('Header Sidebar', 'bookyourtravel'),
			'id'=>'header',
			'description' => esc_html__('This Widget area is used for the header area (usually for purposes of displaying WPML language switcher widget)', 'bookyourtravel'),
			'before_widget' => '',
			'after_widget' => '',
			'class'	=> 'lang-nav',
			'before_title' => '<h4>',
			'after_title' => '</h4>',
		));
		
		// Under Header Sidebar Widget area
		register_sidebar(array(
			'name'=> esc_html__('Under Header Sidebar', 'bookyourtravel'),
			'id'=>'under-header',
			'description' => esc_html__('This Widget area is placed under the website header', 'bookyourtravel'),
			'before_widget' => '<li class="widget widget-sidebar">',
			'after_widget' => '</li>',
			'before_title' => '<h4>',
			'after_title' => '</h4>',
		));
		
		// Under Header Sidebar Widget area
		register_sidebar(array(
			'name'=> esc_html__('Above Footer Sidebar', 'bookyourtravel'),
			'id'=>'above-footer',
			'description' => esc_html__('This Widget area is placed above the website footer', 'bookyourtravel'),
			'before_widget' => '<li class="widget widget-sidebar"><div>',
			'after_widget' => '</div></li>',
			'before_title' => '<h4>',
			'after_title' => '</h4>',
		));
		
		
		// Footer Sidebar Widget area
		register_sidebar(array(
			'name'=> esc_html__('Footer Sidebar', 'bookyourtravel'),
			'id'=>'footer',
			'description' => esc_html__('This Widget area is used for the footer area', 'bookyourtravel'),
			'before_widget' => '<li class="widget widget-sidebar one-fourth">',
			'after_widget' => '</li>',
			'before_title' => '<h6>',
			'after_title' => '</h6>',
		));
		
		// Home Footer Sidebar Widget area
		register_sidebar(array(
			'name'=> esc_html__('Home Footer Widget Area', 'bookyourtravel'),
			'id'=>'home-footer',
			'description' => esc_html__('This Widget area is used for the home page footer area above the regular footer', 'bookyourtravel'),
			'before_widget' => '<li class="widget widget-sidebar"><div>',
			'after_widget' => '</div></li>',
			'before_title' => '<h4>',
			'after_title' => '</h4>',
		));
		
		register_sidebar(array(
			'name'=> esc_html__('Home Content Widget Area', 'bookyourtravel'),
			'id'=>'home-content',
			'description' => esc_html__('This Widget area is used for the home page main content area', 'bookyourtravel'),
			'before_widget' => '<li class="widget widget-sidebar">',
			'after_widget' => '</li>',
			'before_title' => '<h2>',
			'after_title' => '</h2>',
		));
		
		// create new frontend submit role custom to BYT if it's not already created
		$frontend_submit_role = get_role(BOOKYOURTRAVEL_FRONTEND_SUBMIT_ROLE);
		if ($frontend_submit_role == null) {
			$frontend_submit_role = add_role(
				BOOKYOURTRAVEL_FRONTEND_SUBMIT_ROLE,
				esc_html__( 'BYT Frontend Submit Role', 'bookyourtravel' ),
				array(
					'read'         => true,  // true allows this capability
				)
			);
		}
		
		$pending_role = add_role(
			'pending',
			esc_html__( 'Pending activation', 'bookyourtravel' ),
			array()
		);
		
	}
	
	/**
	 * Enqueues scripts and styles for front-end.
	 *
	 * @since Book Your Travel 1.0
	 */
	function enqueue_scripts_styles() {
	
		global $wp_styles, $bookyourtravel_theme_globals;
		
		$language_code = $bookyourtravel_theme_globals->get_current_language_code();

		/*
		 * Adds JavaScript to pages with the comment form to support
		 * sites with threaded comments (when in use).
		 */
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
			wp_enqueue_script( 'comment-reply' );

		/*
		 * Adds JavaScript for various theme features
		 */
		 
		wp_enqueue_script('jquery');

		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-slider');
		wp_enqueue_script('jquery-ui-datepicker');
		
		if (BookYourTravel_Theme_Utils::check_file_exists('/js/i18n/datepicker-' . $language_code . '.js')) {
			wp_register_script(	'bookyourtravel-datepicker-' . $language_code, BookYourTravel_Theme_Utils::get_file_uri('/js/i18n/datepicker-' . $language_code . '.js'), array('jquery', 'jquery-ui-datepicker'), '1.0',true);
			wp_enqueue_script( 'bookyourtravel-datepicker-' . $language_code );
		}
		
		wp_enqueue_script('jquery-effects-core');
		
		wp_enqueue_script( 'bookyourtravel-jquery-validate', BookYourTravel_Theme_Utils::get_file_uri ('/js/jquery.validate.min.js'), array('jquery'), '1.0', true );
		wp_enqueue_script( 'bookyourtravel-extras-jquery-validate', BookYourTravel_Theme_Utils::get_file_uri ('/js/extras.jquery.validate.js'), array('bookyourtravel-jquery-validate'), '1.0', true );
		
		wp_enqueue_script( 'bookyourtravel-jquery-prettyPhoto', BookYourTravel_Theme_Utils::get_file_uri ('/js/jquery.prettyPhoto.js'), array('jquery'), '1.0', true );
		wp_enqueue_script( 'bookyourtravel-jquery-raty', BookYourTravel_Theme_Utils::get_file_uri ('/js/jquery.raty.min.js'), array('jquery'), '1.0', true );
		wp_enqueue_script( 'bookyourtravel-jquery-uniform', BookYourTravel_Theme_Utils::get_file_uri ('/js/jquery.uniform.min.js'), array('jquery'), '1.0', true );
		wp_enqueue_script( 'bookyourtravel-mediaqueries', BookYourTravel_Theme_Utils::get_file_uri ('/js/respond.js'), array('jquery'), '1.0', true );
		wp_enqueue_script( 'bookyourtravel-scripts', BookYourTravel_Theme_Utils::get_file_uri ('/js/scripts.js'), array('jquery', 'bookyourtravel-jquery-uniform'), BOOKYOURTRAVEL_VERSION, true );
		
		$page_object = get_queried_object();
		$page_id     = get_queried_object_id();

		if (is_single()) {
		
			wp_enqueue_script( 'bookyourtravel-jquery-lightSlider', BookYourTravel_Theme_Utils::get_file_uri ('/includes/plugins/lightSlider/js/jquery.lightSlider.js'), 'jquery', '1.0', true	);
			wp_enqueue_style( 'bookyourtravel-lightSlider-style', BookYourTravel_Theme_Utils::get_file_uri('/includes/plugins/lightSlider/css/lightSlider.css') );
		}
		
		if (is_page()) {
			$template_file = get_post_meta($page_id,'_wp_page_template',true);
			if ($template_file == 'page-user-account.php') {
				wp_enqueue_script( 'bookyourtravel-user-account', BookYourTravel_Theme_Utils::get_file_uri ('/js/account.js'), 'jquery', BOOKYOURTRAVEL_VERSION, true );
			} elseif ($template_file == 'page-user-submit-content.php' || $template_file == 'page-user-content-list.php') {
				wp_enqueue_style( 'bookyourtravel-dropzone-style', BookYourTravel_Theme_Utils::get_file_uri('/includes/plugins/frontend-submit/dropzone.min.css') );
				wp_enqueue_script( 'bookyourtravel-dropzone', BookYourTravel_Theme_Utils::get_file_uri ('/includes/plugins/frontend-submit/dropzone.min.js'), array( 'jquery', 'bookyourtravel-jquery-validate' ), '1.0', true );
				wp_enqueue_script( 'bookyourtravel-frontend-submit', BookYourTravel_Theme_Utils::get_file_uri ('/includes/plugins/frontend-submit/frontend-submit.js'), array( 'jquery', 'bookyourtravel-dropzone', 'bookyourtravel-jquery-validate' ), BOOKYOURTRAVEL_VERSION, true );
			}
		}

		$google_maps_key = $bookyourtravel_theme_globals->get_google_maps_key();		
		if (is_single() && get_post_type() == 'accommodation') {
		
			if (!empty($google_maps_key)) {
				wp_enqueue_script( 'bookyourtravel-google-maps', '//maps.google.com/maps/api/js?key=' . $google_maps_key, 'jquery', BOOKYOURTRAVEL_VERSION, true	);
				wp_enqueue_script( 'bookyourtravel-infobox', BookYourTravel_Theme_Utils::get_file_uri ('/js/infobox.js'),'jquery', '1.0', true );				
			}

			wp_enqueue_script( 'bookyourtravel-tablesorter', BookYourTravel_Theme_Utils::get_file_uri ('/js/jquery.tablesorter.min.js'), 'jquery','1.0', true );
			wp_enqueue_script( 'bookyourtravel-accommodations', BookYourTravel_Theme_Utils::get_file_uri ('/js/accommodations.js'), array('jquery', 'bookyourtravel-scripts'), BOOKYOURTRAVEL_VERSION, true );
			wp_enqueue_script( 'bookyourtravel-reviews', BookYourTravel_Theme_Utils::get_file_uri ('/js/reviews.js'), 'jquery', BOOKYOURTRAVEL_VERSION, true );
			wp_enqueue_script( 'bookyourtravel-inquiry', BookYourTravel_Theme_Utils::get_file_uri ('/js/inquiry.js'), 'jquery', BOOKYOURTRAVEL_VERSION, true );
			
		} else if (is_single() && get_post_type() == 'location') {	
			
			wp_enqueue_script( 'bookyourtravel-locations', BookYourTravel_Theme_Utils::get_file_uri ('/js/locations.js'), 'jquery', BOOKYOURTRAVEL_VERSION, true );
			
		} else if (is_single() && get_post_type() == 'tour') {

			if (!empty($google_maps_key)) {
				wp_enqueue_script( 'bookyourtravel-google-maps', '//maps.google.com/maps/api/js?key=' . $google_maps_key, 'jquery', BOOKYOURTRAVEL_VERSION, true	);
			}
			wp_enqueue_script( 'bookyourtravel-tours', BookYourTravel_Theme_Utils::get_file_uri ('/js/tours.js'),  array('jquery', 'bookyourtravel-scripts'), BOOKYOURTRAVEL_VERSION, true );
			wp_enqueue_script( 'bookyourtravel-reviews', BookYourTravel_Theme_Utils::get_file_uri ('/js/reviews.js'), 'jquery', BOOKYOURTRAVEL_VERSION, true );
			wp_enqueue_script( 'bookyourtravel-inquiry', BookYourTravel_Theme_Utils::get_file_uri ('/js/inquiry.js'), 'jquery', BOOKYOURTRAVEL_VERSION, true );
					
		} else if (is_single() && get_post_type() == 'cruise') {
			
			if (!empty($google_maps_key)) {
				wp_enqueue_script( 'bookyourtravel-google-maps', '//maps.google.com/maps/api/js?key=' . $google_maps_key, 'jquery', BOOKYOURTRAVEL_VERSION, true	);
			}
			wp_enqueue_script( 'bookyourtravel-cruises', BookYourTravel_Theme_Utils::get_file_uri ('/js/cruises.js'),  array('jquery', 'bookyourtravel-scripts'), BOOKYOURTRAVEL_VERSION, true );
			wp_enqueue_script( 'bookyourtravel-reviews', BookYourTravel_Theme_Utils::get_file_uri ('/js/reviews.js'), 'jquery', BOOKYOURTRAVEL_VERSION, true );
			wp_enqueue_script( 'bookyourtravel-inquiry', BookYourTravel_Theme_Utils::get_file_uri ('/js/inquiry.js'), 'jquery', BOOKYOURTRAVEL_VERSION, true );
			
		} else if (is_single() && get_post_type() == 'car_rental') {	
			
			wp_enqueue_script( 'bookyourtravel-tablesorter', BookYourTravel_Theme_Utils::get_file_uri ('/js/jquery.tablesorter.min.js'), 'jquery','1.0', true );
			wp_enqueue_script( 'bookyourtravel-car_rentals', BookYourTravel_Theme_Utils::get_file_uri ('/js/car_rentals.js'),  array('jquery', 'bookyourtravel-scripts'), BOOKYOURTRAVEL_VERSION, true );
			wp_enqueue_script( 'bookyourtravel-inquiry', BookYourTravel_Theme_Utils::get_file_uri ('/js/inquiry.js'), 'jquery', BOOKYOURTRAVEL_VERSION, true );
			
		}

		$ajaxurl = admin_url( 'admin-ajax.php' );
	
		global $sitepress;
		if ($sitepress) {
			$lang = $sitepress->get_current_language();
			$ajaxurl = admin_url( 'admin-ajax.php?lang=' . $lang );
		}
		
		wp_localize_script( 'bookyourtravel-scripts', 'BYTAjax', array( 
		   'ajaxurl' => $ajaxurl,
		   'nonce'   => wp_create_nonce('bookyourtravel_nonce') 
		) );

		/*
		 * Loads our main stylesheets.
		 */
		$google_fonts_css_uri = $this->get_google_fonts_css_uri();
		if (!empty($google_fonts_css_uri)) {
			wp_enqueue_style( 'bookyourtravel-font-css-style', $google_fonts_css_uri);
		}

		$google_fonts_icon_uri = $this->get_google_fonts_icon_uri();
		if (!empty($google_fonts_icon_uri)) {
			wp_enqueue_style( 'bookyourtravel-font-icon-style', $google_fonts_icon_uri);
		}
		
		wp_enqueue_style( 'bookyourtravel-font-awesome', BookYourTravel_Theme_Utils::get_file_uri('/css/font-awesome.min.css'), '1.0', "screen,print");
		
		if ($bookyourtravel_theme_globals->enable_rtl() || (defined('BYT_DEMO') && isset($_REQUEST['rtl']))) {
			wp_enqueue_style( 'bookyourtravel-style-rtl', BookYourTravel_Theme_Utils::get_file_uri('/css/style-rtl.css'), array('bookyourtravel-font-css-style', 'bookyourtravel-font-awesome'), '1.0', "screen,print");
		} else {
			wp_enqueue_style( 'bookyourtravel-style-main', BookYourTravel_Theme_Utils::get_file_uri('/css/style.css'), array('bookyourtravel-font-css-style', 'bookyourtravel-font-awesome'), '1.0', "screen,print");
		}

		$color_scheme_style_sheet = $bookyourtravel_theme_globals->get_color_scheme_style_sheet();
		if (!empty($color_scheme_style_sheet)) {
			wp_enqueue_style('bookyourtravel-style-color',  BookYourTravel_Theme_Utils::get_file_uri('/css/' . $color_scheme_style_sheet . '.css'), array(), '1.0', "screen,print");
		}

		wp_enqueue_style( 'bookyourtravel-style', get_stylesheet_uri() );
		
		wp_enqueue_style('bookyourtravel-style-pp',  BookYourTravel_Theme_Utils::get_file_uri('/css/prettyPhoto.css'), array(), '1.0', "screen");		 
	}
	
	/**
	 * Enqueues scripts and styles for admin.
	 *
	 * @since Book Your Travel 1.0
	 */
	function enqueue_admin_scripts_styles() {

		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-effects-core');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-mouse');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('jquery-ui-slider');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('jquery-ui-droppable');
		wp_enqueue_script('jquery-ui-draggable');
		wp_enqueue_script('jquery-ui-selectable');
		wp_enqueue_script('jquery-ui-autocomplete');
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_script('jquery-ui-spinner');
		
		if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'theme_accommodation_booking_admin.php') {
			wp_register_script('bookyourtravel-accommodations-bookings-admin', BookYourTravel_Theme_Utils::get_file_uri('/includes/admin/accommodation_bookings.js'), array('jquery'), '1.0.0');
			wp_enqueue_script('bookyourtravel-accommodations-bookings-admin');
		} else if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'theme_accommodation_vacancy_admin.php') {
			wp_register_script('bookyourtravel-accommodations-vacancies-admin', BookYourTravel_Theme_Utils::get_file_uri('/includes/admin/accommodation_vacancies.js'), array('jquery'), '1.0.0');
			wp_enqueue_script('bookyourtravel-accommodations-vacancies-admin');		
		} else if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'theme_tour_schedule_booking_admin.php') {
			wp_register_script('bookyourtravel-tours-bookings-admin', BookYourTravel_Theme_Utils::get_file_uri('/includes/admin/tour_bookings.js'), array('jquery'), '1.0.0');
			wp_enqueue_script('bookyourtravel-tours-bookings-admin');		
		} else if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'theme_cruise_schedule_booking_admin.php') {
			wp_register_script('bookyourtravel-cruises-bookings-admin', BookYourTravel_Theme_Utils::get_file_uri('/includes/admin/cruise_bookings.js'), array('jquery'), '1.0.0');
			wp_enqueue_script('bookyourtravel-cruises-bookings-admin');		
		} else if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'theme_car_rental_booking_admin.php') {
			wp_register_script('bookyourtravel-car_rental-bookings-admin', BookYourTravel_Theme_Utils::get_file_uri('/includes/admin/car_rental_bookings.js'), array('jquery'), '1.0.0');
			wp_enqueue_script('bookyourtravel-car_rental-bookings-admin');		
		} 

		wp_register_script('bookyourtravel-admin', BookYourTravel_Theme_Utils::get_file_uri('/includes/admin/admin.js'), array('jquery'), '1.0.0');
		wp_enqueue_script('bookyourtravel-admin');		
		
		wp_enqueue_style('bookyourtravel-admin-ui-css', BookYourTravel_Theme_Utils::get_file_uri('/css/jquery-ui.min.css'), false);
		wp_enqueue_style('bookyourtravel-admin-css', BookYourTravel_Theme_Utils::get_file_uri('/css/admin-custom.css'), false);
	}

}

// store the instance in a variable to be retrieved later and call init
$bookyourtravel_theme_actions = BookYourTravel_Theme_Actions::get_instance();
$bookyourtravel_theme_actions->init();