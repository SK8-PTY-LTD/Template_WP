<?php

class FMModelManage_fmc {
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
  public function get_rows_data() {
    global $wpdb;
	$where = 'WHERE `id` IN (' . (get_option('contact_form_forms', '') != '' ? get_option('contact_form_forms') : 0) . ')';
    $where .= ((isset($_POST['search_value']) && (esc_html($_POST['search_value']) != '')) ? 'WHERE title LIKE "%' . esc_html($_POST['search_value']) . '%"' : '');
    $asc_or_desc = ((isset($_POST['asc_or_desc']) && $_POST['asc_or_desc'] == 'desc') ? 'desc' : 'asc');
	$order_by_array = array('id', 'title', 'mail');
	$order_by = isset($_POST['order_by']) && in_array(esc_html(stripslashes($_POST['order_by'])), $order_by_array) ? esc_html(stripslashes($_POST['order_by'])) :  'id';
    $order_by = ' ORDER BY `' . $order_by . '` ' . $asc_or_desc;
    if (isset($_POST['page_number']) && $_POST['page_number']) {
      $limit = ((int) $_POST['page_number'] - 1) * 20;
    }
    else {
      $limit = 0;
    }
    $query = "SELECT * FROM " . $wpdb->prefix . "formmaker " . $where . $order_by . " LIMIT " . $limit . ",20";
    $rows = $wpdb->get_results($query);
    return $rows;
  }
  
