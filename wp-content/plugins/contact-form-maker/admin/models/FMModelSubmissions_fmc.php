<?php 
class FMModelSubmissions_fmc {
	////////////////////////////////////////////////////////////////////////////////////////
	// Events                                                                             //
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	// Constants                                                                          //
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	// Variables                                                                          //
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	// Constructor & Destructor                                                           //
	////////////////////////////////////////////////////////////////////////////////////////
	public function __construct() {
	}
	////////////////////////////////////////////////////////////////////////////////////////
	// Public Methods                                                                     //
	////////////////////////////////////////////////////////////////////////////////////////
	public function blocked_ips() {
		global $wpdb;
		$ips = $wpdb->get_col('SELECT ip FROM ' . $wpdb->prefix . 'formmaker_blocked');
		return $ips;
	}

	public function get_form_titles() {
		global $wpdb;
		$query = "SELECT id, title FROM " . $wpdb->prefix . "formmaker WHERE `id` IN(" . (get_option('contact_form_forms', '') != '' ? get_option('contact_form_forms') : 0) . ") order by title";
		$forms = $wpdb->get_results($query);
		return $forms;
	}
  
	public function get_statistics($form_id) { 
		global $wpdb;
		$statistics = array();
		$query = $wpdb->prepare('SELECT count(distinct group_id) FROM ' . $wpdb->prefix . 'formmaker_submits WHERE form_id ="%d"', $form_id);
		$statistics["total_entries"] = $wpdb->get_var($query);
		$query = $wpdb->prepare('SELECT `views` FROM ' . $wpdb->prefix . 'formmaker_views WHERE form_id="%d"', $form_id);
		$statistics["total_views"] = $wpdb->get_var($query);
		if ($statistics["total_views"]) {
			$statistics["conversion_rate"] = round((($statistics["total_entries"] / $statistics["total_views"]) * 100), 2) . '%';
		}
		else {
			$statistics["conversion_rate"] = '0%';
		}
		return $statistics;
	}
  
