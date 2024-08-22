-- WEBPAGE ELEMENT
CREATE PROCEDURE upgrade_webPageElement()
BEGIN
  -- ALTER TABLE
  ALTER TABLE `webPageElement`
    CHANGE COLUMN `idwebPageElement` `idwebPageElement` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    ADD COLUMN `creativeWork` INT UNSIGNED DEFAULT NULL AFTER `idwebPageElement`,
    ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idwebPageElement`,
    DROP PRIMARY KEY ,
    ADD PRIMARY KEY (`idwebPageElement`);

  -- INSERT THING
  ALTER TABLE `thing` ADD COLUMN `idwebPageElement` INT UNSIGNED DEFAULT NULL;
  -- insert thing
  INSERT INTO `thing` (`idwebPageElement`,`name`,`dateCreated`,`dateModified`,`type`)
    SELECT `idwebPageElement`,
           IF (`name` <> '' AND `name` IS NOT NULL, `name`, 'Undefined name'),
           if(`dateCreated` IS NULL, CURDATE(), `dateCreated`),
           `dateModified`,
      'WebPageElement'
    FROM `webPageElement`;
  -- set thing
  UPDATE `webPageElement`
    JOIN `thing` ON thing.idwebPageElement = webPageElement.idwebPageElement
    SET webPageElement.thing = thing.idthing;
  -- drop column
  ALTER TABLE `thing` DROP COLUMN `idwebPageElement`;

  -- UPDATE IS PART OF
  UPDATE `webPageElement`
    JOIN `webPage` ON webPageElement.ispartOf = webPage.idwebPage
  SET webPageElement.isPartOf = webPage.creativeWork;

  -- INSERT IN CREATIVEWORK
  INSERT INTO `creativeWork` (`thing`,`isPartOf`,`text`,`position`,`author`)
    SELECT `thing`,`isPartOf`,`text`,`position`,`author` FROM `webPageElement`;
  -- update child
  UPDATE `webPageElement`
    JOIN `creativeWork` ON creativeWork.thing = webPageElement.thing
    SET webPageElement.creativeWork = creativeWork.idcreativeWork;

  -- has propertyValue
  INSERT INTO `thing_has_thing` (idHasPart, typeHasPart, idIsPartOf, typeIsPartOf)
  SELECT `webPageElement`.thing, 'WebPageElement', `propertyValue`.idpropertyValue, 'PropertyValue' FROM `webPageElement_has_propertyValue`
   JOIN `webPageElement` ON `webPageElement`.idwebPageElement = `webPageElement_has_propertyValue`.idwebPageElement
   JOIN `propertyValue` ON `propertyValue`.idpropertyValue = `webPageElement_has_propertyValue`.idpropertyValue;

  -- INSERT IMAGES
  INSERT INTO `thing_has_imageObject` (`idthing`,`idimageObject`,`href`,`position`,`representativeOfPage`,`caption`)
    SELECT `thing`,`idimageObject`,`href`,`webPageElement_has_imageObject`.`position`,`representativeOfPage`,`caption` FROM `webPageElement_has_imageObject`
    JOIN `webPageElement` ON `webPageElement_has_imageObject`.idwebPageElement=webPageElement.idwebPageElement;

  -- IMAGES
  UPDATE `thing`
    join webPageElement ON webPageElement.thing = thing.idthing
    join webPageElement_has_imageObject ON webPageElement_has_imageObject.idwebPageElement = webPageElement.idwebPageElement and webPageElement_has_imageObject.representativeOfPage <> 0
    join imageObject ON imageObject.idimageObject = webPageElement_has_imageObject.idimageObject
    join mediaObject ON mediaObject.idmediaObject = imageObject.mediaObject
  SET `thing`.image = `mediaObject`.contentUrl;

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
END;