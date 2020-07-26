<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class Event extends Entity implements TypeInterface
{
    protected $table = "event";
    
    protected $type = "Event";
    
    protected $properties = [ "name", "location", "startDate" ];
    
    protected $hasTypes = [ "location" => "Place", "image" => "ImageObject" ];
    
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
        return parent::delete($id, $params);
    }
    
    public function createSqlTable($type = null) 
    {
        $maintenance = new \Plinct\Api\Server\Maintenance($this->request);
        
        $maintenance->createSqlTable("Person");        
        $maintenance->createSqlTable("ImageObject");        
        $maintenance->createSqlTable("Place");
        
        return parent::createSqlTable("Event");
    }    
}
