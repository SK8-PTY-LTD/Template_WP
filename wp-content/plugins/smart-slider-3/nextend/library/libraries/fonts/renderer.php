<?php

N2Loader::import(array(
    'libraries.fonts.storage'
));

class N2FontRenderer
{

    public static $defaultFont = 'Montserrat';

    public static $pre = '';
    public static $sets = array();
    public static $fonts = array();

    /**
     * @var N2FontStyle
     */
    public static $style;

    public static $mode;

    public static function preLoad($fontId) {
        if (intval($fontId) > 0) {
            $font = N2StorageSectionAdmin::getById($fontId, 'font');
            if ($font) {
                self::$sets[] = $font['referencekey'];
            }
        }
    }

    public static function render($font, $mode, $group, $pre = '', $fontSize = false) {
        self::$pre = $pre;
        if (intval($font) > 0) {
            // Linked
            $font = N2StorageSectionAdmin::getById($font, 'font');
            if ($font) {
                if (is_string($font['value'])) {
                    $value = json_decode(base64_decode($font['value']), true);
                } else {
                    $value = $font['value'];
                }
                $selector = 'n2-font-' . $font['id'] . '-' . $mode;

                self::$sets[] = $font['referencekey'];

                if (!isset(self::$fonts[$font['id']])) {
                    self::$fonts[$font['id']] = array(
                        $mode
                    );
                } else if (!in_array($mode, self::$fonts[$font['id']])) {
                    self::$fonts[$font['id']][] = $mode;
                }

                N2CSS::addCode(self::renderFont($mode, $pre, $selector, $value['data'], $fontSize), $group);

                return $selector . ' ';
            }
        } else if ($font != '') {
            $value = json_decode(base64_decode($font), true);
            if ($value) {
                $selector = 'n2-font-' . md5($font) . '-' . $mode;
                N2CSS::addCode(self::renderFont($mode, $pre, $selector, $value['data'], $fontSize), $group);
                return $selector . ' ';
            }
        }
        return '';
    }

    private static function renderFont($mode, $pre, $selector, $tabs, $fontSize) {
        $template = '';
        foreach (self::$mode[$mode]['selectors'] AS $s => $style) {
            $template .= $s . "{" . $style . "}\n";
        }
        $search  = array(
            '@pre',
            '@selector'
        );
        $replace = array(
            $pre,
            '.' . $selector
        );
        $tabs[0] = array_merge(array(
            'afont'         => self::$defaultFont,
            'color'         => '000000ff',
            'size'          => '14||px',
            'tshadow'       => '0|*|0|*|0|*|000000ff',
            'lineheight'    => '1.5',
            'bold'          => 0,
            'italic'        => 0,
            'underline'     => 0,
            'align'         => 'left',
            'letterspacing' => "normal",
            'wordspacing'   => "normal",
            'texttransform' => "none",
            'extra'         => ''
        ), $tabs[0]);

        if (self::$mode[$mode]['renderOptions']['combined']) {
            for ($i = 1; $i < count($tabs); $i++) {
                $tabs[$i] = array_merge($tabs[$i - 1], $tabs[$i]);
                if ($tabs[$i]['size'] == $tabs[0]['size']) {
                    $tabs[$i]['size'] = '100||%';
                }
            }
        }
        foreach ($tabs AS $k => $tab) {
            $search[]              = '@tab' . $k;
            N2FontStyle::$fontSize = $fontSize;
            $replace[]             = self::$style->style($tab);
        }
        return str_replace($search, $replace, $template);
    }
}


N2FontRenderer::$mode = array(
    '0'                   => array(
        'id'            => '0',
        'label'         => n2_('Text'),
        'tabs'          => array(
            n2_('Text')
        ),
        'renderOptions' => array(
            'combined' => false
        ),
        'preview'       => '<div class="{fontClassName}">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>',
        'selectors'     => array(
            '@pre@selector' => '@tab0'
        )
    ),
    'simple'              => array(
        'id'            => 'simple',
        'label'         => n2_('Text'),
        'tabs'          => array(
            n2_('Text')
        ),
        'renderOptions' => array(
            'combined' => false
        ),
        'preview'       => '<div class="{fontClassName}">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>',
        'selectors'     => array(
            '@pre@selector' => '@tab0'
        )
    ),
    'hover'               => array(
        'id'            => 'hover',
        'label'         => n2_('Hover'),
        'tabs'          => array(
            n2_('Text'),
            n2_('Hover')
        ),
        'renderOptions' => array(
            'combined' => false
        ),
        'preview'       => '<div class="{fontClassName}">' . n2_('Button') . '</div>',
        'selectors'     => array(
            '@pre@selector'       => '@tab0',
            '@pre@selector:HOVER, @pre@selector:ACTIVE, @pre@selector:FOCUS' => '@tab1'
        )
    ),
    'link'                => array(
        'id'            => 'link',
        'label'         => n2_('Link'),
        'tabs'          => array(
            n2_('Text'),
            n2_('Hover')
        ),
        'renderOptions' => array(
            'combined' => false
        ),
        'preview'       => '<div class="{fontClassName}">' . n2_('Button') . '</div>',
        'selectors'     => array(
            '@pre@selector a'       => '@tab0',
            '@pre@selector a:HOVER, @pre@selector a:ACTIVE, @pre@selector a:FOCUS' => '@tab1'
        )
    ),
    'accordionslidetitle' => array(
        'id'            => 'accordionslidetitle',
        'label'         => n2_('Accordion Slide Title'),
        'tabs'          => array(
            n2_('Normal'),
            n2_('Active')
        ),
        'renderOptions' => array(
            'combined' => false
        ),
        'preview'       => '<div class="{fontClassName}">' . n2_('Slide title') . '</div>',
        'selectors'     => array(
            '@pre@selector'                                          => '@tab0',
            '@pre.n2-ss-slide-active @selector, @pre@selector:HOVER' => '@tab1'
        )
    ),
    'paragraph'           => array(
        'id'            => 'paragraph',
        'label'         => n2_('Paragraph'),
        'tabs'          => array(
            n2_('Text'),
            n2_('Link'),
            n2_('Hover')
        ),
        'renderOptions' => array(
            'combined' => true
        ),
        'preview'       => '<div class="{fontClassName}">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do <a href="#">test link</a> incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in <a href="#">test link</a> velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat <a href="#">test link</a>, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>',
        'selectors'     => array(
            '@pre@selector'         => '@tab0',
            '@pre@selector a'       => '@tab1',
            '@pre@selector a:HOVER, @pre@selector a:ACTIVE, @pre@selector a:FOCUS' => '@tab2'
        )
    ),
    'dot'                 => array(
        'id'            => 'dot',
        'label'         => n2_('Dot'),
        'tabs'          => array(
            n2_('Text'),
            n2_('Active')
        ),
        'renderOptions' => array(
            'combined' => false
        ),
        'preview'       => '',
        'selectors'     => array(
            '@pre@selector'                                => '@tab0',
            '@pre@selector.n2-active, @pre@selector:HOVER, @pre@selector:ACTIVE, @pre@selector:FOCUS' => '@tab1'
        )
    )
);

