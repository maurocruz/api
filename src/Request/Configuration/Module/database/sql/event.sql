--
-- CREATE TABLE event
--
CREATE TABLE IF NOT EXISTS `event` (
  `idevent` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thing` INT UNSIGNED NOT NULL,
  `about` INT UNSIGNED DEFAULT NULL,
  `director` VARCHAR(255) DEFAULT NULL,
  `endDate` DATETIME DEFAULT NULL,
  `keywords` VARCHAR(255) DEFAULT NULL,
  `location` INT UNSIGNED DEFAULT NULL,
  `organizer` INT UNSIGNED DEFAULT NULL,
  `startDate` DATETIME DEFAULT NULL,
  `subEvent` INT UNSIGNED DEFAULT NULL,
  `superEvent` INT UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`idevent`,`thing`),
  KEY `fk_event_thing_idx1` (`thing`),
  KEY `fk_event_about_idx1` (`about`),
  KEY `fk_event_location_idx1` (`location`),
  KEY `fk_event_organizer_idx1` (`organizer`),
  KEY `fk_event_subEvent_idx1` (`subEvent`),
  KEY `fk_event_superEvent_idx1` (`superEvent`),
  CONSTRAINT `fk_event_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE,
  CONSTRAINT `fk_event_about` FOREIGN KEY (`about`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE,
  CONSTRAINT `fk_event_location` FOREIGN KEY (`location`) REFERENCES `place` (`idplace`) ON DELETE CASCADE,
  CONSTRAINT `fk_event_organizer` FOREIGN KEY (`organizer`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE,
  CONSTRAINT `fk_event_subEvent` FOREIGN KEY (`subEvent`) REFERENCES `event` (`idevent`) ON DELETE CASCADE,
  CONSTRAINT `fk_event_superEvent` FOREIGN KEY (`superEvent`) REFERENCES `event` (`idevent`) ON DELETE CASCADE
) ENGINE = InnoDB;

