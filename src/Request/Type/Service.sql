--
-- CREATE TABLE service
-- Relationships: Offer; Organization; Person; ImageObject

CREATE TABLE IF NOT EXISTS `service` (
  `idservice` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `additionalType` VARCHAR(255) NULL,
  `serviceType` VARCHAR(255) NULL,
  `termsOfService` TEXT NULL,
  `provider` INT NULL,
  `providerType` VARCHAR(45) NULL,
  `dateCreated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dateModified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idservice`)
) ENGINE = InnoDB;

--
-- IMAGE OBJECT
--

CREATE TABLE IF NOT EXISTS `service_has_imageObject` (
    `idservice` INT NOT NULL,
    `idimageObject` INT NOT NULL,
    `caption` VARCHAR(255) NULL,
    `position` TINYINT NULL,
    `representativeOfPage` TINYINT NULL,
    PRIMARY KEY (`idservice`, `idimageObject`),
    INDEX `fk_service_has_imageObject_1_idx` (`idimageObject` ASC),
    INDEX `fk_service_has_imageObject_2_idx` (`idservice` ASC),
    CONSTRAINT `fk_service_has_imageObject_1` FOREIGN KEY (`idimageObject`) REFERENCES `imageObject` (`idimageObject`) ON DELETE CASCADE ON UPDATE RESTRICT,
    CONSTRAINT `fk_service_has_imageObject_2` FOREIGN KEY (`idservice`) REFERENCES `service` (`idservice`)
) ENGINE = InnoDB;

--
-- OFFER
--

CREATE TABLE IF NOT EXISTS `service_has_offer` (
    `idservice` INT NOT NULL,
    `idoffer` INT NOT NULL,
    PRIMARY KEY (`idservice`, `idoffer`),
    INDEX `fk_service_has_offer_1_idx` (`idoffer` ASC),
    INDEX `fk_service_has_offer_2_idx` (`idservice` ASC),
    CONSTRAINT `fk_service_has_offer_1` FOREIGN KEY (`idoffer`) REFERENCES `offer` (`idoffer`) ON DELETE CASCADE ON UPDATE RESTRICT,
    CONSTRAINT `fk_service_has_offer_2` FOREIGN KEY (`idservice`) REFERENCES `service` (`idservice`) ON DELETE CASCADE
)ENGINE = InnoDB;


DROP TRIGGER IF EXISTS `service_has_imageObject_BEFORE_INSERT`;

CREATE TRIGGER `service_has_imageObject_BEFORE_INSERT` BEFORE INSERT ON `service_has_imageObject` FOR EACH ROW
BEGIN
    DECLARE count INT;
    SET count = (SELECT COUNT(*) FROM `service_has_imageObject` WHERE `idservice`=NEW.`idservice`);
    IF NEW.`position`='' OR NEW.`position` IS NULL
    THEN SET NEW.`position`= count+1;
    END IF;
END;