<?php

class BookYourTravel_Theme_Utils {

	public static function wpml_translate_post($original_post_id, $post_type, $language_code) {
	 
		if (BookYourTravel_Theme_Utils::is_wpml_active()) {
		
			// Include WPML API
			include_once( WP_PLUGIN_DIR . '/sitepress-multilingual-cms/inc/wpml-api.php' );		
		
			global $wpdb, $sitepress;
			
			// Get default language
			$default_lang_code = wpml_get_default_language();

			if ($language_code != $default_lang_code) {			
		
				// Define title of translated post
				$translated_post = get_post( $original_post_id );
				$translated_post->post_title = $translated_post->post_title . ' (' . $language_code . ')';
				$translated_post->post_content = $translated_post->post_content . ' (' . $language_code . ')';
				$translated_post->icl_post_language = $language_code;
				$translated_post->ID = null;
			 
				// Create translated post
				$post_translated_id = wp_insert_post( $translated_post );
			 
				wpml_update_translatable_content( 'post_'. $post_type, $post_translated_id, $language_code ); 	// update language code
				
				$post_meta_keys = get_post_custom_keys($original_post_id);
				if (!empty($post_meta_keys))
				{
					foreach ($post_meta_keys as $meta_key) {
						$meta_values = get_post_custom_values($meta_key, $original_post_id);
						foreach ($meta_values as $meta_value) {
							$meta_value = maybe_unserialize($meta_value);
							add_post_meta($post_translated_id, $meta_key, $meta_value);
						}
					}				
				}
				
				$trid = wpml_get_content_trid('post_'. $post_type, $original_post_id); 	// get trid 
			 
				$sitepress->set_element_language_details($post_translated_id, 'post_'. $post_type, $trid, $language_code, $default_lang_code);				
			 
				// Return translated post ID
				return $post_translated_id;
			}
		}
		
		return -1;
	}

	public static function get_allowed_content_tags_array() {
			
		global $allowedtags;
		
		$allowedtags = array(
			'a' => array(
				'class' => array(), 'rel' => array(), 'style' => array(), 'id' => array(), 'href' => array(), 'title' => array()
			),
			'div' => array(
				'class' => array(), 'id' => array(), 'style' => array()
			),
			'span' => array(
				'class' => array(), 'id' => array(), 'style' => array()
			),
			'ul' => array(
				'id' => array(),			
				'class' => array()
			),			
			'li' => array(
				'class' => array(),	
			),	
			'p' => array(
				'class' => array(),
			),
			'b' => array(
				'class' => array(),
			),
			'i' => array(
				'class' => array(),
			),
			'h1' => array(
				'class' => array(),
			),
			'h2' => array(
				'class' => array(),
			),
			'h3' => array(
				'class' => array(),
			),
			'h4' => array(
				'class' => array(),
			),
			'h5' => array(
				'class' => array(),
			),
			'h6' => array(
				'class' => array(),
			),
			'em' => array(),
			'strong' => array(),
			'img' => array(
				'src' => array(),
				'title' => array(),
				'alt' => array()						
			)				
		);	
	
		return apply_filters( 'get_allowed_content_tags_array', $allowedtags );
	}

	public static function php_version_recent_enough() {

		$check = false;
		if (version_compare(phpversion(), '5.3.0', '>=')) {
			$check = true;
		}
		
		return $check;
	}

	public static function check_file_exists($relative_path_to_file) {
		return (file_exists(get_stylesheet_directory() . $relative_path_to_file) || file_exists(get_template_directory() . $relative_path_to_file));
	}

	public static function string_starts_with($haystack, $needle) {
		return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
	}
	
	public static function strip_tags_and_shorten_by_words($content, $words) {
		$content = wp_strip_all_tags($content);
		return implode(' ', array_slice(explode(' ', $content), 0, $words));
	}
	
	public static function get_php_days_of_week() {
	
		$days_of_week = array();

		$days_of_week[0] = esc_html__('Sunday', 'bookyourtravel'); 		
		$days_of_week[1] = esc_html__('Monday', 'bookyourtravel');
		$days_of_week[2] = esc_html__('Tuesday', 'bookyourtravel');
		$days_of_week[3] = esc_html__('Wednesday', 'bookyourtravel');
		$days_of_week[4] = esc_html__('Thursday', 'bookyourtravel');
		$days_of_week[5] = esc_html__('Friday', 'bookyourtravel');
		$days_of_week[6] = esc_html__('Saturday', 'bookyourtravel');
		
		return $days_of_week;
	}
	
	public static function get_days_of_week() {

		$days_of_week = array();
		
		$days_of_week[0] = esc_html__('Monday', 'bookyourtravel');
		$days_of_week[1] = esc_html__('Tuesday', 'bookyourtravel');
		$days_of_week[2] = esc_html__('Wednesday', 'bookyourtravel');
		$days_of_week[3] = esc_html__('Thursday', 'bookyourtravel');
		$days_of_week[4] = esc_html__('Friday', 'bookyourtravel');
		$days_of_week[5] = esc_html__('Saturday', 'bookyourtravel');
		$days_of_week[6] = esc_html__('Sunday', 'bookyourtravel'); 
		
		return $days_of_week;
	}

