<?php


function report_builder__install($module_id)
{
  global $g_table_prefix, $g_root_dir, $g_root_url, $LANG;

  $success = "";
  $message = "";
  $encrypted_key = isset($_POST["ek"]) ? $_POST["ek"] : "";
  $module_key    = isset($_POST["k"]) ? $_POST["k"] : "";
  if (empty($encrypted_key) || empty($module_key) || $encrypted_key != crypt($module_key, "xp"))
  {
    $success = false;
  }
  else
  {
  	$success = true;
	  ft_register_hook("code", "report_builder", "start", "ft_construct_page_url", "rb_construct_page_url", 50, true);
	  ft_register_hook("code", "report_builder", "middle", "ft_get_admin_menu_pages_dropdown", "rb_add_report_builder_menu_items", 50, true);
	  ft_register_hook("code", "report_builder", "middle", "ft_get_client_menu_pages_dropdown", "rb_add_report_builder_menu_items", 50, true);
	  ft_register_hook("template", "report_builder", "head_bottom", "", "rb_include_in_head");
	  ft_register_hook("code", "report_builder", "main", "ft_display_submission_listing_quicklinks", "rb_add_quicklink", 50, true);

	  $settings = array(
	    "show_reports_icon_on_submission_listing_page" => "yes",
	    "icon_behaviour" => "dialog"
	  );
	  ft_set_settings($settings, "report_builder");
  }

  return array($success, $message);
}


function report_builder__uninstall($module_id)
{
  global $g_table_prefix;

  mysql_query("
    DELETE FROM {$g_table_prefix}menu_items
    WHERE (page_identifier = 'rb_all_reports') OR
          (page_identifier LIKE 'rb_form_%')
  ");

  return array(true, "");
}
