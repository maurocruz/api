--
-- CREATIVE WORK
--

CREATE TABLE IF NOT EXISTS `creativeWork` (
  `idcreativeWork` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thing` INT UNSIGNED NOT NULL,
  `acquireLicensePage` VARCHAR(100) DEFAULT NULL,
  `alternativeHeadline` VARCHAR(50) DEFAULT NULL,
  `author` VARCHAR(50) DEFAULT NULL,
  `copyrightHolder` VARCHAR(50) DEFAULT NULL,
  `datePublished` DATETIME DEFAULT NULL,
  `editor` INT UNSIGNED DEFAULT NULL,
  `headline` VARCHAR(100) DEFAULT NULL,
  `isPartOf` INT UNSIGNED DEFAULT NULL,
  `keywords` VARCHAR(255) NOT NULL DEFAULT '',
  `license` VARCHAR(100) DEFAULT NULL,
  `locationCreated` VARCHAR(100) DEFAULT NULL,
  `maintainer` INT UNSIGNED DEFAULT NULL,
  `position` INT UNSIGNED DEFAULT NULL,
  `publisher` INT UNSIGNED DEFAULT NULL,
  `text` TEXT,
  `thumbnail` VARCHAR(100) DEFAULT NULL,
  `version` VARCHAR(50) DEFAULT '',
  PRIMARY KEY (`idcreativeWork`,`thing`),
  KEY `fk_creativeWork_thing_idx` (`thing`),
  KEY `fk_creativeWork_creativeWork_idx` (`isPartOf`),
  KEY `creativeWork_keywords_idx` (`keywords`),
  CONSTRAINT `fk_creativeWork_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE,
  CONSTRAINT `fk_creativeWork_creativeWork` FOREIGN KEY (`isPartOf`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE
) ENGINE = InnoDB;
