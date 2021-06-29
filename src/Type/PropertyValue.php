<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class PropertyValue extends Entity implements TypeInterface {
    protected $table = "propertyValue";
    protected $type = "PropertyValue";
    protected $properties = [ "name", "value" ];
    
    public function createSqlTable($type = null): array {
        return parent::createSqlTable("PropertyValue");
    }
}
