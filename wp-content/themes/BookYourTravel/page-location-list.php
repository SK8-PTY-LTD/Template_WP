<?php
/*	Template Name: Location list
 * The template for displaying all locations in a list.
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since Book Your Travel 1.0
 */

get_header();  
BookYourTravel_Theme_Utils::breadcrumbs();
get_sidebar('under-header');

global $post, $bookyourtravel_theme_globals, $item_class;

$page_id = $post->ID;
$page_custom_fields = get_post_custom( $page_id);

$parent_location_id = null;
$parent_location_title = '';
if (isset($page_custom_fields['location_list_location_post_id'])) {
	$parent_location_id = $page_custom_fields['location_list_location_post_id'][0];
	$parent_location = get_post($parent_location_id);
	if ($parent_location)
		$parent_location_title = $parent_location->post_title;
}

$sort_by = 'title';
if (isset($page_custom_fields['location_list_sort_by'])) {
	$sort_by = $page_custom_fields['location_list_sort_by'][0];
	$sort_by = empty($sort_by) ? 'title' : $sort_by;
}

$sort_descending = false;
if (isset($page_custom_fields['location_list_sort_descending'])) {
	$sort_descending = $page_custom_fields['location_list_sort_descending'][0] == '1' ? true : false;
}

$show_featured_only = false;
if (isset($page_custom_fields['location_list_show_featured_only'])) {
	$show_featured_only = $page_custom_fields['location_list_show_featured_only'][0] == '1' ? true : false;
}

$sort_order = $sort_descending ? 'DESC' : 'ASC';

if ( get_query_var('paged') ) {
    $paged = get_query_var('paged');
} else if ( get_query_var('page') ) {
    $paged = get_query_var('page');
} else {
    $paged = 1;
}

$posts_per_page = $bookyourtravel_theme_globals->get_locations_archive_posts_per_page();

$args = array(
	'posts_per_page'   => $posts_per_page,
	'paged'			   => $paged,
	'category'         => '',
	'orderby'          => $sort_by,
	'order'            => $sort_order,
	'post_type'        => 'location',
	'post_status'      => 'publish',
	'meta_query'        => array('relation' => 'AND'),
	'tax_query'        => array('relation' => 'AND')
); 

if (isset($show_featured_only) && $show_featured_only) {
	$args['meta_query'][] = array(
		'key'       => 'tour_is_featured',
		'value'     => 1,
		'compare'   => '=',
		'type' => 'numeric'
	);
}
	
if ($parent_location_id) {
	$args['post_parent'] = $parent_location_id;
}

$page_sidebar_positioning = null;
if (isset($page_custom_fields['page_sidebar_positioning'])) {
	$page_sidebar_positioning = $page_custom_fields['page_sidebar_positioning'][0];
	$page_sidebar_positioning = empty($page_sidebar_positioning) ? '' : $page_sidebar_positioning;
}

$section_class = 'full-width';
$item_class = 'one-fourth';
if ($page_sidebar_positioning == 'both') {
	$section_class = 'one-half';
	$item_class = 'one-half';
} else if ($page_sidebar_positioning == 'left' || $page_sidebar_positioning == 'right') {
	$section_class = 'three-fourth';
	$item_class = 'one-third';
}

$location_tags = wp_get_post_terms($page_id, 'location_tag', array("fields" => "all"));
$location_tag_ids = array();
if (!is_wp_error($location_tags) && count($location_tags) > 0) {
	foreach ($location_tags as $location_tag) {
		$location_tag_ids[] = $location_tag->term_id;
	}
}

if (!empty($location_tag_ids)) {
	$args['tax_query'][] = 	array(
			'taxonomy' => 'location_tag',
			'field' => 'id',
			'terms' => $location_tag_ids,
			'operator'=> 'IN'
	);
}	
?>
<div class="row">
<?php
	if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'left')
		get_sidebar('left');
	$allowed_tags = array();
	$allowed_tags['span'] = array('class' => array());
	?>
	<section class="<?php echo esc_attr($section_class); ?>">
		<?php  while ( have_posts() ) : the_post(); ?>
		<article id="page-<?php the_ID(); ?>">
			<h1><?php the_title(); ?></h1>
			<?php the_content( wp_kses(__( 'Continue reading <span class="meta-nav">&rarr;</span>', 'bookyourtravel' ), $allowed_tags) ); ?>
			<?php wp_link_pages('before=<div class="pagination">&after=</div>'); ?>
		</article>
		<?php endwhile; ?>
	<?php
		$query = new WP_Query($args); 
	?>	
		<div class="destinations">
			<?php if ( $query->have_posts() ) { ?>
			<div class="row">
			<?php
			while ($query->have_posts()) {
				global $post;
				$query->the_post(); 
				get_template_part('includes/parts/location', 'item');	
			} // end while ($query->have_posts()) ?>
			</div>
			<nav class="page-navigation bottom-nav">
				<!--back up button-->
				<a href="#" class="scroll-to-top" title="<?php esc_attr_e('Back up', 'bookyourtravel'); ?>"><?php esc_html_e('Back up', 'bookyourtravel'); ?></a> 
				<!--//back up button-->
				<!--pager-->
				<div class="pager">
					<?php BookYourTravel_Theme_Utils::display_pager($query->max_num_pages); ?>
				</div>
			</nav>
		<?php } // end if ( $query->have_posts() ) ?>
		</div><!--//destinations -->
	</section>
	<?php
	wp_reset_postdata();
	wp_reset_query();

	if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'right')
		get_sidebar('right');
	?>
</div>
<?php		
get_footer();