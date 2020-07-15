<?php

namespace Plinct\Api\Type;

class Taxon extends TypeAbstract implements TypeInterface
{    
    protected $table = "taxon";
    
    protected $type = "Taxon";
    
    protected $properties = [ "name", "family", "genus", "specie" ];
    
    protected $withTypes = [ "image" => "ImageObject" ];
    
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
        $maintenance = new \Plinct\Api\Server\Maintenance($this->request);
              
        $maintenance->createSqlTable("ImageObject");
        
        return parent::createSqlTable("Taxon");
    } 
}
