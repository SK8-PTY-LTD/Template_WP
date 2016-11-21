<?php

class FMControllerBlocked_ips_fmc {
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

  public function display() {
    require_once WD_FMC_DIR . "/admin/models/FMModelBlocked_ips_fmc.php";
    $model = new FMModelBlocked_ips_fmc();

    require_once WD_FMC_DIR . "/admin/views/FMViewBlocked_ips_fmc.php";
    $view = new FMViewBlocked_ips_fmc($model);
    $view->display();
  }

  public function save() {
    $message = $this->save_db();
    // $this->display();
    $page = WDW_FMC_Library::get('page');
    WDW_FMC_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'display', 'message' => $message), admin_url('admin.php')));
  }

  public function save_db() {
    global $wpdb;
    $id = (isset($_POST['current_id']) ? (int) $_POST['current_id'] : 0);
    $ip = (isset($_POST['ip']) ? esc_html(stripslashes($_POST['ip'])) : '');
    if ($id != 0) {
      $save = $wpdb->update($wpdb->prefix . 'formmaker_blocked', array(
        'ip' => $ip,
      ), array('id' => $id));
    }
    else {
      $save = $wpdb->insert($wpdb->prefix . 'formmaker_blocked', array(
        'ip' => $ip,
      ), array(
				'%s',
      ));
    }
    if ($save !== FALSE) {
      $message = 1;
    }
    else {
      $message = 2;
    }
  }

  public function save_all() {
    global $wpdb;
    $flag = FALSE;
    $ips_id_col = $wpdb->get_col('SELECT id FROM ' . $wpdb->prefix . 'formmaker_blocked');
    foreach ($ips_id_col as $ip_id) {
      if (isset($_POST['ip' . $ip_id])) {
        $ip = esc_html(stripslashes($_POST['ip' . $ip_id]));
        if ($ip == '') {
          $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'formmaker_blocked WHERE id="%d"', $ip_id));
        }
        else {
          $flag = TRUE;
          $wpdb->update($wpdb->prefix . 'formmaker_blocked', array(
            'ip' => $ip,
          ), array('id' => $ip_id));
        }
      }
    }
    if ($flag) {
      $message = 1;
    }
    else {
      $message = 0;
    }
    // $this->display();
    $page = WDW_FMC_Library::get('page');
    WDW_FMC_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'display', 'message' => $message), admin_url('admin.php')));
  }

  public function delete($id) {
    global $wpdb;
    $query = $wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'formmaker_blocked WHERE id="%d"', $id);
    if ($wpdb->query($query)) {
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
    $ips_id_col = $wpdb->get_col('SELECT id FROM ' . $wpdb->prefix . 'formmaker_blocked');
    foreach ($ips_id_col as $ip_id) {
      if (isset($_POST['check_' . $ip_id])) {
        $flag = TRUE;
        $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'formmaker_blocked WHERE id="%d"', $ip_id));
      }
    }
    if ($flag) {
      $message = 5;
    }
    else {
      $message = 2;
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