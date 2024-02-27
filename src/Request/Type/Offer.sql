--
-- CREATE TABLE offer
--


CREATE TABLE IF NOT EXISTS `offer` (
    `idoffer` INT NOT NULL AUTO_INCREMENT,
    `itemOffered` INT NOT NULL,
    `itemOfferedType` VARCHAR(45) NOT NULL,
    `offeredBy` INT NULL,
    `offeredByType` VARCHAR(45) NULL,
    `price` FLOAT NOT NULL,
    `priceCurrency` VARCHAR(45) NOT NULL DEFAULT 'R$',
    `validThrough` DATETIME NULL DEFAULT NULL,
    `availability` VARCHAR(45) NULL,
    `elegibleQuantity` INT NULL DEFAULT NULL,
    `elegibleDuration` VARCHAR(45) NULL,
    `dateCreated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `dateModified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`idoffer`)
) ENGINE = InnoDB;
