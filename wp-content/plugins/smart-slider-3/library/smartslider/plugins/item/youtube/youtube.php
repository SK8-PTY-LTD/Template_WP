<?php

N2Loader::import('libraries.plugins.N2SliderItemAbstract', 'smartslider');

class N2SSPluginItemYouTube extends N2SSPluginItemAbstract {

    var $_identifier = 'youtube';

    protected $priority = 20;

    protected $layerProperties = array(
        "width"  => 300,
        "height" => 180
    );

    public function __construct() {
        $this->_title = n2_x('YouTube', 'Slide item');
    }

    function getTemplate($slider) {
        return N2Html::tag('div', array(
            "style" => 'width: 100%; height: 100%; min-height: 50px; background: url({image}) no-repeat 50% 50%; background-size: cover;'
        ), '<img class="n2-video-play n2-ow" src="' . N2ImageHelperAbstract::SVGToBase64('$ss$/images/play.svg') . '"/>');
    }

    function _render($data, $itemId, $slider, $slide) {
        /**
         * @var $data N2Data
         */
        $data->fillDefault(array(
            'image'    => '',
            'start'    => 0,
            'volume'   => -1,
            'autoplay' => 0,
            'controls' => 1,
            'center'   => 0,
            'loop'     => 0,
            'reset'    => 0,
            'theme'    => 'dark',
            'related'  => 0,
            'vq'       => 'default'
        ));

        $rawYTUrl = $slide->fill($data->get('youtubeurl', ''));

        $url_parts = parse_url($rawYTUrl);
        if (!empty($url_parts['query'])) {
            parse_str($url_parts['query'], $query);
            if (isset($query['v'])) {
                unset($query['v']);
            }
            $data->set("query", $query);
        }

        $youTubeUrl = $this->parseYoutubeUrl($rawYTUrl);

        $start = $slide->fill($data->get('start', ''));
        $data->set("youtubecode", $youTubeUrl);
        $data->set("start", $start);

        $style = '';

        $hasImage = 0;
        $image    = $data->get('image');

        $playImage = '';
        if (!empty($image)) {
            $style    = 'cursor:pointer; background: url(' . N2ImageHelper::fixed($data->get('image')) . ') no-repeat 50% 50%; background-size: cover';
            $hasImage = 1;
            if ($data->get('playbutton', 1) != 0) {
                $playImage = '<img class="n2-video-play n2-ow" src="' . N2ImageHelperAbstract::SVGToBase64('$ss$/images/play.svg') . '"/>';
            }
        }

        N2JS::addInline('window["' . $slider->elementId . '"].ready(function(){
            new NextendSmartSliderYouTubeItem(this, "' . $itemId . '", ' . $data->toJSON() . ', ' . $hasImage . ');
        });');

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

    function parseYoutubeUrl($youTubeUrl) {
        preg_match('/^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/', $youTubeUrl, $matches);

        if ($matches && isset($matches[7]) && strlen($matches[7]) == 11) {
            return $matches[7];
        }

        return $youTubeUrl;
    }

    function getValues() {
        return array(
            'code'         => 'qesNtYIBDfs',
            'youtubeurl'   => 'https://www.youtube.com/watch?v=MKmIwHAFjSU',
            'image'        => '$system$/images/placeholder/video.png',
            'autoplay'     => 0,
            'controls'     => 1,
            'defaultimage' => 'maxresdefault',
            'related'      => '0',
            'vq'           => 'default',
            'center'       => 0,
            'loop'         => 0,
            'reset'        => 0
        );
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->_identifier . DIRECTORY_SEPARATOR;
    }

    public function getFilled($slide, $data) {
        $data->set('youtubeurl', $slide->fill($data->get('youtubeurl', '')));
        return $data;
    }
}

N2Plugin::addPlugin('ssitem', 'N2SSPluginItemYouTube');