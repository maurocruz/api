--
-- IMAGE OBJECT
--
CREATE TABLE IF NOT EXISTS `imageObject` (
  `idimageObject` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `creativeWork` INT UNSIGNED NOT NULL,
  `mediaObject` INT UNSIGNED NOT NULL,
  `thing` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`idimageObject`, `mediaObject`, `thing`),
  INDEX `fk_imageObject_thing_idx` (`thing`),
  INDEX `fk_imageObject_creativeWork_idx` (`creativeWork`),
  INDEX `fk_imageObject_mediaObject_idx` (`mediaObject`),
  CONSTRAINT `fk_imageObject_creativeWork` FOREIGN KEY (`creativeWork`)  REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE,
  CONSTRAINT `fk_imageObject_mediaObject` FOREIGN KEY (`mediaObject`) REFERENCES `mediaObject` (`idmediaObject`) ON DELETE CASCADE,
  CONSTRAINT `fk_imageObject_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;
