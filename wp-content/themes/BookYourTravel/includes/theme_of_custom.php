<?php

class BookYourTravel_Theme_Of_Custom extends BookYourTravel_BaseSingleton {
	
	protected function __construct() {
	
        // our parent class might contain shared code in its constructor
        parent::__construct();		
    }
	
    public function init() {
		
		add_filter( 'optionsframework_repeat_tab', array( $this, 'repeat_tab_option_type' ), 10, 3 );
		add_filter( 'optionsframework_repeat_extra_field', array( $this, 'repeat_extra_field_option_type' ), 10, 3 );
		add_filter( 'optionsframework_link_button_field', array( $this, 'link_button_field_option_type' ), 10, 3 );
		add_filter( 'optionsframework_dummy_text', array( $this, 'dummy_text_option_type' ), 10, 3 );
		add_filter( 'optionsframework_repeat_review_field', array( $this, 'repeat_review_field_option_type' ), 10, 3 );
		add_filter( 'optionsframework_repeat_form_field', array( $this, 'repeat_form_field_option_type' ), 10, 3 );
		add_filter( 'of_sanitize_repeat_form_field', array( $this, 'sanitize_repeat_form_field' ), 10, 2 );
		add_filter( 'of_sanitize_repeat_review_field', array( $this, 'sanitize_repeat_review_field' ), 10, 2 );
		add_filter( 'of_sanitize_repeat_extra_field', array( $this, 'sanitize_repeat_extra_field' ), 10, 2 );
		add_filter( 'of_sanitize_repeat_tab', array( $this, 'sanitize_repeat_tab' ), 10, 2 );
		add_action( 'optionsframework_custom_scripts', array( $this, 'of_bookyourtravel_options_script' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_optionsframework_scripts_styles' ) );
	}
	
	function enqueue_admin_optionsframework_scripts_styles() {
		wp_register_script('bookyourtravel-optionsframework-custom', BookYourTravel_Theme_Utils::get_file_uri('/includes/admin/optionsframework_custom.js'), array('jquery', 'jquery-ui-core', 'jquery-ui-sortable'), '1.0.0');
		wp_enqueue_script('bookyourtravel-optionsframework-custom');
	}
	
	public function register_dynamic_string_for_translation($name, $value) {
		if (function_exists('icl_register_string')) {
			icl_register_string('BookYourTravel Theme', $name, $value);
		}
	}
	
	public function get_translated_dynamic_string($name, $value) {
		if (function_exists('icl_t')) {
			return icl_t('BookYourTravel Theme', $name, $value);
		}
		return $value;
	}
	
	public static function of_element_exists($element_array, $element_id) {
		
		$exists = false;
		foreach ($element_array as $element) {		
			if (isset($element['id']) && $element['id'] == $element_id) {
				$exists = true;
				break;
			}		
		}
		return $exists;
	}

	/*
	 * Add custom, repeatable input fields to options framework thanks to HelgaTheViking
	 * https://gist.github.com/helgatheviking/6022215
	 */
	public function repeat_tab_option_type( $option_name, $option, $values ) {

		global $bookyourtravel_theme_of_default_fields, $repeatable_field_types;
		$max_tab_index = -1;
	
		$counter = 0;
		$used_indices = array();
		
		$default_values = $bookyourtravel_theme_of_default_fields->get_default_tab_array($option['id']);
		
		$values = BookYourTravel_Theme_Of_Default_Fields::merge_fields_and_defaults($values, $default_values);
		
		$output = '<div class="of-repeat-loop">';
		$output .= '<ul class="sortable of-repeat-tabs">';
	 
		if( is_array( $values ) ) { 
		
			foreach ( (array)$values as $value ) {
				
				if (isset($value['label']) && isset($value['id'])) {
				
					$tab_label 	= $value['label'];
					$tab_id		= $value['id'];
					$tab_hidden = isset($value['hide']) && $value['hide'] == '1' ? true : false;
					$tab_index = isset($value['index']) ? $value['index'] : $counter;
					
					$is_default = (count(BookYourTravel_Theme_Utils::custom_array_search($default_values, 'id', $tab_id)) > 0);
					
					if (in_array($tab_index, $used_indices)) {
						$tab_index = $this->find_available_index($tab_index, $used_indices);
					}
					$used_indices[] = $tab_index;
					
					$output .= '<li class="ui-state-default of-repeat-group">';
					
					$output .= '<div class="of-input-wrap">';
					$output .= $this->render_dynamic_field_input($option_name . '[' . $option['id'] . ']', 'of-input input-tab-label input-label-for-dynamic-id', $option_name . '[' . $option['id'] . ']['.$tab_index.'][label]', '', 'text', $tab_label, esc_html__('Enter tab label', 'bookyourtravel'), ' data-is-default="' . ($is_default ? '1' : '0') . '" data-original-id="' . esc_attr($tab_id) . '"');
					$output .= $this->render_dynamic_field_input($option_name . '[' . $option['id'] . ']', 'of-input input-tab-id input-dynamic-id', $option_name . '[' . $option['id'] . ']['.$tab_index.'][id]', '', 'text', $tab_id, esc_html__('Tab id is generated automatically.', 'bookyourtravel'), 'readonly="readonly" data-is-default="' . ($is_default ? '1' : '0') . '" data-original-id="' . esc_attr($tab_id) . '" data-id="' . esc_attr($tab_id) . '" data-parent="' . esc_attr($option['id']) . '"');
					$output .= '<div class="loading" style="display:none;"></div>';
					$output .= '</div>';
					$output .= $this->render_dynamic_checkbox($option_name, $option, '['.$tab_index.'][modify]', 'label-tab-modify', 'checkbox-tab-modify modify-dynamic-element-id', esc_html__('Modify id?', 'bookyourtravel'));
					$output .= $this->render_dynamic_checkbox($option_name, $option, '['.$tab_index.'][hide]', 'label-tab-hide', 'checkbox-tab-hide', esc_html__('Hidden?', 'bookyourtravel'), ($tab_hidden ? 'checked' : ''));
					$output .= '<input data-rel="' . esc_attr( $option_name . '[' . $option['id'] . ']' ) . '" class="input-index" name="' . esc_attr( $option_name . '[' . $option['id'] . ']['.$tab_index.'][index]' ) . '" type="hidden" value="' . $tab_index . '" />';
					
					if (!$is_default) {
						$output .= '<span class="ui-icon ui-icon-close"></span>';
					}
					$output .= '</li><!--.of-repeat-group-->';
			 
					$max_tab_index = $tab_index > $max_tab_index ? $tab_index : $max_tab_index;
					
					$counter++;
				}
			}
		}
	 
		$output .= '</ul><!--.sortable-->';
		$output .= '<input type="hidden" class="max_tab_index" value="' . $max_tab_index . '" />';
		$output .= '<a href="#" class="docopy_tab button icon add">' . esc_html__('Add tab', 'bookyourtravel') . '</a>';
		$output .= '</div><!--.of-repeat-loop-->';
	 
		return $output;
	}

	function repeat_form_field_option_type( $option_name, $option, $values ) {

		global $bookyourtravel_theme_of_default_fields, $form_field_types;
	
		$max_field_index = -1;
		
		$counter = 0;
		
		$default_values = $bookyourtravel_theme_of_default_fields->get_default_form_fields_array($option['id']);
		
		$values = BookYourTravel_Theme_Of_Default_Fields::merge_fields_and_defaults($values, $default_values);		
		
		$form_type = '';
		if ($option['id'] == 'inquiry_form_fields') {
			$form_type = 'inquiry';
		} else if ($option['id'] == 'booking_form_fields') {
			$form_type = 'booking';
		}
			
		$used_indices = array();
			
		$output = '<div class="of-repeat-loop">';		
		$output .= '<ul class="sortable of-repeat-form-fields">';
		
		if ( is_array( $values ) ) {

			foreach ( (array)$values as $key => $value ) {

				if (isset($value['label']) && isset($value['id'])) {
			
					$field_label 	= $value['label'];
					$field_id		= $value['id'];
					$field_hidden 	= isset($value['hide']) && $value['hide'] == '1' ? true : false;
					$field_required = isset($value['required']) && $value['required'] == '1' ? true : false;
					$field_index 	= isset($value['index']) ? intval($value['index']) : $counter;
					if (in_array($field_index, $used_indices)) {
						$field_index = $this->find_available_index($field_index, $used_indices);
					}
					$used_indices[] = $field_index;
					$field_type		= $value['type'];
					
					$is_default = (count(BookYourTravel_Theme_Utils::custom_array_search($default_values, 'id', $field_id)) > 0);
			
					$output .= '<li class="ui-state-default of-repeat-group">';

					$output .= '<div class="of-input-wrap">';
					$output .= $this->render_dynamic_field_input($option_name . '[' . $option['id'] . ']', 'of-input input-field-label input-label-for-dynamic-id', $option_name . '[' . $option['id'] . ']['.$field_index.'][label]', '', 'text', $field_label, esc_html__('Enter field label', 'bookyourtravel'), ' data-is-default="' . ($is_default ? '1' : '0') . '" data-original-id="' . esc_attr($field_id) . '"');
					$output .= $this->render_dynamic_field_input($option_name . '[' . $option['id'] . ']', 'of-input input-field-id input-' . $form_type . '-form-field-id input-dynamic-id', $option_name . '[' . $option['id'] . ']['.$field_index.'][id]', '', 'text', $field_id, esc_html__('Field id is generated automatically.', 'bookyourtravel'), 'readonly="readonly" data-is-default="' . ($is_default ? '1' : '0') . '" data-original-id="' . esc_attr($field_id) . '" data-id="' . esc_attr($field_id) . '" data-parent="' . esc_attr($option['id']) . '"');
					$output .= '<div class="loading" style="display:none;"></div>';
					$output .= '</div>';						

					$output .= $this->render_dynamic_checkbox($option_name, $option, '['.$field_index.'][modify]', 'label-field-modify', 'checkbox-field-modify modify-dynamic-element-id', esc_html__('Modify id?', 'bookyourtravel'));
					$output .= $this->render_dynamic_checkbox($option_name, $option, '['.$field_index.'][hide]', 'label-field-hide', 'checkbox-field-hide', esc_html__('Hidden?', 'bookyourtravel'), ($field_hidden ? 'checked' : ''));
					$output .= $this->render_dynamic_checkbox($option_name, $option, '['.$field_index.'][required]', 'label-field-required', 'checkbox-field-required', esc_html__('Is Required?', 'bookyourtravel'), ($field_required ? 'checked' : ''));

					$output .= $this->render_dynamic_select($option_name, $option, '['.$field_index.'][type]', 'label-field-type', 'select-field-type', esc_html__('Field type', 'bookyourtravel'), $field_type, $form_field_types);
					
					$output .= '<input data-rel="' . esc_attr( $option_name . '[' . $option['id'] . ']' ) . '" class="input-index" name="' . esc_attr( $option_name . '[' . $option['id'] . ']['.$field_index.'][index]' ) . '" type="hidden" value="' . $field_index . '" />';

					if (count(BookYourTravel_Theme_Utils::custom_array_search($default_values, 'id', $field_id)) == 0) {
						$output .= '<span class="ui-icon ui-icon-close"></span>';
					}
					$output .= '</li><!--.of-repeat-group-->';
			 
					$max_field_index = $field_index > $max_field_index ? $field_index : $max_field_index;
			 
					$counter++;
				}
			}
		}
	 
		$output .= '</ul><!--.sortable-->';
		$output .= '<input type="hidden" class="max_field_index" value="' . $max_field_index . '" />';
		$output .= '<a href="#" class="docopy_form_field button icon add">' . esc_html__('Add form field', 'bookyourtravel') . '</a>';
		$output .= '</div><!--.of-repeat-loop-->';

		return $output;
	}	

	function find_available_index($current_index, $indexes) {
		if (!in_array($current_index, $indexes)) {
			return $current_index;
		}
		$current_index++;
		return $this->find_available_index($current_index, $indexes);
	}
	
	function repeat_review_field_option_type( $option_name, $option, $values ) {

		global $bookyourtravel_theme_of_default_fields;
	
		$max_field_index = -1;
		$counter = 0;
		$used_indices = array();
		
		$default_values = $bookyourtravel_theme_of_default_fields->get_default_review_fields_array($option['id']);
		
		$values = BookYourTravel_Theme_Of_Default_Fields::merge_fields_and_defaults($values, $default_values);
		
		$post_type = '';
		if ($option['id'] == 'accommodation_review_fields') {
			$post_type = 'accommodation';
		} elseif ($option['id'] == 'tour_review_fields') {
			$post_type = 'tour';
		} elseif ($option['id'] == 'cruise_review_fields') {
			$post_type = 'cruise';
		} elseif ($option['id'] == 'car_rental_review_fields') {
			$post_type = 'car_rental';
		}
			
		$output = '<div class="of-repeat-loop">';		
		$output .= '<ul class="sortable of-repeat-review-fields">';

		if ( is_array( $values ) ) {

			foreach ( (array)$values as $key => $value ) {

				if (isset($value['label']) && isset($value['post_type']) &&	isset($value['id'])) {
			
					$field_label 	= $value['label'];
					$field_id		= $value['id'];
					$field_post_type= $value['post_type'];
					$field_hidden 	= isset($value['hide']) && $value['hide'] == '1' ? true : false;
					$field_index 	= isset($value['index']) ? $value['index'] : $counter;
					if (in_array($field_index, $used_indices)) {
						$field_index = $this->find_available_index($field_index, $used_indices);
					}
					$used_indices[] = $field_index;
			
					$is_default = (count(BookYourTravel_Theme_Utils::custom_array_search($default_values, 'id', $field_id)) > 0);			
			
					$output .= '<li class="ui-state-default of-repeat-group">';

					$output .= $this->render_dynamic_field_input($option_name . '[' . $option['id'] . ']', 'input-field-post-type', $option_name . '[' . $option['id'] . ']['.$field_index.'][post_type]', '', 'hidden', $field_post_type);

					$output .= '<div class="of-input-wrap">';
					$output .= $this->render_dynamic_field_input($option_name . '[' . $option['id'] . ']', 'of-input input-field-label input-label-for-dynamic-id', $option_name . '[' . $option['id'] . ']['.$field_index.'][label]', '', 'text', $field_label, esc_html__('Enter field label', 'bookyourtravel'), ' data-is-default="' . ($is_default ? '1' : '0') . '" data-original-id="' . esc_attr($field_id) . '"');
					$output .= $this->render_dynamic_field_input($option_name . '[' . $option['id'] . ']', 'of-input input-field-id input-review-field-id input-dynamic-id', $option_name . '[' . $option['id'] . ']['.$field_index.'][id]', '', 'text', $field_id, esc_html__('Field id is generated automatically.', 'bookyourtravel'), 'readonly="readonly" data-is-default="' . ($is_default ? '1' : '0') . '" data-original-id="' . esc_attr($field_id) . '" data-id="' . esc_attr($field_id) . '" data-parent="' . esc_attr($option['id']) . '"');
					$output .= '<div class="loading" style="display:none;"></div>';
					$output .= '</div>';						

					$output .= $this->render_dynamic_checkbox($option_name, $option, '['.$field_index.'][modify]', 'label-field-modify', 'checkbox-field-modify modify-dynamic-element-id', esc_html__('Modify id?', 'bookyourtravel'));
					$output .= $this->render_dynamic_checkbox($option_name, $option, '['.$field_index.'][hide]', 'label-field-hide', 'checkbox-field-hide', esc_html__('Hidden?', 'bookyourtravel'), ($field_hidden ? 'checked' : ''));

					$output .= '<input data-rel="' . esc_attr( $option_name . '[' . $option['id'] . ']' ) . '" class="input-index" name="' . esc_attr( $option_name . '[' . $option['id'] . ']['.$field_index.'][index]' ) . '" type="hidden" value="' . $field_index . '" />';

					
					if (count(BookYourTravel_Theme_Utils::custom_array_search($default_values, 'id', $field_id)) == 0) {
						$output .= '<span class="ui-icon ui-icon-close"></span>';
					}
					$output .= '</li><!--.of-repeat-group-->';
			 
					$max_field_index = $field_index > $max_field_index ? $field_index : $max_field_index;
			 
					$counter++;
				}
			}
		}
	 
		$output .= '</ul><!--.sortable-->';
		$output .= '<input type="hidden" class="max_field_index" value="' . $max_field_index . '" />';
		$output .= '<a href="#" class="docopy_review_field button icon add">' . esc_html__('Add review field', 'bookyourtravel') . '</a>';
		$output .= '</div><!--.of-repeat-loop-->';

		return $output;
	}
	
	function repeat_extra_field_option_type( $option_name, $option, $values ) {

		global $bookyourtravel_theme_of_default_fields, $repeatable_field_types, $default_accommodation_extra_fields, $default_tour_extra_fields, $default_car_rental_extra_fields, $default_location_extra_fields, $default_cruise_extra_fields;
		
		$counter = 0;
		$max_field_index = -1;
		$used_indices = array();
		
		$default_values = array();
		$tab_array = array();
		
		if ($option['id'] == 'accommodation_extra_fields') {
			$default_values = $default_accommodation_extra_fields;
			$tab_key = 'accommodation_tabs';
		} elseif ($option['id'] == 'tour_extra_fields') {
			$default_values = $default_tour_extra_fields;
			$tab_key = 'tour_tabs';
		} elseif ($option['id'] == 'car_rental_extra_fields') {
			$default_values = $default_car_rental_extra_fields;
			$tab_key = 'car_rental_tabs';
		} elseif ($option['id'] == 'location_extra_fields') {
			$default_values = $default_location_extra_fields;
			$tab_key = 'location_tabs';
		} elseif ($option['id'] == 'cruise_extra_fields') {
			$default_values = $default_cruise_extra_fields;
			$tab_key = 'cruise_tabs';
		}

		$tab_array = of_get_option($tab_key);
		$default_tab_array = $bookyourtravel_theme_of_default_fields->get_default_tab_array($tab_key);
		
		if (!is_array( $tab_array ) || count($tab_array) == 0 || count($tab_array) < count($default_tab_array)) {
			$tab_array = $default_tab_array;
		}
		
		$values = BookYourTravel_Theme_Of_Default_Fields::merge_fields_and_defaults($values, $default_values);

		$output = '<div class="of-repeat-loop">';
		
		if ($tab_array && count($tab_array) > 0) {

			$output .= '<ul class="sortable of-repeat-extra-fields">';

			if( is_array( $values ) && is_array($tab_array) ) {

				foreach ( (array)$values as $key => $value ) {
					if (isset($value['label']) && 
						isset($value['type']) &&
						isset($value['tab_id']) &&
						isset($value['id'])) {
						
						$field_label 	= $value['label'];
						$field_id		= $value['id'];
						$field_type		= $value['type'];
						$field_hidden	= isset($value['hide']) && $value['hide'] == '1' ? true : false;
						$field_index 	= isset($value['index']) ? $value['index'] : $counter;
						if (in_array($field_index, $used_indices)) {
							$field_index = $this->find_available_index($field_index, $used_indices);
						}
						$used_indices[] = $field_index;
						$tab_id			= $value['tab_id'];
						$is_default = (count(BookYourTravel_Theme_Utils::custom_array_search($default_values, 'id', $field_id)) > 0);		 
						
						$output .= '<li class="ui-state-default of-repeat-group">';
						
						$output .= '<div class="of-input-wrap">';
						$output .= $this->render_dynamic_field_input($option_name . '[' . $option['id'] . ']', 'of-input input-field-label input-label-for-dynamic-id', $option_name . '[' . $option['id'] . ']['.$field_index.'][label]', '', 'text', $field_label, esc_html__('Enter field label', 'bookyourtravel'), ' data-is-default="' . ($is_default ? '1' : '0') . '" data-original-id="' . esc_attr($field_id) . '"');
						$output .= $this->render_dynamic_field_input($option_name . '[' . $option['id'] . ']', 'of-input input-field-id input-dynamic-id', $option_name . '[' . $option['id'] . ']['.$field_index.'][id]', '', 'text', $field_id, esc_html__('Field id is generated automatically.', 'bookyourtravel'), 'readonly="readonly" data-is-default="' . ($is_default ? '1' : '0') . '" data-original-id="' . esc_attr($field_id) . '" data-id="' . esc_attr($field_id) . '" data-parent="' . esc_attr($option['id']) . '"');
						$output .= '<div class="loading" style="display:none;"></div>';
						$output .= '</div>';						
						$output .= $this->render_dynamic_checkbox($option_name, $option, '['.$field_index.'][modify]', 'label-field-modify', 'checkbox-field-modify modify-dynamic-element-id', esc_html__('Modify id?', 'bookyourtravel'));
						$output .= $this->render_dynamic_checkbox($option_name, $option, '['.$field_index.'][hide]', 'label-field-hide', 'checkbox-field-hide', esc_html__('Hidden?', 'bookyourtravel'), ($field_hidden ? 'checked' : ''));
						$output .= $this->render_dynamic_select($option_name, $option, '['.$field_index.'][type]', 'label-field-type', 'select-field-type', esc_html__('Field type', 'bookyourtravel'), $field_type, $repeatable_field_types);
						$output .= $this->render_dynamic_select($option_name, $option, '['.$field_index.'][tab_id]', 'label-field-tab', 'select-field-tab', esc_html__('Field tab', 'bookyourtravel'), $tab_id, $tab_array, 'label', 'id');

						$output .= '<input data-rel="' . esc_attr( $option_name . '[' . $option['id'] . ']' ) . '" class="input-index" name="' . esc_attr( $option_name . '[' . $option['id'] . ']['.$field_index.'][index]' ) . '" type="hidden" value="' . $field_index . '" />';
						
						if (count(BookYourTravel_Theme_Utils::custom_array_search($default_values, 'id', $field_id)) == 0) {
							$output .= '<span class="ui-icon ui-icon-close"></span>';
						}
						$output .= '</li><!--.of-repeat-group-->';
				 
						$max_field_index = $field_index > $max_field_index ? $field_index : $max_field_index;
						
						$counter++;
					}
				}
			}

			$output .= '</ul><!--.sortable-->';
			$output .= '<input type="hidden" class="max_field_index" value="' . $max_field_index . '" />';
			$output .= '<a href="#" class="docopy_field button icon add">' . esc_html__('Add field', 'bookyourtravel') . '</a>';
			
		} else {
			$output .= '<p>' . esc_html__('Please hit the "Save Options" button to create the initial collection of tabs so that extra fields can be associated with tabs correctly.', 'bookyourtravel') . '</p>';
		}
		$output .= '</div><!--.of-repeat-loop-->';

		return $output;
	}
	
	function render_dynamic_textbox($option_name, $option, $name_postfix, $label_css, $input_css, $label_text, $value = '') {

		$output = '';

		$output .= '<div class="of-input-wrap">';
		$output .= $this->render_dynamic_field_input($option_name . '[' . $option['id'] . ']', 'of-input ' . $input_css, $option_name . '[' . $option['id'] . ']' . $name_postfix, '', 'text', $value, $label_text);
		$output .= '</div>';

		return $output;
	}
	
	function render_dynamic_checkbox($option_name, $option, $name_postfix, $label_css, $input_css, $label_text, $extra_input_attributes = '') {
	
		$output = '';

		$output .= '<div class="of-check-wrap">';
		$output .= $this->render_dynamic_field_label($option_name . '[' . $option['id'] . ']', 'of-label ' . $label_css, $option_name . '[' . $option['id'] . ']' . $name_postfix, $label_text);
		$output .= $this->render_dynamic_field_input($option_name . '[' . $option['id'] . ']', 'of-checkbox ' . $input_css, $option_name . '[' . $option['id'] . ']' . $name_postfix, '', 'checkbox', '1', '', $extra_input_attributes);
		$output .= '</div>';

		return $output;
	}
	
	function render_dynamic_select($option_name, $option, $name_postfix, $label_css, $select_css, $label_text, $selected_value, $option_array, $text_key = '', $value_key = '') {
	
		$output = '';
		$output .= '<div class="of-select-wrap">';
		$output .= $this->render_dynamic_field_select( $option_name . '[' . $option['id'] . ']', 'of-select ' . $select_css, $option_name . '[' . $option['id'] . ']' . $name_postfix, '', $selected_value, $option_array, $text_key, $value_key );
		$output .= '</div>';

		return $output;
	}

	function link_button_field_option_type ( $option_name, $option, $values ) {

		$button_text = $option['name'];
		if (isset($option['text'])) {
			$button_text = $option['text'];
		}
	
		$output = '<div class="of-input">';
		$output .= '<a href="#" class="button-secondary of-button-field ' . esc_attr($option['id']) . '">' . esc_html($button_text) . '</a>';
		if ($option['id'] == 'synchronise_reviews' || $option['id'] == 'upgrade_bookyourtravel_db') {
			$output .= '<div style="display:none" class="loading"></div>';
		}
		$output .= '</div>';

		return $output;
	}
	
	function dummy_text_option_type ( $option_name, $option, $values) {
		return '';
	}
	
	function render_dynamic_field_select( $data_rel, $css_class, $name, $id, $selected_value, $options_array, $text_key = '', $value_key = '' ) {
	
		$output = '<select class="' . esc_attr($css_class) . '" name="' . esc_attr( $name ) . '" data-rel="' . esc_attr( $data_rel ) . '">';
		
		if (is_array($options_array) && count($options_array)) {
			
			if (!empty($text_key) && !empty($value_key)) {
				foreach($options_array as $option) {
				
					$option_text = isset($option[$text_key]) ? trim($option[$text_key]) : '';
					$option_value = isset($option[$value_key]) ? trim($option[$value_key]) : '';
					
					if (!empty($option_text) && !empty($option_value)) {
						$output .= '<option value="' . $option_value . '" ' . ($option_value == $selected_value ? 'selected' : '') . '>' . $option_text . '</option>';
					}
				} 
			} else {
				foreach($options_array as $key => $text) {
					$output .= '<option value="' . $key . '" ' . ($key == $selected_value ? 'selected' : '') . '>' . $text . '</option>';
				}
			}
			
		}		
		
		$output .= '</select>';
		
		return $output;
	}
	
	function render_dynamic_field_label( $data_rel, $css_class, $for, $text ) {
		return '<label data-rel="' . esc_attr( $data_rel ) . '" class="' . esc_attr($css_class) . '" for="' . esc_attr( $for ) . '">' . $text . '</label>';
	}

	function render_dynamic_field_input( $data_rel, $css_class, $name, $id, $type, $value, $placeholder_text = '', $extra_attributes = '' ) {
		return '<input ' . (!empty($placeholder_text) ? ' placeholder="' . esc_attr($placeholder_text). '"' : '') .' data-rel="' . esc_attr( $data_rel ) . '" class="' . esc_attr($css_class) . '" name="' . esc_attr( $name ) . '" type="' . esc_attr($type) . '" value="' . esc_attr( $value ) . '" ' . $extra_attributes . ' />';
	}
	
	function get_option_id_context($option_id) {

		$option_id_context = '';
		
		if ($option_id == 'location_extra_fields')
			$option_id_context = 'Location extra field';
		elseif ($option_id == 'location_tabs')
			$option_id_context = 'Location tab';
		elseif ($option_id == 'accommodation_extra_fields')
			$option_id_context = 'Accommodation extra field';
		elseif ($option_id == 'accommodation_tabs')
			$option_id_context = 'Accommodation tab';
		elseif ($option_id == 'tour_extra_fields')
			$option_id_context = 'Tour extra field';
		elseif ($option_id == 'tour_tabs')
			$option_id_context = 'Tour tab';
		elseif ($option_id == 'car_rental_extra_fields')
			$option_id_context = 'Car rental extra field';
		elseif ($option_id == 'car_rental_tabs')
			$option_id_context = 'Car rental tab';
		elseif ($option_id == 'cruise_extra_fields')
			$option_id_context = 'Cruise extra field';
		elseif ($option_id == 'cruise_tabs')
			$option_id_context = 'Cruise tab';
		elseif ($option_id == 'accommodation_review_fields')
			$option_id_context = 'Accommodation review field';
		elseif ($option_id == 'tour_review_fields')
			$option_id_context = 'Tour review field';	
		elseif ($option_id == 'cruise_review_fields')
			$option_id_context = 'Cruise review field';
		elseif ($option_id == 'car_rental_review_fields')
			$option_id_context = 'Car rental review field';
		elseif ($option_id == 'inquiry_form_fields')
			$option_id_context = 'Inquiry form field';
		elseif ($option_id == 'booking_form_fields')
			$option_id_context = 'Booking form field';
			
		return $option_id_context;
	}
	
	/*
	 * Sanitize Repeat review inputs
	 */
	function sanitize_repeat_review_field( $fields, $option ) {	
		
		$results = array();
		
		if (is_array($fields)) {
		
			for ($i = 0; $i < count($fields); $i++) { 
			
				if (isset($fields[$i])) {
				
					$field = $fields[$i];
					
					if (!isset($field['id']) && isset($field['label'])) {
						$field['id'] = 'review_' . URLify::filter($field['label']);
					}
					
					if (isset($field['label'])) {
						$this->register_dynamic_string_for_translation($this->get_option_id_context($option['id']) . ' ' . $field['label'], $field['label']);
					}
					
					$results[] = $field;
				}
			}
		}
		return $results;
	}
	
	/*
	 * Sanitize Repeat inputs
	 */
	function sanitize_repeat_extra_field( $fields, $option ) {
	
		$results = array();
		
		if (is_array($fields)) {
		
			for ($i = 0; $i < count($fields); $i++) {
			
				if (isset($fields[$i])) {
				
					$field = $fields[$i];
					
					$field_id = isset($field['id']) ? trim($field['id']) : '';
					$field_label = isset($field['label']) ? $field['label'] : '';
					$field_index = isset($field['index']) ? $field['index'] : 0;
					
					if (empty($field_id) && !empty($field_label)) {
						$field_id = URLify::filter($field_label . '-' . $field_index);
						$field_id = str_replace("-", "_", $field_id);
						$field['id'] = $field_id;
					}
						
					if (isset($field['label'])) {
						$this->register_dynamic_string_for_translation($this->get_option_id_context($option['id']) . ' ' . $field['label'], $field['label']);
					}
						
					$results[] = $field;
				}
			}
		}
		return $results;
	}
	
	/*
	 * Sanitize Repeat inputs
	 */
	function sanitize_repeat_form_field( $fields, $option ) {
	
		$results = array();
		
		if (is_array($fields)) {
		
			foreach ($fields as $field) {
					
					$field_id = isset($field['id']) ? trim($field['id']) : '';
					$field_label = isset($field['label']) ? $field['label'] : '';
					$field_index = isset($field['index']) ? $field['index'] : 0;
					
					if (empty($field_id) && !empty($field_label)) {
						$field_id = URLify::filter($field_label . '-' . $field_index);
						$field_id = str_replace("-", "_", $field_id);
						$field['id'] = $field_id;
					}
						
					if (isset($field['label'])) {
						$this->register_dynamic_string_for_translation($this->get_option_id_context($option['id']) . ' ' . $field['label'], $field['label']);
					}
						
					$results[] = $field;
			}
		}
		return $results;
	}
	
	/*
	 * Sanitize Repeat tabs
	 */
	function sanitize_repeat_tab( $tabs, $option ) {
		
		$results = array();
		
		if (is_array($tabs)) {
		
			for ($i = 0; $i < count($tabs); $i++) { 
			
				if (isset($tabs[$i])) {
				
					$tab = $tabs[$i];

					$tab_id = isset($tab['id']) ? trim($tab['id']) : '';
					$tab_label = isset($tab['label']) ? $tab['label'] : '';
					$tab_index = isset($tab['index']) ? $tab['index'] : 0;
					
					if (empty($tab_id) && !empty($tab_label)) {
						$tab_id = URLify::filter($tab_label . '-' . $tab_index);
						$tab_id = str_replace("-", "_", $tab_id);
						$tab['id'] = $tab_id;
					}
						
					if (isset($tab['label'])) {
						$this->register_dynamic_string_for_translation($this->get_option_id_context($option['id']) . ' ' . $tab['label'], $tab['label']);
					}

					$results[] = $tab;
				}
			}
		}
		return $results;
	}
	
	/*
	 * Custom repeating field scripts
	 * Add and Delete buttons
	 */
	function of_bookyourtravel_options_script() {	
		global $bookyourtravel_theme_globals;?>
		<style>
			#optionsframework .to-copy {display: none;}
			#optionsframework .controls .of-input-wrap { width: 70% !important;display:inline-block }
			#optionsframework .controls .of-input-wrap .of-input { width: 100% !important; display:inline-block; }
			#optionsframework .controls .of-check-wrap { width: 30% !important;display:inline-block }
		</style>
		<script type="text/javascript"><?php
			echo 'window.adminAjaxUrl = ' . json_encode( admin_url( 'admin-ajax.php' ) ) . ';';
			echo 'window.adminSiteUrl = ' . json_encode( admin_url( 'themes.php?page=options-framework' ) ) . ';';?>	
		</script>
	<?php
	}
}

// store the instance in a variable to be retrieved later and call init
$bookyourtravel_theme_of_custom = BookYourTravel_Theme_Of_Custom::get_instance();
$bookyourtravel_theme_of_custom->init();