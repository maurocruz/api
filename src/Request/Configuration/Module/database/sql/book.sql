--
-- CREATE TABLE book
--
CREATE TABLE IF NOT EXISTS `book` (
  `idbook` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `creativeWork` INT UNSIGNED NOT NULL,
  `bookEdition` VARCHAR(24) DEFAULT '',
  `bookFormat` VARCHAR(20) NULL,
  `illustrator` INT UNSIGNED NULL,
  `isbn` VARCHAR(18) NULL,
  `numberOfPages` VARCHAR(24) DEFAULT '',
  PRIMARY KEY (`idbook`,`creativeWork`),
  KEY `fk_book_creativeWork_idx` (`creativeWork`),
  CONSTRAINT `fk_book_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE
) ENGINE = InnoDB;


