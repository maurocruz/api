<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use ReflectionException;

class VideoObject extends Entity implements TypeInterface {
    protected $table = "videoObject";
    protected $type = "VideoObject";
    protected $properties = [ "name", "description", "url", "thumbnailUrl" ];

    public function createSqlTable($type = null): array {
        return parent::createSqlTable("VideoObject");
    }
}
