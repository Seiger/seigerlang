--
-- Структура таблицы `{PREFIX}s_lang`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}s_lang` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `key` varchar(128) COMMENT 'Translate Key',
    PRIMARY KEY (`id`),
    KEY `idx_key` (`key`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Таблица настроек `{PREFIX}system_settings`
--

REPLACE INTO `{PREFIX}system_settings` (`setting_name`, `setting_value`) VALUES ('s_lang_enable', '0')

-- --------------------------------------------------------
