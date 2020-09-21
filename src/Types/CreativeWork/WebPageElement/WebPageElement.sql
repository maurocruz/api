--
-- CREATE TABLE webPageElement
-- -- propertyValue
-- -- imageObject

CREATE TABLE `webPageElement` (
  `idwebPageElement` INT(10) NOT NULL AUTO_INCREMENT,
  `idwebPage` INT(10) NOT NULL DEFAULT '0',
  `name` VARCHAR(255) NULL DEFAULT NULL,
  `text` TEXT NULL DEFAULT NULL,
  `position` TINYINT(3) UNSIGNED NULL DEFAULT NULL,
  `author` VARCHAR(50) NULL DEFAULT NULL,
  `dateCreated` DATETIME NOT NULL,
  `dateModified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idwebPageElement`),
  INDEX `FK_webPageElement_webPage` (`idwebPage`),
  CONSTRAINT `fk_webPageElement_webPage1` FOREIGN KEY (`idwebPage`) REFERENCES `webPage` (`idwebPage`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB;


CREATE TABLE `webPageElement_has_propertyValue` (
  `idwebPageElement` INT(10) NOT NULL,
  `idpropertyValue` INT(10) NOT NULL,
  PRIMARY KEY (`idwebPageElement`, `idpropertyValue`),
  CONSTRAINT `fk_webPageElement_has_propertyValue_webPageElement1` FOREIGN KEY (`idwebPageElement`) REFERENCES `webPageElement` (`idwebPageElement`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_webPageElement_has_propertyValue_propertyValue1` FOREIGN KEY (`idpropertyValue`) REFERENCES `propertyValue` (`idpropertyValue`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB;


CREATE TABLE `webPageElement_has_imageObject` (
  `idwebPageElement` INT(10) NOT NULL,
  `idimageObject` INT(10) NOT NULL,
  `width` FLOAT UNSIGNED NOT NULL DEFAULT '1',
  `height` FLOAT NULL DEFAULT NULL,
  `href` VARCHAR(255) NULL DEFAULT NULL,
  `caption` VARCHAR(255) NULL DEFAULT NULL,
  `position` INT(4) UNSIGNED NULL DEFAULT NULL,
  `representativeOfPage` TINYINT(1) NULL DEFAULT NULL,
  PRIMARY KEY (`idwebPageElement`, `idimageObject`),
  CONSTRAINT `fk_webPageElement_has_imageObject_1` FOREIGN KEY (`idwebPageElement`) REFERENCES `webPageElement` (`idwebPageElement`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_webPageElement_has_imageObject_2` FOREIGN KEY (`idimageObject`) REFERENCES `imageObject` (`idimageObject`) ON DELETE CASCADE  ON UPDATE NO ACTION
) ENGINE = InnoDB;
