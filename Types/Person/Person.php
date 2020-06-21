<?php

namespace Fwc\Api\Type;

use Fwc\Api\Server\Maintenance;

class Person extends TypeAbstract implements TypeInterface
{
    protected $table = "person";
    protected $type = "Person";
        
    public function get(array $args): array {
        ;
    }
    
    public function post(array $params): array 
    {
        if(isset($params['givenName']) && isset($params['familyName'])) {
            return parent::created($params);
        } else {
            return [ "messagen" => "incomplete mandatory data" ];
        } 
    }
    
    
    public function createSqlTable($type = null): bool 
    {         
        $maintenance = new Maintenance($this->request);        
        $maintenance->createSqlTable("PostalAddress");        
        $maintenance->createSqlTable("ContactPoint");        
        return parent::createSqlTable("Person");
    }
}
