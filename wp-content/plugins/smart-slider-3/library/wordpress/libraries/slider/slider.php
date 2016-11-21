<?php

class N2SmartSlider extends N2SmartSliderAbstract {

    public function __construct($sliderId, $parameters) {
        parent::__construct($sliderId, $parameters);
    }

    public function parseSlider($slider) {
        // TODO: Implement parseSlider() method.
        return $slider;
    }

    public function addCMSFunctions($slider) {

        $slider = apply_filters('translate_text', do_shortcode(preg_replace('/\[smartslider3 slider=[0-9]+\]/', '', preg_replace('/\[smartslider3 slider="[0-9]+"\]/', '', $slider))));

        return $slider;
    }


} 