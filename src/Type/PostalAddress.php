<?php

declare(strict_types=1);

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use ReflectionException;

class PostalAddress extends Entity
{
    /**
     * @var string
     */
    protected string $table = 'postalAddress';
    /**
     * @var string
     */
    protected string $type = 'PostalAddress';
    /**
     * @var array|string[]
     */
    protected array $properties = [ 'streetAddress', 'addressLocality', 'addressRegion', 'addressCountry', 'postalCode' ];

    /**
     * @param null $type
     * @return array
     * @throws ReflectionException
     */
    public function createSqlTable($type = null): array
    {
        return parent::createSqlTable("PostalAddress");
    }
}
