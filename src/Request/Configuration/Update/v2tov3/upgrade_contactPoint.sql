-- CONTACT POINT
CREATE PROCEDURE upgrade_contactPoint()
  BEGIN
    -- alter table
    ALTER TABLE `contactPoint`
      CHANGE COLUMN `idcontactPoint` `idcontactPoint` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idcontactPoint`,
      ADD COLUMN `contactOption` VARCHAR(100) DEFAULT NULL AFTER `idcontactPoint`,
      DROP PRIMARY KEY,
      ADD PRIMARY KEY (`idcontactPoint`);

    -- INSERT THING
    ALTER TABLE `thing` ADD COLUMN `idcontactPoint` INT UNSIGNED DEFAULT NULL;
    -- insert thing
    INSERT INTO `thing` (`idcontactPoint`,`name`,`description`,`type`)
    SELECT `idcontactPoint`,
           IF(contactPoint.name, contactPoint.name, IF(telephone, telephone, IF(email, email, CONCAT("Contact point id", contactPoint.idcontactPoint)))),
           CONCAT("Contact point id", contactPoint.idcontactPoint),
           'ContactPoint'
    FROM `contactPoint`;
    -- update this
    UPDATE `contactPoint`
      JOIN `thing` ON thing.idcontactPoint = contactPoint.idcontactPoint
      SET contactPoint.thing = thing.idthing;
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
      ADD PRIMARY KEY (`idcontactPoint`,`thing`),
      ADD INDEX `fk_contactPoint_thing_idx` (`thing`);

END ;
