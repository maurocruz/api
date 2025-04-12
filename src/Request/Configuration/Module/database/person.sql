
-- PERSON
CREATE TABLE IF NOT EXISTS `person` (
  `idperson` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thing` INT UNSIGNED NOT NULL,
  `givenName` VARCHAR(120) NOT NULL,
  `familyName` VARCHAR(120) NULL DEFAULT NULL,
  `additionalName` VARCHAR(45) NULL DEFAULT NULL,
  `taxId` VARCHAR(64) NULL DEFAULT NULL,
  `birthDate` DATE NULL DEFAULT NULL,
  `birthPlace` VARCHAR(45) NULL DEFAULT NULL,
  `gender` VARCHAR(45) NULL DEFAULT NULL,
  `hasOccupation` VARCHAR(45) NULL DEFAULT NULL,
  `homeLocation` INT UNSIGNED NULL DEFAULT NULL,
  `deathDate` DATE NULL DEFAULT NULL,
  `deathPlace` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`idperson`, `thing`),
  INDEX `fk_person_thing_idx` (`thing` ASC) VISIBLE,
  CONSTRAINT `fk_person_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `person` (
  `idperson` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thing` INT UNSIGNED NOT NULL,
  `givenName` VARCHAR(120) DEFAULT NULL,
  `familyName` VARCHAR(120) DEFAULT NULL,
  `additionalName` VARCHAR(45) DEFAULT NULL,
  `taxId` VARCHAR(64) DEFAULT NULL,
  `birthDate` DATE DEFAULT NULL,
  `birthPlace` VARCHAR(45) DEFAULT NULL,
  `deathDate` DATE DEFAULT NULL,
  `deathPlace` VARCHAR(45) DEFAULT NULL,
  `gender` VARCHAR(45) DEFAULT NULL,
  `hasOccupation` VARCHAR(255) DEFAULT NULL,
  `homeLocation` INT UNSIGNED NULL,
  PRIMARY KEY (`idperson`,`thing`),
  KEY (`givenName`,`familyName`),
  CONSTRAINT `fk_person_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;


