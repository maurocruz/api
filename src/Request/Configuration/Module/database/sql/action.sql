--
-- CREATE TABLE action
--

CREATE TABLE IF NOT EXISTS `action` (
  `idaction` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thing` INT UNSIGNED NOT NULL,
  `actionStatus` VARCHAR(100),
  `agent` INT UNSIGNED NOT NULL,
  `endTime` DATETIME DEFAULT NULL,
  `object` INT UNSIGNED NOT NULL,
  `participant` INT UNSIGNED NOT NULL,
  `startTime` DATETIME DEFAULT NULL,
  PRIMARY KEY (`idaction`,`thing`,`agent`,`object`,`participant`),
  KEY `fk_action_thing_idx` (`thing`),
  KEY `fk_action_agent_idx` (`agent`),
  KEY `fk_action_object_idx` (`object`),
  KEY `fk_action_participant_idx` (`participant`),
  CONSTRAINT `fk_action_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE,
  CONSTRAINT `fk_action_agent` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE,
  CONSTRAINT `fk_action_object` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE,
  CONSTRAINT `fk_action_participant` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;