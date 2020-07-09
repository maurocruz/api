<?php

namespace Fwc\Api\Type;

class Article extends TypeAbstract implements TypeInterface
{
    protected $table = "article";
    
    protected $type = "Article";
    
    protected $properties = [ "headline", "author", "datePublished", "publisher"];
    
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
        $maintenance = new \Fwc\Api\Server\Maintenance($this->request);
        
        $maintenance->createSqlTable("Person");
        $maintenance->createSqlTable("ImageObject");
        $maintenance->createSqlTable("Organization");
        
        return parent::createSqlTable("Article");
    }
}
