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