<?php namespace sLang\Models;

use EvolutionCMS\Models\SiteModule;
use Illuminate\Database\Eloquent;

class sLangTranslate extends Eloquent\Model
{
    public static function moduleUrl ()
    {
        $module = SiteModule::whereName('sLang')->first();
        return 'index.php?a=112&id='.$module->id;
    }
}
