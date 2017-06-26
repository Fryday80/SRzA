-- phpMyAdmin SQL Dump
-- version 4.0.4.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 13. Jun 2017 um 13:42
-- Server Version: 5.6.13
-- PHP-Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `abonnements`
--

DROP TABLE IF EXISTS `abonnements`;
CREATE TABLE IF NOT EXISTS `abonnements` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `since` bigint(20) NOT NULL,
  `multi_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `abonnementtypes`
--

DROP TABLE IF EXISTS `abonnementtypes`;
CREATE TABLE IF NOT EXISTS `abonnementtypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `abonnement_id` int(11) NOT NULL,
  `name` text COLLATE utf8_bin NOT NULL,
  `monthCost` decimal(10,0) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `active_users`
--

DROP TABLE IF EXISTS `active_users`;
CREATE TABLE IF NOT EXISTS `active_users` (
  `sid` char(50) COLLATE utf8_bin NOT NULL,
  `ip` text COLLATE utf8_bin NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `last_action_url` text COLLATE utf8_bin NOT NULL,
  `time` bigint(20) NOT NULL DEFAULT '0',
  `data` longtext COLLATE utf8_bin NOT NULL,
  UNIQUE KEY `sid` (`sid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `albumimages`
--

DROP TABLE IF EXISTS `albumimages`;
CREATE TABLE IF NOT EXISTS `albumimages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `album_id` int(11) NOT NULL,
  `image_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `albumimages`
--

INSERT INTO `albumimages` (`id`, `album_id`, `image_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 4),
(4, 2, 3);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `albums`
--

DROP TABLE IF EXISTS `albums`;
CREATE TABLE IF NOT EXISTS `albums` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `folder` varchar(30) COLLATE utf8_bin NOT NULL,
  `event` text COLLATE utf8_bin,
  `timestamp` bigint(20) DEFAULT NULL,
  `preview_pic` text COLLATE utf8_bin,
  `visibility` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=4 ;

--
-- Daten für Tabelle `albums`
--

INSERT INTO `albums` (`id`, `folder`, `event`, `timestamp`, `preview_pic`, `visibility`) VALUES
(1, '2016', 'eventtext', 1480546800, '', 1),
(2, 'folder 2', 'event 2', 557577678, 'test.jpg', 1),
(3, 'filder 3', 'event 3', 596934000, 'gibts.ned', 0);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=8 ;

--
-- Daten für Tabelle `blazon`
--

INSERT INTO `blazon` (`id`, `name`, `isOverlay`, `filename`, `bigFilename`, `offsetX`, `offsetY`) VALUES
(1, 'standard', 0, 'standard.png', NULL, 0, 0),
(2, 'soldat', 0, 'soldat.png', NULL, 0, 0),
(3, 'zuLeym', 0, 'zuLeym.png', 'zuLeym_big.png', 0, 0),
(4, 'Adlerfels', 0, 'Adlerfels.png', 'Adlerfels_big.png', 0, 0),
(5, 'Nane', 0, 'Nane.png', NULL, 0, 0),
(6, 'Steffi', 0, 'Steffi.png', NULL, 0, 0),
(7, 'king', 0, 'king.png', NULL, 0, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `characters`
--

DROP TABLE IF EXISTS `characters`;
CREATE TABLE IF NOT EXISTS `characters` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `surename` text NOT NULL,
  `gender` enum('m','f') NOT NULL,
  `birthday` tinytext NOT NULL,
  `job_id` int(11) NOT NULL,
  `family_id` int(11) NOT NULL,
  `guardian_id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `tross_id` int(11) NOT NULL,
  `vita` text NOT NULL,
  `active` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Daten für Tabelle `characters`
--

INSERT INTO `characters` (`id`, `user_id`, `name`, `surename`, `gender`, `birthday`, `job_id`, `family_id`, `guardian_id`, `supervisor_id`, `tross_id`, `vita`, `active`) VALUES
(1, 1, 'Vorstand', 'von Augsburg', 'm', '1784-04-22', 0, 0, 0, 0, 0, '0', 1),
(2, 2, 'Fry', 'zu Leym', 'm', '1748-04-07', 1, 2, 0, 1, 0, 'jgfjjdj', 1),
(3, 6, 'Nane', 'zu Leym', 'f', '2017-04-09', 0, 2, 2, 0, 0, 'edqq', 1),
(4, 7, 'Christoph', 'zu Leym', 'm', '1715-07-29', 5, 2, 3, 0, 0, 'qd', 1),
(9, 8, 'Lara', 'zu Leym', 'f', '2017-04-23', 0, 2, 3, 0, 0, 'fdfdf', 1),
(11, 9, 'Ben', 'zu Leym', 'm', '', 0, 2, 3, 0, 0, 'dqw', 1),
(12, 10, 'Basti', 'Bogi', 'm', '', 2, 3, 2, 2, 0, 'csd', 1),
(13, 3, 'Wasser', 'Platsch', 'f', '', 4, 4, 2, 2, 0, 'csd', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `dynamic_hash`
--

DROP TABLE IF EXISTS `dynamic_hash`;
CREATE TABLE IF NOT EXISTS `dynamic_hash` (
  `hash` char(32) COLLATE utf8_bin NOT NULL,
  `time` bigint(20) DEFAULT NULL,
  `email` text COLLATE utf8_bin,
  UNIQUE KEY `id` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle `dynamic_hash`
--

INSERT INTO `dynamic_hash` (`hash`, `time`, `email`) VALUES
('a5d162ac9fdccb91c530e55b2c570280', 1496855102, '600'),
('b0a1882b1e21cecc5baebf2f3105ba9b', 1496855339, '600');

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

INSERT INTO `families` (`id`, `name`, `blazon_id`) VALUES
(1, 'BurgerKing', 1),
(2, 'zu Leym', 3),
(3, 'Fam2', 0),
(4, 'Fam3', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `images`
--

DROP TABLE IF EXISTS `images`;
CREATE TABLE IF NOT EXISTS `images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extension` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text_1` text COLLATE utf8mb4_unicode_ci,
  `text_2` text COLLATE utf8mb4_unicode_ci,
  `visibility` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `images`
--

INSERT INTO `images` (`id`, `filename`, `extension`, `text_1`, `text_2`, `visibility`) VALUES
(1, 'testimage', 'jpg', 'Test text 1 i1', 'Test text 2 i1', 1),
(2, 'testimage2', 'jpg', 'Test text 1 i2', 'Test text 2 i2', 1),
(3, 'sgfgsf', 'gfd', 'fdg', 'dgf', NULL),
(4, 'sgs', 'gsg', 'gsgsfg', 'sgfgsgg', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `job`
--

DROP TABLE IF EXISTS `job`;
CREATE TABLE IF NOT EXISTS `job` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=6 ;

--
-- Daten für Tabelle `job`
--

INSERT INTO `job` (`id`, `job`) VALUES
(1, 'Ritter'),
(2, 'Bogenschütze'),
(3, 'Schmied'),
(4, 'Bader'),
(5, 'Hofnarr');

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
('passwordForgotten', 'admin', NULL, '<h3>Hallo {{userName}}, du willst dein Passwort Reseten?</h3><p>Um dein Passwort zu ändern clicke auf folgenden link.</p><a href="http://localhost/password/reset/{{hash}}">Reset Passwort</a>', 'pw', 'userName hash userEmail'),
('successfulRegistered', 'admin', 'info@srza.de', '<h3>Hallo {{userName}}, du hast dich erfolgreich registriert</h3><p>Nach der Aktivierung durch einen Administrator hast du vollen Zugriff.</p><p> Du erhältst eine Mail sobald das erledigt ist.</p>', 'registration', NULL),
('activation', 'admin', 'info.srza.de', '<h3>Hallo {{name}}, dein Acoount {{email}} wurde soeben aktiviert</h3>', 'activation', NULL),
('deactivation', 'admin', 'info@srza.de', '<h3>Hallo {{name}}, dein Account {{email}} wurde deaktiviert</h3>', 'deactivation', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `multiabo`
--

DROP TABLE IF EXISTS `multiabo`;
CREATE TABLE IF NOT EXISTS `multiabo` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=29 ;

--
-- Daten für Tabelle `nav`
--

INSERT INTO `nav` (`id`, `menu_id`, `label`, `uri`, `target`, `min_role_id`, `lft`, `rgt`) VALUES
(1, 0, 'Home', '/', '', 1, 1, 2),
(2, 0, 'Über uns', '/cast', '', 1, 3, 10),
(3, 0, 'Unsere Mitglieder', '/cast', '', 1, 4, 5),
(4, 0, 'Termine', '/calendar', '_self', 1, 6, 7),
(5, 0, 'Soziale Medien', '/Soziale-Medien', '', 1, 8, 9),
(6, 0, 'Für Veranstalter', '#', '', 1, 11, 18),
(7, 0, 'Info', '/Veranstalter', '', 1, 12, 13),
(8, 0, 'Angebote', '/Angebote', '', 1, 14, 15),
(9, 0, 'AGBs', '/AGB', '', 1, 16, 17),
(10, 0, 'Gallery', '/gallery', '', 1, 19, 20),
(11, 0, 'Administration', '#', '', 3, 21, 34),
(12, 0, 'Users', '/user', '', 3, 26, 27),
(13, 0, 'Cast Manager', '/castmanager', '', 3, 28, 29),
(14, 0, 'Content', '/cms', '', 3, 30, 31),
(15, 0, 'Navigation', '/nav/sort', '', 3, 32, 33),
(16, 0, 'Webmasters', '/system/dashboard', '_self', 4, 35, 54),
(17, 0, 'Users and Rights', '#', '', 4, 38, 47),
(18, 0, 'User Rights', '/user', '', 4, 39, 40),
(19, 0, 'Roles', '/role', '', 4, 41, 42),
(20, 0, 'Permissions', '/permission', '', 4, 43, 44),
(21, 0, 'Resources', '/resource', '', 4, 45, 46),
(22, 0, 'FileBrowser', '/media/filebrowser', '', 4, 48, 49),
(23, 0, 'Gallery Edit', '/album', '', 4, 50, 51),
(24, 0, 'Dashboard', '/system/dashboard', '', 4, 52, 53),
(25, 0, 'Links', '/links', '', 1, 55, 56),
(26, 0, 'Wappen', '/castmanager/wappen', '_self', 4, 24, 25),
(27, 0, 'Mail Templates', '/system/mailTemplates', '_self', 4, 22, 23),
(28, 0, 'Calendar Config', '/calendar/config', '_self', 4, 36, 37);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pages`
--

DROP TABLE IF EXISTS `pages`;
CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` tinytext NOT NULL,
  `url` tinytext NOT NULL,
  `exceptedRoles` text NOT NULL,
  `content` mediumtext NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Daten für Tabelle `pages`
--

INSERT INTO `pages` (`id`, `title`, `url`, `exceptedRoles`, `content`, `updated`) VALUES
(1, 'Wilkommen', 'Home', '', '<p>Unsere Seite befindet sich gerade im Aufbau<br />\r\nSchauen Sie sich gerne um<br />\r\n<br />\r\nWir stellen ein Raubritterlager dar. Die Gruppe besteht aktuell aus &uuml;ber 20 Mitgliedern + Kindern.<br />\r\nWir versuchen ein sch&ouml;nes belebtes Lager auf ca. 3 bis 4 Events im Jahr aufzustellen<br />\r\nsowie als Gruppe M&auml;rkte usw. zu Besuchen, machen jedoch kein Reenacment, der Spa&szlig; am Hobby hat Vorrang.<br />\r\nWir bieten Workshops, Training f&uuml;r Show und Freikampf und einen lustigen Haufen.<br />\r\nBei Interesse bitte eine Mail an uns.<br />\r\nWir freuen uns &uuml;ber Anfragen von Einzelpersonen sowie ganzen Familien, Anf&auml;ngern und alten Hasen gleicherma&szlig;en.<br />\r\nWir sind auch auf&nbsp;<a href="https://www.facebook.com/Schwarze-Ritter-zu-Augsburg-799896913462812/" target="_blank">Facebook</a>&nbsp;zu erreichen ...</p>\r\n\r\n<p>&nbsp;</p>\r\n', '2016-09-23 16:45:48'),
(3, 'Für Veranstalter', 'Veranstalter', '', '<div class="page veranstalter"><p>Sehr geehrte Damen und Herren,<br />\r\n<br />\r\nliebe Veranstalter und Organisatoren,<br />\r\nwir bilden momentan ein Gruppe aus 30 Erwachsenen und 8 Kindern, die unser Lager mit ca. 15 Schlaf-, Schau- und Gruppenzelten beleben, unsere Mitglieder haben teilweise &uuml;ber 10 Jahre Erfahrung im Mittelalter was sich in unseren Shows, Animationen und dem gesamten Lager widerspiegelt.<br />\r\n<br />\r\nUnser gesamt Angebot umfasst:<br />\r\nbelebtes Lager mit Handarbeit und Handwerk<br />\r\nKinderspiele gegen Spende<br />\r\noffenes Waffenzelt, zum Infomieren und anfassen<br />\r\nFeldk&uuml;che, nach mittelalterlichen Rezepten<br />\r\nFeldlazarett<br />\r\nPranger<br />\r\noffene Schauzelte<br />\r\nKleidungs- und Darstellungszeitraum ca. 1350-1400 (unter bewusster Aussparung der Waffenklasse Armbrust)<br />\r\nDa wir auch Kleinkinder im Lager haben, ist es nicht m&ouml;glich jedes Zelt als Schauzelt zu &ouml;ffnen.<br />\r\n<br />\r\n<br />\r\nInnerhalb unseres Lagers bieten wir Waffenvorf&uuml;hrungen und bei Platz und Witterung auch Feuerspiel an. Am Pranger und im Feldlazarett werden regelm&auml;&szlig;ig kleine Vorf&uuml;hrungen gemacht und Besucher dadurch animiert.<br />\r\n<br />\r\nGegen vertragliche Regelung sind wir auf Verhandlungsbasis auch f&uuml;r Shows-und Animationen au&szlig;erhalb des Lagers buchbar.<br />\r\n<br />\r\nAuf dem beigef&uuml;gtem Aufstellungsplan ist die derzeitige Idealaufstellung unseres Lagers zu erkennen sowie die gesamt Gr&ouml;&szlig;e und die entsprechend ben&ouml;tigte Fl&auml;che ersichtlich.<br />\r\nDa alle Mitglieder unserer Gruppe das mittelalterliche Lagerleben als Hobby neben Beruf und Familie machen, ist unsere gesamte Ausr&uuml;stung aus privaten Mitteln bestritten die von allen Mitgliedern f&uuml;r die Gruppe zusammengetragen wurden.<br />\r\nAlleine die vorhandenen Zelte bilden hier eine Summe von &uuml;ber 15 000,- Euro.<br />\r\n<br />\r\nIn dieser Aufstellung k&ouml;nnen wir unser gesamtes Repertoire aufbauen und zur Schau stellen.<br />\r\nDie Zelte und Fl&auml;chen sind umstellbar jedoch ben&ouml;tigen wir eine m&ouml;glichst ebene und rechteckige zusammenh&auml;ngende Fl&auml;che.<br />\r\nUnsere Zelte werden normal mit Seilen und Heringen abgespannt, f&uuml;r den Aufbau ben&ouml;tigen wir entsprechenden Untergrund.<br />\r\nDie Feuerstellen sind mindestens 20 cm &uuml;ber dem Boden, wir verf&uuml;gen &uuml;ber die entsprechenden L&ouml;schmittel.Auf Wunsch k&ouml;nnen wir zus&auml;tzlich zu unseren Feuerstellen die wir normal dabei haben auch Feuerk&ouml;rbe mit aufstellen.<br />\r\n<br />\r\nUnser Lager wird mit Seilen abgesperrt und hat i.d.R. zwei Ein und Ausg&auml;nge zum Hauptweg hin, die Bereiche mit den Feuern werden nochmals absperrt.<br />\r\nDie Zelte d&uuml;rfen von au&szlig;en betrachtet werden, ggf. darf durch den ge&ouml;ffneten Eingang hineingesehen werden, sie d&uuml;rfen aber <b>NICHT BETRETEN</b> werden.</p>\r\n</div>\r\n', '2016-09-24 00:13:39'),
(4, 'AGBs', 'AGB', '', '<p>Unsere AGBs:&nbsp;<br />\r\n<br />\r\nAllgemeine Gesch&auml;ftsbedingungen der schwarzen Ritter zu Augsburg f&uuml;r die Teilnahme an Veranstaltungen<br />\r\n<br />\r\nDie schwarzen Ritter zu Augsburg verpflichten sich entsprechend den in der Vereinbarung getroffenen Punkten nach besten M&ouml;glichkeiten und Gegebenheiten zum Erfolg der Veranstaltung beizutragen. Die schwarzen Ritter zu Augsburg versorgen sich auf der Veranstaltung selbst. Sind vom Veranstalter Essens &ndash; und/oder Getr&auml;nkegutscheine vorgesehen, ist die Summe des Gegenwerts oder eine anderweitig verhandelte Summe sp&auml;testens einen Monat vor der Veranstaltung vom Veranstalter auf das Konto der Gruppe zu &uuml;berweisen. Weitere Anspruch von Seiten der SRZA bestehen, falls nicht anders Vereinbart, nicht.<br />\r\nAlle Einnahmen der SRZA die durch Spenden von Besuchern entstehen verbleiben im vollen bei der Gruppe. Ausnahme sind Veranstaltungen mit wohlt&auml;tigen oder karikativen Charakter.<br />\r\nDie SRZA sind, wenn nicht schriftlich anderweitig Vereinbart, in vollem Umfang &uuml;ber den Veranstalter versichert. Sollten die eigenen Versicherungen der SRZA beansprucht werden, haften diese nicht f&uuml;r Sch&auml;den die durch Fehler Dritter oder schadhafte oder fehlerhafte Gegenst&auml;nde, die zu Verf&uuml;gung gestellt werden ( z.B. Absperrungen, B&uuml;hnen, B&uuml;hnenaufbauten usw.) herbeigef&uuml;hrt werden und sind nicht den SRZA und deren Mitgliedern anzulasten.<br />\r\nDie SRZA verpflichten sich M&uuml;ll in die entsprechenden Beh&auml;ltnisse die vom Veranstalter zu Verf&uuml;gung gestellt werden auf dem Gel&auml;nde zu entsorgen.&nbsp;<br />\r\nDie SRZA verpflichten sich den zur Verf&uuml;gung gestellten Lagerplatz den Gegebenheiten entsprechend zu verlassen.&nbsp;<br />\r\nDie SRZA halten g&auml;ngige Feuerl&ouml;scher f&uuml;r jede betriebene Feuerstelle im Lager bereit, weitere geforderte L&ouml;schmittel sind vom Veranstalter zu stellen.&nbsp;<br />\r\nDie SRZA halten eine 24 STD. Bewachung ihres Lagers durch eigene Mitglieder vor, dies umfasst auch die Kontrolle der Feuerstellen im Lager.&nbsp;<br />\r\nGruppen-. Security- und Organisationsteamsfremde Personen d&uuml;rfen im Sinne des &quot;Hausrechts&quot; aus dem Lager (seilumgrenzter Bereich) verwiesen werden. Gruppenfremden Personen ist das Betreten der Zelte nicht gestattet.&nbsp;<br />\r\nShowauftritte und andere Buchungen die der Veranstalter t&auml;tigt, jedoch nicht mindestens zwei Monate vor Veranstaltung wieder absagt wenn er diese nicht ben&ouml;tigt sind regul&auml;r zu bezahlen.&nbsp;<br />\r\nAlle sonstigen Aufwandsentsch&auml;digungen sind einen Monat vor beginn der Veranstaltung, bei Aufwandsentsch&auml;digung aus Gewinn sp&auml;testens einen Monat nach der Veranstaltung auf das Konto der SRZA zu &uuml;berweisen.&nbsp;<br />\r\nWenn nicht anders schriftlich Vereinbart haben die Mitglieder der SRZA ihre Hunde, f&uuml;r die entsprechende Versicherungen vorliegen, unter Beachtung der Hygiene usw. auf dem Event bei sich. Auf- und Abbau Zeiten sind vom Veranstalter wie den SRZA einzuhalten, sollten diese vom Veranstalter kurzfristig ge&auml;ndert werden, k&ouml;nnen die SRZA entstehende Kosten beim Veranstalter geltend machen.&nbsp;<br />\r\nDie SRZA n&auml;chtigen entsprechend Jahreszeit ( April bis Oktober) in den von ihnen aufgestellten Zelten, sollte ein Veranstalter dies nicht w&uuml;nschen oder es au&szlig;erhalb des Zeitraumes April bis Oktober fallen, hat er auf seine kosten eine entsprechende L&ouml;sung anzubieten.&nbsp;<br />\r\nF&uuml;r alle Vereinbarungen gilt deutsches Recht, Gerichtsstand ist Augsburg, Bayern.&nbsp;<br />\r\nSollten einzelne Bestimmungen dieses Vertrages unwirksam oder undurchf&uuml;hrbar sein oder nach Vertragsschluss unwirksam oder undurchf&uuml;hrbar werden, bleibt davon die Wirksamkeit des Vertrages im &Uuml;brigen unber&uuml;hrt. An die Stelle der unwirksamen oder undurchf&uuml;hrbaren Bestimmung soll diejenige wirksame und durchf&uuml;hrbare Regelung treten, deren Wirkungen der wirtschaftlichen Zielsetzung am n&auml;chsten kommen, die die Vertragsparteien mit der unwirksamen bzw. undurchf&uuml;hrbaren Bestimmung verfolgt haben. Die vorstehenden Bestimmungen gelten entsprechend f&uuml;r den Fall, dass sich der Vertrag als l&uuml;ckenhaft erweist.&nbsp;</p>\r\n', '2016-09-24 00:13:54'),
(5, 'Unsere Angebote', 'Angebote', '', '<div class="page veranstalter angebote">Leider ist es einigen Veranstaltern nicht m&ouml;glich aus platztechnischen, finanziellen oder anderen organisatorischen Gr&uuml;nden unsere stetig wachsende Gruppe im Komplettpaket auf ihrem Event unterzubringen. Daher bieten wir verschiedene L&ouml;sungen an. Die einzelnen Bedingungen sind auch noch im Vorfeld verhandelbar, m&uuml;ssen dann aber schriftlich festgehalten werden.&nbsp; <box> <boxtitel>&nbsp;</boxtitel> <boxcontent> <accordion> <span>Vorraussetzungen </span>\r\n<div><strong><u>Grundvoraussetzungen Leistung Veranstalter&nbsp;</u></strong><br />\r\n<strong>&bull; Feuerholz &amp; Wasser&nbsp;<br />\r\n&bull; Sanit&auml;re Anlagen 24 Std. ge&ouml;ffnet&nbsp;<br />\r\n&bull; Sonstige Voraussetzungen gegeben&nbsp;</strong></div>\r\n<span> <strong><u>Angebot 1</u></strong> </span>\r\n\r\n<div><u><strong>Leistung Veranstalter&nbsp;</strong></u><br />\r\nGrundvoraussetzungen&nbsp;<br />\r\n<br />\r\n<strong><u>Leistung schwarze Ritter zu Augsburg&nbsp;</u></strong><br />\r\nTeilnehmerzahl entsprechend R&uuml;ckmeldung&nbsp;<br />\r\nZelte entsprechend Teilnehmerzahl&nbsp;<br />\r\nFeuerstelle &amp; Dreibein, Tische&nbsp;<br />\r\nB&auml;nke &amp; Plane&nbsp;<br />\r\nSpiele gegen Spende&nbsp;</div>\r\n<span> <strong><u>Angebot 2&nbsp;</u></strong> </span>\r\n\r\n<div><u><strong>Leistung Veranstalter&nbsp;</strong></u><br />\r\nGrundvoraussetzungen +&nbsp;<br />\r\nStrom&nbsp;<br />\r\nAufwandsentsch&auml;digung VB&nbsp;<br />\r\n<span><strong><u>Leistung schwarze Ritter zu Augsburg&nbsp;</u></strong><br />\r\nTeilnehmerzahl entsprechend R&uuml;ckmeldung&nbsp;<br />\r\nZelte entsprechend Teilnehmerzahl&nbsp;<br />\r\nFeuerstelle &amp; Dreibein, Tische&nbsp;<br />\r\nB&auml;nke &amp; Plane&nbsp;<br />\r\nSpiele gegen Spende&nbsp;<br />\r\nGe&ouml;ffnete Schauzelte entsprechend Teilnehmerzahl&nbsp;<br />\r\nWaffenzelt&nbsp;<br />\r\nPranger&nbsp;<br />\r\nAnimationen von Besuchern&nbsp;</span></div>\r\n<span> <u><strong>Angebot 3&nbsp;</strong></u> </span>\r\n\r\n<div><u><strong>Leistung Veranstalter</strong></u>&nbsp;<br />\r\nGrundvoraussetzungen +&nbsp;<br />\r\nStrom&nbsp;<br />\r\nAufwandsentsch&auml;digung Anfahrt &amp; Abfahrt&nbsp;<br />\r\n5,- Euro p. Person/Tag (nicht f&uuml;r Kinder)&nbsp;<br />\r\n<span><strong><u>Leistung schwarze Ritter zu Augsburg&nbsp;</u></strong><br />\r\nTeilnehmerzahl entsprechend R&uuml;ckmeldung ( min. 15 ohne Kinder)&nbsp;<br />\r\nZelte entsprechend Teilnehmerzahl&nbsp;<br />\r\nFeuerstelle&nbsp;<br />\r\nSpiele gegen Spende&nbsp;<br />\r\nGe&ouml;ffnete Schauzelte entsprechend Teilnehmerzahl&nbsp;<br />\r\nWaffenzelt&nbsp;<br />\r\nPranger&nbsp;<br />\r\nTafel&nbsp;<br />\r\nFeldlazarett&nbsp;<br />\r\nShowk&uuml;che&nbsp;<br />\r\nAnimationen von Besuchern im Lager&nbsp; </span></div>\r\n<span> <strong><u>Angebot 4&nbsp;</u></strong> </span>\r\n\r\n<div><u><strong>Leistung Veranstalter</strong></u>&nbsp;<br />\r\nGrundvoraussetzungen +&nbsp;<br />\r\nStrom&nbsp;<br />\r\nAufwandsentsch&auml;digung Anfahrt &amp; Abfahrt&nbsp;<br />\r\n6,50 Euro p. Person/Tag (nicht f&uuml;r Kinder) &bull;&nbsp;<br />\r\n<br />\r\n<strong><u>Leistung schwarze Ritter zu Augsburg&nbsp;</u></strong><br />\r\nTeilnehmerzahl entsprechend R&uuml;ckmeldung (min. 20 ohne Kinder)&nbsp;<br />\r\nZelte entsprechend Teilnehmerzahl&nbsp;<br />\r\nFeuerstelle&nbsp;<br />\r\nSpiele gegen Spende&nbsp;<br />\r\nGe&ouml;ffnete Schauzelte entsprechend Teilnehmerzahl&nbsp;<br />\r\nWaffenzelt&nbsp;<br />\r\nPranger&nbsp;<br />\r\nTafel&nbsp;<br />\r\nFeldlazarett&nbsp;<br />\r\nShowk&uuml;che: gro&szlig;e Feldk&uuml;che&nbsp;<br />\r\nFeuershow im Lager&nbsp;<br />\r\nK&auml;mpfe im Lager&nbsp;<br />\r\nAnimationen von Besuchern im Lager</div>\r\n</accordion></boxcontent> </box>\r\n\r\n<div class="clear">&nbsp;</div>\r\n\r\n<div>\r\n<hr />\r\n<p><br />\r\n<strong>Unabh&auml;ngig der Angebote sind f&uuml;r das Gel&auml;nde Feuershows und Kampfshows buchbar&nbsp;</strong><br />\r\n<br />\r\nHierf&uuml;r erheben wir eine Geb&uuml;hr f&uuml;r Versicherungen und Aufwand von&nbsp;<br />\r\n<br />\r\nFeuershow 50,-&nbsp;<br />\r\n<br />\r\nKampfshow 50,-&nbsp;<br />\r\n<br />\r\nje Show.&nbsp;<br />\r\n<br />\r\nBei den Shows sind L&auml;nge und Besonderheiten vereinbar, m&uuml;ssen jedoch sp&auml;testens sechs Monate vor dem jeweiligen Event fest Gebucht und schriftlich Vereinbart werden.&nbsp;<br />\r\n<br />\r\nF&uuml;r Freik&auml;mpfe und Freischlachten sowie Turnire bitte die gesonderten Hinweise beachten.&nbsp;<br />\r\n<br />\r\nWir sind gerne bereit Pakete und Angebote entsprechend zu bearbeiten, jedoch k&ouml;nnen wir f&uuml;r die von uns zu bringenden Leistungen von den Forderungen an die Veranstalter nur bedingt abweichen.&nbsp;<br />\r\nF&uuml;r eine Buchung unsere Gruppe halten wir ein entsprechendes Formular vor, welches auf Anfrage gerne verschickt wird.&nbsp;</p>\r\n</div>\r\n<box> <boxtitel>Ansprechpartner:</boxtitel> <boxcontent><accordion><span>Leitung</span>\r\n\r\n<div>Christian Schmiedt<br />\r\nschwarze Ritter zu Augsburg<br />\r\nSingerstrasse 1<br />\r\n86159 Augsburg<br />\r\n<br />\r\nTel 0821 44 83 55 5<br />\r\nMail Ch.Schmiedt82@gmail.com</div>\r\n<span>Stellvertretung</span>\r\n\r\n<div>Johannes Aumiller<br />\r\nTel 0152 23 96 52 13</div>\r\n<span>Stellvertretung</span>\r\n\r\n<div>Sebastian Wei&szlig;</div>\r\n</accordion></boxcontent> </box></div>\r\n', '2016-11-01 23:00:09'),
(6, 'Soziale Medien', 'Soziale-Medien', '', '<style type="text/css">\r\n.auto-style1 {\r\n	text-align: center;\r\n}\r\n.auto-style2 {\r\n	text-decoration: underline;\r\n}\r\n</style>\r\n<div id="fb-root" class="auto-style1"><strong><span class="auto-style2">Hier \r\n	findest du Links zu unseren Seiten auf den Sozialenmedien auf denen du uns \r\n	folgen kannst.</span><br class="auto-style2"><br class="auto-style2"></strong></div>\r\n<table align="center" text-align="center">\r\n  <tr>\r\n    <th> Besucht uns auf Facebook\r\n    </th>\r\n    </tr>\r\n    <tr>\r\n    <td>\r\n<div>\r\n	<span class="auto-style2">Wir freuen uns auch &uuml;ber Likes f&uuml;r diese Seite.</span></strong></div>\r\n	<div class="fb-like" data-href="http://schwarze-ritter-augsburg.com" data-layout="box_count" data-action="like" data-show-faces="true" data-share="true"></div>\r\n</td></tr><tr><td>\r\n<div class="fb-page" data-href="https://www.facebook.com/Schwarze-Ritter-zu-Augsburg-799896913462812/" data-tabs="timeline" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"></div>\r\n<script>(function(d, s, id) {\r\n  var js, fjs = d.getElementsByTagName(s)[0];\r\n  if (d.getElementById(id)) return;\r\n  js = d.createElement(s); js.id = id;\r\n  js.src = "//connect.facebook.net/de_DE/sdk.js#xfbml=1&version=v2.6";\r\n  fjs.parentNode.insertBefore(js, fjs);\r\n}(document, ''script'', ''facebook-jssdk''));</script>\r\n</td></tr></table>', '2016-11-01 23:11:08'),
(7, 'Termine', 'Termine', '', '<p><iframe frameborder="0" height="600" scrolling="no" src="https://calendar.google.com/calendar/embed?showTitle=0&amp;height=600&amp;wkst=1&amp;bgcolor=%23FFFFFF&amp;\r\nsrc=sra_cal%40schwarze-ritter-augsburg.com&amp;color=%231B887A&amp;\r\nsrc=6h1fqs4om97fvrt8upgrgga1ds%40group.calendar.google.com&amp;color=%23711616&amp;\r\nsrc=j0g40fq5m45tt6i3ma30dle0fo%40group.calendar.google.com&amp;color=%235229A3&amp;ctz=Europe%2FBerlin" style="border:solid 1px #777; border-radius: 10px" width="98%"></iframe></p>\r\n', '2016-11-03 20:35:33'),
(8, 'Test_private content', 'private', 'guest,member', '<p>bla bla bla</p>\r\n', '2016-11-15 01:12:32'),
(9, 'Links', 'links', '', '<p>test1</p>\r\n\r\n<p>test2</p>\r\n', '2017-03-23 22:41:03'),
(10, 'Haftungsausschluß', 'disclaimer', '', '<titel>Haftungsausschluss</titel>\r\n<p>&nbsp;</p>\r\n\r\n<h3>Inhalt des Onlineangebotes</h3>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>Der Autor &uuml;bernimmt keinerlei Gew&auml;hr f&uuml;r die Aktualit&auml;t, Richtigkeit und Vollst&auml;ndigkeit der bereitgestellten Informationen auf unserer Website. Haftungsanspr&uuml;che gegen den Autor, welche sich auf Sch&auml;den materieller oder ideeller Art beziehen, die durch die Nutzung oder Nichtnutzung der dargebotenen Informationen bzw. durch die Nutzung fehlerhafter und unvollst&auml;ndiger Informationen verursacht wurden, sind grunds&auml;tzlich ausgeschlossen, sofern seitens des Autors kein nachweislich vors&auml;tzliches oder grob fahrl&auml;ssiges Verschulden vorliegt.<br />\r\nAlle Angebote sind freibleibend und unverbindlich. Der Autor beh&auml;lt es sich ausdr&uuml;cklich vor, Teile der Seiten oder das gesamte Angebot ohne gesonderte Ank&uuml;ndigung zu ver&auml;ndern, zu erg&auml;nzen, zu l&ouml;schen oder die Ver&ouml;ffentlichung zeitweise oder endg&uuml;ltig einzustellen.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<h3>Verweise und Links</h3>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>Bei direkten oder indirekten Verweisen auf fremde Webseiten (&ldquo;Hyperlinks&rdquo;), die au&szlig;erhalb des Verantwortungsbereiches des Autors liegen, w&uuml;rde eine Haftungsverpflichtung ausschlie&szlig;lich in dem Fall in Kraft treten, in dem der Autor von den Inhalten Kenntnis hat und es ihm technisch m&ouml;glich und zumutbar w&auml;re, die Nutzung im Falle rechtswidriger Inhalte zu verhindern.<br />\r\nDer Autor erkl&auml;rt hiermit ausdr&uuml;cklich, dass zum Zeitpunkt der Linksetzung keine illegalen Inhalte auf den zu verlinkenden Seiten erkennbar waren. Auf die aktuelle und zuk&uuml;nftige Gestaltung, die Inhalte oder die Urheberschaft der verlinkten/verkn&uuml;pften Seiten hat der Autor keinerlei Einfluss. Deshalb distanziert er sich hiermit ausdr&uuml;cklich von allen Inhalten aller verlinkten /verkn&uuml;pften Seiten, die nach der Linksetzung ver&auml;ndert wurden. Diese Feststellung gilt f&uuml;r alle innerhalb des eigenen Internetangebotes gesetzten Links und Verweise sowie f&uuml;r Fremdeintr&auml;ge in vom Autor eingerichteten G&auml;steb&uuml;chern, Diskussionsforen, Linkverzeichnissen, Mailinglisten und in allen anderen Formen von Datenbanken, auf deren Inhalt externe Schreibzugriffe m&ouml;glich sind. F&uuml;r illegale, fehlerhafte oder unvollst&auml;ndige Inhalte und insbesondere f&uuml;r Sch&auml;den, die aus der Nutzung oder Nichtnutzung solcherart dargebotener Informationen entstehen, haftet allein der Anbieter der Seite, auf welche verwiesen wurde, nicht derjenige, der &uuml;ber Links auf die jeweilige Ver&ouml;ffentlichung lediglich verweist.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<h3>Urheber- und Kennzeichenrecht</h3>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>Der Autor ist bestrebt, in allen Publikationen die Urheberrechte der verwendeten Bilder, Grafiken, Tondokumente, Videosequenzen und Texte zu beachten, von ihm selbst erstellte Bilder, Grafiken, Tondokumente, Videosequenzen und Texte zu nutzen oder auf lizenzfreie Grafiken, Tondokumente, Videosequenzen und Texte zur&uuml;ckzugreifen.<br />\r\nAlle innerhalb des Internetangebotes genannten und ggf. durch Dritte gesch&uuml;tzten Marken- und Warenzeichen unterliegen uneingeschr&auml;nkt den Bestimmungen des jeweils g&uuml;ltigen Kennzeichenrechts und den Besitzrechten der jeweiligen eingetragenen Eigent&uuml;mer. Allein aufgrund der blo&szlig;en Nennung ist nicht der Schluss zu ziehen, dass Markenzeichen nicht durch Rechte Dritter gesch&uuml;tzt sind!<br />\r\nDas Copyright f&uuml;r ver&ouml;ffentlichte, vom Autor selbst erstellte Objekte bleibt allein beim Autor der Seiten. Eine Vervielf&auml;ltigung oder Verwendung solcher Grafiken, Tondokumente, Videosequenzen und Texte in anderen elektronischen oder gedruckten Publikationen ist ohne ausdr&uuml;ckliche Zustimmung des Autors nicht gestattet.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<h3>Datenschutz</h3>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>Sofern innerhalb des Internetangebotes die M&ouml;glichkeit zur Eingabe pers&ouml;nlicher oder gesch&auml;ftlicher Daten (Emailadressen, Namen, Anschriften) besteht, so erfolgt die Preisgabe dieser Daten seitens des Nutzers auf ausdr&uuml;cklich freiwilliger Basis. Die Inanspruchnahme und Bezahlung aller angebotenen Dienste ist &ndash; soweit technisch m&ouml;glich und zumutbar &ndash; auch ohne Angabe solcher Daten bzw. unter Angabe anonymisierter Daten oder eines Pseudonyms gestattet. Die Nutzung der im Rahmen des Impressums oder vergleichbarer Angaben ver&ouml;ffentlichten Kontaktdaten wie Postanschriften, Telefon- und Faxnummern sowie Emailadressen durch Dritte zur &Uuml;bersendung von nicht ausdr&uuml;cklich angeforderten Informationen ist nicht gestattet. Rechtliche Schritte gegen die Versender von sogenannten Spam-Mails bei Verst&ouml;ssen gegen dieses Verbot sind ausdr&uuml;cklich vorbehalten.</p>\r\n\r\n<p><!-- \r\n<h3>Datenschutzerklärung für die Nutzung von Facebook-Plugins (Like-Button)</h3>\r\n<p>\r\nDiese Webseite nutzt Plugins des Anbieters Facebook.com, welche durch das Unternehmen Facebook Inc., 1601 S. California Avenue, Palo Alto, CA 94304 in den USA bereitgestellt werden. Nutzer unserer Webseite, auf der das Facebook-Plugin installiert ist, werden hiermit darauf hingewiesen, dass durch das Plugin eine Verbindung zu Facebook aufgebaut wird, wodurch eine Übermittlung an Ihren Browser durchgeführt wird, damit das Plugin auf der Webseite erscheint.<br>\r\nDes Weiteren werden durch die Nutzung Daten an die Facebook-Server weitergeleitet, welche Informationen über Ihre Webseitenbesuche auf unserer Homepage enthalten. Dies hat für eingeloggte Facebook-Nutzer zur Folge, dass die Nutzungsdaten ihrem persönlichen Facebook-Account zugeordnet werden.<br>\r\nSobald Sie als eingeloggter Facebook-Nutzer aktiv das Facebook-Plugin nutzen (z.B. durch das Klicken auf den „Gefällt mir“ Knopf oder die Nutzung der Kommentarfunktion), werden diese Daten zu Ihrem Facebook-Account übertragen und veröffentlicht. Dies können Sie nur durch vorheriges Ausloggen aus Ihrem Facebook-Account umgehen.<br>\r\nWeitere Information bezüglich der Datennutzung durch Facebook entnehmen Sie bitte den datenschutzrechtlichen Bestimmungen auf Facebook.</p>\r\n\r\n<h3>Datenschutzerklärung für die Nutzung von Twitter</h3>\r\n<p>Auf unseren Seiten sind Funktionen des Dienstes Twitter eingebunden. Diese Funktionen werden angeboten durch die Twitter Inc., 795 Folsom St., Suite 600, San Francisco, CA 94107, USA. Durch das Benutzen von Twitter und der Funktion „Re-Tweet“ werden die von Ihnen besuchten Webseiten mit Ihrem Twitter-Account verknüpft und anderen Nutzern bekannt gegeben. Dabei werden auch Daten an Twitter übertragen.</p>\r\n<p>Wir weisen darauf hin, dass wir als Anbieter der Seiten keine Kenntnis vom Inhalt der übermittelten Daten sowie deren Nutzung durch Twitter erhalten. Weitere Informationen hierzu finden Sie in der Datenschutzerklärung von Twitter unter http://twitter.com/privacy.</p>\r\n<p>Ihre Datenschutzeinstellungen bei Twitter können Sie in den Konto-Einstellungen unter http://twitter.com/account/settings ändern.</p>\r\n<p></p>\r\n\r\n<h3>Datenschutzerklärung für die Nutzung von Google +1</h3>\r\n<p><b>Erfassung und Weitergabe von Informationen:</b><br>Mithilfe der Google +1-Schaltfläche können Sie Informationen weltweit veröffentlichen. Über die Google +1-Schaltfläche erhalten Sie und andere Nutzer personalisierte Inhalte von Google und unseren Partnern. Google speichert sowohl die Information, dass Sie für einen Inhalt +1 gegeben haben, als auch Informationen über die Seite, die Sie beim Klicken auf +1 angesehen haben. Ihre +1 können als Hinweise zusammen mit Ihrem Profilnamen und Ihrem Foto in Google-Diensten, wie etwa in Suchergebnissen oder in Ihrem Google-Profil, oder an anderen Stellen auf Websites und Anzeigen im Internet eingeblendet werden. Google zeichnet Informationen über Ihre +1-Aktivitäten auf, um die Google-Dienste für Sie und andere zu verbessern.</p>\r\n<p>Um die Google +1-Schaltfläche verwenden zu können, benötigen Sie ein weltweit sichtbares, öffentliches Google-Profil, das zumindest den für das Profil gewählten Namen enthalten muss. Dieser Name wird in allen Google-Diensten verwendet. In manchen Fällen kann dieser Name auch einen anderen Namen ersetzen, den Sie beim Teilen von Inhalten über Ihr Google-Konto verwendet haben. Die Identität Ihres Google-Profils kann Nutzern angezeigt werden, die Ihre E-Mail-Adresse kennen oder über andere identifizierende Informationen von Ihnen verfügen.</p>\r\n<p><b>Verwendung der erfassten Informationen:</b><br>Neben den oben erläuterten Verwendungszwecken werden die von Ihnen bereitgestellten Informationen gemäß den geltenden Google-Datenschutzbestimmungen genutzt. Google veröffentlicht möglicherweise zusammengefasste Statistiken über die +1-Aktivitäten der Nutzer bzw. gibt diese an Nutzer und Partner weiter, wie etwa Publisher, Inserenten oder verbundene Websites.</p>\r\n\r\n<h3>Datenschutzerklärung für die Nutzung von Google Adsense</h3>\r\n<p>Diese Website benutzt Google AdSense, einen Dienst zum Einbinden von Werbeanzeigen der Google Inc. („Google“). Google AdSense verwendet sog. „Cookies“, Textdateien, die auf Ihrem Computer gespeichert werden und die eine Analyse der Benutzung der Website ermöglicht. Google AdSense verwendet auch so genannte Web Beacons (unsichtbare Grafiken). Durch diese Web Beacons können Informationen wie der Besucherverkehr auf diesen Seiten ausgewertet werden. Die durch Cookies und Web Beacons erzeugten Informationen über die Benutzung dieser Website (einschließlich Ihrer IP-Adresse) und Auslieferung von Werbeformaten werden an einen Server von Google in den USA übertragen und dort gespeichert.</p>\r\n<p>Diese Informationen können von Google an Vertragspartner von Google weiter gegeben werden. Google wird Ihre IP-Adresse jedoch nicht mit anderen von Ihnen gespeicherten Daten zusammenführen. Sie können die Installation der Cookies durch eine entsprechende Einstellung Ihrer Browser Software verhindern; wir weisen Sie jedoch darauf hin, dass Sie in diesem Fall gegebenenfalls nicht sämtliche Funktionen dieser Website voll umfänglich nutzen können. Durch die Nutzung dieser Website erklären Sie sich mit der Bearbeitung der über Sie erhobenen Daten durch Google in der zuvor beschriebenen Art und Weise und zu dem zuvor benannten Zweck einverstanden.</p>\r\n<p></p>\r\n\r\n<h3>Datenschutzerklärung für die Nutzung von Google Analytics</h3>\r\n<p>Diese Website benutzt Google Analytics, einen Webanalysedienst der Google Inc. („Google“). Google Analytics verwendet sog. „Cookies“, Textdateien, die auf Ihrem Computer gespeichert werden und die eine Analyse der Benutzung der Website durch Sie ermöglichen. Die durch den Cookie erzeugten Informationen über Ihre Benutzung dieser Website werden in der Regel an einen Server von Google in den USA übertragen und dort gespeichert.</p>\r\n<p>Im Falle der Aktivierung der IP-Anonymisierung auf dieser Webseite, wird Ihre IP-Adresse von Google jedoch innerhalb von Mitgliedstaaten der Europäischen Union oder in anderen Vertragsstaaten des Abkommens über den Europäischen Wirtschaftsraum zuvor gekürzt. Nur in Ausnahmefällen wird die volle IP-Adresse an einen Server von Google in den USA übertragen und dort gekürzt. Im Auftrag des Betreibers dieser Website wird Google diese Informationen benutzen, um Ihre Nutzung der Website auszuwerten, um Reports über die Websiteaktivitäten zusammenzustellen und um weitere mit der Websitenutzung und der Internetnutzung verbundene Dienstleistungen gegenüber dem Websitebetreiber zu erbringenDie im Rahmen von Google Analytics von Ihrem Browser übermittelte IP-Adresse wird nicht mit anderen Daten von Google zusammengeführt. </p>\r\n<p>Sie können die Speicherung der Cookies durch eine entsprechende Einstellung Ihrer Browser-Software verhindern; wir weisen Sie jedoch darauf hin, dass Sie in diesem Fall gegebenenfalls nicht sämtliche Funktionen dieser Website vollumfänglich werden nutzen können. Sie können darüber hinaus die Erfassung der durch das Cookie erzeugten und auf Ihre Nutzung der Website bezogenen Daten (inkl. Ihrer IP-Adresse) an Google sowie die Verarbeitung dieser Daten durch Google verhindern, indem sie das unter dem folgenden Link verfügbare Browser-Plugin herunterladen und installieren http://tools.google.com/dlpage/gaoptout?hl=de.</p>\r\n--><br />\r\nQuelle: <a href="http://www.haftungsausschluss.org/">Disclaimer</a> von <a href="http://www.haftungsausschluss-vorlage.de/haftungsausschluss/">Haftungsausschluss-Vorlage.de</a> und <a href="http://www.datenschutzgesetz.de/">Datenschutzgesetz.de</a> &nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;</p>\r\n', '2017-04-08 17:51:26'),
(11, 'Impressum', 'impressum', '', '<h3>Impressum</h3>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p><br />\r\nSchwarze Ritter zu Augsburg<br />\r\nc/o Christian Schmiedt<br />\r\nSingerstr. 1<br />\r\n86159 Augsburg<br />\r\n<br />\r\nTel.:<br />\r\n+49 (0)8xxx-xxxx<br />\r\n+49 (0)8xxx-xxxx<br />\r\n<br />\r\nE-Mail: Webkontakt@schwarze-ritter-augsburg.com<br />\r\n<br />\r\nInhaltlich Verantwortlicher gem&auml;&szlig; &sect; 10 Absatz 3 MDStV:<br />\r\nChristian Schmiedt</p>\r\n\r\n<p>&nbsp;</p>\r\n', '2017-04-08 17:54:22');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=787 ;

--
-- Daten für Tabelle `page_hits`
--

INSERT INTO `page_hits` (`id`, `url`, `time`, `counter`) VALUES
(1, '/?_tr_error', 1434, 9),
(2, '/?_tracy_skip_error', 14766434, 3),
(11, '/website/dashboard', 1492194508, 24),
(12, '/website/json', 1492194509, 22),
(13, '/login', 1492194509, 31),
(17, '/register', 1492194844, 3),
(20, '/Veranstalter', 1492195305, 3),
(21, '/nav/sort', 1492195318, 5),
(23, '/', 1492195322, 72),
(25, '/user', 1492195342, 1),
(32, '/gallery', 1492201936, 25),
(99, '/system/dashboard', 1492205817, 320),
(103, '/system/dashboard?_tracy_skip_error', 1492212264, 13),
(130, '/home', 1492217771, 6),
(134, '/cast', 1492218115, 7),
(169, '/system/json', 1492222376, 372),
(174, '/resource', 1492222467, 2),
(175, '/resource/edit/32', 1492222474, 2),
(176, '/role', 1492222485, 2),
(177, '/permission/edit/4', 1492222489, 3),
(374, '/links', 1492377112, 3),
(377, '/disclaimer', 1492377293, 13),
(379, '/Angebote', 1492377371, 6),
(513, '/gallery/small/Facebook', 1492382864, 1),
(768, '/media/filebrowser', 1492384366, 1),
(784, '/termine', 1492427891, 2),
(786, '/AGB', 1492427896, 5);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=173 ;

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
(150, 'Administrator', 34),
(151, 'json', 32),
(152, 'index', 35),
(153, 'json', 28),
(154, 'json', 35),
(156, 'jsonOwnerEdit', 28),
(157, 'formtest', 32),
(158, 'index', 36),
(159, 'add', 36),
(160, 'edit', 36),
(161, 'delete', 36),
(162, 'index', 37),
(163, 'getEvents', 37),
(164, 'config', 37),
(165, 'addEvent', 37),
(166, 'editEvent', 37),
(167, 'deleteEvent', 37),
(168, 'charprofile', 35),
(169, 'mailTemplatesIndex', 32),
(170, 'mailTemplate', 32),
(171, 'Guest', 34),
(172, 'Member', 34);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `resource`
--

DROP TABLE IF EXISTS `resource`;
CREATE TABLE IF NOT EXISTS `resource` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `resource_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=38 ;

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
(19, 'Nav\\Controller\\Nav'),
(20, 'Cms\\Controller\\Content'),
(22, 'Gallery\\Controller\\Gallery'),
(25, 'Cast\\Controller\\Manager'),
(26, 'Cast\\Controller\\Family'),
(27, 'Cast\\Controller\\Job'),
(28, 'Cast\\Controller\\Character'),
(29, 'Cast\\Controller\\Presentation'),
(30, 'Cast\\Controller\\Cast'),
(31, 'Media\\Controller\\FileBrowser'),
(32, 'Application\\Controller\\System'),
(33, 'Cms\\Controller\\Page'),
(34, 'Role'),
(35, 'Auth\\Controller\\Profile'),
(36, 'Cast\\Controller\\Blazon'),
(37, 'Calendar\\Controller\\Calendar');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `role`
--

DROP TABLE IF EXISTS `role`;
CREATE TABLE IF NOT EXISTS `role` (
  `rid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(45) NOT NULL,
  `role_parent` int(11) unsigned DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  PRIMARY KEY (`rid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `role`
--

INSERT INTO `role` (`rid`, `role_name`, `role_parent`, `status`) VALUES
(1, 'Guest', NULL, 'Active'),
(2, 'Role2', 1, 'Active'),
(3, 'Member', 2, 'Active'),
(4, 'Administrator', 3, 'Active');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=344 ;

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
(321, 4, 151),
(322, 4, 152),
(323, 4, 153),
(324, 4, 154),
(325, 4, 155),
(326, 4, 156),
(327, 4, 157),
(328, 4, 158),
(329, 4, 159),
(330, 4, 160),
(331, 4, 161),
(332, 4, 162),
(333, 4, 163),
(334, 4, 164),
(335, 4, 167),
(336, 4, 165),
(337, 4, 166),
(338, 4, 168),
(339, 4, 169),
(340, 4, 170),
(341, 1, 171),
(342, 2, 172),
(343, 4, 150);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `system_log`
--

DROP TABLE IF EXISTS `system_log`;
CREATE TABLE IF NOT EXISTS `system_log` (
  `type` int(11) NOT NULL,
  `msg` text COLLATE utf8_bin NOT NULL,
  `url` text COLLATE utf8_bin NOT NULL,
  `userId` int(11) DEFAULT NULL,
  `userName` text COLLATE utf8_bin,
  `microtime` float DEFAULT NULL,
  `time` bigint(20) NOT NULL,
  `data` longblob,
  UNIQUE KEY `microtime` (`microtime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle `system_log`
--

INSERT INTO `system_log` (`type`, `msg`, `url`, `userId`, `userName`, `microtime`, `time`, `data`) VALUES
(1, 'Argument 1 passed to AlbumControllerGalleryController::__construct() must be an instance of AlbumControllerGalleryService, none given, called in D:WebWebprojekteSRzAvendorzendframeworkzend-servicemanagersrcAbstractPluginManager.php on line 252 and defined', '/gallery', 2, 'FryDay', 1496780000, 1496784930, 0x7b2273657276657250485044617461223a7b2252454449524543545f4150504c49434154494f4e5f454e56223a22646576656c6f706d656e74222c2252454449524543545f535441545553223a22323030222c224150504c49434154494f4e5f454e56223a22646576656c6f706d656e74222c22485454505f484f5354223a226c6f63616c686f7374222c22485454505f434f4e4e454354494f4e223a226b6565702d616c697665222c22485454505f555047524144455f494e5345435552455f5245515545535453223a2231222c22485454505f555345525f4147454e54223a224d6f7a696c6c612f352e30202857696e646f7773204e542031302e303b2057696e36343b2078363429204170706c655765624b69742f3533372e333620284b48544d4c2c206c696b65204765636b6f29204368726f6d652f35382e302e333032392e313130205361666172692f3533372e3336222c22485454505f414343455054223a22746578742f68746d6c2c6170706c69636174696f6e2f7868746d6c2b786d6c2c6170706c69636174696f6e2f786d6c3b713d302e392c696d6167652f776562702c2a2f2a3b713d302e38222c22485454505f444e54223a2231222c22485454505f52454645524552223a22687474703a2f2f6c6f63616c686f73742f70617373776f72642f72657365742f62356663323936613737646266396432396131396362393364653039393934373f5f74726163795f736b69705f6572726f72222c22485454505f4143434550545f454e434f44494e47223a22677a69702c206465666c6174652c20736463682c206272222c22485454505f4143434550545f4c414e4755414745223a2264652d44452c64653b713d302e382c656e2d55533b713d302e362c656e3b713d302e34222c22485454505f434f4f4b4945223a22636f6f6b6965636f6e73656e745f7374617475733d6469736d6973733b2073727a61696b6e6f77796f753d313439323738303337353b205048505345535349443d65736b37646e626a7070663469613176616966307475636e6d333b207a64742d68696464656e3d303b20696f3d55704e57596c38677942495568472d6c41414141222c2250415448223a22433a5c50726f6772616d446174615c4f7261636c655c4a6176615c6a617661706174683b433a5c5065726c36345c736974655c62696e3b433a5c5065726c36345c62696e3b443a5c5765625c57656270726f6a656b74655c54455354494e475c7573627765625c7068703b433a5c50726f6772616d2046696c65732028783836295c496e74656c5c69434c5320436c69656e745c3b433a5c50726f6772616d2046696c65735c496e74656c5c69434c5320436c69656e745c3b433a5c57696e646f77735c73797374656d33323b433a5c57696e646f77733b433a5c57696e646f77735c53797374656d33325c5762656d3b433a5c57696e646f77735c53797374656d33325c57696e646f7773506f7765725368656c6c5c76312e305c3b433a5c50726f6772616d2046696c65732028783836295c496e74656c5c496e74656c285229204d616e6167656d656e7420456e67696e6520436f6d706f6e656e74735c44414c3b433a5c50726f6772616d2046696c65735c496e74656c5c496e74656c285229204d616e6167656d656e7420456e67696e6520436f6d706f6e656e74735c44414c3b433a5c50726f6772616d2046696c65732028783836295c496e74656c5c496e74656c285229204d616e6167656d656e7420456e67696e6520436f6d706f6e656e74735c4950543b433a5c50726f6772616d2046696c65735c496e74656c5c496e74656c285229204d616e6167656d656e7420456e67696e6520436f6d706f6e656e74735c4950543b433a5c50726f6772616d2046696c65732028783836295c4e564944494120436f72706f726174696f6e5c50687973585c436f6d6d6f6e3b433a5c57494e444f57535c73797374656d33323b433a5c57494e444f57533b433a5c57494e444f57535c53797374656d33325c5762656d3b433a5c57494e444f57535c53797374656d33325c57696e646f7773506f7765725368656c6c5c76312e305c3b433a5c50726f6772616d2046696c65732028783836295c436f6d6d6f6e2046696c65735c41646f62655c41474c3b443a5c50726f6772616d2046696c65732028783836295c517569636b54696d655c515453797374656d5c3b443a5c5765625c7573627765625c7068703b433a5c50726f6772616d446174615c436f6d706f73657253657475705c62696e3b433a5c50726f6772616d2046696c65735c6e6f64656a735c3b433a5c50726f6772616d2046696c65732028783836295c427261636b6574735c636f6d6d616e643b433a5c50726f6772616d2046696c65735c4d6963726f736f66742053514c205365727665725c3133305c546f6f6c735c42696e6e5c3b433a5c50726f6772616d2046696c65735c4d6963726f736f66745c57656220506c6174666f726d20496e7374616c6c65725c3b433a5c50726f6772616d2046696c65735c646f746e65745c3b433a5c50726f6772616d2046696c65732028783836295c4d6963726f736f66742053514c205365727665725c3131305c4454535c42696e6e5c3b433a5c50726f6772616d2046696c65732028783836295c4d6963726f736f66742053514c205365727665725c3132305c4454535c42696e6e5c3b433a5c50726f6772616d2046696c65732028783836295c4d6963726f736f66742053514c205365727665725c3133305c4454535c42696e6e5c3b433a5c50726f6772616d2046696c65732028783836295c426974766973652053534820436c69656e743b443a5c4861736869436f72705c56616772616e745c62696e3b443a5c50726f6772616d2046696c65735c50755454595c3b443a5c50726f6772616d2046696c65735c4769745c4769745c636d643b443a5c50726f6772616d2046696c65735c4769745c4769745c6d696e677736345c62696e3b443a5c50726f6772616d2046696c65735c4769745c4769745c7573725c62696e3b433a5c50726f6772616d2046696c65732028783836295c476f757263655c636d643b433a5c55736572735c4672795c417070446174615c526f616d696e675c436f6d706f7365725c76656e646f725c62696e3b433a5c55736572735c4672795c417070446174615c526f616d696e675c6e706d3b22443a5c50726f6772616d2046696c65735c507554545922222c2253797374656d526f6f74223a22433a5c57494e444f5753222c22434f4d53504543223a22433a5c57494e444f57535c73797374656d33325c636d642e657865222c2250415448455854223a222e434f4d3b2e4558453b2e4241543b2e434d443b2e5642533b2e5642453b2e4a533b2e4a53453b2e5753463b2e5753483b2e4d5343222c2257494e444952223a22433a5c57494e444f5753222c225345525645525f5349474e4154555245223a22222c225345525645525f534f465457415245223a224170616368652f322e342e36202857696e333229205048502f352e362e3330222c225345525645525f4e414d45223a226c6f63616c686f7374222c225345525645525f41444452223a223a3a31222c225345525645525f504f5254223a223830222c2252454d4f54455f41444452223a223a3a31222c22444f43554d454e545f524f4f54223a22443a2f5765622f57656270726f6a656b74652f53527a412f7075626c6963222c22524551554553545f534348454d45223a2268747470222c22434f4e544558545f505245464958223a22222c22434f4e544558545f444f43554d454e545f524f4f54223a22443a2f5765622f57656270726f6a656b74652f53527a412f7075626c6963222c225345525645525f41444d494e223a226d61696c406c6f63616c686f7374222c225343524950545f46494c454e414d45223a22443a2f5765622f57656270726f6a656b74652f53527a412f7075626c69632f696e6465782e706870222c2252454d4f54455f504f5254223a223536373531222c2252454449524543545f55524c223a222f67616c6c657279222c22474154455741595f494e54455246414345223a224347492f312e31222c225345525645525f50524f544f434f4c223a22485454502f312e31222c22524551554553545f4d4554484f44223a22474554222c2251554552595f535452494e47223a22222c22524551554553545f555249223a222f67616c6c657279222c225343524950545f4e414d45223a222f696e6465782e706870222c225048505f53454c46223a222f696e6465782e706870222c22524551554553545f54494d455f464c4f4154223a313439363738343933302e3235332c22524551554553545f54494d45223a313439363738343933307d7d),
(1, 'While attempting to create albumcontrollergallery(alias: AlbumControllerGallery) an invalid factory was registered for this instance type.', '/gallery', 2, 'FryDay', 1496790000, 1496784997, 0x7b2273657276657250485044617461223a7b2252454449524543545f4150504c49434154494f4e5f454e56223a22646576656c6f706d656e74222c2252454449524543545f535441545553223a22323030222c224150504c49434154494f4e5f454e56223a22646576656c6f706d656e74222c22485454505f484f5354223a226c6f63616c686f7374222c22485454505f434f4e4e454354494f4e223a226b6565702d616c697665222c22485454505f43414348455f434f4e54524f4c223a226d61782d6167653d30222c22485454505f555047524144455f494e5345435552455f5245515545535453223a2231222c22485454505f555345525f4147454e54223a224d6f7a696c6c612f352e30202857696e646f7773204e542031302e303b2057696e36343b2078363429204170706c655765624b69742f3533372e333620284b48544d4c2c206c696b65204765636b6f29204368726f6d652f35382e302e333032392e313130205361666172692f3533372e3336222c22485454505f414343455054223a22746578742f68746d6c2c6170706c69636174696f6e2f7868746d6c2b786d6c2c6170706c69636174696f6e2f786d6c3b713d302e392c696d6167652f776562702c2a2f2a3b713d302e38222c22485454505f444e54223a2231222c22485454505f52454645524552223a22687474703a2f2f6c6f63616c686f73742f70617373776f72642f72657365742f62356663323936613737646266396432396131396362393364653039393934373f5f74726163795f736b69705f6572726f72222c22485454505f4143434550545f454e434f44494e47223a22677a69702c206465666c6174652c20736463682c206272222c22485454505f4143434550545f4c414e4755414745223a2264652d44452c64653b713d302e382c656e2d55533b713d302e362c656e3b713d302e34222c22485454505f434f4f4b4945223a22636f6f6b6965636f6e73656e745f7374617475733d6469736d6973733b2073727a61696b6e6f77796f753d313439323738303337353b205048505345535349443d65736b37646e626a7070663469613176616966307475636e6d333b20696f3d55704e57596c38677942495568472d6c414141413b207a64742d68696464656e3d30222c2250415448223a22433a5c50726f6772616d446174615c4f7261636c655c4a6176615c6a617661706174683b433a5c5065726c36345c736974655c62696e3b433a5c5065726c36345c62696e3b443a5c5765625c57656270726f6a656b74655c54455354494e475c7573627765625c7068703b433a5c50726f6772616d2046696c65732028783836295c496e74656c5c69434c5320436c69656e745c3b433a5c50726f6772616d2046696c65735c496e74656c5c69434c5320436c69656e745c3b433a5c57696e646f77735c73797374656d33323b433a5c57696e646f77733b433a5c57696e646f77735c53797374656d33325c5762656d3b433a5c57696e646f77735c53797374656d33325c57696e646f7773506f7765725368656c6c5c76312e305c3b433a5c50726f6772616d2046696c65732028783836295c496e74656c5c496e74656c285229204d616e6167656d656e7420456e67696e6520436f6d706f6e656e74735c44414c3b433a5c50726f6772616d2046696c65735c496e74656c5c496e74656c285229204d616e6167656d656e7420456e67696e6520436f6d706f6e656e74735c44414c3b433a5c50726f6772616d2046696c65732028783836295c496e74656c5c496e74656c285229204d616e6167656d656e7420456e67696e6520436f6d706f6e656e74735c4950543b433a5c50726f6772616d2046696c65735c496e74656c5c496e74656c285229204d616e6167656d656e7420456e67696e6520436f6d706f6e656e74735c4950543b433a5c50726f6772616d2046696c65732028783836295c4e564944494120436f72706f726174696f6e5c50687973585c436f6d6d6f6e3b433a5c57494e444f57535c73797374656d33323b433a5c57494e444f57533b433a5c57494e444f57535c53797374656d33325c5762656d3b433a5c57494e444f57535c53797374656d33325c57696e646f7773506f7765725368656c6c5c76312e305c3b433a5c50726f6772616d2046696c65732028783836295c436f6d6d6f6e2046696c65735c41646f62655c41474c3b443a5c50726f6772616d2046696c65732028783836295c517569636b54696d655c515453797374656d5c3b443a5c5765625c7573627765625c7068703b433a5c50726f6772616d446174615c436f6d706f73657253657475705c62696e3b433a5c50726f6772616d2046696c65735c6e6f64656a735c3b433a5c50726f6772616d2046696c65732028783836295c427261636b6574735c636f6d6d616e643b433a5c50726f6772616d2046696c65735c4d6963726f736f66742053514c205365727665725c3133305c546f6f6c735c42696e6e5c3b433a5c50726f6772616d2046696c65735c4d6963726f736f66745c57656220506c6174666f726d20496e7374616c6c65725c3b433a5c50726f6772616d2046696c65735c646f746e65745c3b433a5c50726f6772616d2046696c65732028783836295c4d6963726f736f66742053514c205365727665725c3131305c4454535c42696e6e5c3b433a5c50726f6772616d2046696c65732028783836295c4d6963726f736f66742053514c205365727665725c3132305c4454535c42696e6e5c3b433a5c50726f6772616d2046696c65732028783836295c4d6963726f736f66742053514c205365727665725c3133305c4454535c42696e6e5c3b433a5c50726f6772616d2046696c65732028783836295c426974766973652053534820436c69656e743b443a5c4861736869436f72705c56616772616e745c62696e3b443a5c50726f6772616d2046696c65735c50755454595c3b443a5c50726f6772616d2046696c65735c4769745c4769745c636d643b443a5c50726f6772616d2046696c65735c4769745c4769745c6d696e677736345c62696e3b443a5c50726f6772616d2046696c65735c4769745c4769745c7573725c62696e3b433a5c50726f6772616d2046696c65732028783836295c476f757263655c636d643b433a5c55736572735c4672795c417070446174615c526f616d696e675c436f6d706f7365725c76656e646f725c62696e3b433a5c55736572735c4672795c417070446174615c526f616d696e675c6e706d3b22443a5c50726f6772616d2046696c65735c507554545922222c2253797374656d526f6f74223a22433a5c57494e444f5753222c22434f4d53504543223a22433a5c57494e444f57535c73797374656d33325c636d642e657865222c2250415448455854223a222e434f4d3b2e4558453b2e4241543b2e434d443b2e5642533b2e5642453b2e4a533b2e4a53453b2e5753463b2e5753483b2e4d5343222c2257494e444952223a22433a5c57494e444f5753222c225345525645525f5349474e4154555245223a22222c225345525645525f534f465457415245223a224170616368652f322e342e36202857696e333229205048502f352e362e3330222c225345525645525f4e414d45223a226c6f63616c686f7374222c225345525645525f41444452223a223a3a31222c225345525645525f504f5254223a223830222c2252454d4f54455f41444452223a223a3a31222c22444f43554d454e545f524f4f54223a22443a2f5765622f57656270726f6a656b74652f53527a412f7075626c6963222c22524551554553545f534348454d45223a2268747470222c22434f4e544558545f505245464958223a22222c22434f4e544558545f444f43554d454e545f524f4f54223a22443a2f5765622f57656270726f6a656b74652f53527a412f7075626c6963222c225345525645525f41444d494e223a226d61696c406c6f63616c686f7374222c225343524950545f46494c454e414d45223a22443a2f5765622f57656270726f6a656b74652f53527a412f7075626c69632f696e6465782e706870222c2252454d4f54455f504f5254223a223536383536222c2252454449524543545f55524c223a222f67616c6c657279222c22474154455741595f494e54455246414345223a224347492f312e31222c225345525645525f50524f544f434f4c223a22485454502f312e31222c22524551554553545f4d4554484f44223a22474554222c2251554552595f535452494e47223a22222c22524551554553545f555249223a222f67616c6c657279222c225343524950545f4e414d45223a222f696e6465782e706870222c225048505f53454c46223a222f696e6465782e706870222c22524551554553545f54494d455f464c4f4154223a313439363738343939362e3736362c22524551554553545f54494d45223a313439363738343939367d7d);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `titel`
--

DROP TABLE IF EXISTS `titel`;
CREATE TABLE IF NOT EXISTS `titel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `text` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role_id` int(10) unsigned DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_on` bigint(20) NOT NULL,
  `modified_on` bigint(20) NOT NULL,
  `street` text,
  `city` text,
  `zip` mediumint(9) DEFAULT NULL,
  `member_number` text,
  `real_surename` text,
  `real_name` text,
  `birthday` bigint(20) DEFAULT NULL,
  `gender` text,
  `user_image` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `email`, `name`, `password`, `role_id`, `status`, `created_on`, `modified_on`, `street`, `city`, `zip`, `member_number`, `real_surename`, `real_name`, `birthday`, `gender`, `user_image`) VALUES
(1, 'salt@salt.de', 'salt', '', 4, 1, 152151515113, 1497301711, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'm', './data/_users/salt/profileImage.jpg'),
(2, 'fryday@example.com', 'FryDay', '8b20508657fb4d3b457198e94c02ac916c72ce02', 4, 1, 67665564, 1497351931, 'Eringerstrasse', 'München', 80689, 'MN-G-02', 'Sonntag', 'Christoph', 1980, 'm', NULL),
(3, 'boluuuu@gmail.com', 'stefan', 'ed8da3d7f461715ddeb6f9217613904b1d98d4fb', 3, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'testuser@example.com', 'Test', 'test', 2, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'testuser2@example.com', 'Test2', '37c7419816bc29749747704c96b655f2f7ba6d74', 3, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'Test3@exaple.com', 'NaneTest', '', 2, 1, 0, 1497301117, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'm', 'D:\\Web\\Webprojekte\\SRzA\\Data\\_users\\NaneTest/profileImage.jpg'),
(7, 'Test4@exaple.com', 'ChristophTest', '', 2, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 'Test5@exaple.com', 'LaraTest', '', 2, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 'Test6@exaple.com', 'BenTest', '', 2, 1, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 'BaBo@example.com', 'Basti', '', 3, 0, 345435, 1497307958, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'm', NULL);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
