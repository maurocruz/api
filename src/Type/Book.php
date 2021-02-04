<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class Book extends Entity implements TypeInterface
{
    protected $table = "book";
    
    protected $type = "Book";
    
    protected $properties = [ "name", "author" ];
    
    protected $hasTypes = [ ];

    public function get(array $params): array 
    {
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
        return parent::createSqlTable("Book");
    }
}