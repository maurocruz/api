DROP PROCEDURE IF EXISTS sql_update;
DROP PROCEDURE IF EXISTS create_tables;
DROP PROCEDURE IF EXISTS drop_keys;
DROP PROCEDURE IF EXISTS upgrade_imageObject;
DROP PROCEDURE IF EXISTS upgrade_article;
DROP PROCEDURE IF EXISTS upgrade_book;
DROP PROCEDURE IF EXISTS upgrade_contactPoint;
DROP PROCEDURE IF EXISTS upgrade_event;

CREATE PROCEDURE sql_update(schema_name VARCHAR(64))
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

  COMMIT ;

  SELECT CONCAT('Transação concluída com sucesso em ', schema_name) AS mensagem_sucesso;

END;