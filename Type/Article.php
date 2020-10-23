<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;

class Article extends Entity implements TypeInterface
{
    protected $table = "article";
    
    protected $type = "Article";
    
    protected $properties = [ "headline", "author", "datePublished", "publisher"];
    
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
        return parent::delete($params);
    }
    
    public function createSqlTable($type = null) 
    {
        $maintenance = new Maintenance();
        
        $maintenance->createSqlTable("Person");
        $maintenance->createSqlTable("ImageObject");
        $maintenance->createSqlTable("Organization");
        
        return parent::createSqlTable("Article");
    }
}
