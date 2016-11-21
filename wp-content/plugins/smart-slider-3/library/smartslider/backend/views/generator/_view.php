<?php

class N2SmartsliderBackendGeneratorView extends N2ViewBase
{

    public static $sources;

    public static function loadSources() {
        if (!self::$sources) {

            list($groups, $list) = N2SmartsliderGeneratorModel::getGenerators();


            self::$sources = array(
                'available'    => array(),
                'notavailable' => array()
            );
            foreach ($list AS $group => $sources) {
                foreach ($sources AS $type => $info) {
                    /**
                     * @var $info N2GeneratorInfo
                     */
                    if (is_object($info)) {
                        if (!$info->installed) {
                            if (!isset(self::$sources['notavailable'][$group])) {
                                self::$sources['notavailable'][$group] = array();
                            }
                            self::$sources['notavailable'][$group][$type] = $info;
                        } else {
                            if (!isset(self::$sources['available'][$group])) {
                                self::$sources['available'][$group] = array();
                            }
                            self::$sources['available'][$group][$type] = $info;
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $list
     */
    function _renderSourceList($list) {
        foreach ($list AS $group => $sources) {
            $this->renderGroupOption($group, $sources);
        }
    }

    public function renderGroupOption($group, $sources) {

        $button  = false;
        $buttons = array();


        foreach ($sources AS $type => $info) {
            /**
             * @var $info N2GeneratorInfo
             */

            if ($info->hasConfiguration) {
                $buttons[$this->appType->router->createUrl(array(
                    "generator/checkConfiguration",
                    array(
                        "sliderid" => N2Request::getInt('sliderid'),
                        "group"    => $group,
                        "type"     => $type
                    )
                ))] = $info->title;
            } elseif (!$info->installed) {
                $button = N2Html::link(n2_("Visit site"), $info->readMore, array(
                    "target" => "_blank",
                    "class"  => "n2-button n2-button-big n2-button-grey"
                ));
                break;
            } else {
                $buttons[$this->appType->router->createUrl(array(
                    "generator/createSettings",
                    array(
                        "sliderid" => N2Request::getInt('sliderid'),
                        "group"    => $group,
                        "type"     => $type
                    )
                ))] = $info->title;
            }
        }
        if (!$button && ($count = count($buttons))) {
            if ($count == 1) {
                reset($buttons);
                $key    = key($buttons);
                $button = N2Html::link($buttons[$key], $key, array(
                    "class" => "n2-button n2-button-small n2-button-blue n2-h5"
                ));
            } else {
                $keys    = array_keys($buttons);
                $actions = array();
                for ($i = 0; $i < count($keys); $i++) {
                    $actions[] = N2Html::link($buttons[$keys[$i]], $keys[$i], array(
                        'class' => 'n2-h4'
                    ));
                }
                ob_start();
                $this->widget->init("buttonmenu", array(
                    "content" => N2Html::tag('div', array(
                        'class' => 'n2-button-menu'
                    ), N2Html::tag('div', array(
                        'class' => 'n2-button-menu-inner n2-border-radius'
                    ), implode('', $actions)))
                ));
                $buttonMenu = ob_get_clean();
                $button     = N2Html::tag('div', array('class' => 'n2-button n2-button-with-menu n2-button-small n2-h5 n2-button-blue'), N2Html::link($buttons[$keys[0]], $keys[0], array(
                        'class' => 'n2-button-inner'
                    )) . $buttonMenu);
            }
        }


        $this->widget->init("box", array(
            'attributes' => array(
                'class' => 'n2-box-generator'
            ),
            'image'      => N2ImageHelper::fixed(N2Uri::pathToUri(N2Filesystem::translate($info->path . '/../dynamic.png'))),
            'firstCol'   => $button
        ));
    }

    public function _renderGroupOption($group, $sources) {
        $options = array();
        foreach ($sources AS $type => $info) {
            /**
             * @var $info N2GeneratorInfo
             */
            $options[$type] = $info->title;

            if ($info->hasConfiguration) {
                $button = N2Html::link("Next", $this->appType->router->createUrl(array(
                    "generator/checkConfiguration",
                    array(
                        "sliderid" => N2Request::getInt('sliderid'),
                        "group"    => $group
                    )
                )), array(
                    "onclick" => "var el = n2(this); el.attr('href', el.attr('href') + '&type='+el.parents('.n2-box-placeholder').find('select').val());",
                    "class"   => "n2-button n2-button-small n2-button-blue"
                ));

            } elseif (!$info->installed) {
                $button = N2Html::link("Check extension", $info->readMore, array(
                    "target" => "_blank",
                    "class"  => "n2-button n2-button-small n2-button-grey"
                ));
            } else {
                $button = N2Html::link("Next", $this->appType->router->createUrl(array(
                    "generator/createSettings",
                    array(
                        "sliderid" => N2Request::getInt('sliderid'),
                        "group"    => $group
                    )
                )), array(
                    "onclick" => "var el = n2(this); el.attr('href', el.attr('href') + '&type='+el.parents('.n2-box-placeholder').find('select').val());",
                    "class"   => "n2-button n2-button-small n2-button-blue"
                ));
            }
        }
        $optionsHTML = '';
        foreach ($options AS $k => $v) {
            $optionsHTML .= N2Html::tag('option', array('value' => $k), $v);
        }

        echo N2Html::tag('div', array('class' => 'n2-box'), N2Html::image(N2Uri::pathToUri(N2Filesystem::translate($info->path . '/../dynamic.png'))) . N2Html::tag("div", array(
                'class' => 'n2-box-placeholder'
            ), N2Html::tag("table", array(), N2Html::tag("tr", array(), N2Html::tag("td", array(
                    'class' => 'n2-box-label'
                ), N2Html::tag('select', array(
                    'name' => 'generator-type'
                ), $optionsHTML)) . N2Html::tag("td", array(
                    'class' => 'n2-box-button'
                ), $button)))));
    }
} 