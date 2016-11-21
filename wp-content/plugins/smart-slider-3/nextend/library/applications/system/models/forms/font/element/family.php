<?php
N2Loader::import('libraries.form.element.text');

class N2ElementFamily extends N2ElementText
{

    function fetchElement() {
        $html         = parent::fetchElement();
        $fontSettings = N2Fonts::loadSettings();
        $families     = explode("\n", $fontSettings['preset-families']);
        sort($families);
        N2JS::addInline('new NextendElementAutocompleteSimple("' . $this->_id . '", ' . json_encode($families) . ');');
        return $html;
    }

    protected function getClass() {
        return 'n2-form-element-autocomplete ui-front ' . parent::getClass();
    }
}