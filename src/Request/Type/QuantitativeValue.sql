--
-- CREATE TABLE quantitativeValue
--

CREATE TABLE IF NOT EXISTS `quantitativeValue` (
   `idquantitativeValue` INT NOT NULL AUTO_INCREMENT,
   `value` VARCHAR(45) NOT NULL,
   `unitText` VARCHAR(45) NOT NULL,
   `maxValue` VARCHAR(45) NULL,
   `minValue` VARCHAR(45) NULL,
   PRIMARY KEY (`idquantitativeValue`)
) ENGINE = InnoDB;