<?php

class N2SmartsliderApplicationInfo extends N2ApplicationInfo {

    public function __construct() {
        $this->path      = dirname(__FILE__);
        $this->assetPath = realpath(dirname(__FILE__) . "/../media");
        parent::__construct();
    }

    public function isPublic() {
        return true;
    }

    public function getName() {
        return 'smartslider';
    }

    public function getLabel() {
        return 'Smart Slider';
    }

    public function getInstance() {
        require_once $this->path . NDS . "N2SmartsliderApplication.php";
        return new N2SmartSliderApplication($this);
    }

    public function getPathKey() {
        return '$ss$';
    }

    public function onNextendBaseReady() {
        parent::onNextendBaseReady();

        require_once dirname(__FILE__) . '/libraries/storage.php';
    }

    public function assetsBackend() {
        static $once;
        if ($once != null) {
            return;
        }
        $once = true;

        $path = $this->getAssetsPath();
        N2CSS::addInline('.n2-expert {display: none !important;');
    
        N2CSS::addStaticGroup($path . '/admin/css/smartslider.min.css', 'smartslider-backend');

        if (intval(N2SmartSliderSettings::get('improved-small-screen', 1))) {
            N2CSS::addStaticGroup($path . '/admin/css/smallscreen.min.css', 'smartslider-smallscreen-backend');
        }
    

        N2Localization::addJS(array(
            'Insert',
            'Insert variable',
            'Choose the group',
            'Choose the variable',
            'Result',
            'Filter',
            'No',
            'Clean HTML',
            'Remove HTML',
            'Split',
            'Chars',
            'Words',
            'Start',
            'Length',
            'Find image',
            'Index',
            'Find link',
            'Index'
        ));
        N2JS::addStaticGroup($path . '/dist/smartslider-backend.min.js', 'smartslider-backend');
        if (!N2Base::getApplication('smartslider')->storage->get('free', 'rated')) {
            N2JS::addInline('window.N2SSWHYPRO="' . N2SS3::getWhyProUrl(array(
                    'utm_source'   => 'why-pro',
                    'utm_medium'   => 'smartslider-' . N2Platform::getPlatform() . '-free',
                    'utm_campaign' => N2SS3::$campaign
                )) . '";', true);
            N2JS::addStaticGroup($path . '/dist/smartslider-free.min.js', 'smartslider-free');
        }
    
        if (!N2Base::getApplication('smartslider')->storage->get('free', 'subscribeOnImport')) {
            N2JS::addInline('
        nextend.joinCommunity = function(cb){
            var $ = n2;
            var modal = new NextendModal({
                zero: {
                    size: [
                        600, 460
                    ],
                    title: n2_(\'' . n2_('Join the Nextend community!') . '\'),
                    back: false,
                    close: true,
                    content: \'<form class="n2-form"></form>\',
                    fn: {
                        show: function () {

                            var form = this.content.find(\'.n2-form\');

                            form.append(\'<img src="' . N2ImageHelper::fixed('$ss$/admin/images/free/newsletter.jpg') . '" />\');
                            form.append(this.createHeading("Join more than 12,000 subscribers and get access to the latest Sample Sliders, Tip & tricks and other exclusive contents directly to your inbox!"));

                            form.append(\'<div class="n2-input-button"><input type="email" id="EMAIL" name="EMAIL" value="" /><a href="#" class="n2-button n2-button-big n2-button-green n2-uc n2-h4">Get Free Sliders</a></div>\');


                            form.append(\'<input type="hidden" name="' . strtoupper(N2Platform::getPlatform()) . '" value="Yes" />\');
                            form.append(\'<input type="hidden" name="SOURCE" value="Smart Slider 3" />\');

                            var key = $(\'#EMAIL\').val(\'' . N2Platform::getUserEmail() . '\').focus(),
                                button = form.find(\'.n2-button-green\');
                            form.on(\'submit\', function (e) {
                                e.preventDefault();
                                button.trigger(\'click\');
                            });

                            button.on(\'click\', $.proxy(function (e) {
                                NextendAjaxHelper.ajax({
                                    type: "POST",
                                    url: "//secure.nextendweb.com/mailchimp/subscribe.php",
                                    data: form.serialize(),
                                    dataType: \'json\'
                                }).done($.proxy(function (response) {
                                    cb();
                                }, this));
                            }, this));
                        },
                        hide: function () {
                            cb();
                        }
                    }
                }
            });
            modal.setCustomClass(\'n2-ss-modal-subscribe\');
            modal.show();
            };
        ', true);
        }
    
    }

    public function assetsFrontend() {
        if (N2Platform::$isAdmin) {
            N2JS::addInline('window.N2SSPRO=' . N2SSPRO . ';', true);
            N2JS::addInline('window.N2SS3C="' . N2SS3::$campaign . '";', true);
        }
    


        $path = $this->getAssetsPath();
        N2JS::addStaticGroup($path . '/dist/smartslider-frontend.min.js', 'smartslider-frontend');
    
    }
}

return new N2SmartsliderApplicationInfo();