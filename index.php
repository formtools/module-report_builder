<?php

require_once("../../global/library.php");
ft_init_module_page();
$request = array_merge($_POST, $_GET);

if (isset($request["update"]))
{
  $settings = array(
    "show_reports_icon_on_submission_listing_page" => (isset($request["show_reports_icon_on_submission_listing_page"])) ?  $request["show_reports_icon_on_submission_listing_page"] : "yes",
    "icon_behaviour" => (isset($request["icon_behaviour"])) ?  $request["icon_behaviour"] : "dialog"
  );
  ft_set_module_settings($settings);
  $g_success = true;
  $g_message = $L["notify_settings_updated"];
}

$module_settings = ft_get_module_settings();

$page_vars = array();
$page_vars["module_settings"] = $module_settings;
$page_vars["export_groups"] = array();
if (ft_check_module_available("export_manager") && ft_check_module_enabled("export_manager"))
{
  $page_vars["export_manager_available"] = true;
  ft_include_module("export_manager");
}

ft_display_module_page("templates/index.tpl", $page_vars);
