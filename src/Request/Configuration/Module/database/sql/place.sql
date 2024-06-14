
-- PLACE

CREATE TABLE IF NOT EXISTS `place` (
  `idplace` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thing` INT UNSIGNED NOT NULL,
  `address` INT UNSIGNED DEFAULT NULL,
  `elevation` VARCHAR(125) DEFAULT NULL,
  `latitude` DECIMAL(18,14) DEFAULT NULL,
  `longitude` DECIMAL(18,14) DEFAULT NULL,
  `publicAccess` TINYINT DEFAULT 0,
  PRIMARY KEY (`idplace`,`thing`),
  KEY `fk_place_address_idx` (`address`),
  CONSTRAINT `fk_place_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE,
  CONSTRAINT `fk_place_address` FOREIGN KEY (`address`) REFERENCES `postalAddress` (`idpostalAddress`) ON DELETE SET NULL
) ENGINE = InnoDB;


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
