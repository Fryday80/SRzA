-- phpMyAdmin SQL Dump
-- version 4.0.4.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 09. Mai 2017 um 19:01
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
-- Tabellenstruktur für Tabelle `blazon`
--

DROP TABLE IF EXISTS `blazon`;
CREATE TABLE IF NOT EXISTS `blazon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(40) COLLATE utf8_bin DEFAULT NULL,
  `isOverlay` tinyint(1) NOT NULL DEFAULT '0',
  `filename` char(50) COLLATE utf8_bin NOT NULL,
  `bigFilename` char(50) COLLATE utf8_bin DEFAULT NULL,
  `offsetX` int(11) NOT NULL DEFAULT '0',
  `offsetY` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `blazon_id_uindex` (`id`),
  UNIQUE KEY `blazon_name_uindex` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=7 ;

--
-- Daten für Tabelle `blazon`
--

INSERT INTO `blazon` (`id`, `name`, `isOverlay`, `filename`, `bigFilename`, `offsetX`, `offsetY`) VALUES
(1, 'standard', 0, 'standard.png', NULL, 0, 0),
(2, 'soldat', 0, 'soldat.png', NULL, 0, 0),
(3, 'zuLeym', 0, 'zuLeym.png', 'zuLeym_big.png', 0, 0),
(4, 'Adlerfels', 0, 'Adlerfels.png', 'Adlerfels_big.png', 0, 0),
(5, 'Nane', 0, 'Nane.png', NULL, 0, 0),
(6, 'Steffi', 0, 'Steffi.png', NULL, 0, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
