
CREATE TABLE IF NOT EXISTS `product` (
  `idproduct` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thing` INT UNSIGNED NOT NULL,
  `brand` VARCHAR(100) NULL,
  `category` VARCHAR(64) NULL DEFAULT NULL,
  `color` VARBINARY(45) NULL DEFAULT NULL,
  `keywords` VARCHAR(255) NULL DEFAULT NULL,
  `manufacturer` INT UNSIGNED NULL DEFAULT NULL,
  `material` VARCHAR(45) NULL DEFAULT NULL,
  `model` VARCHAR(45) NULL DEFAULT NULL,
  `mpn` VARCHAR(45) NULL DEFAULT NULL,
  `productionDate` DATE NULL,
  `purchaseDate` DATE NULL,
  PRIMARY KEY (`idproduct`, `thing`),
  INDEX `fk_product_thing_idx` (`thing`),
  INDEX `fk_product_manufacturer_idx` (`manufacturer`),
  CONSTRAINT `fk_product_manufacturer` FOREIGN KEY (`manufacturer`) REFERENCES `thing` (`idthing`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `fk_product_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
)  ENGINE = InnoDB;
