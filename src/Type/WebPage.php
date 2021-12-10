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
    protected array $properties = [];
    /**
     * @var array|string[]
     */
    protected array $hasTypes = [ "hasPart" => "WebPageElement", "identifier" => "PropertyValue", "isPartOf"=>'WebSite'];

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
