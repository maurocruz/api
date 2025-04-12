
-- VIDEO OBJECT
CREATE TABLE IF NOT EXISTS `videoObject` (
  `idvideoObject` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `creativeWork` INT UNSIGNED NOT NULL,
  `mediaObject` INT UNSIGNED NOT NULL,
  `thing` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`idvideoObject`, `mediaObject`, `thing`),
  INDEX `fk_videoObject_thing_idx` (`thing`),
  INDEX `fk_videoObject_creativeWork_idx` (`creativeWork`),
  INDEX `fk_videoObject_mediaObject_idx` (`mediaObject`),
  CONSTRAINT `fk_videoObject_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE,
  CONSTRAINT `fk_videoObject_mediaObject` FOREIGN KEY (`mediaObject`) REFERENCES `mediaObject` (`idmediaObject`) ON DELETE CASCADE,
  CONSTRAINT `fk_videoObject_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;