/**
 * Author:  Mauro Cruz <maurocruz@pirenopolis.tur.br>
 * Created: 25 de fev de 2020
 */

--
-- CREATE TABLE touristAttraction REQUIRE:
-- -- place
-- -- touristDestination
-- -- localBusiness
--
CREATE TABLE IF NOT EXISTS `touristAttraction` (
  `idtouristAttraction` INT(10) NOT NULL AUTO_INCREMENT,
  `location` INT(10) NOT NULL,
  `touristDestination` INT(10) NULL,
  `localBusiness` INT(10) NULL,
  `touristType` VARCHAR(100) NULL,
  `availableLanguage` VARCHAR(45) NULL,
  PRIMARY KEY (`idtouristAttraction`, `location`),
  INDEX `fk_touristAttraction_place1_idx` (`location` ASC),
  INDEX `fk_touristAttraction_touristDestination1_idx` (`touristDestination` ASC),
  INDEX `fk_touristAttraction_localBusiness1_idx` (`localBusiness` ASC),
  CONSTRAINT `fk_touristAttraction_place1` FOREIGN KEY (`location`) REFERENCES `place` (`idplace`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_touristAttraction_touristDestination1` FOREIGN KEY (`touristDestination`) REFERENCES `touristDestination` (`idtouristDestination`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_touristAttraction_localBusiness1` FOREIGN KEY (`localBusiness`) REFERENCES `localBusiness` (`idlocalBusiness`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8;