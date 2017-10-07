<?php

require("../../global/library.php");

use FormTools\Modules;

$module = Modules::initModulePage("admin");
$L = $module->getLangStrings();

$success = true;
$message = "";
if (isset($request["update"])) {
    $settings = array(
        "show_reports_icon_on_submission_listing_page" => (isset($request["show_reports_icon_on_submission_listing_page"])) ?  $request["show_reports_icon_on_submission_listing_page"] : "yes",
        "icon_behaviour" => (isset($request["icon_behaviour"])) ?  $request["icon_behaviour"] : "dialog",
        "expand_by_default" => (isset($request["expand_by_default"])) ?  $request["expand_by_default"] : "no"
    );
    Modules::setModuleSettings($settings);
    $message = $L["notify_settings_updated"];
}

$module_settings = Modules::getModuleSettings();

$page_vars = array(
    "g_success" => $success,
    "g_message" => $message,
    "module_settings" => $module_settings,
    "export_manager_available" => Modules::checkModuleUsable("export_manager")
);

$module->displayPage("templates/index.tpl", $page_vars);
