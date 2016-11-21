<?php

class BookYourTravel_Location extends BookYourTravel_Entity
{
	private $enable_accommodations;
	private $enable_cruises;
	private $enable_tours;
	private $enable_car_rentals;
	
    public function __construct( $entity ) {
		
		global $bookyourtravel_theme_globals;
		parent::__construct( $entity, 'location' );
		
		$this->enable_accommodations = $bookyourtravel_theme_globals->enable_accommodations();
		$this->enable_cruises = $bookyourtravel_theme_globals->enable_cruises();
		$this->enable_tours = $bookyourtravel_theme_globals->enable_tours();
		$this->enable_car_rentals = $bookyourtravel_theme_globals->enable_car_rentals();
    }
	
	public function get_accommodation_count() {
		
		$count = -1;

		if ($this->enable_accommodations) {
			global $bookyourtravel_accommodation_helper;
			
			if ($this->is_custom_field_set('_location_accommodation_count', false)) {
				$count = $this->get_custom_field('_location_accommodation_count', false);
			}
			
			if ($count < 0) {
				$count = (int)$bookyourtravel_accommodation_helper->list_accommodations_count ( 0, -1, 'post_title', 'ASC', $this->get_id(), array(), array(), array(), false);
				update_post_meta($this->get_id(), '_location_accommodation_count', $count);
			}
		}

		return $count;
	}
	
	public function get_tour_count() {
		
		$count = -1;
		
		if ($this->enable_tours) {
			global $bookyourtravel_tour_helper;
			
			if ($this->is_custom_field_set('_location_tour_count', false)) {
				$count = $this->get_custom_field('_location_tour_count', false);
			}
			
			if ($count < 0) {
				$count = (int)$bookyourtravel_tour_helper->list_tours_count ( 0, -1, 'post_title', 'ASC', $this->get_id());
				update_post_meta($this->get_id(), '_location_tour_count', $count);
			}
		}
		
		return $count;
	}
	
	public function get_cruise_count() {

		$count = -1;
		
		if ($this->enable_cruises) {
			global $bookyourtravel_cruise_helper;
			
			if ($this->is_custom_field_set('_location_cruise_count', false)) {
				$count = $this->get_custom_field('_location_cruise_count', false);
			}
			
			if ($count < 0) {
				$count = $bookyourtravel_cruise_helper->list_cruises_count ( 0, -1, 'post_title', 'ASC', $this->get_id());
				update_post_meta($this->get_id(), '_location_cruise_count', $count);
			}
		}

		return $count;
	}
	
	public function get_car_rental_count() {
		
		$count = -1;
		
		if ($this->enable_car_rentals) {
			global $bookyourtravel_car_rental_helper;
			
			if ($this->is_custom_field_set('_location_car_rental_count', false)) {
				$count = $this->get_custom_field('_location_car_rental_count', false);
			}
			
			if ($count < 0) {
				$count = $bookyourtravel_car_rental_helper->list_car_rentals_count ( 0, -1, 'post_title', 'ASC', $this->get_id());
				update_post_meta($this->get_id(), '_location_car_rental_count', $count);
			}
		}

		return $count;
	}
}