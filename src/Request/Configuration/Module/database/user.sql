--
-- CREATE TABLE user
--
CREATE TABLE IF NOT EXISTS `user` (
  `iduser` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `status` TINYINT NULL DEFAULT NULL,
  `dateCreated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dateModified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`iduser`),
  UNIQUE INDEX `email` (`email`)
) ENGINE = InnoDB;

-- PASSWORD RESET
CREATE TABLE IF NOT EXISTS `passwordReset` (
  `iduser` INT UNSIGNED NOT NULL,
  `selector` VARCHAR(16) NULL DEFAULT NULL,
  `token` VARCHAR(64) NULL DEFAULT NULL,
  `expires` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`iduser`),
  INDEX `fk_passwordReset_user_idx` (`iduser`),
  CONSTRAINT `fk_passwordReset_user` FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- HISTORY
CREATE TABLE IF NOT EXISTS `user_history` (
  `iduser_history` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `iduser` INT UNSIGNED NULL DEFAULT NULL,
  `method` VARCHAR(6) NOT NULL,
  `summary` VARCHAR(255) NULL DEFAULT NULL,
  `targetTable` VARCHAR(45) NOT NULL,
  `targetId` INT NOT NULL,
  PRIMARY KEY (`iduser_history`),
  INDEX `fk_user_history_user_idx` (`iduser`),
  CONSTRAINT `fk_user_history_user` FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- PRIVILEGES
CREATE TABLE IF NOT EXISTS `user_privileges` (
  `iduser_privileges` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `iduser` INT UNSIGNED NOT NULL,
  `function` INT UNSIGNED NOT NULL DEFAULT '1',
  `action` CHAR(4) NOT NULL DEFAULT 'r',
  `namespace` VARCHAR(45) NOT NULL DEFAULT '',
  `userCreator` INT NULL DEFAULT NULL,
  PRIMARY KEY (`iduser_privileges`,`iduser`),
  UNIQUE INDEX `unique` (`function`,`iduser`,`namespace`,`action`),
  INDEX `fk_user_privileges_user_idx` (`iduser`),
  CONSTRAINT `fk_user_privileges_user` FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB;
