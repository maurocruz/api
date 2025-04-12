
-- PRODUCT
CREATE TABLE IF NOT EXISTS `product` (
  `idproduct` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thing` INT UNSIGNED NOT NULL,
  `category` VARCHAR(64) NOT NULL DEFAULT '',
  `manufacturer` INT UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`idproduct`, `thing`),
  INDEX `fk_product_thing_idx` (`thing`),
  INDEX `fk_product_manufacturer_idx` (`manufacturer`),
  CONSTRAINT `fk_product_manufacturer` FOREIGN KEY (`manufacturer`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE,
  CONSTRAINT `fk_product_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;
