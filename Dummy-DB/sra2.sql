-- phpMyAdmin SQL Dump
-- version 4.0.4.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 05. Okt 2016 um 14:14
-- Server Version: 5.6.13
-- PHP-Version: 5.4.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `sra`
--
CREATE DATABASE IF NOT EXISTS `sra` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `sra`;

-- --------------------------------------------------------

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `nav`
--

CREATE TABLE IF NOT EXISTS `nav` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL,
  `label` tinytext COLLATE utf8_bin NOT NULL,
  `route` tinytext COLLATE utf8_bin,
  `resource` tinytext COLLATE utf8_bin,
  `privilege` tinytext COLLATE utf8_bin,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=8 ;

--
-- Daten für Tabelle `nav`
--

INSERT INTO `nav` (`id`, `menu_id`, `label`, `route`, `resource`, `privilege`, `lft`, `rgt`) VALUES
(1, 0, 'Users and Rights', 'user', 'Auth\\Controller\\User', 'index', 3, 10),
(2, 0, 'Resources', 'resource', 'Auth\\Controller\\Resource', 'index', 8, 9),
(3, 0, 'Permissions', 'permission', 'Auth\\Controller\\Permission', 'index', 6, 7),
(4, 0, 'Users', 'user', 'Auth\\Controller\\User', 'index', 4, 5),
(5, 0, 'Gallery', 'album', 'Album\\Controller\\Album', 'index', 11, 12),
(7, 0, 'Home', 'home', 'Application\\Controller\\Index', 'index', 1, 2);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` tinytext NOT NULL,
  `content` mediumtext NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Daten für Tabelle `pages`
--

INSERT INTO `pages` (`id`, `title`, `content`, `updated`) VALUES
(1, 'Home', 'Home page', '2016-09-23 16:45:48'),
(2, 'Page 2', 'lorum ipsum....', '2016-09-23 16:45:48'),
(3, 'Wander Tag', 'sers', '2016-09-24 00:13:39'),
(4, 'Langweilig', 'pups', '2016-09-24 00:13:54');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `permission`
--

CREATE TABLE IF NOT EXISTS `permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `permission_name` varchar(45) NOT NULL,
  `resource_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=69 ;

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
(67, 'index', 2);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `resource`
--

CREATE TABLE IF NOT EXISTS `resource` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `resource_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

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
(17, 'Album\\Controller\\Album');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Daten für Tabelle `role`
--

INSERT INTO `role` (`rid`, `role_name`, `role_parent`, `status`) VALUES
(1, 'Guest', NULL, 'Active'),
(2, 'Role2', 1, 'Active'),
(3, 'Role3', 2, 'Active'),
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=171 ;

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
(170, 2, 7);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `password` varchar(100) NOT NULL,
  `status` enum('Y','N') NOT NULL DEFAULT 'Y',
  `created_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `email`, `user_name`, `password`, `status`, `created_on`, `modified_on`) VALUES
(1, 'salt@salt.de', 'salt', 'e4be948b534bc81b68a28e737b9f049eb2e665f6', 'Y', '0000-00-00 00:00:00', '2016-09-27 17:12:35'),
(2, 'example.2@example.com', 'test', 'e4be948b534bc81b68a28e737b9f049eb2e665f6', 'Y', '0000-00-00 00:00:00', '2016-09-29 00:46:21'),
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
(2, 2, 2),
(3, 3, 3);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
