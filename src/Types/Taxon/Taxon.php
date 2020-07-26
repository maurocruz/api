<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class Taxon extends Entity implements TypeInterface
{    
    protected $table = "taxon";
    
    protected $type = "Taxon";
    
    protected $properties = [ "name", "family", "genus", "specie" ];
    
    protected $hasTypes = [ "image" => "ImageObject" ];
    
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
              
        $maintenance->createSqlTable("ImageObject");
        
        return parent::createSqlTable("Taxon");
    } 
}
