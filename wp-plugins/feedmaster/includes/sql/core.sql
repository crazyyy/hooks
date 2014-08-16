-- phpMyAdmin SQL Dump
-- version 3.3.7deb5build0.10.10.1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Июн 11 2011 г., 23:03
-- Версия сервера: 5.1.49
-- Версия PHP: 5.3.3-1ubuntu9.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `feedMaster`
--

-- --------------------------------------------------------
--
-- Структура таблицы `fm_blogs`
--

CREATE TABLE IF NOT EXISTS `fm_blogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `blog` varchar(25) NOT NULL,
  `name` varchar(255) NOT NULL,
  `active` enum('1','0') NOT NULL DEFAULT '1',
  `day` int(11) DEFAULT '10',
  `run` int(11) DEFAULT '1',
  `countPost` smallint(6) NOT NULL,
  `date_start` int(11) NOT NULL DEFAULT '0',
  `date_finish` int(11) NOT NULL DEFAULT '0',
  `time_min` int(11) DEFAULT '20',
  `time_max` int(11) DEFAULT '60',
  `begin` datetime DEFAULT NULL,
  `last` datetime DEFAULT NULL,
  `tic` varchar(50) DEFAULT '0',
  `pr` varchar(50) DEFAULT '0',
  `yandex` varchar(50) DEFAULT '0',
  `google` varchar(50) DEFAULT '0',
  `upd` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fm_blogs_params`
--

