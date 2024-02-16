
CREATE TABLE IF NOT EXISTS `organization` (
                                            `idorganization` int NOT NULL AUTO_INCREMENT,
                                            `additionalType` varchar(255) DEFAULT NULL,
                                            `name` varchar(45) NOT NULL,
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