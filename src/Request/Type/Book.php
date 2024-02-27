<?php

declare(strict_types=1);

namespace Plinct\Api\Request\Type;

use Plinct\Api\Request\Server\Entity;
use ReflectionException;

class Book extends Entity
{
    /**
     * @var string
     */
    protected string $table = "book";
    /**
     * @var string
     */
    protected string $type = "Book";
    /**
     * @var array|string[]
     */
    protected array $properties = [ "name", "author" ];
    /**
     * @var array
     */
    protected array $hasTypes = [ ];

    /**
     * @param null $type
     * @return array
     * @throws ReflectionException
     */
    public function createSqlTable($type = null): array
    {
        return parent::createSqlTable("Book");
    }
}
