<?php
/**
 * The sidebar containing the car rental widget area.
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since Book Your Travel 1.0
 */

global $post, $current_user, $car_rental_obj, $price_per_day, $price_decimal_places, $default_currency_symbol, $show_currency_symbol_after;

$base_car_rental_id = $car_rental_obj->get_base_id();
$car_rental_location = $car_rental_obj->get_location();
$pick_up_location_title = '';
if ($car_rental_location)
	$pick_up_location_title = $car_rental_location->get_title();
?>
<aside id="secondary" class="right-sidebar widget-area one-fourth" role="complementary">
	<ul>
		<li>
			<article class="car_rental-details">
				<h1><?php echo $car_rental_obj->get_title(); ?></h1>
				<span class="address"><?php echo $pick_up_location_title; ?></span>
				<?php if ($price_per_day > 0) { ?>
				<div class="price">
					<?php esc_html_e('Price from ', 'bookyourtravel'); ?>
					<em>
					<?php if (!$show_currency_symbol_after) { ?>
					<span class="curr"><?php echo esc_html($default_currency_symbol); ?></span>
					<span class="amount"><?php echo number_format_i18n( $price_per_day, $price_decimal_places ); ?></span>
					<?php } else { ?>
					<span class="amount"><?php echo number_format_i18n( $price_per_day, $price_decimal_places ); ?></span>
					<span class="curr"><?php echo esc_html($default_currency_symbol); ?></span>
					<?php } ?>
					</em>
				</div>
				<?php } ?>
				<?php BookYourTravel_Theme_Utils::render_field("description", "", "", BookYourTravel_Theme_Utils::strip_tags_and_shorten($car_rental_obj->get_description(), 100), "", true); ?>
				<?php		
				$tags = $car_rental_obj->get_tags();
				if (count($tags) > 0) {?>
				<div class="tags">
					<ul>
						<?php
							foreach ($tags as $tag) {
								$tag_link = get_term_link( (int)$tag->term_id, 'car_rental_tag' );
								echo '<li><a href="' . $tag_link . '">' . $tag->name . '</a></li>';
							}
						?>						
					</ul>
				</div>
				<?php } ?>
				<?php 
				if (!$car_rental_obj->get_custom_field('hide_inquiry_form')) {
					BookYourTravel_Theme_Utils::render_link_button("#", "gradient-button right contact-car_rental", "", esc_html__('Send inquiry', 'bookyourtravel'));
					?>
					<p class="inquiry-form-thank-you" style="display:none;">
					<?php esc_html_e('Thank you for submitting an inquiry. We will get back to you as soon as we can.', 'bookyourtravel'); ?>
					</p>
					<?php
				} ?>
			</article>				
		</li>			
	<?php 
		wp_reset_postdata(); 
		dynamic_sidebar( 'right-car_rental' ); ?>
	</ul>
</aside><!-- #secondary -->