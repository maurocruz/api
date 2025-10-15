--
-- IMAGEOBJECT
--
ALTER TABLE `imageObject`
  CHANGE COLUMN `idimageObject` `idimageObject` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idimageObject`,
  ADD COLUMN `mediaObject` INT UNSIGNED DEFAULT NULL AFTER `idimageObject`,
  ADD COLUMN `creativeWork` INT UNSIGNED DEFAULT NULL AFTER `idimageObject`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idimageObject`);

-- INSERT THING
ALTER TABLE `thing` ADD COLUMN `idimageObject` INT UNSIGNED DEFAULT NULL;
-- insere dados em thing
INSERT INTO `thing` (`idimageObject`,`name`,`dateRegistered`,`type`)
  SELECT `idimageObject`,
         IF(`contentUrl` <> '',`contentUrl`,'Undefined name'),
         `uploadDate`,'ImageObject' from `imageObject`;
-- update this
UPDATE `imageObject`
JOIN `thing` ON `imageObject`.idimageObject = `thing`.idimageObject
SET `imageObject`.thing=`thing`.idthing
WHERE `thing`.idimageObject IS NOT NULL;
-- drop thing column
ALTER TABLE `thing` DROP COLUMN `idimageObject`;

-- insert parent
INSERT INTO `creativeWork` (`thing`,`author`,`license`,`acquireLicensePage`,`thumbnail`,`keywords`,`copyrightHolder`)
SELECT `thing`,`author`,`license`,`acquireLicensePage`,`thumbnail`,`keywords`,`copyright` from `imageObject`;

-- insere parent
INSERT INTO `mediaObject` (`thing`,`creativeWork`,`contentSize`,`contentUrl`,`encodingFormat`,`height`,`width`,`uploadDate`)
SELECT `imageObject`.`thing`,`idcreativeWork`,`contentSize`,`contentUrl`,`imageObject`.`encodingFormat`,`height`,`width`,`uploadDate` FROM `imageObject`
 JOIN `creativeWork` ON `creativeWork`.thing=`imageObject`.thing
 WHERE `contentUrl` <> '';

-- update this
UPDATE `imageObject`
JOIN `mediaObject` ON `imageObject`.thing = `mediaObject`.thing
JOIN `creativeWork` ON `imageObject`.thing = `creativeWork`.thing
SET `imageObject`.mediaObject = `mediaObject`.idmediaObject, `imageObject`.creativeWork = `creativeWork`.idcreativeWork
WHERE `imageObject`.thing = `mediaObject`.thing;

-- alter table
ALTER TABLE `imageObject`
  CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
  CHANGE COLUMN `mediaObject` `mediaObject` INT UNSIGNED NOT NULL,
  CHANGE COLUMN `creativeWork` `creativeWork` INT UNSIGNED NOT NULL,
  DROP COLUMN `contentUrl`,
  DROP COLUMN `contentSize`,
  DROP COLUMN `width`,
  DROP COLUMN `height`,
  DROP COLUMN `encodingFormat`,
  DROP COLUMN `author`,
  DROP COLUMN `license`,
  DROP COLUMN `acquireLicensePage`,
  DROP COLUMN `uploadDate`,
  DROP COLUMN `thumbnail`,
  DROP COLUMN `keywords`,
  DROP COLUMN `copyright`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idimageObject`,`mediaObject`,`thing`);

-- add forign keys
ALTER TABLE `imageObject`
  ADD KEY `fk_imageObject_thing_idx` (`thing`),
  ADD KEY `fk_imageObject_creativeWork_idx` (`creativeWork`),
  ADD KEY `fk_imageObject_mediaObject_idx` (`mediaObject`),
  ADD CONSTRAINT `fk_imageObject_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_imageObject_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_imageObject_mediaObject` FOREIGN KEY (`mediaObject`) REFERENCES `mediaObject` (`idmediaObject`) ON DELETE CASCADE ON UPDATE NO ACTION;
