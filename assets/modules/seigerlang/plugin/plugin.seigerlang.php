<?php
/**
 * Plugin for Seiger Lang Management Module for Evolution CMS admin panel.
 */

use EvolutionCMS\Facades\UrlProcessor;

$e = evolutionCMS()->event;

if ($e->name == 'OnDocFormPrerender') {
    global $content;
    $sLang  = new sLang();
    $content = $sLang->prepareFields($content);
}

if ($e->name == 'OnBeforeDocFormSave') {
    if (empty($e->params['id'])) {
        $id = collect(DB::select("
            SELECT AUTO_INCREMENT 
            FROM `information_schema`.`tables` 
            WHERE `table_name` = '".evo()->getDatabase()->getFullTableName('site_content')."'"))
            ->pluck('AUTO_INCREMENT')
            ->first();
        $e->params['id'] = $id;
    }

    $sLang  = new sLang();
    $sLangDefault = $sLang->langDefault();
    $data = [];

    if (request()->has($sLangDefault)) {
        foreach ($sLang->siteContentFields as $siteContentField) {
            $_REQUEST[$siteContentField] = $_REQUEST[$sLangDefault][$siteContentField];
        }
    }

    if (isset($_REQUEST['alias']) && !trim($_REQUEST['alias'])) {
        if (isset($_REQUEST['en']['pagetitle']) && trim($_REQUEST['en']['pagetitle'])) {
            $_REQUEST['alias'] = strtolower(evolutionCMS()->stripAlias(trim($_REQUEST['en']['pagetitle'])));
        } else {
            $_REQUEST['alias'] = strtolower(evolutionCMS()->stripAlias(trim($_REQUEST['pagetitle'])));
        }
    }

    foreach ($sLang->langConfig() as $langConfig) {
        if (request()->has($langConfig)) {
            unset($_REQUEST[$langConfig]);

            $sLang->setLangContent($e->params['id'], $langConfig, request($langConfig));
        }
    }
}

/**
 * Подмена стандартных полей на мультиязычные
 */
if ($e->name == 'OnAfterLoadDocumentObject') {
    $sLang  = new sLang();
    $lang = evolutionCMS()->getLocale();

    foreach ($sLang->siteContentFields as $siteContentField) {
        $e->params['documentObject'][$siteContentField] = $e->params['documentObject'][$siteContentField.'_'.$lang];
    }

    evolutionCMS()->documentObject = $e->params['documentObject'];
}

/**
 * Параметризация текущего языка
 */
if ($e->name == 'OnWebPageInit' || $e->name == 'OnPageNotFound') {
    $hash = '';
    $identifier = evolutionCMS()->config['error_page'];
    $sLangDefault = evolutionCMS()->config['s_lang_default'];

    if (isset($_SERVER['REQUEST_URI'])) {
        $url = explode('/', ltrim($_SERVER['REQUEST_URI'], '/'), 2);
        $sLangFront = explode(',', evolutionCMS()->config['s_lang_front']);

        if (trim($url[0])) {
            if ($url[0] == $sLangDefault && evolutionCMS()->config['s_lang_default_show'] != 1) {
                evolutionCMS()->sendRedirect(str_replace($url[0] . '/', '', $_SERVER['REQUEST_URI']));die;
            }

            if (in_array($url[0], $sLangFront)) {
                $sLangDefault = $url[0];
                $_SERVER['REQUEST_URI'] = str_replace($url[0] . '/', '', $_SERVER['REQUEST_URI']);
            }
        }
    }

    evolutionCMS()->setLocale($sLangDefault);
    evolutionCMS()->config['lang'] = $sLangDefault;

    if (evolutionCMS()->config['s_lang_default'] != $sLangDefault || evolutionCMS()->config['s_lang_default_show'] == 1) {
        evolutionCMS()->config['base_url'] .= $sLangDefault.'/';
    }

    if (!isset($_SERVER['REQUEST_URI']) || !trim($_SERVER['REQUEST_URI']) || $_SERVER['REQUEST_URI'] == '/') {
        $identifier = evolutionCMS()->config['site_start'];
    } else {
        $q = trim($_SERVER['REQUEST_URI'], '/');
        $hash = '_'.md5(serialize($q));
        if (array_key_exists($q, UrlProcessor::getFacadeRoot()->documentListing)) {
            $identifier = UrlProcessor::getFacadeRoot()->documentListing[$q];
        }
    }
    evolutionCMS()->systemCacheKey = $identifier.'_'.$sLangDefault.$hash;

    evolutionCMS()->sendForward($identifier);die;
}
