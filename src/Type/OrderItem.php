<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;

class OrderItem extends Entity implements TypeInterface {
    protected $table = "orderItem";
    protected $type = "OrderItem";
    protected $properties = [ "*", "orderedItem" ];
    protected $hasTypes = [ "referencesOrder" => "Order", "offer" => "Offer", "orderedItem" => true ];

    public function post(array $params): array {
        unset($params['tableHasPart']);
        return parent::post($params);
    }

    public function createSqlTable($type = null): array {
        $maintenance = (new Maintenance());
        $message[] = $maintenance->createSqlTable("Order");
        $message[] = parent::createSqlTable("OrderItem");
        return $message;
    }
}
