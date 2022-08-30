<?php

declare(strict_types=1);

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class Trip extends Entity
{
    /**
     * @var string
     */
    protected string $table = 'trip';
    /**
     * @var string
     */
    protected string $type = 'Trip';
    /**
     * @var array|string[]
     */
    protected array $properties = ['name'];
    /**
     * @var array|string[]
     */
    protected array $hasTypes = ['provider'=>'Organization','image'=>'ImageObject','identifier'=>'PropertyValue','subTrip'=>'Trip'];
}
