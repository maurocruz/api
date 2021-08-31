<?php

declare(strict_types=1);

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use ReflectionException;

class Book extends Entity implements TypeInterface
{
    /**
     * @var string
     */
    protected $table = "book";
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