	/**
	 * Checks if a particular user has a role. 
	 * Returns true if a match was found.
	 * Thanks to http://docs.appthemes.com/tutorials/wordpress-check-user-role-function/
	 *
	 * @param string $role Role name.
	 * @param int $user_id (Optional) The ID of a user. Defaults to the current user.
	 * @return bool
	 */
	public static function check_user_role( $role, $user_id = null ) {
	 
		if ( is_numeric( $user_id ) )
		$user = get_userdata( $user_id );
		else
			$user = wp_get_current_user();
	 
		if ( empty( $user ) )
		return false;
	 
		return in_array( $role, (array) $user->roles );
	}
	
	public static function render_field($container_css_class, $label_css_class, $label_text, $field_value, $header_text = '', $is_paragraph = false, $hide_empty = false, $container_is_tr = false) {

		$render = !empty($field_value) || (!empty($label_text) && !$hide_empty);
		
		if ($render) {

			$ret_val = '';
		
			if (!empty($container_css_class)) {
				if ($container_is_tr)
					$ret_val .= sprintf("<tr class='%s'>", $container_css_class);
				else
					$ret_val .= sprintf("<div class='%s'>", $container_css_class);
			}

			if (!empty($header_text) && !$container_is_tr)
				$ret_val .= sprintf("<h2>%s</h2>", $header_text);
			
			if ($is_paragraph && !$container_is_tr)
				$ret_val .= '<p>';

			if (!empty($label_text) || !empty($label_css_class)) {
				if ($container_is_tr)
					$ret_val .= sprintf("<th class='%s'>%s</th>", $label_css_class, $label_text);
				else 
					$ret_val .= sprintf("<span class='%s'>%s</span>", $label_css_class, $label_text);
			}

			if (!empty($field_value)) {
				if ($container_is_tr)
					$ret_val .= sprintf('<td>%s</td>', $field_value);
				else
					$ret_val .= $field_value;
			} else {
				if ($container_is_tr)
					$ret_val .= '<td></td>';
			}
			
			if ($is_paragraph && !$container_is_tr)
				$ret_val .= '</p>';
				
			if (!empty($container_css_class)) {
				if ($container_is_tr)
					$ret_val .= '</tr>';
				else
					$ret_val .= '</div>';
			}

			$ret_val = apply_filters('bookyourtravel_render_field', $ret_val, $container_css_class, $label_css_class, $label_text, $field_value, $header_text, $is_paragraph);

			echo $ret_val;
		}
	}
	
	public static function find_extra_field($extra_fields, $field_id) {
	
		$found_field = null;
	
		if (isset($extra_fields) && isset($field_id)) {
			foreach ($extra_fields as $extra_field) {
				
				if (isset($extra_field['id'])) {
					
					if ($extra_field['id'] == $field_id) {
						$found_field = $extra_field;
						break;
					}
					
				}
				
			}
		}
	
		return $found_field;
	}
	
	public static function are_tab_fields_empty($option_id, $extra_fields, $tab_id, $entity_obj) {
	
		global $bookyourtravel_theme_of_custom;
		$count = 0;
		
		$extra_fields = BookYourTravel_Theme_Utils::custom_array_search($extra_fields, 'tab_id', $tab_id); 
		
		if (is_array($extra_fields)) {
		
			foreach ($extra_fields as $extra_field) {
		
				$field_is_hidden = isset($extra_field['hide']) ? intval($extra_field['hide']) : 0;
		
				if (!$field_is_hidden) {
					
					$field_id = isset($extra_field['id']) ? $extra_field['id'] : '';
					$value = $entity_obj->get_custom_field($field_id);
					
					if (!empty($value)) {
						$count++;
					}
				}
			}
		}
		
		return $count == 0;
	
	}
	
	/**
	 * 	Function that renders all extra fields tied to an entity tab, as labeled field in the form of
	 * 	<div class="container_css_class">
	 *		<span class="label_css_class">$label_text</span> $field_value
	 *	</div>
	 */
	public static function render_tab_extra_fields($option_id, $extra_fields, $tab_id, $entity_obj, $container_class = "text-wrap", $label_is_header = true, $container_is_tr = false) {
		
		global $bookyourtravel_theme_of_custom;
		
		$extra_fields = BookYourTravel_Theme_Utils::custom_array_search($extra_fields, 'tab_id', $tab_id); 
		
		if (is_array($extra_fields)) {
		
			foreach ($extra_fields as $extra_field) {
		
				$field_is_hidden = isset($extra_field['hide']) ? intval($extra_field['hide']) : 0;
				
				if (!$field_is_hidden) {
				
					$field_id = isset($extra_field['id']) ? $extra_field['id'] : '';
					$field_label = isset($extra_field['label']) ? $extra_field['label'] : '';
					$field_label = $bookyourtravel_theme_of_custom->get_translated_dynamic_string($bookyourtravel_theme_of_custom->get_option_id_context($option_id) . ' ' . $field_label, $field_label);
					$field_type = isset($extra_field['type']) ? $extra_field['type'] : ''; 
					
					if ($field_type == 'text' || $field_type == 'textarea') {
						if (!empty($field_id) && !empty($field_label)) {
						
							$field_value = $entity_obj->get_custom_field($field_id);
							if ($field_type == 'textarea') {
								$field_value = wpautop($field_value);
							}
							
							$shortcode_html = do_shortcode($field_value);
							
							if (!empty($shortcode_html) && $shortcode_html != $field_value) {
								echo $shortcode_html;
							} else {					
								if ($label_is_header)
									BookYourTravel_Theme_Utils::render_field($container_class . ' ' . $field_id, 	"", "", $field_value, $field_label, false, true, $container_is_tr);
								else
									BookYourTravel_Theme_Utils::render_field($container_class . ' ' . $field_id, 	"", $field_label, $field_value, "", false, true, $container_is_tr);
							}
						}
					} elseif ($field_type == 'image') {
						$field_image_uri = $entity_obj->get_custom_field_image_uri($field_id, 'medium');
						if (!empty($field_image_uri)) 
							echo '<img src="' . $field_image_uri . '" alt="' . $field_label . '" />';
					}
				}
			}
		}
	}

