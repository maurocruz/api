
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
