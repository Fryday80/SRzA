-- phpMyAdmin SQL Dump
-- version 4.0.4.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 19. Jan 2017 um 10:27
-- Server Version: 5.6.13
-- PHP-Version: 5.4.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+01:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `frytest`
--
CREATE DATABASE IF NOT EXISTS `DB2836034` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `DB2836034`;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `abonnements`
--

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

CREATE TABLE IF NOT EXISTS `abonnementtypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `abonnement_id` int(11) NOT NULL,
  `name` text COLLATE utf8_bin NOT NULL,
  `monthCost` decimal(10,0) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `album`
--

CREATE TABLE IF NOT EXISTS `album` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `timestamp` bigint(20) DEFAULT NULL,
  `duration` int(11) NOT NULL DEFAULT '3',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `album`
--

INSERT INTO `album` (`id`, `event`, `timestamp`, `duration`) VALUES
(1, 'Testevent_1', 70457457, 5),
(2, 'Testevent_2', 2016, 3),
(3, 'Testevent_3', 456770, 3),
(4, 'Testevent_4', 40564, 3);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `albumimages`
--

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

CREATE TABLE IF NOT EXISTS `albums` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `folder` varchar(30) COLLATE utf8_bin NOT NULL,
  `event` text COLLATE utf8_bin,
  `timestamp` bigint(20) DEFAULT NULL,
  `preview_pic` text COLLATE utf8_bin,
  `visibility` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `albums`
--

INSERT INTO `albums` (`id`, `folder`, `event`, `timestamp`, `preview_pic`, `visibility`) VALUES
(1, '2016', 'eventtext', 1480546800, '', 1),
(2, 'folder 2', 'event 2', 557577678, 'test.jpg', 1),
(3, 'filder 3', 'event 3', 596934000, 'gibts.ned', 0),
(4, 'func test_folder', 'assaas', 0, '', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `families`
--

CREATE TABLE IF NOT EXISTS `families` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hashjob`
--

