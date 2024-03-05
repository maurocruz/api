
-- PLACE

CREATE TABLE IF NOT EXISTS `place` (
  `idplace` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thing` INT UNSIGNED NOT NULL,
  `elevation` VARCHAR(125) DEFAULT NULL,
  `latitude` DECIMAL(18,14) DEFAULT NULL,
  `longitude` DECIMAL(18,14) DEFAULT NULL,
  `publicAccess` TINYINT DEFAULT 0,
  PRIMARY KEY (`idplace`,`thing`),
  CONSTRAINT `fk_place_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;
