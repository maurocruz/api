
--
-- CREATE TABLE videoObject
--

CREATE TABLE IF NOT EXISTS `videoObject` (
  `idvideoObject` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `tag` varchar(84) DEFAULT NULL,
  `url` varchar(255) NOT NULL DEFAULT '',
  `thumbnail` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `contentUrl` varchar(255) DEFAULT NULL,
  `bitrate` decimal(6,2) DEFAULT NULL,
  `duration` time DEFAULT NULL,
  `position` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `uploadDate` datetime DEFAULT NULL,
  PRIMARY KEY (`idvideoObject`),
  UNIQUE KEY `unico` (`position`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
