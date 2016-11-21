<?php
class FMViewThemes_fmc {
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
    $search_select_value = ((isset($_POST['search_select_value'])) ? (int)$_POST['search_select_value'] : 0);
    $asc_or_desc = ((isset($_POST['asc_or_desc']) && $_POST['asc_or_desc'] == 'desc') ? 'desc' : 'asc');
	$order_by_array = array('id', 'title', 'default');
    $order_by = isset($_POST['order_by']) && in_array(esc_html(stripslashes($_POST['order_by'])), $order_by_array) ? esc_html(stripslashes($_POST['order_by'])) :  'id';
    $order_class = 'manage-column column-title sorted ' . $asc_or_desc;
    $ids_string = '';
    ?>
    <div class="fm-user-manual">
		This section allows you to create, edit form themes.
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
    <form class="wrap" id="themes_form" method="post" action="admin.php?page=themes_fmc" style="width:99%;">
		<?php wp_nonce_field('nonce_fmc', 'nonce_fmc'); ?>
		<div class="fm-page-banner themes-banner">
			<div class="theme_icon">
			</div>
			<div class="fm-logo-title">Themes</div>
			<button class="fm-button add-button medium" style="margin-left: 31px;" onclick="fm_set_input_value('task', 'add'); fm_form_submit(event, 'themes_form');">
				<span></span>
				Add New
			</button>
			<div class="fm-page-actions">
				<button class="fm-button delete-button small" onclick="if (confirm('Do you want to delete selected item(s)?')) { fm_set_input_value('task', 'delete_all'); } else { return false; }">
					<span></span>
					Delete
				</button>
			</div>
		</div>	
		<div class="fm-clear"></div>		
		<div class="tablenav top">
			<?php
				WDW_FMC_Library::search('Title', $search_value, 'themes_form');
				WDW_FMC_Library::html_page_nav($page_nav['total'], $page_nav['limit'], 'themes_form');
			?>
		</div>
		<table class="wp-list-table widefat fixed pages">
			<thead>
				<th class="manage-column column-cb check-column table_small_col"><input id="check_all" type="checkbox" style="margin:0;"/></th>
				<th class="table_small_col <?php if ($order_by == 'id') { echo $order_class; } ?>">
					<a onclick="fm_set_input_value('task', ''); fm_set_input_value('order_by', 'id'); fm_set_input_value('asc_or_desc', '<?php echo (($order_by == 'id' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>'); fm_form_submit(event, 'themes_form')" href="">
					<span>ID</span><span class="sorting-indicator"></span></a>
				</th>
				<th class="<?php if ($order_by == 'title') { echo $order_class; } ?>">
					<a onclick="fm_set_input_value('task', ''); fm_set_input_value('order_by', 'title'); fm_set_input_value('asc_or_desc', '<?php echo (($order_by == 'title' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>'); fm_form_submit(event, 'themes_form')" href="">
					<span>Title</span><span class="sorting-indicator"></span></a>
				</th>
				<th class="table_big_col <?php if ($order_by == 'default') { echo $order_class; } ?>">
					<a onclick="fm_set_input_value('task', ''); fm_set_input_value('order_by', 'default'); fm_set_input_value('asc_or_desc', '<?php echo (($order_by == 'default' && $asc_or_desc == 'asc') ? 'desc' : 'asc'); ?>'); fm_form_submit(event, 'themes_form')" href="">
					<span>Default</span><span class="sorting-indicator"></span></a>
				</th>
				<th class="table_small_col">Edit</th>
				<th class="table_small_col">Delete</th>
			</thead>
			<tbody id="tbody_arr">
			<?php
				if ($rows_data) {
					foreach ($rows_data as $row_data) {
						$alternate = (!isset($alternate) || $alternate == 'class="alternate"') ? '' : 'class="alternate"';
						$default_image = (($row_data->default) ? 'default' : 'notdefault');
						$default = (($row_data->default) ? '' : 'setdefault');
						?>
						<tr id="tr_<?php echo $row_data->id; ?>" <?php echo $alternate; ?>>
							<td class="table_small_col check-column">
								<input id="check_<?php echo $row_data->id; ?>" name="check_<?php echo $row_data->id; ?>" type="checkbox"/>
							</td>
							<td class="table_small_col"><?php echo $row_data->id; ?></td>
							<td>
								<a onclick="fm_set_input_value('task', 'edit'); fm_set_input_value('current_id', '<?php echo $row_data->id; ?>'); fm_form_submit(event, 'themes_form')" href="" title="Edit"><?php echo $row_data->title; ?></a>
							</td>
							<td class="table_big_col">
								<?php if ($default != '') { ?>
									<a onclick="fm_set_input_value('task', '<?php echo $default; ?>'); fm_set_input_value('current_id', '<?php echo $row_data->id; ?>'); fm_form_submit(event, 'themes_form')" href="">
								<?php } ?>
									<img src="<?php echo WD_FMC_URL . '/images/' . $default_image . '.png?ver='. get_option("wd_form_maker_version").''; ?>" />
								<?php if ($default != '') { ?>
									</a>
								<?php } ?>
							</td>
							<td class="table_small_col">
								<button class="fm-icon edit-icon" onclick="fm_set_input_value('task', 'edit'); fm_set_input_value('current_id', '<?php echo $row_data->id; ?>'); fm_form_submit(event, 'themes_form');">
									<span></span>
								</button>
							</td>
							<td class="table_small_col">
								<button class="fm-icon delete-icon" onclick="if (confirm('Do you want to delete selected item(s)?')) { fm_set_input_value('task', 'delete'); fm_set_input_value('current_id', '<?php echo $row_data->id; ?>'); fm_form_submit(event, 'themes_form'); } else {return false;}">
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

	public function edit($id, $reset) {
		$row = $this->model->get_row_data($id, $reset);
		$page_title = (($id != 0) ? 'Edit theme ' . $row->title : 'Create new theme');
		?>
		<style>
		.CodeMirror {
			border: 1px solid #ccc;
			font-size: 12px;
			margin-bottom: 6px;
			background: white;
		}
		</style>
		<div class="fm-user-manual">
			This section allows you to create, edit form themes.
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
		<form class="wrap" method="post" action="admin.php?page=themes_fmc" style="width:99%;">
			<?php wp_nonce_field('nonce_fmc', 'nonce_fmc'); ?>     
			<div class="fm-page-header">
				<div class="fm-page-title">
					<?php echo $page_title; ?>
				</div>
				<div class="fm-page-actions">
					<button class="fm-button save-button small" onclick="if (fm_check_required('title', 'Title')) {return false;}; fm_set_input_value('task', 'save');">
						<span></span>
						Save
					</button>
					<button class="fm-button apply-button small" onclick="if (fm_check_required('title', 'Title')) {return false;}; fm_set_input_value('task', 'apply');">
						<span></span>
						Apply
					</button>
					<button class="fm-button cancel-button small" onclick="fm_set_input_value('task', 'cancel');">
						<span></span>
						Cancel
					</button>
				</div>
			</div>

			<table style="clear:both;">
				<tbody>
					<tr>
						<td class="fm_label"><label for="title">Title: <span style="color:#FF0000;"> * </span> </label></td>
						<td><input type="text" id="title" name="title" value="<?php echo $row->title; ?>" class="fm_text_input" /></td>
					</tr>
					<tr>
						<td class="fm_label"><label for="css">Css: </label></td>
						<td style="width: 90%;"><textarea id="css" name="css" rows="30" style="width: 100%;"><?php echo htmlspecialchars($row->css) ?></textarea></td>
					</tr>
				</tbody>
			</table>
			<input type="hidden" id="task" name="task" value=""/>
			<input type="hidden" id="current_id" name="current_id" value="<?php echo $row->id; ?>"/>
			<input type="hidden" id="default" name="default" value="<?php echo $row->default; ?>"/>
		</form>
		<script>
			var editor = CodeMirror.fromTextArea(document.getElementById("css"), {
				lineNumbers: true,
				lineWrapping: true,
				mode: "css"
			});
      
			CodeMirror.commands["selectAll"](editor);
			editor.autoFormatRange(editor.getCursor(true), editor.getCursor(false));
			editor.scrollTo(0,0);      
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