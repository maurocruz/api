--
-- CREATE TABLE book
--
CREATE TABLE IF NOT EXISTS `book` (
  `idbook` int unsigned NOT NULL AUTO_INCREMENT,
  `idcreativeWork` INT NOT NULL,
  `bookEdition` varchar(24) DEFAULT '',
  `bookFormat` VARCHAR(20) NULL,
  `illustrator` INT NULL,
  `isbn` VARCHAR(18) NULL,
  `numberOfPages` varchar(24) DEFAULT '',
  PRIMARY KEY (`idbook`)
) ENGINE=InnoDB;


