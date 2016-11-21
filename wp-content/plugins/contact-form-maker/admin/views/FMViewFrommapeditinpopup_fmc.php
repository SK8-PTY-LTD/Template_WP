<?php

class FMViewFrommapeditinpopup_fmc {
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
	$fmc_settings = get_option('fmc_settings');
	$map_key = isset($fmc_settings['map_key']) ? $fmc_settings['map_key'] : '';
    $long = ((isset($_GET['long'])) ? esc_html(stripslashes($_GET['long'])) : 0);
    $lat = ((isset($_GET['lat'])) ? esc_html(stripslashes($_GET['lat'])) : 0);
    ?>
    <script src="<?php echo WD_FMC_URL . '/js/main_front_end.js'; ?>"></script>
    <script src="<?php echo WD_FMC_URL . '/js/if_gmap_back_end.js'; ?>"></script>
    <script src="https://maps.google.com/maps/api/js?v=3.exp&key=<?php echo $map_key ?>" type="text/javascript"></script>
    <table style="margin:0px; padding:0px">
      <tr>
        <td><b>Address:</b></td>
        <td><input type="text" id="addrval0" style="border:0px; background:none" size="80" readonly/></td>
      </tr>
      <tr>
        <td><b>Longitude:</b></td>
        <td><input type="text" id="longval0" style="border:0px; background:none" size="80" readonly/></td>
      </tr>
      <tr>
        <td><b>Latitude:</b></td>
        <td><input type="text" id="latval0" style="border:0px; background:none" size="80" readonly/></td>
      </tr>
    </table>
    <div id="0_elementform_id_temp" long="<?php echo $long ?>" center_x="<?php echo $long ?>" center_y="<?php echo $lat ?>" lat="<?php echo $lat ?>" zoom="8" info="" style="width:600px; height:400px; "></div>
    <script>
      if_gmap_init("0");
      add_marker_on_map(0, 0, "<?php echo $long; ?>", "<?php echo $lat; ?>", "");
    </script>
    <?php
    die();
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