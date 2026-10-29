<?php
namespace Plinct\Api\Domain\Entities\Thing\Organization;

use Plinct\Api\Domain\Entities\Thing\Place\Place;
use Plinct\Api\Domain\Entities\Thing\Thing;

class Organization extends Thing
{
	public const IS_MODULE = true;
	public const DEPENDENCIES = ['Thing'];

	private ?int $thing;
	protected ?int $idorganization;
	public ?string $legalName = null;
	public ?int $logo = null;
	public ?string $taxId = null;
	public ?string $hasOfferCatalog = null;
	public mixed $location = null;
	public mixed $address = null;
	public ?int $areaServed = null;

	public function __construct(array $value = ['@type' => 'Organization'])
	{
		parent::__construct($value);

		$this->idorganization = $value['idorganization'] ?? null;
		$this->legalName = $value['legalName'] ?? null;
		$this->logo = $value['logo'] ?? null;
		$this->taxId = $value['taxId'] ?? null;
		$this->hasOfferCatalog = $value['hasOfferCatalog'] ?? null;
		$this->address = $value['address'] ?? null;
		$this->areaServed = $value['areaServed'] ?? null;
		$this->location = isset($value['idplace'])? new Place($value) : $value['location'] ?? null;
	}

	public function getId(): ?int
	{
		return $this->idorganization;
	}
}
