<?php

class N2SmartsliderApplicationTypeBackend extends N2ApplicationType
{

    public $type = "backend";

    protected function autoload() {
        N2Loader::import(array(
            'libraries.embedwidget.embedwidget',
            'libraries.plugin.plugin',
            'libraries.form.form',
            'libraries.image.color',
            'libraries.mobiledetect.Mobile_Detect',
            'libraries.parse.parse'
        ));

        N2Loader::import(array(
            'libraries.settings.settings'
        ), 'smartslider');

        N2Loader::import('helpers.controller.N2SmartSliderController', 'smartslider.backend');
    }

}