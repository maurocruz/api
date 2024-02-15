/*
 Schema sql for plinct system
 tables: user, thing, person, place, contactPoint, address, imageObject
 */

--
-- CREATE TABLE user
--
CREATE TABLE IF NOT EXISTS `user` (
  `iduser` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `dateCreated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dateModified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`iduser`),
  UNIQUE INDEX `email` (`email`)
) ENGINE = InnoDB;

-- PASSWORD RESET
CREATE TABLE IF NOT EXISTS `passwordReset` (
  `iduser` INT NOT NULL,
  `selector` VARCHAR(16) NULL DEFAULT NULL,
  `token` VARCHAR(64) NULL DEFAULT NULL,
  `expires` DATETIME NULL DEFAULT NULL,
  INDEX `fk_passwordReset_1_idx` (`iduser`),
  CONSTRAINT `fk_passwordReset_1` FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`) ON DELETE CASCADE
) ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS `user_history` (
  `iduser_history` INT NOT NULL AUTO_INCREMENT,
  `iduser` INT DEFAULT NULL,
  `method` varchar(6) NOT NULL,
  `summary` varchar(255) DEFAULT NULL,
  `targetTable` varchar(45) NOT NULL,
  `targetId` int NOT NULL,
  PRIMARY KEY (`iduser_history`),
  KEY `fk_user_history_user_idx` (`iduser`),
  CONSTRAINT `fk_user_history_user` FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `user_privileges` (
  `iduser_privileges` int NOT NULL AUTO_INCREMENT,
  `iduser` INT NOT NULL,
  `function` INT unsigned NOT NULL DEFAULT '1',
  `actions` char(4) NOT NULL DEFAULT 'r',
  `namespace` varchar(100) NOT NULL DEFAULT '',
  `userCreator` int DEFAULT NULL,
  PRIMARY KEY (`iduser_privileges`,`iduser`),
  UNIQUE KEY `unique` (`function`,`iduser`,`namespace`,`actions`),
  KEY `fk_user_privileges_user_idx` (`iduser`),
  CONSTRAINT `fk_user_privileges_user` FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`) ON UPDATE RESTRICT
) ENGINE=InnoDB;


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


-- CONTACT POINT

CREATE TABLE IF NOT EXISTS `contactPoint` (
  `idcontactPoint` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(180) DEFAULT NULL,
  `contactType` VARCHAR(160) DEFAULT NULL,
  `telephone` VARCHAR(45) DEFAULT NULL,
  `email` VARCHAR(120) DEFAULT NULL,
  `whatsapp` TINYINT DEFAULT NULL,
  `obs` TEXT,
  `position` INT DEFAULT NULL,
  PRIMARY KEY (`idcontactPoint`)
) ENGINE=InnoDB;

-- PERSON

CREATE TABLE IF NOT EXISTS `person` (
  `idperson` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) DEFAULT NULL,
  `givenName` VARCHAR(120) NOT NULL,
  `familyName` VARCHAR(120) DEFAULT NULL,
  `additionalName` VARCHAR(45) DEFAULT NULL,
  `taxId` VARCHAR(64) DEFAULT NULL,
  `birthDate` DATE DEFAULT NULL,
  `birthPlace` VARCHAR(45) DEFAULT NULL,
  `gender` VARCHAR(45) DEFAULT NULL,
  `hasOccupation` VARCHAR(45) DEFAULT NULL,
  `url` VARCHAR(64) DEFAULT NULL,
  `homeLocation` INT NULL,
  `dateCreated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dateModified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idperson`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `person_has_imageObject` (
  `idperson` INT NOT NULL,
  `idimageObject` INT NOT NULL,
  `position` INT DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `representativeOfPage` tinyint DEFAULT NULL,
  PRIMARY KEY (`idperson`,`idimageObject`),
  KEY `fk_person_has_imageObject_imageObject1_idx` (`idimageObject`),
  KEY `fk_person_has_imageObject_person1_idx` (`idperson`),
  CONSTRAINT `fk_person_has_imageObject_imageObject1` FOREIGN KEY (`idimageObject`) REFERENCES `imageObject` (`idimageObject`),
  CONSTRAINT `fk_person_has_imageObject_person1` FOREIGN KEY (`idperson`) REFERENCES `person` (`idperson`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `person_has_contactPoint` (
  `idperson` INT NOT NULL,
  `idcontactPoint` INT UNSIGNED NOT NULL,
  `position` INT UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`idperson`,`idcontactPoint`),
  KEY `fk_person_has_contactPoint_contactPoint_idx` (`idcontactPoint`),
  KEY `fk_person_has_contactPoint_person_idx` (`idperson`),
  CONSTRAINT `fk_person_has_contactPoint_contactPoint1` FOREIGN KEY (`idcontactPoint`) REFERENCES `contactPoint` (`idcontactPoint`) ON DELETE CASCADE,
  CONSTRAINT `fk_person_has_contactPoint_person` FOREIGN KEY (`idperson`) REFERENCES `person` (`idperson`) ON DELETE CASCADE
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS `postalAddress` (
  `idpostalAddress` INT NOT NULL AUTO_INCREMENT,
  `streetAddress` varchar(255) DEFAULT NULL,
  `addressLocality` varchar(80) DEFAULT NULL,
  `addressRegion` varchar(45) DEFAULT NULL,
  `addressCountry` varchar(45) DEFAULT NULL,
  `postalCode` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idpostalAddress`)
) ENGINE=InnoDB;


-- PLACE

CREATE TABLE IF NOT EXISTS `place` (
  `idplace` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` text,
  `disambiguatingDescription` text,
  `additionalType` text,
  `latitude` decimal(18,14) DEFAULT NULL,
  `longitude` decimal(18,14) DEFAULT NULL,
  `elevation` varchar(125) DEFAULT NULL,
  `address` INT DEFAULT NULL,
  `rank` INT unsigned NOT NULL DEFAULT '1000',
  `keywords` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `dateCreated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dateModified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idplace`),
  KEY `fk_place_1_idx` (`address`),
  CONSTRAINT `fk_place_address` FOREIGN KEY (`address`) REFERENCES `postalAddress` (`idpostalAddress`) ON DELETE CASCADE
) ENGINE=InnoDB;
