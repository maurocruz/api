<?php

declare(strict_types=1);

namespace Plinct\Api\Request\Type;

use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Server\Maintenance;
use ReflectionException;

class OrderItem extends Entity
{
    /**
     * @var string
     */
    protected string $table = "orderItem";
    /**
     * @var string
     */
    protected string $type = "OrderItem";
    /**
     * @var array|string[]
     */
    protected array $properties = [ "*", "orderedItem" ];
    /**
     * @var array
     */
    protected array $hasTypes = [ "referencesOrder" => "Order", "offer" => "Offer", "orderedItem" => true ];

    /**
     * @param array $params
     * @return array
     */
    public function post(array $params): array
    {
        unset($params['tableHasPart']);
        return parent::post($params);
    }

    /**
     * @param null $type
     * @return array
     * @throws ReflectionException
     */
    public function createSqlTable($type = null): array
    {
        $maintenance = (new Maintenance());
        $message[] = $maintenance->createSqlTable("Order");
        $message[] = parent::createSqlTable("OrderItem");
        return $message;
    }
}
