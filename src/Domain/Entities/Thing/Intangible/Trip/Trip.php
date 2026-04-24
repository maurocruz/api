<?php
namespace Plinct\Api\Domain\Entities\Thing\Intangible\Trip;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Entities\Thing\Thing;

class Trip extends Thing
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing'];

	protected int $idthing;
	protected int $thing;
	public ?string $arrivalTime = null;
	public ?string $departureTime = null;
	public int $provider;

	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data);
	}
}
