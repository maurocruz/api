<?php

declare(strict_types=1);

namespace Plinct\Api\Request\Type;

use Plinct\Api\Request\Server\Entity;
use ReflectionException;

class ContactPoint extends Entity
{
    /**
     * @var string
     */
    protected string $table = 'contactPoint';
    /**
     * @var string
     */
    protected  string $type = 'ContactPoint';
    /**
     * @var array|string[]
     */
    protected array $properties = [ "*" ];

}
