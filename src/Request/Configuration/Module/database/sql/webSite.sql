--
-- WEB SITE SCHEMA
-- --

CREATE TABLE IF NOT EXISTS `webSite` (
  `idwebSite` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `creativeWork` INT(10) UNSIGNED NOT NULL,
  `thing` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`idwebSite`,`creativeWork`),
  KEY `fk_webSite_creativeWork` (`creativeWork`),
  CONSTRAINT `fk_webSite_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE
) ENGINE = InnoDB;

--
-- WEB PAGE
--

CREATE TABLE IF NOT EXISTS `webPage` (
  `idwebPage` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `creativeWork` INT(10) UNSIGNED NOT NULL,
  `thing` INT(10) UNSIGNED NOT NULL,
  `breadcrumb` text,
  `primaryImageOfPage` INT(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`idwebPage`,`creativeWork`),
  KEY `fk_webPage_creativeWork` (`creativeWork`),
  CONSTRAINT `fk_webPage_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE
) ENGINE = InnoDB;

--
-- WEB PAGE ELEMENT
CREATE TABLE IF NOT EXISTS `webPageElement` (
  `idwebPageElement` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `creativeWork` INT UNSIGNED NOT NULL,
  `thing` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`idwebPageElement`,`creativeWork`),
  KEY `fk_webPageElement_creativeWork` (`creativeWork`),
  CONSTRAINT `fk_webPageElement_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE
) ENGINE = InnoDB;

