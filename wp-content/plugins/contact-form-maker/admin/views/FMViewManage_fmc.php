<?php

class FMViewManage_fmc {
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
		$rows_data = $this->model->get_rows_data();
		$page_nav = $this->model->page_nav();
		$search_value = ((isset($_POST['search_value'])) ? esc_html($_POST['search_value']) : '');
		$search_select_value = ((isset($_POST['search_select_value'])) ? (int) $_POST['search_select_value'] : 0);
		$asc_or_desc = ((isset($_POST['asc_or_desc']) && $_POST['asc_or_desc'] == 'desc') ? 'desc' : 'asc');
		$order_by_array = array('id', 'title', 'mail');
		$order_by = isset($_POST['order_by']) && in_array(esc_html(stripslashes($_POST['order_by'])), $order_by_array) ? esc_html(stripslashes($_POST['order_by'])) :  'id';
		$order_class = 'manage-column column-title sorted ' . $asc_or_desc;
		$ids_string = '';
		?>
		<div class="fm-user-manual">
			This section allows you to create, edit forms.
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
		<form onkeypress="fm_doNothing(event)" class="wrap" id="manage_form" method="post" action="admin.php?page=manage_fmc" style="width:99%;">
			<?php wp_nonce_field('nonce_fmc', 'nonce_fmc'); ?>
			<div class="fm-page-banner">
				<div class="fm-logo">
				</div>
				<div class="fm-logo-title">Contact Form</br>Maker</div>
				<button class="fm-button add-button medium" onclick="fm_set_input_value('task', 'add'); fm_form_submit(event, 'manage_form')">
					<span></span>
					Add New
				</button>
			</div>	 
			<div class="tablenav top">
			<?php
				WDW_FMC_Library::search('Title', $search_value, 'manage_form');
				WDW_FMC_Library::html_page_nav($page_nav['total'], $page_nav['limit'], 'manage_form');
			?>
			</div>
			<table class="wp-list-table widefat fixed pages">
				<thead>
					<th class="manage-column column-cb check-column table_small_col"><input id="check_all" type="checkbox" style="margin:0;"/></th>
					<th class="table_small_col <?php if ($order_by == 'id') { echo $order_class; } ?>">
						<a onclick="fm_set_input_value('task', ''); fm_set_input_value('order_by', 'id'); fm_set_input_value('asc_or_desc', '<?php echo (($order_by == 'id' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>'); fm_form_submit(event, 'manage_form')" href="">
						<span>ID</span><span class="sorting-indicator"></span></a>
					</th>
					<th class="<?php if ($order_by == 'title') { echo $order_class; } ?>">
						<a onclick="fm_set_input_value('task', ''); fm_set_input_value('order_by', 'title'); fm_set_input_value('asc_or_desc', '<?php echo (($order_by == 'title' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>'); fm_form_submit(event, 'manage_form')" href="">
						<span>Title</span><span class="sorting-indicator"></span></a>
					</th>
					<th class="<?php if ($order_by == 'mail') { echo $order_class; } ?>">
						<a onclick="fm_set_input_value('task', ''); fm_set_input_value('order_by', 'mail'); fm_set_input_value('asc_or_desc', '<?php echo (($order_by == 'mail' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>'); fm_form_submit(event, 'manage_form')" href="">
						<span>Email to send submissions to</span><span class="sorting-indicator"></span></a>
					</th>
					<th class="table_big_col">Shortcode</th>
					<th class="table_large_col">PHP function</th>
					<th class="table_small_col">Edit</th>
					<th class="table_small_col">
						<a title="Delete selected items" href="" onclick="if (confirm('Do you want to delete selected items?')) { fm_set_input_value('task', 'delete_all'); fm_form_submit(event, 'manage_form'); } else { return false; }">Delete</a>
					</th>
				</thead>
				<tbody id="tbody_arr">
					<?php
					if ($rows_data) {
						foreach ($rows_data as $row_data) {
							$alternate = (!isset($alternate) || $alternate == '') ? 'class="alternate"' : '';
							$old = '';
							if (isset($row_data->form) && ($row_data->form != '')) {
								$old = '_old';
							}
							?>
							<tr id="tr_<?php echo $row_data->id; ?>" <?php echo $alternate; ?>>
								<td class="table_small_col check-column">
									<input id="check_<?php echo $row_data->id; ?>" name="check_<?php echo $row_data->id; ?>" type="checkbox"/>
								</td>
								<td class="table_small_col"><?php echo $row_data->id; ?></td>
								<td>
									<a onclick="fm_set_input_value('task', 'edit<?php echo $old; ?>'); fm_set_input_value('current_id', '<?php echo $row_data->id; ?>'); fm_form_submit(event, 'manage_form')" href="" title="Edit"><?php echo $row_data->title; ?></a>
								</td>
								<td><?php echo $row_data->mail; ?></td>
								<td class="table_big_col" style="padding-left: 0; padding-right: 0;">
									<input type="text" value='[wd_contact_form id="<?php echo $row_data->id; ?>"]' onclick="fm_select_value(this)" size="12" readonly="readonly" style="padding-left: 1px; padding-right: 1px;"/>
								</td>
								<td class="table_large_col" style="padding-left: 0; padding-right: 0;">
									<input type="text" value='&#60;?php wd_contact_form_maker(<?php echo $row_data->id; ?>); ?&#62;' onclick="fm_select_value(this)"  readonly="readonly" style="padding-left: 1px; padding-right: 1px;"/>
								</td>
								<td class="table_small_col">
									<button class="fm-icon edit-icon" onclick="fm_set_input_value('task', 'edit<?php echo $old; ?>');  fm_set_input_value('current_id', '<?php echo $row_data->id; ?>'); fm_form_submit(event, 'manage_form')">
										<span></span>
									</button>
								</td>
								<td class="table_small_col">
									<button class="fm-icon delete-icon" onclick="if (confirm('Do you want to delete selected item(s)?')) { fm_set_input_value('task', 'delete'); fm_set_input_value('current_id', '<?php echo $row_data->id; ?>'); fm_form_submit(event, 'manage_form'); } else { return false; }">
										<span></span>
									</button>
								</td>
							</tr>
							<?php
							$ids_string .= $row_data->id . ',';
						}
					}
					?>
				</tbody>
			</table>
			<input id="task" name="task" type="hidden" value=""/>
			<input id="current_id" name="current_id" type="hidden" value=""/>
			<input id="ids_string" name="ids_string" type="hidden" value="<?php echo $ids_string; ?>"/>
			<input id="asc_or_desc" name="asc_or_desc" type="hidden" value="asc"/>
			<input id="order_by" name="order_by" type="hidden" value="<?php echo $order_by; ?>"/>
		</form>
		<?php
	}

	public function edit($id) {
		?>
		<img src="<?php echo WD_FMC_URL . '/images/buttons.png'; ?>" style="display:none;"/>
		<?php
		$row = $this->model->get_row_data_new($id);
		$themes = $this->model->get_theme_rows_data();
		$labels = array();
		$label_id = array();
		$label_order_original = array();
		$label_type = array();
		$label_all = explode('#****#', $row->label_order);
		$label_all = array_slice($label_all, 0, count($label_all) - 1);
		foreach ($label_all as $key => $label_each) {
			$label_id_each = explode('#**id**#', $label_each);
			array_push($label_id, $label_id_each[0]);
			$label_oder_each = explode('#**label**#', $label_id_each[1]);
			array_push($label_order_original, addslashes($label_oder_each[0]));
			array_push($label_type, $label_oder_each[1]);
		}
		$labels['id'] = '"' . implode('","', $label_id) . '"';
		$labels['label'] = '"' . implode('","', $label_order_original) . '"';
		$labels['type'] = '"' . implode('","', $label_type) . '"';
		$page_title = (($id != 0) ? 'Edit form ' . $row->title : 'Create new form');
		?>
		<script type="text/javascript">
			var plugin_url = "<?php echo WD_FMC_URL; ?>";
			var field_limitation = "<?php echo get_option("wd_cfield_limit", ''); ?>";
		</script>
		<script src="<?php echo WD_FMC_URL . '/js/formmaker_div_free.js'; ?>?ver=<?php echo get_option("wd_form_maker_version"); ?>" type="text/javascript"></script>
		<script type="text/javascript">
			form_view = 1;
			form_view_count = 1;
			form_view_max = 1;
			function submitbutton() {
			<?php if ($id) { ?>
				if (!document.getElementById('araqel') || (document.getElementById('araqel').value == '0')) {
					alert('Please wait while page loading.');
					return false;
				}
			<?php } ?>
			tox = '';
			form_fields = '';
			document.getElementById('take').style.display = "none";
			document.getElementById('page_bar').style.display = "none";
			jQuery('#saving').html('<div class="fm-loading-container"><div class="fm-loading-content"></div></div>');
			jQuery('.wdform_section').each(function() {
				var this2 = this;
				jQuery(this2).find('.wdform_column').each(function() {
					if(!jQuery(this).html() && jQuery(this2).children().length>1)
						jQuery(this).remove();
				});
			});
			remove_whitespace(document.getElementById('take'));
			l_id_array = [<?php echo $labels['id']?>];
			l_label_array = [<?php echo $labels['label']?>];
			l_type_array = [<?php echo $labels['type']?>];
			l_id_removed = [];      
			for (x = 0; x < l_id_array.length; x++) {
				l_id_removed[l_id_array[x]] = true;
			}
			for (t = 1; t <= form_view_max; t++) {
			  if (document.getElementById('form_id_tempform_view' + t)) {
				wdform_page = document.getElementById('form_id_tempform_view' + t);
				remove_whitespace(wdform_page);
				n = wdform_page.childNodes.length - 2;
				for (q = 0; q <= n; q++) {
				  if (!wdform_page.childNodes[q].getAttribute("wdid")) {
					wdform_section = wdform_page.childNodes[q];
					for (x = 0; x < wdform_section.childNodes.length; x++) {
					  wdform_column = wdform_section.childNodes[x];
					  if (wdform_column.firstChild) {
						for (y=0; y < wdform_column.childNodes.length; y++) {
						  is_in_old = false;
						  wdform_row = wdform_column.childNodes[y];
						  if (wdform_row.nodeType == 3) {
							continue;
						  }
						  wdid = wdform_row.getAttribute("wdid");
						  if (!wdid) {
							continue;
						  }
						  l_id = wdid;
						  l_label = document.getElementById(wdid + '_element_labelform_id_temp').innerHTML;
						  l_label = l_label.replace(/(\r\n|\n|\r)/gm," ");
						  wdtype = wdform_row.firstChild.getAttribute('type');

						  for (var z = 0; z < l_id_array.length; z++) {
							if (l_type_array[z] == "type_address") {
								if (document.getElementById(l_id + "_mini_label_street1") || document.getElementById(l_id + "_mini_label_street2") || document.getElementById(l_id + "_mini_label_city") || document.getElementById(l_id + "_mini_label_state") || document.getElementById(l_id+"_mini_label_postal") || document.getElementById(l_id+"_mini_label_country")) {
								  l_id_removed[l_id_array[z]] = false;
								}
							}
							else {
								if (l_id_array[z] == wdid) {
									l_id_removed[l_id] = false;
								}
							}
							
						  }

						  if (wdtype == "type_address") {
							addr_id = parseInt(wdid);
							id_for_country = addr_id;
							if (document.getElementById(id_for_country + "_mini_label_street1"))
							  tox = tox + addr_id + '#**id**#' + document.getElementById(id_for_country + "_mini_label_street1").innerHTML + '#**label**#type_address#****#';
							addr_id++; 
							if (document.getElementById(id_for_country + "_mini_label_street2"))
							  tox = tox + addr_id + '#**id**#' + document.getElementById(id_for_country + "_mini_label_street2").innerHTML + '#**label**#type_address#****#';
							addr_id++;
							if (document.getElementById(id_for_country+"_mini_label_city"))
							  tox = tox + addr_id + '#**id**#' + document.getElementById(id_for_country + "_mini_label_city").innerHTML + '#**label**#type_address#****#';
							addr_id++;
							if (document.getElementById(id_for_country + "_mini_label_state"))
							  tox = tox + addr_id + '#**id**#' + document.getElementById(id_for_country + "_mini_label_state").innerHTML + '#**label**#type_address#****#';
							addr_id++;
							if (document.getElementById(id_for_country + "_mini_label_postal"))
							  tox = tox + addr_id + '#**id**#' + document.getElementById(id_for_country + "_mini_label_postal").innerHTML + '#**label**#type_address#****#';
							addr_id++;
							if (document.getElementById(id_for_country+"_mini_label_country")) {
							  tox=tox + addr_id + '#**id**#' + document.getElementById(id_for_country + "_mini_label_country").innerHTML + '#**label**#type_address#****#';
							}
							
						  }
						  else {
							tox = tox + wdid + '#**id**#' + l_label + '#**label**#' + wdtype + '#****#';
						  }
						
						  id = wdid;
						  form_fields += wdid + "*:*id*:*";
						  form_fields += wdtype + "*:*type*:*";
						  w_choices = new Array();
						  w_choices_value=new Array();
						  w_choices_checked = new Array();
						  w_choices_disabled = new Array();
						  w_choices_params =new Array();
						  w_allow_other_num = 0;
						  w_property = new Array();
						  w_property_type = new Array();
						  w_property_values = new Array();
						  w_choices_price = new Array();
						  if (document.getElementById(id+'_element_labelform_id_temp').innerHTML) {
							w_field_label = document.getElementById(id + '_element_labelform_id_temp').innerHTML.replace(/(\r\n|\n|\r)/gm," ");
						  }
						  else {                      
							w_field_label = " ";
						  }
						  if (document.getElementById(id + '_label_sectionform_id_temp')) {
							if (document.getElementById(id + '_label_sectionform_id_temp').style.display == "block") {
							  w_field_label_pos = "top";
							}
							else {
							  w_field_label_pos = "left";
							}
						  }
						  if (document.getElementById(id + "_elementform_id_temp")) {
							s = document.getElementById(id + "_elementform_id_temp").style.width;
							w_size=s.substring(0,s.length - 2);
						  }
						  if (document.getElementById(id + "_label_sectionform_id_temp")) {
							s = document.getElementById(id + "_label_sectionform_id_temp").style.width;
							w_field_label_size = s.substring(0, s.length - 2);
						  }
						  if (document.getElementById(id + "_requiredform_id_temp")) {
							w_required = document.getElementById(id + "_requiredform_id_temp").value;
						  }
						  if (document.getElementById(id + "_uniqueform_id_temp")) {
							w_unique = document.getElementById(id + "_uniqueform_id_temp").value;
						  }
						  if (document.getElementById(id + '_label_sectionform_id_temp')) {
							w_class = document.getElementById(id + '_label_sectionform_id_temp').getAttribute("class");
							if (!w_class) {
							  w_class = "";
							}
						  }
						  gen_form_fields();
						  wdform_row.innerHTML = "%" + id + " - " + l_label + "%";
						}
					  }
					}
				  }
				  else {
					id = wdform_page.childNodes[q].getAttribute("wdid");
					w_editor = document.getElementById(id + "_element_sectionform_id_temp").innerHTML;
					form_fields += id + "*:*id*:*";
					form_fields += "type_section_break" + "*:*type*:*";
					form_fields += "custom_" + id + "*:*w_field_label*:*";
					form_fields += w_editor + "*:*w_editor*:*";
					form_fields += "*:*new_field*:*";
					wdform_page.childNodes[q].innerHTML = "%" + id + " - " + "custom_" + id + "%";
				  }
				}
			  }	
			}
			document.getElementById('label_order_current').value = tox;

			for (x = 0; x < l_id_array.length; x++) {
				if (l_id_removed[l_id_array[x]]) {
					tox = tox + l_id_array[x] + '#**id**#' + l_label_array[x] + '#**label**#' + l_type_array[x] + '#****#';
				}
			}

			document.getElementById('label_order').value = tox;
			document.getElementById('form_fields').value = form_fields;
			refresh_(); 
			document.getElementById('pagination').value=document.getElementById('pages').getAttribute("type");
			document.getElementById('show_title').value=document.getElementById('pages').getAttribute("show_title");
			document.getElementById('show_numbers').value=document.getElementById('pages').getAttribute("show_numbers");
			return true;
		}

		gen = <?php echo (($id != 0) ? $row->counter : 1); ?>;

		function enable() {
			alltypes = Array('customHTML', 'text', 'checkbox', 'radio', 'time_and_date', 'select', 'file_upload', 'captcha', 'map', 'button', 'page_break', 'section_break', 'paypal', 'survey');
			for (x = 0; x < 14; x++) {
				document.getElementById('img_' + alltypes[x]).src = "<?php echo WD_FMC_URL . '/images/'; ?>" + alltypes[x] + ".png?ver=<?php echo get_option("wd_form_maker_version"); ?>";
			}
			if (document.getElementById('formMakerDiv').style.display == 'block') {
				jQuery('#formMakerDiv').slideToggle(200);
			}
			else {
				jQuery('#formMakerDiv').slideToggle(400);
			}
			
			if (document.getElementById('formMakerDiv1').style.display == 'block') {
				jQuery('#formMakerDiv1').slideToggle(200);
			}
			else {
				jQuery('#formMakerDiv1').slideToggle(400);
			}
			document.getElementById('when_edit').style.display = 'none';
		}

		function enable2() {
			alltypes = Array('customHTML', 'text', 'checkbox', 'radio', 'time_and_date', 'select', 'file_upload', 'captcha', 'map', 'button', 'page_break', 'section_break', 'paypal', 'survey');
			for (x = 0; x < 14; x++) {
				document.getElementById('img_' + alltypes[x]).src = "<?php echo WD_FMC_URL . '/images/'; ?>" + alltypes[x] + ".png?ver=<?php echo get_option("wd_form_maker_version"); ?>";
			}
			if (document.getElementById('formMakerDiv').style.display == 'block') {
				jQuery('#formMakerDiv').slideToggle(200);
			}
			else {
				jQuery('#formMakerDiv').slideToggle(400);
			}
			
			if (document.getElementById('formMakerDiv1').style.display == 'block') {
				jQuery('#formMakerDiv1').slideToggle(200);
			}
			else {
				jQuery('#formMakerDiv1').slideToggle(400);
			}
			document.getElementById('when_edit').style.display = 'block';
			if (document.getElementById('field_types').offsetWidth) {
				document.getElementById('when_edit').style.width = document.getElementById('field_types').offsetWidth + 'px';
			}
			if (document.getElementById('field_types').offsetHeight) {
				document.getElementById('when_edit').style.height = document.getElementById('field_types').offsetHeight + 'px';
			}
		}
		</script>
		<div class="fm-user-manual">
			This section allows you to add fields to your form.
			<a style="color: blue; text-decoration: none;" target="_blank" href="https://web-dorado.com/wordpress-form-maker-guide-4.html">Read More in User Manual</a>
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
		<form class="wrap" id="manage_form" method="post" action="admin.php?page=manage_fmc" style="width:99%;">
		<?php wp_nonce_field('nonce_fmc', 'nonce_fmc'); ?>
			<h2 class="fm-h2-message"></h2>
			<div class="fm-page-header">
				<!-- <div class="fm-page-title">
					<?php echo $page_title; ?>
				</div> -->
				<div style="float:left;">
					<div class="fm-logo-edit-page"></div>
					<div class="fm-title-edit-page">Contact Form</br>Maker</div>
				</div>
				<div class="fm-page-actions">
					<button class="fm-button form-options-button medium" onclick="if (fm_check_required('title', 'Form title') || !submitbutton()) {return false;}; fm_set_input_value('task', 'form_options');">
						<span></span>
						Form Options
					</button>	
					<button class="fm-button form-layout-button medium" onclick="if (fm_check_required('title', 'Form title') || !submitbutton()) {return false;}; fm_set_input_value('task', 'form_layout');">
						<span></span>
						Form Layout
					</button>	
					<div style="height:40px; border-right: 1px solid #848484; display: inline-block; width: 5px; vertical-align: bottom; margin-right: 5px;"></div>		
					<?php
					if(isset($row->backup_id) )
						if($row->backup_id!="") {
							global $wpdb;
							$query = "SELECT backup_id FROM " . $wpdb->prefix . "formmaker_backup WHERE backup_id > ".$row->backup_id." AND id = ".$row->id." ORDER BY backup_id ASC LIMIT 0 , 1 ";
							$backup_id = $wpdb->get_var($query);
							if($backup_id) { ?>
								<button class="fm-button redo-button small" onclick="if (fm_check_required('title', 'Form title') || !submitbutton()) {return false;}; jQuery('#saving_text').html('Redo');fm_set_input_value('task', 'redo');">
									<span></span>
									Redo
								</button>
								<?php 
							}
							$query = "SELECT backup_id FROM " . $wpdb->prefix . "formmaker_backup WHERE backup_id < ".$row->backup_id." AND id = ".$row->id." ORDER BY backup_id DESC LIMIT 0 , 1 ";
							$backup_id = $wpdb->get_var($query);

							if($backup_id) { ?>
								<button class="fm-button undo-button small" onclick="if (fm_check_required('title', 'Form title') || !submitbutton()) {return false;}; jQuery('#saving_text').html('Undo');fm_set_input_value('task', 'undo');">
									<span></span>
									Undo
								</button>
								<?php
							}
						}
						?>
					
					<?php if ($id) { ?>
						<button class="fm-button save-as-copy-button medium" onclick="if (fm_check_required('title', 'Form title') || !submitbutton()) {return false;}; fm_set_input_value('task', 'save_as_copy');">
							<span></span>
							Save as Copy
						</button>
					<?php } ?>
					<button class="fm-button save-button small" onclick="if (fm_check_required('title', 'Form title') || !submitbutton()) {return false;}; fm_set_input_value('task', 'save');">
						<span></span>
						Save
					</button>
					<button class="fm-button apply-button small" onclick="if (fm_check_required('title', 'Form title') || !submitbutton()) {return false;}; fm_set_input_value('task', 'apply');">
						<span></span>
						Apply
					</button>
					<button class="fm-button cancel-button small" onclick="fm_set_input_value('task', 'cancel');">
						<span></span>
						Cancel
					</button>
				</div>
				<div class="fm-clear"></div>
			</div>
			<div class="fm-title">
				<span style="">Form title:&nbsp;</span>
				<input id="title" name="title" value="<?php echo $row->title; ?>"/>
			</div>
			<div class="fm-clear"></div>
			<br/>
			<div class="fm-theme-banner">
				<div class="fm-theme" style="float:left;">
					
					<span style="">Theme:&nbsp;</span>
					<select id="theme" name="theme" onChange="set_preview()">
						<?php
						foreach ($themes as $theme) {
							?>
							<option value="<?php echo $theme->id; ?>" <?php echo (($theme->id == $row->theme) ? 'selected' : ''); ?>><?php echo $theme->title; ?></option>
							<?php
						}
						?>
					</select>
					<button id="preview_form" class="fm-button preview-button small" onclick="tb_show('', '<?php echo add_query_arg(array('action' => 'FormMakerPreview_fmc', 'form_id' => $row->id, 'test_theme' => $row->theme, 'width' => '1000', 'height' => '500', 'TB_iframe' => '1'), admin_url('admin-ajax.php')); ?>'); return false;">
						<span></span>
						Preview
					</button>
					<button id="edit_css" class="fm-button options-edit-button small" onclick="tb_show('', '<?php echo add_query_arg(array('action' => 'FormMakerEditCSS_fmc', 'id' => $row->theme, 'form_id' => $row->id, 'width' => '1000', 'height' => '500', 'TB_iframe' => '1'), admin_url('admin-ajax.php')); ?>'); return false;">
						<span></span>
						Edit CSS
					</button>
				</div>
				<div style="float:right;">
					<button class="fm-button add-new-button large" onclick="enable(); Enable(); return false;">
						Add a New Field
						<span></span>
					</button>
				</div>	
			</div>
			<div class="fm-clear"></div>
			<div id="formMakerDiv" onclick="close_window()"></div>
				<div id="formMakerDiv1">
					<table class="formMakerDiv1_table" border="0" width="100%" cellpadding="0" cellspacing="0" height="100%">
						<tr>
							<td style="padding:0px">
								<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" >
									<tr valign="top">
										<td width="20%" height="100%" id="field_types">
											<div id="when_edit" style="display: none;"></div>
											<table border="0" cellpadding="0" cellspacing="3" width="100%" style="border-collapse: separate; border-spacing: 3px;">
												<tbody>
													<tr>
														<td align="center" onclick="addRow('customHTML')" class="field_buttons" id="table_editor">
															<img src="<?php echo WD_FMC_URL; ?>/images/customHTML.png?ver=<?php echo get_option("wd_form_maker_version"); ?>" style="margin:5px" id="img_customHTML">
															<div>Custom HTML</div>
														</td>
														<td align="center" onclick="addRow('text')" class="field_buttons" id="table_text">
															<img src="<?php echo WD_FMC_URL; ?>/images/text.png?ver=<?php echo get_option("wd_form_maker_version"); ?>" style="margin:5px" id="img_text">
															<div>Text input</div>
														</td>
													</tr>
													<tr>
														<td align="center" onclick="alert('This field type is disabled in free version. If you need this functionality, you need to buy the commercial version.')" class="field_buttons field_disabled" id="table_checkbox">
															<img src="<?php echo WD_FMC_URL; ?>/images/checkbox.png?ver=<?php echo get_option("wd_form_maker_version"); ?>" style="margin:5px" id="img_checkbox">
															<div>Multiple Choice</div>
														</td>
														<td align="center" onclick="alert('This field type is disabled in free version. If you need this functionality, you need to buy the commercial version.')" class="field_buttons field_disabled" id="table_radio">
															<img src="<?php echo WD_FMC_URL; ?>/images/radio.png?ver=<?php echo get_option("wd_form_maker_version"); ?>" style="margin:5px" id="img_radio">
															<div>Single Choice</div>
														</td>
													</tr>
													<tr>
														<td align="center" onclick="alert('This field type is disabled in free version. If you need this functionality, you need to buy the commercial version.')" class="field_buttons field_disabled" id="table_survey">
															<img src="<?php echo WD_FMC_URL; ?>/images/survey.png?ver=<?php echo get_option("wd_form_maker_version"); ?>" style="margin:5px" id="img_survey">
															<div>Survey Tools</div>
														</td>           
														<td align="center" onclick="alert('This field type is disabled in free version. If you need this functionality, you need to buy the commercial version.')" class="field_buttons field_disabled" id="table_time_and_date">
															<img src="<?php echo WD_FMC_URL; ?>/images/time_and_date.png?ver=<?php echo get_option("wd_form_maker_version"); ?>" style="margin:5px" id="img_time_and_date">
															<div>Time and Date</div>
														</td>
												   </tr>
													<tr>
														<td align="center" onclick="alert('This field type is disabled in free version. If you need this functionality, you need to buy the commercial version.')" class="field_buttons field_disabled" id="table_select">
															<img src="<?php echo WD_FMC_URL; ?>/images/select.png?ver=<?php echo get_option("wd_form_maker_version"); ?>" style="margin:5px" id="img_select">
															<div>Select Box</div>
														</td>
														<td align="center" onclick="alert('This field type is disabled in free version. If you need this functionality, you need to buy the commercial version.')" class="field_buttons field_disabled" id="table_file_upload">
															<img src="<?php echo WD_FMC_URL; ?>/images/file_upload.png?ver=<?php echo get_option("wd_form_maker_version"); ?>" style="margin:5px" id="img_file_upload">
															<div>File Upload</div>
														</td>
													</tr>
													<tr>
														<td align="center" onclick="addRow('section_break')" class="field_buttons" id="table_section_break">
															<img src="<?php echo WD_FMC_URL; ?>/images/section_break.png?ver=<?php echo get_option("wd_form_maker_version"); ?>" style="margin:5px" id="img_section_break">
															<div>Section Break</div>
														</td>
														<td align="center" onclick="addRow('page_break')" class="field_buttons" id="table_page_break">
															<img src="<?php echo WD_FMC_URL; ?>/images/page_break.png?ver=<?php echo get_option("wd_form_maker_version"); ?>" style="margin:5px" id="img_page_break">
															<div>Page Break</div>
														</td>  
													</tr>
													<tr>
														<td align="center" onclick="addRow('map')" class="field_buttons" id="table_map">
															<img src="<?php echo WD_FMC_URL; ?>/images/map.png?ver=<?php echo get_option("wd_form_maker_version"); ?>" style="margin:5px" id="img_map">
															<div>Map</div>
														</td>  
														<td align="center" onclick="alert('This field type is disabled in free version. If you need this functionality, you need to buy the commercial version.')" id="table_paypal" class="field_buttons field_disabled">
															<img src="<?php echo WD_FMC_URL; ?>/images/paypal.png?ver=<?php echo get_option("wd_form_maker_version"); ?>" style="margin:5px" id="img_paypal">
															<div>PayPal</div>
													</td>       
												   </tr>
													<tr>
														<td align="center" onclick="addRow('captcha')" class="field_buttons" id="table_captcha">
															<img src="<?php echo WD_FMC_URL; ?>/images/captcha.png?ver=<?php echo get_option("wd_form_maker_version"); ?>" style="margin:5px" id="img_captcha">
															<div>Captcha</div>
														</td>
														<td align="center" onclick="addRow('button')" id="table_button" class="field_buttons">
															<img src="<?php echo WD_FMC_URL; ?>/images/button.png?ver=<?php echo get_option("wd_form_maker_version"); ?>" style="margin:5px" id="img_button">
															<div>Button</div>
														</td>			
													</tr>
												</tbody>
											</table>
										</td>
										<td width="40%" height="100%" align="left">
											<div id="edit_table"></div>
										</td>
										<td align="center" valign="top" style="background: url("<?php echo WD_FMC_URL . '/images/border2.png'; ?>") repeat-y;">&nbsp;</td>
										<td style="padding:15px;">
											<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" >
												<tr>
													<td align="right">
														<input type="radio" value="end" name="el_pos" checked="checked" id="pos_end" onclick="Disable()"/>
														At The End
														<input type="radio" value="begin" name="el_pos" id="pos_begin" onclick="Disable()"/>
														At The Beginning
														<input type="radio" value="before" name="el_pos" id="pos_before" onclick="Enable()"/>
														Before
														<select style="width: 100px; margin-left: 5px;" id="sel_el_pos" onclick="change_before()" disabled="disabled"></select>
														<br>
														<button class="fm-button field-save-button small" onclick="add(0, false); return false;">
															Save
															<span></span>
														</button>
														<button class="fm-button cancel-button small" onclick="close_window(); return false;">
															Cancel
															<span></span>
														</button>
														<hr style="margin-bottom:10px" />
													</td>
												</tr>
												<tr height="100%" valign="top">
													<td id="show_table"></td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					<input type="hidden" id="old" />
					<input type="hidden" id="old_selected" />
					<input type="hidden" id="element_type" />
					<input type="hidden" id="editing_id" />
					<input type="hidden" value="<?php echo WD_FMC_URL; ?>" id="form_plugins_url" />
					<div id="main_editor" style="position: fixed; display: none; z-index: 140;">
						<?php if (user_can_richedit()) {
							wp_editor('', 'form_maker_editor', array('teeny' => FALSE, 'textarea_name' => 'form_maker_editor', 'media_buttons' => FALSE, 'textarea_rows' => 5));
						}
						else { ?>
							<textarea name="form_maker_editor" id="form_maker_editor" cols="40" rows="5" style="width: 440px; height: 350px;" class="mce_editable" aria-hidden="true"></textarea>
							<?php
						}
						?>
					</div>
				</div>
					<?php if (!function_exists('the_editor')) { ?>
						<iframe id="tinymce" style="display: none;"></iframe>
					<?php } ?>
				
				<div class="fm-edit-content">		
					<div class="fm-drag-and-drop">
						<div>
							<label for="enable_sortable">Enable Drag & Drop</label>
							<button name="sortable" id="enable_sortable" class="fm-checkbox-radio-button <?php echo $row->sortable == 1 ? 'fm-yes' : 'fm-no' ?>" onclick="enable_drag(this); return false;" value="<?php echo $row->sortable; ?>">
								<span></span>
							</button>	
							<input type="hidden" name="sortable" id="sortable_hidden" value="<?php echo $row->sortable; ?>"/>					
						</div>
						<div>
							You can use drag and drop to move the fields up/down for the change of the order and left/right for creating columns within the form.
						</div>
					</div>
					<fieldset>
						<legend></legend>
						<?php if ($id) { ?>
							<div style="margin: 8px; display: table; width: 100%;" id="page_bar">
								<div id="page_navigation" style="display: table-row;">
									<div align="center" id="pages" show_title="<?php echo $row->show_title; ?>" show_numbers="<?php echo $row->show_numbers; ?>" type="<?php echo $row->pagination; ?>" style="display: table-cell;  width:90%;"></div>
									<div align="left" id="edit_page_navigation" style="display: table-cell; vertical-align: middle;"></div>
								</div>
							</div>
							<div id="take" class="main">
								<?php echo $row->form_front; ?>
							</div>
						<?php } else { ?>
							<div style="margin:8px; display:table; width:100%"  id="page_bar">
								<div id="page_navigation" style="display:table-row">
									<div align="center" id="pages" show_title="false" show_numbers="true" type="none" style="display:table-cell;  width:90%"></div>
									<div align="left" id="edit_page_navigation" style="display:table-cell; vertical-align: middle;"></div>
								</div>
							</div>
							<div id="take" class="main">
								<div class="wdform-page-and-images" style="display:table; border-top:0px solid black;">
									<div id="form_id_tempform_view1" class="wdform_page" page_title="Untitled page" next_title="Next" next_type="text" next_class="wdform-page-button" next_checkable="false" previous_title="Previous" previous_type="text" previous_class="wdform-page-button" previous_checkable="false">
										<div class="wdform_section">
											<div class="wdform_column"></div>
										</div>
										<div valign="top" class="wdform_footer" style="width: 100%;">
											<div style="width: 100%;">
												<div style="width: 100%; display: table; padding-top:10px;">
													<div style="display: table-row-group;">
														<div id="form_id_temppage_nav1" style="display: table-row;"></div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div id="form_id_tempform_view_img1" style="float: right;">
										<div>
											<img src="<?php echo WD_FMC_URL . '/images/minus.png?ver='. get_option("wd_form_maker_version"); ?>" title="Show or hide the page" class="page_toolbar" onClick="show_or_hide('1')" onMouseOver="chnage_icons_src(this,'minus')" onmouseout="chnage_icons_src(this,'minus')" id="show_page_img_1"/>
											<img src="<?php echo WD_FMC_URL . '/images/page_delete.png?ver='. get_option("wd_form_maker_version"); ?>" title="Delete the page" class="page_toolbar" onClick="remove_page('1')" onMouseOver="chnage_icons_src(this,'page_delete')" onmouseout="chnage_icons_src(this,'page_delete')"/>
											<img src="<?php echo WD_FMC_URL . '/images/page_delete_all.png?ver='. get_option("wd_form_maker_version"); ?>" title="Delete the page with fields" class="page_toolbar" onClick="remove_page_all('1')" onMouseOver="chnage_icons_src(this,'page_delete_all')" onmouseout="chnage_icons_src(this,'page_delete_all')"/>
											<img src="<?php echo WD_FMC_URL . '/images/page_edit.png?ver='. get_option("wd_form_maker_version"); ?>" title="Edit the page" class="page_toolbar" onClick="edit_page_break('1')" onMouseOver="chnage_icons_src(this,'page_edit')"  onmouseout="chnage_icons_src(this,'page_edit')"/>
										</div>
									</div>
								</div>
							</div>
						<?php } ?>
					</fieldset>
				</div>
				<input type="hidden" name="form_front" id="form_front" />
				<input type="hidden" name="form_fields" id="form_fields" />
				<input type="hidden" name="pagination" id="pagination" />
				<input type="hidden" name="show_title" id="show_title" />
				<input type="hidden" name="show_numbers" id="show_numbers" />
				<input type="hidden" name="public_key" id="public_key" />
				<input type="hidden" name="private_key" id="private_key" />
				<input type="hidden" name="recaptcha_theme" id="recaptcha_theme" />
				<input type="hidden" id="label_order" name="label_order" value="<?php echo $row->label_order; ?>" />
				<input type="hidden" id="label_order_current" name="label_order_current" value="<?php echo $row->label_order_current; ?>" />  
				<input type="hidden" name="counter" id="counter" value="<?php echo $row->counter; ?>" />
				<input type="hidden" id="araqel" value="0" />
				<input type="hidden" name="backup_id" id="backup_id" value="<?php echo $row->backup_id;?>">
				
				<?php if ($id) { ?>
				<script type="text/javascript">
					function set_preview() {
						jQuery("#preview_form").attr("onclick", "tb_show('', '<?php echo add_query_arg(array('action' => 'FormMakerPreview_fmc', 'form_id' => $row->id), admin_url('admin-ajax.php')); ?>&test_theme=" + jQuery('#theme').val() + "&width=1000&height=500&TB_iframe=1'); return false;");
						jQuery("#edit_css").attr("onclick", "tb_show('', '<?php echo add_query_arg(array('action' => 'FormMakerEditCSS_fmc', 'form_id' => $row->id), admin_url('admin-ajax.php')); ?>&id=" + jQuery('#theme').val() + "&width=800&height=500&TB_iframe=1'); return false;");
					}
					function formOnload() {
						for (t = 0; t < <?php echo $row->counter; ?>; t++) {
							if (document.getElementById(t + "_typeform_id_temp")) {
								if (document.getElementById(t + "_typeform_id_temp").value == "type_map" || document.getElementById(t + "_typeform_id_temp").value == "type_mark_map") {
									if_gmap_init(t);
									for (q = 0; q < 20; q++) {
										if (document.getElementById(t + "_elementform_id_temp").getAttribute("long" + q)) {
											w_long = parseFloat(document.getElementById(t + "_elementform_id_temp").getAttribute("long" + q));
											w_lat = parseFloat(document.getElementById(t + "_elementform_id_temp").getAttribute("lat" + q));
											w_info = parseFloat(document.getElementById(t + "_elementform_id_temp").getAttribute("info" + q));
											add_marker_on_map(t, q, w_long, w_lat, w_info, false);
										}
									}
								}
								else if (document.getElementById(t + "_typeform_id_temp").value == "type_date") {
									// Calendar.setup({
									  // inputField:t + "_elementform_id_temp",
									  // ifFormat:document.getElementById(t + "_buttonform_id_temp").getAttribute('format'),
									  // button:t + "_buttonform_id_temp",
									  // align:"Tl",
									  // singleClick:true,
									  // firstDay:0
									// });
								}
								else if (document.getElementById(t + "_typeform_id_temp").value == "type_name") {
									var myu = t;
									jQuery(document).ready(function () {
										jQuery("#" + myu + "_mini_label_first").click(function () {
											if (jQuery(this).children('input').length == 0) {
												var first = "<input type='text' id='first' class='first' style='outline:none; border:none; background:none;' value=\"" + jQuery(this).text() + "\">";
												jQuery(this).html(first);
												jQuery("input.first").focus();
												jQuery("input.first").blur(function () {
													var id_for_blur = document.getElementById('first').parentNode.id.split('_');
													var value = jQuery(this).val();
													jQuery("#" + id_for_blur[0] + "_mini_label_first").text(value);
												});
											}
										});
										jQuery("label#" + myu + "_mini_label_last").click(function () {
											if (jQuery(this).children('input').length == 0) {
												var last = "<input type='text' id='last' class='last'  style='outline:none; border:none; background:none;' value=\"" + jQuery(this).text() + "\">";
												jQuery(this).html(last);
												jQuery("input.last").focus();
												jQuery("input.last").blur(function () {
													var id_for_blur = document.getElementById('last').parentNode.id.split('_');
													var value = jQuery(this).val();
													jQuery("#" + id_for_blur[0] + "_mini_label_last").text(value);
												});
											}
										});
										jQuery("label#" + myu + "_mini_label_title").click(function () {
											if (jQuery(this).children('input').length == 0) {
												var title_ = "<input type='text' id='title_' class='title_'  style='outline:none; border:none; background:none; width:50px;' value=\"" + jQuery(this).text() + "\">";
												jQuery(this).html(title_);
												jQuery("input.title_").focus();
												jQuery("input.title_").blur(function () {
													var id_for_blur = document.getElementById('title_').parentNode.id.split('_');
													var value = jQuery(this).val();
													jQuery("#" + id_for_blur[0] + "_mini_label_title").text(value);
												});
											}
										});
										jQuery("label#" + myu + "_mini_label_middle").click(function () {
											if (jQuery(this).children('input').length == 0) {
												var middle = "<input type='text' id='middle' class='middle'  style='outline:none; border:none; background:none;' value=\"" + jQuery(this).text() + "\">";
												jQuery(this).html(middle);
												jQuery("input.middle").focus();
												jQuery("input.middle").blur(function () {
													var id_for_blur = document.getElementById('middle').parentNode.id.split('_');
													var value = jQuery(this).val();
													jQuery("#" + id_for_blur[0] + "_mini_label_middle").text(value);
												});
											}
										});
									});
								}
								else if (document.getElementById(t + "_typeform_id_temp").value == "type_phone") {
									var myu = t;
									jQuery(document).ready(function () {
										jQuery("label#" + myu + "_mini_label_area_code").click(function () {
											if (jQuery(this).children('input').length == 0) {
												var area_code = "<input type='text' id='area_code' class='area_code' size='10' style='outline:none; border:none; background:none;' value=\"" + jQuery(this).text() + "\">";
												jQuery(this).html(area_code);
												jQuery("input.area_code").focus();
												jQuery("input.area_code").blur(function () {
													var id_for_blur = document.getElementById('area_code').parentNode.id.split('_');
													var value = jQuery(this).val();
													jQuery("#" + id_for_blur[0] + "_mini_label_area_code").text(value);
												});
											}
										});
										jQuery("label#" + myu + "_mini_label_phone_number").click(function () {
											if (jQuery(this).children('input').length == 0) {
												var phone_number = "<input type='text' id='phone_number' class='phone_number'  style='outline:none; border:none; background:none;' value=\"" + jQuery(this).text() + "\">";
												jQuery(this).html(phone_number);
												jQuery("input.phone_number").focus();
												jQuery("input.phone_number").blur(function () {
													var id_for_blur = document.getElementById('phone_number').parentNode.id.split('_');
													var value = jQuery(this).val();
													jQuery("#" + id_for_blur[0] + "_mini_label_phone_number").text(value);
												});
											}
										});
									});
								}
								else if (document.getElementById(t + "_typeform_id_temp").value == "type_date_fields") {
									var myu = t;
									jQuery(document).ready(function () {
										jQuery("label#" + myu + "_day_label").click(function () {
											if (jQuery(this).children('input').length == 0) {
												var day = "<input type='text' id='day' class='day' size='8' style='outline:none; border:none; background:none;' value=\"" + jQuery(this).text() + "\">";
												jQuery(this).html(day);
												jQuery("input.day").focus();
												jQuery("input.day").blur(function () {
													var id_for_blur = document.getElementById('day').parentNode.id.split('_');
													var value = jQuery(this).val();
													jQuery("#" + id_for_blur[0] + "_day_label").text(value);
												});
											}
										});
										jQuery("label#" + myu + "_month_label").click(function () {
											if (jQuery(this).children('input').length == 0) {
												var month = "<input type='text' id='month' class='month' size='8' style='outline:none; border:none; background:none;' value=\"" + jQuery(this).text() + "\">";
												jQuery(this).html(month);
												jQuery("input.month").focus();
												jQuery("input.month").blur(function () {
													var id_for_blur = document.getElementById('month').parentNode.id.split('_');
													var value = jQuery(this).val();
													jQuery("#" + id_for_blur[0] + "_month_label").text(value);
												});
											}
										});
										jQuery("label#" + myu + "_year_label").click(function () {
											if (jQuery(this).children('input').length == 0) {
												var year = "<input type='text' id='year' class='year' size='8' style='outline:none; border:none; background:none;' value=\"" + jQuery(this).text() + "\">";
												jQuery(this).html(year);
												jQuery("input.year").focus();
												jQuery("input.year").blur(function () {
													var id_for_blur = document.getElementById('year').parentNode.id.split('_');
													var value = jQuery(this).val();
													jQuery("#" + id_for_blur[0] + "_year_label").text(value);
												});
											}
										});
									});
								}
								else if (document.getElementById(t + "_typeform_id_temp").value == "type_time") {
									var myu = t;
									jQuery(document).ready(function () {
										jQuery("label#" + myu + "_mini_label_hh").click(function () {
											if (jQuery(this).children('input').length == 0) {
												var hh = "<input type='text' id='hh' class='hh' size='4' style='outline:none; border:none; background:none;' value=\"" + jQuery(this).text() + "\">";
												jQuery(this).html(hh);
												jQuery("input.hh").focus();
												jQuery("input.hh").blur(function () {
													var id_for_blur = document.getElementById('hh').parentNode.id.split('_');
													var value = jQuery(this).val();
													jQuery("#" + id_for_blur[0] + "_mini_label_hh").text(value);
												});
											}
										});
										jQuery("label#" + myu + "_mini_label_mm").click(function () {
											if (jQuery(this).children('input').length == 0) {
												var mm = "<input type='text' id='mm' class='mm' size='4' style='outline:none; border:none; background:none;' value=\"" + jQuery(this).text() + "\">";
												jQuery(this).html(mm);
												jQuery("input.mm").focus();
												jQuery("input.mm").blur(function () {
													var id_for_blur = document.getElementById('mm').parentNode.id.split('_');
													var value = jQuery(this).val();
													jQuery("#" + id_for_blur[0] + "_mini_label_mm").text(value);
												});
											}
										});
										jQuery("label#" + myu + "_mini_label_ss").click(function () {
											if (jQuery(this).children('input').length == 0) {
												var ss = "<input type='text' id='ss' class='ss' size='4' style='outline:none; border:none; background:none;' value=\"" + jQuery(this).text() + "\">";
												jQuery(this).html(ss);
												jQuery("input.ss").focus();
												jQuery("input.ss").blur(function () {
													var id_for_blur = document.getElementById('ss').parentNode.id.split('_');
													var value = jQuery(this).val();
													jQuery("#" + id_for_blur[0] + "_mini_label_ss").text(value);
												});
											}
										});
										jQuery("label#" + myu + "_mini_label_am_pm").click(function () {
											if (jQuery(this).children('input').length == 0) {
												var am_pm = "<input type='text' id='am_pm' class='am_pm' size='4' style='outline:none; border:none; background:none;' value=\"" + jQuery(this).text() + "\">";
												jQuery(this).html(am_pm);
												jQuery("input.am_pm").focus();
												jQuery("input.am_pm").blur(function () {
													var id_for_blur = document.getElementById('am_pm').parentNode.id.split('_');
													var value = jQuery(this).val();
													jQuery("#" + id_for_blur[0] + "_mini_label_am_pm").text(value);
												});
											}
										});
									});
								}
								else if (document.getElementById(t + "_typeform_id_temp").value == "type_paypal_price") {
									var myu = t;
									jQuery(document).ready(function () {
										jQuery("#" + myu + "_mini_label_dollars").click(function () {
											if (jQuery(this).children('input').length == 0) {
												var dollars = "<input type='text' id='dollars' class='dollars' style='outline:none; border:none; background:none;' value=\"" + jQuery(this).text() + "\">";
												jQuery(this).html(dollars);
												jQuery("input.dollars").focus();
												jQuery("input.dollars").blur(function () {
													var id_for_blur = document.getElementById('dollars').parentNode.id.split('_');
													var value = jQuery(this).val();
													jQuery("#" + id_for_blur[0] + "_mini_label_dollars").text(value);
												});
											}
										});
										jQuery("label#" + myu + "_mini_label_cents").click(function () {
											if (jQuery(this).children('input').length == 0) {
												var cents = "<input type='text' id='cents' class='cents'  style='outline:none; border:none; background:none;' value=\"" + jQuery(this).text() + "\">";
												jQuery(this).html(cents);
												jQuery("input.cents").focus();
												jQuery("input.cents").blur(function () {
													var id_for_blur = document.getElementById('cents').parentNode.id.split('_');
													var value = jQuery(this).val();
													jQuery("#" + id_for_blur[0] + "_mini_label_cents").text(value);
												});
											}
										});
									});
								}
								else if (document.getElementById(t + "_typeform_id_temp").value == "type_address") {
									var myu = t;
									jQuery(document).ready(function () {
										jQuery("label#" + myu + "_mini_label_street1").click(function () {
											if (jQuery(this).children('input').length == 0) {
												var street1 = "<input type='text' id='street1' class='street1' style='outline:none; border:none; background:none;' value=\"" + jQuery(this).text() + "\">";
												jQuery(this).html(street1);
												jQuery("input.street1").focus();
												jQuery("input.street1").blur(function () {
													var id_for_blur = document.getElementById('street1').parentNode.id.split('_');
													var value = jQuery(this).val();
													jQuery("#" + id_for_blur[0] + "_mini_label_street1").text(value);
												});
											}
										});
										jQuery("label#" + myu + "_mini_label_street2").click(function () {
											if (jQuery(this).children('input').length == 0) {
												var street2 = "<input type='text' id='street2' class='street2'  style='outline:none; border:none; background:none;' value=\"" + jQuery(this).text() + "\">";
												jQuery(this).html(street2);
												jQuery("input.street2").focus();
												jQuery("input.street2").blur(function () {
													var id_for_blur = document.getElementById('street2').parentNode.id.split('_');
													var value = jQuery(this).val();
													jQuery("#" + id_for_blur[0] + "_mini_label_street2").text(value);
											  });
											}
										});
										jQuery("label#" + myu + "_mini_label_city").click(function () {
											if (jQuery(this).children('input').length == 0) {
												var city = "<input type='text' id='city' class='city'  style='outline:none; border:none; background:none;' value=\"" + jQuery(this).text() + "\">";
												jQuery(this).html(city);
												jQuery("input.city").focus();
												jQuery("input.city").blur(function () {
													var id_for_blur = document.getElementById('city').parentNode.id.split('_');
													var value = jQuery(this).val();
													jQuery("#" + id_for_blur[0] + "_mini_label_city").text(value);
												});
											}
										});
										jQuery("label#" + myu + "_mini_label_state").click(function () {
											if (jQuery(this).children('input').length == 0) {
												var state = "<input type='text' id='state' class='state'  style='outline:none; border:none; background:none;' value=\"" + jQuery(this).text() + "\">";
												jQuery(this).html(state);
												jQuery("input.state").focus();
												jQuery("input.state").blur(function () {
													var id_for_blur = document.getElementById('state').parentNode.id.split('_');
													var value = jQuery(this).val();
													jQuery("#" + id_for_blur[0] + "_mini_label_state").text(value);
											  });
											}
										});
										jQuery("label#" + myu + "_mini_label_postal").click(function () {
											if (jQuery(this).children('input').length == 0) {
												var postal = "<input type='text' id='postal' class='postal'  style='outline:none; border:none; background:none;' value=\"" + jQuery(this).text() + "\">";
												jQuery(this).html(postal);
												jQuery("input.postal").focus();
												jQuery("input.postal").blur(function () {
													var id_for_blur = document.getElementById('postal').parentNode.id.split('_');
													var value = jQuery(this).val();
													jQuery("#" + id_for_blur[0] + "_mini_label_postal").text(value);
												});
											}
										});
										jQuery("label#" + myu + "_mini_label_country").click(function () {
											if (jQuery(this).children('input').length == 0) {
												var country = "<input type='country' id='country' class='country'  style='outline:none; border:none; background:none;' value=\"" + jQuery(this).text() + "\">";
												jQuery(this).html(country);
												jQuery("input.country").focus();
												jQuery("input.country").blur(function () {
													var id_for_blur = document.getElementById('country').parentNode.id.split('_');
													var value = jQuery(this).val();
													jQuery("#" + id_for_blur[0] + "_mini_label_country").text(value);
												});
											}
										});
									});
								}
								else if (document.getElementById(t + "_typeform_id_temp").value == "type_scale_rating") {
									var myu = t;
									jQuery(document).ready(function () {
										jQuery("#" + myu + "_mini_label_worst").click(function () {
											if (jQuery(this).children('input').length == 0) {
												var worst = "<input type='text' id='worst' class='worst' size='6' style='outline:none; border:none; background:none; font-size:11px;' value=\"" + jQuery(this).text() + "\">";
												jQuery(this).html(worst);
												jQuery("input.worst").focus();
												jQuery("input.worst").blur(function () {
													var id_for_blur = document.getElementById('worst').parentNode.id.split('_');
													var value = jQuery(this).val();
													jQuery("#" + id_for_blur[0] + "_mini_label_worst").text(value);
											  });
											}
										});
										jQuery("label#" + myu + "_mini_label_best").click(function () {
											if (jQuery(this).children('input').length == 0) {
												var best = "<input type='text' id='best' class='best' size='6' style='outline:none; border:none; background:none; font-size:11px;' value=\"" + jQuery(this).text() + "\">";
												jQuery(this).html(best);
												jQuery("input.best").focus();
												jQuery("input.best").blur(function () {
													var id_for_blur = document.getElementById('best').parentNode.id.split('_');
													var value = jQuery(this).val();
													jQuery("#" + id_for_blur[0] + "_mini_label_best").text(value);
												});
											}
										});
									});
								}
								else if (document.getElementById(t + "_typeform_id_temp").value == "type_spinner") {
									var spinner_value = document.getElementById(t + "_elementform_id_temp").value;
									var spinner_min_value = document.getElementById(t + "_min_valueform_id_temp").value;
									var spinner_max_value = document.getElementById(t + "_max_valueform_id_temp").value;
									var spinner_step = document.getElementById(t + "_stepform_id_temp").value;
									jQuery("#" + t + "_elementform_id_temp")[0].spin = null;
									spinner = jQuery("#" + t + "_elementform_id_temp").spinner();
									spinner.spinner("value", spinner_value);
									jQuery("#" + t + "_elementform_id_temp").spinner({ min:spinner_min_value});
									jQuery("#" + t + "_elementform_id_temp").spinner({ max:spinner_max_value});
									jQuery("#" + t + "_elementform_id_temp").spinner({ step:spinner_step});
								}
								else if (document.getElementById(t + "_typeform_id_temp").value == "type_slider") {
									var slider_value = document.getElementById(t + "_slider_valueform_id_temp").value;
									var slider_min_value = document.getElementById(t + "_slider_min_valueform_id_temp").value;
									var slider_max_value = document.getElementById(t + "_slider_max_valueform_id_temp").value;
									var slider_element_value = document.getElementById(t + "_element_valueform_id_temp");
									var slider_value_save = document.getElementById(t + "_slider_valueform_id_temp");
									jQuery("#" + t + "_elementform_id_temp")[0].slide = null;
									jQuery(function () {
										jQuery("#" + t + "_elementform_id_temp").slider({
											range:"min",
											value:eval(slider_value),
											min:eval(slider_min_value),
											max:eval(slider_max_value),
											slide:function (event, ui) {
												slider_element_value.innerHTML = "" + ui.value;
												slider_value_save.value = "" + ui.value;
											}
										});
									});
								}
								else if (document.getElementById(t + "_typeform_id_temp").value == "type_range") {
									var spinner_value0 = document.getElementById(t + "_elementform_id_temp0").value;
									var spinner_step = document.getElementById(t + "_range_stepform_id_temp").value;
									jQuery("#" + t + "_elementform_id_temp0")[0].spin = null;
									jQuery("#" + t + "_elementform_id_temp1")[0].spin = null;
									spinner0 = jQuery("#" + t + "_elementform_id_temp0").spinner();
									spinner0.spinner("value", spinner_value0);
									jQuery("#" + t + "_elementform_id_temp0").spinner({ step:spinner_step});
									var spinner_value1 = document.getElementById(t + "_elementform_id_temp1").value;
									spinner1 = jQuery("#" + t + "_elementform_id_temp1").spinner();
									spinner1.spinner("value", spinner_value1);
									jQuery("#" + t + "_elementform_id_temp1").spinner({ step:spinner_step});
									var myu = t;
									jQuery(document).ready(function () {
										jQuery("#" + myu + "_mini_label_from").click(function () {
											if (jQuery(this).children('input').length == 0) {
												var from = "<input type='text' id='from' class='from' size='6' style='outline:none; border:none; background:none; font-size:11px;' value=\"" + jQuery(this).text() + "\">";
												jQuery(this).html(from);
												jQuery("input.from").focus();
												jQuery("input.from").blur(function () {
													var id_for_blur = document.getElementById('from').parentNode.id.split('_');
													var value = jQuery(this).val();
													jQuery("#" + id_for_blur[0] + "_mini_label_from").text(value);
												});
											}
									});
									jQuery("label#" + myu + "_mini_label_to").click(function () {
										if (jQuery(this).children('input').length == 0) {
											var to = "<input type='text' id='to' class='to' size='6' style='outline:none; border:none; background:none; font-size:11px;' value=\"" + jQuery(this).text() + "\">";
											jQuery(this).html(to);
											jQuery("input.to").focus();
											jQuery("input.to").blur(function () {
												var id_for_blur = document.getElementById('to').parentNode.id.split('_');
												var value = jQuery(this).val();
												jQuery("#" + id_for_blur[0] + "_mini_label_to").text(value);
											});
										}
									});
								});
							}
						}
					}
						
						remove_whitespace(document.getElementById('take'));
						form_view = 1;
						form_view_count = 0;
						
						for (i = 1; i <= 30; i++) {
							if (document.getElementById('form_id_tempform_view' + i)) {
								form_view_count++;
								form_view_max = i;
								tbody_img = document.createElement('div');
								tbody_img.setAttribute('id', 'form_id_tempform_view_img' + i);
								tbody_img.style.cssText = "float:right";
								tr_img = document.createElement('div');
								var img = document.createElement('img');
									img.setAttribute('src', '<?php echo WD_FMC_URL; ?>/images/minus.png?ver=<?php echo get_option("wd_form_maker_version"); ?>');
									img.setAttribute('title', 'Show or hide the page');
									img.setAttribute("class", "page_toolbar");
									img.setAttribute('id', 'show_page_img_' + i);
									img.setAttribute('onClick', 'show_or_hide("' + i + '")');
									img.setAttribute("onmouseover", 'chnage_icons_src(this,"minus")');
									img.setAttribute("onmouseout", 'chnage_icons_src(this,"minus")');
								var img_X = document.createElement("img");
									img_X.setAttribute("src", "<?php echo WD_FMC_URL; ?>/images/page_delete.png?ver=<?php echo get_option("wd_form_maker_version"); ?>");
									img_X.setAttribute('title', 'Delete the page');
									img_X.setAttribute("class", "page_toolbar");
									img_X.setAttribute("onclick", 'remove_page("' + i + '")');
									img_X.setAttribute("onmouseover", 'chnage_icons_src(this,"page_delete")');
									img_X.setAttribute("onmouseout", 'chnage_icons_src(this,"page_delete")');
								var img_X_all = document.createElement("img");
									img_X_all.setAttribute("src", "<?php echo WD_FMC_URL; ?>/images/page_delete_all.png?ver=<?php echo get_option("wd_form_maker_version"); ?>");
									img_X_all.setAttribute('title', 'Delete the page with fields');
									img_X_all.setAttribute("class", "page_toolbar");
									img_X_all.setAttribute("onclick", 'remove_page_all("' + i + '")');
									img_X_all.setAttribute("onmouseover", 'chnage_icons_src(this,"page_delete_all")');
									img_X_all.setAttribute("onmouseout", 'chnage_icons_src(this,"page_delete_all")');
								var img_EDIT = document.createElement("img");
									img_EDIT.setAttribute("src", "<?php echo WD_FMC_URL; ?>/images/page_edit.png?ver=<?php echo get_option("wd_form_maker_version"); ?>");
									img_EDIT.setAttribute('title', 'Edit the page');
									img_EDIT.setAttribute("class", "page_toolbar");
									img_EDIT.setAttribute("onclick", 'edit_page_break("' + i + '")');
									img_EDIT.setAttribute("onmouseover", 'chnage_icons_src(this,"page_edit")');
									img_EDIT.setAttribute("onmouseout", 'chnage_icons_src(this,"page_edit")');
								tr_img.appendChild(img);
								tr_img.appendChild(img_X);
								tr_img.appendChild(img_X_all);
								tr_img.appendChild(img_EDIT);
								tbody_img.appendChild(tr_img);
								document.getElementById('form_id_tempform_view' + i).parentNode.appendChild(tbody_img);
							}
						}
						
						if (form_view_count > 1) {
							for (i = 1; i <= form_view_max; i++) {
								if (document.getElementById('form_id_tempform_view' + i)) {
									first_form_view = i;
									break;
								}
							}
							form_view = form_view_max;
							need_enable = false;
							generate_page_nav(first_form_view);
							var img_EDIT = document.createElement("img");
								img_EDIT.setAttribute("src", "<?php echo WD_FMC_URL . '/images/edit.png?ver='.get_option("wd_form_maker_version"); ?>");
								img_EDIT.style.cssText = "margin-left:40px; cursor:pointer";
								img_EDIT.setAttribute("onclick", 'el_page_navigation()');
							var td_EDIT = document.getElementById("edit_page_navigation");
								td_EDIT.appendChild(img_EDIT);
							document.getElementById('page_navigation').appendChild(td_EDIT);
						}
						document.getElementById('araqel').value = 1;
					}
					jQuery(window).load(function () {
						formOnload();
					});
					jQuery(function() {
						jQuery('.wdform_section .wdform_column:last-child').each(function() {
							jQuery(this).parent().append(jQuery('<div></div>').addClass("wdform_column"));		
						});
							
						sortable_columns();
						if(<?php echo $row->sortable ?>==1) {
							jQuery( ".wdform_arrows" ).hide();
							all_sortable_events();
						}
						else
							jQuery('.wdform_column').sortable( "disable" );	
						  
					});
				</script>
				<?php
				} else { ?>
					<script type="text/javascript">
						jQuery(function() {
							jQuery('.wdform_section .wdform_column:last-child').each(function() {
								jQuery(this).parent().append(jQuery('<div></div>').addClass("wdform_column"));		
							});
							sortable_columns();
							all_sortable_events();
						});
					</script>
				<?php } ?>
			<input type="hidden" name="option" value="com_formmaker" />
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="cid[]" value="<?php echo $row->id; ?>" />
			<input type="hidden" id="task" name="task" value=""/>
			<input type="hidden" id="current_id" name="current_id" value="<?php echo $row->id; ?>" />
		</form>
		<?php
	}

  public function edit_old($id) {
    // header("X-XSS-Protection: 0");
    $row = $this->model->get_row_data($id);
    $themes = $this->model->get_theme_rows_data('old');
    $labels = array();
    $label_id = array();
    $label_order_original = array();
    $label_type = array();
    $label_all = explode('#****#', $row->label_order);
    $label_all = array_slice($label_all, 0, count($label_all) - 1);
    foreach ($label_all as $key => $label_each) {
      $label_id_each = explode('#**id**#', $label_each);
      array_push($label_id, $label_id_each[0]);
      $label_oder_each = explode('#**label**#', $label_id_each[1]);
      array_push($label_order_original, addslashes($label_oder_each[0]));
      array_push($label_type, $label_oder_each[1]);
    }
    $labels['id'] = '"' . implode('","', $label_id) . '"';
    $labels['label'] = '"' . implode('","', $label_order_original) . '"';
    $labels['type'] = '"' . implode('","', $label_type) . '"';

    $page_title = (($id != 0) ? 'Edit form ' . $row->title : 'Create new form');
    ?>
    <script type="text/javascript">
      var plugin_url = "<?php echo WD_FMC_URL; ?>";
    </script>
    <script src="<?php echo WD_FMC_URL . '/js/formmaker_free.js'; ?>?ver=<?php echo get_option("wd_form_maker_version"); ?>" type="text/javascript"></script>
    <script type="text/javascript">
      function submitbutton() {
        if (!document.getElementById('araqel') || (document.getElementById('araqel').value == '0')) {
          alert('Please wait while page loading.');
          return false;
        }
        tox = '';
        l_id_array = [<?php echo $labels['id']?>];
        l_label_array = [<?php echo $labels['label']?>];
        l_type_array = [<?php echo $labels['type']?>];
        l_id_removed = [];      
        for (x=0; x< l_id_array.length; x++) {
          l_id_removed[l_id_array[x]]=true;
        }
        for (t=1;t<=form_view_max;t++) {
          if (document.getElementById('form_id_tempform_view'+t)) {
            form_view_element=document.getElementById('form_id_tempform_view'+t);		
            n=form_view_element.childNodes.length-2;
            for(q=0;q<=n;q++) {
              if (form_view_element.childNodes[q].nodeType!=3) {
                if (!form_view_element.childNodes[q].id) {
                  GLOBAL_tr=form_view_element.childNodes[q];
                  for (x=0; x < GLOBAL_tr.firstChild.childNodes.length; x++) {
                    table=GLOBAL_tr.firstChild.childNodes[x];
                    tbody=table.firstChild;
                    for (y=0; y < tbody.childNodes.length; y++) {
                      is_in_old=false;
                      tr=tbody.childNodes[y];
                      l_id=tr.id;
                      l_label=document.getElementById( tr.id+'_element_labelform_id_temp').innerHTML;
                      l_label = l_label.replace(/(\r\n|\n|\r)/gm," ");
                      l_type=tr.getAttribute('type');
                      for (z = 0; z < l_id_array.length; z++) {
                        if (l_id_array[z] == l_id) {
                          if (l_type_array[z] == "type_address") {
                            if (document.getElementById(l_id + "_mini_label_street1")) {
                              l_id_removed[l_id_array[z]] = false;
                            }
                            if (document.getElementById(l_id+"_mini_label_street2")) {
                              l_id_removed[parseInt(l_id_array[z]) + 1] = false;
                            }
                            if (document.getElementById(l_id+"_mini_label_city")) {
                              l_id_removed[parseInt(l_id_array[z]) + 2] = false;	
                            }
                            if (document.getElementById(l_id+"_mini_label_state")) {
                              l_id_removed[parseInt(l_id_array[z]) + 3] = false;
                            }
                            if (document.getElementById(l_id+"_mini_label_postal")) {
                              l_id_removed[parseInt(l_id_array[z]) + 4] = false;
                            }
                            if (document.getElementById(l_id+"_mini_label_country")) {
                              l_id_removed[parseInt(l_id_array[z]) + 5] = false;	
                            }
                            z = z + 5;
                          }
                          else {
                            l_id_removed[l_id] = false;
                          }
                        }
                      }
                      if (tr.getAttribute('type')=="type_address") {
                        addr_id=parseInt(tr.id);
                        id_for_country= addr_id;
                        if(document.getElementById(id_for_country+"_mini_label_street1"))
                          tox=tox+addr_id+'#**id**#'+document.getElementById(id_for_country+"_mini_label_street1").innerHTML+'#**label**#'+tr.getAttribute('type')+'#****#';addr_id++; 
                        if(document.getElementById(id_for_country+"_mini_label_street2"))
                          tox=tox+addr_id+'#**id**#'+document.getElementById(id_for_country+"_mini_label_street2").innerHTML+'#**label**#'+tr.getAttribute('type')+'#****#';addr_id++; 
                        if(document.getElementById(id_for_country+"_mini_label_city"))
                          tox=tox+addr_id+'#**id**#'+document.getElementById(id_for_country+"_mini_label_city").innerHTML+'#**label**#'+tr.getAttribute('type')+'#****#';	addr_id++; 
                        if(document.getElementById(id_for_country+"_mini_label_state"))
                          tox=tox+addr_id+'#**id**#'+document.getElementById(id_for_country+"_mini_label_state").innerHTML+'#**label**#'+tr.getAttribute('type')+'#****#';	addr_id++;
                        if(document.getElementById(id_for_country+"_mini_label_postal"))									
                          tox=tox+addr_id+'#**id**#'+document.getElementById(id_for_country+"_mini_label_postal").innerHTML+'#**label**#'+tr.getAttribute('type')+'#****#';	addr_id++; 
                        if(document.getElementById(id_for_country+"_mini_label_country"))									
                          tox=tox+addr_id+'#**id**#'+document.getElementById(id_for_country+"_mini_label_country").innerHTML+'#**label**#'+tr.getAttribute('type')+'#****#'; 
                      }
                      else {
                        tox = tox+l_id+'#**id**#'+l_label+'#**label**#'+l_type+'#****#';
                      }
                    }
                  }
                }
              }
            }
          }	
        }
        document.getElementById('label_order_current').value = tox;
        for (x = 0; x < l_id_array.length; x++) {
          if (l_id_removed[l_id_array[x]]) {
            tox = tox + l_id_array[x] + '#**id**#' + l_label_array[x] + '#**label**#' + l_type_array[x] + '#****#';
          }
        }
        document.getElementById('label_order').value = tox;
        refresh_old();
        document.getElementById('pagination').value=document.getElementById('pages').getAttribute("type");
        document.getElementById('show_title').value=document.getElementById('pages').getAttribute("show_title");
        document.getElementById('show_numbers').value=document.getElementById('pages').getAttribute("show_numbers");
        return true;
      }

      gen = <?php echo (($id != 0) ? $row->counter : 1); ?>;

      function enable() {
        alltypes = Array('customHTML', 'text', 'checkbox', 'radio', 'time_and_date', 'select', 'file_upload', 'captcha', 'map', 'button', 'page_break', 'section_break', 'paypal', 'survey');
        for (x = 0; x < 14; x++) {
          document.getElementById('img_' + alltypes[x]).src = "<?php echo WD_FMC_URL . '/images/'; ?>" + alltypes[x] + ".png?ver=<?php echo get_option("wd_form_maker_version"); ?>";
        }
        if (document.getElementById('formMakerDiv').style.display == 'block') {
          jQuery('#formMakerDiv').slideToggle(200);
        }
        else {
          jQuery('#formMakerDiv').slideToggle(400);
        }
        
        if (document.getElementById('formMakerDiv1').style.display == 'block') {
          jQuery('#formMakerDiv1').slideToggle(200);
        }
        else {
          jQuery('#formMakerDiv1').slideToggle(400);
        }
        document.getElementById('when_edit').style.display = 'none';
      }

      function enable2() {
        alltypes = Array('customHTML', 'text', 'checkbox', 'radio', 'time_and_date', 'select', 'file_upload', 'captcha', 'map', 'button', 'page_break', 'section_break', 'paypal', 'survey');
        for (x = 0; x < 14; x++) {
          document.getElementById('img_' + alltypes[x]).src = "<?php echo WD_FMC_URL . '/images/'; ?>" + alltypes[x] + ".png?ver=<?php echo get_option("wd_form_maker_version"); ?>";
        }
        if (document.getElementById('formMakerDiv').style.display == 'block') {
          jQuery('#formMakerDiv').slideToggle(200);
        }
        else {
          jQuery('#formMakerDiv').slideToggle(400);
        }
        
        if (document.getElementById('formMakerDiv1').style.display == 'block') {
          jQuery('#formMakerDiv1').slideToggle(200);
        }
        else {
          jQuery('#formMakerDiv1').slideToggle(400);
        }
        document.getElementById('when_edit').style.display = 'block';
        if (document.getElementById('field_types').offsetWidth) {
          document.getElementById('when_edit').style.width = document.getElementById('field_types').offsetWidth + 'px';
        }
        if (document.getElementById('field_types').offsetHeight) {
          document.getElementById('when_edit').style.height = document.getElementById('field_types').offsetHeight + 'px';
        }
      }
    </script>
<div style="font-size: 14px; font-weight: bold;">
        This section allows you to add fields to your form.
        <a style="color: blue; text-decoration: none;" target="_blank" href="https://web-dorado.com/wordpress-form-maker-guide-4.html">Read More in User Manual</a>
      </div>
    <form class="wrap" id="manage_form" method="post" action="admin.php?page=manage_fmc" style="width:99%;">
      <?php wp_nonce_field('nonce_fmc', 'nonce_fmc'); ?>
	  <div class="fm-page-header">
			<!-- <div class="fm-page-title">
				<?php echo $page_title; ?>
			</div> -->
			<div style="float:left;">
				<div class="fm-logo-edit-page"></div>
				<div class="fm-title-edit-page">Form</br>Maker</div>
			</div>
			<div class="fm-page-actions">
				<button class="fm-button form-options-button medium" onclick="if (fm_check_required('title', 'Form title') || !submitbutton()) {return false;}; fm_set_input_value('task', 'form_options_old');">
					<span></span>
					Form Options
				</button>
				<div style="height:40px; border-right: 1px solid #848484; display: inline-block; width: 5px; vertical-align: bottom; margin-right: 5px;"></div>				
				<?php if ($id) { ?>
					<button class="fm-button save-as-copy-button medium" onclick="if (fm_check_required('title', 'Form title') || !submitbutton()) {return false;}; fm_set_input_value('task', 'save_as_copy_old');">
						<span></span>
						Save as Copy
					</button>
				<?php } ?>
				<button class="fm-button save-button small" onclick="if (fm_check_required('title', 'Form title') || !submitbutton()) {return false;}; fm_set_input_value('task', 'save_old');">
					<span></span>
					Save
				</button>
				<button class="fm-button apply-button small" onclick="if (fm_check_required('title', 'Form title') || !submitbutton()) {return false;}; fm_set_input_value('task', 'apply_old');">
					<span></span>
					Apply
				</button>
				<button class="fm-button cancel-button small" onclick="fm_set_input_value('task', 'cancel');">
					<span></span>
					Cancel
				</button>
			</div>
			<div class="fm-clear"></div>
		</div>
		<div class="fm-theme-banner">
			<div style="float:left;">
				<span style="">Form title:&nbsp;</span>
				<input id="title" name="title" value="<?php echo $row->title; ?>"/>
			</div>
			<div style="float:right;">
				<button class="fm-button add-new-button large" onclick="enable(); Enable(); return false;">
					Add a New Field
					<span></span>
				</button>
			</div>	
		</div>	
		<div class="fm-clear"></div>
		<div id="formMakerDiv" onclick="close_window()"></div>
		<div id="formMakerDiv1" style="padding-top: 20px;" align="center">
			<table border="0" width="100%" cellpadding="0" cellspacing="0" height="100%" class="formMakerDiv1_table">
				<tr>
					<td style="padding:0px">
					<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
                <tr valign="top">
                  <td width="15%" height="100%" style="border-right: dotted black 1px;" id="field_types">
                    <div id="when_edit" style="display: none;"></div>
                    <table border="0" cellpadding="0" cellspacing="3" width="100%">
                      <tr>
                        <td align="center" onClick="addRow('customHTML')" style="cursor:pointer" id="table_editor"  class="field_buttons">
                          <img src="<?php echo WD_FMC_URL . '/images/customHTML.png'; ?>" style="margin:5px" id="img_customHTML"/>
                        </td>
                        <td align="center" onClick="addRow('text')" style="cursor:pointer" id="table_text" class="field_buttons">
                          <img src="<?php echo WD_FMC_URL . '/images/text.png'; ?>" style="margin:5px" id="img_text"/>
                        </td>
                      </tr>
                      <tr>
                        <td align="center" onClick="addRow('time_and_date')" style="cursor:pointer" id="table_time_and_date" class="field_buttons">
                          <img src="<?php echo WD_FMC_URL . '/images/time_and_date.png'; ?>" style="margin:5px" id="img_time_and_date"/>
                        </td>
                        <td align="center" onClick="addRow('select')" style="cursor:pointer" id="table_select" class="field_buttons">
                          <img src="<?php echo WD_FMC_URL . '/images/select.png'; ?>" style="margin:5px" id="img_select"/>
                        </td>
                      </tr>
                      <tr>
                        <td align="center" onClick="addRow('checkbox')" style="cursor:pointer" id="table_checkbox" class="field_buttons">
                          <img src="<?php echo WD_FMC_URL . '/images/checkbox.png'; ?>" style="margin:5px" id="img_checkbox"/>
                        </td>
                        <td align="center" onClick="addRow('radio')" style="cursor:pointer" id="table_radio" class="field_buttons">
                          <img src="<?php echo WD_FMC_URL . '/images/radio.png'; ?>" style="margin:5px" id="img_radio"/>
                        </td>
                      </tr>
                      <tr>
                        <td align="center" onClick="addRow('file_upload')" style="cursor:pointer" id="table_file_upload" class="field_buttons">
                          <img src="<?php echo WD_FMC_URL . '/images/file_upload.png'; ?>" style="margin:5px" id="img_file_upload"/>
                        </td>
                        <td align="center" onClick="addRow('captcha')" style="cursor:pointer" id="table_captcha" class="field_buttons">
                          <img src="<?php echo WD_FMC_URL . '/images/captcha.png'; ?>" style="margin:5px" id="img_captcha"/>
                        </td>
                      </tr>
                      <tr>
                        <td align="center" onClick="addRow('page_break')" style="cursor:pointer" id="table_page_break" class="field_buttons">
                          <img src="<?php echo WD_FMC_URL . '/images/page_break.png'; ?>" style="margin:5px" id="img_page_break"/>
                        </td>
                        <td align="center" onClick="addRow('section_break')" style="cursor:pointer" id="table_section_break" class="field_buttons">
                          <img src="<?php echo WD_FMC_URL . '/images/section_break.png'; ?>" style="margin:5px" id="img_section_break"/>
                        </td>
                      </tr>
                      <tr>
                        <td align="center" onClick="addRow('map')" style="cursor:pointer" id="table_map" class="field_buttons">
                          <img src="<?php echo WD_FMC_URL . '/images/map.png'; ?>" style="margin:5px" id="img_map"/>
                        </td>
                        <td align="center" onClick="addRow('paypal')" id="table_paypal" class="field_buttons">
                          <img src="<?php echo WD_FMC_URL . '/images/paypal.png'; ?>" style="margin:5px" id="img_paypal"/>
                        </td>
                      </tr>
                      <tr>
                        <td align="center" onClick="addRow('survey')" class="field_buttons" id="table_survey">
                          <img src="<?php echo WD_FMC_URL . '/images/survey.png'; ?>" style="margin:5px" id="img_survey"/>
                        </td>
                        <td align="center" onClick="addRow('button')" id="table_button" class="field_buttons">
                          <img src="<?php echo WD_FMC_URL . '/images/button.png'; ?>" style="margin:5px" id="img_button"/>
                        </td>
                    </tr>
                    </table>
                  </td>
                  <td width="35%" height="100%" align="left">
                    <div id="edit_table" style="padding:0px; overflow-y:scroll; height:575px"></div>
                  </td>
                  <td align="center" valign="top" style="background: url("<?php echo WD_FMC_URL . '/images/border2.png'; ?>") repeat-y;">&nbsp;</td>
                  <td style="padding:15px">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
                      <tr>
                        <td align="right">
                          <input type="radio" value="end" name="el_pos" checked="checked" id="pos_end" onclick="Disable()"/>
                          At The End
                          <input type="radio" value="begin" name="el_pos" id="pos_begin" onclick="Disable()"/>
                          At The Beginning
                          <input type="radio" value="before" name="el_pos" id="pos_before" onclick="Enable()"/>
                          Before
                          <select style="width: 100px; margin-left: 5px;" id="sel_el_pos" disabled="disabled"></select>
						  <button class="fm-button field-save-button small" onclick="add(0, false); return false;">
							Save
							<span></span>
						</button>
						<button class="fm-button cancel-button small" onclick="close_window(); return false;">
							Cancel
							<span></span>
						</button>
                          <hr style=" margin-bottom:10px" />
                        </td>
                      </tr>
                      <tr height="100%" valign="top">
                        <td id="show_table"></td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
        <input type="hidden" id="old" />
        <input type="hidden" id="old_selected" />
        <input type="hidden" id="element_type" />
        <input type="hidden" id="editing_id" />
        <input type="hidden" value="<?php echo WD_FMC_URL; ?>" id="form_plugins_url" />
        <div id="main_editor" style="position: fixed; display: none; z-index: 140;">
          <?php
          if (user_can_richedit()) {
            wp_editor('', 'form_maker_editor', array('teeny' => FALSE, 'textarea_name' => 'form_maker_editor', 'media_buttons' => FALSE, 'textarea_rows' => 5));
          }
          else {
            ?>
            <textarea cols="36" rows="5" id="form_maker_editor" name="form_maker_editor" style="width: 440px; height: 350px; resize: vertical;" class="mce_editable" aria-hidden="true"></textarea>
            <?php
          }
          ?>
        </div>
      </div>
      <?php
      if (!function_exists('the_editor')) {
        ?>
        <iframe id="tinymce" style="display: none;"></iframe>
        <?php
      }
      ?>
      <br />      
      <br />
      <fieldset>
        <legend><h2 style="color: #00aeef;">Form</h2></legend>
        <table width="100%" style="margin:8px">
          <tr id="page_navigation">
            <td align="center" width="90%" id="pages" show_title="<?php echo $row->show_title; ?>" show_numbers="<?php echo $row->show_numbers; ?>" type="<?php echo $row->pagination; ?>"></td>
            <td align="left" id="edit_page_navigation"></td>
          </tr>
        </table>
        <div id="take">
          <?php
          if ($row->form) {
            echo $row->form;
          }
          else {
            ?>
            <table border="0" cellpadding="4" cellspacing="0" class="wdform_table1" style="width: 100%;">
              <tbody id="form_id_tempform_view1" class="wdform_tbody1" page_title="Untitled page" next_title="Next" next_type="button" next_class="wdform_page_button" next_checkable="false" previous_title="Previous" previous_type="button" previous_class="wdform_page_button" previous_checkable="false">
                <tr class="wdform_tr1">
                  <td class="wdform_td1" >
                    <table class="wdform_table2">
                      <tbody class="wdform_tbody2"></tbody>
                    </table>
                  </td>
                </tr>
                <tr class="wdform_footer">
					<td colspan="100" valign="top">
						<table width="100%" style="padding-right:170px">
							<tbody>
								<tr id="form_id_temppage_nav1">
								</tr>
							</tbody>
						</table>
					</td>
                </tr>
                <tbody id="form_id_tempform_view_img1" style="float: right !important;" >
                  <tr>
                    <td width="0%"></td>
                    <td align="right">
                      <img src="<?php echo WD_FMC_URL . '/images/minus.png?ver='. get_option("wd_form_maker_version").''; ?>" title="Show or hide the page" class="page_toolbar" onclick="show_or_hide('1')" onmouseover="chnage_icons_src(this,'minus')" onmouseout="chnage_icons_src(this,'minus')" id="show_page_img_1" />
                    </td>
                    <td>
                      <img src="<?php echo WD_FMC_URL . '/images/page_delete.png?ver='. get_option("wd_form_maker_version").''; ?>" title="Delete the page" class="page_toolbar" onclick="remove_page('1')" onmouseover="chnage_icons_src(this,'page_delete')" onmouseout="chnage_icons_src(this,'page_delete')" />
                    </td>
                    <td>
                      <img src="<?php echo WD_FMC_URL . '/images/page_delete_all.png?ver='. get_option("wd_form_maker_version").''; ?>" title="Delete the page with fields" class="page_toolbar" onclick="remove_page_all('1')" onmouseover="chnage_icons_src(this,'page_delete_all')" onmouseout="chnage_icons_src(this,'page_delete_all')" />
                    </td>
                    <td>
                      <img src="<?php echo WD_FMC_URL . '/images/page_edit.png?ver='. get_option("wd_form_maker_version").''; ?>" title="Edit the page" class="page_toolbar" onclick="edit_page_break('1')" onmouseover="chnage_icons_src(this,'page_edit')" onmouseout="chnage_icons_src(this,'page_edit')" />
                    </td>
                  </tr>
              </tbody>
            </table>
            <?php
          }
          ?>
        </div>
      </fieldset>
      <input type="hidden" name="form" id="form" />
      <input type="hidden" name="form_front" id="form_front" />
      <input type="hidden" name="pagination" id="pagination" />
      <input type="hidden" name="show_title" id="show_title" />
      <input type="hidden" name="show_numbers" id="show_numbers" />
      <input type="hidden" name="public_key" id="public_key" />
      <input type="hidden" name="private_key" id="private_key" />
      <input type="hidden" name="recaptcha_theme" id="recaptcha_theme" />
      <input type="hidden" id="label_order" name="label_order" value="<?php echo $row->label_order; ?>" />
      <input type="hidden" id="label_order_current" name="label_order_current" value="<?php echo $row->label_order_current; ?>" />  
      <input type="hidden" name="counter" id="counter" value="<?php echo $row->counter; ?>" />
      <input type="hidden" id="araqel" value="0" />
      <script type="text/javascript">
        form_view = 1;
        form_view_count = 1;
        form_view_max = 1;
        function formOnload() {
          // Enable maps.
          for (t = 0; t < <?php echo $row->counter;?>; t++)
            if (document.getElementById(t+"_typeform_id_temp")) {
              if (document.getElementById(t+"_typeform_id_temp").value=="type_map" || document.getElementById(t+"_typeform_id_temp").value=="type_mark_map") {
                if_gmap_init(t);
                for (q = 0; q < 20; q++) {
                  if (document.getElementById(t+"_elementform_id_temp").getAttribute("long"+q)) {
                    w_long=parseFloat(document.getElementById(t+"_elementform_id_temp").getAttribute("long"+q));
                    w_lat=parseFloat(document.getElementById(t+"_elementform_id_temp").getAttribute("lat"+q));
                    w_info=parseFloat(document.getElementById(t+"_elementform_id_temp").getAttribute("info"+q));
                    add_marker_on_map(t,q, w_long, w_lat, w_info, false);
                  }
                }
              }
              else
                if (document.getElementById(t+"_typeform_id_temp").value == "type_date") {
                  // Calendar.setup({
                      // inputField: t+"_elementform_id_temp",
                      // ifFormat: document.getElementById(t+"_buttonform_id_temp").getAttribute('format'),
                      // button: t+"_buttonform_id_temp",
                      // align: "Tl",
                      // singleClick: true,
                      // firstDay: 0
                      // });
                }
               else				
        if(document.getElementById(t+"_typeform_id_temp").value=="type_spinner")	{
            var spinner_value = jQuery("#" + t + "_elementform_id_temp").get( "aria-valuenow" );
            var spinner_min_value = document.getElementById(t+"_min_valueform_id_temp").value;
            var spinner_max_value = document.getElementById(t+"_max_valueform_id_temp").value;
              var spinner_step = document.getElementById(t+"_stepform_id_temp").value;
                
               jQuery( "#"+t+"_elementform_id_temp" ).removeClass( "ui-spinner-input" )
          .prop( "disabled", false )
          .removeAttr( "autocomplete" )
          .removeAttr( "role" )
          .removeAttr( "aria-valuemin" )
          .removeAttr( "aria-valuemax" )
          .removeAttr( "aria-valuenow" );
          
          span_ui= document.getElementById(t+"_elementform_id_temp").parentNode;
            span_ui.parentNode.appendChild(document.getElementById(t+"_elementform_id_temp"));
            span_ui.parentNode.removeChild(span_ui);
            
            jQuery("#"+t+"_elementform_id_temp")[0].spin = null;
            
            spinner = jQuery( "#"+t+"_elementform_id_temp" ).spinner();
            spinner.spinner( "value", spinner_value );
            jQuery( "#"+t+"_elementform_id_temp" ).spinner({ min: spinner_min_value});    
                    jQuery( "#"+t+"_elementform_id_temp" ).spinner({ max: spinner_max_value});
                    jQuery( "#"+t+"_elementform_id_temp" ).spinner({ step: spinner_step});
            
        }
            else
          if(document.getElementById(t+"_typeform_id_temp").value=="type_slider")	{
     
            var slider_value = document.getElementById(t+"_slider_valueform_id_temp").value;
            var slider_min_value = document.getElementById(t+"_slider_min_valueform_id_temp").value;
            var slider_max_value = document.getElementById(t+"_slider_max_valueform_id_temp").value;
            
            var slider_element_value = document.getElementById( t+"_element_valueform_id_temp" );
            var slider_value_save = document.getElementById( t+"_slider_valueform_id_temp" );
            
            document.getElementById(t+"_elementform_id_temp").innerHTML = "";
            document.getElementById(t+"_elementform_id_temp").removeAttribute( "class" );
            document.getElementById(t+"_elementform_id_temp").removeAttribute( "aria-disabled" );

             jQuery("#"+t+"_elementform_id_temp")[0].slide = null;	
          
              jQuery(function() {
            jQuery( "#"+t+"_elementform_id_temp").slider({
            range: "min",
            value: eval(slider_value),
            min: eval(slider_min_value),
            max: eval(slider_max_value),
            slide: function( event, ui ) {	
              slider_element_value.innerHTML = "" + ui.value ;
              slider_value_save.value = "" + ui.value; 

        }
      });
      
      
    });	
        
            
        }
        else
           if(document.getElementById(t+"_typeform_id_temp").value=="type_range"){
                    var spinner_value0 = jQuery("#" + t+"_elementform_id_temp0").get( "aria-valuenow" );
              var spinner_step = document.getElementById(t+"_range_stepform_id_temp").value;
                
               jQuery( "#"+t+"_elementform_id_temp0" ).removeClass( "ui-spinner-input" )
          .prop( "disabled", false )
          .removeAttr( "autocomplete" )
          .removeAttr( "role" )
          .removeAttr( "aria-valuenow" );
          
          span_ui= document.getElementById(t+"_elementform_id_temp0").parentNode;
            span_ui.parentNode.appendChild(document.getElementById(t+"_elementform_id_temp0"));
            span_ui.parentNode.removeChild(span_ui);
            
            
            jQuery("#"+t+"_elementform_id_temp0")[0].spin = null;
            jQuery("#"+t+"_elementform_id_temp1")[0].spin = null;
            
            spinner0 = jQuery( "#"+t+"_elementform_id_temp0" ).spinner();
            spinner0.spinner( "value", spinner_value0 );
                    jQuery( "#"+t+"_elementform_id_temp0" ).spinner({ step: spinner_step});
            
            
            
            var spinner_value1 = jQuery("#" + t+"_elementform_id_temp1").get( "aria-valuenow" );
                        
               jQuery( "#"+t+"_elementform_id_temp1" ).removeClass( "ui-spinner-input" )
          .prop( "disabled", false )
          .removeAttr( "autocomplete" )
          .removeAttr( "role" )
          .removeAttr( "aria-valuenow" );
          
          span_ui1= document.getElementById(t+"_elementform_id_temp1").parentNode;
            span_ui1.parentNode.appendChild(document.getElementById(t+"_elementform_id_temp1"));
            span_ui1.parentNode.removeChild(span_ui1);
              
            spinner1 = jQuery( "#"+t+"_elementform_id_temp1" ).spinner();
            spinner1.spinner( "value", spinner_value1 );
                    jQuery( "#"+t+"_elementform_id_temp1" ).spinner({ step: spinner_step});
            
              var myu = t;
            jQuery(document).ready(function() {	

        jQuery("#"+myu+"_mini_label_from").click(function() {
        if (jQuery(this).children('input').length == 0) {
          var from = "<input type='text' id='from' class='from' size='6' style='outline:none; border:none; background:none; font-size:11px;' value=\""+jQuery(this).text()+"\">";
            jQuery(this).html(from);
            jQuery("input.from").focus();
            jQuery("input.from").blur(function() {
          var id_for_blur = document.getElementById('from').parentNode.id.split('_');
          var value = jQuery(this).val();
        jQuery("#"+id_for_blur[0]+"_mini_label_from").text(value);
        });
      }
      });
          
        jQuery("label#"+myu+"_mini_label_to").click(function() {
      if (jQuery(this).children('input').length == 0) {	
      
        var to = "<input type='text' id='to' class='to' size='6' style='outline:none; border:none; background:none; font-size:11px;' value=\""+jQuery(this).text()+"\">";	
          jQuery(this).html(to);			
          jQuery("input.to").focus();					
          jQuery("input.to").blur(function() {	
          var id_for_blur = document.getElementById('to').parentNode.id.split('_');			
          var value = jQuery(this).val();			
          
          jQuery("#"+id_for_blur[0]+"_mini_label_to").text(value);
        });	
         
      }	
      });
      
      
      
      });	
          }	

        else
           if(document.getElementById(t+"_typeform_id_temp").value=="type_name"){
        var myu = t;
            jQuery(document).ready(function() {	

        jQuery("#"+myu+"_mini_label_first").click(function() {		
      
        if (jQuery(this).children('input').length == 0) {	

          var first = "<input type='text' id='first' class='first' style='outline:none; border:none; background:none;' value=\""+jQuery(this).text()+"\">";	
            jQuery(this).html(first);							
            jQuery("input.first").focus();			
            jQuery("input.first").blur(function() {	
          
          var id_for_blur = document.getElementById('first').parentNode.id.split('_');
          var value = jQuery(this).val();			
        jQuery("#"+id_for_blur[0]+"_mini_label_first").text(value);		
        });	
      }	
      });	    
          
        jQuery("label#"+myu+"_mini_label_last").click(function() {	
      if (jQuery(this).children('input').length == 0) {	
      
        var last = "<input type='text' id='last' class='last'  style='outline:none; border:none; background:none;' value=\""+jQuery(this).text()+"\">";	
          jQuery(this).html(last);			
          jQuery("input.last").focus();					
          jQuery("input.last").blur(function() {	
          var id_for_blur = document.getElementById('last').parentNode.id.split('_');			
          var value = jQuery(this).val();			
          
          jQuery("#"+id_for_blur[0]+"_mini_label_last").text(value);	
        });	
         
      }	
      });
      
        jQuery("label#"+myu+"_mini_label_title").click(function() {		
        if (jQuery(this).children('input').length == 0) {				
          var title = "<input type='text' id='title' class='title' size='10' style='outline:none; border:none; background:none;' value=\""+jQuery(this).text()+"\">";	
            jQuery(this).html(title);							
            jQuery("input.title").focus();			
            jQuery("input.title").blur(function() {	
            var id_for_blur = document.getElementById('title').parentNode.id.split('_');
          var value = jQuery(this).val();			


        jQuery("#"+id_for_blur[0]+"_mini_label_title").text(value);		
        });	
      }	
      
      });		


      jQuery("label#"+myu+"_mini_label_middle").click(function() {	
      if (jQuery(this).children('input').length == 0) {		
        var middle = "<input type='text' id='middle' class='middle'  style='outline:none; border:none; background:none;' value=\""+jQuery(this).text()+"\">";	
          jQuery(this).html(middle);			
          jQuery("input.middle").focus();					
          jQuery("input.middle").blur(function() {	
                var id_for_blur = document.getElementById('middle').parentNode.id.split('_');			
          var value = jQuery(this).val();			
          
          jQuery("#"+id_for_blur[0]+"_mini_label_middle").text(value);	
        });	
      }	
      });
      
      });		
         }						
        else
           if(document.getElementById(t+"_typeform_id_temp").value=="type_address"){
        var myu = t;
           
      jQuery(document).ready(function() {		
      jQuery("label#"+myu+"_mini_label_street1").click(function() {			

        if (jQuery(this).children('input').length == 0) {				
        var street1 = "<input type='text' id='street1' class='street1' style='outline:none; border:none; background:none;' value=\""+jQuery(this).text()+"\">";
        jQuery(this).html(street1);					
        jQuery("input.street1").focus();		
        jQuery("input.street1").blur(function() {	
        var id_for_blur = document.getElementById('street1').parentNode.id.split('_');
        var value = jQuery(this).val();			
        jQuery("#"+id_for_blur[0]+"_mini_label_street1").text(value);		
        });		
        }	
        });		
      
      jQuery("label#"+myu+"_mini_label_street2").click(function() {		
      if (jQuery(this).children('input').length == 0) {		
      var street2 = "<input type='text' id='street2' class='street2'  style='outline:none; border:none; background:none;' value=\""+jQuery(this).text()+"\">";
      jQuery(this).html(street2);					
      jQuery("input.street2").focus();		
      jQuery("input.street2").blur(function() {	
      var id_for_blur = document.getElementById('street2').parentNode.id.split('_');
      var value = jQuery(this).val();			
      jQuery("#"+id_for_blur[0]+"_mini_label_street2").text(value);		
      });		
      }	
      });	
      
      
      jQuery("label#"+myu+"_mini_label_city").click(function() {	
        if (jQuery(this).children('input').length == 0) {	
        var city = "<input type='text' id='city' class='city'  style='outline:none; border:none; background:none;' value=\""+jQuery(this).text()+"\">";
        jQuery(this).html(city);			
        jQuery("input.city").focus();				
        jQuery("input.city").blur(function() {	
        var id_for_blur = document.getElementById('city').parentNode.id.split('_');		
        var value = jQuery(this).val();		
        jQuery("#"+id_for_blur[0]+"_mini_label_city").text(value);		
      });		
      }	
      });	
      
      jQuery("label#"+myu+"_mini_label_state").click(function() {		
        if (jQuery(this).children('input').length == 0) {	
        var state = "<input type='text' id='state' class='state'  style='outline:none; border:none; background:none;' value=\""+jQuery(this).text()+"\">";	
          jQuery(this).html(state);		
          jQuery("input.state").focus();		
          jQuery("input.state").blur(function() {	
        var id_for_blur = document.getElementById('state').parentNode.id.split('_');					
        var value = jQuery(this).val();			
      jQuery("#"+id_for_blur[0]+"_mini_label_state").text(value);	
      });	
      }
      });		

      jQuery("label#"+myu+"_mini_label_postal").click(function() {		
      if (jQuery(this).children('input').length == 0) {			
      var postal = "<input type='text' id='postal' class='postal'  style='outline:none; border:none; background:none;' value=\""+jQuery(this).text()+"\">";
      jQuery(this).html(postal);			
      jQuery("input.postal").focus();			
      jQuery("input.postal").blur(function() {
        var id_for_blur = document.getElementById('postal').parentNode.id.split('_');	
      var value = jQuery(this).val();		
      jQuery("#"+id_for_blur[0]+"_mini_label_postal").text(value);		
      });	
      }
      });	
      
      
      jQuery("label#"+myu+"_mini_label_country").click(function() {		
        if (jQuery(this).children('input').length == 0) {		
          var country = "<input type='country' id='country' class='country'  style='outline:none; border:none; background:none;' value=\""+jQuery(this).text()+"\">";
          jQuery(this).html(country);		
          jQuery("input.country").focus();	
          jQuery("input.country").blur(function() {	
          var id_for_blur = document.getElementById('country').parentNode.id.split('_');				
          var value = jQuery(this).val();			
          jQuery("#"+id_for_blur[0]+"_mini_label_country").text(value);			
          });	
        }	
      });
      });	

         }						
        else
           if(document.getElementById(t+"_typeform_id_temp").value=="type_phone"){
        var myu = t;
          
      jQuery(document).ready(function() {	
      jQuery("label#"+myu+"_mini_label_area_code").click(function() {		
      if (jQuery(this).children('input').length == 0) {		

        var area_code = "<input type='text' id='area_code' class='area_code' size='10' style='outline:none; border:none; background:none;' value=\""+jQuery(this).text()+"\">";		

        jQuery(this).html(area_code);		
        jQuery("input.area_code").focus();		
        jQuery("input.area_code").blur(function() {	
        var id_for_blur = document.getElementById('area_code').parentNode.id.split('_');
        var value = jQuery(this).val();			
        jQuery("#"+id_for_blur[0]+"_mini_label_area_code").text(value);		
        });		
      }	
      });	

      
      jQuery("label#"+myu+"_mini_label_phone_number").click(function() {		

      if (jQuery(this).children('input').length == 0) {			
        var phone_number = "<input type='text' id='phone_number' class='phone_number'  style='outline:none; border:none; background:none;' value=\""+jQuery(this).text()+"\">";						

        jQuery(this).html(phone_number);					

        jQuery("input.phone_number").focus();			
        jQuery("input.phone_number").blur(function() {		
        var id_for_blur = document.getElementById('phone_number').parentNode.id.split('_');
        var value = jQuery(this).val();			
        jQuery("#"+id_for_blur[0]+"_mini_label_phone_number").text(value);		
        });	
      }	
      });
      
      });	
         }						
        else
           if(document.getElementById(t+"_typeform_id_temp").value=="type_date_fields"){
        var myu = t;
          
      jQuery(document).ready(function() {
      jQuery("label#"+myu+"_day_label").click(function() {		
        if (jQuery(this).children('input').length == 0) {				
          var day = "<input type='text' id='day' class='day' size='8' style='outline:none; border:none; background:none;' value=\""+jQuery(this).text()+"\">";	
            jQuery(this).html(day);							
            jQuery("input.day").focus();			
            jQuery("input.day").blur(function() {	
          var id_for_blur = document.getElementById('day').parentNode.id.split('_');
          var value = jQuery(this).val();			

        jQuery("#"+id_for_blur[0]+"_day_label").text(value);		
        });	
      }	
      });		


      jQuery("label#"+myu+"_month_label").click(function() {	
      if (jQuery(this).children('input').length == 0) {		
        var month = "<input type='text' id='month' class='month' size='8' style='outline:none; border:none; background:none;' value=\""+jQuery(this).text()+"\">";	
          jQuery(this).html(month);			
          jQuery("input.month").focus();					
          jQuery("input.month").blur(function() {	
          var id_for_blur = document.getElementById('month').parentNode.id.split('_');			
          var value = jQuery(this).val();			
          
          jQuery("#"+id_for_blur[0]+"_month_label").text(value);	
        });	
      }	
      });
      
        jQuery("label#"+myu+"_year_label").click(function() {	
      if (jQuery(this).children('input').length == 0) {		
        var year = "<input type='text' id='year' class='year' size='8' style='outline:none; border:none; background:none;' value=\""+jQuery(this).text()+"\">";	
          jQuery(this).html(year);			
          jQuery("input.year").focus();					
          jQuery("input.year").blur(function() {	
        var id_for_blur = document.getElementById('year').parentNode.id.split('_');				
          var value = jQuery(this).val();			
          
          jQuery("#"+id_for_blur[0]+"_year_label").text(value);	
        });	
      }	
      });
      
      });	

      
         }						
          else
           if(document.getElementById(t+"_typeform_id_temp").value=="type_time"){
        var myu = t;
          
    jQuery(document).ready(function() {	
      jQuery("label#"+myu+"_mini_label_hh").click(function() {		
        if (jQuery(this).children('input').length == 0) {				
          var hh = "<input type='text' id='hh' class='hh' size='4' style='outline:none; border:none; background:none;' value=\""+jQuery(this).text()+"\">";	
            jQuery(this).html(hh);							
            jQuery("input.hh").focus();			
            jQuery("input.hh").blur(function() {	
            var id_for_blur = document.getElementById('hh').parentNode.id.split('_');	
          var value = jQuery(this).val();			


        jQuery("#"+id_for_blur[0]+"_mini_label_hh").text(value);		
        });	
      }	
      });		


      jQuery("label#"+myu+"_mini_label_mm").click(function() {	
      if (jQuery(this).children('input').length == 0) {		
        var mm = "<input type='text' id='mm' class='mm' size='4' style='outline:none; border:none; background:none;' value=\""+jQuery(this).text()+"\">";	
          jQuery(this).html(mm);			
          jQuery("input.mm").focus();					
          jQuery("input.mm").blur(function() {
                var id_for_blur = document.getElementById('mm').parentNode.id.split('_');				
          var value = jQuery(this).val();			
          
          jQuery("#"+id_for_blur[0]+"_mini_label_mm").text(value);	
        });	
      }	
      });
      
        jQuery("label#"+myu+"_mini_label_ss").click(function() {	
      if (jQuery(this).children('input').length == 0) {		
        var ss = "<input type='text' id='ss' class='ss' size='4' style='outline:none; border:none; background:none;' value=\""+jQuery(this).text()+"\">";	
          jQuery(this).html(ss);			
          jQuery("input.ss").focus();					
          jQuery("input.ss").blur(function() {
       var id_for_blur = document.getElementById('ss').parentNode.id.split('_');				
          var value = jQuery(this).val();			
          
          jQuery("#"+id_for_blur[0]+"_mini_label_ss").text(value);	
        });	
      }	
      });
      
        jQuery("label#"+myu+"_mini_label_am_pm").click(function() {		
        if (jQuery(this).children('input').length == 0) {				
          var am_pm = "<input type='text' id='am_pm' class='am_pm' size='4' style='outline:none; border:none; background:none;' value=\""+jQuery(this).text()+"\">";	
            jQuery(this).html(am_pm);							
            jQuery("input.am_pm").focus();			
            jQuery("input.am_pm").blur(function() {	
            var id_for_blur = document.getElementById('am_pm').parentNode.id.split('_');	
          var value = jQuery(this).val();			

        jQuery("#"+id_for_blur[0]+"_mini_label_am_pm").text(value);		
        });	
      }	
      });	
      });
        
         }	

        else
           if(document.getElementById(t+"_typeform_id_temp").value=="type_paypal_price"){
        var myu = t;
            jQuery(document).ready(function() {	

        jQuery("#"+myu+"_mini_label_dollars").click(function() {		
      
        if (jQuery(this).children('input').length == 0) {	

          var dollars = "<input type='text' id='dollars' class='dollars' style='outline:none; border:none; background:none;' value=\""+jQuery(this).text()+"\">";	
            jQuery(this).html(dollars);							
            jQuery("input.dollars").focus();			
            jQuery("input.dollars").blur(function() {	
          
          var id_for_blur = document.getElementById('dollars').parentNode.id.split('_');
          var value = jQuery(this).val();			
        jQuery("#"+id_for_blur[0]+"_mini_label_dollars").text(value);		
        });	
      }	
      });	    
          
        jQuery("label#"+myu+"_mini_label_cents").click(function() {	
      if (jQuery(this).children('input').length == 0) {	
      
        var cents = "<input type='text' id='cents' class='cents'  style='outline:none; border:none; background:none;' value=\""+jQuery(this).text()+"\">";	
          jQuery(this).html(cents);			
          jQuery("input.cents").focus();					
          jQuery("input.cents").blur(function() {	
          var id_for_blur = document.getElementById('cents').parentNode.id.split('_');			
          var value = jQuery(this).val();			
          
          jQuery("#"+id_for_blur[0]+"_mini_label_cents").text(value);	
        });	
         
      }	
      });
      });
      }
      else
           if(document.getElementById(t+"_typeform_id_temp").value=="type_scale_rating"){
        var myu = t;
            jQuery(document).ready(function() {	

        jQuery("#"+myu+"_mini_label_worst").click(function() {		
      
        if (jQuery(this).children('input').length == 0) {	

          var worst = "<input type='text' id='worst' class='worst' size='6' style='outline:none; border:none; background:none; font-size:11px;' value=\""+jQuery(this).text()+"\">";	
            jQuery(this).html(worst);							
            jQuery("input.worst").focus();			
            jQuery("input.worst").blur(function() {	
          
          var id_for_blur = document.getElementById('worst').parentNode.id.split('_');
          var value = jQuery(this).val();			
        jQuery("#"+id_for_blur[0]+"_mini_label_worst").text(value);		
        });	
      }	
      });	    
          
        jQuery("label#"+myu+"_mini_label_best").click(function() {	
      if (jQuery(this).children('input').length == 0) {	
      
        var best = "<input type='text' id='best' class='best' size='6' style='outline:none; border:none; background:none; font-size:11px;' value=\""+jQuery(this).text()+"\">";	
          jQuery(this).html(best);			
          jQuery("input.best").focus();					
          jQuery("input.best").blur(function() {	
          var id_for_blur = document.getElementById('best').parentNode.id.split('_');			
          var value = jQuery(this).val();			
          
          jQuery("#"+id_for_blur[0]+"_mini_label_best").text(value);	
        });	
         
      }	
      });
      
      
      
      });		
       }
          }
          form_view = 1;
          form_view_count = 0;
          for (i = 1; i <= 30; i++) {
            if (document.getElementById('form_id_tempform_view'+i)) {
              form_view_count++;
              form_view_max=i;
            }
          }
          if (form_view_count > 1) {
            for (i=1; i<=form_view_max; i++) {
              if (document.getElementById('form_id_tempform_view'+i)) {
                first_form_view=i;
                break;
              }
            }
            form_view=form_view_max;
            generate_page_nav(first_form_view);
            var img_EDIT = document.createElement("img");
            img_EDIT.setAttribute("src", "<?php echo WD_FMC_URL . '/images/edit.png'; ?>");
            img_EDIT.style.cssText = "margin-left:40px; cursor:pointer";
            img_EDIT.setAttribute("onclick", 'el_page_navigation()');
            var td_EDIT = document.getElementById("edit_page_navigation");
            td_EDIT.appendChild(img_EDIT);
            document.getElementById('page_navigation').appendChild(td_EDIT);
          }
          //if(document.getElementById('take').innerHTML.indexOf('up_row(')==-1) location.reload(true);
          //else 
          
          document.getElementById('form').value=document.getElementById('take').innerHTML;
          document.getElementById('araqel').value = 1;
        }
        jQuery(window).load(function () {
          formOnload();
        });
      </script>

      <input type="hidden" name="option" value="com_formmaker" />
      <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
      <input type="hidden" name="cid[]" value="<?php echo $row->id; ?>" />

      <input type="hidden" id="task" name="task" value=""/>
      <input type="hidden" id="current_id" name="current_id" value="<?php echo $row->id; ?>" />
    </form>
    <script>
      jQuery(window).load(function() {
        fm_popup();
      });
    </script>
    <?php
  }

  public function form_options_old($id) {
    $row = $this->model->get_row_data($id);
    $themes = $this->model->get_theme_rows_data('_old');
    $page_title = $row->title . ' form options';
    $label_id = array();
    $label_label = array();
    $label_type = array();
    $label_all = explode('#****#', $row->label_order_current);
    $label_all = array_slice($label_all, 0, count($label_all) - 1);
    foreach ($label_all as $key => $label_each) {
      $label_id_each = explode('#**id**#', $label_each);
      array_push($label_id, $label_id_each[0]);
      $label_order_each = explode('#**label**#', $label_id_each[1]);
      array_push($label_label, $label_order_each[0]);
      array_push($label_type, $label_order_each[1]);
    }
    ?>
    <script>
      gen = "<?php echo $row->counter; ?>";
      form_view_max = 20;
      function set_preview() {
        document.getElementById('preview_form').href = '<?php echo add_query_arg(array('action' => 'FormMakerPreview_fmc', 'form_id' => $row->id), admin_url('admin-ajax.php')); ?>&id='+document.getElementById('theme').value+'&width=1000&height=500&TB_iframe=1';
      }
    </script>
    <div style="font-size: 14px; font-weight: bold;">
        This section allows you to edit form options.
        <a style="color: blue; text-decoration: none;" target="_blank" href="https://web-dorado.com/wordpress-form-maker-guide-3.html">Read More in User Manual</a>
      </div>
    <form class="wrap" method="post" action="admin.php?page=manage_fmc" style="width:99%;" name="adminForm" id="adminForm">
      <?php wp_nonce_field('nonce_fmc', 'nonce_fmc'); ?>
      <h2><?php echo $page_title; ?></h2>
      <div style="float: right; margin: 0 5px 0 0;">
        <input class="button-secondary" type="submit" onclick="if (fm_check_email('mail') ||
                                                                   fm_check_email('from_mail') ||
                                                                   fm_check_email('paypal_email')) {return false;}; fm_set_input_value('task', 'save_options_old')" value="Save"/>
        <input class="button-secondary" type="submit" onclick="if (fm_check_email('mail') ||
                                                                   fm_check_email('from_mail') ||
                                                                   fm_check_email('paypal_email')) {return false;}; fm_set_input_value('task', 'apply_options_old')" value="Apply"/>
        <input class="button-secondary" type="submit" onclick="fm_set_input_value('task', 'cancel_options_old')" value="Cancel"/>
      </div>
      <input type="hidden" name="take" id="take" value="<?php $row->form ?>">
      <div class="submenu-box" style="width: 99%; float: left; margin: 15px 0 0 0;">
        <div class="submenu-pad">
          <ul id="submenu" class="configuration">
            <li>
              <a id="general" class="fm_fieldset_tab" onclick="form_maker_options_tabs('general')" href="#">General Options</a>
            </li>
            <li>
              <a id="actions" class="fm_fieldset_tab" onclick="form_maker_options_tabs('actions')" href="#">Actions after Submission</a>
            </li>
            <li>
              <a id="payment" class="fm_fieldset_tab" onclick="form_maker_options_tabs('payment')" href="#">Payment Options</a>
            </li>
            <li>
              <a id="javascript" class="fm_fieldset_tab" onclick="form_maker_options_tabs('javascript')" href="#">JavaScript</a>
            </li>
            <li>
              <a id="custom" class="fm_fieldset_tab" onclick="form_maker_options_tabs('custom')" href="#">Custom Text in Email</a>
            </li>
          </ul>
        </div>
      </div>
      <fieldset id="actions_fieldset" class="adminform fm_fieldset_deactive">
        <legend style="color:#0B55C4;font-weight: bold;">Actions after submission</legend>
        <table class="admintable">
          <tr valign="top">
            <td class="fm_options_label">
              <label>Action type</label>
            </td>
            <td class="fm_options_value">
              <div><input type="radio" name="submit_text_type" id="text_type_none" onclick="set_type('none')" value="1" <?php echo ($row->submit_text_type != 2 && $row->submit_text_type != 3 && $row->submit_text_type != 4 && $row->submit_text_type != 5) ? "checked" : ""; ?> /><label for="text_type_none">Stay on Form</label></div>
              <div><input type="radio" name="submit_text_type" id="text_type_post" onclick="set_type('post')" value="2" <?php echo ($row->submit_text_type == 2) ? "checked" : ""; ?> /><label for="text_type_post">Post</label></label></div>
              <div><input type="radio" name="submit_text_type" id="text_type_page" onclick="set_type('page')" value="5" <?php echo ($row->submit_text_type == 5) ? "checked" : ""; ?> /><label for="text_type_page">Page</label></label></div>
              <div><input type="radio" name="submit_text_type" id="text_type_custom_text" onclick="set_type('custom_text')" value="3" <?php echo ($row->submit_text_type == 3 ) ? "checked" : ""; ?> /><label for="text_type_custom_text">Custom Text</label></label></div>
              <div><input type="radio" name="submit_text_type" id="text_type_url" onclick="set_type('url')" value="4" <?php echo ($row->submit_text_type == 4) ? "checked" : ""; ?> /><label for="text_type_url">URL</div>
            </td>
          </tr>
			<tr id="none" <?php echo (($row->submit_text_type == 2 || $row->submit_text_type == 3 || $row->submit_text_type == 4 || $row->submit_text_type == 5) ? 'style="display:none"' : ''); ?>>
				<td class="fm_options_label">
				  <label>Stay on Form</label>
				</td>
				<td class="fm_options_value">
				  <img src="<?php echo WD_FMC_URL . '/images/tick.png'; ?>" border="0">
				</td>
			</tr>
			<tr id="post" <?php echo (($row->submit_text_type != 2) ? 'style="display:none"' : ''); ?>>
				<td class="fm_options_label">
					<label for="post_name">Post</label>
				</td>
				<td class="fm_options_value">
					<select id="post_name" name="post_name">
						<option value="0">- Select Post -</option>
						<?php
						// The Query.
						$args = array('posts_per_page'  => 10000);
						query_posts($args);
						// The Loop.
						while (have_posts()) : the_post(); ?>
						<option value="<?php $x = get_permalink(get_the_ID()); echo $x; ?>" <?php echo (($row->article_id == $x) ? 'selected="selected"' : ''); ?>><?php the_title(); ?></option>
						<?php
						endwhile;
						// Reset Query.
						wp_reset_query();
						?>
					</select>
				</td>
			</tr>
			<tr id="page" <?php echo (($row->submit_text_type != 5) ? 'style="display:none"' : ''); ?>>
				<td class="fm_options_label">
					<label for="page_name">Page</label>
				</td>
				<td class="fm_options_value">
					<select id="page_name" name="page_name" style="width:153px; font-size:11px;">
						<option value="0">- Select Page -</option>
						<?php
						// The Query.
						$pages = get_pages();
						// The Loop.
						foreach ($pages as $page) {
						  $page_id = get_page_link($page->ID);
						  ?>
						<option value="<?php echo $page_id; ?>" <?php echo (($row->article_id == $page_id) ? 'selected="selected"' : ''); ?>><?php echo $page->post_title; ?></option>
						  <?php
						}
						// Reset Query.
						wp_reset_query();
						?>
					</select>
				</td>
			</tr>
			<tr id="custom_text" <?php echo (($row->submit_text_type != 3) ? 'style="display: none;"' : ''); ?>>
				<td class="fm_options_label">
					<label for="submit_text">Text</label>
				</td>
				<td class="fm_options_value">
					<?php
					if (user_can_richedit()) {
						wp_editor($row->submit_text, 'submit_text', array('teeny' => FALSE, 'textarea_name' => 'submit_text', 'media_buttons' => FALSE, 'textarea_rows' => 5));
					}
					else {
						?>
						<textarea cols="36" rows="5" id="submit_text" name="submit_text" style="resize: vertical;">
							<?php echo $row->submit_text; ?>
						</textarea>
						<?php
					}
					?>
				</td>
			</tr>
			<tr id="url" <?php echo (($row->submit_text_type != 4 ) ? 'style="display:none"' : ''); ?>>
				<td class="fm_options_label">
					<label for="url">URL</label>
				</td>
				<td class="fm_options_value">
					<input type="text" id="url" name="url" style="width:300px" value="<?php echo $row->url; ?>" />
				</td>
			</tr>
        </table>
		</fieldset>
      <fieldset id="custom_fieldset" class="adminform fm_fieldset_deactive">
        <legend style="color:#0B55C4;font-weight: bold;">Custom text in email</legend>
        <table class="admintable">
          <tr>
            <td class="fm_options_label" valign="top">
              <label>For Administrator</label>
            </td>
            <td class="fm_options_value">
              <div style="margin-bottom:5px">
                <?php
                $choise = "document.getElementById('script_mail')";
                for ($i = 0; $i < count($label_label); $i++) {
                  if ($label_type[$i] == "type_submit_reset" || $label_type[$i] == "type_editor" || $label_type[$i] == "type_map" || $label_type[$i] == "type_mark_map" || $label_type[$i] == "type_captcha" || $label_type[$i] == "type_recaptcha" || $label_type[$i] == "type_button") {
                    continue;
                  }
                  $param = htmlspecialchars(addslashes($label_label[$i]));
                  ?>
                  <input style="border: 1px solid silver; font-size: 10px;" type="button" value="<?php echo htmlspecialchars(addslashes($label_label[$i])); ?>" onClick="insertAtCursor(<?php echo $choise; ?>, '<?php echo $param; ?>')" />
                  <?php
                }
                ?>
                <input style="border: 1px solid silver; font-size: 10px; margin: 3px;" type="button" value="All fields list" onClick="insertAtCursor(<?php echo $choise; ?>, 'all')" />
              </div>
              <?php
              if (user_can_richedit()) {
                wp_editor($row->script_mail, 'script_mail', array('teeny' => FALSE, 'textarea_name' => 'script_mail', 'media_buttons' => FALSE, 'textarea_rows' => 5));
              }
              else {
                ?>
                <textarea name="script_mail" id="script_mail" cols="20" rows="10" style="width:300px; height:450px;"><?php echo $row->script_mail; ?></textarea>
                <?php
              }
              ?>
            </td>
          </tr>
          <tr>
            <td valign="top" height="30"></td>
            <td valign="top"></td>
          </tr>
          <tr>
            <td class="fm_options_label" valign="top">
              <label>For User</label>
            </td>
            <td class="fm_options_value">
              <div style="margin-bottom:5px">
                <?php
                $choise = "document.getElementById('script_mail_user')";
                for ($i = 0; $i < count($label_label); $i++) {
                  if ($label_type[$i] == "type_submit_reset" || $label_type[$i] == "type_editor" || $label_type[$i] == "type_map" || $label_type[$i] == "type_mark_map" || $label_type[$i] == "type_captcha" || $label_type[$i] == "type_recaptcha" || $label_type[$i] == "type_button") {
                    continue;
                  }
                  $param = htmlspecialchars(addslashes($label_label[$i]));
                  ?>
                  <input style="border: 1px solid silver; font-size: 10px;" type="button" value="<?php echo htmlspecialchars(addslashes($label_label[$i])); ?>" onClick="insertAtCursor(<?php echo $choise; ?>, '<?php echo $param; ?>')" />
                  <?php
                }
                ?>
                <input style="border: 1px solid silver; font-size: 10px; margin:3px;" type="button" value="All fields list" onClick="insertAtCursor(<?php echo $choise; ?>, 'all')" />
              </div>
              <?php
              if (user_can_richedit()) {
                wp_editor($row->script_mail_user, 'script_mail_user', array('teeny' => FALSE, 'textarea_name' => 'script_mail_user', 'media_buttons' => FALSE, 'textarea_rows' => 5));
              }
              else {
                ?>
                <textarea name="script_mail_user" id="script_mail_user" cols="20" rows="10" style="width:300px; height:450px;"><?php echo $row->script_mail_user; ?></textarea>
                <?php
              }
              ?>
            </td>
          </tr>
        </table>
      </fieldset>
      <fieldset id="general_fieldset" class="adminform fm_fieldset_deactive">
        <legend style="color:#0B55C4;font-weight: bold;">General Options</legend>
        <table class="admintable" style="float:left">
          <tr valign="top">
            <td class="fm_options_label">
              <label for="mail">Email to send submissions to</label>
            </td>
            <td class="fm_options_value">
              <input id="mail" name="mail" value="<?php echo $row->mail; ?>" style="width:250px;" />
            </td>
          </tr>
          <tr valign="top">
            <td class="fm_options_label">
              <label for="from_mail">From Email</label>
            </td>
            <td class="fm_options_value">
              <input id="from_mail" name="from_mail" value="<?php echo $row->from_mail; ?>" style="width:250px;" />
            </td>
          </tr>
          <tr valign="top">
            <td class="fm_options_label">
              <label for="from_name">From Name</label>
            </td>
            <td class="fm_options_value">
              <input id="from_name" name="from_name" value="<?php echo $row->from_name; ?>" style="width:250px;"/>
            </td>
          </tr>
          <tr valign="top">
            <td class="fm_options_label">
              <label for="theme">Theme</label>
            </td>
            <td class="fm_options_value">
              <select id="theme" name="theme" style="width:260px;" onChange="set_preview()">
                <?php
                foreach ($themes as $theme) {
                  ?>
                  <option value="<?php echo $theme->id; ?>" <?php echo (($theme->id == $row->theme) ? 'selected' : ''); ?>><?php echo $theme->title; ?></option>
                  <?php
                }
                ?>
              </select>
              <a href="<?php echo add_query_arg(array('action' => 'FormMakerPreview_fmc', 'id' => $row->theme, 'form_id' => $row->id, 'width' => '1000', 'height' => '500', 'TB_iframe' => '1'), admin_url('admin-ajax.php')); ?>" class="button-primary thickbox thickbox-preview" id="preview_form" title="Form Preview" onclick="return false;">
                Preview
              </a>
            </td>
          </tr>
        </table>
      </fieldset>
      <fieldset id="payment_fieldset" class="adminform fm_fieldset_deactive">
        <legend style="color:#0B55C4;font-weight: bold;">Payment Options</legend>
        <table class="admintable">
          <tr valign="top">
            <td class="fm_options_label">
              <label>Turn Paypal On</label>
            </td>
            <td class="fm_options_value">
              <div><input type="radio" name="paypal_mode" id="paypal_mode1" value="1" <?php echo ($row->paypal_mode == "1") ? "checked" : ""; ?> /><label for="paypal_mode1">On</label></div>
              <div><input type="radio" name="paypal_mode" id="paypal_mode2" value="0" <?php echo ($row->paypal_mode != "1") ? "checked" : ""; ?> /><label for="paypal_mode2">Off</label></div>
            </td>
          </tr>
          <tr valign="top">
            <td class="fm_options_label">
              <label>Checkout Mode</label>
            </td>
            <td class="fm_options_value">
              <div><input type="radio" name="checkout_mode" id="checkout_mode1" value="production" <?php echo ($row->checkout_mode == "production") ? "checked" : ""; ?> /><label for="checkout_mode1">Production</label></div>
              <div><input type="radio" name="checkout_mode" id="checkout_mode2" value="testmode" <?php echo ($row->checkout_mode != "production") ? "checked" : ""; ?> /><label for="checkout_mode2">Testmode</label></div>
            </td>
          </tr>
          <tr valign="top">
            <td class="fm_options_label">
              <label for="paypal_email">Paypal email</label>
            </td>
            <td class="fm_options_value">
              <input type="text" name="paypal_email" id="paypal_email" value="<?php echo $row->paypal_email; ?>" class="text_area" style="width:250px">
            </td>
          </tr>
          <tr valign="top">
            <td class="fm_options_label">
              <label for="payment_currency">Payment Currency</label>
            </td>
            <td class="fm_options_value">
              <select id="payment_currency" name="payment_currency" style="width:253px">
                <option value="USD" <?php echo (($row->payment_currency == 'USD') ? 'selected' : ''); ?>>$ &#8226; U.S. Dollar</option>
                <option value="EUR" <?php echo (($row->payment_currency == 'EUR') ? 'selected' : ''); ?>>&#8364; &#8226; Euro</option>
                <option value="GBP" <?php echo (($row->payment_currency == 'GBP') ? 'selected' : ''); ?>>&#163; &#8226; Pound Sterling</option>
                <option value="JPY" <?php echo (($row->payment_currency == 'JPY') ? 'selected' : ''); ?>>&#165; &#8226; Japanese Yen</option>
                <option value="CAD" <?php echo (($row->payment_currency == 'CAD') ? 'selected' : ''); ?>>C$ &#8226; Canadian Dollar</option>
                <option value="MXN" <?php echo (($row->payment_currency == 'MXN') ? 'selected' : ''); ?>>Mex$ &#8226; Mexican Peso</option>
                <option value="HKD" <?php echo (($row->payment_currency == 'HKD') ? 'selected' : ''); ?>>HK$ &#8226; Hong Kong Dollar</option>
                <option value="HUF" <?php echo (($row->payment_currency == 'HUF') ? 'selected' : ''); ?>>Ft &#8226; Hungarian Forint</option>
                <option value="NOK" <?php echo (($row->payment_currency == 'NOK') ? 'selected' : ''); ?>>kr &#8226; Norwegian Kroner</option>
                <option value="NZD" <?php echo (($row->payment_currency == 'NZD') ? 'selected' : ''); ?>>NZ$ &#8226; New Zealand Dollar</option>
                <option value="SGD" <?php echo (($row->payment_currency == 'SGD') ? 'selected' : ''); ?>>S$ &#8226; Singapore Dollar</option>
                <option value="SEK" <?php echo (($row->payment_currency == 'SEK') ? 'selected' : ''); ?>>kr &#8226; Swedish Kronor</option>
                <option value="PLN" <?php echo (($row->payment_currency == 'PLN') ? 'selected' : ''); ?>>zl &#8226; Polish Zloty</option>
                <option value="AUD" <?php echo (($row->payment_currency == 'AUD') ? 'selected' : ''); ?>>A$ &#8226; Australian Dollar</option>
                <option value="DKK" <?php echo (($row->payment_currency == 'DKK') ? 'selected' : ''); ?>>kr &#8226; Danish Kroner</option>
                <option value="CHF" <?php echo (($row->payment_currency == 'CHF') ? 'selected' : ''); ?>>CHF &#8226; Swiss Francs</option>
                <option value="CZK" <?php echo (($row->payment_currency == 'CZK') ? 'selected' : ''); ?>>Kc &#8226; Czech Koruny</option>
                <option value="ILS" <?php echo (($row->payment_currency == 'ILS') ? 'selected' : ''); ?>>&#8362; &#8226; Israeli Sheqel</option>
                <option value="BRL" <?php echo (($row->payment_currency == 'BRL') ? 'selected' : ''); ?>>R$ &#8226; Brazilian Real</option>
                <option value="TWD" <?php echo (($row->payment_currency == 'TWD') ? 'selected' : ''); ?>>NT$ &#8226; Taiwan New Dollars</option>
                <option value="MYR" <?php echo (($row->payment_currency == 'MYR') ? 'selected' : ''); ?>>RM &#8226; Malaysian Ringgit</option>
                <option value="PHP" <?php echo (($row->payment_currency == 'PHP') ? 'selected' : ''); ?>>&#8369; &#8226; Philippine Peso</option>
                <option value="THB" <?php echo (($row->payment_currency == 'THB') ? 'selected' : ''); ?>>&#xe3f; &#8226; Thai Bahtv</option>
              </select>
            </td>
          </tr>
          <tr valign="top">
            <td class="fm_options_label">
              <label for="tax">Tax</label>
            </td>
            <td class="fm_options_value">
              <input type="text" name="tax" id="tax" value="<?php echo $row->tax; ?>" class="text_area" style="width: 40px;" onKeyPress="return check_isnum_point(event)"> %
            </td>
          </tr>
        </table>
      </fieldset>
      <fieldset id="javascript_fieldset" class="adminform fm_fieldset_deactive">
        <legend style="color:#0B55C4;font-weight: bold;">JavaScript</legend>
        <table class="admintable">
          <tr valign="top">
            <td class="fm_options_label">
              <label for="javascript">Javascript</label>
            </td>
            <td class="fm_options_value">
              <textarea style="margin: 0px;" cols="60" rows="30" name="javascript" id="javascript"><?php echo $row->javascript; ?></textarea>
            </td>
          </tr>
        </table>
      </fieldset>
      <input type="hidden" name="fieldset_id" id="fieldset_id" value="<?php echo WDW_FMC_Library::get('fieldset_id', 'general'); ?>" />
      <input type="hidden" id="task" name="task" value=""/>
      <input type="hidden" id="current_id" name="current_id" value="<?php echo $row->id; ?>" />
    </form>
    <script>
      jQuery(window).load(function () {
        form_maker_options_tabs(jQuery("#fieldset_id").val());
        fm_popup();
      });
    </script>
    <?php
  }
    
	public function form_options($id) {
		
	
		$row = $this->model->get_row_data($id);
		$themes = $this->model->get_theme_rows_data();
		$queries = $this->model->get_queries_rows_data($id);
		$userGroups = get_editable_roles();
		$page_title = $row->title . ' form options';
		$label_id = array();
		$label_label = array();
		$label_type = array();
		$label_all = explode('#****#', $row->label_order_current);
		$label_all = array_slice($label_all, 0, count($label_all) - 1);
		foreach ($label_all as $key => $label_each) {
			$label_id_each = explode('#**id**#', $label_each);
			array_push($label_id, $label_id_each[0]);
			$label_order_each = explode('#**label**#', $label_id_each[1]);
			array_push($label_label, $label_order_each[0]);
			array_push($label_type, $label_order_each[1]);
		}
		$fields = explode('*:*id*:*type_submitter_mail*:*type*:*', $row->form_fields);
		$fields_count = count($fields);
		?>
		<script>
			function fm_change_radio_checkbox_text(elem) {
				var labels_array = [];
					labels_array['paypal_mode'] = ['Off', 'On'];
					labels_array['checkout_mode'] = ['Testmode', 'Production'];
					labels_array['mail_mode'] = ['Text', 'HTML'];
					labels_array['mail_mode_user'] = ['Text', 'HTML'];
					labels_array['value'] = ['1', '0'];
				
				jQuery(elem).val(labels_array['value'][jQuery(elem).val()]);
				jQuery(elem).next().val(jQuery(elem).val());
				
				var clicked_element = labels_array[jQuery(elem).attr('name')];
				jQuery(elem).find('label').html(clicked_element[jQuery(elem).val()]);
				if(jQuery( elem ).hasClass( "fm-text-yes" )) {
					jQuery( elem ).removeClass('fm-text-yes').addClass('fm-text-no');
					jQuery(elem).find("span").animate({
						right: parseInt(jQuery( elem ).css( "width")) - 14 + 'px'
					}, 400, function() {
					}); 
				}	
				else {
					jQuery( elem ).removeClass('fm-text-no').addClass('fm-text-yes');
					jQuery(elem).find("span").animate({
						right: 0
					}, 400, function() {
					}); 
				}		
			}
			
			gen = "<?php echo $row->counter; ?>";
			form_view_max = 20;
			function set_preview() {
				jQuery("#preview_form").attr("onclick", "tb_show('', '<?php echo add_query_arg(array('action' => 'FormMakerPreview_fmc', 'form_id' => $row->id), admin_url('admin-ajax.php')); ?>&test_theme=" + jQuery('#theme').val() + "&width=1000&height=500&TB_iframe=1'); return false;");
				jQuery("#edit_css").attr("onclick", "tb_show('', '<?php echo add_query_arg(array('action' => 'FormMakerEditCSS_fmc', 'form_id' => $row->id), admin_url('admin-ajax.php')); ?>&id=" + jQuery('#theme').val() + "&width=800&height=500&TB_iframe=1'); return false;");
			}
			
			function set_condition() {
				field_condition = '';
				for(i=0;i<500;i++) {
					conditions = '';
					if(document.getElementById("condition"+i)) {
						field_condition+=document.getElementById("show_hide"+i).value+"*:*show_hide*:*";
						field_condition+=document.getElementById("fields"+i).value+"*:*field_label*:*";
						field_condition+=document.getElementById("all_any"+i).value+"*:*all_any*:*";
						for(k=0;k<500;k++) {
							if(document.getElementById("condition_div"+i+"_"+k)) {
								conditions+=document.getElementById("field_labels"+i+"_"+k).value+"***";
								conditions+=document.getElementById("is_select"+i+"_"+k).value+"***";
								if(document.getElementById("field_value"+i+"_"+k).tagName=="SELECT" ) {
									if(document.getElementById("field_value"+i+"_"+k).getAttribute('multiple')) {
										var sel = document.getElementById("field_value"+i+"_"+k);
										var selValues = '';
										for(m=0; m < sel.length; m++) {
											if(sel.options[m].selected)
											
											selValues += sel.options[m].value+"@@@";
										}
										conditions+=selValues;
									} else {
										conditions+=document.getElementById("field_value"+i+"_"+k).value;
									}								
								}
								else
									conditions+=document.getElementById("field_value"+i+"_"+k).value;
								conditions+="*:*next_condition*:*";
							}
						}
						field_condition+=conditions;
						field_condition+="*:*new_condition*:*";
					}
				}
				document.getElementById('condition').value = field_condition;
			}      
    
			function show_verify_options(s){
				if(s){
					jQuery(".verification_div").removeAttr( "style" );
					jQuery(".expire_link").removeAttr( "style" );
						
				} else{
					jQuery(".verification_div").css( 'display', 'none' );
					jQuery(".expire_link").css( 'display', 'none' );
				}
			}
		</script>
		<style>
		.CodeMirror {
			border: 1px solid #ccc;
			font-size: 12px;
			margin-bottom: 6px;
			background: white;
		}
		</style>
		<div class="fm-user-manual">
			This section allows you to edit form options.
			<a style="color: blue; text-decoration: none;" target="_blank" href="https://web-dorado.com/wordpress-form-maker-guide-3.html">Read More in User Manual</a>
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
		<form class="wrap" method="post" action="admin.php?page=manage_fmc" style="width:99%;" name="adminForm" id="adminForm">
			<?php wp_nonce_field('nonce_fmc', 'nonce_fmc'); ?>
			<div class="fm-page-header">
				<div class="fm-page-title" style="width: inherit;">
					<?php echo $page_title; ?>
				</div>
				<div class="fm-page-actions">
					<button class="fm-button save-button small" onclick="if (fm_check_email('mailToAdd') ||fm_check_email('from_mail') || fm_check_email('reply_to') || fm_check_email('mail_from_user') || fm_check_email('reply_to_user') || fm_check_email('mail_from_other') || fm_check_email('reply_to_other') || fm_check_email('paypal_email')) {return false;}; set_condition(); wd_fm_apply_options('save_options');">
						<span></span>
						Save
					</button>
					<button class="fm-button apply-button small" onclick="if (fm_check_email('mailToAdd') ||  fm_check_email('from_mail') || fm_check_email('reply_to') || fm_check_email('mail_from_user') || fm_check_email('reply_to_user') || fm_check_email('mail_from_other') || fm_check_email('reply_to_other') || fm_check_email('paypal_email')) {return false;}; set_condition(); wd_fm_apply_options('apply_options');">
						<span></span>
						Apply
					</button>
					<button class="fm-button cancel-button small" onclick="fm_set_input_value('task', 'cancel_options');">
						<span></span>
						Cancel
					</button>
				</div>
				<div class="fm-clear"></div>
			</div>	
			<div class="fm-form-options">
				<div class="submenu-box">
					<div class="submenu-pad">
						<ul id="submenu" class="configuration">
							<li>
								<a id="general" class="fm_fieldset_tab" onclick="form_maker_options_tabs('general')" href="#">General Options</a>
							</li>
							<li>
								<a id="custom" class="fm_fieldset_tab" onclick="form_maker_options_tabs('custom')" href="#">Email Options</a>
							</li>
							<li>
								<a id="actions" class="fm_fieldset_tab" onclick="form_maker_options_tabs('actions')" href="#">Actions after Submission</a>
							</li>
							<li>
								<a id="payment" class="fm_fieldset_tab" onclick="form_maker_options_tabs('payment')" href="#">Payment Options</a>
							</li>
							<li>
								<a id="javascript" class="fm_fieldset_tab" onclick="form_maker_options_tabs('javascript'); codemirror_for_javascript();" href="#">JavaScript</a>
							</li>
							<li>
								<a id="conditions" class="fm_fieldset_tab" onclick="form_maker_options_tabs('conditions')" href="#">Conditional Fields</a>
							</li>
							
						</ul>
					</div>
				</div>
				<fieldset id="general_fieldset" class="adminform fm_fieldset_deactive">
					<legend>General Options</legend>
						<table class="admintable" >
							<tr valign="top">
								<td class="fm_options_label">
									<label>Published</label>
								</td>
								<td class="fm_options_value">
									<button class="fm-checkbox-radio-button <?php echo $row->published == 1 ? 'fm-yes' : 'fm-no' ?>" onclick="fm_change_radio(this); return false;" value="<?php echo $row->published; ?>">
										<span></span>
									</button>
									<input type="hidden" name="published" value="<?php echo $row->published; ?>"/>
								</td>
							</tr>
							<tr valign="top">
								<td class="fm_options_label">
									<label>Save data(to database)</label>
								</td>
								<td class="fm_options_value">
									<button class="fm-checkbox-radio-button <?php echo $row->savedb == 1 ? 'fm-yes' : 'fm-no' ?>" onclick="fm_change_radio(this); return false;" value="<?php echo $row->savedb; ?>">
										<span></span>
									</button>
									<input type="hidden" name="savedb" value="<?php echo $row->savedb; ?>"/>
								</td>
							</tr>
							<tr valign="top">
								<td class="fm_options_label">
									<label for="theme">Theme</label>
								</td>
								<td class="fm_options_value">
									<select id="theme" name="theme" onChange="set_preview()">
										<?php
										foreach ($themes as $theme) {
											?>
											<option value="<?php echo $theme->id; ?>" <?php echo (($theme->id == $row->theme) ? 'selected' : ''); ?>><?php echo $theme->title; ?></option>
											<?php
										}
										?>
									</select>
									<button id="preview_form" class="fm-button preview-button small" onclick="tb_show('', '<?php echo add_query_arg(array('action' => 'FormMakerPreview_fmc', 'form_id' => $row->id, 'test_theme' => $row->theme, 'width' => '1000', 'height' => '500', 'TB_iframe' => '1'), admin_url('admin-ajax.php')); ?>'); return false;">
										<span></span>
										Preview
									</button>
									<button id="edit_css" class="fm-button options-edit-button small" onclick="tb_show('', '<?php echo add_query_arg(array('action' => 'FormMakerEditCSS_fmc', 'id' => $row->theme, 'form_id' => $row->id, 'width' => '1000', 'height' => '500', 'TB_iframe' => '1'), admin_url('admin-ajax.php')); ?>'); return false;">
										<span></span>
										Edit CSS
									</button>
								</td>
							</tr>
							<tr valign="top">
								<td class="fm_options_label">
									<label for="requiredmark">Required fields mark</label>
								</td>
								<td class="fm_options_value">
									<input type="text" id="requiredmark" name="requiredmark" value="<?php echo $row->requiredmark; ?>" style="width:250px;" />
								</td>
							</tr>
							<tr valign="top">
								<td class="fm_options_label">
									<label>Save Uploads</label>
								</td>
								<td class="fm_options_value">
									<button class="fm-checkbox-radio-button <?php echo $row->save_uploads == 1 ? 'fm-yes' : 'fm-no' ?>" onclick="fm_change_radio(this); return false;" value="<?php echo $row->save_uploads; ?>">
										<span></span>
									</button>
									<input type="hidden" name="save_uploads" value="<?php echo $row->save_uploads; ?>"/>
								</td>
							</tr>
						</table>
						<br/>
						<div class="error_fm" style="padding: 5px; font-size: 14px;">Front end submissions are disabled in free version.</div>
						<fieldset class="adminform">
							<legend>Front end submissions access level</legend>
							<table>
								<tr>
									<td class="key">
										<label for="name">Allow User to see submissions:</label>
									</td>
									<td>
										<?php
										$checked_UserGroup=explode(',',$row->user_id_wd);
										$i = 0;
										foreach($userGroups as $val => $uG) {
											echo '<input type="checkbox" value="'.$val .'"  id="user_'.$i.'"';                  
											if(in_array($val ,$checked_UserGroup))
												echo 'checked="checked"';                  
											echo 'onchange="acces_level('.count($userGroups).')" disabled/><label for="user_'.$i.'">'.$uG["name"].'</label><br>';
											$i++;
										}
										?>
										<input type="checkbox" value="guest"  id="user_<?php echo $i; ?>" onchange="acces_level(<?php echo count($userGroups); ?>)"<?php echo (in_array('guest', $checked_UserGroup) ? 'checked="checked"' : '') ?> disabled/><label for="user_<?php echo $i; ?>">Guest</label>
										<input type="hidden" name="user_id_wd" value="<?php echo $row->user_id_wd ?>" id="user_id_wd" />
									</td>
								</tr>              
							</table>
						</fieldset>
						<?php
						$labels_for_submissions = $this->model->get_labels($id);
						$payment_info = $this->model->is_paypal($id);
						$labels_id_for_submissions= array();
						$label_titles_for_submissions=array();
						$labels_type_for_submissions= array();
						if($labels_for_submissions) {
							$label_id_for_submissions= array();
							$label_order_original_for_submissions= array();
							$label_type_for_submissions= array();
							
							if(strpos($row->label_order, 'type_paypal_')) {
								$row->label_order=$row->label_order."item_total#**id**#Item Total#**label**#type_paypal_payment_total#****#total#**id**#Total#**label**#type_paypal_payment_total#****#0#**id**#Payment Status#**label**#type_paypal_payment_status#****#";
							}
							
							$label_all_for_submissions	= explode('#****#',$row->label_order);
							$label_all_for_submissions 	= array_slice($label_all_for_submissions,0, count($label_all_for_submissions)-1);   
							foreach($label_all_for_submissions as $key => $label_each) {
								$label_id_each=explode('#**id**#',$label_each);
								array_push($label_id_for_submissions, $label_id_each[0]);
								$label_order_each=explode('#**label**#', $label_id_each[1]);
								array_push($label_order_original_for_submissions, $label_order_each[0]);
								array_push($label_type_for_submissions, $label_order_each[1]);
							}
							
							foreach($label_id_for_submissions as $key => $label) {
								if(in_array($label, $labels_for_submissions)) {
									array_push($labels_type_for_submissions, $label_type_for_submissions[$key]);
									array_push($labels_id_for_submissions, $label);
									array_push($label_titles_for_submissions, $label_order_original_for_submissions[$key]);
								}
							}
						}
						
						$stats_labels = array();
						$stats_labels_ids = array();
						foreach($labels_type_for_submissions as $key => $label_type_cur) {
							if($label_type_cur=="type_checkbox" || $label_type_cur=="type_radio" || $label_type_cur=="type_own_select" || $label_type_cur=="type_country" || $label_type_cur=="type_paypal_select" || $label_type_cur=="type_paypal_radio" || $label_type_cur=="type_paypal_checkbox" || $label_type_cur=="type_paypal_shipping") {
								$stats_labels_ids[] = $labels_id_for_submissions[$key];
								$stats_labels[] = $label_titles_for_submissions[$key];
							}
						}
						?>
						<script type="text/javascript">
							function inArray(needle, myarray) {
								var length = myarray.length;
								for(var i = 0; i < length; i++) {
									if(myarray[i] == needle) return true;
								}
								return false;
							}

							function checked_labels(class_name) {
								var checked_ids ='';            
								jQuery('.'+class_name).each(function() {
								  if(this.checked) {
									checked_ids += this.value+',';		
								  }
								}); 
								
								if(class_name == 'filed_label') {
									document.getElementById("frontend_submit_fields").value = checked_ids ;
									if(checked_ids == document.getElementById("all_fields").value)
										document.getElementById("all_fields").checked = true;
									else
										document.getElementById("all_fields").checked = false;
								}
								else {
								  document.getElementById("frontend_submit_stat_fields").value = checked_ids ;
								  if(checked_ids == document.getElementById("all_stats_fields").value)
									document.getElementById("all_stats_fields").checked = true;
								  else
									document.getElementById("all_stats_fields").checked = false;
								}
							}
          
							jQuery(document).ready(function () {
								jQuery('.filed_label').each(function() {
									if(document.getElementById("frontend_submit_fields").value == document.getElementById("all_fields").value)
										document.getElementById("all_fields").checked = true;
									if(inArray(this.value, document.getElementById("frontend_submit_fields").value.split(","))) {
										this.checked = true;
									}
								});

								jQuery('.stats_filed_label').each(function() {
									if(document.getElementById("frontend_submit_stat_fields").value == document.getElementById("all_stats_fields").value)
										document.getElementById("all_stats_fields").checked = true;          
									if(inArray(this.value, document.getElementById("frontend_submit_stat_fields").value.split(","))) {
										this.checked = true;		
									}              
								});
              
								jQuery(document).on('change','input[name="all_fields"]',function() {
									jQuery('.filed_label').prop("checked" , this.checked);
								});

								jQuery(document).on('change','input[name="all_stats_fields"]',function() {
									jQuery('.stats_filed_label').prop("checked" , this.checked);
								});
							});
						</script>
						<style>
						li{
							list-style-type: none;
						}

						.simple_table {
							padding-left: 0px !important;
						}

						.simple_table input, .simple_table label, .simple_table img {
							display:inline-block !important;
							float:none !important;
						}
						</style>
						<fieldset class="adminform">
							<legend>Fields to hide in frontend submissions</legend>
							<?php if(count($label_titles_for_submissions)): ?>
							<table style="margin-left:-3px;">
							  <tr>
								<td> 
								  <label>Select fields:</label>
								</td>
								<td  class="simple_table">
								  <ul id="form_fields">
									<li>
									<input type="checkbox" name="all_fields" id="all_fields" value="submit_id,<?php echo implode(',',$labels_id_for_submissions)."," . ($payment_info ? "payment_info" : ""); ?>" onclick="checked_labels('filed_label')" disabled/>
									<label for="all_fields">Select All</label>
									</li>
									<?php 
									echo "<li><input type=\"checkbox\" id=\"submit_id\" name=\"submit_id\" value=\"submit_id\" class=\"filed_label\"  onclick=\"checked_labels('filed_label')\" disabled><label for=\"submit_id\">ID</label></li>";	
									  
									for($i=0, $n=count($label_titles_for_submissions); $i < $n ; $i++)     
									{
									  $field_label = $label_titles_for_submissions[$i];

									  echo "<li><input type=\"checkbox\" id=\"filed_label".$i."\" name=\"filed_label".$i."\" value=\"".$labels_id_for_submissions[$i]."\" class=\"filed_label\" onclick=\"checked_labels('filed_label')\" disabled><label for=\"filed_label".$i."\">".(strlen($field_label) > 80 ? substr ($field_label ,0, 80).'...' : $field_label)."</label></li>";
										   
									}
									if($payment_info)
									echo "<li><input type=\"checkbox\" id=\"payment_info\" name=\"payment_info\" value=\"payment_info\" class=\"filed_label\" onclick=\"checked_labels('filed_label')\" disabled><label for=\"payment_info\">Payment Info</label></li>";
									?>
								  </ul>
								  <input type="hidden" name="frontend_submit_fields" value="<?php echo $row->frontend_submit_fields ?>" id="frontend_submit_fields" />
								</td>	
							  </tr>
							  <?php if($stats_labels): ?>
							  <tr id="stats">
								<td> 
								  <label>Stats fields:</label>
								</td>
								<td class="simple_table">
								  <ul id="stats_fields">
									<li>
									<input type="checkbox" name="all_stats_fields" id="all_stats_fields" value="<?php echo implode(',',$stats_labels_ids).","; ?>" onclick="checked_labels('stats_filed_label')" disabled>
									<label for="all_stats_fields">Select All</label>
									</li>
									<?php 
									for($i=0, $n=count($stats_labels); $i < $n ; $i++)     
									{
									  $field_label = $stats_labels[$i];
									  echo "<li><input type=\"checkbox\" id=\"stats_filed_label".$i."\" name=\"stats_filed_label".$i."\" value=\"".$stats_labels_ids[$i]."\" class=\"stats_filed_label\" onclick=\"checked_labels('stats_filed_label')\" disabled><label for=\"stats_filed_label".$i."\" >".(strlen($field_label) > 80 ? substr ($field_label ,0, 80).'...' : $field_label)."</label></li>";
									}
									?>
								  </ul>
								  <input type="hidden" name="frontend_submit_stat_fields" value="<?php echo $row->frontend_submit_stat_fields ?>" id="frontend_submit_stat_fields" />
								</td>	
							  </tr>
							  <?php endif; ?>
							</table>
						  <?php endif; ?>
						</fieldset>
				</fieldset>
				<fieldset id="custom_fieldset" class="adminform fm_fieldset_deactive">
					<legend>Email Options</legend>
					<table class="admintable">
						<tr valign="top">
							<td style="width: 75px; vertical-align: middle;">
								<label>Send E-mail</label>
							</td>
							<td style="padding: 15px;">
								<button class="fm-checkbox-radio-button <?php echo $row->sendemail == 1 ? 'fm-yes' : 'fm-no' ?>" onclick="fm_change_radio(this); return false;" value="<?php echo $row->sendemail; ?>">
									<span></span>
								</button>
								<input type="hidden" name="sendemail" value="<?php echo $row->sendemail; ?>"/>
							</td>
						</tr>
					</table>
					<fieldset class="adminform fm_mail_options">
						<legend>Email to Administrator</legend>
						<table class="admintable">
							<tr valign="top">
								<td class="fm_options_label">
									<label for="mailToAdd">Email to send submissions to</label>
								</td>
								<td class="fm_options_value">
									<input type="text" id="mailToAdd" name="mailToAdd" style="width: 250px;" />
									<input type="hidden" id="mail" name="mail" value="<?php echo $row->mail . ($row->mail && (substr($row->mail, -1) != ',') ? ',' : ''); ?>" />
									<img src="<?php echo WD_FMC_URL . '/images/add.png?ver='. get_option("wd_form_maker_version"); ?>" style="vertical-align: middle; cursor: pointer;" title="Add more emails" onclick="if (fm_check_email('mailToAdd')) {return false;};cfm_create_input('mail', 'mailToAdd', 'cfm_mail_div', '<?php echo WD_FMC_URL; ?>')" />
									<div id="cfm_mail_div">
										<?php
										$mail_array = explode(',', $row->mail);
										foreach ($mail_array as $mail) {
											if ($mail && $mail != ',') {
												?>
												<div class="fm_mail_input">
													<?php echo $mail; ?>
													<img src="<?php echo WD_FMC_URL; ?>/images/delete.png?ver=<?php echo get_option("wd_form_maker_version"); ?>" class="fm_delete_img" onclick="fm_delete_mail(this, '<?php echo $mail; ?>')" title="Delete Email" />
												</div>
												<?php
											}
										}
										?>
									</div>
								</td>
							</tr>
							<tr valign="top">
								<td class="fm_options_label">
									<label for="from_mail">Email From</label>
								</td>
								<td class="fm_options_value">
									<?php 
									$is_other = TRUE;
									for ($i = 0; $i < $fields_count - 1; $i++) {
										?>
										<div>
											 <input type="radio" name="from_mail" id="from_mail<?php echo $i; ?>" value="<?php echo (strlen($fields[$i])!=1 ? substr($fields[$i], strrpos($fields[$i], '*:*new_field*:*')+15, strlen($fields[$i])) : $fields[$i]); ?>"  <?php echo ((strlen($fields[$i])!=1 ? substr($fields[$i], strrpos($fields[$i], '*:*new_field*:*')+15, strlen($fields[$i])) : $fields[$i]) == $row->from_mail ? 'checked="checked"' : '' ); ?> onclick="wdhide('mail_from_other')" />
											<label for="from_mail<?php echo $i; ?>"><?php echo substr($fields[$i + 1], 0, strpos($fields[$i + 1], '*:*w_field_label*:*')); ?></label>
										</div>
										<?php
										if(strlen($fields[$i])!=1) {
											if (substr($fields[$i], strrpos($fields[$i], '*:*new_field*:*') + 15, strlen($fields[$i])) == $row->from_mail) {
												$is_other = FALSE;
											}
										}
										else {
											if($fields[$i] == $row->from_mail)
												$is_other=false;
										}
									}
									?>
									<div style="<?php echo ($fields_count == 1) ? 'display:none;' : ''; ?>">
										<input type="radio" id="other" name="from_mail" value="other" <?php echo ($is_other) ? 'checked="checked"' : ''; ?> onclick="wdshow('mail_from_other')" />
										<label for="other">Other</label>
									</div>
									<input type="text" style="width: <?php echo ($fields_count == 1) ? '250px' : '235px; margin-left: 15px' ?>; display: <?php echo ($is_other) ? 'block;' : 'none;'; ?>" id="mail_from_other" name="mail_from_other" value="<?php echo ($is_other) ? $row->from_mail : ''; ?>" />
								</td>
							</tr>
							<tr valign="top">
								<td class="fm_options_label">
									<label for="from_name">From Name</label>
								</td>
								<td class="fm_options_value">
									<input type="text" id="from_name" name="from_name" value="<?php echo $row->from_name; ?>" style="width: 250px;" />
									<img src="<?php echo WD_FMC_URL . '/images/add.png?ver='. get_option("wd_form_maker_version").''; ?>" onclick="document.getElementById('mail_from_labels').style.display='block';" style="vertical-align: middle; cursor: pointer;display:inline-block; margin:0px; float:none;">
									<?php 
									$choise = "document.getElementById('from_name')";
									echo '<div style="position:relative; top:-3px;"><div id="mail_from_labels" class="email_labels" style="display:none;">';
									echo "<a onClick=\"insertAtCursor(".$choise.",'username'); document.getElementById('mail_from_labels').style.display='none';\" style=\"display:block; text-decoration:none;\">Username</a>";
									for($i=0; $i<count($label_label); $i++) {
										if($label_type[$i]=="type_submit_reset" || $label_type[$i]=="type_editor" || $label_type[$i]=="type_map" || $label_type[$i]=="type_mark_map" || $label_type[$i]=="type_captcha"|| $label_type[$i]=="type_recaptcha" || $label_type[$i]=="type_button" || $label_type[$i]=="type_file_upload" || $label_type[$i]=="type_send_copy" || $label_type[$i]=="type_matrix")			
										continue;		
										
										$param = htmlspecialchars(addslashes($label_label[$i]));			
										$fld_label = $param;
										if(strlen($fld_label)>30) {
											$fld_label = wordwrap(htmlspecialchars(addslashes($label_label[$i])), 30);
											$fld_label = explode("\n", $fld_label);
											$fld_label = $fld_label[0] . ' ...';	
										}
									
										echo "<a onClick=\"insertAtCursor(".$choise.",'".$param."'); document.getElementById('mail_from_labels').style.display='none';\" style=\"display:block; text-decoration:none;\">".$fld_label."</a>";
									}
									echo "<a onClick=\"insertAtCursor(".$choise.",'subid'); document.getElementById('mail_from_name_user_labels').style.display='none';\" style=\"display:block; text-decoration:none;\">Submission ID</a>";	

									echo "<a onClick=\"insertAtCursor(".$choise.",'username'); document.getElementById('mail_from_name_user_labels').style.display='none';\" style=\"display:block; text-decoration:none;\">Username</a>";
									echo '</div></div>';								
									?>
								</td>
							</tr>
							<tr valign="top">
								<td class="fm_options_label">
									<label for="reply_to">Reply to<br/>(if different from "Email From") </label>
								</td>
								<td class="fm_options_value">
									<?php 
									$is_other = TRUE;
									for ($i = 0; $i < $fields_count - 1; $i++) {
										?>
										<div>
											<input type="radio" name="reply_to" id="reply_to<?php echo $i; ?>" value="<?php echo (strlen($fields[$i])!=1 ? substr($fields[$i], strrpos($fields[$i], '*:*new_field*:*')+15, strlen($fields[$i])) : $fields[$i]); ?>"  <?php echo ((strlen($fields[$i])!=1 ? substr($fields[$i], strrpos($fields[$i], '*:*new_field*:*')+15, strlen($fields[$i])) : $fields[$i]) == $row->reply_to ? 'checked="checked"' : '' ); ?> onclick="wdhide('reply_to_other')" />
											<label for="reply_to<?php echo $i; ?>"><?php echo substr($fields[$i + 1], 0, strpos($fields[$i + 1], '*:*w_field_label*:*')); ?></label>
										</div>
										<?php
										if(strlen($fields[$i])!=1) {
											if (substr($fields[$i], strrpos($fields[$i], '*:*new_field*:*') + 15, strlen($fields[$i])) == $row->reply_to) {
												$is_other = FALSE;
											}
										}
										else {
											if($fields[$i] == $row->reply_to)
												$is_other=false;
										}
									}
									?>
									<div style="<?php echo ($fields_count == 1) ? 'display: none;' : ''; ?>">
										<input type="radio" id="other1" name="reply_to" value="other" <?php echo ($is_other) ? 'checked="checked"' : ''; ?> onclick="wdshow('reply_to_other')" />
										<label for="other1">Other</label>
									</div>
									<input type="text" style="width: <?php echo ($fields_count == 1) ? '250px' : '235px; margin-left: 15px'; ?>; display: <?php echo ($is_other) ? 'block;' : 'none;'; ?>" id="reply_to_other" name="reply_to_other" value="<?php echo ($is_other && $row->reply_to) ? $row->reply_to : ''; ?>" />
								</td>
							</tr>
							<tr valign="top">
								<td class="fm_options_label">
									<label> CC: </label>
								</td>
								<td class="fm_options_value">
									<input  type="text" id="mail_cc" name="mail_cc" value="<?php echo $row->mail_cc ?>" style="width:250px;" />
								</td>
							</tr>
							<tr valign="top">
								<td class="fm_options_label">
									<label> BCC: </label>
								</td>
								<td class="fm_options_value">
									<input type="text" id="mail_bcc" name="mail_bcc" value="<?php echo $row->mail_bcc ?>" style="width:250px;" />
								</td>
							</tr>
							<tr valign="top">
								<td class="fm_options_label">
									<label> Subject: </label>
								</td>
								<td class="fm_options_value">
									<input type="text" id="mail_subject" name="mail_subject" value="<?php echo $row->mail_subject ?>" style="width:250px;" />
									<img src="<?php echo WD_FMC_URL . '/images/add.png?ver='. get_option("wd_form_maker_version").''; ?>" onclick="document.getElementById('mail_subject_labels').style.display='block';" style="vertical-align: middle;cursor: pointer; display:inline-block; margin:0px; float:none;">
									<?php 
									$choise = "document.getElementById('mail_subject')";
									echo '<div style="position:relative; top:-3px;"><div id="mail_subject_labels" class="email_labels" style="display:none;">';							
									for($i=0; $i<count($label_label); $i++)	{ 			
										if($label_type[$i]=="type_submit_reset" || $label_type[$i]=="type_editor" || $label_type[$i]=="type_map" || $label_type[$i]=="type_mark_map" || $label_type[$i]=="type_captcha"|| $label_type[$i]=="type_recaptcha" || $label_type[$i]=="type_button" || $label_type[$i]=="type_file_upload" || $label_type[$i]=="type_send_copy" || $label_type[$i]=="type_matrix")			
										continue;		
										
										$param = htmlspecialchars(addslashes($label_label[$i]));			
										
										$fld_label = $param;
										if(strlen($fld_label)>30)
										{
											$fld_label = wordwrap(htmlspecialchars(addslashes($label_label[$i])), 30);
											$fld_label = explode("\n", $fld_label);
											$fld_label = $fld_label[0] . ' ...';	
										}
									
										echo "<a onClick=\"insertAtCursor(".$choise.",'".$param."'); document.getElementById('mail_subject_labels').style.display='none';\" style=\"display:block; text-decoration:none;\">".$fld_label."</a>";	

									}
									echo "<a onClick=\"insertAtCursor(".$choise.",'subid'); document.getElementById('mail_from_name_user_labels').style.display='none';\" style=\"display:block; text-decoration:none;\">Submission ID</a>";	

									echo "<a onClick=\"insertAtCursor(".$choise.",'username'); document.getElementById('mail_from_name_user_labels').style.display='none';\" style=\"display:block; text-decoration:none;\">Username</a>";
									echo '</div></div>';								
									?>
								</td>
							</tr>
							<tr valign="top">
								<td class="fm_options_label" style="vertical-align: middle;">
									<label> Mode: </label>
								</td>
								<td class="fm_options_value">
									<button name="mail_mode"class="fm-checkbox-radio-button <?php echo $row->mail_mode == 1 ? 'fm-text-yes' : 'fm-text-no' ?> medium" onclick="fm_change_radio_checkbox_text(this); return false;" value="<?php echo $row->mail_mode  ?>">
										<label><?php echo $row->mail_mode == 1 ? 'HTML' : 'Text' ?></label>
										<span></span>
									</button>
									<input type="hidden" name="mail_mode" value="<?php echo $row->mail_mode; ?>"/>
								</td>
							</tr>
							<tr valign="top">
								<td class="fm_options_label" style="vertical-align: middle;">
									<label> Attach File: </label>
								</td>
								<td class="fm_options_value">
									<div class="error_fm" style="padding: 5px; font-size: 14px;">File attach is disabled in free version.</div>
									<input type="hidden" name="mail_attachment" value="<?php echo $row->mail_attachment; ?>"/>
								</td>
							</tr>
							<tr valign="top">
								<td class="fm_options_label" style="vertical-align: middle;">
									<label> Email empty fields: </label>
								</td>
								<td class="fm_options_value">
									<button class="fm-checkbox-radio-button <?php echo $row->mail_emptyfields == 1 ? 'fm-yes' : 'fm-no' ?>" onclick="fm_change_radio(this); return false;" value="<?php echo $row->mail_emptyfields; ?>">
										<span></span>
									</button>
									<input type="hidden" name="mail_emptyfields" value="<?php echo $row->mail_emptyfields; ?>"/>
								</td>
							</tr>
							<tr>
								<td class="fm_options_label" valign="top">
									<label>Custom Text in Email For Administrator</label>
								</td>
								<td class="fm_options_value">
									<div style="margin-bottom:5px">
										<?php
										$choise = "document.getElementById('script_mail')";
										for ($i = 0; $i < count($label_label); $i++) {
											if ($label_type[$i]=="type_submit_reset" || $label_type[$i]=="type_editor" || $label_type[$i]=="type_map" || $label_type[$i]=="type_mark_map" || $label_type[$i]=="type_captcha"|| $label_type[$i]=="type_recaptcha" || $label_type[$i]=="type_button"  || $label_type[$i]=="type_send_copy")
											continue;
											
											$param = htmlspecialchars(addslashes($label_label[$i]));
											$fld_label = $param;
											if(strlen($fld_label)>30) {
												$fld_label = wordwrap(htmlspecialchars(addslashes($label_label[$i])), 30);
												$fld_label = explode("\n", $fld_label);
												$fld_label = $fld_label[0] . ' ...';	
											}
														
											if($label_type[$i]=="type_file_upload")
												$fld_label .= '(as image)';
											?>
											<input style="border: 1px solid silver; font-size: 10px;" type="button" value="<?php echo $fld_label; ?>" onClick="insertAtCursor(<?php echo $choise; ?>, '<?php echo $param; ?>')" />
											<?php
										}
										?>
										<input style="border: 1px solid silver; font-size: 10px; margin: 3px;" type="button" value="Submission ID" onClick="insertAtCursor(<?php echo $choise; ?>,'subid')" />
										<input style="border: 1px solid silver; font-size: 10px; margin: 3px;" type="button" value="Ip" onClick="insertAtCursor(<?php echo $choise; ?>,'ip')" />
										<input style="border: 1px solid silver; font-size: 10px; margin: 3px;" type="button" value="Username" onClick="insertAtCursor(<?php echo $choise; ?>,'username')" />
										<input style="border: 1px solid silver; font-size: 10px; margin: 3px;" type="button" value="User Email" onClick="insertAtCursor(<?php echo $choise; ?>,'useremail')" />
										<input style="border: 1px solid silver; font-size: 10px; margin: 3px;" type="button" value="All fields list" onClick="insertAtCursor(<?php echo $choise; ?>, 'all')" />
									</div>
									<?php
									if (user_can_richedit()) {
										wp_editor($row->script_mail, 'script_mail', array('teeny' => FALSE, 'textarea_name' => 'script_mail', 'media_buttons' => FALSE, 'textarea_rows' => 5));
									}
									else {
										?>
										<textarea name="script_mail" id="script_mail" cols="20" rows="10" style="width:300px; height:450px;"><?php echo $row->script_mail; ?></textarea>
										<?php
									}
									?>
								</td>
							</tr>
						</table>
					</fieldset>
					<fieldset class="fm_mail_options">
						<legend>Email to User</legend>
						<table class="admintable">
							<tr valign="top">
								<td class="fm_options_label">
									<label for="mail">Send to</label>
								</td>
								<td class="fm_options_value">
									<?php 
										$fields = explode('*:*id*:*type_submitter_mail*:*type*:*', $row->form_fields);
										$fields_count = count($fields);
										if ($fields_count == 1) { ?>
											There is no email field
											<?php
										}
										else {
											for ($i = 0; $i < $fields_count - 1; $i++) {
												?>
												<div>
													<input type="checkbox" name="send_to<?php echo $i; ?>" id="send_to<?php echo $i; ?>" value="<?php echo (strlen($fields[$i])!=1 ? substr($fields[$i], strrpos($fields[$i], '*:*new_field*:*')+15, strlen($fields[$i])) : $fields[$i]); ?>"  <?php echo (is_numeric(strpos($row->send_to, '*'.(strlen($fields[$i])!=1 ? substr($fields[$i], strrpos($fields[$i], '*:*new_field*:*')+15, strlen($fields[$i])) : $fields[$i]).'*')) ? 'checked="checked"' : '' ); ?> style="margin: 0px 5px 0px 0px;" />
													<label for="send_to<?php echo $i; ?>"><?php echo substr($fields[$i + 1], 0, strpos($fields[$i + 1], '*:*w_field_label*:*')); ?></label>
												</div>
												<?php
											}
										}
									?>
								</td>
							</tr>
							<tr valign="top">
								<td class="fm_options_label">
									<label for="mail_from_user">Email From</label>
								</td>
								<td class="fm_options_value">
									<input type="text" id="mail_from_user" name="mail_from_user" value="<?php echo $row->mail_from_user; ?>" style="width: 250px;" />
								</td>
							</tr>
							<tr valign="top">
								<td class="fm_options_label">
									<label for="mail_from_name_user">From Name</label>
								</td>
								<td class="fm_options_value">
									<input type="text" id="mail_from_name_user" name="mail_from_name_user" value="<?php echo $row->mail_from_name_user; ?>" style="width: 250px;"/>
									<img src="<?php echo WD_FMC_URL . '/images/add.png?ver='. get_option("wd_form_maker_version").''; ?>" onclick="document.getElementById('mail_from_name_user_labels').style.display='block';" style="vertical-align: middle;cursor: pointer; display:inline-block; margin:0px; float:none;">
									<?php 
									$choise = "document.getElementById('mail_from_name_user')";
									echo '<div style="position:relative; top:-3px;"><div id="mail_from_name_user_labels" class="email_labels" style="display:none;">';							
									for($i=0; $i<count($label_label); $i++)	{ 			
										if($label_type[$i]=="type_submit_reset" || $label_type[$i]=="type_editor" || $label_type[$i]=="type_map" || $label_type[$i]=="type_mark_map" || $label_type[$i]=="type_captcha"|| $label_type[$i]=="type_recaptcha" || $label_type[$i]=="type_button" || $label_type[$i]=="type_file_upload" || $label_type[$i]=="type_send_copy")			
											continue;		
								  
										$param = htmlspecialchars(addslashes($label_label[$i]));			
										$fld_label = $param;
										if(strlen($fld_label)>30) {
											$fld_label = wordwrap(htmlspecialchars(addslashes($label_label[$i])), 30);
											$fld_label = explode("\n", $fld_label);
											$fld_label = $fld_label[0] . ' ...';	
										}
								
										echo "<a onClick=\"insertAtCursor(".$choise.",'".$param."'); document.getElementById('mail_from_name_user_labels').style.display='none';\" style=\"display:block; text-decoration:none;\">".$fld_label."</a>";	
									}
									echo "<a onClick=\"insertAtCursor(".$choise.",'subid'); document.getElementById('mail_from_name_user_labels').style.display='none';\" style=\"display:block; text-decoration:none;\">Submission ID</a>";	
									echo "<a onClick=\"insertAtCursor(".$choise.",'username'); document.getElementById('mail_from_name_user_labels').style.display='none';\" style=\"display:block; text-decoration:none;\">Username</a>";
									echo '</div></div>';								
								?>
								</td>
							</tr>
							<tr valign="top">
								<td class="fm_options_label">
									<label for="reply_to_user">Reply to<br />(if different from "Email Form")</label>
								</td>
								<td class="fm_options_value">
									<input type="text" id="reply_to_user" name="reply_to_user" value="<?php echo $row->reply_to_user; ?>" style="width:250px;"/>
								</td>
							</tr>
							<tr valign="top">
								<td class="fm_options_label">
									<label> CC: </label>
								</td>
								<td class="fm_options_value">
									<input type="text" id="mail_cc_user" name="mail_cc_user" value="<?php echo $row->mail_cc_user ?>" style="width:250px;" />
								</td>
							</tr>
							<tr valign="top">
								<td class="fm_options_label">
									<label> BCC: </label>
								</td>
								<td class="fm_options_value">
									<input type="text" id="mail_bcc_user" name="mail_bcc_user" value="<?php echo $row->mail_bcc_user ?>" style="width:250px;" />
								</td>
							</tr>
							<tr valign="top">
								<td class="fm_options_label">
									<label> Subject: </label>
								</td>
								<td class="fm_options_value">
									<input type="text" id="mail_subject_user" name="mail_subject_user" value="<?php echo $row->mail_subject_user ?>" style="width:250px;" />
									<img src="<?php echo WD_FMC_URL . '/images/add.png?ver='. get_option("wd_form_maker_version").''; ?>" onclick="document.getElementById('mail_subject_user_labels').style.display='block';" style="vertical-align: middle; cursor: pointer; display:inline-block; margin:0px; float:none;">
									<?php 
									$choise = "document.getElementById('mail_subject_user')";
									echo '<div style="position:relative; top:-3px;"><div id="mail_subject_user_labels" class="email_labels" style="display:none;">';							
									for($i=0; $i<count($label_label); $i++)			
									{ 			
										if($label_type[$i]=="type_submit_reset" || $label_type[$i]=="type_editor" || $label_type[$i]=="type_map" || $label_type[$i]=="type_mark_map" || $label_type[$i]=="type_captcha"|| $label_type[$i]=="type_recaptcha" || $label_type[$i]=="type_button" || $label_type[$i]=="type_file_upload" || $label_type[$i]=="type_send_copy")			
										continue;		
										
										$param = htmlspecialchars(addslashes($label_label[$i]));			
										
										$fld_label = $param;
										if(strlen($fld_label)>30)
										{
											$fld_label = wordwrap(htmlspecialchars(addslashes($label_label[$i])), 30);
											$fld_label = explode("\n", $fld_label);
											$fld_label = $fld_label[0] . ' ...';	
										}
									
										echo "<a onClick=\"insertAtCursor(".$choise.",'".$param."'); document.getElementById('mail_subject_user_labels').style.display='none';\" style=\"display:block; text-decoration:none;\">".$fld_label."</a>";	

									}
									echo "<a onClick=\"insertAtCursor(".$choise.",'subid'); document.getElementById('mail_from_name_user_labels').style.display='none';\" style=\"display:block; text-decoration:none;\">Submission ID</a>";	

									echo "<a onClick=\"insertAtCursor(".$choise.",'username'); 			document.getElementById('mail_from_name_user_labels').style.display='none';\" style=\"display:block; text-decoration:none;\">Username</a>";
									echo '</div></div>';								
									?>
								</td>
							</tr>
							<tr valign="top">
								<td class="fm_options_label" style="vertical-align: middle;">
									<label> Mode: </label>
								</td>
								<td class="fm_options_value">
									<button name="mail_mode_user" class="fm-checkbox-radio-button <?php echo $row->mail_mode_user == 1 ? 'fm-text-yes' : 'fm-text-no' ?> medium" onclick="fm_change_radio_checkbox_text(this); return false;" value="<?php echo $row->mail_mode_user == 1 ? '1' : '0' ?>">
										<label><?php echo $row->mail_mode_user == 1 ? 'HTML' : 'Text' ?></label>
										<span></span>
									</button>
									<input type="hidden" name="mail_mode_user" value="<?php echo $row->mail_mode_user; ?>"/>
								</td>
							</tr>
							<tr valign="top">
								<td class="fm_options_label" style="vertical-align: middle;">
									<label> Attach File: </label>
								</td>
								<td class="fm_options_value">
									<div class="error_fm" style="padding: 5px; font-size: 14px;">File attach is disabled in free version.</div>
									<input type="hidden" name="mail_attachment_user" value="<?php echo $row->mail_attachment_user; ?>"/>
								</td>
							</tr>
							<tr valign="top">
								<td class="fm_options_label" style="vertical-align: middle;">
									<label> Email verification: </label>
								</td>
								<td class="fm_options_value">
									<button name="mail_verify" class="fm-checkbox-radio-button <?php echo $row->mail_verify == 1 ? 'fm-yes' : 'fm-no' ?>" onclick="fm_change_radio(this); return false;" value="<?php echo $row->mail_verify; ?>">
										<span></span>
									</button>
									<input type="hidden" name="mail_verify" value="<?php echo $row->mail_verify; ?>"/>
								</td>
							</tr>
							<tr valign="top" class="expire_link" <?php echo ($row->mail_verify==0 ? 'style="display:none;"' : '')?>>
								<td class="fm_options_label" valign="top">
									<label> Verification link expires in: </label>
								</td>
								<td class="fm_options_value">
									<input class="inputbox" type="text" name="mail_verify_expiretime" maxlength="10"  value = "<?php echo ($row->mail_verify_expiretime ? $row->mail_verify_expiretime : 0); ?>" style="width:95px;" onkeypress="return check_isnum_point(event)"/><small> -- hours (0 - never expires).</small>
								</td>
							</tr>
							<tr>
								<td class="fm_options_label" valign="top">
									<label>Custom Text in Email For User</label>
								</td>
								<td class="fm_options_value">
									<div style="margin-bottom:5px">
										<?php
										$choise = "document.getElementById('script_mail_user')";
										for ($i = 0; $i < count($label_label); $i++) {
											if ($label_type[$i] == "type_submit_reset" || $label_type[$i] == "type_editor" || $label_type[$i] == "type_map" || $label_type[$i] == "type_mark_map" || $label_type[$i] == "type_captcha" || $label_type[$i] == "type_recaptcha" || $label_type[$i] == "type_button" || $label_type[$i] == "type_file_upload" || $label_type[$i] == "type_send_copy") 
											continue;
											
											$param = htmlspecialchars(addslashes($label_label[$i]));
											$fld_label = $param;
											if(strlen($fld_label)>30) {
												$fld_label = wordwrap(htmlspecialchars(addslashes($label_label[$i])), 30);
												$fld_label = explode("\n", $fld_label);
												$fld_label = $fld_label[0] . ' ...';	
											}
											if($label_type[$i]=="type_file_upload")
												$fld_label .= '(as image)';
											?>
											<input style="border: 1px solid silver; font-size: 10px;" type="button" value="<?php echo $fld_label; ?>" onClick="insertAtCursor(<?php echo $choise; ?>, '<?php echo $param; ?>')" />
											<?php
										}
										?>
										<input style="border: 1px solid silver; font-size: 10px; margin: 3px;" type="button" value="Submission ID" onClick="insertAtCursor(<?php echo $choise; ?>,'subid')" />
										<input style="border: 1px solid silver; font-size: 10px; margin: 3px;" type="button" value="Ip" onClick="insertAtCursor(<?php echo $choise; ?>,'ip')" />
										<input style="border: 1px solid silver; font-size: 10px; margin: 3px;" type="button" value="Username" onClick="insertAtCursor(<?php echo $choise; ?>,'username')" />
										<input style="border: 1px solid silver; font-size: 10px; margin: 3px;" type="button" value="User Email" onClick="insertAtCursor(<?php echo $choise; ?>,'useremail')" />
										<input style="border: 1px solid silver; font-size: 10px; margin:3px;" type="button" value="All fields list" onClick="insertAtCursor(<?php echo $choise; ?>, 'all')" />
										<div class="verification_div" <?php echo ($row->mail_verify==0 ? 'style="display:none;"' : '')?>><input style="border: 1px solid silver; font-size: 10px; margin:3px;" type="button" value="Verification link" onClick="insertAtCursor(<?php echo $choise; ?>,'Verification link')" /> </div>
									</div>
									<?php
									if (user_can_richedit()) {
										wp_editor($row->script_mail_user, 'script_mail_user', array('teeny' => FALSE, 'textarea_name' => 'script_mail_user', 'media_buttons' => FALSE, 'textarea_rows' => 5));
									}
									else {
										?>
										<textarea name="script_mail_user" id="script_mail_user" cols="20" rows="10" style="width:300px; height:450px;"><?php echo $row->script_mail_user; ?></textarea>
										<?php
									}
									?>
								</td>
							</tr>
						</table>
					</fieldset>
				</fieldset>
				<fieldset id="actions_fieldset" class="adminform fm_fieldset_deactive">
					<legend>Actions after submission</legend>
					<table class="admintable">
						<tr valign="top">
							<td class="fm_options_label">
								<label>Action type</label>
							</td>
							<td class="fm_options_value">
								<div>
									<input type="radio" name="submit_text_type" id="text_type_none" onclick="set_type('none')" value="1" <?php echo ($row->submit_text_type != 2 && $row->submit_text_type != 3 && $row->submit_text_type != 4 && $row->submit_text_type != 5) ? "checked" : ""; ?> />
									<label for="text_type_none">Stay on Form</label>
								</div>
								<div>
									<input type="radio" name="submit_text_type" id="text_type_post" onclick="set_type('post')" value="2" <?php echo ($row->submit_text_type == 2) ? "checked" : ""; ?> />
									<label for="text_type_post">Post</label>
								</div>
								<div>
									<input type="radio" name="submit_text_type" id="text_type_page" onclick="set_type('page')" value="5" <?php echo ($row->submit_text_type == 5) ? "checked" : ""; ?> />
									<label for="text_type_page">Page</label>
								</div>
								<div>
									<input type="radio" name="submit_text_type" id="text_type_custom_text" onclick="set_type('custom_text')" value="3" <?php echo ($row->submit_text_type == 3 ) ? "checked" : ""; ?> />
									<label for="text_type_custom_text">Custom Text</label>
								</div>
								<div>
									<input type="radio" name="submit_text_type" id="text_type_url" onclick="set_type('url')" value="4" <?php echo ($row->submit_text_type == 4) ? "checked" : ""; ?> />
									<label for="text_type_url">URL</label>
								</div>
							</td>
						</tr>
						<tr id="none" <?php echo (($row->submit_text_type == 2 || $row->submit_text_type == 3 || $row->submit_text_type == 4 || $row->submit_text_type == 5) ? 'style="display:none"' : ''); ?>>
							<td class="fm_options_label">
								<label>Stay on Form</label>
							</td>
							<td class="fm_options_value">
								<img src="<?php echo WD_FMC_URL . '/images/verified.png'; ?>" border="0">
							</td>
						</tr>
						<tr id="post" <?php echo (($row->submit_text_type != 2) ? 'style="display:none"' : ''); ?>>
							<td class="fm_options_label">
								<label for="post_name">Post</label>
							</td>
							<td class="fm_options_value">
								<select id="post_name" name="post_name">
									<option value="0">- Select Post -</option>
									<?php
									$args = array('posts_per_page'  => 10000);
									query_posts($args);
									while (have_posts()) : the_post(); ?>
									<option value="<?php $x = get_permalink(get_the_ID()); echo $x; ?>" <?php echo (($row->article_id == $x) ? 'selected="selected"' : ''); ?>><?php the_title(); ?></option>
									<?php
									endwhile;
									wp_reset_query();
									?>
								</select>
							</td>
						</tr>
						<tr id="page" <?php echo (($row->submit_text_type != 5) ? 'style="display:none"' : ''); ?>>
							<td class="fm_options_label">
								<label for="page_name">Page</label>
							</td>
							<td class="fm_options_value">
								<select id="page_name" name="page_name">
									<option value="0">- Select Page -</option>
									<?php
									$pages = get_pages();
									foreach ($pages as $page) {
										$page_id = get_page_link($page->ID);
										?>
										<option value="<?php echo $page_id; ?>" <?php echo (($row->article_id == $page_id) ? 'selected="selected"' : ''); ?>><?php echo $page->post_title; ?></option>
										<?php
									}
									wp_reset_query();
									?>
								</select>
							</td>
						</tr>
						<tr id="custom_text" <?php echo (($row->submit_text_type != 3) ? 'style="display: none;"' : ''); ?>>
							<td class="fm_options_label">
								<label for="submit_text">Text</label>
							</td>
							<td class="fm_options_value">
								<?php $choise = "document.getElementById('submit_text')"; 
								for ($i = 0; $i < count($label_label); $i++) {
									if ($label_type[$i]=="type_submit_reset" || $label_type[$i]=="type_editor" || $label_type[$i]=="type_map" || $label_type[$i]=="type_mark_map" || $label_type[$i]=="type_captcha"|| $label_type[$i]=="type_recaptcha" || $label_type[$i]=="type_button"  || $label_type[$i]=="type_send_copy" || $label_type[$i]=="type_file_upload")
									  continue;
									
									$param = htmlspecialchars(addslashes($label_label[$i]));
									$fld_label = $param;
									if(strlen($fld_label)>30) {
										$fld_label = wordwrap(htmlspecialchars(addslashes($label_label[$i])), 30);
										$fld_label = explode("\n", $fld_label);
										$fld_label = $fld_label[0] . ' ...';	
									}

									?>
									<input style="border: 1px solid silver; font-size: 10px;" type="button" value="<?php echo $fld_label; ?>" onClick="insertAtCursor(<?php echo $choise; ?>, '<?php echo $param; ?>')" />
									<?php
								}
								?>
								<input style="border: 1px solid silver; font-size: 10px; margin: 3px;" type="button" value="Submission ID" onClick="insertAtCursor(<?php echo $choise; ?>,'subid')" />
								<input style="border: 1px solid silver; font-size: 10px; margin: 3px;" type="button" value="Ip" onClick="insertAtCursor(<?php echo $choise; ?>,'ip')" />
								<input style="border: 1px solid silver; font-size: 10px; margin:3px;" type="button" value="User Id" onClick="insertAtCursor(<?php echo $choise; ?>, 'userid')" />
								<input style="border: 1px solid silver; font-size: 10px; margin: 3px;" type="button" value="Username" onClick="insertAtCursor(<?php echo $choise; ?>,'username')" />
								<input style="border: 1px solid silver; font-size: 10px; margin: 3px;" type="button" value="User Email" onClick="insertAtCursor(<?php echo $choise; ?>,'useremail')" />
								<?php
								if (user_can_richedit()) {
									wp_editor($row->submit_text, 'submit_text', array('teeny' => FALSE, 'textarea_name' => 'submit_text', 'media_buttons' => FALSE, 'textarea_rows' => 5));
								}
									else {
										?>
										<textarea cols="36" rows="5" id="submit_text" name="submit_text" style="resize: vertical;">
											<?php echo $row->submit_text; ?>
										</textarea>
										<?php
									}
									?>
								</td>
							</tr>
						<tr id="url" <?php echo (($row->submit_text_type != 4 ) ? 'style="display:none"' : ''); ?>>
							<td class="fm_options_label">
								<label for="url">URL</label>
							</td>
							<td class="fm_options_value">
								<input type="text" id="url" name="url" style="width:300px" value="<?php echo $row->url; ?>" />
							</td>
						</tr>
					</table>
				</fieldset>
				<fieldset id="payment_fieldset" class="adminform fm_fieldset_deactive">
					<legend>Payment Options</legend>
					<table class="admintable">
						<tr>
							<td colspan="2">
								<div class="error_fm" style="padding: 5px; font-size: 14px;">Paypal Options are disabled in free version.</div>
							</td>
						</tr>
						<tr valign="top">
							<td class="fm_options_label">
								<label>Turn Paypal On</label>
							</td>
							<td class="fm_options_value">
								<button name="paypal_mode" class="fm-checkbox-radio-button <?php echo $row->paypal_mode == 1 ? 'fm-text-yes' : 'fm-text-no' ?> small" onclick="fm_change_radio_checkbox_text(this); return false;" value="<?php echo $row->paypal_mode == 1 ? '1' : '0' ?>" disabled="disabled">
									<label><?php echo $row->paypal_mode == 1 ? 'On' : 'Off' ?></label>
									<span></span>
								</button>
								<input type="hidden" name="paypal_mode" value="<?php echo $row->paypal_mode; ?>"/>
							</td>
						</tr>
						<tr valign="top">
							<td class="fm_options_label">
								<label>Checkout Mode</label>
							</td>
							<td class="fm_options_value">
								<button name="checkout_mode" class="fm-checkbox-radio-button <?php echo $row->checkout_mode == 1 ? 'fm-text-yes' : 'fm-text-no' ?> large" onclick="fm_change_radio_checkbox_text(this); return false;" value="<?php echo $row->checkout_mode == 1 ? '1' : '0' ?>" disabled="disabled">
									<label><?php echo $row->checkout_mode == 1 ? 'Production' : 'Testmode' ?></label>
									<span></span>
								</button>
								<input type="hidden" name="checkout_mode" value="<?php echo $row->checkout_mode; ?>"/>
							</td>
						</tr>
						<tr valign="top">
							<td class="fm_options_label">
								<label for="paypal_email">Paypal email</label>
							</td>
							<td class="fm_options_value">
								<input type="text" name="paypal_email" id="paypal_email" value="<?php echo $row->paypal_email; ?>" class="text_area" style="width:250px" disabled="disabled">
							</td>
						</tr>
						<tr valign="top">
							<td class="fm_options_label">
								<label for="payment_currency">Payment Currency</label>
							</td>
							<td class="fm_options_value">
								<select id="payment_currency" name="payment_currency" disabled="disabled">
									<option value="USD" <?php echo (($row->payment_currency == 'USD') ? 'selected' : ''); ?>>$ &#8226; U.S. Dollar</option>
									<option value="EUR" <?php echo (($row->payment_currency == 'EUR') ? 'selected' : ''); ?>>&#8364; &#8226; Euro</option>
									<option value="GBP" <?php echo (($row->payment_currency == 'GBP') ? 'selected' : ''); ?>>&#163; &#8226; Pound Sterling</option>
									<option value="JPY" <?php echo (($row->payment_currency == 'JPY') ? 'selected' : ''); ?>>&#165; &#8226; Japanese Yen</option>
									<option value="CAD" <?php echo (($row->payment_currency == 'CAD') ? 'selected' : ''); ?>>C$ &#8226; Canadian Dollar</option>
									<option value="MXN" <?php echo (($row->payment_currency == 'MXN') ? 'selected' : ''); ?>>Mex$ &#8226; Mexican Peso</option>
									<option value="HKD" <?php echo (($row->payment_currency == 'HKD') ? 'selected' : ''); ?>>HK$ &#8226; Hong Kong Dollar</option>
									<option value="HUF" <?php echo (($row->payment_currency == 'HUF') ? 'selected' : ''); ?>>Ft &#8226; Hungarian Forint</option>
									<option value="NOK" <?php echo (($row->payment_currency == 'NOK') ? 'selected' : ''); ?>>kr &#8226; Norwegian Kroner</option>
									<option value="NZD" <?php echo (($row->payment_currency == 'NZD') ? 'selected' : ''); ?>>NZ$ &#8226; New Zealand Dollar</option>
									<option value="SGD" <?php echo (($row->payment_currency == 'SGD') ? 'selected' : ''); ?>>S$ &#8226; Singapore Dollar</option>
									<option value="SEK" <?php echo (($row->payment_currency == 'SEK') ? 'selected' : ''); ?>>kr &#8226; Swedish Kronor</option>
									<option value="PLN" <?php echo (($row->payment_currency == 'PLN') ? 'selected' : ''); ?>>zl &#8226; Polish Zloty</option>
									<option value="AUD" <?php echo (($row->payment_currency == 'AUD') ? 'selected' : ''); ?>>A$ &#8226; Australian Dollar</option>
									<option value="DKK" <?php echo (($row->payment_currency == 'DKK') ? 'selected' : ''); ?>>kr &#8226; Danish Kroner</option>
									<option value="CHF" <?php echo (($row->payment_currency == 'CHF') ? 'selected' : ''); ?>>CHF &#8226; Swiss Francs</option>
									<option value="CZK" <?php echo (($row->payment_currency == 'CZK') ? 'selected' : ''); ?>>Kc &#8226; Czech Koruny</option>
									<option value="ILS" <?php echo (($row->payment_currency == 'ILS') ? 'selected' : ''); ?>>&#8362; &#8226; Israeli Sheqel</option>
									<option value="BRL" <?php echo (($row->payment_currency == 'BRL') ? 'selected' : ''); ?>>R$ &#8226; Brazilian Real</option>
									<option value="TWD" <?php echo (($row->payment_currency == 'TWD') ? 'selected' : ''); ?>>NT$ &#8226; Taiwan New Dollars</option>
									<option value="MYR" <?php echo (($row->payment_currency == 'MYR') ? 'selected' : ''); ?>>RM &#8226; Malaysian Ringgit</option>
									<option value="PHP" <?php echo (($row->payment_currency == 'PHP') ? 'selected' : ''); ?>>&#8369; &#8226; Philippine Peso</option>
									<option value="THB" <?php echo (($row->payment_currency == 'THB') ? 'selected' : ''); ?>>&#xe3f; &#8226; Thai Bahtv</option>
								</select>
							</td>
						</tr>
						<tr valign="top">
							<td class="fm_options_label">
								<label for="tax">Tax</label>
							</td>
							<td class="fm_options_value">
								<input type="text" name="tax" id="tax" value="<?php echo $row->tax; ?>" class="text_area" style="width: 40px;" onKeyPress="return check_isnum_point(event)" disabled="disabled"> %
							</td>
						</tr>
					</table>
				</fieldset>
				<fieldset id="javascript_fieldset" class="adminform fm_fieldset_deactive">
					<legend>JavaScript</legend>
					<table class="admintable">
						<tr valign="top">
							<td class="fm_options_label">
								<label for="javascript">Javascript</label>
							</td>
							<td class="fm_options_value" style="width:650px;">
								<textarea style="margin: 0px; height: 400px; width: 600px;" cols="60" rows="30" name="javascript" id="form_javascript"><?php echo $row->javascript; ?></textarea>
							</td>
						</tr>
					</table>
				</fieldset>
				<fieldset id="conditions_fieldset" class="adminform fm_fieldset_deactive">
					<?php 	
					$ids = array();
					$types = array();
					$labels = array();
					$paramss = array();
					$all_ids = array();
					$all_labels = array();

					$select_and_input = array("type_text", "type_password", "type_textarea", "type_name", "type_number", "type_phone", "type_submitter_mail", "type_address", "type_spinner", "type_checkbox", "type_radio", "type_own_select", "type_paypal_price", "type_paypal_select", "type_paypal_checkbox", "type_paypal_radio", "type_paypal_shipping");
					$select_type_fields = array("type_address", "type_checkbox", "type_radio", "type_own_select", "type_paypal_select", "type_paypal_checkbox", "type_paypal_radio", "type_paypal_shipping");
		
					$fields=explode('*:*new_field*:*',$row->form_fields);
					$fields 	= array_slice($fields,0, count($fields)-1);   
					foreach($fields as $field) {
						$temp=explode('*:*id*:*',$field);
						array_push($ids, $temp[0]);
						array_push($all_ids, $temp[0]);
						$temp=explode('*:*type*:*',$temp[1]);
						array_push($types, $temp[0]);
						$temp=explode('*:*w_field_label*:*',$temp[1]);
						array_push($labels, $temp[0]);
						array_push($all_labels, $temp[0]);
						array_push($paramss, $temp[1]);

					}
					
					foreach($types as $key=>$value){
						if(!in_array($types[$key],$select_and_input)){					
							unset($ids[$key]);						
							unset($labels[$key]);					
							unset($types[$key]);
							unset($paramss[$key]);					
						}
					}	

					$ids = array_values($ids);
					$labels = array_values($labels);
					$types = array_values($types);
					$paramss = array_values($paramss);
					
					$chose_ids = implode('@@**@@',$ids);
					$chose_labels = implode('@@**@@',$labels);
					$chose_types = implode('@@**@@',$types);
					$chose_paramss = implode('@@**@@',$paramss);
						
					$all_ids_cond = implode('@@**@@',$all_ids);
					$all_labels_cond = implode('@@**@@',$all_labels);

					$show_hide	= array();
					$field_label	= array();
					$all_any 	= array();
					$condition_params 	= array();
		
					$count_of_conditions=0;
					if($row->condition!="") {
						$conditions=explode('*:*new_condition*:*',$row->condition);
						$conditions 	= array_slice($conditions,0, count($conditions)-1); 
						$count_of_conditions = count($conditions);					

						foreach($conditions as $condition) {
							$temp=explode('*:*show_hide*:*',$condition);
							array_push($show_hide, $temp[0]);
							$temp=explode('*:*field_label*:*',$temp[1]);
							array_push($field_label, $temp[0]);
							$temp=explode('*:*all_any*:*',$temp[1]);
							array_push($all_any, $temp[0]);
							array_push($condition_params, $temp[1]);
						}
					}
					else {
						$show_hide[0]=1;
						$all_any[0]='and';
						$condition_params[0]='';
						if($all_ids)
						$field_label[0] = $all_ids[0];
					}
					?>
					<div>
						<button class="fm-button add-button large" onclick="add_condition('<?php echo $chose_ids; ?>', '<?php echo htmlspecialchars(addslashes($chose_labels)); ?>', '<?php echo $chose_types; ?>', '<?php echo htmlspecialchars(addslashes($chose_paramss)); ?>', '<?php echo $all_ids_cond; ?>', '<?php echo htmlspecialchars(addslashes($all_labels_cond)); ?>'); return false;">
							Add Condition
							<span></span>
						</button>
					</div>
						<?php
						for($k=0; $k<$count_of_conditions; $k++) {	
							if(in_array($field_label[$k],$all_ids)) : ?>
								<div id="condition<?php echo $k; ?>" class="fm-condition">
									<div id="conditional_fileds<?php echo $k; ?>">
										<select id="show_hide<?php echo $k; ?>" name="show_hide<?php echo $k; ?>" style="width:80px; ">
											<option value="1" <?php if($show_hide[$k]==1) echo 'selected="selected"'; ?>>show</option>
											<option value="0" <?php if($show_hide[$k]==0) echo 'selected="selected"'; ?>>hide</option>
										</select> 
										<select id="fields<?php echo $k; ?>" name="fields<?php echo $k; ?>" style="width:300px; " onChange="" >
											<?php 
											foreach($all_labels as $key => $value) { 	
												if($field_label[$k]==$all_ids[$key])
													$selected = 'selected="selected"';
												else
													$selected ='';
												echo '<option value="'.$all_ids[$key].'" '.$selected.'>'.$value.'</option>';
											}
											?>
										</select> 
										<span>if</span>
										<select id="all_any<?php echo $k; ?>" name="all_any<?php echo $k; ?>" style="width:60px; ">
											<option value="and" <?php if($all_any[$k]=="and") echo 'selected="selected"'; ?>>all</option>
											<option value="or" <?php if($all_any[$k]=="or") echo 'selected="selected"'; ?>>any</option>
										</select> 
										<span>of the following match:</span>	
										<img src="<?php echo WD_FMC_URL . '/images/add.png?ver='. get_option("wd_form_maker_version").''; ?>" title="add" onclick="add_condition_fields(<?php echo $k; ?>,'<?php echo $chose_ids; ?>', '<?php echo htmlspecialchars(addslashes($chose_labels)); ?>', '<?php echo $chose_types; ?>', '<?php echo htmlspecialchars(addslashes($chose_paramss)); ?>')" style="cursor: pointer; vertical-align: middle;">
										<img src="<?php echo WD_FMC_URL . '/images/page_delete.png?ver='. get_option("wd_form_maker_version").''; ?>" onclick="delete_condition('<?php echo $k; ?>')" style="cursor: pointer; vertical-align: middle;">
									</div>
								<?php 
								if($condition_params[$k]) {
									$_params = explode('*:*next_condition*:*',$condition_params[$k]);
									$_params = array_slice($_params,0, count($_params)-1); 
								
										foreach($_params as $key=>$_param) {
											$key_select_or_input ='';
											$param_values = explode('***',$_param);
											$multiselect = explode('@@@',$param_values[2]);
										
											if(in_array($param_values[0],$ids)): ?>
											<div id="condition_div<?php echo $k; ?>_<?php echo $key; ?>">
											<select id="field_labels<?php echo $k; ?>_<?php echo $key; ?>" onchange="change_choices(this.options[this.selectedIndex].id+'_<?php echo $key; ?>','<?php echo $chose_ids; ?>', '<?php echo $chose_types; ?>', '<?php echo htmlspecialchars(addslashes($chose_paramss)); ?>')" style="width:300px;"/>
												<?php 
												foreach($labels as $key1 => $value) 		
												{ 		
													if($param_values[0]==$ids[$key1])
													{
														$selected = 'selected="selected"';
														if ($types[$key1]=="type_checkbox" || $types[$key1]=="type_paypal_checkbox")
															$multiple = 'multiple="multiple" class="multiple_select"';
														else
															$multiple ='';
														
														$key_select_or_input = $key1;
													}	
													else
														$selected ='';
													if($field_label[$k]!=$ids[$key1])
													echo '<option id="'.$k.'_'.$key1.'" value="'.$ids[$key1].'" '.$selected.'>'.$value.'</option>';
											
												}
									
												?>	
											</select>
											
											<select id="is_select<?php echo $k; ?>_<?php echo $key; ?>" style="vertical-align: top; width:94px;">
											<option value="==" <?php if($param_values[1]=="==") echo 'selected="selected"'; ?>>is</option>
											<option value="!=" <?php if($param_values[1]=="!=") echo 'selected="selected"'; ?>>is not</option>
											<option value="%" <?php if($param_values[1]=="%") echo 'selected="selected"'; ?>>like</option>

											<option value="!%" <?php if($param_values[1]=="!%") echo 'selected="selected"'; ?>>not like</option>

											<option value="=" <?php if($param_values[1]=="=") echo 'selected="selected"'; ?>>empty</option>

											<option value="!" <?php if($param_values[1]=="!") echo 'selected="selected"'; ?>>not empty</option>

											</select>
											
											<?php if ($key_select_or_input !== '' && in_array($types[$key_select_or_input],$select_type_fields)) : ?>
											<select id="field_value<?php echo $k; ?>_<?php echo $key; ?>" <?php echo $multiple; ?> style="width: 200px;">
											<?php  
											switch($types[$key_select_or_input])
											{
												case "type_own_select":
												case "type_paypal_select":
													$w_size = explode('*:*w_size*:*',$paramss[$key_select_or_input]);	
												break;
												
												case "type_radio":
												case "type_checkbox":
												case "type_paypal_radio":
												case "type_paypal_checkbox":
												case "type_paypal_shipping":
													$w_size = explode('*:*w_flow*:*',$paramss[$key_select_or_input]);	
												break;	
											}	
											
												$w_choices = explode('*:*w_choices*:*',$w_size[1]);
												$w_choices_array = explode('***',$w_choices[0]);
												
												$w_choices_price = explode('*:*w_choices_price*:*',$w_choices[1]);
												$w_choices_price_array = explode('***',$w_choices_price[0]);
													
												for($m=0; $m<count($w_choices_array); $m++)	
												{
													if($types[$key_select_or_input]=="type_paypal_checkbox" || $types[$key_select_or_input]=="type_paypal_radio" || $types[$key_select_or_input]=="type_paypal_shipping" || $types[$key_select_or_input]=="type_paypal_select")
														$w_choice = $w_choices_array[$m].'*:*value*:*'.$w_choices_price_array[$m];
													else
														$w_choice = $w_choices_array[$m];
														
													if(in_array(esc_html($w_choice),$multiselect))
													{
														$selected = 'selected="selected"';
													}	
													else
														$selected ='';

								if(strpos($w_choices_array[$m], '[') === false && strpos($w_choices_array[$m], ']') === false && strpos($w_choices_array[$m], ':') === false) {
													echo '<option id="choise_'.$k.'_'.$m.'" value="'.$w_choice.'" '.$selected.'>'.$w_choices_array[$m].'</option>';
								}
										}
										
										if($types[$key_select_or_input]=="type_address")
										{
											$w_countries = array("","Afghanistan","Albania","Algeria","Andorra","Angola","Antigua and Barbuda","Argentina","Armenia","Australia","Austria","Azerbaijan","Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bhutan","Bolivia","Bosnia and Herzegovina","Botswana","Brazil","Brunei","Bulgaria","Burkina Faso","Burundi","Cambodia","Cameroon","Canada","Cape Verde","Central African Republic","Chad","Chile","China","Colombia","Comoros","Congo (Brazzaville)","Congo","Costa Rica","Cote d'Ivoire","Croatia","Cuba","Cyprus","Czech Republic","Denmark","Djibouti","Dominica","Dominican Republic","East Timor (Timor Timur)","Ecuador","Egypt","El Salvador","Equatorial Guinea","Eritrea","Estonia","Ethiopia","Fiji","Finland","France","Gabon","Gambia, The","Georgia","Germany","Ghana","Greece","Grenada","Guatemala","Guinea","Guinea-Bissau","Guyana","Haiti","Honduras","Hungary","Iceland","India","Indonesia","Iran","Iraq","Ireland","Israel","Italy","Jamaica","Japan","Jordan","Kazakhstan","Kenya","Kiribati","Korea, North","Korea, South","Kuwait","Kyrgyzstan","Laos","Latvia","Lebanon","Lesotho","Liberia","Libya","Liechtenstein","Lithuania","Luxembourg","Macedonia","Madagascar","Malawi","Malaysia","Maldives","Mali","Malta","Marshall Islands","Mauritania","Mauritius","Mexico","Micronesia","Moldova","Monaco","Mongolia","Morocco","Mozambique","Myanmar","Namibia","Nauru","Nepal","Netherlands","New Zealand","Nicaragua","Niger","Nigeria","Norway","Oman","Pakistan","Palau","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Poland","Portugal","Qatar","Romania","Russia","Rwanda","Saint Kitts and Nevis","Saint Lucia","Saint Vincent","Samoa","San Marino","Sao Tome and Principe","Saudi Arabia","Senegal","Serbia and Montenegro","Seychelles","Sierra Leone","Singapore","Slovakia","Slovenia","Solomon Islands","Somalia","South Africa","Spain","Sri Lanka","Sudan","Suriname","Swaziland","Sweden","Switzerland","Syria","Taiwan","Tajikistan","Tanzania","Thailand","Togo","Tonga","Trinidad and Tobago","Tunisia","Turkey","Turkmenistan","Tuvalu","Uganda","Ukraine","United Arab Emirates","United Kingdom","United States","Uruguay","Uzbekistan","Vanuatu","Vatican City","Venezuela","Vietnam","Yemen","Zambia","Zimbabwe");	
											$w_options = '';
											foreach($w_countries as $w_country)
											{
												if(in_array($w_country,$multiselect))
												{
													$selected = 'selected="selected"';
												}	
												else
													$selected ='';
																
												echo '<option value="'.$w_country.'" '.$selected.'>'.$w_country.'</option>';
											}
										}
							
										?>	
									</select>
									<?php else : 
									if($key_select_or_input != '' && ($types[$key_select_or_input]=="type_number" || $types[$key_select_or_input]=="type_phone"))
										$onkeypress_function = "onkeypress='return check_isnum_space(event)'";
									else
										if($key_select_or_input != '' && $types[$key_select_or_input]=="type_paypal_price")
											$onkeypress_function = "onkeypress='return check_isnum_point(event)'";
										else
											$onkeypress_function = "";
									?>
									<input id="field_value<?php echo $k; ?>_<?php echo $key; ?>" type="text" value="<?php echo $param_values[2];?>" <?php echo $onkeypress_function; ?> style=" width: 200px;"><?php endif; ?>
									
									<img src="<?php echo WD_FMC_URL . '/images/delete.png?ver='. get_option("wd_form_maker_version").''; ?>" id="delete_condition<?php echo $k; ?>_<?php echo $key; ?>" onclick="delete_field_condition('<?php echo $k; ?>_<?php echo $key; ?>')" style="vertical-align: middle;">
									</div>
									<?php endif;
								}
							}

						?>
						</div>
						<?php endif; 
						} 
						?>
					<input type="hidden" id="condition" name="condition" value="<?php echo $row->condition; ?>" />	
				</fieldset>
				
				</div>
				<input type="hidden" name="boxchecked" value="0">
				<input type="hidden" name="fieldset_id" id="fieldset_id" value="<?php echo WDW_FMC_Library::get('fieldset_id', 'general'); ?>" />
				<input type="hidden" id="task" name="task" value=""/>
				<input type="hidden" id="current_id" name="current_id" value="<?php echo $row->id; ?>" />
			</form>
		<script>
		jQuery(window).load(function () {
			form_maker_options_tabs(jQuery("#fieldset_id").val());
			fm_popup();
			function hide_email_labels(event) {
				var e = event.toElement || event.relatedTarget;
				if (e.parentNode == this || e == this) {
					return;
				}
				this.style.display="none";
			}
			if(document.getElementById('mail_from_labels')) {
				document.getElementById('mail_from_labels').addEventListener('mouseout',hide_email_labels,true);
			}
			if(document.getElementById('mail_subject_labels')) {
				document.getElementById('mail_subject_labels').addEventListener('mouseout',hide_email_labels,true);
			}
			if(document.getElementById('mail_from_name_user_labels')) {
				document.getElementById('mail_from_name_user_labels').addEventListener('mouseout',hide_email_labels,true);
			}
			if(document.getElementById('mail_subject_user_labels')) {
				document.getElementById('mail_subject_user_labels').addEventListener('mouseout',hide_email_labels,true);
			}
			if(document.getElementById('post_title_labels')) {
				document.getElementById('post_title_labels').addEventListener('mouseout',hide_email_labels,true);
			}
			if(document.getElementById('post_tags_labels')) {
				document.getElementById('post_tags_labels').addEventListener('mouseout',hide_email_labels,true);
			}
			if(document.getElementById('post_featured_image_labels')) {
				document.getElementById('post_featured_image_labels').addEventListener('mouseout',hide_email_labels,true);
			}
			if(document.getElementById('dbox_upload_dir_labels')) {
				document.getElementById('dbox_upload_dir_labels').addEventListener('mouseout',hide_email_labels,true);
			}
		});
		function wd_fm_apply_options(task) {
			set_condition();
			fm_set_input_value('task', task);
			document.getElementById('adminForm').submit();
		}
		</script>
		<?php
	}

	public function form_layout($id) {
		$row = $this->model->get_row_data($id);
		$ids = array();
		$types = array();
		$labels = array();
		$fields = explode('*:*new_field*:*', $row->form_fields);
		$fields = array_slice($fields, 0, count($fields) - 1);
		foreach ($fields as $field) {
			$temp = explode('*:*id*:*', $field);
			array_push($ids, $temp[0]);
			$temp = explode('*:*type*:*', $temp[1]);
			array_push($types, $temp[0]);
			$temp = explode('*:*w_field_label*:*', $temp[1]);
			array_push($labels, $temp[0]);
		}
		?>
		<script>
		var form_front = '<?php echo addslashes($row->form_front);?>';
		var custom_front = '<?php echo addslashes($row->custom_front);?>';
		if (custom_front == '') {
			custom_front = form_front;
		}
		function submitbutton() {
			if (jQuery('#autogen_layout').is(':checked')) {
				jQuery('#custom_front').val(custom_front.replace(/\s+/g, ' ').replace(/> </g, '><'));
			}
			else {
				jQuery('#custom_front').val(editor.getValue().replace(/\s+/g, ' ').replace(/> </g, '><'));
			}
		}
		function insertAtCursor_form(myId, myLabel) {
			if (jQuery('#autogen_layout').is(':checked')) {
				alert("Uncheck the Auto-Generate Layout box.");
				return;
			}
			myValue = '<div wdid="' + myId + '" class="wdform_row">%' + myId + ' - ' + myLabel + '%</div>';
			line = editor.getCursor().line;
			ch = editor.getCursor().ch;
			text = editor.getLine(line);
			text1 = text.substr(0, ch);
			text2 = text.substr(ch);
			text = text1 + myValue + text2;
			editor.setLine(line, text);
			editor.focus();
		}
		function autogen(status) {
			if (status) {
				custom_front = editor.getValue();
				editor.setValue(form_front);
				editor.setOption('readOnly', true);
				autoFormat();
			}
			else {
				editor.setValue(custom_front);
				editor.setOption('readOnly', false);
				autoFormat();
			}
		}
		function autoFormat() {
			CodeMirror.commands["selectAll"](editor);
			editor.autoFormatRange(editor.getCursor(true), editor.getCursor(false));
			editor.scrollTo(0,0);
		}
		</script>

		<div class="fm_layout">
			<form action="admin.php?page=manage_fmc" method="post" name="adminForm" enctype="multipart/form-data">
				<?php wp_nonce_field('nonce_fmc', 'nonce_fmc'); ?>
				<div class="fm-layout-actions">
					<div class="fm-page-actions">
						<button class="fm-button save-button small" onclick="submitbutton(); fm_set_input_value('task', 'save_layout');">
							Save
							<span></span>
						</button>
						<button class="fm-button apply-button small" onclick="submitbutton(); fm_set_input_value('task', 'apply_layout');">
							Apply
							<span></span>
						</button>
						<button class="fm-button cancel-button small" onclick="fm_set_input_value('task', 'cancel_options');">
							Cancel
							<span></span>
						</button>
					</div>
				</div>
				<div class="fm-layout-content">
					<h2 style="clear: both;">Description</h2>
					<p>To customize the layout of the form fields uncheck the Auto-Generate Layout box and edit the HTML.</p>
					<p>You can change positioning, add in-line styles and etc. Click on the provided buttons to add the corresponding field.<br /> This will add the following line:
					  <b><span class="cm-tag">&lt;div</span> <span class="cm-attribute">wdid</span>=<span class="cm-string">"example_id"</span> <span class="cm-attribute">class</span>=<span class="cm-string">"wdform_row"</span><span class="cm-tag">&gt;</span>%example_id - Example%<span class="cm-tag">&lt;/div&gt;</span></b>
					  , where <b><span class="cm-tag">&lt;div&gt;</span></b> is used to set a row.</p>
					<p>To return to the default settings you should check Auto-Generate Layout box.</p>
					<h3 style="color:red">Notice</h3>
					<p>Make sure not to publish the same field twice. This will cause malfunctioning of the form.</p>
					<hr/>
					<label for="autogen_layout" style="font-size: 20px; line-height: 45px; margin-left: 10px;">Auto Generate Layout? </label>
					<input type="checkbox" value="1" name="autogen_layout" id="autogen_layout" <?php echo (($row->autogen_layout) ? 'checked="checked"' : ''); ?> />
					<input type="hidden" name="custom_front" id="custom_front" value="" />
					<input type="hidden" id="task" name="task" value=""/>
					<input type="hidden" id="current_id" name="current_id" value="<?php echo $row->id; ?>" />
					<br/>
					<?php
					foreach($ids as $key => $id) {
						if ($types[$key] != "type_section_break") {
							?>
							<button onClick="insertAtCursor_form('<?php echo $ids[$key]; ?>','<?php echo $labels[$key]; ?>')" class="fm_label_buttons" title="<?php echo $labels[$key]; ?>"><?php echo $labels[$key]; ?></button>
							<?php
						}
					}
					?>
				</form>	
			</div>
			<br /><br />
			<button class="fm_submit_layout button button-secondary button-hero" onclick="autoFormat()"><strong>Apply Source Formatting</strong>  <em>(ctrl-enter)</em></button>
			<textarea id="source" name="source" style="display: none;"></textarea>
		</div>
		<script>
		var editor = CodeMirror.fromTextArea(document.getElementById("source"), {
			lineNumbers: true,
			lineWrapping: true,
			mode: "htmlmixed",
			value: form_front
		});
		if (jQuery('#autogen_layout').is(':checked')) {
			editor.setOption('readOnly',  true);
			editor.setValue(form_front);
		}
		else {
			editor.setOption('readOnly',  false);
			editor.setValue(custom_front);
		}
		jQuery('#autogen_layout').click(function() {
			autogen(jQuery(this).is(':checked'));
		});
		autoFormat();
		</script>
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