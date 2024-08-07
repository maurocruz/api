-- PRODUCT
CREATE PROCEDURE upgrade_product()
BEGIN
  -- ALTER TABLE
  ALTER TABLE `product`
    CHANGE COLUMN `idproduct` `idproduct` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idproduct`,
    DROP PRIMARY KEY ,
    ADD PRIMARY KEY (`idproduct`);

  -- INSERT THING
  ALTER TABLE `thing` ADD COLUMN `idproduct` INT UNSIGNED DEFAULT NULL;
  -- insert thing
  INSERT INTO `thing` (`idproduct`,`name`,`additionalType`,`description`,`disambiguatingDescription`,`dateCreated`,`dateModified`,`type`)
    SELECT `idproduct`,
      IF (`name` <> '', `name`, 'Undefined name'),
      `additionalType`,
      description,
      SUBSTRING(REGEXP_REPLACE(disambiguatingDescription, '<[^>]*>+', ''),1,255) as disambiguatingDescription,
      if(`dateCreated` IS NULL, CURDATE(), `dateCreated`),
      `dateModified`,
      'Product'
    FROM `product`;
  -- set thing
  UPDATE `product` JOIN `thing` ON thing.idproduct = product.idproduct SET product.thing = thing.idthing;
  -- drop column
  ALTER TABLE `thing` DROP COLUMN `idproduct`;

  -- insert images
  INSERT INTO `thing_has_imageObject` (`idthing`,`idimageObject`,`position`,`representativeOfPage`,`caption`)
  SELECT `thing`,`idimageObject`,`product_has_imageObject`.`position`,`representativeOfPage`,`caption` FROM `product_has_imageObject`
    JOIN `product` ON `product_has_imageObject`.idproduct = product.idproduct;

  ALTER TABLE `product`
    CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
    DROP COLUMN `name`,
    DROP COLUMN `additionalType`,
    DROP COLUMN `description`,
    DROP COLUMN `disambiguatingDescription`,
    DROP COLUMN `dateCreated`,
    DROP COLUMN `dateModified`,
    DROP PRIMARY KEY ,
    ADD PRIMARY KEY (`idproduct`,`thing`)
  ;

  DROP TABLE `product_has_imageObject`;
END;