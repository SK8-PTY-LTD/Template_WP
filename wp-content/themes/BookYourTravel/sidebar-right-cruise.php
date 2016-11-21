<?php
/**
 * The sidebar containing the cruise widget area.
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since Book Your Travel 1.0
 */

global $post, $cruise_price, $current_user, $show_currency_symbol_after, $default_currency_symbol, $price_decimal_places, $cruise_obj, $score_out_of_10, $bookyourtravel_theme_globals, $bookyourtravel_review_helper;
$enable_reviews = $bookyourtravel_theme_globals->enable_reviews();
?>
<aside id="secondary" class="right-sidebar widget-area one-fourth" role="complementary">
	<ul>
		<li>
			<article class="cruise-details">
				<h1><?php echo $cruise_obj->get_title(); ?></h1>
				<?php if ($score_out_of_10 > 0) { ?>
				<span class="rating"><?php echo $score_out_of_10; ?> / 10</span>
				<?php } ?>
				
				<?php if ($cruise_price > 0) { ?>
				<div class="price">
					<?php esc_html_e('Price from ', 'bookyourtravel'); ?>
					<em>
					<?php if (!$show_currency_symbol_after) { ?>
					<span class="curr"><?php echo esc_html($default_currency_symbol); ?></span>
					<span class="amount"><?php echo number_format_i18n( $cruise_price, $price_decimal_places ); ?></span>
					<?php } else { ?>
					<span class="amount"><?php echo number_format_i18n( $cruise_price, $price_decimal_places ); ?></span>
					<span class="curr"><?php echo esc_html($default_currency_symbol); ?></span>
					<?php } ?>
					</em>
				</div>
				<?php } ?>
				
				<?php BookYourTravel_Theme_Utils::render_field("description", "", "", BookYourTravel_Theme_Utils::strip_tags_and_shorten($cruise_obj->get_description(), 100), "", true); ?>
				<?php
				$tags = $cruise_obj->get_tags();
				if (count($tags) > 0) {?>
				<div class="tags">
					<ul>
						<?php
							foreach ($tags as $tag) {
								$tag_link = get_term_link( (int)$tag->term_id, 'cruise_tag' );
								echo '<li><a href="' . $tag_link . '">' . $tag->name . '</a></li>';
							}
						?>						
					</ul>
				</div>
				<?php } ?>
				
				<?php 
				if ($enable_reviews) {
					$reviews_by_current_user_query = $bookyourtravel_review_helper->list_reviews($cruise_obj->get_base_id(), $current_user->ID);
					if (!$reviews_by_current_user_query->have_posts() && is_user_logged_in()) {
						BookYourTravel_Theme_Utils::render_link_button("#", "gradient-button right leave-review review-cruise", "", esc_html__('Leave a review', 'bookyourtravel'));
					} 
					?>
					<p class="review-form-thank-you" style="display:none;">
					<?php esc_html_e('Thank you for submitting a review.', 'bookyourtravel'); ?>
					</p>
					<?php
				}
				if (!$cruise_obj->get_custom_field('hide_inquiry_form')) {
					BookYourTravel_Theme_Utils::render_link_button("#", "gradient-button right contact-cruise", "", esc_html__('Send inquiry', 'bookyourtravel'));
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
			$all_reviews_query = $bookyourtravel_review_helper->list_reviews($cruise_obj->get_base_id());
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
					<?php 
					break; 
				} 
			} ?>
		</li>
		<?php 
		}
		wp_reset_postdata(); 
		dynamic_sidebar( 'right-cruise' ); ?>
	</ul>
</aside><!-- #secondary -->