
--
-- offer
--

ALTER TABLE `offer`
  CHANGE COLUMN `idoffer` `idoffer` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  CHANGE COLUMN `itemOffered` `itemOffered` INT UNSIGNED NOT NULL,
  CHANGE COLUMN `offeredBy` `offeredBy` INT UNSIGNED NOT NULL,
  CHANGE COLUMN `elegibleQuantity` `eligibleQuantity` VARCHAR(45) NULL DEFAULT NULL,
  CHANGE COLUMN `elegibleDuration` `eligibleDuration` VARCHAR(45) NULL DEFAULT NULL,
  ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idoffer`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idoffer`);

--
UPDATE `offer`
  left join `service` on service.idservice=offer.itemOffered and `offer`.itemOfferedType='service'
  left join `product` on product.idproduct=offer.itemOffered and `offer`.itemOfferedType='product'
  left join `organization`ON `organization`.idorganization=`offer`.offeredBy AND `offer`.offeredByType='organization'
SET `offer`.itemOffered = IF(`offer`.itemOfferedType='service', service.thing, product.thing), `offer`.offeredBy = `organization`.thing
WHERE `offer`.itemOfferedType IS NOT NULL;

DELETE FROM `offer` WHERE `itemOffered`='0' || `offeredBy`='0';

-- INSERT THING
ALTER TABLE `thing` ADD COLUMN `idoffer` INT UNSIGNED DEFAULT NULL;

-- insert thing
INSERT INTO `thing` (`idoffer`,`name`,`additionalType`,`description`,`disambiguatingDescription`,`type`)
SELECT `offer`.idoffer,
       IF (`name` <> '', `name`, 'Undefined name'),
       `additionalType`,
       description,
       SUBSTRING(REGEXP_REPLACE(disambiguatingDescription, '<[^>]*>+', ''),1,255) as disambiguatingDescription,
       'Offer'
FROM `offer` LEFT JOIN `thing` ON `thing`.idthing=`offer`.itemOffered;

-- set thing
UPDATE `offer`
  JOIN `thing` ON thing.idoffer = `offer`.idoffer
SET `offer`.thing = `thing`.idthing
WHERE `thing`.name <> '';

-- drop column
ALTER TABLE `thing` DROP COLUMN `idoffer`;

--
ALTER TABLE `offer`
  CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
  DROP COLUMN `itemOfferedType`,
  DROP COLUMN `offeredByType`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idoffer`,`itemOffered`,`offeredBy`);

-- add foreign keys
ALTER TABLE `offer`
  ADD KEY `fk_offer_thing_idx` (`thing`),
  ADD KEY `fk_offer_itemOffered_thing_idx` (`itemOffered`),
  ADD KEY `fk_offer_offeredBy_thing_idx` (`offeredBy`),
  ADD CONSTRAINT `fk_offer_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_offer_itemOffered_thing` FOREIGN KEY (`itemOffered`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_offer_offeredBy_thing` FOREIGN KEY (`offeredBy`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION;

