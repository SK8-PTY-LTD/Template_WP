<?php
	global $post, $item_class, $display_mode, $bookyourtravel_theme_globals, $current_url, $bookyourtravel_car_rental_helper;
	
	$list_user_car_rentals_url = $bookyourtravel_theme_globals->get_list_user_car_rentals_url();
	$submit_car_rentals_url = $bookyourtravel_theme_globals->get_submit_car_rentals_url();
	
	$car_rental_id = $post->ID;
	$car_rental_obj = new BookYourTravel_Car_Rental($post);

	$car_rental_image = $car_rental_obj->get_main_image();	
	if (empty($car_rental_image)) {
		$car_rental_image = BookYourTravel_Theme_Utils::get_file_uri('/images/uploads/img.jpg');
	}
	
	$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
	$default_currency_symbol = $bookyourtravel_theme_globals->get_default_currency_symbol();
	$show_currency_symbol_after = $bookyourtravel_theme_globals->show_currency_symbol_after();
	
	$price_per_day = $car_rental_obj->get_custom_field('price_per_day');
	
	if (empty($display_mode) || $display_mode == 'card') {
?>
<!--car rental-->
<article class="car_rental_item <?php echo esc_attr($item_class); ?>">
	<div>
		<figure>
			<a href="<?php echo esc_url($car_rental_obj->get_permalink()); ?>" title="<?php echo esc_attr($car_rental_obj->get_title()); ?>">
				<img src="<?php echo esc_url($car_rental_image); ?>" alt="<?php echo esc_attr($car_rental_obj->get_title()); ?>" />
			</a>
		</figure>
		<div class="details cars">
			<h3><?php echo $car_rental_obj->get_title(); ?></h3>
			<?php if ($price_per_day > 0) { ?>
			<div class="price">
				<?php esc_html_e('Price per day ', 'bookyourtravel'); ?>
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
			<div class="description clearfix ">
				<?php BookYourTravel_Theme_Utils::render_field("text-wrap car_type", "", esc_html__('Car type', 'bookyourtravel'), $car_rental_obj->get_type_name(), '', false, true); ?>
				<?php BookYourTravel_Theme_Utils::render_field("text-wrap max_people", "", esc_html__('Max people', 'bookyourtravel'), $car_rental_obj->get_custom_field('max_count'), '', false, true); ?>
				<?php BookYourTravel_Theme_Utils::render_field("text-wrap door_count", "", esc_html__('Door count', 'bookyourtravel'), $car_rental_obj->get_custom_field('number_of_doors'), '', false, true); ?>
				<?php BookYourTravel_Theme_Utils::render_field("text-wrap min_age", "", esc_html__('Minimum driver age', 'bookyourtravel'), $car_rental_obj->get_custom_field('min_age'), '', false, true); ?>
				<?php BookYourTravel_Theme_Utils::render_field("text-wrap transmission", "", esc_html__('Transmission', 'bookyourtravel'), ($car_rental_obj->get_custom_field('transmission_type') == 'manual' ? esc_html__('Manual', 'bookyourtravel') : esc_html__('Automatic', 'bookyourtravel')), '', false, true); ?>
				<?php BookYourTravel_Theme_Utils::render_field("text-wrap air_conditioned", "", esc_html__('Air-conditioned?', 'bookyourtravel'), ($car_rental_obj->get_custom_field('is_air_conditioned') ? esc_html__('Yes', 'bookyourtravel') : esc_html__('No', 'bookyourtravel')), '', false, true); ?>
				<?php BookYourTravel_Theme_Utils::render_field("text-wrap unlimited_mileage", "", esc_html__('Unlimited mileage?', 'bookyourtravel'), ($car_rental_obj->get_custom_field('is_unlimited_mileage') ? esc_html__('Yes', 'bookyourtravel') : esc_html__('No', 'bookyourtravel')), '', false, true); ?>
				<?php 
				$car_rental_extra_fields = $bookyourtravel_theme_globals->get_car_rental_extra_fields();
				BookYourTravel_Theme_Utils::render_tab_extra_fields('car_rental_extra_fields', $car_rental_extra_fields, 'description', $car_rental_obj, '', false, false); ?>
			</div>
			<?php 
			echo "<div class='actions'>";
			if (!empty($current_url) && $current_url == $list_user_car_rentals_url)
				BookYourTravel_Theme_Utils::render_link_button(esc_url( add_query_arg( 'fesid', $car_rental_id, $submit_car_rentals_url )), "gradient-button clearfix", "", esc_html__('Edit', 'bookyourtravel')); 
			else 
				BookYourTravel_Theme_Utils::render_link_button($car_rental_obj->get_permalink(), "gradient-button clearfix", "", esc_html__('Book now', 'bookyourtravel')); 
			echo "</div>";
			?>
		</div>
	</div>
</article>
<!--//car rental item-->
<?php 
	} else {
?>
	<li>
		<a href="<?php echo esc_url($car_rental_obj->get_permalink()); ?>">
			<h3><?php echo $car_rental_obj->get_title(); ?> <?php if ($car_rental_obj->get_status() == 'draft' || $car_rental_obj->get_status() == 'private') echo '<span class="private">' . esc_html__('Pending', 'bookyourtravel') . '</span>'; ?>
			</h3>
			<?php if ($price_per_day > 0) { ?>
			<p>
				<?php 
				$price_string = '';
				if (!$show_currency_symbol_after) { 
					$price_string = '<span class="curr">' . $default_currency_symbol . '</span>';
					$price_string .= '<span class="amount">' . number_format_i18n( $price_per_day, $price_decimal_places ) . '</span>';
				} else { 
					$price_string = '<span class="amount">' . number_format_i18n( $price_per_day, $price_decimal_places ) . '</span>';
					$price_string .= '<span class="curr">' . $default_currency_symbol . '</span>';
				}
				echo sprintf(esc_html__('From %s per day', 'bookyourtravel'), $price_string);
				?>
			</p>
			<?php } ?>
		</a>
	</li>
<?php }