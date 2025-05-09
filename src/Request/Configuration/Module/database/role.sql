--
-- Role
--
CREATE TABLE IF NOT EXISTS `role` (
  `idrole` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `thing` INT UNSIGNED NOT NULL,
  `person` INT UNSIGNED NOT NULL,
  `organization` INT UNSIGNED NOT NULL,
  `endDate` DATE,
  `startDate` DATE,
  `secundaryType` INT UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`idrole`,`thing`),
  INDEX `fk_role_thing_idx` (`thing`),
  INDEX `fk_role_person_idx` (`person`),
  INDEX `fk_role_organization_idx` (`organization`),
  CONSTRAINT `fk_role_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE,
  CONSTRAINT `fk_role_person` FOREIGN KEY (`person`) REFERENCES `person` (`idperson`) ON DELETE CASCADE,
  CONSTRAINT `fk_role_organization` FOREIGN KEY (`organization`) REFERENCES `organization` (`idorganization`) ON DELETE CASCADE
) ENGINE = InnoDB;