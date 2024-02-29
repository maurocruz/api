
-- CONTACT POINT

CREATE TABLE IF NOT EXISTS `contactPoint` (
  `idcontactPoint` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thing` INT UNSIGNED NOT NULL,
  `contactType` VARCHAR(160) DEFAULT NULL,
  `contactOption` VARCHAR(100) DEFAULT NULL,
  `email` VARCHAR(120) DEFAULT NULL,
  `telephone` VARCHAR(45) DEFAULT NULL,
  `position` INT UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`idcontactPoint`,`thing`),
  KEY (`contactType`),
  CONSTRAINT `fk_contactPoint_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `postalAddress` (
  `idpostalAddress` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `contactPoint` INT UNSIGNED NOT NULL,
  `streetAddress` VARCHAR(255) DEFAULT NULL,
  `addressLocality` VARCHAR(80) DEFAULT NULL,
  `addressRegion` VARCHAR(45) DEFAULT NULL,
  `addressCountry` VARCHAR(45) DEFAULT NULL,
  `postalCode` VARCHAR(45) DEFAULT NULL,
  PRIMARY KEY (`idpostalAddress`,'contactPoint'),
  KEY (`addressLocality`),
  CONSTRAINT `fk_postalAddress_contactPoint` FOREIGN KEY (`contactPoint`) REFERENCES `contactPoint` (`idcontactPoint`) ON DELETE CASCADE
) ENGINE=InnoDB;
