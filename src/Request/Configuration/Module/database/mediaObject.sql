--
-- MEDIA OBJECT
--
CREATE TABLE IF NOT EXISTS `mediaObject` (
  `idmediaObject` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `creativeWork` INT UNSIGNED NOT NULL,
  `thing` INT UNSIGNED NOT NULL,
  `bitrate` DECIMAL(6,2) NULL DEFAULT NULL,
  `duration` TIME NULL DEFAULT NULL,
  `contentSize` VARCHAR(100) NULL DEFAULT NULL,
  `contentUrl` VARCHAR(255) NOT NULL,
  `encodingFormat` VARCHAR(50) NULL DEFAULT NULL,
  `height` INT NULL DEFAULT NULL,
  `width` INT NULL DEFAULT NULL,
  `uploadDate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idmediaObject`, `creativeWork`, `thing`),
  INDEX `fk_mediaObject_thing_idx` (`thing` ASC) VISIBLE,
  INDEX `fk_mediaObject_creativeWork_idx` (`creativeWork` ASC) VISIBLE,
  CONSTRAINT `fk_mediaObject_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE,
  CONSTRAINT `fk_mediaObject_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;
