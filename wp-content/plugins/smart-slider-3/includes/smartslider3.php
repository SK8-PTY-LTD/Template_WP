<?php
define('NEXTEND_SMARTSLIDER_3_URL', plugins_url(NEXTEND_SMARTSLIDER_3_URL_PATH));

if (!class_exists('N2WP', false)) {
    require_once(dirname(NEXTEND_SMARTSLIDER_3__FILE__) . '/nextend/nextend.php');
    require_once(dirname(NEXTEND_SMARTSLIDER_3__FILE__) . '/library/smartslider/smartslider3.php');
}

class N2_SMARTSLIDER_3 {

    public static function init() {
        if (class_exists('N2Wordpress')) {
            N2_SMARTSLIDER_3::registerApplication();
        } else {
            add_action('nextend_loaded', 'N2_SMARTSLIDER_3::registerApplication');
        }

        add_action('init', 'N2_SMARTSLIDER_3::_init');

        add_action('admin_menu', 'N2_SMARTSLIDER_3::nextendAdminInit');

        add_action('network_admin_menu', 'N2_SMARTSLIDER_3::nextendNetworkAdminInit');

        register_activation_hook(NEXTEND_SMARTSLIDER_3__FILE__, 'N2_SMARTSLIDER_3::install');
        add_action('upgrader_process_complete', 'N2_SMARTSLIDER_3::upgrade', 10, 2);

        add_action('wpmu_new_blog', 'N2_SMARTSLIDER_3::install_new_blog');
        add_action('delete_blog', 'N2_SMARTSLIDER_3::delete_blog', 10, 2);

        require_once dirname(NEXTEND_SMARTSLIDER_3__FILE__) . DIRECTORY_SEPARATOR . 'includes/shortcode.php';
        require_once dirname(NEXTEND_SMARTSLIDER_3__FILE__) . DIRECTORY_SEPARATOR . 'includes/widget.php';
        require_once dirname(NEXTEND_SMARTSLIDER_3__FILE__) . DIRECTORY_SEPARATOR . 'editor' . DIRECTORY_SEPARATOR . 'shortcode.php';

        add_action('et_builder_ready', 'N2_SMARTSLIDER_3::Divi_load_module');

        add_action('vc_after_set_mode', 'N2_SMARTSLIDER_3::initVisualComposer');

        if (class_exists('FLBuilderModel', false)) {
            add_action('fl_builder_before_render_module', 'N2_SMARTSLIDER_3::removeShortcodeBeaverBuilder');
        }
    }

    public static function removeShortcodeBeaverBuilder() {
        if (FLBuilderModel::is_builder_active()) {
            N2_SMARTSLIDER_3::removeShortcode();
        }
    }

    public static function removeShortcode() {
        remove_shortcode('smartslider3');
    }

    public static function registerApplication() {

        N2Base::registerApplication(dirname(NEXTEND_SMARTSLIDER_3__FILE__) . '/library/smartslider/N2SmartsliderApplicationInfo.php');
    }

    public static function _init() {
        N2Loader::import('libraries.settings.settings', 'smartslider');
        if (current_user_can('smartslider_edit') && intval(N2SmartSliderSettings::get('wp-adminbar', 1))) {
            add_action('admin_bar_menu', 'N2_SMARTSLIDER_3::admin_bar_menu', 81);
        }
    }

    public static function nextendAdminInit() {
        $icon = NEXTEND_SMARTSLIDER_3_URL . '/icon.png';
        if (isset($_REQUEST['page']) && $_REQUEST['page'] == NEXTEND_SMARTSLIDER_3_URL_PATH) {
            $icon = NEXTEND_SMARTSLIDER_3_URL . '/icon-active.png';
        }


        add_menu_page('Smart Slider', 'Smart Slider', 'smartslider', NEXTEND_SMARTSLIDER_3_URL_PATH, 'N2_SMARTSLIDER_3::application', $icon);

        function nextend_smart_slider_admin_menu() {
            echo '<style type="text/css">#adminmenu .toplevel_page_' . NEXTEND_SMARTSLIDER_3_URL_PATH . ' .wp-menu-image img{opacity: 1;}</style>';
        }

        add_action('admin_head', 'nextend_smart_slider_admin_menu');
    }

    public static function nextendNetworkAdminInit() {
        $icon = NEXTEND_SMARTSLIDER_3_URL . '/icon.png';
        add_menu_page('Smart Slider Update', 'Smart Slider Update', 'smartslider', NEXTEND_SMARTSLIDER_3_URL_PATH, 'N2_SMARTSLIDER_3::networkUpdate', $icon);

        function nextend_smart_slider_admin_menu() {
            echo '<style type="text/css">#adminmenu .toplevel_page_' . NEXTEND_SMARTSLIDER_3_URL_PATH . '{display: none;}</style>';
        }

        add_action('admin_head', 'nextend_smart_slider_admin_menu');
    }

    public static function networkUpdate() {
        N2Base::getApplication("smartslider")
              ->getApplicationType('backend')
              ->setCurrent()
              ->render(array(
                  "controller" => 'update',
                  "action"     => 'update'
              ));
        n2_exit();
    }

    public static function application($dummy, $controller = 'sliders', $action = 'index') {
        N2Base::getApplication("smartslider")
              ->getApplicationType('backend')
              ->setCurrent()
              ->render(array(
                  "controller" => $controller,
                  "action"     => $action
              ));
        n2_exit();
    }

