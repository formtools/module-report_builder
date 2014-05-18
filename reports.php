<?php

require_once("../../global/session_start.php");
ft_check_permission("client");
ft_include_module("report_builder");
$request = array_merge($_POST, $_GET);
$form_id = (isset($request["form_id"])) ? $request["form_id"] : "";

$report_info = rb_get_reports($form_id);

$page_vars = array();
$page_vars["page"] = "report_builder_reports_page";
$page_vars["heading"] = $report_info["heading"];
$page_vars["content"] = $report_info["content"];

if ($report_info["error"])
{
  $g_success = false;
  $g_message = $report_info["error"];
}

ft_display_page("../../modules/report_builder/templates/reports.tpl", $page_vars);
