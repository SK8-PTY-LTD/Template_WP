<?php
N2Loader::import('libraries.form.element.text');

class N2ElementNumber extends N2ElementText
{

    function fetchElement() {

        $min = N2XmlHelper::getAttribute($this->_xml, 'min');
        if ($min == '') {
            $min = '-Number.MAX_VALUE';
        }

        $max = N2XmlHelper::getAttribute($this->_xml, 'max');
        if ($max == '') {
            $max = 'Number.MAX_VALUE';
        }

        N2JS::addInline('new NextendElementNumber("' . $this->_id . '", ' . $min . ', ' . $max . ');');

        $html = N2Html::openTag('div', array(
            'class' => 'n2-form-element-text ' . $this->getClass() . ($this->_xml->unit ? 'n2-text-has-unit ' : '') . 'n2-border-radius',
            'style' => ($this->fieldType == 'hidden' ? 'display: none;' : '')
        ));

        $subLabel = N2XmlHelper::getAttribute($this->_xml, 'sublabel');
        if ($subLabel) {
            $html .= N2Html::tag('div', array(
                'class' => 'n2-text-sub-label n2-h5 n2-uc'
            ), n2_($subLabel));
        }

        $html .= $this->pre();

        $html .= N2Html::tag('input', array(
            'type'         => $this->fieldType,
            'id'           => $this->_id,
            'name'         => $this->_inputname,
            'value'        => $this->_form->get($this->_name, $this->_default),
            'class'        => 'n2-h5',
            'style'        => $this->getStyle(),
            'autocomplete' => 'off'
        ), false);

        $html .= $this->post();

        if ($this->_xml->unit) {
            $html .= N2Html::tag('div', array(
                'class' => 'n2-text-unit n2-h5 n2-uc'
            ), n2_((string)$this->_xml->unit));
        }
        $html .= "</div>";
        return $html;
    }

    protected function getClass() {
        return 'n2-form-element-number ';
    }
}