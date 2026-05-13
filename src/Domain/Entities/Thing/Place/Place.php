<?php
namespace Plinct\Api\Domain\Entities\Thing\Place;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Entities\Thing\Thing;

class Place extends Thing
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing','GeoCoordinates'];

	protected int $idplace;
	protected int $thing;
	public ?string $keywords = null;
	public mixed $geo = null;
	public ?int $publicAccess = 0;

	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data);
	}
}
