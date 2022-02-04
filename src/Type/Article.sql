/**
 * Author:  Mauro Cruz <maurocruz@pirenopolis.tur.br>
 * Created: 29 de mar de 2020
 */

--
-- CREATE TABLE article
-- Relationships
-- -- Person
-- -- ImageObject
-- -- Organization
--

CREATE TABLE IF NOT EXISTS `article` (
  `idarticle` int NOT NULL AUTO_INCREMENT,
  `headline` varchar(255) DEFAULT NULL,
  `articleBody` text,
  `articleSection` varchar(255) DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `datePublished` datetime DEFAULT NULL,
  `publishied` tinyint DEFAULT NULL,
  `author` INT DEFAULT NULL,
  `publisher` INT DEFAULT NULL,
  `publisherType` varchar(45) DEFAULT NULL,
  `position` INT(5) NULL,
  PRIMARY KEY (`idarticle`),
  KEY `idx_1` (`headline`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `article_has_imageObject` (
  `idarticle` int(10) NOT NULL,
  `idimageObject` int(10) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `position` tinyint(4) unsigned DEFAULT NULL,
  `representativeOfPage` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`idarticle`,`idimageObject`),
  KEY `FK_news_has_images_images` (`idimageObject`),
  CONSTRAINT `FK_news_has_images_news` FOREIGN KEY (`idarticle`) REFERENCES `article` (`idarticle`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_article_has_imageObject_1` FOREIGN KEY (`idimageObject`) REFERENCES `imageObject` (`idimageObject`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB;

DROP TRIGGER IF EXISTS `article_has_imageObject_BEFORE_INSERT`;

CREATE TRIGGER `article_has_imageObject_BEFORE_INSERT` BEFORE INSERT ON `article_has_imageObject` FOR EACH ROW
BEGIN
    DECLARE count INT;
    SET count = (SELECT COUNT(*) FROM `article_has_imageObject` WHERE `idarticle`=NEW.`idarticle`);
    IF NEW.`position`='' OR NEW.`position` IS NULL
        THEN SET NEW.`position`= count+1;
    END IF;
END;
