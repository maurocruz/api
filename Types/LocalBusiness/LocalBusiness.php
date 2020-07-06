<?php

namespace Fwc\Api\Type;

use Fwc\Api\Server\Maintenance;

class LocalBusiness extends TypeAbstract implements TypeInterface
{
    protected $table = "localBusiness";
    
    protected $type = "LocalBusiness";
    
    protected $properties = [ "name", "location", "organization" ];
    
    protected $withTypes = [ "location" => "Place", "organization" => "Organization", "contactPoint" => "ContactPoint", "address" => "PostalAddress", "person" => "Person", "image" => "ImageObject" ];

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
    public function put(string $id, $params): array 
    {
        return parent::put($id, $params);
    }
    
    /**
     * DELETE
     * @param string $id
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
