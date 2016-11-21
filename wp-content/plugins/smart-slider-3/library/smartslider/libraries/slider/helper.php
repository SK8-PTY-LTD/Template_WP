<?php

class N2SmartSliderHelper
{

    /**
     * @var N2Application
     */
    private $application;

    public function __construct($application) {
        $this->application = $application;
    }

    /**
     * @return N2SmartSliderHelper
     */
    public static function getInstance() {
        static $instance = null;
        if ($instance === null) {
            $instance = new self(N2Base::getApplication('smartslider'));
        }
        return $instance;
    }

    public function isSliderChanged($sliderId, $value = 1) {
        return intval($this->application->storage->get('sliderChanged', $sliderId, $value));
    }

    public function setSliderChanged($sliderId, $value = 1) {
        $this->application->storage->set('sliderChanged', $sliderId, $value);
    }
}