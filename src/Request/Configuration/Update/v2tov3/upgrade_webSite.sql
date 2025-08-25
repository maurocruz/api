-- WEBSITE
CREATE PROCEDURE upgrade_webSite()
BEGIN
  -- ALTER TABLE
  ALTER TABLE `webSite`
    CHANGE COLUMN `idwebSite` `idwebSite` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    ADD COLUMN `creativeWork` INT UNSIGNED DEFAULT NULL AFTER `idwebSite`,
    ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idwebSite`,
    DROP PRIMARY KEY ,
    ADD PRIMARY KEY (`idwebSite`);

  -- INSERT THING
  ALTER TABLE `thing` ADD COLUMN `idwebSite` INT UNSIGNED DEFAULT NULL;
  -- insert thing
  INSERT INTO `thing` (`idwebSite`,`name`,`description`,`url`,`dateCreated`,`dateModified`,`type`)
    SELECT `idwebSite`,
      IF (`name` <> '', `name`, 'Undefined name'),
      description,
      `url`,
      if(`dateCreated` IS NULL, CURDATE(), `dateCreated`),
      CURDATE(),
      'WebSite'
    FROM `webSite`;
  -- set thing
  UPDATE `webSite`
    JOIN `thing` ON thing.idwebSite = webSite.idwebSite
    SET webSite.thing = thing.idthing;
  -- drop column
  ALTER TABLE `thing` DROP COLUMN `idwebSite`;

  -- INSERT IN CREATIVEWORK
  INSERT INTO `creativeWork` (`thing`,`publisher`,`editor`)
  SELECT `thing`,`publisher`,`editor` FROM `webSite`;
  -- update child
  UPDATE `webSite`
    JOIN `creativeWork` ON creativeWork.thing = webSite.thing
    SET webSite.creativeWork = creativeWork.idcreativeWork;

  -- HAS PERSON
  INSERT INTO `thing_has_thing` (idHasPart, typeHasPart, idIsPartOf, typeIsPartOf)
  SELECT `webSite`.thing,'WebSite',`person`.thing,'Person' FROM `webSite_has_person`
    JOIN `webSite` ON `webSite`.idwebSite = `webSite_has_person`.idwebSite
    JOIN `person` ON `person`.idperson = `webSite_has_person`.idperson;

  -- ALTER TABLE
  ALTER TABLE `webSite`
    CHANGE COLUMN `creativeWork` `creativeWork` INT UNSIGNED NOT NULL,
    CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
    DROP COLUMN `name`,
    DROP COLUMN `dateCreated`,
    DROP COLUMN `description`,
    DROP COLUMN `image`,
    DROP COLUMN `url`,
    DROP COLUMN `publisher`,
    DROP COLUMN `editor`,
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (`idwebSite`,`creativeWork`,`thing`);

  -- drop old relationship
  DROP TABLE `webSite_has_person`;

  -- add foreign key
  ALTER TABLE `webSite`
    ADD KEY `fk_webSite_thing_idx` (`thing`),
    ADD KEY `fk_webSite_creativeWork_idx` (`creativeWork`),
    ADD CONSTRAINT `fk_webSite_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_webSite_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE ON UPDATE NO ACTION;

END;