<?php

namespace Plinct\Api\Type;

class Article extends TypeAbstract implements TypeInterface
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
        return parent::delete($id, $params);
    }
    
    public function createSqlTable($type = null) 
    {
        $maintenance = new \Plinct\Api\Server\Maintenance($this->request);
        
        $maintenance->createSqlTable("Person");
        $maintenance->createSqlTable("ImageObject");
        $maintenance->createSqlTable("Organization");
        
        return parent::createSqlTable("Article");
    }
}
