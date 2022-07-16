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
  `status` TINYINT(4) NULL DEFAULT NULL,
  `create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date` DATE NULL,
  PRIMARY KEY (`iduser`),
  UNIQUE INDEX `email` (`email`)
) ENGINE = InnoDB;

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

CREATE TABLE `user_privileges` (
   `iduser_privileges` int NOT NULL AUTO_INCREMENT,
   `code` varchar(16) DEFAULT NULL,
   `name` varchar(45) DEFAULT NULL,
   `description` text,
   PRIMARY KEY (`iduser_privileges`)
) ENGINE=InnoDB;


CREATE TABLE `user_has_user_privileges` (
    `iduser` int NOT NULL,
    `iduser_privileges` int NOT NULL,
    PRIMARY KEY (`iduser`,`iduser_privileges`),
    KEY `fk_user_has_user_privilegies_user_privilegies1_idx` (`iduser_privileges`),
    KEY `fk_user_has_user_privilegies_user1_idx` (`iduser`),
    CONSTRAINT `fk_user_has_user_privileges_user` FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`),
    CONSTRAINT `fk_user_has_user_privileges_user_privileges` FOREIGN KEY (`iduser_privileges`) REFERENCES `user_privileges` (`iduser_privileges`)
) ENGINE=InnoDB;

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
