<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Maintenance;
use Plinct\Api\Server\Entity;
use ReflectionException;

class LocalBusiness extends Entity implements TypeInterface {
    protected $table = "localBusiness";
    protected $type = "LocalBusiness";
    protected $properties = [ "name" ];
    protected $hasTypes = [ "location" => "Place", "organization" => "Organization", "contactPoint" => "ContactPoint", "address" => "PostalAddress", "member" => "Person", "image" => "ImageObject" ];

    /**
     * GET
     * @param array $params
     * @return array
     */
    public function get(array $params): array {
        return parent::get($params);
    }
    
    /**
     * POST
     * @param array $params
     * @return array
     */
    public function post(array $params): array {
        $params['dateCreated'] = date("Y-m-d");
        return parent::post($params);
    }
    
    /**
     * PUT
     * @param array $params
     * @return array
     */
    public function put(array $params): array {
        return parent::put($params);
    }
    
    /**
     * DELETE
     * @param array $params
     * @return array
     */
    public function delete(array $params): array {
        return parent::delete($params);
    }

    public function buildSchema($params, $data): array {
        return parent::buildSchema($params, $data);
    }

    /**
     * CREATE SQL
     * @param null $type
     * @return array
     * @throws ReflectionException
     */
    public function createSqlTable($type = null): array {
        $maintenance = new Maintenance();
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
