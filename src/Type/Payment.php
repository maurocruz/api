<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Auth\SessionUser;
use ReflectionException;

class Payment extends Entity implements TypeInterface
{
    protected $table = "payment";
    
    protected $type = "Payment";
    
    protected $properties = [ "*" ];
    
    protected $hasTypes = [ "referencesOrder" => "Order" ];

    /**
     * GET
     * @param array $params
     * @return array
     */
    public function get(array $params): array 
    {
        if (array_key_exists('orderBy',$params) === false) {
            $params['orderBy'] = "paymentDate";
        }
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
     * @param array $params
     * @return array
     */
    public function put(array $params): array
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
        
        return parent::delete([ "idpayment" => $params['id'] ]);
    }

    /**
     * CREATE SQL
     * @param string|null $type
     * @return array|string[]
     * @throws ReflectionException
     */
    public function createSqlTable($type = null): array
    {        
        return parent::createSqlTable("Payment");
    }

    /**
     * SET HISTORY
     * @param string $action
     * @param array $params
     * @return array
     */
    private static function setHistory(string $action, array $params): array
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
