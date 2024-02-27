--
-- CREATIVE WORK
--

CREATE TABLE IF NOT EXISTS `creativeWork` (
  `idcreativeWork` INT NOT NULL,
  `idthing` INT NOT NULL,
  `keywords` TEXT NULL,
  `datePublished` DATETIME DEFAULT '',
  `version` varchar(50) DEFAULT '',
  `locationCreated` varchar(255) DEFAULT '',
  PRIMARY KEY (`idcreativeWork`, `idthing`)
) ENGINE=InnoDB;