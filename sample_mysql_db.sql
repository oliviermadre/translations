CREATE TABLE IF NOT EXISTS `translations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `iso2` varchar(2) NOT NULL,
  PRIMARY KEY (`id`,`iso2`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=101 ;

--
-- Contenu de la table `translations`
--

INSERT INTO `translations` (`id`, `name`, `description`, `iso2`) VALUES
(100, 'indien', 'inde en allemand', 'de'),
(100, 'inde', '', 'fr'),
(100, 'india', '', 'es');