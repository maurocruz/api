
--
-- PERSON
--

ALTER TABLE `person`
  CHANGE COLUMN `idperson` `idperson` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  CHANGE COLUMN `address` `homeLocation` INT UNSIGNED NULL,
  ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idperson`,
  ADD COLUMN `deathDate` DATE DEFAULT NULL,
  ADD COLUMN `deathPlace` VARCHAR(45) DEFAULT NULL,
  DROP PRIMARY KEY ,
  ADD PRIMARY KEY (`idperson`);

-- CREATE THING
ALTER TABLE `thing` ADD COLUMN `idperson` INT UNSIGNED DEFAULT NULL;

-- insert thing
INSERT INTO `thing` (`idperson`,`name`,`url`,`lastModified`,`dateRegistered`,`type`)
  SELECT
    idperson,
    CONCAT(`givenName`,IF(familyName is NOT NULL,CONCAT(' ',familyName),'')) as name,
    `url`,
    `dateModified`,
    `dateRegistration`,
    'Person'
  FROM `person`;

-- atualiza tabela
UPDATE `person`
  JOIN `thing` ON thing.idperson=person.idperson SET person.thing = thing.idthing
WHERE 1;

ALTER TABLE `thing` DROP COLUMN `idperson`;

-- has contact point
INSERT INTO `thing_has_thing` (idHasPart, typeHasPart, idIsPartOf, typeIsPartOf)
  SELECT
    `person`.thing,
    'Person',
    `contactPoint`.thing,
    'ContactPoint'
  FROM `person_has_contactPoint`
  JOIN `person` ON `person`.idperson = `person_has_contactPoint`.idperson
  JOIN `contactPoint` ON `contactPoint`.idcontactPoint = `person_has_contactPoint`.idcontactPoint;

-- IMAGES
CALL set_image_in_thing('person');

-- insert images
CALL insert_thing_has_thing('person','imageObject');

ALTER TABLE `person`
  CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
  DROP COLUMN `name`,
  DROP COLUMN `url`,
  DROP COLUMN `dateRegistration`,
  DROP COLUMN `dateModified`,
  DROP PRIMARY KEY ,
  ADD PRIMARY KEY (`idperson`,`thing`);

DROP TABLE `person_has_contactPoint`;
DROP TABLE `person_has_imageObject`;

-- add foreign key
ALTER TABLE `person`
  ADD KEY `fk_person_thing_idx` (`thing`),
  ADD CONSTRAINT `fk_person_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION;

