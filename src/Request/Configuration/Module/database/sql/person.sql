
-- PERSON

CREATE TABLE IF NOT EXISTS `person` (
  `idperson` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thing` INT UNSIGNED NOT NULL,
  `givenName` VARCHAR(120) DEFAULT NULL,
  `familyName` VARCHAR(120) DEFAULT NULL,
  `additionalName` VARCHAR(45) DEFAULT NULL,
  `taxId` VARCHAR(64) DEFAULT NULL,
  `birthDate` DATE DEFAULT NULL,
  `deathDate` DATE DEFAULT NULL,
  `birthPlace` VARCHAR(45) DEFAULT NULL,
  `gender` VARCHAR(45) DEFAULT NULL,
  `hasOccupation` VARCHAR(45) DEFAULT NULL,
  `homeLocation` INT UNSIGNED NULL,
  PRIMARY KEY (`idperson`,`thing`),
  KEY (`givenName`,`familyName`),
  CONSTRAINT `fk_person_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;

