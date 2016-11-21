<?php
	global $post, $item_class, $display_mode, $bookyourtravel_theme_globals, $current_url, $bookyourtravel_tour_helper, $bookyourtravel_review_helper;

	$list_user_tours_url = $bookyourtravel_theme_globals->get_list_user_tours_url();
	$submit_tours_url = $bookyourtravel_theme_globals->get_submit_tours_url();
	
	$tour_id = $post->ID;
	$tour_obj = new BookYourTravel_Tour($post);
	$base_id = $tour_obj->get_base_id();
	$reviews_total = $bookyourtravel_review_helper->get_reviews_count($base_id);

	$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
	$default_currency_symbol = $bookyourtravel_theme_globals->get_default_currency_symbol();
	$show_currency_symbol_after = $bookyourtravel_theme_globals->show_currency_symbol_after();
	
	$tour_image = $tour_obj->get_main_image();	
	if (empty($tour_image)) {
		$tour_image = BookYourTravel_Theme_Utils::get_file_uri('/images/uploads/img.jpg');
	}

	$is_price_per_group = $tour_obj->get_is_price_per_group();
	
	$score_out_of_10 = 0;
	if ($reviews_total > 0) {
		$review_score = $tour_obj->get_custom_field('review_score', false);
		$score_out_of_10 = round($review_score * 10);
	}	
	
	$tour_description_html = BookYourTravel_Theme_Utils::strip_tags_and_shorten($tour_obj->get_description(), 100) . '<a href="' . $tour_obj->get_permalink() . '">' . esc_html__('More info', 'bookyourtravel') . '</a>';

	$current_date = date('Y-m-d', time());
	$tour_min_price = $bookyourtravel_tour_helper->get_tour_min_price($tour_id, $current_date);
	
	$tour_locations = $tour_obj->get_locations();
	$tour_location_title = '';	
	if ($tour_locations && count($tour_locations) > 0) {
		foreach ($tour_locations as $location_id) {
			$location_obj = new BookYourTravel_Location((int)$location_id);
			$location_title = $location_obj->get_title();
			$tour_location_title .= $location_title . ', ';
		}
	}
	$tour_location_title = rtrim($tour_location_title, ', ');
		
	if (empty($display_mode) || $display_mode == 'card') {
?>
<!--tour item-->
<article class="tour_item <?php echo esc_attr($item_class); ?>">
	<div>
		<figure>
			<a href="<?php echo esc_url($tour_obj->get_permalink()); ?>" title="<?php echo esc_attr($tour_obj->get_title()); ?>">
				<img src="<?php echo esc_url($tour_image); ?>" alt="<?php echo esc_attr($tour_obj->get_title()); ?>" />
			</a>
		</figure>
		<div class="details">
			<h3><?php echo $tour_obj->get_title(); ?></h3>
			<?php
			// display tour address
			BookYourTravel_Theme_Utils::render_field("", "address", $tour_location_title, '', '', false, false); 
			if ($score_out_of_10 > 0) { 
				// display score out of 10
				BookYourTravel_Theme_Utils::render_field("", "rating", $score_out_of_10 . ' / 10', "", '', false, false);
			} 
			if ($tour_min_price > 0) { ?>
			<div class="price">
				<?php 
				if (!$is_price_per_group) 
					_e('Price per person from ', 'bookyourtravel');
				else
					_e('Price per group from ', 'bookyourtravel');
				?>
				<em>
				<?php if (!$show_currency_symbol_after) { ?>
				<span class="curr"><?php echo esc_html($default_currency_symbol); ?></span>
				<span class="amount"><?php echo number_format_i18n( $tour_min_price, $price_decimal_places ); ?></span>
				<?php } else { ?>
				<span class="amount"><?php echo number_format_i18n( $tour_min_price, $price_decimal_places ); ?></span>
				<span class="curr"><?php echo esc_html($default_currency_symbol); ?></span>
				<?php } ?>
				</em>
			</div>
			<?php 
			} 
			BookYourTravel_Theme_Utils::render_field("description clearfix", "", "", $tour_description_html, '', false, true);
			echo "<div class='actions'>";
			if (!empty($current_url) && $current_url == $list_user_tours_url)
				BookYourTravel_Theme_Utils::render_link_button(esc_url( add_query_arg( 'fesid', $tour_id, $submit_tours_url )), "gradient-button clearfix", "", esc_html__('Edit', 'bookyourtravel')); 
			else 
				BookYourTravel_Theme_Utils::render_link_button($tour_obj->get_permalink(), "gradient-button clearfix", "", esc_html__('Book now', 'bookyourtravel')); 
			echo "</div>";
			?>
		</div>
	</div>
</article>
<!--//tour item-->
<?php 
	} else {
?>
	<li>
		<a href="<?php echo esc_url($tour_obj->get_permalink()); ?>">
			<h3><?php echo $tour_obj->get_title(); ?> <?php if ($tour_obj->get_status() == 'draft' || $tour_obj->get_status() == 'private') echo '<span class="private">' . esc_html__('Pending', 'bookyourtravel') . '</span>'; ?>
			</h3>
			<?php if ($tour_min_price > 0) { ?>
			<p>
				<?php 
				$price_string = '';
				if (!$show_currency_symbol_after) { 
					$price_string = '<span class="curr">' . $default_currency_symbol . '</span>';
					$price_string .= '<span class="amount">' . number_format_i18n( $tour_min_price, $price_decimal_places ) . '</span>';
				} else { 
					$price_string = '<span class="amount">' . number_format_i18n( $tour_min_price, $price_decimal_places ) . '</span>';
					$price_string .= '<span class="curr">' . $default_currency_symbol . '</span>';
				}
				if (!$is_price_per_group) 
					echo sprintf(esc_html__('From %s per person', 'bookyourtravel'), $price_string);
				else
					echo sprintf(esc_html__('From %s per group', 'bookyourtravel'), $price_string);				
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