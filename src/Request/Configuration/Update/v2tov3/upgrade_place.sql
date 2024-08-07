-- PLACE
CREATE PROCEDURE upgrade_place()
BEGIN
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
      description,
      SUBSTRING(REGEXP_REPLACE(disambiguatingDescription, '<[^>]*>+', ''),1,255) as disambiguatingDescription,
      `url`,
      if(`dateCreated` IS NULL, CURDATE(), `dateCreated`),
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
  INSERT INTO `thing_has_imageObject` (`idthing`,`idimageObject`,`position`,`representativeOfPage`,`caption`)
  SELECT `thing`,`idimageObject`,`place_has_imageObject`.`position`,`representativeOfPage`,`caption` FROM `place_has_imageObject`
    JOIN `place` ON `place_has_imageObject`.idplace = place.idplace;

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
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (`idplace`,`thing`);

  DROP TABLE `place_has_imageObject`;
END;