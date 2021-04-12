<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;

class Product extends Entity implements TypeInterface {
    protected $table = "product";
    protected $type = "Product";
    protected $properties = [ "name" ];
    protected $hasTypes = [ "image" => "ImageObject", "manufacturer" => "Organization", "offers" => "Offer" ];

    public function post(array $params): array {
        $params['dateCreated'] = date("Y-m-d H:i:s");
        return parent::post($params);
    }

    public function createSqlTable($type = null): array {
        $maintenance = new Maintenance();
        $message[] = $maintenance->createSqlTable("ImageObject");
        $message[] = parent::createSqlTable("Product");
        return $message;
    }
}
