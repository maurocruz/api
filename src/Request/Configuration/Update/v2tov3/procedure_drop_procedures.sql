-- DROP PROCEDURES
CREATE PROCEDURE drop_procedures()
BEGIN
  DROP PROCEDURE IF EXISTS sql_upgrade;
  DROP PROCEDURE IF EXISTS create_tables;
  DROP PROCEDURE IF EXISTS drop_keys;
  DROP PROCEDURE IF EXISTS upgrade_imageObject;
  DROP PROCEDURE IF EXISTS upgrade_article;
  DROP PROCEDURE IF EXISTS upgrade_book;
  DROP PROCEDURE IF EXISTS upgrade_contactPoint;
  DROP PROCEDURE IF EXISTS upgrade_event;
  DROP PROCEDURE IF EXISTS upgrade_invoice;
  DROP PROCEDURE IF EXISTS upgrade_person;
  DROP PROCEDURE IF EXISTS upgrade_localBusiness;
  DROP PROCEDURE IF EXISTS upgrade_organization;
  DROP PROCEDURE IF EXISTS upgrade_place;
  DROP PROCEDURE IF EXISTS upgrade_product;
END;