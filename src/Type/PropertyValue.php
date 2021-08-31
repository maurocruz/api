<?php

declare(strict_types=1);

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use ReflectionException;

class PropertyValue extends Entity implements TypeInterface
{
    /**
     * @var string
     */
    protected $table = "propertyValue";
    /**
     * @var string
     */
    protected string $type = "PropertyValue";
    /**
     * @var array|string[]
     */
    protected array $properties = [ "name", "value" ];

    /**
     * @param null $type
     * @return array
     * @throws ReflectionException
     */
    public function createSqlTable($type = null): array
    {
        return parent::createSqlTable("PropertyValue");
    }
}
