
-- POSTAL ADDRESS
CREATE TABLE IF NOT EXISTS `postalAddress` (
`idpostalAddress` INT UNSIGNED NOT NULL AUTO_INCREMENT,
`streetAddress` VARCHAR(255) NULL DEFAULT NULL,
`addressLocality` VARCHAR(80) NULL DEFAULT NULL,
`addressRegion` VARCHAR(45) NULL DEFAULT NULL,
`addressCountry` VARCHAR(45) NULL DEFAULT NULL,
`postalCode` VARCHAR(45) NULL DEFAULT NULL,
PRIMARY KEY (`idpostalAddress`)
) ENGINE = InnoDB;

-- GEO COORDINATES
CREATE TABLE IF NOT EXISTS `geoCoordinates` (
  `idgeoCoordinates` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `address` INT UNSIGNED NULL DEFAULT NULL,
  `elevation` VARCHAR(125) NULL DEFAULT NULL,
  `latitude` DECIMAL(18,14) NULL DEFAULT NULL,
  `longitude` DECIMAL(18,14) NULL DEFAULT NULL,
  PRIMARY KEY (`idgeoCoordinates`),
  INDEX `fk_geoCoordinates_postalAddress_idx` (`address`),
  CONSTRAINT `fk_geoCoordinates_postalAddress` FOREIGN KEY (`address`) REFERENCES `postalAddress` (`idpostalAddress`) ON DELETE SET NULL
) ENGINE = InnoDB;

-- PLACE
CREATE TABLE IF NOT EXISTS `place` (
  `idplace` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thing` INT UNSIGNED NOT NULL,
  `geo` INT UNSIGNED NULL DEFAULT NULL,
  `keywords` VARCHAR(255) NULL DEFAULT NULL,
  `publicAccess` TINYINT NULL DEFAULT '0',
  PRIMARY KEY (`idplace`, `thing`),
  INDEX `fk_place_thing_idx` (`thing` ASC) VISIBLE,
  INDEX `fk_place_geo_idx` (`geo` ASC) VISIBLE,
  CONSTRAINT `fk_place_geo` FOREIGN KEY (`geo`) REFERENCES `geoCoordinates` (`idgeoCoordinates`) ON DELETE SET NULL,
  CONSTRAINT `fk_place_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;

