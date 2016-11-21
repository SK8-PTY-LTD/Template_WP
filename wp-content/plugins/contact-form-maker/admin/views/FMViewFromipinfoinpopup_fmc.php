<?php

class FMViewFromipinfoinpopup_fmc {
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
    $data_ip = ((isset($_GET['data_ip'])) ? esc_html(stripslashes($_GET['data_ip'])) : 0);
    $query = @unserialize(file_get_contents('http://ip-api.com/php/' . $data_ip));
    if ($query && $query['status'] == 'success' && $query['countryCode']) {
      $country_flag = '<img width="16px" src="' .  WD_FMC_URL . '/images/flags/' . strtolower($query['countryCode']) . '.png" class="sub-align" alt="' . $query['country'] . '" title="' . $query['country'] . '" />';
      $country = $query['country'] ;
      $countryCode = $query['countryCode'] ;
      $city = $query['city'];
      $timezone = $query['timezone'];
      $lat = $query['lat'];
      $lon = $query['lon'];
    }
    else {
      $country_flag = '';
      $country = '';
      $countryCode = '';
      $city = '';
      $timezone = '';
      $lat = '';
      $lon = '';
    }
    ?>
    <style>
      .admintable {
        height: 100%;
        margin: 0 auto;
        padding: 0;
        width: 100%;
      }
      table.admintable td.key, table.admintable td.paramlist_key {
        background-color: #F6F6F6;
        border-bottom: 1px solid #E9E9E9;
        border-right: 1px solid #E9E9E9;
        color: #666666;
        font-weight: bold;
        margin-right: 10px;
        text-align: right;
        width: 140px;
      }
    </style>
    <table class="admintable">
      <tr>
        <td class="key"><b>IP:</b></td><td><?php echo $data_ip; ?></td>
      </tr>
      <tr>
        <td class="key"><b>Country:</b></td><td><?php echo $country . ' ' . $country_flag; ?></td>
      </tr>
      <tr>
        <td class="key"><b>CountryCode:</b></td><td><?php echo $countryCode; ?></td>
      </tr>
	    <tr>
        <td class="key"><b>City:</b></td><td><?php echo $city; ?></td>
      </tr>
      <tr>
        <td class="key"><b>Timezone:</b></td><td><?php echo $timezone; ?></td>
      </tr>
      <tr>
        <td class="key"><b>Latitude:</b></td><td><?php echo $lat; ?></td>
      </tr>
      <tr>
        <td class="key"><b>Longitude:</b></td><td><?php echo $lon; ?></td>
      </tr>
    </table>
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