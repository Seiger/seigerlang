<?php namespace sLang\Models;

use Illuminate\Database\Eloquent;
use Illuminate\Support\Facades\DB;

class sLangContent extends Eloquent\Model
{
    protected $table = 's_lang_content';
    protected $fillable = ['resource', 'lang', 'pagetitle', 'longtitle', 'description', 'introtext', 'content', 'menutitle', 'seotitle', 'seodescription'];

    /**
     * Get the content item with lang and original fields
     *
     * @param $query
     * @param $locale
     * @return mixed
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $locale
     * @param  array  $tvNames
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLangAndTvs($query, $locale, $tvNames = [])
    {
        $query->select('*', 's_lang_content.resource as id', 's_lang_content.pagetitle as pagetitle');
        $query->addSelect('s_lang_content.longtitle as longtitle', 's_lang_content.description as description');
        $query->addSelect('s_lang_content.introtext as introtext', 's_lang_content.content as content');
        $query->addSelect('s_lang_content.menutitle as menutitle');

        if (count($tvNames)) {
            foreach ($tvNames as $tvName) {
                $query->addSelect(
                    DB::raw("(SELECT value FROM ".DB::getTablePrefix()."site_tmplvar_contentvalues
                        WHERE ".DB::getTablePrefix()."site_tmplvar_contentvalues.contentid = ".DB::getTablePrefix()."s_lang_content.resource
                        AND ".DB::getTablePrefix()."site_tmplvar_contentvalues.tmplvarid = (
                            SELECT ".DB::getTablePrefix()."site_tmplvars.id 
                            FROM ".DB::getTablePrefix()."site_tmplvars
                            WHERE ".DB::getTablePrefix()."site_tmplvars.name = '".$tvName."'
                        ) LIMIT 1) as ".$tvName
                    )
                );
            }
        }

        return $query->addSelect('site_content.pagetitle as pagetitle_orig', 'site_content.longtitle as longtitle_orig')
            ->addSelect('site_content.description as description_orig', 'site_content.introtext as introtext_orig')
            ->addSelect('site_content.content as content_orig', 'site_content.menutitle as menutitle_orig')
            ->leftJoin('site_content', 's_lang_content.resource', '=', 'site_content.id')
            ->where('lang', '=', $locale);
    }

    /**
     * Only active resources
     *
     * @param $query
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('published', '1')->where('deleted', '0');
    }

    /**
     * Filtering documents by TV parameter
     *
     * @param $query
     * @param $name
     * @param $value
     * @return mixed
     */
    public function scopeWhereTv($query, $name, $value)
    {
        return $query->leftJoin('site_tmplvar_contentvalues', 'site_tmplvar_contentvalues.contentid', '=', 's_lang_content.resource')
            ->leftJoin('site_tmplvars', 'site_tmplvars.id', '=', 'site_tmplvar_contentvalues.tmplvarid')
            ->whereName($name)
            ->whereValue($value);
    }
}