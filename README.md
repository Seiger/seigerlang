# sLang
Seiger Lang Management Module for Evolution CMS admin panel. Based on **templatesEdit3** plugin.

## Фишки ##
 * Автоматический перевод фраз через Google
 * Автоматический поиск переводов в шаблонах

## Использование в шаблонах ##
Текущий язык:
```php
    [(lang)]
```

Перевод фраз:
```php
    @lang('phrase')
```
## Настройка ##
Этот модуль использует плагин **templatesEdit3** для отображения мультиязычных полей контента в админке сайта.

Если после настройки модуля, на вкладке редактировния ресурса не отобразились поля мультиязычности, тогда необходимо проверить файл *MODX_BASE_PATH.'assets/plugins/templatesedit/configs/custom_fields.php'*
```php
<?php global $_lang, $modx;
return [
	'pagetitle_en' => [
		'title' => $_lang['resource_title'].' (EN)',
		'help' => $_lang['resource_title_help'],
		'default' => '',
		'save' => true,
	],
	'longtitle_en' => [
		'title' => $_lang['long_title'].' (EN)',
		'help' => $_lang['resource_long_title_help'],
		'default' => '',
		'save' => true,
	],
	'description_en' => [
		'title' => $_lang['resource_description'].' (EN)',
		'help' => $_lang['resource_description_help'],
		'default' => '',
		'save' => true,
	],
	'introtext_en' => [
		'title' => $_lang['resource_summary'].' (EN)',
		'help' => $_lang['resource_summary_help'],
		'default' => '',
		'save' => true,
	],
	'content_en' => [
		'title' => $_lang['resource_content'].' (EN)',
		'default' => '',
		'save' => true,
	],
	'menutitle_en' => [
		'title' => $_lang['resource_opt_menu_title'].' (EN)',
		'help' => $_lang['resource_opt_menu_title_help'],
		'default' => '',
		'save' => true,
	],
	'pagetitle_ru' => [
		'title' => $_lang['resource_title'].' (RU)',
		'help' => $_lang['resource_title_help'],
		'default' => '',
		'save' => true,
	],
	'longtitle_ru' => [
		'title' => $_lang['long_title'].' (RU)',
		'help' => $_lang['resource_long_title_help'],
		'default' => '',
		'save' => true,
	],
	'description_ru' => [
		'title' => $_lang['resource_description'].' (RU)',
		'help' => $_lang['resource_description_help'],
		'default' => '',
		'save' => true,
	],
	'introtext_ru' => [
		'title' => $_lang['resource_summary'].' (RU)',
		'help' => $_lang['resource_summary_help'],
		'default' => '',
		'save' => true,
	],
	'content_ru' => [
		'title' => $_lang['resource_content'].' (RU)',
		'default' => '',
		'save' => true,
	],
	'menutitle_ru' => [
		'title' => $_lang['resource_opt_menu_title'].' (RU)',
		'help' => $_lang['resource_opt_menu_title_help'],
		'default' => '',
		'save' => true,
	],
	'pagetitle_ua' => [
		'title' => $_lang['resource_title'].' (UA)',
		'help' => $_lang['resource_title_help'],
		'default' => '',
		'save' => true,
	],
	'longtitle_ua' => [
		'title' => $_lang['long_title'].' (UA)',
		'help' => $_lang['resource_long_title_help'],
		'default' => '',
		'save' => true,
	],
	'description_ua' => [
		'title' => $_lang['resource_description'].' (UA)',
		'help' => $_lang['resource_description_help'],
		'default' => '',
		'save' => true,
	],
	'introtext_ua' => [
		'title' => $_lang['resource_summary'].' (UA)',
		'help' => $_lang['resource_summary_help'],
		'default' => '',
		'save' => true,
	],
	'content_ua' => [
		'title' => $_lang['resource_content'].' (UA)',
		'default' => '',
		'save' => true,
	],
	'menutitle_ua' => [
		'title' => $_lang['resource_opt_menu_title'].' (UA)',
		'help' => $_lang['resource_opt_menu_title_help'],
		'default' => '',
		'save' => true,
	],
];
```

Чтобы включить у поля контент текстовый редактор, необходимо выбрать ***Type: Rich Text*** для поля при настройке полей шаблона в templatesEdit3.