CREATE PROCEDURE set_image_in_thing(table_name VARCHAR(64))
BEGIN
  SET @table_has_part = CONCAT(table_name,'_has_imageObject');
  SET @id_name = CONCAT('id',table_name);
  SET @sql_text = CONCAT('UPDATE `thing`
      join ',table_name,' as tb1 ON tb1.thing = thing.idthing
      join (select * from ',@table_has_part,' where position = (select min(position) from ',@table_has_part,')) as tb_has on tb_has.',@id_name,' = tb1.',@id_name,'
      join imageObject ON imageObject.idimageObject = tb_has.idimageObject
      join mediaObject ON mediaObject.idmediaObject = imageObject.mediaObject
    SET `thing`.image = `mediaObject`.contentUrl');
  PREPARE stmt from @sql_text;
  EXECUTE stmt;
  DEALLOCATE PREPARE stmt;
END
