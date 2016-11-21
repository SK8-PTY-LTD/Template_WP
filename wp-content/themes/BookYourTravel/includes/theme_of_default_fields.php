<?php

class BookYourTravel_Theme_Of_Default_Fields extends BookYourTravel_BaseSingleton {

	protected function __construct() {
	
        // our parent class might contain shared code in its constructor
        parent::__construct();
		
    }
	
    public function init() {

	}
	
	public static function merge_fields_and_defaults($values, $default_values) {
	
		if (!is_array( $values ) || count($values) == 0) {
		
			return $default_values;
		
		} else {
	
			foreach ($default_values as $default_field_array) {
			
				$default_found = false;
				
				foreach ($values as $field_array) {
					if (isset($default_field_array['id']) && isset($field_array['id'])) {
						if ($default_field_array['id'] == $field_array['id']) {
							$default_found = true;
						}
					}
				}
				
				if (!$default_found) {
					$values[] = $default_field_array;
				}		
			}
			
			return $values;
		}
	}

	function get_default_tab_array($option_id) {

		global $default_accommodation_tabs, $default_tour_tabs, $default_car_rental_tabs, $default_location_tabs, $default_cruise_tabs;

		$tab_array = array();
		
		if ($option_id == 'accommodation_tabs') {
			$tab_array = $default_accommodation_tabs;
		} elseif ($option_id == 'tour_tabs') {
			$tab_array = $default_tour_tabs;
		} elseif ($option_id == 'car_rental_tabs') {
			$tab_array = $default_car_rental_tabs;
		} elseif ($option_id == 'location_tabs') {
			$tab_array = $default_location_tabs;
		} elseif ($option_id == 'cruise_tabs') {
			$tab_array = $default_cruise_tabs;
		}
		
		return $tab_array;
	}

	function get_default_review_fields_array($option_id) {
		
		global $default_accommodation_review_fields, $default_tour_review_fields, $default_cruise_review_fields, $default_car_rental_review_fields;
		
		$default_values = array();
		
		if ($option_id == 'accommodation_review_fields') {
			$default_values = $default_accommodation_review_fields;
		} elseif ($option_id == 'tour_review_fields') {
			$default_values = $default_tour_review_fields;
		} elseif ($option_id == 'cruise_review_fields') {
			$default_values = $default_cruise_review_fields;
		} elseif ($option_id == 'car_rental_review_fields') {
			$default_values = $default_car_rental_review_fields;
		}
		
		return $default_values;
	}
	
	function get_default_form_fields_array($option_id) {
		
		global 	$default_inquiry_form_fields, $default_booking_form_fields;
		
		$default_values = array();
		
		if ($option_id == 'inquiry_form_fields') {
			$default_values = $default_inquiry_form_fields;
		} else  if ($option_id == 'booking_form_fields') {
			$default_values = $default_booking_form_fields;
		}
		
		return $default_values;
	}
}

// store the instance in a variable to be retrieved later and call init
$bookyourtravel_theme_of_default_fields = BookYourTravel_Theme_Of_Default_Fields::get_instance();
$bookyourtravel_theme_of_default_fields->init();

global $repeatable_field_types;
$repeatable_field_types = array(
	'text' => esc_html__('Text', 'bookyourtravel'),
	'textarea' => esc_html__('Text area', 'bookyourtravel'),
	'image' => esc_html__('Image', 'bookyourtravel'),
);

$availability_label = esc_html__('Availability', 'bookyourtravel');
$things_to_do_label = esc_html__('Things to do', 'bookyourtravel');
$reviews_label = esc_html__('Reviews', 'bookyourtravel');
$location_label = esc_html__('Location', 'bookyourtravel');
$facilities_label = esc_html__('Facilities', 'bookyourtravel');
$description_label = esc_html__('Description', 'bookyourtravel');

// Accommodations
global $default_accommodation_tabs;
$default_accommodation_tabs = array(
	array('label' => $availability_label, 'id' => 'availability', 'hide' => 0),
	array('label' => $description_label, 'id' => 'description', 'hide' => 0),
	array('label' => $facilities_label, 'id' => 'facilities', 'hide' => 0),
	array('label' => $location_label, 'id' => 'location', 'hide' => 0),
	array('label' => $things_to_do_label, 'id' => 'things-to-do', 'hide' => 0),
	array('label' => $reviews_label, 'id' => 'reviews', 'hide' => 0)
);

