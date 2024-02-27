CREATE TABLE IF NOT EXISTS `trip` (
   `idtrip` INT NOT NULL AUTO_INCREMENT,
   `provider` INT NOT NULL,
   `providerType` VARCHAR(45) NOT NULL,
   `name` VARCHAR(180) NOT NULL,
   `description` TEXT NULL DEFAULT NULL,
   `disambiguatingDescription` TEXT NULL DEFAULT NULL,
   `arrivalDate` DATE NULL DEFAULT NULL,
   `departureDate` DATE NULL DEFAULT NULL,
   `arrivalTime` TIME NULL DEFAULT NULL,
   `departureTime` TIME NULL DEFAULT NULL,
   `dateModified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   `dateCreated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
   PRIMARY KEY (`idtrip`),
   UNIQUE INDEX `nome_pacotes` (`name` ASC),
   INDEX `idx_2` (`idtrip` ASC, `name` ASC),
   INDEX `idx_1` (`idtrip` ASC),
   INDEX `idx_3` (`provider` ASC, `idtrip` ASC)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `trip_has_imageObject` (
   `idtrip` INT NOT NULL,
   `idimageObject` INT NOT NULL,
   `caption` VARCHAR(50) NULL DEFAULT NULL,
   `position` INT UNSIGNED NOT NULL,
   `representativeOfPage` INT NULL DEFAULT NULL,
   PRIMARY KEY (`idtrip`, `idimageObject`),
   INDEX `fk_trip_has_imageObject_2_idx` (`idimageObject` ASC),
   CONSTRAINT `fk_trip_has_imageObject_1` FOREIGN KEY (`idtrip`) REFERENCES `trip` (`idtrip`) ON DELETE CASCADE ON UPDATE NO ACTION,
   CONSTRAINT `fk_trip_has_imageObject_2` FOREIGN KEY (`idimageObject`) REFERENCES `imageObject` (`idimageObject`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `trip_has_propertyValue` (
 `idtrip` INT NOT NULL,
 `idpropertyValue` INT NOT NULL,
 PRIMARY KEY (`idtrip`, `idpropertyValue`),
 INDEX `fk_trip_has_propertyValue_2_idx` (`idpropertyValue` ASC),
 CONSTRAINT `fk_trip_has_propertyValue_1` FOREIGN KEY (`idtrip`) REFERENCES `trip` (`idtrip`) ON DELETE CASCADE,
 CONSTRAINT `fk_trip_has_propertyValue_2` FOREIGN KEY (`idpropertyValue`) REFERENCES `propertyValue` (`idpropertyValue`) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `trip_has_trip` (
    `idHasPart` INT NOT NULL,
    `idIsPartOf` INT NOT NULL,
    PRIMARY KEY (`idHasPart`, `idIsPartOf`),
    INDEX `fk_trip_has_trip_2_idx` (`idIsPartOf` ASC),
    CONSTRAINT `fk_trip_has_trip_1` FOREIGN KEY (`idHasPart`) REFERENCES `trip` (`idtrip`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `fk_trip_has_trip_2`   FOREIGN KEY (`idIsPartOf`) REFERENCES `trip` (`idtrip`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB;
