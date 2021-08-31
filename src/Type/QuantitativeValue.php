<?php

declare(strict_types=1);

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use ReflectionException;

class QuantitativeValue extends Entity implements TypeInterface
{
    /**
     * @var string
     */
    protected $table = "quantitativeValue";
    /**
     * @var string
     */
    protected string $type = "QuantitativeValue";

    /**
     * @param null $type
     * @return array
     * @throws ReflectionException
     */
    public function createSqlTable($type = null): array
    {
        return parent::createSqlTable("QuantitativeValue");
    }
}
