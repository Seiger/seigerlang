<?php
/**
 * Plugin for Seiger Lang Management Module for Evolution CMS admin panel.
 */

use EvolutionCMS\Facades\UrlProcessor;

$e = evolutionCMS()->event;

if ($e->name == 'OnDocFormSave') {
    if (!empty($e->params['id'])) {
        $sLang  = new sLang();
        $sLangDefault = $sLang->langDefault();
        $data = [];

        foreach ($sLang->siteContentFields as $siteContentField) {
            if (isset($_REQUEST[$siteContentField])) {
                $value = $_REQUEST[$siteContentField.'_'.$sLangDefault];
                if (!is_null($value) && !empty($value)) {
                    if (is_array($value)) {
                        $value = implode('||', $value);
                    }
                    $data[$siteContentField] = evolutionCMS()->db->escape($value);
                }
            }
        }

        if (isset($_REQUEST['alias']) && !trim($_REQUEST['alias'])) {
            if (isset($_REQUEST['pagetitle_en']) && trim($_REQUEST['pagetitle_en'])) {
                $data['alias'] = strtolower(evolutionCMS()->stripAlias(trim($_REQUEST['pagetitle_en'])));
            } else {
                $data['alias'] = strtolower(evolutionCMS()->stripAlias(trim($_REQUEST['pagetitle'])));
            }
        }

        if (!empty($data)) {
            evolutionCMS()->db->update($data, '[+prefix+]site_content', 'id=' . $e->params['id']);
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
