<?php

declare(strict_types=1);

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;
use ReflectionException;

class Offer extends Entity implements TypeInterface
{
    /**
     * @var string
     */
    protected $table = "offer";
    /**
     * @var string
     */
    protected string $type = "Offer";
    /**
     * @var array|string[]
     */
    protected array $properties = [ "*" ];
    /**
     * @var array|bool[]
     */
    protected array $hasTypes = [ "itemOffered" => true ];

    /**
     * @param null $type
     * @return array
     * @throws ReflectionException
     */
    public function createSqlTable($type = null) : array
    {
        $maintenance = new Maintenance();
        $message[] = $maintenance->createSqlTable("QuantitativeValue");
        // sql create statement
        $message[] = parent::createSqlTable("Offer");
        return $message;
    }
}
