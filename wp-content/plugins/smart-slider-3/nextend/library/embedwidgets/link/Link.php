<?php

/**
 * User: David
 * Date: 2014.05.21.
 * Time: 11:45
 */
class N2Link extends N2EmbedWidget implements N2EmbedWidgetInterface
{

    /**
     * @var array
     */
    public static $params = array(
        'class'     => false,
        'iconClass' => false,
        'title'     => '',
        'link'      => '#'
    );

    public function run($params) {
        $params = array_merge(self::$params, $params);

        $this->render($params);
    }

} 