$cancellation_prepayment_label = esc_html__('Cancellation / Prepayment', 'bookyourtravel');
$children_and_extra_beds_label = esc_html__('Children and extra beds', 'bookyourtravel');
$pets_label = esc_html__('Pets', 'bookyourtravel');
$accepted_credit_cards_label = esc_html__('Accepted credit cards', 'bookyourtravel');
$activities_label = esc_html__('Activities', 'bookyourtravel');
$internet_label = esc_html__('Internet', 'bookyourtravel');
$parking_label = esc_html__('Parking', 'bookyourtravel');
$check_in_time_label = esc_html__('Check-in time', 'bookyourtravel');
$check_out_time_label = esc_html__('Check-out time', 'bookyourtravel');

global $default_accommodation_extra_fields;
$default_accommodation_extra_fields = array(
	array('label' => $cancellation_prepayment_label, 'id' => 'cancellation_prepayment', 'type' => 'textarea', 'tab_id' => 'description', 'hide' => 0, 'index' => 0),
	array('label' => $children_and_extra_beds_label, 'id' => 'children_and_extra_beds', 'type' => 'textarea', 'tab_id' => 'description', 'hide' => 0, 'index' => 1),
	array('label' => $pets_label, 'id' => 'pets', 'type' => 'textarea', 'tab_id' => 'description', 'hide' => 0, 'index' => 2),
	array('label' => $accepted_credit_cards_label, 'id' => 'accepted_credit_cards', 'type' => 'textarea', 'tab_id' => 'description', 'hide' => 0, 'index' => 3),
	array('label' => $activities_label, 'id' => 'activities', 'type' => 'textarea', 'tab_id' => 'facilities', 'hide' => 0, 'index' => 4),
	array('label' => $internet_label, 'id' => 'internet', 'type' => 'textarea', 'tab_id' => 'facilities', 'hide' => 0, 'index' => 5),
	array('label' => $parking_label, 'id' => 'parking', 'type' => 'textarea', 'tab_id' => 'facilities', 'hide' => 0, 'index' => 6),
	array('label' => $check_in_time_label, 'id' => 'check_in_time', 'type' => 'text', 'tab_id' => 'description', 'hide' => 0, 'index' => 7),
	array('label' => $check_out_time_label, 'id' => 'check_out_time', 'type' => 'text', 'tab_id' => 'description', 'hide' => 0, 'index' => 8),
);

$description_label = esc_html__('Description', 'bookyourtravel');
$availability_label = esc_html__('Availability', 'bookyourtravel');
$location_label = esc_html__('Location', 'bookyourtravel');
$locations_label = esc_html__('Locations', 'bookyourtravel');
$map_label = esc_html__('Map', 'bookyourtravel');
$reviews_label = esc_html__('Reviews', 'bookyourtravel');

// Tours
global $default_tour_tabs;
$default_tour_tabs = array(
	array('label' => $description_label, 'id' => 'description', 'hide' => 0),
	array('label' => $availability_label, 'id' => 'availability', 'hide' => 0),
	array('label' => $map_label, 'id' => 'map', 'hide' => 0),
	array('label' => $locations_label, 'id' => 'locations', 'hide' => 0),
	array('label' => $reviews_label, 'id' => 'reviews', 'hide' => 0)
);

$activities_label = esc_html__('Activities', 'bookyourtravel');

global $default_tour_extra_fields;
$default_tour_extra_fields = array(
	array('label' => $activities_label, 'id' => 'activities', 'type' => 'textarea', 'tab_id' => 'description', 'hide' => 0),
);

// Car rentals
global $default_car_rental_tabs;
$default_car_rental_tabs = array(
	array('label' => $description_label, 'id' => 'description', 'hide' => 0),
	array('label' => $availability_label, 'id' => 'availability', 'hide' => 0),
);

$co2_emission_label = esc_html__('CO2 emission', 'bookyourtravel');

global $default_car_rental_extra_fields;
$default_car_rental_extra_fields = array(
	array('label' => $co2_emission_label, 'id' => 'co2_emission', 'type' => 'text', 'tab_id' => 'description', 'hide' => 0)
);

$general_info_label = esc_html__('General information', 'bookyourtravel');
$sports_and_nature_label = esc_html__('Sports &amp; nature', 'bookyourtravel');
$nightlife_label = esc_html__('Nightlife', 'bookyourtravel');
$culture_and_history_label = esc_html__('Culture and history', 'bookyourtravel');
$accommodations_label = esc_html__('Accommodations', 'bookyourtravel');
$tours_label = esc_html__('Tours', 'bookyourtravel');
$cruises_label = esc_html__('Cruises', 'bookyourtravel');
$car_rentals_label = esc_html__('Car rentals', 'bookyourtravel');

