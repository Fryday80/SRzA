-- phpMyAdmin SQL Dump
-- version 4.0.4.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 14. Apr 2017 um 15:49
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
-- Tabellenstruktur für Tabelle `active_users`
--

DROP TABLE IF EXISTS `active_users`;
CREATE TABLE IF NOT EXISTS `active_users` (
  `sid` char(50) COLLATE utf8_bin NOT NULL,
  `ip` text COLLATE utf8_bin NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `time` bigint(20) NOT NULL DEFAULT '0',
  `last_action_url` text COLLATE utf8_bin NOT NULL,
  `action_data` longtext COLLATE utf8_bin,
  UNIQUE KEY `sid` (`sid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `page_hits`
--

DROP TABLE IF EXISTS `page_hits`;
CREATE TABLE IF NOT EXISTS `page_hits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` char(100) COLLATE utf8_bin NOT NULL,
  `time` bigint(20) NOT NULL,
  `counter` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=11 ;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `system_log`
--

DROP TABLE IF EXISTS `system_log`;
CREATE TABLE IF NOT EXISTS `system_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` text COLLATE utf8_bin NOT NULL,
  `title` text COLLATE utf8_bin NOT NULL,
  `message` text COLLATE utf8_bin NOT NULL,
  `time` bigint(20) NOT NULL,
  `data` text COLLATE utf8_bin,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
