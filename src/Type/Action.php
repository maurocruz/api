<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class Action extends Entity implements TypeInterface {
    protected string $table = "action";
    protected string $type = "Action";

    public function createSqlTable($type = null): array {
        return parent::createSqlTable("Action");
    }
}
