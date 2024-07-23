
CREATE PROCEDURE drop_keys(schema_name VARCHAR(64))

BEGIN
  DECLARE done INT DEFAULT FALSE;
  DECLARE tablename VARCHAR(255);
  DECLARE constraintname VARCHAR(255);
  DECLARE indexname VARCHAR(255);

  -- Cursor para apagar as chaves estrangeiras
  DECLARE cur1 CURSOR FOR
    SELECT table_name, constraint_name
    FROM information_schema.table_constraints
    WHERE constraint_schema = schema_name AND constraint_type = 'FOREIGN KEY';

  -- Cursor para apagar os índices (excluindo as chaves primárias)
  DECLARE cur2 CURSOR FOR
    SELECT table_name, index_name
    FROM information_schema.statistics
    WHERE table_schema = schema_name AND index_name != 'PRIMARY' AND table_name != 'user' AND table_name != 'user_history' AND table_name != 'user_privileges';

  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

  -- Apagar todas as chaves estrangeiras
  OPEN cur1;
  read_loop1: LOOP
    FETCH cur1 INTO tablename, constraintname;
    IF done THEN
      LEAVE read_loop1;
    END IF;
    SET @drop_fk = CONCAT('ALTER TABLE `', schema_name, '`.`', tablename, '` DROP FOREIGN KEY `', constraintname, '`');
    PREPARE stmt FROM @drop_fk;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
  END LOOP;
  CLOSE cur1;

  SET done = 0; -- Reset done flag for the next cursor

  -- Apagar todos os índices
  OPEN cur2;
  read_loop2: LOOP
    FETCH cur2 INTO tablename, indexname;
    IF done THEN
      LEAVE read_loop2;
    END IF;
    SET @drop_idx = CONCAT('ALTER TABLE `', schema_name, '`.`', tablename, '` DROP INDEX `', indexname, '`');
    PREPARE stmt2 FROM @drop_idx;
    EXECUTE stmt2;
    DEALLOCATE PREPARE stmt2;
  END LOOP;
  CLOSE cur2;
END;


