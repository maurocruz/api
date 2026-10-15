<?php
namespace Plinct\Api\Domain\Entities\Thing\Taxon;

use Plinct\Api\Domain\Entities\Thing\Thing;

class Taxon extends Thing
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing'];

	private ?int $thing;
	protected ?int $idtaxon;
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

		$this->idtaxon = $value['idtaxon'] ?? null;
		$this->taxonRank = $value['taxonRank'] ?? null;
		$this->vernacularName = $value['vernacularName'] ?? null;
		$this->parentTaxon = $value['parentTaxon'] ?? null;
		$this->childTaxon = $value['childTaxon'] ?? null;
		$this->scientificNameAuthorship = $value['scientificNameAuthorship'] ?? null;
		$this->occurrence = $value['occurrence'] ?? null;
		$this->flowering = $value['flowering'] ?? null;
		$this->fructification = $value['fructification'] ?? null;
		$this->height = $value['height'] ?? null;
		$this->roots = $value['roots'] ?? null;
		$this->leafs = $value['leafs'] ?? null;
		$this->flowers = $value['flowers'] ?? null;
		$this->fruits = $value['fruits'] ?? null;
		$this->citations = $value['citations'] ?? null;
	}

	public function getId(): ?int
	{
		return $this->idtaxon;
	}
}
