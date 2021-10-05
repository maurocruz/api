
/**
 * Author:  Mauro Cruz <maurocruz@pirenopolis.tur.br>
 * Created: 25 de fev de 2020
 */

--
-- CREATE TABLE organization REQUIRE
-- -- postalAddress
-- -- contactPoint
-- -- place
-- -- imageObject
-- -- person
CREATE TABLE IF NOT EXISTS `organization`
(
    `idorganization`            INT          NOT NULL AUTO_INCREMENT,
    `additionalType`            VARCHAR(255) NULL     DEFAULT NULL,
    `name`                      VARCHAR(255) NOT NULL,
    `description`               TEXT         NULL     DEFAULT NULL,
    `disambiguatingDescription` TEXT         NULL,
    `legalName`                 VARCHAR(255) NULL     DEFAULT NULL,
    `taxId`                     VARCHAR(24)  NULL     DEFAULT NULL,
    `url`                       VARCHAR(255) NULL     DEFAULT NULL,
    `hasOfferCatalog`           TEXT         NULL     DEFAULT NULL,
    `location`                  INT          NULL     DEFAULT NULL,
    `dateCreated`               DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `dateModified`              TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`idorganization`),
    INDEX `fk_organization_1_idx` (`location` ASC),
    CONSTRAINT `fk_organization_1` FOREIGN KEY (`location`) REFERENCES `place` (`idplace`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE = InnoDB;

--
-- relational table with ImageObject
--
CREATE TABLE IF NOT EXISTS `organization_has_imageObject` (
  `idorganization` INT(10) NOT NULL,
  `idimageObject` INT(10) NOT NULL,
  `position` INT NULL DEFAULT NULL,
  `representativeOfPage` TINYINT NULL DEFAULT NULL,
  `caption` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`idorganization`, `idimageObject`),
  INDEX `idx_1` (`representativeOfPage`, `idimageObject` ASC),
  INDEX `fk_organization_has_imageObject_2_idx` (`idimageObject` ASC),
  CONSTRAINT `fk_organization_has_imageObject_1` FOREIGN KEY (`idorganization`) REFERENCES `organization` (`idorganization`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_organization_has_imageObject_2` FOREIGN KEY (`idimageObject`) REFERENCES `imageObject` (`idimageObject`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8;

DROP TRIGGER IF EXISTS `organization_has_imageObject_BEFORE_INSERT`;
DELIMITER $$
CREATE DEFINER = CURRENT_USER TRIGGER `organization_has_imageObject_BEFORE_INSERT` BEFORE INSERT ON `organization_has_imageObject` FOR EACH ROW
BEGIN
    DECLARE count INT;
    SET count = (SELECT COUNT(*) FROM `organization_has_imageObject` WHERE `idorganization`=NEW.`idorganization`);
    IF NEW.`position`='' OR NEW.`position` IS NULL
    THEN SET NEW.`position`= count+1;
    END IF;
END$$
DELIMITER ;

--
-- relational table with person
--
CREATE TABLE IF NOT EXISTS `organization_has_person` (
  `idOrganization` INT(10) NOT NULL,
  `idperson` INT(10) NOT NULL,
  `jobTitle` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`idOrganization`, `idperson`),
  INDEX `fk_organization_has_person_2_idx` (`idperson` ASC),
  CONSTRAINT `fk_organization_has_person_1` FOREIGN KEY (`idOrganization`) REFERENCES `organization` (`idorganization`) ON DELETE CASCADE ON UPDATE NO ACTION, 
  CONSTRAINT `fk_organization_has_person_2` FOREIGN KEY (`idperson`) REFERENCES `person` (`idperson`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8;

--
-- relational table with contactPoint
--
CREATE TABLE IF NOT EXISTS `organization_has_contactPoint` (
  `idorganization` INT(10) NOT NULL,
  `idcontactPoint` INT(10) NOT NULL,
  PRIMARY KEY (`idorganization`, `idcontactPoint`),
  INDEX `fk_organization_has_contactPoint_2_idx` (`idcontactPoint` ASC),
  CONSTRAINT `fk_organization_has_contactPoint_1` FOREIGN KEY (`idorganization`) REFERENCES `organization` (`idorganization`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_organization_has_contactPoint_2` FOREIGN KEY (`idcontactPoint`) REFERENCES `contactPoint` (`idcontactPoint`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8;