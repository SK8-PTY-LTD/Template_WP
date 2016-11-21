<?php
define('N2WORDPRESS', 1);
define('N2JOOMLA', 0);
define('N2MAGENTO', 0);
define('N2NATIVE', 0);

class N2Wordpress {

    private static $outputStarted = false;
    public static $nextend_js = '', $nextend_css = '', $nextend_wp_head = false, $nextend_wp_footer = false;

    public static function init() {

        add_action('after_setup_theme', 'N2Wordpress::afterSetupTheme');

        if (is_admin()) {
            add_action('admin_init', 'N2Wordpress::outputStart', 3000);
            add_action('vc_admin_inline_editor', 'N2Wordpress::outputStart'); // Visual composer inline editor fix
        } else if (N2Settings::get('safemode')) {
            add_action('wp', 'N2Wordpress::outputStart', 30000);
        } else {
            add_action('plugins_loaded', 'N2Wordpress::plugins_loaded');
        }
    }

    public static function plugins_loaded() {
        if (class_exists('CWS_PageLinksTo')) {
            // Fix for an issue with https://wordpress.org/plugins/page-links-to/
            add_action('wp_enqueue_scripts', 'N2Wordpress::outputStart', -10000);
        } else if (class_exists('WPSEO_Frontend')) {
            // Fix for an issue with Yoast SEO
            add_action('template_redirect', 'N2Wordpress::outputStart');
        } else {
            add_action('wp_enqueue_scripts', 'N2Wordpress::outputStart');
            add_action('wp_head', 'N2Wordpress::outputStart');
        }
    }

    public static function afterSetupTheme() {
        if (class_exists('HeadwayDisplay', false)) {
            add_action('headway_html_close', 'N2Wordpress::afterOutputEnd');
        } else {
            add_action('wp_footer', 'N2Wordpress::afterOutputEnd');
        }
        add_action('admin_footer', 'N2Wordpress::afterOutputEnd');
    }

    public static function outputStart() {
        if (self::$outputStarted) return;
        self::$outputStarted = true;
        if (class_exists('The_Neverending_Home_Page', false) && The_Neverending_Home_Page::got_infinity()) {
            add_filter('infinite_scroll_results', "N2Wordpress::infiniteScrollRenderEnd", 1, 3);
        } else {
            self::$nextend_wp_head = true;

            if (N2Settings::get('safemode') != 1) {
                ob_start("N2Wordpress::platformRenderEnd");
                ob_start();
                if(class_exists('\\Warp\\Warp', false)){
                    ob_start();
                }
            }
        }
    }

    public static function infiniteScrollRenderEnd($results, $query_args, $wp_query) {

        if (defined('N2LIBRARY')) {
            ob_start();

            do_action('nextend_css');
            do_action('nextend_js');
            if (class_exists('N2AssetsManager')) {
                echo N2AssetsManager::getCSS();
                echo N2AssetsManager::getJs();
            }
            $results['html'] = ob_get_clean() . $results['html'];
        }
        return $results;
    }

    public static function afterOutputEnd() {
        self::$nextend_wp_footer = true;

        if (defined('N2LIBRARY')) {
            ob_start();
            do_action('nextend_css');
            if (class_exists('N2AssetsManager')) {
                echo N2AssetsManager::getCSS();
            }
            self::$nextend_css = ob_get_clean();

            ob_start();
            do_action('nextend_js');
            if (class_exists('N2AssetsManager')) {
                echo N2AssetsManager::getJs();
            }
            self::$nextend_js = ob_get_clean();

        }
        if (N2Settings::get('safemode') == 1) echo self::$nextend_js;
        return true;
    }

    public static function platformRenderEnd($buffer) {
        if (self::$nextend_css != '' && strpos($buffer, '<!--n2css-->') !== false) {
            $buffer            = str_replace('<!--n2css-->', self::$nextend_css, $buffer);
            self::$nextend_css = '';
        }
        
        if (self::$nextend_css != '' || self::$nextend_js != '') {
            return preg_replace('/<\/head>/', self::$nextend_css . self::$nextend_js . '</head>', $buffer, 1);
        }
        return $buffer;
    }
}

N2Wordpress::init();

do_action('nextend_loaded');

function nextend_comment_for_css() {
    echo "<!--n2css-->";
}

add_action('wp_print_scripts', 'nextend_comment_for_css');