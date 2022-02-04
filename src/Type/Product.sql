--
-- TABLE product
-- Relationships: ImageObject, Offer
--

CREATE TABLE IF NOT EXISTS `product` (
    `idproduct` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `additionalType` VARCHAR(45) NULL DEFAULT NULL,
    `category` VARCHAR(64) NULL,
    `manufacturer` INT NOT NULL,
    `manufacturerType` VARCHAR(45) NOT NULL,
    `description` TEXT NOT NULL,
    `disambiguatingDescription` TEXT NULL,
    `dateCreated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `dateModified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`idproduct`),
    INDEX `fk_product_organization_1_idx` (`manufacturer` ASC),
    CONSTRAINT `fk_product_organization_1` FOREIGN KEY (`manufacturer`) REFERENCES `organization` (`idorganization`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB;

--
-- IMAGE OBJECT RELATIONSHIP
--

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


--
-- OFFER
--

CREATE TABLE IF NOT EXISTS `product_has_offer` (
    `idproduct` INT NOT NULL,
    `idoffer` INT NOT NULL,
    PRIMARY KEY (`idproduct`, `idoffer`),
    INDEX `fk_product_has_offer_1_idx` (`idoffer` ASC) VISIBLE,
    INDEX `fk_product_has_offer_2_idx` (`idproduct` ASC) VISIBLE,
    CONSTRAINT `fk_product_has_offer_1` FOREIGN KEY (`idoffer`) REFERENCES `offer` (`idoffer`) ON DELETE CASCADE ON UPDATE RESTRICT,
    CONSTRAINT `fk_product_has_offer_2` FOREIGN KEY (`idproduct`) REFERENCES `product` (`idproduct`)
) ENGINE = InnoDB;


DROP TRIGGER IF EXISTS `product_has_imageObject_BEFORE_INSERT`;

CREATE TRIGGER `product_has_imageObject_BEFORE_INSERT` BEFORE INSERT ON `product_has_imageObject` FOR EACH ROW
BEGIN
    DECLARE count INT;
    SET count = (SELECT COUNT(*) FROM `product_has_imageObject` WHERE `idproduct`=NEW.`idproduct`);
    IF NEW.`position`='' OR NEW.`position` IS NULL
    THEN SET NEW.`position`= count+1;
    END IF;
END;