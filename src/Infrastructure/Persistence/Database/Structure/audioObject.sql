--
-- AUDIO OBJECT
--
CREATE TABLE IF NOT EXISTS `audioObject` (
  `idaudioObject` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `creativeWork` INT UNSIGNED NOT NULL,
  `mediaObject` INT UNSIGNED NOT NULL,
  `thing` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`idaudioObject`,`creativeWork`,`mediaObject`,`thing`),
  INDEX `fk_audioObject_thing_idx` (`thing`),
  INDEX `fk_audioObject_creativeWork_idx` (`creativeWork`),
  INDEX `fk_audioObject_mediaObject_idx` (`mediaObject`),
  CONSTRAINT `fk_audioObject_creativeWork` FOREIGN KEY (`creativeWork`)  REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE,
  CONSTRAINT `fk_audioObject_mediaObject` FOREIGN KEY (`mediaObject`) REFERENCES `mediaObject` (`idmediaObject`) ON DELETE CASCADE,
  CONSTRAINT `fk_audioObject_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;
