CREATE PROCEDURE upgrade_offer()
BEGIN
  ALTER TABLE `offer`
    CHANGE COLUMN `idoffer` `idoffer` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    CHANGE COLUMN `itemOffered` `itemOffered` INT UNSIGNED NOT NULL,
    CHANGE COLUMN `offeredBy` `offeredBy` INT UNSIGNED NOT NULL,
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (`idoffer`);

  UPDATE `offer`
    left join `service` on service.idservice=offer.itemOffered and `offer`.itemOfferedType='service'
    left join `product` on product.idproduct=offer.itemOffered and `offer`.itemOfferedType='product'
    left join `organization`ON `organization`.idorganization=`offer`.offeredBy AND `offer`.offeredByType='organization'
  SET `offer`.itemOffered = IF(`offer`.itemOfferedType='service', service.thing, product.thing), `offer`.offeredBy = `organization`.thing;

  DELETE FROM `offer` WHERE `itemOffered`='0' || `offeredBy`='0';

  ALTER TABLE `offer`
    DROP COLUMN `itemOfferedType`,
    DROP COLUMN `offeredByType`,
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (`idoffer`,`itemOffered`,`offeredBy`);
END;