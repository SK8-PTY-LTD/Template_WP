<?php
/**
 * @package DrawIt (draw.io)
 * @version 1.1.0
 */
/*
Plugin Name:    DrawIt (draw.io)
Plugin URI:     http://www.assortedchips.com/#drawit
Description:    Draw and edit flow charts, diagrams, images and more while editing a post.
Version:        1.1.0
Author:         assorted[chips]
Author URI:     http://www.assortedchips.com/
License:        GPL3 or later
License URI:    https://www.gnu.org/licenses/gpl-3.0.html


    Copyright 2015  Mike Thomson  (email : contact@mike-thomson.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 3, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

define('FRONTEND_BUTTON_PRIORITY_DEFAULT', 10);
define('FRONTEND_BUTTON_PRIORITY_LAST', 2147483647);

$plugin_slug = "drawit";
$plugin_label = 'DrawIt';
$plugin_default_options = array(
    //'default_width'     => '6.5in',
    //'default_height'    => '5in',
    //'max_width'         => '100%',
    //'max_height'        => '9in',
    'default_type'      => 'png',
    'allow_svg'         => 'no',
    'temp_dir'          => 'wp_default',
    'enable_frontend'   => 'yes',
    'override_conflict' => 'no'
);
$valid_types = array(
    'png',
    'svg'
);
$valid_units = array(
    'em',
    'ex',
    '%',
    'px',
    'cm',
    'mm',
    'in',
    'pt',
    'pc',
    'ch',
    'rem',
    'vh',
    'vw',
    'vmin',
    'vmax'
);
$valid_temp_dirs = array(
    'wp_default',
    'wp_content'
);

class drawit {

    public function __construct($plugin_slug, $plugin_label, $plugin_default_options, $valid_types, $valid_units, $valid_temp_dirs) {
        $this->plugin_slug = $plugin_slug;
        $this->plugin_label = $plugin_label;
        $this->plugin_default_options = $plugin_default_options;
        $this->valid_units = $valid_units;
        $this->valid_temp_dirs = $valid_temp_dirs;
        $this->plugin_version = "1.1.0";

        // Options saved to database are used throughout the functions here, so 
        // make a copy now so they are easily accessible later.
        $this->options = get_option($this->plugin_slug . '_options', $this->plugin_default_options);

        // Starting w/ version 1.0.10, need to check for new options that don't exist in database yet.
        // v 1.0.10:
        if(!array_key_exists('temp_dir', $this->options)) {
            $this->options['temp_dir'] = $this->plugin_default_options['temp_dir'];
        }

        if(!array_key_exists('enable_frontend', $this->options)) {
            $this->options['enable_frontend'] = $this->plugin_default_options['enable_frontend'];
        }

        if(!array_key_exists('override_conflict', $this->options)) {
            $this->options['override_conflict'] = $this->plugin_default_options['override_conflict'];
        }

        // If the user has selected to not allow SVG uploads, then remove that 
        // from the "valid types".
        $tmp_types = array();
        foreach($valid_types as $type) {
            if(strtolower($type) != 'svg' || strtolower($this->options['allow_svg']) == 'yes') {
                array_push($tmp_types, strtolower($type));
            }
        }
        $this->valid_types = $tmp_types;

        add_action('admin_menu', array($this, 'admin_add_page'));
        add_action('admin_init', array($this, 'admin_init'));
        add_filter('plugin_action_links', array($this, 'settings_link'), 10, 2);
        add_filter('media_upload_tabs', array($this, 'add_tab'));
        add_filter('upload_mimes', array($this, 'add_mime_type'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('media_upload_' . $this->plugin_slug, array($this, 'media_menu_handler'));
        add_action('wp_ajax_submit-form-' . $this->plugin_slug, array($this, 'sideload_handler'));
        add_action('admin_print_scripts', array($this, 'quicktags_add_button'));

        if(strtolower($this->options['enable_frontend']) == 'yes') {
            $bttn_priority = FRONTEND_BUTTON_PRIORITY_DEFAULT;
            if(strtolower($this->options['override_conflict']) == 'yes') {
                $bttn_priority = FRONTEND_BUTTON_PRIORITY_LAST - 100;
            }
            add_action('init', array($this, 'user_init'));
            add_action('wp_print_scripts', array($this, 'quicktags_add_button'), $bttn_priority);
        }
    }

    // "Settings" link on plugin list page.
    public function settings_link($links, $file) {
        $this_plugin_basename = plugin_basename(__FILE__);
        if($file == $this_plugin_basename) {
            $settings_link = '<a href="options-general.php?page=' . $this->plugin_slug . '">' . __('Settings', $this->plugin_slug) . '</a>';
            array_unshift($links, $settings_link);
        }
        return $links;
    }

    // Enqueue the javascript.
    public function enqueue_scripts() {
        // Admin user, so don't use minified CSS.
        if(current_user_can('manage_options')) {
            wp_enqueue_style($this->plugin_slug . '-css', plugins_url('css/' . $this->plugin_slug . '.css', __FILE__), array(), $this->plugin_version);
        } else {
            wp_enqueue_style($this->plugin_slug . '-css', plugins_url('css/' . $this->plugin_slug . '.min.css', __FILE__), array(), $this->plugin_version);
        }
        wp_enqueue_script($this->plugin_slug . '-iframe-js', plugins_url('js/' . $this->plugin_slug . '-iframe.js', __FILE__), array(), $this->plugin_version, true);
        //wp_enqueue_script($this->plugin_slug . '-js-embed', 'https://www.draw.io/embed.js?s=basic', array(), $this->plugin_version, true);
    }

    // Add draw.io tab to "Insert Media" page when editing a post or page.
    public function add_tab($tabs) {
        $tabs[$this->plugin_slug] = 'Draw with draw.io';
        return $tabs;
    }

    // Add MIME type for svg.
    public function add_mime_type($mimes) {
        if(strtolower($this->options['allow_svg']) == 'yes') {
            $mimes['svg'] = 'image/svg+xml';
        }
        return $mimes;
    }

    // This calls the iframe-maker and enqueues associated javascript for generating
    // iframe that will hold editor.
    public function media_menu_handler() {
        $errors = '';
        wp_enqueue_script($this->plugin_slug . '-js', plugins_url('js/' . $this->plugin_slug . '.js', __FILE__));
        if(current_user_can('manage_options')) {
            wp_enqueue_style($this->plugin_slug . '-css', plugins_url('css/' . $this->plugin_slug . '.css', __FILE__), array(), $this->plugin_version);
        } else {
            wp_enqueue_style($this->plugin_slug . '-css', plugins_url('css/' . $this->plugin_slug . '.min.css', __FILE__), array(), $this->plugin_version);
        }
        return wp_iframe(array($this, 'iframe'), $errors);
    }

    // After user presses "save", this function gets called via admin-ajax.php.
    public function sideload_handler() {
        $resp = array('success' => false, 'html' => '');
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string(stripslashes($_POST['xml']));
        libxml_use_internal_errors(false);
        $specified_title = '';

        $img_b64 = $_POST['img_data'];
        $comma_pos = strpos($img_b64, ',');
        $img_type = 'png';
        if($comma_pos === false) {
            $img_data = stripslashes($img_b64);
        } else {
            // SVG
            if(strpos($img_b64, 'image/svg') !== false) {
                if(strpos($img_b64, 'base64') < $comma_pos) {
                    $img_data = base64_decode(substr($img_b64, $comma_pos + 1));
                } else {
                    $img_data = urldecode(stripslashes(substr($img_b64, $comma_pos + 1)));
                }   
                $img_type = 'svg';

            // PNG
            } else {
                $img_data = base64_decode(substr($img_b64, $comma_pos + 1));
            }
        }

        // Make sure nonce matches.
        if(!isset($_POST['nonce']) || !check_ajax_referer('media-form_' . $this->plugin_slug, 'nonce')) {
            $resp['html'] = 'Sorry, your nonce did not verify.';

        /*
        // Check other submitted values.
        } elseif(!isset($_POST['img_type']) || $_POST['img_type'] == "") {
            $resp['html'] = 'Sorry, no image type was specified.';
         */

        //} elseif(strtolower($_POST['img_type']) == 'svg' && $this->options['allow_svg'] != 'yes') {
        } elseif(strtolower($img_type) == 'svg' && $this->options['allow_svg'] != 'yes') {
            $resp['html'] = 'Sorry, uploading SVG images has been disabled.';

        } elseif(!isset($_POST['img_data']) || $_POST['img_data'] == "") {
            $resp['html'] = 'Sorry, no image data was provided.';

        // Make sure we received nonempty content that is valid XML.
        } elseif(!isset($_POST['xml']) || $_POST['xml'] == "" || !$xml) {
            $resp['html'] = 'Sorry, invalid XML was received.';

        // Make sure this is associated with a post ID.
        } elseif(!isset($_POST['post_id']) || $_POST['post_id'] == "" || !ctype_digit($_POST['post_id'])) {
            $resp['html'] = 'Sorry, post ID was not an integer.';

        // All is well.
        } else {
            $post_id = (int) $_POST['post_id'];
            //$img_type = $_POST['img_type'];

            if(!isset($_POST['title']) || sanitize_file_name($_POST['title']) == "") {
                $title = $this->plugin_slug . '_diagram';
            } else {
                $title = $_POST['title'];
            }
            $file_title = sanitize_file_name($title . '.' . $img_type);

            // We want to set or override these attributes of the XML entity.
            $xml_attr = array(
                'grid'      => '0',
                'page'      => '0',
                'pageScale' => '1',
                'pan'       => '1',
                'zoom'      => '1',
                'resize'    => '1',
                'fit'       => '1',
                'nav'       => '0',
                'border'    => '0',
                'links'     => '1'
            );
            foreach($xml_attr as $key => $val) {
                // If attribute exists, then change it.
                if(isset($xml[$key])) {
                    $xml[$key] = $val;

                // If attribute doesn't exist, create it.
                } else {
                    $xml->addAttribute($key, $val);
                }
            }

            // Check for temp directory location.
            if($this->options['temp_dir'] == "wp_content") {
                $tempdir_base = wp_upload_dir();
                $tempdir = $tempdir_base['basedir'] . "/" . $this->plugin_slug . "_temp";

                // Temp dir doesn't exist, create it.
                if(!file_exists($tempdir)) {

                    // Couldn't create directory, use default setting instead.
                    if(!mkdir($tempdir)) {
                        $tempdir = get_temp_dir();
                    }
                }
            } else {
                $tempdir = get_temp_dir();
            }

            // Write the XML to a temp file.
            $tmpfname = tempnam($tempdir, "php");
            if(strtolower($img_type) == 'svg') {
                $ftmp = fopen($tmpfname, "w");
            } else {
                $ftmp = fopen($tmpfname, "wb");
            }
            $ftmp_size = fwrite($ftmp, $img_data);
            $ftmp_meta = stream_get_meta_data($ftmp);
            $file_array = array(
                'name' => $file_title,
                'tmp_name' => $ftmp_meta['uri'],
                'error' => '',
                'type' => $img_type,
                'size' => $ftmp_size
            );
            fclose($ftmp);

            // Check if any file renaming is needed (e.g., to avoid overwriting existing file).
            $file_array = apply_filters('wp_handle_upload_prefilter', $file_array);

            // Add file to uploads directory, add to media library and attach to post.
            $attach_id = media_handle_sideload($file_array, $post_id);

            // Get attachment URL and return the HTML to the post editor.
            if($attach_id) {
                if(!is_wp_error($attach_id)) {
                    // Update attachment metadata with plugin info.
                    $metadata = wp_get_attachment_metadata($attach_id);
                    if(is_array($metadata) && array_key_exists('image_meta', $metadata)) {
                        $image_meta = $metadata['image_meta'];
                    } else {
                        $image_meta = array();
                    }
                    $image_meta['is_' . $this->plugin_slug] = true;
                    $image_meta[$this->plugin_slug . '_xml'] = $xml->asXML();
                    $image_meta['title'] = $title;
                    $metadata['image_meta'] = $image_meta;
                    wp_update_attachment_metadata($attach_id, $metadata);

                    $file_url = wp_get_attachment_url($attach_id);
                    $resp['success'] = true;
                    $resp['att_id'] = $attach_id;
                    $resp['misc'] = $movefile['url'];

                    $img_html = wp_get_attachment_image($attach_id, 'full', false, array(
                        'class' => 'aligncenter wp-image-' . $attach_id,
                        'title' => htmlentities($title)
                    ));
                    if($img_html != '') {
                        $resp['html'] = $img_html;
                        
                    } else {
                        $resp['html'] = '<img class="' . $this->plugin_slug . '-img wp-image-' . $attach_id . '" src="' . $file_url . '" title="' . htmlentities($title) . '">';
                    }

                } else {
                    if($ftmp_size !== false) {
                        $resp['html'] = 'Sorry, could not insert attachment into media library. WP error: ' . $attach_id->get_error_message();
                    } else {
                        $resp['html'] = 'Sorry, could not save temp file to filesystem. WP error: ' . $attach_id->get_error_message();
                    }
                }

            } else {
                $resp['html'] = 'Sorry, file attachment failed.';
            }

            if(file_exists($tmpfname)) {
                unlink($tmpfname);
            }
        }

        echo json_encode($resp);
        exit;
    }

    // This is the actual iframe content for the editor.
    public function iframe() {
        $post_id = isset($_REQUEST['post_id']) ? intval( $_REQUEST['post_id'] ) : 0;
        $form_action_url = admin_url("admin-post.php?post_id=$post_id");
        $form_action_url = apply_filters('media_upload_form_url', $form_action_url);
        $form_class = 'media-upload-form type-form validate';
        $edit_xml = '';
        $edit_imgtype = '';
        $edit_imgdata = '';

        // Title of diagram.
        $diag_title = $this->plugin_slug . ' diagram';
        if(isset($_REQUEST['title'])) {
            $diag_title = $_REQUEST['title'];
        }

        // File type
        $save_type = $this->options['default_type'];

        if(isset($_REQUEST['img_id']) && $_REQUEST['img_id'] != "" && ctype_digit($_REQUEST['img_id'])) {
            $img_id = (int) $_REQUEST['img_id'];
            $metadata = wp_get_attachment_metadata($img_id);
            if($metadata !== false) {
                $image_meta = $metadata['image_meta'];
                $save_type = strtolower(end(explode('.', wp_get_attachment_url($img_id))));

                if($image_meta['title'] != "") {
                    $diag_title = $image_meta['title'];
                }

                if(array_key_exists('is_' . $this->plugin_slug, $image_meta) && array_key_exists($this->plugin_slug . '_xml', $image_meta)) {
                    $orig_xml = simplexml_load_string($image_meta[$this->plugin_slug . '_xml']);
                    if($orig_xml !== false) {
                        // Override these attributes of the XML entity for ease of editing existing diagram.
                        $xml_attr = array(
                            'grid'      => '1',
                            'page'      => '1',
                        );
                        foreach($xml_attr as $key => $val) {
                            // If attribute exists, then change it.
                            if(isset($orig_xml[$key])) {
                                $orig_xml[$key] = $val;

                            // If attribute doesn't exist, create it.
                            } else {
                                $orig_xml->addAttribute($key, $val);
                            }
                        }

                        $edit_xml = $orig_xml->asXML();
                    }
                }
            }
        }

        if ( get_user_setting('uploader') )
            $form_class .= ' html-uploader';
    ?>

        <?php if(function_exists('wp_nonce_field')) wp_nonce_field('media-form_' . $this->plugin_slug, $this->plugin_slug . '-nonce'); ?>
        <form class="<?php echo $this->plugin_slug; ?>-media-form" id="<?php echo $this->plugin_slug; ?>-form" method="post" action="">
            <input type="hidden" name="<?php echo $this->plugin_slug; ?>-action" value="submit-form-<?php echo $this->plugin_slug; ?>">
        </form>
        <input type="hidden" name="<?php echo $this->plugin_slug; ?>-post-id" id="<?php echo $this->plugin_slug; ?>-post-id" value="<?php echo (int) $post_id; ?>">
        <input type="hidden" id="<?php echo $this->plugin_slug; ?>-xml" value="<?php echo htmlspecialchars($edit_xml); ?>">
        <input type="hidden" id="<?php echo $this->plugin_slug; ?>-imgtype" value="<?php echo htmlspecialchars($edit_imgtype); ?>">
        <input type="hidden" id="<?php echo $this->plugin_slug; ?>-imgdata" value="<?php echo htmlspecialchars($edit_imgdata); ?>">
        <div class="<?php echo $this->plugin_slug; ?>-form-title-block"><label class="<?php echo $this->plugin_slug; ?>-form-label" for="<?php echo $this->plugin_slug; ?>-title">Title: </label><input type="text" class="<?php echo $this->plugin_slug; ?>-form-text-input" id="<?php echo $this->plugin_slug; ?>-title" name="<?php echo $this->plugin_slug; ?>-title" value="<?php echo htmlspecialchars($diag_title); ?>"> Filetype: <select id="<?php echo $this->plugin_slug; ?>-type" class="<?php echo $this->plugin_slug; ?>-type" name="type"><?php
            foreach($this->valid_types as &$tp) {
                if(strtolower($tp) == strtolower($save_type)) {
                    $select_type = " selected";
                } else {
                    $select_type = "";
                }
                echo '<option value="' . strtolower($tp) . '"' . $select_type . '>' . strtoupper($tp) . '</option>';
            }
            unset($tp);
            ?></select></div>
        <iframe class="<?php echo $this->plugin_slug; ?>-editor-iframe" id="<?php echo $this->plugin_slug; ?>-iframe" src="https://www.draw.io/?embed=1&analytics=0&gapi=0&db=0&od=0&proto=json&spin=1"></iframe>
        <div class="<?php echo $this->plugin_slug; ?>-editor-mask" id="<?php echo $this->plugin_slug; ?>-editor-mask" style="display:none;"><div class="<?php echo $this->plugin_slug; ?>-editor-saving">Saving...<div class="<?php echo $this->plugin_slug; ?>-editor-saving-x" onclick="jQuery('.<?php echo $this->plugin_slug; ?>-editor-mask').css('display','none');">x</div></div></div>

    <?php
    }

    // Add frontend text editor buttons
    public function user_init(){
        if(!is_admin()) {
            if (current_user_can('edit_posts') || current_user_can('edit_pages')) {
                $bttn_priority = FRONTEND_BUTTON_PRIORITY_DEFAULT;
                if(strtolower($this->options['override_conflict']) == 'yes') {
                    $bttn_priority = FRONTEND_BUTTON_PRIORITY_LAST - 100;
                }
                add_filter('mce_external_plugins', array($this, 'add_mce_plugin'), $bttn_priority);
                add_filter('mce_buttons', array($this, 'register_mce_button'), $bttn_priority);
            }
        }
    }

    // Plugin options page
    public function admin_init(){
        if (current_user_can('edit_posts') || current_user_can('edit_pages')) {
            add_filter('mce_external_plugins', array($this, 'add_mce_plugin'));
            add_filter('mce_buttons', array($this, 'register_mce_button'));
        }

        register_setting( $this->plugin_slug . '_options', $this->plugin_slug . '_options', array($this, 'options_validate') );
        add_settings_section($this->plugin_slug . '_img_type', 'Diagram Save-as Image Type', array($this, 'img_type_settings_section_text'), $this->plugin_slug);
        add_settings_field($this->plugin_slug . '_allow_svg', 'Allow uploading SVG', array($this, 'setting_allow_svg'), $this->plugin_slug, $this->plugin_slug . '_img_type');
        add_settings_field($this->plugin_slug . '_default_type', 'Default image type', array($this, 'setting_default_type'), $this->plugin_slug, $this->plugin_slug . '_img_type');

        add_settings_section($this->plugin_slug . '_advanced', 'Advanced Options', array($this, 'advanced_settings_section_text'), $this->plugin_slug);
        add_settings_field($this->plugin_slug . '_enable_frontend', 'Enable in frontend-based editors', array($this, 'setting_enable_frontend'), $this->plugin_slug, $this->plugin_slug . '_advanced');
        add_settings_field($this->plugin_slug . '_override_conflict', 'Override other plugins disabling the frontend buttons', array($this, 'setting_override_conflict'), $this->plugin_slug, $this->plugin_slug . '_advanced');
        add_settings_field($this->plugin_slug . '_temp_dir', 'Default temporary directory', array($this, 'setting_temp_dir'), $this->plugin_slug, $this->plugin_slug . '_advanced');
        /*
        add_settings_section($this->plugin_slug . '_diagram_size', 'Diagram Size Settings', array($this, 'diagram_settings_section_text'), $this->plugin_slug);
        add_settings_field($this->plugin_slug . '_default_width', 'Default diagram iframe width', array($this, 'setting_default_width'), $this->plugin_slug, $this->plugin_slug . '_iframe_size');
        add_settings_field($this->plugin_slug . '_default_height', 'Default diagram iframe height', array($this, 'setting_default_height'), $this->plugin_slug, $this->plugin_slug . '_iframe_size');
        add_settings_field($this->plugin_slug . '_max_width', 'Max diagram width', array($this, 'setting_max_width'), $this->plugin_slug, $this->plugin_slug . '_diagram_size');
        add_settings_field($this->plugin_slug . '_max_height', 'Max diagram height', array($this, 'setting_max_height'), $this->plugin_slug, $this->plugin_slug . '_diagram_size');
         */
    }

    public function admin_add_page() {
        add_options_page($this->plugin_label . ' (draw.io) Settings', $this->plugin_label . ' (draw.io)', 'manage_options', $this->plugin_slug, array($this, 'options_page'));
    }

    public function img_type_settings_section_text() {
        echo '<p>These settings specify if you would like to allow uploading of images in SVG format and the default image type to save as (either PNG or SVG). Note that whatever you choose for the default selection can be overridden per-diagram when saving a diagram.</p>';
        echo '<p class="' . $this->plugin_slug . '-warn-svg"><strong>WARNING:</strong> If you plan to use SVG images, you should be aware that you may have visual problems when viewed in ALL versions of Internet Explorer, which does not support the usage of the &quot;foreignObject&quot; tags that are used in these SVG images. These SVGs and the foreignObject tags are supported in pretty much any other modern browser, including Microsoft\'s new Edge browser.</p>';
    }

    public function advanced_settings_section_text() {
        echo '<p>These are various settings that generally you would not need to change as a typical user, unless you run into a specific problem or have a very customized server configuration. For example, users of the Beaver Builder frontend-based editor will need to enable both the frontend capabilities of this plugin, as well as overriding Beaver Builder\'s attempt to disable this plugin\'s custom editor buttons.</p>';
    }

    /*
    public function diagram_settings_section_text() {
        echo '<p>These settings specify the default size of the diagram/drawing in the post/page that you have created. Sizes must follow typical CSS syntax: a number followed by a unit of measurement (e.g., "100%", "400px", "6in", "35em", etc.). The diagram size will be the lesser of the "default" and "max" values for each dimension. The maximum numeric value that can be entered for any of these is 9999.</p><p><strong>NOTE:</strong> These values are only applied to newly created diagrams. A diagram\'s size can be maually adjusted when creating the diagram.</p>';
    }
     */

    // Displaying settings fields.
    public function setting_allow_svg() {
        if(strtolower($this->options['allow_svg']) == 'yes') {
            $yes_checked = " checked";
            $no_checked = "";
        } else {
            $no_checked = " checked";
            $yes_checked = "";
        }
        echo "<input type='radio' id='allow_svg' name='" . $this->plugin_slug . "_options[allow_svg]' value='yes'" . $yes_checked . "><label for='allow_svg'> Yes</label><br>";
        echo "<input type='radio' id='disallow_svg' name='" . $this->plugin_slug . "_options[allow_svg]' value='no'" . $no_checked . "><label for='disallow_svg'> No</label>";
    }

    public function setting_enable_frontend() {
        if(strtolower($this->options['enable_frontend']) == 'yes') {
            $yes_checked = " checked";
            $no_checked = "";
        } else {
            $no_checked = " checked";
            $yes_checked = "";
        }
        echo "<input type='radio' id='enable_frontend' name='" . $this->plugin_slug . "_options[enable_frontend]' value='yes'" . $yes_checked . "><label for='enable_frontend'> Yes</label><br>";
        echo "<input type='radio' id='disable_frontend' name='" . $this->plugin_slug . "_options[enable_frontend]' value='no'" . $no_checked . "><label for='disable_frontend'> No</label>";
    }

    public function setting_override_conflict() {
        if(strtolower($this->options['override_conflict']) == 'yes') {
            $yes_checked = " checked";
            $no_checked = "";
        } else {
            $no_checked = " checked";
            $yes_checked = "";
        }
        echo "<input type='radio' id='override_conflict' name='" . $this->plugin_slug . "_options[override_conflict]' value='yes'" . $yes_checked . "><label for='override_conflict'> Yes</label><br>";
        echo "<input type='radio' id='no_override_conflict' name='" . $this->plugin_slug . "_options[override_conflict]' value='no'" . $no_checked . "><label for='no_override_conflict'> No</label>";
    }

    public function setting_default_type() {
        echo "<select id='" . $this->plugin_slug . "_default_type' name='" . $this->plugin_slug . "_options[default_type]'>";
        foreach($this->valid_types as &$tp) {
            if(strtolower($this->options['default_type']) == strtolower($tp)) {
                $selected = " selected";
            } else {
                $selected = "";
            }
            echo "<option value='" . strtolower($tp) . "'" . $selected . ">" . strtoupper($tp) . "</option>";
        }
        unset($tp);
        echo "</select>";
    }

    public function setting_temp_dir() {
        //if(!array_key_exists('temp_dir', $this->options) || strtolower($this->options['temp_dir']) == 'wp_default') {
        $tempdir_base = wp_upload_dir();
        if(strtolower($this->options['temp_dir']) == 'wp_content') {
            $content_checked = " checked";
            $default_checked = "";
        } else {
            $default_checked = " checked";
            $content_checked = "";
        }
        echo "<input type='radio' id='tmp_wpdefault' name='" . $this->plugin_slug . "_options[temp_dir]' value='wp_default'" . $default_checked . "><label for='tmp_wpdefault'> Default system temp location, via get_temp_dir():</label><br><code>" . get_temp_dir() . "</code><br>";
        echo "<input type='radio' id='tmp_wpcontent' name='" . $this->plugin_slug . "_options[temp_dir]' value='wp_content'" . $content_checked . "><label for='tmp_wpcontent'> In wp-content/uploads:</label><br><code>" . $tempdir_base['basedir'] . "/" . $this->plugin_slug . "_temp</code><br>";
        echo '<p>This selects where to save the temporary files while saving a diagram. This is not the final location, only where it temporarily saves them during processing. Sometimes this setting needs to change if WordPress\'s built-in get_temp_dir() function does not return a valid temp directory location on your system.</p>';
    }

    /*
    public function setting_default_width() {
        echo "<input id='" . $this->plugin_slug . "_default_width' name='" . $this->plugin_slug . "_options[default_width]' size='10' type='text' value='{$this->options['default_width']}' />";
    }

    public function setting_default_height() {
        echo "<input id='" . $this->plugin_slug . "_default_height' name='" . $this->plugin_slug . "_options[default_height]' size='10' type='text' value='{$this->options['default_height']}' />";
    }

    public function setting_max_width() {
        echo "<input id='" . $this->plugin_slug . "_max_width' name='" . $this->plugin_slug . "_options[max_width]' size='10' type='text' value='{$this->options['max_width']}' />";
    }

    public function setting_max_height() {
        echo "<input id='" . $this->plugin_slug . "_max_height' name='" . $this->plugin_slug . "_options[max_height]' size='10' type='text' value='{$this->options['max_height']}' />";
    }
     */

    // Validating settings fields.
    public function options_validate($input) {
        $old_options = get_option($this->plugin_slug . '_options', $this->plugin_default_options);
        $opt = $old_options;
        $units = implode('|', $this->valid_units);
        $unit_pregmatch_str = '/^([0-9]{0,4}\.)?[0-9]{1,4}(' . $units . ')$/i';

        // Copy over values
        $opt['default_type'] = $input['default_type'];
        $opt['allow_svg'] = $input['allow_svg'];
        $opt['enable_frontend'] = $input['enable_frontend'];
        $opt['override_conflict'] = $input['override_conflict'];
        $opt['temp_dir'] = $input['temp_dir'];

        // Remove characters that might be commonly added by mistake.
        /*
        $opt['default_width'] = strtolower(preg_replace("/[\s\"]+/", "", $input['default_width']));
        $opt['default_height'] = strtolower(preg_replace("/[\s\"]+/", "", $input['default_height']));
        $opt['max_width'] = strtolower(preg_replace("/[\s\"]+/", "", $input['max_width']));
        $opt['max_height'] = strtolower(preg_replace("/[\s\"]+/", "", $input['max_height']));
         */

        // Default values for each field.
        if(!in_array($opt['default_type'], $this->valid_types)) {
            $opt['default_type'] = $this->plugin_default_options['default_type'];
        }

        if(strtolower($opt['allow_svg']) != 'yes' && strtolower($opt['allow_svg']) != 'no') {
            $opt['allow_svg'] = $this->plugin_default_options['allow_svg'];
        }

        if(strtolower($opt['enable_frontend']) != 'yes' && strtolower($opt['enable_frontend']) != 'no') {
            $opt['enable_frontend'] = $this->plugin_default_options['enable_frontend'];
        }

        if(strtolower($opt['override_conflict']) != 'yes' && strtolower($opt['override_conflict']) != 'no') {
            $opt['override_conflict'] = $this->plugin_default_options['override_conflict'];
        }


        // Default values for each field.
        if(!in_array($opt['temp_dir'], $this->valid_temp_dirs)) {
            $opt['temp_dir'] = $this->plugin_default_options['temp_dir'];
        }

        if(strtolower($opt['temp_dir']) != 'wp_default' && strtolower($opt['temp_dir']) != 'wp_content') {
            $opt['temp_dir'] = $this->plugin_default_options['temp_dir'];
        }

        /*
        if(!preg_match($unit_pregmatch_str, $opt['default_width'])) {
            $opt['default_width'] = array_key_exists('default_width', $old_options) ? $old_options['default_width'] : $this->plugin_default_options['default_width'];
        }

        if(!preg_match($unit_pregmatch_str, $opt['default_height'])) {
            $opt['default_height'] = array_key_exists('default_height', $old_options) ? $old_options['default_height'] : $this->plugin_default_options['default_height'];
        }

        if(!preg_match($unit_pregmatch_str, $opt['max_width'])) {
            $opt['max_width'] = array_key_exists('max_width', $old_options) ? $old_options['max_width'] : $this->plugin_default_options['max_width'];
        }

        if(!preg_match($unit_pregmatch_str, $opt['max_height'])) {
            $opt['max_height'] = array_key_exists('max_height', $old_options) ? $old_options['max_height'] : $this->plugin_default_options['max_height'];
        }
         */

        return $opt;
    }

    public function options_page() {
    ?>
    <div>
    <h2><?php echo $this->plugin_label; ?> (draw.io) Settings</h2>
    <hr>
    <form action="options.php" method="post">
    <?php settings_fields($this->plugin_slug . '_options'); ?>
    <?php do_settings_sections($this->plugin_slug); ?>
     
    <input name="Submit" type="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
    </form></div>
    <br>
    <hr>
    <h2>Plugin Version</h2>
    <p><?php echo $this->plugin_label . ' ' . $this->plugin_version; ?></p>
    <br>
    <hr>
    <h2>Frequently Asked Questions (FAQ)</h2>
    <h3>How do I edit a diagram?</h3>
    <p>To edit a diagram that you've already created, just select it (e.g., the source code in the text post editor or the image itself in the visual post editor) and then click on the DrawIt button in the editor!</p>
    <h3>How do I report a bug or feature request?</h3>
    <p>Please report all bugs and feature requests through the <a href="https://wordpress.org/support/plugin/drawit" title="DrawIt support - WordPress">DrawIt support page on WordPress</a> or through the <a href="https://plus.google.com/communities/112051242587930767153" title="DrawIt - Google+">Google+ DrawIt community</a>.</p>
    <h3>Where is the source code for my diagram saved?</h3>
    <p>The source code for the diagram is saved with the image in your WordPress installation. As long as you do not delete the image from your media library, then you will be able to open and edit the image from the post/page editor where it is being used.</p>
    <h3>How do I edit a diagram that is only in the media library and not inserted into a post?</h3>
    <p>For now, you'll have to insert it into a post to be able to edit it. We'll work on improving this later.</p>
     
    <hr>
    <?php
    }

    // TinyMCE editor buttons
    public function add_mce_plugin($plugin_array) {
        $plugin_array[$this->plugin_slug . '_mce_button'] = plugins_url('js/mce-btn.js', __FILE__);
        wp_localize_script($this->plugin_slug . '_mce_button', $this->plugin_slug . 'FE', array( 'mediaupload' => admin_url() . '/media-upload.php' ));
        return $plugin_array;
    }

    public function register_mce_button($buttons) {
        array_push($buttons, $this->plugin_slug . '_mce_button');
        return $buttons;
    }

    public function quicktags_add_button() {
        wp_enqueue_script('quicktags_' . $this->plugin_slug, plugins_url('js/qt-btn.js', __FILE__), array('quicktags'), $this->plugin_version);
        wp_localize_script('quicktags_' . $this->plugin_slug, $this->plugin_slug . 'FE', array( 'mediaupload' => admin_url() . '/media-upload.php' ));
    }

} // End class

$custom_plugin = new $plugin_slug($plugin_slug, $plugin_label, $plugin_default_options, $valid_types, $valid_units, $valid_temp_dirs);

?>
