--
-- CREATE TABLE article
--

CREATE TABLE IF NOT EXISTS `article` (
  `idarticle` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `creativeWork` INT UNSIGNED NOT NULL,
  `articleBody` TEXT,
  `articleSection` VARCHAR(255) DEFAULT NULL,
  `backstory` TEXT,
  PRIMARY KEY (`idarticle`,`creativeWork`),
  KEY `fk_article_creativeWork_idx` (`creativeWork`),
  CONSTRAINT `fk_article_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE
) ENGINE = InnoDB;

