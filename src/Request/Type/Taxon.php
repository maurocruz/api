<?php

declare(strict_types=1);

namespace Plinct\Api\Request\Type;

use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Server\Maintenance;
use ReflectionException;

class Taxon extends Entity
{
    /**
     * @var string
     */
    protected string $table = "taxon";
    /**
     * @var string
     */
    protected string $type = "Taxon";
    /**
     * @var array|string[]
     */
    protected array $properties = [];
    /**
     * @var array|string[]
     */
    protected array $hasTypes = [ "image" => "ImageObject", "parentTaxon" => "Taxon" ];

    /**
     * @param null $type
     * @return array
     * @throws ReflectionException
     */
    public function createSqlTable($type = null): array
    {
        $maintenance = new Maintenance();
        $maintenance->createSqlTable("ImageObject");
        return parent::createSqlTable("Taxon");
    } 
}
