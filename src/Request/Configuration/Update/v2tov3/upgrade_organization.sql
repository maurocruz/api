
--
-- ORGANIZATION
--

ALTER TABLE `organization`
  CHANGE COLUMN `idorganization` `idorganization` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  CHANGE COLUMN `areaServed` `areaServed` INT UNSIGNED DEFAULT NULL,
  CHANGE COLUMN `location` `location` INT UNSIGNED DEFAULT NULL,
  ADD COLUMN `thing` INT UNSIGNED NOT NULL AFTER `idorganization`,
  ADD COLUMN `logo` INT UNSIGNED DEFAULT NULL AFTER `idorganization`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idorganization`);

-- INSERT THING
ALTER TABLE `thing` ADD COLUMN `idorganization` INT UNSIGNED DEFAULT NULL;

-- insert thing
INSERT INTO `thing` (`idorganization`,`name`,`additionalType`,`description`,`disambiguatingDescription`,`url`,`dateRegistered`,`lastModified`,`type`)
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
  SET organization.thing = thing.idthing
WHERE thing.name <> '';

-- drop column
ALTER TABLE `thing` DROP COLUMN `idorganization`;

-- set location null if idplace not exists
UPDATE `organization`
  SET `organization`.location = NULL
  WHERE `organization`.location NOT IN (SELECT idplace FROM place);

-- muda o location de idplace para idthing de place
UPDATE `organization`
  JOIN `place` ON place.idplace=organization.location
SET organization.location=place.thing
WHERE organization.location IS NOT NULL ;

-- has contact point
INSERT INTO `thing_has_thing` (idHasPart, typeHasPart, idIsPartOf, typeIsPartOf)
SELECT `organization`.thing, 'Organization', `contactPoint`.thing, 'ContactPoint' FROM `organization_has_contactPoint`
  JOIN `organization` ON `organization`.idorganization = `organization_has_contactPoint`.idorganization
  JOIN `contactPoint` ON `contactPoint`.idcontactPoint = `organization_has_contactPoint`.idcontactPoint;

-- insert images
CALL insert_thing_has_thing('organization','imageObject');

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

-- add foreign keys
ALTER TABLE `organization`
  ADD KEY `fk_organization_thing_idx` (`thing`),
  ADD KEY `fk_organization_location_idx` (`location`),
  ADD CONSTRAINT `fk_organization_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_organization_location` FOREIGN KEY (`location`) REFERENCES `place` (`thing`) ON DELETE CASCADE ON UPDATE NO ACTION;
