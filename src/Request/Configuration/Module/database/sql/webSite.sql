--
-- WEB SITE SCHEMA
-- --

CREATE TABLE IF NOT EXISTS `webSite` (
  `idwebSite` int NOT NULL AUTO_INCREMENT,
  `creativeWork` INT NOT NULL,
  PRIMARY KEY (`idwebSite`,`creativeWork`),
  CONSTRAINT `fk_webSite_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB;

--
-- WEB PAGE
--

CREATE TABLE IF NOT EXISTS `webPage` (
  `idwebPage` int NOT NULL AUTO_INCREMENT,
  `creativeWork` INT NOT NULL,
  `breadcrumb` text,
  `primaryImageOfPage` INT DEFAULT NULL,
  `lastReviewed` DATETIME,
  PRIMARY KEY (`idwebPage`,`creativeWork`),
  CONSTRAINT `fk_webPage_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE ON UPDATE NO ACTION,
) ENGINE=InnoDB;

--
-- WEB PAGE ELEMENT
CREATE TABLE IF NOT EXISTS `webPageElement` (
  `idwebPageElement` int NOT NULL AUTO_INCREMENT,
  `creativeWork` INT NOT NULL,
  PRIMARY KEY (`idwebPageElement`,`creativeWork`),
  CONSTRAINT `fk_webPage_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB;

