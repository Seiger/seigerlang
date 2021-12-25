--
-- Структура таблицы `{PREFIX}s_translates`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}s_lang_translates` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `key` varchar(128) COMMENT 'Translate Key',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_key` (`key`)
    ) ENGINE=MyISAM  DEFAULT CHARSET={TABLEENCODING} AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Таблица переводов ресурса `{PREFIX}s_lang_content`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}s_lang_content` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `resource` int(11) NOT NULL COMMENT 'Resource ID',
    `pagetitle` varchar(255) COMMENT 'Translate pagetitle',
    `longtitle` varchar(255) COMMENT 'Translate longtitle',
    `description` varchar(255) COMMENT 'Translate description',
    `introtext` text COMMENT 'Translate introtext',
    `content` longtext COMMENT 'Translate content',
    `menutitle` varchar(255) COMMENT 'Translate menutitle',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_key` (`key`)
    ) ENGINE=MyISAM  DEFAULT CHARSET={TABLEENCODING} AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Таблица настроек `{PREFIX}system_settings`
--

REPLACE INTO `{PREFIX}system_settings` (`setting_name`, `setting_value`) VALUES ('s_lang_enable', '0')
REPLACE INTO `{PREFIX}system_settings` (`setting_name`, `setting_value`) VALUES ('s_lang_default_show', '0')
REPLACE INTO `{PREFIX}system_settings` (`setting_name`, `setting_value`) VALUES ('s_lang_default', '')
REPLACE INTO `{PREFIX}system_settings` (`setting_name`, `setting_value`) VALUES ('s_lang_config', '')
REPLACE INTO `{PREFIX}system_settings` (`setting_name`, `setting_value`) VALUES ('s_lang_front', '')

-- --------------------------------------------------------
