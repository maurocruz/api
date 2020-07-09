<?php

namespace Fwc\Api\Type;

class History extends TypeAbstract implements TypeInterface
{
    protected $table = "history";
    
    protected $type = "History";
    
    protected $properties = [ "*" ];
    
    protected $withTypes = [  ];

    public function get(array $params): array 
    {        
         if (isset($params['tableOwner']) && isset($params['idOwner'])) {
             return parent::getWithPartOf($params);
         }
        
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
    
    public function delete(array $params): array 
    {
        return parent::delete($params);
    }
    
    public function createSqlTable($type = null) 
    {        
        return parent::createSqlTable("History");
    }
}
