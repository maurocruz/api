/**
 * Author:  Mauro Cruz <maurocruz@pirenopolis.tur.br>
 * Created: 27 de fev de 2020
 */

--
-- CREATE TABLE service REQUIRE
-- -- relational provider
-- -- relational broker
-- -- organization
-- -- person
-- -- place
-- -- imageObject
-- -- offer
--

--
-- TABLE service
-- 
CREATE TABLE `service` (
  `idservice` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(125) DEFAULT NULL,
  `description` text,
  `providerId` int(10) DEFAULT NULL,
  `providerType` varchar(45) DEFAULT NULL,
  `audience` varchar(45) DEFAULT NULL,
  `areaServed` int(10) DEFAULT NULL,
  `offers` int(10) DEFAULT NULL,
  `hasOfferCatalog` int(10) DEFAULT NULL,
  `serviceType` varchar(255) DEFAULT NULL,
  `serviceOutput` varchar(255) DEFAULT NULL,
  `termsOfService` text,
  PRIMARY KEY (`idservice`),
  KEY `fk_service_place1_idx` (`areaServed`),
  KEY `fk_service_1_idx` (`hasOfferCatalog`),
  CONSTRAINT `fk_service_1` FOREIGN KEY (`hasOfferCatalog`) REFERENCES `offerCatalog_has_service` (`idofferCatalog`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_service_place1` FOREIGN KEY (`areaServed`) REFERENCES `place` (`idplace`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
--
-- relational table with offerCatalog
--
CREATE TABLE IF NOT EXISTS `offerCatalog_has_service` (
  `idofferCatalog` INT(10) NOT NULL,
  `idservice` INT(10) NOT NULL,
  PRIMARY KEY (`idofferCatalog`, `idservice`),
  INDEX `fk_offerCatalog_has_service_service1_idx` (`idservice` ASC),
  CONSTRAINT `fk_offerCatalog_has_service_service1` FOREIGN KEY (`idservice`) REFERENCES `service` (`idservice`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB;

--
-- relational table with service (hasOfferCatalog)
--
CREATE TABLE IF NOT EXISTS `service_has_offerCatalog` (
  `idservice` INT(10) NOT NULL,
  `ifOfferCatalogOf` INT(10) NOT NULL,
  PRIMARY KEY (`idservice`, `hasOfferCatalog`),
  INDEX `fk_service_has_offerCatalog_1_idx` (`hasOfferCatalog` ASC),
  CONSTRAINT `fk_service_has_offerCatalog_1` FOREIGN KEY (`hasOfferCatalog`) REFERENCES `service` (`idservice`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_service_has_offerCatalog_2` FOREIGN KEY (`idservice`) REFERENCES `service` (`idservice`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8;

--
-- relational table broker
--
CREATE TABLE IF NOT EXISTS `broker` (
  `idbroker` INT(10) NOT NULL AUTO_INCREMENT,
  `organization` INT(10) NULL,
  `person` INT(10) NULL,
  PRIMARY KEY (`idbroker`),
  INDEX `fk_broker_1_idx` (`organization` ASC),
  INDEX `fk_broker_2_idx` (`person` ASC),
  CONSTRAINT `fk_broker_1` FOREIGN KEY (`organization`) REFERENCES `organization` (`idorganization`) ON DELETE SET NULL ON UPDATE NO ACTION,
  CONSTRAINT `fk_broker_2` FOREIGN KEY (`person`) REFERENCES `person` (`idperson`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE = InnoDB;

--
-- relational table with broker
--
CREATE TABLE IF NOT EXISTS `service_has_broker` (
  `idservice` INT(10) NOT NULL,
  `idbroker` INT(10) NOT NULL,
  PRIMARY KEY (`idservice`, `idbroker`),
  INDEX `fk_service_has_broker_broker1_idx` (`idbroker` ASC),
  INDEX `fk_service_has_broker_service1_idx` (`idservice` ASC),
  CONSTRAINT `fk_service_has_broker_service1` FOREIGN KEY (`idservice`) REFERENCES `service` (`idservice`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_service_has_broker_broker1` FOREIGN KEY (`idbroker`) REFERENCES `broker` (`idbroker`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8;

--
-- relational table with offer
--
CREATE TABLE IF NOT EXISTS `service_has_offer` (
  `idservice` INT(10) NOT NULL,
  `idoffer` INT(10) NOT NULL,
  `quantity` INT(5) NULL,
  PRIMARY KEY (`idservice`, `idoffer`),
  INDEX `fk_service_has_offer_offer1_idx` (`idoffer` ASC),
  INDEX `fk_service_has_offer_service1_idx` (`idservice` ASC),
  CONSTRAINT `fk_service_has_offer_service1` FOREIGN KEY (`idservice`) REFERENCES `service` (`idservice`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_service_has_offer_offer1` FOREIGN KEY (`idoffer`) REFERENCES `offer` (`idoffer`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8;

--
-- relational table with imageObject
--
CREATE TABLE IF NOT EXISTS `service_has_imageObject` (
  `idservice` INT(10) NOT NULL,
  `idimageObject` INT(10) NOT NULL,
  PRIMARY KEY (`idservice`, `idimageObject`),
  INDEX `fk_service_has_imageObject_imageObject1_idx` (`idimageObject` ASC),
  INDEX `fk_service_has_imageObject_service1_idx` (`idservice` ASC),
  CONSTRAINT `fk_service_has_imageObject_service1` FOREIGN KEY (`idservice`) REFERENCES `service` (`idservice`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_service_has_imageObject_imageObject1` FOREIGN KEY (`idimageObject`) REFERENCES `imageObject` (`idimageObject`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8;