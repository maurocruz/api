
--
-- TRIP
--

CREATE TABLE IF NOT EXISTS `trip`(
  `idtrip` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thing` INT UNSIGNED NOT NULL,
  `arrivalTime` time DEFAULT NULL,
  `departureTime` time DEFAULT NULL,
  `provider` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`idtrip`,`thing`),
  INDEX `fk_trip_thing_idx` (`thing`),
  INDEX `fk_trip_provider_idx` (`provider`),
  CONSTRAINT `fk_trip_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE,
  CONSTRAINT `fk_trip_provider` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;
