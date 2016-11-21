<?php

class N2AssetsPredefined {

    public static function backend($force = false) {
        static $once;
        if ($once != null && !$force) {
            return;
        }
        $once   = true;
        $family = n2_x('Montserrat', 'Default Google font family for admin');
        foreach (explode(',', n2_x('latin', 'Default Google font charset for admin')) AS $subset) {
            N2GoogleFonts::addSubset($subset);
        }
        N2GoogleFonts::addFont($family);

        N2CSS::addInline('.n2,html[dir="rtl"] .n2,.n2 td,.n2 th,.n2 select, .n2 textarea, .n2 input{font-family: "' . $family . '", Arial, sans-serif;}');
        N2CSS::addStaticGroup(N2LIBRARYASSETS . '/dist/nextend-backend.min.css', 'nextend-backend');
    

        N2Localization::addJS(array(
            'Cancel',
            'Delete',
            'Delete and never show again',
            'Are you sure you want to delete?',
            'Documentation'
        ));
        wp_enqueue_script('nextend-ui', N2Uri::pathToUri(N2LIBRARYASSETS . "/js/core/jquery/ui/jquery-ui.nextend.js"), array(
            'jquery-ui-core',
            'jquery-ui-widget',
            'jquery-ui-autocomplete',
            'jquery-ui-draggable',
            'jquery-ui-droppable',
            'jquery-ui-resizable',
            'jquery-ui-sortable',
            'jquery-ui-slider'
        ), '1.0', 1);
        wp_enqueue_script('nextend-ui-iframe-transport', N2Uri::pathToUri(N2LIBRARYASSETS . "/js/core/jquery/ui/jquery.iframe-transport.js"), array(
            'jquery-ui-core',
            'jquery-ui-widget'
        ), '1.0', 1);
        wp_enqueue_script('nextend-ui-fileupload', N2Uri::pathToUri(N2LIBRARYASSETS . "/js/core/jquery/ui/jquery.fileupload.js"), array(
            'jquery-ui-core',
            'jquery-ui-widget'
        ), '1.0', 1);
    
        N2JS::addStaticGroup(N2LIBRARYASSETS . '/dist/nextend-backend.min.js', 'nextend-backend');
    


        N2Base::getApplication('system')->info->assetsBackend();
        N2JS::addFirstCode("NextendAjaxHelper.addAjaxArray(" . json_encode(N2Form::tokenizeUrl()) . ");");

        N2Plugin::callPlugin('fontservices', 'onFontManagerLoadBackend');
    }

    public static function frontend($force = false) {
        static $once;
        if ($once != null && !$force) {
            return;
        }
        $once = true;
        N2AssetsManager::getInstance();
        if (N2Platform::$isAdmin) {
            N2JS::addInline('window.N2PRO=' . N2PRO . ';', true);
            N2JS::addInline('window.N2GSAP=' . N2GSAP . ';', true);
            N2JS::addInline('window.N2PLATFORM="' . N2Platform::getPlatform() . '";', true);
        }
    

        N2JS::addInline('window.nextend={localization: {}, deferreds:[], loadScript: function(url){n2jQuery.ready(function () {nextend.deferreds.push(n2.ajax({url:url,dataType:"script",cache:true,error:function(){console.log(arguments)}}))})}, ready: function(cb){n2.when.apply(n2, nextend.deferreds).done(function(){cb.call(window,n2)})}};', true);

        N2JS::jQuery($force);

        self::animation($force);
        N2JS::addStaticGroup(N2LIBRARYASSETS . "/dist/nextend-frontend.min.js", 'nextend-frontend');
    

        N2Loader::import('libraries.fonts.fonts');
        N2Plugin::callPlugin('fontservices', 'onFontManagerLoad', array($force));
    }

    private static function form($force = false) {
        static $once;
        if ($once != null && !$force) {
            return;
        }
        $once = true;

        N2JS::addFiles(N2LIBRARYASSETS . "/js", array(
            'form.js',
            'element.js'
        ), 'nextend-backend');

        N2Localization::addJS('The changes you made will be lost if you navigate away from this page.');


        N2JS::addFiles(N2LIBRARYASSETS . "/js/element", array(
            'text.js'
        ), 'nextend-backend');

        foreach (glob(N2LIBRARYASSETS . "/js/element/*.js") AS $file) {
            N2JS::addFile($file, 'nextend-backend');
        }
    }

    private static function animation($force = false) {
        static $once;
        if ($once != null && !$force) {
            return;
        }
        $once = true;

        if (N2Pluggable::hasAction('animationFramework')) {
            N2Pluggable::doAction('animationFramework');
        } else {
            if (N2Settings::get('gsap') || N2Platform::$isAdmin) {
                N2JS::addStaticGroup(N2LIBRARYASSETS . "/dist/nextend-gsap.min.js", 'nextend-gsap');
            
            } else {
                N2JS::addInline(N2Filesystem::readFile(N2LIBRARYASSETS . "/js/core/gsap/NextendTimeline.js"));
            }
        }
    }

    public static function custom_animation_framework() {
    }

    public static function loadLiteBox() {
        N2CSS::addStaticGroup(N2LIBRARYASSETS . '/css/litebox.min.css', 'nextend-litebox');
        N2JS::addStaticGroup(N2LIBRARYASSETS . '/dist/nextend-litebox.min.js', 'nextend-litebox');
    
    }
}