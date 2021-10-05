--
-- CREATE TABLE webPage
-- --
-- -- propertyValue

CREATE TABLE IF NOT EXISTS `webPage` (
  `idwebPage` INT NOT NULL AUTO_INCREMENT,
  `isPartOf` INT NULL,
  `url` VARCHAR(255) NULL DEFAULT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `alternativeHeadline` VARCHAR(50) NULL DEFAULT NULL,
  `showtitle` TINYINT(1) NULL DEFAULT NULL,
  `showdescription` TINYINT(1) NULL DEFAULT NULL,
  `breadcrumb` JSON NULL DEFAULT NULL,
  `dateCreated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dateModified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idwebPage`),
  INDEX `fk_webPage_webSite1_idx` (`isPartOf` ASC),
  CONSTRAINT `fk_webPage_webSite1` FOREIGN KEY (`isPartOf`) REFERENCES `webSite` (`idwebSite`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `webPage_has_propertyValue` (
  `idwebPage` INT(10) NOT NULL,
  `idpropertyValue` INT(10) NOT NULL,
  PRIMARY KEY (`idwebPage`, `idpropertyValue`),
  INDEX `fk_webPage_has_propertyValue_2_idx` (`idpropertyValue`),
  CONSTRAINT `fk_webPage_has_propertyValue_webPage1` FOREIGN KEY (`idwebPage`) REFERENCES `webPage` (`idwebPage`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_webPage_has_propertyValue_propertyValue1` FOREIGN KEY (`idpropertyValue`) REFERENCES `propertyValue` (`idpropertyValue`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB;
