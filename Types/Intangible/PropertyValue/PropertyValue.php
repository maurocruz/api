<?php

namespace Fwc\Api\Type;


class PropertyValue extends TypeAbstract implements TypeInterface
{
    protected $table = "attributes";
    
    protected $type = "PropertyValue";
    
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
    }
}
