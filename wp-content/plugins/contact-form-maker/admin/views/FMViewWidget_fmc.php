<?php

class FMViewWidget_fmc {
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

  public function display() {
  }

  function widget($args, $instance) {
    extract($args);
    $title = $instance['title'];
    $form_id = (isset($instance['form_id']) ? $instance['form_id'] : 0);
    // Before widget.
    echo $before_widget;
    // Title of widget.
    if ($title) {
      echo $before_title . $title . $after_title;
    }
    // Widget output.
    require_once(WD_FMC_DIR . '/frontend/controllers/FMControllerForm_maker_fmc.php');
    $controller_class = 'FMControllerForm_maker_fmc';
    $controller = new $controller_class();
    echo $controller->execute($instance['form_id']);
    // After widget.
    echo $after_widget;
  }
  
  // Widget Control Panel.
  function form($instance, $id_title, $name_title, $id_form_id, $name_form_id) {
     $defaults = array(
      'title' => '',
      'form_id' => 0
    );
    $instance = wp_parse_args((array)$instance, $defaults);
    global $wpdb; ?>
    <p>
      <label for="<?php echo $id_title; ?>">Title:</label>
      <input class="widefat" id="<?php echo $id_title; ?>" name="<?php echo $name_title; ?>" type="text" value="<?php echo $instance['title']; ?>"/>
      <label for="<?php echo $id_form_id; ?>">Select a form:</label>
      <select name="<?php echo $name_form_id; ?>" id="<?php echo $id_form_id; ?>" style="width:225px;text-align:center;">
        <option style="text-align:center" value="0">- Select a Form -</option>
        <?php
        $ids_Form_Maker = $this->model->get_gallery_rows_data();
        foreach ($ids_Form_Maker as $arr_Form_Maker) {
          ?>
          <option value="<?php echo $arr_Form_Maker->id; ?>" <?php if ($arr_Form_Maker->id == $instance['form_id']) {
            echo "SELECTED";
          } ?>><?php echo $arr_Form_Maker->title; ?></option>
          <?php }?>
      </select>
    </p>
    <?php
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