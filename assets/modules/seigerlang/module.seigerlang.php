<?php
/**
 *	Модуль управления языками и доменами
 */

if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') die("No access");

require_once MODX_BASE_PATH . 'assets/modules/seigerlang/sLang.class.php';
require_once MODX_BASE_PATH . 'assets/modules/seigerlang/models/sLangTranslate.php';

$sLang  = new sLang();
$evo    = evolutionCMS();
$data['get']    = isset($_REQUEST['get']) ? $_REQUEST['get'] : "translates";
$data['url']    = "index.php?a=112&id=".$_REQUEST['id']."";
$data['sLang']  = $sLang;
$tbl_system_settings  = $evo->getDatabase()->getFullTableName('system_settings');
$tbl_site_content     = $evo->getDatabase()->getFullTableName('site_content');
$tbl_a_lang           = $evo->getDatabase()->getFullTableName('s_lang');

switch ($data['get']) {
    default:
        $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
        switch ($action) {
            case "synchronize":
                // Парсинг Blade шаблонов
                $sLang->parseBlade();
                break;
            case "translate":
                $result = $sLang->getAutomaticTranslate($_POST['source'], $_POST['target']);
                die($result);
            case "update":
                $result = $sLang->updateTranslate($_POST['source'], $_POST['target'], $_POST['value']);
                die($result);
            default:
                break;
        }
        break;
    case "settings":
        if (count($_POST) > 0) {
            // Язык по умолчанию
            $sLang->setLangDefault($_POST['s_lang_default']);

            // Отображение языка по умолчанию
            $sLang->setLangDefaultShow($_POST['s_lang_default_show']);
            $sLang->evo->config['s_lang_default_show'] = $_POST['s_lang_default_show'];

            // Список языков сайта
            $sLang->setLangConfig($_POST['s_lang_config']);

            // Список языков для фронтенда
            $sLang->setLangFront($_POST['s_lang_front']);

            // Модификация таблиц
            $sLang->setModifyTables();
        }
        break;
}

$sLang->view('index', $data);