  public function get_labels_parameters($form_id) {
    global $wpdb;
    $labels = array();
    $labels_id = array();
    $sorted_labels_id = array();
    $label_names = array();
    $label_types = array();
    $sorted_label_types = array();
    $label_names_original = array();
    $labels_parameters = array();
    $join_query = array();
    $join_where = array();
    $rows_ord = array();
    $join = '';
    for ($i = 0; $i < 8; $i++) {
      array_push($labels_parameters, NULL);
    }
    $sorted_label_names = array();
    $sorted_label_names_original = array();
    $where_labels = array();
    $where2 = array();
	
	$pagination_clicked = (isset($_POST['pagination_clicked']) && $_POST['pagination_clicked'] == '1' ? '1' : '0');
	
	$order_by = ((isset($_POST['order_by']) && esc_html(stripslashes($_POST['order_by'])) != '') ? esc_html(stripslashes($_POST['order_by'])) : 'group_id');
    $asc_or_desc = ((isset($_POST['asc_or_desc']) && $_POST['asc_or_desc'] == 'desc') ? 'desc' : 'asc');
    $limit = ((isset($_POST['page_number'])) ? ((int) $_POST['page_number'] - 1) * 20 : 0);
    $lists['hide_label_list'] = ((isset($_POST['hide_label_list'])) ? esc_html(stripslashes($_POST['hide_label_list'])) : '');
    $lists['startdate'] = ((isset($_POST['startdate'])) ? esc_html(stripslashes($_POST['startdate'])) : '');
    $lists['enddate'] = ((isset($_POST['enddate'])) ? esc_html(stripslashes($_POST['enddate'])) : '');
    $lists['ip_search'] = ((isset($_POST['ip_search'])) ? esc_html(stripslashes($_POST['ip_search'])) : '');
	
	$lists['username_search'] = ((isset($_POST['username_search'])) ? esc_html(stripslashes($_POST['username_search'])) : '');
	$lists['useremail_search'] = ((isset($_POST['useremail_search'])) ? esc_html(stripslashes($_POST['useremail_search'])) : '');
	$lists['id_search'] = ((isset($_POST['id_search'])) ? esc_html(stripslashes($_POST['id_search'])) : '');
	
    if ($lists['ip_search']) {
      $where[] = 'ip LIKE "%' . $lists['ip_search'] . '%"';
    }
    if ($lists['startdate'] != '') {
      $where[] = " `date`>='" . $lists['startdate'] . " 00:00:00' ";
    }
    if ($lists['enddate'] != '') {
      $where[] = " `date`<='" . $lists['enddate'] . " 23:59:59' ";
    }
	
	
	if ($lists['username_search']) {
      	$where[] = 'user_id_wd IN (SELECT ID FROM ' . $wpdb->prefix . 'users WHERE display_name LIKE "%'.$lists['username_search'].'%")';
    }
	if ($lists['useremail_search']) {
      $where[] = 'user_id_wd IN (SELECT ID FROM ' . $wpdb->prefix . 'users WHERE user_email LIKE "%'.$lists['useremail_search'].'%")';
    }
	
	if ($lists['id_search']) {
      $where[] = 'group_id ='.$lists['id_search'];
    }
	
    $where[] = 'form_id=' . $form_id . '';
    $where = (count($where) ? ' ' . implode(' AND ', $where) : ''); 
    if ($order_by == 'group_id' or $order_by == 'date' or $order_by == 'ip') { 
      $orderby = ' ORDER BY ' . $order_by . ' ' . $asc_or_desc . '';
    }elseif($order_by == 'display_name' or $order_by == 'user_email'){
	  $orderby 	= ' ORDER BY (SELECT '.$order_by.' FROM ' . $wpdb->prefix . 'users WHERE ID=user_id_wd) '. $asc_or_desc .'';
	}
    else {
      $orderby = "";
    }
		if ($form_id) {
      for($i = 0; $i < 8; $i++) {
        array_pop($labels_parameters);
      }
      $query = "SELECT distinct element_label FROM " . $wpdb->prefix . "formmaker_submits WHERE ". $where;
      $results = $wpdb->get_results($query); 
      for ($i = 0; $i < count($results); $i++) {
        array_push($labels, $results[$i]->element_label);
		  }
      $form = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "formmaker WHERE id='%d'", $form_id));
      if (strpos($form->label_order, 'type_paypal_')) {
        $form->label_order = $form->label_order . "item_total#**id**#Item Total#**label**#type_paypal_payment_total#****#total#**id**#Total#**label**#type_paypal_payment_total#****#0#**id**#Payment Status#**label**#type_paypal_payment_status#****#";
      }

      $form_labels = explode('#****#', $form->label_order);
      $form_labels = array_slice($form_labels, 0, count($form_labels) - 1);
	 
      foreach ($form_labels as $key => $form_label) {
        $label_id = explode('#**id**#', $form_label);
        array_push($labels_id, $label_id[0]);
        $label_name_type = explode('#**label**#', $label_id[1]);
        array_push($label_names_original, $label_name_type[0]);
        $ptn = "/[^a-zA-Z0-9_]/";
        $rpltxt = "";
        $label_name = preg_replace($ptn, $rpltxt, $label_name_type[0]);
        array_push($label_names, $label_name);
        array_push($label_types, $label_name_type[1]);
      }
	
