<?php

class FMViewForm_maker_fmc {
  ////////////////////////////////////////////////////////////////////////////////////////
  // Events                                                                             //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Constants                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Variables                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
  private $model;


  ////////////////////////////////////////////////////////////////////////////////////////
  // Constructor & Destructor                                                           //
  ////////////////////////////////////////////////////////////////////////////////////////
  public function __construct($model) {
    $this->model = $model;
  }
  ////////////////////////////////////////////////////////////////////////////////////////
  // Public Methods                                                                     //
  ////////////////////////////////////////////////////////////////////////////////////////
  public function display($id) {
	$fmc_settings = get_option('fmc_settings');
    $current_url=htmlentities($_SERVER['REQUEST_URI']);
    $form_maker_front_end = "";
	$pattern = '/\/\/(.+)(\r\n|\r|\n)/';
    $result = $this->model->showform($id);
    if (!$result) {
      return;
    }
    $ok = $this->model->savedata($result[0], $id);
    if (is_numeric($ok)) {
      $this->model->remove($ok);
    }
    $row = $result[0];
    $label_id = $result[2];
    $label_type = $result[3];
    $form_theme = $result[4];
    
    $old = FALSE;
    if (isset($_SESSION['form_submit_type' . $id])) {
      $type_and_id = $_SESSION['form_submit_type' . $id];
      $type_and_id = explode(',', $type_and_id);
      $form_get_type = $type_and_id[0];
      $form_get_id = isset($type_and_id[1]) ? $type_and_id[1] : '';
      $_SESSION['form_submit_type' . $id] = 0;
      if ($form_get_type == 3) {
        $_SESSION['massage_after_submit' . $id] = "";
        $after_submission_text = $this->model->get_after_submission_text($form_get_id);
        require_once(WD_FMC_DIR . '/framework/WDW_FMC_Library.php');
        $form_maker_front_end .=  WDW_FMC_Library::message(wpautop(html_entity_decode($after_submission_text)), 'warning', $id);
        return $form_maker_front_end;
      }
    }
    if (isset($_SESSION['redirect_paypal' . $id]) && ($_SESSION['redirect_paypal' . $id] == 1)) {
      $_SESSION['redirect_paypal' . $id] = 0;
      if (isset($_GET['succes'])) {
        require_once(WD_FMC_DIR . '/framework/WDW_FMC_Library.php');
        if ($_GET['succes'] == 0) {
          $form_maker_front_end .=  WDW_FMC_Library::message(__('Error, email was not sent.', 'form_maker'), 'error', $id);
        }
        else {
          $form_maker_front_end .=  WDW_FMC_Library::message(__('Your form was successfully submitted.', 'form_maker'), 'warning', $id);
        }
      }
    }
    elseif (isset($_SESSION['massage_after_submit' . $id]) && $_SESSION['massage_after_submit' . $id] != "") {
      $message = $_SESSION['massage_after_submit' . $id];
      $_SESSION['massage_after_submit' . $id] = "";        
      if ($_SESSION['error_or_no' . $id]) {
        $error = 'error';
      }
      else {
        $error = 'warning';
      }
      require_once(WD_FMC_DIR . '/framework/WDW_FMC_Library.php');
      $form_maker_front_end .=  WDW_FMC_Library::message($message, $error, $id);
    }
    
    if (isset($_SESSION['show_submit_text' . $id])) {
      if ($_SESSION['show_submit_text' . $id] == 1) {
        $_SESSION['show_submit_text' . $id] = 0;
        $form_maker_front_end .= $row->submit_text;
        return;
      }
    }
    $this->model->increment_views_count($id);
    if (isset($row->form)) {
			$old = TRUE;
    }
    $article = $row->article_id;
      
      $form_maker_front_end .= '<script type="text/javascript">' . preg_replace($pattern, ' ', $row->javascript) . '</script>';
      $new_form_theme = explode('{', $form_theme);
      $count_after_explod_theme = count($new_form_theme);
      for ($i = 0; $i < $count_after_explod_theme; $i++) {
        $body_or_classes[$i] = explode('}', $new_form_theme[$i]);
      }
      for ($i = 0; $i < $count_after_explod_theme; $i++) {
        if ($i == 0) {
          $body_or_classes[$i][0] = ".form" . $id . ' ' . str_replace(',', ", .form" . $id, $body_or_classes[$i][0]);
        }
        else {
          $body_or_classes[$i][1] = ".form" . $id . ' ' . str_replace(',', ", .form" . $id, $body_or_classes[$i][1]);
        }
      }
      for ($i = 0; $i < $count_after_explod_theme; $i++) {
        $body_or_classes_implode[$i] = implode('}', $body_or_classes[$i]);
      }
      $form_theme = implode('{', $body_or_classes_implode);
	  $form_theme = preg_replace($pattern, ' ', $form_theme);
      $form_maker_front_end .= '<style>' . str_replace('[SITE_ROOT]', WD_FMC_URL, $form_theme) . '</style>';
      wp_print_scripts('main' . (($old == false || ($old == true && $row->form=='')) ? '_div' : '') . '_front_end', WD_FMC_URL . '/js/main' . (($old == false || ($old == true && $row->form=='')) ? '_div' : '') . '_front_end.js?ver='. get_option("wd_form_maker_version"));
      $form_currency = '$';
      $check_js = '';
      $onload_js = '';
      $onsubmit_js = '';
      $currency_code = array('USD', 'EUR', 'GBP', 'JPY', 'CAD', 'MXN', 'HKD', 'HUF', 'NOK', 'NZD', 'SGD', 'SEK', 'PLN', 'AUD', 'DKK', 'CHF', 'CZK', 'ILS', 'BRL', 'TWD', 'MYR', 'PHP', 'THB');
      $currency_sign = array('$', '&#8364;', '&#163;', '&#165;', 'C$', 'Mex$', 'HK$', 'Ft', 'kr', 'NZ$', 'S$', 'kr', 'zl', 'A$', 'kr', 'CHF', 'Kc', '&#8362;', 'R$', 'NT$', 'RM', '&#8369;', '&#xe3f;');
      if ($row->payment_currency) {
        $form_currency =	$currency_sign[array_search($row->payment_currency, $currency_code)];
      }
      $form_paypal_tax = $row->tax;
      $form_maker_front_end .= '<form name="form' . $id . '" action="' . $current_url . '" method="post" id="form' . $id . '" class="form' . $id . '" enctype="multipart/form-data"  onsubmit="check_required(\'submit\', \'' . $id . '\'); return false;">
      <div id="' . $id . 'pages" class="wdform_page_navigation" show_title="' . $row->show_title . '" show_numbers="' . $row->show_numbers . '" type="' . $row->pagination . '"></div>
      <input type="hidden" id="counter' . $id . '" value="' . $row->counter . '" name="counter' . $id . '" />
      <input type="hidden" id="Itemid' . $id . '" value="" name="Itemid' . $id . '" />';
    if ($old == FALSE || ($old == TRUE && $row->form == '')) {
      $is_type = array();
      $id1s = array();
      $types = array();
      $labels = array();
      $paramss = array();
      $required_sym = $row->requiredmark;
      $fields = explode('*:*new_field*:*', $row->form_fields);
      $fields = array_slice($fields,0, count($fields) - 1);   
      foreach ($fields as $field) {
        $temp = explode('*:*id*:*', $field);
        array_push($id1s, $temp[0]);
        $temp = explode('*:*type*:*', $temp[1]);
        array_push($types, $temp[0]);
        $temp = explode('*:*w_field_label*:*', $temp[1]);
        array_push($labels, $temp[0]);
        array_push($paramss, $temp[1]);
      }
      $form_id = $id;	
      
      $show_hide	= array();
	$field_label	= array();
	$all_any 	= array();
	$condition_params 	= array();
	$type_and_id = array();

	$condition_js='';
	if($row->condition!="")
	{
		$conditions=explode('*:*new_condition*:*',$row->condition);
		$conditions 	= array_slice($conditions,0, count($conditions)-1); 
		$count_of_conditions = count($conditions);					

		foreach($conditions as $condition)
		{
			$temp=explode('*:*show_hide*:*',$condition);
			array_push($show_hide, $temp[0]);
			$temp=explode('*:*field_label*:*',$temp[1]);
			array_push($field_label, $temp[0]);
			$temp=explode('*:*all_any*:*',$temp[1]);
			array_push($all_any, $temp[0]);
			array_push($condition_params, $temp[1]);
		}

		foreach($id1s as $id1s_key => $id1)
		{	
			$type_and_id[$id1]=$types[$id1s_key];
		}
		
		
		for($k=0; $k<$count_of_conditions; $k++)
		{	
			if($show_hide[$k])
			{
				$display = 'removeAttr("style")';
				$display_none = 'css("display", "none")';
			}
			else
			{
				$display = 'css("display", "none")';
				$display_none = 'removeAttr("style")';
			}

			if($all_any[$k]=="and")
				$or_and = '&&';
			else
				$or_and = '||';
				
		
				if($condition_params[$k])
				{
					$cond_params =explode('*:*next_condition*:*',$condition_params[$k]);
					$cond_params 	= array_slice($cond_params,0, count($cond_params)-1); 
					for($l=0; $l<count($cond_params); $l++)
					{
						$params_value = explode('***',$cond_params[$l]);
						if(!isset($type_and_id[$params_value[0]]))
							unset($cond_params[$l]);
					}
					$cond_params = array_values($cond_params);
					
					$if = '';
					$keyup = '';
					$change = '';
					$click = '';
					
					for($m=0; $m<count($cond_params); $m++)
					{
						$params_value = explode('***',wp_specialchars_decode($cond_params[$m], 'single'));
						switch($type_and_id[$params_value[0]])
						{
							case "type_text":
							case "type_password":
							case "type_textarea":
							case "type_number":
							case "type_submitter_mail":	
							case "type_spinner":
							    if($params_value[1] == "%" || $params_value[1] == "!%")
								{
									$like_or_not = ($params_value[1] == "%" ? ">" : "==");
									$if .= ' jQuery("#wdform_'.$params_value[0].'_element'.$form_id.'").val().indexOf("'.$params_value[2].'")'.$like_or_not.'-1 ';
								}
								else
								{
									if($params_value[1] == "=" || $params_value[1] == "!")
									{
										$params_value[2] = "";
										$params_value[1] = $params_value[1]."=";						
									}		
									$if .= ' jQuery("#wdform_'.$params_value[0].'_element'.$form_id.'").val()'.$params_value[1].'"'.$params_value[2].'" ';
								}
								
								$keyup .= '#wdform_'.$params_value[0].'_element'.$form_id.', ';	
								if($type_and_id[$params_value[0]] == "type_spinner")
									$click .= '#wdform_'.$params_value[0].'_element'.$form_id.' ~ a, ';
							break;
							
							case "type_name":	
								if($params_value[1] == "%" || $params_value[1] == "!%")
								{	
									$extended0 = '';
									$extended1 = '';
									$extended2 = '';
									$extended3 = '';
									$normal0 = '';
									$normal1 = '';
									$normal2 = '';
									$normal3 = '';
									
									$like_or_not = ($params_value[1] == "%" ? ">" : "==");
									$name_fields = explode(' ',$params_value[2]);
									if($name_fields[0]!='')
									{
										$extended0 = 'jQuery("#wdform_'.$params_value[0].'_element_title'.$form_id.'").val().indexOf("'.$name_fields[0].'")'.$like_or_not.'-1 ';
										$normal0 = 'jQuery("#wdform_'.$params_value[0].'_element_first'.$form_id.'").val().indexOf("'.$name_fields[0].'")'.$like_or_not.'-1 ';
									}
										
									if(isset($name_fields[1]) && $name_fields[1]!='')
									{
										$extended1 = 'jQuery("#wdform_'.$params_value[0].'_element_first'.$form_id.'").val().indexOf("'.$name_fields[1].'")'.$like_or_not.'-1 ';
										$normal1 = 'jQuery("#wdform_'.$params_value[0].'_element_last'.$form_id.'").val().indexOf("'.$name_fields[1].'")'.$like_or_not.'-1 ';
									}
									
									if(isset($name_fields[2]) && $name_fields[2]!='')
									{
										$extended2 = 'jQuery("#wdform_'.$params_value[0].'_element_last'.$form_id.'").val().indexOf("'.$name_fields[2].'")'.$like_or_not.'-1 ';
										$normal2 = '';
									}
										
									if(isset($name_fields[3]) && $name_fields[3]!='')
									{
										$extended3 = 'jQuery("#wdform_'.$params_value[0].'_element_middle'.$form_id.'").val().indexOf("'.$name_fields[3].'")'.$like_or_not.'-1 ';
										$normal3 = '';
									}
									
									
									if(isset($name_fields[3]))
									{
										$extended ='';
										$normal ='';
										if($extended0)
										{	
											$extended = $extended0;
											if($extended1)
											{
												$extended .= ' && '.$extended1;
												if($extended2)
													$extended .=' && '.$extended2;	
													
												if($extended3)
													$extended .=' && '.$extended3;
											}
											else
											{
												if($extended2)
													$extended .= ' && '.$extended2;
												if($extended3)
													$extended .= ' && '.$extended3;	
											}
										}
										else
										{
											if($extended1)
											{	
												$extended = $extended1;
												if($extended2)
													$extended .=' && '.$extended2;
													
												if($extended3)
													$extended .=' && '.$extended3;
											}
											else
											{
												if($extended2)
												{
													$extended = $extended2;
													if($extended3)
														$extended .= ' && '.$extended3;
												}
												else
													if($extended3)
														$extended = $extended3;
											}		
										}
									
										if($normal0)
										{	
											$normal = $normal0;
											if($normal1)
												$normal .= ' && '.$normal1;
										}
										else
										{
											if($normal1)
												$normal = $normal1;			
										}
									}
									else
									{
										if(isset($name_fields[2]))
										{
											$extended ="";
											$normal ="";
											if($extended0)
											{	
												$extended = $extended0;	
												if($extended1)
													$extended .= ' && '.$extended1;

												if($extended2)
														$extended .=' && '.$extended2;

											}
											else
											{
												if($extended1)
												{
													$extended = $extended1;
													if($extended2)
														$extended .= ' && '.$extended2;	
												}		
												else
													if($extended2)
														$extended = $extended2;	
											}
											
											
											if($normal0)
											{	
												$normal = $normal0;	
												if($normal1)
													$normal .= ' && '.$normal1;
											}
											else
											{
												if($normal1)
													$normal = $normal1;
											}
											
										}
										else
										{
											if(isset($name_fields[1]))
											{
												$extended ='';
												$normal ='';
												if($extended0)
												{	
													if($extended1)
														$extended = $extended0.' && '.$extended1;
													else
														$extended = $extended0;
												}
												else
												{
													if($extended1)
														$extended = $extended1;
												}
												
												
												if($normal0)
												{	
													if($normal1)
														$normal = $normal0.' && '.$normal1;
													else
														$normal = $normal0;
												}
												else
												{
													if($normal1)
														$normal = $normal1;
												}
											}
											else
											{
												$extended = $extended0;
												$normal = $normal0;
											}
										}	
									}
									
									if($extended!="" && $normal!="")			
										$if .= ' ((jQuery("#wdform_'.$params_value[0].'_element_title'.$form_id.'").length != 0 || jQuery("#wdform_'.$params_value[0].'_element_middle'.$form_id.'").length != 0) ?  '.$extended.' : '.$normal.') ';
									else
										$if .= ' true';
								}
								else
								{
									if($params_value[1] == "=" || $params_value[1] == "!")
									{
										$name_and_or = $params_value[1] == "=" ? "&&" : "||";			
										$name_empty_or_not = $params_value[1]."=";	
										$extended = ' (jQuery("#wdform_'.$params_value[0].'_element_title'.$form_id.'").val()'.$name_empty_or_not.'"" '.$name_and_or.' jQuery("#wdform_'.$params_value[0].'_element_first'.$form_id.'").val()'.$name_empty_or_not.'"" '.$name_and_or.' jQuery("#wdform_'.$params_value[0].'_element_last'.$form_id.'").val()'.$name_empty_or_not.'"" '.$name_and_or.' jQuery("#wdform_'.$params_value[0].'_element_middle'.$form_id.'").val()'.$name_empty_or_not.'"") ';		
										$normal = ' (jQuery("#wdform_'.$params_value[0].'_element_first'.$form_id.'").val()'.$name_empty_or_not.'"" '.$name_and_or.' jQuery("#wdform_'.$params_value[0].'_element_last'.$form_id.'").val()'.$name_empty_or_not.'"") ';
										
										$if .= ' ((jQuery("#wdform_'.$params_value[0].'_element_title'.$form_id.'").length != 0 || jQuery("#wdform_'.$params_value[0].'_element_middle'.$form_id.'").length != 0) ?  '.$extended.' : '.$normal.') ';
									}
									else
									{
										$extended0 = '';
										$extended1 = '';
										$extended2 = '';
										$extended3 = '';
										$normal0 = '';
										$normal1 = '';
										$normal2 = '';
										$normal3 = '';
									
										$name_fields = explode(' ',$params_value[2]);
										if($name_fields[0]!='')
										{
											$extended0 = 'jQuery("#wdform_'.$params_value[0].'_element_title'.$form_id.'").val()'.$params_value[1].'"'.$name_fields[0].'"';
											$normal0 = 'jQuery("#wdform_'.$params_value[0].'_element_first'.$form_id.'").val()'.$params_value[1].'"'.$name_fields[0].'"';
										}
										
										if(isset($name_fields[1]) && $name_fields[1]!='')
										{
											$extended1 = 'jQuery("#wdform_'.$params_value[0].'_element_first'.$form_id.'").val()'.$params_value[1].'"'.$name_fields[1].'"';
											$normal1 = 'jQuery("#wdform_'.$params_value[0].'_element_last'.$form_id.'").val()'.$params_value[1].'"'.$name_fields[1].'"';
										}
										
										if(isset($name_fields[2]) && $name_fields[2]!='')
										{
											$extended2 = 'jQuery("#wdform_'.$params_value[0].'_element_last'.$form_id.'").val()'.$params_value[1].'"'.$name_fields[2].'"';
											$normal2 = '';
										}
										
										if(isset($name_fields[3]) && $name_fields[3]!='')
										{			
											$extended3 = 'jQuery("#wdform_'.$params_value[0].'_element_middle'.$form_id.'").val()'.$params_value[1].'"'.$name_fields[3].'"';
											$normal3 = '';
										}
										
										
										if(isset($name_fields[3]))
										{
											$extended ='';
											$normal ='';
		
											if($extended0)
											{	
												$extended = $extended0;
												if($extended1)
												{
													$extended .= ' && '.$extended1;
													if($extended2)
														$extended .= ' && '.$extended2;	
														
													if($extended3)
														$extended .= ' && '.$extended3;
												}
												else
												{
													if($extended2)
														$extended .= ' && '.$extended2;
													if($extended3)
														$extended .= ' && '.$extended3;	
												}
											}
											else
											{
												if($extended1)
												{	
													$extended = $extended1;
													if($extended2)
														$extended .= ' && '.$extended2;
														
													if($extended3)
														$extended .= ' && '.$extended3;
												}
												else
												{
													if($extended2)
													{
														$extended = $extended2;
														if($extended3)
															$extended .= ' && '.$extended3;
													}
													else
														if($extended3)
															$extended = $extended3;
												}		
											}
										
											if($normal0)
											{	
												$normal = $normal0;
												if($normal1)
													$normal .= ' && '.$normal1;
											}
											else
											{
												if($normal1)
													$normal = $normal1;			
											}
										}
										else
										{
											if(isset($name_fields[2]))
											{
												$extended ="";
												$normal ="";
												if($extended0)
												{	
													$extended = $extended0;	
													if($extended1)
														$extended .= ' && '.$extended1;

													if($extended2)
															$extended .= ' && '.$extended2;

												}
												else
												{
													if($extended1)
													{
														$extended = $extended1;
														if($extended2)
															$extended .= ' && '.$extended2;	
													}		
													else
														if($extended2)
															$extended = $extended2;	
												}
												
												
												if($normal0)
												{	
													$normal = $normal0;	
													if($normal1)
														$normal .= ' && '.$normal1;
												}
												else
												{
													if($normal1)
														$normal = $normal1;
												}
												
											}
											else
											{
												if(isset($name_fields[1]))
												{
													$extended ='';
													$normal ='';
													if($extended0)
													{	
														if($extended1)
															$extended = $extended0.' && '.$extended1;
														else
															$extended = $extended0;
													}
													else
													{
														if($extended1)
															$extended = $extended1;
													}
													
													
													if($normal0)
													{	
														if($normal1)
															$normal = $normal0.' && '.$normal1;
														else
															$normal = $normal0;
													}
													else
													{
														if($normal1)
															$normal = $normal1;
													}
												}
												else
												{
													$extended = $extended0;
													$normal = $normal0;
												}
											}	
										}

										if($extended!="" && $normal!="")			
											$if .= ' ((jQuery("#wdform_'.$params_value[0].'_element_title'.$form_id.'").length != 0 || jQuery("#wdform_'.$params_value[0].'_element_middle'.$form_id.'").length != 0) ?  '.$extended.' : '.$normal.') ';
										else
											$if .= ' true';
									}	
								}
								$keyup .= '#wdform_'.$params_value[0].'_element_title'.$form_id.', #wdform_'.$params_value[0].'_element_first'.$form_id.', #wdform_'.$params_value[0].'_element_last'.$form_id.', #wdform_'.$params_value[0].'_element_middle'.$form_id.', ';		
							break;
								
							case "type_phone":
								if($params_value[1] == "==" || $params_value[1] == "!=")
								{
									$phone_fields = explode(' ',$params_value[2]);
									if(isset($phone_fields[1]))
									{
										if($phone_fields[0]!='' && $phone_fields[1]!='')
											$if .= ' (jQuery("#wdform_'.$params_value[0].'_element_first'.$form_id.'").val()'.$params_value[1].'"'.$phone_fields[0].'" && jQuery("#wdform_'.$params_value[0].'_element_last'.$form_id.'").val()'.$params_value[1].'"'.$phone_fields[1].'") ';	
										else
										{
											if($phone_fields[0]=='')
												$if .= ' (jQuery("#wdform_'.$params_value[0].'_element_last'.$form_id.'").val()'.$params_value[1].'"'.$phone_fields[1].'") ';	
											else
												if($phone_fields[1]=='')
													$if .= ' (jQuery("#wdform_'.$params_value[0].'_element_first'.$form_id.'").val()'.$params_value[1].'"'.$phone_fields[1].'") ';	
										}
									}
									else
										$if .= ' jQuery("#wdform_'.$params_value[0].'_element_first'.$form_id.'").val()'.$params_value[1].'"'.$params_value[2].'" ';
								}
								
								if($params_value[1] == "%" || $params_value[1] == "!%")
								{
									$like_or_not = ($params_value[1] == "%" ? ">" : "==");
									$phone_fields = explode(' ',$params_value[2]);
									if(isset($phone_fields[1]))
									{
										if($phone_fields[0]!='' && $phone_fields[1]!='')
											$if .= ' (jQuery("#wdform_'.$params_value[0].'_element_first'.$form_id.'").val().indexOf("'.$phone_fields[0].'")'.$like_or_not.'-1 && jQuery("#wdform_'.$params_value[0].'_element_last'.$form_id.'").val().indexOf("'.$phone_fields[1].'")'.$like_or_not.'-1)';
										else
										{
											if($phone_fields[0]=='')
												$if .= ' (jQuery("#wdform_'.$params_value[0].'_element_last'.$form_id.'").val().indexOf("'.$phone_fields[1].'")'.$like_or_not.'-1) ';	
											else
												if($phone_fields[1]=='')
													$if .= ' (jQuery("#wdform_'.$params_value[0].'_element_first'.$form_id.'").val().indexOf("'.$phone_fields[0].'")'.$like_or_not.'-1) ';															
										}
									}
									else
										$if .= ' (jQuery("#wdform_'.$params_value[0].'_element_first'.$form_id.'").val().indexOf("'.$phone_fields[0].'")'.$like_or_not.'-1) ';
								}
								
								if($params_value[1] == "=" || $params_value[1] == "!")
								{
									$params_value[2] = "";
									$and_or_phone = ($params_value[1]=="=" ? "&&" : "||");	
									$params_value[1] = $params_value[1]."=";	
									
									$if .= ' (jQuery("#wdform_'.$params_value[0].'_element_first'.$form_id.'").val()'.$params_value[1].'"'.$params_value[2].'" '.$and_or_phone.' jQuery("#wdform_'.$params_value[0].'_element_last'.$form_id.'").val()'.$params_value[1].'"'.$params_value[2].'") ';										
								}
						
							
								$keyup .= '#wdform_'.$params_value[0].'_element_first'.$form_id.', #wdform_'.$params_value[0].'_element_last'.$form_id.', ';
							break;
						
							case "type_paypal_price":	
								if($params_value[1] == "==" || $params_value[1] == "!=")
									$if .= ' (jQuery("#wdform_'.$params_value[0].'_td_name_cents").attr("style")=="display: none;" ? jQuery("#wdform_'.$params_value[0].'_element_dollars'.$form_id.'").val()'.$params_value[1].'"'.$params_value[2].'" : parseFloat(jQuery("#wdform_'.$params_value[0].'_element_dollars'.$form_id.'").val()+"."+jQuery("#wdform_'.$params_value[0].'_element_cents'.$form_id.'").val())'.$params_value[1].'parseFloat("'.str_replace('.0', '.', $params_value[2]).'"))';

								if($params_value[1] == "%" || $params_value[1] == "!%")
								{
									$like_or_not = ($params_value[1] == "%" ? ">" : "==");
									$if .= ' (jQuery("#wdform_'.$params_value[0].'_td_name_cents").attr("style")=="display: none;" ? jQuery("#wdform_'.$params_value[0].'_element_dollars'.$form_id.'").val().indexOf("'.$params_value[2].'")'.$like_or_not.'-1 : (jQuery("#wdform_'.$params_value[0].'_element_dollars'.$form_id.'").val()+"."+jQuery("#wdform_'.$params_value[0].'_element_cents'.$form_id.'").val()).indexOf("'.str_replace('.0', '.', $params_value[2]).'")'.$like_or_not.'-1) ';									
								}	
									
								if($params_value[1] == "=" || $params_value[1] == "!")
								{
									$params_value[2] = "";
									$and_or_price = ($params_value[1]=="=" ? "&&" : "||");	
									$params_value[1] = $params_value[1]."=";
									$if .= ' (jQuery("#wdform_'.$params_value[0].'_td_name_cents").attr("style")=="display: none;" ? jQuery("#wdform_'.$params_value[0].'_element_dollars'.$form_id.'").val()'.$params_value[1].'"'.$params_value[2].'" : (jQuery("#wdform_'.$params_value[0].'_element_dollars'.$form_id.'").val()'.$params_value[1].'"'.$params_value[2].'" '.$and_or_price.' jQuery("#wdform_'.$params_value[0].'_element_cents'.$form_id.'").val()'.$params_value[1].'"'.$params_value[2].'"))';										
								}
								
								$keyup .= '#wdform_'.$params_value[0].'_element_dollars'.$form_id.', #wdform_'.$params_value[0].'_element_cents'.$form_id.', ';
							break;
							
							case "type_own_select":
								if($params_value[1] == "%" || $params_value[1] == "!%")
								{
									$like_or_not = ($params_value[1] == "%" ? ">" : "==");
									$if .= ' jQuery("#wdform_'.$params_value[0].'_element'.$form_id.'").val().indexOf("'.$params_value[2].'")'.$like_or_not.'-1 ';
								}
								else
								{
									if($params_value[1] == "=" || $params_value[1] == "!")
									{
										$params_value[2] = "";
										$params_value[1] = $params_value[1]."=";						
									}
									$if .= ' jQuery("#wdform_'.$params_value[0].'_element'.$form_id.'").val()'.$params_value[1].'"'.$params_value[2].'" ';
								}
								$change .= '#wdform_'.$params_value[0].'_element'.$form_id.', ';
							break;
							
							case "type_paypal_select":
								if($params_value[1] == "%" || $params_value[1] == "!%")
								{
									$like_or_not = ($params_value[1] == "%" ? ">" : "==");
									$if .= ' jQuery("#wdform_'.$params_value[0].'_element'.$form_id.'").val().indexOf("'.$params_value[2].'")'.$like_or_not.'-1 ';
								}
								else
								{
									if($params_value[1] == "=" || $params_value[1] == "!")
									{
										$params_value[2] = "";
										$params_value[1] = $params_value[1]."=";	
										$if .= ' jQuery("#wdform_'.$params_value[0].'_element'.$form_id.'").val()'.$params_value[1].'"'.$params_value[2].'"';									
									}
									else
									{
										if ( strpos($params_value[2], '*:*value*:*') > -1 ) {
											$and_or = $params_value[1] == "==" ? '&&' : '||';
											$choise_and_value = explode("*:*value*:*", $params_value[2]);
											$params_value[2] = $choise_and_value[1];
											$params_label = $choise_and_value[0];
											$if .= ' jQuery("#wdform_'.$params_value[0].'_element'.$form_id.'").val()'.$params_value[1].'"'.$params_value[2].'" '.$and_or.' jQuery("div[wdid='.$params_value[0].'] select option:selected").text()'.$params_value[1].'"'.$params_label.'" ';
										} else {
											$if .= ' jQuery("#wdform_'.$params_value[0].'_element'.$form_id.'").val()'.$params_value[1].'"'.$params_value[2].'" ';
										}	
									}
								}
								$change .= '#wdform_'.$params_value[0].'_element'.$form_id.', ';
							break;
							case "type_address":	
								if($params_value[1] == "%" || $params_value[1] == "!%")
								{
									$like_or_not = ($params_value[1] == "%" ? ">" : "==");
									$if .= ' jQuery("#wdform_'.$params_value[0].'_country'.$form_id.'").val().indexOf("'.$params_value[2].'")'.$like_or_not.'-1 ';
								}
								else
								{
									if($params_value[1] == "=" || $params_value[1] == "!")
									{
										$params_value[2] = "";
										$params_value[1] = $params_value[1]."=";						
									}								
									$if .= ' jQuery("#wdform_'.$params_value[0].'_country'.$form_id.'").val()'.$params_value[1].'"'.$params_value[2].'" ';
								}	
								$change .= '#wdform_'.$params_value[0].'_country'.$form_id.', ';
							break;
							case "type_country":
								if($params_value[1] == "%" || $params_value[1] == "!%")
								{
									$like_or_not = ($params_value[1] == "%" ? ">" : "==");
									$if .= ' wdformjQuery("#wdform_'.$params_value[0].'_element'.$form_id.'").val().indexOf("'.$params_value[2].'")'.$like_or_not.'-1 ';
								}
								else
								{
									if($params_value[1] == "=" || $params_value[1] == "!")
									{
										$params_value[2] = "";
										$params_value[1] = $params_value[1]."=";						
									}								
									$if .= ' wdformjQuery("#wdform_'.$params_value[0].'_element'.$form_id.'").val()'.$params_value[1].'"'.$params_value[2].'" ';
								}	
								$change .= '#wdform_'.$params_value[0].'_element'.$form_id.', ';
							break;
							
							case "type_radio":
							case "type_paypal_radio":
							case "type_paypal_shipping":
								
								if($params_value[1] == "==" || $params_value[1] == "!=")
								{
									if ( strpos($params_value[2], '*:*value*:*') > -1 ) {
										$and_or = $params_value[1] == "==" ? '&&' : '||';
										$choise_and_value = explode("*:*value*:*", $params_value[2]);
										$params_value[2] = $choise_and_value[1];
										$params_label = $choise_and_value[0];
										$if .= ' jQuery("input[name^=\'wdform_'.$params_value[0].'_element'.$form_id.'\']:checked").val()'.$params_value[1].'"'.$params_value[2].'" '.$and_or.' jQuery("input[name^=\'wdform_'.$params_value[0].'_element'.$form_id.'\']:checked").attr("title")'.$params_value[1].'"'.$params_label.'" ';
									} else {
										$if .= ' jQuery("input[name^=\'wdform_'.$params_value[0].'_element'.$form_id.'\']:checked").val()'.$params_value[1].'"'.$params_value[2].'" ';
									}
			
									$click .= 'div[wdid='.$params_value[0].'] input[type=\'radio\'], ';
								}
								
								if($params_value[1] == "%" || $params_value[1] == "!%")
								{
									$click .= 'div[wdid='.$params_value[0].'] input[type=\'radio\'], ';
									$like_or_not = ($params_value[1] == "%" ? ">" : "==");
									$if .= ' (jQuery("input[name^=\'wdform_'.$params_value[0].'_element'.$form_id.'\']:checked").val() ? (jQuery("input[name^=\'wdform_'.$params_value[0].'_element'.$form_id.'\']:checked").attr("other") ? false  : (jQuery("input[name^=\'wdform_'.$params_value[0].'_element'.$form_id.'\']:checked").val().indexOf("'.$params_value[2].'")'.$like_or_not.'-1 )) : false) ';

								}
								
								if($params_value[1] == "=" || $params_value[1] == "!")
								{	
									$ckecked_or_no = ($params_value[1] == "=" ? "!" : "");
									$if .= ' '.$ckecked_or_no.'jQuery("input[name^=\'wdform_'.$params_value[0].'_element'.$form_id.'\']:checked").val()';
									$click .= 'div[wdid='.$params_value[0].'] input[type=\'radio\'], ';
								}	
								
							break;
							
							case "type_checkbox":
							case "type_paypal_checkbox":	
							if($params_value[1] == "==" || $params_value[1] == "!=")
								{
									if($params_value[2])
									{
										$choises = explode('@@@',$params_value[2]);
										$choises 	= array_slice($choises,0, count($choises)-1); 
										
										if($params_value[1]=="!=")
											$is = "!";
										else
											$is = "";
											
										foreach($choises as $key1=>$choise)
										{
											if($type_and_id[$params_value[0]]=="type_paypal_checkbox")
											{
												$choise_and_value = explode("*:*value*:*",$choise);
												$if .= ' '.$is.'(jQuery("div[wdid='.$params_value[0].'] input[value=\"'.$choise_and_value[1].'\"]").is(":checked") && jQuery("div[wdid='.$params_value[0].'] input[title=\"'.$choise_and_value[0].'\"]"))';

											}
											else
											$if .= ' '.$is.'jQuery("div[wdid='.$params_value[0].'] input[value=\"'.$choise.'\"]").is(":checked") ';
											
											if($key1!=count($choises)-1)
												$if .= '&&';
										}
									
										$click .= 'div[wdid='.$params_value[0].'] input[type=\'checkbox\'], ';
									}
									else
									{
										if($or_and=='&&')
											$if .= ' true';
										else
											$if .= ' false';
									}
								}
								
								if($params_value[1] == "%" || $params_value[1] == "!%")
								{
									$like_or_not = ($params_value[1] == "%" ? ">" : "==");
									if($params_value[2])
									{
										$choises = explode('@@@',$params_value[2]);
										$choises 	= array_slice($choises,0, count($choises)-1);

										if($type_and_id[$params_value[0]]=="type_paypal_checkbox")
										{
											foreach($choises as $key1=>$choise)
											{
										
												$choise_and_value = explode("*:*value*:*",$choise);
					
												$if .= ' jQuery("div[wdid='.$params_value[0].']  input[type=\"checkbox\"]:checked").serialize().indexOf("'.$choise_and_value[1].'")'.$like_or_not.'-1 ';
													
												if($key1!=count($choises)-1)
													$if .= '&&';			
											}																			
										}
										else
										{
											foreach($choises as $key1=>$choise)
											{
												$if .= ' jQuery("div[wdid='.$params_value[0].']  input[type=\"checkbox\"]:checked").serialize().indexOf("'.str_replace(" ","+",$choise).'")'.$like_or_not.'-1 ';
												
												if($key1!=count($choises)-1)
													$if .= '&&';
											}	
										}
									
											
											
								
										$click .= 'div[wdid='.$params_value[0].'] input[type=\'checkbox\'], ';
									}
									else
									{
										if($or_and=='&&')
											$if .= ' true';
										else
											$if .= ' false';
									}
						
								}
								
								if($params_value[1] == "=" || $params_value[1] == "!")
								{
									$ckecked_or_no = ($params_value[1] == "=" ? "==" : ">");				
									$if .= ' jQuery("div[wdid='.$params_value[0].'] input[type=\"checkbox\"]:checked").length'.$ckecked_or_no.'0 ';
									$click .= 'div[wdid='.$params_value[0].'] input[type=\'checkbox\'], ';	
									
								}

								
                             break;
						}	

						if($m!=count($cond_params)-1)
						    {
								$params_value_next = explode('***',$cond_params[$m+1]);
								if(isset($type_and_id[$params_value_next[0]]))
								$if .= $or_and;
							}
					    
					}

					if($if)
					{
						$condition_js .= '
					
							if('.$if.')
								jQuery("div[wdid='.$field_label[$k].']").'.$display .';
							else
								jQuery("div[wdid='.$field_label[$k].']").'.$display_none .';';							
					}
					
					if($keyup)
						$condition_js .= '
							jQuery("'.substr($keyup,0,-2).'").keyup(function() { 

								if('.$if.')
									jQuery("div[wdid='.$field_label[$k].']").'.$display .';
								else
									jQuery("div[wdid='.$field_label[$k].']").'.$display_none .'; });';
					
					if($change)
						$condition_js .= '
							jQuery("'.substr($change,0,-2).'").change(function() { 
								if('.$if.')
									jQuery("div[wdid='.$field_label[$k].']").'.$display .';
								else
									jQuery("div[wdid='.$field_label[$k].']").'.$display_none .'; });';
			
					if($click)
					{	
					        $condition_js .= '
							jQuery("'.substr($click,0,-2).'").click(function() { 
								if('.$if.')
									jQuery("div[wdid='.$field_label[$k].']").'.$display .';
								else
									jQuery("div[wdid='.$field_label[$k].']").'.$display_none .'; });';
		            }
			}
			
			
		}
	
	}
      
      if ($row->autogen_layout == 0) {
        $form=$row->custom_front;
      }
      else {
        $form = $row->form_front;
      }
      foreach($id1s as $id1s_key => $id1) {
        $label=$labels[$id1s_key];
        $type=$types[$id1s_key];
        $params=$paramss[$id1s_key];
        if (strpos($form, '%'.$id1.' - '.$label.'%') || strpos($form, '%'.$id1.' -'.$label.'%')) {
          $rep='';
          $required=false;
          $param=array();
          $param['attributes'] = '';
          $is_type[$type]=true;          
          switch($type) {
            case 'type_section_break': {
              $params_names=array('w_editor');
              $temp=$params;
              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              $rep ='<div type="type_section_break" class="wdform-field-section-break"><div class="wdform_section_break">' . html_entity_decode($param['w_editor']) . '</div></div>';
              break;
            }
            case 'type_editor': {
              $params_names=array('w_editor');
              $temp=$params;
              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              $rep ='<div type="type_editor" class="wdform-field">' . html_entity_decode($param['w_editor']) . '</div>';
              break;
            }
            case 'type_send_copy': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_first_val','w_required');
              $temp=$params;
              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if($temp) {
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }              
              $input_active = ($param['w_first_val']=='true' ? "checked='checked'" : "");	
              $post_value = isset($_POST["counter".$form_id]) ? esc_html($_POST["counter".$form_id]) : NULL;
              if(isset($post_value)) {
                $post_temp = isset($_POST['wdform_'.$id1.'_element'.$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1.'_element'.$form_id])) : "";
                $input_active = (isset($post_temp) ? "checked='checked'" : "");	
              }              
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");              
              $required = ($param['w_required']=="yes" ? true : false);	              
              $rep ='<div type="type_send_copy" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label"><label for="wdform_'.$id1.'_element'.$form_id.'">'.$label.'</label></span>';
              if($required) {
                $rep.='<span class="wdform-required">'.$required_sym.'</span>';
              }
              $rep.='</div>
              <div class="wdform-element-section" style="'.$param['w_field_label_pos2'].'" >
                <div class="checkbox-div" style="left:3px">
                <input type="checkbox" id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" '.$input_active.' '.$param['attributes'].'/>
                <label for="wdform_'.$id1.'_element'.$form_id.'"></label>
                </div>
              </div></div>';

              $onsubmit_js.='
              if(!jQuery("#wdform_'.$id1.'_element'.$form_id.'").is(":checked"))
                jQuery("<input type=\"hidden\" name=\"wdform_send_copy_'.$form_id.'\" value = \"1\" />").appendTo("#form'.$form_id.'");';
              if($required) {
                $check_js.='
                if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
                {
                  if(x.find(jQuery("div[wdid='.$id1.'] input:checked")).length == 0)
                  {
                    alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                    old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                    x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                    
                    return false;
                  }						
                }
                ';
              }
              break;
            }
           case 'type_text': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_size','w_first_val','w_title','w_required','w_unique');
              $temp=$params;
			  if(strpos($temp, 'w_regExp_status') > -1)
				$params_names = array('w_field_label_size','w_field_label_pos','w_size','w_first_val','w_title','w_required', 'w_regExp_status', 'w_regExp_value', 'w_regExp_common', 'w_regExp_arg', 'w_regExp_alert', 'w_unique');
              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' '.$attr;
              }

              $param['w_first_val'] = (isset($_POST['wdform_'.$id1.'_element'.$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1.'_element'.$form_id])) : $param['w_first_val']);
          
              $wdformfieldsize = ($param['w_field_label_pos']=="left" ? $param['w_field_label_size']+$param['w_size'] + 10 : max($param['w_field_label_size'],$param['w_size']));	
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
              
              $input_active = ($param['w_first_val']==$param['w_title'] ? "input_deactive" : "input_active");	
              $required = ($param['w_required']=="yes" ? true : false);	

			  $param['w_regExp_status'] = (isset($param['w_regExp_status']) ? $param['w_regExp_status'] : "no");
			  
              $rep ='<div type="type_text" class="wdform-field" style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              if($required) {
                $rep.='<span class="wdform-required">'.$required_sym.'</span>';
              }
              $rep.='</div><div class="wdform-element-section" style="'.$param['w_field_label_pos2'].' width: '.$param['w_size'].'px;"  ><input type="text" class="'.$input_active.'" id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.$param['w_first_val'].'" title="'.$param['w_title'].'"  style="width: 100%;" '.$param['attributes'].'></div></div>';
              
