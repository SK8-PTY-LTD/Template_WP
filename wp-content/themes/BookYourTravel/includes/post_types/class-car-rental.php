<?php

class BookYourTravel_Car_Rental extends BookYourTravel_Entity
{
    public function __construct( $entity ) {
		parent::__construct( $entity, 'car_rental' );
    }
	
	public function get_price_per_day() {
		$price_per_day = $this->get_custom_field('price_per_day');
		return isset($price_per_day) ? $price_per_day : 0;
	}	
	
	public function get_location() {
		$location_id = $this->get_custom_field('location_post_id');
		return $location_id ? new BookYourTravel_Location(intval($location_id)) : '';
	}
	
    public function get_type_name() {	
		$type_objs = wp_get_post_terms( $this->get_id(), 'car_type', array( "fields" => "all" ) );
		return $type_objs ? $type_objs[0]->name : '';
    }
	
    public function get_type_id() {	
		$type_objs = wp_get_post_terms( $this->get_id(), 'car_type', array( "fields" => "all" ) );
		return $type_objs ? $type_objs[0]->term_id : null;
    }
		
	public function get_tags() {
		return wp_get_post_terms( $this->get_id(), 'car_rental_tag', array( "fields" => "all" ) );
	}
	
	public function get_tag_ids() {
		$tag_ids = array();
		$tags = wp_get_post_terms( $this->get_id(), 'car_rental_tag', array( "fields" => "all" ) );
		if (count($tags) > 0) {
			foreach ($tags as $tag) {
				$tag_ids[] = $tag->term_id;
			}
		}
		return $tag_ids;
	}
	
	public function get_is_reservation_only() {
		$is_reservation_only = $this->get_custom_field( 'is_reservation_only' );
		return isset($is_reservation_only) ? $is_reservation_only : 0;
	}
	
	public function get_field_value($field_name, $use_prefix = true) {
		if ( $field_name == 'car_rental_tag' ) {
			$tag_ids = array();
			$tags = $this->get_tags();
			if ($tags && count($tags) > 0) {
				for( $i = 0; $i < count($tags); $i++) {
					$tag = $tags[$i];
					$tag_ids[] = $tag->term_id;
				}
			}
			return $tag_ids;
		} elseif ( $field_name == 'car_type' )
			return $this->get_type_id();
		elseif ( $field_name == 'post_title' )
			return $this->post ? $this->post->post_title : '';
		elseif ( $field_name == 'post_content' )
			return $this->post ? $this->post->post_content : '';
		else
			return $this->get_custom_field($field_name, $use_prefix);			
	}
}