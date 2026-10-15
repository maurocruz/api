
CREATE TABLE IF NOT EXISTS `product` (
  `idproduct` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thing` INT UNSIGNED NOT NULL,
  `brand` VARCHAR(100) NULL,
  `category` VARCHAR(64) NULL DEFAULT NULL,
  `color` VARBINARY(45) NULL DEFAULT NULL,
  `keywords` VARCHAR(255) NULL DEFAULT NULL,
  `manufacturer` VARCHAR(255) NULL DEFAULT NULL,
  `material` VARCHAR(45) NULL DEFAULT NULL,
  `model` VARCHAR(45) NULL DEFAULT NULL,
  `mpn` VARCHAR(45) NULL DEFAULT NULL,
  `productionDate` DATE NULL,
  `purchaseDate` DATE NULL,
  PRIMARY KEY (`idproduct`, `thing`),
  INDEX `fk_product_thing_idx` (`thing`),
  CONSTRAINT `fk_product_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
)  ENGINE = InnoDB;
