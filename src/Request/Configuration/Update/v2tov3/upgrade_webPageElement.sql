-- WEBPAGE ELEMENT
-- ALTER TABLE
ALTER TABLE `webPageElement`
  CHANGE COLUMN `idwebPageElement` `idwebPageElement` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ADD COLUMN `creativeWork` INT UNSIGNED DEFAULT NULL AFTER `idwebPageElement`,
  ADD COLUMN `cssSelector` TEXT AFTER `creativeWork`,
  ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `cssSelector`,
  DROP PRIMARY KEY ,
  ADD PRIMARY KEY (`idwebPageElement`);

-- INSERT THING
ALTER TABLE `thing` ADD COLUMN `idwebPageElement` INT UNSIGNED DEFAULT NULL;
-- insert thing
INSERT INTO `thing` (`idwebPageElement`,`name`,`dateRegistered`,`lastModified`,`type`)
  SELECT `idwebPageElement`,
         IF (`name` <> '' AND `name` IS NOT NULL, `name`, 'Undefined name'),
         if(`dateCreated`, `dateCreated`, CURDATE()),
         `dateModified`,
    'WebPageElement'
  FROM `webPageElement`;
-- set thing
UPDATE `webPageElement`
  JOIN `thing` ON thing.idwebPageElement = webPageElement.idwebPageElement
  SET webPageElement.thing = thing.idthing;
-- drop column
ALTER TABLE `thing` DROP COLUMN `idwebPageElement`;


-- INSERT IN CREATIVEWORK
INSERT INTO `creativeWork` (`thing`,`text`,`position`,`author`)
  SELECT `thing`,`text`,`position`,`author` FROM `webPageElement`;
-- update child
UPDATE `webPageElement`
  JOIN `creativeWork` ON creativeWork.thing = webPageElement.thing
  SET webPageElement.creativeWork = creativeWork.idcreativeWork;

-- has propertyValue
INSERT INTO `thing_has_thing` (idHasPart, typeHasPart, idIsPartOf, typeIsPartOf)
SELECT `webPageElement`.thing, 'WebPageElement', `propertyValue`.idpropertyValue, 'PropertyValue' FROM `webPageElement_has_propertyValue`
 JOIN `webPageElement` ON `webPageElement`.idwebPageElement = `webPageElement_has_propertyValue`.idwebPageElement
 JOIN `propertyValue` ON `propertyValue`.idpropertyValue = `webPageElement_has_propertyValue`.idpropertyValue;

-- is part of
-- UPDATE IS PART OF
UPDATE `webPageElement`
  JOIN `webPage` ON webPageElement.ispartOf = webPage.idwebPage
SET webPageElement.isPartOf = webPage.thing;

INSERT INTO `thing_has_thing` (idHasPart, typeHasPart, idIsPartOf, typeIsPartOf)
SELECT `webPageElement`.isPartOf, 'WebPage', `webPageElement`.thing, 'WebPageElement' FROM `webPageElement`
JOIN `thing` on `thing`.idthing = `webPageElement`.isPartOf;

UPDATE `thing`
  JOIN `webPageElement`ON `thing`.idthing = `webPageElement`.thing
  JOIN `webPageElement_has_imageObject` ON `href` is not null AND `href`<>'' AND `webPageElement_has_imageObject`.idwebPageElement=webPageElement.idwebPageElement
SET thing.sameAs = webPageElement_has_imageObject.href;

-- IMAGES
CALL set_image_in_thing('webPageElement');

-- insert images
CALL insert_thing_has_thing('webPageElement','imageObject');

-- ALTER TABLE
ALTER TABLE `webPageElement`
  CHANGE COLUMN `creativeWork` `creativeWork` INT UNSIGNED NOT NULL,
  CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
  DROP COLUMN `author`,
  DROP COLUMN `name`,
  DROP COLUMN `dateCreated`,
  DROP COLUMN `dateModified`,
  DROP COLUMN `gallery`,
  DROP COLUMN `isPartOf`,
  DROP COLUMN `text`,
  DROP COLUMN `position`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idwebPageElement`,`creativeWork`,`thing`);

-- drop old relationship
DROP TABLE `webPageElement_has_imageObject`;
DROP TABLE `webPageElement_has_propertyValue`;

-- add foreign key
ALTER TABLE `webPageElement`
  ADD KEY `fk_webPageElement_thing_idx` (`thing`),
  ADD KEY `fk_webPageElement_creativeWork_idx` (`creativeWork`),
  ADD CONSTRAINT `fk_webPageElement_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_webPageElement_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE ON UPDATE NO ACTION;
