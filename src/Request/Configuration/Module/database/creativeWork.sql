--
-- CREATIVE WORK
--
CREATE TABLE IF NOT EXISTS `creativeWork` (
  `idcreativeWork` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thing` INT UNSIGNED NOT NULL,
  `about` INT UNSIGNED NULL DEFAULT NULL,
  `acquireLicensePage` VARCHAR(100) NULL DEFAULT NULL,
  `alternativeHeadline` VARCHAR(100) NULL DEFAULT NULL,
  `author` VARCHAR(100) NULL DEFAULT NULL,
  `copyrightHolder` VARCHAR(100) NULL DEFAULT NULL,
  `creativeWorkStatus` VARCHAR(45) NULL DEFAULT NULL,
  `datePublished` DATETIME NULL DEFAULT NULL,
  `editor` VARCHAR(100) NULL DEFAULT NULL,
  `encodingFormat` VARCHAR(100) NULL DEFAULT NULL,
  `headline` VARCHAR(255) NULL DEFAULT NULL,
  `keywords` VARCHAR(255) NOT NULL DEFAULT '',
  `license` VARCHAR(100) NULL DEFAULT NULL,
  `locationCreated` VARCHAR(100) NULL DEFAULT NULL,
  `maintainer` VARCHAR(100) NULL DEFAULT NULL,
  `position` VARCHAR(100) NULL DEFAULT NULL,
  `publisher` VARCHAR(100) NULL DEFAULT NULL,
  `size` VARCHAR(45) NULL DEFAULT NULL,
  `text` TEXT NULL DEFAULT NULL,
  `thumbnail` VARCHAR(255) NULL DEFAULT NULL,
  `version` VARCHAR(50) NULL DEFAULT '',
  PRIMARY KEY (`idcreativeWork`, `thing`),
  KEY `fk_creativeWork_thing_idx` (`thing`),
  KEY `creativeWork_keywords_idx` (`keywords`),
  INDEX `fk_creativeWork_about_idx` (`about`),
  CONSTRAINT `fk_creativeWork_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE,
  CONSTRAINT `fk_creativeWork_about` FOREIGN KEY (`about`) REFERENCES `thing` (`idthing`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE = InnoDB;

