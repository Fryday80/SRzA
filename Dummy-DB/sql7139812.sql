-- phpMyAdmin SQL Dump
-- version 3.5.5
-- http://www.phpmyadmin.net
--
-- Host: sql7.freemysqlhosting.net
-- Erstellungszeit: 16. Okt 2016 um 17:19
-- Server Version: 5.5.49-0ubuntu0.14.04.1
-- PHP-Version: 5.3.28

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `sql7139812`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `album`
--

CREATE TABLE IF NOT EXISTS `album` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `timestamp` bigint(20) DEFAULT NULL,
  `duration` int(11) NOT NULL DEFAULT '3',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `album`
--

INSERT INTO `album` (`id`, `event`, `timestamp`, `duration`) VALUES
(1, 'Testevent_1', 70457457, 5),
(2, 'Testevent_2', 2016, 3),
(3, 'Testevent_3', 456770, 3),
(4, 'Testevent_4', 40564, 3);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `nav`
--

CREATE TABLE IF NOT EXISTS `nav` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL,
  `label` tinytext COLLATE utf8_bin NOT NULL,
  `uri` tinytext COLLATE utf8_bin,
  `permission_id` int(11) NOT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=16 ;

--
-- Daten für Tabelle `nav`
--

INSERT INTO `nav` (`id`, `menu_id`, `label`, `uri`, `permission_id`, `lft`, `rgt`) VALUES
(1, 0, 'Users and Rights', '/user', 67, 12, 15),
(2, 0, 'Resources', '/resource', 62, 25, 26),
(3, 0, 'Permissions', '/permission', 58, 23, 24),
(4, 0, 'Users', '/user', 67, 13, 14),
(5, 0, 'Gallery Edit', '/album', 66, 10, 11),
(7, 0, 'Home', '/', 1, 1, 2),
(8, 0, 'Navigation', '/nav/sort', 73, 19, 20),
(9, 0, 'Administration', '/#', 69, 9, 28),
(10, 0, 'Roles', '/role', 54, 21, 22),
(11, 0, 'Content', '/cms', 69, 17, 18),
(12, 0, 'Wander Tag', 'Wander-Tag', 80, 5, 8),
(13, 0, 'Langweile', 'Langweilig', 80, 6, 7),
(14, 0, 'Gallery', '/gallery', 80, 3, 4),
(15, 0, 'Webmaster', '/#', 69, 16, 27);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` tinytext NOT NULL,
  `url` tinytext NOT NULL,
  `content` mediumtext NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `pages`
--

INSERT INTO `pages` (`id`, `title`, `url`, `content`, `updated`) VALUES
(1, 'Home', 'Home', '<p>Home page bla&Ocirc;</p>\r\n\r\n<div style="page-break-after: always"><span style="display:none">&nbsp;</span></div>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>Problemliste:</p>\r\n<br>\r\n<p>&nbsp;</p>\r\n\r\n<p>Fry: media query schaltet immernoch bei 900px einstellung ist aber 1200 ... google finde ich nix</p>\r\n<br>\r\n<p>!!!! wenn man in der Console "device width" ausprobiert geht es beim richtigen breakpoint!!!<br />\r\n=&gt; irgend ne einstellung zerschie&szlig;t die werte der Anzeige im chrome-normalbetrieb... <br>width=device width nicht, das hatte ich zum test ausgebaut</p>\r\n', '2016-09-23 16:45:48'),
(3, 'Wander Tag', 'Wander-Tag', 'sers', '2016-09-24 00:13:39'),
(4, 'Langweilig', 'Langweilig', 'warum giebts in php my admin eigentilich nix zum einfügen von dummy texten ??', '2016-09-24 00:13:54');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `permission`
--

CREATE TABLE IF NOT EXISTS `permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `permission_name` varchar(45) NOT NULL,
  `resource_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=87 ;

--
-- Daten für Tabelle `permission`
--

INSERT INTO `permission` (`id`, `permission_name`, `resource_id`) VALUES
(1, 'index', 1),
(3, 'show', 1),
(4, 'test', 1),
(5, 'add', 2),
(6, 'edit', 2),
(7, 'delete', 2),
(52, 'login', 13),
(53, 'logout', 13),
(54, 'index', 14),
(55, 'add', 14),
(56, 'edit', 14),
(57, 'delete', 14),
(58, 'index', 15),
(59, 'edit', 15),
(60, 'add', 15),
(61, 'delete', 15),
(62, 'index', 16),
(63, 'add', 16),
(64, 'edit', 16),
(65, 'delete', 16),
(66, 'index', 17),
(67, 'index', 2),
(69, 'index', 19),
(70, 'edit', 19),
(71, 'add', 19),
(73, 'sort', 19),
(74, 'success', 13),
(75, 'index', 20),
(76, 'detail', 20),
(77, 'add', 20),
(78, 'edit', 20),
(79, 'delete', 20),
(80, 'index', 21),
(81, 'index', 22),
(82, ' full', 22),
(83, ' small', 22),
(84, ' edit', 17);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `resource`
--

CREATE TABLE IF NOT EXISTS `resource` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `resource_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

