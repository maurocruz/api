
--
-- CREATE TABLE book
--

CREATE TABLE IF NOT EXISTS `book` (
  `idbook` int(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `author` varchar(100) NOT NULL DEFAULT '',
  `birthDate` int(4) DEFAULT NULL,
  `deathDate` int(4) DEFAULT NULL,
  `version` varchar(50) DEFAULT '',
  `bookEdition` varchar(24) DEFAULT '',
  `locationCreated` varchar(50) DEFAULT '',
  `publisher` varchar(100) DEFAULT '',
  `datePublished` varchar(8) DEFAULT '',
  `numberOfPages` varchar(24) DEFAULT '',
  `keywords` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`idbook`),
  UNIQUE KEY `titulo` (`name`)
) ENGINE=InnoDB;

