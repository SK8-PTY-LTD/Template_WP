<?php

/*-----------------------------------------------------------------------------------

	Plugin Name: BYT Search Widget

-----------------------------------------------------------------------------------*/

// Add function to widgets_init that'll load our widget.
add_action( 'widgets_init', 'bookyourtravel_search_widgets' );

// Register widget.
function bookyourtravel_search_widgets() {
	register_widget( 'bookyourtravel_search_widget' );
}

// Widget class.
class bookyourtravel_search_widget extends WP_Widget {

	/*-----------------------------------------------------------------------------------*/
	/*	Widget Setup
	/*-----------------------------------------------------------------------------------*/
	
	private $custom_search_results_page;
	private $enable_reviews;
	private $enable_tours;
	private $enable_car_rentals;
	private $enable_accommodations;
	private $enable_cruises;
	private $make_car_rentals_searchable; 
	private $make_cruises_searchable; 
	private $make_tours_searchable; 
	private $make_accommodations_searchable;
	
	function __construct() {
	
		if (!is_admin()) {
			wp_register_script( 'bookyourtravel-search-widget', BookYourTravel_Theme_Utils::get_file_uri('/js/search_widget.js'), array('jquery', 'bookyourtravel-jquery-uniform', 'jquery-ui-spinner'), BOOKYOURTRAVEL_VERSION, true );	
			wp_enqueue_script( 'bookyourtravel-search-widget' );	
			wp_enqueue_script( 'custom-suggest', BookYourTravel_Theme_Utils::get_file_uri ('/js/custom-suggest.js'), array('jquery'), '', true );
		}
		
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'bookyourtravel_search_widget', 'description' => esc_html__('BookYourTravel: Search', 'bookyourtravel') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 260, 'height' => 600, 'id_base' => 'bookyourtravel_search_widget' );

		/* Create the widget. */
		parent::__construct( 'bookyourtravel_search_widget', esc_html__('BookYourTravel: Search', 'bookyourtravel'), $widget_ops, $control_ops );
		
		global $bookyourtravel_theme_globals;
		
		$this->custom_search_results_page = $bookyourtravel_theme_globals->get_custom_search_results_page_url();
		
		$this->enable_reviews = $bookyourtravel_theme_globals->enable_reviews();
		$this->enable_tours = $bookyourtravel_theme_globals->enable_tours();
		$this->enable_car_rentals = $bookyourtravel_theme_globals->enable_car_rentals();
		$this->enable_accommodations = $bookyourtravel_theme_globals->enable_accommodations();
		$this->enable_cruises = $bookyourtravel_theme_globals->enable_cruises();
	}


