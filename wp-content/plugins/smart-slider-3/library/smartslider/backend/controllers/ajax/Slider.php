<?php

class N2SmartsliderBackendSliderControllerAjax extends N2SmartSliderControllerAjax {

    public function initialize() {
        parent::initialize();

        N2Loader::import(array(
            'models.Ajax',
            'models.Sliders'
        ), 'smartslider');
    }

    public function actionCreate() {
        $this->validateToken();
        $this->validatePermission('smartslider_edit');
        $slidersModel = new N2SmartsliderSlidersModel();

        $title = N2Request::getVar('sliderTitle');
        $this->validateVariable(!empty($title), 'slider name');

        $slider = array(
            'type'   => 'simple',
            'title'  => N2Request::getVar('sliderTitle'),
            'width'  => max(N2Request::getInt('sliderSizeWidth', 800), 50),
            'height' => max(N2Request::getInt('sliderSizeHeight', 500), 50)
        );

        $preset = N2Request::getVar('preset');
        switch ($preset) {
            case 'fullwidth':
                $slider['responsive-mode'] = 'fullwidth';
                $slider['widgetarrow']     = 'imageEmpty';
                break;
            case 'fullpage':
                $slider['responsive-mode'] = 'fullpage';
                $slider['widgetarrow']     = 'imageEmpty';
                break;
            case 'block':
                $slider['type']            = 'block';
                $slider['responsive-mode'] = 'fullwidth';
                break;
            case 'showcase':
                $slider['type']         = 'showcase';
                $slider['slide-width']  = intval($slider['width'] * 0.8);
                $slider['slide-height'] = intval($slider['height'] * 0.8);
                break;
            case 'carousel':
                $slider['responsiveScaleUp'] = 1;
                $slider['type']              = 'carousel';
                $slider['slide-width']       = intval(($slider['width'] - 40) * 0.32);
                $slider['slide-height']      = intval($slider['height'] * 0.8);
                $slider['widgetbullet']      = 'transitionRectangle';
                $slider['widgetarrow']       = 'disabled';
                break;
            case 'thumbnailhorizontal':
                $slider['widgetthumbnail'] = 'default';
                break;
            case 'thumbnailvertical':
                $slider['widgetthumbnail']                = 'default';
                $slider['widget-thumbnail-position-area'] = '8';
                break;
            case 'caption':
                $slider['widgetarrow'] = 'imageEmpty';
                $slider['widgetbar']   = 'horizontalFull';
                break;
            case 'horizontalaccordion':
                $slider['type']        = 'accordion';
                $slider['orientation'] = 'horizontal';
                break;
            case 'verticalaccordion':
                $slider['type']        = 'accordion';
                $slider['orientation'] = 'vertical';
                break;
            default:
                $slider['widgetarrow'] = 'imageEmpty';
        }

        $sliderid = $slidersModel->create($slider);

        N2Message::success(n2_('Slider created.'));

        $this->response->redirect(array(
            "slider/edit",
            array("sliderid" => $sliderid)
        ));
    }

    public function actionEdit() {
        $this->validateToken();
        $this->validatePermission('smartslider_edit');

        if (N2Request::getInt('save')) {

            $slidersModel = new N2SmartsliderSlidersModel();

            $slider = $slidersModel->get(N2Request::getInt('sliderid'));
            $this->validateDatabase($slider);
            if ($sliderid = $slidersModel->save($slider['id'], N2Request::getVar('slider'))) {
                N2Message::success(n2_('Slider saved.'));
                $this->response->respond();
            }
        }

        $response = null;

        $id = N2Request::getCmd('id');

        $ajaxModel = new N2SmartSliderAjaxModel();

        ob_start();
        switch ($id) {
            case 'slidertype':
                $response = $ajaxModel->sliderType($this->appType);
                break;
            case 'sliderresponsivemode':
                $response = $ajaxModel->sliderResponsiveMode($this->appType);
                break;
            case 'sliderwidgetarrow':
                $response = $ajaxModel->sliderWidget($this->appType, 'arrow');
                break;
            case 'sliderwidgetbullet':
                $response = $ajaxModel->sliderWidget($this->appType, 'bullet');
                break;
            case 'sliderwidgetautoplay':
                $response = $ajaxModel->sliderWidget($this->appType, 'autoplay');
                break;
            case 'sliderwidgetindicator':
                $response = $ajaxModel->sliderWidget($this->appType, 'indicator');
                break;
            case 'sliderwidgetfullscreen':
                $response = $ajaxModel->sliderWidget($this->appType, 'fullscreen');
                break;
            case 'sliderwidgetbar':
                $response = $ajaxModel->sliderWidget($this->appType, 'bar');
                break;
            case 'sliderwidgetthumbnail':
                $response = $ajaxModel->sliderWidget($this->appType, 'thumbnail');
                break;
            case 'sliderwidgetshadow':
                $response = $ajaxModel->sliderWidget($this->appType, 'shadow');
                break;
            case 'sliderwidgethtml':
                $response = $ajaxModel->sliderWidget($this->appType, 'html');
                break;
        }

        if ($response == null) {
            $response = array(
                'html'   => '',
                'script' => ''
            );
        }
        $response['html'] .= ob_get_clean();

        $this->response->respond($response);
    }

    public function actionImportDemo() {
        $this->validateToken();
        $this->validatePermission('smartslider_edit');

        $key = 'http:' . base64_decode(N2Request::getVar('key'));
        if (strpos($key, 'http://smartslider3.com/') !== 0) {
            N2Message::error(sprintf(n2_('Import url is not valid: %s'), $key));
            $this->response->error();
        }
        N2Base::getApplication('smartslider')->storage->set('free', 'subscribeOnImport', 1);
    

        $posts  = array(
            'action' => 'asset',
            'asset'  => $key
        );
        $result = N2SS3::api($posts);

        if (!is_string($result)) {
            $hasError = N2SS3::hasApiError($result['status'], array(
                'key' => $key
            ));
            if (is_array($hasError)) {
                $this->redirect($hasError);
            } else if ($hasError !== false) {
                $this->response->error();
            }
        } else {

            N2Loader::import(array(
                'models.Sliders',
                'models.Slides'
            ), 'smartslider');

            N2Loader::import('libraries.import', 'smartslider');

            $import   = new N2SmartSliderImport();
            $sliderId = $import->import($result, 'clone', 1, false);

            if ($sliderId !== false) {
                N2Message::success(n2_('Slider imported.'));

                $this->response->redirect(array(
                    "slider/edit",
                    array("sliderid" => $sliderId)
                ));
            } else {
                N2Message::error(n2_('Import error!'));
                $this->response->error();
            }
        }

        $this->response->respond();
    }
} 