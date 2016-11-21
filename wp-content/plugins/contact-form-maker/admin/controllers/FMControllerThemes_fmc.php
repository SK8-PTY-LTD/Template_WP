<?php

class FMControllerThemes_fmc {
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
    require_once WD_FMC_DIR . "/admin/models/FMModelThemes_fmc.php";
    $model = new FMModelThemes_fmc();

    require_once WD_FMC_DIR . "/admin/views/FMViewThemes_fmc.php";
    $view = new FMViewThemes_fmc($model);
    $view->display();
  }

  public function add() {
    require_once WD_FMC_DIR . "/admin/models/FMModelThemes_fmc.php";
    $model = new FMModelThemes_fmc();

    require_once WD_FMC_DIR . "/admin/views/FMViewThemes_fmc.php";
    $view = new FMViewThemes_fmc($model);
    $view->edit(0, FALSE);
  }

  public function edit() {
    require_once WD_FMC_DIR . "/admin/models/FMModelThemes_fmc.php";
    $model = new FMModelThemes_fmc();

    require_once WD_FMC_DIR . "/admin/views/FMViewThemes_fmc.php";
    $view = new FMViewThemes_fmc($model);
    // $id = ((isset($_POST['current_id']) && esc_html($_POST['current_id']) != '') ? esc_html($_POST['current_id']) : 0);
    $id = (int)WDW_FMC_Library::get('current_id', 0);
    $view->edit($id, FALSE);
  }

  public function save() {
    $message = $this->save_db();
    // $this->display();
    $page = WDW_FMC_Library::get('page');
    WDW_FMC_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'display', 'message' => $message), admin_url('admin.php')));
  }

  public function apply() {
    $message = $this->save_db();
    global $wpdb;
    // if (!isset($_POST['current_id']) || (esc_html($_POST['current_id']) == 0) || (esc_html($_POST['current_id']) == '')) {
      
    // }
    $id = (int) $wpdb->get_var('SELECT MAX(`id`) FROM ' . $wpdb->prefix . 'formmaker_themes');
    $current_id = (int)WDW_FMC_Library::get('current_id', $id);
    $page = WDW_FMC_Library::get('page');
    WDW_FMC_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'edit', 'current_id' => $current_id, 'message' => $message), admin_url('admin.php')));
    // $this->edit();
  }
  
  public function save_db() {
    global $wpdb;
    // $id = (isset($_POST['current_id']) ? (int) esc_html(stripslashes( $_POST['current_id'])) : 0);
    $id = (int) WDW_FMC_Library::get('current_id', 0);
    $title = (isset($_POST['title']) ? esc_html(stripslashes( $_POST['title'])) : '');
    $css = (isset($_POST['css']) ? stripslashes(preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $_POST['css'])) : '');
    $default = (isset($_POST['default']) ? esc_html(stripslashes( $_POST['default'])) : 0);
    if ($id != 0) {
      $save = $wpdb->update($wpdb->prefix . 'formmaker_themes', array(
        'title' => $title,
        'css' => $css,
        'default' => $default,
      ), array('id' => $id));
    }
    else {
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
    if ($save !== FALSE) {
      return 1;
    }
    else {
      return 2;
    }
  }

  public function delete($id) {
    global $wpdb;
    $isDefault = $wpdb->get_var($wpdb->prepare('SELECT `default` FROM ' . $wpdb->prefix . 'formmaker_themes WHERE id="%d"', $id));
    if ($isDefault) {
      $message = 4;
    }
    else {
      $query = $wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'formmaker_themes WHERE id="%d"', $id);
      if ($wpdb->query($query)) {
        $message = 3;
      }
      else {
        $message = 2;
      }
    }
    // $this->display();
    $page = WDW_FMC_Library::get('page');
    WDW_FMC_Library::fm_redirect(add_query_arg(array('page' => $page, 'task' => 'display', 'message' => $message), admin_url('admin.php')));
  }
  
  public function delete_all() {
    global $wpdb;
    $flag = FALSE;
    $isDefault = FALSE;
    $theme_ids_col = $wpdb->get_col('SELECT id FROM ' . $wpdb->prefix . 'formmaker_themes');
    foreach ($theme_ids_col as $theme_id) {
      if (isset($_POST['check_' . $theme_id])) {
        $isDefault = $wpdb->get_var($wpdb->prepare('SELECT `default` FROM ' . $wpdb->prefix . 'formmaker_themes WHERE id="%d"', $theme_id));
        if ($isDefault) {
          $message = 4;
        }
        else {
          $flag = TRUE;
          $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'formmaker_themes WHERE id="%d"', $theme_id));
        }
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

  public function setdefault($id) {
    global $wpdb;
    $wpdb->update($wpdb->prefix . 'formmaker_themes', array('default' => 0), array('default' => 1));
    $save = $wpdb->update($wpdb->prefix . 'formmaker_themes', array('default' => 1), array('id' => $id));
    if ($save !== FALSE) {
      $message = 7;
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