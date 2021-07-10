<?php
/**
 * Plugin for Seiger Lang Management Module for Evolution CMS admin panel.
 */

use EvolutionCMS\Facades\UrlProcessor;

$e = evolutionCMS()->event;

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

    if (isset($_GET['q'])) {
        $url = explode('/', $_GET['q'], 2);
        $sLangFront = explode(',', evolutionCMS()->config['s_lang_front']);

        if (trim($url[0])) {
            if (in_array($url[0], $sLangFront)) {
                $sLangDefault = $url[0];
                $_GET['q'] = str_replace($url[0] . '/', '', $_GET['q']);
            }
        }
    }

    evolutionCMS()->setLocale($sLangDefault);
    evolutionCMS()->config['lang'] = $sLangDefault;

    if (!isset($_GET['q']) || !trim($_GET['q'])) {
        $identifier = evolutionCMS()->config['site_start'];
    } else {
        $q = trim($_GET['q'], '/');
        $hash = '_'.md5(serialize($q));
        if (array_key_exists($q, UrlProcessor::getFacadeRoot()->documentListing)) {
            $identifier = UrlProcessor::getFacadeRoot()->documentListing[$q];
        }
    }
    evolutionCMS()->systemCacheKey = $identifier.'_'.$sLangDefault.$hash;

    evolutionCMS()->sendForward($identifier);die;
}
