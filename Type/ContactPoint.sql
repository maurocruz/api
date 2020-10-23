/**
 * Author:  Mauro Cruz <maurocruz@pirenopolis.tur.br>
 * Created: 25 de fev de 2020
 */

--
-- CREATE TABLE contactPoint
--
CREATE TABLE IF NOT EXISTS `contactPoint` (
  `idcontactPoint` INT(10) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(180) NULL DEFAULT NULL,
  `contactType` VARCHAR(160) NULL DEFAULT NULL,
  `telephone` VARCHAR(45) NULL DEFAULT NULL,
  `email` VARCHAR(120) NULL DEFAULT NULL,
  `whatsapp` TINYINT(1) NULL DEFAULT NULL,
  `obs` TEXT NULL DEFAULT NULL,
  `position` INT(3) NULL DEFAULT NULL,
  PRIMARY KEY (`idcontactPoint`)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8;

