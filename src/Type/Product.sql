--
-- TABLE product
-- -- relational dependencies
--

CREATE TABLE IF NOT EXISTS `product` (
  `idproduct` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `additionalType` varchar(45) DEFAULT NULL,
  `category` varchar(64) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `position` tinyint unsigned DEFAULT NULL,
  `availability` varchar(45) NOT NULL DEFAULT '',
  `manufacturer` INT NULL,
  `offers` INT NULL,
  `dateCreated` timestamp NULL DEFAULT NULL,
  `dateModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idproduct`),
  KEY `idx_1` (`idproduct`,`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `product_has_imageObject` (
  `idproduct` int NOT NULL,
  `idimageObject` int NOT NULL,
  `caption` varchar(50) DEFAULT NULL,
  `position` tinyint unsigned NOT NULL DEFAULT '1',
  `representativeOfPage` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`idproduct`,`idimageObject`),
  KEY `FK_product_has_images_images` (`idimageObject`),
  CONSTRAINT `fk_product_has_imageObject_imageObject1` FOREIGN KEY (`idimageObject`) REFERENCES `imageObject` (`idimageObject`) ON DELETE CASCADE,
  CONSTRAINT `fk_product_has_imageObject_product1` FOREIGN KEY (`idproduct`) REFERENCES `product` (`idproduct`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TRIGGER IF EXISTS `product_has_imageObject_BEFORE_INSERT`;
DELIMITER $$
CREATE DEFINER = CURRENT_USER TRIGGER `product_has_imageObject_BEFORE_INSERT` BEFORE INSERT ON `product_has_imageObject` FOR EACH ROW
BEGIN
    DECLARE count INT;
    SET count = (SELECT COUNT(*) FROM `product_has_imageObject` WHERE `idproduct`=NEW.`idproduct`);
    IF NEW.`position`='' OR NEW.`position` IS NULL
    THEN SET NEW.`position`= count+1;
    END IF;
END$$
DELIMITER ;