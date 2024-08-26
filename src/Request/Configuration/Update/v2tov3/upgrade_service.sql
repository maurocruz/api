-- SERVICE
CREATE PROCEDURE upgrade_service()
BEGIN
  -- alter table
  ALTER TABLE `service`
    CHANGE COLUMN `idservice` `idservice` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    CHANGE COLUMN `provider` `provider` INT UNSIGNED NOT NULL,
    ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idservice`,
    DROP PRIMARY KEY ,
    ADD PRIMARY KEY (`idservice`);

  -- INSERT THING
  ALTER TABLE `thing` ADD COLUMN `idservice` INT UNSIGNED DEFAULT NULL;
  -- insert thing
  INSERT INTO `thing` (`idservice`,`name`,`additionalType`,`description`,`disambiguatingDescription`,`dateCreated`,`dateModified`,`type`)
    SELECT `idservice`,
         IF (`name` <> '', `name`, 'Undefined name'),
         `additionalType`,
         description,
         SUBSTRING(REGEXP_REPLACE(disambiguatingDescription, '<[^>]*>+', ''),1,255) as disambiguatingDescription,
         if(`dateCreated` IS NULL, CURDATE(), `dateCreated`),
         `dateModified`,
         'Service'
    FROM `service`;
  -- set thing
  UPDATE `service` JOIN `thing` ON thing.idservice = service.idservice SET service.thing = thing.idthing;
  -- drop column
  ALTER TABLE `thing` DROP COLUMN `idservice`;

  -- insert images
  INSERT INTO `thing_has_imageObject` (`idthing`,`idimageObject`,`position`,`representativeOfPage`,`caption`)
  SELECT `thing`,`idimageObject`,`service_has_imageObject`.`position`,`representativeOfPage`,`caption` FROM `service_has_imageObject`
    JOIN `service` ON `service_has_imageObject`.idservice = service.idservice;

  -- IMAGES
  CALL set_image_in_thing('service');

  ALTER TABLE `service`
    CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
    DROP COLUMN `name`,
    DROP COLUMN `additionalType`,
    DROP COLUMN `description`,
    DROP COLUMN `disambiguatingDescription`,
    DROP COLUMN `dateCreated`,
    DROP COLUMN `dateModified`,
    DROP PRIMARY KEY ,
    ADD PRIMARY KEY (`idservice`,`thing`)
  ;

  DROP TABLE `service_has_imageObject`;
END;