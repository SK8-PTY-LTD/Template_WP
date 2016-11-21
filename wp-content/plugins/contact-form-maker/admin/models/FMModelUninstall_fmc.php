<?php

class FMModelUninstall_fmc {
  ////////////////////////////////////////////////////////////////////////////////////////
  // Events                                                                             //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Constants                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Variables                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Constructor & Destructor                                                           //
  ////////////////////////////////////////////////////////////////////////////////////////
  public function __construct() {
  }
  ////////////////////////////////////////////////////////////////////////////////////////
  // Public Methods                                                                     //
  ////////////////////////////////////////////////////////////////////////////////////////
  public function delete_db_tables() {
    global $wpdb;
    $true_or_false_forms = $wpdb->get_var("SELECT COUNT(*) FROM " . $wpdb->prefix . "formmaker WHERE `id` IN (" . (get_option('contact_form_forms', '') != '' ? get_option('contact_form_forms') : 0) . ")");
    if ($true_or_false_forms) {
      $wpdb->query("DELETE FROM " . $wpdb->prefix . "formmaker WHERE `id` NOT IN (" . (get_option('contact_form_forms', '') != '' ? get_option('contact_form_forms') : 0) . ")");
      $wpdb->query("DELETE FROM " . $wpdb->prefix . "formmaker_submits WHERE `form_id` NOT IN (" . (get_option('contact_form_forms', '') != '' ? get_option('contact_form_forms') : 0) . ")");
      $wpdb->query("DELETE FROM " . $wpdb->prefix . "formmaker_views WHERE `form_id` NOT IN (" . (get_option('contact_form_forms', '') != '' ? get_option('contact_form_forms') : 0) . ")");
	  delete_option('contact_form_forms');
    }
    else {
      $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "formmaker");
      $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "formmaker_submits");
      $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "formmaker_views");
      $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "formmaker_themes");
      $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "formmaker_sessions");
      $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "formmaker_blocked");
      $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "formmaker_query");
      $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "formmaker_backup");
	  delete_option('contact_form_forms');
      delete_option("wd_form_maker_version");
      delete_option('formmaker_cureent_version');
      delete_option('fm_emailverification');
      delete_option('form_maker_pro_active');
	  
    }
	delete_option('fmc_settings');
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