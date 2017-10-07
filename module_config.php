<?php

$HOOKS = array(
    array(
        "hook_type"       => "code",
        "action_location" => "start",
        "function_name"   => "ft_construct_page_url",
        "hook_function"   => "constructPageUrl",
        "priority"        => "50"
    ),
    array(
        "hook_type"       => "code",
        "action_location" => "middle",
        "function_name"   => "ft_get_admin_menu_pages_dropdown",
        "hook_function"   => "addReportBuilderMenuItems",
        "priority"        => "50"
    ),
    array(
        "hook_type"       => "code",
        "action_location" => "middle",
        "function_name"   => "ft_get_client_menu_pages_dropdown",
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
        "function_name"   => "ft_display_submission_listing_quicklinks",
        "hook_function"   => "addQuicklink",
        "priority"        => "50"
    )
);
