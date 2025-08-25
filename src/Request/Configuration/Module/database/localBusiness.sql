--
-- localBusiness
--
CREATE TABLE IF NOT EXISTS `localBusiness` (
  `idlocalBusiness` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `thing` int(10) unsigned NOT NULL,
  `organization` int(10) unsigned DEFAULT NULL,
  `location` int(10) unsigned DEFAULT NULL,
  `openingHours` VARCHAR(255) DEFAULT NULL,
  `paymentAccepted` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`idlocalBusiness`,`thing`),
  KEY `fk_localBusiness_thing_idx` (`thing`),
  KEY `fk_localBusiness_place_idx` (`location`),
  KEY `fk_localBusiness_organization_idx` (`organization`),
  CONSTRAINT `fk_localBusiness_organization` FOREIGN KEY (`organization`) REFERENCES `organization` (`idorganization`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_localBusiness_place` FOREIGN KEY (`location`) REFERENCES `place` (`idplace`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_localBusiness_thing` FOREIGN KEY (`thing`) REFERENCES `thing` (`idthing`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB;
