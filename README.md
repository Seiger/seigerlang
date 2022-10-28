# This is an old module, it is no longer supported. Use the new [sLang](https://github.com/Seiger/slang) module instead.

# Welcome to sLang

![slang](https://user-images.githubusercontent.com/12029039/167660172-9596574a-47ae-4304-a389-814bfa4c9e87.png)
[![GitHub version](https://img.shields.io/badge/version-v.1.1.1-blue)](https://github.com/Seiger/seigerlang/releases)
[![CMS Evolution](https://img.shields.io/badge/CMS-Evolution-brightgreen.svg)](https://github.com/evolution-cms/evolution)
![PHP version](https://img.shields.io/badge/PHP->=v7.4-red.svg?php=7.4)

Seiger Lang multi language Management Module for Evolution CMS admin panel.

The work of the module is based on the use of the standard Laravel functionality for multilingualism.

## Features
- [x] Based on **templatesEdit3** plugin.
- [x] Automatic translation of phrases through Google
- [x] Automatic search for translations in templates
- [x] Unlimited translation languages

## Requirements
Before installing the module, make sure you have the templatesEdit3 plugin installed.

## Use in controllers
For using this module on front pages your need add few includes to base controller
```php
require_once MODX_BASE_PATH . 'assets/modules/seigerlang/sLang.class.php';
```

## Use in templates
Current language:
```php
[(lang)]
```

Translation of phrases:
```php
@lang('phrase')
```

Default language:
```php
[(s_lang_default)]
```

List of frontend languages by comma:
```php
[(s_lang_front)]
```

Multilingual link:
```php
[~~[(catalog_root)]~~]
```

Localized versions of your page for Google hreflang
```php
@php($sLang = new sLang())
{!!$sLang->hrefLang()!!}
```

[See documentation here](https://seiger.github.io/seigerlang/)