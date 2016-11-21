<?php
/**
 * Plugin Name: Contact Form Maker
 * Plugin URI: http://web-dorado.com/products/form-maker-wordpress.html
 * Description: WordPress Contact Form Maker is a simple contact form builder, which allows the user with almost no knowledge of programming to create and edit different type of contact forms.
 * Version: 1.8.37
 * Author: WebDorado
 * Author URI: http://web-dorado.com/
 * License: GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
define('WD_FMC_DIR', WP_PLUGIN_DIR . "/" . plugin_basename(dirname(__FILE__)));
define('WD_FMC_URL', plugins_url(plugin_basename(dirname(__FILE__))));

// Plugin menu.
function form_maker_options_panel_cfm() {
  if (!get_option('form_maker_pro_active', FALSE)) {
  add_menu_page('Contact Form Maker', 'Contact Form', 'manage_options', 'manage_fmc', 'form_maker_cfm', WD_FMC_URL . '/images/FormMakerLogo-16.png');

  $manage_page = add_submenu_page('manage_fmc', 'Manager', 'Manager', 'manage_options', 'manage_fmc', 'form_maker_cfm');
  add_action('admin_print_styles-' . $manage_page, 'form_maker_manage_styles_cfm');
  add_action('admin_print_scripts-' . $manage_page, 'form_maker_manage_scripts_cfm');

  $submissions_page = add_submenu_page('manage_fmc', 'Submissions', 'Submissions', 'manage_options', 'submissions_fmc', 'form_maker_cfm');
  add_action('admin_print_styles-' . $submissions_page, 'form_maker_cfm_submissions_styles');
  add_action('admin_print_scripts-' . $submissions_page, 'form_maker_cfm_submissions_scripts');

  $blocked_ips_page = add_submenu_page('manage_fmc', 'Blocked IPs', 'Blocked IPs', 'manage_options', 'blocked_ips_fmc', 'form_maker_cfm');
  add_action('admin_print_styles-' . $blocked_ips_page, 'form_maker_manage_styles_cfm');
  add_action('admin_print_scripts-' . $blocked_ips_page, 'form_maker_manage_scripts_cfm');

  $themes_page = add_submenu_page('manage_fmc', 'Themes', 'Themes', 'manage_options', 'themes_fmc', 'form_maker_cfm');
  add_action('admin_print_styles-' . $themes_page, 'form_maker_manage_styles_cfm');
  add_action('admin_print_scripts-' . $themes_page, 'form_maker_manage_scripts_cfm');

  $global_options_page = add_submenu_page('manage_fmc', 'Global Options', 'Global Options', 'manage_options', 'goptions_fmc', 'form_maker_cfm');
  add_action('admin_print_styles-' . $global_options_page, 'form_maker_manage_styles_cfm');
  add_action('admin_print_scripts-' . $global_options_page, 'form_maker_manage_scripts_cfm');
  
  $licensing_plugins_page = add_submenu_page('manage_fmc', 'Get Pro', 'Get Pro', 'manage_options', 'licensing_fmc', 'form_maker_cfm');

  add_submenu_page('manage_fmc', 'Featured Plugins', 'Featured Plugins', 'manage_options', 'featured_plugins_fmc', 'fmc_featured');
  add_submenu_page('manage_fmc', 'Featured Themes', 'Featured Themes', 'manage_options', 'featured_themes_fmc', 'fmc_featured_themes');
  
  $uninstall_page = add_submenu_page('manage_fmc', 'Uninstall', 'Uninstall', 'manage_options', 'uninstall_fmc', 'form_maker_cfm');
  add_action('admin_print_styles-' . $uninstall_page, 'form_maker_styles_cfm');
  add_action('admin_print_scripts-' . $uninstall_page, 'form_maker_scripts_cfm');
  }
}
add_action('admin_menu', 'form_maker_options_panel_cfm');

function form_maker_cfm() {
  if (function_exists('current_user_can')) {
    if (!current_user_can('manage_options')) {
      die('Access Denied');
    }
  }
  else {
    die('Access Denied');
  }
  require_once(WD_FMC_DIR . '/framework/WDW_FMC_Library.php');
  $page = WDW_FMC_Library::get('page');
  require_once(WD_FMC_DIR . '/framework/WDW_FMC_Library.php');

  if (($page != '') && (($page == 'manage_fmc') || ($page == 'submissions_fmc') || ($page == 'goptions_fmc') || ($page == 'blocked_ips_fmc') || ($page == 'themes_fmc') || ($page == 'licensing_fmc') || ($page == 'uninstall_fmc') || ($page == 'formcontactwindow') || ($page == 'featured_plugins_fmc') || ($page == 'extensions_fmc'))) {
    require_once (WD_FMC_DIR . '/admin/controllers/FMController' . ucfirst(strtolower($page)) . '.php');
    $controller_class = 'FMController' . ucfirst(strtolower($page));
    $controller = new $controller_class();
    $controller->execute();
  }
}

function fmc_featured() {
  if (function_exists('current_user_can')) {
    if (!current_user_can('manage_options')) {
      die('Access Denied');
    }
  }
  else {
    die('Access Denied');
  }
  require_once(WD_FMC_DIR . '/featured/featured.php');
  wp_register_style('fmc_featured', WD_FMC_URL . '/featured/style.css', array(), 'cfm-'.get_option("wd_form_maker_version"));
  wp_print_styles('fmc_featured');
  fmc_featured_page('contact-maker');
}

function fmc_featured_themes() {
  if (function_exists('current_user_can')) {
    if (!current_user_can('manage_options')) {
      die('Access Denied');
    }
  }
  else {
    die('Access Denied');
  }
  require_once(WD_FMC_DIR . '/featured/featured_themes.php');
  wp_register_style('fmc_featured_themes', WD_FMC_URL . '/featured/featured_themes.css', array(), 'cfm-'.get_option("wd_form_maker_version"));
  wp_print_styles('fmc_featured_themes');
  fmc_featured_themes_page('contact-form-maker');
}

function fmc_extensions() {
  if (function_exists('current_user_can')) {
    if (!current_user_can('manage_options')) {
      die('Access Denied');
    }
  }
  else {
    die('Access Denied');
  }
  require_once(WD_FMC_DIR . '/featured/featured.php');
  wp_register_style('fmc_featured', WD_FMC_URL . '/featured/style.css', array(), 'cfm-'.get_option("wd_form_maker_version"));
  wp_print_styles('fmc_featured');
  fmc_extensions_page('contact-form-maker');
}

add_action('wp_ajax_get_stats_fmc', 'form_maker_cfm'); //Show statistics
add_action('wp_ajax_generete_csv_fmc', 'form_maker_ajax_cfm'); // Export csv.
add_action('wp_ajax_generete_xml_fmc', 'form_maker_ajax_cfm'); // Export xml.
add_action('wp_ajax_FormMakerPreview_fmc', 'form_maker_ajax_cfm');
add_action('wp_ajax_formcontactwdcaptcha', 'form_maker_ajax_cfm'); // Generete captcha image and save it code in session.
add_action('wp_ajax_nopriv_formcontactwdcaptcha', 'form_maker_ajax_cfm'); // Generete captcha image and save it code in session for all users.
add_action('wp_ajax_formcontactwdmathcaptcha', 'form_maker_ajax_cfm'); // Generete math captcha image and save it code in session.
add_action('wp_ajax_nopriv_formcontactwdmathcaptcha', 'form_maker_ajax_cfm'); // Generete math captcha image and save it code in session for all users.
add_action('wp_ajax_frommapeditinpopup_fmc', 'form_maker_ajax_cfm'); // Open map in submissions.
add_action('wp_ajax_fromipinfoinpopup_fmc', 'form_maker_ajax_cfm'); // Open ip in submissions.
add_action('wp_ajax_FormMakerEditCSS_fmc', 'form_maker_ajax_cfm'); // Edit css from form options.


if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	require_once( 'fmc_admin_class.php' );
	include_once('contact_form_maker_notices_class.php');
	add_action( 'plugins_loaded', array( 'FMC_Admin', 'get_instance' ) );
}

function form_maker_ajax_cfm() {
  require_once(WD_FMC_DIR . '/framework/WDW_FMC_Library.php');
  $page = WDW_FMC_Library::get('action');
  if ($page != 'formcontactwdcaptcha' && $page != 'formcontactwdmathcaptcha' ) {
    if (function_exists('current_user_can')) {
      if (!current_user_can('manage_options')) {
        die('Access Denied');
      }
    }
    else {
      die('Access Denied');
    }
  }
  if ($page != '') {
    require_once (WD_FMC_DIR . '/admin/controllers/FMController' . ucfirst($page) . '.php');
    $controller_class = 'FMController' . ucfirst($page);
    $controller = new $controller_class();
    $controller->execute();
  }
}

// Add the Contact Form Maker button.
function form_maker_add_button_cfm($buttons) {
  if (!get_option('form_maker_pro_active', FALSE)) {
    array_push($buttons, "fmc_form_mce");
  }
  return $buttons;
}

// Register Contact Form Maker button.
function form_maker_register_cfm($plugin_array) {
  if (!get_option('form_maker_pro_active', FALSE)) {
    $url = WD_FMC_URL . '/js/form_maker_editor_button.js';
    $plugin_array["fmc_form_mce"] = $url;
  }
  return $plugin_array;
}

function form_maker_admin_ajax_cfm() {
  ?>
  <script>
    var form_maker_admin_ajax_cfm = '<?php echo add_query_arg(array('action' => 'formcontactwindow'), admin_url('admin-ajax.php')); ?>';
    var plugin_url = '<?php echo WD_FMC_URL; ?>';
    var fmc_plugin_url = '<?php echo WD_FMC_URL; ?>';
	var admin_url = '<?php echo admin_url('admin.php'); ?>';
  </script>
  <?php
}
add_action('admin_head', 'form_maker_admin_ajax_cfm');

function do_output_buffer_fmc() {
  ob_start();
}
 add_action('init', 'do_output_buffer_fmc');
 
add_shortcode('wd_contact_form', 'contact_fm_shortcode');
add_shortcode('contact_form', 'contact_fm_shortcode');
function contact_fm_shortcode($attrs) {
	ob_start();
	FMC_front_end_main($attrs);
	return str_replace(array("\r\n", "\n", "\r"), '', ob_get_clean());
}

function FMC_front_end_main($params) {
	$form_id =  isset($params['id']) ? (int)$params['id'] : '';
	if($form_id)
		wd_contact_form_maker($form_id);
	return;
}

add_shortcode('email_verification', 'fmc_email_verification_shortcode');
function fmc_email_verification_shortcode() {
	require_once(WD_FMC_DIR . '/framework/WDW_FMC_Library.php');
	require_once(WD_FMC_DIR . '/frontend/controllers/FMControllerVerify_email_fmc.php');
    $controller_class = 'FMontrollerVerify_email_fmc';
    $controller = new $controller_class();
    $controller->execute();
}

function wd_contact_form_maker($id) {
  require_once (WD_FMC_DIR . '/frontend/controllers/FMControllerForm_maker_fmc.php');
  $controller = new FMControllerForm_maker_fmc();
  $form = $controller->execute($id);
  echo $form;
}


// Add the Form Maker button to editor.
add_action('wp_ajax_formcontactwindow', 'form_maker_ajax_cfm');
add_filter('mce_external_plugins', 'form_maker_register_cfm');
add_filter('mce_buttons', 'form_maker_add_button_cfm', 0);

// Form Maker Widget.
if (class_exists('WP_Widget')) {
  require_once(WD_FMC_DIR . '/admin/controllers/FMControllerWidget_fmc.php');
  add_action('widgets_init', create_function('', 'return register_widget("FMControllerWidget_fmc");'));
}

// Register fmemailverification post type
add_action('init', 'register_fmcemailverification_cpt');
function register_fmcemailverification_cpt(){
	$args = array(
	  'public' => true,
	  'label'  => 'CFM Email Verification'
	);
	
	register_post_type( 'cfmemailverification', $args );
	if(!get_option('cfm_emailverification')) {	
		flush_rewrite_rules();
		add_option('cfm_emailverification', true);
	}
}

// Activate plugin.
function form_maker_activate_cfm() {
  $version = get_option("wd_form_maker_version");
  $new_version = '1.8.37';
  global $wpdb;
  if (!$version) {
	add_option("wd_form_maker_version", $new_version, '', 'no');
	if ($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->prefix . "formmaker'") == $wpdb->prefix . "formmaker") {
		$recaptcha_keys = $wpdb->get_row('SELECT `public_key`, `private_key` FROM ' . $wpdb->prefix . 'formmaker WHERE public_key!="" and private_key!=""', ARRAY_A);
		$public_key = isset($recaptcha_keys['public_key']) ? $recaptcha_keys['public_key'] : '';
		$private_key = isset($recaptcha_keys['private_key']) ? $recaptcha_keys['private_key'] : '';
		if (FALSE === $fmc_settings = get_option('fmc_settings')) {
			add_option('fmc_settings', array('public_key' => $public_key, 'private_key' => $private_key, 'csv_delimiter' => ','));	
		}
		
      require_once WD_FMC_DIR . "/contact_form_maker_update.php";
	  
	  
      form_maker_update_until_mvc();
      contact_form_maker_update('');
    }
    else {
	
      require_once WD_FMC_DIR . "/contact_form_maker_insert.php";
      contact_from_maker_insert();
	  add_option("wd_cfield_limit", '9', '', 'no');
	  $cf_email_verification_post = array(
		  'post_title'    => 'Email Verification',
		  'post_content'  => '[email_verification]',
		  'post_status'   => 'publish',
		  'post_author'   => 1,
		  'post_type'   => 'fmemailverification',
		);
		
		
	add_option('fmc_settings', array('public_key' => '', 'private_key' => '', 'csv_delimiter' => ','));
		
		
	  $cf_mail_verification_post_id = wp_insert_post( $cf_email_verification_post );
	  $wpdb->update($wpdb->prefix . "formmaker", array(
        'mail_verification_post_id' => $cf_mail_verification_post_id,
      ), array('id' => 1), array(
        '%d',
      ), array('%d'));
    }
  }
  elseif (version_compare($version, $new_version, '<')) {
   require_once WD_FMC_DIR . "/contact_form_maker_update.php";
    contact_form_maker_update($version);
    update_option("wd_form_maker_version", $new_version);
	$recaptcha_keys = $wpdb->get_row('SELECT `public_key`, `private_key` FROM ' . $wpdb->prefix . 'formmaker WHERE public_key!="" and private_key!=""', ARRAY_A);
	$public_key = isset($recaptcha_keys['public_key']) ? $recaptcha_keys['public_key'] : '';
	$private_key = isset($recaptcha_keys['private_key']) ? $recaptcha_keys['private_key'] : '';
	
	if (FALSE === $fmc_settings = get_option('fmc_settings')) {
		add_option('fmc_settings', array('public_key' => $public_key, 'private_key' => $private_key, 'csv_delimiter' => ','));	
	}
  }
  
  require_once WD_FMC_DIR . "/contact_form_maker_insert.php";
  install_demo_forms_fmc();
}
register_activation_hook(__FILE__, 'form_maker_activate_cfm');

if (!isset($_GET['action']) || $_GET['action'] != 'deactivate') {
  add_action('admin_init', 'form_maker_activate_cfm');
}

// Form Maker manage page styles.
function form_maker_manage_styles_cfm() {
  wp_admin_css('thickbox');
  wp_enqueue_style('form_maker_tables', WD_FMC_URL . '/css/form_maker_tables.css', array(), 'cfm-'.get_option("wd_form_maker_version"));
  wp_enqueue_style('form_maker_first', WD_FMC_URL . '/css/form_maker_first.css', array(), 'cfm-'.get_option("wd_form_maker_version"));
  wp_enqueue_style('form_maker_calendar-jos', WD_FMC_URL . '/css/calendar-jos.css');
  wp_enqueue_style('jquery-ui', WD_FMC_URL . '/css/jquery-ui-1.10.3.custom.css');
  wp_enqueue_style('jquery-ui-spinner', WD_FMC_URL . '/css/jquery-ui-spinner.css');
  wp_enqueue_style('form_maker_style', WD_FMC_URL . '/css/style.css', array(), 'cfm-'.get_option("wd_form_maker_version"));
  wp_enqueue_style('form_maker_codemirror', WD_FMC_URL . '/css/codemirror.css');
  wp_enqueue_style('form_maker_layout', WD_FMC_URL . '/css/form_maker_layout.css', array(), 'cfm-'.get_option("wd_form_maker_version"));
}
// Form Maker manage page scripts.
function form_maker_manage_scripts_cfm() {
  wp_enqueue_script('thickbox');
  global $wp_scripts;
  $fmc_settings = get_option('fmc_settings');
  $map_key = isset($fmc_settings['map_key']) ? $fmc_settings['map_key'] : '';
  if (isset($wp_scripts->registered['jquery'])) {
    $jquery = $wp_scripts->registered['jquery'];
    if (!isset($jquery->ver) OR version_compare($jquery->ver, '1.8.2', '<')) {
      wp_deregister_script('jquery');
      wp_register_script('jquery', FALSE, array('jquery-core', 'jquery-migrate'), '1.10.2' );
    }
  }
  wp_enqueue_script('jquery');
  wp_enqueue_script('jquery-ui-sortable');
  wp_enqueue_script('jquery-ui-widget');
  wp_enqueue_script('jquery-ui-slider');
  wp_enqueue_script('jquery-ui-spinner');

  // wp_enqueue_script('mootools', WD_FMC_URL . '/js/mootools.js', array(), '1.12');
  wp_enqueue_script('gmap_form_api', 'https://maps.google.com/maps/api/js?v=3.exp&key='.$map_key);
  wp_enqueue_script('gmap_form', WD_FMC_URL . '/js/if_gmap_back_end.js');

  wp_enqueue_script('form_maker_admin', WD_FMC_URL . '/js/form_maker_admin.js', array(), 'cfm-'.get_option("wd_form_maker_version"));
  wp_enqueue_script('form_maker_manage', WD_FMC_URL . '/js/form_maker_manage.js', array(), 'cfm-'.get_option("wd_form_maker_version"));

  wp_enqueue_script('form_maker_codemirror', WD_FMC_URL . '/js/layout/codemirror.js', array(), '2.3');
  wp_enqueue_script('form_maker_clike', WD_FMC_URL . '/js/layout/clike.js', array(), '1.0.0');
  wp_enqueue_script('form_maker_formatting', WD_FMC_URL . '/js/layout/formatting.js', array(), '1.0.0');
  wp_enqueue_script('form_maker_css', WD_FMC_URL . '/js/layout/css.js', array(), '1.0.0');
  wp_enqueue_script('form_maker_javascript', WD_FMC_URL . '/js/layout/javascript.js', array(), '1.0.0');
  wp_enqueue_script('form_maker_xml', WD_FMC_URL . '/js/layout/xml.js', array(), '1.0.0');
  wp_enqueue_script('form_maker_php', WD_FMC_URL . '/js/layout/php.js', array(), '1.0.0');
  wp_enqueue_script('form_maker_htmlmixed', WD_FMC_URL . '/js/layout/htmlmixed.js', array(), '1.0.0');

  wp_enqueue_script('Calendar', WD_FMC_URL . '/js/calendar/calendar.js', array(), '1.0');
  wp_enqueue_script('calendar_function', WD_FMC_URL . '/js/calendar/calendar_function.js');
  // wp_enqueue_script('form_maker_calendar_setup', WD_FMC_URL . '/js/calendar/calendar-setup.js');
}

// Form Maker submissions page styles.
function form_maker_cfm_submissions_styles() {
  wp_admin_css('thickbox');
  wp_enqueue_style('form_maker_tables', WD_FMC_URL . '/css/form_maker_tables.css', array(), 'cfm-'.get_option("wd_form_maker_version"));
  wp_enqueue_style('form_maker_calendar-jos', WD_FMC_URL . '/css/calendar-jos.css');
  wp_enqueue_style('jquery-ui', WD_FMC_URL . '/css/jquery-ui-1.10.3.custom.css', array(), '1.10.3');
  wp_enqueue_style('jquery-ui-spinner', WD_FMC_URL . '/css/jquery-ui-spinner.css', array(), '1.10.3');
  wp_enqueue_style('form_maker_style', WD_FMC_URL . '/css/style.css', array(), 'cfm-'.get_option("wd_form_maker_version"));
}
// Form Maker submissions page scripts.
function form_maker_cfm_submissions_scripts() {
  wp_enqueue_script('thickbox');
  global $wp_scripts;
  if (isset($wp_scripts->registered['jquery'])) {
    $jquery = $wp_scripts->registered['jquery'];
    if (!isset($jquery->ver) OR version_compare($jquery->ver, '1.8.2', '<')) {
      wp_deregister_script('jquery');
      wp_register_script('jquery', FALSE, array('jquery-core', 'jquery-migrate'), '1.10.2' );
    }
  }
  wp_enqueue_script('jquery');
  wp_enqueue_script( 'jquery-ui-progressbar' );
  wp_enqueue_script('jquery-ui-sortable');
  wp_enqueue_script('jquery-ui-widget');
  wp_enqueue_script('jquery-ui-slider');
  wp_enqueue_script('jquery-ui-spinner');
  wp_enqueue_script('jquery-ui-mouse');
  wp_enqueue_script('jquery-ui-core');

  // wp_enqueue_script('mootools', WD_FMC_URL . '/js/mootools.js', array(), '1.12');

  wp_enqueue_script('form_maker_admin', WD_FMC_URL . '/js/form_maker_admin.js', array(), 'cfm-'.get_option("wd_form_maker_version"));
  wp_enqueue_script('form_maker_manage', WD_FMC_URL . '/js/form_maker_manage.js', array(), 'cfm-'.get_option("wd_form_maker_version"));
  wp_enqueue_script('form_maker_submissions', WD_FMC_URL . '/js/form_maker_submissions.js', array(), 'cfm-'.get_option("wd_form_maker_version"));

  wp_enqueue_script('main', WD_FMC_URL . '/js/main.js', array(), 'cfm-'.get_option("wd_form_maker_version"));
  wp_enqueue_script('main_div_front_end', WD_FMC_URL . '/js/main_div_front_end.js', array(), 'cfm-'.get_option("wd_form_maker_version"));

  wp_enqueue_script('Calendar', WD_FMC_URL . '/js/calendar/calendar.js', array(), '1.0');
  wp_enqueue_script('calendar_function', WD_FMC_URL . '/js/calendar/calendar_function.js');
  // wp_enqueue_script('form_maker_calendar_setup', WD_FMC_URL . '/js/calendar/calendar-setup.js');
  
  wp_localize_script('main_div_front_end', 'fm_objectL10n', array(
    'plugin_url' => WD_FMC_URL
  ));
}

function form_maker_styles_cfm() {
  wp_enqueue_style('form_maker_tables', WD_FMC_URL . '/css/form_maker_tables.css', array(), 'cfm-'.get_option("wd_form_maker_version"));
}
function form_maker_scripts_cfm() {
  wp_enqueue_script('form_maker_admin', WD_FMC_URL . '/js/form_maker_admin.js', array(), 'cfm-'.get_option("wd_form_maker_version"));
}

$contact_form_maker_generate_action = 0;
function contact_form_maker_generate_action() {
  global $contact_form_maker_generate_action;
  $contact_form_maker_generate_action = 1;
}
add_filter('wp_head', 'contact_form_maker_generate_action', 10000);

function form_maker_front_end_scripts_cfm() {
	$fmc_settings = get_option('fmc_settings');
    $map_key = isset($fmc_settings['map_key']) ? $fmc_settings['map_key'] : '';
  // global $wp_scripts;
  // if (isset($wp_scripts->registered['jquery'])) {
    // $jquery = $wp_scripts->registered['jquery'];
    // if (!isset($jquery->ver) OR version_compare($jquery->ver, '1.8.2', '<')) {
      // wp_deregister_script('jquery');
      // wp_register_script('jquery', FALSE, array('jquery-core', 'jquery-migrate'), '1.10.2' );
    // }
  // }
  wp_enqueue_script('jquery');
  wp_enqueue_script('jquery-ui-widget');
  wp_enqueue_script('jquery-ui-slider');
  wp_enqueue_script('jquery-ui-spinner');
  wp_enqueue_script('jquery-effects-shake');

  wp_enqueue_style('jquery-ui', WD_FMC_URL . '/css/jquery-ui-1.10.3.custom.css');
  wp_enqueue_style('jquery-ui-spinner', WD_FMC_URL . '/css/jquery-ui-spinner.css');

  // wp_enqueue_script('mootools', WD_FMC_URL . '/js/mootools.js', array(), '1.12');
  wp_enqueue_script('gmap_form_api', 'https://maps.google.com/maps/api/js?v=3.exp&key='.$map_key);
  wp_enqueue_script('gmap_form', WD_FMC_URL . '/js/if_gmap_front_end.js');
  wp_enqueue_script('jelly.min', WD_FMC_URL . '/js/jelly.min.js');
  wp_enqueue_script('file-upload', WD_FMC_URL . '/js/file-upload.js');
  // wp_enqueue_style('gmap_styles_', WD_FMC_URL . '/css/style_for_map.css');

  wp_enqueue_script('Calendar', WD_FMC_URL . '/js/calendar/calendar.js');
  wp_enqueue_script('calendar_function', WD_FMC_URL . '/js/calendar/calendar_function.js');
  // wp_enqueue_script('form_maker_calendar_setup', WD_FMC_URL . '/js/calendar/calendar-setup.js');
  wp_enqueue_style('form_maker_calendar-jos', WD_FMC_URL . '/css/calendar-jos.css');
  wp_enqueue_style('form_maker_frontend', WD_FMC_URL . '/css/form_maker_frontend.css');

  wp_register_script('main_div_front_end', WD_FMC_URL . '/js/main_div_front_end.js', array(), 'cfm-'.get_option("wd_form_maker_version"));
  wp_enqueue_script('main_div_front_end', WD_FMC_URL . '/js/main_div_front_end.js', array(), 'cfm-'.get_option("wd_form_maker_version"));
  wp_register_script('main_front_end', WD_FMC_URL . '/js/main_front_end.js', array(), 'cfm-'.get_option("wd_form_maker_version"));
  wp_localize_script('main_div_front_end', 'fm_objectL10n', array(
    'plugin_url' => WD_FMC_URL
  ));
  wp_localize_script('main_front_end', 'fm_objectL10n', array(
    'plugin_url' => WD_FMC_URL
  ));
}
add_action('wp_enqueue_scripts', 'form_maker_front_end_scripts_cfm');

// Languages localization.
function form_maker_language_load_cfm() {
  load_plugin_textdomain('form_maker', FALSE, basename(dirname(__FILE__)) . '/languages');
}
add_action('init', 'form_maker_language_load_cfm');

?>