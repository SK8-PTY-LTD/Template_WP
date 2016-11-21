<?php

class  FMViewSubmissions_fmc {
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
	public function display($form_id) {
		
		
		global $wpdb;
		$forms = $this->model->get_form_titles();
		$statistics = $this->model->get_statistics($form_id);
		$labels_parameters = $this->model->get_labels_parameters($form_id);

		$sorted_labels_id = $labels_parameters[0]; 
		$sorted_label_types = $labels_parameters[1]; 
		$lists = $labels_parameters[2];
		$sorted_label_names = $labels_parameters[3]; 
		$sorted_label_names_original = $labels_parameters[4]; 
		$rows = ((isset($labels_parameters[5])) ? $labels_parameters[5] : NULL);
		$group_ids = ((isset($labels_parameters[6])) ? $labels_parameters[6] : NULL);
		$where_choices = $labels_parameters[7];	
		$order_by = (isset($_POST['order_by']) ? esc_html(stripslashes($_POST['order_by'])) : 'group_id');
		$asc_or_desc = ((isset($_POST['asc_or_desc']) && $_POST['asc_or_desc'] == 'asc') ? 'asc' : 'desc');
		$style_id = $this->model->hide_or_not($lists['hide_label_list'], '@submitid@'); 
		$style_date = $this->model->hide_or_not($lists['hide_label_list'], '@submitdate@');
		$style_ip = $this->model->hide_or_not($lists['hide_label_list'], '@submitterip@');

		$style_username = $this->model->hide_or_not($lists['hide_label_list'], '@submitterusername@');
		$style_useremail = $this->model->hide_or_not($lists['hide_label_list'], '@submitteremail@');

		$oder_class_default = "manage-column column-autor sortable desc";
		$oder_class = "manage-column column-title sorted " . $asc_or_desc; 
		$ispaypal = FALSE;
		$temp = array();
		$m = count($sorted_label_names);
		$n = count($rows);
		$group_id_s = array();	
		$group_id_s = $this->model->sort_group_ids(count($sorted_label_names),$group_ids);  
		$ka_fielderov_search = (($lists['ip_search'] || $lists['startdate'] || $lists['enddate'] || $lists['username_search'] || $lists['useremail_search'] || $lists['id_search']) ? TRUE : FALSE);
		$is_stats = false;
		$blocked_ips = $this->model->blocked_ips();
		
		if (defined('WD_FM_PDF') && is_plugin_active(constant('WD_FM_PDF'))) {
			require_once(WD_FM_PDF_DIR.'/model.php');
			$pdf_data = WD_FM_PDF_model::get_pdf_data($form_id);
		}
		$subs_count = $this->model->get_subs_count($form_id);
		$chosen_form_title = '';
		if ($forms) { 
			foreach($forms as $form) {
				if ($form_id == $form->id) { 
					$chosen_form_title = $form->title;
				}
			}
		}
		?>
		<script type="text/javascript">
			function export_submissions(type, limit) {
			var progressbar = jQuery( "#fm-progressbar" );
			var progressLabel = jQuery( ".fm-progress-label" );
		 
			progressbar.progressbar({
				max: <?php echo $subs_count; ?>
			});
			
			jQuery.ajax({
			    type: "POST",  
				url:"<?php echo add_query_arg(array('form_id' => $form_id, 'send_header' => 0), admin_url('admin-ajax.php')); ?>&action=generete_"+type+"_fmc&limitstart="+limit,
				beforeSend: function() {
					if(<?php echo $subs_count; ?> >= 1000 )
						jQuery('.fm_modal').show();
			    },
			    success: function(data) {
					if(limit < <?php echo $subs_count; ?>) {
						limit += 1000;
						export_submissions(type, limit);
						progressbar.progressbar( "value",  limit);
						loaded_percent = Math.round((progressbar.progressbar( "value" ) * 100)/ parseInt(<?php echo $subs_count; ?>));
						progressLabel.text( loaded_percent + ' %');
						progressbarValue = progressbar.find( ".fm-progress-label" );
						if( loaded_percent >= 46 ) {
							progressbarValue.css({
								"color": '#fff',
							});
						}
						else {
							progressbarValue.css({
								"color": '#444',
							});
						}
					}
					else{
						jQuery('.fm_modal').hide();
						progressbar.progressbar( "value",  0);
						progressLabel.text( 'Loading ...' );
						progressbarValue = progressbar.find( ".fm-progress-label" );
						progressbarValue.css({
							"color": '#444',
						});
						window.location = "<?php echo add_query_arg(array('form_id' => $form_id, 'send_header' => 1), admin_url('admin-ajax.php')); ?>&action=generete_"+type+"_fmc&limitstart="+limit;
					}
			    }
			});
		}
	
			function clickLabChBAll(ChBAll) {
				<?php
					if (isset($sorted_label_names)) {
						$templabels = array_merge(array(
							'submitid',
							'submitdate',
							'submitterip',
							'submitterusername',
							'submitteremail'
						), $sorted_labels_id);
						$sorted_label_names_for_check = array_merge(array(
							'ID',
							'Submit date',
							"Submitter's IP",
							"Submitter's Username",
							"Submitter's Email Address"
						), $sorted_label_names_original);
					}
					else {
						$templabels = array(
							'submitid',
							'submitdate',
							'submitterip',
							'submitterusername',
							'submitteremail'
						);
						$sorted_label_names_for_check = array(
							'ID',
							'Submit date',
							"Submitter's IP",
							'Submitter\'s Username',
							'Submitter\'s Email Address'
						);
					}
				?>
				if (ChBAll.checked) {
					document.forms.admin_form.hide_label_list.value = '';
					for (i = 0; i <= ChBAll.form.length; i++) {
						if (typeof(ChBAll.form[i]) != "undefined") {
							if (ChBAll.form[i].type == "checkbox") {
								ChBAll.form[i].checked = true;
							}
						}
					}
				}
				else {
					document.forms.admin_form.hide_label_list.value = '@<?php echo implode($templabels, '@@') ?>@' + '@payment_info@';
					for (i = 0; i <= ChBAll.form.length; i++) {
						if (typeof(ChBAll.form[i]) != "undefined") {
							if (ChBAll.form[i].type == "checkbox") {
								ChBAll.form[i].checked = false;
							}
						}
					}
				}
				renderColumns();
			}
			
			function remove_all() {
				if(document.getElementById('startdate'))
					document.getElementById('startdate').value='';
				if(document.getElementById('enddate'))
					document.getElementById('enddate').value='';
				if(document.getElementById('id_search'))
					document.getElementById('id_search').value='';
				if(document.getElementById('ip_search'))
					document.getElementById('ip_search').value='';
				if(document.getElementById('username_search'))
					document.getElementById('username_search').value='';
				if(document.getElementById('useremail_search'))
					document.getElementById('useremail_search').value='';
				<?php
				$n = count($rows);
				for ($i = 0; $i < count($sorted_label_names); $i++) {
					if ($sorted_label_types[$i] != "type_mark_map") { ?>
						document.getElementById('<?php echo $form_id . '_' . $sorted_labels_id[$i] . '_search'; ?>').value='';
						<?php
					}
				}
				?>
			}
			function show_hide_filter() {
				if (document.getElementById('fields_filter').style.display == "none") {
					document.getElementById('fields_filter').style.display = '';
				}
				else {
					document.getElementById('fields_filter').style.display = "none";
				}
				return false;
			}
			jQuery(document).ready(function () { 
				jQuery('.theme-detail').click(function () {
					jQuery(this).siblings('.themedetaildiv').toggle();
					return false;
				});
			});
		</script>
		<div class="fm_modal"> 
			<div id="fm-progressbar" >
				<div class="fm-progress-label">Loading...</div>
			</div> 
		</div> 
		<div class="export_progress">
			<span class="exp_count"><?php echo $subs_count; ?></span> left from <?php echo $subs_count; ?>
		</div> 
		<div id="sbox-overlay" onclick="toggleChBDiv(false);">
		</div>
		<div id="ChBDiv">
			<form action="#">
				<?php wp_nonce_field('nonce_fmc', 'nonce_fmc'); ?>
				<p style="font-weight: bold; font-size: 18px; margin-top: 0px;">Select Columns</p>
				<div class="fm_check_labels"><input type="checkbox" <?php echo ($lists['hide_label_list'] === '') ? 'checked="checked"' : ''; ?> onclick="clickLabChBAll(this)" id="ChBAll"/><label for="ChBAll"> All</label></div>
				<?php
				foreach ($templabels as $key => $curlabel) {
					if (strpos($lists['hide_label_list'], '@' . $curlabel . '@') === FALSE) {
						?>
						<div class="fm_check_labels"><input type="checkbox" checked="checked" onclick="clickLabChB('<?php echo $curlabel; ?>', this)" id="fm_check_id_<?php echo $curlabel; ?>" /><label for="fm_check_id_<?php echo $curlabel; ?>"> <?php echo stripslashes($sorted_label_names_for_check[$key]); ?></label></div>
						<?php
					}		  
					else {
						?>
						<div class="fm_check_labels"><input type="checkbox" onclick="clickLabChB('<?php echo $curlabel; ?>', this)" id="fm_check_id_<?php echo $curlabel; ?>"/><label for="fm_check_id_<?php echo $curlabel; ?>"> <?php echo stripslashes($sorted_label_names_for_check[$key]); ?></label></div>
						<?php  
					}
				}
				$ispaypal = FALSE;
				for ($i = 0; $i < count($sorted_label_names); $i++) {
					if ($sorted_label_types[$i] == 'type_paypal_payment_status') {
						$ispaypal = TRUE;
					}
				}
				if ($ispaypal) {
					?>
					<div class="fm_check_labels">
						<input type="checkbox" onclick="clickLabChB('payment_info', this)" id="fm_check_payment_info" <?php echo (strpos($lists['hide_label_list'], '@payment_info@') === FALSE) ? 'checked="checked"' : ''; ?> />
						<label for="fm_check_payment_info"> Payment Info</label>
					</div>
					<?php
				}
				?>
				<div style="text-align: center; padding-top: 20px;">
					<button onclick="toggleChBDiv(false); return false;" style="background: #4EC0D9; width: 78px; height: 32px; border: 1px solid #4EC0D9; border-radius: 0px; color: #fff; cursor: pointer;">Done</button>
				</div>
			</form>
		</div>
		<div class="fm-user-manual">
			This section allows you to view and manage form submissions.
			<a style="color: blue; text-decoration: none;" target="_blank" href="https://web-dorado.com/wordpress-form-maker-guide-7.html">Read More in User Manual</a>
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
		<form action="admin.php?page=submissions_fmc" method="post" id="admin_form" name="admin_form">
			<?php wp_nonce_field('nonce_fmc', 'nonce_fmc'); ?>
			<input type="hidden" name="option" value="com_formmaker" />
			<input type="hidden" id="task" name="task" value="" />
			<input type="hidden" id="current_id" name="current_id" value="" />
			<input type="hidden" name="asc_or_desc" id="asc_or_desc" value="<?php echo $asc_or_desc; ?>" />
			<input type="hidden" name="order_by" id="order_by" value="<?php echo $order_by; ?>" />
			
			<div class="fm-submissions-page">
				<div class="submissions-actions">
					<div class="fm-form-title">
						<?php echo $chosen_form_title; ?>	
					</div>
					<div class="fm-page-actions">
						<button class="fm-button block-button small" onclick="fm_set_input_value('task', 'block_ip'); fm_form_submit(event, 'admin_form');">
							<span></span>
							Block IP
						</button>
						<button class="fm-button unblock-button medium" onclick="fm_set_input_value('task', 'unblock_ip'); fm_form_submit(event, 'admin_form');">
							<span></span>
							Unblock IP
						</button>
						<button class="fm-button delete-button small" onclick="if (confirm('Do you want to delete selected items?')) { fm_set_input_value('task', 'delete_all'); fm_form_submit(event, 'admin_form'); } else { return false; }">
							<span></span>
							Delete
						</button>
					</div>
				</div>
				<div class="submissions-toolbar">
					<div class="submissions-tools">
						<select name="form_id" id="form_id" onchange="document.admin_form.submit();">
							<option value="0" selected="selected"> - Select a Form - </option>
							<?php if ($forms) { 
								foreach($forms as $form) {
									?>
									<option value="<?php echo $form->id; ?>" <?php if ($form_id == $form->id) { echo 'selected="selected"'; }?>> <?php echo $form->title ?> </option>
									<?php
								}
							} ?>
						</select>
						<div class="fm-reports">
							<div class="fm-tools-button"><div class="fm-total_entries"><?php echo $statistics["total_entries"]; ?></div>Entries</div>
							<div class="fm-tools-button"><div class="fm-total_rate"><?php echo $statistics["conversion_rate"]; ?></div>Conversion Rate</div>
							<div class="fm-tools-button"><div class="fm-total_views"><?php echo $statistics["total_views"] ? $statistics["total_views"] : 0; ?></div>Views</div>
						</div>
						
						<div class="fm-export-tools">
							<span class="exp_but_span">Export to</span>
							&nbsp;
							<button class="fm-tools-button" onclick="export_submissions('csv', 0); return false;">
								CSV
							</button>
							<button class="fm-tools-button" onclick="export_submissions('xml', 0); return false;">
								XML
							</button>
						</div>
					</div>
				</div>
				<div class="tablenav top">
					<div class="fm-filters">
						<div class="fm-search-tools">
							<input type="hidden" name="hide_label_list" value="<?php echo $lists['hide_label_list']; ?>"> 
							<button class="fm-icon show-filter-icon" onclick="show_hide_filter(); return false;" title="Show Filters">
								<span></span>
							</button>
							<button class="fm-icon search-icon" onclick="fm_form_submit(event, 'admin_form'); return false;" title="Search">
							</button>
							<button class="fm-icon reset-icon" onclick="remove_all(); fm_form_submit(event, 'admin_form'); return false;" title="Reset">
							</button>
						</div>
						<div class="fm-add-remove">
							<?php if (isset($sorted_label_names)) { ?>
							<button class="fm-button" onclick="toggleChBDiv(true); return false;">
								Add/Remove Columns
							</button>
							<?php WDW_FMC_Library::html_page_nav($lists['total'], $lists['limit'], 'admin_form'); ?>
							<?php } ?>
							<input type="hidden" name="pagination_clicked" id="pagination_clicked" value=""/>
						</div>
					</div>
					<div class="fm-clear"></div>
				</div>
				
				<div class="fm-loading-container" style="display:none;">
					<div class="fm-loading-content">
					</div>
				</div>
				<div class="submit_content" id="fm-scroll" style="width: 100%;">
					<table class="wp-list-table widefat fixed posts table_content">
						<thead>
							<tr>
								<?php if (defined('WD_FM_PDF') && is_plugin_active(constant('WD_FM_PDF'))): ?> 
								<th class="table_small_col count_col sub-align">PDF</th>
								<?php endif; ?>
								<th class="table_small_col count_col sub-align">#</th>
								<th scope="col" id="cb" class="manage-column column-cb check-column table_small_col sub-align form_check"><input id="check_all" type="checkbox"></th>
								<th scope="col" id="submitid_fc" class="table_small_col sub-align submitid_fc <?php if ($order_by == "group_id") echo $oder_class; else echo $oder_class_default; ?>" <?php echo $style_id;?>>
									<a href="" class="sub_id" onclick="fm_set_input_value('order_by', 'group_id');
													   fm_set_input_value('asc_or_desc', '<?php echo (($order_by == 'group_id' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>');
													   fm_form_submit(event, 'admin_form')">
										<span>ID</span>
										<span class="sorting-indicator" style="margin-top: 8px;"></span>
									</a>
								</th>
								<th class="table_small_col sub-align">Edit</th>
								<th class="table_small_col sub-align">Delete</th>
								<th scope="col" id="submitdate_fc" class="table_large_col submitdate_fc <?php if ($order_by == "date") echo $oder_class; else echo $oder_class_default; ?>" <?php echo $style_date;?>>
									<a href="" onclick="fm_set_input_value('order_by', 'date');
										fm_set_input_value('asc_or_desc', '<?php echo (($order_by == 'date' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>');
										fm_form_submit(event, 'admin_form')">
										<span>Submit date</span>
										<span class="sorting-indicator"></span>
									</a>
								</th>
								<th scope="col" id="submitterip_fc" class="table_medium_col_uncenter submitterip_fc <?php if ($order_by == "ip")echo $oder_class; else echo $oder_class_default;  ?>" <?php echo $style_ip;?>>
									<a href="" onclick="fm_set_input_value('order_by', 'ip');
										fm_set_input_value('asc_or_desc', '<?php echo (($order_by == 'ip' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>');
										fm_form_submit(event, 'admin_form')">
										<span>Submitter's IP</span>
										<span class="sorting-indicator"></span>
									</a>
								</th>	
								<th scope="col" id="submitterusername_fc" class="table_medium_col_uncenter submitterusername_fc <?php if ($order_by == "display_name")echo $oder_class; else echo $oder_class_default;  ?>" <?php echo $style_username;?>>
									<a href="" onclick="fm_set_input_value('order_by', 'display_name');
										fm_set_input_value('asc_or_desc', '<?php echo (($order_by == 'display_name' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>');
										fm_form_submit(event, 'admin_form')">
										<span>Submitter's Username</span>
										<span class="sorting-indicator"></span>
									</a>
								</th>	
								<th scope="col" id="submitteremail_fc" class="table_medium_col_uncenter submitteremail_fc <?php if ($order_by == "user_email")echo $oder_class; else echo $oder_class_default;  ?>" <?php echo $style_useremail ;?>>
									<a href="" onclick="fm_set_input_value('order_by', 'user_email');
										fm_set_input_value('asc_or_desc', '<?php echo (($order_by == 'user_email' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>');
										fm_form_submit(event, 'admin_form')">
										<span>Submitter's Email Address</span>
										<span class="sorting-indicator"></span>
									</a>
								</th>	
								<?php
								for ($i = 0; $i < count($sorted_label_names); $i++) {
									$styleStr = $this->model->hide_or_not($lists['hide_label_list'], $sorted_labels_id[$i]); 
									$styleStr2 = $this->model->hide_or_not($lists['hide_label_list'] , '@payment_info@');		   
									$field_title = $this->model->get_type_address($sorted_label_types[$i], $sorted_label_names_original[$i]);
									if ($sorted_label_types[$i] == 'type_paypal_payment_status') {
										$ispaypal = TRUE;
										?>
										<th <?php echo $styleStr; ?> id="<?php echo $sorted_labels_id[$i] . '_fc'; ?>" class="table_large_col <?php echo $sorted_labels_id[$i] . '_fc'; if ($order_by == $sorted_labels_id[$i] . "_field") echo $oder_class . '"';else echo $oder_class_default . '"'; ?>">
											<a href="" onclick="fm_set_input_value('order_by', '<?php echo $sorted_labels_id[$i] . '_field'; ?>'); fm_set_input_value('asc_or_desc', '<?php echo (($order_by == $sorted_labels_id[$i] . '_field' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>'); fm_form_submit(event, 'admin_form')">	
												<span><?php echo $field_title; ?></span>
												<span class="sorting-indicator"></span>
											</a>
										</th>
										<th class="table_large_col payment_info_fc" <?php echo $styleStr2; ?>>Payment Info</th>
										<?php  
									}
									else {
										?>
										<th <?php echo $styleStr; ?> id="<?php  echo $sorted_labels_id[$i] . '_fc';?>" class="<?php echo ($sorted_label_types[$i] == 'type_mark_map' || $sorted_label_types[$i] == 'type_matrix') ? 'table_large_col ' : ''; echo $sorted_labels_id[$i] . '_fc'; if ($order_by == $sorted_labels_id[$i] . "_field") echo $oder_class . '"';else echo $oder_class_default . '"'; ?>">
											<a href="" onclick="fm_set_input_value('order_by', '<?php echo $sorted_labels_id[$i] . '_field'; ?>'); fm_set_input_value('asc_or_desc', '<?php echo (($order_by == $sorted_labels_id[$i] . '_field' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>'); fm_form_submit(event, 'admin_form')">
												<span><?php echo $field_title; ?></span>
												<span class="sorting-indicator"></span>
											</a>
										</th>
										<?php
									}
								}			
								?>		           
							</tr>
							<tr id="fields_filter" style="display: none;">
								<th></th>
								<th></th> 
								<th class="submitid_fc" <?php echo $style_id; ?> >
									<input type="text" name="id_search" id="id_search" value="<?php echo $lists['id_search'] ?>" onChange="this.form.submit();" style="width:30px"/>
								</th>
								<th></th>
								<th></th>
								<th width="150" class="submitdate_fc" <?php echo $style_date; ?>>
									<table align="center" style="margin:auto" class="simple_table">
										<tr class="simple_table">
											<td class="simple_table" style="text-align: left;">From:</td>
											<td style="text-align: center;" class="simple_table">
												<input class="inputbox" type="text" name="startdate" id="startdate" size="10" maxlength="10" value="<?php echo $lists['startdate']; ?>" />
											</td>
											<td style="text-align: center;" class="simple_table">
												<input type="reset" style="width: 22px; border-radius: 3px !important;" class="button" value="..." onclick="return showCalendar('startdate','%Y-%m-%d');" />
											</td>
										</tr>
										<tr class="simple_table">
											<td style="text-align: left;" class="simple_table">To:</td>
											<td style="text-align: center;" class="simple_table">
												<input class="inputbox" type="text" name="enddate" id="enddate" size="10" maxlength="10" value="<?php echo $lists['enddate']; ?>" />
											</td>
											<td style="text-align: center;" class="simple_table">
												<input type="reset" style="width: 22px; border-radius: 3px !important;" class="button" value="..." onclick="return showCalendar('enddate','%Y-%m-%d');" />
											</td>
										</tr>
									</table>
								</th>
								<th class="table_medium_col_uncenter submitterip_fc" <?php echo $style_ip; ?>>
									<input type="text" name="ip_search" id="ip_search" value="<?php echo $lists['ip_search']; ?>" onChange="this.form.submit();" />
								</th>
								<th class="table_medium_col_uncenter submitterusername_fc" <?php echo $style_username; ?>>
									<input type="text" name="username_search" id="username_search" value="<?php echo $lists['username_search']; ?>" onChange="this.form.submit();" />
								</th>
								<th class="table_medium_col_uncenter submitteremail_fc" <?php echo $style_useremail; ?>>
									<input type="text" name="useremail_search" id="useremail_search" value="<?php echo $lists['useremail_search']; ?>" onChange="this.form.submit();" />
								</th>
								<?php
								for ($i = 0; $i < count($sorted_label_names); $i++) {
									$styleStr = $this->model->hide_or_not($lists['hide_label_list'], $sorted_labels_id[$i]);
									if (!$ka_fielderov_search) {
										if ($lists[$form_id . '_' . $sorted_labels_id[$i] . '_search']) {
											$ka_fielderov_search = TRUE;
										}
									}	
									switch ($sorted_label_types[$i]) {
										case 'type_mark_map': ?>
											<th class="table_large_col <?php echo $sorted_labels_id[$i]; ?>_fc" <?php echo $styleStr; ?>></th>
											<?php
										break;
										case 'type_paypal_payment_status': ?>
											<th class="table_large_col <?php echo $sorted_labels_id[$i]; ?>_fc" <?php echo $styleStr; ?>>
												<select style="font-size: 11px; margin: 0; padding: 0; height: inherit;" name="<?php echo $form_id . '_' . $sorted_labels_id[$i]; ?>_search" id="<?php echo $form_id.'_'.$sorted_labels_id[$i]; ?>_search" onChange="this.form.submit();" value="<?php echo $lists[$form_id.'_'.$sorted_labels_id[$i].'_search']; ?>" >
													<option value="" ></option>
													<option value="canceled" >Canceled</option>
													<option value="cleared" >Cleared</option>
													<option value="cleared by payment review" >Cleared by payment review</option>
													<option value="completed" >Completed</option>
													<option value="denied" >Denied</option>
													<option value="failed" >Failed</option>
													<option value="held" >Held</option>
													<option value="in progress" >In progress</option>
													<option value="on hold" >On hold</option>
													<option value="paid" >Paid</option>
													<option value="partially refunded" >Partially refunded</option>
													<option value="pending verification" >Pending verification</option>
													<option value="placed" >Placed</option>
													<option value="processing" >Processing</option>
													<option value="refunded" >Refunded</option>
													<option value="refused" >Refused</option>
													<option value="removed" >Removed</option>
													<option value="returned" >Returned</option>
													<option value="reversed" >Reversed</option>
													<option value="temporary hold" >Temporary hold</option>
													<option value="unclaimed" >Unclaimed</option>
												</select>	
												<script> 
													var element = document.getElementById('<?php echo $form_id.'_'.$sorted_labels_id[$i]; ?>_search');
													element.value = '<?php echo $lists[$form_id.'_'.$sorted_labels_id[$i].'_search']; ?>';
												</script>
											</th>
											<th class="table_large_col  payment_info_fc" <?php echo $styleStr2; ?>></th>
											<?php				
										break;
										default: ?>
											<th class="<?php echo $sorted_labels_id[$i]; ?>_fc" <?php echo $styleStr; ?>>
												<input name="<?php echo $form_id .'_' . $sorted_labels_id[$i].'_search'; ?>" id="<?php echo $form_id .'_' . $sorted_labels_id[$i].'_search'; ?>" type="text" value="<?php echo $lists[$form_id.'_'.$sorted_labels_id[$i].'_search']; ?>"  onChange="this.form.submit();" >
											</th>
											<?php	
										break;			
									}
								}
								?>
							</tr>
						</thead>
						<?php
						$k = 0;
						for ($www = 0, $qqq = count($group_id_s); $www < $qqq; $www++) {
							$i = $group_id_s[$www];
							$alternate = (!isset($alternate) || $alternate == 'class="alternate"') ? '' : 'class="alternate"';
							$temp = $this->model->array_for_group_id($group_id_s[$www], $rows);
							$data = $temp[0];
							$userinfo=get_userdata($data->user_id_wd);
							$useremail=$userinfo ? $userinfo->user_email : "";
							$username=$userinfo ? $userinfo->display_name : "";
							?>
							<tr <?php echo $alternate; ?>>
								<?php if (defined('WD_FM_PDF') && is_plugin_active(constant('WD_FM_PDF'))): ?> 
								<td class="table_small_col pdf_col sub-align">
									<?php if($pdf_data && isset($pdf_data[$group_id_s[$www]])): ?> 
									<a href="<?php echo site_url().'/'.$pdf_data[$group_id_s[$www]]; ?>" style="display: block;" download><img src="<?php echo WD_FM_PDF_URL . '/images/pdf-icon.png'; ?>" /></a>
									<?php endif; ?>
								</td>
								<?php endif; ?>
								<td class="table_small_col count_col sub-align"><?php echo $www + 1; ?></td>
								<td class="check-column table_small_col sub-align" style="padding: 0;">
									<input type="checkbox" name="post[]" value="<?php echo $data->group_id; ?>">
								</td>   
								<td class="table_small_col sub-align submitid_fc" id="submitid_fc" <?php echo $style_id; ?>>
									<a href="" onclick="fm_set_input_value('task', 'edit');						   fm_set_input_value('current_id',<?php echo $data->group_id; ?>); fm_form_submit(event, 'admin_form');" >
										<?php echo $data->group_id; ?>
									</a>
								</td> 
								<td class="table_small_col sub-align">
									<a href="" onclick="fm_set_input_value('task', 'edit');						   fm_set_input_value('current_id',<?php echo $data->group_id; ?>); fm_form_submit(event, 'admin_form');">Edit
									</a>
								</td>
								<td class="table_small_col sub-align">
									<a href="" onclick="if (confirm('Do you want to delete selected item(s)?')) { fm_set_input_value('task', 'delete'); fm_set_input_value('current_id',<?php echo $data->group_id; ?>); fm_form_submit(event, 'admin_form'); } else { return false; }">Delete
									</a>
								</td>		 
								<td  class="table_large_col submitdate_fc sub-align" id="submitdate_fc" <?php echo $style_date; ?>>
									<a href="" onclick="fm_set_input_value('task', 'edit'); fm_set_input_value('current_id',<?php echo $data->group_id; ?>); fm_form_submit(event, 'admin_form');" ><?php echo $data->date ;?>
									</a>
								</td>
								<td class="table_medium_col_uncenter submitterip_fc sub-align" id="submitterip_fc" <?php echo $style_ip; ?>>
									<a class="thickbox-preview" href="<?php echo add_query_arg(array('action' => 'fromipinfoinpopup_fmc', 'data_ip' => $data->ip, 'width' => '400', 'height' => '300', 'TB_iframe' => '1'), admin_url('admin-ajax.php')); ?>" title="Show submitter information" <?php echo (!in_array($data->ip, $blocked_ips)) ? '' : 'style="color: #FF0000;"'; ?>><?php echo $data->ip; ?></a>
								</td>
								<td  class="table_large_col submitterusername_fc sub-align" id="submitterusername_fc" <?php echo $style_username; ?>>
									<?php   echo  $username; ?>
								</td>
								<td  class="table_large_col submitteremail_fc sub-align" id="submitteremail_fc" <?php echo $style_useremail; ?>>
									<?php  echo $useremail; ?>
								</td>
								<?php
								for ($h = 0; $h < $m; $h++) {
									$not_label = TRUE;
									for ($g = 0; $g < count($temp); $g++) {
										$styleStr = $this->model->hide_or_not($lists['hide_label_list'], $sorted_labels_id[$h]);
										if ($temp[$g]->element_label == $sorted_labels_id[$h]) {
											if (strpos($temp[$g]->element_value, "***map***")) {
												$map_params = explode('***map***', $temp[$g]->element_value);
												?>
												<td class="table_large_col <?php echo $sorted_labels_id[$h]; ?>_fc sub-align" id="<?php echo $sorted_labels_id[$h]; ?>_fc" <?php echo $styleStr; ?>>
													<a class="thickbox-preview" href="<?php echo add_query_arg(array('action' => 'frommapeditinpopup_fmc', 'long' => $map_params[0], 'lat' => $map_params[1], 'width' => '620', 'height' => '550', 'TB_iframe' => '1'), admin_url('admin-ajax.php')); ?>" title="Show on Map">Show on Map</a>
												</td>
												<?php 
											}
											elseif (strpos($temp[$g]->element_value, "*@@url@@*")) {
												?>
												<td class="<?php echo $sorted_labels_id[$h]; ?>_fc sub-align" id="<?php echo $sorted_labels_id[$h]; ?>_fc" <?php echo $styleStr; ?>>
													<?php
													$new_files = explode("*@@url@@*", $temp[$g]->element_value);
													foreach ($new_files as $new_file) {
														if ($new_file) {
															$new_filename = explode('/', $new_file);
															$new_filename = $new_filename[count($new_filename) - 1];
															?>
															<a target="_blank" class="fm_fancybox" rel="group_<?php echo $www; ?>" href="<?php echo $new_file; ?>"><?php echo $new_filename; ?></a><br />
															<?php
														}
													}
													?>
												</td>
												<?php
											}
											elseif (strpos($temp[$g]->element_value, "***star_rating***")) {
												$view_star_rating_array = $this->model->view_for_star_rating($temp[$g]->element_value, $temp[$g]->element_label);
												$stars = $view_star_rating_array[0];
												?>
												<td align="center" class="<?php echo $sorted_labels_id[$h];?>_fc sub-align" id="<?php echo $sorted_labels_id[$h]; ?>_fc" <?php echo $styleStr; ?>><?php echo $stars; ?></td>
												<?php  
											}
											elseif (strpos($temp[$g]->element_value, "***matrix***")) {
												?>   
												<td class="table_large_col <?php echo $sorted_labels_id[$h];?>_fc sub-align" id="<?php echo $sorted_labels_id[$h]; ?>_fc" <?php echo $styleStr; ?>>
													<a class="thickbox-preview" href="<?php echo add_query_arg(array('action' => 'show_matrix', 'matrix_params' => $temp[$g]->element_value, 'width' => '620', 'height' => '550', 'TB_iframe' => '1'), admin_url('admin-ajax.php')); ?>" title="Show Matrix">Show Matrix</a>
												</td>
												<?php
											}
											elseif (strpos($temp[$g]->element_value, "@@@") !== FALSE || $temp[$g]->element_value == "@@@" || $temp[$g]->element_value == "@@@@@@@@@") {
												?>
												<td class="<?php echo $sorted_labels_id[$h]; ?>_fc" <?php echo $styleStr; ?>>
													<p><?php echo str_replace("@@@", " ", $temp[$g]->element_value); ?></p>
												</td>
												<?php
											}
											elseif (strpos($temp[$g]->element_value, "***grading***")) {
												$view_grading_array = $this->model->view_for_grading($temp[$g]->element_value);
												$items = $view_grading_array[0];
												?>
												<td class="<?php echo $sorted_labels_id[$h];?>_fc sub-align" id="<?php echo $sorted_labels_id[$h]; ?>_fc" <?php echo $styleStr; ?>>
													<p><?php echo $items; ?></p>
												</td>
												<?php
											}
											else {
												if (strpos($temp[$g]->element_value, "***quantity***")) {
													$temp[$g]->element_value = str_replace("***quantity***", " ", $temp[$g]->element_value);
												}
												if (strpos($temp[$g]->element_value, "***property***")) {
													$temp[$g]->element_value = str_replace("***property***", " ", $temp[$g]->element_value);
												}

												if($sorted_label_types[$h]=="type_submitter_mail"){	
													$query = $wpdb->prepare('SELECT id FROM ' . $wpdb->prefix . 'formmaker_submits WHERE form_id ="%d" AND group_id="%d" AND element_value="verified**%d"', $form_id, $i, $sorted_labels_id[$h]);
													$isverified = $wpdb->get_var($query);
								
													if($isverified) { ?>
														<td class="<?php echo $sorted_labels_id[$h];?>_fc" id="<?php echo $sorted_labels_id[$h]; ?>_fc" <?php echo $styleStr; ?>>
															<p><?php echo $temp[$g]->element_value; ?> <span style="color:#2DA068;">( Verified <img src="<?php echo WD_FMC_URL . '/images/verified.png'; ?>" /> )</span></p>
														</td>
													<?php }	
													else {?>
														<td class="<?php echo $sorted_labels_id[$h];?>_fc" id="<?php echo $sorted_labels_id[$h]; ?>_fc" <?php echo $styleStr; ?>>
															<p><?php echo $temp[$g]->element_value; ?></p>
														</td>	
													<?php }	
												}	
												else{
													?>
													<td class="<?php echo $sorted_labels_id[$h];?>_fc sub-align" id="<?php echo $sorted_labels_id[$h]; ?>_fc" <?php echo $styleStr; ?>>
														<p><?php echo str_replace("***br***", '<br>', stripslashes($temp[$g]->element_value)) ; ?></p>
													</td>
													<?php   
												}
											}	
											$not_label = FALSE;
										}
									}
									if ($not_label) {
										?>
										<td class="<?php echo $sorted_labels_id[$h];?>_fc sub-align" id="<?php echo $sorted_labels_id[$h]; ?>_fc" <?php echo $styleStr; ?>><p>&nbsp;</p></td>
										<?php
									}
								}
								if ($ispaypal) {
									$styleStr = $this->model->hide_or_not($lists['hide_label_list'], '@payment_info@');
									?>
									<td class="table_large_col payment_info_fc sub-align" id="payment_info_fc" <?php echo $styleStr; ?>>
										<a class="thickbox-preview" href="<?php echo add_query_arg(array('action' => 'paypal_info', 'id' => $i, 'width' => '600', 'height' => '500', 'TB_iframe' => '1'), admin_url('admin-ajax.php')); ?>">
											<img src="<?php echo WD_FMC_URL . '/images/info.png'; ?>" />
										</a>
									</td>
									<?php
								}
								?>
							</tr>
							<?php
							$k = 1 - $k;
						}
						?>
					</table>
				</div>	 
				<?php
				if ($sorted_label_types) {
					foreach ($sorted_label_types as $key => $sorted_label_type) {
						if ($this->model->check_radio_type($sorted_label_type)) {
							$is_stats = true;
							break;
						}
					}
					if ($is_stats) {
						$ajax_nonce = wp_create_nonce( "nonce_fmc_ajax" );
						?>
						<br/>
						<div class="fm-statistics">
							<h1>Statistics</h1>		
							<table class="stats">
								<tr>
									<td>
										<label for="sorted_label_key">Select a Field:</label>
									</td>
									<td>
										<select id="sorted_label_key">
											<option value="">Select a Field</option>
											<?php 
											foreach ($sorted_label_types as $key => $sorted_label_type) {
												if ($sorted_label_type=="type_checkbox" || $sorted_label_type=="type_radio" || $sorted_label_type=="type_own_select" || $sorted_label_type=="type_country" || $sorted_label_type=="type_paypal_select" || $sorted_label_type=="type_paypal_radio" || $sorted_label_type=="type_paypal_checkbox" || $sorted_label_type=="type_paypal_shipping") {				  
													?>
													<option value="<?php echo $key; ?>"><?php echo $sorted_label_names_original[$key]; ?></option>
													<?php
												}
											}
											?>
										</select>
									</td>
									<td></td>
								</tr>
								<tr>
									<td>
										<label>Select a Date:</label>
									</td>
									<td>
										From: <input class="inputbox"  type="text" name="startstats" id="startstats" size="9" maxlength="9" />
										  <input type="reset" class="button" style="width: 22px;"  value="..." name="startstats_but" id="startstats_but" onclick="return showCalendar('startstats','%Y-%m-%d');" /> 
											 
										To: <input class="inputbox" type="text" name="endstats" id="endstats" size="9" maxlength="9" />
										<input type="reset" class="button" style="width: 22px;"  value="..." name="endstats_but" id="endstats_but" onclick="return showCalendar('endstats','%Y-%m-%d');" />
									</td>
									<td>
										<button onclick="show_stats(); return false;">Show</button>
									</td>
								</tr>
							</table>
							
							<div id="div_stats"></div>	
						</div>
						<script>
						function show_stats() { 
							jQuery('#div_stats').html('<div class="fm-loading-container"><div class="fm-loading-content"></div></div>');
							if(jQuery('#sorted_label_key').val()!="") {	 	  
								jQuery('#div_stats').load('<?php echo add_query_arg(array('action' => 'get_stats_fmc', 'page' => 'submissions_fmc'), admin_url('admin-ajax.php')); ?>', { 
									'task': 'show_stats',
									'form_id' : '<?php echo $form_id; ?>',
									'sorted_label_key' : jQuery('#sorted_label_key').val(),
									'startdate' : jQuery('#startstats').val(), 
									'enddate' : jQuery('#endstats').val(),
									'nonce_fmc_ajax': '<?php echo $ajax_nonce; ?>'
								});
							}		
							else {
								jQuery('#div_stats').html("<div style='padding:10px 5px; color:red; font-size:14px;'>Please select the field!</div>");
							}	
							jQuery("#div_stats").removeClass("fm_loading");
						}
						</script>
						<?php	
					}
				}
				?>
			</div>	
		</form>	
		<script> 
		function fm_scroll(element) {
			var scrollbar= document.createElement('div');
			scrollbar.appendChild(document.createElement('div'));
			scrollbar.style.overflow= 'auto';
			scrollbar.style.overflowY= 'hidden';
			scrollbar.firstChild.style.width= element.scrollWidth+'px';
			scrollbar.firstChild.style.paddingTop= '1px';
			scrollbar.firstChild.appendChild(document.createTextNode('\xA0'));
			scrollbar.onscroll= function() {
				element.scrollLeft= scrollbar.scrollLeft;
			};
			element.onscroll= function() {
				scrollbar.scrollLeft= element.scrollLeft;
			};
			element.parentNode.insertBefore(scrollbar, element);
		}
		jQuery(window).load(function() {
			fm_popup();
			fm_scroll(document.getElementById('fm-scroll'));
			if (typeof jQuery().fancybox !== 'undefined' && jQuery.isFunction(jQuery().fancybox)) {
				jQuery(".fm_fancybox").fancybox({
					'maxWidth ' : 600,
					'maxHeight' : 500
				});
			}
		});
		<?php if ($ka_fielderov_search) { ?> 
			document.getElementById('fields_filter').style.display = '';
        <?php } ?>
		</script>
		<?php
	}

