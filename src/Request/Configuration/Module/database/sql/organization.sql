--
-- ORGANIZATION
--

CREATE TABLE IF NOT EXISTS `organization` (
  `idorganization` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thing` INT UNSIGNED NOT NULL,
  `areaServed` INT UNSIGNED DEFAULT NULL,
  `hasOfferCatalog` text,
  `legalName` VARCHAR(124) DEFAULT NULL,
  `location` INT UNSIGNED DEFAULT NULL,
  `logo` INT UNSIGNED DEFAULT NULL,
  `taxId` VARCHAR(24) DEFAULT NULL,
  PRIMARY KEY (`idorganization`,`thing`),
  key `fk_organization_thing_idx` (`thing`),
  CONSTRAINT `fk_organization_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;
