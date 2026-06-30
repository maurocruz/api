<?php
namespace Plinct\Api\Domain\Entities\Thing\Organization;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Entities\Thing\Place\Place;
use Plinct\Api\Domain\Entities\Thing\Thing;

class Organization extends Thing
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing'];

	protected int $idorganization;
	protected int $thing;
	public ?string $legalName = null;
	public ?int $logo = null;
	public ?string $taxId = null;
	public ?string $hasOfferCatalog = null;
	public mixed $location = null;
	public mixed $address = null;
	public ?int $areaServed = null;

	public function __construct(array $value = ['@type' => 'Organization'])
	{
		$this->idorganization = $value['idorganization'] ?? 0;
		$this->type = $this->type ?? 'Organization';
		if ($this->idorganization !== 0) {
			$this->identifier[] = ['@type' => 'PropertyValue', 'name' => 'idorganization', 'value' => $this->idorganization];
		}

		parent::__construct($value);

		$this->thing = $value['thing'] ?? 0;
		$this->legalName = $value['legalName'] ?? null;
		$this->logo = $value['logo'] ?? null;
		$this->taxId = $value['taxId'] ?? null;
		$this->hasOfferCatalog = $value['hasOfferCatalog'] ?? null;
		$this->address = $value['address'] ?? null;
		$this->areaServed = $value['areaServed'] ?? null;

		if (isset($value['idplace'])) {
			$this->location =  new Place($value);
		} else {
			$this->location = $value['location'] ?? null;
		}
	}

	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data);
	}

	public function getType(): string
	{
		return 'Organization';
	}
}
