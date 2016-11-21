<?php
/*	Template Name: Car rental list
 * The template for displaying the car rental list.
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since Book Your Travel 1.0
 */

get_header();  
BookYourTravel_Theme_Utils::breadcrumbs();
get_sidebar('under-header');

global $bookyourtravel_car_rental_helper, $bookyourtravel_theme_globals, $item_class;

if ( get_query_var('paged') ) {
    $paged = get_query_var('paged');
} else if ( get_query_var('page') ) {
    $paged = get_query_var('page');
} else {
    $paged = 1;
}

$posts_per_page = $bookyourtravel_theme_globals->get_car_rentals_archive_posts_per_page();

global $post;
$page_id = $post->ID;
$page_custom_fields = get_post_custom( $page_id);

$car_types = wp_get_post_terms($page_id, 'car_type', array("fields" => "all"));

$car_type_ids = array();
if (!is_wp_error($car_types) && count($car_types) > 0) {
	$car_type_ids[] = $car_types[0]->term_id;
}

$sort_by = 'title';
if (isset($page_custom_fields['car_rental_list_sort_by'])) {
	$sort_by = $page_custom_fields['car_rental_list_sort_by'][0];
	$sort_by = empty($sort_by) ? 'title' : $sort_by;
}

$sort_descending = false;
if (isset($page_custom_fields['car_rental_list_sort_descending'])) {
	$sort_descending = $page_custom_fields['car_rental_list_sort_descending'][0] == '1' ? true : false;
}

$show_featured_only = false;
if (isset($page_custom_fields['car_rental_list_show_featured_only'])) {
	$show_featured_only = $page_custom_fields['car_rental_list_show_featured_only'][0] == '1' ? true : false;
}

$sort_order = $sort_descending ? 'DESC' : 'ASC';

$car_rental_tags = wp_get_post_terms($page_id, 'car_rental_tag', array("fields" => "all"));
$car_rental_tag_ids = array();
if (!is_wp_error($car_rental_tags) && count($car_rental_tags) > 0) {
	foreach ($car_rental_tags as $car_rental_tag) {
		$car_rental_tag_ids[] = $car_rental_tag->term_id;
	}
}

$parent_location = null;
$parent_location_id = 0;
if (isset($page_custom_fields['car_rental_list_location_post_id'])) {
	$parent_location_id = $page_custom_fields['car_rental_list_location_post_id'][0];
	$parent_location_id = empty($parent_location_id) ? 0 : (int)$parent_location_id;
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
			$car_rental_results = $bookyourtravel_car_rental_helper->list_car_rentals($paged, $posts_per_page, 'post_title', 'ASC', 0, $car_type_ids, $car_rental_tag_ids, array(), $show_featured_only);
		?>	
		<div class="deals">
			<script>
				window.formMultipleError = <?php echo json_encode(esc_html__('You failed to provide {0} fields. They have been highlighted below.', 'bookyourtravel'));  ?>;
			</script>
			<?php if ( count($car_rental_results) > 0 && $car_rental_results['total'] > 0 ) { ?>
			<div class="row">
			<?php
				foreach ($car_rental_results['results'] as $car_rental_result) { 
					global $post;
					$post = $car_rental_result;
					setup_postdata( $post );
					get_template_part('includes/parts/car_rental', 'item');
				}
			?>
			</div>
			<nav class="page-navigation bottom-nav">
				<!--back up button-->
				<a href="#" class="scroll-to-top" title="<?php esc_attr_e('Back up', 'bookyourtravel'); ?>"><?php esc_html_e('Back up', 'bookyourtravel'); ?></a> 
				<!--//back up button-->
				<!--pager-->
				<div class="pager">
					<?php 
					$total_results = $car_rental_results['total'];
					BookYourTravel_Theme_Utils::display_pager( ceil($total_results/$posts_per_page) );
					?>
				</div>
			</nav>
		<?php } // end if ( $query->have_posts() ) ?>
		</div><!--//deals-->
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