-- LOCAL BUSINESS
CREATE PROCEDURE upgrade_localBusiness()
  BEGIN
    -- ALTER TABLE
    ALTER TABLE `localBusiness`
      CHANGE COLUMN `idlocalBusiness` `idlocalBusiness` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      CHANGE COLUMN `organization` `organization` INT UNSIGNED DEFAULT NULL,
      CHANGE COLUMN `location` `location` INT UNSIGNED DEFAULT NULL,
      CHANGE COLUMN `additionalType` `additionalType` VARCHAR(255) DEFAULT NULL,
      ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idlocalBusiness`,
      DROP PRIMARY KEY ,
      ADD PRIMARY KEY (`idlocalBusiness`);

    ALTER TABLE `thing`
      ADD COLUMN `idlocalBusiness` INT UNSIGNED DEFAULT NULL;

    -- INSERT THING
    ALTER TABLE `thing` ADD COLUMN `idevent` INT UNSIGNED DEFAULT NULL;
    -- insert thing
    INSERT INTO `thing` (`idlocalBusiness`,`name`,`additionalType`,`description`,`disambiguatingDescription`,`url`,`dateCreated`,`dateModified`,`type`)
    SELECT `idlocalBusiness`,`name`,`additionalType`,
       description,
       SUBSTRING(REGEXP_REPLACE(disambiguatingDescription, '<[^>]*>+', ''),1,255) as disambiguatingDescription,
       `url`,`dateCreated`,`dateModified`,'LocalBusiness'
    FROM `localBusiness`;
    -- update this
    UPDATE `localBusiness`
      JOIN `thing` ON thing.idlocalBusiness = localBusiness.idlocalBusiness
      SET localBusiness.thing = thing.idthing;
    -- drop thing column
    ALTER TABLE `thing` DROP COLUMN `idevent`;

    -- set organization
    UPDATE `localBusiness`
      JOIN `organization` ON `organization`.`name` = `localBusiness`.`name`
      SET `localBusiness`.organization = `organization`.idorganization
    WHERE `localBusiness`.organization IS NULL;

    UPDATE `localBusiness`
      SET `localBusiness`.organization = NULL
      WHERE `localBusiness`.organization NOT IN (SELECT idorganization FROM organization);

    -- insert organization
    INSERT INTO `organization` (`hasOfferCatalog`,`location`)
      SELECT `hasOfferCatalog`,`location` FROM `localBusiness` WHERE `organization` IS NOT NULL;

    UPDATE `localBusiness`
      JOIN `organization` ON `localBusiness`.hasOfferCatalog = `organization`.hasOfferCatalog AND `localBusiness`.location = `organization`.location
    SET `localBusiness`.organization=`organization`.idorganization;

    -- has contact point
    INSERT INTO `thing_has_thing` (idHasPart, typeHasPart, idIsPartOf, typeIsPartOf)
    SELECT `localBusiness`.thing, 'LocalBusiness', `contactPoint`.thing, 'ContactPoint' FROM `localBusiness_has_contactPoint`
      JOIN `localBusiness` ON `localBusiness`.idlocalBusiness = `localBusiness_has_contactPoint`.idlocalBusiness
      JOIN `contactPoint` ON `contactPoint`.idcontactPoint = `localBusiness_has_contactPoint`.idcontactPoint;

    -- has images
    INSERT INTO `thing_has_imageObject` (`idthing`,`idimageObject`,`position`,`representativeOfPage`,`caption`)
    SELECT `thing`,`idimageObject`,`localBusiness_has_imageObject`.`position`,`representativeOfPage`,`caption` FROM `localBusiness_has_imageObject`
      JOIN `localBusiness` ON `localBusiness_has_imageObject`.idlocalBusiness = localBusiness.idlocalBusiness;

    -- IMAGES
    CALL set_image_in_thing('localBusiness');

    -- has person
    INSERT INTO `thing_has_thing` (idHasPart, typeHasPart, idIsPartOf, typeIsPartOf, caption, position)
    SELECT `localBusiness`.thing,'LocalBusiness',`person`.thing,'Person',jobTitle,position FROM `localBusiness_has_person`
      JOIN `localBusiness` ON `localBusiness`.idlocalBusiness = `localBusiness_has_person`.idlocalBusiness
      JOIN `person` ON `person`.idperson = `localBusiness_has_person`.idperson;

    ALTER TABLE `thing`
      DROP COLUMN `idlocalBusiness`;

    ALTER TABLE `localBusiness`
      CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
      DROP COLUMN `name`,
      DROP COLUMN `additionalType`,
      DROP COLUMN `description`,
      DROP COLUMN `disambiguatingDescription`,
      DROP COLUMN `url`,
      DROP COLUMN `hasOfferCatalog`,
      DROP COLUMN `address`,
      DROP COLUMN `rank`,
      DROP COLUMN `dateCreated`,
      DROP COLUMN `dateModified`,
      DROP PRIMARY KEY ,
      ADD PRIMARY KEY (`idlocalBusiness`,`thing`);

    DROP TABLE `localBusiness_has_contactPoint`;
    DROP TABLE `localBusiness_has_imageObject`;
    DROP TABLE `localBusiness_has_person`;
  END;