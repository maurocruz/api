
--
-- CREATE TABLE webPage
-- --
-- -- propertyValue

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


CREATE TABLE `webPage_has_propertyValue` (
  `idwebPage` INT(10) NOT NULL,
  `idpropertyValue` INT(10) NOT NULL,
  PRIMARY KEY (`idwebPage`, `idpropertyValue`),
  INDEX `fk_webPage_has_attributes_2_idx` (`idpropertyValue`),
  CONSTRAINT `fk_webPage_has_propertyValue_webPage1` FOREIGN KEY (`idwebPage`) REFERENCES `webPage` (`idwebPage`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_webPage_has_propertyValue_propertyValue1` FOREIGN KEY (`idpropertyValue`) REFERENCES `propertyValue` (`idpropertyValue`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB;
