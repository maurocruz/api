-- Update em 2026-01-09 15:56:56 ---

--
-- THING
--

CREATE TABLE IF NOT EXISTS `thing` (
  `idthing` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `additionalType` VARCHAR(255) NULL DEFAULT NULL,
  `alternateName` VARCHAR(255) NULL DEFAULT NULL,
  `dateRegistered` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastModified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
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

    -- ordena os itens existentes
    SET @pos := 0;
    UPDATE thing_has_thing
    SET position = (@pos := @pos + 1)
    WHERE idHasPart = p_idHasPart AND typeIsPartOf = p_typeIsPartOf
    ORDER BY (position IS NULL),    -- faz os NULLs irem para o final; opcional
             position;

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
END;

--
-- Exclui e reordena o grupo
--
DROP procedure IF EXISTS `sp_thing_has_thing_delete`;

CREATE PROCEDURE `sp_thing_has_thing_delete`(IN p_idHasPart int unsigned, IN p_idIsPartOf int unsigned)
BEGIN
  DECLARE v_typeIsPartOf INT UNSIGNED;
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

  SELECT typeIsPartOf INTO v_typeIsPartOf
  FROM thing_has_thing
  WHERE idHasPart   = p_idHasPart
    AND idIsPartOf  = p_idIsPartOf
  LIMIT 1;

  IF v_typeIsPartOf IS NULL THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Registro não encontrado para exclusão';
  END IF;

  DELETE FROM thing_has_thing
  WHERE idHasPart   = p_idHasPart
    AND idIsPartOf  = p_idIsPartOf;

  SET @pos := 0;

  UPDATE thing_has_thing
  SET position = (@pos := @pos + 1)
  WHERE idHasPart = p_idHasPart
    AND typeIsPartOf = v_typeIsPartOf
  ORDER BY
    (position IS NULL),    -- faz os NULLs irem para o final; opcional
    position;

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
-- CREATIVE WORK
--
CREATE TABLE IF NOT EXISTS `creativeWork` (
  `idcreativeWork` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thing` INT UNSIGNED NOT NULL,
  `about` INT UNSIGNED NULL DEFAULT NULL,
  `acquireLicensePage` VARCHAR(100) NULL DEFAULT NULL,
  `alternativeHeadline` VARCHAR(100) NULL DEFAULT NULL,
  `author` VARCHAR(100) NULL DEFAULT NULL,
  `copyrightHolder` VARCHAR(100) NULL DEFAULT NULL,
  `creativeWorkStatus` VARCHAR(45) NULL DEFAULT NULL,
  `datePublished` DATETIME NULL DEFAULT NULL,
  `editor` VARCHAR(100) NULL DEFAULT NULL,
  `encodingFormat` VARCHAR(100) NULL DEFAULT NULL,
  `headline` VARCHAR(255) NULL DEFAULT NULL,
  `keywords` VARCHAR(255) NOT NULL DEFAULT '',
  `license` VARCHAR(100) NULL DEFAULT NULL,
  `locationCreated` VARCHAR(100) NULL DEFAULT NULL,
  `maintainer` VARCHAR(100) NULL DEFAULT NULL,
  `publisher` VARCHAR(100) NULL DEFAULT NULL,
  `size` VARCHAR(45) NULL DEFAULT NULL,
  `text` TEXT NULL DEFAULT NULL,
  `thumbnail` VARCHAR(255) NULL DEFAULT NULL,
  `version` VARCHAR(50) NULL DEFAULT '',
  PRIMARY KEY (`idcreativeWork`, `thing`),
  KEY `fk_creativeWork_thing_idx` (`thing`),
  KEY `creativeWork_keywords_idx` (`keywords`),
  INDEX `fk_creativeWork_about_idx` (`about`),
  CONSTRAINT `fk_creativeWork_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE,
  CONSTRAINT `fk_creativeWork_about` FOREIGN KEY (`about`) REFERENCES `thing` (`idthing`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE = InnoDB;

--
-- MEDIA OBJECT
--
CREATE TABLE IF NOT EXISTS `mediaObject` (
  `idmediaObject` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `creativeWork` INT UNSIGNED NOT NULL,
  `thing` INT UNSIGNED NOT NULL,
  `bitrate` DECIMAL(6,2) NULL DEFAULT NULL,
  `duration` TIME NULL DEFAULT NULL,
  `contentSize` VARCHAR(100) NULL DEFAULT NULL,
  `contentUrl` VARCHAR(255) NOT NULL,
  `encodingFormat` VARCHAR(50) NULL DEFAULT NULL,
  `height` INT NULL DEFAULT NULL,
  `width` INT NULL DEFAULT NULL,
  `uploadDate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idmediaObject`, `creativeWork`, `thing`),
  INDEX `fk_mediaObject_thing_idx` (`thing` ASC) VISIBLE,
  INDEX `fk_mediaObject_creativeWork_idx` (`creativeWork` ASC) VISIBLE,
  CONSTRAINT `fk_mediaObject_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE,
  CONSTRAINT `fk_mediaObject_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE
) ENGINE = InnoDB;
--
-- ACTION
--
CREATE TABLE IF NOT EXISTS `action` (
`idaction` INT UNSIGNED NOT NULL AUTO_INCREMENT,
`thing` INT UNSIGNED NOT NULL,
`actionProcess` VARCHAR(255) DEFAULT NULL,
`actionStatus` VARCHAR(45) NOT NULL,
`agent` INT UNSIGNED DEFAULT NULL,
`endTime` DATETIME NULL DEFAULT NULL,
`object` INT UNSIGNED DEFAULT NULL,
`provider` INT UNSIGNED DEFAULT NULL,
`result` TEXT DEFAULT NULL,
`startTime` DATETIME NOT NULL,
`targetCollection` VARCHAR(45) NULL DEFAULT NULL,
PRIMARY KEY (`idaction`, `thing`),
INDEX `fk_action_thing_idx` (`thing`),
INDEX `fk_action_agent_idx` (`agent`),
INDEX `fk_action_object_idx` (`object`),
INDEX `fk_action_provider_idx` (`provider`),
CONSTRAINT `fk_action_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
CONSTRAINT `fk_action_agent` FOREIGN KEY (`agent`) REFERENCES `user` (`iduser`) ON DELETE SET NULL ON UPDATE NO ACTION,
CONSTRAINT `fk_action_object` FOREIGN KEY (`object`) REFERENCES `thing` (`idthing`) ON DELETE SET NULL ON UPDATE NO ACTION,
CONSTRAINT `fk_action_provider` FOREIGN KEY (`provider`) REFERENCES `thing` (`idthing`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE = InnoDB;
-- history
ALTER TABLE `history`
  CHANGE COLUMN `user` `user` INT UNSIGNED NOT NULL ;
-- map viewport
ALTER TABLE `map_viewport`
  CHANGE COLUMN `iduser` `iduser` INT UNSIGNED NOT NULL ;
-- user history
ALTER TABLE `user_history`
  CHANGE COLUMN `iduser` `iduser` INT UNSIGNED NOT NULL ;
-- user password
ALTER TABLE `passwordReset`
  CHANGE COLUMN `iduser` `iduser` INT UNSIGNED NOT NULL , RENAME TO  `user_passwordReset` ;
-- user privileges
ALTER TABLE `user_privileges`
  CHANGE COLUMN `iduser_privileges` `iduser_privileges` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  CHANGE COLUMN `iduser` `iduser` INT UNSIGNED NOT NULL,
  CHANGE `actions` `action` CHAR(4) DEFAULT 'r' NOT NULL ;

-- user id change
ALTER TABLE `user`
  CHANGE COLUMN `iduser` `iduser` INT UNSIGNED NOT NULL AUTO_INCREMENT ;

-- foreign keys
ALTER TABLE `map_viewport`
  ADD CONSTRAINT `fk_map_viewport_user`
  FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`) ON DELETE CASCADE;
ALTER TABLE `user_passwordReset`
  ADD CONSTRAINT `fk_user_passwordReset_user`
    FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`) ON DELETE CASCADE;
ALTER TABLE `user_privileges`
  ADD CONSTRAINT `fk_user_privileges_user`
    FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `user_history`
  ADD CONSTRAINT `fk_user_history_user`
    FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`) ON DELETE RESTRICT ON UPDATE RESTRICT;

-- PASSWORD RESET
CREATE TABLE IF NOT EXISTS `user_passwordReset` (
  `iduser` INT UNSIGNED NOT NULL,
  `selector` VARCHAR(16) NULL DEFAULT NULL,
  `token` VARCHAR(64) NULL DEFAULT NULL,
  `expires` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`iduser`),
  INDEX `fk_user_passwordReset_user_idx` (`iduser`),
  CONSTRAINT `fk_user_passwordReset_user` FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB;
--
-- CONTACT POINT
--
ALTER TABLE `contactPoint`
  CHANGE COLUMN `idcontactPoint` `idcontactPoint` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idcontactPoint`,
  ADD COLUMN `contactOption` VARCHAR(100) DEFAULT NULL AFTER `thing`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idcontactPoint`);

