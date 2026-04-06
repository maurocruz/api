<?php
namespace Plinct\Api\Domain\Entities\Thing\CreativeWork\MediaObject\VideoObject;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\MediaObject\MediaObject;

class VideoObject extends MediaObject
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing','CreativeWork','MediaObject'];

	protected int $idvideoObject;
	protected int $thing;

	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data);
	}
}
