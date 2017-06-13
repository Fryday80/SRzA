-- phpMyAdmin SQL Dump
-- version 4.0.4.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 10. Jun 2017 um 14:49
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
-- Tabellenstruktur für Tabelle `mail_templates`
--

DROP TABLE IF EXISTS `mail_templates`;
CREATE TABLE IF NOT EXISTS `mail_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(50) COLLATE utf8_bin DEFAULT NULL,
  `sender` text COLLATE utf8_bin,
  `sender_address` char(254) COLLATE utf8_bin DEFAULT NULL,
  `msg` longtext COLLATE utf8_bin,
  `build_in` int(11) DEFAULT NULL,
  `subject` text COLLATE utf8_bin,
  `variables` mediumtext COLLATE utf8_bin,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=7 ;

--
-- Daten für Tabelle `mail_templates`
--

INSERT INTO `mail_templates` (`id`, `name`, `sender`, `sender_address`, `msg`, `build_in`, `subject`, `variables`) VALUES
(1, 'passwordForgotten', 'admin', 'info@srza.de', '<h3>Hallo {{userName}}, du willst dein Passwort Reseten?</h3>\r\n<p>\r\nUm dein Passwort zu ändern clicke auf folgenden link.\r\n</p>\r\n<a href="http://localhost/password/reset/{{hash}}">Reset Passwort</a>\r\n', 1, 'pw', NULL),
(2, 'successfulRegistered', 'admin', 'info@srza.de', '<h3>Hallo {{userName}}, du hast dich erfolgreich registriert</h3><p>Nach der Aktivierung durch einen Administrator hast du vollen Zugriff.</p><p> Du erhältst eine Mail sobald das erledigt ist.</p>', 1, 'registration', NULL),
(3, 'activation', 'admin', 'info.srza.de', '<h3>Hallo {{name}}, dein Acoount {{email}} wurde soeben aktiviert</h3>', 1, 'activation', NULL),
(4, 'deactivation', 'admin', 'info@srza.de', '<h3>Hallo {{name}}, dein Account {{email}} wurde deaktiviert</h3>', 1, 'deactivation', NULL);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
