<?php

declare(strict_types=1);

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use ReflectionException;

class Thing extends Entity
{
    /**
     * @var string
     */
    protected string $table = "thing";
    /**
     * @var string
     */
    protected string $type = "Thing";

    /**
     * @param null $type
     * @return array
     * @throws ReflectionException
     */
    public function createSqlTable($type = null): array
    {
        return parent::createSqlTable('Thing');
    }
}
