--
-- CREATE TABLE orderItem
--

CREATE TABLE `orderItem` (
     `idorderItem` int NOT NULL AUTO_INCREMENT,
     `orderItemNumber` int DEFAULT NULL,
     `orderedItem` int DEFAULT NULL,
     `orderedItemType` varchar(45) DEFAULT NULL,
     `orderQuantity` int DEFAULT '1',
     `orderItemStatus` varchar(45) DEFAULT NULL,
     PRIMARY KEY (`idorderItem`),
     KEY `fk_orderItem_order1_idx` (`orderItemNumber`),
     CONSTRAINT `fk_orderItem_order1` FOREIGN KEY (`orderItemNumber`) REFERENCES `order` (`idorder`) ON DELETE CASCADE
) ENGINE=InnoDB;
