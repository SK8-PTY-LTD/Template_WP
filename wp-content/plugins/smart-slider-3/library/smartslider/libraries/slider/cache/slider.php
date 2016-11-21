<?php

class N2CacheManifestSlider extends N2CacheManifest {

    private $parameters = array();

    public function __construct($cacheId, $parameters = array()) {
        parent::__construct($cacheId, false);
        $this->parameters = $parameters;

    }

    public function makeCache($fileName, $hash, $callable) {
        $variations = 1;
        if (N2Filesystem::existsFile($this->getManifestFilePath('variations'))) {
            $variations = intval(N2Filesystem::readFile($this->getManifestFilePath('variations')));
        }
        return parent::makeCache($fileName . mt_rand(1, $variations), $hash, $callable);
    }

    protected function isCacheValid(&$manifestData) {

        if (!isset($manifestData['version']) || $manifestData['version'] != N2SS3::$version) {
            return false;
        }

        if (N2SmartSliderHelper::getInstance()
                               ->isSliderChanged($this->parameters['slider']->sliderId, 1)
        ) {
            $this->clearCurrentGroup();
            N2SmartSliderHelper::getInstance()
                               ->setSliderChanged($this->parameters['slider']->sliderId, 0);
            return false;
        }

        $time = N2Platform::getTime();

        if ($manifestData['nextCacheRefresh'] < $time) {
            return false;
        }

        if (!isset($manifestData['currentPath']) || $manifestData['currentPath'] != md5($this->currentPath)) {
            return false;
        }

        return true;
    }

    protected function addManifestData(&$manifestData) {

        $manifestData['nextCacheRefresh'] = N2Pluggable::applyFilters('SSNextCacheRefresh', $this->parameters['slider']->slidesBuilder->getNextCacheRefresh(), array($this->parameters['slider']));
        $manifestData['currentPath']      = md5($this->currentPath);
        $manifestData['version']          = N2SS3::$version;

        $variations = 1;

        $params = $this->parameters['slider']->params;
        if ($params->get('randomize', 0) || $params->get('randomizeFirst', 0)) {
            $variations = intval($params->get('variations', 5));
            if ($variations < 1) {
                $variations = 1;
            }
        }

        N2Filesystem::createFile($this->getManifestFilePath('variations'), $variations);
    }
}