/*-----------------------------------------------------------------------------------*/
/*	Display Widget
/*-----------------------------------------------------------------------------------*/
	
	function widget( $args, $instance ) {
		
		global $bookyourtravel_theme_globals;
		
		extract( $args );
		
		
		$what = 1;
		if (isset($_GET['what'])) {
			$what = intval(wp_kses($_GET['what'], array()));
		}
		
		$searchable_count = 0;
		
		$this->make_cruises_searchable = isset($instance['make_cruises_searchable'] ) ? $instance['make_cruises_searchable'] : true;
		$this->make_car_rentals_searchable = isset($instance['make_car_rentals_searchable'] ) ? $instance['make_car_rentals_searchable'] : true;
		$this->make_accommodations_searchable = isset($instance['make_accommodations_searchable'] ) ? $instance['make_accommodations_searchable'] : true;
		$this->make_tours_searchable = isset($instance['make_tours_searchable'] ) ? $instance['make_tours_searchable'] : true;
		
		if ($this->enable_accommodations && $this->make_accommodations_searchable)
			$searchable_count++;
		if ($this->enable_car_rentals && $this->make_car_rentals_searchable)
			$searchable_count++;	
		if ($this->enable_tours && $this->make_tours_searchable)
			$searchable_count++;
		if ($this->enable_cruises && $this->make_cruises_searchable)
			$searchable_count++;

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : esc_html__('Refine search results', 'bookyourtravel') );
		
		$show_what_filter = isset($instance['show_what_filter'] ) ? $instance['show_what_filter'] : true;
		$show_where_filter = isset($instance['show_where_filter'] ) ? $instance['show_where_filter'] : true;
		$show_when_filter = isset($instance['show_when_filter'] ) ? $instance['show_when_filter'] : true;
		$show_price_filter = isset($instance['show_price_filter'] ) ? $instance['show_price_filter'] : false;
		
		$show_stars_filter = isset($instance['show_stars_filter'] ) ? $instance['show_stars_filter'] : false;
		$show_rating_filter = isset($instance['show_rating_filter'] ) ? $instance['show_rating_filter'] : false;
		$show_room_count_filter = isset($instance['show_room_count_filter'] ) ? $instance['show_room_count_filter'] : false;
		$show_accommodation_type_filter = isset($instance['show_accommodation_type_filter'] ) ? $instance['show_accommodation_type_filter'] : false;
		
		$show_cruise_type_filter = isset($instance['show_cruise_type_filter'] ) ? $instance['show_cruise_type_filter'] : false;
		$show_cabin_count_filter = isset($instance['show_cabin_count_filter'] ) ? $instance['show_cabin_count_filter'] : false;
		$show_car_type_filter = isset($instance['show_car_type_filter'] ) ? $instance['show_car_type_filter'] : false;
		$show_tour_type_filter = isset($instance['show_tour_type_filter'] ) ? $instance['show_tour_type_filter'] : false;
		
		$what_text = isset($instance['what_text']) ? $instance['what_text'] : esc_html__('What?', 'bookyourtravel');
		$when_text = isset($instance['when_text']) ? $instance['when_text'] : esc_html__('When?', 'bookyourtravel');
		$where_text = isset($instance['where_text']) ? $instance['where_text'] : esc_html__('Where?', 'bookyourtravel');
		
		$accommodation_date_from_label_text = isset($instance['accommodation_date_from_label_text']) ? $instance['accommodation_date_from_label_text'] : esc_html__('Check-in date', 'bookyourtravel');
		$accommodation_date_to_label_text = isset($instance['accommodation_date_to_label_text']) ? $instance['accommodation_date_to_label_text'] : esc_html__('Check-out date', 'bookyourtravel');

		$rooms_label_text = isset($instance['rooms_label_text']) ? $instance['rooms_label_text'] : esc_html__('Rooms', 'bookyourtravel');
		$accommodation_type_label_text = isset($instance['accommodation_type_label_text']) ? $instance['accommodation_type_label_text'] : esc_html__('Accommodation type', 'bookyourtravel');
		$star_rating_label_text = isset($instance['star_rating_label_text']) ? $instance['star_rating_label_text'] : esc_html__('Star rating', 'bookyourtravel');
		$user_rating_label_text = isset($instance['user_rating_label_text']) ? $instance['user_rating_label_text'] : esc_html__('User rating', 'bookyourtravel');
		
		$car_rental_date_from_label_text = isset($instance['car_rental_date_from_label_text']) ? $instance['car_rental_date_from_label_text'] : esc_html__('Pick-up date', 'bookyourtravel');
		$car_rental_date_to_label_text = isset($instance['car_rental_date_to_label_text']) ? $instance['car_rental_date_to_label_text'] : esc_html__('Drop-off date', 'bookyourtravel');	
		$car_type_label_text = isset($instance['car_type_label_text']) ? $instance['car_type_label_text'] : esc_html__('Car type', 'bookyourtravel');		

		$tour_type_label_text = isset($instance['tour_type_label_text']) ? $instance['tour_type_label_text'] : esc_html__('Tour type', 'bookyourtravel');		
		$tour_date_from_label_text = isset($instance['tour_date_from_label_text']) ? $instance['tour_date_from_label_text'] : esc_html__('Start date', 'bookyourtravel');

		$cruise_type_label_text = isset($instance['cruise_type_label_text']) ? $instance['cruise_type_label_text'] : esc_html__('Cruise type', 'bookyourtravel');	
		$cruise_date_from_label_text = isset($instance['cruise_date_from_label_text']) ? $instance['cruise_date_from_label_text'] : esc_html__('Start date', 'bookyourtravel');		
		$cabins_label_text = isset($instance['cabins_label_text']) ? $instance['cabins_label_text'] : esc_html__('Cabins', 'bookyourtravel');
		
		$price_per_person_label_text = isset($instance['price_per_person_label_text']) ? $instance['price_per_person_label_text'] : esc_html__('Price per person', 'bookyourtravel');
		$price_per_night_label_text = isset($instance['price_per_night_label_text']) ? $instance['price_per_night_label_text'] : esc_html__('Price per night', 'bookyourtravel');
		$price_per_day_label_text = isset($instance['price_per_day_label_text']) ? $instance['price_per_day_label_text'] : esc_html__('Price per day', 'bookyourtravel');
		
		$location_select_label_text = isset($instance['location_select_label_text']) ? $instance['location_select_label_text'] : esc_html__('Location', 'bookyourtravel');

		$submit_button_text = isset($instance['submit_button_text']) ? $instance['submit_button_text'] : esc_html__('Search again', 'bookyourtravel');

		$search_page_id = 0;
		$permalinks_enabled = $bookyourtravel_theme_globals->permalinks_enabled();
		if (!$permalinks_enabled) {
			$search_page_id = $bookyourtravel_theme_globals->get_custom_search_results_page_id();
		}
		
		/* Before widget (defined by themes). */
		echo $before_widget;
		
		/* Display Widget */
		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title ) { }
			// echo $before_title . $title . $after_title;
			
		if ($searchable_count > 0) {
		?>
			<script>
				
				window.searchWidgetPricePerPersonLabel = <?php echo json_encode($price_per_person_label_text); ?>;
				window.searchWidgetPricePerNightLabel = <?php echo json_encode($price_per_night_label_text); ?>;
				window.searchWidgetPricePerDayLabel = <?php echo json_encode($price_per_day_label_text); ?>;
				
			</script>
			<article class="refine-search-results byt_search_widget BookYourTravel_Search_Widget">
				<form class="widget-search" method="get" action="<?php echo esc_url($this->custom_search_results_page); ?>">
					<?php if ($search_page_id > 0) { ?>
					<input type="hidden" name="page_id" value="<?php echo esc_attr($search_page_id); ?>" />
					<?php } ?>
					<?php echo $before_title . $title . $after_title; ?>
					<div>
					<?php
					if ($show_what_filter)
						$this->render_what_section($searchable_count, $what_text); 
					else {
						if ($this->enable_accommodations && $this->make_accommodations_searchable) {
							echo '<input type="hidden" id="what" name="what" value="1" />';
							echo '<script>window.activeSearchableNumber = 1;</script>';
						} elseif ($this->enable_car_rentals && $this->make_car_rentals_searchable) {
							echo '<input type="hidden" id="what" name="what" value="2" />';
							echo '<script>window.activeSearchableNumber = 2;</script>';
						} elseif ($this->enable_tours && $this->make_tours_searchable) {
							echo '<input type="hidden" id="what" name="what" value="3" />';
							echo '<script>window.activeSearchableNumber = 3;</script>';
						} elseif ($this->enable_cruises && $this->make_cruises_searchable) {
							echo '<input type="hidden" id="what" name="what" value="4" />';
							echo '<script>window.activeSearchableNumber = 4;</script>';
						}
					}
						
					if ($show_when_filter)
						$this->render_when_section($when_text, $accommodation_date_from_label_text, $accommodation_date_to_label_text, $car_rental_date_from_label_text, $car_rental_date_to_label_text, $tour_date_from_label_text, $cruise_date_from_label_text);
					
					if ($show_where_filter)
						$this->render_where_section($where_text, $location_select_label_text);
					
					if ($show_price_filter)
						$this->render_price_range_section($what, $price_per_person_label_text, $price_per_night_label_text, $price_per_day_label_text);
					
					if ($this->enable_accommodations && $show_stars_filter)
						$this->render_star_rating_section($star_rating_label_text);
					
					if ($this->enable_reviews && $show_rating_filter)
						$this->render_user_rating_section($user_rating_label_text);
						
					if ($this->enable_accommodations && $show_accommodation_type_filter)
						$this->render_accommodation_type_section($accommodation_type_label_text);

					if ($this->enable_accommodations && $show_room_count_filter)
						$this->render_room_count_section($rooms_label_text);
						
					if ($this->enable_tours && $show_tour_type_filter)
						$this->render_tour_type_section($tour_type_label_text);
						
					if ($this->enable_car_rentals && $show_car_type_filter)
						$this->render_car_type_section($car_type_label_text);
					
					if ($this->enable_cruises && $show_cruise_type_filter)
						$this->render_cruise_type_section($cruise_type_label_text);
						
					if ($this->enable_cruises && $show_cabin_count_filter)
						$this->render_cabin_count_section($cabins_label_text);
						
					?>					
					</div>
					<input type="submit" value="<?php echo esc_attr( $submit_button_text ); ?>" class="gradient-button" id="search-submit" />
				</form>
			</article>        	
		<?php
		}
		
		/* After widget (defined by themes). */
		echo $after_widget;
	}
	
	function render_where_section($where_text, $location_select_label_text) {
		
		global $bookyourtravel_location_helper;
		
		$l = isset($_GET['l']) ? intval(wp_kses($_GET['l'], array())) : 0;
		
		$locations_select = '<select id="search_widget_term" name="l">';
		$locations_select .= '<option value="">' . esc_html__('Select location', 'bookyourtravel') . '</option>';
		$location_results = $bookyourtravel_location_helper->list_locations(0, 1, -1, 'title', 'asc');
		if ( count($location_results) > 0 && $location_results['total'] > 0 ) {
			foreach ($location_results['results'] as $location_result) {
				$locations_select .= '<option value="' . esc_attr($location_result->ID) . '" ' . ($location_result->ID == $l ? 'selected' : '') . '>' . $location_result->post_title . '</option>';
			}
		}
		$locations_select .= '</select>';
	?>	
		<div class="column">
			<div class="where dt">
				<?php echo $where_text; ?>
			</div>
			<div class="where dd">
				<script>		
					window.searchAccommodationLocationLabel = <?php echo json_encode($where_text); ?>;
					window.searchCarRentalLocationLabel = <?php echo json_encode($where_text); ?>;
					window.searchTourLocationLabel = <?php echo json_encode($where_text); ?>;
					window.searchCruiseLocationLabel = <?php echo json_encode($where_text); ?>;
				</script>
				<div class="destination">
					<label for="search_widget_term"><?php echo $location_select_label_text; ?></label>
					<?php echo $locations_select; ?>
				</div>
			</div>
		</div>
	<?php
	}
		
	function render_room_count_section($rooms_label_text) {
		$rooms = isset($_GET['rooms']) ? intval(wp_kses($_GET['rooms'], array())) : 0;
		?>
		<div class="column">
			<div class="rooms dt">
				<?php echo $rooms_label_text; ?>
			</div>
			<div class="rooms dd">
				<div class="spinner">
					<input type="text" id="search_widget_rooms" name="rooms" value="<?php echo esc_attr( $rooms ); ?>" />
				</div>
			</div>
		</div>
		<?php
	}
	
	function render_cabin_count_section($cabins_label_text) {
		$cabins = isset($_GET['cabins']) ? intval(wp_kses($_GET['cabins'], array())) : 0;
		?>
		<div class="column">
			<div class="cabins dt">
				<?php echo $cabins_label_text; ?>
			</div>
			<div class="cabins dd">
				<div class="spinner">
					<input type="text" id="search_widget_cabins" name="cabins" value="<?php echo esc_attr( $cabins ); ?>" />
				</div>
			</div>
		</div>
		<?php
	}
		
	function render_when_section($when_text, $accommodation_date_from_label_text, $accommodation_date_to_label_text, $car_rental_date_from_label_text, $car_rental_date_to_label_text, $tour_date_from_label_text, $cruise_date_from_label_text) { 
		
		$date_format = get_option('date_format');
		$from = isset($_GET['from']) && !empty($_GET['from']) ? date($date_format, strtotime(sanitize_text_field($_GET['from']))) : null;
		$to = isset($_GET['to']) && !empty($_GET['to']) ? date($date_format, strtotime(sanitize_text_field($_GET['to']))) : null;
	?>
		<div class="column">
			<div class="when dt">
				<?php echo $when_text; ?>
			</div>
			<div class="when dd">
				<script>
					window.searchAccommodationDateFromLabel = <?php echo json_encode($accommodation_date_from_label_text); ?>;
					window.searchAccommodationDateToLabel = <?php echo json_encode($accommodation_date_to_label_text); ?>;
					window.searchCarRentalDateFromLabel = <?php echo json_encode($car_rental_date_from_label_text); ?>;
					window.searchCarRentalDateToLabel = <?php echo json_encode($car_rental_date_to_label_text); ?>;
					window.searchTourDateFromLabel = <?php echo json_encode($tour_date_from_label_text); ?>;	
					window.searchCruiseDateFromLabel = <?php echo json_encode($cruise_date_from_label_text); ?>;
					window.datePickerFromValue = '<?php echo $from != null ? esc_js($from) : ""; ?>';
					window.datePickerToValue = '<?php echo $to != null ? esc_js($to) : ""; ?>';
				</script>
				<div class="datepicker">
					<label for="search_widget_date_from"><?php echo $accommodation_date_from_label_text; ?></label>
					<div class="datepicker-wrap"><input type="text" id="search_widget_date_from" placeholder="" /></div>
					<input type="hidden" id="from" name="from" />
				</div>
				<div class="datepicker">
					<label for="search_widget_date_to"><?php echo $accommodation_date_to_label_text; ?></label>
					<div class="datepicker-wrap"><input type="text" id="search_widget_date_to" placeholder="" /></div>
					<input type="hidden" id="to" name="to" />
				</div>
			</div>
		</div>
		<?php
	}	
	

	function render_cruise_type_section($cruise_type_label_text) {
	
		$request_type_ids = array();
		if (isset($_GET['cruise_types'])) {
			$request_type_ids = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('cruise_types', true);
		}
	
		$args = array( 
			'taxonomy'=>'cruise_type', 
			'hide_empty'=>'0'
		);
		$cruise_types = get_categories($args);
	
		if (count($cruise_types) > 0) {
	?>	
		<div class="column">
			<div class="cruise_type dt"><?php echo $cruise_type_label_text; ?></div>
			<div class="cruise_type dd">
			<?php for ($i = 0; $i < count($cruise_types); $i++) {
				$checked = '';
				if (isset($cruise_types[$i])) {
					$cruise_type = $cruise_types[$i];
					if (in_array($cruise_type->term_id, $request_type_ids)) {
						$checked = " checked='checked' ";
					}
			?>
				<div class="checkbox">
					<input <?php echo $checked; ?> value="<?php echo esc_attr( $cruise_type->term_id ); ?>" type="checkbox" id="at<?php echo $i + 1; ?>" name="cruise_types[]" />
					<label for="at<?php echo $i + 1; ?>"><?php echo esc_attr( $cruise_type->name ); ?></label>
				</div>
			<?php 	} ?>
			<?php } ?>
			</div>	
		</div>	
	<?php
		}	
	}
			
	function render_accommodation_type_section($accommodation_type_label_text) {
	
		$request_type_ids = array();
		if (isset($_GET['accommodation_types'])) {
			$request_type_ids = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('accommodation_types', true);
		}
	
		$args = array( 
			'taxonomy'=>'accommodation_type', 
			'hide_empty'=>'0'
		);
		$accommodation_types = get_categories($args);
	
		if (count($accommodation_types) > 0) {
	?>	
		<div class="column">
			<div class="accommodation_type dt"><?php echo $accommodation_type_label_text; ?></div>
			<div class="accommodation_type dd">
			<?php for ($i = 0; $i < count($accommodation_types); $i++) {
				$checked = '';
				if (isset($accommodation_types[$i])) {
					$accommodation_type = $accommodation_types[$i];
					if (in_array($accommodation_type->term_id, $request_type_ids)) {
						$checked = " checked='checked' ";
					}
			?>
				<div class="checkbox">
					<input <?php echo $checked; ?> value="<?php echo esc_attr( $accommodation_type->term_id ); ?>" type="checkbox" id="at<?php echo $i + 1; ?>" name="accommodation_types[]" />
					<label for="at<?php echo $i + 1; ?>"><?php echo esc_attr( $accommodation_type->name ); ?></label>
				</div>
			<?php 	} ?>
			<?php } ?>
			</div>
		</div>
	<?php
		}	
	}
	
	function render_car_type_section($car_type_label_text) {
	
		$request_type_ids = array();
		if (isset($_GET['car_types'])) {
			$request_type_ids = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('car_types', true);
		}
	
		$args = array( 
			'taxonomy'=>'car_type', 
			'hide_empty'=>'0'
		);
		$car_types = get_categories($args);
		
		if (count($car_types) > 0) {
	?>	
		<div class="column">
			<div class="car_type dt"><?php echo $car_type_label_text; ?></div>
			<div class="car_type dd">
			<?php for ($i = 0; $i < count($car_types); $i++) { 
				if (isset($car_types[$i])) {
				$car_type = $car_types[$i];
				
				$checked = '';
				if (in_array($car_type->term_id, $request_type_ids)) {
					$checked = " checked='checked' ";
				}
			?>
				<div class="checkbox">
					<input <?php echo $checked; ?> value="<?php echo esc_attr( $car_type->term_id ); ?>" type="checkbox" id="ct<?php echo $i + 1; ?>" name="car_types[]" />
					<label for="ct<?php echo $i + 1; ?>"><?php echo esc_attr( $car_type->name ); ?></label>
				</div>
			<?php } 		
			}?>
			</div>
		</div>	
	<?php
		}	
	}
	

	function render_tour_type_section($tour_type_label_text) {
	
		$request_type_ids = array();
		if (isset($_GET['tour_types'])) {
			$request_type_ids = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('tour_types', true);
		}
	
		$args = array( 
			'taxonomy'=>'tour_type', 
			'hide_empty'=>'0'
		);
		$tour_types = get_categories($args);
		
		if (count($tour_types) > 0) {
	?>	
		<div class="column">
			<div class="tour_type dt"><?php echo $tour_type_label_text; ?></div>
			<div class="tour_type dd">
			<?php for ($i = 0; $i < count($tour_types); $i++) { 
				if (isset($tour_types[$i])) {
				$tour_type = $tour_types[$i];
				
				$checked = '';
				if (in_array($tour_type->term_id, $request_type_ids)) {
					$checked = " checked='checked' ";
				}
			?>
				<div class="checkbox">
					<input <?php echo $checked; ?> value="<?php echo esc_attr( $tour_type->term_id ); ?>" type="checkbox" id="ct<?php echo $i + 1; ?>" name="tour_types[]" />
					<label for="ct<?php echo $i + 1; ?>"><?php echo esc_attr( $tour_type->name ); ?></label>
				</div>
			<?php } 		
			}?>
			</div>
		</div>	
	<?php
		}	
	}
	
	function render_user_rating_section($user_rating_label_text) {
		$rating = isset($_GET['rating']) ? intval(wp_kses($_GET['rating'], array())) : 0;
		if (isset($_GET['rating'])) {
			if ($rating > 10)
				$rating = 10;
			else if ($rating < 0)
				$rating = 0;
		}
	?>
		<div class="column">
			<div class="user_rating dt"><?php echo $user_rating_label_text; ?></div>
			<div class="user_rating dd">
				<script>
					window.searchWidgetRating = <?php echo json_encode($rating); ?>;
				</script>
				<div id="search_widget_rating_slider"></div>
				<input type="hidden" id="search_widget_rating" name="rating" value="<?php echo esc_attr( $rating ); ?>" />
				<span class="min">0</span><span class="max">10</span>
			</div>
		</div>
	<?php
	
	}
	
	function render_star_rating_section($star_rating_label_text) {
		$stars = isset($_GET['stars']) ? intval(wp_kses($_GET['stars'], array())) : 0;
		if (isset($_GET['stars'])) {
			$stars = intval(wp_kses($_GET['stars'], array()));
			if ($stars > 5)
				$stars = 5;
			else if ($stars < 0)
				$stars = 0;
		}
	?>
		<div class="column">
			<div class="star_rating dt"><?php echo $star_rating_label_text; ?></div>
			<div class="star_rating dd">
				<script>
					window.searchWidgetStars = <?php echo json_encode($stars); ?>;
				</script>
				<span class="stars-info"><?php echo sprintf(esc_html__('%d or more', 'bookyourtravel'), $stars); ?></span>
				<div class="search_widget_star" data-rating="<?php echo esc_attr( $stars ); ?>"></div>
			</div>
		</div>
	<?php	
	}
	
	function render_price_range_section($what, $price_per_person_label_text, $price_per_night_label_text, $price_per_day_label_text) {
	
		global $bookyourtravel_theme_globals;
		
		$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
		$default_currency_symbol = $bookyourtravel_theme_globals->get_default_currency_symbol();
		$show_currency_symbol_after = $bookyourtravel_theme_globals->show_currency_symbol_after();
	
		$request_type_ids = array();
		if (isset($_GET['price'])) {
			$request_type_ids = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('price', true);
		}
	
		$price_range_bottom = $bookyourtravel_theme_globals->get_price_range_bottom();
		$price_range_increment = $bookyourtravel_theme_globals->get_price_range_increment();
		$price_range_count = $bookyourtravel_theme_globals->get_price_range_count();
	
		if ($price_range_count > 0) { ?>
			<div class="column">
				<div class="price_per dt">
				<?php
				if ($what == 1) {
					echo $price_per_night_label_text;
				} elseif ($what == 2) {
					echo $price_per_day_label_text;
				} elseif ($what == 3 || $what == 4) { 
					echo $price_per_person_label_text;
				} ?>
				</div>
				<div class="dd">
				<?php 
					$bottom = 0;
					$top = 0;
					$out = '';
					for ( $i = 0; $i < $price_range_count; $i++ ) { 
						$price_index = $i + 1;
						$checked = '';
						if (in_array($price_index, $request_type_ids)) {
							$checked = " checked='checked' ";
						}
					?>
					<div class="checkbox">
						<input <?php echo $checked; ?> type="checkbox" id="price<?php echo esc_attr( $price_index ); ?>" name="price[]" value="<?php echo esc_attr( $price_index ); ?>" />
						<label for="price<?php echo $price_index; ?>">
						<?php								
						$bottom = ($i * $price_range_increment) + $price_range_bottom;
						$top = (($price_index) * $price_range_increment) + $price_range_bottom - 1;								
						echo $bottom;
						if ($i == ($price_range_count-1)) {
							echo ' <span class="curr">' . $default_currency_symbol . '</span> +';
						} else {
							echo " - " . $top . ' <span class="curr">' . $default_currency_symbol . '</span>';
						}								
						?>
						</label>
					</div>
					<?php } ?>
				</div>
			</div>
		<?php }
	}
	
	function render_what_section($searchable_count, $what_text) {
	
		$what = isset($_GET['what']) ? intval(wp_kses($_GET['what'], array())) : 1;
	
		if ($searchable_count > 1) { ?>
			<div class="column">
				<div class="what dt"><?php echo $what_text; ?></div>
				<div class="what dd">
			<?php
				if ($this->enable_accommodations && $this->make_accommodations_searchable) {
					if ($this->is_accommodation_what_active($what)) { ?>
					<script>window.activeSearchableNumber = 1;</script>
					<?php } ?>
					<div class="checkbox <?php echo $this->is_accommodation_what_active($what) ? 'active' : ''; ?>" >
						<input type="radio" name="what" id="accommodation" value="1" <?php echo $this->is_accommodation_what_active($what) ? ' checked="checked"' : ''; ?> />
						<label for="accommodation"> <?php esc_html_e('Accommodation', 'bookyourtravel'); ?></label>
					</div>
				<?php } 
				if ($this->enable_car_rentals && $this->make_car_rentals_searchable) {
					if ($this->is_car_rental_what_active($what)) { ?>
					<script>window.activeSearchableNumber = 2;</script>
					<?php } ?>
					<div class="checkbox <?php echo $this->is_car_rental_what_active($what) ? 'active' : ''?>">
						<input type="radio" name="what" id="car_rental" value="2" <?php echo $this->is_car_rental_what_active($what)  ? ' checked="checked" ' : '' ?> />
						<label for="car_rental"> <?php esc_html_e('Rent a Car', 'bookyourtravel'); ?></label>
					</div>
				<?php } 
				if ($this->enable_tours && $this->make_tours_searchable) {
					if ($this->is_tour_what_active($what)) {?>
					<script>window.activeSearchableNumber = 3;</script>
					<?php } ?>
					<div class="checkbox <?php echo $this->is_tour_what_active($what) ? 'active' : ''?>" >
						<input type="radio" name="what" id="tour" value="3" <?php echo $this->is_tour_what_active($what)  ? ' checked="checked" ' : '' ?> />
						<label for="tour"> <?php esc_html_e('Tour', 'bookyourtravel'); ?></label>
					</div>
				<?php }
				if ($this->enable_cruises && $this->make_cruises_searchable) {
					if ($this->is_cruise_what_active($what) ) { ?>
					<script>window.activeSearchableNumber = 4;</script>
					<?php } ?>
					<div class="checkbox <?php echo $this->is_cruise_what_active($what) ? 'active' : ''?>" >
						<input type="radio" name="what" id="cruise" value="4" <?php echo $this->is_cruise_what_active($what) ? ' checked="checked"' : '' ?> />
						<label for="cruise"> <?php esc_html_e('Cruise', 'bookyourtravel'); ?></label>
					</div>
					<?php 
				} ?>
				</div>
			</div>
		<?php
		} else {
			if ($this->enable_accommodations && $this->make_accommodations_searchable) {
				echo '<input type="hidden" id="what" name="what" value="1" />';
				echo '<script>window.activeSearchableNumber = 1;</script>';
			} elseif ($this->enable_car_rentals && $this->make_car_rentals_searchable) {
				echo '<input type="hidden" id="what" name="what" value="2" />';
				echo '<script>window.activeSearchableNumber = 2;</script>';
			} elseif ($this->enable_tours && $this->make_tours_searchable) {
				echo '<input type="hidden" id="what" name="what" value="3" />';
				echo '<script>window.activeSearchableNumber = 3;</script>';
			} elseif ($this->enable_cruises && $this->make_cruises_searchable) {
				echo '<input type="hidden" id="what" name="what" value="4" />';
				echo '<script>window.activeSearchableNumber = 4;</script>';
			}
		}	
	}
	
	function is_accommodation_what_active($what) {
		return ($this->enable_accommodations && $this->make_accommodations_searchable) || $what == 1;
	}

	function is_car_rental_what_active($what) {
		return ((!$this->is_accommodation_what_active(2) && 
				$this->enable_car_rentals && 
				$this->make_car_rentals_searchable)
				|| $what == 2);
	}
	
	function is_tour_what_active($what) {
		return ((!$this->is_accommodation_what_active(3) && 
				!$this->is_car_rental_what_active(3) && 
				$this->enable_tours && 
				$this->make_tours_searchable) 
				|| $what == 3);
	}
	
	function is_cruise_what_active($what) {
		return ((!$this->is_accommodation_what_active(4) && 
				!$this->is_car_rental_what_active(4) && 
				!$this->is_tour_what_active(4) && 
				$this->enable_cruises && 
				$this->make_cruises_searchable) 
				|| $what == 4);
	}

