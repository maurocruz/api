
-- THING
CREATE TABLE IF NOT EXISTS `thing` (
  `idthing` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `additionalType` VARCHAR(255) NULL DEFAULT NULL,
  `alternateName` VARCHAR(255) NULL DEFAULT NULL,
  `dateCreated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dateModified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `description` TEXT NULL DEFAULT NULL,
  `disambiguatingDescription` VARCHAR(255) NULL DEFAULT NULL,
  `image` VARCHAR(255) NULL DEFAULT NULL,
  `mainEntityOfPage` VARCHAR(255) NULL DEFAULT NULL,
  `name` VARCHAR(255) NOT NULL,
  `type` VARCHAR(45) NOT NULL,
  `url` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`idthing`),
  KEY (`name`,`disambiguatingDescription`,`url`),
  CONSTRAINT `thing_check_name` CHECK (`name` <> '')
) ENGINE = InnoDB;

-- RELATIONAL THING HAS THING
CREATE TABLE IF NOT EXISTS `thing_has_thing` (
  `idHasPart` INT UNSIGNED NOT NULL,
  `typeHasPart` VARCHAR(48) NOT NULL,
  `idIsPartOf` INT UNSIGNED NOT NULL,
  `typeIsPartOf` VARCHAR(48) NOT NULL,
  `caption` VARCHAR(255) NULL DEFAULT NULL,
  `position` INT UNSIGNED NULL DEFAULT '1',
  PRIMARY KEY (`idHasPart`, `idIsPartOf`, `typeHasPart`, `typeIsPartOf`),
  INDEX `fk_thing_has_thing_idHasPart_idx` (`idHasPart` ASC) VISIBLE,
  INDEX `fk_thing_has_thing_idIsPartOf_idx` (`idIsPartOf` ASC) VISIBLE,
  CONSTRAINT `fk_thing_has_thing_idHasPart` FOREIGN KEY (`idHasPart`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE,
  CONSTRAINT `fk_thing_has_thing_idIsPartOf` FOREIGN KEY (`idIsPartOf`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;

-- PROPERTY VALUE
CREATE TABLE IF NOT EXISTS `propertyValue` (
  `idpropertyValue` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `value` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`idpropertyValue`)
) ENGINE = InnoDB;

