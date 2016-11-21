<?php

class FMControllerGoptions_fmc {
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
    require_once WD_FMC_DIR . "/admin/models/FMModelGoptions_fmc.php";
    $model = new FMModelGoptions_fmc();

    require_once WD_FMC_DIR . "/admin/views/FMViewGoptions_fmc.php";
    $view = new FMViewGoptions_fmc($model);
    $view->display();
  }

  public function save() {
    $message = $this->save_db();
    $page = WDW_FMC_Library::get('page');
    WDW_FMC_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'display', 'message' => $message), admin_url('admin.php')));
  }
  
  public function save_db() {
	global $wpdb;
    $public_key = (isset($_POST['public_key']) ? esc_html(stripslashes( $_POST['public_key'])) : '');
    $private_key = (isset($_POST['private_key']) ?  esc_html(stripslashes( $_POST['private_key'])) : '');
    $csv_delimiter = (isset($_POST['csv_delimiter']) && $_POST['csv_delimiter']!='' ? esc_html(stripslashes( $_POST['csv_delimiter'])) : ',');
    $map_key = (isset($_POST['map_key']) ?  esc_html(stripslashes( $_POST['map_key'])) : '');
    update_option('fmc_settings', array('public_key' => $public_key, 'private_key' => $private_key, 'csv_delimiter' => $csv_delimiter, 'map_key' => $map_key,));	
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