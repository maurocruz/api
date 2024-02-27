
-- CONTACT POINT

CREATE TABLE IF NOT EXISTS `contactPoint` (
  `idcontactPoint` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thing` INT NOT NULL,
  `name` VARCHAR(180) DEFAULT NULL,
  `contactType` VARCHAR(160) DEFAULT NULL,
  `telephone` VARCHAR(45) DEFAULT NULL,
  `email` VARCHAR(120) DEFAULT NULL,
  `whatsapp` TINYINT DEFAULT NULL,
  `obs` TEXT,
  `position` INT DEFAULT NULL,
  PRIMARY KEY (`idcontactPoint`,`thing`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `postalAddress` (
  `idpostalAddress` INT NOT NULL AUTO_INCREMENT,
  `thing` INT NOT NULL,
  `streetAddress` varchar(255) DEFAULT NULL,
  `addressLocality` varchar(80) DEFAULT NULL,
  `addressRegion` varchar(45) DEFAULT NULL,
  `addressCountry` varchar(45) DEFAULT NULL,
  `postalCode` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idpostalAddress`,'thing')
) ENGINE=InnoDB;