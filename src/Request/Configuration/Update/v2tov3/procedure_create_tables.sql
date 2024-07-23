CREATE PROCEDURE create_tables()
  BEGIN
    -- CREATE TABLE article
    CREATE TABLE IF NOT EXISTS `article` (
     `idarticle` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
     `creativeWork` INT(10) UNSIGNED NOT NULL,
     `thing` INT(10) UNSIGNED NOT NULL,
     `articleBody` TEXT,
     `articleSection` VARCHAR(255) DEFAULT NULL,
     `backstory` TEXT,
     PRIMARY KEY (`idarticle`,`creativeWork`,`thing`),
     KEY `fk_article_creativeWork_idx` (`creativeWork`)
    ) ENGINE = InnoDB;

    -- CREATE TABLE book
    CREATE TABLE IF NOT EXISTS `book` (
      `idbook` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `creativeWork` INT UNSIGNED NOT NULL,
      `thing` INT UNSIGNED NOT NULL,
      `bookEdition` VARCHAR(24) DEFAULT '',
      `bookFormat` VARCHAR(20) NULL,
      `illustrator` INT UNSIGNED NULL,
      `isbn` VARCHAR(18) NULL,
      `numberOfPages` VARCHAR(24) DEFAULT '',
      PRIMARY KEY (`idbook`,`creativeWork`,`thing`),
      KEY `fk_book_creativeWork_idx` (`creativeWork`)
    ) ENGINE = InnoDB;

    -- CONTACT POINT
    CREATE TABLE IF NOT EXISTS `contactPoint` (
      `idcontactPoint` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `thing` INT UNSIGNED NOT NULL,
      `contactType` VARCHAR(160) DEFAULT NULL,
      `contactOption` VARCHAR(100) DEFAULT NULL,
      `email` VARCHAR(225) DEFAULT NULL,
      `telephone` VARCHAR(45) DEFAULT NULL,
      PRIMARY KEY (`idcontactPoint`,`thing`),
      KEY (`contactType`)
    ) ENGINE=InnoDB;

    -- CREATIVE WORK
    CREATE TABLE IF NOT EXISTS `creativeWork` (
      `idcreativeWork` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `thing` INT UNSIGNED NOT NULL,
      `about` INT UNSIGNED DEFAULT NULL,
      `acquireLicensePage` VARCHAR(100) DEFAULT NULL,
      `alternativeHeadline` VARCHAR(100) DEFAULT NULL,
      `author` VARCHAR(255) DEFAULT NULL,
      `copyrightHolder` VARCHAR(50) DEFAULT NULL,
      `creativeWorkStatus` VARCHAR(45) NULL,
      `datePublished` DATETIME DEFAULT NULL,
      `editor` INT UNSIGNED DEFAULT NULL,
      `headline` VARCHAR(255) DEFAULT NULL,
      `isPartOf` INT UNSIGNED DEFAULT NULL,
      `keywords` VARCHAR(255) NOT NULL DEFAULT '',
      `license` VARCHAR(100) DEFAULT NULL,
      `locationCreated` VARCHAR(100) DEFAULT NULL,
      `maintainer` INT UNSIGNED DEFAULT NULL,
      `position` INT UNSIGNED DEFAULT NULL,
      `publisher` VARCHAR(255) DEFAULT NULL,
      `text` TEXT,
      `thumbnail` VARCHAR(255) DEFAULT NULL,
      `version` VARCHAR(50) DEFAULT '',
      PRIMARY KEY (`idcreativeWork`,`thing`),
      KEY `fk_creativeWork_thing_idx` (`thing`),
      KEY `fk_creativeWork_creativeWork_idx` (`isPartOf`),
      KEY `creativeWork_keywords_idx` (`keywords`)
    ) ENGINE = InnoDB;

    -- EVENT
    CREATE TABLE IF NOT EXISTS `event` (
     `idevent` INT UNSIGNED NOT NULL AUTO_INCREMENT,
     `thing` INT UNSIGNED NOT NULL,
     `about` INT UNSIGNED DEFAULT NULL,
     `director` VARCHAR(255) DEFAULT NULL,
     `endDate` DATETIME DEFAULT NULL,
     `keywords` VARCHAR(255) DEFAULT NULL,
     `location` INT UNSIGNED DEFAULT NULL,
     `organizer` INT UNSIGNED DEFAULT NULL,
     `startDate` DATETIME DEFAULT NULL,
     `subEvent` INT UNSIGNED DEFAULT NULL,
     `superEvent` INT UNSIGNED DEFAULT NULL,
     PRIMARY KEY (`idevent`,`thing`),
     KEY `fk_event_thing_idx1` (`thing`),
     KEY `fk_event_about_idx1` (`about`),
     KEY `fk_event_location_idx1` (`location`),
     KEY `fk_event_organizer_idx1` (`organizer`),
     KEY `fk_event_subEvent_idx1` (`subEvent`),
     KEY `fk_event_superEvent_idx1` (`superEvent`)
    ) ENGINE = InnoDB;

    -- IMAGE OBJECT
    CREATE TABLE IF NOT EXISTS `imageObject` (
     `idimageObject` INT UNSIGNED NOT NULL AUTO_INCREMENT,
     `mediaObject` INT UNSIGNED NOT NULL,
     PRIMARY KEY (`idimageObject`)
    ) ENGINE=InnoDB;

    -- MEDIA OBJECT
    CREATE TABLE IF NOT EXISTS `mediaObject` (
     `idmediaObject` INT UNSIGNED NOT NULL AUTO_INCREMENT,
     `creativeWork` INT UNSIGNED NOT NULL,
     `thing` int unsigned not null,
     `contentSize` VARCHAR(100) DEFAULT NULL,
     `contentUrl` VARCHAR(255) NOT NULL,
     `encodingFormat` VARCHAR(50) DEFAULT NULL,
     `height` INT DEFAULT NULL,
     `width` INT DEFAULT NULL,
     `uploadDate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
     PRIMARY KEY (`idmediaObject`,`creativeWork`),
     KEY `fk_mediaObject_creativeWork_idx` (`creativeWork`)
    ) ENGINE = InnoDB;

    -- THING
    CREATE TABLE IF NOT EXISTS `thing` (
     `idthing` INT UNSIGNED NOT NULL AUTO_INCREMENT,
     `additionalType` VARCHAR(255) NULL,
     `alternateName` VARCHAR(255) NULL,
     `dateCreated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
     `dateModified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
     `description` VARCHAR(255),
     `disambiguatingDescription` TEXT,
     `name` VARCHAR(255) NOT NULL,
     `mainEntityOfPage` VARCHAR(255) DEFAULT NULL,
     `type` VARCHAR(45) NOT NULL,
     `url` VARCHAR(255) NULL,
     PRIMARY KEY (`idthing`),
     KEY `thing_name` (`name`),
     KEY `thing_description` (`description`),
     KEY `thing_url` (`url`),
     CONSTRAINT `thing_check_name` CHECK (`name` <> '')
    ) ENGINE = InnoDB;

    -- THING HAS IMAGEOBJECT
    CREATE TABLE IF NOT EXISTS `thing_has_imageObject` (
     `idthing` INT UNSIGNED NOT NULL,
     `idimageObject` INT UNSIGNED NOT NULL,
     `position` INT UNSIGNED DEFAULT NULL,
     `representativeOfPage` TINYINT NOT NULL DEFAULT 0,
     `caption` TEXT,
     PRIMARY KEY (`idthing`, `idimageObject`)
    ) ENGINE = InnoDB;

    -- THING_HAS_THING
    CREATE TABLE IF NOT EXISTS `thing_has_thing` (
     `idHasPart` INT UNSIGNED NOT NULL,
     `typeHasPart` VARCHAR(48) NOT NULL,
     `idIsPartOf` INT UNSIGNED NOT NULL,
     `typeIsPartOf` VARCHAR(48) NOT NULL,
     PRIMARY KEY (`idHasPart`,`idIsPartOf`,`typeHasPart`,`typeIsPartOf`),
     KEY `fk_thing_has_thing_hasPart_idx1` (`idHasPart`),
     KEY `fk_thing_has_thing_isPartOf_idx1` (`idIsPartOf`)
    ) ENGINE = InnoDB;

  END;
