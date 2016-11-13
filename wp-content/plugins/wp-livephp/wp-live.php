<?php

/*
  Plugin Name: WP-Live.php
  Description: Automatically refresh your browser if you update a post, or change any files in your theme or plugins directory
  Author: Bence Meszaros
  Author URI: http://bencemeszaros.com
  Plugin URI: http://wordpress.org/extend/plugins/wp-livephp/
  Version: 1.6.1
  License: GPL2
 */
/*  Copyright 2011  Bence Meszaros  (email : bence@bencemeszaros.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if (!class_exists('LivePhp'))
{
    class LivePhp
    {
        protected $contentCheckFile = 'wp-live-contentcheck.txt';

        /** options array */
        protected $options = array();

        /**
         * Constructor
         */
        public function __construct()
        {
            add_action('init', array(&$this, 'init'));
        }

        /**
         * Initialize the wordpress plugin
         * On the visitor side: load the javascript
         * On the admin side: add menu items and dactivation hook
         */
        public function init()
        {
            $this->getOptions();

            if (!empty($this->options['adminbar']))
            {
                add_action('admin_bar_menu', array(&$this, 'add_admin_bar_link'), 900);
                add_action('wp_head', array(&$this, 'header_scripts'));
            }

            add_action('admin_head', array(&$this, 'header_scripts'));

            // frontend init
            if (!is_admin())
            {
                // autorefresh for frontend
                if (1 == $this->options['frontend'])
                {
                    wp_enqueue_script('wp-live-php', plugins_url('wp-live.js', __FILE__));
                }
            }
            // backend init
            else
            {
                // admin panel init
                register_deactivation_hook( __FILE__, array(&$this, 'deactivate') );
                add_action('admin_menu', array(&$this, 'adminMenu'));
                add_action('wp_ajax_livephp-settings', array(&$this, 'ajaxHandler'));

                if (1 == $this->options['content']) {
                    add_action( 'save_post', array(&$this, 'touchContentCheckFile') );
                }
                // autorefresh for wp-admin backend
                if (1 == $this->options['backend'])
                {
                    wp_enqueue_script('wp-live-php', plugins_url('wp-live.js', __FILE__));
                }
            }
        }

        public function header_scripts()
        {
            $path = plugins_url( '/images/' , __FILE__);
            $ajax_nonce = wp_create_nonce("wp-livephp-top-secret");
            $key = is_admin() ? 'backend' : 'frontend';
    ?>
    <script>
    if(typeof(jQuery)!="undefined") {
        <?php if (!is_admin()) : ?>
        if (typeof(ajaxurl) == "undefined") {
            var ajaxurl = "<?php echo admin_url('admin-ajax.php') ?>";
        }
        <?php endif ?>
        function live_option_switch(opt, state, reload) {
            reload = typeof(reload) == "undefined" ? false : reload;
            var data = {
                action: "livephp-settings",
                security: '<?php echo $ajax_nonce; ?>',
                option: opt,
                state:  state
            };
            jQuery.post(ajaxurl, data, function(response) {
                if ('content' == opt && state) {
                    if (response != '') {
                        // error updating the option
                        jQuery('#enable_content').click();
                    }
                    jQuery('#wp-livephp_contenterror').html(response);
                }
                if (reload) {
                    location.reload();
                }
            });
        }
        jQuery(document).ready(function(){
            jQuery('#wp-admin-bar-livephp a').click(function(){
                var h = jQuery('span', this).toggleClass('livephp-off').hasClass('livephp-off') ? 0 : 1;
                live_option_switch('<?php echo $key ?>', h, true);
                jQuery(this).blur();

                return false;
            });
        });
    }
    </script>
    <style>
        #wpadminbar .livephp-icon {
            float: left;
            position: relative;
            width: 40px;
            background: url('<?php echo $path ?>/live-logo.png') no-repeat;
            color: #64a8ff;
            text-align: center;

        }
        #wpadminbar .livephp-off {
    /*        background-position: 0 -28px;*/
            color: #ccc;
        }
    </style>
    <?php
        }

        public function add_admin_bar_link()
        {
            global $wp_admin_bar;
            $key = is_admin() ? 'backend' : 'frontend';
            $c = $this->options[$key] ? '' : 'livephp-off';
            $wp_admin_bar->add_menu( array(
                'id' => 'livephp',
                'title' => '<span class="livephp-icon ' . $c . '">Live</span>',
                'href' => '#',
                'meta'  => array(
                    'title' => __('Enable Live.php monitoring'),
                )
            ));
        }

        /**
         * Get the settings from wp_options table
         * Or add it if none found
         */
        protected function getOptions()
        {
            $this->options = get_option('wp-livephp');
            // check for settings
            if (empty($this->options))
            {
                $this->options = array('frontend' => 1, 'backend' => 0, 'adminbar' => 1);
                add_option('wp-livephp', $this->options);
            }
            if (!isset($this->options['frontend']))
            {
                $this->options['frontend'] = 1;
                update_option('wp-livephp', $this->options);
            }
            if (!isset($this->options['backend']))
            {
                $this->options['backend'] = 0;
                update_option('wp-livephp', $this->options);
            }
            if (!isset($this->options['content']))
            {
                $this->options['content'] = 0;
                update_option('wp-livephp', $this->options);
            }
            if (!isset($this->options['adminbar']))
            {
                $this->options['adminbar'] = 1;
                update_option('wp-livephp', $this->options);
            }
            // set the content check file path
            $upload_dir = wp_upload_dir();
            $this->contentCheckFile = $upload_dir['basedir'] . '/' . $this->contentCheckFile;
        }

        /**
         * On deactivateing the plugin, we remove the options record from wp_options
         */
        public function deactivate()
        {
            delete_option('wp-livephp');
            // remove the content check file
            unlink($this->contentCheckFile);
        }

        public function adminMenu()
        {
            add_options_page('WP Live.php', 'WP Live.php', 'manage_options', 'wp-livephp', array(&$this, 'settingsPage'));
        }

        /**
         * Content check file updater
         * This is called at the save_post hook, to trigger a refresh at every post/page save
         */
        public function touchContentCheckFile()
        {
            touch($this->contentCheckFile);
        }

        /**
         * Ajax handler
         */
        public function ajaxHandler() {
                check_ajax_referer( 'wp-livephp-top-secret', 'security' );
                if (isset($_POST['option']) && isset($_POST['state']))
                {
                    if ('frontend' == $_POST['option'])
                    {
                        $this->options['frontend'] = $_POST['state'];
                    }
                    if ('backend' == $_POST['option'])
                    {
                        $this->options['backend'] = $_POST['state'];
                    }
                    if ('content' == $_POST['option'])
                    {
                        $this->options['content'] = $_POST['state'];
                        // check content updates file
                        if ($_POST['state']) {
                            if (!touch($this->contentCheckFile)) {
                                // file touch failed
                                echo 'Error: Unable to create content check file at<br>' . $this->contentCheckFile;
                            }
                        }
                        else {
                            // remove the file
                            unlink($this->contentCheckFile);
                        }
                    }
                    if ('adminbar' == $_POST['option'])
                    {
                        $this->options['adminbar'] = $_POST['state'];
                    }
                    update_option('wp-livephp', $this->options);
                }

                die();
        }

        /**
         * Settings page
         */
        public function settingsPage()
        {
            if (!current_user_can('manage_options'))
            {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }

            if (isset($_POST['enable']))
            {
                $this->options['frontend'] = $_POST['enable'];
                update_option('wp-livephp', $this->options);
            }
            $adminbar = $this->options['adminbar'] ? 'checked' : '';

            include_once('wp-live-settings.php');

            $this->javascript();
        }

        protected function javascript()
        {
            $frontend = $this->options['frontend'] ? 'on' : 'off';
            $backend  = $this->options['backend'] ? 'on' : 'off';
            $content  = $this->options['content'] ? 'on' : 'off';
            $path = plugins_url( '/images/' , __FILE__);
    ?>
    <script>
    if(typeof(jQuery) != "undefined") {
    /** iphone style switch by Ashley Ford */
        jQuery.fn.iphoneSwitch = function(start_state, switched_on_callback, switched_off_callback, options) {
            var state = start_state == 'on' ? start_state : 'off';
            // define default settings
            var settings = {
                mouse_over: 'pointer',
                mouse_out:  'default',
                switch_on_container_path: '<?php echo $path ?>iphone_switch_container_on.png',
                switch_off_container_path: '<?php echo $path ?>iphone_switch_container_off.png',
                switch_path: '<?php echo $path ?>iphone_switch.png',
                switch_height: 27,
                switch_width: 94
            };
            if(options) {
                jQuery.extend(settings, options);
            }
            // create the switch
            return this.each(function() {
                var container;
                var image;
                // make the container
                container = jQuery('<div class="iphone_switch_container" style="height:'+settings.switch_height+'px; width:'+settings.switch_width+'px; position: relative; overflow: hidden"></div>');
                // make the switch image based on starting state
                image = jQuery('<img class="iphone_switch" style="height:'+settings.switch_height+'px; width:'+settings.switch_width+'px; background-image:url('+settings.switch_path+'); background-repeat:none; background-position:'+(state == 'on' ? 0 : -53)+'px" src="'+(state == 'on' ? settings.switch_on_container_path : settings.switch_off_container_path)+'" /></div>');
                // insert into placeholder
                jQuery(this).html(jQuery(container).html(jQuery(image)));
                jQuery(this).mouseover(function(){
                    jQuery(this).css("cursor", settings.mouse_over);
                });
                jQuery(this).mouseout(function(){
                    jQuery(this).css("background", settings.mouse_out);
                });
                // click handling
                jQuery(this).click(function() {
                    if(state == 'on') {
                        jQuery(this).find('.iphone_switch').animate({backgroundPosition: -53}, 150, function() {
                            jQuery(this).attr('src', settings.switch_off_container_path);
                            switched_off_callback();
                        });
                        state = 'off';
                    }
                    else {
                        jQuery(this).find('.iphone_switch').animate({backgroundPosition: 0}, 150, function() {
                            switched_on_callback();
                        });
                        jQuery(this).find('.iphone_switch').attr('src', settings.switch_on_container_path);
                        state = 'on';
                    }
                });
            });
        }

        jQuery('#enable_frontend').iphoneSwitch("<?php echo $frontend ?>",
        function() { live_option_switch('frontend', 1);},
        function() { live_option_switch('frontend', 0);},
        {});
        jQuery('#enable_backend').iphoneSwitch("<?php echo $backend ?>",
        function() { live_option_switch('backend', 1, true);},
        function() { live_option_switch('backend', 0, true);},
        {});
        jQuery('#enable_content').iphoneSwitch("<?php echo $content ?>",
        function() { live_option_switch('content', 1);},
        function() { live_option_switch('content', 0);},
        {});
        jQuery('#livephp-adminbar').click(function(){
            live_option_switch('adminbar', jQuery(this).is(':checked') ? 1 : 0, true);
        });
    }
    </script>
    <?php
        }
    }

    new LivePhp;
}