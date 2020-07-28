<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Auth\SessionUser;

class Payment extends Entity implements TypeInterface
{
    protected $table = "payment";
    
    protected $type = "Payment";
    
    protected $properties = [ "*" ];
    
    protected $hasTypes = [  ];

    /**
     * GET
     * @param array $params
     * @return array
     */
    public function get(array $params): array 
    {
        return parent::get($params);
    }
    
    /**
     * POST
     * @param array $params
     * @return array
     */
    public function post(array $params): array 
    {
        $params = self::setHistory("CREATE", $params);
        
        return parent::post($params);
    }
    
    /**
     * PUT
     * @param string $id
     * @param type $params
     * @return array
     */
    public function put($params): array 
    {   
        $params = self::setHistory("UPDATE", $params);
        unset($params['tableHasPart']);
        unset($params['idHasPart']);
        unset($params['idadvertising']);
        
        return parent::put($params);
    }
    
    /**
     * DELETE
     * @param array $params
     * @return array
     */
    public function delete(array $params): array 
    {        
        $params = self::setHistory("DELETE", $params);
        
        return parent::delete([ "idpayment" => $params['idpayment'] ]);
    }
    
    /**
     * CREATE SQL
     * @param type $type
     * @return type
     */
    public function createSqlTable($type = null) 
    {        
        return parent::createSqlTable("Payment");
    }
    
    /**
     * SET HISTORY
     * @param type $action
     * @param type $params
     * @return type
     */
    private static function setHistory($action, $params) 
    {                        
        $paramsHistory["action"] = $action;
        $paramsHistory["summary"] = "Valor: ".number_format($params['valorparc'], 2, ",", ".")." / vencimento: ".$params['vencimentoparc']." / quitado: ".$params['quitado'];
        $paramsHistory['tableHasPart'] = $params['tableHasPart'];
        $paramsHistory['idHasPart'] = $params['idHasPart'];
        $paramsHistory['user'] = SessionUser::getName();
        
        (new History())->post($paramsHistory);
        
        return $params;
    }
}
