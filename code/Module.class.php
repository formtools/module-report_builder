<?php


namespace FormTools\modules\ReportBuilder;

use FormTools\Core;
use FormTools\Hooks;
use FormTools\Menus;
use FormTools\Module as FormToolsModule;
use FormTools\Modules;
use FormTools\Settings;
use FormTools\Sessions;


class Module extends FormToolsModule
{
    protected $moduleName = "Report Builder";
    protected $moduleDesc = "This module provides an alternative way to view the data stored in your forms. It creates pages that list all available Views for the administrator and clients, with all available export options - just one click away.";
    protected $author = "Ben Keen";
    protected $authorEmail = "ben.keen@gmail.com";
    protected $authorLink = "https://formtools.org";
    protected $version = "2.0.3";
    protected $date = "2018-03-15";
    protected $originLanguage = "en_us";

    protected $nav = array(
        "module_name" => array("index.php", false),
        "word_help"   => array("help.php", true)
    );

    public function install($module_id)
    {
        $this->resetHooks();

        $settings = array(
            "show_reports_icon_on_submission_listing_page" => "yes",
            "icon_behaviour" => "dialog",
            "expand_by_default" => "yes"
        );
        Settings::set($settings, "report_builder");

        return array(true, "");
    }


    public function uninstall($module_id)
    {
        $db = Core::$db;

        $db->query("
            DELETE FROM {PREFIX}menu_items
            WHERE (page_identifier = 'rb_all_reports') OR
                  (page_identifier LIKE 'rb_form_%')
        ");
        $db->execute();

        // ensure the menu is re-cached
        Menus::cacheAccountMenu(Core::$user->getAccountId());

        return array(true, "");
    }


    public function upgrade($module_id, $old_module_version)
    {
        $this->resetHooks();
    }


    public function resetHooks()
    {
        $this->clearHooks();

        Hooks::registerHook("code", "report_builder", "start", "FormTools\\Pages::constructPageUrl", "constructPageUrl", 50, true);
        Hooks::registerHook("code", "report_builder", "middle", "FormTools\\Menus::getAdminMenuPagesDropdown", "addReportBuilderMenuItems", 50, true);
        Hooks::registerHook("code", "report_builder", "middle", "FormTools\\Menus::getClientMenuPagesDropdown", "addReportBuilderMenuItems", 50, true);
        Hooks::registerHook("template", "report_builder", "head_bottom", "", "includeInHead");
        Hooks::registerHook("code", "report_builder", "main", "FormTools\\Submissions::displaySubmissionListingQuicklinks", "addQuicklink", 50, true);
    }


    public function constructPageUrl($params)
    {
        $page_identifier = $params["page_identifier"];

        $url = "";
        if ($page_identifier == "rb_all_reports") {
            $url = "/modules/report_builder/reports.php";
        } else {
            if (preg_match("/rb_form_(\d+)/", $page_identifier, $matches)) {
                $form_id = $matches[1];
                $url = "/modules/report_builder/reports.php?form_id=$form_id";
            }
        }

        return array("url" => $url);
    }


    /**
     * This embeds the necessary styles on the Reports pages.
     *
     * @param string $location
     * @param array $params
     */
    public static function includeInHead($location, $params)
    {
        $LANG = Core::$L;
        $root_url = Core::getRootUrl();

        if ($params["page"] == "report_builder_reports_page") {
            echo <<< END
    <link type="text/css" rel="stylesheet" href="$root_url/modules/report_builder/css/reports.css">
    <script src="$root_url/modules/report_builder/scripts/reports.js"></script>
END;
        }

        if ($params["page"] == "admin_forms" || $params["page"] == "client_forms") {
            $module = Modules::getModuleInstance("report_builder");
            $L = $module->getLangStrings();

            echo <<< END
    <link type="text/css" rel="stylesheet" href="$root_url/modules/report_builder/css/reports.css">
    <script src="$root_url/modules/report_builder/scripts/reports.js"></script>
    <script>
    g.reports_dialog = $("<div id=\"rb_reports_dialog\"><div style=\"text-align: center; padding: 30px\"><img src=\"{$root_url}/global/images/loading.gif\" /></div></div>");
    g.preload_loading_icon = new Image(32, 32);
    g.preload_loading_icon.src = "{$root_url}/global/images/loading.gif";
    
    g.show_reports_dialog = function() {
      ft.create_dialog({
        title: "{$L["word_reports"]}",
        dialog: g.reports_dialog,
        min_width:  700,
        max_height: 400,
        min_height: 400,
        open: function() {
          $.ajax({
            url:  g.root_url + "/modules/report_builder/code/actions.php",
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


    public function addQuicklink()
    {
        $root_url = Core::getRootUrl();
        $module = Modules::getModuleInstance("report_builder");
        $L = $module->getLangStrings();

        $form_id = Sessions::get("curr_form_id");

        $settings = array("show_reports_icon_on_submission_listing_page", "icon_behaviour");
        $results = Settings::get($settings, "report_builder");
        $show_reports_icon_on_submission_listing_page = $results["show_reports_icon_on_submission_listing_page"];
        if ($show_reports_icon_on_submission_listing_page != "yes") {
            return "";
        }

        $icon_behaviour = $results["icon_behaviour"];

        $quicklinks = array(
            "icon_url" => "{$root_url}/modules/report_builder/images/icon_report_builder16x16.png",
            "title_text" => $L["phrase_view_reports"]
        );

        if ($icon_behaviour == "dialog") {
            $quicklinks["href"] = "#";
            $quicklinks["onclick"] = "return g.show_reports_dialog()";
        } else {
            $quicklinks["href"] = "$root_url/modules/report_builder/reports.php?form_id=$form_id";
        }

        return array("quicklinks" => $quicklinks);
    }


    public function addReportBuilderMenuItems($params)
    {
        $module = Modules::getModuleInstance("report_builder");
        return General::addReportBuilderMenuItems($params, $module->getLangStrings());
    }

    public function getReports($form_id)
    {
        return General::getReports($form_id);
    }
}