	public function show_stats($form_id) {
		$key = (isset($_POST['sorted_label_key']) ? esc_html(stripslashes($_POST['sorted_label_key'])) : ''); 
		$labels_parameters = $this->model->get_labels_parameters($form_id);
		$where_choices = $labels_parameters[7];
		$sorted_label_names_original = $labels_parameters[4];
		$sorted_labels_id = $labels_parameters[0];	 
		if(count($sorted_labels_id)!=0 && $key < count($sorted_labels_id)  ) { 
			$choices_params = $this->model->statistic_for_radio($where_choices, $sorted_labels_id[$key]);
			$sorted_label_name_original = $sorted_label_names_original[$key];
			$choices_count = $choices_params[0];
			$choices_labels = $choices_params[1];
			$unanswered = $choices_params[2];
			$all = $choices_params[3];
			$colors = $choices_params[4];	  
			$choices_colors = $choices_params[5];	  
		}
		else {
			$choices_labels = array();
			$sorted_label_name_original = '';
			$unanswered = NULL;
			$all = 0;
		}
		?>
		<br/>
		<br/>
		<div class="field-label"><?php echo stripslashes($sorted_label_name_original); ?></div>
		<table class="adminlist">
			<thead>
				<tr>
					<th width="20%">Choices</th>
					<th>Percentage</th>
					<th width="10%">Count</th>
				</tr>
			</thead>
			<?php
			$k=0;
			foreach ($choices_labels as $key => $choices_label) {
				if (strpos($choices_label, "***quantity***")) {
					$choices_label = str_replace("***quantity***", " ", $choices_label);
				}
				if (strpos($choices_label, "***property***")) {
					$choices_label = str_replace("***property***", " ", $choices_label);
				}
				?>
				<tr>
					<td class="label<?php echo $k; ?>"><?php echo str_replace("***br***",'<br>', $choices_label)?></td>
					<td>
						<div class="bordered" style="width:<?php echo ($choices_count[$key]/($all-$unanswered))*100; ?>%; height:16px; background-color:<?php echo $colors[$key % 2]; ?>; float: left;">
						</div>
						<div <?php echo ($choices_count[$key]/($all-$unanswered)!=1 ? 'class="bordered'.$k.'"' : "") ?> style="width:<?php echo 100-($choices_count[$key]/($all-$unanswered))*100; ?>%; height:16px; background-color:#F2F0F1; float: left;">
						</div>
					</td>
					<td>
						<div>
							<div style="width: 0; height: 0; border-top: 8px solid transparent;border-bottom: 8px solid transparent; border-right:8px solid <?php echo $choices_colors[$key % 2]; ?>; float:left;">
							</div>
							<div style="background-color:<?php echo $choices_colors[$key % 2]; ?>; height:16px; width:16px; text-align: center; margin-left:8px; color: #fff;">
							<?php echo $choices_count[$key]?>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="3">
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			if($unanswered){
				?>
				<tr>
					<td colspan="2" style="text-align:right; color: #000;">Unanswered</th>
					<td><strong style="margin-left:10px;"><?php echo $unanswered;?></strong></th>
				</tr>
				<?php	
			}
			?>
			<tr>
				<td colspan="2" style="text-align:right; color: #000;"><strong>Total</strong></th>
				<td><strong style="margin-left:10px;"><?php echo $all;?></strong></th>
			</tr>
		</table>
		<?php
		die();
	}

  public function edit($id) {
    $current_id = ((isset($id)) ? $id : 0);
    $params = $this->model->get_data_of_group_id($current_id);
    $rows = $params[0];
    $labels_id = $params[1];
    $labels_name = $params[2];
    $labels_type = $params[3];
    $ispaypal = $params[4];
	$userinfo = get_userdata($rows[0]->user_id_wd);
	$username = $userinfo ? $userinfo->display_name : "";
	$useremail = $userinfo ? $userinfo->user_email : "";
	
    ?>
    <form action="admin.php?page=submissions_fmc" method="post" id="adminForm" name="adminForm">
      <?php wp_nonce_field('nonce_fmc', 'nonce_fmc'); ?>
      <table width="99%">
        <tbody>
          <tr>
            <td width="100%"><h2>Edit Submission</h2></td>
            <td align="right">
              <input type="button" onclick="fm_set_input_value('task', 'save');						  
                                            fm_set_input_value('current_id', <?php echo $current_id; ?>);
                                            fm_form_submit(event, 'adminForm');" value="Save" class="button-secondary action">
            </td>
            <td align="right">
              <input type="button" onclick="fm_set_input_value('task', 'apply');						  
                                            fm_set_input_value('current_id', <?php echo $current_id ;?>);
                                            fm_form_submit(event, 'adminForm');" value="Apply" class="button-secondary action">
            </td>
            <td align="right">
              <input type="button" onclick="fm_set_input_value('task', '');fm_form_submit(event, 'adminForm');" value="Cancel" class="button-secondary action">
            </td>
          </tr>
        </tbody>
      </table>
      <table class="admintable">
        <tr>
          <td class="key"><label for="ID">ID: </label></td>
          <td><?php echo $rows[0]->group_id; ?></td>
        </tr>
        <tr>
          <td class="key"><label for="Date">Date: </label></td>
          <td><?php echo $rows[0]->date; ?></td>
        </tr>
        <tr>
          <td class="key"><label for="IP">IP: </label></td>
          <td><?php echo $rows[0]->ip; ?></td>
        </tr>
		
		
		<tr>
          <td class="key"><label for="Submitter's Username">Submitter's Username: </label></td>
          <td><?php echo $username; ?></td>
        </tr>
		<tr>
          <td class="key"><label for="Submitter's Email Address">Submitter's Email Address: </label></td>
          <td><?php echo $useremail; ?></td>
        </tr>
        <?php
        foreach ($labels_id as $key => $label_id) {
          if ($this->model->check_type_for_edit_function($labels_type[$key])) {
            $element_value = $this->model->check_for_submited_label($rows, $label_id);
            if ($element_value == "continue") {
              continue;
            }
            switch ($labels_type[$key]) {
              case 'type_checkbox':
                $choices = explode('***br***', $element_value);
                $choices = array_slice($choices, 0, count($choices) - 1);
                ?>
				<tr>
          <td class="key" rowspan="<?php echo count($choices); ?>">
            <label for="title"><?php echo $labels_name[$key]; ?></label>
          </td>
                <?php
                foreach ($choices as $choice_key => $choice) {
                  ?>
				  <td>
            <input type="text" name="submission_<?php echo $label_id.'_'.$choice_key; ?>" id="submission_<?php echo $label_id.'_'.$choice_key; ?>" value="<?php echo $choice; ?>" size="80" />
				  </td>
				</tr>
                  <?php
                }
                break;
              case 'type_paypal_payment_status':
                ?>
				<tr>
          <td class="key">
            <label for="title"><?php echo $labels_name[$key]; ?></label>
          </td>
          <td>
            <select name="submission_0" id="submission_0" >
              <option value=""></option>
              <option value="Canceled" >Canceled</option>
              <option value="Cleared" >Cleared</option>
              <option value="Cleared by payment review" >Cleared by payment review</option>
              <option value="Completed" >Completed</option>
              <option value="Denied" >Denied</option>
              <option value="Failed" >Failed</option>
              <option value="Held" >Held</option>
              <option value="In progress" >In progress</option>
              <option value="On hold" >On hold</option>
              <option value="Paid" >Paid</option>
              <option value="Partially refunded" >Partially refunded</option>
              <option value="Pending verification" >Pending verification</option>
              <option value="Placed" >Placed</option>
              <option value="Processing" >Processing</option>
              <option value="Refunded" >Refunded</option>
              <option value="Refused" >Refused</option>
              <option value="Removed" >Removed</option>
              <option value="Returned" >Returned</option>
              <option value="Reversed" >Reversed</option>
              <option value="Temporary hold" >Temporary hold</option>
              <option value="Unclaimed" >Unclaimed</option>
            </select>	
            <script> 
              var element = document.getElementById("submission_0");
              element.value = "<?php echo $element_value; ?>";
            </script>
			    </td>
				</tr>
                <?php
                break;
              case 'type_star_rating':
                $star_rating_array = $this->model->images_for_star_rating($element_value, $label_id);
                $edit_stars = $star_rating_array[0];
                $stars_value = $star_rating_array[1];
                ?>
        <tr>
          <td class="key">
            <label for="title"><?php echo $labels_name[$key]; ?></label>
          </td>
          <td>
            <input type="hidden" id="<?php echo $label_id; ?>_star_amountform_id_temp" name="<?php echo $label_id; ?>_star_amountform_id_temp" value="<?php echo $stars_value[0]; ?>">
            <input type="hidden" name="<?php echo $label_id; ?>_star_colorform_id_temp" id="<?php echo $label_id; ?>_star_colorform_id_temp" value="<?php echo $stars_value[2]; ?>">
            <input type="hidden" id="<?php echo $label_id; ?>_selected_star_amountform_id_temp" name="<?php echo $label_id; ?>_selected_star_amountform_id_temp" value="<?php echo $stars_value[1]; ?>">
            <?php echo $edit_stars; ?>
            <input type="hidden" name="submission_<?php echo $label_id; ?>" id="submission_<?php echo $label_id; ?>" value="<?php echo $element_value; ?>" size="80" />
          </td>
        </tr>
                <?php
                break;
              case "type_scale_rating":
                $scale_rating_array = $this->model->params_for_scale_rating($element_value, $label_id);
                $scale = $scale_rating_array[0];
                $scale_radio = $scale_rating_array[1];
                $checked = $scale_rating_array[2];
                ?>
				<tr>
          <td class="key">
            <label for="title"><?php echo $labels_name[$key]; ?></label>
          </td>
          <td>
            <input type="hidden" id="<?php echo $label_id; ?>_scale_checkedform_id_temp" name="<?php echo $label_id; ?>_scale_checkedform_id_temp" value="<?php echo $scale_radio[1]; ?>">
            <?php echo $scale; ?>
            <input type="hidden" name="submission_<?php echo $label_id; ?>" id="submission_<?php echo $label_id; ?>" value="<?php echo $element_value; ?>" size="80" />
          </td>
				</tr>
                <?php
                break;
              case 'type_range':
                $range = $this->model->params_for_type_range($element_value, $label_id);
                ?>
				<tr>
          <td class="key">
            <label for="title"><?php echo $labels_name[$key]; ?></label>
          </td>
          <td>
            <?php echo $range; ?>
            <input type="hidden" name="submission_<?php echo $label_id; ?>" id="submission_<?php echo $label_id; ?>" value="<?php echo $element_value; ?>" size="80" />
          </td>
				</tr>
                <?php
                break;
              case 'type_spinner':
                ?>
				<tr>
          <td class="key">
            <label for="title"><?php echo $labels_name[$key]; ?></label>
          </td>
          <td>
            <input type="text" name="submission_<?php echo $label_id; ?>" id="submission_<?php echo $label_id; ?>" value="<?php echo str_replace("*@@url@@*", '', $element_value); ?>" size="20" />
          </td>
        </tr>
                <?php
                break;
              case 'type_grading':
                $type_grading_array = $this->model->params_for_type_grading($element_value, $label_id);
                $garding = $type_grading_array[0];
                $garding_value = $type_grading_array[1];
                $sum = $type_grading_array[2];
                $items_count = $type_grading_array[3];
                $element_value1 = $type_grading_array[4];
                ?>
				<tr>
          <td class="key">
            <label for="title"><?php echo $labels_name[$key]; ?></label>
          </td>
          <td>
            <?php echo $garding; ?>
            <span id="<?php echo $label_id; ?>_grading_sumform_id_temp"><?php echo $sum; ?></span>/<span id="<?php echo $label_id; ?>_grading_totalform_id_temp"><?php echo $garding_value[$items_count]; ?></span><span id="<?php echo $label_id; ?>_text_elementform_id_temp"></span>
            <input type="hidden"  id="<?php echo $label_id; ?>_element_valueform_id_temp" name="<?php echo $label_id; ?>_element_valueform_id_temp" value="<?php echo $element_value1; ?>" />
            <input type="hidden"  id="<?php echo $label_id; ?>_grading_totalform_id_temp" name="<?php echo $label_id; ?>_grading_totalform_id_temp" value="<?php echo $garding_value[$items_count]; ?>" />
            <input type="hidden" name="submission_<?php echo $label_id; ?>" id="submission_<?php echo $label_id; ?>" value="<?php echo $element_value; ?>" size="80" />
          </td>
				</tr>
                <?php
                break;
              case 'type_matrix':
                $type_matrix_array = $this->model->params_for_type_matrix($element_value, $label_id);
                $matrix = $type_matrix_array[0];
                $new_filename = $type_matrix_array[1];
                ?>
				<tr>
          <td class="key">
            <label for="title"><?php echo $labels_name[$key]; ?></label>
          </td>
          <td>
            <input type="hidden"  id="<?php echo $label_id; ?>_matrixform_id_temp" name="<?php echo $label_id; ?>_matrixform_id_temp" value="<?php echo $new_filename; ?>">
            <?php echo $matrix; ?>
            <input type="hidden" name="submission_<?php echo $label_id; ?>" id="submission_<?php echo $label_id; ?>" value="<?php echo $element_value; ?>" size="80" />
          </td>
				</tr>
                <?php
                break;
              default: 
              ?>
				<tr>
          <td class="key">
            <label for="title"><?php echo $labels_name[$key]; ?></label>
          </td>
          <td>
            <input type="text" name="submission_<?php echo $label_id; ?>" id="submission_<?php echo $label_id; ?>" value="<?php echo str_replace("*@@url@@*", '', $element_value); ?>" size="80" />
          </td>
        </tr>
              <?php
                break;
            }
          }
        }
        ?>
      </table>
      <input type="hidden" name="option" value="com_formmaker"/>
      <input type="hidden" id="current_id" name="current_id" value="<?php echo $rows[0]->group_id; ?>" />
      <input type="hidden" name="form_id" value="<?php echo $rows[0]->form_id; ?>" />
      <input type="hidden" name="date" value="<?php echo $rows[0]->date; ?>" />
      <input type="hidden" name="ip" value="<?php echo $rows[0]->ip; ?>" />
      <input type="hidden" id="task" name="task" value="" />
      <input type="hidden" value="<?php echo WD_FMC_URL; ?>" id="form_plugins_url" />
      <script>
        plugin_url = document.getElementById('form_plugins_url').value;
      </script>
    </form>
	  <?php
  }

  public function new_edit($id) {
    $current_id = ((isset($id)) ? $id : 0);
    $params = $this->model->get_data_of_group_id($current_id);
    $rows = $params[0]; 
    $labels_id = $params[1]; 
    $labels_name = $params[2]; 
    $labels_type = $params[3];
    $ispaypal = $params[4];
    $form = $params[5];
    $form_theme = $params[6];
    $userinfo = get_userdata($rows[0]->user_id_wd);
    $username = $userinfo ? $userinfo->display_name : "";
    $useremail = $userinfo ? $userinfo->user_email : "";
    ?>
	<div class="fm-user-manual">
		This section allows you to edit form submissions.
		<a style="color: blue; text-decoration: none;" target="_blank" href="https://web-dorado.com/wordpress-form-maker-guide-6.html">Read More in User Manual</a>
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
    <form action="admin.php?page=submissions_fmc"  method="post" id="formform_id_temp" name="formform_id_temp">
		<?php wp_nonce_field('nonce_fmc', 'nonce_fmc'); ?>
		<div class="fm-page-header">
			<div class="fm-page-title">Edit Submission</div>
			<div class="fm-page-actions">
				<button class="fm-button save-button small" onclick="pressbutton(); fm_set_input_value('task', 'save');	 fm_set_input_value('current_id', <?php echo $current_id ;?>); fm_form_submit(event, 'formform_id_temp');">
					<span></span>
					Save
				</button>
				<button class="fm-button apply-button small" onclick="pressbutton(); fm_set_input_value('task', 'apply');	 fm_set_input_value('current_id', <?php echo $current_id ;?>); fm_form_submit(event, 'formform_id_temp');">
					<span></span>
					Apply
				</button>
				<button class="fm-button cancel-button small" onclick="fm_set_input_value('task', '');fm_form_submit(event, 'formform_id_temp');">
					<span></span>
					Cancel
				</button>
			</div>
			<div class="fm-clear"></div>
		</div>
		<div class="fm-submissins-edit">
		<table>
			<tr>
				<td class="fm-key"><label for="ID">ID: </label></td>
				<td><?php echo $rows[0]->group_id; ?></td>
			</tr>	
			<tr>
				<td class="fm-key"><label for="Date">Date: </label></td>
				<td><?php echo $rows[0]->date; ?></td>
			</tr>
			<tr>
				<td class="fm-key"><label for="IP">IP: </label></td>
				<td><?php echo $rows[0]->ip; ?></td>
			</tr>
			<tr>
				<td class="fm-key"><label for="Submitter's Username">Submitter's Username: </label></td>
				<td><?php echo $username; ?></td>
			</tr>
			<tr>
				<td class="fm-key"><label for="Submitter's Email Address">Submitter's Email Address: </label></td>
				<td><?php echo $useremail; ?></td>
			</tr>
		</table>
		<?php
		$css_rep1 = array("[SITE_ROOT]");
		$css_rep2 = array(WD_FMC_URL);
		$order = array("\r\n", "\n", "\r");
		$form_theme = str_replace($order, '', $form_theme);
		$form_theme = str_replace($css_rep1, $css_rep2, $form_theme);
		$form_theme = "#form" . $form->id . ' ' . $form_theme;
		?>
		<style>
        <?php
        echo $form_theme;
        ?>
        .wdform-page-and-images{
          width: 50%;
        }
        .wdform-page-and-images div {
          background-color: rgba(0, 0, 0, 0);
        }
      </style>
      <?php	
      $form_currency = '$';
      $check_js = '';
      $onload_js = '';
      $onsubmit_js = '';
      $currency_code = array('USD', 'EUR', 'GBP', 'JPY', 'CAD', 'MXN', 'HKD', 'HUF', 'NOK', 'NZD', 'SGD', 'SEK', 'PLN', 'AUD', 'DKK', 'CHF', 'CZK', 'ILS', 'BRL', 'TWD', 'MYR', 'PHP', 'THB');
      $currency_sign = array('$'  , '€'  , '£'  , '¥'  , 'C$', 'Mex$', 'HK$', 'Ft' , 'kr' , 'NZ$', 'S$' , 'kr' , 'zl' , 'A$' , 'kr' , 'CHF' , 'Kc', '?'  , 'R$' , 'NT$', 'RM' , '?'  , '?'  );
      $is_type	= array();
      $id1s = array();
      $types = array();
      $labels = array();
      $paramss = array();
      $fields = explode('*:*new_field*:*', $form->form_fields);
      $fields = array_slice($fields, 0, count($fields) - 1);   
      foreach ($fields as $field) {
        $temp = explode('*:*id*:*',$field);
        array_push($id1s, $temp[0]);
        $temp = explode('*:*type*:*', $temp[1]);
        array_push($types, $temp[0]);
        $temp = explode('*:*w_field_label*:*', $temp[1]);
        array_push($labels, $temp[0]);
        array_push($paramss, $temp[1]);
      }
      $form = $form->form_front;
      $form_id = 'form_id_temp';
      $start = 0;
      foreach ($id1s as $id1s_key => $id1) {
        $label = $labels[$id1s_key];
        $type = $types[$id1s_key];
        $params = $paramss[$id1s_key];
        if ($type != 'type_address') {
          foreach ($rows as $row) {
            if ($row->element_label == $id1) {		
              $element_value =	$row->element_value;
              break;
            }
            else {
              $element_value =	'';
            }
          }
        }
        else {
          for ($i = 0; $i < 6; $i++) {
            $address_value = '';
            foreach ($rows as $row) {
              if ($row->element_label == (string)((int) $id1 + $i)) {
                $address_value = $row->element_value;
              }
            }
            $elements_of_address[$i] = $address_value;
          }
        }
        if (strpos($form, '%' . $id1 . ' - ' . $label . '%')) {
          $rep = '';
          $param = array();
          $param['attributes'] = '';
          $is_type[$type] = TRUE;
          switch ($type) {
            case 'type_section_break':
            case 'type_editor':
            case 'type_file_upload':
            case 'type_captcha':		
            case 'type_recaptcha':
            case 'type_mark_map':	
            case 'type_map':
            case 'type_submit_reset':
            case 'type_button':
            case 'type_paypal_total':
              break;
            
            case 'type_text': {
              $params_names = array('w_field_label_size','w_field_label_pos','w_size','w_first_val','w_title','w_required','w_unique');
              $temp = $params;
              foreach ($params_names as $params_name ) {
                $temp = explode('*:*'.$params_name.'*:*', $temp);
                $param[$params_name] = $temp[0];
                $temp = $temp[1];
              }
              if ($temp) {	
                $temp	= explode('*:*w_attr_name*:*', $temp);
                $attrs = array_slice($temp, 0, count($temp) - 1);   
                foreach ($attrs as $attr) {
                  $param['attributes'] = $param['attributes'].' '.$attr;
                }
              }
              $wdformfieldsize = ($param['w_field_label_pos'] == "left" ? $param['w_field_label_size']+$param['w_size'] : max($param['w_field_label_size'],$param['w_size']));
              $param['w_field_label_pos'] = ($param['w_field_label_pos'] == "left" ? "float: left;" : "display:block;");
              $rep ='<div type="type_text" class="wdform-field" style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              $rep.='</div><div class="wdform-element-section" style="width: '.$param['w_size'].'px;"  ><input type="text" class="" id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.$element_value.'" style="width: 100%;" '.$param['attributes'].'></div></div>';
              break;
            }

            case 'type_number': {
              $params_names = array('w_field_label_size','w_field_label_pos','w_size','w_first_val','w_title','w_required','w_unique','w_class');
              $temp = $params;
              foreach ($params_names as $params_name ) {	
                $temp = explode('*:*'.$params_name.'*:*', $temp);
                $param[$params_name] = $temp[0];
                $temp = $temp[1];
              }
              if ($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' '.$attr;
              }				
              
              $wdformfieldsize = ($param['w_field_label_pos']=="left" ? $param['w_field_label_size']+$param['w_size'] : max($param['w_field_label_size'],$param['w_size']));	
              $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	
              
              $rep ='<div type="type_number" class="wdform-field" style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section"  class="'.$param['w_class'].'" style="'.$param['w_field_label_pos'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="width: '.$param['w_size'].'px;"><input type="text" class="" id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.$element_value.'"  style="width: 100%;" '.$param['attributes'].'></div></div>';
              
              break;
            }

            case 'type_password': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_size','w_required','w_unique','w_class');
              $temp=$params;

              foreach($params_names as $params_name ) {	
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
                        
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' '.$attr;
              }
          
              $wdformfieldsize = ($param['w_field_label_pos']=="left" ? $param['w_field_label_size']+$param['w_size'] : max($param['w_field_label_size'],$param['w_size']));	
              $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	
                

              $rep ='<div type="type_password" class="wdform-field" style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section"  class="'.$param['w_class'].'" style="'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="width: '.$param['w_size'].'px;"><input type="password" id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.$element_value.'" style="width: 100%;" '.$param['attributes'].'></div></div>';
              
              
              break;
            }

            case 'type_textarea': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_size_w','w_size_h','w_first_val','w_title','w_required','w_unique','w_class');
              $temp=$params;

              foreach($params_names as $params_name ) {	
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              
              
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' '.$attr;
              }
            
            
                
              $wdformfieldsize = ($param['w_field_label_pos']=="left" ? $param['w_field_label_size']+$param['w_size_w'] : max($param['w_field_label_size'],$param['w_size_w']));	
              $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	
            
            
              $rep ='<div type="type_textarea" class="wdform-field"  style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="width: '.$param['w_size_w'].'px"><textarea class="" id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" title="'.$param['w_title'].'"  style="width: 100%; height: '.$param['w_size_h'].'px;" '.$param['attributes'].'>'.$element_value.'</textarea></div></div>';

              

              break;
            }

            case 'type_wdeditor': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_size_w','w_size_h','w_title','w_required','w_class');
              $temp=$params;
            
              foreach($params_names as $params_name ) {	
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' '.$attr;
              }
                      
              $wdformfieldsize = ($param['w_field_label_pos']=="left" ? $param['w_field_label_size']+$param['w_size_w']+10 : max($param['w_field_label_size'],$param['w_size_w']));	
              $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	
                                    
              $rep ='<div type="type_wdeditor" class="wdform-field"  style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
            
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="width: '.$param['w_size_w'].'px">';
              
              if(user_can_richedit()) {
                ob_start();
                wp_editor($element_value, 'wdform_'.$id1.'_wd_editor'.$form_id, array('teeny' => FALSE, 'media_buttons' => FALSE, 'textarea_rows' => 5));
                $wd_editor = ob_get_clean();
              }
              else {
                $wd_editor='
                <textarea  class="'.$param['w_class'].'" name="wdform_'.$id1.'_wd_editor'.$form_id.'" id="wdform_'.$id1.'_wd_editor'.$form_id.'" style="width: '.$param['w_size_w'].'px; height: '.$param['w_size_h'].'px; " class="mce_editable" aria-hidden="true">'.$element_value.'</textarea>';
              }	
              
              $rep.= $wd_editor.'</div></div>';
      
              break;
            }

            case 'type_phone': {
            
              if($element_value=='')
                $element_value = ' ';
                
                $params_names=array('w_field_label_size','w_field_label_pos','w_size','w_first_val','w_title','w_mini_labels','w_required','w_unique', 'w_class');
                $temp=$params;

                foreach($params_names as $params_name ) {	
                  $temp=explode('*:*'.$params_name.'*:*',$temp);
                  $param[$params_name] = $temp[0];
                  $temp=$temp[1];
                } 
                
                if($temp) {	
                  $temp	=explode('*:*w_attr_name*:*',$temp);
                  $attrs	= array_slice($temp,0, count($temp)-1);   
                  foreach($attrs as $attr)
                    $param['attributes'] = $param['attributes'].' '.$attr;
                }
                
                $element_value = explode(' ',$element_value);
              
                $wdformfieldsize = ($param['w_field_label_pos']=="left" ? ($param['w_field_label_size']+$param['w_size']+65) : max($param['w_field_label_size'],($param['w_size']+65)));	
                $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");		
                
                $w_mini_labels = explode('***',$param['w_mini_labels']);
            
                $rep ='<div type="type_phone" class="wdform-field" style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label" >'.$label.'</span>';
                
                $rep.='
                </div>
                <div class="wdform-element-section '.$param['w_class'].'" style="width: '.($param['w_size']+65).'px;">
                  <div style="display: table-cell;vertical-align: middle;">
                    <div><input type="text" class="" id="wdform_'.$id1.'_element_first'.$form_id.'" name="wdform_'.$id1.'_element_first'.$form_id.'" value="'.$element_value[0].'" style="width: 50px;" '.$param['attributes'].'></div>
                    <div><label class="mini_label">'.$w_mini_labels[0].'</label></div>
                  </div>
                  <div style="display: table-cell;vertical-align: middle;">
                    <div class="wdform_line" style="margin: 0px 4px 10px 4px; padding: 0px;">-</div>
                  </div>
                  <div style="display: table-cell;vertical-align: middle; width:100%;">
                    <div><input type="text" class="" id="wdform_'.$id1.'_element_last'.$form_id.'" name="wdform_'.$id1.'_element_last'.$form_id.'" value="'.$element_value[1].'" style="width: 100%;" '.$param['attributes'].'></div>
                    <div><label class="mini_label">'.$w_mini_labels[1].'</label></div>
                  </div>
                </div>
                </div>';

              break;
            }

            case 'type_name': {
            
              if($element_value =='')
                $element_value = '@@@';
              
               $params_names = array('w_field_label_size', 'w_field_label_pos', 'w_first_val', 'w_title', 'w_mini_labels', 'w_size', 'w_name_format', 'w_required', 'w_unique', 'w_class');
               $temp = $params;
			   if(strpos($temp, 'w_name_fields') > -1)
				$params_names = array('w_field_label_size', 'w_field_label_pos', 'w_first_val', 'w_title', 'w_mini_labels', 'w_size', 'w_name_format', 'w_required', 'w_unique', 'w_class', 'w_name_fields');

                foreach($params_names as $params_name ) {	
                  $temp=explode('*:*'.$params_name.'*:*',$temp);
                  $param[$params_name] = $temp[0];
                  $temp=$temp[1];
                }
                
                
                if($temp) {	
                  $temp	= explode('*:*w_attr_name*:*',$temp);
                  $attrs = array_slice($temp,0, count($temp)-1);   
                  foreach($attrs as $attr)
                    $param['attributes'] = $param['attributes'].' '.$attr;
                }
                
                $w_mini_labels = explode('***',$param['w_mini_labels']);
			    $param['w_name_fields'] = isset($param['w_name_fields']) ? $param['w_name_fields'] : ($param['w_name_format'] == 'normal' ? 'no***no' : 'yes***yes');
			    $w_name_fields = explode('***', $param['w_name_fields']);
                $element_value = explode('@@@', $element_value);
				
				if($w_name_fields[0]== 'no' && $w_name_fields[1]== 'no' ) {
					$w_name_format = '
						<div style="display: table-cell; width:50%">
						  <div><input type="text" class="" id="wdform_'.$id1.'_element_first'.$form_id.'" name="wdform_'.$id1.'_element_first'.$form_id.'" value="'.(count($element_value)==2 ? $element_value[0] : $element_value[1]).'" style="width: 100%;"'.$param['attributes'].'></div>
						  <div><label class="mini_label">'.$w_mini_labels[1].'</label></div>
						</div>
						<div style="display:table-cell;"><div style="margin: 0px 8px; padding: 0px;"></div></div>
						<div  style="display: table-cell; width:50%">
						  <div><input type="text" class="" id="wdform_'.$id1.'_element_last'.$form_id.'" name="wdform_'.$id1.'_element_last'.$form_id.'" value="'.(count($element_value)==2 ? $element_value[1] : $element_value[2]).'" style="width: 100%;" '.$param['attributes'].'></div>
						  <div><label class="mini_label">'.$w_mini_labels[2].'</label></div>
						</div>
						';
					$w_size=2*$param['w_size'];
				 }
				else {
					$first_last_size = $w_name_fields[0] == 'yes' && $w_name_fields[1] == 'no' ? 45 : 30;
					$w_name_format = '
						<div style="display: table-cell; width:30%">
						  <div><input type="text" class="" id="wdform_'.$id1.'_element_first'.$form_id.'" name="wdform_'.$id1.'_element_first'.$form_id.'" value="'.(count($element_value)==2 ? $element_value[0] : $element_value[1]).'" style="width:100%;"></div>
						  <div><label class="mini_label">'.$w_mini_labels[1].'</label></div>
						</div>
						<div style="display:table-cell;"><div style="margin: 0px 4px; padding: 0px;"></div></div>
						<div style="display: table-cell; width:30%">
						  <div><input type="text" class="" id="wdform_'.$id1.'_element_last'.$form_id.'" name="wdform_'.$id1.'_element_last'.$form_id.'" value="'.(count($element_value)==2 ? $element_value[1] : $element_value[2]).'" style="width:  100%;"></div>
						  <div><label class="mini_label">'.$w_mini_labels[2].'</label></div>
						</div>';
					$w_size = 2*$param['w_size'];
					
					if($w_name_fields[0] == 'yes') {
						$w_name_format = '
							<div style="display: table-cell;">
							  <div><input type="text" class="" id="wdform_'.$id1.'_element_title'.$form_id.'" name="wdform_'.$id1.'_element_title'.$form_id.'" value="'.(count($element_value)==2 ? "" : $element_value[0]).'" style="width: 40px;"></div>
							  <div><label class="mini_label">'.$w_mini_labels[0].'</label></div>
							</div>
							<div style="display:table-cell;"><div style="margin: 0px 1px; padding: 0px;"></div></div>'.$w_name_format;
						$w_size	+= 80;
					}
					if($w_name_fields[1] == 'yes') {
						$w_name_format = $w_name_format.'
							<div style="display:table-cell;"><div style="margin: 0px 4px; padding: 0px;"></div></div>
							<div style="display: table-cell; width:30%">
							  <div><input type="text" class="" id="wdform_'.$id1.'_element_middle'.$form_id.'" name="wdform_'.$id1.'_element_middle'.$form_id.'" value="'.(count($element_value)==2 ? "" : $element_value[3]).'"style="width: 100%;"></div>
							  <div><label class="mini_label">'.$w_mini_labels[3].'</label></div>
							</div>						
							';
						$w_size	+= $param['w_size'];		
					}		
				}
				
                $wdformfieldsize = ($param['w_field_label_pos']=="left" ? ($param['w_field_label_size']+$w_size) : max($param['w_field_label_size'],$w_size));	
                $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	

                $rep ='<div type="type_name" class="wdform-field"  style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
                
                $rep.='</div>
                <div class="wdform-element-section '.$param['w_class'].'" style="width: '.$w_size.'px;">'.$w_name_format.'</div></div>';
        
              break;
            }
            
            case 'type_address': {
              $params_names = array('w_field_label_size','w_field_label_pos','w_size','w_mini_labels','w_disabled_fields','w_required','w_class');
              $temp = $params;
              foreach($params_names as $params_name ) {	
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' '.$attr;
              }
              
              $wdformfieldsize = ($param['w_field_label_pos']=="left" ? ($param['w_field_label_size']+$param['w_size']) : max($param['w_field_label_size'], $param['w_size']));	
              $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	
              
              $w_mini_labels = explode('***',$param['w_mini_labels']);
              $w_disabled_fields = explode('***',$param['w_disabled_fields']);
            
              $rep ='<div type="type_address" class="wdform-field"  style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';		
          
              $address_fields ='';
              $g=0;
              if (isset($w_disabled_fields[0]) && $w_disabled_fields[0]=='no') {
                  $g+=2;
                  $address_fields .= '<span style="float: left; width: 100%; padding-bottom: 8px; display: block;"><input type="text" id="wdform_'.$id1.'_street1'.$form_id.'" name="wdform_'.$id1.'_street1'.$form_id.'" value="'.$elements_of_address[0].'" style="width: 100%;" '.$param['attributes'].'><label class="mini_label" >'.$w_mini_labels[0].'</label></span>';
              }
              if (isset($w_disabled_fields[1]) && $w_disabled_fields[1]=='no') {
                $g+=2;
                $address_fields .= '<span style="float: left; width: 100%; padding-bottom: 8px; display: block;"><input type="text" id="wdform_'.$id1.'_street2'.$form_id.'" name="wdform_'.($id1+1).'_street2'.$form_id.'" value="'.$elements_of_address[1].'" style="width: 100%;" '.$param['attributes'].'><label class="mini_label" >'.$w_mini_labels[1].'</label></span>';
              }
              if (isset($w_disabled_fields[2]) && $w_disabled_fields[2]=='no') {
                $g++;
                $address_fields .= '<span style="float: left; width: 48%; padding-bottom: 8px;"><input type="text" id="wdform_'.$id1.'_city'.$form_id.'" name="wdform_'.($id1+2).'_city'.$form_id.'" value="'.$elements_of_address[2].'" style="width: 100%;" '.$param['attributes'].'><label class="mini_label" >'.$w_mini_labels[2].'</label></span>';
              }
              if (isset($w_disabled_fields[3]) && $w_disabled_fields[3]=='no') {
                $g++;												
                $w_states = array("","Alabama","Alaska", "Arizona","Arkansas","California","Colorado","Connecticut","Delaware","District Of Columbia","Florida","Georgia","Hawaii","Idaho","Illinois","Indiana","Iowa","Kansas","Kentucky","Louisiana","Maine","Maryland","Massachusetts","Michigan","Minnesota","Mississippi","Missouri","Montana","Nebraska","Nevada","New Hampshire","New Jersey","New Mexico","New York","North Carolina","North Dakota","Ohio","Oklahoma","Oregon","Pennsylvania","Rhode Island","South Carolina","South Dakota","Tennessee","Texas","Utah","Vermont","Virginia","Washington","West Virginia","Wisconsin","Wyoming");	
                $w_state_options = '';
                foreach($w_states as $w_state) {						
                  if($w_state == $elements_of_address[3])					
                    $selected = 'selected=\"selected\"';
                  else
                    $selected = '';
                  $w_state_options .= '<option value="'.$w_state.'" '.$selected.'>'.$w_state.'</option>';
                }
                if(isset($w_disabled_fields[5]) && $w_disabled_fields[5]=='yes' && isset($w_disabled_fields[6]) && $w_disabled_fields[6]=='yes') {
                  $address_fields .= '<span style="float: '.(($g%2==0) ? 'right' : 'left').'; width: 48%; padding-bottom: 8px;"><select type="text" id="wdform_'.$id1.'_state'.$form_id.'" name="wdform_'.($id1+3).'_state'.$form_id.'" style="width: 100%;" '.$param['attributes'].'>'.$w_state_options.'</select><label class="mini_label" style="display: block;" id="'.$id1.'_mini_label_state">'.$w_mini_labels[3].'</label></span>';
                }
                else
                  $address_fields .= '<span style="float: '.(($g%2==0) ? 'right' : 'left').'; width: 48%; padding-bottom: 8px;"><input type="text" id="wdform_'.$id1.'_state'.$form_id.'" name="wdform_'.($id1+3).'_state'.$form_id.'" value="'.$elements_of_address[3].'" style="width: 100%;" '.$param['attributes'].'><label class="mini_label">'.$w_mini_labels[3].'</label></span>';
              }
              if (isset($w_disabled_fields[4]) && $w_disabled_fields[4]=='no') {
                $g++;
                $address_fields .= '<span style="float: '.(($g%2==0) ? 'right' : 'left').'; width: 48%; padding-bottom: 8px;"><input type="text" id="wdform_'.$id1.'_postal'.$form_id.'" name="wdform_'.($id1+4).'_postal'.$form_id.'" value="'.$elements_of_address[4].'" style="width: 100%;" '.$param['attributes'].'><label class="mini_label">'.$w_mini_labels[4].'</label></span>';
              }
              $w_countries = array("","Afghanistan","Albania","Algeria","Andorra","Angola","Antigua and Barbuda","Argentina","Armenia","Australia","Austria","Azerbaijan","Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bhutan","Bolivia","Bosnia and Herzegovina","Botswana","Brazil","Brunei","Bulgaria","Burkina Faso","Burundi","Cambodia","Cameroon","Canada","Cape Verde","Central African Republic","Chad","Chile","China","Colombia","Comoros","Congo (Brazzaville)","Congo","Costa Rica","Cote d'Ivoire","Croatia","Cuba","Cyprus","Czech Republic","Denmark","Djibouti","Dominica","Dominican Republic","East Timor (Timor Timur)","Ecuador","Egypt","El Salvador","Equatorial Guinea","Eritrea","Estonia","Ethiopia","Fiji","Finland","France","Gabon","Gambia, The","Georgia","Germany","Ghana","Greece","Grenada","Guatemala","Guinea","Guinea-Bissau","Guyana","Haiti","Honduras","Hungary","Iceland","India","Indonesia","Iran","Iraq","Ireland","Israel","Italy","Jamaica","Japan","Jordan","Kazakhstan","Kenya","Kiribati","Korea, North","Korea, South","Kuwait","Kyrgyzstan","Laos","Latvia","Lebanon","Lesotho","Liberia","Libya","Liechtenstein","Lithuania","Luxembourg","Macedonia","Madagascar","Malawi","Malaysia","Maldives","Mali","Malta","Marshall Islands","Mauritania","Mauritius","Mexico","Micronesia","Moldova","Monaco","Mongolia","Morocco","Mozambique","Myanmar","Namibia","Nauru","Nepal","Netherlands","New Zealand","Nicaragua","Niger","Nigeria","Norway","Oman","Pakistan","Palau","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Poland","Portugal","Qatar","Romania","Russia","Rwanda","Saint Kitts and Nevis","Saint Lucia","Saint Vincent","Samoa","San Marino","Sao Tome and Principe","Saudi Arabia","Senegal","Serbia and Montenegro","Seychelles","Sierra Leone","Singapore","Slovakia","Slovenia","Solomon Islands","Somalia","South Africa","Spain","Sri Lanka","Sudan","Suriname","Swaziland","Sweden","Switzerland","Syria","Taiwan","Tajikistan","Tanzania","Thailand","Togo","Tonga","Trinidad and Tobago","Tunisia","Turkey","Turkmenistan","Tuvalu","Uganda","Ukraine","United Arab Emirates","United Kingdom","United States","Uruguay","Uzbekistan","Vanuatu","Vatican City","Venezuela","Vietnam","Yemen","Zambia","Zimbabwe");	
              $w_options = '';
              foreach($w_countries as $w_country) {
                if($w_country == $elements_of_address[5])					
                  $selected = 'selected="selected"';
                else
                  $selected = '';
                $w_options .= '<option value="'.$w_country.'" '.$selected.'>'.$w_country.'</option>';
              }
            
              if (isset($w_disabled_fields[5]) && $w_disabled_fields[5]=='no') {
                $g++;
                $address_fields .= '<span style="float: '.(($g%2==0) ? 'right' : 'left').'; width: 48%; padding-bottom: 8px;display: inline-block;"><select type="text" id="wdform_'.$id1.'_country'.$form_id.'" name="wdform_'.($id1+5).'_country'.$form_id.'" style="width:100%" '.$param['attributes'].'>'.$w_options.'</select><label class="mini_label">'.$w_mini_labels[5].'</span>';
              }				

            
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="width: '.$param['w_size'].'px;"><div>
              '.$address_fields.'</div></div></div>';	
              break;
            }

            case 'type_submitter_mail': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_size','w_first_val','w_title','w_required','w_unique', 'w_class');
              $temp=$params;

              foreach($params_names as $params_name ) {	
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
              
              
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' '.$attr;
              }
              
              
              $wdformfieldsize = ($param['w_field_label_pos']=="left" ? ($param['w_field_label_size']+$param['w_size']) : max($param['w_field_label_size'], $param['w_size']));	
              $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	
              
              $rep ='<div type="type_submitter_mail" class="wdform-field"  style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="width: '.$param['w_size'].'px;"><input type="text" class="" id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.$element_value.'" title="'.$param['w_title'].'"  style="width: 100%;" '.$param['attributes'].'></div></div>';
              
            
              
              break;
            }

            case 'type_checkbox':

					{

					

						$params_names=array('w_field_label_size','w_field_label_pos','w_flow','w_choices','w_choices_checked','w_rowcol', 'w_required','w_randomize','w_allow_other','w_allow_other_num','w_class');

						$temp=$params;

						if(strpos($temp, 'w_field_option_pos') > -1)

							$params_names=array('w_field_label_size','w_field_label_pos','w_field_option_pos','w_flow','w_choices','w_choices_checked','w_rowcol', 'w_required','w_randomize','w_allow_other','w_allow_other_num', 'w_value_disabled','w_choices_value', 'w_choices_params', 'w_class');

							

						foreach($params_names as $params_name )

						{	

							$temp=explode('*:*'.$params_name.'*:*',$temp);

							$param[$params_name] = $temp[0];

							$temp=$temp[1];

						}

						

						if($temp)

						{	

							$temp	=explode('*:*w_attr_name*:*',$temp);

							$attrs	= array_slice($temp,0, count($temp)-1);   

							foreach($attrs as $attr)

								$param['attributes'] = $param['attributes'].' '.$attr;

						}

						

						$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	

						if(!isset($param['w_value_disabled']))

						$param['w_value_disabled'] = 'no';

						

						if(!isset($param['w_field_option_pos']))

						$param['w_field_option_pos'] = 'left';

						

						$param['w_field_option_pos1'] = ($param['w_field_option_pos']=="right" ? "style='float: none !important;'" : "");

						$param['w_field_option_pos2'] = ($param['w_field_option_pos']=="right" ? "style='float: left !important; margin-right: 8px !important; display: inline-block !important;'" : "");	

						

						$param['w_choices']	= explode('***',$param['w_choices']);

						if(isset($param['w_choices_value']))

						{

							$param['w_choices_value'] = explode('***',$param['w_choices_value']);

							$param['w_choices_params'] = explode('***',$param['w_choices_params']);	

						}

						

						$element_value	= explode('***br***',$element_value);

						$element_value 	= array_slice($element_value,0, count($element_value)-1); 

						$is_other=false;

						$other_value = '';



						foreach($element_value as $key => $value)

						{

							if(!in_array($value, ($param['w_value_disabled']=='no' ? $param['w_choices'] : (isset($param['w_choices_value']) ? $param['w_choices_value'] : array()))))

							{

								$other_value = $value;

								$is_other=true;

								break;

							}

						}



						$rep='<div type="type_checkbox" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';

						

						$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos'].';">';

					

						$rep.='<div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).'; vertical-align:top">';

						$total_queries = 0;

						foreach($param['w_choices'] as $key => $choice)

						{	

							$key1 = $key + $total_queries;

							if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])

							{	

							$choices_labels =array();
							$choices_values = array();

							$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
							$where = (str_replace(array('[',']'), '', $w_choices_params[0]) ? ' WHERE '.str_replace(array('[',']'), '', $w_choices_params[0]) : '');
							$w_choices_params = explode('[db_info]',$w_choices_params[1]);
							
							$order_by = str_replace(array('[',']'), '', $w_choices_params[0]);
							$db_info = str_replace(array('[',']'), '', $w_choices_params[1]);
						
		

								$label_table_and_column = explode(':',str_replace(array('[',']'), '', $choice));

								$table = $label_table_and_column[0];

								$label_column = $label_table_and_column[1];

								if($label_column)

								{
								$choices_labels = $this->model->select_data_from_db_for_labels($db_info, $label_column, $table, $where, $order_by);					

								}	



								$value_table_and_column = explode(':',str_replace(array('[',']'), '', $param['w_choices_value'][$key]));

								$value_column = $value_table_and_column[1];



								if($value_column)

								{
								$choices_values = $this->model->select_data_from_db_for_values($db_info, $value_column, $table, $where, $order_by);

								}		

								$columns_count_checkbox = count($choices_labels)>0 ?  count($choices_labels) : count($choices_values);

								

								if(array_filter($choices_labels) || array_filter($choices_values))

								{

									$total_queries = $total_queries + $columns_count_checkbox-1;

									

									if(!isset($post_value))

										$param['w_choices_checked'][$key]=($param['w_choices_checked'][$key]=='true' ? 'checked="checked"' : '');

										

									for($k=0; $k<$columns_count_checkbox; $k++)

									{

										$choice_label = isset($choices_labels[$k]) ? $choices_labels[$k] : '';

										$choice_value = isset($choices_values[$k]) ? $choices_values[$k] : $choice_label;

										if(($key1+$k)%$param['w_rowcol']==0 && ($key1+$k)>0)

											$rep.='</div><div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';  vertical-align:top">';

										

										$checked=(in_array($choice_value, $element_value) ? 'checked="checked"' : '');

										

										$rep.='<div style="display: '.($param['w_flow']!='hor' ? 'table-cell' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" '.$param['w_field_option_pos1'].'>'.$choice_label.'</label><div class="checkbox-div forlabs" '.$param['w_field_option_pos2'].'><input type="checkbox" '.(($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key) ? 'other="1"' : ''	).' id="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" name="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" value="'.htmlspecialchars($choice_value).'" '.(($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key) ? 'onclick="if(set_checked(&quot;wdform_'.$id1.'&quot;,&quot;'.($key1+$k).'&quot;,&quot;'.$form_id.'&quot;)) show_other_input(&quot;wdform_'.$id1.'&quot;,&quot;'.$form_id.'&quot;);"' : '').' '.$param['attributes'].' '.$checked.'><label for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'"></label></div></div>';

										

									}

								}	

							}	

							else

							{

								if($key1%$param['w_rowcol']==0 && $key1>0)

									$rep.='</div><div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';  vertical-align:top">';

									

								$checked=(in_array($choice, $element_value) ? 'checked="checked"' : '');

								

								if($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key && $is_other)

									$checked = 'checked="checked"';	

									

								

								$rep.='<div style="display: '.($param['w_flow']!='hor' ? 'table-cell' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.$key1.'" '.$param['w_field_option_pos1'].'>'.$choice.'</label><div class="checkbox-div forlabs" '.$param['w_field_option_pos2'].'><input type="checkbox" '.(($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key) ? 'other="1"' : ''	).' id="wdform_'.$id1.'_element'.$form_id.''.$key1.'" name="wdform_'.$id1.'_element'.$form_id.''.$key1.'" value="'.htmlspecialchars($choice).'" '.(($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key) ? 'onclick="if(set_checked(&quot;wdform_'.$id1.'&quot;,&quot;'.$key1.'&quot;,&quot;'.$form_id.'&quot;)) show_other_input(&quot;wdform_'.$id1.'&quot;,&quot;'.$form_id.'&quot;);"' : '').' '.$checked.' '.$param['attributes'].'><label for="wdform_'.$id1.'_element'.$form_id.''.$key1.'"></label></div></div>';

								

								$param['w_allow_other_num'] = $param['w_allow_other_num']==$key ? $key1 : $param['w_allow_other_num'];

							}	

						}

						$rep.='</div>';



						$rep.='</div></div>';

						

						

						if($is_other)

							$onload_js .='show_other_input("wdform_'.$id1.'","'.$form_id.'"); jQuery("#wdform_'.$id1.'_other_input'.$form_id.'").val("'.$other_value.'");';

						

						$onsubmit_js.='

							jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_allow_other'.$form_id.'\" value = \"'.$param['w_allow_other'].'\" />").appendTo("#adminForm");

							jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_allow_other_num'.$form_id.'\" value = \"'.$param['w_allow_other_num'].'\" />").appendTo("#adminForm");

							';

							

						break;

					}

