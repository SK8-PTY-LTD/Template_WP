<?php

/*-----------------------------------------------------------------------------------

	Plugin Name: BookYourTravel Post List Widget

-----------------------------------------------------------------------------------*/

// Add function to widgets_init that'll load our widget.
add_action( 'widgets_init', 'bookyourtravel_post_lists_widgets' );

// Register widget.
function bookyourtravel_post_lists_widgets() {
	register_widget( 'bookyourtravel_post_list_widget' );
}

// Widget class.
class bookyourtravel_post_list_widget extends WP_Widget {

	/*-----------------------------------------------------------------------------------*/
	/*	Widget Setup
	/*-----------------------------------------------------------------------------------*/
	
	function __construct() {
	
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'bookyourtravel_post_list_widget', 'description' => esc_html__('BookYourTravel: Post List', 'bookyourtravel') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 260, 'height' => 400, 'id_base' => 'bookyourtravel_post_list_widget' );

		/* Create the widget. */
		parent::__construct( 'bookyourtravel_post_list_widget', esc_html__('BookYourTravel: Post List', 'bookyourtravel'), $widget_ops, $control_ops );
	}


/*-----------------------------------------------------------------------------------*/
/*	Display Widget
/*-----------------------------------------------------------------------------------*/
	
	function widget( $args, $instance ) {
		
		global $sc_theme_globals, $bookyourtravel_post_helper;
		
		$card_layout_classes = array(
			'full-width',
			'one-half',
			'one-third',
			'one-fourth',
			'one-fifth'
		);
		
		extract( $args );
		
		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : esc_html__('Explore our latest offers', 'bookyourtravel') );
		
		$number_of_posts = isset($instance['number_of_posts']) ? (int)$instance['number_of_posts'] : 4;
		$sort_by = isset($instance['sort_by']) ? $instance['sort_by'] : 'title';
		$sort_descending = isset($instance['sort_by']) && $instance['sort_descending'] == '1';
		$order = $sort_descending ? 'DESC' : 'ASC';
		$posts_per_row = isset($instance['posts_per_row']) ? (int)$instance['posts_per_row'] : 4;
		global $display_mode;
		$display_mode = isset($instance['display_mode']) ? $instance['display_mode'] : 'card';
		$category_ids = isset($instance['category_ids']) ? (array)$instance['category_ids'] : array();
		
		echo $before_widget;
		
		if ($display_mode == 'card') {
		?><!--deals--><div class="offers"><?php
		} else {
		?><!--deals--><ul class="small-list"><?php
		}
		
		/* Display Widget */
				
		$post_results = $bookyourtravel_post_helper->list_posts(0, $number_of_posts, $sort_by, $order, $category_ids);
		
		if ($display_mode == 'card') { ?>
			<header class="s-title">
			<?php echo $before_title . $title . $after_title; ?>
			</header> <?php			
		} else {
			echo $before_title . $title . $after_title; 		
		}
		
		if ( count($post_results) > 0 && $post_results['total'] > 0 ) {
			if ($display_mode == 'card') {
			?><div class="row"><?php
			}
				foreach ($post_results['results'] as $post_result) {
					global $post;				
					$post = $post_result;
					setup_postdata( $post ); 
					global $post_class;
					if (isset($card_layout_classes[$posts_per_row - 1]))
						$post_class = $card_layout_classes[$posts_per_row - 1];
					else
						$post_class = 'one-fourth';
					get_template_part('includes/parts/post', 'item');
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
		$instance['category_ids'] = $new_instance['category_ids'];
		
		return $instance;
	}
	

/*-----------------------------------------------------------------------------------*/
/*	Widget Settings
/*-----------------------------------------------------------------------------------*/
	 
	function form( $instance ) {
	
		$cat_args = array( 
			'taxonomy'=>'category', 
			'hide_empty'=>'0'
		);
		$categories = get_categories($cat_args);
			
		/* Set up some default widget settings. */
		$defaults = array(
			'title' => esc_html__('Explore our latest offers', 'bookyourtravel'),
			'number_of_posts' => '4',
			'sort_by' => 'title',
			'sort_descending' => '1',
			'display_mode' => 'card',
			'posts_per_row' => 4,
			'category_ids' => array(),
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e('Title:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr ($instance['title']); ?>" />
		</p>

		<p>
			<label><?php esc_html_e('Categories', 'bookyourtravel') ?></label>
			<div>
				<?php for ($j=0;$j<count($categories);$j++) { 
					$type = $categories[$j];
					$checked = false;
					if (isset($instance['category_ids'])) {
						if (in_array($type->term_id, $instance['category_ids']))
							$checked = true;
					}
				?>
				<input <?php echo ($checked ? 'checked="checked"' : ''); ?> type="checkbox" id="<?php echo esc_attr ( $this->get_field_name( 'category_ids' ) ); ?>_<?php echo esc_attr ($type->term_id); ?>" name="<?php echo esc_attr ( $this->get_field_name( 'category_ids' ) ); ?>[]" value="<?php echo esc_attr ($type->term_id); ?>">
				<label for="<?php echo esc_attr ( $this->get_field_name( 'category_ids' ) ); ?>_<?php echo esc_attr ($type->term_id); ?>"><?php echo $type->name; ?></label>
				<br />
				<?php } ?>
			</div>
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number_of_posts' ) ); ?>"><?php esc_html_e('How many posts do you want to display?', 'bookyourtravel') ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'number_of_posts' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number_of_posts' ) ); ?>">
				<?php for ($i=1;$i<13;$i++) { ?>
				<option <?php echo ($i == $instance['number_of_posts'] ? 'selected="selected"' : ''); ?> value="<?php echo esc_attr ( $i ); ?>"><?php echo $i; ?></option>
				<?php } ?>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'sort_by' ) ); ?>"><?php esc_html_e('What do you want to sort the posts by?', 'bookyourtravel') ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'sort_by' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'sort_by') ); ?>">
				<option <?php echo 'title' == $instance['sort_by'] ? 'selected="selected"' : ''; ?> value="title"><?php esc_html_e('Post Title', 'bookyourtravel') ?></option>
				<option <?php echo 'ID' == $instance['sort_by'] ? 'selected="selected"' : ''; ?> value="ID"><?php esc_html_e('Post ID', 'bookyourtravel') ?></option>
				<option <?php echo 'rand' == $instance['sort_by'] ? 'selected="selected"' : ''; ?> value="rand"><?php esc_html_e('Random', 'bookyourtravel') ?></option>
				<option <?php echo 'date' == $instance['sort_by'] ? 'selected="selected"' : ''; ?> value="date"><?php esc_html_e('Publish Date', 'bookyourtravel') ?></option>
				<option <?php echo 'comment_count' == $instance['sort_by'] ? 'selected="selected"' : ''; ?> value="comment_count"><?php esc_html_e('Comment Count', 'bookyourtravel') ?></option>
			</select>
		</p>		

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'sort_descending' ) ); ?>"><?php esc_html_e('Sort posts in descending order?', 'bookyourtravel') ?></label>
			<input type="checkbox"  <?php echo ($instance['sort_descending'] == '1' ? 'checked="checked"' : ''); ?> class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'sort_descending' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'sort_descending') ); ?>" value="1" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'display_mode' ) ); ?>"><?php esc_html_e('Display mode?', 'bookyourtravel') ?></label>
			<select class="posts_widget_display_mode" id="<?php echo esc_attr( $this->get_field_id( 'display_mode' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'display_mode') ); ?>">
				<option <?php echo 'small' == $instance['display_mode'] ? 'selected="selected"' : ''; ?> value="small"><?php esc_html_e('Small (usually sidebar)', 'bookyourtravel') ?></option>
				<option <?php echo 'card' == $instance['display_mode'] ? 'selected="selected"' : ''; ?> value="card"><?php esc_html_e('Card (usually in grid view)', 'bookyourtravel') ?></option>
			</select>
		</p>
		
		<p class="cards" <?php echo ( $instance['display_mode'] != 'card' ? 'style="display:none"' : '' ); ?>>
			<label for="<?php echo esc_attr ( $this->get_field_id( 'posts_per_row' ) ); ?>"><?php esc_html_e('How many posts do you want to display per row?', 'bookyourtravel') ?></label>
			<select id="<?php echo esc_attr ( $this->get_field_id( 'posts_per_row' ) ); ?>" name="<?php echo esc_attr ( $this->get_field_name( 'posts_per_row' ) ); ?>">
				<?php for ($i=1;$i<6;$i++) { ?>
				<option <?php echo ($i == $instance['posts_per_row'] ? 'selected="selected"' : ''); ?> value="<?php echo esc_attr ( $i ); ?>"><?php echo $i; ?></option>
				<?php } ?>
			</select>
		</p>		
	<?php
	}
}