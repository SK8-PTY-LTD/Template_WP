<?php

/*-----------------------------------------------------------------------------------

	Plugin Name: BYT Social Address

-----------------------------------------------------------------------------------*/


// Add function to widgets_init that'll load our widget.
add_action( 'widgets_init', 'bookyourtravel_social_widgets' );

// Register widget.
function bookyourtravel_social_widgets() {
	register_widget( 'bookyourtravel_social_widget' );
}

// Widget class.
class bookyourtravel_social_widget extends WP_Widget {


/*-----------------------------------------------------------------------------------*/
/*	Widget Setup
/*-----------------------------------------------------------------------------------*/
	
	function __construct() {
	
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'bookyourtravel_social_widget', 'description' => esc_html__('BookYourTravel: Social Widget', 'bookyourtravel') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 550, 'id_base' => 'bookyourtravel_social_widget' );

		/* Create the widget. */
		parent::__construct( 'bookyourtravel_social_widget', esc_html__('BookYourTravel: Social Widget', 'bookyourtravel'), $widget_ops, $control_ops );
	}


/*-----------------------------------------------------------------------------------*/
/*	Display Widget
/*-----------------------------------------------------------------------------------*/
	
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );

		$facebook_id = $instance['facebook_id'];
		$facebook_id = str_replace('http://www.facebook.com/', '', $facebook_id);
		
		$twitter_id = $instance['twitter_id'];
		$twitter_id = str_replace('http://twitter.com/', '', $twitter_id);
		
		$youtube_profile = $instance['youtube_profile'];
		$rss_feed = $instance['rss_feed'];	
		$linked_in_profile = $instance['linked_in_profile'];	
		$gplus_profile = $instance['gplus_profile'];	
		$vimeo_profile = $instance['vimeo_profile'];
		$pinterest_profile = $instance['pinterest_profile'];
		$whatsapp_profile = isset($instance['whatsapp_profile']) ? $instance['whatsapp_profile'] : '';
		$instagram_profile = isset($instance['instagram_profile']) ? $instance['instagram_profile'] : '';
		$skype_profile = isset($instance['skype_profile']) ? $instance['skype_profile'] : '';

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display Widget */
		/* Display the widget title if one was input (before and after defined by themes). */
		?>
			<article class="byt_social_widget BookYourTravel_Social_Widget">
		<?php
			if ( $title )
				echo $before_title . $title . $after_title;
		?>
				<ul class="social"> <?php
					if (!empty($facebook_id))
						echo '<li><a href="//www.facebook.com/' . $facebook_id . '" title="facebook"><i class="fa fa-facebook fa-fw"></i></a></li>';
					if (!empty($twitter_id))
						echo '<li><a href="//twitter.com/' . $twitter_id . '" title="twitter"><i class="fa fa-twitter fa-fw"></i></a></li>';
					if (!empty($rss_feed))
						echo '<li><a href="' . $rss_feed . '" title="rss"><i class="fa fa-rss fa-fw"></i></a></li>';
					if (!empty($linked_in_profile))
						echo '<li><a href="' . $linked_in_profile . '" title="linkedin"><i class="fa fa-linkedin fa-fw"></i></a></li>';
					if (!empty($gplus_profile))
						echo '<li><a href="' . $gplus_profile . '" title="googleplus"><i class="fa fa-google-plus fa-fw"></i></a></li>';
					if (!empty($youtube_profile))
						echo '<li><a href="' . $youtube_profile . '" title="youtube"><i class="fa fa-youtube fa-fw"></i></a></li>';
					if (!empty($vimeo_profile))
						echo '<li><a href="' . $vimeo_profile . '" title="vimeo"><i class="fa fa-vimeo fa-fw"></i></a></li>';
					if (!empty($pinterest_profile))
						echo '<li><a href="' . $pinterest_profile . '" title="pinterest"><i class="fa fa-pinterest-p fa-fw"></i></a></li>';
					if (!empty($whatsapp_profile))
						echo '<li><a href="' . $whatsapp_profile . '" title="whatsapp"><i class="fa fa-whatsapp fa-fw"></i></a></li>';
					if (!empty($instagram_profile))
						echo '<li><a href="' . $instagram_profile . '" title="instagram"><i class="fa fa-instagram fa-fw"></i></a></li>';
					if (!empty($skype_profile))
						echo '<li><a href="' . $skype_profile . '" title="skype"><i class="fa fa-skype fa-fw"></i></a></li>';

					?>
				</ul>
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
		$instance['facebook_id'] = strip_tags( $new_instance['facebook_id'] );
		$instance['twitter_id'] = strip_tags( $new_instance['twitter_id'] );
		$instance['youtube_profile'] = strip_tags( $new_instance['youtube_profile'] );
		$instance['rss_feed'] = strip_tags( $new_instance['rss_feed'] );
		$instance['linked_in_profile'] = strip_tags( $new_instance['linked_in_profile'] );
		$instance['gplus_profile'] = strip_tags( $new_instance['gplus_profile'] );
		$instance['vimeo_profile'] = strip_tags( $new_instance['vimeo_profile'] );
		$instance['pinterest_profile'] = strip_tags( $new_instance['pinterest_profile'] );
		$instance['whatsapp_profile'] = strip_tags( $new_instance['whatsapp_profile'] );
		$instance['instagram_profile'] = strip_tags( $new_instance['instagram_profile'] );
		$instance['skype_profile'] = strip_tags( $new_instance['skype_profile'] );
		
		return $instance;
	}
	

