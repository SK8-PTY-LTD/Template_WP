<?php

class FMControllerSubmissions_fmc {
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
  public function execute() {
    $task = ((isset($_POST['task'])) ? esc_html($_POST['task']) : ''); 
    $id = ((isset($_POST['current_id'])) ? (int)esc_html($_POST['current_id']) : 0);
    $form_id = ((isset($_POST['form_id']) && esc_html($_POST['form_id']) != '') ? (int)esc_html($_POST['form_id']) : 0);	
	
    if (method_exists($this, $task)) {
		if($task != 'show_stats')
			check_admin_referer('nonce_fmc', 'nonce_fmc');
		else
			check_ajax_referer('nonce_fmc_ajax', 'nonce_fmc_ajax');
		$this->$task($id); 
    }
    else {
		$this->display($form_id); 
    }
  }
  
  public function display($form_id) {
    $form_id = ((isset($_POST['form_id']) && esc_html($_POST['form_id']) != '') ? (int)esc_html($_POST['form_id']) : 0);
    require_once WD_FMC_DIR . "/admin/models/FMModelSubmissions_fmc.php";
    $model = new FMModelSubmissions_fmc();

    require_once WD_FMC_DIR . "/admin/views/FMViewSubmissions_fmc.php";
    $view = new FMViewSubmissions_fmc($model);
    $view->display($form_id);
  }

  public function show_stats() {

    $form_id = ((isset($_POST['form_id']) && esc_html($_POST['form_id']) != '') ? (int)esc_html($_POST['form_id']) : 0);
    require_once WD_FMC_DIR . "/admin/models/FMModelSubmissions_fmc.php";
    $model = new FMModelSubmissions_fmc();

    require_once WD_FMC_DIR . "/admin/views/FMViewSubmissions_fmc.php";
    $view = new FMViewSubmissions_fmc($model);
    $view->show_stats($form_id);
  }

  public function edit() {
    global $wpdb;
    require_once WD_FMC_DIR . "/admin/models/FMModelSubmissions_fmc.php";
    $model = new FMModelSubmissions_fmc();

    require_once WD_FMC_DIR . "/admin/views/FMViewSubmissions_fmc.php";
    $view = new FMViewSubmissions_fmc($model);
    $id = ((isset($_POST['current_id']) && esc_html($_POST['current_id']) != '') ? (int) $_POST['current_id'] : 0);
			
    $form_id = (int)$wpdb->get_var("SELECT form_id FROM " . $wpdb->prefix . "formmaker_submits WHERE group_id='" . $id . "'");	
    $form = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "formmaker WHERE id='" . $form_id . "'");

    if (isset($form->form)) {
      $old = TRUE;
    }
    else {
      $old = FALSE;
    }