-- INSERT THING
ALTER TABLE `thing` ADD COLUMN `idcontactPoint` INT UNSIGNED DEFAULT NULL;
-- insert thing
INSERT INTO `thing` (`idcontactPoint`,`name`,`description`,`type`)
SELECT
  cp.`idcontactPoint`,
  COALESCE(
          NULLIF(cp.`name`, ''),
          NULLIF(cp.`telephone`, ''),
          NULLIF(cp.`email`, ''),
          CONCAT('Contact point id ', cp.`idcontactPoint`)
  ) AS `name`,
  CONCAT('Contact point id ', cp.`idcontactPoint`) AS `description`,
  'ContactPoint' AS `type`
FROM `contactPoint` AS cp;
-- update this
UPDATE `contactPoint`
  JOIN `thing` ON thing.idcontactPoint = contactPoint.idcontactPoint
  SET contactPoint.thing = thing.idthing;
-- options
UPDATE `contactPoint` SET `contactOption` = 'whatsapp' WHERE `whatsapp` = 1;
-- drop thing column
ALTER TABLE `thing` DROP COLUMN `idcontactPoint`;

-- alter table
ALTER TABLE `contactPoint`
  CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
  DROP COLUMN `name`,
  DROP COLUMN `whatsapp`,
  DROP COLUMN `obs`,
  DROP COLUMN `position`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idcontactPoint`,`thing`);

-- add foreign keys
ALTER TABLE `contactPoint`
  ADD KEY `fk_contactPoint_thing_idx` (`thing`),
  ADD CONSTRAINT `fk_contactPoint_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION;
-- POSTAL ADDRESS
ALTER TABLE `postalAddress`
  CHANGE COLUMN `idpostalAddress` `idpostalAddress` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idpostalAddress`);
--
-- Property Value
--

ALTER TABLE `propertyValue`
  CHANGE COLUMN `idpropertyValue` `idpropertyValue` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ADD COLUMN `thing` INT UNSIGNED NOT NULL AFTER `idpropertyValue`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idpropertyValue`);

-- INSERT THINGS
ALTER TABLE `thing` ADD COLUMN `idpropertyValue` INT UNSIGNED DEFAULT NULL;

-- insert thing
INSERT INTO `thing` (`idpropertyValue`,`name`,`description`,`type`)
SELECT
  pv.`idpropertyValue`,
  pv.`name`,
  CONCAT('name=', pv.`name`,'; value=', pv.`value`) AS `description`,
  'PropertyValue' AS `type`
FROM `propertyValue` AS pv;

-- update this
UPDATE `propertyValue`
  JOIN `thing` ON thing.idpropertyValue = propertyValue.idpropertyValue
SET propertyValue.thing = thing.idthing
WHERE 1;

-- drop thing column
ALTER TABLE `thing` DROP COLUMN `idpropertyValue`;

-- alter table
ALTER TABLE `propertyValue`
  DROP COLUMN `name`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idpropertyValue`,`thing`);
--
-- IMAGEOBJECT
--

ALTER TABLE `imageObject`
  CHANGE COLUMN `idimageObject` `idimageObject` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idimageObject`,
  ADD COLUMN `mediaObject` INT UNSIGNED DEFAULT NULL AFTER `idimageObject`,
  ADD COLUMN `creativeWork` INT UNSIGNED DEFAULT NULL AFTER `idimageObject`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idimageObject`);

-- INSERT THING
ALTER TABLE `thing` ADD COLUMN `idimageObject` INT UNSIGNED DEFAULT NULL;

-- insere dados em thing
INSERT INTO `thing` (`idimageObject`,`name`,`lastModified`,`dateRegistered`,`type`)
  SELECT `idimageObject`,
   IF(`contentUrl` <> '',`contentUrl`,'Undefined name'),
   `uploadDate`,
   `uploadDate`,
   'ImageObject'
  FROM `imageObject`;

-- update this
UPDATE `imageObject`
  JOIN `thing` ON `imageObject`.idimageObject = `thing`.idimageObject
SET `imageObject`.thing=`thing`.idthing
WHERE `thing`.idimageObject IS NOT NULL;

-- drop thing column
ALTER TABLE `thing` DROP COLUMN `idimageObject`;

-- insert parent
INSERT INTO `creativeWork` (`thing`,`author`,`license`,`acquireLicensePage`,`thumbnail`,`keywords`,`copyrightHolder`)
  SELECT `thing`,`author`,`license`,`acquireLicensePage`,`thumbnail`,`keywords`,`copyright` from `imageObject`;

-- insere parent
INSERT INTO `mediaObject` (`thing`,`creativeWork`,`contentSize`,`contentUrl`,`encodingFormat`,`height`,`width`,`uploadDate`)
  SELECT `imageObject`.`thing`,`idcreativeWork`,`contentSize`,`contentUrl`,`imageObject`.`encodingFormat`,`height`,`width`,`uploadDate` FROM `imageObject`
  JOIN `creativeWork` ON `creativeWork`.thing=`imageObject`.thing
WHERE `contentUrl` <> '';

-- update this
UPDATE `imageObject`
  JOIN `mediaObject` ON `imageObject`.thing = `mediaObject`.thing
  JOIN `creativeWork` ON `imageObject`.thing = `creativeWork`.thing
SET `imageObject`.mediaObject = `mediaObject`.idmediaObject, `imageObject`.creativeWork = `creativeWork`.idcreativeWork
WHERE `imageObject`.thing = `mediaObject`.thing;

-- alter table
ALTER TABLE `imageObject`
  CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
  CHANGE COLUMN `mediaObject` `mediaObject` INT UNSIGNED NOT NULL,
  CHANGE COLUMN `creativeWork` `creativeWork` INT UNSIGNED NOT NULL,
  DROP COLUMN `contentUrl`,
  DROP COLUMN `contentSize`,
  DROP COLUMN `width`,
  DROP COLUMN `height`,
  DROP COLUMN `encodingFormat`,
  DROP COLUMN `author`,
  DROP COLUMN `license`,
  DROP COLUMN `acquireLicensePage`,
  DROP COLUMN `uploadDate`,
  DROP COLUMN `thumbnail`,
  DROP COLUMN `keywords`,
  DROP COLUMN `copyright`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idimageObject`,`mediaObject`,`thing`);

-- add forign keys
ALTER TABLE `imageObject`
  ADD KEY `fk_imageObject_thing_idx` (`thing`),
  ADD KEY `fk_imageObject_creativeWork_idx` (`creativeWork`),
  ADD KEY `fk_imageObject_mediaObject_idx` (`mediaObject`),
  ADD CONSTRAINT `fk_imageObject_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_imageObject_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_imageObject_mediaObject` FOREIGN KEY (`mediaObject`) REFERENCES `mediaObject` (`idmediaObject`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- PERSON
--

ALTER TABLE `person`
  CHANGE COLUMN `idperson` `idperson` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  CHANGE COLUMN `address` `homeLocation` INT UNSIGNED NULL,
  ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idperson`,
  ADD COLUMN `deathDate` DATE DEFAULT NULL,
  ADD COLUMN `deathPlace` VARCHAR(45) DEFAULT NULL,
  DROP PRIMARY KEY ,
  ADD PRIMARY KEY (`idperson`);

-- CREATE THING
ALTER TABLE `thing` ADD COLUMN `idperson` INT UNSIGNED DEFAULT NULL;

-- insert thing
INSERT INTO `thing` (`idperson`,`name`,`url`,`lastModified`,`dateRegistered`,`type`)
  SELECT
    idperson,
    CONCAT(`givenName`,IF(familyName is NOT NULL,CONCAT(' ',familyName),'')) as name,
    `url`,
    `dateModified`,
    `dateRegistration`,
    'Person'
  FROM `person`;

-- atualiza tabela
UPDATE `person`
  JOIN `thing` ON thing.idperson=person.idperson SET person.thing = thing.idthing
WHERE 1;

ALTER TABLE `thing` DROP COLUMN `idperson`;

-- has contact point
INSERT INTO `thing_has_thing` (idHasPart, typeHasPart, idIsPartOf, typeIsPartOf)
  SELECT
    `person`.thing,
    'Person',
    `contactPoint`.thing,
    'ContactPoint'
  FROM `person_has_contactPoint`
  JOIN `person` ON `person`.idperson = `person_has_contactPoint`.idperson
  JOIN `contactPoint` ON `contactPoint`.idcontactPoint = `person_has_contactPoint`.idcontactPoint;

-- IMAGES
CALL set_image_in_thing('person');

-- insert images
CALL insert_thing_has_thing('person','imageObject');

ALTER TABLE `person`
  CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
  DROP COLUMN `name`,
  DROP COLUMN `url`,
  DROP COLUMN `dateRegistration`,
  DROP COLUMN `dateModified`,
  DROP PRIMARY KEY ,
  ADD PRIMARY KEY (`idperson`,`thing`);

DROP TABLE `person_has_contactPoint`;
DROP TABLE `person_has_imageObject`;

-- add foreign key
ALTER TABLE `person`
  ADD KEY `fk_person_thing_idx` (`thing`),
  ADD CONSTRAINT `fk_person_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION;


--
-- ARTICLE
--

ALTER TABLE `article`
  CHANGE COLUMN `idarticle` `idarticle` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  CHANGE COLUMN `headline` `headline` VARCHAR(255) DEFAULT NULL,
  ADD COLUMN `creativeWork` INT UNSIGNED DEFAULT NULL AFTER `idarticle`,
  ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idarticle`,
  ADD COLUMN `backstory` TEXT,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idarticle`);

