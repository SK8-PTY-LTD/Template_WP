<?php

N2Loader::import('libraries.form.element.text');

N2Localization::addJS(array(
    'Link',
    'Lightbox',
    'Create lightbox from image, video or iframe.',
    'Actions',
    'Create action for click or tap.',
    'Content list',
    'One per line',
    'Autoplay duration',
    'Examples',
    'Image',
    'Insert',
    'Keyword',
    'No search term specified. Showing recent items.',
    'Showing items match for "%s"',
    'Select'
));


class N2ElementUrl extends N2ElementText
{

    function fetchElement() {
        $html = parent::fetchElement();

        N2JS::addInline("new NextendElementUrl('" . $this->_id . "', " . self::getNextendElementUrlParameters() . " );");
        return $html;
    }

    public static function getNextendElementUrlParameters() {
        $params             = array(
            'hasPosts' => N2Platform::$hasPosts
        );
        $params['imageUrl'] = N2Uri::pathToUri(N2LIBRARYASSETS . "/images");
        $params['url']      = N2Base::getApplication('system')
                                    ->getApplicationType('backend')->router->createUrl("link/search");

        return json_encode(N2ElementUrlParams::extend($params));
    }

    protected function post() {
        if (!N2Platform::$hasPosts && !N2PRO) {
            return '';
        }
        return N2Html::tag('a', array(
            'href'  => '#',
            'class' => 'n2-form-element-clear'
        ), N2Html::tag('i', array('class' => 'n2-i n2-it n2-i-empty n2-i-grey-opacity'), '')) . '<a id="' . $this->_id . '_button" class="n2-form-element-button n2-h5 n2-uc" href="#">' . n2_('Link') . '</a>';
    }
}

N2Loader::import('libraries.form.element.url', 'platform');