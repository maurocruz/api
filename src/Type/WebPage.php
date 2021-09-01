<?php

declare(strict_types=1);

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;
use ReflectionException;

class WebPage extends Entity implements TypeInterface
{
    /**
     * @var string
     */
    protected string $table = "webPage";
    /**
     * @var string
     */
    protected string $type = "WebPage";
    /**
     * @var array|string[]
     */
    protected array $properties = [ "name", "description", "url", "identifier","isPartOf" ];
    /**
     * @var array|string[]
     */
    protected array $hasTypes = [ "hasPart" => "WebPageElement", "identifier" => "PropertyValue", "isPartOf"=>'WebSite'];

    /**
     * @param array $params
     * @return array
     */
    public function post(array $params): array
    {
        $params['breadcrumb'] = json_encode((new Breadcrumb())->get($params), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return parent::post($params);
    }

    /**
     * @param array $params
     * @return array
     */
    public function put(array $params): array
    {
        $params['breadcrumb'] = json_encode((new Breadcrumb())->get($params), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return parent::put($params);
    }

    /**
     * @param null $type
     * @return array
     * @throws ReflectionException
     */
    public function createSqlTable($type = null): array
    {
        $maintenance = new Maintenance();
        $message[] = $maintenance->createSqlTable("propertyValue");
        $message[] = parent::createSqlTable("webPage");
        $message[] = $maintenance->createSqlTable("webPageElement");
        return $message;
    }
}
