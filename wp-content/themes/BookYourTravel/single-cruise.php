<?php 

get_header();  
BookYourTravel_Theme_Utils::breadcrumbs();
get_sidebar('under-header');

global $post, $cruise_price, $bookyourtravel_theme_globals, $current_user, $cruise_obj, $entity_obj, $default_cruise_tabs, $score_out_of_10, $bookyourtravel_cruise_helper, $bookyourtravel_theme_of_custom;

$enable_extra_items = $bookyourtravel_theme_globals->enable_extra_items();
$enable_reviews = $bookyourtravel_theme_globals->enable_reviews();
$enable_cruises = $bookyourtravel_theme_globals->enable_cruises();
$cruise_extra_fields = $bookyourtravel_theme_globals->get_cruise_extra_fields();
$tab_array = $bookyourtravel_theme_globals->get_cruise_tabs();
$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
$default_currency_symbol = $bookyourtravel_theme_globals->get_default_currency_symbol();
$show_currency_symbol_after = $bookyourtravel_theme_globals->show_currency_symbol_after();
$light_slider_pause_between_slides = $bookyourtravel_theme_globals->get_light_slider_pause_between_slides();

if ( have_posts() ) {

	the_post();
	$cruise_obj = new BookYourTravel_Cruise($post);
	$cruise_id = $cruise_obj->get_id();
	$cruise_count_children_stay_free = $cruise_obj->get_count_children_stay_free();
	$entity_obj = $cruise_obj;
	$cruise_date_from = date('Y-m-d', strtotime("+0 day", time()));
	$cruise_date_from_year = date('Y', strtotime("+0 day", time()));
	$cruise_date_from_month = date('n', strtotime("+0 day", time()));
	$cruise_is_reservation_only = $cruise_obj->get_is_reservation_only();
	$cruise_locations = $cruise_obj->get_locations();
	
	$cruise_price = $bookyourtravel_cruise_helper->get_cruise_min_price($cruise_id);
?>
	<div class="row">
		<script>
			window.postType = 'cruise';
			window.pauseBetweenSlides = <?php echo $light_slider_pause_between_slides * 1000; ?>;
		</script>
	<?php	
		if ($enable_reviews) {
			get_template_part('includes/parts/review', 'form'); 
		}
		get_template_part('includes/parts/inquiry', 'form');
		?>
		<!--cruise three-fourth content-->
		<section class="three-fourth">
			<?php
			get_template_part('includes/parts/cruise', 'booking-form');
			get_template_part('includes/parts/cruise', 'confirmation-form');
			?>	
			<script>
				window.startDate = null;
				window.formSingleError = <?php echo json_encode(esc_html__('You failed to provide 1 field. It has been highlighted below.', 'bookyourtravel')); ?>;
				window.formMultipleError = <?php echo json_encode(esc_html__('You failed to provide {0} fields. They have been highlighted below.', 'bookyourtravel'));  ?>;
				window.cruiseCountChildrenStayFree = <?php echo (int)$cruise_count_children_stay_free; ?>;
				window.cruiseId = <?php echo $cruise_obj->get_id(); ?>;
				window.cruiseIsPricePerPerson = <?php echo $cruise_obj->get_is_price_per_person(); ?>;
				window.cruiseDateFrom = <?php echo json_encode($cruise_date_from); ?>;
				window.cruiseTitle = <?php echo json_encode($cruise_obj->get_title()); ?>;
				window.cruiseIsReservationOnly = <?php echo (int)$cruise_is_reservation_only; ?>;
				window.currentMonth = <?php echo json_encode(date_i18n('n')); ?>;
				window.currentYear = <?php echo json_encode( date_i18n('Y')); ?>;
				window.currentDay = <?php echo json_encode( date_i18n('j')); ?>;
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
			<?php $cruise_obj->render_image_gallery(); ?>
			<!--inner navigation-->
			<nav class="inner-nav">
				<ul>
					<?php
					do_action( 'bookyourtravel_show_single_cruise_tab_items_before' );
					$first_display_tab = '';			
					$i = 0;
					if (is_array($tab_array) && count($tab_array) > 0) {
						foreach ($tab_array as $tab) {
						
							if (!isset($tab['hide']) || $tab['hide'] != '1') {
						
								$tab_label = '';
								if (isset($tab['label'])) {
									$tab_label = $tab['label'];
									$tab_label = $bookyourtravel_theme_of_custom->get_translated_dynamic_string($bookyourtravel_theme_of_custom->get_option_id_context('cruise_tabs') . ' ' . $tab['label'], $tab_label);
								}
							
								if($i==0)
									$first_display_tab = $tab['id'];
								if ($tab['id'] == 'reviews' && $enable_reviews) {
									BookYourTravel_Theme_Utils::render_tab("cruise", $tab['id'], '',  '<a href="#' . $tab['id'] . '" title="' . $tab_label . '">' . $tab_label . '</a>');
								} elseif ($tab['id'] == 'locations') {
									if ($cruise_locations && count($cruise_locations) > 0)
										BookYourTravel_Theme_Utils::render_tab("cruise", $tab['id'], '',  '<a href="#' . $tab['id'] . '" title="' . $tab_label . '">' . $tab_label . '</a>');
								} elseif ($tab['id'] == 'description' || $tab['id'] == 'availability') {
									BookYourTravel_Theme_Utils::render_tab("tour", $tab['id'], '',  '<a href="#' . $tab['id'] . '" title="' . $tab_label . '">' . $tab_label . '</a>');
								} else {
									$all_empty_fields = BookYourTravel_Theme_Utils::are_tab_fields_empty('cruise_extra_fields', $cruise_extra_fields, $tab['id'], $cruise_obj);
									
									if (!$all_empty_fields) {
										BookYourTravel_Theme_Utils::render_tab("cruise", $tab['id'], '',  '<a href="#' . $tab['id'] . '" title="' . $tab_label . '">' . $tab_label . '</a>');
									}
								}
								$i++;
							}
						}
					} 				
					do_action( 'bookyourtravel_show_single_cruise_tab_items_after' ); 
					?>
				</ul>
			</nav>
			<!--//inner navigation-->
			<?php do_action( 'bookyourtravel_show_single_cruise_tab_content_before' ); ?>
			<!--description-->
			<section id="description" class="tab-content <?php echo $first_display_tab == 'description' ? 'initial' : ''; ?>">
				<article>
					<?php do_action( 'bookyourtravel_show_single_cruise_description_before' ); ?>
					<?php BookYourTravel_Theme_Utils::render_field("text-wrap", "", "", $cruise_obj->get_description(), esc_html__('General', 'bookyourtravel')); ?>
					<?php BookYourTravel_Theme_Utils::render_tab_extra_fields('cruise_extra_fields', $cruise_extra_fields, 'description', $cruise_obj); ?>
					<?php do_action( 'bookyourtravel_show_single_cruise_description_after' ); ?>
				</article>
			</section>
			<!--//description-->
			<!--availability-->
			<script>
				window.moreInfoText = <?php echo json_encode(esc_html__('+ more info', 'bookyourtravel')); ?>;
				window.lessInfoText = <?php echo json_encode(esc_html__('+ less info', 'bookyourtravel')); ?>;
			</script>
			<section id="availability" class="tab-content <?php echo $first_display_tab == 'availability' ? 'initial' : ''; ?>">
				<article>
					<?php do_action( 'bookyourtravel_show_single_cruise_availability_before' ); ?>
					<h2><?php esc_html_e('Available departures', 'bookyourtravel'); ?></h2>
					<?php BookYourTravel_Theme_Utils::render_field("text-wrap", "", "", $cruise_obj->get_custom_field('availability_text'), '', false, true); ?>
					<form id="launch-cruise-booking" action="#" method="POST">
						<?php 
						$cabin_type_ids = $cruise_obj->get_cabin_types();
						if ($cabin_type_ids && count($cabin_type_ids) > 0) { ?>
						<ul class="cabin-types room-types">
							<?php 
							// Loop through the items returned				
							for ( $z = 0; $z < count($cabin_type_ids); $z++ ) {
								$cabin_type_id = $cabin_type_ids[$z];
								$cabin_type_obj = new BookYourTravel_Cabin_Type(intval($cabin_type_id));
								$cabin_type_min_price = $bookyourtravel_cruise_helper->get_cruise_min_price($cruise_id, $cabin_type_id, $cruise_date_from);
							?>
							<!--cabin_type-->
							<li id="cabin_type_<?php echo $cabin_type_id; ?>">
								<?php if ($cabin_type_obj->get_main_image('medium')) { ?>
								<figure class="left"><img src="<?php echo esc_url($cabin_type_obj->get_main_image('medium')) ?>" alt="<?php echo esc_attr($cabin_type_obj->get_title()); ?>" /><a href="<?php echo esc_url($cabin_type_obj->get_main_image()); ?>" class="image-overlay" rel="prettyPhoto[gallery1]"></a></figure>
								<?php } ?>
								<div class="meta cabin_type room_type">
									<h3><?php echo $cabin_type_obj->get_title(); ?></h3>
									<?php BookYourTravel_Theme_Utils::render_field('', '', '', $cabin_type_obj->get_custom_field('meta'), '', true, true); ?>
									<?php BookYourTravel_Theme_Utils::render_link_button("#", "more-info", "", esc_html__('+ more info', 'bookyourtravel')); ?>
								</div>
								<div class="cabin-information room-information">
									<div>
										<span class="first"><?php esc_html_e('Max:', 'bookyourtravel'); ?></span>
										<span class="second">
											<?php for ( $j = 0; $j < $cabin_type_obj->get_max_adult_count(); $j++ ) { ?>
											<i class="material-icons">&#xE7FD;</i>
											<?php } ?>
										</span>
									</div>
									<?php if ($cabin_type_min_price > 0) { ?>
									<div>
										<span class="first"><?php esc_html_e('Price from:', 'bookyourtravel'); ?></span>
										<div class="second price">
											<em>
												<?php if (!$show_currency_symbol_after) { ?>
												<span class="curr"><?php echo esc_html($default_currency_symbol); ?></span>
												<span class="amount"><?php echo number_format_i18n( $cabin_type_min_price, $price_decimal_places ); ?></span>
												<?php } else { ?>
												<span class="amount"><?php echo number_format_i18n( $cabin_type_min_price, $price_decimal_places ); ?></span>
												<span class="curr"><?php echo esc_html($default_currency_symbol); ?></span>
												<?php } ?>
											</em>
											<input type="hidden" class="max_count" value="<?php echo esc_attr($cabin_type_obj->get_max_adult_count()); ?>" />
											<input type="hidden" class="max_child_count" value="<?php echo esc_attr($cabin_type_obj->get_max_child_count()); ?>" />
										</div>
									</div>
									<?php BookYourTravel_Theme_Utils::render_link_button("#", "gradient-button book-cruise-select-dates", "book-cruise-$cabin_type_id", esc_html__('Select dates', 'bookyourtravel')); ?>
									<?php } ?>
								</div>
								<div class="more-information">
									<?php BookYourTravel_Theme_Utils::render_field('', '', esc_html__('Cabin facilities:', 'bookyourtravel'), $cabin_type_obj->get_facilities_string(), '', true, true); ?>
									<?php echo $cabin_type_obj->get_description(); ?>
									<?php BookYourTravel_Theme_Utils::render_field('', '', esc_html__('Bed size:', 'bookyourtravel'), $cabin_type_obj->get_custom_field('bed_size'), '', true, true); ?>
									<?php BookYourTravel_Theme_Utils::render_field('', '', esc_html__('Cabin size:', 'bookyourtravel'), $cabin_type_obj->get_custom_field('cabin_size'), '', true, true); ?>
								</div>
								<div class="booking_form_controls" style="display:none"></div>
							</li>
							<!--//cabin-->
							<?php 
							} 
							// Reset Second Loop Post Data
							wp_reset_postdata(); 
							// end while ?>
						</ul>	
						<?php 
						} else { 
							BookYourTravel_Theme_Utils::render_field('text-wrap', '', '', esc_html__('We are sorry, there are no cabins available at this cruise at the moment', 'bookyourtravel'), '', true, true);
						} 

						?>
					</form>
					<?php BookYourTravel_Theme_Utils::render_tab_extra_fields('cruise_extra_fields', $cruise_extra_fields, 'availability', $cruise_obj); ?>
					<?php do_action( 'bookyourtravel_show_single_cruise_availability_after' ); ?>

				</article>
			</section>
			<!--//availability-->
			<?php if ($cruise_locations && count($cruise_locations) > 0) { ?>		
			<!--locations-->
			<section id="locations" class="tab-content <?php echo $first_display_tab == 'locations' ? 'initial' : ''; ?>">
				<article>
					<?php do_action( 'bookyourtravel_show_single_cruise_locations_before' ); ?>				
					<?php foreach ($cruise_locations as $location_id) {
						$location_obj = new BookYourTravel_Location((int)$location_id);
						$location_title = $location_obj->get_title();
						$location_excerpt = $location_obj->get_excerpt();
						if (!empty($location_title)) {
							BookYourTravel_Theme_Utils::render_field("", "", "", BookYourTravel_Theme_Utils::render_image('', '', $location_obj->get_main_image(), $location_title, $location_title, false) . $location_excerpt, $location_title);
							BookYourTravel_Theme_Utils::render_link_button(get_permalink($location_obj->get_id()), "gradient-button right", "", esc_html__('Read more', 'bookyourtravel'));
						}
					}?>								
					<?php BookYourTravel_Theme_Utils::render_tab_extra_fields('cruise_extra_fields', $cruise_extra_fields, 'locations', $cruise_obj); ?>
					<?php do_action( 'bookyourtravel_show_single_cruise_locations_after' ); ?>
				</article>
			</section>
			<!--//locations-->
			<?php } ?>						
			<!--facilities-->
			<section id="facilities" class="tab-content <?php echo $first_display_tab == 'facilities' ? 'initial' : ''; ?>">
				<article>
					<?php do_action( 'bookyourtravel_show_single_cruise_facilites_before' ); ?>
					<?php 
					$facilities = $cruise_obj->get_facilities();
					if ($facilities && count($facilities) > 0) { ?>
					<h1><?php esc_html_e('Facilities', 'bookyourtravel'); ?></h1>
					<div class="text-wrap">	
						<ul class="three-col">
						<?php
						for( $i = 0; $i < count($facilities); $i++) {
							$facility = $facilities[$i];
							echo '<li>' . $facility->name  . '</li>';
						} ?>					
						</ul>
					</div>
					<?php } // endif (!empty($facilities)) ?>			
					<?php BookYourTravel_Theme_Utils::render_tab_extra_fields('cruise_extra_fields', $cruise_extra_fields, 'facilities', $cruise_obj); ?>			
					<?php do_action( 'bookyourtravel_show_single_cruise_facilites_after' ); ?>
				</article>
			</section>
			<!--//facilities-->
			<?php if ($enable_reviews) { ?>
			<!--reviews-->
			<section id="reviews" class="tab-content <?php echo $first_display_tab == 'reviews' ? 'initial' : ''; ?>">
				<?php 
				do_action( 'bookyourtravel_show_single_cruise_reviews_before' );
				get_template_part('includes/parts/review', 'item'); 
				BookYourTravel_Theme_Utils::render_tab_extra_fields('cruise_extra_fields', $cruise_extra_fields, 'reviews', $cruise_obj); 
				do_action( 'bookyourtravel_show_single_cruise_reviews_after' ); 
				?>
			</section>
			<!--//reviews-->
			<?php } // if ($enable_reviews) ?>
			<?php
			foreach ($tab_array as $tab) {
				if (count(BookYourTravel_Theme_Utils::custom_array_search($default_cruise_tabs, 'id', $tab['id'])) == 0) {
					$all_empty_fields = BookYourTravel_Theme_Utils::are_tab_fields_empty('cruise_extra_fields', $cruise_extra_fields, $tab['id'], $cruise_obj);
					
					if (!$all_empty_fields) {
				?>
					<section id="<?php echo esc_attr($tab['id']); ?>" class="tab-content <?php echo ($first_display_tab == $tab['id'] ? 'initial' : ''); ?>">
						<article>
							<?php do_action( 'bookyourtravel_show_single_cruise_' . $tab['id'] . '_before' ); ?>
							<?php BookYourTravel_Theme_Utils::render_tab_extra_fields('cruise_extra_fields', $cruise_extra_fields, $tab['id'], $cruise_obj); ?>
							<?php do_action( 'bookyourtravel_show_single_cruise_' . $tab['id'] . '_after' ); ?>
						</article>
					</section>
				<?php
					}
				}
			}	
			do_action( 'bookyourtravel_show_single_cruise_tab_content_after' ); ?>
		</section>
		<!--//cruise content-->	
		<?php get_sidebar('right-cruise'); ?>
	</div>
	<div class="booking_form_controls_holder" style="display:none">

		<div class="text-wrap booking_terms">
			<div>
				<p><?php esc_html_e('Select your cruise date using the calendar below to book this cruise.', 'bookyourtravel') ?></p>
				<p>
				<?php 					
				if ($cruise_obj->get_type_is_repeated() == 1) {
					echo esc_html__('This is a daily cruise.', 'bookyourtravel'); 
				} else if ($cruise_obj->get_type_is_repeated() == 2) {
					echo esc_html__('This cruise is repeated every weekday (working day).', 'bookyourtravel'); 
				} else if ($cruise_obj->get_type_is_repeated() == 3) {
					echo sprintf(esc_html__('This cruise is repeated every week on a %s.', 'bookyourtravel'), $cruise_obj->get_type_day_of_week_day()); 
				} else if ($cruise_obj->get_type_is_repeated() == 4) {
					echo esc_html__('This cruise is repeated every week on multiple days.', 'bookyourtravel'); 
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
				<div class="datepicker_holder"></div>
			</div>
		</div>
		<div class="row loading" id="datepicker_loading" style="display:none">
			<div class="ball"></div>
			<div class="ball1"></div>
		</div>			
		<div class="text-wrap price_row" style="display:none">
			<h3><?php esc_html_e('Who is checking in?', 'bookyourtravel') ?></h3>
			<p><?php esc_html_e('Please select number of adults and children checking into the cruise using the controls you see below.', 'bookyourtravel') ?></p>
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

		$extra_items = $bookyourtravel_extra_item_helper->list_extra_items_by_post_type('cruise', array($cruise_obj->get_type_id()), $cruise_obj->get_tag_ids());
		if (count($extra_items) > 0) {
		?>
		<div class="text-wrap price_row extra_items_row" style="display:none">
			<h3><?php esc_html_e('Extra items', 'bookyourtravel') ?></h3>
			<p><?php esc_html_e('Please select the extra items you wish to be included with your cruise using the controls you see below.', 'bookyourtravel') ?></p>
		
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
							$cruise_extra_item_obj = new BookYourTravel_Extra_Item($extra_item);
							$item_teaser = BookYourTravel_Theme_Utils::strip_tags_and_shorten_by_words($cruise_extra_item_obj->get_content(), 20);
							$max_allowed = $cruise_extra_item_obj->get_custom_field('_extra_item_max_allowed', false);
							$item_price =$cruise_extra_item_obj->get_custom_field('_extra_item_price', false);
							$item_price_per_person = intval($cruise_extra_item_obj->get_custom_field('_extra_item_price_per_person', false));
							$item_price_per_day = intval($cruise_extra_item_obj->get_custom_field('_extra_item_price_per_day', false));
							$item_is_required = intval($cruise_extra_item_obj->get_custom_field('_extra_item_is_required', false));											
							
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

		<div class="text-wrap price_row" style="display:none">
			<h3><?php esc_html_e('Summary', 'bookyourtravel'); ?></h3>
			<p><?php esc_html_e('The summary of your cruise booking is shown below.', 'bookyourtravel'); ?></p>
			
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
						<th><?php esc_html_e('Cruise duration (days)', 'bookyourtravel') ?></th>
						<td>
							<span id="duration_days_span"></span>
							<input type="hidden" name="duration_days" id="duration_days" value="" />
						</td>
					</tr>
					<tr class="cabin_type_row">
						<th>
							<?php esc_html_e('Cabin type', 'bookyourtravel') ?>
						</th>
						<td>
							<span class="cabin_type_span"></span>
							<input type="hidden" name="cabin_type_id" id="cabin_type_id" />							
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
							<?php esc_html_e('Booking total', 'bookyourtravel') ?>
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
					<label><?php esc_html_e('Cruise price breakdown', 'bookyourtravel') ?></label>
					<table class="cruise_price_breakdown tablesorter responsive">
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

		<div class='booking-commands'>
		<?php
		BookYourTravel_Theme_Utils::render_link_button("#", "gradient-button book-cruise-reset", "book-cruise-rest", esc_html__('Reset', 'bookyourtravel'));
		BookYourTravel_Theme_Utils::render_link_button("#", "gradient-button book-cruise-next", "book-cruise-next", esc_html__('Proceed', 'bookyourtravel'));
		?>
		</div>
	</div>
<?php
} // end if
get_footer(); 