// Locations
global $default_location_tabs;
$default_location_tabs = array(
	array('label' => $general_info_label, 'id' => 'general_info', 'hide' => 0),
	array('label' => $sports_and_nature_label, 'id' => 'sports_and_nature', 'hide' => 0),
	array('label' => $nightlife_label, 'id' => 'nightlife', 'hide' => 0),
	array('label' => $culture_and_history_label, 'id' => 'culture', 'hide' => 0),
	array('label' => $accommodations_label, 'id' => 'accommodations', 'hide' => 0),
	array('label' => $tours_label, 'id' => 'tours', 'hide' => 0),
	array('label' => $cruises_label, 'id' => 'cruises', 'hide' => 0),
	array('label' => $car_rentals_label, 'id' => 'car_rentals', 'hide' => 0)
);

$sports_and_nature_label = esc_html__('Sports &amp; nature', 'bookyourtravel');
$sports_and_nature_image_label = esc_html__('Sports and nature image', 'bookyourtravel');
$nightlife_info_label = esc_html__('Nightlife info', 'bookyourtravel');
$nightlife_info_image_label = esc_html__('Nightlife image', 'bookyourtravel');
$culture_and_history_info_label = esc_html__('Culture and history info', 'bookyourtravel');
$culture_and_history_image_label = esc_html__('Culture and history image', 'bookyourtravel');
$visa_requirements_label = esc_html__('Visa requirements', 'bookyourtravel');
$languages_spoken_label = esc_html__('Languages spoken', 'bookyourtravel');
$currency_used_label = esc_html__('Currency used', 'bookyourtravel');
$area_label = esc_html__('Area (km2)', 'bookyourtravel');

global $default_location_extra_fields;
$default_location_extra_fields = array(
	array('label' => $sports_and_nature_label, 'id' => 'sports_and_nature', 'type' => 'textarea', 'tab_id' => 'sports_and_nature', 'hide' => 0),
	array('label' => $sports_and_nature_image_label, 'id' => 'sports_and_nature_image', 'type' => 'image', 'tab_id' => 'sports_and_nature', 'hide' => 0),
	array('label' => $nightlife_info_label, 'id' => 'nightlife', 'type' => 'textarea', 'tab_id' => 'nightlife', 'hide' => 0),
	array('label' => $nightlife_info_image_label, 'id' => 'nightlife_image', 'type' => 'image', 'tab_id' => 'nightlife', 'hide' => 0),
	array('label' => $culture_and_history_info_label, 'id' => 'culture_and_history', 'type' => 'textarea', 'tab_id' => 'culture', 'hide' => 0),
	array('label' => $culture_and_history_image_label, 'id' => 'culture_and_history_image', 'type' => 'image', 'tab_id' => 'culture', 'hide' => 0),
	array('label' => $visa_requirements_label, 'id' => 'visa_requirements', 'type' => 'textarea', 'tab_id' => 'general_info', 'hide' => 0),
	array('label' => $languages_spoken_label, 'id' => 'languages_spoken', 'type' => 'text', 'tab_id' => 'general_info', 'hide' => 0),
	array('label' => $currency_used_label, 'id' => 'currency', 'type' => 'text', 'tab_id' => 'general_info', 'hide' => 0),
	array('label' => $area_label, 'id' => 'area', 'type' => 'text', 'tab_id' => 'general_info', 'hide' => 0),
);

// Cruises
global $default_cruise_tabs;
$default_cruise_tabs = array(
	array('label' => $description_label, 'id' => 'description', 'hide' => 0),
	array('label' => $availability_label, 'id' => 'availability', 'hide' => 0),
	array('label' => $locations_label, 'id' => 'locations', 'hide' => 0),
	array('label' => $facilities_label, 'id' => 'facilities', 'hide' => 0),
	array('label' => $reviews_label, 'id' => 'reviews', 'hide' => 0)
);

$arrival_time_label = esc_html__('Arrival time', 'bookyourtravel');
$departure_time_label = esc_html__('Departure time', 'bookyourtravel');

global $default_cruise_extra_fields;
$default_cruise_extra_fields = array(
	array('label' => $cancellation_prepayment_label, 'id' => 'cancellation_prepayment', 'type' => 'textarea', 'tab_id' => 'description', 'hide' => 0),
	array('label' => $pets_label, 'id' => 'pets', 'type' => 'textarea', 'tab_id' => 'description', 'hide' => 0),
	array('label' => $accepted_credit_cards_label, 'id' => 'accepted_credit_cards', 'type' => 'textarea', 'tab_id' => 'description', 'hide' => 0),
	array('label' => $activities_label, 'id' => 'activities', 'type' => 'textarea', 'tab_id' => 'facilities', 'hide' => 0),
	array('label' => $internet_label, 'id' => 'internet', 'type' => 'textarea', 'tab_id' => 'facilities', 'hide' => 0),
);

