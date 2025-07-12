--
-- ACTION
--
CREATE TABLE IF NOT EXISTS `action` (
`idaction` INT UNSIGNED NOT NULL AUTO_INCREMENT,
`thing` INT UNSIGNED NOT NULL,
`actionProcess` VARCHAR(255) DEFAULT NULL,
`actionStatus` VARCHAR(45) NOT NULL,
`agent` INT UNSIGNED DEFAULT NULL,
`endTime` DATETIME NULL DEFAULT NULL,
`object` INT UNSIGNED DEFAULT NULL,
`provider` INT UNSIGNED DEFAULT NULL,
`result` TEXT DEFAULT NULL,
`startTime` DATETIME NOT NULL,
`targetCollection` VARCHAR(45) NULL DEFAULT NULL,
PRIMARY KEY (`idaction`, `thing`),
INDEX `fk_action_thing_idx` (`thing`),
INDEX `fk_action_agent_idx` (`agent`),
INDEX `fk_action_object_idx` (`object`),
INDEX `fk_action_provider_idx` (`provider`),
CONSTRAINT `fk_action_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
CONSTRAINT `fk_action_agent` FOREIGN KEY (`agent`) REFERENCES `thing` (`idthing`) ON DELETE SET NULL ON UPDATE NO ACTION,
CONSTRAINT `fk_action_object` FOREIGN KEY (`object`) REFERENCES `thing` (`idthing`) ON DELETE SET NULL ON UPDATE NO ACTION,
CONSTRAINT `fk_action_provider` FOREIGN KEY (`provider`) REFERENCES `thing` (`idthing`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE = InnoDB;
