<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class QuantitativeValue extends Entity implements TypeInterface
{
    protected $table = "quantitativeValue";

    protected $type = "QuantitativeValue";

    public function get(array $params): array
    {
        return parent::get($params);
    }

    public function post(array $params): array
    {
        return parent::post($params);
    }

    public function put(array $params): array
    {
        return parent::put($params);
    }

    public function delete(array $params): array
    {
        return parent::delete($params);
    }

    public function createSqlTable($type = null)
    {
        return parent::createSqlTable("QuantitativeValue");
    }
}