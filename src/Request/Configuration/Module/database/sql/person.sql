
-- PERSON

CREATE TABLE IF NOT EXISTS `person` (
  `idperson` INT NOT NULL AUTO_INCREMENT,
  `thing` INT NOT NULL,
  `givenName` VARCHAR(120) DEFAULT NULL,
  `familyName` VARCHAR(120) DEFAULT NULL,
  `additionalName` VARCHAR(45) DEFAULT NULL,
  `taxId` VARCHAR(64) DEFAULT NULL,
  `birthDate` DATE DEFAULT NULL,
  `deathDate` DATE DEFAULT NULL,
  `birthPlace` VARCHAR(45) DEFAULT NULL,
  `gender` VARCHAR(45) DEFAULT NULL,
  `hasOccupation` VARCHAR(45) DEFAULT NULL,
  `homeLocation` INT NULL,
  `dateCreated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dateModified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idperson`,`thing`),
  CONSTRAINT `fk_person_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;

/*CREATE TABLE IF NOT EXISTS `person_has_contactPoint` (
  `idperson` INT NOT NULL,
  `idcontactPoint` INT UNSIGNED NOT NULL,
  `position` INT UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`idperson`,`idcontactPoint`),
  KEY `fk_person_has_contactPoint_contactPoint_idx` (`idcontactPoint`),
  KEY `fk_person_has_contactPoint_person_idx` (`idperson`),
  CONSTRAINT `fk_person_has_contactPoint_contactPoint1` FOREIGN KEY (`idcontactPoint`) REFERENCES `contactPoint` (`idcontactPoint`) ON DELETE CASCADE,
  CONSTRAINT `fk_person_has_contactPoint_person` FOREIGN KEY (`idperson`) REFERENCES `person` (`idperson`) ON DELETE CASCADE
) ENGINE=InnoDB;*/
