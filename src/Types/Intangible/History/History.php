<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Auth\SessionUser;
use Plinct\Api\Server\Relationships;

class History extends Entity implements TypeInterface
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
    
    public function put(array $params): array 
    {
        return parent::put($params);
    }
    
    public function delete(array $params): array 
    {
        return parent::delete($params);
    }
    
    public function createSqlTable($type = null) 
    {        
        return parent::createSqlTable("History");
    }
    
    public function setHistory($tableOwner, $idOwner, $paramsHistory)
    {     
        $paramsHistory['user'] = SessionUser::getName();
            
        $idhistory = (new History())->post($paramsHistory);            

        (new Relationships())->putRelationship($tableOwner, $idOwner, "history", $idhistory['id']);
    }
}
