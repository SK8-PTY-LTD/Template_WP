<?php

class N2SSShortcodeInsert {

    public static function init() {
        add_action('admin_init', array(
            'N2SSShortcodeInsert',
            'addButton'
        ));
    }

    public static function addButton() {
        N2Loader::import('libraries.settings.settings', 'smartslider');
        if ((!current_user_can('edit_posts') && !current_user_can('edit_pages')) || !intval(N2SmartSliderSettings::get('editor-icon', 1))) {
            return;
        }
        if (in_array(basename($_SERVER['PHP_SELF']), array(
            'post-new.php',
            'page-new.php',
            'post.php',
            'page.php'
        ))) {
            wp_register_style('smart-slider-editor', plugin_dir_url(__FILE__) . 'editor.min.css', array(), '3.22', 'screen');
        
            wp_enqueue_style('smart-slider-editor');

            add_action('admin_print_footer_scripts', array(
                'N2SSShortcodeInsert',
                'addButtonDialog'
            ));

            wp_enqueue_script('jquery-ui-dialog');
            wp_enqueue_style("wp-jquery-ui-dialog");

            if (get_user_option('rich_editing') == 'true') {
                add_filter('mce_external_plugins', array(
                    'N2SSShortcodeInsert',
                    'mceAddPlugin'
                ));
                add_filter('mce_buttons', array(
                    'N2SSShortcodeInsert',
                    'mceRegisterButton'
                ));
            }
        }
    }

    public static function mceAddPlugin($plugin_array) {
        $plugin_array['nextend2smartslider3'] = plugin_dir_url(__FILE__) . 'shortcode.js';
        return $plugin_array;
    }

    public static function mceRegisterButton($buttons) {
        array_push($buttons, "|", "nextend2smartslider3");
        return $buttons;
    }

    public static function addButtonDialog() {

        global $wpdb;
        $query   = 'SELECT sliders.title, sliders.id, slides.thumbnail
            FROM ' . $wpdb->prefix . 'nextend2_smartslider3_sliders AS sliders
            LEFT JOIN ' . $wpdb->prefix . 'nextend2_smartslider3_slides AS slides ON slides.id = (SELECT id FROM ' . $wpdb->prefix . 'nextend2_smartslider3_slides WHERE slider = sliders.id AND published = 1 AND generator_id = 0 AND thumbnail NOT LIKE \'\' ORDER BY ordering DESC LIMIT 1)
            ORDER BY time DESC';
        $sliders = $wpdb->get_results($query, ARRAY_A);
        ?>
        <div id='n2-ss-editor-modal' title='Select a Slider'>
            <div class="n2-ss-editor-inner">
                <div class="n2-ss-editor-header">Select a Slider<div class="n2-ss-editor-header-close"></div></div>
                <div class="n2-ss-editor-boxes">
                <?php
                $router = N2Base::getApplication('smartslider')->router;
                $token  = N2Form::tokenizeUrl();
                foreach ($sliders AS $slider) :
                    if (empty($slider['thumbnail'])) {
                        $slider['thumbnail'] = '$system$/images/placeholder/image.png';
                    }
                    ?>
                    <div class="n2-ss-editor-box" data-sliderid="<?php echo $slider['id']; ?>" style="background-image: url(<?php echo N2ImageHelper::fixed($slider['thumbnail']); ?>); ">
                        <div class="n2-ss-editor-box-actions">
                            <a target="_blank" href="<?php echo $router->createUrl(array(
                                'slider/edit',
                                array(
                                    'sliderid' => $slider["id"]
                                )
                            )); ?>">Edit</a>
                            <a target="_blank" href="<?php echo $router->createUrl(array(
                                'preview/index',
                                array(
                                    'sliderid' => $slider["id"]
                                ) + $token
                            )); ?>">Preview</a>
                        </div>
                        <div class="n2-ss-editor-box-title"><?php echo $slider['title']; ?></div>
                    </div>
                    <?php
                endforeach;
                ?>
                </div>
                <div class="n2-ss-editor-buttons">
                    <a href="#" class="n2-ss-editor-insert">Insert slider</a>
                    <a target="_blank" href="<?php echo $router->createUrl(array('sliders/index')); ?>" class="n2-ss-editor-create-slider">Create slider</a>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                var modal = $('#n2-ss-editor-modal'),
                    inner = $('.n2-ss-editor-inner'),
                    boxes = inner.find('.n2-ss-editor-boxes'),
                    $window = $(window),
                    active = null,
                    callback = function () {
                    },
                    watchResize = function () {
                        boxes.height(inner.height() - 116);
                        $window.on('resize.ss', function () {
                            boxes.height(inner.height() - 116);
                        });
                    },
                    unWatchResize = function () {
                        $window.off('resize.ss');
                    },
                    show = function () {
                        modal.addClass('n2-active');
                        watchResize();
                    },
                    hide = function () {
                        unWatchResize();
                        modal.removeClass('n2-active');
                    };

                boxes.find('.n2-ss-editor-box').on('click', function () {
                    if (active !== null) {
                        active.removeClass('n2-active');
                    }
                    active = $(this).addClass('n2-active');
                });

                modal.on('click', function (e) {
                    if (e.target == modal.get(0)) {
                        hide();
                    }
                });
                $('.n2-ss-editor-header-close').on('click', function (e) {
                    e.preventDefault();
                    hide();
                });

                $('.n2-ss-editor-insert').on('click', function (e) {
                    e.preventDefault();
                    if (active !== null) {
                        callback(active.data('sliderid'));
                        hide();
                    } else {
                        alert('Please select a slider!');
                    }
                });

                window.NextendSmartSliderWPTinyMCEModal = function (ed) {
                    callback = function (id) {
                        ed.execCommand('mceInsertContent', false, '<div>[smartslider3 slider=' + id + ']</div>');
                    };
                    show();
                };

                if (typeof QTags !== 'undefined') {
                    QTags.addButton('smart-slider-3', 'Smart Slider', function () {
                        callback = function (id) {
                            QTags.insertContent('<div>[smartslider3 slider=' + id + ']</div>');
                        };
                        show();
                    });
                }

                window.NextendSmartSliderDiviModal = function (button) {
                    var $input = $(button).siblings('.regular-text');
                    callback = function (id) {
                        $input.val(id);
                    };
                    show();
                    return false;
                };
            });
        </script>
        <?php
    }
}

N2SSShortcodeInsert::init();