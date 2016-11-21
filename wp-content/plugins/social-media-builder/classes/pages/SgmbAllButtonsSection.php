<?php

class SgmbAllButtonsSection
{
	private $table;

	public function init()
	{
		$this->renderScripts();
		require_once(SGMB_CLASSES.'sgmbDataTable/SgmbButtonsTable.php');
		$this->table = new Sgmb_ButtonsView();
		$this->render();
	}

	public function render()
	{
		require_once(SGMB_VIEW.'sgmb_buttons_main.php');
	}

	public function renderScripts()
	{
		wp_register_script('sgmb-delete-scripts',SGMB_URL.'js/allButtonsSection/sgmb-main.js', array('jquery'),null);
		wp_enqueue_script('sgmb-delete-scripts');
		wp_register_style('sgmb_widget_style', SGMB_URL.'css/widget/widget-style.css');
		wp_enqueue_style('sgmb_widget_style');
	}
	public static function showInfo()
	{
		$sgmbInfo = '';
		$divisor = "<span class=\"info-vertical-divisor\">|</span>";
		$sgmbInfo .= "<span>If you like the plugin, please <a href=\"https://wordpress.org/support/view/plugin-reviews/social-media-builder?filter=5\" target=\"_blank\">rate it 5 stars</a></span>".$divisor;
		$sgmbInfo .= "<a href=\"https://wordpress.org/support/plugin/social-media-builder\" target=\"_blank\">Support</a>";
		echo $sgmbInfo;
	}
}
