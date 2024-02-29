--
-- ORGANIZATION
--

CREATE TABLE IF NOT EXISTS `organization` (
  `idorganization` int NOT NULL AUTO_INCREMENT,
  `thing` INT NOT NULL,
  `areaServed` int(10) DEFAULT NULL,
  `hasOfferCatalog` text,
  `legalName` varchar(124) DEFAULT NULL,
  `location` int DEFAULT NULL,
  `taxId` varchar(24) DEFAULT NULL,
  PRIMARY KEY (`idorganization`,`thing`),
  CONSTRAINT `fk_organization_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB;
