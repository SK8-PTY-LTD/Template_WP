<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Pingraphy
 */


if ( ! function_exists('pingraphy_header_title') ) :
	function pingraphy_header_title() {
		$logo = get_theme_mod('pingraphy_logo');
		?>
			<?php if ( !empty($logo) ) : ?>
				<meta itemprop="logo" content="<?php echo esc_url($logo); ?>">
				<?php if( is_front_page() || is_home() ) : ?>
				<h1 class="site-title logo" itemprop="headline">
					<a itemprop="url" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" title="<?php echo esc_attr(get_bloginfo( 'description' )); ?>">
						<img src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_attr(get_bloginfo( 'description' )); ?>" />
					</a>
				</h1>
				<?php else : ?>
					<h2 class="site-title logo" itemprop="headline">
						<a itemprop="url" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" title="<?php echo esc_attr(get_bloginfo( 'description' )); ?>">
							<img src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_attr(get_bloginfo( 'description' )); ?>" />
						</a>
					</h2>
				<?php endif ?>
			<?php else : ?>
				<?php if( is_front_page() || is_home() ) : ?>
					<h1 itemprop="headline" class="site-title">
						<a itemprop="url" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" title="<?php echo esc_attr(get_bloginfo( 'description' )); ?>">
							<?php bloginfo( 'name' ); ?>
						</a>
					</h1>
					<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
					<?php else : ?>
						<h2 class="site-title">
						<a itemprop="url" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" title="<?php echo esc_attr(get_bloginfo( 'description' )); ?>">
							<?php bloginfo( 'name' ); ?>
						</a>
						</h2>
						<h3 class="site-description"><?php bloginfo( 'description' ); ?></h3>
					<?php endif ?>
			<?php endif ?>
		<?php
	}
endif;

if ( ! function_exists( 'pingraphy_footer_copyright' ) ) :

	function pingraphy_footer_copyright() {
		$copyright = esc_html(get_theme_mod('pingraphy_copyright'));
		?>
		<?php if ( empty($copyright) ) : ?>
			<?php printf( sprintf(__( '<a href="%s" rel="designer">Pingraphy</a> powered by <a href="http://wordpress.org/">WordPress</a>', 'pingraphy' ), PINGRAPHY_PRO_URL )); ?>
		<?php else : ?>
			<?php echo $copyright; ?>
		<?php endif; 
	}

endif;

if ( ! function_exists( 'pingraphy_the_posts_navigation' ) ) :
/**
 |------------------------------------------------------------------------------
 | Display navigation to next/previous set of posts when applicable.
 |------------------------------------------------------------------------------
 |
 | @todo Remove this function when WordPress 4.3 is released.
 |
 */
function pingraphy_the_posts_navigation() {
	
	// Don't print empty markup if there's only one page.
	if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
		return;
	}

	$nav_style = get_theme_mod ('pingraphy_general_pagination_mode', 'default');

	
	if ( $nav_style == 'numberal') {
		// Previous/next page navigation.
			the_posts_pagination( array(
				'prev_text'          => __( 'Previous page', 'pingraphy' ),
				'next_text'          => __( 'Next page', 'pingraphy' ),
				'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'pingraphy' ) . ' </span>',
			) );

	} else {
		
	?>
	<nav class="navigation paging-navigation clearfix" role="navigation">
		<span class="screen-reader-text"><?php _e( 'Posts navigation', 'pingraphy' ); ?></span>
		<div class="nav-links">

			<?php if ( get_next_posts_link() ) : ?>
			<div class="nav-previous"><?php next_posts_link( __( '<i class="fa fa-angle-double-left"></i> Older posts', 'pingraphy' ) ); ?></div>
			<?php endif; ?>

			<?php if ( get_previous_posts_link() ) : ?>
			<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <i class="fa fa-angle-double-right"></i>', 'pingraphy' ) ); ?></div>
			<?php endif; ?>

		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
	
	
	};
}
endif;

if ( ! function_exists( 'pingraphy_the_post_navigation' ) ) :
/**
 |------------------------------------------------------------------------------
 | Display navigation to next/previous post when applicable.
 |------------------------------------------------------------------------------
 |
 | @todo Remove this function when WordPress 4.3 is released.
 |
 */
function pingraphy_the_post_navigation() {
	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}
	?>
	<nav class="navigation post-navigation clearfix" role="navigation">
		<h2 class="screen-reader-text"><?php _e( 'Post navigation', 'pingraphy' ); ?></h2>
		<div class="nav-links clearfix">
			<?php
				previous_post_link( '<div class="nav-previous">%link</div>', '<i class="fa fa-angle-double-left"></i> %title' );
				next_post_link( '<div class="nav-next">%link</div>', '%title <i class="fa fa-angle-double-right"></i>' );
			?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'pingraphy_entry_footer' ) ) :
/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function pingraphy_entry_footer() {
	// Hide category and tag text for pages.
	if ( 'post' == get_post_type() ) {
		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( esc_html__( ', ', 'pingraphy' ) );
		if ( $categories_list && pingraphy_categorized_blog() ) {
			printf( '<span class="cat-links">' . esc_html__( 'Posted in %1$s', 'pingraphy' ) . '</span>', $categories_list ); // WPCS: XSS OK.
		}

		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', esc_html__( ', ', 'pingraphy' ) );
		if ( $tags_list ) {
			printf( '<span class="tags-links">' . esc_html__( 'Tagged %1$s', 'pingraphy' ) . '</span>', $tags_list ); // WPCS: XSS OK.
		}
	}

	if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<span class="comments-link">';
		comments_popup_link( esc_html__( 'Leave a comment', 'pingraphy' ), esc_html__( '1 Comment', 'pingraphy' ), esc_html__( '% Comments', 'pingraphy' ) );
		echo '</span>';
	}

	edit_post_link( esc_html__( 'Edit', 'pingraphy' ), '<span class="edit-link">', '</span>' );
}
endif;


/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function pingraphy_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'pingraphy_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,

			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'pingraphy_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so pingraphy_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so pingraphy_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in pingraphy_categorized_blog.
 */
function pingraphy_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'pingraphy_categories' );
}
add_action( 'edit_category', 'pingraphy_category_transient_flusher' );
add_action( 'save_post',     'pingraphy_category_transient_flusher' );
