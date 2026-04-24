<?php
namespace Plinct\Api\Domain\Entities\Thing\Organization;

use Plinct\Api\Application\Support\EntityReflections;
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
	public ?int $location = null;
	public ?int $address = null;
	public ?int $areaServed = null;

	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data);
	}
}
