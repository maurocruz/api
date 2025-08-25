-- PLACE
CREATE PROCEDURE upgrade_place()
BEGIN

  ALTER TABLE `postalAddress`
    CHANGE COLUMN `idpostalAddress` `idpostalAddress` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (`idpostalAddress`);

  -- GEO COORDINATES
  CREATE TABLE IF NOT EXISTS `geoCoordinates` (
    `idgeoCoordinates` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `address` INT UNSIGNED NULL DEFAULT NULL,
    `elevation` VARCHAR(125) NULL DEFAULT NULL,
    `latitude` DECIMAL(18,14) NULL DEFAULT NULL,
    `longitude` DECIMAL(18,14) NULL DEFAULT NULL,
    PRIMARY KEY (`idgeoCoordinates`),
    INDEX `fk_geoCoordinates_postalAddress_idx` (`address`),
    CONSTRAINT `fk_geoCoordinates_postalAddress` FOREIGN KEY (`address`) REFERENCES `postalAddress` (`idpostalAddress`) ON DELETE SET NULL
  ) ENGINE = InnoDB;

  -- alter table
  ALTER TABLE `place`
    CHANGE COLUMN `idplace` `idplace` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    ADD COLUMN `geo` INT UNSIGNED DEFAULT NULL AFTER `idplace`,
    ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idplace`,
    ADD COLUMN `publicAccess` TINYINT DEFAULT 0,
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (`idplace`);

  -- INSERT THING
  ALTER TABLE `thing` ADD COLUMN `idplace` INT UNSIGNED DEFAULT NULL;
    -- insert thing
  INSERT INTO `thing` (`idplace`,`name`,`additionalType`,`description`,`disambiguatingDescription`,`url`,`dateCreated`,`dateModified`,`type`)
    SELECT `idplace`,
      IF (`name` <> '', `name`, 'Undefined name'),
      `additionalType`,
      CONCAT(description,' ', disambiguatingDescription),
      SUBSTRING(REGEXP_REPLACE(disambiguatingDescription, '<[^>]*>+', ''),1,255) as disambiguatingDescription,
      `url`,
      `dateCreated`,
      `dateModified`,
      'Place'
    FROM `place`;
  -- set thing
  UPDATE `place` JOIN `thing` ON thing.idplace = place.idplace SET place.thing = thing.idthing;
  -- drop column
  ALTER TABLE `thing` DROP COLUMN `idplace`;

  -- insert geo coordinates
  ALTER TABLE `geoCoordinates` ADD COLUMN `idplace` INT UNSIGNED DEFAULT NULL;
  INSERT INTO `geoCoordinates` (`idplace`,`address`,`elevation`,`latitude`,`longitude`)
    SELECT `idplace`,`address`,`elevation`,`latitude`,`longitude` FROM `place`;
  UPDATE `place`
    JOIN `geoCoordinates` ON geoCoordinates.idplace = place.idplace
    SET place.geo = geoCoordinates.idgeoCoordinates;
  ALTER TABLE `geoCoordinates` DROP COLUMN `idplace`;

  -- insert images
  CALL insert_thing_has_thing('place','imageObject');

  -- IMAGES
  CALL set_image_in_thing('place');

  -- alter table
  ALTER TABLE `place`
    CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
    DROP COLUMN `name`,
    DROP COLUMN `additionalType`,
    DROP COLUMN `description`,
    DROP COLUMN `disambiguatingDescription`,
    DROP COLUMN `url`,
    DROP COLUMN `dateCreated`,
    DROP COLUMN `dateModified`,
    DROP COLUMN `rank`,
    DROP COLUMN `address`,
    DROP COLUMN `elevation`,
    DROP COLUMN `longitude`,
    DROP COLUMN `latitude`,
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (`idplace`,`thing`);

  DROP TABLE `place_has_imageObject`;

  -- PLACE
  ALTER TABLE `place`
    ADD KEY `fk_place_thing_idx` (`thing`),
    ADD KEY `fk_place_geo_idx` (`geo`),
    ADD CONSTRAINT `fk_place_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_place_geo` FOREIGN KEY (`geo`) REFERENCES `geoCoordinates` (`idgeoCoordinates`) ON DELETE SET NULL ON UPDATE NO ACTION;

END;