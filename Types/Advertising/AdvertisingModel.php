<?php
namespace fwc\Thing;
/**
 * AdvertsModel
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
use fwc\Api\Crud;

class AdvertisingModel extends Crud 
{
    public $table = "advertising";
    
    public function getAll($order = "vencimento DESC", $search = null, $limit = null, $offset = null) {
        $query = "SELECT * FROM $this->table";
        $query .= " LEFT JOIN contratostipos ON advertising.tipo=contratostipos.idcontratostipo";
        $query .= " ORDER BY $order";
        $query .= $limit ? " LIMIT $limit OFFSET $offset" : null;
        $query .= ";";
        return parent::getQuery($query);
    }

    public function getContractsByIdLocalBusines($idlocalBusiness, $order = "vencimento DESC") {
        $query = "SELECT * FROM $this->table";
        $query .= " LEFT JOIN contratostipos ON advertising.tipo=contratostipos.idcontratostipo";
        $query .= " WHERE idlocalBusiness=$idlocalBusiness";
        $query .= " ORDER BY $order";
        $query .= ";";
        return parent::getQuery($query);        
    }    
    
    public function getCustomerByIdContract($idcontract) {
        $query = "SELECT * FROM advertising";
        $query .= " LEFT JOIN contratostipos ON advertising.tipo=contratostipos.idcontratostipo";
        $query .= " WHERE idadvertising=$idcontract";
        $query .= ";";
        return parent::getQuery($query);
    }

    public function getAllActivedCustomer() {
        $query = "SELECT * FROM advertising WHERE status=1 AND (advertising.tipo=2 OR advertising.tipo=3 OR advertising.tipo=5) AND idlocalBusiness IS NOT NULL";
        $query .= ";";
        return parent::getQuery($query);
    } 
    
    public function getActivedCustomerById($idLocalBusiness) {
        $query = "SELECT * FROM advertising WHERE status=1 AND (advertising.tipo=2 OR advertising.tipo=3 OR advertising.tipo=5)";
        $query .= " AND idlocalBusiness='$idLocalBusiness'";
        $query .= ";";
        return parent::getQuery($query);
    }  
    
    public function getContratosByTags($url) {
        $query = "SELECT * FROM advertising WHERE status=1 AND (advertising.tipo=2 OR advertising.tipo=3 OR advertising.tipo=5) AND idlocalBusiness IS NOT NULL";        
        $tags = explode("/",$url);        
        foreach ($tags as $value) {
            $query .= " AND tags LIKE '%$value%'";
        }        
        $query .= ";";
        return parent::getQuery($query);
    }
    
    public function getContractNameByType($type) {
        $query = "SELECT contrato_name FROM contratostipos WHERE idcontratostipo=$type;";
        $data = parent::getQuery($query);
        return $data[0]['contrato_name'];
    }
    
    public function getallContractType() {
        $query = "SELECT * FROM contratostipos ORDER BY contrato_name ASC";
        return parent::getQuery($query);
    }
    
    public function getContractsExpired($dateLimit) {
        $query = "SELECT advertising.*, localBusiness.name, contratostipos.contrato_name FROM advertising, contratostipos, localBusiness WHERE advertising.status=1 AND contratostipos.idcontratostipo=advertising.tipo AND advertising.idlocalBusiness=localBusiness.idlocalBusiness";
        $query .= $dateLimit ? " AND advertising.vencimento < '$dateLimit'" : null;
        $query .= " ORDER BY vencimento ASC";
        $query .= ";";
        return parent::getQuery($query);
    }
}