-- INSERT THING
ALTER TABLE `thing` ADD COLUMN `idarticle` INT UNSIGNED DEFAULT NULL;
-- insert thing
INSERT INTO `thing` (`idarticle`,`name`,`additionalType`,`dateRegistered`, `lastModified`, `type`)
  SELECT
    `idarticle`,
    `headline`,
    `additionalType`,
    `dateCreated`,
    `datePublished`,
    'Article'
  FROM `article`;

-- update this
UPDATE `article`
  JOIN `thing` ON thing.idarticle = article.idarticle
  SET article.thing = thing.idthing
WHERE article.headline <> '';

-- drop thing column
ALTER TABLE `thing` DROP COLUMN `idarticle`;

-- insert creative work
INSERT INTO `creativeWork` (`thing`,`headline`,`datePublished`,`author`,`publisher`, `creativeWorkStatus`)
  SELECT
    `thing`,
    `headline`,
    `datePublished`,
    `author`,
    `publisher`,
    IF(`publishied`=1,'published','')
  FROM `article`;

-- update child
UPDATE `article`
  JOIN `creativeWork` ON creativeWork.thing=article.thing
  SET article.creativeWork=creativeWork.idcreativeWork
WHERE article.headline <> '';

UPDATE `creativeWork`
  JOIN `person` ON `person`.idperson=`creativeWork`.author
SET `creativeWork`.author=`person`.thing
WHERE `creativeWork`.author IS NOT NULL;

-- IMAGES
CALL set_image_in_thing('article');

-- insert images
CALL insert_thing_has_thing('article','imageObject');

-- alter table
ALTER TABLE `article`
  CHANGE COLUMN `creativeWork` `creativeWork` INT UNSIGNED NOT NULL,
  CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
  DROP COLUMN `additionalType`,
  DROP COLUMN `headline`,
  DROP COLUMN `dateCreated`,
  DROP COLUMN `dateModified`,
  DROP COLUMN `datePublished`,
  DROP COLUMN `publishied`,
  DROP COLUMN `author`,
  DROP COLUMN `publisher`,
  DROP COLUMN `publisherType`,
  DROP COLUMN `position`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idarticle`,`creativeWork`,`thing`);

-- drop old relationship
DROP TABLE `article_has_imageObject`;

-- add foreign keys
ALTER TABLE `article`
  ADD KEY `fk_article_thing_idx` (`thing`),
  ADD KEY `fk_article_creativeWork_idx` (`creativeWork`),
  ADD CONSTRAINT `fk_article_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_article_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- BOOK
--

ALTER TABLE `book`
  CHANGE COLUMN `idbook` `idbook` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  CHANGE COLUMN `datePublished` `datePublished` VARCHAR(19) DEFAULT NULL,
  ADD COLUMN `isbn` VARCHAR(18) NULL AFTER `idbook`,
  ADD COLUMN `illustrator` INT UNSIGNED NULL AFTER `idbook`,
  ADD COLUMN `bookFormat` VARCHAR(20) NULL AFTER `idbook`,
  ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idbook`,
  ADD COLUMN `creativeWork` INT UNSIGNED NOT NULL AFTER `idbook`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idbook`);

-- INSERT THING
ALTER TABLE `thing` ADD COLUMN `idbook` INT UNSIGNED DEFAULT NULL;

-- insert thing
INSERT INTO `thing` (`idbook`,`name`, `lastModified`, `dateRegistered`, `type`)
  SELECT
    `idbook`,
    `name`,
    `dateModified`,
    `dateCreated`,
    'Book'
  FROM `book`;

-- update this
UPDATE `book`
  JOIN `thing` ON thing.idbook = book.idbook
  SET book.thing=thing.idthing
WHERE thing.name <> '';

-- drop thing column
ALTER TABLE `thing` DROP COLUMN `idbook`;

-- insert creativework
INSERT INTO `creativeWork` (`thing`,`author`,`datePublished`,`keywords`,`locationCreated`,`publisher`,`version`)
  SELECT
    `thing`,
    `author`,
    CONCAT(`datePublished`,'-01-01 00:00:00') as datetime,
    `keywords`,
    `locationCreated`,
    `publisher`,
    `version`
  FROM `book`;

-- update child
UPDATE `book`
  JOIN `creativeWork` ON creativeWork.thing=book.thing
  SET book.creativeWork=creativeWork.idcreativeWork
WHERE book.name <> '';

-- IMAGES
CALL set_image_in_thing('book');

-- insert images
CALL insert_thing_has_thing('book','imageObject');

-- alter table
ALTER TABLE `book`
  CHANGE COLUMN `creativeWork` `creativeWork` INT UNSIGNED NOT NULL,
  CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
  DROP COLUMN `author`,
  DROP COLUMN `birthDate`,
  DROP COLUMN `deathDate`,
  DROP COLUMN `datePublished`,
  DROP COLUMN `keywords`,
  DROP COLUMN `locationCreated`,
  DROP COLUMN `name`,
  DROP COLUMN `publisher`,
  DROP COLUMN `version`,
  DROP COLUMN `dateCreated`,
  DROP COLUMN `dateModified`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idbook`,`creativeWork`,`thing`);

-- drop old relationship
DROP TABLE `book_has_imageObject`;

-- add foreign keys
ALTER TABLE `book`
  ADD KEY `fk_book_thing_idx` (`thing`),
  ADD KEY `fk_book_creativeWork_idx` (`creativeWork`),
  ADD CONSTRAINT `fk_book_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_book_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- VIDEO OBJECT
--

ALTER TABLE `videoObject`
  CHANGE COLUMN `idvideoObject` `idvideoObject` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idvideoObject`,
  ADD COLUMN `mediaObject` INT UNSIGNED DEFAULT NULL AFTER `idvideoObject`,
  ADD COLUMN `creativeWork` INT UNSIGNED DEFAULT NULL AFTER `idvideoObject`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idvideoObject`),
  ENGINE = InnoDB ;

-- insert thing
ALTER TABLE `thing` ADD COLUMN `idvideoObject` INT UNSIGNED DEFAULT NULL;

INSERT INTO `thing` (`idvideoObject`,`name`,`url`,`image`,`description`,`dateRegistered`,`lastModified`,`type`)
  SELECT `idvideoObject`,`name`,`url`,
    thumbnailUrl,
          description,
IF(`uploadDate`, `uploadDate`, CURDATE()),
IF(`uploadDate`, `uploadDate`, CURDATE()),
    'videoObject'
  FROM `videoObject` WHERE `name` <> '';

-- update this
UPDATE `videoObject`
  JOIN `thing` ON `videoObject`.idvideoObject = thing.idvideoObject
SET `videoObject`.thing = `thing`.idthing
WHERE `thing`.name <> '';

ALTER TABLE `thing` DROP COLUMN `idvideoObject`;

-- insert parent
INSERT INTO `creativeWork` (`thing`,`thumbnail`,`keywords`)
  SELECT `thing`,`thumbnailUrl`,`tag` from `videoObject`;

