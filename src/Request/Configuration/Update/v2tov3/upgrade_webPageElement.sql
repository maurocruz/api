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
      IF (`name` <> '', `name`, 'Undefined name'),
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

  -- INSERT IN CREATIVEWORK
  INSERT INTO `creativeWork` (`thing`,`isPartOf`,`text`,`position`,`author`)
    SELECT `thing`,`isPartOf`,`text`,`position`,`author` FROM `webPageElement`;
  -- update child
  UPDATE `webPageElement`
    JOIN `creativeWork` ON creativeWork.thing = webPageElement.thing
    SET webPageElement.creativeWork = creativeWork.idcreativeWork;

  -- INSERT IMAGES
  INSERT INTO `thing_has_imageObject` (`idthing`,`idimageObject`,`position`,`representativeOfPage`,`caption`)
    SELECT `thing`,`idimageObject`,`webPageElement_has_imageObject`.`position`,`representativeOfPage`,`caption` FROM `webPageElement_has_imageObject`
    JOIN `webPageElement` ON `webPageElement_has_imageObject`.idwebPageElement=webPageElement.idwebPageElement;

  -- ALTER TABLE
  ALTER TABLE `webPageElement`
    CHANGE COLUMN `creativeWork` `creativeWork` INT UNSIGNED NOT NULL,
    CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
    DROP COLUMN `author`,
    DROP COLUMN `name`,
    DROP COLUMN `dateCreated`,
    DROP COLUMN `dateModified`,
    DROP COLUMN `isPartOf`,
    DROP COLUMN `text`,
    DROP COLUMN `position`,
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (`idwebPageElement`,`creativeWork`,`thing`);

  -- drop old relationship
  DROP TABLE `webPageElement_has_imageObject`;
END;