<?php

declare(strict_types=1);

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use ReflectionException;

class VideoObject extends Entity implements TypeInterface
{
    /**
     * @var string
     */
    protected $table = "videoObject";
    /**
     * @var string
     */
    protected string $type = "VideoObject";
    /**
     * @var array|string[]
     */
    protected array $properties = [ "name", "description", "url", "thumbnailUrl" ];

    /**
     * @param null $type
     * @return array
     * @throws ReflectionException
     */
    public function createSqlTable($type = null): array
    {
        return parent::createSqlTable("VideoObject");
    }
}