CREATE TABLE IF NOT EXISTS `hashjob` (
  `job_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `images`
--

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

CREATE TABLE IF NOT EXISTS `job` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `multiabo`
--

CREATE TABLE IF NOT EXISTS `multiabo` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `nav`
--

CREATE TABLE IF NOT EXISTS `nav` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL,
  `label` tinytext COLLATE utf8_bin NOT NULL,
  `uri` tinytext COLLATE utf8_bin,
  `permission_id` int(11) NOT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=21 ;

--
-- Daten für Tabelle `nav`
--

INSERT INTO `nav` (`id`, `menu_id`, `label`, `uri`, `permission_id`, `lft`, `rgt`) VALUES
(1, 0, 'Users and Rights', '/user', 67, 11, 14),
(2, 0, 'Resources', '/resource', 62, 26, 27),
(3, 0, 'Permissions', '/permission', 58, 24, 25),
(4, 0, 'Users', '/user', 67, 12, 13),
(5, 0, 'Gallery Edit', '/album', 88, 10, 15),
(7, 0, 'Home', '/', 1, 1, 2),
(8, 0, 'Navigation', '/nav/sort', 73, 20, 21),
(9, 0, 'Administration', '/#', 69, 9, 16),
(10, 0, 'Roles', '/role', 54, 22, 23),
(11, 0, 'Content', '/cms', 69, 18, 19),
(12, 0, 'Für Veranstalter', '/Veranstalter', 1, 6, 7),
(13, 0, 'AGBs', '/AGB', 1, 7, 8),
(14, 0, 'Gallery', '/gallery', 81, 3, 4),
(15, 0, 'Webmaster', '/#', 69, 17, 28),
(16, 0, 'Angebote', '/Angebote', 117, 5, 8),
(17, 0, 'Soziale Medien', '/Soziale-Medien', 85, 35, 36),
(18, 0, 'Termine', '/termine', 85, 3, 4),
(19, 0, 'User Manager', '/usermanager', 86, 13, 14),
(20, 0, 'Home', 'Home', 1, 1, 2);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` tinytext NOT NULL,
  `url` tinytext NOT NULL,
  `exceptedRoles` text NOT NULL,
  `content` mediumtext NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Daten für Tabelle `pages`
--

INSERT INTO `pages` (`id`, `title`, `url`, `exceptedRoles`, `content`, `updated`) VALUES
(1, 'Wilkommen', 'Home', '', '<p>Unsere Seite befindet sich gerade im Aufbau<br />\r\nSchauen Sie sich gerne um<br />\r\n<br />\r\nWir stellen ein Raubritterlager dar. Die Gruppe besteht aktuell aus &uuml;ber 20 Mitgliedern + Kindern.<br />\r\nWir versuchen ein sch&ouml;nes belebtes Lager auf ca. 3 bis 4 Events im Jahr aufzustellen<br />\r\nsowie als Gruppe M&auml;rkte usw. zu Besuchen, machen jedoch kein Reenacment, der Spa&szlig; am Hobby hat Vorrang.<br />\r\nWir bieten Workshops, Training f&uuml;r Show und Freikampf und einen lustigen Haufen.<br />\r\nBei Interesse bitte eine Mail an uns.<br />\r\nWir freuen uns &uuml;ber Anfragen von Einzelpersonen sowie ganzen Familien, Anf&auml;ngern und alten Hasen gleicherma&szlig;en.<br />\r\nWir sind auch auf&nbsp;<a href="https://www.facebook.com/Schwarze-Ritter-zu-Augsburg-799896913462812/" target="_blank">Facebook</a>&nbsp;zu erreichen ...</p>\r\n\r\n<p>&nbsp;</p>\r\n', '2016-09-23 16:45:48'),
(3, 'Für Veranstalter', 'Veranstalter', '', '<p>Sehr geehrte Damen und Herren,<br />\r\n<br />\r\nliebe Veranstalter und Organisatoren,<br />\r\nwir bilden momentan ein Gruppe aus 30 Erwachsenen und 8 Kindern, die unser Lager mit ca. 15 Schlaf-, Schau- und Gruppenzelten beleben, unsere Mitglieder haben teilweise &uuml;ber 10 Jahre Erfahrung im Mittelalter was sich in unseren Shows, Animationen und dem gesamten Lager widerspiegelt.<br />\r\n<br />\r\nUnser gesamt Angebot umfasst:<br />\r\nbelebtes Lager mit Handarbeit und Handwerk<br />\r\nKinderspiele gegen Spende<br />\r\noffenes Waffenzelt, zum Infomieren und anfassen<br />\r\nFeldk&uuml;che, nach mittelalterlichen Rezepten<br />\r\nFeldlazarett<br />\r\nPranger<br />\r\noffene Schauzelte<br />\r\nKleidungs- und Darstellungszeitraum ca. 1350-1400 (unter bewusster Aussparung der Waffenklasse Armbrust)<br />\r\nDa wir auch Kleinkinder im Lager haben, ist es nicht m&ouml;glich jedes Zelt als Schauzelt zu &ouml;ffnen.<br />\r\n<br />\r\n<br />\r\nInnerhalb unseres Lagers bieten wir Waffenvorf&uuml;hrungen und bei Platz und Witterung auch Feuerspiel an. Am Pranger und im Feldlazarett werden regelm&auml;&szlig;ig kleine Vorf&uuml;hrungen gemacht und Besucher dadurch animiert.<br />\r\n<br />\r\nGegen vertragliche Regelung sind wir auf Verhandlungsbasis auch f&uuml;r Shows-und Animationen au&szlig;erhalb des Lagers buchbar.<br />\r\n<br />\r\nAuf dem beigef&uuml;gtem Aufstellungsplan ist die derzeitige Idealaufstellung unseres Lagers zu erkennen sowie die gesamt Gr&ouml;&szlig;e und die entsprechend ben&ouml;tigte Fl&auml;che ersichtlich.<br />\r\nDa alle Mitglieder unserer Gruppe das mittelalterliche Lagerleben als Hobby neben Beruf und Familie machen, ist unsere gesamte Ausr&uuml;stung aus privaten Mitteln bestritten die von allen Mitgliedern f&uuml;r die Gruppe zusammengetragen wurden.<br />\r\nAlleine die vorhandenen Zelte bilden hier eine Summe von &uuml;ber 15 000,- Euro.<br />\r\n<br />\r\nIn dieser Aufstellung k&ouml;nnen wir unser gesamtes Repertoire aufbauen und zur Schau stellen.<br />\r\nDie Zelte und Fl&auml;chen sind umstellbar jedoch ben&ouml;tigen wir eine m&ouml;glichst ebene und rechteckige zusammenh&auml;ngende Fl&auml;che.<br />\r\nUnsere Zelte werden normal mit Seilen und Heringen abgespannt, f&uuml;r den Aufbau ben&ouml;tigen wir entsprechenden Untergrund.<br />\r\nDie Feuerstellen sind mindestens 20 cm &uuml;ber dem Boden, wir verf&uuml;gen &uuml;ber die entsprechenden L&ouml;schmittel.Auf Wunsch k&ouml;nnen wir zus&auml;tzlich zu unseren Feuerstellen die wir normal dabei haben auch Feuerk&ouml;rbe mit aufstellen.<br />\r\n<br />\r\nUnser Lager wird mit Seilen abgesperrt und hat i.d.R. zwei Ein und Ausg&auml;nge zum Hauptweg hin, die Bereiche mit den Feuern werden nochmals absperrt.<br />\r\nDie Zelte d&uuml;rfen von au&szlig;en betrachtet werden, ggf. darf durch den ge&ouml;ffneten Eingang hineingesehen werden, sie d&uuml;rfen aber NICHT BETRETEN werden.</p>\r\n', '2016-09-24 00:13:39'),
(4, 'AGBs', 'AGB', '', '<p>Unsere AGBs:&nbsp;<br />\r\n<br />\r\nAllgemeine Gesch&auml;ftsbedingungen der schwarzen Ritter zu Augsburg f&uuml;r die Teilnahme an Veranstaltungen<br />\r\n<br />\r\nDie schwarzen Ritter zu Augsburg verpflichten sich entsprechend den in der Vereinbarung getroffenen Punkten nach besten M&ouml;glichkeiten und Gegebenheiten zum Erfolg der Veranstaltung beizutragen. Die schwarzen Ritter zu Augsburg versorgen sich auf der Veranstaltung selbst. Sind vom Veranstalter Essens &ndash; und/oder Getr&auml;nkegutscheine vorgesehen, ist die Summe des Gegenwerts oder eine anderweitig verhandelte Summe sp&auml;testens einen Monat vor der Veranstaltung vom Veranstalter auf das Konto der Gruppe zu &uuml;berweisen. Weitere Anspruch von Seiten der SRZA bestehen, falls nicht anders Vereinbart, nicht.<br />\r\nAlle Einnahmen der SRZA die durch Spenden von Besuchern entstehen verbleiben im vollen bei der Gruppe. Ausnahme sind Veranstaltungen mit wohlt&auml;tigen oder karikativen Charakter.<br />\r\nDie SRZA sind, wenn nicht schriftlich anderweitig Vereinbart, in vollem Umfang &uuml;ber den Veranstalter versichert. Sollten die eigenen Versicherungen der SRZA beansprucht werden, haften diese nicht f&uuml;r Sch&auml;den die durch Fehler Dritter oder schadhafte oder fehlerhafte Gegenst&auml;nde, die zu Verf&uuml;gung gestellt werden ( z.B. Absperrungen, B&uuml;hnen, B&uuml;hnenaufbauten usw.) herbeigef&uuml;hrt werden und sind nicht den SRZA und deren Mitgliedern anzulasten.<br />\r\nDie SRZA verpflichten sich M&uuml;ll in die entsprechenden Beh&auml;ltnisse die vom Veranstalter zu Verf&uuml;gung gestellt werden auf dem Gel&auml;nde zu entsorgen.&nbsp;<br />\r\nDie SRZA verpflichten sich den zur Verf&uuml;gung gestellten Lagerplatz den Gegebenheiten entsprechend zu verlassen.&nbsp;<br />\r\nDie SRZA halten g&auml;ngige Feuerl&ouml;scher f&uuml;r jede betriebene Feuerstelle im Lager bereit, weitere geforderte L&ouml;schmittel sind vom Veranstalter zu stellen.&nbsp;<br />\r\nDie SRZA halten eine 24 STD. Bewachung ihres Lagers durch eigene Mitglieder vor, dies umfasst auch die Kontrolle der Feuerstellen im Lager.&nbsp;<br />\r\nGruppen-. Security- und Organisationsteamsfremde Personen d&uuml;rfen im Sinne des &quot;Hausrechts&quot; aus dem Lager (seilumgrenzter Bereich) verwiesen werden. Gruppenfremden Personen ist das Betreten der Zelte nicht gestattet.&nbsp;<br />\r\nShowauftritte und andere Buchungen die der Veranstalter t&auml;tigt, jedoch nicht mindestens zwei Monate vor Veranstaltung wieder absagt wenn er diese nicht ben&ouml;tigt sind regul&auml;r zu bezahlen.&nbsp;<br />\r\nAlle sonstigen Aufwandsentsch&auml;digungen sind einen Monat vor beginn der Veranstaltung, bei Aufwandsentsch&auml;digung aus Gewinn sp&auml;testens einen Monat nach der Veranstaltung auf das Konto der SRZA zu &uuml;berweisen.&nbsp;<br />\r\nWenn nicht anders schriftlich Vereinbart haben die Mitglieder der SRZA ihre Hunde, f&uuml;r die entsprechende Versicherungen vorliegen, unter Beachtung der Hygiene usw. auf dem Event bei sich. Auf- und Abbau Zeiten sind vom Veranstalter wie den SRZA einzuhalten, sollten diese vom Veranstalter kurzfristig ge&auml;ndert werden, k&ouml;nnen die SRZA entstehende Kosten beim Veranstalter geltend machen.&nbsp;<br />\r\nDie SRZA n&auml;chtigen entsprechend Jahreszeit ( April bis Oktober) in den von ihnen aufgestellten Zelten, sollte ein Veranstalter dies nicht w&uuml;nschen oder es au&szlig;erhalb des Zeitraumes April bis Oktober fallen, hat er auf seine kosten eine entsprechende L&ouml;sung anzubieten.&nbsp;<br />\r\nF&uuml;r alle Vereinbarungen gilt deutsches Recht, Gerichtsstand ist Augsburg, Bayern.&nbsp;<br />\r\nSollten einzelne Bestimmungen dieses Vertrages unwirksam oder undurchf&uuml;hrbar sein oder nach Vertragsschluss unwirksam oder undurchf&uuml;hrbar werden, bleibt davon die Wirksamkeit des Vertrages im &Uuml;brigen unber&uuml;hrt. An die Stelle der unwirksamen oder undurchf&uuml;hrbaren Bestimmung soll diejenige wirksame und durchf&uuml;hrbare Regelung treten, deren Wirkungen der wirtschaftlichen Zielsetzung am n&auml;chsten kommen, die die Vertragsparteien mit der unwirksamen bzw. undurchf&uuml;hrbaren Bestimmung verfolgt haben. Die vorstehenden Bestimmungen gelten entsprechend f&uuml;r den Fall, dass sich der Vertrag als l&uuml;ckenhaft erweist.&nbsp;</p>\r\n', '2016-09-24 00:13:54'),
(5, 'Unsere Angebote', 'Angebote', '', '<p>Leider ist es einigen Veranstaltern nicht m&ouml;glich aus platztechnischen, finanziellen oder anderen organisatorischen Gr&uuml;nden unsere stetig wachsende Gruppe im Komplettpaket auf ihrem Event unterzubringen. Daher bieten wir verschiedene L&ouml;sungen an. Die einzelnen Bedingungen sind auch noch im Vorfeld verhandelbar, m&uuml;ssen dann aber schriftlich festgehalten werden.&nbsp;</p>\r\n\r\n<p><br />\r\n<strong><u>Grundvoraussetzungen Leistung Veranstalter&nbsp;</u></strong><br />\r\n<strong>&bull; Feuerholz &amp; Wasser&nbsp;<br />\r\n&bull; Sanit&auml;re Anlagen 24 Std. ge&ouml;ffnet&nbsp;<br />\r\n&bull; Sonstige Voraussetzungen gegeben&nbsp;</strong></p>\r\n\r\n<p><br />\r\n<strong><u>Angebot 1&nbsp;</u></strong><br />\r\nLeistung Veranstalter&nbsp;<br />\r\nGrundvoraussetzungen&nbsp;</p>\r\n\r\n<p><br />\r\n<u>Leistung schwarze Ritter zu Augsburg&nbsp;</u><br />\r\nTeilnehmerzahl entsprechend R&uuml;ckmeldung&nbsp;<br />\r\nZelte entsprechend Teilnehmerzahl&nbsp;<br />\r\nFeuerstelle &amp; Dreibein, Tische&nbsp;<br />\r\nB&auml;nke &amp; Plane&nbsp;<br />\r\nSpiele gegen Spende&nbsp;<br />\r\n<br />\r\n<strong><u>Angebot 2&nbsp;</u></strong><br />\r\nLeistung Veranstalter&nbsp;<br />\r\nGrundvoraussetzungen +&nbsp;<br />\r\nStrom&nbsp;<br />\r\nAufwandsentsch&auml;digung VB&nbsp;</p>\r\n\r\n<p><br />\r\n<u>Leistung schwarze Ritter zu Augsburg&nbsp;</u><br />\r\nTeilnehmerzahl entsprechend R&uuml;ckmeldung&nbsp;<br />\r\nZelte entsprechend Teilnehmerzahl&nbsp;<br />\r\nFeuerstelle &amp; Dreibein, Tische&nbsp;<br />\r\nB&auml;nke &amp; Plane&nbsp;<br />\r\nSpiele gegen Spende&nbsp;<br />\r\nGe&ouml;ffnete Schauzelte entsprechend Teilnehmerzahl&nbsp;<br />\r\nWaffenzelt&nbsp;<br />\r\nPranger&nbsp;<br />\r\nAnimationen von Besuchern&nbsp;<br />\r\n<br />\r\n<u><strong>Angebot 3&nbsp;</strong></u><br />\r\nLeistung Veranstalter&nbsp;<br />\r\nGrundvoraussetzungen +&nbsp;<br />\r\nStrom&nbsp;<br />\r\nAufwandsentsch&auml;digung Anfahrt &amp; Abfahrt&nbsp;<br />\r\n5,- Euro p. Person/Tag (nicht f&uuml;r Kinder)&nbsp;</p>\r\n\r\n<p><br />\r\n<u>Leistung schwarze Ritter zu Augsburg&nbsp;</u><br />\r\nTeilnehmerzahl entsprechend R&uuml;ckmeldung ( min. 15 ohne Kinder)&nbsp;<br />\r\nZelte entsprechend Teilnehmerzahl&nbsp;<br />\r\nFeuerstelle&nbsp;<br />\r\nSpiele gegen Spende&nbsp;<br />\r\nGe&ouml;ffnete Schauzelte entsprechend Teilnehmerzahl&nbsp;<br />\r\nWaffenzelt&nbsp;<br />\r\nPranger&nbsp;<br />\r\nTafel&nbsp;<br />\r\nFeldlazarett&nbsp;<br />\r\nShowk&uuml;che&nbsp;<br />\r\nAnimationen von Besuchern im Lager&nbsp;</p>\r\n\r\n<p><br />\r\n<strong><u>Angebot 4&nbsp;</u></strong><br />\r\nLeistung Veranstalter&nbsp;<br />\r\nGrundvoraussetzungen +&nbsp;<br />\r\nStrom&nbsp;<br />\r\nAufwandsentsch&auml;digung Anfahrt &amp; Abfahrt&nbsp;<br />\r\n6,50 Euro p. Person/Tag (nicht f&uuml;r Kinder) &bull;&nbsp;<br />\r\n<br />\r\n<u>Leistung schwarze Ritter zu Augsburg&nbsp;</u><br />\r\nTeilnehmerzahl entsprechend R&uuml;ckmeldung (min. 20 ohne Kinder)&nbsp;<br />\r\nZelte entsprechend Teilnehmerzahl&nbsp;<br />\r\nFeuerstelle&nbsp;<br />\r\nSpiele gegen Spende&nbsp;<br />\r\nGe&ouml;ffnete Schauzelte entsprechend Teilnehmerzahl&nbsp;<br />\r\nWaffenzelt&nbsp;<br />\r\nPranger&nbsp;<br />\r\nTafel&nbsp;<br />\r\nFeldlazarett&nbsp;<br />\r\nShowk&uuml;che: gro&szlig;e Feldk&uuml;che&nbsp;<br />\r\nFeuershow im Lager&nbsp;<br />\r\nK&auml;mpfe im Lager&nbsp;<br />\r\nAnimationen von Besuchern im Lager&nbsp;</p>\r\n\r\n<hr />\r\n<p><br />\r\n<strong>Unabh&auml;ngig der Angebote sind f&uuml;r das Gel&auml;nde Feuershows und Kampfshows Buchbar&nbsp;</strong><br />\r\n<br />\r\nHierf&uuml;r erheben wir eine Geb&uuml;hr f&uuml;r Versicherungen und Aufwand von&nbsp;<br />\r\n<br />\r\nFeuershow 50,-&nbsp;<br />\r\n<br />\r\nKampfshow 50,-&nbsp;<br />\r\n<br />\r\nje Show.&nbsp;<br />\r\n<br />\r\nBei den Shows sind L&auml;nge und Besonderheiten vereinbar, m&uuml;ssen jedoch sp&auml;testens sechs Monate vor dem jeweiligen Event fest Gebucht und schriftlich Vereinbart werden.&nbsp;<br />\r\n<br />\r\nF&uuml;r Freik&auml;mpfe und Freischlachten sowie Turnire bitte die gesonderten Hinweise beachten.&nbsp;<br />\r\n<br />\r\nWir sind gerne bereit Pakete und Angebote entsprechend zu bearbeiten, jedoch k&ouml;nnen wir f&uuml;r die von uns zu bringenden Leistungen von den Forderungen an die Veranstalter nur bedingt abweichen.&nbsp;<br />\r\nF&uuml;r eine Buchung unsere Gruppe halten wir ein entsprechendes Formular vor, welches auf Anfrage gerne verschickt wird.&nbsp;</p>\r\n\r\n<hr />\r\n<p>Ansprechpartner:&nbsp;<br />\r\n<br />\r\nLeitung&nbsp;<br />\r\nChristian Schmiedt&nbsp;<br />\r\nschwarze Ritter zu Augsburg&nbsp;<br />\r\nSingerstrasse 1&nbsp;<br />\r\n86159 Augsburg&nbsp;<br />\r\n<br />\r\nTel 0821 44 83 55 5&nbsp;<br />\r\nMail Ch.Schmiedt82@gmail.com&nbsp;</p>\r\n\r\n<hr />\r\n<p>Stellvertreter&nbsp;</p>\r\n\r\n<p>Johannes Aumiller&nbsp;<br />\r\n<br />\r\nTel 0152 23 96 52 13&nbsp;</p>\r\n\r\n<hr />\r\n<p>Stellvertreter&nbsp;</p>\r\n\r\n<p>Sebastian Wei&szlig;&nbsp;<br />\r\n<br />\r\nTel 0151 21 52 07 41&nbsp;</p>\r\n', '2016-11-01 23:00:09'),
(6, 'Soziale Medien', 'Soziale-Medien', '', '<style type="text/css">\r\n.auto-style1 {\r\n	text-align: center;\r\n}\r\n.auto-style2 {\r\n	text-decoration: underline;\r\n}\r\n</style>\r\n<div id="fb-root" class="auto-style1"><strong><span class="auto-style2">Hier \r\n	findest du Links zu unseren Seiten auf den Sozialenmedien auf denen du uns \r\n	folgen kannst.</span><br class="auto-style2"><br class="auto-style2"></strong></div>\r\n<table align="center" text-align="center">\r\n  <tr>\r\n    <th> Besucht uns auf Facebook\r\n    </th>\r\n    </tr>\r\n    <tr>\r\n    <td>\r\n<div>\r\n	<span class="auto-style2">Wir freuen uns auch &uuml;ber Likes f&uuml;r diese Seite.</span></strong></div>\r\n	<div class="fb-like" data-href="http://schwarze-ritter-augsburg.com" data-layout="box_count" data-action="like" data-show-faces="true" data-share="true"></div>\r\n</td></tr><tr><td>\r\n<div class="fb-page" data-href="https://www.facebook.com/Schwarze-Ritter-zu-Augsburg-799896913462812/" data-tabs="timeline" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"></div>\r\n<script>(function(d, s, id) {\r\n  var js, fjs = d.getElementsByTagName(s)[0];\r\n  if (d.getElementById(id)) return;\r\n  js = d.createElement(s); js.id = id;\r\n  js.src = "//connect.facebook.net/de_DE/sdk.js#xfbml=1&version=v2.6";\r\n  fjs.parentNode.insertBefore(js, fjs);\r\n}(document, ''script'', ''facebook-jssdk''));</script>\r\n</td></tr></table>', '2016-11-01 23:11:08'),
(7, 'Termine', 'Termine', '', '<p><iframe frameborder="0" height="600" scrolling="no" src="https://calendar.google.com/calendar/embed?showTitle=0&amp;height=600&amp;wkst=1&amp;bgcolor=%23FFFFFF&amp;\r\nsrc=sra_cal%40schwarze-ritter-augsburg.com&amp;color=%231B887A&amp;\r\nsrc=6h1fqs4om97fvrt8upgrgga1ds%40group.calendar.google.com&amp;color=%23711616&amp;\r\nsrc=j0g40fq5m45tt6i3ma30dle0fo%40group.calendar.google.com&amp;color=%235229A3&amp;ctz=Europe%2FBerlin" style="border:solid 1px #777; border-radius: 10px" width="98%"></iframe></p>\r\n', '2016-11-03 20:35:33'),
(8, 'Test_private content', 'private', 'guest,member', '<p>bla bla bla</p>\r\n', '2016-11-15 01:12:32');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `permission`
--

CREATE TABLE IF NOT EXISTS `permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `permission_name` varchar(45) NOT NULL,
  `resource_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=118 ;

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
(89, 'index', 23),
(91, 'add', 23),
(93, 'edit', 23),
(94, 'showprofile', 23),
(95, 'delete', 23),
(96, 'edit', 17),
(97, 'add', 17),
(98, 'full', 22),
(99, 'small', 22),
(114, 'edit', 1),
(115, 'add', 1),
(116, 'delete', 1),
(117, 'onlyGuests', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `resource`
--

CREATE TABLE IF NOT EXISTS `resource` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `resource_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

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
(23, 'Usermanager\\Controller\\Usermanager');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `role`
--

CREATE TABLE IF NOT EXISTS `role` (
  `rid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(45) NOT NULL,
  `role_parent` int(11) DEFAULT NULL,
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
(4, 'Administrator', NULL, 'Active');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `role_permission`
--

CREATE TABLE IF NOT EXISTS `role_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL,
  `permission_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=278 ;

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
(277, 4, 117);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `titel`
--

CREATE TABLE IF NOT EXISTS `titel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `text` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_role`
--

CREATE TABLE IF NOT EXISTS `user_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Daten für Tabelle `user_role`
--

INSERT INTO `user_role` (`id`, `user_id`, `role_id`) VALUES
(1, 1, 4),
(2, 2, 4),
(3, 3, 3);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(100) NOT NULL,
  `status` enum('Y','N') NOT NULL DEFAULT 'Y',
  `created_on` bigint(20) NOT NULL,
  `modified_on` bigint(20) NOT NULL,
  `family_id` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `vita` text NOT NULL,
  `realfirstname` text NOT NULL,
  `realname` text NOT NULL,
  `showname` text NOT NULL,
  `showsurename` text NOT NULL,
  `gender` enum('m','f') NOT NULL,
  `membernumber` int(11) NOT NULL,
  `street` text NOT NULL,
  `zip` int(11) NOT NULL,
  `city` text NOT NULL,
  `birthday` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `email`, `name`, `password`, `status`, `created_on`, `modified_on`, `family_id`, `order`, `vita`, `realfirstname`, `realname`, `showname`, `showsurename`, `gender`, `membernumber`, `street`, `zip`, `city`, `birthday`) VALUES
(1, 'salt@salt.de', 'salt', '88f716c2b137f811a8ebe9ea10a7867d7b9e7622', 'Y', 152151515113, 20161008200020, 0, 0, 'jhfjgfgfghgh  fgfhgnhf h ghfjgf', 'Christoph', 'Sonntag', 'Christoph', 'von Leym zu Munichen', 'm', 2, 'hkjhk', 80689, 'München', 0),
(2, 'fryday@example.com', 'FryDay', '8b20508657fb4d3b457198e94c02ac916c72ce02', 'Y', 67665564, 20161008200401, 0, 0, 'stzsghxghgfn  fxgxg  gxxf', 'gnn', '0gnxgn', '0nhghnh', '0ncnncn', 'm', 544, 'gn', 56866, 'gfxngx', 303453431),
(3, 'example.3@example.com', 'example', 'd7d833534a39afbac08ec536bed7ae9eeac45638', 'Y', 0, 20160927025802, 0, 0, '0', '0', '0', '0', '0', '', 0, '', 0, '', 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;