-- insere parent
INSERT INTO `mediaObject` (`thing`,`creativeWork`,`contentUrl`,`bitrate`,`duration`,`uploadDate`)
  SELECT `videoObject`.`thing`,`idcreativeWork`,`contentUrl`,`bitrate`,`duration`,
         IF(`uploadDate`,`uploadDate`,CURDATE()) FROM `videoObject`
  JOIN `creativeWork` ON `creativeWork`.thing = `videoObject`.thing;

-- update this
UPDATE `videoObject`
  JOIN `mediaObject` ON `videoObject`.thing = `mediaObject`.thing
  JOIN `creativeWork` ON `videoObject`.thing = `creativeWork`.thing
  SET `videoObject`.mediaObject = `mediaObject`.idmediaObject, `videoObject`.creativeWork = `creativeWork`.idcreativeWork
WHERE 1;

-- alter table
ALTER TABLE `videoObject`
  CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
  CHANGE COLUMN `mediaObject` `mediaObject` INT UNSIGNED NOT NULL,
  CHANGE COLUMN `creativeWork` `creativeWork` INT UNSIGNED NOT NULL,
  DROP COLUMN `contentUrl`,
  DROP COLUMN `uploadDate`,
  DROP COLUMN `thumbnailUrl`,
  DROP COLUMN `tag`,
  DROP COLUMN `position`,
  DROP COLUMN `bitrate`,
  DROP COLUMN `duration`,
  DROP COLUMN `name`,
  DROP COLUMN `url`,
  DROP COLUMN `description`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idvideoObject`,`mediaObject`,`thing`);

-- add foreign key
ALTER TABLE `videoObject`
  ADD KEY `fk_videoObject_thing_idx` (`thing`),
  ADD KEY `fk_videoObject_creativeWork_idx` (`creativeWork`),
  ADD KEY `fk_videoObject_mediaObject_idx` (`mediaObject`),
  ADD CONSTRAINT `fk_videoObject_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_videoObject_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_videoObject_mediaObject` FOREIGN KEY (`mediaObject`) REFERENCES `mediaObject` (`idmediaObject`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- END VIDEO OBJECT
--
-- WEBSITE
-- ALTER TABLE
ALTER TABLE `webSite`
  CHANGE COLUMN `idwebSite` `idwebSite` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ADD COLUMN `creativeWork` INT UNSIGNED DEFAULT NULL AFTER `idwebSite`,
  ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idwebSite`,
  DROP PRIMARY KEY ,
  ADD PRIMARY KEY (`idwebSite`);

-- INSERT THING
ALTER TABLE `thing` ADD COLUMN `idwebSite` INT UNSIGNED DEFAULT NULL;
-- insert thing
INSERT INTO `thing` (`idwebSite`,`name`,`description`,`url`,`dateRegistered`,`lastModified`,`type`)
  SELECT `idwebSite`,
    IF (`name` <> '', `name`, 'Undefined name'),
    description,
    `url`,
    if(`dateCreated`, `dateCreated`, CURDATE()),
    CURDATE(),
    'WebSite'
  FROM `webSite`;
-- set thing
UPDATE `webSite`
  JOIN `thing` ON thing.idwebSite = webSite.idwebSite
  SET webSite.thing = thing.idthing;
-- drop column
ALTER TABLE `thing` DROP COLUMN `idwebSite`;

-- INSERT IN CREATIVEWORK
INSERT INTO `creativeWork` (`thing`,`publisher`,`editor`)
SELECT `thing`,`publisher`,`editor` FROM `webSite`;
-- update child
UPDATE `webSite`
  JOIN `creativeWork` ON creativeWork.thing = webSite.thing
  SET webSite.creativeWork = creativeWork.idcreativeWork;

-- HAS PERSON
INSERT INTO `thing_has_thing` (idHasPart, typeHasPart, idIsPartOf, typeIsPartOf)
SELECT `webSite`.thing,'WebSite',`person`.thing,'Person' FROM `webSite_has_person`
  JOIN `webSite` ON `webSite`.idwebSite = `webSite_has_person`.idwebSite
  JOIN `person` ON `person`.idperson = `webSite_has_person`.idperson;

-- ALTER TABLE
ALTER TABLE `webSite`
  CHANGE COLUMN `creativeWork` `creativeWork` INT UNSIGNED NOT NULL,
  CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
  DROP COLUMN `name`,
  DROP COLUMN `dateCreated`,
  DROP COLUMN `description`,
  DROP COLUMN `image`,
  DROP COLUMN `url`,
  DROP COLUMN `publisher`,
  DROP COLUMN `editor`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idwebSite`,`creativeWork`,`thing`);

-- drop old relationship
DROP TABLE `webSite_has_person`;

-- add foreign key
ALTER TABLE `webSite`
  ADD KEY `fk_webSite_thing_idx` (`thing`),
  ADD KEY `fk_webSite_creativeWork_idx` (`creativeWork`),
  ADD CONSTRAINT `fk_webSite_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_webSite_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE ON UPDATE NO ACTION;
-- WEBPAGE
-- ALTER TABLE
ALTER TABLE `webPage`
  CHANGE COLUMN `idwebPage` `idwebPage` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ADD COLUMN `creativeWork` INT UNSIGNED DEFAULT NULL AFTER `idwebPage`,
  ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idwebPage`,
  DROP PRIMARY KEY ,
  ADD PRIMARY KEY (`idwebPage`);

-- INSERT THING
ALTER TABLE `thing` ADD COLUMN `idwebPage` INT UNSIGNED DEFAULT NULL;
-- insert thing
INSERT INTO `thing` (`idwebPage`,`name`,`description`,`url`,`dateRegistered`,`lastModified`,`type`)
  SELECT `idwebPage`,
    IF (`name` <> '', `name`, 'Undefined name'),
    description,
    `url`,
    if(`dateCreated`, `dateCreated`, CURDATE()),
    `dateModified`,
    'WebPage'
    FROM `webPage`;
-- set thing
UPDATE `webPage`
  JOIN `thing` ON thing.idwebPage = webPage.idwebPage
  SET webPage.thing = thing.idthing;
-- drop column
ALTER TABLE `thing` DROP COLUMN `idwebPage`;

-- insert in creativeWork
INSERT INTO `creativeWork` (`thing`,`alternativeHeadline`)
  SELECT `thing`,`alternativeHeadline` FROM `webPage`;

-- update child
UPDATE `webPage`
  JOIN `creativeWork` ON creativeWork.thing = webPage.thing
  SET webPage.creativeWork = creativeWork.idcreativeWork;

-- UPDATE IS PART OF
UPDATE `webPage`
  JOIN `webSite` ON webPage.ispartOf = webSite.idwebSite
SET webPage.isPartOf = webSite.thing;

INSERT INTO `thing_has_thing` (idHasPart, typeHasPart, idIsPartOf, typeIsPartOf)
SELECT `webPage`.isPartOf, 'WebSite', `webPage`.thing, 'WebPage' FROM `webPage`
left join `thing` on `thing`.idthing = `webPage`.isPartOf
where `webPage`.isPartOf is not null;

-- has propertyValue
INSERT INTO `thing_has_thing` (idHasPart, typeHasPart, idIsPartOf, typeIsPartOf)
SELECT `webPage`.thing, 'WebPage', `propertyValue`.thing, 'PropertyValue' FROM `webPage_has_propertyValue`
 JOIN `webPage` ON `webPage`.idwebPage = `webPage_has_propertyValue`.idwebPage
 JOIN `propertyValue` ON `propertyValue`.idpropertyValue = `webPage_has_propertyValue`.idpropertyValue;

-- alter table
ALTER TABLE `webPage`
  CHANGE COLUMN `creativeWork` `creativeWork` INT UNSIGNED NOT NULL,
  CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
  DROP COLUMN `alternativeHeadline`,
  DROP COLUMN `name`,
  DROP COLUMN `dateCreated`,
  DROP COLUMN `dateModified`,
  DROP COLUMN `description`,
  DROP COLUMN `isPartOf`,
  DROP COLUMN `url`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idwebPage`,`creativeWork`,`thing`);

DROP TABLE `webPage_has_propertyValue`;
DROP TABLE `webPage_has_attributes`;

