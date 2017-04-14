-- phpMyAdmin SQL Dump
-- version 4.0.4.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 14. Apr 2017 um 14:01
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

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `nav`
--

DROP TABLE IF EXISTS `nav`;
CREATE TABLE IF NOT EXISTS `nav` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL,
  `label` tinytext COLLATE utf8_bin NOT NULL,
  `uri` tinytext COLLATE utf8_bin,
  `target` text COLLATE utf8_bin NOT NULL,
  `min_role_id` int(11) NOT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=28 ;

--
-- Daten für Tabelle `nav`
--

INSERT INTO `nav` (`id`, `menu_id`, `label`, `uri`, `target`, `min_role_id`, `lft`, `rgt`) VALUES
(1, 0, 'Home', '/', '', 1, 1, 2),
(2, 0, 'Über uns', '/cast', '', 1, 3, 10),
(3, 0, 'Unsere Mitglieder', '/cast', '', 1, 4, 5),
(4, 0, 'Termine', '/termine', '', 1, 6, 7),
(5, 0, 'Soziale Medien', '/Soziale-Medien', '', 1, 8, 9),
(6, 0, 'Für Veranstalter', '#', '', 1, 11, 18),
(7, 0, 'Info', '/Veranstalter', '', 1, 12, 13),
(8, 0, 'Angebote', '/Angebote', '', 1, 14, 15),
(9, 0, 'AGBs', '/AGB', '', 1, 16, 17),
(10, 0, 'Gallery', '/gallery', '', 1, 19, 20),
(11, 0, 'Administration', '#', '', 3, 21, 30),
(12, 0, 'Users', '/user', '', 3, 22, 23),
(13, 0, 'Cast Manager', '/castmanager', '', 3, 24, 25),
(14, 0, 'Content', '/cms', '', 3, 26, 27),
(15, 0, 'Navigation', '/nav/sort', '', 3, 28, 29),
(16, 0, 'Webmasters', '#', '', 4, 31, 48),
(17, 0, 'Users and Rights', '#', '', 4, 32, 41),
(18, 0, 'User Rights', '/user', '', 4, 33, 34),
(19, 0, 'Roles', '/role', '', 4, 35, 36),
(20, 0, 'Permissions', '/permission', '', 4, 37, 38),
(21, 0, 'Resources', '/resource', '', 4, 39, 40),
(22, 0, 'FileBrowser', '/media/filebrowser', '', 4, 42, 43),
(23, 0, 'Gallery Edit', '/album', '', 4, 44, 45),
(24, 0, 'Gallery Edit', '/album', '', 4, 46, 47),
(25, 0, 'Links', '/links', '', 1, 49, 50);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
