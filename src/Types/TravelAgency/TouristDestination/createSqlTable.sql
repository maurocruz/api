/**
 * Author:  Mauro Cruz <maurocruz@pirenopolis.tur.br>
 * Created: 26 de fev de 2020
 */

--
-- CREATE TABLE touristDestination
--
CREATE TABLE IF NOT EXISTS `touristDestination` (
  `idtouristDestination` INT(10) NOT NULL AUTO_INCREMENT,
  `location` INT(10) NOT NULL,
  `touristType` VARCHAR(255) NULL,
  PRIMARY KEY (`idtouristDestination`, `location`),
  INDEX `fk_touristDestination_1_idx` (`location` ASC),
  CONSTRAINT `fk_touristDestination_1` FOREIGN KEY (`location`) REFERENCES `place` (`idplace`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8;
