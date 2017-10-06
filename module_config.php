<?php

$HOOKS = array(
    array(
        "hook_type"       => "code",
        "action_location" => "start",
        "function_name"   => "ft_construct_page_url",
        "hook_function"   => "rb_construct_page_url",
        "priority"        => "50"
    ),
    array(
        "hook_type"       => "code",
        "action_location" => "middle",
        "function_name"   => "ft_get_admin_menu_pages_dropdown",
        "hook_function"   => "rb_add_report_builder_menu_items",
        "priority"        => "50"
    ),
    array(
        "hook_type"       => "code",
        "action_location" => "middle",
        "function_name"   => "ft_get_client_menu_pages_dropdown",
        "hook_function"   => "rb_add_report_builder_menu_items",
        "priority"        => "50"
    ),
    array(
        "hook_type"       => "template",
        "action_location" => "head_bottom",
        "function_name"   => "",
        "hook_function"   => "rb_include_in_head",
        "priority"        => "50"
    ),
    array(
        "hook_type"       => "code",
        "action_location" => "main",
        "function_name"   => "ft_display_submission_listing_quicklinks",
        "hook_function"   => "rb_add_quicklink",
        "priority"        => "50"
    )
);
