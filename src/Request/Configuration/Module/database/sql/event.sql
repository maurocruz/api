--
-- CREATE TABLE event
--
CREATE TABLE IF NOT EXISTS `event` (
  `idevent` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thing` INT UNSIGNED NOT NULL,
  `director` VARCHAR(255) DEFAULT NULL,
  `endDate` DATETIME DEFAULT NULL,
  `keywords` VARCHAR(255) DEFAULT NULL,
  `location` INT UNSIGNED DEFAULT NULL,
  `organizer` INT UNSIGNED DEFAULT NULL,
  `organizerType` VARCHAR(45) DEFAULT NULL,
  `startDate` DATETIME DEFAULT NULL,
  `superEvent` INT UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`idevent`,`thing`),
  KEY `fk_event_thing_idx1` (`thing`),
  CONSTRAINT `fk_event_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;

