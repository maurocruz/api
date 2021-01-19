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
  `providerType` VARCHAR(45) NULL,
  `dateCreated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dateModified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idservice`)
) ENGINE = InnoDB;
