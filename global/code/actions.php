<?php

require_once(realpath(dirname(__FILE__) . "/../../../../global/session_start.php"));
ft_include_module("report_builder");
ft_check_permission("client");


// the action to take and the ID of the page where it will be displayed (allows for
// multiple calls on same page to load content in unique areas)
$request = array_merge($_POST, $_GET);
$action  = $request["action"];

// Find out if we need to return anything back with the response. This mechanism allows us to pass any information
// between the Ajax submit function and the Ajax return function. Usage:
//   "return_vals[]=question1:answer1&return_vals[]=question2:answer2&..."
$return_val_str = "";
if (isset($request["return_vals"]))
{
  $vals = array();
  foreach ($request["return_vals"] as $pair)
  {
    list($key, $value) = split(":", $pair);
    $vals[] = "$key: \"$value\"";
  }
  $return_val_str = ", " . join(", ", $vals);
}


switch ($action)
{
  case "get_reports":
    $form_id = $request["form_id"];
    $view_id = $request["view_id"];

    if (empty($form_id) || empty($view_id))
    {
      exit;
    }

    if (!ft_is_admin())
    {
      $account_id = isset($_SESSION["ft"]["account"]["account_id"]) ? $_SESSION["ft"]["account"]["account_id"] : "";
      if (!ft_check_client_may_view($account_id, $form_id, $view_id))
      {
        exit;
      }
    }

    $report_info = rb_get_reports($form_id);
    echo $report_info["content"];
    break;
}

