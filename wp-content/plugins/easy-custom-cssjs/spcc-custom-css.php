<?php

/*
  Plugin Name: Easy Custom Css/Js
  Plugin URI:
  Description: Add custom css to the whole website and to specific posts and pages, and option for responsive css,sass and less also.
  Version: 1.3
  Author: Sunil Prajapati
  Author URI: http://www.sunilprajapati.in
  Text Domain: sp-custom-css
  Domain Path: /languages/
  License: GPL2
 */

if (!defined('ABSPATH'))
       exit; // Exit if accessed directly

ob_start();

define('SPCC_VERSION', '1.3');

define('SPCC_REQUIRED_WP_VERSION', '3.2');

define('SPCC_PLUGIN', __FILE__);

define('SPCC_PLUGIN_BASENAME', plugin_basename(SPCC_PLUGIN));

define('SPCC_PLUGIN_NAME', trim(dirname(SPCC_PLUGIN_BASENAME), '/'));

define('SPCC_PLUGIN_DIR', untrailingslashit(dirname(SPCC_PLUGIN)));

if (!defined('ABSPATH'))
       die('No direct access allowed');

require_once SPCC_PLUGIN_DIR . '/spcc_class.php';

function spcc_update_db_check() {
       if (get_option('spcc_plugin_version')) {
              update_option('spcc_plugin_version', SPCC_VERSION);
       } else {
              add_option('spcc_plugin_version', SPCC_VERSION);
       }
}


add_action('plugins_loaded', 'spcc_update_db_check');

add_action('admin_init', 'spcc_enqueued_style');

function spcc_enqueued_style() {
       if (is_admin()) {
              wp_enqueue_style('bootstrap_css', spcc_plugin_url('css/bootstrap.css'), array(), '1.0', 'all');
              wp_enqueue_style('spcc_custom_css', spcc_plugin_url('css/spcc_custom.css'), array(), '1.0', 'all');
       }
}

add_action('admin_init', 'spcc_enqueued_scripts');

function spcc_enqueued_scripts() {
       if (is_admin()) {
              wp_enqueue_script('ace_script', spcc_plugin_url('js/src-min/ace.js'), array('jquery'), '1.1', true);
              wp_enqueue_script('css_script', spcc_plugin_url('js/spcc_custom.js'), array('jquery'), '1.1', true);
       }
}

function spcc_plugin_url($path = '') {
       $url = plugins_url($path, SPCC_PLUGIN);

       if (is_ssl() && 'http:' == substr($url, 0, 5)) {
              $url = 'https:' . substr($url, 5);
       }
       return $url;
}

if (class_exists('wpspcc')) {
       register_uninstall_hook(__FILE__, array('wpspcc', 'uninstall'));
       $wpspcc = new wpspcc();
}

if (isset($wpspcc)) {

       function spcc_settings_link($links) {
              $settings_link = '<a href="admin.php?page=spcc_custom_css">' . __('Settings', 'wp-add-custom-css') . '</a>';
              array_unshift($links, $settings_link);
              return $links;
       }

       add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'spcc_settings_link');
}

function spcc_get_custom_css($ex = '') {
       $default = "body {\n}";

       if (in_array($ex, array('lg', 'md', 'sm', 'xs'))) {
              $default = '';
       }
       if ($ex == 'less') {
              $default = "@my-var: #ccc;\n@my-other-var: #fff;\n\n.any-container {\n\t.nested-container {\n\t\tcolor: @my-var;\n\t}\n}";
       } else
       if ($ex == 'sass') {
              $default = "\$color: #abc;\n\ndiv.example-el {\n\tcolor: lighten(\$color, 20%);\n}";
       }

       if (!is_admin())
              $default = '';

       return stripslashes(get_option('spcc_custom_css' . ($ex ? "_{$ex}" : ''), $default));
}

function spcc_set_custom_css($css, $ex = '') {
       update_option('spcc_custom_css' . ($ex ? "_{$ex}" : ''), $css);
}

add_action('wp_head', 'spcc_css_print_styles');

