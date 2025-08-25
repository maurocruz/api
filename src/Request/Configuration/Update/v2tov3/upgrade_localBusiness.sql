-- LOCAL BUSINESS
CREATE PROCEDURE upgrade_localBusiness()
  BEGIN
    -- alter table LOCAL BUSINESS
    ALTER TABLE `localBusiness`
      CHANGE COLUMN `idlocalBusiness` `idlocalBusiness` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      CHANGE COLUMN `organization` `organization` INT UNSIGNED DEFAULT NULL,
      CHANGE COLUMN `location` `location` INT UNSIGNED DEFAULT NULL,
      CHANGE COLUMN `additionalType` `additionalType` VARCHAR(255) DEFAULT NULL,
      ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idlocalBusiness`,
      ADD COLUMN`openingHours` VARCHAR(255) DEFAULT NULL,
      ADD COLUMN `paymentAccepted` VARCHAR(255) DEFAULT NULL,
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

    -- insert organization
    INSERT INTO `organization` (`thing`,`address`,`hasOfferCatalog`,`location`)
    SELECT `thing`, `address`, `hasOfferCatalog`, `location` FROM `localBusiness`;

    -- update localBusiness
    UPDATE `localBusiness`
      JOIN `organization` ON `organization`.thing = `localBusiness`.thing
      SET `localBusiness`.organization = `organization`.idorganization;

    -- has contact point
    INSERT INTO `thing_has_thing` (idHasPart, typeHasPart, idIsPartOf, typeIsPartOf)
      SELECT `localBusiness`.thing, 'LocalBusiness', `contactPoint`.thing, 'ContactPoint' FROM `localBusiness_has_contactPoint`
      JOIN `localBusiness` ON `localBusiness`.idlocalBusiness = `localBusiness_has_contactPoint`.idlocalBusiness
      JOIN `contactPoint` ON `contactPoint`.idcontactPoint = `localBusiness_has_contactPoint`.idcontactPoint;

    -- insert images
    CALL insert_thing_has_thing('localBusiness','imageObject');

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
      CHANGE COLUMN `organization` `organization` INT UNSIGNED NOT NULL,
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
      DROP COLUMN `location`,
      DROP PRIMARY KEY ,
      ADD PRIMARY KEY (`idlocalBusiness`,`thing`);

    DROP TABLE `localBusiness_has_contactPoint`;
    DROP TABLE `localBusiness_has_imageObject`;
    DROP TABLE `localBusiness_has_person`;

    -- add foreign keys
    ALTER TABLE `localBusiness`
      ADD KEY `fk_localBusiness_thing_idx` (`thing`),
      ADD KEY `fk_localBusiness_organization_idx` (`organization`),
      ADD KEY `fk_localBusiness_place_idx` (`location`),
      ADD CONSTRAINT `fk_localBusiness_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
      ADD CONSTRAINT `fk_localBusiness_organization` FOREIGN KEY (`organization`) REFERENCES `organization` (`idorganization`) ON DELETE CASCADE ON UPDATE NO ACTION,
      ADD CONSTRAINT `fk_localBusiness_place` FOREIGN KEY (`location`) REFERENCES `place` (`idplace`) ON DELETE CASCADE ON UPDATE NO ACTION;

  END;