

CREATE TABLE IF NOT EXISTS `thing_has_imageObject` (
  `idthing` int(10) UNSIGNED NOT NULL,
  `idimageObject` int(10) UNSIGNED NOT NULL,
  `position` int(10) UNSIGNED DEFAULT NULL,
  `representativeOfPage` tinyint(1) NOT NULL DEFAULT '0',
  `caption` text,
  PRIMARY KEY (`idthing`,`idimageObject`),
  KEY `idx_1` (`representativeOfPage`),
  KEY `FK_thing_has_imageObject_imageObject` (`idimageObject`),
  CONSTRAINT `FK_thing_has_imageObject_imageObject` FOREIGN KEY (`idimageObject`) REFERENCES `imageObject` (`idimageObject`) ON DELETE CASCADE,
  CONSTRAINT `FK_thing_has_imageObject_thing` FOREIGN KEY (`idthing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE=InnoDB;