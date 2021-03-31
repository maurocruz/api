<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class Action extends Entity implements TypeInterface {
    protected $table = "action";
    protected $type = "Action";

    public function createSqlTable($type = null): array {
        return parent::createSqlTable("Action");
    }
}
