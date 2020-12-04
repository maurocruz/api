--
-- CREATE TABLE invoice
--

CREATE TABLE IF NOT EXISTS `invoice` (
  `idinvoice` INT NOT NULL AUTO_INCREMENT,
  `totalPaymentDue` FLOAT NULL,
  `paymentDueDate` DATETIME NULL,
  `paymentStatus` VARCHAR(45) NULL,
  PRIMARY KEY (`idinvoice`)
) ENGINE = InnoDB;
