ALTER TABLE `thing_has_thing`
  ADD COLUMN `repUniqueFlag` TINYINT(1) GENERATED ALWAYS AS (CASE WHEN `representativeOfPage` = 1 THEN 1 END) VIRTUAL,
  ADD UNIQUE KEY `uq_group_representative` (`idHasPart`,`typeHasPart`,`typeIsPartOf`,`repUniqueFlag`);

--
-- procedure: sp_thing_has_thing_delete
--
DELIMITER $$
CREATE PROCEDURE `sp_thing_has_thing_delete`(
  IN p_idHasPart INT UNSIGNED,
  IN p_typeHasPart VARCHAR(48),
  IN p_idIsPartOf INT UNSIGNED,
  IN p_typeIsPartOf VARCHAR(48)
)
BEGIN
  DECLARE v_old_pos INT UNSIGNED;
  DECLARE v_sqlstate CHAR(5);
  DECLARE v_errmsg TEXT;
  DECLARE v_has_error TINYINT(1) DEFAULT 0;

  -- Handler: captura o erro e faz rollback, mas não dá SELECT aqui
  DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
    BEGIN
      GET DIAGNOSTICS CONDITION 1 v_sqlstate = RETURNED_SQLSTATE, v_errmsg = MESSAGE_TEXT;
      SET v_has_error = 1;
      ROLLBACK;
    END;
  START TRANSACTION;

  SELECT position INTO v_old_pos
  FROM thing_has_thing
  WHERE idHasPart   = p_idHasPart
    AND typeHasPart = p_typeHasPart
    AND idIsPartOf  = p_idIsPartOf
    AND typeIsPartOf = p_typeIsPartOf
  LIMIT 1;

  IF v_old_pos IS NULL THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro não encontrado para exclusão';
  END IF;

  DELETE FROM thing_has_thing
  WHERE idHasPart   = p_idHasPart
    AND typeHasPart = p_typeHasPart
    AND idIsPartOf  = p_idIsPartOf
    AND typeIsPartOf = p_typeIsPartOf;

  UPDATE thing_has_thing
  SET position = position - 1
  WHERE idHasPart   = p_idHasPart
    AND typeHasPart = p_typeHasPart
    AND typeIsPartOf= p_typeIsPartOf
    AND position > v_old_pos;

  -- Commit apenas se não houve erro
  IF v_has_error = 0 THEN
    COMMIT;
  END IF;

  -- Em caso de erro, retorna uma linha com detalhes (o PHP verá $data não vazio)
  IF v_has_error = 1 THEN
    SELECT JSON_OBJECT('status','error','sqlstate',v_sqlstate,'message',v_errmsg) AS error;
  END IF;
END$$
DELIMITER ;


