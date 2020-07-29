<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class WebPageElement extends Entity implements TypeInterface
{
    protected $table = "webPageElement";
    
    protected $type = "WebPageElement";
    
    protected $properties = [ "*" ];
    
    protected $hasTypes = [ "image" => "ImageObject" ];
    
    public function get(array $params): array 
    {
        return parent::get($params);
    }
    
    public function post(array $params): array 
    {
        return parent::post($params);
    }
    
    public function put($params): array 
    {
        return parent::put($params);
    }
    
    public function delete($params): array 
    {
        return parent::delete($params);
    }
    
    public function createSqlTable($type = null) 
    {
        return parent::createSqlTable("WebPageElement");
    }
}
