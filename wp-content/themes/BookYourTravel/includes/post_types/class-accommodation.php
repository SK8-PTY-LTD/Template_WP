<?php

class BookYourTravel_Accommodation extends BookYourTravel_Entity
{
    public function __construct( $entity ) {
		parent::__construct( $entity, 'accommodation' );
    }
	
	public function get_rent_type() {
		$rent_type = $this->get_custom_field( 'rent_type' );
		return isset($rent_type) ? $rent_type : 0;	
	}
	
	public function get_location() {
		$location_id = $this->get_custom_field('location_post_id');
		return $location_id ? new BookYourTravel_Location(intval($location_id)) : '';
	}
	
    public function get_type_name() {	
		$type_objs = wp_get_post_terms( $this->get_id(), 'accommodation_type', array( "fields" => "all" ) );
		return $type_objs ? $type_objs[0]->name : '';
    }
	
    public function get_type_id() {	
		$type_objs = wp_get_post_terms( $this->get_id(), 'accommodation_type', array( "fields" => "all" ) );
		return $type_objs ? $type_objs[0]->term_id : null;
    }
	
	public function get_is_price_per_person() {
		$is_price_per_person = $this->get_custom_field( 'is_price_per_person' );
		return isset($is_price_per_person) ? $is_price_per_person : 0;
	}
	
	public function get_checkin_week_day() {
		$checkin_week_day = $this->get_custom_field( 'checkin_week_day' );
		return isset($checkin_week_day) && !empty($checkin_week_day) ? $checkin_week_day : -1;
	}
	
	public function get_checkout_week_day() {
		$checkout_week_day = $this->get_custom_field( 'checkout_week_day' );
		return isset($checkout_week_day) && !empty($checkout_week_day) ? $checkout_week_day : -1;
	}
	
	public function get_min_days_stay() {
		$min_days_stay = $this->get_custom_field( 'min_days_stay' );
		return isset($min_days_stay) && $min_days_stay > 1 ? $min_days_stay : 1;
	}
	
	public function get_max_days_stay() {
		$max_days_stay = $this->get_custom_field( 'max_days_stay' );
		return isset($max_days_stay) && $max_days_stay > 0 ? $max_days_stay : 0;
	}

	public function get_min_adult_count() {
		$min_adult_count = $this->get_custom_field( 'min_count' );
		return isset($min_adult_count) && $min_adult_count > 1 ? $min_adult_count : 1;
	}
	
	public function get_max_adult_count() {
		$max_adult_count = $this->get_custom_field( 'max_count' );
		return isset($max_adult_count) && $max_adult_count > 0 ? $max_adult_count : 0;
	}
	
	public function get_min_child_count() {
		$min_child_count = $this->get_custom_field( 'min_child_count' );
		return isset($min_child_count) && $min_child_count > 0 ? $min_child_count : 0;
	}
	
	public function get_max_child_count() {
		$max_child_count = $this->get_custom_field( 'max_child_count' );
		return isset($max_child_count) && $max_child_count > 0 ? $max_child_count : 0;
	}
	
	public function get_disabled_room_types() {
		$disabled_room_types = $this->get_custom_field( 'disabled_room_types' );
		return isset($disabled_room_types) ? $disabled_room_types : 0;
	}
	
	public function get_is_reservation_only() {
		$is_reservation_only = $this->get_custom_field( 'is_reservation_only' );
		return isset($is_reservation_only) ? $is_reservation_only : 0;
	}
	
	public function get_count_children_stay_free() {
		$count_children_stay_free = $this->get_custom_field( 'count_children_stay_free' );
		return isset($count_children_stay_free) ? $count_children_stay_free : 0;
	}

	public function get_room_types() {
		$room_type_ids = $this->get_custom_field( 'room_types', false );
		return unserialize($room_type_ids);
	}
	
	public function get_tags() {
		return wp_get_post_terms( $this->get_id(), 'acc_tag', array( "fields" => "all" ) );
	}
	
	public function get_tag_ids() {
		$tag_ids = array();
		$tags = wp_get_post_terms( $this->get_id(), 'acc_tag', array( "fields" => "all" ) );
		if (count($tags) > 0) {
			foreach ($tags as $tag) {
				$tag_ids[] = $tag->term_id;
			}
		}
		return $tag_ids;
	}
	
	public function get_facilities() {
		return wp_get_post_terms($this->get_id(), 'facility', array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'all'));	
	}
	
	public function get_field_value($field_name, $use_prefix = true) {
		if ( $field_name == 'facilities' ) {
			$facility_ids = array();
			$facilities = $this->get_facilities();
			if ($facilities && count($facilities) > 0) {
				for( $i = 0; $i < count($facilities); $i++) {
					$facility = $facilities[$i];
					$facility_ids[] = $facility->term_id;
				}
			}
			return $facility_ids;
		} else if ( $field_name == 'acc_tag' ) {
			$tag_ids = array();
			$tags = $this->get_tags();
			if ($tags && count($tags) > 0) {
				for( $i = 0; $i < count($tags); $i++) {
					$tag = $tags[$i];
					$tag_ids[] = $tag->term_id;
				}
			}
			return $tag_ids;
		} elseif ( $field_name == 'accommodation_type' )
			return $this->get_type_id();
		elseif ( $field_name == 'room_types' )
			return $this->get_room_types();
		elseif ( $field_name == 'post_title' )
			return $this->post ? $this->post->post_title : '';
		elseif ( $field_name == 'post_content' )
			return $this->post ? $this->post->post_content : '';
		else
			return $this->get_custom_field($field_name, $use_prefix);			
	}

}