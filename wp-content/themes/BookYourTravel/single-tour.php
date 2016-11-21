<?php 

get_header();  
BookYourTravel_Theme_Utils::breadcrumbs();
get_sidebar('under-header');	

global $post, $tour_price, $bookyourtravel_theme_globals, $tour_date_from, $current_user, $tour_obj, $entity_obj, $default_tour_tabs, $score_out_of_10, $bookyourtravel_tour_helper, $bookyourtravel_theme_of_custom;

$enable_extra_items = $bookyourtravel_theme_globals->enable_extra_items();
$enable_reviews = $bookyourtravel_theme_globals->enable_reviews();
$enable_tours = $bookyourtravel_theme_globals->enable_tours();
$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
$tour_extra_fields = $bookyourtravel_theme_globals->get_tour_extra_fields();
$tab_array = $bookyourtravel_theme_globals->get_tour_tabs();

$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
$default_currency_symbol = $bookyourtravel_theme_globals->get_default_currency_symbol();
$show_currency_symbol_after = $bookyourtravel_theme_globals->show_currency_symbol_after();
$light_slider_pause_between_slides = $bookyourtravel_theme_globals->get_light_slider_pause_between_slides();

if ( have_posts() ) {

	the_post();
	
	$tour_obj = new BookYourTravel_Tour($post);
	$tour_id = $tour_obj->get_id();
	$entity_obj = $tour_obj;
	$tour_map_code = $tour_obj->get_custom_field( 'map_code' );
	$tour_locations = $tour_obj->get_locations();
	$tour_is_reservation_only = $tour_obj->get_is_reservation_only();
		
	$tour_date_from = date('Y-m-d', strtotime("+0 day", time()));
	$tour_date_from_year = date('Y', strtotime("+0 day", time()));
	$tour_date_from_month = date('n', strtotime("+0 day", time()));
	
	$tour_price = $bookyourtravel_tour_helper->get_tour_min_price($tour_id);
?>
<div class="row">
	<script>
		window.postType = 'tour';
		window.pauseBetweenSlides = <?php echo $light_slider_pause_between_slides * 1000; ?>;
	</script>
<?php	
	if ($enable_reviews) {
		get_template_part('includes/parts/review', 'form'); 
	}
	get_template_part('includes/parts/inquiry', 'form');
?>
<!--tour three-fourth content-->
<section class="three-fourth">
	<?php
	get_template_part('includes/parts/tour', 'booking-form');
	get_template_part('includes/parts/tour', 'confirmation-form');
	?>	
	<script>
		window.bookingFormStartDateError = <?php echo json_encode(esc_html__('Please select a valid start date!', 'bookyourtravel')); ?>;
		window.startDate = null;
		window.formSingleError = <?php echo json_encode(esc_html__('You failed to provide 1 field. It has been highlighted below.', 'bookyourtravel')); ?>;
		window.formMultipleError = <?php echo json_encode(esc_html__('You failed to provide {0} fields. They have been highlighted below.', 'bookyourtravel'));  ?>;
		window.tourId = <?php echo $tour_obj->get_id(); ?>;
		window.tourIsPricePerGroup = <?php echo $tour_obj->get_is_price_per_group(); ?>;
		window.tourDateFrom = <?php echo json_encode($tour_date_from); ?>;
		window.tourTitle = <?php echo json_encode($tour_obj->get_title()); ?>;
		window.currentMonth = <?php echo json_encode(date_i18n('n')); ?>;
		window.currentYear = <?php echo json_encode( date_i18n('Y')); ?>;
		window.currentDay = <?php echo json_encode( date_i18n('j')); ?>;
		window.tourIsReservationOnly = <?php echo (int)$tour_is_reservation_only; ?>;
		window.enableExtraItems = <?php echo json_encode($enable_extra_items); ?>;
		window.showPriceBreakdownLabel = <?php echo json_encode(esc_html__('Show price breakdown', 'bookyourtravel')); ?>;
		window.hidePriceBreakdownLabel = <?php echo json_encode(esc_html__('Hide price breakdown', 'bookyourtravel')); ?>;
		window.dateLabel = <?php echo json_encode(esc_html__('Date', 'bookyourtravel')); ?>;
		window.itemLabel = <?php echo json_encode(esc_html__('Item', 'bookyourtravel')); ?>;
		window.priceLabel = <?php echo json_encode(esc_html__('Price', 'bookyourtravel')); ?>;
		window.pricedPerDayPerPersonLabel = <?php echo json_encode(esc_html__('priced per day, per person', 'bookyourtravel')); ?>;
		window.pricedPerDayLabel = <?php echo json_encode(esc_html__('priced per day', 'bookyourtravel')); ?>;
		window.pricedPerPersonLabel = <?php echo json_encode(esc_html__('priced per person', 'bookyourtravel')); ?>;
		window.pricePerAdultLabel = <?php echo json_encode(esc_html__('Price per adult', 'bookyourtravel')); ?>;
		window.pricePerPersonLabel = <?php echo json_encode(esc_html__('Price per person', 'bookyourtravel')); ?>;
		window.adultCountLabel = <?php echo json_encode(esc_html__('Adults', 'bookyourtravel')); ?>;
		window.childCountLabel = <?php echo json_encode(esc_html__('Children', 'bookyourtravel')); ?>;
		window.pricePerChildLabel = <?php echo json_encode(esc_html__('Price per child', 'bookyourtravel')); ?>;
		window.pricePerDayLabel = <?php echo json_encode(esc_html__('Price per day', 'bookyourtravel')); ?>;
		window.extraItemsPriceTotalLabel = <?php echo json_encode(esc_html__('Extra items total price', 'bookyourtravel')); ?>;
		window.priceTotalLabel = <?php echo json_encode(esc_html__('Total price', 'bookyourtravel')); ?>;
	</script>
	<?php $tour_obj->render_image_gallery(); ?>
	<!--inner navigation-->
	<nav class="inner-nav">
		<ul>
			<?php
			do_action( 'bookyourtravel_show_single_tour_tab_items_before' );
			$first_display_tab = '';			
			$i = 0;
			if (is_array($tab_array) && count($tab_array) > 0) {
				foreach ($tab_array as $tab) {
					if (!isset($tab['hide']) || $tab['hide'] != '1') {
					
						$tab_label = '';
						if (isset($tab['label'])) {
							$tab_label = $tab['label'];
							$tab_label = $bookyourtravel_theme_of_custom->get_translated_dynamic_string($bookyourtravel_theme_of_custom->get_option_id_context('tour_tabs') . ' ' . $tab['label'], $tab_label);
						}
					
						if($i==0)
							$first_display_tab = $tab['id'];
						if ($tab['id'] == 'reviews' && $enable_reviews) {
							BookYourTravel_Theme_Utils::render_tab("tour", $tab['id'], '',  '<a href="#' . $tab['id'] . '" title="' . $tab_label . '">' . $tab_label . '</a>');
						} elseif ($tab['id'] == 'map' && !empty($tour_map_code)) {
							BookYourTravel_Theme_Utils::render_tab("tour", $tab['id'], '',  '<a href="#' . $tab['id'] . '" title="' . $tab_label . '">' . $tab_label . '</a>');
						} elseif ($tab['id'] == 'locations') {
							if ($tour_locations && count($tour_locations) > 0)
								BookYourTravel_Theme_Utils::render_tab("tour", $tab['id'], '',  '<a href="#' . $tab['id'] . '" title="' . $tab_label . '">' . $tab_label . '</a>');
						} elseif ($tab['id'] == 'description' || $tab['id'] == 'availability') {
							BookYourTravel_Theme_Utils::render_tab("tour", $tab['id'], '',  '<a href="#' . $tab['id'] . '" title="' . $tab_label . '">' . $tab_label . '</a>');
						} else {
							$all_empty_fields = BookYourTravel_Theme_Utils::are_tab_fields_empty('tour_extra_fields', $tour_extra_fields, $tab['id'], $tour_obj);
							
							if (!$all_empty_fields) {
								BookYourTravel_Theme_Utils::render_tab("tour", $tab['id'], '',  '<a href="#' . $tab['id'] . '" title="' . $tab_label . '">' . $tab_label . '</a>');
							}
						}
						
						$i++;
					}
				}
			} 				
			do_action( 'bookyourtravel_show_single_tour_tab_items_after' ); 
			?>
		</ul>
	</nav>
	<!--//inner navigation-->
	<?php do_action( 'bookyourtravel_show_single_tour_tab_content_before' ); ?>
	<!--description-->
	<section id="description" class="tab-content <?php echo $first_display_tab == 'description' ? 'initial' : ''; ?>">
		<article>
			<?php do_action( 'bookyourtravel_show_single_tour_description_before' ); ?>
			<?php BookYourTravel_Theme_Utils::render_field("text-wrap", "", "", $tour_obj->get_description(), esc_html__('General', 'bookyourtravel')); ?>
			<?php BookYourTravel_Theme_Utils::render_tab_extra_fields('tour_extra_fields', $tour_extra_fields, 'description', $tour_obj); ?>
			<?php do_action( 'bookyourtravel_show_single_tour_description_after' ); ?>
		</article>
	</section>
	<!--//description-->
	<!--availability-->
	<section id="availability" class="tab-content <?php echo $first_display_tab == 'availability' ? 'initial' : ''; ?>">
		<article>
			<?php do_action( 'bookyourtravel_show_single_tour_availability_before' ); ?>
			<h2><?php esc_html_e('Available departures', 'bookyourtravel'); ?></h2>
			<?php BookYourTravel_Theme_Utils::render_field("text-wrap", "", "", $tour_obj->get_custom_field('availability_text'), '', false, true); ?>
			<form id="launch-tour-booking" action="#" method="POST">
				<?php 
				$type_day_of_week_indexes = $tour_obj->get_type_day_of_week_indexes();
				$year  = date('Y', strtotime($tour_date_from));
				$month  = date('m', strtotime($tour_date_from));
				$schedule_entries = $bookyourtravel_tour_helper->list_available_tour_schedule_entries($tour_obj->get_id(), $tour_date_from, $year, $month, $tour_obj->get_type_is_repeated(), $type_day_of_week_indexes);

				if (count($schedule_entries) > 0) { ?>
					<div class="booking_form_controls_holder">
						<div class="text-wrap booking_terms">
							<div>
								<p><?php esc_html_e('Select your tour date using the calendar below to book this tour.', 'bookyourtravel') ?></p>
								<p>
								<?php 					
								if ($tour_obj->get_type_is_repeated() == 1) {
									echo esc_html__('This is a daily tour.', 'bookyourtravel'); 
								} else if ($tour_obj->get_type_is_repeated() == 2) {
									echo esc_html__('This tour is repeated every weekday (working day).', 'bookyourtravel'); 
								} else if ($tour_obj->get_type_is_repeated() == 3) {
									echo sprintf(esc_html__('This tour is repeated every week on a %s.', 'bookyourtravel'), $tour_obj->get_type_day_of_week_day()); 
								} else if ($tour_obj->get_type_is_repeated() == 4) {
									echo esc_html__('This tour is repeated every week on multiple days.', 'bookyourtravel'); 
								}
								?>
								</p>
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
						<div class="row calendar">
							<div class="f-item full-width">
								<div class="tour_schedule_datepicker"></div>
							</div>
						</div>
						<div class="row loading" id="datepicker_loading" style="display:none">
							<div class="ball"></div>
							<div class="ball1"></div>
						</div>
						<div class="text-wrap price_row" style="display:none">
							<h3><?php esc_html_e('Who is checking in?', 'bookyourtravel') ?></h3>
							<p><?php esc_html_e('Please select number of adults and children checking into the tour using the controls you see below.', 'bookyourtravel') ?></p>

							<div class="row">
								<div class="f-item one-half">
									<label for="booking_form_adults"><?php esc_html_e('Adults', 'bookyourtravel') ?></label>
									<select class="dynamic_control" id="booking_form_adults" name="booking_form_adults"></select>
								</div>
								<div class="f-item one-half booking_form_children">
									<label for="booking_form_children"><?php esc_html_e('Children', 'bookyourtravel') ?></label>
									<select class="dynamic_control" id="booking_form_children" name="booking_form_children"></select>
								</div>
							</div>
						</div>
						<?php if ($enable_extra_items) { 
						global $bookyourtravel_extra_item_helper;
				
						$extra_items = $bookyourtravel_extra_item_helper->list_extra_items_by_post_type('tour', array($tour_obj->get_type_id()), $tour_obj->get_tag_ids());
						if (count($extra_items) > 0) {
						?>
												
						<div class="text-wrap price_row extra_items_row" style="display:none">
							<h3><?php esc_html_e('Extra items', 'bookyourtravel') ?></h3>
							<p><?php esc_html_e('Please select the extra items you wish to be included on your tour using the controls you see below.', 'bookyourtravel') ?></p>
						
							<table class="extraitems responsive">
								<thead>
									<tr>
										<th><?php esc_html_e('Item', 'bookyourtravel'); ?></th>
										<th><?php esc_html_e('Price', 'bookyourtravel'); ?></th>
										<th><?php esc_html_e('Per person?', 'bookyourtravel'); ?></th>
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
											$tour_extra_item_obj = new BookYourTravel_Extra_Item($extra_item);
											$item_teaser = BookYourTravel_Theme_Utils::strip_tags_and_shorten_by_words($tour_extra_item_obj->get_content(), 20);
											$max_allowed = $tour_extra_item_obj->get_custom_field('_extra_item_max_allowed', false);
											$item_price =$tour_extra_item_obj->get_custom_field('_extra_item_price', false);
											$item_price_per_person = intval($tour_extra_item_obj->get_custom_field('_extra_item_price_per_person', false));
											$item_price_per_day = intval($tour_extra_item_obj->get_custom_field('_extra_item_price_per_day', false));
											$item_is_required = intval($tour_extra_item_obj->get_custom_field('_extra_item_is_required', false));											
							
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
											<td><?php echo $item_price_per_person ? esc_html__('Yes', 'bookyourtravel') : esc_html__('No', 'bookyourtravel'); ?></td>
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
							<p><?php esc_html_e('The summary of your tour booking is shown below.', 'bookyourtravel'); ?></p>
							
							<table class="summary responsive">
								<tbody>
									<tr>
										<th><?php esc_html_e('Start date', 'bookyourtravel') ?></th>
										<td>
											<span id="start_date_span"></span>
											<input type="hidden" name="start_date" id="start_date" value="" />
										</td>
									</tr>
									<tr>
										<th><?php esc_html_e('Tour duration (days)', 'bookyourtravel') ?></th>
										<td>
											<span id="duration_days_span"></span>
											<input type="hidden" name="duration_days" id="duration_days" value="" />
										</td>
									</tr>
									<tr class=" people_count_div" style="display:none">
										<th>
											<?php esc_html_e('People', 'bookyourtravel') ?>
										</th>
										<td>
											<span class="people_text">1</span>
										</td>
									</tr>
									<tr class=" adult_count_div">
										<th>
											<?php esc_html_e('Adults', 'bookyourtravel') ?>
										</th>
										<td>
											<span class="adults_text">1</span>
										</td>
									</tr>
									<tr class=" children_count_div">
										<th>
											<?php esc_html_e('Children', 'bookyourtravel') ?>
										</th>
										<td>
											<span class="children_text">0</span>
										</td>
									</tr>
									<?php if ($enable_extra_items) { ?>
									<tr>
										<th>
											<?php esc_html_e('Tour booking total', 'bookyourtravel') ?>
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
									<label><?php esc_html_e('Tour price breakdown', 'bookyourtravel') ?></label>
									<table class="tour_price_breakdown tablesorter responsive">
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
				<?php 
					echo '<div class="booking-commands">';
					BookYourTravel_Theme_Utils::render_link_button("#", "gradient-button book-tour-reset", "book-tour-rest", esc_html__('Reset', 'bookyourtravel'));
					BookYourTravel_Theme_Utils::render_link_button("#", "clearfix gradient-button book-tour-proceed", "book-tour", esc_html__('Proceed', 'bookyourtravel'));
					echo '</div>';
				} else { 
					echo '<div class="text-wrap">' . esc_html__('Unfortunately, no places are available on this tour at the moment', 'bookyourtravel') . '</div>';		
				}
				?>
			</form>
			<?php BookYourTravel_Theme_Utils::render_tab_extra_fields('tour_extra_fields', $tour_extra_fields, 'availability', $tour_obj); ?>
			<?php do_action( 'bookyourtravel_show_single_tour_availability_after' ); ?>
		</article>
	</section>
	<!--//availability-->
		
	<?php if (!empty($tour_map_code)) { ?>
	<!--map-->
	<section id="map" class="tab-content <?php echo $first_display_tab == 'map' ? 'initial' : ''; ?>">
		<article>
			<?php do_action( 'bookyourtravel_show_single_tour_map_before' ); ?>
			<!--map-->
			<div class="gmap"><?php echo $tour_map_code; ?></div>
			<!--//map-->
			<?php BookYourTravel_Theme_Utils::render_tab_extra_fields('tour_extra_fields', $tour_extra_fields, 'map', $tour_obj); ?>
			<?php do_action( 'bookyourtravel_show_single_tour_map_after' ); ?>
		</article>
	</section>
	<!--//map-->
	<?php } // endif (!empty($tour_map_code)) ?>
	
	<?php if ($tour_locations && count($tour_locations) > 0) { ?>		
	<!--locations-->
	<section id="locations" class="tab-content <?php echo $first_display_tab == 'locations' ? 'initial' : ''; ?>">
		<article>
			<?php do_action( 'bookyourtravel_show_single_tour_locations_before' ); ?>				
			<?php foreach ($tour_locations as $location_id) {
				$location_obj = new BookYourTravel_Location((int)$location_id);
				$location_title = $location_obj->get_title();
				$location_excerpt = $location_obj->get_excerpt();
				if (!empty($location_title)) {
					BookYourTravel_Theme_Utils::render_field("", "", "", BookYourTravel_Theme_Utils::render_image('', '', $location_obj->get_main_image(), $location_title, $location_title, false) . $location_excerpt, $location_title);
					BookYourTravel_Theme_Utils::render_link_button(get_permalink($location_obj->get_id()), "gradient-button right", "", esc_html__('Read more', 'bookyourtravel'));
				}
			} ?>								
			<?php BookYourTravel_Theme_Utils::render_tab_extra_fields('tour_extra_fields', $tour_extra_fields, 'locations', $tour_obj); ?>
			<?php do_action( 'bookyourtravel_show_single_tour_locations_after' ); ?>
		</article>
	</section>
	<!--//locations-->	
	<?php } // endif (!empty($tour_map_code)) ?>
	<?php if ($enable_reviews) { ?>
	<!--reviews-->
	<section id="reviews" class="tab-content <?php echo $first_display_tab == 'review' ? 'initial' : ''; ?>">
		<?php 
		do_action( 'bookyourtravel_show_single_tour_reviews_before' );
		get_template_part('includes/parts/review', 'item'); 
		BookYourTravel_Theme_Utils::render_tab_extra_fields('tour_extra_fields', $tour_extra_fields, 'reviews', $tour_obj); 
		do_action( 'bookyourtravel_show_single_tour_reviews_after' ); 
		?>
	</section>
	<!--//reviews-->
	<?php } // if ($enable_reviews) ?>
	<?php
	foreach ($tab_array as $tab) {
		if (count(BookYourTravel_Theme_Utils::custom_array_search($default_tour_tabs, 'id', $tab['id'])) == 0) {
			$all_empty_fields = BookYourTravel_Theme_Utils::are_tab_fields_empty('tour_extra_fields', $tour_extra_fields, $tab['id'], $tour_obj);
			
			if (!$all_empty_fields) {
		?>
			<section id="<?php echo esc_attr($tab['id']); ?>" class="tab-content <?php echo ($first_display_tab == $tab['id'] ? 'initial' : ''); ?>">
				<article>
					<?php do_action( 'bookyourtravel_show_single_tour_' . $tab['id'] . '_before' ); ?>
					<?php BookYourTravel_Theme_Utils::render_tab_extra_fields('tour_extra_fields', $tour_extra_fields, $tab['id'], $tour_obj); ?>
					<?php do_action( 'bookyourtravel_show_single_tour_' . $tab['id'] . '_after' ); ?>
				</article>
			</section>
		<?php
			}
		}
	}	
	do_action( 'bookyourtravel_show_single_tour_tab_content_after' ); ?>
</section>
<!--//tour content-->	
<?php
	get_sidebar('right-tour'); 
?>
</div>
<?php
} // end if
get_footer(); 