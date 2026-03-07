--
-- CREATE ARTICLE MODULE
--

-- ARTICLE
CREATE TABLE IF NOT EXISTS `article` (
  `idarticle` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thing` INT UNSIGNED NOT NULL,
  `creativeWork` INT UNSIGNED NOT NULL,
  `articleBody` TEXT NULL DEFAULT NULL,
  `articleSection` VARCHAR(255) NULL DEFAULT NULL,
  `backstory` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`idarticle`, `creativeWork`, `thing`),
  INDEX `fk_article_thing_idx` (`thing`),
  INDEX `fk_article_creativeWork_idx` (`creativeWork`),
  CONSTRAINT `fk_article_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE,
  CONSTRAINT `fk_article_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;



