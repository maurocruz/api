
--
-- CREATE TABLE thing
--

CREATE TABLE IF NOT EXISTS `thing` (
  `idthing` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `alternateName` VARCHAR(255) NULL,
  `type` VARCHAR(45) NOT NULL,
  `additionalType` VARCHAR(255) NULL,
  `description` TEXT NULL,
  `disambiguatingDescription` TEXT NULL,
  `url` VARCHAR(255) NULL,
  `dateCreated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dateModified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idthing`)
) ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS `imageObject` (
   `idimageObject` int NOT NULL AUTO_INCREMENT,
   `contentUrl` varchar(255) NOT NULL,
   `contentSize` varchar(45) DEFAULT NULL,
   `width` int DEFAULT NULL,
   `height` int DEFAULT NULL,
   `encodingFormat` varchar(45) DEFAULT NULL,
   `author` int DEFAULT NULL,
   `license` varchar(180) DEFAULT NULL,
   `acquireLicensePage` varchar(180) DEFAULT NULL,
   `uploadDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
   `thumbnail` varchar(255) DEFAULT NULL,
   `keywords` varchar(125) DEFAULT NULL,
   `copyright` varchar(180) DEFAULT NULL,
   PRIMARY KEY (`idimageObject`)
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS `thing_has_imageObject` (
    `idthing` INT NOT NULL,
    `idimageObject` INT NOT NULL,
    `position` INT DEFAULT NULL ,
    `representativeOfPage` TINYINT DEFAULT NULL ,
    `caption` TEXT,
    PRIMARY KEY (`idthing`, `idimageObject`),
    KEY `idx_1` (`representativeOfPage`),
    CONSTRAINT `FK_thing_has_imageObject_imageObject` FOREIGN KEY (`idimageObject`) REFERENCES `imageObject` (`idimageObject`) ON DELETE CASCADE,
    CONSTRAINT `FK_thing_has_imageObject_thing` FOREIGN KEY (`idthing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;
