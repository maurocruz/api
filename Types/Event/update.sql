
ALTER TABLE `pirenopolis02`.`event` 
ADD COLUMN `organizerId` INT(10) NULL AFTER `location`,
ADD COLUMN `organizerType` VARCHAR(45) NULL AFTER `organizerId`;

