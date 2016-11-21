<?php

class N2SmartsliderLayersModel extends N2Model
{

    function renderForm($data = array()) {

        N2Loader::import('libraries.animations.manager');

        $configurationXmlFile = dirname(__FILE__) . '/forms/layer.xml';

        N2Loader::import('libraries.form.form');
        $form = new N2Form();
        $form->loadArray($data);

        $form->loadXMLFile($configurationXmlFile);

        echo $form->render('layer');
    }

} 