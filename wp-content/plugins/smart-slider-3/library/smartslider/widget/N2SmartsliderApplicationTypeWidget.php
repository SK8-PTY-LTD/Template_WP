<?php

class N2SmartsliderApplicationTypeWidget extends N2ApplicationType
{

    public $type = "widget";

    public function __construct($app, $appTypePath) {
        parent::__construct($app, $appTypePath);

        N2AssetsManager::addCachedGroup('core');
        N2AssetsManager::addCachedGroup('smartslider');
    }

    protected function autoload() {
        N2Loader::import(array(
            'libraries.cache.NextendModuleCache',
            'libraries.embedwidget.embedwidget',
        ));

        N2Loader::import(array(
            'libraries.settings.settings'
        ), 'smartslider');
    }
}

