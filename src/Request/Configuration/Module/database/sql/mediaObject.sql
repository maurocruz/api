--
-- MEDIA OBJECT
--

CREATE TABLE IF NOT EXISTS `mediaObject` (
  `idmediaObject` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `creativeWork` INT UNSIGNED NOT NULL,
  `thing` int unsigned not null,
  `contentSize` VARCHAR(100) DEFAULT NULL,
  `contentUrl` VARCHAR(255) NOT NULL,
  `encodingFormat` VARCHAR(50) DEFAULT NULL,
  `height` INT DEFAULT NULL,
  `width` INT DEFAULT NULL,
  `uploadDate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idmediaObject`,`creativeWork`),
  KEY `fk_mediaObject_creativeWork_idx` (`creativeWork`),
  CONSTRAINT `fk_mediaObject_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE
)ENGINE = InnoDB;