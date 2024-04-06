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

CREATE TABLE `programMembership` (
  `idprogramMembership` int unsigned NOT NULL AUTO_INCREMENT,
  `thing` int unsigned NOT NULL,
  `hostingOrganization` int unsigned NOT NULL,
  `member` int unsigned NOT NULL,
  `membershipNumber` varchar(64) DEFAULT NULL,
  `membershipPointsEarned` varchar(124) DEFAULT NULL,
  `programName` varchar(124) NOT NULL,
  PRIMARY KEY (`idprogramMembership`,`thing`,`hostingOrganization`,`member`),
  KEY `fk_programMembership_thing_idx` (`thing`),
  KEY `fk_programMembership_hostOrganization_idx` (`hostingOrganization`),
  KEY `fk_programMembership_member_idx` (`member`),
  CONSTRAINT `fk_programMembership_hostOrganization` FOREIGN KEY (`hostingOrganization`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE,
  CONSTRAINT `fk_programMembership_member` FOREIGN KEY (`member`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE,
  CONSTRAINT `fk_programMembership_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE=InnoDB;