  public function get_row_data($id) {
    global $wpdb;
    if ($id != 0) {
      $row = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'formmaker WHERE id="%d"', $id));
    }
    else {
      $row = new stdClass();
      $row->id = 0;
      $row->title = '';
      $row->mail = '';
      $row->form = '';
      $row->form_front = '';
      $row->theme = 0;
      $row->javascript = '';
      $row->submit_text = '';
      $row->url = '';
      $row->submit_text_type = 0;
      $row->script1 = '';
      $row->script2 = '';
      $row->script_user1 = '';
      $row->script_user2 = '';
      $row->counter = 0;
      $row->label_order = '';
      $row->article_id = '';
      $row->pagination = '';
      $row->show_title = '';
      $row->show_numbers = '';
      $row->public_key = '';
      $row->private_key = '';
      $row->recaptcha_theme = '';
      $row->from_name = '';
      $row->from_mail = '';
      $row->label_order_current = '';
      $row->script_mail_user = '';
      $row->script_mail = '';
      $row->tax = 0;
      $row->payment_currency = '$';
      $row->paypal_email = '';
      $row->checkout_mode = 'testmode';
      $row->paypal_mode = 0;

      $row->published = 1;
      $row->form_fields = '';
      $row->savedb = 1;
      $row->sendemail = 1;
      $row->requiredmark = '*';
      $row->reply_to = 0;
      $row->send_to = 0;
      $row->autogen_layout = 1;
      $row->custom_front = '';
      $row->mail_from_user = '';
      $row->mail_from_name_user = '';
      $row->reply_to_user = '';
      $row->save_uploads = 1;
    }
    return $row;
  }

  public function get_row_data_new($id) {
    global $wpdb;
    if ($id != 0) {
      $row = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'formmaker_backup WHERE backup_id="%d"', $id));
      $labels2 = array();
      $label_id = array();
      $label_order_original = array(); 
      $label_type = array();
      $label_all = explode('#****#', $row->label_order);
      $label_all = array_slice($label_all, 0, count($label_all) - 1);
  		foreach($label_all as $key => $label_each) {
        $label_id_each=explode('#**id**#',$label_each);
        array_push($label_id, $label_id_each[0]);
        $label_oder_each=explode('#**label**#', $label_id_each[1]);
        array_push($label_order_original, addslashes($label_oder_each[0]));
        array_push($label_type, $label_oder_each[1]);
  		}
      $labels2['id'] = '"' . implode('","', $label_id) . '"';
      $labels2['label'] = '"' . implode('","', $label_order_original) . '"';
      $labels2['type'] = '"' . implode('","', $label_type) . '"';
      $ids = array();
      $types = array();
      $labels = array();
      $paramss = array();
      $fields = explode('*:*new_field*:*', $row->form_fields);
      $fields = array_slice($fields, 0, count($fields) - 1);   
      foreach ($fields as $field) {
        $temp=explode('*:*id*:*',$field);
        array_push($ids, $temp[0]);
        $temp=explode('*:*type*:*',$temp[1]);
        array_push($types, $temp[0]);
        $temp=explode('*:*w_field_label*:*',$temp[1]);
        array_push($labels, $temp[0]);
        array_push($paramss, $temp[1]);
      }
      $form = $row->form_front;
      foreach ($ids as $ids_key => $id) {
        $label = $labels[$ids_key];
        $type = $types[$ids_key];
        $params = $paramss[$ids_key];
        if (strpos($form, '%'.$id.' - '.$label.'%') || strpos($form, '%'.$id.' -'.$label.'%')) {
          $rep = '';
          $arrows='';
          $param = array();
          $param['attributes'] = '';
          switch($type)
          {
            case 'type_section_break':
            {
              $arrows =$arrows.'<div id="wdform_arrows'.$id.'" class="wdform_arrows" ><div id="X_'.$id.'" class="element_toolbar"><img src="' . WD_FMC_URL . '/images/delete_el.png?ver='. get_option("wd_form_maker_version").'" title="Remove the field" onclick="remove_section_break(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;delete_el&quot;)" onmouseout="chnage_icons_src(this,&quot;delete_el&quot;)"></div><div id="edit_'.$id.'" class="element_toolbar"><img src="' . WD_FMC_URL . '/images/edit.png?ver='. get_option("wd_form_maker_version").'" title="Edit the field" onclick="edit(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;edit&quot;)" onmouseout="chnage_icons_src(this,&quot;edit&quot;)"><span id="'.$id.'_element_labelform_id_temp" style="display: none;">custom_'.$id.'</span></div><div id="duplicate_'.$id.'" class="element_toolbar"><img src="' . WD_FMC_URL . '/images/duplicate.png?ver='. get_option("wd_form_maker_version").'" title="Duplicate the field" onclick="duplicate(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;duplicate&quot;)" onmouseout="chnage_icons_src(this,&quot;duplicate&quot;)"></div></div>';
              break;
            }
            case 'type_editor':
            {
              $arrows =$arrows.'<div id="wdform_arrows'.$id.'" class="wdform_arrows" type="type_editor" style="margin-top:0px;"><div id="X_'.$id.'" valign="middle" align="right" class="element_toolbar"><img src="' . WD_FMC_URL . '/images/delete_el.png?ver='. get_option("wd_form_maker_version").'" title="Remove the field" onclick="remove_row(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;delete_el&quot;)" onmouseout="chnage_icons_src(this,&quot;delete_el&quot;)"></div><div id="left_'.$id.'" valign="middle" class="element_toolbar"><img src="' . WD_FMC_URL . '/images/left.png?ver='. get_option("wd_form_maker_version").'" title="Move the field to the left" onclick="left_row(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;left&quot;)" onmouseout="chnage_icons_src(this,&quot;left&quot;)"></div><div id="up_'.$id.'" valign="middle" class="element_toolbar"><img src="' . WD_FMC_URL . '/images/up.png?ver='. get_option("wd_form_maker_version").'" title="Move the field up" onclick="up_row(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;up&quot;)" onmouseout="chnage_icons_src(this,&quot;up&quot;)"></div><div id="down_'.$id.'" valign="middle" class="element_toolbar"><img src="' . WD_FMC_URL . '/images/down.png?ver='. get_option("wd_form_maker_version").'" title="Move the field down" onclick="down_row(&quot;'.$id.'&quot;)"  onmouseover="chnage_icons_src(this,&quot;down&quot;)" onmouseout="chnage_icons_src(this,&quot;down&quot;)"></div><div id="right_'.$id.'" valign="middle" class="element_toolbar"><img src="' . WD_FMC_URL . '/images/right.png?ver='. get_option("wd_form_maker_version").'" title="Move the field to the right" onclick="right_row(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;right&quot;)" onmouseout="chnage_icons_src(this,&quot;right&quot;)"></div><div id="edit_'.$id.'" valign="middle" class="element_toolbar"><img src="' . WD_FMC_URL . '/images/edit.png?ver='. get_option("wd_form_maker_version").'" title="Edit the field" onclick="edit(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;edit&quot;)" onmouseout="chnage_icons_src(this,&quot;edit&quot;)"></div><div id="duplicate_'.$id.'" valign="middle" class="element_toolbar"><img src="' . WD_FMC_URL . '/images/duplicate.png?ver='. get_option("wd_form_maker_version").'" title="Duplicate the field" onclick="duplicate(&quot;'.$id.'&quot;)"  onmouseover="chnage_icons_src(this,&quot;duplicate&quot;)" onmouseout="chnage_icons_src(this,&quot;duplicate&quot;)"></div><div id="page_up_'.$id.'" valign="middle" class="element_toolbar"><img src="' . WD_FMC_URL . '/images/page_up.png?ver='. get_option("wd_form_maker_version").'" title="Move the field to the upper page" onclick="page_up(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;page_up&quot;)" onmouseout="chnage_icons_src(this,&quot;page_up&quot;)"></div><div id="page_down_'.$id.'" valign="middle" class="element_toolbar"><img src="' . WD_FMC_URL . '/images/page_down.png?ver='. get_option("wd_form_maker_version").'" title="Move the field to the lower page" onclick="page_down(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;page_down&quot;)" onmouseout="chnage_icons_src(this,&quot;page_down&quot;)"></div></div>';
              break;
            }
            
            case 'type_send_copy':
            case 'type_captcha':
			case 'type_arithmetic_captcha':
            case 'type_recaptcha':
            {
              $arrows =$arrows.'<div id="wdform_arrows'.$id.'" class="wdform_arrows"><div id="X_'.$id.'" valign="middle" align="right" class="element_toolbar"><img src="' . WD_FMC_URL . '/images/delete_el.png?ver='. get_option("wd_form_maker_version").'" title="Remove the field" onclick="remove_row(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;delete_el&quot;)" onmouseout="chnage_icons_src(this,&quot;delete_el&quot;)"></div><div id="left_'.$id.'" valign="middle" class="element_toolbar"><img src="' . WD_FMC_URL . '/images/left.png?ver='. get_option("wd_form_maker_version").'" title="Move the field to the left" onclick="left_row(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;left&quot;)" onmouseout="chnage_icons_src(this,&quot;left&quot;)"></div><div id="up_'.$id.'" valign="middle" class="element_toolbar"><img src="' . WD_FMC_URL . '/images/up.png?ver='. get_option("wd_form_maker_version").'" title="Move the field up" onclick="up_row(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;up&quot;)" onmouseout="chnage_icons_src(this,&quot;up&quot;)"></div><div id="down_'.$id.'" valign="middle" class="element_toolbar"><img src="' . WD_FMC_URL . '/images/down.png?ver='. get_option("wd_form_maker_version").'" title="Move the field down" onclick="down_row(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;down&quot;)" onmouseout="chnage_icons_src(this,&quot;down&quot;)"></div><div id="right_'.$id.'" valign="middle" class="element_toolbar"><img src="' . WD_FMC_URL . '/images/right.png?ver='. get_option("wd_form_maker_version").'" title="Move the field to the right" onclick="right_row(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;right&quot;)" onmouseout="chnage_icons_src(this,&quot;right&quot;)"></div><div id="edit_'.$id.'" valign="middle" class="element_toolbar"><img src="' . WD_FMC_URL . '/images/edit.png?ver='. get_option("wd_form_maker_version").'" title="Edit the field" onclick="edit(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;edit&quot;)" onmouseout="chnage_icons_src(this,&quot;edit&quot;)"></div><div id="duplicate_'.$id.'" valign="middle" class="element_toolbar"></div><div id="page_up_'.$id.'" valign="middle" class="element_toolbar"><img src="' . WD_FMC_URL . '/images/page_up.png?ver='. get_option("wd_form_maker_version").'" title="Move the field to the upper page" onclick="page_up(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;page_up&quot;)" onmouseout="chnage_icons_src(this,&quot;page_up&quot;)"></div><div id="page_down_'.$id.'" valign="middle" class="element_toolbar"><img src="' . WD_FMC_URL . '/images/page_down.png?ver='. get_option("wd_form_maker_version").'" title="Move the field to the lower page" onclick="page_down(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;page_down&quot;)" onmouseout="chnage_icons_src(this,&quot;page_down&quot;)"></div></div>';
              break;
            }
            
            default :
            {
              $arrows =$arrows.'<div id="wdform_arrows'.$id.'" class="wdform_arrows" ><div id="X_'.$id.'" valign="middle" align="right" class="element_toolbar"><img src="' . WD_FMC_URL . '/images/delete_el.png?ver='. get_option("wd_form_maker_version").'" title="Remove the field" onclick="remove_row(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;delete_el&quot;)" onmouseout="chnage_icons_src(this,&quot;delete_el&quot;)"></div><div id="left_'.$id.'" valign="middle" class="element_toolbar"><img src="' . WD_FMC_URL . '/images/left.png?ver='. get_option("wd_form_maker_version").'" title="Move the field to the left" onclick="left_row(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;left&quot;)" onmouseout="chnage_icons_src(this,&quot;left&quot;)"></div><div id="up_'.$id.'" valign="middle" class="element_toolbar"><img src="' . WD_FMC_URL . '/images/up.png?ver='. get_option("wd_form_maker_version").'" title="Move the field up" onclick="up_row(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;up&quot;)" onmouseout="chnage_icons_src(this,&quot;up&quot;)"></div><div id="down_'.$id.'" valign="middle" class="element_toolbar"><img src="' . WD_FMC_URL . '/images/down.png?ver='. get_option("wd_form_maker_version").'" title="Move the field down" onclick="down_row(&quot;'.$id.'&quot;)"  onmouseover="chnage_icons_src(this,&quot;down&quot;)" onmouseout="chnage_icons_src(this,&quot;down&quot;)"></div><div id="right_'.$id.'" valign="middle" class="element_toolbar"><img src="' . WD_FMC_URL . '/images/right.png?ver='. get_option("wd_form_maker_version").'" title="Move the field to the right" onclick="right_row(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;right&quot;)" onmouseout="chnage_icons_src(this,&quot;right&quot;)"></div><div id="edit_'.$id.'" valign="middle" class="element_toolbar"><img src="' . WD_FMC_URL . '/images/edit.png?ver='. get_option("wd_form_maker_version").'" title="Edit the field" onclick="edit(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;edit&quot;)" onmouseout="chnage_icons_src(this,&quot;edit&quot;)"></div><div id="duplicate_'.$id.'" valign="middle" class="element_toolbar"><img src="' . WD_FMC_URL . '/images/duplicate.png?ver='. get_option("wd_form_maker_version").'" title="Duplicate the field" onclick="duplicate(&quot;'.$id.'&quot;)"  onmouseover="chnage_icons_src(this,&quot;duplicate&quot;)" onmouseout="chnage_icons_src(this,&quot;duplicate&quot;)"></div><div id="page_up_'.$id.'" valign="middle" class="element_toolbar"><img src="' . WD_FMC_URL . '/images/page_up.png?ver='. get_option("wd_form_maker_version").'" title="Move the field to the upper page" onclick="page_up(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;page_up&quot;)" onmouseout="chnage_icons_src(this,&quot;page_up&quot;)"></div><div id="page_down_'.$id.'" valign="middle" class="element_toolbar"><img src="' . WD_FMC_URL . '/images/page_down.png?ver='. get_option("wd_form_maker_version").'" title="Move the field to the lower page" onclick="page_down(&quot;'.$id.'&quot;)" onmouseover="chnage_icons_src(this,&quot;page_down&quot;)" onmouseout="chnage_icons_src(this,&quot;page_down&quot;)"></div></div>';
              break;
            }
            
          }
          switch ($type) {
            case 'type_section_break': {
              $params_names = array('w_editor');
              $temp = $params;
              foreach ($params_names as $params_name) {
                $temp = explode('*:*' . $params_name . '*:*', $temp);
                $param[$params_name] = $temp[0];
                $temp = $temp[1];
              }
              $rep ='<div id="wdform_field'.$id.'" type="type_section_break" class="wdform_field_section_break">'.$arrows.'<span id="'.$id.'_element_labelform_id_temp" style="display: none;">custom_'.$id.'</span><div id="'.$id.'_element_sectionform_id_temp" align="left" class="wdform_section_break">'.$param['w_editor'].'</div></div><div id="'.$id.'_element_labelform_id_temp" style="color:red;">custom_'.$id.'</div>';
              break;
            }
            case 'type_editor': {
              $params_names = array('w_editor');
              $temp = $params;
              foreach ($params_names as $params_name ) {
                $temp = explode('*:*' . $params_name . '*:*', $temp);
                $param[$params_name] = $temp[0];
                $temp = $temp[1];
              }
              $rep =$arrows.'<div id="wdform_field'.$id.'" type="type_editor" class="wdform_field" >'.$param['w_editor'].'</div><div id="'.$id.'_element_labelform_id_temp" style="color: red;">custom_'.$id.'</div>';
              break;
            }
            case 'type_send_copy': {
              $params_names = array('w_field_label_size', 'w_field_label_pos', 'w_first_val', 'w_required');
              $temp = $params;
              foreach ($params_names as $params_name ) {
                $temp = explode('*:*' . $params_name . '*:*', $temp);
                $param[$params_name] = $temp[0];
                $temp = $temp[1];
              }
              if ($temp) {	
                $temp	= explode('*:*w_attr_name*:*', $temp);
                $attrs = array_slice($temp, 0, count($temp) - 1);
                foreach ($attrs as $attr) {
                  $param['attributes'] = $param['attributes'] . ' add_' . $attr;
                }
              }
              $param['w_field_label_pos'] = ($param['w_field_label_pos'] == "left" ? "table-cell" : "block");	
              $input_active = ($param['w_first_val'] == 'true' ? "checked='checked'" : "");
              $required_sym = ($param['w_required'] == "yes" ? " *" : "");
              $rep ='<div id="wdform_field'.$id.'" type="type_send_copy" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" style="display: '.$param['w_field_label_pos'].'"><input type="hidden" value="type_send_copy" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp" /><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp" /><input type="checkbox" id="'.$id.'_elementform_id_temp" name="'.$id.'_elementform_id_temp" onclick="set_checked(&quot;'.$id.'&quot;,&quot;&quot;,&quot;form_id_temp&quot;)" '.$input_active.' '.$param['attributes'].' disabled /></div></div>';
              break;
            }
            case 'type_text': {
				$params_names = array('w_field_label_size', 'w_field_label_pos', 'w_size', 'w_first_val', 'w_title', 'w_required', 'w_unique');
				$temp = $params;
				if(strpos($temp, 'w_regExp_status') > -1)
					$params_names = array('w_field_label_size','w_field_label_pos','w_size','w_first_val','w_title','w_required', 'w_regExp_status', 'w_regExp_value', 'w_regExp_common', 'w_regExp_arg', 'w_regExp_alert', 'w_unique');
				foreach ($params_names as $params_name) {
					$temp = explode('*:*' . $params_name . '*:*', $temp);
					$param[$params_name] = $temp[0];
					$temp = $temp[1];
				}
				if ($temp) {
					$temp	= explode('*:*w_attr_name*:*', $temp);
					$attrs = array_slice($temp, 0, count($temp) - 1);
					foreach ($attrs as $attr) {
					  $param['attributes'] = $param['attributes'] . ' add_' . $attr;
					}
				}
				
				$param['w_field_label_pos'] = ($param['w_field_label_pos'] == "left" ? "table-cell" : "block");
				$input_active = ($param['w_first_val'] == $param['w_title'] ? "input_deactive" : "input_active");
				$required_sym = ($param['w_required'] == "yes" ? " *" : "");
				
				$param['w_regExp_status'] = (isset($param['w_regExp_status']) ? $param['w_regExp_status'] : "no");
				$param['w_regExp_value'] = (isset($param['w_regExp_value']) ? $param['w_regExp_value'] : "");
				$param['w_regExp_common'] = (isset($param['w_regExp_common']) ? $param['w_regExp_common'] : "");
				$param['w_regExp_arg'] = (isset($param['w_regExp_arg']) ? $param['w_regExp_arg'] : "");
				$param['w_regExp_alert'] = (isset($param['w_regExp_alert']) ? $param['w_regExp_alert'] : "Incorrect Value");
				
				$rep ='<div id="wdform_field'.$id.'" type="type_text" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" style="display: '.$param['w_field_label_pos'].'"><input type="hidden" value="type_text" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp" /><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp" /><input type="hidden" value="'.$param['w_regExp_status'].'" name="'.$id.'_regExpStatusform_id_temp" id="'.$id.'_regExpStatusform_id_temp"><input type="hidden" value="'.$param['w_regExp_value'].'" name="'.$id.'_regExp_valueform_id_temp" id="'.$id.'_regExp_valueform_id_temp"><input type="hidden" value="'.$param['w_regExp_common'].'" name="'.$id.'_regExp_commonform_id_temp" id="'.$id.'_regExp_commonform_id_temp"><input type="hidden" value="'.$param['w_regExp_alert'].'" name="'.$id.'_regExp_alertform_id_temp" id="'.$id.'_regExp_alertform_id_temp"><input type="hidden" value="'.$param['w_regExp_arg'].'" name="'.$id.'_regArgumentform_id_temp" id="'.$id.'_regArgumentform_id_temp"><input type="hidden" value="'.$param['w_unique'].'" name="'.$id.'_uniqueform_id_temp" id="'.$id.'_uniqueform_id_temp" /><input type="text" class="'.$input_active.'" id="'.$id.'_elementform_id_temp" name="'.$id.'_elementform_id_temp" value="'.$param['w_first_val'].'" title="'.$param['w_title'].'" onfocus="delete_value(&quot;'.$id.'_elementform_id_temp&quot;)" onblur="return_value(&quot;'.$id.'_elementform_id_temp&quot;)" onchange="change_value(&quot;'.$id.'_elementform_id_temp&quot;)" style="width: '.$param['w_size'].'px;" '.$param['attributes'].' disabled /></div></div>';
				
				break;
            }
           case 'type_number': {
              $params_names = array('w_field_label_size', 'w_field_label_pos', 'w_size', 'w_first_val', 'w_title', 'w_required', 'w_unique', 'w_class');
              $temp = $params;
              foreach ($params_names as $params_name) {
                $temp = explode('*:*' . $params_name . '*:*', $temp);
                $param[$params_name] = $temp[0];
                $temp = $temp[1];
              }
              if ($temp) {
                $temp	= explode('*:*w_attr_name*:*', $temp);
                $attrs = array_slice($temp, 0, count($temp) - 1);   
                foreach ($attrs as $attr) {
                  $param['attributes'] = $param['attributes'] . ' add_' . $attr;
                }
              }
              $param['w_field_label_pos'] = ($param['w_field_label_pos'] == "left" ? "table-cell" : "block");
              $input_active = ($param['w_first_val'] == $param['w_title'] ? "input_deactive" : "input_active");
              $required_sym = ($param['w_required'] == "yes" ? " *" : "");
              $rep ='<div id="wdform_field'.$id.'" type="type_number" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp"  class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'"><input type="hidden" value="type_number" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_unique'].'" name="'.$id.'_uniqueform_id_temp" id="'.$id.'_uniqueform_id_temp"><input type="text" class="'.$input_active.'" id="'.$id.'_elementform_id_temp" name="'.$id.'_elementform_id_temp" value="'.$param['w_first_val'].'" title="'.$param['w_title'].'" onkeypress="return check_isnum(event)" onfocus="delete_value(&quot;'.$id.'_elementform_id_temp&quot;)" onblur="return_value(&quot;'.$id.'_elementform_id_temp&quot;)" onchange="change_value(&quot;'.$id.'_elementform_id_temp&quot;)" style="width: '.$param['w_size'].'px;" '.$param['attributes'].' disabled /></div></div>';
              break;
            }
            case 'type_password': {
              $params_names = array('w_field_label_size', 'w_field_label_pos', 'w_size', 'w_required', 'w_unique', 'w_class');
              $temp = $params;
              foreach ($params_names as $params_name) {
                $temp = explode('*:*' . $params_name . '*:*', $temp);
                $param[$params_name] = $temp[0];
                $temp = $temp[1];
              }
              if ($temp) {
                $temp	= explode('*:*w_attr_name*:*', $temp);
                $attrs = array_slice($temp, 0, count($temp) - 1);   
                foreach ($attrs as $attr) {
                  $param['attributes'] = $param['attributes'] . ' add_' . $attr;
                }
              }
              $param['w_field_label_pos'] = ($param['w_field_label_pos'] == "left" ? "table-cell" : "block");	
              $required_sym = ($param['w_required'] == "yes" ? " *" : "");	
              $rep ='<div id="wdform_field'.$id.'" type="type_password" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp"  class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'"><input type="hidden" value="type_password" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_unique'].'" name="'.$id.'_uniqueform_id_temp" id="'.$id.'_uniqueform_id_temp"><input type="password" id="'.$id.'_elementform_id_temp" name="'.$id.'_elementform_id_temp" style="width: '.$param['w_size'].'px;" '.$param['attributes'].' disabled /></div></div>';
              break;
            }
            case 'type_textarea': {
              $params_names = array('w_field_label_size', 'w_field_label_pos', 'w_size_w', 'w_size_h', 'w_first_val', 'w_title', 'w_required', 'w_unique', 'w_class');
              $temp = $params;
              foreach ($params_names as $params_name) {
                $temp = explode('*:*' . $params_name . '*:*', $temp);
                $param[$params_name] = $temp[0];
                $temp = $temp[1];
              }
              if ($temp) {
                $temp	= explode('*:*w_attr_name*:*', $temp);
                $attrs = array_slice($temp, 0, count($temp) - 1);
                foreach ($attrs as $attr) {
                  $param['attributes'] = $param['attributes'] . ' add_' . $attr;
                }
              }
              $param['w_field_label_pos'] = ($param['w_field_label_pos'] == "left" ? "table-cell" : "block");
              $input_active = ($param['w_first_val'] == $param['w_title'] ? "input_deactive" : "input_active");
              $required_sym = ($param['w_required'] == "yes" ? " *" : "");
              $rep ='<div id="wdform_field'.$id.'" type="type_textarea" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display:'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: table-cell;"><input type="hidden" value="type_textarea" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_unique'].'" name="'.$id.'_uniqueform_id_temp" id="'.$id.'_uniqueform_id_temp"><textarea class="'.$input_active.'" id="'.$id.'_elementform_id_temp" name="'.$id.'_elementform_id_temp" title="'.$param['w_title'].'"  onfocus="delete_value(&quot;'.$id.'_elementform_id_temp&quot;)" onblur="return_value(&quot;'.$id.'_elementform_id_temp&quot;)" onchange="change_value(&quot;'.$id.'_elementform_id_temp&quot;)" style="width: '.$param['w_size_w'].'px; height: '.$param['w_size_h'].'px;" '.$param['attributes'].' disabled>'.$param['w_first_val'].'</textarea></div></div>';
              break;
            }
            case 'type_phone': {
              $params_names = array('w_field_label_size', 'w_field_label_pos', 'w_size', 'w_first_val', 'w_title', 'w_mini_labels', 'w_required', 'w_unique', 'w_class');
              $temp = $params;
              foreach ($params_names as $params_name) {
                $temp = explode('*:*' . $params_name . '*:*', $temp);
                $param[$params_name] = $temp[0];
                $temp = $temp[1];
              }
              if ($temp) {
                $temp	= explode('*:*w_attr_name*:*', $temp);
                $attrs = array_slice($temp, 0, count($temp) - 1);
                foreach ($attrs as $attr) {
                  $param['attributes'] = $param['attributes'] . ' add_' . $attr;
                }
              }
              $w_first_val = explode('***', $param['w_first_val']);
              $w_title = explode('***', $param['w_title']);
              $w_mini_labels = explode('***', $param['w_mini_labels']);
              $param['w_field_label_pos'] = ($param['w_field_label_pos'] == "left" ? "table-cell" : "block");
              $input_active = ($param['w_first_val'] == $param['w_title'] ? "input_deactive" : "input_active");
              $required_sym = ($param['w_required'] == "yes" ? " *" : "");
              $rep ='<div id="wdform_field'.$id.'" type="type_phone" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_phone" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_unique'].'" name="'.$id.'_uniqueform_id_temp" id="'.$id.'_uniqueform_id_temp"><div id="'.$id.'_table_name" style="display: table;"><div id="'.$id.'_tr_name1" style="display: table-row;"><div id="'.$id.'_td_name_input_first" style="display: table-cell;"><input type="text" class="'.$input_active.'" id="'.$id.'_element_firstform_id_temp" name="'.$id.'_element_firstform_id_temp" value="'.$w_first_val[0].'" title="'.$w_title[0].'" onfocus="delete_value(&quot;'.$id.'_element_firstform_id_temp&quot;)"onblur="return_value(&quot;'.$id.'_element_firstform_id_temp&quot;)"onchange="change_value(&quot;'.$id.'_element_firstform_id_temp&quot;)" onkeypress="return check_isnum(event)"style="width: 50px;" '.$param['attributes'].' disabled /><span class="wdform_line" style="margin: 0px 4px; padding: 0px;">-</span></div><div id="'.$id.'_td_name_input_last" style="display: table-cell;"><input type="text" class="'.$input_active.'" id="'.$id.'_element_lastform_id_temp" name="'.$id.'_element_lastform_id_temp" value="'.$w_first_val[1].'" title="'.$w_title[1].'" onfocus="delete_value(&quot;'.$id.'_element_lastform_id_temp&quot;)"onblur="return_value(&quot;'.$id.'_element_lastform_id_temp&quot;)" onchange="change_value(&quot;'.$id.'_element_lastform_id_temp&quot;)" onkeypress="return check_isnum(event)"style="width: '.$param['w_size'].'px;" '.$param['attributes'].' disabled /></div></div><div id="'.$id.'_tr_name2" style="display: table-row;"><div id="'.$id.'_td_name_label_first" align="left" style="display: table-cell;"><label class="mini_label" id="'.$id.'_mini_label_area_code">'.$w_mini_labels[0].'</label></div><div id="'.$id.'_td_name_label_last" align="left" style="display: table-cell;"><label class="mini_label" id="'.$id.'_mini_label_phone_number">'.$w_mini_labels[1].'</label></div></div></div></div></div>';
              break;
            }
            case 'type_name': {
              $params_names = array('w_field_label_size', 'w_field_label_pos', 'w_first_val', 'w_title', 'w_mini_labels', 'w_size', 'w_name_format', 'w_required', 'w_unique', 'w_class');
              $temp = $params;
			  if(strpos($temp, 'w_name_fields') > -1)
				$params_names = array('w_field_label_size', 'w_field_label_pos', 'w_first_val', 'w_title', 'w_mini_labels', 'w_size', 'w_name_format', 'w_required', 'w_unique', 'w_class', 'w_name_fields');
              foreach ($params_names as $params_name) {
                $temp = explode('*:*' . $params_name . '*:*', $temp);
                $param[$params_name] = $temp[0];
                $temp = $temp[1];
              }
              if ($temp) {
                $temp	= explode('*:*w_attr_name*:*', $temp);
                $attrs = array_slice($temp, 0, count($temp) - 1);
                foreach ($attrs as $attr) {
                  $param['attributes'] = $param['attributes'] . ' add_' . $attr;
                }
              }
              $param['w_field_label_pos'] = ($param['w_field_label_pos'] == "left" ? "table-cell" : "block");
              $required_sym = ($param['w_required'] == "yes" ? " *" : "");
              $w_first_val = explode('***', $param['w_first_val']);
              $w_title = explode('***', $param['w_title']);
              $w_mini_labels = explode('***', $param['w_mini_labels']);
			  
			  $param['w_name_fields'] =  isset($param['w_name_fields']) ? $param['w_name_fields'] : ($param['w_name_format'] == 'normal' ? 'no***no' : 'yes***yes');
			  $w_name_fields = explode('***', $param['w_name_fields']);

              $w_name_format = '<div id="'.$id.'_td_name_input_first" style="display: table-cell;"><input type="text" class="'.($w_first_val[0]==$w_title[0] ? "input_deactive" : "input_active").'" id="'.$id.'_element_firstform_id_temp" name="'.$id.'_element_firstform_id_temp" value="'.$w_first_val[0].'" title="'.$w_title[0].'" onfocus="delete_value(&quot;'.$id.'_element_firstform_id_temp&quot;)"onblur="return_value(&quot;'.$id.'_element_firstform_id_temp&quot;)" onchange="change_value(&quot;'.$id.'_element_firstform_id_temp&quot;)" style="margin-right: 10px; width: '.$param['w_size'].'px;"'.$param['attributes'].' disabled /></div><div id="'.$id.'_td_name_input_last" style="display: table-cell;"><input type="text" class="'.($w_first_val[1]==$w_title[1] ? "input_deactive" : "input_active").'" id="'.$id.'_element_lastform_id_temp" name="'.$id.'_element_lastform_id_temp" value="'.$w_first_val[1].'" title="'.$w_title[1].'" onfocus="delete_value(&quot;'.$id.'_element_lastform_id_temp&quot;)"onblur="return_value(&quot;'.$id.'_element_lastform_id_temp&quot;)" onchange="change_value(&quot;'.$id.'_element_lastform_id_temp&quot;)" style="margin-right: 10px; width: '.$param['w_size'].'px;" '.$param['attributes'].' disabled /></div>';
              $w_name_format_mini_labels = '<div id="'.$id.'_td_name_label_first" align="left" style="display: table-cell;"><label class="mini_label" id="'.$id.'_mini_label_first">'.$w_mini_labels[1].'</label></div><div id="'.$id.'_td_name_label_last" align="left" style="display: table-cell;"><label class="mini_label" id="'.$id.'_mini_label_last">'.$w_mini_labels[2].'</label></div>';
              
			  if($w_name_fields[0] == 'yes') {
				$w_name_format = '<div id="'.$id.'_td_name_input_title" style="display: table-cell;"><input type="text" class="'.($w_first_val[2]==$w_title[2] ? "input_deactive" : "input_active").'" id="'.$id.'_element_titleform_id_temp" name="'.$id.'_element_titleform_id_temp" value="'.$w_first_val[2].'" title="'.$w_title[2].'" onfocus="delete_value(&quot;'.$id.'_element_titleform_id_temp&quot;)" onblur="return_value(&quot;'.$id.'_element_titleform_id_temp&quot;)" onchange="change_value(&quot;'.$id.'_element_titleform_id_temp&quot;)" style="margin: 0px 10px 0px 0px; width: 40px;" disabled /></div>'.$w_name_format;
				$w_name_format_mini_labels ='<div id="'.$id.'_td_name_label_title" align="left" style="display: table-cell;"><label class="mini_label" id="'.$id.'_mini_label_title">'.$w_mini_labels[0].'</label></div>'.$w_name_format_mini_labels;
			  }
			  
			  if($w_name_fields[1] == 'yes') {
				$w_name_format = $w_name_format.'<div id="'.$id.'_td_name_input_middle" style="display: table-cell;"><input type="text" class="'.($w_first_val[3]==$w_title[3] ? "input_deactive" : "input_active").'" id="'.$id.'_element_middleform_id_temp" name="'.$id.'_element_middleform_id_temp" value="'.$w_first_val[3].'" title="'.$w_title[3].'" onfocus="delete_value(&quot;'.$id.'_element_middleform_id_temp&quot;)" onblur="return_value(&quot;'.$id.'_element_middleform_id_temp&quot;)" onchange="change_value(&quot;'.$id.'_element_middleform_id_temp&quot;)" style="width: '.$param['w_size'].'px;" disabled /></div>';
				$w_name_format_mini_labels = $w_name_format_mini_labels.'<div id="'.$id.'_td_name_label_middle" align="left" style="display: table-cell;"><label class="mini_label" id="'.$id.'_mini_label_middle">'.$w_mini_labels[3].'</label></div>';
			  }
			  
              $rep ='<div id="wdform_field'.$id.'" type="type_name" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_name" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_unique'].'" name="'.$id.'_uniqueform_id_temp" id="'.$id.'_uniqueform_id_temp"><input type="hidden" name="'.$id.'_enable_fieldsform_id_temp" id="'.$id.'_enable_fieldsform_id_temp" title="'.$w_name_fields[0].'" first="yes" last="yes" middle="'.$w_name_fields[1].'"><div id="'.$id.'_table_name" cellpadding="0" cellspacing="0" style="display: table;"><div id="'.$id.'_tr_name1" style="display: table-row;">'.$w_name_format.'</div><div id="'.$id.'_tr_name2" style="display: table-row;">'.$w_name_format_mini_labels.'</div></div></div></div>';
              break;
            }
            case 'type_address': {
              $params_names = array('w_field_label_size', 'w_field_label_pos', 'w_size', 'w_mini_labels', 'w_disabled_fields', 'w_required', 'w_class');
              $temp = $params;
              foreach ($params_names as $params_name) {
                $temp = explode('*:*' . $params_name . '*:*', $temp);
                $param[$params_name] = $temp[0];
                $temp = $temp[1];
              }
              if ($temp) {
                $temp	= explode('*:*w_attr_name*:*', $temp);
                $attrs = array_slice($temp, 0, count($temp) - 1);
                foreach ($attrs as $attr) {
                  $param['attributes'] = $param['attributes'] . ' add_' . $attr;
                }
              }
              $param['w_field_label_pos'] = ($param['w_field_label_pos'] == "left" ? "table-cell" : "block");
              $required_sym = ($param['w_required'] == "yes" ? " *" : "");
              $w_mini_labels = explode('***', $param['w_mini_labels']);
              $w_disabled_fields = explode('***', $param['w_disabled_fields']);
              $hidden_inputs = '';
              $labels_for_id = array('street1', 'street2', 'city', 'state', 'postal', 'country');
              foreach ($w_disabled_fields as $key => $w_disabled_field) {
                if ($key != 6) {
                  if ($w_disabled_field == 'yes') {
                    $hidden_inputs .= '<input type="hidden" id="'.$id.'_'.$labels_for_id[$key].'form_id_temp" value="'.$w_mini_labels[$key].'" id_for_label="'.($id+$key).'">';
                  }
                }
              }
              $address_fields ='';
					$g=0;
					if($w_disabled_fields[0]=='no')
					{
					$g+=2;
					$address_fields .= '<span style="float: left; width: 100%; padding-bottom: 8px; display: block;"><input type="text" id="'.$id.'_street1form_id_temp" name="'.$id.'_street1form_id_temp" onchange="change_value(&quot;'.$id.'_street1form_id_temp&quot;)" style="width: 100%;" '.$param['attributes'].' disabled /><label class="mini_label" id="'.$id.'_mini_label_street1" style="display: block;">'.$w_mini_labels[0].'</label></span>';
					}
					
					if($w_disabled_fields[1]=='no')
					{
					$g+=2;
					$address_fields .= '<span style="float: left; width: 100%; padding-bottom: 8px; display: block;"><input type="text" id="'.$id.'_street2form_id_temp" name="'.($id+1).'_street2form_id_temp" onchange="change_value(&quot;'.$id.'_street2form_id_temp&quot;)" style="width: 100%;" '.$param['attributes'].' disabled /><label class="mini_label" style="display: block;" id="'.$id.'_mini_label_street2">'.$w_mini_labels[1].'</label></span>';
					}
					
					if($w_disabled_fields[2]=='no')
					{
					$g++;
					$address_fields .= '<span style="float: left; width: 48%; padding-bottom: 8px;"><input type="text" id="'.$id.'_cityform_id_temp" name="'.($id+2).'_cityform_id_temp" onchange="change_value(&quot;'.$id.'_cityform_id_temp&quot;)" style="width: 100%;" '.$param['attributes'].' disabled /><label class="mini_label" style="display: block;" id="'.$id.'_mini_label_city">'.$w_mini_labels[2].'</label></span>';
					}
					
					if($w_disabled_fields[3]=='no')
					{
					$g++;
					if($w_disabled_fields[5]=='yes' && $w_disabled_fields[6]=='yes')
					$address_fields .= '<span style="float: '.(($g%2==0) ? 'right' : 'left').'; width: 48%; padding-bottom: 8px;"><select type="text" id="'.$id.'_stateform_id_temp" name="'.($id+3).'_stateform_id_temp" onchange="change_value(&quot;'.$id.'_stateform_id_temp&quot;)" style="width: 100%;" '.$param['attributes'].' disabled ><option value=""></option><option value="Alabama">Alabama</option><option value="Alaska">Alaska</option><option value="Arizona">Arizona</option><option value="Arkansas">Arkansas</option><option value="California">California</option><option value="Colorado">Colorado</option><option value="Connecticut">Connecticut</option><option value="Delaware">Delaware</option><option value="Florida">Florida</option><option value="Georgia">Georgia</option><option value="Hawaii">Hawaii</option><option value="Idaho">Idaho</option><option value="Illinois">Illinois</option><option value="Indiana">Indiana</option><option value="Iowa">Iowa</option><option value="Kansas">Kansas</option><option value="Kentucky">Kentucky</option><option value="Louisiana">Louisiana</option><option value="Maine">Maine</option><option value="Maryland">Maryland</option><option value="Massachusetts">Massachusetts</option><option value="Michigan">Michigan</option><option value="Minnesota">Minnesota</option><option value="Mississippi">Mississippi</option><option value="Missouri">Missouri</option><option value="Montana">Montana</option><option value="Nebraska">Nebraska</option><option value="Nevada">Nevada</option><option value="New Hampshire">New Hampshire</option><option value="New Jersey">New Jersey</option><option value="New Mexico">New Mexico</option><option value="New York">New York</option><option value="North Carolina">North Carolina</option><option value="North Dakota">North Dakota</option><option value="Ohio">Ohio</option><option value="Oklahoma">Oklahoma</option><option value="Oregon">Oregon</option><option value="Pennsylvania">Pennsylvania</option><option value="Rhode Island">Rhode Island</option><option value="South Carolina">South Carolina</option><option value="South Dakota">South Dakota</option><option value="Tennessee">Tennessee</option><option value="Texas">Texas</option><option value="Utah">Utah</option><option value="Vermont">Vermont</option><option value="Virginia">Virginia</option><option value="Washington">Washington</option><option value="West Virginia">West Virginia</option><option value="Wisconsin">Wisconsin</option><option value="Wyoming">Wyoming</option></select><label class="mini_label" style="display: block;" id="'.$id.'_mini_label_state">'.$w_mini_labels[3].'</label></span>';
					else
					$address_fields .= '<span style="float: '.(($g%2==0) ? 'right' : 'left').'; width: 48%; padding-bottom: 8px;"><input type="text" id="'.$id.'_stateform_id_temp" name="'.($id+3).'_stateform_id_temp" onchange="change_value(&quot;'.$id.'_stateform_id_temp&quot;)" style="width: 100%;" '.$param['attributes'].' disabled><label class="mini_label" style="display: block;" id="'.$id.'_mini_label_state">'.$w_mini_labels[3].'</label></span>';
					}
					
					if($w_disabled_fields[4]=='no')
					{
					$g++;
					$address_fields .= '<span style="float: '.(($g%2==0) ? 'right' : 'left').'; width: 48%; padding-bottom: 8px;"><input type="text" id="'.$id.'_postalform_id_temp" name="'.($id+4).'_postalform_id_temp" onchange="change_value(&quot;'.$id.'_postalform_id_temp&quot;)" style="width: 100%;" '.$param['attributes'].' disabled><label class="mini_label" style="display: block;" id="'.$id.'_mini_label_postal">'.$w_mini_labels[4].'</label></span>';
					}
					
					if($w_disabled_fields[5]=='no')
					{
					$g++;
					$address_fields .= '<span style="float: '.(($g%2==0) ? 'right' : 'left').'; width: 48%; padding-bottom: 8px;"><select type="text" id="'.$id.'_countryform_id_temp" name="'.($id+5).'_countryform_id_temp" onchange="change_value(&quot;'.$id.'_countryform_id_temp&quot;)" style="width: 100%;" '.$param['attributes'].' disabled><option value=""></option><option value="Afghanistan">Afghanistan</option><option value="Albania">Albania</option><option value="Algeria">Algeria</option><option value="Andorra">Andorra</option><option value="Angola">Angola</option><option value="Antigua and Barbuda">Antigua and Barbuda</option><option value="Argentina">Argentina</option><option value="Armenia">Armenia</option><option value="Australia">Australia</option><option value="Austria">Austria</option><option value="Azerbaijan">Azerbaijan</option><option value="Bahamas">Bahamas</option><option value="Bahrain">Bahrain</option><option value="Bangladesh">Bangladesh</option><option value="Barbados">Barbados</option><option value="Belarus">Belarus</option><option value="Belgium">Belgium</option><option value="Belize">Belize</option><option value="Benin">Benin</option><option value="Bhutan">Bhutan</option><option value="Bolivia">Bolivia</option><option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option><option value="Botswana">Botswana</option><option value="Brazil">Brazil</option><option value="Brunei">Brunei</option><option value="Bulgaria">Bulgaria</option><option value="Burkina Faso">Burkina Faso</option><option value="Burundi">Burundi</option><option value="Cambodia">Cambodia</option><option value="Cameroon">Cameroon</option><option value="Canada">Canada</option><option value="Cape Verde">Cape Verde</option><option value="Central African Republic">Central African Republic</option><option value="Chad">Chad</option><option value="Chile">Chile</option><option value="China">China</option><option value="Colombia">Colombia</option><option value="Comoros">Comoros</option><option value="Congo (Brazzaville)">Congo (Brazzaville)</option><option value="Congo">Congo</option><option value="Costa Rica">Costa Rica</option><option value="Cote d\'Ivoire">Cote d\'Ivoire</option><option value="Croatia">Croatia</option><option value="Cuba">Cuba</option><option value="Cyprus">Cyprus</option><option value="Czech Republic">Czech Republic</option><option value="Denmark">Denmark</option><option value="Djibouti">Djibouti</option><option value="Dominica">Dominica</option><option value="Dominican Republic">Dominican Republic</option><option value="East Timor (Timor Timur)">East Timor (Timor Timur)</option><option value="Ecuador">Ecuador</option><option value="Egypt">Egypt</option><option value="El Salvador">El Salvador</option><option value="Equatorial Guinea">Equatorial Guinea</option><option value="Eritrea">Eritrea</option><option value="Estonia">Estonia</option><option value="Ethiopia">Ethiopia</option><option value="Fiji">Fiji</option><option value="Finland">Finland</option><option value="France">France</option><option value="Gabon">Gabon</option><option value="Gambia, The">Gambia, The</option><option value="Georgia">Georgia</option><option value="Germany">Germany</option><option value="Ghana">Ghana</option><option value="Greece">Greece</option><option value="Grenada">Grenada</option><option value="Guatemala">Guatemala</option><option value="Guinea">Guinea</option><option value="Guinea-Bissau">Guinea-Bissau</option><option value="Guyana">Guyana</option><option value="Haiti">Haiti</option><option value="Honduras">Honduras</option><option value="Hungary">Hungary</option><option value="Iceland">Iceland</option><option value="India">India</option><option value="Indonesia">Indonesia</option><option value="Iran">Iran</option><option value="Iraq">Iraq</option><option value="Ireland">Ireland</option><option value="Israel">Israel</option><option value="Italy">Italy</option><option value="Jamaica">Jamaica</option><option value="Japan">Japan</option><option value="Jordan">Jordan</option><option value="Kazakhstan">Kazakhstan</option><option value="Kenya">Kenya</option><option value="Kiribati">Kiribati</option><option value="Korea, North">Korea, North</option><option value="Korea, South">Korea, South</option><option value="Kuwait">Kuwait</option><option value="Kyrgyzstan">Kyrgyzstan</option><option value="Laos">Laos</option><option value="Latvia">Latvia</option><option value="Lebanon">Lebanon</option><option value="Lesotho">Lesotho</option><option value="Liberia">Liberia</option><option value="Libya">Libya</option><option value="Liechtenstein">Liechtenstein</option><option value="Lithuania">Lithuania</option><option value="Luxembourg">Luxembourg</option><option value="Macedonia">Macedonia</option><option value="Madagascar">Madagascar</option><option value="Malawi">Malawi</option><option value="Malaysia">Malaysia</option><option value="Maldives">Maldives</option><option value="Mali">Mali</option><option value="Malta">Malta</option><option value="Marshall Islands">Marshall Islands</option><option value="Mauritania">Mauritania</option><option value="Mauritius">Mauritius</option><option value="Mexico">Mexico</option><option value="Micronesia">Micronesia</option><option value="Moldova">Moldova</option><option value="Monaco">Monaco</option><option value="Mongolia">Mongolia</option><option value="Morocco">Morocco</option><option value="Mozambique">Mozambique</option><option value="Myanmar">Myanmar</option><option value="Namibia">Namibia</option><option value="Nauru">Nauru</option><option value="Nepal">Nepal</option><option value="Netherlands">Netherlands</option><option value="New Zealand">New Zealand</option><option value="Nicaragua">Nicaragua</option><option value="Niger">Niger</option><option value="Nigeria">Nigeria</option><option value="Norway">Norway</option><option value="Oman">Oman</option><option value="Pakistan">Pakistan</option><option value="Palau">Palau</option><option value="Panama">Panama</option><option value="Papua New Guinea">Papua New Guinea</option><option value="Paraguay">Paraguay</option><option value="Peru">Peru</option><option value="Philippines">Philippines</option><option value="Poland">Poland</option><option value="Portugal">Portugal</option><option value="Qatar">Qatar</option><option value="Romania">Romania</option><option value="Russia">Russia</option><option value="Rwanda">Rwanda</option><option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option><option value="Saint Lucia">Saint Lucia</option><option value="Saint Vincent">Saint Vincent</option><option value="Samoa">Samoa</option><option value="San Marino">San Marino</option><option value="Sao Tome and Principe">Sao Tome and Principe</option><option value="Saudi Arabia">Saudi Arabia</option><option value="Senegal">Senegal</option><option value="Serbia and Montenegro">Serbia and Montenegro</option><option value="Seychelles">Seychelles</option><option value="Sierra Leone">Sierra Leone</option><option value="Singapore">Singapore</option><option value="Slovakia">Slovakia</option><option value="Slovenia">Slovenia</option><option value="Solomon Islands">Solomon Islands</option><option value="Somalia">Somalia</option><option value="South Africa">South Africa</option><option value="Spain">Spain</option><option value="Sri Lanka">Sri Lanka</option><option value="Sudan">Sudan</option><option value="Suriname">Suriname</option><option value="Swaziland">Swaziland</option><option value="Sweden">Sweden</option><option value="Switzerland">Switzerland</option><option value="Syria">Syria</option><option value="Taiwan">Taiwan</option><option value="Tajikistan">Tajikistan</option><option value="Tanzania">Tanzania</option><option value="Thailand">Thailand</option><option value="Togo">Togo</option><option value="Tonga">Tonga</option><option value="Trinidad and Tobago">Trinidad and Tobago</option><option value="Tunisia">Tunisia</option><option value="Turkey">Turkey</option><option value="Turkmenistan">Turkmenistan</option><option value="Tuvalu">Tuvalu</option><option value="Uganda">Uganda</option><option value="Ukraine">Ukraine</option><option value="United Arab Emirates">United Arab Emirates</option><option value="United Kingdom">United Kingdom</option><option value="United States">United States</option><option value="Uruguay">Uruguay</option><option value="Uzbekistan">Uzbekistan</option><option value="Vanuatu">Vanuatu</option><option value="Vatican City">Vatican City</option><option value="Venezuela">Venezuela</option><option value="Vietnam">Vietnam</option><option value="Yemen">Yemen</option><option value="Zambia">Zambia</option><option value="Zimbabwe">Zimbabwe</option></select><label class="mini_label" style="display: block;" id="'.$id.'_mini_label_country">'.$w_mini_labels[5].'</span>';
					}				
				
					$rep ='<div id="wdform_field'.$id.'" type="type_address" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px; vertical-align:top;"><span id="'.$id.'_element_labelform_id_temp" class="label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_address" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" name="'.$id.'_disable_fieldsform_id_temp" id="'.$id.'_disable_fieldsform_id_temp" street1="'.$w_disabled_fields[0].'" street2="'.$w_disabled_fields[1].'" city="'.$w_disabled_fields[2].'" state="'.$w_disabled_fields[3].'" postal="'.$w_disabled_fields[4].'" country="'.$w_disabled_fields[5].'" us_states="'.$w_disabled_fields[6].'"><div id="'.$id.'_div_address" style="width: '.$param['w_size'].'px;">'.$address_fields.$hidden_inputs.'</div></div></div>';
					break;
            }
            case 'type_submitter_mail': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_size','w_first_val','w_title','w_required','w_unique', 'w_class');
              $temp=$params;
              foreach($params_names as $params_name )
              {	
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }

              if($temp)
              {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' add_'.$attr;
              }

              $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
              $input_active = ($param['w_first_val']==$param['w_title'] ? "input_deactive" : "input_active");	
              $required_sym = ($param['w_required']=="yes" ? " *" : "");	
            
              $rep ='<div id="wdform_field'.$id.'" type="type_submitter_mail" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_submitter_mail" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_unique'].'" name="'.$id.'_uniqueform_id_temp" id="'.$id.'_uniqueform_id_temp"><input type="text" class="'.$input_active.'" id="'.$id.'_elementform_id_temp" name="'.$id.'_elementform_id_temp" value="'.$param['w_first_val'].'" title="'.$param['w_title'].'" onfocus="delete_value(&quot;'.$id.'_elementform_id_temp&quot;)" onblur="return_value(&quot;'.$id.'_elementform_id_temp&quot;)" onchange="change_value(&quot;'.$id.'_elementform_id_temp&quot;)" style="width: '.$param['w_size'].'px;" '.$param['attributes'].' disabled /></div></div>';
              break;
            }
            case 'type_checkbox':
            {
              $params_names=array('w_field_label_size','w_field_label_pos','w_flow','w_choices','w_choices_checked','w_rowcol', 'w_required','w_randomize','w_allow_other','w_allow_other_num','w_class');
              $temp=$params;
			  if(strpos($temp, 'w_field_option_pos') > -1)
						$params_names=array('w_field_label_size','w_field_label_pos','w_field_option_pos','w_flow','w_choices','w_choices_checked','w_rowcol', 'w_required','w_randomize','w_allow_other','w_allow_other_num', 'w_value_disabled','w_choices_value', 'w_choices_params', 'w_class');
              foreach($params_names as $params_name )
              {	
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }

              if($temp)
              {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' add_'.$attr;
              }
               if(!isset($param['w_value_disabled']))
						$param['w_value_disabled'] = 'no';
					
			   if(!isset($param['w_field_option_pos']))
						$param['w_field_option_pos'] = 'left';
						
              $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
              $required_sym = ($param['w_required']=="yes" ? " *" : "");	
              $param['w_choices']	= explode('***',$param['w_choices']);
              $param['w_choices_checked']	= explode('***',$param['w_choices_checked']);
			  
              if(isset($param['w_choices_value']))
				{
					$param['w_choices_value'] = explode('***',$param['w_choices_value']);
					$param['w_choices_params'] = explode('***',$param['w_choices_params']);	
				}
					
              foreach($param['w_choices_checked'] as $key => $choices_checked )
              {
                if($choices_checked=='true')
                  $param['w_choices_checked'][$key]='checked="checked"';
                else
                  $param['w_choices_checked'][$key]='';
              }
              
              $rep='<div id="wdform_field'.$id.'" type="type_checkbox" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_checkbox" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_randomize'].'" name="'.$id.'_randomizeform_id_temp" id="'.$id.'_randomizeform_id_temp"><input type="hidden" value="'.$param['w_allow_other'].'" name="'.$id.'_allow_otherform_id_temp" id="'.$id.'_allow_otherform_id_temp"><input type="hidden" value="'.$param['w_allow_other_num'].'" name="'.$id.'_allow_other_numform_id_temp" id="'.$id.'_allow_other_numform_id_temp"><input type="hidden" value="'.$param['w_rowcol'].'" name="'.$id.'_rowcol_numform_id_temp" id="'.$id.'_rowcol_numform_id_temp"><input type="hidden" value="'.$param['w_field_option_pos'].'" id="'.$id.'_option_left_right"><input type="hidden" value="'.$param['w_value_disabled'].'" name="'.$id.'_value_disabledform_id_temp" id="'.$id.'_value_disabledform_id_temp"><div style="display: table;"><div id="'.$id.'_table_little" style="display: table-row-group;" '.($param['w_flow']=='hor' ? 'for_hor="'.$id.'_hor"' : '').'>';
				
            if($param['w_flow']=='hor')
            {
                $j = 0;
                for($i=0; $i<(int)$param['w_rowcol']; $i++)
                {
                  $rep.='<div id="'.$id.'_element_tr'.$i.'" style="display: table-row;">';
                  
                    for($l=0; $l<=(int)(count($param['w_choices'])/$param['w_rowcol']); $l++)
                    {
                      if($j >= count($param['w_choices'])%$param['w_rowcol'] && $l==(int)(count($param['w_choices'])/$param['w_rowcol']))
                      continue;
                      
                      if($param['w_allow_other']=="yes" && $param['w_allow_other_num']==(int)$param['w_rowcol']*$i+$l)
							         $rep.='<div valign="top" id="'.$id.'_td_little'.((int)$param['w_rowcol']*$l+$i).'" idi="'.((int)$param['w_rowcol']*$l+$i).'" style="display: table-cell;"><input type="checkbox" value="" id="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$l+$i).'" name="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$l+$i).'" other="1" onclick="if(set_checked(&quot;'.$id.'&quot;,&quot;'.((int)$param['w_rowcol']*$l+$i).'&quot;,&quot;form_id_temp&quot;)) show_other_input(&quot;'.$id.'&quot;,&quot;form_id_temp&quot;);" '.$param['w_choices_checked'][(int)$param['w_rowcol']*$l+$i].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled  /><label id="'.$id.'_label_element'.((int)$param['w_rowcol']*$l+$i).'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$l+$i).'">'.$param['w_choices'][(int)$param['w_rowcol']*$l+$i].'</label></div>';
								else	
									{
										$where = '';
										$order_by = '';
										$db_info = '';
										if(isset($param['w_choices_value']))
											$choise_value = $param['w_choices_value'][(int)$param['w_rowcol']*$l+$i];
										else
											$choise_value = $param['w_choices'][(int)$param['w_rowcol']*$l+$i];
									
										if(isset($param['w_choices_params']) && $param['w_choices_params'][(int)$param['w_rowcol']*$l+$i])
										{
											$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][(int)$param['w_rowcol']*$l+$i]);
											$where = "where='".$w_choices_params[0]."'";
											$w_choices_params = explode('[db_info]',$w_choices_params[1]);
											$order_by = "order_by='".$w_choices_params[0]."'";
											$db_info = "db_info='".$w_choices_params[1]."'";
										}
									
										$rep.='<div valign="top" id="'.$id.'_td_little'.((int)$param['w_rowcol']*$l+$i).'" idi="'.((int)$param['w_rowcol']*$l+$i).'" style="display: table-cell;"><input type="checkbox" value="'.$choise_value.'" id="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$l+$i).'" name="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$l+$i).'" onclick="set_checked(&quot;'.$id.'&quot;,&quot;'.((int)$param['w_rowcol']*$l+$i).'&quot;,&quot;form_id_temp&quot;)" '.$param['w_choices_checked'][(int)$param['w_rowcol']*$l+$i].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.((int)$param['w_rowcol']*$l+$i).'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$l+$i).'" '.$where.' '.$order_by.' '.$db_info.'>'.$param['w_choices'][(int)$param['w_rowcol']*$l+$i].'</label></div>';	
									}
                    }
                  
                  $j++;
                  $rep.='</div>';	
                  
                }
          
            }
            else
            {
                for($i=0; $i<(int)(count($param['w_choices'])/$param['w_rowcol']); $i++)
                {
                  $rep.='<div id="'.$id.'_element_tr'.$i.'" style="display: table-row;">';
							
							if(count($param['w_choices']) > (int)$param['w_rowcol'])
								for($l=0; $l<$param['w_rowcol']; $l++)
								{
									if($param['w_allow_other']=="yes" && $param['w_allow_other_num']==(int)$param['w_rowcol']*$i+$l)
										$rep.='<div valign="top" id="'.$id.'_td_little'.((int)$param['w_rowcol']*$i+$l).'" idi="'.((int)$param['w_rowcol']*$i+$l).'" style="display: table-cell;"><input type="checkbox" value="" id="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'" name="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'" other="1" onclick="if(set_checked(&quot;'.$id.'&quot;,&quot;'.((int)$param['w_rowcol']*$i+$l).'&quot;,&quot;form_id_temp&quot;)) show_other_input(&quot;'.$id.'&quot;,&quot;form_id_temp&quot;);" '.$param['w_choices_checked'][(int)$param['w_rowcol']*$i+$l].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled /><label id="'.$id.'_label_element'.((int)$param['w_rowcol']*$i+$l).'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'">'.$param['w_choices'][(int)$param['w_rowcol']*$i+$l].'</label></div>';
										else
									{
										$where = '' ;
										$order_by = '' ;
										$db_info = '' ;
										if(isset($param['w_choices_value']))
											$choise_value =	$param['w_choices_value'][(int)$param['w_rowcol']*$i+$l]; 
										else
											$choise_value = $param['w_choices'][(int)$param['w_rowcol']*$i+$l];

										if(isset($param['w_choices_params']) && $param['w_choices_params'][(int)$param['w_rowcol']*$i+$l])
										{
											$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][(int)$param['w_rowcol']*$i+$l]);
											$where = "where='".$w_choices_params[0]."'";
											$w_choices_params = explode('[db_info]',$w_choices_params[1]);
											$order_by = "order_by='".$w_choices_params[0]."'";
											$db_info = "db_info='".$w_choices_params[1]."'";
										}
									
										$rep.='<div valign="top" id="'.$id.'_td_little'.((int)$param['w_rowcol']*$i+$l).'" idi="'.((int)$param['w_rowcol']*$i+$l).'" style="display: table-cell;"><input type="checkbox" value="'.$choise_value.'" id="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'" name="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'" onclick="set_checked(&quot;'.$id.'&quot;,&quot;'.((int)$param['w_rowcol']*$i+$l).'&quot;,&quot;form_id_temp&quot;)" '.$param['w_choices_checked'][(int)$param['w_rowcol']*$i+$l].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.((int)$param['w_rowcol']*$i+$l).'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'"
										'.$where.' '.$order_by.' '.$db_info.'>'.$param['w_choices'][(int)$param['w_rowcol']*$i+$l].'</label></div>';
									}	
								}
							else
								for($l=0; $l<count($param['w_choices']); $l++)
								{
									if($param['w_allow_other']=="yes" && $param['w_allow_other_num']==(int)$param['w_rowcol']*$i+$l)
											$rep.='<div valign="top" id="'.$id.'_td_little'.((int)$param['w_rowcol']*$i+$l).'" idi="'.((int)$param['w_rowcol']*$i+$l).'" style="display: table-cell;"><input type="checkbox" value="" id="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'" name="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'" other="1" onclick="if(set_checked(&quot;'.$id.'&quot;,&quot;'.((int)$param['w_rowcol']*$i+$l).'&quot;,&quot;form_id_temp&quot;)) show_other_input(&quot;'.$id.'&quot;,&quot;form_id_temp&quot;);" '.$param['w_choices_checked'][(int)$param['w_rowcol']*$i+$l].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.((int)$param['w_rowcol']*$i+$l).'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'">'.$param['w_choices'][(int)$param['w_rowcol']*$i+$l].'</label></div>';
									else
									{
										$where = '' ;
										$order_by = '' ;
										$db_info = '' ;
										if(isset($param['w_choices_value']))
											$choise_value = $param['w_choices_value'][(int)$param['w_rowcol']*$i+$l];
										else
											$choise_value = $param['w_choices'][(int)$param['w_rowcol']*$i+$l];
							
										if(isset($param['w_choices_params']) && $param['w_choices_params'][(int)$param['w_rowcol']*$i+$l])
										{
											$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][(int)$param['w_rowcol']*$i+$l]);
											$where = "where='".$w_choices_params[0]."'";
											$w_choices_params = explode('[db_info]',$w_choices_params[1]);
											$order_by = "order_by='".$w_choices_params[0]."'";
											$db_info = "db_info='".$w_choices_params[1]."'";
										}
									
										$rep.='<div valign="top" id="'.$id.'_td_little'.((int)$param['w_rowcol']*$i+$l).'" idi="'.((int)$param['w_rowcol']*$i+$l).'" style="display: table-cell;"><input type="checkbox" value="'.$choise_value.'" id="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'" name="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'" onclick="set_checked(&quot;'.$id.'&quot;,&quot;'.((int)$param['w_rowcol']*$i+$l).'&quot;,&quot;form_id_temp&quot;)" '.$param['w_choices_checked'][(int)$param['w_rowcol']*$i+$l].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.((int)$param['w_rowcol']*$i+$l).'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'" '.$where.' '.$order_by.' '.$db_info.'>'.$param['w_choices'][(int)$param['w_rowcol']*$i+$l].'</label></div>';
									}
								}
							
							$rep.='</div>';	
                }
                
                if(count($param['w_choices'])%$param['w_rowcol']!=0)
                {
                  $rep.='<div id="'.$id.'_element_tr'.((int)(count($param['w_choices'])/(int)$param['w_rowcol'])).'" style="display: table-row;">';
                  
                  for($k=0; $k<count($param['w_choices'])%$param['w_rowcol']; $k++)
                  {
                    $l = count($param['w_choices']) - count($param['w_choices'])%$param['w_rowcol'] + $k;
                    if($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$l)
										$rep.='<div valign="top" id="'.$id.'_td_little'.$l.'" idi="'.$l.'" style="display: table-cell;"><input type="checkbox" value="" id="'.$id.'_elementform_id_temp'.$l.'" name="'.$id.'_elementform_id_temp'.$l.'" other="1" onclick="if(set_checked(&quot;'.$id.'&quot;,&quot;'.$l.'&quot;,&quot;form_id_temp&quot;)) show_other_input(&quot;'.$id.'&quot;,&quot;form_id_temp&quot;);" '.$param['w_choices_checked'][$l].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.$l.'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.$l.'">'.$param['w_choices'][$l].'</label></div>';
								else
								{
									$where = '' ;
									$order_by = '' ;
									$db_info = '' ;
									if(isset($param['w_choices_value']))
										$choise_value = $param['w_choices_value'][$l];
									else
										$choise_value = $param['w_choices'][$l];
							
									if(isset($param['w_choices_params']) && $param['w_choices_params'][$l])
									{
										$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$l]);
										$where = "where='".$w_choices_params[0]."'";
										$w_choices_params = explode('[db_info]',$w_choices_params[1]);
										$order_by = "order_by='".$w_choices_params[0]."'";
										$db_info = "db_info='".$w_choices_params[1]."'";
									}
									$rep.='<div valign="top" id="'.$id.'_td_little'.$l.'" idi="'.$l.'" style="display: table-cell;"><input type="checkbox" value="'.$choise_value.'" id="'.$id.'_elementform_id_temp'.$l.'" name="'.$id.'_elementform_id_temp'.$l.'" onclick="set_checked(&quot;'.$id.'&quot;,&quot;'.$l.'&quot;,&quot;form_id_temp&quot;)" '.$param['w_choices_checked'][$l].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.$l.'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.$l.'" '.$where.' '.$order_by.' '.$db_info.'>'.$param['w_choices'][$l].'</label></div>';
								}	
                  }
                  
                  $rep.='</div>';	
                }
                
      
              
            }
            $rep.='</div></div></div></div>';
              break;
            }
            case 'type_radio':
            {
            
              $params_names=array('w_field_label_size','w_field_label_pos','w_flow','w_choices','w_choices_checked','w_rowcol', 'w_required','w_randomize','w_allow_other','w_allow_other_num','w_class');
              $temp=$params;
			  if(strpos($temp, 'w_field_option_pos') > -1)
						$params_names=array('w_field_label_size','w_field_label_pos','w_field_option_pos','w_flow','w_choices','w_choices_checked','w_rowcol', 'w_required','w_randomize','w_allow_other','w_allow_other_num', 'w_value_disabled', 'w_choices_value', 'w_choices_params','w_class');
              foreach($params_names as $params_name )
              {	
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }

              if($temp)
              {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' add_'.$attr;
              }
              if(!isset($param['w_value_disabled']))
						$param['w_value_disabled'] = 'no';
					
			  if(!isset($param['w_field_option_pos']))
						$param['w_field_option_pos'] = 'left';
              $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
              $required_sym = ($param['w_required']=="yes" ? " *" : "");	
              $param['w_choices']	= explode('***',$param['w_choices']);
              $param['w_choices_checked']	= explode('***',$param['w_choices_checked']);
			  
              if(isset($param['w_choices_value']))
					{
						$param['w_choices_value'] = explode('***',$param['w_choices_value']);
						$param['w_choices_params'] = explode('***',$param['w_choices_params']);	
					}
              
              foreach($param['w_choices_checked'] as $key => $choices_checked )
              {
                if($choices_checked=='true')
                  $param['w_choices_checked'][$key]='checked="checked"';
                else
                  $param['w_choices_checked'][$key]='';
              }	
              
              $rep='<div id="wdform_field'.$id.'" type="type_radio" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_radio" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_randomize'].'" name="'.$id.'_randomizeform_id_temp" id="'.$id.'_randomizeform_id_temp"><input type="hidden" value="'.$param['w_allow_other'].'" name="'.$id.'_allow_otherform_id_temp" id="'.$id.'_allow_otherform_id_temp"><input type="hidden" value="'.$param['w_allow_other_num'].'" name="'.$id.'_allow_other_numform_id_temp" id="'.$id.'_allow_other_numform_id_temp"><input type="hidden" value="'.$param['w_rowcol'].'" name="'.$id.'_rowcol_numform_id_temp" id="'.$id.'_rowcol_numform_id_temp"><input type="hidden" value="'.$param['w_field_option_pos'].'" id="'.$id.'_option_left_right"><input type="hidden" value="'.$param['w_value_disabled'].'" name="'.$id.'_value_disabledform_id_temp" id="'.$id.'_value_disabledform_id_temp"><div style="display: table;"><div id="'.$id.'_table_little" style="display: table-row-group;" '.($param['w_flow']=='hor' ? 'for_hor="'.$id.'_hor"' : '').'>';
            
            
              if($param['w_flow']=='hor')
              {
                $j = 0;
                for($i=0; $i<(int)$param['w_rowcol']; $i++)
                {
                  $rep.='<div id="'.$id.'_element_tr'.$i.'" style="display: table-row;">';
                  
                    for($l=0; $l<=(int)(count($param['w_choices'])/$param['w_rowcol']); $l++)
                    {
                      if($j >= count($param['w_choices'])%$param['w_rowcol'] && $l==(int)(count($param['w_choices'])/$param['w_rowcol']))
                      continue;
                      
                      if($param['w_allow_other']=="yes" && $param['w_allow_other_num']==(int)$param['w_rowcol']*$l+$i)
											$rep.='<div valign="top" id="'.$id.'_td_little'.((int)$param['w_rowcol']*$l+$i).'" idi="'.((int)$param['w_rowcol']*$l+$i).'" style="display: table-cell;"><input type="radio" value="'.$param['w_choices'][(int)$param['w_rowcol']*$l+$i].'" id="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$l+$i).'" name="'.$id.'_elementform_id_temp" other="1" onclick="set_default(&quot;'.$id.'&quot;,&quot;'.((int)$param['w_rowcol']*$l+$i).'&quot;,&quot;form_id_temp&quot;); show_other_input(&quot;'.$id.'&quot;,&quot;form_id_temp&quot;);" '.$param['w_choices_checked'][(int)$param['w_rowcol']*$l+$i].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.((int)$param['w_rowcol']*$l+$i).'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$l+$i).'">'.$param['w_choices'][(int)$param['w_rowcol']*$l+$i].'</label></div>';
										else	
										{
											$where = '' ;
											$order_by = '' ;
											$db_info = '' ;
											if(isset($param['w_choices_value']))
												$choise_value = $param['w_choices_value'][(int)$param['w_rowcol']*$l+$i];	
											else
												$choise_value = $param['w_choices'][(int)$param['w_rowcol']*$l+$i];
												
											if(isset($param['w_choices_params']) && $param['w_choices_params'][(int)$param['w_rowcol']*$l+$i])
											{
												$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][(int)$param['w_rowcol']*$l+$i]);
												$where = "where='".$w_choices_params[0]."'";
												$w_choices_params = explode('[db_info]',$w_choices_params[1]);
												$order_by = "order_by='".$w_choices_params[0]."'";
												$db_info = "db_info='".$w_choices_params[1]."'";
											}
											
											$rep.='<div valign="top" id="'.$id.'_td_little'.((int)$param['w_rowcol']*$l+$i).'" idi="'.((int)$param['w_rowcol']*$l+$i).'" style="display: table-cell;"><input type="radio" value="'.$choise_value.'" id="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$l+$i).'" name="'.$id.'_elementform_id_temp" onclick="set_default(&quot;'.$id.'&quot;,&quot;'.((int)$param['w_rowcol']*$l+$i).'&quot;,&quot;form_id_temp&quot;)" '.$param['w_choices_checked'][(int)$param['w_rowcol']*$l+$i].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.((int)$param['w_rowcol']*$l+$i).'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$l+$i).'" '.$where.' '.$order_by.' '.$db_info.'>'.$param['w_choices'][(int)$param['w_rowcol']*$l+$i].'</label></div>';
										}		
                    }
                    
                  $j++;
                  $rep.='</div>';	
                  
                }
        
              }
              else
              {
                for($i=0; $i<(int)(count($param['w_choices'])/$param['w_rowcol']); $i++)
                {
                  $rep.='<div id="'.$id.'_element_tr'.$i.'" style="display: table-row;">';
							
							if(count($param['w_choices']) > (int)$param['w_rowcol'])
								for($l=0; $l<$param['w_rowcol']; $l++)
								{
									if($param['w_allow_other']=="yes" && $param['w_allow_other_num']==(int)$param['w_rowcol']*$i+$l)
										$rep.='<div valign="top" id="'.$id.'_td_little'.((int)$param['w_rowcol']*$i+$l).'" idi="'.((int)$param['w_rowcol']*$i+$l).'" style="display: table-cell;"><input type="radio" value="'.$param['w_choices'][(int)$param['w_rowcol']*$i+$l].'" id="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'" name="'.$id.'_elementform_id_temp" other="1" onclick="set_default(&quot;'.$id.'&quot;,&quot;'.((int)$param['w_rowcol']*$i+$l).'&quot;,&quot;form_id_temp&quot;); show_other_input(&quot;'.$id.'&quot;,&quot;form_id_temp&quot;);" '.$param['w_choices_checked'][(int)$param['w_rowcol']*$i+$l].' '.$param['attributes'].'  '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.((int)$param['w_rowcol']*$i+$l).'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'">'.$param['w_choices'][(int)$param['w_rowcol']*$i+$l].'</label></div>';
									else
									{
										$where = '' ;
										$order_by = '' ;
										$db_info = '' ;
										if(isset($param['w_choices_value']))
											$choise_value = $param['w_choices_value'][(int)$param['w_rowcol']*$i+$l];	
										else
											$choise_value = $param['w_choices'][(int)$param['w_rowcol']*$i+$l];

										if(isset($param['w_choices_params']) && $param['w_choices_params'][(int)$param['w_rowcol']*$i+$l])
										{
											$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][(int)$param['w_rowcol']*$i+$l]);
											$where = "where='".$w_choices_params[0]."'";
											$w_choices_params = explode('[db_info]',$w_choices_params[1]);
											$order_by = "order_by='".$w_choices_params[0]."'";
											$db_info = "db_info='".$w_choices_params[1]."'";
										}		
									
										$rep.='<div valign="top" id="'.$id.'_td_little'.((int)$param['w_rowcol']*$i+$l).'" idi="'.((int)$param['w_rowcol']*$i+$l).'" style="display: table-cell;"><input type="radio" value="'.$choise_value.'" id="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'" name="'.$id.'_elementform_id_temp" onclick="set_default(&quot;'.$id.'&quot;,&quot;'.((int)$param['w_rowcol']*$i+$l).'&quot;,&quot;form_id_temp&quot;)" '.$param['w_choices_checked'][(int)$param['w_rowcol']*$i+$l].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.((int)$param['w_rowcol']*$i+$l).'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'" '.$where.' '.$order_by.' '.$db_info.'>'.$param['w_choices'][(int)$param['w_rowcol']*$i+$l].'</label></div>';
									}	
								}
							else
								for($l=0; $l<count($param['w_choices']); $l++)
								{
									if($param['w_allow_other']=="yes" && $param['w_allow_other_num']==(int)$param['w_rowcol']*$i+$l)
										$rep.='<div valign="top" id="'.$id.'_td_little'.((int)$param['w_rowcol']*$i+$l).'" idi="'.((int)$param['w_rowcol']*$i+$l).'" style="display: table-cell;"><input type="radio" value="'.$param['w_choices'][(int)$param['w_rowcol']*$i+$l].'" id="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'" name="'.$id.'_elementform_id_temp" other="1" onclick="set_default(&quot;'.$id.'&quot;,&quot;'.((int)$param['w_rowcol']*$i+$l).'&quot;,&quot;form_id_temp&quot;); show_other_input(&quot;'.$id.'&quot;,&quot;form_id_temp&quot;);" '.$param['w_choices_checked'][(int)$param['w_rowcol']*$i+$l].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").'  disabled/><label id="'.$id.'_label_element'.((int)$param['w_rowcol']*$i+$l).'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'">'.$param['w_choices'][(int)$param['w_rowcol']*$i+$l].'</label></div>';
									else
									{
										$where = '' ;
										$order_by = '' ;
										$db_info = '' ;
										if(isset($param['w_choices_value']))
											$choise_value = $param['w_choices_value'][(int)$param['w_rowcol']*$i+$l];	
										else
											$choise_value = $param['w_choices'][(int)$param['w_rowcol']*$i+$l];

										if(isset($param['w_choices_params']) && $param['w_choices_params'][(int)$param['w_rowcol']*$i+$l])
										{
											$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][(int)$param['w_rowcol']*$i+$l]);
											$where = "where='".$w_choices_params[0]."'";
											$w_choices_params = explode('[db_info]',$w_choices_params[1]);
											$order_by = "order_by='".$w_choices_params[0]."'";
											$db_info = "db_info='".$w_choices_params[1]."'";
										}		
									
										$rep.='<div valign="top" id="'.$id.'_td_little'.((int)$param['w_rowcol']*$i+$l).'" idi="'.((int)$param['w_rowcol']*$i+$l).'" style="display: table-cell;"><input type="radio" value="'.$choise_value.'" id="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'" name="'.$id.'_elementform_id_temp" onclick="set_default(&quot;'.$id.'&quot;,&quot;'.((int)$param['w_rowcol']*$i+$l).'&quot;,&quot;form_id_temp&quot;)" '.$param['w_choices_checked'][(int)$param['w_rowcol']*$i+$l].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.((int)$param['w_rowcol']*$i+$l).'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.((int)$param['w_rowcol']*$i+$l).'" '.$where.' '.$order_by.' '.$db_info.'>'.$param['w_choices'][(int)$param['w_rowcol']*$i+$l].'</label></div>';
									}	
								}
							
							$rep.='</div>';	
                }
                
                if(count($param['w_choices'])%$param['w_rowcol']!=0)
                {
                  $rep.='<div id="'.$id.'_element_tr'.((int)(count($param['w_choices'])/(int)$param['w_rowcol'])).'" style="display: table-row;">';
							
							for($k=0; $k<count($param['w_choices'])%$param['w_rowcol']; $k++)
							{
								$l = count($param['w_choices']) - count($param['w_choices'])%$param['w_rowcol'] + $k;
								if($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$l)
									$rep.='<div valign="top" id="'.$id.'_td_little'.$l.'" idi="'.$l.'" style="display: table-cell;"><input type="radio" value="'.$param['w_choices'][$l].'" id="'.$id.'_elementform_id_temp'.$l.'" name="'.$id.'_elementform_id_temp" other="1" onclick="set_default(&quot;'.$id.'&quot;,&quot;'.$l.'&quot;,&quot;form_id_temp&quot;); show_other_input(&quot;'.$id.'&quot;,&quot;form_id_temp&quot;);" '.$param['w_choices_checked'][$l].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.$l.'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.$l.'">'.$param['w_choices'][$l].'</label></div>';
								else
								{
									$where = '' ;
									$order_by = '' ;
									$db_info = '' ;
									if(isset($param['w_choices_value']))
										$choise_value = $param['w_choices_value'][$l];
									else
										$choise_value = $param['w_choices'][$l];
									
									if(isset($param['w_choices_params']) && $param['w_choices_params'][$l])
									{
										$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$l]);
										$where = "where='".$w_choices_params[0]."'";
										$w_choices_params = explode('[db_info]',$w_choices_params[1]);
										$order_by = "order_by='".$w_choices_params[0]."'";
										$db_info = "db_info='".$w_choices_params[1]."'";
									}
								
									$rep.='<div valign="top" id="'.$id.'_td_little'.$l.'" idi="'.$l.'" style="display: table-cell;"><input type="radio" value="'.$choise_value.'" id="'.$id.'_elementform_id_temp'.$l.'" name="'.$id.'_elementform_id_temp" onclick="set_default(&quot;'.$id.'&quot;,&quot;'.$l.'&quot;,&quot;form_id_temp&quot;)" '.$param['w_choices_checked'][$l].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.$l.'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.$l.'"
									'.$where.' '.$order_by.' '.$db_info.'>'.$param['w_choices'][$l].'</label></div>';
								}	
							}
							
							$rep.='</div>';	
                }
                
              }
                  
              
        
            $rep.='</div></div></div></div>';
            
              break;
            }
            case 'type_own_select':
            {
              $params_names=array('w_field_label_size','w_field_label_pos','w_size','w_choices','w_choices_checked', 'w_choices_disabled','w_required','w_class');
              $temp=$params;
			  if(strpos($temp, 'w_choices_value') > -1)
						$params_names=array('w_field_label_size','w_field_label_pos','w_size','w_choices','w_choices_checked', 'w_choices_disabled', 'w_required', 'w_value_disabled', 'w_choices_value', 'w_choices_params', 'w_class');
              foreach($params_names as $params_name )
              {	
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }

              if($temp)
              {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' add_'.$attr;
              }

              $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
              $required_sym = ($param['w_required']=="yes" ? " *" : "");	
              $param['w_choices']	= explode('***',$param['w_choices']);
              $param['w_choices_checked']	= explode('***',$param['w_choices_checked']);
              $param['w_choices_disabled']	= explode('***',$param['w_choices_disabled']);
			  
              if(isset($param['w_choices_value']))
					{
						$param['w_choices_value'] = explode('***',$param['w_choices_value']);
						$param['w_choices_params'] = explode('***',$param['w_choices_params']);	
					}
					
			   if(!isset($param['w_value_disabled']))
						$param['w_value_disabled'] = 'no';
						
              foreach($param['w_choices_checked'] as $key => $choices_checked )
              {
                if($choices_checked=='true')
                  $param['w_choices_checked'][$key]='selected="selected"';
                else
                  $param['w_choices_checked'][$key]='';
              }
              
              $rep='<div id="wdform_field'.$id.'" type="type_own_select" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; "><input type="hidden" value="type_own_select" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_value_disabled'].'" name="'.$id.'_value_disabledform_id_temp" id="'.$id.'_value_disabledform_id_temp"><select id="'.$id.'_elementform_id_temp" name="'.$id.'_elementform_id_temp" onchange="set_select(this)" style="width: '.$param['w_size'].'px;"  '.$param['attributes'].' disabled>';
              foreach($param['w_choices'] as $key => $choice)
              {
                        $where = '';
						$order_by = '';
						$db_info = '';
						$choice_value = $param['w_choices_disabled'][$key]=='true' ? '' : (isset($param['w_choices_value']) ? $param['w_choices_value'][$key] : $choice);
						if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])
						{
							$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
							$where = "where='".$w_choices_params[0]."'";
							$w_choices_params = explode('[db_info]',$w_choices_params[1]);
							$order_by = "order_by='".$w_choices_params[0]."'";
							$db_info = "db_info='".$w_choices_params[1]."'";
						}
					
						$rep.='<option id="'.$id.'_option'.$key.'" value="'.$choice_value.'" onselect="set_select(&quot;'.$id.'_option'.$key.'&quot;)" '.$param['w_choices_checked'][$key].' '.$where.' '.$order_by.' '.$db_info.'>'.$choice.'</option>';
              }
              $rep.='</select></div></div>';
              break;
            }
            
            case 'type_country':
            {
              $params_names=array('w_field_label_size','w_field_label_pos','w_size','w_countries','w_required','w_class');
              $temp=$params;
              foreach($params_names as $params_name )
              {	
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }

              if($temp)
              {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' add_'.$attr;
              }

              $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
              $required_sym = ($param['w_required']=="yes" ? " *" : "");	
              $param['w_countries']	= explode('***',$param['w_countries']);
            
              $rep='<div id="wdform_field'.$id.'" type="type_country" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; "><input type="hidden" value="type_country" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><select id="'.$id.'_elementform_id_temp" name="'.$id.'_elementform_id_temp" style="width: '.$param['w_size'].'px;"  '.$param['attributes'].' disabled>';
              foreach($param['w_countries'] as $key => $choice)
              {
                $choice_value=$choice;
                $rep.='<option value="'.$choice_value.'">'.$choice.'</option>';
              }
              $rep.='</select></div></div>';
              break;
            }
            
            case 'type_time':
            {
              $params_names=array('w_field_label_size','w_field_label_pos','w_time_type','w_am_pm','w_sec','w_hh','w_mm','w_ss','w_mini_labels','w_required','w_class');
              $temp=$params;
              foreach($params_names as $params_name )
              {	
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }

              if($temp)
              {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' add_'.$attr;
              }

              $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
              $required_sym = ($param['w_required']=="yes" ? " *" : "");	
            
              $w_mini_labels = explode('***',$param['w_mini_labels']);
              
            
              if($param['w_sec']=='1')
              {
                $w_sec = '<div align="center" style="display: table-cell;"><span class="wdform_colon" style="vertical-align: middle;">&nbsp;:&nbsp;</span></div><div id="'.$id.'_td_time_input3" style="width: 32px; display: table-cell;"><input type="text" value="'.$param['w_ss'].'" class="time_box" id="'.$id.'_ssform_id_temp" name="'.$id.'_ssform_id_temp" onkeypress="return check_second(event, &quot;'.$id.'_ssform_id_temp&quot;)" onkeyup="change_second(&quot;'.$id.'_ssform_id_temp&quot;)" onblur="add_0(&quot;'.$id.'_ssform_id_temp&quot;)" '.$param['attributes'].' disabled /></div>';
                $w_sec_label='<div style="display: table-cell;"></div><div id="'.$id.'_td_time_label3" style="display: table-cell;"><label class="mini_label" id="'.$id.'_mini_label_ss">'.$w_mini_labels[2].'</label></div>';
              }
              else
              {
                $w_sec = '';
                $w_sec_label='';
              }
              
              if($param['w_time_type']=='12')
              {
                if($param['w_am_pm']=='am')
                {
                  $am_ = "selected=\"selected\"";
                  $pm_ = "";
                }	
                else
                {
                  $am_ = "";
                  $pm_ = "selected=\"selected\"";
                  
                }	
              
              $w_time_type = '<div id="'.$id.'_am_pm_select" class="td_am_pm_select" style="display: table-cell;"><select class="am_pm_select" name="'.$id.'_am_pmform_id_temp" id="'.$id.'_am_pmform_id_temp" onchange="set_sel_am_pm(this)" '.$param['attributes'].'><option value="am" '.$am_.'>AM</option><option value="pm" '.$pm_.'>PM</option></select></div>';
              
              $w_time_type_label = '<div id="'.$id.'_am_pm_label" class="td_am_pm_select" style="display: table-cell;"><label class="mini_label" id="'.$id.'_mini_label_am_pm">'.$w_mini_labels[3].'</label></div>';
              
              }
              else
              {
              $w_time_type='';
              $w_time_type_label = '';
              }

              
              $rep ='<div id="wdform_field'.$id.'" type="type_time" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_time" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><div id="'.$id.'_table_time" style="display: table;"><div id="'.$id.'_tr_time1" style="display: table-row;"><div id="'.$id.'_td_time_input1" style="width: 32px; display: table-cell;"><input type="text" value="'.$param['w_hh'].'" class="time_box" id="'.$id.'_hhform_id_temp" name="'.$id.'_hhform_id_temp" onkeypress="return check_hour(event, &quot;'.$id.'_hhform_id_temp&quot;, &quot;23&quot;)" onkeyup="change_hour(event, &quot;'.$id.'_hhform_id_temp&quot;,&quot;23&quot;)" onblur="add_0(&quot;'.$id.'_hhform_id_temp&quot;)" '.$param['attributes'].' disabled/></div><div align="center" style="display: table-cell;"><span class="wdform_colon" style="vertical-align: middle;">&nbsp;:&nbsp;</span></div><div id="'.$id.'_td_time_input2" style="width: 32px; display: table-cell;"><input type="text" value="'.$param['w_mm'].'" class="time_box" id="'.$id.'_mmform_id_temp" name="'.$id.'_mmform_id_temp" onkeypress="return check_minute(event, &quot;'.$id.'_mmform_id_temp&quot;)" onkeyup="change_minute(event, &quot;'.$id.'_mmform_id_temp&quot;)" onblur="add_0(&quot;'.$id.'_mmform_id_temp&quot;)" '.$param['attributes'].' disabled/></div>'.$w_sec.$w_time_type.'</div><div id="'.$id.'_tr_time2" style="display: table-row;"><div id="'.$id.'_td_time_label1" style="display: table-cell;"><label class="mini_label" id="'.$id.'_mini_label_hh">'.$w_mini_labels[0].'</label></div><div style="display: table-cell;"></div><div id="'.$id.'_td_time_label2" style="display: table-cell;"><label class="mini_label" id="'.$id.'_mini_label_mm">'.$w_mini_labels[1].'</label></div>'.$w_sec_label.$w_time_type_label.'</div></div></div></div>';
              
              break;
            }
            case 'type_date':
            {
              $params_names=array('w_field_label_size','w_field_label_pos','w_date','w_required','w_class','w_format','w_but_val');
              $temp = $params;
			  if(strpos($temp, 'w_disable_past_days') > -1)
				$params_names = array('w_field_label_size','w_field_label_pos','w_date','w_required','w_class','w_format','w_but_val', 'w_disable_past_days');
              foreach($params_names as $params_name )
              {	
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }

              if($temp)
              {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' add_'.$attr;
              }

              $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
              $required_sym = ($param['w_required']=="yes" ? " *" : "");	
              $param['w_disable_past_days'] = isset($param['w_disable_past_days']) ? $param['w_disable_past_days'] : 'no';
			  $disable_past_days = $param['w_disable_past_days'] == 'yes' ? 'true' : 'false';

              $rep ='<div id="wdform_field'.$id.'" type="type_date" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_date" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_disable_past_days'].'" name="'.$id.'_dis_past_daysform_id_temp" id="'.$id.'_dis_past_daysform_id_temp"><input type="text" value="'.$param['w_date'].'" class="wdform-date" id="'.$id.'_elementform_id_temp" name="'.$id.'_elementform_id_temp" maxlength="10" size="10" onchange="change_value(&quot;'.$id.'_elementform_id_temp&quot;)" '.$param['attributes'].' disabled/><input id="'.$id.'_buttonform_id_temp" class="button" type="reset" value="'.$param['w_but_val'].'" format="'.$param['w_format'].'" src="templates/bluestork/images/system/calendar.png?ver='. get_option("wd_form_maker_version").'" alt="calendario" '.$param['attributes'].' onclick="return showCalendar(&quot;'.$id.'_elementform_id_temp&quot; , &quot;'.$param['w_format'].'&quot;, '.$disable_past_days.')"></div></div>';
              
              break;
            }
            case 'type_date_fields':
            {
              $params_names=array('w_field_label_size','w_field_label_pos','w_day','w_month','w_year','w_day_type','w_month_type','w_year_type','w_day_label','w_month_label','w_year_label','w_day_size','w_month_size','w_year_size','w_required','w_class','w_from','w_to','w_divider');
              
              $temp=$params;
              foreach($params_names as $params_name )
              {	
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }

              if($temp)
              {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' add_'.$attr;
              }

              $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
              $required_sym = ($param['w_required']=="yes" ? " *" : "");	
            
      
              if($param['w_day_type']=="SELECT")
              {

              
                $w_day_type = '<select id="'.$id.'_dayform_id_temp" name="'.$id.'_dayform_id_temp" onchange="set_select(this)" style="width: '.$param['w_day_size'].'px;" '.$param['attributes'].' disabled><option value=""></option>';
                for($k=0; $k<=31; $k++)
                {
                  if($k<10)
                  {
                    if($param['w_day']=='0'.$k)
                    $selected = "selected=\"selected\"";
                    else
                    $selected = "";
                    
                    $w_day_type .= '<option value="0'.$k.'" '.$selected.'>0'.$k.'</option>';
                  }
                  else
                  {
                  if($param['w_day']==''.$k)
                    $selected = "selected=\"selected\"";
                    else
                    $selected = "";
                    
                    $w_day_type .= '<option value="'.$k.'" '.$selected.'>'.$k.'</option>';
                  }
                  
                  
                  
                }
                $w_day_type .= '</select>';
              }
              else
					{
						$w_day_type = '<input type="text" value="'.$param['w_day'].'" id="'.$id.'_dayform_id_temp" name="'.$id.'_dayform_id_temp" onchange="change_value(&quot;'.$id.'_dayform_id_temp&quot;)" onkeypress="return check_day(event, &quot;'.$id.'_dayform_id_temp&quot;)" onblur="if (this.value==&quot;0&quot;) this.value=&quot;&quot;; else add_0(&quot;'.$id.'_dayform_id_temp&quot;)" style="width: '.$param['w_day_size'].'px;" '.$param['attributes'].' disabled/>';
					}

					if($param['w_month_type']=="SELECT")
					{
					
						$w_month_type = '<select id="'.$id.'_monthform_id_temp" name="'.$id.'_monthform_id_temp" onchange="set_select(this)" style="width: '.$param['w_month_size'].'px;" '.$param['attributes'].' disabled><option value=""></option><option value="01" '.($param['w_month']=="01" ? "selected=\"selected\"": "").'  ><!--repstart-->January<!--repend--></option><option value="02" '.($param['w_month']=="02" ? "selected=\"selected\"": "").'><!--repstart-->February<!--repend--></option><option value="03" '.($param['w_month']=="03"? "selected=\"selected\"": "").'><!--repstart-->March<!--repend--></option><option value="04" '.($param['w_month']=="04" ? "selected=\"selected\"": "").' ><!--repstart-->April<!--repend--></option><option value="05" '.($param['w_month']=="05" ? "selected=\"selected\"": "").' ><!--repstart-->May<!--repend--></option><option value="06" '.($param['w_month']=="06" ? "selected=\"selected\"": "").' ><!--repstart-->June<!--repend--></option><option value="07" '.($param['w_month']=="07" ? "selected=\"selected\"": "").' ><!--repstart-->July<!--repend--></option><option value="08" '.($param['w_month']=="08" ? "selected=\"selected\"": "").' ><!--repstart-->August<!--repend--></option><option value="09" '.($param['w_month']=="09" ? "selected=\"selected\"": "").' ><!--repstart-->September<!--repend--></option><option value="10" '.($param['w_month']=="10" ? "selected=\"selected\"": "").' ><!--repstart-->October<!--repend--></option><option value="11" '.($param['w_month']=="11" ? "selected=\"selected\"": "").'><!--repstart-->November<!--repend--></option><option value="12" '.($param['w_month']=="12" ? "selected=\"selected\"": "").' ><!--repstart-->December<!--repend--></option></select>';
					
					}
					else
					{
					$w_month_type = '<input type="text" value="'.$param['w_month'].'" id="'.$id.'_monthform_id_temp" name="'.$id.'_monthform_id_temp" onkeypress="return check_month(event, &quot;'.$id.'_monthform_id_temp&quot;)" onchange="change_value(&quot;'.$id.'_monthform_id_temp&quot;)" onblur="if (this.value==&quot;0&quot;) this.value=&quot;&quot;; else add_0(&quot;'.$id.'_monthform_id_temp&quot;)" style="width: '.$param['w_day_size'].'px;" '.$param['attributes'].' disabled/>';
					
					}

					if($param['w_year_type']=="SELECT")
					{
						$w_year_type = '<select id="'.$id.'_yearform_id_temp" name="'.$id.'_yearform_id_temp" onchange="set_select(this)" from="'.$param['w_from'].'" to="'.$param['w_to'].'" style="width: '.$param['w_year_size'].'px;" '.$param['attributes'].' disabled><option value=""></option>';
                for($k=$param['w_to']; $k>=$param['w_from']; $k--)
                {
                  if($param['w_year']==$k)
                  $selected = "selected=\"selected\"";
                  else
                  $selected = "";
                  
                  $w_year_type .= '<option value="'.$k.'" '.$selected.'>'.$k.'</option>';
                }
                $w_year_type .= '</select>';
              
              }
              else
              {
                $w_year_type = '<input type="text" value="'.$param['w_year'].'" id="'.$id.'_yearform_id_temp" name="'.$id.'_yearform_id_temp" onchange="change_year(&quot;'.$id.'_yearform_id_temp&quot;)" onkeypress="return check_year1(event, &quot;'.$id.'_yearform_id_temp&quot;)" onblur="check_year2(&quot;'.$id.'_yearform_id_temp&quot;)" from="'.$param['w_from'].'" to="'.$param['w_to'].'" style="width: '.$param['w_day_size'].'px;" '.$param['attributes'].' disabled/>';
              }

              
              $rep ='<div id="wdform_field'.$id.'" type="type_date_fields" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_date_fields" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><div id="'.$id.'_table_date" style="display: table;"><div id="'.$id.'_tr_date1" style="display: table-row;"><div id="'.$id.'_td_date_input1" style="display: table-cell;">
              '.$w_day_type.'
              
              </div><div id="'.$id.'_td_date_separator1" style="display: table-cell;"><span id="'.$id.'_separator1" class="wdform_separator">'.$param['w_divider'].'</span></div><div id="'.$id.'_td_date_input2" style="display: table-cell;">'.$w_month_type.'</div><div id="'.$id.'_td_date_separator2" style="display: table-cell;"><span id="'.$id.'_separator2" class="wdform_separator">'.$param['w_divider'].'</span></div><div id="'.$id.'_td_date_input3" style="display: table-cell;">'.$w_year_type.'</div></div><div id="'.$id.'_tr_date2" style="display: table-row;"><div id="'.$id.'_td_date_label1" style="display: table-cell;"><label class="mini_label" id="'.$id.'_day_label">'.$param['w_day_label'].'</label></div><div style="display: table-cell;"></div><div id="'.$id.'_td_date_label2" style="display: table-cell;"><label class="mini_label" id="'.$id.'_month_label">'.$param['w_month_label'].'</label></div><div style="display: table-cell;"></div><div id="'.$id.'_td_date_label3" style="display: table-cell;"><label class="mini_label" id="'.$id.'_year_label">'.$param['w_year_label'].'</label></div></div></div></div></div>';
              
              break;
            }
            case 'type_file_upload':
            {
              $params_names=array('w_field_label_size','w_field_label_pos','w_destination','w_extension','w_max_size','w_required','w_multiple','w_class');
              $temp=$params;
              foreach($params_names as $params_name )
              {	
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                if (isset($temp[1])) {
                  $temp = $temp[1];
                }
                else {
                  $temp = '';
                }
              }

              if($temp)
              {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' add_'.$attr;
              }

              $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
              $required_sym = ($param['w_required']=="yes" ? " *" : "");	
              $multiple = ($param['w_multiple']=="yes" ? "multiple='multiple'" : "");	

              $rep ='<div id="wdform_field'.$id.'" type="type_file_upload" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_file_upload" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="***max_sizeskizb'.$id.'***'.$param['w_max_size'].'***max_sizeverj'.$id.'***" id="'.$id.'_max_size" name="'.$id.'_max_size"><input type="hidden" value="***destinationskizb'.$id.'***'.$param['w_destination'].'***destinationverj'.$id.'***" id="'.$id.'_destination" name="'.$id.'_destination"><input type="hidden" value="***extensionskizb'.$id.'***'.$param['w_extension'].'***extensionverj'.$id.'***" id="'.$id.'_extension" name="'.$id.'_extension"><input type="file" class="file_upload" id="'.$id.'_elementform_id_temp" name="'.$id.'_fileform_id_temp"  '.$multiple.' '.$param['attributes'].' disabled/></div></div>';
              
              break;
            }
            case 'type_captcha':
            {
              $params_names=array('w_field_label_size','w_field_label_pos','w_digit','w_class');
              $temp=$params;
              foreach($params_names as $params_name )
              {	
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }

              if($temp)
              {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' add_'.$attr;
              }

              $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
              
              $rep ='<div id="wdform_field'.$id.'" type="type_captcha" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display:'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="label" style="vertical-align: top;">'.$label.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_captcha" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><div style="display: table;"><div style="display: table-row;"><div valign="middle" style="display: table-cell;"><img type="captcha" digit="'.$param['w_digit'].'" src="' . add_query_arg(array('action' => 'formcontactwdcaptcha', 'digit' => $param['w_digit'], 'i' => 'form_id_temp'), admin_url('admin-ajax.php')) . '" id="_wd_captchaform_id_temp" class="captcha_img" onclick="captcha_refresh(&quot;_wd_captcha&quot;,&quot;form_id_temp&quot;)" '.$param['attributes'].'></div><div valign="middle" style="display: table-cell;"><div class="captcha_refresh" id="_element_refreshform_id_temp" onclick="captcha_refresh(&quot;_wd_captcha&quot;,&quot;form_id_temp&quot;)" '.$param['attributes'].'></div></div></div><div style="display: table-row;"><div style="display: table-cell;"><input type="text" class="captcha_input" id="_wd_captcha_inputform_id_temp" name="captcha_input" style="width: '.($param['w_digit']*10+15).'px;" '.$param['attributes'].' disabled/></div></div></div></div></div>';
              
              break;
            }
			case 'type_arithmetic_captcha':
            {
              $params_names=array('w_field_label_size','w_field_label_pos', 'w_count', 'w_operations','w_class', 'w_input_size');
              $temp=$params;
              foreach($params_names as $params_name )
              {	
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }

              if($temp)
              {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' add_'.$attr;
              }
			 
              $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
			  $param['w_count'] = $param['w_count'] ? $param['w_count'] : 1;
              $param['w_operations'] = $param['w_operations'] ? $param['w_operations'] : '+, -, *, /';
              $param['w_input_size'] = $param['w_input_size'] ? $param['w_input_size'] : 60;
              
              $rep ='<div id="wdform_field'.$id.'" type="type_arithmetic_captcha" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display:'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="label" style="vertical-align: top;">'.$label.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_captcha" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><div style="display: table;"><div style="display: table-row;"><div style="display: table-cell;"><img type="captcha" operations_count="'.$param['w_count'].'" operations="'.$param['w_operations'].'" input_size="'.$param['w_input_size'].'" src="' . add_query_arg(array('action' => 'formcontactwdmathcaptcha', 'operations_count' => $param['w_count'], 'operations' => $param['w_operations'], 'i' => 'form_id_temp'), admin_url('admin-ajax.php')) . '" id="_wd_arithmetic_captchaform_id_temp" class="arithmetic_captcha_img" onclick="captcha_refresh(&quot;_wd_arithmetic_captcha&quot;,&quot;form_id_temp&quot;)" '.$param['attributes'].'></div><div style="display: table-cell;"><input type="text" class="arithmetic_captcha_input" id="_wd_arithmetic_captcha_inputform_id_temp" name="arithmetic_captcha_input" onkeypress="return check_isnum(event)" style="width: '.$param['w_input_size'].'px;" '.$param['attributes'].' disabled/></div><div style="display: table-cell; vertical-align: middle;"><div class="captcha_refresh" id="_element_refreshform_id_temp" onclick="captcha_refresh(&quot;_wd_arithmetic_captcha&quot;,&quot;form_id_temp&quot;)" '.$param['attributes'].'></div></div></div></div></div></div>';
              
              break;
            }


            case 'type_recaptcha':
            {
              $params_names=array('w_field_label_size','w_field_label_pos','w_public','w_private','w_theme','w_class');
              $temp=$params;
              foreach($params_names as $params_name )
              {	
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }

              if($temp)
              {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' add_'.$attr;
              }

              $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
              
              $rep ='<div id="wdform_field'.$id.'" type="type_recaptcha" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="label" style="vertical-align: top;">'.$label.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_recaptcha" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><div id="wd_recaptchaform_id_temp" public_key="'.$param['w_public'].'" private_key="'.$param['w_private'].'" theme="'.$param['w_theme'].'" '.$param['attributes'].'><span style="color: red; font-style: italic;">Recaptcha doesn\'t display in back end</span></div></div></div>';
              
              break;
            }
            
            case 'type_hidden':
            {
              $params_names=array('w_name','w_value');
              $temp=$params;
              foreach($params_names as $params_name )
              {	
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }

              if($temp)
              {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' add_'.$attr;
              }
              $param['w_name'] = str_replace('&nbsp;','',$param['w_name']);
              $rep ='<div id="wdform_field'.$id.'" type="type_hidden" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" style="display: table-cell;"><span id="'.$id.'_element_labelform_id_temp" style="display: none;">'.$param['w_name'].'</span><span style="color: red; font-size: 13px;">Hidden field</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" style="display: table-cell; padding-left:7px;"><input type="hidden" value="'.$param['w_value'].'" id="'.$id.'_elementform_id_temp" name="'.$param['w_name'].'" '.$param['attributes'].'><input type="hidden" value="type_hidden" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><div><span align="left">Name: </span><span align="left" id="'.$id.'_hidden_nameform_id_temp">'.$param['w_name'].'</span></div><div><span align="left">Value: </span><span align="left" id="'.$id.'_hidden_valueform_id_temp">'.$param['w_value'].'</span></div></div></div>';
              
              break;
            }
            case 'type_mark_map':
            {
              $params_names=array('w_field_label_size','w_field_label_pos','w_center_x','w_center_y','w_long','w_lat','w_zoom','w_width','w_height','w_info','w_class');
              $temp=$params;
              foreach($params_names as $params_name )
              {	
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }

              if($temp)
              {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' add_'.$attr;
              }
            
              $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
            
              $rep ='<div id="wdform_field'.$id.'" type="type_mark_map" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="label" style="vertical-align: top;">'.$label.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_mark_map" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><div id="'.$id.'_elementform_id_temp" long0="'.$param['w_long'].'" lat0="'.$param['w_lat'].'" zoom="'.$param['w_zoom'].'" info0="'.$param['w_info'].'" center_x="'.$param['w_center_x'].'" center_y="'.$param['w_center_y'].'" style="width: '.$param['w_width'].'px; height: '.$param['w_height'].'px;" '.$param['attributes'].'></div></div></div>	';
              
              break;
            }
            
            case 'type_map':
            {
              $params_names=array('w_center_x','w_center_y','w_long','w_lat','w_zoom','w_width','w_height','w_info','w_class');
              $temp=$params;
              foreach($params_names as $params_name )
              {	
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }

              if($temp)
              {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' add_'.$attr;
              }
              
              $marker='';
              
              $param['w_long']	= explode('***',$param['w_long']);
              $param['w_lat']	= explode('***',$param['w_lat']);
              $param['w_info']	= explode('***',$param['w_info']);
              foreach($param['w_long'] as $key => $w_long )
              {
                $marker.='long'.$key.'="'.$w_long.'" lat'.$key.'="'.$param['w_lat'][$key].'" info'.$key.'="'.$param['w_info'][$key].'"';
              }
            
              $rep ='<div id="wdform_field'.$id.'" type="type_map" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: table-cell;"><span id="'.$id.'_element_labelform_id_temp" style="display: none;">'.$label.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: table-cell;"><input type="hidden" value="type_map" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><div id="'.$id.'_elementform_id_temp" zoom="'.$param['w_zoom'].'" center_x="'.$param['w_center_x'].'" center_y="'.$param['w_center_y'].'" style="width: '.$param['w_width'].'px; height: '.$param['w_height'].'px;" '.$marker.' '.$param['attributes'].'></div></div></div>';
              
              break;
            }
            case 'type_paypal_price':
            {
                        
              $params_names=array('w_field_label_size','w_field_label_pos','w_first_val','w_title', 'w_mini_labels','w_size','w_required','w_hide_cents','w_class','w_range_min','w_range_max');
              $temp=$params;
              foreach($params_names as $params_name )
              {	
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }

              if($temp)
              {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' add_'.$attr;
              }

              $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
              $input_active = ($param['w_first_val']==$param['w_title'] ? "input_deactive" : "input_active");	
              $required_sym = ($param['w_required']=="yes" ? " *" : "");	
              $hide_cents = ($param['w_hide_cents']=="yes" ? "none;" : "table-cell;");	
            

              $w_first_val = explode('***',$param['w_first_val']);
              $w_title = explode('***',$param['w_title']);
              $w_mini_labels = explode('***',$param['w_mini_labels']);
              
              $rep ='<div id="wdform_field'.$id.'" type="type_paypal_price" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required"style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_paypal_price" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_range_min'].'" name="'.$id.'_range_minform_id_temp" id="'.$id.'_range_minform_id_temp"><input type="hidden" value="'.$param['w_range_max'].'" name="'.$id.'_range_maxform_id_temp" id="'.$id.'_range_maxform_id_temp"><div id="'.$id.'_table_price" style="display: table;"><div id="'.$id.'_tr_price1" style="display: table-row;"><div id="'.$id.'_td_name_currency" style="display: table-cell;"><span class="wdform_colon" style="vertical-align: middle;"><!--repstart-->&nbsp;$&nbsp;<!--repend--></span></div><div id="'.$id.'_td_name_dollars" style="display: table-cell;"><input type="text" class="'.$input_active.'" id="'.$id.'_element_dollarsform_id_temp" name="'.$id.'_element_dollarsform_id_temp" value="'.$w_first_val[0].'" title="'.$w_title[0].'"onfocus="delete_value(&quot;'.$id.'_element_dollarsform_id_temp&quot;)" onblur="return_value(&quot;'.$id.'_element_dollarsform_id_temp&quot;)"onchange="change_value(&quot;'.$id.'_element_dollarsform_id_temp&quot;)" onkeypress="return check_isnum(event)" style="width: '.$param['w_size'].'px;" '.$param['attributes'].' disabled/></div><div id="'.$id.'_td_name_divider" style="display: '.$hide_cents.';"><span class="wdform_colon" style="vertical-align: middle;">&nbsp;.&nbsp;</span></div><div id="'.$id.'_td_name_cents" style="display: '.$hide_cents.'"><input type="text" class="'.$input_active.'" id="'.$id.'_element_centsform_id_temp" name="'.$id.'_element_centsform_id_temp" value="'.$w_first_val[1].'" title="'.$w_title[1].'"onfocus="delete_value(&quot;'.$id.'_element_centsform_id_temp&quot;)" onblur="return_value(&quot;'.$id.'_element_centsform_id_temp&quot;); add_0(&quot;'.$id.'_element_centsform_id_temp&quot;)"onchange="change_value(&quot;'.$id.'_element_centsform_id_temp&quot;)" onkeypress="return check_isnum_interval(event,&quot;'.$id.'_element_centsform_id_temp&quot;,0,99)"style="width: 30px;" '.$param['attributes'].' disabled/></div></div><div id="'.$id.'_tr_price2" style="display: table-row;"><div style="display: table-cell;"><label class="mini_label"></label></div><div align="left" style="display: table-cell;"><label class="mini_label" id="'.$id.'_mini_label_dollars">'.$w_mini_labels[0].'</label></div><div id="'.$id.'_td_name_label_divider" style="display: '.$hide_cents.'"><label class="mini_label"></label></div><div align="left" id="'.$id.'_td_name_label_cents" style="display: '.$hide_cents.'"><label class="mini_label" id="'.$id.'_mini_label_cents">'.$w_mini_labels[1].'</label></div></div></div></div></div>';
              break;
            }
            
            case 'type_paypal_select':
            {
            $params_names=array('w_field_label_size','w_field_label_pos','w_size','w_choices','w_choices_price','w_choices_checked', 'w_choices_disabled','w_required','w_quantity', 'w_quantity_value','w_class','w_property','w_property_values');
              $temp=$params;
			  if(strpos($temp, 'w_choices_params') > -1)
						$params_names=array('w_field_label_size','w_field_label_pos','w_size','w_choices','w_choices_price','w_choices_checked', 'w_choices_disabled','w_required','w_quantity', 'w_quantity_value', 'w_choices_params', 'w_class', 'w_property', 'w_property_values');	
              foreach($params_names as $params_name )
              {	
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }

              if($temp)
              {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' add_'.$attr;
              }

              $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
              $required_sym = ($param['w_required']=="yes" ? " *" : "");	
              $param['w_choices']	= explode('***',$param['w_choices']);
                 
              $param['w_choices_price']	= explode('***',$param['w_choices_price']);
                 
              $param['w_choices_checked']	= explode('***',$param['w_choices_checked']);
                
              $param['w_choices_disabled']	= explode('***',$param['w_choices_disabled']);
              $param['w_property']	= explode('***',$param['w_property']);
              $param['w_property_values']	= explode('***',$param['w_property_values']);
             if(isset($param['w_choices_params']))
						$param['w_choices_params'] = explode('***',$param['w_choices_params']);	
              foreach($param['w_choices_checked'] as $key => $choices_checked )
              {
                if($choices_checked=='true')
                  $param['w_choices_checked'][$key]='selected="selected"';
                else
                  $param['w_choices_checked'][$key]='';
              }

              
              $rep='<div id="wdform_field'.$id.'" type="type_paypal_select" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; "><input type="hidden" value="type_paypal_select" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><select id="'.$id.'_elementform_id_temp" name="'.$id.'_elementform_id_temp" onchange="set_select(this)" style="width: '.$param['w_size'].'px;"  '.$param['attributes'].' disabled>';
					foreach($param['w_choices'] as $key => $choice)
					{
						$where = '';
						$order_by = '';
						$db_info = '';
						$choice_value = $param['w_choices_disabled'][$key]=="true" ? '' : $param['w_choices_price'][$key];
							
						if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])
						{
							$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
							$where = "where='".$w_choices_params[0]."'";
							$w_choices_params = explode('[db_info]',$w_choices_params[1]);
							$order_by = "order_by='".$w_choices_params[0]."'";
							$db_info = "db_info='".$w_choices_params[1]."'";
						}	
						 $rep.='<option id="'.$id.'_option'.$key.'" value="'.$choice_value.'" onselect="set_select(&quot;'.$id.'_option'.$key.'&quot;)" '.$param['w_choices_checked'][$key].' '.$where.' '.$order_by.' '.$db_info.'>'.$choice.'</option>';
					}
					$rep.='</select><div id="'.$id.'_divform_id_temp">';
					if($param['w_quantity']=="yes")
						$rep.='<span id="'.$id.'_element_quantity_spanform_id_temp" style="margin-right: 15px;"><label class="mini_label" id="'.$id.'_element_quantity_label_form_id_temp" style="margin-right: 5px;"><!--repstart-->Quantity<!--repend--></label><input type="text" value="'.$param['w_quantity_value'].'" id="'.$id.'_element_quantityform_id_temp" name="'.$id.'_element_quantityform_id_temp" onkeypress="return check_isnum(event)" onchange="change_value(&quot;'.$id.'_element_quantityform_id_temp&quot;, this.value)" style="width: 30px; margin: 2px 0px;" disabled /></span>';
					if($param['w_property'][0])					
					foreach($param['w_property'] as $key => $property)
					{
	
					$rep.='
					<span id="'.$id.'_property_'.$key.'" style="margin-right: 15px;">
					
					<label class="mini_label" id="'.$id.'_property_label_form_id_temp'.$key.'" style="margin-right: 5px;">'.$property.'</label>
					<select id="'.$id.'_propertyform_id_temp'.$key.'" name="'.$id.'_propertyform_id_temp'.$key.'" style="width: auto; margin: 2px 0px;" disabled>';
					$param['w_property_values'][$key]	= explode('###',$param['w_property_values'][$key]);
					$param['w_property_values'][$key]	= array_slice($param['w_property_values'][$key],1, count($param['w_property_values'][$key]));   
					foreach($param['w_property_values'][$key] as $subkey => $property_value)
					{
						$rep.='<option id="'.$id.'_'.$key.'_option'.$subkey.'" value="'.$property_value.'">'.$property_value.'</option>';
					}
					$rep.='</select></span>';
					}
					
					$rep.='</div></div></div>';
					break;
            }
            
            case 'type_paypal_checkbox':
            {
                        
              $params_names=array('w_field_label_size','w_field_label_pos','w_flow','w_choices','w_choices_price','w_choices_checked','w_required','w_randomize','w_allow_other','w_allow_other_num','w_class','w_property','w_property_values','w_quantity','w_quantity_value');
              $temp=$params;
			  if(strpos($temp, 'w_field_option_pos') > -1)
						$params_names=array('w_field_label_size','w_field_label_pos', 'w_field_option_pos', 'w_flow','w_choices','w_choices_price','w_choices_checked','w_required','w_randomize','w_allow_other','w_allow_other_num', 'w_choices_params', 'w_class','w_property','w_property_values','w_quantity','w_quantity_value');
              foreach($params_names as $params_name )
              {	
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }

              if($temp)
              {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' add_'.$attr;
              }
              if(!isset($param['w_field_option_pos']))
						$param['w_field_option_pos'] = 'left';
						
              $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
              $required_sym = ($param['w_required']=="yes" ? " *" : "");	
              $param['w_choices']	= explode('***',$param['w_choices']);				   
              $param['w_choices_price']	= explode('***',$param['w_choices_price']);					
              $param['w_choices_checked']	= explode('***',$param['w_choices_checked']);		  
              $param['w_property']	= explode('***',$param['w_property']);
              $param['w_property_values']	= explode('***',$param['w_property_values']);
			  
              if(isset($param['w_choices_params']))
						$param['w_choices_params']	= explode('***',$param['w_choices_params']);
					
              foreach($param['w_choices_checked'] as $key => $choices_checked )
              {
                if($choices_checked=='true')
                  $param['w_choices_checked'][$key]='checked="checked"';
                else
                  $param['w_choices_checked'][$key]='';
              }
              
              $rep='<div id="wdform_field'.$id.'" type="type_paypal_checkbox" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_paypal_checkbox" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_randomize'].'" name="'.$id.'_randomizeform_id_temp" id="'.$id.'_randomizeform_id_temp"><input type="hidden" value="'.$param['w_allow_other'].'" name="'.$id.'_allow_otherform_id_temp" id="'.$id.'_allow_otherform_id_temp"><input type="hidden" value="'.$param['w_allow_other_num'].'" name="'.$id.'_allow_other_numform_id_temp" id="'.$id.'_allow_other_numform_id_temp"><input type="hidden" value="'.$param['w_field_option_pos'].'" id="'.$id.'_option_left_right"><div style="display: table;"><div id="'.$id.'_table_little" style="display: table-row-group;">';
				
					if($param['w_flow']=='hor')
					{
						$rep.= '<div id="'.$id.'_hor" style="display: table-row;">';
						foreach($param['w_choices'] as $key => $choice)
						{
						    $where ='';
							$order_by ='';
							$db_info = '';
							if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])
							{
								$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
								$where = "where='".$w_choices_params[0]."'";
								$w_choices_params = explode('[db_info]',$w_choices_params[1]);
								$order_by = "order_by='".$w_choices_params[0]."'";
								$db_info = "db_info='".$w_choices_params[1]."'";
							}
						
							$rep.='<div valign="top" id="'.$id.'_td_little'.$key.'" idi="'.$key.'" style="display: table-cell;"><input type="checkbox" id="'.$id.'_elementform_id_temp'.$key.'" name="'.$id.'_elementform_id_temp'.$key.'" value="'.$param['w_choices_price'][$key].'" onclick="set_checked(&quot;'.$id.'&quot;,&quot;'.$key.'&quot;,&quot;form_id_temp&quot;)" '.$param['w_choices_checked'][$key].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.$key.'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.$key.'" '.$where.' '.$order_by.' '.$db_info.'>'.$choice.'</label></div>';
						}
						$rep.= '</div>';
					}
					else
					{
						
						foreach($param['w_choices'] as $key => $choice)
						{
						$where ='';
							$order_by ='';
							$db_info ='';
							if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])
							{
								$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
								$where = "where='".$w_choices_params[0]."'";
								$w_choices_params = explode('[db_info]',$w_choices_params[1]);
								$order_by = "order_by='".$w_choices_params[0]."'";
								$db_info = "db_info='".$w_choices_params[1]."'";
							}
						
							$rep.='<div id="'.$id.'_element_tr'.$key.'" style="display: table-row;"><div valign="top" id="'.$id.'_td_little'.$key.'" idi="'.$key.'" style="display: table-cell;"><input type="checkbox" id="'.$id.'_elementform_id_temp'.$key.'" name="'.$id.'_elementform_id_temp'.$key.'" value="'.$param['w_choices_price'][$key].'" onclick="set_checked(&quot;'.$id.'&quot;,&quot;'.$key.'&quot;,&quot;form_id_temp&quot;)" '.$param['w_choices_checked'][$key].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.$key.'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.$key.'" '.$where.' '.$order_by.' '.$db_info.'>'.$choice.'</label></div></div>';		
					    }
						
					}
					$rep.='</div></div>';

					$rep.='<div id="'.$id.'_divform_id_temp">';
					if($param['w_quantity']=="yes")
						$rep.='<span id="'.$id.'_element_quantity_spanform_id_temp" style="margin-right: 15px;"><label class="mini_label" id="'.$id.'_element_quantity_label_form_id_temp" style="margin-right: 5px;"><!--repstart-->Quantity<!--repend--></label><input type="text" value="'.$param['w_quantity_value'].'" id="'.$id.'_element_quantityform_id_temp" name="'.$id.'_element_quantityform_id_temp" onkeypress="return check_isnum(event)" onchange="change_value(&quot;'.$id.'_element_quantityform_id_temp&quot;, this.value)" style="width: 30px; margin: 2px 0px;" disabled/></span>';
					if($param['w_property'][0])					
					foreach($param['w_property'] as $key => $property)
					{
					$rep.='
					<span id="'.$id.'_property_'.$key.'" style="margin-right: 15px;">
					
					<label class="mini_label" id="'.$id.'_property_label_form_id_temp'.$key.'" style="margin-right: 5px;">'.$property.'</label>
					<select id="'.$id.'_propertyform_id_temp'.$key.'" name="'.$id.'_propertyform_id_temp'.$key.'" style="width: auto; margin: 2px 0px;" disabled>';
					$param['w_property_values'][$key]	= explode('###',$param['w_property_values'][$key]);
					$param['w_property_values'][$key]	= array_slice($param['w_property_values'][$key],1, count($param['w_property_values'][$key]));   
					foreach($param['w_property_values'][$key] as $subkey => $property_value)
					{
						$rep.='<option id="'.$id.'_'.$key.'_option'.$subkey.'" value="'.$property_value.'">'.$property_value.'</option>';
					}
					$rep.='</select></span>';
					}
					
					$rep.='</div></div></div>';
					break;
            }
            case 'type_paypal_radio':
            {
              
              $params_names=array('w_field_label_size','w_field_label_pos','w_flow','w_choices','w_choices_price','w_choices_checked','w_required','w_randomize','w_allow_other','w_allow_other_num','w_class','w_property','w_property_values','w_quantity','w_quantity_value');
              $temp=$params;
			  if(strpos($temp, 'w_field_option_pos') > -1)
						$params_names=array('w_field_label_size','w_field_label_pos', 'w_field_option_pos', 'w_flow','w_choices','w_choices_price','w_choices_checked','w_required','w_randomize','w_allow_other','w_allow_other_num', 'w_choices_params', 'w_class','w_property','w_property_values','w_quantity','w_quantity_value');
              foreach($params_names as $params_name )
              {	
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }

              if($temp)
              {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' add_'.$attr;
              }
             if(!isset($param['w_field_option_pos']))
						$param['w_field_option_pos'] = 'left';
              $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
              $required_sym = ($param['w_required']=="yes" ? " *" : "");	
              $param['w_choices']	= explode('***',$param['w_choices']);
                 
              $param['w_choices_price']	= explode('***',$param['w_choices_price']);
              
              $param['w_choices_checked']	= explode('***',$param['w_choices_checked']);
                
              $param['w_property']	= explode('***',$param['w_property']);
              $param['w_property_values']	= explode('***',$param['w_property_values']);
			  
              if(isset($param['w_choices_params']))
						$param['w_choices_params']	= explode('***',$param['w_choices_params']);
						
              foreach($param['w_choices_checked'] as $key => $choices_checked )
              {
                if($choices_checked=='true')
                  $param['w_choices_checked'][$key]='checked="checked"';
                else
                  $param['w_choices_checked'][$key]='';
              }
              
              $rep='<div id="wdform_field'.$id.'" type="type_paypal_radio" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_paypal_radio" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_randomize'].'" name="'.$id.'_randomizeform_id_temp" id="'.$id.'_randomizeform_id_temp"><input type="hidden" value="'.$param['w_allow_other'].'" name="'.$id.'_allow_otherform_id_temp" id="'.$id.'_allow_otherform_id_temp"><input type="hidden" value="'.$param['w_allow_other_num'].'" name="'.$id.'_allow_other_numform_id_temp" id="'.$id.'_allow_other_numform_id_temp"><input type="hidden" value="'.$param['w_field_option_pos'].'" id="'.$id.'_option_left_right"><div style="display: table;"><div id="'.$id.'_table_little" style="display: table-row-group;">';
				
				if($param['w_flow']=='hor')
				{
					$rep.= '<div id="'.$id.'_hor" style="display: table-row;">';
					foreach($param['w_choices'] as $key => $choice)
					{
					$where ='';
						$order_by ='';
						$db_info ='';
						if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])
						{
							$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
							$where = "where='".$w_choices_params[0]."'";
							$w_choices_params = explode('[db_info]',$w_choices_params[1]);
							$order_by = "order_by='".$w_choices_params[0]."'";
							$db_info = "db_info='".$w_choices_params[1]."'";
						}
						
						$rep.='<div valign="top" id="'.$id.'_td_little'.$key.'" idi="'.$key.'" style="display: table-cell;"><input type="radio" id="'.$id.'_elementform_id_temp'.$key.'" name="'.$id.'_elementform_id_temp" value="'.$param['w_choices_price'][$key].'" onclick="set_default(&quot;'.$id.'&quot;,&quot;'.$key.'&quot;,&quot;form_id_temp&quot;)" '.$param['w_choices_checked'][$key].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.$key.'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.$key.'" '.$where.' '.$order_by.' '.$db_info.'>'.$choice.'</label></div>';
					}
					$rep.= '</div>';
				}
				else
				{
					
					foreach($param['w_choices'] as $key => $choice)
					{
					
						$where ='';
						$order_by ='';
						$db_info ='';
						if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])
						{
							$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
							$where = "where='".$w_choices_params[0]."'";
							$w_choices_params = explode('[db_info]',$w_choices_params[1]);
							$order_by = "order_by='".$w_choices_params[0]."'";
							$db_info = "db_info='".$w_choices_params[1]."'";
						}
						
						$rep.='<div id="'.$id.'_element_tr'.$key.'" style="display: table-row;"><div valign="top" id="'.$id.'_td_little'.$key.'" idi="'.$key.'" style="display: table-cell;"><input type="radio" id="'.$id.'_elementform_id_temp'.$key.'" name="'.$id.'_elementform_id_temp" value="'.$param['w_choices_price'][$key].'" onclick="set_default(&quot;'.$id.'&quot;,&quot;'.$key.'&quot;,&quot;form_id_temp&quot;)" '.$param['w_choices_checked'][$key].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.$key.'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.$key.'" '.$where.' '.$order_by.' '.$db_info.'>'.$choice.'</label></div></div>';				
					}
					
				}
					$rep.='</div></div>';

					$rep.='<div id="'.$id.'_divform_id_temp">';
					if($param['w_quantity']=="yes")
						$rep.='<span id="'.$id.'_element_quantity_spanform_id_temp" style="margin-right: 15px;"><label class="mini_label" id="'.$id.'_element_quantity_label_form_id_temp" style="margin-right: 5px;"><!--repstart-->Quantity<!--repend--></label><input type="text" value="'.$param['w_quantity_value'].'" id="'.$id.'_element_quantityform_id_temp" name="'.$id.'_element_quantityform_id_temp" onkeypress="return check_isnum(event)" onchange="change_value(&quot;'.$id.'_element_quantityform_id_temp&quot;, this.value)" style="width: 30px; margin: 2px 0px;" disabled/></span>';
					if($param['w_property'][0])					
					foreach($param['w_property'] as $key => $property)
					{
					$rep.='
					<span id="'.$id.'_property_'.$key.'" style="margin-right: 15px;">
					
					<label class="mini_label" id="'.$id.'_property_label_form_id_temp'.$key.'" style="margin-right: 5px;">'.$property.'</label>
					<select id="'.$id.'_propertyform_id_temp'.$key.'" name="'.$id.'_propertyform_id_temp'.$key.'" style="width: auto; margin: 2px 0px;" disabled>';
					$param['w_property_values'][$key]	= explode('###',$param['w_property_values'][$key]);
					$param['w_property_values'][$key]	= array_slice($param['w_property_values'][$key],1, count($param['w_property_values'][$key]));   
					foreach($param['w_property_values'][$key] as $subkey => $property_value)
					{
						$rep.='<option id="'.$id.'_'.$key.'_option'.$subkey.'" value="'.$property_value.'">'.$property_value.'</option>';
					}
					$rep.='</select></span>';
					}
					
					$rep.='</div></div></div>';
				
					break;
            }
            case 'type_paypal_shipping':
            {
              
              $params_names=array('w_field_label_size','w_field_label_pos', 'w_field_option_pos', 'w_flow','w_choices','w_choices_price','w_choices_checked','w_required','w_randomize','w_allow_other','w_allow_other_num','w_class');
              $temp=$params;
			  if(strpos($temp, 'w_field_option_pos') > -1)
				$params_names=array('w_field_label_size','w_field_label_pos', 'w_flow','w_choices','w_choices_price','w_choices_checked','w_required','w_randomize','w_allow_other','w_allow_other_num','w_choices_params','w_class');
              foreach($params_names as $params_name )
              {	
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }

              if($temp)
              {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' add_'.$attr;
              }
              if(!isset($param['w_field_option_pos']))
						$param['w_field_option_pos'] = 'left';
						
              $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
              $required_sym = ($param['w_required']=="yes" ? " *" : "");	
              $param['w_choices']	= explode('***',$param['w_choices']);
                 
              $param['w_choices_price']	= explode('***',$param['w_choices_price']);
              
              $param['w_choices_checked']	= explode('***',$param['w_choices_checked']);
                
              if(isset($param['w_choices_params']))
						$param['w_choices_params']	= explode('***',$param['w_choices_params']); 
						
              foreach($param['w_choices_checked'] as $key => $choices_checked )
              {
                if($choices_checked=='true')
                  $param['w_choices_checked'][$key]='checked="checked"';
                else
                  $param['w_choices_checked'][$key]='';
              }
              
              $rep='<div id="wdform_field'.$id.'" type="type_paypal_shipping" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="wd_form_label" style="vertical-align: top;">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required" style="vertical-align: top;">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; vertical-align:top;"><input type="hidden" value="type_paypal_shipping" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_randomize'].'" name="'.$id.'_randomizeform_id_temp" id="'.$id.'_randomizeform_id_temp"><input type="hidden" value="'.$param['w_allow_other'].'" name="'.$id.'_allow_otherform_id_temp" id="'.$id.'_allow_otherform_id_temp"><input type="hidden" value="'.$param['w_allow_other_num'].'" name="'.$id.'_allow_other_numform_id_temp" id="'.$id.'_allow_other_numform_id_temp"><input type="hidden" value="'.$param['w_field_option_pos'].'" id="'.$id.'_option_left_right"><div style="display: table;"><div id="'.$id.'_table_little" style="display: table-row-group;">';
				
				if($param['w_flow']=='hor')
				{
					$rep.= '<div id="'.$id.'_hor" style="display: table-row;">';
					foreach($param['w_choices'] as $key => $choice)
					{
					    $where ='';
						$order_by ='';
						$db_info ='';
						if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])
						{
							$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
							$where = "where='".$w_choices_params[0]."'";
							$w_choices_params = explode('[db_info]',$w_choices_params[1]);
							$order_by = "order_by='".$w_choices_params[0]."'";
							$db_info = "db_info='".$w_choices_params[1]."'";
						}
						
						$rep.='<div valign="top" id="'.$id.'_td_little'.$key.'" idi="'.$key.'" style="display: table-cell;"><input type="radio" id="'.$id.'_elementform_id_temp'.$key.'" name="'.$id.'_elementform_id_temp" value="'.$param['w_choices_price'][$key].'" onclick="set_default(&quot;'.$id.'&quot;,&quot;'.$key.'&quot;,&quot;form_id_temp&quot;)" '.$param['w_choices_checked'][$key].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.$key.'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.$key.'" '.$where.' '.$order_by.' '.$db_info.'>'.$choice.'</label></div>';
					}
					$rep.= '</div>';
				}
				else
				{
					
					foreach($param['w_choices'] as $key => $choice)
					{
					$where ='';
						$order_by ='';
						$db_info =''; 
						if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])
						{
							$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
							$where = "where='".$w_choices_params[0]."'";
							$w_choices_params = explode('[db_info]',$w_choices_params[1]);
							$order_by = "order_by='".$w_choices_params[0]."'";
							$db_info = "db_info='".$w_choices_params[1]."'";
						}
					
						$rep.='<div id="'.$id.'_element_tr'.$key.'" style="display: table-row;"><div valign="top" id="'.$id.'_td_little'.$key.'" idi="'.$key.'" style="display: table-cell;"><input type="radio" id="'.$id.'_elementform_id_temp'.$key.'" name="'.$id.'_elementform_id_temp" value="'.$param['w_choices_price'][$key].'" onclick="set_default(&quot;'.$id.'&quot;,&quot;'.$key.'&quot;,&quot;form_id_temp&quot;)" '.$param['w_choices_checked'][$key].' '.$param['attributes'].' '.($param['w_field_option_pos']=='right' ? 'style="float:left !important;"' : "").' disabled/><label id="'.$id.'_label_element'.$key.'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.$key.'" '.$where.' '.$order_by.' '.$db_info.'>'.$choice.'</label></div></div>';
					}
					
				}
					$rep.='</div></div>';

					$rep.='</div></div>';
				
					break;
            }
            case 'type_paypal_total':
            {
              $params_names=array('w_field_label_size','w_field_label_pos','w_class');
              $temp=$params;
              foreach($params_names as $params_name )
              {	
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }

              if($temp)
              {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' add_'.$attr;
              }
              
              $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
            
                    
                      
              $rep='<div id="wdform_field'.$id.'" type="type_paypal_total" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="label">'.$label.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_paypal_total" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><div id="'.$id.'paypal_totalform_id_temp" class="wdform_paypal_total paypal_totalform_id_temp"><input type="hidden" value="" name="'.$id.'_paypal_totalform_id_temp" class="input_paypal_totalform_id_temp"><div id="'.$id.'div_totalform_id_temp" class="div_totalform_id_temp" style="margin-bottom: 10px;"><!--repstart-->$300<!--repend--></div><div id="'.$id.'paypal_productsform_id_temp" class="paypal_productsform_id_temp" style="border-spacing: 2px;"><div style="border-spacing: 2px;"><!--repstart-->product 1 $100<!--repend--></div><div style="border-spacing: 2px;"><!--repstart-->product 2 $200<!--repend--></div></div><div id="'.$id.'paypal_taxform_id_temp" class="paypal_taxform_id_temp" style="border-spacing: 2px; margin-top: 7px;"></div></div></div></div>';
            
            
              break;
            }

            case 'type_star_rating':
            {
              $params_names=array('w_field_label_size','w_field_label_pos','w_field_label_col','w_star_amount','w_required','w_class');
              $temp=$params;
              foreach($params_names as $params_name )
              {	
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }

              if($temp)
              {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' add_'.$attr;
              }
              $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
              $required_sym = ($param['w_required']=="yes" ? " *" : "");
              
      
            $images = '';	
              for($i=0; $i<$param['w_star_amount']; $i++)
              {
                $images .= '<img id="'.$id.'_star_'.$i.'" src="' . WD_FMC_URL . '/images/star.png?ver='. get_option("wd_form_maker_version").'" onmouseover="change_src('.$i.','.$id.',&quot;form_id_temp&quot;)" onmouseout="reset_src('.$i.','.$id.')" onclick="select_star_rating('.$i.','.$id.', &quot;form_id_temp&quot;)">';
              }
              
              $rep ='<div id="wdform_field'.$id.'" type="type_star_rating" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="label">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_star_rating" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_star_amount'].'" id="'.$id.'_star_amountform_id_temp" name="'.$id.'_star_amountform_id_temp"><input type="hidden" value="'.$param['w_field_label_col'].'" name="'.$id.'_star_colorform_id_temp" id="'.$id.'_star_colorform_id_temp"><div id="'.$id.'_elementform_id_temp" class="wdform_stars" '.$param['attributes'].'>'.$images.'</div></div></div>';
              
              
              break;
            }
            case 'type_scale_rating':
            {
              $params_names=array('w_field_label_size','w_field_label_pos','w_mini_labels','w_scale_amount','w_required','w_class');
              $temp=$params;
              foreach($params_names as $params_name )
              {	
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }

              if($temp)
              {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' add_'.$attr;
              }
              $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
              $required_sym = ($param['w_required']=="yes" ? " *" : "");
              
              $w_mini_labels = explode('***',$param['w_mini_labels']);
              
              $numbers = '';	
              for($i=1; $i<=$param['w_scale_amount']; $i++)
              {
                $numbers .= '<div id="'.$id.'_scale_td1_'.$i.'form_id_temp" style="text-align: center; display: table-cell;"><span>'.$i.'</span></div>';
              }
              
                        
              $radio_buttons = '';	
              for($k=1; $k<=$param['w_scale_amount']; $k++)
              {
                $radio_buttons .= '<div id="'.$id.'_scale_td2_'.$k.'form_id_temp" style="display: table-cell;"><input id="'.$id.'_scale_radioform_id_temp_'.$k.'" name="'.$id.'_scale_radioform_id_temp" value="'.$k.'" type="radio"></div>';
              }
              
              $rep ='<div id="wdform_field'.$id.'" type="type_scale_rating" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; vertical-align: top; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="label">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_scale_rating" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_scale_amount'].'" id="'.$id.'_scale_amountform_id_temp" name="'.$id.'_scale_amountform_id_temp"><div id="'.$id.'_elementform_id_temp" style="float: left;" '.$param['attributes'].'><label class="mini_label" id="'.$id.'_mini_label_worst" style="position: relative; top: 6px; font-size: 11px; display: inline-table;">'.$w_mini_labels[0].'</label><div id="'.$id.'_scale_tableform_id_temp" style="display: inline-table;"><div id="'.$id.'_scale_tr1form_id_temp" style="display: table-row;">'.$numbers.'</div><div id="'.$id.'_scale_tr2form_id_temp" style="display: table-row;">'.$radio_buttons.'</div></div><label class="mini_label" id="'.$id.'_mini_label_best" style="position: relative; top: 6px; font-size: 11px; display: inline-table;">'.$w_mini_labels[1].'</label></div></div></div>';
                  
              break;
            }
            case 'type_spinner':
            {
              $params_names=array('w_field_label_size','w_field_label_pos','w_field_width','w_field_min_value','w_field_max_value', 'w_field_step', 'w_field_value', 'w_required','w_class');
              $temp=$params;
              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' add_'.$attr;
              }
              $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
              $required_sym = ($param['w_required']=="yes" ? " *" : "");
              $rep ='<div id="wdform_field'.$id.'" type="type_spinner" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="label">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_spinner" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_field_width'].'" name="'.$id.'_spinner_widthform_id_temp" id="'.$id.'_spinner_widthform_id_temp"><input type="hidden" value="'.$param['w_field_min_value'].'" id="'.$id.'_min_valueform_id_temp" name="'.$id.'_min_valueform_id_temp"><input type="hidden" value="'.$param['w_field_max_value'].'" name="'.$id.'_max_valueform_id_temp" id="'.$id.'_max_valueform_id_temp"><input type="hidden" value="'.$param['w_field_step'].'" name="'.$id.'_stepform_id_temp" id="'.$id.'_stepform_id_temp"><input type="" value="'.($param['w_field_value']!= 'null' ? $param['w_field_value'] : '').'" name="'.$id.'_elementform_id_temp" id="'.$id.'_elementform_id_temp" onkeypress="return check_isnum_or_minus(event)" style="width: '.$param['w_field_width'].'px;" '.$param['attributes'].' disabled/></div></div>';
              break;
            }
            case 'type_slider': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_field_width','w_field_min_value','w_field_max_value', 'w_field_value', 'w_required','w_class');
              $temp=$params;
              foreach($params_names as $params_name ) {	
                $temp = explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if ($temp) {
                $temp	= explode('*:*w_attr_name*:*', $temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'] . ' add_' . $attr;
                }
              }
              $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
              $required_sym = ($param['w_required']=="yes" ? " *" : "");
              $rep ='<div id="wdform_field'.$id.'" type="type_slider" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; vertical-align: top; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="label">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_slider" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_field_width'].'" name="'.$id.'_slider_widthform_id_temp" id="'.$id.'_slider_widthform_id_temp"><input type="hidden" value="'.$param['w_field_min_value'].'" id="'.$id.'_slider_min_valueform_id_temp" name="'.$id.'_slider_min_valueform_id_temp"><input type="hidden" value="'.$param['w_field_max_value'].'" id="'.$id.'_slider_max_valueform_id_temp" name="'.$id.'_slider_max_valueform_id_temp"><input type="hidden" value="'.$param['w_field_value'].'" id="'.$id.'_slider_valueform_id_temp" name="'.$id.'_slider_valueform_id_temp"><div id="'.$id.'_slider_tableform_id_temp"><div><div id="'.$id.'_slider_td1form_id_temp"><div name="'.$id.'_elementform_id_temp" id="'.$id.'_elementform_id_temp" style="width: '.$param['w_field_width'].'px;" '.$param['attributes'].'"></div></div></div><div><div align="left" id="'.$id.'_slider_td2form_id_temp" style="display: inline-table; width: 33.3%; text-align: left;"><span id="'.$id.'_element_minform_id_temp" class="label">'.$param['w_field_min_value'].'</span></div><div align="right" id="'.$id.'_slider_td3form_id_temp" style="display: inline-table; width: 33.3%; text-align: center;"><span id="'.$id.'_element_valueform_id_temp" class="label">'.$param['w_field_value'].'</span></div><div align="right" id="'.$id.'_slider_td4form_id_temp" style="display: inline-table; width: 33.3%; text-align: right;"><span id="'.$id.'_element_maxform_id_temp" class="label">'.$param['w_field_max_value'].'</span></div></div></div></div></div>';
              break;
            }
            case 'type_range': {
              $params_names = array('w_field_label_size','w_field_label_pos','w_field_range_width','w_field_range_step','w_field_value1', 'w_field_value2', 'w_mini_labels', 'w_required','w_class');
              $temp = $params;
              foreach ($params_names as $params_name ) {
                $temp = explode('*:*' . $params_name . '*:*', $temp);
                $param[$params_name] = $temp[0];
                $temp = $temp[1];
              }
              if ($temp) {	
                $temp	= explode('*:*w_attr_name*:*', $temp);
                $attrs = array_slice($temp, 0, count($temp) - 1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' add_'.$attr;
                }
              }
              $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
              $required_sym = ($param['w_required']=="yes" ? " *" : "");
              $w_mini_labels = explode('***',$param['w_mini_labels']);
              $rep ='<div id="wdform_field'.$id.'" type="type_range" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="label">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_range" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_field_range_width'].'" name="'.$id.'_range_widthform_id_temp" id="'.$id.'_range_widthform_id_temp"><input type="hidden" value="'.$param['w_field_range_step'].'" name="'.$id.'_range_stepform_id_temp" id="'.$id.'_range_stepform_id_temp"><div id="'.$id.'_elemet_table_littleform_id_temp" style="display: table;"><div style="display: table-row;"><div valign="middle" align="left" style="display: table-cell;"><input type="" value="'.($param['w_field_value1']!= 'null' ? $param['w_field_value1'] : '').'" name="'.$id.'_elementform_id_temp0" id="'.$id.'_elementform_id_temp0" onkeypress="return check_isnum_or_minus(event)" style="width: '.$param['w_field_range_width'].'px;"  '.$param['attributes'].' disabled/></div><div valign="middle" align="left" style="display: table-cell; padding-left: 4px;"><input type="" value="'.($param['w_field_value2']!= 'null' ? $param['w_field_value2'] : '').'" name="'.$id.'_elementform_id_temp1" id="'.$id.'_elementform_id_temp1" onkeypress="return check_isnum_or_minus(event)" style="width: '.$param['w_field_range_width'].'px;" '.$param['attributes'].' disabled/></div></div><div style="display: table-row;"><div valign="top" align="left" style="display: table-cell;"><label class="mini_label" id="'.$id.'_mini_label_from">'.$w_mini_labels[0].'</label></div><div valign="top" align="left" style="display: table-cell;"><label class="mini_label" id="'.$id.'_mini_label_to">'.$w_mini_labels[1].'</label></div></div></div></div></div>';
              break;
            }
            case 'type_grading': {
              $params_names = array('w_field_label_size', 'w_field_label_pos', 'w_items', 'w_total', 'w_required', 'w_class');
              $temp = $params;
              foreach($params_names as $params_name) {
                $temp = explode('*:*' . $params_name . '*:*', $temp);
                $param[$params_name] = $temp[0];
                $temp = $temp[1];
              }
              if ($temp) {	
                $temp	= explode('*:*w_attr_name*:*', $temp);
                $attrs	= array_slice($temp, 0, count($temp) - 1);   
                foreach ($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' add_'.$attr;
                }
              }
              $param['w_field_label_pos'] = ($param['w_field_label_pos'] == "left" ? "table-cell" : "block");
              $required_sym = ($param['w_required'] == "yes" ? " *" : "");
              $w_items = explode('***', $param['w_items']);
              $grading_items = '';
              for($i=0; $i<count($w_items); $i++)
					{
						$grading_items .= '<div id="'.$id.'_element_div'.$i.'" class="grading"><input id="'.$id.'_elementform_id_temp_'.$i.'" name="'.$id.'_elementform_id_temp_'.$i.'" onkeypress="return check_isnum_or_minus(event)" value="" size="5" onkeyup="sum_grading_values('.$id.',&quot;form_id_temp&quot;)" onchange="sum_grading_values('.$id.',&quot;form_id_temp&quot;)" '.$param['attributes'].' disabled/><label id="'.$id.'_label_elementform_id_temp'.$i.'" class="ch-rad-label">'.$w_items[$i].'</label></div>';
					}
									
					$rep ='<div id="wdform_field'.$id.'" type="type_grading" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; vertical-align: top; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="label">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_grading" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_total'].'" name="'.$id.'_grading_totalform_id_temp" id="'.$id.'_grading_totalform_id_temp"><div id="'.$id.'_elementform_id_temp">'.$grading_items.'<div id="'.$id.'_element_total_divform_id_temp" class="grading_div">Total:<span id="'.$id.'_sum_elementform_id_temp" name="'.$id.'_sum_elementform_id_temp">0</span>/<span id="'.$id.'_total_elementform_id_temp" name="'.$id.'_total_elementform_id_temp">'.$param['w_total'].'</span><span id="'.$id.'_text_elementform_id_temp" name="'.$id.'_text_elementform_id_temp"></span></div></div></div></div>';
											
					break;
            }
            case 'type_matrix': {
              $params_names=array('w_field_label_size','w_field_label_pos', 'w_field_input_type', 'w_rows', 'w_columns', 'w_required','w_class','w_textbox_size');
              $temp = $params;
              foreach ($params_names as $params_name) {
                $temp = explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp = $temp[1];
              }
              if ($temp) {
                $temp	= explode('*:*w_attr_name*:*', $temp);
                $attrs = array_slice($temp, 0, count($temp) - 1);
                foreach ($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' add_'.$attr;
                }
              }
              $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "table-cell" : "block");	
              $required_sym = ($param['w_required']=="yes" ? " *" : "");
			  $param['w_textbox_size'] = isset($param['w_textbox_size']) ? $param['w_textbox_size'] : '100';
              $w_rows = explode('***',$param['w_rows']);
              $w_columns = explode('***',$param['w_columns']);
              $column_labels = '';
              for ($i = 1; $i < count($w_columns); $i++) {
                $column_labels .= '<div id="'.$id.'_element_td0_'.$i.'" class="matrix_" style="display: table-cell;"><label id="'.$id.'_label_elementform_id_temp0_'.$i.'" name="'.$id.'_label_elementform_id_temp0_'.$i.'" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.$i.'" value="'.$w_columns[$i].'">'.$w_columns[$i].'</label></div>';
              }
              $rows_columns = '';
              for($i=1; $i<count($w_rows); $i++)
					{
					
						$rows_columns .= '<div id="'.$id.'_element_tr'.$i.'" style="display: table-row;"><div id="'.$id.'_element_td'.$i.'_0" class="matrix_" style="display: table-cell;"><label id="'.$id.'_label_elementform_id_temp'.$i.'_0" class="ch-rad-label" for="'.$id.'_elementform_id_temp'.$i.'" value="'.$w_rows[$i].'">'.$w_rows[$i].'</label></div>';
						
						
						for($k=1; $k<count($w_columns); $k++)
						{
							if($param['w_field_input_type']=='radio')
								$rows_columns .= '<div id="'.$id.'_element_td'.$i.'_'.$k.'" style="text-align: center; display: table-cell;  padding: 5px 0 0 5px;"><input id="'.$id.'_input_elementform_id_temp'.$i.'_'.$k.'" align="center" size="14" type="radio" name="'.$id.'_input_elementform_id_temp'.$i.'" value="'.$i.'_'.$k.'" disabled/></div>';
							else
								if($param['w_field_input_type']=='checkbox')
									$rows_columns .= '<div id="'.$id.'_element_td'.$i.'_'.$k.'" style="text-align: center; display: table-cell;  padding: 5px 0 0 5px;"><input id="'.$id.'_input_elementform_id_temp'.$i.'_'.$k.'" align="center" size="14" type="checkbox" name="'.$id.'_input_elementform_id_temp'.$i.'_'.$k.'" value="1" disabled/></div>';
								else
									if($param['w_field_input_type']=='text')
										$rows_columns .= '<div id="'.$id.'_element_td'.$i.'_'.$k.'" style="text-align: center; display: table-cell; padding: 5px 0 0 5px;"><input id="'.$id.'_input_elementform_id_temp'.$i.'_'.$k.'" align="center" type="text" name="'.$id.'_input_elementform_id_temp'.$i.'_'.$k.'" value="" style="width:'.$param['w_textbox_size'].'px" disabled/></div>';
									else
										if($param['w_field_input_type']=='select')
											$rows_columns .= '<div id="'.$id.'_element_td'.$i.'_'.$k.'" style="text-align: center; display: table-cell; padding: 5px 0 0 5px;"><select id="'.$id.'_select_yes_noform_id_temp'.$i.'_'.$k.'" name="'.$id.'_select_yes_noform_id_temp'.$i.'_'.$k.'" disabled><option value=""> </option><option value="yes">Yes</option><option value="no">No</option></select></div>';
								
						}
							
						$rows_columns .= '</div>';	
					}
						
					
						
					$rep ='<div id="wdform_field'.$id.'" type="type_matrix" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span id="'.$id.'_element_labelform_id_temp" class="label">'.$label.'</span><span id="'.$id.'_required_elementform_id_temp" class="required">'.$required_sym.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: '.$param['w_field_label_pos'].';"><input type="hidden" value="type_matrix" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><input type="hidden" value="'.$param['w_required'].'" name="'.$id.'_requiredform_id_temp" id="'.$id.'_requiredform_id_temp"><input type="hidden" value="'.$param['w_field_input_type'].'" name="'.$id.'_input_typeform_id_temp" id="'.$id.'_input_typeform_id_temp"><input type="hidden" value="'.$param['w_textbox_size'].'" name="'.$id.'_textbox_sizeform_id_temp" id="'.$id.'_textbox_sizeform_id_temp"><div id="'.$id.'_elementform_id_temp" style="display: table;" '.$param['attributes'].'><div id="'.$id.'_table_little" style="display: table-row-group;"><div id="'.$id.'_element_tr0" style="display: table-row;"><div id="'.$id.'_element_td0_0" style="display: table-cell;"></div>'.$column_labels.'</div>'.$rows_columns.'</div></div></div></div>';
											
					break;
            }
            case 'type_submit_reset': {
              $params_names=array('w_submit_title','w_reset_title','w_class','w_act');
              $temp=$params;
              foreach ($params_names as $params_name) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if ($temp) {
                $temp	= explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach ($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' add_'.$attr;
                }
              }
              $param['w_act'] = ($param['w_act']=="false" ? 'style="display: none;"' : "");	
              $rep='<div id="wdform_field'.$id.'" type="type_submit_reset" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: table-cell;"><span id="'.$id.'_element_labelform_id_temp" style="display: none;">type_submit_reset_'.$id.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: table-cell;"><input type="hidden" value="type_submit_reset" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp"><button type="button" class="button-submit" id="'.$id.'_element_submitform_id_temp" value="'.$param['w_submit_title'].'" onclick="check_required(&quot;submit&quot;, &quot;form_id_temp&quot;);" '.$param['attributes'].'>'.$param['w_submit_title'].'</button><button type="button" class="button-reset" id="'.$id.'_element_resetform_id_temp" value="'.$param['w_reset_title'].'" onclick="check_required(&quot;reset&quot;);" '.$param['w_act'].' '.$param['attributes'].'>'.$param['w_reset_title'].'</button></div></div>';
              break;
            }
            case 'type_button': {
              $params_names=array('w_title','w_func','w_class');
              $temp=$params;
              foreach($params_names as $params_name) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if ($temp) {	
                $temp	= explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' add_'.$attr;
                }
              }
              $param['w_title']	= explode('***',$param['w_title']);
              $param['w_func']	= explode('***',$param['w_func']);
              $rep.='<div id="wdform_field'.$id.'" type="type_button" class="wdform_field" style="display: table-cell;">'.$arrows.'<div align="left" id="'.$id.'_label_sectionform_id_temp" class="'.$param['w_class'].'" style="display: table-cell;"><span id="'.$id.'_element_labelform_id_temp" style="display: none;">button_'.$id.'</span></div><div align="left" id="'.$id.'_element_sectionform_id_temp" class="'.$param['w_class'].'" style="display: table-cell;"><input type="hidden" value="type_button" name="'.$id.'_typeform_id_temp" id="'.$id.'_typeform_id_temp">';
              foreach ($param['w_title'] as $key => $title) {
                $rep.='<button type="button" id="'.$id.'_elementform_id_temp'.$key.'" name="'.$id.'_elementform_id_temp'.$key.'" value="'.$title.'" onclick="'.$param['w_func'][$key].'" '.$param['attributes'].'>'.$title.'</button>';				
              }
              $rep .= '</div></div>';
              break;
            }
          }
          
          $form = str_replace('%' . $id . ' - ' . $labels[$ids_key] . '%', $rep, $form);
          $form = str_replace('%' . $id . ' -' . $labels[$ids_key] . '%', $rep, $form);
          $row->form_front = $form;
        }
      }
    }
    else {
      $row = new stdClass();
      $row->id = 0;
      $row->backup_id ='';
      $row->title = '';
      $row->mail = '';
      $row->form = '';
      $row->form_front = '';
      $row->theme = $wpdb->get_var("SELECT id FROM " . $wpdb->prefix . "formmaker_themes WHERE `default`='1'");
      $row->javascript = '';
      $row->submit_text = '';
      $row->url = '';
      $row->submit_text_type = 0;
      $row->script1 = '';
      $row->script2 = '';
      $row->script_user1 = '';
      $row->script_user2 = '';
      $row->counter = 0;
      $row->label_order = '';
      $row->article_id = '';
      $row->pagination = '';
      $row->show_title = '';
      $row->show_numbers = '';
      $row->public_key = '';
      $row->private_key = '';
      $row->recaptcha_theme = '';
      $row->from_name = '';
      $row->from_mail = '';
      $row->label_order_current = '';
      $row->script_mail_user = '';
      $row->script_mail = '';
      $row->tax = 0;
      $row->payment_currency = '$';
      $row->paypal_email = '';
      $row->checkout_mode = 'testmode';
      $row->paypal_mode = 0;

      $row->published = 1;
      $row->form_fields = '';
      $row->savedb = 1;
      $row->sendemail = 1;
      $row->requiredmark = '*';
      $row->reply_to = 0;
      $row->send_to = 0;
      $row->autogen_layout = 1;
      $row->custom_front = '';
      $row->mail_from_user = '';
      $row->mail_from_name_user = '';
      $row->reply_to_user = '';
      $row->save_uploads = 1;

      $row->condition = '';
      $row->mail_cc = '';
      $row->mail_cc_user = '';
      $row->mail_bcc = '';
      $row->mail_bcc_user = '';
      $row->mail_subject = '';
      $row->mail_subject_user = '';
      $row->mail_mode = 1;
      $row->mail_mode_user = 1;
      $row->mail_attachment = 1;
      $row->mail_attachment_user = 1;
      
      $row->user_id_wd = '';
      $row->sortable = 1;
      $row->frontend_submit_fields = '';
      $row->frontend_submit_stat_fields = '';
    }
    return $row;
  }

  public function get_theme_rows_data($old = '') {
    global $wpdb;
    $rows = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "formmaker_themes WHERE css " . ($old ? 'NOT' : '') . " LIKE '%.wdform_section%' ORDER BY title");
    return $rows;
  }
  
  public function get_queries_rows_data($id) {
    global $wpdb;
    $rows = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "formmaker_query WHERE form_id=" . $id . " ORDER BY id ASC");
    return $rows;
  }
  
  public function get_labels($id) {
    global $wpdb;
    $rows = $wpdb->get_col("SELECT DISTINCT `element_label` FROM " . $wpdb->prefix . "formmaker_submits WHERE form_id=" . $id);
    return $rows;
  }
  
  public function is_paypal($id) {
    global $wpdb;
    $rows = $wpdb->get_var("SELECT COUNT(*) FROM " . $wpdb->prefix . "formmaker_sessions WHERE form_id=" . $id);
    return $rows;
  }

  public function page_nav() {
    global $wpdb;
	$where = 'WHERE `id` IN (' . (get_option('contact_form_forms', '') != '' ? get_option('contact_form_forms') : 0) . ')';
    $where .= ((isset($_POST['search_value']) && (esc_html($_POST['search_value']) != '')) ? 'WHERE title LIKE "%' . esc_html($_POST['search_value']) . '%"'  : '');
    $query = "SELECT COUNT(*) FROM " . $wpdb->prefix . "formmaker " . $where;
    $total = $wpdb->get_var($query);
    $page_nav['total'] = $total;
    if (isset($_POST['page_number']) && $_POST['page_number']) {
      $limit = ((int) $_POST['page_number'] - 1) * 20;
    }
    else {
      $limit = 0;
    }
    $page_nav['limit'] = (int) ($limit / 20 + 1);
    return $page_nav;
  }
  ////////////////////////////////////////////////////////////////////////////////////////
  // Getters & Setters                                                                  //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Private Methods                                                                    //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Listeners                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
}