<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class Advertising extends Entity implements TypeInterface
{
    protected $table = "advertising";
    
    protected $type = "Advertising";
    
    protected $properties = [ "customer","tipo","valor","data","vencimento","status","tags" ];
    
    protected $hasTypes = [ "customer" => "LocalBusiness", "history" => "History", "payment" => "Payment" ];

    public function get(array $params): array 
    {
        return parent::get($params);
    }
    
    public function post(array $params): array 
    {        
        $data = parent::post($params);
        
        (new History())->postHistory("CREATED", _("Create new advertising"), "advertising", $data['id']);
        
        return $data;
    }
    
    public function put($params): array 
    {
        (new History())->postHistory("UPDATE", filter_input(INPUT_GET, "summaryHistory"), "advertising", $params['id']);
        
        return parent::put($params);
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