-- EVENT --
-- name, description, dateCreated, dateModified --
START TRANSACTION ;
  -- insere dados em thing
  INSERT INTO `thing` (`name`,`disambiguatingDescription`,`dateModified`,`dateCreated`,`type`)
  SELECT `name`,`description`,`dateModified`,`dateCreated`, 'Event' FROM `event` WHERE `name` <> '';
  -- atualiza tabela
  UPDATE `event`
  JOIN `thing` ON `event`.name=thing.name AND event.dateModified=thing.dateModified AND event.dateCreated=thing.dateCreated
  SET event.thing=thing.idthing;
COMMIT;

-- ORGANIZATION --
-- name, description, disambiguatingDescription, url, dateCreated, dateModified --
START TRANSACTION ;
  -- insere dados em thing
  INSERT INTO `thing` (`name`,`disambiguatingDescription`,`url`,`dateCreated`,`dateModified`,`type`)
  SELECT `name`,CONCAT(`description`,`disambiguatingDescription`),`url`,IF(`dateCreated` IS NULL, CURDATE(), `dateCreated`),`dateModified`, 'Organization' FROM `organization` WHERE `name` <> '';
  -- atualiza tabela
  UPDATE `organization`
  JOIN `thing` ON `organization`.name=thing.name AND organization.dateModified=thing.dateModified AND organization.dateCreated=thing.dateCreated
  SET organization.thing=thing.idthing;
COMMIT;

-- PERSON --
-- name, url, dateModified --
START TRANSACTION ;
  -- insere dados em thing
	INSERT INTO `thing` (`name`,`url`,`dateModified`,`dateCreated`,`type`)
	SELECT `name`,`url`,`dateModified`,`dateRegistration`,'Person' FROM `person` WHERE `name` <> '';
  -- atualiza tabela
  UPDATE `person`
  JOIN `thing` ON `person`.name=thing.name AND person.url=thing.url AND person.dateModified=thing.dateModified AND person.dateRegistration=thing.dateCreated
  SET person.thing=thing.idthing;
COMMIT;

-- PLACE --
-- name, description, disambiguatingDescription, url, dateCreated, dateModified --
START TRANSACTION ;
  -- insere dados em thing
  INSERT INTO `thing` (`name`,`disambiguatingDescription`,`url`,`dateCreated`,`dateModified`,`type`)
  SELECT `name`,CONCAT(`description`,`disambiguatingDescription`),`url`,`dateCreated`,`dateModified`,'Place' FROM `place` WHERE `name` <> '';
  -- atualiza tabela
  UPDATE `place`
  JOIN `thing` ON `place`.name=thing.name AND place.dateModified=thing.dateModified AND place.dateCreated=thing.dateCreated
  SET place.thing=thing.idthing;
COMMIT;

-- PRODUCT --
-- name, description, disambiguatingDescription, dateCreated, dateModified --
START TRANSACTION ;
  -- insere dados em thing
  INSERT INTO `thing` (`name`,`description`,`disambiguatingDescription`,`dateModified`,`dateCreated`,`type`)
  SELECT `name`,`description`,`disambiguatingDescription`,`dateModified`,`dateCreated`,'Product' FROM `product` WHERE `name` <> '';
  -- atualiza tabela
  UPDATE `product`
    JOIN `thing` ON `product`.name=thing.name AND product.dateModified=thing.dateModified AND product.dateCreated=thing.dateCreated
  SET product.thing=thing.idthing;
COMMIT;

-- TAXON --
-- name, description, disambiguatingDescription, url, dateModified --
START TRANSACTION ;
  -- insere dados em thing
  INSERT INTO `thing` (`name`,`disambiguatingDescription`,`description`,`dateModified`,`url`,`type`)
  SELECT `name`,`description`,`disambiguatingDescription`,`dateModified`,`url`,'Taxon' FROM `taxon` WHERE `name` <> '';
  -- atualiza tabela
  UPDATE `taxon`
  JOIN `thing` ON `taxon`.name=thing.name AND taxon.dateModified=thing.dateModified AND taxon.url=thing.url
  SET taxon.thing=thing.idthing;
COMMIT;
