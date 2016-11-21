<?php
/**
 * Pingraphy functions and definitions
 * 
 * TODO: Need to remove this function after fixed error 
 * 		of ot_get_option.
 * @package Pingraphy
 */

define('PINGRAPHY_PRO_URL', 'http://themecountry.com/themes/pingraphy/');

if ( ! function_exists( 'pingraphy_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function pingraphy_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on Pingraphy, use a find and replace
	 * to change 'pingraphy' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'pingraphy', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'pingraphy-home-thumbnail', 640, 440, true);
	add_image_size( 'pingraphy-single-thumbnail', 810, 540, true );
	add_image_size( 'pingraphy-ralated-thumbnail', 170, 170, true );
	add_image_size( 'pingraphy-widget-thumbnail', 68, 68, true );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' 	=> __( 'Primary Menu', 'pingraphy' ),
		'secondary'	=> __( 'Secondary Menu', 'pingraphy' ),
		'footer' => __('Footer Menu', 'pingraphy')
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside',
		'image',
		'video',
		'quote',
		'link',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'pingraphy_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );

	// This theme styles the visual editor to resemble the theme style.
	$google_font_url = str_replace( ',', '%2C', '//fonts.googleapis.com/css?family=Fira+Sans:700,400|Roboto:700,400' );
	add_editor_style( array( 'css/editor-style.css', $google_font_url ) );
}
endif; // pingraphy_setup
add_action( 'after_setup_theme', 'pingraphy_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function pingraphy_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'pingraphy_content_width', 640 );
}
add_action( 'after_setup_theme', 'pingraphy_content_width', 0 );



/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function pingraphy_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'pingraphy' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer Widget 1', 'pingraphy' ),
		'id'            => 'footer-sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer Widget 2', 'pingraphy' ),
		'id'            => 'footer-sidebar-2',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer Widget 3', 'pingraphy' ),
		'id'            => 'footer-sidebar-3',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'pingraphy_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function pingraphy_scripts() {
		wp_enqueue_style( 'pingraphy-google-font-style', '//fonts.googleapis.com/css?family=Fira+Sans:700,400|Roboto:700,400');

	wp_enqueue_style( 'font-awesome', get_template_directory_uri() .  '/css/font-awesome.min.css');
	wp_enqueue_style( 'pingraphy-style', get_stylesheet_uri() );

	wp_enqueue_style( 'pingraphy-responsive-style', get_template_directory_uri() .  '/css/responsive.css');


	wp_enqueue_script( 'isotope', get_template_directory_uri() . '/js/isotope.pkgd.min.js', array('jquery', 'jquery-masonry'), '20160115', true);

	wp_enqueue_script( 'pingraphy-image-script', get_template_directory_uri() . '/js/imagesloaded.pkgd.min.js', array(), '20160115', true);

	
	wp_enqueue_script( 'pingraphy-custom-script', get_template_directory_uri() . '/js/script.js', array(), '20160115', true);

	wp_localize_script( 'pingraphy-custom-script', 'AdminAjaxURL', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

	wp_enqueue_script( 'pingraphy-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20160115', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

add_action( 'wp_enqueue_scripts', 'pingraphy_scripts' );


/**
|------------------------------------------------------------------------------
| Custom Tags Cloud
|------------------------------------------------------------------------------
|
*/
if( !function_exists('pingraphy_custom_tag_cloud_widget')) {
	function pingraphy_custom_tag_cloud_widget($args) {
		$args['largest'] = 12; //largest tag
		$args['smallest'] = 12; //smallest tag
		$args['unit'] = 'px'; //tag font unit
		return $args;
	}
	add_filter( 'widget_tag_cloud_args', 'pingraphy_custom_tag_cloud_widget' );
}

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Custom Style
 */
require get_template_directory() . '/inc/custom-style.php';


/** 
|------------------------------------------------------------------------------
|  Remove comment box
|
*/
function pingraphy_comments_form_defaults($default) {
        $default['comment_notes_after'] = '';
        return $default;
}

add_filter('comment_form_defaults','pingraphy_comments_form_defaults');


/**
* Custom Style 
*/
load_template( get_template_directory() . '/inc/custom-style.php' );



