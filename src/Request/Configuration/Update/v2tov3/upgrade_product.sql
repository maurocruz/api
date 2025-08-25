-- PRODUCT
CREATE PROCEDURE upgrade_product()
BEGIN
  -- ALTER TABLE
  ALTER TABLE `product`
    CHANGE COLUMN `idproduct` `idproduct` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    CHANGE COLUMN `manufacturer` `manufacturer` INT UNSIGNED DEFAULT NULL,
    ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idproduct`,
    DROP PRIMARY KEY,
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
  CALL insert_thing_has_thing('product','imageObject');

  -- IMAGES
  CALL set_image_in_thing('product');

  ALTER TABLE `product`
    CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
    DROP COLUMN `name`,
    DROP COLUMN `additionalType`,
    DROP COLUMN `description`,
    DROP COLUMN `disambiguatingDescription`,
    DROP COLUMN `dateCreated`,
    DROP COLUMN `dateModified`,
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (`idproduct`,`thing`);

  DROP TABLE `product_has_imageObject`;
  DROP TABLE `product_has_offer`;

  -- add foreign keys
  ALTER TABLE `product`
    ADD KEY `fk_product_thing_idx` (`thing`),
    ADD KEY `fk_product_manufacturer_idx` (`manufacturer`),
    ADD CONSTRAINT `fk_product_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
    ADD CONSTRAINT `fk_product_manufacturer` FOREIGN KEY (`manufacturer`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION;

END;