-- add foreign key
ALTER TABLE `webPage`
  ADD KEY `fk_webPage_thing_idx` (`thing`),
  ADD KEY `fk_webPage_creativeWork_idx` (`creativeWork`),
  ADD CONSTRAINT `fk_webPage_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_webPage_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE ON UPDATE NO ACTION;
-- WEBPAGE ELEMENT
-- ALTER TABLE
ALTER TABLE `webPageElement`
  CHANGE COLUMN `idwebPageElement` `idwebPageElement` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ADD COLUMN `creativeWork` INT UNSIGNED DEFAULT NULL AFTER `idwebPageElement`,
  ADD COLUMN `cssSelector` TEXT AFTER `creativeWork`,
  ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `cssSelector`,
  DROP PRIMARY KEY ,
  ADD PRIMARY KEY (`idwebPageElement`);

-- INSERT THING
ALTER TABLE `thing` ADD COLUMN `idwebPageElement` INT UNSIGNED DEFAULT NULL;
-- insert thing
INSERT INTO `thing` (`idwebPageElement`,`name`,`dateRegistered`,`lastModified`,`type`)
  SELECT `idwebPageElement`,
         IF (`name` <> '' AND `name` IS NOT NULL, `name`, 'Undefined name'),
         if(`dateCreated`, `dateCreated`, CURDATE()),
         `dateModified`,
    'WebPageElement'
  FROM `webPageElement`;
-- set thing
UPDATE `webPageElement`
  JOIN `thing` ON thing.idwebPageElement = webPageElement.idwebPageElement
  SET webPageElement.thing = thing.idthing;
-- drop column
ALTER TABLE `thing` DROP COLUMN `idwebPageElement`;


-- INSERT IN CREATIVEWORK
INSERT INTO `creativeWork` (`thing`,`text`,`author`)
  SELECT `thing`,`text`,`author` FROM `webPageElement`;
-- update child
UPDATE `webPageElement`
  JOIN `creativeWork` ON creativeWork.thing = webPageElement.thing
  SET webPageElement.creativeWork = creativeWork.idcreativeWork;

-- has propertyValue
INSERT INTO `thing_has_thing` (idHasPart, typeHasPart, idIsPartOf, typeIsPartOf)
SELECT `webPageElement`.thing, 'WebPageElement', `propertyValue`.thing, 'PropertyValue' FROM `webPageElement_has_propertyValue`
 JOIN `webPageElement` ON `webPageElement`.idwebPageElement = `webPageElement_has_propertyValue`.idwebPageElement
 JOIN `propertyValue` ON `propertyValue`.idpropertyValue = `webPageElement_has_propertyValue`.idpropertyValue;

-- is part of
-- UPDATE IS PART OF
UPDATE `webPageElement`
  JOIN `webPage` ON webPageElement.ispartOf = webPage.idwebPage
SET webPageElement.isPartOf = webPage.thing;

INSERT INTO `thing_has_thing` (idHasPart, typeHasPart, idIsPartOf, typeIsPartOf)
SELECT `webPageElement`.isPartOf, 'WebPage', `webPageElement`.thing, 'WebPageElement' FROM `webPageElement`
JOIN `thing` on `thing`.idthing = `webPageElement`.isPartOf;

UPDATE `thing`
  JOIN `webPageElement`ON `thing`.idthing = `webPageElement`.thing
  JOIN `webPageElement_has_imageObject` ON `href` is not null AND `href`<>'' AND `webPageElement_has_imageObject`.idwebPageElement=webPageElement.idwebPageElement
SET thing.sameAs = webPageElement_has_imageObject.href;

-- IMAGES
CALL set_image_in_thing('webPageElement');

-- insert images
CALL insert_thing_has_thing('webPageElement','imageObject');

-- ALTER TABLE
ALTER TABLE `webPageElement`
  CHANGE COLUMN `creativeWork` `creativeWork` INT UNSIGNED NOT NULL,
  CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
  DROP COLUMN `author`,
  DROP COLUMN `name`,
  DROP COLUMN `dateCreated`,
  DROP COLUMN `dateModified`,
  DROP COLUMN `gallery`,
  DROP COLUMN `isPartOf`,
  DROP COLUMN `text`,
  DROP COLUMN `position`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idwebPageElement`,`creativeWork`,`thing`);

-- drop old relationship
DROP TABLE `webPageElement_has_imageObject`;
DROP TABLE `webPageElement_has_propertyValue`;

-- add foreign key
ALTER TABLE `webPageElement`
  ADD KEY `fk_webPageElement_thing_idx` (`thing`),
  ADD KEY `fk_webPageElement_creativeWork_idx` (`creativeWork`),
  ADD CONSTRAINT `fk_webPageElement_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_webPageElement_creativeWork` FOREIGN KEY (`creativeWork`) REFERENCES `creativeWork` (`idcreativeWork`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- PLACE
--

ALTER TABLE `postalAddress`
  CHANGE COLUMN `idpostalAddress` `idpostalAddress` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idpostalAddress`);

-- GEO COORDINATES
CREATE TABLE IF NOT EXISTS `geoCoordinates` (
  `idgeoCoordinates` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `address` INT UNSIGNED NULL DEFAULT NULL,
  `elevation` VARCHAR(125) NULL DEFAULT NULL,
  `latitude` DECIMAL(18,14) NULL DEFAULT NULL,
  `longitude` DECIMAL(18,14) NULL DEFAULT NULL,
  PRIMARY KEY (`idgeoCoordinates`),
  INDEX `fk_geoCoordinates_postalAddress_idx` (`address`),
  CONSTRAINT `fk_geoCoordinates_postalAddress` FOREIGN KEY (`address`) REFERENCES `postalAddress` (`idpostalAddress`) ON DELETE SET NULL
) ENGINE = InnoDB;

-- alter table
ALTER TABLE `place`
  CHANGE COLUMN `idplace` `idplace` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ADD COLUMN `geo` INT UNSIGNED DEFAULT NULL AFTER `idplace`,
  ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idplace`,
  ADD COLUMN `publicAccess` TINYINT DEFAULT 0,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idplace`);

-- INSERT THING
ALTER TABLE `thing` ADD COLUMN `idplace` INT UNSIGNED DEFAULT NULL;
  -- insert thing
INSERT INTO `thing` (`idplace`,`name`,`additionalType`,`description`,`disambiguatingDescription`,`url`,`dateRegistered`,`lastModified`,`type`)
  SELECT `idplace`,
    IF (`name` <> '', `name`, 'Undefined name'),
    `additionalType`,
    CONCAT(description,' ', disambiguatingDescription),
    SUBSTRING(REGEXP_REPLACE(disambiguatingDescription, '<[^>]*>+', ''),1,255) as disambiguatingDescription,
    `url`,
    `dateCreated`,
    `dateModified`,
    'Place'
  FROM `place`;
-- set thing
UPDATE `place` JOIN `thing` ON thing.idplace = place.idplace SET place.thing = thing.idthing;
-- drop column
ALTER TABLE `thing` DROP COLUMN `idplace`;

-- insert geo coordinates
ALTER TABLE `geoCoordinates` ADD COLUMN `idplace` INT UNSIGNED DEFAULT NULL;
INSERT INTO `geoCoordinates` (`idplace`,`address`,`elevation`,`latitude`,`longitude`)
  SELECT `idplace`,`address`,`elevation`,`latitude`,`longitude` FROM `place`;

UPDATE `place`
  JOIN `geoCoordinates` ON geoCoordinates.idplace = place.idplace
  SET place.geo = geoCoordinates.idgeoCoordinates;

ALTER TABLE `geoCoordinates` DROP COLUMN `idplace`;

-- insert images
CALL insert_thing_has_thing('place','imageObject');

-- IMAGES
CALL set_image_in_thing('place');

-- alter table
ALTER TABLE `place`
  CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
  DROP COLUMN `name`,
  DROP COLUMN `additionalType`,
  DROP COLUMN `description`,
  DROP COLUMN `disambiguatingDescription`,
  DROP COLUMN `url`,
  DROP COLUMN `dateCreated`,
  DROP COLUMN `dateModified`,
  DROP COLUMN `rank`,
  DROP COLUMN `address`,
  DROP COLUMN `elevation`,
  DROP COLUMN `longitude`,
  DROP COLUMN `latitude`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idplace`,`thing`);

DROP TABLE `place_has_imageObject`;

-- PLACE
ALTER TABLE `place`
  ADD KEY `fk_place_thing_idx` (`thing`),
  ADD KEY `fk_place_geo_idx` (`geo`),
  ADD CONSTRAINT `fk_place_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_place_geo` FOREIGN KEY (`geo`) REFERENCES `geoCoordinates` (`idgeoCoordinates`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- EVENT
--

UPDATE `event` SET `superEvent`=null WHERE `superEvent`=0;

-- alter table
ALTER TABLE `event`
  DROP COLUMN `additionalType`,
  CHANGE COLUMN `idevent` `idevent` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  CHANGE COLUMN `location` `location` INT UNSIGNED DEFAULT NULL,
  CHANGE COLUMN `superEvent` `superEvent` INT UNSIGNED DEFAULT NULL,
  CHANGE COLUMN `organizerId` `organizer` VARCHAR(255) DEFAULT NULL,
  CHANGE COLUMN `directed` `director` VARCHAR(255) DEFAULT NULL,
  ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idevent`,
  ADD COLUMN `about` INT UNSIGNED DEFAULT NULL AFTER `idevent`,
  ADD COLUMN `keywords` VARCHAR(255) DEFAULT NULL AFTER `idevent`,
  ADD COLUMN `subEvent` INT UNSIGNED DEFAULT NULL AFTER `idevent`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idevent`);

