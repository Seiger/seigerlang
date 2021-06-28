<?php
/**
 *	Модуль управления языками и доменами
 */
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') die("No access");

$res    = [];
$get    = $_REQUEST['get'] ?: "translates";
$url    = "index.php?a=112&id=".$_REQUEST['id']."";

$table_system_settings  = $modx->getDatabase()->getFullTableName('system_settings');
$table_site_content     = $modx->getDatabase()->getFullTableName('site_content');
$table_a_lang           = $modx->getDatabase()->getFullTableName('s_lang');

dd($table_system_settings);