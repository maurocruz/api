<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Maintenance;
use Plinct\Api\Server\Entity;

class LocalBusiness extends Entity implements TypeInterface {
    protected $table = "localBusiness";
    protected $type = "LocalBusiness";
    protected $properties = [ "name" ];
    protected $hasTypes = [ "location" => "Place", "organization" => "Organization", "contactPoint" => "ContactPoint", "address" => "PostalAddress", "member" => "Person", "image" => "ImageObject" ];

    public function post(array $params): array {
        $params['dateCreated'] = date("Y-m-d");
        return parent::post($params);
    }

    public function buildSchema($params, $data): array {
        return parent::buildSchema($params, $data);
    }

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