--
-- procedure: sp_thing_has_thing_insert
--
DELIMITER $$
CREATE PROCEDURE `sp_thing_has_thing_insert`(
  IN p_idHasPart INT UNSIGNED,
  IN p_typeHasPart VARCHAR(48),
  IN p_idIsPartOf INT UNSIGNED,
  IN p_typeIsPartOf VARCHAR(48),
  IN p_caption VARCHAR(255),
  IN p_position INT UNSIGNED,
  IN p_representativeOfPage TINYINT(1)
)
BEGIN
  DECLARE v_pos INT UNSIGNED;
  DECLARE v_sqlstate CHAR(5);
  DECLARE v_errmsg TEXT;
  DECLARE v_has_error TINYINT(1) DEFAULT 0;

  -- Handler: captura o erro e faz rollback, mas não dá SELECT aqui
  DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
    BEGIN
      GET DIAGNOSTICS CONDITION 1 v_sqlstate = RETURNED_SQLSTATE, v_errmsg = MESSAGE_TEXT;
      SET v_has_error = 1;
      ROLLBACK;
    END;

  START TRANSACTION;

  -- Se posição não for informada ou inválida, vai para o final
  IF p_position IS NULL OR p_position = 0 THEN
    SELECT COALESCE(MAX(position), 0) + 1
    INTO v_pos
    FROM thing_has_thing
    WHERE idHasPart = p_idHasPart
      AND typeHasPart = p_typeHasPart;
  ELSE
    SET v_pos = p_position;

    -- Abre espaço no grupo
    UPDATE thing_has_thing
    SET position = position + 1
    WHERE idHasPart = p_idHasPart
      AND typeHasPart = p_typeHasPart
      AND position >= v_pos;
  END IF;

  -- marca como 0 se representativeOfPage for null
  IF p_representativeOfPage is null THEN
    SET p_representativeOfPage = 0;
  END IF;

  --
  -- VERIFICA REPRESENTATIVO
  --
  -- Zera todos representativoOfPage do grupo se for = 1
  IF p_representativeOfPage = 1 THEN
    -- Zera todos representativeOfPage do grupo se for = 1
    UPDATE thing_has_thing
    SET representativeOfPage = 0
    WHERE idHasPart   = p_idHasPart
      AND typeHasPart = p_typeHasPart;
    -- salva nova imagem
    UPDATE thing as t1
    SET image = (SELECT image FROM thing WHERE idthing = p_idIsPartOf)
    WHERE t1.idthing = p_idHasPart;
  END IF;

  INSERT INTO thing_has_thing (idHasPart, typeHasPart, idIsPartOf, typeIsPartOf, caption, position, representativeOfPage)
  VALUES (p_idHasPart, p_typeHasPart, p_idIsPartOf, p_typeIsPartOf, p_caption, v_pos, p_representativeOfPage);

  -- Commit apenas se não houve erro
  IF v_has_error = 0 THEN
    COMMIT;
  END IF;

  -- Em caso de erro, retorna uma linha com detalhes (o PHP verá $data não vazio)
  IF v_has_error = 1 THEN
    SELECT JSON_OBJECT('status','error','sqlstate',v_sqlstate,'message',v_errmsg) AS error;
  END IF;
END$$
DELIMITER ;



--
-- procedure: sp_thing_has_thing_update
--
DELIMITER $$
CREATE PROCEDURE `sp_thing_has_thing_update`(
  IN p_idHasPart INT UNSIGNED,
  IN p_idIsPartOf INT UNSIGNED,
  IN p_caption VARCHAR(255),
  IN p_position INT UNSIGNED,
  IN p_representativeOfPage VARCHAR(1)
)
BEGIN
  DECLARE v_old_pos INT UNSIGNED;
  DECLARE v_new_pos INT UNSIGNED;
  DECLARE v_sqlstate CHAR(5);
  DECLARE v_errmsg TEXT;
  DECLARE v_has_error TINYINT(1) DEFAULT 0;

  -- Handler: captura o erro e faz rollback, mas não dá SELECT aqui
  DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
    BEGIN
      GET DIAGNOSTICS CONDITION 1 v_sqlstate = RETURNED_SQLSTATE, v_errmsg = MESSAGE_TEXT;
      SET v_has_error = 1;
      ROLLBACK;
    END;

  START TRANSACTION;

  --
  -- VERIFICA POSITION
  --
  -- Posição atual do registro
  SELECT position INTO v_old_pos
  FROM thing_has_thing
  WHERE idHasPart   = p_idHasPart
    AND idIsPartOf  = p_idIsPartOf
  LIMIT 1;

  IF v_old_pos IS NULL THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro original não encontrado em thing_has_thing';
  END IF;

  -- Mesma "chave de grupo": só reordenar dentro do grupo
  IF p_position IS NULL OR p_position = 0 THEN
    -- Se posição não informada, mantém posição atual
    SET v_new_pos = v_old_pos;
  ELSE
    SET v_new_pos = p_position;
  END IF;

  IF v_new_pos <> v_old_pos THEN
    IF v_new_pos < v_old_pos THEN
      -- Desloca para cima: empurra para baixo quem está no intervalo [v_new_pos, v_old_pos-1]
      UPDATE thing_has_thing
      SET position = position + 1
      WHERE idHasPart   = p_idHasPart
        AND position BETWEEN v_new_pos AND v_old_pos - 1;
    ELSE
      -- Desloca para baixo: puxa para cima quem está no intervalo [v_old_pos+1, v_new_pos]
      UPDATE thing_has_thing
      SET position = position - 1
      WHERE idHasPart   = p_idHasPart
        AND position BETWEEN v_old_pos + 1 AND v_new_pos;
    END IF;
  END IF;

  --
  -- VERIFICA REPRESENTATIVO
  --
  IF p_representativeOfPage = '1' THEN
    -- Zera todos representativeOfPage do grupo se for = 1
    UPDATE thing_has_thing
    SET representativeOfPage = 0
    WHERE idHasPart   = p_idHasPart;
    -- salva nova imagem
    UPDATE thing as t1
    SET image = (SELECT image FROM thing WHERE idthing = p_idIsPartOf)
    WHERE t1.idthing = p_idHasPart;
  END IF;

  -- atualiza o item
  UPDATE thing_has_thing
  SET caption  = p_caption,
      position = v_new_pos,
      representativeOfPage = IF(p_representativeOfPage = '', representativeOfPage, p_representativeOfPage)
  WHERE idHasPart    = p_idHasPart
    AND idIsPartOf   = p_idIsPartOf;

  -- Commit apenas se não houve erro
  IF v_has_error = 0 THEN
    COMMIT;
  END IF;

  -- Em caso de erro, retorna uma linha com detalhes (o PHP verá $data não vazio)
  IF v_has_error = 1 THEN
    SELECT JSON_OBJECT('status','error','sqlstate',v_sqlstate,'message',v_errmsg) AS error;
  END IF;
