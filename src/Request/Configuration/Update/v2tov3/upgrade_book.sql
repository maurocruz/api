
--
-- BOOK
--

ALTER TABLE `book`
  CHANGE COLUMN `idbook` `idbook` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  CHANGE COLUMN `datePublished` `datePublished` VARCHAR(19) DEFAULT NULL,
  ADD COLUMN `isbn` VARCHAR(18) NULL AFTER `idbook`,
  ADD COLUMN `illustrator` INT UNSIGNED NULL AFTER `idbook`,
  ADD COLUMN `bookFormat` VARCHAR(20) NULL AFTER `idbook`,
  ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idbook`,
  ADD COLUMN `creativeWork` INT UNSIGNED NOT NULL AFTER `idbook`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idbook`);

-- INSERT THING
ALTER TABLE `thing` ADD COLUMN `idbook` INT UNSIGNED DEFAULT NULL;

-- insert thing
INSERT INTO `thing` (`idbook`,`name`, `lastModified`, `dateRegistered`, `type`)
  SELECT
    `idbook`,
    `name`,
    `dateModified`,
    `dateCreated`,
    'Book'
  FROM `book`;

-- update this
UPDATE `book`
  JOIN `thing` ON thing.idbook = book.idbook
  SET book.thing=thing.idthing
WHERE thing.name <> '';

-- drop thing column
ALTER TABLE `thing` DROP COLUMN `idbook`;

-- insert creativework
INSERT INTO `creativeWork` (`thing`,`author`,`datePublished`,`keywords`,`locationCreated`,`publisher`,`version`)
  SELECT
    `thing`,
    `author`,
    CONCAT(`datePublished`,'-01-01 00:00:00') as datetime,
    `keywords`,
    `locationCreated`,
    `publisher`,
    `version`
  FROM `book`;

-- update child
UPDATE `book`
  JOIN `creativeWork` ON creativeWork.thing=book.thing
  SET book.creativeWork=creativeWork.idcreativeWork
WHERE book.name <> '';

-- IMAGES
CALL set_image_in_thing('book');

-- insert images
CALL insert_thing_has_thing('book','imageObject');

-- alter table
ALTER TABLE `book`
  CHANGE COLUMN `creativeWork` `creativeWork` INT UNSIGNED NOT NULL,
  CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
  DROP COLUMN `author`,
  DROP COLUMN `birthDate`,
  DROP COLUMN `deathDate`,
  DROP COLUMN `datePublished`,
  DROP COLUMN `keywords`,
  DROP COLUMN `locationCreated`,
  DROP COLUMN `name`,
  DROP COLUMN `publisher`,
  DROP COLUMN `version`,
  DROP COLUMN `dateCreated`,
  DROP COLUMN `dateModified`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idbook`,`creativeWork`,`thing`);

-- drop old relationship
DROP TABLE `book_has_imageObject`;

-- add foreign keys
ALTER TABLE `book`
  ADD KEY `fk_book_thing_idx` (`thing`),
  ADD KEY `fk_book_creativeWork_idx` (`creativeWork`),
  ADD CONSTRAINT `fk_book_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_book_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE ON UPDATE NO ACTION;
