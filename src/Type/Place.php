<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Maintenance;
use Plinct\Api\Server\Entity;
use ReflectionException;

class Place extends Entity implements TypeInterface
{
    protected $table = "place";
    
    protected $type = "Place";
    
    protected $properties = [ "*", "address" ];
    
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
        $params['dateCreated'] = date("Y-m-d H:i:s");
        
        return parent::post($params);
    }
    
    /**
     * PUT
     * @param array $params
     * @return array
     */
    public function put(array $params): array 
    {
        return parent::put($params);
    }
    
    /**
     * DELETE
     * @param array $params
     * @return array
     */
    public function delete(array $params): array 
    {
        return parent::delete($params);
    }

    /**
     * CREATE SQL
     * @param ?string $type
     * @return array
     * @throws ReflectionException
     */
    public function createSqlTable($type = null): array
    {    
        $maintenance = new Maintenance();
        
        $message[] = $maintenance->createSqlTable("ImageObject");        
        $message[] = $maintenance->createSqlTable("PostalAddress");  
        
        $message[] =  parent::createSqlTable("Place");        
        
        return $message;
    }
}
