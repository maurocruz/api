-- EVENT
CREATE PROCEDURE upgrade_event()
  BEGIN

    UPDATE `event` SET `superEvent`=null WHERE `superEvent`=0;

    -- alter table
    ALTER TABLE `event`
      DROP COLUMN `additionalType`,
      CHANGE COLUMN `idevent` `idevent` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      CHANGE COLUMN `location` `location` INT UNSIGNED DEFAULT NULL,
      CHANGE COLUMN `superEvent` `superEvent` INT UNSIGNED DEFAULT NULL,
      CHANGE COLUMN `organizerId` `organizer` INT UNSIGNED DEFAULT NULL,
      CHANGE COLUMN `directed` `director` INT UNSIGNED DEFAULT NULL,
      ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idevent`,
      ADD COLUMN `about` INT UNSIGNED DEFAULT NULL AFTER `idevent`,
      ADD COLUMN `keywords` VARCHAR(255) DEFAULT NULL AFTER `idevent`,
      ADD COLUMN `subEvent` INT UNSIGNED DEFAULT NULL AFTER `idevent`,
      DROP PRIMARY KEY,
      ADD PRIMARY KEY (`idevent`);

    -- INSERT THING
    ALTER TABLE `thing` ADD COLUMN `idevent` INT UNSIGNED DEFAULT NULL;
    -- insere dados em thing
    INSERT INTO `thing` (`idevent`,`name`,`description`,`dateModified`,`dateCreated`,`type`)
      SELECT `idevent`,`name`,`description`,`dateModified`,`dateCreated`, 'Event' FROM `event` WHERE `name` <> '';
    -- atualiza tabela
    UPDATE `event`
      JOIN `thing` ON `event`.idevent = thing.idevent
      SET event.thing = thing.idthing;
    -- drop thing column
    ALTER TABLE `thing` DROP COLUMN `idevent`;

    -- insert images
    INSERT INTO `thing_has_imageObject` (`idthing`,`idimageObject`,`position`,`representativeOfPage`,`caption`)
    SELECT `thing`,`idimageObject`,`event_has_imageObject`.`position`,`representativeOfPage`,`caption` FROM `event_has_imageObject`
      JOIN `event` ON `event_has_imageObject`.idevent=event.idevent;

    -- IMAGES
    CALL set_image_in_thing('event');

    -- insert thing_has_thing event_has_event
    INSERT INTO `thing_has_thing` (idHasPart, typeHasPart, idIsPartOf, typeIsPartOf)
      SELECT t1.thing, 'Event', t2.thing, 'Event' FROM `event_has_event`
        JOIN `event` AS t1 ON t1.idevent=idHasPart
        JOIN `event` AS t2 ON t2.idevent=idIsPartOf;

    -- alter table
    ALTER TABLE `event`
      CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
      DROP COLUMN `name`,
      DROP COLUMN `description`,
      DROP COLUMN `src`,
      DROP COLUMN `organizerType`,
      DROP COLUMN `place`,
      DROP COLUMN `schedule`,
      DROP COLUMN `link_directed`,
      DROP COLUMN `dateCreated`,
      DROP COLUMN `dateModified`,
      DROP PRIMARY KEY,
      ADD PRIMARY KEY (`idevent`,`thing`);

    -- drop relational tables
    DROP TABLE `event_has_event`;
    DROP TABLE `event_has_imageObject`;

  END ;