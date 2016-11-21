<?php
/*
 * Initialized plugins widget
 */

add_action( 'widgets_init', create_function( '', 'return register_widget("AWS_Widget");' ) );


class AWS_Widget extends WP_Widget {

    /*
     * Constructor
     */
    function __construct() {
        $widget_ops = array( 'description' => __('Advanced WooCommerce search widget', 'aws' ) );
        $control_ops = array( 'width' => 400 );
        parent::__construct( false, __( '&raquo; AWS Widget', 'aws' ), $widget_ops, $control_ops );
    }

    /*
     * Display widget
     */
    function widget( $args, $instance ) {
        extract( $args );
        $title = apply_filters('widget_title', $instance['title'] );

        echo $before_widget;
        echo $before_title;
        echo $title;
        echo $after_title;

        // Generate search form markup
        echo AWS()->markup();

        echo $after_widget;
    }

    /*
     * Update widget settings
     */
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $params = array( 'title' );
        foreach ( $params as $k ) {
            $instance[$k] = strip_tags( $new_instance[$k] );
        }
        return $instance;
    }

    /*
     * Widget settings form
     */
    function form( $instance ) {
        global $shortname;
        $defaults = array(
            'title' => __( 'Search...', 'aws' )
        );
        $instance = wp_parse_args( (array) $instance, $defaults );
        ?>

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id('title') ); ?>"><?php _e( 'Title:', 'aws' ); ?></label>
            <input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>">
        </p>

    <?php
    }
}
?>