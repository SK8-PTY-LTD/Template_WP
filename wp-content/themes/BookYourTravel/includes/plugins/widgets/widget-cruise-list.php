<?php

/*-----------------------------------------------------------------------------------

	Plugin Name: BookYourTravel Cruise List Widget

-----------------------------------------------------------------------------------*/

// Add function to widgets_init that'll load our widget.
add_action( 'widgets_init', 'bookyourtravel_cruise_lists_widgets' );

// Register widget.
function bookyourtravel_cruise_lists_widgets() {
	global $bookyourtravel_theme_globals;
	if ($bookyourtravel_theme_globals->enable_cruises()) {
		register_widget( 'bookyourtravel_cruise_list_widget' );
	}
}

// Widget class.
class bookyourtravel_cruise_list_widget extends WP_Widget {

	/*-----------------------------------------------------------------------------------*/
	/*	Widget Setup
	/*-----------------------------------------------------------------------------------*/
	
	function __construct() {
	
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'bookyourtravel_cruise_list_widget', 'description' => esc_html__('BookYourTravel: Cruise List', 'bookyourtravel') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 260, 'height' => 400, 'id_base' => 'bookyourtravel_cruise_list_widget' );

		/* Create the widget. */
		parent::__construct( 'bookyourtravel_cruise_list_widget', esc_html__('BookYourTravel: Cruise List', 'bookyourtravel'), $widget_ops, $control_ops );
	}


/*-----------------------------------------------------------------------------------*/
/*	Display Widget
/*-----------------------------------------------------------------------------------*/
	
	function widget( $args, $instance ) {
		
		global $sc_theme_globals, $bookyourtravel_cruise_helper;
				
		extract( $args );
		
		$card_layout_classes = array(
			'full-width',
			'one-half',
			'one-third',
			'one-fourth',
			'one-fifth'
		);
		
		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : esc_html__('Explore our latest cruises', 'bookyourtravel') );
		
		$number_of_posts = isset($instance['number_of_posts']) ? (int)$instance['number_of_posts'] : 4;
		$sort_by = isset($instance['sort_by']) ? $instance['sort_by'] : 'title';
		
		$sort_descending = isset($instance['sort_by']) && $instance['sort_descending'] == '1';
		$order = $sort_descending ? 'DESC' : 'ASC';
		$posts_per_row = isset($instance['posts_per_row']) ? (int)$instance['posts_per_row'] : 4;
		$show_featured_only = isset($instance['show_featured_only']) && $instance['show_featured_only'] == '1';
		$cruise_type_ids = isset($instance['cruise_type_ids']) ? (array)$instance['cruise_type_ids'] : array();
		$cruise_tag_ids = isset($instance['cruise_tag_ids']) ? (array)$instance['cruise_tag_ids'] : array();
		global $display_mode;
		$display_mode = isset($instance['display_mode']) ? $instance['display_mode'] : 'card';

		echo $before_widget;
		
		if ($display_mode == 'card') {
		?><!--deals--><div class="deals"><?php
		} else {
		?><!--deals--><ul class="small-list"><?php
		}
		
		/* Display Widget */
		
		$cruise_results = $bookyourtravel_cruise_helper->list_cruises(1, $number_of_posts, $sort_by, $order, 0, $cruise_type_ids, $cruise_tag_ids, array(), $show_featured_only);
		
		if ($display_mode == 'card') { ?>
			<header class="s-title">
			<?php echo $before_title . $title . $after_title; ?>
			</header> <?php			
		} else {
			echo $before_title . $title . $after_title; 		
		}
		
		if ( count($cruise_results) > 0 && $cruise_results['total'] > 0 ) {
			if ($display_mode == 'card') {
			?><div class="row"><?php
			}
				foreach ($cruise_results['results'] as $cruise_result) {
					global $post;				
					$post = $cruise_result;
					setup_postdata( $post ); 
					global $item_class;
					if (isset($card_layout_classes[$posts_per_row - 1]))
						$item_class = $card_layout_classes[$posts_per_row - 1];
					else
						$item_class = 'one-fourth';	
					get_template_part('includes/parts/cruise', 'item');
				}			
			if ($display_mode == 'card') {				
			?></div><?php
			}
		}
		if ($display_mode == 'card') {
		?></div><!--//deals--><?php
		} else {
		?></ul><?php
		}
		/* After widget (defined by themes). */
		echo $after_widget;
		
		// set back to default since this is a global variable
		$display_mode = 'card';
	}
	