      foreach ($labels_id as $key => $label_id) {
        if (in_array($label_id, $labels)) {
          if (!in_array($label_id, $sorted_labels_id)) {
            array_push($sorted_labels_id, $label_id);
          }
          array_push($sorted_label_names, $label_names[$key]);
          array_push($sorted_label_types, $label_types[$key]);
          array_push($sorted_label_names_original, $label_names_original[$key]);
          if (isset($_POST[$form_id . '_' . $label_id . '_search'])) {
            $search_temp = esc_html($_POST[$form_id . '_' . $label_id . '_search']);
          } 
          else {
            $search_temp = '';
          }
          $search_temp = strtolower($search_temp);
          $lists[$form_id . '_' . $label_id . '_search'] = $search_temp;
          if ($search_temp) {
            $join_query[] = 'search';
            $join_where[] = array('label' => $label_id, 'search' => $search_temp);
          }
        }
      }
      if (strpos($order_by, "_field")) { 	  
        if (in_array(str_replace("_field", "", $order_by), $labels)) {
          $join_query[]	= 'sort';
          $join_where[]	= array('label'=>str_replace("_field", "", $order_by));
        }
      }
      $cols = 'group_id';
      if ($order_by == 'date' or $order_by == 'ip') {
        $cols = 'group_id, date, ip';
      }
      switch (count($join_query)) {
        case 0:
          $join = 'SELECT distinct group_id FROM ' . $wpdb->prefix . 'formmaker_submits WHERE '. $where;
          break;
        case 1:
          if ($join_query[0] == 'sort') {
            $join = 'SELECT group_id FROM ' . $wpdb->prefix . 'formmaker_submits WHERE ' . $where . ' AND element_label="' . $join_where[0]['label'] . '" ';
            $join_count	= 'SELECT count(group_id) FROM ' . $wpdb->prefix . 'formmaker_submits WHERE form_id="' . $form_id . '" AND element_label="' . $join_where[0]['label'] . '" ';
            $orderby =	' ORDER BY `element_value` ' . $asc_or_desc;
          } 
          else {
            $join = 'SELECT group_id FROM ' . $wpdb->prefix . 'formmaker_submits WHERE element_label="' . $join_where[0]['label'] . '" AND  element_value LIKE "%' . $join_where[0]['search'] . '%" AND ' . $where;
          }
          break;			
        default:
          $join = 'SELECT t.group_id FROM (SELECT ' . $cols . '  FROM ' . $wpdb->prefix . 'formmaker_submits WHERE ' . $where . ' AND element_label="' . $join_where[0]['label'] . '" AND  element_value LIKE "%' . $join_where[0]['search'] . '%" ) as t ';
          for ($key = 1; $key < count($join_query); $key++) {
            if ($join_query[$key] == 'sort') {
              $join .= 'LEFT JOIN (SELECT group_id as group_id' . $key . ', element_value   FROM ' . $wpdb->prefix . 'formmaker_submits WHERE ' . $where . ' AND element_label="' . $join_where[$key]['label'] . '") as t' . $key . ' ON t' . $key . '.group_id' . $key . '=t.group_id ';
              $orderby = ' ORDER BY t' . $key . '.`element_value` ' . $asc_or_desc . '';
            }
            else {
              $join .= 'INNER JOIN (SELECT group_id as group_id' . $key . ' FROM ' . $wpdb->prefix . 'formmaker_submits WHERE '.$where.' AND element_label="' . $join_where[$key]['label'] . '" AND  element_value LIKE "%' . $join_where[$key]['search'] . '%" ) as t' . $key . ' ON t' . $key . '.group_id' . $key . '=t.group_id ';
            }
          }
          break;
      }			  	  
      $pos = strpos($join, 'SELECT t.group_id');
      if ($pos === FALSE) {
        $query = str_replace(array('SELECT group_id','SELECT distinct group_id'), array('SELECT count(distinct group_id)','SELECT count(distinct group_id)'), $join);
      }
      else {
        $query = str_replace('SELECT t.group_id', 'SELECT count(t.group_id)', $join);
      }
      $total = $wpdb->get_var($query);
	  
	  $query_sub_count = "SELECT count(distinct group_id) from ".$wpdb->prefix."formmaker_submits";
      $sub_count = (int)$wpdb->get_var($query_sub_count);

	  $limit1 = (int)$total < $sub_count && !$pagination_clicked ? 0 : $limit; 
	  
