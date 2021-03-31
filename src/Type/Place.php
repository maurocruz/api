<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Maintenance;
use Plinct\Api\Server\Entity;

class Place extends Entity implements TypeInterface {
    protected $table = "place";
    protected $type = "Place";
    protected $properties = [ "*","address" ];
    protected $hasTypes = [ "address" => "PostalAddress", "image" => "ImageObject" ];

    public function post(array $params): array {
        $params['dateCreated'] = date("Y-m-d H:i:s");
        return parent::post($params);
    }

    public function createSqlTable($type = null): array {
        $maintenance = new Maintenance();
        $message[] = $maintenance->createSqlTable("ImageObject");
        $message[] = $maintenance->createSqlTable("PostalAddress");
        $message[] =  parent::createSqlTable("Place");
        return $message;
    }
}
