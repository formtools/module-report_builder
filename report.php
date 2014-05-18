<?php

require_once("../../global/session_start.php");
ft_include_module("export_manager");
ft_check_permission("client");

if (isset($_GET["form_id"]))
{
  $form_id = $_GET["form_id"];
  $_SESSION["ft"]["curr_form_id"] = $form_id;
}
else
{
  $form_id = $_SESSION["ft"]["curr_form_id"];
}

if (isset($_GET["view_id"]))
{
  $view_id = $_GET["view_id"];
  $_SESSION["ft"]["form_{$form_id}_view_id"] = $view_id;
}
else
{
  $view_id = $_SESSION["ft"]["form_{$form_id}_view_id"];
}

// sort order
$view_info = ft_get_view($view_id);
$order = "{$view_info['default_sort_field']}-{$view_info['default_sort_field_order']}";


$_SESSION["ft"]["current_search"]["order"] = $order;
$_SESSION["ft"]["current_search"]["search_fields"] = array(
  "search_field"   => "",
  "search_date"    => "",
  "search_keyword" => ""
);

require_once(realpath(dirname(__FILE__) . "/../export_manager/export.php"));
