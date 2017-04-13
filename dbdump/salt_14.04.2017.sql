-- phpMyAdmin SQL Dump
-- version 4.0.4.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 13. Apr 2017 um 22:21
-- Server Version: 5.6.13
-- PHP-Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `db2836034`
--
CREATE DATABASE IF NOT EXISTS `db2836034` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `db2836034`;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `permission`
--

DROP TABLE IF EXISTS `permission`;
CREATE TABLE IF NOT EXISTS `permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `permission_name` varchar(45) NOT NULL,
  `resource_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=151 ;

--
-- Daten für Tabelle `permission`
--

INSERT INTO `permission` (`id`, `permission_name`, `resource_id`) VALUES
(1, 'index', 1),
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
(81, 'index', 22),
(96, 'edit', 17),
(97, 'add', 17),
(98, 'full', 22),
(99, 'small', 22),
(114, 'edit', 1),
(115, 'add', 1),
(116, 'delete', 1),
(117, 'onlyGuests', 1),
(118, 'delete', 17),
(123, 'register', 13),
(124, 'index', 25),
(126, 'add', 25),
(127, 'edit', 25),
(128, 'delete', 25),
(129, 'index', 26),
(130, 'add', 26),
(131, 'edit', 26),
(132, 'delete', 26),
(133, 'index', 27),
(134, 'add', 27),
(135, 'edit', 27),
(136, 'delete', 27),
(137, 'index', 28),
(138, 'add', 28),
(139, 'edit', 28),
(140, 'delete', 28),
(141, 'index', 29),
(142, 'index', 30),
(143, 'index', 31),
(144, 'action', 31),
(145, 'delete', 19),
(147, 'dashboard', 32),
(148, 'settings', 32),
(149, 'index', 33),
(150, 'json', 32);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `resource`
--

DROP TABLE IF EXISTS `resource`;
CREATE TABLE IF NOT EXISTS `resource` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `resource_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=34 ;

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
(22, 'Album\\Controller\\Gallery'),
(25, 'Cast\\Controller\\Manager'),
(26, 'Cast\\Controller\\Family'),
(27, 'Cast\\Controller\\Job'),
(28, 'Cast\\Controller\\Character'),
(29, 'Cast\\Controller\\Presentation'),
(30, 'Cast\\Controller\\Cast'),
(31, 'Media\\Controller\\FileBrowser'),
(32, 'Application\\Controller\\System'),
(33, 'Cms\\Controller\\Page');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `role_permission`
--

DROP TABLE IF EXISTS `role_permission`;
CREATE TABLE IF NOT EXISTS `role_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL,
  `permission_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=322 ;

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
(92, 4, 3),
(93, 4, 4),
(96, 4, 58),
(97, 4, 59),
(98, 4, 60),
(99, 4, 61),
(132, 3, 3),
(133, 3, 4),
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
(174, 4, 72),
(177, 1, 74),
(185, 4, 82),
(186, 4, 83),
(187, 4, 84),
(188, 4, 86),
(189, 4, 85),
(190, 4, 88),
(191, 4, 87),
(192, 1, 85),
(193, 1, 4),
(197, 4, 90),
(203, 1, 81),
(208, 1, 99),
(209, 4, 111),
(221, 4, 66),
(222, 4, 96),
(223, 4, 97),
(224, 4, 81),
(225, 4, 98),
(226, 4, 99),
(227, 4, 1),
(228, 4, 108),
(229, 4, 109),
(230, 4, 110),
(231, 4, 106),
(232, 4, 107),
(233, 4, 74),
(234, 4, 52),
(235, 4, 53),
(236, 4, 62),
(237, 4, 63),
(238, 4, 64),
(239, 4, 65),
(240, 4, 54),
(241, 4, 55),
(242, 4, 56),
(243, 4, 57),
(244, 4, 5),
(245, 4, 67),
(246, 4, 6),
(247, 4, 7),
(248, 4, 79),
(249, 4, 75),
(250, 4, 76),
(251, 4, 77),
(252, 4, 78),
(253, 4, 100),
(254, 4, 80),
(255, 4, 101),
(256, 4, 102),
(257, 4, 103),
(258, 4, 69),
(259, 4, 70),
(260, 4, 71),
(261, 4, 73),
(262, 4, 94),
(263, 4, 95),
(264, 4, 89),
(265, 4, 91),
(266, 4, 93),
(267, 3, 81),
(268, 3, 98),
(269, 3, 99),
(270, 3, 1),
(271, 3, 74),
(272, 3, 52),
(273, 3, 53),
(274, 3, 94),
(275, 3, 89),
(276, 1, 117),
(278, 1, 1),
(279, 4, 118),
(280, 4, 117),
(281, 4, 120),
(282, 4, 121),
(283, 4, 122),
(284, 4, 119),
(285, 1, 119),
(286, 4, 123),
(287, 1, 123),
(288, 4, 125),
(289, 4, 126),
(290, 4, 127),
(291, 4, 128),
(292, 4, 124),
(293, 4, 114),
(294, 4, 115),
(295, 4, 116),
(296, 4, 138),
(297, 4, 139),
(298, 4, 140),
(299, 4, 137),
(300, 4, 132),
(301, 4, 129),
(302, 4, 130),
(303, 4, 131),
(304, 4, 133),
(305, 4, 134),
(306, 4, 135),
(307, 4, 136),
(308, 4, 141),
(309, 4, 142),
(310, 4, 143),
(311, 4, 144),
(312, 4, 145),
(313, 2, 116),
(314, 2, 75),
(315, 2, 81),
(316, 4, 146),
(317, 4, 147),
(318, 4, 148),
(320, 1, 149),
(321, 4, 150);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
