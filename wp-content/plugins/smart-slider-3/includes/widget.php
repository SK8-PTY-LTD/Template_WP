<?php

class N2SS3Widget extends WP_Widget {

    private $preventRender = false;

    function __construct() {

        parent::__construct('smartslider3', // Base ID
            'Smart Slider', // Name
            array('description' => 'Displays a Smart Slider') // Args
        );

        // YOAST SEO fix
        add_action('wpseo_head', array(
            $this,
            'preventRender'
        ), 0);
        add_action('wpseo_head', array(
            $this,
            'notPreventRender'
        ), 10000000000);
    }

    public static function register_widget() {
        register_widget('N2SS3Widget');
    }

    public function preventRender() {
        $this->preventRender = true;
    }

    public function notPreventRender() {
        $this->preventRender = false;
    }

    function widget($args, $instance) {
        if ($this->preventRender) {
            return;
        }
        $instance = array_merge(array(
            'id'     => md5(time()),
            'slider' => 0
        ), $instance);

        $slider = do_shortcode('[smartslider3 slider=' . $instance['slider'] . ']');

        if ($slider != '') {

            $title = apply_filters('widget_title', $instance['title']);

            echo $args['before_widget'];
            if (!empty($title)) echo $args['before_title'] . $title . $args['after_title'];

            echo $slider;

            echo $args['after_widget'];
        }
    }

    function form($instance) {
        global $wpdb;
        $instance = wp_parse_args((array)$instance, array(
            'title'  => '',
            'slider' => -1
        ));
        $title    = $instance['title'];
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">
                Title:
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                       name="<?php echo $this->get_field_name('title'); ?>" type="text"
                       value="<?php echo esc_attr($title); ?>"/>
            </label>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('smartslider2'); ?>">
                Smart Slider:
                <select class="widefat" id="<?php echo $this->get_field_id('slider'); ?>"
                        name="<?php echo $this->get_field_name('slider'); ?>">
                    <?php
                    $slider = $instance['slider'];

                    $res = $wpdb->get_results('SELECT id, title FROM ' . $wpdb->prefix . 'nextend2_smartslider3_sliders');
                    foreach ($res AS $r) {
                        ?>
                        <option <?php if ($r->id == $slider) { ?>selected="selected"
                                <?php } ?>value="<?php echo $r->id; ?>"><?php echo $r->title; ?></option>
                        <?php
                    }
                    ?>
                </select>
            </label>
        </p>
        <p>You can create Sliders in the left sidebar.</p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance           = $old_instance;
        $instance['title']  = $new_instance['title'];
        $instance['slider'] = $new_instance['slider'];
        return $instance;
    }
}

add_action('widgets_init', 'N2SS3Widget::register_widget');