
-- ORDER
CREATE TABLE IF NOT EXISTS `order` (
  `idorder` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer` INT UNSIGNED NOT NULL,
  `seller` INT UNSIGNED NOT NULL,
  `orderDate` DATE NULL DEFAULT NULL,
  `paymentDueDate` DATE NULL DEFAULT NULL,
  `tags` VARCHAR(255) CHARACTER SET 'latin1' NULL DEFAULT NULL,
  `orderStatus` VARCHAR(45) NULL DEFAULT NULL,
  `discount` INT NULL DEFAULT NULL,
  PRIMARY KEY (`idorder`, `customer`, `seller`),
  INDEX `fk_order_customer_idx` (`customer`),
  INDEX `fk_order_seller_idx` (`seller`),
  CONSTRAINT `fk_order_customer` FOREIGN KEY (`customer`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE,
  CONSTRAINT `fk_order_seller` FOREIGN KEY (`seller`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;

-- ORDER ITEM
CREATE TABLE IF NOT EXISTS `orderItem` (
  `idorderItem` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `orderItemNumber` INT UNSIGNED NOT NULL,
  `offer` INT UNSIGNED NOT NULL,
  `orderedItem` INT UNSIGNED NOT NULL,
  `orderQuantity` INT NOT NULL DEFAULT '1',
  PRIMARY KEY (`idorderItem`, `orderItemNumber`),
  INDEX `fk_orderItem_offer_idx` (`offer`),
  INDEX `fk_orderedItem_thing_idx` (`orderedItem`),
  INDEX `fk_orderItemNumber_thing_idx` (`orderItemNumber`),
  CONSTRAINT `fk_orderedItem_offer` FOREIGN KEY (`offer`) REFERENCES `offer` (`idoffer`) ON DELETE CASCADE,
  CONSTRAINT `fk_orderedItem_thing` FOREIGN KEY (`orderedItem`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE,
  CONSTRAINT `fk_orderItemNumber_thing` FOREIGN KEY (`orderItemNumber`) REFERENCES `order` (`idorder`) ON DELETE CASCADE
) ENGINE = InnoDB;

-- INVOICE
CREATE TABLE IF NOT EXISTS `invoice` (
  `idinvoice` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer` INT UNSIGNED NOT NULL,
  `referencesOrder` INT UNSIGNED NOT NULL,
  `provider` INT UNSIGNED NOT NULL,
  `totalPaymentDue` FLOAT NOT NULL,
  `scheduledPaymentDate` DATE NOT NULL,
  `paymentDueDate` DATE NULL DEFAULT NULL,
  `paymentStatus` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`idinvoice`, `referencesOrder`),
  INDEX `fk_invoice_order_idx` (`referencesOrder`),
  CONSTRAINT `fk_invoice_order` FOREIGN KEY (`referencesOrder`) REFERENCES `order` (`idorder`) ON DELETE CASCADE
) ENGINE = InnoDB;

-- OFFER
CREATE TABLE IF NOT EXISTS `offer` (
  `idoffer` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thing` INT UNSIGNED NOT NULL,
  `itemOffered` INT UNSIGNED NOT NULL,
  `offeredBy` INT UNSIGNED NOT NULL,
  `price` FLOAT NOT NULL,
  `priceCurrency` VARCHAR(45) NOT NULL DEFAULT 'BRL',
  `validThrough` DATETIME NULL DEFAULT NULL,
  `availability` VARCHAR(45) NULL DEFAULT NULL,
  `eligibleQuantity` VARCHAR(45) NULL DEFAULT NULL,
  `eligibleDuration` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`idoffer`, `itemOffered`, `offeredBy`),
  INDEX `fk_offer_thing_idx` (`thing`),
  INDEX `fk_offer_itemOffered_thing_idx` (`itemOffered`),
  INDEX `fk_offer_offeredBy_thing_idx` (`offeredBy`),
  CONSTRAINT `fk_offer_itemOffered_thing` FOREIGN KEY (`itemOffered`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE,
  CONSTRAINT `fk_offer_offeredBy_thing` FOREIGN KEY (`offeredBy`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE,
  CONSTRAINT `fk_offer_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;