--
-- Daten für Tabelle `resource`
--

INSERT INTO `resource` (`id`, `resource_name`) VALUES
(1, 'Application\\Controller\\Index'),
(2, 'Auth\\Controller\\User'),
(13, 'Auth\\Controller\\Auth'),
(14, 'Auth\\Controller\\Role'),
(15, 'Auth\\Controller\\Permission'),
(16, 'Auth\\Controller\\Resource'),
(17, 'Album\\Controller\\Album'),
(19, 'Nav\\Controller\\Nav'),
(20, 'Cms\\Controller\\Content'),
(21, 'Cms\\Controller\\Page'),
(22, 'Album\\Controller\\Gallery');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `role`
--

CREATE TABLE IF NOT EXISTS `role` (
  `rid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(45) NOT NULL,
  `role_parent` int(11) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  PRIMARY KEY (`rid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `role`
--

INSERT INTO `role` (`rid`, `role_name`, `role_parent`, `status`) VALUES
(1, 'Guest', NULL, 'Active'),
(2, 'Role2', 1, 'Active'),
(3, 'Member', 2, 'Active'),
(4, 'Administrator', NULL, 'Active');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `role_permission`
--

CREATE TABLE IF NOT EXISTS `role_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL,
  `permission_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=190 ;

--
-- Daten für Tabelle `role_permission`
--

INSERT INTO `role_permission` (`id`, `role_id`, `permission_id`) VALUES
(2, 1, 2),
(5, 2, 1),
(6, 2, 2),
(7, 2, 60),
(8, 2, 59),
(10, 1, 2),
(13, 2, 1),
(14, 2, 2),
(18, 1, 2),
(21, 2, 1),
(22, 2, 2),
(83, 1, 3),
(84, 1, 4),
(85, 1, 1),
(89, 1, 66),
(90, 4, 66),
(91, 4, 1),
(92, 4, 3),
(93, 4, 4),
(94, 4, 52),
(95, 4, 53),
(96, 4, 58),
(97, 4, 59),
(98, 4, 60),
(99, 4, 61),
(100, 4, 64),
(101, 4, 65),
(102, 4, 62),
(103, 4, 63),
(104, 4, 54),
(105, 4, 55),
(106, 4, 56),
(107, 4, 57),
(108, 4, 5),
(109, 4, 67),
(110, 4, 6),
(111, 4, 7),
(130, 3, 66),
(131, 3, 1),
(132, 3, 3),
(133, 3, 4),
(134, 3, 52),
(135, 3, 53),
(136, 3, 58),
(137, 3, 59),
(138, 3, 60),
(139, 3, 61),
(140, 3, 64),
(141, 3, 65),
(142, 3, 62),
(143, 3, 63),
(144, 3, 54),
(145, 3, 55),
(146, 3, 56),
(147, 3, 57),
(148, 3, 5),
(149, 3, 67),
(150, 3, 6),
(151, 3, 7),
(152, 2, 66),
(153, 2, 3),
(154, 2, 4),
(155, 2, 52),
(156, 2, 53),
(157, 2, 58),
(158, 2, 61),
(159, 2, 64),
(160, 2, 65),
(161, 2, 62),
(162, 2, 63),
(163, 2, 54),
(164, 2, 55),
(165, 2, 56),
(166, 2, 57),
(167, 2, 5),
(168, 2, 67),
(169, 2, 6),
(170, 2, 7),
(171, 4, 71),
(172, 4, 69),
(173, 4, 70),
(174, 4, 72),
(175, 4, 73),
(176, 4, 74),
(177, 1, 74),
(178, 4, 77),
(179, 4, 78),
(180, 4, 79),
(181, 4, 75),
(182, 4, 76),
(183, 4, 80),
(184, 4, 81),
(185, 4, 82),
(186, 4, 83),
(187, 4, 84),
(188, 4, 86),
(189, 4, 85);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(100) NOT NULL,
  `status` enum('Y','N') NOT NULL DEFAULT 'Y',
  `created_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `email`, `name`, `password`, `status`, `created_on`, `modified_on`) VALUES
(1, 'salt@salt.de', 'salt', '88f716c2b137f811a8ebe9ea10a7867d7b9e7622', 'Y', '0000-00-00 00:00:00', '2016-10-08 18:00:20'),
(2, 'fryday@example.com', 'FryDay', '8b20508657fb4d3b457198e94c02ac916c72ce02', 'Y', '0000-00-00 00:00:00', '2016-10-08 18:04:01'),
(3, 'example.3@example.com', 'example', 'd7d833534a39afbac08ec536bed7ae9eeac45638', 'Y', '0000-00-00 00:00:00', '2016-09-27 00:58:02');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_role`
--

CREATE TABLE IF NOT EXISTS `user_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Daten für Tabelle `user_role`
--

INSERT INTO `user_role` (`id`, `user_id`, `role_id`) VALUES
(1, 1, 4),
(2, 2, 4),
(3, 3, 3);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