CREATE TABLE IF NOT EXISTS `fm_blogs_params` (
  `blog` int(11) NOT NULL DEFAULT '0',
  `key` varchar(64) DEFAULT NULL,
  `value` text,
  UNIQUE KEY `item` (`blog`,`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `fm_blogs_replace`
--

CREATE TABLE IF NOT EXISTS `fm_blogs_replace` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `blog` int(11) DEFAULT NULL,
  `from` text,
  `to` text,
  `limit` tinyint(4) DEFAULT '0',
  `active` enum('1','0') DEFAULT '1',
  `title` enum('0','1') NOT NULL DEFAULT '1',
  `content` enum('0','1') NOT NULL DEFAULT '1',
  `tags` enum('0','1') NOT NULL DEFAULT '1',
  `cats` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `fm_blogs_sort`
--

CREATE TABLE IF NOT EXISTS `fm_blogs_sort` (
  `id` int(11) NOT NULL auto_increment,
  `blog` int(11) DEFAULT NULL,
  `key` varchar(255) DEFAULT NULL,
  `category` int(11) DEFAULT NULL,
  `active` enum('1','0') DEFAULT '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fm_blogs_stat`
--

CREATE TABLE IF NOT EXISTS `fm_blogs_stat` (
  `blog` int(11) NOT NULL,
  `key` varchar(32) NOT NULL,
  `value` varchar(64) NOT NULL,
  `last` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `fm_blogs_syn`
--

CREATE TABLE IF NOT EXISTS `fm_blogs_syn` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `base` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `word` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `syn` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `indx_word` (`word`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------
--
-- Структура таблицы `fm_items`
--

CREATE TABLE IF NOT EXISTS `fm_items` (
  `id` int(11) NOT NULL auto_increment,
  `plugin` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `active` enum('1','0') DEFAULT '0',
  `exist` enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fm_items_exist`
--

CREATE TABLE IF NOT EXISTS `fm_items_exist` (
  `id` bigint(11) NOT NULL auto_increment,
  `item` int(11) NOT NULL,
  `blog` int(11) NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Структура таблицы `fm_items_params`
--

CREATE TABLE IF NOT EXISTS `fm_items_params` (
  `item` int(11) NOT NULL DEFAULT '0',
  `key` varchar(64) DEFAULT NULL,
  `value` text,
  UNIQUE KEY `item` (`item`,`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `fm_items_url`
--

CREATE TABLE IF NOT EXISTS `fm_items_url` (
  `url` varchar(255) NOT NULL,
  `blog` tinyint(4) NOT NULL,
  `item` tinyint(4) NOT NULL,
  `level` tinyint(4) NOT NULL,
  `load` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `fm_log`
--

CREATE TABLE IF NOT EXISTS `fm_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `blogId` int(11) NOT NULL,
  `status` varchar(255) DEFAULT NULL,
  `error` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `fm_options`
--

CREATE TABLE IF NOT EXISTS `fm_options` (
  `key` varchar(50) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY  (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `fm_planner`
--

CREATE TABLE IF NOT EXISTS `fm_planner` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `blog` int(10) unsigned NOT NULL,
  `day` tinyint(4) NOT NULL,
  `start` smallint(6) NOT NULL,
  `finish` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `fm_plugins`
--

CREATE TABLE IF NOT EXISTS `fm_plugins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `icon` varchar(20) NOT NULL,
  `class` varchar(50) NOT NULL,
  `active` enum('1','0') DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `fm_plugin_feeds_posts`
--

CREATE TABLE IF NOT EXISTS `fm_plugin_feeds_posts` (
  `id` bigint(20) NOT NULL,
  `created` date NOT NULL,
  `title` varchar(512) NOT NULL,
  `link` varchar(512) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `fm_proxy`
--

CREATE TABLE IF NOT EXISTS `fm_proxy` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(16) NOT NULL,
  `port` int(10) NOT NULL,
  `user` varchar(64) NOT NULL,
  `pass` varchar(64) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `countGood` smallint(6) NOT NULL,
  `countBad` smallint(6) NOT NULL,
  `lastGood` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastBad` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `proxy` (`ip`,`port`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `fm_rss`
--

CREATE TABLE IF NOT EXISTS `fm_rss` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `blogId` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `title` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `pubDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `desc` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `fm_templates`
--

CREATE TABLE IF NOT EXISTS `fm_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `title` text NOT NULL,
  `content` text NOT NULL,
  `category` text NOT NULL,
  `tags` text NOT NULL,
  `data` text NOT NULL,
  `active` enum('1','0') DEFAULT '1',
  `sort` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `fm_users`
--

CREATE TABLE IF NOT EXISTS `fm_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


-- --------------------------------------------------------
-- --------------------------------------------------------

--
-- Дамп данных таблицы `fm_options`
--

INSERT INTO `fm_options` (`key`, `value`) VALUES
('version', '2.0.0'),
('useProxy', '0'),
('delIfCountProxy', '5'),
('useRandomProxy', '1'),
('timeZone', '2'),
('replaseNfeeds', '1'),
('protectRun', '1'),
('restartRun', '30'),
('isDebug', '0'),
('clearLog', '1'),
('isFixTree', '1'),
('baseUrl',	'http://');

-- --------------------------------------------------------

--
-- Дамп данных таблицы `fm_plugins`
--

INSERT INTO `fm_plugins` (`id`, `name`, `description`, `icon`, `class`, `active`) VALUES
(1, 'Новостные каналы (new)', 'Новые Ленты новостей (rss, atom), статические страницы', 'icon.gif', 'nfeeds', '1'),
(2, 'Изображения с flickr.com', 'Поиск изображений по ключу на flickr.com, возвращение в контент найденные изображения', 'icon.gif', 'flickr', '1'),
(3, 'Видео с youtube.com', 'Поиск видео на сайте youtube.com по ключевым словам, возращает код видео', 'icon.gif', 'youtube', '1'),
(4, 'Изображения с google images', 'Поиск изображений по ключу на google.com, возвращение в контент найденные изображения', 'icon.gif', 'googleimages', '1'),
(5, 'Пользовательский плагин', 'Иморт данных и пользовательского плагина. Передача данных в json', 'icon.gif', 'userplugin', '1'),
(6, 'Вставка keyword из файла', 'Вставка keyword из файла', 'icon.gif', 'keywords', '1');

-- --------------------------------------------------------

--
-- Дамп данных таблицы `fm_users`
--

INSERT INTO `fm_users` (`id`, `login`, `password`) VALUES
(1, 'admin', 'eb0a191797624dd3a48fa681d3061212');

