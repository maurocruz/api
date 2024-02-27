
-- THING

CREATE TABLE IF NOT EXISTS `thing` (
  `idthing` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `alternateName` VARCHAR(255) NULL,
  `type` VARCHAR(45) NOT NULL,
  `additionalType` VARCHAR(255) NULL,
  `description` TEXT NULL,
  `disambiguatingDescription` TEXT NULL,
  `url` VARCHAR(255) NULL,
  `dateCreated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dateModified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idthing`)
) ENGINE = InnoDB;

-- PROPERTY VALUE

CREATE TABLE IF NOT EXISTS `propertyValue` (
  `idpropertyValue` int NOT NULL AUTO_INCREMENT,
  `thing` INT NOT NULL,
  `name` varchar(45) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`idpropertyValue`,`thing`)
) ENGINE=InnoDB;

-- IMAGEOBJECT

CREATE TABLE IF NOT EXISTS `imageObject` (
  `idimageObject` INT NOT NULL AUTO_INCREMENT,
  `contentUrl` varchar(255) NOT NULL,
  `contentSize` varchar(45) DEFAULT NULL,
  `width` INT DEFAULT NULL,
  `height` INT DEFAULT NULL,
  `encodingFormat` varchar(45) DEFAULT NULL,
  `author` INT DEFAULT NULL,
  `license` varchar(180) DEFAULT NULL,
  `acquireLicensePage` varchar(180) DEFAULT NULL,
  `uploadDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `thumbnail` varchar(255) DEFAULT NULL,
  `keywords` varchar(125) DEFAULT NULL,
  `copyright` varchar(180) DEFAULT NULL,
  PRIMARY KEY (`idimageObject`)
) ENGINE=InnoDB;

-- THING HAS IMAGEOBJECT

CREATE TABLE IF NOT EXISTS `thing_has_imageObject` (
  `idthing` INT NOT NULL,
  `idimageObject` INT NOT NULL,
  `position` INT DEFAULT NULL ,
  `representativeOfPage` TINYINT DEFAULT NULL ,
  `caption` TEXT,
  PRIMARY KEY (`idthing`, `idimageObject`),
  KEY `idx_1` (`representativeOfPage`),
  CONSTRAINT `FK_thing_has_imageObject_imageObject` FOREIGN KEY (`idimageObject`) REFERENCES `imageObject` (`idimageObject`) ON DELETE CASCADE,
  CONSTRAINT `FK_thing_has_imageObject_thing` FOREIGN KEY (`idthing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;

--  TRIGGER

DELIMITER $$
CREATE TRIGGER `thing_has_imageObject_BEFORE_INSERT` BEFORE INSERT ON `thing_has_imageObject` FOR EACH ROW
BEGIN
  DECLARE count INT;
  SET count = (SELECT COUNT(*) FROM `thing_has_imageObject` WHERE `idthing`=NEW.`idthing`);
  IF NEW.`position`='' OR NEW.`position` IS NULL
  THEN SET NEW.`position`= count+1;
  END IF;
END;
DELIMITER ;