    public static function install($network_wide) {
        global $wpdb;

        N2WP::install($network_wide);

        if (is_multisite() && $network_wide) {
            $tmpPrefix = $wpdb->prefix;
            $blogs     = function_exists('wp_get_sites') ? wp_get_sites(array('network_id' => $wpdb->siteid)) : get_blog_list(0, 'all');
            foreach ($blogs AS $blog) {
                $wpdb->prefix = $wpdb->get_blog_prefix($blog['blog_id']);

                N2Base::getApplication("smartslider")
                      ->getApplicationType('backend')
                      ->render(array(
                          "controller" => "install",
                          "action"     => "index",
                          "useRequest" => false
                      ), array(true));
            }

            $wpdb->prefix = $tmpPrefix;
            return true;
        }

        N2Base::getApplication("smartslider")
              ->getApplicationType('backend')
              ->render(array(
                  "controller" => "install",
                  "action"     => "index",
                  "useRequest" => false
              ), array(true));
    }

    public static function upgrade($upgrader_object, $options) {
        if (isset($options['plugins']) && is_array($options['plugins']) && $options['action'] == 'update' && $options['type'] == 'plugin') {
            foreach ($options['plugins'] as $plugin) {
                if ($plugin == NEXTEND_SMARTSLIDER_3_BASENAME) {
                    self::install(true);
                }
            }
        }
    }

    public static function install_new_blog($blog_id) {
        global $wpdb;
        N2WP::install_new_blog($blog_id);

        $tmpPrefix    = $wpdb->prefix;
        $wpdb->prefix = $wpdb->get_blog_prefix($blog_id);

        N2Base::getApplication("smartslider")
              ->getApplicationType('backend')
              ->render(array(
                  "controller" => "install",
                  "action"     => "index",
                  "useRequest" => false
              ), array(true));

        $wpdb->prefix = $tmpPrefix;
    }

    public static function delete_blog($blog_id, $drop) {
        if ($drop) {
            N2WP::delete_blog($blog_id, $drop);

            global $wpdb;
            $prefix = $wpdb->get_blog_prefix($blog_id);
            $wpdb->query('DROP TABLE IF EXISTS ' . $prefix . 'nextend2_smartslider3_generators, ' . $prefix . 'nextend2_smartslider3_sliders,	' . $prefix . 'nextend2_smartslider3_slides;');

        }
    }

    public static function Divi_load_module() {
        require_once dirname(__FILE__) . '/divi.php';
    }

    public static function initVisualComposer() {
        require_once dirname(__FILE__) . '/vc.php';
    }

    /**
     * @param WP_Admin_Bar $wp_admin_bar
     */
    public static function admin_bar_menu($wp_admin_bar) {
        global $wpdb;

        $wp_admin_bar->add_node(array(
            'id'     => 'new_content_smart_slider',
            'parent' => 'new-content',
            'title'  => 'Slider [Smart Slider 3]',
            'href'   => admin_url("admin.php?page=" . NEXTEND_SMARTSLIDER_3_URL_PATH . '#createslider')
        ));

        $wp_admin_bar->add_node(array(
            'id'    => 'smart_slider_3',
            'title' => 'Smart Slider',
            'href'  => admin_url("admin.php?page=" . NEXTEND_SMARTSLIDER_3_URL_PATH)
        ));

        $wp_admin_bar->add_node(array(
            'id'     => 'smart_slider_3_dashboard',
            'parent' => 'smart_slider_3',
            'title'  => 'Dashboard',
            'href'   => admin_url("admin.php?page=" . NEXTEND_SMARTSLIDER_3_URL_PATH)
        ));

        $wp_admin_bar->add_node(array(
            'id'     => 'smart_slider_3_create_slider',
            'parent' => 'smart_slider_3',
            'title'  => 'Create slider',
            'href'   => admin_url("admin.php?page=" . NEXTEND_SMARTSLIDER_3_URL_PATH . '#createslider')
        ));

        $query   = 'SELECT sliders.title, sliders.id, slides.thumbnail
            FROM ' . $wpdb->prefix . 'nextend2_smartslider3_sliders AS sliders
            LEFT JOIN ' . $wpdb->prefix . 'nextend2_smartslider3_slides AS slides ON slides.id = (SELECT id FROM ' . $wpdb->prefix . 'nextend2_smartslider3_slides WHERE slider = sliders.id AND published = 1 AND generator_id = 0 AND thumbnail NOT LIKE \'\' ORDER BY ordering DESC LIMIT 1)
            ORDER BY time DESC LIMIT 10';
        $sliders = $wpdb->get_results($query, ARRAY_A);

        if (count($sliders)) {

            $wp_admin_bar->add_node(array(
                'id'     => 'smart_slider_3_edit',
                'parent' => 'smart_slider_3',
                'title'  => 'Edit slider',
                'href'   => admin_url("admin.php?page=" . NEXTEND_SMARTSLIDER_3_URL_PATH)
            ));

            foreach ($sliders AS $slider) {
                $wp_admin_bar->add_node(array(
                    'id'     => 'smart_slider_3_slider_' . $slider['id'],
                    'parent' => 'smart_slider_3_edit',
                    'title'  => '#' . $slider['id'] . ' - ' . $slider['title'],
                    'href'   => admin_url("admin.php?page=" . NEXTEND_SMARTSLIDER_3_URL_PATH . '&nextendcontroller=slider&nextendaction=edit&sliderid=' . $slider['id'])
                ));
            }

            if (count($sliders) == 10) {
                $wp_admin_bar->add_node(array(
                    'id'     => 'smart_slider_3_slider_view_all',
                    'parent' => 'smart_slider_3_edit',
                    'title'  => 'View all',
                    'href'   => admin_url("admin.php?page=" . NEXTEND_SMARTSLIDER_3_URL_PATH)
                ));
            }
        }
    }
}

N2_SMARTSLIDER_3::init();