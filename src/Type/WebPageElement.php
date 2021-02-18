<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;

class WebPageElement extends Entity implements TypeInterface
{
    protected $table = "webPageElement";
    protected $type = "WebPageElement";
    protected $properties = [ "name", "text", "position", "image", "identifier" ];
    protected $hasTypes = [ "image" => "ImageObject", "identifier" => "PropertyValue", "webPage" => "WebPage" ];

    public function get(array $params): array {
        $params['orderBy'] = $params['orderBy'] ?? "position";
        return parent::get($params);
    }
    
    public function post(array $params): array {
        return parent::post($params);
    }
    
    public function put($params): array {
        return parent::put($params);
    }
    
    public function delete($params): array {
        return parent::delete($params);
    }
    
    public function createSqlTable($type = null): array {
        $maintenance = new Maintenance();
        $message[] = $maintenance->createSqlTable("propertyValue");
        $message[] = $maintenance->createSqlTable("webPage");
        $message[] = $maintenance->createSqlTable("imageObject");
        $message[] = parent::createSqlTable("WebPageElement");
        return $message;
    }
}
