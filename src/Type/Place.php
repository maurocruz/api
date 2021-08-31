<?php

declare(strict_types=1);

namespace Plinct\Api\Type;

use Plinct\Api\Server\Maintenance;
use Plinct\Api\Server\Entity;
use ReflectionException;

class Place extends Entity implements TypeInterface
{
    /**
     * @var string
     */
    protected $table = "place";
    /**
     * @var string
     */
    protected string $type = "Place";
    /**
     * @var array|string[]
     */
    protected array $properties = [ "*","address" ];
    /**
     * @var array|string[]
     */
    protected array $hasTypes = [ "address" => "PostalAddress", "image" => "ImageObject" ];

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
        $message[] = $maintenance->createSqlTable("ImageObject");
        $message[] = $maintenance->createSqlTable("PostalAddress");
        $message[] =  parent::createSqlTable("Place");
        return $message;
    }
}
