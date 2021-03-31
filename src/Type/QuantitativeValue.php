<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class QuantitativeValue extends Entity implements TypeInterface {
    protected $table = "quantitativeValue";
    protected $type = "QuantitativeValue";

    public function createSqlTable($type = null): array {
        return parent::createSqlTable("QuantitativeValue");
    }
}