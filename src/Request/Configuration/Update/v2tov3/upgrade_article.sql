-- ARTICLE
CREATE PROCEDURE upgrade_article()
  BEGIN
    -- alter table
    ALTER TABLE `article`
      DROP COLUMN `additionalType`,
      CHANGE COLUMN `idarticle` `idarticle` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      CHANGE COLUMN `headline` `headline` VARCHAR(255) DEFAULT NULL,
      ADD COLUMN `creativeWork` INT UNSIGNED DEFAULT NULL AFTER `idarticle`,
      ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idarticle`,
      ADD COLUMN `backstory` TEXT,
      DROP PRIMARY KEY,
      ADD PRIMARY KEY (`idarticle`);

    -- INSERT THING
    ALTER TABLE `thing` ADD COLUMN `idarticle` INT UNSIGNED DEFAULT NULL;
    -- insert thing
    INSERT INTO `thing` (`idarticle`,`name`,`dateCreated`, `dateModified`, `type`)
      SELECT `idarticle`,`headline`,`dateCreated`, `dateModified`, 'Article' FROM `article`;
    -- update this
    UPDATE `article`
      JOIN `thing` ON thing.idarticle = article.idarticle
      SET article.thing = thing.idthing;
    -- drop thing column
    ALTER TABLE `thing` DROP COLUMN `idarticle`;

    -- insert creative work
    INSERT INTO `creativeWork` (`thing`,`headline`,`datePublished`,`author`,`publisher`,`position`)
      SELECT `thing`,`headline`,`datePublished`,`author`,`publisher`,`position` FROM `article`;
    -- update child
    UPDATE `article`
      JOIN `creativeWork` ON creativeWork.thing=article.thing
      SET article.creativeWork=creativeWork.idcreativeWork;

    -- IMAGES
    CALL set_image_in_thing('article');

    INSERT INTO `thing_has_imageObject` (`idthing`,`idimageObject`,`position`,`representativeOfPage`,`caption`)
      SELECT `thing`,`idimageObject`,`article_has_imageObject`.`position`,`representativeOfPage`,`caption` FROM `article_has_imageObject`
        JOIN `article` ON `article_has_imageObject`.idarticle=article.idarticle;

    -- alter table
    ALTER TABLE `article`
      CHANGE COLUMN `creativeWork` `creativeWork` INT UNSIGNED NOT NULL,
      CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
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
      ADD PRIMARY KEY (`idarticle`,`creativeWork`,`thing`);

    -- drop old relationship
    DROP TABLE `article_has_imageObject`;
END ;