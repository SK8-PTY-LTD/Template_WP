<?php
/*	Template Name: User Submit Content
 * The template for displaying submit forms for front-end content submission
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since Book Your Travel 1.0
 */

global $bookyourtravel_theme_globals, $bookyourtravel_accommodation_helper, $bookyourtravel_review_helper, $current_user, $frontend_submit;

if ( !is_user_logged_in() || !$frontend_submit->user_has_correct_role()) {
	wp_redirect( home_url('/') );
	exit;
}

get_header();  
BookYourTravel_Theme_Utils::breadcrumbs();
get_sidebar('under-header');

$enable_reviews = $bookyourtravel_theme_globals->enable_reviews();
$enable_accommodations = $bookyourtravel_theme_globals->enable_accommodations();
$enable_tours = $bookyourtravel_theme_globals->enable_tours();
$enable_cruises = $bookyourtravel_theme_globals->enable_cruises();
$enable_car_rentals = $bookyourtravel_theme_globals->enable_car_rentals();

$current_user = wp_get_current_user();
$user_info = get_userdata($current_user->ID);
$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
$default_currency_symbol = $bookyourtravel_theme_globals->get_default_currency_symbol();
$show_currency_symbol_after = $bookyourtravel_theme_globals->show_currency_symbol_after();

global $post;
$page_id = $post->ID;
$page_custom_fields = get_post_custom( $page_id );
$current_url = get_permalink( $page_id );

$content_type = 'accommodation';
if (isset($page_custom_fields['frontend_submit_content_type'])) {
	$content_type = $page_custom_fields['frontend_submit_content_type'][0];
	$frontend_submit->prepare_form($content_type);
}

$page_sidebar_positioning = null;
if (isset($page_custom_fields['page_sidebar_positioning'])) {
	$page_sidebar_positioning = $page_custom_fields['page_sidebar_positioning'][0];
	$page_sidebar_positioning = empty($page_sidebar_positioning) ? '' : $page_sidebar_positioning;
}

$section_class = 'full-width';
if ($page_sidebar_positioning == 'both')
	$section_class = 'one-half';
else if ($page_sidebar_positioning == 'left' || $page_sidebar_positioning == 'right') 
	$section_class = 'three-fourth';
?>
<div class="row">
	<?php	
	if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'left')
		get_sidebar('left');
		
	$allowed_tags = array();
	$allowed_tags['span'] = array('class' => array());
	?>
	<section class="<?php echo esc_attr($section_class); ?>">
		<?php get_template_part('includes/parts/user-account', 'menu'); ?>				
		<section id="Submit" class="tab-content initial">
			<?php  while ( have_posts() ) : the_post(); ?>
			<article id="page-<?php the_ID(); ?>">
				<h2><?php the_title(); ?></h2>
				<?php the_content( wp_kses(__( 'Continue reading <span class="meta-nav">&rarr;</span>', 'bookyourtravel' ), $allowed_tags) ); ?>
				<?php wp_link_pages('before=<div class="pagination">&after=</div>'); ?>
				<?php echo $frontend_submit->render_upload_form(); ?>
			</article>		
			<?php endwhile; ?>
		</section>
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