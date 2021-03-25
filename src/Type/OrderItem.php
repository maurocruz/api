<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;

class OrderItem extends Entity implements TypeInterface {
    protected string $table = "orderItem";
    protected string $type = "OrderItem";
    protected array $properties = [ "*", "orderedItem" ];
    protected array $hasTypes = [ "orderItemNumber" => "Order", "orderedItem" => true ];

    public function post(array $params): array {
        unset($params['tableHasPart']);
        return parent::post($params);
    }

    public function createSqlTable($type = null): array {
        $maintenance = (new Maintenance());
        $message[] = $maintenance->createSqlTable("Order");
        $messagem[] = parent::createSqlTable("OrderItem");
        return $message;
    }
}
