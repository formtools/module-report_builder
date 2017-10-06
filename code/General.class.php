<?php


function rb_add_report_builder_menu_items($params)
{
  $L = ft_get_module_lang_file_contents("report_builder");

  $select_lines = $params["select_lines"];

  $select_lines[] = array("type" => "optgroup_open", "label" => $L["module_name"]);
  $select_lines[] = array("type" => "option", "k" => "rb_all_reports", "v" => $L["word_reports"]);

  $forms = ft_get_form_list();
  foreach ($forms as $form_info)
  {
    $form_id = $form_info["form_id"];
    $select_lines[] = array("type" => "option", "k" => "rb_form_{$form_id}", "v" => $form_info["form_name"]);
  }
  $select_lines[] = array("type" => "optgroup_close");

  return array(
    "select_lines" => $select_lines
  );
}


function rb_construct_page_url($params)
{
  $page_identifier = $params["page_identifier"];

  $url = "";
  if ($page_identifier == "rb_all_reports")
  {
    $url = "/modules/report_builder/reports.php";
  }
  else if (preg_match("/rb_form_(\d+)/", $page_identifier, $matches))
  {
    $form_id = $matches[1];
    $url = "/modules/report_builder/reports.php?form_id=$form_id";
  }

  return array("url" => $url);
}


/**
 * This embeds the necessary styles on the Reports pages.
 *
 * @param string $location
 * @param array $params
 */
function rb_include_in_head($location, $params)
{
  global $g_root_url, $LANG;

  if ($params["page"] == "report_builder_reports_page")
  {
    echo <<< END
<link type="text/css" rel="stylesheet" href="$g_root_url/modules/report_builder/global/css/reports.css">
<script src="$g_root_url/modules/report_builder/global/scripts/reports.js?v=2"></script>
END;
  }

  if ($params["page"] == "admin_forms" || $params["page"] == "client_forms")
  {
  	$L = ft_get_module_lang_file_contents("report_builder");

    echo <<< END
<link type="text/css" rel="stylesheet" href="$g_root_url/modules/report_builder/global/css/reports.css">
<script src="$g_root_url/modules/report_builder/global/scripts/reports.js"></script>
<script>
g.reports_dialog = $("<div id=\"rb_reports_dialog\"><div style=\"text-align: center; padding: 30px\"><img src=\"{$g_root_url}/global/images/loading.gif\" /></div></div>");
g.preload_loading_icon = new Image(32, 32);
g.preload_loading_icon.src = "{$g_root_url}/global/images/loading.gif";

g.show_reports_dialog = function() {
  ft.create_dialog({
    title: "{$L["word_reports"]}",
    dialog: g.reports_dialog,
    min_width:  700,
    max_height: 400,
    min_height: 400,
    open: function() {
      $.ajax({
        url:  g.root_url + "/modules/report_builder/global/code/actions.php",
        data: {
          action:  "get_reports",
          form_id: ms.form_id,
          view_id: ms.view_id
        },
        type: "POST",
        success: function(html) {
          $("#rb_reports_dialog").html(html);
        }
      })
    },
    buttons: [{
      text: "{$LANG["word_close"]}",
      click: function() { $(this).dialog("close"); }
    }]
  });

  return false;
}
</script>
END;
  }
}


function rb_add_quicklink($params)
{
  global $g_root_url, $g_smarty;

  $form_id = $g_smarty->_tpl_vars["SESSION"]["curr_form_id"];

  $settings = array("show_reports_icon_on_submission_listing_page", "icon_behaviour");
  $results = ft_get_settings($settings, "report_builder");
  $show_reports_icon_on_submission_listing_page = $results["show_reports_icon_on_submission_listing_page"];
  if ($show_reports_icon_on_submission_listing_page != "yes")
    return;

  $icon_behaviour = $results["icon_behaviour"];

  $L = ft_get_module_lang_file_contents("report_builder");

  $quicklinks = array(
    "icon_url"   => "{$g_root_url}/modules/report_builder/images/icon_report_builder16x16.png",
    "title_text" => $L["phrase_view_reports"]
  );

  if ($icon_behaviour == "dialog")
  {
    $quicklinks["href"]    = "#";
    $quicklinks["onclick"] = "return g.show_reports_dialog()";
  }
  else
  {
    $quicklinks["href"] = "$g_root_url/modules/report_builder/reports.php?form_id=$form_id";
  }


  return array("quicklinks" => $quicklinks);
}



/**
 * This function does the work of actually figuring out what reports should be displayed, and
 * returns the markup for the form(s) reports.
 *
 * @param mixed $form_id a valid form ID, or empty string for all
 * @return array
 */