    if ($old == FALSE || ($old == TRUE && $form->form == '')) {
      $view->new_edit($id);
    }
    else {
      $view->edit($id);
    }
  }
  
  public function save() {
    $form_id = ((isset($_POST['form_id']) && esc_html($_POST['form_id']) != '') ? (int)esc_html($_POST['form_id']) : 0);
    $this->save_db();
    $this->display($form_id);
  }

  public function apply() {
    $this->save_db();
    $this->edit();
  }  
  
  public function save_db() {
    global $wpdb;
    $id = (isset($_POST['current_id']) ? (int) esc_html(stripslashes( $_POST['current_id'])) : 0);
	$group_id = $id;
    $date = esc_html($_POST['date']);
    $ip = esc_html($_POST['ip']);
    $form_id = (int)$wpdb->get_var("SELECT form_id FROM " . $wpdb->prefix . "formmaker_submits WHERE group_id='" . $id . "'");
    $form = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "formmaker WHERE id='" . $form_id . "'");
    $label_id = array();
    $label_order_original = array();
    $label_type = array();
	$old = false;
	$form_currency = '$';
	
    if (strpos($form->label_order, 'type_paypal_')) {
      $form->label_order = $form->label_order."0#**id**#Payment Status#**label**#type_paypal_payment_status#****#";
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
	
	if(isset($form->form))
		$old = true;
	
    if($old == false || ($old == true && $form->form=='')) {

		foreach($label_type as $key => $type) {
		
			$value='';
			if($type=="type_submit_reset" or $type=="type_map" or $type=="type_editor" or  $type=="type_captcha" or  $type=="type_recaptcha" or  $type=="type_button" or $type=="type_paypal_total")
				continue;

			$i=$label_id[$key];
			$id = 'form_id_temp';	
			switch ($type) {
			
				case 'type_text':
				case 'type_password':
				case 'type_textarea':
				case "type_submitter_mail":
				case "type_date":
				case "type_own_select":					
				case "type_country":				
				case "type_number":	{
					$value = (isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : NULL);
					break;
				}
				case "type_wdeditor": {
				    $value = (isset($_POST['wdform_'.$i.'_wd_editor'.$id]) ? $_POST['wdform_'.$i.'_wd_editor'.$id] : NULL);
				    break;
				}
				case "type_mark_map": {	
				    $long = (isset($_POST['wdform_'.$i."_long".$id]) ? $_POST['wdform_'.$i."_long".$id] : NULL);
				    $lat = (isset($_POST['wdform_'.$i."_lat".$id]) ? $_POST['wdform_'.$i."_lat".$id] : NULL);
					    if($long and $lat)
					        $value = $long . '***map***' . $lat;

					break;
				}
									
				case "type_date_fields": {
				    $day = (isset($_POST['wdform_'.$i."_day".$id]) ? $_POST['wdform_'.$i."_day".$id] : NULL);
				    $month = (isset($_POST['wdform_'.$i."_month".$id]) ? $_POST['wdform_'.$i."_month".$id] : NULL);
					$year = (isset($_POST['wdform_'.$i."_year".$id]) ? $_POST['wdform_'.$i."_year".$id] : NULL);
					    if($day or $month or $year)   
							$value = $day . '-' . $month . '-' . $year;
					break;
				}
				
				case "type_time": {
					$ss = (isset($_POST['wdform_'.$i."_ss".$id]) ? $_POST['wdform_'.$i."_ss".$id] : NULL);
					$hh = (isset($_POST['wdform_'.$i."_hh".$id]) ? $_POST['wdform_'.$i."_hh".$id] : NULL);
					$mm = (isset($_POST['wdform_'.$i."_mm".$id]) ? $_POST['wdform_'.$i."_mm".$id] : NULL);					
					
					if(isset($ss))
						$value = $hh .':'. $mm .':' . $ss;
					else 
						$value = $hh .':' . $mm;
						
					$am_pm = (isset($_POST['wdform_'.$i."_am_pm".$id]) ? $_POST['wdform_'.$i."_am_pm".$id] : NULL);
					if(isset($am_pm))
						$value = $value . ' ' . $am_pm;
						
					break;
				}
				
				case "type_phone": {
				    $first = (isset($_POST['wdform_'.$i."_element_first".$id]) ? $_POST['wdform_'.$i."_element_first".$id] : NULL);
					$last = (isset($_POST['wdform_'.$i."_element_last".$id]) ? $_POST['wdform_'.$i."_element_last".$id] : NULL);
					    if($first or $last)
					        $value = $first . ' ' . $last;	
					
					break;
				}
	
				case "type_name": {
			
					$element_title = (isset($_POST['wdform_'.$i."_element_title".$id]) ? $_POST['wdform_'.$i."_element_title".$id] : NULL);
					$element_first = (isset($_POST['wdform_'.$i."_element_first".$id]) ? $_POST['wdform_'.$i."_element_first".$id] : NULL);
					$element_last = (isset($_POST['wdform_'.$i."_element_last".$id]) ? $_POST['wdform_'.$i."_element_last".$id] : NULL);
					$element_middle = (isset($_POST['wdform_'.$i."_element_middle".$id]) ? $_POST['wdform_'.$i."_element_middle".$id] : NULL);
					
					if(isset($element_title))
						$value = $element_title . '@@@' . $element_first . '@@@' . $element_last . '@@@' . $element_middle;
					else
						$value = $element_first . '@@@' . $element_last;
						
					break;
				}
	
				case "type_file_upload": {					
					break;
				}
				
				case 'type_address': {
					$value='*#*#*#';
					$element = (isset($_POST['wdform_'.$i."_street1".$id]) ? $_POST['wdform_'.$i."_street1".$id] : NULL);
					if($element) {
						$value = $element;
						break;
					}
					
					$element = (isset($_POST['wdform_'.$i."_street2".$id]) ? $_POST['wdform_'.$i."_street2".$id] : NULL); 
					if($element) {
						$value = $element;
						break;
					}
					
					$element = (isset($_POST['wdform_'.$i."_city".$id]) ? $_POST['wdform_'.$i."_city".$id] : NULL); 
					if(isset($element)) {
						$value = $element;
						break;
					}
					
					$element = (isset($_POST['wdform_'.$i."_state".$id]) ? $_POST['wdform_'.$i."_state".$id] : NULL);
					if(isset($element)) {
						$value = $element;
						break;
					}
					
					$element = (isset($_POST['wdform_'.$i."_postal".$id]) ? $_POST['wdform_'.$i."_postal".$id] : NULL);
					if(isset($element)) {
						$value = $element;
						break;
					}
					
					$element = (isset($_POST['wdform_'.$i."_country".$id]) ? $_POST['wdform_'.$i."_country".$id] : NULL);
					if(isset($element)) {
						$value = $element;
						break;
					}
					
					break;
				}
				
				case "type_hidden": {
					$value = (isset($_POST[$label_order_original[$key]]) ? $_POST[$label_order_original[$key]] : NULL); 
					break;
				}
				
				case "type_radio": {
					$element = (isset($_POST['wdform_'.$i."_other_input".$id]) ? $_POST['wdform_'.$i."_other_input".$id] : NULL);
					if(isset($element)) {
						$value = $element;	
						break;
					}
					
					$value = (isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : NULL);
					break;
				}
				
				case "type_checkbox": {
					$start=-1;
					$value='';
					for($j=0; $j<100; $j++) {
					
						$element = (isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL);
		
						if(isset($element)) {
							$start = $j;
							break;
						}
					}
						
					$other_element_id=-1;
					$is_other = (isset($_POST['wdform_'.$i."_allow_other".$id]) ? $_POST['wdform_'.$i."_allow_other".$id] : NULL);
					if($is_other == "yes") {
						$other_element_id = (isset($_POST['wdform_'.$i."_allow_other_num".$id]) ? $_POST['wdform_'.$i."_allow_other_num".$id] : NULL);
					}
					
					if($start!=-1) {
						for($j=$start; $j<100; $j++) {
							$element = (isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL);
							if(isset($element))
							if($j == $other_element_id) {
								$value = $value . (isset($_POST['wdform_'.$i."_other_input".$id]) ? $_POST['wdform_'.$i."_other_input".$id] : NULL) . '***br***';
							}
							else	
								$value = $value . (isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL) . '***br***';
						}
					}
					
					break;
				}
				
				case "type_paypal_price": {		
					$value=0;
					if((isset($_POST['wdform_'.$i."_element_dollars".$id]) ? $_POST['wdform_'.$i."_element_dollars".$id] : NULL)) 
						$value = (isset($_POST['wdform_'.$i."_element_dollars".$id]) ? $_POST['wdform_'.$i."_element_dollars".$id] : NULL);
						
					$value = (int) preg_replace('/\D/', '', $value); 
					
					if((isset($_POST['wdform_'.$i."_element_cents".$id]) ? $_POST['wdform_'.$i."_element_cents".$id] : NULL))
						$value = $value . '.' . ( preg_replace('/\D/', '', (isset($_POST['wdform_'.$i."_element_cents".$id]) ? $_POST['wdform_'.$i."_element_cents".$id] : NULL)));
					
					
					$value = $value; 
					break;
				}			
				
				case "type_paypal_select": {	
		
					if((isset($_POST['wdform_'.$i."_element_label".$id]) ? $_POST['wdform_'.$i."_element_label".$id] : NULL)) 
						$value = (isset($_POST['wdform_'.$i."_element_label".$id]) ? $_POST['wdform_'.$i."_element_label".$id] : NULL) . ' : ' . (isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : NULL) . $form_currency; 
					else
						$value = '';
					
					$element_quantity = (isset($_POST['wdform_'.$i."element_quantity".$id]) ? $_POST['wdform_'.$i."element_quantity".$id] : NULL);
					if(isset($element_quantity) && $value!='')
						$value .= '***br***' . (isset($_POST['wdform_'.$i."_element_quantity_label".$id]) ? $_POST['wdform_'.$i."_element_quantity_label".$id] : NULL) . ': ' . (isset($_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : NULL);
					
					
					for($k=0; $k<50; $k++) {
						$temp_val = (isset($_POST['wdform_'.$i."_property".$id.$k]) ? $_POST['wdform_'.$i."_property".$id.$k] : NULL);
						if(isset($temp_val) && $value!='') {			
							$value .= '***br***' . (isset($_POST['wdform_'.$i."_element_property_label".$id]) ? $_POST['wdform_'.$i."_element_property_label".$id] : NULL) . ': ' . (isset($_POST['wdform_'.$i."_property".$id.$k]) ? $_POST['wdform_'.$i."_property".$id.$k] : NULL) . '***property***';
						}
					}
					
					break;
				}
				
				case "type_paypal_radio": { 
					
					if((isset($_POST['wdform_'.$i."_element_label".$id]) ? $_POST['wdform_'.$i."_element_label".$id] : NULL)) 
						$value = (isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : NULL) . ' : ' . (isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : NULL) . $form_currency;
					else
						$value=''; 

					
					$element_quantity = (isset($_POST['wdform_'.$i."element_quantity".$id]) ? $_POST['wdform_'.$i."element_quantity".$id] : NULL);
					if(isset($element_quantity) && $value!='') 
						$value .= '***br***' . (isset($_POST['wdform_'.$i."_element_quantity_label".$id]) ? $_POST['wdform_'.$i."_element_quantity_label".$id] : NULL) . ': ' . (isset($_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : NULL) . '***quantity***';
					

					for($k=0; $k<50; $k++) { 
						$temp_val = (isset($_POST['wdform_'.$i."_property".$id.$k]) ? $_POST['wdform_'.$i."_property".$id.$k] : NULL);
						if(isset($temp_val) && $value!='') {			
							
							$value .= '***br***' . (isset($_POST['wdform_'.$i."_element_property_label".$id]) ? $_POST['wdform_'.$i."_element_property_label".$id] : NULL) . ': ' . (isset($_POST['wdform_'.$i."_property".$id.$k]) ? $_POST['wdform_'.$i."_property".$id.$k] : NULL) . '***property***';
						}
					}
				
					break;
				}

				case "type_paypal_shipping": {
					
					if((isset($_POST['wdform_'.$i."_element_label".$id]) ? $_POST['wdform_'.$i."_element_label".$id] : NULL)) 
						$value = (isset($_POST['wdform_'.$i."_element_label".$id]) ? $_POST['wdform_'.$i."_element_label".$id] : NULL) . ' : ' . (isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : NULL) . $form_currency;
					else
						$value=''; 
					$value = (isset($_POST['wdform_'.$i."_element_label".$id]) ? $_POST['wdform_'.$i."_element_label".$id] : NULL) . ' - ' . (isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : NULL);
					
					$paypal['shipping'] = (isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : NULL);

					break;
				}

				case "type_paypal_checkbox": {
					$start=-1;
					$value='';
					for($j=0; $j<100; $j++) {
					
						$element = (isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL);
		
						if(isset($element)) {
							$start=$j;
							break;
						}
					}
					
					$other_element_id = -1;
					$is_other = (isset($_POST['wdform_'.$i."_allow_other".$id]) ? $_POST['wdform_'.$i."_allow_other".$id] : NULL);
					if($is_other=="yes") {
						$other_element_id = (isset($_POST['wdform_'.$i."_allow_other_num".$id]) ? $_POST['wdform_'.$i."_allow_other_num".$id] : NULL);
					}
					
					if($start!=-1) {
						for($j=$start; $j<100; $j++) {
							$element = (isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL);
							if(isset($element))
							if($j==$other_element_id) {
								$value = $value . (isset($_POST['wdform_'.$i."_other_input".$id]) ? $_POST['wdform_'.$i."_other_input".$id] : NULL) . '***br***'; 
								
							}
							else { 
							  
								$value = $value . (isset($_POST['wdform_'.$i."_element".$id.$j."_label"]) ? $_POST['wdform_'.$i."_element".$id.$j."_label"] : NULL) . ' - ' . ((isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL) == '' ? '0' : (isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL)) . $form_currency . '***br***';
							
							}
						}
						
						$element_quantity = (isset($_POST['wdform_'.$i."element_quantity".$id]) ? $_POST['wdform_'.$i."element_quantity".$id] : NULL);
						if(isset($element_quantity)) 
							$value .= (isset($_POST['wdform_'.$i."_element_quantity_label".$id]) ? $_POST['wdform_'.$i."_element_quantity_label".$id] : NULL) . ': '. (isset($_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : NULL) . '***quantity***';
						
						for($k=0; $k<50; $k++) { 
							$temp_val = (isset($_POST['wdform_'.$i."_property".$id.$k]) ? $_POST['wdform_'.$i."_property".$id.$k] : NULL);
							if(isset($temp_val)) {								                             
							
								$value .= '***br***' . (isset($_POST['wdform_'.$i."_element_property_label".$id]) ? $_POST['wdform_'.$i."_element_property_label".$id] : NULL) . ': ' . (isset($_POST['wdform_'.$i."_property".$id.$k]) ? $_POST['wdform_'.$i."_property".$id.$k] : NULL) . '***property***';
							}
						}
						
					}
					
					
					break;
				}
				
				case "type_star_rating": { 
				
					if((isset($_POST['wdform_'.$i."_selected_star_amount".$id]) ? $_POST['wdform_'.$i."_selected_star_amount".$id] : NULL) == "")
					  $selected_star_amount = 0;   
					else {
					  $selected_star_amount = (isset($_POST['wdform_'.$i."_selected_star_amount".$id]) ? $_POST['wdform_'.$i."_selected_star_amount".$id] : NULL);
					}
					$value = $selected_star_amount . '/' . (isset($_POST['wdform_'.$i."_star_amount".$id]) ? $_POST['wdform_'.$i."_star_amount".$id] : NULL); 		 							
					break;
				}
			
				case "type_scale_rating": { 
																
					$value = (isset($_POST['wdform_'.$i."_scale_radio".$id]) ? $_POST['wdform_'.$i."_scale_radio".$id] : 0) . '/' .	(isset($_POST['wdform_'.$i."_scale_amount".$id]) ? $_POST['wdform_'.$i."_scale_amount".$id] : NULL);								
					break;
				}
				
				case "type_spinner": { 
					$value = (isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : NULL);		
				
					break;
				}
				
				case "type_slider": {
					$value = (isset($_POST['wdform_'.$i."_slider_value".$id]) ? $_POST['wdform_'.$i."_slider_value".$id] : NULL);		
				
					break;
				}
				case "type_range": { 
					$value = (isset($_POST['wdform_'.$i."_element".$id.'0']) ? $_POST['wdform_'.$i."_element".$id.'0'] : NULL) . '-' . (isset($_POST['wdform_'.$i."_element".$id.'1']) ? $_POST['wdform_'.$i."_element".$id.'1'] : NULL);	
				
					break;
				}
				case "type_grading": {
					$value ="";
					$items = explode(":",(isset($_POST['wdform_'.$i."_hidden_item".$id]) ? $_POST['wdform_'.$i."_hidden_item".$id] : NULL)); 
					for($k=0; $k<sizeof($items)-1; $k++)
					    $value .= (isset($_POST['wdform_'.$i."_element".$id.'_'.$k]) ? $_POST['wdform_'.$i."_element".$id.'_'.$k] : NULL) . ':';
						
					$value .= (isset($_POST['wdform_'.$i."_hidden_item".$id]) ? $_POST['wdform_'.$i."_hidden_item".$id] : NULL) . '***grading***';
			
					break;
				}
				
				case "type_matrix": {
				
					$rows_of_matrix = explode("***",(isset($_POST['wdform_'.$i."_hidden_row".$id]) ? $_POST['wdform_'.$i."_hidden_row".$id] : NULL)); 
					$rows_count = sizeof($rows_of_matrix)-1;
					$column_of_matrix = explode("***",(isset($_POST['wdform_'.$i."_hidden_column".$id]) ? $_POST['wdform_'.$i."_hidden_column".$id] : NULL));
					$columns_count = sizeof($column_of_matrix)-1;   
					                                           
				
					if((isset($_POST['wdform_'.$i."_input_type".$id]) ? $_POST['wdform_'.$i."_input_type".$id] : NULL) == "radio") { 
						$input_value="";                         

						for($k=1; $k<=$rows_count; $k++)
						$input_value .= (isset($_POST['wdform_'.$i."_input_element".$id.$k]) ? $_POST['wdform_'.$i."_input_element".$id.$k] : 0) ."***"; 
						
					}
					if((isset($_POST['wdform_'.$i."_input_type".$id]) ? $_POST['wdform_'.$i."_input_type".$id] : NULL) == "checkbox") { 
						$input_value="";
						
						for($k=1; $k<=$rows_count; $k++) 
						for($j=1; $j<=$columns_count; $j++)
						$input_value .= (isset($_POST['wdform_'.$i."_input_element".$id.$k.'_'.$j]) ? $_POST['wdform_'.$i."_input_element".$id.$k.'_'.$j] : 0) . "***";
					}
					
					if((isset($_POST['wdform_'.$i."_input_type".$id]) ? $_POST['wdform_'.$i."_input_type".$id] : NULL) == "text") { 
						$input_value="";
						for($k=1; $k<=$rows_count; $k++)
						for($j=1; $j<=$columns_count; $j++) 
						$input_value .= (isset($_POST['wdform_'.$i."_input_element".$id.$k.'_'.$j]) ? $_POST['wdform_'.$i."_input_element".$id.$k.'_'.$j] : NULL) . "***";
					}
					
					if((isset($_POST['wdform_'.$i."_input_type".$id]) ? $_POST['wdform_'.$i."_input_type".$id] : NULL) == "select") { 
						$input_value="";
						for($k=1; $k<=$rows_count; $k++) 
						for($j=1; $j<=$columns_count; $j++)
						$input_value .= (isset($_POST['wdform_'.$i."_select_yes_no".$id.$k.'_'.$j]) ? $_POST['wdform_'.$i."_select_yes_no".$id.$k.'_'.$j] : NULL) . "***";	
					}
										                                                                                           					
					$value = $rows_count . (isset($_POST['wdform_'.$i."_hidden_row".$id]) ? $_POST['wdform_'.$i."_hidden_row".$id] : NULL) . '***' . $columns_count . (isset($_POST['wdform_'.$i."_hidden_column".$id]) ? $_POST['wdform_'.$i."_hidden_column".$id] : NULL) . '***' . (isset($_POST['wdform_'.$i."_input_type".$id]) ? $_POST['wdform_'.$i."_input_type".$id] : NULL) . '***' . $input_value . '***matrix***';	
				
					break;
				}
				
			}

			if($type == "type_address")
				if($value == '*#*#*#')
					continue;

	
			if($value) {
				$query = "SELECT id FROM " . $wpdb->prefix . "formmaker_submits WHERE group_id='" . $group_id . "' AND element_label='" . $i . "'";
				$result = $wpdb->get_var($query);
															
				if($result) {					
					$save = $wpdb->update($wpdb->prefix . "formmaker_submits", array(
					  'element_value' => stripslashes($value),
					), array(
					  'group_id' => $group_id,
					  'element_label' => $i
					), array(
					  '%s',
					), array(
					  '%d',
					  '%s'
					));															
				}
				else {
					
					$save = $wpdb->insert($wpdb->prefix . "formmaker_submits", array(
					  'form_id' => $form_id,
					  'element_label' => $i,
					  'element_value' => stripslashes($value),
					  'group_id' => $group_id,
					  'date' => $date,
					  'ip' => $ip
					 ), array(
						 '%d',
						 '%s',
						 '%s',
						 '%d',
						 '%s',
						 '%s'
						)
					);
					
					
				}
			}

	
			
		}
	}
	
    else {	
	
		foreach ($label_id as $key => $label_id_1) {
		  $element_value = (isset($_POST["submission_" . $label_id_1]) ? esc_html(stripslashes($_POST["submission_" . $label_id_1])) : " ");
		  if (isset($_POST["submission_" . $label_id_1])) {
			$query = "SELECT id FROM " . $wpdb->prefix . "formmaker_submits WHERE group_id='" . $id . "' AND element_label='" . $label_id_1 . "'";
			$result = $wpdb->get_var($query);
			if ($label_type[$key] == 'type_file_upload')
			  if ($element_value)
				$element_value = $element_value . "*@@url@@*";
			  if ($result) {
				$save = $wpdb->update($wpdb->prefix . "formmaker_submits", array(
				  'element_value' => stripslashes($element_value),
				), array(
				  'group_id' => $id,
				  'element_label' => $label_id_1
				), array(
				  '%s',
				), array(
				  '%d',
				  '%s'
				)); 
			  }
			  else {
				$save = $wpdb->insert($wpdb->prefix . "formmaker_submits", array(
				  'form_id' => $form_id,
				  'element_label' => $label_id_1,
				  'element_value' => stripslashes($element_value),
				  'group_id' => $id,
				  'date' => $date,
				  'ip' => $ip
				 ), array(
					 '%d',
					 '%s',
					 '%s',
					 '%d',
					 '%s',
					 '%s'
					)
				);
			  }
		  }
		  else {
			$element_value_ch = (isset($_POST["submission_" . $label_id_1 . '_0']) ? esc_html(stripslashes($_POST["submission_" . $label_id_1 . '_0'])) : " ");
			if (isset($_POST["submission_" . $label_id_1 . '_0'])) {
			  for ($z = 0; $z < 21; $z++) {
				$element_value_ch = $_POST["submission_" . $label_id_1 . '_' . $z];
				if (isset($element_value_ch))
				  $element_value = $element_value . $element_value_ch . '***br***';
				else
				  break;
			  }
			  $query = "SELECT id FROM " . $wpdb->prefix . "formmaker_submits WHERE group_id='" . $id . "' AND element_label='" . $label_id_1 . "'";
			  $result = $wpdb->get_var($query);
			  if ($result) {
				$query = "UPDATE " . $wpdb->prefix . "formmaker_submits SET `element_value`='" . stripslashes($element_value) . "' WHERE group_id='" . $id . "' AND element_label='" . $label_id_1 . "'";
				$save = $wpdb->update($wpdb->prefix . "formmaker_submits", array(
					'element_value' => stripslashes($element_value),
				), array(
				  'group_id' => $id,
				  'element_label' => $label_id_1
				), array(
				  '%s',
				), array(
				  '%d',
				  '%s'
				)); 
			  }
			  else {
				$query = "INSERT INTO " . $wpdb->prefix . "formmaker_submits (form_id, element_label, element_value, group_id, date, ip) VALUES('" . $form_id . "', '" . $label_id_1 . "', '" . stripslashes($element_value) . "','" . $id . "', '" . $date . "', '" . $ip . "')";
				$save = $wpdb->insert($wpdb->prefix . "formmaker_submits", array(
					'form_id' => $form_id,
					'element_label' => $label_id_1,
					'element_value' => stripslashes($element_value),
					'group_id' => $id,
					'date' => $date,
					'ip' => $ip
				  ), array(
					 '%d',
					 '%s',
					 '%s',
					 '%d',
					 '%s',
					 '%s'
					)
				);
			  }
			}
		  }
		}		
	}
    if ($save !== FALSE) {
      echo WDW_FMC_Library::message('Submission Succesfully Saved.', 'updated');
    }
    else {
      echo WDW_FMC_Library::message('Error. Please install plugin again.', 'error');
    }
  }
  
  public function delete($id) {
    global $wpdb;
    $form_id = ((isset($_POST['form_id']) && esc_html($_POST['form_id']) != '') ? (int)esc_html($_POST['form_id']) : 0);	    
    $query = $wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'formmaker_submits WHERE group_id="%d"', $id);
    // $elements_col = $wpdb->get_col($wpdb->prepare('SELECT element_value FROM ' . $wpdb->prefix . 'formmaker_submits WHERE group_id="%d"', $id));
    if ($wpdb->query($query)) {
      // foreach ($elements_col as $element_value) {
        // $destination = str_replace(site_url() . '/', '', $element_value);
        // $destination = str_replace('*@@url@@*', '', $destination);
        // if ($destination) {
          // $destination = ABSPATH . $destination;
          // if (file_exists($destination)) {
             // unlink($destination);
          // }
        // }
      // }
      echo WDW_FMC_Library::message('Item Succesfully Deleted.', 'updated');
    }
    else {
      echo WDW_FMC_Library::message('Error. Please install plugin again.', 'error');
    }    
    $this->display($form_id);
  }
  
  public function delete_all() {
    global $wpdb;
    $form_id = ((isset($_POST['form_id']) && esc_html($_POST['form_id']) != '') ? esc_html($_POST['form_id']) : 0);	
    $cid = ((isset($_POST['post']) && $_POST['post'] != '') ? $_POST['post'] : NULL); 
    if (count($cid)) {
	  array_walk($cid, create_function('&$value', '$value = (int)$value;')); 
      $cids = implode(',', $cid);
      $query = 'DELETE FROM ' . $wpdb->prefix . 'formmaker_submits WHERE group_id IN ( ' . $cids . ' )';
      // $elements_col = $wpdb->get_col('SELECT element_value FROM ' . $wpdb->prefix . 'formmaker_submits WHERE group_id IN ( ' . $cids . ' )');
      if ($wpdb->query($query)) {
        // foreach ($elements_col as $element_value) {
          // $destination = str_replace(site_url() . '/', '', $element_value);
          // $destination = str_replace('*@@url@@*', '', $destination);
          // if ($destination) {
            // $destination = ABSPATH . $destination;
            // if (file_exists($destination)) {
               // unlink($destination);
            // }
          // }
        // }
        echo WDW_FMC_Library::message('Items Succesfully Deleted.', 'updated');
      }
      else {
        echo WDW_FMC_Library::message('Error. Please install plugin again.', 'error');
      }
    }
    else {
      echo WDW_FMC_Library::message('You must select at least one item.', 'error');
    }
    $this->display($form_id);
  }

  public function block_ip() {
    global $wpdb;
    $flag = FALSE;
    $form_id = ((isset($_POST['form_id']) && esc_html($_POST['form_id']) != '') ? (int)esc_html($_POST['form_id']) : 0);	
    $cid = ((isset($_POST['post']) && $_POST['post'] != '') ? $_POST['post'] : NULL); 
    if (count($cid)) {
	  array_walk($cid, create_function('&$value', '$value = (int)$value;'));
      $cids = implode(',', $cid);
      $query = 'SELECT * FROM ' . $wpdb->prefix . 'formmaker_submits WHERE group_id IN ( '. $cids .' )';
      $rows = $wpdb->get_results($query);
			foreach ($rows as $row) {
				$ips = $wpdb->get_var($wpdb->prepare('SELECT ip FROM ' . $wpdb->prefix . 'formmaker_blocked WHERE ip="%s"', $row->ip));
        $flag = TRUE;
				if (!$ips) {
          $save = $wpdb->insert($wpdb->prefix . 'formmaker_blocked', array(
            'ip' => $row->ip,
          ), array(
            '%s',
          ));
				}
			}
    }
    if ($flag) {
      echo WDW_FMC_Library::message('IPs Succesfully Blocked.', 'updated');
    }
    else {
      echo WDW_FMC_Library::message('You must select at least one item.', 'error');
    }
    $this->display($form_id);
  }

  public function unblock_ip() {
    global $wpdb;
    $flag = FALSE;
    $form_id = ((isset($_POST['form_id']) && esc_html($_POST['form_id']) != '') ? (int)esc_html($_POST['form_id']) : 0);	
    $cid = ((isset($_POST['post']) && $_POST['post'] != '') ? $_POST['post'] : NULL); 
    if (count($cid)) {
	  array_walk($cid, create_function('&$value', '$value = (int)$value;')); 
      $cids = implode(',', $cid);
      $query = 'SELECT * FROM ' . $wpdb->prefix . 'formmaker_submits WHERE group_id IN ( '. $cids .' )';
      $rows = $wpdb->get_results($query);
			foreach ($rows as $row) {
        $flag = TRUE;
				$ips = $wpdb->get_var($wpdb->prepare('SELECT ip FROM ' . $wpdb->prefix . 'formmaker_blocked WHERE ip="%s"', $row->ip));
				if ($ips) {
          $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'formmaker_blocked WHERE ip="%s"', $ips));
				}
			}
    }
    if ($flag) {
      echo WDW_FMC_Library::message('IPs Succesfully Unblocked.', 'updated');
    }
    else {
      echo WDW_FMC_Library::message('You must select at least one item.', 'error');
    }
    $this->display($form_id);
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