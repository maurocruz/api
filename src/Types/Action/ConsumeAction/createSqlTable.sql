/**
 * Author:  Mauro Cruz <maurocruz@pirenopolis.tur.br>
 * Created: 18 de mar de 2020
 */

--
-- CREATE TABLE consumerAction
-- -- require
-- -- offer
-- -- action
--

CREATE TABLE IF NOT EXISTS `consumeAction` (
  `idconsumeAction` INT(10) NOT NULL AUTO_INCREMENT,
  `idaction` INT(10) NULL,
  `additionalType` VARCHAR(45) NULL,
  `expectsAcceptanceOf` INT(10) NULL,
  PRIMARY KEY (`idconsumeAction`),
  INDEX `fk_consumerAction_1_idx` (`expectsAcceptanceOf` ASC),
  INDEX `fk_consumerAction_2_idx` (`idaction` ASC),
  CONSTRAINT `fk_consumerAction_1` FOREIGN KEY (`expectsAcceptanceOf`) REFERENCES `offer` (`idoffer`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_consumerAction_2` FOREIGN KEY (`idaction`) REFERENCES `action` (`idaction`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB;
