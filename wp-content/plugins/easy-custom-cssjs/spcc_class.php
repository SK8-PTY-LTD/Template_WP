<?php

if (!defined('ABSPATH'))
       exit; // 

if (!class_exists('wpspcc')) {

       class wpspcc {

              private $options;

              public function __construct() {
                     add_action('admin_menu', array($this, 'spcc_add_menu'));
                     add_action('admin_init', array($this, 'spcc_init_settings'));
                     add_action('add_meta_boxes', array($this, 'spcc_add_meta_box'));
                     add_action('save_post', array($this, 'spcc_single_save'));
                     add_action('init', array($this, 'init'));
                     add_action('wp_enqueue_scripts', array($this, 'spcc_add_custom_css'), 999);
                     add_action('wp_head', array($this, 'spcc_single_custom_css'));
              }

              public function init() {
                     load_plugin_textdomain('wp-add-custom-css', false, dirname(plugin_basename(__FILE__)) . '/languages');
              }

              public static function uninstall() {
                     self::spcc_delete_options();
                     self::spcc_delete_custom_meta();
              }

              public function spcc_add_meta_box($post_type) {
                     $post_types = array('post', 'page');
                     if (in_array($post_type, $post_types)) {
                            add_meta_box('wp_add_custom_css', __('Custom CSS', 'wp-add-custom-css'), array($this, 'spcc_render_meta_box_content'), $post_type, 'advanced', 'high');
                            add_meta_box('wp_add_custom_js', __('Custom Js', 'wp-add-custom-css'), array($this, 'spcc_render_meta_box_contentjs'), $post_type, 'advanced', 'high');
                     }
                     if (get_option('spcc_settings')) {
                            $edit_settings = get_option('spcc_settings');
                            $edit_settings = $edit_settings['spcc_post_type'];
                            if (is_array($edit_settings) && !empty($edit_settings)) {
                                   if (in_array($post_type, $edit_settings)) {
                                          add_meta_box('wp_add_custom_css', __('Custom CSS', 'wp-add-custom-css'), array($this, 'spcc_render_meta_box_content'), $post_type, 'advanced', 'high');
                                          add_meta_box('wp_add_custom_js', __('Custom Js', 'wp-add-custom-css'), array($this, 'spcc_render_meta_box_contentjs'), $post_type, 'advanced', 'high');
                                   }
                            } else if ($post_type == $edit_settings) {
                                   add_meta_box('wp_add_custom_js', __('Custom Js', 'wp-add-custom-css'), array($this, 'spcc_render_meta_box_contentjs'), $post_type, 'advanced', 'high');
                            }
                     }
              }

              public function spcc_single_save($post_id) {
                     if (!isset($_POST['wp_add_custom_css_box_nonce']) || !wp_verify_nonce($_POST['wp_add_custom_css_box_nonce'], 'single_add_custom_css_box')) {
                            return;
                     }
                     if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                            return;
                     }
                     if ('page' == $_POST['post_type']) {
                            if (!current_user_can('edit_page', $post_id))
                                   return;
                     } else {
                            if (!current_user_can('edit_post', $post_id))
                                   return;
                     }

                     $single_custom_css = wp_kses($_POST['single_custom_css'], array('\'', '\"'));
                     update_post_meta($post_id, '_single_add_custom_css', $single_custom_css);

                     $single_custom_js = wp_kses($_POST['single_custom_js_name'], array('\'', '\"'));
                     update_post_meta($post_id, '_single_add_custom_js', $single_custom_js);
              }

              public function spcc_render_meta_box_content($post) {
                     wp_nonce_field('single_add_custom_css_box', 'wp_add_custom_css_box_nonce');
                     $single_custom_css = get_post_meta($post->ID, '_single_add_custom_css', true);
                     echo '<p>' . sprintf(__('Add custom CSS rules for this %s', 'wp-add-custom-css'), $post->post_type) . '</p> ';
                     echo '<div id="sngleeditor" class="mh200">' . esc_attr($single_custom_css) . '</div><input type="hidden" id="single_custom_css" name="single_custom_css" value="">';
              }

              public function spcc_render_meta_box_contentjs($post) {
                     wp_nonce_field('single_add_custom_css_box', 'single_add_custom_css_box');
                     $single_custom_js = get_post_meta($post->ID, '_single_add_custom_js', true);
                     echo '<p>' . sprintf(__('Add custom JS rules for this %s', 'wp-add-custom-css'), $post->post_type) . '</p> ';
                     echo '<div id="sngleeditorjs" class="mh200">' . esc_attr($single_custom_js) . '</div><input type="hidden" id="single_custom_js" name="single_custom_js_name" value="">';
              }

              public function spcc_add_menu() {
                     global $wpspcc_settings_page;
                     $wpspcc_settings_page = add_menu_page(__('Wordpress Add Custom CSS', 'wp-add-custom-css'), __('Custom Css/Js', 'wp-add-custom-css'), 'manage_options', 'spcc_custom_css', array($this, 'spcc_create_settings_page'), plugin_dir_url(__FILE__) . '/images/icon.png');
              }

              public function spcc_create_settings_page() {
                     $this->options = get_option('wpspcc_settings');

                     load_template(SPCC_PLUGIN_DIR . '/template/form.php');
              }

              public function spcc_init_settings() {
                     register_setting(
                             'wpspcc_group', 'wpspcc_settings'
                     );
              }

              public function spcc_delete_options() {
                     unregister_setting(
                             'wpspcc_group', 'wpspcc_settings'
                     );
                     delete_option('wpspcc_settings');
              }

              public function spcc_delete_custom_meta() {
                     delete_post_meta_by_key('_single_add_custom_css');
              }

              public function spcc_add_custom_css() {
                     $this->options = get_option('wpspcc_settings');
                     if (isset($this->options['main_custom_style']) && $this->options['main_custom_style'] != '') {
                            if (function_exists('icl_object_id')) {
                                   $css_base_url = site_url();
                            } else {
                                   $css_base_url = get_bloginfo('url');
                            }
                            wp_register_style('wp-add-custom-css', $css_base_url . '?display_custom_css=css');
                            wp_enqueue_style('wp-add-custom-css');
                     }
              }

              public function spcc_single_custom_css() {
                     if (is_single() || is_page()) {
                            global $post;
                            $single_custom_css = get_post_meta($post->ID, '_single_add_custom_css', true);
                            if ($single_custom_css !== '') {
                                   $single_custom_css = str_replace('&gt;', '>', $single_custom_css);
                                   $output = "<style type=\"text/css\">\n" . $single_custom_css . "\n</style>\n";
                                   echo $output;
                            }
                     }
              }

       }

}
?>