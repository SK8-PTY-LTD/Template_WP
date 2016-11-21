<?php

class N2SS3 {

    public static $version = '3.0.37';

    public static $product = 'smartslider3';

    public static $campaign = 'smartslider3';

    public static $source = '';

    public static function getProUrlHome($params = array()) {
        if (!empty(self::$source)) {
            $params['source'] = self::$source;
        }
        return 'http://smartslider3.com/?' . http_build_query($params);
    }

    public static function getProUrlPricing($params = array()) {
        if (!empty(self::$source)) {
            $params['source'] = self::$source;
        }
        return 'http://smartslider3.com/pricing/?' . http_build_query($params);
    }

    public static function getWhyProUrl($params = array()) {
        if (!empty(self::$source)) {
            $params['source'] = self::$source;
        }
        return 'http://smartslider3.com/why-upgrade-to-pro/?' . http_build_query($params);
    }

    public static function getUpdateInfo() {
        return array(
            'name'   => 'smartslider3',
            'plugin' => 'nextend-smart-slider3-pro/nextend-smart-slider3-pro.php'
        );
    }

    public static function api($_posts, $returnUrl = false) {

        $posts = array(
            'product' => self::$product,
            'pro'     => N2SSPRO
        );
        return N2::api($_posts + $posts, $returnUrl);
    }

    public static function hasApiError($status, $data = array()) {
        extract($data);
        switch ($status) {
            case 'OK':
                return false;
            case 'PRODUCT_ASSET_NOT_AVAILABLE':
                N2Message::error(sprintf(n2_('Demo slider is not available with the following ID: %s'), $key));
            case 'ASSET_PREMIUM':
                N2Message::error('Premium sliders are available in PRO version only!');
                break;
            case 'LICENSE_EXPIRED':
                N2Message::error('Your license key expired!');
                break;
            case 'DOMAIN_REGISTER_FAILED':
                N2Message::error('Your license key authorized on a different domain!');
                break;
            case 'LICENSE_INVALID':
                N2Message::error('Your license key invalid, please enter again!');
                N2SmartsliderLicenseModel::getInstance()
                                         ->setKey('');
                return array(
                    "sliders/index"
                );
                break;
            case 'UPDATE_ERROR':
                N2Message::error('Update error, please update manually!');
                break;
            case 'PLATFORM_NOT_ALLOWED':
                N2Message::error(sprintf('Your license key is not valid for Smart Slider3 - %s!', N2Platform::getPlatformName()));
                break;
            case 'ERROR_HANDLED':
                break;
            case null:
                N2Message::error('Licensing server not reachable, try again later!');
                break;
            default:
                N2Message::error('Debug: ' . $status);
                N2Message::error('Licensing server not reachable, try again later!');
                break;
        }
        return true;
    }

    public static function showBeacon($search = '') {
        if (intval(N2SmartSliderSettings::get('beacon', 1))) {
            echo '<script>!function(e,o,n){window.HSCW=o,window.HS=n,n.beacon=n.beacon||{};var t=n.beacon;t.userConfig={},t.readyQueue=[],t.config=function(e){this.userConfig=e},t.ready=function(e){this.readyQueue.push(e)},o.config={docs:{enabled:!0,baseUrl:"//smart-slider-3.helpscoutdocs.com/"},contact:{enabled:!0,formId:"5bf2183c-77e2-11e5-8846-0e599dc12a51"}};var r=e.getElementsByTagName("script")[0],c=e.createElement("script");c.type="text/javascript",c.async=!0,c.src="https://djtflbt20bdde.cloudfront.net/",r.parentNode.insertBefore(c,r)}(document,window.HSCW||{},window.HS||{});HS.beacon.ready(function () {HS.beacon.search("' . $search . '");});</script>';
        }
    }
}
if (defined('SMARTSLIDER3AFFILIATE')) {
    N2SS3::$source = SMARTSLIDER3AFFILIATE;
}
N2SS3::$source = apply_filters('smartslider3_hoplink', N2SS3::$source);


