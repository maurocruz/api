CREATE PROCEDURE upgrade_book()
  BEGIN
    -- alter table
    ALTER TABLE `book`
      CHANGE COLUMN `idbook` `idbook` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      CHANGE COLUMN `datePublished` `datePublished` DATETIME DEFAULT NULL,
      ADD COLUMN `isbn` VARCHAR(18) NULL AFTER `idbook`,
      ADD COLUMN `illustrator` INT UNSIGNED NULL AFTER `idbook`,
      ADD COLUMN `bookFormat` VARCHAR(20) NULL AFTER `idbook`,
      ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idbook`,
      ADD COLUMN `creativeWork` INT UNSIGNED NOT NULL AFTER `idbook`,
      DROP PRIMARY KEY,
      ADD PRIMARY KEY (`idbook`);

    -- insert thing
    INSERT INTO `thing` (`name`,`dateCreated`, `dateModified`, `type`)
    SELECT `name`,`dateCreated`, `dateModified`, 'Book' FROM `book`;
    -- update this
    UPDATE `book`
      JOIN `thing` ON thing.name=book.name AND thing.dateCreated=book.dateCreated AND thing.dateModified=book.dateModified
    SET book.thing=thing.idthing;

    -- insert creativework
    INSERT INTO `creativeWork` (`thing`,`author`,`datePublished`,`keywords`,`locationCreated`,`publisher`,`version`)
    SELECT `thing`,`author`,`datePublished`,`keywords`,`locationCreated`,`publisher`,`version` FROM `book`;
    -- update child
    UPDATE `book`
      JOIN `creativeWork` ON creativeWork.thing=book.thing
    SET book.creativeWork=creativeWork.idcreativeWork;

    -- insert images
    INSERT INTO `thing_has_imageObject` (`idthing`,`idimageObject`,`position`,`representativeOfPage`,`caption`)
    SELECT thing, idimageObject, `book_has_imageObject`.position, IF(representativeOfPage is null,0,1), caption FROM `book_has_imageObject`
      JOIN `book` ON `book_has_imageObject`.idbook=`book`.idbook;

    -- alter table
    ALTER TABLE `book`
      DROP COLUMN `author`,
      DROP COLUMN `birthDate`,
      DROP COLUMN `deathDate`,
      DROP COLUMN `dateCreated`,
      DROP COLUMN `dateModified`,
      DROP COLUMN `datePublished`,
      DROP COLUMN `keywords`,
      DROP COLUMN `locationCreated`,
      DROP COLUMN `name`,
      DROP COLUMN `publisher`,
      DROP COLUMN `version`,
      DROP PRIMARY KEY,
      ADD PRIMARY KEY (`idbook`,`creativeWork`,`thing`),
      ADD INDEX `fk_book_creativeWork_idx` (`creativeWork`),
      ADD INDEX `fk_book_thing_idx` (`thing`);
    -- drop old relationship
    DROP TABLE `book_has_imageObject`;
END ;
