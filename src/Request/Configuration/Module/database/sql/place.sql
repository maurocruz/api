
-- PLACE

CREATE TABLE IF NOT EXISTS `place` (
  `idplace` INT NOT NULL AUTO_INCREMENT,
  `thing` INT NOT NULL,
  `latitude` decimal(18,14) DEFAULT NULL,
  `longitude` decimal(18,14) DEFAULT NULL,
  `elevation` varchar(125) DEFAULT NULL,
  `address` INT DEFAULT NULL,
  `rank` INT unsigned NOT NULL DEFAULT '1000',
  `dateCreated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dateModified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idplace`,`thing`),
  KEY `fk_place_1_idx` (`address`),
  CONSTRAINT `fk_place_address` FOREIGN KEY (`address`) REFERENCES `postalAddress` (`idpostalAddress`) ON DELETE CASCADE
) ENGINE=InnoDB;