-- THING
CREATE TABLE IF NOT EXISTS `thing` (
  `idthing` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `additionalType` VARCHAR(255) NULL DEFAULT NULL,
  `alternateName` VARCHAR(255) NULL DEFAULT NULL,
  `dateRegistered` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastModified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `description` TEXT NULL DEFAULT NULL,
  `disambiguatingDescription` VARCHAR(255) NULL DEFAULT NULL,
  `image` VARCHAR(255) NULL DEFAULT NULL,
  `mainEntityOfPage` VARCHAR(255) NULL DEFAULT NULL,
  `name` VARCHAR(255) NOT NULL,
  `sameAs` VARCHAR(255) NULL DEFAULT NULL,
  `type` VARCHAR(45) NOT NULL,
  `url` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`idthing`),
  KEY (`name`,`disambiguatingDescription`,`url`),
  CONSTRAINT `thing_check_name` CHECK (`name` <> '')
) ENGINE = InnoDB;

-- RELATIONAL THING HAS THING
CREATE TABLE IF NOT EXISTS `thing_has_thing` (
  `idHasPart` INT UNSIGNED NOT NULL,
  `typeHasPart` VARCHAR(48) NOT NULL,
  `idIsPartOf` INT UNSIGNED NOT NULL,
  `typeIsPartOf` VARCHAR(48) NOT NULL,
  `caption` VARCHAR(255) NULL DEFAULT NULL,
  `position` INT UNSIGNED NULL DEFAULT '1',
  `representativeOfPage` TINYINT NOT NULL DEFAULT 0,
  `repUniqueFlag` TINYINT GENERATED ALWAYS AS (CASE WHEN `representativeOfPage` = 1 THEN 1 END) VIRTUAL,
  PRIMARY KEY (`idHasPart`, `idIsPartOf`, `typeHasPart`, `typeIsPartOf`),
  INDEX `fk_thing_has_thing_idHasPart_idx` (`idHasPart`),
  INDEX `fk_thing_has_thing_idIsPartOf_idx` (`idIsPartOf`),
  UNIQUE KEY `uq_group_representative` (`idHasPart`,`typeHasPart`,`typeIsPartOf`,`repUniqueFlag`),
  CONSTRAINT `fk_thing_has_thing_idHasPart` FOREIGN KEY (`idHasPart`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE,
  CONSTRAINT `fk_thing_has_thing_idIsPartOf` FOREIGN KEY (`idIsPartOf`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;

-- PROPERTY VALUE
CREATE TABLE IF NOT EXISTS `propertyValue` (
  `idpropertyValue` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `value` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`idpropertyValue`)
) ENGINE = InnoDB;

--
-- Insere com reordenação: abre espaço se p_position for informado; se nulo, vai para o final
--
DROP procedure IF EXISTS `sp_thing_has_thing_insert`;

CREATE PROCEDURE sp_thing_has_thing_insert (
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
      AND typeHasPart = p_typeHasPart
      AND typeIsPartOf = p_typeIsPartOf;
  ELSE
    SET v_pos = p_position;

    -- Abre espaço no grupo
    UPDATE thing_has_thing
    SET position = position + 1
    WHERE idHasPart = p_idHasPart
      AND typeHasPart = p_typeHasPart
      AND typeIsPartOf = p_typeIsPartOf
      AND position >= v_pos;
  END IF;

  -- Zera todos representativoOfPage do grupo se for = 1
  IF p_representativeOfPage = 1 THEN
    UPDATE thing_has_thing
    SET representativeOfPage = 0
    WHERE idHasPart   = p_idHasPart
      AND typeHasPart = p_typeHasPart
      AND typeIsPartOf= p_typeIsPartOf;
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
END;

--
-- Atualiza posição e/ou troca de grupo com reordenação nos grupos afetados
--
DROP procedure IF EXISTS `sp_thing_has_thing_update`;

CREATE PROCEDURE sp_thing_has_thing_update(
  IN p_idHasPart INT UNSIGNED,
  IN p_typeHasPart VARCHAR(48),
  IN p_idIsPartOf INT UNSIGNED,
  IN p_typeIsPartOf VARCHAR(48),
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
    AND typeHasPart = p_typeHasPart
    AND idIsPartOf  = p_idIsPartOf
    AND typeIsPartOf= p_typeIsPartOf
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
        AND typeHasPart = p_typeHasPart
        AND typeIsPartOf= p_typeIsPartOf
        AND position BETWEEN v_new_pos AND v_old_pos - 1;
    ELSE
      -- Desloca para baixo: puxa para cima quem está no intervalo [v_old_pos+1, v_new_pos]
      UPDATE thing_has_thing
      SET position = position - 1
      WHERE idHasPart   = p_idHasPart
        AND typeHasPart = p_typeHasPart
        AND typeIsPartOf= p_typeIsPartOf
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
    WHERE idHasPart   = p_idHasPart
      AND typeHasPart = p_typeHasPart
      AND typeIsPartOf= p_typeIsPartOf;
  END IF;

  -- atualiza o item
  UPDATE thing_has_thing
  SET caption  = p_caption,
      position = v_new_pos,
      representativeOfPage = IF(p_representativeOfPage = '', representativeOfPage, p_representativeOfPage)
  WHERE idHasPart    = p_idHasPart
    AND typeHasPart  = p_typeHasPart
    AND idIsPartOf   = p_idIsPartOf
    AND typeIsPartOf = p_typeIsPartOf;

  -- Commit apenas se não houve erro
  IF v_has_error = 0 THEN
    COMMIT;
  END IF;

  -- Em caso de erro, retorna uma linha com detalhes (o PHP verá $data não vazio)
  IF v_has_error = 1 THEN
    SELECT JSON_OBJECT('status','error','sqlstate',v_sqlstate,'message',v_errmsg) AS error;
  END IF;
END;

--
-- Exclui e reordena o grupo
--
DROP procedure IF EXISTS `sp_thing_has_thing_delete`;

CREATE PROCEDURE sp_thing_has_thing_delete (
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
    AND typeIsPartOf= p_typeIsPartOf
  LIMIT 1;

  IF v_old_pos IS NULL THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro não encontrado para exclusão';
  END IF;

  DELETE FROM thing_has_thing
  WHERE idHasPart   = p_idHasPart
    AND typeHasPart = p_typeHasPart
    AND idIsPartOf  = p_idIsPartOf
    AND typeIsPartOf= p_typeIsPartOf;

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
END;
