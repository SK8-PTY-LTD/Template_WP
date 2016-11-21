<?php
require_once(dirname(__FILE__).'/Table.php');

class Sgmb_ButtonsView extends SgmbB_Table
{
	public function __construct()
	{
		global $wpdb;
		parent::__construct('');

		$this->setRowsPerPage(SGMB_TABLE_LIMIT);
		$this->setTablename($wpdb->prefix.'sgmb_widget');
		$this->setColumns(array(
			'id',
			'title',
		));
		$this->setDisplayColumns(array(
			'id' => 'ID',
			'title' => 'Title',
			'shortcode' => 'Shortcode',
			'options' => 'Options'
		));
		$this->setSortableColumns(array(
			'id' => array('id', false),
			'title' => array('title', true),
			$this->setInitialSort(array(
			   'id' => 'DESC'
		   ))
		));
	}

	public function customizeRow(&$row)
	{
		$id = $row[0];
	   	$editUrl = admin_url()."admin.php?page=create-button&id=".$id;
		$row[2] = "<input type='text' onfocus='this.select();' readonly value='[sgmb id=".$id."]' class='large-text code'>";

		//CSRF token for delete action
		$ajax_nonce = wp_create_nonce('sgmb-delete-action');
		$ajax_nonce_clone = wp_create_nonce('sgmb-clone-action');
		$row[3] = '<a href="'.@$editUrl.'">'.__('Edit', 'sgpt').'</a>&nbsp;&nbsp;<a href="#" data-sgmb-csrf-token="'.$ajax_nonce.'" data-sgmb-button-id="'.$id.'" class="sgmb-js-delete-link">'.__('Delete', 'sgpt').'</a>&nbsp;&nbsp;<a href="#" data-sgmb-csrf-token="'.$ajax_nonce_clone.'" data-sgmb-button-id="'.$id.'" class="sgmb-js-clone-link">'.__('Clone', 'sgpt').'</a>';
	}

	public function customizeQuery(&$query)
	{
		$searchQuery = '';
		global $wpdb;
		if(isset($_POST['s']) && !empty($_POST['s']))
		{
			$searchCriteria = sanitize_text_field($_POST['s']);
			$searchQuery = " WHERE title LIKE '%$searchCriteria%' ";
		}
		$query .= $searchQuery;
	}
}
