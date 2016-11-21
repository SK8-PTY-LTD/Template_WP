<?php
class SGMBInstaller
{
	public static function createTables($blogsId)
	{
		global $wpdb;
		$sgmbButtonBase = "CREATE TABLE IF NOT EXISTS ". $wpdb->prefix.$blogsId."sgmb_widget (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`type` varchar(255) NOT NULL,
			`title` varchar(255) NOT NULL,
			`options` text NOT NULL,
			PRIMARY KEY (id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8; ";
		$wpdb->query($sgmbButtonBase);
	}

	public static function install()
	{
		update_option('SGMB_SHARE_BUTTON_VERSION', SGMB_SHARE_BUTTON_VERSION);
		$obj = new self();
		$obj->createTables("");
		if(is_multisite()) {
			$sites = wp_get_sites();
			foreach($sites as $site) {
				$blogsId = $site['blog_id']."_";
				global $wpdb;
				$obj->createTables($blogsId);
			}
		}
	}

	public static function uninstall()
	{
		$obj = new self();
		$obj->uninstallTables("");
		if (is_multisite()) {
			$sites = wp_get_sites();
			foreach($sites as $site) {
				$blogsId = $site['blog_id']."_";
				$obj->uninstallTables($blogsId);
			}
		}
	}

	public function uninstallTables($blogsId)
	{
		global $wpdb;
		$sgmbTable = $wpdb->prefix.$blogsId."sgmb_widget";
		$sgmbSql = "DROP TABLE ". $sgmbTable;
		$wpdb->query($sgmbSql);	
	}
}