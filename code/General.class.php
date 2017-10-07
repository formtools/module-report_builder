<?php


namespace FormTools\Modules\ReportBuilder;

use FormTools\Core;
use FormTools\Forms;
use FormTools\Modules;
use FormTools\Sessions;
use FormTools\Views;

use Smarty;


class General
{
    public static function addReportBuilderMenuItems($params, $L)
    {
        $select_lines = $params["select_lines"];

        $select_lines[] = array("type" => "optgroup_open", "label" => $L["module_name"]);
        $select_lines[] = array("type" => "option", "k" => "rb_all_reports", "v" => $L["word_reports"]);

        $forms = Forms::getFormList();
        foreach ($forms as $form_info) {
            $form_id = $form_info["form_id"];
            $select_lines[] = array(
                "type" => "option",
                "k" => "rb_form_{$form_id}",
                "v" => $form_info["form_name"]
            );
        }
        $select_lines[] = array("type" => "optgroup_close");

        return array(
            "select_lines" => $select_lines
        );
    }


    /**
     * This function does the work of actually figuring out what reports should be displayed, and
     * returns the markup for the form(s) reports.
     *
     * @param mixed $form_id a valid form ID, or empty string for all
     * @return array
     */
    public static function getReports($form_id)
    {
        $LANG = Core::$L;
        $root_dir = Core::getRootDir();
        $root_url = Core::getRootUrl();

        $smarty = new Smarty();

        $return_info = array(
            "heading" => "",
            "content" => "",
            "error" => ""
        );

        // first off, find out if we want to display a single form or all of them
        $smarty->setTemplateDir("$root_dir/themes/default");
        $smarty->setCompileDir("$root_dir/themes/default/cache/");
        $smarty->assign("LANG", $LANG);
        $smarty->assign("g_root_url", $root_url);
        $smarty->assign("is_single_form", false);

        $is_single_form = false;
        $is_valid_single_form = null;
        if (!empty($form_id)) {
            $is_single_form = true;
            $is_valid_single_form = Forms::checkFormExists($form_id, false);
        }
        $smarty->assign("is_single_form", $is_single_form);
        $smarty->assign("is_valid_single_form", $is_valid_single_form);

        $export_manager_available = Modules::checkModuleUsable("export_manager");
        $smarty->assign("export_manager_available", $export_manager_available);

        $report_builder = Modules::getModuleInstance("report_builder");
        $L = $report_builder->getLangStrings();

        $smarty->assign("L", $L);
        $account_id = Core::$user->getAccountId();

        // deeply klutzy function, that ft_search_forms...
        if (Core::$user->isAdmin()) {
            $account_id = "";
        }
        $forms = Forms::searchForms(array(
            "account_id" => $account_id,
            "is_admin" => true
        ));

        // if this is only a single form, ignore all but the one we're interested in
        $current_form_name = "";
        if ($is_single_form && $is_valid_single_form) {
            $filtered_forms = array();
            foreach ($forms as $form_info) {
                if ($form_info["form_id"] != $form_id) {
                    continue;
                }

                $filtered_forms[] = $form_info;
                $current_form_name = $form_info["form_name"];
                break;
            }
            $forms = $filtered_forms;
        }
        $smarty->assign("current_form_name", $current_form_name);

        // this should only ever occur when a client attempts to hack the query string to get access to a form
        // that they are not permitted to see
        if (empty($forms)) {
            return array(
                "heading" => "", // give 'em no information :)
                "error" => $L["notify_form_no_permissions"],
                "content" => ""
            );
        }

        // generate the heading
        $return_info["heading"] = $smarty->fetch(realpath(__DIR__ . "/../templates/heading.tpl"));

        if (!$export_manager_available) {
            $return_info["error"] = $L["text_export_manager_not_available"];
            return $return_info;
        }

        // include the Export Manager. We're going to need it
        $export_manager = Modules::getModuleInstance("export_manager");

        $form_views = array();
        foreach ($forms as $form_info) {
            $curr_form_id = $form_info["form_id"];
            $views = Views::getFormViews($curr_form_id, $account_id);

            $updated_views = array();
            foreach ($views as $view_info) {
                $view_id = $view_info["view_id"];

                if (!Sessions::exists("view_{$view_id}_num_submissions")) {
                    Views::cacheViewStats($curr_form_id, $view_id);
                }

                if (!Sessions::isNonEmpty("view_{$view_id}_num_submissions") || !is_numeric(Sessions::get("view_{$view_id}_num_submissions"))) {
                    Sessions::set("view_{$view_id}_num_submissions", 0);
                }

                $view_info["num_submissions_in_view"] = number_format(Sessions::get("view_{$view_id}_num_submissions"));
                $updated_views[] = $view_info;
            }

            $form_views["form_{$curr_form_id}"] = $updated_views;
        }

        $groups = $export_manager->getExportGroups();
        $export_options = array();
        foreach ($groups as $group_info) {
            if ($group_info["visibility"] != "show") {
                continue;
            }

            $group_id = $group_info["export_group_id"];
            $export_types = $export_manager->getExportTypes($group_id, true);

            foreach ($export_types as $export_type_info) {
                if ($export_type_info["visibility"] == "hide") {
                    continue;
                }

                $export_options[] = array(
                    "group_id" => $group_info["export_group_id"],
                    "group_name" => $group_info["group_name"],
                    "icon" => $group_info["icon"],
                    "export_type_id" => $export_type_info["export_type_id"],
                    "export_type_name" => $export_type_info["export_type_name"],
                );
                break;
            }
        }

        $module_settings = Modules::getModuleSettings("", "report_builder");
        $expand_by_default = isset($module_settings["expand_by_default"]) ? $module_settings["expand_by_default"] : "no";

        $smarty->assign("forms", $forms);
        $smarty->assign("form_views", $form_views);
        $smarty->assign("export_options", $export_options);
        $smarty->assign("expand_by_default", $expand_by_default);

        $return_info["content"] = $smarty->fetch(realpath(__DIR__ . "/../templates/report.tpl"));

        return $return_info;
    }
}
