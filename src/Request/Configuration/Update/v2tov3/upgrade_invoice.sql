
CREATE PROCEDURE upgrade_invoice()
  BEGIN
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
      DROP COLUMN `customerType`,
      DROP COLUMN `providerType`;
  END ;

