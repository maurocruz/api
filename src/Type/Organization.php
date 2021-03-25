<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Maintenance;
use Plinct\Api\Server\Entity;

class Organization extends Entity implements TypeInterface {
    protected string $table = "organization";
    protected string $type = "Organization";
    protected array $properties = [ "name", "description", "legalName", "taxId" ];
    protected array $hasTypes = [ "address" => "PostalAddress", "contactPoint" => "ContactPoint", "member" => "Person", "location" => "Place", "image" => "ImageObject", "localBusiness" => "LocalBusiness" ];

    public function createSqlTable($type = null) : array {
        $maintenance = new Maintenance();
        $message[] = $maintenance->createSqlTable("ContactPoint");
        $message[] = $maintenance->createSqlTable("PostalAddress");
        $message[] = $maintenance->createSqlTable("ImageObject");
        $message[] = $maintenance->createSqlTable("Person");
        $message[] = $maintenance->createSqlTable("Place");
        $message[] = parent::createSqlTable("Organization");
        return $message;
    }
}
