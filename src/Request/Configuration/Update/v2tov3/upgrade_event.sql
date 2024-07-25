-- EVENT
CREATE PROCEDURE upgrade_event()
  BEGIN
    -- alter table
    ALTER TABLE `event`
      DROP COLUMN `additionalType`,
      CHANGE COLUMN `idevent` `idevent` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      CHANGE COLUMN `superEvent` `superEvent` INT UNSIGNED DEFAULT NULL,
      CHANGE COLUMN `organizerId` `organizer` INT UNSIGNED DEFAULT NULL,
      CHANGE COLUMN `directed` `director` INT UNSIGNED DEFAULT NULL,
      ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idevent`,
      ADD COLUMN `about` INT UNSIGNED DEFAULT NULL AFTER `idevent`,
      ADD COLUMN `keywords` VARCHAR(255) DEFAULT NULL AFTER `idevent`,
      ADD COLUMN `subEvent` INT UNSIGNED DEFAULT NULL AFTER `idevent`,
      DROP PRIMARY KEY,
      ADD PRIMARY KEY (`idevent`);

    -- insere dados em thing
    INSERT INTO `thing` (`name`,`disambiguatingDescription`,`dateModified`,`dateCreated`,`type`)
    SELECT `name`,`description`,`dateModified`,`dateCreated`, 'Event' FROM `event` WHERE `name` <> '';
    -- atualiza tabela
    UPDATE `event`
      JOIN `thing` ON `event`.name=thing.name AND event.dateModified=thing.dateModified AND event.dateCreated=thing.dateCreated
    SET event.thing=thing.idthing;

    -- insert images
    INSERT INTO `thing_has_imageObject` (`idthing`,`idimageObject`,`position`,`representativeOfPage`,`caption`)
    SELECT `thing`,`idimageObject`,`event_has_imageObject`.`position`,`representativeOfPage`,`caption` FROM `event_has_imageObject`
      JOIN `event` ON `event_has_imageObject`.idevent=event.idevent;

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
      ADD PRIMARY KEY (`idevent`,`thing`),
      ADD KEY `fk_event_about_idx1` (`about`),
      ADD KEY `fk_event_location_idx1` (`location`),
      ADD KEY `fk_event_organizer_idx1` (`organizer`),
      ADD KEY `fk_event_subEvent_idx1` (`subEvent`),
      ADD KEY `fk_event_superEvent_idx1` (`superEvent`);

    -- drop relational tables
    DROP TABLE `event_has_event`;
    DROP TABLE `event_has_imageObject`;

  END ;