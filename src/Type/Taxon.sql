--
-- CREATE TABLE taxon
-- -- relationships:
-- -- -- imageObject

CREATE TABLE IF NOT EXISTS `taxon` (
  `idtaxon` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT '',
  `description` text,
  `url` varchar(255) DEFAULT NULL,
  `taxonRank` varchar(255) DEFAULT NULL,
  `vernacularName` varchar(255) DEFAULT NULL,
  `parentTaxon` varchar(255) DEFAULT NULL,
  `scientificNameAuthorship` varchar(255) DEFAULT NULL,
  `occurrence` varchar(255) DEFAULT NULL,
  `flowering` varchar(100) DEFAULT NULL,
  `fructification` varchar(100) DEFAULT NULL,
  `height` text,
  `roots` text,
  `leafs` text,
  `flowers` text,
  `fruits` text,
  `citations` text,
  `dateModified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idtaxon`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `taxon_has_imageObject` (
  `idtaxon` int(10) unsigned NOT NULL,
  `idimageObject` int(10) NOT NULL,
  `caption` varchar(45) DEFAULT NULL,
  `position` int(5) DEFAULT NULL,
  `representativeOfPage` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`idtaxon`,`idimageObject`),
  KEY `FK_taxon_has_images_imageObject` (`idimageObject`),
  CONSTRAINT `FK_taxon_has_images_imageObject` FOREIGN KEY (`idimageObject`) REFERENCES `imageObject` (`idimageObject`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `FK_taxon_has_imageObject` FOREIGN KEY (`idtaxon`) REFERENCES `taxon` (`idtaxon`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB;

DROP TRIGGER IF EXISTS `taxon_has_imageObject_BEFORE_INSERT`;
DELIMITER $$
CREATE DEFINER = CURRENT_USER TRIGGER `taxon_has_imageObject_BEFORE_INSERT` BEFORE INSERT ON `taxon_has_imageObject` FOR EACH ROW
BEGIN
    DECLARE count INT;
    SET count = (SELECT COUNT(*) FROM `taxon_has_imageObject` WHERE `idtaxon`=NEW.`idtaxon`);
    IF NEW.`position`='' OR NEW.`position` IS NULL
        THEN SET NEW.`position`= count+1;
    END IF;
END$$
DELIMITER ;