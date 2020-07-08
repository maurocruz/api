<?php

namespace Fwc\Api\Type;

class Advertising extends TypeAbstract implements TypeInterface
{
    protected $table = "advertising";
    
    protected $type = "Advertising";
    
    protected $properties = [ "customer","tipo","valor","data","vencimento","status" ];
    
    protected $withTypes = [ "customer" => "LocalBusiness", "history" => "History" ];

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
        return parent::createSqlTable("Advertising");
    }
}