	/**
	 * Function that either renders or echos image tag in the form of
	 * <img class="image_css_class" id="$image_id" src="$image_src" title="$image_title" alt="$image_alt" />
	 */
	public static function render_image($image_css_class, $image_id, $image_src, $image_title, $image_alt, $echo = true) {
		if ( !empty( $image_src) ) {
			$ret_val = sprintf("<img class='%s'", $image_css_class);
			if (!empty($image_id)) {
				$ret_val .= sprintf(" id='%s'", $image_id);
			}
			$ret_val .= sprintf(" src='%s' title='%s' alt='%s' />", $image_src, $image_title, $image_alt);

			$ret_val = apply_filters('bookyourtravel_render_image', $ret_val, $image_css_class, $image_id, $image_src, $image_title, $image_alt);
			if ($echo)
				echo $ret_val;
			else
				return $ret_val;
		}
		return "";
	}

	/**
	 * Function that renders tab in the form of
	 * <li class="tab_css_class" id="$tab_id">$tab_content</li>
	 */
	public static function render_tab($page_post_type, $tab_css_class, $tab_id, $tab_content, $echo = true) {
		$ret_val = sprintf("<li class='%s'", $tab_css_class);
		if (!empty($tab_id)) {
			$ret_val .= sprintf(" id='%s'", $tab_id);
		}
		$ret_val .= sprintf(">%s</li>", $tab_content);
		$ret_val = apply_filters('bookyourtravel_render_tab', $ret_val, $page_post_type, $tab_css_class, $tab_id, $tab_content);
		if ($echo)
			echo $ret_val;
		else
			return $ret_val;
	}

	/**
	 * Function that renders link button in the form of
	 * <a href="$href" class="$link_css_class" id="$link_id" title="$text">$text</a>
	 */
	public static function render_link_button($href, $link_css_class, $link_id, $text, $echo = true)  {
		$ret_val = sprintf("<a href='%s' class='%s' ", $href, $link_css_class);
		if (!empty($link_id))
			$ret_val .= sprintf(" id='%s' ", $link_id);
		$ret_val .= sprintf(" title='%s'>%s</a>", $text, $text);
		
		$ret_val = apply_filters('bookyourtravel_render_link_button', $ret_val, $href, $link_css_class, $link_id, $text);
		if ($echo)
			echo $ret_val;
		else
			return $ret_val;
	}

	/**
	 * Function that renders submit button in the form of
	 * <input type="submit" value="$text" id="$submit_id" name="$submit_id" class="$submit_css_class" />
	 */
	public static function render_submit_button($submit_css_class, $submit_id, $text, $echo = true)  {
		$ret_val = sprintf("<input type='submit' class='%s' id='%s' name='%s' value='%s' />", $submit_css_class, $submit_id, $submit_id, $text);
		$ret_val = apply_filters('bookyourtravel_render_link_button', $ret_val, $submit_css_class, $submit_id, $text);
		if ($echo)
			echo $ret_val;
		else
			return $ret_val;
	}

	public static function custom_array_search($array, $key, $value)
	{
		$results = array();

		if (is_array($array)) {
			if (isset($array[$key]) && $array[$key] == $value) {
				$results[] = $array;
			}

			foreach ($array as $subarray) {
				$results = array_merge($results, BookYourTravel_Theme_Utils::custom_array_search($subarray, $key, $value));
			}
		}

		return $results;
	}

	public static function encrypt($string, $key) {
		if ( function_exists ('base64_encode') && function_exists ('mcrypt_encrypt') ) {
			return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
		} else {
			return $string;
		}		
	}
	
	public static function decrypt($encrypted, $key) {
		if ( function_exists ('base64_decode') && function_exists ('mcrypt_encrypt') ) {
			return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($encrypted), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
		} else {
			return $encrypted;
		}
	}

	public static function get_posts_children($parent_id, $args) {

		$args['post_parent'] = $parent_id;
		$children = array();
		// grab the posts children
		$posts = get_posts( $args );
		// now grab the grand children
		foreach( $posts as $child ) {
			// recursion!! hurrah
			$gchildren = get_posts_children($child->ID, $args);
			// merge the grand children into the children array
			if( !empty($gchildren) ) {
				$children = array_merge($children, $gchildren);
			}
		}
		// merge in the direct descendants we found earlier
		$children = array_merge($children,$posts);
		return $children;
	}

