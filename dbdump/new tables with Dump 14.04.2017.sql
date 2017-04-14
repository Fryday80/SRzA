-- phpMyAdmin SQL Dump
-- version 4.0.4.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 14. Apr 2017 um 14:54
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
  `last_action_time` int(11) NOT NULL DEFAULT '0',
  `last_action_url` text COLLATE utf8_bin NOT NULL,
  `action_data` longtext COLLATE utf8_bin,
  UNIQUE KEY `sid` (`sid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle `active_users`
--

INSERT INTO `active_users` (`sid`, `ip`, `user_id`, `last_action_time`, `last_action_url`, `action_data`) VALUES
('j3ki4irtpv27sqskcshlsp16e5', '::1', 2, 1492181471, '/gallery', 'a:1:{i:0;a:41:{s:15:"REDIRECT_STATUS";s:3:"200";s:9:"HTTP_HOST";s:9:"localhost";s:15:"HTTP_CONNECTION";s:10:"keep-alive";s:11:"HTTP_PRAGMA";s:8:"no-cache";s:18:"HTTP_CACHE_CONTROL";s:8:"no-cache";s:30:"HTTP_UPGRADE_INSECURE_REQUESTS";s:1:"1";s:15:"HTTP_USER_AGENT";s:115:"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Safari/537.36";s:11:"HTTP_ACCEPT";s:74:"text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8";s:8:"HTTP_DNT";s:1:"1";s:12:"HTTP_REFERER";s:24:"http://localhost/gallery";s:20:"HTTP_ACCEPT_ENCODING";s:23:"gzip, deflate, sdch, br";s:20:"HTTP_ACCEPT_LANGUAGE";s:35:"de-DE,de;q=0.8,en-US;q=0.6,en;q=0.4";s:11:"HTTP_COOKIE";s:129:"pla3412_1_9_7_1=789b3a7284ca426a574144085665b786; pla3412_1_9_7_1_salt=vuV3inj4mwPiwml1QlMF; PHPSESSID=j3ki4irtpv27sqskcshlsp16e5";s:4:"PATH";s:1515:"C:Perl64sitein;C:Perl64in;D:WebWebprojekteTESTINGusbwebphp;C:Program Files (x86)InteliCLS Client;C:Program FilesInteliCLS Client;C:Windowssystem32;C:Windows;C:WindowsSystem32Wbem;C:WindowsSystem32WindowsPowerShellv1.0;C:Program Files (x86)IntelIntel(R) Management Engine ComponentsDAL;C:Program FilesIntelIntel(R) Management Engine ComponentsDAL;C:Program Files (x86)IntelIntel(R) Management Engine ComponentsIPT;C:Program FilesIntelIntel(R) Management Engine ComponentsIPT;C:Program Files (x86)NVIDIA CorporationPhysXCommon;C:WINDOWSsystem32;C:WINDOWS;C:WINDOWSSystem32Wbem;C:WINDOWSSystem32WindowsPowerShellv1.0;C:Program Files (x86)Common FilesAdobeAGL;D:Program Files (x86)QuickTimeQTSystem;D:Webusbwebphp;C:ProgramDataComposerSetupin;C:Program Files\nodejs;C:Program Files (x86)Bracketscommand;C:Program FilesMicrosoft SQL Server130ToolsBinn;C:Program FilesMicrosoftWeb Platform Installer;C:Program Filesdotnet;C:Program Files (x86)Microsoft SQL Server110DTSBinn;C:Program Files (x86)Microsoft SQL Server120DTSBinn;C:Program Files (x86)Microsoft SQL Server130DTSBinn;C:Program Files (x86)Bitvise SSH Client;D:HashiCorpVagrantin;D:Program FilesPuTTY;D:Program FilesGitGitcmd;D:Program FilesGitGitmingw64in;D:Program FilesGitGitusrin;C:Program Files (x86)Gourcecmd;C:UsersFryAppDataRoamingComposervendorin;C:UsersFryAppDataRoaming\npm;"D:Program FilesPuTTY"";s:10:"SystemRoot";s:10:"C:WINDOWS";s:7:"COMSPEC";s:27:"C:WINDOWSsystem32cmd.exe";s:7:"PATHEXT";s:53:".COM;.EXE;.BAT;.CMD;.VBS;.VBE;.JS;.JSE;.WSF;.WSH;.MSC";s:6:"WINDIR";s:10:"C:WINDOWS";s:16:"SERVER_SIGNATURE";s:0:"";s:15:"SERVER_SOFTWARE";s:31:"Apache/2.4.6 (Win32) PHP/5.6.30";s:11:"SERVER_NAME";s:9:"localhost";s:11:"SERVER_ADDR";s:3:"::1";s:11:"SERVER_PORT";s:2:"80";s:11:"REMOTE_ADDR";s:3:"::1";s:13:"DOCUMENT_ROOT";s:30:"D:/Web/Webprojekte/SRzA/public";s:14:"REQUEST_SCHEME";s:4:"http";s:14:"CONTEXT_PREFIX";s:0:"";s:21:"CONTEXT_DOCUMENT_ROOT";s:30:"D:/Web/Webprojekte/SRzA/public";s:12:"SERVER_ADMIN";s:14:"mail@localhost";s:15:"SCRIPT_FILENAME";s:40:"D:/Web/Webprojekte/SRzA/public/index.php";s:11:"REMOTE_PORT";s:5:"64850";s:12:"REDIRECT_URL";s:5:"/role";s:17:"GATEWAY_INTERFACE";s:7:"CGI/1.1";s:15:"SERVER_PROTOCOL";s:8:"HTTP/1.1";s:14:"REQUEST_METHOD";s:3:"GET";s:12:"QUERY_STRING";s:0:"";s:11:"REQUEST_URI";s:5:"/role";s:11:"SCRIPT_NAME";s:10:"/index.php";s:8:"PHP_SELF";s:10:"/index.php";s:18:"REQUEST_TIME_FLOAT";d:1492181471.069;s:12:"REQUEST_TIME";i:1492181471;}}');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `page_hits`
--

DROP TABLE IF EXISTS `page_hits`;
CREATE TABLE IF NOT EXISTS `page_hits` (
  `url` char(100) COLLATE utf8_bin NOT NULL,
  `last_action_time` bigint(20) NOT NULL,
  `counter` int(11) NOT NULL DEFAULT '1',
  `hit_day` char(50) COLLATE utf8_bin NOT NULL,
  UNIQUE KEY `url` (`url`,`hit_day`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


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
