<?php

defined('N2GSAP') || define('N2GSAP', 1);
if (!defined("N2_PLATFORM_LIBRARY")) define('N2_PLATFORM_LIBRARY', dirname(__FILE__) . '/wordpress');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'library.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'wordpress' . DIRECTORY_SEPARATOR . 'init.php');

class N2WP
{

    public static function init() {
        if (class_exists('N2Wordpress')) {
            N2WP::registerApplication();
        } else {
            add_action('nextend_loaded', 'N2WP::registerApplication');
        }
        add_action('admin_menu', 'N2WP::nextendApplicationInit');

    }

    public static function registerApplication() {
        N2Base::registerApplication(dirname(__FILE__) . '/library/applications/system/N2SystemApplicationInfo.php');
        N2Base::getApplication('system')->getApplicationType('backend');
    }

    public static function nextendApplicationInit() {
        add_submenu_page('options-general.php', 'Nextend', 'Nextend', 'nextend', 'nextend', 'N2WP::nextendApplication');

        function nextend_admin_menu() {
            echo '<style type="text/css">#adminmenu .toplevel_page_nextend .wp-menu-image img{opacity: 1;}</style>';
        }

        add_action('admin_head', 'nextend_admin_menu');
    }

    public static function nextendApplication() {
        N2Base::getApplication("system")->getApplicationType('backend')->setCurrent()->render(array(
            "controller" => "dashboard",
            "action"     => "index"
        ));
        n2_exit();
    }

    public static function install($network_wide) {
        global $wpdb;

        if (is_multisite() && $network_wide) {
            $tmpPrefix = $wpdb->prefix;
            $blogs     = function_exists('wp_get_sites') ? wp_get_sites(array('network_id' => $wpdb->siteid)) : get_blog_list(0, 'all');
            foreach ($blogs AS $blog) {
                $wpdb->prefix = $wpdb->get_blog_prefix($blog['blog_id']);

                N2Base::getApplication("system")->getApplicationType('backend')->render(array(
                    "controller" => "install",
                    "action"     => "index",
                    "useRequest" => false
                ), array(true));
            }

            $wpdb->prefix = $tmpPrefix;
            return true;
        }

        N2Base::getApplication("system")->getApplicationType('backend')->render(array(
            "controller" => "install",
            "action"     => "index",
            "useRequest" => false
        ), array(true));
    }

    public static function install_new_blog($blog_id) {
        global $wpdb;
        $tmpPrefix    = $wpdb->prefix;
        $wpdb->prefix = $wpdb->get_blog_prefix($blog_id);

        N2Base::getApplication("system")->getApplicationType('backend')->render(array(
            "controller" => "install",
            "action"     => "index",
            "useRequest" => false
        ), array(true));

        $wpdb->prefix = $tmpPrefix;
    }

    public static function delete_blog($blog_id, $drop) {
        if($drop){
            
            global $wpdb;
            $prefix = $wpdb->get_blog_prefix($blog_id);
            $wpdb->query('DROP TABLE IF EXISTS '.$prefix.'nextend2_image_storage, '.$prefix.'nextend2_section_storage;');
          
        }
    }
}

N2WP::init();
