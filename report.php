<?php

require_once("../../global/library.php");

use FormTools\Modules;
use FormTools\Sessions;
use FormTools\Views;

$module = Modules::initModulePage("client");

if (isset($_GET["form_id"])) {
    Sessions::set("curr_form_id", $_GET["form_id"]);
    $form_id = $_GET["form_id"];
} else {
    $form_id = Sessions::get("curr_form_id");
}

if (isset($_GET["view_id"])) {
    Sessions::set("form_{$form_id}_view_id", $_GET["view_id"]);
    $view_id = $_GET["view_id"];
} else {
    $view_id = Sessions::get("form_{$form_id}_view_id");
}

// sort order
$view_info = Views::getView($view_id);
$order = "{$view_info['default_sort_field']}-{$view_info['default_sort_field_order']}";

$export_manager = Modules::getModuleInstance("export_manager");
$export_manager->export(array(
    "form_id" => $form_id,
    "view_id" => $view_id,
    "order" => $order,
    "search_fields" => array(
        "search_field"   => "",
        "search_date"    => "",
        "search_keyword" => ""
    ),
    "export_group_id" => $request["export_group_id"],
    "export_type_id" => $request["export_type_id"],
    "results" => "all"
));
