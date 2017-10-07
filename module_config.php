<?php

$STRUCTURE = array();

$HOOKS = array(
    array(
        "hook_type"       => "code",
        "action_location" => "start",
        "function_name"   => "FormTools\\Pages::constructPageUrl",
        "hook_function"   => "constructPageUrl",
        "priority"        => "50"
    ),
    array(
        "hook_type"       => "code",
        "action_location" => "middle",
        "function_name"   => "FormTools\\Menus::getAdminMenuPagesDropdown",
        "hook_function"   => "addReportBuilderMenuItems",
        "priority"        => "50"
    ),
    array(
        "hook_type"       => "code",
        "action_location" => "middle",
        "function_name"   => "FormTools\\Menus::getClientMenuPagesDropdown",
        "hook_function"   => "addReportBuilderMenuItems",
        "priority"        => "50"
    ),
    array(
        "hook_type"       => "template",
        "action_location" => "head_bottom",
        "function_name"   => "",
        "hook_function"   => "includeInHead",
        "priority"        => "50"
    ),
    array(
        "hook_type"       => "code",
        "action_location" => "main",
        "function_name"   => "FormTools\\Submissions::displaySubmissionListingQuicklinks",
        "hook_function"   => "addQuicklink",
        "priority"        => "50"
    )
);

$FILES = array(
    "code/",
    "code/actions.php",
    "code/General.class.php",
    "code/index.html",
    "code/Module.class.php",
    "css/",
    "css/index.html",
    "css/reports.css",
    "images/",
    "images/icon_report_builder.png",
    "images/icon_report_builder16x16.png",
    "lang/",
    "lang/en_us.php",
    "lang/index.html",
    "scripts/",
    "scripts/reports.js",
    "templates/",
    "templates/heading.tpl",
    "templates/help.tpl",
    "templates/index.html",
    "templates/index.tpl",
    "templates/report.tpl",
    "templates/reports.tpl",
    "help.php",
    "index.php",
    "library.php",
    "LICENSE",
    "module_config.php",
    "README.md",
    "report.php",
    "reports.php"
);
