
-- CONTACT POINT

CREATE TABLE IF NOT EXISTS `contactPoint` (
  `idcontactPoint` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thing` INT UNSIGNED NOT NULL,
  `contactOption` VARCHAR(100) NULL DEFAULT NULL,
  `contactType` VARCHAR(160) NULL DEFAULT NULL,
  `telephone` VARCHAR(45) NULL DEFAULT NULL,
  `email` VARCHAR(120) NULL DEFAULT NULL,
  PRIMARY KEY (`idcontactPoint`, `thing`),
  INDEX `fk_contactPoint_thing_idx` (`thing`),
  CONSTRAINT `fk_contactPoint_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;
