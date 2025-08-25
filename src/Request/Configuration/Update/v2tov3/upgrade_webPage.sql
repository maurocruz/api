-- WEBPAGE
CREATE PROCEDURE upgrade_webPage()
BEGIN
  -- ALTER TABLE
  ALTER TABLE `webPage`
    CHANGE COLUMN `idwebPage` `idwebPage` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    ADD COLUMN `creativeWork` INT UNSIGNED DEFAULT NULL AFTER `idwebPage`,
    ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idwebPage`,
    DROP PRIMARY KEY ,
    ADD PRIMARY KEY (`idwebPage`);

  -- INSERT THING
  ALTER TABLE `thing` ADD COLUMN `idwebPage` INT UNSIGNED DEFAULT NULL;
  -- insert thing
  INSERT INTO `thing` (`idwebPage`,`name`,`description`,`url`,`dateCreated`,`dateModified`,`type`)
    SELECT `idwebPage`,
      IF (`name` <> '', `name`, 'Undefined name'),
      description,
      `url`,
      if(`dateCreated` IS NULL, CURDATE(), `dateCreated`),
      `dateModified`,
      'WebPage'
      FROM `webPage`;
  -- set thing
  UPDATE `webPage`
    JOIN `thing` ON thing.idwebPage = webPage.idwebPage
    SET webPage.thing = thing.idthing;
  -- drop column
  ALTER TABLE `thing` DROP COLUMN `idwebPage`;

  -- UPDATE IS PART OF
  UPDATE `webPage`
    JOIN `webSite` ON webPage.ispartOf = webSite.idwebSite
    SET webPage.isPartOf = webSite.creativeWork;

  -- insert in creativeWork
  INSERT INTO `creativeWork` (`thing`,`isPartOf`,`alternativeHeadline`)
    SELECT `thing`,`isPartOf`,`alternativeHeadline` FROM `webPage`;
  -- update child
  UPDATE `webPage`
    JOIN `creativeWork` ON creativeWork.thing = webPage.thing
    SET webPage.creativeWork = creativeWork.idcreativeWork;

  -- has propertyValue
  INSERT INTO `thing_has_thing` (idHasPart, typeHasPart, idIsPartOf, typeIsPartOf)
  SELECT `webPage`.thing, 'WebPage', `propertyValue`.idpropertyValue, 'PropertyValue' FROM `webPage_has_propertyValue`
   JOIN `webPage` ON `webPage`.idwebPage = `webPage_has_propertyValue`.idwebPage
   JOIN `propertyValue` ON `propertyValue`.idpropertyValue = `webPage_has_propertyValue`.idpropertyValue;

  -- alter table
  ALTER TABLE `webPage`
    CHANGE COLUMN `creativeWork` `creativeWork` INT UNSIGNED NOT NULL,
    CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
    DROP COLUMN `alternativeHeadline`,
    DROP COLUMN `name`,
    DROP COLUMN `dateCreated`,
    DROP COLUMN `dateModified`,
    DROP COLUMN `description`,
    DROP COLUMN `url`,
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (`idwebPage`,`creativeWork`,`thing`);

  DROP TABLE `webPage_has_propertyValue`;

  -- add foreign key
  ALTER TABLE `webPage`
    ADD KEY `fk_webPage_thing_idx` (`thing`),
    ADD KEY `fk_webPage_creativeWork_idx` (`creativeWork`),
    ADD CONSTRAINT `fk_webPage_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_webPage_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE ON UPDATE NO ACTION;

END;