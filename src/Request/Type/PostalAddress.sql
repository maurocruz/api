
--
-- CREATE TABLE postalAddress
--

CREATE TABLE IF NOT EXISTS `postalAddress` (
  `idpostalAddress` INT(10) NOT NULL AUTO_INCREMENT,
  `streetAddress` VARCHAR(255) NULL DEFAULT NULL,
  `addressLocality` VARCHAR(80) NULL DEFAULT NULL,
  `addressRegion` VARCHAR(45) NULL DEFAULT NULL,
  `addressCountry` VARCHAR(45) NULL DEFAULT NULL,
  `postalCode` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`idpostalAddress`)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8;