N2Loader::import('libraries.image.color');

class N2FontStyle
{

    public static $fontSize = false;

    /**
     * @param string $tab
     *
     * @return string
     */
    public function style($tab) {
        $style = '';
        $extra = '';
        if (isset($tab['extra'])) {
            $extra = $tab['extra'];
            unset($tab['extra']);
        }
        foreach ($tab AS $k => $v) {
            $style .= $this->parse($k, $v);
        }
        $style .= $this->parse('extra', $extra);
        return $style;
    }

    /**
     * @param $property
     * @param $value
     *
     * @return mixed
     */
    public function parse($property, $value) {
        $fn = 'parse' . $property;
        return $this->$fn($value);
    }

    /**
     * @param $v
     *
     * @return string
     */
    public function parseColor($v) {
        $hex   = N2Color::hex82hex($v);
        $style = 'color: #' . $hex[0] . ';';
        if ($hex[1] != 'ff') {
            $rgba = N2Color::hex2rgba($v);
            $style .= 'color: RGBA(' . $rgba[0] . ',' . $rgba[1] . ',' . $rgba[2] . ',' . round($rgba[3] / 127, 2) . ');';
        }
        return $style;
    }

    /**
     * @param $v
     *
     * @return string
     */
    public function parseSize($v) {
        if (self::$fontSize) {
            $fontSize = N2Parse::parse($v);
            if ($fontSize[1] == 'px') {
                return 'font-size:' . ($fontSize[0] / self::$fontSize * 100) . '%;';
            }
        }
        return 'font-size:' . N2Parse::parse($v, '') . ';';
    }

    /**
     * @param $v
     *
     * @return string
     */
    public function parseTshadow($v) {
        $v    = N2Parse::parse($v);
        $rgba = N2Color::hex2rgba($v[3]);
        if ($v[0] == 0 && $v[1] == 0 && $v[2] == 0) return 'text-shadow: none;';
        return 'text-shadow: ' . $v[0] . 'px ' . $v[1] . 'px ' . $v[2] . 'px RGBA(' . $rgba[0] . ',' . $rgba[1] . ',' . $rgba[2] . ',' . round($rgba[3] / 127, 2) . ');';
    }

    /**
     * @param $v
     *
     * @return string
     */
    public function parseAfont($v) {
        return 'font-family: ' . $this->loadFont($v) . ';';
    }

    /**
     * @param $v
     *
     * @return string
     */
    public function parseLineheight($v) {
        if ($v == '') return '';
        return 'line-height: ' . $v . ';';
    }

    /**
     * @param $v
     *
     * @return string
     */
    public function parseBold($v) {
        if ($v == '1') return 'font-weight: bold;';
        return 'font-weight: normal;';
    }

    /**
     * @param $v
     *
     * @return string
     */
    public function parseItalic($v) {
        if ($v == '1') return 'font-style: italic;';
        return 'font-style: normal;';
    }

    /**
     * @param $v
     *
     * @return string
     */
    public function parseUnderline($v) {
        if ($v == '1') return 'text-decoration: underline;';
        return 'text-decoration: none;';
    }

    /**
     * @param $v
     *
     * @return string
     */
    public function parseAlign($v) {
        return 'text-align: ' . $v . ';';
    }

    public function parseLetterSpacing($v) {
        return 'letter-spacing: ' . $v . ';';
    }

    public function parseWordSpacing($v) {
        return 'word-spacing: ' . $v . ';';
    }

    public function parseTextTransform($v) {
        return 'text-transform: ' . $v . ';';
    }

    public function parseExtra($v) {
        return $v;
    }

    /**
     * @param $families
     *
     * @return mixed
     */
    public function loadFont($families) {
        $families = explode(',', $families);
        for ($i = 0; $i < count($families); $i++) {
            $families[$i] = $this->getFamily(trim(trim($families[$i]), '\'"'));
        }
        return implode(',', $families);
    }

    private function getFamily($family) {
        static $cache = array();
        if (!isset($cache[$family])) {
            N2Pluggable::doAction('fontFamily', array($family));
            $cache[$family] = '';
        }
        return "'" . $family . "'";
    }
}

N2FontRenderer::$style = new N2FontStyle();