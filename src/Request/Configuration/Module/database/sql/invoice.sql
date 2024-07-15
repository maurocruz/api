CREATE TABLE `invoice` (
  `idinvoice` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thing` INT UNSIGNED NOT NULL,
  `customer` INT UNSIGNED DEFAULT NULL,
  `customerType` varchar(45) DEFAULT NULL,
  `referencesOrder` INT UNSIGNED NOT NULL,
  `provider` INT UNSIGNED DEFAULT NULL,
  `providerType` VARCHAR(45) DEFAULT NULL,
  `totalPaymentDue` FLOAT NOT NULL,
  `paymentDueDate` DATE NOT NULL,
  `paymentDate` DATE DEFAULT NULL,
  `paymentStatus` VARCHAR(45) DEFAULT NULL,
  PRIMARY KEY (`idinvoice`,`thing`,`referencesOrder`),
  KEY `invoice_paymentDueDate_idx` (`paymentDueDate`),
  KEY `invoice_referencesOrder_idx` (`referencesOrder`),
  CONSTRAINT `fk_invoice_order1` FOREIGN KEY (`referencesOrder`) REFERENCES `order` (`idorder`) ON DELETE CASCADE
) ENGINE=InnoDB;
