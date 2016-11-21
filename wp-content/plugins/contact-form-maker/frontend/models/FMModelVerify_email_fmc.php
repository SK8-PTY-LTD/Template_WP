<?php

class FMModelVerify_email_fmc {
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
  function setValidation($gid, $md5, $email) {
		global $wpdb;
		$query = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."formmaker_submits WHERE group_id='%d' AND element_label= 'verifyInfo' AND element_value REGEXP 'verified'", $gid);
		$verified_row = $wpdb->get_row($query);

		if($verified_row)
			$view = __('Your email address is already verified.', 'form_maker');
		else
		{
			$query = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."formmaker_submits WHERE group_id='%d' AND element_label REGEXP 'verifyInfo' AND element_value  NOT REGEXP 'verified'", $gid);
			$rows = $wpdb->get_results($query);

			if (!$rows)
				return false;
				
			$view = '';	
			foreach($rows as $row){
				$verifyInfo = explode('**',$row->element_value);
				$key = $verifyInfo[0];
				$expHour = $verifyInfo[1];
				$recipiend = $verifyInfo[2];
			
				if($recipiend == $email) {
					$date = strtotime($row->date);
					if($key === $md5){
						if($expHour > 0){					
							$now = time();
							$hourInterval  = floor(($now - $date)/3600); //return hour
							if ($hourInterval <= $expHour) {
								$wpdb->update( 
									$wpdb->prefix."formmaker_submits",
									array( 
										'element_value' => 'verified**'.$recipiend,
										'element_label' => 'verifyInfo'
									), 
									array( 
										'group_id' => $gid ,
										'element_label' => 'verifyInfo@'.$recipiend
									), 
									array( 
										'%s',	
										'%s'
									), 
									array( '%d', 
										'%s'
									) 
								);
								$view = __('Your email has been successfully verified.', 'form_maker');
							}
							else							
								$view = __('Your email verification has timed out.', 'form_maker'); // 0 = time expired
								
						}
						else
						{
							$wpdb->update( 
								$wpdb->prefix."formmaker_submits",
								array( 
									'element_value' => 'verified**'.$recipiend,
									'element_label' => 'verifyInfo'
								), 
								array( 
									'group_id' => $gid ,
									'element_label' => 'verifyInfo@'.$recipiend
								), 
								array( 
									'%s',	
									'%s'
								), 
								array( '%d', 
									'%s'
								) 
							);
							$view = __('Your email has been successfully verified.', 'form_maker');
							
						}
					}
					else
						$view = __('Verification link is invalid.', 'form_maker'); //wrong code
						
					break;	
				}	
				else
					continue;
			}
		}
		return $view;
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