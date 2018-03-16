<?php

require_once("../../../global/library.php");

use FormTools\Core;
use FormTools\General;
use FormTools\Modules;

$module = Modules::initModulePage("client");


// the action to take and the ID of the page where it will be displayed (allows for
// multiple calls on same page to load content in unique areas)
$action  = $request["action"];

// Find out if we need to return anything back with the response. This mechanism allows us to pass any information
// between the Ajax submit function and the Ajax return function. Usage:
//   "return_vals[]=question1:answer1&return_vals[]=question2:answer2&..."
$return_val_str = "";
if (isset($request["return_vals"])) {
    $vals = array();
    foreach ($request["return_vals"] as $pair) {
        list($key, $value) = explode(":", $pair);
        $vals[] = "$key: \"$value\"";
    }
    $return_val_str = ", " . join(", ", $vals);
}


switch ($action) {
    case "get_reports":
        $form_id = $request["form_id"];
        $view_id = $request["view_id"];

        if (empty($form_id) || empty($view_id)) {
            exit;
        }

        if (!Core::$user->isAdmin()) {
            $account_id = Core::$user->getAccountId();
            if (!General::checkClientMayView($account_id, $form_id, $view_id)) {
                exit;
            }
        }

        $report_info = $module->getReports($form_id);
        echo $report_info["content"];
        break;
}

