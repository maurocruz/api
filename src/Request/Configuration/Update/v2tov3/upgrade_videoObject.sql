-- VIDEO OBJECT
CREATE PROCEDURE upgrade_videoObject()
BEGIN
  -- ALTER TABLE
  ALTER TABLE `videoObject`
    CHANGE COLUMN `idvideoObject` `idvideoObject` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idvideoObject`,
    ADD COLUMN `mediaObject` INT UNSIGNED DEFAULT NULL AFTER `idvideoObject`,
    ADD COLUMN `creativeWork` INT UNSIGNED DEFAULT NULL AFTER `idvideoObject`,
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (`idvideoObject`),
    ENGINE = InnoDB ;

  -- insert thing
  ALTER TABLE `thing` ADD COLUMN `idvideoObject` INT UNSIGNED DEFAULT NULL;
  INSERT INTO `thing` (`idvideoObject`,`name`,`url`,`image`,`description`,`dateCreated`,`type`)
    SELECT `idvideoObject`,`name`,`url`,
      thumbnailUrl,
            description,
`uploadDate`,
      'videoObject'
    FROM `videoObject` WHERE `name` <> '';
  -- update this
  UPDATE `videoObject` JOIN `thing` ON `videoObject`.idvideoObject = thing.idvideoObject SET `videoObject`.thing = `thing`.idthing;
  ALTER TABLE `thing` DROP COLUMN `idvideoObject`;

  -- insert parent
  INSERT INTO `creativeWork` (`thing`,`thumbnail`,`keywords`,`position`)
    SELECT `thing`,`thumbnailUrl`,`tag`,`position` from `videoObject`;

  -- insere parent
  INSERT INTO `mediaObject` (`thing`,`creativeWork`,`contentUrl`,`bitrate`,`duration`,`uploadDate`)
    SELECT `videoObject`.`thing`,`idcreativeWork`,`contentUrl`,`bitrate`,`duration`,`uploadDate` FROM `videoObject`
    JOIN `creativeWork` ON `creativeWork`.thing = `videoObject`.thing;

  -- update this
  UPDATE `videoObject`
    JOIN `mediaObject` ON `videoObject`.thing = `mediaObject`.thing
    JOIN `creativeWork` ON `videoObject`.thing = `creativeWork`.thing
    SET `videoObject`.mediaObject = `mediaObject`.idmediaObject, `videoObject`.creativeWork = `creativeWork`.idcreativeWork;

  -- alter table
  ALTER TABLE `videoObject`
    CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
    CHANGE COLUMN `mediaObject` `mediaObject` INT UNSIGNED NOT NULL,
    CHANGE COLUMN `creativeWork` `creativeWork` INT UNSIGNED NOT NULL,
    DROP COLUMN `contentUrl`,
    DROP COLUMN `uploadDate`,
    DROP COLUMN `thumbnailUrl`,
    DROP COLUMN `tag`,
    DROP COLUMN `position`,
    DROP COLUMN `bitrate`,
    DROP COLUMN `duration`,
    DROP COLUMN `name`,
    DROP COLUMN `url`,
    DROP COLUMN `description`,
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (`idvideoObject`,`mediaObject`,`thing`);

  -- add foreign key
  ALTER TABLE `videoObject`
    ADD KEY `fk_videoObject_thing_idx` (`thing`),
    ADD KEY `fk_videoObject_creativeWork_idx` (`creativeWork`),
    ADD KEY `fk_videoObject_mediaObject_idx` (`mediaObject`),
    ADD CONSTRAINT `fk_videoObject_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_videoObject_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_videoObject_mediaObject` FOREIGN KEY (`mediaObject`) REFERENCES `mediaObject` (`idmediaObject`) ON DELETE CASCADE ON UPDATE NO ACTION;


END;