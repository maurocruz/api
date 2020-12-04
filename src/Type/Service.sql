--
-- CREATE TABLE service
-- relationships
-- -- offer
-- -- organization
-- -- person

CREATE TABLE IF NOT EXISTS `service` (
  `idservice` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `additionalType` VARCHAR(255) NULL,
  `serviceType` VARCHAR(255) NULL,
  `termsOfService` TEXT NULL,
  `provider` INT NULL,
  `offers` INT NULL,
  `dateCreated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dateModified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idservice`),
  INDEX `index1` (`provider` ASC),
  INDEX `index2` (`offers` ASC),
  CONSTRAINT `fk_service_organization1` FOREIGN KEY (`provider`) REFERENCES `organization` (`idorganization`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_service_offer1` FOREIGN KEY (`offers`) REFERENCES `offer` (`idoffer`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB;
