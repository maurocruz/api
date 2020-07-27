<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Auth\SessionUser;

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
        return parent::post($params);
    }
    
    public function put($params): array 
    {
        $summary = filter_input(INPUT_GET, "summaryHistory");
        
        if ($summary) {
            $paramsHistory["action"] = "UPDATE";
            $paramsHistory["summary"] = $summary == "" ? "ND" : $summary;
            $paramsHistory['tableHasPart'] = "advertising";
            $paramsHistory['idHasPart'] = $params['id'];
            $paramsHistory['user'] = SessionUser::getName();
            
            (new History())->post($paramsHistory);
        }
        
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