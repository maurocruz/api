<?php
namespace Plinct\Api\Domain\Entities\Thing\CreativeWork\WebPageElement;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\CreativeWork;

class WebPageElement extends CreativeWork
{
	public const bool IS_MODULE = false;

	public const array DEPENDENCIES = ['Thing','CreativeWork'];

	protected int $idwebPageElement;
	protected int $creativeWork;
	public ?string $cssSelector = null;

	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data);
	}
}
