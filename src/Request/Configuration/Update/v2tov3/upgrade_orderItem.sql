CREATE PROCEDURE upgrade_orderItem()
BEGIN
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
  SET `orderItem`.orderedItem= IF(orderedItemType='service',service.thing,product.thing);

  DELETE `orderItem` FROM `orderItem`
    LEFT JOIN `order` ON `orderItem`.orderItemNumber = `order`.idorder
  WHERE `order`.idorder IS NULL;

  DELETE FROM `orderItem` WHERE `offer` is null OR `offer`=0;

  ALTER TABLE `orderItem`
    DROP COLUMN `orderedItemType`,
    DROP COLUMN `orderItemStatus`,
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (`idorderItem`,`orderItemNumber`);
END;