-- INSERT THING
ALTER TABLE `thing` ADD COLUMN `idevent` INT UNSIGNED DEFAULT NULL;

-- insere dados em thing
INSERT INTO `thing` (`idevent`,`name`,`description`,`lastModified`,`dateRegistered`,`type`)
  SELECT
    `idevent`,
    `name`,
    `description`,
    `dateModified`,
    `dateCreated`,
    'Event'
  FROM `event` WHERE `name` <> '';

-- atualiza tabela
UPDATE `event`
  JOIN `thing` ON `event`.idevent = thing.idevent
  SET event.thing = thing.idthing
  WHERE event.`name` <> '';

-- drop thing column
ALTER TABLE `thing` DROP COLUMN `idevent`;

-- set location null if idplace not exists
UPDATE `event`
SET `event`.location = NULL
WHERE `event`.location NOT IN (SELECT idplace FROM place);

-- muda o location de idplace para idthing de place
UPDATE `event`
  JOIN `place` ON place.idplace=event.location
  SET event.location=place.thing
  WHERE event.location IS NOT NULL;

-- insert images
CALL insert_thing_has_thing('event','imageObject');

-- IMAGES
CALL set_image_in_thing('event');

-- insert thing_has_thing event_has_event
INSERT INTO `thing_has_thing` (idHasPart, typeHasPart, idIsPartOf, typeIsPartOf)
  SELECT t1.thing, 'Event', t2.thing, 'Event' FROM `event_has_event`
    JOIN `event` AS t1 ON t1.idevent=idHasPart
    JOIN `event` AS t2 ON t2.idevent=idIsPartOf;

-- alter table
ALTER TABLE `event`
  CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
  DROP COLUMN `name`,
  DROP COLUMN `description`,
  DROP COLUMN `src`,
  DROP COLUMN `organizerType`,
  DROP COLUMN `place`,
  DROP COLUMN `schedule`,
  DROP COLUMN `link_directed`,
  DROP COLUMN `dateCreated`,
  DROP COLUMN `dateModified`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idevent`,`thing`);

-- drop relational tables
DROP TABLE `event_has_event`;
DROP TABLE `event_has_imageObject`;

-- add foreign keys
ALTER TABLE `event`
  ADD KEY `fk_event_thing_idx` (`thing`),
  ADD KEY `fk_event_location_idx` (`location`),
  ADD KEY `fk_event_subEvent_idx` (`subEvent`),
  ADD KEY `fk_event_superEvent_idx` (`superEvent`),
  ADD CONSTRAINT `fk_event_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_event_location` FOREIGN KEY (`location`) REFERENCES `place` (`thing`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_event_subEvent` FOREIGN KEY (`subEvent`) REFERENCES `event` (`idevent`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_event_superEvent` FOREIGN KEY (`superEvent`) REFERENCES `event` (`idevent`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- ORGANIZATION
--

ALTER TABLE `organization`
  CHANGE COLUMN `idorganization` `idorganization` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  CHANGE COLUMN `areaServed` `areaServed` INT UNSIGNED DEFAULT NULL,
  CHANGE COLUMN `location` `location` INT UNSIGNED DEFAULT NULL,
  ADD COLUMN `thing` INT UNSIGNED NOT NULL AFTER `idorganization`,
  ADD COLUMN `logo` INT UNSIGNED DEFAULT NULL AFTER `idorganization`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idorganization`);

-- INSERT THING
ALTER TABLE `thing` ADD COLUMN `idorganization` INT UNSIGNED DEFAULT NULL;

-- insert thing
INSERT INTO `thing` (`idorganization`,`name`,`additionalType`,`description`,`disambiguatingDescription`,`url`,`dateRegistered`,`lastModified`,`type`)
SELECT `idorganization`,
   IF (`name` <> '', `name`, 'Undefined name'),
   `additionalType`,
   description,
   SUBSTRING(REGEXP_REPLACE(disambiguatingDescription, '<[^>]*>+', ''),1,255) as disambiguatingDescription,
   `url`,
   if(`dateCreated` IS NULL, CURDATE(), `dateCreated`),
   `dateModified`,
   'Organization'
FROM `organization`;

-- set thing
UPDATE `organization`
  JOIN `thing` ON thing.idorganization = organization.idorganization
  SET organization.thing = thing.idthing
WHERE thing.name <> '';

-- drop column
ALTER TABLE `thing` DROP COLUMN `idorganization`;

-- set location null if idplace not exists
UPDATE `organization`
  SET `organization`.location = NULL
  WHERE `organization`.location NOT IN (SELECT idplace FROM place);

-- muda o location de idplace para idthing de place
UPDATE `organization`
  JOIN `place` ON place.idplace=organization.location
SET organization.location=place.thing
WHERE organization.location IS NOT NULL ;

-- has contact point
INSERT INTO `thing_has_thing` (idHasPart, typeHasPart, idIsPartOf, typeIsPartOf)
SELECT `organization`.thing, 'Organization', `contactPoint`.thing, 'ContactPoint' FROM `organization_has_contactPoint`
  JOIN `organization` ON `organization`.idorganization = `organization_has_contactPoint`.idorganization
  JOIN `contactPoint` ON `contactPoint`.idcontactPoint = `organization_has_contactPoint`.idcontactPoint;

-- insert images
CALL insert_thing_has_thing('organization','imageObject');

-- IMAGES
CALL set_image_in_thing('organization');

-- has person
INSERT INTO `thing_has_thing` (idHasPart, typeHasPart, idIsPartOf, typeIsPartOf, caption)
SELECT `organization`.thing,'organization',`person`.thing,'Person',jobTitle FROM `organization_has_person`
  JOIN `organization` ON `organization`.idorganization = `organization_has_person`.idorganization
  JOIN `person` ON `person`.idperson = `organization_has_person`.idperson;


ALTER TABLE `organization`
  CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
  DROP COLUMN `name`,
  DROP COLUMN `additionalType`,
  DROP COLUMN `description`,
  DROP COLUMN `disambiguatingDescription`,
  DROP COLUMN `url`,
  DROP COLUMN `dateCreated`,
  DROP COLUMN `dateModified`,
  DROP PRIMARY KEY ,
  ADD PRIMARY KEY (`idorganization`,`thing`);

DROP TABLE `organization_has_contactPoint`;
DROP TABLE `organization_has_imageObject`;
DROP TABLE `organization_has_person`;

-- add foreign keys
ALTER TABLE `organization`
  ADD KEY `fk_organization_thing_idx` (`thing`),
  ADD KEY `fk_organization_location_idx` (`location`),
  ADD CONSTRAINT `fk_organization_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_organization_location` FOREIGN KEY (`location`) REFERENCES `place` (`thing`) ON DELETE CASCADE ON UPDATE NO ACTION;
-- PRODUCT
-- ALTER TABLE
ALTER TABLE `product`
  CHANGE COLUMN `idproduct` `idproduct` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  CHANGE COLUMN `manufacturer` `manufacturer` INT UNSIGNED DEFAULT NULL,
  ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idproduct`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idproduct`);

-- INSERT THING
ALTER TABLE `thing` ADD COLUMN `idproduct` INT UNSIGNED DEFAULT NULL;
-- insert thing
INSERT INTO `thing` (`idproduct`,`name`,`additionalType`,`description`,`disambiguatingDescription`,`dateRegistered`,`lastModified`,`type`)
  SELECT `idproduct`,
    IF (`name` <> '', `name`, 'Undefined name'),
    `additionalType`,
    description,
    SUBSTRING(REGEXP_REPLACE(disambiguatingDescription, '<[^>]*>+', ''),1,255) as disambiguatingDescription,
    if(`dateCreated`, CURDATE(), `dateCreated`),
    `dateModified`,
    'Product'
  FROM `product`;
-- set thing
UPDATE `product` JOIN `thing` ON thing.idproduct = product.idproduct SET product.thing = thing.idthing;
-- drop column
ALTER TABLE `thing` DROP COLUMN `idproduct`;

-- insert images
CALL insert_thing_has_thing('product','imageObject');

