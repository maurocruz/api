<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class Invoice extends Entity implements TypeInterface
{
    protected $table = "invoice";

    protected $type = "Invoice";

    protected $properties = [ "*" ];

    protected $hasTypes = [ "referencesOrder" => "Order", "customer" => true, "provider" => true ];

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

    public function createSqlTable($type = null): array
    {
        // sql create statement
        $message[] = parent::createSqlTable("Invoice");

        return $message;
    }
}