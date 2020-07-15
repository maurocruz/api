
--
-- CREATE TABLE event
--
-- relationships
-- -- place (one to one)
-- -- imageObject (ono to many)
-- -- Person (one to many)
--

CREATE TABLE IF NOT EXISTS `event` (
  `idevent` int(5) NOT NULL AUTO_INCREMENT,
  `startDate` datetime DEFAULT NULL,
  `endDate` datetime DEFAULT NULL,
  `name` varchar(180) NOT NULL DEFAULT '',
  `description` text,
  `location` int(10) DEFAULT NULL,
  `organizerId` int(10) DEFAULT NULL,
  `organizerType` varchar(45) DEFAULT NULL,
  `src` varchar(255) DEFAULT NULL,
  `place` varchar(80) DEFAULT NULL,
  `schedule` time DEFAULT NULL,
  `directed` varchar(255) DEFAULT NULL,
  `link_directed` varchar(100) DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idevent`),
  KEY `idx_1` (`endDate`),
  KEY `idx_2` (`startDate`),
  KEY `fk_events_1_idx` (`location`),
  CONSTRAINT `fk_events_1` FOREIGN KEY (`location`) REFERENCES `place` (`idplace`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB;

CREATE TABLE `event_has_imageObject` (
  `idimageObject` int(10) NOT NULL,
  `idevent` int(10) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `position` tinyint(4) DEFAULT NULL,
  `cover` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`idevent`,`idimageObject`),
  KEY `FK_events_has_images_imageObject` (`idimageObject`),
  CONSTRAINT `FK_event_has_imageObject_imageObject1` FOREIGN KEY (`idimageObject`) REFERENCES `imageObject` (`idimageObject`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_event_has_imageObject_event1` FOREIGN KEY (`idevent`) REFERENCES `event` (`idevent`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `events_has_person` (
  `idevents` INT(10) NOT NULL,
  `idperson` INT(10) NOT NULL,
  PRIMARY KEY (`idevents`, `idperson`),
  INDEX `fk_events_has_person_person1_idx` (`idperson` ASC),
  INDEX `fk_events_has_person_events1_idx` (`idEvents` ASC),
  CONSTRAINT `fk_events_has_person_events1` FOREIGN KEY (`idevents`) REFERENCES `event` (`idevents`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_events_has_person_person1` FOREIGN KEY (`idperson`) REFERENCES `person` (`idperson`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB;