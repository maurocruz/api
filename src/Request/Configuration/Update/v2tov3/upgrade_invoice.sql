-- ALTER TABLE
ALTER TABLE `invoice`
  CHANGE COLUMN `idinvoice` `idinvoice` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  CHANGE COLUMN `paymentDueDate` `scheduledPaymentDate` DATE NOT NULL,
  CHANGE COLUMN `paymentDate` `paymentDueDate` DATE DEFAULT NULL,
  CHANGE COLUMN `referencesOrder` `referencesOrder` INT UNSIGNED NOT NULL ,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idinvoice`,`referencesOrder`);

DELETE `invoice` FROM invoice
  LEFT JOIN `order` ON `order`.idorder = `invoice`.referencesOrder
WHERE `order`.idorder IS NULL ;

UPDATE `invoice`
  JOIN `order` ON `order`.idorder = `invoice`.referencesOrder
SET `invoice`.customer=`order`.customer, `invoice`.provider = `order`.seller;

ALTER TABLE `invoice`
  CHANGE COLUMN `customer` `customer` INT UNSIGNED NOT NULL ,
  CHANGE COLUMN `provider` `provider` INT UNSIGNED NOT NULL ,
  DROP COLUMN `customerType`,
  DROP COLUMN `providerType`;

-- add foreign key
ALTER TABLE `invoice`
  ADD KEY `fk_invoice_order_idx` (`referencesOrder`),
  ADD CONSTRAINT `fk_invoice_order` FOREIGN KEY (`referencesOrder`) REFERENCES `order` (`idorder`) ON DELETE CASCADE ON UPDATE NO ACTION;

