
--
-- CREATE TABLE thing
--

CREATE TABLE IF NOT EXISTS `thing` (
  `idthing` INT(10) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  PRIMARY KEY (`idthing`)
) ENGINE = InnoDB;
