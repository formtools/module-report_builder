<?php

require_once("../../global/library.php");

use FormTools\Modules;
use FormTools\Modules\ReportBuilder\General;

$module = Modules::initModulePage("client");


$form_id = (isset($request["form_id"])) ? $request["form_id"] : "";
$report_info = General::getReports($form_id);

$page_vars = array(
    "page" => "report_builder_reports_page",
    "heading" => $report_info["heading"],
    "content" => $report_info["content"]
);

if ($report_info["error"]) {
    $g_success = false;
    $g_message = $report_info["error"];
}

$module->displayPage("../../modules/report_builder/templates/reports.tpl", $page_vars);
