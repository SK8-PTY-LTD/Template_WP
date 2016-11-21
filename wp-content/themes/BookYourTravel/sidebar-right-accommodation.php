<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since Book Your Travel 1.0
 */

global $post, $accommodation_price, $date_from, $date_to, $price_decimal_places, $default_currency_symbol, $show_currency_symbol_after, $current_user, $accommodation_obj, $score_out_of_10, $bookyourtravel_review_helper, $bookyourtravel_theme_globals;

$enable_reviews = $bookyourtravel_theme_globals->enable_reviews();
$accommodation_location = $accommodation_obj->get_location(); 
?>
<aside id="secondary" class="right-sidebar widget-area one-fourth" role="complementary">
	<ul>
		<li>
			<article class="accommodation-details hotel-details">
				<h1><?php echo $accommodation_obj->get_title(); ?>
					<span class="stars">
						<?php for ($i=0;$i<$accommodation_obj->get_custom_field('star_count');$i++) { ?>
						<i class="material-icons">&#xE838;</i>
						<?php } ?>
					</span>
				</h1>
				<?php if ($accommodation_location != null) { ?>
				<span class="address"><?php echo $accommodation_obj->get_custom_field('address'); ?>, <?php echo (isset($accommodation_location) ? $accommodation_location->get_title() : ''); ?></span>
				<?php } ?>				
				<?php if ($score_out_of_10 > 0) { ?><span class="rating"><?php echo $score_out_of_10; ?> / 10</span><?php } ?>

				<?php if ($accommodation_price > 0) { ?>
				<div class="price">
					<?php if (isset($date_from) && isset($date_to)) { ?>
					<?php esc_html_e('Price per night from ', 'bookyourtravel'); ?>
					<?php } else { ?>
					<?php esc_html_e('Price from ', 'bookyourtravel'); ?>
					<?php } ?>
					<em>
					<?php if (!$show_currency_symbol_after) { ?>
					<span class="curr"><?php echo esc_html($default_currency_symbol); ?></span>
					<span class="amount"><?php echo number_format_i18n( $accommodation_price, $price_decimal_places ); ?></span>
					<?php } else { ?>
					<span class="amount"><?php echo number_format_i18n( $accommodation_price, $price_decimal_places ); ?></span>
					<span class="curr"><?php echo esc_html($default_currency_symbol); ?></span>
					<?php } ?>
					</em>
				</div>
				<?php } ?>
				<?php BookYourTravel_Theme_Utils::render_field("description", "", "", BookYourTravel_Theme_Utils::strip_tags_and_shorten($accommodation_obj->get_description(), 100), "", true); ?>
				<?php
				$tags = $accommodation_obj->get_tags();
				if (count($tags) > 0) {?>
				<div class="tags">
					<ul>
						<?php
							foreach ($tags as $tag) {
								$tag_link = get_term_link( (int)$tag->term_id, 'acc_tag' );
								echo '<li><a href="' . $tag_link . '">' . $tag->name . '</a></li>';
							}
						?>						
					</ul>
				</div>
				<?php } ?>
				<?php 
				if ($enable_reviews) {
					$reviews_by_current_user_query = $bookyourtravel_review_helper->list_reviews($accommodation_obj->get_base_id(), $current_user->ID);	
					if (!$reviews_by_current_user_query->have_posts() && is_user_logged_in()) {
						BookYourTravel_Theme_Utils::render_link_button("#", "gradient-button right leave-review review-accommodation", "", esc_html__('Leave a review', 'bookyourtravel'));
					}
					?>
					<p class="review-form-thank-you" style="display:none;">
					<?php esc_html_e('Thank you for submitting a review.', 'bookyourtravel'); ?>
					</p>
					<?php
				}
				if (!$accommodation_obj->get_custom_field('hide_inquiry_form')) {
					BookYourTravel_Theme_Utils::render_link_button("#", "gradient-button right contact-accommodation", "", esc_html__('Send inquiry', 'bookyourtravel'));
					?>
					<p class="inquiry-form-thank-you" style="display:none;">
					<?php esc_html_e('Thank you for submitting an inquiry. We will get back to you as soon as we can.', 'bookyourtravel'); ?>
					</p>
					<?php
				} ?>
				
			</article>				
		</li>
		<?php if ($enable_reviews) { ?>
		<li>
			<?php 
				$all_reviews_query = $bookyourtravel_review_helper->list_reviews($accommodation_obj->get_base_id());
				if ($all_reviews_query->have_posts()) { 
					while ($all_reviews_query->have_posts()) { 
					$all_reviews_query->the_post();
					global $post;	
					$likes = get_post_meta($post->ID, 'review_likes', true); 
					$author = get_the_author();
					?>
					<!--testimonials-->
					<article class="testimonials">
						<blockquote><?php echo $likes; ?></blockquote>
						<span class="name"><?php echo $author; ?></span>
					</article>
					<!--//testimonials-->
			<?php break; } } ?>
		</li>
		<?php } // $enable_reviews ?>
	<?php 
		wp_reset_postdata(); 
		dynamic_sidebar( 'right-accommodation' ); ?>
	</ul>
</aside><!-- #secondary -->