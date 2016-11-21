<?php

/*-----------------------------------------------------------------------------------

	Plugin Name: BYT Home Feature

-----------------------------------------------------------------------------------*/


// Add function to widgets_init that'll load our widget.
add_action( 'widgets_init', 'bookyourtravel_home_feature_widgets' );

// Register widget.
function bookyourtravel_home_feature_widgets() {
	register_widget( 'bookyourtravel_home_feature_widget' );
}

// Widget class.
class bookyourtravel_home_feature_widget extends WP_Widget {


/*-----------------------------------------------------------------------------------*/
/*	Widget Setup
/*-----------------------------------------------------------------------------------*/
	
	function __construct() {
	
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'bookyourtravel_home_feature_widget', 'description' => esc_html__('BookYourTravel: Home Feature Widget', 'bookyourtravel') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 550, 'id_base' => 'bookyourtravel_home_feature_widget' );

		/* Create the widget. */
		parent::__construct( 'bookyourtravel_home_feature_widget', esc_html__('BookYourTravel: Home Feature Widget', 'bookyourtravel'), $widget_ops, $control_ops );
	}


/*-----------------------------------------------------------------------------------*/
/*	Display Widget
/*-----------------------------------------------------------------------------------*/
	
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$home_feature_text = $instance['home_feature_text'];

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display Widget */
		?>
			<article class="home_features">
				<div class="hfc">
					<h4><?php echo $title; ?></h4>
					<p><?php echo $home_feature_text; ?></p>
				</div>
			</article>
		<?php

		/* After widget (defined by themes). */
		echo $after_widget;
	}


/*-----------------------------------------------------------------------------------*/
/*	Update Widget
/*-----------------------------------------------------------------------------------*/
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['home_feature_text'] = strip_tags( $new_instance['home_feature_text']);

		return $instance;
	}
	

/*-----------------------------------------------------------------------------------*/
/*	Widget Settings
/*-----------------------------------------------------------------------------------*/
	 
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array(
		'title' => esc_html__('Handpicked Accommodations', 'bookyourtravel'),
		'home_feature_text' => esc_html__('All Book Your Travel Accommodations fulfil strict selection criteria. Each accommodation is chosen individually and inclusion cannot be bought.', 'bookyourtravel'),
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' )); ?>"><?php esc_html_e('Title:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'home_feature_text' ) ); ?>"><?php esc_html_e('Feature text:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'home_feature_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'home_feature_text' ) ); ?>" value="<?php echo esc_attr( $instance['home_feature_text'] ); ?>" />
		</p>
		
	<?php
	}
}