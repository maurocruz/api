<?php
namespace Plinct\Api\Domain\Entities\Thing\Intangible\Structuredvalue;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Entities\Thing\Thing;

class PropertyValue extends Thing
{
	public const bool IS_MODULE = false;
	public const array DEPENDENCIES = ['Thing'];

	protected int $idpropertyValue;
	protected int $thing;
	public ?string $value = null;

	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data);
	}
}
