<?php

class N2SmartsliderBackendGeneratorController extends N2SmartSliderController
{

    public $layoutName = 'default';

    public function initialize() {
        parent::initialize();

        N2Loader::import(array(
            'models.generator',
            'models.Sliders',
            'models.Slides'
        ), 'smartslider');
    }

    public function actionCreate() {
        if ($this->validatePermission('smartslider_edit')) {

            $sliderID     = N2Request::getInt("sliderid", 0);
            $slidersModel = new N2SmartsliderSlidersModel();
            $slider       = $slidersModel->get($sliderID);
            if ($this->validateDatabase($slider)) {
                $this->addView("create", array(
                    "slider" => $slider
                ));
                $this->render();
            }
        }
    }

    public function actionEdit() {
        if ($this->validatePermission('smartslider_edit')) {

            $generatorId = N2Request::getInt('generator_id');

            $generatorModel = new N2SmartsliderGeneratorModel();
            $generator      = $generatorModel->get($generatorId);
            if ($this->validateDatabase($generator)) {

                $slidesModel = new N2SmartsliderSlidesModel();
                $slides      = $slidesModel->getAll(-1, 'OR generator_id = ' . $generator['id'] . '');
                if (count($slides) > 0) {
                    $slide = $slides[0];


                    if (N2Request::getInt('save')) {
                        $request = new N2Data(N2Request::getVar('generator'));

                        $slideParams = new N2Data($slide['params'], true);
                        $slideParams->set('record-slides', $request->get('record-slides', 1));
                        $slidesModel->updateParams($slide['id'], $slideParams->toArray());

                        $request->un_set('record-slides');
                        $generatorModel->save($generatorId, $request->toArray());

                        N2SmartsliderSlidesModel::markChanged($slide['slider']);
                        N2Message::success(n2_('Generator updated and cache cleared.'));

                        $this->redirect(array(
                            "generator/edit",
                            array(
                                "generator_id" => $generatorId
                            )
                        ), 302, true);
                    }

                    N2Request::set('sliderid', $slide['slider']);

                    $this->addView("../../inline/_sliders", array(
                        "appObj" => $this
                    ), "sidebar");
                    $this->addView("edit", array(
                        "generatorModel" => $generatorModel,
                        "generator"      => $generator,
                        "slide"          => $slide
                    ));
                    $this->render();
                } else {
                    $this->redirect(array(
                        "sliders/index"
                    ), 302, true);
                }
            } else {
                $this->redirect(array(
                    "sliders/index"
                ), 302, true);

            }
        }
    }

    public function actionCreateSettings() {
        if ($this->validatePermission('smartslider_edit')) {
            $slidersModel = new N2SmartsliderSlidersModel();
            if (!($slider = $slidersModel->get(N2Request::getInt('sliderid')))) {
                $this->redirectToSliders();
            }

            if (N2Request::getInt('save')) {

                $generatorModel = new N2SmartsliderGeneratorModel();
                $result         = $generatorModel->createGenerator($slider['id'], N2Request::getVar('generator'));

                N2Message::success(n2_('Generator created.'));

                $this->redirect(array(
                    "slides/edit",
                    array(
                        "sliderid" => $slider['id'],
                        "slideid"  => $result['slideId']
                    )
                ), 302, true);
            }

            $this->addView("create_settings", array(
                'slider' => $slider
            ));
            $this->render();
        }
    }

    public function actionCheckConfiguration() {
        if ($this->validatePermission('smartslider_edit')) {
            $this->actionConfigure(true);
        }
    }

    public function actionConfigure($create = false) {
        if ($this->validatePermission('smartslider_config')) {

            $generatorModel = new N2SmartsliderGeneratorModel();

            $group = N2Request::getVar('group');
            $type  = N2Request::getVar('type');

            $info = $generatorModel->getGeneratorInfo($group, $type);

            $configuration = $info->getConfiguration();

            if (N2Request::getInt('save')) {
                if ($this->validateToken()) {
                    $configuration->addData(N2Request::getVar('generator'));
                    $this->refresh();
                } else {
                    $this->refresh();
                }
            }


            $this->addView("../../inline/_sidebar_settings", array(), "sidebar");

            if ($create == false || !$configuration->wellConfigured()) {

                $this->addView("check_configuration", array(
                    'configuration' => $configuration
                ));
                $this->render();


            } else {

                $this->redirect(array(
                    "generator/createsettings",
                    array(
                        "sliderid" => N2Request::getInt('sliderid'),
                        "group"    => $group,
                        "type"     => $type
                    )
                ), 302, true);
            }
        }

    }

    public function actionFinishAuth() {
        if ($this->validatePermission('smartslider_config')) {

            $generatorModel = new N2SmartsliderGeneratorModel();

            $group = N2Request::getVar('group');
            $type  = N2Request::getVar('type');

            $info = $generatorModel->getGeneratorInfo($group, $type);

            $configuration = $info->getConfiguration();
            $result        = $configuration->finishAuth();
            if ($result === true) {
                N2Message::success(n2_('Authentication successful.'));
                echo '<script>window.opener.location.reload();self.close();</script>';
            } else {
                if ($result instanceof Exception) {
                    $message = $result->getMessage();
                } else {
                    $message = 'Something wrong with the credentials';
                }
                echo '<script>window.opener.nextend.notificationCenter.error("' . htmlspecialchars($message) . '");self.close();</script>';
            }
            n2_exit(true);
        }
    }
}