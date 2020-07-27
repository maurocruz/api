<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Maintenance;

use Plinct\Api\Server\Entity;

class LocalBusiness extends Entity implements TypeInterface
{
    protected $table = "localBusiness";
    
    protected $type = "LocalBusiness";
    
    protected $properties = [ "*" ];
    
    protected $hasTypes = [ "location" => "Place", "organization" => "Organization", "contactPoint" => "ContactPoint", "address" => "PostalAddress", "person" => "Person", "image" => "ImageObject" ];

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
        $maintenance = (new Maintenance($this->request));
        
        $message[] = $maintenance->createSqlTable("ContactPoint");
        $message[] = $maintenance->createSqlTable("PostalAddress");
        $message[] = $maintenance->createSqlTable("ImageObject");
        $message[] = $maintenance->createSqlTable("Person");
        $message[] = $maintenance->createSqlTable("Place");
        $message[] = $maintenance->createSqlTable("Organization");
        
        $message[] = parent::createSqlTable("LocalBusiness");
        
        return $message;
    }
}
