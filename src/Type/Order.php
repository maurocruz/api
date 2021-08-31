<?php

declare(strict_types=1);

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;
use ReflectionException;

class Order extends Entity implements TypeInterface
{
    /**
     * @var string
     */
    protected $table = "order";
    /**
     * @var string
     */
    protected string $type = "Order";
    /**
     * @var array|string[]
     */
    protected array $properties = [ "*" ];
    /**
     * @var array
     */
    protected array $hasTypes = [ "history" => "History", "partOfInvoice" => "Invoice", "orderedItem" => "OrderItem", "customer" => true, "seller" => true ];

    /**
     * @param null $type
     * @return array
     * @throws ReflectionException
     */
    public function createSqlTable($type = null): array
    {
        $maintenance = new Maintenance();
        $message[] = $maintenance->createSqlTable("Invoice");
        $message[] = parent::createSqlTable("Order");
        $message[] = $maintenance->createSqlTable("OrderItem");
        return $message;
    }
}
