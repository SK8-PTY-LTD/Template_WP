<?php

/**
 * Class N2Filesystem
 */
class N2Filesystem extends N2FilesystemAbstract {

    public $paths = array();

    public function __construct() {
        $this->_basepath    = realpath(WP_CONTENT_DIR);
        $this->_librarypath = str_replace($this->_basepath, '', N2LIBRARY);

        $this->paths[] = $this->_basepath;

        $wp_upload_dir = wp_upload_dir();
        $uploadPath    = rtrim(realpath($wp_upload_dir['basedir']), "/\\");
        if (strpos($this->_basepath, $uploadPath) !== 0) {
            $this->paths[] = $uploadPath;
        }

        self::measurePermission(N2Platform::getPublicDir());
    }

    public static function getImagesFolder() {
        return N2Platform::getPublicDir();
    }

    public static function getWebCachePath() {
        if (!NEXTEND_CUSTOM_CACHE) {
            self::check(self::getBasePath(), 'cache');
        }
        if (is_multisite()) {
            return self::getBasePath() . NEXTEND_RELATIVE_CACHE_WEB . get_current_blog_id();
        }
        return self::getBasePath() . NEXTEND_RELATIVE_CACHE_WEB;
    }

    public static function getNotWebCachePath() {
        if (is_multisite()) {
            return self::getBasePath() . NEXTEND_RELATIVE_CACHE_NOTWEB . get_current_blog_id();
        }
        return self::getBasePath() . NEXTEND_RELATIVE_CACHE_NOTWEB;
    }

    public static function getPaths() {
        $i = N2Filesystem::getInstance();
        return $i->paths;
    }

    public static function absoluteURLToPath($url) {
        $uris = N2Uri::getUris();
        foreach ($uris AS $i => $uri) {
            if (substr($url, 0, strlen($uri)) == $uri) {
                $ins = N2Filesystem::getInstance();
                return str_replace($uri, $ins->paths[$i], $url);
            }
        }
        return $url;
    }
}