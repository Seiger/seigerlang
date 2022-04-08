# sLang
Seiger Lang multi language Management Module for Evolution CMS admin panel.

## Features ##
 * Based on **templatesEdit3** plugin.
 * Automatic translation of phrases through Google
 * Automatic search for translations in templates

## Use in templates ##
Current language:
```php
    [(lang)]
```

Translation of phrases:
```php
    @lang('phrase')
```

List of frontend languages:
```php
    [(s_lang_front)]
```
## Setting ##
This module uses the **templatesEdit3** plugin to display multilingual content fields in the site's admin area.

If, after setting up the module, the multilingual fields are not displayed on the resource editing tab, then you need to check the file *MODX_BASE_PATH.'assets/plugins/templatesedit/configs/custom_fields.php'*
```php
<?php global $_lang, $modx; 
return [
	'en_pagetitle' => [
		'title' => $_lang['resource_title'].' (EN)',
		'help' => $_lang['resource_title_help'],
		'default' => '',
		'save' => '',
	],
	'en_longtitle' => [
		'title' => $_lang['long_title'].' (EN)',
		'help' => $_lang['resource_long_title_help'],
		'default' => '',
		'save' => '',
	],
	'en_description' => [
		'title' => $_lang['resource_description'].' (EN)',
		'help' => $_lang['resource_description_help'],
		'default' => '',
		'save' => '',
	],
	'en_introtext' => [
		'title' => $_lang['resource_summary'].' (EN)',
		'help' => $_lang['resource_summary_help'],
		'default' => '',
		'save' => '',
	],
	'en_content' => [
		'title' => $_lang['resource_content'].' (EN)',
		'default' => '',
		'save' => '',
	],
	'en_menutitle' => [
		'title' => $_lang['resource_opt_menu_title'].' (EN)',
		'help' => $_lang['resource_opt_menu_title_help'],
		'default' => '',
		'save' => '',
	],
	'en_seotitle' => [
		'title' => $_lang['resource_title'].' SEO (EN)',
		'default' => '',
		'save' => '',
	],
	'en_seodescription' => [
		'title' => $_lang['resource_description'].' SEO (EN)',
		'default' => '',
		'save' => '',
	],
];
```

To enable a text editor for a content field, you must select ***Type: Rich Text*** for the field when setting the template fields in templatesEdit3.