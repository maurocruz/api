/**
 * Author:  Mauro Cruz <maurocruz@pirenopolis.tur.br>
 * Created: 25 de fev de 2020
 * last modified: 2021/10/04
 */

--
-- CREATE TABLE imageObject
--

CREATE TABLE IF NOT EXISTS `imageObject` (
    `idimageObject` INT NOT NULL AUTO_INCREMENT,
    `contentUrl` VARCHAR(255) NOT NULL,
    `contentSize` VARCHAR(45) NULL DEFAULT NULL,
    `width` INT NULL DEFAULT NULL,
    `height` INT NULL DEFAULT NULL,
    `encodingFormat` VARCHAR(45) NULL DEFAULT NULL,
    `author` INT NULL DEFAULT NULL,
    `license` VARCHAR(180) NULL DEFAULT NULL,
    `acquireLicensePage` VARCHAR(180) NULL DEFAULT NULL,
    `uploadDate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `thumbnail` VARCHAR(255) NULL DEFAULT NULL,
    `keywords` VARCHAR(125) NULL DEFAULT NULL,
    `copyright` VARCHAR(180) NULL DEFAULT NULL,
    `exifData` VARCHAR(255) NULL,
    PRIMARY KEY (`idimageObject`)
) ENGINE = InnoDB;
