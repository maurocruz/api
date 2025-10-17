
INSERT INTO `thing_has_thing` (idHasPart, typeHasPart, idIsPartOf, typeIsPartOf)
SELECT thaspart.idthing, thaspart.type, tispartof.idthing, tispartof.type FROM creativeWork as cispartof
join creativeWork as chaspart on chaspart.idcreativeWork= cispartof.isPartOf
join thing as thaspart on thaspart.idthing=chaspart.thing
join thing as tispartof on tispartof.idthing= cispartof.thing
where cispartof.isPartOf is not null;

ALTER TABLE `creativeWork`
  DROP FOREIGN KEY `fk_creativeWork_isPartOf`;

ALTER TABLE `creativeWork`
  DROP COLUMN `isPartOf`,
  DROP INDEX `fk_creativeWork_isPartOf_idx` ;

DROP TABLE `thing_has_imageObject`;
