# sLang
Seiger Lang Management Module for Evolution CMS admin panel. Based on **templatesEdit3** plugin.

Этот модуль использует плагин **templatesEdit3** для отображения мультиязычных полей контента в админке сайта.

Если после настройки модуля, на вкладке редактировния ресурса не отобразились поля мультиязычности, тогда необходимо проверить файл *MODX_BASE_PATH.'assets/plugins/templatesedit/configs/custom_fields.php'*

```php
return [
    'pagetitle_ru' => [
        'title' => $_lang['resource_title'].' (RU)',
        'help' => $_lang['resource_title_help'],
        'default' => '',
        'save' => true
    ],
    'pagetitle_ua' => [
        'title' => $_lang['resource_title'].' (UA)',
        'help' => $_lang['resource_title_help'],
        'default' => '',
        'save' => true
    ],
];
```