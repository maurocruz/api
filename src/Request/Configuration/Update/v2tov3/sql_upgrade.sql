

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
  -- INVOICE
  CALL upgrade_invoice();
  -- PERSON
  CALL upgrade_person();
  -- LOCAL BUSINESS
  CALL upgrade_localBusiness();
  -- ORGANIZATION
  CALL upgrade_organization();
  -- PLACE
  CALL upgrade_place();
  -- PRODUCT
  CALL upgrade_product();
  -- SERVIC
  CALL upgrade_service();
  -- TAXON
  CALL upgrade_taxon();

  COMMIT ;

  SELECT CONCAT('Transação concluída com sucesso em ', schema_name) AS mensagem_sucesso;

END;