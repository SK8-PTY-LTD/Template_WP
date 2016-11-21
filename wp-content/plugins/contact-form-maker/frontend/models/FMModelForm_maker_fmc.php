<?php

class FMModelForm_maker_fmc {
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
  public function showform($id) {
    global $wpdb;
    $row = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'formmaker WHERE id="%d"', $id));
    if (!$row || !$row->published) {
      return FALSE;
    }
    if (isset($_GET['test_theme']) && (esc_html(stripslashes($_GET['test_theme'])) != '')) {
      /* From preview.*/
      $theme_id = esc_html(stripslashes($_GET['test_theme']));
    }
    else {
      $theme_id = $row->theme;
    }
    $form_theme = $wpdb->get_var($wpdb->prepare('SELECT css FROM ' . $wpdb->prefix . 'formmaker_themes WHERE id="%d"', $theme_id));
    if (!$form_theme) {
      $form_theme = $wpdb->get_var('SELECT css FROM ' . $wpdb->prefix . 'formmaker_themes');
      if (!$form_theme) {
        return FALSE;
      }
    }
    $label_id = array();
    $label_type = array();
    $label_all = explode('#****#', $row->label_order);
    $label_all = array_slice($label_all, 0, count($label_all) - 1);
    foreach ($label_all as $key => $label_each) {
      $label_id_each = explode('#**id**#', $label_each);
      array_push($label_id, $label_id_each[0]);
      $label_order_each = explode('#**label**#', $label_id_each[1]);
      array_push($label_type, $label_order_each[1]);
    }
    return array(
      $row,
      1,
      $label_id,
      $label_type,
      $form_theme
    );
  }

  public function savedata($form, $id) {
	$fmc_settings = get_option('fmc_settings');
    $all_files = array();
    $correct = FALSE;
    $id_for_old = $id;
    if (!$form->form_front) {
      $id = '';
    }
    if (isset($_POST["counter" . $id])) {
      $counter = esc_html($_POST["counter" . $id]);
      if (isset($_POST["captcha_input"])) {
        $captcha_input = esc_html($_POST["captcha_input"]);
        $session_wd_captcha_code = isset($_SESSION[$id . '_wd_captcha_code']) ? $_SESSION[$id . '_wd_captcha_code'] : '-';
        if (md5($captcha_input) == $session_wd_captcha_code) {
          $correct = TRUE;
        }
        else {
          ?>
          <script>alert("<?php echo addslashes(__('Error, incorrect Security code.', 'form_maker')); ?>");</script>
          <?php
        }
      }
	   elseif (isset($_POST["arithmetic_captcha_input"])) {
        $arithmetic_captcha_input = esc_html($_POST["arithmetic_captcha_input"]);
        $session_wd_arithmetic_captcha_code = isset($_SESSION[$id . '_wd_arithmetic_captcha_code']) ? $_SESSION[$id . '_wd_arithmetic_captcha_code'] : '-';
        if (md5($arithmetic_captcha_input) == $session_wd_arithmetic_captcha_code) {
          $correct = TRUE;
        }
        else {
          ?>
          <script>alert("<?php echo addslashes(__('Error, incorrect Security code.', 'form_maker')); ?>");</script>
          <?php
        }
      }
	  elseif (isset($_POST["g-recaptcha-response"])){
		$privatekey= isset($fmc_settings['private_key']) ? $fmc_settings['private_key'] : '';	
		$captcha = $_POST['g-recaptcha-response'];
		$url = 'https://www.google.com/recaptcha/api/siteverify';
		$data = array(
			'secret' => $privatekey,
			'response' => $captcha,
			'remoteip' => $_SERVER['REMOTE_ADDR']
		);
    
		$curlConfig = array(
			CURLOPT_URL => $url,
			CURLOPT_POST => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POSTFIELDS => $data
		);

		$ch = curl_init();
		curl_setopt_array($ch, $curlConfig);
		$response = curl_exec($ch);
		curl_close($ch);
    
		$jsonResponse = json_decode($response);

		if ($jsonResponse->success == "true")
			$correct = TRUE;
		else {
			?>
			<script>alert("<?php echo addslashes(__('Error, incorrect Security code.', 'form_maker')); ?>");</script>
			<?php
		}
	  }
      else {
        $correct = TRUE;
      }
      if ($correct) {
        
        $ip=$_SERVER['REMOTE_ADDR'];
        
        global $wpdb;
        $blocked_ip = $wpdb->get_var($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'formmaker_blocked WHERE ip="%s"', $ip));
        if ($blocked_ip) {
          $_SESSION['massage_after_submit' . $id] = addslashes(__('Your ip is blacklisted. Please contact the website administrator.', 'form_maker'));
          wp_redirect($_SERVER["REQUEST_URI"]);//to be checked
          exit;
        }
        
        $result_temp = $this->save_db($counter, $id_for_old);
        $all_files = $result_temp[0];
        if (is_numeric($all_files)) {
          $this->remove($all_files, $id_for_old);
        }
        elseif (isset($counter)) {
          $this->gen_mail($counter, $all_files, $id_for_old, $result_temp[1]);
        }
         
      }    
      return $all_files;
    }
 
    return $all_files;
  }
  
   public function select_data_from_db_for_labels($db_info,$label_column, $table, $where, $order_by) {
        global $wpdb;
        
		$query = "SELECT `".$label_column."` FROM ".$table.$where." ORDER BY ".$order_by;
		if($db_info)
			{ 
			
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
				$choices_labels = $wpdb_temp->get_results($query,ARRAY_N);				
			}
		else{
            $choices_labels = $wpdb->get_results($query,ARRAY_N);
        }
        return $choices_labels;
  } 
  
  public function select_data_from_db_for_values($db_info,$value_column, $table, $where, $order_by) {
        global $wpdb;
		  
		$query = "SELECT `".$value_column."` FROM ".$table.$where." ORDER BY ".$order_by;
		if($db_info)
			{ 
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
				$choices_values = $wpdb_temp->get_results($query,ARRAY_N);				
			}
		else{
            $choices_values = $wpdb->get_results($query,ARRAY_N);
        }
        return $choices_values; 
  }
  
  public function save_db($counter, $id) {
    global $wpdb;
	$current_user =  wp_get_current_user();
	if ($current_user->ID != 0)
	{
		$wp_userid =  $current_user->ID;
		$wp_username =  $current_user->display_name;
		$wp_useremail =  $current_user->user_email;
	}
	else
	{
		$wp_userid = '';
		$wp_username = '';
		$wp_useremail = '';
	}
	$ip = $_SERVER['REMOTE_ADDR']; 
	 
    $chgnac = TRUE;
    $all_files = array();
    $paypal = array();
    $paypal['item_name'] = array();
    $paypal['quantity'] = array();
    $paypal['amount'] = array();
    $is_amount=false;
    $paypal['on_os'] = array();
    $total = 0;
    $form_currency = '$';
    $currency_code = array('USD', 'EUR', 'GBP', 'JPY', 'CAD', 'MXN', 'HKD', 'HUF', 'NOK', 'NZD', 'SGD', 'SEK', 'PLN', 'AUD', 'DKK', 'CHF', 'CZK', 'ILS', 'BRL', 'TWD', 'MYR', 'PHP', 'THB');
    $currency_sign = array('$', '&#8364;', '&#163;', '&#165;', 'C$', 'Mex$', 'HK$', 'Ft', 'kr', 'NZ$', 'S$', 'kr', 'zl', 'A$', 'kr', 'CHF', 'Kc', '&#8362;', 'R$', 'NT$', 'RM', '&#8369;', '&#xe3f;');
    $form = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "formmaker WHERE id= %d", $id));
    $id_old = $id;
    if (!$form->form_front) {
      $id = '';
    }
    if ($form->payment_currency) {
      $form_currency = $currency_sign[array_search($form->payment_currency, $currency_code)];
    }
    $old = false;		
		if(isset($form->form)) {
			$old = true;
    }
    $label_id = array();
    $label_label = array();
    $label_type = array();
    
    $disabled_fields = explode(',', (isset($_REQUEST["disabled_fields".$id]) ? $_REQUEST["disabled_fields".$id] : ""));
    $disabled_fields = array_slice($disabled_fields,0, count($disabled_fields)-1);
    
    if($old == false || ($old == true && $form->form=='')) {
      $label_all	= explode('#****#',$form->label_order_current);		
    }
    else {
      $label_all	= explode('#****#',$form->label_order);
    }
    $label_all = array_slice($label_all, 0, count($label_all) - 1);
    foreach ($label_all as $key => $label_each) {
      $label_id_each = explode('#**id**#', $label_each);
      array_push($label_id, $label_id_each[0]);
      $label_order_each = explode('#**label**#', $label_id_each[1]);
      array_push($label_label, $label_order_each[0]);
      array_push($label_type, $label_order_each[1]);
    }
    $max = $wpdb->get_var("SELECT MAX( group_id ) FROM " . $wpdb->prefix . "formmaker_submits");
    $fvals=array();
    if ($old == FALSE || ($old == TRUE && $form->form == '')) {
		foreach ($label_type as $key => $type) {
			$value = '';
			if ($type == "type_submit_reset" or $type == "type_map" or $type == "type_editor" or  $type == "type_captcha" or $type == "type_arithmetic_captcha" or  $type == "type_recaptcha" or  $type == "type_button" or $type == "type_paypal_total" or $type == "type_send_copy") 
				continue;
        
			$i = $label_id[$key];
			if(!in_array($i,$disabled_fields)) {
				switch ($type) {
				case 'type_text':
				case 'type_password':
				case 'type_textarea':
				case "type_submitter_mail":
				case "type_date":
				case "type_own_select":					
				case "type_country":				
				case "type_number": {
				  $value = isset($_POST['wdform_'.$i."_element".$id]) ? esc_html($_POST['wdform_'.$i."_element".$id]) : "";
				  break;
				}
				case "type_wdeditor": {
				  $value = isset($_POST['wdform_'.$i.'_wd_editor'.$id]) ? esc_html($_POST['wdform_'.$i.'_wd_editor'.$id]) : "";
				  break;
				}
				case "type_mark_map": {
				  $value = (isset($_POST['wdform_'.$i."_long".$id]) ? $_POST['wdform_'.$i."_long".$id] : "") . '***map***' . (isset($_POST['wdform_'.$i."_lat".$id]) ? $_POST['wdform_'.$i."_lat".$id] : "");
				  break;
				}
				case "type_date_fields": {
				  $value = (isset($_POST['wdform_'.$i."_day".$id]) ? $_POST['wdform_'.$i."_day".$id] : "") . '-' . (isset($_POST['wdform_'.$i."_month".$id]) ? $_POST['wdform_'.$i."_month".$id] : "") . '-' . (isset($_POST['wdform_'.$i."_year".$id]) ? $_POST['wdform_'.$i."_year".$id] : "");
				  break;
				}					
				case "type_time": {
				  $ss = isset($_POST['wdform_'.$i."_ss".$id]) ? $_POST['wdform_'.$i."_ss".$id] : NULL;
				  if(isset($ss)) {
					$value = (isset($_POST['wdform_'.$i."_hh".$id]) ? $_POST['wdform_'.$i."_hh".$id] : "") . ':' . (isset($_POST['wdform_'.$i."_mm".$id]) ? $_POST['wdform_'.$i."_mm".$id] : "") . ':' . (isset($_POST['wdform_'.$i."_ss".$id]) ? $_POST['wdform_'.$i."_ss".$id] : "");
				  }
				  else {
					$value = (isset($_POST['wdform_'.$i."_hh".$id]) ? $_POST['wdform_'.$i."_hh".$id] : "") . ':' . (isset($_POST['wdform_'.$i."_mm".$id]) ? $_POST['wdform_'.$i."_mm".$id] : "");
				  }								
				  $am_pm = isset($_POST['wdform_'.$i."_am_pm".$id]) ? $_POST['wdform_'.$i."_am_pm".$id] : NULL;
				  if(isset($am_pm)) {
					$value = $value . ' ' . $am_pm;
				  }
				  break;
				}					
				case "type_phone": {
				  $value = (isset($_POST['wdform_'.$i."_element_first".$id]) ? $_POST['wdform_'.$i."_element_first".$id] : "") . ' ' . (isset($_POST['wdform_'.$i."_element_last".$id]) ? $_POST['wdform_'.$i."_element_last".$id] : "");							
				  break;
				}		
				case "type_name": {				
				 $element_title = isset($_POST['wdform_'.$i."_element_title".$id]) ? esc_html($_POST['wdform_'.$i."_element_title".$id]) : NULL;
				  $element_middle = isset($_POST['wdform_'.$i."_element_middle".$id]) ? esc_html($_POST['wdform_'.$i."_element_middle".$id]) : NULL;
				  if(isset($element_title) || isset($element_middle)) {
					$value = (isset($_POST['wdform_'.$i."_element_title".$id]) ? esc_html($_POST['wdform_'.$i."_element_title".$id]) : "") . '@@@' . (isset($_POST['wdform_'.$i."_element_first".$id]) ? esc_html($_POST['wdform_'.$i."_element_first".$id]) : "") . '@@@' . (isset($_POST['wdform_'.$i."_element_last".$id]) ? esc_html($_POST['wdform_'.$i."_element_last".$id]) : "") . '@@@' . (isset($_POST['wdform_'.$i."_element_middle".$id]) ? esc_html($_POST['wdform_'.$i."_element_middle".$id]) : "");
				  }
				  else {
					$value = (isset($_POST['wdform_'.$i."_element_first".$id]) ? esc_html($_POST['wdform_'.$i."_element_first".$id]) : "") . '@@@' . (isset($_POST['wdform_'.$i."_element_last".$id]) ? esc_html($_POST['wdform_'.$i."_element_last".$id]) : "");
				  }
				  break;
				}		
				case "type_file_upload": {
				  $files = isset($_FILES['wdform_'.$i.'_file'.$id]) ? $_FILES['wdform_'.$i.'_file'.$id] : NULL;
				  foreach($files['name'] as $file_key => $file_name) {
					if($file_name) {
					  $untilupload = $form->form_fields;
					  $untilupload = substr($untilupload, strpos($untilupload,$i.'*:*id*:*type_file_upload'), -1);
					  $untilupload = substr($untilupload, 0, strpos($untilupload,'*:*new_field*:'));
					  $untilupload = explode('*:*w_field_label_pos*:*',$untilupload);
					  $untilupload = $untilupload[1];
					  $untilupload = explode('*:*w_destination*:*',$untilupload);
					  $destination = $untilupload[0];
					  $destination = str_replace(site_url() . '/', '', $destination);
					  $untilupload = $untilupload[1];
					  $untilupload = explode('*:*w_extension*:*',$untilupload);
					  $extension 	 = $untilupload[0];
					  $untilupload = $untilupload[1];
					  $untilupload = explode('*:*w_max_size*:*',$untilupload);
					  $max_size 	 = $untilupload[0];
					  $untilupload = $untilupload[1];
					  $fileName = $files['name'][$file_key];
					  $fileSize = $files['size'][$file_key];

					  if($fileSize > $max_size * 1024) {
						echo "<script> alert('" . addslashes(__('The file exceeds the allowed size of', 'form_maker')) . $max_size . " KB');</script>";
						return array($max+1);
					  }

					  $uploadedFileNameParts = explode('.',$fileName);
					  $uploadedFileExtension = array_pop($uploadedFileNameParts);
					  $to = strlen($fileName) - strlen($uploadedFileExtension) - 1;
					  
					  $fileNameFree = substr($fileName, 0, $to);
					  $invalidFileExts = explode(',', $extension);
					  $extOk = false;

					  foreach($invalidFileExts as $key => $valuee) {
						if(is_numeric(strpos(strtolower($valuee), strtolower($uploadedFileExtension)))) {
						  $extOk = true;
						}
					  }
					   
					  if ($extOk == false) {
						echo "<script> alert('" . addslashes(__('Sorry, you are not allowed to upload this type of file.', 'form_maker')) . "');</script>";
						return array($max+1);
					  }
					  
					  $fileTemp = $files['tmp_name'][$file_key];
					  $p = 1;
					  
					  if(!file_exists($destination))
						mkdir($destination , 0777);
					  if (file_exists($destination . "/" . $fileName)) {
						$fileName1 = $fileName;
						while (file_exists($destination . "/" . $fileName1)) {
						  $to = strlen($file_name) - strlen($uploadedFileExtension) - 1;
						  $fileName1 = substr($fileName, 0, $to) . '(' . $p . ').' . $uploadedFileExtension;
						//  $file['name'] = $fileName;
						  $p++;
						}
						$fileName = $fileName1;
					  }
                                          
                                        // for dropbox & google drive integration addons
                                          $check_both = 0;
					if($form->save_uploads == 0){
                                            if(defined('WD_FM_DBOX_INT') && is_plugin_active(constant('WD_FM_DBOX_INT'))){
                                                $enable = $wpdb->get_var("SELECT enable FROM " . $wpdb->prefix ."formmaker_dbox_int WHERE form_id=" . $form->id);
						if($enable == 1){
                                                    $selectable_upload = $wpdb->get_var("SELECT selectable_upload FROM " . $wpdb->prefix ."formmaker_dbox_int WHERE form_id=" . $form->id);

                                                    if((int)$selectable_upload == 1){
                                                        $temp_dir_dbox =  explode('\\', $fileTemp);
                                                        $temp_dir_dbox = implode('%%', $temp_dir_dbox);

                                                        $value.= $temp_dir_dbox . '*@@url@@*' . $fileName;
                                                    }
                                                    else{
                                                        $dlink_dbox = '<a href="'.add_query_arg(array('action' => 'WD_FM_DBOX_INT', 'addon_task' => 'upload_dbox_file', 'id' => $form->id), admin_url('admin-ajax.php')).'&dbox_file_name=' . $fileName . '&dbox_folder_name=/'.$form->title.'" >' . $fileName . '</a>';

                                                        $value.= $dlink_dbox;
                                                    }

                                                    $files['tmp_name'][$file_key]=$fileTemp;
                                                    $temp_file = array( "name" => $files['name'][$file_key], "type" => $files['type'][$file_key], "tmp_name" => $files['tmp_name'][$file_key]);
                                                }
                                                else
                                                    $check_both ++;
                                              
                                            }
                                            else
                                                $check_both ++;
                                            if(defined('WD_FM_GDRIVE_INT') && is_plugin_active(constant('WD_FM_GDRIVE_INT'))){
                                                $enable = $wpdb->get_var("SELECT enable FROM " . $wpdb->prefix ."formmaker_gdrive_int WHERE form_id=" . $form->id);
						if($enable == 1){
                                                    $selectable_upload = $wpdb->get_var("SELECT selectable_upload FROM " . $wpdb->prefix ."formmaker_gdrive_int WHERE form_id=" . $form->id);

                                                    if((int)$selectable_upload == 1){
                                                        $temp_dir_dbox =  explode('\\', $fileTemp);
                                                        $temp_dir_dbox = implode('%%', $temp_dir_dbox);
                                                        $value.= 'wdCloudAddon' . $temp_dir_dbox . '*@@url@@*' . $fileName . '*@@url@@*' . $files['type'][$file_key];
                                                    }
                                                    else{                                          
                                                        $dlink_dbox = '<a target="_blank" href="'.add_query_arg(array('action' => 'WD_FM_GDRIVE_INT', 'addon_task' => 'create_drive_link', 'id' => $form->id), admin_url('admin-ajax.php')).'&gdrive_file_name=' . $fileName . '&gdrive_folder_name='.$form->title.'" >' . $fileName . '</a>';                                                        
                                                        $value.= $dlink_dbox;
                                                    }

                                                    $files['tmp_name'][$file_key]=$fileTemp;
                                                    $temp_file = array( "name" => $files['name'][$file_key], "type" => $files['type'][$file_key], "tmp_name" => $files['tmp_name'][$file_key]);
                                                }
                                                else
                                                    $check_both ++;
                                              
                                            }
                                            else
                                                $check_both ++;
                                         
                                        }
//                                           
											if($check_both != 0){
                                                $value.= '';
                                                $files['tmp_name'][$file_key]=$fileTemp;
                                                $temp_file = array( "name" => $files['name'][$file_key], "type" => $files['type'][$file_key], "tmp_name" => $files['tmp_name'][$file_key]);
                                            }
                                            // dropbox and google drive integration addons
                                            if($form->save_uploads == 1){
                                                if(!move_uploaded_file($fileTemp, ABSPATH . $destination . '/' . $fileName)) {	
                                                    echo "<script> alert('" . addslashes(__('Error, file cannot be moved.', 'form_maker')) . "');</script>";
                                                    return array($max+1);
                                                }
                                                $value.= site_url() . '/' . $destination . '/' . $fileName . '*@@url@@*';
                                                $files['tmp_name'][$file_key]=$destination . "/" . $fileName;
                                                $temp_file = array( "name" => $files['name'][$file_key], "type" => $files['type'][$file_key], "tmp_name" => $files['tmp_name'][$file_key]);

                                            }
                                              array_push($all_files,$temp_file);
					}
				  }
				  break;
				}
				
				case 'type_address': {
				  $value = '*#*#*#';
				  $element = isset($_POST['wdform_'.$i."_street1".$id]) ? esc_html($_POST['wdform_'.$i."_street1".$id]) : NULL;
				  if(isset($element)) {
					$value = $element;
					break;
				  }
				  
				  $element = isset($_POST['wdform_'.$i."_street2".$id]) ? esc_html($_POST['wdform_'.$i."_street2".$id]) : NULL;
				  if(isset($element)) {
					$value = $element;
					break;
				  }
				  
				  $element = isset($_POST['wdform_'.$i."_city".$id]) ? esc_html($_POST['wdform_'.$i."_city".$id]) : NULL;
				  if(isset($element)) {
					$value = $element;
					break;
				  }
				  
				  $element = isset($_POST['wdform_'.$i."_state".$id]) ? esc_html($_POST['wdform_'.$i."_state".$id]) : NULL;
				  if(isset($element)) {
					$value = $element;
					break;
				  }
				  
				  $element = isset($_POST['wdform_'.$i."_postal".$id]) ? esc_html($_POST['wdform_'.$i."_postal".$id]) : NULL;
				  if(isset($element)) {
					$value = $element;
					break;
				  }
				  
				  $element = isset($_POST['wdform_'.$i."_country".$id]) ? esc_html($_POST['wdform_'.$i."_country".$id]) : NULL;
				  if(isset($element)) {
					$value = $element;
					break;
				  }						
				  break;
				}
				
				case "type_hidden": {
				  $value = isset($_POST[$label_label[$key]]) ? esc_html($_POST[$label_label[$key]]) : "";
				  break;
				}
				
				case "type_radio": {
				  $element = isset($_POST['wdform_'.$i."_other_input".$id]) ? $_POST['wdform_'.$i."_other_input".$id] : NULL;
				  if(isset($element)) {
					$value = $element;	
					break;
				  }						
				  $value = isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : "";
				  break;
				}
				
				case "type_checkbox": {
				  $start = -1;
				  $value = '';
				  for($j = 0; $j < 100; $j++) {						
					$element = isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL;
					if(isset($element)) {
					  $start = $j;
					  break;
					}
				  }
					
				  $other_element_id = -1;
				  $is_other = isset($_POST['wdform_'.$i."_allow_other".$id]) ? $_POST['wdform_'.$i."_allow_other".$id] : "";
				  if($is_other == "yes") {
					$other_element_id = isset($_POST['wdform_'.$i."_allow_other_num".$id]) ? $_POST['wdform_'.$i."_allow_other_num".$id] : "";
				  }
				  
				  if($start != -1) {
					for($j = $start; $j < 100; $j++) {
					  $element = isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL;
					  if(isset($element)) {
						if($j == $other_element_id) {
						  $value = $value . (isset($_POST['wdform_'.$i."_other_input".$id]) ? $_POST['wdform_'.$i."_other_input".$id] : "") . '***br***';
						}
						else {								
						  $value = $value . (isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : "") . '***br***';
						}
					  }
					}
				  }						
				  break;
				}
				
				case "type_paypal_price":	{
				  $value = isset($_POST['wdform_'.$i."_element_dollars".$id]) ? $_POST['wdform_'.$i."_element_dollars".$id] : 0;

				  $value = (int) preg_replace('/\D/', '', $value);
				  
				  if(isset($_POST['wdform_'.$i."_element_cents".$id])) {
					$value = $value . '.' . ( preg_replace('/\D/', '', $_POST['wdform_'.$i."_element_cents".$id]));
				  }
				  
				  $total += (float)($value);						
				  $paypal_option = array();

				  if($value != 0) {
					$quantity = (isset($_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : 1);
					array_push ($paypal['item_name'], $label_label[$key]);
					array_push ($paypal['quantity'], $quantity);
					array_push ($paypal['amount'], $value);
					$is_amount=true;
					array_push ($paypal['on_os'], $paypal_option);
				  }
				  $value = $value . $form_currency;
				  break;
				}
				
				case "type_paypal_select": {
				  if(isset($_POST['wdform_'.$i."_element_label".$id]) && $_POST['wdform_'.$i."_element".$id] !='') {
					$value = $_POST['wdform_'.$i."_element_label".$id] . ' : ' . (isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : "") . $form_currency;
				  }
				  else {
					$value = '';
				  }
				  $quantity = (isset($_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : 1);
				  $total += (float)(isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : 0) * $quantity;
				  array_push ($paypal['item_name'], $label_label[$key] . ' ' . (isset($_POST['wdform_'.$i."_element_label".$id]) ? $_POST['wdform_'.$i."_element_label".$id] : ""));
				  array_push ($paypal['quantity'], $quantity);
				  array_push ($paypal['amount'], (isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : ""));
				  if(isset($_POST['wdform_'.$i."_element".$id]) && $_POST['wdform_'.$i."_element".$id] != 0) {
					$is_amount=true;
				  }
				  $element_quantity = isset($_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : NULL;
				  if(isset($element_quantity) && $value != '') {
					$value .= '***br***' . (isset($_POST['wdform_'.$i."_element_quantity_label".$id]) ? $_POST['wdform_'.$i."_element_quantity_label".$id] : "") . ': ' . $_POST['wdform_'.$i."_element_quantity".$id] . '***quantity***';
				  }
				  $paypal_option = array();
				  $paypal_option['on'] = array();
				  $paypal_option['os'] = array();

				  for($k = 0; $k < 50; $k++) {
					$temp_val = isset($_POST['wdform_'.$i."_property".$id.$k]) ? $_POST['wdform_'.$i."_property".$id.$k] : NULL;
					if(isset($temp_val) && $value != '') {
					  array_push ($paypal_option['on'], (isset($_POST['wdform_'.$i."_element_property_label".$id.$k]) ? $_POST['wdform_'.$i."_element_property_label".$id.$k] : ""));
					  array_push ($paypal_option['os'], (isset($_POST['wdform_'.$i."_property".$id.$k]) ? $_POST['wdform_'.$i."_property".$id.$k] : ""));
					  $value .= '***br***' . (isset($_POST['wdform_'.$i."_element_property_label".$id.$k]) ? $_POST['wdform_'.$i."_element_property_label".$id.$k] : "") . ': ' . (isset($_POST['wdform_'.$i."_property".$id.$k]) ? $_POST['wdform_'.$i."_property".$id.$k] : "") . '***property***';
					}
				  }
				  array_push ($paypal['on_os'], $paypal_option);
				  break;
				}
            
				case "type_paypal_radio": {
				  if(isset($_POST['wdform_'.$i."_element_label".$id])) {
					$value = $_POST['wdform_'.$i."_element_label".$id] . ' : ' . (isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : "") . $form_currency;
				  }
				  else {
					$value = '';
				  }
				  $quantity = (isset($_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : 1);
				  $total += (float)(isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : 0) * $quantity;
				  array_push ($paypal['item_name'], $label_label[$key] . ' ' . (isset($_POST['wdform_'.$i."_element_label".$id]) ? $_POST['wdform_'.$i."_element_label".$id] : ""));
				  array_push ($paypal['quantity'], $quantity);
				  array_push ($paypal['amount'], (isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : 0));
				  if(isset($_POST['wdform_'.$i."_element".$id]) && $_POST['wdform_'.$i."_element".$id] != 0) {
					$is_amount=true;
				  }
				  
				  $element_quantity = isset($_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : NULL;
				  if(isset($element_quantity) && $value != '') {
					$value .= '***br***' . (isset($_POST['wdform_'.$i."_element_quantity_label".$id]) ? $_POST['wdform_'.$i."_element_quantity_label".$id] : "") . ': ' . $_POST['wdform_'.$i."_element_quantity".$id] . '***quantity***';
				  }					
				  
				  $paypal_option = array();
				  $paypal_option['on'] = array();
				  $paypal_option['os'] = array();

				  for($k = 0; $k < 50; $k++) {
					$temp_val = isset($_POST['wdform_'.$i."_property".$id.$k]) ? $_POST['wdform_'.$i."_property".$id.$k] : NULL;
					if(isset($temp_val) && $value != '') {
					  array_push ($paypal_option['on'], (isset($_POST['wdform_'.$i."_element_property_label".$id.$k]) ? $_POST['wdform_'.$i."_element_property_label".$id.$k] : ""));
					  array_push ($paypal_option['os'], $_POST['wdform_'.$i."_property".$id.$k]);
					  $value .= '***br***' . (isset($_POST['wdform_'.$i."_element_property_label".$id.$k]) ? $_POST['wdform_'.$i."_element_property_label".$id.$k] : "") . ': ' . $_POST['wdform_'.$i."_property".$id.$k] . '***property***';
					}
				  }
				  array_push ($paypal['on_os'], $paypal_option);
				  break;
				}

				case "type_paypal_shipping": {
				  if(isset($_POST['wdform_'.$i."_element_label".$id])) {
					$value = $_POST['wdform_'.$i."_element_label".$id] . ' : ' . (isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : "") . $form_currency;
				  }
				  else {
					$value = '';
				  }
				  $value = (isset($_POST['wdform_'.$i."_element_label".$id]) ? $_POST['wdform_'.$i."_element_label".$id] : "") . ' - ' . (isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : "") . $form_currency;						
				  $paypal['shipping'] = isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : "";
				  break;
				}

				case "type_paypal_checkbox": {
				  $start = -1;
				  $value = '';
				  for($j = 0; $j < 100; $j++) {						
					$element = isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL;
					if(isset($element)) {
					  $start = $j;
					  break;
					}
				  }
				  
				  $other_element_id = -1;
				  $is_other = isset($_POST['wdform_'.$i."_allow_other".$id]) ? $_POST['wdform_'.$i."_allow_other".$id] : "";
				  if($is_other == "yes") {
					$other_element_id = isset($_POST['wdform_'.$i."_allow_other_num".$id]) ? $_POST['wdform_'.$i."_allow_other_num".$id] : "";
				  }
				  
				  if($start != -1) {
					for($j = $start; $j < 100; $j++) {
					  $element = isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL;
					  if(isset($element)) {
						if($j == $other_element_id) {
						  $value = $value . (isset($_POST['wdform_'.$i."_other_input".$id]) ? $_POST['wdform_'.$i."_other_input".$id] : "") . '***br***';									
						}
						else {
						  $value = $value . (isset($_POST['wdform_'.$i."_element".$id.$j."_label"]) ? $_POST['wdform_'.$i."_element".$id.$j."_label"] : "") . ' - ' . (isset($_POST['wdform_'.$i."_element".$id.$j]) && $_POST['wdform_'.$i."_element".$id.$j] == '' ? '0' : (isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : "")) . $form_currency . '***br***';
						  $quantity = ((isset($_POST['wdform_' . $i . "_element_quantity" . $id]) && ($_POST['wdform_' . $i . "_element_quantity" . $id] >= 1)) ? $_POST['wdform_'.$i . "_element_quantity" . $id] : 1);
						  $total += (float)(isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : 0) * (float)($quantity);
						  array_push ($paypal['item_name'], $label_label[$key] . ' ' . (isset($_POST['wdform_'.$i."_element".$id.$j."_label"]) ? $_POST['wdform_'.$i."_element".$id.$j."_label"] : ""));
						  array_push ($paypal['quantity'], $quantity);
						  array_push ($paypal['amount'], (isset($_POST['wdform_'.$i."_element".$id.$j]) ? ($_POST['wdform_'.$i."_element".$id.$j] == '' ? '0' : $_POST['wdform_'.$i."_element".$id.$j]) : ""));
						  if (isset($_POST['wdform_'.$i."_element".$id.$j]) && $_POST['wdform_'.$i."_element".$id.$j] != 0) {
							$is_amount = TRUE;
						  }
						  $paypal_option = array();
						  $paypal_option['on'] = array();
						  $paypal_option['os'] = array();

						  for($k = 0; $k < 50; $k++) {
							$temp_val = isset($_POST['wdform_'.$i."_property".$id.$k]) ? $_POST['wdform_'.$i."_property".$id.$k] : NULL;
							if(isset($temp_val)) {
							  array_push ($paypal_option['on'], isset($_POST['wdform_'.$i."_element_property_label".$id.$k]) ? $_POST['wdform_'.$i."_element_property_label".$id.$k] : "");
							  array_push ($paypal_option['os'], $_POST['wdform_'.$i."_property".$id.$k]);
							}
						  }
						  array_push ($paypal['on_os'], $paypal_option);
						}
					  }
					}
					
					$element_quantity = isset($_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : NULL;
					if(isset($element_quantity)) {
					  $value .= (isset($_POST['wdform_'.$i."_element_quantity_label".$id]) ? $_POST['wdform_'.$i."_element_quantity_label".$id] : "") . ': ' . $_POST['wdform_'.$i."_element_quantity".$id] . '***quantity***';
					}
					for($k = 0; $k < 50; $k++) {
					  $temp_val = isset($_POST['wdform_'.$i."_property".$id.$k]) ? $_POST['wdform_'.$i."_property".$id.$k] : NULL;
					  if(isset($temp_val)) {
						$value .= '***br***' . (isset($_POST['wdform_'.$i."_element_property_label".$id.$k]) ? $_POST['wdform_'.$i."_element_property_label".$id.$k] : "") . ': ' . $_POST['wdform_'.$i."_property".$id.$k] . '***property***';
					  }
					}							
				  }
				  break;
				}
				
				case "type_star_rating": {
				  if(isset($_POST['wdform_'.$i."_selected_star_amount".$id]) && $_POST['wdform_'.$i."_selected_star_amount".$id] == "") {
					$selected_star_amount = 0;
				  }
				  else {
					$selected_star_amount = isset($_POST['wdform_'.$i."_selected_star_amount".$id]) ? $_POST['wdform_'.$i."_selected_star_amount".$id] : 0;
				  }						
				  $value = $selected_star_amount . '/' . (isset($_POST['wdform_'.$i."_star_amount".$id]) ? $_POST['wdform_'.$i."_star_amount".$id] : "");
				  break;
				}
			  
				case "type_scale_rating": {
				  $value = (isset($_POST['wdform_'.$i."_scale_radio".$id]) ? $_POST['wdform_'.$i."_scale_radio".$id] : 0) . '/' . (isset($_POST['wdform_'.$i."_scale_amount".$id]) ? $_POST['wdform_'.$i."_scale_amount".$id] : "");
				  break;
				}
				
				case "type_spinner": {
				  $value = isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : "";
				  break;
				}
				
				case "type_slider": {
				  $value = isset($_POST['wdform_'.$i."_slider_value".$id]) ? $_POST['wdform_'.$i."_slider_value".$id] : "";
				  break;
				}
				
				case "type_range": {
				  $value = (isset($_POST['wdform_'.$i."_element".$id.'0']) ? $_POST['wdform_'.$i."_element".$id.'0'] : "") . '-' . (isset($_POST['wdform_'.$i."_element".$id.'1']) ? $_POST['wdform_'.$i."_element".$id.'1'] : "");
				  break;
				}
				
				case "type_grading": {
				  $value = "";
				  $items = explode(":", isset($_POST['wdform_'.$i."_hidden_item".$id]) ? $_POST['wdform_'.$i."_hidden_item".$id] : "");
				  for($k = 0; $k < sizeof($items) - 1; $k++) {
					$value .= (isset($_POST['wdform_'.$i."_element".$id.'_'.$k]) ? $_POST['wdform_'.$i."_element".$id.'_'.$k] : "") . ':';
				  }
				  $value .= (isset($_POST['wdform_'.$i."_hidden_item".$id]) ? $_POST['wdform_'.$i."_hidden_item".$id] : "") . '***grading***';				
				  break;
				}
				
				case "type_matrix": {
				  $rows_of_matrix = explode("***", isset($_POST['wdform_'.$i."_hidden_row".$id]) ? $_POST['wdform_'.$i."_hidden_row".$id] : "");
				  $rows_count = sizeof($rows_of_matrix) - 1;
				  $column_of_matrix = explode("***", isset($_POST['wdform_'.$i."_hidden_column".$id]) ? $_POST['wdform_'.$i."_hidden_column".$id] : "");
				  $columns_count = sizeof($column_of_matrix) - 1;						
				
				  if(isset($_POST['wdform_'.$i."_input_type".$id]) && $_POST['wdform_'.$i."_input_type".$id] == "radio") {
					$input_value = "";
					for($k = 1; $k <= $rows_count; $k++) {
					  $input_value .= (isset($_POST['wdform_'.$i."_input_element".$id.$k]) ? $_POST['wdform_'.$i."_input_element".$id.$k] : 0) . "***";
					}
				  }
				  if(isset($_POST['wdform_'.$i."_input_type".$id]) && $_POST['wdform_'.$i."_input_type".$id] == "checkbox") {
					$input_value = "";							
					for($k = 1; $k <= $rows_count; $k++) {
					  for($j = 1; $j <= $columns_count; $j++) {
						$input_value .= (isset($_POST['wdform_'.$i."_input_element".$id.$k.'_'.$j]) ? $_POST['wdform_'.$i."_input_element".$id.$k.'_'.$j] : 0)."***";
					  }
					}
				  }
				  
				  if(isset($_POST['wdform_'.$i."_input_type".$id]) && $_POST['wdform_'.$i."_input_type".$id] == "text") {
					$input_value = "";
					for($k = 1; $k <= $rows_count; $k++) {
					  for($j = 1; $j <= $columns_count; $j++) {
						$input_value .= (isset($_POST['wdform_'.$i."_input_element".$id.$k.'_'.$j]) ? esc_html($_POST['wdform_'.$i."_input_element".$id.$k.'_'.$j]) : "") . "***";
					  }
					}
				  }
				  
				  if(isset($_POST['wdform_'.$i."_input_type".$id]) && $_POST['wdform_'.$i."_input_type".$id] == "select") {
					$input_value = "";
					for($k = 1; $k <= $rows_count; $k++) {
					  for($j = 1; $j <= $columns_count; $j++) {
						$input_value .= (isset($_POST['wdform_'.$i."_select_yes_no".$id.$k.'_'.$j]) ? $_POST['wdform_'.$i."_select_yes_no".$id.$k.'_'.$j] : "") . "***";	
					  }
					}
				  }
				  
				  $value = $rows_count . (isset($_POST['wdform_'.$i."_hidden_row".$id]) ? $_POST['wdform_'.$i."_hidden_row".$id] : "") . '***' . $columns_count . (isset($_POST['wdform_'.$i."_hidden_column".$id]) ? $_POST['wdform_'.$i."_hidden_column".$id] : "") . '***' . (isset($_POST['wdform_'.$i."_input_type".$id]) ? $_POST['wdform_'.$i."_input_type".$id] : "") . '***' . $input_value . '***matrix***';
				  break;
				}
				
			  }

			  if($type == "type_address") {
				if(	$value == '*#*#*#') {
				  continue;
				}
			  }
			  if($type == "type_text" or $type == "type_password" or $type == "type_textarea" or $type == "type_name" or $type == "type_submitter_mail" or $type == "type_number" or $type == "type_phone")
			  {					
				$untilupload = $form->form_fields;
				$untilupload = substr($untilupload, strpos($untilupload, $i.'*:*id*:*'.$type), -1);
				$untilupload = substr($untilupload, 0, strpos($untilupload, '*:*new_field*:'));
				$untilupload = explode('*:*w_required*:*', $untilupload);
				$untilupload = $untilupload[1];
				$untilupload = explode('*:*w_unique*:*', $untilupload);
				$unique_element = $untilupload[0];
				if(strlen($unique_element)>3)
				$unique_element = substr($unique_element, -3);
			
				if($unique_element == 'yes') {						
				  $unique = $wpdb->get_col($wpdb->prepare("SELECT id FROM " . $wpdb->prefix . "formmaker_submits WHERE form_id= %d  and element_label= %s and element_value= %s", $id, $i, addslashes($value)));
				  if ($unique) {
					echo "<script> alert('" . addslashes(__('This field %s requires a unique entry and this value was already submitted.', 'form_maker')) . "'.replace('%s','" . $label_label[$key] . "'));</script>";
					return array($max + 1);
				  }
				}
			  }
			  $save_or_no = TRUE;
			  $fvals['{'.$i.'}']=str_replace(array("***map***", "*@@url@@*", "@@@@@@@@@", "@@@", "***grading***", "***br***"), array(" ", "", " ", " ", " ", ", "), addslashes($value));
			  if ($form->savedb) {
				$save_or_no = $wpdb->insert($wpdb->prefix . "formmaker_submits", array(
				  'form_id' => $id,
				  'element_label' => $i,
				  'element_value' => stripslashes($value),
				  'group_id' => ($max + 1),
				  'date' => date('Y-m-d H:i:s'),
				  'ip' => $_SERVER['REMOTE_ADDR'],
				  'user_id_wd' => $current_user->ID,
				), array(
				  '%d',
				  '%s',
				  '%s',
				  '%d',
				  '%s',
				  '%s',
				  '%d'
				));
				}
				if (!$save_or_no) {
					return FALSE;
				}
				$chgnac = FALSE;
			}
			else{
				$fvals['{'.$i.'}']='';
			}
		}
    }
    else {
      foreach ($label_type as $key => $type) {
        $value = '';
        if ($type == "type_submit_reset" or $type == "type_map" or $type == "type_editor" or  $type == "type_captcha" or $type == "type_arithmetic_captcha" or $type == "type_recaptcha" or  $type == "type_button" or $type=="type_paypal_total")
          continue;
        $i = $label_id[$key];
        if ($type != "type_address") {
          $deleted = isset($_POST[$i . "_type" . $id]) ? $_POST[$i . "_type" . $id] : NULL;
          if (!isset($deleted))
            break;
        }
        if ($type == 'type_paypal_total') {
          continue;
        }
        switch ($type) {
          case 'type_text':
          case 'type_password':
          case 'type_textarea':
          case "type_submitter_mail":
          case "type_date":
          case "type_own_select":
          case "type_country":
          case "type_number": {
            $value = isset($_POST[$i . "_element" . $id]) ? esc_html($_POST[$i . "_element" . $id]) : "";
            break;
          }
          case "type_mark_map": {
            $value = (isset($_POST[$i . "_long" . $id]) ? $_POST[$i . "_long" . $id] : "") . '***map***' . (isset($_POST[$i . "_lat" . $id]) ? $_POST[$i . "_lat" . $id] : "");
            break;
          }
          case "type_date_fields": {
            $value = (isset($_POST[$i . "_day" . $id]) ? $_POST[$i . "_day" . $id] : "") . '-' . (isset($_POST[$i . "_month" . $id]) ? $_POST[$i . "_month" . $id] : "") . '-' . (isset($_POST[$i . "_year" . $id]) ? $_POST[$i . "_year" . $id] : "");
            break;
          }
          case "type_time": {
            $ss = isset($_POST[$i . "_ss" . $id]) ? $_POST[$i . "_ss" . $id] : NULL;
            if (isset($ss)) {
              $value = (isset($_POST[$i . "_hh" . $id]) ? $_POST[$i . "_hh" . $id] : "") . ':' . (isset($_POST[$i . "_mm" . $id]) ? $_POST[$i . "_mm" . $id] : "") . ':' . $ss;
            }
            else {
              $value = (isset($_POST[$i . "_hh" . $id]) ? $_POST[$i . "_hh" . $id] : "") . ':' . (isset($_POST[$i . "_mm" . $id]) ? $_POST[$i . "_mm" . $id] : "");
            }
            $am_pm = isset($_POST[$i . "_am_pm" . $id]) ? $_POST[$i . "_am_pm" . $id] : NULL;
            if (isset($am_pm))
              $value = $value . ' ' . $am_pm;
            break;
          }
          case "type_phone": {
            $value = (isset($_POST[$i . "_element_first" . $id]) ? $_POST[$i . "_element_first" . $id] : "") . ' ' . (isset($_POST[$i . "_element_last" . $id]) ? $_POST[$i . "_element_last" . $id] : "");
            break;
          }
          case "type_name": {
            $element_title = isset($_POST[$i . "_element_title" . $id]) ? esc_html($_POST[$i . "_element_title" . $id]) : NULL;
            if (isset($element_title)) {
              $value = $element_title . ' ' . (isset($_POST[$i . "_element_first" . $id]) ? esc_html($_POST[$i . "_element_first" . $id]) : "") . ' ' . (isset($_POST[$i . "_element_last" . $id]) ? esc_html($_POST[$i . "_element_last" . $id]) : "") . ' ' . (isset($_POST[$i . "_element_middle" . $id]) ? esc_html($_POST[$i . "_element_middle" . $id]) : "");
            }
            else {
              $value = (isset($_POST[$i . "_element_first" . $id]) ? esc_html($_POST[$i . "_element_first" . $id]) : "") . ' ' . (isset($_POST[$i . "_element_last" . $id]) ? esc_html($_POST[$i . "_element_last" . $id]) : "");
            }
            break;
          }
          case "type_file_upload": {
            $file = isset($_FILES[$i . '_file' . $id]) ? $_FILES[$i . '_file' . $id] : NULL;
            if ($file['name']) {
              $untilupload = $form->form;
              $pos1 = strpos($untilupload, "***destinationskizb" . $i . "***");
              $pos2 = strpos($untilupload, "***destinationverj" . $i . "***");
              $destination = substr($untilupload, $pos1 + (23 + (strlen($i) - 1)), $pos2 - $pos1 - (23 + (strlen($i) - 1)));
              $pos1 = strpos($untilupload, "***extensionskizb" . $i . "***");
              $pos2 = strpos($untilupload, "***extensionverj" . $i . "***");
              $extension = substr($untilupload, $pos1 + (21 + (strlen($i) - 1)), $pos2 - $pos1 - (21 + (strlen($i) - 1)));
              $pos1 = strpos($untilupload, "***max_sizeskizb" . $i . "***");
              $pos2 = strpos($untilupload, "***max_sizeverj" . $i . "***");
              $max_size = substr($untilupload, $pos1 + (20 + (strlen($i) - 1)), $pos2 - $pos1 - (20 + (strlen($i) - 1)));
              $fileName = $file['name'];
              $destination = str_replace(site_url() . '/', '', $destination);
              $fileSize = $file['size'];
              if ($fileSize > $max_size * 1024) {
                echo "<script> alert('" . addslashes(__('The file exceeds the allowed size of', 'form_maker')) . $max_size . " KB');</script>";
                return array($max + 1);
              }
              $uploadedFileNameParts = explode('.', $fileName);
              $uploadedFileExtension = array_pop($uploadedFileNameParts);
              $to = strlen($fileName) - strlen($uploadedFileExtension) - 1;
              $fileNameFree = substr($fileName, 0, $to);
              $invalidFileExts = explode(',', $extension);
              $extOk = FALSE;
              foreach ($invalidFileExts as $key => $value) {
                if (is_numeric(strpos(strtolower($value), strtolower($uploadedFileExtension)))) {
                  $extOk = TRUE;
                }
              }
              if ($extOk == FALSE) {
                echo "<script> alert('" . addslashes(__('Sorry, you are not allowed to upload this type of file.', 'form_maker')) . "');</script>";
                return array($max + 1);
              }
              $fileTemp = $file['tmp_name'];
              $p = 1;
			  
			  if(!file_exists($destination))
					mkdir($destination , 0777);
					
              if (file_exists($destination . "/" . $fileName)) {
                $fileName1 = $filename;
                while (file_exists($destination . "/" . $fileName1)) {
                  $to = strlen($file['name']) - strlen($uploadedFileExtension) - 1;
                  $fileName1 = substr($fileName, 0, $to) . '(' . $p . ').' . $uploadedFileExtension;
                  $file['name'] = $fileName;
                  $p++;
                }
                $fileName = $fileName1;
              }
              if (is_dir(ABSPATH . $destination)) {
                if (!move_uploaded_file($fileTemp, ABSPATH . $destination . '/' . $fileName)) {
                  echo "<script> alert('" . addslashes(__('Error, file cannot be moved.', 'form_maker')) . "');</script>";
                  return array($max + 1);
                }
              }
              else {
                echo "<script> alert('" . addslashes(__('Error, file destination does not exist.', 'form_maker')) . "');</script>";
                return array($max + 1);
              }
              $value = site_url() . '/' . $destination . '/' . $fileName . '*@@url@@*';
              $file['tmp_name'] = $destination . "/" . $fileName;
              $file['name'] = ABSPATH . $destination . "/" . $fileName;
              // $temp_file = array( "name" => $files['name'][$file_key], "type" => $files['type'][$file_key], "tmp_name" => $files['tmp_name'][$file_key]);
							array_push($all_files, $file);
            }
            break;
          }
          case 'type_address': {
            $value = '*#*#*#';
            if (isset($_POST[$i . "_street1" . $id])) {
              $value = esc_html($_POST[$i . "_street1" . $id]);
              break;
            }
            if (isset($_POST[$i . "_street2" . $id])) {
              $value = esc_html($_POST[$i . "_street2" . $id]);
              break;
            }
            if (isset($_POST[$i . "_city" . $id])) {
              $value = esc_html($_POST[$i . "_city" . $id]);
              break;
            }
            if (isset($_POST[$i . "_state" . $id])) {
              $value = esc_html($_POST[$i . "_state" . $id]);
              break;
            }
            if (isset($_POST[$i . "_postal" . $id])) {
              $value = esc_html($_POST[$i . "_postal" . $id]);
              break;
            }
            if (isset($_POST[$i . "_country" . $id])) {
              $value = esc_html($_POST[$i . "_country" . $id]);
              break;
            }
            break;
          }
          case "type_hidden": {
            $value = isset($_POST[$label_label[$key]]) ? $_POST[$label_label[$key]] : "";
            break;
          }
          case "type_radio": {
            $element = isset($_POST[$i . "_other_input" . $id]) ? $_POST[$i . "_other_input" . $id] : NULL;
            if (isset($element)) {
              $value = $element;
              break;
            }
            $value = isset($_POST[$i . "_element" . $id]) ? $_POST[$i . "_element" . $id] : "";
            break;
          }
          case "type_checkbox": {
            $start = -1;
            $value = '';
            for ($j = 0; $j < 100; $j++) {
              if (isset($_POST[$i . "_element" . $id . $j])) {
                $start = $j;
                break;
              }
            }
            $other_element_id = -1;
            $is_other = isset($_POST[$i . "_allow_other" . $id]) ? $_POST[$i . "_allow_other" . $id] : "";
            if ($is_other == "yes") {
              $other_element_id = isset($_POST[$i . "_allow_other_num" . $id]) ? $_POST[$i . "_allow_other_num" . $id] : "";
            }
            if ($start != -1) {
              for ($j = $start; $j < 100; $j++) {
                if (isset($_POST[$i . "_element" . $id . $j])) {
                  if ($j == $other_element_id) {
                    $value = $value . (isset($_POST[$i . "_other_input" . $id]) ? $_POST[$i . "_other_input" . $id] : "") . '***br***';
                  }
                  else {
                    $value = $value . $_POST[$i . "_element" . $id . $j] . '***br***';
                  }
                }
              }
            }
            break;
          }
          case "type_paypal_price": {
            $value = 0;
            if (isset($_POST[$i."_element_dollars".$id])) {
              $value = $_POST[$i . "_element_dollars" . $id];
            }
            $value = (int) preg_replace('/\D/', '', $value);
            if (isset($_POST[$i . "_element_cents" . $id])) {
              $value = $value . '.' . (preg_replace('/\D/', '', $_POST[$i . "_element_cents" . $id]));
            }
            $total += (float)($value);
            $paypal_option = array();
            if ($value != 0) {
              array_push($paypal['item_name'], $label_label[$key]);
              $quantity = ((isset($_POST[$i . "_element_quantity" . $id]) && ($_POST[$i . "_element_quantity" . $id] >= 1)) ? $_POST[$i . "_element_quantity" . $id] : 1);
              array_push($paypal['quantity'], $quantity);
              array_push($paypal['amount'], $value);
              $is_amount=true;
              array_push($paypal['on_os'], $paypal_option);
            }
            $value = $value . $form_currency;
            break;
          }
          case "type_paypal_select": {
            $value = '';
            $value = (isset($_POST[$i . "_element_label" . $id]) ? $_POST[$i . "_element_label" . $id] : "") . ' : ' . (isset($_POST[$i . "_element" . $id]) ? $_POST[$i . "_element" . $id] : "") . $form_currency;
            $quantity = ((isset($_POST[$i . "_element_quantity" . $id]) && ($_POST[$i . "_element_quantity" . $id] >= 1)) ? $_POST[$i . "_element_quantity" . $id] : 1);
            $total += (float)(isset($_POST[$i . "_element" . $id]) ? $_POST[$i . "_element" . $id] : 0) * (float)($quantity);
            array_push($paypal['item_name'], $label_label[$key] . ' ' . (isset($_POST[$i."_element_label".$id]) ? $_POST[$i."_element_label".$id] : ""));
            array_push($paypal['quantity'], $quantity);
            array_push($paypal['amount'], isset($_POST[$i."_element".$id]) ? $_POST[$i."_element".$id] : "");
            if(isset($_POST[$i."_element".$id]) && $_POST[$i."_element".$id] != 0) {
							$is_amount=true;
						}
            $element_quantity_label = isset($_POST[$i."_element_quantity_label".$id]) ? $_POST[$i."_element_quantity_label".$id] : NULL;
            if (isset($element_quantity_label)) {
              $value .= '***br***' . $element_quantity_label . ': ' . $quantity;
            }
            $paypal_option=array();
            $paypal_option['on']=array();
            $paypal_option['os']=array();
            for($k=0; $k<50; $k++) {
              $temp_val = isset($_POST[$i."_element_property_value".$id.$k]) ? $_POST[$i."_element_property_value".$id.$k] : NULL;
              if(isset($temp_val)) {			
                array_push($paypal_option['on'], isset($_POST[$i."_element_property_label".$id.$k]) ? $_POST[$i."_element_property_label".$id.$k] : "");
                array_push($paypal_option['os'], $temp_val);
                $value .= '***br***' . (isset($_POST[$i."_element_property_label".$id.$k]) ? $_POST[$i."_element_property_label".$id.$k] : "") . ': ' . $temp_val;
              }
            }
            array_push($paypal['on_os'], $paypal_option);
            break;
          }
          case "type_paypal_radio": {
            $value = '';
            if (isset($_POST[$i."_element_label".$id]) && ($_POST[$i."_element_label".$id] != '')) {
              $value = $_POST[$i."_element_label".$id] . ' - ' . (isset($_POST[$i."_element".$id]) ? $_POST[$i."_element".$id] : "") . $form_currency;
              $quantity = ((isset($_POST[$i . "_element_quantity" . $id]) && ($_POST[$i . "_element_quantity" . $id] >= 1)) ? $_POST[$i . "_element_quantity" . $id] : 1);
              $total+=(float)(isset($_POST[$i."_element".$id]) ? $_POST[$i."_element".$id] : 0)*(float)($quantity);
              array_push($paypal['item_name'], $label_label[$key] . ' ' . $_POST[$i."_element_label".$id]);
              array_push($paypal['quantity'], $quantity);
              array_push($paypal['amount'], isset($_POST[$i."_element".$id]) ? $_POST[$i."_element".$id] : "");
              if(isset($_POST[$i."_element".$id]) && $_POST[$i."_element".$id] != 0) {
                $is_amount=true;
              }
              $element_quantity_label = isset($_POST[$i."_element_quantity_label".$id]) ? $_POST[$i."_element_quantity_label".$id] : NULL;
              if (isset($element_quantity_label)) {
                $value .= '***br***' . $element_quantity_label . ': ' . $quantity;
              }
              $paypal_option = array();
              $paypal_option['on'] = array();
              $paypal_option['os'] = array();
              for ($k = 0; $k < 50; $k++) {
                $temp_val = isset($_POST[$i."_element_property_value".$id.$k]) ? $_POST[$i."_element_property_value".$id.$k] : NULL;
                if(isset($temp_val)) {			
                  array_push ($paypal_option['on'], isset($_POST[$i."_element_property_label".$id.$k]) ? $_POST[$i."_element_property_label".$id.$k] : "");
                  array_push ($paypal_option['os'], $temp_val);
                  $value .= '***br***' . (isset($_POST[$i."_element_property_label".$id.$k]) ? $_POST[$i."_element_property_label".$id.$k] : "") . ': ' . $temp_val;
                }
              }
              array_push ($paypal['on_os'], $paypal_option);
            }
            break;
          }
          case "type_paypal_shipping": {
            $value = '';
            if (isset($_POST[$i."_element_label".$id]) && ($_POST[$i."_element_label".$id] != '')) {
              $value = $_POST[$i."_element_label".$id] . ' - ' . (isset($_POST[$i."_element".$id]) ? $_POST[$i."_element".$id] : "") . $form_currency;
              $paypal['shipping'] = isset($_POST[$i."_element".$id]) ? $_POST[$i."_element".$id] : "";
            }
            break;
          }
          case "type_paypal_checkbox": {
            $start = -1;
            $value = '';
            for ($j = 0; $j < 100; $j++) {
              if (isset($_POST[$i."_element".$id.$j])) {
                $start = $j;
                break;
              }
            }
            $other_element_id = -1;
            $is_other = isset($_POST[$i."_allow_other".$id]) ? $_POST[$i."_allow_other".$id] : "";
            if ($is_other == "yes") {
              $other_element_id = isset($_POST[$i."_allow_other_num".$id]) ? $_POST[$i."_allow_other_num".$id] : "";
            }
            if ($start != -1) {
              for ($j = $start; $j < 100; $j++) {
                if (isset($_POST[$i."_element".$id.$j])) {
                  if ($j == $other_element_id) {
                    $value = $value . (isset($_POST[$i."_other_input".$id]) ? $_POST[$i."_other_input".$id] : "") . '***br***';
                  }
                  else {
                    $value = $value . (isset($_POST[$i."_element".$id.$j."_label"]) ? $_POST[$i."_element".$id.$j."_label"] : "") . ' - ' . ($_POST[$i."_element".$id.$j] == '' ? '0' : $_POST[$i."_element".$id.$j]) . $form_currency . '***br***';
                    $quantity = ((isset($_POST[$i . "_element_quantity" . $id]) && ($_POST[$i . "_element_quantity" . $id] >= 1)) ? $_POST[$i . "_element_quantity" . $id] : 1);
                    $total += (float)(isset($_POST[$i."_element".$id.$j]) ? $_POST[$i."_element".$id.$j] : 0) * (float)($quantity);
                    array_push($paypal['item_name'], $label_label[$key] . ' ' . (isset($_POST[$i."_element".$id.$j."_label"]) ? $_POST[$i."_element".$id.$j."_label"] : ""));
                    array_push($paypal['quantity'], $quantity);
                    array_push($paypal['amount'], ($_POST[$i."_element".$id.$j] == '') ? '0' : $_POST[$i."_element".$id.$j]);
                    if (isset($_POST[$i."_element".$id.$j]) && $_POST[$i."_element".$id.$j] != 0) {
                      $is_amount = TRUE;
                    }
                    $paypal_option = array();
                    $paypal_option['on'] = array();
                    $paypal_option['os'] = array();
                    for ($k = 0; $k < 50; $k++) {
                      $temp_val = isset($_POST[$i."_element_property_value".$id.$k]) ? $_POST[$i."_element_property_value".$id.$k] : NULL;
                      if (isset($temp_val)) {			
                        array_push ($paypal_option['on'], isset($_POST[$i."_element_property_label".$id.$k]) ? $_POST[$i."_element_property_label".$id.$k] : "");
                        array_push ($paypal_option['os'], $temp_val);
                      }
                    }
                    array_push ($paypal['on_os'], $paypal_option);
                  }
                }
              }
              $element_quantity_label = isset($_POST[$i."_element_quantity_label".$id]) ? $_POST[$i."_element_quantity_label".$id] : NULL;
              $quantity = ((isset($_POST[$i . "_element_quantity" . $id]) && ($_POST[$i . "_element_quantity" . $id] >= 1)) ? $_POST[$i . "_element_quantity" . $id] : 1);
              if (isset($element_quantity_label)) {
                $value .= $element_quantity_label . ': ' . $quantity . '***br***';
              }
              for ($k = 0; $k < 50; $k++) {
                $temp_val = isset($_POST[$i."_element_property_value".$id.$k]) ? $_POST[$i."_element_property_value".$id.$k] : NULL;
                if (isset($temp_val)) {			
                  $value .= (isset($_POST[$i."_element_property_label".$id.$k]) ? $_POST[$i."_element_property_label".$id.$k] : "") . ': ' . $temp_val . '***br***';
                }
              }
            }
            break;
          }
          case "type_star_rating": {
            if (isset($_POST[$i."_selected_star_amount".$id]) &&  $_POST[$i."_selected_star_amount".$id] != "") {
              $selected_star_amount = $_POST[$i."_selected_star_amount".$id];
            }
            else {
              $selected_star_amount = 0;
            }
            $value = (isset($_POST[$i."_star_amount".$id]) ? $_POST[$i."_star_amount".$id] : '').'***'.$selected_star_amount.'***'.(isset($_POST[$i."_star_color".$id]) ? $_POST[$i."_star_color".$id] : '').'***star_rating***';									
            break;
          }
          case "type_scale_rating": {
            $value = (isset($_POST[$i."_scale_radio".$id]) ? $_POST[$i."_scale_radio".$id] : 0).'/'.(isset($_POST[$i."_scale_amount".$id]) ? $_POST[$i."_scale_amount".$id] : '');
            break;
          }
          case "type_spinner": {
            $value = (isset($_POST[$i."_element".$id]) ? $_POST[$i."_element".$id] : '');
            break;
          }
          case "type_slider":	{
            $value = (isset($_POST[$i."_slider_value".$id]) ? $_POST[$i."_slider_value".$id] : '');
            break;
          }
          case "type_range": {
            $value = (isset($_POST[$i."_element".$id . '0']) ? $_POST[$i."_element".$id . '0'] : '') .'-'.(isset($_POST[$i."_element".$id.'1']) ? $_POST[$i."_element".$id.'1'] : '');
            break;
          }
          case "type_grading": {
            $value = "";
            if (isset($_POST[$i."_hidden_item".$id])) {
              $items = explode(":", $_POST[$i."_hidden_item".$id]);
              for ($k = 0; $k < sizeof($items) - 1; $k++) {
                if (isset($_POST[$i."_element".$id.$k])) {
                  $value .= $_POST[$i."_element".$id.$k].':';
                }
              }
              $value .= $_POST[$i."_hidden_item".$id].'***grading***';
            }
            break;
          }
          case "type_matrix": {
            $rows_of_matrix = explode("***", isset($_POST[$i."_hidden_row".$id]) ? $_POST[$i."_hidden_row".$id] : "");
            $rows_count = sizeof($rows_of_matrix) - 1;
            $column_of_matrix = explode("***", isset($_POST[$i."_hidden_column".$id]) ? $_POST[$i."_hidden_column".$id] : "");
            $columns_count = sizeof($column_of_matrix) - 1;
            $row_ids = explode(",", substr(isset($_POST[$i."_row_ids".$id]) ? $_POST[$i."_row_ids".$id] : "", 0, -1));
            $column_ids = explode(",", substr(isset($_POST[$i."_column_ids".$id]) ? $_POST[$i."_column_ids".$id] : "", 0, -1));
            if (isset($_POST[$i."_input_type".$id]) && $_POST[$i."_input_type".$id] == "radio") {
              $input_value="";
              foreach($row_ids as $row_id) {
                $input_value.=(isset($_POST[$i."_input_element".$id.$row_id]) ? $_POST[$i."_input_element".$id.$row_id] : 0)."***";
              }
            }
            if (isset($_POST[$i."_input_type".$id]) && $_POST[$i."_input_type".$id] == "checkbox") {
              $input_value="";
              foreach($row_ids as $row_id)
                foreach($column_ids as $column_id)
                  $input_value .= (isset($_POST[$i."_input_element".$id.$row_id.'_'.$column_id]) ? $_POST[$i."_input_element".$id.$row_id.'_'.$column_id] : 0)."***";
            }
            if (isset($_POST[$i."_input_type".$id]) && $_POST[$i."_input_type".$id] == "text") {
              $input_value="";
              foreach($row_ids as $row_id)
                foreach($column_ids as $column_id)
                  $input_value .= (isset($_POST[$i."_input_element".$id.$row_id.'_'.$column_id]) ? esc_html($_POST[$i."_input_element".$id.$row_id.'_'.$column_id]) : "")."***";
            }
            if (isset($_POST[$i."_input_type".$id]) && $_POST[$i."_input_type".$id] == "select") {
              $input_value="";
              foreach($row_ids as $row_id)
              foreach($column_ids as $column_id)
              $input_value .= (isset($_POST[$i."_select_yes_no".$id.$row_id.'_'.$column_id]) ? $_POST[$i."_select_yes_no".$id.$row_id.'_'.$column_id] : "")."***";	
            }
            $value = $rows_count . '***' . (isset($_POST[$i."_hidden_row".$id]) ? $_POST[$i."_hidden_row".$id] : "") . $columns_count . '***' . (isset($_POST[$i."_hidden_column".$id]) ? $_POST[$i."_hidden_column".$id] : "") . (isset($_POST[$i."_input_type".$id]) ? $_POST[$i."_input_type".$id] : "") . '***' .$input_value . '***matrix***';	
            break;
          }
        }
        if ($type == "type_address") {
          if ($value == '*#*#*#') {
            // break; ?????????????????????????????????????????????????????
            continue;
          }
        }
        $unique_element = isset($_POST[$i . "_unique" . $id]) ? $_POST[$i . "_unique" . $id] : "";
        if ($unique_element == 'yes') {
          $unique = $wpdb->get_col($wpdb->prepare("SELECT id FROM " . $wpdb->prefix . "formmaker_submits WHERE form_id= %d  and element_label= %s and element_value= %s", $id_old, $i, addslashes($value)));
          if ($unique) {
            echo "<script> alert('" . addslashes(__('This field %s requires a unique entry and this value was already submitted.', 'form_maker')) . "'.replace('%s','" . $label_label[$key] . "'));</script>";
            return array($max + 1);
          }
        }
       
        $r = $wpdb->prefix . "formmaker_submits";
        
        $save_or_no = $wpdb->insert($r, array(
            'form_id' => $id_old,
            'element_label' => $i,
            'element_value' => stripslashes($value),
            'group_id' => ($max + 1),
            'date' => date('Y-m-d H:i:s'),
            'ip' => $ip,
            'user_id_wd' => $current_user->ID,
          ), array(
            '%d',
            '%s',
            '%s',
            '%d',
            '%s',
            '%s',
            '%d'
          ));
        if (!$save_or_no) {
          return FALSE;
        }
        $chgnac = FALSE;
      }
    }

		
		$subid = $wpdb->get_var("SELECT MAX( group_id ) FROM " . $wpdb->prefix ."formmaker_submits" );
		$user_fields = array("subid"=>$subid, "ip"=>$ip, "userid"=>$wp_userid, "username"=>$wp_username, "useremail"=>$wp_useremail);

		$queries = $wpdb->get_results( $wpdb->prepare("SELECT * FROM " .$wpdb->prefix. "formmaker_query WHERE form_id=%d",(int)$id ));
		if($queries)
		{
			foreach($queries as $query)
			{
				$temp		= explode('***wdfcon_typewdf***',$query->details);
				$con_type	= $temp[0];
				$temp		= explode('***wdfcon_methodwdf***',$temp[1]);
				$con_method	= $temp[0];
				$temp		= explode('***wdftablewdf***',$temp[1]);
				$table_cur	= $temp[0];
				$temp		= explode('***wdfhostwdf***',$temp[1]);
				$host		= $temp[0];
				$temp		= explode('***wdfportwdf***',$temp[1]);
				$port		= $temp[0];
				$temp		= explode('***wdfusernamewdf***',$temp[1]);
				$username	= $temp[0];
				$temp		= explode('***wdfpasswordwdf***',$temp[1]);
				$password	= $temp[0];
				$temp		= explode('***wdfdatabasewdf***',$temp[1]);
				$database	= $temp[0];
				
				$query=str_replace(array_keys($fvals), $fvals ,$query->query);		
				foreach($user_fields as $user_key=>$user_field)
					$query=str_replace('{'.$user_key.'}', $user_field , $query);		
		
				if($con_type == 'remote')
				{ 
					$wpdb_temp = new wpdb($username, $password, $database, $host);
					$wpdb_temp->query($query);				
				}
				else {
          $wpdb->query($query);
        }
			}
      // $wpdb= new wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
		}
		
	$addons = array('WD_FM_MAILCHIMP' => 'MailChimp', 'WD_FM_REG' => 'Registration');
	foreach($addons as $addon => $addon_name)
	{	
		if (defined($addon) && is_plugin_active(constant($addon))) {
			$_GET['addon_task']='frontend';
			$_GET['form_id']=$id;
			$GLOBALS['fvals']=$fvals;
			do_action($addon.'_init');
		}				
	}
	
    $str = '';
    
    if ($form->paypal_mode)	{
      if ($paypal['item_name'])	{
        if ($is_amount)	{
          $tax = $form->tax;
          $currency = $form->payment_currency;
          $business = $form->paypal_email;
          $ip = $_SERVER['REMOTE_ADDR'];       
          $total2 = round($total, 2);
          $save_or_no = $wpdb->insert($wpdb->prefix . "formmaker_submits", array(
            'form_id' => $id,
            'element_label' => 'item_total',
            'element_value' => $total2 . $form_currency,
            'group_id' => ($max + 1),
            'date' => date('Y-m-d H:i:s'),
            'ip' => $ip,
            'user_id_wd' => $current_user->ID,
          ), array(
            '%d',
            '%s',
            '%s',
            '%d',
            '%s',
            '%s',
            '%d'
          ));
          if (!$save_or_no) {
            return false;
          }
          $total = $total + ($total * $tax) / 100;
          if (isset($paypal['shipping'])) {
            $total = $total + $paypal['shipping'];
          }
          $total = round($total, 2);        
          $save_or_no = $wpdb->insert($wpdb->prefix . "formmaker_submits", array(
            'form_id' => $id,
            'element_label' => 'total',
            'element_value' => $total . $form_currency,
            'group_id' => ($max + 1),
            'date' => date('Y-m-d H:i:s'),
            'ip' => $ip,
            'user_id_wd' => $current_user->ID,
          ), array(
            '%d',
            '%s',
            '%s',
            '%d',
            '%s',
            '%s',
            '%d'
          ));
          if (!$save_or_no) {
            return false;
          }
          $save_or_no = $wpdb->insert($wpdb->prefix . "formmaker_submits", array(
            'form_id' => $id,
            'element_label' => '0',
            'element_value' => 'In progress',
            'group_id' => ($max + 1),
            'date' => date('Y-m-d H:i:s'),
            'ip' => $ip,
            'user_id_wd' => $current_user->ID,
          ), array(
            '%d',
            '%s',
            '%s',
            '%d',
            '%s',
            '%s',
            '%d'
          ));
          if (!$save_or_no) {
            return false;
          }
          $str = '';
          if ($form->checkout_mode==1 || $form->checkout_mode == "production") {
            $str .= "https://www.paypal.com/cgi-bin/webscr?";
          }
          else {
            $str .= "https://www.sandbox.paypal.com/cgi-bin/webscr?";
          }
          $str .= "currency_code=" . $currency;
          $str .= "&business=" . urlencode($business);
          $str .= "&cmd=" . "_cart";
          $str .= "&notify_url=" . admin_url('admin-ajax.php?action=checkpaypal%26form_id=' . $id . '%26group_id=' . ($max + 1));
          $str .= "&upload=" . "1";
          $str .= "&charset=UTF-8";
          if (isset($paypal['shipping'])) {
            $str = $str . "&shipping_1=" . $paypal['shipping'];
            //	$str=$str."&weight_cart=".$paypal['shipping'];
            //	$str=$str."&shipping2=3".$paypal['shipping'];
            $str = $str."&no_shipping=2";
          }
          $i=0;
          foreach ($paypal['item_name'] as $pkey => $pitem_name) {
            if($paypal['amount'][$pkey]) {
              $i++;
              $str = $str."&item_name_".$i."=".urlencode($pitem_name);
              $str = $str."&amount_".$i."=".$paypal['amount'][$pkey];
              $str = $str."&quantity_".$i."=".$paypal['quantity'][$pkey];
              if ($tax) {
                $str = $str . "&tax_rate_" . $i . "=" . $tax;
              }
              if ($paypal['on_os'][$pkey]) {
                foreach ($paypal['on_os'][$pkey]['on'] as $on_os_key => $on_item_name) {
                  $str = $str."&on".$on_os_key."_".$i."=".$on_item_name;
                  $str = $str."&os".$on_os_key."_".$i."=".$paypal['on_os'][$pkey]['os'][$on_os_key];
                }
              }
            }
          }
        }
      }
    }
	
	if($form->mail_verify){	
		unset($_SESSION['hash']);
		unset($_SESSION['gid']);
		$ip = $_SERVER['REMOTE_ADDR'];	
		$_SESSION['gid']  = $max+1;
		$send_tos = explode('**',$form->send_to);
		if($send_tos){
			foreach($send_tos as $send_index => $send_to)
			{
				$_SESSION['hash'][] = md5($ip.time().rand());
				$send_to = str_replace('*', '',$send_to);		
				$save_or_no = $wpdb->insert($wpdb->prefix . "formmaker_submits", array(
					'form_id' => $id,
					'element_label' => 'verifyInfo@'.$send_to,
					'element_value' => $_SESSION['hash'][$send_index]."**".$form->mail_verify_expiretime."**".$send_to,
					'group_id' => ($max + 1),
					'date' => date('Y-m-d H:i:s'),
					'ip' => $ip,
					'user_id_wd' => $current_user->ID,
				  ), array(
					'%d',
					'%s',
					'%s',
					'%d',
					'%s',
					'%s',
					'%d'
				  ));
				if (!$save_or_no) {
					return false;
				}  
			}
		}
	}
	
	
    if ($chgnac) {
		global $wpdb;
		if ($form->submit_text_type != 4) {
			$_SESSION['massage_after_submit' . $id] = addslashes(addslashes(__('Nothing was submitted.', 'form_maker')));
		}
		$_SESSION['error_or_no' . $id] = 1;
		$_SESSION['form_submit_type' . $id] = $form->submit_text_type . "," . $form->id;
		wp_redirect($_SERVER["REQUEST_URI"]);
		exit;
    }

    $addons = array('WD_FM_GDRIVE_INT' => 'GDriveInt', 'WD_FM_DBOX_INT' => 'DboxInt', 'WD_FM_POST_GEN' => 'PostGen', 'WD_FM_PUSHOVER' => 'Pushover'); // the sequence is important for google drive and drop box addons !!!!!!!!!!
    foreach($addons as $addon => $addon_name) {	
        if (defined($addon) && is_plugin_active(constant($addon))) {
            $_GET['addon_task'] = 'frontend';
            $_GET['form_id'] = $id;
            $GLOBALS['all_files'] = json_encode($all_files);
            $GLOBALS['form_currency'] = $form_currency;
            do_action($addon.'_init');
        }				
    }
    return array($all_files, $str);
  }

  public function remove($group_id) {
    global $wpdb;
    $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'formmaker_submits WHERE group_id= %d', $group_id));
  }
  
   public function get_after_submission_text($form_id) {
    global $wpdb;
	$submit_text = $wpdb->get_var("SELECT submit_text FROM " . $wpdb->prefix . "formmaker WHERE id='" . $form_id . "'");
	$current_user =  wp_get_current_user();
	if ($current_user->ID != 0){
		$userid =  $current_user->ID;
		$username =  $current_user->display_name;
		$useremail =  $current_user->user_email;
	} else{
		$userid =  '';
		$username = '';
		$useremail = '';
	}
	$ip = $_SERVER['REMOTE_ADDR'];
	$subid = $wpdb->get_var("SELECT MAX( group_id ) FROM " . $wpdb->prefix ."formmaker_submits" );	
	$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "formmaker WHERE id=%d", $form_id));
	
	$old = false;		
	if(isset($row->form)) {
		$old = true;
	}
	$label_order_original = array();
	$label_order_ids = array();
	$submission_array = array();
	if($old == false || ($old == true && $row->form == '')) {
		$label_all = explode('#****#',$row->label_order_current);
	} else {
		$label_all = explode('#****#',$row->label_order);    
	}
	$label_all = array_slice($label_all, 0, count($label_all) - 1);
	foreach ($label_all as $key => $label_each) {
		$label_id_each = explode('#**id**#', $label_each);
		$label_id = $label_id_each[0];
		array_push($label_order_ids, $label_id);
		$label_order_each = explode('#**label**#', $label_id_each[1]);
		$label_order_original[$label_id] = $label_order_each[0];
	}
	
	$submissions_row = $wpdb->get_results($wpdb->prepare("SELECT `element_label`, `element_value` FROM " . $wpdb->prefix . "formmaker_submits WHERE form_id=%d AND group_id=%d", $form_id, $subid));
	foreach ($submissions_row as $sub_row){
		$submission_array[$sub_row->element_label] = $sub_row->element_value;
	}

	foreach($label_order_original as $key => $label_each) {
		if(strpos($submit_text, "%".$label_each."%")>-1)	 {				
			$submit_text = str_replace("%".$label_each."%", $submission_array[$key], $submit_text);
		}
	}
	
	$custom_fields = array( "subid"=>$subid, "ip"=>$ip, "userid"=>$userid, "username"=>$username, "useremail"=>$useremail);	
	foreach($custom_fields as $key=>$custom_field)
	{
		if(strpos($submit_text, "%".$key."%")>-1)
			$submit_text = str_replace("%".$key."%", $custom_field, $submit_text);
	}
	$submit_text = str_replace(array("***map***", "*@@url@@*", "@@@@@@@@@", "@@@", "***grading***", "***br***", "***star_rating***"), array(" ", "", " ", " ", " ", ", ", " "), $submit_text);
	
    return $submit_text;
  }
  
  public function increment_views_count($id) {
    global $wpdb;
    $vives_form = $wpdb->get_var($wpdb->prepare("SELECT views FROM " . $wpdb->prefix . "formmaker_views WHERE form_id=%d", $id));
    if (isset($vives_form)) {
    $vives_form = $vives_form + 1;
    $wpdb->update($wpdb->prefix . "formmaker_views", array(
        'views' => $vives_form,
      ), array('form_id' => $id), array(
        '%d',
      ), array('%d'));
    }
    else {
      $wpdb->insert($wpdb->prefix . 'formmaker_views', array(
        'form_id' => $id,
        'views' => 1
        ), array(
          '%d',
          '%d'
      ));
    }
  }

	public function gen_mail($counter, $all_files, $id, $str) {
            // checking save uploads option
            global $wpdb;           
            $save_uploads = $wpdb->get_var("SELECT save_uploads FROM " . $wpdb->prefix ."formmaker WHERE id=" . $id);
            if($save_uploads == 0){
                $destination = 'wp-content/uploads/tmpAddon';
                if(!file_exists($destination))
                    mkdir($destination , 0777);
            
                foreach($all_files as &$all_file){
                    $fileTemp = $all_file['tmp_name'];
                    $fileName = $all_file['name'];
                    if(!move_uploaded_file($fileTemp, ABSPATH . $destination . '/' . $fileName)) {	
                        echo "<script> alert('" . addslashes(__('Error, file cannot be moved.', 'form_maker')) . "');</script>";
                        return array($max+1);
                    }
                    
                    $all_file['tmp_name'] = $destination . "/" . $fileName;
                }
            }
		$ip = $_SERVER['REMOTE_ADDR'];
		$replyto = '';
		global $wpdb;
		$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "formmaker WHERE id=%d", $id));
		if (!$row->form_front) {
			$id = '';
		}

		$custom_fields = array('ip', 'useremail', 'username', 'subid', 'all' );
		$subid = $wpdb->get_var("SELECT MAX( group_id ) FROM " . $wpdb->prefix ."formmaker_submits" );
		
		$current_user =  wp_get_current_user();
		if ($current_user->ID != 0)
		{
			$username =  $current_user->display_name;
			$useremail =  $current_user->user_email;
		}
		else
		{
			$username = '';
			$useremail = '';
		}
		
		$label_order_original = array();
		$label_order_ids = array();
		$label_label = array();
		$label_type = array();
		$total = 0;
		$form_currency = '$';
		$currency_code = array('USD', 'EUR', 'GBP', 'JPY', 'CAD', 'MXN', 'HKD', 'HUF', 'NOK', 'NZD', 'SGD', 'SEK', 'PLN', 'AUD', 'DKK', 'CHF', 'CZK', 'ILS', 'BRL', 'TWD', 'MYR', 'PHP', 'THB');
		$currency_sign = array('$', '&#8364;', '&#163;', '&#165;', 'C$', 'Mex$', 'HK$', 'Ft', 'kr', 'NZ$', 'S$', 'kr', 'zl', 'A$', 'kr', 'CHF', 'Kc', '&#8362;', 'R$', 'NT$', 'RM', '&#8369;', '&#xe3f;');
		if ($row->payment_currency) {
		  $form_currency = $currency_sign[array_search($row->payment_currency, $currency_code)];
		}

		$old = false;		
		if(isset($row->form)) {
			$old = true;
		}

		$cc = array();
		$row_mail_one_time = 1;
		$label_type = array();
			
		if($old == false || ($old == true && $row->form == '')) {
			$label_all	= explode('#****#',$row->label_order_current);
		}
		else {
			$label_all	= explode('#****#',$row->label_order);    
		}
		$label_all = array_slice($label_all, 0, count($label_all) - 1);
		foreach ($label_all as $key => $label_each) {
			$label_id_each = explode('#**id**#', $label_each);
			$label_id = $label_id_each[0];
			array_push($label_order_ids, $label_id);
			$label_order_each = explode('#**label**#', $label_id_each[1]);
			$label_order_original[$label_id] = $label_order_each[0];
			$label_type[$label_id] = $label_order_each[1];
			array_push($label_label, $label_order_each[0]);
			array_push($label_type, $label_order_each[1]);
		}

		$disabled_fields = explode(',', isset($_REQUEST["disabled_fields".$id]) ? $_REQUEST["disabled_fields".$id] : "");
		$disabled_fields = array_slice($disabled_fields,0, count($disabled_fields)-1);   
		
		$list='<table border="1" cellpadding="3" cellspacing="0" style="width:600px;">';
		$list_text_mode = '';
		if($old == false || ($old == true && $row->form == '')) {
			foreach($label_order_ids as $key => $label_order_id) {
				$i = $label_order_id;
				$type = $label_type[$i];

				if($type != "type_map" and  $type != "type_submit_reset" and  $type != "type_editor" and  $type != "type_captcha" and $type != "type_arithmetic_captcha" and  $type != "type_recaptcha" and  $type != "type_button") {	
					$element_label=$label_order_original[$i];
					if(!in_array($i,$disabled_fields)) {
						switch ($type) {
							case 'type_text':
							case 'type_password':
							case 'type_textarea':
							case "type_date":
							case "type_own_select":					
							case "type_country":				
							case "type_number": {
								$element = isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : NULL;
								if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
									$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td>' . $element . '</td></tr>';
									$list_text_mode=$list_text_mode.$element_label.' - '.$element."\r\n";
								}	
							
								break;
							}
							case "type_hidden": {
								$element = isset($_POST[$element_label]) ? $_POST[$element_label] : NULL;
								if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
									$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td>' . $element . '</td></tr>';
									$list_text_mode=$list_text_mode.$element_label.' - '.$element."\r\n";
								}
								break;
							}
							case "type_mark_map": {
								$element = isset($_POST['wdform_'.$i."_long".$id]) ? $_POST['wdform_'.$i."_long".$id] : NULL;
								if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
									$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td>Longitude:' . $element . '<br/>Latitude:' . (isset($_POST['wdform_'.$i."_lat".$id]) ? $_POST['wdform_'.$i."_lat".$id] : "") . '</td></tr>';
									$list_text_mode=$list_text_mode.$element_label.' - Longitude:'.$element.' Latitude:'.(isset($_POST['wdform_'.$i."_lat".$id]) ? $_POST['wdform_'.$i."_lat".$id] : "")."\r\n";
								}
								break;		
							}
							case "type_submitter_mail": {
								$element = isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : NULL;
								if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
									$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $element . '</td></tr>';
									$list_text_mode=$list_text_mode.$element_label.' - '.$element."\r\n";
								}
								break;		
							}						
							
							case "type_time": {							
								$hh = isset($_POST['wdform_'.$i."_hh".$id]) ? $_POST['wdform_'.$i."_hh".$id] : NULL;
								if(isset($hh) && ($this->empty_field($hh, $row->mail_emptyfields) || $this->empty_field($_POST['wdform_'.$i."_mm".$id], $row->mail_emptyfields) || $this->empty_field($_POST['wdform_'.$i."_ss".$id], $row->mail_emptyfields))) {
									$ss = isset($_POST['wdform_'.$i."_ss".$id]) ? $_POST['wdform_'.$i."_ss".$id] : NULL;
									if(isset($ss)) {
										$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $hh . ':' . (isset($_POST['wdform_'.$i."_mm".$id]) ? $_POST['wdform_'.$i."_mm".$id] : "") . ':' . $ss;
										$list_text_mode=$list_text_mode.$element_label.' - '.$hh.':'.(isset($_POST['wdform_'.$i."_mm".$id]) ? $_POST['wdform_'.$i."_mm".$id] : "").':'.$ss;
									}
									else {
										$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $hh . ':' . (isset($_POST['wdform_'.$i."_mm".$id]) ? $_POST['wdform_'.$i."_mm".$id] : "");
										$list_text_mode=$list_text_mode.$element_label.' - '.$hh.':'.(isset($_POST['wdform_'.$i."_mm".$id]) ? $_POST['wdform_'.$i."_mm".$id] : "");
									}
									$am_pm = isset($_POST['wdform_'.$i."_am_pm".$id]) ? $_POST['wdform_'.$i."_am_pm".$id] : NULL;
									if(isset($am_pm)) {
										$list = $list . ' ' . $am_pm . '</td></tr>';
										$list_text_mode=$list_text_mode.$am_pm."\r\n";
									}
									else {
										$list = $list.'</td></tr>';
										$list_text_mode=$list_text_mode."\r\n";
									}
								}								
								break;
							}
						  
							case "type_phone": {
								$element_first = isset($_POST['wdform_'.$i."_element_first".$id]) ? $_POST['wdform_'.$i."_element_first".$id] : NULL;
								if(isset($element_first) && $this->empty_field($element_first, $row->mail_emptyfields)) {
									$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $element_first . ' ' . (isset($_POST['wdform_'.$i."_element_last".$id]) ? $_POST['wdform_'.$i."_element_last".$id] : "") . '</td></tr>';
									$list_text_mode=$list_text_mode.$element_label.' - '.$element_first.' '.(isset($_POST['wdform_'.$i."_element_last".$id]) ? $_POST['wdform_'.$i."_element_last".$id] : "")."\r\n";
								}	
								break;
							}
						  
							case "type_name": {
								$element_first = isset($_POST['wdform_'.$i."_element_first".$id]) ? $_POST['wdform_'.$i."_element_first".$id] : NULL;
								if(isset($element_first)) {
									$element_title = isset($_POST['wdform_'.$i."_element_title".$id]) ? $_POST['wdform_'.$i."_element_title".$id] : NULL;
									$element_middle = isset($_POST['wdform_'.$i."_element_middle".$id]) ? esc_html($_POST['wdform_'.$i."_element_middle".$id]) : NULL;
									if((isset($element_title) || isset($element_middle))  && ($this->empty_field($element_title, $row->mail_emptyfields) || $this->empty_field($element_first, $row->mail_emptyfields) || $this->empty_field($_POST['wdform_'.$i."_element_last".$id], $row->mail_emptyfields) || $this->empty_field($_POST['wdform_'.$i."_element_middle".$id], $row->mail_emptyfields))) {
										$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . (isset($_POST['wdform_'.$i."_element_title".$id]) ? $_POST['wdform_'.$i."_element_title".$id] : '') . ' ' . $element_first . ' ' . (isset($_POST['wdform_'.$i."_element_last".$id]) ? $_POST['wdform_'.$i."_element_last".$id] : "") . ' ' . (isset($_POST['wdform_'.$i."_element_middle".$id]) ? $_POST['wdform_'.$i."_element_middle".$id] : "") . '</td></tr>';
										$list_text_mode=$list_text_mode.$element_label.' - '.(isset($_POST['wdform_'.$i."_element_title".$id]) ? $_POST['wdform_'.$i."_element_title".$id] : '').' '.$element_first.' '.(isset($_POST['wdform_'.$i."_element_last".$id]) ? $_POST['wdform_'.$i."_element_last".$id] : "").' '.(isset($_POST['wdform_'.$i."_element_middle".$id]) ? $_POST['wdform_'.$i."_element_middle".$id] : "")."\r\n";
									}
									else {
										if($this->empty_field($element_first, $row->mail_emptyfields) || $this->empty_field($_POST['wdform_'.$i."_element_last".$id], $row->mail_emptyfields)) {
											$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $element_first . ' ' . (isset($_POST['wdform_'.$i."_element_last".$id]) ? $_POST['wdform_'.$i."_element_last".$id] : "") . '</td></tr>';
											$list_text_mode=$list_text_mode.$element_label.' - '.$element_first.' '.(isset($_POST['wdform_'.$i."_element_last".$id]) ? $_POST['wdform_'.$i."_element_last".$id] : "")."\r\n";
										}
									}
								}	   
								break;		
							}
						  
							case "type_address": {
								$element = isset($_POST['wdform_'.$i."_street1".$id]) ? $_POST['wdform_'.$i."_street1".$id] : NULL;
								if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
									$list = $list . '<tr valign="top"><td >' . $label_order_original[$i] . '</td><td >' . $element . '</td></tr>';
									$list_text_mode=$list_text_mode.$label_order_original[$i].' - '.$element."\r\n";
									break;
								}
								$element = isset($_POST['wdform_'.$i."_street2".$id]) ? $_POST['wdform_'.$i."_street2".$id] : NULL;
								if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
									$list = $list . '<tr valign="top"><td >' . $label_order_original[$i] . '</td><td >' . $element . '</td></tr>';
									$list_text_mode=$list_text_mode.$label_order_original[$i].' - '.$element."\r\n";
									break;
								}
								$element = isset($_POST['wdform_'.$i."_city".$id]) ? $_POST['wdform_'.$i."_city".$id] : NULL;
								if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
									$list = $list . '<tr valign="top"><td >' . $label_order_original[$i] . '</td><td >' . $element . '</td></tr>';
									$list_text_mode=$list_text_mode.$label_order_original[$i].' - '.$element."\r\n";
									break;
								}
								$element = isset($_POST['wdform_'.$i."_state".$id]) ? $_POST['wdform_'.$i."_state".$id] : NULL;
								if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
									$list = $list . '<tr valign="top"><td >' . $label_order_original[$i] . '</td><td >' . $element . '</td></tr>';
									$list_text_mode=$list_text_mode.$label_order_original[$i].' - '.$element."\r\n";
									break;
								}
								$element = isset($_POST['wdform_'.$i."_postal".$id]) ? $_POST['wdform_'.$i."_postal".$id] : NULL;
								if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
									$list = $list . '<tr valign="top"><td >' . $label_order_original[$i] . '</td><td >' . $element . '</td></tr>';
									$list_text_mode=$list_text_mode.$label_order_original[$i].' - '.$element."\r\n";
									break;
								}
								$element = isset($_POST['wdform_'.$i."_country".$id]) ? $_POST['wdform_'.$i."_country".$id] : NULL;
								if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
									$list = $list . '<tr valign="top"><td >' . $label_order_original[$i] . '</td><td >' . $element . '</td></tr>';
									$list_text_mode=$list_text_mode.$label_order_original[$i].' - '.$element."\r\n";
									break;
								}
								break;							
							}
							
							case "type_date_fields": {
								$day = isset($_POST['wdform_'.$i."_day".$id]) ? $_POST['wdform_'.$i."_day".$id] : NULL;
								$month = isset($_POST['wdform_'.$i."_month".$id]) ? $_POST['wdform_'.$i."_month".$id] : "";
								$year = isset($_POST['wdform_'.$i."_year".$id]) ? $_POST['wdform_'.$i."_year".$id] : "";
								if(isset($day) && $this->empty_field($day, $row->mail_emptyfields)) {
									$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' .(($day || $month || $year) ? $day . '-' . $month . '-' . $year : '' ). '</td></tr>';
									$list_text_mode=$list_text_mode.$element_label.(($day || $month || $year) ? $day.'-'.$month.'-'.$year : '')."\r\n";
								}
								break;
							}
							
							case "type_radio": {
								$element = isset($_POST['wdform_'.$i."_other_input".$id]) ? $_POST['wdform_'.$i."_other_input".$id] : NULL;
								if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
									$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $element . '</td></tr>';
									$list_text_mode=$list_text_mode.$element_label.' - '.$element."\r\n";
									break;
								}								
								$element = isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : NULL;
								if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
									$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $element . '</td></tr>';
									$list_text_mode=$list_text_mode.$element_label.' - '.$element."\r\n";
								}
								break;	
							}	
							
							case "type_checkbox": {
								$start = -1;
								for($j = 0; $j < 100; $j++) {
									$element = isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL;
									if(isset($element)) {
										$start = $j;
										break;
									}
								}								
								$other_element_id = -1;
								$is_other = isset($_POST['wdform_'.$i."_allow_other".$id]) ? $_POST['wdform_'.$i."_allow_other".$id] : "";
								if($is_other == "yes") {
									$other_element_id = isset($_POST['wdform_'.$i."_allow_other_num".$id]) ? $_POST['wdform_'.$i."_allow_other_num".$id] : "";
								}
				
								if($start != -1 || ($start == -1 && $row->mail_emptyfields))
								{
									$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >';
									$list_text_mode=$list_text_mode.$element_label.' - '; 
								}
								
								if($start != -1) {
									for($j = $start; $j < 100; $j++) {									
										$element = isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL;
										if(isset($element)) {
											if($j == $other_element_id) {
												$list = $list . (isset($_POST['wdform_'.$i."_other_input".$id]) ? $_POST['wdform_'.$i."_other_input".$id] : "") . '<br>';
												$list_text_mode=$list_text_mode.(isset($_POST['wdform_'.$i."_other_input".$id]) ? $_POST['wdform_'.$i."_other_input".$id] : "").', ';	
											}
											else {									
												$list = $list . (isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : "") . '<br>';
												$list_text_mode=$list_text_mode.(isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : "").', ';
											}
										}
									}
								}
								
								if($start != -1 || ($start == -1 && $row->mail_emptyfields))
								{
									$list = $list . '</td></tr>';
									$list_text_mode=$list_text_mode."\r\n";
								}	
								break;
							}
							
							case "type_paypal_price":	{
								$value = 0;
								if(isset($_POST['wdform_'.$i."_element_dollars".$id])) {
									$value = $_POST['wdform_'.$i."_element_dollars".$id];
								}
								if(isset($_POST['wdform_'.$i."_element_cents".$id]) && $_POST['wdform_'.$i."_element_cents".$id]) {
									$value = $value . '.' . $_POST['wdform_'.$i."_element_cents".$id];
								}
							
								if($this->empty_field($value, $row->mail_emptyfields) && $value!='.')
								{
									$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $value . $form_currency . '</td></tr>';
									$list_text_mode=$list_text_mode.$element_label.' - '.$value.$form_currency."\r\n";
								}	
								break;
							}			
					  
							case "type_paypal_select": {
								if(isset($_POST['wdform_'.$i."_element_label".$id]) && $_POST['wdform_'.$i."_element".$id] != '') {
									$value = $_POST['wdform_'.$i."_element_label".$id] . ' : ' . (isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : "") . $form_currency;
								}
								else {
									$value='';
								}
								$element_quantity_label = (isset($_POST['wdform_'.$i."_element_quantity_label".$id]) && $_POST['wdform_'.$i."_element_quantity_label".$id]) ? $_POST['wdform_'.$i."_element_quantity_label".$id] : NULL;
								$element_quantity = (isset($_POST['wdform_'.$i."_element_quantity".$id]) && $_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : NULL;
								if($value != '' && isset($element_quantity)) {
									$value .= '<br/>' . $element_quantity_label . ': ' . $element_quantity;
								}
								for($k = 0; $k < 50; $k++) {
									$temp_val = isset($_POST['wdform_'.$i."_property".$id.$k]) ? $_POST['wdform_'.$i."_property".$id.$k] : NULL;
									if(isset($temp_val)) {			
										$value .= '<br/>' . (isset($_POST['wdform_'.$i."_element_property_label".$id.$k]) ? $_POST['wdform_'.$i."_element_property_label".$id.$k] : "") . ': ' . $temp_val;
									}
								}

								if($this->empty_field($value, $row->mail_emptyfields))
								{
									$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $value . '</td></tr>';
									$list_text_mode=$list_text_mode.$element_label.' - '.str_replace('<br/>',', ',$value)."\r\n";
								}	
								break;
							}
					  
							case "type_paypal_radio": {
								if(isset($_POST['wdform_'.$i."_element".$id])) {
									$value = $_POST['wdform_'.$i."_element_label".$id] . ' : ' . (isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : "") . $form_currency;
							  
									$element_quantity_label = isset($_POST['wdform_'.$i."_element_quantity_label".$id]) ? $_POST['wdform_'.$i."_element_quantity_label".$id] : NULL;
									$element_quantity = (isset($_POST['wdform_'.$i."_element_quantity".$id]) && $_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : NULL;
									if (isset($element_quantity)) {
										$value .= '<br/>' . $element_quantity_label . ': ' . $element_quantity;
									}
									for($k = 0; $k < 50; $k++) {
										$temp_val = isset($_POST['wdform_'.$i."_property".$id.$k]) ? $_POST['wdform_'.$i."_property".$id.$k] : NULL;
										if(isset($temp_val)) {
											$value .= '<br/>' . (isset($_POST['wdform_'.$i."_element_property_label".$id.$k]) ? $_POST['wdform_'.$i."_element_property_label".$id.$k] : "") . ': ' . $temp_val;
										}
									}
								}
								else {
									$value='';
								}

								if($this->empty_field($value, $row->mail_emptyfields))		
								{
									$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $value . '</td></tr>';
									$list_text_mode=$list_text_mode.$element_label.' - '.str_replace('<br/>',', ',$value)."\r\n";
								}
								break;	
							}

							case "type_paypal_shipping": {						
								if(isset($_POST['wdform_'.$i."_element".$id])) {
									$value = $_POST['wdform_'.$i."_element_label".$id] . ' : ' . (isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : "") . $form_currency;
									
									if($this->empty_field($value, $row->mail_emptyfields))		
									{
										$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $value . '</td></tr>';
										$list_text_mode=$list_text_mode.$element_label.' - '.$value."\r\n";
									}	
								}
								else {
									$value='';
								}	
								
								break;
							}

							case "type_paypal_checkbox": {
								              
								$start = -1;
								for($j = 0; $j < 100; $j++) {
									$element = isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL;
									if(isset($element)) {
										$start=$j;
										break;
									}
								}	
							
								if($start != -1 || ($start == -1 && $row->mail_emptyfields))
								{
									$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >';		
									$list_text_mode=$list_text_mode.$element_label.' - ';  
								}
								if($start!=-1) {
									for($j = $start; $j < 100; $j++) {									
										$element = isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL;
										if(isset($element)) {
											$list = $list . (isset($_POST['wdform_'.$i."_element".$id.$j."_label"]) ? $_POST['wdform_'.$i."_element".$id.$j."_label"] : "") . ' - ' . ($element == '' ? '0' . $form_currency : $element) . $form_currency . '<br>';
											$list_text_mode=$list_text_mode.(isset($_POST['wdform_'.$i."_element".$id.$j."_label"]) ? $_POST['wdform_'.$i."_element".$id.$j."_label"] : "").' - '.($element == '' ? '0' . $form_currency : $element).$form_currency.', ';
										}
									}
								}
								$element_quantity_label = isset($_POST['wdform_'.$i."_element_quantity_label".$id]) ? $_POST['wdform_'.$i."_element_quantity_label".$id] : NULL;
								$element_quantity = (isset($_POST['wdform_'.$i."_element_quantity".$id]) && $_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : NULL;
								if (isset($element_quantity)) {
									$list = $list . '<br/>' . $element_quantity_label . ': ' . $element_quantity;
									$list_text_mode=$list_text_mode.$element_quantity_label . ': ' . $element_quantity.', ';		
								}
								for($k = 0; $k < 50; $k++) {
									$temp_val = isset($_POST['wdform_'.$i."_element_property_value".$id.$k]) ? $_POST['wdform_'.$i."_element_property_value".$id.$k] : NULL;
									if(isset($temp_val)) {			
										$list = $list . '<br/>' . (isset($_POST['wdform_'.$i."_element_property_label".$id.$k]) ? $_POST['wdform_'.$i."_element_property_label".$id.$k] : "") . ': ' . $temp_val;
										$list_text_mode=$list_text_mode.(isset($_POST['wdform_'.$i."_element_property_label".$id.$k]) ? $_POST['wdform_'.$i."_element_property_label".$id.$k] : "") . ': ' . $temp_val.', ';	
									}
								}
								if($start != -1 || ($start == -1 && $row->mail_emptyfields))
								{
									$list = $list . '</td></tr>';
									$list_text_mode=$list_text_mode."\r\n";	
								}
								break;
							}
						  
							case "type_paypal_total": {
								$element = isset($_POST['wdform_'.$i."_paypal_total".$id]) ? $_POST['wdform_'.$i."_paypal_total".$id] : "";
								if($this->empty_field($element, $row->mail_emptyfields))		
								{
									$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $element . '</td></tr>';
									$list_text_mode=$list_text_mode.$element_label.' - '.$element."\r\n";
								}	
								break;
							}
						
							case "type_star_rating": {
								$element = isset($_POST['wdform_'.$i."_star_amount".$id]) ? $_POST['wdform_'.$i."_star_amount".$id] : NULL;
								$selected = isset($_POST['wdform_'.$i."_selected_star_amount".$id]) ? $_POST['wdform_'.$i."_selected_star_amount".$id] : 0;
								if(isset($element) && $this->empty_field($selected, $row->mail_emptyfields)) {
									$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $selected . '/' . $element . '</td></tr>';
									$list_text_mode=$list_text_mode.$element_label.' - '.$selected.'/'.$element."\r\n";
								}
								break;
							}
						  
							case "type_scale_rating": {
								$element = isset($_POST['wdform_'.$i."_scale_amount".$id]) ? $_POST['wdform_'.$i."_scale_amount".$id] : NULL;
								$selected = isset($_POST['wdform_'.$i."_scale_radio".$id]) ? $_POST['wdform_'.$i."_scale_radio".$id] : 0;
								if(isset($element) && $this->empty_field($selected, $row->mail_emptyfields)) {
									$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $selected . '/' . $element . '</td></tr>';	
									$list_text_mode=$list_text_mode.$element_label.' - '.$selected.'/'.$element."\r\n";
								}
								break;
							}
						  
							case "type_spinner": {
								$element = isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : NULL;
								if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
									$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $element . '</td></tr>';
									$list_text_mode=$list_text_mode.$element_label.' - '.$element."\r\n";
								}
								break;
							}
						  
							case "type_slider": {
								$element = isset($_POST['wdform_'.$i."_slider_value".$id]) ? $_POST['wdform_'.$i."_slider_value".$id] : NULL;
								if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
									$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $element . '</td></tr>';
									$list_text_mode=$list_text_mode.$element_label.' - '.$element."\r\n";
								}
								break;
							}
						  
							case "type_range": {
								$element0 = isset($_POST['wdform_'.$i."_element".$id.'0']) ? $_POST['wdform_'.$i."_element".$id.'0'] : NULL;
								$element1 = isset($_POST['wdform_'.$i."_element".$id.'1']) ? $_POST['wdform_'.$i."_element".$id.'1'] : NULL;
								if((isset($element0) && $this->empty_field($element0, $row->mail_emptyfields)) || (isset($element1) && $this->empty_field($element1, $row->mail_emptyfields))) {
									$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >From:' . $element0 . '<span style="margin-left:6px">To</span>:' . $element1 . '</td></tr>';					
									$list_text_mode=$list_text_mode.$element_label.' - From:'.$element0.' To:'.$element1."\r\n";
								}
								break;
							}
						  
							case "type_grading": {
								$element = isset($_POST['wdform_'.$i."_hidden_item".$id]) ? $_POST['wdform_'.$i."_hidden_item".$id] : "";
								$grading = explode(":", $element);
								$items_count = sizeof($grading) - 1;							
								$element = "";
								$total = "";	
								$form_empty_field = 1;
								for($k = 0;$k < $items_count; $k++) {
									$element .= $grading[$k] . ":" . (isset($_POST['wdform_'.$i."_element".$id.'_'.$k]) ? $_POST['wdform_'.$i."_element".$id.'_'.$k] : "") . " ";
									$total += (isset($_POST['wdform_'.$i."_element".$id.'_'.$k]) ? $_POST['wdform_'.$i."_element".$id.'_'.$k] : 0);
									if(isset($_POST['wdform_'.$i."_element".$id.'_'.$k]))
										$form_empty_field = 0;
								}
								$element .= "Total:" . $total;
								if(isset($element) && $this->empty_field($form_empty_field, $row->mail_emptyfields)) {
									$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $element . '</td></tr>';
									$list_text_mode=$list_text_mode.$element_label.' - '.$element."\r\n";
								}
								break;
							}
						
							case "type_matrix": {
								$input_type = isset($_POST['wdform_'.$i."_input_type".$id]) ? $_POST['wdform_'.$i."_input_type".$id] : "";
								$mat_rows = explode("***", isset($_POST['wdform_'.$i."_hidden_row".$id]) ? $_POST['wdform_'.$i."_hidden_row".$id] : "");
								$rows_count = sizeof($mat_rows) - 1;
								$mat_columns = explode("***", isset($_POST['wdform_'.$i."_hidden_column".$id]) ? $_POST['wdform_'.$i."_hidden_column".$id] : "");
								$columns_count = sizeof($mat_columns) - 1;
								$matrix = "<table>";
								$matrix .= '<tr><td></td>';							
								for($k = 1; $k < count($mat_columns); $k++) {
									$matrix .= '<td style="background-color:#BBBBBB; padding:5px; ">' . $mat_columns[$k] . '</td>';
								}
								$matrix .= '</tr>';							
								$aaa = Array();							
								for($k = 1; $k <= $rows_count; $k++) {
									$matrix .= '<tr><td style="background-color:#BBBBBB; padding:5px;">' . $mat_rows[$k] . '</td>';
									if($input_type == "radio") {
										$mat_radio = isset($_POST['wdform_'.$i."_input_element".$id.$k]) ? $_POST['wdform_'.$i."_input_element".$id.$k] : 0;
										if($mat_radio == 0) {
											$checked = "";
											$aaa[1] = "";
										}
										else {
											$aaa = explode("_", $mat_radio);
										}
										for($j = 1; $j <= $columns_count; $j++) {
											if($aaa[1] == $j) {
												$checked = "checked";
											}
											else {
												$checked = "";
											}
											$matrix .= '<td style="text-align:center"><input  type="radio" ' . $checked . ' disabled /></td>';
										}
									}
									else {
										if($input_type == "checkbox") {                
											for($j = 1; $j <= $columns_count; $j++) {
												$checked = isset($_POST['wdform_'.$i."_input_element".$id.$k.'_'.$j]) ? $_POST['wdform_'.$i."_input_element".$id.$k.'_'.$j] : "";
												if($checked == 1) {
													$checked = "checked";
												}
												else {
													$checked = "";
												}
												$matrix .= '<td style="text-align:center"><input  type="checkbox" ' . $checked . ' disabled /></td>';									
											}								
										}
										else {
											if($input_type == "text") {																  
												for($j = 1; $j <= $columns_count; $j++) {
													$checked = isset($_POST['wdform_'.$i."_input_element".$id.$k.'_'.$j]) ? $_POST['wdform_'.$i."_input_element".$id.$k.'_'.$j] : "";
													$matrix .= '<td style="text-align:center"><input  type="text" value="' . $checked . '" disabled /></td>';								
												}										
											}
											else {
												for($j = 1; $j <= $columns_count; $j++) {
													$checked = isset($_POST['wdform_'.$i."_select_yes_no".$id.$k.'_'.$j]) ? $_POST['wdform_'.$i."_select_yes_no".$id.$k.'_'.$j] : "";
													$matrix .= '<td style="text-align:center">' . $checked . '</td>';
												}
											}									
										}									
									}
									$matrix .= '</tr>';							
								}
								$matrix .= '</table>';	
								if(isset($matrix)) {
									$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $matrix . '</td></tr>';
								}						
								break;
							}
							default: break;
						}
					}
				}				
			}

			$list = $list . '</table>';
			if($row->sendemail) {
				$fromname = $row->mail_from_name_user;        
				if($row->mail_subject_user)	
					$subject 	= $row->mail_subject_user;
				else
					$subject 	= $row->title;
				if($row->reply_to_user) {
					$replyto = $row->reply_to_user;
				}
				$attachment_user = array(); 	
				if ($row->mail_attachment_user) {
					for ($k = 0; $k < count($all_files); $k++) {
						if (isset($all_files[$k]['tmp_name'])) {
							$attachment_user[$k] = $all_files[$k]['tmp_name'];
						}
					}
				}
					
				if ($row->mail_mode_user) {
					$content_type = "text/html";
					$mode = 1;
					$list_user = wordwrap($list, 70, "\n", true);
					$new_script = wpautop($row->script_mail_user);
				}	
				else {
					$content_type = "text/plain";
					$mode = 0; 
					$list_user = wordwrap($list_text_mode, 1000, "\n", true);
					$new_script = str_replace(array('<p>','</p>'),'',$row->script_mail_user);
				}
				
				foreach($label_order_original as $key => $label_each) {
					$type=$label_type[$key];
					if(strpos($row->script_mail_user, "%".$label_each."%")>-1)	 {
						$new_value = $this->custom_fields_mail($type, $key, $id, $attachment_user, '');				
						$new_script = str_replace("%".$label_each."%", $new_value, $new_script);
					}
					
					if(strpos($fromname, "%".$label_each."%")>-1) {	
						$new_value = str_replace('<br>',', ',$this->custom_fields_mail($type, $key, $id, '', ''));		
						if(substr($new_value, -2)==', ') {
							$new_value = substr($new_value, 0, -2);
						}
						$fromname = str_replace("%".$label_each."%", $new_value, $fromname);							
					}	
					
					if(strpos($subject, "%".$label_each."%")>-1) {	
						$new_value = str_replace('<br>',', ',$this->custom_fields_mail($type, $key, $id, '', ''));		
						if(substr($new_value, -2)==', ') {
							$new_value = substr($new_value, 0, -2);		
						}
						$subject = str_replace("%".$label_each."%", $new_value, $subject);							
					}
				}

				$recipient = '';
				$cca = $row->mail_cc_user;
				$bcc = $row->mail_bcc_user;
				if ($row->mail_from_user != '') {
					if ($fromname != '') {
						$from = "From: '" . $fromname . "' <" . $row->mail_from_user . ">" . "\r\n";
					}	
					else {
						$from = "From: '' <" . $row->mail_from_user . ">" . "\r\n";
					}
				}
				else {
					$from = '';
				}
					
				$headers =  $from . " Content-Type: " . $content_type . "; charset=\"" . get_option('blog_charset') . "\"\n";
				if ($replyto) {
					$headers .= "Reply-To: <" . $replyto . ">\r\n";
				}
				if ($cca) {
					$headers .= "Cc: <" . $cca . ">\r\n";          
				}
				if ($bcc) {
					$headers .= "Bcc: <" . $bcc . ">\r\n";          
				}

				$custom_fields_value = array( $ip, $useremail, $username, $subid, $list_user );	
				foreach($custom_fields as $key=>$custom_field)
				{
					if(strpos($new_script, "%".$custom_field."%")>-1)
					$new_script = str_replace("%".$custom_field."%", $custom_fields_value[$key], $new_script);

					if($key==2 || $key==3)
					{
						if(strpos($fromname, "%".$custom_field."%")>-1)
							$fromname = str_replace("%".$custom_field."%", $custom_fields_value[$key], $fromname);
							
						if(strpos($subject, "%".$custom_field."%")>-1)
							$subject = str_replace("%".$custom_field."%", $custom_fields_value[$key], $subject);
					}
				}
				$body = $new_script;
				$GLOBALS['attachment_user'] = array();
				$GLOBALS['attachment'] = array();
				if (defined('WD_FM_PDF') && is_plugin_active(constant('WD_FM_PDF'))) {
					$_GET['addon_task'] = 'frontend';
					$_GET['form_id'] = $id;
					$_GET['form_currency'] = $form_currency;
					$GLOBALS['custom_fields_value'] = $custom_fields_value;
					do_action('WD_FM_PDF_init');
				}
				if(!empty($GLOBALS['attachment_user']))
					array_push($attachment_user, $GLOBALS['attachment_user']);
				
				if($row->send_to) {
					$send_tos = explode('**',$row->send_to);
					$send_copy = isset($_POST["wdform_send_copy_".$id]) ? $_POST["wdform_send_copy_".$id] : NULL;
					if(isset($send_copy)) {
						$send=true;
					}
					else {
						$mail_verification_post_id = (int)$wpdb->get_var($wpdb->prepare('SELECT mail_verification_post_id FROM ' . $wpdb->prefix . 'formmaker WHERE id="%d"', $id));
						$verification_link = get_post( $mail_verification_post_id );
						foreach($send_tos as $index => $send_to) {
							$recipient = isset($_POST['wdform_'.str_replace('*', '', $send_to)."_element".$id]) ? $_POST['wdform_'.str_replace('*', '', $send_to)."_element".$id] : NULL;
							if(strpos($new_script, "%Verification link%")>-1 && $verification_link !== NULL) {
								$ver_link = $row->mail_mode_user ? "<a href =".add_query_arg(array('gid' => $_SESSION['gid'], 'h' => $_SESSION['hash'][$index].'@'.str_replace("*", "", $send_to)), get_post_permalink($mail_verification_post_id)).">".add_query_arg(array('gid' => $_SESSION['gid'], 'h' => $_SESSION['hash'][$index].'@'.str_replace("*", "", $send_to)), get_post_permalink($mail_verification_post_id))."</a><br/>" : add_query_arg(array('gid' => $_SESSION['gid'], 'h' => $_SESSION['hash'][$index].'@'.str_replace("*", "", $send_to)), get_post_permalink($mail_verification_post_id));
								
								$body = $row->mail_verify ? str_replace("%Verification link%", $ver_link, $new_script) : str_replace("%Verification link%", '', $new_script);
							}
							
							if($recipient) {
								$send = wp_mail(str_replace(' ', '', $recipient), $subject, stripslashes($body), $headers, $attachment_user);
							}
						}
					}
				}
			}
			
			if($row->sendemail) {
				if($row->reply_to) {
					$replyto = isset($_POST['wdform_'.$row->reply_to."_element".$id]) ? $_POST['wdform_'.$row->reply_to."_element".$id] : NULL;
					if(!isset($replyto)) {
						$replyto = $row->reply_to;
					}
				}
				$recipient = $row->mail;
				if($row->mail_subject) {
					$subject 	= $row->mail_subject;
				}
				else {
					$subject 	= $row->title;
				}
		
				if ($row->from_name) {
					$fromname = $row->from_name;
				}
				else {
					$fromname = '';
				}
				$attachment = array(); 
				if ($row->mail_attachment) {
					for ($k = 0; $k < count($all_files); $k++) {
						if (isset($all_files[$k]['tmp_name'])) {
						$attachment[$k] = $all_files[$k]['tmp_name'];
						}
					}
				}
				if(!empty($GLOBALS['attachment']))
					array_push($attachment, $GLOBALS['attachment']);	

					
				if ($row->mail_mode) {
					$content_type = "text/html";
					$mode = 1; 
					$list = wordwrap($list, 70, "\n", true);
					$new_script = wpautop($row->script_mail);
				}	
				else {
					$content_type = "text/plain";
					$mode = 0; 
					$list = $list_text_mode;
					$list = wordwrap($list, 1000, "\n", true);
					$new_script = str_replace(array('<p>','</p>'),'',$row->script_mail);
				}
					
				foreach($label_order_original as $key => $label_each) {							
					$type=$label_type[$key];
					if(strpos($row->script_mail, "%".$label_each."%")>-1) {
						$new_value = $this->custom_fields_mail($type, $key, $id, $attachment, '');				
						$new_script = str_replace("%".$label_each."%", $new_value, $new_script);							
					}
		
					if(strpos($fromname, "%".$label_each."%")>-1) {
						$new_value = str_replace('<br>',', ',$this->custom_fields_mail($type, $key, $id, '', ''));		
						if(substr($new_value, -2)==', ') {
							$new_value = substr($new_value, 0, -2);
						}
						$fromname = str_replace("%".$label_each."%", $new_value, $fromname);							
					}
					
					if(strpos($fromname, "%username%")>-1){
						$fromname = str_replace("%username%", $username, $fromname);
					}
		  
					if(strpos($subject, "%".$label_each."%")>-1) {
						$new_value = str_replace('<br>',', ',$this->custom_fields_mail($type, $key, $id, '', ''));		
						if(substr($new_value, -2)==', ') {
							$new_value = substr($new_value, 0, -2);				
						}
						$subject = str_replace("%".$label_each."%", $new_value, $subject);							
					}
				}
			
				if ($row->from_mail) {
					$from = isset($_POST['wdform_'.$row->from_mail."_element".$id]) ? $_POST['wdform_'.$row->from_mail."_element".$id] : NULL;
					if (!isset($from)) {
						$from = $row->from_mail;
					}
					$from = "From: '" . $fromname . "' <" . $from . ">" . "\r\n";
				}
				else {
					$from = "";
				}
					
				$headers =  $from . " Content-Type: " . $content_type . "; charset=\"" . get_option('blog_charset') . "\"\n";
				if ($replyto) {
					$headers .= "Reply-To: <" . $replyto . ">\r\n";
				}
				$cca = $row->mail_cc;
				$bcc = $row->mail_bcc;
				if ($cca) {
					$headers .= "Cc: <" . $cca . ">\r\n";          
				}
				if ($bcc) {
					$headers .= "Bcc: <" . $bcc . ">\r\n";          
				}
			
				$custom_fields_value = array( $ip, $useremail, $username, $subid, $list );	
				foreach($custom_fields as $key=>$custom_field)
				{
					if(strpos($new_script, "%".$custom_field."%")>-1)
					$new_script = str_replace("%".$custom_field."%", $custom_fields_value[$key], $new_script);

					if($key==2 || $key==3)
					{
						if(strpos($fromname, "%".$custom_field."%")>-1)
							$fromname = str_replace("%".$custom_field."%", $custom_fields_value[$key], $fromname);
							
						if(strpos($subject, "%".$custom_field."%")>-1)
							$subject = str_replace("%".$custom_field."%", $custom_fields_value[$key], $subject);
					}
				}
				$admin_body = $new_script;
				if($recipient) {
					$send = wp_mail(str_replace(' ', '', $recipient), $subject, stripslashes($admin_body), $headers, $attachment);
				}
			}
			
			$_SESSION['error_or_no' . $id] = 0;
			$msg = addslashes(__('Your form was successfully submitted.', 'form_maker'));
			$succes = 1;

			if($row->sendemail)
				if($row->mail || $row->send_to) {
					if ($send) {
						if ($send !== true ) {
							$_SESSION['error_or_no' . $id] = 1;
							$msg = addslashes(__('Error, email was not sent.', 'form_maker'));
							$succes = 0;
						}
						else {
							$_SESSION['error_or_no' . $id] = 0;
							$msg = addslashes(__('Your form was successfully submitted.', 'form_maker'));
						}
					}
				}
				
			$fm_email_params = $row->sendemail ? array('admin_body' => $admin_body, 'body' => $body, 'subject' => $subject, 'headers' => $headers, 'attachment' => $attachment, 'attachment_user' => $attachment_user) : array();
                       
			$addons = array('WD_FM_EMAIL_COND' => 'Email Conditions');
			$addons_array = array();
			foreach($addons as $addon => $addon_name) {	
				if (defined($addon) && is_plugin_active(constant($addon))) {
					$_GET['addon_task'] = 'frontend';
					$_GET['form_id'] = $id;
					$GLOBALS['fm_email_params'] = $fm_email_params;
					$GLOBALS['form_currency'] = $form_currency;
					$GLOBALS['custom_fields_value'] = isset($custom_fields_value) ? $custom_fields_value : array();
					do_action($addon.'_init');
				}				
			}	
		}
		else { /* Old form.*/
			foreach ($label_order_ids as $key => $label_order_id) {
				$i = $label_order_id;
				$type = $_POST[$i . "_type" . $id];
				if (isset($_POST[$i . "_type" . $id]))
					if ($type != "type_map" and  $type != "type_submit_reset" and  $type != "type_editor" and  $type != "type_captcha" and $type != "type_arithmetic_captcha" and  $type != "type_recaptcha" and  $type != "type_button") {
						$element_label = $label_order_original[$i];
						switch ($type) {
							case 'type_text':
							case 'type_password':
							case 'type_textarea':
							case "type_date":
							case "type_own_select":
							case "type_country":
							case "type_number":
							{
								$element = $_POST[$i . "_element" . $id];
								if (isset($_POST[$i . "_element" . $id])) {
									$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td ><pre style="margin:0px; padding:0px">' . $element . '</pre></td></tr>';
								}
								break;
							}
							case "type_hidden": {
								$element = $_POST[$element_label];
								if (isset($element)) {
									$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td ><pre style="margin:0px; padding:0px">' . $element . '</pre></td></tr>';
								}
								break;
							}
							case "type_submitter_mail":
							{
								$element = $_POST[$i . "_element" . $id];
								if (isset($_POST[$i . "_element" . $id])) {
									$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td ><pre style="margin:0px; padding:0px">' . $element . '</pre></td></tr>';
									if ($_POST[$i . "_send" . $id] == "yes")
										array_push($cc, $element);
								}
								break;
							}
							case "type_time":
							{
								$hh = $_POST[$i . "_hh" . $id];
								if (isset($_POST[$i . "_hh" . $id])) {
									$ss = $_POST[$i . "_ss" . $id];
									if (isset($_POST[$i . "_ss" . $id]))
										$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $_POST[$i . "_hh" . $id] . ':' . $_POST[$i . "_mm" . $id] . ':' . $_POST[$i . "_ss" . $id];
									else
										$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $_POST[$i . "_hh" . $id] . ':' . $_POST[$i . "_mm" . $id];
									$am_pm = $_POST[$i . "_am_pm" . $id];
									if (isset($_POST[$i . "_am_pm" . $id]))
										$list = $list . ' ' . $_POST[$i . "_am_pm" . $id] . '</td></tr>';
									else
										$list = $list . '</td></tr>';
								}
								break;
							}
							case "type_phone":
							{
								$element_first = $_POST[$i . "_element_first" . $id];
								if (isset($_POST[$i . "_element_first" . $id])) {
									$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $_POST[$i . "_element_first" . $id] . ' ' . $_POST[$i . "_element_last" . $id] . '</td></tr>';
								}
								break;
							}
							case "type_name":
							{
								$element_first = $_POST[$i . "_element_first" . $id];
								if (isset($_POST[$i . "_element_first" . $id])) {
									$element_title = $_POST[$i . "_element_title" . $id];
									if (isset($_POST[$i . "_element_title" . $id]))
										$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $_POST[$i . "_element_title" . $id] . ' ' . $_POST[$i . "_element_first" . $id] . ' ' . $_POST[$i . "_element_last" . $id] . ' ' . $_POST[$i . "_element_middle" . $id] . '</td></tr>';
									else
										$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $_POST[$i . "_element_first" . $id] . ' ' . $_POST[$i . "_element_last" . $id] . '</td></tr>';
								}
								break;
							}
							case "type_mark_map":
							{
								if (isset($_POST[$i . "_long" . $id])) {
									$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >Longitude:' . $_POST[$i . "_long" . $id] . '<br/>Latitude:' . $_POST[$i . "_lat" . $id] . '</td></tr>';
								}
								break;
							}
							case "type_address":
							{
								if (isset($_POST[$i . "_street1" . $id]))
									$list = $list . '<tr valign="top"><td >' . $label_order_original[$i] . '</td><td >' . $_POST[$i . "_street1" . $id] . '</td></tr>';
								$i++;
								if (isset($_POST[$i."_street2".$id]))
									$list = $list . '<tr valign="top"><td >' . $label_order_original[$i] . '</td><td >' . $_POST[$i . "_street2" . $id] . '</td></tr>';
								$i++;
								if (isset($_POST[$i."_city".$id]))
									$list = $list . '<tr valign="top"><td >' . $label_order_original[$i] . '</td><td >' . $_POST[$i . "_city" . $id] . '</td></tr>';
								$i++;
								if (isset($_POST[$i."_state".$id]))
									$list = $list . '<tr valign="top"><td >' . $label_order_original[$i] . '</td><td >' . $_POST[$i . "_state" . $id] . '</td></tr>';
								$i++;
								if (isset($_POST[$i."_postal".$id]))
									$list = $list . '<tr valign="top"><td >' . $label_order_original[$i] . '</td><td >' . $_POST[$i . "_postal" . $id] . '</td></tr>';
								$i++;
								if (isset($_POST[$i."_country".$id]))
									$list = $list . '<tr valign="top"><td >' . $label_order_original[$i] . '</td><td >' . $_POST[$i . "_country" . $id] . '</td></tr>';
								$i++;
							break;
						}
						case "type_date_fields":
						{
							$day = $_POST[$i . "_day" . $id];
							if (isset($_POST[$i . "_day" . $id])) {
								$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $_POST[$i . "_day" . $id] . '-' . $_POST[$i . "_month" . $id] . '-' . $_POST[$i . "_year" . $id] . '</td></tr>';
							}
							break;
						}
						case "type_radio":
						{
							$element = $_POST[$i . "_other_input" . $id];
							if (isset($_POST[$i . "_other_input" . $id])) {
								$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $_POST[$i . "_other_input" . $id] . '</td></tr>';
								break;
							}
							$element = $_POST[$i . "_element" . $id];
							if (isset($_POST[$i . "_element" . $id])) {
								$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td ><pre style="margin:0px; padding:0px">' . $element . '</pre></td></tr>';
							}
							break;
						}
						case "type_checkbox":
						{
							$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >';
							$start = -1;
							for ($j = 0; $j < 100; $j++) {
								if (isset($_POST[$i . "_element" . $id . $j])) {
									$start = $j;
									break;
								}
							}
							$other_element_id = -1;
							$is_other = $_POST[$i . "_allow_other" . $id];
							if ($is_other == "yes") {
								$other_element_id = $_POST[$i . "_allow_other_num" . $id];
							}
							if ($start != -1) {
								for ($j = $start; $j < 100; $j++) {
									$element = $_POST[$i . "_element" . $id . $j];
									if (isset($_POST[$i . "_element" . $id . $j]))
										if ($j == $other_element_id) {
											$list = $list . $_POST[$i . "_other_input" . $id] . '<br>';
										}
										else
											$list = $list . $_POST[$i . "_element" . $id . $j] . '<br>';
								}
								$list = $list . '</td></tr>';
							}
							break;
						}
						case "type_paypal_price":	 {		
							$value = 0;
							if ($_POST[$i."_element_dollars".$id]) {
								$value = $_POST[$i."_element_dollars".$id];
							}
							if ($_POST[$i."_element_cents".$id]) {
								$value = $value.'.'.$_POST[$i."_element_cents".$id];
							}
							$list = $list.'<tr valign="top"><td >'.$element_label.'</td><td >'.$value.$form_currency.'</td></tr>';
							break;
						}
						case "type_paypal_select": {
							$value = $_POST[$i."_element_label".$id].':'.$_POST[$i."_element".$id].$form_currency;
							$element_quantity_label = $_POST[$i."_element_quantity_label".$id];
							if (isset($element_quantity_label)) {
								$quantity = ((isset($_POST[$i . "_element_quantity" . $id]) && ($_POST[$i . "_element_quantity" . $id] >= 1)) ? $_POST[$i . "_element_quantity" . $id] : 1);
								$value .= '<br/>'.$_POST[$i."_element_quantity_label".$id].': '.$quantity;
							}
							for ($k = 0; $k < 50; $k++) {
								$temp_val = $_POST[$i."_element_property_value".$id.$k];
								if (isset($temp_val)) {			
									$value .= '<br/>'.$_POST[$i."_element_property_label".$id.$k].': '.$_POST[$i."_element_property_value".$id.$k];
								}
							}
							$list = $list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="margin:0px; padding:0px">'.$value.'</pre></td></tr>';					
							break;
						}
						case "type_paypal_radio": {
							$value = $_POST[$i."_element_label".$id].' - '.$_POST[$i."_element".$id].$form_currency;
							$element_quantity_label = $_POST[$i."_element_quantity_label".$id];
							if (isset($element_quantity_label)) {
								$quantity = ((isset($_POST[$i . "_element_quantity" . $id]) && ($_POST[$i . 	"_element_quantity" . $id] >= 1)) ? $_POST[$i . "_element_quantity" . $id] : 1);
								$value .= '<br/>' . $_POST[$i."_element_quantity_label".$id] . ': ' . $quantity;
							}
							for ($k = 0; $k < 50; $k++) {
								$temp_val = $_POST[$i."_element_property_value".$id.$k];
								if (isset($temp_val)) {			
									$value .= '<br/>'.$_POST[$i."_element_property_label".$id.$k].': '.$_POST[$i."_element_property_value".$id.$k];
								}
							}
							$list = $list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="margin:0px; padding:0px">'.$value.'</pre></td></tr>';				
							break;	
						}
						case "type_paypal_shipping": {
							$value = $_POST[$i."_element_label".$id].' - '.$_POST[$i."_element".$id].$form_currency;		
							$list = $list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="margin:0px; padding:0px">'.$value.'</pre></td></tr>';				
							break;
						}
						case "type_paypal_checkbox": {
							$list = $list.'<tr valign="top"><td >'.$element_label.'</td><td >';
							$start = -1;
							for ($j = 0; $j < 100; $j++) {
								$element = $_POST[$i."_element".$id.$j];
								if (isset($element)) {
									$start = $j;
									break;
								}
							}
							if ($start != -1) {
								for ($j = $start; $j < 100; $j++) {
									$element = $_POST[$i."_element".$id.$j];
									if (isset($element)) {
										$list = $list.$_POST[$i."_element".$id.$j."_label"].' - '.($_POST[$i."_element".$id.$j]=='' ? '0'.$form_currency : $_POST[$i."_element".$id.$j]).$form_currency.'<br>';
									}
								}
							}
							$element_quantity_label = $_POST[$i."_element_quantity_label".$id];
							if (isset($element_quantity_label)) {
								$quantity = ((isset($_POST[$i . "_element_quantity" . $id]) && ($_POST[$i . "_element_quantity" . $id] >= 1)) ? $_POST[$i . "_element_quantity" . $id] : 1);
								$list = $list.'<br/>'.$_POST[$i."_element_quantity_label".$id].': '.$quantity;
							}
							for ($k = 0; $k < 50; $k++) {
								$temp_val = $_POST[$i."_element_property_value".$id.$k];
								if (isset($temp_val)) {			
									$list = $list.'<br/>'.$_POST[$i."_element_property_label".$id.$k].': '.$_POST[$i."_element_property_value".$id.$k];
								}
							}
							$list = $list.'</td></tr>';
							break;
						}
						case "type_star_rating": {
							$selected = (isset($_POST[$i."_selected_star_amount".$id]) ? $_POST[$i."_selected_star_amount".$id] : 0);
							if (isset($_POST[$i."_star_amount".$id])) {
								$list = $list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="margin:0px; padding:0px">'.$selected.'/'.$_POST[$i."_star_amount".$id].'</pre></td></tr>';
							}
							break;
						}
						case "type_scale_rating": {
							$selected = (isset($_POST[$i."_scale_radio".$id]) ? $_POST[$i."_scale_radio".$id] : 0);
							if (isset($_POST[$i."_scale_amount".$id])) {
								$list = $list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="margin:0px; padding:0px">'.$selected.'/'.$_POST[$i."_scale_radio".$id].'</pre></td></tr>';
							}
							break;
						}
						case "type_spinner": {
							if (isset($_POST[$i."_element".$id])) {
								$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="margin:0px; padding:0px">'.$_POST[$i."_element".$id].'</pre></td></tr>';					
							}
							break;
						}
						case "type_slider": {
							if (isset($_POST[$i."_slider_value".$id])) {
								$list=$list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="margin:0px; padding:0px">'.$_POST[$i."_slider_value".$id].'</pre></td></tr>';					
							}
							break;
						}
						case "type_range": {
							if(isset($_POST[$i."_element".$id.'0']) || isset($_POST[$i."_element".$id.'1'])) {
								$list = $list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="margin:0px; padding:0px">From:'.$_POST[$i."_element".$id.'0'].'<span style="margin-left:6px">To</span>:'.$_POST[$i."_element".$id.'1'].'</pre></td></tr>';
							}
							break;
						}
						case "type_grading": {
							if (isset($_POST[$i."_hidden_item".$id])) {
								$element = $_POST[$i."_hidden_item".$id];
								$grading = explode(":", $element);
								$items_count = sizeof($grading) - 1;
								$total = "";
								for ($k = 0; $k < $items_count; $k++) {
									if (isset($_POST[$i."_element".$id.$k])) {
										$element .= $grading[$k].":".$_POST[$i."_element".$id.$k]." ";
										$total += $_POST[$i."_element".$id.$k];
									}
								}
								$element .= "Total:".$total;
								$list = $list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="margin:0px; padding:0px">'.$element.'</pre></td></tr>';
							}
							break;
						}
						case "type_matrix": {
							$input_type=$_POST[$i."_input_type".$id]; 
							$mat_rows = $_POST[$i."_hidden_row".$id];
							$mat_rows = explode('***', $mat_rows);
							$mat_rows = array_slice($mat_rows,0, count($mat_rows)-1);
							$mat_columns = $_POST[$i."_hidden_column".$id];
							$mat_columns = explode('***', $mat_columns);
							$mat_columns = array_slice($mat_columns,0, count($mat_columns)-1);
							$row_ids=explode(",",substr($_POST[$i."_row_ids".$id], 0, -1));
							$column_ids=explode(",",substr($_POST[$i."_column_ids".$id], 0, -1));
							$matrix = "<table>";
							$matrix .= '<tr><td></td>';
							for ($k = 0; $k < count($mat_columns); $k++) {
								$matrix .='<td style="background-color:#BBBBBB; padding:5px; ">'.$mat_columns[$k].'</td>';
							}
							$matrix .= '</tr>';
							$aaa = Array();
							$k = 0;
							foreach ($row_ids as $row_id) {
								$matrix .= '<tr><td style="background-color:#BBBBBB; padding:5px;">'.$mat_rows[$k].'</td>';
								if ($input_type=="radio") {
									$mat_radio = (isset($_POST[$i."_input_element".$id.$row_id]) ? $_POST[$i."_input_element".$id.$row_id] : 0);											
									if ($mat_radio == 0) {
										$checked = "";
										$aaa[1] = "";
									}
									else {
										$aaa = explode("_", $mat_radio);
									}
									foreach ($column_ids as $column_id) {
										if ($aaa[1] == $column_id) {
											$checked = "checked";
										}
										else {
											$checked = "";
										}
										$matrix .= '<td style="text-align:center"><input  type="radio" '.$checked.' disabled /></td>';
									}
								}
								else {
									if ($input_type=="checkbox") {
										foreach($column_ids as $column_id) {
											$checked = $_POST[$i."_input_element".$id.$row_id.'_'.$column_id];                     
											if ($checked == 1) {			
												$checked = "checked";
											}
											else {		
												$checked = "";
											}
											$matrix .= '<td style="text-align:center"><input  type="checkbox" '.$checked.' disabled /></td>';
										} 
									}
									else {
										if ($input_type=="text") {
											foreach ($column_ids as $column_id) {
												$checked = $_POST[$i."_input_element".$id.$row_id.'_'.$column_id];
												$matrix .='<td style="text-align:center"><input  type="text" value="'.$checked.'" disabled /></td>';
											}
										}
										else {
											foreach ($column_ids as $column_id) {
												$checked = $_POST[$i."_select_yes_no".$id.$row_id.'_'.$column_id];
												$matrix .='<td style="text-align:center">'.$checked.'</td>';
											}
										}
									}
								}
								$matrix .= '</tr>';
								$k++;
							}
							$matrix .= '</table>';
							if (isset($matrix)) {
								$list = $list.'<tr valign="top"><td >'.$element_label.'</td><td ><pre style="margin:0px; padding:0px">'.$matrix.'</pre></td></tr>';
							}
							break;
						}
						default:
						break;
					}
				}
			}
			$list = $list . '</table>';
			$list = wordwrap($list, 70, "\n", TRUE);
			// add_filter('wp_mail_content_type', create_function('', 'return "text/html";'));
			if ($row->from_mail != '') {
				if ($row->from_name != '') {
					$from_mail = "From: '" . $row->from_name . "' <" . $row->from_mail . ">" . "\r\n";
				}
				else {
					$from_mail = "From: '' <" . $row->from_mail . ">" . "\r\n";
				}
			}
			else {
				$from_mail = '';
			}
			$headers = $from_mail . " Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"\n";
			for ($k = 0; $k < count($all_files); $k++) {
				// $attachment[$k] = dirname(__FILE__) . '/uploads/' . $all_files[$k]['name'];
				$attachment[$k]= $all_files[$k]['name'];
			}
			if (isset($cc[0])) {
				foreach ($cc as $c) {
					if ($c) {
						$recipient = $c;
						$subject = $row->title;
						$new_script = wpautop($row->script_mail_user);
						foreach ($label_order_original as $key => $label_each) {
							if (strpos($row->script_mail_user, "%" . $label_each . "%") !== FALSE) {
								$type = $label_type[$key];
								if ($type != "type_submit_reset" or $type != "type_map" or $type != "type_editor" or  $type != "type_captcha" or $type != "type_arithmetic_captcha" or  $type != "type_recaptcha" or  $type != "type_button") {
									$new_value = "";
								switch ($type) {
									case 'type_text':
									case 'type_password':
									case 'type_textarea':
									case "type_date":
									case "type_own_select":					
									case "type_country":				
									case "type_number":	 {
										$element = $_POST[$key."_element".$id];
										if (isset($element)) {
											$new_value = $element;					
										}
										break;
									}
						case "type_hidden": {
						  $element = $_POST[$element_label];
						  if (isset($element)) {
							$new_value = $element;	
						  }
						  break;
						}                
						case "type_mark_map": {
						  $element = $_POST[$key."_long".$id];
						  if (isset($element)) {
							$new_value = 'Longitude:'.$_POST[$key."_long".$id].'<br/>Latitude:' . $_POST[$key."_lat".$id];
						  }
						  break;
						}
						case "type_submitter_mail": {
						  $element = $_POST[$key."_element".$id];
						  if (isset($element)) {
							$new_value = $element;					
						  }
						  break;
						}
						case "type_time": {
						  $hh = $_POST[$key."_hh".$id];
						  if (isset($hh)) {
							$ss = $_POST[$key."_ss".$id];
							if (isset($ss)) {
							  $new_value = $_POST[$key."_hh".$id].':'.$_POST[$key."_mm".$id].':'.$_POST[$key."_ss".$id];
							}
							else {
							  $new_value = $_POST[$key."_hh".$id].':'.$_POST[$key."_mm".$id];
							}
							$am_pm = $_POST[$key."_am_pm".$id];
							if (isset($am_pm)) {
							  $new_value = $new_value.' '.$_POST[$key."_am_pm".$id];
							}
						  }
						  break;
						}
						case "type_phone": {
						  $element_first = $_POST[$key."_element_first".$id];
						  if (isset($element_first)) {
							$new_value = $_POST[$key."_element_first".$id].' '.$_POST[$key."_element_last".$id];
						  }	
						  break;
						}
						case "type_name": {
						  $element_first = $_POST[$key."_element_first".$id];
						  if (isset($element_first)) {
							$element_title = $_POST[$key."_element_title".$id];
							if (isset($element_title)) {
							  $new_value = $_POST[$key."_element_title".$id].' '.$_POST[$key."_element_first".$id].' '.$_POST[$key."_element_last".$id].' '.$_POST[$key."_element_middle".$id];
							}
							else {
							  $new_value = $_POST[$key."_element_first".$id].' '.$_POST[$key."_element_last".$id];
							}
						  }	   
						  break;		
						}
						case "type_address": {
						  if (isset($_POST[$key."_street1".$id])) {
							$new_value = $new_value.$_POST[$key."_street1".$id];
							break;
						  }
						  if (isset($_POST[$key."_street2".$id])) {
							$new_value = $new_value.$_POST[$key."_street2".$id];
							break;
						  }
						  if (isset($_POST[$key."_city".$id])) {
							$new_value = $new_value.$_POST[$key."_city".$id];
							break;
						  }
						  if (isset($_POST[$key."_state".$id])) {
							$new_value = $new_value.$_POST[$key."_state".$id];
							break;
						  }
						  if (isset($_POST[$key."_postal".$id])) {
							$new_value = $new_value.$_POST[$key."_postal".$id];
							break;
						  }
						  if (isset($_POST[$key."_country".$id])) {
							$new_value = $new_value.$_POST[$key."_country".$id];
							break;
						  }
						}
						case "type_date_fields": {
						  $day = $_POST[$key."_day".$id];
						  if (isset($day)) {
							$new_value = $_POST[$key."_day".$id].'-'.$_POST[$key."_month".$id].'-'.$_POST[$key."_year".$id];
						  }
						  break;
						}
						case "type_radio": {
						  $element = $_POST[$key."_other_input".$id];
						  if (isset($element)) {
							$new_value = $_POST[$key."_other_input".$id];
							break;
						  }
						  $element = $_POST[$key."_element".$id];
						  if (isset($element)) {
							$new_value = $element;					
						  }
						  break;	
						}
						case "type_checkbox": {
						  $start = -1;
						  for ($j = 0; $j < 100; $j++) {
							$element = $_POST[$key."_element".$id.$j];
							if (isset($element)) {
							  $start = $j;
							  break;
							}
						  }
						  $other_element_id = -1;
						  $is_other = $_POST[$key."_allow_other".$id];
						  if ($is_other == "yes") {
							$other_element_id = $_POST[$key."_allow_other_num".$id];
						  }
						  if ($start != -1) {
							for ($j = $start; $j < 100; $j++) {
							  $element = $_POST[$key."_element".$id.$j];
							  if (isset($element)) {
								if ($j == $other_element_id) {
								  $new_value = $new_value.$_POST[$key."_other_input".$id].'<br>';
								}
								else {
								  $new_value = $new_value.$_POST[$key."_element".$id.$j].'<br>';
								}
							  }
							}
						  }
						  break;
						}
						case "type_paypal_price":	{		
						  $new_value = 0;
						  if ($_POST[$key."_element_dollars".$id]) {
							$new_value = $_POST[$key."_element_dollars".$id];
						  }
						  if ($_POST[$key."_element_cents".$id]) {
							$new_value = $new_value.'.'.$_POST[$key."_element_cents".$id];
						  }
						  $new_value = $new_value.$form_currency;
						  break;
						}
						case "type_paypal_select": {	
						  $new_value = $_POST[$key."_element_label".$id].':'.$_POST[$key."_element".$id].$form_currency;
						  $element_quantity_label = $_POST[$key."_element_quantity_label".$id];
						  if (isset($element_quantity_label)) {
							$quantity = ((isset($_POST[$key . "_element_quantity" . $id]) && ($_POST[$key . "_element_quantity" . $id] >= 1)) ? $_POST[$key . "_element_quantity" . $id] : 1);
							$new_value.='<br/>'.$_POST[$key."_element_quantity_label".$id].': '.$quantity;
						  }
						  for ($k = 0; $k < 50; $k++) {
							$temp_val = $_POST[$key."_element_property_value".$id.$k];
							if (isset($temp_val)) {			
							  $new_value .= '<br/>'.$_POST[$key."_element_property_label".$id.$k].': '.$_POST[$i."_element_property_value".$id.$k];
							}
						  }
						  break;
						}
						case "type_paypal_radio": {
						  $new_value = $_POST[$key."_element_label".$id].' - '.$_POST[$key."_element".$id].$form_currency;
						  $element_quantity_label = $_POST[$key."_element_quantity_label".$id];
						  if (isset($element_quantity_label)) {
							$quantity = ((isset($_POST[$key . "_element_quantity" . $id]) && ($_POST[$key . "_element_quantity" . $id] >= 1)) ? $_POST[$key . "_element_quantity" . $id] : 1);
							$new_value .= '<br/>'.$_POST[$key."_element_quantity_label".$id].': '.$quantity;
						  }
						  for ($k = 0; $k < 50; $k++) {
							$temp_val = $_POST[$key."_element_property_value".$id.$k];
							if (isset($temp_val)) {	
							  $new_value .= '<br/>' . $_POST[$key."_element_property_label".$id.$k].': '.$_POST[$key."_element_property_value".$id.$k];
							}
						  }
						  break;	
						}
						case "type_paypal_shipping": {
						  $new_value = $_POST[$key."_element_label".$id].' : '.$_POST[$key."_element".$id].$form_currency;		
						  break;
						}
						case "type_paypal_checkbox": {
						  $start = -1;
						  for($j = 0; $j < 100; $j++) {
							$element = $_POST[$key."_element".$id.$j];
							if (isset($element)) {
							  $start = $j;
							  break;
							}
						  }
						  if ($start != -1) {
							for ($j = $start; $j<100; $j++) {
							  $element = $_POST[$key."_element".$id.$j];
							  if (isset($element)) {
								$new_value = $new_value.$_POST[$key."_element".$id.$j."_label"].' - '.(($_POST[$key."_element".$id.$j] == '') ? '0'.$form_currency : $_POST[$key."_element".$id.$j]).$form_currency.'<br>';
							  }
							}
						  }
						  $element_quantity_label = $_POST[$key."_element_quantity_label".$id];
						  if (isset($element_quantity_label)) {
							$quantity = ((isset($_POST[$key . "_element_quantity" . $id]) && ($_POST[$key . "_element_quantity" . $id] >= 1)) ? $_POST[$key . "_element_quantity" . $id] : 1);
							$new_value .= '<br/>'.$_POST[$key."_element_quantity_label".$id].': '.$quantity;
						  }
						  for ($k = 0; $k < 50; $k++) {
							$temp_val = $_POST[$key."_element_property_value".$id.$k];
							if (isset($temp_val)) {			
							  $new_value .= '<br/>'.$_POST[$key."_element_property_label".$id.$k].': '.$_POST[$key."_element_property_value".$id.$k];
							}
						  }
						  break;
						}
						case "type_star_rating":
									  {
										$element=$_POST[$key."_star_amount".$id];
										$selected=(isset($_POST[$key."_selected_star_amount".$id]) ? $_POST[$key."_selected_star_amount".$id] : 0);
										
										
										if(isset($element))
										{
										  $new_value=$new_value.$selected.'/'.$element;					
										}
										break;
									  }
									  

									  case "type_scale_rating":
									  {
									  $element=$_POST[$key."_scale_amount".$id];
									  $selected=(isset($_POST[$key."_scale_radio".$id]) ? $_POST[$key."_scale_radio".$id] : 0);
									  
										
										if(isset($element))
										{
										  $new_value=$new_value.$selected.'/'.$element;					
										}
										break;
									  }
									  
									  case "type_spinner":
									  {

										if (isset($_POST[$key."_element".$id])) {
										  $new_value = $new_value . $_POST[$key."_element".$id];					
										}
										break;
									  }
									  
									  case "type_slider":
									  {

										$element=$_POST[$key."_slider_value".$id];
										if(isset($element))
										{
										  $new_value=$new_value.$element;					
										}
										break;
									  }
									  case "type_range":
									  {

										$element0=$_POST[$key."_element".$id.'0'];
										$element1=$_POST[$key."_element".$id.'1'];
										if(isset($element0) || isset($element1))
										{
										  $new_value=$new_value.$element0.'-'.$element1;					
										}
										break;
									  }
									  
									  case "type_grading":
									  {
										$element=$_POST[$key."_hidden_item".$id];
										$grading = explode(":",$element);
										$items_count = sizeof($grading)-1;
										
										$element = "";
										$total = "";
										
										for($k=0;$k<$items_count;$k++)

										{
										  $element .= $grading[$k].":".$_POST[$key."_element".$id.$k]." ";
									  $total += $_POST[$key."_element".$id.$k];
									}

									$element .="Total:".$total;

															  
									if(isset($element))
									{
									  $new_value=$new_value.$element;					
									}
									break;
								  }
								  
									case "type_matrix":
								  {
								  
									
									$input_type=$_POST[$key."_input_type".$id]; 
												
									$mat_rows = $_POST[$key."_hidden_row".$id];
									$mat_rows = explode('***', $mat_rows);
									$mat_rows = array_slice($mat_rows,0, count($mat_rows)-1);
									
									$mat_columns = $_POST[$key."_hidden_column".$id];
									$mat_columns = explode('***', $mat_columns);
									$mat_columns = array_slice($mat_columns,0, count($mat_columns)-1);
								  
									$row_ids=explode(",",substr($_POST[$key."_row_ids".$id], 0, -1));
									$column_ids=explode(",",substr($_POST[$key."_column_ids".$id], 0, -1)); 
						
										  
									$matrix="<table>";
										  
									  $matrix .='<tr><td></td>';
									
									for( $k=0;$k< count($mat_columns) ;$k++)
									  $matrix .='<td style="background-color:#BBBBBB; padding:5px; ">'.$mat_columns[$k].'</td>';
									  $matrix .='</tr>';
									
									$aaa=Array();
									   $k=0;
									foreach( $row_ids as $row_id){
									$matrix .='<tr><td style="background-color:#BBBBBB; padding:5px;">'.$mat_rows[$k].'</td>';
									
									  if($input_type=="radio"){
									 
									$mat_radio = (isset($_POST[$key."_input_element".$id.$row_id]) ? $_POST[$key."_input_element".$id.$row_id] : 0);											
									  if($mat_radio==0){
										$checked="";
										$aaa[1]="";
										}
										else{
										$aaa=explode("_",$mat_radio);
										}
										
										foreach( $column_ids as $column_id){
										  if($aaa[1]==$column_id)
										  $checked="checked";
										  else
										  $checked="";
										$matrix .='<td style="text-align:center"><input  type="radio" '.$checked.' disabled /></td>';
										
										}
										
									  } 
									  else{
									  if($input_type=="checkbox")
									  {                
										foreach( $column_ids as $column_id){
										 $checked = $_POST[$key."_input_element".$id.$row_id.'_'.$column_id];                              
										 if($checked==1)							
										 $checked = "checked";						
										 else									 
										 $checked = "";

										$matrix .='<td style="text-align:center"><input  type="checkbox" '.$checked.' disabled /></td>';
									  
									  }
									  
									  }
									  else
									  {
									  if($input_type=="text")
									  {
													
										foreach( $column_ids as $column_id){
										 $checked = $_POST[$key."_input_element".$id.$row_id.'_'.$column_id];
										  
										$matrix .='<td style="text-align:center"><input  type="text" value="'.$checked.'" disabled /></td>';
								  
									  }
									  
									  }
									  else{
										foreach( $column_ids as $column_id){
										 $checked = $_POST[$key."_select_yes_no".$id.$row_id.'_'.$column_id];
										   $matrix .='<td style="text-align:center">'.$checked.'</td>';
										
								
									  
										}
									  }
									  
									  }
									  
									  }
									  $matrix .='</tr>';
									  $k++;
									}
									 $matrix .='</table>';

						  
						  
						  
															
									if(isset($matrix))
									{
									  $new_value=$new_value.$matrix;					
									}
								  
									break;
								  }
						default: break;
					  }
					  $new_script = str_replace("%".$label_each."%", $new_value, $new_script);	
					}
				  }
				}       
				if (strpos($new_script, "%all%") !== FALSE) {
				  $new_script = str_replace("%all%", $list, $new_script);
				}
				$body = $new_script;
				$send = wp_mail(str_replace(' ', '', $recipient), $subject, stripslashes($body), $headers, $attachment);
			  }
			  if ($row->mail) {
				if ($c) {
				  // $headers_form_mail = "From: " . $c . " <" . $c . ">" . "\r\n";
				  $headers = "From: '" . $c . "' <" . $c . ">" . "\r\n" . "Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"\n";
				}
				// else {
				  // $headers_form_mail = "";
				// }
				if ($row_mail_one_time) {
				  $recipient = $row->mail;
				  $subject = $row->title;
				  $new_script = wpautop($row->script_mail);
				  foreach($label_order_original as $key => $label_each) {	
					if (strpos($row->script_mail, "%" . $label_each . "%") !== FALSE) {
					  $type = $label_type[$key];
					  if ($type != "type_submit_reset" or $type!="type_map" or $type!="type_editor" or  $type!="type_captcha" or $type != "type_arithmetic_captcha" or  $type!="type_recaptcha" or  $type!="type_button") {
						$new_value ="";
						switch ($type) {
						  case 'type_text':
						  case 'type_password':
						  case 'type_textarea':
						  case "type_date":
						  case "type_own_select":					
						  case "type_country":				
						  case "type_number":	 {
							$element = $_POST[$key."_element".$id];
							if (isset($element)) {
							  $new_value = $element;					
							}
							break;
						  }
						  case "type_hidden": {
							$element = $_POST[$element_label];
							if(isset($element))
							{
							  $new_value = $element;	
							}
							break;
						  }
						  case "type_mark_map": {
							$element = $_POST[$key."_long".$id];
							if (isset($element)) {
							  $new_value = 'Longitude:'.$_POST[$key."_long".$id].'<br/>Latitude:'.$_POST[$key."_lat".$id];
							}
							break;		
						  }
						  case "type_submitter_mail": {
							$element = $_POST[$key."_element".$id];
							if (isset($element)) {
							  $new_value = $element;					
							}
							break;
						  }
						  case "type_time": {
							$hh = $_POST[$key."_hh".$id];
							if (isset($hh)) {
							  $ss = $_POST[$key."_ss".$id];
							  if (isset($ss)) {
								$new_value = $_POST[$key."_hh".$id].':'.$_POST[$key."_mm".$id].':'.$_POST[$key."_ss".$id];
							  }
							  else {
								$new_value = $_POST[$key."_hh".$id].':'.$_POST[$key."_mm".$id];
							  }
							  $am_pm = $_POST[$key."_am_pm".$id];
							  if (isset($am_pm)) {
								$new_value = $new_value.' '.$_POST[$key."_am_pm".$id];
							  }
							}
							break;
						  }
						  case "type_phone": {
							$element_first = $_POST[$key."_element_first".$id];
							if (isset($element_first)) {
							  $new_value = $_POST[$key."_element_first".$id].' '.$_POST[$key."_element_last".$id];
							}	
							break;
						  }
						  case "type_name": {
							$element_first = $_POST[$key."_element_first".$id];
							if (isset($element_first)) {
							  $element_title = $_POST[$key."_element_title".$id];
							  if (isset($element_title)) {
								$new_value = $_POST[$key."_element_title".$id].' '.$_POST[$key."_element_first".$id].' '.$_POST[$key."_element_last".$id].' '.$_POST[$key."_element_middle".$id];
							  }
							  else {
								$new_value = $_POST[$key."_element_first".$id].' '.$_POST[$key."_element_last".$id];
							  }
							}	   
							break;		
						  }
						  case "type_address": {
							$street1 = $_POST[$key."_street1".$id];
							if (isset($_POST[$key."_street1".$id])) {
							  $new_value = $new_value.$_POST[$key."_street1".$id];
							  break;
							}
							if (isset($_POST[$key."_street2".$id])) {
							  $new_value=$new_value.$_POST[$key."_street2".$id];
							  break;
							}
							if (isset($_POST[$key."_city".$id])) {
							  $new_value=$new_value.$_POST[$key."_city".$id];
							  break;
							}
							if (isset($_POST[$key."_state".$id])) {
							  $new_value=$new_value.$_POST[$key."_state".$id];
							  break;
							}
							if (isset($_POST[$key."_postal".$id])) {
							  $new_value=$new_value.$_POST[$key."_postal".$id];
							  break;
							}
							if (isset($_POST[$key."_country".$id])) {
							  $new_value=$new_value.$_POST[$key."_country".$id];
							  break;
							}
						  }
						  case "type_date_fields": {
							$day = $_POST[$key."_day".$id];
							if (isset($day)) {
							  $new_value = $_POST[$key."_day".$id].'-'.$_POST[$key."_month".$id].'-'.$_POST[$key."_year".$id];
							}
							break;
						  }
						  case "type_radio": {
							$element = $_POST[$key."_other_input".$id];
							if (isset($element)) {
							  $new_value = $_POST[$key."_other_input".$id];
							  break;
							}
							$element = $_POST[$key."_element".$id];
							if (isset($element)) {
							  $new_value = $element;					
							}
							break;
						  }
						  case "type_checkbox": {
							$start = -1;
							for ($j=0; $j<100; $j++) {
							  $element = $_POST[$key."_element".$id.$j];
							  if (isset($element)) {
								$start = $j;
								break;
							  }
							}	
							$other_element_id=-1;
							$is_other = $_POST[$key."_allow_other".$id];
							if ($is_other == "yes") {
							  $other_element_id = $_POST[$key."_allow_other_num".$id];
							}
							if ($start != -1) {
							  for ($j = $start; $j < 100; $j++) {
								$element = $_POST[$key."_element".$id.$j];
								if (isset($element)) {
								  if ($j == $other_element_id) {
									$new_value = $new_value.$_POST[$key."_other_input".$id].'<br>';
								  }
								  else {
									$new_value = $new_value.$_POST[$key."_element".$id.$j].'<br>';
								  }
								}
							  }
							}
							break;
						  }
						  case "type_paypal_price": {
							$new_value = 0;
							if ($_POST[$key."_element_dollars".$id]) {
							  $new_value = $_POST[$key."_element_dollars".$id];
							}
							if ($_POST[$key."_element_cents".$id]) {
							  $new_value = $new_value.'.'.$_POST[$key."_element_cents".$id];
							}
							$new_value = $new_value.$form_currency;
							break;
						  }
						  case "type_paypal_select": {
							$new_value = $_POST[$key."_element_label".$id].':'.$_POST[$key."_element".$id].$form_currency;
							$element_quantity_label = $_POST[$key."_element_quantity_label".$id];
							if (isset($element_quantity_label)) {
							  $quantity = ((isset($_POST[$key . "_element_quantity" . $id]) && ($_POST[$key . "_element_quantity" . $id] >= 1)) ? $_POST[$key . "_element_quantity" . $id] : 1);
							  $new_value .= '<br/>'.$_POST[$key."_element_quantity_label".$id].': '.$quantity;
							}
							for($k = 0; $k < 50; $k++) {
							  $temp_val = $_POST[$key."_element_property_value".$id.$k];
							  if (isset($temp_val)) {
								$new_value .= '<br/>'.$_POST[$key."_element_property_label".$id.$k].': '.$_POST[$key."_element_property_value".$id.$k];
							  }
							}
							break;
						  }
						  case "type_paypal_radio": {
							$new_value = $_POST[$key."_element_label".$id].' - '.$_POST[$key."_element".$id].$form_currency;
							$element_quantity_label = $_POST[$key."_element_quantity_label".$id];
							if (isset($element_quantity_label)) {
							  $quantity = ((isset($_POST[$key . "_element_quantity" . $id]) && ($_POST[$key . "_element_quantity" . $id] >= 1)) ? $_POST[$key . "_element_quantity" . $id] : 1);
							  $new_value .= '<br/>'.$_POST[$key."_element_quantity_label".$id].': '.$quantity;
							}
							for ($k = 0; $k < 50; $k++) {
							  $temp_val = $_POST[$key."_element_property_value".$id.$k];
							  if (isset($temp_val)) {
								$new_value .= '<br/>'.$_POST[$key."_element_property_label".$id.$k].': '.$_POST[$key."_element_property_value".$id.$k];
							  }
							}
							break;
						  }
						  case "type_paypal_shipping": {
							$new_value = $_POST[$key."_element_label".$id].' : '.$_POST[$key."_element".$id].$form_currency;
							break;
						  }
						  case "type_paypal_checkbox": {
							$start = -1;
							for ($j = 0; $j < 100; $j++) {
							  $element = $_POST[$key."_element".$id.$j];
							  if (isset($element)) {
								$start = $j;
								break;
							  }
							}
							if ($start != -1) {
							  for ($j = $start; $j < 100; $j++) {
								$element = $_POST[$key."_element".$id.$j];
								if (isset($element)) {
								  $new_value = $new_value.$_POST[$key."_element".$id.$j."_label"].' - '.(($_POST[$key."_element".$id.$j] == '') ? '0'.$form_currency : $_POST[$key."_element".$id.$j]).$form_currency.'<br>';
								}
							  }
							}
							$element_quantity_label = $_POST[$key."_element_quantity_label".$id];
							if (isset($element_quantity_label)) {
							  $quantity = ((isset($_POST[$key . "_element_quantity" . $id]) && ($_POST[$key . "_element_quantity" . $id] >= 1)) ? $_POST[$key . "_element_quantity" . $id] : 1);
							  $new_value .= '<br/>'.$_POST[$key."_element_quantity_label".$id].': '.$quantity;
							}
							for ($k = 0; $k < 50; $k++) {
							  $temp_val = $_POST[$key."_element_property_value".$id.$k];
							  if (isset($temp_val)) {
								$new_value .= '<br/>'.$_POST[$key."_element_property_label".$id.$k].': '.$_POST[$key."_element_property_value".$id.$k];
							  }
							}
							break;
						  }
						  case "type_star_rating": {
							if (isset($_POST[$key."_star_amount".$id])) {
							  $selected = (isset($_POST[$key."_selected_star_amount".$id]) ? $_POST[$key."_selected_star_amount".$id] : 0);
							  $new_value = $new_value.$selected.'/'.$_POST[$key."_star_amount".$id];					
							}
							break;
						  }
						  case "type_scale_rating": {
							if (isset($_POST[$key."_scale_amount".$id])) {
							  $selected = (isset($_POST[$key."_scale_radio".$id]) ? $_POST[$key."_scale_radio".$id] : 0);
							  $new_value=$new_value.$selected.'/'.$_POST[$key."_scale_amount".$id];					
							}
							break;
						  }
						  case "type_spinner": {
							if(isset($_POST[$key."_element".$id])) {
							  $new_value = $new_value.$_POST[$key."_element".$id];					
							}
							break;
						  }
						  case "type_slider": {
							if (isset($_POST[$key."_slider_value".$id])) {
							  $new_value = $new_value.$_POST[$key."_slider_value".$id];
							}
							break;
						  }
						  case "type_range": {
							if (isset($_POST[$key."_element".$id.'0']) || isset($_POST[$key."_element".$id.'1'])) {
							  $new_value=$new_value.$_POST[$key."_element".$id.'0'].'-'.$_POST[$key."_element".$id.'1'];
							}
							break;
						  }
									  
									  case "type_grading":
									  {
										$element=$_POST[$key."_hidden_item".$id];
										$grading = explode(":",$element);
										$items_count = sizeof($grading)-1;
										
										$element = "";
										$total = "";
										
										for($k=0;$k<$items_count;$k++) {
										  $element .= $grading[$k].":".$_POST[$key."_element".$id.$k]." ";
									  $total += $_POST[$key."_element".$id.$k];
									}

									$element .="Total:".$total;

															  
									if(isset($element))
									{
									  $new_value=$new_value.$element;					
									}
									break;
								  }
								  
									case "type_matrix":
								  {
								  
									
									$input_type=$_POST[$key."_input_type".$id]; 
												
									$mat_rows = $_POST[$key."_hidden_row".$id];
									$mat_rows = explode('***', $mat_rows);
									$mat_rows = array_slice($mat_rows,0, count($mat_rows)-1);
									
									$mat_columns = $_POST[$key."_hidden_column".$id];
									$mat_columns = explode('***', $mat_columns);
									$mat_columns = array_slice($mat_columns,0, count($mat_columns)-1);
								  
									$row_ids=explode(",",substr($_POST[$key."_row_ids".$id], 0, -1));
									$column_ids=explode(",",substr($_POST[$key."_column_ids".$id], 0, -1)); 
									$matrix="<table>";
										  
									$matrix .='<tr><td></td>';
									
									for( $k=0;$k< count($mat_columns) ;$k++)
									  $matrix .='<td style="background-color:#BBBBBB; padding:5px; ">'.$mat_columns[$k].'</td>';
									  $matrix .='</tr>';
									
									$aaa=Array();
									   $k=0;
									foreach( $row_ids as $row_id){
									$matrix .='<tr><td style="background-color:#BBBBBB; padding:5px;">'.$mat_rows[$k].'</td>';
									
									  if($input_type=="radio"){
									 
									$mat_radio = (isset($_POST[$key."_input_element".$id.$row_id]) ? $_POST[$key."_input_element".$id.$row_id] : 0);											
									  if($mat_radio==0){
										$checked="";
										$aaa[1]="";
										}
										else{
										$aaa=explode("_",$mat_radio);
										}
										
										foreach( $column_ids as $column_id){
										  if($aaa[1]==$column_id)
										  $checked="checked";
										  else
										  $checked="";
										$matrix .='<td style="text-align:center"><input  type="radio" '.$checked.' disabled /></td>';
										
										}
										
									  } 
									  else{
									  if($input_type=="checkbox")
									  {                
										foreach( $column_ids as $column_id){
										 $checked = $_POST[$key."_input_element".$id.$row_id.'_'.$column_id];                              
										 if($checked==1)							
										 $checked = "checked";						
										 else									 
										 $checked = "";

										$matrix .='<td style="text-align:center"><input  type="checkbox" '.$checked.' disabled /></td>';
									  
									  }
									  
									  }
									  else
									  {
									  if($input_type=="text")
									  {
													
										foreach( $column_ids as $column_id){
										 $checked = $_POST[$key."_input_element".$id.$row_id.'_'.$column_id];
										  
										$matrix .='<td style="text-align:center"><input  type="text" value="'.$checked.'" disabled /></td>';
								  
									  }
									  
									  }
									  else{
										foreach( $column_ids as $column_id){
										 $checked = $_POST[$key."_select_yes_no".$id.$row_id.'_'.$column_id];
										   $matrix .='<td style="text-align:center">'.$checked.'</td>';
										
								
									  
									  }
									  }
									  
									  }
									  
									  }
									  $matrix .='</tr>';
									  $k++;
									}
									 $matrix .='</table>';

						  
						  
						  
															
									if(isset($matrix))
									{
									  $new_value=$new_value.$matrix;					
									}
								  
									break;
								  }
						  default: break;
						}
						$new_script = str_replace("%".$label_each."%", $new_value, $new_script);	
					  }
					}
				  }
				  if (strpos($new_script, "%all%") !== FALSE) {
					$new_script = str_replace("%all%", $list, $new_script);
				  }
				  $body = $new_script;
				  $mode = 1;
				  $send = wp_mail(str_replace(' ', '', $recipient), $subject, stripslashes($body), $headers, $attachment);
				  $row_mail_one_time = 0;
				}
			  }
			}
		  }
		  else {
			if ($row->mail) {
			  $recipient = $row->mail;
			  $subject = $row->title;
			  $new_script = wpautop($row->script_mail);
			  foreach($label_order_original as $key => $label_each) {
				if (strpos($row->script_mail, "%" . $label_each . "%") !== FALSE) {
				  $type = $label_type[$key];
				  if ($type != "type_submit_reset" or $type != "type_map" or $type != "type_editor" or  $type!="type_captcha" or $type != "type_arithmetic_captcha" or  $type!="type_recaptcha" or  $type!="type_button") {
					$new_value = "";
					switch ($type) {
					  case 'type_text':
					  case 'type_password':
					  case 'type_textarea':
					  case "type_date":
					  case "type_own_select":					
					  case "type_country":				
					  case "type_number":	{
						$element = $_POST[$key."_element".$id];
						if (isset($element)) {
						  $new_value = $element;					
						}
						break;
					  }
					  case "type_hidden": {
						$element = $_POST[$element_label];
						if (isset($element)) {
						  $new_value = $element;	
						}
						break;
					  }
					  case "type_mark_map": {
						$element = $_POST[$key."_long".$id];
						if (isset($element)) {
						  $new_value = 'Longitude:'.$_POST[$key."_long".$id].'<br/>Latitude:'.$_POST[$key."_lat".$id];
						}
						break;
					  }
					  case "type_submitter_mail": {
						$element = $_POST[$key."_element".$id];
						if (isset($element)) {
						  $new_value = $element;					
						}
						break;		
					  }
					  case "type_time": {
						$hh = $_POST[$key."_hh".$id];
						if (isset($hh)) {
						  $ss = $_POST[$key."_ss".$id];
						  if (isset($ss)) {
							$new_value = $_POST[$key."_hh".$id].':'.$_POST[$key."_mm".$id].':'.$_POST[$key."_ss".$id];
						  }
						  else {
							$new_value = $_POST[$key."_hh".$id].':'.$_POST[$key."_mm".$id];
						  }
						  $am_pm = $_POST[$key."_am_pm".$id];
						  if (isset($am_pm)) {
							$new_value = $new_value.' '.$_POST[$key."_am_pm".$id];
						  }
						}
						break;
					  }
					  case "type_phone": {
						$element_first = $_POST[$key."_element_first".$id];
						if (isset($element_first)) {
						  $new_value = $_POST[$key."_element_first".$id].' '.$_POST[$key."_element_last".$id];
						}
						break;
					  }
					  case "type_name": {
						$element_first = $_POST[$key."_element_first".$id];
						if (isset($element_first)) {
						  $element_title = $_POST[$key."_element_title".$id];
						  if (isset($element_title)) {
							$new_value = $_POST[$key."_element_title".$id].' '.$_POST[$key."_element_first".$id].' '.$_POST[$key."_element_last".$id].' '.$_POST[$key."_element_middle".$id];
						  }
						  else {
							$new_value = $_POST[$key."_element_first".$id].' '.$_POST[$key."_element_last".$id];
						  }
						}	   
						break;
					  }
					  case "type_address": {
						if (isset($_POST[$key."_street1".$id])) {
						  $new_value = $new_value.$_POST[$key."_street1".$id];
						  break;
						}
						if (isset($_POST[$key."_street2".$id])) {
						  $new_value = $new_value.$_POST[$key."_street2".$id];
						  break;
						}
						if (isset($_POST[$key."_city".$id])) {
						  $new_value = $new_value.$_POST[$key."_city".$id];
						  break;
						}
						if (isset($_POST[$key."_state".$id])) {
						  $new_value = $new_value.$_POST[$key."_state".$id];
						  break;
						}
						if (isset($_POST[$key."_postal".$id])) {
						  $new_value = $new_value.$_POST[$key."_postal".$id];
						  break;
						}
						if (isset($_POST[$key."_country".$id])) {
						  $new_value = $new_value.$_POST[$key."_country".$id];
						  break;
						}
					  }
					  case "type_date_fields": {
						$day = $_POST[$key."_day".$id];
						if (isset($day)) {
						  $new_value = $_POST[$key."_day".$id].'-'.$_POST[$key."_month".$id].'-'.$_POST[$key."_year".$id];
						}
						break;
					  }
					  case "type_radio": {
						$element = $_POST[$key."_other_input".$id];
						if (isset($element)) {
						  $new_value = $_POST[$key."_other_input".$id];
						  break;
						}
						$element = $_POST[$key."_element".$id];
						if (isset($element)) {
						  $new_value = $element;
						}
						break;
					  }
					  case "type_checkbox": {
						$start = -1;
						for ($j = 0; $j < 100; $j++) {
						  $element = $_POST[$key."_element".$id.$j];
						  if (isset($element)) {
							$start = $j;
							break;
						  }
						}
						$other_element_id = -1;
						$is_other = $_POST[$key."_allow_other".$id];
						if ($is_other == "yes") {
						  $other_element_id = $_POST[$key."_allow_other_num".$id];
						}
						if ($start != -1) {
						  for ($j = $start; $j < 100; $j++) {
							$element = $_POST[$key."_element".$id.$j];
							if (isset($element)) {
							  if ($j == $other_element_id) {
								$new_value = $new_value.$_POST[$key."_other_input".$id].'<br>';
							  }
							  else {
								$new_value = $new_value.$_POST[$key."_element".$id.$j].'<br>';
							  }
							}
						  }
						}
						break;
					  }
					  case "type_paypal_price": {		
						$new_value = 0;
						if ($_POST[$key."_element_dollars".$id]) {
						  $new_value = $_POST[$key."_element_dollars".$id];
						}
						if ($_POST[$key."_element_cents".$id]) {
						  $new_value = $new_value.'.'.$_POST[$key."_element_cents".$id];
						}
						$new_value = $new_value.$form_currency;
						break;
					  }
					  case "type_paypal_select": {	
						$new_value = $_POST[$key."_element_label".$id].':'.$_POST[$key."_element".$id].$form_currency;
						$element_quantity_label = $_POST[$key."_element_quantity_label".$id];
						if (isset($element_quantity_label)) {
						  $quantity = ((isset($_POST[$key . "_element_quantity" . $id]) && ($_POST[$key . "_element_quantity" . $id] >= 1)) ? $_POST[$key . "_element_quantity" . $id] : 1);
						  $new_value .= '<br/>'.$_POST[$key."_element_quantity_label".$id].': '.$quantity;
						}
						for ($k = 0; $k < 50; $k++) {
						  $temp_val = $_POST[$key."_element_property_value".$id.$k];
						  if (isset($temp_val)) {			
							$new_value .= '<br/>'.$_POST[$key."_element_property_label".$id.$k].': '.$_POST[$key."_element_property_value".$id.$k];
						  }
						}
						break;
					  }
					  case "type_paypal_radio": {
						$new_value = $_POST[$key."_element_label".$id].' - '.$_POST[$key."_element".$id].$form_currency;
						$element_quantity_label = $_POST[$key."_element_quantity_label".$id];
						if (isset($element_quantity_label)) {
						  $quantity = ((isset($_POST[$key . "_element_quantity" . $id]) && ($_POST[$key . "_element_quantity" . $id] >= 1)) ? $_POST[$key . "_element_quantity" . $id] : 1);
						  $new_value .= '<br/>'.$_POST[$key."_element_quantity_label".$id].': '.$quantity;
						}
						for ($k = 0; $k < 50; $k++) {
						  $temp_val = $_POST[$key."_element_property_value".$id.$k];
						  if (isset($temp_val)) {			
							$new_value .= '<br/>'.$_POST[$key."_element_property_label".$id.$k].': '.$_POST[$key."_element_property_value".$id.$k];
						  }
						}
						break;	
					  }
					  case "type_paypal_shipping": {
						$new_value = $_POST[$key."_element_label".$id].' : '.$_POST[$key."_element".$id].$form_currency;
						break;
					  }
					  case "type_paypal_checkbox": {
						$start = -1;
						for ($j = 0; $j < 100; $j++) {
						  $element = $_POST[$key."_element".$id.$j];
						  if (isset($element)) {
							$start = $j;
							break;
						  }
						}
						if ($start != -1) {
						  for ($j = $start; $j < 100; $j++) {
							$element = $_POST[$key."_element".$id.$j];
							if (isset($element)) {
							  $new_value = $new_value.$_POST[$key."_element".$id.$j."_label"].' - '.(($_POST[$key."_element".$id.$j] == '') ? '0'.$form_currency : $_POST[$key."_element".$id.$j]).$form_currency.'<br>';
							}
						  }
						}
						$element_quantity_label = $_POST[$key."_element_quantity_label".$id];
						if (isset($element_quantity_label)) {
						  $quantity = ((isset($_POST[$key . "_element_quantity" . $id]) && ($_POST[$key . "_element_quantity" . $id] >= 1)) ? $_POST[$key . "_element_quantity" . $id] : 1);
						  $new_value .= '<br/>'.$_POST[$key."_element_quantity_label".$id].': '.$quantity;
						}
						for ($k = 0; $k < 50; $k++) {
						  $temp_val = $_POST[$key."_element_property_value".$id.$k];
						  if (isset($temp_val)) {			
							$new_value .= '<br/>'.$_POST[$key."_element_property_label".$id.$k].': '.$_POST[$key."_element_property_value".$id.$k];
						  }
						}
						break;
					  }
					  case "type_star_rating":
									  {
										$element=$_POST[$key."_star_amount".$id];
										$selected=(isset($_POST[$key."_selected_star_amount".$id]) ? $_POST[$key."_selected_star_amount".$id] : 0);
										if(isset($element))
										{
										  $new_value=$new_value.$selected.'/'.$element;					
										}
										break;
									  }
									  

									  case "type_scale_rating":
									  {
									  $element=$_POST[$key."_scale_amount".$id];
									  $selected=(isset($_POST[$key."_scale_radio".$id]) ? $_POST[$key."_scale_radio".$id] : 0);
									  
										
										if(isset($element))
										{
										  $new_value=$new_value.$selected.'/'.$element;					
										}
										break;
									  }
									  
									  case "type_spinner":
									  {

										if(isset($_POST[$key."_element".$id]))
										{
										  $new_value=$new_value.$_POST[$key."_element".$id];					
										}
										break;
									  }
									  
									  case "type_slider":
									  {

										$element=$_POST[$key."_slider_value".$id];
										if(isset($element))
										{
										  $new_value=$new_value.$element;					
										}
										break;
									  }
									  case "type_range":
									  {

										$element0=$_POST[$key."_element".$id.'0'];
										$element1=$_POST[$key."_element".$id.'1'];
										if(isset($element0) || isset($element1))
										{
										  $new_value=$new_value.$element0.'-'.$element1;					
										}
										break;
									  }
									  
									  case "type_grading":
									  {
										$element=$_POST[$key."_hidden_item".$id];
										$grading = explode(":",$element);
										$items_count = sizeof($grading)-1;
										
										$element = "";
										$total = "";
										
										for($k=0;$k<$items_count;$k++)

										{
										  $element .= $grading[$k].":".$_POST[$key."_element".$id.$k]." ";
									  $total += $_POST[$key."_element".$id.$k];
									}

									$element .="Total:".$total;

															  
									if(isset($element))
									{
									  $new_value=$new_value.$element;					
									}
									break;
								  }
								  
									case "type_matrix":
								  {
								  
									
									$input_type=$_POST[$key."_input_type".$id]; 
												
									$mat_rows = $_POST[$key."_hidden_row".$id];
									$mat_rows = explode('***', $mat_rows);
									$mat_rows = array_slice($mat_rows,0, count($mat_rows)-1);
									
									$mat_columns = $_POST[$key."_hidden_column".$id];
									$mat_columns = explode('***', $mat_columns);
									$mat_columns = array_slice($mat_columns,0, count($mat_columns)-1);
								  
									$row_ids=explode(",",substr($_POST[$key."_row_ids".$id], 0, -1));
									$column_ids=explode(",",substr($_POST[$key."_column_ids".$id], 0, -1)); 
												  
										  
									$matrix="<table>";
										  
									  $matrix .='<tr><td></td>';
									
									for( $k=0;$k< count($mat_columns) ;$k++)
									  $matrix .='<td style="background-color:#BBBBBB; padding:5px; ">'.$mat_columns[$k].'</td>';
									  $matrix .='</tr>';
									
									$aaa=Array();
									   $k=0;
									foreach($row_ids as $row_id)
									{
									$matrix .='<tr><td style="background-color:#BBBBBB; padding:5px;">'.$mat_rows[$k].'</td>';
									
									  if($input_type=="radio"){
									 
									$mat_radio = (isset($_POST[$key."_input_element".$id.$row_id]) ? $_POST[$key."_input_element".$id.$row_id] : 0);											
									  if($mat_radio==0){
										$checked="";
										$aaa[1]="";
										}
										else{
										$aaa=explode("_",$mat_radio);
										}
										
										foreach($column_ids as $column_id){
										  if($aaa[1]==$column_id)
										  $checked="checked";
										  else
										  $checked="";
										$matrix .='<td style="text-align:center"><input  type="radio" '.$checked.' disabled /></td>';
										
										}
										
									  } 
									  else{
									  if($input_type=="checkbox")
									  {                
										foreach($column_ids as $column_id){
										 $checked = $_POST[$key."_input_element".$id.$row_id.'_'.$column_id];                              
										 if($checked==1)							
										 $checked = "checked";						
										 else									 
										 $checked = "";

										$matrix .='<td style="text-align:center"><input  type="checkbox" '.$checked.' disabled /></td>';
									  
									  }
									  
									  }
									  else
									  {
									  if($input_type=="text")
									  {
													
										foreach($column_ids as $column_id){
										 $checked = $_POST[$key."_input_element".$id.$row_id.'_'.$column_id];
										  
										$matrix .='<td style="text-align:center"><input  type="text" value="'.$checked.'" disabled /></td>';
								  
									  }
									  
									  }
									  else{
										foreach($column_ids as $column_id){
										 $checked = $_POST[$i."_select_yes_no".$id.$row_id.'_'.$column_id];
										   $matrix .='<td style="text-align:center">'.$checked.'</td>';
										
								
									  
										}
									  }
									  
									  }
									  
									  }
									  $matrix .='</tr>';
									  $k++;
									}
									 $matrix .='</table>';

						  
						  
						  
															
									if(isset($matrix))
									{
									  $new_value=$new_value.$matrix;					
									}
								  
									break;
								  }
					  default: break;
					}
					$new_script = str_replace("%".$label_each."%", $new_value, $new_script);
				  }
				}
			  }
			  if (strpos($new_script, "%all%") !== FALSE) {
				$new_script = str_replace("%all%", $list, $new_script);
			  }
			  $body = $new_script;
			  $send = wp_mail(str_replace(' ', '', $recipient), $subject, stripslashes($body), $headers, $attachment);
			}
		  }
		  if ($row->mail) {
			if ($send != TRUE) {
			  $_SESSION['error_or_no' . $id] = 1;
			  $msg = addslashes(__('Error, email was not sent.', 'form_maker'));
			}
			else {
			  $_SESSION['error_or_no' . $id] = 0;
			  $msg = addslashes(__('Your form was successfully submitted.', 'form_maker'));
			}
		  }
		  else {
			$_SESSION['error_or_no' . $id] = 0;
			$msg = addslashes(__('Your form was successfully submitted.', 'form_maker'));
		  }
		}
               
                // delete files from uploads (save_upload = 0)
                if($row->save_uploads == 0){
                    foreach ($all_files as &$all_file) {
                        if (file_exists(ABSPATH.'/'.$all_file['tmp_name'])) {
                            unlink(ABSPATH.'/'.$all_file['tmp_name']);
                        }
                    }
					
                }
		$https = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://');
		switch ($row->submit_text_type) {
			case "2":
			case "5": {
				if ($row->submit_text_type != 4) {
				  $_SESSION['massage_after_submit' . $id] = $msg;
				}
				$_SESSION['form_submit_type' . $id] = $row->submit_text_type . "," . $row->id;
				if ($row->article_id) {
				  $redirect_url = $row->article_id;
				}
				else {
				  $redirect_url = $https . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
				}
				break;
			}
			case "3": {
				if ($row->submit_text_type != 4) {
				  $_SESSION['massage_after_submit' . $id] = $msg;
				}
				$_SESSION['form_submit_type' . $id] = $row->submit_text_type . "," . $row->id;
				$redirect_url = $https . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
				break;
			}
			case "4": {
				if ($row->submit_text_type != 4) {
				  $_SESSION['massage_after_submit' . $id] = $msg;
				}
				$_SESSION['form_submit_type' . $id] = $row->submit_text_type . "," . $row->id;
				$redirect_url = $row->url;
				break;
			}
			default: {
				if ($row->submit_text_type != 4) {
					$_SESSION['massage_after_submit' . $id] = $msg;
				}
				$_SESSION['form_submit_type' . $id] = $row->submit_text_type . "," . $row->id;
				$redirect_url = $https . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
				break;
			}
		}
		if (!$str) {
			wp_redirect($redirect_url);
			exit;
		}
		else {
			$_SESSION['redirect_paypal'.$id] = 1;
		  
			$str .= "&return=" . urlencode($redirect_url);
			wp_redirect($str);
			exit;
		}
	}

	public static function custom_fields_mail($type, $key, $id, $attachment, $form_currency)
	{
		$new_value ="";
		
		$disabled_fields	= explode(',', isset($_REQUEST["disabled_fields".$id]) ? $_REQUEST["disabled_fields".$id] : "");
		$disabled_fields 	= array_slice($disabled_fields,0, count($disabled_fields)-1);
    
    if($type!="type_submit_reset" or $type!="type_map" or $type!="type_editor" or  $type!="type_captcha" or $type != "type_arithmetic_captcha" or  $type!="type_recaptcha" or  $type!="type_button") {
      switch ($type) {
        case 'type_text':
        case 'type_password':
        case 'type_textarea':
        case "type_date":
        case "type_own_select":					
        case "type_country":				
        case "type_number": {
          $element = isset($_POST['wdform_'.$key."_element".$id]) ? $_POST['wdform_'.$key."_element".$id] : NULL;
          if(isset($element)) {
            $new_value = $element;					
          }
          break;
        }
        case "type_file_upload": {
          	if($attachment)
				foreach($attachment as $attachment_temp)
				{
					$uploadedFileNameParts = explode('.',$attachment_temp[1]);
					$uploadedFileExtension = array_pop($uploadedFileNameParts);
					
					$invalidFileExts = array('gif', 'jpg', 'jpeg', 'png', 'swf', 'psd', 'bmp', 'tiff', 'jpc', 'jp2', 'jpf', 'jb2', 'swc', 'aiff', 'wbmp', 'xbm' );
					$extOk = false;

					foreach($invalidFileExts as $key => $valuee)
					{
						if(is_numeric(strpos(strtolower($valuee), strtolower($uploadedFileExtension) )) )
							$extOk = true;
					}
					
					if ($extOk == true) 
						$new_value .= '<img src="'.site_url().'/'.$attachment_temp.'" alt="'.$attachment_temp[1].'"/>';
						
				}
			break;
        }
        case "type_hidden": {
          $element = isset($_POST[$element_label]) ? $_POST[$element_label] : NULL;
          if(isset($element)) {
            $new_value = $element;	
          }
          break;
        }
        case "type_mark_map": {
          $element = isset($_POST['wdform_'.$key."_long".$id]) ? $_POST['wdform_'.$key."_long".$id] : NULL;
          if(isset($element)) {
            $new_value = 'Longitude:' . $element . '<br/>Latitude:' . (isset($_POST['wdform_'.$key."_lat".$id]) ? $_POST['wdform_'.$key."_lat".$id] : "");
          }
          break;		
        }
        case "type_submitter_mail": {
          $element = isset($_POST['wdform_'.$key."_element".$id]) ? $_POST['wdform_'.$key."_element".$id] : NULL;
          if(isset($element)) {
            $new_value = $element;					
          }
          break;		
        }								
        case "type_time": {
          $hh = isset($_POST['wdform_'.$key."_hh".$id]) ? $_POST['wdform_'.$key."_hh".$id] : NULL;
          if(isset($hh)) {
            $ss = isset($_POST['wdform_'.$key."_ss".$id]) ? $_POST['wdform_'.$key."_ss".$id] : NULL;
            if(isset($ss)) {
              $new_value = $hh . ':' . (isset($_POST['wdform_'.$key."_mm".$id]) ? $_POST['wdform_'.$key."_mm".$id] : "") . ':' . $ss;
            }
            else {
              $new_value = $hh . ':' . (isset($_POST['wdform_'.$key."_mm".$id]) ? $_POST['wdform_'.$key."_mm".$id] : "");
            }
            $am_pm = isset($_POST['wdform_'.$key."_am_pm".$id]) ? $_POST['wdform_'.$key."_am_pm".$id] : NULL;
            if(isset($am_pm)) {
              $new_value = $new_value . ' ' . $am_pm;
            }
          }
          break;
        }
        
        case "type_phone": {
          $element_first = isset($_POST['wdform_'.$key."_element_first".$id]) ? $_POST['wdform_'.$key."_element_first".$id] : NULL;
          if(isset($element_first)) {
              $new_value = $element_first . ' ' . (isset($_POST['wdform_'.$key."_element_last".$id]) ? $_POST['wdform_'.$key."_element_last".$id] : "");
          }	
          break;
        }								
        case "type_name": {
          $element_first = isset($_POST['wdform_'.$key."_element_first".$id]) ? $_POST['wdform_'.$key."_element_first".$id] : NULL;
          if(isset($element_first)) {
            $element_title = isset($_POST['wdform_'.$key."_element_title".$id]) ? $_POST['wdform_'.$key."_element_title".$id] : NULL;
            if(isset($element_title)) {
              $new_value = $element_title . ' ' . $element_first . ' ' . (isset($_POST['wdform_'.$key."_element_last".$id]) ? $_POST['wdform_'.$key."_element_last".$id] : "") . ' ' . (isset($_POST['wdform_'.$key."_element_middle".$id]) ? $_POST['wdform_'.$key."_element_middle".$id] : "");
            }
            else {
              $new_value = $element_first . ' ' . (isset($_POST['wdform_'.$key."_element_last".$id]) ? $_POST['wdform_'.$key."_element_last".$id] : "");
            }
          }	   
          break;		
        }								
        case "type_address": {
          $street1 = isset($_POST['wdform_'.$key."_street1".$id]) ? $_POST['wdform_'.$key."_street1".$id] : NULL;
          if(isset($street1)) {
            $new_value = $street1;
            break;
          }                  
          $street2 = isset($_POST['wdform_'.$key."_street2".$id]) ? $_POST['wdform_'.$key."_street2".$id] : NULL;
          if(isset($street2)) {
            $new_value = $street2;
            break;
          }
          $city = isset($_POST['wdform_'.$key."_city".$id]) ? $_POST['wdform_'.$key."_city".$id] : NULL;
          if(isset($city)) {
            $new_value = $city;
            break;
          }                  
          $state = isset($_POST['wdform_'.$key."_state".$id]) ? $_POST['wdform_'.$key."_state".$id] : NULL;
          if(isset($state)) {
            $new_value = $state;
            break;
          }
          $postal = isset($_POST['wdform_'.$key."_postal".$id]) ? $_POST['wdform_'.$key."_postal".$id] : NULL;
          if(isset($postal)) {
            $new_value = $postal;
            break;
          }
          $country = isset($_POST['wdform_'.$key."_country".$id]) ? $_POST['wdform_'.$key."_country".$id] : NULL;
          if(isset($country)) {
            $new_value = $country;
            break;
          }
          break;
        }
        case "type_date_fields": {
          $day = isset($_POST['wdform_'.$key."_day".$id]) ? $_POST['wdform_'.$key."_day".$id] : NULL;
          if(isset($day)) {
            $new_value = $day . '-' . (isset($_POST['wdform_'.$key."_month".$id]) ? $_POST['wdform_'.$key."_month".$id] : "") . '-' . (isset($_POST['wdform_'.$key."_year".$id]) ? $_POST['wdform_'.$key."_year".$id] : "");
          }
          break;
        }
        
        case "type_radio": {
          $element = isset($_POST['wdform_'.$key."_other_input".$id]) ? $_POST['wdform_'.$key."_other_input".$id] : NULL;
          if(isset($element)) {
            $new_value = $element;
            break;
          }									
          $element = isset($_POST['wdform_'.$key."_element".$id]) ? $_POST['wdform_'.$key."_element".$id] : NULL;
          if(isset($element)) {
            $new_value = $element;					
          }
          break;	
        }								
        case "type_checkbox": {
          $start = -1;
          for($j = 0; $j < 100; $j++) {
            $element = isset($_POST['wdform_'.$key."_element".$id.$j]) ? $_POST['wdform_'.$key."_element".$id.$j] : NULL;
            if(isset($element)) {
              $start = $j;
              break;
            }
          }									
          $other_element_id = -1;
          $is_other = isset($_POST['wdform_'.$key."_allow_other".$id]) ? $_POST['wdform_'.$key."_allow_other".$id] : "";
          if($is_other == "yes") {
            $other_element_id = isset($_POST['wdform_'.$key."_allow_other_num".$id]) ? $_POST['wdform_'.$key."_allow_other_num".$id] : "";
          }
          if($start != -1) {
            for($j = $start; $j < 100; $j++) {											
              $element = isset($_POST['wdform_'.$key."_element".$id.$j]) ? $_POST['wdform_'.$key."_element".$id.$j] : NULL;
              if(isset($element)) {
                if($j == $other_element_id) {
                  $new_value = $new_value . (isset($_POST['wdform_'.$key."_other_input".$id]) ? $_POST['wdform_'.$key."_other_input".$id] : "") . '<br>';
                }
                else {											
                  $new_value = $new_value . $element . '<br>';
                }
              }
            }										
          }
          break;
        }
        case "type_paypal_price": {		
          $new_value = 0;
          if(isset($_POST['wdform_'.$key."_element_dollars".$id])) {
            $new_value = $_POST['wdform_'.$key."_element_dollars".$id];
          }
          if(isset($_POST['wdform_'.$key."_element_cents".$id])) {
            $new_value = $new_value . '.' . $_POST['wdform_'.$key."_element_cents".$id];
          }
          $new_value = $new_value . $form_currency;
          break;
        }								
        case "type_paypal_select": {
          $new_value = (isset($_POST['wdform_'.$key."_element_label".$id]) ? $_POST['wdform_'.$key."_element_label".$id] : "") . ':' . (isset($_POST['wdform_'.$key."_element".$id]) ? $_POST['wdform_'.$key."_element".$id] : "") . $form_currency;
          $element_quantity_label = isset($_POST['wdform_'.$key."_element_quantity_label".$id]) ? $_POST['wdform_'.$key."_element_quantity_label".$id] : NULL;
          $element_quantity = (isset($_POST['wdform_'.$key."_element_quantity".$id]) && $_POST['wdform_'.$key."_element_quantity".$id]) ? $_POST['wdform_'.$key."_element_quantity".$id] : NULL;
          if (isset($element_quantity)) {
            $new_value .= '<br/>' . $element_quantity_label . ': ' . $element_quantity;
          }
          for($k = 0; $k < 50; $k++) {
            $temp_val = isset($_POST['wdform_'.$key."_property".$id.$k]) ? $_POST['wdform_'.$key."_property".$id.$k] : NULL;
            if(isset($temp_val)) {
              $new_value .= '<br/>' . (isset($_POST['wdform_'.$key."_element_property_label".$id.$k]) ? $_POST['wdform_'.$key."_element_property_label".$id.$k] : "") . ': ' . $temp_val;
            }
          }
          break;
        }								
        case "type_paypal_radio": {
          $new_value = (isset($_POST['wdform_'.$key."_element_label".$id]) ? $_POST['wdform_'.$key."_element_label".$id] : "") . ' - ' . (isset($_POST['wdform_'.$key."_element".$id]) ? $_POST['wdform_'.$key."_element".$id] : "") . $form_currency;									
          $element_quantity_label = isset($_POST['wdform_'.$key."_element_quantity_label".$id]) ? $_POST['wdform_'.$key."_element_quantity_label".$id] : NULL;
          $element_quantity = (isset($_POST['wdform_'.$i."_element_quantity".$id]) && $_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : NULL;
          if (isset($element_quantity)) {
            $new_value .= '<br/>' . $element_quantity_label . ': ' . $element_quantity;
          }
          for($k = 0; $k < 50; $k++) {
            $temp_val = isset($_POST['wdform_'.$key."_property".$id.$k]) ? $_POST['wdform_'.$key."_property".$id.$k] : NULL;
            if(isset($temp_val)) {
              $new_value .= '<br/>' . (isset($_POST['wdform_'.$key."_element_property_label".$id.$k]) ? $_POST['wdform_'.$key."_element_property_label".$id.$k] : "") . ': ' . $temp_val;
            }
          }							
          break;
        }
        case "type_paypal_shipping": {									
          $new_value = (isset($_POST['wdform_'.$key."_element_label".$id]) ? $_POST['wdform_'.$key."_element_label".$id] : "") . ' : ' . (isset($_POST['wdform_'.$key."_element".$id]) ? $_POST['wdform_'.$key."_element".$id] : "") . $form_currency;		
          break;
        }
        case "type_paypal_checkbox": {
          $start = -1;
          for($j = 0; $j < 100; $j++) {
            $element = isset($_POST['wdform_'.$key."_element".$id.$j]) ? $_POST['wdform_'.$key."_element".$id.$j] : NULL;
            if(isset($element)) {
              $start = $j;
              break;
            }
          }									
          if($start != -1) {
            for($j = $start; $j < 100; $j++) {											
              $element = isset($_POST['wdform_'.$key."_element".$id.$j]) ? $_POST['wdform_'.$key."_element".$id.$j] : NULL;
              if(isset($element)) {
                $new_value = $new_value . (isset($_POST['wdform_'.$key."_element".$id.$j."_label"]) ? $_POST['wdform_'.$key."_element".$id.$j."_label"] : "") . ' - ' . ($element == '' ? '0' . $form_currency : $element) . $form_currency . '<br>';
              }
            }
          }									
          $element_quantity_label = isset($_POST['wdform_'.$key."_element_quantity_label".$id]) ? $_POST['wdform_'.$key."_element_quantity_label".$id] : NULL;
          $element_quantity = (isset($_POST['wdform_'.$key."_element_quantity".$id]) && $_POST['wdform_'.$key."_element_quantity".$id]) ? $_POST['wdform_'.$key."_element_quantity".$id] : NULL;
          if (isset($element_quantity)) {
            $new_value .= '<br/>' . $element_quantity_label . ': ' . $element_quantity;
          }
          for($k = 0; $k < 50; $k++) {
            $temp_val = isset($_POST['wdform_'.$key."_property".$id.$k]) ? $_POST['wdform_'.$key."_property".$id.$k] : NULL;
            if(isset($temp_val)) {
              $new_value .= '<br/>' . (isset($_POST['wdform_'.$key."_element_property_label".$id.$k]) ? $_POST['wdform_'.$key."_element_property_label".$id.$k] : "") . ': ' . $temp_val;
            }
          }									
          break;
        }								
        case "type_paypal_total": {
          $element = isset($_POST['wdform_'.$key."_paypal_total".$id]) ? $_POST['wdform_'.$key."_paypal_total".$id] : "";
          $new_value = $new_value . $element;
          break;
        }
        case "type_star_rating": {
          $element = isset($_POST['wdform_'.$key."_star_amount".$id]) ? $_POST['wdform_'.$key."_star_amount".$id] : NULL;
          $selected = isset($_POST['wdform_'.$key."_selected_star_amount".$id]) ? $_POST['wdform_'.$key."_selected_star_amount".$id] : 0;									
          if(isset($element)) {
            $new_value = $new_value . $selected . '/' . $element;					
          }
          break;
        }
        case "type_scale_rating": {
          $element = isset($_POST['wdform_'.$key."_scale_amount".$id]) ? $_POST['wdform_'.$key."_scale_amount".$id] : NULL;
          $selected = isset($_POST['wdform_'.$key."_scale_radio".$id]) ? $_POST['wdform_'.$key."_scale_radio".$id] : 0;
          if(isset($element)) {
            $new_value = $new_value . $selected . '/' . $element;					
          }
          break;
        }								
        case "type_spinner": {
          $element = isset($_POST['wdform_'.$key."_element".$id]) ? $_POST['wdform_'.$key."_element".$id] : NULL;
          if(isset($element)) {
            $new_value = $new_value . $element;					
          }
          break;
        }								
        case "type_slider": {
          $element = isset($_POST['wdform_'.$key."_slider_value".$id]) ? $_POST['wdform_'.$key."_slider_value".$id] : NULL;
          if(isset($element)) {
            $new_value = $new_value . $element;					
          }
          break;
        }
        case "type_range": {
          $element0 = isset($_POST['wdform_'.$key."_element".$id.'0']) ? $_POST['wdform_'.$key."_element".$id.'0'] : NULL;
          $element1 = isset($_POST['wdform_'.$key."_element".$id.'1']) ? $_POST['wdform_'.$key."_element".$id.'1'] : NULL;
          if(isset($element0) || isset($element1)) {
            $new_value = $new_value . $element0 . '-' . $element1;					
          }
          break;
        }								
        case "type_grading": {
          $element = isset($_POST['wdform_'.$key."_hidden_item".$id]) ? $_POST['wdform_'.$key."_hidden_item".$id] : "";
          $grading = explode(":", $element);
          $items_count = sizeof($grading) - 1;									
          $element = "";
          $total = "";									
          for($k = 0;$k < $items_count; $k++) {
            $element .= $grading[$k] . ":" . (isset($_POST['wdform_'.$key."_element".$id.'_'.$k]) ? $_POST['wdform_'.$key."_element".$id.'_'.$k] : "") . " ";
            $total += (isset($_POST['wdform_'.$key."_element".$id.'_'.$k]) ? $_POST['wdform_'.$key."_element".$id.'_'.$k] : 0);
          }
          $element .="Total:" . $total;
          if(isset($element)) {
            $new_value = $new_value . $element;
          }
          break;
        }						
        case "type_matrix": {
          $input_type = isset($_POST['wdform_'.$key."_input_type".$id]) ? $_POST['wdform_'.$key."_input_type".$id] : "";
          $mat_rows = explode("***", isset($_POST['wdform_'.$key."_hidden_row".$id]) ? $_POST['wdform_'.$key."_hidden_row".$id] : "");
          $rows_count = sizeof($mat_rows) - 1;
          $mat_columns = explode("***", isset($_POST['wdform_'.$key."_hidden_column".$id]) ? $_POST['wdform_'.$key."_hidden_column".$id] : "");
          $columns_count = sizeof($mat_columns) - 1;												
          $matrix="<table>";												
          $matrix .='<tr><td></td>';
          for( $k=1;$k< count($mat_columns) ;$k++) {
            $matrix .= '<td style="background-color:#BBBBBB; padding:5px; ">' . $mat_columns[$k] . '</td>';
          }
          $matrix .= '</tr>';										
          $aaa=Array();										
            for($k=1; $k<=$rows_count; $k++) {
              $matrix .= '<tr><td style="background-color:#BBBBBB; padding:5px;">' . $mat_rows[$k] . '</td>';										
              if($input_type=="radio") {
                $mat_radio = isset($_POST['wdform_'.$key."_input_element".$id.$k]) ? $_POST['wdform_'.$key."_input_element".$id.$k] : 0;											
                if($mat_radio == 0) {
                  $checked = "";
                  $aaa[1] = "";
                }
                else {
                  $aaa = explode("_", $mat_radio);
                }
                
                for($j = 1; $j <= $columns_count; $j++) {
                  if($aaa[1]==$j) {
                    $checked="checked";
                  }
                  else {
                    $checked="";
                  }
                  $matrix .= '<td style="text-align:center"><input  type="radio" ' . $checked . ' disabled /></td>';												
                }
              }
              else {
                if($input_type == "checkbox") {                
                  for($j = 1; $j <= $columns_count; $j++) {
                    $checked = isset($_POST['wdform_'.$key."_input_element".$id.$k.'_'.$j]) ? $_POST['wdform_'.$key."_input_element".$id.$k.'_'.$j] : 0;
                    if($checked==1) {
                      $checked = "checked";				
                    }
                    else {
                      $checked = "";
                    }
                    $matrix .= '<td style="text-align:center"><input  type="checkbox" ' . $checked . ' disabled /></td>';												
                  }
                }
                else {
                  if($input_type == "text") {																			  
                    for($j = 1; $j <= $columns_count; $j++) {
                      $checked = isset($_POST['wdform_'.$key."_input_element".$id.$k.'_'.$j]) ? $_POST['wdform_'.$key."_input_element".$id.$k.'_'.$j] : "";
                      $matrix .= '<td style="text-align:center"><input  type="text" value="' . $checked . '" disabled /></td>';											
                    }													
                  }
                  else {
                    for($j = 1; $j <= $columns_count; $j++) {
                      $checked = isset($_POST['wdform_'.$key."_select_yes_no".$id.$k.'_'.$j]) ? $_POST['wdform_'.$key."_select_yes_no".$id.$k.'_'.$j] : "";
                      $matrix .= '<td style="text-align:center">' . $checked . '</td>';
                    }
                  }
                }
              }
              $matrix .= '</tr>';
            }
            $matrix .= '</table>';
            if(isset($matrix)) {
              $new_value = $new_value . $matrix;
            }
          break;
        }
        default: break;
      }
      // $new_script = str_replace("%" . $label_each . "%", $new_value, $new_script);	
    }
    
    return $new_value;
  }

	public function empty_field($element, $mail_emptyfields) {		
		if(!$mail_emptyfields)
			if(empty($element))
				return 0;

		return 1;
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