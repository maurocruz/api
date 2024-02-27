<?php

declare(strict_types=1);

namespace Plinct\Api\Request\Type;

use Plinct\Api\Request\Server\Entity;
use ReflectionException;

class VideoObject extends Entity
{
    /**
     * @var string
     */
    protected string $table = "videoObject";
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
