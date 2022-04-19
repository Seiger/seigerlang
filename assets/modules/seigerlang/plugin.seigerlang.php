<?php
/**
 * Plugin for Seiger Lang Management Module for Evolution CMS admin panel.
 */

use EvolutionCMS\Facades\UrlProcessor;
use EvolutionCMS\Models\SiteContent;

$e = evo()->event;

/**
 * Filling in the fields when opening a resource in the admin panel
 */
if ($e->name == 'OnDocFormPrerender') {
    global $content;
    $sLang  = new sLang();
    $content = $sLang->prepareFields($content);
}

/**
 * Modifying fields before saving a resource
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
        $fields = [];
        foreach (request()->all() as $key => $value) {
            if (str_starts_with($key, $langConfig.'_')) {
                $keyName = str_replace($langConfig.'_', '', $key);
                $fields[$keyName] = $value;
                unset($_REQUEST[$key]);
            }
        }

        if (count($fields)) {
            $sLang->setLangContent($e->params['id'], $langConfig, $fields);
        }
    }
}

/**
 * Alias generation
 */
if ($e->name == 'OnDocFormSave') {
    if (isset($e->params['id']) && !empty($e->params['id'])) {
        $sLang  = new sLang();
        $sLangDefault = $sLang->langDefault();
        $data = [];

        foreach (request()->all() as $key => $value) {
            if (str_starts_with($key, $sLang->langDefault().'_')) {
                $keyName = str_replace($sLang->langDefault().'_', '', $key);
                $data[$keyName] = $value;
            }
        }

        if (request()->has('alias') && !trim(request('alias')) && request()->has('en_pagetitle')) {
            $alias = strtolower(evo()->stripAlias(trim(request('en_pagetitle'))));
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
            unset($data['seotitle'], $data['seodescription']);
            evo()->db->update($data, evo()->getDatabase()->getFullTableName('site_content'), 'id=' . $e->params['id']);
        }
    }
}

/**
 * Replacing standard fields with multilingual frontend
 */
if ($e->name == 'OnAfterLoadDocumentObject') {
    $sLang  = new sLang();
    $lang = evo()->getLocale();

    $langContentField = $sLang->getLangContent($e->params['documentObject']['id'], $lang);

    if (count($langContentField)) {
        foreach ($sLang->siteContentFields as $siteContentField) {
            $e->params['documentObject'][$siteContentField] = $langContentField[$siteContentField];
        }
    }

    evo()->documentObject = $e->params['documentObject'];
}

/**
 * Parameterization of the current language
 */
if ($e->name == 'OnWebPageInit') {
    $hash = '';
    $identifier = evo()->getConfig('error_page', 1);
    $sLangDefault = evo()->getConfig('s_lang_default', 'uk');

    if (isset($_SERVER['REQUEST_URI'])) {
        $url = explode('/', ltrim($_SERVER['REQUEST_URI'], '/'), 2);
        $sLangFront = explode(',', evo()->getConfig('s_lang_front', 'uk'));

        if (trim($url[0])) {
            if ($url[0] == $sLangDefault && evo()->config['s_lang_default_show'] != 1) {
                evo()->sendRedirect(str_replace($url[0] . '/', '', $_SERVER['REQUEST_URI']));
                die;
            }

            if (in_array($url[0], $sLangFront)) {
                $sLangDefault = $url[0];
                $_SERVER['REQUEST_URI'] = str_replace($url[0] . '/', '', $_SERVER['REQUEST_URI']);
            }
        }
    }

    evo()->setLocale($sLangDefault);
    evo()->config['lang'] = $sLangDefault;

    if (evo()->config['s_lang_default'] != $sLangDefault || evo()->config['s_lang_default_show'] == 1) {
        evo()->config['base_url'] .= $sLangDefault.'/';
    }

    if (!isset($_SERVER['REQUEST_URI']) || !trim($_SERVER['REQUEST_URI']) || $_SERVER['REQUEST_URI'] == '/') {
        $identifier = evo()->getConfig('site_start', 1);
    } else {
        $q = trim($_SERVER['REQUEST_URI'], '/');
        $hash = '_'.md5(serialize($q));
        $path = explode('?', $q);
        $path = trim($path[0], '/');
        if (array_key_exists($path, UrlProcessor::getFacadeRoot()->documentListing)) {
            $identifier = UrlProcessor::getFacadeRoot()->documentListing[$path];
        }
    }

    evo()->systemCacheKey = $identifier.'_'.$sLangDefault.$hash;

    if ($identifier == evo()->getConfig('error_page', 1)) {
        evo()->invokeEvent('OnPageNotFound');
    }

    evo()->sendForward($identifier);
    exit();
}

if ($e->name == 'OnDocFormRender') {
    $sLang  = new sLang();
    evo()->regClientScript($sLang->baseUrl.'scripts/wisywingEditor.js');
}
