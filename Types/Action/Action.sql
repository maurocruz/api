/**
 * Author:  Mauro Cruz <maurocruz@pirenopolis.tur.br>
 * Created: 18 de mar de 2020
 */

--
-- CREATE TABLE action
--

CREATE TABLE IF NOT EXISTS `action` (
  `idaction` INT(10) NOT NULL AUTO_INCREMENT,
  `additionalType` VARCHAR(45) NULL,
  `name` VARCHAR(125) NULL,
  `description` TEXT NULL,
  `agentId` INT(10) NULL,
  `agentType` VARCHAR(45) NULL,
  `startTime` TIME NULL,
  `startDate` DATE NULL,
  `endTime` TIME NULL,
  `endDate` DATE NULL,
  `location` INT(10) NULL,
  PRIMARY KEY (`idaction`),
  KEY `fk_action_1_idx` (`location` ASC),
  CONSTRAINT `fk_action_1` FOREIGN KEY (`location`) REFERENCES `place` (`idplace`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE = InnoDB;
