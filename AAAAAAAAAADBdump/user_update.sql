
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(100) NOT NULL,
  `sure_name` tinytext NOT NULL,
  `gender` tinyint(1) NOT NULL,
  `vita` text NOT NULL,
  `family_id` int(11) NOT NULL,
  `family_order` int(11) NOT NULL,
  `status` enum('Y','N') NOT NULL DEFAULT 'Y',
  `created_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `email`, `name`, `password`, `sure_name`, `gender`, `vita`, `family_id`, `family_order`, `status`, `created_on`, `modified_on`) VALUES
(1, 'salt@salt.de', 'salt', '88f716c2b137f811a8ebe9ea10a7867d7b9e7622', '', 0, '', 0, 0, 'Y', '0000-00-00 00:00:00', '2016-10-08 18:00:20'),
(2, 'fryday@example.com', 'FryDay', '8b20508657fb4d3b457198e94c02ac916c72ce02', '', 0, '', 0, 0, 'Y', '0000-00-00 00:00:00', '2016-10-08 18:04:01'),
(3, 'example.3@example.com', 'example', 'd7d833534a39afbac08ec536bed7ae9eeac45638', '', 0, '', 0, 0, 'Y', '0000-00-00 00:00:00', '2016-11-17 17:23:56');

