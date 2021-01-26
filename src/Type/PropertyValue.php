<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class PropertyValue extends Entity implements TypeInterface
{
    protected $table = "propertyValue";
    
    protected $type = "PropertyValue";
    
    protected $properties = [ "name", "value" ];
    

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
    
    public function createSqlTable($type = null): array
    {
        return parent::createSqlTable("PropertyValue");
    }
        
    public static function extractValue($array, $name) 
    {
        if ($array) {
            foreach ($array as $value) {
                if ($value['name'] == $name) {
                    return $value['value'] ?? $value['result'] ?? $value['description'] ?? null;
                }
            }
        }
        return null;
    }
}
