--
-- WEB SITE SCHEMA
-- --

CREATE TABLE IF NOT EXISTS `webSite` (
  `idwebSite` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `creativeWork` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`idwebSite`,`creativeWork`),
  CONSTRAINT `fk_webSite_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE
) ENGINE = InnoDB;

--
-- WEB PAGE
--

CREATE TABLE IF NOT EXISTS `webPage` (
  `idwebPage` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `creativeWork` INT UNSIGNED NOT NULL,
  `breadcrumb` text,
  `primaryImageOfPage` INT UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`idwebPage`,`creativeWork`),
  CONSTRAINT `fk_webPage_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE
) ENGINE = InnoDB;

--
-- WEB PAGE ELEMENT
CREATE TABLE IF NOT EXISTS `webPageElement` (
  `idwebPageElement` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `creativeWork` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`idwebPageElement`,`creativeWork`),
  CONSTRAINT `fk_webPageElement_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE
) ENGINE = InnoDB;

