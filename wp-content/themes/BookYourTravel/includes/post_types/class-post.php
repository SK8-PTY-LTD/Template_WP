<?php

class BookYourTravel_Post extends BookYourTravel_Entity
{
    public function __construct( $entity ) {
		parent::__construct( $entity, 'post' );	
    }
	
	public function get_categories() {
		$category_objs = wp_get_post_categories( $this->get_base_id(), 'category', array( "fields" => "all" ) );
		return $category_objs;
	}
	
	public function get_tags() {
		$type_objs = wp_get_post_terms( $this->get_base_id(), 'tag', array( "fields" => "all" ) );
		return $type_objs;
	}
}