
--
-- ORDER
--

ALTER TABLE `order`
  CHANGE COLUMN `idorder` `idorder` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  CHANGE COLUMN `customer` `customer` INT UNSIGNED NOT NULL,
  CHANGE COLUMN `seller` `seller` INT UNSIGNED NOT NULL,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idorder`);

-- SET CUSTOMER
UPDATE `order`
  LEFT JOIN `localBusiness` ON `localBusiness`.idlocalBusiness = `order`.customer AND `order`.customerType = 'localBusiness'
  LEFT JOIN organization as org1 ON org1.idorganization = `order`.customer AND `order`.customerType = 'organization'
  LEFT JOIN person ON person.idperson = `order`.customer AND `order`.customerType = 'person'
  LEFT JOIN `organization` ON `organization`.idorganization = `order`.seller
SET customer= IF(customerType='localbusiness', `localBusiness`.thing, IF(customerType = 'organization', org1.thing, `person`.thing)), seller = `organization`.thing
WHERE `order`.customerType IS NOT NULL;

DELETE FROM `order` WHERE `customer`='0' || `seller`='0';

ALTER TABLE `order`
  DROP COLUMN `customerType`,
  DROP COLUMN `sellerType`,
  DROP COLUMN `tipo`,
  DROP COLUMN `valor`,
  DROP COLUMN `dateCreated`,
  DROP COLUMN `dateModified`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idorder`,`customer`,`seller`);

-- add foreign keys
ALTER TABLE `order`
  ADD KEY `fk_order_customer_idx` (`customer`),
  ADD KEY `fk_order_seller_idx` (`seller`),
  ADD CONSTRAINT `fk_order_customer` FOREIGN KEY (`customer`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_order_seller` FOREIGN KEY (`seller`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION;
