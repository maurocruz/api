
-- THING

CREATE TABLE IF NOT EXISTS `thing` (
  `idthing` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `additionalType` VARCHAR(255) NULL,
  `alternateName` VARCHAR(255) NULL,
  `dateCreated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dateModified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `description` VARCHAR(255),
  `disambiguatingDescription` TEXT,
  `name` VARCHAR(255) NOT NULL,
  `mainEntityOfPage` VARCHAR(255) DEFAULT NULL,
  `type` VARCHAR(45) NOT NULL,
  `url` VARCHAR(255) NULL,
  PRIMARY KEY (`idthing`),
  KEY (`name`,`description`,`url`),
  CONSTRAINT `thing_check_name` CHECK (`name` <> '')
) ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS `thing_has_thing` (
  `idHasPart` INT UNSIGNED NOT NULL,
  `idIsPartOf` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`idHasPart`,`idIsPartOf`),
  KEY `fk_thing_has_thing_hasPart_idx1` (`idHasPart`),
  KEY `fk_thing_has_thing_isPartOf_idx1` (`idIsPartOf`),
  CONSTRAINT `fk_thing_has_thing_hasPart` FOREIGN KEY (`idHasPart`) REFERENCES `thing` (`idthing`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_thing_has_thing_isPartOf` FOREIGN KEY (`idIsPartOf`) REFERENCES `thing` (`idthing`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB;
