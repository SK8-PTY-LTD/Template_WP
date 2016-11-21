<?php
class FMViewGoptions_fmc {
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
		$public_key = isset($fmc_settings['public_key']) ? $fmc_settings['public_key'] : '';
		$private_key = isset($fmc_settings['private_key']) ? $fmc_settings['private_key'] : '';
		$csv_delimiter = isset($fmc_settings['csv_delimiter']) ? $fmc_settings['csv_delimiter'] : ',';
		$map_key = isset($fmc_settings['map_key']) ? $fmc_settings['map_key'] : '';
		?>
		<div class="fm-user-manual">
			This section allows you to edit form settings.
			<a style="color: blue; text-decoration: none;" target="_blank" href="https://web-dorado.com/wordpress-form-maker-guide-2.html">Read More in User Manual</a>
		</div>
		<div class="fm-upgrade-pro">
			<a target="_blank" href="https://web-dorado.com/files/fromContactForm.php">
				<div class="fm-upgrade-img">
					UPGRADE TO PRO VERSION 
					<span></span>
				</div>
			</a>
		</div>
		<div class="fm-clear"></div>
		<form class="wrap" method="post" action="admin.php?page=goptions_fmc" style="width:99%;">
			<?php wp_nonce_field('nonce_fmc', 'nonce_fmc'); ?>     
			<div class="fm-page-header">
				<div class="fm-page-title">
					Form Settings
				</div>
				<div class="fm-page-actions">
					<button class="fm-button save-button small" onclick="if (fm_check_required('title', 'Title')) {return false;}; fm_set_input_value('task', 'save');">
						<span></span>
						Save
					</button>
				</div>
			</div>

			<table style="clear:both;">
				<tbody>
					<tr>
						<td>
							<label for="public_key">Recaptcha Site Key:</label>
						</td>
						<td>
							<input type="text" id="public_key" name="public_key" value="<?php echo $public_key; ?>" style="width:250px;" />
						</td>
						<td rowspan="2">
							<a href="https://www.google.com/recaptcha/intro/index.html" target="_blank">Get Recaptcha</a>
						</td>
					</tr>
					<tr>
						<td>
							<label for="private_key">Recaptcha Secret Key:</label>
						</td>
						<td>
							<input type="text" id="private_key" name="private_key" value="<?php echo $private_key; ?>" style="width:250px;" />
						</td>
					</tr>
					<tr>
						<td>
							<label for="public_key">Map API Key:</label>
						</td>
						<td>
							<input type="text" id="map_key" name="map_key" value="<?php echo $map_key; ?>" style="width:250px;" />
						</td>
						<td>
							<a href="https://console.developers.google.com/flows/enableapi?apiid=maps_backend&keyType=CLIENT_SIDE&reusekey=true" target="_blank">Get Map API Key</a>
						</td>
					</tr>
					<tr>
						<td>
							
						</td>
						<td>
							<span style="width:250px; display: inline-block; padding: 0 5px;">(It may take up to 5 minutes for API key change to take effect.)</span>
						</td>
						<td>
							
						</td>
					</tr>
					<tr>
						<td>
							<label for="csv_delimiter">CSV Delimiter:</label>
						</td>
						<td>
							<input type="text" id="csv_delimiter" name="csv_delimiter" value="<?php echo $csv_delimiter; ?>" style="width:50px;" />
						</td>
					</tr>
				</tbody>
			</table>
			<input type="hidden" id="task" name="task" value=""/>
		</form>
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