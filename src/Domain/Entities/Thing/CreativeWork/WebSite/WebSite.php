<?php
namespace Plinct\Api\Domain\Entities\Thing\CreativeWork\WebSite;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\CreativeWork;

class WebSite extends CreativeWork
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing'];

	protected int $idwebSite;
	protected int $thing;
	protected int $creativeWork;

	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data, $withParent);
	}
}
