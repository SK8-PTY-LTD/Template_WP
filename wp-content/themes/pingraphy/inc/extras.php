<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package Pingraphy
 */


/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function pingraphy_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	return $classes;
}
add_filter( 'body_class', 'pingraphy_body_classes' );

if ( version_compare( $GLOBALS['wp_version'], '4.1', '<' ) ) :
	/**
	 * Filters wp_title to print a neat <title> tag based on what is being viewed.
	 *
	 * @param string $title Default title text for current view.
	 * @param string $sep Optional separator.
	 * @return string The filtered title.
	 */
	function pingraphy_wp_title( $title, $sep ) {
		if ( is_feed() ) {
			return $title;
		}

		global $page, $paged;

		// Add the blog name.
		$title .= get_bloginfo( 'name', 'display' );

		// Add the blog description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) ) {
			$title .= " $sep $site_description";
		}

		// Add a page number if necessary.
		if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
			$title .= " $sep " . sprintf( esc_html__( 'Page %s', 'pingraphy' ), max( $paged, $page ) );
		}

		return $title;
	}
	add_filter( 'wp_title', 'pingraphy_wp_title', 10, 2 );

	/**
	 * Title shim for sites older than WordPress 4.1.
	 *
	 * @link https://make.wordpress.org/core/2014/10/29/title-tags-in-4-1/
	 * @todo Remove this function when WordPress 4.3 is released.
	 */
	function pingraphy_render_title() {
		?>
		<title><?php wp_title( '|', true, 'right' ); ?></title>
		<?php
	}
	add_action( 'wp_head', 'pingraphy_render_title' );
endif;


function pingraphy_excerpt_length( $length ) {
	$number = intval (get_theme_mod('pingraphy_general_excerpt_lengh')) > 0 ?  intval (get_theme_mod('pingraphy_general_excerpt_lengh')) : $length;
	return $number;
}
add_filter( 'excerpt_length', 'pingraphy_excerpt_length', 999 );

function pingraphy_excerpt_more( $more ) {
	return get_theme_mod('pingraphy_general_excerpt_end_text', '...');
}
add_filter('excerpt_more', 'pingraphy_excerpt_more');



/**
|------------------------------------------------------------------------------
| Social Links
|------------------------------------------------------------------------------
|
*/
function pingraphy_social_links() {

	$fb = esc_url(get_theme_mod('pingraphy_socials_facebook'));
	$tw = esc_url(get_theme_mod('pingraphy_socials_twitter'));
	$gplus = esc_url(get_theme_mod('pingraphy_socials_gplus'));
	$in = esc_url(get_theme_mod('pingraphy_socials_linkedin'));
	$yt = esc_url(get_theme_mod('pingraphy_socials_youtube'));

	?>
		<h5><?php echo esc_html(get_theme_mod('pingraphy_title_social_profile')); ?></h5>
		<ul class="social-icons clearfix">
			<?php if ($fb) : ?>
				<li>
			 		<a href="<?php echo $fb ?>" rel="nofollow">
			 			<span class="fa fa-facebook"></span>
			 		</a>
		 		</li>
			<?php endif; ?>
			<?php if ($tw) : ?>
				<li>
			 		<a href="<?php echo $tw ?>" rel="nofollow">
			 			<span class="fa fa-twitter"></span>
			 		</a>
		 		</li>
			<?php endif; ?>
			<?php if ($gplus) : ?>
				<li>
			 		<a href="<?php echo $gplus ?>" rel="nofollow">
			 			<span class="fa fa-google-plus"></span>
			 		</a>
		 		</li>
			<?php endif; ?>
			<?php if ($in) : ?>
				<li>
			 		<a href="<?php echo $in ?>" rel="nofollow">
			 			<span class="fa fa-linkedin"></span>
			 		</a>
		 		</li>
			<?php endif; ?>
			<?php if ($yt) : ?>
				<li>
			 		<a href="<?php echo $yt ?>" rel="nofollow">
			 			<span class="fa fa-youtube"></span>
			 		</a>
		 		</li>
			<?php endif; ?>
		</ul>
	<?php
}


/**
|------------------------------------------------------------------------------
| Related Posts
|------------------------------------------------------------------------------
|
| You can show related posts by Categories or Tags. 
| It has two options to show related posts
|
| 1. Thumbnail related posts (default)
| 2. List of related posts
| 
| @return void
|
*/
if (! function_exists('pingraphy_related_posts') ):
	function pingraphy_related_posts() {
		global $post;

		$taxonomy = get_theme_mod('pingraphy_single_related_post_taxonomy', true);
		$numberRelated = 4;
		$args =  array();

		if ($taxonomy == 'tag') {

			$tags = wp_get_post_tags($post->ID);
			$arr_tags = array();
			foreach($tags as $tag) {
				array_push($arr_tags, $tag->term_id);
			}
			
			if (!empty($arr_tags)) { 
			    $args = array(  
				    'tag__in' => $arr_tags,  
				    'post__not_in' => array($post->ID),  
				    'posts_per_page'=> $numberRelated,
			    ); 
			}

		} else {

			 $args = array( 
			 	'category__in' => wp_get_post_categories($post->ID), 
			 	'posts_per_page' => $numberRelated, 
			 	'post__not_in' => array($post->ID) 
			 );

		}

		if (! empty($args) ) {
			$posts = get_posts($args);

			if ($posts) {
				?>
			<h3 class="title-related-posts"><?php _e('Recommended', 'pingraphy') ?></h3>
				<ul class="related grid clearfix">
				<?php
				foreach ($posts as $p) {
					?>
					<li>
						<div class="related-entry">
							<?php if (has_post_thumbnail($p->ID)) : ?>
							<div class="thumbnail">
								<a href="<?php echo get_the_permalink($p->ID) ?>">
								<?php 
									echo get_the_post_thumbnail($p->ID, 'pingraphy-ralated-thumbnail') 
								?>
								</a>
							</div>
							<?php endif; ?>
							<a href="<?php echo get_the_permalink($p->ID) ?>"><?php echo get_the_title($p->ID) ?></a>
						</div>
					</li>
					<?php
				}
				?>
				</ul>
				<?php
			
			}
		}
	}
endif;

if ( ! function_exists( 'pingraphy_header_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function pingraphy_header_posted_on() {
		
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);

		$posted_on = sprintf(
			esc_html_x( 'Posted on %s ', 'post date', 'pingraphy' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
		);

		$byline = sprintf(
			esc_html_x( 'By %s', 'post author', 'pingraphy' ),
			'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
		);
	
		echo '<span class="posted-on"> ' . $posted_on . '</span>';
		echo '<span class="byline">' . $byline . '</span>';
		
	
}
endif;

/**
|------------------------------------------------------------------------------
| Custom Post Meta
|------------------------------------------------------------------------------
|
*/
function pingraphy_footer_post_meta() {
	
	?>

	<div class="entry-meta">
		<div class="entry-footer-right">
			
			<?php
				if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
					echo '<span class="comments-link"><i class="fa fa-comment"></i> ';
					comments_popup_link( esc_html__( '0', 'pingraphy' ), esc_html__( '1', 'pingraphy' ), esc_html__( '%', 'pingraphy' ) );
					echo '</span>';
				}
			?>
			
		</div>
	</div>
	<?php
}