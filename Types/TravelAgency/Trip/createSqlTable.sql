/**
 * Author:  Mauro Cruz <maurocruz@pirenopolis.tur.br>
 * Created: 8 de mar de 2020
 */

--
-- CREATE TABLE trip
-- Require:
-- -- action
-- -- localBusiness
-- -- offer
-- 

CREATE TABLE IF NOT EXISTS `trip` (
  `idtrip` INT(10) NOT NULL AUTO_INCREMENT,
  `providerId` INT(10) NOT NULL,
  `providerType` VARCHAR(45) NULL,
  `additionalType` VARCHAR(45) NULL,
  `name` VARCHAR(125) NULL,
  `description` TEXT NULL,
  `disambiguatingDescription` TEXT NULL,
  `url` VARCHAR(225) NULL,
  `partOfTrip` INT(10) NULL,
  `subTrip` INT(10) NULL,
  PRIMARY KEY (`idtrip`, `providerId`),
  INDEX `fk_trip_1_idx` (`partOfTrip` ASC),
  INDEX `fk_trip_trip1_idx` (`subTrip` ASC),
  INDEX `fk_trip_2_idx` (`provider` ASC),
  CONSTRAINT `fk_trip_1` FOREIGN KEY (`partOfTrip`) REFERENCES `trip` (`idtrip`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_trip_trip1` FOREIGN KEY (`subTrip`) REFERENCES `trip` (`idtrip`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_trip_2` FOREIGN KEY (`providerId`) REFERENCES `localBusiness` (`idlocalBusiness`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `trip_has_action` (
  `idtrip` INT(10) NOT NULL,
  `idaction` INT(10) NOT NULL,
  PRIMARY KEY (`idtrip`, `idaction`),
  INDEX `fk_trip_has_action_action1_idx` (`idaction` ASC),
  INDEX `fk_trip_has_action_trip1_idx` (`idtrip` ASC),
  CONSTRAINT `fk_trip_has_action_trip1` FOREIGN KEY (`idtrip`) REFERENCES `trip` (`idtrip`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_trip_has_action_action1` FOREIGN KEY (`idaction`) REFERENCES `action` (`idaction`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `trip_has_offer` (
  `idtrip` INT(10) NOT NULL,
  `idoffer` INT(10) NOT NULL,
  `quantity`INT(5) NULL,
  PRIMARY KEY (`idtrip`, `idoffer`),
  INDEX `fk_trip_has_offer_offer1_idx` (`idoffer` ASC),
  INDEX `fk_trip_has_offer_trip1_idx` (`idtrip` ASC),
  CONSTRAINT `fk_trip_has_offer_trip1` FOREIGN KEY (`idtrip`) REFERENCES `trip` (`idtrip`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_trip_has_offer_offer1` FOREIGN KEY (`idoffer`) REFERENCES `offer` (`idoffer`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `trip_has_imageObject` (
  `idtrip` INT(10) NOT NULL,
  `idimageObject` INT(10) NOT NULL,
  `position` INT(5) NULL,
  `representativeOfPage` TINYINT(1) NULL,
  `caption` TEXT NULL,
  PRIMARY KEY (`idtrip`, `idimageObject`),
  INDEX `fk_trip_has_imageObject_imageObject1_idx` (`idimageObject` ASC),
  INDEX `fk_trip_has_imageObject_trip1_idx` (`idtrip` ASC),
  CONSTRAINT `fk_trip_has_imageObject_trip1` FOREIGN KEY (`idtrip`) REFERENCES `trip` (`idtrip`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_trip_has_imageObject_imageObject1` FOREIGN KEY (`idimageObject`) REFERENCES `imageObject` (`idimageObject`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB;
