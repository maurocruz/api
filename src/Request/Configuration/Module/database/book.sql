--
-- CREATE TABLE book
--
CREATE TABLE IF NOT EXISTS `book` (
  `idbook` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `creativeWork` INT UNSIGNED NOT NULL,
  `thing` INT UNSIGNED NOT NULL,
  `bookFormat` VARCHAR(20) NULL DEFAULT NULL,
  `illustrator` INT UNSIGNED NULL DEFAULT NULL,
  `isbn` VARCHAR(18) NULL DEFAULT NULL,
  `bookEdition` VARCHAR(24) CHARACTER SET 'latin1' NULL DEFAULT '',
  `numberOfPages` VARCHAR(24) CHARACTER SET 'latin1' NULL DEFAULT '',
  PRIMARY KEY (`idbook`, `creativeWork`, `thing`),
  INDEX `fk_book_thing_idx` (`thing`),
  INDEX `fk_book_creativeWork_idx` (`creativeWork`),
  CONSTRAINT `fk_book_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE,
  CONSTRAINT `fk_book_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;
