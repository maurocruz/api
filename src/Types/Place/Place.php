<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Maintenance;

class Place extends TypeAbstract implements TypeInterface
{
    protected $table = "place";
    
    protected $type = "Place";
    
    protected $properties = [ "name", "latitude", "longitude", "address" ];
    
    protected $hasTypes = [ "address" => "PostalAddress", "image" => "ImageObject" ];

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
     * @return array
     */
    public function delete(array $params): array 
    {
        return parent::delete($params);
    }
    
    /**
     * CREATE SQL
     * @param type $type
     * @return type
     */
    public function createSqlTable($type = null)
    {    
        $maintenance = new Maintenance($this->request);        
        
        $message[] = $maintenance->createSqlTable("ImageObject");        
        $message[] = $maintenance->createSqlTable("PostalAddress");  
        
        $message[] =  parent::createSqlTable("Place");        
        
        return $message;
    }
}
