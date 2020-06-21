<?php

namespace Fwc\Api\Type;

use Fwc\Api\Server\Maintenance;

class Person extends TypeAbstract implements TypeInterface
{
    public function get(array $args): array {
        ;
    }
    
    public function post(array $params): array 
    {
        return [ "mesage" => "under development" ];
    }
    
    
    public function createSqlTable($type = null): bool 
    {         
        $maintenance = new Maintenance($this->request);        
        $maintenance->createSqlTable("PostalAddress");        
        $maintenance->createSqlTable("ContactPoint");        
        return parent::createSqlTable("Person");
    }
}
