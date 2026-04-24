
--
-- Property Value
--

ALTER TABLE `propertyValue`
  CHANGE COLUMN `idpropertyValue` `idpropertyValue` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ADD COLUMN `thing` INT UNSIGNED NOT NULL AFTER `idpropertyValue`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idpropertyValue`);

-- INSERT THINGS
ALTER TABLE `thing` ADD COLUMN `idpropertyValue` INT UNSIGNED DEFAULT NULL;

-- insert thing
INSERT INTO `thing` (`idpropertyValue`,`name`,`description`,`type`)
SELECT
  pv.`idpropertyValue`,
  pv.`name`,
  CONCAT('name=', pv.`name`,'; value=', pv.`value`) AS `description`,
  'PropertyValue' AS `type`
FROM `propertyValue` AS pv;

-- update this
UPDATE `propertyValue`
  JOIN `thing` ON thing.idpropertyValue = propertyValue.idpropertyValue
SET propertyValue.thing = thing.idthing
WHERE 1;

-- drop thing column
ALTER TABLE `thing` DROP COLUMN `idpropertyValue`;

-- alter table
ALTER TABLE `propertyValue`
  DROP COLUMN `name`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idpropertyValue`,`thing`);