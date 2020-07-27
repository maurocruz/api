<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

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
        $idadvertising = $params['idadvertising'];
                        
        $paramsHistory["action"] = $action;
        $paramsHistory["summary"] = "Valor: ".number_format($params['valorparc'], 2, ",", ".")." / vencimento: ".$params['vencimentoparc']." / quitado: ".$params['quitado'];
        
        (new History())->setHistory("advertising", $idadvertising, $paramsHistory);
        
        return $params;
    }
}
