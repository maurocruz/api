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
      SUBSTRING(REGEXP_REPLACE(description, '<[^>]*>+', ''),1,255) as description,
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

  -- insert in creativeWork
  INSERT INTO `creativeWork` (`thing`,`isPartOf`,`alternativeHeadline`)
    SELECT `thing`,`isPartOf`,`alternativeHeadline` FROM `webPage`;
  -- update child
  UPDATE `webPage`
    JOIN `creativeWork` ON creativeWork.thing = webPage.thing
    SET webPage.creativeWork = creativeWork.idcreativeWork;

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

END;