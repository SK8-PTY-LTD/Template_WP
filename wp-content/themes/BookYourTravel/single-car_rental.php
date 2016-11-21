<?php 

get_header();  
BookYourTravel_Theme_Utils::breadcrumbs();
get_sidebar('under-header');

global $post, $bookyourtravel_theme_globals, $price_per_day, $current_user, $car_rental_obj, $entity_obj, $default_car_rental_tabs, $score_out_of_10, $bookyourtravel_car_rental_helper, $bookyourtravel_theme_of_custom;

$enable_extra_items = $bookyourtravel_theme_globals->enable_extra_items();
$enable_reviews = $bookyourtravel_theme_globals->enable_reviews();
$enable_cruises = $bookyourtravel_theme_globals->enable_cruises();
$car_rental_extra_fields = $bookyourtravel_theme_globals->get_car_rental_extra_fields();
$tab_array = $bookyourtravel_theme_globals->get_car_rental_tabs();
$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
$default_currency_symbol = $bookyourtravel_theme_globals->get_default_currency_symbol();
$show_currency_symbol_after = $bookyourtravel_theme_globals->show_currency_symbol_after();
$light_slider_pause_between_slides = $bookyourtravel_theme_globals->get_light_slider_pause_between_slides();

if ( have_posts() ) {

	the_post();
	$car_rental_obj = new BookYourTravel_Car_Rental($post);
	$car_rental_is_reservation_only = $car_rental_obj->get_is_reservation_only();
	$entity_obj = $car_rental_obj;
	
	$price_per_day = $car_rental_obj->get_custom_field('price_per_day');
	
	$car_rental_location = $car_rental_obj->get_location();
	$pick_up_location_title = '';
	if ($car_rental_location)
		$pick_up_location_title = $car_rental_location->get_title();
?>
<div class="row">
	<script>
		window.postType = 'car_rental';
		window.pauseBetweenSlides = <?php echo $light_slider_pause_between_slides * 1000; ?>;
	</script>
<?php		
	get_template_part('includes/parts/inquiry', 'form');
?>
	<!--car rental three-fourth content-->
	<section class="three-fourth">
		<?php	
		get_template_part('includes/parts/car_rental', 'booking-form');
		get_template_part('includes/parts/car_rental', 'confirmation-form');	
		?>	
		<script>	
			window.defaultDateToText = <?php echo json_encode(esc_html__('Select your to date using the calendar above.', 'bookyourtravel')); ?>;
			window.carRentalId = <?php echo json_encode($car_rental_obj->get_id()); ?>;
			window.formSingleError = <?php echo json_encode(esc_html__('You failed to provide 1 field. It has been highlighted below.', 'bookyourtravel')); ?>;
			window.formMultipleError = <?php echo json_encode(esc_html__('You failed to provide {0} fields. They have been highlighted below.', 'bookyourtravel'));  ?>;
			window.carRentalPrice = <?php echo json_encode($price_per_day); ?>;
			window.carRentalTitle = <?php echo json_encode($car_rental_obj->get_title()); ?>;
			window.carRentalCarType = <?php echo json_encode($car_rental_obj->get_type_name()); ?>;
			window.carRentalPickUp = <?php echo json_encode($pick_up_location_title); ?>;
			window.currentMonth = <?php echo json_encode(date_i18n('n')); ?>;
			window.currentYear = <?php echo json_encode( date_i18n('Y')); ?>;
			window.currentDay = <?php echo json_encode( date_i18n('j')); ?>;		
			window.carRentalIsReservationOnly = <?php echo (int)$car_rental_is_reservation_only; ?>;
			window.enableExtraItems = <?php echo json_encode($enable_extra_items); ?>;
			window.showPriceBreakdownLabel = <?php echo json_encode(esc_html__('Show price breakdown', 'bookyourtravel')); ?>;
			window.hidePriceBreakdownLabel = <?php echo json_encode(esc_html__('Hide price breakdown', 'bookyourtravel')); ?>;
			window.dateLabel = <?php echo json_encode(esc_html__('Date', 'bookyourtravel')); ?>;
			window.itemLabel = <?php echo json_encode(esc_html__('Item', 'bookyourtravel')); ?>;
			window.priceLabel = <?php echo json_encode(esc_html__('Price', 'bookyourtravel')); ?>;
			window.pricedPerDayPerPersonLabel = <?php echo json_encode(esc_html__('priced per day, per person', 'bookyourtravel')); ?>;
			window.pricedPerDayLabel = <?php echo json_encode(esc_html__('priced per day', 'bookyourtravel')); ?>;
			window.pricedPerPersonLabel = <?php echo json_encode(esc_html__('priced per person', 'bookyourtravel')); ?>;
			window.pricePerDayLabel = <?php echo json_encode(esc_html__('Price per day', 'bookyourtravel')); ?>;
			window.extraItemsPriceTotalLabel = <?php echo json_encode(esc_html__('Extra items total price', 'bookyourtravel')); ?>;
			window.priceTotalLabel = <?php echo json_encode(esc_html__('Total price', 'bookyourtravel')); ?>;

		</script>
		<?php $car_rental_obj->render_image_gallery(); ?>
		<!--inner navigation-->
		<nav class="inner-nav">
			<ul>
				<?php do_action( 'bookyourtravel_show_single_car_rental_tab_items_before' ); ?>
				<?php
				$first_display_tab = '';			
				$i = 0;
				if (is_array($tab_array) && count($tab_array) > 0) {
					foreach ($tab_array as $tab) {
					
						if (!isset($tab['hide']) || $tab['hide'] != '1') {
					
							$tab_label = '';
							if (isset($tab['label'])) {
								$tab_label = $tab['label'];
								$tab_label = $bookyourtravel_theme_of_custom->get_translated_dynamic_string($bookyourtravel_theme_of_custom->get_option_id_context('car_rental_tabs') . ' ' . $tab['label'], $tab_label);
							}
						
							if ($i==0) {
								$first_display_tab = $tab['id'];
							}
								
							if ($tab['id'] == 'description' || $tab['id'] == 'availability') {
								BookYourTravel_Theme_Utils::render_tab("car_rental", $tab['id'], '',  '<a href="#' . $tab['id'] . '" title="' . $tab_label . '">' . $tab_label . '</a>');
							} else {
								$all_empty_fields = BookYourTravel_Theme_Utils::are_tab_fields_empty('car_rental_extra_fields', $car_rental_extra_fields, $tab['id'], $car_rental_obj);
								
								if (!$all_empty_fields) {
									BookYourTravel_Theme_Utils::render_tab("car_rental", $tab['id'], '',  '<a href="#' . $tab['id'] . '" title="' . $tab_label . '">' . $tab_label . '</a>');
								}
							}

							$i++;
						}
					}
				} 	
				?>
				<?php do_action( 'bookyourtravel_show_single_car_rental_tab_items_after' ); ?>
			</ul>
		</nav>
		<!--//inner navigation-->
		<?php do_action( 'bookyourtravel_show_single_car_rental_tab_content_before' ); ?>
		<!--description-->
		<section id="description" class="tab-content <?php echo $first_display_tab == 'description' ? 'initial' : ''; ?>">
			<article>
				<h2><?php echo $car_rental_obj->get_title(); ?></h2>
				<?php BookYourTravel_Theme_Utils::render_field("text-wrap", "", "", $car_rental_obj->get_description()); ?>
				<div class="text-wrap">
				<?php BookYourTravel_Theme_Utils::render_field("location", "", esc_html__('Location', 'bookyourtravel'), $pick_up_location_title, '', false, true); ?>
				<?php BookYourTravel_Theme_Utils::render_field("car_type", "", esc_html__('Car type', 'bookyourtravel'), $car_rental_obj->get_type_name(), '', false, true); ?>
				<?php BookYourTravel_Theme_Utils::render_field("max_people", "", esc_html__('Max people', 'bookyourtravel'), $car_rental_obj->get_custom_field('max_count'), '', false, true); ?>
				<?php BookYourTravel_Theme_Utils::render_field("door_count", "", esc_html__('Door count', 'bookyourtravel'), $car_rental_obj->get_custom_field('number_of_doors'), '', false, true); ?>
				<?php BookYourTravel_Theme_Utils::render_field("min_age", "", esc_html__('Minimum driver age', 'bookyourtravel'), $car_rental_obj->get_custom_field('min_age'), '', false, true); ?>
				<?php BookYourTravel_Theme_Utils::render_field("transmission", "", esc_html__('Transmission', 'bookyourtravel'), ($car_rental_obj->get_custom_field('transmission_type') == 'manual' ? esc_html__('Manual', 'bookyourtravel') : esc_html__('Automatic', 'bookyourtravel')), '', false, true); ?>
				<?php BookYourTravel_Theme_Utils::render_field("air_conditioned", "", esc_html__('Air-conditioned?', 'bookyourtravel'), ($car_rental_obj->get_custom_field('is_air_conditioned') ? esc_html__('Yes', 'bookyourtravel') : esc_html__('No', 'bookyourtravel')), '', false, true); ?>
				<?php BookYourTravel_Theme_Utils::render_field("unlimited_mileage", "", esc_html__('Unlimited mileage?', 'bookyourtravel'), ($car_rental_obj->get_custom_field('is_unlimited_mileage') ? esc_html__('Yes', 'bookyourtravel') : esc_html__('No', 'bookyourtravel')), '', false, true); ?>
				<?php BookYourTravel_Theme_Utils::render_tab_extra_fields('car_rental_extra_fields', $car_rental_extra_fields, 'description', $car_rental_obj, '', false, false); ?>
				</div>
			</article>
		</section>
		<!--//description-->
		<?php
		
		$locations_select_options = '<option value="">' . esc_html__('Select drop-off location', 'bookyourtravel') . '</option>';
		$location_results = $bookyourtravel_location_helper->list_locations(0, 1, -1, 'title', 'asc');
		if ( count($location_results) > 0 && $location_results['total'] > 0 ) {
			foreach ($location_results['results'] as $location_result) {
				$locations_select_options .= '<option value="' . esc_attr($location_result->ID) . '">' . $location_result->post_title . '</option>';
			}
		}

		?>
		<!--availability-->
		<section id="availability" class="tab-content <?php echo $first_display_tab == 'availability' ? 'initial' : ''; ?>">
			<article>
				<?php do_action( 'bookyourtravel_show_single_car_rental_availability_before' ); ?>
				<h2><?php esc_html_e('Available dates', 'bookyourtravel'); ?></h2>
				<?php BookYourTravel_Theme_Utils::render_field("text-wrap", "", "", $car_rental_obj->get_custom_field('availability_text'), '', false, true); ?>
				<form id="launch-car_rental-booking" action="#" method="POST">
					<div class="booking_form_controls_holder">
						<div class="text-wrap booking_terms">
							<div>
								<p><?php esc_html_e('Select your rental dates using the calendar below to rent this car.', 'bookyourtravel') ?></p>
							</div>
						</div>
						<div class="row calendar-colors">
							<div class="f-item full-width">
								<div class="today"><span></span><?php esc_html_e('Today', 'bookyourtravel') ?></div>
								<div class="selected"><span></span><?php esc_html_e('Selected', 'bookyourtravel') ?></div>
								<div class="available"><span></span><?php esc_html_e('Available', 'bookyourtravel') ?></div>
								<div class="unavailable"><span></span><?php esc_html_e('Unavailable', 'bookyourtravel') ?></div>
							</div>		
						</div>
						<div class="error step1_error text-wrap" style="display:none;"><div><p></p></div></div>						
						<div class="row calendar">
							<div class="f-item full-width">
								<div class="car_booking_form_datepicker"></div>
								<input type="hidden" id="selected_date_from" name="selected_date_from" value="" />
								<input type="hidden" id="selected_date_to" name="selected_date_to" value="" />
							</div>
						</div>
						<div class="row loading" id="datepicker_loading" style="display:none">
							<div class="ball"></div>
							<div class="ball1"></div>
						</div>
						
						<div class="row price_row" style="display:none">
							<div class="f-item full-width booking_form_drop_off_div">
								<label for="booking_form_drop_off"><?php esc_html_e('Drop off location', 'bookyourtravel') ?></label>
								<select class="dynamic_control booking_form_drop_off" id="booking_form_drop_off" name="booking_form_drop_off">
								<?php echo $locations_select_options; ?>
								</select>
							</div>
						</div>
						
						<?php if ($enable_extra_items) { 
							global $bookyourtravel_extra_item_helper;
					
							$extra_items = $bookyourtravel_extra_item_helper->list_extra_items_by_post_type('car_rental', array($car_rental_obj->get_type_id()), $car_rental_obj->get_tag_ids());
	
							if (count($extra_items) > 0) {
						?>												
						<div class="text-wrap price_row extra_items_row" style="display:none">
							<h3><?php esc_html_e('Extra items', 'bookyourtravel') ?></h3>
							<p><?php esc_html_e('Please select the extra items you wish to be included with your car using the controls you see below.', 'bookyourtravel') ?></p>
						
							<table class="extraitems responsive">
								<thead>
									<tr>
										<th><?php esc_html_e('Item', 'bookyourtravel'); ?></th>
										<th><?php esc_html_e('Price', 'bookyourtravel'); ?></th>
										<th><?php esc_html_e('Per day?', 'bookyourtravel'); ?></th>
										<th><?php esc_html_e('Quantity', 'bookyourtravel'); ?></th>
									</tr>
								</thead>
								<tbody>
									<script>
										window.requiredExtraItems = [];
									</script>								
									<?php
										foreach ($extra_items as $extra_item) {
											$car_rental_extra_item_obj = new BookYourTravel_Extra_Item($extra_item);
											$item_teaser = BookYourTravel_Theme_Utils::strip_tags_and_shorten_by_words($car_rental_extra_item_obj->get_content(), 20);
											$max_allowed = $car_rental_extra_item_obj->get_custom_field('_extra_item_max_allowed', false);
											$item_price =$car_rental_extra_item_obj->get_custom_field('_extra_item_price', false);
											$item_price_per_person = intval($car_rental_extra_item_obj->get_custom_field('_extra_item_price_per_person', false));
											$item_price_per_day = intval($car_rental_extra_item_obj->get_custom_field('_extra_item_price_per_day', false));
											$item_is_required = intval($car_rental_extra_item_obj->get_custom_field('_extra_item_is_required', false));											
											
											if ($max_allowed > 0) {
												$starting_index = $item_is_required ? 1 : 0;
												
												if ($item_is_required) {
												?>
												<script>
													window.requiredExtraItems.push(<?php echo $extra_item->ID; ?>);
												</script>							
												<?php
												}												
										?>
										<tr>
											<td>
												<span id="extra_item_title_<?php echo esc_attr($extra_item->ID); ?>"><?php echo esc_html($extra_item->post_title); ?></span>
												<?php if (!empty($item_teaser)) { ?>
												<i>
												<?php
												$allowed_tags = BookYourTravel_Theme_Utils::get_allowed_content_tags_array();
												echo wp_kses($item_teaser, $allowed_tags); 
												?>											
												</i>
												<?php } ?>
											</td>
											<td>
												<em>
													<?php if (!$show_currency_symbol_after) { ?>
													<span class="curr"><?php echo esc_html($default_currency_symbol); ?></span>
													<span class="amount"><?php echo number_format_i18n( $item_price, $price_decimal_places ); ?></span>
													<?php } else { ?>
													<span class="amount"><?php echo number_format_i18n( $item_price, $price_decimal_places ); ?></span>
													<span class="curr"><?php echo esc_html($default_currency_symbol); ?></span>
													<?php } ?>
													<input type="hidden" value="<?php echo esc_attr($item_price); ?>" name="extra_item_price_<?php echo esc_attr($extra_item->ID); ?>" id="extra_item_price_<?php echo esc_attr($extra_item->ID); ?>" />
													<input type="hidden" value="<?php echo esc_attr($item_price_per_person); ?>" name="extra_item_price_per_person_<?php echo esc_attr($extra_item->ID); ?>" id="extra_item_price_per_person_<?php echo esc_attr($extra_item->ID); ?>" />
													<input type="hidden" value="<?php echo esc_attr($item_price_per_day); ?>" name="extra_item_price_per_day_<?php echo esc_attr($extra_item->ID); ?>" id="extra_item_price_per_day_<?php echo esc_attr($extra_item->ID); ?>" />
												</em>							
											</td>
											<td><?php echo $item_price_per_day ? esc_html__('Yes', 'bookyourtravel') : esc_html__('No', 'bookyourtravel'); ?></td>
											<td>
												<select class="extra_item_quantity dynamic_control" name="extra_item_quantity_<?php echo esc_attr($extra_item->ID); ?>" id="extra_item_quantity_<?php echo esc_attr($extra_item->ID); ?>">
													<?php for ($i=$starting_index;$i<=$max_allowed;$i++) {?>
													<option value="<?php echo esc_attr($i); ?>"><?php echo esc_html($i); ?></option>
													<?php } ?>
												</select>
											</td>
										</tr>
										<?php
											}
										}
									?>
								</tbody>
								<tfoot></tfoot>
							</table>
						</div>							
						<?php } 
						}
						?>
						
						<div class="text-wrap dates_row" style="display:none">
							<h3><?php esc_html_e('Summary', 'bookyourtravel'); ?></h3>
							<p><?php esc_html_e('The summary of your car booking is shown below.', 'bookyourtravel'); ?></p>
							
							<table class="summary responsive">
								<tbody>
									<tr>
										<th><?php esc_html_e('From date', 'bookyourtravel') ?></th>
										<td>
											<span class="date_from_text"></span>
											<input type="hidden" name="selected_date_from" id="selected_date_from" value="" />
										</td>
									</tr>
									<tr>
										<th><?php esc_html_e('To date', 'bookyourtravel') ?></th>
										<td>
											<span class="date_to_text"><?php esc_html_e('Select your to date using the calendar above.', 'bookyourtravel') ?></span>
											<input type="hidden" name="selected_date_to" id="selected_date_to" value="" />
										</td>
									</tr>
									<?php if ($enable_extra_items) { ?>
									<tr>
										<th>
											<?php esc_html_e('Car rental booking total', 'bookyourtravel') ?>
										</th>
										<td>
											<span class="reservation_total"></span>
										</td>
									</tr>
									<tr class="extra_items_breakdown_row">
										<th>
											<?php esc_html_e('Extra items total', 'bookyourtravel') ?>
										</th>
										<td>
											<span class="extra_items_total"></span>
										</td>
									</tr>
									<?php } ?>						
								</tbody>
								<tfoot>
									<tr>
										<th><?php esc_html_e('Total price', 'bookyourtravel') ?></th>
										<td class="total_price"></td>
									</tr>
								</tfoot>
							</table>
							<a href="#" class="toggle_breakdown show_breakdown"><?php esc_html_e('Show price breakdown', 'bookyourtravel') ?></a>
							<div class="row price_breakdown_row hidden" style="display:none">
								<div class="f-item full-width">
									<label><?php esc_html_e('Car rental price breakdown', 'bookyourtravel') ?></label>
									<table class="car_rental_price_breakdown tablesorter responsive">
										<thead></thead>
										<tbody></tbody>
										<tfoot></tfoot>
									</table>
								</div>
							</div>
							<div class="row price_breakdown_row extra_items_breakdown_row" style="display:none">
								<div class="f-item full-width">
									<label><?php esc_html_e('Extra items price breakdown', 'bookyourtravel') ?></label>
									<table class="extra_items_price_breakdown tablesorter responsive">
										<thead></thead>
										<tbody></tbody>
										<tfoot></tfoot>
									</table>
								</div>
							</div>
						</div>
						
					</div>
					<div class="booking-commands">
					<?php BookYourTravel_Theme_Utils::render_link_button("#", "gradient-button book-car_rental-reset", "book-car_rental-rest", esc_html__('Reset', 'bookyourtravel')); ?>
					<?php BookYourTravel_Theme_Utils::render_link_button("#", "clearfix gradient-button book-car_rental-proceed", "book-car_rental", esc_html__('Proceed', 'bookyourtravel')); ?>
					</div>
				</form>
				<?php BookYourTravel_Theme_Utils::render_tab_extra_fields('car_rental_extra_fields', $car_rental_extra_fields, 'availability', $car_rental_obj); ?>
				<?php do_action( 'bookyourtravel_show_single_car_rental_availability_after' ); ?>
			</article>
		</section>
		<!--//availability-->
		
		<?php
		foreach ($tab_array as $tab) {
			if (count(BookYourTravel_Theme_Utils::custom_array_search($default_car_rental_tabs, 'id', $tab['id'])) == 0) {
			
				$all_empty_fields = BookYourTravel_Theme_Utils::are_tab_fields_empty('car_rental_extra_fields', $car_rental_extra_fields, $tab['id'], $car_rental_obj);
				
				if (!$all_empty_fields) {

			?>
				<section id="<?php echo esc_attr($tab['id']); ?>" class="tab-content <?php echo ($first_display_tab == $tab['id'] ? 'initial' : ''); ?>">
					<article>
						<?php do_action( 'bookyourtravel_show_single_car_rental_' . $tab['id'] . '_before' ); ?>
						<?php BookYourTravel_Theme_Utils::render_tab_extra_fields('car_rental_extra_fields', $car_rental_extra_fields, $tab['id'], $car_rental_obj); ?>
						<?php do_action( 'bookyourtravel_show_single_car_rental_' . $tab['id'] . '_after' ); ?>
					</article>
				</section>
			<?php
				}
			}
		}	
		?>
		<?php do_action( 'bookyourtravel_show_single_car_rental_tab_content_after' ); ?>
	</section>
	<!--//car rental content-->	
<?php
	get_sidebar('right-car_rental'); 
?>
</div>
<?php
} // end if
get_footer(); 