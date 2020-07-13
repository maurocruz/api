
--
-- CREATE TABLE webPage
-- 

CREATE TABLE `webPage` (
  `idwebPage` int(10) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `alternativeHeadline` varchar(50) DEFAULT NULL,
  `showtitle` tinyint(1) DEFAULT NULL,
  `showdescription` tinyint(1) DEFAULT NULL,
  `breadcrumb` text,
  `jsonwebpage` json DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idwebPage`)
) ENGINE=InnoDB;
