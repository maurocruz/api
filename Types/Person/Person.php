<?php

namespace Fwc\Api\Type;

use Fwc\Api\Server\Maintenance;

class Person extends TypeAbstract implements TypeInterface
{
    public function get($args) {
        ;
    }
    
    public function createSqlTable($type = null): bool 
    {         
        $maintenance = new Maintenance($this->request);
        
        $maintenance->createSqlTable("PostalAddress");
        
        $maintenance->createSqlTable("ContactPoint");
        
        return parent::createSqlTable("Person");
    }
}
