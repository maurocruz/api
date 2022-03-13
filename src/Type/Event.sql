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
  `directed` varchar(255) DEFAULT NULL,
  `link_directed` varchar(100) DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idevent`),
  KEY `idx_1` (`endDate`),
  KEY `idx_2` (`startDate`),
  KEY `fk_events_1_idx` (`location`),
  CONSTRAINT `fk_events_1` FOREIGN KEY (`location`) REFERENCES `place` (`idplace`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `event_has_event` (
  `idHasPart` INT NOT NULL,
  `idIsPartOf` INT NOT NULL,
  PRIMARY KEY (`idHasPart`, `idIsPartOf`),
  INDEX `fk_event_has_event_event2_idx` (`idIsPartOf` ASC),
  INDEX `fk_event_has_event_event1_idx` (`idHasPart` ASC),
  CONSTRAINT `fk_event_has_event_event1` FOREIGN KEY (`idHasPart`) REFERENCES `event` (`idevent`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_event_has_event_event2` FOREIGN KEY (`idIsPartOf`) REFERENCES `event` (`idevent`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `events_has_person` (
  `idevents` INT(10) NOT NULL,
  `idperson` INT(10) NOT NULL,
  PRIMARY KEY (`idevents`, `idperson`),
  INDEX `fk_events_has_person_person1_idx` (`idperson` ASC),
  INDEX `fk_events_has_person_events1_idx` (`idEvents` ASC),
  CONSTRAINT `fk_events_has_person_events1` FOREIGN KEY (`idevents`) REFERENCES `event` (`idevents`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_events_has_person_person1` FOREIGN KEY (`idperson`) REFERENCES `person` (`idperson`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS `event_has_imageObject` (
   `idimageObject` int NOT NULL,
   `idevent` int NOT NULL,
   `caption` varchar(255) DEFAULT NULL,
   `position` tinyint DEFAULT NULL,
   `cover` tinyint DEFAULT NULL,
   PRIMARY KEY (`idevent`,`idimageObject`),
   KEY `FK_events_has_images_imageObject` (`idimageObject`),
   CONSTRAINT `FK_event_has_imageObject_imageObject1` FOREIGN KEY (`idimageObject`) REFERENCES `imageObject` (`idimageObject`) ON DELETE CASCADE ON UPDATE NO ACTION,
   CONSTRAINT `fk_event_has_imageObject_event1` FOREIGN KEY (`idevent`) REFERENCES `event` (`idevent`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB;


DROP TRIGGER IF EXISTS `event_has_imageObject_BEFORE_INSERT`;
DELIMITER $$
CREATE DEFINER = CURRENT_USER TRIGGER `event_has_imageObject_BEFORE_INSERT` BEFORE INSERT ON `event_has_imageObject` FOR EACH ROW
BEGIN
    DECLARE count INT;
    SET count = (SELECT COUNT(*) FROM `event_has_imageObject` WHERE `idevent`=NEW.`idevent`);
    IF NEW.`position`='' OR NEW.`position` IS NULL
    THEN SET NEW.`position`= count+1;
    END IF;
END$$
DELIMITER ;
