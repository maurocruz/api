<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class Thing extends Entity implements TypeInterface {
    protected $table = "thing";
    protected $type = "Thing";

    public function createSqlTable($type = null): array {
        return parent::createSqlTable('Thing');
    }
}
