<?php

namespace Fwc\Api\Type;

use Fwc\Api\Server\Maintenance;

class Place extends TypeAbstract implements TypeInterface
{
    public function get(): array
    {
        return parent::get();
    }
    
    public function post(array $params): array 
    {
        return parent::post($params);
    }
    
    public function put(string $id, $params = null): array 
    {
        return parent::put($id, $params);
    }
    
    public function delete(string $id): array 
    {
        return parent::delete($id);
    }
    
    public function createSqlTable($type = null)
    {    
        $maintenance = new Maintenance($this->request);
        
        $message[] = $maintenance->createSqlTable("ImageObject");
        
        $message[] = $maintenance->createSqlTable("PostalAddress");
        
        $message[] =  parent::createSqlTable("Place");
        
        return $message;
    }
}
