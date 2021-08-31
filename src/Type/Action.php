<?php

declare(strict_types=1);

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use ReflectionException;

class Action extends Entity implements TypeInterface
{
    /**
     * @var string
     */
    protected $table = "action";
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
