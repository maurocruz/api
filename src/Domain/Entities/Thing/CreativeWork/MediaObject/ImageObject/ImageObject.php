<?php
namespace Plinct\Api\Domain\Entities\Thing\CreativeWork\MediaObject\ImageObject;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\MediaObject\MediaObject;

class ImageObject extends MediaObject
{
	public const bool IS_MODULE = false;
	public const array DEPENDENCIES = ['Thing','CreativeWork','MediaObject'];

	protected int $idimageObject;
	protected int $thing;
	protected int $creativeWork;
	protected int $mediaObject;

	public ?string $caption = null;
	public ?int $representativeOfPage = null;

	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data);
	}
}
