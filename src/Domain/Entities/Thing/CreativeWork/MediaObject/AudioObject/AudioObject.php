<?php
namespace Plinct\Api\Domain\Entities\Thing\CreativeWork\MediaObject\AudioObject;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\MediaObject\MediaObject;

class AudioObject extends MediaObject
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing','CreativeWork','MediaObject'];

	protected int $idaudioObject;
	protected int $thing;
	protected int $creativeWork;
	protected int $mediaObject;


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
