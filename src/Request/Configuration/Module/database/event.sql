--
-- CREATE TABLE event
--
-- relationships
-- -- place (one to one)
-- -- imageObject (ono to many)
-- -- Person (one to many)
--
CREATE TABLE IF NOT EXISTS `event` (
  `idevent` int NOT NULL AUTO_INCREMENT,
  `superEvent` int DEFAULT NULL,
  `startDate` datetime DEFAULT NULL,
  `endDate` datetime DEFAULT NULL,
  `name` varchar(180) NOT NULL DEFAULT '',
  `description` text,
  `location` int DEFAULT NULL,
  `organizerId` int DEFAULT NULL,
  `organizerType` varchar(45) DEFAULT NULL,
  `src` varchar(255) DEFAULT NULL,
  `place` varchar(80) DEFAULT NULL,
  `schedule` time DEFAULT NULL,
  `directed` varchar(255) DEFAULT NULL,
  `link_directed` varchar(100) DEFAULT NULL,
  `dateCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dateModified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idevent`),
  KEY `idx_1` (`endDate`),
  KEY `idx_2` (`startDate`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `event_has_event` (
  `idHasPart` int NOT NULL,
  `idIsPartOf` int NOT NULL,
  PRIMARY KEY (`idHasPart`,`idIsPartOf`),
  KEY `fk_event_has_event_event2_idx` (`idIsPartOf`),
  KEY `fk_event_has_event_event1_idx` (`idHasPart`),
  CONSTRAINT `fk_event_has_event_event1` FOREIGN KEY (`idHasPart`) REFERENCES `event` (`idevent`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_event_has_event_event2` FOREIGN KEY (`idIsPartOf`) REFERENCES `event` (`idevent`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `event_has_imageObject` (
  `idevent` int NOT NULL,
  `idimageObject` int NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `position` tinyint DEFAULT NULL,
  `representativeOfPage` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`idevent`,`idimageObject`),
  KEY `FK_events_has_images_imageObject` (`idimageObject`),
  CONSTRAINT `fk_event_has_imageObject_event1` FOREIGN KEY (`idevent`) REFERENCES `event` (`idevent`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `FK_event_has_imageObject_imageObject1` FOREIGN KEY (`idimageObject`) REFERENCES `imageObject` (`idimageObject`) ON DELETE CASCADE ON UPDATE NO ACTION
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
