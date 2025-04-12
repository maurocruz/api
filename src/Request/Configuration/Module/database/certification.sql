--
-- CERTIFICATION
--
CREATE TABLE IF NOT EXISTS `certification`(
  `idcertification` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `creativeWork` INT UNSIGNED NOT NULL,
  `thing` INT UNSIGNED NOT NULL,
  `about` INT UNSIGNED NOT NULL,
  `auditDate` DATETIME DEFAULT NULL,
  `certificationIdentification` VARCHAR(120) NOT NULL,
  `certificationStatus` TINYINT default 0,
  `datePublished` DATETIME DEFAULT NULL,
  `expires` DATETIME DEFAULT NULL,
  `issuedBy` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`idcertification`, `creativeWork`, `thing`),
  INDEX `certification_certificationIdentification_idx` (`certificationIdentification`),
  KEY `certification_creativeWork_idx` (`creativeWork`),
  KEY `certification_organization_idx` (`issuedBy`),
  KEY `certification_about_idx` (`about`),
  CONSTRAINT `fk_certification_creativeWork_idx` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE,
  CONSTRAINT `fk_certification_organization_idx` FOREIGN KEY (`issuedBy`) REFERENCES `organization` (`idorganization`) ON DELETE CASCADE
) ENGINE = InnoDB;
