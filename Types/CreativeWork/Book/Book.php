<?php

namespace Fwc\Api\Type;

class Book extends TypeAbstract implements TypeInterface
{
    protected $table = "book";
    
    protected $type = "Book";
    
    protected $properties = [ "name", "author" ];
    
    protected $withTypes = [ ];

    public function get(array $params): array 
    {
        return parent::get($params);
    }
    
    public function post(array $params): array 
    {
        return parent::post($params);
    }
    
    public function put(string $id, $params): array 
    {
        return parent::put($id, $params);
    }
    
    public function delete(array $params): array 
    {
        return parent::delete($id, $params);
    }
    
    public function createSqlTable($type = null) 
    {                
        return parent::createSqlTable("Book");
    }
}
