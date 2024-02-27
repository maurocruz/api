<?php

declare(strict_types=1);

namespace Plinct\Api\Request\Type;

use Plinct\Api\Request\Server\Entity;
use ReflectionException;

class Action extends Entity
{
    /**
     * @var string
     */
    protected string $table = "action";
    /**
     * @var string
     */
    protected string $type = "Action";

    /**
     * @param null $type
     * @return array
     * @throws ReflectionException
     */
    public function createSqlTable($type = null): array
    {
        return parent::createSqlTable("Action");
    }
}
