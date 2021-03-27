<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;

class WebPage extends Entity implements TypeInterface {
    protected string $table = "webPage";
    protected string $type = "WebPage";
    protected array $properties = [ "name", "description", "url", "identifier" ];
    protected array $hasTypes = [ "hasPart" => "WebPageElement", "identifier" => "PropertyValue" ];

    public function get(array $params): array {
        return parent::get($params);
    }

    public function post(array $params): array {
        $params['breadcrumb'] = json_encode((new Breadcrumb())->get($params), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return parent::post($params);
    }

    public function put(array $params): array {
        $params['breadcrumb'] = json_encode((new Breadcrumb())->get($params), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return parent::put($params);
    }

    public function delete(array $params): array {
        return parent::delete($params);
    }

    public function createSqlTable($type = null): array {
        $maintenance = new Maintenance();
        $message[] = $maintenance->createSqlTable("propertyValue");
        $message[] = parent::createSqlTable("webPage");
        $message[] = $maintenance->createSqlTable("webPageElement");
        return $message;
    }
}
