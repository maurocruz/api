<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;

class Offer extends Entity implements TypeInterface
{
    protected $table = "offer";

    protected $type = "Offer";

    protected $properties = [ "*" ];

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
        $maintenance = new Maintenance();
        $message[] = $maintenance->createSqlTable("QuantitativeValue");

        // sql create statement
        $message[] = parent::createSqlTable("Offer");

        return $message;
    }
}