<?php

declare(strict_types=1);

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use ReflectionException;

class Banner extends Entity implements TypeInterface
{
    /**
     * @var string
     */
    protected $table = "banner";
    /**
     * @var string
     */
    protected string $type = "Banner";
    /**
     * @var array|string[]
     */
    protected array $properties = [ "*","image" ];
    /**
     * @var array|string[]
     */
    protected array $hasTypes = [ "image" => "ImageObject" ];

    /**
     * @param array $params
     * @return array
     */
    public function put(array $params): array
    {
        unset($params['idadvertising']);
        return parent::put($params);
    }

    /**
     * @param array $params
     * @return array
     */
    public function delete(array $params): array
    {
        return parent::delete([ "idbanner" => $params['idbanner'] ]);
    }

    /**
     * @param null $type
     * @return array
     * @throws ReflectionException
     */
    public function createSqlTable($type = null): array
    {
        return parent::createSqlTable("Banner");
    }
}
