<?php

namespace Fwc\Api\Type;


interface TypeInterface 
{   
    /**
     * HTTP Request GET
     * @param array $args
     */
    public function get(array $args): array;
    
    public function post(array $params): array;
    
    public function erase(string $id): array;
    
    public function put(string $id): array;
    
    public function createSqlTable($type = null): bool;
}
