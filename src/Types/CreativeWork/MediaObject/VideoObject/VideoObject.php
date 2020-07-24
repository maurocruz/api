<?php

namespace Plinct\Api\Type;

class VideoObject extends TypeAbstract implements TypeInterface
{
    protected $table = "videoObject";
    
    protected $type = "VideoObject";
    
    protected $properties = [ "name", "description", "url", "thumbnailUrl" ];
    
    /**
     * GET
     * @param array $params
     * @return array
     */
    public function get(array $params): array 
    {
        return parent::get($params);
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
    public function put(array $params): array 
    {
        return parent::put($params);
    }
    
    /**
     * DELETE
     * @param string $id
     * @param type $params
     * @return array
     */
    public function delete(array $params): array 
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
        return parent::createSqlTable("VideoObject");
    }
}
