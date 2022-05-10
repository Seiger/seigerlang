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

## Use in templates
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

Multilingual link:
```php
    [~~[(catalog_root)]~~]
```

[See documentation here](https://seiger.github.io/seigerlang/)