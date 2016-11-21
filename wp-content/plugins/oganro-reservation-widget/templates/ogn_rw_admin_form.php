<?php
	if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="ognwrap" style="width:100%;">

	
	<h2>Oganro Reservation Widget</h2>

	<?php if(isset($_POST['ogn_rw_srch_wdgt_opt']) || isset($_POST["ogn_rw_sb_default_opt"])): ?>
		<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"> 
			<p><strong>Settings updated.</strong></p>
			<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
		</div>
	<?php endif; ?>
	
	<?php if(!empty($ogn_rw_admin_erros)): ?>
		<div class="error notice">
			<?php foreach($ogn_rw_admin_erros as $errors): ?>
	        	<p><stron><?php echo esc_html($errors['field']); ?> : </stron><?php echo esc_html($errors['msg']); ?></p>
	    	<?php endforeach; ?>
	    </div>
    <?php endif; ?>

	<form method="POST" action="">

		<fieldset class="fieldwrap settings">

			<legend title="settings"><span class="iconssettings"></span>General Settings</legend>

			<div style="border:solid 2px #dcb300;background-color:#fbf6d5;color:#673500;padding:15px;border-radius:10px;margin-bottom:20px;">
			<h3 style="margin:5px;text-align:center;"><a href="http://www.oganro.com/contact-us">Contact Us</a></h3>
			<b> if you wish to go ahead with your own solution.
		<a href="http://travelportal.oganro.net/price/">Click Here</a> to find our travel portal development pricing structure. We offer 
		custom travel portal and travel website solutions as well.</b></div>


			<table class="form-table">
				<tbody>

					<tr>
						<th scope="row">Submit URL </th>
						<td>
							<input type="text" name="ogn_rw_opt_sb_submit_url" value="<?php echo esc_attr($ogn_rw_submit_url); ?>" class="regular-text"/>
							<p class="description" >By default Submit URL is pointed to <a href='http://www.demo.oganro.net'>http://www.demo.oganro.net</a> for your testing purpose. Please note if you <br>change Submit URL, result page redirected to a 404 error page.</p>
						</td>
					</tr>

					<tr>
						<th scope="row">Title </th>
						<td>
							<input type="text" name="ogn_rw_opt_sb_title" value="<?php echo esc_attr($ogn_rw_title); ?>" class="regular-text"/>
							<p class="description" >Enter search box Title</p>
						</td>
					</tr>

					<tr>
						<th scope="row">Title Color</th>
						<td>
							<input type="text" name="ogn_rw_opt_sb_title_color" value="<?php echo esc_attr($ogn_rw_title_color); ?>" class=" jscolor {width:243, height:150, position:'right',borderColor:'#FFF', insetColor:'#FFF', backgroundColor:'#666'}"/>
							<p class="description" >Select Title color</p>
						</td>
					</tr>

					<tr>
						<th scope="row">Title Size</th>
						<td>
							<input type="number" name="ogn_rw_opt_sb_title_size" value="<?php echo esc_attr($ogn_rw_title_size); ?>"/><b> px</b>
							<p class="description" >Enter title size in px</p>
						</td>
					</tr>

					

				</tbody>
			</table>
		</fieldset>

		<fieldset class="fieldwrap settings">
			
			<legend title="settings"><span class="iconssettings"></span>Search box style settings</legend>
			<table class="form-table">
				<tbody>

					<tr>
						<th scope="row">Search Box Width</th>
						<td>
							<input type="number" name="ogn_rw_opt_sb_search_box_width" value="<?php echo esc_attr($ogn_rw_search_box_width); ?>" max="200"/><b> %</b>
							<p class="description" >Enter search box width in %</p>
						</td>
					</tr>

					<tr>
						<th scope="row">Background Color</th>
						<td>
							<input type="text" name="ogn_rw_opt_sb_background_color" value="<?php echo esc_attr($ogn_rw_background_color); ?>" class=" jscolor {width:243, height:150, position:'right',borderColor:'#FFF', insetColor:'#FFF', backgroundColor:'#666',onFineChange:'update(this)'}" />
							<p class="description" >Select background color</p>
						</td>
					</tr>

					<tr>
						<th scope="row">Icons Color</th>
						<td>
							<input type="text" name="ogn_rw_opt_sb_icon_color" value="<?php echo esc_attr($ogn_rw_icon_color); ?>" class=" jscolor {width:243, height:150, position:'right',borderColor:'#FFF', insetColor:'#FFF', backgroundColor:'#666'}"/>
							<p class="description" >Select Icon color</p>
						</td>
					</tr>

					<tr>
						<th scope="row">Input label Color</th>
						<td>
							<input type="text" name="ogn_rw_opt_sb_label_color" value="<?php echo esc_attr($ogn_rw_label_color); ?>" class=" jscolor {width:243, height:150, position:'right',borderColor:'#FFF', insetColor:'#FFF', backgroundColor:'#666'}"/>
							<p class="description" >Select Input label color</p>
						</td>
					</tr>

					<tr>
						<th scope="row">Search Box Opacity</th>
						<td>
							<div id="ogn_rw_slider"></div>
						</td>
					</tr>

					<tr>
						<th scope="row">Search Box Border Radius</th>
						<td>
							<input type="number" name="ogn_rw_opt_sb_border_radius" value="<?php echo esc_attr($ogn_rw_border_radius); ?>" /><b> px</b>
							<p class="description" >Enter search box radius in px</p>
						</td>
					</tr>

					<tr>
						<th scope="row">Search Box Border Color</th>
						<td>
							<input type="text" name="ogn_rw_opt_sb_border_color" value="<?php echo esc_attr($ogn_rw_border_color); ?>" class=" jscolor {width:243, height:150, position:'right',borderColor:'#FFF', insetColor:'#FFF', backgroundColor:'#666'}"/>
							<p class="description" >Enter search box radus in px</p>
						</td>
					</tr>
					<tr>
						<th scope="row">Search Box Border width</th>
						<td>
							<input type="number" name="ogn_rw_opt_sb_border_width" value="<?php echo esc_attr($ogn_rw_border_width); ?>" max="50" /><b> px</b>
							<p class="description" >Enter search box border width in px</p>
						</td>
					</tr>
					
					
				</tbody>
			</table>
		</fieldset>

		<!-- Location settings -->
		<fieldset title="Settings" class="fieldwrap">
			<legend><span class="location"></span>Location Settings</legend>
			<table class="form-table">
				<tbody>

					<tr>
						<th scope="row">Location Title</th>
						<td>
							<input type="text" name="ogn_rw_opt_sb_location_title" value="<?php echo esc_attr($ogn_rw_location_title); ?>"  class="regular-text"/>
							<p class="description" >Enter location label text</p>
						</td>
					</tr>

					<tr>
						<th scope="row">Location Placeholder Text</th>
						<td>
							<input type="text" name="ogn_rw_opt_sb_location_placeholder" value="<?php echo esc_attr($ogn_rw_location_placeholder); ?>"  class="regular-text"/>
							<p class="description" >Enter location placeholder text</p>
						</td>
					</tr>

				</tbody>
			</table>
		</fieldset>

		<!-- Location settings -->
		<fieldset title="Settings" class="fieldwrap">
			<legend><span class="checkout"></span>Datepicker settings</legend>
			<table class="form-table">
				<tbody>

					<tr>
						<th scope="row">Check In Title</th>
						<td>
							<input type="text" name="ogn_rw_opt_sb_checkin_title" value="<?php echo esc_attr($ogn_rw_checkin_title); ?>"  class="regular-text"/>
							<p class="description" >Enter check in label text</p>
						</td>
					</tr>

					<tr>
						<th scope="row">Checkout Title</th>
						<td>
							<input type="text" name="ogn_rw_opt_sb_checkout_title" value="<?php echo esc_attr($ogn_rw_checkout_title); ?>"  class="regular-text"/>
							<p class="description" >Enter checkout label text</p>
						</td>
					</tr>

				</tbody>
			</table>
		</fieldset>


		<!-- Nights settings -->
		<fieldset title="Settings" class="fieldwrap">
			<legend><span class="nights"></span>Nights Settings</legend>
			<table class="form-table">
				<tbody>

					<tr>
						<th scope="row">Nights Title</th>
						<td>
							<input type="text" name="ogn_rw_opt_sb_nights_title" value="<?php echo esc_attr($ogn_rw_nights_title); ?>"  class="regular-text"/>
							<p class="description" >Enter Nights label text</p>
						</td>
					</tr>

					<tr>
						<th scope="row">Default Nights</th>
						<td>
							<select name="ogn_rw_opt_sb_nights">
								<?php for($i = 1; $i < 31;$i++): ?>
										<option value="<?php echo esc_attr($i); ?>" <?php if($i == $ogn_rw_nights) echo esc_attr("selected"); ?>><?php echo esc_html($i); ?></option>
								<?php endfor; ?>
							</select>
							<p class="description" >Select default nights count</p>
						</td>
					</tr>

				</tbody>
			</table>
		</fieldset>

		<!-- Rooms settings -->
		<fieldset title="Settings" class="fieldwrap">
			<legend><span class="roomsicon"></span>Rooms Settings</legend>
			<table class="form-table">
				<tbody>

					<tr>
						<th scope="row">Rooms Title</th>
						<td>
							<input type="text" name="ogn_rw_opt_sb_rooms_title" value="<?php echo esc_attr($ogn_rw_rooms_title); ?>"  class="regular-text"/>
							<p class="description" >Enter Rooms label text</p>
						</td>
					</tr>

				</tbody>
			</table>
		</fieldset>

		<!-- Search button settings -->
		<fieldset title="Settings" class="fieldwrap">
			<legend><span class="seachbox"></span>Search Button</legend>
			<table class="form-table">
				<tbody>

					<tr>
						<th scope="row">Search Button Text</th>
						<td>
							<input type="text" name="ogn_rw_opt_sb_button_text" value="<?php echo esc_attr($ogn_rw_button_text); ?>" />
							<p class="description" >Enter search button text</p>
						</td>
					</tr>

					<tr>
						<th scope="row">Button Background Color</th>
						<td>
							<input type="text" name="ogn_rw_opt_sb_button_background_color" value="<?php echo esc_attr($ogn_rw_button_background_color); ?>" class=" jscolor {width:243, height:150, position:'right',borderColor:'#FFF', insetColor:'#FFF', backgroundColor:'#666'}"/>
							<p class="description" >Select Search button background color</p>
						</td>
					</tr>

					<tr>
						<th scope="row">Button Text Color</th>
						<td>
							<input type="text" name="ogn_rw_opt_sb_button_text_color" value="<?php echo esc_attr($ogn_rw_button_text_color); ?>" class=" jscolor {width:243, height:150, position:'right',borderColor:'#FFF', insetColor:'#FFF', backgroundColor:'#666'}"/>
							<p class="description" >Select Search button background color</p>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>

		<fieldset class="fieldwrap settings">

			<legend title="settings"><span class="iconssettings"></span>Advance Settings</legend>
			<table class="form-table">
				<tbody>
					
					<tr>
						<th scope="row">Load Bootstrap js</th>
						<td>
							<input type="checkbox" id="ToggleSwitchSample" name="ogn_rw_opt_sb_bootstrap" />
							<p class="description" >Turn Off bootstrap js on conflict issues</p>
						</td>
					</tr>

					<tr>
						<th scope="row">Settings</th>
						<td>
							<a class="button" style="background-color:#8C0000;color:white;" id="ogn_rw_reset_btn">Reset to defaults</a>
							<p class="description" >Warning : Reset reservation widget settings to their original defaults</p>
						</td>
					</tr>

				</tbody>
			</table>
		</fieldset>

		<input type="hidden" name="ogn_rw_opt_sb_autocomplete_url" value="<?php echo esc_attr($ogn_rw_autocomplete_url); ?>" class="regular-text"/>
		<input type="hidden" name="ogn_rw_srch_wdgt_opt" value="y"/>
		<input type="hidden" name="ogn_rw_opt_sb_background_rgba" value="<?php echo esc_attr($ogn_rw_background_rgba);  ?>" id="bgr_rgba"/>
		<input type="hidden" name="ogn_rw_opt_sb_opacity" id="opacity" value="<?php echo esc_attr($ogn_rw_opacity); ?>" />
		<input type="hidden" id="ogn_sb_bootstrap" value="<?php echo esc_attr($ogn_rw_bootstrap); ?>"/>

		<fieldset class="fieldwrap settings" style="border:none;margin:0;box-shadow:none;">
				<input type="submit" id="submit" class="button button-primary" value="Save Changes">
		</fieldset>

	</form>
	<form action="" method="POST" id="ogn_rw_reset_form">
		<input type="hidden" name="ogn_rw_sb_default_opt" value="y">
	</form>
</div>


<p>
	<span id="footer-thankyou">This plug-in is brought to you by <a href="http://www.Oganro.com">Oganro (pvt) Ltd</a>. Oganro is an experienced travel technology software development company with over 10 years of experience in travel and technology industry.
	</span>
</p>


<script type="text/javascript">
	function update(picker) {

		var val = Math.round(picker.rgb[0]) + ', ' +
	        Math.round(picker.rgb[1]) + ', ' +
	        Math.round(picker.rgb[2]);

	    document.getElementById('bgr_rgba').value = val;
	}
</script>
<style>
	#ogn_rw_slider label {
	    position: absolute;
	    width: 20px;
	    margin-left: -10px;
	    text-align: center;
	    margin-top: 20px;
	}

	#ogn_rw_slider{
		width: 50%;
	}

	@media screen and (max-width: 1024px) {
	    #ogn_rw_slider{
			width: 80%;
		}
	}

	@media screen and (max-width: 783px) {
	    #ogn_rw_slider{
			width: 98%;
			margin-bottom: 25px;
		}
	}
</style>