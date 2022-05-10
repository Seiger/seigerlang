<?php namespace sLang\Models;

use Illuminate\Database\Eloquent;

class sLangContent extends Eloquent\Model
{
    protected $table = 's_lang_content';
    protected $fillable = ['resource', 'lang', 'pagetitle', 'longtitle', 'description', 'introtext', 'content', 'menutitle', 'seotitle', 'seodescription'];

    /**
     * Get the post item with lang
     *
     * @param $query
     * @param $locale
     * @return mixed
     */
    public function scopeOriginalAndLang($query, $locale)
    {
        return $this->select('*', 'site_content.pagetitle as pagetitle_orig', 'site_content.longtitle as longtitle_orig')
            ->addSelect('site_content.description as description_orig', 'site_content.introtext as introtext_orig')
            ->addSelect('site_content.content as content_orig', 'site_content.menutitle as menutitle_orig')
            ->leftJoin('site_content', 's_lang_content.resource', '=', 'site_content.id')
            ->where('lang', '=', $locale);
    }

    /**
     * Get the menutitle attribute
     *
     * @return mixed
     */
    public function getMenutitleAttribute()
    {
        $menutitle_orig = $this->menutitle_orig ?? '';
        $pagetitle_orig = $this->pagetitle_orig ?? '';
        $menutitle = empty($this->menutitle) ? $menutitle_orig : $this->menutitle;
        $pagetitle = empty($this->pagetitle) ? $pagetitle_orig : $this->pagetitle;
        return empty($menutitle) ? $pagetitle : $menutitle;
    }
}