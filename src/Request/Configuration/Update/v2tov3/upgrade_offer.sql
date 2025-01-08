CREATE PROCEDURE upgrade_offer()
BEGIN
  ALTER TABLE `offer`
    CHANGE COLUMN `idoffer` `idoffer` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    CHANGE COLUMN `itemOffered` `itemOffered` INT UNSIGNED NOT NULL,
    CHANGE COLUMN `offeredBy` `offeredBy` INT UNSIGNED NOT NULL,
    CHANGE COLUMN `elegibleQuantity` `eligibleQuantity` VARCHAR(45) NULL DEFAULT NULL,
    CHANGE COLUMN `elegibleDuration` `eligibleDuration` VARCHAR(45) NULL DEFAULT NULL,
    ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idoffer`,
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (`idoffer`);

  UPDATE `offer`
    left join `service` on service.idservice=offer.itemOffered and `offer`.itemOfferedType='service'
    left join `product` on product.idproduct=offer.itemOffered and `offer`.itemOfferedType='product'
    left join `organization`ON `organization`.idorganization=`offer`.offeredBy AND `offer`.offeredByType='organization'
  SET `offer`.itemOffered = IF(`offer`.itemOfferedType='service', service.thing, product.thing), `offer`.offeredBy = `organization`.thing;

  DELETE FROM `offer` WHERE `itemOffered`='0' || `offeredBy`='0';

  -- INSERT THING
  ALTER TABLE `thing` ADD COLUMN `idoffer` INT UNSIGNED DEFAULT NULL;
  -- insert thing
  INSERT INTO `thing` (`idoffer`,`name`,`additionalType`,`description`,`disambiguatingDescription`,`dateCreated`,`dateModified`,`type`)
  SELECT `offer`.idoffer,
         IF (`name` <> '', `name`, 'Undefined name'),
         `additionalType`,
         description,
         SUBSTRING(REGEXP_REPLACE(disambiguatingDescription, '<[^>]*>+', ''),1,255) as disambiguatingDescription,
         if(`dateCreated` IS NULL, CURDATE(), `dateCreated`),
         `dateModified`,
         'Offer'
  FROM `offer` LEFT JOIN `thing` ON `thing`.idthing=`offer`.itemOffered;
  -- set thing
  UPDATE `offer` JOIN `thing` ON thing.idoffer = `offer`.idoffer SET `offer`.thing = `thing`.idthing;
  -- drop column
  ALTER TABLE `thing` DROP COLUMN `idoffer`;


  ALTER TABLE `offer`
    CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
    DROP COLUMN `itemOfferedType`,
    DROP COLUMN `offeredByType`,
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (`idoffer`,`itemOffered`,`offeredBy`);
END;