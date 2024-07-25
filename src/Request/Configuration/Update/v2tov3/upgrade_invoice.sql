
CREATE PROCEDURE upgrade_invoice()
  BEGIN
    -- ALTER TABLE
    ALTER TABLE `invoice`
      CHANGE COLUMN `idinvoice` `idinvoice` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      DROP COLUMN `customer`,
      DROP COLUMN `customerType`,
      DROP COLUMN `provider`,
      DROP COLUMN `providerType`,
      DROP PRIMARY KEY,
      ADD PRIMARY KEY (`idinvoice`,`referencesOrder`);

  END ;

