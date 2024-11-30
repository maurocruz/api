CREATE PROCEDURE upgrade_postalAddress()
BEGIN
  ALTER TABLE `postalAddress`
    CHANGE COLUMN `idpostalAddress` `idpostalAddress` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (`idpostalAddress`);
END;