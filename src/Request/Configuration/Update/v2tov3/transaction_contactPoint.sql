START TRANSACTION ;
-- CONTACT POINT

CREATE TABLE IF NOT EXISTS `contactPoint` (
  `idcontactPoint` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thing` INT UNSIGNED NOT NULL,
  `contactType` VARCHAR(160) DEFAULT NULL,
  `contactOption` VARCHAR(100) DEFAULT NULL,
  `email` VARCHAR(255) DEFAULT NULL,
  `telephone` VARCHAR(45) DEFAULT NULL,
  PRIMARY KEY (`idcontactPoint`,`thing`),
  CONSTRAINT `fk_contactPoint_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE=InnoDB;

ALTER TABLE `contactPoint`
  CHANGE COLUMN `idcontactPoint` `idcontactPoint` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idcontactPoint`,
  ADD COLUMN `contactOption` VARCHAR(100) DEFAULT NULL AFTER `idcontactPoint`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idcontactPoint`);

-- insert thing
INSERT INTO `thing` (`name`,`description`,`type`)
SELECT IF(contactPoint.name, contactPoint.name, IF(telephone, telephone, IF(email, email, CONCAT("Contact point id", contactPoint.idcontactPoint)))), CONCAT("Contact point id", contactPoint.idcontactPoint), 'ContactPoint' FROM `contactPoint`;
-- update this
UPDATE `contactPoint`
  JOIN `thing` ON thing.description = CONCAT("Contact point id", contactPoint.idcontactPoint)
SET contactPoint.thing = thing.idthing;

-- alter table
ALTER TABLE `contactPoint`
  DROP COLUMN `name`,
  DROP COLUMN `whatsapp`,
  DROP COLUMN `obs`,
  DROP COLUMN `position`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idcontactPoint`,`thing`),
  ADD INDEX `fk_contactPoint_thing_idx` (`thing`);

COMMIT ;
