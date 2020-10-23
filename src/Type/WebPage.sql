
--
-- CREATE TABLE webPage
-- --
-- -- propertyValue
CREATE TABLE `webPage` (
  `idwebPage` int NOT NULL AUTO_INCREMENT,
  `url` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1 NOT NULL,
  `alternativeHeadline` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `showtitle` tinyint(1) DEFAULT NULL,
  `showdescription` tinyint(1) DEFAULT NULL,
  `breadcrumb` text CHARACTER SET latin1,
  `jsonwebpage` json DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idwebPage`),
  KEY `date_modified` (`dateModified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `webPage_has_propertyValue` (
  `idwebPage` INT(10) NOT NULL,
  `idpropertyValue` INT(10) NOT NULL,
  PRIMARY KEY (`idwebPage`, `idpropertyValue`),
  INDEX `fk_webPage_has_propertyValue_2_idx` (`idpropertyValue`),
  CONSTRAINT `fk_webPage_has_propertyValue_webPage1` FOREIGN KEY (`idwebPage`) REFERENCES `webPage` (`idwebPage`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_webPage_has_propertyValue_propertyValue1` FOREIGN KEY (`idpropertyValue`) REFERENCES `propertyValue` (`idpropertyValue`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB;
