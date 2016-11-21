<?php

class FMControllerManage_fmc {
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
    $task = WDW_FMC_Library::get('task');
    $id = (int)WDW_FMC_Library::get('current_id', 0);
    $message = WDW_FMC_Library::get('message');
    echo WDW_FMC_Library::message_id($message);
    if (method_exists($this, $task)) {
		check_admin_referer('nonce_fmc', 'nonce_fmc');
		$this->$task($id);
    }
    else {
		$this->display();
    }
  }
public function undo()
{
    require_once WD_FMC_DIR . "/admin/models/FMModelManage_fmc.php";
    $model = new FMModelManage_fmc();

    require_once WD_FMC_DIR . "/admin/views/FMViewManage_fmc.php";

    global $wpdb;	
    $backup_id = (int)WDW_FMC_Library::get('backup_id');
    $id = (int)WDW_FMC_Library::get('id');
	
	$query = "SELECT backup_id FROM ".$wpdb->prefix."formmaker_backup WHERE backup_id < $backup_id AND id = $id ORDER BY backup_id DESC LIMIT 0 , 1 ";
    $backup_id = $wpdb->get_var($query);
	
    $view = new FMViewManage_fmc($model);
    $view->edit($backup_id);

}
public function redo()
{
    require_once WD_FMC_DIR . "/admin/models/FMModelManage_fmc.php";
    $model = new FMModelManage_fmc();

    require_once WD_FMC_DIR . "/admin/views/FMViewManage_fmc.php";
    global $wpdb;	
    $backup_id = (int)WDW_FMC_Library::get('backup_id');
    $id = (int)WDW_FMC_Library::get('id');
	
	$query = "SELECT backup_id FROM ".$wpdb->prefix."formmaker_backup WHERE backup_id > $backup_id AND id = $id ORDER BY backup_id ASC LIMIT 0 , 1 ";
    $backup_id = $wpdb->get_var($query);
 
	$view = new FMViewManage_fmc($model);
	$view->edit($backup_id);

}


  public function display() {
    require_once WD_FMC_DIR . "/admin/models/FMModelManage_fmc.php";
    $model = new FMModelManage_fmc();

    require_once WD_FMC_DIR . "/admin/views/FMViewManage_fmc.php";
    $view = new FMViewManage_fmc($model);
    $view->display();
  }

  public function add() {
    require_once WD_FMC_DIR . "/admin/models/FMModelManage_fmc.php";
    $model = new FMModelManage_fmc();

    require_once WD_FMC_DIR . "/admin/views/FMViewManage_fmc.php";
    $view = new FMViewManage_fmc($model);
    $view->edit(0);
  }

  public function edit() {
    require_once WD_FMC_DIR . "/admin/models/FMModelManage_fmc.php";
    $model = new FMModelManage_fmc();

    require_once WD_FMC_DIR . "/admin/views/FMViewManage_fmc.php";
    $view = new FMViewManage_fmc($model);
    $id = (int)WDW_FMC_Library::get('current_id', 0);
	
    global $wpdb;	
	$query = "SELECT backup_id FROM ".$wpdb->prefix."formmaker_backup WHERE cur=1 and id=".$id;
    $backup_id = $wpdb->get_var($query);
	
	if(!$backup_id)
	{
		$query = "SELECT max(backup_id) FROM ".$wpdb->prefix."formmaker_backup";
		$backup_id = $wpdb->get_var($query);
		if($backup_id)
			$backup_id++;
		else
			$backup_id=1;
		$query = "INSERT INTO ".$wpdb->prefix."formmaker_backup SELECT ".$backup_id." AS backup_id, 1 AS cur, formmakerbkup.id, formmakerbkup.title, formmakerbkup.mail, formmakerbkup.form_front, formmakerbkup.theme, formmakerbkup.javascript, formmakerbkup.submit_text, formmakerbkup.url, formmakerbkup.submit_text_type, formmakerbkup.script_mail, formmakerbkup.script_mail_user, formmakerbkup.counter, formmakerbkup.published, formmakerbkup.label_order, formmakerbkup.label_order_current, formmakerbkup.article_id, formmakerbkup.pagination, formmakerbkup.show_title, formmakerbkup.show_numbers, formmakerbkup.public_key, formmakerbkup.private_key, formmakerbkup.recaptcha_theme, formmakerbkup.paypal_mode, formmakerbkup.checkout_mode, formmakerbkup.paypal_email, formmakerbkup.payment_currency, formmakerbkup.tax, formmakerbkup.form_fields, formmakerbkup.savedb, formmakerbkup.sendemail, formmakerbkup.requiredmark, formmakerbkup.from_mail, formmakerbkup.from_name, formmakerbkup.reply_to, formmakerbkup.send_to, formmakerbkup.autogen_layout, formmakerbkup.custom_front, formmakerbkup.mail_from_user, formmakerbkup.mail_from_name_user, formmakerbkup.reply_to_user, formmakerbkup.condition, formmakerbkup.mail_cc, formmakerbkup.mail_cc_user, formmakerbkup.mail_bcc, formmakerbkup.mail_bcc_user, formmakerbkup.mail_subject, formmakerbkup.mail_subject_user, formmakerbkup.mail_mode, formmakerbkup.mail_mode_user, formmakerbkup.mail_attachment, formmakerbkup.mail_attachment_user, formmakerbkup.user_id_wd, formmakerbkup.sortable, formmakerbkup.frontend_submit_fields, formmakerbkup.frontend_submit_stat_fields, formmakerbkup.mail_emptyfields, formmakerbkup.mail_verify, formmakerbkup.mail_verify_expiretime, formmakerbkup.mail_verification_post_id, formmakerbkup.save_uploads FROM ".$wpdb->prefix."formmaker as formmakerbkup WHERE id=".$id;
		$wpdb->query($query);
	}		

    $view->edit($backup_id);
  }

  public function edit_old() {
    require_once WD_FMC_DIR . "/admin/models/FMModelManage_fmc.php";
    $model = new FMModelManage_fmc();

    require_once WD_FMC_DIR . "/admin/views/FMViewManage_fmc.php";
    $view = new FMViewManage_fmc($model);
    $id = (int)WDW_FMC_Library::get('current_id', 0);
    $view->edit_old($id);
  }

  public function form_options_old() {
    if (!isset($_GET['task'])) {
      $this->save_db();
    }
    require_once WD_FMC_DIR . "/admin/models/FMModelManage_fmc.php";
    $model = new FMModelManage_fmc();

    require_once WD_FMC_DIR . "/admin/views/FMViewManage_fmc.php";
    $view = new FMViewManage_fmc($model);
    global $wpdb;
    $id = (int)WDW_FMC_Library::get('current_id', $wpdb->get_var("SELECT MAX(id) FROM " . $wpdb->prefix . "formmaker"));
    $view->form_options_old($id);
  }

  public function save_options_old() {
    $message = $this->save_db_options_old();
    // $this->edit_old();
    $page = WDW_FMC_Library::get('page');
    $current_id = (int)WDW_FMC_Library::get('current_id', 0);
    WDW_FMC_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'edit_old', 'current_id' => $current_id, 'message' => $message), admin_url('admin.php')));
  }

  public function apply_options_old() {
    $message = $this->save_db_options_old();
    require_once WD_FMC_DIR . "/admin/models/FMModelManage_fmc.php";
    $model = new FMModelManage_fmc();

    require_once WD_FMC_DIR . "/admin/views/FMViewManage_fmc.php";
    $view = new FMViewManage_fmc($model);
    $page = WDW_FMC_Library::get('page');
    $current_id = (int)WDW_FMC_Library::get('current_id', 0);
    $fieldset_id = WDW_FMC_Library::get('fieldset_id', 'general');
    WDW_FMC_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'form_options_old', 'current_id' => $current_id, 'message' => $message, 'fieldset_id' => $fieldset_id), admin_url('admin.php')));
  }

  public function save_db_options_old() {
    $javascript = "// Before the form is loaded.
function before_load() {
  
}	
// Before form submit.
function before_submit() {
  
}	
// Before form reset.
function before_reset() {
  
}";
    global $wpdb;
    $id = (int)WDW_FMC_Library::get('current_id', 0);
    $mail = (isset($_POST['mail']) ? esc_html(stripslashes($_POST['mail'])) : '');
    $theme = (isset($_POST['theme']) ? (int)esc_html(stripslashes($_POST['theme'])) : 1);
    $javascript = (isset($_POST['javascript']) ? stripslashes($_POST['javascript']) : $javascript);
    $script1 = (isset($_POST['script1']) ? esc_html(stripslashes($_POST['script1'])) : '');
    $script2 = (isset($_POST['script2']) ? esc_html(stripslashes($_POST['script2'])) : '');
    $script_user1 = (isset($_POST['script_user1']) ? esc_html(stripslashes($_POST['script_user1'])) : '');
    $script_user2 = (isset($_POST['script_user2']) ? esc_html(stripslashes($_POST['script_user2'])) : '');
    $submit_text = (isset($_POST['submit_text']) ? stripslashes($_POST['submit_text']) : '');
    $url = (isset($_POST['url']) ? esc_html(stripslashes($_POST['url'])) : '');
    $script_mail = (isset($_POST['script_mail']) ? stripslashes($_POST['script_mail']) : '%all%');
    $script_mail_user = (isset($_POST['script_mail_user']) ? stripslashes($_POST['script_mail_user']) : '%all%');
    $label_order_current = (isset($_POST['label_order_current']) ? esc_html(stripslashes($_POST['label_order_current'])) : '');
    $tax = (isset($_POST['tax']) ? esc_html(stripslashes($_POST['tax'])) : 0);
    $payment_currency = (isset($_POST['payment_currency']) ? stripslashes($_POST['payment_currency']) : '');
    $paypal_email = (isset($_POST['paypal_email']) ? esc_html(stripslashes($_POST['paypal_email'])) : '');
    $checkout_mode = (isset($_POST['checkout_mode']) ? esc_html(stripslashes($_POST['checkout_mode'])) : 'testmode');
    $paypal_mode = (isset($_POST['paypal_mode']) ? esc_html(stripslashes($_POST['paypal_mode'])) : 0);
    $from_mail = (isset($_POST['from_mail']) ? esc_html(stripslashes($_POST['from_mail'])) : '');
    $from_name = (isset($_POST['from_name']) ? esc_html(stripslashes($_POST['from_name'])) : '');
    if (isset($_POST['submit_text_type'])) {
      $submit_text_type = esc_html(stripslashes($_POST['submit_text_type']));
      if ($submit_text_type == 5) {
        $article_id = (isset($_POST['page_name']) ? esc_html(stripslashes($_POST['page_name'])) : 0);
      }
      else {
        $article_id = (isset($_POST['post_name']) ? esc_html(stripslashes($_POST['post_name'])) : 0);
      }
    }
    else {
      $submit_text_type = 0;
      $article_id = 0;
    }
    $save = $wpdb->update($wpdb->prefix . 'formmaker', array(
      'mail' => $mail,
      'theme' => $theme,
      'javascript' => $javascript,
      'submit_text' => $submit_text,
      'url' => $url,
      'submit_text_type' => $submit_text_type,
      'script_mail' => $script_mail,
      'script_mail_user' => $script_mail_user,
      'article_id' => $article_id,
      'paypal_mode' => $paypal_mode,
      'checkout_mode' => $checkout_mode,
      'paypal_email' => $paypal_email,
      'payment_currency' => $payment_currency,
      'tax' => $tax,
      'from_mail' => $from_mail,
      'from_name' => $from_name,                  
    ), array('id' => $id));
    if ($save !== FALSE) {
      return 8;
    }
    else {
      return 2;
    }
  }

  public function cancel_options_old() {
    $this->edit_old();
  }

  public function form_layout() {
    if (!isset($_GET['task'])) {
      $this->save_db();
    }
    require_once WD_FMC_DIR . "/admin/models/FMModelManage_fmc.php";
    $model = new FMModelManage_fmc();

    require_once WD_FMC_DIR . "/admin/views/FMViewManage_fmc.php";
    $view = new FMViewManage_fmc($model);
    global $wpdb;
    $id = (int)WDW_FMC_Library::get('current_id', $wpdb->get_var("SELECT MAX(id) FROM " . $wpdb->prefix . "formmaker"));
    $view->form_layout($id);
  }

  public function save_layout() {
    $message = $this->save_db_layout();
    // $this->edit();
    $page = WDW_FMC_Library::get('page');
    $current_id = (int)WDW_FMC_Library::get('current_id', 0);
    WDW_FMC_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'edit', 'current_id' => $current_id, 'message' => $message), admin_url('admin.php')));
  }

  public function apply_layout() {
    $message = $this->save_db_layout();
    require_once WD_FMC_DIR . "/admin/models/FMModelManage_fmc.php";
    $model = new FMModelManage_fmc();

    require_once WD_FMC_DIR . "/admin/views/FMViewManage_fmc.php";
    $view = new FMViewManage_fmc($model);
    $page = WDW_FMC_Library::get('page');
    $current_id = (int)WDW_FMC_Library::get('current_id', 0);
    WDW_FMC_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'form_layout', 'current_id' => $current_id, 'message' => $message), admin_url('admin.php')));
    // $view->form_layout($id);
  }

  public function save_db_layout() {
    global $wpdb;
    $id = (int)WDW_FMC_Library::get('current_id', 0);
    $custom_front = (isset($_POST['custom_front']) ? stripslashes($_POST['custom_front']) : '');
    $autogen_layout = (isset($_POST['autogen_layout']) ? 1 : 0);
    $save = $wpdb->update($wpdb->prefix . 'formmaker', array(
      'custom_front' => $custom_front,
      'autogen_layout' => $autogen_layout
    ), array('id' => $id));
    if ($save !== FALSE) {
      return 1;
    }
    else {
      return 2;
    }
  }

  public function form_options() {
    if (!isset($_GET['task'])) {
      $this->save_db();
    }
    require_once WD_FMC_DIR . "/admin/models/FMModelManage_fmc.php";
    $model = new FMModelManage_fmc();

    require_once WD_FMC_DIR . "/admin/views/FMViewManage_fmc.php";
    $view = new FMViewManage_fmc($model);
    // $id = ((isset($_POST['current_id']) && esc_html($_POST['current_id']) != '') ? esc_html($_POST['current_id']) : 0);
    global $wpdb;
    $id = (int)WDW_FMC_Library::get('current_id', $wpdb->get_var("SELECT MAX(id) FROM " . $wpdb->prefix . "formmaker"));
    $view->form_options($id);
  }

  public function save_options() {
    $message = $this->save_db_options();
    // $this->edit();
    $page = WDW_FMC_Library::get('page');
    $current_id = (int)WDW_FMC_Library::get('current_id', 0);
    WDW_FMC_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'edit', 'current_id' => $current_id, 'message' => $message), admin_url('admin.php')));
  }

  public function apply_options() {
    $message = $this->save_db_options();
    require_once WD_FMC_DIR . "/admin/models/FMModelManage_fmc.php";
    $model = new FMModelManage_fmc();

    require_once WD_FMC_DIR . "/admin/views/FMViewManage_fmc.php";
    $view = new FMViewManage_fmc($model);
    // $id = ((isset($_POST['current_id']) && esc_html($_POST['current_id']) != '') ? esc_html($_POST['current_id']) : 0);
    // $view->form_options($id);
    $page = WDW_FMC_Library::get('page');
    $current_id = (int)WDW_FMC_Library::get('current_id', 0);
    $fieldset_id = WDW_FMC_Library::get('fieldset_id', 'general');
    WDW_FMC_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'form_options', 'current_id' => $current_id, 'message' => $message, 'fieldset_id' => $fieldset_id), admin_url('admin.php')));
  }

  public function remove_query() {
    global $wpdb;
    $cid = ((isset($_POST['cid']) && $_POST['cid'] != '') ? $_POST['cid'] : NULL); 
	if (count($cid)) {
      array_walk($cid, create_function('&$value', '$value = (int)$value;')); 
      $cids = implode(',', $cid);
      $query = 'DELETE FROM ' . $wpdb->prefix . 'formmaker_query WHERE id IN ( ' . $cids . ' )';
      if ($wpdb->query($query)) {
        echo WDW_FMC_Library::message('Items Succesfully Deleted.', 'updated');
      }
      else {
        echo WDW_FMC_Library::message('Error. Please install plugin again.', 'error');
      }
    }
    else {
      echo WDW_FMC_Library::message('You must select at least one item.', 'error');
    }
    $this->apply_options();
  }
  
  public function cancel_options() {
    $this->edit();
  }

  public function save_db_options() {
    $javascript = "// Before the form is loaded.
function before_load() {
  
}	
// Before form submit.
function before_submit() {
  
}	
// Before form reset.
function before_reset() {
  
}";
    global $wpdb;
    // $id = (isset($_POST['current_id']) ? (int) esc_html(stripslashes($_POST['current_id'])) : 0);
    $id = (int)WDW_FMC_Library::get('current_id', 0);
    $published = (isset($_POST['published']) ? esc_html(stripslashes($_POST['published'])) : 1);
    $savedb = (isset($_POST['savedb']) ? esc_html(stripslashes($_POST['savedb'])) : 1);
    $theme = (int)((isset($_POST['theme']) && (esc_html($_POST['theme']) != 0)) ? esc_html(stripslashes($_POST['theme'])) : $wpdb->get_var("SELECT id FROM " . $wpdb->prefix . "formmaker_themes WHERE `default`='1'"));
    $requiredmark = (isset($_POST['requiredmark']) ? esc_html(stripslashes($_POST['requiredmark'])) : '*');
    $sendemail = (isset($_POST['sendemail']) ? esc_html(stripslashes($_POST['sendemail'])) : 1);
    $save_uploads = (isset($_POST['save_uploads']) ? esc_html(stripslashes($_POST['save_uploads'])) : 1);
    $mail = (isset($_POST['mail']) ? esc_html(stripslashes($_POST['mail'])) : '');
    if (isset($_POST['mailToAdd']) && esc_html(stripslashes($_POST['mailToAdd'])) != '') {
      $mail .= esc_html(stripslashes($_POST['mailToAdd'])) . ',';
    }
    $from_mail = (isset($_POST['from_mail']) ? esc_html(stripslashes($_POST['from_mail'])) : '');
    $from_name = (isset($_POST['from_name']) ? esc_html(stripslashes($_POST['from_name'])) : '');
    $reply_to = (isset($_POST['reply_to']) ? esc_html(stripslashes($_POST['reply_to'])) : '');
    if ($from_mail == "other") {
      $from_mail = (isset($_POST['mail_from_other']) ? esc_html(stripslashes($_POST['mail_from_other'])) : '');
    }
    if ($reply_to == "other") {
      $reply_to = (isset($_POST['reply_to_other']) ? esc_html(stripslashes($_POST['reply_to_other'])) : '');
    }
    $script_mail = (isset($_POST['script_mail']) ? stripslashes($_POST['script_mail']) : '%all%');
    $mail_from_user = (isset($_POST['mail_from_user']) ? esc_html(stripslashes($_POST['mail_from_user'])) : '');
    $mail_from_name_user = (isset($_POST['mail_from_name_user']) ? esc_html(stripslashes($_POST['mail_from_name_user'])) : '');
    $reply_to_user = (isset($_POST['reply_to_user']) ? esc_html(stripslashes($_POST['reply_to_user'])) : '');
    $condition = (isset($_POST['condition']) ? esc_html(stripslashes($_POST['condition'])) : '');
    $mail_cc = (isset($_POST['mail_cc']) ? esc_html(stripslashes($_POST['mail_cc'])) : '');
    $mail_cc_user = (isset($_POST['mail_cc_user']) ? esc_html(stripslashes($_POST['mail_cc_user'])) : '');
    $mail_bcc = (isset($_POST['mail_bcc']) ? esc_html(stripslashes($_POST['mail_bcc'])) : '');
    $mail_bcc_user = (isset($_POST['mail_bcc_user']) ? esc_html(stripslashes($_POST['mail_bcc_user'])) : '');
    $mail_subject = (isset($_POST['mail_subject']) ? esc_html(stripslashes($_POST['mail_subject'])) : '');
    $mail_subject_user = (isset($_POST['mail_subject_user']) ? esc_html(stripslashes($_POST['mail_subject_user'])) : '');
    $mail_mode = (isset($_POST['mail_mode']) ? esc_html(stripslashes($_POST['mail_mode'])) : 1);
    $mail_mode_user = (isset($_POST['mail_mode_user']) ? esc_html(stripslashes($_POST['mail_mode_user'])) : 1);
    $mail_attachment = (isset($_POST['mail_attachment']) ? esc_html(stripslashes($_POST['mail_attachment'])) : 1);
    $mail_attachment_user = (isset($_POST['mail_attachment_user']) ? esc_html(stripslashes($_POST['mail_attachment_user'])) : 1);
    $script_mail_user = (isset($_POST['script_mail_user']) ? stripslashes($_POST['script_mail_user']) : '%all%');
    $submit_text = (isset($_POST['submit_text']) ? stripslashes($_POST['submit_text']) : '');
    $url = (isset($_POST['url']) ? esc_html(stripslashes($_POST['url'])) : '');
    $tax = (isset($_POST['tax']) ? esc_html(stripslashes($_POST['tax'])) : 0);
    $payment_currency = (isset($_POST['payment_currency']) ? stripslashes($_POST['payment_currency']) : '');
    $paypal_email = (isset($_POST['paypal_email']) ? esc_html(stripslashes($_POST['paypal_email'])) : '');
    $checkout_mode = (isset($_POST['checkout_mode']) ? esc_html(stripslashes($_POST['checkout_mode'])) : 'testmode');
    $paypal_mode = (isset($_POST['paypal_mode']) ? esc_html(stripslashes($_POST['paypal_mode'])) : 0);
    $javascript = (isset($_POST['javascript']) ? stripslashes($_POST['javascript']) : $javascript);
    $user_id_wd = (isset($_POST['user_id_wd']) ? stripslashes($_POST['user_id_wd']) : 'administrator,');
    $frontend_submit_fields = (isset($_POST['frontend_submit_fields']) ? stripslashes($_POST['frontend_submit_fields']) : '');
    $frontend_submit_stat_fields = (isset($_POST['frontend_submit_stat_fields']) ? stripslashes($_POST['frontend_submit_stat_fields']) : '');
	$mail_emptyfields = (isset($_POST['mail_emptyfields']) ? esc_html(stripslashes($_POST['mail_emptyfields'])) : 0);
	$mail_verify = (isset($_POST['mail_verify']) ? esc_html(stripslashes($_POST['mail_verify'])) : 0);
	$mail_verify_expiretime = (isset($_POST['mail_verify_expiretime']) ? esc_html(stripslashes($_POST['mail_verify_expiretime'])) : '');
    $send_to = '';
    for ($i = 0; $i < 20; $i++) {
      if (isset($_POST['send_to' . $i])) {
        $send_to .= '*' . esc_html(stripslashes($_POST['send_to' . $i])) . '*';
      }
    }
    if (isset($_POST['submit_text_type'])) {
      $submit_text_type = esc_html(stripslashes($_POST['submit_text_type']));
      if ($submit_text_type == 5) {
        $article_id = (isset($_POST['page_name']) ? esc_html(stripslashes($_POST['page_name'])) : 0);
      }
      else {
        $article_id = (isset($_POST['post_name']) ? esc_html(stripslashes($_POST['post_name'])) : 0);
      }
    }
    else {
      $submit_text_type = 0;
      $article_id = 0;
    }
	
	$mail_verification_post_id = (int)$wpdb->get_var('SELECT mail_verification_post_id FROM ' . $wpdb->prefix . 'formmaker WHERE mail_verification_post_id!=0');
	if($mail_verify) {
		$email_verification_post = array(
		  'post_title'    => 'Email Verification',
		  'post_content'  => '[email_verification]',
		  'post_status'   => 'publish',
		  'post_author'   => 1,
		  'post_type'   => 'fmemailverification',
		);

		if(!$mail_verification_post_id || get_post( $mail_verification_post_id )===NULL)
			$mail_verification_post_id = wp_insert_post( $email_verification_post );
	}
	
    $save = $wpdb->update($wpdb->prefix . 'formmaker', array(
      'published' => $published,
      'savedb' => $savedb,
      'theme' => $theme,
      'requiredmark' => $requiredmark,
      'sendemail' => $sendemail,
      'save_uploads' => $save_uploads,
      'mail' => $mail,
      'from_mail' => $from_mail,
      'from_name' => $from_name,
      'reply_to' => $reply_to,
      'script_mail' => $script_mail,
      'mail_from_user' => $mail_from_user,
      'mail_from_name_user' => $mail_from_name_user,
      'reply_to_user' => $reply_to_user,
      'condition' => $condition,
      'mail_cc' => $mail_cc,
      'mail_cc_user' => $mail_cc_user,
      'mail_bcc' => $mail_bcc,
      'mail_bcc_user' => $mail_bcc_user,
      'mail_subject' => $mail_subject,
      'mail_subject_user' => $mail_subject_user,
      'mail_mode' => $mail_mode,
      'mail_mode_user' => $mail_mode_user,
      'mail_attachment' => $mail_attachment,
      'mail_attachment_user' => $mail_attachment_user,
      'script_mail_user' => $script_mail_user,
      'submit_text' => $submit_text,
      'url' => $url,
      'submit_text_type' => $submit_text_type,
      'article_id' => $article_id,
      'tax' => $tax,
      'payment_currency' => $payment_currency,
      'paypal_email' => $paypal_email,
      'checkout_mode' => $checkout_mode,
      'paypal_mode' => $paypal_mode,
      'javascript' => $javascript,
      'user_id_wd' => $user_id_wd,
      'send_to' => $send_to,
      'frontend_submit_fields' => $frontend_submit_fields,
      'frontend_submit_stat_fields' => $frontend_submit_stat_fields,
	  'mail_emptyfields' => $mail_emptyfields,
	  'mail_verify' => $mail_verify,
	  'mail_verify_expiretime' => $mail_verify_expiretime,
	  'mail_verification_post_id' => $mail_verification_post_id,
    ), array('id' => $id));
    if ($save !== FALSE) {
		$save_theme_in_backup = $wpdb->update($wpdb->prefix . 'formmaker_backup', array(
			'theme' => $theme
		), array('id' => $id));
		return 8;
    }
    else {
		return 2;
    }
  }

  public function save_as_copy_old() {
    $message = $this->save_db_as_copy_old();
    // $this->display();
    $page = WDW_FMC_Library::get('page');
    WDW_FMC_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'display', 'message' => $message), admin_url('admin.php')));
  }

  public function save_old() {
    $message = $this->save_db_old();
    // $this->display();
    $page = WDW_FMC_Library::get('page');
    WDW_FMC_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'display', 'message' => $message), admin_url('admin.php')));
  }

  public function apply_old() {
    global $wpdb;
    $message = $this->save_db_old();
    // $this->edit_old();
    $id = (int) $wpdb->get_var("SELECT MAX(id) FROM " . $wpdb->prefix . "formmaker");
    $current_id = (int)WDW_FMC_Library::get('current_id', $id);
    $page = WDW_FMC_Library::get('page');
    WDW_FMC_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'edit_old', 'current_id' => $current_id, 'message' => $message), admin_url('admin.php')));
  }

  public function save_db_old() {
    global $wpdb;
    // $id = (isset($_POST['current_id']) ? (int) esc_html(stripslashes($_POST['current_id'])) : 0);
    $id = (int)WDW_FMC_Library::get('current_id', 0);
    $title = (isset($_POST['title']) ? esc_html(stripslashes($_POST['title'])) : '');
    $form = (isset($_POST['form']) ? stripslashes($_POST['form']) : '');
    $form_front = (isset($_POST['form_front']) ? stripslashes($_POST['form_front']) : '');
    $counter = (isset($_POST['counter']) ? esc_html(stripslashes($_POST['counter'])) : 0);
    $label_order = (isset($_POST['label_order']) ? esc_html(stripslashes($_POST['label_order'])) : '');
    $label_order_current = (isset($_POST['label_order_current']) ? esc_html(stripslashes($_POST['label_order_current'])) : '');
    $pagination = (isset($_POST['pagination']) ? esc_html(stripslashes($_POST['pagination'])) : '');
    $show_title = (isset($_POST['show_title']) ? esc_html(stripslashes($_POST['show_title'])) : '');
    $show_numbers = (isset($_POST['show_numbers']) ? esc_html(stripslashes($_POST['show_numbers'])) : '');
    $public_key = (isset($_POST['public_key']) ? esc_html(stripslashes($_POST['public_key'])) : '');
    $private_key = (isset($_POST['private_key']) ? esc_html(stripslashes($_POST['private_key'])) : '');
    $recaptcha_theme = (isset($_POST['recaptcha_theme']) ? esc_html(stripslashes($_POST['recaptcha_theme'])) : '');

    $save = $wpdb->update($wpdb->prefix . 'formmaker', array(
      'title' => $title,
      'form' => $form,
      'form_front' => $form_front,
      'counter' => $counter,
      'label_order' => $label_order,
      'label_order_current' => $label_order_current,
      'pagination' => $pagination,
      'show_title' => $show_title,
      'show_numbers' => $show_numbers,
      'public_key' => $public_key,
      'private_key' => $private_key,
      'recaptcha_theme' => $recaptcha_theme,                   
    ), array('id' => $id));
    if ($save !== FALSE) {
      return 1;
    }
    else {
      return 2;
    }
  }

  public function save_db_as_copy_old() {
    global $wpdb;
    // $id = (isset($_POST['current_id']) ? (int) esc_html(stripslashes($_POST['current_id'])) : 0);
    $id = (int)WDW_FMC_Library::get('current_id', 0);
    $row = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'formmaker WHERE id="%d"', $id));
    $title = (isset($_POST['title']) ? esc_html(stripslashes($_POST['title'])) : '');
    $form = (isset($_POST['form']) ? stripslashes($_POST['form']) : '');
    $form_front = (isset($_POST['form_front']) ? stripslashes($_POST['form_front']) : '');
    $counter = (isset($_POST['counter']) ? esc_html(stripslashes($_POST['counter'])) : 0);
    $label_order = (isset($_POST['label_order']) ? esc_html(stripslashes($_POST['label_order'])) : '');
    $pagination = (isset($_POST['pagination']) ? esc_html(stripslashes($_POST['pagination'])) : '');
    $show_title = (isset($_POST['show_title']) ? esc_html(stripslashes($_POST['show_title'])) : '');
    $show_numbers = (isset($_POST['show_numbers']) ? esc_html(stripslashes($_POST['show_numbers'])) : '');
    $public_key = (isset($_POST['public_key']) ? esc_html(stripslashes($_POST['public_key'])) : '');
    $private_key = (isset($_POST['private_key']) ? esc_html(stripslashes($_POST['private_key'])) : '');
    $recaptcha_theme = (isset($_POST['recaptcha_theme']) ? esc_html(stripslashes($_POST['recaptcha_theme'])) : '');

    $save = $wpdb->insert($wpdb->prefix . 'formmaker', array(
      'title' => $title,
      'mail' => $row->mail,
      'form' => $form,
      'form_front' => $form_front,
      'theme' => $row->theme,
      'counter' => $counter,
      'label_order' => $label_order,
      'pagination' => $pagination,
      'show_title' => $show_title,
      'show_numbers' => $show_numbers,
      'public_key' => $public_key,
      'private_key' => $private_key,
      'recaptcha_theme' => $recaptcha_theme,
      'javascript' => $row->javascript,
      'script1' => $row->script1,
      'script2' => $row->script2,
      'script_user1' => $row->script_user1,
      'script_user2' => $row->script_user2,
      'submit_text' => $row->submit_text,
      'url' => $row->url,
      'article_id' => $row->article_id,
      'submit_text_type' => $row->submit_text_type,
      'script_mail' => $row->script_mail,
      'script_mail_user' => $row->script_mail_user,
      'paypal_mode' => $row->paypal_mode,
      'checkout_mode' => $row->checkout_mode,
      'paypal_email' => $row->paypal_email,
      'payment_currency' => $row->payment_currency,
      'tax' => $row->tax,
      'label_order_current' => $row->label_order_current,
      'from_mail' => $row->from_mail,
      'from_name' => $row->from_name,
      'reply_to_user' => $row->reply_to_user,
      'mail_from_name_user' => $row->mail_from_name_user,
      'mail_from_user' => $row->mail_from_user,
      'custom_front' => $row->custom_front,
      'autogen_layout' => $row->autogen_layout,
      'send_to' => $row->send_to,
      'reply_to' => $row->reply_to,
      'requiredmark' => $row->requiredmark,
      'sendemail' => $row->sendemail,
      'savedb' => $row->savedb,
      'form_fields' => $row->form_fields,
      'published' => $row->published,
      'save_uploads' => $row->save_uploads
    ), array(
      '%s',
      '%s',
      '%s',
      '%s',
      '%d',
      '%d',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%d',
      '%s',
      '%s',
      '%d',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%d',
      '%s',
      '%s',
      '%s',
      '%d',
      '%d',
      '%s',
      '%d'
    ));
    $id = (int)$wpdb->get_var("SELECT MAX(id) FROM " . $wpdb->prefix . "formmaker");
    update_option('contact_form_forms', ((get_option('contact_form_forms')) ? (get_option('contact_form_forms')) . ',' . $id : $id));
    $wpdb->insert($wpdb->prefix . 'formmaker_views', array(
      'form_id' => $id,
      'views' => 0
      ), array(
        '%d',
        '%d'
    ));
    if ($save !== FALSE) {
      return 1;
    }
    else {
      return 2;
    }
  }

  public function save_as_copy() {
    $message = $this->save_db_as_copy();
    // $this->display();
    $page = WDW_FMC_Library::get('page');
    WDW_FMC_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'display', 'message' => $message), admin_url('admin.php')));
  }

  public function save() {
    $message = $this->save_db();
    // $this->display();
    $page = WDW_FMC_Library::get('page');
    WDW_FMC_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'display', 'message' => $message), admin_url('admin.php')));
  }

  public function apply() {
    $message = $this->save_db();
    // $this->edit();
    global $wpdb;
    $id = (int) $wpdb->get_var("SELECT MAX(id) FROM " . $wpdb->prefix . "formmaker");
    $current_id = (int)WDW_FMC_Library::get('current_id', $id);
    $page = WDW_FMC_Library::get('page');
    WDW_FMC_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'edit', 'current_id' => $current_id, 'message' => $message), admin_url('admin.php')));
  }

  public function save_db() {
    global $wpdb;
    $javascript = "// before form is load
function before_load() {	
}	
// before form submit
function before_submit() {
}	
// before form reset
function before_reset() {	
}";
    $id = (int)WDW_FMC_Library::get('current_id', 0);
    $title = (isset($_POST['title']) ? esc_html(stripslashes($_POST['title'])) : '');
	$theme = (isset($_POST['theme']) ? esc_html(stripslashes($_POST['theme'])) : $wpdb->get_var("SELECT id FROM " . $wpdb->prefix . "formmaker_themes WHERE `default`='1'"));
    $form_front = (isset($_POST['form_front']) ? stripslashes($_POST['form_front']) : '');
    $sortable = (isset($_POST['sortable']) ? stripslashes($_POST['sortable']) : 1);
    $counter = (isset($_POST['counter']) ? esc_html(stripslashes($_POST['counter'])) : 0);
    $label_order = (isset($_POST['label_order']) ? esc_html(stripslashes($_POST['label_order'])) : '');
    $pagination = (isset($_POST['pagination']) ? esc_html(stripslashes($_POST['pagination'])) : '');
    $show_title = (isset($_POST['show_title']) ? esc_html(stripslashes($_POST['show_title'])) : '');
    $show_numbers = (isset($_POST['show_numbers']) ? esc_html(stripslashes($_POST['show_numbers'])) : '');
    $public_key = (isset($_POST['public_key']) ? esc_html(stripslashes($_POST['public_key'])) : '');
    $private_key = (isset($_POST['private_key']) ? esc_html(stripslashes($_POST['private_key'])) : '');
    $recaptcha_theme = (isset($_POST['recaptcha_theme']) ? esc_html(stripslashes($_POST['recaptcha_theme'])) : '');
    $label_order_current = (isset($_POST['label_order_current']) ? esc_html(stripslashes($_POST['label_order_current'])) : '');
    $form_fields = (isset($_POST['form_fields']) ? stripslashes($_POST['form_fields']) : '');
    if ($id != 0) {
      $save = $wpdb->update($wpdb->prefix . 'formmaker', array(
        'title' => $title,
        'theme' => $theme,
        'form_front' => $form_front,
        'sortable' => $sortable,
        'counter' => $counter,
        'label_order' => $label_order,
        'label_order_current' => $label_order_current,
        'pagination' => $pagination,
        'show_title' => $show_title,
        'show_numbers' => $show_numbers,
        'public_key' => $public_key,
        'private_key' => $private_key,
        'recaptcha_theme' => $recaptcha_theme,
        'form_fields' => $form_fields,
      ), array('id' => $id));
    }
    else {
      $save = $wpdb->insert($wpdb->prefix . 'formmaker', array(
        'title' => $title,
        'mail' => '',
        'form_front' => $form_front,
        'theme' => $theme,
        'counter' => $counter,
        'label_order' => $label_order,
        'pagination' => $pagination,
        'show_title' => $show_title,
        'show_numbers' => $show_numbers,
        'public_key' => $public_key,
        'private_key' => $private_key,
        'recaptcha_theme' => $recaptcha_theme,
        'javascript' => $javascript,
        'submit_text' => '',
        'url' => '',
        'article_id' => 0,
        'submit_text_type' => 0,
        'script_mail' => '%all%',
        'script_mail_user' => '%all%',
        'label_order_current' => $label_order_current,
        'tax' => 0,
        'payment_currency' => '',
        'paypal_email' => '',
        'checkout_mode' => 'testmode',
        'paypal_mode' => 0,
        'published' => 1,
        'form_fields' => $form_fields,
        'savedb' => 1,
        'sendemail' => 1,
        'requiredmark' => '*',
        'from_mail' => '',
        'from_name' => '',
        'reply_to' => '',
        'send_to' => '',
        'autogen_layout' => 1,
        'custom_front' => '',
        'mail_from_user' => '',
        'mail_from_name_user' => '',
        'reply_to_user' => '',
        'condition' => '',
        'mail_cc' => '',
        'mail_cc_user' => '',
        'mail_bcc' => '',
        'mail_bcc_user' => '',
        'mail_subject' => '',
        'mail_subject_user' => '',
        'mail_mode' => 1,
        'mail_mode_user' => 1,
        'mail_attachment' => 1,
        'mail_attachment_user' => 1,
        'sortable' => $sortable,
        'user_id_wd' => 'administrator,',
        'frontend_submit_fields' => '',
        'frontend_submit_stat_fields' => '',
        'save_uploads' => 1,
      ), array(
				'%s',
        '%s',
        '%s',
        '%d',
        '%d',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%d',
        '%d',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%d',
        '%d',
        '%s',
        '%d',
        '%d',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%d',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%d',
        '%d',
        '%d',
        '%d',
        '%d',
        '%s',
        '%s',
        '%s',
        '%d',
      ));
      $id = (int)$wpdb->get_var("SELECT MAX(id) FROM " . $wpdb->prefix . "formmaker");
      update_option('contact_form_forms', ((get_option('contact_form_forms')) ? (get_option('contact_form_forms')) . ',' . $id : $id));
      // $_POST['current_id'] = $id;
      $wpdb->insert($wpdb->prefix . 'formmaker_views', array(
        'form_id' => $id,
        'views' => 0
        ), array(
          '%d',
          '%d'
      ));
    }
	
    $backup_id = (isset($_POST['backup_id']) ? (int)esc_html(stripslashes($_POST['backup_id'])) : '');
	
	if($backup_id)
	{
		$query = "SELECT backup_id FROM ".$wpdb->prefix."formmaker_backup WHERE backup_id > ".$backup_id." AND id = ".$id." ORDER BY backup_id ASC LIMIT 0 , 1 ";

		if($wpdb->get_var($query))
		{
			$query = "DELETE FROM ".$wpdb->prefix."formmaker_backup WHERE backup_id > ".$backup_id." AND id = ".$id;
			$wpdb->query($query);
		}

		$row = $wpdb->get_row($wpdb->prepare("SELECT form_fields, form_front FROM ".$wpdb->prefix."formmaker_backup WHERE backup_id = '%d'", $backup_id));

		if($row->form_fields==$form_fields and $row->form_front==$form_front)
		{
			  $save = $wpdb->update($wpdb->prefix . 'formmaker_backup', array(
				'cur' => 1,
				'title' => $title,
				'theme' => $theme,
				'form_front' => $form_front,
				'sortable' => $sortable,
				'counter' => $counter,
				'label_order' => $label_order,
				'label_order_current' => $label_order_current,
				'pagination' => $pagination,
				'show_title' => $show_title,
				'show_numbers' => $show_numbers,
				'public_key' => $public_key,
				'private_key' => $private_key,
				'recaptcha_theme' => $recaptcha_theme,
				'form_fields' => $form_fields,
			  ), array('backup_id' => $backup_id));
			
		
			if ($save !== FALSE) {
			  return 1;
			}
			else {
			  return 2;
			}
		}
	}
	
	$wpdb->query("UPDATE ".$wpdb->prefix."formmaker_backup SET cur=0 WHERE id=".$id ); 

	$save = $wpdb->insert($wpdb->prefix . 'formmaker_backup', array(
        'cur' => 1,
        'id' => $id,
        'title' => $title,
        'mail' => '',
        'form_front' => $form_front,
        'theme' => $theme,
        'counter' => $counter,
        'label_order' => $label_order,
        'pagination' => $pagination,
        'show_title' => $show_title,
        'show_numbers' => $show_numbers,
        'public_key' => $public_key,
        'private_key' => $private_key,
        'recaptcha_theme' => $recaptcha_theme,
        'javascript' => $javascript,
        'submit_text' => '',
        'url' => '',
        'article_id' => 0,
        'submit_text_type' => 0,
        'script_mail' => '%all%',
        'script_mail_user' => '%all%',
        'label_order_current' => $label_order_current,
        'tax' => 0,
        'payment_currency' => '',
        'paypal_email' => '',
        'checkout_mode' => 'testmode',
        'paypal_mode' => 0,
        'published' => 1,
        'form_fields' => $form_fields,
        'savedb' => 1,
        'sendemail' => 1,
        'requiredmark' => '*',
        'from_mail' => '',
        'from_name' => '',
        'reply_to' => '',
        'send_to' => '',
        'autogen_layout' => 1,
        'custom_front' => '',
        'mail_from_user' => '',
        'mail_from_name_user' => '',
        'reply_to_user' => '',
        'condition' => '',
        'mail_cc' => '',
        'mail_cc_user' => '',
        'mail_bcc' => '',
        'mail_bcc_user' => '',
        'mail_subject' => '',
        'mail_subject_user' => '',
        'mail_mode' => 1,
        'mail_mode_user' => 1,
        'mail_attachment' => 1,
        'mail_attachment_user' => 1,
        'sortable' => $sortable,
        'user_id_wd' => 'administrator,',
        'frontend_submit_fields' => '',
        'frontend_submit_stat_fields' => '',
      ), array(
        '%d',
        '%d',
		'%s',
        '%s',
        '%s',
        '%d',
        '%d',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%d',
        '%d',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%d',
        '%d',
        '%s',
        '%d',
        '%d',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%d',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%d',
        '%d',
        '%d',
        '%d',
        '%d',
        '%s',
        '%s',
        '%s',
     ))	;
  
	$query = "SELECT count(backup_id) FROM ".$wpdb->prefix."formmaker_backup WHERE id = ".$id;
	$wpdb->get_var($query);
	if($wpdb->get_var($query)>10)
	{
		$query = "DELETE FROM ".$wpdb->prefix."formmaker_backup WHERE id = ".$id." ORDER BY backup_id ASC LIMIT 1 ";
		$wpdb->query($query);
	}

    if ($save !== FALSE) {
      return 1;
    }
    else {
      return 2;
    }
  }

  public function save_db_as_copy() {
    global $wpdb;
    // $id = (isset($_POST['current_id']) ? (int) esc_html(stripslashes($_POST['current_id'])) : 0);
    $id = (int)WDW_FMC_Library::get('current_id', 0);
    $row = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'formmaker WHERE id="%d"', $id));
    $title = (isset($_POST['title']) ? esc_html(stripslashes($_POST['title'])) : '');
    $form_front = (isset($_POST['form_front']) ? stripslashes($_POST['form_front']) : '');
    $sortable = (isset($_POST['sortable']) ? stripslashes($_POST['sortable']) : 1);
    $counter = (isset($_POST['counter']) ? esc_html(stripslashes($_POST['counter'])) : 0);
    $label_order = (isset($_POST['label_order']) ? esc_html(stripslashes($_POST['label_order'])) : '');
    $label_order_current = (isset($_POST['label_order_current']) ? esc_html(stripslashes($_POST['label_order_current'])) : '');
    $pagination = (isset($_POST['pagination']) ? esc_html(stripslashes($_POST['pagination'])) : '');
    $show_title = (isset($_POST['show_title']) ? esc_html(stripslashes($_POST['show_title'])) : '');
    $show_numbers = (isset($_POST['show_numbers']) ? esc_html(stripslashes($_POST['show_numbers'])) : '');
    $public_key = (isset($_POST['public_key']) ? esc_html(stripslashes($_POST['public_key'])) : '');
    $private_key = (isset($_POST['private_key']) ? esc_html(stripslashes($_POST['private_key'])) : '');
    $recaptcha_theme = (isset($_POST['recaptcha_theme']) ? esc_html(stripslashes($_POST['recaptcha_theme'])) : '');
    $form_fields = (isset($_POST['form_fields']) ? stripslashes($_POST['form_fields']) : '');

    $save = $wpdb->insert($wpdb->prefix . 'formmaker', array(
      'title' => $title,
      'mail' => $row->mail,
      'form_front' => $form_front,
      'theme' => $row->theme,
      'counter' => $counter,
      'label_order' => $label_order,
      'pagination' => $pagination,
      'show_title' => $show_title,
      'show_numbers' => $show_numbers,
      'public_key' => $public_key,
      'private_key' => $private_key,
      'recaptcha_theme' => $recaptcha_theme,
      'javascript' => $row->javascript,
      'submit_text' => $row->submit_text,
      'url' => $row->url,
      'article_id' => $row->article_id,
      'submit_text_type' => $row->submit_text_type,
      'script_mail' => $row->script_mail,
      'script_mail_user' => $row->script_mail_user,
      'label_order_current' => $label_order_current,
      'tax' => $row->tax,
      'payment_currency' => $row->payment_currency,
      'paypal_email' => $row->paypal_email,
      'checkout_mode' => $row->checkout_mode,
      'paypal_mode' => $row->paypal_mode,
      'published' => $row->published,
      'form_fields' => $form_fields,
      'savedb' => $row->savedb,
      'sendemail' => $row->sendemail,
      'requiredmark' => $row->requiredmark,
      'from_mail' => $row->from_mail,
      'from_name' => $row->from_name,
      'reply_to' => $row->reply_to,
      'send_to' => $row->send_to,
      'autogen_layout' => $row->autogen_layout,
      'custom_front' => $row->custom_front,
      'mail_from_user' => $row->mail_from_user,
      'mail_from_name_user' => $row->mail_from_name_user,
      'reply_to_user' => $row->reply_to_user,
      'condition' => $row->condition,
      'mail_cc' => $row->mail_cc,
      'mail_cc_user' => $row->mail_cc_user,
      'mail_bcc' => $row->mail_bcc,
      'mail_bcc_user' => $row->mail_bcc_user,
      'mail_subject' => $row->mail_subject,
      'mail_subject_user' => $row->mail_subject_user,
      'mail_mode' => $row->mail_mode,
      'mail_mode_user' => $row->mail_mode_user,
      'mail_attachment' => $row->mail_attachment,
      'mail_attachment_user' => $row->mail_attachment_user,
      'sortable' => $sortable,
      'user_id_wd' => $row->user_id_wd,
      'frontend_submit_fields' => $row->frontend_submit_fields,
      'frontend_submit_stat_fields' => $row->frontend_submit_stat_fields,
      'save_uploads' => $row->save_uploads,
    ), array(
      '%s',
      '%s',
      '%s',
      '%d',
      '%d',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%d',
      '%d',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%d',
      '%d',
      '%s',
      '%d',
      '%d',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%d',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%d',
      '%d',
      '%d',
      '%d',
      '%d',
      '%s',
      '%s',
      '%s',
      '%d',
    ));
    $new_id = (int)$wpdb->get_var("SELECT MAX(id) FROM " . $wpdb->prefix . "formmaker");
    update_option('contact_form_forms', ((get_option('contact_form_forms')) ? (get_option('contact_form_forms')) . ',' . $id : $id));
    $wpdb->insert($wpdb->prefix . 'formmaker_views', array(
      'form_id' => $new_id,
      'views' => 0
      ), array(
        '%d',
        '%d'
    ));
    if ($save !== FALSE) {
		
		return 1;
    }
    else {
      return 2;
    }
  }

  public function delete($id) {
    global $wpdb;
    $query = $wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'formmaker WHERE id="%d"', $id);
    if ($wpdb->query($query)) {
      $arr = explode(',', get_option('contact_form_forms'));
      $arr = array_diff($arr, array($id));
      $arr = implode(',', $arr);
      update_option('contact_form_forms', $arr);
      $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'formmaker_views WHERE form_id="%d"', $id));
      $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'formmaker_submits WHERE form_id="%d"', $id));
	  
      $message = 3;
    }
    else {
      $message = 2;
    }
    // $this->display();
    $page = WDW_FMC_Library::get('page');
    WDW_FMC_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'display', 'message' => $message), admin_url('admin.php')));
  }
  
  public function delete_all() {
    global $wpdb;
    $flag = FALSE;
    $isDefault = FALSE;
    $form_ids_col = $wpdb->get_col('SELECT id FROM ' . $wpdb->prefix . 'formmaker');
    foreach ($form_ids_col as $form_id) {
      if (isset($_POST['check_' . $form_id])) {
        $flag = TRUE;
        $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'formmaker WHERE id="%d"', $form_id));
        $arr = explode(',', get_option('contact_form_forms'));
        $arr = array_diff($arr, array($form_id));
        $arr = implode(',', $arr);
        update_option('contact_form_forms', $arr);
        $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'formmaker_views WHERE form_id="%d"', $form_id));
        $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'formmaker_submits WHERE form_id="%d"', $form_id));
      }
    }
    if ($flag) {
      $message = 5;
    }
    else {
      $message = 6;
    }
    // $this->display();
    $page = WDW_FMC_Library::get('page');
    WDW_FMC_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'display', 'message' => $message), admin_url('admin.php')));
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