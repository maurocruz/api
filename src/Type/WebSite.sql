--
-- WEBSITE TABLE
-- --
-- -- dependencies
-- -- -- organization
-- -- -- person
-- -- -- imageObject

CREATE TABLE `webSite` (
    `idwebSite` int NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `description` text,
    `url` text NOT NULL,
    `image` int DEFAULT NULL,
    `publisher` int DEFAULT NULL,
    `editor` int DEFAULT NULL,
    `dateCreated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`idwebSite`),
    KEY `fk_webSite_1_idx` (`publisher`),
    KEY `fk_webSite_2_idx` (`editor`),
    KEY `fk_webSite_3_idx` (`image`),
    CONSTRAINT `fk_webSite_1` FOREIGN KEY (`publisher`) REFERENCES `organization` (`idorganization`),
    CONSTRAINT `fk_webSite_2` FOREIGN KEY (`editor`) REFERENCES `person` (`idperson`),
    CONSTRAINT `fk_webSite_3` FOREIGN KEY (`image`) REFERENCES `imageObject` (`idimageObject`) ON DELETE SET NULL
) ENGINE=InnoDB;


CREATE TABLE `webSite_has_person` (
    `idwebSite` int NOT NULL,
    `idperson` int NOT NULL,
    PRIMARY KEY (`idwebSite`,`idperson`),
    KEY `fk_webSite_has_person_1_idx` (`idperson`),
    KEY `fk_webSite_has_person_2_idx` (`idwebSite`),
    CONSTRAINT `fk_webSite_has_person_1` FOREIGN KEY (`idperson`) REFERENCES `person` (`idperson`),
    CONSTRAINT `fk_webSite_has_person_2` FOREIGN KEY (`idwebSite`) REFERENCES `webSite` (`idwebSite`)
) ENGINE=InnoDB;


CREATE TABLE `webSite_has_propertyValue` (
     `idwebSite` int NOT NULL,
     `idpropertyValue` int NOT NULL,
     PRIMARY KEY (`idwebSite`,`idpropertyValue`),
     KEY `fk_webSite_has_propertyValue_1_idx` (`idpropertyValue`),
     KEY `fk_webSite_has_propertyValue_2_idx` (`idwebSite`),
     CONSTRAINT `fk_webSite_has_propertyValue_1` FOREIGN KEY (`idpropertyValue`) REFERENCES `propertyValue` (`idpropertyValue`)
) ENGINE=InnoDB;


