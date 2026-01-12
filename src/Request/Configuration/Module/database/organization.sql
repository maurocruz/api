--
-- ORGANIZATION
--
CREATE TABLE IF NOT EXISTS `organization` (
  `idorganization` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `logo` INT UNSIGNED NULL DEFAULT NULL,
  `thing` INT UNSIGNED NOT NULL,
  `legalName` VARCHAR(124) NULL DEFAULT NULL,
  `taxId` VARCHAR(24) NULL DEFAULT NULL,
  `hasOfferCatalog` TEXT NULL DEFAULT NULL,
  `location` INT UNSIGNED NULL DEFAULT NULL,
  `address` INT NULL DEFAULT NULL,
  `areaServed` INT UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`idorganization`, `thing`),
  INDEX `fk_organization_thing_idx` (`thing` ASC) VISIBLE,
  INDEX `fk_organization_location_idx` (`location` ASC) VISIBLE,
  CONSTRAINT `fk_organization_location` FOREIGN KEY (`location`) REFERENCES `place` (`idplace`)  ON DELETE CASCADE,
  CONSTRAINT `fk_organization_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;
