<?php

declare(strict_types=1);

namespace Plinct\Api\Request\Type;

use Plinct\Api\Request\Server\Entity;
use ReflectionException;

class QuantitativeValue extends Entity
{
    /**
     * @var string
     */
    protected string $table = "quantitativeValue";
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
