
--
-- LOCAL BUSINESS
--

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

-- CREATE THING

ALTER TABLE `thing` ADD COLUMN `idlocalBusiness` INT UNSIGNED DEFAULT NULL;

-- insert thing
INSERT INTO `thing` (`idlocalBusiness`,`name`,`additionalType`,`description`,`disambiguatingDescription`,`url`,`dateRegistered`,`lastModified`,`type`)
SELECT
  `idlocalBusiness`,
  `name`,
  CONCAT('schema:LocalBusiness,',`additionalType`),
   description,
   SUBSTRING(REGEXP_REPLACE(disambiguatingDescription, '<[^>]*>+', ''),1,255) as disambiguatingDescription,
   `url`,
   `dateCreated`,
   `dateModified`,
   'Organization'
FROM `localBusiness`;

-- update this
UPDATE `localBusiness`
  JOIN `thing` ON thing.idlocalBusiness = localBusiness.idlocalBusiness
  SET localBusiness.thing = thing.idthing
WHERE thing.name <> '';

-- drop thing column
ALTER TABLE `thing` DROP COLUMN `idlocalBusiness`;

-- END CREATE THING

-- set location null if idplace not exists
UPDATE `localBusiness`
SET `localBusiness`.location = NULL
WHERE `localBusiness`.location NOT IN (SELECT idplace FROM place);

-- muda o location de idplace para idthing de place
UPDATE `localBusiness`
  JOIN `place` ON place.idplace=localBusiness.location
  SET localBusiness.location=place.thing
WHERE localBusiness.location IS NOT NULL;

-- insert organization
INSERT INTO `organization` (`thing`,`address`,`hasOfferCatalog`,`location`)
SELECT `thing`, `address`, `hasOfferCatalog`, `location` FROM `localBusiness`;

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


ALTER TABLE `localBusiness`
  CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
  DROP COLUMN `name`,
  DROP COLUMN `additionalType`,
  DROP COLUMN `description`,
  DROP COLUMN `disambiguatingDescription`,
  DROP COLUMN `url`,
  DROP COLUMN `hasOfferCatalog`,
  DROP COLUMN `address`,
  DROP COLUMN `dateCreated`,
  DROP COLUMN `dateModified`,
  DROP COLUMN `location`,
  DROP COLUMN `organization`,
  DROP COLUMN `rank`,
  DROP PRIMARY KEY ,
  ADD PRIMARY KEY (`idlocalBusiness`,`thing`);

DROP TABLE `localBusiness_has_contactPoint`;
DROP TABLE `localBusiness_has_imageObject`;
DROP TABLE `localBusiness_has_person`;

-- add foreign keys
ALTER TABLE `localBusiness`
  ADD KEY `fk_localBusiness_thing_idx` (`thing`),
  ADD CONSTRAINT `fk_localBusiness_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION;
