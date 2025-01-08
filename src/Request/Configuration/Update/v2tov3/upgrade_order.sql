CREATE PROCEDURE upgrade_order()
BEGIN
  ALTER TABLE `order`
    CHANGE COLUMN `idorder` `idorder` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    CHANGE COLUMN `customer` `customer` INT UNSIGNED NOT NULL,
    CHANGE COLUMN `seller` `seller` INT UNSIGNED NOT NULL,
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (`idorder`);

  UPDATE `order`
    LEFT JOIN `localBusiness` ON `localBusiness`.idlocalBusiness = `order`.customer AND `order`.customerType = 'localBusiness'
    LEFT JOIN organization as org1 ON org1.idorganization = `order`.customer AND `order`.customerType = 'organization'
    LEFT JOIN person ON person.idperson = `order`.customer AND `order`.customerType = 'person'
    LEFT JOIN `organization` ON `organization`.idorganization = `order`.seller
  SET customer= IF(customerType='localbusiness', `localBusiness`.thing, IF(customerType = 'organization', org1.thing, `person`.thing)), seller = `organization`.thing;

  DELETE FROM `order` WHERE `customer`='0' || `seller`='0';

  -- history to action
  INSERT INTO `action` (`actionStatus`,`agent`,`endTime`,`object`,`result`,`startTime`)
  SELECT `action`, `user`, `datetime`, `order_has_history`.idorder, summary, `datetime` FROM `history`
    LEFT JOIN order_has_history ON `history`.idhistory=order_has_history.idhistory WHERE `order_has_history`.idorder IS NOT NULL;

  DROP TABLE `history`;
  DROP TABLE `order_has_history`;

  ALTER TABLE `order`
    DROP COLUMN `customerType`,
    DROP COLUMN `sellerType`,
    DROP COLUMN `tipo`,
    DROP COLUMN `valor`,
    DROP COLUMN `dateCreated`,
    DROP COLUMN `dateModified`,
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (`idorder`,`customer`,`seller`);

END;