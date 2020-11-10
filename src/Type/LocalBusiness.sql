
--
-- CREATE TABLE localBusiness
--
-- Relationships
-- -- one to one
-- -- -- postalAddress
-- -- -- organization
-- -- one to many
-- -- -- place
-- -- -- contactPoint
-- -- -- person
-- -- -- imageObject
--

CREATE TABLE IF NOT EXISTS `localBusiness` (
  `idlocalBusiness` int(10) NOT NULL AUTO_INCREMENT,
  `location` int(10) NOT NULL,
  `organization` int(10) DEFAULT NULL,
  `additionalType` varchar(180) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `disambiguatingDescription` text,
  `url` varchar(255) DEFAULT NULL,
  `providerId` INT(10) NULL,
  `providerType` VARCHAR(45) NULL,
  `hasOfferCatalog` varchar(125) DEFAULT NULL,
  `address` int(10) DEFAULT NULL,
  `paymentAccepted` varchar(255) DEFAULT NULL,
  `openingHours` text,
  `priceRange` varchar(255) DEFAULT NULL,
  `dateCreated` date DEFAULT NULL,
  `dateModified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idlocalBusiness`,`location`),
  KEY `idx_1` (`idlocalBusiness`,`hasOfferCatalog`),
  KEY `idx_2` (`idlocalBusiness`),
  KEY `fk_localBusiness_2_idx` (`address`),
  KEY `fk_localBusiness_3_idx` (`organization`),
  CONSTRAINT `fk_localBusiness_2` FOREIGN KEY (`address`) REFERENCES `postalAddress` (`idpostalAddress`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_localBusiness_3` FOREIGN KEY (`organization`) REFERENCES `organization` (`idorganization`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- relational table with contactPoint
--
CREATE TABLE IF NOT EXISTS `localBusiness_has_contactPoint` (
  `idlocalBusiness` INT(10) NOT NULL,
  `idcontactPoint` INT(10) NOT NULL,
  PRIMARY KEY (`idlocalBusiness`, `idcontactPoint`),
  INDEX `fk_localBusiness_has_contactPoint_2_idx` (`idcontactPoint` ASC),
  CONSTRAINT `fk_localBusiness_has_contactPoint_1` FOREIGN KEY (`idlocalBusiness`) REFERENCES `localBusiness` (`idlocalBusiness`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_localBusiness_has_contactPoint_2` FOREIGN KEY (`idcontactPoint`) REFERENCES `contactPoint` (`idcontactPoint`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8;

--
-- relational table with person
--
CREATE TABLE IF NOT EXISTS `localBusiness_has_person` (
  `idlocalBusiness` INT(10) NOT NULL,
  `idperson` INT(10) NOT NULL,
  `jobTitle` VARCHAR(45) NULL DEFAULT NULL,
  `position` INT(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idlocalBusiness`, `idperson`),
  INDEX `fk_localBusiness_has_person_person1_idx` (`idperson` ASC),
  INDEX `fk_localBusiness_has_person_localBusiness1_idx` (`idlocalBusiness` ASC),
  CONSTRAINT `fk_localBusiness_has_person_localBusiness1` FOREIGN KEY (`idlocalBusiness`) REFERENCES `localBusiness` (`idlocalBusiness`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_localBusiness_has_person_person1` FOREIGN KEY (`idperson`) REFERENCES `person` (`idperson`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8;

--
-- relational table with imageObject
--
CREATE TABLE IF NOT EXISTS `localBusiness_has_imageObject` (
  `idlocalBusiness` INT(10) NOT NULL,
  `idimageObject` INT(10) NOT NULL,
  `position` INT(5) NULL DEFAULT NULL,
  `representativeOfPage` TINYINT(1) NULL DEFAULT NULL,
  `caption` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`idlocalBusiness`, `idimageObject`),
  INDEX `idx_1` (`representativeOfPage` ASC, `idimageObject` ASC),
  INDEX `FK_localBusiness_has_imageObject_imageObject_idx` (`idimageObject` ASC),
  CONSTRAINT `FK_localBusiness_has_imageObject_imageObject` FOREIGN KEY (`idimageObject`) REFERENCES `imageObject` (`idimageObject`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `FK_localBusiness_has_imageObject_localBusiness` FOREIGN KEY (`idlocalBusiness`) REFERENCES `localBusiness` (`idlocalBusiness`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8;

DROP TRIGGER IF EXISTS `increment_position`;
DELIMITER $$
CREATE DEFINER = CURRENT_USER TRIGGER `increment_position` BEFORE INSERT ON `localBusiness_has_imageObject` FOR EACH ROW
BEGIN
  DECLARE countIt INT;
  SET countIt = (SELECT COUNT(*) FROM `localBusiness_has_imageObject`);
  IF NEW.`position`='' THEN SET NEW.`position`= countIt+1;
  END IF;
END$$
DELIMITER ;
