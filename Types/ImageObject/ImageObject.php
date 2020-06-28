<?php

namespace Fwc\Api\Type;

class ImageObject extends TypeAbstract implements TypeInterface
{
    protected $table = "imageObject";
    protected $type = "ImageObject";

    /**
     * PUT
     * @return array
     */
    public function get(): array
    {
        return parent::get();
    }
    
    /**
     * POST
     * @param array $params
     * @return array
     */
    public function post(array $params): array 
    {
        return parent::post($params);
    }
    
    /**
     * PUT
     * @param string $id
     * @param type $params
     * @return array
     */
    public function put(string $id, $params = null): array 
    {
        return parent::put($id, $params);
    }
    
    /**
     * DELETE
     * @param string $id
     * @param type $params
     * @return array
     */
    public function delete(string $id, $params): array 
    {
        return parent::delete($id, $params);
    }
    
    /**
     * CREATE SQL
     * @param type $type
     * @return type
     */
    public function createSqlTable($type = null)
    {
        $message[] =  parent::createSqlTable("ImageObject");
        return $message;
    }
}
