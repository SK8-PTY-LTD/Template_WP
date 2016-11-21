<?php
/*
Class Name: Frontend Submit based on Frontend Uploader plugin
Description: Allow your visitors to upload content and moderate it.
Author: Rinat Khaziev, Daniel Bachhuber, ThemeEnergy.com
Version of Frontend Uploader: 0.8.1
Author of original plugin class URI: http://digitallyconscious.com
Author of modification: http://www.themeenergy.com

GNU General Public License, Free Software Foundation <http://creativecommons.org/licenses/GPL/2.0/>

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

// adding dropzone image handling
// http://wordpress.stackexchange.com/questions/147474/dropzone-js-and-wordpress-plugin

define( 'FES_ROOT' , dirname( __FILE__ ) );
define( 'FES_FILE_PATH' , FES_ROOT . '/' . basename( __FILE__ ) );
define( 'FES_URL' , plugins_url( '/', __FILE__ ) );

require_once FES_ROOT . '/class-html-helper.php';

class Frontend_Submit {

	protected $allowed_mime_types;
	protected $has_correct_role;
	protected $html_helper;
	protected $form_fields;
	protected $entry = null;
	protected $entry_id = 0;
	protected $content_type = '';
	protected $date_format;
	
	function __construct() {

		$this->allowed_mime_types = function_exists( 'wp_get_mime_types' ) ? wp_get_mime_types() : get_allowed_mime_types();
		$this->has_correct_role = BookYourTravel_Theme_Utils::check_user_role(BOOKYOURTRAVEL_FRONTEND_SUBMIT_ROLE, $this->get_current_user_id());
		$this->html_helper = new Html_Helper();
		$this->date_format = get_option('date_format');		
	}
			
	function init() {
	
		add_action( 'wp_ajax_frontend_submit', array( $this, 'upload_content' ) );
		add_action( 'wp_ajax_nopriv_frontend_submit', array( $this, 'upload_content' ) );
		add_action( 'wp_ajax_frontend_featured_upload', array( $this, 'upload_featured_image' ) );		
		add_action( 'wp_ajax_nopriv_frontend_featured_upload', array( $this, 'upload_featured_image' ) );
		add_action( 'wp_ajax_frontend_gallery_upload', array( $this, 'upload_gallery_images' ) );		
		add_action( 'wp_ajax_nopriv_frontend_gallery_upload', array( $this, 'upload_gallery_images' ) );
		add_action( 'wp_ajax_frontend_delete_featured_image', array( $this, 'delete_featured_image' ) );
		add_action( 'wp_ajax_nopriv_frontend_delete_featured_image', array( $this, 'delete_featured_image' ) );
		add_action( 'wp_ajax_frontend_delete_gallery_image', array( $this, 'delete_gallery_image' ) );
		add_action( 'wp_ajax_nopriv_frontend_delete_gallery_image', array( $this, 'delete_gallery_image' ) );
	}
	
	function get_author_id_for_list() {
	
		global $current_user;
		if (!isset($current_user)) {
			$current_user = wp_get_current_user();
		}
		
		return is_super_admin() ? null : $current_user->ID;
	}	
	
	function get_current_user_id() {
	
		global $current_user;
		if (!isset($current_user)) {
			$current_user = wp_get_current_user();
		}
		
		return $current_user->ID;		
	}
	
	public function user_has_correct_role() {
		return $this->has_correct_role || is_super_admin();
	}
	
	public function prepare_form($content_type = 'accommodation') {

		global $bookyourtravel_accommodation_helper;
		
		$this->content_type = $content_type;
	
		$this->entry_id = 0;
		if (isset($_GET['fesid'])) {
			$this->entry_id = intval(wp_kses($_GET['fesid'], array()));
		} else if (isset($_POST['entry_id'])) {
			$this->entry_id = intval(wp_kses($_POST['entry_id'], array()));
		}
		
		$this->initialize_entry();
	}
	
	function initialize_entry() {

		if ($this->entry_id > 0) {
		
			global $bookyourtravel_cruise_helper, $bookyourtravel_car_rental_helper, $bookyourtravel_accommodation_helper, $bookyourtravel_tour_helper, $bookyourtravel_room_type_helper;
		
			if ($this->content_type == 'accommodation' || 
				$this->content_type == 'room_type' || 
				$this->content_type == 'tour' || 
				$this->content_type == 'cruise' || 
				$this->content_type == 'cabin_type' || 
				$this->content_type == 'car_rental'	) {
			
				if ($this->content_type == 'accommodation') {
				
					$this->entry = new BookYourTravel_Accommodation($this->entry_id);
				} else if ($this->content_type == 'room_type') {

					$this->entry = new BookYourTravel_Room_Type($this->entry_id);
				} else if ($this->content_type == 'tour') {

					$this->entry = new BookYourTravel_Tour($this->entry_id);
				} else if ($this->content_type == 'cruise') {

					$this->entry = new BookYourTravel_Cruise($this->entry_id);
				} else if ($this->content_type == 'cabin_type') {

					$this->entry = new BookYourTravel_Cabin_Type($this->entry_id);
				} else if ($this->content_type == 'car_rental') {

					$this->entry = new BookYourTravel_Car_Rental($this->entry_id);
				}

				if (($this->entry->get_post_author() != $this->get_current_user_id() && !is_super_admin()) || $this->entry->get_post_type() != $this->content_type) {
				
					$this->entry_id = 0;
					$this->entry = null;
				}
				
			} else if ($this->content_type == 'vacancy') {

				$this->entry = $bookyourtravel_accommodation_helper->get_accommodation_vacancy($this->entry_id);

			} else if ($this->content_type == 'accommodation_booking') {

				$this->entry = $bookyourtravel_accommodation_helper->get_accommodation_booking($this->entry_id);				
			} else if ($this->content_type == 'tour_schedule') {

				$this->entry = $bookyourtravel_tour_helper->get_tour_schedule($this->entry_id);
			} else if ($this->content_type == 'tour_booking') {

				$this->entry = $bookyourtravel_tour_helper->get_tour_booking($this->entry_id);
			} else if ($this->content_type == 'cruise_schedule') {

				$this->entry = $bookyourtravel_cruise_helper->get_cruise_schedule($this->entry_id);
			} else if ($this->content_type == 'cruise_booking') {

				$this->entry = $bookyourtravel_cruise_helper->get_cruise_booking($this->entry_id);				
			}
		} else {
			$this->entry = null;
		}
	}

	function is_public() {
		return of_get_option('publish_frontend_submissions_immediately') && $this->user_has_correct_role();
	}
	
	function is_demo() {
		return defined('BookYourTravel_DEMO');
	}
	
	/**
	 * Handle uploading of the files
	 *
	 * @param int     $post_id Parent post id
	 * @return array Combined result of media ids and errors if any
	 */
	function upload_files( $post_id, $set_as_featured ) {
		
		$media_ids = $errors = array();
		
		// Bail if there are no files
		if ( empty( $_FILES ) )
			return false;

		$files = $_FILES;

		foreach ($files as $file) {
			
			$fields = array( 'name', 'type', 'tmp_name', 'error', 'size' );

			$k = array();
			
			foreach ( $fields as $field ) {
				$k[$field] = $file[$field];
			}

			$k['name'] = sanitize_file_name( $k['name'] );

			// Skip to the next file if upload went wrong
			if ( $k['tmp_name'] == "" ) {
				continue;
			}

			$type_check = wp_check_filetype_and_ext( $k['tmp_name'], $k['name'], false );
			
			// Add an error message if MIME-type is not allowed
			if ( ! in_array( $type_check['type'], (array) $this->allowed_mime_types ) ) {
				$errors['fes-disallowed-mime-type'][] = array( 'name' => $k['name'], 'mime' => $k['type'] );
				continue;
			}

			// Setup some default values, however you can make additional changes on 'fes_after_upload' action
			$caption = '';
			$file_name = pathinfo( $k['name'], PATHINFO_FILENAME );
			
			$post_overrides = array(
				'post_status' => $this->is_public() ? 'publish' : 'draft',
				'post_title' => sanitize_text_field( $file_name ),
				'post_content' => empty( $caption ) ? esc_html__( 'Unnamed', 'bookyourtravel' ) : $caption,
				'post_excerpt' => empty( $caption ) ? esc_html__( 'Unnamed', 'bookyourtravel' ) : $caption,
			);

			// Trying to upload the file
			$upload_id = media_handle_sideload( $k, (int) $post_id, $post_overrides['post_title'], $post_overrides );
			
			if ( !is_wp_error( $upload_id ) ) {
				if ($set_as_featured) {
					set_post_thumbnail($post_id, $upload_id);
				}
				$media_ids[] = $upload_id;
			} else {
				$errors['fes-error-media'][] = $k['name'];
			}
		}

		// $success determines the rest of upload flow. Setting this to true if no errors were produced even if there's was no files to upload
		$success = empty( $errors ) ? true : false;

		// Allow additional setup. Pass array of attachment ids.
		do_action( 'fes_after_upload', $media_ids, $success, $post_id );

		return array( 'success' => $success, 'media_ids' => $media_ids, 'errors' => $errors );
	}
	
	private function save_post_meta_fields( $post_id, $existing = false ) {

		// Post ID not set, bailing
		if ( !$post_id = (int) $post_id )
			return false;
			
		// No meta fields in field mapping, bailing
		if ( !isset( $this->form_fields ) || empty( $this->form_fields ) )
			return false;
			
		foreach ( $this->form_fields as $extra_field ) {
			
			$extra_field_name = $extra_field->name;
			
			if ($extra_field->type == 'file' ) {
				
			} else {
				
				if ( $extra_field->type != 'checkbox' && !isset( $_REQUEST[$extra_field_name] ) )
					continue;
					
				$value = isset($_REQUEST[$extra_field_name]) ? $_REQUEST[$extra_field_name] : '';
				
				if ( $extra_field_name == 'facilities' ) {
				
					$term_ids = array();
					foreach ($value as $term_id) {
						$term_ids[] = intval($term_id);
					}
					
					wp_set_post_terms( $post_id, $term_ids, 'facility');
				
				} else if ( $extra_field_name == 'locations' ) {
				
					$value = array_map( array( $this, 'sanitize_array_element_callback' ), $value );
					
					if ( !$existing ) {
						add_post_meta( $post_id, $extra_field_name, $value, true );
					} else {
						update_post_meta( $post_id, $extra_field_name, $value );
					}
				
				} else if ( $extra_field_name == 'acc_tag' ) {
				
					$term_ids = array();
					foreach ($value as $term_id) {
						$term_ids[] = intval($term_id);
					}
					
					wp_set_post_terms( $post_id, $term_ids, 'acc_tag');
					
				} else if ( $extra_field_name == 'tour_tag' ) {
				
					$term_ids = array();
					foreach ($value as $term_id) {
						$term_ids[] = intval($term_id);
					}
					
					wp_set_post_terms( $post_id, $term_ids, 'tour_tag');
					
				} else if ( $extra_field_name == 'cruise_tag' ) {
				
					$term_ids = array();
					foreach ($value as $term_id) {
						$term_ids[] = intval($term_id);
					}
					
					wp_set_post_terms( $post_id, $term_ids, 'cruise_tag');
					
				} else if ( $extra_field_name == 'car_rental_tag' ) {
				
					$term_ids = array();
					foreach ($value as $term_id) {
						$term_ids[] = intval($term_id);
					}
					
					wp_set_post_terms( $post_id, $term_ids, 'car_rental_tag');
					
				} elseif ($extra_field_name == 'accommodation_type') {	
				
					wp_set_post_terms( $post_id, array(intval($value)), 'accommodation_type');
					
				} elseif ($extra_field_name == 'tour_type') {	
				
					wp_set_post_terms( $post_id, array(intval($value)), 'tour_type');
					
				} elseif ($extra_field_name == 'cruise_type') {	
				
					wp_set_post_terms( $post_id, array(intval($value)), 'cruise_type');

				} elseif ($extra_field_name == 'car_type') {	
				
					wp_set_post_terms( $post_id, array(intval($value)), 'car_type');
					
				} else if ($extra_field_name == 'tour_map_code') {
					
					$allowed = array(
						'iframe' => array(
							'src' => array(),
							'width' => array(),
							'height' => array(),
							'frameborder' => array()
						),
					);
					
					$value = wp_kses( $value, $allowed );
					
					if ( !$existing ) {
						add_post_meta( $post_id, $extra_field_name, $value, true );
					} else {
						update_post_meta( $post_id, $extra_field_name, $value );
					}
					
				} else {

					// Sanitize array
					if ( $extra_field->type == 'checkbox' && isset($extra_field->class) && $extra_field->class != 'checkboxes') {
						$value = intval($value);
					} elseif ( is_array( $value ) ) {
						// Sanitize everything else
						$value = array_map( array( $this, 'sanitize_array_element_callback' ), $value );
					} else {
						global $allowedtags;
						$value = wp_kses( $value, $allowedtags );
					}
					
					if ( !$existing ) {
						add_post_meta( $post_id, $extra_field_name, $value, true );
					} else {
						update_post_meta( $post_id, $extra_field_name, $value );
					}
				}
			}
		}
	}
	
	function sanitize_array_element_callback( $el ) {
		return sanitize_text_field( $el );
	}
	
	/**
	 * Handle post uploads
	 */
	function upload_entry() {
	
		global $bookyourtravel_accommodation_helper, $bookyourtravel_tour_helper, $bookyourtravel_cruise_helper;
	
		$errors = array();
		$success = true;
		
		if ($this->content_type == 'accommodation' || 
			$this->content_type == 'room_type' ||
			$this->content_type == 'tour' ||
			$this->content_type == 'cruise' ||
			$this->content_type == 'cabin_type' ||
			$this->content_type == 'car_rental' ) {

			$post_type = $this->content_type;
			
			$this->entry_id = 0;
			$existing_post = null;
			
			$allowed_tags = BookYourTravel_Theme_Utils::get_allowed_content_tags_array();
			
			if ( isset($_POST['entry_id']) ) {
			
				$this->entry_id = intval(wp_kses($_POST['entry_id'], array()));
				
				$this->initialize_entry();
				
				if ($this->entry) {
				
					$this->entry->post->post_content = isset($_POST['post_content']) ? wp_kses(wp_unslash($_POST['post_content']), $allowed_tags) : '';
					$this->entry->post->post_title = isset($_POST['post_title']) ? sanitize_text_field(wp_unslash( $_POST['post_title'] )) : '';
					$this->entry->post->post_status = $this->is_public() ? 'publish' : 'draft';
					
					$this->entry_id = wp_update_post($this->entry->post, true);
				}
			}

			if ( $this->entry_id == 0 ) {
				
				// Construct post array;
				$post_array = array(
					'post_type' =>  $post_type,
					'post_title'    => sanitize_text_field( wp_unslash( $_POST['post_title'] ) ),
					'post_content'  => (isset($_POST['post_content']) ? wp_kses(wp_unslash($_POST['post_content']), $allowed_tags) : ''),
					'post_status'   => $this->is_public() ? 'publish' : 'draft',
				);

				$author = isset( $_POST['post_author'] ) ? sanitize_text_field( $_POST['post_author'] ) : '';
				$users = get_users( array(
					'search' => $author,
					'fields' => 'ID'
				) );

				if ( isset( $users[0] ) ) {
					$post_array['post_author'] = (int) $users[0];
				}

				$post_array = apply_filters( 'fes_before_create_post', $post_array );
					
				$this->entry_id = wp_insert_post( wp_slash($post_array), true );
				
				// If the author name is not in registered users. Save the author name if it was filled and post was created successfully.
				if ( $author ) {
					add_post_meta( $this->entry_id, 'author_name', $author );
				}				
			}
			
			// Something went wrong
			if ( is_wp_error( $this->entry_id ) ) {
				$errors[] = 'fes-error-post';
				$success = false;
			} else {
				do_action( 'fes_after_create_post', $this->entry_id );

				$existing = (isset($this->entry) && isset($this->entry->post));
				$this->save_post_meta_fields( $this->entry_id, $existing);
			}
		} elseif ($this->content_type == 'vacancy') {
		
			$author = isset( $_POST['post_author'] ) ? sanitize_text_field( $_POST['post_author'] ) : '';
			$users = get_users( array(
				'search' => $author,
				'fields' => 'ID'
			) );
			
			$user_id = (int) $users[0];
				
			$accommodation_id = isset( $_POST['accommodation_id'] ) ? intval( $_POST['accommodation_id'] ) : 0;
			
			if ( $accommodation_id > 0 ) {
				
				$accommodation = get_post($accommodation_id);
				
				if ( $accommodation ) {
					if ( $accommodation->post_author == $user_id || is_super_admin() ) {					
						
						$accommodation_obj = new BookYourTravel_Accommodation($accommodation_id);
						
						$start_date = isset( $_POST['start_date'] ) ? sanitize_text_field( $_POST['start_date'] ) : '';
						$end_date = isset( $_POST['end_date'] ) ? sanitize_text_field( $_POST['end_date'] ) : '';
						
						$start_date = date('Y-m-d', strtotime($start_date));
						$end_date = date('Y-m-d', strtotime($end_date));
						
						$room_type_id = isset( $_POST['room_type_id'] ) ? intval( $_POST['room_type_id'] ) : 0;
						$room_count = isset( $_POST['room_count'] ) ? intval( $_POST['room_count'] ) : '';
						$room_count = $room_count > 0 ? $room_count : 1;
						
						$price_per_day = isset( $_POST['price_per_day'] ) ?  sanitize_text_field ( $_POST['price_per_day'] ) : 0;
						$price_per_day_child = isset( $_POST['price_per_day_child'] ) ? sanitize_text_field( $_POST['price_per_day_child'] ) : null;
						$weekend_price_per_day = isset( $_POST['weekend_price_per_day'] ) ?  sanitize_text_field ( $_POST['weekend_price_per_day'] ) : 0;
						$weekend_price_per_day_child = isset( $_POST['weekend_price_per_day_child'] ) ? sanitize_text_field( $_POST['weekend_price_per_day_child'] ) : null;
						
						$season_name = isset( $_POST['season_name'] ) ?  sanitize_text_field ( $_POST['season_name'] ) : '';
						
						$this->entry_id = 0;
						if ( isset($_POST['entry_id']) ) {
							$this->entry_id = intval(wp_kses($_POST['entry_id'], array()));
							$existing_vacancy = $bookyourtravel_accommodation_helper->get_accommodation_vacancy($this->entry_id);
							
							if (!$existing_vacancy) {
								$this->entry_id = 0;
							} 
						}
						
						if ($this->entry_id > 0 ) {
							$bookyourtravel_accommodation_helper->update_accommodation_vacancy($this->entry_id, $season_name, $start_date, $end_date, $accommodation_id, $room_type_id, $room_count, $price_per_day, $price_per_day_child, $weekend_price_per_day, $weekend_price_per_day_child);
						} else {
							$this->entry_id = $bookyourtravel_accommodation_helper->create_accommodation_vacancy($season_name, $start_date, $end_date, $accommodation_id, $room_type_id, $room_count, $price_per_day, $price_per_day_child, $weekend_price_per_day, $weekend_price_per_day_child);
						}
						$success = true;
				
					} else {
						$errors[] = 'fes-error-vacancy-wrong-user';
						$success = false;
					}				
				} else {
					$errors[] = 'fes-error-vacancy-no-acc-obj';
					$success = false;
				}
				
			} else {
				$errors[] = 'fes-error-vacancy-no-acc-id';
				$success = false;
			}
		
		} elseif ($this->content_type == 'tour_schedule') {
		
			$author = isset( $_POST['post_author'] ) ? sanitize_text_field( $_POST['post_author'] ) : '';
			$users = get_users( array(
				'search' => $author,
				'fields' => 'ID'
			) );
			
			$user_id = (int) $users[0];
				
			$tour_id = isset( $_POST['tour_id'] ) ? intval( $_POST['tour_id'] ) : 0;
			
			if ( $tour_id > 0 ) {
				
				$tour = get_post($tour_id);
				
				if ( $tour ) {
					if ( $tour->post_author == $user_id ) {					
						
						$tour_obj = new BookYourTravel_Tour($tour_id);
						
						$start_date = isset( $_POST['start_date'] ) ? sanitize_text_field( $_POST['start_date'] ) : '';
						$end_date = isset( $_POST['end_date'] ) ? sanitize_text_field( $_POST['end_date'] ) : '';
						
						$start_date = date('Y-m-d', strtotime($start_date));
						$end_date = date('Y-m-d', strtotime($end_date));
						
						$price = isset( $_POST['price'] ) ?  sanitize_text_field ( $_POST['price'] ) : 0;
						$price_child = isset( $_POST['price_child'] ) ? sanitize_text_field( $_POST['price_child'] ) : null;
						$duration_days = isset( $_POST['duration_days'] ) ?  sanitize_text_field ( $_POST['duration_days'] ) : 1;
						$max_people = isset( $_POST['max_people'] ) ?  sanitize_text_field ( $_POST['max_people'] ) : 1;
						
						$season_name = isset( $_POST['season_name'] ) ?  sanitize_text_field ( $_POST['season_name'] ) : '';
						
						$this->entry_id = 0;
						if ( isset($_POST['entry_id']) ) {
							$this->entry_id = intval(wp_kses($_POST['entry_id'], array()));
							$existing_schedule = $bookyourtravel_tour_helper->get_tour_schedule($this->entry_id);
							
							if (!$existing_schedule) {
								$this->entry_id = 0;
							} 
						}
						
						if ($this->entry_id > 0 ) {
							$bookyourtravel_tour_helper->update_tour_schedule($this->entry_id, $season_name, $start_date, $duration_days, $tour_id, $price, $price_child, $max_people, $end_date);
						} else {
							$this->entry_id = $bookyourtravel_tour_helper->create_tour_schedule($season_name, $tour_id, $start_date, $duration_days, $price, $price_child, $max_people, $end_date);
						}
						$success = true;
				
					} else {
						$errors[] = 'fes-error-tour_schedule-wrong-user';
						$success = false;
					}				
				} else {
					$errors[] = 'fes-error-tour_schedule-no-tour-obj';
					$success = false;
				}
				
			} else {
				$errors[] = 'fes-error-tour_schedule-no-tour-id';
				$success = false;
			}		
		} elseif ($this->content_type == 'cruise_schedule') {
		
			$author = isset( $_POST['post_author'] ) ? sanitize_text_field( $_POST['post_author'] ) : '';
			$users = get_users( array(
				'search' => $author,
				'fields' => 'ID'
			) );
			
			$user_id = (int) $users[0];
				
			$cruise_id = isset( $_POST['cruise_id'] ) ? intval( $_POST['cruise_id'] ) : 0;
			
			if ( $cruise_id > 0 ) {
				
				$cruise = get_post($cruise_id);
				
				if ( $cruise ) {
					if ( $cruise->post_author == $user_id ) {					
						
						$cruise_obj = new BookYourTravel_Tour($cruise_id);
						
						$start_date = isset( $_POST['start_date'] ) ? sanitize_text_field( $_POST['start_date'] ) : '';
						$end_date = isset( $_POST['end_date'] ) ? sanitize_text_field( $_POST['end_date'] ) : '';
						
						$start_date = date('Y-m-d', strtotime($start_date));
						$end_date = date('Y-m-d', strtotime($end_date));
						
						$cabin_type_id = isset( $_POST['cabin_type_id'] ) ? intval( $_POST['cabin_type_id'] ) : 0;
						$cabin_count = isset( $_POST['cabin_count'] ) ? intval( $_POST['cabin_count'] ) : '';
						$cabin_count = $cabin_count > 0 ? $cabin_count : 1;
						
						$price = isset( $_POST['price'] ) ?  sanitize_text_field ( $_POST['price'] ) : 0;
						$price_child = isset( $_POST['price_child'] ) ? sanitize_text_field( $_POST['price_child'] ) : null;
						
						$duration_days = isset( $_POST['duration_days'] ) ?  sanitize_text_field ( $_POST['duration_days'] ) : 1;
						
						$season_name = isset( $_POST['season_name'] ) ?  sanitize_text_field ( $_POST['season_name'] ) : '';
						
						$this->entry_id = 0;
						if ( isset($_POST['entry_id']) ) {
							$this->entry_id = intval(wp_kses($_POST['entry_id'], array()));
							$existing_schedule = $bookyourtravel_cruise_helper->get_cruise_schedule($this->entry_id);
							
							if (!$existing_schedule) {
								$this->entry_id = 0;
							} 
						}
						
						if ($this->entry_id > 0 ) {
							$bookyourtravel_cruise_helper->update_cruise_schedule($this->entry_id, $season_name, $cruise_id, $cabin_type_id, $cabin_count, $start_date, $duration_days, $price, $price_child, $end_date);
						} else {
							$this->entry_id = $bookyourtravel_cruise_helper->create_cruise_schedule($season_name, $cruise_id, $cabin_type_id, $cabin_count, $start_date, $duration_days, $price, $price_child, $end_date);
						}
						$success = true;
				
					} else {
						$errors[] = 'fes-error-cruise_schedule-wrong-user';
						$success = false;
					}				
				} else {
					$errors[] = 'fes-error-cruise_schedule-no-cruise-obj';
					$success = false;
				}
				
			} else {
				$errors[] = 'fes-error-cruise_schedule-no-cruise-id';
				$success = false;
			}		
		}
		
		return array( 'success' => $success, 'entry_id' => $this->entry_id, 'errors' => $errors, 'content_type' => $this->content_type );
	}
	
	function delete_featured_image() {

		$result = array();

		// Bail if something fishy is going on
		if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'bookyourtravel_nonce' ) ) {
			wp_safe_redirect( esc_url_raw( add_query_arg( array( 'response' => 'fes-error', 'errors' =>  'nonce-failure' ), wp_get_referer() ) ) );
			exit;
		}
		
		if (isset($_REQUEST['entry_id'])) {

			$image_id = (int) $_REQUEST['image_id'];
			$this->entry_id = (int) $_REQUEST['entry_id'];
			$this->content_type = sanitize_text_field($_REQUEST['content_type']);
			
			$this->initialize_entry();
			
			if ($this->entry != null && $image_id > 0) {

				delete_post_thumbnail($this->entry_id);			
			}
		}
	
		exit;
	}
	
	function delete_gallery_image() {
	
		$result = array();

		// Bail if something fishy is going on
		if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'bookyourtravel_nonce' ) ) {
			wp_safe_redirect( esc_url_raw( add_query_arg( array( 'response' => 'fes-error', 'errors' =>  'nonce-failure' ), wp_get_referer() ) ) );
			exit;
		}
		
		if (isset($_REQUEST['entry_id'])) {

			$image_id = (int) $_REQUEST['image_id'];
			$this->entry_id = (int) $_REQUEST['entry_id'];
			$this->content_type = sanitize_text_field($_REQUEST['content_type']);

			$this->initialize_entry();
			
			if ($this->entry != null && $image_id > 0) {

				$gallery_images = $this->entry->get_images();

				for ( $i = 0; $i < count($gallery_images); $i++ ) { 
					$image = $gallery_images[$i];
					$image_meta_id = $image['image'];
					if ($image_meta_id == $image_id) {
						unset($gallery_images[$i]);
					}
				}

				$extra_field_name = '';
				if ($this->content_type == 'accommodation') {
					$extra_field_name = 'accommodation_images';
				} else if ($this->content_type == 'room_type') {
					$extra_field_name = 'room_type_images';
				} else if ($this->content_type == 'tour') {
					$extra_field_name = 'tour_images';
				} else if ($this->content_type == 'cruise') {
					$extra_field_name = 'cruise_images';
				} else if ($this->content_type == 'cabin_type') {
					$extra_field_name = 'cabin_type_images';
				} else if ($this->content_type == 'car_rental') {
					$extra_field_name = 'car_rental_images';
				}
				
				update_post_meta( $this->entry_id, $extra_field_name, $gallery_images );
				
			}
		}
		
		exit;
	}

	function upload_featured_image() {

		$result = array();

		// Bail if something fishy is going on
		if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'bookyourtravel_nonce' ) ) {
			wp_safe_redirect( esc_url_raw( add_query_arg( array( 'response' => 'fes-error', 'errors' =>  'nonce-failure' ), wp_get_referer() ) ) );
			exit;
		}
		
		if (isset($_REQUEST['entry_id'])) {

			$this->entry_id = (int) $_REQUEST['entry_id'];
			$this->content_type = sanitize_text_field($_REQUEST['content_type']);
			
			$this->initialize_entry();
			
			if ($this->entry != null) {
			
				$media_result = $this->upload_files( $this->entry_id, true );
				
				if ($media_result['success']) {
					echo json_encode($media_result['media_ids']);
				} else {
					echo json_encode($media_result['errors']);
				}
				
			} else {
				echo '-1';
			}
		
		} else {
			echo '-2';
		}
	
		exit;
	}
	
	function upload_gallery_images() {

		$result = array();

		// Bail if something fishy is going on
		if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'bookyourtravel_nonce' ) ) {
			wp_safe_redirect( esc_url_raw( add_query_arg( array( 'response' => 'fes-error', 'errors' =>  'nonce-failure' ), wp_get_referer() ) ) );
			exit;
		}
		
		if (isset($_REQUEST['entry_id'])) {

			$this->entry_id = (int) $_REQUEST['entry_id'];
			$this->content_type = sanitize_text_field($_REQUEST['content_type']);

			$this->initialize_entry();
			
			if ($this->entry != null) {
			
				$media_result = $this->upload_files( $this->entry_id, false );
				
				if ($media_result['success']) {
				
					$gallery_images = $this->entry->get_images();
					if (!isset($gallery_images) || !is_array($gallery_images)) {
						$gallery_images = array();
					}
					
					foreach ($media_result['media_ids'] as $media_id) {
						if (!empty($media_id)) {
							$gallery_images[] = array('image' => $media_id);
						}
					}
					
					$extra_field_name = '';
					if ($this->content_type == 'accommodation') {
						$extra_field_name = 'accommodation_images';
					} else if ($this->content_type == 'room_type') {
						$extra_field_name = 'room_type_images';
					} else if ($this->content_type == 'tour') {
						$extra_field_name = 'tour_images';
					} else if ($this->content_type == 'cruise') {
						$extra_field_name = 'cruise_images';
					} else if ($this->content_type == 'cabin_type') {
						$extra_field_name = 'cabin_type_images';
					} else if ($this->content_type == 'car_rental') {
						$extra_field_name = 'car_rental_images';
					}
					
					update_post_meta( $this->entry_id, $extra_field_name, $gallery_images );
				
					echo json_encode($media_result['media_ids']);
				} else {
					echo json_encode($media_result['errors']);
				}
				
			} else {
				echo '-1';
			}
		
		} else {
			echo '-2';
		}
	
		exit;
	
		exit;	
	}

	function upload_content() {
	
		$result = array();

		// Bail if something fishy is going on
		if ( !wp_verify_nonce( $_POST['_wpnonce'], 'bookyourtravel_nonce' ) ) {
			wp_safe_redirect( esc_url_raw( add_query_arg( array( 'response' => 'fes-error', 'errors' =>  'nonce-failure' ), wp_get_referer() ) ) );
			exit;
		}

		$form_post_id = isset( $_POST['form_post_id'] ) ? (int) $_POST['form_post_id'] : 0;
		$this->content_type = isset( $_POST['content_type'] ) ? $_POST['content_type'] : '';

		if ( $this->content_type == 'accommodation' ) {
			$this->initialize_accommodation_fields();	
		} elseif ( $this->content_type == 'room_type' ) {
			$this->initialize_room_type_fields();		
		} elseif ( $this->content_type == 'tour' ) {
			$this->initialize_tour_fields();
		} elseif ( $this->content_type == 'cruise' ) {
			$this->initialize_cruise_fields();	
		} elseif ( $this->content_type == 'cabin_type' ) {
			$this->initialize_cabin_type_fields();
		} elseif ( $this->content_type == 'car_rental' ) {
			$this->initialize_car_rental_fields();
		} elseif ( $this->content_type == 'vacancy' ) {
			$this->initialize_vacancy_fields();		
		} elseif ( $this->content_type == 'tour_schedule' ) {
			$this->initialize_tour_schedule_fields();		
		} elseif ( $this->content_type == 'cruise_schedule' ) {
			$this->initialize_cruise_schedule_fields();		
		}
			
		if (!$this->is_demo()) {
			$result = $this->upload_entry();
		} else {
			$result = array( 'success' => true, 'entry_id' => 0, 'errors' => array(), 'content_type' => $this->content_type );
		}

		do_action( 'fes_upload_result', $result );

		// Notify the admin via email
		$this->notify_admin( $result );

		// Handle error and success messages, and redirect
		$this->handle_result( $result );
		exit;
	}

	function notify_admin( $result = array() ) {
		// Notify site admins of new upload
		if ( !$result['success'] )
			return;
		// TODO: It'd be nice to add the list of upload files
		//$to = !empty( $this->settings['notification_email'] ) && filter_var( $this->settings['notification_email'], FILTER_VALIDATE_EMAIL ) ? $this->settings['notification_email'] : get_option( 'admin_email' );
		//$subj = esc_html__( 'New content was uploaded on your site', 'bookyourtravel' );
		//wp_mail( $to, $subj, $this->settings['admin_notification_text'] );
	}

	function handle_result( $result = array() ) {
	
		// Redirect to referrer if repsonse is malformed
		if ( empty( $result ) || !is_array( $result ) ) {
			wp_safe_redirect( wp_get_referer() );
			return;
		}

		// Either redirect to success page if it's set and valid. Or to referrer.
		$errors_formatted = array();

		$url = wp_get_referer();

		// $query_args will hold everything that's needed for displaying notices to user
		$query_args = array();
		
		// Account for successful uploads
		if ( isset( $result['success'] ) && $result['success'] ) {
			
			// If it's a post
			if ( isset( $result['entry_id'] ) ) {
			
				if (!isset($_POST['entry_id'])) {
					$query_args['insert'] = '1';
				} else {
					$url = remove_query_arg( 'insert', $url );
				}
				
				$query_args['fesid'] = $result['entry_id'];
				
				if ( $this->content_type == 'room_type' ) {
					$query_args['response'] = 'fes-room_type-sent';
				} else if ( $this->content_type == 'accommodation' ) {
					$query_args['response'] = 'fes-accommodation-sent';
				} else if ( $this->content_type == 'tour' ) {
					$query_args['response'] = 'fes-tour-sent';
				} else if ( $this->content_type == 'cruise' ) {
					$query_args['response'] = 'fes-cruise-sent';
				} else if ( $this->content_type == 'cabin_type' ) {
					$query_args['response'] = 'fes-cabin_type-sent';
				} else if ( $this->content_type == 'car_rental' ) {
					$query_args['response'] = 'fes-car_rental-sent';
				} else if ( $this->content_type == 'vacancy' ) {
					$query_args['response'] = 'fes-vacancy-sent';
				} else if ( $this->content_type == 'tour_schedule' ) {
					$query_args['response'] = 'fes-tour_schedule-sent';
				} else if ( $this->content_type == 'cruise_schedule' ) {
					$query_args['response'] = 'fes-cruise_schedule-sent';
				}
					
			} elseif ( isset( $result['media_ids'] ) && !isset( $result['entry_id'] ) ) {
				// If it's media uploads
				$query_args['response'] = 'fes-sent';
			}
		}

		// Some errors happened. Format a string to be passed as GET value.
		if ( !empty( $result['errors'] ) ) {
			$query_args['response'] = 'fes-error';
			$_errors = array();
			
			// Iterate through key=>value pairs of errors
			foreach ( $result['errors'] as $key => $error ) {
				if ( isset( $error[0] ) )
					$_errors[$key] = join( ',,,', (array) $error[0] );
			}

			foreach ( $_errors as $key => $value ) {
				$errors_formatted[] = "{$key}::{$value}";
			}

			$query_args['errors'] = join( ';', $errors_formatted );
		}

		// Perform a safe redirect and exit
		wp_safe_redirect( esc_url_raw ( add_query_arg( $query_args, $url ) ) );
		
		exit;
	}

	function display_response_notices( $get = array() ) {
	
		if ( empty( $get ) )
			return;

		$mapping_prefix = '';
		if ($this->is_demo()) {
			$mapping_prefix = 'If this were not a demo, the message would read: ';
		} else if (isset($get['response'])) {
			if ($get['response'] == 'fes-sent' || $get['response'] == 'fes-accommodation-sent' || $get['response'] == 'fes-room_type-sent' || $get['response'] == 'fes-vacancy-sent' || $get['response'] == 'fes-tour_schedule-sent' || $get['response'] == 'fes-cruise_schedule-sent' ) {
				$mapping_prefix = esc_html__('Success: ', 'bookyourtravel');
			} else if ($get['response'] == 'fes-error') {
				$mapping_prefix = esc_html__('Error: ', 'bookyourtravel');
			}
		}
		
		$mapping_postfix = '';
		if (!isset($get['insert'])) {
			$mapping_postfix = esc_html__('updated', 'bookyourtravel');
		} else {
			$mapping_postfix = esc_html__('created', 'bookyourtravel');
		}
			
		$output = '';
		$map = array(
			'fes-sent' => array(
				'text' => sprintf(esc_html__( '%s your file was successfully %s!', 'bookyourtravel' ), $mapping_prefix, $mapping_postfix),
				'class' => 'success',
			),
			'fes-accommodation-sent' => array(
				'text' => sprintf(esc_html__( '%s your accommodation was successfully %s!', 'bookyourtravel' ), $mapping_prefix, $mapping_postfix),
				'class' => 'success',
			),
			'fes-room_type-sent' => array(
				'text' => sprintf(esc_html__( '%s your room type was successfully %s!', 'bookyourtravel' ), $mapping_prefix, $mapping_postfix),
				'class' => 'success',
			),
			'fes-vacancy-sent' => array(
				'text' => sprintf(esc_html__('%s your vacancy was successfully %s!', 'bookyourtravel' ), $mapping_prefix, $mapping_postfix),
				'class' => 'success',
			),
			'fes-tour_schedule-sent' => array(
				'text' => sprintf(esc_html__('%s your tour schedule was successfully %s!', 'bookyourtravel' ), $mapping_prefix, $mapping_postfix),
				'class' => 'success',
			),
			'fes-cruise_schedule-sent' => array(
				'text' => sprintf(esc_html__('%s your cruise schedule was successfully %s!', 'bookyourtravel' ), $mapping_prefix, $mapping_postfix),
				'class' => 'success',
			),
			'fes-error' => array(
				'text' => sprintf(esc_html__( '%s there was an error with your submission', 'bookyourtravel' ), $mapping_prefix),
				'class' => 'failure',
			),
		);
		
		$edit_notices = array(
			'accommodation' => array (
				'text' => esc_html__('You are currently editing your selected accommodation. Click "Update" to save your changes.', 'bookyourtravel'),
				'class' => 'warning'
			),
			'room_type' => array (
				'text' => esc_html__('You are currently editing your selected room type. Click "Update" to save your changes.', 'bookyourtravel'),
				'class' => 'warning'
			),
			'tour' => array (
				'text' => esc_html__('You are currently editing your selected tour. Click "Update" to save your changes.', 'bookyourtravel'),
				'class' => 'warning'
			),
			'cruise' => array (
				'text' => esc_html__('You are currently editing your selected cruise. Click "Update" to save your changes.', 'bookyourtravel'),
				'class' => 'warning'
			),
			'cabin_type' => array (
				'text' => esc_html__('You are currently editing your selected cabin type. Click "Update" to save your changes.', 'bookyourtravel'),
				'class' => 'warning'
			),
			'car_rental' => array (
				'text' => esc_html__('You are currently editing your selected car rental. Click "Update" to save your changes.', 'bookyourtravel'),
				'class' => 'warning'
			),
			'vacancy' => array (
				'text' => esc_html__('You are currently editing your selected vacancy. Click "Update" to save your changes.', 'bookyourtravel'),
				'class' => 'warning'
			),
			'tour_schedule' => array (
				'text' => esc_html__('You are currently editing your selected tour schedule. Click "Update" to save your changes.', 'bookyourtravel'),
				'class' => 'warning'
			),	
			'cruise_schedule' => array (
				'text' => esc_html__('You are currently editing your selected cruise schedule. Click "Update" to save your changes.', 'bookyourtravel'),
				'class' => 'warning'
			),	
		);	

		if ( isset( $get['response'] ) && isset( $map[ $get['response'] ] ) ) {
			$output .= $this->notice_html( $map[ $get['response'] ]['text'] , $map[ $get['response'] ]['class'] );
		}

		if ( !empty( $get['errors' ] ) ) {
			$output .= $this->display_errors( $get['errors' ] );
		}
			
		if ( !empty( $get['fesid'] ) && isset($this->entry_id) && isset($this->content_type) && $this->entry != null ) {
			$output .= $this->notice_html( $edit_notices[ $this->content_type ]['text'] , $edit_notices[ $this->content_type ]['class'] );
		}
			
		echo $output;
	}
	
	function notice_html( $message, $class ) {
	
		if ( empty( $message ) || empty( $class ) )
			return;
		return sprintf( '<p class="fes-notice %1$s">%2$s</p>', $class, $message );
	}

	function display_errors( $errors ) {
		
		$errors_arr = explode( ';', $errors );
		$output = '';
		$map = array(
			'nonce-failure' => array(
				'text' => esc_html__( 'Security check failed!', 'bookyourtravel' ),
			),
			'fes-disallowed-mime-type' => array(
				'text' => esc_html__( 'This kind of file is not allowed. Please, try selecting another file.', 'bookyourtravel' ),
				'format' => '%1$s: <br/> File name: %2$s <br/> MIME-TYPE: %3$s',
			),
			'fes-invalid-post' => array(
				'text' =>esc_html__( 'The content you are trying to post is invalid.', 'bookyourtravel' ),
			),
			'fes-error-media' => array(
				'text' =>esc_html__( "Couldn't upload the file", 'bookyourtravel' ),
			),
			'fes-error-post' => array(
				'text' =>esc_html__( "Couldn't create the post", 'bookyourtravel' ),
			),
			'fes-error-vacancy-wrong-user' => array(
				'text' =>esc_html__( "User does not own accommodation specified", 'bookyourtravel' ),
			),
			'fes-error-vacancy-no-acc-obj' => array(
				'text' =>esc_html__( "Could not find accommodation object", 'bookyourtravel' ),
			),
			'fes-error-vacancy-no-acc-id' => array(
				'text' =>esc_html__( "Accommodation id was not specified", 'bookyourtravel' ),
			),
			'fes-error-tour_schedule-wrong-user' => array(
				'text' =>esc_html__( "User does not own tour specified", 'bookyourtravel' ),
			),
			'fes-error-tour_schedule-no-tour-obj' => array(
				'text' =>esc_html__( "Could not find tour object", 'bookyourtravel' ),
			),
			'fes-error-tour_schedule-no-tour-id' => array(
				'text' =>esc_html__( "Tour id was not specified", 'bookyourtravel' ),
			),
			'fes-error-cruise_schedule-wrong-user' => array(
				'text' =>esc_html__( "User does not own cruise specified", 'bookyourtravel' ),
			),
			'fes-error-cruise_schedule-no-cruise-obj' => array(
				'text' =>esc_html__( "Could not find cruise object", 'bookyourtravel' ),
			),
			'fes-error-cruise_schedule-no-cruise-id' => array(
				'text' =>esc_html__( "Cruise id was not specified", 'bookyourtravel' ),
			),
		);

		// TODO: DAMN SON you should refactor this
		foreach ( $errors_arr as $error ) {
			$error_type = explode( '::', $error );
			$error_details = explode( '|', $error_type[1] );
			// Iterate over different errors
			foreach ( $error_details as $single_error ) {

				// And see if there's any additional details
				$details = isset( $single_error ) ? explode( ',,,', $single_error ) : explode( ',,,', $single_error );
				// Add a description to our details array
				array_unshift( $details, $map[ $error_type[0] ]['text']  );
				// If we have a format, let's format an error
				// If not, just display the message
				if ( isset( $map[ $error_type[0] ]['format'] ) )
					$message = vsprintf( $map[ $error_type[0] ]['format'], $details );
				else
					$message = $map[ $error_type[0] ]['text'];
			}
			$output .= $this->notice_html( $message, 'failure' );
		}

		return $output;
	}
	
	function render_checkbox_input($atts) {

		$atts = $this->prepare_atts($atts);
	
		extract( $atts );
		
		$atts = array( 'id' => $id, 'class' => $class, 'multiple' => $multiple );
		
		// Workaround for HTML5 multiple attribute
		if ( (bool) $multiple === false )
			unset( $atts['multiple'] );

		$selected_value = $this->get_entry_field_value($name);
		if ($this->entry != null && isset($selected_value) ) {
			if ($type == 'checkbox' && $selected_value == '1')
				$atts['checked'] = 'checked';
		}
		
		$input = $this->html_helper->input( $type, $name, $value, $atts );

		$element = $this->html_helper->element( 'label',  $input . $description, array( 'for' => $id ), false );

		$container_class = 'fes-input-wrapper';
		if (isset($container_class_override) && !empty($container_class_override))
			$container_class .= ' ' . $container_class_override;
		return $this->html_helper->element( 'div', $element, array( 'class' => $container_class ), false );
		
	}
	
	function render_input( $atts ) {

		$atts = $this->prepare_atts($atts);
	
		extract( $atts );
		
		$atts = array( 'id' => $id, 'class' => $class, 'multiple' => $multiple );
		
		// Workaround for HTML5 multiple attribute
		if ( (bool) $multiple === false )
			unset( $atts['multiple'] );

		$selected_value = $this->get_entry_field_value($name);
		if ($this->entry != null && isset($selected_value) ) {
			if ($type == 'checkbox' && $selected_value == '1')
				$atts['checked'] = 'checked';
			else if ($type == 'text')
				$value = $selected_value;
		}
			
		// Allow multiple file upload by default.
		// To do so, we need to add array notation to name field: []
		if ( !strpos( $name, '[]' ) && $type == 'file' )
			$name = $name . '[]';
			
		$input = $this->html_helper->input( $type, $name, $value, $atts );

		// No need for wrappers or labels for hidden input
		if ( $type == 'hidden' )
			return $input;

		$element = $this->html_helper->element( 'label', $description . $input , array( 'for' => $id ), false );

		$container_class = 'fes-input-wrapper';
		if (isset($container_class_override) && !empty($container_class_override))
			$container_class .= ' ' . $container_class_override;
		return $this->html_helper->element( 'div', $element, array( 'class' => $container_class ), false );
	}

	function render_textarea( $atts ) {
	
		$atts = $this->prepare_atts($atts);
	
		extract( $atts );
		
		$selected_value = $this->get_entry_field_value($name);
		if ( $this->entry != null && isset($selected_value) ) {
			$value = $selected_value;
		}
		
		// Render WYSIWYG textarea
		if ( $wysiwyg_enabled ) {
			ob_start();
			wp_editor( $value, $id, array(
					'textarea_name' => $name,
					'media_buttons' => false,
					'teeny' => true,
					"quicktags" => array(
						"buttons" => "em,strong,link"
					)
				) );
			$tiny = ob_get_clean();
			$label =  $this->html_helper->element( 'label', $description , array( 'for' => $id ), false );
			return $this->html_helper->element( 'div', $label . $tiny, array( 'class' => 'fes-input-wrapper' ), false ) ;
		}
		// Render plain textarea
		$element = $this->html_helper->element( 'textarea', $value, array( 'name' => $name, 'id' => $id, 'class' => $class ) );
		$label = $this->html_helper->element( 'label', $description, array( 'for' => $id ), false );

		$container_class = 'fes-input-wrapper';
		if (isset($container_class_override) && !empty($container_class_override))
			$container_class .= ' ' . $container_class_override;
		return $this->html_helper->element( 'div', $label . $element, array( 'class' => $container_class ), false );
	}

	function get_entry_field_value($field_id) {
		
		if ($this->entry != null) { 
		
			if ( $this->content_type == 'accommodation' ) {
				
				$accommodation_obj = new BookYourTravel_Accommodation(intval($this->entry_id));
				return $accommodation_obj->get_field_value($field_id, false);
				
			} elseif ( $this->content_type == 'room_type' ) {
				
				$room_type_obj = new BookYourTravel_Room_Type(intval($this->entry_id));
				return $room_type_obj->get_field_value($field_id, false);			
			} elseif ( $this->content_type == 'tour' ) {
				
				$tour_obj = new BookYourTravel_Tour(intval($this->entry_id));
				return $tour_obj->get_field_value($field_id, false);			
			} elseif ( $this->content_type == 'cruise' ) {
				
				$cruise_obj = new BookYourTravel_Cruise(intval($this->entry_id));
				return $cruise_obj->get_field_value($field_id, false);			
			} elseif ( $this->content_type == 'cabin_type' ) {
				
				$cabin_type_obj = new BookYourTravel_Cabin_Type(intval($this->entry_id));
				return $cabin_type_obj->get_field_value($field_id, false);			
			} elseif ( $this->content_type == 'car_rental' ) {
				
				$car_rental_obj = new BookYourTravel_Car_Rental(intval($this->entry_id));
				return $car_rental_obj->get_field_value($field_id, false);			
				
			} else if ( $this->content_type == 'vacancy' ) {
			
				if (property_exists($this->entry,$field_id) && isset($this->entry->$field_id)) {
					return $this->entry->$field_id;
				}
				
			} else if ( $this->content_type == 'tour_schedule' ) {
			
				if (property_exists($this->entry,$field_id) && isset($this->entry->$field_id)) {
					return $this->entry->$field_id;
				}
				
			} else if ( $this->content_type == 'cruise_schedule' ) {
			
				if (property_exists($this->entry,$field_id) && isset($this->entry->$field_id)) {
					return $this->entry->$field_id;
				}
				
			}				
		}
		
		return null;
	}
	
	/**
	 * Select element callback
	 *
	 * @param array   shortcode attributes
	 * @return [type]       [description]
	 */
	function render_select( $atts ) {
	
		$atts = $this->prepare_atts($atts);
	
		extract( $atts );
		$atts = array( 'values' => $values );
		$values = explode( ',', $values );
		$options = '';
		
		$selected_value = $this->get_entry_field_value($name);
		
		//Build options for the list
		foreach ( $values as $option ) {
			$kv = explode( "::", $option );
			$caption = isset( $kv[1] ) ? $kv[1] : $kv[0];
			$option_atts = array( 'value' => $kv[0] );
			if ( isset($selected_value) && $selected_value == $kv[0] )
				$option_atts['selected'] = 'selected';
			
			$options .= $this->html_helper->element( 'option', $caption, $option_atts, false );
		}

		//Render select field
		$element = $this->html_helper->element( 'label', $description . $this->html_helper->element( 'select', $options, array(
					'name' => $name,
					'id' => $id,
					'class' => $class
				), false ), array( 'for' => $id ), false );

		$container_class = 'fes-input-wrapper';
		if (isset($container_class_override) && !empty($container_class_override))
			$container_class .= ' ' . $container_class_override;
		return $this->html_helper->element( 'div', $element, array( 'class' => $container_class ), false );
	}

	/**
	 * Checkboxes element callback
	 *
	 * @param array   shortcode attributes
	 * @return [type]       [description]
	 */
	function render_checkboxes( $atts ) {
	
		$atts = $this->prepare_atts($atts);
		extract( $atts );
		
		$atts = array( 'values' => $values );
		$values = explode( ',', $values );
		$options = '';

		$selected_values = $this->get_entry_field_value($name);
		
		// Making sure we're having array of values for checkboxes
		if ( false === stristr( '[]', $name ) )
			$name = $name . '[]';

		//Build options for the list
		foreach ( $values as $option ) {
			$kv = explode( "::", $option );
			if (is_array($selected_values) && in_array($kv[0], $selected_values)) {
				$atts['checked'] = 'checked';
			} else {
				unset($atts['checked']);
			}
			$options .= $this->html_helper->_checkbox( $name, isset( $kv[1] ) ? $kv[1] : $kv[0], $kv[0], $atts, array() );
		}

		$description = $label = $this->html_helper->element( 'label', $description, array(), false );

		// Render select field
		$element = $this->html_helper->element( 'div', $description . $options, array( 'class' => 'checkbox-wrapper' ), false );
		
		$container_class = 'fes-input-wrapper';
		if (isset($container_class_override) && !empty($container_class_override))
			$container_class .= ' ' . $container_class_override;
		return $this->html_helper->element( 'div', $element, array( 'class' => $container_class ), false );
	}
	
	function prepare_atts($atts) {
	
		$supported_atts = array(
			'id' => '',
			'name' => '',
			'description' => '',
			'value' => '',
			'type' => '',
			'class' => '',
			'multiple' => false,
			'values' => '',
			'wysiwyg_enabled' => false,
			'role' => 'meta',
			'container_class_override' => '',
		);
		
		return shortcode_atts($supported_atts, $atts);
	}

	function initialize_vacancy_fields() {
	
		global $bookyourtravel_accommodation_helper, $bookyourtravel_room_type_helper;
	
		$this->form_fields = array();	
		
		$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => 'season_name', 'id' => 'fes_season_name', 'description' => esc_html__( 'Season name', 'bookyourtravel' ), 'class' => 'text required' );

		$accommodation_results = $bookyourtravel_accommodation_helper->list_accommodations ( 0, -1, 'title', 'asc', 0, array(),  array(), array(), false, null, $this->get_author_id_for_list(), true );
		
		$accommodations_str = '';
		if ( $accommodation_results && count($accommodation_results) > 0 && $accommodation_results['total'] > 0 ) {
			foreach ($accommodation_results['results'] as $accommodation_result) {
				$accommodations_str .= "{$accommodation_result->ID}::{$accommodation_result->post_title},";		
			}
		}
		$accommodations_str = '::' . esc_html__('Select accommodation', 'bookyourtravel') . ',' .  rtrim($accommodations_str, ',');
		$accommodations_str = rtrim($accommodations_str, ',');
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'accommodation_id', 'id' => 'fes_accommodation_id', 'description' => esc_html__( 'Accommodation', 'bookyourtravel' ), 'values' => $accommodations_str, 'class' => 'select required' );
	
		$room_types_str = '';
		if ($this->entry_id > 0) {
		
			$room_type_query = $bookyourtravel_room_type_helper->list_room_types($this->get_author_id_for_list(), array('publish', 'draft'));
			if ($room_type_query->have_posts()) {
				while ($room_type_query->have_posts()) {
					$room_type_query->the_post();
					global $post;				
					$room_types_str .= "{$post->ID}::{$post->post_title},";
				}
			}
			$room_types_str = rtrim($room_types_str, ',');

		}		
		$room_types_str = '::' . esc_html__('Select room type', 'bookyourtravel') . ',' .  rtrim($room_types_str, ',');
		$room_types_str = rtrim($room_types_str, ',');
		
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'room_type_id', 'id' => 'fes_room_type_id', 'description' => esc_html__( 'Room type', 'bookyourtravel' ), 'values' => $room_types_str, 'class' => 'select', 'container_class_override' => 'room_types' );
		
		$number_of_available_rooms_str = '::0,';
		for ($i=1;$i<100;$i++) {
			$number_of_available_rooms_str .= "$i::$i,";
		}
		$number_of_available_rooms_str = rtrim($number_of_available_rooms_str, ',');		
		
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'room_count', 'id' => 'fes_room_count', 'description' => esc_html__( 'Number of available rooms', 'bookyourtravel' ), 'values' => $number_of_available_rooms_str, 'class' => 'select', 'container_class_override' => 'room_types' );
		$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => 'price_per_day', 'id' => 'fes_price_per_day', 'description' => esc_html__( 'Price per day', 'bookyourtravel' ), 'class' => 'number required' );
		$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => 'price_per_day_child', 'id' => 'fes_price_per_day_child', 'description' => esc_html__( 'Price per day child', 'bookyourtravel' ), 'class' => 'number required', 'container_class_override' => 'per_person' );
		$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => 'weekend_price_per_day', 'id' => 'fes_weekend_price_per_day', 'description' => esc_html__( 'Weekend price per day', 'bookyourtravel' ), 'class' => 'number required', 'container_class_override' => 'daily_rent' );		
		$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => 'weekend_price_per_day_child', 'id' => 'fes_weekend_price_per_day_child', 'description' => esc_html__( 'Weekend price per day child', 'bookyourtravel' ), 'class' => 'number required', 'container_class_override' => 'per_person daily_rent' );
		$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => 'fes_start_date', 'id' => 'fes_start_date', 'description' => esc_html__( 'Start date', 'bookyourtravel' ), 'class' => 'dateFormatDate required datepicker' );
		$this->form_fields[] = (object)array( 'type' => 'hidden', 'role' => 'internal', 'name' => 'start_date', 'id' => 'start_date' );
		$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => 'fes_end_date', 'id' => 'fes_end_date', 'description' => esc_html__( 'End date', 'bookyourtravel' ), 'class' => 'dateFormatDate required datepicker' );
		$this->form_fields[] = (object)array( 'type' => 'hidden', 'role' => 'internal', 'name' => 'end_date', 'id' => 'end_date' );					
	}

	function initialize_tour_schedule_fields() {
	
		global $bookyourtravel_tour_helper;
	
		$this->form_fields = array();	
		
		$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => 'season_name', 'id' => 'fes_season_name', 'description' => esc_html__( 'Season name', 'bookyourtravel' ), 'class' => 'text required' );

		$tour_results = $bookyourtravel_tour_helper->list_tours ( 0, -1, '', '', 0, array(),  array(), array(), false, $this->get_author_id_for_list(), true );
		
		$tours_str = '';
		if ( $tour_results && count($tour_results) > 0 && $tour_results['total'] > 0 ) {
			foreach ($tour_results['results'] as $tour_result) {
				$tours_str .= "{$tour_result->ID}::{$tour_result->post_title},";		
			}
		}
		$tours_str = '::' . esc_html__('Select tour', 'bookyourtravel') . ',' .  rtrim($tours_str, ',');
		$tours_str = rtrim($tours_str, ',');
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'tour_id', 'id' => 'fes_tour_id', 'description' => esc_html__( 'Tour', 'bookyourtravel' ), 'values' => $tours_str, 'class' => 'select required' );
	
		$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => 'price', 'id' => 'fes_price', 'description' => esc_html__( 'Price', 'bookyourtravel' ), 'class' => 'number required' );
		$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => 'price_child', 'id' => 'fes_price_child', 'description' => esc_html__( 'Price per child', 'bookyourtravel' ), 'class' => 'number required', 'container_class_override' => 'per_person' );

		$duration_days_count_str = '';
		for ($i=1;$i<100;$i++) {
			$duration_days_count_str .= "$i::$i,";
		}
		$duration_days_count_str = rtrim($duration_days_count_str, ',');	
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'duration_days', 'id' => 'fes_duration_days', 'description' => esc_html__( 'Duration days', 'bookyourtravel' ), 'values' => $duration_days_count_str, 'class' => 'select', 'container_class_override' => '' );

		$max_people_count_str = '';
		for ($i=1;$i<100;$i++) {
			$max_people_count_str .= "$i::$i,";
		}
		$max_people_count_str = rtrim($max_people_count_str, ',');	
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'max_people', 'id' => 'fes_max_people', 'description' => esc_html__( 'Maximum people', 'bookyourtravel' ), 'values' => $max_people_count_str, 'class' => 'select', 'container_class_override' => '' );

		$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => 'fes_start_date', 'id' => 'fes_start_date', 'description' => esc_html__( 'Start date', 'bookyourtravel' ), 'class' => 'dateFormatDate required datepicker' );
		$this->form_fields[] = (object)array( 'type' => 'hidden', 'role' => 'internal', 'name' => 'start_date', 'id' => 'start_date' );
		$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => 'fes_end_date', 'id' => 'fes_end_date', 'description' => esc_html__( 'End date', 'bookyourtravel' ), 'class' => 'dateFormatDate required datepicker', 'container_class_override' => 'is_repeated' );
		$this->form_fields[] = (object)array( 'type' => 'hidden', 'role' => 'internal', 'name' => 'end_date', 'id' => 'end_date' );					
	}
	
	function initialize_cruise_schedule_fields() {
	
		global $bookyourtravel_cruise_helper, $bookyourtravel_cabin_type_helper;
	
		$this->form_fields = array();	
		
		$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => 'season_name', 'id' => 'fes_season_name', 'description' => esc_html__( 'Season name', 'bookyourtravel' ), 'class' => 'text required' );

		$cruise_results = $bookyourtravel_cruise_helper->list_cruises ( 0, -1, '', '', 0, array(),  array(), array(), false, $this->get_author_id_for_list(), true );
		
		$cruises_str = '';
		if ( $cruise_results && count($cruise_results) > 0 && $cruise_results['total'] > 0 ) {
			foreach ($cruise_results['results'] as $cruise_result) {
				$cruises_str .= "{$cruise_result->ID}::{$cruise_result->post_title},";		
			}
		}
		$cruises_str = '::' . esc_html__('Select cruise', 'bookyourtravel') . ',' .  rtrim($cruises_str, ',');
		$cruises_str = rtrim($cruises_str, ',');
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'cruise_id', 'id' => 'fes_cruise_id', 'description' => esc_html__( 'Cruise', 'bookyourtravel' ), 'values' => $cruises_str, 'class' => 'select required' );
	
		$cabin_types_str = '';
		if ($this->entry_id > 0) {
		
			$cabin_type_query = $bookyourtravel_cabin_type_helper->list_cabin_types($this->get_author_id_for_list(), array('publish', 'draft'));
			if ($cabin_type_query->have_posts()) {
				while ($cabin_type_query->have_posts()) {
					$cabin_type_query->the_post();
					global $post;				
					$cabin_types_str .= "{$post->ID}::{$post->post_title},";
				}
			}
			$cabin_types_str = rtrim($cabin_types_str, ',');

		}		
		$cabin_types_str = '::' . esc_html__('Select cabin type', 'bookyourtravel') . ',' .  rtrim($cabin_types_str, ',');
		$cabin_types_str = rtrim($cabin_types_str, ',');
		
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'cabin_type_id', 'id' => 'fes_cabin_type_id', 'description' => esc_html__( 'Cabin type', 'bookyourtravel' ), 'values' => $cabin_types_str, 'class' => 'select required', 'container_class_override' => 'cabin_types' );
		
		$number_of_available_cabins_str = '';
		for ($i=1;$i<100;$i++) {
			$number_of_available_cabins_str .= "$i::$i,";
		}
		$number_of_available_cabins_str = rtrim($number_of_available_cabins_str, ',');		
		
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'cabin_count', 'id' => 'fes_cabin_count', 'description' => esc_html__( 'Number of available cabins', 'bookyourtravel' ), 'values' => $number_of_available_cabins_str, 'class' => 'select', 'container_class_override' => 'cabin_types' );

		$duration_days_count_str = '';
		for ($i=1;$i<100;$i++) {
			$duration_days_count_str .= "$i::$i,";
		}
		$duration_days_count_str = rtrim($duration_days_count_str, ',');	
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'duration_days', 'id' => 'fes_duration_days', 'description' => esc_html__( 'Duration days', 'bookyourtravel' ), 'values' => $duration_days_count_str, 'class' => 'select', 'container_class_override' => '' );
		
		$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => 'price', 'id' => 'fes_price', 'description' => esc_html__( 'Price per day', 'bookyourtravel' ), 'class' => 'number required' );
		$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => 'price_child', 'id' => 'fes_price_child', 'description' => esc_html__( 'Price per child', 'bookyourtravel' ), 'class' => 'number required', 'container_class_override' => 'per_person' );
		$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => 'fes_start_date', 'id' => 'fes_start_date', 'description' => esc_html__( 'Start date', 'bookyourtravel' ), 'class' => 'dateFormatDate required datepicker' );
		$this->form_fields[] = (object)array( 'type' => 'hidden', 'role' => 'internal', 'name' => 'start_date', 'id' => 'start_date' );
		$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => 'fes_end_date', 'id' => 'fes_end_date', 'description' => esc_html__( 'End date', 'bookyourtravel' ), 'class' => 'dateFormatDate required datepicker', 'container_class_override' => 'is_repeated' );
		$this->form_fields[] = (object)array( 'type' => 'hidden', 'role' => 'internal', 'name' => 'end_date', 'id' => 'end_date' );					
	}	
	
	function initialize_room_type_fields() {
				
		global $bookyourtravel_room_type_helper;

		$this->form_fields = array();	
		
		wp_reset_postdata();
		
		$counts_str = "0::0,1::1,2::2,3::3,4::4,5::5,6::6,7::7,8::8,9::9,10::10,11::11,12::12,13::13,14::14,15::15,16::16,17::17,18::18,19::19,20::20";
		$counts_str_start_1 = "1::1,2::2,3::3,4::4,5::5,6::6,7::7,8::8,9::9,10::10,11::11,12::12,13::13,14::14,15::15,16::16,17::17,18::18,19::19,20::20";
		
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'room_type_min_count', 'id' => 'fes_room_type_min_count', 'description' => esc_html__( 'Minimum adult count', 'bookyourtravel' ), 'values' => $counts_str_start_1, 'class' => 'select' );
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'room_type_min_child_count', 'id' => 'fes_room_type_min_child_count', 'description' => esc_html__( 'Minimum child count', 'bookyourtravel' ), 'values' => $counts_str, 'class' => 'select' );
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'room_type_max_count', 'id' => 'fes_room_type_max_count', 'description' => esc_html__( 'Maximum adult count', 'bookyourtravel' ), 'values' => $counts_str, 'class' => 'select' );
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'room_type_max_child_count', 'id' => 'fes_room_type_max_child_count', 'description' => esc_html__( 'Maximum child count', 'bookyourtravel' ), 'values' => $counts_str, 'class' => 'select' );
		$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => 'room_type_bed_size', 'id' => 'fes_room_type_bed_size', 'description' => esc_html__( 'Bed size', 'bookyourtravel' ) );
		$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => 'room_type_room_size', 'id' => 'fes_room_type_room_size', 'description' => esc_html__( 'Room size', 'bookyourtravel' ) );
		$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => 'room_type_meta', 'id' => 'fes_room_type_meta', 'description' => esc_html__( 'Room meta information', 'bookyourtravel' ) );
		
		$taxonomies = array( 'facility' );
		$args = array( 'hide_empty' => false, 'fields' => 'all' ); 
		$facilities = get_terms($taxonomies, $args);
		$facilities_str = '';
		foreach ($facilities as $facility) {
			$facilities_str .= "{$facility->term_id}::{$facility->name},";
		}
		$facilities_str = rtrim($facilities_str, ',');
		if (!empty($facilities_str)) {
			$this->form_fields[] = (object)array( 'type' => 'checkbox', 'role' => 'internal', 'name' => 'facilities', 'id' => 'fes_facilities', 'description' => esc_html__( 'Facilities', 'bookyourtravel' ), 'values' => $facilities_str, 'class' => 'checkboxes' );
		}
	}
	
	function initialize_cabin_type_fields() {
				
		global $bookyourtravel_cabin_type_helper;

		$this->form_fields = array();	
		
		wp_reset_postdata();
		
		$counts_str = "0::0,1::1,2::2,3::3,4::4,5::5,6::6,7::7,8::8,9::9,10::10,11::11,12::12,13::13,14::14,15::15,16::16,17::17,18::18,19::19,20::20";
		$counts_str_start_1 = "1::1,2::2,3::3,4::4,5::5,6::6,7::7,8::8,9::9,10::10,11::11,12::12,13::13,14::14,15::15,16::16,17::17,18::18,19::19,20::20";
		
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'cabin_type_max_count', 'id' => 'fes_cabin_type_max_count', 'description' => esc_html__( 'Maximum adult count', 'bookyourtravel' ), 'values' => $counts_str, 'class' => 'select' );
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'cabin_type_max_child_count', 'id' => 'fes_cabin_type_max_child_count', 'description' => esc_html__( 'Maximum child count', 'bookyourtravel' ), 'values' => $counts_str, 'class' => 'select' );
		$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => 'cabin_type_bed_size', 'id' => 'fes_cabin_type_bed_size', 'description' => esc_html__( 'Bed size', 'bookyourtravel' ) );
		$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => 'cabin_type_room_size', 'id' => 'fes_cabin_type_room_size', 'description' => esc_html__( 'Cabin size', 'bookyourtravel' ) );
		$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => 'cabin_type_meta', 'id' => 'fes_cabin_type_meta', 'description' => esc_html__( 'Cabin meta information', 'bookyourtravel' ) );
		
		$taxonomies = array( 'facility' );
		$args = array( 'hide_empty' => false, 'fields' => 'all' ); 
		$facilities = get_terms($taxonomies, $args);
		$facilities_str = '';
		foreach ($facilities as $facility) {
			$facilities_str .= "{$facility->term_id}::{$facility->name},";
		}
		$facilities_str = rtrim($facilities_str, ',');
		if (!empty($facilities_str)) {
			$this->form_fields[] = (object)array( 'type' => 'checkbox', 'role' => 'internal', 'name' => 'facilities', 'id' => 'fes_facilities', 'description' => esc_html__( 'Facilities', 'bookyourtravel' ), 'values' => $facilities_str, 'class' => 'checkboxes' );
		}
	}
	
	function initialize_accommodation_fields() {	
	
		global $bookyourtravel_location_helper, $bookyourtravel_room_type_helper;
	
		$this->form_fields = array();

		$allowed_tags = array();
		$allowed_tags['div'] = array('class' => array(), 'id' => array(), 'style' => array());
		$allowed_tags['br'] = array();
		$allowed_tags['small'] = array();
		
		$rent_types_str = sprintf("0::%s", __('Daily', 'bookyourtravel')); 
		$rent_types_str .= sprintf(",1::%s", __('Weekly', 'bookyourtravel')); 
		$rent_types_str .= sprintf(",2::%s", __('Monthly', 'bookyourtravel')); 
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'accommodation_rent_type', 'id' => 'fes_accommodation_rent_type', 'description' => wp_kses(__( '<div>Rent type<br /><small>Are you renting the accommodation on a daily (default), weekly or monthly basis?</small></div>', 'bookyourtravel' ), $allowed_tags) , 'values' => $rent_types_str, 'class' => 'select', 'container_class_override' => '' );		

		$this->form_fields[] = (object)array( 'type' => 'checkbox', 'value' => '1', 'role' => 'internal', 'name' => 'accommodation_disabled_room_types', 'id' => 'fes_accommodation_disabled_room_types', 'description' => wp_kses(__( '<div>Disabled room types?<br /><small>Is the accommodation bookable as one entity (lodges, houses etc) or does it provide individual room booking (hotel/motel style)?</small></div>', 'bookyourtravel' ), $allowed_tags) );
		$this->form_fields[] = (object)array( 'type' => 'checkbox', 'value' => '1', 'role' => 'internal', 'name' => 'accommodation_is_price_per_person', 'id' => 'fes_accommodation_is_price_per_person', 'description' => wp_kses(__( '<div>Is priced per person?<br /><small>Otherwise it\'s priced on a per-room basis</small></div>', 'bookyourtravel' ), $allowed_tags) );
		$this->form_fields[] = (object)array( 'type' => 'checkbox', 'value' => '1', 'role' => 'internal', 'name' => 'accommodation_is_reservation_only', 'id' => 'fes_accommodation_is_reservation_only', 'description' => wp_kses(__( '<div>Is for reservation only?<br /><small>If this option is checked, then this particular accommodation will not be processed for payment even if WooCommerce is in use.</small></div>', 'bookyourtravel' ), $allowed_tags) );
		$this->form_fields[] = (object)array( 'type' => 'checkbox', 'value' => '1', 'role' => 'internal', 'name' => 'accommodation_hide_inquiry_form', 'id' => 'fes_accommodation_hide_inquiry_form', 'description' => wp_kses(__( '<div>Hide inquiry form?<br /><small>Do you want to not show the inquiry form and inquiry button in right hand sidebar for this accommodation?</small></div>', 'bookyourtravel' ), $allowed_tags) );

		$room_types_str = '';
		$room_type_query = $bookyourtravel_room_type_helper->list_room_types($this->get_author_id_for_list(), array('publish', 'draft'));
		if ($room_type_query->have_posts()) {
			while ($room_type_query->have_posts()) {
				$room_type_query->the_post();
				global $post;				
				$room_types_str .= "{$post->ID}::{$post->post_title},";
			}
		}
		$room_types_str = rtrim($room_types_str, ',');
		wp_reset_postdata();
		$this->form_fields[] = (object)array( 'type' => 'checkbox', 'role' => 'internal', 'name' => 'room_types', 'id' => 'fes_room_types', 'description' => esc_html__( 'Room types', 'bookyourtravel' ), 'values' => $room_types_str, 'class' => 'checkboxes', 'container_class_override' => 'room_types' );
		
		$star_count_str = "0::0,1::1,2::2,3::3,4::4,5::5";
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'accommodation_star_count', 'id' => 'fes_accommodation_star_count', 'description' => esc_html__( 'Star count', 'bookyourtravel' ), 'values' => $star_count_str, 'class' => 'select' );

		$taxonomies = array( 'accommodation_type' );
		$args = array( 'hide_empty' => false, 'fields' => 'all' ); 
		$accommodation_types = get_terms($taxonomies, $args);
		$accommodation_types_str = '::' . esc_html__('Select accommodation type', 'bookyourtravel') . ',';
		if (isset($accommodation_types)) {		
			foreach ($accommodation_types as $accommodation_type) {
				if (isset($accommodation_type) && isset($accommodation_type->term_id) && isset($accommodation_type->name)) {
					$accommodation_types_str .= "{$accommodation_type->term_id}::{$accommodation_type->name},";
				}
			}
		}
		$accommodation_types_str = rtrim($accommodation_types_str, ',');				
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'accommodation_type', 'id' => 'fes_accommodation_type', 'description' => esc_html__( 'Accommodation type', 'bookyourtravel' ), 'values' => $accommodation_types_str, 'class' => 'select required' );

		$locations_str = '::' . esc_html__('Select location', 'bookyourtravel') . ',';
		$location_results = $bookyourtravel_location_helper->list_locations();
		if ( count($location_results) > 0 && $location_results['total'] > 0 ) {
			foreach ($location_results['results'] as $location_result) {
				$locations_str .= "{$location_result->ID}::{$location_result->post_title},";
			}		
		}
		$locations_str = rtrim($locations_str, ',');
		wp_reset_postdata();
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'accommodation_location_post_id', 'id' => 'fes_accommodation_location_post_id', 'description' => esc_html__( 'Location', 'bookyourtravel' ), 'values' => $locations_str, 'class' => 'select required' );
		
		$taxonomies = array( 'facility' );
		$args = array( 'hide_empty' => false, 'fields' => 'all' ); 
		$facilities = get_terms($taxonomies, $args);
		$facilities_str = '';
		if (isset($facilities)) {		
			foreach ($facilities as $facility) {
				if (isset($facility) && isset($facility->term_id) && isset($facility->name)) {		
					$facilities_str .= "{$facility->term_id}::{$facility->name},";
				}
			}
		}
		$facilities_str = rtrim($facilities_str, ',');
		if (!empty($facilities_str)) {
			$this->form_fields[] = (object)array( 'type' => 'checkbox', 'role' => 'internal', 'name' => 'facilities', 'id' => 'fes_facilities', 'description' => esc_html__( 'Facilities', 'bookyourtravel' ), 'values' => $facilities_str, 'class' => 'checkboxes' );
		}
				
		$taxonomies = array( 'acc_tag' );
		$args = array( 'hide_empty' => false, 'fields' => 'all' ); 
		$tags = get_terms($taxonomies, $args);
		$tags_str = '';
		if (isset($tags)) {
			foreach ($tags as $tag) {
				if (isset($tag) && isset($tag->term_id) && isset($tag->name)) {		
					$tags_str .= "{$tag->term_id}::{$tag->name},";
				}
			}
		}
		$tags_str = rtrim($tags_str, ',');
		if (!empty($tags_str)) {
			$this->form_fields[] = (object)array( 'type' => 'checkbox', 'role' => 'internal', 'name' => 'acc_tag', 'id' => 'fes_acc_tag', 'description' => esc_html__( 'Tags', 'bookyourtravel' ), 'values' => $tags_str, 'class' => 'checkboxes' );
		}
				
		$min_days_stay_str = "1::1,2::2,3::3,4::4,5::5,6::6,7::7,8::8,9::9,10::10,11::11,12::12,13::13,14::14,15::15,16::16,17::17,18::18,19::19,20::20";
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'accommodation_min_days_stay', 'id' => 'fes_accommodation_min_days_stay', 'description' => esc_html__( 'Minimum days stay', 'bookyourtravel' ), 'values' => $min_days_stay_str, 'class' => 'select', 'container_class_override' => '' );

		$max_days_stay_str = "0::0,1::1,2::2,3::3,4::4,5::5,6::6,7::7,8::8,9::9,10::10,11::11,12::12,13::13,14::14,15::15,16::16,17::17,18::18,19::19,20::20";
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'accommodation_max_days_stay', 'id' => 'fes_accommodation_max_days_stay', 'description' => esc_html__( 'Maximum days stay', 'bookyourtravel' ), 'values' => $max_days_stay_str, 'class' => 'select', 'container_class_override' => '' );

		$min_adult_count_str = "1::1,2::2,3::3,4::4,5::5,6::6,7::7,8::8,9::9,10::10,11::11,12::12,13::13,14::14,15::15,16::16,17::17,18::18,19::19,20::20";
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'accommodation_min_count', 'id' => 'fes_accommodation_min_count', 'description' => esc_html__( 'Minimum adult count', 'bookyourtravel' ), 'values' => $min_adult_count_str, 'class' => 'select', 'container_class_override' => 'not_room_types' );

		$max_adult_count_str = "0::0,1::1,2::2,3::3,4::4,5::5,6::6,7::7,8::8,9::9,10::10,11::11,12::12,13::13,14::14,15::15,16::16,17::17,18::18,19::19,20::20";
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'accommodation_max_count', 'id' => 'fes_accommodation_max_count', 'description' => esc_html__( 'Maximum adult count', 'bookyourtravel' ), 'values' => $max_adult_count_str, 'class' => 'select', 'container_class_override' => 'not_room_types' );

		$min_child_count_str = "0::0,1::1,2::2,3::3,4::4,5::5,6::6,7::7,8::8,9::9,10::10,11::11,12::12,13::13,14::14,15::15,16::16,17::17,18::18,19::19,20::20";
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'accommodation_min_child_count', 'id' => 'fes_accommodation_min_child_count', 'description' => esc_html__( 'Minimum child count', 'bookyourtravel' ), 'values' => $min_child_count_str, 'class' => 'select', 'container_class_override' => 'not_room_types' );

		$max_child_count_str = "0::0,1::1,2::2,3::3,4::4,5::5,6::6,7::7,8::8,9::9,10::10,11::11,12::12,13::13,14::14,15::15,16::16,17::17,18::18,19::19,20::20";
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'accommodation_max_child_count', 'id' => 'fes_accommodation_max_child_count', 'description' => esc_html__( 'Maximum child count', 'bookyourtravel' ), 'values' => $max_child_count_str, 'class' => 'select', 'container_class_override' => 'not_room_types' );
		
		$count_children_stay_free_str = "0::0,1::1,2::2,3::3,4::4,5::5";
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'accommodation_count_children_stay_free', 'id' => 'fes_accommodation_count_children_stay_free', 'description' => esc_html__( 'Count children stay free', 'bookyourtravel' ), 'values' => $count_children_stay_free_str, 'class' => 'select', 'container_class_override' => 'per_person' );

		$stay_start_days_str = '-1::' . __('Any day', 'bookyourtravel');		
		$days_of_week = BookYourTravel_Theme_Utils::get_php_days_of_week();
		foreach ($days_of_week as $key => $label) {
			$stay_start_days_str .= sprintf(",%s::%s", $key, $label); 
		}
	
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'accommodation_checkin_week_day', 'id' => 'fes_accommodation_checkin_week_day', 'description' => esc_html__( 'Allowed check-in day of the week for stay', 'bookyourtravel' ), 'values' => $stay_start_days_str, 'class' => 'select', 'container_class_override' => '' );		
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'accommodation_checkout_week_day', 'id' => 'fes_accommodation_checkout_week_day', 'description' => esc_html__( 'Allowed check-out day of the week for stay', 'bookyourtravel' ), 'values' => $stay_start_days_str, 'class' => 'select', 'container_class_override' => '' );		
		
		$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => 'accommodation_address', 'id' => 'fes_accommodation_address', 'description' => esc_html__( 'Address', 'bookyourtravel' ) );
		$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => 'accommodation_website_address', 'id' => 'fes_accommodation_website_address', 'description' => esc_html__( 'Website address', 'bookyourtravel' ) );
		$this->form_fields[] = (object)array( 'type' => 'textarea', 'role' => 'content', 'name' => 'accommodation_availability_text', 'id' => 'fes_accommodation_availability_text', 'description' => esc_html__( 'Availability extra text', 'bookyourtravel' ), 'wysiwyg_enabled' => true  );
		$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => 'accommodation_contact_email', 'id' => 'fes_accommodation_contact_email', 'description' => esc_html__( 'Contact email addresses (separate multiple addresses with semi-colon ;)', 'bookyourtravel' ) );
		$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => 'accommodation_latitude', 'id' => 'fes_accommodation_latitude', 'description' => esc_html__( 'Latitude coordinates', 'bookyourtravel' ) );
		$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => 'accommodation_longitude', 'id' => 'fes_accommodation_longitude', 'description' => esc_html__( 'Longitude coordinates', 'bookyourtravel' ) );
		
		$accommodation_extra_fields = of_get_option('accommodation_extra_fields');
		if (!is_array($accommodation_extra_fields) || count($accommodation_extra_fields) == 0) {
			$accommodation_extra_fields = $default_accommodation_extra_fields;			
		}
		
		foreach ($accommodation_extra_fields as $extra_field) {
		
			$field_is_hidden = isset($extra_field['hide']) ? intval($extra_field['hide']) : 0;
			
			if (!$field_is_hidden) {
			
				$field_id = 'accommodation_' . (isset($extra_field['id']) ? $extra_field['id'] : '');
				$field_label = isset($extra_field['label']) ? $extra_field['label'] : '';
				$field_type = isset($extra_field['type']) ? $extra_field['type'] : '';
				
				if ($field_type == 'text') {
					$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => $field_id, 'id' => 'fes_' . $field_id, 'description' => $field_label );
				} elseif ($field_type == 'textarea') {
					$this->form_fields[] = (object)array( 'type' => 'textarea', 'role' => 'content', 'name' => $field_id, 'id' => 'fes_' . $field_id, 'description' => $field_label, 'wysiwyg_enabled' => true  );
				} elseif ($field_type == 'image') {
					$this->form_fields[] = (object)array( 'type' => 'file', 'role' => 'file', 'name' => $field_id, 'id' => 'fes_' . $field_id, 'multiple' => false, 'description' => $field_label );
				}
			}		
		}	
	}
	
	function initialize_car_rental_fields() {	
	
		global $bookyourtravel_location_helper;
	
		$this->form_fields = array();

		$allowed_tags = array();
		$allowed_tags['div'] = array('class' => array(), 'id' => array(), 'style' => array());
		$allowed_tags['br'] = array();
		$allowed_tags['small'] = array();
		
		$this->form_fields[] = (object)array( 'type' => 'checkbox', 'value' => '1', 'role' => 'internal', 'name' => 'car_rental_is_reservation_only', 'id' => 'fes_car_rental_is_reservation_only', 'description' => wp_kses(__( '<div>Is for reservation only?<br /><small>If this option is checked, then this particular car_rental will not be processed for payment even if WooCommerce is in use.</small></div>', 'bookyourtravel' ), $allowed_tags) );
		$this->form_fields[] = (object)array( 'type' => 'checkbox', 'value' => '1', 'role' => 'internal', 'name' => 'car_rental_hide_inquiry_form', 'id' => 'fes_car_rental_hide_inquiry_form', 'description' => wp_kses(__( '<div>Hide inquiry form?<br /><small>Do you want to not show the inquiry form and inquiry button in right hand sidebar for this car_rental?</small></div>', 'bookyourtravel' ), $allowed_tags) );

		$taxonomies = array( 'car_type' );
		$args = array( 'hide_empty' => false, 'fields' => 'all' ); 
		$car_rental_types = get_terms($taxonomies, $args);
		$car_rental_types_str = '::' . esc_html__('Select car type', 'bookyourtravel') . ',';
		foreach ($car_rental_types as $car_rental_type) {
			$car_rental_types_str .= "{$car_rental_type->term_id}::{$car_rental_type->name},";
		}
		$car_rental_types_str = rtrim($car_rental_types_str, ',');				
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'car_type', 'id' => 'fes_car_type', 'description' => esc_html__( 'Car type', 'bookyourtravel' ), 'values' => $car_rental_types_str, 'class' => 'select required' );

		$locations_str = '::' . esc_html__('Select location', 'bookyourtravel') . ',';
		$location_results = $bookyourtravel_location_helper->list_locations();
		if ( count($location_results) > 0 && $location_results['total'] > 0 ) {
			foreach ($location_results['results'] as $location_result) {
				$locations_str .= "{$location_result->ID}::{$location_result->post_title},";
			}		
		}
		$locations_str = rtrim($locations_str, ',');
		wp_reset_postdata();
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'car_rental_location_post_id', 'id' => 'fes_car_rental_location_post_id', 'description' => esc_html__( 'Location', 'bookyourtravel' ), 'values' => $locations_str, 'class' => 'select required' );
		
		$taxonomies = array( 'car_rental_tag' );
		$args = array( 'hide_empty' => false, 'fields' => 'all' ); 
		$tags = get_terms($taxonomies, $args);
		$tags_str = '';
		foreach ($tags as $tag) {
			$tags_str .= "{$tag->term_id}::{$tag->name},";
		}
		$tags_str = rtrim($tags_str, ',');
		if (!empty($tags_str)) {
			$this->form_fields[] = (object)array( 'type' => 'checkbox', 'role' => 'internal', 'name' => 'car_rental_tag', 'id' => 'fes_car_rental_tag', 'description' => esc_html__( 'Tags', 'bookyourtravel' ), 'values' => $tags_str, 'class' => 'checkboxes' );
		}
				
		$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => 'car_rental_contact_email', 'id' => 'fes_car_rental_contact_email', 'description' => esc_html__( 'Contact email addresses (separate multiple addresses with semi-colon ;)', 'bookyourtravel' ) );
		
		$car_number_str = "1::1,2::2,3::3,4::4,5::5,6::6,7::7,8::8,9::9,10::10,11::11,12::12,13::13,14::14,15::15,16::16,17::17,18::18,19::19,20::20,21::21,22::22,23::23,24::24,25::25,26::26,27::27,28::28,29::29,30::30,31::31,32::32,33::33,34::34,35::35,36::36,37::37,38::38,39::39,40::40,41::41,42::42,43::43,44::44,45::45,46::46,47::47,48::48,49::49,50::50";
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'car_rental_number_of_cars', 'id' => 'fes_car_rental_number_of_cars', 'description' => esc_html__( 'Number of available cars', 'bookyourtravel' ), 'values' => $car_number_str, 'class' => 'select', 'container_class_override' => '' );

		$max_count_str = "0::0,1::1,2::2,3::3,4::4,5::5,6::6,7::7,8::8,9::9,10::10,11::11,12::12,13::13,14::14,15::15,16::16,17::17,18::18,19::19,20::20";
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'car_rental_max_count', 'id' => 'fes_car_rental_max_count', 'description' => esc_html__( 'Maximum number of people allowed in car', 'bookyourtravel' ), 'values' => $max_count_str, 'class' => 'select', 'container_class_override' => '' );

		$min_age_str = "1::1,2::2,3::3,4::4,5::5,6::6,7::7,8::8,9::9,10::10,11::11,12::12,13::13,14::14,15::15,16::16,17::17,18::18,19::19,20::20,21::21,22::22,23::23,24::24,25::25,26::26,27::27,28::28,29::29,30::30,31::31,32::32,33::33,34::34,35::35,36::36,37::37,38::38,39::39,40::40,41::41,42::42,43::43,44::44,45::45,46::46,47::47,48::48,49::49,50::50";
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'car_rental_min_age', 'id' => 'fes_car_rental_min_age', 'description' => esc_html__( 'Minimum age of passengers', 'bookyourtravel' ), 'values' => $min_age_str, 'class' => 'select', 'container_class_override' => '' );

		$this->form_fields[] = (object)array( 'type' => 'checkbox', 'value' => '1', 'role' => 'internal', 'name' => 'car_rental_is_unlimited_mileage', 'id' => 'fes_car_rental_is_unlimited_mileage', 'description' => wp_kses(__( '<div>Unlimited mileage?</div>', 'bookyourtravel' ), $allowed_tags) );		
		$this->form_fields[] = (object)array( 'type' => 'checkbox', 'value' => '1', 'role' => 'internal', 'name' => 'car_rental_is_air_conditioned', 'id' => 'fes_car_rental_is_air_conditioned', 'description' => wp_kses(__( '<div>Air-conditioned?</div>', 'bookyourtravel' ), $allowed_tags) );		

		$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => 'car_rental_price_per_day', 'id' => 'fes_car_rental_price_per_day', 'description' => esc_html__( 'What is the car\'s rental price per day?', 'bookyourtravel' ) );
		
		$car_rental_extra_fields = of_get_option('car_rental_extra_fields');
		if (!is_array($car_rental_extra_fields) || count($car_rental_extra_fields) == 0) {
			$car_rental_extra_fields = $default_car_rental_extra_fields;			
		}
		
		foreach ($car_rental_extra_fields as $extra_field) {
		
			$field_is_hidden = isset($extra_field['hide']) ? intval($extra_field['hide']) : 0;
			
			if (!$field_is_hidden) {
			
				$field_id = 'car_rental_' . (isset($extra_field['id']) ? $extra_field['id'] : '');
				$field_label = isset($extra_field['label']) ? $extra_field['label'] : '';
				$field_type = isset($extra_field['type']) ? $extra_field['type'] : '';
				
				if ($field_type == 'text') {
					$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => $field_id, 'id' => 'fes_' . $field_id, 'description' => $field_label );
				} elseif ($field_type == 'textarea') {
					$this->form_fields[] = (object)array( 'type' => 'textarea', 'role' => 'content', 'name' => $field_id, 'id' => 'fes_' . $field_id, 'description' => $field_label, 'wysiwyg_enabled' => true  );
				} elseif ($field_type == 'image') {
					$this->form_fields[] = (object)array( 'type' => 'file', 'role' => 'file', 'name' => $field_id, 'id' => 'fes_' . $field_id, 'multiple' => false, 'description' => $field_label );
				}
			}		
		}	
	}
	
	function initialize_tour_fields() {	
	
		global $bookyourtravel_location_helper, $bookyourtravel_tour_helper;
	
		$this->form_fields = array();

		$allowed_tags = array();
		$allowed_tags['div'] = array('class' => array(), 'id' => array(), 'style' => array());
		$allowed_tags['br'] = array();
		$allowed_tags['small'] = array();
		
		$this->form_fields[] = (object)array( 'type' => 'checkbox', 'value' => '1', 'role' => 'internal', 'name' => 'tour_is_price_per_group', 'id' => 'fes_tour_is_price_per_group', 'description' => wp_kses(__( '<div>Is priced per group?<br /><small>Otherwise it\'s priced on a per-person basis</small></div>', 'bookyourtravel' ), $allowed_tags) );
		$this->form_fields[] = (object)array( 'type' => 'checkbox', 'value' => '1', 'role' => 'internal', 'name' => 'tour_is_reservation_only', 'id' => 'fes_tour_is_reservation_only', 'description' => wp_kses(__( '<div>Is for reservation only?<br /><small>If this option is checked, then this particular tour will not be processed for payment even if WooCommerce is in use.</small></div>', 'bookyourtravel' ), $allowed_tags) );
		$this->form_fields[] = (object)array( 'type' => 'checkbox', 'value' => '1', 'role' => 'internal', 'name' => 'tour_hide_inquiry_form', 'id' => 'fes_tour_hide_inquiry_form', 'description' => wp_kses(__( '<div>Hide inquiry form?<br /><small>Do you want to not show the inquiry form and inquiry button in right hand sidebar for this tour?</small></div>', 'bookyourtravel' ), $allowed_tags) );

		$taxonomies = array( 'tour_type' );
		$args = array( 'hide_empty' => false, 'fields' => 'all' ); 
		$tour_types = get_terms($taxonomies, $args);
		$tour_types_str = '::' . esc_html__('Select tour type', 'bookyourtravel') . ',';
		foreach ($tour_types as $tour_type) {
			$tour_types_str .= "{$tour_type->term_id}::{$tour_type->name},";
		}
		$tour_types_str = rtrim($tour_types_str, ',');				
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'tour_type', 'id' => 'fes_tour_type', 'description' => esc_html__( 'Tour type', 'bookyourtravel' ), 'values' => $tour_types_str, 'class' => 'select required' );

		$locations_str = '';
		$location_results = $bookyourtravel_location_helper->list_locations();
		if ( count($location_results) > 0 && $location_results['total'] > 0 ) {
			foreach ($location_results['results'] as $location_result) {
				$locations_str .= "{$location_result->ID}::{$location_result->post_title},";
			}		
		}
		$locations_str = rtrim($locations_str, ',');
		wp_reset_postdata();
		$this->form_fields[] = (object)array( 'type' => 'checkbox', 'role' => 'internal', 'name' => 'locations', 'id' => 'fes_locations', 'description' => esc_html__( 'Locations', 'bookyourtravel' ), 'values' => $locations_str, 'class' => 'checkboxes' );
		
		$taxonomies = array( 'tour_tag' );
		$args = array( 'hide_empty' => false, 'fields' => 'all' ); 
		$tags = get_terms($taxonomies, $args);
		$tags_str = '';
		foreach ($tags as $tag) {
			$tags_str .= "{$tag->term_id}::{$tag->name},";
		}
		$tags_str = rtrim($tags_str, ',');
		if (!empty($tags_str)) {
			$this->form_fields[] = (object)array( 'type' => 'checkbox', 'role' => 'internal', 'name' => 'tour_tag', 'id' => 'fes_tour_tag', 'description' => esc_html__( 'Tags', 'bookyourtravel' ), 'values' => $tags_str, 'class' => 'checkboxes' );
		}

		$this->form_fields[] = (object)array( 'type' => 'textarea', 'role' => 'content', 'name' => 'tour_availability_text', 'id' => 'fes_tour_availability_text', 'description' => esc_html__( 'Availability extra text', 'bookyourtravel' ), 'wysiwyg_enabled' => true  );
		$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => 'tour_contact_email', 'id' => 'fes_tour_contact_email', 'description' => esc_html__( 'Contact email addresses (separate multiple addresses with semi-colon ;)', 'bookyourtravel' ) );
		$this->form_fields[] = (object)array( 'type' => 'textarea', 'role' => 'content', 'name' => 'tour_map_code', 'id' => 'fes_tour_map_code', 'description' => esc_html__( 'Map code', 'bookyourtravel' ), 'wysiwyg_enabled' => false  );
		
		$tour_extra_fields = of_get_option('tour_extra_fields');
		if (!is_array($tour_extra_fields) || count($tour_extra_fields) == 0) {
			$tour_extra_fields = $default_tour_extra_fields;			
		}
		
		foreach ($tour_extra_fields as $extra_field) {
		
			$field_is_hidden = isset($extra_field['hide']) ? intval($extra_field['hide']) : 0;
			
			if (!$field_is_hidden) {
			
				$field_id = 'tour_' . (isset($extra_field['id']) ? $extra_field['id'] : '');
				$field_label = isset($extra_field['label']) ? $extra_field['label'] : '';
				$field_type = isset($extra_field['type']) ? $extra_field['type'] : '';
				
				if ($field_type == 'text') {
					$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => $field_id, 'id' => 'fes_' . $field_id, 'description' => $field_label );
				} elseif ($field_type == 'textarea') {
					$this->form_fields[] = (object)array( 'type' => 'textarea', 'role' => 'content', 'name' => $field_id, 'id' => 'fes_' . $field_id, 'description' => $field_label, 'wysiwyg_enabled' => true  );
				} elseif ($field_type == 'image') {
					$this->form_fields[] = (object)array( 'type' => 'file', 'role' => 'file', 'name' => $field_id, 'id' => 'fes_' . $field_id, 'multiple' => false, 'description' => $field_label );
				}
			}		
		}	
	}
	
	function initialize_cruise_fields() {	
	
		global $bookyourtravel_location_helper, $bookyourtravel_cabin_type_helper;
	
		$this->form_fields = array();

		$allowed_tags = array();
		$allowed_tags['div'] = array('class' => array(), 'id' => array(), 'style' => array());
		$allowed_tags['br'] = array();
		$allowed_tags['small'] = array();
		
		$this->form_fields[] = (object)array( 'type' => 'checkbox', 'value' => '1', 'role' => 'internal', 'name' => 'cruise_is_price_per_person', 'id' => 'fes_cruise_is_price_per_person', 'description' => wp_kses(__( '<div>Is priced per person?<br /><small>Otherwise it\'s priced on a per-cabin basis</small></div>', 'bookyourtravel' ), $allowed_tags) );
		$this->form_fields[] = (object)array( 'type' => 'checkbox', 'value' => '1', 'role' => 'internal', 'name' => 'cruise_is_reservation_only', 'id' => 'fes_cruise_is_reservation_only', 'description' => wp_kses(__( '<div>Is for reservation only?<br /><small>If this option is checked, then this particular cruise will not be processed for payment even if WooCommerce is in use.</small></div>', 'bookyourtravel' ), $allowed_tags) );
		$this->form_fields[] = (object)array( 'type' => 'checkbox', 'value' => '1', 'role' => 'internal', 'name' => 'cruise_hide_inquiry_form', 'id' => 'fes_cruise_hide_inquiry_form', 'description' => wp_kses(__( '<div>Hide inquiry form?<br /><small>Do you want to not show the inquiry form and inquiry button in right hand sidebar for this cruise?</small></div>', 'bookyourtravel' ), $allowed_tags) );

		$cabin_types_str = '';
		$cabin_type_query = $bookyourtravel_cabin_type_helper->list_cabin_types($this->get_author_id_for_list(), array('publish', 'draft'));
		if ($cabin_type_query->have_posts()) {
			while ($cabin_type_query->have_posts()) {
				$cabin_type_query->the_post();
				global $post;				
				$cabin_types_str .= "{$post->ID}::{$post->post_title},";
			}
		}
		
		$cabin_types_str = rtrim($cabin_types_str, ',');
		wp_reset_postdata();
		$this->form_fields[] = (object)array( 'type' => 'checkbox', 'role' => 'internal', 'name' => 'cabin_types', 'id' => 'fes_cabin_types', 'description' => esc_html__( 'Cabin types', 'bookyourtravel' ), 'values' => $cabin_types_str, 'class' => 'checkboxes', 'container_class_override' => 'cabin_types' );
		
		$taxonomies = array( 'cruise_type' );
		$args = array( 'hide_empty' => false, 'fields' => 'all' ); 
		$cruise_types = get_terms($taxonomies, $args);
		$cruise_types_str = '::' . esc_html__('Select cruise type', 'bookyourtravel') . ',';
		foreach ($cruise_types as $cruise_type) {
			$cruise_types_str .= "{$cruise_type->term_id}::{$cruise_type->name},";
		}
		$cruise_types_str = rtrim($cruise_types_str, ',');				
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'cruise_type', 'id' => 'fes_cruise_type', 'description' => esc_html__( 'Cruise type', 'bookyourtravel' ), 'values' => $cruise_types_str, 'class' => 'select required' );

		$locations_str = '';
		$location_results = $bookyourtravel_location_helper->list_locations();
		if ( count($location_results) > 0 && $location_results['total'] > 0 ) {
			foreach ($location_results['results'] as $location_result) {
				$locations_str .= "{$location_result->ID}::{$location_result->post_title},";
			}		
		}
		$locations_str = rtrim($locations_str, ',');
		wp_reset_postdata();
		$this->form_fields[] = (object)array( 'type' => 'checkbox', 'role' => 'internal', 'name' => 'locations', 'id' => 'fes_locations', 'description' => esc_html__( 'Locations', 'bookyourtravel' ), 'values' => $locations_str, 'class' => 'checkboxes' );
		
		$taxonomies = array( 'facility' );
		$args = array( 'hide_empty' => false, 'fields' => 'all' ); 
		$facilities = get_terms($taxonomies, $args);
		$facilities_str = '';
		foreach ($facilities as $facility) {
			$facilities_str .= "{$facility->term_id}::{$facility->name},";
		}
		$facilities_str = rtrim($facilities_str, ',');
		if (!empty($facilities_str)) {
			$this->form_fields[] = (object)array( 'type' => 'checkbox', 'role' => 'internal', 'name' => 'facilities', 'id' => 'fes_facilities', 'description' => esc_html__( 'Facilities', 'bookyourtravel' ), 'values' => $facilities_str, 'class' => 'checkboxes' );
		}
				
		$taxonomies = array( 'cruise_tag' );
		$args = array( 'hide_empty' => false, 'fields' => 'all' ); 
		$tags = get_terms($taxonomies, $args);
		$tags_str = '';
		foreach ($tags as $tag) {
			$tags_str .= "{$tag->term_id}::{$tag->name},";
		}
		$tags_str = rtrim($tags_str, ',');
		if (!empty($tags_str)) {
			$this->form_fields[] = (object)array( 'type' => 'checkbox', 'role' => 'internal', 'name' => 'cruise_tag', 'id' => 'fes_cruise_tag', 'description' => esc_html__( 'Tags', 'bookyourtravel' ), 'values' => $tags_str, 'class' => 'checkboxes' );
		}
				
		$count_children_stay_free_str = "0::0,1::1,2::2,3::3,4::4,5::5";
		$this->form_fields[] = (object)array( 'type' => 'select', 'role' => 'internal', 'name' => 'cruise_count_children_stay_free', 'id' => 'fes_cruise_count_children_stay_free', 'description' => esc_html__( 'Count children stay free', 'bookyourtravel' ), 'values' => $count_children_stay_free_str, 'class' => 'select', 'container_class_override' => 'per_person' );

		$this->form_fields[] = (object)array( 'type' => 'textarea', 'role' => 'content', 'name' => 'cruise_availability_text', 'id' => 'fes_cruise_availability_text', 'description' => esc_html__( 'Availability extra text', 'bookyourtravel' ), 'wysiwyg_enabled' => true  );
		$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => 'cruise_contact_email', 'id' => 'fes_cruise_contact_email', 'description' => esc_html__( 'Contact email addresses (separate multiple addresses with semi-colon ;)', 'bookyourtravel' ) );
		
		$cruise_extra_fields = of_get_option('cruise_extra_fields');
		if (!is_array($cruise_extra_fields) || count($cruise_extra_fields) == 0) {
			$cruise_extra_fields = $default_cruise_extra_fields;			
		}
		
		foreach ($cruise_extra_fields as $extra_field) {
		
			$field_is_hidden = isset($extra_field['hide']) ? intval($extra_field['hide']) : 0;
			
			if (!$field_is_hidden) {
			
				$field_id = 'cruise_' . (isset($extra_field['id']) ? $extra_field['id'] : '');
				$field_label = isset($extra_field['label']) ? $extra_field['label'] : '';
				$field_type = isset($extra_field['type']) ? $extra_field['type'] : '';
				
				if ($field_type == 'text') {
					$this->form_fields[] = (object)array( 'type' => 'text', 'role' => 'internal', 'name' => $field_id, 'id' => 'fes_' . $field_id, 'description' => $field_label );
				} elseif ($field_type == 'textarea') {
					$this->form_fields[] = (object)array( 'type' => 'textarea', 'role' => 'content', 'name' => $field_id, 'id' => 'fes_' . $field_id, 'description' => $field_label, 'wysiwyg_enabled' => true  );
				} elseif ($field_type == 'image') {
					$this->form_fields[] = (object)array( 'type' => 'file', 'role' => 'file', 'name' => $field_id, 'id' => 'fes_' . $field_id, 'multiple' => false, 'description' => $field_label );
				}
			}		
		}	
	}
	
	/**
	 * Display the upload post form
	 */
	function render_upload_form() {
	
		global $bookyourtravel_accommodation_helper, $bookyourtravel_tour_helper, $bookyourtravel_cruise_helper;
	
		if ( $this->user_has_correct_role() ) {
		
			if ( $this->content_type == 'accommodation' ) {
				$this->initialize_accommodation_fields();
			} elseif ( $this->content_type == 'room_type' ) {
				$this->initialize_room_type_fields();						
			} elseif ( $this->content_type == 'tour' ) {
				$this->initialize_tour_fields();		
			} elseif ( $this->content_type == 'cruise' ) {
				$this->initialize_cruise_fields();
			} elseif ( $this->content_type == 'cabin_type' ) {
				$this->initialize_cabin_type_fields();
			} elseif ( $this->content_type == 'car_rental' ) {
				$this->initialize_car_rental_fields();
			} elseif ( $this->content_type == 'vacancy' ) {
				$this->initialize_vacancy_fields();
			} elseif ( $this->content_type == 'tour_schedule' ) {
				$this->initialize_tour_schedule_fields();
			} elseif ( $this->content_type == 'cruise_schedule' ) {
				$this->initialize_cruise_schedule_fields();
			}
				
			// Reset postdata in case it got polluted somewhere
			wp_reset_postdata();
			
			$post_id = (int)get_the_id();

			ob_start();
	?>
			<script>
				window.adminAjaxUrl = <?php echo json_encode(admin_url('admin-ajax.php')); ?>;
			<?php
			if ( $this->content_type == 'vacancy' ) {	
			?>
				window.pricePerDayLabel = <?php echo json_encode(__('Price per day', 'bookyourtravel')); ?>;
				window.pricePerWeekLabel = <?php echo json_encode(__('Price per week', 'bookyourtravel')); ?>;
				window.pricePerMonthLabel = <?php echo json_encode(__('Price per month', 'bookyourtravel')); ?>;			
				window.pricePerDayChildLabel = <?php echo json_encode(__('Price per day child', 'bookyourtravel')); ?>;
				window.pricePerWeekChildLabel = <?php echo json_encode(__('Price per week child', 'bookyourtravel')); ?>;
				window.pricePerMonthChildLabel = <?php echo json_encode(__('Price per month child', 'bookyourtravel')); ?>;	
			<?php
				if ( $this->entry_id > 0 ) {
					$existing_vacancy = $bookyourtravel_accommodation_helper->get_accommodation_vacancy($this->entry_id);
				
					if ($existing_vacancy) {
						$accommodation_obj = new BookYourTravel_Accommodation($existing_vacancy->accommodation_id);
						
						$checkin_week_day = $accommodation_obj->get_checkin_week_day();
						$checkout_week_day = $accommodation_obj->get_checkout_week_day();
			?>		
				window.datepickerVacancyStartDate = <?php echo json_encode(date_i18n( $this->date_format, strtotime( $existing_vacancy->start_date ))) ; ?>;
				window.datepickerVacancyEndDate = <?php echo json_encode(date_i18n( $this->date_format, strtotime( $existing_vacancy->end_date ))) ; ?>;
				window.accommodationCheckinWeekday = <?php echo json_encode($checkin_week_day); ?>;
				window.accommodationCheckoutWeekday = <?php echo json_encode($checkout_week_day); ?>;
			<?php 
					}
				}
			} else if ( $this->content_type == 'tour_schedule' ) {	
				if ( $this->entry_id > 0 ) {
					$existing_schedule = $bookyourtravel_tour_helper->get_tour_schedule($this->entry_id);
				
					if ($existing_schedule) {
			?>		
				window.datepickerScheduleStartDate = <?php echo json_encode(date_i18n( $this->date_format, strtotime( $existing_schedule->start_date ))) ; ?>;
				window.datepickerScheduleEndDate = <?php echo json_encode(date_i18n( $this->date_format, strtotime( $existing_schedule->end_date ))) ; ?>;
			<?php 
					}
				}
			
			} else if ( $this->content_type == 'cruise_schedule' ) {	
				if ( $this->entry_id > 0 ) {
					$existing_schedule = $bookyourtravel_cruise_helper->get_cruise_schedule($this->entry_id);
				
					if ($existing_schedule) {
			?>		
				window.datepickerScheduleStartDate = <?php echo json_encode(date_i18n( $this->date_format, strtotime( $existing_schedule->start_date ))) ; ?>;
				window.datepickerScheduleEndDate = <?php echo json_encode(date_i18n( $this->date_format, strtotime( $existing_schedule->end_date ))) ; ?>;
			<?php 
					}
				}
			}
			?>
			</script>
			<form action="<?php echo esc_url(admin_url( 'admin-ajax.php' )) ?>" method="post" id="fes-upload-form-<?php echo esc_attr($this->content_type); ?>" name="fes-upload-form-<?php echo esc_attr($this->content_type); ?>" class="fes-upload-form fes-form-<?php echo esc_attr($this->content_type); ?>" enctype="multipart/form-data">
				<div class="fes-inner-wrapper">
	<?php
					if ( !empty( $_GET ) ) {
						$this->display_response_notices( $_GET );
					}

					$atts = array( 'type' => 'hidden', 'role' => 'internal', 'name' => 'post_author', 'id' => 'fes_post_author', 'value' =>  $this->get_current_user_id() );
					echo $this->render_input($atts);
					
					$atts = array( 'type' => 'hidden', 'role' => 'internal', 'name' => 'content_type', 'id' => 'fes_content_type', 'value' =>  $this->content_type );
					echo $this->render_input($atts);
					
					if ($this->entry_id > 0) {
						$atts = array( 'type' => 'hidden', 'role' => 'internal', 'name' => 'entry_id', 'value' => $this->entry_id, 'id' => 'fes_entry_id' );
						echo $this->render_input($atts);
					}
					
					if ( $this->content_type == 'accommodation' || 
						 $this->content_type == 'room_type' ||
						 $this->content_type == 'tour' ||
						 $this->content_type == 'cruise' ||
						 $this->content_type == 'cabin_type' ||
						 $this->content_type == 'car_rental' ) {
						
						$atts = array( 'type' => 'text', 'role' => 'title', 'name' => 'post_title',	'id' => 'fes_post_title', 'class' => 'required', 'description' =>  esc_html__( 'Title', 'bookyourtravel' ) );
						echo $this->render_input($atts);
						
						$atts = array( 'role' => 'content', 'name' => 'post_content', 'id' => 'fes_post_content', 'class' => 'required', 'description' =>  esc_html__( 'Description', 'bookyourtravel' ), 'wysiwyg_enabled' => true );
						echo $this->render_textarea($atts);

						if ($this->entry != null && $this->entry_id > 0) {

							// $atts = array( 'type' => 'file', 'role' => 'file', 'name' => 'featured_image', 'id' => 'fes_featured_image', 'multiple' => false, 'description' =>  esc_html__( 'Featured image', 'bookyourtravel' ) );
							// echo $this->render_input($atts);
							
							$feature_image_uri = $this->entry->get_main_image('medium');
							$featured_image_id = get_post_thumbnail_id( $this->entry_id );
							
							if ($featured_image_id > 0) {
								echo '<script>';
								echo 'window.featuredImageUri = ' . json_encode($feature_image_uri) . ';';
								echo 'window.featuredImageId = ' . json_encode($featured_image_id) . ';';
								echo '</script>';
							}
							
							echo '<div class="fes-input-wrapper">';
							echo '<label>' . esc_html__( 'Featured image', 'bookyourtravel' ) . '</label>';
							echo '<div id="featured-image-uploader" class="dropzone"></div><input type="hidden" id="featured-image-id" name="featured-image-id" value="' . $featured_image_id . '">';
							echo '</div>';

							echo '<script>';	
							echo 'window.galleryImageUris = [];';
							$gallery_images = $this->entry->get_custom_field( 'images' );
							
							$gallery_images_ids_str = '';
							if ($gallery_images && count($gallery_images) > 0) {
								for ( $i = 0; $i < count($gallery_images); $i++ ) { 
									$image = $gallery_images[$i];
									$image_meta_id = $image['image'];
									if (isset($image_meta_id) && $image_meta_id != '') {
										$image_src = wp_get_attachment_image_src($image_meta_id, 'full');	
										$image_src = $image_src[0];
										$gallery_images_ids_str .= $image_meta_id . ',';

										echo 'window.galleryImageUris.push({ image_id: ' . $image_meta_id . ', image_uri: ' . json_encode($image_src) . '});';
									}
								}
							}
							$gallery_images_ids_str = rtrim($gallery_images_ids_str, ',');
							echo '</script>';
							
							echo '<div class="fes-input-wrapper">';
							echo '<label>' . esc_html__( 'Gallery images', 'bookyourtravel' ) . '</label>';
							echo '<div id="gallery-image-uploader" class="dropzone"></div><input type="hidden" id="gallery-image-ids" name="gallery-image-ids" value="' . $gallery_images_ids_str . '">';
							echo '</div>';
						}
					}
					
					$submit_label = '';
					if ($this->entry_id != null ) {						
						$this->render_extra_fields();
						$submit_label = esc_html__( 'Update', 'bookyourtravel' );
					} else {
						if ($this->content_type == 'vacancy') {
							$this->render_extra_fields();
						} else if ($this->content_type == 'tour_schedule') {
							$this->render_extra_fields();
						} else if ($this->content_type == 'cruise_schedule') {
							$this->render_extra_fields();
						}

						$submit_label = esc_html__( 'Create', 'bookyourtravel' );
					}					
					
					$atts = array( 'type' => 'submit', 'role' => 'internal', 'name' => 'submit_button', 'id' => 'fes_submit_button', 'class' => 'btn gradient-button', 'value' =>  $submit_label );
					echo $this->render_input($atts);

					$atts = array( 'type' => 'hidden', 'role' => 'internal', 'name' => 'action', 'id' => 'fes_action', 'value' =>  'frontend_submit' );
					echo $this->render_input($atts);
	?>
					<?php wp_nonce_field('bookyourtravel_nonce'); ?>
					<input type="hidden" name="post_id" value="<?php echo esc_attr($post_id); ?>" />
					<div class="clear"></div>
				</div>
			</form>
	<?php
			return ob_get_clean();
		} 
		return '';
	}
	
	function render_extra_fields() {
		
		foreach ($this->form_fields  as $form_field) {

			if ($form_field->type == 'select')
				echo $this->render_select($form_field);
			elseif  ($form_field->type == 'checkbox' && isset($form_field->class) && $form_field->class == 'checkboxes')
				echo $this->render_checkboxes($form_field);
			elseif  ($form_field->type == 'textarea')
				echo $this->render_textarea($form_field);
			elseif ($form_field->type == 'checkbox')
				echo $this->render_checkbox_input($form_field);			
			elseif  ($form_field->type == 'text' || $form_field->type == 'file' || $form_field->type == 'hidden')
				echo $this->render_input($form_field);			
				
		}

	}
}

global $frontend_submit;
$frontend_submit = new Frontend_Submit();
$frontend_submit->init();