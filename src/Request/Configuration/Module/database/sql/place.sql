
-- POSTAL ADDRESS

CREATE TABLE IF NOT EXISTS `postalAddress` (
  `idpostalAddress` INT NOT NULL AUTO_INCREMENT,
  `streetAddress` VARCHAR(255) NULL DEFAULT NULL,
  `addressLocality` VARCHAR(80) NULL DEFAULT NULL,
  `addressRegion` VARCHAR(45) NULL DEFAULT NULL,
  `addressCountry` VARCHAR(45) NULL DEFAULT NULL,
  `postalCode` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`idpostalAddress`)
) ENGINE = InnoDB;

-- PLACE

CREATE TABLE IF NOT EXISTS `place` (
  `idplace` INT NOT NULL AUTO_INCREMENT,
  `thing` INT NOT NULL,
  `latitude` decimal(18,14) DEFAULT NULL,
  `longitude` decimal(18,14) DEFAULT NULL,
  `elevation` varchar(125) DEFAULT NULL,
  `address` INT DEFAULT NULL,
  PRIMARY KEY (`idplace`,`thing`),
  KEY `fk_place_1_idx` (`address`),
  CONSTRAINT `fk_place_address` FOREIGN KEY (`address`) REFERENCES `postalAddress` (`idpostalAddress`) ON DELETE CASCADE
) ENGINE=InnoDB;
