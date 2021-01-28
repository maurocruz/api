<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;

class Service extends Entity implements TypeInterface
{
    protected $table = "service";

    protected $type = "Service";

    protected $properties = [ "*", "offers" ];

    protected $hasTypes = [ "offers" => "Offer", "provider" => true ];

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
        $maintenance = new Maintenance();
        $message[] = $maintenance->createSqlTable("Person");
        $message[] = $maintenance->createSqlTable("Organization");

        // sql create statement
        $message[] = parent::createSqlTable("Service");

        $message[] = $maintenance->createSqlTable("Offer");

        return $message;
    }
}