--
-- CREATE TABLE article
--

CREATE TABLE IF NOT EXISTS `article` (
`idarticle` int NOT NULL AUTO_INCREMENT,
`headline` varchar(255) DEFAULT NULL,
`articleBody` text,
`articleSection` varchar(255) DEFAULT NULL,
`datePublished` datetime DEFAULT NULL,
`publishied` tinyint(1) DEFAULT NULL,
`author` int DEFAULT NULL,
`publisher` int DEFAULT NULL,
`publisherType` varchar(45) DEFAULT NULL,
`position` int DEFAULT NULL,
`dateCreated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
`dateModified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`idarticle`),
KEY `autor` (`author`),
KEY `id` (`idarticle`),
KEY `idx_1` (`headline`)
) ENGINE=InnoDB;

