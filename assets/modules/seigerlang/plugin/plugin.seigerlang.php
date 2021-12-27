<?php
/**
 * Plugin for Seiger Lang Management Module for Evolution CMS admin panel.
 */

use EvolutionCMS\Facades\UrlProcessor;
use EvolutionCMS\Models\SiteContent;

$e = evolutionCMS()->event;

/**
 * Заполнение полей при открытии ресурса в админке
 */
if ($e->name == 'OnDocFormPrerender') {
    global $content;
    $sLang  = new sLang();
    $content = $sLang->prepareFields($content);
}

/**
 * Модификация полей перед сохранением ресурса
 */
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

    foreach ($sLang->langConfig() as $langConfig) {
        if (request()->has($langConfig)) {
            unset($_REQUEST[$langConfig]);

            $sLang->setLangContent($e->params['id'], $langConfig, request($langConfig));
        }
    }
}

if ($e->name == 'OnDocFormSave') {
    if (!empty($e->params['id'])) {
        $sLang  = new sLang();
        $sLangDefault = $sLang->langDefault();
        $data = [];

        if (request()->has($sLangDefault)) {
            $data = request($sLangDefault);
        }

        if (request()->has('alias') && !trim(request('alias')) && request()->has('en')) {
            $request = request('en');
            $alias = strtolower(evolutionCMS()->stripAlias(trim($request['pagetitle'])));
            if (SiteContent::withTrashed()
                    ->where('id', '<>', $id)
                    ->where('alias', $alias)->count() > 0) {
                $cnt = 1;
                $tempAlias = $alias;
                while (SiteContent::withTrashed()
                        ->where('id', '<>', $id)
                        ->where('alias', $tempAlias)->count() > 0) {
                    $tempAlias = $alias;
                    $tempAlias .= $cnt;
                    $cnt++;
                }
                $alias = $tempAlias;
            }
            $data['alias'] = $alias;
        }

        if (!empty($data)) {
            evolutionCMS()->db->update($data, evolutionCMS()->getDatabase()->getFullTableName('site_content'), 'id=' . $e->params['id']);
        }
    }
}

/**
 * Подмена стандартных полей на мультиязычные фронтенд
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
