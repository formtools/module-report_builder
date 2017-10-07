<?php

require("../../global/library.php");

use FormTools\Modules;

$module = Modules::initModulePage("admin");
$L = $module->getLangStrings();

$module->displayPage("templates/help.tpl");
