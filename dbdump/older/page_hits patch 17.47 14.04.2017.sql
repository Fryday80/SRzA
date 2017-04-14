-- phpMyAdmin SQL Dump
-- version 4.0.4.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 14. Apr 2017 um 15:47
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
-- Tabellenstruktur für Tabelle `page_hits`
--

DROP TABLE IF EXISTS `page_hits`;
CREATE TABLE IF NOT EXISTS `page_hits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` char(100) COLLATE utf8_bin NOT NULL,
  `last_action_time` bigint(20) NOT NULL,
  `counter` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=11 ;

--
-- Daten für Tabelle `page_hits`
--

INSERT INTO `page_hits` (`id`, `url`, `last_action_time`, `counter`) VALUES
(1, '/?_tr_error', 1434, 9),
(2, '/?_tracy_skip_error', 14766434, 3);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
