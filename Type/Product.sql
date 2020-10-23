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
  `dateCreated` timestamp NULL DEFAULT NULL,
  `dateModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idproduct`),
  KEY `idx_1` (`idproduct`,`category`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;


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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
