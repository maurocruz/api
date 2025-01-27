-- SERVICE
CREATE PROCEDURE upgrade_service()
BEGIN
  -- alter table
  ALTER TABLE `service`
    CHANGE COLUMN `idservice` `idservice` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    CHANGE COLUMN `provider` `provider` INT UNSIGNED NOT NULL,
    ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idservice`,
    ADD COLUMN `isRelatedTo` INT UNSIGNED DEFAULT NULL,
    ADD COLUMN `serviceOutput` INT UNSIGNED DEFAULT NULL,
    DROP PRIMARY KEY,
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

  UPDATE `service`
    LEFT JOIN `organization` ON `organization`.idorganization = `service`.provider AND `providerType` = 'organization'
    LEFT JOIN `person` ON `person`.idperson = `service`.provider AND `providerType` = 'person'
  SET `service`.provider = IF(`providerType`='organization', IF(`organization`.thing IS NOT NULL, `organization`.thing, `service`.provider),IF(`person`.thing IS NOT NULL,`person`.thing, `service`.provider));

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
    DROP COLUMN `providerType`,
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (`idservice`,`thing`);

  DROP TABLE `service_has_imageObject`;
  DROP TABLE `service_has_offer`;
END;