      $query = $join . ' ' . $orderby . ' limit ' . $limit1 . ', 20 ';
      $results = $wpdb->get_results($query);
      for ($i = 0; $i < count($results); $i++) {
        array_push($rows_ord, $results[$i]->group_id); 
      }
      $where2 = array();
      $where2[] = "group_id='0'";
      foreach ($rows_ord as $rows_ordd) { 
        $where2[] = "group_id='" . $rows_ordd . "'";
      }
      $where2 = (count($where2) ? ' WHERE ' . implode( ' OR ', $where2 ) . '' : '' );
      $query = 'SELECT * FROM ' . $wpdb->prefix . 'formmaker_submits ' . $where2;
      $rows = $wpdb->get_results($query);
      $group_ids = $rows_ord;
      $lists['total'] = $total;
      $lists['limit'] = (int) ($limit1 / 20 + 1);
      $where_choices = $where;
      array_push($labels_parameters, $sorted_labels_id);
      array_push($labels_parameters, $sorted_label_types);
      array_push($labels_parameters, $lists);
      array_push($labels_parameters, $sorted_label_names);
      array_push($labels_parameters, $sorted_label_names_original);
      array_push($labels_parameters, $rows);
      array_push($labels_parameters, $group_ids);
      array_push($labels_parameters, $where_choices);
    }
    return $labels_parameters;
  }

  public function get_type_address($sorted_label_type, $sorted_label_name_original) {
    if ($sorted_label_type == 'type_address') {
      switch ($sorted_label_name_original) {
        case 'Street Line':
          $field_title = __('Street Address', 'form_maker');
          break;
        case 'Street Line2':
          $field_title = __('Street Address Line 2', 'form_maker');
          break;
        case 'City':
          $field_title = __('City', 'form_maker');
          break;
          case 'State':
          $field_title = __('State / Province / Region', 'form_maker');
          break;
        case 'Postal':
          $field_title = __('Postal / Zip Code', 'form_maker');
          break;
        case 'Country':
          $field_title = __('Country', 'form_maker');
          break;
        default :
          $field_title = stripslashes($sorted_label_name_original);
          break;
      }
    }
	  else {
	    $field_title = stripslashes($sorted_label_name_original);
    }
    return $field_title;
  }

  public function hide_or_not($hide_strings,$hide_string) {
    if (strpos($hide_string,'@') === FALSE) {
      if (strpos($hide_strings, '@' . $hide_string . '@') === FALSE) {
        $style = '';
      }
      else {
        $style = 'style="display:none"';
      }
    }
    else {
      if (strpos($hide_strings, $hide_string) === FALSE) {
        $style = '';
      }
      else {
        $style = 'style="display:none"';
      }
    }
    return $style;
  }

  public function sort_group_ids($sorted_label_names_count, $group_ids) {
    $count_labe = $sorted_label_names_count;
    $group_id_s = array();
    $l = 0;
    if (count($group_ids) > 0 and $count_labe) {
      for ($i = 0; $i < count($group_ids); $i++) {
        if (!in_array($group_ids[$i], $group_id_s)) {
          array_push($group_id_s, $group_ids[$i]);
        }
      }
    }
    return $group_id_s;
  }

  public function array_for_group_id($group, $rows) {
    $i = $group;
    $count_rows = count($rows);
    $temp = array();
    for ($j = 0; $j < $count_rows; $j++) {
      $row = $rows[$j];
      if ($row->group_id == $i) {
        array_push($temp, $row);
      }
    }
    return $temp;
  }

  public function check_radio_type($sorted_label_type) {
    if ($sorted_label_type == "type_checkbox" || $sorted_label_type == "type_radio" || $sorted_label_type == "type_own_select" || $sorted_label_type == "type_country"  || $sorted_label_type == "type_paypal_select" || $sorted_label_type == "type_paypal_radio" || $sorted_label_type == "type_paypal_checkbox" || $sorted_label_type == "type_paypal_shipping") {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

	public function statistic_for_radio($where_choices, $sorted_label_id) { 
		global $wpdb;
		$choices_params = array();
		$query = "SELECT element_value FROM " . $wpdb->prefix . "formmaker_submits WHERE " . $where_choices . " AND element_label='" . $sorted_label_id . "'";
		$choices = $wpdb->get_results($query);
		$colors=array('#5FE2FF','#F9E89C');
		$choices_colors=array('#4EC0D9','#DDCC7F');
		$choices_labels = array();
		$choices_count = array();
		$all = count($choices);
		$unanswered = 0;
		foreach ($choices as $key => $choice) {
			if ($choice->element_value == '') {
				$unanswered++;
			}
			else {
				if (!in_array($choice->element_value, $choices_labels)) {
					array_push($choices_labels, $choice->element_value);
					array_push($choices_count, 0);
				}
				$choices_count[array_search($choice->element_value, $choices_labels)]++;
			}
		}
		array_multisort($choices_count, SORT_DESC, $choices_labels);
		array_push($choices_params, $choices_count);
		array_push($choices_params, $choices_labels);
		array_push($choices_params, $unanswered);
		array_push($choices_params, $all);
		array_push($choices_params, $colors);
		array_push($choices_params, $choices_colors);
		return $choices_params;
	}

  public function get_data_of_group_id($id) {
    global $wpdb;
    $query = "SELECT * FROM " . $wpdb->prefix . "formmaker_submits WHERE group_id=" . $id;
    $rows = $wpdb->get_results($query);
    $form = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "formmaker WHERE id=" . $rows[0]->form_id);
    $params = array();
    $label_id = array();
    $label_order_original = array();
    $label_type = array();
    $ispaypal = strpos($form->label_order, 'type_paypal_');
    if ($form->paypal_mode == 1) {
      if ($ispaypal) {
        $form->label_order = $form->label_order."0#**id**#Payment Status#**label**#type_paypal_payment_status#****#";
      }
    }
    $label_all = explode('#****#', $form->label_order);
    $label_all = array_slice($label_all, 0, count($label_all) - 1);
    foreach ($label_all as $key => $label_each) {
      $label_id_each = explode('#**id**#', $label_each);
      array_push($label_id, $label_id_each[0]);
      $label_oder_each = explode('#**label**#', $label_id_each[1]);
      array_push($label_order_original, $label_oder_each[0]);
      array_push($label_type, $label_oder_each[1]);
    }
    /*$theme_id = $wpdb->get_var("SELECT theme FROM " . $wpdb->prefix . "formmaker WHERE id='" . $form->id . "'");*/
    $css = $wpdb->get_var("SELECT css FROM " . $wpdb->prefix . "formmaker_themes");
    array_push($params, $rows);
    array_push($params, $label_id);
    array_push($params, $label_order_original);
    array_push($params, $label_type);
    array_push($params, $ispaypal);
    array_push($params, $form);
    array_push($params, $css);
    return $params;
  }

  public function check_type_for_edit_function($label_type) {
    if ($label_type != 'type_editor' and $label_type != 'type_submit_reset' and $label_type != 'type_map' and $label_type != 'type_mark_map' and $label_type != 'type_captcha' and $label_type != 'type_recaptcha' and $label_type != 'type_button') {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  public function check_for_submited_label($rows, $label_id) {
    foreach ($rows as $row) {
      if ($row->element_label == $label_id) {
        $element_value = $row->element_value;
        break;
      }
      else {
        $element_value = 'continue';
      }
    }
    return $element_value;
  }

  public function view_for_star_rating($element_value, $element_label) {
    $view_star_rating_array = array();
    $new_filename = str_replace("***star_rating***", '', $element_value);
    $stars = "";
    $new_filename=explode('***', $new_filename);
    for ($j = 0; $j < $new_filename[1]; $j++) {
      $stars .= '<img id="' . $element_label . '_star_' . $j . '" src="' . WD_FMC_URL . '/images/star_' . $new_filename[2] . '.png?ver='. get_option("wd_form_maker_version").'" /> ';
    }
    for ($k = $new_filename[1]; $k < $new_filename[0]; $k++) {
      $stars .= '<img id="' . $element_label . '_star_' . $k . '" src="' . WD_FMC_URL . '/images/star.png?ver='. get_option("wd_form_maker_version").'" /> ';
    }
    array_push($view_star_rating_array, $stars);
    return $view_star_rating_array;
  }

  public function view_for_grading($element_value) {
    $view_grading_array = array();
    $new_filename = str_replace("***grading***", '', $element_value);
    $grading = explode(":", $new_filename);
    $items_count = sizeof($grading) - 1;
    $items = "";
    $total = "";
    for ($k = 0; $k < $items_count / 2; $k++) {
      $items .= $grading[$items_count / 2 + $k] . ": " . $grading[$k] . "</br>";
      $total += $grading[$k];
    }
    $items .= "Total: " . $total;
    array_push($view_grading_array, $items);
    return $view_grading_array;
  }

  public function images_for_star_rating($element_value, $label_id) {
    $edit_stars = "";
    $star_rating_array = array();
    $element_value1 = str_replace("***star_rating***", '', $element_value);
    $stars_value = explode('***', $element_value1);
    for ($j = 0; $j < $stars_value[1]; $j++) {
      $edit_stars .= '<img id="'.$label_id.'_star_'.$j.'" onclick="edit_star_rating('.$j.','.$label_id.')" src="' . WD_FMC_URL . '/images/star_'.$stars_value[2].'.png?ver='. get_option("wd_form_maker_version").'" /> ';
    }
    for( $k=$stars_value[1];$k<$stars_value[0];$k++) {
      $edit_stars .= '<img id="'.$label_id.'_star_'.$k.'" onclick="edit_star_rating('.$k.','.$label_id.')" src="' . WD_FMC_URL . '/images/star.png?ver='. get_option("wd_form_maker_version").'" /> ';
    }
    array_push($star_rating_array, $edit_stars);
    array_push($star_rating_array, $stars_value);
    return $star_rating_array;
  }

  public function params_for_scale_rating($element_value, $label_id) {
    $scale_rating_array = array();
    $scale_radio = explode('/', $element_value);
    $scale_value = $scale_radio[0];
    $scale = '<table><tr>';
    for ($k = 1; $k <= $scale_radio[1]; $k++) {
      $scale .= '<td style="text-align:center"><span>' . $k . '</span></td>';
    }
    $scale .= '<tr></tr>';
    for ($l = 1; $l <= $scale_radio[1]; $l++) {
      if ($l == $scale_radio[0]) {
        $checked = "checked";
      }
      else {
        $checked = "";
      }
      $scale .= '<td><input type="radio" name = "'.$label_id.'_scale_rating_radio" id = "'.$label_id.'_scale_rating_radio_'.$l.'" value="'.$l.'" '.$checked.' onClick="edit_scale_rating(this.value,'.$label_id.')" /></td>';
    }	
    $scale .= '</tr></table>';
    array_push($scale_rating_array, $scale);
    array_push($scale_rating_array, $scale_radio);
    array_push($scale_rating_array, $checked);
    return $scale_rating_array;
  }

  public function params_for_type_range($element_value, $label_id) {
    $range_value = explode('-', $element_value);
    $range = '<input name="'.$label_id.'_element0"  id="'.$label_id.'_element0" type="text" value="'.$range_value[0].'" onChange="edit_range(this.value,'.$label_id.',0)" size="8"/> - <input name="'.$label_id.'_element1"  id="'.$label_id.'_element1" type="text" value="'.$range_value[1].'" onChange="edit_range(this.value,'.$label_id.',1)" size="8"/>';
    return $range;
  }

  public function params_for_type_grading($element_value, $label_id) {
    $type_grading_array = array();
    $element_value1 = str_replace("***grading***", '', $element_value);
    $garding_value = explode(':', $element_value1);
    $items_count = sizeof($garding_value) - 1;
    $garding = "";
    $sum = "";
    for ($k = 0; $k < $items_count/2; $k++) {
      $garding .= '<input name="'.$label_id.'_element'.$k.'"  id="'.$label_id.'_element'.$k.'" type="text" value="'.$garding_value[$k].'" onKeyUp="edit_grading('.$label_id.','.$items_count.')" size="5"/> '.$garding_value[$items_count/2+$k].'</br>';
      $sum += $garding_value[$k];
    }
    array_push($type_grading_array, $garding);
    array_push($type_grading_array, $garding_value);
    array_push($type_grading_array, $sum);
    array_push($type_grading_array, $items_count);
    array_push($type_grading_array, $element_value1);
    return $type_grading_array;
  }

  public function params_for_type_matrix($element_value, $label_id) {
    $type_matrix_array = array();	
    $new_filename = str_replace("***matrix***", '', $element_value);
    $matrix_value = explode('***', $new_filename);
    $matrix_value = array_slice($matrix_value, 0, count($matrix_value) - 1);
    $mat_rows = $matrix_value[0];
    $mat_columns = $matrix_value[$mat_rows + 1];
    $matrix = "<table>";
    $matrix .= '<tr><td></td>';
    for ($k = 1; $k <= $mat_columns; $k++) {
      $matrix .= '<td style="background-color:#BBBBBB; padding:5px; border:1px; ">'.$matrix_value[$mat_rows+1+$k].'</td>';
    }
    $matrix .= '</tr>';
    $aaa = Array();
    $var_checkbox = 1;
    $selected_value = "";
    $selected_value_yes = "";
    $selected_value_no = "";
    for ($k = 1; $k <= $mat_rows; $k++) {
      $matrix .= '<tr><td style="background-color:#BBBBBB; padding:5px; border:1px;">'.$matrix_value[$k].'</td>';
      if ($matrix_value[$mat_rows + $mat_columns + 2] == "radio") {
        if ($matrix_value[$mat_rows + $mat_columns + 2 + $k] == 0) {
          $checked = "";
          $aaa[1] = "";
        }
        else {
          $aaa = explode("_", $matrix_value[$mat_rows + $mat_columns + 2 + $k]);
        }
        for ($l = 1; $l <= $mat_columns; $l++) {
          if ($aaa[1] == $l) {
            $checked = 'checked';
          }
          else {
            $checked = "";
          }
          $index = "'" . $k . '_' . $l . "'";
          $matrix .= '<td style="text-align:center;"><input name="'.$label_id.'_input_elementform_id_temp'.$k.'"  id="'.$label_id.'_input_elementform_id_temp'.$k.'_'.$l.'" type="'.$matrix_value[$mat_rows+$mat_columns+2].'" '.$checked.' onClick="change_radio_values('.$index.','.$label_id.','.$mat_rows.','.$mat_columns.')"/></td>';
        }
      }
      else {
        if ($matrix_value[$mat_rows+$mat_columns+2] == "checkbox") {
          for ($l = 1; $l <= $mat_columns; $l++) {
            if ($matrix_value[$mat_rows + $mat_columns + 2 + $var_checkbox] == 1) {
              $checked = 'checked';
            }
            else {
              $checked = '';
            }
            $index = "'".$k.'_'.$l."'";
            $matrix .='<td style="text-align:center;"><input name="'.$label_id.'_input_elementform_id_temp'.$k.'_'.$l.'"  id="'.$label_id.'_input_elementform_id_temp'.$k.'_'.$l.'" type="'.$matrix_value[$mat_rows+$mat_columns+2].'" '.$checked.' onClick="change_checkbox_values('.$index.','.$label_id.','.$mat_rows.','.$mat_columns.')"/></td>';
            $var_checkbox++;
          }
        }
        else {
          if ($matrix_value[$mat_rows + $mat_columns + 2] == "text") {
            for ($l = 1; $l <= $mat_columns; $l++) {
              $text_value = $matrix_value[$mat_rows+$mat_columns+2+$var_checkbox];
              $index = "'".$k.'_'.$l."'";									
              $matrix .= '<td style="text-align:center;"><input name="'.$label_id.'_input_elementform_id_temp'.$k.'_'.$l.'"  id="'.$label_id.'_input_elementform_id_temp'.$k.'_'.$l.'" type="'.$matrix_value[$mat_rows+$mat_columns+2].'" value="'.$text_value.'" onKeyUp="change_text_values('.$index.','.$label_id.','.$mat_rows.','.$mat_columns.')"/></td>';
              $var_checkbox++;
            }
          }
          else {
            for ($l = 1; $l <= $mat_columns; $l++) {
              $selected_text = $matrix_value[$mat_rows+$mat_columns + 2 + $var_checkbox];
              if ($selected_text == 'yes') {
                $selected_value_yes = 'selected';
                $selected_value_no = '';
                $selected_value = '';
              }
              else {
                if ($selected_text=='no') {
                  $selected_value_yes ='';
                  $selected_value_no ='selected';
                  $selected_value ='';
                }
                else {
                  $selected_value_yes = '';
                  $selected_value_no ='';
                  $selected_value ='selected';
                }
              }
              $index = "'".$k.'_'.$l."'";
              $matrix .= '<td style="text-align:center;"><select name="'.$label_id.'_select_yes_noform_id_temp'.$k.'_'.$l.'"  id="'.$label_id.'_select_yes_noform_id_temp'.$k.'_'.$l.'" onChange="change_option_values('.$index.','.$label_id.','.$mat_rows.','.$mat_columns.')"><option value="" '.$selected_value.'></option><option value="yes" '.$selected_value_yes.' >Yes</option><option value="no" '.$selected_value_no.'>No</option></select></td>';
              $var_checkbox++;
            }
          }
        }
      }
      $matrix .= '</tr>';
    }
    $matrix .= '</table>';
    array_push($type_matrix_array, $matrix);
    array_push($type_matrix_array, $new_filename);
    return $type_matrix_array;
  }
  
  public function select_data_from_db_for_labels($db_info,$label_column, $table, $where, $order_by) {
    global $wpdb;
        
		$query = "SELECT `" . $label_column . "` FROM " . $table . $where . " ORDER BY " . $order_by;
		if($db_info) { 
      $temp		= explode('@@@wdfhostwdf@@@',$db_info);
      $host		= $temp[0];
      $temp		= explode('@@@wdfportwdf@@@',$temp[1]);
      $port		= $temp[0];
      $temp		= explode('@@@wdfusernamewdf@@@',$temp[1]);
      $username	= $temp[0];
      $temp		= explode('@@@wdfpasswordwdf@@@',$temp[1]);
      $password	= $temp[0];
      $temp		= explode('@@@wdfdatabasewdf@@@',$temp[1]);
      $database	= $temp[0];
       
      $wpdb_temp = new wpdb($username, $password, $database, $host);
      $choices_labels = $wpdb_temp->get_col($query);				
    }
		else {
      $choices_labels = $wpdb->get_col($query);
    }
    return $choices_labels;
  }
  public function select_data_from_db_for_values($db_info,$value_column, $table, $where, $order_by) {
    global $wpdb;		  
		$query = "SELECT `" . $value_column . "` FROM " . $table . $where . " ORDER BY " . $order_by;
		if($db_info) {
      $temp		= explode('@@@wdfhostwdf@@@',$db_info);
      $host		= $temp[0];
      $temp		= explode('@@@wdfportwdf@@@',$temp[1]);
      $port		= $temp[0];
      $temp		= explode('@@@wdfusernamewdf@@@',$temp[1]);
      $username	= $temp[0];
      $temp		= explode('@@@wdfpasswordwdf@@@',$temp[1]);
      $password	= $temp[0];
      $temp		= explode('@@@wdfdatabasewdf@@@',$temp[1]);
      $database	= $temp[0];
       
      $wpdb_temp = new wpdb($username, $password, $database, $host);
      $choices_values = $wpdb_temp->get_col($query);				
    }
		else {
      $choices_values = $wpdb->get_col($query);
    }
    return $choices_values; 
  }
  public function get_subs_count($form_id) { 
    global $wpdb;
	$query = $wpdb->prepare("SELECT distinct group_id FROM " . $wpdb->prefix . "formmaker_submits where form_id=%d", $form_id);		
	$group_id_s = $wpdb->get_col($query);	
    return count($group_id_s);
  }
}

?>
