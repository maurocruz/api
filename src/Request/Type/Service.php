<?php

declare(strict_types=1);

namespace Plinct\Api\Request\Type;

use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Server\Maintenance;
use ReflectionException;

class Service extends Entity
{
    /**
     * @var string
     */
    protected string $table = "service";
    /**
     * @var string
     */
    protected string $type = "Service";
    /**
     * @var array|string[]
     */
    protected array $properties = [ "*", "offers" ];
    /**
     * @var array
     */
    protected array $hasTypes = [ "image" => "ImageObject", "offers" => "Offer", "provider" => true ];

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
        // sql create statement
        $message[] = parent::createSqlTable("Service");
        return $message;
    }
}
