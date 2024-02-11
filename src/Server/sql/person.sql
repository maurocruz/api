--
-- CREATED TABLES
-- -- person
-- -- contactPoint
--

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
  `address` INT NULL,
  `dateCreated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dateModified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idperson`)
) ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS `person_has_contactPoint` (
  `idperson` INT(10) NOT NULL,
  `idcontactPoint` INT(10) NOT NULL,
  `position` INT(3) NULL DEFAULT NULL,
  PRIMARY KEY (`idperson`, `idcontactPoint`),
  INDEX `fk_person_has_contactPoint_contactPoint1_idx` (`idcontactPoint` ASC),
  INDEX `fk_person_has_contactPoint_person_idx` (`idperson` ASC),
  CONSTRAINT `fk_person_has_contactPoint_contactPoint1` FOREIGN KEY (`idcontactPoint`) REFERENCES `contactPoint` (`idcontactPoint`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_person_has_contactPoint_person` FOREIGN KEY (`idperson`) REFERENCES `person` (`idperson`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8;

--
-- relational table with imageObject

CREATE TABLE IF NOT EXISTS `person_has_imageObject` (
  `idperson` INT(10) NOT NULL,
  `idimageObject` INT(10) NOT NULL,
  `position` INT(5) NULL,
  `caption` VARCHAR(255) NULL,
  `representativeOfPage` TINYINT(1) NULL,
  PRIMARY KEY (`idperson`, `idimageObject`),
  INDEX `fk_person_has_imageObject_imageObject1_idx` (`idimageObject` ASC),
  INDEX `fk_person_has_imageObject_person1_idx` (`idperson` ASC),
  CONSTRAINT `fk_person_has_imageObject_person1` FOREIGN KEY (`idperson`) REFERENCES `person` (`idperson`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_person_has_imageObject_imageObject1` FOREIGN KEY (`idimageObject`) REFERENCES `imageObject` (`idimageObject`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB;

DROP TRIGGER IF EXISTS `person_has_imageObject_BEFORE_INSERT`;

CREATE TRIGGER `person_has_imageObject_BEFORE_INSERT` BEFORE INSERT ON `person_has_imageObject` FOR EACH ROW
BEGIN
    DECLARE count INT;
    SET count = (SELECT COUNT(*) FROM `person_has_imageObject` WHERE `idperson`=NEW.`idperson`);
    IF NEW.`position`='' OR NEW.`position` IS NULL
        THEN SET NEW.`position`= count+1;
    END IF;
END;
