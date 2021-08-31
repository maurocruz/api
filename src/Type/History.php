<?php

declare(strict_types=1);

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use ReflectionException;

class History extends Entity implements TypeInterface
{
    /**
     * @var string
     */
    protected $table = "history";
    /**
     * @var string
     */
    protected string $type = "History";
    /**
     * @var array|string[]
     */
    protected array $properties = [ "*", "user" ];
    /**
     * @var array|string[]
     */
    protected array $hasTypes = [ "user" => "User" ];

    /**
     * @param null $type
     * @return array
     * @throws ReflectionException
     */
    public function createSqlTable($type = null): array
    {
        return parent::createSqlTable("History");
    }
}
