<?php

namespace Fwc\Api\Type;

use Fwc\Api\Server\Maintenance;

class ImageObject extends TypeAbstract implements TypeInterface
{
    protected $table = "imageObject";
    protected $type = "ImageObject";

    public function get(): array
    {
        return parent::get();
    }
    
    public function post(array $params): array 
    {
        return parent::post($params);
    }
    
    public function put(string $id, $params = null): array 
    {
        return parent::put($id, $params);
    }
    
    public function delete(string $id): array 
    {
        return parent::delete($id);
    }
    
    public function createSqlTable($type = null)
    {
        $message[] =  parent::createSqlTable("ImageObject");
        return $message;
    }
}
