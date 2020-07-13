/**
 * Author:  Mauro Cruz <maurocruz@pirenopolis.tur.br>
 * Created: 27 de fev de 2020
 */

--
-- CREATE TABLE offer REQUIRE
-- -- imageObject
-- -- relational quantitativeValue
--

CREATE TABLE IF NOT EXISTS `quantitativeValue` (
  `idquantitativeValue` INT(10) NOT NULL AUTO_INCREMENT,
  `value` VARCHAR(125) NULL,
  `maxValue` VARCHAR(125) NULL,
  `minValue` VARCHAR(125) NULL,
  `unitText` VARCHAR(125) NULL,
  `unitCode` VARCHAR(125) NULL,
  PRIMARY KEY (`idquantitativeValue`))
ENGINE = InnoDB;

--
-- TABLE offer
--
CREATE TABLE IF NOT EXISTS `offer` (
  `idoffer` INT(10) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NULL,
  `description` TEXT NULL,
  `validThrough` DATETIME NULL,
  `price` DECIMAL(8,2) NULL,
  `priceCurrency` VARCHAR(24) NULL,
  `priceSpecification` VARCHAR(100) NULL,
  `priceValidUntil` DATETIME NULL,
  `itemCondition` TEXT NULL,
  `itemOfferedId` INT(10) NULL,
  `itemOfferedType` VARCHAR(45) NULL,
  `eligibleQuantity` INT(10) NULL,
  `leaseLenght` INT(10) NULL,
  `inventoryLevel` INT(10) NULL,
  PRIMARY KEY (`idoffer`),
  INDEX `fk_offer_quantitativeValue1_idx` (`leaseLenght` ASC),
  INDEX `fk_offer_quantitativeValue2_idx` (`inventoryLevel` ASC),
  INDEX `fk_offer_quantitativeValue3_idx` (`eligibleQuantity` ASC),
  CONSTRAINT `fk_offer_quantitativeValue1` FOREIGN KEY (`leaseLenght`) REFERENCES `quantitativeValue` (`idquantitativeValue`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_offer_quantitativeValue2` FOREIGN KEY (`inventoryLevel`) REFERENCES `quantitativeValue` (`idquantitativeValue`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_offer_quantitativeValue3` FOREIGN KEY (`eligibleQuantity`) REFERENCES `quantitativeValue` (`idquantitativeValue`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8;
