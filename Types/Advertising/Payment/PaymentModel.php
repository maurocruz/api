<?php
namespace fwc\Thing;

/**
 * PaymentModel
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

use fwc\Api\Crud;

class PaymentModel extends Crud {   
    public $table = "formpg";
            
    public function getPaymentByIdpayment($idpayment) {
        $query = "SELECT * FROM $this->table WHERE idformpg=$idpayment;";
        return parent::getQuery($query);
    }
    
    public function getAll($order = null) {
        $query = "SELECT * FROM $this->table, advertising";
        $query .= " WHERE advertising.status=1 AND quitado is NULL AND advertising.idadvertising=formpg.idcontratos";
        $query .= $order ? " ORDER BY $order" : null;
        $query .= ";";
        return parent::getQuery($query);
    }
    
    public function getPaymentsByIdContract($idcontract){
        $query = "SELECT * FROM $this->table WHERE idcontratos=$idcontract ORDER BY vencimentoparc DESC";
        return parent::getQuery($query);
    }
    
    public function getSimpleList($order = null, $date = null) {
        $query = "SELECT *, (SELECT COUNT(*) FROM formpg WHERE formpg.idcontratos=advertising.idadvertising) as number_parc FROM formpg, advertising, localBusiness, contratostipos WHERE formpg.quitado is null AND formpg.idcontratos=advertising.idadvertising and advertising.status=1 AND advertising.idlocalBusiness=localBusiness.idlocalBusiness AND advertising.tipo=contratostipos.idcontratostipo";
        $query .= $date ? " AND formpg.vencimentoparc <= '$date'" : null;
        $query .= $order ? " ORDER BY vencimentoparc ASC" : null;
        $query .= ";";
        return parent::getQuery($query);
    }
}
