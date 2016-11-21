<?php

class NextendSmartSlider3VisualComposer
{

    public static function init()
    {
        self::vc_add_element();

        add_action('vc_frontend_editor_render', 'NextendSmartSlider3VisualComposer::removeShortcode');
        add_action('vc_front_load_page_', 'NextendSmartSlider3VisualComposer::removeShortcode');
        add_action('vc_load_shortcode', 'NextendSmartSlider3VisualComposer::removeShortcode');
    }

    public static function vc_add_element()
    {

        global $wpdb;

        $res = $wpdb->get_results('SELECT id, title FROM ' . $wpdb->prefix . 'nextend2_smartslider3_sliders');
        $options = array();
        foreach ($res AS $r) {
            $options[$r->title] = $r->id;
        }

        vc_map(array(
            "name" => "Smart Slider 3",
            "base" => "smartslider3",
            "category" => __('Content'),
            "params" => array(
                array(
                    'type' => 'dropdown',
                    'heading' => 'Slider',
                    'param_name' => 'slider',
                    'value' => $options,
                    'save_always' => true,
                    'description' => 'Select a slider to add it to your post or page.',
                    'admin_label' => true,
                ),
            )
        ));

        add_action('admin_footer', 'NextendSmartSlider3VisualComposer::add_admin_icon');
    }

    public static function add_admin_icon()
    {
        ?>
        <style type="text/css">
            .wpb_smartslider3 .vc_element-icon {
                background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAMAAABEpIrGAAAAYFBMVEUTr+8YnOQPxPkYnOQUsO8PxPkYnOQTr+5hp8Uoncg1kryMwtqh0ePT6PFvude83ers9vlFqc4Vi8EQo9kSls4Xo+j///8XoOcYm+QVp+oQwPcStPARuvQYneUUrewPxPkMc7TJAAAACHRSTlNt9G1uG8vNAnToTbkAAAFrSURBVHgBfZPr0qowDEXrBVB6b0KgUMv7v+UJ2MFy5tOVdIbuvXT0B6Lrumszr38wN1cuRXdv1q80dxa4/2F04srftc7zdtZqSrZeRTOfWc7XRqS5BtAj1E4SdZ3ROHLq5Ig5zem9Gbymjd1JJRXvBz7gLXdaKWXJWb+UQqTC4h3XVjurjEfIqXAICczTP7SUVlsDR8rCkpZ9wQD2ypG1RE++lxULkxYGDBoi+cTnLpR+Ewqoe0cSsnek4EhrwT6IQs7emhBrIZeB4IkMZED+fD5G5A9BE6kA+UQtwJMN5zF+E0YIiohkOAkx5n0jBO8BvSMyWLLtiFhAr0mHiD2RC/HgEMbebT8wxqD/E4a4D0rOETEYIhs4KcPCG9wKaeT2P/pp+CCGcdh2CJLe2B45OVaMhQnDQypp+jCNNeI1HoMYELl+VXMR7esnrbj9Fm6ia6fyPB3zod1e3nb6Sntnoetu7eWv9tLeuPwHrqBewxDhYIoAAAAASUVORK5CYII=);
            }
        </style>
        <?php
    }

    public static function removeShortcode(){
        add_shortcode('smartslider3', 'NextendSmartSlider3VisualComposer::_removeShortcode');
    }
    
    public static function _removeShortcode($atts) {
        return '<h3>Smart Slider 3 - Slider ID: #' . $atts['slider'] . '</h3><img src="' . NEXTEND_SMARTSLIDER_3_URL . '/images/ss3.jpg" />';
    }
}

NextendSmartSlider3VisualComposer::init();