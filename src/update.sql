
ALTER TABLE `pirenopolis02`.`videos` 
CHANGE COLUMN `id` `idvideos` INT(10) UNSIGNED NOT NULL, 
CHANGE COLUMN `title` `name` VARCHAR(255) NOT NULL DEFAULT '',
CHANGE COLUMN `thumb` `thumbnail` VARCHAR(255) NOT NULL DEFAULT '',
CHANGE COLUMN `size` `bitrate` DECIMAL(6,2) NULL DEFAULT NULL ,
CHANGE COLUMN `length` `duration` TIME NULL DEFAULT NULL ;

ALTER TABLE `pirenopolis02`.`videos` 
ADD COLUMN `contentUrl` VARCHAR(255) NULL AFTER `description`,
CHANGE COLUMN `data` `uploadDate` DATETIME NULL DEFAULT NULL , RENAME TO  `pirenopolis02`.`videoObject` ;

ALTER TABLE `pirenopolis02`.`videoObject` 
CHANGE COLUMN `idvideos` `idvideoObject` INT(10) UNSIGNED NOT NULL ;

UPDATE `pirenopolis02`.`videoObject` SET `url` = CONCAT('/multimidia/video/',url);

UPDATE `pirenopolis02`.`videoObject` SET `thumbnail` = CONCAT('/portal/public/images/videos/',thumbnail);

UPDATE `pirenopolis02`.`videoObject` SET `contentUrl` = CONCAT('/portal/public/images/videos/',substring_index(url,'/',-1),'_VP8.webm');

ALTER TABLE `pirenopolis02`.`videoObject` 
CHANGE COLUMN `idvideoObject` `idvideoObject` INT(10) NOT NULL AUTO_INCREMENT ;



ALTER TABLE `pirenopolis02`.`herbario` 
RENAME TO  `pirenopolis02`.`taxon` ;

ALTER TABLE `pirenopolis02`.`taxon` 
CHANGE COLUMN `idherbario` `idtaxon` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ;

ALTER TABLE `pirenopolis02`.`herbario_has_imageObject` 
DROP FOREIGN KEY `FK_herbario_has_images_herbario`;

ALTER TABLE `pirenopolis02`.`herbario_has_imageObject` 
CHANGE COLUMN `idherbario` `idtaxon` INT(10) UNSIGNED NOT NULL, 
RENAME TO  `pirenopolis02`.`taxon_has_imageObject` ;

ALTER TABLE `pirenopolis02`.`taxon_has_imageObject` 
ADD CONSTRAINT `FK_taxon_has_imageObject`
  FOREIGN KEY (`idtaxon`)
  REFERENCES `pirenopolis02`.`taxon` (`idtaxon`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

ALTER TABLE `pirenopolis02`.`taxon` 
CHANGE COLUMN `nome` `name` VARCHAR(255) NULL DEFAULT '' ;

ALTER TABLE `pirenopolis02`.`taxon` 
CHANGE COLUMN `genero` `genus` VARCHAR(50) NOT NULL DEFAULT '' ,
CHANGE COLUMN `especie` `specie` VARCHAR(50) NOT NULL DEFAULT '' ;
