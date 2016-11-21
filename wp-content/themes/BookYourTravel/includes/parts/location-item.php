<?php
	global $post, $item_class, $display_mode, $bookyourtravel_theme_globals, $bookyourtravel_location_helper, $bookyourtravel_accommodation_helper, $bookyourtravel_tour_helper, $bookyourtravel_cruise_helper, $bookyourtravel_car_rental_helper;

	$enable_accommodations = $bookyourtravel_theme_globals->enable_accommodations();
	$enable_cruises = $bookyourtravel_theme_globals->enable_cruises();
	$enable_tours = $bookyourtravel_theme_globals->enable_tours();
	$enable_car_rentals = $bookyourtravel_theme_globals->enable_car_rentals();
	$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
	$default_currency_symbol = $bookyourtravel_theme_globals->get_default_currency_symbol();
	$show_currency_symbol_after = $bookyourtravel_theme_globals->show_currency_symbol_after();
	
	$show_accommodation_count_in_location_items = $bookyourtravel_theme_globals->show_accommodation_count_in_location_items();
	$show_cruise_count_in_location_items = $bookyourtravel_theme_globals->show_cruise_count_in_location_items();
	$show_tour_count_in_location_items = $bookyourtravel_theme_globals->show_tour_count_in_location_items();
	$show_car_rental_count_in_location_items = $bookyourtravel_theme_globals->show_car_rental_count_in_location_items();
	
	$location_id = $post->ID;
	$location_obj = new BookYourTravel_Location($post);
	$base_id = $location_obj->get_base_id();

	$location_image = $location_obj->get_main_image();	
	if (empty($location_image)) {
		$location_image = BookYourTravel_Theme_Utils::get_file_uri('/images/uploads/img.jpg');
	}
	
	$accommodation_count = $cruise_count = $tour_count = $car_rental_count = 0;
	
	$accommodation_price = 0;
	if ($enable_accommodations && $show_accommodation_count_in_location_items) {
		$accommodation_count = (int)$location_obj->get_accommodation_count();
		$accommodation_price = $bookyourtravel_accommodation_helper->get_accommodation_price(0, 0, null, null, $location_id);
	}
	
	if ($enable_cruises && $show_cruise_count_in_location_items) {
		$cruise_count = (int)$location_obj->get_cruise_count();
	}
	
	if ($enable_tours && $show_tour_count_in_location_items) {
		$tour_count = (int)$location_obj->get_tour_count();
	}

	if ($enable_car_rentals && $show_car_rental_count_in_location_items) {
		$car_rental_count = (int)$location_obj->get_car_rental_count();
	}

	if (empty($display_mode) || $display_mode == 'card') {
?>
	<!--location item-->
	<article class="location_item <?php echo esc_attr($item_class); ?>">
		<div>
			<figure>
				<a href="<?php  echo esc_url($location_obj->get_permalink()); ?>" title="<?php echo esc_attr($location_obj->get_title()); ?>">
					<img src="<?php echo esc_url($location_image); ?>" alt="<?php echo esc_attr($location_obj->get_title()); ?>" />
				</a>
			</figure>
			<div class="details">
				<?php 
				echo "<div class='actions'>";
				BookYourTravel_Theme_Utils::render_link_button($location_obj->get_permalink(), "gradient-button", "", esc_html__('View all', 'bookyourtravel')); 
				echo "</div>";
				?>				
				<h4><?php echo $location_obj->get_title(); ?></h4>
				<?php
				if ($enable_accommodations && $show_accommodation_count_in_location_items) {
					BookYourTravel_Theme_Utils::render_field("", "count", $accommodation_count . ' ' . esc_html__('Accommodations', 'bookyourtravel'), '', '', false);
				}
				if ($enable_tours && $show_tour_count_in_location_items) {
					BookYourTravel_Theme_Utils::render_field("", "count", $tour_count . ' ' . esc_html__('Tours', 'bookyourtravel'), '', '', false);
				}
				if ($enable_cruises && $show_cruise_count_in_location_items) {
					BookYourTravel_Theme_Utils::render_field("", "count", $cruise_count . ' ' . esc_html__('Cruises', 'bookyourtravel'), '', '', false);
				}
				if ($enable_car_rentals && $show_car_rental_count_in_location_items) {
					BookYourTravel_Theme_Utils::render_field("", "count", $car_rental_count . ' ' . esc_html__('Car rentals', 'bookyourtravel'), '', '', false);
				}				
				if ($accommodation_price > 0 && $show_accommodation_count_in_location_items) { ?>
				<div class="ribbon">
					<div class="half accommodation">
						<a href="<?php echo esc_url($location_obj->get_permalink()); ?>#hotels" title="<?php esc_attr_e('View all', 'bookyourtravel'); ?>">
							<span class="small"><?php esc_html_e('from', 'bookyourtravel'); ?></span>
							<div class="price">
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
						</a>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</article>
	<!--//location item-->
	<?php 
	} else {
?>
	<li>
		<a href="<?php echo esc_url($location_obj->get_permalink()); ?>">
			<figure>
				<img src="<?php echo esc_url($location_image); ?>" alt="<?php echo esc_attr($location_obj->get_title()); ?>" />
			</figure>
			<h3><?php echo $location_obj->get_title(); ?> <?php if ($location_obj->get_status() == 'draft' || $location_obj->get_status() == 'private') echo '<span class="private">' . esc_html__('Pending', 'bookyourtravel') . '</span>'; ?>
			</h3>			
		</a>
	</li>
<?php }