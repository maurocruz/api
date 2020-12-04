--
-- CREATE TABLE offer
-- -- relationals
-- -- quantitativeValue

CREATE TABLE IF NOT EXISTS `offer` (
    `idoffer` INT NOT NULL AUTO_INCREMENT,
    `price` FLOAT NOT NULL,
    `priceCurrency` VARCHAR(45) NOT NULL DEFAULT 'R$',
    `validThrough` DATETIME NULL,
    `availability` VARCHAR(45) NULL,
    `elegibleDuration` INT NULL,
    PRIMARY KEY (`idoffer`),
    CONSTRAINT `fk_offer_quantitativeValue1` FOREIGN KEY (`elegibleDuration`) REFERENCES `quantitativeValue` (`idquantitativeValue`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB;
