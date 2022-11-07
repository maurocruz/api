/**
 * Author:  Mauro Cruz <maurocruz@pirenopolis.tur.br>
 * Created: 25 de fev de 2020
 */

--
-- CREATE TABLE user
--
CREATE TABLE IF NOT EXISTS `user` (
  `iduser` INT NOT NULL AUTO_INCREMENT,
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
    `id` INT NOT NULL AUTO_INCREMENT,
    `iduser` INT NOT NULL,
    `selector` VARCHAR(16) NULL DEFAULT NULL,
    `token` VARCHAR(64) NULL DEFAULT NULL,
    `expires` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `fk_passwordReset_1_idx` (`iduser`),
    CONSTRAINT `fk_passwordReset_1` FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`) ON DELETE CASCADE
) ENGINE = InnoDB;


CREATE TABLE `user_history` (
    `iduser_history` int NOT NULL AUTO_INCREMENT,
    `iduser` int DEFAULT NULL,
    `method` varchar(6) NOT NULL,
    `summary` varchar(255) DEFAULT NULL,
    `targetTable` varchar(45) NOT NULL,
    `targetId` int NOT NULL,
    PRIMARY KEY (`iduser_history`),
    KEY `fk_user_history_user_idx` (`iduser`),
    CONSTRAINT `fk_user_history_user` FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`)
) ENGINE=InnoDB;

CREATE TABLE `user_privileges` (
   `iduser_privileges` int NOT NULL AUTO_INCREMENT,
   `iduser` int NOT NULL,
   `function` int unsigned NOT NULL DEFAULT '1',
   `actions` char(4) NOT NULL DEFAULT 'r',
   `namespace` varchar(45) NOT NULL DEFAULT '',
   `userCreator` int DEFAULT NULL,
   PRIMARY KEY (`iduser_privileges`,`iduser`),
   UNIQUE KEY `unique` (`function`,`iduser`,`namespace`,`actions`),
   KEY `fk_user_privileges_user_idx` (`iduser`),
   CONSTRAINT `fk_user_privileges_user` FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`) ON UPDATE RESTRICT
) ENGINE=InnoDB;