$cleanliness_label = esc_html__('Cleanliness', 'bookyourtravel');
$comfort_label = esc_html__('Comfort', 'bookyourtravel');
$staff_label = esc_html__('Staff', 'bookyourtravel');
$services_label = esc_html__('Services', 'bookyourtravel');
$value_for_money_label = esc_html__('Value for money', 'bookyourtravel');
$sleep_quality_label = esc_html__('Sleep quality', 'bookyourtravel');

global $default_accommodation_review_fields;
$default_accommodation_review_fields = array(
	array('label' => $cleanliness_label, 'id' => 'review_cleanliness', 'post_type' => 'accommodation', 'hide' => 0),
	array('label' => $comfort_label, 'id' => 'review_comfort', 'post_type' => 'accommodation', 'hide' => 0),
	array('label' => $location_label, 'id' => 'review_location', 'post_type' => 'accommodation', 'hide' => 0),
	array('label' => $staff_label, 'id' => 'review_staff', 'post_type' => 'accommodation', 'hide' => 0),
	array('label' => $services_label, 'id' => 'review_services', 'post_type' => 'accommodation', 'hide' => 0),
	array('label' => $value_for_money_label, 'id' => 'review_value_for_money', 'post_type' => 'accommodation', 'hide' => 0),
	array('label' => $sleep_quality_label, 'id' => 'review_sleep_quality', 'post_type' => 'accommodation', 'hide' => 0),
);

$overall_label = esc_html__('Overall', 'bookyourtravel');
$accommodation_label = esc_html__('Accommodation', 'bookyourtravel');
$transport_label = esc_html__('Transport', 'bookyourtravel');
$meals_label = esc_html__('Meals', 'bookyourtravel');
$guide_label = esc_html__('Guide', 'bookyourtravel');
$program_accuracy_label = esc_html__('Program accuracy', 'bookyourtravel');

global $default_tour_review_fields;
$default_tour_review_fields = array(
	array('label' => $overall_label, 'id' => 'review_overall', 'post_type' => 'tour', 'hide' => 0),
	array('label' => $accommodation_label, 'id' => 'review_accommodation', 'post_type' => 'tour', 'hide' => 0),
	array('label' => $transport_label, 'id' => 'review_transport', 'post_type' => 'tour', 'hide' => 0),
	array('label' => $meals_label, 'id' => 'review_meals', 'post_type' => 'tour', 'hide' => 0),
	array('label' => $guide_label, 'id' => 'review_guide', 'post_type' => 'tour', 'hide' => 0),
	array('label' => $value_for_money_label, 'id' => 'review_value_for_money', 'post_type' => 'tour', 'hide' => 0),
	array('label' => $program_accuracy_label, 'id' => 'review_program_accuracy', 'post_type' => 'tour', 'hide' => 0),
);

$entertainment_label = esc_html__('Entertainment', 'bookyourtravel');

global $default_cruise_review_fields;
$default_cruise_review_fields = array(
	array('label' => $overall_label, 'id' => 'review_overall', 'post_type' => 'cruise', 'hide' => 0),
	array('label' => $accommodation_label, 'id' => 'review_accommodation', 'post_type' => 'cruise', 'hide' => 0),
	array('label' => $transport_label, 'id' => 'review_transport', 'post_type' => 'cruise', 'hide' => 0),
	array('label' => $meals_label, 'id' => 'review_meals', 'post_type' => 'cruise', 'hide' => 0),
	array('label' => $guide_label, 'id' => 'review_guide', 'post_type' => 'cruise', 'hide' => 0),
	array('label' => $value_for_money_label, 'id' => 'review_value_for_money', 'post_type' => 'cruise', 'hide' => 0),
	array('label' => $entertainment_label, 'id' => 'review_entertainment', 'post_type' => 'cruise', 'hide' => 0),
	array('label' => $program_accuracy_label, 'id' => 'review_program_accuracy', 'post_type' => 'cruise', 'hide' => 0),
);

$speed_label = esc_html__('Speed', 'bookyourtravel');
$punctuality_label = esc_html__('Punctuality', 'bookyourtravel');
$delivery_label = esc_html__('Delivery', 'bookyourtravel');
$customer_service_label = esc_html__('Customer service', 'bookyourtravel');

