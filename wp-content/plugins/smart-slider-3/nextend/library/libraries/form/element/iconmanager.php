<?php

N2Loader::import('libraries.form.element.hidden');
N2Loader::import('libraries.form.form');

class N2ElementIconManager extends N2ElementHidden
{

    public $_tooltip = true;

    function fetchElement() {

        $html = N2Html::tag('div', array(
            'class' => 'n2-form-element-text n2-form-element-icon n2-border-radius'
        ), N2Html::image(N2Image::base64Transparent(), '', array(
                'class' => 'n2-form-element-preview'
            )) . '<a id="' . $this->_id . '_edit" class="n2-form-element-button n2-h5 n2-uc" href="#">' . n2_('Choose') . '</a>' . parent::fetchElement());

        N2JS::addInline('
            new NextendElementIconManager("' . $this->_id . '");
        ');
        return $html;
    }

}