
-- TAXON
CREATE TABLE IF NOT EXISTS `taxon` (
  `idtaxon` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thing` INT UNSIGNED NOT NULL,
  `taxonRank` VARCHAR(255) NULL DEFAULT NULL,
  `vernacularName` VARCHAR(255) NULL DEFAULT NULL,
  `parentTaxon` VARCHAR(255) NULL DEFAULT NULL,
  `scientificNameAuthorship` VARCHAR(255) NULL DEFAULT NULL,
  `occurrence` VARCHAR(255) NULL DEFAULT NULL,
  `flowering` VARCHAR(100) NULL DEFAULT NULL,
  `fructification` VARCHAR(100) NULL DEFAULT NULL,
  `height` TEXT NULL DEFAULT NULL,
  `roots` TEXT NULL DEFAULT NULL,
  `leafs` TEXT NULL DEFAULT NULL,
  `flowers` TEXT NULL DEFAULT NULL,
  `fruits` TEXT NULL DEFAULT NULL,
  `citations` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`idtaxon`, `thing`),
  INDEX `fk_taxon_thing_idx` (`thing`),
  INDEX `id_taxonRank_idx` (`taxonRank` ASC),
  CONSTRAINT `fk_taxon_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;
