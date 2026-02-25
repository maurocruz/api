
--
-- ORDER ITEM
--

ALTER TABLE `orderItem`
  CHANGE COLUMN `idorderItem` `idorderItem` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  CHANGE COLUMN `offer` `offer` INT UNSIGNED NOT NULL,
  CHANGE COLUMN `referencesOrder` `orderItemNumber` INT UNSIGNED NOT NULL,
  CHANGE COLUMN `orderedItem` `orderedItem` INT UNSIGNED NOT NULL,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idorderItem`);

UPDATE `orderItem`
  LEFT JOIN `service` ON `orderItem`.orderedItem = `service`.idservice AND `orderItem`.orderedItemType='service'
  LEFT JOIN `product` ON `product`.idproduct = `orderItem`.orderedItem AND `orderItem`.orderedItemType='product'
SET `orderItem`.orderedItem= IF(orderedItemType='service',service.thing,product.thing)
WHERE `orderItem`.orderedItemType IS NOT NULL;

DELETE `orderItem` FROM `orderItem`
  LEFT JOIN `order` ON `orderItem`.orderItemNumber = `order`.idorder
WHERE `order`.idorder IS NULL;

DELETE FROM `orderItem` WHERE `offer`=0;

UPDATE `orderItem`
 LEFT JOIN `offer` ON orderItem.offer = offer.idoffer
SET `orderItem`.orderedItem = `offer`.thing
WHERE `orderItem`.offer <> 0;

ALTER TABLE `orderItem`
  DROP COLUMN `orderedItemType`,
  DROP COLUMN `orderItemStatus`,
  DROP COLUMN `offer`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idorderItem`,`orderItemNumber`);

-- add foreign keys
ALTER TABLE `orderItem`
  ADD KEY `fk_orderedItem_thing_idx` (`orderedItem`),
  ADD KEY `fk_orderItemNumber_thing_idx` (`orderItemNumber`),
  ADD CONSTRAINT `fk_orderedItem_thing` FOREIGN KEY (`orderedItem`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_orderItemNumber_thing` FOREIGN KEY (`orderItemNumber`) REFERENCES `order` (`idorder`) ON DELETE CASCADE ON UPDATE NO ACTION;