/*-----------------------------------------------------------------------------------*/
/*	Update Widget
/*-----------------------------------------------------------------------------------*/
	
	function update( $new_instance, $old_instance ) {
	
		$instance = $old_instance;

		/* Strip tags to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );		
		$instance['number_of_posts'] = strip_tags( $new_instance['number_of_posts']);
		$instance['sort_by'] = strip_tags( $new_instance['sort_by']);
		$instance['sort_descending'] = strip_tags( $new_instance['sort_descending']);
		$instance['display_mode'] = strip_tags( $new_instance['display_mode']);
		$instance['posts_per_row'] = strip_tags( $new_instance['posts_per_row']);
		$instance['show_featured_only'] = strip_tags( $new_instance['show_featured_only']);
		$instance['cruise_type_ids'] = $new_instance['cruise_type_ids'];
		$instance['cruise_tag_ids'] = $new_instance['cruise_tag_ids'];
		
		return $instance;
	}
	

/*-----------------------------------------------------------------------------------*/
/*	Widget Settings
/*-----------------------------------------------------------------------------------*/
	 
	function form( $instance ) {
			
		$cat_args = array( 
			'taxonomy'=>'cruise_type', 
			'hide_empty'=>'0'
		);
		$cruise_types = get_categories($cat_args);
		
		$cat_args = array( 
			'taxonomy'=>'cruise_tag', 
			'hide_empty'=>'0'
		);
		$cruise_tags = get_categories($cat_args);
			
		/* Set up some default widget settings. */
		$defaults = array(
			'title' => esc_html__('Explore our latest cruises', 'bookyourtravel'),
			'number_of_posts' => '4',
			'sort_by' => 'title',
			'sort_descending' => '1',
			'display_mode' => 'card',
			'posts_per_row' => 4,
			'show_featured_only' => '0',
			'cruise_type_ids' => array(),
			'cruise_tag_ids' => array()
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e('Title:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr ($instance['title']); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number_of_posts' ) ); ?>"><?php esc_html_e('How many cruises do you want to display?', 'bookyourtravel') ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'number_of_posts' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number_of_posts' ) ); ?>">
				<?php for ($i=1;$i<13;$i++) { ?>
				<option <?php echo ($i == $instance['number_of_posts'] ? 'selected="selected"' : ''); ?> value="<?php echo esc_attr ( $i ); ?>"><?php echo $i; ?></option>
				<?php } ?>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'sort_by' ) ); ?>"><?php esc_html_e('What do you want to sort the cruises by?', 'bookyourtravel') ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'sort_by' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'sort_by') ); ?>">
				<option <?php echo 'title' == $instance['sort_by'] ? 'selected="selected"' : ''; ?> value="title"><?php esc_html_e('Post Title', 'bookyourtravel') ?></option>
				<option <?php echo 'ID' == $instance['sort_by'] ? 'selected="selected"' : ''; ?> value="ID"><?php esc_html_e('Post ID', 'bookyourtravel') ?></option>
				<option <?php echo 'rand' == $instance['sort_by'] ? 'selected="selected"' : ''; ?> value="rand"><?php esc_html_e('Random', 'bookyourtravel') ?></option>
				<option <?php echo 'date' == $instance['sort_by'] ? 'selected="selected"' : ''; ?> value="date"><?php esc_html_e('Publish Date', 'bookyourtravel') ?></option>
				<option <?php echo 'comment_count' == $instance['sort_by'] ? 'selected="selected"' : ''; ?> value="comment_count"><?php esc_html_e('Comment Count', 'bookyourtravel') ?></option>
			</select>
		</p>		

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'sort_descending' ) ); ?>"><?php esc_html_e('Sort cruises in descending order?', 'bookyourtravel') ?></label>
			<input type="checkbox"  <?php echo ($instance['sort_descending'] == '1' ? 'checked="checked"' : ''); ?> class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'sort_descending' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'sort_descending') ); ?>" value="1" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'display_mode' ) ); ?>"><?php esc_html_e('Display mode?', 'bookyourtravel') ?></label>
			<select class="posts_widget_display_mode" id="<?php echo esc_attr( $this->get_field_id( 'display_mode' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'display_mode') ); ?>">
				<option <?php echo 'small' == $instance['display_mode'] ? 'selected="selected"' : ''; ?> value="small"><?php esc_html_e('Small (usually sidebar)', 'bookyourtravel') ?></option>
				<option <?php echo 'card' == $instance['display_mode'] ? 'selected="selected"' : ''; ?> value="card"><?php esc_html_e('Card (usually in grid view)', 'bookyourtravel') ?></option>
			</select>
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_featured_only') ); ?>"><?php esc_html_e('Show only featured cruises?', 'bookyourtravel') ?></label>
			<input type="checkbox"  <?php echo ( $instance['show_featured_only'] == '1' ? 'checked="checked"' : '' ); ?> class="checkbox" id="<?php echo esc_attr ( $this->get_field_id( 'show_featured_only' ) ); ?>" name="<?php echo esc_attr ( $this->get_field_name( 'show_featured_only' ) ); ?>" value="1" />
		</p>
		
		<p>
			<label><?php esc_html_e('Cruise type (leave blank to ignore)', 'bookyourtravel') ?></label>
			<div>
				<?php for ($j=0;$j<count($cruise_types);$j++) { 
					$type = $cruise_types[$j];
					$checked = false;
					if (isset($instance['cruise_type_ids'])) {
						if (in_array($type->term_id, $instance['cruise_type_ids']))
							$checked = true;
					}
				?>
				<input <?php echo ($checked ? 'checked="checked"' : ''); ?> type="checkbox" id="<?php echo esc_attr ( $this->get_field_name( 'cruise_type_ids' ) ); ?>_<?php echo esc_attr ($type->term_id); ?>" name="<?php echo esc_attr ( $this->get_field_name( 'cruise_type_ids' ) ); ?>[]" value="<?php echo esc_attr ($type->term_id); ?>">
				<label for="<?php echo esc_attr ( $this->get_field_name( 'cruise_type_ids' ) ); ?>_<?php echo esc_attr ($type->term_id); ?>"><?php echo $type->name; ?></label>
				<br />
				<?php } ?>
			</div>
		</p>
		
		<p>
			<label><?php esc_html_e('Cruise tag (leave blank to ignore)', 'bookyourtravel') ?></label>
			<div>
				<?php for ($j=0;$j<count($cruise_tags);$j++) { 
					$tag = $cruise_tags[$j];
					$checked = false;
					if (isset($instance['cruise_tag_ids'])) {
						if (in_array($tag->term_id, $instance['cruise_tag_ids']))
							$checked = true;
					}
				?>
				<input <?php echo ($checked ? 'checked="checked"' : ''); ?> type="checkbox" id="<?php echo esc_attr ( $this->get_field_name( 'cruise_tag_ids' ) ); ?>_<?php echo esc_attr ($tag->term_id); ?>" name="<?php echo esc_attr ( $this->get_field_name( 'cruise_tag_ids' ) ); ?>[]" value="<?php echo esc_attr ($tag->term_id); ?>">
				<label for="<?php echo esc_attr ( $this->get_field_name( 'cruise_tag_ids' ) ); ?>_<?php echo esc_attr ($tag->term_id); ?>"><?php echo $tag->name; ?></label>
				<br />
				<?php } ?>
			</div>
		</p>
		
		<p class="cards" <?php echo ( $instance['display_mode'] != 'card' ? 'style="display:none"' : '' ); ?>>
			<label for="<?php echo esc_attr ( $this->get_field_id( 'posts_per_row' ) ); ?>"><?php esc_html_e('How many cruises do you want to display per row?', 'bookyourtravel') ?></label>
			<select id="<?php echo esc_attr ( $this->get_field_id( 'posts_per_row' ) ); ?>" name="<?php echo esc_attr ( $this->get_field_name( 'posts_per_row' ) ); ?>">
				<?php for ($i=1;$i<6;$i++) { ?>
				<option <?php echo ($i == $instance['posts_per_row'] ? 'selected="selected"' : ''); ?> value="<?php echo esc_attr ( $i ); ?>"><?php echo $i; ?></option>
				<?php } ?>
			</select>
		</p>		
	<?php
	}
}