-- IMAGES
CALL set_image_in_thing('product');

ALTER TABLE `product`
  CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
  DROP COLUMN `name`,
  DROP COLUMN `additionalType`,
  DROP COLUMN `description`,
  DROP COLUMN `disambiguatingDescription`,
  DROP COLUMN `dateCreated`,
  DROP COLUMN `dateModified`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idproduct`,`thing`);

DROP TABLE `product_has_imageObject`;
DROP TABLE `product_has_offer`;

-- add foreign keys
ALTER TABLE `product`
  ADD KEY `fk_product_thing_idx` (`thing`),
  ADD KEY `fk_product_manufacturer_idx` (`manufacturer`),
  ADD CONSTRAINT `fk_product_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_product_manufacturer` FOREIGN KEY (`manufacturer`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION;
-- TAXON
-- ALTER TABLE
ALTER TABLE `taxon`
  CHANGE COLUMN `idtaxon` `idtaxon` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idtaxon`,
  DROP PRIMARY KEY ,
  ADD PRIMARY KEY (`idtaxon`);

-- INSERT THING
ALTER TABLE `thing` ADD COLUMN `idtaxon` INT UNSIGNED DEFAULT NULL;
-- insert thing
INSERT INTO `thing` (`idtaxon`,`name`,`alternateName`,`description`,`url`,`dateRegistered`,`lastModified`,`type`)
  SELECT `idtaxon`,
    IF (`name` <> '', `name`, 'Undefined name'),
    `vernacularName`,
    description,
    `url`,
    if(`dateModified` IS NULL, CURDATE(), `dateModified`),
    `dateModified`,
    'Taxon'
  FROM `taxon`;
-- set thing
UPDATE `taxon` JOIN `thing` ON thing.idtaxon = taxon.idtaxon SET taxon.thing = thing.idthing;
-- drop column
ALTER TABLE `thing` DROP COLUMN `idtaxon`;

-- insert images
CALL insert_thing_has_thing('taxon','imageObject');

-- IMAGES
CALL set_image_in_thing('taxon');

-- alter table
ALTER TABLE `taxon`
  CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
  DROP COLUMN `name`,
  DROP COLUMN `description`,
  DROP COLUMN `url`,
  DROP COLUMN `dateModified`,
  DROP PRIMARY KEY ,
  ADD PRIMARY KEY (`idtaxon`,`thing`)
;

DROP TABLE `taxon_has_imageObject`;

-- add foreign key
ALTER TABLE `taxon`
  ADD KEY `fk_taxon_thing_idx` (`thing`),
  ADD CONSTRAINT `fk_taxon_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- LOCAL BUSINESS
--

ALTER TABLE `localBusiness`
  CHANGE COLUMN `idlocalBusiness` `idlocalBusiness` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  CHANGE COLUMN `organization` `organization` INT UNSIGNED DEFAULT NULL,
  CHANGE COLUMN `location` `location` INT UNSIGNED DEFAULT NULL,
  CHANGE COLUMN `additionalType` `additionalType` VARCHAR(255) DEFAULT NULL,
  ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idlocalBusiness`,
  ADD COLUMN`openingHours` VARCHAR(255) DEFAULT NULL,
  ADD COLUMN `paymentAccepted` VARCHAR(255) DEFAULT NULL,
  DROP PRIMARY KEY ,
  ADD PRIMARY KEY (`idlocalBusiness`);

-- CREATE THING

ALTER TABLE `thing` ADD COLUMN `idlocalBusiness` INT UNSIGNED DEFAULT NULL;

-- insert thing
INSERT INTO `thing` (`idlocalBusiness`,`name`,`additionalType`,`description`,`disambiguatingDescription`,`url`,`dateRegistered`,`lastModified`,`type`)
SELECT
  `idlocalBusiness`,
  `name`,
  CONCAT('schema:LocalBusiness,',`additionalType`),
   description,
   SUBSTRING(REGEXP_REPLACE(disambiguatingDescription, '<[^>]*>+', ''),1,255) as disambiguatingDescription,
   `url`,
   `dateCreated`,
   `dateModified`,
   'Organization'
FROM `localBusiness`;

-- update this
UPDATE `localBusiness`
  JOIN `thing` ON thing.idlocalBusiness = localBusiness.idlocalBusiness
  SET localBusiness.thing = thing.idthing
WHERE thing.name <> '';

-- drop thing column
ALTER TABLE `thing` DROP COLUMN `idlocalBusiness`;

-- END CREATE THING

-- set location null if idplace not exists
UPDATE `localBusiness`
SET `localBusiness`.location = NULL
WHERE `localBusiness`.location NOT IN (SELECT idplace FROM place);

-- muda o location de idplace para idthing de place
UPDATE `localBusiness`
  JOIN `place` ON place.idplace=localBusiness.location
  SET localBusiness.location=place.thing
WHERE localBusiness.location IS NOT NULL;

-- insert organization
INSERT INTO `organization` (`thing`,`address`,`hasOfferCatalog`,`location`)
SELECT `thing`, `address`, `hasOfferCatalog`, `location` FROM `localBusiness`;

-- has contact point
INSERT INTO `thing_has_thing` (idHasPart, typeHasPart, idIsPartOf, typeIsPartOf)
  SELECT `localBusiness`.thing, 'LocalBusiness', `contactPoint`.thing, 'ContactPoint' FROM `localBusiness_has_contactPoint`
  JOIN `localBusiness` ON `localBusiness`.idlocalBusiness = `localBusiness_has_contactPoint`.idlocalBusiness
  JOIN `contactPoint` ON `contactPoint`.idcontactPoint = `localBusiness_has_contactPoint`.idcontactPoint;

-- insert images
CALL insert_thing_has_thing('localBusiness','imageObject');

-- IMAGES
CALL set_image_in_thing('localBusiness');

-- has person
INSERT INTO `thing_has_thing` (idHasPart, typeHasPart, idIsPartOf, typeIsPartOf, caption, position)
SELECT `localBusiness`.thing,'LocalBusiness',`person`.thing,'Person',jobTitle,position FROM `localBusiness_has_person`
  JOIN `localBusiness` ON `localBusiness`.idlocalBusiness = `localBusiness_has_person`.idlocalBusiness
  JOIN `person` ON `person`.idperson = `localBusiness_has_person`.idperson;


ALTER TABLE `localBusiness`
  CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
  DROP COLUMN `name`,
  DROP COLUMN `additionalType`,
  DROP COLUMN `description`,
  DROP COLUMN `disambiguatingDescription`,
  DROP COLUMN `url`,
  DROP COLUMN `hasOfferCatalog`,
  DROP COLUMN `address`,
  DROP COLUMN `dateCreated`,
  DROP COLUMN `dateModified`,
  DROP COLUMN `location`,
  DROP COLUMN `organization`,
  DROP COLUMN `rank`,
  DROP PRIMARY KEY ,
  ADD PRIMARY KEY (`idlocalBusiness`,`thing`);

DROP TABLE `localBusiness_has_contactPoint`;
DROP TABLE `localBusiness_has_imageObject`;
DROP TABLE `localBusiness_has_person`;

-- add foreign keys
ALTER TABLE `localBusiness`
  ADD KEY `fk_localBusiness_thing_idx` (`thing`),
  ADD CONSTRAINT `fk_localBusiness_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION;
-- SERVICE
-- alter table
ALTER TABLE `service`
  CHANGE COLUMN `idservice` `idservice` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  CHANGE COLUMN `provider` `provider` INT UNSIGNED NOT NULL,
  ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idservice`,
  ADD COLUMN `isRelatedTo` INT UNSIGNED DEFAULT NULL,
  ADD COLUMN `serviceOutput` INT UNSIGNED DEFAULT NULL,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idservice`);

-- INSERT THING
ALTER TABLE `thing` ADD COLUMN `idservice` INT UNSIGNED DEFAULT NULL;
-- insert thing
INSERT INTO `thing` (`idservice`,`name`,`additionalType`,`description`,`disambiguatingDescription`,`dateRegistered`,`lastModified`,`type`)
  SELECT `idservice`,
       IF (`name` <> '', `name`, 'Undefined name'),
       `additionalType`,
       description,
       SUBSTRING(REGEXP_REPLACE(disambiguatingDescription, '<[^>]*>+', ''),1,255) as disambiguatingDescription,
       if(`dateCreated`, `dateCreated`, CURDATE()),
       `dateModified`,
       'Service'
  FROM `service`;
-- set thing
UPDATE `service` JOIN `thing` ON thing.idservice = service.idservice SET service.thing = thing.idthing;
-- drop column
ALTER TABLE `thing` DROP COLUMN `idservice`;

UPDATE `service`
  LEFT JOIN `organization` ON `organization`.idorganization = `service`.provider AND `providerType` = 'organization'
  LEFT JOIN `person` ON `person`.idperson = `service`.provider AND `providerType` = 'person'
SET `service`.provider = IF(`providerType`='organization', IF(`organization`.thing IS NOT NULL, `organization`.thing, `service`.provider),IF(`person`.thing IS NOT NULL,`person`.thing, `service`.provider));

-- insert images
CALL insert_thing_has_thing('service','imageObject');

-- IMAGES
CALL set_image_in_thing('service');

ALTER TABLE `service`
  CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
  DROP COLUMN `name`,
  DROP COLUMN `additionalType`,
  DROP COLUMN `description`,
  DROP COLUMN `disambiguatingDescription`,
  DROP COLUMN `dateCreated`,
  DROP COLUMN `dateModified`,
  DROP COLUMN `providerType`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idservice`,`thing`);

