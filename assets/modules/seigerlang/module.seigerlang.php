<?php
/**
 *	Модуль управления языками и доменами
 */
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') die("No access");

require_once MODX_BASE_PATH . 'assets/modules/seigerlang/sLang.class.php';

$sLang  = new sLang();
$data['get']    = $_REQUEST['get'] ?: "translates";
$data['url']    = "index.php?a=112&id=".$_REQUEST['id']."";
$table_system_settings  = evolutionCMS()->getDatabase()->getFullTableName('system_settings');
$table_site_content     = evolutionCMS()->getDatabase()->getFullTableName('site_content');
$table_a_lang           = evolutionCMS()->getDatabase()->getFullTableName('s_lang');

switch ($data['get']) {
    default:
        $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
        switch ($action) {
            default:
                break;
        }
        break;
    case "settings":
        break;
}

$sLang->view('index', $data);