<?php

namespace Fwc\Api\Type;


interface TypeInterface 
{    
    public function get($args);
    
    public function createSqlTable($type = null): bool;
}