DROP TABLE `service_has_imageObject`;

-- add foreign keys
ALTER TABLE `service`
  ADD KEY `fk_service_thing_idx` (`thing`),
  ADD KEY `fk_service_provider_idx` (`provider`),
  ADD CONSTRAINT `fk_service_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_service_provider` FOREIGN KEY (`provider`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- ORDER
--

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

ALTER TABLE `order`
  DROP COLUMN `customerType`,
  DROP COLUMN `sellerType`,
  DROP COLUMN `tipo`,
  DROP COLUMN `valor`,
  DROP COLUMN `dateCreated`,
  DROP COLUMN `dateModified`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idorder`,`customer`,`seller`);

-- add foreign keys
ALTER TABLE `order`
  ADD KEY `fk_order_customer_idx` (`customer`),
  ADD KEY `fk_order_seller_idx` (`seller`),
  ADD CONSTRAINT `fk_order_customer` FOREIGN KEY (`customer`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_order_seller` FOREIGN KEY (`seller`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- ORDER ITEM
--

ALTER TABLE `orderItem`
  CHANGE COLUMN `idorderItem` `idorderItem` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  CHANGE COLUMN `offer` `offer` INT UNSIGNED NOT NULL,
  CHANGE COLUMN `referencesOrder` `orderItemNumber` INT UNSIGNED NOT NULL,
  CHANGE COLUMN `orderedItem` `orderedItem` INT UNSIGNED NOT NULL,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idorderItem`);

UPDATE `orderItem`
  LEFT JOIN `service` ON `orderItem`.orderedItem = `service`.idservice AND `orderItem`.orderedItemType='service'
  LEFT JOIN `product` ON `product`.idproduct = `orderItem`.orderedItem AND `orderItem`.orderedItemType='product'
SET `orderItem`.orderedItem= IF(orderedItemType='service',service.thing,product.thing);

DELETE `orderItem` FROM `orderItem`
  LEFT JOIN `order` ON `orderItem`.orderItemNumber = `order`.idorder
WHERE `order`.idorder IS NULL;

DELETE FROM `orderItem` WHERE `offer` is null OR `offer`=0;

ALTER TABLE `orderItem`
  DROP COLUMN `orderedItemType`,
  DROP COLUMN `orderItemStatus`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idorderItem`,`orderItemNumber`);

-- add foreign keys
ALTER TABLE `orderItem`
  ADD KEY `fk_orderedItem_thing_idx` (`orderedItem`),
  ADD KEY `fk_orderItemNumber_thing_idx` (`orderItemNumber`),
  ADD CONSTRAINT `fk_orderedItem_thing` FOREIGN KEY (`orderedItem`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_orderItemNumber_thing` FOREIGN KEY (`orderItemNumber`) REFERENCES `order` (`idorder`) ON DELETE CASCADE ON UPDATE NO ACTION;
-- offer
ALTER TABLE `offer`
  CHANGE COLUMN `idoffer` `idoffer` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  CHANGE COLUMN `itemOffered` `itemOffered` INT UNSIGNED NOT NULL,
  CHANGE COLUMN `offeredBy` `offeredBy` INT UNSIGNED NOT NULL,
  CHANGE COLUMN `elegibleQuantity` `eligibleQuantity` VARCHAR(45) NULL DEFAULT NULL,
  CHANGE COLUMN `elegibleDuration` `eligibleDuration` VARCHAR(45) NULL DEFAULT NULL,
  ADD COLUMN `thing` INT UNSIGNED DEFAULT NULL AFTER `idoffer`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idoffer`);

UPDATE `offer`
  left join `service` on service.idservice=offer.itemOffered and `offer`.itemOfferedType='service'
  left join `product` on product.idproduct=offer.itemOffered and `offer`.itemOfferedType='product'
  left join `organization`ON `organization`.idorganization=`offer`.offeredBy AND `offer`.offeredByType='organization'
SET `offer`.itemOffered = IF(`offer`.itemOfferedType='service', service.thing, product.thing), `offer`.offeredBy = `organization`.thing;

DELETE FROM `offer` WHERE `itemOffered`='0' || `offeredBy`='0';

-- INSERT THING
ALTER TABLE `thing` ADD COLUMN `idoffer` INT UNSIGNED DEFAULT NULL;
-- insert thing
INSERT INTO `thing` (`idoffer`,`name`,`additionalType`,`description`,`disambiguatingDescription`,`type`)
SELECT `offer`.idoffer,
       IF (`name` <> '', `name`, 'Undefined name'),
       `additionalType`,
       description,
       SUBSTRING(REGEXP_REPLACE(disambiguatingDescription, '<[^>]*>+', ''),1,255) as disambiguatingDescription,
       'Offer'
FROM `offer` LEFT JOIN `thing` ON `thing`.idthing=`offer`.itemOffered;
-- set thing
UPDATE `offer` JOIN `thing` ON thing.idoffer = `offer`.idoffer SET `offer`.thing = `thing`.idthing;
-- drop column
ALTER TABLE `thing` DROP COLUMN `idoffer`;

ALTER TABLE `offer`
  CHANGE COLUMN `thing` `thing` INT UNSIGNED NOT NULL,
  DROP COLUMN `itemOfferedType`,
  DROP COLUMN `offeredByType`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idoffer`,`itemOffered`,`offeredBy`);

-- add foreign keys
ALTER TABLE `offer`
  ADD KEY `fk_offer_thing_idx` (`thing`),
  ADD KEY `fk_offer_itemOffered_thing_idx` (`itemOffered`),
  ADD KEY `fk_offer_offeredBy_thing_idx` (`offeredBy`),
  ADD CONSTRAINT `fk_offer_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_offer_itemOffered_thing` FOREIGN KEY (`itemOffered`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_offer_offeredBy_thing` FOREIGN KEY (`offeredBy`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION;

-- add foreign keys orderItem
ALTER TABLE `orderItem`
  ADD KEY `fk_orderItem_offer_idx` (`offer`),
  ADD CONSTRAINT `fk_orderedItem_offer` FOREIGN KEY (`offer`) REFERENCES `offer` (`idoffer`) ON DELETE CASCADE ON UPDATE NO ACTION;
-- ALTER TABLE
ALTER TABLE `invoice`
  CHANGE COLUMN `idinvoice` `idinvoice` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  CHANGE COLUMN `paymentDueDate` `scheduledPaymentDate` DATE NOT NULL,
  CHANGE COLUMN `paymentDate` `paymentDueDate` DATE DEFAULT NULL,
  CHANGE COLUMN `referencesOrder` `referencesOrder` INT UNSIGNED NOT NULL ,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`idinvoice`,`referencesOrder`);

DELETE `invoice` FROM invoice
  LEFT JOIN `order` ON `order`.idorder = `invoice`.referencesOrder
WHERE `order`.idorder IS NULL ;

UPDATE `invoice`
  JOIN `order` ON `order`.idorder = `invoice`.referencesOrder
SET `invoice`.customer=`order`.customer, `invoice`.provider = `order`.seller;

ALTER TABLE `invoice`
  CHANGE COLUMN `customer` `customer` INT UNSIGNED NOT NULL ,
  CHANGE COLUMN `provider` `provider` INT UNSIGNED NOT NULL ,
  DROP COLUMN `customerType`,
  DROP COLUMN `providerType`;

-- add foreign key
ALTER TABLE `invoice`
  ADD KEY `fk_invoice_order_idx` (`referencesOrder`),
  ADD CONSTRAINT `fk_invoice_order` FOREIGN KEY (`referencesOrder`) REFERENCES `order` (`idorder`) ON DELETE CASCADE ON UPDATE NO ACTION;

