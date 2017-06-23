-- phpMyAdmin SQL Dump
-- version 4.0.4.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 23. Jun 2017 um 11:51
-- Server Version: 5.6.13
-- PHP-Version: 7.1.6

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
  `name` char(50) COLLATE utf8_bin DEFAULT NULL,
  `sender` text COLLATE utf8_bin,
  `sender_address` char(254) COLLATE utf8_bin DEFAULT NULL,
  `msg` longtext COLLATE utf8_bin,
  `subject` text COLLATE utf8_bin,
  `variables` mediumtext COLLATE utf8_bin,
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle `mail_templates`
--

INSERT INTO `mail_templates` (`name`, `sender`, `sender_address`, `msg`, `subject`, `variables`) VALUES
('passwordForgotten', 'Administration@schwarze-ritter-augsburg.com', 'Administration@schwarze-ritter-augsburg.com', '<h3>Hallo {{userName}}, du willst dein Passwort Reseten?</h3><p>Um dein Passwort zu ändern clicke auf folgenden link.</p><a href="http://localhost/password/reset/{{hash}}">Reset Passwort</a>', 'pw', 'userName, hash, userEmail'),
('successfulRegistered', 'Administration@schwarze-ritter-augsburg.com', 'info@srza.Administration@schwarze-ritter-augsburg.com', '<h3>Hallo {{userName}}, du hast dich erfolgreich registriert</h3><p>Nach der Aktivierung durch einen Administrator hast du vollen Zugriff.</p><p> Du erhältst eine Mail sobald das erledigt ist.</p>', 'registration', NULL),
('activation', 'Administration@schwarze-ritter-augsburg.com', 'Administration@schwarze-ritter-augsburg.com', '<h3>Hallo {{name}}, dein Acoount {{email}} wurde soeben aktiviert</h3>', 'activation', 'name, email'),
('deactivation', 'Administration@schwarze-ritter-augsburg.com', 'Administration@schwarze-ritter-augsburg.com', '<h3>Hallo {{name}}, dein Account {{email}} wurde deaktiviert</h3>', 'deactivation', NULL),
('noReply', 'Administration@schwarze-ritter-augsburg.com', 'Administration@schwarze-ritter-augsburg.com', '<br/><p>Diese Nachricht wurde automatisch erstellt. Antworten auf diese eMailadresse werden nicht empfangen.</p>', 'noReply', NULL);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
