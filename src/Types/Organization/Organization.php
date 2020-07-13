<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Maintenance;

class Organization extends TypeAbstract implements TypeInterface
{
    protected $table = "organization";
    
    protected $type = "Organization";
    
    protected $properties = [ "name", "description", "legalName", "taxId" ];


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
    public function put(string $id, $params = null): array 
    {
        return parent::put($id, $params);
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
        // sql create statement
        $message[] = parent::createSqlTable("Organization");
        
        return $message;
    }
}
