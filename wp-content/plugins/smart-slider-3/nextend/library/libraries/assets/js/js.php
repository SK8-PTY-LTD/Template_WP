<?php

class N2JS {

    public static function addFile($pathToFile, $group) {
        N2AssetsManager::$js->addFile($pathToFile, $group);
    }

    public static function addFiles($path, $files, $group) {
        N2AssetsManager::$js->addFiles($path, $files, $group);
    }

    public static function addStaticGroup($file, $group) {
        N2AssetsManager::$js->addStaticGroup($file, $group);
    }

    public static function addCode($code, $group) {
        N2AssetsManager::$js->addCode($code, $group);
    }

    public static function addUrl($url) {
        N2AssetsManager::$js->addUrl($url);
    }

    public static function addFirstCode($code, $unshift = false) {
        N2AssetsManager::$js->addFirstCode($code, $unshift);
    }

    public static function addInline($code, $global = false) {
        N2AssetsManager::$js->addInline($code, $global);
    }

    public static function jQuery($force = false) {
        if ($force) {
            self::addFiles(ABSPATH . '/wp-includes/js/jquery/', array(
                "jquery.js",
                "jquery-migrate.min.js"
            ), "n2");
            self::addFiles(N2LIBRARYASSETS . '/js/core/jquery', array(
                "njQuery.js"
            ), "n2");
        } else {
            wp_enqueue_script('jquery');

            if (N2Settings::get('async', '0')) {
                self::addInline(file_get_contents(N2LIBRARYASSETS . '/js/core/jquery/njQuery.js'), true);
            } else {
                self::addFiles(N2LIBRARYASSETS . '/js/core/jquery', array(
                    "njQuery.js"
                ), "n2");
            }
        }
    
    }

    public static function modernizr() {
        self::addFile(N2LIBRARYASSETS . '/js/core/modernizr/modernizr.js', "nextend-frontend");
    }

} 