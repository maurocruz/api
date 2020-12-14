<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;

class Order extends Entity implements TypeInterface
{
    protected $table = "order";

    protected $type = "Order";

    protected $hasTypes = [ "seller" => "Organization" ];

    public function get(array $params): array
    {
        return parent::get($params);
    }

    public function post(array $params): array
    {
        var_dump(parent::post($params));
    }

    public function put(array $params): array
    {
        return parent::put($params);
    }

    public function delete(array $params): array
    {
        return parent::delete($params);
    }

    public function createSqlTable($type = null): array
    {
        $maintenance = new Maintenance();
        $message[] = $maintenance->createSqlTable("Offer");
        $message[] = $maintenance->createSqlTable("Invoice");

        // sql create statement
        $message[] = parent::createSqlTable("Order");

        return $message;
    }
}