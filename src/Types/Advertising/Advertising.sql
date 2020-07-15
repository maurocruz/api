
--
-- CREATE TABLE advertising
--
-- Relationships
-- -- one to one
-- -- -- LocalBusiness
--

CREATE TABLE IF NOT EXISTS `advertising` (
  `idadvertising` int(10) NOT NULL,
  `customer` int(10) DEFAULT NULL,
  `tipo` tinyint(2) NOT NULL,
  `valor` decimal(20,2) DEFAULT NULL,
  `data` date DEFAULT NULL,
  `vencimento` date DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `status` int(1) DEFAULT '1',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idadvertising`),
  KEY `status` (`status`),
  KEY `tipo` (`tipo`),
  KEY `fk_advertising_1_idx` (`customer`),
  ADD CONSTRAINT `fk_advertising_localBusiness1` FOREIGN KEY (`customer`) REFERENCES `pirenopolis02`.`localBusiness` (`idlocalBusiness`) ON DELETE NO ACTION ON UPDATE NO ACTION;
) ENGINE=InnoDB;



