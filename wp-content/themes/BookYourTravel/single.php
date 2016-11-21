<?php 
	get_header();  
	BookYourTravel_Theme_Utils::breadcrumbs();
	get_sidebar('under-header');
?><!--three-fourth content-->
<div class="row">
	<section class="three-fourth">
	<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>					
		<!--post-->
		<article id="post-<?php the_ID(); ?>" <?php post_class("static-content post"); ?>>
			<header class="entry-header">
				<h1><?php the_title(); ?></h1>
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
				<a href="<?php esc_url(get_the_permalink()) ?>">
					<figure>
						<?php the_post_thumbnail('featured', array('title' => '')); ?>
					</figure>
				</a>
			</div>
			<?php } ?>
			<div class="entry-content">
				<?php the_content(); ?>
				<?php wp_link_pages('before=<div class="pagination">&after=</div>'); ?>
			</div>
		</article>
		<!--//post-->	
		<?php comments_template( '', true ); ?>			
		<?php endwhile; ?>
	</section>
	<!--//three-fourth content-->
	<?php 
	get_sidebar('right');
	?>
</div>
<?php
get_footer();