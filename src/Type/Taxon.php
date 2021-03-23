<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;

class Taxon extends Entity implements TypeInterface {
    protected string $table = "taxon";
    protected string $type = "Taxon";
    protected array $properties = [ "name", "taxonRank", "parentTaxon", "url" ];
    protected array $hasTypes = [ "image" => "ImageObject", "parentTaxon" => "Taxon" ];

    public function createSqlTable($type = null): array {
        $maintenance = new Maintenance();
        $maintenance->createSqlTable("ImageObject");
        return parent::createSqlTable("Taxon");
    } 
}
