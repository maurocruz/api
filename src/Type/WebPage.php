<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;

class WebPage extends Entity implements TypeInterface {
    protected $table = "webPage";
    protected $type = "WebPage";
    protected $properties = [ "name", "description", "url", "identifier" ];
    protected $hasTypes = [ "hasPart" => "WebPageElement", "identifier" => "PropertyValue" ];

    public function post(array $params): array {
        $params['breadcrumb'] = json_encode((new Breadcrumb())->get($params), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return parent::post($params);
    }

    public function put(array $params): array {
        $params['breadcrumb'] = json_encode((new Breadcrumb())->get($params), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return parent::put($params);
    }

    public function createSqlTable($type = null): array {
        $maintenance = new Maintenance();
        $message[] = $maintenance->createSqlTable("propertyValue");
        $message[] = parent::createSqlTable("webPage");
        $message[] = $maintenance->createSqlTable("webPageElement");
        return $message;
    }
}
