<?php

class BookYourTravel_Cruise extends BookYourTravel_Entity
{
    public function __construct( $entity ) {
		parent::__construct( $entity, 'cruise' );	
    }
	
    public function get_type_name() {	
		$type_objs = wp_get_post_terms( $this->get_id(), 'cruise_type', array( "fields" => "all" ) );
		return $type_objs ? $type_objs[0]->name : '';
    }
	
	public function get_is_reservation_only() {
		$is_reservation_only = $this->get_custom_field( 'is_reservation_only' );
		return isset($is_reservation_only) ? $is_reservation_only : 0;
	}
	
    public function get_type_id() {	
		$type_objs = wp_get_post_terms( $this->get_id(), 'cruise_type', array( "fields" => "all" ) );
		return $type_objs ? $type_objs[0]->term_id : null;
    }	

	public function get_locations() {
		$location_ids = $this->get_custom_field( 'locations', false );
		return unserialize($location_ids);
	}	
	
	public function get_type_is_repeated() {
		$type_id = $this->get_type_id();
		$term_meta = get_option( "taxonomy_$type_id" );
		return (int)$term_meta['cruise_type_is_repeated'];
	}
		
	public function get_type_day_of_week_day() {
	
		$day_of_week_indexes = $this->get_type_day_of_week_indexes();
		$days_of_week = BookYourTravel_Theme_Utils::get_days_of_week();
		if (count($day_of_week_indexes) > 0)
			return $days_of_week[$day_of_week_indexes[0]];
		return '';
	}
	
	public function get_is_price_per_person() {
		return $this->get_custom_field( 'is_price_per_person' );
	}
	
	public function get_count_children_stay_free() {
		$count_children_stay_free = $this->get_custom_field( 'count_children_stay_free' );
		return isset($count_children_stay_free) ? $count_children_stay_free : 0;
	}

	public function get_cabin_types() {
		$cabin_type_ids = $this->get_custom_field( 'cabin_types', false );
		return unserialize($cabin_type_ids);
	}
	
	public function get_facilities() {
		return wp_get_post_terms($this->get_id(), 'facility', array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'all'));	
	}
	
	public function get_type_day_of_week_indexes() {
		
		$indexes = array();
		$type_is_repeated = $this->get_type_is_repeated();
		
		if ($type_is_repeated == 3) {
		
			$type_id = $this->get_type_id();
			$term_meta = get_option( "taxonomy_$type_id" );
			$indexes[] = $term_meta['cruise_type_day_of_week'];
			
		} elseif ($type_is_repeated == 4) {
			$type_id = $this->get_type_id();
			$term_meta = get_option( "taxonomy_$type_id" );
			$indexes = (array)$term_meta['cruise_type_days_of_week'];
		}
		
		return $indexes;
	}
	
	public function get_tags() {
		return wp_get_post_terms( $this->get_id(), 'cruise_tag', array( "fields" => "all" ) );
	}
	
	public function get_tag_ids() {
		$tag_ids = array();
		$tags = wp_get_post_terms( $this->get_id(), 'cruise_tag', array( "fields" => "all" ) );
		if (count($tags) > 0) {
			foreach ($tags as $tag) {
				$tag_ids[] = $tag->term_id;
			}
		}
		return $tag_ids;
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
		} else if ( $field_name == 'cruise_tag' ) {
			$tag_ids = array();
			$tags = $this->get_tags();
			if ($tags && count($tags) > 0) {
				for( $i = 0; $i < count($tags); $i++) {
					$tag = $tags[$i];
					$tag_ids[] = $tag->term_id;
				}
			}
			return $tag_ids;
		} elseif ( $field_name == 'cruise_type' )
			return $this->get_type_id();
		elseif ( $field_name == 'cabin_types' )
			return $this->get_cabin_types();
		elseif ( $field_name == 'locations' )
			return $this->get_locations();
		elseif ( $field_name == 'post_title' )
			return $this->post ? $this->post->post_title : '';
		elseif ( $field_name == 'post_content' )
			return $this->post ? $this->post->post_content : '';
		else
			return $this->get_custom_field($field_name, $use_prefix);			
	}
}