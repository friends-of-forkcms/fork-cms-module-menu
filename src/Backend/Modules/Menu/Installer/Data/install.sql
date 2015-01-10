CREATE TABLE IF NOT EXISTS `menu_alacarte` (
 `id` int(11) NOT NULL auto_increment,
 `meta_id` int(11) NOT NULL,
 `category_id` int(11) NOT NULL,
 `language` varchar(5) NOT NULL,
 `title` varchar(255) NOT NULL,
 `description` text,
 `price` decimal(10,2),
 `highlight` enum('N','Y') NOT NULL default 'N',
 `hidden` enum('N','Y') NOT NULL default 'N',
 `created_on` datetime NOT NULL,
 `edited_on` datetime NOT NULL,
 `sequence` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `menu` (
 `id` int(11) NOT NULL auto_increment,
 `meta_id` int(11) NOT NULL,
 `extra_id` int(11) NOT NULL,
 `language` varchar(5) NOT NULL,
 `title` varchar(255) NOT NULL,
 `description` text,
 `price` text,
 `hidden` enum('N','Y') NOT NULL default 'N',
 `created_on` datetime NOT NULL,
 `edited_on` datetime NOT NULL,
 `sequence` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `menu_categories` (
 `id` int(11) NOT NULL auto_increment,
 `meta_id` int(11) NOT NULL,
 `language` varchar(5) NOT NULL,
 `title` varchar(255) NOT NULL,
 `sequence` int(11) NOT NULL,
 `created_on` datetime NOT NULL,
 `edited_on` datetime NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;