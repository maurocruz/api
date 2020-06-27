<?php

namespace Fwc\Api\Type;

use Fwc\Api\Server\Maintenance;

class Organization extends TypeAbstract implements TypeInterface
{
    protected $table = "organization";
    protected $type = "Organization";
    
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
        // require
        $maintenance = (new Maintenance($this->request));
        $message[] = $maintenance->createSqlTable("ContactPoint");
        $message[] = $maintenance->createSqlTable("PostalAddress");
        $message[] = $maintenance->createSqlTable("ImageObject");
        $message[] = $maintenance->createSqlTable("Person");
        $message[] = $maintenance->createSqlTable("Place");
        // sql create statement
        $message[] = parent::createSqlTable("Organization");
        
        return $message;
    }
}