END$$
DELIMITER ;

--
-- procedure: insert_thing_has_thing
--
DELIMITER $$
CREATE PROCEDURE `insert_thing_has_thing`(IN table_has_part VARCHAR(64), IN table_is_part_of VARCHAR(64))
BEGIN
  DECLARE v_table_has VARCHAR(128);
  DECLARE v_id_name_has_part VARCHAR(128);
  DECLARE v_id_name_is_part_of VARCHAR(128);
  DECLARE v_upper_has_part VARCHAR(128);
  DECLARE v_upper_is_part_of VARCHAR(128);

  SET v_table_has = 'thing_has_imageObject';
  SET v_id_name_has_part = CONCAT('id',table_has_part);
  SET v_id_name_is_part_of = CONCAT('id',table_is_part_of);
  SET v_upper_has_part = CONCAT(UPPER(LEFT(table_has_part, 1)),SUBSTRING(table_has_part, 2));
  SET v_upper_is_part_of = CONCAT(UPPER(LEFT(table_is_part_of, 1)),SUBSTRING(table_is_part_of, 2));

  SET @v_sql_text = CONCAT(
          'INSERT INTO `thing_has_thing` (`idHasPart`, `typeHasPart`, `idIsPartOf`, `typeIsPartOf`, `caption`, `position`, `representativeOfPage`)
            SELECT ', table_has_part, '.`thing`,
      ''', v_upper_has_part, ''',
      ', table_is_part_of, '.`thing`,
      ''', v_upper_is_part_of, ''',
      ', v_table_has, '.`caption`,
      ', v_table_has, '.`position`,
      ', v_table_has, '.`representativeOfPage`
      FROM ', v_table_has, '
      JOIN `', table_has_part, '` ON ', v_table_has, '.`idthing` = `', table_has_part, '`.`thing`
      JOIN `', table_is_part_of, '` ON ', v_table_has, '.`', v_id_name_is_part_of, '` = `', table_is_part_of, '`.`', v_id_name_is_part_of, '`'
                    );

  PREPARE stmt FROM @v_sql_text;
  EXECUTE stmt;
  DEALLOCATE PREPARE stmt;
END$$
DELIMITER ;

call insert_thing_has_thing('action','imageObject');
call insert_thing_has_thing('event','imageObject');
call insert_thing_has_thing('person','imageObject');
call insert_thing_has_thing('webPage','imageObject');
call insert_thing_has_thing('webPageElement','imageObject');
call insert_thing_has_thing('webSite','imageObject');

alter table creativeWork
  drop column position;