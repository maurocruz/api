--
-- WEBSITE
CREATE TABLE IF NOT EXISTS `webSite` (
  `idwebSite` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thing` INT UNSIGNED NOT NULL,
  `creativeWork` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`idwebSite`, `creativeWork`, `thing`),
  INDEX `fk_webSite_thing_idx` (`thing`),
  INDEX `fk_webSite_creativeWork_idx` (`creativeWork`),
  CONSTRAINT `fk_webSite_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE,
  CONSTRAINT `fk_webSite_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;

--
-- WEBPAGE
CREATE TABLE IF NOT EXISTS `webPage` (
  `idwebPage` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thing` INT UNSIGNED NOT NULL,
  `creativeWork` INT UNSIGNED NOT NULL,
  `isPartOf` INT NULL DEFAULT NULL,
  `breadcrumb` TEXT NULL DEFAULT NULL,
  `primaryImageOfPage` INT(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`idwebPage`, `creativeWork`, `thing`),
  INDEX `fk_webPage_thing_idx` (`thing`),
  INDEX `fk_webPage_creativeWork_idx` (`creativeWork`),
  CONSTRAINT `fk_webPage_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE,
  CONSTRAINT `fk_webPage_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;

--
-- WEBPAGE ELEMENT
CREATE TABLE IF NOT EXISTS `webPageElement` (
  `idwebPageElement` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `creativeWork` INT UNSIGNED NOT NULL,
  `cssSelector` TEXT NULL DEFAULT NULL,
  `thing` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`idwebPageElement`, `creativeWork`, `thing`),
  INDEX `fk_webPageElement_thing_idx` (`thing`),
  INDEX `fk_webPageElement_creativeWork_idx` (`creativeWork`),
  CONSTRAINT `fk_webPageElement_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE,
  CONSTRAINT `fk_webPageElement_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;