/*-----------------------------------------------------------------------------------*/
/*	Update Widget
/*-----------------------------------------------------------------------------------*/
	
	function update( $new_instance, $old_instance ) {
	
		$instance = $old_instance;

		/* Strip tags to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );	
		
		$instance['show_what_filter'] = isset($new_instance['show_what_filter']) ? strip_tags( $new_instance['show_what_filter'] ) : '';	
		$instance['show_where_filter'] = isset($new_instance['show_where_filter']) ? strip_tags( $new_instance['show_where_filter'] ) : '';	
		$instance['show_when_filter'] = isset($new_instance['show_when_filter']) ? strip_tags( $new_instance['show_when_filter'] ) : '';	
		$instance['show_price_filter'] = isset($new_instance['show_price_filter']) ? strip_tags( $new_instance['show_price_filter'] ) : '';	
		$instance['show_stars_filter'] = isset($new_instance['show_stars_filter']) ? strip_tags( $new_instance['show_stars_filter'] ) : '';	
		$instance['show_rating_filter'] = isset($new_instance['show_rating_filter']) ? strip_tags( $new_instance['show_rating_filter'] ) : '';	
		$instance['show_room_count_filter'] = isset($new_instance['show_room_count_filter']) ? strip_tags( $new_instance['show_room_count_filter'] ) : '';	
		$instance['show_accommodation_type_filter'] = isset($new_instance['show_accommodation_type_filter']) ? strip_tags( $new_instance['show_accommodation_type_filter'] ) : '';	
		$instance['show_cruise_type_filter'] = isset($new_instance['show_cruise_type_filter']) ? strip_tags( $new_instance['show_cruise_type_filter'] ) : '';	
		$instance['show_cabin_count_filter'] = isset($new_instance['show_cabin_count_filter']) ? strip_tags( $new_instance['show_cabin_count_filter'] ) : '';	
		$instance['show_car_type_filter'] = isset($new_instance['show_car_type_filter']) ? strip_tags( $new_instance['show_car_type_filter'] ) : '';	
		$instance['show_tour_type_filter'] = isset($new_instance['show_tour_type_filter']) ? strip_tags( $new_instance['show_tour_type_filter'] ) : '';	
		
		$instance['what_text'] = isset($new_instance['what_text']) ? strip_tags( $new_instance['what_text']) : '';
		$instance['when_text'] = isset($new_instance['when_text']) ? strip_tags( $new_instance['when_text']) : '';
		$instance['where_text'] = isset($new_instance['where_text']) ? strip_tags( $new_instance['where_text']) : '';
		
		$instance['accommodation_date_from_label_text'] = isset($new_instance['accommodation_date_from_label_text']) ? strip_tags( $new_instance['accommodation_date_from_label_text']) : '';
		$instance['accommodation_date_to_label_text'] = isset($new_instance['accommodation_date_to_label_text']) ? strip_tags( $new_instance['accommodation_date_to_label_text']) : '';
		$instance['accommodation_location_label_text'] = isset($new_instance['accommodation_location_label_text']) ? strip_tags( $new_instance['accommodation_location_label_text']) : '';
		$instance['rooms_label_text'] = isset($new_instance['rooms_label_text']) ? strip_tags( $new_instance['rooms_label_text']) : '';
		$instance['accommodation_type_label_text'] = isset($new_instance['accommodation_type_label_text']) ? strip_tags( $new_instance['accommodation_type_label_text']) : '';
		$instance['star_rating_label_text'] = isset($new_instance['star_rating_label_text']) ? strip_tags( $new_instance['star_rating_label_text']) : '';
		$instance['user_rating_label_text'] = isset($new_instance['user_rating_label_text']) ? strip_tags( $new_instance['user_rating_label_text']) : '';
		$instance['car_rental_location_label_text'] = isset($new_instance['car_rental_location_label_text']) ? strip_tags( $new_instance['car_rental_location_label_text']) : '';
		$instance['car_rental_date_from_label_text'] = isset($new_instance['car_rental_date_from_label_text']) ? strip_tags( $new_instance['car_rental_date_from_label_text']) : '';
		$instance['car_rental_date_to_label_text'] = isset($new_instance['car_rental_date_to_label_text']) ? strip_tags( $new_instance['car_rental_date_to_label_text']) : '';	
		$instance['car_type_label_text'] = isset($new_instance['car_type_label_text']) ? strip_tags( $new_instance['car_type_label_text']) : '';

		$instance['tour_type_label_text'] = isset($new_instance['tour_type_label_text']) ? strip_tags( $new_instance['tour_type_label_text']) : '';
		
		$instance['tour_location_label_text'] = isset($new_instance['tour_location_label_text']) ? strip_tags( $new_instance['tour_location_label_text']) : '';
		$instance['tour_date_from_label_text'] = isset($new_instance['tour_date_from_label_text']) ? strip_tags( $new_instance['tour_date_from_label_text']) : '';
		$instance['cruise_type_label_text'] = isset($new_instance['cruise_type_label_text']) ? strip_tags( $new_instance['cruise_type_label_text']) : '';
		$instance['cruise_date_from_label_text'] = isset($new_instance['cruise_date_from_label_text']) ? strip_tags( $new_instance['cruise_date_from_label_text']) : '';		
		$instance['cabins_label_text'] = isset($new_instance['cabins_label_text']) ? strip_tags( $new_instance['cabins_label_text']) : '';
		$instance['price_per_person_label_text'] = isset($new_instance['price_per_person_label_text']) ? strip_tags( $new_instance['price_per_person_label_text']) : '';
		$instance['price_per_night_label_text'] = isset($new_instance['price_per_night_label_text']) ? strip_tags( $new_instance['price_per_night_label_text']) : '';
		$instance['price_per_day_label_text'] = isset($new_instance['price_per_day_label_text']) ? strip_tags( $new_instance['price_per_day_label_text']) : '';		
		$instance['submit_button_text'] = isset($new_instance['submit_button_text']) ? strip_tags( $new_instance['submit_button_text']) : '';
		
		$instance['location_select_label_text'] = isset($new_instance['location_select_label_text']) ? strip_tags( $new_instance['location_select_label_text']) : '';

		$instance['make_accommodations_searchable'] = isset($new_instance['make_accommodations_searchable']) ? strip_tags( $new_instance['make_accommodations_searchable'] ) : '';	
		$instance['make_tours_searchable'] = isset($new_instance['make_tours_searchable']) ? strip_tags( $new_instance['make_tours_searchable'] ) : '';	
		$instance['make_cruises_searchable'] = isset($new_instance['make_cruises_searchable']) ? strip_tags( $new_instance['make_cruises_searchable'] ) : '';	
		$instance['make_car_rentals_searchable'] = isset($new_instance['make_car_rentals_searchable']) ? strip_tags( $new_instance['make_car_rentals_searchable'] ) : '';	
		
		return $instance;
	}
	

/*-----------------------------------------------------------------------------------*/
/*	Widget Settings
/*-----------------------------------------------------------------------------------*/
	 
	function form( $instance ) {
	
		global $bookyourtravel_theme_globals;

		/* Set up some default widget settings. */
		$defaults = array(
			'title' => esc_html__('Refine search results', 'bookyourtravel'),
			'enabled_search_fields_text' => esc_html__('What search fields do you want enabled?', 'bookyourtravel'),
			'what_text' => esc_html__('What?', 'bookyourtravel'),
			'when_text' => esc_html__('When?', 'bookyourtravel'),
			'where_text' => esc_html__('Where?', 'bookyourtravel'),
			'accommodation_date_from_label_text' => esc_html__('Check-in date', 'bookyourtravel'),
			'accommodation_date_to_label_text' => esc_html__('Check-out date', 'bookyourtravel'),
			'rooms_label_text' => esc_html__('Rooms', 'bookyourtravel'),
			'accommodation_location_label_text' => esc_html__('Your destination', 'bookyourtravel'),
			'accommodation_type_label_text' => esc_html__('Accommodation type', 'bookyourtravel'),
			'star_rating_label_text' => esc_html__('Star rating', 'bookyourtravel'),
			'user_rating_label_text' => esc_html__('User rating', 'bookyourtravel'),
			'car_rental_location_label_text' => esc_html__('Pick up from', 'bookyourtravel'),
			'car_rental_date_from_label_text' => esc_html__('Pick-up date', 'bookyourtravel'),
			'car_rental_date_to_label_text' => esc_html__('Drop-off date', 'bookyourtravel'),	
			'car_type_label_text' => esc_html__('Car type', 'bookyourtravel'),		
			'tour_type_label_text' => esc_html__('Tour type', 'bookyourtravel'),	
			'tour_location_label_text' => esc_html__('Tour location', 'bookyourtravel'),
			'tour_date_from_label_text' => esc_html__('Start date', 'bookyourtravel'),
			'cruise_date_from_label_text' => esc_html__('Start date', 'bookyourtravel'),	
			'cruise_type_label_text' => esc_html__('Cruise type', 'bookyourtravel'),	
			'cabins_label_text' => esc_html__('Cabins', 'bookyourtravel'),
			'price_per_person_label_text' => esc_html__('Price per person', 'bookyourtravel'),
			'price_per_night_label_text' => esc_html__('Price per night', 'bookyourtravel'),
			'price_per_day_label_text' => esc_html__('Price per day', 'bookyourtravel'),
			'submit_button_text' => esc_html__('Search again', 'bookyourtravel'),
			'show_what_filter' => true,
			'show_where_filter' => true,
			'show_when_filter' => true,
			'show_price_filter' => false,
			'show_stars_filter' => false,
			'show_rating_filter' => false,
			'show_room_count_filter' => false,
			'show_accommodation_type_filter' => false,		
			'show_cruise_type_filter' => false,
			'show_cabin_count_filter' => false,
			'show_car_type_filter' => false,
			'show_tour_type_filter' => false,
			'location_select_label_text' => esc_html__('Search location', 'bookyourtravel'),
			'make_cruises_searchable' => true,
			'make_car_rentals_searchable' => true,
			'make_accommodations_searchable' => true,
			'make_tours_searchable' => true,
		);
		
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e('Title:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		
		<p>
			<?php esc_html_e('What general search filters do you want enabled?', 'bookyourtravel') ?>
		</p>
		
		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_what_filter' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_what_filter' ) ); ?>" type="checkbox" value="1" <?php checked( $instance['show_what_filter'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_what_filter' ) ); ?>"><?php esc_html_e( 'Show "What" filter?', 'bookyourtravel'); ?></label>
		</p>
		
		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_where_filter' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_where_filter' ) ); ?>" type="checkbox" value="1" <?php checked( $instance['show_where_filter'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_where_filter' ) ); ?>"><?php esc_html_e( 'Show "Where" filter?', 'bookyourtravel'); ?></label>
		</p>
		
		<p style="<?php echo $bookyourtravel_theme_globals->search_only_available_properties() ? 'display:block;' : 'display:none'; ?>">
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_when_filter' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_when_filter' ) ); ?>" type="checkbox" value="1" <?php checked( $instance['show_when_filter'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_when_filter' ) ); ?>"><?php esc_html_e( 'Show "When" filter?', 'bookyourtravel'); ?></label>
		</p>
		
		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_price_filter' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_price_filter' ) ); ?>" type="checkbox" value="1" <?php checked( $instance['show_price_filter'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_price_filter' ) ); ?>"><?php esc_html_e( 'Show "Price" filter?', 'bookyourtravel'); ?></label>
		</p>
		
		<?php if ($this->enable_reviews) { ?>
		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_rating_filter' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_rating_filter' ) ); ?>" type="checkbox" value="1" <?php checked( $instance['show_rating_filter'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_rating_filter' ) ); ?>"><?php esc_html_e( 'Show "Rating" filter?', 'bookyourtravel'); ?></label>
		</p>
		<?php } ?>	


		<h4><?php esc_html_e('General field labels', 'bookyourtravel') ?></h4>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'what_text' ) ); ?>"><?php esc_html_e('What label:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'what_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'what_text' ) ); ?>" value="<?php echo esc_attr( $instance['what_text'] ); ?>" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'when_text' ) ); ?>"><?php esc_html_e('When label:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'when_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'when_text' ) ); ?>" value="<?php echo esc_attr( $instance['when_text'] ); ?>" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'where_text' ) ); ?>"><?php esc_html_e('Where label:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'where_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'where_text' ) ); ?>" value="<?php echo esc_attr( $instance['where_text'] ); ?>" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'location_select_label_text' ) ); ?>"><?php esc_html_e('Location label:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'location_select_label_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'location_select_label_text' ) ); ?>" value="<?php echo esc_attr( $instance['location_select_label_text'] ); ?>" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'price_per_person_label_text' ) ); ?>"><?php esc_html_e("Price per person label", 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'price_per_person_label_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'price_per_person_label_text' ) ); ?>" value="<?php echo esc_attr( $instance['price_per_person_label_text'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'price_per_night_label_text' ) ); ?>"><?php esc_html_e("Price per night label", 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'price_per_night_label_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'price_per_night_label_text' ) ); ?>" value="<?php echo esc_attr( $instance['price_per_night_label_text'] ); ?>" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'price_per_day_label_text' ) ); ?>"><?php esc_html_e("Price per day label", 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'price_per_day_label_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'price_per_day_label_text' ) ); ?>" value="<?php echo esc_attr( $instance['price_per_day_label_text'] ); ?>" />
		</p>

		<?php if ($this->enable_reviews) { ?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'user_rating_label_text' ) ); ?>"><?php esc_html_e("User rating label", 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'user_rating_label_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'user_rating_label_text' ) ); ?>" value="<?php echo esc_attr( $instance['user_rating_label_text'] ); ?>" />
		</p>
		<?php } ?>			
		
		<?php if ($this->enable_accommodations) { ?>
		
		<?php $accommodation_fields_visible = ($instance['make_accommodations_searchable']); ?>

		<p>
			<?php esc_html_e('For accommodations', 'bookyourtravel') ?>
		</p>
		
		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'make_accommodations_searchable' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'make_accommodations_searchable' ) ); ?>" type="checkbox" value="1" <?php checked( $instance['make_accommodations_searchable'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'make_accommodations_searchable' ) ); ?>"><?php esc_html_e( 'Make accommodations searchable?', 'bookyourtravel'); ?></label>
		</p>
		
		<p style="<?php echo $accommodation_fields_visible ? '' : 'display:none'; ?>">
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_accommodation_type_filter' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_accommodation_type_filter' ) ); ?>" type="checkbox" value="1" <?php checked( $instance['show_accommodation_type_filter'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_accommodation_type_filter' ) ); ?>"><?php esc_html_e( 'Show "Accommodation type" filter?', 'bookyourtravel'); ?></label>
		</p>
		
		<p style="<?php echo $accommodation_fields_visible ? '' : 'display:none'; ?>">
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_stars_filter' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_stars_filter' ) ); ?>" type="checkbox" value="1" <?php checked( $instance['show_stars_filter'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_stars_filter' ) ); ?>"><?php esc_html_e( 'Show "Stars" filter?', 'bookyourtravel'); ?></label>
		</p>
		
		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_room_count_filter' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_room_count_filter' ) ); ?>" type="checkbox" value="1" <?php checked( $instance['show_room_count_filter'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_room_count_filter' ) ); ?>"><?php esc_html_e( 'Show "Rooms" filter?', 'bookyourtravel'); ?></label>
		</p>		
		
		<p style="<?php echo $accommodation_fields_visible ? '' : 'display:none'; ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'accommodation_date_from_label_text' ) ); ?>"><?php esc_html_e('Accommodation date from label:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'accommodation_date_from_label_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'accommodation_date_from_label_text' ) ); ?>" value="<?php echo esc_attr( $instance['accommodation_date_from_label_text'] ); ?>" />
		</p>

		<p style="<?php echo $accommodation_fields_visible ? '' : 'display:none'; ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'accommodation_date_to_label_text' ) ); ?>"><?php esc_html_e('Accommodation date to label:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'accommodation_date_to_label_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'accommodation_date_to_label_text' ) ); ?>" value="<?php echo esc_attr( $instance['accommodation_date_to_label_text'] ); ?>" />
		</p>
		
		<p style="<?php echo $accommodation_fields_visible ? '' : 'display:none'; ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'accommodation_location_label_text' ) ); ?>"><?php esc_html_e('Accommodation location label:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'accommodation_location_label_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'accommodation_location_label_text' ) ); ?>" value="<?php echo esc_attr( $instance['accommodation_location_label_text'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'rooms_label_text' ) ); ?>"><?php esc_html_e('Rooms label:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'rooms_label_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'rooms_label_text' ) ); ?>" value="<?php echo esc_attr( $instance['rooms_label_text'] ); ?>" />
		</p>
		
		<p style="<?php echo $accommodation_fields_visible ? '' : 'display:none'; ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'accommodation_type_label_text' ) ); ?>"><?php esc_html_e("Accommodation type label", 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'accommodation_type_label_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'accommodation_type_label_text' ) ); ?>" value="<?php echo esc_attr( $instance['accommodation_type_label_text'] ); ?>" />
		</p>
		
		<p style="<?php echo $accommodation_fields_visible ? '' : 'display:none'; ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'star_rating_label_text' ) ); ?>"><?php esc_html_e("Star rating label", 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'star_rating_label_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'star_rating_label_text' ) ); ?>" value="<?php echo esc_attr( $instance['star_rating_label_text'] ); ?>" />
		</p>
		
		<?php } ?>
		
		<?php if ($this->enable_tours) { ?>
		
		<?php $tour_fields_visible = ($instance['make_tours_searchable']); ?>
		
		<p>
			<?php esc_html_e('For tours', 'bookyourtravel') ?>
		</p>

		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'make_tours_searchable' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'make_tours_searchable' ) ); ?>" type="checkbox" value="1" <?php checked( $instance['make_tours_searchable'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'make_tours_searchable' ) ); ?>"><?php esc_html_e( 'Make tours searchable?', 'bookyourtravel'); ?></label>
		</p>
		
		<p style="<?php echo $tour_fields_visible ? '' : 'display:none'; ?>">
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_tour_type_filter' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_tour_type_filter' ) ); ?>" type="checkbox" value="1" <?php checked( $instance['show_tour_type_filter'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_tour_type_filter' ) ); ?>"><?php esc_html_e( 'Show "Tour type" filter?', 'bookyourtravel'); ?></label>
		</p>
		
		<p style="<?php echo $tour_fields_visible ? '' : 'display:none'; ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'tour_date_from_label_text' ) ); ?>"><?php esc_html_e('Tour date from label:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'tour_date_from_label_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tour_date_from_label_text' ) ); ?>" value="<?php echo esc_attr( $instance['tour_date_from_label_text'] ); ?>" />
		</p>	
		
		<p style="<?php echo $tour_fields_visible ? '' : 'display:none'; ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'tour_type_label_text' ) ); ?>"><?php esc_html_e("Tour type label", 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'tour_type_label_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tour_type_label_text' ) ); ?>" value="<?php echo esc_attr( $instance['tour_type_label_text'] ); ?>" />
		</p>
		
		<p style="<?php echo $tour_fields_visible ? '' : 'display:none'; ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'tour_location_label_text' ) ); ?>"><?php esc_html_e('Tour location label:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'tour_location_label_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tour_location_label_text' ) ); ?>" value="<?php echo esc_attr( $instance['tour_location_label_text'] ); ?>" />
		</p>		
		
		<?php } ?>

		<?php if ($this->enable_car_rentals) { ?>
		
		<?php $car_rental_fields_visible = ($instance['make_car_rentals_searchable']); ?>

		<p>
			<?php esc_html_e('For car rentals', 'bookyourtravel') ?>
		</p>
		
		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'make_car_rentals_searchable' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'make_car_rentals_searchable' ) ); ?>" type="checkbox" value="1" <?php checked( $instance['make_car_rentals_searchable'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'make_car_rentals_searchable' ) ); ?>"><?php esc_html_e( 'Make car rentals searchable?', 'bookyourtravel'); ?></label>
		</p>
		
		<p style="<?php echo $car_rental_fields_visible ? '' : 'display:none'; ?>">
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_car_type_filter' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_car_type_filter' ) ); ?>" type="checkbox" value="1" <?php checked( $instance['show_car_type_filter'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_car_type_filter' ) ); ?>"><?php esc_html_e( 'Show "Car type" filter?', 'bookyourtravel'); ?></label>
		</p>
		
		<p style="<?php echo $car_rental_fields_visible ? '' : 'display:none'; ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'car_rental_date_from_label_text' ) ); ?>"><?php esc_html_e('Car rental date from label:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'car_rental_date_from_label_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'car_rental_date_from_label_text' ) ); ?>" value="<?php echo esc_attr( $instance['car_rental_date_from_label_text'] ); ?>" />
		</p>

		<p style="<?php echo $car_rental_fields_visible ? '' : 'display:none'; ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'car_rental_date_to_label_text' ) ); ?>"><?php esc_html_e('Car rental date to label:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'car_rental_date_to_label_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'car_rental_date_to_label_text' ) ); ?>" value="<?php echo esc_attr( $instance['car_rental_date_to_label_text'] ); ?>" />
		</p>	
		
		<p style="<?php echo $car_rental_fields_visible ? '' : 'display:none'; ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'car_rental_location_label_text' ) ); ?>"><?php esc_html_e('Car rental location label:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'car_rental_location_label_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'car_rental_location_label_text' ) ); ?>" value="<?php echo esc_attr( $instance['car_rental_location_label_text'] ); ?>" />
		</p>
		
		<p style="<?php echo $car_rental_fields_visible ? '' : 'display:none'; ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'car_type_label_text' ) ); ?>"><?php esc_html_e("Car type label", 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'car_type_label_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'car_type_label_text' ) ); ?>" value="<?php echo esc_attr( $instance['car_type_label_text'] ); ?>" />
		</p>		
		
		<?php } ?>
		
		<?php if ($this->enable_cruises) { ?>
		
		<?php $cruise_fields_visible = ($instance['make_cruises_searchable']); ?>
		
		<p>
			<?php esc_html_e('For cruises', 'bookyourtravel') ?>
		</p>

		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'make_cruises_searchable' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'make_cruises_searchable' ) ); ?>" type="checkbox" value="1" <?php checked( $instance['make_cruises_searchable'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'make_cruises_searchable' ) ); ?>"><?php esc_html_e( 'Make cruises searchable?', 'bookyourtravel'); ?></label>
		</p>
		
		<p style="<?php echo $cruise_fields_visible ? '' : 'display:none'; ?>">
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_cruise_type_filter' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_cruise_type_filter' ) ); ?>" type="checkbox" value="1" <?php checked( $instance['show_cruise_type_filter'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_cruise_type_filter' ) ); ?>"><?php esc_html_e( 'Show "Cruise type" filter?', 'bookyourtravel'); ?></label>
		</p>

		<p style="<?php echo $cruise_fields_visible ? '' : 'display:none'; ?>">
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_cabin_count_filter' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_cabin_count_filter' ) ); ?>" type="checkbox" value="1" <?php checked( $instance['show_cabin_count_filter'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_cabin_count_filter' ) ); ?>"><?php esc_html_e( 'Show "Cabin count" filter?', 'bookyourtravel'); ?></label>
		</p>
		
		<p style="<?php echo $cruise_fields_visible ? '' : 'display:none'; ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'cruise_date_from_label_text' ) ); ?>"><?php esc_html_e('Cruise date from label:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'cruise_date_from_label_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'cruise_date_from_label_text' ) ); ?>" value="<?php echo esc_attr( $instance['cruise_date_from_label_text'] ); ?>" />
		</p>	

		<p style="<?php echo $cruise_fields_visible ? '' : 'display:none'; ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'cabins_label_text' ) ); ?>"><?php esc_html_e('Cabins label:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'cabins_label_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'cabins_label_text' ) ); ?>" value="<?php echo esc_attr( $instance['cabins_label_text'] ); ?>" />
		</p>
		
		<p style="<?php echo $cruise_fields_visible ? '' : 'display:none'; ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'cruise_type_label_text' ) ); ?>"><?php esc_html_e('Cruise type label:', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'cruise_type_label_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'cruise_type_label_text' ) ); ?>" value="<?php echo esc_attr( $instance['cruise_type_label_text'] ); ?>" />
		</p>

		<?php } ?>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'submit_button_text' ) ); ?>"><?php esc_html_e('Search submit button text', 'bookyourtravel') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'submit_button_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'submit_button_text' ) ); ?>" value="<?php echo esc_attr( $instance['submit_button_text'] ); ?>" />
		</p>		
		
	<?php
	}
}