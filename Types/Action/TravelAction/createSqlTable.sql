/**
 * Author:  Mauro Cruz <maurocruz@pirenopolis.tur.br>
 * Created: 14 de mar de 2020
 */

--
-- CREATE TABLE travelAction
--

CREATE TABLE IF NOT EXISTS `travelAction` (
  `idtravelAction` int(10) NOT NULL AUTO_INCREMENT,
  `idaction` int(10),
  `additionalType` varchar(45) DEFAULT NULL,
  `fromLocation` int(10) DEFAULT NULL,
  `toLocation` int(10) DEFAULT NULL,
  `distance` varchar(125) DEFAULT NULL,
  PRIMARY KEY (`idtravelAction`),
  INDEX `fk_travelAction_1_idx` (`fromLocation`),
  INDEX `fk_travelAction_2_idx` (`toLocation`),
  INDEX `fk_travelAction_3_idx` (`idaction`),
  CONSTRAINT `fk_travelAction_1` FOREIGN KEY (`fromLocation`) REFERENCES `place` (`idplace`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_travelAction_2` FOREIGN KEY (`toLocation`) REFERENCES `place` (`idplace`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_travelAction_3` FOREIGN KEY (`idaction`) REFERENCES `action` (`idaction`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB;