              if($required) {
                $check_js.='
                if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
                {
                  if(jQuery("#wdform_'.$id1.'_element'.$form_id.'").val()=="'.$param['w_title'].'" || jQuery("#wdform_'.$id1.'_element'.$form_id.'").val()=="")
                  {
                    alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                    jQuery("#wdform_'.$id1.'_element'.$form_id.'").addClass( "form-error" );
                    old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                    x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                    jQuery("#wdform_'.$id1.'_element'.$form_id.'").focus();
                    jQuery("#wdform_'.$id1.'_element'.$form_id.'").change(function() { if( jQuery(this).val()!="" ) jQuery(this).removeClass("form-error"); else jQuery(this).addClass("form-error");});
                    return false;
                  }
                }
                ';
              }
             
			  if($param['w_regExp_status'] == 'yes') {
				$check_js .='			
					var RegExpression = "";
					var rules = unescape("'.$param["w_regExp_value"].'"); 
					("'.$param["w_regExp_arg"].'".length <= 0) ?  RegExpression = new RegExp(rules) : RegExpression = new RegExp(rules'.', "'.$param["w_regExp_arg"].'" );

					if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none") {
						if(jQuery("#wdform_'.$id1.'_element'.$form_id.'").val().length > 0){
							if (RegExpression.test(jQuery("#wdform_'.$id1.'_element'.$form_id.'").val()) != true)
							{
								alert( " '.$param["w_regExp_alert"].' ");
								old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
								x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
								jQuery("#wdform_'.$id1.'_element'.$form_id.'").addClass( "form-error" );
								jQuery("#wdform_'.$id1.'_element'.$form_id.'").focus();
								jQuery("#wdform_'.$id1.'_element'.$form_id.'").change(function() { if( jQuery(this).val()!="" ) jQuery(this).removeClass("form-error"); else jQuery(this).addClass("form-error");});
								return false;
							}
						}
					}';
			   }
					
			  break;              
            }

            case 'type_number': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_size','w_first_val','w_title','w_required','w_unique','w_class');
              $temp=$params;
              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }
              
              $param['w_first_val']=(isset($_POST['wdform_'.$id1.'_element'.$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1.'_element'.$form_id])) : $param['w_first_val']);

              $wdformfieldsize = ($param['w_field_label_pos']=="left" ? $param['w_field_label_size']+$param['w_size'] + 10 : max($param['w_field_label_size'],$param['w_size']));	
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
              $input_active = ($param['w_first_val']==$param['w_title'] ? "input_deactive" : "input_active");	
              $required = ($param['w_required']=="yes" ? true : false);	
                      
              $rep ='<div type="type_number" class="wdform-field" style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section"  class="'.$param['w_class'].'" style="'.$param['w_field_label_pos1'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              if($required) {
                $rep.='<span class="wdform-required">'.$required_sym.'</span>';
              }
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].' width: '.$param['w_size'].'px;"><input type="text" class="'.$input_active.'" id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.$param['w_first_val'].'" title="'.$param['w_title'].'" style="width: 100%;" '.$param['attributes'].'></div></div>';
              
              if($required) {
                $check_js.='
                if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
                {
                  if(jQuery("#wdform_'.$id1.'_element'.$form_id.'").val()=="'.$param['w_title'].'" || jQuery("#wdform_'.$id1.'_element'.$form_id.'").val()=="")
                  {
                    alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                    jQuery("#wdform_'.$id1.'_element'.$form_id.'").addClass( "form-error" );
                    old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                    x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                    jQuery("#wdform_'.$id1.'_element'.$form_id.'").focus();
                    jQuery("#wdform_'.$id1.'_element'.$form_id.'").change(function() { if( jQuery(this).val()!="" ) jQuery(this).removeClass("form-error"); else jQuery(this).addClass("form-error");});
                    return false;
                  }
                }
                ';
              }
              break;
            }

            case 'type_password': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_size','w_required','w_unique','w_class');
              $temp=$params;
              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if($temp) {
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }
          
              $wdformfieldsize = ($param['w_field_label_pos']=="left" ? $param['w_field_label_size']+$param['w_size'] + 10 : max($param['w_field_label_size'],$param['w_size']));	
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
              $required = ($param['w_required']=="yes" ? true : false);	

              $rep ='<div type="type_password" class="wdform-field" style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section"  class="'.$param['w_class'].'" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              if($required) {
                $rep.='<span class="wdform-required">'.$required_sym.'</span>';
              }
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].' width: '.$param['w_size'].'px;"><input type="password" id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" style="width: 100%;" '.$param['attributes'].'></div></div>';
              
              if($required) {
                $check_js.='
                if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
                {
                  if(jQuery("#wdform_'.$id1.'_element'.$form_id.'").val()=="")
                  {
                    alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                    jQuery("#wdform_'.$id1.'_element'.$form_id.'").addClass( "form-error" );
                    old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                    x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                    jQuery("#wdform_'.$id1.'_element'.$form_id.'").focus();
                    jQuery("#wdform_'.$id1.'_element'.$form_id.'").change(function() { if( jQuery(this).val()!="" ) jQuery(this).removeClass("form-error"); else jQuery(this).addClass("form-error");});
                    return false;
                  }
                }
                ';
              }
              break;
            }

            case 'type_textarea': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_size_w','w_size_h','w_first_val','w_title','w_required','w_unique','w_class');
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
                  $param['attributes'] = $param['attributes'].' '.$attr;
              }
            
              $param['w_first_val']=(isset($_POST['wdform_'.$id1.'_element'.$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1.'_element'.$form_id])) : $param['w_first_val']);			
                
              $wdformfieldsize = ($param['w_field_label_pos']=="left" ? $param['w_field_label_size']+$param['w_size_w'] + 10 : max($param['w_field_label_size'],$param['w_size_w']));	
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
              $input_active = ($param['w_first_val']==$param['w_title'] ? "input_deactive" : "input_active");	
              $required = ($param['w_required']=="yes" ? true : false);	
            
              $rep ='<div type="type_textarea" class="wdform-field"  style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              if($required) {
                $rep.='<span class="wdform-required">'.$required_sym.'</span>';
              }
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].' width: '.$param['w_size_w'].'px"><textarea class="'.$input_active.'" id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" title="'.$param['w_title'].'"  style="width: 100%; height: '.$param['w_size_h'].'px;" '.$param['attributes'].'>'.$param['w_first_val'].'</textarea></div></div>';
             
              if($required) {
                $check_js.='
                if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
                {
                  if(jQuery("#wdform_'.$id1.'_element'.$form_id.'").val()=="'.$param['w_title'].'" || jQuery("#wdform_'.$id1.'_element'.$form_id.'").val()=="")
                  {
                    alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                    jQuery("#wdform_'.$id1.'_element'.$form_id.'").addClass( "form-error" );
                    old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                    x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                    jQuery("#wdform_'.$id1.'_element'.$form_id.'").focus();
                    jQuery("#wdform_'.$id1.'_element'.$form_id.'").change(function() { if( jQuery(this).val()!="" ) jQuery(this).removeClass("form-error"); else jQuery(this).addClass("form-error");});
                    return false;
                  }
                }
                ';
              }
              break;
            }

            case 'type_wdeditor': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_size_w','w_size_h','w_title','w_required','w_class');
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
                  $param['attributes'] = $param['attributes'].' '.$attr;
              }          
              $wdformfieldsize = ($param['w_field_label_pos']=="left" ? $param['w_field_label_size']+$param['w_size_w']+10 : max($param['w_field_label_size'],$param['w_size_w']));	
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
              
              $required = ($param['w_required']=="yes" ? true : false);	
            
              $rep ='<div type="type_wdeditor" class="wdform-field"  style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              if($required) {
                $rep.='<span class="wdform-required">'.$required_sym.'</span>';
              }
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].' width: '.$param['w_size_w'].'px">';
              
              if(user_can_richedit()) {
                ob_start();
                wp_editor($param['w_title'], 'wdform_'.$id1.'_wd_editor'.$form_id, array('teeny' => FALSE, 'media_buttons' => FALSE, 'textarea_rows' => 5));
                $wd_editor = ob_get_clean();
              }
              else {
                $wd_editor = '<textarea  class="'.$param['w_class'].'" name="wdform_'.$id1.'_wd_editor'.$form_id.'" id="wdform_'.$id1.'_wd_editor'.$form_id.'" style="width: '.$param['w_size_w'].'px; height: '.$param['w_size_h'].'px; " class="mce_editable" aria-hidden="true">'.$param['w_title'].'</textarea>';
              }
              $rep.= $wd_editor.'</div></div>';
              if($required) {
                $check_js.='
                if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
                {
                  if(tinyMCE.get("wdform_'.$id1.'_wd_editor'.$form_id.'").getContent()=="")
                  {
                    alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                    jQuery("#wdform_'.$id1.'_wd_editor'.$form_id.'").addClass( "form-error" );
                    old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                    x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                    jQuery("#wdform_'.$id1.'_wd_editor'.$form_id.'").focus();
                    jQuery("#wdform_'.$id1.'_wd_editor'.$form_id.'").change(function() { if( jQuery(this).val()!="" ) jQuery(this).removeClass("form-error"); else jQuery(this).addClass("form-error");});
                    return false;
                  }
                }
                ';
              }             
              break;
            }

            case 'type_phone': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_size','w_first_val','w_title','w_mini_labels','w_required','w_unique', 'w_class');
              $temp=$params;
              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }
              $w_first_val = explode('***',$param['w_first_val']);
              $w_title = explode('***',$param['w_title']);
              
              $param['w_first_val']=(isset($_POST['wdform_'.$id1.'_element_first'.$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1.'_element_first'.$form_id])) : $w_first_val[0]).'***'.(isset($_POST['wdform_'.$id1.'_element_last'.$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1.'_element_last'.$form_id])) : $w_first_val[1]);
              
              $wdformfieldsize = ($param['w_field_label_pos']=="left" ? ($param['w_field_label_size']+$param['w_size']+65) : max($param['w_field_label_size'],($param['w_size']+65)));	
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
              $input_active = ($param['w_first_val']==$param['w_title'] ? "input_deactive" : "input_active");	
              $required = ($param['w_required']=="yes" ? true : false);	
              
              $w_first_val = explode('***',$param['w_first_val']);
              $w_title = explode('***',$param['w_title']);
              $w_mini_labels = explode('***',$param['w_mini_labels']);
          

              $rep ='<div type="type_phone" class="wdform-field" style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label" >'.$label.'</span>';
              if($required) {
                $rep.='<span class="wdform-required">'.$required_sym.'</span>';
              }
              $rep.='
              </div>
              <div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].' width: '.($param['w_size']+65).'px;">
                <div style="display: table-cell;vertical-align: middle;">
                  <div><input type="text" class="'.$input_active.'" id="wdform_'.$id1.'_element_first'.$form_id.'" name="wdform_'.$id1.'_element_first'.$form_id.'" value="'.$w_first_val[0].'" title="'.$w_title[0].'" style="width: 50px;" '.$param['attributes'].'></div>
                  <div><label class="mini_label">'.$w_mini_labels[0].'</label></div>
                </div>
                <div style="display: table-cell;vertical-align: middle;">
                  <div class="wdform_line" style="margin: 0px 4px 10px 4px; padding: 0px;">-</div>
                </div>
                <div style="display: table-cell;vertical-align: middle; width:100%;">
                  <div><input type="text" class="'.$input_active.'" id="wdform_'.$id1.'_element_last'.$form_id.'" name="wdform_'.$id1.'_element_last'.$form_id.'" value="'.$w_first_val[1].'" title="'.$w_title[1].'" style="width: 100%;" '.$param['attributes'].'></div>
                  <div><label class="mini_label">'.$w_mini_labels[1].'</label></div>
                </div>
              </div>
              </div>';
            
              if($required) {
                $check_js.='
                if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
                {
                  if(jQuery("#wdform_'.$id1.'_element_first'.$form_id.'").val()=="'.$w_title[0].'" || jQuery("#wdform_'.$id1.'_element_first'.$form_id.'").val()=="" || jQuery("#wdform_'.$id1.'_element_last'.$form_id.'").val()=="'.$w_title[1].'" || jQuery("#wdform_'.$id1.'_element_last'.$form_id.'").val()=="")
                  {
                    alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                    old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                    x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                    jQuery("#wdform_'.$id1.'_element_first'.$form_id.'").focus();
                    return false;
                  }
                  
                }
                ';
              }
              break;
            }

            case 'type_name': {
              $params_names = array('w_field_label_size', 'w_field_label_pos', 'w_first_val', 'w_title', 'w_mini_labels', 'w_size', 'w_name_format', 'w_required', 'w_unique', 'w_class');
              $temp = $params;
			  if(strpos($temp, 'w_name_fields') > -1)
				$params_names = array('w_field_label_size', 'w_field_label_pos', 'w_first_val', 'w_title', 'w_mini_labels', 'w_size', 'w_name_format', 'w_required', 'w_unique', 'w_class', 'w_name_fields');
              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }
              
              $w_first_val = explode('***',$param['w_first_val']);
              $w_title = explode('***',$param['w_title']);

              $w_mini_labels = explode('***',$param['w_mini_labels']);
              $param['w_name_fields'] =  isset($param['w_name_fields']) ? $param['w_name_fields'] : ($param['w_name_format'] == 'normal' ? 'no***no' : 'yes***yes');
			  $w_name_fields = explode('***', $param['w_name_fields']);
			  
              $element_title = isset($_POST['wdform_'.$id1.'_element_title'.$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1.'_element_title'.$form_id])) : NULL;
			  $element_middle = isset($_POST['wdform_'.$id1.'_element_middle'.$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1.'_element_middle'.$form_id])) : NULL;
              $element_first = isset($_POST['wdform_'.$id1.'_element_first'.$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1.'_element_first'.$form_id])) : NULL;
              if(isset($element_title) || isset($element_middle)) {
                $param['w_first_val']=(isset($_POST['wdform_'.$id1.'_element_first'.$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1.'_element_first'.$form_id])) : $w_first_val[0]).'***'.(isset($_POST['wdform_'.$id1.'_element_last'.$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1.'_element_last'.$form_id])) : $w_first_val[1]).'***'.(isset($_POST['wdform_'.$id1.'_element_title'.$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1.'_element_title'.$form_id])) : $w_first_val[2]).'***'.(isset($_POST['wdform_'.$id1.'_element_middle'.$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1.'_element_middle'.$form_id])) : $w_first_val[3]);
              }
              else {
                if(isset($element_first)) {
                  $param['w_first_val']=(isset($_POST['wdform_'.$id1.'_element_first'.$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1.'_element_first'.$form_id])) : $w_first_val[0]).'***'.(isset($_POST['wdform_'.$id1.'_element_last'.$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1.'_element_last'.$form_id])) : $w_first_val[1]);
                }
              }

              $required = ($param['w_required']=="yes" ? true : false);	
              $w_first_val = explode('***',$param['w_first_val']);

			  if($w_name_fields[0]== 'no' && $w_name_fields[1]== 'no' ) {
				$w_name_format = '
					<div style="display: table-cell; width:50%">
					  <div><input type="text" class="'.($w_first_val[0] == $w_title[0] ? "input_deactive" : "input_active").'" id="wdform_'.$id1.'_element_first'.$form_id.'" name="wdform_'.$id1.'_element_first'.$form_id.'" value="'.$w_first_val[0].'" title="'.$w_title[0].'"  style="width: 100%;"'.$param['attributes'].'></div>
					  <div><label class="mini_label">'.$w_mini_labels[1].'</label></div>
					</div>
					<div style="display:table-cell;"><div style="margin: 0px 8px; padding: 0px;"></div></div>
					<div  style="display: table-cell; width:50%">
					  <div><input type="text" class="'.($w_first_val[1] == $w_title[1] ? "input_deactive" : "input_active").'" id="wdform_'.$id1.'_element_last'.$form_id.'" name="wdform_'.$id1.'_element_last'.$form_id.'" value="'.$w_first_val[1].'" title="'.$w_title[1].'" style="width: 100%;" '.$param['attributes'].'></div>
					  <div><label class="mini_label">'.$w_mini_labels[2].'</label></div>
					</div>
					';
				$w_size=2*$param['w_size'];
			  }
              else {
				$first_last_size = $w_name_fields[0] == 'yes' && $w_name_fields[1] == 'no' ? 45 : 30;
				$w_name_format = '
					<div style="display: table-cell; width:'.$first_last_size.'%">
					  <div><input type="text" class="'.($w_first_val[0] == $w_title[0] ? "input_deactive" : "input_active").'" id="wdform_'.$id1.'_element_first'.$form_id.'" name="wdform_'.$id1.'_element_first'.$form_id.'" value="'.$w_first_val[0].'" title="'.$w_title[0].'" style="width:100%;"></div>
					  <div><label class="mini_label">'.$w_mini_labels[1].'</label></div>
					</div>
					<div style="display:table-cell;"><div style="margin: 0px 4px; padding: 0px;"></div></div>
					<div style="display: table-cell; width:'.$first_last_size.'%">
					  <div><input type="text" class="'.($w_first_val[1] == $w_title[1] ? "input_deactive" : "input_active").'" id="wdform_'.$id1.'_element_last'.$form_id.'" name="wdform_'.$id1.'_element_last'.$form_id.'" value="'.$w_first_val[1].'" title="'.$w_title[1].'" style="width:  100%;"></div>
					  <div><label class="mini_label">'.$w_mini_labels[2].'</label></div>
					</div>';
					
				$w_size = 2*$param['w_size'];
				if($w_name_fields[0] == 'yes') {
					$w_name_format = '
						<div style="display: table-cell;">
						  <div><input type="text" class="'.($w_first_val[2] == $w_title[2] ? "input_deactive" : "input_active").'" id="wdform_'.$id1.'_element_title'.$form_id.'" name="wdform_'.$id1.'_element_title'.$form_id.'" value="'.$w_first_val[2].'" title="'.$w_title[2].'" style="width: 40px;"></div>
						  <div><label class="mini_label">'.$w_mini_labels[0].'</label></div>
						</div>
						<div style="display:table-cell;"><div style="margin: 0px 1px; padding: 0px;"></div></div>'.$w_name_format;
					$w_size	+= 80;
                }
				if($w_name_fields[1] == 'yes') {
					$w_name_format = $w_name_format.'
						<div style="display:table-cell;"><div style="margin: 0px 4px; padding: 0px;"></div></div>
						<div style="display: table-cell; width:30%">
						  <div><input type="text" class="'.($w_first_val[3] == $w_title[3] ? "input_deactive" : "input_active").'" id="wdform_'.$id1.'_element_middle'.$form_id.'" name="wdform_'.$id1.'_element_middle'.$form_id.'" value="'.$w_first_val[3].'" title="'.$w_title[3].'" style="width: 100%;"></div>
						  <div><label class="mini_label">'.$w_mini_labels[3].'</label></div>
						</div>						
						';
					$w_size	+= $param['w_size'];	
				}		
              }
        
              $wdformfieldsize = ($param['w_field_label_pos']=="left" ? ($param['w_field_label_size']+$w_size) : max($param['w_field_label_size'],$w_size));	
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");

              $rep ='<div type="type_name" class="wdform-field"  style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              if($required) {
                $rep.='<span class="wdform-required">'.$required_sym.'</span>';
              }
              $rep.='</div>
              <div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].' width: '.$w_size.'px;">'.$w_name_format.'</div></div>';

              if($required) {
                  $check_js.='
                  if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
                  {
                    if(jQuery("#wdform_'.$id1.'_element_first'.$form_id.'").val()=="'.$w_title[0].'" || jQuery("#wdform_'.$id1.'_element_first'.$form_id.'").val()=="" || jQuery("#wdform_'.$id1.'_element_last'.$form_id.'").val()=="'.$w_title[1].'" || jQuery("#wdform_'.$id1.'_element_last'.$form_id.'").val()=="" || (jQuery("#wdform_'.$id1.'_element_title'.$form_id.'").length != 0 && (jQuery("#wdform_'.$id1.'_element_title'.$form_id.'").val()=="'.(isset($w_title[2]) ? $w_title[2] : '').'" || jQuery("#wdform_'.$id1.'_element_title'.$form_id.'").val()=="")) || (jQuery("#wdform_'.$id1.'_element_middle'.$form_id.'").length != 0 && (jQuery("#wdform_'.$id1.'_element_middle'.$form_id.'").val()=="'.(isset($w_title[3]) ? $w_title[3] : '').'" || jQuery("#wdform_'.$id1.'_element_middle'.$form_id.'").val()=="")))
                    {
						alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
						old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
						x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
						jQuery("#wdform_'.$id1.'_element_first'.$form_id.'").focus();
						return false;
                    }
                  }
                  ';	
              }
              break;
            }
            
            case 'type_address': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_size','w_mini_labels','w_disabled_fields','w_required','w_class');
              $temp=$params;
              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }
              $wdformfieldsize = ($param['w_field_label_pos']=="left" ? ($param['w_field_label_size']+$param['w_size']) : max($param['w_field_label_size'], $param['w_size']));	
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
              $required = ($param['w_required']=="yes" ? true : false);	
              $w_mini_labels = explode('***',$param['w_mini_labels']);
              $w_disabled_fields = explode('***', $param['w_disabled_fields']);
              $rep ='<div type="type_address" class="wdform-field"  style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              if ($required) {
                $rep.='<span class="wdform-required">'.$required_sym.'</span>';
              }
              $address_fields = '';
              $g = 0;
              if (isset($w_disabled_fields[0]) && $w_disabled_fields[0] == 'no') {
                $g+=2;
                $address_fields .= '<span style="float: left; width: 100%; padding-bottom: 8px; display: block;"><input type="text" id="wdform_'.$id1.'_street1'.$form_id.'" name="wdform_'.$id1.'_street1'.$form_id.'" value="'.(isset($_POST['wdform_'.$id1.'_street1'.$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1.'_street1'.$form_id])) : "").'" style="width: 100%;" '.$param['attributes'].'><label class="mini_label" >'.$w_mini_labels[0].'</label></span>';
              }
              if (isset($w_disabled_fields[1]) && $w_disabled_fields[1]=='no') {
                $g+=2;
                $address_fields .= '<span style="float: left; width: 100%; padding-bottom: 8px; display: block;"><input type="text" id="wdform_'.$id1.'_street2'.$form_id.'" name="wdform_'.($id1+1).'_street2'.$form_id.'" value="'.(isset($_POST['wdform_'.($id1+1).'_street2'.$form_id]) ? esc_html(stripslashes($_POST['wdform_'.($id1+1).'_street2'.$form_id])) : "").'" style="width: 100%;" '.$param['attributes'].'><label class="mini_label" >'.$w_mini_labels[1].'</label></span>';
              }
              if (isset($w_disabled_fields[2]) && $w_disabled_fields[2]=='no') {
                $g++;
                $address_fields .= '<span style="float: left; width: 48%; padding-bottom: 8px;"><input type="text" id="wdform_'.$id1.'_city'.$form_id.'" name="wdform_'.($id1+2).'_city'.$form_id.'" value="'.(isset($_POST['wdform_'.($id1+2).'_city'.$form_id]) ? esc_html(stripslashes($_POST['wdform_'.($id1+2).'_city'.$form_id])) : "").'" style="width: 100%;" '.$param['attributes'].'><label class="mini_label" >'.$w_mini_labels[2].'</label></span>';
              }
              if (isset($w_disabled_fields[3]) && $w_disabled_fields[3]=='no') {
                $g++;
                $w_states = array("","Alabama","Alaska", "Arizona","Arkansas","California","Colorado","Connecticut","Delaware","District Of Columbia","Florida","Georgia","Hawaii","Idaho","Illinois","Indiana","Iowa","Kansas","Kentucky","Louisiana","Maine","Maryland","Massachusetts","Michigan","Minnesota","Mississippi","Missouri","Montana","Nebraska","Nevada","New Hampshire","New Jersey","New Mexico","New York","North Carolina","North Dakota","Ohio","Oklahoma","Oregon","Pennsylvania","Rhode Island","South Carolina","South Dakota","Tennessee","Texas","Utah","Vermont","Virginia","Washington","West Virginia","Wisconsin","Wyoming");	
                $w_state_options = '';
                $post_state = isset($_POST['wdform_'.($id1+3).'_state'.$form_id]) ? esc_html(stripslashes($_POST['wdform_'.($id1+3).'_state'.$form_id])) : "";
                foreach($w_states as $w_state) {
                  if($w_state == $post_state) {
                    $selected = 'selected="selected"';
                  }
                  else {
                    $selected = '';
                  }
                  $w_state_options .= '<option value="'.$w_state.'" '.$selected.'>'.$w_state.'</option>';
                }
                if(isset($w_disabled_fields[5]) && $w_disabled_fields[5]=='yes' && isset($w_disabled_fields[6]) && $w_disabled_fields[6]=='yes') {
                  $address_fields .= '<span style="float: '.(($g%2==0) ? 'right' : 'left').'; width: 48%; padding-bottom: 8px;"><select type="text" id="wdform_'.$id1.'_state'.$form_id.'" name="wdform_'.($id1+3).'_state'.$form_id.'" style="width: 100%;" '.$param['attributes'].'>'.$w_state_options.'</select><label class="mini_label" style="display: block;" id="'.$id1.'_mini_label_state">'.$w_mini_labels[3].'</label></span>';
                }
                else {
                  $address_fields .= '<span style="float: '.(($g%2==0) ? 'right' : 'left').'; width: 48%; padding-bottom: 8px;"><input type="text" id="wdform_'.$id1.'_state'.$form_id.'" name="wdform_'.($id1+3).'_state'.$form_id.'" value="'.(isset($_POST['wdform_'.($id1+3).'_state'.$form_id]) ? esc_html(stripslashes($_POST['wdform_'.($id1+3).'_state'.$form_id])) : "").'" style="width: 100%;" '.$param['attributes'].'><label class="mini_label">'.$w_mini_labels[3].'</label></span>';
                }
              }
              if (isset($w_disabled_fields[4]) && $w_disabled_fields[4]=='no') {
                $g++;
                $address_fields .= '<span style="float: '.(($g%2==0) ? 'right' : 'left').'; width: 48%; padding-bottom: 8px;"><input type="text" id="wdform_'.$id1.'_postal'.$form_id.'" name="wdform_'.($id1+4).'_postal'.$form_id.'" value="'.(isset($_POST['wdform_'.($id1+4).'_postal'.$form_id]) ? esc_html(stripslashes($_POST['wdform_'.($id1+4).'_postal'.$form_id])) : "").'" style="width: 100%;" '.$param['attributes'].'><label class="mini_label">'.$w_mini_labels[4].'</label></span>';
              }
              $w_countries = array("","Afghanistan","Albania","Algeria","Andorra","Angola","Antigua and Barbuda","Argentina","Armenia","Australia","Austria","Azerbaijan","Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bhutan","Bolivia","Bosnia and Herzegovina","Botswana","Brazil","Brunei","Bulgaria","Burkina Faso","Burundi","Cambodia","Cameroon","Canada","Cape Verde","Central African Republic","Chad","Chile","China","Colombia","Comoros","Congo (Brazzaville)","Congo","Costa Rica","Cote d'Ivoire","Croatia","Cuba","Cyprus","Czech Republic","Denmark","Djibouti","Dominica","Dominican Republic","East Timor (Timor Timur)","Ecuador","Egypt","El Salvador","Equatorial Guinea","Eritrea","Estonia","Ethiopia","Fiji","Finland","France","Gabon","Gambia, The","Georgia","Germany","Ghana","Greece","Grenada","Guatemala","Guinea","Guinea-Bissau","Guyana","Haiti","Honduras","Hungary","Iceland","India","Indonesia","Iran","Iraq","Ireland","Israel","Italy","Jamaica","Japan","Jordan","Kazakhstan","Kenya","Kiribati","Korea, North","Korea, South","Kuwait","Kyrgyzstan","Laos","Latvia","Lebanon","Lesotho","Liberia","Libya","Liechtenstein","Lithuania","Luxembourg","Macedonia","Madagascar","Malawi","Malaysia","Maldives","Mali","Malta","Marshall Islands","Mauritania","Mauritius","Mexico","Micronesia","Moldova","Monaco","Mongolia","Morocco","Mozambique","Myanmar","Namibia","Nauru","Nepal","Netherlands","New Zealand","Nicaragua","Niger","Nigeria","Norway","Oman","Pakistan","Palau","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Poland","Portugal","Qatar","Romania","Russia","Rwanda","Saint Kitts and Nevis","Saint Lucia","Saint Vincent","Samoa","San Marino","Sao Tome and Principe","Saudi Arabia","Senegal","Serbia and Montenegro","Seychelles","Sierra Leone","Singapore","Slovakia","Slovenia","Solomon Islands","Somalia","South Africa","Spain","Sri Lanka","Sudan","Suriname","Swaziland","Sweden","Switzerland","Syria","Taiwan","Tajikistan","Tanzania","Thailand","Togo","Tonga","Trinidad and Tobago","Tunisia","Turkey","Turkmenistan","Tuvalu","Uganda","Ukraine","United Arab Emirates","United Kingdom","United States","Uruguay","Uzbekistan","Vanuatu","Vatican City","Venezuela","Vietnam","Yemen","Zambia","Zimbabwe");	
              $w_options = '';
              $post_country = isset($_POST['wdform_'.($id1+5).'_country'.$form_id]) ? esc_html(stripslashes($_POST['wdform_'.($id1+5).'_country'.$form_id])) : "";
              foreach($w_countries as $w_country) {              
                if($w_country == $post_country) {
                  $selected = 'selected="selected"';
                }
                else {
                  $selected = '';
                }
                $w_options .= '<option value="'.$w_country.'" '.$selected.'>'.$w_country.'</option>';
              }
              if (isset($w_disabled_fields[5]) && $w_disabled_fields[5]=='no') {
                $g++;
                $address_fields .= '<span style="float: '.(($g%2==0) ? 'right' : 'left').'; width: 48%; padding-bottom: 8px;display: inline-block;"><select type="text" id="wdform_'.$id1.'_country'.$form_id.'" name="wdform_'.($id1+5).'_country'.$form_id.'" style="width:100%" '.$param['attributes'].'>'.$w_options.'</select><label class="mini_label">'.$w_mini_labels[5].'</label></span>';
              }				
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].' width: '.$param['w_size'].'px;"><div>
              '.$address_fields.'</div></div></div>';
              
              if ($required) {
                $check_js.='
                if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
                {
                  if(jQuery("#wdform_'.$id1.'_street1'.$form_id.'").val()=="" || (jQuery("#wdform_'.$id1.'_street1'.$form_id.'").val()=="" && jQuery("#wdform_'.$id1.'_street2'.$form_id.'").val()=="") || jQuery("#wdform_'.$id1.'_city'.$form_id.'").val()=="" || jQuery("#wdform_'.$id1.'_state'.$form_id.'").val()=="" || jQuery("#wdform_'.$id1.'_postal'.$form_id.'").val()=="" || jQuery("#wdform_'.$id1.'_country'.$form_id.'").val()=="")
                  {
                    alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                    old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                    x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                    jQuery("#wdform_'.$id1.'_street1'.$form_id.'").focus();
                    return false;
                  }
                  
                }
                ';
              }
              $post = isset($_POST['wdform_'.($id1+5).'_country'.$form_id]) ? esc_html(stripslashes($_POST['wdform_'.($id1+5).'_country'.$form_id])) : NULL;
              if(isset($post)) {
                $onload_js .=' jQuery("#wdform_'.$id1.'_country'.$form_id.'").val("'.(isset($_POST['wdform_'.($id1+5)."_country".$form_id]) ? esc_html(stripslashes($_POST['wdform_'.($id1+5)."_country".$form_id])) : '').'");';
              }
              if (isset($w_disabled_fields[6]) && $w_disabled_fields[6]=='yes') {
                $onload_js .=' jQuery("#wdform_'.$id1.'_country'.$form_id.'").change(function() { 
                if( jQuery(this).val()=="United States") 
                {
                  jQuery("#wdform_'.$id1.'_state'.$form_id.'").parent().append("<select type=\"text\" id=\"wdform_'.$id1.'_state'.$form_id.'\" name=\"wdform_'.($id1+3).'_state'.$form_id.'\" style=\"width: 100%;\" '.$param['attributes'].'>'.addslashes($w_state_options).'</select><label class=\"mini_label\" style=\"display: block;\" id=\"'.$id1.'_mini_label_state\">'.$w_mini_labels[3].'</label>");
                  jQuery("#wdform_'.$id1.'_state'.$form_id.'").parent().children("input:first, label:first").remove();
                }
                else
                {
                  if(jQuery("#wdform_'.$id1.'_state'.$form_id.'").attr("tagName")=="SELECT")
                  {
              
                    jQuery("#wdform_'.$id1.'_state'.$form_id.'").parent().append("<input type=\"text\" id=\"wdform_'.$id1.'_state'.$form_id.'\" name=\"wdform_'.($id1+3).'_state'.$form_id.'\" value=\"'.(isset($_POST['wdform_'.($id1+3).'_state'.$form_id]) ? esc_html(stripslashes($_POST['wdform_'.($id1+3).'_state'.$form_id])) : "").'\" style=\"width: 100%;\" '.$param['attributes'].'><label class=\"mini_label\">'.$w_mini_labels[3].'</label>");
                    jQuery("#wdform_'.$id1.'_state'.$form_id.'").parent().children("select:first, label:first").remove();	
                  }
                }
              
              });';
              }
              break;
            }

            case 'type_submitter_mail': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_size','w_first_val','w_title','w_required','w_unique', 'w_class');
              $temp=$params;
              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }
              
              $param['w_first_val']=(isset($_POST['wdform_'.$id1.'_element'.$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1.'_element'.$form_id])) : $param['w_first_val']);
                
              $wdformfieldsize = ($param['w_field_label_pos']=="left" ? ($param['w_field_label_size']+$param['w_size']) : max($param['w_field_label_size'], $param['w_size']));	
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
              $input_active = ($param['w_first_val']==$param['w_title'] ? "input_deactive" : "input_active");	
              $required = ($param['w_required']=="yes" ? true : false);	
            
              $rep ='<div type="type_submitter_mail" class="wdform-field"  style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              if($required) {
                $rep.='<span class="wdform-required">'.$required_sym.'</span>';
              }
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].' width: '.$param['w_size'].'px;"><input type="text" class="'.$input_active.'" id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.$param['w_first_val'].'" title="'.$param['w_title'].'"  style="width: 100%;" '.$param['attributes'].'></div></div>';
              
              if($required) {
                $check_js.='
                if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
                {
                  if(jQuery("#wdform_'.$id1.'_element'.$form_id.'").val()=="'.$param['w_title'].'" || jQuery("#wdform_'.$id1.'_element'.$form_id.'").val()=="")
                  {
                    alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                    jQuery("#wdform_'.$id1.'_element'.$form_id.'").addClass( "form-error" );
                    old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                    x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                    jQuery("#wdform_'.$id1.'_element'.$form_id.'").focus();
                    jQuery("#wdform_'.$id1.'_element'.$form_id.'").change(function() { if( jQuery(this).val()!="" ) jQuery(this).removeClass("form-error"); else jQuery(this).addClass("form-error");});
                    return false;
                  }
                }
                ';
              }
              $check_js.='
              if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
              {
              
              if(jQuery("#wdform_'.$id1.'_element'.$form_id.'").val()!="" && jQuery("#wdform_'.$id1.'_element'.$form_id.'").val().search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) == -1 )
                {
                  alert("' . addslashes(__("This is not a valid email address.", 'form_maker')) . '");
                  old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                  x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                  jQuery("#wdform_'.$id1.'_element'.$form_id.'").focus();
                  return false;
                }
              
              }
              ';		
              
              break;
            }

            case 'type_checkbox': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_flow','w_choices','w_choices_checked','w_rowcol', 'w_required','w_randomize','w_allow_other','w_allow_other_num','w_class');
              $temp=$params;

              if(strpos($temp, 'w_field_option_pos') > -1)
				$params_names=array('w_field_label_size','w_field_label_pos','w_field_option_pos','w_flow','w_choices','w_choices_checked','w_rowcol', 'w_required','w_randomize','w_allow_other','w_allow_other_num', 'w_value_disabled','w_choices_value', 'w_choices_params', 'w_class');
              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              
              if($temp) {
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }
			  
              if(!isset($param['w_value_disabled']))
						$param['w_value_disabled'] = 'no';
				
			  if(!isset($param['w_field_option_pos']))
						$param['w_field_option_pos'] = 'left';
						
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
			  $param['w_field_option_pos1'] = ($param['w_field_option_pos']=="right" ? "style='float: none
			  !important;'" : "");
			  $param['w_field_option_pos2'] = ($param['w_field_option_pos']=="right" ? "style='float: left !important; margin:3px 8px 0 0 !important; display: inline-block !important;'" : "");	
              $required = ($param['w_required']=="yes" ? true : false);	
              $param['w_choices']	= explode('***',$param['w_choices']);
              $param['w_choices_checked']	= explode('***',$param['w_choices_checked']);
			  
               if(isset($param['w_choices_value']))
				{
				   $param['w_choices_value'] = explode('***',$param['w_choices_value']);
				   $param['w_choices_params'] = explode('***',$param['w_choices_params']);	
				}
              $post_value = isset($_POST["counter".$form_id]) ? esc_html($_POST["counter".$form_id]) : NULL;
              $is_other=false;

              if (isset($post_value)) {
                if($param['w_allow_other']=="yes") {
                  $is_other = FALSE;
                  $other_element = isset($_POST['wdform_'.$id1."_other_input".$form_id]) ? esc_html($_POST['wdform_'.$id1."_other_input".$form_id]) : NULL;
                  if (isset($other_element)) {
                    $is_other = TRUE;
                  }
                }
              }
              else {
                $is_other=($param['w_allow_other']=="yes" && $param['w_choices_checked'][$param['w_allow_other_num']]=='true') ;
              }
              $rep='<div type="type_checkbox" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              if($required) {
                $rep.='<span class="wdform-required">'.$required_sym.'</span>';
              }
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].';">';
            
              $rep.='<div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).'; vertical-align:top">';
			  
              $total_queries = 0;
              foreach ($param['w_choices'] as $key => $choice) {
			  
			     $key1 = $key + $total_queries;
				 if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])
						{
							$choices_labels =array();
							$choices_values = array();

							$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
							$where = (str_replace(array('[',']'), '', $w_choices_params[0]) ? ' WHERE '.str_replace(array('[',']'), '', $w_choices_params[0]) : '');
							$w_choices_params = explode('[db_info]',$w_choices_params[1]);
							
							$order_by = str_replace(array('[',']'), '', $w_choices_params[0]);
							$db_info = str_replace(array('[',']'), '', $w_choices_params[1]);
						
		
							$label_table_and_column = explode(':',str_replace(array('[',']'), '', $choice));
							$table = $label_table_and_column[0];
							$label_column = $label_table_and_column[1];
							if($label_column)
							{
								$choices_labels = $this->model->select_data_from_db_for_labels($db_info, $label_column, $table, $where, $order_by);					
							}	

							$value_table_and_column = explode(':',str_replace(array('[',']'), '', $param['w_choices_value'][$key]));
							$value_column = $value_table_and_column[1];

							if($value_column)
							{
								$choices_values = $this->model->select_data_from_db_for_values($db_info, $value_column, $table, $where, $order_by);
							}		
							$columns_count_checkbox = count($choices_labels)>0 ?  count($choices_labels) : count($choices_values);
							
							if(array_filter($choices_labels) || array_filter($choices_values))
							{
								$total_queries = $total_queries + $columns_count_checkbox-1;
								
								if(!isset($post_value))
									$param['w_choices_checked'][$key]=($param['w_choices_checked'][$key]=='true' ? 'checked="checked"' : '');
									
								for($k=0; $k<$columns_count_checkbox; $k++)
								{
									$choice_label = isset($choices_labels[$k]) ? $choices_labels[$k] : '';
									$choice_value = isset($choices_values[$k]) ? $choices_values[$k] : $choice_label;
										
									if(($key1+$k)%$param['w_rowcol']==0 && ($key1+$k)>0)
										$rep.='</div><div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';  vertical-align:top">';

									if(isset($post_value))
									{
										$post_valuetemp=$_REQUEST['wdform_'.$id1."_element".$form_id.($key1+$k)];
										$param['w_choices_checked'][$key]=(isset($post_valuetemp) ? 'checked="checked"' : '');
									}

									$rep.='<div style="display: '.($param['w_flow']!='hor' ? 'table-cell' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" '.$param['w_field_option_pos1'].'>'.$choice_label[0].'</label><div class="checkbox-div forlabs" '.$param['w_field_option_pos2'].'><input type="checkbox" '.(($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key) ? 'other="1"' : ''	).' id="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" name="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" value="'.htmlspecialchars($choice_value[0]).'" '.(($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key) ? 'onclick="if(set_checked(&quot;wdform_'.$id1.'&quot;,&quot;'.($key1+$k).'&quot;,&quot;'.$form_id.'&quot;)) show_other_input(&quot;wdform_'.$id1.'&quot;,&quot;'.$form_id.'&quot;);"' : '').' '.$param['w_choices_checked'][$key].' '.$param['attributes'].'><label for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'"></label></div></div>';
									
								}
							}	
						}	
			  else
			  {
                if ($key1%$param['w_rowcol']==0 && $key1>0) {
                  $rep.='</div><div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';  vertical-align:top">';
                }
                if(!isset($post_value)) {
                  $param['w_choices_checked'][$key]=($param['w_choices_checked'][$key]=='true' ? 'checked="checked"' : '');
                }
                else {
                  $post_valuetemp = isset($_POST['wdform_'.$id1."_element".$form_id.$key]) ? esc_html(stripslashes($_POST['wdform_'.$id1."_element".$form_id.$key])) : NULL;
                  $param['w_choices_checked'][$key]=(isset($post_valuetemp) ? 'checked="checked"' : '');
                }
                $choice_value = isset($param['w_choices_value']) ? $param['w_choices_value'][$key] : $choice;
				
                $rep.='<div style="display: '.($param['w_flow']!='hor' ? 'table-cell' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.$key1.'" '.$param['w_field_option_pos1'].'>'.$choice.'</label><div class="checkbox-div forlabs" '.$param['w_field_option_pos2'].'><input type="checkbox" '.(($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key) ? 'other="1"' : ''	).' id="wdform_'.$id1.'_element'.$form_id.''.$key1.'" name="wdform_'.$id1.'_element'.$form_id.''.$key1.'" value="'.htmlspecialchars($choice_value).'" '.(($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key) ? 'onclick="if(set_checked(&quot;wdform_'.$id1.'&quot;,&quot;'.$key1.'&quot;,&quot;'.$form_id.'&quot;)) show_other_input(&quot;wdform_'.$id1.'&quot;,&quot;'.$form_id.'&quot;);"' : '').' '.$param['w_choices_checked'][$key].' '.$param['attributes'].'><label for="wdform_'.$id1.'_element'.$form_id.''.$key1.'"></label></div></div>';
				
				$param['w_allow_other_num'] = $param['w_allow_other_num']==$key ? $key1 : $param['w_allow_other_num'];
              }
			}
              $rep.='</div>';

              $rep.='</div></div>';
              
              if($required) {
                $check_js.='
                if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
                {
                  if(x.find(jQuery("div[wdid='.$id1.'] input:checked")).length == 0 || jQuery("#wdform_'.$id1.'_other_input'.$form_id.'").val() == "")
                  {
                    alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                    old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                    x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                    
                    return false;
                  }						
                }
                ';
              }
              if($is_other) {
                $onload_js .='show_other_input("wdform_'.$id1.'","'.$form_id.'"); jQuery("#wdform_'.$id1.'_other_input'.$form_id.'").val("'.(isset($_POST['wdform_'.$id1."_other_input".$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1."_other_input".$form_id])) : '').'");';
              }
              if($param['w_randomize']=='yes')
              {
                $onload_js .='jQuery("#form'.$form_id.' div[wdid='.$id1.'] .wdform-element-section> div").shuffle();
                ';
              }
              $onsubmit_js.='
                jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_allow_other'.$form_id.'\" value = \"'.$param['w_allow_other'].'\" />").appendTo("#form'.$form_id.'");
                jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_allow_other_num'.$form_id.'\" value = \"'.$param['w_allow_other_num'].'\" />").appendTo("#form'.$form_id.'");
                ';
              break;
            }

            case 'type_radio': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_flow','w_choices','w_choices_checked','w_rowcol', 'w_required','w_randomize','w_allow_other','w_allow_other_num','w_class');
              $temp=$params;
			  if(strpos($temp, 'w_field_option_pos') > -1)
				$params_names=array('w_field_label_size','w_field_label_pos','w_field_option_pos','w_flow','w_choices','w_choices_checked','w_rowcol', 'w_required','w_randomize','w_allow_other','w_allow_other_num','w_value_disabled','w_choices_value', 'w_choices_params','w_class');

              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              
              if($temp) {
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }
              if(!isset($param['w_value_disabled']))
						$param['w_value_disabled'] = 'no';
				
			  if(!isset($param['w_field_option_pos']))
						$param['w_field_option_pos'] = 'left';
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
			  $param['w_field_option_pos1'] = ($param['w_field_option_pos']=="right" ? "style='float: none
			  !important;'" : "");
			  $param['w_field_option_pos2'] = ($param['w_field_option_pos']=="right" ? "style='float: left !important; margin:3px 8px 0 0 !important; display: inline-block !important;'" : "");
              $required = ($param['w_required']=="yes" ? true : false);	
              $param['w_choices']	= explode('***',$param['w_choices']);
              $param['w_choices_checked']	= explode('***',$param['w_choices_checked']);
              if(isset($param['w_choices_value']))
			  {
				$param['w_choices_value'] = explode('***',$param['w_choices_value']);
				$param['w_choices_params'] = explode('***',$param['w_choices_params']);	
			  }
              $post_value = isset($_POST["counter".$form_id]) ? esc_html($_POST["counter".$form_id]) : NULL;
              $is_other=false;

              if(isset($post_value)) {
                if($param['w_allow_other']=="yes") {
                  $is_other=false;
                  $other_element = isset($_POST['wdform_'.$id1."_other_input".$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1."_other_input".$form_id])) : "";
                  if(isset($other_element)) {
                    $is_other=true;
                  }
                }
              }
              else {
                $is_other=($param['w_allow_other']=="yes" && $param['w_choices_checked'][$param['w_allow_other_num']]=='true') ;
              }
              $rep='<div type="type_radio" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              if($required) {
                $rep.='<span class="wdform-required">'.$required_sym.'</span>';
              }
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].';">';
            
              $rep.='<div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).'; vertical-align:top">';

			  $total_queries =0;
              foreach($param['w_choices'] as $key => $choice) {
			  
			    $key1 = $key + $total_queries;
			  
			        if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])
						{	
							$choices_labels =array();	
							$choices_values =array();	
							$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
							$where = (str_replace(array('[',']'), '', $w_choices_params[0]) ? ' WHERE '.str_replace(array('[',']'), '', $w_choices_params[0]) : '');
							$w_choices_params = explode('[db_info]',$w_choices_params[1]);
							$order_by = str_replace(array('[',']'), '', $w_choices_params[0]);
							$db_info = str_replace(array('[',']'), '', $w_choices_params[1]);
							
							
							$label_table_and_column = explode(':',str_replace(array('[',']'), '', $choice));
							$table = $label_table_and_column[0];
							$label_column = $label_table_and_column[1];
							if($label_column)
							{
								$choices_labels = $this->model->select_data_from_db_for_labels($db_info, $label_column, $table, $where, $order_by);					
							}	

							$value_table_and_column = explode(':',str_replace(array('[',']'), '', $param['w_choices_value'][$key]));
							$value_column = $value_table_and_column[1];

							if($value_column)
							{
								$choices_values = $this->model->select_data_from_db_for_values($db_info, $value_column, $table, $where, $order_by);
							}	
							
							$columns_count_radio = count($choices_labels)>0 ?  count($choices_labels) : count($choices_values);
							if(array_filter($choices_labels) || array_filter($choices_values))
							{
								$total_queries = $total_queries + $columns_count_radio-1;
								
								if(!isset($post_value))
									$param['w_choices_checked'][$key]=($param['w_choices_checked'][$key]=='true' ? 'checked="checked"' : '');
									
								for($k=0; $k<$columns_count_radio; $k++)
								{
									$choice_label = isset($choices_labels[$k]) ? $choices_labels[$k] : '';
									$choice_value = isset($choices_values[$k]) ? $choices_values[$k] : $choice_label;
										
									if(($key1+$k)%$param['w_rowcol']==0 && ($key1+$k)>0)
										$rep.='</div><div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';  vertical-align:top">';

									if(isset($post_value))
									{
										$post_valuetemp=$_REQUEST['wdform_'.$id1."_element".$form_id];
										$param['w_choices_checked'][$key]=(isset($post_valuetemp) ? 'checked="checked"' : '');
									}
									
									$rep.='<div style="display: '.($param['w_flow']!='hor' ? 'table-cell' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" '.$param['w_field_option_pos1'].'>'.$choice_label[0].'</label><div class="radio-div forlabs" '.$param['w_field_option_pos2'].'><input type="radio" '.(($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key) ? 'other="1"' : ''	).' id="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.htmlspecialchars($choice_value[0]).'" onclick="set_default(&quot;wdform_'.$id1.'&quot;,&quot;'.($key1+$k).'&quot;,&quot;'.$form_id.'&quot;); '.(($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key) ? 'show_other_input(&quot;wdform_'.$id1.'&quot;,&quot;'.$form_id.'&quot;);' : '').'" '.$param['w_choices_checked'][$key].' '.$param['attributes'].'><label for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'"></label></div></div>';
								}
							}	
						}	
		    else
			    {
			  
                if($key1%$param['w_rowcol']==0 && $key1>0) {
                  $rep.='</div><div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';  vertical-align:top">';
                }
                if(!isset($post_value)) {
                  $param['w_choices_checked'][$key]=($param['w_choices_checked'][$key]=='true' ? 'checked="checked"' : '');
                }
                else {
                  $param['w_choices_checked'][$key] = (htmlspecialchars($choice) == htmlspecialchars(isset($_POST['wdform_'.$id1."_element".$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1."_element".$form_id])) : "") ? 'checked="checked"' : '');
                }
				$choice_value = isset($param['w_choices_value']) ? $param['w_choices_value'][$key] : $choice;
                $rep.='<div style="display: '.($param['w_flow']!='hor' ? 'table-cell' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.$key1.'" '.$param['w_field_option_pos1'].'>'.$choice.'</label><div class="radio-div forlabs" '.$param['w_field_option_pos2'].'><input type="radio" '.(($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key) ? 'other="1"' : ''	).' id="wdform_'.$id1.'_element'.$form_id.''.$key1.'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.htmlspecialchars($choice_value).'" onclick="set_default(&quot;wdform_'.$id1.'&quot;,&quot;'.$key1.'&quot;,&quot;'.$form_id.'&quot;); '.(($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key) ? 'show_other_input(&quot;wdform_'.$id1.'&quot;,&quot;'.$form_id.'&quot;);' : '').'" '.$param['w_choices_checked'][$key].' '.$param['attributes'].'><label for="wdform_'.$id1.'_element'.$form_id.''.$key1.'"></label></div></div>';
				$param['w_allow_other_num'] = $param['w_allow_other_num']==$key ? $key1 : $param['w_allow_other_num'];
				}
             }
              $rep.='</div>';

              $rep.='</div></div>';
            
              if($required) {
                $check_js.='
                if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none") {
                  if(x.find(jQuery("div[wdid='.$id1.'] input:checked")).length == 0 || jQuery("#wdform_'.$id1.'_other_input'.$form_id.'").val() == "") {
                    alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                    old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                    x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                    
                    return false;
                  }						
                }';
              }
              if($is_other) {
                $onload_js .='show_other_input("wdform_'.$id1.'","'.$form_id.'"); jQuery("#wdform_'.$id1.'_other_input'.$form_id.'").val("'.(isset($_POST['wdform_'.$id1."_other_input".$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1."_other_input".$form_id])) : '').'");';
              }
              if($param['w_randomize']=='yes')
              {
                $onload_js .='jQuery("#form'.$form_id.' div[wdid='.$id1.'] .wdform-element-section> div").shuffle();
                ';
              }
              $onsubmit_js.='
                jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_allow_other'.$form_id.'\" value = \"'.$param['w_allow_other'].'\" />").appendTo("#form'.$form_id.'");
                jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_allow_other_num'.$form_id.'\" value = \"'.$param['w_allow_other_num'].'\" />").appendTo("#form'.$form_id.'");
                ';
              break;
            }

            case 'type_own_select': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_size','w_choices','w_choices_checked', 'w_choices_disabled','w_required','w_class');
              $temp=$params;
			  
               if(strpos($temp, 'w_choices_value') > -1)
				  $params_names=array('w_field_label_size','w_field_label_pos','w_size','w_choices','w_choices_checked', 'w_choices_disabled', 'w_required', 'w_value_disabled', 'w_choices_value', 'w_choices_params', 'w_class');
				  
              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' '.$attr;
              }
              
              $wdformfieldsize = ($param['w_field_label_pos']=="left" ? ($param['w_field_label_size']+$param['w_size']) : max($param['w_field_label_size'], $param['w_size']));	
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
              $required = ($param['w_required']=="yes" ? true : false);	
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
					
              $post_value = isset($_POST["counter".$form_id]) ? esc_html($_POST["counter".$form_id]) : NULL;
              
              $rep='<div type="type_own_select" class="wdform-field"  style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              if($required) {
                $rep.='<span class="wdform-required">'.$required_sym.'</span>';
              }
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].' width: '.($param['w_size']).'px; "><select id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" style="width: 100%"  '.$param['attributes'].'>';
            foreach($param['w_choices'] as $key => $choice)
					{
						if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])
						{
							$choices_labels =array();
							$choices_values = array();
							$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
							$where = (str_replace(array('[',']'), '', $w_choices_params[0]) ? ' WHERE '.str_replace(array('[',']'), '', $w_choices_params[0]) : '');
							$w_choices_params = explode('[db_info]',$w_choices_params[1]);
							$order_by = str_replace(array('[',']'), '', $w_choices_params[0]);
							$db_info = str_replace(array('[',']'), '', $w_choices_params[1]);
							
							
							
							$label_table_and_column = explode(':',str_replace(array('[',']'), '', $choice));
							$table = $label_table_and_column[0];
							$label_column = $label_table_and_column[1];
							if($label_column)
							{
								$choices_labels = $this->model->select_data_from_db_for_labels($db_info, $label_column, $table, $where, $order_by);				
							}	

							$value_table_and_column = explode(':',str_replace(array('[',']'), '', $param['w_choices_value'][$key]));
							$value_column = $param['w_choices_disabled'][$key]=="true" ? '' : $value_table_and_column[1];

							if($value_column)
							{
								$choices_values = $this->model->select_data_from_db_for_values($db_info, $value_column, $table, $where, $order_by);
							}	

							$columns_count = count($choices_labels)>0 ?  count($choices_labels) : count($choices_values);
							if(array_filter($choices_labels) || array_filter($choices_values))
								for($k=0; $k<$columns_count; $k++)
								{
									$choice_label = isset($choices_labels[$k]) ? $choices_labels[$k] : '';
									$choice_value = isset($choices_values[$k]) ? $choices_values[$k] : ($param['w_choices_disabled'][$key]=="true" ? '' : $choice_label);
									if(!isset($post_value))
										$param['w_choices_checked'][$key]=(($param['w_choices_checked'][$key]=='true' && $k == 0) ? 'selected="selected"' : '');
									else
										$param['w_choices_checked'][$key]=($choice_value==htmlspecialchars($_REQUEST['wdform_'.$id1."_element".$form_id]) ? 'selected="selected"' : '');

									$rep.='<option value="'.htmlspecialchars($choice_value[0]).'" '.$param['w_choices_checked'][$key].'>'.$choice_label[0].'</option>';
												
								}		
						}
						else
						{
							if(!isset($post_value))
								$param['w_choices_checked'][$key]=($param['w_choices_checked'][$key]=='true' ? 'selected="selected"' : '');
							else
								$param['w_choices_checked'][$key]=(htmlspecialchars($choice)==htmlspecialchars($_REQUEST['wdform_'.$id1."_element".$form_id]) ? 'selected="selected"' : '');
				
							$choice_value = $param['w_choices_disabled'][$key]=="true" ? '' : (isset($param['w_choices_value']) ? $param['w_choices_value'][$key] : $choice);
								
							$rep.='<option value="'.htmlspecialchars($choice_value).'" '.$param['w_choices_checked'][$key].'>'.$choice.'</option>';	
							
						}

					}
              $rep.='</select></div></div>';
              
              if($required) {
                $check_js.='
                if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
                {
                  if( jQuery("#wdform_'.$id1.'_element'.$form_id.'").val()=="")
                    {
                      alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                      jQuery("#wdform_'.$id1.'_element'.$form_id.'").addClass( "form-error" );
                      old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                      x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                      jQuery("#wdform_'.$id1.'_element'.$form_id.'").focus();
                      jQuery("#wdform_'.$id1.'_element'.$form_id.'").change(function() { if( jQuery(this).val()!="" ) jQuery(this).removeClass("form-error"); else jQuery(this).addClass("form-error");});
                      return false;
                    }
                }
                ';		
              }
              break;
            }
            
            case 'type_country': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_size','w_countries','w_required','w_class');
              $temp=$params;
              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }

              $wdformfieldsize = ($param['w_field_label_pos']=="left" ? ($param['w_field_label_size']+$param['w_size']) : max($param['w_field_label_size'], $param['w_size']));	
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
              $required = ($param['w_required']=="yes" ? true : false);	
              $param['w_countries']	= explode('***',$param['w_countries']);
              
              $post_value = isset($_POST["counter".$form_id]) ? esc_html($_POST["counter".$form_id]) : NULL;
              $selected='';
     
              $rep='<div type="type_country" class="wdform-field"  style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              if($required) {
                $rep.='<span class="wdform-required">'.$required_sym.'</span>';
              }
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].' width: '.$param['w_size'].'px;"><select id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" style="width: 100%;"  '.$param['attributes'].'>';
              foreach($param['w_countries'] as $key => $choice) {
                if(isset($post_value)) {
                  $selected = (htmlspecialchars($choice) == htmlspecialchars(isset($_POST['wdform_'.$id1."_element".$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1."_element".$form_id])) : "") ? 'selected="selected"' : '');
                }
                $choice_value=$choice;
                $rep.='<option value="'.$choice_value.'" '.$selected.'>'.$choice.'</option>';
              }
              $rep.='</select></div></div>';
              
              if($required) {
                $check_js.='
                if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
                {
                  if(jQuery("#wdform_'.$id1.'_element'.$form_id.'").val()=="")
                    {
                      alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                      jQuery("#wdform_'.$id1.'_element'.$form_id.'").addClass( "form-error" );
                      old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                      x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                      jQuery("#wdform_'.$id1.'_element'.$form_id.'").focus();
                      jQuery("#wdform_'.$id1.'_element'.$form_id.'").change(function() { if( jQuery(this).val()!="" ) jQuery(this).removeClass("form-error"); else jQuery(this).addClass("form-error");});
                      return false;
                    }
                }
                ';		
              }
              break;
            }
            
            case 'type_time': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_time_type','w_am_pm','w_sec','w_hh','w_mm','w_ss','w_mini_labels','w_required','w_class');
              $temp=$params;

              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              
              if($temp) {
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }
            
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
              $required = ($param['w_required']=="yes" ? true : false);	
              $w_mini_labels = explode('***',$param['w_mini_labels']);

              $w_sec = '';
              $w_sec_label='';
            
              if($param['w_sec']=='1') {
                $w_sec = '<div align="center" style="display: table-cell;"><span class="wdform_colon" style="vertical-align: middle;">&nbsp;:&nbsp;</span></div><div style="display: table-cell;"><input type="text" value="'.(isset($_POST['wdform_'.$id1."_ss".$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1."_ss".$form_id])) : $param['w_ss']).'" class="time_box" id="wdform_'.$id1.'_ss'.$form_id.'" name="wdform_'.$id1.'_ss'.$form_id.'" onkeypress="return check_second(event, &quot;wdform_'.$id1.'_ss'.$form_id.'&quot;)" '.$param['attributes'].'></div>';
                
                $w_sec_label='<div style="display: table-cell;"></div><div style="display: table-cell;"><label class="mini_label">'.$w_mini_labels[2].'</label></div>';
              }

              if($param['w_time_type']=='12') {
                if((isset($_POST['wdform_'.$id1."_am_pm".$form_id]) ? esc_html($_POST['wdform_'.$id1."_am_pm".$form_id]) : $param['w_am_pm'])=='am') {
                  $am_ = "selected=\"selected\"";
                  $pm_ = "";
                }	
                else {
                  $am_ = "";
                  $pm_ = "selected=\"selected\"";                  
                }	
              
                $w_time_type = '<div style="display: table-cell;"><select class="am_pm_select" name="wdform_'.$id1.'_am_pm'.$form_id.'" id="wdform_'.$id1.'_am_pm'.$form_id.'" '.$param['attributes'].'><option value="am" '.$am_.'>AM</option><option value="pm" '.$pm_.'>PM</option></select></div>';
                
                $w_time_type_label = '<div ><label class="mini_label">'.$w_mini_labels[3].'</label></div>';              
              }
              else {
                $w_time_type='';
                $w_time_type_label = '';
              }
              $rep ='<div type="type_time" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              if($required) {
                $rep.='<span class="wdform-required">'.$required_sym.'</span>';
              }
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].';"><div style="display: table;"><div style="display: table-row;"><div style="display: table-cell;"><input type="text" value="'.(isset($_POST['wdform_'.$id1."_hh".$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1."_hh".$form_id])) : $param['w_hh']).'" class="time_box" id="wdform_'.$id1.'_hh'.$form_id.'" name="wdform_'.$id1.'_hh'.$form_id.'" onkeypress="return check_hour(event, &quot;wdform_'.$id1.'_hh'.$form_id.'&quot;, &quot;23&quot;)" '.$param['attributes'].'></div><div align="center" style="display: table-cell;"><span class="wdform_colon" style="vertical-align: middle;">&nbsp;:&nbsp;</span></div><div style="display: table-cell;"><input type="text" value="'.(isset($_POST['wdform_'.$id1."_mm".$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1."_mm".$form_id])) : $param['w_mm']).'" class="time_box" id="wdform_'.$id1.'_mm'.$form_id.'" name="wdform_'.$id1.'_mm'.$form_id.'" onkeypress="return check_minute(event, &quot;wdform_'.$id1.'_mm'.$form_id.'&quot;)" '.$param['attributes'].'></div>'.$w_sec.$w_time_type.'</div><div style="display: table-row;"><div style="display: table-cell;"><label class="mini_label">'.$w_mini_labels[0].'</label></div><div style="display: table-cell;"></div><div style="display: table-cell;"><label class="mini_label">'.$w_mini_labels[1].'</label></div>'.$w_sec_label.$w_time_type_label.'</div></div></div></div>';
              
              if($required) {
                $check_js.='
                if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
                {
                  if(jQuery("#wdform_'.$id1.'_mm'.$form_id.'").val()=="" || jQuery("#wdform_'.$id1.'_hh'.$form_id.'").val()=="" || (jQuery("#wdform_'.$id1.'_ss'.$form_id.'").length != 0 ? jQuery("#wdform_'.$id1.'_ss'.$form_id.'").val()=="" : false))
                  {
                    alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                    old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                    x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                    jQuery("#wdform_'.$id1.'_hh'.$form_id.'").focus();
                    return false;
                  }
                }
                ';
              }
              break;
            }

            case 'type_date': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_date','w_required','w_class','w_format','w_but_val');
              $temp = $params;
			  if(strpos($temp, 'w_disable_past_days') > -1)
				$params_names = array('w_field_label_size','w_field_label_pos','w_date','w_required','w_class','w_format','w_but_val', 'w_disable_past_days');

              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }
                
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
              $required = ($param['w_required']=="yes" ? true : false);	
            
			  $param['w_disable_past_days'] = isset($param['w_disable_past_days']) ? $param['w_disable_past_days'] : 'no';
			  $disable_past_days = $param['w_disable_past_days'] == 'yes' ? 'true' : 'false';
              $param['w_date']=(isset($_POST['wdform_'.$id1."_element".$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1."_element".$form_id])) : $param['w_date']);

              $rep ='<div type="type_date" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              if($required) {
                $rep.='<span class="wdform-required">'.$required_sym.'</span>';
              }
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].';"><input type="text" value="'.$param['w_date'].'" class="wdform-date" id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" maxlength="10" '.$param['attributes'].'><input id="wdform_'.$id1.'_button'.$form_id.'" class="wdform-calendar-button" value="'.$param['w_but_val'].'" format="'.$param['w_format'].'" onclick="return showCalendar(\'wdform_'.$id1.'_element'.$form_id.'\' , \''.$param['w_format'].'\', '.$disable_past_days.')" '.$param['attributes'].' ></div></div>';
              
              if($required) {
                $check_js.='
                if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
                {
                  if(jQuery("#wdform_'.$id1.'_element'.$form_id.'").val()=="")
                  {
                    alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                    jQuery("#wdform_'.$id1.'_element'.$form_id.'").addClass( "form-error" );
                    old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                    x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                    jQuery("#wdform_'.$id1.'_element'.$form_id.'").focus();
                    jQuery("#wdform_'.$id1.'_element'.$form_id.'").change(function() { if( jQuery(this).val()!="" ) jQuery(this).removeClass("form-error"); else jQuery(this).addClass("form-error");});
                    return false;
                  }
                }
                ';		
              }
			  
			  if($disable_past_days == 'true') {
                $check_js.='
                var currentDate = new Date();
				if( Date.parse(jQuery("#wdform_'.$id1.'_element'.$form_id.'").val() + " 23:59:59") < currentDate.getTime() ) {
					alert("'.__('You cannot select former dates. Choose a date starting from the current one.', 'form_maker').'");
					return false;
				}
				';		
              }
              break;
            }

            case 'type_date_fields': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_day','w_month','w_year','w_day_type','w_month_type','w_year_type','w_day_label','w_month_label','w_year_label','w_day_size','w_month_size','w_year_size','w_required','w_class','w_from','w_to','w_divider');              
              $temp = $params;
              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }
              
              $param['w_day']=(isset($_POST['wdform_'.$id1."_day".$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1."_day".$form_id])) : $param['w_day']);
              $param['w_month']=(isset($_POST['wdform_'.$id1."_month".$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1."_month".$form_id])) : $param['w_month']);
              $param['w_year']=(isset($_POST['wdform_'.$id1."_year".$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1."_year".$form_id])) : $param['w_year']);
                
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
              $required = ($param['w_required']=="yes" ? true : false);	            
      
              if($param['w_day_type']=="SELECT") {
                $w_day_type = '<select id="wdform_'.$id1.'_day'.$form_id.'" name="wdform_'.$id1.'_day'.$form_id.'" style="width: '.$param['w_day_size'].'px;" '.$param['attributes'].'><option value=""></option>';                
                for($k=1; $k<=31; $k++) {                
                  if($k<10) {
                    if($param['w_day']=='0'.$k) {
                      $selected = "selected=\"selected\"";
                    }
                    else {
                      $selected = "";
                    }
                    $w_day_type .= '<option value="0'.$k.'" '.$selected.'>0'.$k.'</option>';
                  }
                  else {
                    if($param['w_day']==''.$k) {
                      $selected = "selected=\"selected\"";
                    }
                    else {
                      $selected = "";
                    }
                    $w_day_type .= '<option value="'.$k.'" '.$selected.'>'.$k.'</option>';
                  }
                }
                $w_day_type .= '</select>';
              }
              else {
                $w_day_type = '<input type="text" value="'.$param['w_day'].'" id="wdform_'.$id1.'_day'.$form_id.'" name="wdform_'.$id1.'_day'.$form_id.'" style="width: '.$param['w_day_size'].'px;" '.$param['attributes'].'>';
                $onload_js .='jQuery("#wdform_'.$id1.'_day'.$form_id.'").blur(function() {if (jQuery(this).val()=="0") jQuery(this).val(""); else add_0(this)});';
                $onload_js .='jQuery("#wdform_'.$id1.'_day'.$form_id.'").keypress(function() {return check_day(event, this)});';
              }
              if($param['w_month_type']=="SELECT") {              
                $w_month_type = '<select id="wdform_'.$id1.'_month'.$form_id.'" name="wdform_'.$id1.'_month'.$form_id.'" style="width: '.$param['w_month_size'].'px;" '.$param['attributes'].'><option value=""></option><option value="01" '.($param['w_month']=="01" ? "selected=\"selected\"": "").'  >'.(__("January", 'form_maker')).'</option><option value="02" '.($param['w_month']=="02" ? "selected=\"selected\"": "").'>'.(__("February", 'form_maker')).'</option><option value="03" '.($param['w_month']=="03"? "selected=\"selected\"": "").'>'.(__("March", 'form_maker')).'</option><option value="04" '.($param['w_month']=="04" ? "selected=\"selected\"": "").' >'.(__("April", 'form_maker')).'</option><option value="05" '.($param['w_month']=="05" ? "selected=\"selected\"": "").' >'.(__("May", 'form_maker')).'</option><option value="06" '.($param['w_month']=="06" ? "selected=\"selected\"": "").' >'.(__("June", 'form_maker')).'</option><option value="07" '.($param['w_month']=="07" ? "selected=\"selected\"": "").' >'.(__("July", 'form_maker')).'</option><option value="08" '.($param['w_month']=="08" ? "selected=\"selected\"": "").' >'.(__("August", 'form_maker')).'</option><option value="09" '.($param['w_month']=="09" ? "selected=\"selected\"": "").' >'.(__("September", 'form_maker')).'</option><option value="10" '.($param['w_month']=="10" ? "selected=\"selected\"": "").' >'.(__("October", 'form_maker')).'</option><option value="11" '.($param['w_month']=="11" ? "selected=\"selected\"": "").'>'.(__("November", 'form_maker')).'</option><option value="12" '.($param['w_month']=="12" ? "selected=\"selected\"": "").' >'.(__("December", 'form_maker')).'</option></select>';              
              }
              else {
                $w_month_type = '<input type="text" value="'.$param['w_month'].'" id="wdform_'.$id1.'_month'.$form_id.'" name="wdform_'.$id1.'_month'.$form_id.'"  style="width: '.$param['w_day_size'].'px;" '.$param['attributes'].'>';
                $onload_js .='jQuery("#wdform_'.$id1.'_month'.$form_id.'").blur(function() {if (jQuery(this).val()=="0") jQuery(this).val(""); else add_0(this)});';
                $onload_js .='jQuery("#wdform_'.$id1.'_month'.$form_id.'").keypress(function() {return check_month(event, this)});';
              }
              if($param['w_year_type']=="SELECT") {
                $w_year_type = '<select id="wdform_'.$id1.'_year'.$form_id.'" name="wdform_'.$id1.'_year'.$form_id.'"  from="'.$param['w_from'].'" to="'.$param['w_to'].'" style="width: '.$param['w_year_size'].'px;" '.$param['attributes'].'><option value=""></option>';
                
                for($k=$param['w_to']; $k>=$param['w_from']; $k--) {
                  if($param['w_year']==$k) {
                    $selected = "selected=\"selected\"";
                  }
                  else {
                    $selected = "";
                  }
                  $w_year_type .= '<option value="'.$k.'" '.$selected.'>'.$k.'</option>';
                }
                $w_year_type .= '</select>';
              }
              else {
                $w_year_type = '<input type="text" value="'.$param['w_year'].'" id="wdform_'.$id1.'_year'.$form_id.'" name="wdform_'.$id1.'_year'.$form_id.'" from="'.$param['w_from'].'" to="'.$param['w_to'].'" style="width: '.$param['w_day_size'].'px;" '.$param['attributes'].'>';
                $onload_js .='jQuery("#wdform_'.$id1.'_year'.$form_id.'").keypress(function() {return check_year1(event, this)});';
                $onload_js .='jQuery("#wdform_'.$id1.'_year'.$form_id.'").change(function() {change_year(this)});';
              }
              $rep ='<div type="type_date_fields" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              if($required) {
                $rep.='<span class="wdform-required">'.$required_sym.'</span>';
              }
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].';"><div style="display: table;"><div style="display: table-row;"><div style="display: table-cell;">'.$w_day_type.'</div><div style="display: table-cell;"><span class="wdform_separator">'.$param['w_divider'].'</span></div><div style="display: table-cell;">'.$w_month_type.'</div><div style="display: table-cell;"><span class="wdform_separator">'.$param['w_divider'].'</span></div><div style="display: table-cell;">'.$w_year_type.'</div></div><div style="display: table-row;"><div style="display: table-cell;"><label class="mini_label">'.$param['w_day_label'].'</label></div><div style="display: table-cell;"></div><div style="display: table-cell;"><label class="mini_label" >'.$param['w_month_label'].'</label></div><div style="display: table-cell;"></div><div style="display: table-cell;"><label class="mini_label">'.$param['w_year_label'].'</label></div></div></div></div></div>';
              
              if($required) {
                $check_js.='
                if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
                {
                  if(jQuery("#wdform_'.$id1.'_day'.$form_id.'").val()=="" || jQuery("#wdform_'.$id1.'_month'.$form_id.'").val()=="" || jQuery("#wdform_'.$id1.'_year'.$form_id.'").val()=="")
                  {
                    alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                    old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                    x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                    jQuery("#wdform_'.$id1.'_day'.$form_id.'").focus();
                    return false;
                  }
                }
                ';		
              }
              break;
            }

            case 'type_file_upload': {
              $params_names = array('w_field_label_size','w_field_label_pos','w_destination','w_extension','w_max_size','w_required','w_multiple','w_class');
              $temp = $params;
              foreach ($params_names as $params_name ) {
                $temp = explode('*:*'.$params_name.'*:*', $temp);
                $param[$params_name] = $temp[0];
                if (isset($temp[1])) {
                  $temp = $temp[1];
                }
                else {
                  $temp = '';
                }
              }
              if ($temp) {
                $temp	= explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp, 0, count($temp) - 1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");	
              $required = ($param['w_required']=="yes" ? true : false);	
              $multiple = ($param['w_multiple']=="yes" ? "multiple='multiple'" : "");
              $rep ='<div type="type_file_upload" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              if($required) {
                $rep.='<span class="wdform-required">'.$required_sym.'</span>';
              }
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].';"><label class="file-upload"><div class="file-picker"></div><input type="file" id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_file'.$form_id.'[]" '.$multiple.' '.$param['attributes'].'></label></div></div>';
        
              if($required) {
                $check_js.='
                if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
                {
                  if(jQuery("#wdform_'.$id1.'_element'.$form_id.'").val()=="")
                  {
                    alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                    old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                    x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                    jQuery("#wdform_'.$id1.'_element'.$form_id.'").focus();
                    return false;
                  }
                }
                ';	
              }
              $check_js.='
              if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
              {
                ext_available=getfileextension(jQuery("#wdform_'.$id1.'_element'.$form_id.'").val(),"'.$param['w_extension'].'");
                if(!ext_available)
                {
                  alert("'.addslashes(__("Sorry, you are not allowed to upload this type of file.", 'form_maker')).'");
                  old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                  x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                  jQuery("#wdform_'.$id1.'_element'.$form_id.'").focus();
                  return false;
                }
              }
              ';
              break;
            }

            case 'type_captcha': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_digit','w_class');
              $temp=$params;
              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }
              
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
              
              $rep ='<div type="type_captcha" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span></div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].'"><div style="display: table;"><div style="display: table-cell;vertical-align: middle;"><div valign="middle" style="display: table-cell; text-align: center;"><img type="captcha" digit="'.$param['w_digit'].'" src=" ' . add_query_arg(array('action' => 'formcontactwdcaptcha', 'digit' => $param['w_digit'], 'i' => $form_id), admin_url('admin-ajax.php')) . '" id="wd_captcha'.$form_id.'" class="captcha_img" style="display:none" '.$param['attributes'].'></div><div valign="middle" style="display: table-cell;"><div class="captcha_refresh" id="_element_refresh'.$form_id.'" '.$param['attributes'].'></div></div></div><div style="display: table-cell;vertical-align: middle;"><div style="display: table-cell;"><input type="text" class="captcha_input" id="wd_captcha_input'.$form_id.'" name="captcha_input" style="width: '.($param['w_digit']*10+15).'px;" '.$param['attributes'].'></div></div></div></div></div>';
              
              $onload_js .='jQuery("#wd_captcha'.$form_id.'").click(function() {captcha_refresh("wd_captcha","'.$form_id.'")});';
              $onload_js .='jQuery("#_element_refresh'.$form_id.'").click(function() {captcha_refresh("wd_captcha","'.$form_id.'")});';
              
              $check_js.='
              if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
              {
                if(jQuery("#wd_captcha_input'.$form_id.'").val()=="")
                {
                  alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                  old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                  x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                  jQuery("#wd_captcha_input'.$form_id.'").focus();
                  return false;
                }
              }
              ';
              $onload_js.= 'captcha_refresh("wd_captcha", "'.$form_id.'");';
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
			 
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
			  $param['w_count'] = $param['w_count'] ? $param['w_count'] : 1;
              $param['w_operations'] = $param['w_operations'] ? $param['w_operations'] : '+, -, *, /';
              $param['w_input_size'] = $param['w_input_size'] ? $param['w_input_size'] : 60;
   
              $rep ='<div type="type_arithmetic_captcha" class="wdform-field">
			  <div align="left" class="wdform-label-section" style="display:'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label" style="vertical-align: top;">'.$label.'</span></div><div class="wdform-element-section '.$param['w_class'].'" style="display: '.$param['w_field_label_pos2'].';"><div style="display: table;"><div style="display: table-row;"><div style="display: table-cell; vertical-align: middle;"><img type="captcha" operations_count="'.$param['w_count'].'" operations="'.$param['w_operations'].'" src="' . add_query_arg(array('action' => 'formcontactwdmathcaptcha', 'operations_count' => $param['w_count'], 'operations' => $param['w_operations'], 'i' => $form_id), admin_url('admin-ajax.php')) . '" id="wd_arithmetic_captcha'.$form_id.'" class="arithmetic_captcha_img" '.$param['attributes'].'></div><div style="display: table-cell;"><input type="text" class="arithmetic_captcha_input" id="wd_arithmetic_captcha_input'.$form_id.'" name="arithmetic_captcha_input" onkeypress="return check_isnum(event)" style="width: '.$param['w_input_size'].'px;" '.$param['attributes'].'/></div><div style="display: table-cell; vertical-align: middle;"><div class="captcha_refresh" id="_element_refresh'.$form_id.'" '.$param['attributes'].'></div></div></div></div></div></div>';
              
			  $onload_js .='jQuery("#wd_arithmetic_captcha'.$form_id.'").click(function() { captcha_refresh("wd_arithmetic_captcha","'.$form_id.'") });';
              $onload_js .='jQuery("#_element_refresh'.$form_id.'").click(function() {captcha_refresh("wd_arithmetic_captcha","'.$form_id.'")});';
              
              $check_js.='
              if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
              {
                if(jQuery("#wd_arithmetic_captcha_input'.$form_id.'").val()=="")
                {
                  alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                  old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                  x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                  jQuery("#wd_arithmetic_captcha_input'.$form_id.'").focus();
                  return false;
                }
              }
              ';
              $onload_js.= 'captcha_refresh("wd_arithmetic_captcha", "'.$form_id.'");';
              break;
            }

            
			case 'type_recaptcha': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_public','w_private','w_class');
              $temp=$params;
              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
            
              $publickey= isset($fmc_settings['public_key']) ? $fmc_settings['public_key'] : '0';	

              $rep =' <script src="https://www.google.com/recaptcha/api.js"></script><div type="type_recaptcha" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span></div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].';"><div class="g-recaptcha" data-sitekey="'.$publickey.'"></div></div></div>';
              break;
            }
            
            case 'type_hidden': {
              $params_names=array('w_name','w_value');
              $temp=$params;

              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }
              $rep ='<div type="type_hidden" class="wdform-field"><div class="wdform-label-section" style="display: table-cell;"></div><div class="wdform-element-section" style="display: table-cell;"><input type="hidden" value="'.$param['w_value'].'" id="wdform_'.$id1.'_element'.$form_id.'" name="'.$param['w_name'].'" '.$param['attributes'].'></div></div>';
              break;
            }

            case 'type_mark_map': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_center_x','w_center_y','w_long','w_lat','w_zoom','w_width','w_height','w_info','w_class');
              $temp=$params;
              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }
            
              $wdformfieldsize = ($param['w_field_label_pos']=="left" ? ($param['w_field_label_size']+$param['w_width']) : max($param['w_field_label_size'], $param['w_width']));	
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");

              $rep ='<div type="type_mark_map" class="wdform-field" style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span></div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].' width: '.$param['w_width'].'px;"><input type="hidden" id="wdform_'.$id1.'_long'.$form_id.'" name="wdform_'.$id1.'_long'.$form_id.'" value="'.$param['w_long'].'"><input type="hidden" id="wdform_'.$id1.'_lat'.$form_id.'" name="wdform_'.$id1.'_lat'.$form_id.'" value="'.$param['w_lat'].'"><div id="wdform_'.$id1.'_element'.$form_id.'" long0="'.$param['w_long'].'" lat0="'.$param['w_lat'].'" zoom="'.$param['w_zoom'].'" info0="'.str_replace(array("\r\n", "\n", "\r"), '<br />', $param['w_info']).'" center_x="'.$param['w_center_x'].'" center_y="'.$param['w_center_y'].'" style="width: 100%; height: '.$param['w_height'].'px;" '.$param['attributes'].'></div></div></div>	';
              
              $onload_js .='if_gmap_init("wdform_'.$id1.'", '.$form_id.');';
              $onload_js .='add_marker_on_map("wdform_'.$id1.'", 0, "'.$param['w_long'].'", "'.$param['w_lat'].'", "'.str_replace(array("\r\n", "\n", "\r"), '<br />', $param['w_info']).'", '.$form_id.',true);';
              
              break;
            }
            
            case 'type_map': {
              $params_names=array('w_center_x','w_center_y','w_long','w_lat','w_zoom','w_width','w_height','w_info','w_class');
              $temp=$params;

              foreach($params_names as $params_name) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }
              $marker='';
              $param['w_long']	= explode('***',$param['w_long']);
              $param['w_lat']	= explode('***',$param['w_lat']);
              $param['w_info']	= explode('***',$param['w_info']);
              foreach($param['w_long'] as $key => $w_long ) {
                $marker.='long'.$key.'="'.$w_long.'" lat'.$key.'="'.$param['w_lat'][$key].'" info'.$key.'="'.str_replace(array("\r\n", "\n", "\r"), '<br />', $param['w_info'][$key]).'"';
              }

              $rep ='<div type="type_map" class="wdform-field"  style="width:'.($param['w_width']).'px"><div class="wdform-label-section" style="display: table-cell;"><span id="wdform_'.$id1.'_element_label'.$form_id.'" style="display: none;">'.$label.'</span></div><div class="wdform-element-section '.$param['w_class'].'" style="width: '.$param['w_width'].'px;"><div id="wdform_'.$id1.'_element'.$form_id.'" zoom="'.$param['w_zoom'].'" center_x="'.$param['w_center_x'].'" center_y="'.$param['w_center_y'].'" style="width: 100%; height: '.$param['w_height'].'px;" '.$marker.' '.$param['attributes'].'></div></div></div>';
              
              $onload_js .='if_gmap_init("wdform_'.$id1.'", '.$form_id.');';
              
              foreach($param['w_long'] as $key => $w_long ) {
                $onload_js .='add_marker_on_map("wdform_'.$id1.'",'.$key.', "'.$w_long.'", "'.$param['w_lat'][$key].'", "'.str_replace(array("\r\n", "\n", "\r"), '<br />', $param['w_info'][$key]).'", '.$form_id.',false);';
              }
              break;
            }

            case 'type_paypal_price': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_first_val','w_title', 'w_mini_labels','w_size','w_required','w_hide_cents','w_class','w_range_min','w_range_max');
              $temp=$params;

              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }
              
              $w_first_val = explode('***',$param['w_first_val']);
              $w_title = explode('***',$param['w_title']);
      
              $param['w_first_val']=(isset($_POST['wdform_'.$id1.'_element_dollars'.$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1.'_element_dollars'.$form_id])) : $w_first_val[0]).'***'.(isset($_POST['wdform_'.$id1.'_element_cents'.$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1.'_element_cents'.$form_id])) : $w_first_val[1]);

              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
              $input_active = ($param['w_first_val']==$param['w_title'] ? "input_deactive" : "input_active");	
              $required = ($param['w_required']=="yes" ? true : false);	
              $hide_cents = ($param['w_hide_cents']=="yes" ? "none;" : "table-cell;");	
              
              $w_first_val = explode('***',$param['w_first_val']);
              $w_title = explode('***',$param['w_title']);
              $w_mini_labels = explode('***',$param['w_mini_labels']);
              
              $rep ='<div type="type_paypal_price" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              if($required) {
                $rep.='<span class="wdform-required">'.$required_sym.'</span>';
              }
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].';"><input type="hidden" value="'.$param['w_range_min'].'" name="wdform_'.$id1.'_range_min'.$form_id.'" id="wdform_'.$id1.'_range_min'.$form_id.'"><input type="hidden" value="'.$param['w_range_max'].'" name="wdform_'.$id1.'_range_max'.$form_id.'" id="wdform_'.$id1.'_range_max'.$form_id.'"><div id="wdform_'.$id1.'_table_price" style="display: table;"><div id="wdform_'.$id1.'_tr_price1" style="display: table-row;"><div id="wdform_'.$id1.'_td_name_currency" style="display: table-cell;"><span class="wdform_colon" style="vertical-align: middle;"><!--repstart-->&nbsp;'.$form_currency.'&nbsp;<!--repend--></span></div><div id="wdform_'.$id1.'_td_name_dollars" style="display: table-cell;"><input type="text" class="'.$input_active.'" id="wdform_'.$id1.'_element_dollars'.$form_id.'" name="wdform_'.$id1.'_element_dollars'.$form_id.'" value="'.$w_first_val[0].'" title="'.$w_title[0].'" onkeypress="return check_isnum(event)" style="width: '.$param['w_size'].'px;" '.$param['attributes'].'></div><div id="wdform_'.$id1.'_td_name_divider" style="display: '.$hide_cents.';"><span class="wdform_colon" style="vertical-align: middle;">&nbsp;.&nbsp;</span></div><div id="wdform_'.$id1.'_td_name_cents" style="display: '.$hide_cents.'"><input type="text" class="'.$input_active.'" id="wdform_'.$id1.'_element_cents'.$form_id.'" name="wdform_'.$id1.'_element_cents'.$form_id.'" value="'.$w_first_val[1].'" title="'.$w_title[1].'" style="width: 30px;" '.$param['attributes'].'></div></div><div id="wdform_'.$id1.'_tr_price2" style="display: table-row;"><div style="display: table-cell;"><label class="mini_label"></label></div><div align="left" style="display: table-cell;"><label class="mini_label">'.$w_mini_labels[0].'</label></div><div id="wdform_'.$id1.'_td_name_label_divider" style="display: '.$hide_cents.'"><label class="mini_label"></label></div><div align="left" id="wdform_'.$id1.'_td_name_label_cents" style="display: '.$hide_cents.'"><label class="mini_label">'.$w_mini_labels[1].'</label></div></div></div></div></div>';
              
              $onload_js .='jQuery("#wdform_'.$id1.'_element_cents'.$form_id.'").blur(function() {add_0(this)});';
              $onload_js .='jQuery("#wdform_'.$id1.'_element_cents'.$form_id.'").keypress(function(event) {return check_isnum_interval(event,this,0,99)});';

              if($required) {
                $check_js.='
                if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
                {
                  if(jQuery("#wdform_'.$id1.'_element_dollars'.$form_id.'").val()=="'.$w_title[0].'" || jQuery("#wdform_'.$id1.'_element_dollars'.$form_id.'").val()=="")
                  {
                    alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                    old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                    x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                    jQuery("#wdform_'.$id1.'_element_dollars'.$form_id.'").focus();
                    return false;
                  }
                }
                ';
              }
              $check_js.='
              if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
              {
                dollars=0;
                cents=0;
              
                if(jQuery("#wdform_'.$id1.'_element_dollars'.$form_id.'").val()!="'.$w_title[0].'" || jQuery("#wdform_'.$id1.'_element_dollars'.$form_id.'").val())
                  dollars =jQuery("#wdform_'.$id1.'_element_dollars'.$form_id.'").val();
                
                if(jQuery("#wdform_'.$id1.'_element_cents'.$form_id.'").val()!="'.$w_title[1].'" || jQuery("#wdform_'.$id1.'_element_cents'.$form_id.'").val())
                  cents =jQuery("#wdform_'.$id1.'_element_cents'.$form_id.'").val();

                var price=dollars+"."+cents;

                if(isNaN(price))
                {
                  alert("Invalid value of number field");
                  old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                  x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                  jQuery("#wdform_'.$id1.'_element_dollars'.$form_id.'").focus();
                  return false;
                }
              
                var range_min='.($param['w_range_min'] ? $param['w_range_min'] : 0).';
                var range_max='.($param['w_range_max'] ? $param['w_range_max'] : -1).';

                
                if('.($required ? 'true' : 'false').' || jQuery("#wdform_'.$id1.'_element_dollars'.$form_id.'").val()!="'.$w_title[0].'" || jQuery("#wdform_'.$id1.'_element_cents'.$form_id.'").val()!="'.$w_title[1].'")
                  if((range_max!=-1 && parseFloat(price)>range_max) || parseFloat(price)<range_min)
                  {		
                    alert("' . addslashes((__('The', 'form_maker')) . $label . (__('value must be between', 'form_maker')) . ($param['w_range_min'] ? $param['w_range_min'] : 0) . '-' . ($param['w_range_max'] ? $param['w_range_max'] : "any")) . '");

                    old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                    x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                    jQuery("#wdform_'.$id1.'_element_dollars'.$form_id.'").focus();
                    return false;
                  }
              }
              ';
              
              break;
            }
            
            case 'type_paypal_select': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_size','w_choices','w_choices_price','w_choices_checked', 'w_choices_disabled','w_required','w_quantity','w_quantity_value','w_class','w_property','w_property_values');
              $temp=$params;
			  if(strpos($temp, 'w_choices_params') > -1)
				$params_names=array('w_field_label_size','w_field_label_pos','w_size','w_choices','w_choices_price','w_choices_checked', 'w_choices_disabled','w_required','w_quantity', 'w_quantity_value', 'w_choices_params','w_class','w_property','w_property_values');
				
              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }
              $wdformfieldsize = ($param['w_field_label_pos']=="left" ? ($param['w_field_label_size']+$param['w_size']) : max($param['w_field_label_size'], $param['w_size']));	
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
              $required = ($param['w_required']=="yes" ? true : false);	
              $param['w_choices']	= explode('***',$param['w_choices']);
              $param['w_choices_price']	= explode('***',$param['w_choices_price']);
              $param['w_choices_checked']	= explode('***',$param['w_choices_checked']);
              $param['w_choices_disabled']	= explode('***',$param['w_choices_disabled']);
              $param['w_property']	= explode('***',$param['w_property']);
              $param['w_property_values']	= explode('***',$param['w_property_values']);
			  
              if(isset($param['w_choices_params']))
				$param['w_choices_params'] = explode('***',$param['w_choices_params']);	
				
              $post_value = isset($_POST['wdform_'.$id1."_element".$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1."_element".$form_id])) : NULL;
             $rep='<div type="type_paypal_select" class="wdform-field" style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
					if($required)
						$rep.='<span class="wdform-required">'.$required_sym.'</span>';
					$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].' width: '.$param['w_size'].'px;"><select id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" style="width: 100%;"  '.$param['attributes'].'>';
					foreach($param['w_choices'] as $key => $choice)
					{		
						if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])
						{
					
							$choices_labels =array();
							$choices_values =array();
							$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
							$where = (str_replace(array('[',']'), '', $w_choices_params[0]) ? ' WHERE '.str_replace(array('[',']'), '', $w_choices_params[0]) : '');
							$w_choices_params = explode('[db_info]',$w_choices_params[1]);
							$order_by = str_replace(array('[',']'), '', $w_choices_params[0]);
							$db_info = str_replace(array('[',']'), '', $w_choices_params[1]);
							
							
							
							$label_table_and_column = explode(':',str_replace(array('[',']'), '', $choice));
							$table = $label_table_and_column[0];
							$label_column = $label_table_and_column[1];
							if($label_column)
							{
								 
								$choices_labels = $this->model->select_data_from_db_for_labels($db_info, $label_column, $table, $where, $order_by);					
							}	

							$value_table_and_column = explode(':',str_replace(array('[',']'), '', $param['w_choices_price'][$key]));
							$value_column = $param['w_choices_disabled'][$key]=="true" ? '' : $value_table_and_column[1];

							if($value_column)
							{
								$choices_values = $this->model->select_data_from_db_for_values($db_info, $value_column, $table, $where, $order_by);
							}		
							
							$columns_count = count($choices_labels)>0 ?  count($choices_labels) : count($choices_values);
							
							for($k=0; $k<$columns_count; $k++)
							{
								$choice_label = isset($choices_labels[$k]) ? $choices_labels[$k] : '';
								$choice_value = isset($choices_values[$k]) ? (float)$choices_values[$k][0] : '';
								
								if(isset($post_value))
								{							
									if($post_value==$choice_value && $choice_label==$_REQUEST["wdform_".$id1."_element_label".$form_id])
										$param['w_choices_checked'][$key]='selected="selected"';
									else
										$param['w_choices_checked'][$key]='';
								}	
								else
									$param['w_choices_checked'][$key]=(($param['w_choices_checked'][$key]=='true' && $k == 0) ? 'selected="selected"' : '');

								$rep.='<option value="'.$choice_value.'" '.$param['w_choices_checked'][$key].'>'.$choice_label[0].'</option>';
							}			
						}
						else
						{
							$choice_value = $param['w_choices_disabled'][$key]=="true" ? '' : $param['w_choices_price'][$key];
						
							if(isset($post_value))
							{	
								if($post_value==$choice_value && $choice==$_REQUEST["wdform_".$id1."_element_label".$form_id])
									$param['w_choices_checked'][$key]='selected="selected"';
								else
									$param['w_choices_checked'][$key]='';	
							}	
							else	
							{
								if($param['w_choices_checked'][$key]=='true')
									$param['w_choices_checked'][$key]='selected="selected"';
								else
									$param['w_choices_checked'][$key]='';
							}
							
							$rep.='<option value="'.$choice_value.'" '.$param['w_choices_checked'][$key].'>'.$choice.'</option>';
						}	
					}
              $rep.='</select><div id="wdform_'.$id1.'_div'.$form_id.'">';
              if($param['w_quantity']=="yes") {
                $rep.='<div class="paypal-property"><label class="mini_label" style="margin: 0px 5px;">'.(__("Quantity", 'form_maker')).'</label><input type="text" value="'.(isset($_POST['wdform_'.$id1."_element_quantity".$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1."_element_quantity".$form_id])) : $param['w_quantity_value']).'" id="wdform_'.$id1.'_element_quantity'.$form_id.'" name="wdform_'.$id1.'_element_quantity'.$form_id.'" class="wdform-quantity"></div>';
              }
              if($param['w_property'][0]) {
                foreach($param['w_property'] as $key => $property) {
                  $rep.='
                  <div id="wdform_'.$id1.'_property_'.$key.'" class="paypal-property">
                  <div style="width:150px; display:inline-block;">
                  <label class="mini_label" id="wdform_'.$id1.'_property_label_'.$form_id.''.$key.'" style="margin-right: 5px;">'.$property.'</label>
                  <select id="wdform_'.$id1.'_property'.$form_id.''.$key.'" name="wdform_'.$id1.'_property'.$form_id.''.$key.'" style="margin: 2px 0px;">';
                  $param['w_property_values'][$key]	= explode('###',$param['w_property_values'][$key]);
                  $param['w_property_values'][$key]	= array_slice($param['w_property_values'][$key],1, count($param['w_property_values'][$key]));   
                  foreach($param['w_property_values'][$key] as $subkey => $property_value) {
                    $rep.='<option id="wdform_'.$id1.'_'.$key.'_option'.$subkey.'" value="'.$property_value.'" '.(isset($_POST['wdform_'.$id1.'_property'.$form_id.''.$key]) && esc_html(stripslashes($_POST['wdform_'.$id1.'_property'.$form_id.''.$key])) == $property_value ? 'selected="selected"' : "").'>'.$property_value.'</option>';
                  }
                  $rep.='</select></div></div>';
                }
              }
              $rep.='</div></div></div>';
              
              if($required) {
                $check_js.='
                if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
                {
                  if(jQuery("#wdform_'.$id1.'_element'.$form_id.'").val()=="")
                  {
                    alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                    jQuery("#wdform_'.$id1.'_element'.$form_id.'").addClass( "form-error" );
                    old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                    x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                    jQuery("#wdform_'.$id1.'_element'.$form_id.'").focus();
                    jQuery("#wdform_'.$id1.'_element'.$form_id.'").change(function() { if( jQuery(this).val()!="" ) jQuery(this).removeClass("form-error"); else jQuery(this).addClass("form-error");});
                    return false;
                  }
                }
                ';	
              }
              $onsubmit_js.='
                jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_element_label'.$form_id.'\"  />").val(jQuery("#wdform_'.$id1.'_element'.$form_id.' option:selected").text()).appendTo("#form'.$form_id.'");
                ';
              $onsubmit_js.='
                jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_element_quantity_label'.$form_id.'\"  />").val("'.(__("Quantity", 'form_maker')).'").appendTo("#form'.$form_id.'");
                ';
              foreach($param['w_property'] as $key => $property)
              {	
                $onsubmit_js.='
                  jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_element_property_label'.$form_id.$key.'\"  />").val("'.$property.'").appendTo("#form'.$form_id.'");
                  ';
                  
              }
              break;
            }
            
            case 'type_paypal_checkbox': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_flow','w_choices','w_choices_price','w_choices_checked','w_required','w_randomize','w_allow_other','w_allow_other_num','w_class','w_property','w_property_values','w_quantity','w_quantity_value');
              $temp=$params;
			  if(strpos($temp, 'w_field_option_pos') > -1)
				$params_names=array('w_field_label_size','w_field_label_pos', 'w_field_option_pos','w_flow','w_choices','w_choices_price','w_choices_checked','w_required','w_randomize','w_allow_other','w_allow_other_num', 'w_choices_params', 'w_class','w_property','w_property_values','w_quantity','w_quantity_value');	
				
              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }
			  if(!isset($param['w_field_option_pos']))
				$param['w_field_option_pos'] = 'left';
				
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
			  $param['w_field_option_pos1'] = ($param['w_field_option_pos']=="right" ? "style='float: none 
			  !important;'" : "");
			  $param['w_field_option_pos2'] = ($param['w_field_option_pos']=="right" ? "style='float: left !important; margin:3px 8px 0 0 !important; display: inline-block !important;'" : "");
              $required = ($param['w_required']=="yes" ? true : false);	
              $param['w_choices']	= explode('***',$param['w_choices']);
              $param['w_choices_price']	= explode('***',$param['w_choices_price']);
              $param['w_choices_checked']	= explode('***',$param['w_choices_checked']);
              $param['w_property']	= explode('***',$param['w_property']);
              $param['w_property_values']	= explode('***',$param['w_property_values']);
			  if(isset($param['w_choices_params']))
				$param['w_choices_params'] = explode('***',$param['w_choices_params']);	
				
              foreach($param['w_choices_checked'] as $key => $choices_checked ) {
                $param['w_choices_checked'][$key]=($choices_checked=='true' ? 'checked="checked"' : '');
              }
              $rep='<div type="type_paypal_checkbox" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              if($required) {
                $rep.='<span class="wdform-required">'.$required_sym.'</span>';
              }
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].';">';
              $total_queries = 0;
              foreach($param['w_choices'] as $key => $choice)
					{	
						$key1 = $key + $total_queries;
						if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])
						{
							$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
							$where = (str_replace(array('[',']'), '', $w_choices_params[0]) ? ' WHERE '.str_replace(array('[',']'), '', $w_choices_params[0]) : '');
							$w_choices_params = explode('[db_info]',$w_choices_params[1]);
							
							$order_by = str_replace(array('[',']'), '', $w_choices_params[0]);
							$db_info = str_replace(array('[',']'), '', $w_choices_params[1]);
						
							
							
							$label_table_and_column = explode(':',str_replace(array('[',']'), '', $choice));
							$table = $label_table_and_column[0];
							$label_column = $label_table_and_column[1];
							if($label_column)
							{
								$choices_labels = $this->model->select_data_from_db_for_labels($db_info, $label_column, $table, $where, $order_by);				
							}	

							$value_table_and_column = explode(':',str_replace(array('[',']'), '', $param['w_choices_price'][$key]));
							$value_column = $value_table_and_column[1];

							if($value_column)
							{
								$choices_values = $this->model->select_data_from_db_for_values($db_info, $value_column, $table, $where, $order_by);
							}		
							
							$columns_count = count($choices_labels)>0 ?  count($choices_labels) : count($choices_values);
							
							if(array_filter($choices_labels) || array_filter($choices_values))
							{
								$total_queries = $total_queries + $columns_count-1;
								if(!isset($post_value))
									$param['w_choices_checked'][$key]=($param['w_choices_checked'][$key]=='true' ? 'checked="checked"' : '');
									
								for($k=0; $k<$columns_count; $k++)
								{
									$choice_label = isset($choices_labels) ? $choices_labels[$k] : '';
									$choice_value = isset($choices_values) ? (float)$choices_values[$k][0] : '';
									
									if(isset($post_value))
									{	
										$param['w_choices_checked'][$key]="";
										$checkedvalue = $_REQUEST['wdform_'.$id1."_element".$form_id.($key1+$k)];

										if(isset($checkedvalue))
											$param['w_choices_checked'][$key]='checked="checked"';							
									}

									$rep.='<div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" '.$param['w_field_option_pos1'].'>'.$choice_label[0].'</label><div class="checkbox-div forlabs" '.$param['w_field_option_pos2'].'><input type="checkbox" id="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" name="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" value="'.$choice_value.'" title="'.htmlspecialchars($choice_label[0]).'" '.$param['w_choices_checked'][$key].' '.$param['attributes'].'><label for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'"></label></div><input type="hidden" name="wdform_'.$id1.'_element'.$form_id.($key1+$k).'_label" value="'.htmlspecialchars($choice_label[0]).'" /></div>';
								}
							}	
						}
						else
						{
							if(isset($post_value))
							{
								$param['w_choices_checked'][$key]="";
								$checkedvalue=$_REQUEST['wdform_'.$id1."_element".$form_id.$key1];
								if(isset($checkedvalue))
									$param['w_choices_checked'][$key]='checked="checked"';		
							}
							else
								$param['w_choices_checked'][$key]=($param['w_choices_checked'][$key]=='true' ? 'checked="checked"' : '');
								
							$rep.='<div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.$key1.'" '.$param['w_field_option_pos1'].'>'.$choice.'</label><div class="checkbox-div forlabs" '.$param['w_field_option_pos2'].'><input type="checkbox" id="wdform_'.$id1.'_element'.$form_id.''.$key1.'" name="wdform_'.$id1.'_element'.$form_id.''.$key1.'" value="'.$param['w_choices_price'][$key].'" title="'.htmlspecialchars($choice).'" '.$param['w_choices_checked'][$key].' '.$param['attributes'].'><label for="wdform_'.$id1.'_element'.$form_id.''.$key1.'"></label></div><input type="hidden" name="wdform_'.$id1.'_element'.$form_id.$key1.'_label" value="'.htmlspecialchars($choice).'" /></div>';
						
						}
					}
          
              $rep.='<div id="wdform_'.$id1.'_div'.$form_id.'">';
              if($param['w_quantity']=="yes") {
                $rep.='<div class="paypal-property"><label class="mini_label" style="margin: 0px 5px;">'.(__("Quantity", 'form_maker')).'</label><input type="text" value="'.(isset($_POST['wdform_'.$id1."_element_quantity".$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1."_element_quantity".$form_id])) : $param['w_quantity_value']).'" id="wdform_'.$id1.'_element_quantity'.$form_id.'" name="wdform_'.$id1.'_element_quantity'.$form_id.'" class="wdform-quantity"></div>';
              }
              if($param['w_property'][0]) {
                foreach($param['w_property'] as $key => $property) {
                  $rep.='
                  <div class="paypal-property">
                  <div style="display:inline-block;">
                  <label class="mini_label" id="wdform_'.$id1.'_property_label_'.$form_id.''.$key.'" style="margin-right: 5px;">'.$property.'</label>
                  <select id="wdform_'.$id1.'_property'.$form_id.''.$key.'" name="wdform_'.$id1.'_property'.$form_id.''.$key.'" style="margin: 2px 0px;">';
                  $param['w_property_values'][$key]	= explode('###',$param['w_property_values'][$key]);
                  $param['w_property_values'][$key]	= array_slice($param['w_property_values'][$key],1, count($param['w_property_values'][$key]));   
                  foreach($param['w_property_values'][$key] as $subkey => $property_value) {
                    $rep.='<option id="wdform_'.$id1.'_'.$key.'_option'.$subkey.'" value="'.$property_value.'" '.(isset($_POST['wdform_'.$id1.'_property'.$form_id.''.$key]) && esc_html(stripslashes($_POST['wdform_'.$id1.'_property'.$form_id.''.$key])) == $property_value ? 'selected="selected"' : "").'>'.$property_value.'</option>';
                  }
                  $rep.='</select></div></div>';
                }
              }
              $rep.='</div></div></div>';              
              if($required) {
                $check_js.='
                if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
                {
                  if(x.find(jQuery("div[wdid='.$id1.'] input:checked")).length == 0)
                  {
                    alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                    old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                    x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                    
                    return false;
                  }						
                }
                ';
              }
              $onsubmit_js.='
                jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_element_label'.$form_id.'\"  />").val((x.find(jQuery("div[wdid='.$id1.'] input:checked")).length != 0) ? jQuery("#"+x.find(jQuery("div[wdid='.$id1.'] input:checked")).attr("id").replace("element", "elementlabel_")) : "").appendTo("#form'.$form_id.'");
                ';
              $onsubmit_js.='
                jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_element_quantity_label'.$form_id.'\"  />").val("'.(__("Quantity", 'form_maker')).'").appendTo("#form'.$form_id.'");
                ';
              foreach($param['w_property'] as $key => $property)
              {
                $onsubmit_js.='
                  jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_element_property_label'.$form_id.$key.'\"  />").val("'.$property.'").appendTo("#form'.$form_id.'");
                  ';
              }
              break;
            }

            case 'type_paypal_radio': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_flow','w_choices','w_choices_price','w_choices_checked','w_required','w_randomize','w_allow_other','w_allow_other_num','w_class','w_property','w_property_values','w_quantity','w_quantity_value');
              $temp=$params;
			  if(strpos($temp, 'w_field_option_pos') > -1)
				$params_names=array('w_field_label_size','w_field_label_pos', 'w_field_option_pos', 'w_flow','w_choices','w_choices_price','w_choices_checked','w_required','w_randomize','w_allow_other','w_allow_other_num', 'w_choices_params', 'w_class', 'w_property','w_property_values','w_quantity','w_quantity_value');
						
              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }
              if(!isset($param['w_field_option_pos']))
				$param['w_field_option_pos'] = 'left';
				
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
			  $param['w_field_option_pos1'] = ($param['w_field_option_pos']=="right" ? "style='float: none
			  !important;'" : "");
			  $param['w_field_option_pos2'] = ($param['w_field_option_pos']=="right" ? "style='float: left !important; margin-right: 8px !important; display: inline-block !important;'" : "");
              $required = ($param['w_required']=="yes" ? true : false);	
              $param['w_choices']	= explode('***',$param['w_choices']);
              $param['w_choices_price']	= explode('***',$param['w_choices_price']);
              $param['w_choices_checked']	= explode('***',$param['w_choices_checked']);
              $param['w_property']	= explode('***',$param['w_property']);
              $param['w_property_values']	= explode('***',$param['w_property_values']);
              if(isset($param['w_choices_params']))
				$param['w_choices_params'] = explode('***',$param['w_choices_params']);	
				
              foreach($param['w_choices_checked'] as $key => $choices_checked ) {
                $param['w_choices_checked'][$key]=($choices_checked=='true' ? 'checked="checked"' : '');
              }
                
              $rep='<div type="type_paypal_radio" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              if($required) {
                $rep.='<span class="wdform-required">'.$required_sym.'</span>';
              }
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].';">';
            
              $total_queries = 0;
             foreach($param['w_choices'] as $key => $choice)
					{
						$key1 = $key + $total_queries;
						if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])
						{
							$choices_labels =array();
							$choices_values =array();
							$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
							$where = (str_replace(array('[',']'), '', $w_choices_params[0]) ? ' WHERE '.str_replace(array('[',']'), '', $w_choices_params[0]) : '');
							$w_choices_params = explode('[db_info]',$w_choices_params[1]);
							
							$order_by = str_replace(array('[',']'), '', $w_choices_params[0]);
							$db_info = str_replace(array('[',']'), '', $w_choices_params[1]);
						
							
							$label_table_and_column = explode(':',str_replace(array('[',']'), '', $choice));
							$table = $label_table_and_column[0];
							$label_column = $label_table_and_column[1];
							if($label_column)
							{
								$choices_labels = $this->model->select_data_from_db_for_labels($db_info, $label_column, $table, $where, $order_by);					
							}	

							$value_table_and_column = explode(':',str_replace(array('[',']'), '', $param['w_choices_price'][$key]));
							$value_column = $value_table_and_column[1];

							if($value_column)
							{
								$choices_values = $this->model->select_data_from_db_for_values($db_info, $value_column, $table, $where, $order_by);
							}		

							$columns_count_radio = count($choices_labels)>0 ?  count($choices_labels) : count($choices_values);
							
							if(array_filter($choices_labels) || array_filter($choices_values))
							{
								$total_queries = $total_queries + $columns_count_radio-1;
								for($k=0; $k<$columns_count_radio; $k++)
								{
									$choice_label = isset($choices_labels) ? $choices_labels[$k] : '';
									$choice_value = isset($choices_values) ? (float)$choices_values[$k][0] : '';
			
									if(isset($post_value))
										$param['w_choices_checked'][$key]=(($post_value==$choice_value && htmlspecialchars($choice_label)==htmlspecialchars($_REQUEST['wdform_'.$id1."_element_label".$form_id])) ? 'checked="checked"' : '');
									else
										$param['w_choices_checked'][$key]=($param['w_choices_checked'][$key]=='true' ? 'checked="checked"' : '');	
		
									$rep.='<div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" '.$param['w_field_option_pos1'].'>'.$choice_label[0].'</label><div class="radio-div forlabs" '.$param['w_field_option_pos2'].'><input type="radio" id="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.$choice_value.'" title="'.htmlspecialchars($choice_label[0]).'" '.$param['w_choices_checked'][$key].' '.$param['attributes'].'><label for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'"></label></div></div>';
								}
							}			
						}
						else
						{
							if(isset($post_value))
								$param['w_choices_checked'][$key]=(($post_value==$param['w_choices_price'][$key] && htmlspecialchars($choice)==htmlspecialchars($_REQUEST['wdform_'.$id1."_element_label".$form_id])) ? 'checked="checked"' : '');
							else
								$param['w_choices_checked'][$key]=($param['w_choices_checked'][$key]=='true' ? 'checked="checked"' : '');	
								
							$rep.='<div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.$key1.'" '.$param['w_field_option_pos1'].'>'.$choice.'</label><div class="radio-div forlabs" '.$param['w_field_option_pos2'].'><input type="radio" id="wdform_'.$id1.'_element'.$form_id.''.$key1.'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.$param['w_choices_price'][$key].'" title="'.htmlspecialchars($choice).'" '.$param['w_choices_checked'][$key].' '.$param['attributes'].'><label for="wdform_'.$id1.'_element'.$form_id.''.$key1.'"></label></div></div>';
						}
					}
			   $rep.='<div id="wdform_'.$id1.'_div'.$form_id.'">';
              if($param['w_quantity']=="yes") {
                $rep.='<div class="paypal-property"><label class="mini_label" style="margin: 0px 5px;">'.(__("Quantity", 'form_maker')).'</label><input type="text" value="'.(isset($_POST['wdform_'.$id1."_element_quantity".$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1."_element_quantity".$form_id])) : $param['w_quantity_value']).'" id="wdform_'.$id1.'_element_quantity'.$form_id.'" name="wdform_'.$id1.'_element_quantity'.$form_id.'" class="wdform-quantity"></div>';
              }
              if($param['w_property'][0])	{				
                foreach($param['w_property'] as $key => $property) {
                  $rep.='
                  <div class="paypal-property">
                  <div style="width:150px; display:inline-block;">
                  <label class="mini_label" id="wdform_'.$id1.'_property_label_'.$form_id.''.$key.'" style="margin-right: 5px;">'.$property.'</label>
                  <select id="wdform_'.$id1.'_property'.$form_id.''.$key.'" name="wdform_'.$id1.'_property'.$form_id.''.$key.'" style="margin: 2px 0px;">';
                  $param['w_property_values'][$key]	= explode('###',$param['w_property_values'][$key]);
                  $param['w_property_values'][$key]	= array_slice($param['w_property_values'][$key],1, count($param['w_property_values'][$key]));   
                  foreach($param['w_property_values'][$key] as $subkey => $property_value) {
                    $rep.='<option id="wdform_'.$id1.'_'.$key.'_option'.$subkey.'" value="'.$property_value.'" '.(isset($_POST['wdform_'.$id1.'_property'.$form_id.''.$key]) && esc_html(stripslashes($_POST['wdform_'.$id1.'_property'.$form_id.''.$key])) == $property_value ? 'selected="selected"' : "").'>'.$property_value.'</option>';
                  }
                  $rep.='</select></div></div>';
                }
              }
              $rep.='</div></div></div>';
              
              if($required) {
                $check_js.='
                if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
                {
                  if(x.find(jQuery("div[wdid='.$id1.'] input:checked")).length == 0)
                  {
                    alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                    old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                    x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                    
                    return false;
                  }						
                }
                ';		
              }
              $onsubmit_js.='
                jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_element_label'.$form_id.'\" />").val(
                jQuery("label[for=\'"+jQuery("input[name^=\'wdform_'.$id1.'_element'.$form_id.'\']:checked").attr("id")+"\']").eq(0).text()
                ).appendTo("#form'.$form_id.'");

                ';
              $onsubmit_js.='
                jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_element_quantity_label'.$form_id.'\"  />").val("'.(__("Quantity", 'form_maker')).'").appendTo("#form'.$form_id.'");
                ';
              foreach($param['w_property'] as $key => $property)
              {
                $onsubmit_js.='
                  jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_element_property_label'.$form_id.$key.'\"  />").val("'.$property.'").appendTo("#form'.$form_id.'");
                  ';
              }
              break;
            }
            

            case 'type_paypal_shipping': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_flow','w_choices','w_choices_price','w_choices_checked','w_required','w_randomize','w_allow_other','w_allow_other_num','w_class');
              $temp=$params;
              if(strpos($temp, 'w_field_option_pos') > -1)
				$params_names=array('w_field_label_size','w_field_label_pos', 'w_field_option_pos', 'w_flow','w_choices','w_choices_price','w_choices_checked','w_required','w_randomize','w_allow_other','w_allow_other_num','w_choices_params', 'w_class');
				
              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp); 
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              
              if($temp) {
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }
			  if(!isset($param['w_field_option_pos']))
						$param['w_field_option_pos'] = 'left';
						
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
			  $param['w_field_option_pos1'] = ($param['w_field_option_pos']=="right" ? "style='float: none
			  !important;'" : "");
			  $param['w_field_option_pos2'] = ($param['w_field_option_pos']=="right" ? "style='float: left !important; margin:3px 8px 0 0 !important; display: inline-block !important;'" : "");
					
              $required = ($param['w_required']=="yes" ? true : false);	
              $param['w_choices']	= explode('***',$param['w_choices']);
              $param['w_choices_price']	= explode('***',$param['w_choices_price']);
              $param['w_choices_checked']	= explode('***',$param['w_choices_checked']);
              if(isset($param['w_choices_params']))
				$param['w_choices_params'] = explode('***',$param['w_choices_params']);	
						
              foreach($param['w_choices_checked'] as $key => $choices_checked ) {
                $param['w_choices_checked'][$key]=($choices_checked=='true' ? 'checked="checked"' : '');
              }
              $rep='<div type="type_paypal_shipping" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              if($required) {
                $rep.='<span class="wdform-required">'.$required_sym.'</span>';
              }
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].';">';
              $total_queries = 0;
             foreach($param['w_choices'] as $key => $choice)
					{
						$key1 = $key + $total_queries;
						if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])
						{			
							$choices_labels =array();
							$choices_values =array();
							$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
							$where = (str_replace(array('[',']'), '', $w_choices_params[0]) ? ' WHERE '.str_replace(array('[',']'), '', $w_choices_params[0]) : '');
							$w_choices_params = explode('[db_info]',$w_choices_params[1]);
							
							$order_by = str_replace(array('[',']'), '', $w_choices_params[0]);
							$db_info = str_replace(array('[',']'), '', $w_choices_params[1]);
						
							
							$label_table_and_column = explode(':',str_replace(array('[',']'), '', $choice));
							$table = $label_table_and_column[0];
							$label_column = $label_table_and_column[1];
							if($label_column)
							{
								$choices_labels = $this->model->select_data_from_db_for_labels($db_info, $label_column, $table, $where, $order_by);
						
							}	

							$value_table_and_column = explode(':',str_replace(array('[',']'), '', $param['w_choices_price'][$key]));
							$value_column = $value_table_and_column[1];

							if($value_column)
							{
								$choices_values = $this->model->select_data_from_db_for_values($db_info, $value_column, $table, $where, $order_by);
							}		

							$columns_count_shipping = count($choices_labels)>0 ?  count($choices_labels) : count($choices_values);
							
							if(array_filter($choices_labels) || array_filter($choices_values))
							{
								$total_queries = $total_queries + $columns_count_shipping-1;
								for($k=0; $k<$columns_count_shipping; $k++)
								{
									$choice_label = isset($choices_labels) ? $choices_labels[$k][0] : '';
									$choice_value = isset($choices_values) ? (float)$choices_values[$k][0] : '';
			
									if(isset($post_value))
										$param['w_choices_checked'][$key]=(($post_value==$choice_value && htmlspecialchars($choice_label)==htmlspecialchars($_REQUEST['wdform_'.$id1."_element_label".$form_id])) ? 'checked="checked"' : '');
									else
										$param['w_choices_checked'][$key]=($param['w_choices_checked'][$key]=='true' ? 'checked="checked"' : '');	
										
									$rep.='<div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" '.$param['w_field_option_pos1'].'>'.$choice_label.'</label><div class="radio-div forlabs" '.$param['w_field_option_pos2'].'><input type="radio" id="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.$choice_value.'" title="'.htmlspecialchars($choice_label[0]).'" '.$param['w_choices_checked'][$key].' '.$param['attributes'].'><label for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'"></label></div></div>';
								}	
							}	
						}
						else
						{
							if(isset($post_value))
								$param['w_choices_checked'][$key]=(($post_value==$param['w_choices_price'][$key] && htmlspecialchars($choice)==htmlspecialchars($_REQUEST['wdform_'.$id1."_element_label".$form_id])) ? 'checked="checked"' : '');
							else
								$param['w_choices_checked'][$key]=($param['w_choices_checked'][$key]=='true' ? 'checked="checked"' : '');	

							$rep.='<div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.$key1.'" '.$param['w_field_option_pos1'].'>'.$choice.'</label><div class="radio-div forlabs" '.$param['w_field_option_pos2'].'><input type="radio" id="wdform_'.$id1.'_element'.$form_id.''.$key1.'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.$param['w_choices_price'][$key].'" title="'.htmlspecialchars($choice).'" '.$param['w_choices_checked'][$key].' '.$param['attributes'].'><label for="wdform_'.$id1.'_element'.$form_id.''.$key1.'"></label></div></div>';
						}
					}

              $rep.='</div></div>';
              if($required) {
                $check_js.='
                if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
                {
                  if(x.find(jQuery("div[wdid='.$id1.'] input:checked")).length == 0)
                  {
                    alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                    old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                    x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                    
                    return false;
                  }						
                }
                ';		
              }            
              $onsubmit_js.='
                jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_element_label'.$form_id.'\" />").val(
                jQuery("label[for=\'"+jQuery("input[name^=\'wdform_'.$id1.'_element'.$form_id.'\']:checked").attr("id")+"\']").eq(0).text()
                ).appendTo("#form'.$form_id.'");
                ';                
              break;
            }

            case 'type_submit_reset': {
              $params_names=array('w_submit_title','w_reset_title','w_class','w_act');
              $temp=$params;
              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }
              $param['w_act'] = ($param['w_act']=="false" ? 'style="display: none;"' : "");	              
              $rep='<div type="type_submit_reset" class="wdform-field"><div class="wdform-label-section" style="display: table-cell;"></div><div class="wdform-element-section '.$param['w_class'].'" style="display: table-cell;"><button type="button" class="button-submit" onclick="check_required'.$form_id.'(&quot;submit&quot;, &quot;'.$form_id.'&quot;);" '.$param['attributes'].'>'.$param['w_submit_title'].'</button><button type="button" class="button-reset" onclick="check_required'.$form_id.'(&quot;reset&quot;);" '.$param['w_act'].' '.$param['attributes'].'>'.$param['w_reset_title'].'</button></div></div>';            
              break;
            }
            
            case 'type_button': {
              $params_names=array('w_title','w_func','w_class');
              $temp=$params;
              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }
              $param['w_title']	= explode('***',$param['w_title']);
              $param['w_func']	= explode('***',$param['w_func']);
              $rep.='<div type="type_button" class="wdform-field"><div class="wdform-label-section" style="display: table-cell;"><span style="display: none;">button_'.$id1.'</span></div><div class="wdform-element-section '.$param['w_class'].'" style="display: table-cell;">';

              foreach($param['w_title'] as $key => $title) {
                $rep.='<button type="button" name="wdform_'.$id1.'_element'.$form_id.''.$key.'" onclick="'.$param['w_func'][$key].'" '.$param['attributes'].'>'.$title.'</button>';
              }
              $rep.='</div></div>';
              break;
            }
            
            case 'type_star_rating': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_field_label_col','w_star_amount','w_required','w_class');
              $temp=$params;
              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
              $required = ($param['w_required']=="yes" ? true : false);	
              $images = '';	
              for($i=0; $i<$param['w_star_amount']; $i++) {
                $images .= '<img id="wdform_'.$id1.'_star_'.$i.'_'.$form_id.'" src="' . WD_FMC_URL . '/images/star.png" >';                
                $onload_js .='jQuery("#wdform_'.$id1.'_star_'.$i.'_'.$form_id.'").mouseover(function() {change_src('.$i.',"wdform_'.$id1.'", '.$form_id.', "'.$param['w_field_label_col'].'");});';
                $onload_js .='jQuery("#wdform_'.$id1.'_star_'.$i.'_'.$form_id.'").mouseout(function() {reset_src('.$i.',"wdform_'.$id1.'", '.$form_id.');});';
                $onload_js .='jQuery("#wdform_'.$id1.'_star_'.$i.'_'.$form_id.'").click(function() {select_star_rating('.$i.',"wdform_'.$id1.'", '.$form_id.',"'.$param['w_field_label_col'].'", "'.$param['w_star_amount'].'");});';
              }
              
              $rep ='<div type="type_star_rating" class="wdform-field"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos1'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              if($required) {
                $rep.='<span class="wdform-required">'.$required_sym.'</span>';
              }
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'"  style="'.$param['w_field_label_pos2'].'"><div id="wdform_'.$id1.'_element'.$form_id.'" '.$param['attributes'].'>'.$images.'</div><input type="hidden" value="" id="wdform_'.$id1.'_selected_star_amount'.$form_id.'" name="wdform_'.$id1.'_selected_star_amount'.$form_id.'"></div></div>';
              
              if($required) {
                $check_js.='
                if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
                {
                  if(jQuery("#wdform_'.$id1.'_selected_star_amount'.$form_id.'").val()=="")
                  {
                    alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                    old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                    x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                    return false;
                  }
                }
                ';
              }
              $post = isset($_POST['wdform_'.$id1.'_selected_star_amount'.$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1.'_selected_star_amount'.$form_id])) : NULL;
              if(isset($post)) {
                $onload_js .=' select_star_rating('.($post-1).',"wdform_'.$id1.'", '.$form_id.',"'.$param['w_field_label_col'].'", "'.$param['w_star_amount'].'");';
              }
              $onsubmit_js.='
                jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_star_amount'.$form_id.'\" value = \"'.$param['w_star_amount'].'\" />").appendTo("#form'.$form_id.'");
                ';
              break;
            }
            case 'type_scale_rating': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_mini_labels','w_scale_amount','w_required','w_class');
              $temp=$params;
              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
              $required = ($param['w_required']=="yes" ? true : false);	              
              $w_mini_labels = explode('***',$param['w_mini_labels']);              
              $numbers = '';	
              $radio_buttons = '';	
              $to_check=0;
              $post_value = isset($_POST['wdform_'.$id1.'_scale_radio'.$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1.'_scale_radio'.$form_id])) : NULL;
              if(isset($post_value)) {
                $to_check=$post_value;
              }
              for($i=1; $i<=$param['w_scale_amount']; $i++) {
                $numbers.= '<div  style="text-align: center; display: table-cell;"><span>'.$i.'</span></div>';
                $radio_buttons.= '<div style="text-align: center; display: table-cell;"><div class="radio-div"><input id="wdform_'.$id1.'_scale_radio'.$form_id.'_'.$i.'" name="wdform_'.$id1.'_scale_radio'.$form_id.'" value="'.$i.'" type="radio" '.( $to_check==$i ? 'checked="checked"' : '' ).'><label for="wdform_'.$id1.'_scale_radio'.$form_id.'_'.$i.'"></label></div></div>';
              }
      
              $rep ='<div type="type_scale_rating" class="wdform-field"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos1'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              if($required) {
                $rep.='<span class="wdform-required">'.$required_sym.'</span>';
              }
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'"  style="'.$param['w_field_label_pos2'].'"><div id="wdform_'.$id1.'_element'.$form_id.'" style="float: left;" '.$param['attributes'].'><label class="mini_label">'.$w_mini_labels[0].'</label><div  style="display: inline-table; vertical-align: middle;border-spacing: 7px;"><div style="display: table-row;">'.$numbers.'</div><div style="display: table-row;">'.$radio_buttons.'</div></div><label class="mini_label" >'.$w_mini_labels[1].'</label></div></div></div>';
              if($required) {
                $check_js.='
                if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
                {
                  if(x.find(jQuery("div[wdid='.$id1.'] input:checked")).length == 0)
                  {
                    alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                    old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                    x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                    
                    return false;
                  }						
                }
                ';		
              }
              $onsubmit_js.='
                jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_scale_amount'.$form_id.'\" value = \"'.$param['w_scale_amount'].'\" />").appendTo("#form'.$form_id.'");
                ';              
              break;
            }
            
            case 'type_spinner': {
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
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
              $required = ($param['w_required']=="yes" ? true : false);	
              $param['w_field_value']=(isset($_POST['wdform_'.$id1.'_element'.$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1.'_element'.$form_id])) : $param['w_field_value']);

              $rep ='<div type="type_spinner" class="wdform-field"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos1'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              if($required) {
                $rep.='<span class="wdform-required">'.$required_sym.'</span>';
              }
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'"  style="'.$param['w_field_label_pos2'].'"><input type="text" value="'.($param['w_field_value']!= 'null' ? $param['w_field_value'] : '').'" name="wdform_'.$id1.'_element'.$form_id.'" id="wdform_'.$id1.'_element'.$form_id.'" style="width: '.$param['w_field_width'].'px;" '.$param['attributes'].'></div></div>';              
              $onload_js .='
                jQuery("#form'.$form_id.' #wdform_'.$id1.'_element'.$form_id.'")[0].spin = null;
                spinner = jQuery("#form'.$form_id.' #wdform_'.$id1.'_element'.$form_id.'").spinner();
                spinner.spinner( "value", "'.($param['w_field_value']!= 'null' ? $param['w_field_value'] : '').'");
                jQuery("#form'.$form_id.' #wdform_'.$id1.'_element'.$form_id.'").spinner({ min: "'.$param['w_field_min_value'].'"});    
                jQuery("#form'.$form_id.' #wdform_'.$id1.'_element'.$form_id.'").spinner({ max: "'.$param['w_field_max_value'].'"});
                jQuery("#form'.$form_id.' #wdform_'.$id1.'_element'.$form_id.'").spinner({ step: "'.$param['w_field_step'].'"});
              ';

              if($required) {
                $check_js.='
                if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
                {
                  if(jQuery("#wdform_'.$id1.'_element'.$form_id.'").val()=="")
                  {
                    alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                    jQuery("#wdform_'.$id1.'_element'.$form_id.'").addClass( "form-error" );
                    old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                    x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                    jQuery("#wdform_'.$id1.'_element'.$form_id.'").focus();
                    jQuery("#wdform_'.$id1.'_element'.$form_id.'").change(function() { if( jQuery(this).val()!="" ) jQuery(this).removeClass("form-error"); else jQuery(this).addClass("form-error");});
                    return false;
                  }
                }
                ';		
              }
              break;
            }
            
            case 'type_slider': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_field_width','w_field_min_value','w_field_max_value', 'w_field_value', 'w_required','w_class');
              $temp=$params;
              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
              $required = ($param['w_required']=="yes" ? true : false);	
              $param['w_field_value']=(isset($_POST['wdform_'.$id1.'_slider_value'.$form_id]) ? esc_html(stripslashes($_POST['wdform_'.$id1.'_slider_value'.$form_id])) : $param['w_field_value']);

              $rep ='<div type="type_slider" class="wdform-field"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos1'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              if($required) {
                $rep.='<span class="wdform-required">'.$required_sym.'</span>';
              }               
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'"  style="'.$param['w_field_label_pos2'].'"><input type="hidden" value="'.$param['w_field_value'].'" id="wdform_'.$id1.'_slider_value'.$form_id.'" name="wdform_'.$id1.'_slider_value'.$form_id.'"><div name="'.$id1.'_element'.$form_id.'" id="wdform_'.$id1.'_element'.$form_id.'" style="width: '.$param['w_field_width'].'px;" '.$param['attributes'].'"></div><div align="left" style="display: inline-block; width: 33.3%; text-align: left;"><span id="wdform_'.$id1.'_element_min'.$form_id.'" class="label">'.$param['w_field_min_value'].'</span></div><div align="right" style="display: inline-block; width: 33.3%; text-align: center;"><span id="wdform_'.$id1.'_element_value'.$form_id.'" class="label">'.$param['w_field_value'].'</span></div><div align="right" style="display: inline-block; width: 33.3%; text-align: right;"><span id="wdform_'.$id1.'_element_max'.$form_id.'" class="label">'.$param['w_field_max_value'].'</span></div></div></div>';
              $onload_js .='
                jQuery("#wdform_'.$id1.'_element'.$form_id.'")[0].slide = null;
                jQuery("#wdform_'.$id1.'_element'.$form_id.'").slider({
                  range: "min",
                  value: eval('.$param['w_field_value'].'),
                  min: eval('.$param['w_field_min_value'].'),
                  max: eval('.$param['w_field_max_value'].'),
                  slide: function( event, ui ) {	
                  
                    jQuery("#wdform_'.$id1.'_element_value'.$form_id.'").html("" + ui.value);
                    jQuery("#wdform_'.$id1.'_slider_value'.$form_id.'").val("" + ui.value);

                  }
                  });
              ';
              if($required) {
                $check_js.='
                if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
                {
                  if(jQuery("#wdform_'.$id1.'_slider_value'.$form_id.'").val()=='.$param['w_field_min_value'].')
                  {
                    alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                    old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                    x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                    return false;
                  }
                }
                ';		
              }
              break;
            }
            
            case 'type_range': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_field_range_width','w_field_range_step','w_field_value1', 'w_field_value2', 'w_mini_labels', 'w_required','w_class');
              $temp=$params;
              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
              $required = ($param['w_required']=="yes" ? true : false);	
              $param['w_field_value1']=(isset($_POST['wdform_'.$id1.'_element'.$form_id.'0']) ? esc_html(stripslashes($_POST['wdform_'.$id1.'_element'.$form_id.'0'])) : $param['w_field_value1']);
              $param['w_field_value2']=(isset($_POST['wdform_'.$id1.'_element'.$form_id.'1']) ? esc_html(stripslashes($_POST['wdform_'.$id1.'_element'.$form_id.'1'])) : $param['w_field_value2']);

              $w_mini_labels = explode('***',$param['w_mini_labels']);              
              $rep ='<div type="type_range" class="wdform-field"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos1'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              if($required) {
                $rep.='<span class="wdform-required">'.$required_sym.'</span>';
              }
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'"  style="'.$param['w_field_label_pos2'].'"><div style="display: table;"><div style="display: table-row;"><div valign="middle" align="left" style="display: table-cell;"><input type="text" value="'.($param['w_field_value1']!= 'null' ? $param['w_field_value1'] : '').'" name="wdform_'.$id1.'_element'.$form_id.'0" id="wdform_'.$id1.'_element'.$form_id.'0" style="width: '.$param['w_field_range_width'].'px;"  '.$param['attributes'].'></div><div valign="middle" align="left" style="display: table-cell; padding-left: 4px;"><input type="text" value="'.($param['w_field_value2']!= 'null' ? $param['w_field_value2'] : '').'" name="wdform_'.$id1.'_element'.$form_id.'1" id="wdform_'.$id1.'_element'.$form_id.'1" style="width: '.$param['w_field_range_width'].'px;" '.$param['attributes'].'></div></div><div style="display: table-row;"><div valign="top" align="left" style="display: table-cell;"><label class="mini_label" id="wdform_'.$id1.'_mini_label_from">'.$w_mini_labels[0].'</label></div><div valign="top" align="left" style="display: table-cell;"><label class="mini_label" id="wdform_'.$id1.'_mini_label_to">'.$w_mini_labels[1].'</label></div></div></div></div></div>';
              $onload_js .='
                jQuery("#form'.$form_id.' #wdform_'.$id1.'_element'.$form_id.'0")[0].spin = null;
                jQuery("#form'.$form_id.' #wdform_'.$id1.'_element'.$form_id.'1")[0].spin = null;
                
                spinner0 = jQuery("#form'.$form_id.' #wdform_'.$id1.'_element'.$form_id.'0").spinner();
                spinner0.spinner( "value", "'.($param['w_field_value1']!= 'null' ? $param['w_field_value1'] : '').'");
                jQuery("#form'.$form_id.' #wdform_'.$id1.'_element'.$form_id.'").spinner({ step: '.$param['w_field_range_step'].'});
                
                spinner1 = jQuery("#form'.$form_id.' #wdform_'.$id1.'_element'.$form_id.'1").spinner();
                spinner1.spinner( "value", "'.($param['w_field_value2']!= 'null' ? $param['w_field_value2'] : '').'");
                jQuery("#form'.$form_id.' #wdform_'.$id1.'_element'.$form_id.'").spinner({ step: '.$param['w_field_range_step'].'});
              ';
              
              if($required) {
                $check_js.='
                if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
                {
                  if(jQuery("#wdform_'.$id1.'_element'.$form_id.'0").val()=="" || jQuery("#wdform_'.$id1.'_element'.$form_id.'1").val()=="")
                  {
                    alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                    old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                    x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                    jQuery("#wdform_'.$id1.'_element'.$form_id.'0").focus();
                    return false;
                  }
                }
                ';		
              }
              break;
            }
            
            case 'type_grading': {
              $params_names=array('w_field_label_size','w_field_label_pos', 'w_items', 'w_total', 'w_required','w_class');
              $temp=$params;
              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if($temp) {
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
              $required = ($param['w_required']=="yes" ? true : false);	
              
              $w_items = explode('***',$param['w_items']);
              $required_check='true';
              $w_items_labels =implode(':',$w_items);              
              $grading_items ='';
            
              for($i=0; $i<count($w_items); $i++) {
                $value=(isset($_POST['wdform_'.$id1.'_element'.$form_id.'_'.$i]) ? esc_html(stripslashes($_POST['wdform_'.$id1.'_element'.$form_id.'_'.$i])) : '');
                $grading_items .= '<div class="wdform_grading"><input type="text" id="wdform_'.$id1.'_element'.$form_id.'_'.$i.'" name="wdform_'.$id1.'_element'.$form_id.'_'.$i.'"  value="'.$value.'" '.$param['attributes'].'><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.'_'.$i.'">'.$w_items[$i].'</label></div>';
                $required_check.=' && jQuery("#wdform_'.$id1.'_element'.$form_id.'_'.$i.'").val()==""';
              }                
              $rep ='<div type="type_grading" class="wdform-field"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos1'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              if($required) {
                $rep.='<span class="wdform-required">'.$required_sym.'</span>';
              }                
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'"  style="'.$param['w_field_label_pos2'].'"><input type="hidden" value="'.$param['w_total'].'" name="wdform_'.$id1.'_grading_total'.$form_id.'" id="wdform_'.$id1.'_grading_total'.$form_id.'"><div id="wdform_'.$id1.'_element'.$form_id.'">'.$grading_items.'<div id="wdform_'.$id1.'_element_total_div'.$form_id.'" class="grading_div">Total: <span id="wdform_'.$id1.'_sum_element'.$form_id.'">0</span>/<span id="wdform_'.$id1.'_total_element'.$form_id.'">'.$param['w_total'].'</span><span id="wdform_'.$id1.'_text_element'.$form_id.'"></span></div></div></div></div>';              
              $onload_js.='
              jQuery("#wdform_'.$id1.'_element'.$form_id.' input").change(function() {sum_grading_values("wdform_'.$id1.'",'.$form_id.');});';              
              $onload_js.='
              jQuery("#wdform_'.$id1.'_element'.$form_id.' input").keyup(function() {sum_grading_values("wdform_'.$id1.'",'.$form_id.');});';              
              $onload_js.='
              jQuery("#wdform_'.$id1.'_element'.$form_id.' input").keyup(function() {sum_grading_values("wdform_'.$id1.'",'.$form_id.');});';              
              $onload_js.='
              sum_grading_values("wdform_'.$id1.'",'.$form_id.');';
              if($required) {
                $check_js.='
                if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
                {
                  if('.$required_check.')
                  {
                    alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                    old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                    x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                    jQuery("#wdform_'.$id1.'_element'.$form_id.'0").focus();
                    return false;
                  }
                }
                ';		
              }
              $check_js.='
              if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
              {
                if(parseInt(jQuery("#wdform_'.$id1.'_sum_element'.$form_id.'").html()) > '.$param['w_total'].')
                {
                  alert("' . addslashes(__("Your score should be less than", 'form_maker')) . $param['w_total'] . '");
                  return false;
                }
              }
              ';		
              
              $onsubmit_js.='
                jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_hidden_item'.$form_id.'\" value = \"'.$w_items_labels.':'.$param['w_total'].'\" />").appendTo("#form'.$form_id.'");
                ';              
              break;
            }
            case 'type_matrix': {
              $params_names=array('w_field_label_size','w_field_label_pos', 'w_field_input_type', 'w_rows', 'w_columns', 'w_required','w_class','w_textbox_size');
              $temp=$params;
              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }              
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");
              $required = ($param['w_required']=="yes" ? true : false);	
              $param['w_textbox_size'] = isset($param['w_textbox_size']) ? $param['w_textbox_size'] : '120';            
              $w_rows = explode('***',$param['w_rows']);
              $w_columns = explode('***',$param['w_columns']);
              $column_labels ='';
            
              for($i=1; $i<count($w_columns); $i++) {
                $column_labels .= '<div><label class="wdform-ch-rad-label">'.$w_columns[$i].'</label></div>';
              }              
              $rows_columns = '';
              for($i=1; $i<count($w_rows); $i++) {              
                $rows_columns .= '<div class="wdform-matrix-row'.($i%2).'" row="'.$i.'"><div class="wdform-matrix-column"><label class="wdform-ch-rad-label" >'.$w_rows[$i].'</label></div>';
                for($k=1; $k<count($w_columns); $k++) {
                  $rows_columns .= '<div class="wdform-matrix-cell">';
                  if($param['w_field_input_type']=='radio') {
                    $to_check=0;
                    $post_value = isset($_POST['wdform_'.$id1.'_input_element'.$form_id.''.$i]) ? esc_html(stripslashes($_POST['wdform_'.$id1.'_input_element'.$form_id.''.$i])) : NULL;
                    if(isset($post_value)) {
                      $to_check=$post_value;	
                    }
                    $rows_columns .= '<div class="radio-div"><input id="wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k.'"  type="radio" name="wdform_'.$id1.'_input_element'.$form_id.''.$i.'" value="'.$i.'_'.$k.'" '.($to_check==$i.'_'.$k ? 'checked="checked"' : '').'><label for="wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k.'"></label></div>';                    
                  }
                  else {
                    if($param['w_field_input_type']=='checkbox') {
                      $to_check=0;
                      $post_value = isset($_POST['wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k]) ? esc_html(stripslashes($_POST['wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k])) : NULL;
                      if(isset($post_value)) {
                        $to_check=$post_value;	
                      }
                      $rows_columns .= '<div class="checkbox-div"><input id="wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k.'" type="checkbox" name="wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k.'" value="1" '.($to_check=="1" ? 'checked="checked"' : '').'><label for="wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k.'"></label></div>';
                    }
                    else {
                      if($param['w_field_input_type']=='text') {
                        $rows_columns .= '<input id="wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k.'" type="text" name="wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k.'" value="'.(isset($_POST['wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k]) ? esc_html(stripslashes($_POST['wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k])) : "").'" style="width:'.$param['w_textbox_size'].'px">';
                      }
                      else {
                        if($param['w_field_input_type']=='select') {
                          $rows_columns .= '<select id="wdform_'.$id1.'_select_yes_no'.$form_id.''.$i.'_'.$k.'" name="wdform_'.$id1.'_select_yes_no'.$form_id.''.$i.'_'.$k.'" ><option value="" '.(isset($_POST['wdform_'.$id1.'_select_yes_no'.$form_id.''.$i.'_'.$k]) && esc_html($_POST['wdform_'.$id1.'_select_yes_no'.$form_id.''.$i.'_'.$k]) == "" ? "selected=\"selected\"": "").'> </option><option value="yes" '.(isset($_POST['wdform_'.$id1.'_select_yes_no'.$form_id.''.$i.'_'.$k]) && esc_html($_POST['wdform_'.$id1.'_select_yes_no'.$form_id.''.$i.'_'.$k]) == "yes" ? "selected=\"selected\"": "").'>Yes</option><option value="no" '.(isset($_POST['wdform_'.$id1.'_select_yes_no'.$form_id.''.$i.'_'.$k]) && esc_html($_POST['wdform_'.$id1.'_select_yes_no'.$form_id.''.$i.'_'.$k]) == "no" ? "selected=\"selected\"": "").'>No</option></select>';
                        }
                      }
                    }
                  }
                  $rows_columns.='</div>';
                } 
                $rows_columns .= '</div>';	
              }
                
              $rep ='<div type="type_matrix" class="wdform-field"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos1'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              if($required) {
                $rep.='<span class="wdform-required">'.$required_sym.'</span>';
              }                
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'"  style="'.$param['w_field_label_pos2'].'"><div id="wdform_'.$id1.'_element'.$form_id.'" class="wdform-matrix-table" '.$param['attributes'].'><div style="display: table-row-group;"><div class="wdform-matrix-head"><div style="display: table-cell;"></div>'.$column_labels.'</div>'.$rows_columns.'</div></div></div></div>';              
              $onsubmit_js.='
                jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_input_type'.$form_id.'\" value = \"'.$param['w_field_input_type'].'\" /><input type=\"hidden\" name=\"wdform_'.$id1.'_hidden_row'.$form_id.'\" value = \"'.addslashes($param['w_rows']).'\" /><input type=\"hidden\" name=\"wdform_'.$id1.'_hidden_column'.$form_id.'\" value = \"'.addslashes($param['w_columns']).'\" />").appendTo("#form'.$form_id.'");
                ';
              if($required) {
                if($param['w_field_input_type']=='radio') {
                  $check_js.='
                  if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
                  {
                    var radio_checked=true;
                    for(var k=1; k<'.count($w_rows).';k++)
                    {
                      if(x.find(jQuery("div[wdid='.$id1.']")).find(jQuery("div[row="+k+"]")).find(jQuery("input[type=\'radio\']:checked")).length == 0)
                      {
                        radio_checked=false;
                        break;
                      }
                    }
                    
                    if(radio_checked==false)
                    {
                      alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                      old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                      x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                      return false;
                    }
                  }
                  ';		
                }                
                if($param['w_field_input_type']=='checkbox') {
                  $check_js.='
                  if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
                  {
                    if(x.find(jQuery("div[wdid='.$id1.']")).find(jQuery("input[type=\'checkbox\']:checked")).length == 0)
                    {
                      alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                      old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                      x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                      return false;
                    }
                  }
                  
                  ';		
                }                
                if($param['w_field_input_type']=='text') {
                  $check_js.='
                  if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
                  {
                    if(x.find(jQuery("div[wdid='.$id1.']")).find(jQuery("input[type=\'text\']")).filter(function() {return this.value.length !== 0;}).length == 0)
                    {
                      alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                      old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                      x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                      return false;
                    }
                  }                  
                  ';		
                }
                
                if($param['w_field_input_type']=='select') {
                  $check_js.='
                  if(x.find(jQuery("div[wdid='.$id1.']")).length != 0 && x.find(jQuery("div[wdid='.$id1.']")).css("display") != "none")
                  {
                    if(x.find(jQuery("div[wdid='.$id1.']")).find(jQuery("select")).filter(function() {return this.value.length !== 0;}).length == 0)
                    {
                      alert("' .addslashes($label. ' ' . __('field is required.', 'form_maker')) . '");
                      old_bg=x.find(jQuery("div[wdid='.$id1.']")).css("background-color");
                      x.find(jQuery("div[wdid='.$id1.']")).effect( "shake", {}, 500 ).css("background-color","#FF8F8B").animate({backgroundColor: old_bg}, {duration: 500, queue: false });
                      return false;
                    }
                  }
                  
                  ';		
                }
              }
              break;
            }
          
            case 'type_paypal_total': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_class');
              $temp=$params;
              foreach($params_names as $params_name ) {
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }              
              $param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	
              $param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");                    
              $rep ='<div type="type_paypal_total" class="wdform-field"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos1'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';                      
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'"  style="'.$param['w_field_label_pos2'].'"><div id="wdform_'.$id1.'paypal_total'.$form_id.'" class="wdform_paypal_total paypal_total'.$form_id.'"><input type="hidden" value="" name="wdform_'.$id1.'_paypal_total'.$form_id.'" class="input_paypal_total'.$form_id.'"><div id="wdform_'.$id1.'div_total'.$form_id.'" class="div_total'.$form_id.'" style="margin-bottom: 10px;"></div><div id="wdform_'.$id1.'paypal_products'.$form_id.'" class="paypal_products'.$form_id.'" style="border-spacing: 2px;"><div style="border-spacing: 2px;"></div><div style="border-spacing: 2px;"></div></div><div id="wdform_'.$id1.'paypal_tax'.$form_id.'" class="paypal_tax'.$form_id.'" style="border-spacing: 2px; margin-top: 7px;"></div></div></div></div>';            
              $onload_js .='set_total_value('.$form_id.');';
              break;
            }
          }          
          $form=str_replace('%'.$id1.' - '.$labels[$id1s_key].'%', $rep, $form);
          $form=str_replace('%'.$id1.' -'.$labels[$id1s_key].'%', $rep, $form);
        }        
      }
      
      $onsubmit_js.='
        var disabled_fields ="";	
        jQuery("div[wdid]").each(function() {
          if(jQuery(this).css("display")=="none")
          {		
            disabled_fields += jQuery(this).attr("wdid");
            disabled_fields += ",";
          }	

            if(disabled_fields)
            jQuery("<input type=\"hidden\" name=\"disabled_fields'.$form_id.'\" value =\""+disabled_fields+"\" />").appendTo("#form'.$form_id.'");
            
        })';
      
      $rep1=array('form_id_temp');
      $rep2=array($id);

      $form = str_replace($rep1,$rep2,$form);

      $form_maker_front_end .= $form;
      $form_maker_front_end .= '<div class="wdform_preload"></div>';
      $form_maker_front_end .= '</form>';
    ?>
    <script type="text/javascript">
		var plugin_url = "<?php echo WD_FMC_URL; ?>";
      WDF_GRADING_TEXT ='<?php echo addslashes(__("Your score should be less than", 'form_maker')); ?>'; 
      WDF_INVALID_GRADING_<?php echo $id; ?> 	= '<?php echo addslashes(sprintf(__("Your score should be less than", 'form_maker'), '`grading_label`', '`grading_total`')); ?>';
      FormCurrency_<?php echo $id; ?> = '<?php echo $form_currency ?>';  
      FormPaypalTax_<?php echo $id; ?> = '<?php echo $form_paypal_tax ?>';  

      function formOnload<?php echo $id; ?>() {
        if (navigator.userAgent.toLowerCase().indexOf('msie') != -1 && parseInt(navigator.userAgent.toLowerCase().split('msie')[1]) === 8) {
          jQuery("#form<?php echo $id; ?>").find(jQuery("input[type='radio']")).click(function() {jQuery("input[type='radio']+label").removeClass('if-ie-div-label'); jQuery("input[type='radio']:checked+label").addClass('if-ie-div-label')});	
          jQuery("#form<?php echo $id; ?>").find(jQuery("input[type='radio']:checked+label")).addClass('if-ie-div-label');
          jQuery("#form<?php echo $id; ?>").find(jQuery("input[type='checkbox']")).click(function() {jQuery("input[type='checkbox']+label").removeClass('if-ie-div-label'); jQuery("input[type='checkbox']:checked+label").addClass('if-ie-div-label')});	
          jQuery("#form<?php echo $id; ?>").find(jQuery("input[type='checkbox']:checked+label")).addClass('if-ie-div-label');
        }

        jQuery("div[type='type_text'] input, div[type='type_number'] input, div[type='type_phone'] input, div[type='type_name'] input, div[type='type_submitter_mail'] input, div[type='type_paypal_price'] input, div[type='type_textarea'] textarea").focus(function() {delete_value(this)}).blur(function() {return_value(this)});
        jQuery("div[type='type_number'] input, div[type='type_phone'] input, div[type='type_spinner'] input, div[type='type_range'] input, .wdform-quantity").keypress(function(evt) {return check_isnum(evt)});
        
        jQuery("div[type='type_grading'] input").keypress(function(evt) {return check_isnum_or_minus(evt)});
        
        jQuery("div[type='type_paypal_checkbox'] input[type='checkbox'], div[type='type_paypal_radio'] input[type='radio'], div[type='type_paypal_shipping'] input[type='radio']").click(function() {set_total_value(<?php echo $form_id; ?>)});
        jQuery("div[type='type_paypal_select'] select, div[type='type_paypal_price'] input").change(function() {set_total_value(<?php echo $form_id; ?>)});
        jQuery(".wdform-quantity").change(function() {set_total_value(<?php echo $form_id; ?>)});

        jQuery("div[type='type_address'] select").change(function() {set_total_value(<?php echo $form_id; ?>)});
        
        jQuery("div[type='type_time'] input").blur(function() {add_0(this)});

        jQuery('.wdform-element-section').each(function() {
          if(!jQuery(this).parent()[0].style.width && parseInt(jQuery(this).width())!=0)
          {
            
            if(jQuery(this).css('display')=="table-cell")
            {
              if(jQuery(this).parent().attr('type')!="type_captcha")
                jQuery(this).parent().css('width', parseInt(jQuery(this).width()) + parseInt(jQuery(this).parent().find(jQuery(".wdform-label-section"))[0].style.width)+15);
              else
                jQuery(this).parent().css('width', (parseInt(jQuery(this).parent().find(jQuery(".captcha_input"))[0].style.width)*2+50) + parseInt(jQuery(this).parent().find(jQuery(".wdform-label-section"))[0].style.width)+15);
            }
          }
          if (!jQuery(this).parent()[0].style.width && parseInt(jQuery(this).width()) != 0) {
            if (jQuery(this).css('display') == "table-cell") {
              if (jQuery(this).parent().attr('type') != "type_captcha") {
                jQuery(this).parent().css('width', parseInt(jQuery(this).width()) + parseInt(jQuery(this).parent().find(jQuery(".wdform-label-section"))[0].style.width)+15);
              }
              else {
                jQuery(this).parent().css('width', (parseInt(jQuery(this).parent().find(jQuery(".captcha_input"))[0].style.width)*2+50) + parseInt(jQuery(this).parent().find(jQuery(".wdform-label-section"))[0].style.width)+15);
              }
            }
          }
          if(parseInt(jQuery(this)[0].style.width.replace('px', '')) < parseInt(jQuery(this).css('min-width').replace('px', '')))
            jQuery(this).css('min-width', parseInt(jQuery(this)[0].style.width.replace('px', ''))-10);
        });	
        
        jQuery('.wdform-label').each(function() {
          if(parseInt(jQuery(this).height()) >= 2*parseInt(jQuery(this).css('line-height').replace('px', '')))
          {
            jQuery(this).parent().css('max-width',jQuery(this).parent().width());
            jQuery(this).parent().css('width','');
          }
        });
        
        (function(jQuery){
          jQuery.fn.shuffle = function() {
            var allElems = this.get(),
              getRandom = function(max) {
                return Math.floor(Math.random() * max);
              },
              shuffled = jQuery.map(allElems, function(){
                var random = getRandom(allElems.length),
                  randEl = jQuery(allElems[parseInt(random)]).clone(true)[0];
                allElems.splice(random, 1);
                return randEl;
               });
            this.each(function(i){
              jQuery(this).replaceWith(jQuery(shuffled[i]));
            });
            return jQuery(shuffled);
          };
        })(jQuery);
        
        <?php echo $onload_js; ?>
        <?php echo $condition_js; ?>
        
        if(window.before_load)
        {
          before_load();
        }
      }

      jQuery(window).load(function () {
        formOnload<?php echo $id ?>();
      });

      form_view_count<?php echo $id ?>=0;
      jQuery(document).ready(function () {
        for(i=1; i<=30; i++) {
          if (document.getElementById('<?php echo $id ?>form_view'+i)) {
            form_view_count<?php echo $id ?>++;
            form_view_max<?php echo $id ?> = i;
          }
        }
        if (form_view_count<?php echo $id ?> > 1) {
          for (i = 1; i <= form_view_max<?php echo $id ?>; i++) {
            if (document.getElementById('<?php echo $id ?>form_view' + i)) {
              first_form_view<?php echo $id ?> = i;
              break;
            }
          }
          generate_page_nav(first_form_view<?php echo $id ?>, '<?php echo $id ?>', form_view_count<?php echo $id ?>, form_view_max<?php echo $id ?>);
        }
      });
      function check_required<?php echo $form_id ?>(but_type) {
        if (but_type == 'reset') {
          if (window.before_reset) {
            before_reset();
          }
         var types = <?php echo json_encode($types) ?>;
			var label_ids = <?php echo json_encode($id1s) ?>;
			jQuery.each(types, function (index, elem) {
				switch(elem) {
					case "type_text":
					case "type_password":
					case "type_textarea":
					case "type_number":
					case "type_submitter_mail":	
					case "type_spinner":
					case 'type_own_select':
					case 'type_country':
					case 'type_date': 
					case 'type_hidden': 
						jQuery("#wdform_"+label_ids[index]+"_element<?php echo $id ?>").val('');
					break;
						
					case 'type_send_copy':	
						jQuery("#wdform_"+label_ids[index]+"_element<?php echo $id ?>").prop('checked', false);
					break;
					case 'type_phone':	
						jQuery("#wdform_"+label_ids[index]+"_element_first<?php echo $id ?>, #wdform_"+label_ids[index]+"_element_last<?php echo $id ?>").val('');
					break;
					
					case 'type_name':	
						jQuery("#wdform_"+label_ids[index]+"_element_first<?php echo $id ?>, #wdform_"+label_ids[index]+"_element_last<?php echo $id ?>, #wdform_"+label_ids[index]+"_element_title<?php echo $id ?>, #wdform_"+label_ids[index]+"_element_middle<?php echo $id ?>").val('');
					break;
					
					case 'type_address':
						jQuery("#wdform_"+label_ids[index]+"_street1<?php echo $id ?>, #wdform_"+label_ids[index]+"_street2<?php echo $id ?>, #wdform_"+label_ids[index]+"_city<?php echo $id ?>, #wdform_"+label_ids[index]+"_state<?php echo $id ?>, #wdform_"+label_ids[index]+"_postal<?php echo $id ?>, #wdform_"+label_ids[index]+"_country<?php echo $id ?>").val('');
					break;
					
					case 'type_checkbox':
						jQuery("#form<?php echo $id ?> div[wdid='"+label_ids[index]+"'] .checkbox-div input").prop('checked', false);
						jQuery("#wdform_"+label_ids[index]+"_other_br<?php echo $id ?>").remove();
						jQuery("#wdform_"+label_ids[index]+"_other_input<?php echo $id ?>").remove();
					break;
					
					case 'type_radio':
						jQuery("#form<?php echo $id ?> div[wdid='"+label_ids[index]+"'] .radio-div input").prop('checked', false);
						jQuery("#wdform_"+label_ids[index]+"_other_br<?php echo $id ?>").remove();
						jQuery("#wdform_"+label_ids[index]+"_other_input<?php echo $id ?>").remove();
					break;
           
					case 'type_time':
						jQuery("#wdform_"+label_ids[index]+"_hh<?php echo $id ?>, #wdform_"+label_ids[index]+"_mm<?php echo $id ?>, #wdform_"+label_ids[index]+"_ss<?php echo $id ?>, #wdform_"+label_ids[index]+"_am_pm<?php echo $id ?>").val('');
					break;
            
					case 'type_date_fields': 
						jQuery("#wdform_"+label_ids[index]+"_day<?php echo $id ?>, #wdform_"+label_ids[index]+"_month<?php echo $id ?>, #wdform_"+label_ids[index]+"_year<?php echo $id ?>").val('');
					break;

					case 'type_file_upload':
						jQuery("#wdform_"+label_ids[index]+"_element<?php echo $id ?>_save").remove();
					break;
            
					case 'type_paypal_price': 
						jQuery("#wdform_"+label_ids[index]+"_element_dollars<?php echo $id ?>, #wdform_"+label_ids[index]+"_element_cents<?php echo $id ?>").val('');
					break;
            
					case 'type_paypal_select': 
						jQuery("#wdform_"+label_ids[index]+"_element<?php echo $id ?>, #wdform_"+label_ids[index]+"_element_quantity<?php echo $id ?>, #form<?php echo $id ?> div[wdid='"+label_ids[index]+"'] .paypal-property select").val('');
					break;
					
					case 'type_paypal_radio': 
						jQuery("#wdform_"+label_ids[index]+"_element_quantity<?php echo $id ?>,#form<?php echo $id ?> div[wdid='"+label_ids[index]+"'] .paypal-property select").val('');
						jQuery("#form<?php echo $id ?> div[wdid='"+label_ids[index]+"'] .radio-div input").prop('checked', false);
					break;
					
					case 'type_paypal_shipping':
						jQuery("#form<?php echo $id ?> div[wdid='"+label_ids[index]+"'] .radio-div input").prop('checked', false);
					break;
            
					case 'type_paypal_checkbox':
						jQuery("#wdform_"+label_ids[index]+"_element_quantity<?php echo $id ?>,#form<?php echo $id ?> div[wdid='"+label_ids[index]+"'] .paypal-property select").val('');
						jQuery("#form<?php echo $id ?> div[wdid='"+label_ids[index]+"'] .checkbox-div input").prop('checked', false);
					break;
				
					case 'type_star_rating': 
						jQuery("#wdform_"+label_ids[index]+"_selected_star_amount<?php echo $id ?>").val('');
						jQuery("#wdform_"+label_ids[index]+"_element<?php echo $id ?> img").attr('src', plugin_url+'/images/star.png');
					break;
					
					case 'type_scale_rating': 
						jQuery("#form<?php echo $id ?> div[wdid='"+label_ids[index]+"'] .radio-div input").prop('checked', false);
					break;	

					case 'type_slider':
						jQuery("#wdform_"+label_ids[index]+"_element<?php echo $id ?>").slider({
							value: eval(0),
						});
						jQuery("#wdform_"+label_ids[index]+"_element_value<?php echo $id ?>").html('0');
					break;	
					
					case 'type_range':
						jQuery("#wdform_"+label_ids[index]+"_element<?php echo $id ?>0, #wdform_"+label_ids[index]+"_element<?php echo $id ?>1").val('');					
					break;	
					
					case 'type_grading': 
						jQuery("#wdform_"+label_ids[index]+"_element<?php echo $id ?> input").val('');
					break;
					
					case 'type_matrix':
						jQuery("#wdform_"+label_ids[index]+"_element<?php echo $id ?> .radio-div input").prop('checked', false);
						jQuery("#wdform_"+label_ids[index]+"_element<?php echo $id ?> .checkbox-div input").prop('checked', false);
						jQuery("#wdform_"+label_ids[index]+"_element<?php echo $id ?> input[type='text']").val('');
						jQuery("#wdform_"+label_ids[index]+"_element<?php echo $id ?> select").val('');
					break;
					
					case 'type_paypal_total': 
						jQuery("#wdform_"+label_ids[index]+"div_total<?php echo $id ?>").html('$0');	
						jQuery("#wdform_"+label_ids[index]+"paypal_products<?php echo $id ?>").empty();	
					break;	
					default:
					break;
				}
			});
			
          return;
        }
        if (window.before_submit) {
          before_submit();
        }
        x = jQuery("#form<?php echo $form_id; ?>");
        <?php echo $check_js; ?>;
        var a = [];
        if (typeof a[<?php echo $form_id ?>] !== 'undefined' && a[<?php echo $form_id ?>] == 1) {
          return;
        }
        <?php echo $onsubmit_js; ?>;
        a[<?php echo $form_id ?>] = 1;
        document.getElementById("form"+<?php echo $form_id ?>).submit();
      }
      function check<?php echo $form_id ?>(id) {
        x = jQuery("#<?php echo $form_id ?>form_view"+id);
        <?php echo $check_js; ?>;
        return true;
      }
    </script>
    <?php
    }
    else {
      $captcha_url = 'components/com_formmaker/wd_captcha.php?digit=';
      $captcha_rep_url = 'components/com_formmaker/wd_captcha.php?r2=' . mt_rand(0, 1000) . '&digit=';
      $rep1 = array(
        "<!--repstart-->Title<!--repend-->",
        "<!--repstart-->First<!--repend-->",
        "<!--repstart-->Last<!--repend-->",
        "<!--repstart-->Middle<!--repend-->",
        "<!--repstart-->January<!--repend-->",
        "<!--repstart-->February<!--repend-->",
        "<!--repstart-->March<!--repend-->",
        "<!--repstart-->April<!--repend-->",
        "<!--repstart-->May<!--repend-->",
        "<!--repstart-->June<!--repend-->",
        "<!--repstart-->July<!--repend-->",
        "<!--repstart-->August<!--repend-->",
        "<!--repstart-->September<!--repend-->",
        "<!--repstart-->October<!--repend-->",
        "<!--repstart-->November<!--repend-->",
        "<!--repstart-->December<!--repend-->",
        "<!--repstart-->Street Address<!--repend-->",
        "<!--repstart-->Street Address Line 2<!--repend-->",
        "<!--repstart-->City<!--repend-->",
        "<!--repstart-->State / Province / Region<!--repend-->",
        "<!--repstart-->Postal / Zip Code<!--repend-->",
        "<!--repstart-->Country<!--repend-->",
        "<!--repstart-->Area Code<!--repend-->",
        "<!--repstart-->Phone Number<!--repend-->",
        "<!--repstart-->Dollars<!--repend-->",
        "<!--repstart-->Cents<!--repend-->",
        "<!--repstart-->&nbsp;$&nbsp;<!--repend-->",
        "<!--repstart-->Quantity<!--repend-->",
        "<!--repstart-->From<!--repend-->",				
        "<!--repstart-->To<!--repend-->",
        "<!--repstart-->$300<!--repend-->",
        "<!--repstart-->product 1 $100<!--repend-->",
        "<!--repstart-->product 2 $200<!--repend-->",
        $captcha_url,
        'class="captcha_img"',
        plugins_url("images/refresh.png", __FILE__),
        'form_id_temp',
        'style="padding-right:170px"'
      );
      $rep2 = array(
        addslashes(__("Title", 'form_maker')),
        addslashes(__("First", 'form_maker')),
        addslashes(__("Last", 'form_maker')),
        addslashes(__("Middle", 'form_maker')),
        addslashes(__("January", 'form_maker')),
        addslashes(__("February", 'form_maker')),
        addslashes(__("March", 'form_maker')),
        addslashes(__("April", 'form_maker')),
        addslashes(__("May", 'form_maker')),
        addslashes(__("June", 'form_maker')),
        addslashes(__("July", 'form_maker')),
        addslashes(__("August", 'form_maker')),
        addslashes(__("September", 'form_maker')),
        addslashes(__("October", 'form_maker')),
        addslashes(__("November", 'form_maker')),
        addslashes(__("December", 'form_maker')),
        addslashes(__("Street Address", 'form_maker')),
        addslashes(__("Street Address Line 2", 'form_maker')),
        addslashes(__("City", 'form_maker')),
        addslashes(__("State / Province / Region", 'form_maker')),
        addslashes(__("Postal / Zip Code", 'form_maker')),
        addslashes(__("Country", 'form_maker')),
        addslashes(__("Area Code", 'form_maker')),
        addslashes(__("Phone Number", 'form_maker')),
        addslashes(__("Dollars", 'form_maker')),
        addslashes(__("Cents", 'form_maker')),
        '&nbsp;' . $form_currency . '&nbsp;',
        addslashes(__("Quantity", 'form_maker')),
        addslashes(__("From", 'form_maker')),
        addslashes(__("To", 'form_maker')),
        '',
        '',
        '',
        $captcha_rep_url,
        'class="captcha_img" style="display:none"',
        plugins_url("images/refresh.png", __FILE__),
        $id,
        ''
      );
      $untilupload = str_replace($rep1, $rep2, $row->form_front);
      while (strpos($untilupload, "***destinationskizb") > 0) {
        $pos1 = strpos($untilupload, "***destinationskizb");
        $pos2 = strpos($untilupload, "***destinationverj");
        $untilupload = str_replace(substr($untilupload, $pos1, $pos2 - $pos1 + 22), "", $untilupload);
      }
      $form_maker_front_end .= $untilupload;
      $is_recaptcha = FALSE;
      $form_maker_front_end .= '<script type="text/javascript">';
      $form_maker_front_end .= 'WDF_FILE_TYPE_ERROR = \'' . addslashes(__("Sorry, you are not allowed to upload this type of file.", 'form_maker')) . '\';';
      $form_maker_front_end .= 'WDF_GRADING_TEXT = \'' . addslashes(__("Your score should be less than", 'form_maker')) . '\';';
      $form_maker_front_end .= 'WDF_INVALID_GRADING_' . $id . ' 	= \'' . addslashes(sprintf(__("Your score should be less than", 'form_maker'), '`grading_label`', '`grading_total`')) . '\';';
      $form_maker_front_end .= 'WDF_INVALID_EMAIL = \'' . addslashes(__("This is not a valid email address.", 'form_maker')) . '\';';
      $form_maker_front_end .= 'REQUEST_URI_' . $id . '	= "' . $current_url . '";';
      $form_maker_front_end .= 'ReqFieldMsg_' . $id . '	=\'`FIELDNAME` ' . addslashes(__('field is required.', 'form_maker')) . '\';';
      $form_maker_front_end .= 'RangeFieldMsg_' . $id . '	=\'' . addslashes(__('The', 'form_maker')) . ' `FIELDNAME` ' . addslashes(__('value must be between', 'form_maker')) . ' `FROM` - `TO`\';';
      $form_maker_front_end .= 'FormCurrency_' . $id . ' = "' . $form_currency . '";';
      $form_maker_front_end .= 'FormPaypalTax_' . $id . ' = ' . $form_paypal_tax . ';';
      $form_maker_front_end .= 'function formOnload' . $id . '()
  {';
      foreach ($label_type as $key => $type) {
        switch ($type) {
          case 'type_map':
            $form_maker_front_end .= 'if(document.getElementById("' . $label_id[$key] . '_element' . $id . '"))
      {
        if_gmap_init(' . $label_id[$key] . ',' . $id . ');
        for(q=0; q<20; q++)
          if(document.getElementById(' . $label_id[$key] . '+"_element"+' . $id . ').getAttribute("long"+q))
          {
          
            w_long=parseFloat(document.getElementById(' . $label_id[$key] . '+"_element"+' . $id . ').getAttribute("long"+q));
            w_lat=parseFloat(document.getElementById(' . $label_id[$key] . '+"_element"+' . $id . ').getAttribute("lat"+q));
            w_info=parseFloat(document.getElementById(' . $label_id[$key] . '+"_element"+' . $id . ').getAttribute("info"+q));
            add_marker_on_map(' . $label_id[$key] . ',q, w_long, w_lat, w_info,' . $id . ',false);
          }
      }';
            break;
          case 'type_mark_map':
            $form_maker_front_end .= 'if(document.getElementById("' . $label_id[$key] . '_element' . $id . '"))
    if(!document.getElementById("' . $label_id[$key] . '_long' . $id . '"))	
    {      	
    
      var longit = document.createElement(\'input\');
            longit.setAttribute("type", \'hidden\');
            longit.setAttribute("id", \'' . $label_id[$key] . '_long' . $id . '\');
            longit.setAttribute("name", \'' . $label_id[$key] . '_long' . $id . '\');

      var latit = document.createElement(\'input\');
            latit.setAttribute("type", \'hidden\');
            latit.setAttribute("id", \'' . $label_id[$key] . '_lat' . $id . '\');
            latit.setAttribute("name", \'' . $label_id[$key] . '_lat' . $id . '\');

      document.getElementById("' . $label_id[$key] . '_element_section' . $id . '").appendChild(longit);
      document.getElementById("' . $label_id[$key] . '_element_section' . $id . '").appendChild(latit);
    
      if_gmap_init(' . $label_id[$key] . ', ' . $id . ');
      
      w_long=parseFloat(document.getElementById(' . $label_id[$key] . '+"_element"+' . $id . ').getAttribute("long0"));
      w_lat=parseFloat(document.getElementById(' . $label_id[$key] . '+"_element"+' . $id . ').getAttribute("lat0"));
      w_info=parseFloat(document.getElementById(' . $label_id[$key] . '+"_element"+' . $id . ').getAttribute("info0"));
      
      
      longit.value=w_long;
      latit.value=w_lat;
      add_marker_on_map(' . $label_id[$key] . ',0, w_long, w_lat, w_info, ' . $id . ', true);		
    }';
            break;
          case 'type_captcha':
            $form_maker_front_end .= 'if(document.getElementById(\'_wd_captcha' . $id . '\'))
      captcha_refresh(\'_wd_captcha\', \'' . $id . '\');';
            break;
          case 'type_recaptcha':
            $is_recaptcha = TRUE;
            break;
          case 'type_radio':
          case 'type_checkbox':
            $form_maker_front_end .= 'if(document.getElementById(\'' . $label_id[$key] . '_randomize' . $id . '\'))
      if (document.getElementById(\'' . $label_id[$key] . '_randomize' . $id . '\').value == "yes") {
        choises_randomize(\'' . $label_id[$key] . '\', \'' . $id . '\');}';
            break;
          case 'type_spinner':
            $form_maker_front_end .= '
      if (document.getElementById(\'' . $label_id[$key] . '_element' . $id . '\')) {
        var spinner_value = document.getElementById(\'' . $label_id[$key] . '_element' . $id . '\').getAttribute(\'aria-valuenow\');
      }
      if (document.getElementById(\'' . $label_id[$key] . '_min_value' . $id . '\'))
        var spinner_min_value = document.getElementById(\'' . $label_id[$key] . '_min_value' . $id . '\').value;
      if (document.getElementById(\'' . $label_id[$key] . '_max_value' . $id . '\'))
        var spinner_max_value = document.getElementById(\'' . $label_id[$key] . '_max_value' . $id . '\').value;
      if (document.getElementById(\'' . $label_id[$key] . '_step' . $id . '\'))
        var spinner_step = document.getElementById(\'' . $label_id[$key] . '_step' . $id . '\').value;
      jQuery( \'' . $label_id[$key] . '_element' . $id . '\' ).removeClass( \'ui-spinner-input\')
      .attr( \'disabled\', false )
      .removeAttr( \'autocomplete\' )
      .removeAttr( \'role\' )
      .removeAttr( \'aria-valuemin\' )
      .removeAttr( \'aria-valuemax\' )
      .removeAttr( \'aria-valuenow\' );
      if (document.getElementById(\'' . $label_id[$key] . '_element' . $id . '\')) {
        span_ui= document.getElementById(\'' . $label_id[$key] . '_element' . $id . '\').parentNode;
        span_ui.parentNode.appendChild(document.getElementById(\'' . $label_id[$key] . '_element' . $id . '\'));
        span_ui.parentNode.removeChild(span_ui);
        jQuery(\'#' . $label_id[$key] . '_element' . $id . '\')[0].spin = null;
      }
      spinner = jQuery( \'#' . $label_id[$key] . '_element' . $id . '\' ).spinner();
      spinner.spinner( \'value\', spinner_value );
      jQuery( \'#' . $label_id[$key] . '_element' . $id . '\' ).spinner({ min: spinner_min_value});
      jQuery( \'#' . $label_id[$key] . '_element' . $id . '\' ).spinner({ max: spinner_max_value});
      jQuery( \'#' . $label_id[$key] . '_element' . $id . '\' ).spinner({ step: spinner_step});';
            break;
          case 'type_slider':
            $form_maker_front_end .= '
      if (document.getElementById(\'' . $label_id[$key] . '_slider_value' . $id . '\'))
        var slider_value = document.getElementById(\'' . $label_id[$key] . '_slider_value' . $id . '\').value;
      if (document.getElementById(\'' . $label_id[$key] . '_slider_min_value' . $id . '\'))
        var slider_min_value = document.getElementById(\'' . $label_id[$key] . '_slider_min_value' . $id . '\').value;
      if (document.getElementById(\'' . $label_id[$key] . '_slider_max_value' . $id . '\'))
        var slider_max_value = document.getElementById(\'' . $label_id[$key] . '_slider_max_value' . $id . '\').value;
      if (document.getElementById(\'' . $label_id[$key] . '_element_value' . $id . '\'))
        var slider_element_value = document.getElementById(\'' . $label_id[$key] . '_element_value' . $id . '\' );
      if (document.getElementById(\'' . $label_id[$key] . '_slider_value' . $id . '\'))
        var slider_value_save = document.getElementById( \'' . $label_id[$key] . '_slider_value' . $id . '\' );
      if (document.getElementById(\'' . $label_id[$key] . '_element' . $id . '\')) {
        document.getElementById(\'' . $label_id[$key] . '_element' . $id . '\').innerHTML = \'\';
        document.getElementById(\'' . $label_id[$key] . '_element' . $id . '\').removeAttribute( \'class\' );
        document.getElementById(\'' . $label_id[$key] . '_element' . $id . '\').removeAttribute( \'aria-disabled\' );
      }
      if (document.getElementById(\'' . $label_id[$key] . '_element' . $id . '\'))
        jQuery(\'#' . $label_id[$key] . '_element' . $id . '\')[0].slide = null;
      jQuery( \'#' . $label_id[$key] . '_element' . $id . '\').slider({
        range: \'min\',
        value: eval(slider_value),
        min: eval(slider_min_value),
        max: eval(slider_max_value),	
        slide: function( event, ui ) {
          slider_element_value.innerHTML = \'\' + ui.value;
          slider_value_save.value = \'\' + ui.value;
        }
      });';
            break;			
          case 'type_range':
            $form_maker_front_end .= '
      if (document.getElementById(\'' . $label_id[$key] . '_element' . $id . '0\'))
        var spinner_value0 = document.getElementById(\'' . $label_id[$key] . '_element' . $id . '0\').getAttribute( \'aria-valuenow\' );
      if (document.getElementById(\'' . $label_id[$key] . '_element' . $id . '1\'))
        var spinner_value1 = document.getElementById(\'' . $label_id[$key] . '_element' . $id . '1\').getAttribute( \'aria-valuenow\' );
      if (document.getElementById(\'' . $label_id[$key] . '_range_step' . $id . '\'))
        var spinner_step = document.getElementById(\'' . $label_id[$key] . '_range_step' . $id . '\').value;
      jQuery( \'#' . $label_id[$key] . '_element' . $id . '0\' ).removeClass( \'ui-spinner-input\' )
      .attr( \'disabled\', false )	
      .removeAttr( \'autocomplete\' )		
      .removeAttr( \'role\' )			
      .removeAttr( \'aria-valuenow\' );		
      if (document.getElementById(\'' . $label_id[$key] . '_element' . $id . '0\')) {
        span_ui= document.getElementById(\'' . $label_id[$key] . '_element' . $id . '0\').parentNode;
        span_ui.parentNode.appendChild(document.getElementById(\'' . $label_id[$key] . '_element' . $id . '0\'));
        span_ui.parentNode.removeChild(span_ui);
        jQuery(\'#' . $label_id[$key] . '_element' . $id . '0\')[0].spin = null;
      }
      spinner0 = jQuery( \'#' . $label_id[$key] . '_element' . $id . '0\' ).spinner();
      spinner0.spinner( \'value\', spinner_value0 );
      jQuery( \'#' . $label_id[$key] . '_element' . $id . '0\' ).spinner({ step: spinner_step});
      jQuery( \'#' . $label_id[$key] . '_element' . $id . '1\' ).removeClass( \'ui-spinner-input\' )
      .attr( \'disabled\', false )
      .removeAttr( \'autocomplete\' )
      .removeAttr( \'role\' )
      .removeAttr( \'aria-valuenow\' );
      if (document.getElementById(\'' . $label_id[$key] . '_element' . $id . '1\')) {
        span_ui1= document.getElementById(\'' . $label_id[$key] . '_element' . $id . '1\').parentNode;
        span_ui1.parentNode.appendChild(document.getElementById(\'' . $label_id[$key] . '_element' . $id . '1\'));
        span_ui1.parentNode.removeChild(span_ui1);
        jQuery(\'#' . $label_id[$key] . '_element' . $id . '1\')[0].spin = null;
      }
      spinner1 = jQuery( \'#' . $label_id[$key] . '_element' . $id . '1\' ).spinner();
      spinner1.spinner( \'value\', spinner_value1 );
      jQuery( \'#' . $label_id[$key] . '_element' . $id . '1\').spinner({ step: spinner_step});';
            break;
          case 'type_paypal_total':
            $form_maker_front_end .= '
      set_total_value(' . $label_id[$key] . ', ' . $id . ');';
            break;
          default:
            break;
        }
      }
      $form_maker_front_end .= '
       if (window.before_load) {
        before_load();
       }
    }';
      $form_maker_front_end .= '
        jQuery(window).load(function () {
          formOnload' . $id . '();
        });';
      if (isset($_POST["counter" . $id])) {
        $counter = esc_html($_POST["counter" . $id]);
      }
      $old_key = -1;
      if (isset($counter)) {
        foreach ($label_type as $key => $type) {
          switch ($type) {
            case "type_text":
            case "type_number":
            case "type_submitter_mail":
              {
              $form_maker_front_end .= "if(document.getElementById('" . $label_id[$key] . "_element" . $id . "'))
      if(document.getElementById('" . $label_id[$key] . "_element" . $id . "').title!='" . addslashes((isset($_POST[$label_id[$key] . "_element" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_element" . $id])) : "")) . "')
      {	document.getElementById('" . $label_id[$key] . "_element" . $id . "').value='" . addslashes(isset($_POST[$label_id[$key] . "_element" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_element" . $id])) : "") . "';
        document.getElementById('" . $label_id[$key] . "_element" . $id . "').className='input_active';
      }
    ";
              break;
              }
            case "type_textarea":
              {
              $order = array(
                "\r\n",
                "\n",
                "\r"
              );
              $form_maker_front_end .= "if(document.getElementById('" . $label_id[$key] . "_element" . $id . "'))
      if(document.getElementById('" . $label_id[$key] . "_element" . $id . "').title!='" . str_replace($order, '\n', addslashes(isset($_POST[$label_id[$key] . "_element" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_element" . $id])) : "")) . "')
      {	document.getElementById('" . $label_id[$key] . "_element" . $id . "').innerHTML='" . str_replace($order, '\n', addslashes(isset($_POST[$label_id[$key] . "_element" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_element" . $id])) : "")) . "';
        document.getElementById('" . $label_id[$key] . "_element" . $id . "').className='input_active';
      }
    ";
              break;
              }
            case "type_name":
              {
              $element_title = isset($_POST[$label_id[$key] . "_element_title" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_element_title" . $id])) : "";
              if (isset($_POST[$label_id[$key] . "_element_title" . $id])) {
                $form_maker_front_end .= "if(document.getElementById('" . $label_id[$key] . "_element_first" . $id . "'))
    {
      if(document.getElementById('" . $label_id[$key] . "_element_title" . $id . "').title!='" . addslashes(isset($_POST[$label_id[$key] . "_element_title" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_element_title" . $id])) : "") . "')
      {	document.getElementById('" . $label_id[$key] . "_element_title" . $id . "').value='" . addslashes(isset($_POST[$label_id[$key] . "_element_title" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_element_title" . $id])) : "") . "';
        document.getElementById('" . $label_id[$key] . "_element_title" . $id . "').className='input_active';
      }
      
      if(document.getElementById('" . $label_id[$key] . "_element_first" . $id . "').title!='" . addslashes(isset($_POST[$label_id[$key] . "_element_first" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_element_first" . $id])) : "") . "')
      {	document.getElementById('" . $label_id[$key] . "_element_first" . $id . "').value='" . addslashes(isset($_POST[$label_id[$key] . "_element_first" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_element_first" . $id])) : "") . "';
        document.getElementById('" . $label_id[$key] . "_element_first" . $id . "').className='input_active';
      }
      
      if(document.getElementById('" . $label_id[$key] . "_element_last" . $id . "').title!='" . addslashes(isset($_POST[$label_id[$key] . "_element_last" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_element_last" . $id])) : "") . "')
      {	document.getElementById('" . $label_id[$key] . "_element_last" . $id . "').value='" . addslashes(isset($_POST[$label_id[$key] . "_element_last" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_element_last" . $id])) : "") . "';
        document.getElementById('" . $label_id[$key] . "_element_last" . $id . "').className='input_active';
      }
      
      if(document.getElementById('" . $label_id[$key] . "_element_middle" . $id . "').title!='" . addslashes(isset($_POST[$label_id[$key] . "_element_middle" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_element_middle" . $id])) : "") . "')
      {	document.getElementById('" . $label_id[$key] . "_element_middle" . $id . "').value='" . addslashes(isset($_POST[$label_id[$key] . "_element_middle" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_element_middle" . $id])) : "") . "';
        document.getElementById('" . $label_id[$key] . "_element_middle" . $id . "').className='input_active';
      }
      
    }";
              }
              else {
                $form_maker_front_end .= "if(document.getElementById('" . $label_id[$key] . "_element_first" . $id . "'))
    {
      
      if(document.getElementById('" . $label_id[$key] . "_element_first" . $id . "').title!='" . addslashes(isset($_POST[$label_id[$key] . "_element_first" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_element_first" . $id])) : "") . "')
      {	document.getElementById('" . $label_id[$key] . "_element_first" . $id . "').value='" . addslashes(isset($_POST[$label_id[$key] . "_element_first" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_element_first" . $id])) : "") . "';
        document.getElementById('" . $label_id[$key] . "_element_first" . $id . "').className='input_active';
      }
      
      if(document.getElementById('" . $label_id[$key] . "_element_last" . $id . "').title!='" . addslashes(isset($_POST[$label_id[$key] . "_element_last" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_element_last" . $id])) : "") . "')
      {	document.getElementById('" . $label_id[$key] . "_element_last" . $id . "').value='" . addslashes(isset($_POST[$label_id[$key] . "_element_last" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_element_last" . $id])) : "") . "';
        document.getElementById('" . $label_id[$key] . "_element_last" . $id . "').className='input_active';
      }
      
    }";
              }
              break;
              }
            case "type_phone":
              {
              $form_maker_front_end .= "if(document.getElementById('" . $label_id[$key] . "_element_first" . $id . "'))
    {
      if(document.getElementById('" . $label_id[$key] . "_element_first" . $id . "').title!='" . addslashes(isset($_POST[$label_id[$key] . "_element_first" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_element_first" . $id])) : "") . "')
      {	document.getElementById('" . $label_id[$key] . "_element_first" . $id . "').value='" . addslashes(isset($_POST[$label_id[$key] . "_element_first" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_element_first" . $id])) : "") . "';
        document.getElementById('" . $label_id[$key] . "_element_first" . $id . "').className='input_active';
      }
      
      if(document.getElementById('" . $label_id[$key] . "_element_last" . $id . "').title!='" . addslashes(isset($_POST[$label_id[$key] . "_element_last" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_element_last" . $id])) : "") . "')
      {	document.getElementById('" . $label_id[$key] . "_element_last" . $id . "').value='" . addslashes(isset($_POST[$label_id[$key] . "_element_last" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_element_last" . $id])) : "") . "';
        document.getElementById('" . $label_id[$key] . "_element_last" . $id . "').className='input_active';
      }
    }";
              break;
              }
              
            case "type_paypal_price": {
              $form_maker_front_end .= "if(document.getElementById('".$label_id[$key]."_element_dollars".$id."'))
    {
      if(document.getElementById('".$label_id[$key]."_element_dollars".$id."').title!='".addslashes(isset($_POST[$label_id[$key]."_element_dollars".$id]) ? esc_html(stripslashes($_POST[$label_id[$key]."_element_dollars".$id])) : "")."')
      {	document.getElementById('".$label_id[$key]."_element_dollars".$id."').value='".addslashes(isset($_POST[$label_id[$key]."_element_dollars".$id]) ? esc_html(stripslashes($_POST[$label_id[$key]."_element_dollars".$id])) : "")."';
        document.getElementById('".$label_id[$key]."_element_dollars".$id."').className='input_active';
      }
      
      if(document.getElementById('".$label_id[$key]."_element_cents".$id."').title!='".addslashes(isset($_POST[$label_id[$key]."_element_cents".$id]) ? esc_html(stripslashes($_POST[$label_id[$key]."_element_cents".$id])) : "")."')
      {	document.getElementById('".$label_id[$key]."_element_cents".$id."').value='".addslashes(isset($_POST[$label_id[$key]."_element_cents".$id]) ? esc_html(stripslashes($_POST[$label_id[$key]."_element_cents".$id])) : "")."';
        document.getElementById('".$label_id[$key]."_element_cents".$id."').className='input_active';
      }
    }";
                  
                  break;
                  }
                  
                  case "type_paypal_select":{
    
                  $form_maker_front_end .= 
    "if(document.getElementById('".$label_id[$key]."_element".$id."')){
      document.getElementById('".$label_id[$key]."_element".$id."').value='".addslashes(isset($_POST[$label_id[$key]."_element".$id]) ? esc_html(stripslashes($_POST[$label_id[$key]."_element".$id])) : "")."';
    
    if(document.getElementById('".$label_id[$key]."_element_quantity".$id."'))
      document.getElementById('".$label_id[$key]."_element_quantity".$id."').value='".((isset($_POST[$label_id[$key]."_element_quantity".$id]) && ((int) $_POST[$label_id[$key]."_element_quantity".$id] >= 1)) ? addslashes($_POST[$label_id[$key]."_element_quantity".$id]) : 1)."';
      ";
      for($j=0; $j<100; $j++)
                  {
                      $element = isset($_POST[$label_id[$key]."_property".$id.$j]) ? esc_html(stripslashes($_POST[$label_id[$key]."_property".$id.$j])) : NULL;
                      if(isset($element))
                          {
                          $form_maker_front_end .= 
    "document.getElementById('".$label_id[$key]."_property".$id.$j."').value='".addslashes($element)."';
    ";
                          }
                  }
      $form_maker_front_end .= "
      }";
    
                  
                  break;
                  }
            case "type_paypal_checkbox":{
                
                $form_maker_front_end .= 
    "
    for(k=0; k<30; k++)
      if(document.getElementById('".$label_id[$key]."_element".$id."'+k))
        document.getElementById('".$label_id[$key]."_element".$id."'+k).removeAttribute('checked');
      else break;
    ";
                  for($j=0; $j<100; $j++)
                  {
                      $element = isset($_POST[$label_id[$key]."_element".$id.$j]) ? esc_html(stripslashes($_POST[$label_id[$key]."_element".$id.$j])) : NULL;
                      if(isset($element))
                          {
                          $form_maker_front_end .= 
    "document.getElementById('".$label_id[$key]."_element".$id.$j."').setAttribute('checked', 'checked');
    ";
                          }
                  }
      
                  $form_maker_front_end .= 
    "
    if(document.getElementById('".$label_id[$key]."_element_quantity".$id."'))
      document.getElementById('".$label_id[$key]."_element_quantity".$id."').value='".((isset($_POST[$label_id[$key]."_element_quantity".$id])) ? addslashes($_POST[$label_id[$key]."_element_quantity".$id]) : 1)."';
      ";
      for($j=0; $j<100; $j++)
                  {
                      $element = isset($_POST[$label_id[$key]."_property".$id.$j]) ? esc_html(stripslashes($_POST[$label_id[$key]."_property".$id.$j])) : NULL;
                      if(isset($element))
                          {
                          $form_maker_front_end .= 
    "document.getElementById('".$label_id[$key]."_property".$id.$j."').value='".addslashes($element)."';
    ";
                          }
                  };	
                  break;
                  }		
    case "type_paypal_radio":{
                
                $form_maker_front_end .= 
    "
    for(k=0; k<50; k++)
      if(document.getElementById('".$label_id[$key]."_element".$id."'+k))
      {
        document.getElementById('".$label_id[$key]."_element".$id."'+k).removeAttribute('checked');
        if(document.getElementById('".$label_id[$key]."_element".$id."'+k).value=='".addslashes(isset($_POST[$label_id[$key]."_element".$id]) ? esc_html(stripslashes($_POST[$label_id[$key]."_element".$id])) : "")."')
        {
          document.getElementById('".$label_id[$key]."_element".$id."'+k).setAttribute('checked', 'checked');
                  
        }
      }
      

    if(document.getElementById('".$label_id[$key]."_element_quantity".$id."'))
      document.getElementById('".$label_id[$key]."_element_quantity".$id."').value='".((isset($_POST[$label_id[$key]."_element_quantity".$id])) ? esc_html(stripslashes($_POST[$label_id[$key]."_element_quantity".$id])) : 1)."';
      ";
      for($j=0; $j<100; $j++)
                  {
                      $element = isset($_POST[$label_id[$key]."_property".$id.$j]) ? esc_html(stripslashes($_POST[$label_id[$key]."_property".$id.$j])) : NULL;
                      if(isset($element))
                          {
                          $form_maker_front_end .= 
    "document.getElementById('".$label_id[$key]."_property".$id.$j."').value='".addslashes($element)."';
    ";
                          }
                  };
      
    
                  
                  break;
                  }
                  
          case "type_paypal_shipping":{
        
                  $form_maker_front_end .= 
    "
    for(k=0; k<50; k++)
      if(document.getElementById('".$label_id[$key]."_element".$id."'+k))
      {
        document.getElementById('".$label_id[$key]."_element".$id."'+k).removeAttribute('checked');
        if(document.getElementById('".$label_id[$key]."_element".$id."'+k).value=='".addslashes(isset($_POST[$label_id[$key]."_element".$id]) ? esc_html(stripslashes($_POST[$label_id[$key]."_element".$id])) : "")."')
        {
          document.getElementById('".$label_id[$key]."_element".$id."'+k).setAttribute('checked', 'checked');
                  
        }
      }
    
    ";
    
              break;
                }
            case "type_star_rating": {
              $form_maker_front_end .=
            "if(document.getElementById('".$label_id[$key]."_element".$id."')) {
              document.getElementById('".$label_id[$key]."_selected_star_amount".$id."').value='".addslashes(isset($_POST[$label_id[$key]."_selected_star_amount".$id]) ? esc_html(stripslashes($_POST[$label_id[$key]."_selected_star_amount".$id])) : "")."';	
              if (document.getElementById('".$label_id[$key]."_selected_star_amount".$id."').value)	
                select_star_rating((document.getElementById('".$label_id[$key]."_selected_star_amount".$id."').value-1),".$label_id[$key].",".$id.");	
            }";
              break;
            
            }

          case "type_scale_rating": {
            $form_maker_front_end .=
            "for (k=0; k<100; k++) {
              if (document.getElementById('".$label_id[$key]."_scale_radio".$id."_'+k)) {
                document.getElementById('".$label_id[$key]."_scale_radio".$id."_'+k).removeAttribute('checked');
                if (document.getElementById('".$label_id[$key]."_scale_radio".$id."_'+k).value=='".(isset($_POST[$label_id[$key]."_scale_radio".$id]) ? esc_html(stripslashes($_POST[$label_id[$key]."_scale_radio".$id])) : "")."')
                  document.getElementById('".$label_id[$key]."_scale_radio".$id."_'+k).setAttribute('checked', 'checked');
              }
            }";
            break;

          }
          case "type_spinner": {
            $form_maker_front_end .=
            "if (document.getElementById('".$label_id[$key]."_element".$id."')) {
              document.getElementById('".$label_id[$key]."_element".$id."').setAttribute('aria-valuenow','".(isset($_POST[$label_id[$key]."_element".$id]) ? esc_html(stripslashes($_POST[$label_id[$key]."_element".$id])) : "")."');
            }";
            break;

          }
          case "type_slider": {
            $form_maker_front_end .=
            "if (document.getElementById('".$label_id[$key]."_element".$id."'))
              document.getElementById('".$label_id[$key]."_element".$id."').setAttribute('aria-valuenow','".(isset($_POST[$label_id[$key]."_slider_value".$id]) ? esc_html(stripslashes($_POST[$label_id[$key]."_slider_value".$id])) : "")."');
            if (document.getElementById('".$label_id[$key]."_slider_value".$id."'))
              document.getElementById('".$label_id[$key]."_slider_value".$id."').value='".(isset($_POST[$label_id[$key]."_slider_value".$id]) ? esc_html(stripslashes($_POST[$label_id[$key]."_slider_value".$id])) : "")."';
            if (document.getElementById('".$label_id[$key]."_element_value".$id."'))
              document.getElementById('".$label_id[$key]."_element_value".$id."').innerHTML='".(isset($_POST[$label_id[$key]."_slider_value".$id]) ? esc_html(stripslashes($_POST[$label_id[$key]."_slider_value".$id])) : "")."';";
            break;

          }
          case "type_range": {
            $form_maker_front_end .=
              "if (document.getElementById('".$label_id[$key]."_element".$id."0'))
                document.getElementById('".$label_id[$key]."_element".$id."0').setAttribute('aria-valuenow','".(isset($_POST[$label_id[$key]."_element".$id."0"]) ? esc_html(stripslashes($_POST[$label_id[$key]."_element".$id."0"])) : "")."');
              if (document.getElementById('".$label_id[$key]."_element".$id."1'))
                document.getElementById('".$label_id[$key]."_element".$id."1').setAttribute('aria-valuenow','".(isset($_POST[$label_id[$key]."_element".$id."1"]) ? esc_html(stripslashes($_POST[$label_id[$key]."_element".$id."1"])) : "")."');";
            break;

          }
          case "type_grading": {
            for ($k = 0; $k < 100; $k++) {
              $form_maker_front_end .= "if (document.getElementById('".$label_id[$key]."_element".$id.$k."')) {		
                document.getElementById('".$label_id[$key]."_element".$id.$k."').value='".(isset($_POST[$label_id[$key]."_element".$id.$k]) ? esc_html(stripslashes($_POST[$label_id[$key]."_element".$id.$k])) : "")."';}";
            }
            $form_maker_front_end .= "sum_grading_values(".$label_id[$key].",".$id.");";
            break;

          }
          case "type_matrix": {
            $form_maker_front_end .= 
            "if (document.getElementById('".$label_id[$key]."_input_type".$id."').value == 'radio') {";	
              for ($k = 1; $k < 40; $k++) {
                for ($l = 1; $l < 40; $l++) {
                  $form_maker_front_end .= 
                    "if (document.getElementById('".$label_id[$key]."_input_element".$id.$k."_".$l."')) {
                      document.getElementById('".$label_id[$key]."_input_element".$id.$k."_".$l."').removeAttribute('checked');
                      if (document.getElementById('".$label_id[$key]."_input_element".$id.$k."_".$l."').value=='".(isset($_POST[$label_id[$key]."_input_element".$id.$k]) ? esc_html(stripslashes($_POST[$label_id[$key]."_input_element".$id.$k])) : "")."')
                        document.getElementById('".$label_id[$key]."_input_element".$id.$k."_".$l."').setAttribute('checked', 'checked');
                    }";
                }
              }
              $form_maker_front_end .= 
            "}	
            else	
              if (document.getElementById('".$label_id[$key]."_input_type".$id."').value == 'checkbox') {";
              for ($k = 1; $k < 40; $k++) {
                for ($l = 1; $l < 40; $l++) {
                  $form_maker_front_end .= 
                  "if (document.getElementById('".$label_id[$key]."_input_element".$id.$k."_".$l."')) {
                    document.getElementById('".$label_id[$key]."_input_element".$id.$k."_".$l."').removeAttribute('checked');
                    if (document.getElementById('".$label_id[$key]."_input_element".$id.$k."_".$l."').value=='".(isset($_POST[$label_id[$key]."_input_element".$id.$k."_".$l]) ? esc_html(stripslashes($_POST[$label_id[$key]."_input_element".$id.$k."_".$l])) : "")."')		
                      document.getElementById('".$label_id[$key]."_input_element".$id.$k."_".$l."').setAttribute('checked', 'checked');
                  }";	
                }
              }
              $form_maker_front_end .= 
            "}	
            else	
              if (document.getElementById('".$label_id[$key]."_input_type".$id."').value == 'text') {";
              for ($k = 1; $k < 40; $k++) {
                for ($l = 1; $l < 40; $l++) {
                  $form_maker_front_end .= 
                  "if (document.getElementById('".$label_id[$key]."_input_element".$id.$k."_".$l."'))
                    document.getElementById('".$label_id[$key]."_input_element".$id.$k."_".$l."').value='".(isset($_POST[$label_id[$key]."_input_element".$id.$k."_".$l]) ? esc_html(stripslashes($_POST[$label_id[$key]."_input_element".$id.$k."_".$l])) : "")."';";
                }
              }
              $form_maker_front_end .= "
            }
            else
              if (document.getElementById('".$label_id[$key]."_input_type".$id."').value == 'select') {";
                for ($k = 1; $k < 40; $k++) {
                  for ($l = 1; $l < 40; $l++) {
                    $form_maker_front_end .= 
                    "if (document.getElementById('".$label_id[$key]."_select_yes_no".$id.$k."_".$l."'))
                      document.getElementById('".$label_id[$key]."_select_yes_no".$id.$k."_".$l."').value='".(isset($_POST[$label_id[$key]."_select_yes_no".$id.$k."_".$l]) ? esc_html(stripslashes($_POST[$label_id[$key]."_select_yes_no".$id.$k."_".$l])) : "")."';";
                  }
                }
              $form_maker_front_end .= 
              "}";	
              break;

            }
            case "type_address":
              {
              if ($key > $old_key) {
                $form_maker_front_end .= "if(document.getElementById('" . $label_id[$key] . "_street1" . $id . "'))
    {
        document.getElementById('" . $label_id[$key] . "_street1" . $id . "').value='" . addslashes(isset($_POST[$label_id[$key] . "_street1" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_street1" . $id])) : "") . "';
        document.getElementById('" . $label_id[$key] . "_street2" . $id . "').value='" . addslashes(isset($_POST[$label_id[$key + 1] . "_street2" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key + 1] . "_street2" . $id])) : "") . "';
        document.getElementById('" . $label_id[$key] . "_city" . $id . "').value='" . addslashes(isset($_POST[$label_id[$key + 2] . "_city" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key + 2] . "_city" . $id])) : "") . "';
        document.getElementById('" . $label_id[$key] . "_state" . $id . "').value='" . addslashes(isset($_POST[$label_id[$key + 3] . "_state" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key + 3] . "_state" . $id])) : "") . "';
        document.getElementById('" . $label_id[$key] . "_postal" . $id . "').value='" . addslashes(isset($_POST[$label_id[$key + 4] . "_postal" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key + 4] . "_postal" . $id])) : "") . "';
        document.getElementById('" . $label_id[$key] . "_country" . $id . "').value='" . addslashes(isset($_POST[$label_id[$key + 5] . "_country" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key + 5] . "_country" . $id])) : "") . "';
      
    }";
                $old_key = $key + 5;
              }
              break;
              }
            case "type_checkbox":
              {
              $is_other = FALSE;
              if (isset($_POST[$label_id[$key] . "_allow_other" . $id]) && esc_html(stripslashes($_POST[$label_id[$key] . "_allow_other" . $id])) == "yes") {
                $other_element = isset($_POST[$label_id[$key] . "_other_input" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_other_input" . $id])) : "";
                $other_element_id = isset($_POST[$label_id[$key] . "_allow_other_num" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_allow_other_num" . $id])) : NULL;
                if (isset($_POST[$label_id[$key] . "_allow_other_num" . $id]))
                  $is_other = TRUE;
              }
              $form_maker_front_end .= "
    if(document.getElementById('" . $label_id[$key] . "_other_input" . $id . "'))
    {
    document.getElementById('" . $label_id[$key] . "_other_input" . $id . "').parentNode.removeChild(document.getElementById('" . $label_id[$key] . "_other_br" . $id . "'));
    document.getElementById('" . $label_id[$key] . "_other_input" . $id . "').parentNode.removeChild(document.getElementById('" . $label_id[$key] . "_other_input" . $id . "'));
    }
    for(k=0; k<30; k++)
      if(document.getElementById('" . $label_id[$key] . "_element" . $id . "'+k))
        document.getElementById('" . $label_id[$key] . "_element" . $id . "'+k).removeAttribute('checked');
      else break;
    ";
              for ($j = 0; $j < 100; $j++) {
                $element = isset($_POST[$label_id[$key] . "_element" . $id . $j]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_element" . $id . $j])) : NULL;
                if (isset($_POST[$label_id[$key] . "_element" . $id . $j])) {
                  $form_maker_front_end .= "document.getElementById('" . $label_id[$key] . "_element" . $id . $j . "').setAttribute('checked', 'checked');
    ";
                }
              }
              if ($is_other)
                $form_maker_front_end .= "
      show_other_input('" . $label_id[$key] . "','" . $id . "');
      document.getElementById('" . $label_id[$key] . "_other_input" . $id . "').value='" . (isset($_POST[$label_id[$key] . "_other_input" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_other_input" . $id])) : "") . "';
    ";
              break;
              }
            case "type_radio":
              {
              $is_other = FALSE;
              if (isset($_POST[$label_id[$key] . "_allow_other" . $id]) && esc_html(stripslashes($_POST[$label_id[$key] . "_allow_other" . $id])) == "yes") {
                $other_element = isset($_POST[$label_id[$key] . "_other_input" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_other_input" . $id])) : "";
                if (isset($_POST[$label_id[$key] . "_other_input" . $id]))
                  $is_other = TRUE;
              }
              $form_maker_front_end .= "
    if(document.getElementById('" . $label_id[$key] . "_other_input" . $id . "'))
    {
    document.getElementById('" . $label_id[$key] . "_other_input" . $id . "').parentNode.removeChild(document.getElementById('" . $label_id[$key] . "_other_br" . $id . "'));
    document.getElementById('" . $label_id[$key] . "_other_input" . $id . "').parentNode.removeChild(document.getElementById('" . $label_id[$key] . "_other_input" . $id . "'));
    }
    
    for(k=0; k<50; k++)
      if(document.getElementById('" . $label_id[$key] . "_element" . $id . "'+k))
      {
        document.getElementById('" . $label_id[$key] . "_element" . $id . "'+k).removeAttribute('checked');
        if(document.getElementById('" . $label_id[$key] . "_element" . $id . "'+k).value=='" . addslashes(isset($_POST[$label_id[$key] . "_element" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_element" . $id])) : "") . "')
        {
          document.getElementById('" . $label_id[$key] . "_element" . $id . "'+k).setAttribute('checked', 'checked');
                  
        }
      }
      else break;
    ";
              if ($is_other)
                $form_maker_front_end .= "
      show_other_input('" . $label_id[$key] . "','" . $id . "');
      document.getElementById('" . $label_id[$key] . "_other_input" . $id . "').value='" . (isset($_POST[$label_id[$key] . "_other_input" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_other_input" . $id])) : "") . "';
    ";
              break;
              }
            case "type_time":
              {
              $ss = isset($_POST[$label_id[$key] . "_ss" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_ss" . $id])) : "";
              if (isset($_POST[$label_id[$key] . "_ss" . $id])) {
                $form_maker_front_end .= "if(document.getElementById('" . $label_id[$key] . "_hh" . $id . "'))
    {
      document.getElementById('" . $label_id[$key] . "_hh" . $id . "').value='" . (isset($_POST[$label_id[$key] . "_hh" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_hh" . $id])) : "") . "';
      document.getElementById('" . $label_id[$key] . "_mm" . $id . "').value='" . (isset($_POST[$label_id[$key] . "_mm" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_mm" . $id])) : "") . "';
      document.getElementById('" . $label_id[$key] . "_ss" . $id . "').value='" . (isset($_POST[$label_id[$key] . "_ss" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_ss" . $id])) : "") . "';
    }";
              }
              else {
                $form_maker_front_end .= "if(document.getElementById('" . $label_id[$key] . "_hh" . $id . "'))
    {
      document.getElementById('" . $label_id[$key] . "_hh" . $id . "').value='" . (isset($_POST[$label_id[$key] . "_hh" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_hh" . $id])) : "") . "';
      document.getElementById('" . $label_id[$key] . "_mm" . $id . "').value='" . (isset($_POST[$label_id[$key] . "_mm" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_mm" . $id])) : "") . "';
    }";
              }
              $am_pm = isset($_POST[$label_id[$key] . "_am_pm" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_am_pm" . $id])) : NULL;
              if (isset($am_pm))
                $form_maker_front_end .= "if(document.getElementById('" . $label_id[$key] . "_am_pm" . $id . "'))
      document.getElementById('" . $label_id[$key] . "_am_pm" . $id . "').value='" . $am_pm . "';
    ";
              break;
              }
            case "type_date_fields":
              {
              $form_maker_front_end .= "if(document.getElementById('" . $label_id[$key] . "_day" . $id . "'))
    {
      document.getElementById('" . $label_id[$key] . "_day" . $id . "').value='" . (isset($_POST[$label_id[$key] . "_day" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_day" . $id])) : "") . "';
      document.getElementById('" . $label_id[$key] . "_month" . $id . "').value='" . (isset($_POST[$label_id[$key] . "_month" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_month" . $id])) : "") . "';
      document.getElementById('" . $label_id[$key] . "_year" . $id . "').value='" . (isset($_POST[$label_id[$key] . "_year" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_year" . $id])) : "") . "';
    }";
              break;
              }
            case "type_date":
            case "type_own_select":
            case "type_country":
              {
              $form_maker_front_end .= "if(document.getElementById('" . $label_id[$key] . "_element" . $id . "'))
      document.getElementById('" . $label_id[$key] . "_element" . $id . "').value='" . addslashes(isset($_POST[$label_id[$key] . "_element" . $id]) ? esc_html(stripslashes($_POST[$label_id[$key] . "_element" . $id])) : "") . "';
    ";
              break;
              }
            default:
              {
              break;
              }
          }
        }
      }
      $form_maker_front_end .= '	form_view_count' . $id . '=0;
    for(i=1; i<=30; i++)
    {
      if(document.getElementById(\'' . $id . 'form_view\'+i))
      {
        form_view_count' . $id . '++;
        form_view_max' . $id . '=i;
        document.getElementById(\'' . $id . 'form_view\'+i).parentNode.removeAttribute(\'style\');
      }
    }	
    if(form_view_count' . $id . '>1)
    {
      for(i=1; i<=form_view_max' . $id . '; i++)
      {
        if(document.getElementById(\'' . $id . 'form_view\'+i))
        {
          first_form_view' . $id . '=i;
          break;
        }
      }		
      generate_page_nav(first_form_view' . $id . ', \'' . $id . '\', form_view_count' . $id . ', form_view_max' . $id . ');
    }
    var RecaptchaOptions = {
  theme: "' . $row->recaptcha_theme . '"
  };
  </script>
  </form>';
      if ($is_recaptcha) {
        $form_maker_front_end .= '<div id="main_recaptcha" style="display:none;">';
        if ($row->public_key)
          $publickey = $row->public_key;
        else
          $publickey = '0';
        $error = NULL;
        require_once(WD_FMC_DIR . '/recaptchalib.php');
        $form_maker_front_end .= recaptcha_get_html($publickey, $error);
        $form_maker_front_end .= '</div>
      <script>
    recaptcha_html = document.getElementById(\'main_recaptcha\').innerHTML.replace(\'Recaptcha.widget = Recaptcha.$("recaptcha_widget_div"); Recaptcha.challenge_callback();\',"");
    document.getElementById(\'main_recaptcha\').innerHTML="";
    if (document.getElementById(\'wd_recaptcha' . $id . '\')) {
      document.getElementById(\'wd_recaptcha' . $id . '\').innerHTML=recaptcha_html;
      Recaptcha.widget = Recaptcha.$("recaptcha_widget_div");
      Recaptcha.challenge_callback();
    }
      </script>';
      }
    }
    return $form_maker_front_end;
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