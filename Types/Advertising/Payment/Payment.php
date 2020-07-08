<?php

namespace Fwc\Api\Type;

class Payment extends TypeAbstract implements TypeInterface
{
    protected $table = "payment";
    
    protected $type = "Payment";
    
    protected $properties = [ "*" ];
    
    protected $withTypes = [  ];

    public function get(array $params): array 
    {
        return parent::get($params);
    }
    
    public function post(array $params): array 
    {
        return parent::post($params);
    }
    
    public function put(string $id, $params): array 
    {
        return parent::put($id, $params);
    }
    
    public function delete(string $id, $params): array 
    {
        return parent::delete($id, $params);
    }
    
    public function createSqlTable($type = null) 
    {        
        return parent::createSqlTable("Payment");
    }
}
