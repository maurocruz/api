<?php
namespace Plinct\Api\Domain\Entities\Thing\CreativeWork\MediaObject;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\CreativeWork;

class MediaObject extends CreativeWork
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing','CreativeWork'];

	protected int $idmediaObject;
	protected int $thing;
	public ?int $bitrate = null;
	public ?string $duration = null;
	public ?string $contentSize = null;
	public string $contentUrl;
	public ?string $encodingFormat = null;
	public ?int $height = null;
	public ?int $width = null;
	public string $uploadDate;


	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data);
	}
}
