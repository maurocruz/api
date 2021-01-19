--
-- CREATE TABLE invoice
--

CREATE TABLE IF NOT EXISTS `invoice` (
  `idinvoice` INT NOT NULL AUTO_INCREMENT,
  `referencesOrder` INT NOT NULL,
  `totalPaymentDue` FLOAT NULL,
  `paymentDueDate` DATE NULL,
  `paymentDate` DATE NULL,
  `paymentDate` DATE NULL,
  `paymentStatus` VARCHAR(45) NULL,
  PRIMARY KEY (`idinvoice`)
) ENGINE = InnoDB;
