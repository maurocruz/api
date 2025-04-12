--
-- IMAGE OBJECT
--
CREATE TABLE IF NOT EXISTS `imageObject` (
  `idimageObject` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `creativeWork` INT UNSIGNED NOT NULL,
  `mediaObject` INT UNSIGNED NOT NULL,
  `thing` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`idimageObject`, `mediaObject`, `thing`),
  INDEX `fk_imageObject_thing_idx` (`thing` ASC) VISIBLE,
  INDEX `fk_imageObject_creativeWork_idx` (`creativeWork` ASC) VISIBLE,
  INDEX `fk_imageObject_mediaObject_idx` (`mediaObject` ASC) VISIBLE,
  CONSTRAINT `fk_imageObject_creativeWork` FOREIGN KEY (`creativeWork`)  REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE,
  CONSTRAINT `fk_imageObject_mediaObject` FOREIGN KEY (`mediaObject`) REFERENCES `mediaObject` (`idmediaObject`) ON DELETE CASCADE,
  CONSTRAINT `fk_imageObject_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;


-- THING HAS IMAGEOBJECT
CREATE TABLE IF NOT EXISTS `thing_has_imageObject` (
  `idthing` INT UNSIGNED NOT NULL,
  `idimageObject` INT UNSIGNED NOT NULL,
  `caption` TEXT NULL DEFAULT NULL,
  `href` VARCHAR(255) NULL DEFAULT NULL,
  `position` INT NOT NULL DEFAULT '0',
  `representativeOfPage` TINYINT NOT NULL DEFAULT '0',
  PRIMARY KEY (`idthing`, `idimageObject`),
  INDEX `fk_thing_has_imageObject_idthing_idx` (`idthing` ASC) VISIBLE,
  INDEX `fk_thing_has_imageObject_idimageObject_idx` (`idimageObject` ASC) VISIBLE,
  CONSTRAINT `fk_thing_has_imageObject_idimageObject`
   FOREIGN KEY (`idimageObject`) REFERENCES `imageObject` (`idimageObject`) ON DELETE CASCADE,
  CONSTRAINT `fk_thing_has_imageObject_idthing` FOREIGN KEY (`idthing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
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