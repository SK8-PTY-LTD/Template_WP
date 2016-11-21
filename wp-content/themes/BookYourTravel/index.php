<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * For example, it puts together the home page when no home.php file exists.
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

	global $bookyourtravel_theme_globals;
?><!--three-fourth content-->
<div class="row">
	<section class="three-fourth">
		<div class="row">
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>			
			<!--post-->
			<div class="full-width">
				<article class="static-content post">
					<header class="entry-header">
						<h2><a href="<?php echo esc_url(get_the_permalink()) ?>"><?php the_title(); ?></a></h2>
						<p class="entry-meta">
							<span class="date"><?php esc_html_e('Date', 'bookyourtravel');?>: <?php the_time(get_option('date_format')); ?></span> 
							<span class="author"><?php esc_html_e('By ', 'bookyourtravel'); the_author_posts_link(); ?></span> 
							<span class="categories"><?php esc_html_e('Categories', 'bookyourtravel'); ?>: <?php the_category(' ') ?></span>
							<span class="tags"><?php the_tags(); ?></span>
							<span class="comments">
								<a href="<?php esc_url(get_comments_link()); ?>" rel="nofollow">
									<?php comments_number(esc_html__('No comments', 'bookyourtravel'), esc_html__('1 Comment', 'bookyourtravel'), esc_html__('% Comments', 'bookyourtravel')); ?>
								</a>
							</span>
						</p>
					</header>
					<?php if ( has_post_thumbnail() ) { ?>
					<div class="entry-featured">
						<a href="<?php echo esc_url(get_the_permalink()) ?>">
							<figure>
								<?php the_post_thumbnail('featured', array('title' => '')); ?>
							</figure>
						</a>
					</div>
					<?php } ?>
					<div class="entry-content">
						<?php the_excerpt(); ?>
						<a href="<?php echo esc_url(get_the_permalink()) ?>" class="gradient-button" rel="nofollow"><?php esc_html_e('Read More...', 'bookyourtravel'); ?></a>
					</div>
				</article>
			</div>
			<!--//post-->			
			<?php endwhile; else: ?>
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
			<?php endif; ?>
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
	</section>
	<!--//three-fourth content-->
<?php get_sidebar('right'); ?>
</div>
<?php get_footer(); ?>