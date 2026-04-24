
--
-- CONTACT POINT
--
ALTER TABLE `contactPoint`
  CHANGE COLUMN `idcontactPoint` `idcontactPoint` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idcontactPoint`,
  ADD COLUMN `contactOption` VARCHAR(100) DEFAULT NULL AFTER `thing`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idcontactPoint`);

-- INSERT THING
ALTER TABLE `thing` ADD COLUMN `idcontactPoint` INT UNSIGNED DEFAULT NULL;
-- insert thing
INSERT INTO `thing` (`idcontactPoint`,`name`,`description`,`type`)
SELECT
  cp.`idcontactPoint`,
  COALESCE(
          NULLIF(cp.`name`, ''),
          NULLIF(cp.`telephone`, ''),
          NULLIF(cp.`email`, ''),
          CONCAT('Contact point id ', cp.`idcontactPoint`)
  ) AS `name`,
  CONCAT('Contact point id ', cp.`idcontactPoint`) AS `description`,
  'ContactPoint' AS `type`
FROM `contactPoint` AS cp;
-- update this
UPDATE `contactPoint`
  JOIN `thing` ON thing.idcontactPoint = contactPoint.idcontactPoint
  SET contactPoint.thing = thing.idthing;
-- options
UPDATE `contactPoint` SET `contactOption` = 'whatsapp' WHERE `whatsapp` = 1;
-- drop thing column
ALTER TABLE `thing` DROP COLUMN `idcontactPoint`;

-- alter table
ALTER TABLE `contactPoint`
  CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
  DROP COLUMN `name`,
  DROP COLUMN `whatsapp`,
  DROP COLUMN `obs`,
  DROP COLUMN `position`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idcontactPoint`,`thing`);

-- add foreign keys
ALTER TABLE `contactPoint`
  ADD KEY `fk_contactPoint_thing_idx` (`thing`),
  ADD CONSTRAINT `fk_contactPoint_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION;
