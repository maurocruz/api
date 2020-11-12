/**
 * Author:  Mauro Cruz <maurocruz@pirenopolis.tur.br>
 * Created: 25 de fev de 2020
 */

--
-- CREATE TABLE imageObject
--

CREATE TABLE IF NOT EXISTS `imageObject` (
  `idimageObject` INT(10) NOT NULL AUTO_INCREMENT ,
  `contentUrl` VARCHAR(255) NOT NULL,
  `contentSize` VARCHAR(45) NULL DEFAULT NULL,
  `uploadData` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `author` INT NULL DEFAULT NULL,
  `license` VARCHAR(180) NULL DEFAULT NULL,
  `keywords` VARCHAR(125) NULL,
  PRIMARY KEY (`idimageObject`)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8;
