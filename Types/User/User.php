<?php

namespace Fwc\Api\Type;

class User extends TypeAbstract implements TypeInterface
{
    public function get($args) {
        ;
    }
    
    public function createSqlTable($type = null): bool 
    {
        return parent::createSqlTable('user');
    }
}
