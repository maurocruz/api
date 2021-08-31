<?php

declare(strict_types=1);

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use ReflectionException;

class ContactPoint extends Entity implements TypeInterface
{
    /**
     * @var string
     */
    protected $table = 'contactPoint';
    /**
     * @var string
     */
    protected  string $type = 'ContactPoint';
    /**
     * @var array|string[]
     */
    protected array $properties = [ "*" ];

    /**
     * @param null $type
     * @return array
     * @throws ReflectionException
     */
    public function createSqlTable($type = null): array
    {
        return parent::createSqlTable("ContactPoint");
    }
}
