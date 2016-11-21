<?php

if (N2Platform::$isWordpress) {
    N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

    class N2SSPluginGeneratorPosts extends N2SliderGeneratorPluginAbstract
    {

        public static $_group = 'posts';
        public static $groupLabel = 'WordPress';

        function onGeneratorList(&$group, &$list) {
            $group[self::$_group] = 'Posts';

            if (!isset($list[self::$_group])) $list[self::$_group] = array();

            $list[self::$_group]['posts'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('Posts by filter'), $this->getPath() . 'posts')
                                                           ->setType('article');

            $list[self::$_group]['postsbyids'] = N2GeneratorInfo::getInstance(self::$groupLabel, n2_('Posts by IDs'), $this->getPath() . 'postsbyids')
                                                                ->setType('article');
        }

        function getPath() {
            return dirname(__FILE__) . DIRECTORY_SEPARATOR;
        }
    }

    N2Plugin::addPlugin('ssgenerator', 'N2SSPluginGeneratorPosts');
}