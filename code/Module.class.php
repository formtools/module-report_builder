<?php


namespace FormTools\modules\ReportBuilder;

use FormTools\Core;
use FormTools\Hooks;
use FormTools\Module as FormToolsModule;
use FormTools\Settings;


class Module extends FormToolsModule
{
    protected $moduleName = "Report Builder";
    protected $moduleDesc = "This module provides an alternative way to view the data stored in your forms. It creates pages that list all available Views for the administrator and clients, with all available export options - just one click away.";
    protected $author = "Ben Keen";
    protected $authorEmail = "ben.keen@gmail.com";
    protected $authorLink = "http://formtools.org";
    protected $version = "2.0.0";
    protected $date = "2017-10-06";
    protected $originLanguage = "en_us";
    protected $jsFiles = array(
//        "{FTROOT}/global/codemirror/js/codemirror.js",
//        "{MODULEROOT}/scripts/pages.js"
    );

    protected $nav = array(
        "module_name" => array("index.php", false),
        "word_help"   => array("help.php", true)
    );

    public function install($module_id)
    {
        Hooks::registerHook("code", "report_builder", "start", "ft_construct_page_url", "constructPageUrl", 50, true);
        Hooks::registerHook("code", "report_builder", "middle", "ft_get_admin_menu_pages_dropdown", "addReportBuilderMenuItems", 50, true);
        Hooks::registerHook("code", "report_builder", "middle", "ft_get_client_menu_pages_dropdown", "addReportBuilderMenuItems", 50, true);
        Hooks::registerHook("template", "report_builder", "head_bottom", "", "includeInHead");
        Hooks::registerHook("code", "report_builder", "main", "ft_display_submission_listing_quicklinks", "addQuicklink", 50, true);

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

        return array(true, "");
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
    function rb_include_in_head($location, $params)
    {
        global $g_root_url, $LANG;

        if ($params["page"] == "report_builder_reports_page") {
            echo <<< END
    <link type="text/css" rel="stylesheet" href="$g_root_url/modules/report_builder/global/css/reports.css">
    <script src="$g_root_url/modules/report_builder/global/scripts/reports.js?v=2"></script>
END;
        }

        if ($params["page"] == "admin_forms" || $params["page"] == "client_forms") {
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


    public function addQuicklink($params)
    {
        $root_url = Core::getRootUrl();
        $smarty = Core::$smarty;

        // $g_smarty;

        print_r($smarty->getTemplateVars());
        return "";

        $form_id = $smarty->_tpl_vars["SESSION"]["curr_form_id"];

        $settings = array("show_reports_icon_on_submission_listing_page", "icon_behaviour");
        $results = Settings::get($settings, "report_builder");
        $show_reports_icon_on_submission_listing_page = $results["show_reports_icon_on_submission_listing_page"];
        if ($show_reports_icon_on_submission_listing_page != "yes") {
            return "";
        }

        $icon_behaviour = $results["icon_behaviour"];

        $L = ft_get_module_lang_file_contents("report_builder");

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


}



