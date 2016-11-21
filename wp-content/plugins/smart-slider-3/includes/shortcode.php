<?php

class N2SS3Shortcode
{

    public static function doShortcode($parameters) {
        return self::render($parameters);
    }

    public static function render($parameters, $usage = 'WordPress Shortcode') {
        $parameters = shortcode_atts(array(
            'id'     => md5(time()),
            'slider' => 0
        ), $parameters);

        if (intval($parameters['slider']) > 0) {
            ob_start();
            N2Base::getApplication("smartslider")->getApplicationType('widget')->render(array(
                "controller" => 'home',
                "action"     => 'wordpress',
                "useRequest" => false
            ), array(
                intval($parameters['slider']),
                $usage
            ));
            return ob_get_clean();
        }

        return '';
    }
}

add_shortcode('smartslider3', 'N2SS3Shortcode::doShortcode');

if (defined('DOING_AJAX') && DOING_AJAX) {
    if (isset($_POST['action']) && ($_POST['action'] == 'stpb_preview_builder_item' || $_POST['action'] == 'stpb_load_builder_templates' || $_POST['action'] == 'stpb_load_template')) {
        remove_shortcode('smartslider3');
    }
}