            case 'type_radio':

					{

					

						

						$params_names=array('w_field_label_size','w_field_label_pos','w_flow','w_choices','w_choices_checked','w_rowcol', 'w_required','w_randomize','w_allow_other','w_allow_other_num','w_class');

						$temp=$params;

						if(strpos($temp, 'w_field_option_pos') > -1)

						$params_names=array('w_field_label_size','w_field_label_pos','w_field_option_pos','w_flow','w_choices','w_choices_checked','w_rowcol', 'w_required','w_randomize','w_allow_other','w_allow_other_num','w_value_disabled','w_choices_value', 'w_choices_params','w_class');

						

						foreach($params_names as $params_name )

						{	

							$temp=explode('*:*'.$params_name.'*:*',$temp);

							$param[$params_name] = $temp[0];

							$temp=$temp[1];

						}

						

						if($temp)

						{	

							$temp	=explode('*:*w_attr_name*:*',$temp);

							$attrs	= array_slice($temp,0, count($temp)-1);   

							foreach($attrs as $attr)

								$param['attributes'] = $param['attributes'].' '.$attr;

						}

						if(!isset($param['w_value_disabled']))

							$param['w_value_disabled'] = 'no';

					

						if(!isset($param['w_field_option_pos']))

							$param['w_field_option_pos'] = 'left';

						

						$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	

						$param['w_field_option_pos1'] = ($param['w_field_option_pos']=="right" ? "style='float: none !important;'" : "");

						$param['w_field_option_pos2'] = ($param['w_field_option_pos']=="right" ? "style='float: left !important; margin-right: 8px !important; display: inline-block !important;'" : "");

					

						$param['w_choices']	= explode('***',$param['w_choices']);

						if(isset($param['w_choices_value']))

						{

							$param['w_choices_value'] = explode('***',$param['w_choices_value']);

							$param['w_choices_params'] = explode('***',$param['w_choices_params']);	

						}

						

						$is_other=true;



						foreach($param['w_choices'] as $key => $choice)

						{

							$choice_value = isset($param['w_choices_value']) ? $param['w_choices_value'][$key] : $choice;	

							if($choice_value==$element_value)

							{

								$is_other=false;

								break;

							}

						}	

											

						$rep='<div type="type_radio" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';

						

						$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos'].';">';

					

						$rep.='<div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).'; vertical-align:top">';

						$total_queries =0;

						foreach($param['w_choices'] as $key => $choice)

						{	

							$key1 = $key + $total_queries;

							if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])

