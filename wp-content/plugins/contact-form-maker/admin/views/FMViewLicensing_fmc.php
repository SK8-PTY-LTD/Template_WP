<?php

class FMViewLicensing_fmc {
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
	wp_register_style('fm_license', WD_FMC_URL . '/css/license.css', array(), get_option("wd_form_maker_version"));
	wp_print_styles('fm_license');
    ?>
    <div style="width:99%">
      <div id="featurs_tables">
		  <div id="featurs_table1">
			<span>Unlimited Fields</span>
			<span>File Upload Field</span>
			<span>Google Map</span>
			<span>PayPal Integration</span>
			<span>Front-End Submissions</span>
			<span>Add-ons support</span>
		  </div>
		  <div id="featurs_table2">
			<span>Free</span>
			<span class="no"></span>
			<span class="no"></span>
			<span class="no"></span>
			<span class="no"></span>
			<span class="no"></span>
			<span class="no"></span>
		  </div>
		  <div id="featurs_table3">
			<span>Pro Version</span>
			<span class="yes"></span>
			<span class="yes"></span>
			<span class="yes"></span>
			<span class="yes"></span>
			<span class="yes"></span>
			<span class="yes"></span>
		  </div>
		</div>
	<div style="float: right; text-align: right;">
        <a style="text-decoration: none;" target="_blank" href="https://web-dorado.com/files/fromContactForm.php">
          <img width="215" border="0" alt="web-dorado.com" src="<?php echo WD_FMC_URL . '/images/wd_logo.png'; ?>" />
        </a>
      </div>
	<div style="float: left; clear: both;">
      <a href="https://web-dorado.com/files/fromContactForm.php" class="button-primary" target="_blank">Purchase a License</a>
      <br/><br/>
      <p>After purchasing the commercial version follow these steps:</p>
      <ol>
        <li>Deactivate Contact Form Maker Plugin.</li>
        <li>Delete Contact Form Maker Plugin.</li>
        <li>Install the downloaded commercial version of the plugin.</li>
      </ol>
      <br/>
      <p>If you enjoy using Contact Form Maker and find it useful, please consider making a donation. Your donation will help encourage and support the plugin's continued development and better user support.</p>
      <br/>
      <a href="https://web-dorado.com/files/donate_redirect.php" target="_blank">
	  <div class="fm-get-pro"></div>
	  </a>
    </div>
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