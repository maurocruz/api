--
-- REVIEW
--
CREATE TABLE IF NOT EXISTS `review` (
  `idreview` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `creativeWork` INT UNSIGNED NOT NULL,
  `thing` INT UNSIGNED NOT NULL,
  `itemReviewed` INT UNSIGNED NOT NULL,
  `reviewAspect` VARCHAR(64) NULL DEFAULT NULL,
  `reviewBody` TEXT NULL DEFAULT NULL,
  `reviewRating` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`idreview`, `thing`),
  INDEX `fk_review_thing_idx` (`thing`),
  INDEX `fk_review_itemReviewed_idx` (`itemReviewed`),
  INDEX `fk_review_creativeWork_idx` (`creativeWork`),
  CONSTRAINT `fk_review_itemReviewed` FOREIGN KEY (`itemReviewed`) REFERENCES `thing` (`idthing`)  ON DELETE CASCADE,
  CONSTRAINT `fk_review_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE,
  CONSTRAINT `fk_review_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB;
