-- history
ALTER TABLE `history`
  CHANGE COLUMN `user` `user` INT UNSIGNED NOT NULL ;
-- map viewport
ALTER TABLE `map_viewport`
  CHANGE COLUMN `iduser` `iduser` INT UNSIGNED NOT NULL ;
-- user history
ALTER TABLE `user_history`
  CHANGE COLUMN `iduser` `iduser` INT UNSIGNED NOT NULL ;
-- user password
ALTER TABLE `passwordReset`
  CHANGE COLUMN `iduser` `iduser` INT UNSIGNED NOT NULL , RENAME TO  `user_passwordReset` ;
-- user privileges
ALTER TABLE `user_privileges`
  CHANGE COLUMN `iduser_privileges` `iduser_privileges` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  CHANGE COLUMN `iduser` `iduser` INT UNSIGNED NOT NULL,
  CHANGE `actions` `action` CHAR(4) DEFAULT 'r' NOT NULL ;

-- user id change
ALTER TABLE `user`
  CHANGE COLUMN `iduser` `iduser` INT UNSIGNED NOT NULL AUTO_INCREMENT ;

-- foreign keys
ALTER TABLE `map_viewport`
  ADD CONSTRAINT `fk_map_viewport_user`
  FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`) ON DELETE CASCADE;
ALTER TABLE `user_passwordReset`
  ADD CONSTRAINT `fk_user_passwordReset_user`
    FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`) ON DELETE CASCADE;
ALTER TABLE `user_privileges`
  ADD CONSTRAINT `fk_user_privileges_user`
    FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `user_history`
  ADD CONSTRAINT `fk_user_history_user`
    FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`) ON DELETE RESTRICT ON UPDATE RESTRICT;

-- PASSWORD RESET
CREATE TABLE IF NOT EXISTS `user_passwordReset` (
  `iduser` INT UNSIGNED NOT NULL,
  `selector` VARCHAR(16) NULL DEFAULT NULL,
  `token` VARCHAR(64) NULL DEFAULT NULL,
  `expires` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`iduser`),
  INDEX `fk_user_passwordReset_user_idx` (`iduser`),
  CONSTRAINT `fk_user_passwordReset_user` FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB;