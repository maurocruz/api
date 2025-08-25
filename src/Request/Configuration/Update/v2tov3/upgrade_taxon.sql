-- TAXON
CREATE PROCEDURE upgrade_taxon()
BEGIN
  -- ALTER TABLE
  ALTER TABLE `taxon`
    CHANGE COLUMN `idtaxon` `idtaxon` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idtaxon`,
    DROP PRIMARY KEY ,
    ADD PRIMARY KEY (`idtaxon`);

  -- INSERT THING
  ALTER TABLE `thing` ADD COLUMN `idtaxon` INT UNSIGNED DEFAULT NULL;
  -- insert thing
  INSERT INTO `thing` (`idtaxon`,`name`,`alternateName`,`description`,`disambiguatingDescription`,`url`,`dateCreated`,`dateModified`,`type`)
    SELECT `idtaxon`,
      IF (`name` <> '', `name`, 'Undefined name'),
      `vernacularName`,
      description,
      SUBSTRING(REGEXP_REPLACE(disambiguatingDescription, '<[^>]*>+', ''),1,255) as disambiguatingDescription,
      `url`,
      if(`dateModified` IS NULL, CURDATE(), `dateModified`),
      `dateModified`,
      'Taxon'
    FROM `taxon`;
  -- set thing
  UPDATE `taxon` JOIN `thing` ON thing.idtaxon = taxon.idtaxon SET taxon.thing = thing.idthing;
  -- drop column
  ALTER TABLE `thing` DROP COLUMN `idtaxon`;

  -- insert images
  CALL insert_thing_has_thing('taxon','imageObject');

  -- IMAGES
  CALL set_image_in_thing('taxon');

  -- alter table
  ALTER TABLE `taxon`
    CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
    DROP COLUMN `name`,
    DROP COLUMN `alternateName`,
    DROP COLUMN `description`,
    DROP COLUMN `disambiguatingDescription`,
    DROP COLUMN `url`,
    DROP COLUMN `dateModified`,
    DROP PRIMARY KEY ,
    ADD PRIMARY KEY (`idtaxon`,`thing`)
  ;

  DROP TABLE `taxon_has_imageObject`;

  -- add foreign key
  ALTER TABLE `taxon`
    ADD KEY `fk_taxon_thing_idx` (`thing`),
    ADD CONSTRAINT `fk_taxon_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION;

END;