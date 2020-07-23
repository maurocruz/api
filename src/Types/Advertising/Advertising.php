<?php

namespace Plinct\Api\Type;

class Advertising extends TypeAbstract implements TypeInterface
{
    protected $table = "advertising";
    
    protected $type = "Advertising";
    
    protected $properties = [ "customer","tipo","valor","data","vencimento","status","tags" ];
    
    protected $hasTypes = [ "customer" => "LocalBusiness", "history" => "History" ];

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
        $summary = filter_input(INPUT_GET, "summaryHistory");
        
        if ($summary) {
            $paramsHistory["action"] = "UPDATE";
            $paramsHistory["summary"] = $summary == "" ? "ND" : $summary;
            
            (new History())->setHistory("advertising", $id, $paramsHistory);
        }
        
        return parent::put($id, $params);
    }
    
    public function delete(array $params): array 
    {
        return parent::delete($params);
    }
    
    public function createSqlTable($type = null) 
    {        
        return parent::createSqlTable("Advertising");
    }
}