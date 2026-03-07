
-- SERVICE
CREATE TABLE IF NOT EXISTS `service` (
  `idservice` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thing` INT UNSIGNED NOT NULL,
  `provider` INT UNSIGNED NOT NULL,
  `category` VARCHAR(100) NULL DEFAULT NULL,
  `serviceType` VARCHAR(255) NULL DEFAULT NULL,
  `termsOfService` TEXT NULL DEFAULT NULL,
  `isRelatedTo` INT UNSIGNED NULL DEFAULT NULL,
  `serviceOutput` INT UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`idservice`, `thing`),
  INDEX `fk_service_thing_idx` (`thing`),
  INDEX `fk_service_provider_idx` (`provider`),
  CONSTRAINT `fk_service_provider` FOREIGN KEY (`provider`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE,
  CONSTRAINT `fk_service_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;