function spcc_css_print_styles() {
       $screen_lg = 1200;
       $screen_md = 992;
       $screen_sm = 480;
       $screen_xs = 480;

       $custom_css = trim(spcc_get_custom_css());
       $custom_css_lg = trim(spcc_get_custom_css('lg'));
       $custom_css_md = trim(spcc_get_custom_css('md'));
       $custom_css_sm = trim(spcc_get_custom_css('sm'));
       $custom_css_xs = trim(spcc_get_custom_css('xs'));

       $custom_css_less = trim(spcc_get_custom_css('less'));
       $custom_css_sass = trim(spcc_get_custom_css('sass'));
       $custom_js = trim(spcc_get_custom_css('js'));
       $custom_footer_js = trim(spcc_get_custom_css('footer_js'));


       $custom_css_append = '';

       if ($custom_css) {
              $custom_css_append .= $custom_css;
              $custom_css_append .= PHP_EOL . PHP_EOL;
       }


       # XS - Media Screen CSS
       if ($custom_css_xs) {
              $custom_css_append .= "@media screen and (max-width: {$screen_xs}px){" . PHP_EOL;
              $custom_css_append .= $custom_css_xs . PHP_EOL;
              $custom_css_append .= '}';
              $custom_css_append .= PHP_EOL . PHP_EOL;
       }

       # SM - Media Screen CSS
       if ($custom_css_sm) {
              $custom_css_append .= "@media screen and (min-width: {$screen_sm}px){" . PHP_EOL;
              $custom_css_append .= $custom_css_sm . PHP_EOL;
              $custom_css_append .= '}';
              $custom_css_append .= PHP_EOL . PHP_EOL;
       }

       # MD - Media Screen CSS
       if ($custom_css_md) {
              $custom_css_append .= "@media screen and (min-width: {$screen_md}px){" . PHP_EOL;
              $custom_css_append .= $custom_css_md . PHP_EOL;
              $custom_css_append .= '}';
              $custom_css_append .= PHP_EOL . PHP_EOL;
       }

       # LG - Media Screen CSS
       if ($custom_css_lg) {
              $custom_css_append .= "@media screen and (min-width: {$screen_lg}px){" . PHP_EOL;
              $custom_css_append .= $custom_css_lg . PHP_EOL;
              $custom_css_append .= '}';
              $custom_css_append .= PHP_EOL . PHP_EOL;
       }



       # LESS CSS
       if ($custom_css_less) {
              if (!class_exists('lessc')) {
                     require_once SPCC_PLUGIN_DIR . '/include/lessc.inc.php';
              }

              $less = new lessc;
              $compiled_less = '';

              try {
                     $compiled_less = $less->compile($custom_css_less);
              } catch (exception $e) {
                     
              }

              if ($compiled_less) {
                     $custom_css_append .= $compiled_less . PHP_EOL . PHP_EOL;
              }
       }


       # SASS CSS
       if ($custom_css_sass) {
              if (!class_exists('sassc')) {
                     require_once SPCC_PLUGIN_DIR . '/include/scss.inc.php';
              }

              $scss = new scssc;
              $compiled_sass = '';

              try {
                     $compiled_sass = $scss->compile($custom_css_sass);
              } catch (exception $e) {
                     
              }

              if ($compiled_sass) {
                     $custom_css_append .= $compiled_sass . PHP_EOL . PHP_EOL;
              }
       }

       if ($custom_css_append = trim($custom_css_append)) {
              echo '<style id="spcc-custom-css">' . PHP_EOL . spcc_compress_text($custom_css_append) . PHP_EOL . '</style>';
       }
       if ($custom_js_append = trim($custom_js)) {
              echo '<script>(function() {' . PHP_EOL . trim($custom_js, '"') . PHP_EOL . ' })();</script>';
       }
       if ($custom_js_append = trim($custom_footer_js)) {
              echo '<script>(function() {' . PHP_EOL . trim($custom_footer_js, '"') . PHP_EOL . ' })();</script>';
       }
}

function spcc_compress_text($buffer) {
       /* remove comments */
       $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
       /* remove tabs, spaces, newlines, etc. */
       $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '	', '	', '	'), '', $buffer);
       return $buffer;
}

?>