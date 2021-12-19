<?php
/**
 * Class SeigerLang - Seiger Lang Management Module for Evolution CMS admin panel.
 */

use EvolutionCMS\Models\SystemSetting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use sLang\Models\sLangTranslate;

if (!class_exists('sLang')) {
    class sLang
    {
        public $evo;
        public $siteContentFields = ['pagetitle', 'longtitle', 'description', 'introtext', 'content', 'menutitle'];
        public $url;
        protected $doc;
        protected $params;
        protected $basePath = MODX_BASE_PATH . 'assets/modules/seigerlang/';
        protected $tblSsystemSettings = 'system_settings';
        protected $tblSiteContent = 'site_content';
        protected $tblLang = 's_lang_translates';

        public function __construct($doc = [])
        {
            $this->doc = $doc;
            $this->evo = evolutionCMS();
            $this->params = $this->evo->event->params;
            $this->url = sLangTranslate::moduleUrl();

            $this->tblSsystemSettings = $this->evo->getDatabase()->getFullTableName($this->tblSsystemSettings);
            $this->tblSiteContent = $this->evo->getDatabase()->getFullTableName($this->tblSiteContent);
            $this->tblLang = $this->evo->getDatabase()->getFullTableName($this->tblLang);

            Paginator::defaultView('pagination');
        }

        /**
         * Список языков
         *
         * @return array
         */
        public function langList():array
        {
            $langList = [];
            if (is_file($this->basePath.'lang_list.php')) {
                $langList = require $this->basePath.'lang_list.php';
            }
            return $langList;
        }

        /**
         * Язык по умолчанию
         *
         * @return string
         */
        public function langDefault():string
        {
            $langDefault = $this->evo->config['manager_language'];
            if (trim($this->evo->config['s_lang_default'])) {
                $langDefault = $this->evo->getConfig("s_lang_default");
            }
            return $langDefault;
        }

        /**
         * Список языков сайта
         *
         * @return array
         */
        public function langConfig():array
        {
            $langConfig = [$this->evo->config['manager_language']];
            $sLangConfig = $this->getConfigValue('s_lang_config');
            if (trim($sLangConfig)) {
                $langConfig = explode(',', $sLangConfig);
            }
            return $langConfig;
        }

        /**
         * Список языков фронтенда
         *
         * @return array
         */
        public function langFront():array
        {
            $langFront = [$this->evo->config['manager_language']];
            $sLangFront = $this->getConfigValue('s_lang_front');
            if (trim($sLangFront)) {
                $langFront = explode(',', $sLangFront);
            }
            return $langFront;
        }

        /**
         * Список переводов БД
         *
         * @return array
         */
        public function dictionary()
        {
            $translates = sLangTranslate::orderByDesc('id')->paginate(30);
            $translates->withPath($this->url);

            return $translates;
        }

        /**
         * Установить язык по умолчанию
         *
         * @param $value string
         * @return mixed
         */
        public function setLangDefault($value)
        {
            $langs = array_keys($this->langList());
            $lang_default = $this->langDefault();
            if (trim($value) && in_array($value, $langs)) {
                $lang_default = trim($value);
            }

            return $this->updateTblSetting('s_lang_default', $lang_default);
        }

        /**
         * Установить видимость языка по умолчанию
         *
         * @param $value string
         * @return mixed
         */
        public function setLangDefaultShow($value)
        {
            $value = (int)$value;

            return $this->updateTblSetting('s_lang_default_show', $value);
        }

        /**
         * Установить список языков сайта
         *
         * @param $value array
         * @return mixed
         */
        public function setLangConfig($value)
        {
            $langList = array_keys($this->langList());
            $langConfig = $this->langConfig();

            if (is_array($value)) {
                $langConfig = array_filter($value, function ($var) use($langList) {
                    return in_array($var, $langList) ? true : false;
                });
            }

            $langConfig = implode(',', $langConfig);

            return $this->updateTblSetting('s_lang_config', $langConfig);
        }

        /**
         * Установить список языков для фронтенд
         *
         * @param $value array
         * @return mixed
         */
        public function setLangFront($value)
        {
            $langConfig = $this->langConfig();
            $langFront = $this->langFront();

            if (is_array($value)) {
                $langFront = array_filter($value, function ($var) use($langConfig) {
                    return in_array($var, $langConfig) ? true : false;
                });
            }

            $langFront = implode(',', $langFront);

            return $this->updateTblSetting('s_lang_front', $langFront);
        }

        /**
         * Модификация полей таблиц
         */
        public function setModifyTables()
        {
            $langConfig = $this->langConfig();

            /**
             * Модификация контентной таблицы
             */
            $columns = [];
            $needs = [];
            $query = $this->evo->getDatabase()->query("DESCRIBE {$this->tblSiteContent}");

            if ($query) {
                $fields = $this->evo->getDatabase()->makeArray($query);

                foreach ($fields as &$field) {
                    $columns[$field['Field']] = $field;
                }

                foreach ($langConfig as &$lang) {
                    foreach ($this->siteContentFields as &$siteContentField) {
                        if (!isset($columns[$siteContentField.'_'.$lang])) {
                            $f = $columns[$siteContentField];

                            $null = 'NULL';
                            if ($f['Null'] == 'NO') {
                                $null = 'NOT NULL';
                            }

                            $default = "DEFAULT ''";
                            if ($f['Default'] === null && $f['Type'] != 'text' && $f['Type'] != 'longtext') {
                                $default = 'DEFAULT NULL';
                            }
                            $needs[] = "ADD `{$siteContentField}_{$lang}` {$f['Type']} {$null} {$default} COMMENT '{$siteContentField} for {$lang} sLang version'";
                        }
                    }
                }
            }

            if (count($needs)) {
                $need = implode(', ', $needs);
                $query = "ALTER TABLE `{$this->tblSiteContent}` {$need}";
                $this->evo->getDatabase()->query($query);
            }

            /**
             * Модификация таблицы переводов
             */
            $columns = [];
            $needs = [];
            $query = $this->evo->getDatabase()->query("DESCRIBE {$this->tblLang}");

            if ($query) {
                $fields = $this->evo->getDatabase()->makeArray($query);

                foreach ($fields as &$field) {
                    $columns[$field['Field']] = $field;
                }

                foreach ($langConfig as &$lang) {
                    if (!isset($columns[$lang])) {
                        $needs[] = "ADD `{$lang}` text COMMENT '".strtoupper($lang)." sLang version'";
                    }
                }
            }

            if (count($needs)) {
                $need = implode(', ', $needs);
                $query = "ALTER TABLE `{$this->tblLang}` {$need}";
                $this->evo->getDatabase()->query($query);
            }

            /**
             * Настройка табов админки
             */
            if (is_file(MODX_BASE_PATH.'assets/plugins/templatesedit/configs/custom_fields.example.php')
                && !is_file(MODX_BASE_PATH.'assets/plugins/templatesedit/configs/custom_fields.php')) {
                copy(MODX_BASE_PATH.'assets/plugins/templatesedit/configs/custom_fields.example.php',
                    MODX_BASE_PATH.'assets/plugins/templatesedit/configs/custom_fields.php');
            }
            if (is_file(MODX_BASE_PATH.'assets/plugins/templatesedit/configs/custom_fields.php')) {
                $custom_fields = [];
                $custom_fields = include MODX_BASE_PATH.'assets/plugins/templatesedit/configs/custom_fields.php';
                if (count($custom_fields)) {
                    foreach ($custom_fields as $key => $value) {
                        $fName = explode('_', $key);
                        array_pop($fName);
                        $fName = implode('_', $fName);

                        if (in_array($fName, $this->siteContentFields)) {
                            unset($custom_fields[$key]);
                        }
                    }
                }

                if (isset($custom_fields['createdon'])) {
                    unset($custom_fields['createdon']);
                    $custom_fields['createdon'] = [
                        'default' => '$modx->toDateFormat(time())',
                        'save' => 'true',
                        'prepareSave' => 'function ($data, $modx) {'."\r\n".
                            "\t\t\t".'if (!empty($data)) {'."\r\n".
                            "\t\t\t\t".'return $modx->toTimeStamp($data);'."\r\n".
                            "\t\t\t".'} else {'."\r\n".
                            "\t\t\t\t".'return time();'."\r\n".
                            "\t\t\t".'}'."\r\n".
                            "\t\t".'}'
                    ];
                }

                foreach ($langConfig as &$lang) {
                    $custom_fields['pagetitle_'.$lang] = [
                        'title' => '$_lang[\'resource_title\'].\' ('.strtoupper($lang).')\'',
                        'help' => '$_lang[\'resource_title_help\']',
                        'default' => "",
                        'save' => 'true'
                    ];
                    $custom_fields['longtitle_'.$lang] = [
                        'title' => '$_lang[\'long_title\'].\' ('.strtoupper($lang).')\'',
                        'help' => '$_lang[\'resource_long_title_help\']',
                        'default' => "",
                        'save' => 'true'
                    ];
                    $custom_fields['description_'.$lang] = [
                        'title' => '$_lang[\'resource_description\'].\' ('.strtoupper($lang).')\'',
                        'help' => '$_lang[\'resource_description_help\']',
                        'default' => "",
                        'save' => 'true'
                    ];
                    $custom_fields['introtext_'.$lang] = [
                        'title' => '$_lang[\'resource_summary\'].\' ('.strtoupper($lang).')\'',
                        'help' => '$_lang[\'resource_summary_help\']',
                        'default' => "",
                        'save' => 'true'
                    ];
                    $custom_fields['content_'.$lang] = [
                        'title' => '$_lang[\'resource_content\'].\' ('.strtoupper($lang).')\'',
                        'default' => "",
                        'save' => 'true'
                    ];
                    $custom_fields['menutitle_'.$lang] = [
                        'title' => '$_lang[\'resource_opt_menu_title\'].\' ('.strtoupper($lang).')\'',
                        'help' => '$_lang[\'resource_opt_menu_title_help\']',
                        'default' => "",
                        'save' => 'true'
                    ];
                }

                $f = fopen(MODX_BASE_PATH.'assets/plugins/templatesedit/configs/custom_fields.php', "w");
                fwrite($f, '<?php global $_lang, $modx; '."\r\n".'return ['."\r\n");
                foreach ($custom_fields as $key => $item) {
                    fwrite($f, "\t'".$key."' => [\r\n");
                    foreach ($item as $name => $value) {
                        if (mb_substr($value, 0, 1) != '$' && !is_numeric($value) && !is_bool($value)) {
                            $value = "'".$value."'";
                        }
                        fwrite($f, "\t\t'".$name."' => ".$value.",\r\n");
                    }
                    fwrite($f, "\t],\r\n");
                }
                fwrite($f, "];");
                fclose($f);
            }

            /**
             * Конфигурация файлов переводов
             */
            foreach ($langConfig as &$lang) {
                if (!is_file(MODX_BASE_PATH.'core/lang/'.$lang.'.json')) {
                    file_put_contents(MODX_BASE_PATH.'core/lang/'.$lang.'.json', '{}');
                }
            }

            /**
             * Очистка кеша
             */
            return $this->evo->clearCache('full');
        }

        /**
         * Парсинг переводов в шаблонах Blade
         * @return bool
         */
        public function parseBlade():bool
        {
            $list = [];
            $langDefault = $this->langDefault();
            if (is_dir(MODX_BASE_PATH.'views')) {
                $views = array_merge(glob(MODX_BASE_PATH.'views/*.blade.php'), glob(MODX_BASE_PATH.'views/*/*.blade.php'));

                if (is_array($views) && count($views)) {
                    foreach ($views as $view) {
                        $data = file_get_contents($view);
                        preg_match_all('/@lang\(\'.*\'\)/', $data, $match);

                        if (is_array($match) && is_array($match[0]) && count($match[0])) {
                            foreach ($match[0] as $item) {
                                $list[] = str_replace(["@lang('", "')"], '', $item);
                            }
                        }
                    }
                }
            }
            $list = array_unique($list);

            $sLangs = sLangTranslate::all()->pluck('key')->toArray();

            $needs = array_diff($list, $sLangs);
            if (count($needs)) {
                foreach ($needs as &$need) {
                    $sLangTranslate = new sLangTranslate();
                    $sLangTranslate->key = $need;
                    $sLangTranslate->{$langDefault} = $need;
                    $sLangTranslate->save();
                }
            }

            $this->updateLangFiles();

            return true;
        }

        /**
         * Получить автоматический перевод
         *
         * @param $source
         * @param $target
         * @return string
         */
        public function getAutomaticTranslate($source, $target):string
        {
            $result = '';
            $langDefault = $this->langDefault();
            $phrase = sLangTranslate::find($source);

            if ($phrase) {
                $text = $phrase[$langDefault];
                $result = $this->googleTranslate($text, $langDefault, $target);
            }

            if (trim($result)) {
                $phrase->{$target} = $result;
                $phrase->save();
            }

            $this->updateLangFiles();

            return $result;
        }

        /**
         * Обновить поле перевода
         *
         * @param $source
         * @param $target
         * @param $value
         * @return bool
         */
        public function updateTranslate($source, $target, $value):bool
        {
            $result = false;
            $phrase = sLangTranslate::find($source);

            if ($phrase) {
                $phrase->{$target} = $value;
                $phrase->update();

                $this->updateLangFiles();

                $result = true;
            }

            return $result;
        }

        /**
         * Рендер отображения
         *
         * @param $tpl
         * @param array $data
         * @return bool
         */
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
            return true;
        }

        /**
         * Обновить файлы переводов
         */
        protected function updateLangFiles():void
        {
            foreach ($this->langConfig() as &$lang) {
                $json = sLangTranslate::all()->pluck($lang, 'key')->toJson();

                file_put_contents(MODX_BASE_PATH.'core/lang/'.$lang.'.json', $json);
            }
        }

        /**
         * Получение переводов от Google
         *
         * @param $text
         * @param string $source
         * @param string $target
         * @return string
         */
        protected function googleTranslate($text, $source = 'ru', $target = 'uk')
        {
            if ($source == $target) {
                return $text;
            }

            $out = '';

            // Google translate URL
            $url = 'https://translate.google.com/translate_a/single?client=at&dt=t&dt=ld&dt=qca&dt=rm&dt=bd&dj=1&hl=uk-RU&ie=UTF-8&oe=UTF-8&inputm=2&otf=2&iid=1dd3b944-fa62-4b55-b330-74909a99969e';
            $fields_string = 'sl=' . urlencode($source) . '&tl=' . urlencode($target) . '&q=' . urlencode($text) ;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 3);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
            curl_setopt($ch, CURLOPT_USERAGENT, 'AndroidTranslate/5.3.0.RC02.130475354-53000263 5.1 phone TRANSLATE_OPM5_TEST_1');

            $result = curl_exec($ch);
            $result = json_decode($result, TRUE);

            if (isset($result['sentences'])) {
                foreach ($result['sentences'] as $s) {
                    $out .= isset($s['trans']) ? $s['trans'] : '';
                }
            } else {
                $out = 'No result';
            }

            if(preg_match('%^\p{Lu}%u', $text) && !preg_match('%^\p{Lu}%u', $out)) { // Если оригинал с заглавной буквы то делаем и певерод с заглавной
                $out = mb_strtoupper(mb_substr($out, 0, 1)) . mb_substr($out, 1);
            }

            return $out;
        }

        /**
         * Обновить данные в таблице системных настроек
         *
         * @param $name string
         * @param $value string
         * @return mixed
         */
        protected function updateTblSetting($name, $value)
        {
            return $this->evo->getDatabase()->query("REPLACE INTO {$this->tblSsystemSettings} (`setting_name`, `setting_value`) VALUES ('{$name}', '{$value}')");
        }

        /**
         * Получить значение системной настройки в обход кеша
         *
         * @param $name string
         * @return string
         */
        protected function getConfigValue($name):string
        {
            $return = '';
            $result = SystemSetting::where('setting_name', $name)->first();

            if ($result) {
                $return = $result->setting_value;
            }

            return $return;
        }
    }
}