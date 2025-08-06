--
-- COLLECTION
--
CREATE TABLE IF NOT EXISTS `collection` (
  `idcollection` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `creativeWork` INT UNSIGNED NOT NULL,
  `thing` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`idcollection`,`creativeWork`,`thing`),
  INDEX `fk_collection_thing_idx` (`thing`),
  INDEX `fk_collection_creativeWork_idx` (`creativeWork`),
  CONSTRAINT `fk_collection_creativeWork` FOREIGN KEY (`creativeWork`)  REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE,
  CONSTRAINT `fk_collection_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;
