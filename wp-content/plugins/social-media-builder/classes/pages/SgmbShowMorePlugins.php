<?php

class SgmbShowMorePlugins
{	
	public function init()
	{
		$this->renderScripts();		
		$this->render();
	}

	public function render()
	{
		require_once(SGMB_VIEW.'sgmb_more_plugins.php');
	}

	public function renderScripts()
	{
		wp_register_style('sgmb_widget_style', SGMB_URL.'css/widget/widget-style.css');
		wp_enqueue_style('sgmb_widget_style');
	}
}