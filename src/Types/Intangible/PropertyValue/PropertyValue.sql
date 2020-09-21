--
-- CREATE TABLE propertyValue
--

CREATE TABLE IF NOT EXISTS `propertyValue` (
  `idpropertyValue` INT(10) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `value` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`idpropertyValue`)
) ENGINE = InnoDB;