	public static function strip_tags_and_shorten($content, $character_count) {
		$content = wp_strip_all_tags($content);
		return (mb_strlen($content) > $character_count) ? mb_substr($content, 0, $character_count).' ' : $content;
		// return implode(' ', array_slice(explode(' ', $content), 0, $words));
	}

	public static function get_current_language_page_id($id) {
		if(function_exists('icl_object_id')) {
			return icl_object_id($id,'page',true);
		} else {
			return $id;
		}
	}
	
	public static function get_current_language_post_id($id, $post_type = 'post', $return_original_if_missing = true) {
		if(function_exists('icl_object_id')) {
			return icl_object_id($id,$post_type, $return_original_if_missing);
		} else {
			return $id;
		}
	}

	public static function get_language_post_id($id, $post_type, $language, $return_original_if_missing = true) {
		global $sitepress;
		if ($sitepress) {
			if(function_exists('icl_object_id')) {
				return icl_object_id($id, $post_type, $return_original_if_missing, $language);
			} else {
				return $id;
			}
		}
		return $id;	
	}
	
	public static function get_active_languages() {
	
		$language_array = array();
		$language_array[] = BookYourTravel_Theme_Utils::get_default_language();
		
		global $sitepress;
		if ($sitepress) {
			if(function_exists('icl_get_languages')) {
				$languages = icl_get_languages('skip_missing=0&orderby=code');
				if(!empty($languages)) {
					foreach($languages as $l) {
						if($l['active'] && !in_array($l['language_code'], $language_array))
							$language_array[] = $l['language_code'];
					}
				}
			}
		}
		
		return $language_array;
	}

	public static function get_default_language_post_id($id, $post_type) {
		global $sitepress;
		if ($sitepress) {
			$default_language = $sitepress->get_default_language();
			if(function_exists('icl_object_id')) {
				return icl_object_id($id, $post_type, true, $default_language);
			} else {
				return $id;
			}
		}
		return $id;	
	}

	public static function get_default_language() {
		global $sitepress;
		if ($sitepress) {
			return $sitepress->get_default_language();
		} else if (defined('WPLANG')) {
			return WPLANG;
		} else
			return "en";	
	}

