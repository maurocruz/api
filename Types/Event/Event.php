<?php

namespace Fwc\Api\Type;

class Event extends TypeAbstract implements TypeInterface
{
    protected $table = "event";
    
    protected $type = "Event";
    
    protected $properties = [ "name", "location", "startDate" ];
    
    protected $withTypes = [ "location" => "Place", "image" => "ImageObject" ];
    
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
    
    public function delete(string $id, $params): array 
    {
        return parent::delete($id, $params);
    }
    
    public function createSqlTable($type = null) 
    {
        $maintenance = new \Fwc\Api\Server\Maintenance($this->request);
        
        $maintenance->createSqlTable("Person");        
        $maintenance->createSqlTable("ImageObject");        
        $maintenance->createSqlTable("Place");
        
        return parent::createSqlTable($type);
    }    
}
