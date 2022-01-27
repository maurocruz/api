/**
 * Author:  Mauro Cruz <maurocruz@pirenopolis.tur.br>
 * Created: 25 de fev de 2020
 */

--
-- CREATE TABLE place REQUIRE:
-- -- postalAddress
-- -- imageObject
--

CREATE TABLE IF NOT EXISTS `place` (
  `idplace` INT(10) NOT NULL AUTO_INCREMENT,
  `additionalType` VARCHAR(125) NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `disambiguatingDescription` TEXT NULL,
  `latitude` FLOAT(10,6) NULL DEFAULT NULL,
  `longitude` FLOAT(10,6) NULL DEFAULT NULL,
  `elevation` VARCHAR(45) NULL,
  `address` INT(10) NULL DEFAULT NULL,
  `dateCreated` timestamp NULL DEFAULT NULL,
  `dateModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idplace`),
  INDEX `fk_place_1_idx` (`address` ASC),
  CONSTRAINT `fk_place_1` FOREIGN KEY (`address`) REFERENCES `postalAddress` (`idpostalAddress`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8;

--
-- relational table with imageObject

CREATE TABLE IF NOT EXISTS `place_has_imageObject` (
  `idplace` INT(10) NOT NULL,
  `idimageObject` INT(10) NOT NULL,
  `position` INT(5) NULL,
  `caption` VARCHAR(255) NULL,
  `representativeOfPage` TINYINT(1) NULL,
  PRIMARY KEY (`idplace`, `idimageObject`),
  INDEX `fk_place_has_imageObject_imageObject1_idx` (`idimageObject` ASC),
  INDEX `fk_place_has_imageObject_place1_idx` (`idplace` ASC),
  CONSTRAINT `fk_place_has_imageObject_place1` FOREIGN KEY (`idplace`) REFERENCES `place` (`idplace`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_place_has_imageObject_imageObject1` FOREIGN KEY (`idimageObject`) REFERENCES `imageObject` (`idimageObject`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8;

DROP TRIGGER IF EXISTS `place_has_imageObject_BEFORE_INSERT`;

CREATE TRIGGER `place_has_imageObject_BEFORE_INSERT` BEFORE INSERT ON `place_has_imageObject` FOR EACH ROW
BEGIN
    DECLARE count INT;
    SET count = (SELECT COUNT(*) FROM `place_has_imageObject` WHERE `idplace`=NEW.`idplace`);
    IF NEW.`position`='' OR NEW.`position` IS NULL
        THEN SET NEW.`position`= count+1;
    END IF;
END;
