<?php
/**
 * The sidebar containing the left widget area.
 *
 * If no active widgets in sidebar, let's hide it completely.
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since Book Your Travel 1.0
 */
 
global $post;
$template_file = get_post_meta($post->ID,'_wp_page_template',true);
 
if ( is_active_sidebar( 'left' ) ) { ?>
	<aside id="secondary" class="left-sidebar widget-area one-fourth" role="complementary">
		<ul>
		<?php dynamic_sidebar( 'left' ); ?>
		</ul>
	</aside><!-- #secondary -->
<?php 
} else {
	if ($template_file == 'custom-search-results.php') { ?>
	<aside class="left-sidebar" role="complementary">
		<?php 
		$widget_args = array(
			'before_widget' => '',
			'after_widget'  => '',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>',
		);
		the_widget('BookYourTravel_Search_Widget', null, $widget_args); 
		?>
	</aside>	
	<?php }
}