/*-----------------------------------------------------------------------------------*/
/*	Widget Settings
/*-----------------------------------------------------------------------------------*/
	 
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array(
		'title' => esc_html__('Follow us', 'bookyourtravel'),
		'facebook_id' => '',
		'twitter_id' => '',
		'youtube_profile' => '',
		'rss_feed' => '',
		'linked_in_profile' => '',
		'gplus_profile' => '',
		'vimeo_profile' => '',
		'pinterest_profile' => '',
		'whatsapp_profile' => '',
		'instagram_profile' => '',
		'skype_profile' => ''
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		
		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e('Title:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'facebook_id' ) ); ?>"><?php esc_html_e('Facebook ID:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'facebook_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'facebook_id' ) ); ?>" value="<?php echo esc_attr( $instance['facebook_id'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'twitter_id' ) ); ?>"><?php esc_html_e('Twitter ID:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'twitter_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'twitter_id' ) ); ?>" value="<?php echo esc_attr( $instance['twitter_id'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'youtube_profile' ) ); ?>"><?php esc_html_e('Youtube url:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'youtube_profile' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'youtube_profile' ) ); ?>" value="<?php echo esc_attr( $instance['youtube_profile'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'rss_feed' ) ); ?>"><?php esc_html_e('Rss feed:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'rss_feed' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'rss_feed' ) ); ?>" value="<?php echo esc_attr( $instance['rss_feed'] ); ?>" />
		</p>		

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'linked_in_profile' ) ); ?>"><?php esc_html_e('LinkedIn url:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'linked_in_profile' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'linked_in_profile' ) ); ?>" value="<?php echo esc_attr( $instance['linked_in_profile'] ); ?>" />
		</p>		

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'gplus_profile' )); ?>"><?php esc_html_e('GPlus url:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'gplus_profile' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'gplus_profile' ) ); ?>" value="<?php echo esc_attr( $instance['gplus_profile'] ); ?>" />
		</p>		

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'vimeo_profile' )); ?>"><?php esc_html_e('Vimeo profile:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'vimeo_profile' ) ); ?>" name="<?php echo esc_attr(  $this->get_field_name( 'vimeo_profile' ) ); ?>" value="<?php echo esc_attr( $instance['vimeo_profile'] ); ?>" />
		</p>		

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'pinterest_profile' )); ?>"><?php esc_html_e('Pinterest url:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'pinterest_profile' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'pinterest_profile' ) ); ?>" value="<?php echo esc_attr( $instance['pinterest_profile'] ); ?>" />
		</p>		
		
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'whatsapp_profile' )); ?>"><?php esc_html_e('Whatsapp url:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'whatsapp_profile' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'whatsapp_profile' ) ); ?>" value="<?php echo esc_attr( $instance['whatsapp_profile'] ); ?>" />
		</p>		
		
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'instagram_profile' )); ?>"><?php esc_html_e('Instagram url:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'instagram_profile' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'instagram_profile' ) ); ?>" value="<?php echo esc_attr( $instance['instagram_profile'] ); ?>" />
		</p>		
		
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'skype_profile' )); ?>"><?php esc_html_e('Skype url:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'skype_profile' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'skype_profile' ) ); ?>" value="<?php echo esc_attr( $instance['skype_profile'] ); ?>" />
		</p>		
		
	<?php
	}
}