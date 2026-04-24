<?php
namespace Plinct\Api\Domain\Entities\Thing\Person;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Entities\Thing\Thing;

class Person extends Thing
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing','Place','PostalAddress'];

	protected int $idperson;
	private int $thing;
	public ?string $additionalName = null;
	public ?int $address = null;
	public ?string $birthDate = null;
	public ?string $birthPlace = null;
	public ?string $deathDate = null;
	public ?string $deathPlace = null;
	public ?string $familyName = null;
	public ?string $hasOccupation = null;
	public ?int $homeLocation = null;
	public ?string $gender = null;
	public ?string $givenName = null;

	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data);
	}

	public function getDependencies(): array
	{
		return self::DEPENDENCIES;
	}
}
