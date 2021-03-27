<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;

class Product extends Entity implements TypeInterface
{
    protected string $table = "product";
    protected string $type = "Product";
    protected array $properties = [ "name" ];
    protected array $hasTypes = [ "image" => "ImageObject" ];

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
