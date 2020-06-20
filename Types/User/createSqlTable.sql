/**
 * Author:  Mauro Cruz <maurocruz@pirenopolis.tur.br>
 * Created: 25 de fev de 2020
 */

--
-- CREATE TABLE user
--
CREATE TABLE IF NOT EXISTS `user` (
  `iduser` INT(10) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `status` TINYINT(4) NULL DEFAULT NULL,
  `create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`iduser`),
  UNIQUE INDEX `email` (`email` ASC) 
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8;