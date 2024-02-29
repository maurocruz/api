
-- THING

CREATE TABLE IF NOT EXISTS `thing` (
  `idthing` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `alternateName` VARCHAR(255) NULL,
  `type` VARCHAR(45) NOT NULL,
  `additionalType` VARCHAR(255) NULL,
  `description` VARCHAR(255),
  `disambiguatingDescription` TEXT,
  `url` VARCHAR(255) NULL,
  `dateCreated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dateModified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idthing`),
  KEY (`name`,`description`)
) ENGINE = InnoDB;

-- PROPERTY VALUE

CREATE TABLE IF NOT EXISTS `propertyValue` (
  `idpropertyValue` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thing` INT UNSIGNED NOT NULL,
  `name` varchar(45) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`idpropertyValue`,`thing`),
  CONSTRAINT `FK_propertyValue_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE=InnoDB;
