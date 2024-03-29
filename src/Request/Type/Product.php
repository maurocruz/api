<?php

declare(strict_types=1);

namespace Plinct\Api\Request\Type;

use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Server\Maintenance;
use ReflectionException;

class Product extends Entity
{
    /**
     * @var string
     */
    protected string $table = "product";
    /**
     * @var string
     */
    protected string $type = "Product";
    /**
     * @var array|string[]
     */
    protected array $properties = [ "name" ];
    /**
     * @var array|string[]
     */
    protected array $hasTypes = [ "image" => "ImageObject", "manufacturer" => "Organization", "offers" => "Offer" ];

    /**
     * @param array $params
     * @return array
     */
    public function post(array $params): array
    {
        $params['dateCreated'] = date("Y-m-d H:i:s");
        return parent::post($params);
    }

    /**
     * @param null $type
     * @return array
     * @throws ReflectionException
     */
    public function createSqlTable($type = null): array
    {
        $maintenance = new Maintenance();
        $message[] = $maintenance->createSqlTable("Person");
        $message[] = $maintenance->createSqlTable("Organization");
        $message[] = $maintenance->createSqlTable("ImageObject");
        $message[] = $maintenance->createSqlTable("Offer");
        $message[] = parent::createSqlTable("Product");
        return $message;
    }
}
