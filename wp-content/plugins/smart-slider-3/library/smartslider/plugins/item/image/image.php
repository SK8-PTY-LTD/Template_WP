<?php

N2Loader::import('libraries.plugins.N2SliderItemAbstract', 'smartslider');

class N2SSPluginItemImage extends N2SSPluginItemAbstract
{

    var $_identifier = 'image';

    protected $priority = 1;

    protected $layerProperties = array("width" => 200);

    private static $style = '';

    public function __construct() {
        $this->_title = n2_x('Image', 'Slide item');
    }

    private static function initDefaultStyle() {
        static $inited = false;
        if (!$inited) {
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-image-style');
            if (is_array($res)) {
                self::$style = $res['value'];
            }
            if (is_numeric(self::$style)) {
                N2StyleRenderer::preLoad(self::$style);
            }
            $inited = true;
        }
    }

    public static function onSmartsliderDefaultSettings(&$settings) {
        self::initDefaultStyle();
        $settings['style'][] = '<param name="item-image-style" type="style" previewmode="box" label="Item - Image" default="' . self::$style . '" />';
    }

    function getTemplate($slider) {
        $html = N2Html::openTag("div", array(
            'class' => '{styleclass}',
            'style' => 'overflow:hidden;'
        ));
        $html .= N2Html::openTag("a", array(
            "href"    => "{url}",
            "onclick" => 'return false;',
            "class"   => "n2-ow",
            "style"   => "display: block;background: none !important;"
        ));

        $html .= '<img class="n2-ow" src="{image}" style="display: inline-block; max-width: 100%;width:{width};height:{height};" class="{cssclass}">';

        $html .= N2Html::closeTag("a");
        $html .= N2Html::closeTag("div");

        return $html;
    }

    function _render($data, $itemId, $slider, $slide) {
        return $this->getHtml($data, $itemId, $slider, $slide);
    }

    function _renderAdmin($data, $itemId, $slider, $slide) {
        return $this->getHtml($data, $itemId, $slider, $slide);
    }

    private function getHtml($data, $id, $slider, $slide) {

        $size = (array)N2Parse::parse($data->get('size', ''));
        if (empty($size[0])) $size[0] = 'auto';
        if (empty($size[1])) $size[1] = 'auto';


        $html = N2Html::tag('img', self::optimizeImage($slide->fill($data->get('image', '')), $data, $slider) + array(
                "id"    => $id,
                "alt"   => htmlspecialchars($slide->fill($data->get('alt', ''))),
                "style" => "display: inline-block; max-width: 100%; width: {$size[0]};height: {$size[1]};",
                "class" => $data->get('cssclass', '') . ' n2-ow',
                "title" => htmlspecialchars($slide->fill($data->get('title', '')))
            ), false);

        $style = N2StyleRenderer::render($data->get('style'), 'heading', $slider->elementId, 'div#' . $slider->elementId . ' ');
        return N2Html::tag("div", array(
            "class" => $style,
            'style' => 'overflow:hidden;'
        ), $this->getLink($slide, $data, $html, array('class' => 'n2-ow')));
    }

    function getValues() {
        self::initDefaultStyle();
        return array(
            'image'          => '$system$/images/placeholder/image.png',
            'alt'            => n2_('Image is not available'),
            'title'          => '',
            'link'           => '#|*|_self',
            'size'           => '100%|*|auto',
            'style'          => self::$style,
            'cssclass'       => '',
            'image-optimize' => 1
        );
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->_identifier . DIRECTORY_SEPARATOR;
    }

    public function getFilled($slide, $data) {
        $data->set('image', $slide->fill($data->get('image', '')));
        $data->set('alt', $slide->fill($data->get('alt', '')));
        $data->set('title', $slide->fill($data->get('title', '')));
        $data->set('link', $slide->fill($data->get('link', '#|*|')));
        return $data;
    }

    public function prepareExport($export, $data) {
        $export->addImage($data->get('image'));
        $export->addVisual($data->get('style'));
        $export->addLightbox($data->get('link'));
    }

    public function prepareImport($import, $data) {
        $data->set('image', $import->fixImage($data->get('image')));
        $data->set('style', $import->fixSection($data->get('style')));
        $data->set('link', $import->fixLightbox($data->get('link')));
        return $data;
    }
}

N2Plugin::addPlugin('ssitem', 'N2SSPluginItemImage');

N2Pluggable::addAction('smartsliderDefault', 'N2SSPluginItemImage::onSmartsliderDefaultSettings');