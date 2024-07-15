-- ARTICLE
START TRANSACTION ;
  -- alter table
  ALTER TABLE `article`
    DROP COLUMN `additionalType`,
    CHANGE COLUMN `idarticle` `idarticle` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    CHANGE COLUMN `headline` `headline` VARCHAR(255) DEFAULT NULL,
    ADD COLUMN `creativeWork` INT UNSIGNED NOT NULL AFTER `idarticle`,
    ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idarticle`,
    ADD COLUMN `backstory` TEXT,
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (`idarticle`);
  -- insert thing
  INSERT INTO `thing` (`name`,`dateCreated`, `dateModified`, `type`)
    SELECT `headline`,`dateCreated`, `dateModified`, 'Article' FROM `article`;
  -- update this
  UPDATE `article`
    JOIN `thing` ON thing.name=article.headline AND thing.dateCreated=article.dateCreated AND thing.dateModified=article.dateModified
    SET article.thing=thing.idthing;
  -- insert parent
  INSERT INTO `creativeWork` (`thing`,`headline`,`datePublished`,`author`,`publisher`,`position`)
    SELECT `thing`,`headline`,`datePublished`,`author`,`publisher`,`position` FROM `article`;
  -- update child
  UPDATE `article`
    JOIN `creativeWork` ON creativeWork.thing=article.thing
    SET article.creativeWork=creativeWork.idcreativeWork;
  -- insert images
  INSERT INTO `thing_has_imageObject` (`idthing`,`idimageObject`,`position`,`representativeOfPage`,`caption`)
    SELECT `thing`,`idimageObject`,`article_has_imageObject`.`position`,`representativeOfPage`,`caption` FROM `article_has_imageObject`
      JOIN `article` ON `article_has_imageObject`.idarticle=article.idarticle;
  -- alter table
  ALTER TABLE `article`
    DROP COLUMN `headline`,
    DROP COLUMN `dateCreated`,
    DROP COLUMN `dateModified`,
    DROP COLUMN `datePublished`,
    DROP COLUMN `publishied`,
    DROP COLUMN `author`,
    DROP COLUMN `publisher`,
    DROP COLUMN `publisherType`,
    DROP COLUMN `position`,
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (`idarticle`,`creativeWork`,`thing`),
    ADD INDEX `fk_article_creativeWork_idx` (`creativeWork`),
    ADD INDEX `fk_article_thing_idx` (`thing`),
    ADD CONSTRAINT `fk_article_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE,
    ADD CONSTRAINT `fk_article_thing` FOREIGN KEY (`creativeWork`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE;
  -- drop old relationship
  DROP TABLE `article_has_imageObject`;

COMMIT ;