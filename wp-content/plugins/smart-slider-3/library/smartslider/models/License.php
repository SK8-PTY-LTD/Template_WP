<?php

class N2SmartsliderLicenseModel {

    private $key;

    public function __construct() {
        $this->key = N2Base::getApplication('smartslider')->storage->get('license', 'key');
    
    }

    public static function getInstance() {
        static $ins;
        if (!$ins) {
            $ins = new N2SmartsliderLicenseModel();
        }
        return $ins;
    }

    public function hasKey() {
        return !empty($this->key);
    }

    public function maybeActive() {
        $lastActive = N2Base::getApplication('smartslider')->storage->get('license', 'isActive');
        if ($lastActive && $lastActive > strtotime("-1 week")) {
            return true;
        }
        return false;
    }

    public function getKey() {
        return $this->key;
    }

    public function setKey($licenseKey) {
        N2Base::getApplication('smartslider')->storage->set('license', 'key', $licenseKey);
        $this->key = $licenseKey;
    
    }

    public function checkKey($license, $action = 'licensecheck') {
        return 0;
    
    }

    public function isActive($cacheAccepted = true) {
        if ($cacheAccepted && $this->maybeActive()) {
            return 'OK';
        }
        $status = $this->checkKey($this->key);
        if ($this->hasKey() && $status == 'OK') {
            N2Base::getApplication('smartslider')->storage->set('license', 'isActive', time());
            return $status;
        }
        N2Base::getApplication('smartslider')->storage->set('license', 'isActive', 0);
        return $status;
    }

    public function deAuthorize() {
        if ($this->hasKey()) {
            $status = $this->checkKey($this->key, 'licensedeauthorize');
            if ($status == 'OK' || $status == 'LICENSE_EXPIRED' || $status == 'PLATFORM_NOT_ALLOWED') {
                $this->setKey('');
                N2Message::notice('Smart Slider deauthorized on this site!');
            }
            return $status;
        }
        return false;
    }
}