<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class History extends Entity implements TypeInterface {
    protected $table = "history";
    protected $type = "History";
    protected $properties = [ "*", "user" ];
    protected $hasTypes = [ "user" => "User" ];
    
    public function createSqlTable($type = null): array {
        return parent::createSqlTable("History");
    }
}
