-- phpMyAdmin SQL Dump
-- version 4.0.4.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 11. Jul 2017 um 21:09
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
CREATE DATABASE IF NOT EXISTS `db2836034` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `db2836034`;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `blazon`
--

DROP TABLE IF EXISTS `blazon`;
CREATE TABLE IF NOT EXISTS `blazon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(40) COLLATE utf8_bin DEFAULT NULL,
  `filename` char(50) COLLATE utf8_bin NOT NULL,
  `bigFilename` char(50) COLLATE utf8_bin DEFAULT NULL,
  `isOverlay` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=12 ;

--
-- Daten für Tabelle `blazon`
--

UPDATE `blazon` SET `id` = 1,`name` = 'standard',`filename` = 'standard.png',`bigFilename` = NULL,`isOverlay` = 0 WHERE `blazon`.`id` = 1;
UPDATE `blazon` SET `id` = 2,`name` = 'soldat',`filename` = 'soldat.png',`bigFilename` = NULL,`isOverlay` = 1 WHERE `blazon`.`id` = 2;
UPDATE `blazon` SET `id` = 3,`name` = 'zuLeym',`filename` = 'zuLeym.png',`bigFilename` = 'zuLeym_big.png',`isOverlay` = 0 WHERE `blazon`.`id` = 3;
UPDATE `blazon` SET `id` = 7,`name` = 'king',`filename` = 'king.png',`bigFilename` = NULL,`isOverlay` = 1 WHERE `blazon`.`id` = 7;
UPDATE `blazon` SET `id` = 9,`name` = 'Drachenfels',`filename` = 'Drachenfels.png',`bigFilename` = 'Drachenfels_big.png',`isOverlay` = 0 WHERE `blazon`.`id` = 9;
UPDATE `blazon` SET `id` = 11,`name` = 'Nane',`filename` = 'Nane.png',`bigFilename` = NULL,`isOverlay` = 0 WHERE `blazon`.`id` = 11;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `families`
--

DROP TABLE IF EXISTS `families`;
CREATE TABLE IF NOT EXISTS `families` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8_bin NOT NULL,
  `blazon_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `families`
--

UPDATE `families` SET `id` = 1,`name` = 'BurgerKing',`blazon_id` = 1 WHERE `families`.`id` = 1;
UPDATE `families` SET `id` = 2,`name` = 'zu Leym',`blazon_id` = 3 WHERE `families`.`id` = 2;
UPDATE `families` SET `id` = 3,`name` = 'vom Drachenstein',`blazon_id` = 9 WHERE `families`.`id` = 3;
UPDATE `families` SET `id` = 4,`name` = 'Fam3',`blazon_id` = 1 WHERE `families`.`id` = 4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `job`
--

DROP TABLE IF EXISTS `job`;
CREATE TABLE IF NOT EXISTS `job` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job` text COLLATE utf8_bin NOT NULL,
  `blazon_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=7 ;

--
-- Daten für Tabelle `job`
--

UPDATE `job` SET `id` = 1,`job` = 'Ritter',`blazon_id` = 0 WHERE `job`.`id` = 1;
UPDATE `job` SET `id` = 2,`job` = 'Bogenschütze',`blazon_id` = 0 WHERE `job`.`id` = 2;
UPDATE `job` SET `id` = 3,`job` = 'Schmied',`blazon_id` = 0 WHERE `job`.`id` = 3;
UPDATE `job` SET `id` = 4,`job` = 'Bader',`blazon_id` = 0 WHERE `job`.`id` = 4;
UPDATE `job` SET `id` = 5,`job` = 'Hofnarr',`blazon_id` = 0 WHERE `job`.`id` = 5;
UPDATE `job` SET `id` = 6,`job` = 'Hauptmann',`blazon_id` = 2 WHERE `job`.`id` = 6;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
