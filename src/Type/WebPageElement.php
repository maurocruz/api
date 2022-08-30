<?php

declare(strict_types=1);

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;
use ReflectionException;

class WebPageElement extends Entity
{
    /**
     * @var string
     */
    protected string $table = "webPageElement";
    /**
     * @var string
     */
    protected string $type = "WebPageElement";
    /**
     * @var array|string[]
     */
    protected array $properties = [ "name", "text", "position", "image", "identifier" ];
    /**
     * @var array|string[]
     */
    protected array $hasTypes = [ "image" => "ImageObject", "identifier" => "PropertyValue", "isPartOf" => "WebPage" ];

    /**
     * @param array $params
     * @return array
     */
    public function get(array $params = []): array
    {
        $params['orderBy'] = $params['orderBy'] ?? "position";
        return parent::get($params);
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
        $message[] = $maintenance->createSqlTable("webPage");
        $message[] = $maintenance->createSqlTable("imageObject");
        $message[] = parent::createSqlTable("WebPageElement");
        return $message;
    }
}
