<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Maintenance;
use Plinct\Api\Server\Entity;
use ReflectionException;

class Organization extends Entity implements TypeInterface
{
    protected $table = "organization";
    
    protected $type = "Organization";
    
    protected $properties = [ "name", "description", "legalName", "taxId" ];

    protected $hasTypes = [ "address" => "PostalAddress", "contactPoint" => "ContactPoint", "member" => "Person", "location" => "Place", "image" => "ImageObject", "localBusiness" => "LocalBusiness" ];

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
     * @param null $type
     * @return array
     * @throws ReflectionException
     */
    public function createSqlTable($type = null) : array
    {
        $maintenance = new Maintenance();
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
