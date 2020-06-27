<?php

namespace Fwc\Api\Type;

class PostalAddress extends TypeAbstract implements TypeInterface
{
    protected $table = 'postalAddress';
    
    protected $type = 'PostalAddress';
    
    protected $properties = [ 'streetAddress', 'addressLocality', 'addressRegion', 'addressCountry', 'postalCode' ]; 
    

    public function get(): array 
    {
        return parent::get();
    }
    
    public function post(array $params): array 
    {
        return parent::post($params);
    }
    
    public function delete(string $id): array 
    {
        return parent::delete($id);
    }
    
    public function put(string $id, $params = null): array 
    {
        return parent::put($id, $params);
    }
    
    public function createSqlTable($type = null)
    {
        return parent::createSqlTable("PostalAddress");
    }
}
