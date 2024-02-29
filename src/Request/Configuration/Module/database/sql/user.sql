--
-- CREATE TABLE user
--

CREATE TABLE IF NOT EXISTS `user` (
  `iduser` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `dateCreated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dateModified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`iduser`),
  UNIQUE INDEX `email` (`email`)
) ENGINE = InnoDB;

-- PASSWORD RESET

CREATE TABLE IF NOT EXISTS `user_passwordReset` (
  `iduser` INT UNSIGNED NOT NULL,
  `selector` VARCHAR(16) NULL DEFAULT NULL,
  `token` VARCHAR(64) NULL DEFAULT NULL,
  `expires` DATETIME NULL DEFAULT NULL
) ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS `user_history` (
  `iduser_history` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `iduser` INT UNSIGNED NOT NULL,
  `method` VARCHAR(6) NOT NULL,
  `summary` VARCHAR(255) DEFAULT NULL,
  `targetTable` VARCHAR(45) NOT NULL,
  `targetId` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`iduser_history`,`iduser`),
  CONSTRAINT `fk_user_history_user` FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `user_privileges` (
  `iduser_privileges` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `iduser` INT UNSIGNED NOT NULL,
  `function` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  `actions` CHAR(4) NOT NULL DEFAULT 'r',
  `namespace` VARCHAR(100) NOT NULL DEFAULT '',
  `userCreator` INT UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`iduser_privileges`,`iduser`),
  UNIQUE KEY `unique` (`function`,`iduser`,`namespace`,`actions`),
  CONSTRAINT `fk_user_privileges_user` FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`) ON DELETE CASCADE
) ENGINE=InnoDB;