function rb_get_reports($form_id)
{
  global $g_smarty, $g_smarty_use_sub_dirs, $g_root_dir, $g_root_url, $LANG;

  $return_info = array(
    "heading" => "",
    "content" => "",
    "error"   => ""
  );

  // first off, find out if we want to display a single form or all of them
  $smarty = $g_smarty;
  $smarty->template_dir = "$g_root_dir/themes/default";
  $smarty->compile_dir  = "$g_root_dir/themes/default/cache/";
  $smarty->use_sub_dirs = $g_smarty_use_sub_dirs;
  $smarty->assign("LANG", $LANG);
  $smarty->assign("g_root_url", $g_root_url);
  $smarty->assign("is_single_form", false);

  $is_single_form = false;
  $is_valid_single_form = null;
  if (!empty($form_id))
  {
    $is_single_form = true;
    if (ft_check_form_exists($form_id, false))
    {
      $is_valid_single_form = true;
    }
    else
    {
      $is_valid_single_form = false;
    }
  }
  $smarty->assign("is_single_form", $is_single_form);
  $smarty->assign("is_valid_single_form", $is_valid_single_form);

  $export_manager_available = false;
  if (ft_check_module_available("export_manager") && ft_check_module_enabled("export_manager"))
  {
    $smarty->assign("export_manager_available", true);
    $export_manager_available = true;
  }

  $account_id = isset($_SESSION["ft"]["account"]["account_id"]) ? $_SESSION["ft"]["account"]["account_id"] : "";

  // deeply klutzy function, that ft_search_forms...
  if (ft_is_admin())
  {
    $account_id = "";
  }

  $forms = ft_search_forms($account_id, true);

  // if this is only a single form, ignore all but the one we're interested in
  $current_form_name = "";
  if ($is_single_form && $is_valid_single_form)
  {
    $filtered_forms = array();
    foreach ($forms as $form_info)
    {
      if ($form_info["form_id"] != $form_id)
        continue;

      $filtered_forms[] = $form_info;
      $current_form_name = $form_info["form_name"];
      break;
    }
    $forms = $filtered_forms;
  }
  $smarty->assign("current_form_name", $current_form_name);

  // this should only ever occur when a client attempts to hack the query string to get access to a form
  // that they are not permitted to see
  if (empty($forms))
  {
    return array(
      "heading" => "", // give 'em no information :)
      "error"   => $LANG["report_builder"]["notify_form_no_permissions"],
      "content" => ""
    );
  }

  // generate the heading
  $return_info["heading"] = $smarty->fetch(realpath(dirname(__FILE__) . "/../../templates/heading.tpl"));

  if (!$export_manager_available)
  {
    $return_info["error"] = $LANG["report_builder"]["text_export_manager_not_available"];
    return $return_info;
  }

  // include the Export Manager. We're going to need it
  ft_include_module("export_manager");

  $form_views = array();
  foreach ($forms as $form_info)
  {
    $curr_form_id = $form_info["form_id"];
    $views = ft_get_form_views($curr_form_id, $account_id);

    $updated_views = array();
    foreach ($views as $view_info)
    {
      $view_id = $view_info["view_id"];
      if (!isset($_SESSION["ft"]["view_{$view_id}_num_submissions"]))
      {
        _ft_cache_view_stats($curr_form_id, $view_id);
      }

      if (!isset($_SESSION["ft"]["view_{$view_id}_num_submissions"]) || empty($_SESSION["ft"]["view_{$view_id}_num_submissions"]) || !is_numeric($_SESSION["ft"]["view_{$view_id}_num_submissions"]))
        $_SESSION["ft"]["view_{$view_id}_num_submissions"] = 0;

      $view_info["num_submissions_in_view"] = number_format($_SESSION["ft"]["view_{$view_id}_num_submissions"]);
      $updated_views[] = $view_info;
    }

    $form_views["form_{$curr_form_id}"] = $updated_views;
  }

  $groups = exp_get_export_groups();
  $export_options = array();
  foreach ($groups as $group_info)
  {
    if ($group_info["visibility"] != "show")
      continue;

    $group_id = $group_info["export_group_id"];
    $export_types = exp_get_export_types($group_id, true);

    foreach ($export_types as $export_type_info)
    {
      if ($export_type_info["visibility"] == "hide")
        continue;

      $export_options[] = array(
        "group_id"         => $group_info["export_group_id"],
        "group_name"       => $group_info["group_name"],
        "icon"             => $group_info["icon"],
        "export_type_id"   => $export_type_info["export_type_id"],
        "export_type_name" => $export_type_info["export_type_name"],
      );
      break;
    }
  }

  $module_settings = ft_get_module_settings("", "report_builder");
  $expand_by_default = isset($module_settings["expand_by_default"]) ? $module_settings["expand_by_default"] : "no";

  $smarty->assign("forms", $forms);
  $smarty->assign("form_views", $form_views);
  $smarty->assign("export_options", $export_options);
  $smarty->assign("expand_by_default", $expand_by_default);
  $return_info["content"] = $smarty->fetch(realpath(dirname(__FILE__) . "/../../templates/report.tpl"));

  return $return_info;
}
