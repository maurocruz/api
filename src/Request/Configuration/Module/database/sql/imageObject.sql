--
-- IMAGE OBJECT
--

CREATE TABLE IF NOT EXISTS `imageObject` (
  `idimageObject` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `mediaObject` INT UNSIGNED NOT NULL,
  `representativeOfPage` TINYINT DEFAULT 0,
  PRIMARY KEY (`idimageObject`),
  CONSTRAINT `fk_imageObject_mediaObject` FOREIGN KEY (`mediaObject`) REFERENCES `mediaObject` (`idmediaObject`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- THING HAS IMAGEOBJECT

CREATE TABLE IF NOT EXISTS `thing_has_imageObject` (
  `idthing` INT UNSIGNED NOT NULL,
  `idimageObject` INT UNSIGNED NOT NULL,
  `position` INT UNSIGNED DEFAULT NULL,
  `representativeOfPage` TINYINT(1) NOT NULL DEFAULT 0,
  `caption` TEXT,
  PRIMARY KEY (`idthing`, `idimageObject`),
  KEY `idx_1` (`representativeOfPage`),
  CONSTRAINT `FK_thing_has_imageObject_imageObject` FOREIGN KEY (`idimageObject`) REFERENCES `imageObject` (`idimageObject`) ON DELETE CASCADE,
  CONSTRAINT `FK_thing_has_imageObject_thing` FOREIGN KEY (`idthing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;

--  TRIGGER

DELIMITER $$
CREATE TRIGGER `thing_has_imageObject_BEFORE_INSERT` BEFORE INSERT ON `thing_has_imageObject` FOR EACH ROW
BEGIN
  DECLARE count INT UNSIGNED;
  SET count = (SELECT COUNT(*) FROM `thing_has_imageObject` WHERE `idthing`=NEW.`idthing`);
  IF NEW.`position`='' OR NEW.`position` IS NULL
  THEN SET NEW.`position`= count+1;
  END IF;
END;
DELIMITER ;