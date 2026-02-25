DROP PROCEDURE IF EXISTS set_image_in_thing;
DROP PROCEDURE IF EXISTS drop_keys;
DROP PROCEDURE IF EXISTS insert_thing_has_thing;

CREATE PROCEDURE insert_thing_has_thing(IN table_has_part VARCHAR(64), IN table_is_part_of VARCHAR(64))
BEGIN
  DECLARE v_table_has VARCHAR(128);
  DECLARE v_id_name_has_part VARCHAR(128);
  DECLARE v_id_name_is_part_of VARCHAR(128);
  DECLARE v_upper_has_part VARCHAR(128);
  DECLARE v_upper_is_part_of VARCHAR(128);

  SET v_table_has = CONCAT(table_has_part,'_has_',table_is_part_of);
  SET v_id_name_has_part = CONCAT('id',table_has_part);
  SET v_id_name_is_part_of = CONCAT('id',table_is_part_of);
  SET v_upper_has_part = CONCAT(UPPER(LEFT(table_has_part, 1)),SUBSTRING(table_has_part, 2));
  SET v_upper_is_part_of = CONCAT(UPPER(LEFT(table_is_part_of, 1)),SUBSTRING(table_is_part_of, 2));

  SET @v_sql_text = CONCAT(
          'INSERT INTO `thing_has_thing` (`idHasPart`, `typeHasPart`, `idIsPartOf`, `typeIsPartOf`, `caption`, `position`, `representativeOfPage`)
            SELECT ', table_has_part, '.thing,
      ''', v_upper_has_part, ''',
      ', table_is_part_of, '.thing,
      ''', v_upper_is_part_of, ''',
      ', v_table_has, '.caption,
      ', v_table_has, '.position,
      ', v_table_has, '.representativeOfPage
      FROM ', v_table_has, '
      JOIN `', table_has_part, '` ON ', v_table_has, '.`', v_id_name_has_part, '` = `', table_has_part, '`.`', v_id_name_has_part, '`
      JOIN `', table_is_part_of, '` ON ', v_table_has, '.`', v_id_name_is_part_of, '` = `', table_is_part_of, '`.`', v_id_name_is_part_of, '`'
                    );

  PREPARE stmt FROM @v_sql_text;
  EXECUTE stmt;
  DEALLOCATE PREPARE stmt;
END;

CREATE PROCEDURE set_image_in_thing(IN table_name VARCHAR(64))
BEGIN
  DECLARE v_table_has_part VARCHAR(128);
  DECLARE v_id_name VARCHAR(128);
  -- Monta nomes dinâmicos
  SET v_table_has_part = CONCAT(table_name, '_has_imageObject');
  SET v_id_name = CONCAT('id', table_name);
  -- Monta SQL dinâmico
  SET @v_sql_text = CONCAT(
          'UPDATE `thing`
            JOIN `', table_name, '` AS tb1 ON tb1.thing = thing.idthing
      JOIN `', v_table_has_part, '` AS tb_has ON tb_has.', v_id_name, ' = tb1.', v_id_name, '
      JOIN imageObject ON imageObject.idimageObject = tb_has.idimageObject
      JOIN mediaObject ON mediaObject.idmediaObject = imageObject.mediaObject
     SET `thing`.image = mediaObject.contentUrl'
                   );
  -- Executa o SQL dinâmico
  PREPARE stmt FROM @v_sql_text;
  EXECUTE stmt;
  DEALLOCATE PREPARE stmt;
END;

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
    WHERE table_schema = schema_name AND index_name <> 'PRIMARY' AND table_name <> 'user';

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
    IF EXISTS(
      SELECT 1 FROM information_schema.statistics
      WHERE table_schema = schema_name
        AND table_name = tablename
        AND index_name = indexname
    ) THEN
      SET @drop_idx = CONCAT('ALTER TABLE `', schema_name, '`.`', tablename, '` DROP INDEX `', indexname, '`');
      PREPARE stmt2 FROM @drop_idx;
      EXECUTE stmt2;
      DEALLOCATE PREPARE stmt2;
    END IF;
  END LOOP;
  CLOSE cur2;
END;