	public static function table_exists($table_name) {
		global $wpdb;
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			return false;
		}
		return true;
	}

	public static function contact_form_js() {

		global $business_address_longitude, $business_address_latitude;

		global $bookyourtravel_theme_globals;
		$google_maps_key = $bookyourtravel_theme_globals->get_google_maps_key();	
		if (!empty($google_maps_key)) {
			wp_enqueue_script( 'bookyourtravel-google-maps', '//maps.google.com/maps/api/js?key=' . $google_maps_key, 'jquery', BOOKYOURTRAVEL_VERSION, true	);
			wp_enqueue_script( 'bookyourtravel-google-maps' );	
		}
		wp_register_script('infobox', BookYourTravel_Theme_Utils::get_file_uri('/js/infobox.js'),'jquery','1.0',true);
		wp_enqueue_script( 'infobox' );
		wp_register_script(	'contact', BookYourTravel_Theme_Utils::get_file_uri('/js/contact.js'), 'jquery', '1.0',true);
		wp_enqueue_script( 'contact' );

		/* Contact form related stuff */
		$business_address_latitude =  of_get_option('business_address_latitude', '');
		$business_address_longitude =  of_get_option('business_address_longitude', '');
		$contact_company_name = trim(of_get_option('contact_company_name', ''));
		$contact_phone_number = trim(of_get_option('contact_phone_number', ''));
		$contact_address_street = trim(of_get_option('contact_address_street', ''));
		$contact_address_city = trim(of_get_option('contact_address_city', ''));
		$contact_address_country = trim(of_get_option('contact_address_country', ''));	 
		$company_address = '<strong>' . $contact_company_name . '</strong>';
		$company_address .= (!empty($contact_address_street) ? $contact_address_street : '') . ', ';
		$company_address .= (!empty($contact_address_city) ? $contact_address_city : '') . ', ';
		$company_address .= (!empty($contact_address_country) ? $contact_address_country : '');
		$company_address = rtrim(trim($company_address), ',');

		if (!empty($business_address_longitude) && !empty($business_address_latitude)) {
		?>	 
		<script>
			window.business_address_latitude = <?php echo json_encode ($business_address_latitude); ?>;
			window.business_address_longitude = <?php echo json_encode ($business_address_longitude); ?>;
			window.company_address = <?php echo json_encode ($company_address); ?>;
		</script>
		<?php
		}
	}

	public static function breadcrumbs() {
	
		ob_start();
		
		if (is_home()) {}
		else {
			echo '<!--breadcrumbs--><nav role="navigation" class="breadcrumbs">';
			echo '<ul>';
			echo '<li><a href="' . home_url('/') . '" title="' . esc_html__('Home', 'bookyourtravel') . '">' . esc_html__('Home', 'bookyourtravel') . '</a></li>';
			if (is_category()) {
				echo "<li>";
				the_category('</li><li>');
				echo "</li>";
			} elseif (is_page() || is_single()) {
				echo "<li>";
				echo the_title();
				echo "</li>";
			} elseif (is_404()) {
				echo "<li>" . esc_html__('Error 404 - Page not found', 'bookyourtravel') . "</li>";
			} elseif (is_search()) {
				echo "<li>";
				echo esc_html__('Search results for: ', 'bookyourtravel');
				echo '"<em>';
				echo get_search_query();
				echo '</em>"';
				echo "</li>";
			} else if (is_post_type_archive('accommodation')) {
				echo "<li>";
				echo esc_html__('Accommodations', 'bookyourtravel');
				echo "</li>";
			} else if (is_post_type_archive('location')) {
				echo "<li>";
				echo esc_html__('Locations', 'bookyourtravel');
				echo "</li>";
			}
			
			echo '</ul>';
			echo '</nav><!--//breadcrumbs-->';
		}
		
		$breadcrumbs = ob_get_contents();
		ob_end_clean();
		echo apply_filters( 'bookyourtravel_breadcrumbs', $breadcrumbs );
	}

	public static function string_contains($haystack, $needle) {
		if (strpos($haystack, $needle) !== FALSE)
			return true;
		else
			return false;
	}
	
	public static function get_current_page_url_no_query() {
	
		global $wp;	
		$current_url = add_query_arg( '', '', home_url( $wp->request ) );
		return $current_url;
	}	

	public static function get_current_page_url() {
	
		global $wp;	
		$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
		return $current_url;
	}

	public static function display_pager($max_num_pages, $custom_bookyourtravel_paged = false) {

		$pattern = '#(www\.|https?:\/\/) {1}[a-zA-Z0-9\-]{2,254}\.[a-zA-Z0-9]{2,20}[a-zA-Z0-9.?&=_/]*#i';

		$big = 999999999; // need an unlikely integer
		
		$pager_settings = array(
			'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'total' => $max_num_pages,
			'prev_text'    => esc_html__('&lt;', 'bookyourtravel'),
			'next_text'    => esc_html__('&gt;', 'bookyourtravel'),
			'type'		   => 'array'
		);
		
		if ($custom_bookyourtravel_paged) {
			$pager_settings['format'] = '?paged-byt=%#%';
			$pager_settings['current'] = max( 1, get_query_var('paged-byt') );
		} else {
			$pager_settings['format'] = '?paged=%#%';
			$pager_settings['current'] = max( 1, get_query_var('paged') );
		}
		
		$pager_links = paginate_links( $pager_settings );
		
		$count_links = count($pager_links);
		if ($count_links > 0) {
		
			$first_link = $pager_links[0];
			$last_link = $first_link;
			preg_match_all($pattern, $first_link, $matches, PREG_PATTERN_ORDER);
			echo '<span><a href="' . get_pagenum_link(1) . '">' . esc_html__('First page', 'bookyourtravel') . '</a></span>';
			for ($i=0; $i<$count_links; $i++) {
				$pager_link = $pager_links[$i];
				if (!BookYourTravel_Theme_Utils::string_contains($pager_link, 'current'))
					echo '<span>' . $pager_link . '</span>';
				else
					echo $pager_link;
				$last_link = $pager_link;
			}
			preg_match_all($pattern, $last_link, $matches, PREG_PATTERN_ORDER);
			echo '<span><a href="' . get_pagenum_link($max_num_pages) . '">' . esc_html__('Last page', 'bookyourtravel') . '</a></span>';
		}
	}

	public static function comment($comment, $args, $depth) {
	   $GLOBALS['comment'] = $comment; 
	   $comment_class = comment_class('clearfix', null, null, false);
	   ?>							
		<!--single comment-->
		<article <?php echo $comment_class; ?> id="article-comment-<?php comment_ID() ?>">
			<div class="third">
				<figure><?php echo get_avatar( $comment->comment_author_email, 70 ); ?></figure>
				<address>
					<span><?php echo get_comment_author_link(); ?></span><br />
					<?php comment_time('F j, Y'); ?>
				</address>
				<div class="comment-meta commentmetadata"><?php edit_comment_link(esc_html__('(Edit)', 'bookyourtravel'),'  ','') ?></div>
			</div>
			<?php if ($comment->comment_approved == '0') : ?>
			<em><?php esc_html_e('Your comment is awaiting moderation.', 'bookyourtravel') ?></em>
			<?php endif; ?>
			<div class="comment-content"><?php echo get_comment_text(); ?></div>
	<?php 
		$reply_link = get_comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth'])));
		$reply_link = str_replace('comment-reply-link', 'comment-reply-link reply', $reply_link);
		$reply_link = str_replace('comment-reply-login', 'comment-reply-login reply', $reply_link);
	?>		
			<?php echo $reply_link; ?>
		</article>
		<!--//single comment-->
	<?php
	}

	/**
	 * Email sent to user during registration process requiring confirmation if option enabled in Theme settings
	 */
	public static function send_activation_notification( $user_id ) {

		$user = get_userdata( $user_id );
		
		if( !$user  ) return false;
		
		$user_activation_key = get_user_meta($user_id, 'user_activation_key', true);
		
		if (empty($user_activation_key))
			return false;
		
		$register_page_url_id = BookYourTravel_Theme_Utils::get_current_language_page_id(of_get_option('register_page_url', ''));
		$register_page_url = get_permalink($register_page_url_id);
		if (!$register_page_url)
			$register_page_url = home_url('/') . '/wp-login.php';
		
		$activation_url = esc_url_raw( add_query_arg( 
			array( 
				'action' => 'activate',
				'user_id' => $user->ID,
				'activation_key' => $user_activation_key
			), 
			$register_page_url
		) );
		
		$subject = get_bloginfo( 'name' ) . esc_html__( ' - User Activation ', 'bookyourtravel' );
		$body = esc_html__( 'To activate your user account, please click the activation link below: ', 'bookyourtravel' );
		$body .= "\r\n";
		$body .= "<a href='$activation_url'>$activation_url</a>";

		$admin_email = get_option( 'admin_email' );
		
		// $headers_array   = array();
		// $headers_array[] = "Content-Type: text/html\r\n";
		// $headers_array[] = "MIME-Version: 1.0";
		// $headers_array[] = "Content-type: text/plain; charset=utf-8";
		// $headers_array[] = "From: " . $admin_name . " <" . $admin_email . ">";
		// $headers_array[] = "Reply-To: " . $admin_name . " <" . $admin_email . ">";
		// $headers_array[] = "X-Mailer: PHP/".phpversion();
		
		// $headers = implode( "\r\n", $headers_array );		
		
		$headers = "Content-Type: text/html; charset=utf-8\r\n";	
		$headers .= "From: " . $admin_email . " <" . $admin_email . ">\r\n";
		$headers .= "Reply-To: " . $admin_email . " <" . $admin_email . ">\r\n";
		
		$ret = wp_mail( $user->user_email, $subject, $body, $headers, "");
		if ( $ret ) {
			return true;
		} else {
			global $phpmailer;
			if (isset($phpmailer) && WP_DEBUG) {
				var_dump($phpmailer->ErrorInfo);
			}
			return false;
		}
	}
	
	public static function activation_success_notification( $user_id ) {

		global $bookyourtravel_theme_globals;
		
		$user = get_userdata( $user_id );
		
		if( !$user  ) return false;
		
		$redirect_to_after_login_url = $bookyourtravel_theme_globals->get_redirect_to_after_login_page_url();
		if (!$redirect_to_after_login_url)
			$redirect_to_after_login_url = home_url('/');

		$login_url = $bookyourtravel_theme_globals->get_login_page_url();
		if (!$login_url)
			$login_url = home_url('/') . '/wp-login.php';
			
		$let_users_set_pass = $bookyourtravel_theme_globals->let_users_set_pass();
		
		$subject = get_bloginfo( 'name' ) . esc_html__( ' - User Activation Success ', 'bookyourtravel' );
		if ($let_users_set_pass)
			$body = esc_html__( 'Thank you for activating your account. You may now log in using the credentials you supplied when you created your account.', 'bookyourtravel' );
		else {
			$new_password = get_user_meta($user_id, 'user_pass', true);
			$body = esc_html__( 'Thank you for activating your account. You may now log in using the following credentials:', 'bookyourtravel' ) . "\r\n";
			$body .= sprintf(esc_html__('Username: %s', 'bookyourtravel'), $user->user_login) . "\r\n";
			$body .= sprintf(esc_html__('Password: %s', 'bookyourtravel'), $new_password) . "\r\n";
			$body .= sprintf(esc_html__('Login url: %s', 'bookyourtravel'), $login_url) . "\r\n";
		}
		
		// Delete plaintext pass
		delete_user_meta( $user_id, 'user_pass' );
		
		$body .= "\r\n";

		$admin_email = get_option( 'admin_email' );
		
		// $headers   = array();
		// $headers[] = "MIME-Version: 1.0";
		// $headers[] = "Content-type: text/plain; charset=utf-8";
		// $headers[] = "From: " . get_bloginfo( 'name' ) . " <" . $admin_email . ">";
		// $headers[] = "Reply-To: " . get_bloginfo( 'name' ) . " <" . $admin_email . ">";
		// $headers[] = "X-Mailer: PHP/".phpversion();
		
		$headers = "Content-Type: text/plain; charset=utf-8\r\n";	
		$headers .= "From: " . $admin_email . " <" . $admin_email . ">\r\n";
		$headers .= "Reply-To: " . $admin_email . " <" . $admin_email . ">\r\n";
		
		$ret = wp_mail( $user->user_email, $subject, $body, $headers, "" );
		if( $ret ) {
			return true;
		} else {
			global $phpmailer;
			if (isset($phpmailer) && WP_DEBUG) {
				var_dump($phpmailer->ErrorInfo);
			}
			return false;
		}

	}

	public static function activate_user( $user_id, $activation_key ) {
	
		$user = get_userdata( $user_id );
		$user_activation_key = get_user_meta($user_id, 'user_activation_key', true);

		if ( $user && !empty($user_activation_key) && $user_activation_key === $activation_key ) {
		
			// change user role from pending
			$user_can_frontend_submit = get_user_meta($user->ID, 'user_can_frontend_submit', true);

			$userdata = array('ID' => $user->ID);		
			if ($user_can_frontend_submit) {
				$userdata['role'] = BOOKYOURTRAVEL_FRONTEND_SUBMIT_ROLE;
			} else {
				$userdata['role'] = get_option('default_role');
			};
			
			wp_update_user( $userdata );
			delete_user_meta( $user_id, 'user_activation_key' );
			delete_user_meta( $user_id, 'user_can_frontend_submit' );
			
			BookYourTravel_Theme_Utils::activation_success_notification($user_id);
			
			return true;
		} else{
			return false;
		}
	}

	public static function newpassword_notification( $user_id, $new_password ) {

		$user = get_userdata( $user_id );
		if( !$user || !$new_password ) return false;

		$subject = get_bloginfo( 'name' ) . esc_html__( ' - New Password ', 'bookyourtravel' );
		$body = esc_html__( 'Your password was successfully reset. ', 'bookyourtravel' );
		$body .= "\r\n";
		$body .= "\r\n";
		$body .= esc_html__( 'Your new password is:', 'bookyourtravel' );
		$body .= ' ' . $new_password;

		$admin_email = get_option( 'admin_email' );
		
		$headers   = array();
		$headers[] = "MIME-Version: 1.0";
		$headers[] = "Content-type: text/plain; charset=utf-8";
		$headers[] = "From: " . get_bloginfo( 'name' ) . " <" . $admin_email . ">";
		$headers[] = "Reply-To: " . get_bloginfo( 'name' ) . " <" . $admin_email . ">";
		$headers[] = "X-Mailer: PHP/".phpversion();
		
		if( mail( $user->user_email, $subject, $body, implode( "\r\n", $headers ), '-f ' . $admin_email ) ) {
			return true;
		} else {
			return false;
		}
	}

	public static function resetpassword_notification( $user_id ) {

		$user = get_userdata( $user_id );
		if( !$user || !$user->user_resetpassword_key ) return false;

		$override_wp_login = of_get_option('override_wp_login', 0);
		$reset_password_page_url_id = BookYourTravel_Theme_Utils::get_current_language_page_id(of_get_option('reset_password_page_url', ''));
		$reset_password_page_url = get_permalink($reset_password_page_url_id);
		if (!$reset_password_page_url || !$override_wp_login)
			$reset_password_page_url = home_url('/') . '/wp-login.php';
		
		$admin_email = get_option( 'admin_email' );
		
		$resetpassword_url = esc_url_raw ( add_query_arg( 
			array( 
				'action' => 'resetpassword',
				'user_id' => $user->ID,
				'resetpassword_key' => $user->user_resetpassword_key
			), 
			$reset_password_page_url
		) );

		$subject = get_bloginfo( 'name' ) . esc_html__( ' - Reset Password ', 'bookyourtravel' );
		$body = esc_html__( 'To reset your password please go to the following url: ', 'bookyourtravel' );
		$body .= "\r\n";
		$body .= $resetpassword_url;
		$body .= "\r\n";
		$body .= "\r\n";
		$body .= esc_html__( 'This link will remain valid for the next 24 hours.', 'bookyourtravel' );
		$body .= esc_html__( 'In case you did not request a password reset, please ignore this email.', 'bookyourtravel' );

		$headers   = array();
		$headers[] = "MIME-Version: 1.0";
		$headers[] = "Content-type: text/plain; charset=utf-8";
		$headers[] = "From: " . get_bloginfo( 'name' ) . " <" . $admin_email . ">";
		$headers[] = "Reply-To: " . get_bloginfo( 'name' ) . " <" . $admin_email . ">";
		$headers[] = "X-Mailer: PHP/".phpversion();
		
		if( mail( $user->user_email, $subject, $body, implode( "\r\n", $headers ), '-f ' . $admin_email ) ) {
			return true;
		} else {
			return false;
		}
	}

	public static function resetpassword( $user_id, $resetpassword_key ) {
		$user = get_userdata( $user_id );

		if( 
			$user && 
			$user->user_resetpassword_key && 
			$user->user_resetpassword_key === $resetpassword_key 
		) {
			// check reset password time
			if(
				!$user->user_resetpassword_datetime ||
				strtotime( $user->user_resetpassword_datetime ) < time() - ( 24 * 60 * 60 )
			) return false;

			// reset password
			$userdata = array(
				'ID' => $user->ID,
				'user_pass' => wp_generate_password( 8, false )
			);

			wp_update_user( $userdata );
			delete_user_meta( $user->ID, 'user_resetpassword_key' );
			
			return $userdata['user_pass'];
		} else{
			return false;
		}
	}

	public static function get_file_path($relative_path_to_file) {
		if (is_child_theme()) {
			if (file_exists( get_stylesheet_directory() . $relative_path_to_file ) )
				return get_stylesheet_directory() . $relative_path_to_file;
			else
				return get_template_directory() . $relative_path_to_file;
		}
		return get_template_directory() . $relative_path_to_file;
	}


	public static function get_file_uri($relative_path_to_file) {
		if (is_child_theme()) {
			if (file_exists( get_stylesheet_directory() . $relative_path_to_file ) )
				return get_stylesheet_directory_uri() . $relative_path_to_file;
			else
				return get_template_directory_uri() . $relative_path_to_file;
		}
		return get_template_directory_uri() . $relative_path_to_file;
	}

	public static function retrieve_array_of_values_from_query_string($key, $are_numbers = false) {
		$values_array = array();
		$query_string = explode("&",$_SERVER['QUERY_STRING']);
		foreach ($query_string as $part) {
			if (strpos($part, $key) !== false) {
				$split = strpos($part,"=");
				$value = trim(substr($part, $split + 1));
				if (!empty($value))
					$values_array[] = $are_numbers ? intval($value) : $value;
			}
		}
		return $values_array;
	}

	public static function get_post_descendants($parent_id, $post_type) {
		$children = array();
		$posts = get_posts( array( 'numberposts' => -1, 'post_status' => 'publish', 'post_type' => $post_type, 'post_parent' => $parent_id, 'suppress_filters' => false ));
		foreach( $posts as $child ) {
			$gchildren = BookYourTravel_Theme_Utils::get_post_descendants($child->ID, $post_type);
			if( !empty($gchildren) ) {
				$children = array_merge($children, $gchildren);
			}
		}
		$children = array_merge($children,$posts);
		return $children;
	}
	
	public static function is_woocommerce_active() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		return is_plugin_active( 'woocommerce/woocommerce.php');
	}
	
	public static function is_wpml_active() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		return is_plugin_active( 'sitepress-multilingual-cms/sitepress.php');
	}

	// period_type = 0, increase by day
	// period_type = 1, increase by 7 days (week)
	// period_type = 2, incraese by 1 month
	public static function get_dates_from_range($start, $end, $period_type = 0) {
	
		$dates = array($start);
		
		while(end($dates) < $end) {
			if ($period_type == 1) {
				$new_date = date('Y-m-d', strtotime(end($dates).' +7 day'));
				if ($new_date < $end) {
					$dates[] = $new_date;
				} else {
					break;
				}
			} else if ($period_type == 2) {
				$new_date = date('Y-m-d', strtotime(end($dates).' +1 month'));
				if ($new_date < $end) {
					$dates[] = $new_date;
				} else {
					break;
				}
			} else {
				$dates[] = date('Y-m-d', strtotime(end($dates).' +1 day'));
			}
		}
		return $dates;
	}
		
	/*
	 * from: http://stackoverflow.com/questions/16702398/convert-a-php-date-format-to-a-jqueryui-datepicker-date-format
	 * Matches each symbol of PHP date format standard
	 * with jQuery equivalent codeword
	 * @author Tristan Jahier
	 */
	public static function dateformat_PHP_to_jQueryUI($php_format)
	{
		$SYMBOLS_MATCHING = array(
			// Day
			'd' => 'dd',
			'D' => 'D',
			'j' => 'd',
			'l' => 'DD',
			'N' => '',
			'S' => '',
			'w' => '',
			'z' => 'o',
			// Week
			'W' => '',
			// Month
			'F' => 'MM',
			'm' => 'mm',
			'M' => 'M',
			'n' => 'm',
			't' => '',
			// Year
			'L' => '',
			'o' => '',
			'Y' => 'yy',
			'y' => 'y',
			// Time
			'a' => '',
			'A' => '',
			'B' => '',
			'g' => '',
			'G' => '',
			'h' => '',
			'H' => '',
			'i' => '',
			's' => '',
			'u' => ''
		);
		$jqueryui_format = "";
		$escaping = false;
		for($i = 0; $i < strlen($php_format); $i++)
		{
			$char = $php_format[$i];
			if($char === '\\') // PHP date format escaping character
			{
				$i++;
				if($escaping) $jqueryui_format .= $php_format[$i];
				else $jqueryui_format .= '\'' . $php_format[$i];
				$escaping = true;
			}
			else
			{
				if($escaping) { $jqueryui_format .= "'"; $escaping = false; }
				if(isset($SYMBOLS_MATCHING[$char]))
					$jqueryui_format .= $SYMBOLS_MATCHING[$char];
				else
					$jqueryui_format .= $char;
			}
		}
		return $jqueryui_format;
	}
	
	public static function is_weekend($date) {
		return (date('N', strtotime($date)) >= 6);
	}
}

//
// http://scotty-t.com/2012/07/09/wp-you-oop/
//
abstract class BookYourTravel_BaseSingleton {
    private static $instance = array();
    protected function __construct() {}
    
	public static function get_instance() {
        $c = get_called_class();
        if ( !isset( self::$instance[$c] ) ) {
            self::$instance[$c] = new $c();
            self::$instance[$c]->init();
        }

        return self::$instance[$c];
    }

    abstract public function init();
}

function bookyourtravel_comment($comment, $args, $depth) {
	BookYourTravel_Theme_Utils::comment($comment, $args, $depth);
}

function bookyourtravel_comment_end($comment, $args, $depth) {
	BookYourTravel_Theme_Utils::comment_end($comment, $args, $depth);
}