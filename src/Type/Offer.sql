--
-- CREATE TABLE offer
-- -- relationals
-- -- quantitativeValue

CREATE TABLE IF NOT EXISTS `offer` (
    `idoffer` INT NOT NULL AUTO_INCREMENT,
    `itemOffered` INT NOT NULL,
    `itemOfferedType` VARCHAR(45) NOT NULL,
    `price` FLOAT NOT NULL,
    `priceCurrency` VARCHAR(45) NOT NULL DEFAULT 'R$',
    `validThrough` DATETIME NULL,
    `availability` VARCHAR(45) NULL,
    `elegibleDuration` INT NULL,
    `elegibleQuantity` INT NULL,
PRIMARY KEY (`idoffer`),
    CONSTRAINT `fk_offer_quantitativeValue1` FOREIGN KEY (`elegibleDuration`) REFERENCES `quantitativeValue` (`idquantitativeValue`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB;

CREATE TABLE `quantitativeValue` (
     `idquantitativeValue` int NOT NULL AUTO_INCREMENT,
     `value` varchar(45) NOT NULL,
     `unitText` varchar(45) NOT NULL,
     `maxValue` varchar(45) DEFAULT NULL,
     `minValue` varchar(45) DEFAULT NULL,
     PRIMARY KEY (`idquantitativeValue`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

