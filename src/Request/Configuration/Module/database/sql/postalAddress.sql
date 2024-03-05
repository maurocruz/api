
CREATE TABLE IF NOT EXISTS `postalAddress` (
  `idpostalAddress` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thing` INT UNSIGNED NOT NULL,
  `streetAddress` VARCHAR(255) DEFAULT NULL,
  `addressLocality` VARCHAR(80) DEFAULT NULL,
  `addressRegion` VARCHAR(45) DEFAULT NULL,
  `addressCountry` VARCHAR(45) DEFAULT NULL,
  `postalCode` VARCHAR(45) DEFAULT NULL,
  PRIMARY KEY (`idpostalAddress`,`thing`),
  KEY (`addressLocality`),
  CONSTRAINT `fk_postalAddress_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;