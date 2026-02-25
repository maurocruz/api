
--
-- EVENT
--

UPDATE `event` SET `superEvent`=null WHERE `superEvent`=0;

-- alter table
ALTER TABLE `event`
  DROP COLUMN `additionalType`,
  CHANGE COLUMN `idevent` `idevent` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  CHANGE COLUMN `location` `location` INT UNSIGNED DEFAULT NULL,
  CHANGE COLUMN `superEvent` `superEvent` INT UNSIGNED DEFAULT NULL,
  CHANGE COLUMN `organizerId` `organizer` VARCHAR(255) DEFAULT NULL,
  CHANGE COLUMN `directed` `director` VARCHAR(255) DEFAULT NULL,
  ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idevent`,
  ADD COLUMN `about` INT UNSIGNED DEFAULT NULL AFTER `idevent`,
  ADD COLUMN `keywords` VARCHAR(255) DEFAULT NULL AFTER `idevent`,
  ADD COLUMN `subEvent` INT UNSIGNED DEFAULT NULL AFTER `idevent`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idevent`);

-- INSERT THING
ALTER TABLE `thing` ADD COLUMN `idevent` INT UNSIGNED DEFAULT NULL;

-- insere dados em thing
INSERT INTO `thing` (`idevent`,`name`,`description`,`lastModified`,`dateRegistered`,`type`)
  SELECT
    `idevent`,
    `name`,
    `description`,
    `dateModified`,
    `dateCreated`,
    'Event'
  FROM `event` WHERE `name` <> '';

-- atualiza tabela
UPDATE `event`
  JOIN `thing` ON `event`.idevent = thing.idevent
  SET event.thing = thing.idthing
  WHERE event.`name` <> '';

-- drop thing column
ALTER TABLE `thing` DROP COLUMN `idevent`;

-- set location null if idplace not exists
UPDATE `event`
SET `event`.location = NULL
WHERE `event`.location NOT IN (SELECT idplace FROM place);

-- muda o location de idplace para idthing de place
UPDATE `event`
  JOIN `place` ON place.idplace=event.location
  SET event.location=place.thing
  WHERE event.location IS NOT NULL;

-- insert images
CALL insert_thing_has_thing('event','imageObject');

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

-- add foreign keys
ALTER TABLE `event`
  ADD KEY `fk_event_thing_idx` (`thing`),
  ADD KEY `fk_event_location_idx` (`location`),
  ADD KEY `fk_event_subEvent_idx` (`subEvent`),
  ADD KEY `fk_event_superEvent_idx` (`superEvent`),
  ADD CONSTRAINT `fk_event_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_event_location` FOREIGN KEY (`location`) REFERENCES `place` (`thing`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_event_subEvent` FOREIGN KEY (`subEvent`) REFERENCES `event` (`idevent`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_event_superEvent` FOREIGN KEY (`superEvent`) REFERENCES `event` (`idevent`) ON DELETE SET NULL ON UPDATE NO ACTION;
