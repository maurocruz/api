<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;

class Order extends Entity implements TypeInterface {
    protected $table = "order";
    protected $type = "Order";
    protected $properties = [ "*" ];
    protected $hasTypes = [ "history" => "History", "partOfInvoice" => "Invoice", "orderedItem" => "OrderItem", "customer" => true, "seller" => true ];

    public function createSqlTable($type = null): array {
        $maintenance = new Maintenance();
        $message[] = $maintenance->createSqlTable("Invoice");
        $message[] = parent::createSqlTable("Order");
        $message[] = $maintenance->createSqlTable("OrderItem");
        return $message;
    }
}
