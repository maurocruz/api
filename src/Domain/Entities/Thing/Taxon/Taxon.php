<?php
namespace Plinct\Api\Domain\Entities\Thing\Taxon;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Entities\Thing\Thing;

class Taxon extends Thing
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing'];

	protected int $idtaxon;
	private int $thing;
	public ?string $taxonRank = null;
	public ?string $vernacularName = null;
	public mixed $parentTaxon = null;
	public ?array $childTaxon = null;
	public ?string $scientificNameAuthorship = null;
	public ?string $occurrence = null;
	public ?string $flowering = null;
	public ?string $fructification = null;
	public ?string $height = null;
	public ?string $roots = null;
	public ?string $leafs = null;
	public ?string $flowers = null;
	public ?string $fruits = null;
	public ?string $citations = null;

	public function __construct(array $value = ["@type" => "Taxon"])
	{
		parent::__construct($value);
	}

	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data);
	}

}
