<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;

class Taxon extends Entity implements TypeInterface
{    
    protected $table = "taxon";
    
    protected $type = "Taxon";
    
    protected $properties = [ "name", "taxonRank", "parentTaxon", "url" ];
    
    protected $hasTypes = [ "image" => "ImageObject", "parentTaxon" => "Taxon" ];
    
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
        $maintenance = new Maintenance();
              
        $maintenance->createSqlTable("ImageObject");
        
        return parent::createSqlTable("Taxon");
    } 
}
