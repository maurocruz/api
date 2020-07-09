
ALTER TABLE `pirenopolis02`.`advertising` 
CHANGE COLUMN `idlocalBusiness` `customer` INT(10) NULL DEFAULT NULL ;

ALTER TABLE `pirenopolis02`.`advertising` 
CHANGE COLUMN `idadvertising` `idadvertising` INT(10) NOT NULL AUTO_INCREMENT  ;

ALTER TABLE `pirenopolis02`.`formpg` 
ENGINE = InnoDB ;

ALTER TABLE `pirenopolis02`.`formpg` 
CHANGE COLUMN `idformpg` `idpayment` INT(10) NOT NULL AUTO_INCREMENT ,
CHANGE COLUMN `idcontratos` `idadvertising` INT(10) NOT NULL , 
RENAME TO  `pirenopolis02`.`payment` ;

/*
SELECT * FROM payment LEFT JOIN advertising ON payment.idadvertising=advertising.idadvertising
WHERE advertising.idadvertising is null order by create_time;

ALTER TABLE `pirenopolis02`.`payment` 
ADD CONSTRAINT `fk_payment_advertising1` FOREIGN KEY (`idadvertising`) REFERENCES `pirenopolis02`.`advertising` (`idadvertising`) ON DELETE CASCADE ON UPDATE NO ACTION;*/

ALTER TABLE `pirenopolis02`.`advertising_has_history` 
ADD CONSTRAINT `fk_advertising_has_history_2`
  FOREIGN KEY (`idadvertising`)
  REFERENCES `pirenopolis02`.`advertising` (`idadvertising`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;