							{	

								$choices_labels =array();	
							$choices_values =array();	
							$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
							$where = (str_replace(array('[',']'), '', $w_choices_params[0]) ? ' WHERE '.str_replace(array('[',']'), '', $w_choices_params[0]) : '');
							$w_choices_params = explode('[db_info]',$w_choices_params[1]);
							$order_by = str_replace(array('[',']'), '', $w_choices_params[0]);
							$db_info = str_replace(array('[',']'), '', $w_choices_params[1]);
	
								$label_table_and_column = explode(':',str_replace(array('[',']'), '', $choice));

								$table = $label_table_and_column[0];

								$label_column = $label_table_and_column[1];

								if($label_column) {
									$choices_labels = $this->model->select_data_from_db_for_labels($db_info, $label_column, $table, $where, $order_by);
								}



								$value_table_and_column = explode(':',str_replace(array('[',']'), '', $param['w_choices_value'][$key]));

								$value_column = $value_table_and_column[1];



								if($value_column) {
                  $choices_values = $this->model->select_data_from_db_for_values($db_info, $value_column, $table, $where, $order_by);
								}	

								

								$columns_count_radio = count($choices_labels)>0 ?  count($choices_labels) : count($choices_values);

								if(array_filter($choices_labels) || array_filter($choices_values))

								{

									$total_queries = $total_queries + $columns_count_radio-1;

				

									for($k=0; $k<$columns_count_radio; $k++)

									{

										$choice_label = isset($choices_labels[$k]) ? $choices_labels[$k] : '';

										$choice_value = isset($choices_values[$k]) ? $choices_values[$k] : $choice_label;

											

										if(($key1+$k)%$param['w_rowcol']==0 && ($key1+$k)>0)

											$rep.='</div><div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';  vertical-align:top">';



										$checked =($choice_value==$element_value ? 'checked="checked"' : '');

										if($choice_value==$element_value)

											$is_other=false;

										

										$rep.='<div style="display: '.($param['w_flow']!='hor' ? 'table-cell' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" '.$param['w_field_option_pos1'].'>'.$choice_label.'</label><div class="radio-div forlabs" '.$param['w_field_option_pos2'].'><input type="radio" '.(($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key) ? 'other="1"' : ''	).' id="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.htmlspecialchars($choice_value).'" onclick="set_default(&quot;wdform_'.$id1.'&quot;,&quot;'.($key1+$k).'&quot;,&quot;'.$form_id.'&quot;); '.(($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key) ? 'show_other_input(&quot;wdform_'.$id1.'&quot;,&quot;'.$form_id.'&quot;);' : '').'" '.$checked.' '.$param['attributes'].'><label for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'"></label></div></div>';

					

									}

								}	

							}	

							else

							{

								if($key1%$param['w_rowcol']==0 && $key1>0)

									$rep.='</div><div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';  vertical-align:top">';

								

								$checked =($choice==$element_value ? 'checked="checked"' : '');

								if($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key && $is_other==true && $element_value!='')

									$checked = 'checked="checked"';

								$choice_value = isset($param['w_choices_value']) ? $param['w_choices_value'][$key] : $choice;

								

								$rep.='<div style="display: '.($param['w_flow']!='hor' ? 'table-cell' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.$key1.'" '.$param['w_field_option_pos1'].'>'.$choice.'</label><div class="radio-div forlabs" '.$param['w_field_option_pos2'].'><input type="radio" '.(($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key) ? 'other="1"' : ''	).' id="wdform_'.$id1.'_element'.$form_id.''.$key1.'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.htmlspecialchars($choice_value).'" onclick="set_default(&quot;wdform_'.$id1.'&quot;,&quot;'.$key1.'&quot;,&quot;'.$form_id.'&quot;); '.(($param['w_allow_other']=="yes" && $param['w_allow_other_num']==$key) ? 'show_other_input(&quot;wdform_'.$id1.'&quot;,&quot;'.$form_id.'&quot;);' : '').'" '.$checked.' '.$param['attributes'].'><label for="wdform_'.$id1.'_element'.$form_id.''.$key1.'"></label></div></div>';

								

								$param['w_allow_other_num'] = $param['w_allow_other_num']==$key ? $key1 : $param['w_allow_other_num'];

							}	

						}

								$rep.='</div>';



						$rep.='</div></div>';

					

						

						if($is_other && $element_value!='') 

							$onload_js .='show_other_input("wdform_'.$id1.'","'.$form_id.'"); jQuery("#wdform_'.$id1.'_other_input'.$form_id.'").val("'.$element_value.'");';

						

						$onsubmit_js.='

							jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_allow_other'.$form_id.'\" value = \"'.$param['w_allow_other'].'\" />").appendTo("#form'.$form_id.'");

							jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_allow_other_num'.$form_id.'\" value = \"'.$param['w_allow_other_num'].'\" />").appendTo("#adminForm");

							';

						

						break;

					}

            case 'type_own_select':

					{

					

						$params_names=array('w_field_label_size','w_field_label_pos','w_size','w_choices','w_choices_checked', 'w_choices_disabled','w_required','w_class');

						$temp=$params;

						if(strpos($temp, 'w_choices_value') > -1)

						$params_names=array('w_field_label_size','w_field_label_pos','w_size','w_choices','w_choices_checked', 'w_choices_disabled', 'w_required', 'w_value_disabled', 'w_choices_value', 'w_choices_params', 'w_class');

						

						foreach($params_names as $params_name )

						{	

							$temp=explode('*:*'.$params_name.'*:*',$temp);

							$param[$params_name] = $temp[0];

							$temp=$temp[1];

						}

						

						

						if($temp)

						{	

							$temp	=explode('*:*w_attr_name*:*',$temp);

							$attrs	= array_slice($temp,0, count($temp)-1);   

							foreach($attrs as $attr)

								$param['attributes'] = $param['attributes'].' '.$attr;

						}

						

					

						$wdformfieldsize = ($param['w_field_label_pos']=="left" ? ($param['w_field_label_size']+$param['w_size']) : max($param['w_field_label_size'], $param['w_size']));	

						$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	

						$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");

					

						$param['w_choices']	= explode('***',$param['w_choices']);

						$param['w_choices_disabled']	= explode('***',$param['w_choices_disabled']);

						if(isset($param['w_choices_value']))

						{

							$param['w_choices_value'] = explode('***',$param['w_choices_value']);

							$param['w_choices_params'] = explode('***',$param['w_choices_params']);	

						}

						

						if(!isset($param['w_value_disabled']))

						$param['w_value_disabled'] = 'no';

						

						$rep='<div type="type_own_select" class="wdform-field" style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';

						

						$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].' width: '.($param['w_size']).'px; "><select id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" style="width: 100%;"  '.$param['attributes'].'>';

						foreach($param['w_choices'] as $key => $choice)

						{

							if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])

							{

								$choices_labels =array();
							$choices_values = array();
							$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
							$where = (str_replace(array('[',']'), '', $w_choices_params[0]) ? ' WHERE '.str_replace(array('[',']'), '', $w_choices_params[0]) : '');
							$w_choices_params = explode('[db_info]',$w_choices_params[1]);
							$order_by = str_replace(array('[',']'), '', $w_choices_params[0]);
							$db_info = str_replace(array('[',']'), '', $w_choices_params[1]);
							
							
								$label_table_and_column = explode(':',str_replace(array('[',']'), '', $choice));

								$table = $label_table_and_column[0];

								$label_column = $label_table_and_column[1];

								if($label_column)

								{

									$choices_labels = $this->model->select_data_from_db_for_labels($db_info, $label_column, $table, $where, $order_by);

								}	



								$value_table_and_column = explode(':',str_replace(array('[',']'), '', $param['w_choices_value'][$key]));

								$value_column = $param['w_choices_disabled'][$key]=="true" ? '' : $value_table_and_column[1];



								if($value_column)

								{

									$choices_values = $this->model->select_data_from_db_for_values($db_info, $value_column, $table, $where, $order_by);

								}	



								$columns_count = count($choices_labels)>0 ?  count($choices_labels) : count($choices_values);

								if(array_filter($choices_labels) || array_filter($choices_values))

									for($k=0; $k<$columns_count; $k++)

									{

										$choice_label = isset($choices_labels[$k]) ? $choices_labels[$k] : '';

										$choice_value = isset($choices_values[$k]) ? $choices_values[$k] : ($param['w_choices_disabled'][$key]=="true" ? '' : $choice_label);

									

										$selected=($element_value && htmlspecialchars($choice_value)==htmlspecialchars($element_value) ? 'selected="selected"' : '');



										$rep.='<option value="'.htmlspecialchars($choice_value).'" '.$selected.'>'.$choice_label.'</option>';

													

									}		

							}

							else

							{		

								$choice_value = $param['w_choices_disabled'][$key]=="true" ? '' : (isset($param['w_choices_value']) ? $param['w_choices_value'][$key] : $choice);

								

								$selected=($element_value && htmlspecialchars($choice_value)==htmlspecialchars($element_value) ? 'selected="selected"' : '');

	

								$rep.='<option value="'.htmlspecialchars($choice_value).'" '.$selected.'>'.$choice.'</option>';

							}  

						}

						$rep.='</select></div></div>';

						

						

						break;

					}
            
            case 'type_country': {
              $params_names=array('w_field_label_size','w_field_label_pos','w_size','w_countries','w_required','w_class');
              $temp=$params;
              foreach($params_names as $params_name ) {	
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }

              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' add_'.$attr;
              }

              $wdformfieldsize = ($param['w_field_label_pos']=="left" ? ($param['w_field_label_size']+$param['w_size']) : max($param['w_field_label_size'], $param['w_size']));	
              $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	
              
              $param['w_countries']	= explode('***',$param['w_countries']);
              
              $selected='';
     
              $rep='<div type="type_country" class="wdform-field"  style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              
              $rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="width: '.$param['w_size'].'px;"><select id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" style="width: 100%;"  '.$param['attributes'].'>';
              foreach($param['w_countries'] as $key => $choice) {
                
                  $selected=(htmlspecialchars($choice)==htmlspecialchars($element_value) ? 'selected="selected"' : '');

                $choice_value=$choice;
                $rep.='<option value="'.$choice_value.'" '.$selected.'>'.$choice.'</option>';
              }
              $rep.='</select></div></div>';
                          
              break;
            }
            
            case 'type_time': {
              if($element_value =='')
                $element_value = ':';
                
                $params_names=array('w_field_label_size','w_field_label_pos','w_time_type','w_am_pm','w_sec','w_hh','w_mm','w_ss','w_mini_labels','w_required','w_class');
                $temp=$params;

                foreach($params_names as $params_name ) {	
                  $temp=explode('*:*'.$params_name.'*:*',$temp);
                  $param[$params_name] = $temp[0];
                  $temp=$temp[1];
                }
                
                if($temp) {	
                  $temp	=explode('*:*w_attr_name*:*',$temp);
                  $attrs	= array_slice($temp,0, count($temp)-1);   
                  foreach($attrs as $attr)
                    $param['attributes'] = $param['attributes'].' '.$attr;
                }
              
                $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	
              
                $w_mini_labels = explode('***',$param['w_mini_labels']);
                $element_value = explode(':',$element_value);
              
              
                $w_sec = '';
                $w_sec_label='';
                
                if($param['w_sec']=='1') {
                  $w_sec = '<div align="center" style="display: table-cell;"><span class="wdform_colon" style="vertical-align: middle;">&nbsp;:&nbsp;</span></div><div style="display: table-cell;"><input type="text" value="'.(count($element_value)==2 ? '' : $element_value[2]).'" class="time_box" id="wdform_'.$id1.'_ss'.$form_id.'" name="wdform_'.$id1.'_ss'.$form_id.'" onkeypress="return check_second(event, &quot;wdform_'.$id1.'_ss'.$form_id.'&quot;)" '.$param['attributes'].'></div>';
                  
                  $w_sec_label='<div style="display: table-cell;"></div><div style="display: table-cell;"><label class="mini_label">'.$w_mini_labels[2].'</label></div>';
                }

                
                if($param['w_time_type']=='12') {
                  if(strpos($element_value[2],'pm')!==false) {
                    $am_ = "";
                    $pm_ = "selected=\"selected\"";	
                  }	
                  else {
                    $am_ = "selected=\"selected\"";
                    $pm_ = "";	
                  }	
                
                    $w_time_type = '<div style="display: table-cell;"><select class="am_pm_select" name="wdform_'.$id1.'_am_pm'.$form_id.'" id="wdform_'.$id1.'_am_pm'.$form_id.'" '.$param['attributes'].'><option value="am" '.$am_.'>AM</option><option value="pm" '.$pm_.'>PM</option></select></div>';
                
                    $w_time_type_label = '<div ><label class="mini_label">'.$w_mini_labels[3].'</label></div>';
                
                }
                else {
                  $w_time_type='';
                  $w_time_type_label = '';
                }
                          
                $rep ='<div type="type_time" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
                
                $rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos'].';"><div style="display: table;"><div style="display: table-row;"><div style="display: table-cell;"><input type="text" value="'.$element_value[0].'" class="time_box" id="wdform_'.$id1.'_hh'.$form_id.'" name="wdform_'.$id1.'_hh'.$form_id.'" onkeypress="return check_hour(event, &quot;wdform_'.$id1.'_hh'.$form_id.'&quot;, &quot;23&quot;)" '.$param['attributes'].'></div><div align="center" style="display: table-cell;"><span class="wdform_colon" style="vertical-align: middle;">&nbsp;:&nbsp;</span></div><div style="display: table-cell;"><input type="text" value="'.$element_value[1].'" class="time_box" id="wdform_'.$id1.'_mm'.$form_id.'" name="wdform_'.$id1.'_mm'.$form_id.'" onkeypress="return check_minute(event, &quot;wdform_'.$id1.'_mm'.$form_id.'&quot;)" '.$param['attributes'].'></div>'.$w_sec.$w_time_type.'</div><div style="display: table-row;"><div style="display: table-cell;"><label class="mini_label">'.$w_mini_labels[0].'</label></div><div style="display: table-cell;"></div><div style="display: table-cell;"><label class="mini_label">'.$w_mini_labels[1].'</label></div>'.$w_sec_label.$w_time_type_label.'</div></div></div></div>';
                                
              break;
            }

            case 'type_date': {
                $params_names=array('w_field_label_size','w_field_label_pos','w_date','w_required','w_class','w_format','w_but_val');
                $temp = $params;
			    if(strpos($temp, 'w_disable_past_days') > -1)
				  $params_names = array('w_field_label_size','w_field_label_pos','w_date','w_required','w_class','w_format','w_but_val', 'w_disable_past_days');

                foreach($params_names as $params_name ) {	
                  $temp=explode('*:*'.$params_name.'*:*',$temp);
                  $param[$params_name] = $temp[0];
                  $temp=$temp[1];
                }
                              
                if($temp) {	
                  $temp	=explode('*:*w_attr_name*:*',$temp);
                  $attrs	= array_slice($temp,0, count($temp)-1);   
                  foreach($attrs as $attr)
                    $param['attributes'] = $param['attributes'].' '.$attr;
                }
                              
                $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	
                
                $rep ='<div type="type_date" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
                
                $rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos'].';"><input type="text" value="'.$element_value.'" class="wdform-date" id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" maxlength="10" '.$param['attributes'].'><input id="wdform_'.$id1.'_button'.$form_id.'" class="wdform-calendar-button" type="reset" value="'.$param['w_but_val'].'" format="'.$param['w_format'].'" onclick="return showCalendar(\'wdform_'.$id1.'_element'.$form_id.'\' , \'%Y-%m-%d\')"  '.$param['attributes'].' "></div></div>';
        
                
                // $onload_js.= 'Calendar.setup({inputField: "wdform_'.$id1.'_element'.$form_id.'",	ifFormat: "'.$param['w_format'].'",button: "wdform_'.$id1.'_button'.$form_id.'",align: "Tl",singleClick: true,firstDay: 0});';
                
              break;
            }

            case 'type_date_fields': {
              if($element_value=='')
                $element_value='--';
                
                $params_names=array('w_field_label_size','w_field_label_pos','w_day','w_month','w_year','w_day_type','w_month_type','w_year_type','w_day_label','w_month_label','w_year_label','w_day_size','w_month_size','w_year_size','w_required','w_class','w_from','w_to','w_divider');
                
                $temp=$params;

                foreach($params_names as $params_name ) {	
                  $temp=explode('*:*'.$params_name.'*:*',$temp);
                  $param[$params_name] = $temp[0];
                  $temp=$temp[1];
                }
                
                
                if($temp) {	
                  $temp	=explode('*:*w_attr_name*:*',$temp);
                  $attrs	= array_slice($temp,0, count($temp)-1);   
                  foreach($attrs as $attr)
                    $param['attributes'] = $param['attributes'].' '.$attr;
                }
                
                $element_value = explode('-',$element_value); 
                
                $param['w_day'] = (isset($_POST['wdform_'.$id1."_day".$form_id]) ? esc_html(stripslashes( $_POST['wdform_'.$id1."_day".$form_id])) : $param['w_day']);
                $param['w_month'] = (isset($_POST['wdform_'.$id1."_month".$form_id]) ? esc_html(stripslashes( $_POST['wdform_'.$id1."_month".$form_id])) : $element_value[1]);//??????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????
                $param['w_year'] = (isset($_POST['wdform_'.$id1."w_year".$form_id]) ? esc_html(stripslashes( $_POST['wdform_'.$id1."_year".$form_id])) : $param['w_year']);
                  
                $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	
                
                if($param['w_day_type']=="SELECT") {
                
                  $w_day_type = '<select id="wdform_'.$id1.'_day'.$form_id.'" name="wdform_'.$id1.'_day'.$form_id.'" style="width: '.$param['w_day_size'].'px;" '.$param['attributes'].'><option value=""></option>';
                  
                  for($k=1; $k<=31; $k++) {
                  
                    if($k<10) {
                      if($element_value[0]=='0'.$k)
                      $selected = "selected=\"selected\"";
                      else
                      $selected = "";
                      
                      $w_day_type .= '<option value="0'.$k.'" '.$selected.'>0'.$k.'</option>';
                    }
                    else {
                      if($element_value[0]==''.$k)
                      $selected = "selected=\"selected\"";
                      else
                      $selected = "";
                      
                      $w_day_type .= '<option value="'.$k.'" '.$selected.'>'.$k.'</option>';
                    }
                    
                  }
                  $w_day_type .= '</select>';
                  
                }
                else {
                  $w_day_type = '<input type="text" value="'.$element_value[0].'" id="wdform_'.$id1.'_day'.$form_id.'" name="wdform_'.$id1.'_day'.$form_id.'" style="width: '.$param['w_day_size'].'px;" '.$param['attributes'].'>';
                  $onload_js .='jQuery("#wdform_'.$id1.'_day'.$form_id.'").blur(function() {if (jQuery(this).val()=="0") jQuery(this).val(""); else add_0(this)});';
                  $onload_js .='jQuery("#wdform_'.$id1.'_day'.$form_id.'").keypress(function() {return check_day(event, this)});';
                }
                
                
                if($param['w_month_type']=="SELECT") {
                
                      $w_month_type = '<select id="wdform_'.$id1.'_month'.$form_id.'" name="wdform_'.$id1.'_month'.$form_id.'" style="width: '.$param['w_month_size'].'px;" '.$param['attributes'].'><option value=""></option><option value="01" '.($param['w_month']=="01" ? "selected=\"selected\"": "").'  >'.(__("January", 'form_maker')).'</option><option value="02" '.($param['w_month']=="02" ? "selected=\"selected\"": "").'>'.(__("February", 'form_maker')).'</option><option value="03" '.($param['w_month']=="03"? "selected=\"selected\"": "").'>'.(__("March", 'form_maker')).'</option><option value="04" '.($param['w_month']=="04" ? "selected=\"selected\"": "").' >'.(__("April", 'form_maker')).'</option><option value="05" '.($param['w_month']=="05" ? "selected=\"selected\"": "").' >'.(__("May", 'form_maker')).'</option><option value="06" '.($param['w_month']=="06" ? "selected=\"selected\"": "").' >'.(__("June", 'form_maker')).'</option><option value="07" '.($param['w_month']=="07" ? "selected=\"selected\"": "").' >'.(__("July", 'form_maker')).'</option><option value="08" '.($param['w_month']=="08" ? "selected=\"selected\"": "").' >'.(__("August", 'form_maker')).'</option><option value="09" '.($param['w_month']=="09" ? "selected=\"selected\"": "").' >'.(__("September", 'form_maker')).'</option><option value="10" '.($param['w_month']=="10" ? "selected=\"selected\"": "").' >'.(__("October", 'form_maker')).'</option><option value="11" '.($param['w_month']=="11" ? "selected=\"selected\"": "").'>'.(__("November", 'form_maker')).'</option><option value="12" '.($param['w_month']=="12" ? "selected=\"selected\"": "").' >'.(__("December", 'form_maker')).'</option></select>';              
                }
                else {
                  $w_month_type = '<input type="text" value="'.$element_value[1].'" id="wdform_'.$id1.'_month'.$form_id.'" name="wdform_'.$id1.'_month'.$form_id.'"  style="width: '.$param['w_day_size'].'px;" '.$param['attributes'].'>';
                  $onload_js .='jQuery("#wdform_'.$id1.'_month'.$form_id.'").blur(function() {if (jQuery(this).val()=="0") jQuery(this).val(""); else add_0(this)});';
                  $onload_js .='jQuery("#wdform_'.$id1.'_month'.$form_id.'").keypress(function() {return check_month(event, this)});';
                }
                
                
                if($param['w_year_type']=="SELECT" ) {
                  $w_year_type = '<select id="wdform_'.$id1.'_year'.$form_id.'" name="wdform_'.$id1.'_year'.$form_id.'"  from="'.$param['w_from'].'" to="'.$param['w_to'].'" style="width: '.$param['w_year_size'].'px;" '.$param['attributes'].'><option value=""></option>';
                  
                  for($k=$param['w_to']; $k>=$param['w_from']; $k--) {
                    if($element_value[2]==$k)
                    $selected = "selected=\"selected\"";
                    else
                    $selected = "";
                    
                    $w_year_type .= '<option value="'.$k.'" '.$selected.'>'.$k.'</option>';
                  }
                  $w_year_type .= '</select>';
                }
                else {
                  $w_year_type = '<input type="text" value="'.$element_value[2].'" id="wdform_'.$id1.'_year'.$form_id.'" name="wdform_'.$id1.'_year'.$form_id.'" from="'.$param['w_from'].'" to="'.$param['w_to'].'" style="width: '.$param['w_day_size'].'px;" '.$param['attributes'].'>';
                  $onload_js .='jQuery("#wdform_'.$id1.'_year'.$form_id.'").blur(function() {check_year2(this)});';
                  $onload_js .='jQuery("#wdform_'.$id1.'_year'.$form_id.'").keypress(function() {return check_year1(event, this)});';
                  $onload_js .='jQuery("#wdform_'.$id1.'_year'.$form_id.'").change(function() {change_year(this)});';
                }
                  
                $rep ='<div type="type_date_fields" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
                
                $rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos'].';"><div style="display: table;"><div style="display: table-row;"><div style="display: table-cell;">'.$w_day_type.'</div><div style="display: table-cell;"><span class="wdform_separator">'.$param['w_divider'].'</span></div><div style="display: table-cell;">'.$w_month_type.'</div><div style="display: table-cell;"><span class="wdform_separator">'.$param['w_divider'].'</span></div><div style="display: table-cell;">'.$w_year_type.'</div></div><div style="display: table-row;"><div style="display: table-cell;"><label class="mini_label">'.$param['w_day_label'].'</label></div><div style="display: table-cell;"></div><div style="display: table-cell;"><label class="mini_label" >'.$param['w_month_label'].'</label></div><div style="display: table-cell;"></div><div style="display: table-cell;"><label class="mini_label">'.$param['w_year_label'].'</label></div></div></div></div></div>';
                 
              break;
            }
    
            
            case 'type_hidden': {
              $params_names=array('w_name','w_value');
              $temp=$params;

              foreach($params_names as $params_name ) {	
                $temp=explode('*:*'.$params_name.'*:*',$temp);
                $param[$params_name] = $temp[0];
                $temp=$temp[1];
              }
                
              if($temp) {	
                $temp	=explode('*:*w_attr_name*:*',$temp);
                $attrs	= array_slice($temp,0, count($temp)-1);   
                foreach($attrs as $attr)
                  $param['attributes'] = $param['attributes'].' '.$attr;
              }
              
              $rep ='<div type="type_hidden" class="wdform-field"><div class="wdform-label-section" style="float:left; width: 150px;"><span class="wdform-label">' . $label . '</span></div><div class="wdform-element-section" style="display: table-cell;"><input type="text" value="'.$element_value.'" id="wdform_'.$id1.'_element'.$form_id.'" name="'.$param['w_name'].'" '.$param['attributes'].'></div></div>';
              
              break;
            }


            case 'type_paypal_price': {
              
                $params_names=array('w_field_label_size','w_field_label_pos','w_first_val','w_title', 'w_mini_labels','w_size','w_required','w_hide_cents','w_class','w_range_min','w_range_max');
                $temp=$params;

                foreach($params_names as $params_name ) {	
                  $temp=explode('*:*'.$params_name.'*:*',$temp);
                  $param[$params_name] = $temp[0];
                  $temp=$temp[1];
                }
                
                
                if($temp) {	
                  $temp	=explode('*:*w_attr_name*:*',$temp);
                  $attrs	= array_slice($temp,0, count($temp)-1);   
                  foreach($attrs as $attr)
                    $param['attributes'] = $param['attributes'].' '.$attr;
                }
                
                if (strpos($element_value,'.')!== false)
                  $element = explode('.',$element_value);
                else {
                  $element = Array();
                  $element[0] = preg_replace("/[^0-9]/","",$element_value);
                  $element[1] = '';
                }
                  
                $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	
                
                $hide_cents = ($param['w_hide_cents']=="yes" ? "none;" : "table-cell;");	
                          
                $w_mini_labels = explode('***',$param['w_mini_labels']);
                
                $rep ='<div type="type_paypal_price" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
                 
                $rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos'].';"><input type="hidden" value="'.$param['w_range_min'].'" name="wdform_'.$id1.'_range_min'.$form_id.'" id="wdform_'.$id1.'_range_min'.$form_id.'"><input type="hidden" value="'.$param['w_range_max'].'" name="wdform_'.$id1.'_range_max'.$form_id.'" id="wdform_'.$id1.'_range_max'.$form_id.'"><div id="wdform_'.$id1.'_table_price" style="display: table;"><div id="wdform_'.$id1.'_tr_price1" style="display: table-row;"><div id="wdform_'.$id1.'_td_name_currency" style="display: table-cell;"><span class="wdform_colon" style="vertical-align: middle;"><!--repstart-->&nbsp;'.$form_currency.'&nbsp;<!--repend--></span></div><div id="wdform_'.$id1.'_td_name_dollars" style="display: table-cell;"><input type="text" class="" id="wdform_'.$id1.'_element_dollars'.$form_id.'" name="wdform_'.$id1.'_element_dollars'.$form_id.'" value="'.(strpos($element_value,'.') !== false ? preg_replace("/[^0-9]/","",$element[0]) : $element[0]).'"  onkeypress="return check_isnum(event)" style="width: '.$param['w_size'].'px;" '.$param['attributes'].'></div><div id="wdform_'.$id1.'_td_name_divider" style="display: '.$hide_cents.';"><span class="wdform_colon" style="vertical-align: middle;">&nbsp;.&nbsp;</span></div><div id="wdform_'.$id1.'_td_name_cents" style="display: '.$hide_cents.'"><input type="text" class="" id="wdform_'.$id1.'_element_cents'.$form_id.'" name="wdform_'.$id1.'_element_cents'.$form_id.'" value="'.(strpos($element_value,'.') !== false ? preg_replace("/[^0-9]/","",$element[1]) : " ").'"  style="width: 30px;" '.$param['attributes'].'></div></div><div id="wdform_'.$id1.'_tr_price2" style="display: table-row;"><div style="display: table-cell;"><label class="mini_label"></label></div><div align="left" style="display: table-cell;"><label class="mini_label">'.$w_mini_labels[0].'</label></div><div id="wdform_'.$id1.'_td_name_label_divider" style="display: '.$hide_cents.'"><label class="mini_label"></label></div><div align="left" id="wdform_'.$id1.'_td_name_label_cents" style="display: '.$hide_cents.'"><label class="mini_label">'.$w_mini_labels[1].'</label></div></div></div></div></div>';
                
                $onload_js .='jQuery("#wdform_'.$id1.'_element_cents'.$form_id.'").blur(function() {add_0(this)});';
                $onload_js .='jQuery("#wdform_'.$id1.'_element_cents'.$form_id.'").keypress(function() {return check_isnum_interval(event,this,0,99)});';
                
              break;
            }
            
            case 'type_paypal_select':

					{



						if($element_value=='')

							$element_value = ' :';

						

						$params_names=array('w_field_label_size','w_field_label_pos','w_size','w_choices','w_choices_price','w_choices_checked', 'w_choices_disabled','w_required','w_quantity','w_class','w_property','w_property_values');

						$temp=$params;



						if(strpos($temp, 'w_choices_params') > -1)

							$params_names=array('w_field_label_size','w_field_label_pos','w_size','w_choices','w_choices_price','w_choices_checked', 'w_choices_disabled','w_required','w_quantity', 'w_quantity_value', 'w_choices_params','w_class','w_property','w_property_values');

							

						foreach($params_names as $params_name )

						{	

							$temp=explode('*:*'.$params_name.'*:*',$temp);

							$param[$params_name] = $temp[0];

							$temp=$temp[1];

						}

						

						

						if($temp)

						{	

							$temp	=explode('*:*w_attr_name*:*',$temp);

							$attrs	= array_slice($temp,0, count($temp)-1);   

							foreach($attrs as $attr)

								$param['attributes'] = $param['attributes'].' '.$attr;

						}

						

					

						$wdformfieldsize = ($param['w_field_label_pos']=="left" ? ($param['w_field_label_size']+$param['w_size']) : max($param['w_field_label_size'], $param['w_size']));	

						$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	

						$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");

						

						$param['w_choices']	= explode('***',$param['w_choices']);

						$param['w_choices_price']	= explode('***',$param['w_choices_price']);

						

						$param['w_choices_disabled']	= explode('***',$param['w_choices_disabled']);

						$param['w_property']	= explode('***',$param['w_property']);

						$param['w_property_values']	= explode('***',$param['w_property_values']);

						if(isset($param['w_choices_params']))

							$param['w_choices_params'] = explode('***',$param['w_choices_params']);	

						

						

						if(strpos($element_value,'***br***') !== false)

						{	

							$element_value	= explode('***br***',$element_value);

							if(count($element_value)==2)

							{

								if(strpos($element_value[1],'***quantity***') !== false)

								{

									$quantity_value = explode(': ',str_replace("***quantity***","",$element_value[1]));

									$quantity_value =  $quantity_value[1];

									$property_of_select = '';

								}

								else

								{

									$quantity_value = '' ;

									$property_of_select = explode(': ',str_replace("***property***","",$element_value[1]));

									$property_of_select = $property_of_select[1];

								}

								

								$element_value	= explode(' :',$element_value[0]);

								$choise_value = preg_replace("/[^0-9]/","",$element_value[1]);

							}

							else

							{

								$quantity_value = explode(': ',str_replace("***quantity***","",$element_value[1]));

								$quantity_value =  $quantity_value[1];

								$property_of_select = explode(': ',str_replace("***property***","",$element_value[2]));

								$property_of_select = $property_of_select[1];

								$element_value	= explode(' :',$element_value[0]);

								$choise_value = preg_replace("/[^0-9]/","",$element_value[1]);

							}

						}

						else

						{

							$element_value	= explode(' :',$element_value);

							$choise_value = preg_replace("/[^0-9]/","",$element_value[1]);

							$quantity_value = '';

							$property_of_select = '';

						}

						

					

						$rep='<div type="type_paypal_select" class="wdform-field" style="width:'.$wdformfieldsize.'px"><div class="wdform-label-section" style="'.$param['w_field_label_pos1'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';

						

						$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos2'].'; width: '.$param['w_size'].'px;"><select id="wdform_'.$id1.'_element'.$form_id.'" name="wdform_'.$id1.'_element'.$form_id.'" style="width:100%;"  '.$param['attributes'].'>';

						foreach($param['w_choices'] as $key => $choice)

						{

							if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])

							{

								$choices_labels =array();
								$choices_values =array();
								$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
								$where = (str_replace(array('[',']'), '', $w_choices_params[0]) ? ' WHERE '.str_replace(array('[',']'), '', $w_choices_params[0]) : '');
								$w_choices_params = explode('[db_info]',$w_choices_params[1]);
								$order_by = str_replace(array('[',']'), '', $w_choices_params[0]);
								$db_info = str_replace(array('[',']'), '', $w_choices_params[1]);
			
								
								$label_table_and_column = explode(':',str_replace(array('[',']'), '', $choice));

								$table = $label_table_and_column[0];

								$label_column = $label_table_and_column[1];

								if($label_column)

								{

									$choices_labels = $this->model->select_data_from_db_for_labels($db_info, $label_column, $table, $where, $order_by);

								}	



								$value_table_and_column = explode(':',str_replace(array('[',']'), '', $param['w_choices_price'][$key]));

								$value_column = $param['w_choices_disabled'][$key]=="true" ? '' : $value_table_and_column[1];



								if($value_column)

								{

									$choices_values = $this->model->select_data_from_db_for_values($db_info, $value_column, $table, $where, $order_by);

								}		

								

								$columns_count = count($choices_labels)>0 ?  count($choices_labels) : count($choices_values);

								

								for($k=0; $k<$columns_count; $k++)

								{

									$choice_label = isset($choices_labels[$k]) ? $choices_labels[$k] : '';

									$choice_value = isset($choices_values[$k]) ? (float)$choices_values[$k] : '';

									

									if($element_value[0] == $choice_label && $choice_value == $choise_value)

										$selected = 'selected="selected"';

									else

										$selected = '';	



									$rep.='<option value="'.$choice_value.'" '.$selected.'>'.$choice_label.'</option>';

								}			

							}

							else

							{

								if($param['w_choices_disabled'][$key]=="true")

									$choice_value='';

								else

									$choice_value=$param['w_choices_price'][$key];

									

								if($element_value[0] == $choice && $param['w_choices_price'][$key] == $choise_value)

									$selected = 'selected="selected"';

								else

									$selected = '';	

									

								$rep.='<option value="'.$choice_value.'" '.$selected.'>'.$choice.'</option>';

							

							}

						}

						$rep.='</select><div id="wdform_'.$id1.'_div'.$form_id.'">';

						if($param['w_quantity']=="yes")

						{

							$rep.='<div class="paypal-property"><label class="mini_label" style="margin: 0px 5px;">Quantity</label><input type="text" id="wdform_'.$id1.'_element_quantity'.$form_id.'" name="wdform_'.$id1.'_element_quantity'.$form_id.'" value="'.($quantity_value=="" ? 1 : $quantity_value).'"  class="wdform-quantity"></div>';

						}

						if($param['w_property'][0])					

						foreach($param['w_property'] as $key => $property)

						{

		

						$rep.='

						<div id="wdform_'.$id1.'_property_'.$key.'" class="paypal-property">

						<div style="width:150px; display:inline-block;">

						<label class="mini_label" id="wdform_'.$id1.'_property_label_'.$form_id.''.$key.'" style="margin-right: 5px;">'.$property.'</label>

						<select id="wdform_'.$id1.'_property'.$form_id.''.$key.'" name="wdform_'.$id1.'_property'.$form_id.''.$key.'" style="width: 100%; margin: 2px 0px;">';

						$param['w_property_values'][$key]	= explode('###',$param['w_property_values'][$key]);

						$param['w_property_values'][$key]	= array_slice($param['w_property_values'][$key],1, count($param['w_property_values'][$key]));   

						foreach($param['w_property_values'][$key] as $subkey => $property_value)

						{

							$rep.='<option id="wdform_'.$id1.'_'.$key.'_option'.$subkey.'" value="'.$property_value.'" '.($property_of_select==$property_value ? 'selected="selected"' : "").'>'.$property_value.'</option>';

						}

						$rep.='</select></div></div>';

						}

						

						$rep.='</div></div></div>';

						



						$onsubmit_js.='

							jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_element_label'.$form_id.'\"  />").val(jQuery("#wdform_'.$id1.'_element'.$form_id.' option:selected").text()).appendTo("#adminForm");

							';

						$onsubmit_js.='

							jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_element_quantity_label'.$form_id.'\"  />").val("Quantity").appendTo("#adminForm");

								';

						$onsubmit_js.='

							jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_element_property_label'.$form_id.'\"  />").val("'.$param['w_property'][0].'").appendTo("#adminForm");

								';

			

					

						break;

					}
            
            case 'type_paypal_checkbox':

					{

					

							if($element_value == '')

								$element_value =' :';

											

							$params_names=array('w_field_label_size','w_field_label_pos','w_flow','w_choices','w_choices_price','w_choices_checked','w_required','w_randomize','w_allow_other','w_allow_other_num','w_class','w_property','w_property_values','w_quantity');

							$temp=$params;

							if(strpos($temp, 'w_field_option_pos') > -1)

								$params_names=array('w_field_label_size','w_field_label_pos', 'w_field_option_pos','w_flow','w_choices','w_choices_price','w_choices_checked','w_required','w_randomize','w_allow_other','w_allow_other_num', 'w_choices_params', 'w_class','w_property','w_property_values','w_quantity');	

							foreach($params_names as $params_name )

							{	

								$temp=explode('*:*'.$params_name.'*:*',$temp);

								$param[$params_name] = $temp[0];

								$temp=$temp[1];

							}

							

							

							if($temp)

							{	

								$temp	=explode('*:*w_attr_name*:*',$temp);

								$attrs	= array_slice($temp,0, count($temp)-1);   

								foreach($attrs as $attr)

									$param['attributes'] = $param['attributes'].' '.$attr;

							}

							

						

							if(!isset($param['w_field_option_pos']))

								$param['w_field_option_pos'] = 'left';

					

							$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	

							$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");

							$param['w_field_option_pos1'] = ($param['w_field_option_pos']=="right" ? "style='float: none !important;'" : "");

							$param['w_field_option_pos2'] = ($param['w_field_option_pos']=="right" ? "style='float: left !important; margin-right: 8px !important; display: inline-block !important;'" : "");

						

							

							$param['w_choices']	= explode('***',$param['w_choices']);

							$param['w_choices_price']	= explode('***',$param['w_choices_price']);

							

							$param['w_property']	= explode('***',$param['w_property']);

							$param['w_property_values']	= explode('***',$param['w_property_values']);

							if(isset($param['w_choices_params']))

								$param['w_choices_params'] = explode('***',$param['w_choices_params']);

							

							$rep='<div type="type_paypal_checkbox" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';

							

							$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos'].';">';

						

			

										if(strpos($element_value,'***br***') !== false)

										{	

											$element_value	= explode('***br***',$element_value);	

											foreach($element_value as $key => $element)	

											{

											

												if(strpos($element,'***quantity***') !== false)

												{

													$quantity_value = explode(': ',str_replace("***quantity***","",$element));

													$quantity_value =  $quantity_value[1];

													unset($element_value[$key]);

												}

												else

												if(strpos($element,'***property***') !== false)

												{

													$property_of_check = explode(': ',str_replace("***property***","",$element));

													$property_of_check = $property_of_check[1];

													unset($element_value[$key]);

												}

												else

												{	

													for($m=0; $m<strlen($element); $m++)

													{

														if(!ctype_digit($element[strlen($element)-1]))

															$element_value[$key] = substr($element,0,-1);

														else

															break;

													}

													$quantity_value = '';

													$property_of_check = '';

												}

												

											}

										}

										else

										{

											$element_value	= explode(' :',$element_value);

											$choise_value = preg_replace("/[^0-9]/","",$element_value[1]);

											$quantity_value = '';

											$property_of_check = '';

										}			

					

							$total_queries = 0;

							foreach($param['w_choices'] as $key => $choice)

							{

								$key1 = $key + $total_queries;

								if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])

								{

									$choices_labels = array();
									$choices_values = array();
									
									$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
									$where = (str_replace(array('[',']'), '', $w_choices_params[0]) ? ' WHERE '.str_replace(array('[',']'), '', $w_choices_params[0]) : '');
									$w_choices_params = explode('[db_info]',$w_choices_params[1]);
									
									$order_by = str_replace(array('[',']'), '', $w_choices_params[0]);
									$db_info = str_replace(array('[',']'), '', $w_choices_params[1]);
								
									$label_table_and_column = explode(':',str_replace(array('[',']'), '', $choice));

									$table = $label_table_and_column[0];

									$label_column = $label_table_and_column[1];

									if($label_column)

									{

										$choices_labels = $this->model->select_data_from_db_for_labels($db_info, $label_column, $table, $where, $order_by);
									}	



									$value_table_and_column = explode(':',str_replace(array('[',']'), '', $param['w_choices_price'][$key]));

									$value_column = $value_table_and_column[1];



									if($value_column)

									{

										$choices_values = $this->model->select_data_from_db_for_values($db_info, $value_column, $table, $where, $order_by);

									}		

									

									$columns_count = count($choices_labels)>0 ?  count($choices_labels) : count($choices_values);

									

									if(array_filter($choices_labels) || array_filter($choices_values))

									{

										$total_queries = $total_queries + $columns_count-1;

										

											

										for($k=0; $k<$columns_count; $k++)

										{

											$choice_label = isset($choices_labels) ? $choices_labels[$k] : '';

											$choice_value = isset($choices_values) ? (float)$choices_values[$k] : '';

											

											$checked=(in_array($choice_label.' - '.$choice_value, $element_value)=='true' ? 'checked="checked"' : '');

											

											$rep.='<div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" '.$param['w_field_option_pos1'].'>'.$choice_label.'</label><div class="checkbox-div forlabs" '.$param['w_field_option_pos2'].'><input type="checkbox" id="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" name="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" value="'.$choice_value.'" '.$checked.' '.$param['attributes'].'><label for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'"></label></div><input type="hidden" name="wdform_'.$id1.'_element'.$form_id.($key1+$k).'_label" value="'.htmlspecialchars($choice_label).'" /></div>';

											

											

										}

									}	

								}

								else

								{

									$checked=(in_array($choice.' - '.$param['w_choices_price'][$key], $element_value)=='true' ? 'checked="checked"' : '');

									

									$rep.='<div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.$key1.'" '.$param['w_field_option_pos1'].'>'.$choice.'</label><div class="checkbox-div forlabs" '.$param['w_field_option_pos2'].'><input type="checkbox" id="wdform_'.$id1.'_element'.$form_id.''.$key1.'" name="wdform_'.$id1.'_element'.$form_id.''.$key1.'" value="'.$param['w_choices_price'][$key].'" '.$checked.' '.$param['attributes'].'><label for="wdform_'.$id1.'_element'.$form_id.''.$key1.'"></label></div><input type="hidden" name="wdform_'.$id1.'_element'.$form_id.$key1.'_label" value="'.htmlspecialchars($choice).'" /></div>';

								}

							}

					

							$rep.='<div id="wdform_'.$id1.'_div'.$form_id.'">';

							if($param['w_quantity']=="yes")

								$rep.='<div class="paypal-property"><label class="mini_label" style="margin: 0px 5px;">Quantity</label><input type="text" value="'.($quantity_value == '' ? 1 : $quantity_value).'" id="wdform_'.$id1.'_element_quantity'.$form_id.'" name="wdform_'.$id1.'_element_quantity'.$form_id.'" class="wdform-quantity"></div>';

								

							if($param['w_property'][0])					

							foreach($param['w_property'] as $key => $property)

							{



							$rep.='

							<div class="paypal-property">

							<div style="width:150px; display:inline-block;">

							<label class="mini_label" id="wdform_'.$id1.'_property_label_'.$form_id.''.$key.'" style="margin-right: 5px;">'.$property.'</label>

							<select id="wdform_'.$id1.'_property'.$form_id.''.$key.'" name="wdform_'.$id1.'_property'.$form_id.''.$key.'" style="width: 100%; margin: 2px 0px;">';

							$param['w_property_values'][$key]	= explode('###',$param['w_property_values'][$key]);

							$param['w_property_values'][$key]	= array_slice($param['w_property_values'][$key],1, count($param['w_property_values'][$key]));   

							foreach($param['w_property_values'][$key] as $subkey => $property_value)

							{

								$rep.='<option id="wdform_'.$id1.'_'.$key.'_option'.$subkey.'" value="'.$property_value.'" '.($property_of_check==$property_value ? 'selected="selected"' : "").'>'.$property_value.'</option>';

							}

							$rep.='</select></div></div>';

							}

							

							$rep.='</div></div></div>';

							

							$onsubmit_js.='

							jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_element_quantity_label'.$form_id.'\"  />").val("Quantity").appendTo("#adminForm");

							';

							$onsubmit_js.='

							jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_element_property_label'.$form_id.'\"  />").val("'.$param['w_property'][0].'").appendTo("#adminForm");

							';		

			

						break;

					}

            case 'type_paypal_radio':

					{

					

						if($element_value=='')

							$element_value = ' :';

						

							$params_names=array('w_field_label_size','w_field_label_pos','w_flow','w_choices','w_choices_price','w_choices_checked','w_required','w_randomize','w_allow_other','w_allow_other_num','w_class','w_property','w_property_values','w_quantity');

							$temp=$params;



							if(strpos($temp, 'w_field_option_pos') > -1)

								$params_names=array('w_field_label_size','w_field_label_pos', 'w_field_option_pos', 'w_flow','w_choices','w_choices_price','w_choices_checked','w_required','w_randomize','w_allow_other','w_allow_other_num', 'w_choices_params', 'w_class', 'w_property','w_property_values','w_quantity');

								

							foreach($params_names as $params_name )

							{	

								$temp=explode('*:*'.$params_name.'*:*',$temp);

								$param[$params_name] = $temp[0];

								$temp=$temp[1];

							}



							if($temp)

							{	

								$temp	=explode('*:*w_attr_name*:*',$temp);

								$attrs	= array_slice($temp,0, count($temp)-1);   

								foreach($attrs as $attr)

									$param['attributes'] = $param['attributes'].' '.$attr;

							}

							if(!isset($param['w_field_option_pos']))

								$param['w_field_option_pos'] = 'left';



							$param['w_field_label_pos1'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "");	

							$param['w_field_label_pos2'] = ($param['w_field_label_pos']=="left" ? "" : "display:block;");

							$param['w_field_option_pos1'] = ($param['w_field_option_pos']=="right" ? "style='float: none !important;'" : "");

							$param['w_field_option_pos2'] = ($param['w_field_option_pos']=="right" ? "style='float: left !important; margin-right: 8px !important; display: inline-block !important;'" : "");

							

							$param['w_choices']	= explode('***',$param['w_choices']);

							$param['w_choices_price']	= explode('***',$param['w_choices_price']);

							

							$param['w_property']	= explode('***',$param['w_property']);

							$param['w_property_values']	= explode('***',$param['w_property_values']);

							if(isset($param['w_choices_params']))

								$param['w_choices_params'] = explode('***',$param['w_choices_params']);	

								

							$rep='<div type="type_paypal_radio" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';

						

							$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos'].';">';

		

							if(strpos($element_value,'***br***') !== false)

							{	

								$element_value	= explode('***br***',$element_value);

								if(count($element_value)==2)

								{

									if(strpos($element_value[1],'***quantity***') !== false)

									{

										$quantity_value = explode(': ',str_replace("***quantity***","",$element_value[1]));

										$quantity_value = $quantity_value[1];

										$property_of_radio = '';

									}

									else

									{

										$quantity_value = '' ;

										$property_of_radio = explode(': ',str_replace("***property***","",$element_value[1]));

										$property_of_radio = $property_of_radio[1];

									}

									

									$element_value	= explode(' :',$element_value[0]);

									$choise_value = preg_replace("/[^0-9]/","",$element_value[1]);

								}

								else

								{

									$quantity_value = explode(': ',str_replace("***quantity***","",$element_value[1]));

									$quantity_value = $quantity_value[1];

									$property_of_radio = explode(': ',str_replace("***property***","",$element_value[2]));

									$property_of_radio = $property_of_radio[1];

								

									$element_value	= explode(' :',$element_value[0]);

									$choise_value = preg_replace("/[^0-9]/","",$element_value[1]);

								}

							}

							else

							{

								$element_value	= explode(' :',$element_value);

								$choise_value = preg_replace("/[^0-9]/","",$element_value[1]);

								$quantity_value = '';

								$property_of_radio = '';

							}

							$total_queries = 0;

							foreach($param['w_choices'] as $key => $choice)

							{

								$key1 = $key + $total_queries;

								if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])

								{

									$choices_labels =array();
									$choices_values =array();
									$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
									$where = (str_replace(array('[',']'), '', $w_choices_params[0]) ? ' WHERE '.str_replace(array('[',']'), '', $w_choices_params[0]) : '');
									$w_choices_params = explode('[db_info]',$w_choices_params[1]);
									
									$order_by = str_replace(array('[',']'), '', $w_choices_params[0]);
									$db_info = str_replace(array('[',']'), '', $w_choices_params[1]);
				

									$label_table_and_column = explode(':',str_replace(array('[',']'), '', $choice));

									$table = $label_table_and_column[0];

									$label_column = $label_table_and_column[1];

									if($label_column)

									{

										$choices_labels = $this->model->select_data_from_db_for_labels($db_info, $label_column, $table, $where, $order_by);
									}	



									$value_table_and_column = explode(':',str_replace(array('[',']'), '', $param['w_choices_price'][$key]));

									$value_column = $value_table_and_column[1];



									if($value_column)

									{

										$choices_values = $this->model->select_data_from_db_for_values($db_info, $value_column, $table, $where, $order_by);

									}		



									$columns_count_radio = count($choices_labels)>0 ?  count($choices_labels) : count($choices_values);

									

									if(array_filter($choices_labels) || array_filter($choices_values))

									{

										$total_queries = $total_queries + $columns_count_radio-1;

										for($k=0; $k<$columns_count_radio; $k++)

										{	

											$choice_label = isset($choices_labels) ? $choices_labels[$k] : '';

											$choice_value = isset($choices_values) ? (float)$choices_values[$k] : '';

					

											if($element_value[0] == $choice_label && $choice_value == $choise_value)

												$checked = 'checked="checked"';

											else

												$checked = '';

					

					

											$rep.='<div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'">'.$choice_label.'</label><div class="radio-div forlabs"><input type="radio" id="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.$choice_value.'" title="'.htmlspecialchars($choice_label).'" '.$checked.' '.$param['attributes'].'><label for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'"></label></div></div>';

				

										}

									}			

								}

								else

								{

									if($element_value[0] == $choice && $param['w_choices_price'][$key] == $choise_value)

										$checked = 'checked="checked"';

									else

										$checked = '';

										

									$rep.='<div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.$key1.'">'.$choice.'</label><div class="radio-div forlabs"><input type="radio" id="wdform_'.$id1.'_element'.$form_id.''.$key1.'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.$param['w_choices_price'][$key].'" title="'.htmlspecialchars($choice).'" '.$checked.' '.$param['attributes'].'><label for="wdform_'.$id1.'_element'.$form_id.''.$key1.'"></label></div></div>';

								}

							

							}



							$rep.='<div id="wdform_'.$id1.'_div'.$form_id.'">';

							if($param['w_quantity']=="yes")

								$rep.='<div class="paypal-property"><label class="mini_label" style="margin: 0px 5px;">Quantity</label><input type="text" value="'.($quantity_value=='' ? 1 : $quantity_value).'" id="wdform_'.$id1.'_element_quantity'.$form_id.'" name="wdform_'.$id1.'_element_quantity'.$form_id.'" class="wdform-quantity"></div>';

								

							if($param['w_property'][0])					

							foreach($param['w_property'] as $key => $property)

							{



							$rep.='

							<div class="paypal-property">

							<div style="width:150px; display:inline-block;">

							<label class="mini_label" id="wdform_'.$id1.'_property_label_'.$form_id.''.$key.'" style="margin-right: 5px;">'.$property.'</label>

							<select id="wdform_'.$id1.'_property'.$form_id.''.$key.'" name="wdform_'.$id1.'_property'.$form_id.''.$key.'" style="width: 100%; margin: 2px 0px;">';

							$param['w_property_values'][$key]	= explode('###',$param['w_property_values'][$key]);

							$param['w_property_values'][$key]	= array_slice($param['w_property_values'][$key],1, count($param['w_property_values'][$key]));   

							foreach($param['w_property_values'][$key] as $subkey => $property_value)

							{

								$rep.='<option id="wdform_'.$id1.'_'.$key.'_option'.$subkey.'" value="'.$property_value.'" '.($property_of_radio==$property_value ? 'selected="selected"' : "").'>'.$property_value.'</option>';

							}

							$rep.='</select></div></div>';

							}

							

							$rep.='</div></div></div>';

								

						

							$onsubmit_js.='

								jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_element_label'.$form_id.'\" />").val(

								jQuery("label[for=\'"+jQuery("input[name^=\'wdform_'.$id1.'_element'.$form_id.'\']:checked").prop("id")+"\']").eq(0).text()

								).appendTo("#adminForm");



								';

							$onsubmit_js.='

								jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_element_quantity_label'.$form_id.'\"  />").val("Quantity").appendTo("#adminForm");

										';

							$onsubmit_js.='

								jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_element_property_label'.$form_id.'\"  />").val("'.$param['w_property'][0].'").appendTo("#adminForm");

										';	

						break;

					}
            

            case 'type_paypal_shipping':

					{

					

						if($element_value=='')

							$element_value =' - ';

		

							$params_names=array('w_field_label_size','w_field_label_pos','w_flow','w_choices','w_choices_price','w_choices_checked','w_required','w_randomize','w_allow_other','w_allow_other_num','w_class');

							$temp=$params;



							if(strpos($temp, 'w_field_option_pos') > -1)

							$params_names=array('w_field_label_size','w_field_label_pos', 'w_field_option_pos', 'w_flow','w_choices','w_choices_price','w_choices_checked','w_required','w_randomize','w_allow_other','w_allow_other_num','w_choices_params', 'w_class');

							foreach($params_names as $params_name )

							{	

								$temp=explode('*:*'.$params_name.'*:*',$temp); 

								$param[$params_name] = $temp[0];

								$temp=$temp[1];

							}

							

							if($temp)

							{	

								$temp	=explode('*:*w_attr_name*:*',$temp);

								$attrs	= array_slice($temp,0, count($temp)-1);   

								foreach($attrs as $attr)

									$param['attributes'] = $param['attributes'].' '.$attr;

							}

							

							if(!isset($param['w_field_option_pos']))

								$param['w_field_option_pos'] = 'left';

								

							$param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");

							$param['w_field_option_pos1'] = ($param['w_field_option_pos']=="right" ? "style='float: none !important;'" : "");

							$param['w_field_option_pos2'] = ($param['w_field_option_pos']=="right" ? "style='float: left !important; margin-right: 8px !important; display: inline-block !important;'" : "");

							

							

							$param['w_choices']	= explode('***',$param['w_choices']);

							$param['w_choices_price']	= explode('***',$param['w_choices_price']);

							if(isset($param['w_choices_params']))

								$param['w_choices_params'] = explode('***',$param['w_choices_params']);	

						

							

							

							$element_value	= explode(' - ',$element_value);

							$element_value[1] = preg_replace("/[^0-9]/","",$element_value[1]);

								

							$rep='<div type="type_paypal_shipping" class="wdform-field"><div class="wdform-label-section" style="'.$param['w_field_label_pos'].'; width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';

						

							$rep.='</div><div class="wdform-element-section '.$param['w_class'].'" style="'.$param['w_field_label_pos'].';">';

							

							$total_queries = 0;

							foreach($param['w_choices'] as $key => $choice)

							{

								$key1 = $key + $total_queries;

								if(isset($param['w_choices_params']) && $param['w_choices_params'][$key])

								{			

									$choices_labels =array();
									$choices_values =array();
									$w_choices_params = explode('[where_order_by]',$param['w_choices_params'][$key]);
									$where = (str_replace(array('[',']'), '', $w_choices_params[0]) ? ' WHERE '.str_replace(array('[',']'), '', $w_choices_params[0]) : '');
									$w_choices_params = explode('[db_info]',$w_choices_params[1]);
									
									$order_by = str_replace(array('[',']'), '', $w_choices_params[0]);
									$db_info = str_replace(array('[',']'), '', $w_choices_params[1]);
				
									
									$label_table_and_column = explode(':',str_replace(array('[',']'), '', $choice));

									$table = $label_table_and_column[0];

									$label_column = $label_table_and_column[1];

									if($label_column)

									{

										$choices_labels = $this->model->select_data_from_db_for_labels($db_info, $label_column, $table, $where, $order_by);
									}	



									$value_table_and_column = explode(':',str_replace(array('[',']'), '', $param['w_choices_price'][$key]));

									$value_column = $value_table_and_column[1];



									if($value_column)

									{

										$choices_values = $this->model->select_data_from_db_for_values($db_info, $value_column, $table, $where, $order_by);

									}		



									$columns_count_shipping = count($choices_labels)>0 ?  count($choices_labels) : count($choices_values);

									

									if(array_filter($choices_labels) || array_filter($choices_values))

									{

										$total_queries = $total_queries + $columns_count_shipping-1;

										for($k=0; $k<$columns_count_shipping; $k++)

										{

											$choice_label = isset($choices_labels) ? $choices_labels[$k] : '';

											$choice_value = isset($choices_values) ? (float)$choices_values[$k] : '';

						

											$checked =(($choice_label==$element_value[0] && $choice_value== $element_value[1]) ? 'checked="checked"' : '');



											$rep.='<div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'">'.$choice_label.'</label><div class="radio-div forlabs"><input type="radio" id="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.$choice_value.'" title="'.htmlspecialchars($choice_label).'" '.$checked.' '.$param['attributes'].'><label for="wdform_'.$id1.'_element'.$form_id.''.($key1+$k).'"></label></div></div>';

										}	

									}	

								}

								else

								{

									$checked =(($choice==$element_value[0] && $param['w_choices_price'][$key]== $element_value[1]) ? 'checked="checked"' : '');

									

									$rep.='<div style="display: '.($param['w_flow']=='hor' ? 'inline-block' : 'table-row' ).';"><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.''.$key1.'">'.$choice.'</label><div class="radio-div forlabs"><input type="radio" id="wdform_'.$id1.'_element'.$form_id.''.$key1.'" name="wdform_'.$id1.'_element'.$form_id.'" value="'.$param['w_choices_price'][$key].'" title="'.htmlspecialchars($choice).'" '.$checked.' '.$param['attributes'].'><label for="wdform_'.$id1.'_element'.$form_id.''.$key1.'"></label></div></div>';

								}

							}



							$rep.='</div></div>';



						

							$onsubmit_js.='

								jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_element_label'.$form_id.'\" />").val(

								jQuery("label[for=\'"+jQuery("input[name^=\'wdform_'.$id1.'_element'.$form_id.'\']:checked").prop("id")+"\']").eq(0).text()

								).appendTo("#adminForm");



								';

				

						break;

					}
            
            case 'type_star_rating': { 					
              if($element_value=='')
                $element_value = '/'; 
                
                $params_names = array('w_field_label_size','w_field_label_pos','w_field_label_col','w_star_amount','w_required','w_class');
                $temp = $params;
                foreach($params_names as $params_name ) {	
                  $temp=explode('*:*'.$params_name.'*:*',$temp);
                  $param[$params_name] = $temp[0];
                  $temp=$temp[1];
                }

                if($temp) {	
                  $temp	=explode('*:*w_attr_name*:*',$temp);
                  $attrs	= array_slice($temp,0, count($temp)-1);   
                  foreach($attrs as $attr)
                    $param['attributes'] = $param['attributes'].' add_'.$attr;
                }
                          $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	
                
                $element_value = explode('/', $element_value);
        
                $images = '';	
                for($i=0; $i<$element_value[1]; $i++) {
                  $images .= '<img id="wdform_'.$id1.'_star_'.$i.'_'.$form_id.'" src="' . WD_FMC_URL . '/images/star.png" >';
                  
                  $onload_js .='jQuery("#wdform_'.$id1.'_star_'.$i.'_'.$form_id.'").mouseover(function() {change_src('.$i.',"wdform_'.$id1.'", "'.$form_id.'", "'.$param['w_field_label_col'].'");});';
                  $onload_js .='jQuery("#wdform_'.$id1.'_star_'.$i.'_'.$form_id.'").mouseout(function() {reset_src('.$i.',"wdform_'.$id1.'", "'.$form_id.'");});';
                  $onload_js .='jQuery("#wdform_'.$id1.'_star_'.$i.'_'.$form_id.'").click(function() {select_star_rating('.$i.',"wdform_'.$id1.'", "'.$form_id.'","'.$param['w_field_label_col'].'", "'.$element_value[1].'");});';
                  $onload_js .='select_star_rating('.($element_value[0]-1).',"wdform_'.$id1.'", "'.$form_id.'","'.$param['w_field_label_col'].'", "'.$element_value[1].'");';
                }
                
                $rep ='<div type="type_star_rating" class="wdform-field"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
                
                $rep.='</div><div class="wdform-element-section '.$param['w_class'].'"  style="'.$param['w_field_label_pos'].'"><div id="wdform_'.$id1.'_element'.$form_id.'" '.$param['attributes'].'>'.$images.'</div><input type="hidden" value="" id="wdform_'.$id1.'_selected_star_amount'.$form_id.'" name="wdform_'.$id1.'_selected_star_amount'.$form_id.'"></div></div>';
                
            
                $onsubmit_js.='
                jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_star_amount'.$form_id.'\" value = \"'.$param['w_star_amount'].'\" />").appendTo("#form'.$form_id.'");
                ';

              break;
            }
            case 'type_scale_rating': {
            
              if($element_value=='')
                $element_value = '/';
                
                $params_names=array('w_field_label_size','w_field_label_pos','w_mini_labels','w_scale_amount','w_required','w_class');
                $temp=$params;
                foreach($params_names as $params_name ) {	
                  $temp=explode('*:*'.$params_name.'*:*',$temp);
                  $param[$params_name] = $temp[0];
                  $temp=$temp[1];
                }

                if($temp) {	
                  $temp	=explode('*:*w_attr_name*:*',$temp);
                  $attrs	= array_slice($temp,0, count($temp)-1);   
                  foreach($attrs as $attr)
                    $param['attributes'] = $param['attributes'].' add_'.$attr;
                }
                          $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	
                
                $element_value = explode('/',$element_value);
                $w_mini_labels = explode('***',$param['w_mini_labels']);
                
                $numbers = '';	
                $radio_buttons = '';	
                $to_check=0;
                $to_check=$element_value[0];

                for($i=1; $i<=$element_value[1]; $i++) {
                  $numbers.= '<div  style="text-align: center; display: table-cell;"><span>'.$i.'</span></div>';
                  $radio_buttons.= '<div style="text-align: center; display: table-cell;"><div class="radio-div"><input id="wdform_'.$id1.'_scale_radio'.$form_id.'_'.$i.'" name="wdform_'.$id1.'_scale_radio'.$form_id.'" value="'.$i.'" type="radio" '.( $to_check==$i ? 'checked="checked"' : '' ).'><label for="wdform_'.$id1.'_scale_radio'.$form_id.'_'.$i.'"></label></div></div>';
                }
        
                $rep ='<div type="type_scale_rating" class="wdform-field"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
                 
                $rep.='</div><div class="wdform-element-section '.$param['w_class'].'"  style="'.$param['w_field_label_pos'].'"><div id="wdform_'.$id1.'_element'.$form_id.'" style="float: left;" '.$param['attributes'].'><label class="mini_label">'.$w_mini_labels[0].'</label><div  style="display: inline-table; vertical-align: middle;border-spacing: 7px;"><div style="display: table-row;">'.$numbers.'</div><div style="display: table-row;">'.$radio_buttons.'</div></div><label class="mini_label" >'.$w_mini_labels[1].'</label></div></div></div>';
                 
                $onsubmit_js.='
                jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_scale_amount'.$form_id.'\" value = \"'.$param['w_scale_amount'].'\" />").appendTo("#form'.$form_id.'");
                ';
              
                break;
            }
            
            case 'type_spinner': {
              
                $params_names=array('w_field_label_size','w_field_label_pos','w_field_width','w_field_min_value','w_field_max_value', 'w_field_step', 'w_field_value', 'w_required','w_class');
                $temp=$params;
                foreach($params_names as $params_name ) {	
                  $temp=explode('*:*'.$params_name.'*:*',$temp);
                  $param[$params_name] = $temp[0];
                  $temp=$temp[1];
                }

                if($temp) {	
                  $temp	=explode('*:*w_attr_name*:*',$temp);
                  $attrs	= array_slice($temp,0, count($temp)-1);   
                  foreach($attrs as $attr)
                    $param['attributes'] = $param['attributes'].' add_'.$attr;
                }
                          $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	
                
                
                $rep ='<div type="type_spinner" class="wdform-field"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
              
                $rep.='</div><div class="wdform-element-section '.$param['w_class'].'"  style="'.$param['w_field_label_pos'].'"><input type="text" value="'.($element_value!= 'null' ? $element_value : '').'" name="wdform_'.$id1.'_element'.$form_id.'" id="wdform_'.$id1.'_element'.$form_id.'" style="width: '.$param['w_field_width'].'px;" '.$param['attributes'].'></div></div>';
                
                $onload_js .='
                  jQuery("#wdform_'.$id1.'_element'.$form_id.'")[0].spin = null;
                  spinner = jQuery("#wdform_'.$id1.'_element'.$form_id.'").spinner();
                  spinner.spinner( "value", "'.($element_value!= 'null' ? $element_value : '').'");
                  jQuery("#wdform_'.$id1.'_element'.$form_id.'").spinner({ min: "'.$param['w_field_min_value'].'"});    
                  jQuery("#wdform_'.$id1.'_element'.$form_id.'").spinner({ max: "'.$param['w_field_max_value'].'"});
                  jQuery("#wdform_'.$id1.'_element'.$form_id.'").spinner({ step: "'.$param['w_field_step'].'"});
                ';
              
              break;
            }
            
            case 'type_slider': {
            
                $params_names=array('w_field_label_size','w_field_label_pos','w_field_width','w_field_min_value','w_field_max_value', 'w_field_value', 'w_required','w_class');
                $temp=$params;
                foreach($params_names as $params_name ) {	
                  $temp=explode('*:*'.$params_name.'*:*',$temp);
                  $param[$params_name] = $temp[0];
                  $temp=$temp[1];
                }

                if($temp) {	
                  $temp	=explode('*:*w_attr_name*:*',$temp);
                  $attrs	= array_slice($temp,0, count($temp)-1);   
                  foreach($attrs as $attr)
                    $param['attributes'] = $param['attributes'].' add_'.$attr;
                }
                $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	
                
                
                $rep ='<div type="type_slider" class="wdform-field"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
                
                 
                $rep.='</div><div class="wdform-element-section '.$param['w_class'].'"  style="'.$param['w_field_label_pos'].'"><input type="hidden" value="'.$element_value.'" id="wdform_'.$id1.'_slider_value'.$form_id.'" name="wdform_'.$id1.'_slider_value'.$form_id.'"><div name="'.$id1.'_element'.$form_id.'" id="wdform_'.$id1.'_element'.$form_id.'" style="width: '.$param['w_field_width'].'px;" '.$param['attributes'].'"></div><div align="left" style="display: inline-block; width: 33.3%; text-align: left;"><span id="wdform_'.$id1.'_element_min'.$form_id.'" class="label">'.$param['w_field_min_value'].'</span></div><div align="right" style="display: inline-block; width: 33.3%; text-align: center;"><span id="wdform_'.$id1.'_element_value'.$form_id.'" class="label">'.$element_value.'</span></div><div align="right" style="display: inline-block; width: 33.3%; text-align: right;"><span id="wdform_'.$id1.'_element_max'.$form_id.'" class="label">'.$param['w_field_max_value'].'</span></div></div></div>';
                    
                    
                $onload_js .='
                  jQuery("#wdform_'.$id1.'_element'.$form_id.'")[0].slide = null;
                  jQuery("#wdform_'.$id1.'_element'.$form_id.'").slider({
                    range: "min",
                    value: eval('.$element_value.'),
                    min: eval('.$param['w_field_min_value'].'),
                    max: eval('.$param['w_field_max_value'].'),
                    slide: function( event, ui ) {	
                    
                      jQuery("#wdform_'.$id1.'_element_value'.$form_id.'").html("" + ui.value)
                      jQuery("#wdform_'.$id1.'_slider_value'.$form_id.'").val("" + ui.value)

                    }
                    });
                ';
            
              break;
            }
            
            
            case 'type_range': {
            
              if($element_value=='')
                $element_value = '-';
                
                $params_names=array('w_field_label_size','w_field_label_pos','w_field_range_width','w_field_range_step','w_field_value1', 'w_field_value2', 'w_mini_labels', 'w_required','w_class');
                $temp=$params;
                foreach($params_names as $params_name ) {	
                  $temp=explode('*:*'.$params_name.'*:*',$temp);
                  $param[$params_name] = $temp[0];
                  $temp=$temp[1];
                }

                if($temp) {	
                  $temp	=explode('*:*w_attr_name*:*',$temp);
                  $attrs	= array_slice($temp,0, count($temp)-1);   
                  foreach($attrs as $attr)
                    $param['attributes'] = $param['attributes'].' add_'.$attr;
                }
                $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	
              
                $element_value = explode('-',$element_value);
                
                $w_mini_labels = explode('***',$param['w_mini_labels']);
                
                $rep ='<div type="type_range" class="wdform-field"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
               
                $rep.='</div><div class="wdform-element-section '.$param['w_class'].'"  style="'.$param['w_field_label_pos'].'"><div style="display: table;"><div style="display: table-row;"><div valign="middle" align="left" style="display: table-cell;"><input type="text" value="'.($element_value[0]!= 'null' ? $element_value[0] : '').'" name="wdform_'.$id1.'_element'.$form_id.'0" id="wdform_'.$id1.'_element'.$form_id.'0" style="width: '.$param['w_field_range_width'].'px;"  '.$param['attributes'].'></div><div valign="middle" align="left" style="display: table-cell; padding-left: 4px;"><input type="text" value="'.($element_value[1]!= 'null' ? $element_value[1] : '').'" name="wdform_'.$id1.'_element'.$form_id.'1" id="wdform_'.$id1.'_element'.$form_id.'1" style="width: '.$param['w_field_range_width'].'px;" '.$param['attributes'].'></div></div><div style="display: table-row;"><div valign="top" align="left" style="display: table-cell;"><label class="mini_label" id="wdform_'.$id1.'_mini_label_from">'.$w_mini_labels[0].'</label></div><div valign="top" align="left" style="display: table-cell;"><label class="mini_label" id="wdform_'.$id1.'_mini_label_to">'.$w_mini_labels[1].'</label></div></div></div></div></div>';
                 
                $onload_js .='
                  jQuery("#wdform_'.$id1.'_element'.$form_id.'0")[0].spin = null;
                  jQuery("#wdform_'.$id1.'_element'.$form_id.'1")[0].spin = null;
                  
                  spinner0 = jQuery("#wdform_'.$id1.'_element'.$form_id.'0").spinner();
                  spinner0.spinner( "value", "'.($element_value[0]!= 'null' ? $element_value[0] : '').'");
                  jQuery("#wdform_'.$id1.'_element'.$form_id.'").spinner({ step: '.$param['w_field_range_step'].'});
                  
                  spinner1 = jQuery("#wdform_'.$id1.'_element'.$form_id.'1").spinner();
                  spinner1.spinner( "value", "'.($element_value[1]!= 'null' ? $element_value[1] : '').'");
                  jQuery("#wdform_'.$id1.'_element'.$form_id.'").spinner({ step: '.$param['w_field_range_step'].'});
                ';
          
              break;
            }
            
            case 'type_grading': { 
                $params_names=array('w_field_label_size','w_field_label_pos', 'w_items', 'w_total', 'w_required','w_class');
                $temp=$params;
                foreach($params_names as $params_name ) {	
                  $temp=explode('*:*'.$params_name.'*:*',$temp);
                  $param[$params_name] = $temp[0];
                  $temp=$temp[1];
                }

                if($temp) {	
                  $temp	=explode('*:*w_attr_name*:*',$temp);
                  $attrs	= array_slice($temp,0, count($temp)-1);   
                  foreach($attrs as $attr)
                    $param['attributes'] = $param['attributes'].' add_'.$attr;
                }
                          $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	
              
                
                $element_value = explode(':', $element_value);
                
                $w_items = explode('***',$param['w_items']);
                  
                $w_items_labels =implode(':',$w_items);
                
                $grading_items ='';
                
              
                for($i=0; $i<(count($element_value))/2-1; $i++) {
                  $value=$element_value[$i];

                  $grading_items .= '<div class="wdform_grading"><input type="text" id="wdform_'.$id1.'_element'.$form_id.'_'.$i.'" name="wdform_'.$id1.'_element'.$form_id.'_'.$i.'"  value="'.$value.'" '.$param['attributes'].'><label class="wdform-ch-rad-label" for="wdform_'.$id1.'_element'.$form_id.'_'.$i.'">'.$w_items[$i].'</label></div>';
                    
                }
                  
                $rep ='<div type="type_grading" class="wdform-field"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
                
                $rep.='</div><div class="wdform-element-section '.$param['w_class'].'"  style="'.$param['w_field_label_pos'].'"><input type="hidden" value="'.$param['w_total'].'" name="wdform_'.$id1.'_grading_total'.$form_id.'" id="wdform_'.$id1.'_grading_total'.$form_id.'"><div id="wdform_'.$id1.'_element'.$form_id.'">'.$grading_items.'<div id="wdform_'.$id1.'_element_total_div'.$form_id.'" class="grading_div">Total: <span id="wdform_'.$id1.'_sum_element'.$form_id.'">0</span>/<span id="wdform_'.$id1.'_total_element'.$form_id.'">'.$param['w_total'].'</span><span id="wdform_'.$id1.'_text_element'.$form_id.'"></span></div></div></div></div>';
                
                $onload_js.='
                jQuery("#wdform_'.$id1.'_element'.$form_id.' input").change(function() {sum_grading_values("wdform_'.$id1.'","'.$form_id.'");});';
                
                $onload_js.='
                jQuery("#wdform_'.$id1.'_element'.$form_id.' input").keyup(function() {sum_grading_values("wdform_'.$id1.'","'.$form_id.'");});';
              
                
                $onload_js.='
                sum_grading_values("wdform_'.$id1.'","'.$form_id.'");';
                
                $onsubmit_js.='
                jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_hidden_item'.$form_id.'\" value = \"'.$w_items_labels.':'.$param['w_total'].'\" />").appendTo("#form'.$form_id.'");
                ';
            
              break;
            }
            case 'type_matrix': {
            
                $params_names=array('w_field_label_size','w_field_label_pos', 'w_field_input_type', 'w_rows', 'w_columns', 'w_required','w_class');
                $temp=$params;
                foreach($params_names as $params_name ) {	
                  $temp=explode('*:*'.$params_name.'*:*',$temp);
                  $param[$params_name] = $temp[0];
                  $temp=$temp[1];
                }

                if($temp) {	
                  $temp	=explode('*:*w_attr_name*:*',$temp);
                  $attrs	= array_slice($temp,0, count($temp)-1);   
                  foreach($attrs as $attr)
                    $param['attributes'] = $param['attributes'].' add_'.$attr;
                }
                
                $param['w_field_label_pos'] = ($param['w_field_label_pos']=="left" ? "float: left;" : "display:block;");	 
                $w_rows = explode('***',$param['w_rows']);
                $w_columns = explode('***',$param['w_columns']); 
                $element_value = str_replace("******matrix***","",$element_value);
                $element_value = explode($param['w_field_input_type'].'***', $element_value);
                $element_value = explode('***', $element_value[1]); 
                $column_labels ='';
                
                for($i=1; $i<count($w_columns); $i++) {
                  $column_labels .= '<div><label class="wdform-ch-rad-label">'.$w_columns[$i].'</label></div>';
                }
                
                $rows_columns = ''; 
                $for_matrix =0; 
                for($i=1; $i<count($w_rows); $i++) {
                
                  $rows_columns .= '<div class="wdform-matrix-row'.($i%2).'"><div class="wdform-matrix-column"><label class="wdform-ch-rad-label" >'.$w_rows[$i].'</label></div>';
                  
                
                  for($k=1; $k<count($w_columns); $k++) {
                    $rows_columns .= '<div class="wdform-matrix-cell">';
                    if($param['w_field_input_type']=='radio') { 	
                      if (array_key_exists($i-1,$element_value))
                        $to_check=$element_value[$i-1];
                      else
                        $to_check= '' ;
                                      
                      $rows_columns .= '<div class="radio-div"><input id="wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k.'"  type="radio" name="wdform_'.$id1.'_input_element'.$form_id.''.$i.'" value="'.$i.'_'.$k.'" '.($to_check==$i.'_'.$k ? 'checked="checked"' : '').'><label for="wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k.'"></label></div>';
                     
                    }
                    else
                      if($param['w_field_input_type']=='checkbox') {
                        
                        if (array_key_exists($for_matrix,$element_value))
                          $to_check=$element_value[$for_matrix];
                        else
                          $to_check= '' ;
                            
                        $rows_columns .= '<div class="checkbox-div"><input id="wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k.'" type="checkbox" name="wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k.'" value="1" '.($to_check=="1" ? 'checked="checked"' : '').'><label for="wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k.'"></label></div>';
                        
                        $for_matrix++;
                      }
                      else
                        if($param['w_field_input_type']=='text') {
                          $rows_columns .= '<input id="wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k.'" type="text" name="wdform_'.$id1.'_input_element'.$form_id.''.$i.'_'.$k.'" value="'.(array_key_exists($for_matrix,$element_value) ? $element_value[$for_matrix] : '').'">';
                        
                          $for_matrix++;										
                        }	
                        else
                          if($param['w_field_input_type']=='select') {
                            $rows_columns .= '<select id="wdform_'.$id1.'_select_yes_no'.$form_id.''.$i.'_'.$k.'" name="wdform_'.$id1.'_select_yes_no'.$form_id.''.$i.'_'.$k.'" ><option value="" '.(array_key_exists($for_matrix,$element_value) ? ($element_value[$for_matrix]=="" ? "selected=\"selected\"": "") : '').'> </option><option value="yes" '.(array_key_exists($for_matrix,$element_value) ? ($element_value[$for_matrix]=="yes" ? "selected=\"selected\"": "") : '').'>Yes</option><option value="no" '.(array_key_exists($for_matrix,$element_value) ? ($element_value[$for_matrix]=="no" ? "selected=\"selected\"": "") : '').'>No</option></select>';
                            
                            $for_matrix++;	
                          }
                    $rows_columns.='</div>';
                  }
                    
                  $rows_columns .= '</div>';	
                }
                  
                $rep ='<div type="type_matrix" class="wdform-field"><div class="wdform-label-section '.$param['w_class'].'" style="'.$param['w_field_label_pos'].' width: '.$param['w_field_label_size'].'px;"><span class="wdform-label">'.$label.'</span>';
                 
                $rep.='</div><div class="wdform-element-section '.$param['w_class'].'"  style="'.$param['w_field_label_pos'].'"><div id="wdform_'.$id1.'_element'.$form_id.'" class="wdform-matrix-table" '.$param['attributes'].'><div style="display: table-row-group;"><div class="wdform-matrix-head"><div style="display: table-cell;"></div>'.$column_labels.'</div>'.$rows_columns.'</div></div></div></div>';
                
                $onsubmit_js.='
                  jQuery("<input type=\"hidden\" name=\"wdform_'.$id1.'_input_type'.$form_id.'\" value = \"'.$param['w_field_input_type'].'\" /><input type=\"hidden\" name=\"wdform_'.$id1.'_hidden_row'.$form_id.'\" value = \"'.$param['w_rows'].'\" /><input type=\"hidden\" name=\"wdform_'.$id1.'_hidden_column'.$form_id.'\" value = \"'.$param['w_columns'].'\" />").appendTo("#form'.$form_id.'");
                  ';		
              
              break;
            }
          }
          $form = str_replace('%'.$id1.' - '.$labels[$id1s_key].'%', $rep, $form);
        }
      }
      echo $form;
      ?>
      
	  </div>
	  <input type="hidden" name="option" value="com_formmaker"/>
      <input type="hidden" id="current_id" name="current_id" value="<?php echo $rows[0]->group_id; ?>" />
      <input type="hidden" name="form_id" value="<?php echo $rows[0]->form_id; ?>" />
      <input type="hidden" name="date" value="<?php echo $rows[0]->date; ?>" />
      <input type="hidden" name="ip" value="<?php echo $rows[0]->ip; ?>" />
      <input type="hidden" id="task" name="task" value="" />
      <input type="hidden" value="<?php echo WD_FMC_URL; ?>" id="form_plugins_url" />
      <script type="text/javascript">
        function  pressbutton() {
          <?php echo $onsubmit_js; ?>;
        }
        jQuery("div[type='type_number'] input, div[type='type_phone'] input, div[type='type_spinner'] input, div[type='type_range'] input, .wdform-quantity").keypress(function(evt) {
          return check_isnum(evt);
        });	
        jQuery("div[type='type_grading'] input").keypress(function() {
          return check_isnum_or_minus(event);
        });
        plugin_url = '<?php echo WD_FMC_URL; ?>';
        <?php
        if ($onload_js) {
          ?>
          jQuery(window).load(function () {
            <?php echo $onload_js; ?>;
          });
          <?php
        }
        ?> 
      </script>
    </form> 
    <?php
  }
}

?>
