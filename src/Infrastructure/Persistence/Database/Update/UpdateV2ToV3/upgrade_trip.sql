
--
-- TRIP
--

ALTER TABLE `trip`
  CHANGE COLUMN `idtrip` `idtrip` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idtrip`,
  CHANGE COLUMN `provider` `provider` INT UNSIGNED NOT NULL,
  DROP PRIMARY KEY ,
  ADD PRIMARY KEY (`idtrip`);

-- INSERT THING
ALTER TABLE `thing` ADD COLUMN `idtrip` INT UNSIGNED DEFAULT NULL;
-- insert thing
INSERT INTO `thing` (`idtrip`,`name`,`description`,`disambiguatingDescription`,`dateRegistered`,`lastModified`,`type`)
SELECT `idtrip`,
       IF (`name` <> '', `name`, 'Undefined name'),
       CONCAT(`description`,' - ', disambiguatingDescription),
       SUBSTR(`disambiguatingDescription`,0,255),
       `dateModified`,
       `dateModified`,
       'Trip'
FROM `trip`;
-- set thing
UPDATE `trip`
  JOIN `thing` ON thing.idtrip = trip.idtrip
  SET trip.thing = thing.idthing
  WHERE thing.name <> '';
-- drop column
ALTER TABLE `thing` DROP COLUMN `idtrip`;

-- insert images
CALL insert_thing_has_thing('trip','imageObject');

-- insert property values
ALTER TABLE `trip_has_propertyValue`
  ADD COLUMN `caption` VARCHAR(255) DEFAULT NULL,
  ADD COLUMN `position` INT UNSIGNED NULL DEFAULT '1',
  ADD COLUMN `representativeOfPage` TINYINT NOT NULL DEFAULT 0;

CALL insert_thing_has_thing('trip','propertyValue');

-- IMAGES
CALL set_image_in_thing('trip');

-- insert thing_has_thing trip_has_trip
INSERT INTO `thing_has_thing` (idHasPart, typeHasPart, idIsPartOf, typeIsPartOf)
  SELECT t1.thing, 'trip', t2.thing, 'trip'
    FROM `trip_has_trip`
    JOIN `trip` AS t1 ON t1.idtrip=idHasPart
    JOIN `trip` AS t2 ON t2.idtrip=idIsPartOf;

-- alter table
ALTER TABLE `trip`
  CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
  DROP COLUMN `name`,
  DROP COLUMN `description`,
  DROP COLUMN `disambiguatingDescription`,
  DROP COLUMN `arrivalDate`,
  DROP COLUMN `departureDate`,
  DROP COLUMN `providerType`,
  DROP COLUMN `dateModified`,
  DROP COLUMN `dateCreated`,
  DROP PRIMARY KEY ,
  ADD PRIMARY KEY (`idtrip`,`thing`)
;

DROP TABLE `trip_has_imageObject`;
DROP TABLE `trip_has_trip`;
DROP TABLE `trip_has_propertyValue`;

-- add foreign key
ALTER TABLE `trip`
  ADD KEY `fk_trip_thing_idx` (`thing`),
  ADD KEY `fk_trip_provider_idx` (`provider`),
  ADD CONSTRAINT `fk_trip_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_trip_provider` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION;

