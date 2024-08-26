-- TAXON
CREATE PROCEDURE upgrade_taxon()
BEGIN
  -- ALTER TABLE
  ALTER TABLE `taxon`
    CHANGE COLUMN `idtaxon` `idtaxon` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    CHANGE COLUMN `height` `height` VARCHAR(255) DEFAULT NULL,
    CHANGE COLUMN `roots` `roots` VARCHAR(255) DEFAULT NULL,
    CHANGE COLUMN `leafs` `leafs` VARCHAR(255) DEFAULT NULL,
    CHANGE COLUMN `flowers` `flowers` VARCHAR(255) DEFAULT NULL,
    CHANGE COLUMN `fruits` `fruits` VARCHAR(255) DEFAULT NULL,
    CHANGE COLUMN `citations` `citations` VARCHAR(255) DEFAULT NULL,
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

  -- has images
  INSERT INTO `thing_has_imageObject` (`idthing`,`idimageObject`,`position`,`representativeOfPage`,`caption`)
  SELECT `thing`,`idimageObject`,`taxon_has_imageObject`.`position`,`representativeOfPage`,`caption` FROM `taxon_has_imageObject`
    JOIN `taxon` ON `taxon_has_imageObject`.idtaxon = taxon.idtaxon;

  UPDATE `thing`
    join taxon ON taxon.thing = thing.idthing
    join (SELECT idtaxon, idimageObject FROM taxon_has_imageObject group by idtaxon order by position) as has ON has.idtaxon = taxon.idtaxon
    join imageObject ON imageObject.idimageObject = has.idimageObject
    join mediaObject ON mediaObject.idmediaObject = imageObject.mediaObject
  SET `thing`.image = `mediaObject`.contentUrl;

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
END;