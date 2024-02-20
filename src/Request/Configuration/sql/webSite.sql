--
-- WEBSITE TABLE
-- --

CREATE TABLE IF NOT EXISTS `organization` (
  `idorganization` int NOT NULL AUTO_INCREMENT,
  `additionalType` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `disambiguatingDescription` text,
  `legalName` varchar(124) DEFAULT NULL,
  `taxId` varchar(24) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `hasOfferCatalog` text,
  `location` int DEFAULT NULL,
  `address` int DEFAULT NULL,
  `areaServed` int(10) DEFAULT NULL,
  `dateCreated` datetime DEFAULT CURRENT_TIMESTAMP,
  `dateModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idorganization`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `organization_has_contactPoint` (
  `idorganization` int NOT NULL,
  `idcontactPoint` int unsigned NOT NULL,
  PRIMARY KEY (`idorganization`,`idcontactPoint`),
  KEY `fk_Organization_has_contactPoint_contactPoint1_idx` (`idcontactPoint`),
  KEY `fk_Organization_has_contactPoint_Organization1_idx` (`idorganization`),
  CONSTRAINT `fk_Organization_has_contactPoint_contactPoint` FOREIGN KEY (`idcontactPoint`) REFERENCES `contactPoint` (`idcontactPoint`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_organization_has_contactPoints_organization` FOREIGN KEY (`idorganization`) REFERENCES `organization` (`idorganization`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `organization_has_imageObject` (
  `idorganization` int NOT NULL,
  `idimageObject` int NOT NULL,
  `position` int DEFAULT NULL,
  `representativeOfPage` tinyint DEFAULT NULL,
  `caption` text,
  PRIMARY KEY (`idorganization`,`idimageObject`),
  KEY `fk_Organization_has_ImageObject_Organization_idx` (`idorganization`),
  KEY `fk_Organization_has_imageObject_imageObject_idx` (`idimageObject`),
  CONSTRAINT `fk_Organization_has_imageObject_imageObject` FOREIGN KEY (`idimageObject`) REFERENCES `imageObject` (`idimageObject`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_organization_has_imageObject_organization` FOREIGN KEY (`idorganization`) REFERENCES `organization` (`idorganization`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `organization_has_person` (
  `idorganization` int NOT NULL,
  `idperson` int NOT NULL,
  `jobTitle` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idorganization`,`idperson`),
  KEY `fk_Organization_has_person_person_idx` (`idperson`),
  KEY `fk_Organization_has_person_Organization_idx` (`idorganization`),
  CONSTRAINT `fk_Organization_has_person_organization1` FOREIGN KEY (`idorganization`) REFERENCES `organization` (`idorganization`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_Organization_has_person_person1` FOREIGN KEY (`idperson`) REFERENCES `person` (`idperson`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `webSite` (
  `idwebSite` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `url` text NOT NULL,
  `image` int DEFAULT NULL,
  `publisher` int DEFAULT NULL,
  `editor` int DEFAULT NULL,
  `dateCreated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dateModified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idwebSite`),
  KEY `fk_webSite_1_idx` (`publisher`),
  KEY `fk_webSite_2_idx` (`editor`),
  KEY `fk_webSite_3_idx` (`image`),
  CONSTRAINT `fk_webSite_organization` FOREIGN KEY (`publisher`) REFERENCES `organization` (`idorganization`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_webSite_person` FOREIGN KEY (`editor`) REFERENCES `person` (`idperson`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_webSite_imageObject` FOREIGN KEY (`image`) REFERENCES `imageObject` (`idimageObject`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `webSite_has_person` (
  `idwebSite` int NOT NULL,
  `idperson` int NOT NULL,
  PRIMARY KEY (`idwebSite`,`idperson`),
  KEY `fk_webSite_has_person_person1_idx` (`idperson`),
  KEY `fk_webSite_has_person_webSite1_idx` (`idwebSite`),
  CONSTRAINT `fk_webSite_has_person_person` FOREIGN KEY (`idperson`) REFERENCES `person` (`idperson`) ON DELETE NO ACTION ON UPDATE NO ACTION,
CONSTRAINT `fk_webSite_has_person_webSite` FOREIGN KEY (`idwebSite`) REFERENCES `webSite` (`idwebSite`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `propertyValue` (
  `idpropertyValue` int NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `value` varchar(255) NOT NULL,
PRIMARY KEY (`idpropertyValue`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `webSite_has_propertyValue` (
    `idwebSite` INT NOT NULL,
    `idpropertyValue` INT NOT NULL,
    PRIMARY KEY (`idwebSite`, `idpropertyValue`),
    INDEX `fk_webSite_has_propertyValue_1_idx` (`idpropertyValue` ASC),
    INDEX `fk_webSite_has_propertyValue_2_idx` (`idwebSite` ASC),
    CONSTRAINT `fk_webSite_has_propertyValue_webSite` FOREIGN KEY (`idwebSite`) REFERENCES `webSite` (`idwebSite`) ON DELETE CASCADE ON UPDATE NO ACTION,
    CONSTRAINT `fk_webSite_has_propertyValue_propertyValue` FOREIGN KEY (`idpropertyValue`) REFERENCES `propertyValue` (`idpropertyValue`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `webPage` (
  `idwebPage` int NOT NULL AUTO_INCREMENT,
  `isPartOf` int DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `alternativeHeadline` varchar(50) DEFAULT NULL,
  `showtitle` tinyint DEFAULT NULL,
  `showdescription` tinyint DEFAULT NULL,
  `breadcrumb` text,
  `dateCreated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dateModified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idwebPage`),
  KEY `url` (`url`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `webPage_has_propertyValue` (
  `idwebPage` int NOT NULL,
  `idpropertyValue` int NOT NULL,
  PRIMARY KEY (`idwebPage`,`idpropertyValue`),
  KEY `fk_webPage_has_propertyValue_idx` (`idpropertyValue`),
  CONSTRAINT `fk_webPage_has_propertyValue_propertyValue` FOREIGN KEY (`idpropertyValue`) REFERENCES `propertyValue` (`idpropertyValue`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_webPage_has_propertyValue_webPage` FOREIGN KEY (`idwebPage`) REFERENCES `webPage` (`idwebPage`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS `webPageElement` (
  `idwebPageElement` int NOT NULL AUTO_INCREMENT,
  `isPartOf` int NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `text` text,
  `position` tinyint unsigned DEFAULT NULL,
  `author` varchar(50) DEFAULT NULL,
  `gallery` varchar(5) DEFAULT NULL,
  `dateCreated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dateModified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idwebPageElement`),
  KEY `FK_posts_pages` (`isPartOf`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `webPageElement_has_imageObject` (
  `idwebPageElement` int NOT NULL,
  `idimageObject` int NOT NULL,
  `width` float unsigned NOT NULL DEFAULT '1',
  `height` float DEFAULT NULL,
  `href` varchar(255) DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `position` int unsigned DEFAULT NULL,
  `representativeOfPage` tinyint(1) DEFAULT NULL,
  `ingallery` tinyint DEFAULT NULL,
  `incontent` tinyint DEFAULT '1',
  PRIMARY KEY (`idwebPageElement`,`idimageObject`),
  KEY `fk_webPageElement_has_images_1_idx` (`idwebPageElement`),
  KEY `fk_webPageElement_has_images_2_idx` (`idimageObject`),
  CONSTRAINT `fk_webPageElement_has_imageObject_webPageElement` FOREIGN KEY (`idwebPageElement`) REFERENCES `webPageElement` (`idwebPageElement`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_webPageElement_has_imageObject_imageObject` FOREIGN KEY (`idimageObject`) REFERENCES `imageObject` (`idimageObject`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `webPageElement_has_propertyValue` (
  `idwebPageElement` int NOT NULL,
  `idpropertyValue` int NOT NULL,
  PRIMARY KEY (`idwebPageElement`,`idpropertyValue`),
  KEY `fk_webPageElement_has_propertyValue_1_idx` (`idpropertyValue`),
  CONSTRAINT `fk_webPageElement_has_propertyValue_webPageElement` FOREIGN KEY (`idwebPageElement`) REFERENCES `webPageElement` (`idwebPageElement`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_webPageElement_has_propertyValue_propertyValue` FOREIGN KEY (`idpropertyValue`) REFERENCES `propertyValue` (`idpropertyValue`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB;
