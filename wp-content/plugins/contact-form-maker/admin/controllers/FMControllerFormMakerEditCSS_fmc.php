<?php

class FMControllerFormMakerEditCSS_fmc {
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
    if (method_exists($this, $task)) {
      $this->$task($id);
    }
    else {
      $this->display();
    }
  }

  public function display() {
    require_once WD_FMC_DIR . "/admin/models/FMModelFormMakerEditCSS_fmc.php";
    $model = new FMModelFormMakerEditCSS_fmc();

    require_once WD_FMC_DIR . "/admin/views/FMViewFormMakerEditCSS_fmc.php";
    $view = new FMViewFormMakerEditCSS_fmc($model);
    $view->display();
  }

  public function save() {
    $this->update_db();
  }

  public function apply() {
    $this->update_db();
    $this->display();
  }

  public function save_as_new() {
    $this->insert_db();
  }

  public function insert_db() {
    global $wpdb;
    $title = (isset($_POST['title']) ? esc_html(stripslashes( $_POST['title'])) : '');
    $css = (isset($_POST['css']) ? stripslashes(preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $_POST['css'])) : '');
    $default = (isset($_POST['default']) ? esc_html(stripslashes( $_POST['default'])) : 0);
    $save = $wpdb->insert($wpdb->prefix . 'formmaker_themes', array(
      'title' => $title,                       
      'css' => $css,         
      'default' => $default,
    ), array(
      '%s',
      '%s',
      '%d',
    ));
  }

  public function update_db() {
    global $wpdb;
    $id = (isset($_POST['current_id']) ? (int) esc_html(stripslashes( $_POST['current_id'])) : 0);
    $title = (isset($_POST['title']) ? esc_html(stripslashes( $_POST['title'])) : '');
    $css = (isset($_POST['css']) ? stripslashes(preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $_POST['css'])) : '');
    $default = (isset($_POST['default']) ? esc_html(stripslashes( $_POST['default'])) : 0);
    $save = $wpdb->update($wpdb->prefix . 'formmaker_themes', array(
      'title' => $title,
      'css' => $css,
      'default' => $default,
    ), array('id' => $id));
    if ($save !== FALSE) {
      echo WDW_FMC_Library::message('Item Succesfully Saved.', 'updated');
    }
    else {
      echo WDW_FMC_Library::message('Error. Please install plugin again.', 'error');
    }
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