<?php

declare(strict_types=1);

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;
use ReflectionException;

class Taxon extends Entity implements TypeInterface
{
    /**
     * @var
     */
    protected string $table = "taxon";
    /**
     * @var string
     */
    protected string $type = "Taxon";
    /**
     * @var array|string[]
     */
    protected array $properties = [ "name", "taxonRank", "parentTaxon", "url" ];
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
