<?php
	global $post, $date_from, $date_to, $accommodation_price, $item_class, $display_mode, $current_url, $bookyourtravel_theme_globals, $bookyourtravel_review_helper, $bookyourtravel_accommodation_helper;
	
	$list_user_accommodations_url = $bookyourtravel_theme_globals->get_list_user_accommodations_url();
	$submit_accommodations_url = $bookyourtravel_theme_globals->get_submit_accommodations_url();
	$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
	$default_currency_symbol = $bookyourtravel_theme_globals->get_default_currency_symbol();
	$show_currency_symbol_after = $bookyourtravel_theme_globals->show_currency_symbol_after();
	
	$accommodation_id = $post->ID;
	$accommodation_obj = new BookYourTravel_Accommodation($post);
	$accommodation_rent_type = $accommodation_obj->get_rent_type();
	$accommodation_rent_type_str = __('night', 'bookyourtravel');
	if ($accommodation_rent_type == 1) {
		$accommodation_rent_type_str = __('week', 'bookyourtravel');
	} else if ($accommodation_rent_type == 2) {
		$accommodation_rent_type_str = __('month', 'bookyourtravel');
	}	
	$base_id = $accommodation_obj->get_base_id();
	$reviews_total = $bookyourtravel_review_helper->get_reviews_count($base_id);
	
	$accommodation_image = $accommodation_obj->get_main_image();	
	if (empty($accommodation_image)) {
		$accommodation_image = BookYourTravel_Theme_Utils::get_file_uri('/images/uploads/img.jpg');
	}
	
	$score_out_of_10 = 0;
	if ($reviews_total > 0) {
		$review_score = $accommodation_obj->get_custom_field('review_score', false);
		$score_out_of_10 = floor($review_score * 10);
	}
	
	$accommodation_location = $accommodation_obj->get_location();
	if (isset($post->accommodation_price)) {
		$accommodation_price = number_format($post->accommodation_price, $price_decimal_places, ".", "");
	} else {
		$accommodation_price = number_format($bookyourtravel_accommodation_helper->get_accommodation_price($accommodation_id, 0, $date_from, $date_to), $price_decimal_places, ".", "");
	}
	$accommodation_description_html = BookYourTravel_Theme_Utils::strip_tags_and_shorten($accommodation_obj->get_description(), 100) . '<a href="' . esc_url($accommodation_obj->get_permalink()) . '">' . esc_html__('More info', 'bookyourtravel') . '</a>';
	
	if (empty($display_mode) || $display_mode == 'card') {
?><!--accommodation item-->
	<article class="accommodation_item <?php echo esc_attr($item_class); ?>">
		<div>
			<figure>
				<a href="<?php echo esc_url($accommodation_obj->get_permalink()); ?>" title="<?php echo esc_attr($accommodation_obj->get_title()); ?>">
					<img src="<?php echo esc_url($accommodation_image); ?>" alt="<?php echo esc_attr($accommodation_obj->get_title()); ?>" />
				</a>
			</figure>
			<div class="details">
				<h3>
					<?php echo $accommodation_obj->get_title(); ?> <?php if ($accommodation_obj->get_status() == 'draft' || $accommodation_obj->get_status() == 'private') echo '<span class="private">' . esc_html__('Pending', 'bookyourtravel') . '</span>'; ?>
					<span class="stars">
					<?php
					for ( $i = 0; $i < $accommodation_obj->get_custom_field('star_count'); $i++ ) { ?>
						<i class="material-icons">&#xE838;</i>
					<?php } ?>
					</span>
				</h3>
				<?php 
				
				// display accommodation address
				$accommodation_address = $accommodation_obj->get_custom_field('address');
				$accommodation_address .= isset($accommodation_location) && is_object($accommodation_location) ? (!empty($accommodation_address) ? ', ' : '') . $accommodation_location->get_title() : '';
				BookYourTravel_Theme_Utils::render_field("", "address", $accommodation_address, '', '', false, false);

				if ($score_out_of_10 > 0) {
					// display score out of 10
					BookYourTravel_Theme_Utils::render_field("", "rating", $score_out_of_10 . ' / 10', "", '', false, false);
				}			
				if ($accommodation_price > 0) { ?>
				<div class="price">
					<?php if (isset($date_from) && isset($date_to)) { ?>
					<?php	echo sprintf(esc_html__('Price per %s from ', 'bookyourtravel'), $accommodation_rent_type_str); ?>
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
				<?php 
					BookYourTravel_Theme_Utils::render_field("description clearfix", "", "", $accommodation_description_html, '', false, true);
					echo "<div class='actions'>";
					if (!empty($current_url) && $current_url == $list_user_accommodations_url)
						BookYourTravel_Theme_Utils::render_link_button(esc_url( add_query_arg( 'fesid', $accommodation_id, $submit_accommodations_url )), "gradient-button clearfix", "", esc_html__('Edit', 'bookyourtravel')); 
					else 
						BookYourTravel_Theme_Utils::render_link_button($accommodation_obj->get_permalink(), "gradient-button clearfix", "", esc_html__('Book now', 'bookyourtravel')); 
					echo "</div>";
				?>
			</div>
		</div>
	</article>
	<!--//accommodation item-->
<?php 
	} else {
?>
	<li>
		<a href="<?php echo esc_url($accommodation_obj->get_permalink()); ?>">
			<h3><?php echo $accommodation_obj->get_title(); ?> <?php if ($accommodation_obj->get_status() == 'draft' || $accommodation_obj->get_status() == 'private') echo '<span class="private">' . esc_html__('Pending', 'bookyourtravel') . '</span>'; ?>
				<span class="stars">
				<?php
				for ( $i = 0; $i < $accommodation_obj->get_custom_field('star_count'); $i++ ) { ?>
					<i class="material-icons">&#xE838;</i>
				<?php } ?>
				</span>
			</h3>
			<?php if ($accommodation_price > 0) { ?>
			<p>
				<?php 
				$price_string = '';
				if (!$show_currency_symbol_after) { 
					$price_string = '<span class="curr">' . $default_currency_symbol . '</span>';
					$price_string .= '<span class="amount">' . number_format_i18n( $accommodation_price, $price_decimal_places ) . '</span>';
				} else { 
					$price_string = '<span class="amount">' . number_format_i18n( $accommodation_price, $price_decimal_places ) . '</span>';
					$price_string .= '<span class="curr">' . $default_currency_symbol . '</span>';
				}
				
				if (isset($date_from) && isset($date_to)) {
					echo sprintf(esc_html__('From %s per %s', 'bookyourtravel'), $price_string, $accommodation_rent_type_str);
				} else {
					echo sprintf(esc_html__('%s per %s', 'bookyourtravel'), $price_string, $accommodation_rent_type_str);
				}
				?>
			</p>
			<?php } ?>
			<?php
			if ($score_out_of_10 > 0) {
				// display score out of 10
				BookYourTravel_Theme_Utils::render_field("", "rating", $score_out_of_10 . ' / 10', "", '', false, false);
			}	
			?>
		</a>
	</li>
<?php }