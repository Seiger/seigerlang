<?php
/**
 * Class sLang - Seiger Lang Management Module for Evolution CMS admin panel.
 */

use Illuminate\Support\Facades\View;

if (!class_exists('sLang')) {
    class sLang
    {
        protected $doc;
        protected $evo;
        protected $params;
        protected $basePath = MODX_BASE_PATH . 'assets/modules/seigerlang/';

        public function __construct($doc = [])
        {
            $this->doc = $doc;
            $this->evo = evolutionCMS();
            $this->params = $this->evo->event->params;
        }

        public function view($tpl, $data = [])
        {
            global $_lang;
            if (is_file($this->basePath.'lang/'.$this->evo->config['manager_language'].'.php')) {
                require_once $this->basePath.'lang/'.$this->evo->config['manager_language'].'.php';
            }

            $data = array_merge($data, ['modx' => $this->evo, 'data' => $data, '_lang' => $_lang]);

            View::getFinder()->setPaths([
                MODX_BASE_PATH.'assets/modules/seigerlang/views',
                MODX_MANAGER_PATH.'views'
            ]);
            echo View::make($tpl, $data);
        }
    }
}