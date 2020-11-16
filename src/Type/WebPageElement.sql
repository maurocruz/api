--
-- CREATE TABLE webPageElement
-- -- propertyValue
-- -- imageObject

CREATE TABLE IF NOT EXISTS `webPageElement` (
  `idwebPageElement` INT NOT NULL AUTO_INCREMENT,
  `idwebPage` INT NOT NULL DEFAULT '0',
  `name` VARCHAR(255) NULL DEFAULT NULL,
  `text` TEXT NULL DEFAULT NULL,
  `position` TINYINT UNSIGNED NULL DEFAULT NULL,
  `author` VARCHAR(50) NULL DEFAULT NULL,
  `dateCreated` DATETIME NOT NULL,
  `dateModified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idwebPageElement`),
  INDEX `FK_webPageElement_webPage` (`idwebPage`),
  CONSTRAINT `fk_webPageElement_webPage1` FOREIGN KEY (`idwebPage`) REFERENCES `webPage` (`idwebPage`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS `webPageElement_has_propertyValue` (
  `idwebPageElement` INT NOT NULL,
  `idpropertyValue` INT NOT NULL,
  PRIMARY KEY (`idwebPageElement`, `idpropertyValue`),
  CONSTRAINT `fk_webPageElement_has_propertyValue_webPageElement1` FOREIGN KEY (`idwebPageElement`) REFERENCES `webPageElement` (`idwebPageElement`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_webPageElement_has_propertyValue_propertyValue1` FOREIGN KEY (`idpropertyValue`) REFERENCES `propertyValue` (`idpropertyValue`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS `webPageElement_has_imageObject` (
  `idwebPageElement` INT NOT NULL,
  `idimageObject` INT NOT NULL,
  `width` FLOAT UNSIGNED NOT NULL DEFAULT '1',
  `height` FLOAT NULL DEFAULT NULL,
  `href` VARCHAR(255) NULL DEFAULT NULL,
  `caption` VARCHAR(255) NULL DEFAULT NULL,
  `position` INT UNSIGNED NULL DEFAULT NULL,
  `representativeOfPage` TINYINT NULL DEFAULT NULL,
  PRIMARY KEY (`idwebPageElement`, `idimageObject`),
  CONSTRAINT `fk_webPageElement_has_imageObject_1` FOREIGN KEY (`idwebPageElement`) REFERENCES `webPageElement` (`idwebPageElement`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_webPageElement_has_imageObject_2` FOREIGN KEY (`idimageObject`) REFERENCES `imageObject` (`idimageObject`) ON DELETE CASCADE  ON UPDATE NO ACTION
) ENGINE = InnoDB;


DROP TRIGGER IF EXISTS `webPageElement_has_imageObject_BEFORE_INSERT`;
DELIMITER $$
CREATE DEFINER = CURRENT_USER TRIGGER `webPageElement_has_imageObject_BEFORE_INSERT` BEFORE INSERT ON `webPageElement_has_imageObject` FOR EACH ROW BEGIN
    DECLARE count INT;
    SET count = (SELECT COUNT(*) FROM `webPageElement_has_imageObject` WHERE `idwebPageElement`=NEW.`idwebPageElement`);
    IF NEW.`position`='' OR NEW.`position` IS NULL
        THEN SET NEW.`position`= count+1;
    END IF;
END$$
DELIMITER ;
