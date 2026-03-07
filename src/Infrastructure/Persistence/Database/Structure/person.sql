-- PERSON
CREATE TABLE IF NOT EXISTS `person` (
  `idperson` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thing` INT UNSIGNED NOT NULL,
  `additionalName` VARCHAR(45) NULL DEFAULT NULL,
  `address` INT UNSIGNED NULL DEFAULT NULL,
  `birthDate` DATE NULL DEFAULT NULL,
  `birthPlace` VARCHAR(45) NULL DEFAULT NULL,
  `deathDate` DATE NULL DEFAULT NULL,
  `deathPlace` VARCHAR(45) NULL DEFAULT NULL,
  `familyName` VARCHAR(120) NULL DEFAULT NULL,
  `hasOccupation` VARCHAR(45) NULL DEFAULT NULL,
  `homeLocation` INT UNSIGNED NULL DEFAULT NULL,
  `gender` VARCHAR(45) NULL DEFAULT NULL,
  `givenName` VARCHAR(120) NOT NULL,
  PRIMARY KEY (`idperson`, `thing`),
  INDEX `fk_person_thing_idx` (`thing`),
  INDEX `fk_person_postalAddress_idx` (`address`),
  INDEX `fk_person_homeLocation_idx` (`homeLocation`),
  CONSTRAINT `fk_person_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE,
  CONSTRAINT `fk_person_address` FOREIGN KEY (`address`) REFERENCES `postalAddress` (`idpostalAddress`) ON DELETE SET NULL,
  CONSTRAINT `fk_person_homeLocation` FOREIGN KEY (`homeLocation`) REFERENCES `place` (`idplace`) ON DELETE SET NULL
) ENGINE = InnoDB;
