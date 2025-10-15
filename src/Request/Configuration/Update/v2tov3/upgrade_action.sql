-- ACTION
CREATE TABLE IF NOT EXISTS `action` (
  `idaction` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `actionStatus` VARCHAR(255),
  `agent` INT UNSIGNED NOT NULL,
  `endTime` DATETIME NOT NULL,
  `object` INT UNSIGNED NOT NULL,
  `result` TEXT,
  `startTime` DATETIME NOT NULL,
  `targetCollection` INT UNSIGNED NOT NULL,
  `type` VARCHAR(45) NOT NULL DEFAULT 'Action',
  PRIMARY KEY (`idaction`,`object`)
) ENGINE = InnoDB;


-- history to action
INSERT INTO `action` (`actionStatus`,`agent`,`endTime`,`object`,`result`,`startTime`,`targetCollection`)
SELECT `action`, `user`, `datetime`, `order_has_history`.idorder, summary, `datetime`, `order_has_history`.idorder FROM `history`
  LEFT JOIN order_has_history ON `history`.idhistory = order_has_history.idhistory WHERE `order_has_history`.idorder IS NOT NULL;

UPDATE `action` SET `type` = 'ReplaceAction' WHERE `actionStatus` = 'UPDATE' OR `actionStatus` = 'EDIT';
UPDATE `action` SET `type` = 'AddAction' WHERE `actionStatus` = 'INSERT' OR `actionStatus` = 'CREATE' OR `actionStatus` = 'CREATED';
UPDATE `action` SET `type` = 'DeleteAction' WHERE `actionStatus` = 'delete';
UPDATE `action` SET `actionStatus` = 'CompletedActionStatus' WHERE `type` = 'ReplaceAction' OR `type` = 'AddAction' OR `type` = 'DeleteAction';

DROP TABLE `history`;
DROP TABLE `order_has_history`;
