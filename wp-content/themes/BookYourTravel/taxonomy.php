<?php
/**
 * The taxonomy archive template file.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since Book Your Travel 1.0
 */ 
get_header();  
BookYourTravel_Theme_Utils::breadcrumbs();
get_sidebar('under-header');	

$taxonomy = get_query_var( 'taxonomy' );

$template_part = '';
if ($taxonomy == 'acc_tag' || $taxonomy == 'accommodation_type') {
	$template_part = 'includes/parts/accommodation';
} else if ($taxonomy == 'tour_tag' || $taxonomy == 'tour_type') {
	$template_part = 'includes/parts/tour';
} else if ($taxonomy == 'cruise_tag' || $taxonomy == 'cruise_type') {
	$template_part = 'includes/parts/cruise';
} else if ($taxonomy == 'car_rental_tag' || $taxonomy == 'car_type') {
	$template_part = 'includes/parts/car_rental';
}

if (!empty($template_part) || $taxonomy == 'facility') {
	$item_class = 'one-fourth';	
	global $bookyourtravel_theme_globals;
	?><!--three-fourth content-->
	<div class="row">
		<section class="full-width">
			<header class="page-header">
				<?php
					the_archive_title( '<h1 class="page-title">', '</h1>' );
					the_archive_description( '<div class="taxonomy-description">', '</div>' );
				?>
			</header><!-- .page-header -->
			<?php if (have_posts()) { ?>
			<div class="deals">
				<div class="row">
					<?php 
					while (have_posts()) {
						the_post(); 
						global $post;
						if ($taxonomy == 'facility') {
							if ($post->post_type == 'accommodation') {
								$template_part = 'includes/parts/accommodation';
							} else if ($post->post_type == 'cruise') {
								$template_part = 'includes/parts/cruise';								
							}
						}
						get_template_part($template_part, 'item');
					}
					?>
				</div>
				<!--bottom navigation-->
				<div class="full-width">
					<nav class="page-navigation bottom-nav">
						<a href="#" class="scroll-to-top" title="<?php esc_attr_e('Back up', 'bookyourtravel'); ?>"><?php esc_html_e('Back up', 'bookyourtravel'); ?></a> 
						<div class="pager">
						<?php 	
							global $wp_query;
							BookYourTravel_Theme_Utils::display_pager($wp_query->max_num_pages); 
						?>
						</div>
					</nav>
				</div>
				<!--//bottom navigation-->
			</div>
			<?php } else { ?>
			<div class="row">			
				<div class="full-width">
					<article class="static-content post">
						<header class="entry-header">
							<p><strong><?php esc_html_e('There has been an error.', 'bookyourtravel'); ?></strong></p>
						</header>
						<div class="entry-content">
							<p><?php esc_html_e('We apologize for any inconvenience, please hit back on your browser or if you are an admin, enter some content.', 'bookyourtravel'); ?></p>
						</div>
					</article>
				</div>
			</div>
			<?php } ?>
		</section>
		<!--//three-fourth content-->
	<?php get_sidebar('right'); ?>
	</div>
<?php } else { ?>

<?php } ?>
<?php get_footer(); ?>