<?php
/*	Template Name: Byt Home page
 * The Front Page template file.
 *
 * This is the template of the page that can be selected to be shown as the front page.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since Book Your Travel 1.0
 */
global $bookyourtravel_theme_globals, $post, $item_class;

$page_id = $post->ID;
$page_custom_fields = get_post_custom( $page_id);

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

get_header();  	
get_sidebar('under-header');

$allowed_tags = array();
$allowed_tags['span'] = array('class' => array());
?>
<div class="row">
	<?php
	if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'left')
		get_sidebar('left');
	?>
	<section class="<?php echo esc_attr($section_class); ?>">
	<?php
	if (have_posts()) { ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class(); ?> id="page-<?php the_ID(); ?>">
				<?php the_content( wp_kses(__( 'Continue reading <span class="meta-nav">&rarr;</span>', 'bookyourtravel' ), $allowed_tags) ); ?>
				<?php wp_link_pages('before=<div class="pagination">&after=</div>'); ?>
			</article>
		<?php endwhile;
	}
	get_sidebar('home-content');
	get_sidebar('home-footer');
	?>
	</section>
	<?php
	if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'right')
		get_sidebar('right');
?>
</div>
<?php
get_footer();