-- ORGANIZATION
CREATE PROCEDURE upgrade_organization()
  BEGIN
    -- mesclar dados duplicados
    DROP TABLE IF EXISTS `organization_tmp`;
    CREATE TABLE `organization_tmp` AS
      SELECT
        max(idorganization) as idorganization,
        max(additionalType) as additionalType,
        name,
        max(description) as description,
        max(disambiguatingDescription) as disambiguatingDescription,
        max(legalName) as legalName,
        max(taxId) as taxId,
        max(url) as url,
        max(hasOfferCatalog) as hasOfferCatalog,
        max(location) as location,
        max(address) as address,
        max(areaServed) as areaServed,
        max(dateCreated) as dateCreated,
        max(dateModified) as dateModified
      FROM organization GROUP BY name HAVING count(name) > 1;
    INSERT INTO `organization_tmp` SELECT * FROM `organization` GROUP BY name HAVING count(name) = 1;
    RENAME TABLE `organization` TO `organization_old`, `organization_tmp` TO `organization`;
    DROP TABLE `organization_old`;

    -- alter table
    ALTER TABLE `organization`
      CHANGE COLUMN `idorganization` `idorganization` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      CHANGE COLUMN `areaServed` `areaServed` INT UNSIGNED DEFAULT NULL,
      CHANGE COLUMN `location` `location` INT UNSIGNED DEFAULT NULL,
      ADD COLUMN `thing` INT UNSIGNED NOT NULL AFTER `idorganization`,
      ADD COLUMN `logo` INT UNSIGNED DEFAULT NULL AFTER `idorganization`,
      ADD PRIMARY KEY (`idorganization`);

    -- INSERT THING
    ALTER TABLE `thing` ADD COLUMN `idorganization` INT UNSIGNED DEFAULT NULL;
    -- insert thing
    INSERT INTO `thing` (`idorganization`,`name`,`additionalType`,`description`,`disambiguatingDescription`,`url`,`dateCreated`,`dateModified`,`type`)
    SELECT `idorganization`,
       IF (`name` <> '', `name`, 'Undefined name'),
       `additionalType`,
       description,
       SUBSTRING(REGEXP_REPLACE(disambiguatingDescription, '<[^>]*>+', ''),1,255) as disambiguatingDescription,
       `url`,
       if(`dateCreated` IS NULL, CURDATE(), `dateCreated`),
       `dateModified`,
       'Organization'
    FROM `organization`;
    -- set thing
    UPDATE `organization`
      JOIN `thing` ON thing.idorganization = organization.idorganization
      SET organization.thing = thing.idthing;
    -- drop column
    ALTER TABLE `thing` DROP COLUMN `idorganization`;

    UPDATE `organization`
      SET `organization`.location = NULL
      WHERE `organization`.location NOT IN (SELECT idplace FROM place);

    UPDATE `localBusiness`
    SET `localBusiness`.organization = NULL
    WHERE `localBusiness`.organization NOT IN (SELECT idorganization FROM organization);

    -- has contact point
    INSERT INTO `thing_has_thing` (idHasPart, typeHasPart, idIsPartOf, typeIsPartOf)
    SELECT `organization`.thing, 'Organization', `contactPoint`.thing, 'ContactPoint' FROM `organization_has_contactPoint`
      JOIN `organization` ON `organization`.idorganization = `organization_has_contactPoint`.idorganization
      JOIN `contactPoint` ON `contactPoint`.idcontactPoint = `organization_has_contactPoint`.idcontactPoint;

    -- has images
    INSERT INTO `thing_has_imageObject` (`idthing`,`idimageObject`,`position`,`representativeOfPage`,`caption`)
    SELECT `thing`,`idimageObject`,`organization_has_imageObject`.`position`,`representativeOfPage`,`caption` FROM `organization_has_imageObject`
      JOIN `organization` ON `organization_has_imageObject`.idorganization = organization.idorganization;

    -- IMAGES
    CALL set_image_in_thing('organization');

    -- has person
    INSERT INTO `thing_has_thing` (idHasPart, typeHasPart, idIsPartOf, typeIsPartOf, caption)
    SELECT `organization`.thing,'organization',`person`.thing,'Person',jobTitle FROM `organization_has_person`
      JOIN `organization` ON `organization`.idorganization = `organization_has_person`.idorganization
      JOIN `person` ON `person`.idperson = `organization_has_person`.idperson;


    ALTER TABLE `organization`
      CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
      DROP COLUMN `name`,
      DROP COLUMN `additionalType`,
      DROP COLUMN `description`,
      DROP COLUMN `disambiguatingDescription`,
      DROP COLUMN `url`,
      DROP COLUMN `dateCreated`,
      DROP COLUMN `dateModified`,
      DROP PRIMARY KEY ,
      ADD PRIMARY KEY (`idorganization`,`thing`);

    DROP TABLE `organization_has_contactPoint`;
    DROP TABLE `organization_has_imageObject`;
    DROP TABLE `organization_has_person`;


  end ;