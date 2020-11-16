
--
-- CREATED TABLE person
-- -- address
-- -- contactPoint
--

CREATE TABLE IF NOT EXISTS `person` (
  `idperson` INT(10) NOT NULL AUTO_INCREMENT,
  `idplace` INT(10) NULL DEFAULT NULL,
  `name` VARCHAR(255) NULL,
  `givenName` VARCHAR(120) NOT NULL,
  `familyName` VARCHAR(120) NULL DEFAULT NULL,
  `additionalName` VARCHAR(45) NULL DEFAULT NULL,
  `taxId` VARCHAR(64) NULL DEFAULT NULL,
  `birthDate` DATE NULL DEFAULT NULL,
  `birthPlace` VARCHAR(45) NULL DEFAULT NULL,
  `gender` VARCHAR(45) NULL DEFAULT NULL,
  `hasOccupation` VARCHAR(45) NULL DEFAULT NULL,
  `url` VARCHAR(64) NULL DEFAULT NULL,
  `address` INT(10) NULL,
  `dateRegistration` DATE NOT NULL,
  `dateModified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idperson`),
  INDEX `fk_person_1_idx` (`address` ASC),
  CONSTRAINT `fk_person_1` FOREIGN KEY (`address`) REFERENCES `postalAddress` (`idpostalAddress`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE = InnoDB;

--
-- relational table with contactPoint
--
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
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8;

DROP TRIGGER IF EXISTS `person_has_imageObject_BEFORE_INSERT`;
DELIMITER $$
CREATE DEFINER = CURRENT_USER TRIGGER `person_has_imageObject_BEFORE_INSERT` BEFORE INSERT ON `person_has_imageObject` FOR EACH ROW
BEGIN
    DECLARE count INT;
    SET count = (SELECT COUNT(*) FROM `person_has_imageObject` WHERE `idperson`=NEW.`idperson`);
    IF NEW.`position`='' OR NEW.`position` IS NULL
    THEN SET NEW.`position`= count+1;
    END IF;
END$$
DELIMITER ;
