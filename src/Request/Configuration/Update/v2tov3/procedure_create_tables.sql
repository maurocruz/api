CREATE PROCEDURE create_tables()
  BEGIN
    -- AGGREGATE RATING

    -- ARTICLE
    CREATE TABLE IF NOT EXISTS `article` (
     `idarticle` INT UNSIGNED NOT NULL AUTO_INCREMENT,
     `creativeWork` INT UNSIGNED NOT NULL,
     `thing` INT UNSIGNED NOT NULL,
     `articleBody` TEXT,
     `articleSection` VARCHAR(255) DEFAULT NULL,
     `backstory` TEXT,
     PRIMARY KEY (`idarticle`,`creativeWork`,`thing`)
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
      PRIMARY KEY (`idbook`,`creativeWork`,`thing`)
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
    ) ENGINE = InnoDB;

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
     PRIMARY KEY (`idevent`,`thing`)
    ) ENGINE = InnoDB;

    -- GEO COORDINATES
    CREATE TABLE IF NOT EXISTS `geoCoordinates` (
      `idgeoCoordinates` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `address` INT UNSIGNED DEFAULT NULL,
      `elevation` INT DEFAULT NULL,
      `latitude` DECIMAL(18,14) DEFAULT NULL,
      `longitude` DECIMAL(18,14) DEFAULT NULL,
      PRIMARY KEY (`idgeoCoordinates`)
    ) ENGINE = InnoDB;

    -- IMAGE OBJECT
    CREATE TABLE IF NOT EXISTS `imageObject` (
     `idimageObject` INT UNSIGNED NOT NULL AUTO_INCREMENT,
     `mediaObject` INT UNSIGNED NOT NULL,
     `creativeWork` INT UNSIGNED NOT NULL,
     `thing` INT UNSIGNED NOT NULL,
     PRIMARY KEY (`idimageObject`,`mediaObject`,`creativeWork`,`thing`)
    ) ENGINE = InnoDB;

    -- INVOICE
    CREATE TABLE IF NOT EXISTS `invoice` (
     `idinvoice` INT UNSIGNED NOT NULL AUTO_INCREMENT,
     `referencesOrder` INT UNSIGNED NOT NULL,
     `totalPaymentDue` FLOAT NOT NULL,
     `paymentDueDate` DATE NOT NULL,
     `paymentDate` DATE DEFAULT NULL,
     `paymentStatus` VARCHAR(45) DEFAULT NULL,
     PRIMARY KEY (`idinvoice`,`referencesOrder`),
     KEY `invoice_paymentDueDate_idx` (`paymentDueDate`),
     KEY `invoice_referencesOrder_idx` (`referencesOrder`)
    ) ENGINE = InnoDB;

    -- LOCAL BUSINESS
    CREATE TABLE IF NOT EXISTS `localBusiness` (
      `idlocalBusiness` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `thing` INT UNSIGNED NOT NULL,
      `organization` INT UNSIGNED DEFAULT NULL ,
      `location` INT UNSIGNED NOT NULL,
      PRIMARY KEY (`idlocalBusiness`,`thing`),
      KEY `localBusiness_organization_idx` (`organization`),
      KEY `localBusiness_place_idx` (`location`)
    ) ENGINE = InnoDB;

    -- MEDIA OBJECT
    CREATE TABLE IF NOT EXISTS `mediaObject` (
      `idmediaObject` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `creativeWork` INT UNSIGNED NOT NULL,
      `thing` INT UNSIGNED NOT NULL ,
      `bitrate` DECIMAL(6,2) DEFAULT NULL,
      `duration` TIME DEFAULT NULL,
      `contentSize` VARCHAR(100) DEFAULT NULL,
      `contentUrl` VARCHAR(255) NOT NULL,
      `encodingFormat` VARCHAR(50) DEFAULT NULL,
      `height` INT DEFAULT NULL,
      `width` INT DEFAULT NULL,
      `uploadDate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`idmediaObject`,`creativeWork`,`thing`)
    ) ENGINE = InnoDB;

    -- OFFER
    CREATE TABLE IF NOT EXISTS `offer` (
      `idoffer` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `itemOffered` INT NOT NULL,
      `itemOfferedType` VARCHAR(45) NOT NULL,
      `offeredBy` INT DEFAULT NULL,
      `offeredByType` VARCHAR(45) DEFAULT NULL,
      `price` FLOAT NOT NULL,
      `priceCurrency` VARCHAR(45) NOT NULL DEFAULT 'R$',
      `validThrough` DATETIME DEFAULT NULL,
      `availability` VARCHAR(45) DEFAULT NULL,
      `elegibleQuantity` INT DEFAULT NULL,
      `elegibleDuration` VARCHAR(45) DEFAULT NULL,
      PRIMARY KEY (`idoffer`)
    ) ENGINE = InnoDB;

    -- ORGANIZATION
    CREATE TABLE IF NOT EXISTS `organization` (
      `idorganization` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `thing` INT UNSIGNED NOT NULL,
      `areaServed` INT UNSIGNED DEFAULT NULL,
      `hasOfferCatalog` text,
      `legalName` VARCHAR(124) DEFAULT NULL,
      `location` INT UNSIGNED DEFAULT NULL,
      `logo` INT UNSIGNED DEFAULT NULL,
      `taxId` VARCHAR(24) DEFAULT NULL,
      PRIMARY KEY (`idorganization`,`thing`)
    ) ENGINE = InnoDB;

    -- PERSON
    CREATE TABLE IF NOT EXISTS `person` (
      `idperson` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `thing` INT UNSIGNED NOT NULL,
      `givenName` VARCHAR(120) DEFAULT NULL,
      `familyName` VARCHAR(120) DEFAULT NULL,
      `additionalName` VARCHAR(45) DEFAULT NULL,
      `taxId` VARCHAR(64) DEFAULT NULL,
      `birthDate` DATE DEFAULT NULL,
      `birthPlace` VARCHAR(45) DEFAULT NULL,
      `deathDate` DATE DEFAULT NULL,
      `deathPlace` VARCHAR(45) DEFAULT NULL,
      `gender` VARCHAR(45) DEFAULT NULL,
      `hasOccupation` VARCHAR(255) DEFAULT NULL,
      `homeLocation` INT UNSIGNED DEFAULT NULL,
      PRIMARY KEY (`idperson`,`thing`),
      KEY (`givenName`,`familyName`)
    ) ENGINE = InnoDB;

    -- PLACE
    CREATE TABLE IF NOT EXISTS `place` (
      `idplace` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `thing` INT UNSIGNED NOT NULL,
      `geo` INT UNSIGNED DEFAULT NULL,
      `publicAccess` BOOLEAN DEFAULT FALSE,
      PRIMARY KEY (`idplace`,`thing`,`geo`)
    ) ENGINE = InnoDB;

    --  POSTAL ADDRESS
    CREATE TABLE IF NOT EXISTS `postalAddress` (
      `idpostalAddress` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `streetAddress` VARCHAR(255) DEFAULT NULL,
      `addressLocality` VARCHAR(80) DEFAULT NULL,
      `addressRegion` VARCHAR(45) DEFAULT NULL,
      `addressCountry` VARCHAR(45) DEFAULT NULL,
      `postalCode` VARCHAR(45) DEFAULT NULL,
      PRIMARY KEY (`idpostalAddress`)
    ) ENGINE = InnoDB;

    -- PRODUCT
    CREATE TABLE IF NOT EXISTS `product` (
      `idproduct` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `thing` INT UNSIGNED NOT NULL,
      `category` VARCHAR(64) NOT NULL DEFAULT '',
      `manufacturer` INT UNSIGNED DEFAULT NULL,
      PRIMARY KEY (`idproduct`,`thing`)
    ) ENGINE = InnoDB;

    -- PROPERTY VALUE
    CREATE TABLE IF NOT EXISTS `propertyValue` (
      `idpropertyValue` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `name` VARCHAR(80) NOT NULL,
      `value` VARCHAR(255) NOT NULL,
      PRIMARY KEY (`idpropertyValue`)
    ) ENGINE = InnoDB;

    -- REVIEW
    CREATE TABLE IF NOT EXISTS `review` (
      `idreview` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `creativeWork` INT UNSIGNED NOT NULL,
      `thing` INT UNSIGNED NOT NULL,
      `itemReviewed` INT UNSIGNED NOT NULL,
      `reviewAspect` VARCHAR(64),
      `reviewBody` TEXT,
      `reviewRating` INT UNSIGNED NOT NULL,
      PRIMARY KEY (`idreview`,`thing`),
      KEY `review_itemReviewed_idx` (`itemReviewed`)
    ) ENGINE = InnoDB;

    -- SERVICE
    CREATE TABLE IF NOT EXISTS `service` (
      `idservice` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `provider` INT UNSIGNED NOT NULL,
      `providerType` VARCHAR(45) NOT NULL,
      `category` VARCHAR(100) DEFAULT NULL,
      `serviceType` VARCHAR(255) DEFAULT NULL,
      `termsOfService` TEXT,
      PRIMARY KEY (`idservice`),
      KEY `service_provider_idx` (`provider`)
    ) ENGINE = InnoDB;

    -- TAXON
    CREATE TABLE IF NOT EXISTS `taxon` (
      `idtaxon` INT unsigned NOT NULL AUTO_INCREMENT,
      `thing` INT UNSIGNED NOT NULL,
      `taxonRank` VARCHAR(255) DEFAULT NULL,
      `vernacularName` VARCHAR(255) DEFAULT NULL,
      `parentTaxon` VARCHAR(255) DEFAULT NULL,
      `scientificNameAuthorship` VARCHAR(255) DEFAULT NULL,
      `occurrence` VARCHAR(255) DEFAULT NULL,
      `flowering` VARCHAR(100) DEFAULT NULL,
      `fructification` VARCHAR(100) DEFAULT NULL,
      `height` TEXT,
      `roots` TEXT,
      `leafs` TEXT,
      `flowers` TEXT,
      `fruits` TEXT,
      `citations` TEXT,
      PRIMARY KEY (`idtaxon`,`thing`)
    ) ENGINE = InnoDB;

    -- THING
    CREATE TABLE IF NOT EXISTS `thing` (
     `idthing` INT UNSIGNED NOT NULL AUTO_INCREMENT,
     `additionalType` VARCHAR(255) DEFAULT NULL,
     `alternateName` VARCHAR(255) NULL,
     `dateCreated` TIMESTAMP DEFAULT NULL,
     `dateModified` TIMESTAMP DEFAULT NULL,
     `description` TEXT,
     `disambiguatingDescription` VARCHAR(255),
     `image` VARCHAR(255) DEFAULT NULL,
     `mainEntityOfPage` VARCHAR(255) DEFAULT NULL,
     `name` VARCHAR(255) NOT NULL,
     `type` VARCHAR(45) NOT NULL,
     `url` VARCHAR(255) DEFAULT NULL,
     PRIMARY KEY (`idthing`),
     KEY `thing_name_idx` (`name`),
     KEY `thing_disambiguatingDescription_idx` (`disambiguatingDescription`),
     KEY `thing_url_idx` (`url`),
     CONSTRAINT `thing_check_name` CHECK (`name` <> '')
    ) ENGINE = InnoDB;

    -- THING HAS IMAGEOBJECT
    CREATE TABLE IF NOT EXISTS `thing_has_imageObject` (
      `idthing` INT UNSIGNED NOT NULL,
      `idimageObject` INT UNSIGNED NOT NULL,
      `caption` TEXT,
      `href` VARCHAR(255),
      `position` INT NOT NULL DEFAULT 0,
      `representativeOfPage` TINYINT NOT NULL DEFAULT 0,
      PRIMARY KEY (`idthing`, `idimageObject`)
    ) ENGINE = InnoDB;

    -- THING_HAS_THING
    CREATE TABLE IF NOT EXISTS `thing_has_thing` (
      `idHasPart` INT UNSIGNED NOT NULL,
      `typeHasPart` VARCHAR(48) NOT NULL,
      `idIsPartOf` INT UNSIGNED NOT NULL,
      `typeIsPartOf` VARCHAR(48) NOT NULL,
      `caption` VARCHAR(255) DEFAULT NULL,
      `position` INT UNSIGNED DEFAULT 1,
      PRIMARY KEY (`idHasPart`,`idIsPartOf`,`typeHasPart`,`typeIsPartOf`)
    ) ENGINE = InnoDB;

    -- VIDEO OBJECT
    CREATE TABLE IF NOT EXISTS `videoObject` (
      `idvideoObject` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `mediaObject` INT UNSIGNED NOT NULL,
      `creativeWork` INT UNSIGNED NOT NULL,
      `thing` INT UNSIGNED NOT NULL,
      PRIMARY KEY (`idvideoObject`)
    ) ENGINE = InnoDB;

    -- WEB PAGE
    CREATE TABLE IF NOT EXISTS `webPage` (
      `idwebPage` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `thing` INT UNSIGNED NOT NULL,
      `creativeWork` INT UNSIGNED NOT NULL,
      `breadcrumb` text,
      `primaryImageOfPage` INT UNSIGNED DEFAULT NULL,
      PRIMARY KEY (`idwebPage`,`creativeWork`,`thing`)
    ) ENGINE = InnoDB;

    -- WEB PAGE ELEMENT
    CREATE TABLE IF NOT EXISTS `webPageElement` (
      `idwebPageElement` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `creativeWork` INT UNSIGNED NOT NULL,
      `thing` INT UNSIGNED NOT NULL,
      PRIMARY KEY (`idwebPageElement`,`creativeWork`)
    ) ENGINE = InnoDB;

    -- WEB SITE
    CREATE TABLE IF NOT EXISTS `webSite` (
      `idwebSite` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `creativeWork` INT UNSIGNED NOT NULL,
      `thing` INT UNSIGNED NOT NULL,
      PRIMARY KEY (`idwebSite`,`creativeWork`)
    ) ENGINE = InnoDB;

  END;
