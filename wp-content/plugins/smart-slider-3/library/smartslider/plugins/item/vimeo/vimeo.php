<?php

N2Loader::import('libraries.plugins.N2SliderItemAbstract', 'smartslider');

class N2SSPluginItemVimeo extends N2SSPluginItemAbstract
{

    var $_identifier = 'vimeo';

    protected $priority = 20;

    protected $layerProperties = array(
        "width"  => 300,
        "height" => 180
    );

    public function __construct() {
        $this->_title = n2_x('Vimeo', 'Slide item');
    }

    function getTemplate($slider) {
        return N2Html::tag('div', array(
            "style" => 'width: 100%; height: 100%; min-height: 50px; background: url({image}) no-repeat 50% 50%; background-size: cover;'
        ), '<img class="n2-video-play n2-ow" src="' . N2ImageHelperAbstract::SVGToBase64('$ss$/images/play.svg') . '"/>');
    }

    function _render($data, $itemId, $slider, $slide) {

        $data->set("vimeocode", preg_replace('/\D/', '', $slide->fill($data->get("vimeourl"))));

        $style = '';

        $hasImage = 0;
        $playImage = '';
        $image    = $data->get('image');
        if (!empty($image)) {
            $style    = 'cursor:pointer; background: url(' . N2ImageHelper::fixed($data->get('image')) . ') no-repeat 50% 50%; background-size: cover';
            $hasImage = 1;
            $playImage = '<img class="n2-video-play n2-ow" src="' . N2ImageHelperAbstract::SVGToBase64('$ss$/images/play.svg') . '"/>';
        }

        N2JS::addInline('window["' . $slider->elementId . '"].ready(function() {
                var vimeo = new NextendSmartSliderVimeoItem(this, "' . $itemId . '", "' . $slider->elementId . '", ' . $data->toJSON() . ', ' . $hasImage . ');
            });
        ');

        return N2Html::tag('div', array(
            'id'    => $itemId,
            'style' => 'position: absolute; top: 0; left: 0; width: 100%; height: 100%;' . $style
        ), $playImage);
    }

    function _renderAdmin($data, $itemId, $slider, $slide) {
        return N2Html::tag('div', array(
            "style" => 'width: 100%; height: 100%; background: url(' . N2ImageHelper::fixed($data->getIfEmpty('image', '$system$/images/placeholder/video.png')) . ') no-repeat 50% 50%; background-size: cover;'
        ), '<img class="n2-video-play n2-ow" src="' . N2ImageHelperAbstract::SVGToBase64('$ss$/images/play.svg') . '"/>');
    }

    function getValues() {
        return array(
            'vimeourl' => '75251217',
            'image'    => '$system$/images/placeholder/video.png',
            'center'   => 0,
            'autoplay' => 0,
            'title'    => 1,
            'byline'   => 1,
            'portrait' => 0,
            'color'    => '00adef',
            'loop'     => 0
        );
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->_identifier . DIRECTORY_SEPARATOR;
    }

    public function getFilled($slide, $data) {
        $data->set('vimeourl', $slide->fill($data->get('vimeourl', '')));
        return $data;
    }
}

N2Plugin::addPlugin('ssitem', 'N2SSPluginItemVimeo');