global $default_car_rental_review_fields;
$default_car_rental_review_fields = array(
	array('label' => $overall_label, 'id' => 'review_overall', 'post_type' => 'car_rental', 'hide' => 0),
	array('label' => $speed_label, 'id' => 'review_speed', 'post_type' => 'car_rental', 'hide' => 0),
	array('label' => $cleanliness_label, 'id' => 'review_cleanliness', 'post_type' => 'car_rental', 'hide' => 0),
	array('label' => $punctuality_label, 'id' => 'review_punctuality', 'post_type' => 'car_rental', 'hide' => 0),
	array('label' => $delivery_label, 'id' => 'review_delivery', 'post_type' => 'car_rental', 'hide' => 0),
	array('label' => $value_for_money_label, 'id' => 'review_value_for_money', 'post_type' => 'car_rental', 'hide' => 0),
	array('label' => $customer_service_label, 'id' => 'review_customer_service', 'post_type' => 'car_rental', 'hide' => 0),
);

global $form_field_types;
$form_field_types = array(
	'text' => esc_html__('Text', 'bookyourtravel'),
	'email' => esc_html__('Email', 'bookyourtravel'),
	'textarea' => esc_html__('Text area', 'bookyourtravel'),
);

$your_name_label = esc_html__('Your name', 'bookyourtravel');
$your_email_label = esc_html__('Your email', 'bookyourtravel');
$your_phone_label = esc_html__('Your phone', 'bookyourtravel');
$your_message_label = esc_html__('What would you like to inquire about?', 'bookyourtravel');

global 	$default_inquiry_form_fields; 
$default_inquiry_form_fields = array(
	array('label' => $your_name_label, 'id' => 'your_name', 'type' => 'text', 'hide' => 0, 'required' => 1),
	array('label' => $your_email_label, 'id' => 'your_email', 'type' => 'email', 'hide' => 0, 'required' => 1),
	array('label' => $your_phone_label, 'id' => 'your_phone', 'type' => 'text', 'hide' => 0, 'required' => 1),
	array('label' => $your_message_label, 'id' => 'your_message', 'type' => 'textarea', 'hide' => 0, 'required' => 1),
);

$first_name_label = esc_html__('First name', 'bookyourtravel');
$last_name_label = esc_html__('Last name', 'bookyourtravel');
$email_label = esc_html__('Email', 'bookyourtravel');
$phone_label = esc_html__('Phone', 'bookyourtravel');
$company_label = esc_html__('Company', 'bookyourtravel');
$address_label = esc_html__('Address', 'bookyourtravel');
$address_2_label = esc_html__('Address 2', 'bookyourtravel');
$city_label = esc_html__('City', 'bookyourtravel');
$postcode_label = esc_html__('Zip', 'bookyourtravel');
$country_label = esc_html__('Country', 'bookyourtravel');
$state_label = esc_html__('State', 'bookyourtravel');
$special_requirements_label = esc_html__('Special requirements', 'bookyourtravel');

global 	$default_booking_form_fields; 
$default_booking_form_fields = array(
	array('label' => $first_name_label, 'id' => 'first_name', 'type' => 'text', 'hide' => 0, 'required' => 1),
	array('label' => $last_name_label, 'id' => 'last_name', 'type' => 'text', 'hide' => 0, 'required' => 1),
	array('label' => $company_label, 'id' => 'company', 'type' => 'text', 'hide' => 0, 'required' => 0),
	array('label' => $email_label, 'id' => 'email', 'type' => 'email', 'hide' => 0, 'required' => 1),
	array('label' => $phone_label, 'id' => 'phone', 'type' => 'text', 'hide' => 0, 'required' => 0),
	array('label' => $address_label, 'id' => 'address', 'type' => 'text', 'hide' => 0, 'required' => 0),
	array('label' => $address_2_label, 'id' => 'address_2', 'type' => 'text', 'hide' => 0, 'required' => 0),
	array('label' => $city_label, 'id' => 'town', 'type' => 'text', 'hide' => 0, 'required' => 0),
	array('label' => $postcode_label, 'id' => 'zip', 'type' => 'text', 'hide' => 0, 'required' => 0),
	array('label' => $state_label, 'id' => 'state', 'type' => 'text', 'hide' => 0, 'required' => 0),
	array('label' => $country_label, 'id' => 'country', 'type' => 'text', 'hide' => 0, 'required' => 0),
	array('label' => $special_requirements_label, 'id' => 'special_requirements', 'type' => 'textarea', 'hide' => 0, 'required' => 0),
);