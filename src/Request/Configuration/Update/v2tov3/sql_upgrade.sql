

CREATE PROCEDURE sql_upgrade(schema_name VARCHAR(64))
BEGIN
  DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
    GET DIAGNOSTICS CONDITION 1 @error_code = MYSQL_ERRNO, @error_message = MESSAGE_TEXT, @sql_state = RETURNED_SQLSTATE ;
    ROLLBACK;
    SELECT CONCAT('ERROR ', @error_code, ' (', @sql_state,') ', @error_message) AS mensagem_erro;
  END;

  START TRANSACTION;

  -- CREATE TABLES
  CALL create_tables();
  -- DROP KEYS
  CALL drop_keys(schema_name);
  -- IMAGE OBJECT
  CALL upgrade_imageObject();
  -- ARTICLE
  CALL upgrade_article();
  -- BOOK
  CALL upgrade_book();
  -- CONTACT POINT
  CALL upgrade_contactPoint();
  -- EVENT
  CALL upgrade_event();
  -- PERSON
  CALL upgrade_person();
  -- ORGANIZATION
  CALL upgrade_organization();
  -- LOCAL BUSINESS
  CALL upgrade_localBusiness();
  -- PLACE
  CALL upgrade_place();
  -- POSTAL ADDRESS
  CALL upgrade_postalAddress();
  -- PRODUCT
  CALL upgrade_product();
  -- SERVIC
  CALL upgrade_service();
  -- ORDER
  CALL upgrade_order();
  -- ACTION
  CALL upgrade_action();
  -- ORDERiTEM
  CALL upgrade_orderItem();
  -- OFFER
  CALL upgrade_offer();
  -- INVOICE
  CALL upgrade_invoice();
  -- TAXON
  CALL upgrade_taxon();
  -- VIDEO OBJECT
  CALL upgrade_videoObject();
  -- WEBSITE
  CALL upgrade_webSite();
  -- WEBPAGE
  CALL upgrade_webPage();
  -- WEBPAGE ELEMENT
  CALL upgrade_webPageElement();
  -- ADD FOREIGN KEYS
  CALL add_foreign_keys();

  ALTER TABLE `thing`
    CHANGE COLUMN `dateCreated` `dateCreated` TIMESTAMP,
    CHANGE COLUMN `dateModified` `dateModified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

  COMMIT ;

  SELECT CONCAT('Transação concluída com sucesso em ', schema_name) AS mensagem_sucesso;

END;